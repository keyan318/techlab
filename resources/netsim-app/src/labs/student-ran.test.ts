// A live probe proves the network is configured right; it doesn't prove the student typed the command
// themselves. These guard the fix: a lesson that explicitly says "From X, run `ping ...`" must see that
// real command succeed before its objective passes — a typo'd command that never sent anything must not
// ride free on a network that happens to already be correct. A "defend it yourself" lab's scripted
// outside probe (never named in the lesson text) must keep passing on network state alone, unchanged.
import { readFileSync } from 'node:fs';
import { describe, expect, it } from 'vitest';
import { restoreNetwork, type SaveFile } from '../engine/serialize';
import type { Network } from '../engine/network';
import { IpDevice } from '../engine/ipdevice';
import { Shell } from '../commands/shell';
import { runChecks, type CheckResult } from './checker';
import type { Objective } from './types';
import { attachRequireOwnCommand, studentTypesIt } from './student-ran';

const load = (id: string): SaveFile => JSON.parse(readFileSync(`public/labs/${id}.json`, 'utf8'));

function dev(net: Network, name: string): IpDevice {
  const d = [...net.devices.values()].find((x) => x.name === name);
  expect(d, `device ${name}`).toBeTruthy();
  return d as IpDevice;
}

function grade(net: Network, objectives: Objective[]): Record<string, CheckResult> {
  const out: Record<string, CheckResult> = {};
  let done = false;
  runChecks(net, objectives, (r) => (out[r.id] = r), () => (done = true));
  net.scheduler.advanceTo(net.scheduler.now + 120_000);
  expect(done).toBe(true);
  return out;
}

function sh(net: Network, d: IpDevice, line: string): string {
  let text = '';
  let done = false;
  new Shell(d).exec(line, { write: (t: string) => (text += t) }, () => (done = true));
  net.scheduler.advanceTo(net.scheduler.now + 60_000);
  expect(done, line).toBe(true);
  return text;
}

describe('studentTypesIt', () => {
  it('matches "From <device>, run `<command>`"', () => {
    expect(studentTypesIt('From astro, run `ping -n 3 192.168.1.20` and see.', 'astro', 'ping')).toBe(true);
  });

  it('does not credit a device the lesson never names', () => {
    expect(studentTypesIt('On `citadel-gw`, add an ACL to filter doom-scan.', 'doom-scan', 'tcp')).toBe(false);
  });

  it('does not credit the wrong command verb', () => {
    expect(studentTypesIt('From astro, run `ipconfig` to check your work.', 'astro', 'ping')).toBe(false);
  });

  it('scopes a "from" mention to before the next device is named', () => {
    const body = 'From astro, run `curl http://x/`. From rivet, run `ping 1.2.3.4`.';
    expect(studentTypesIt(body, 'astro', 'ping')).toBe(false);
    expect(studentTypesIt(body, 'rivet', 'ping')).toBe(true);
  });
});

describe('a lesson-instructed ping requires the student\'s own command', () => {
  it('m1-l1: addressing alone is not enough once the student is asked to ping', () => {
    const f = load('m1-l1');
    attachRequireOwnCommand(f.lab!);
    const net = restoreNetwork(f);
    const [, step2] = f.lab!.steps.map((s) => s.objectives);
    sh(net, dev(net, 'astro'), 'ip addr add 192.168.1.10/24 dev eth0');
    sh(net, dev(net, 'rivet'), 'ip addr add 192.168.1.20/24 dev eth0');

    // The network is fully configured, but astro never ran a real ping (a typo'd flag never sends one) —
    // the objective the lesson explicitly asks astro to prove must still fail.
    sh(net, dev(net, 'astro'), 'ping -x 192.168.1.20'); // unsupported flag: errors before sending anything
    const r1 = grade(net, step2);
    expect(r1['m1l1-ping-ar'].pass).toBe(false);

    // A real ping from astro observes success, and only then does the objective pass.
    sh(net, dev(net, 'astro'), 'ping -c 1 192.168.1.20');
    const r2 = grade(net, step2);
    expect(r2['m1l1-ping-ar'].pass).toBe(true);
    // The lesson never tells rivet to ping back; that objective stays network-state-only.
    expect(r2['m1l1-ping-ra'].pass).toBe(true);
  });
});

describe('a scripted "defend it yourself" probe stays network-state-only', () => {
  it('g6-l1: doom-scan is never named in the lesson text, so no console session is required', () => {
    const f = load('g6-l1');
    attachRequireOwnCommand(f.lab!);
    const obj = f.lab!.steps[0].objectives.find((o) => o.id === 'g6l1-ssh')!;
    expect((obj.check as { requireOwnCommand?: boolean }).requireOwnCommand).toBeFalsy();
  });
});
