// A ✓ must mean the student did the work (see grading.ts). Reported: objectives were ticked, and the lab page
// said "Signal received. Lab complete", although the student had not done the task.
import { readFileSync, readdirSync } from 'node:fs';
import { describe, expect, it } from 'vitest';
import { restoreNetwork, type SaveFile } from '../engine/serialize';
import type { Network } from '../engine/network';
import { IpDevice } from '../engine/ipdevice';
import { SwitchDevice } from '../engine/switch';
import { WinShell } from '../commands/windows/cmd';
import { checkLabStep, isEarned, labEarned, startingPasses, type LabResults } from './grading';

const load = (id: string): SaveFile => JSON.parse(readFileSync(`public/labs/${id}.json`, 'utf8'));
const labIds = readdirSync('public/labs')
  .filter((f) => f.endsWith('.json'))
  .map((f) => f.slice(0, -5))
  .sort();

function dev(net: Network, name: string): IpDevice {
  const d = [...net.devices.values()].find((x) => x.name === name);
  expect(d, `device ${name}`).toBeTruthy();
  return d as IpDevice;
}

function win(net: Network, name: string, line: string): void {
  let done = false;
  new WinShell(dev(net, name)).exec(line, { write: () => {} }, () => (done = true));
  net.scheduler.advanceTo(net.scheduler.now + 60_000);
  expect(done, line).toBe(true);
}

// What pressing "Check objectives" on a step does, results kept across presses like the store keeps them.
function press(net: Network, f: SaveFile, step: number, results: LabResults): LabResults {
  let done = false;
  checkLabStep(net, f.lab!, step, () => results, (r) => (results[r.id] = { pass: r.pass, detail: r.detail }), () => (done = true));
  for (let i = 0; !done && i < 100; i++) net.scheduler.advanceTo(net.scheduler.now + 10_000);
  expect(done).toBe(true);
  return results;
}

const earned = (f: SaveFile, r: LabResults, baseline: string[]) =>
  f.lab!.steps.flatMap((s) => s.objectives).filter((o) => isEarned(f.lab!, r, baseline, o.id)).map((o) => o.id);

describe('nothing is earned before the student does anything', () => {
  for (const id of labIds) {
    it(`${id}: pressing Check on every step of the untouched lab earns no objective and no pass`, () => {
      const f = load(id);
      const baseline = startingPasses(f);
      const net = restoreNetwork(f);
      const r: LabResults = {};
      f.lab!.steps.forEach((_, i) => press(net, f, i, r));
      expect(earned(f, r, baseline)).toEqual([]);
      expect(labEarned(f.lab!, r, baseline)).toBe(false);
    });

    it(`${id}: every step has work to do (at least one objective fails at the start)`, () => {
      const f = load(id);
      const baseline = startingPasses(f);
      for (const step of f.lab!.steps) {
        expect(step.objectives.some((o) => !baseline.includes(o.id)), step.title).toBe(true);
      }
    });
  }
});

describe('m1-l4: "astro can reach volt through the router" before any router is set up', () => {
  it('passes its ping at the start (same LAN) but only counts once the router step is done', () => {
    const f = load('m1-l4');
    const baseline = startingPasses(f);
    expect(baseline).toContain('m1l4-cross');
    const net = restoreNetwork(f);
    const r: LabResults = {};

    press(net, f, 1, r);
    expect(r['m1l4-cross'].pass).toBe(true);
    expect(isEarned(f.lab!, r, baseline, 'm1l4-cross')).toBe(false);

    const relay = [...net.devices.values()].find((d) => d.name === 'relay') as SwitchDevice;
    relay.vlans.set('eth2', 20);
    relay.vlans.set('eth4', 20);
    press(net, f, 0, r);
    win(net, 'gate', 'netsh interface ip set address "Ethernet" static 192.168.1.1 255.255.255.0');
    win(net, 'gate', 'netsh interface ip set address "Ethernet 2" static 10.0.0.1 255.255.255.0');
    win(net, 'volt', 'netsh interface ip set address "Ethernet" static 10.0.0.30 255.255.255.0');
    win(net, 'astro', 'route add 0.0.0.0 mask 0.0.0.0 192.168.1.1');
    win(net, 'volt', 'route add 0.0.0.0 mask 0.0.0.0 10.0.0.1');
    press(net, f, 1, r);

    expect(isEarned(f.lab!, r, baseline, 'm1l4-cross')).toBe(true);
    expect(labEarned(f.lab!, r, baseline)).toBe(true);
  });
});

describe('an old ✓ does not count once the network no longer does it', () => {
  it('m1-l1: undoing step 1 before checking step 2 does not pass the lab', () => {
    const f = load('m1-l1');
    const baseline = startingPasses(f);
    const net = restoreNetwork(f);
    const r: LabResults = {};

    win(net, 'astro', 'netsh interface ip set address "Ethernet" static 192.168.1.10 255.255.255.0');
    win(net, 'rivet', 'netsh interface ip set address "Ethernet" static 192.168.1.20 255.255.255.0');
    press(net, f, 0, r);
    expect(r['m1l1-astro-ip'].pass && r['m1l1-rivet-ip'].pass).toBe(true);

    // Both consoles move off the lesson's network; they can still ping each other, so step 2 passes on its own.
    win(net, 'astro', 'netsh interface ip set address "Ethernet" static 10.0.0.10 255.255.255.0');
    win(net, 'rivet', 'netsh interface ip set address "Ethernet" static 10.0.0.20 255.255.255.0');
    press(net, f, 1, r);
    expect(r['m1l1-ping-ar'].pass && r['m1l1-ping-ra'].pass).toBe(true);

    // Step 1's ✓ were checked again live, not trusted.
    expect(r['m1l1-astro-ip'].pass).toBe(false);
    expect(labEarned(f.lab!, r, baseline)).toBe(false);

    win(net, 'astro', 'netsh interface ip set address "Ethernet" static 192.168.1.10 255.255.255.0');
    win(net, 'rivet', 'netsh interface ip set address "Ethernet" static 192.168.1.20 255.255.255.0');
    press(net, f, 1, r);
    expect(labEarned(f.lab!, r, baseline)).toBe(true);
  });
});

describe('"should be blocked" objectives need a probe that actually left the sender', () => {
  it('c4-l3: a scanner with no address does not make port 21 "filtered"', () => {
    const f = load('c4-l3');
    const net = restoreNetwork(f);
    const volt = dev(net, 'volt');
    const r: LabResults = {};
    win(net, 'volt', 'netsh interface ip delete address "Ethernet" 192.168.5.10');
    expect(volt.interfaces[0].ip).toBeNull();
    press(net, f, 0, r);
    expect(r['c4l3-ftp-blocked'].pass, r['c4l3-ftp-blocked'].detail).toBe(false);
    expect(r['c4l3-rdp-blocked'].pass, r['c4l3-rdp-blocked'].detail).toBe(false);
  });

  it('m1-l4: astro with no address does not count as "can no longer reach volt"', () => {
    const f = load('m1-l4');
    const net = restoreNetwork(f);
    const r: LabResults = {};
    win(net, 'astro', 'netsh interface ip delete address "Ethernet" 192.168.1.10');
    expect(dev(net, 'astro').interfaces[0].ip).toBeNull();
    press(net, f, 0, r);
    expect(r['m1l4-isolated'].pass, r['m1l4-isolated'].detail).toBe(false);
  });
});
