import type { Network } from './network';
import type { EthernetFrame } from './types';
import type { U32 } from './ip';

export type DeviceKind = 'host' | 'switch' | 'router' | 'firewall' | 'server' | 'ap';

// TechLab (Networking 2): per-interface settings the Cisco-style console (commands/ios) writes.
// Routers read the OSPF/FHRP parts, switches the switchport/STP/port-security parts.
export interface FhrpGroup {
  proto: 'hsrp' | 'vrrp';
  group: number;
  vip: number | null;
  priority: number;
  preempt: boolean;
  // `standby 1 track g0/0 20`: while that interface (on the same router) is down, priority drops by 20.
  track?: { iface: string; decrement: number }[];
}

export interface PortSecurity {
  enabled: boolean;
  max: number;
  violation: 'shutdown' | 'restrict' | 'protect';
  sticky: boolean;
  macs: string[];
  violations: number;
}

export interface IfaceCfg {
  description?: string;
  // Router side
  ospfCost?: number;
  ospfArea?: number; // `ip ospf <pid> area <a>`
  ospfPriority?: number; // DR election
  bandwidth?: number; // kbps
  fhrp?: FhrpGroup[];
  aclIn?: string; // `ip access-group <name> in`
  aclOut?: string;
  // Switch side
  mode?: 'access' | 'trunk';
  native?: number;
  allowed?: number[] | null; // null/undefined = all VLANs
  nonegotiate?: boolean;
  portfast?: boolean;
  bpduguard?: boolean;
  rootguard?: boolean;
  stpCost?: number;
  portSecurity?: PortSecurity | null;
  errDisabled?: string | null; // reason, while the port is err-disabled
}

export class NetInterface {
  private _link: Link | null = null;
  private _up = true;
  private _ip: { addr: U32; prefix: number } | null = null;
  cfg: IfaceCfg = {};

  constructor(
    public device: Device,
    public name: string,
    public mac: string,
  ) {}

  // Every change to cabling or on/off state bumps the network's topology version, so cached
  // spanning-tree and OSPF results are recomputed (see Network.touch()).
  get link(): Link | null {
    return this._link;
  }

  set link(l: Link | null) {
    this._link = l;
    this.device.network.touch();
  }

  get up(): boolean {
    return this._up;
  }

  set up(v: boolean) {
    if (this._up === v) return;
    this._up = v;
    this.device.network.touch();
  }

  get ip(): { addr: U32; prefix: number } | null {
    return this._ip;
  }

  set ip(v: { addr: U32; prefix: number } | null) {
    this._ip = v;
    this.device.network.touch();
  }

  // The physical port a frame really leaves from (itself, or a sub-interface's parent).
  get physical(): NetInterface {
    return this;
  }

  send(frame: EthernetFrame): void {
    if (this.up && this.link) this.link.transmit(this, frame);
  }
}

// A router sub-interface (GigabitEthernet0/0.10): its own address, sharing the parent's cable.
// Frames it sends carry an 802.1Q tag for its VLAN, unless it is the native VLAN (untagged).
export class SubInterface extends NetInterface {
  vlan: number | null = null;
  nativeVlan = false;
  private _ownUp = true;

  constructor(
    device: Device,
    name: string,
    public parent: NetInterface,
  ) {
    super(device, name, parent.mac);
  }

  override get link(): Link | null {
    return this.parent.link;
  }

  override set link(_l: Link | null) {
    // A sub-interface is never cabled itself.
  }

  override get up(): boolean {
    return this._ownUp && this.parent.up;
  }

  override set up(v: boolean) {
    if (this._ownUp === v) return;
    this._ownUp = v;
    this.device.network.touch();
  }

  get ownUp(): boolean {
    return this._ownUp;
  }

  override get physical(): NetInterface {
    return this.parent;
  }

  override send(frame: EthernetFrame): void {
    if (!this.up || !this.parent.link || this.vlan === null) return;
    this.parent.link.transmit(this.parent, this.nativeVlan ? { ...frame, vlan: undefined } : { ...frame, vlan: this.vlan });
  }
}

export abstract class Device {
  abstract readonly kind: DeviceKind;
  interfaces: NetInterface[] = [];
  // TechLab: 'ios' gives the device a Cisco-style console (routers and switches in Networking 2 labs).
  cli: 'ios' | null = null;
  // The IOS `hostname`. Display only: labs and checks always find devices by `name`.
  hostname: string | null = null;

  constructor(
    public network: Network,
    public id: string,
    public name: string,
  ) {}

  protected addInterfaces(prefix: string, count: number): void {
    for (let i = 0; i < count; i++) {
      this.interfaces.push(new NetInterface(this, `${prefix}${i}`, this.network.nextMac()));
    }
  }

  getInterface(name: string): NetInterface | null {
    return this.interfaces.find((i) => i.name === name) ?? null;
  }

  freeInterface(): NetInterface | null {
    return this.interfaces.find((i) => !(i instanceof SubInterface) && !i.link) ?? null;
  }

  // Physical ports only (sub-interfaces left out): what can be cabled and drawn.
  ports(): NetInterface[] {
    return this.interfaces.filter((i) => !(i instanceof SubInterface));
  }

  abstract receiveFrame(iface: NetInterface, frame: EthernetFrame): void;
}

export class Link {
  constructor(
    public network: Network,
    public id: string,
    public a: NetInterface,
    public b: NetInterface,
    public latency = 80,
  ) {
    a.link = this;
    b.link = this;
  }

  other(i: NetInterface): NetInterface {
    return i === this.a ? this.b : this.a;
  }

  transmit(from: NetInterface, frame: EthernetFrame): void {
    const to = this.other(from);
    this.network.emitWire(this, from.device, to.device, frame);
    this.network.scheduler.schedule(this.latency, () => {
      if (to.up) to.device.receiveFrame(to, frame);
    });
  }

  detach(): void {
    this.a.link = null;
    this.b.link = null;
  }
}
