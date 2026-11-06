import { Device, NetInterface } from './device';
import type { Network } from './network';
import type { EthernetFrame } from './types';
import { BROADCAST_MAC } from './ip';
import { stpCanForward, stpEnabledFor } from './stp';

// A frame that has crossed this many switches is dropped: only possible in a loop with
// spanning tree switched off, and it keeps the simulator from spinning forever.
const MAX_SWITCH_HOPS = 32;

// L2 learning switch. Upstream NetSim had access-port VLANs only; TechLab (Networking 2) adds
// 802.1Q trunks, a VLAN database, per-VLAN spanning tree (engine/stp.ts) and port security.
// Frames never cross VLAN boundaries — two hosts in the same subnet but different VLANs
// cannot even ARP for each other.
export class SwitchDevice extends Device {
  readonly kind: 'switch' | 'ap';
  // MAC table keys are "vlan|mac" so identical MACs stay separated per VLAN.
  macTable = new Map<string, NetInterface>();
  private macTableVersion = -1;
  // Access VLAN per port (default 1).
  vlans = new Map<string, number>();
  // VLANs that exist on this switch (`vlan 10` / `name CREW`). A trunk only carries VLANs listed here.
  vlanDb = new Map<number, string>([[1, 'default']]);
  // Spanning tree: mode is shown by `show spanning-tree`; priorities are per VLAN (PVST) or per MST instance.
  stpMode: 'pvst' | 'rapid-pvst' | 'mst' = 'rapid-pvst';
  stpPriority = new Map<number, number>();
  stpDisabled = new Set<number>();
  mstInstances = new Map<number, number[]>();
  mstPriority = new Map<number, number>();
  portfastDefault = false;
  bpduguardDefault = false;

  constructor(network: Network, id: string, name: string, kind: 'switch' | 'ap' = 'switch', ports = 8) {
    super(network, id, name);
    this.kind = kind;
    this.addInterfaces('eth', ports);
  }

  get bridgeMac(): string {
    return this.interfaces[0]?.mac ?? '00:00:00:00:00:00';
  }

  vlanOf(portName: string): number {
    return this.vlans.get(portName) ?? 1;
  }

  isTrunk(port: NetInterface): boolean {
    return port.cfg.mode === 'trunk';
  }

  nativeOf(port: NetInterface): number {
    return port.cfg.native ?? 1;
  }

  trunkAllows(port: NetInterface, vlan: number): boolean {
    const allowed = port.cfg.allowed;
    return (!allowed || allowed.includes(vlan)) && this.vlanDb.has(vlan);
  }

  // Does this port carry `vlan` at all (ignoring spanning tree)?
  carries(port: NetInterface, vlan: number): boolean {
    if (!port.up || !port.link) return false;
    return this.isTrunk(port) ? this.trunkAllows(port, vlan) : this.vlanOf(port.name) === vlan;
  }

  // The STP priority this switch uses for a VLAN (MST: the priority of the instance the VLAN maps to).
  priorityFor(vlan: number): number {
    if (this.stpMode === 'mst') return this.mstPriority.get(this.mstInstanceOf(vlan)) ?? 32768;
    return this.stpPriority.get(vlan) ?? 32768;
  }

  mstInstanceOf(vlan: number): number {
    for (const [inst, vlans] of this.mstInstances) if (vlans.includes(vlan)) return inst;
    return 0;
  }

  // The VLAN a frame arriving on `port` belongs to, or null when the port must drop it.
  ingressVlan(port: NetInterface, frame: EthernetFrame): number | null {
    if (this.isTrunk(port)) {
      const vlan = frame.vlan ?? this.nativeOf(port);
      return this.trunkAllows(port, vlan) ? vlan : null;
    }
    // Access ports carry untagged frames only.
    return frame.vlan === undefined ? this.vlanOf(port.name) : null;
  }

  private egressFrame(port: NetInterface, vlan: number, frame: EthernetFrame): EthernetFrame {
    const tag = this.isTrunk(port) && vlan !== this.nativeOf(port) ? vlan : undefined;
    return { ...frame, vlan: tag, hops: (frame.hops ?? 0) + 1 };
  }

  forwards(port: NetInterface, vlan: number): boolean {
    return this.carries(port, vlan) && (!stpEnabledFor(this, vlan) || stpCanForward(this, port, vlan));
  }

  // Port security: true when the frame may enter. Learns allowed MACs, and on a violation
  // shuts the port (err-disabled), or just drops the frame (restrict counts it, protect is silent).
  private securityAdmits(port: NetInterface, frame: EthernetFrame): boolean {
    const ps = port.cfg.portSecurity;
    if (!ps || !ps.enabled || this.isTrunk(port)) return true;
    const mac = frame.srcMac;
    if (ps.macs.includes(mac)) return true;
    if (ps.macs.length < ps.max) {
      ps.macs.push(mac);
      return true;
    }
    ps.violations++;
    if (ps.violation === 'shutdown') {
      port.cfg.errDisabled = 'psecure-violation';
      port.up = false;
    }
    return false;
  }

  receiveFrame(iface: NetInterface, frame: EthernetFrame): void {
    if ((frame.hops ?? 0) >= MAX_SWITCH_HOPS) return;
    const vlan = this.ingressVlan(iface, frame);
    if (vlan === null) return;
    // A blocked (alternate / root-inconsistent) port discards everything it receives.
    if (!this.forwards(iface, vlan)) return;
    if (!this.securityAdmits(iface, frame)) return;
    // A topology change flushes the table, like a real switch's TCN, so frames never head for a dead port.
    if (this.macTableVersion !== this.network.topoVersion) {
      this.macTable.clear();
      this.macTableVersion = this.network.topoVersion;
    }
    this.macTable.set(`${vlan}|${frame.srcMac}`, iface);
    if (frame.dstMac === BROADCAST_MAC) {
      this.flood(vlan, iface, frame);
      return;
    }
    const out = this.macTable.get(`${vlan}|${frame.dstMac}`);
    if (out && this.forwards(out, vlan)) {
      if (out !== iface) out.send(this.egressFrame(out, vlan, frame));
    } else {
      this.flood(vlan, iface, frame);
    }
  }

  private flood(vlan: number, except: NetInterface, frame: EthernetFrame): void {
    for (const p of this.interfaces) {
      if (p !== except && this.forwards(p, vlan)) p.send(this.egressFrame(p, vlan, frame));
    }
  }

  // `shutdown` / `no shutdown` on an err-disabled port is how it is recovered.
  setPortUp(port: NetInterface, up: boolean): void {
    if (up) port.cfg.errDisabled = null;
    port.up = up;
    this.network.touch();
  }
}

// A wireless access point behaves as a bridge for its associated clients;
// links to hosts render as wireless (dashed) in the UI.
export class ApDevice extends SwitchDevice {
  ssid = 'netsim-wifi';

  constructor(network: Network, id: string, name: string, ports = 8) {
    super(network, id, name, 'ap', ports);
  }
}
