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

const steps = (f: SaveFile) => f.lab!.steps.map((s) => s.objectives);
const all = (f: SaveFile) => f.lab!.steps.flatMap((s) => s.objectives);
const allPass = (r: Record<string, CheckResult>) => Object.values(r).every((x) => x.pass);

describe('every TechLab lab file is well formed', () => {
  for (const id of ['m1-l1', 'm1-l2', 'm1-l3', 'm1-l4', 'm1-l5', 'm1-l6']) {
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
