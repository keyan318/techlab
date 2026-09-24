import type { IpDevice } from '../engine/ipdevice';
import { ipToString, type U32 } from '../engine/ip';
import type { Running } from './shell';
import { resolveHost } from './resolve';

const INTERVAL_MS = 1000;
const TIMEOUT_MS = 2000;

export type OutputStyle = 'linux' | 'windows';

export function runPing(
  device: IpDevice,
  args: string[],
  write: (s: string) => void,
  done: () => void,
): Running | null {
  let count: number | null = null;
  let target: string | null = null;
  for (let i = 0; i < args.length; i++) {
    const a = args[i];
    if (a === '-c') {
      count = Number(args[++i]);
      if (!Number.isInteger(count) || count <= 0) {
        write('ping: invalid count\n');
        done();
        return null;
      }
    } else if (a.startsWith('-')) {
      write(`ping: netsim supports only: ping [-c count] <host>  (got "${a}")\n`);
      done();
      return null;
    } else {
      target = a;
    }
  }
  if (!target) {
    write('usage: ping [-c count] <destination>\n');
    done();
    return null;
  }
  return pingTarget(device, target, count, write, done, 'linux');
}

// Resolves `target` and sends echo requests; `count` null means until Ctrl-C.
export function pingTarget(
  device: IpDevice,
  target: string,
  count: number | null,
  write: (s: string) => void,
  done: () => void,
  style: OutputStyle,
): Running {
  const win = style === 'windows';
  // Resolution may complete synchronously (literal IP) or after DNS traffic.
  let cancelFn: (() => void) | undefined;
  const resolver = resolveHost(device, target, (res) => {
    if ('error' in res) {
      if (win) {
        const hint = res.error.match(/\(hint: [^)]*\)/)?.[0];
        write(`Ping request could not find host ${target}. Please check the name and try again.\n`);
        if (hint) write(`${hint}\n`);
      } else {
        write(`ping: ${res.error}\n`);
      }
      done();
      return;
    }
    cancelFn = start(res.ip);
  });
  if (!cancelFn) {
    cancelFn = () => {
      resolver.cancel();
      done();
    };
  }
  return { cancel: () => cancelFn!() };

  function start(ip: U32): () => void {
    const sched = device.network.scheduler;
    const id = device.allocIcmpId();
    const startedAt = sched.now;
    const sendTimes = new Map<number, number>();
    const answered = new Set<number>();
    const rtts: number[] = [];
    let sent = 0;
    let received = 0;
    let finished = false;
    const addr = ipToString(ip);

    if (win) {
      const label = target === addr ? addr : `${target} [${addr}]`;
      write(`\nPinging ${label} with 32 bytes of data:\n`);
    } else {
      write(`PING ${target} (${addr}) 56(84) bytes of data.\n`);
    }

    const finish = () => {
      if (finished) return;
      finished = true;
      device.offIcmp(id);
      const loss = sent ? Math.round(((sent - received) / sent) * 100) : 0;
      if (win) {
        write(`\nPing statistics for ${addr}:\n`);
        write(`    Packets: Sent = ${sent}, Received = ${received}, Lost = ${sent - received} (${loss}% loss),\n`);
        if (rtts.length) {
          const min = Math.round(Math.min(...rtts));
          const max = Math.round(Math.max(...rtts));
          const avg = Math.round(rtts.reduce((a, b) => a + b, 0) / rtts.length);
          write('Approximate round trip times in milli-seconds:\n');
          write(`    Minimum = ${min}ms, Maximum = ${max}ms, Average = ${avg}ms\n`);
        }
      } else {
        write(`\n--- ${target} ping statistics ---\n`);
        write(
          `${sent} packets transmitted, ${received} received, ${loss}% packet loss, time ${Math.round(
            sched.now - startedAt,
          )}ms\n`,
        );
        if (rtts.length) {
          const min = Math.min(...rtts);
          const max = Math.max(...rtts);
          const avg = rtts.reduce((a, b) => a + b, 0) / rtts.length;
          write(`rtt min/avg/max = ${min.toFixed(1)}/${avg.toFixed(1)}/${max.toFixed(1)} ms\n`);
        }
      }
      done();
    };

    const maybeFinish = (seq: number) => {
      if (count !== null && seq >= count) finish();
    };

    device.onIcmp(id, (ev) => {
      if (finished) return;
      switch (ev.type) {
        case 'reply': {
          if (answered.has(ev.seq)) break;
          answered.add(ev.seq);
          received++;
          const rtt = sched.now - (sendTimes.get(ev.seq) ?? sched.now);
          rtts.push(rtt);
          write(
            win
              ? `Reply from ${ipToString(ev.from)}: bytes=32 time=${Math.round(rtt)}ms TTL=${ev.ttl}\n`
              : `64 bytes from ${ipToString(ev.from)}: icmp_seq=${ev.seq} ttl=${ev.ttl} time=${rtt.toFixed(1)} ms\n`,
          );
          maybeFinish(ev.seq);
          break;
        }
        case 'time-exceeded': {
          const seq = ev.seq ?? 0;
          answered.add(seq);
          write(
            win
              ? `Reply from ${ipToString(ev.from)}: TTL expired in transit.\n`
              : `From ${ipToString(ev.from)} icmp_seq=${seq} Time to live exceeded\n`,
          );
          maybeFinish(seq);
          break;
        }
        case 'unreachable': {
          const seq = ev.seq ?? 0;
          answered.add(seq);
          const what = ev.code === 'net' ? 'Net' : ev.code === 'host' ? 'Host' : 'Port';
          write(
            win
              ? `Reply from ${ipToString(ev.from)}: Destination ${what.toLowerCase()} unreachable.\n`
              : `From ${ipToString(ev.from)} icmp_seq=${seq} Destination ${what} Unreachable\n`,
          );
          maybeFinish(seq);
          break;
        }
        case 'send-error': {
          const seq = ev.seq ?? 0;
          answered.add(seq);
          write(win ? `Reply from ${device.name}: ${ev.message}.\n` : `From ${device.name} icmp_seq=${seq} ${ev.message}\n`);
          maybeFinish(seq);
          break;
        }
      }
    });

    const sendOne = () => {
      if (finished) return;
      const seq = ++sent;
      sendTimes.set(seq, sched.now);
      const err = device.sendEcho(ip, id, seq);
      if (err) {
        sent--;
        if (win) {
          write(
            err === 'Operation not permitted'
              ? 'PING: transmit failed. Blocked by this computer\'s firewall (outbound rule).\n'
              : 'PING: transmit failed. General failure.\n',
          );
        } else if (err === 'Operation not permitted') {
          // EPERM (an OUTPUT-chain drop) reads differently from a routing failure.
          write(`ping: sendmsg: ${err}\n`);
        } else {
          write(`ping: connect: ${err}\n`);
        }
        finish();
        return;
      }
      sched.schedule(TIMEOUT_MS, () => {
        if (finished || answered.has(seq)) return;
        answered.add(seq);
        // Matches real `ping -O` behaviour; silent gaps teach nothing.
        write(win ? 'Request timed out.\n' : `no answer yet for icmp_seq=${seq}\n`);
        maybeFinish(seq);
      });
      if (count === null || sent < count) sched.schedule(INTERVAL_MS, sendOne);
    };

    sendOne();
    return finish;
  }
}
