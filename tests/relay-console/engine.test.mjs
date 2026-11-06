// Relay Console engine tests (Networking 2 router-console labs). Run: node --test tests/relay-console
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, readdirSync } from 'node:fs';
import {
  createState, currentStep, evaluate, ifName, match, progress, promptText, restore, runCommand, snapshot,
} from '../../public/relay-console/js/engine.js';
import { SOLUTIONS } from './solutions.mjs';

const LAB_DIR = new URL('../../public/relay-console/labs/', import.meta.url);
const labIds = readdirSync(LAB_DIR).filter((f) => f.endsWith('.json')).map((f) => f.slice(0, -5)).sort();
const loadLab = (id) => JSON.parse(readFileSync(new URL(`${id}.json`, LAB_DIR), 'utf8'));
const sh = (lab, state, line) => runCommand(lab, state, line).output;

test('IOS-style matching: abbreviations, parameters, interface names', () => {
  assert.equal(match('show ip route', ['sh', 'ip', 'ro']).status, 'full');
  assert.equal(match('show ip route', ['sh', 'ip']).status, 'incomplete');
  assert.equal(match('show ip route', ['sh', 'ipx', 'route']).status, 'fail');
  assert.deepEqual(match('neighbor <ip> remote-as <n>', ['nei', '203.0.113.1', 'rem', '64500']).args, ['203.0.113.1', '64500']);
  assert.equal(match('neighbor <ip> remote-as <n>', ['nei', '203.0.113', 'rem', '64500']).status, 'fail');
  assert.deepEqual(match('interface <if>', ['int', 'gi0/0']).args, ['GigabitEthernet0/0']);
  assert.deepEqual(match('interface <if>', ['interface', 'Tunnel', '1']).args, ['Tunnel1']);
  assert.equal(ifName('lo0'), 'Loopback0');
});

test('every lab file is well formed, fails at the start, and has a tested solution', () => {
  assert.ok(labIds.length > 0);
  for (const id of labIds) {
    const lab = loadLab(id);
    assert.equal(lab.id, id, 'lab id matches the file name');
    assert.equal(lab.app, 'relay-console');
    const ids = lab.steps.flatMap((s) => s.objectives.map((o) => o.id));
    assert.equal(new Set(ids).size, ids.length, `${id}: objective ids are unique`);
    for (const s of lab.steps) {
      assert.ok(['astro', 'rivet', 'volt'].includes(s.by), `${id}: each step is given by a crew member`);
      assert.ok(s.objectives.length > 0);
    }
    const p = progress(lab, createState(lab));
    assert.equal(p.passed, 0, `${id}: nothing passes before the student acts`);
    assert.ok(lab.story?.alert && lab.story?.victory, `${id}: has an alert and a victory line`);
    assert.ok(SOLUTIONS[id], `${id}: has a solution in tests/relay-console/solutions.mjs`);
  }
});

for (const id of labIds) {
  test(`${id} can be solved with the commands its steps teach`, () => {
    const lab = loadLab(id);
    const s = createState(lab);
    for (const line of SOLUTIONS[id] ?? []) {
      const out = sh(lab, s, line);
      assert.doesNotMatch(out, /% Invalid input|% Incomplete/, `${id}: "${line}" was rejected:\n${out}`);
    }
    const res = evaluate(lab, s).flat().filter((o) => !o.pass).map((o) => o.id);
    assert.deepEqual(res, [], `${id}: objectives still failing`);
    assert.equal(currentStep(lab, s), lab.steps.length);
    // The progress survives a save and reload.
    const again = restore(lab, JSON.parse(JSON.stringify(snapshot(s))));
    assert.equal(progress(lab, again).passed, progress(lab, s).passed);
  });
}

test('modes, prompts, do, and wrong answers', () => {
  const lab = loadLab('g1-l2');
  const s = createState(lab);
  assert.equal(promptText(lab, s), 'SKYPORT-EDGE>');
  assert.match(sh(lab, s, 'conf t'), /Invalid input/);
  sh(lab, s, 'en');
  assert.equal(promptText(lab, s), 'SKYPORT-EDGE#');
  sh(lab, s, 'conf t');
  assert.equal(promptText(lab, s), 'SKYPORT-EDGE(config)#');
  assert.match(sh(lab, s, 'do sh ip route'), /Tunnel1/);
  assert.match(sh(lab, s, 'submit A=SaaS'), /Not the answer/);
  assert.match(sh(lab, s, 'submit a=iaas'), /Accepted/);
  sh(lab, s, 'ip route 10.200.0.0 255.255.0.0 172.31.0.2');
  sh(lab, s, 'end');
  assert.match(sh(lab, s, 'show running-config'), /ip route 10\.200\.0\.0 255\.255\.0\.0 172\.31\.0\.2/);
  assert.match(sh(lab, s, '?'), /show ip route/);
});
