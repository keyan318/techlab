// TechLab (Networking 2): first-hop redundancy (HSRP and VRRP).
// Routers on one LAN share a virtual gateway address. The active router (highest priority, then
// highest interface address) answers ARP for it with a virtual MAC and routes what is sent to it.
// If the active router's LAN interface goes down, the next one takes over; a recovered router
// with a higher priority only takes the role back if it has `preempt` (VRRP preempts by default).
import type { FhrpGroup, NetInterface } from './device';
import type { Network } from './network';

export function vmacFor(g: Pick<FhrpGroup, 'proto' | 'group'>): string {
  const hex = (g.group & 0xff).toString(16).padStart(2, '0');
  return g.proto === 'hsrp' ? `00:00:0c:07:ac:${hex}` : `00:00:5e:00:01:${hex}`;
}

function key(g: FhrpGroup): string {
  return `${g.proto}|${g.group}|${g.vip}`;
}

function alive(i: NetInterface): boolean {
  return i.up && i.physical.link !== null && i.ip !== null;
}

// VRRP: the router that owns the virtual address as its real address always wins (priority 255).
// Tracked interfaces that are down lower the priority, so a router that lost its uplink hands over.
export function effectivePriority(i: NetInterface, g: FhrpGroup): number {
  if (g.proto === 'vrrp' && i.ip && g.vip === i.ip.addr) return 255;
  let p = g.priority;
  for (const t of g.track ?? []) {
    const ti = i.device.getInterface(t.iface);
    if (!ti || !ti.up || !ti.physical.link) p -= t.decrement;
  }
  return Math.max(0, p);
}

function members(net: Network, g: FhrpGroup): { iface: NetInterface; g: FhrpGroup }[] {
  const out: { iface: NetInterface; g: FhrpGroup }[] = [];
  for (const d of net.devices.values()) {
    for (const i of d.interfaces) {
      for (const m of i.cfg.fhrp ?? []) {
        if (m.vip !== null && key(m) === key(g)) out.push({ iface: i, g: m });
      }
    }
  }
  return out;
}

// The active interface for a group right now (null when every member is down).
export function fhrpActive(net: Network, g: FhrpGroup): NetInterface | null {
  if (g.vip === null) return null;
  const k = key(g);
  const live = members(net, g).filter((m) => alive(m.iface));
  if (!live.length) {
    net.fhrpActive.delete(k);
    return null;
  }
  live.sort(
    (a, b) =>
      effectivePriority(b.iface, b.g) - effectivePriority(a.iface, a.g) || (b.iface.ip!.addr >>> 0) - (a.iface.ip!.addr >>> 0),
  );
  const best = live[0];
  const cur = net.fhrpActive.get(k);
  const curM = cur ? live.find((m) => m.iface === cur) : undefined;
  let winner = best.iface;
  if (curM && best.iface !== cur) {
    const takes = (best.g.proto === 'vrrp' || best.g.preempt) && effectivePriority(best.iface, best.g) > effectivePriority(curM.iface, curM.g);
    if (!takes) winner = curM.iface;
  }
  net.fhrpActive.set(k, winner);
  return winner;
}

export function fhrpState(net: Network, i: NetInterface, g: FhrpGroup): 'Active' | 'Standby' | 'Init' {
  if (!alive(i) || g.vip === null) return 'Init';
  return fhrpActive(net, g) === i ? 'Active' : 'Standby';
}

// Groups this interface is currently active for.
export function activeGroups(i: NetInterface): FhrpGroup[] {
  return (i.cfg.fhrp ?? []).filter((g) => g.vip !== null && fhrpActive(i.device.network, g) === i);
}
