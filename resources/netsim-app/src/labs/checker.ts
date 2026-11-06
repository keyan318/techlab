// Runs lab objectives against the live simulation. Passive checks read
// engine state; active checks (ping/tcp/dns/http) send real packets and
// wait on the virtual clock, so students watch the grader's traffic.
import type { Network } from '../engine/network';
import { IpDevice } from '../engine/ipdevice';
import { cidrToString, ipToString, networkOf, parseCidr, parseIp, type U32 } from '../engine/ip';
import type { Check, IfaceExpect, Objective } from './types';
import type { Device, NetInterface } from '../engine/device';
import { SubInterface } from '../engine/device';
import { SwitchDevice } from '../engine/switch';
import { stpFor } from '../engine/stp';
import { ospfNeighbors, ospfPassive } from '../engine/ospf';
import { activeGroups, fhrpActive } from '../engine/fhrp';
import { l2Peers } from '../engine/l2';
import { iosName } from '../commands/ios/names';
import { aceText } from '../engine/acl';
import { shellOs } from '../os';
import { adapterNameOf, prefixToMask } from '../commands/windows/util';

export interface CheckResult {
  id: string;
  pass: boolean;
  detail: string;
}

const PING_TIMEOUT_MS = 3500;
const TCP_TIMEOUT_MS = 3000;
const HTTP_TIMEOUT_MS = 4000;

function findDevice(net: Network, name: string): IpDevice | null {
  for (const d of net.devices.values()) {
    if (d.name === name && d instanceof IpDevice) return d;
  }
  return null;
}

function findAny(net: Network, name: string): Device | null {
  for (const d of net.devices.values()) if (d.name === name) return d;
  return null;
}

// What an interface is called to the student: Cisco names on IOS devices, Windows names on PCs.
function ifLabel(d: Device, i: NetInterface): string {
  if (d.cli === 'ios') return iosName(d, i);
  return shellOs() === 'windows' && d instanceof IpDevice ? `"${adapterNameOf(d, i.name)}"` : i.name;
}

// Follows the routing tables hop by hop (no packets), returning the devices traffic passes through.
export function routedPath(from: IpDevice, dst: U32): { hops: string[]; reached: boolean } {
  const hops: string[] = [];
  let cur: IpDevice = from;
  for (let n = 0; n < 24; n++) {
    hops.push(cur.name);
    if (cur.hasIp(dst) || cur.ownsVip(dst)) return { hops, reached: true };
    if (n > 0 && !cur.forwarding) return { hops, reached: false };
    const route = cur.lookupRoute(dst);
    if (!route) return { hops, reached: false };
    const out = cur.getInterface(route.ifaceName);
    if (!out) return { hops, reached: false };
    const nh = route.via ?? dst;
    const peers = l2Peers(out);
    const next =
      peers.find((p) => p.ip?.addr === nh) ?? peers.find((p) => activeGroups(p).some((g) => g.vip === nh));
    if (!next || !(next.device instanceof IpDevice)) return { hops, reached: false };
    cur = next.device;
  }
  return { hops, reached: false };
}

function ifaceMatches(d: Device, i: NetInterface, is: IfaceExpect): string | null {
  const c = i.cfg;
  const sw = d instanceof SwitchDevice ? d : null;
  const fail = (what: string) => what;
  if (is.mode !== undefined && (c.mode ?? 'dynamic') !== is.mode) return fail(`mode is ${c.mode ?? 'dynamic auto'}`);
  if (is.accessVlan !== undefined && sw && sw.vlanOf(i.name) !== is.accessVlan) return fail(`access VLAN is ${sw.vlanOf(i.name)}`);
  if (is.native !== undefined && (c.native ?? 1) !== is.native) return fail(`native VLAN is ${c.native ?? 1}`);
  if (is.allowed !== undefined) {
    const got = c.allowed ? [...c.allowed].sort((a, b) => a - b).join(',') : 'all';
    if (got !== [...is.allowed].sort((a, b) => a - b).join(',')) return fail(`allowed VLANs: ${got}`);
  }
  if (is.nonegotiate !== undefined && !!c.nonegotiate !== is.nonegotiate) return fail(`DTP negotiation ${c.nonegotiate ? 'off' : 'on'}`);
  if (is.portfast !== undefined && !!c.portfast !== is.portfast) return fail(`PortFast ${c.portfast ? 'on' : 'off'}`);
  if (is.bpduguard !== undefined && !!c.bpduguard !== is.bpduguard) return fail(`BPDU Guard ${c.bpduguard ? 'on' : 'off'}`);
  if (is.rootguard !== undefined && !!c.rootguard !== is.rootguard) return fail(`Root Guard ${c.rootguard ? 'on' : 'off'}`);
  const ps = c.portSecurity;
  if (is.portSecurity !== undefined && !!ps?.enabled !== is.portSecurity) return fail(`port security ${ps?.enabled ? 'on' : 'off'}`);
  if (is.psMax !== undefined && ps?.max !== is.psMax) return fail(`maximum ${ps?.max ?? 'not set'}`);
  if (is.psViolation !== undefined && ps?.violation !== is.psViolation) return fail(`violation mode ${ps?.violation ?? 'not set'}`);
  if (is.psSticky !== undefined && !!ps?.sticky !== is.psSticky) return fail(`sticky ${ps?.sticky ? 'on' : 'off'}`);
  if (is.ospfCost !== undefined && c.ospfCost !== is.ospfCost) return fail(`OSPF cost ${c.ospfCost ?? 'default'}`);
  if (is.ospfPassive !== undefined && d instanceof IpDevice && ospfPassive(d, i) !== is.ospfPassive) return fail(ospfPassive(d, i) ? 'passive' : 'not passive');
  if (is.shutdown !== undefined && (!i.up && !c.errDisabled) !== is.shutdown) return fail(i.up ? 'up (no shutdown)' : 'shut down');
  if (is.dot1q !== undefined && (!(i instanceof SubInterface) || i.vlan !== is.dot1q)) return fail(`no encapsulation dot1Q ${is.dot1q}`);
  if (is.aclIn !== undefined && c.aclIn !== is.aclIn) return fail(c.aclIn ? `inbound access list is ${c.aclIn}` : 'no inbound access list');
  if (is.aclOut !== undefined && c.aclOut !== is.aclOut) return fail(c.aclOut ? `outbound access list is ${c.aclOut}` : 'no outbound access list');
  if (is.fhrpTrack !== undefined && !(c.fhrp ?? []).some((g) => (g.track ?? []).some((t) => t.iface === is.fhrpTrack))) return fail(`not tracking ${is.fhrpTrack}`);
  if (is.fhrpPreempt !== undefined && !!(c.fhrp ?? []).some((g) => g.preempt) !== is.fhrpPreempt) return fail(`preempt ${is.fhrpPreempt ? 'not set' : 'set'}`);
  return null;
}

function targetAddr(net: Network, toDevice?: string, toAddr?: string): U32 | null {
  if (toAddr) return parseIp(toAddr);
  if (toDevice) {
    const d = findDevice(net, toDevice);
    const iface = d?.interfaces.find((i) => i.ip);
    return iface?.ip?.addr ?? null;
  }
  return null;
}

// TechLab: when the lesson text told the student to run this themselves (Check.requireOwnCommand, set
// by labs/student-ran.ts from the step's own body), a live pass alone isn't enough — their own command
// must have observed the same outcome, or a typo'd command rides free on a network that's already right.
// See IpDevice.recordActivity (engine/ipdevice.ts) for where that gets logged.
function gateOnActivity(
  from: IpDevice,
  requireOwnCommand: boolean | undefined,
  kind: string,
  key: string,
  expect: string,
  done: (pass: boolean, detail: string) => void,
): (pass: boolean, detail: string) => void {
  if (!requireOwnCommand) return done;
  return (pass, detail) => {
    if (!pass) return done(false, detail);
    const ran = from.activity.get(`${kind}:${key}`.toLowerCase());
    if (ran?.outcome !== expect) return done(false, `the network's right — now try it yourself from ${from.name}'s console`);
    done(true, detail);
  };
}

function runOne(net: Network, check: Check, done: (pass: boolean, detail: string) => void): void {
  if (runN2(net, check, done)) return;
  switch (check.type) {
    case 'iface-ip': {
      const d = findDevice(net, check.device);
      const iface = d?.getInterface(check.iface);
      if (!d || !iface) return done(false, `no device/interface ${check.device}/${check.iface}`);
      const win = shellOs() === 'windows';
      const label = win ? `"${adapterNameOf(d, check.iface)}"` : check.iface;
      if (!iface.ip) return done(false, `${label} has no address yet`);
      const got = cidrToString(iface.ip.addr, iface.ip.prefix);
      const shown = win ? `${ipToString(iface.ip.addr)} mask ${prefixToMask(iface.ip.prefix)}` : got;
      if (check.cidr) return done(got === check.cidr, `${label} is ${shown}`);
      if (check.inSubnet) {
        const net_ = parseCidr(check.inSubnet);
        if (!net_) return done(false, 'bad subnet in lab definition');
        const ok =
          networkOf(iface.ip.addr, net_.prefix) === networkOf(net_.addr, net_.prefix) &&
          iface.ip.prefix === net_.prefix;
        return done(ok, `${label} is ${shown}`);
      }
      return done(true, `${label} is ${shown}`);
    }
    case 'default-route': {
      const d = findDevice(net, check.device);
      if (!d) return done(false, `no device ${check.device}`);
      const def = d.routes().find((r) => r.prefix === 0);
      if (!def) return done(false, 'no default route');
      if (check.via) {
        const via = parseIp(check.via);
        return done(
          def.via === via,
          `default via ${def.via !== null ? ipToString(def.via) : '(direct)'}`,
        );
      }
      return done(true, 'default route present');
    }
    case 'forwarding': {
      const d = findDevice(net, check.device);
      if (!d) return done(false, `no device ${check.device}`);
      const fwd = shellOs() === 'windows' ? `IP routing ${d.forwarding ? 'enabled' : 'disabled'}` : `ip_forward = ${d.forwarding ? 1 : 0}`;
      return done(d.forwarding === check.expect, fwd);
    }
    case 'fw-policy': {
      const d = findDevice(net, check.device);
      if (!d) return done(false, `no device ${check.device}`);
      const chain = d.fw.filter[check.hook];
      if (shellOs() === 'windows') {
        const what = check.hook === 'output' ? 'outbound' : check.hook === 'forward' ? 'routed traffic' : 'inbound';
        if (!chain.declared) return done(false, 'Windows Defender Firewall is off');
        return done(chain.policy === check.policy, `${what} policy is ${chain.policy === 'drop' ? 'block' : 'allow'}`);
      }
      if (!chain.declared) return done(false, `${check.hook} chain not declared`);
      return done(chain.policy === check.policy, `${check.hook} policy is ${chain.policy}`);
    }
    case 'ping': {
      const from = findDevice(net, check.from);
      if (!from) return done(false, `no device ${check.from}`);
      const target = targetAddr(net, check.toDevice, check.toAddr);
      if (target === null) return done(false, 'target has no address');
      const gated = gateOnActivity(from, check.requireOwnCommand, 'ping', ipToString(target), check.expect, done);
      const wantSuccess = check.expect === 'success';
      const id = from.allocIcmpId();
      let finished = false;
      const finish = (gotReply: boolean, note: string, sent = true) => {
        if (finished) return;
        finished = true;
        from.offIcmp(id);
        gated(sent && gotReply === wantSuccess, note);
      };
      from.onIcmp(id, (ev) => {
        if (ev.type === 'reply') finish(true, `reply from ${ipToString(ev.from)}`);
        else if (ev.type === 'unreachable') finish(false, `unreachable (from ${ipToString(ev.from)})`);
        else if (ev.type === 'send-error') finish(false, ev.message);
      });
      const err = from.sendEcho(target, id, 1);
      // TechLab: a ping that never left the sender (no address or route yet) proves nothing, so it cannot
      // pass an "expect fail" objective either.
      if (err) return finish(false, err, false);
      net.scheduler.schedule(PING_TIMEOUT_MS, () => finish(false, 'no reply (timed out)'));
      return;
    }
    case 'tcp': {
      const from = findDevice(net, check.from);
      if (!from) return done(false, `no device ${check.from}`);
      const target = targetAddr(net, check.toDevice, check.toAddr);
      if (target === null) return done(false, 'target has no address');
      const gated = gateOnActivity(from, check.requireOwnCommand, 'tcp', `${ipToString(target)}:${check.port}`, check.expect, done);
      const err = from.connectTcp(target, check.port, TCP_TIMEOUT_MS, (r) => {
        const observed = r === 'open' ? 'open' : r === 'refused' ? 'refused' : 'filtered';
        gated(observed === check.expect, `port ${check.port} is ${observed}`);
      });
      // TechLab: a probe that never left the sender is not a port the defender filtered.
      if (err) gated(false, err);
      return;
    }
    case 'dns': {
      const from = findDevice(net, check.from);
      if (!from) return done(false, `no device ${check.from}`);
      if (from.nameserver === null) return done(false, 'no nameserver configured');
      const gated = gateOnActivity(from, check.requireOwnCommand, 'dns', check.name, check.expect, done);
      from.resolveDns(check.name, from.nameserver, (r) => {
        if (typeof r === 'number') {
          if (check.expect !== 'resolves') return gated(false, `resolved to ${ipToString(r)}`);
          if (check.addr) return gated(ipToString(r) === check.addr, `resolved to ${ipToString(r)}`);
          return gated(true, `resolved to ${ipToString(r)}`);
        }
        if (r === null) return gated(check.expect === 'nxdomain', 'NXDOMAIN');
        return gated(check.expect === 'blocked', `lookup failed (${r})`);
      });
      return;
    }
    case 'http': {
      const from = findDevice(net, check.from);
      if (!from) return done(false, `no device ${check.from}`);
      let rest = check.url.startsWith('http://') ? check.url.slice(7) : check.url;
      const slash = rest.indexOf('/');
      const hostPort = slash < 0 ? rest : rest.slice(0, slash);
      const path = slash < 0 ? '/' : rest.slice(slash);
      const colon = hostPort.indexOf(':');
      const host = colon < 0 ? hostPort : hostPort.slice(0, colon);
      const port = colon < 0 ? 80 : Number(hostPort.slice(colon + 1));
      const wantOk = check.expect === 'ok';
      const gated = gateOnActivity(from, check.requireOwnCommand, 'http', host, check.expect, done);
      const withIp = (ip: U32) => {
        const err = from.httpGet(ip, port, path, host, HTTP_TIMEOUT_MS, (r) => {
          if (r.ok) {
            if (!wantOk) return gated(false, `got HTTP ${r.status}`);
            if (check.contains && !r.body.includes(check.contains))
              return gated(false, 'page is missing the expected content');
            return gated(true, `HTTP ${r.status} ${r.reason}`);
          }
          return gated(!wantOk, `fetch failed (${r.error})`);
        });
        if (err) gated(!wantOk, err);
      };
      const literal = parseIp(host);
      if (literal !== null) return withIp(literal);
      if (from.nameserver === null) return gated(!wantOk, 'cannot resolve host (no nameserver)');
      from.resolveDns(host, from.nameserver, (r) => {
        if (typeof r === 'number') withIp(r);
        else gated(!wantOk, `cannot resolve ${host}`);
      });
      return;
    }
  }
}

function runN2(net: Network, check: Check, done: (pass: boolean, detail: string) => void): boolean {
  switch (check.type) {
    case 'ospf-neighbor': {
      const d = findDevice(net, check.device);
      const nb = findDevice(net, check.neighbor);
      if (!d || !nb) return done(false, 'device not found'), true;
      const up = ospfNeighbors(d).some((n) => n.device === nb);
      done(up === (check.expect === 'full'), up ? `${check.neighbor} is a FULL neighbour` : `no OSPF neighbour ${check.neighbor}`);
      return true;
    }
    case 'route': {
      const d = findDevice(net, check.device);
      const want = parseCidr(check.dest);
      if (!d || !want) return done(false, 'device not found'), true;
      const dest = networkOf(want.addr, want.prefix);
      const rs = d.routes().filter((x) => x.dest === dest && x.prefix === want.prefix && (!check.source || x.kind === check.source));
      const r = rs[0];
      if (check.paths && check.expect === 'present') {
        done(rs.length >= check.paths, `${rs.length} path${rs.length === 1 ? '' : 's'} to ${check.dest}`);
        return true;
      }
      done(!!r === (check.expect === 'present'), r ? `${check.dest} is in the routing table (${r.kind})` : `no ${check.source ?? ''} route to ${check.dest}`.replace('  ', ' '));
      return true;
    }
    case 'survives': {
      const from = findDevice(net, check.from);
      const dst = targetAddr(net, check.toDevice, check.toAddr);
      const cutDev = findAny(net, check.cut.device);
      const cut = cutDev?.getInterface(check.cut.iface);
      if (!from || dst === null || !cutDev || !cut) return done(false, 'device not found'), true;
      // Simulate the failure, follow the routing (OSPF and HSRP/VRRP react to it), then undo everything.
      const fhrp = new Map(net.fhrpActive);
      const wasUp = cut.up;
      cut.up = false;
      const p = routedPath(from, dst);
      cut.up = wasUp;
      net.fhrpActive.clear();
      for (const [k, v] of fhrp) net.fhrpActive.set(k, v);
      done(p.reached, p.reached ? `with ${check.cut.device} ${ifLabel(cutDev, cut)} down: ${p.hops.join(' > ')}` : `with ${check.cut.device} ${ifLabel(cutDev, cut)} down, traffic stops at ${p.hops[p.hops.length - 1]}`);
      return true;
    }
    case 'path': {
      const from = findDevice(net, check.from);
      const dst = targetAddr(net, check.toDevice, check.toAddr);
      if (!from || dst === null) return done(false, 'device not found'), true;
      const p = routedPath(from, dst);
      if (!p.reached) return done(false, `no route all the way (stops at ${p.hops[p.hops.length - 1]})`), true;
      const via = p.hops.includes(check.through);
      done(via === (check.expect === 'through'), `path: ${p.hops.join(' > ')}`);
      return true;
    }
    case 'stp-root': {
      const d = findAny(net, check.device);
      if (!d) return done(false, 'device not found'), true;
      const info = stpFor(net, check.vlan).get(d.id);
      const root = [...stpFor(net, check.vlan).entries()].find(([, s]) => s.isRoot && s.rootId === info?.rootId);
      const rootName = root ? net.devices.get(root[0])?.name : 'none';
      done(!!info?.isRoot, `root bridge for VLAN ${check.vlan} is ${rootName}`);
      return true;
    }
    case 'stp-mode': {
      const d = findAny(net, check.device);
      if (!(d instanceof SwitchDevice)) return done(false, 'device not found'), true;
      done(d.stpMode === check.mode, `spanning-tree mode ${d.stpMode}`);
      return true;
    }
    case 'stp-port': {
      const d = findAny(net, check.device);
      const i = d?.getInterface(check.iface);
      if (!d || !i) return done(false, 'device not found'), true;
      const p = stpFor(net, check.vlan).get(d.id)?.ports.get(i.name);
      const state = i.cfg.errDisabled ? 'err-disabled' : (p?.state ?? 'down');
      done(state === check.state, `${ifLabel(d, i)} is ${state}`);
      return true;
    }
    case 'iface-config': {
      const d = findAny(net, check.device);
      const i = d?.getInterface(check.iface);
      if (!d || !i) return done(false, `${check.iface} does not exist yet`), true;
      const bad = ifaceMatches(d, i, check.is);
      done(bad === null, bad ? `${ifLabel(d, i)}: ${bad}` : `${ifLabel(d, i)} is set`);
      return true;
    }
    case 'acl': {
      const d = findDevice(net, check.device);
      if (!d) return done(false, 'device not found'), true;
      const acl = d.acls.get(check.name);
      if (!acl) return done(false, `no access list ${check.name}`), true;
      const want = check.has.trim().replace(/\s+/g, ' ').toLowerCase();
      const hit = acl.entries.some((e) => aceText(acl.kind, e).toLowerCase().includes(want));
      done(hit, hit ? `access list ${check.name} has "${check.has}"` : `access list ${check.name} has no "${check.has}" entry`);
      return true;
    }
    case 'fw-rule': {
      const d = findDevice(net, check.device);
      if (!d) return done(false, 'device not found'), true;
      const has = (['input', 'forward', 'output'] as const).some((h) => d.fw.filter[h].rules.some((r) => r.comment === check.name));
      done(has, has ? `rule "${check.name}" exists` : `no rule named "${check.name}"`);
      return true;
    }
    case 'fhrp-active': {
      const d = findDevice(net, check.device);
      if (!d) return done(false, 'device not found'), true;
      for (const i of d.interfaces) {
        for (const g of i.cfg.fhrp ?? []) {
          if (g.group !== check.group || (check.proto && g.proto !== check.proto)) continue;
          const act = fhrpActive(net, g);
          return done(act === i, act ? `${act.device.name} is active for group ${g.group}` : `group ${g.group} has no active router`), true;
        }
      }
      done(false, `${check.device} is not in group ${check.group}`);
      return true;
    }
    default:
      return false;
  }
}

// Sequential so the probe traffic reads clearly on the canvas.
export function runChecks(
  net: Network,
  objectives: Objective[],
  onResult: (r: CheckResult) => void,
  onDone: () => void,
): void {
  let i = 0;
  const next = () => {
    if (i >= objectives.length) {
      onDone();
      return;
    }
    const obj = objectives[i++];
    runOne(net, obj.check, (pass, detail) => {
      onResult({ id: obj.id, pass, detail });
      net.scheduler.schedule(150, next);
    });
  };
  next();
}
