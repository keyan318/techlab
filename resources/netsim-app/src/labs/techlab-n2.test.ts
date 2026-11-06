// Networking 2 ("Light the Grid") NetSim labs: every g*-l* lab can be solved with the commands its steps
// teach. Routers and switches are typed at their Cisco IOS console (one session per device, so modes carry
// over between lines, like a real terminal); PCs and servers use Windows commands.
import { readFileSync, readdirSync } from 'node:fs';
import { describe, expect, it } from 'vitest';
import { restoreNetwork, type SaveFile } from '../engine/serialize';
import type { Network } from '../engine/network';
import type { Device } from '../engine/device';
import { IpDevice } from '../engine/ipdevice';
import { WinShell } from '../commands/windows/cmd';
import { IosShell } from '../commands/ios/ios';
import { runChecks, type CheckResult } from './checker';
import { setShellOs } from '../os';
import { N2_SOLUTIONS } from './techlab-n2.solutions';

setShellOs('windows');

const load = (id: string): SaveFile => JSON.parse(readFileSync(`public/labs/${id}.json`, 'utf8'));
const labIds = readdirSync('public/labs')
  .filter((f) => /^g\d+-l\d+\.json$/.test(f))
  .map((f) => f.slice(0, -5))
  .sort();

function dev(net: Network, name: string): Device {
  const d = [...net.devices.values()].find((x) => x.name === name);
  expect(d, `device ${name}`).toBeTruthy();
  return d!;
}

export function play(net: Network, steps: [string, string][]): string {
  const ios = new Map<string, IosShell>();
  let all = '';
  for (const [name, line] of steps) {
    const d = dev(net, name);
    let out = '';
    let done = false;
    if (d.cli === 'ios') {
      if (!ios.has(name)) ios.set(name, new IosShell(d));
      ios.get(name)!.exec(line, { write: (s) => (out += s) }, () => (done = true));
    } else {
      new WinShell(d as IpDevice).exec(line, { write: (s) => (out += s) }, () => (done = true));
    }
    net.scheduler.advanceTo(net.scheduler.now + 30_000);
    expect(done, `${name}: ${line}`).toBe(true);
    expect(out, `${name}: ${line}`).not.toMatch(/% Invalid input|% Incomplete|is not recognized|Command rejected/);
    all += out;
  }
  return all;
}

function grade(net: Network, f: SaveFile): Record<string, CheckResult> {
  const out: Record<string, CheckResult> = {};
  let done = false;
  runChecks(net, f.lab!.steps.flatMap((s) => s.objectives), (r) => (out[r.id] = r), () => (done = true));
  for (let i = 0; !done && i < 200; i++) net.scheduler.advanceTo(net.scheduler.now + 10_000);
  expect(done).toBe(true);
  return out;
}

describe('Networking 2 NetSim labs', () => {
  it('there are labs, and each has a solution', () => {
    expect(labIds.length).toBeGreaterThan(0);
    for (const id of labIds) expect(N2_SOLUTIONS[id], `${id} needs a solution in techlab-n2.solutions.ts`).toBeTruthy();
  });

  for (const id of labIds) {
    it(`${id}: well formed, with IOS routers/switches`, () => {
      const f = load(id);
      expect(f.lab!.id).toBe(id);
      const ids = f.lab!.steps.flatMap((s) => s.objectives.map((o) => o.id));
      expect(new Set(ids).size).toBe(ids.length);
      for (const d of f.devices) if (d.kind === 'router' || d.kind === 'switch') expect(d.cli, d.name).toBe('ios');
    });

    it(`${id}: solved with the commands the steps teach`, () => {
      const f = load(id);
      const net = restoreNetwork(f);
      play(net, N2_SOLUTIONS[id] ?? []);
      const r = grade(net, f);
      const failing = Object.values(r).filter((x) => !x.pass).map((x) => `${x.id}: ${x.detail}`);
      expect(failing).toEqual([]);
    });
  }
});
