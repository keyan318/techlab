// TechLab (Networking 2): Cisco IOS `show` output for NetSim routers and switches.
// Formats follow real IOS closely enough that what students learn here reads the same on real gear.
import { SubInterface, type Device, type NetInterface } from '../../engine/device';
import { IpDevice } from '../../engine/ipdevice';
import { SwitchDevice } from '../../engine/switch';
import { cidrToString, ipToString, networkOf } from '../../engine/ip';
import { prefixToMask } from '../windows/util';
import { stpFor, type StpPort } from '../../engine/stp';
import { effectivePriority, fhrpActive, fhrpState } from '../../engine/fhrp';
import {
  ospfArea,
  ospfCost,
  ospfIfaces,
  ospfNeighbors,
  ospfOwnRole,
  ospfPassive,
  ospfRoutesFor,
  routerId,
  type OspfRoute,
} from '../../engine/ospf';
import { ciscoMac, iosName, iosShort, vlanListText } from './names';
import { aceText } from '../../engine/acl';

const pad = (s: string | number, n: number) => String(s).padEnd(n);
const lpad = (s: string | number, n: number) => String(s).padStart(n);

export function hostnameOf(d: Device): string {
  return d.hostname ?? d.name;
}

function statusOf(i: NetInterface): [string, string] {
  if (i.cfg.errDisabled) return ['down', 'down'];
  if (!i.up) return ['administratively down', 'down'];
  if (!i.physical.link) return ['down', 'down'];
  return ['up', 'up'];
}

export function showIpIntBrief(d: Device): string {
  let s = `${pad('Interface', 23)}${pad('IP-Address', 16)}OK? Method Status                Protocol\n`;
  const list = [...d.interfaces].sort((a, b) => iosName(d, a).localeCompare(iosName(d, b), undefined, { numeric: true }));
  for (const i of list) {
    const [st, pr] = statusOf(i);
    const ip = i.ip ? ipToString(i.ip.addr) : 'unassigned';
    s += `${pad(iosName(d, i), 23)}${pad(ip, 16)}YES ${pad(i.ip ? 'manual' : 'unset', 7)}${pad(st, 22)}${pr}\n`;
  }
  return s;
}

const CODES = `Codes: L - local, C - connected, S - static, R - RIP, O - OSPF
       IA - OSPF inter area, E2 - OSPF external type 2
       * - candidate default

`;

export function showIpRoute(d: IpDevice, filter: string | null): string {
  type Line = { dest: number; prefix: number; order: number; text: string };
  const lines: Line[] = [];
  for (const i of d.interfaces) {
    if (!i.up || !i.physical.link || !i.ip) continue;
    const name = iosName(d, i);
    const net = networkOf(i.ip.addr, i.ip.prefix);
    lines.push({ dest: net, prefix: i.ip.prefix, order: 0, text: `C        ${cidrToString(net, i.ip.prefix)} is directly connected, ${name}` });
    lines.push({ dest: i.ip.addr, prefix: 32, order: 1, text: `L        ${ipToString(i.ip.addr)}/32 is directly connected, ${name}` });
  }
  for (const r of d.staticRoutes) {
    const star = r.prefix === 0 ? '*' : ' ';
    const via = r.via !== null ? `via ${ipToString(r.via)}` : `is directly connected, ${iosName(d, d.getInterface(r.ifaceName)!)}`;
    lines.push({ dest: r.dest, prefix: r.prefix, order: 2, text: `S${star}       ${cidrToString(r.dest, r.prefix)} [1/0] ${via}` });
  }
  // OSPF routes: one line per prefix, extra equal-cost paths indented below it.
  const ospf = ospfRoutesFor(d);
  const groups = new Map<string, OspfRoute[]>();
  for (const r of ospf) {
    const k = `${r.dest}/${r.prefix}`;
    groups.set(k, [...(groups.get(k) ?? []), r]);
  }
  for (const rs of groups.values()) {
    const r = rs[0];
    // A static route for the same prefix wins (administrative distance 1 < 110).
    if (d.staticRoutes.some((x) => x.dest === r.dest && x.prefix === r.prefix)) continue;
    const code = r.ospfType === 'O*E2' ? 'O*E2' : r.ospfType === 'O IA' ? 'O IA' : 'O   ';
    const head = `${pad(code, 9)}${cidrToString(r.dest, r.prefix)} [110/${r.metric}] via ${ipToString(r.via!)}, 00:00:12, ${iosName(d, d.getInterface(r.ifaceName)!)}`;
    const more = rs.slice(1).map((x) => `${' '.repeat(9 + cidrToString(r.dest, r.prefix).length)} [110/${x.metric}] via ${ipToString(x.via!)}, 00:00:12, ${iosName(d, d.getInterface(x.ifaceName)!)}`);
    lines.push({ dest: r.dest, prefix: r.prefix, order: 3, text: [head, ...more].join('\n') });
  }
  const shown = lines
    .filter((l) => !filter || (filter === 'ospf' ? l.text.startsWith('O') : filter === 'static' ? l.text.startsWith('S') : l.text.startsWith('C') || l.text.startsWith('L')))
    .sort((a, b) => (a.dest >>> 0) - (b.dest >>> 0) || a.prefix - b.prefix || a.order - b.order);
  const def = d.lookupRoute(0);
  let gw = 'Gateway of last resort is not set\n\n';
  if (def && def.prefix === 0 && def.via !== null) gw = `Gateway of last resort is ${ipToString(def.via)} to network 0.0.0.0\n\n`;
  return CODES + gw + shown.map((l) => l.text).join('\n') + (shown.length ? '\n' : '');
}

export function showOspfNeighbor(d: IpDevice): string {
  if (!d.ospf) return '';
  let s = `${pad('Neighbor ID', 16)}${pad('Pri', 6)}${pad('State', 16)}${pad('Dead Time', 12)}${pad('Address', 16)}Interface\n`;
  for (const n of ospfNeighbors(d)) {
    s += `${pad(ipToString(routerId(n.device)), 16)}${pad(n.iface.cfg.ospfPriority ?? 1, 6)}${pad(`FULL/${n.role}`, 16)}${pad('00:00:35', 12)}${pad(ipToString(n.iface.ip!.addr), 16)}${iosName(d, n.local)}\n`;
  }
  return s;
}

export function showOspfIntBrief(d: IpDevice): string {
  if (!d.ospf) return '';
  let s = `${pad('Interface', 12)}${pad('PID', 6)}${pad('Area', 16)}${pad('IP Address/Mask', 20)}${pad('Cost', 6)}${pad('State', 6)}Nbrs F/C\n`;
  const nbrs = ospfNeighbors(d);
  for (const i of ospfIfaces(d)) {
    const n = nbrs.filter((x) => x.local === i).length;
    const state = ospfPassive(d, i) ? 'DR' : n === 0 ? 'DR' : ospfOwnRole(d, i);
    s += `${pad(iosShort(d, i), 12)}${pad(d.ospf.pid, 6)}${pad(ospfArea(d, i)!, 16)}${pad(cidrToString(i.ip!.addr, i.ip!.prefix), 20)}${pad(ospfCost(d, i), 6)}${pad(state, 6)}${n}/${n}\n`;
  }
  return s;
}

export function showIpProtocols(d: IpDevice): string {
  if (!d.ospf) return '*** IP Routing is NSF aware ***\n\n';
  const o = d.ospf;
  let s = `*** IP Routing is NSF aware ***\n\nRouting Protocol is "ospf ${o.pid}"\n  Router ID ${ipToString(routerId(d))}\n`;
  const areas = new Set(ospfIfaces(d).map((i) => ospfArea(d, i)));
  s += `  Number of areas in this router is ${areas.size}. ${areas.size} normal 0 stub 0 nssa\n  Maximum path: 4\n  Routing for Networks:\n`;
  for (const n of o.networks) s += `    ${ipToString(n.addr)} ${ipToString(n.wildcard)} area ${n.area}\n`;
  const passive = ospfIfaces(d).filter((i) => ospfPassive(d, i));
  if (passive.length) {
    s += '  Passive Interface(s):\n';
    for (const i of passive) s += `    ${iosName(d, i)}\n`;
  }
  s += `  Routing Information Sources:\n    Gateway         Distance      Last Update\n`;
  for (const n of ospfNeighbors(d)) s += `    ${pad(ipToString(routerId(n.device)), 16)}${pad(110, 14)}00:00:12\n`;
  s += `  Distance: (default is 110)\n  Reference bandwidth unit is ${o.refBw} mbps\n`;
  return s;
}

function lsaAge(d: Device): number {
  return 60 + (Math.floor(d.network.now / 1000) % 1500);
}

function cksum(seed: string): string {
  let h = 0x1234;
  for (const ch of seed) h = (h * 31 + ch.charCodeAt(0)) & 0xffff;
  return `0x00${h.toString(16).toUpperCase().padStart(4, '0')}`;
}

// LSA types 1 (router), 2 (network), 3 (summary from ABRs) and 5 (external default).
export function showOspfDatabase(d: IpDevice): string {
  if (!d.ospf) return '';
  const rid = ipToString(routerId(d));
  const routers = [...d.network.devices.values()].filter((x): x is IpDevice => x instanceof IpDevice && x.ospf !== null);
  const areasOf = (r: IpDevice) => new Set(ospfIfaces(r).map((i) => ospfArea(r, i)!));
  const myAreas = [...areasOf(d)].sort((a, b) => a - b);
  const age = lsaAge(d);
  let s = `\n            OSPF Router with ID (${rid}) (Process ID ${d.ospf.pid})\n`;
  const head = `\nLink ID         ADV Router      Age         Seq#       Checksum`;
  for (const area of myAreas) {
    const inArea = routers.filter((r) => areasOf(r).has(area));
    s += `\n\t\tRouter Link States (Area ${area})\n${head} Link count\n`;
    for (const r of inArea) {
      const id = ipToString(routerId(r));
      const links = ospfIfaces(r).filter((i) => ospfArea(r, i) === area).length;
      s += `${pad(id, 16)}${pad(id, 16)}${pad(age, 12)}0x80000003 ${cksum(id + area)} ${links}\n`;
    }
    // Type 2: one per multi-access segment with a DR and at least one neighbour.
    const nets: string[] = [];
    for (const r of inArea) {
      for (const n of ospfNeighbors(r)) {
        if (n.area !== area || ospfOwnRole(r, n.local) !== 'DR') continue;
        const line = `${pad(ipToString(n.local.ip!.addr), 16)}${pad(ipToString(routerId(r)), 16)}${pad(age, 12)}0x80000002 ${cksum(ipToString(n.local.ip!.addr))}`;
        if (!nets.includes(line)) nets.push(line);
      }
    }
    if (nets.length) s += `\n\t\tNet Link States (Area ${area})\n${head}\n${nets.join('\n')}\n`;
    // Type 3: networks from other areas, injected by ABRs (routers in area 0 and this area).
    const sums: string[] = [];
    for (const abr of inArea) {
      const as = areasOf(abr);
      if (as.size < 2 || !as.has(0)) continue;
      for (const other of routers) {
        for (const i of ospfIfaces(other)) {
          if (ospfArea(other, i) === area) continue;
          const id = ipToString(networkOf(i.ip!.addr, i.ip!.prefix));
          const line = `${pad(id, 16)}${pad(ipToString(routerId(abr)), 16)}${pad(age, 12)}0x80000001 ${cksum(id + abr.id)}`;
          if (!sums.includes(line)) sums.push(line);
        }
      }
    }
    if (sums.length) s += `\n\t\tSummary Net Link States (Area ${area})\n${head}\n${sums.sort().join('\n')}\n`;
  }
  const ext = routers.filter((r) => r.ospf!.defaultOriginate && r.staticRoutes.some((x) => x.prefix === 0));
  if (ext.length) {
    s += `\n\t\tType-5 AS External Link States\n${head} Tag\n`;
    for (const r of ext) s += `${pad('0.0.0.0', 16)}${pad(ipToString(routerId(r)), 16)}${pad(age, 12)}0x80000001 ${cksum('ext' + r.id)} 1\n`;
  }
  return s;
}

export function showArp(d: IpDevice): string {
  let s = 'Protocol  Address          Age (min)  Hardware Addr   Type   Interface\n';
  for (const i of d.interfaces) {
    if (i.ip) s += `Internet  ${pad(ipToString(i.ip.addr), 17)}${pad('-', 11)}${pad(ciscoMac(i.mac), 16)}ARPA   ${iosName(d, i)}\n`;
  }
  for (const [ip, e] of d.arpTable) {
    const i = d.getInterface(e.ifaceName);
    s += `Internet  ${pad(ipToString(ip), 17)}${pad(Math.floor((d.network.now - e.at) / 60000), 11)}${pad(ciscoMac(e.mac), 16)}ARPA   ${i ? iosName(d, i) : e.ifaceName}\n`;
  }
  return s;
}

// ---------- switching ----------

export function showVlanBrief(sw: SwitchDevice): string {
  let s = `\n${pad('VLAN', 5)}${pad('Name', 33)}${pad('Status', 10)}Ports\n${'-'.repeat(4)} ${'-'.repeat(32)} ${'-'.repeat(9)} ${'-'.repeat(31)}\n`;
  const ids = [...sw.vlanDb.keys()].sort((a, b) => a - b);
  for (const v of ids) {
    const ports = sw.ports().filter((p) => !sw.isTrunk(p) && sw.vlanOf(p.name) === v).map((p) => iosShort(sw, p));
    const chunks: string[] = [];
    for (let i = 0; i < ports.length; i += 4) chunks.push(ports.slice(i, i + 4).join(', '));
    s += `${pad(v, 5)}${pad(sw.vlanDb.get(v)!, 33)}${pad('active', 10)}${chunks[0] ?? ''}\n`;
    for (const c of chunks.slice(1)) s += `${' '.repeat(48)}${c}\n`;
  }
  // Access VLANs a port uses that were never created.
  const missing = [...new Set(sw.ports().filter((p) => !sw.isTrunk(p)).map((p) => sw.vlanOf(p.name)))].filter((v) => !sw.vlanDb.has(v));
  for (const v of missing) s += `${pad(v, 5)}${pad('(not created)', 33)}inactive\n`;
  return s;
}

function stpVlans(sw: SwitchDevice): number[] {
  const out = new Set<number>();
  for (const p of sw.ports()) {
    if (!p.link) continue;
    if (sw.isTrunk(p)) {
      for (const v of sw.vlanDb.keys()) if (sw.trunkAllows(p, v)) out.add(v);
    } else {
      out.add(sw.vlanOf(p.name));
    }
  }
  return [...out].filter((v) => !sw.stpDisabled.has(v)).sort((a, b) => a - b);
}

export function showInterfacesTrunk(sw: SwitchDevice): string {
  const trunks = sw.ports().filter((p) => sw.isTrunk(p) && p.link && p.up);
  if (!trunks.length) return '';
  let s = `\n${pad('Port', 12)}${pad('Mode', 13)}${pad('Encapsulation', 15)}${pad('Status', 14)}Native vlan\n`;
  for (const p of trunks) s += `${pad(iosShort(sw, p), 12)}${pad('on', 13)}${pad('802.1q', 15)}${pad('trunking', 14)}${sw.nativeOf(p)}\n`;
  s += `\n${pad('Port', 12)}Vlans allowed on trunk\n`;
  for (const p of trunks) s += `${pad(iosShort(sw, p), 12)}${p.cfg.allowed ? vlanListText(p.cfg.allowed) : '1-4094'}\n`;
  s += `\n${pad('Port', 12)}Vlans allowed and active in management domain\n`;
  for (const p of trunks) s += `${pad(iosShort(sw, p), 12)}${vlanListText([...sw.vlanDb.keys()].filter((v) => sw.trunkAllows(p, v))) || 'none'}\n`;
  s += `\n${pad('Port', 12)}Vlans in spanning tree forwarding state and not pruned\n`;
  for (const p of trunks) {
    const fwd = [...sw.vlanDb.keys()].filter((v) => sw.forwards(p, v));
    s += `${pad(iosShort(sw, p), 12)}${vlanListText(fwd) || 'none'}\n`;
  }
  return s;
}

export function showInterfacesStatus(sw: SwitchDevice): string {
  let s = `\n${pad('Port', 10)}${pad('Name', 19)}${pad('Status', 13)}${pad('Vlan', 11)}${pad('Duplex', 7)}${pad('Speed', 7)}Type\n`;
  for (const p of sw.ports()) {
    const st = p.cfg.errDisabled ? 'err-disabled' : !p.up ? 'disabled' : p.link ? 'connected' : 'notconnect';
    const vlan = sw.isTrunk(p) ? 'trunk' : String(sw.vlanOf(p.name));
    s += `${pad(iosShort(sw, p), 10)}${pad((p.cfg.description ?? '').slice(0, 18), 19)}${pad(st, 13)}${pad(vlan, 11)}${pad('a-full', 7)}${pad('a-1000', 7)}10/100/1000BaseTX\n`;
  }
  return s;
}

export function showSwitchport(sw: SwitchDevice, p: NetInterface): string {
  const trunk = sw.isTrunk(p);
  const admin = trunk ? 'trunk' : p.cfg.mode === 'access' ? 'static access' : 'dynamic auto';
  return (
    `Name: ${iosShort(sw, p)}\nSwitchport: Enabled\nAdministrative Mode: ${admin}\nOperational Mode: ${trunk ? 'trunk' : 'static access'}\n` +
    `Administrative Trunking Encapsulation: dot1q\nNegotiation of Trunking: ${p.cfg.nonegotiate ? 'Off' : 'On'}\n` +
    `Access Mode VLAN: ${sw.vlanOf(p.name)} (${sw.vlanDb.get(sw.vlanOf(p.name)) ?? 'Inactive'})\n` +
    `Trunking Native Mode VLAN: ${sw.nativeOf(p)} (${sw.vlanDb.get(sw.nativeOf(p)) ?? 'Inactive'})\n` +
    `Trunking VLANs Enabled: ${p.cfg.allowed ? vlanListText(p.cfg.allowed) : 'ALL'}\n`
  );
}

const ROLE: Record<string, string> = { root: 'Root', designated: 'Desg', alternate: 'Altn', disabled: 'Dsbl' };
const STS = (p: StpPort) => (p.state === 'forwarding' ? 'FWD' : p.state === 'root-inconsistent' ? 'BKN*' : 'BLK');

function stpBlock(sw: SwitchDevice, vlan: number, label: string): string {
  const info = stpFor(sw.network, vlan).get(sw.id);
  if (!info) return '';
  const proto = sw.stpMode === 'rapid-pvst' ? 'rstp' : sw.stpMode === 'mst' ? 'mstp' : 'ieee';
  const ext = sw.stpMode === 'mst' ? sw.mstInstanceOf(vlan) : vlan;
  const rootPortIface = info.rootPort ? sw.getInterface(info.rootPort) : null;
  let s = `\n${label}\n  Spanning tree enabled protocol ${proto}\n`;
  s += `  Root ID    Priority    ${info.rootPriority + ext}\n             Address     ${ciscoMac(info.rootMac)}\n`;
  if (info.isRoot) s += `             This bridge is the root\n`;
  else s += `             Cost        ${info.rootCost}\n             Port        ${sw.ports().indexOf(rootPortIface!) + 1} (${iosName(sw, rootPortIface!)})\n`;
  s += `\n  Bridge ID  Priority    ${info.priority + ext}  (priority ${info.priority} sys-id-ext ${ext})\n             Address     ${ciscoMac(sw.bridgeMac)}\n\n`;
  s += `${pad('Interface', 20)}${pad('Role', 5)}${pad('Sts', 5)}${pad('Cost', 10)}${pad('Prio.Nbr', 9)}Type\n${'-'.repeat(19)} ${'-'.repeat(4)} ${'-'.repeat(3)} ${'-'.repeat(9)} ${'-'.repeat(8)} ${'-'.repeat(24)}\n`;
  for (const p of sw.ports()) {
    const pi = info.ports.get(p.name);
    if (!pi || pi.state === 'err-disabled' || pi.state === 'down') continue;
    const edge = p.cfg.portfast || (sw.portfastDefault && !sw.isTrunk(p)) ? ' Edge' : '';
    const extra = pi.state === 'root-inconsistent' ? ' *ROOT_Inc' : '';
    s += `${pad(iosShort(sw, p), 20)}${pad(ROLE[pi.role], 5)}${pad(STS(pi), 5)}${pad(pi.cost, 10)}${pad(`128.${sw.ports().indexOf(p) + 1}`, 9)}P2p${edge}${extra}\n`;
  }
  return s;
}

export function showSpanningTree(sw: SwitchDevice, vlan: number | null): string {
  if (sw.stpMode === 'mst') {
    const insts = new Map<number, number>([[0, 1]]);
    for (const [i, vs] of sw.mstInstances) if (vs.length) insts.set(i, vs[0]);
    let s = '';
    for (const [inst, rep] of [...insts].sort((a, b) => a[0] - b[0])) {
      if (vlan !== null && sw.mstInstanceOf(vlan) !== inst) continue;
      const vs = inst === 0 ? 'all others' : vlanListText(sw.mstInstances.get(inst) ?? []);
      s += stpBlock(sw, rep, `MST${String(inst).padStart(4, '0')}    vlans mapped:   ${vs}`);
    }
    return s;
  }
  const vlans = vlan !== null ? [vlan] : stpVlans(sw);
  if (vlan !== null && sw.stpDisabled.has(vlan)) return `Spanning tree instance(s) for vlan ${vlan} does not exist.\n`;
  return vlans.map((v) => stpBlock(sw, v, `VLAN${String(v).padStart(4, '0')}`)).join('');
}

export function showPortSecurity(sw: SwitchDevice): string {
  let s = `Secure Port  MaxSecureAddr  CurrentAddr  SecurityViolation  Security Action\n                (Count)       (Count)          (Count)\n${'-'.repeat(75)}\n`;
  for (const p of sw.ports()) {
    const ps = p.cfg.portSecurity;
    if (!ps?.enabled) continue;
    const act = ps.violation[0].toUpperCase() + ps.violation.slice(1);
    s += `${lpad(iosShort(sw, p), 11)}${lpad(ps.max, 15)}${lpad(ps.macs.length, 13)}${lpad(ps.violations, 19)}${lpad(act, 17)}\n`;
  }
  return s + `${'-'.repeat(75)}\n`;
}

export function showPortSecurityIf(sw: SwitchDevice, p: NetInterface): string {
  const ps = p.cfg.portSecurity;
  if (!ps?.enabled) return 'Port Security              : Disabled\n';
  const status = p.cfg.errDisabled ? 'Secure-shutdown' : p.up && p.link ? 'Secure-up' : 'Secure-down';
  return (
    `Port Security              : Enabled\nPort Status                : ${status}\n` +
    `Violation Mode             : ${ps.violation[0].toUpperCase() + ps.violation.slice(1)}\n` +
    `Maximum MAC Addresses      : ${ps.max}\nTotal MAC Addresses        : ${ps.macs.length}\n` +
    `Sticky MAC Addresses       : ${ps.sticky ? ps.macs.length : 0}\n` +
    `Last Source Address:Vlan   : ${ps.macs.length ? `${ciscoMac(ps.macs[ps.macs.length - 1])}:${sw.vlanOf(p.name)}` : '0000.0000.0000:0'}\n` +
    `Security Violation Count   : ${ps.violations}\n`
  );
}

export function showMacTable(sw: SwitchDevice): string {
  let s = `          Mac Address Table\n-------------------------------------------\n\nVlan    Mac Address       Type        Ports\n----    -----------       --------    -----\n`;
  const rows = [...sw.macTable.entries()]
    .filter(([, port]) => port.up && port.link)
    .map(([k, port]) => {
      const [vlan, mac] = k.split('|');
      const sticky = port.cfg.portSecurity?.sticky && port.cfg.portSecurity.macs.includes(mac);
      return { vlan: Number(vlan), text: `${lpad(vlan, 4)}    ${pad(ciscoMac(mac), 18)}${pad(sticky ? 'STATIC' : 'DYNAMIC', 12)}${iosShort(sw, port)}` };
    })
    .sort((a, b) => a.vlan - b.vlan);
  return s + rows.map((r) => r.text).join('\n') + (rows.length ? '\n' : '') + `Total Mac Addresses for this criterion: ${rows.length}\n`;
}

// ---------- first hop redundancy ----------

export function showStandbyBrief(d: IpDevice): string {
  let s = `                     P indicates configured to preempt.\n                     |\n${pad('Interface', 11)}${pad('Grp', 5)}${pad('Pri', 4)}P ${pad('State', 8)}${pad('Active', 16)}${pad('Standby', 16)}Virtual IP\n`;
  for (const i of d.interfaces) {
    for (const g of (i.cfg.fhrp ?? []).filter((x) => x.proto === 'hsrp')) {
      const st = fhrpState(d.network, i, g);
      const act = fhrpActive(d.network, g);
      const peers = [...d.network.devices.values()].flatMap((x) => x.interfaces.filter((y) => y !== act && (y.cfg.fhrp ?? []).some((z) => z.proto === 'hsrp' && z.group === g.group && z.vip === g.vip) && y.up && y.physical.link));
      const standby = st === 'Standby' ? 'local' : peers[0]?.ip ? ipToString(peers[0].ip.addr) : 'unknown';
      const active = st === 'Active' ? 'local' : act?.ip ? ipToString(act.ip.addr) : 'unknown';
      s += `${pad(iosShort(d, i), 11)}${pad(g.group, 5)}${pad(effectivePriority(i, g), 4)}${g.preempt ? 'P' : ' '} ${pad(st, 8)}${pad(active, 16)}${pad(standby, 16)}${g.vip !== null ? ipToString(g.vip) : 'unknown'}\n`;
    }
  }
  return s;
}

export function showVrrpBrief(d: IpDevice): string {
  let s = `${pad('Interface', 19)}${pad('Grp', 4)}${pad('Pri', 4)}${pad('Time', 6)}${pad('Own', 4)}${pad('Pre', 4)}${pad('State', 8)}${pad('Master addr', 16)}Group addr\n`;
  for (const i of d.interfaces) {
    for (const g of (i.cfg.fhrp ?? []).filter((x) => x.proto === 'vrrp')) {
      const st = fhrpState(d.network, i, g);
      const act = fhrpActive(d.network, g);
      const own = i.ip && g.vip === i.ip.addr ? 'Y' : ' ';
      s += `${pad(iosShort(d, i), 19)}${pad(g.group, 4)}${pad(effectivePriority(i, g), 4)}${pad(3570, 6)}${pad(own, 4)}${pad('Y', 4)}${pad(st === 'Active' ? 'Master' : st === 'Standby' ? 'Backup' : 'Init', 8)}${pad(act?.ip ? ipToString(act.ip.addr) : 'unknown', 16)}${g.vip !== null ? ipToString(g.vip) : 'unknown'}\n`;
    }
  }
  return s;
}

// ---------- neighbours / config ----------

export function showCdpNeighbors(d: Device): string {
  let s = `Capability Codes: R - Router, S - Switch, H - Host\n\n${pad('Device ID', 17)}${pad('Local Intrfce', 18)}${pad('Holdtme', 11)}${pad('Capability', 12)}${pad('Platform', 10)}Port ID\n`;
  for (const p of d.ports()) {
    if (!p.up || !p.link) continue;
    const q = p.link.other(p);
    const far = q.device;
    if (far.cli !== 'ios' || !q.up) continue;
    const cap = far instanceof SwitchDevice ? 'S I' : 'R';
    const plat = far instanceof SwitchDevice ? 'WS-C2960' : 'ISR4331';
    s += `${pad(hostnameOf(far), 17)}${pad(iosShort(d, p).replace('Gi', 'Gig '), 18)}${pad(150, 11)}${pad(cap, 12)}${pad(plat, 10)}${iosShort(far, q).replace('Gi', 'Gig ')}\n`;
  }
  return s;
}

export function showAccessLists(d: IpDevice, name: string | null): string {
  let s = '';
  for (const [n, acl] of d.acls) {
    if (name !== null && n !== name) continue;
    const label = /^\d+$/.test(n) ? `${acl.kind === 'standard' ? 'Standard' : 'Extended'} IP access list ${n}` : `${acl.kind === 'standard' ? 'Standard' : 'Extended'} IP access list ${n}`;
    s += `${label}\n`;
    for (const e of acl.entries) s += `    ${e.seq} ${aceText(acl.kind, e)}${e.matches ? ` (${e.matches} match${e.matches === 1 ? '' : 'es'})` : ''}\n`;
  }
  return s;
}

export function showIpInterface(d: IpDevice, i: NetInterface): string {
  const [st, pr] = statusOf(i);
  return (
    `${iosName(d, i)} is ${st}, line protocol is ${pr}\n` +
    (i.ip ? `  Internet address is ${cidrToString(i.ip.addr, i.ip.prefix)}\n` : '  Internet protocol processing disabled\n') +
    `  Outgoing access list is ${i.cfg.aclOut ?? 'not set'}\n  Inbound  access list is ${i.cfg.aclIn ?? 'not set'}\n`
  );
}

export function runningConfig(d: Device): string {
  const L: string[] = ['Building configuration...', '', 'Current configuration:', '!', `hostname ${hostnameOf(d)}`, '!'];
  if (d instanceof SwitchDevice) {
    L.push(`spanning-tree mode ${d.stpMode}`);
    if (d.portfastDefault) L.push('spanning-tree portfast default');
    if (d.bpduguardDefault) L.push('spanning-tree portfast bpduguard default');
    for (const [v, p] of d.stpPriority) L.push(`spanning-tree vlan ${v} priority ${p}`);
    for (const v of d.stpDisabled) L.push(`no spanning-tree vlan ${v}`);
    if (d.mstInstances.size) {
      L.push('spanning-tree mst configuration');
      for (const [i, vs] of d.mstInstances) L.push(` instance ${i} vlan ${vlanListText(vs)}`);
    }
    for (const [i, p] of d.mstPriority) L.push(`spanning-tree mst ${i} priority ${p}`);
    L.push('!');
    for (const [v, n] of [...d.vlanDb].sort((a, b) => a[0] - b[0])) if (v !== 1) L.push(`vlan ${v}`, ` name ${n}`, '!');
  }
  for (const i of [...d.interfaces].sort((a, b) => iosName(d, a).localeCompare(iosName(d, b), undefined, { numeric: true }))) {
    L.push(`interface ${iosName(d, i)}`);
    const c = i.cfg;
    if (c.description) L.push(` description ${c.description}`);
    if (i instanceof SubInterface && i.vlan !== null) L.push(` encapsulation dot1Q ${i.vlan}${i.nativeVlan ? ' native' : ''}`);
    if (d instanceof SwitchDevice) {
      if (d.isTrunk(i)) {
        L.push(' switchport mode trunk');
        if (c.native && c.native !== 1) L.push(` switchport trunk native vlan ${c.native}`);
        if (c.allowed) L.push(` switchport trunk allowed vlan ${vlanListText(c.allowed)}`);
      } else {
        if (d.vlanOf(i.name) !== 1) L.push(` switchport access vlan ${d.vlanOf(i.name)}`);
        if (c.mode === 'access') L.push(' switchport mode access');
      }
      if (c.nonegotiate) L.push(' switchport nonegotiate');
      if (c.portSecurity) {
        const ps = c.portSecurity;
        if (ps.enabled) L.push(' switchport port-security');
        if (ps.max !== 1) L.push(` switchport port-security maximum ${ps.max}`);
        if (ps.violation !== 'shutdown') L.push(` switchport port-security violation ${ps.violation}`);
        if (ps.sticky) L.push(' switchport port-security mac-address sticky');
        for (const m of ps.sticky ? ps.macs : []) L.push(` switchport port-security mac-address sticky ${ciscoMac(m)}`);
      }
      if (c.portfast) L.push(' spanning-tree portfast');
      if (c.bpduguard) L.push(' spanning-tree bpduguard enable');
      if (c.rootguard) L.push(' spanning-tree guard root');
      if (c.stpCost !== undefined) L.push(` spanning-tree cost ${c.stpCost}`);
    } else {
      if (i.ip) L.push(` ip address ${ipToString(i.ip.addr)} ${prefixToMask(i.ip.prefix)}`);
      else L.push(' no ip address');
      if (c.bandwidth !== undefined) L.push(` bandwidth ${c.bandwidth}`);
      if (c.ospfCost !== undefined) L.push(` ip ospf cost ${c.ospfCost}`);
      if (c.ospfPriority !== undefined) L.push(` ip ospf priority ${c.ospfPriority}`);
      if (c.ospfArea !== undefined && d instanceof IpDevice && d.ospf) L.push(` ip ospf ${d.ospf.pid} area ${c.ospfArea}`);
      if (c.aclIn) L.push(` ip access-group ${c.aclIn} in`);
      if (c.aclOut) L.push(` ip access-group ${c.aclOut} out`);
      for (const g of c.fhrp ?? []) {
        const k = g.proto === 'hsrp' ? 'standby' : 'vrrp';
        if (g.vip !== null) L.push(` ${k} ${g.group} ip ${ipToString(g.vip)}`);
        if (g.priority !== 100) L.push(` ${k} ${g.group} priority ${g.priority}`);
        if (g.proto === 'hsrp' && g.preempt) L.push(` ${k} ${g.group} preempt`);
        for (const t of g.track ?? []) L.push(` ${k} ${g.group} track ${iosName(d, d.getInterface(t.iface) ?? d.interfaces[0])} ${t.decrement}`);
      }
    }
    if (!i.up && !c.errDisabled) L.push(' shutdown');
    L.push('!');
  }
  if (d instanceof IpDevice && d.ospf) {
    const o = d.ospf;
    L.push(`router ospf ${o.pid}`);
    if (o.routerId !== null) L.push(` router-id ${ipToString(o.routerId)}`);
    if (o.refBw !== 100) L.push(` auto-cost reference-bandwidth ${o.refBw}`);
    if (o.passiveDefault) L.push(' passive-interface default');
    for (const n of o.passiveDefault ? o.nonPassive : o.passive) {
      L.push(` ${o.passiveDefault ? 'no ' : ''}passive-interface ${iosName(d, d.getInterface(n) ?? d.interfaces[0])}`);
    }
    for (const n of o.networks) L.push(` network ${ipToString(n.addr)} ${ipToString(n.wildcard)} area ${n.area}`);
    if (o.defaultOriginate) L.push(' default-information originate');
    L.push('!');
  }
  if (d instanceof IpDevice) {
    for (const [n, acl] of d.acls) {
      if (/^\d+$/.test(n)) for (const e of acl.entries) L.push(`access-list ${n} ${aceText(acl.kind, e)}`);
      else {
        L.push(`ip access-list ${acl.kind} ${n}`);
        for (const e of acl.entries) L.push(` ${e.seq} ${aceText(acl.kind, e)}`);
      }
    }
    if (d.acls.size) L.push('!');
    for (const r of d.staticRoutes) {
      const via = r.via !== null ? ipToString(r.via) : iosName(d, d.getInterface(r.ifaceName)!);
      L.push(`ip route ${ipToString(r.dest)} ${prefixToMask(r.prefix)} ${via}`);
    }
  }
  L.push('!', 'end');
  return L.join('\n') + '\n';
}
