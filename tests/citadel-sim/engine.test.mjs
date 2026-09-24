// Citadel Sim engine tests. Run: node --test tests/citadel-sim
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, readdirSync } from 'node:fs';
import {
  checkOne, createState, currentStep, evaluate, missionText, progress, resolvePath, restore, runCommand, snapshot, tokenize, weakReason,
} from '../../public/citadel-sim/js/engine.js';

const LAB_DIR = new URL('../../public/citadel-sim/labs/', import.meta.url);
const loadLab = (id) => JSON.parse(readFileSync(new URL(`${id}.json`, LAB_DIR), 'utf8'));
const sh = (lab, state, line) => runCommand(lab, state, line).output;

test('every lab file is well formed and fails in its starting state', () => {
  for (const f of readdirSync(LAB_DIR).filter((x) => x.endsWith('.json'))) {
    const lab = loadLab(f.replace(/\.json$/, ''));
    assert.equal(`${lab.id}.json`, f, 'lab id matches the file name');
    const ids = lab.steps.flatMap((s) => s.objectives.map((o) => o.id));
    assert.equal(new Set(ids).size, ids.length, `${f}: objective ids are unique`);
    assert.ok(ids.length > 0);
    for (const s of lab.steps) assert.ok(['astro', 'rivet', 'volt'].includes(s.by), `${f}: each step is given by a crew member`);
    const p = progress(lab, createState(lab));
    assert.ok(p.passed < p.total, `${f}: not already solved`);
    assert.ok(lab.story?.alert && lab.story?.victory, `${f}: has a story alert and victory line`);
  }
});

test('c1-l1 Know Your Citadel can be solved with the commands the steps teach', () => {
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  assert.equal(currentStep(lab, s), 0);

  assert.match(sh(lab, s, 'type README.txt'), /ORDERS FROM ASTRO/);
  assert.match(sh(lab, s, 'type inventory.txt'), /factory-password/);
  sh(lab, s, 'classify starmap asset');
  sh(lab, s, 'classify doom-hackers threat');
  sh(lab, s, 'classify factory-password vulnerability');
  sh(lab, s, 'classify vex-plan risk');
  assert.equal(currentStep(lab, s), 1);

  sh(lab, s, 'cd logs');
  assert.match(sh(lab, s, 'type night.log'), /event-3/);
  sh(lab, s, 'classify event-1 C');
  sh(lab, s, 'classify event-2 I');
  sh(lab, s, 'classify event-3 availability');
  assert.equal(currentStep(lab, s), 2);

  sh(lab, s, 'cd ..');
  assert.match(sh(lab, s, 'dir'), /<DIR>\s+temp/);
  assert.match(sh(lab, s, 'type temp\\note.txt'), /FLAG\{WALLS-ARE-PAPER\}/);
  assert.match(sh(lab, s, 'submit FLAG{WALLS-ARE-PAPER}'), /accepted/);
  const p = progress(lab, s);
  assert.equal(p.passed, p.total);
});

test('a wrong classification does not pass, and fixing it does', () => {
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  sh(lab, s, 'classify starmap threat');
  const find = () => evaluate(lab, s)[0].find((o) => o.id === 'starmap').pass;
  assert.equal(find(), false);
  sh(lab, s, 'classify starmap asset');
  assert.equal(find(), true);
  assert.match(sh(lab, s, 'classify moon asset'), /Unknown item/);
  assert.match(sh(lab, s, 'submit FLAG{nope}'), /not right/);
});

test('paths, quoting and Windows behaviour', () => {
  assert.equal(resolvePath('C:\\Citadel', 'logs'), 'C:\\Citadel\\logs');
  assert.equal(resolvePath('C:\\Citadel\\logs', '..'), 'C:\\Citadel');
  assert.equal(resolvePath('C:\\Citadel', '..\\..'), 'C:\\');
  assert.equal(resolvePath('C:\\Citadel', 'c:/citadel/temp'), 'C:\\citadel\\temp');
  assert.deepEqual(tokenize('findstr /i "shield power" night.log'), ['findstr', '/i', 'shield power', 'night.log']);
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  assert.match(sh(lab, s, 'ls'), /not recognized.*\n\(This is Windows\. Try: dir\)/s);
  assert.match(sh(lab, s, 'cd nowhere'), /cannot find the path/);
  assert.match(sh(lab, s, 'findstr /i vex logs\\night.log'), /^$/);
  assert.match(sh(lab, s, 'findstr /i "unknown account" logs\\night.log'), /event-1/);
  assert.match(sh(lab, s, 'whoami'), /citadel-core\\cadet/);
});

test('net user: list, details, disable, and a password policy', () => {
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  assert.match(sh(lab, s, 'net user'), /astro\s+rivet\s+volt/);
  assert.match(sh(lab, s, 'net user volt'), /Account active\s+Yes/);
  sh(lab, s, 'net user volt /active:no');
  assert.match(sh(lab, s, 'net user volt'), /Account active\s+No/);
  assert.match(sh(lab, s, 'net user astro admin'), /password policy/);
  assert.equal(weakReason('Sh1eld!Codexia#7', 'astro'), null);
  assert.match(sh(lab, s, 'net user astro Sh1eld!Codexia#7'), /completed successfully/);
});

test('taskkill stops a program and closes the port it served', () => {
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  assert.match(sh(lab, s, 'netstat -an'), /0\.0\.0\.0:23/);
  sh(lab, s, 'taskkill /im relayd.exe /f');
  assert.doesNotMatch(sh(lab, s, 'netstat -an'), /0\.0\.0\.0:23/);
  assert.doesNotMatch(sh(lab, s, 'tasklist'), /relayd\.exe/);
});

test('progress survives a save and reload, and a bad save starts fresh', () => {
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  sh(lab, s, 'type inventory.txt');
  sh(lab, s, 'classify starmap asset');
  sh(lab, s, 'net user volt /active:no');
  const again = restore(lab, JSON.parse(JSON.stringify(snapshot(s))));
  assert.equal(progress(lab, again).passed, progress(lab, s).passed);
  assert.match(sh(lab, again, 'net user volt'), /Account active\s+No/);
  assert.equal(progress(lab, restore(lab, { v: 99 })).passed, 0);
  assert.equal(progress(lab, restore(lab, null)).passed, 0);
});

// missionText() is what Ask Astro is sent as "the student's orders": only the current step, as the student sees it.
test('missionText shows only the current step and never an unread file or flag', () => {
  const lab = loadLab('c1-l1');
  const s = createState(lab);

  const first = missionText(lab, s);
  assert.match(first, /Step 1: Name what we protect/);
  assert.match(first, /\[ \] Read inventory\.txt/);
  assert.doesNotMatch(first, /Volt's Drill/, 'later steps stay hidden');
  assert.doesNotMatch(first, /WALLS-ARE-PAPER/);
  assert.doesNotMatch(first, /`/, 'backticks are stripped to plain text');

  for (const line of [
    'type inventory.txt', 'classify starmap asset', 'classify doom-hackers threat',
    'classify factory-password vulnerability', 'classify vex-plan risk',
    'cd logs', 'type night.log', 'classify event-1 C', 'classify event-2 I', 'classify event-3 A',
  ]) sh(lab, s, line);

  const drill = missionText(lab, s);
  assert.match(drill, /Step 3: Volt's Drill: find the calling card/);
  assert.match(drill, /\[ \] Found and read the intruder's file/);
  assert.doesNotMatch(drill, /WALLS-ARE-PAPER/, 'the real flag is not in the orders (only the FLAG{...} placeholder)');

  sh(lab, s, 'cd ..');
  sh(lab, s, 'type temp\\note.txt');
  sh(lab, s, 'submit FLAG{WALLS-ARE-PAPER}');
  assert.equal(missionText(lab, s), 'Every objective is clear. The attack is repelled.\n');
});

// A synthetic lab exercising the commands added after c1-l1, so new mechanics
// are covered without depending on any specific written lesson's lab file.
const KIT_LAB = {
  id: 'kit-test',
  host: { cwd: 'C:\\Kit' },
  files: {
    'C:\\Kit\\secret.txt': 'crew id 4471-2290, access code 998877\n',
    'C:\\Kit\\shield.cfg': 'shield_power=5\n',
    'C:\\Kit\\backup\\shield.cfg.bak': 'shield_power=100\n',
  },
  users: [{ name: 'relay', fullName: 'Relay Console', password: 'admin' }],
  masks: { 'c:\\kit\\secret.txt': { pattern: '\\d{4,}', replace: '****', output: 'C:\\Kit\\secret.masked.txt' } },
  inbox: [
    { id: 'm1', from: 'astro@codexia', subject: 'Real order', body: 'Proceed to bay 3.' },
    { id: 'm2', from: 'astro@c0dexia', subject: 'Fake order', body: 'Send the keys now.' },
  ],
  crackable: { AAAA1111: 'sunshine' },
  classify: {},
  steps: [{ by: 'volt', title: 'Kit', objectives: [{ id: 'x', label: 'x', check: { type: 'flag', value: 'never' } }] }],
};

test('login: exploit a default password, then it stops working after a change', () => {
  const s = createState(KIT_LAB);
  assert.match(sh(KIT_LAB, s, 'login relay admin'), /Login successful/);
  assert.ok(checkOne(KIT_LAB, s, { type: 'login', user: 'relay', password: 'admin', expect: 'success' }));
  sh(KIT_LAB, s, 'net user relay Sh1eld!Codexia#7');
  assert.match(sh(KIT_LAB, s, 'login relay admin'), /incorrect password/);
  assert.ok(checkOne(KIT_LAB, s, { type: 'login', user: 'relay', password: 'admin', expect: 'fail' }));
});

test('icacls locks a file to a user, mask redacts it, and both survive save/restore', () => {
  const s = createState(KIT_LAB);
  assert.match(sh(KIT_LAB, s, 'icacls secret.txt /deny doom_intern:F'), /Successfully processed 1 files/);
  assert.ok(checkOne(KIT_LAB, s, { type: 'denied', file: 'C:\\Kit\\secret.txt', user: 'doom_intern' }));
  sh(KIT_LAB, s, 'mask secret.txt');
  assert.ok(checkOne(KIT_LAB, s, { type: 'masked', file: 'C:\\Kit\\secret.masked.txt', mustNotMatch: '\\d{4,}' }));
  const again = restore(KIT_LAB, JSON.parse(JSON.stringify(snapshot(s))));
  assert.ok(checkOne(KIT_LAB, again, { type: 'denied', file: 'C:\\Kit\\secret.txt', user: 'doom_intern' }));
  assert.ok(checkOne(KIT_LAB, again, { type: 'masked', file: 'C:\\Kit\\secret.masked.txt', mustNotMatch: '\\d{4,}' }));
});

test('copy restores a tampered file from backup, checked by same-content', () => {
  const s = createState(KIT_LAB);
  assert.ok(!checkOne(KIT_LAB, s, { type: 'same-content', a: 'C:\\Kit\\shield.cfg', b: 'C:\\Kit\\backup\\shield.cfg.bak' }));
  sh(KIT_LAB, s, 'copy backup\\shield.cfg.bak shield.cfg');
  assert.ok(checkOne(KIT_LAB, s, { type: 'same-content', a: 'C:\\Kit\\shield.cfg', b: 'C:\\Kit\\backup\\shield.cfg.bak' }));
});

test('certutil/hash fingerprints a file deterministically, and ordered checks command sequence', () => {
  const s = createState(KIT_LAB);
  const out1 = sh(KIT_LAB, s, 'certutil -hashfile shield.cfg SHA256');
  const out2 = sh(KIT_LAB, s, 'hash shield.cfg');
  assert.match(out1, /SHA256 hash of/);
  assert.equal(out1.match(/\n([0-9A-F]{64})\n/)[1], out2.match(/\n([0-9A-F]{64})\n/)[1], 'same content, same fake hash');
  assert.ok(!checkOne(KIT_LAB, s, { type: 'ordered', before: '^hash ', after: '^copy ' }));
  sh(KIT_LAB, s, 'copy backup\\shield.cfg.bak shield.cfg');
  assert.ok(checkOne(KIT_LAB, s, { type: 'ordered', before: '^hash ', after: '^copy ' }));
});

test('inbox triage: reading and marking messages', () => {
  const s = createState(KIT_LAB);
  assert.match(sh(KIT_LAB, s, 'inbox'), /m1.*m2/s);
  assert.match(sh(KIT_LAB, s, 'open m2'), /Send the keys now/);
  sh(KIT_LAB, s, 'report m2');
  sh(KIT_LAB, s, 'keep m1');
  assert.ok(checkOne(KIT_LAB, s, { type: 'triaged', id: 'm2', as: 'report' }));
  assert.ok(checkOne(KIT_LAB, s, { type: 'triaged', id: 'm1', as: 'keep' }));
  assert.ok(!checkOne(KIT_LAB, s, { type: 'triaged', id: 'm1', as: 'report' }));
});

test('caesar and xor ciphers round-trip, and hashcrack looks up the wordlist', () => {
  const s = createState(KIT_LAB);
  const enc = sh(KIT_LAB, s, 'caesar encode 3 Doom Falls').trim();
  assert.equal(sh(KIT_LAB, s, `caesar decode 3 ${enc}`).trim(), 'Doom Falls');
  const cipher = sh(KIT_LAB, s, 'xor codexia secret plan').trim();
  assert.match(cipher, /^[0-9a-f]+$/);
  assert.equal(sh(KIT_LAB, s, `xor codexia ${cipher}`).trim(), 'secret plan');
  assert.match(sh(KIT_LAB, s, 'hashcrack AAAA1111'), /sunshine/);
  assert.match(sh(KIT_LAB, s, 'hashcrack ZZZZ0000'), /No match/);
});

test('classify accepts free-form categories beyond the built-in CIA/asset shortcuts', () => {
  const lab = { ...KIT_LAB, classify: { 'control-1': 'x' } };
  const s = createState(lab);
  assert.match(sh(lab, s, 'classify control-1 detect'), /Recorded: control-1 = detect/);
  assert.ok(checkOne(lab, s, { type: 'classified', item: 'control-1', as: 'detect' }));
});
