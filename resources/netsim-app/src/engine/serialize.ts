import { Network } from './network';
import { createDevice } from './factory';
import { IpDevice } from './ipdevice';
import { ApDevice, SwitchDevice } from './switch';
import { SubInterface, type DeviceKind, type IfaceCfg } from './device';
import type { OspfConfig } from './ospf';
import { aclFromJSON, aclToJSON, type Acl } from './acl';
import { cidrToString, ipToString, networkOf, parseCidr, parseIp } from './ip';
import { firewallFromJSON, firewallToJSON, type FirewallJSON } from './firewall';
import type { LabDef, LabProgress } from '../labs/types';

export interface SavedIface {
  name: string;
  ip: string | null; // CIDR, e.g. "192.168.1.10/24"
  up: boolean;
}

export interface SavedRoute {
  dest: string; // CIDR ("0.0.0.0/0" for default)
  via: string | null;
  dev: string;
}

export interface SavedDevice {
  id: string;
  kind: DeviceKind;
  name: string;
  x: number;
  y: number;
  forwarding: boolean;
  ifaces: SavedIface[];
  routes: SavedRoute[];
  services?: { tcp: number[]; udp: number[] };
  firewall?: FirewallJSON;
  nameserver?: string | null;
  dhcpServer?: {
    enabled: boolean;
    rangeStart: string;
    rangeEnd: string;
    router: string | null;
    dns: string | null;
  };
  dnsServer?: { enabled: boolean; records: [string, string][] };
  httpServer?: { enabled: boolean; port: number; body: string };
  ripEnabled?: boolean;
  vlans?: Record<string, number>;
  ssid?: string;
  // TechLab (Networking 2)
  cli?: 'ios';
  hostname?: string;
  ifaceCfg?: Record<string, IfaceCfg>;
  subifs?: SavedSubif[];
  ospf?: SavedOspf;
  vlanDb?: [number, string][];
  stp?: SavedStp;
  acls?: Record<string, { kind: Acl['kind']; entries: { seq: number; text: string }[] }>;
}

export interface SavedSubif {
  name: string; // eth0.10
  parent: string; // eth0
  vlan: number | null;
  native?: boolean;
  ip: string | null;
  up: boolean;
}

export interface SavedOspf {
  pid: number;
  routerId?: string | null;
  networks: { addr: string; wildcard: string; area: number }[];
  passive?: string[];
  passiveDefault?: boolean;
  nonPassive?: string[];
  refBw?: number;
  defaultOriginate?: boolean;
}

export interface SavedStp {
  mode?: 'pvst' | 'rapid-pvst' | 'mst';
  priority?: Record<string, number>;
  disabled?: number[];
  mstInstances?: Record<string, number[]>;
  mstPriority?: Record<string, number>;
  portfastDefault?: boolean;
  bpduguardDefault?: boolean;
}

export function ospfToJSON(o: OspfConfig): SavedOspf {
  return {
    pid: o.pid,
    routerId: o.routerId !== null ? ipToString(o.routerId) : null,
    networks: o.networks.map((n) => ({ addr: ipToString(n.addr), wildcard: ipToString(n.wildcard), area: n.area })),
    passive: [...o.passive],
    passiveDefault: o.passiveDefault,
    nonPassive: [...o.nonPassive],
    refBw: o.refBw,
    defaultOriginate: o.defaultOriginate,
  };
}

export function ospfFromJSON(j: SavedOspf): OspfConfig {
  return {
    pid: j.pid,
    routerId: j.routerId ? parseIp(j.routerId) : null,
    networks: j.networks
      .map((n) => ({ addr: parseIp(n.addr), wildcard: parseIp(n.wildcard), area: n.area }))
      .filter((n): n is { addr: number; wildcard: number; area: number } => n.addr !== null && n.wildcard !== null),
    passive: [...(j.passive ?? [])],
    passiveDefault: j.passiveDefault ?? false,
    nonPassive: [...(j.nonPassive ?? [])],
    refBw: j.refBw ?? 100,
    defaultOriginate: j.defaultOriginate ?? false,
  };
}

function cfgIsEmpty(c: IfaceCfg): boolean {
  return Object.values(c).every((v) => v === undefined || v === null || (Array.isArray(v) && v.length === 0));
}

export interface SavedLink {
  id: string;
  a: [deviceId: string, ifaceName: string];
  b: [deviceId: string, ifaceName: string];
}

export interface SaveFile {
  app: 'netsim';
  version: 1;
  devices: SavedDevice[];
  links: SavedLink[];
  // Present when the file is a lab (or a student's attempt at one).
  lab?: LabDef;
  labProgress?: LabProgress;
}

export function serializeNetwork(
  net: Network,
  positions: Record<string, { x: number; y: number }>,
): SaveFile {
  const devices: SavedDevice[] = [...net.devices.values()].map((d) => {
    const pos = positions[d.id] ?? { x: 0, y: 0 };
    const ipd = d instanceof IpDevice ? d : null;
    const saved: SavedDevice = {
      id: d.id,
      kind: d.kind,
      name: d.name,
      x: pos.x,
      y: pos.y,
      forwarding: ipd?.forwarding ?? false,
      ifaces: d.ports().map((i) => ({
        name: i.name,
        ip: i.ip ? cidrToString(i.ip.addr, i.ip.prefix) : null,
        up: i.up,
      })),
      routes: (ipd?.staticRoutes ?? []).map((r) => ({
        dest: cidrToString(r.dest, r.prefix),
        via: r.via !== null ? ipToString(r.via) : null,
        dev: r.ifaceName,
      })),
    };
    if (ipd) {
      if (ipd.services.tcp.size || ipd.services.udp.size) {
        saved.services = { tcp: [...ipd.services.tcp], udp: [...ipd.services.udp] };
      }
      const fwj = firewallToJSON(ipd.fw);
      if (fwj) saved.firewall = fwj;
      if (ipd.nameserver !== null) saved.nameserver = ipToString(ipd.nameserver);
      if (ipd.dhcpServer) {
        saved.dhcpServer = {
          enabled: ipd.dhcpServer.enabled,
          rangeStart: ipToString(ipd.dhcpServer.rangeStart),
          rangeEnd: ipToString(ipd.dhcpServer.rangeEnd),
          router: ipd.dhcpServer.router !== null ? ipToString(ipd.dhcpServer.router) : null,
          dns: ipd.dhcpServer.dns !== null ? ipToString(ipd.dhcpServer.dns) : null,
        };
      }
      if (ipd.dnsServer) {
        saved.dnsServer = {
          enabled: ipd.dnsServer.enabled,
          records: [...ipd.dnsServer.records.entries()].map(([n, a]) => [n, ipToString(a)]),
        };
      }
      if (ipd.httpServer) {
        saved.httpServer = {
          enabled: ipd.httpServer.enabled,
          port: ipd.httpServer.port,
          body: ipd.httpServer.body,
        };
      }
      if (ipd.ripEnabled) saved.ripEnabled = true;
      if (ipd.ospf) saved.ospf = ospfToJSON(ipd.ospf);
      if (ipd.acls.size) saved.acls = Object.fromEntries([...ipd.acls].map(([n, a]) => [n, aclToJSON(a)]));
      const subs = ipd.interfaces.filter((i): i is SubInterface => i instanceof SubInterface);
      if (subs.length) {
        saved.subifs = subs.map((i) => ({
          name: i.name,
          parent: i.parent.name,
          vlan: i.vlan,
          ...(i.nativeVlan ? { native: true } : {}),
          ip: i.ip ? cidrToString(i.ip.addr, i.ip.prefix) : null,
          up: i.ownUp,
        }));
      }
    }
    if (d.cli) saved.cli = d.cli;
    if (d.hostname) saved.hostname = d.hostname;
    const cfg: Record<string, IfaceCfg> = {};
    for (const i of d.interfaces) if (!cfgIsEmpty(i.cfg)) cfg[i.name] = JSON.parse(JSON.stringify(i.cfg));
    if (Object.keys(cfg).length) saved.ifaceCfg = cfg;
    if (d instanceof SwitchDevice) {
      const vlans: Record<string, number> = {};
      for (const [port, vlan] of d.vlans) if (vlan !== 1) vlans[port] = vlan;
      if (Object.keys(vlans).length) saved.vlans = vlans;
      if (d instanceof ApDevice) saved.ssid = d.ssid;
      const db = [...d.vlanDb.entries()].filter(([v, n]) => !(v === 1 && n === 'default'));
      if (db.length) saved.vlanDb = db;
      const stp: SavedStp = {};
      if (d.stpMode !== 'rapid-pvst') stp.mode = d.stpMode;
      if (d.stpPriority.size) stp.priority = Object.fromEntries(d.stpPriority);
      if (d.stpDisabled.size) stp.disabled = [...d.stpDisabled];
      if (d.mstInstances.size) stp.mstInstances = Object.fromEntries(d.mstInstances);
      if (d.mstPriority.size) stp.mstPriority = Object.fromEntries(d.mstPriority);
      if (d.portfastDefault) stp.portfastDefault = true;
      if (d.bpduguardDefault) stp.bpduguardDefault = true;
      if (Object.keys(stp).length) saved.stp = stp;
    }
    return saved;
  });
  const links: SavedLink[] = [...net.links.values()].map((l) => ({
    id: l.id,
    a: [l.a.device.id, l.a.name],
    b: [l.b.device.id, l.b.name],
  }));
  return { app: 'netsim', version: 1, devices, links };
}

export function restoreNetwork(data: SaveFile): Network {
  if (data.app !== 'netsim' || data.version !== 1) throw new Error('not a NetSim v1 topology file');
  const net = new Network();
  for (const sd of data.devices) {
    const d = createDevice(net, sd.kind, { id: sd.id, name: sd.name });
    for (const si of sd.ifaces) {
      const iface = d.getInterface(si.name);
      if (!iface) continue;
      iface.up = si.up;
      if (si.ip) {
        const cidr = parseCidr(si.ip);
        if (cidr) iface.ip = cidr;
      }
    }
    if (d instanceof IpDevice) {
      d.forwarding = sd.forwarding;
      if (sd.services) {
        d.services.tcp = new Set(sd.services.tcp);
        d.services.udp = new Set(sd.services.udp);
      }
      if (sd.firewall) firewallFromJSON(d.fw, sd.firewall);
      if (sd.nameserver) d.nameserver = parseIp(sd.nameserver);
      if (sd.dhcpServer) {
        const start = parseIp(sd.dhcpServer.rangeStart);
        const end = parseIp(sd.dhcpServer.rangeEnd);
        if (start !== null && end !== null) {
          d.dhcpServer = {
            enabled: sd.dhcpServer.enabled,
            rangeStart: start,
            rangeEnd: end,
            router: sd.dhcpServer.router !== null ? parseIp(sd.dhcpServer.router) : null,
            dns: sd.dhcpServer.dns !== null ? parseIp(sd.dhcpServer.dns) : null,
            leases: new Map(),
          };
        }
      }
      if (sd.dnsServer) {
        const records = new Map<string, number>();
        for (const [name, addr] of sd.dnsServer.records) {
          const ip = parseIp(addr);
          if (ip !== null) records.set(name.toLowerCase(), ip);
        }
        d.dnsServer = { enabled: sd.dnsServer.enabled, records };
      }
      if (sd.httpServer) d.httpServer = { ...sd.httpServer };
      if (sd.ospf) d.ospf = ospfFromJSON(sd.ospf);
      for (const [n, a] of Object.entries(sd.acls ?? {})) d.acls.set(n, aclFromJSON(a));
      for (const ss of sd.subifs ?? []) {
        const parent = d.getInterface(ss.parent);
        if (!parent || d.getInterface(ss.name)) continue;
        const sub = new SubInterface(d, ss.name, parent);
        sub.vlan = ss.vlan;
        sub.nativeVlan = ss.native === true;
        sub.up = ss.up;
        if (ss.ip) sub.ip = parseCidr(ss.ip);
        d.interfaces.push(sub);
      }
      for (const sr of sd.routes) {
        const dest = parseCidr(sr.dest);
        const via = sr.via !== null ? parseIp(sr.via) : null;
        if (!dest || !d.getInterface(sr.dev)) continue;
        d.staticRoutes.push({
          dest: networkOf(dest.addr, dest.prefix),
          prefix: dest.prefix,
          via,
          ifaceName: sr.dev,
          kind: 'static',
        });
      }
    }
    if (d instanceof SwitchDevice) {
      if (sd.vlans) for (const [port, vlan] of Object.entries(sd.vlans)) d.vlans.set(port, vlan);
      if (d instanceof ApDevice && sd.ssid) d.ssid = sd.ssid;
      for (const [v, n] of sd.vlanDb ?? []) d.vlanDb.set(v, n);
      // Older files have no VLAN database: every access VLAN in use exists.
      for (const v of d.vlans.values()) if (!d.vlanDb.has(v)) d.vlanDb.set(v, `VLAN${String(v).padStart(4, '0')}`);
      const stp = sd.stp ?? {};
      if (stp.mode) d.stpMode = stp.mode;
      for (const [v, p] of Object.entries(stp.priority ?? {})) d.stpPriority.set(Number(v), p);
      for (const v of stp.disabled ?? []) d.stpDisabled.add(v);
      for (const [i, vs] of Object.entries(stp.mstInstances ?? {})) d.mstInstances.set(Number(i), vs);
      for (const [i, p] of Object.entries(stp.mstPriority ?? {})) d.mstPriority.set(Number(i), p);
      d.portfastDefault = stp.portfastDefault === true;
      d.bpduguardDefault = stp.bpduguardDefault === true;
    }
    if (sd.cli) d.cli = sd.cli;
    if (sd.hostname) d.hostname = sd.hostname;
    for (const [name, c] of Object.entries(sd.ifaceCfg ?? {})) {
      const iface = d.getInterface(name);
      if (iface) iface.cfg = JSON.parse(JSON.stringify(c));
    }
  }
  for (const sl of data.links) {
    try {
      net.connect(sl.a[0], sl.b[0], { id: sl.id, aIface: sl.a[1], bIface: sl.b[1] });
    } catch {
      // Skip malformed links rather than failing the whole import.
    }
  }
  // RIP starts after cabling so the first advertisement actually goes somewhere.
  for (const sd of data.devices) {
    if (!sd.ripEnabled) continue;
    const d = net.devices.get(sd.id);
    if (d instanceof IpDevice) d.enableRip(true);
  }
  return net;
}
