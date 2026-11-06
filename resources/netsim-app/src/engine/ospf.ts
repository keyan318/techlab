// TechLab (Networking 2): OSPF, the link-state routing protocol.
//
// As with spanning tree, no hello or LSA packets travel; the result is computed from the live
// topology and cached until anything changes. What students configure and observe is real:
//  - an interface joins OSPF through a `network <addr> <wildcard> area <n>` statement (or
//    `ip ospf <pid> area <n>` on the interface);
//  - two routers become neighbours (FULL) when their interfaces share a broadcast domain and a
//    subnet, are in the same area, and neither is passive;
//  - every router runs Dijkstra over the neighbour graph; a link's cost is the outgoing
//    interface's cost (`ip ospf cost`, else reference bandwidth / interface bandwidth);
//  - equal-cost paths are all installed (up to 4) and traffic is shared between them;
//  - a router with `default-information originate` and a default route advertises 0.0.0.0/0.
import type { NetInterface } from './device';
import type { Network } from './network';
import { networkOf, type U32 } from './ip';
import { l2Peers } from './l2';
import { IpDevice, type Route } from './ipdevice';

export interface OspfNetwork {
  addr: U32;
  wildcard: U32;
  area: number;
}

export interface OspfConfig {
  pid: number;
  routerId: U32 | null;
  networks: OspfNetwork[];
  passive: string[];
  passiveDefault: boolean;
  nonPassive: string[];
  refBw: number; // Mbps
  defaultOriginate: boolean;
}

export function newOspf(pid: number): OspfConfig {
  return { pid, routerId: null, networks: [], passive: [], passiveDefault: false, nonPassive: [], refBw: 100, defaultOriginate: false };
}

export interface OspfNeighbor {
  device: IpDevice;
  iface: NetInterface; // the neighbour's interface
  local: NetInterface; // ours
  area: number;
  role: 'DR' | 'BDR' | 'DROTHER';
}

export const MAX_PATHS = 4;

export function ospfArea(d: IpDevice, i: NetInterface): number | null {
  if (!d.ospf || !i.ip) return null;
  if (i.cfg.ospfArea !== undefined) return i.cfg.ospfArea;
  for (const n of d.ospf.networks) {
    const mask = ~n.wildcard >>> 0;
    if (((i.ip.addr & mask) >>> 0) === ((n.addr & mask) >>> 0)) return n.area;
  }
  return null;
}

export function ospfPassive(d: IpDevice, i: NetInterface): boolean {
  if (!d.ospf) return false;
  return d.ospf.passiveDefault ? !d.ospf.nonPassive.includes(i.name) : d.ospf.passive.includes(i.name);
}

export function ospfCost(d: IpDevice, i: NetInterface): number {
  if (i.cfg.ospfCost !== undefined) return i.cfg.ospfCost;
  const refKbps = (d.ospf?.refBw ?? 100) * 1000;
  return Math.max(1, Math.floor(refKbps / (i.cfg.bandwidth ?? 1_000_000)));
}

export function routerId(d: IpDevice): U32 {
  if (d.ospf?.routerId != null) return d.ospf.routerId;
  let best = 0;
  for (const i of d.interfaces) if (i.ip && i.ip.addr >>> 0 > best) best = i.ip.addr >>> 0;
  return best;
}

// Interfaces running OSPF (passive ones included: their networks are advertised, no neighbours).
export function ospfIfaces(d: IpDevice): NetInterface[] {
  return d.interfaces.filter((i) => i.up && i.ip && ospfArea(d, i) !== null);
}

export function ospfNeighbors(d: IpDevice): OspfNeighbor[] {
  const out: OspfNeighbor[] = [];
  if (!d.ospf) return out;
  for (const local of ospfIfaces(d)) {
    if (ospfPassive(d, local)) continue;
    const area = ospfArea(d, local)!;
    const net = networkOf(local.ip!.addr, local.ip!.prefix);
    const onSegment: { dev: IpDevice; iface: NetInterface }[] = [{ dev: d, iface: local }];
    for (const q of l2Peers(local)) {
      const qd = q.device;
      if (!(qd instanceof IpDevice) || !qd.ospf || !q.ip) continue;
      if (q.ip.prefix !== local.ip!.prefix || networkOf(q.ip.addr, q.ip.prefix) !== net) continue;
      if (ospfArea(qd, q) !== area || ospfPassive(qd, q) || routerId(qd) === routerId(d)) continue;
      onSegment.push({ dev: qd, iface: q });
    }
    // DR / BDR: highest interface priority, then highest router ID (display only).
    const ranked = [...onSegment].sort(
      (a, b) => (b.iface.cfg.ospfPriority ?? 1) - (a.iface.cfg.ospfPriority ?? 1) || routerId(b.dev) - routerId(a.dev),
    ).filter((x) => (x.iface.cfg.ospfPriority ?? 1) > 0);
    for (const x of onSegment) {
      if (x.dev === d) continue;
      const role = ranked[0]?.dev === x.dev ? 'DR' : ranked[1]?.dev === x.dev ? 'BDR' : 'DROTHER';
      out.push({ device: x.dev, iface: x.iface, local, area, role });
    }
  }
  return out;
}

export function ospfOwnRole(d: IpDevice, local: NetInterface): 'DR' | 'BDR' | 'DROTHER' | 'P2P' {
  const nbrs = ospfNeighbors(d).filter((n) => n.local === local);
  if (!nbrs.length) return 'DR';
  const all = [{ dev: d, iface: local }, ...nbrs.map((n) => ({ dev: n.device, iface: n.iface }))]
    .filter((x) => (x.iface.cfg.ospfPriority ?? 1) > 0)
    .sort((a, b) => (b.iface.cfg.ospfPriority ?? 1) - (a.iface.cfg.ospfPriority ?? 1) || routerId(b.dev) - routerId(a.dev));
  return all[0]?.dev === d ? 'DR' : all[1]?.dev === d ? 'BDR' : 'DROTHER';
}

export type OspfRoute = Route & { ospfType: 'O' | 'O IA' | 'O*E2'; area: number | null };

const cache = new WeakMap<Network, { version: number; routes: Map<string, OspfRoute[]> }>();

function hasDefault(d: IpDevice): boolean {
  return d.staticRoutes.some((r) => r.prefix === 0);
}

function compute(net: Network): Map<string, OspfRoute[]> {
  const routers = [...net.devices.values()].filter((d): d is IpDevice => d instanceof IpDevice && d.ospf !== null);
  const adj = new Map<IpDevice, { to: IpDevice; cost: number; via: U32; ifaceName: string }[]>();
  for (const r of routers) {
    adj.set(
      r,
      ospfNeighbors(r).map((n) => ({ to: n.device, cost: ospfCost(r, n.local), via: n.iface.ip!.addr, ifaceName: n.local.name })),
    );
  }
  const result = new Map<string, OspfRoute[]>();
  for (const src of routers) {
    // Dijkstra keeping every equal-cost first hop.
    const dist = new Map<IpDevice, number>([[src, 0]]);
    const first = new Map<IpDevice, { via: U32; ifaceName: string }[]>([[src, []]]);
    const done = new Set<IpDevice>();
    for (;;) {
      let u: IpDevice | null = null;
      for (const [k, v] of dist) if (!done.has(k) && (u === null || v < dist.get(u)!)) u = k;
      if (!u) break;
      done.add(u);
      for (const e of adj.get(u) ?? []) {
        const nd = dist.get(u)! + e.cost;
        const hops = u === src ? [{ via: e.via, ifaceName: e.ifaceName }] : first.get(u)!;
        const cur = dist.get(e.to);
        if (cur === undefined || nd < cur) {
          dist.set(e.to, nd);
          first.set(e.to, [...hops]);
        } else if (nd === cur && !done.has(e.to)) {
          const merged = first.get(e.to)!;
          for (const h of hops) if (!merged.some((m) => m.via === h.via && m.ifaceName === h.ifaceName)) merged.push(h);
        }
      }
    }

    const srcAreas = new Set(ospfIfaces(src).map((i) => ospfArea(src, i)));
    const connected = new Set(
      src.interfaces.filter((i) => i.up && i.ip).map((i) => `${networkOf(i.ip!.addr, i.ip!.prefix)}/${i.ip!.prefix}`),
    );
    const best = new Map<string, { dest: U32; prefix: number; metric: number; hops: { via: U32; ifaceName: string }[]; type: OspfRoute['ospfType']; area: number | null }>();
    const offer = (dest: U32, prefix: number, metric: number, hops: { via: U32; ifaceName: string }[], type: OspfRoute['ospfType'], area: number | null) => {
      const k = `${dest}/${prefix}`;
      if (connected.has(k) || !hops.length) return;
      const cur = best.get(k);
      if (!cur || metric < cur.metric) best.set(k, { dest, prefix, metric, hops: [...hops], type, area });
      else if (metric === cur.metric) for (const h of hops) if (!cur.hops.some((m) => m.via === h.via)) cur.hops.push(h);
    };
    for (const [r, dr] of dist) {
      if (r === src) continue;
      const hops = first.get(r)!;
      for (const i of ospfIfaces(r)) {
        const area = ospfArea(r, i);
        offer(networkOf(i.ip!.addr, i.ip!.prefix), i.ip!.prefix, dr + ospfCost(r, i), hops, srcAreas.has(area) ? 'O' : 'O IA', area);
      }
      if (r.ospf!.defaultOriginate && hasDefault(r)) offer(0, 0, dr, hops, 'O*E2', null);
    }
    const routes: OspfRoute[] = [];
    for (const b of best.values()) {
      for (const h of b.hops.slice(0, MAX_PATHS)) {
        routes.push({
          dest: b.dest,
          prefix: b.prefix,
          via: h.via,
          ifaceName: h.ifaceName,
          kind: 'ospf',
          // E2 routes show the external metric (1), not the path cost.
          metric: b.type === 'O*E2' ? 1 : b.metric,
          ospfType: b.type,
          area: b.area,
        });
      }
    }
    result.set(src.id, routes);
  }
  return result;
}

export function ospfRoutesFor(d: IpDevice): OspfRoute[] {
  if (!d.ospf) return [];
  const net = d.network;
  let c = cache.get(net);
  if (!c || c.version !== net.topoVersion) {
    c = { version: net.topoVersion, routes: compute(net) };
    cache.set(net, c);
  }
  return c.routes.get(d.id) ?? [];
}
