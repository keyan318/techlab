// TechLab (Networking 2): the IOS console drives the new engine features end to end:
// OSPF, 802.1Q trunks, router-on-a-stick, spanning tree (+ guards), port security, HSRP/VRRP.
import { describe, expect, it } from 'vitest';
import { Network } from '../../engine/network';
import { createDevice } from '../../engine/factory';
import { IpDevice } from '../../engine/ipdevice';
import { SwitchDevice } from '../../engine/switch';
import type { Device } from '../../engine/device';
import { parseIp } from '../../engine/ip';
import { stpFor } from '../../engine/stp';
import { ospfNeighbors } from '../../engine/ospf';
import { restoreNetwork, serializeNetwork } from '../../engine/serialize';
import { IosShell } from './ios';

const ip = (s: string) => parseIp(s)!;

function mk(net: Network, kind: 'host' | 'router' | 'switch', name: string): Device {
  const d = createDevice(net, kind, { id: name, name });
  if (kind !== 'host') d.cli = 'ios';
  return d;
}

function cli(net: Network, d: Device, lines: string[]): string {
  const sh = new IosShell(d);
  let out = '';
  for (const l of ['enable', 'configure terminal', ...lines]) {
    let done = false;
    sh.exec(l, { write: (s) => (out += s) }, () => (done = true));
    net.scheduler.advanceTo(net.scheduler.now + 30_000);
    expect(done, l).toBe(true);
  }
  return out;
}

function exec(net: Network, d: Device, line: string): string {
  const sh = new IosShell(d);
  sh.mode = 'priv';
  let out = '';
  let done = false;
  sh.exec(line, { write: (s) => (out += s) }, () => (done = true));
  net.scheduler.advanceTo(net.scheduler.now + 30_000);
  expect(done, line).toBe(true);
  return out;
}

function host(net: Network, name: string, cidr: string, gw?: string): IpDevice {
  const h = mk(net, 'host', name) as IpDevice;
  const [a, p] = cidr.split('/');
  h.interfaces[0].ip = { addr: ip(a), prefix: Number(p) };
  if (gw) h.addRoute(0, 0, ip(gw), 'eth0');
  return h;
}

function ping(net: Network, from: IpDevice, to: string): boolean {
  const id = from.allocIcmpId();
  let ok = false;
  from.onIcmp(id, (ev) => {
    if (ev.type === 'reply') ok = true;
  });
  from.sendEcho(ip(to), id, 1);
  net.scheduler.advanceTo(net.scheduler.now + 5000);
  from.offIcmp(id);
  return ok;
}

describe('IOS console basics', () => {
  it('accepts abbreviations, sets addresses and shows them', () => {
    const net = new Network();
    const r = mk(net, 'router', 'r1');
    const out = cli(net, r, ['hostname EDGE', 'int g0/0', 'ip add 10.0.0.1 255.255.255.0', 'no shut', 'end']);
    expect(out).toContain('Enter configuration commands');
    expect((r as IpDevice).interfaces[0].ip?.prefix).toBe(24);
    const br = exec(net, r, 'sh ip int br');
    expect(br).toMatch(/GigabitEthernet0\/0\s+10\.0\.0\.1/);
    const sh = new IosShell(r);
    expect(sh.prompt()).toBe('EDGE>');
  });

  it('rejects bad input with a caret, and incomplete commands', () => {
    const net = new Network();
    const r = mk(net, 'router', 'r1');
    expect(cli(net, r, ['interfaze g0/0'])).toContain("% Invalid input detected at '^' marker.");
    expect(cli(net, r, ['router'])).toContain('% Incomplete command.');
    expect(cli(net, r, ['int g0/0', 'ip address 10.0.0.0 255.255.255.0'])).toContain('Bad mask');
  });

  it('runs exec commands from config mode with do', () => {
    const net = new Network();
    const r = mk(net, 'router', 'r1');
    expect(cli(net, r, ['int g0/1', 'ip add 10.9.9.1 255.255.255.252', 'do show ip interface brief'])).toContain('10.9.9.1');
  });
});

// h1 - r1 - r2 - r3 - h3, plus a shortcut r1 - r3 for cost tests.
function ospfLine(withShortcut = false) {
  const net = new Network();
  const h1 = host(net, 'h1', '192.168.1.10/24', '192.168.1.1');
  const h3 = host(net, 'h3', '192.168.3.10/24', '192.168.3.1');
  const r1 = mk(net, 'router', 'r1') as IpDevice;
  const r2 = mk(net, 'router', 'r2') as IpDevice;
  const r3 = mk(net, 'router', 'r3') as IpDevice;
  net.connect('h1', 'r1', { aIface: 'eth0', bIface: 'eth0' });
  net.connect('r1', 'r2', { aIface: 'eth1', bIface: 'eth0' });
  net.connect('r2', 'r3', { aIface: 'eth1', bIface: 'eth0' });
  net.connect('r3', 'h3', { aIface: 'eth1', bIface: 'eth0' });
  cli(net, r1, ['int g0/0', 'ip add 192.168.1.1 255.255.255.0', 'int g0/1', 'ip add 10.0.12.1 255.255.255.252']);
  cli(net, r2, ['int g0/0', 'ip add 10.0.12.2 255.255.255.252', 'int g0/1', 'ip add 10.0.23.1 255.255.255.252']);
  cli(net, r3, ['int g0/0', 'ip add 10.0.23.2 255.255.255.252', 'int g0/1', 'ip add 192.168.3.1 255.255.255.0']);
  if (withShortcut) {
    net.connect('r1', 'r3', { aIface: 'eth2', bIface: 'eth2' });
    cli(net, r1, ['int g0/2', 'ip add 10.0.13.1 255.255.255.252']);
    cli(net, r3, ['int g0/2', 'ip add 10.0.13.2 255.255.255.252']);
  }
  return { net, h1, h3, r1, r2, r3 };
}

const OSPF_ALL = ['router ospf 1', 'network 10.0.0.0 0.0.255.255 area 0', 'network 192.168.0.0 0.0.255.255 area 0'];

describe('OSPF', () => {
  it('forms neighbours, learns routes and carries traffic end to end', () => {
    const { net, h1, r1, r2, r3 } = ospfLine();
    expect(ping(net, h1, '192.168.3.10')).toBe(false);
    for (const r of [r1, r2, r3]) cli(net, r, OSPF_ALL);
    expect(ospfNeighbors(r2).length).toBe(2);
    expect(exec(net, r1, 'show ip ospf neighbor')).toMatch(/FULL\//);
    expect(exec(net, r1, 'show ip route')).toMatch(/O\s+192\.168\.3\.0\/24 \[110\/3\] via 10\.0\.12\.2/);
    expect(ping(net, h1, '192.168.3.10')).toBe(true);
  });

  it('a mismatched area stops the adjacency', () => {
    const { net, h1, r1, r2, r3 } = ospfLine();
    cli(net, r1, OSPF_ALL);
    cli(net, r3, OSPF_ALL);
    cli(net, r2, ['router ospf 1', 'network 10.0.12.0 0.0.0.3 area 1', 'network 10.0.23.0 0.0.0.3 area 0']);
    expect(ospfNeighbors(r1).length).toBe(0);
    expect(ping(net, h1, '192.168.3.10')).toBe(false);
    cli(net, r2, ['router ospf 1', 'network 10.0.12.0 0.0.0.3 area 0']);
    expect(ospfNeighbors(r1).length).toBe(1);
    expect(ping(net, h1, '192.168.3.10')).toBe(true);
  });

  it('a passive interface advertises its network but makes no neighbours', () => {
    const { net, r1, r2 } = ospfLine();
    cli(net, r1, [...OSPF_ALL, 'passive-interface g0/1']);
    cli(net, r2, OSPF_ALL);
    expect(ospfNeighbors(r1).length).toBe(0);
    cli(net, r1, ['router ospf 1', 'no passive-interface g0/1']);
    expect(ospfNeighbors(r1).length).toBe(1);
  });

  it('cost steers the path; equal costs load-share', () => {
    const { net, r1, r2, r3 } = ospfLine(true);
    for (const r of [r1, r2, r3]) cli(net, r, OSPF_ALL);
    // Direct link wins at cost 1+1 vs 1+1+1.
    expect(r1.lookupRoute(ip('192.168.3.10'))?.via).toBe(ip('10.0.13.2'));
    cli(net, r1, ['int g0/2', 'ip ospf cost 50']);
    expect(r1.lookupRoute(ip('192.168.3.10'))?.via).toBe(ip('10.0.12.2'));
    cli(net, r1, ['int g0/2', 'ip ospf cost 2']);
    const routes = r1.routes().filter((x) => x.kind === 'ospf' && x.prefix === 24 && x.dest === ip('192.168.3.0'));
    expect(routes.length).toBe(2);
    const shown = exec(net, r1, 'show ip route ospf');
    expect(shown).toContain('[110/3] via 10.0.12.2');
    expect(shown).toContain('[110/3] via 10.0.13.2');
  });

  it('reconverges when a link goes down', () => {
    const { net, h1, r1, r2, r3 } = ospfLine(true);
    for (const r of [r1, r2, r3]) cli(net, r, OSPF_ALL);
    cli(net, r1, ['int g0/2', 'shutdown']);
    expect(r1.lookupRoute(ip('192.168.3.10'))?.via).toBe(ip('10.0.12.2'));
    expect(ping(net, h1, '192.168.3.10')).toBe(true);
  });

  it('advertises a default route with default-information originate', () => {
    const { net, r1, r2, r3 } = ospfLine();
    for (const r of [r1, r2, r3]) cli(net, r, OSPF_ALL);
    cli(net, r3, ['ip route 0.0.0.0 0.0.0.0 192.168.3.10', 'router ospf 1', 'default-information originate']);
    expect(exec(net, r1, 'show ip route')).toContain('O*E2');
    expect(exec(net, r1, 'show ip ospf database')).toContain('Type-5 AS External');
  });

  it('survives a save and restore', () => {
    const { net, r1, r2, r3 } = ospfLine();
    for (const r of [r1, r2, r3]) cli(net, r, [...OSPF_ALL]);
    cli(net, r1, ['int g0/1', 'ip ospf cost 7']);
    const copy = restoreNetwork(JSON.parse(JSON.stringify(serializeNetwork(net, {}))));
    const c1 = copy.devices.get('r1') as IpDevice;
    expect(c1.ospf?.networks.length).toBe(2);
    expect(c1.getInterface('eth1')!.cfg.ospfCost).toBe(7);
    expect(ospfNeighbors(c1).length).toBe(1);
  });
});

// h10a -- s1 == trunk == s2 -- h10b, and VLAN 20 hosts on both.
function twoSwitches() {
  const net = new Network();
  const s1 = mk(net, 'switch', 's1') as SwitchDevice;
  const s2 = mk(net, 'switch', 's2') as SwitchDevice;
  const a10 = host(net, 'a10', '192.168.10.11/24');
  const b10 = host(net, 'b10', '192.168.10.12/24');
  const a20 = host(net, 'a20', '192.168.20.11/24');
  const b20 = host(net, 'b20', '192.168.20.12/24');
  net.connect('a10', 's1', { aIface: 'eth0', bIface: 'eth0' });
  net.connect('a20', 's1', { aIface: 'eth0', bIface: 'eth1' });
  net.connect('b10', 's2', { aIface: 'eth0', bIface: 'eth0' });
  net.connect('b20', 's2', { aIface: 'eth0', bIface: 'eth1' });
  net.connect('s1', 's2', { aIface: 'eth7', bIface: 'eth7' });
  for (const s of [s1, s2]) {
    cli(net, s, ['vlan 10', 'name CREW', 'vlan 20', 'name CARGO', 'int g0/1', 'switchport mode access', 'switchport access vlan 10', 'int g0/2', 'switchport mode access', 'switchport access vlan 20']);
  }
  return { net, s1, s2, a10, b10, a20, b20 };
}

describe('VLANs and 802.1Q trunks', () => {
  it('an access link carries one VLAN; a trunk carries them all', () => {
    const { net, s1, s2, a10, b10, a20, b20 } = twoSwitches();
    // g0/8 is an access port in VLAN 1: neither VLAN crosses.
    expect(ping(net, a10, '192.168.10.12')).toBe(false);
    cli(net, s1, ['int g0/8', 'switchport mode trunk']);
    cli(net, s2, ['int g0/8', 'switchport mode trunk']);
    expect(ping(net, a10, '192.168.10.12')).toBe(true);
    expect(ping(net, a20, '192.168.20.12')).toBe(true);
    // VLANs stay separate: a VLAN 10 host cannot reach a VLAN 20 address in its own subnet trick.
    expect(ping(net, a10, '192.168.20.12')).toBe(false);
    expect(exec(net, s1, 'show interfaces trunk')).toMatch(/Gi0\/8\s+on\s+802\.1q\s+trunking\s+1/);
    expect(b10 && b20).toBeTruthy();
  });

  it('allowed-VLAN lists and the VLAN database limit what a trunk carries', () => {
    const { net, s1, s2, a10, a20 } = twoSwitches();
    cli(net, s1, ['int g0/8', 'switchport mode trunk', 'switchport trunk allowed vlan 10']);
    cli(net, s2, ['int g0/8', 'switchport mode trunk']);
    expect(ping(net, a10, '192.168.10.12')).toBe(true);
    expect(ping(net, a20, '192.168.20.12')).toBe(false);
    cli(net, s1, ['int g0/8', 'switchport trunk allowed vlan add 20']);
    expect(ping(net, a20, '192.168.20.12')).toBe(true);
    cli(net, s2, ['no vlan 20']);
    expect(ping(net, a20, '192.168.20.12')).toBe(false);
  });

  it('a native VLAN mismatch splits untagged traffic', () => {
    const { net, s1, s2, a10 } = twoSwitches();
    cli(net, s1, ['int g0/8', 'switchport mode trunk', 'switchport trunk native vlan 10']);
    cli(net, s2, ['int g0/8', 'switchport mode trunk']);
    // s1 sends VLAN 10 untagged; s2 files it under VLAN 1.
    expect(ping(net, a10, '192.168.10.12')).toBe(false);
    cli(net, s2, ['int g0/8', 'switchport trunk native vlan 10']);
    expect(ping(net, a10, '192.168.10.12')).toBe(true);
  });
});

describe('router-on-a-stick', () => {
  it('routes between VLANs over one trunk', () => {
    const { net, s1, a10, a20 } = twoSwitches();
    const r = mk(net, 'router', 'r1') as IpDevice;
    net.connect('r1', 's1', { aIface: 'eth0', bIface: 'eth6' });
    a10.addRoute(0, 0, ip('192.168.10.1'), 'eth0');
    a20.addRoute(0, 0, ip('192.168.20.1'), 'eth0');
    expect(ping(net, a10, '192.168.20.11')).toBe(false);
    cli(net, s1, ['int g0/7', 'switchport mode trunk']);
    const out = cli(net, r, ['int g0/0.10', 'ip address 192.168.10.1 255.255.255.0']);
    expect(out).toContain('IEEE 802.1Q');
    cli(net, r, ['int g0/0', 'no shut', 'int g0/0.10', 'encapsulation dot1q 10', 'ip address 192.168.10.1 255.255.255.0', 'int g0/0.20', 'encapsulation dot1Q 20', 'ip add 192.168.20.1 255.255.255.0']);
    expect(ping(net, a10, '192.168.20.11')).toBe(true);
    expect(exec(net, r, 'show ip interface brief')).toContain('GigabitEthernet0/0.20');
    const copy = restoreNetwork(JSON.parse(JSON.stringify(serializeNetwork(net, {}))));
    const ca = copy.devices.get('a10') as IpDevice;
    expect(ping(copy, ca, '192.168.20.11')).toBe(true);
  });
});

// Three switches in a triangle, one host on each.
function triangle() {
  const net = new Network();
  const sw = ['s1', 's2', 's3'].map((n) => mk(net, 'switch', n) as SwitchDevice);
  net.connect('s1', 's2', { aIface: 'eth0', bIface: 'eth0' });
  net.connect('s2', 's3', { aIface: 'eth1', bIface: 'eth1' });
  net.connect('s3', 's1', { aIface: 'eth2', bIface: 'eth2' });
  const hs = sw.map((s, i) => {
    const h = host(net, `h${i + 1}`, `10.1.1.${i + 1}/24`);
    net.connect(h.id, s.id, { aIface: 'eth0', bIface: 'eth5' });
    return h;
  });
  return { net, sw, hs };
}

describe('spanning tree', () => {
  it('blocks one port in a loop so traffic still flows and does not circle', () => {
    const { net, sw, hs } = triangle();
    expect(ping(net, hs[0], '10.1.1.3')).toBe(true);
    const tree = stpFor(net, 1);
    const blocked = [...tree.values()].flatMap((s) => [...s.ports.values()]).filter((p) => p.state === 'blocking');
    expect(blocked.length).toBe(1);
    expect(net.scheduler.pending).toBeLessThan(5);
    expect(sw.length).toBe(3);
  });

  it('the lowest priority becomes root', () => {
    const { net, sw } = triangle();
    cli(net, sw[2], ['spanning-tree vlan 1 priority 4096']);
    expect(stpFor(net, 1).get('s3')!.isRoot).toBe(true);
    expect(exec(net, sw[2], 'show spanning-tree')).toContain('This bridge is the root');
    cli(net, sw[1], ['spanning-tree vlan 1 root primary']);
    expect(stpFor(net, 1).get('s3')!.isRoot).toBe(true); // 4096 still beats 24576
    expect(cli(net, sw[0], ['spanning-tree vlan 1 priority 5000'])).toContain('increments of 4096');
  });

  it('BPDU Guard err-disables a port that hears a switch', () => {
    const { net, sw } = triangle();
    cli(net, sw[0], ['int g0/1', 'spanning-tree portfast', 'spanning-tree bpduguard enable']);
    stpFor(net, 1);
    expect(sw[0].interfaces[0].cfg.errDisabled).toBe('bpduguard');
    expect(exec(net, sw[0], 'show interfaces status')).toMatch(/Gi0\/1\s+err-disabled/);
  });

  it('Root Guard blocks a rogue switch claiming root', () => {
    const { net, sw, hs } = triangle();
    cli(net, sw[0], ['spanning-tree vlan 1 priority 4096']);
    const rogue = mk(net, 'switch', 'rogue') as SwitchDevice;
    net.connect('rogue', 's2', { aIface: 'eth0', bIface: 'eth4' });
    cli(net, rogue, ['spanning-tree vlan 1 priority 0']);
    expect(stpFor(net, 1).get('s1')!.isRoot).toBe(false);
    cli(net, sw[1], ['int g0/5', 'spanning-tree guard root']);
    expect(stpFor(net, 1).get('s1')!.isRoot).toBe(true);
    expect(exec(net, sw[1], 'show spanning-tree')).toContain('ROOT_Inc');
    expect(ping(net, hs[0], '10.1.1.2')).toBe(true);
  });

  it('MST maps VLANs to instances with their own priority', () => {
    const { net, sw } = triangle();
    for (const s of sw) cli(net, s, ['spanning-tree mode mst', 'spanning-tree mst configuration', 'instance 1 vlan 10,20']);
    cli(net, sw[1], ['spanning-tree mst 1 priority 4096']);
    expect(stpFor(net, 10).get('s2')!.isRoot).toBe(true);
    expect(stpFor(net, 1).get('s2')!.isRoot).toBe(false);
  });
});

describe('port security', () => {
  it('needs an access port, then shuts the port on an unknown MAC', () => {
    const net = new Network();
    const s = mk(net, 'switch', 's1') as SwitchDevice;
    const crew = host(net, 'crew', '10.0.0.10/24');
    host(net, 'srv', '10.0.0.1/24');
    net.connect('crew', 's1', { aIface: 'eth0', bIface: 'eth0' });
    net.connect('srv', 's1', { aIface: 'eth0', bIface: 'eth1' });
    expect(cli(net, s, ['int g0/1', 'switchport port-security'])).toContain('dynamic port');
    cli(net, s, ['int g0/1', 'switchport mode access', 'switchport port-security', 'switchport port-security mac-address sticky']);
    expect(ping(net, crew, '10.0.0.1')).toBe(true);
    // An intruder swaps in on the same port.
    net.disconnect([...net.links.values()].find((l) => l.a.device === crew || l.b.device === crew)!.id);
    const intruder = host(net, 'intruder', '10.0.0.66/24');
    net.connect('intruder', 's1', { aIface: 'eth0', bIface: 'eth0' });
    expect(ping(net, intruder, '10.0.0.1')).toBe(false);
    expect(s.interfaces[0].cfg.errDisabled).toBe('psecure-violation');
    expect(exec(net, s, 'show port-security')).toMatch(/Gi0\/1\s+1\s+1\s+1\s+Shutdown/);
  });
});

describe('HSRP and VRRP', () => {
  function lan(proto: 'standby' | 'vrrp') {
    const net = new Network();
    mk(net, 'switch', 's1');
    const pc = host(net, 'pc', '10.0.10.50/24', '10.0.10.1');
    const r1 = mk(net, 'router', 'r1') as IpDevice;
    const r2 = mk(net, 'router', 'r2') as IpDevice;
    mk(net, 'switch', 's2');
    host(net, 'far', '10.0.99.10/24', '10.0.99.1');
    net.connect('pc', 's1', { aIface: 'eth0', bIface: 'eth0' });
    net.connect('r1', 's1', { aIface: 'eth0', bIface: 'eth1' });
    net.connect('r2', 's1', { aIface: 'eth0', bIface: 'eth2' });
    net.connect('r1', 's2', { aIface: 'eth1', bIface: 'eth0' });
    net.connect('r2', 's2', { aIface: 'eth1', bIface: 'eth1' });
    net.connect('far', 's2', { aIface: 'eth0', bIface: 'eth2' });
    cli(net, r1, ['int g0/0', 'ip add 10.0.10.2 255.255.255.0', `${proto} 1 ip 10.0.10.1`, `${proto} 1 priority 110`, 'int g0/1', 'ip add 10.0.99.2 255.255.255.0', `${proto} 2 ip 10.0.99.1`]);
    cli(net, r2, ['int g0/0', 'ip add 10.0.10.3 255.255.255.0', `${proto} 1 ip 10.0.10.1`, 'int g0/1', 'ip add 10.0.99.3 255.255.255.0', `${proto} 2 ip 10.0.99.1`]);
    return { net, pc, r1, r2 };
  }

  it('HSRP: the virtual gateway survives the active router going down', () => {
    const { net, pc, r1, r2 } = lan('standby');
    expect(ping(net, pc, '10.0.99.10')).toBe(true);
    expect(exec(net, r1, 'show standby brief')).toMatch(/Gi0\/0\s+1\s+110\s+Active/);
    cli(net, r1, ['int g0/0', 'shutdown']);
    expect(ping(net, pc, '10.0.99.10')).toBe(true);
    expect(exec(net, r2, 'show standby brief')).toMatch(/Gi0\/0\s+1\s+100\s+Active/);
    // Back up without preempt: r2 keeps the role. With preempt, r1 takes it back.
    cli(net, r1, ['int g0/0', 'no shutdown']);
    expect(exec(net, r1, 'show standby brief')).toMatch(/Gi0\/0\s+1\s+110\s+Standby/);
    cli(net, r1, ['int g0/0', 'standby 1 preempt']);
    expect(exec(net, r1, 'show standby brief')).toMatch(/Gi0\/0\s+1\s+110 P Active/);
    expect(ping(net, pc, '10.0.99.10')).toBe(true);
    expect(ping(net, pc, '10.0.10.1')).toBe(true);
  });

  it('VRRP: the backup becomes master', () => {
    const { net, pc, r1, r2 } = lan('vrrp');
    expect(exec(net, r1, 'show vrrp brief')).toMatch(/Master/);
    cli(net, r1, ['int g0/0', 'shutdown']);
    expect(exec(net, r2, 'show vrrp brief')).toMatch(/Gi0\/0\s+1\s+100.*Master/);
    expect(ping(net, pc, '10.0.99.10')).toBe(true);
  });
});

describe('HSRP interface tracking', () => {
  it('a router that loses its uplink lowers its priority and the standby (with preempt) takes over', () => {
    const net = new Network();
    mk(net, 'switch', 's1');
    const pc = host(net, 'pc', '10.0.10.50/24', '10.0.10.1');
    const r1 = mk(net, 'router', 'r1') as IpDevice;
    const r2 = mk(net, 'router', 'r2') as IpDevice;
    net.connect('pc', 's1', { aIface: 'eth0', bIface: 'eth0' });
    net.connect('r1', 's1', { aIface: 'eth0', bIface: 'eth1' });
    net.connect('r2', 's1', { aIface: 'eth0', bIface: 'eth2' });
    const up = mk(net, 'router', 'up') as IpDevice;
    net.connect('r1', 'up', { aIface: 'eth1', bIface: 'eth0' });
    cli(net, r1, ['int g0/1', 'ip add 10.9.9.1 255.255.255.252', 'int g0/0', 'ip add 10.0.10.2 255.255.255.0', 'standby 1 ip 10.0.10.1', 'standby 1 priority 110', 'standby 1 track g0/1 20']);
    cli(net, r2, ['int g0/0', 'ip add 10.0.10.3 255.255.255.0', 'standby 1 ip 10.0.10.1', 'standby 1 preempt']);
    expect(exec(net, r1, 'show standby brief')).toMatch(/Gi0\/0\s+1\s+110\s+Active/);
    cli(net, up, ['int g0/0', 'shutdown']);
    net.disconnect([...net.links.values()].find((l) => l.a.device === up || l.b.device === up)!.id);
    expect(exec(net, r2, 'show standby brief')).toMatch(/Gi0\/0\s+1\s+100 P Active/);
    expect(exec(net, r1, 'show standby brief')).toMatch(/Gi0\/0\s+1\s+90\s+Standby/);
    expect(exec(net, r1, 'show running-config')).toContain('standby 1 track GigabitEthernet0/1 20');
    expect(ping(net, pc, '10.0.10.1')).toBe(true);
  });
});

describe('IOS access lists', () => {
  function edge() {
    const net = new Network();
    const inside = host(net, 'inside', '192.168.20.21/24', '192.168.20.1');
    const web = host(net, 'web', '192.168.20.80/24', '192.168.20.1');
    web.httpServer = { enabled: true, port: 80, body: 'citadel web' };
    web.services.tcp.add(22);
    const earth = host(net, 'earth', '203.0.113.10/24', '203.0.113.1');
    earth.httpServer = { enabled: true, port: 80, body: 'earth' };
    const doom = host(net, 'doom', '203.0.113.66/24', '203.0.113.1');
    const s1 = mk(net, 'switch', 'in-sw');
    const s2 = mk(net, 'switch', 'out-sw');
    const gw = mk(net, 'router', 'gw') as IpDevice;
    net.connect('inside', 'in-sw', { aIface: 'eth0', bIface: 'eth0' });
    net.connect('web', 'in-sw', { aIface: 'eth0', bIface: 'eth1' });
    net.connect('gw', 'in-sw', { aIface: 'eth0', bIface: 'eth7' });
    net.connect('earth', 'out-sw', { aIface: 'eth0', bIface: 'eth0' });
    net.connect('doom', 'out-sw', { aIface: 'eth0', bIface: 'eth1' });
    net.connect('gw', 'out-sw', { aIface: 'eth1', bIface: 'eth7' });
    cli(net, gw, ['int g0/0', 'ip add 192.168.20.1 255.255.255.0', 'int g0/1', 'ip add 203.0.113.1 255.255.255.0']);
    void s1; void s2;
    return { net, inside, web, earth, doom, gw };
  }
  function tcp(net: Network, from: IpDevice, to: string, port: number): string {
    let res = '';
    from.connectTcp(ip(to), port, 3000, (r) => (res = r));
    net.scheduler.advanceTo(net.scheduler.now + 5000);
    return res;
  }

  it('a stateless ACL blocks replies until "established" lets them back in', () => {
    const { net, inside, earth, doom, gw } = edge();
    expect(tcp(net, doom, '192.168.20.80', 22)).toBe('open');
    cli(net, gw, ['access-list 110 permit tcp any host 192.168.20.80 eq 80', 'int g0/1', 'ip access-group 110 in']);
    expect(tcp(net, doom, '192.168.20.80', 22)).toBe('timeout');
    expect(tcp(net, earth, '192.168.20.80', 80)).toBe('open');
    // The crew's own outbound connection: the reply is dropped by the implicit deny.
    expect(tcp(net, inside, '203.0.113.10', 80)).toBe('timeout');
    cli(net, gw, ['access-list 110 permit tcp any 192.168.20.0 0.0.0.255 established']);
    expect(tcp(net, inside, '203.0.113.10', 80)).toBe('open');
    expect(exec(net, gw, 'show access-lists')).toMatch(/Extended IP access list 110\n\s+10 permit tcp any host 192\.168\.20\.80 eq 80 \(\d+ match/);
  });

  it('order matters: a deny inserted before the permits with a sequence number', () => {
    const { net, earth, doom, gw } = edge();
    cli(net, gw, ['ip access-list extended OUTSIDE-IN', 'permit tcp any host 192.168.20.80 eq www', 'exit', 'int g0/1', 'ip access-group OUTSIDE-IN in']);
    expect(tcp(net, doom, '192.168.20.80', 80)).toBe('open');
    cli(net, gw, ['ip access-list extended OUTSIDE-IN', '5 deny ip host 203.0.113.66 any']);
    expect(tcp(net, doom, '192.168.20.80', 80)).toBe('timeout');
    expect(tcp(net, earth, '192.168.20.80', 80)).toBe('open');
    const run = exec(net, gw, 'show running-config');
    expect(run).toContain('ip access-list extended OUTSIDE-IN\n 5 deny ip host 203.0.113.66 any\n 10 permit tcp any host 192.168.20.80 eq 80');
    expect(run).toContain(' ip access-group OUTSIDE-IN in');
    const copy = restoreNetwork(JSON.parse(JSON.stringify(serializeNetwork(net, {}))));
    expect((copy.devices.get('gw') as IpDevice).acls.get('OUTSIDE-IN')?.entries.length).toBe(2);
  });
});
