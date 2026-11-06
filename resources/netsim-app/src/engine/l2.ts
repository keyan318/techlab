// TechLab (Networking 2): who shares a broadcast domain with an interface.
// Walks the cables the way a broadcast frame would: through switches (respecting access VLANs,
// 802.1Q trunks, the VLAN database and spanning tree) and into router sub-interfaces by tag.
// OSPF uses it to find neighbours and HSRP/VRRP to find their peers.
import { NetInterface, SubInterface } from './device';
import { SwitchDevice } from './switch';

function receiver(port: NetInterface, tag: number | undefined): NetInterface | null {
  const dev = port.device;
  if (tag === undefined) {
    const native = dev.interfaces.find((i) => i instanceof SubInterface && i.parent === port && i.nativeVlan);
    return native ?? port;
  }
  return (
    dev.interfaces.find((i) => i instanceof SubInterface && i.parent === port && i.vlan === tag && !i.nativeVlan) ?? null
  );
}

export function l2Peers(iface: NetInterface): NetInterface[] {
  const phys = iface.physical;
  if (!iface.up || !phys.link) return [];
  const startTag = iface instanceof SubInterface ? (iface.nativeVlan ? undefined : (iface.vlan ?? undefined)) : undefined;
  const found: NetInterface[] = [];
  const seen = new Set<string>();
  const queue: [NetInterface, number | undefined][] = [[phys, startTag]];
  while (queue.length) {
    const [out, tag] = queue.shift()!;
    if (!out.link) continue;
    const at = out.link.other(out);
    const key = `${at.device.id}|${at.name}|${tag ?? '-'}`;
    if (seen.has(key) || !at.up) continue;
    seen.add(key);
    const dev = at.device;
    if (dev instanceof SwitchDevice) {
      const vlan = dev.ingressVlan(at, { srcMac: '', dstMac: '', payload: null as never, vlan: tag });
      if (vlan === null || !dev.forwards(at, vlan)) continue;
      for (const p of dev.ports()) {
        if (p === at || !dev.forwards(p, vlan)) continue;
        queue.push([p, dev.isTrunk(p) && vlan !== dev.nativeOf(p) ? vlan : undefined]);
      }
      continue;
    }
    const r = receiver(at, tag);
    if (r && r !== iface && r.up && r.ip && !found.includes(r)) found.push(r);
  }
  return found;
}
