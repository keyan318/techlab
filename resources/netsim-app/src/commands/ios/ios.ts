// TechLab (Networking 2): a Cisco IOS-style console for NetSim routers and switches.
//
// A curated subset of real IOS: the modes (Router> / Router# / (config)# / (config-if)# ...),
// unique-prefix abbreviations (`conf t`, `sh ip int br`, `int g0/1`, `no shut`), `do` from config
// modes, and the configuration and show commands Networking 2's labs use: interfaces and
// sub-interfaces, static routes, OSPF, VLANs and trunks, spanning tree, port security and
// HSRP/VRRP. Anything else is rejected the way IOS rejects it, never faked.
import { SubInterface, type Device, type FhrpGroup, type NetInterface } from '../../engine/device';
import { IpDevice, DEFAULT_TTL } from '../../engine/ipdevice';
import { SwitchDevice } from '../../engine/switch';
import { ipToString, networkOf, parseIp, type U32 } from '../../engine/ip';
import { newOspf } from '../../engine/ospf';
import { aclKindFor, parseAce } from '../../engine/acl';
import type { Running, TermIO } from '../shell';
import { resolveHost } from '../resolve';
import { traceTarget } from '../traceroute';
import { findPort, parseIfName, parseMac, parseRange, parseVlanList, iosName } from './names';
import * as show from './show';

type Mode = 'user' | 'priv' | 'config' | 'if' | 'subif' | 'range' | 'router' | 'vlan' | 'mst' | 'acl';
type DevKind = 'router' | 'switch' | 'any';

interface Ctx {
  sh: IosShell;
  a: string[];
  write: (s: string) => void;
  done: () => void;
}

interface Cmd {
  modes: Mode[];
  pat: string;
  dev?: DevKind;
  run: (c: Ctx) => Running | null | void;
}

const CONFIG_MODES: Mode[] = ['config', 'if', 'subif', 'range', 'router', 'vlan', 'mst', 'acl'];
const IF_MODES: Mode[] = ['if', 'subif', 'range'];
const EXEC: Mode[] = ['user', 'priv'];

function maskToPrefix(mask: U32): number | null {
  const m = mask >>> 0;
  let p = 0;
  while (p < 32 && (m & (0x80000000 >>> p)) !== 0) p++;
  return ((p === 0 ? 0 : (0xffffffff << (32 - p)) >>> 0) === m) ? p : null;
}

const INVALID = "% Invalid input detected at '^' marker.\n";

// ---------- matching ----------

type MatchResult = { status: 'full' | 'incomplete' | 'fail'; args: string[]; exact: number; at: number };

function tryMatch(parts: string[], toks: string[]): MatchResult {
  let ti = 0;
  let exact = 0;
  const args: string[] = [];
  for (const part of parts) {
    if (ti >= toks.length) return { status: 'incomplete', args, exact, at: ti };
    const tok = toks[ti];
    if (part.startsWith('<')) {
      const type = part.slice(1, -1);
      if (type === 'rest') {
        args.push(toks.slice(ti).join(' '));
        ti = toks.length;
        continue;
      }
      if (type === 'if') {
        let joined = tok;
        if (/^[a-z]+$/i.test(tok) && toks[ti + 1] && /^\d/.test(toks[ti + 1])) {
          joined = tok + toks[ti + 1];
          ti++;
        }
        if (!parseIfName(joined)) return { status: 'fail', args, exact, at: ti };
        args.push(joined);
        ti++;
        continue;
      }
      const ok =
        type === 'ip' ? parseIp(tok) !== null
        : type === 'n' ? /^\d+$/.test(tok)
        : type === 'vlans' ? parseVlanList(tok) !== null
        : type === 'mac' ? parseMac(tok) !== null
        : true;
      if (!ok) return { status: 'fail', args, exact, at: ti };
      args.push(tok);
      ti++;
      continue;
    }
    const t = tok.toLowerCase();
    if (part === t) exact++;
    else if (!part.startsWith(t)) return { status: 'fail', args, exact, at: ti };
    ti++;
  }
  if (ti < toks.length) return { status: 'fail', args, exact, at: ti };
  return { status: 'full', args, exact, at: ti };
}

// ---------- the shell ----------

export class IosShell {
  mode: Mode = 'user';
  ifaces: NetInterface[] = [];
  vlanIds: number[] = [];
  aclName = '';

  constructor(public device: Device) {}

  get net() {
    return this.device.network;
  }

  get router(): IpDevice | null {
    return this.device instanceof IpDevice ? this.device : null;
  }

  get sw(): SwitchDevice | null {
    return this.device instanceof SwitchDevice ? this.device : null;
  }

  prompt(): string {
    const h = show.hostnameOf(this.device);
    const suffix: Record<Mode, string> = {
      user: '>',
      priv: '#',
      config: '(config)#',
      if: '(config-if)#',
      subif: '(config-subif)#',
      range: '(config-if-range)#',
      router: '(config-router)#',
      vlan: '(config-vlan)#',
      mst: '(config-mst)#',
      acl: this.router?.acls.get(this.aclName)?.kind === 'standard' ? '(config-std-nacl)#' : '(config-ext-nacl)#',
    };
    return h + suffix[this.mode];
  }

  banner(): string {
    return `\n${this.device instanceof SwitchDevice ? 'Cisco Catalyst 2960 (NetSim)' : 'Cisco ISR 4331 (NetSim)'}\nPress RETURN to get started. Type ? for the commands you can use here.\n`;
  }

  exec(line: string, io: TermIO, done: () => void): Running | null {
    const write = io.write.bind(io);
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith('!')) {
      done();
      return null;
    }
    if (trimmed.endsWith('?')) {
      write(this.help(trimmed.slice(0, -1).trim()));
      done();
      return null;
    }
    const promptLen = this.prompt().length;
    const toks = trimmed.split(/\s+/);
    // `do <exec command>` from any configuration mode.
    if (CONFIG_MODES.includes(this.mode) && toks[0].toLowerCase() === 'do' && toks.length > 1) {
      const saved = this.mode;
      this.mode = 'priv';
      const r = this.dispatch(toks.slice(1), write, () => {
        this.mode = saved;
        done();
      }, promptLen + 3);
      if (this.mode === 'priv') this.mode = saved;
      return r;
    }
    return this.dispatch(toks, write, done, promptLen);
  }

  private dispatch(toks: string[], write: (s: string) => void, done: () => void, offset: number): Running | null {
    const pick = (modes: Mode[]) => {
      const cands = COMMANDS.filter(
        (c) => c.modes.some((m) => modes.includes(m)) && this.devOk(c.dev ?? 'any'),
      ).map((c) => ({ c, r: tryMatch(c.pat.split(' '), toks) }));
      const full = cands.filter((x) => x.r.status === 'full').sort((a, b) => b.r.exact - a.r.exact);
      return { cands, full };
    };
    let { cands, full } = pick([this.mode]);
    // Global commands also work from configuration sub-modes (IOS falls back to the parent mode).
    if (!full.length && CONFIG_MODES.includes(this.mode) && this.mode !== 'config') {
      const parent = pick(['config']);
      if (parent.full.length) {
        full = parent.full;
        cands = parent.cands;
      }
    }
    if (!full.length) {
      if (cands.some((x) => x.r.status === 'incomplete')) write('% Incomplete command.\n\n');
      else {
        const at = Math.max(0, ...cands.map((x) => x.r.at));
        const col = toks.slice(0, at).join(' ').length + (at > 0 ? 1 : 0);
        write(`${' '.repeat(offset + col)}^\n${INVALID}\n`);
      }
      done();
      return null;
    }
    const { c, r } = full[0];
    const out = c.run({ sh: this, a: r.args, write, done });
    if (out && typeof out === 'object') return out;
    done();
    return null;
  }

  private devOk(k: DevKind): boolean {
    return k === 'any' || (k === 'router' ? this.router !== null : this.sw !== null);
  }

  private help(prefix: string): string {
    const toks = prefix ? prefix.split(/\s+/) : [];
    const seen = new Set<string>();
    const rows: string[] = [];
    for (const c of COMMANDS) {
      if (!c.modes.includes(this.mode) || !this.devOk(c.dev ?? 'any')) continue;
      const parts = c.pat.split(' ');
      if (toks.length && tryMatch(parts.slice(0, toks.length), toks).status !== 'full') continue;
      if (!seen.has(c.pat)) {
        seen.add(c.pat);
        rows.push(`  ${c.pat}`);
      }
    }
    return rows.length ? rows.join('\n') + '\n\n' : `% Unrecognized command\n`;
  }

  // ---------- helpers used by commands ----------

  touch(): void {
    this.net.touch();
  }

  enterInterface(text: string, write: (s: string) => void): void {
    const parsed = parseIfName(text);
    const port = findPort(this.device, text);
    if (!parsed || !port) {
      write(INVALID);
      return;
    }
    if (parsed.sub === null) {
      this.ifaces = [port];
      this.mode = 'if';
      return;
    }
    const r = this.router;
    if (!r) {
      write('% Sub-interfaces are a router feature. On a switch, use switchport commands.\n');
      return;
    }
    const name = `${port.name}.${parsed.sub}`;
    let sub = r.getInterface(name);
    if (!sub) {
      sub = new SubInterface(r, name, port);
      r.interfaces.push(sub);
      this.touch();
    }
    this.ifaces = [sub];
    this.mode = 'subif';
  }

  ospfOn(write: (s: string) => void): IpDevice | null {
    const r = this.router;
    if (!r?.ospf) {
      write('% OSPF is not running. Start it with: router ospf 1\n');
      return null;
    }
    return r;
  }
}

// ---------- ping (IOS style) ----------

function iosPing(d: IpDevice, target: string, write: (s: string) => void, done: () => void): Running {
  let cancelled = false;
  const finish = () => {
    if (!cancelled) done();
    cancelled = true;
  };
  resolveHost(d, target, (res) => {
    if (cancelled) return;
    if ('error' in res) {
      write(`% Unrecognized host or address, or protocol not running.\n\n`);
      finish();
      return;
    }
    const dst = res.ip;
    write(`Type escape sequence to abort.\nSending 5, 100-byte ICMP Echos to ${ipToString(dst)}, timeout is 2 seconds:\n`);
    const id = d.allocIcmpId();
    let seq = 0;
    let ok = 0;
    let waiting: { seq: number; timer: ReturnType<IpDevice['network']['scheduler']['schedule']> } | null = null;
    const next = () => {
      if (cancelled) return;
      if (seq >= 5) {
        d.offIcmp(id);
        write(`\nSuccess rate is ${ok * 20} percent (${ok}/5)${ok ? ', round-trip min/avg/max = 1/1/2 ms' : ''}\n`);
        finish();
        return;
      }
      seq++;
      const mySeq = seq;
      const err = d.sendEcho(dst, id, mySeq, DEFAULT_TTL);
      if (err) {
        write('.');
        d.network.scheduler.schedule(10, next);
        return;
      }
      const timer = d.network.scheduler.schedule(2000, () => {
        if (waiting?.seq !== mySeq) return;
        waiting = null;
        write('.');
        next();
      });
      waiting = { seq: mySeq, timer };
    };
    d.onIcmp(id, (ev) => {
      if (!waiting || ev.seq !== waiting.seq) return;
      d.network.scheduler.cancel(waiting.timer);
      waiting = null;
      if (ev.type === 'reply') {
        ok++;
        write('!');
      } else if (ev.type === 'unreachable') write('U');
      else write('.');
      d.network.scheduler.schedule(20, next);
    });
    next();
  });
  return {
    cancel: () => {
      if (cancelled) return;
      write('\n');
      finish();
    },
  };
}

// ---------- command table ----------

const each = (c: Ctx, fn: (i: NetInterface) => void) => {
  for (const i of c.sh.ifaces) fn(i);
  c.sh.touch();
};

const needRouter = (c: Ctx): IpDevice => c.sh.router!;
const needSwitch = (c: Ctx): SwitchDevice => c.sh.sw!;

function setAccessVlan(c: Ctx, vlan: number) {
  const sw = needSwitch(c);
  if (!sw.vlanDb.has(vlan)) {
    c.write(`% Access VLAN does not exist. Creating vlan ${vlan}\n`);
    sw.vlanDb.set(vlan, `VLAN${String(vlan).padStart(4, '0')}`);
  }
  each(c, (i) => {
    if (vlan === 1) sw.vlans.delete(i.name);
    else sw.vlans.set(i.name, vlan);
  });
}

function fhrpGroup(c: Ctx, proto: 'hsrp' | 'vrrp', group: number): FhrpGroup[] {
  const out: FhrpGroup[] = [];
  for (const i of c.sh.ifaces) {
    const list = (i.cfg.fhrp ??= []);
    let g = list.find((x) => x.proto === proto && x.group === group);
    if (!g) {
      g = { proto, group, vip: null, priority: 100, preempt: proto === 'vrrp' };
      list.push(g);
    }
    out.push(g);
  }
  return out;
}

function trackCmd(c: Ctx, ifText: string, decrement: number) {
  const port = findPort(c.sh.device, ifText);
  if (!port) return c.write(INVALID);
  for (const g of fhrpGroup(c, 'hsrp', Number(c.a[0]))) {
    g.track = [...(g.track ?? []).filter((t) => t.iface !== port.name), { iface: port.name, decrement }];
  }
  c.sh.touch();
}

// Adds (or replaces, by sequence number) one access list entry. seq null = 10 after the last one.
function addAce(c: Ctx, name: string, seq: number | null, action: string, rest: string): void {
  const r = needRouter(c);
  if (action !== 'permit' && action !== 'deny') return c.write(INVALID);
  const kind = r.acls.get(name)?.kind ?? aclKindFor(name);
  const e = parseAce(kind, rest.split(/\s+/).filter(Boolean));
  if (typeof e === 'string') return c.write(`% ${e}\n`);
  let acl = r.acls.get(name);
  if (!acl) {
    acl = { kind, entries: [] };
    r.acls.set(name, acl);
  }
  const s = seq ?? (acl.entries.length ? Math.max(...acl.entries.map((x) => x.seq)) + 10 : 10);
  acl.entries = acl.entries.filter((x) => x.seq !== s);
  acl.entries.push({ ...e, seq: s, action, matches: 0 });
  acl.entries.sort((a, b) => a.seq - b.seq);
  c.sh.touch();
}

function portSec(i: NetInterface) {
  return (i.cfg.portSecurity ??= { enabled: false, max: 1, violation: 'shutdown', sticky: false, macs: [], violations: 0 });
}

function requireAccess(c: Ctx): boolean {
  for (const i of c.sh.ifaces) {
    if (i.cfg.mode !== 'access') {
      c.write(`Command rejected: ${iosName(c.sh.device, i)} is a dynamic port.\n`);
      return false;
    }
  }
  return true;
}

function stpPriorityOk(c: Ctx, p: number): boolean {
  if (p % 4096 !== 0 || p > 61440) {
    c.write('% Bridge Priority must be in increments of 4096.\n% Allowed values are:\n  0     4096  8192  12288 16384 20480 24576 28672\n  32768 36864 40960 45056 49152 53248 57344 61440\n');
    return false;
  }
  return true;
}

const COMMANDS: Cmd[] = [
  // ----- exec -----
  { modes: ['user'], pat: 'enable', run: (c) => void (c.sh.mode = 'priv') },
  { modes: ['priv'], pat: 'disable', run: (c) => void (c.sh.mode = 'user') },
  { modes: ['priv'], pat: 'configure terminal', run: (c) => {
    c.write('Enter configuration commands, one per line.  End with CNTL/Z.\n');
    c.sh.mode = 'config';
  } },
  { modes: EXEC, pat: 'exit', run: (c) => void (c.sh.mode = 'user') },
  { modes: EXEC, pat: 'logout', run: (c) => void (c.sh.mode = 'user') },
  { modes: ['priv'], pat: 'copy running-config startup-config', run: (c) => c.write('Destination filename [startup-config]?\nBuilding configuration...\n[OK]\n') },
  { modes: ['priv'], pat: 'write memory', run: (c) => c.write('Building configuration...\n[OK]\n') },
  { modes: ['priv'], pat: 'write', run: (c) => c.write('Building configuration...\n[OK]\n') },
  { modes: ['priv'], pat: 'clear ip ospf process', dev: 'router', run: (c) => c.write('Reset ALL OSPF processes? [no]: yes\n') },
  { modes: ['priv'], pat: 'clear mac address-table dynamic', dev: 'switch', run: (c) => needSwitch(c).macTable.clear() },
  { modes: EXEC, pat: 'ping <word>', dev: 'router', run: (c) => iosPing(needRouter(c), c.a[0], c.write, c.done) },
  { modes: EXEC, pat: 'traceroute <word>', dev: 'router', run: (c) => {
    c.write('Type escape sequence to abort.\n');
    return traceTarget(needRouter(c), c.a[0], 30, c.write, c.done, 'linux');
  } },
  { modes: EXEC, pat: 'ping <word>', dev: 'switch', run: (c) => c.write('% This switch has no IP address in NetSim. Ping from a PC or a router.\n') },

  // ----- show -----
  { modes: ['priv'], pat: 'show running-config', run: (c) => c.write(show.runningConfig(c.sh.device)) },
  { modes: ['priv'], pat: 'show startup-config', run: (c) => c.write(show.runningConfig(c.sh.device)) },
  { modes: EXEC, pat: 'show version', run: (c) => c.write(`Cisco IOS Software (NetSim for TechLab)\n${show.hostnameOf(c.sh.device)} uptime is ${Math.floor(c.sh.net.now / 60000)} minutes\n`) },
  { modes: EXEC, pat: 'show ip interface brief', run: (c) => c.write(show.showIpIntBrief(c.sh.device)) },
  { modes: EXEC, pat: 'show ip route', dev: 'router', run: (c) => c.write(show.showIpRoute(needRouter(c), null)) },
  { modes: EXEC, pat: 'show ip route ospf', dev: 'router', run: (c) => c.write(show.showIpRoute(needRouter(c), 'ospf')) },
  { modes: EXEC, pat: 'show ip route static', dev: 'router', run: (c) => c.write(show.showIpRoute(needRouter(c), 'static')) },
  { modes: EXEC, pat: 'show ip route connected', dev: 'router', run: (c) => c.write(show.showIpRoute(needRouter(c), 'connected')) },
  { modes: EXEC, pat: 'show ip ospf neighbor', dev: 'router', run: (c) => c.write(show.showOspfNeighbor(needRouter(c))) },
  { modes: EXEC, pat: 'show ip ospf interface brief', dev: 'router', run: (c) => c.write(show.showOspfIntBrief(needRouter(c))) },
  { modes: EXEC, pat: 'show ip ospf database', dev: 'router', run: (c) => c.write(show.showOspfDatabase(needRouter(c))) },
  { modes: EXEC, pat: 'show ip protocols', dev: 'router', run: (c) => c.write(show.showIpProtocols(needRouter(c))) },
  { modes: EXEC, pat: 'show ip arp', dev: 'router', run: (c) => c.write(show.showArp(needRouter(c))) },
  { modes: EXEC, pat: 'show arp', dev: 'router', run: (c) => c.write(show.showArp(needRouter(c))) },
  { modes: EXEC, pat: 'show standby brief', dev: 'router', run: (c) => c.write(show.showStandbyBrief(needRouter(c))) },
  { modes: EXEC, pat: 'show standby', dev: 'router', run: (c) => c.write(show.showStandbyBrief(needRouter(c))) },
  { modes: EXEC, pat: 'show vrrp brief', dev: 'router', run: (c) => c.write(show.showVrrpBrief(needRouter(c))) },
  { modes: EXEC, pat: 'show vrrp', dev: 'router', run: (c) => c.write(show.showVrrpBrief(needRouter(c))) },
  { modes: EXEC, pat: 'show cdp neighbors', run: (c) => c.write(show.showCdpNeighbors(c.sh.device)) },
  { modes: EXEC, pat: 'show vlan brief', dev: 'switch', run: (c) => c.write(show.showVlanBrief(needSwitch(c))) },
  { modes: EXEC, pat: 'show vlan', dev: 'switch', run: (c) => c.write(show.showVlanBrief(needSwitch(c))) },
  { modes: EXEC, pat: 'show interfaces trunk', dev: 'switch', run: (c) => c.write(show.showInterfacesTrunk(needSwitch(c))) },
  { modes: EXEC, pat: 'show interfaces status', dev: 'switch', run: (c) => c.write(show.showInterfacesStatus(needSwitch(c))) },
  { modes: EXEC, pat: 'show interfaces <if> switchport', dev: 'switch', run: (c) => {
    const p = findPort(c.sh.device, c.a[0]);
    c.write(p ? show.showSwitchport(needSwitch(c), p) : INVALID);
  } },
  { modes: EXEC, pat: 'show spanning-tree', dev: 'switch', run: (c) => c.write(show.showSpanningTree(needSwitch(c), null)) },
  { modes: EXEC, pat: 'show spanning-tree vlan <n>', dev: 'switch', run: (c) => c.write(show.showSpanningTree(needSwitch(c), Number(c.a[0]))) },
  { modes: EXEC, pat: 'show spanning-tree mst', dev: 'switch', run: (c) => c.write(show.showSpanningTree(needSwitch(c), null)) },
  { modes: EXEC, pat: 'show port-security', dev: 'switch', run: (c) => c.write(show.showPortSecurity(needSwitch(c))) },
  { modes: EXEC, pat: 'show port-security interface <if>', dev: 'switch', run: (c) => {
    const p = findPort(c.sh.device, c.a[0]);
    c.write(p ? show.showPortSecurityIf(needSwitch(c), p) : INVALID);
  } },
  { modes: EXEC, pat: 'show mac address-table', dev: 'switch', run: (c) => c.write(show.showMacTable(needSwitch(c))) },

  // ----- global configuration -----
  { modes: CONFIG_MODES, pat: 'end', run: (c) => void (c.sh.mode = 'priv') },
  { modes: ['config'], pat: 'exit', run: (c) => void (c.sh.mode = 'priv') },
  { modes: ['if', 'subif', 'range', 'router', 'vlan', 'mst'], pat: 'exit', run: (c) => void (c.sh.mode = 'config') },
  { modes: ['config'], pat: 'hostname <word>', run: (c) => {
    c.sh.device.hostname = c.a[0];
  } },
  { modes: ['config'], pat: 'enable secret <rest>', run: () => {} },
  { modes: ['config'], pat: 'service password-encryption', run: () => {} },
  { modes: ['config'], pat: 'no ip domain-lookup', run: () => {} },
  { modes: ['config'], pat: 'ip routing', dev: 'router', run: () => {} },
  { modes: ['config'], pat: 'interface range <rest>', run: (c) => {
    const ports = parseRange(c.sh.device, c.a[0]);
    if (!ports) return c.write(INVALID);
    c.sh.ifaces = ports;
    c.sh.mode = 'range';
  } },
  { modes: ['config'], pat: 'interface vlan <n>', dev: 'switch', run: (c) => c.write('% NetSim switches are Layer 2 here. Route between VLANs on a router (router-on-a-stick).\n') },
  { modes: ['config'], pat: 'interface <if>', run: (c) => c.sh.enterInterface(c.a[0], c.write) },
  { modes: ['config'], pat: 'ip route <ip> <ip> <ip>', dev: 'router', run: (c) => {
    const r = needRouter(c);
    const dest = parseIp(c.a[0])!;
    const prefix = maskToPrefix(parseIp(c.a[1])!);
    if (prefix === null) return c.write('% Inconsistent address and mask\n');
    const err = r.addRoute(networkOf(dest, prefix), prefix, parseIp(c.a[2])!);
    if (err && !/exists/.test(err)) c.write(`%Invalid next hop address (it's this router or not on a connected network)\n`);
  } },
  { modes: ['config'], pat: 'ip route <ip> <ip> <if>', dev: 'router', run: (c) => {
    const r = needRouter(c);
    const prefix = maskToPrefix(parseIp(c.a[1])!);
    const port = findPort(r, c.a[2]);
    if (prefix === null || !port) return c.write(INVALID);
    r.addRoute(networkOf(parseIp(c.a[0])!, prefix), prefix, null, port.name);
  } },
  { modes: ['config'], pat: 'no ip route <ip> <ip> <rest>', dev: 'router', run: (c) => {
    const prefix = maskToPrefix(parseIp(c.a[1])!);
    if (prefix !== null) needRouter(c).delRoute(parseIp(c.a[0])!, prefix);
  } },
  { modes: ['config'], pat: 'no ip route <ip> <ip>', dev: 'router', run: (c) => {
    const prefix = maskToPrefix(parseIp(c.a[1])!);
    if (prefix !== null) needRouter(c).delRoute(parseIp(c.a[0])!, prefix);
  } },
  { modes: ['config'], pat: 'router ospf <n>', dev: 'router', run: (c) => {
    const r = needRouter(c);
    const pid = Number(c.a[0]);
    if (r.ospf && r.ospf.pid !== pid) return c.write(`% NetSim runs one OSPF process per router. Use: router ospf ${r.ospf.pid}\n`);
    if (!r.ospf) {
      r.ospf = newOspf(pid);
      c.sh.touch();
    }
    c.sh.mode = 'router';
  } },
  { modes: ['config'], pat: 'no router ospf <n>', dev: 'router', run: (c) => {
    needRouter(c).ospf = null;
    c.sh.touch();
  } },
  { modes: ['config'], pat: 'ip default-gateway <ip>', dev: 'switch', run: () => {} },
  { modes: ['config'], pat: 'vlan <vlans>', dev: 'switch', run: (c) => {
    const sw = needSwitch(c);
    const vs = parseVlanList(c.a[0])!;
    for (const v of vs) if (!sw.vlanDb.has(v)) sw.vlanDb.set(v, `VLAN${String(v).padStart(4, '0')}`);
    c.sh.vlanIds = vs;
    c.sh.mode = 'vlan';
    c.sh.touch();
  } },
  { modes: ['config'], pat: 'no vlan <vlans>', dev: 'switch', run: (c) => {
    const sw = needSwitch(c);
    for (const v of parseVlanList(c.a[0])!) if (v !== 1) sw.vlanDb.delete(v);
    c.sh.touch();
  } },
  { modes: ['vlan'], pat: 'name <word>', dev: 'switch', run: (c) => {
    for (const v of c.sh.vlanIds) needSwitch(c).vlanDb.set(v, c.a[0]);
  } },
  { modes: ['config'], pat: 'spanning-tree mode pvst', dev: 'switch', run: (c) => { needSwitch(c).stpMode = 'pvst'; c.sh.touch(); } },
  { modes: ['config'], pat: 'spanning-tree mode rapid-pvst', dev: 'switch', run: (c) => { needSwitch(c).stpMode = 'rapid-pvst'; c.sh.touch(); } },
  { modes: ['config'], pat: 'spanning-tree mode mst', dev: 'switch', run: (c) => { needSwitch(c).stpMode = 'mst'; c.sh.touch(); } },
  { modes: ['config'], pat: 'spanning-tree vlan <vlans> priority <n>', dev: 'switch', run: (c) => {
    const p = Number(c.a[1]);
    if (!stpPriorityOk(c, p)) return;
    for (const v of parseVlanList(c.a[0])!) needSwitch(c).stpPriority.set(v, p);
    c.sh.touch();
  } },
  { modes: ['config'], pat: 'spanning-tree vlan <vlans> root primary', dev: 'switch', run: (c) => {
    for (const v of parseVlanList(c.a[0])!) needSwitch(c).stpPriority.set(v, 24576);
    c.sh.touch();
  } },
  { modes: ['config'], pat: 'spanning-tree vlan <vlans> root secondary', dev: 'switch', run: (c) => {
    for (const v of parseVlanList(c.a[0])!) needSwitch(c).stpPriority.set(v, 28672);
    c.sh.touch();
  } },
  { modes: ['config'], pat: 'no spanning-tree vlan <vlans> priority', dev: 'switch', run: (c) => {
    for (const v of parseVlanList(c.a[0])!) needSwitch(c).stpPriority.delete(v);
    c.sh.touch();
  } },
  { modes: ['config'], pat: 'no spanning-tree vlan <vlans>', dev: 'switch', run: (c) => {
    for (const v of parseVlanList(c.a[0])!) needSwitch(c).stpDisabled.add(v);
    c.sh.touch();
  } },
  { modes: ['config'], pat: 'spanning-tree vlan <vlans>', dev: 'switch', run: (c) => {
    for (const v of parseVlanList(c.a[0])!) needSwitch(c).stpDisabled.delete(v);
    c.sh.touch();
  } },
  { modes: ['config'], pat: 'spanning-tree portfast default', dev: 'switch', run: (c) => {
    needSwitch(c).portfastDefault = true;
    c.write('%Warning: this command enables portfast by default on all interfaces. You\n should now disable portfast explicitly on switched ports leading to hubs,\n switches and bridges as they may create temporary bridging loops.\n');
    c.sh.touch();
  } },
  { modes: ['config'], pat: 'spanning-tree portfast bpduguard default', dev: 'switch', run: (c) => { needSwitch(c).bpduguardDefault = true; c.sh.touch(); } },
  { modes: ['config'], pat: 'no spanning-tree portfast bpduguard default', dev: 'switch', run: (c) => { needSwitch(c).bpduguardDefault = false; c.sh.touch(); } },
  { modes: ['config'], pat: 'spanning-tree mst configuration', dev: 'switch', run: (c) => void (c.sh.mode = 'mst') },
  { modes: ['mst'], pat: 'instance <n> vlan <vlans>', dev: 'switch', run: (c) => {
    const sw = needSwitch(c);
    const inst = Number(c.a[0]);
    if (inst < 1 || inst > 4094) return c.write(INVALID);
    const vs = parseVlanList(c.a[1])!;
    for (const [i, list] of sw.mstInstances) sw.mstInstances.set(i, list.filter((v) => !vs.includes(v)));
    sw.mstInstances.set(inst, [...new Set([...(sw.mstInstances.get(inst) ?? []), ...vs])].sort((a, b) => a - b));
    c.sh.touch();
  } },
  { modes: ['mst'], pat: 'name <word>', dev: 'switch', run: () => {} },
  { modes: ['mst'], pat: 'revision <n>', dev: 'switch', run: () => {} },
  { modes: ['config'], pat: 'spanning-tree mst <n> priority <n>', dev: 'switch', run: (c) => {
    const p = Number(c.a[1]);
    if (!stpPriorityOk(c, p)) return;
    needSwitch(c).mstPriority.set(Number(c.a[0]), p);
    c.sh.touch();
  } },
  { modes: ['config'], pat: 'spanning-tree mst <n> root primary', dev: 'switch', run: (c) => { needSwitch(c).mstPriority.set(Number(c.a[0]), 24576); c.sh.touch(); } },
  { modes: ['config'], pat: 'spanning-tree mst <n> root secondary', dev: 'switch', run: (c) => { needSwitch(c).mstPriority.set(Number(c.a[0]), 28672); c.sh.touch(); } },

  // ----- access lists -----
  { modes: ['config'], pat: 'access-list <n> <word> <rest>', dev: 'router', run: (c) => {
    const n = Number(c.a[0]);
    if (!((n >= 1 && n <= 199) || (n >= 1300 && n <= 2699))) return c.write(INVALID);
    addAce(c, c.a[0], null, c.a[1].toLowerCase(), c.a[2]);
  } },
  { modes: ['config'], pat: 'access-list <n> remark <rest>', dev: 'router', run: () => {} },
  { modes: ['config'], pat: 'no access-list <n>', dev: 'router', run: (c) => {
    needRouter(c).acls.delete(c.a[0]);
    c.sh.touch();
  } },
  { modes: ['config'], pat: 'ip access-list extended <word>', dev: 'router', run: (c) => {
    const r = needRouter(c);
    if (!r.acls.has(c.a[0])) r.acls.set(c.a[0], { kind: 'extended', entries: [] });
    c.sh.aclName = c.a[0];
    c.sh.mode = 'acl';
  } },
  { modes: ['config'], pat: 'ip access-list standard <word>', dev: 'router', run: (c) => {
    const r = needRouter(c);
    if (!r.acls.has(c.a[0])) r.acls.set(c.a[0], { kind: 'standard', entries: [] });
    c.sh.aclName = c.a[0];
    c.sh.mode = 'acl';
  } },
  { modes: ['config'], pat: 'no ip access-list extended <word>', dev: 'router', run: (c) => {
    needRouter(c).acls.delete(c.a[0]);
    c.sh.touch();
  } },
  { modes: ['acl'], pat: '<n> <word> <rest>', dev: 'router', run: (c) => addAce(c, c.sh.aclName, Number(c.a[0]), c.a[1].toLowerCase(), c.a[2]) },
  { modes: ['acl'], pat: 'permit <rest>', dev: 'router', run: (c) => addAce(c, c.sh.aclName, null, 'permit', c.a[0]) },
  { modes: ['acl'], pat: 'deny <rest>', dev: 'router', run: (c) => addAce(c, c.sh.aclName, null, 'deny', c.a[0]) },
  { modes: ['acl'], pat: 'remark <rest>', dev: 'router', run: () => {} },
  { modes: ['acl'], pat: 'no <n>', dev: 'router', run: (c) => {
    const acl = needRouter(c).acls.get(c.sh.aclName);
    if (acl) acl.entries = acl.entries.filter((e) => e.seq !== Number(c.a[0]));
    c.sh.touch();
  } },
  { modes: ['acl'], pat: 'exit', run: (c) => void (c.sh.mode = 'config') },
  { modes: ['if', 'subif'], pat: 'ip access-group <word> in', dev: 'router', run: (c) => each(c, (i) => void (i.cfg.aclIn = c.a[0])) },
  { modes: ['if', 'subif'], pat: 'ip access-group <word> out', dev: 'router', run: (c) => each(c, (i) => void (i.cfg.aclOut = c.a[0])) },
  { modes: ['if', 'subif'], pat: 'no ip access-group <word> in', dev: 'router', run: (c) => each(c, (i) => void delete i.cfg.aclIn) },
  { modes: ['if', 'subif'], pat: 'no ip access-group <word> out', dev: 'router', run: (c) => each(c, (i) => void delete i.cfg.aclOut) },
  { modes: EXEC, pat: 'show access-lists', dev: 'router', run: (c) => c.write(show.showAccessLists(needRouter(c), null)) },
  { modes: EXEC, pat: 'show access-lists <word>', dev: 'router', run: (c) => c.write(show.showAccessLists(needRouter(c), c.a[0])) },
  { modes: EXEC, pat: 'show ip access-lists', dev: 'router', run: (c) => c.write(show.showAccessLists(needRouter(c), null)) },
  { modes: EXEC, pat: 'show ip interface <if>', dev: 'router', run: (c) => {
    const p = findPort(c.sh.device, c.a[0]);
    const sub = parseIfName(c.a[0])?.sub;
    const i = p && sub !== null && sub !== undefined ? c.sh.device.getInterface(`${p.name}.${sub}`) : p;
    c.write(i ? show.showIpInterface(needRouter(c), i) : INVALID);
  } },

  // ----- router ospf -----
  { modes: ['router'], pat: 'network <ip> <ip> area <n>', dev: 'router', run: (c) => {
    const r = c.sh.ospfOn(c.write);
    if (!r) return;
    const addr = parseIp(c.a[0])!;
    const wildcard = parseIp(c.a[1])!;
    const area = Number(c.a[2]);
    const dup = r.ospf!.networks.find((n) => n.addr === addr && n.wildcard === wildcard);
    if (dup) dup.area = area;
    else r.ospf!.networks.push({ addr, wildcard, area });
    c.sh.touch();
  } },
  { modes: ['router'], pat: 'no network <ip> <ip> area <n>', dev: 'router', run: (c) => {
    const r = c.sh.ospfOn(c.write);
    if (!r) return;
    const addr = parseIp(c.a[0])!;
    const wildcard = parseIp(c.a[1])!;
    r.ospf!.networks = r.ospf!.networks.filter((n) => !(n.addr === addr && n.wildcard === wildcard));
    c.sh.touch();
  } },
  { modes: ['router'], pat: 'passive-interface default', dev: 'router', run: (c) => {
    const r = c.sh.ospfOn(c.write);
    if (!r) return;
    r.ospf!.passiveDefault = true;
    r.ospf!.nonPassive = [];
    c.sh.touch();
  } },
  { modes: ['router'], pat: 'no passive-interface default', dev: 'router', run: (c) => {
    const r = c.sh.ospfOn(c.write);
    if (!r) return;
    r.ospf!.passiveDefault = false;
    r.ospf!.passive = [];
    c.sh.touch();
  } },
  { modes: ['router'], pat: 'passive-interface <if>', dev: 'router', run: (c) => {
    const r = c.sh.ospfOn(c.write);
    const p = r && findPort(r, c.a[0]);
    if (!r || !p) return void (r && c.write(INVALID));
    const name = parseIfName(c.a[0])!.sub !== null ? `${p.name}.${parseIfName(c.a[0])!.sub}` : p.name;
    const o = r.ospf!;
    if (o.passiveDefault) o.nonPassive = o.nonPassive.filter((n) => n !== name);
    else if (!o.passive.includes(name)) o.passive.push(name);
    c.sh.touch();
  } },
  { modes: ['router'], pat: 'no passive-interface <if>', dev: 'router', run: (c) => {
    const r = c.sh.ospfOn(c.write);
    const p = r && findPort(r, c.a[0]);
    if (!r || !p) return void (r && c.write(INVALID));
    const name = parseIfName(c.a[0])!.sub !== null ? `${p.name}.${parseIfName(c.a[0])!.sub}` : p.name;
    const o = r.ospf!;
    if (o.passiveDefault) {
      if (!o.nonPassive.includes(name)) o.nonPassive.push(name);
    } else o.passive = o.passive.filter((n) => n !== name);
    c.sh.touch();
  } },
  { modes: ['router'], pat: 'router-id <ip>', dev: 'router', run: (c) => {
    const r = c.sh.ospfOn(c.write);
    if (!r) return;
    r.ospf!.routerId = parseIp(c.a[0]);
    c.sh.touch();
  } },
  { modes: ['router'], pat: 'auto-cost reference-bandwidth <n>', dev: 'router', run: (c) => {
    const r = c.sh.ospfOn(c.write);
    if (!r) return;
    r.ospf!.refBw = Math.max(1, Number(c.a[0]));
    c.write('% OSPF: Reference bandwidth is changed.\n        Please ensure reference bandwidth is consistent across all routers.\n');
    c.sh.touch();
  } },
  { modes: ['router'], pat: 'default-information originate', dev: 'router', run: (c) => {
    const r = c.sh.ospfOn(c.write);
    if (!r) return;
    r.ospf!.defaultOriginate = true;
    c.sh.touch();
  } },
  { modes: ['router'], pat: 'no default-information originate', dev: 'router', run: (c) => {
    const r = c.sh.ospfOn(c.write);
    if (!r) return;
    r.ospf!.defaultOriginate = false;
    c.sh.touch();
  } },

  // ----- interface (both) -----
  { modes: IF_MODES, pat: 'shutdown', run: (c) => each(c, (i) => {
    if (c.sh.sw) c.sh.sw.setPortUp(i, false);
    else i.up = false;
  }) },
  { modes: IF_MODES, pat: 'no shutdown', run: (c) => each(c, (i) => {
    if (c.sh.sw) c.sh.sw.setPortUp(i, true);
    else i.up = true;
  }) },
  { modes: IF_MODES, pat: 'description <rest>', run: (c) => each(c, (i) => void (i.cfg.description = c.a[0])) },
  { modes: IF_MODES, pat: 'no description', run: (c) => each(c, (i) => void delete i.cfg.description) },

  // ----- interface (router) -----
  { modes: ['if', 'subif'], pat: 'ip address <ip> <ip>', dev: 'router', run: (c) => {
    const r = needRouter(c);
    const i = c.sh.ifaces[0];
    const addr = parseIp(c.a[0])!;
    const prefix = maskToPrefix(parseIp(c.a[1])!);
    if (prefix === null || prefix > 30) return c.write('% Bad mask /' + (prefix ?? '?') + ' for address ' + c.a[0] + '\n');
    const net = networkOf(addr, prefix);
    if (addr === net || ((addr | (~(0xffffffff << (32 - prefix)) >>> 0)) >>> 0) === addr >>> 0) {
      return c.write(`Bad mask /${prefix} for address ${c.a[0]}\n`);
    }
    if (i instanceof SubInterface && i.vlan === null) {
      return c.write('% Configuring IP routing on a LAN subinterface is only allowed if that\nsubinterface is already configured as part of an IEEE 802.10, IEEE 802.1Q,\nor ISL vLAN.\n');
    }
    for (const o of r.interfaces) {
      if (o === i || !o.ip) continue;
      const p = Math.min(o.ip.prefix, prefix);
      if (networkOf(o.ip.addr, p) === networkOf(addr, p)) {
        return c.write(`% ${ipToString(networkOf(addr, prefix))} overlaps with ${iosName(r, o)}\n`);
      }
    }
    i.ip = { addr, prefix };
  } },
  { modes: ['if', 'subif'], pat: 'no ip address', dev: 'router', run: (c) => each(c, (i) => void (i.ip = null)) },
  { modes: ['subif'], pat: 'encapsulation dot1q <n>', dev: 'router', run: (c) => {
    const i = c.sh.ifaces[0] as SubInterface;
    i.vlan = Number(c.a[0]);
    i.nativeVlan = false;
    c.sh.touch();
  } },
  { modes: ['subif'], pat: 'encapsulation dot1q <n> native', dev: 'router', run: (c) => {
    const i = c.sh.ifaces[0] as SubInterface;
    i.vlan = Number(c.a[0]);
    i.nativeVlan = true;
    c.sh.touch();
  } },
  { modes: ['if'], pat: 'encapsulation dot1q <rest>', dev: 'router', run: (c) => c.write('% Encapsulation goes on a sub-interface. Try: interface g0/0.10\n') },
  { modes: ['if', 'subif'], pat: 'ip ospf cost <n>', dev: 'router', run: (c) => each(c, (i) => void (i.cfg.ospfCost = Math.max(1, Number(c.a[0])))) },
  { modes: ['if', 'subif'], pat: 'no ip ospf cost', dev: 'router', run: (c) => each(c, (i) => void delete i.cfg.ospfCost) },
  { modes: ['if', 'subif'], pat: 'ip ospf priority <n>', dev: 'router', run: (c) => each(c, (i) => void (i.cfg.ospfPriority = Math.min(255, Number(c.a[0])))) },
  { modes: ['if', 'subif'], pat: 'ip ospf <n> area <n>', dev: 'router', run: (c) => {
    const r = needRouter(c);
    if (!r.ospf) r.ospf = newOspf(Number(c.a[0]));
    each(c, (i) => void (i.cfg.ospfArea = Number(c.a[1])));
  } },
  { modes: ['if', 'subif'], pat: 'no ip ospf <n> area <n>', dev: 'router', run: (c) => each(c, (i) => void delete i.cfg.ospfArea) },
  { modes: ['if', 'subif'], pat: 'bandwidth <n>', dev: 'router', run: (c) => each(c, (i) => void (i.cfg.bandwidth = Math.max(1, Number(c.a[0])))) },
  { modes: ['if', 'subif'], pat: 'no bandwidth', dev: 'router', run: (c) => each(c, (i) => void delete i.cfg.bandwidth) },
  { modes: ['if', 'subif'], pat: 'standby version <n>', dev: 'router', run: () => {} },
  { modes: ['if', 'subif'], pat: 'standby <n> ip <ip>', dev: 'router', run: (c) => {
    for (const g of fhrpGroup(c, 'hsrp', Number(c.a[0]))) g.vip = parseIp(c.a[1]);
    c.sh.touch();
  } },
  { modes: ['if', 'subif'], pat: 'standby <n> priority <n>', dev: 'router', run: (c) => {
    for (const g of fhrpGroup(c, 'hsrp', Number(c.a[0]))) g.priority = Math.min(255, Number(c.a[1]));
    c.sh.touch();
  } },
  { modes: ['if', 'subif'], pat: 'standby <n> preempt', dev: 'router', run: (c) => {
    for (const g of fhrpGroup(c, 'hsrp', Number(c.a[0]))) g.preempt = true;
    c.sh.touch();
  } },
  { modes: ['if', 'subif'], pat: 'standby <n> track <if> decrement <n>', dev: 'router', run: (c) => trackCmd(c, c.a[1], Number(c.a[2])) },
  { modes: ['if', 'subif'], pat: 'standby <n> track <if> <n>', dev: 'router', run: (c) => trackCmd(c, c.a[1], Number(c.a[2])) },
  { modes: ['if', 'subif'], pat: 'standby <n> track <if>', dev: 'router', run: (c) => trackCmd(c, c.a[1], 10) },
  { modes: ['if', 'subif'], pat: 'no standby <n> track <if>', dev: 'router', run: (c) => {
    const port = findPort(c.sh.device, c.a[1]);
    for (const g of fhrpGroup(c, 'hsrp', Number(c.a[0]))) g.track = (g.track ?? []).filter((t) => t.iface !== port?.name);
    c.sh.touch();
  } },
  { modes: ['if', 'subif'], pat: 'no standby <n> preempt', dev: 'router', run: (c) => {
    for (const g of fhrpGroup(c, 'hsrp', Number(c.a[0]))) g.preempt = false;
    c.sh.touch();
  } },
  { modes: ['if', 'subif'], pat: 'no standby <n>', dev: 'router', run: (c) => each(c, (i) => {
    i.cfg.fhrp = (i.cfg.fhrp ?? []).filter((g) => !(g.proto === 'hsrp' && g.group === Number(c.a[0])));
  }) },
  { modes: ['if', 'subif'], pat: 'vrrp <n> ip <ip>', dev: 'router', run: (c) => {
    for (const g of fhrpGroup(c, 'vrrp', Number(c.a[0]))) g.vip = parseIp(c.a[1]);
    c.sh.touch();
  } },
  { modes: ['if', 'subif'], pat: 'vrrp <n> priority <n>', dev: 'router', run: (c) => {
    for (const g of fhrpGroup(c, 'vrrp', Number(c.a[0]))) g.priority = Math.min(254, Number(c.a[1]));
    c.sh.touch();
  } },
  { modes: ['if', 'subif'], pat: 'no vrrp <n>', dev: 'router', run: (c) => each(c, (i) => {
    i.cfg.fhrp = (i.cfg.fhrp ?? []).filter((g) => !(g.proto === 'vrrp' && g.group === Number(c.a[0])));
  }) },

  // ----- interface (switch) -----
  { modes: ['if', 'range'], pat: 'switchport mode access', dev: 'switch', run: (c) => each(c, (i) => void (i.cfg.mode = 'access')) },
  { modes: ['if', 'range'], pat: 'switchport mode trunk', dev: 'switch', run: (c) => each(c, (i) => void (i.cfg.mode = 'trunk')) },
  { modes: ['if', 'range'], pat: 'switchport mode dynamic <word>', dev: 'switch', run: (c) => each(c, (i) => void delete i.cfg.mode) },
  { modes: ['if', 'range'], pat: 'switchport trunk encapsulation dot1q', dev: 'switch', run: () => {} },
  { modes: ['if', 'range'], pat: 'switchport access vlan <n>', dev: 'switch', run: (c) => {
    const v = Number(c.a[0]);
    if (v < 1 || v > 4094) return c.write(INVALID);
    setAccessVlan(c, v);
  } },
  { modes: ['if', 'range'], pat: 'no switchport access vlan', dev: 'switch', run: (c) => setAccessVlan(c, 1) },
  { modes: ['if', 'range'], pat: 'switchport trunk native vlan <n>', dev: 'switch', run: (c) => each(c, (i) => void (i.cfg.native = Number(c.a[0]))) },
  { modes: ['if', 'range'], pat: 'no switchport trunk native vlan', dev: 'switch', run: (c) => each(c, (i) => void delete i.cfg.native) },
  { modes: ['if', 'range'], pat: 'switchport trunk allowed vlan add <vlans>', dev: 'switch', run: (c) => each(c, (i) => {
    if (!i.cfg.allowed) return;
    i.cfg.allowed = [...new Set([...i.cfg.allowed, ...parseVlanList(c.a[0])!])].sort((a, b) => a - b);
  }) },
  { modes: ['if', 'range'], pat: 'switchport trunk allowed vlan remove <vlans>', dev: 'switch', run: (c) => each(c, (i) => {
    const rm = parseVlanList(c.a[0])!;
    const base = i.cfg.allowed ?? Array.from({ length: 4094 }, (_, k) => k + 1);
    i.cfg.allowed = base.filter((v) => !rm.includes(v));
  }) },
  { modes: ['if', 'range'], pat: 'switchport trunk allowed vlan all', dev: 'switch', run: (c) => each(c, (i) => void (i.cfg.allowed = null)) },
  { modes: ['if', 'range'], pat: 'switchport trunk allowed vlan none', dev: 'switch', run: (c) => each(c, (i) => void (i.cfg.allowed = [])) },
  { modes: ['if', 'range'], pat: 'switchport trunk allowed vlan <vlans>', dev: 'switch', run: (c) => each(c, (i) => void (i.cfg.allowed = parseVlanList(c.a[0])!)) },
  { modes: ['if', 'range'], pat: 'switchport nonegotiate', dev: 'switch', run: (c) => each(c, (i) => void (i.cfg.nonegotiate = true)) },
  { modes: ['if', 'range'], pat: 'no switchport nonegotiate', dev: 'switch', run: (c) => each(c, (i) => void delete i.cfg.nonegotiate) },
  { modes: ['if', 'range'], pat: 'switchport port-security', dev: 'switch', run: (c) => {
    if (!requireAccess(c)) return;
    each(c, (i) => void (portSec(i).enabled = true));
  } },
  { modes: ['if', 'range'], pat: 'no switchport port-security', dev: 'switch', run: (c) => each(c, (i) => {
    if (i.cfg.portSecurity) i.cfg.portSecurity.enabled = false;
  }) },
  { modes: ['if', 'range'], pat: 'switchport port-security maximum <n>', dev: 'switch', run: (c) => {
    if (!requireAccess(c)) return;
    each(c, (i) => void (portSec(i).max = Math.max(1, Number(c.a[0]))));
  } },
  { modes: ['if', 'range'], pat: 'switchport port-security violation shutdown', dev: 'switch', run: (c) => { if (requireAccess(c)) each(c, (i) => void (portSec(i).violation = 'shutdown')); } },
  { modes: ['if', 'range'], pat: 'switchport port-security violation restrict', dev: 'switch', run: (c) => { if (requireAccess(c)) each(c, (i) => void (portSec(i).violation = 'restrict')); } },
  { modes: ['if', 'range'], pat: 'switchport port-security violation protect', dev: 'switch', run: (c) => { if (requireAccess(c)) each(c, (i) => void (portSec(i).violation = 'protect')); } },
  { modes: ['if', 'range'], pat: 'switchport port-security mac-address sticky', dev: 'switch', run: (c) => { if (requireAccess(c)) each(c, (i) => void (portSec(i).sticky = true)); } },
  { modes: ['if', 'range'], pat: 'switchport port-security mac-address sticky <mac>', dev: 'switch', run: (c) => {
    if (!requireAccess(c)) return;
    each(c, (i) => {
      const ps = portSec(i);
      ps.sticky = true;
      const m = parseMac(c.a[0])!;
      if (!ps.macs.includes(m)) ps.macs.push(m);
    });
  } },
  { modes: ['if', 'range'], pat: 'switchport port-security mac-address <mac>', dev: 'switch', run: (c) => {
    if (!requireAccess(c)) return;
    each(c, (i) => {
      const ps = portSec(i);
      const m = parseMac(c.a[0])!;
      if (!ps.macs.includes(m)) ps.macs.push(m);
    });
  } },
  { modes: ['if', 'range'], pat: 'spanning-tree portfast', dev: 'switch', run: (c) => {
    each(c, (i) => void (i.cfg.portfast = true));
    c.write('%Warning: portfast should only be enabled on ports connected to a single\n host. Connecting hubs, concentrators, switches, bridges, etc... to this\n interface  when portfast is enabled, can cause temporary bridging loops.\n Use with CAUTION\n');
  } },
  { modes: ['if', 'range'], pat: 'no spanning-tree portfast', dev: 'switch', run: (c) => each(c, (i) => void delete i.cfg.portfast) },
  { modes: ['if', 'range'], pat: 'spanning-tree bpduguard enable', dev: 'switch', run: (c) => each(c, (i) => void (i.cfg.bpduguard = true)) },
  { modes: ['if', 'range'], pat: 'spanning-tree bpduguard disable', dev: 'switch', run: (c) => each(c, (i) => void (i.cfg.bpduguard = false)) },
  { modes: ['if', 'range'], pat: 'no spanning-tree bpduguard', dev: 'switch', run: (c) => each(c, (i) => void delete i.cfg.bpduguard) },
  { modes: ['if', 'range'], pat: 'spanning-tree guard root', dev: 'switch', run: (c) => each(c, (i) => void (i.cfg.rootguard = true)) },
  { modes: ['if', 'range'], pat: 'spanning-tree guard none', dev: 'switch', run: (c) => each(c, (i) => void delete i.cfg.rootguard) },
  { modes: ['if', 'range'], pat: 'no spanning-tree guard', dev: 'switch', run: (c) => each(c, (i) => void delete i.cfg.rootguard) },
  { modes: ['if', 'range'], pat: 'spanning-tree cost <n>', dev: 'switch', run: (c) => each(c, (i) => void (i.cfg.stpCost = Math.max(1, Number(c.a[0])))) },
  { modes: ['if', 'range'], pat: 'no spanning-tree cost', dev: 'switch', run: (c) => each(c, (i) => void delete i.cfg.stpCost) },
];
