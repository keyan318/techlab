// TechLab (Networking 2): spanning tree, worked out per VLAN like Cisco's (Rapid) PVST+.
//
// No BPDUs travel as packets; the outcome of the election is computed from the topology
// (and cached until anything changes), which is what students reason about anyway:
//   1. the switch with the lowest bridge ID (priority, then MAC) is the root bridge;
//   2. every other switch picks one root port: lowest cost to the root, then lowest
//      neighbour bridge ID, then lowest port number;
//   3. on every switch-to-switch link the side closer to the root is designated;
//      the other side, unless it is the root port, is an alternate port and blocks.
// Ports facing hosts and routers are designated (forwarding). BPDU Guard err-disables a port
// that hears another switch; Root Guard blocks a port that hears a better root
// ("root-inconsistent") so a rogue switch cannot take over.
import type { NetInterface } from './device';
import type { Network } from './network';
import { SwitchDevice } from './switch';

export type StpRole = 'root' | 'designated' | 'alternate' | 'disabled';
export type StpState = 'forwarding' | 'blocking' | 'root-inconsistent' | 'err-disabled' | 'down';

export interface StpPort {
  role: StpRole;
  state: StpState;
  cost: number;
}

export interface StpSwitch {
  priority: number;
  rootId: string; // bridge-id key of the root this switch agrees on
  rootPriority: number;
  rootMac: string;
  rootCost: number;
  rootPort: string | null;
  isRoot: boolean;
  ports: Map<string, StpPort>;
}

export const DEFAULT_PORT_COST = 4; // 1 Gbps, short path-cost method

const cache = new WeakMap<Network, { version: number; byVlan: Map<number, Map<string, StpSwitch>> }>();

export function stpEnabledFor(sw: SwitchDevice, vlan: number): boolean {
  return !sw.stpDisabled.has(vlan);
}

export function portCost(port: NetInterface): number {
  return port.cfg.stpCost ?? DEFAULT_PORT_COST;
}

function bidKey(sw: SwitchDevice, vlan: number): string {
  return `${String(sw.priorityFor(vlan)).padStart(5, '0')}|${sw.bridgeMac}`;
}

function portIndex(port: NetInterface): number {
  return port.device.interfaces.indexOf(port);
}

function cmp(a: (string | number)[], b: (string | number)[]): number {
  for (let i = 0; i < a.length; i++) {
    if (a[i] < b[i]) return -1;
    if (a[i] > b[i]) return 1;
  }
  return 0;
}

// BPDU Guard: a guarded port with another switch on the far end is err-disabled (sticky until
// `shutdown` / `no shutdown`). Runs before the election so the port simply drops out of it.
function applyBpduGuard(net: Network): void {
  for (const d of net.devices.values()) {
    if (!(d instanceof SwitchDevice)) continue;
    for (const p of d.ports()) {
      const guarded = p.cfg.bpduguard ?? (d.bpduguardDefault && p.cfg.portfast === true);
      if (!guarded || !p.up || !p.link) continue;
      const far = p.link.other(p).device;
      if (far instanceof SwitchDevice) {
        p.cfg.errDisabled = 'bpduguard';
        p.up = false;
      }
    }
  }
}

interface Edge {
  a: SwitchDevice;
  pa: NetInterface;
  b: SwitchDevice;
  pb: NetInterface;
}

function elect(net: Network, vlan: number, excluded: Set<NetInterface>): Map<string, StpSwitch> {
  const switches = [...net.devices.values()].filter(
    (d): d is SwitchDevice => d instanceof SwitchDevice && stpEnabledFor(d, vlan),
  );
  const edges: Edge[] = [];
  const seen = new Set<NetInterface>();
  for (const sw of switches) {
    for (const p of sw.ports()) {
      if (seen.has(p) || excluded.has(p) || !sw.carries(p, vlan)) continue;
      const q = p.link!.other(p);
      const far = q.device;
      if (!(far instanceof SwitchDevice) || !stpEnabledFor(far, vlan) || excluded.has(q) || !far.carries(q, vlan)) continue;
      seen.add(p);
      seen.add(q);
      edges.push({ a: sw, pa: p, b: far, pb: q });
    }
  }

  // Connected components; each elects its own root.
  const comp = new Map<SwitchDevice, number>();
  let nComp = 0;
  for (const sw of switches) {
    if (comp.has(sw)) continue;
    const stack = [sw];
    comp.set(sw, nComp);
    while (stack.length) {
      const cur = stack.pop()!;
      for (const e of edges) {
        const nb = e.a === cur ? e.b : e.b === cur ? e.a : null;
        if (nb && !comp.has(nb)) {
          comp.set(nb, nComp);
          stack.push(nb);
        }
      }
    }
    nComp++;
  }
  const rootOf = new Map<number, SwitchDevice>();
  for (const sw of switches) {
    const c = comp.get(sw)!;
    const cur = rootOf.get(c);
    if (!cur || bidKey(sw, vlan) < bidKey(cur, vlan)) rootOf.set(c, sw);
  }

  // Dijkstra: root path cost, adding the cost of the port a BPDU is received on.
  const dist = new Map<SwitchDevice, number>();
  for (const r of rootOf.values()) dist.set(r, 0);
  const done = new Set<SwitchDevice>();
  for (;;) {
    let best: SwitchDevice | null = null;
    for (const [sw, dd] of dist) if (!done.has(sw) && (best === null || dd < dist.get(best)!)) best = sw;
    if (!best) break;
    done.add(best);
    for (const e of edges) {
      const [u, v, pv] = e.a === best ? [e.a, e.b, e.pb] : e.b === best ? [e.b, e.a, e.pa] : [null, null, null];
      if (!u || !v || !pv) continue;
      const nd = dist.get(u)! + portCost(pv);
      if (!dist.has(v) || nd < dist.get(v)!) dist.set(v, nd);
    }
  }

  const out = new Map<string, StpSwitch>();
  for (const sw of switches) {
    const root = rootOf.get(comp.get(sw)!)!;
    const isRoot = root === sw;
    // Root port: best (cost via port, neighbour BID, neighbour port, own port).
    let rootPort: NetInterface | null = null;
    let rootTuple: (string | number)[] | null = null;
    if (!isRoot) {
      for (const e of edges) {
        const [p, nb, np] = e.a === sw ? [e.pa, e.b, e.pb] : e.b === sw ? [e.pb, e.a, e.pa] : [null, null, null];
        if (!p || !nb || !np || !dist.has(nb)) continue;
        const t = [dist.get(nb)! + portCost(p), bidKey(nb, vlan), portIndex(np), portIndex(p)];
        if (!rootTuple || cmp(t, rootTuple) < 0) {
          rootTuple = t;
          rootPort = p;
        }
      }
    }
    out.set(sw.id, {
      priority: sw.priorityFor(vlan),
      rootId: bidKey(root, vlan),
      rootPriority: root.priorityFor(vlan),
      rootMac: root.bridgeMac,
      rootCost: isRoot ? 0 : (dist.get(sw) ?? 0),
      rootPort: rootPort?.name ?? null,
      isRoot,
      ports: new Map(),
    });
  }

  // Roles on switch-to-switch links: the side with the better (cost, BID, port) is designated.
  const role = new Map<NetInterface, StpRole>();
  for (const e of edges) {
    const ta = [dist.get(e.a) ?? 0, bidKey(e.a, vlan), portIndex(e.pa)];
    const tb = [dist.get(e.b) ?? 0, bidKey(e.b, vlan), portIndex(e.pb)];
    const [desig, other, otherSw] = cmp(ta, tb) <= 0 ? [e.pa, e.pb, e.b] : [e.pb, e.pa, e.a];
    role.set(desig, 'designated');
    role.set(other, out.get(otherSw.id)!.rootPort === other.name ? 'root' : 'alternate');
  }

  for (const sw of switches) {
    const info = out.get(sw.id)!;
    for (const p of sw.ports()) {
      if (!p.link) continue;
      const cost = portCost(p);
      if (p.cfg.errDisabled) {
        info.ports.set(p.name, { role: 'disabled', state: 'err-disabled', cost });
      } else if (excluded.has(p)) {
        info.ports.set(p.name, { role: 'alternate', state: 'root-inconsistent', cost });
      } else if (!sw.carries(p, vlan)) {
        if (!p.up) info.ports.set(p.name, { role: 'disabled', state: 'down', cost });
      } else {
        const r = role.get(p) ?? 'designated';
        info.ports.set(p.name, { role: r, state: r === 'alternate' ? 'blocking' : 'forwarding', cost });
      }
    }
  }
  return out;
}

// The spanning tree for one VLAN: switch id -> its view of the tree.
export function stpFor(net: Network, vlan: number): Map<string, StpSwitch> {
  let c = cache.get(net);
  if (!c || c.version !== net.topoVersion) {
    applyBpduGuard(net);
    c = { version: net.topoVersion, byVlan: new Map() };
    cache.set(net, c);
  }
  const hit = c.byVlan.get(vlan);
  if (hit) return hit;

  // Root Guard: a guarded port that would become root or alternate is hearing a superior BPDU.
  // Block it and elect again without it, until nothing changes.
  const excluded = new Set<NetInterface>();
  let result = elect(net, vlan, excluded);
  for (let i = 0; i < 8; i++) {
    let changed = false;
    for (const d of net.devices.values()) {
      if (!(d instanceof SwitchDevice)) continue;
      const info = result.get(d.id);
      if (!info) continue;
      for (const p of d.ports()) {
        const pi = info.ports.get(p.name);
        if (p.cfg.rootguard && pi && (pi.role === 'root' || pi.role === 'alternate') && !excluded.has(p)) {
          excluded.add(p);
          changed = true;
        }
      }
    }
    if (!changed) break;
    result = elect(net, vlan, excluded);
  }
  c.byVlan.set(vlan, result);
  return result;
}

export function stpCanForward(sw: SwitchDevice, port: NetInterface, vlan: number): boolean {
  const info = stpFor(sw.network, vlan).get(sw.id);
  const p = info?.ports.get(port.name);
  return !p || p.state === 'forwarding';
}
