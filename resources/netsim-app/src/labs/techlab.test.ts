// Solvability guard for the labs TechLab ships in public/labs/*.json.
// Every lab must FAIL its objectives in its starting state and PASS once solved.
import { readFileSync } from 'node:fs';
import { describe, expect, it } from 'vitest';
import { restoreNetwork, type SaveFile } from '../engine/serialize';
import type { Network } from '../engine/network';
import { IpDevice } from '../engine/ipdevice';
import { SwitchDevice } from '../engine/switch';
import { parseIp } from '../engine/ip';
import { Shell } from '../commands/shell';
import { runChecks, type CheckResult } from './checker';
import type { Objective } from './types';

const ip = (s: string) => parseIp(s)!;
const load = (id: string): SaveFile => JSON.parse(readFileSync(`public/labs/${id}.json`, 'utf8'));

function dev(net: Network, name: string): IpDevice {
  const d = [...net.devices.values()].find((x) => x.name === name);
  expect(d, `device ${name}`).toBeTruthy();
  return d as IpDevice;
}

function grade(net: Network, objectives: Objective[]): Record<string, CheckResult> {
  const out: Record<string, CheckResult> = {};
  let done = false;
  runChecks(net, objectives, (r) => (out[r.id] = r), () => (done = true));
  net.scheduler.advanceTo(net.scheduler.now + 120_000);
  expect(done).toBe(true);
  return out;
}

function sh(net: Network, d: IpDevice, line: string): void {
  let done = false;
  new Shell(d).exec(line, { write: () => {} }, () => (done = true));
  net.scheduler.advanceTo(net.scheduler.now + 60_000);
  expect(done, line).toBe(true);
}

// Like sh(), but returns what the command printed (dhclient reports each DHCP step).
function out(net: Network, d: IpDevice, line: string): string {
  let text = '';
  let done = false;
  new Shell(d).exec(line, { write: (t: string) => (text += t) }, () => (done = true));
  net.scheduler.advanceTo(net.scheduler.now + 60_000);
  expect(done, line).toBe(true);
  return text;
}

const steps = (f: SaveFile) => f.lab!.steps.map((s) => s.objectives);
const all = (f: SaveFile) => f.lab!.steps.flatMap((s) => s.objectives);
const allPass = (r: Record<string, CheckResult>) => Object.values(r).every((x) => x.pass);

describe('every TechLab lab file is well formed', () => {
  for (const id of ['m1-l1', 'm1-l2', 'm1-l3', 'm1-l4', 'm1-l5', 'm1-l6', 'm2-l1', 'm2-l2', 'm2-l3', 'm2-l4', 'm2-l5', 'm2-l6', 'm2-l7', 'm3-l1', 'm3-l2', 'm3-l3', 'm3-l4', 'm3-l5', 'm4-l1', 'm4-l2', 'm4-l3', 'm4-l4', 'm4-l5', 'm4-l6']) {
    it(`${id}: lab id matches the file name and objective ids are unique`, () => {
      const f = load(id);
      expect(f.lab!.id).toBe(id);
      const ids = all(f).map((o) => o.id);
      expect(new Set(ids).size).toBe(ids.length);
      expect(ids.length).toBeGreaterThan(0);
    });
  }
});

describe('m1-l1 First Contact', () => {
  it('fails at the start, passes once both consoles are addressed', () => {
    const f = load('m1-l1');
    const net = restoreNetwork(f);
    expect(allPass(grade(net, all(f)))).toBe(false);
    sh(net, dev(net, 'astro'), 'ip addr add 192.168.1.10/24 dev eth0');
    sh(net, dev(net, 'rivet'), 'ip addr add 192.168.1.20/24 dev eth0');
    expect(allPass(grade(net, all(f)))).toBe(true);
  });
});

describe('m1-l2 Ship\'s Wiring', () => {
  it('fails at the start, passes once the star is addressed', () => {
    const f = load('m1-l2');
    const net = restoreNetwork(f);
    expect(allPass(grade(net, all(f)))).toBe(false);
    sh(net, dev(net, 'astro'), 'ip addr add 192.168.1.10/24 dev eth0');
    sh(net, dev(net, 'rivet'), 'ip addr add 192.168.1.20/24 dev eth0');
    sh(net, dev(net, 'volt'), 'ip addr add 192.168.1.30/24 dev eth0');
    expect(allPass(grade(net, all(f)))).toBe(true);
  });
});

describe('m1-l3 Cables & Signals', () => {
  it('fails while the link is down, passes after ip link set up', () => {
    const f = load('m1-l3');
    const net = restoreNetwork(f);
    const before = grade(net, all(f));
    expect(before['m1l3-ping-ar'].pass).toBe(false);
    expect(before['m1l3-ping-ra'].pass).toBe(false);
    sh(net, dev(net, 'rivet'), 'ip link set eth0 up');
    expect(allPass(grade(net, all(f)))).toBe(true);
  });
});

describe('m1-l4 Switches, Routers & VLANs', () => {
  it('proves the exposure first, isolates with a VLAN, then routes between zones', () => {
    const f = load('m1-l4');
    const net = restoreNetwork(f);
    const [s1, s2] = steps(f);

    // Everything shares VLAN 1, so Volt is reachable: the isolation objective must fail.
    let r = grade(net, s1);
    expect(r['m1l4-comms'].pass).toBe(true);
    expect(r['m1l4-isolated'].pass).toBe(false);

    const relay = [...net.devices.values()].find((d) => d.name === 'relay') as SwitchDevice;
    relay.vlans.set('eth2', 20);
    relay.vlans.set('eth4', 20);
    r = grade(net, s1);
    expect(allPass(r)).toBe(true);

    // Step 2 not solved yet.
    expect(allPass(grade(net, s2))).toBe(false);

    sh(net, dev(net, 'gate'), 'ip addr add 192.168.1.1/24 dev eth0');
    sh(net, dev(net, 'gate'), 'ip addr add 10.0.0.1/24 dev eth1');
    sh(net, dev(net, 'volt'), 'ip addr del 192.168.1.30/24 dev eth0');
    sh(net, dev(net, 'volt'), 'ip addr add 10.0.0.30/24 dev eth0');
    sh(net, dev(net, 'astro'), 'ip route add default via 192.168.1.1');
    sh(net, dev(net, 'volt'), 'ip route add default via 10.0.0.1');

    expect(allPass(grade(net, s2))).toBe(true);
    // The step-1 isolation result must still hold after routing is added, so a final pass stays a pass.
    expect(allPass(grade(net, s1))).toBe(true);
  });
});

describe('m1-l5 Servers & Virtualization', () => {
  it('fails with the services off, passes once web and DNS are on one server', () => {
    const f = load('m1-l5');
    const net = restoreNetwork(f);
    const [s1, s2] = steps(f);
    expect(allPass(grade(net, s1))).toBe(false);

    const server = dev(net, 'ship-server');
    server.httpServer = { enabled: true, port: 80, body: 'Codexia mission server online' };
    expect(allPass(grade(net, s1))).toBe(true);
    expect(allPass(grade(net, s2))).toBe(false);

    server.dnsServer = { enabled: true, records: new Map([['codexia.lan', ip('192.168.1.50')]]) };
    dev(net, 'astro').nameserver = ip('192.168.1.50');
    expect(allPass(grade(net, s2))).toBe(true);
  });
});

describe('m1-l6 Cloud Relay', () => {
  it('fails without return routes, passes with a default route on each end', () => {
    const f = load('m1-l6');
    const net = restoreNetwork(f);
    expect(allPass(grade(net, all(f)))).toBe(false);
    sh(net, dev(net, 'astro'), 'ip route add default via 192.168.1.1');
    // One-way routing is not enough: the reply cannot come back.
    expect(grade(net, all(f))['m1l6-http'].pass).toBe(false);
    sh(net, dev(net, 'earth-cloud'), 'ip route add default via 203.0.113.1');
    expect(allPass(grade(net, all(f)))).toBe(true);
  });
});

// ── Module 2: Networking Protocols ────────────────────────────────────────────

describe('m2-l1 Climb the Layers', () => {
  it('needs each layer fixed in order: link + address, then port, then HTTP', () => {
    const f = load('m2-l1');
    const net = restoreNetwork(f);
    const [s1, s2, s3] = steps(f);
    const server = dev(net, 'ship-server');

    expect(allPass(grade(net, s1))).toBe(false);
    sh(net, server, 'ip link set eth0 up');
    // Link up but no address yet: still not reachable.
    expect(grade(net, s1)['m2l1-ping'].pass).toBe(false);
    sh(net, server, 'ip addr add 192.168.1.50/24 dev eth0');
    expect(allPass(grade(net, s1))).toBe(true);

    expect(allPass(grade(net, s2))).toBe(false);
    server.services.tcp.add(80);
    expect(allPass(grade(net, s2))).toBe(true);

    // An open port alone does not answer HTTP.
    expect(allPass(grade(net, s3))).toBe(false);
    server.httpServer = { enabled: true, port: 80, body: 'Codexia mission server online' };
    expect(allPass(grade(net, s3))).toBe(true);
  });
});

describe('m2-l2 Standard Ports', () => {
  it('fails until 80 and 22 are open, and Telnet stays closed throughout', () => {
    const f = load('m2-l2');
    const net = restoreNetwork(f);
    const before = grade(net, all(f));
    expect(before['m2l2-80'].pass).toBe(false);
    expect(before['m2l2-22'].pass).toBe(false);
    expect(before['m2l2-23'].pass).toBe(true);

    const server = dev(net, 'ship-server');
    server.services.tcp.add(80);
    server.services.tcp.add(22);
    expect(allPass(grade(net, all(f)))).toBe(true);

    // Opening Telnet is the mistake the lab warns about.
    server.services.tcp.add(23);
    expect(grade(net, all(f))['m2l2-23'].pass).toBe(false);
  });
});

describe('m2-l3 Private Addresses', () => {
  it('fails with the typo, passes once rivet is on the right network', () => {
    const f = load('m2-l3');
    const net = restoreNetwork(f);
    expect(allPass(grade(net, all(f)))).toBe(false);
    sh(net, dev(net, 'rivet'), 'ip addr del 192.168.1.20/24 dev eth0');
    sh(net, dev(net, 'rivet'), 'ip addr add 192.168.0.20/24 dev eth0');
    expect(allPass(grade(net, all(f)))).toBe(true);
  });
});

describe('m2-l4 Network or Host?', () => {
  it('fails with the mismatched mask, passes once volt uses /24', () => {
    const f = load('m2-l4');
    const net = restoreNetwork(f);
    const before = grade(net, all(f));
    expect(before['m2l4-mask'].pass).toBe(false);
    expect(before['m2l4-ping-va'].pass).toBe(false);
    sh(net, dev(net, 'volt'), 'ip addr del 192.168.1.130/25 dev eth0');
    sh(net, dev(net, 'volt'), 'ip addr add 192.168.1.130/24 dev eth0');
    expect(allPass(grade(net, all(f)))).toBe(true);
  });
});

describe('m2-l5 Split the Block', () => {
  it('needs the subnet addresses, then the gateways', () => {
    const f = load('m2-l5');
    const net = restoreNetwork(f);
    const [s1, s2] = steps(f);
    expect(allPass(grade(net, s1))).toBe(false);
    sh(net, dev(net, 'gate'), 'ip addr add 192.168.10.1/25 dev eth0');
    sh(net, dev(net, 'gate'), 'ip addr add 192.168.10.129/25 dev eth1');
    sh(net, dev(net, 'astro'), 'ip addr add 192.168.10.10/25 dev eth0');
    sh(net, dev(net, 'rivet'), 'ip addr add 192.168.10.140/25 dev eth0');
    expect(allPass(grade(net, s1))).toBe(true);

    // Addressed but no gateways: cannot cross subnets yet.
    expect(allPass(grade(net, s2))).toBe(false);
    sh(net, dev(net, 'astro'), 'ip route add default via 192.168.10.1');
    sh(net, dev(net, 'rivet'), 'ip route add default via 192.168.10.129');
    expect(allPass(grade(net, s2))).toBe(true);
  });

  it('rejects a /24 host address inside the /25 plan', () => {
    const f = load('m2-l5');
    const net = restoreNetwork(f);
    sh(net, dev(net, 'astro'), 'ip addr add 192.168.10.10/24 dev eth0');
    expect(grade(net, steps(f)[0])['m2l5-astro'].pass).toBe(false);
  });
});

describe('m2-l6 Static Routes', () => {
  it('needs both routers taught the far network, in both directions', () => {
    const f = load('m2-l6');
    const net = restoreNetwork(f);
    const [s1, s2] = steps(f);
    expect(allPass(grade(net, s1))).toBe(false);
    sh(net, dev(net, 'astro'), 'ip route add default via 192.168.1.1');
    sh(net, dev(net, 'earth-cloud'), 'ip route add default via 172.16.0.1');
    expect(allPass(grade(net, s1))).toBe(true);

    expect(allPass(grade(net, s2))).toBe(false);
    sh(net, dev(net, 'r1'), 'ip route add 172.16.0.0/24 via 10.0.12.2');
    // Forward path only: the reply still cannot return through r2.
    expect(grade(net, s2)['m2l6-ping'].pass).toBe(false);
    sh(net, dev(net, 'r2'), 'ip route add 192.168.1.0/24 via 10.0.12.1');
    expect(allPass(grade(net, s2))).toBe(true);
  });
});

describe('m2-l7 Midterm Mission', () => {
  it('starts blank and passes only when the whole ship is built', () => {
    const f = load('m2-l7');
    const net = restoreNetwork(f);
    const [s1, s2, s3] = steps(f);
    expect(allPass(grade(net, all(f)))).toBe(false);

    sh(net, dev(net, 'gate'), 'ip addr add 192.168.20.1/25 dev eth0');
    sh(net, dev(net, 'gate'), 'ip addr add 192.168.20.129/25 dev eth1');
    sh(net, dev(net, 'gate'), 'ip addr add 203.0.113.1/24 dev eth2');
    sh(net, dev(net, 'astro'), 'ip addr add 192.168.20.10/25 dev eth0');
    sh(net, dev(net, 'rivet'), 'ip addr add 192.168.20.20/25 dev eth0');
    sh(net, dev(net, 'volt'), 'ip addr add 192.168.20.140/25 dev eth0');
    expect(allPass(grade(net, s1))).toBe(true);

    // Addressed, but nobody has a gateway: Earth is out of reach.
    expect(allPass(grade(net, s3))).toBe(false);

    sh(net, dev(net, 'astro'), 'ip route add default via 192.168.20.1');
    sh(net, dev(net, 'rivet'), 'ip route add default via 192.168.20.1');
    sh(net, dev(net, 'volt'), 'ip route add default via 192.168.20.129');
    expect(allPass(grade(net, s2))).toBe(true);
    expect(allPass(grade(net, s3))).toBe(true);
  });
});

// ── Module 3: TCP/IP Services ─────────────────────────────────────────────────

const pool = (start: string, end: string, router: string | null = null, dns: string | null = null) => ({
  enabled: true,
  rangeStart: ip(start),
  rangeEnd: ip(end),
  router: router ? ip(router) : null,
  dns: dns ? ip(dns) : null,
  leases: new Map<string, number>(),
});

describe('m3-l1 Static and Dynamic', () => {
  it('needs a static server, then a DHCP server before the crew can lease', () => {
    const f = load('m3-l1');
    const net = restoreNetwork(f);
    const [s1, s2] = steps(f);
    expect(allPass(grade(net, s1))).toBe(false);
    sh(net, dev(net, 'ship-server'), 'ip addr add 192.168.1.2/24 dev eth0');
    expect(allPass(grade(net, s1))).toBe(true);

    // Without a DHCP server the crew stays unaddressed.
    for (const n of ['astro', 'rivet', 'volt']) expect(out(net, dev(net, n), 'dhclient')).toContain('No DHCPOFFERS');
    expect(allPass(grade(net, s2))).toBe(false);

    // Same defaults the Inspector creates when you tick "serve leases".
    dev(net, 'ship-server').dhcpServer = pool('192.168.1.100', '192.168.1.150', '192.168.1.2');
    for (const n of ['astro', 'rivet', 'volt']) sh(net, dev(net, n), 'dhclient');
    expect(allPass(grade(net, s2))).toBe(true);
  });
});

describe('m3-l2 The DHCP Lease Process', () => {
  it('shows the four steps once the server is on, and hands different clients different addresses', () => {
    const f = load('m3-l2');
    const net = restoreNetwork(f);
    const [s1, s2] = steps(f);
    expect(out(net, dev(net, 'astro'), 'dhclient')).toContain('No DHCPOFFERS');
    expect(allPass(grade(net, s1))).toBe(false);

    dev(net, 'ship-server').dhcpServer!.enabled = true;
    const text = out(net, dev(net, 'astro'), 'dhclient');
    for (const step of ['DHCPDISCOVER', 'DHCPOFFER', 'DHCPREQUEST', 'DHCPACK']) expect(text).toContain(step);
    expect(allPass(grade(net, s1))).toBe(true);

    expect(allPass(grade(net, s2))).toBe(false);
    sh(net, dev(net, 'rivet'), 'dhclient');
    expect(allPass(grade(net, s2))).toBe(true);
    expect(dev(net, 'ship-server').dhcpServer!.leases.size).toBe(2);
  });
});

describe('m3-l3 Centralized DHCP', () => {
  it('serves the comms deck, then shows broadcasts stopping at the router until gate serves the far deck', () => {
    const f = load('m3-l3');
    const net = restoreNetwork(f);
    const [s1, s2] = steps(f);
    expect(allPass(grade(net, s1))).toBe(false);
    sh(net, dev(net, 'astro'), 'dhclient');
    sh(net, dev(net, 'rivet'), 'dhclient');
    expect(allPass(grade(net, s1))).toBe(true);

    // The broadcast does not cross the router.
    expect(out(net, dev(net, 'volt'), 'dhclient')).toContain('No DHCPOFFERS');
    expect(allPass(grade(net, s2))).toBe(false);

    // Enabled with the Inspector's defaults (range copied from eth0's network): wrong subnet for volt.
    dev(net, 'gate').dhcpServer = pool('192.168.1.100', '192.168.1.150', '192.168.1.1');
    sh(net, dev(net, 'volt'), 'dhclient');
    expect(grade(net, s2)['m3l3-volt'].pass).toBe(false);

    // Range and gateway set for the far deck.
    dev(net, 'gate').dhcpServer = pool('192.168.2.100', '192.168.2.150', '192.168.2.1');
    dev(net, 'volt').getInterface('eth0')!.ip = null;
    sh(net, dev(net, 'volt'), 'dhclient');
    expect(allPass(grade(net, s2))).toBe(true);
  });
});

describe('m3-l4 DHCP Server Settings', () => {
  it('leaves the third client without an address until the pool grows, then needs the options', () => {
    const f = load('m3-l4');
    const net = restoreNetwork(f);
    const [s1, s2] = steps(f);
    const results = ['astro', 'rivet', 'volt'].map((n) => out(net, dev(net, n), 'dhclient'));
    expect(results[0]).toContain('DHCPACK');
    expect(results[1]).toContain('DHCPACK');
    expect(results[2]).toContain('No DHCPOFFERS');
    expect(allPass(grade(net, s1))).toBe(false);

    dev(net, 'ship-server').dhcpServer!.rangeEnd = ip('192.168.1.150');
    sh(net, dev(net, 'volt'), 'dhclient');
    expect(allPass(grade(net, s1))).toBe(true);

    // Addresses only: no gateway and no resolver yet.
    expect(allPass(grade(net, s2))).toBe(false);
    dev(net, 'ship-server').dhcpServer!.router = ip('192.168.1.1');
    dev(net, 'ship-server').dhcpServer!.dns = ip('192.168.1.2');
    sh(net, dev(net, 'astro'), 'dhclient');
    expect(allPass(grade(net, s2))).toBe(true);
  });
});

describe('m3-l5 DNS Overview', () => {
  it('needs the DNS server on with a record before names resolve, and unknown names give NXDOMAIN', () => {
    const f = load('m3-l5');
    const net = restoreNetwork(f);
    const [s1, s2] = steps(f);
    expect(allPass(grade(net, s1))).toBe(false);
    sh(net, dev(net, 'astro'), 'dhclient');
    expect(allPass(grade(net, s1))).toBe(true);
    // DHCP told astro who its resolver is.
    expect(dev(net, 'astro').nameserver).toBe(ip('192.168.1.2'));

    expect(allPass(grade(net, s2))).toBe(false);
    const server = dev(net, 'ship-server');
    server.dnsServer = { enabled: true, records: new Map() };
    // Server on but no record yet: the name is unknown.
    expect(grade(net, s2)['m3l5-dns'].pass).toBe(false);
    server.dnsServer.records.set('portal.codexia.lan', ip('192.168.1.2'));
    expect(allPass(grade(net, s2))).toBe(true);
  });
});

// ── Module 4: Network Management and Troubleshooting ─────────────────────────

describe('m4-l1 Network Management', () => {
  it('finds the down device, then brings the printer into line with the address plan', () => {
    const f = load('m4-l1');
    const net = restoreNetwork(f);
    const [s1, s2] = steps(f);
    const before = grade(net, s1);
    expect(before['m4l1-server'].pass).toBe(true);
    expect(before['m4l1-printer'].pass).toBe(true);
    expect(before['m4l1-sensor'].pass).toBe(false);
    sh(net, dev(net, 'sensor'), 'ip link set eth0 up');
    expect(allPass(grade(net, s1))).toBe(true);

    // The printer works, but it breaks the plan (.150 is inside the DHCP pool).
    expect(grade(net, s2)['m4l1-plan'].pass).toBe(false);
    sh(net, dev(net, 'printer'), 'ip addr del 192.168.1.150/24 dev eth0');
    sh(net, dev(net, 'printer'), 'ip addr add 192.168.1.10/24 dev eth0');
    expect(allPass(grade(net, s2))).toBe(true);
  });
});

describe('m4-l2 Physical Issues', () => {
  it('keeps the healthy pair working and needs both dead links repaired', () => {
    const f = load('m4-l2');
    const net = restoreNetwork(f);
    const before = grade(net, all(f));
    expect(before['m4l2-control'].pass).toBe(true);   // the reporters are fine
    expect(before['m4l2-rivet'].pass).toBe(false);
    expect(before['m4l2-files-a'].pass).toBe(false);
    expect(before['m4l2-files-v'].pass).toBe(false);

    sh(net, dev(net, 'rivet'), 'ip link set eth0 up');
    const mid = grade(net, all(f));
    expect(mid['m4l2-rivet'].pass).toBe(true);
    expect(mid['m4l2-files-a'].pass).toBe(false);     // one fix is not enough

    sh(net, dev(net, 'file-server'), 'ip link set eth0 up');
    expect(allPass(grade(net, all(f)))).toBe(true);
  });
});

describe('m4-l3 Logical Issues', () => {
  it('has three faults stacked in order: address, gateway, resolver', () => {
    const f = load('m4-l3');
    const net = restoreNetwork(f);
    const [s1, s2, s3] = steps(f);
    const astro = dev(net, 'astro');
    expect(allPass(grade(net, s1))).toBe(false);
    sh(net, astro, 'ip addr del 192.168.10.10/24 dev eth0');
    sh(net, astro, 'ip addr add 192.168.1.10/24 dev eth0');
    expect(allPass(grade(net, s1))).toBe(true);

    expect(allPass(grade(net, s2))).toBe(false);      // no default route
    sh(net, astro, 'ip route add default via 192.168.1.1');
    expect(allPass(grade(net, s2))).toBe(true);

    // Pinging by number works, but the resolver points at the wrong address.
    expect(grade(net, s3)['m4l3-dns'].pass).toBe(false);
    astro.nameserver = ip('192.168.1.2');
    expect(allPass(grade(net, s3))).toBe(true);
  });
});

describe('m4-l4 Wireless Issues', () => {
  it('needs the AP uplink VLAN fixed, then a bigger DHCP pool for the second client', () => {
    const f = load('m4-l4');
    const net = restoreNetwork(f);
    const [s1, s2] = steps(f);
    // Associated but unreachable: the DHCP server is in another VLAN from the AP.
    expect(out(net, dev(net, 'laptop'), 'dhclient')).toContain('No DHCPOFFERS');
    expect(allPass(grade(net, s1))).toBe(false);

    const core = [...net.devices.values()].find((d) => d.name === 'core') as SwitchDevice;
    core.vlans.set('eth1', 1);
    sh(net, dev(net, 'laptop'), 'dhclient');
    expect(allPass(grade(net, s1))).toBe(true);

    // The pool holds one address: the second wireless client gets nothing.
    expect(out(net, dev(net, 'tablet'), 'dhclient')).toContain('No DHCPOFFERS');
    expect(allPass(grade(net, s2))).toBe(false);
    dev(net, 'ship-server').dhcpServer!.rangeEnd = ip('192.168.1.150');
    sh(net, dev(net, 'tablet'), 'dhclient');
    expect(allPass(grade(net, s2))).toBe(true);
  });
});

describe('m4-l5 Follow the Trail', () => {
  it('hides a return-path fault behind a forward-path fault', () => {
    const f = load('m4-l5');
    const net = restoreNetwork(f);
    expect(allPass(grade(net, all(f)))).toBe(false);

    // traceroute shows the first hop answering, then the trail going cold.
    const trace = out(net, dev(net, 'astro'), 'traceroute 172.16.0.10');
    expect(trace).toContain('192.168.1.1');
    expect(trace).not.toMatch(/\b172\.16\.0\.10\b.*ms/);

    sh(net, dev(net, 'r2'), 'ip route add 172.16.0.0/24 via 10.0.2.2');
    // Forward path fixed, but the reply still cannot get home: the second fault was hiding.
    expect(allPass(grade(net, all(f)))).toBe(false);
    sh(net, dev(net, 'r3'), 'ip route add 192.168.1.0/24 via 10.0.2.1');
    expect(allPass(grade(net, all(f)))).toBe(true);
  });
});

describe('m4-l6 Final Mission: Call Earth', () => {
  it('needs all six faults fixed, each layer only after the one below it', () => {
    const f = load('m4-l6');
    const net = restoreNetwork(f);
    const [s1, s2, s3, s4] = steps(f);
    const server = dev(net, 'ship-server');

    // Nothing works at the start.
    for (const st of [s1, s2, s3, s4]) expect(allPass(grade(net, st))).toBe(false);

    // 1. Physical: rivet's link.
    sh(net, dev(net, 'rivet'), 'ip link set eth0 up');
    expect(allPass(grade(net, s1))).toBe(true);

    // 2. DHCP: pool of one address, no router option, no dns option.
    sh(net, dev(net, 'astro'), 'dhclient');
    expect(out(net, dev(net, 'volt'), 'dhclient')).toContain('No DHCPOFFERS');
    server.dhcpServer!.rangeEnd = ip('192.168.20.150');
    sh(net, dev(net, 'volt'), 'dhclient');
    // Addresses now, but still no gateway to hand out.
    expect(allPass(grade(net, s2))).toBe(false);
    server.dhcpServer!.router = ip('192.168.20.1');
    server.dhcpServer!.dns = ip('192.168.20.2');
    sh(net, dev(net, 'astro'), 'dhclient');
    sh(net, dev(net, 'volt'), 'dhclient');
    expect(allPass(grade(net, s2))).toBe(true);

    // 3. Routing: Earth has no route back.
    expect(allPass(grade(net, s3))).toBe(false);
    sh(net, dev(net, 'earth-cloud'), 'ip route add default via 203.0.113.1');
    expect(allPass(grade(net, s3))).toBe(true);

    // 4. Services: the name is missing from DNS.
    expect(allPass(grade(net, s4))).toBe(false);
    server.dnsServer!.records.set('earth.relay', ip('203.0.113.10'));
    expect(allPass(grade(net, s4))).toBe(true);
  });
});

// ── Typing an address with its mask where only an address belongs ─────────────

describe('ping with a CIDR address', () => {
  it('fails like Linux does, but tells the student what to change', () => {
    const net = restoreNetwork(load('m1-l1'));
    sh(net, dev(net, 'astro'), 'ip addr add 192.168.1.10/24 dev eth0');
    sh(net, dev(net, 'rivet'), 'ip addr add 192.168.1.20/24 dev eth0');

    const wrong = out(net, dev(net, 'astro'), 'ping -c 2 192.168.1.20/24');
    expect(wrong).toContain('Temporary failure in name resolution');
    expect(wrong).toContain('use the address only');
    expect(wrong).toContain('without the /24');

    const right = out(net, dev(net, 'astro'), 'ping -c 2 192.168.1.20');
    expect(right).toContain('2 received');
  });
});
