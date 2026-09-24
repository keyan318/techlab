// Solvability guard for the two TechLab Information Security 1 labs that run in NetSim
// (c4-l2, c4-l3), authored as plain JSON in public/netsim-app/labs rather than in ./library.
// Every objective must fail from the lab's starting state, and pass after exactly the
// Windows commands the lesson text tells the student to type.
import { describe, expect, it } from 'vitest';
import { readFileSync } from 'node:fs';
import { restoreNetwork } from '../engine/serialize';
import type { Network } from '../engine/network';
import { IpDevice } from '../engine/ipdevice';
import { WinShell } from '../commands/windows/cmd';
import { runChecks, type CheckResult } from './checker';
import type { Objective } from './types';

function loadLab(id: string) {
  const raw = readFileSync(new URL(`../../../../public/netsim-app/labs/${id}.json`, import.meta.url), 'utf8');
  return JSON.parse(raw);
}

function device(net: Network, name: string): IpDevice {
  const d = [...net.devices.values()].find((x) => x.name === name);
  expect(d, `device ${name}`).toBeTruthy();
  return d as IpDevice;
}

function check(net: Network, objectives: Objective[]): Record<string, CheckResult> {
  const results: Record<string, CheckResult> = {};
  let finished = false;
  runChecks(net, objectives, (r) => (results[r.id] = r), () => (finished = true));
  net.scheduler.advanceTo(net.scheduler.now + 120_000);
  expect(finished).toBe(true);
  return results;
}

function shell(net: Network, dev: IpDevice, line: string): void {
  let finished = false;
  new WinShell(dev).exec(line, { write: () => {} }, () => (finished = true));
  net.scheduler.advanceTo(net.scheduler.now + 60_000);
  expect(finished).toBe(true);
}

describe('c4-l2 — Enterprise Security Architecture (Zones of Trust)', () => {
  it('starts open between zones, then locks down after the commands in the lesson', () => {
    const file = loadLab('c4-l2');
    const net = restoreNetwork(file);
    const lab = file.lab;
    const step1 = lab.steps[0].objectives;
    const step2 = lab.steps[1].objectives;

    let r = check(net, step1);
    expect(r['c4l2-default-block'].pass, r['c4l2-default-block']?.detail).toBe(false);

    const fw = device(net, 'citadel-fw');
    shell(net, fw, 'netsh advfirewall set allprofiles firewallpolicy blockinbound,allowoutbound');

    r = check(net, step1);
    expect(r['c4l2-default-block'].pass, r['c4l2-default-block']?.detail).toBe(true);

    r = check(net, step2);
    expect(r['c4l2-web-open'].pass, r['c4l2-web-open']?.detail).toBe(false);
    expect(r['c4l2-deck-blocked'].pass, r['c4l2-deck-blocked']?.detail).toBe(true);

    shell(net, fw, 'netsh advfirewall firewall add rule name=AllowWeb dir=in action=allow protocol=TCP localport=80');

    r = check(net, step2);
    expect(r['c4l2-web-open'].pass, r['c4l2-web-open']?.detail).toBe(true);
    expect(r['c4l2-deck-blocked'].pass, r['c4l2-deck-blocked']?.detail).toBe(true);
  });
});

describe('c4-l3 — Network Security (scan + block)', () => {
  it('all three ports open at first, then only 21 and 3389 stay filtered after hardening', () => {
    const file = loadLab('c4-l3');
    const net = restoreNetwork(file);
    const lab = file.lab;
    const objectives = lab.steps[0].objectives;

    let r = check(net, objectives);
    expect(r['c4l3-ftp-blocked'].pass, r['c4l3-ftp-blocked']?.detail).toBe(false);
    expect(r['c4l3-rdp-blocked'].pass, r['c4l3-rdp-blocked']?.detail).toBe(false);
    expect(r['c4l3-web-open'].pass, r['c4l3-web-open']?.detail).toBe(true);

    const hull = device(net, 'outer-hull');
    shell(net, hull, 'netsh advfirewall set allprofiles state on');
    shell(net, hull, 'netsh advfirewall firewall add rule name=BlockFTP dir=in action=block protocol=TCP localport=21');
    shell(net, hull, 'netsh advfirewall firewall add rule name=BlockRDP dir=in action=block protocol=TCP localport=3389');
    shell(net, hull, 'netsh advfirewall firewall add rule name=AllowWeb dir=in action=allow protocol=TCP localport=80');

    r = check(net, objectives);
    expect(r['c4l3-ftp-blocked'].pass, r['c4l3-ftp-blocked']?.detail).toBe(true);
    expect(r['c4l3-rdp-blocked'].pass, r['c4l3-rdp-blocked']?.detail).toBe(true);
    expect(r['c4l3-web-open'].pass, r['c4l3-web-open']?.detail).toBe(true);
  });
});
