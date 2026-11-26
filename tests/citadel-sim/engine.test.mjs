// Citadel Sim engine tests. Run: node --test tests/citadel-sim
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, readdirSync } from 'node:fs';
import {
  COMMAND_NAMES, checkOne, complete, createState, currentStep, evaluate, missionText, progress, promptFor, resolvePath, restore,
  runCommand, snapshot, tokenize, weakReason,
} from '../../public/citadel-sim/js/engine.js';

const LAB_DIR = new URL('../../public/citadel-sim/labs/', import.meta.url);
const loadLab = (id) => JSON.parse(readFileSync(new URL(`${id}.json`, LAB_DIR), 'utf8'));
const allLabs = () => readdirSync(LAB_DIR).filter((x) => x.endsWith('.json')).map((f) => loadLab(f.replace(/\.json$/, '')));
const sh = (lab, state, line) => runCommand(lab, state, line).output;
const HOME = '/home/cadet/citadel';

test('every lab file is well formed and fails in its starting state', () => {
  for (const lab of allLabs()) {
    const f = `${lab.id}.json`;
    const ids = lab.steps.flatMap((s) => s.objectives.map((o) => o.id));
    assert.equal(new Set(ids).size, ids.length, `${f}: objective ids are unique`);
    assert.ok(ids.length > 0);
    for (const s of lab.steps) assert.ok(['astro', 'rivet', 'volt'].includes(s.by), `${f}: each step is given by a crew member`);
    const p = progress(lab, createState(lab));
    assert.ok(p.passed < p.total, `${f}: not already solved`);
    assert.ok(lab.story?.alert && lab.story?.victory, `${f}: has a story alert and victory line`);
  }
});

test('every lab is a Linux machine: Linux paths, and every graded thing exists', () => {
  for (const lab of allLabs()) {
    const f = `${lab.id}.json`;
    assert.equal(lab.host.cwd, HOME, `${f}: starts in ~/citadel`);
    assert.ok(createState(lab).users.length >= 0);
    const files = Object.keys(lab.files);
    for (const k of files) assert.match(k, /^\/home\/cadet\/citadel\/[^\\]+$/, `${f}: ${k} is a Linux path`);
    const all = JSON.stringify(lab);
    assert.doesNotMatch(all, /[A-Za-z]:\\\\|\.exe\b|net user|taskkill|tasklist|icacls|certutil|findstr|Command Prompt/, `${f}: no Windows left`);
    const procs = (lab.processes || []).map((p) => p.name);
    const ports = (lab.services || []).map((s) => s.port);
    const users = (lab.users || []).map((u) => u.name);
    for (const s of lab.services || []) if (s.process) assert.ok(procs.includes(s.process), `${f}: service ${s.name} runs a listed process`);
    for (const o of lab.steps.flatMap((s) => s.objectives)) {
      const c = o.check;
      if (c.type === 'read' || c.type === 'denied') assert.ok(files.includes(c.file), `${f}: ${o.id} file ${c.file} exists`);
      if (c.type === 'same-content') for (const p of [c.a, c.b]) assert.ok(files.includes(p), `${f}: ${o.id} file ${p} exists`);
      if (c.type === 'process-killed') assert.ok(procs.includes(c.name), `${f}: ${o.id} process ${c.name} exists`);
      if (c.type === 'port-closed') assert.ok(ports.includes(c.port), `${f}: ${o.id} port ${c.port} is served`);
      if (/^user-|^password-changed$|^login$/.test(c.type)) assert.ok(users.includes(c.user), `${f}: ${o.id} user ${c.user} exists`);
    }
  }
});

test('every command a lab tells students to type is one the machine knows', () => {
  for (const lab of allLabs()) {
    const texts = [...lab.steps.flatMap((s) => [s.body, ...s.objectives.map((o) => o.label)])];
    for (const t of texts) {
      for (const [, code] of t.matchAll(/`([^`]+)`/g)) {
        const words = code.trim().split(/\s+/);
        if (words.length === 1 && !COMMAND_NAMES.includes(words[0])) continue;   // a file, folder or value, not a command
        const cmd = words[0] === 'sudo' ? words[1] : words[0];
        assert.ok(COMMAND_NAMES.includes(cmd), `${lab.id}: \`${code}\` uses a command the sim knows`);
      }
    }
  }
});

// Plays every lab to the end with Linux commands only, one objective at a time, in step order.
function solveWithLinux(lab) {
  const s = createState(lab);
  const run = (line) => runCommand(lab, s, line);
  const STRONG = 'Sh1eld!Codexia#7';
  const act = (c) => {
    switch (c.type) {
      case 'read': return run(`cat ${c.file}`);
      case 'classified': return run(`echo ${c.item} ${c.as} >> findings.txt`);
      case 'flag': return run(`submit ${c.value}`);
      case 'user-disabled': return run(`sudo usermod -L ${c.user}`);
      case 'user-enabled': return run(`sudo usermod -U ${c.user}`);
      case 'password-changed': run(`sudo passwd ${c.user}`); run(STRONG); return run(STRONG);
      case 'process-killed': {
        const p = s.processes.find((x) => x.name === c.name);
        return run(`sudo kill ${p.pid}`);
      }
      case 'port-closed': {
        const sv = s.services.find((x) => x.port === c.port);
        return run(`sudo pkill ${sv.process}`);
      }
      case 'denied': return run(`setfacl -m u:${c.user}:--- ${c.file}`);
      case 'same-content': return c.expect === 'different' ? null : run(`cp ${c.b} ${c.a}`);
      case 'triaged': return run(`${c.as} ${c.id}`);
      case 'login': return run(`login ${c.user} ${c.password}`);
      case 'masked': return run(`mask ${Object.keys(lab.masks).find((k) => lab.masks[k].output === c.file)}`);
      case 'ordered':
        if (/sha256sum/.test(c.before)) return run('sha256sum README.txt');
        if (/test-node/.test(c.before)) { run('cp patch/shield-fix.cfg test-node.cfg'); return run('cp patch/shield-fix.cfg prod-node.cfg'); }
        return null;
      default: return null;
    }
  };
  // Evidence first: anything that must happen before a restore runs before the step's other work.
  for (const o of lab.steps.flatMap((st) => st.objectives)) if (o.check.type === 'ordered') act(o.check);
  for (const st of lab.steps) {
    for (let pass = 0; pass < 3; pass++) for (const o of st.objectives) if (!checkOne(lab, s, o.check)) act(o.check);
  }
  return s;
}

test('every lab can be finished with Linux commands', () => {
  for (const lab of allLabs()) {
    const s = solveWithLinux(lab);
    const failing = evaluate(lab, s).flatMap((r, i) => r.filter((o) => !o.pass).map((o) => `step ${i + 1} ${o.id}`));
    assert.deepEqual(failing, [], `${lab.id}: objectives left`);
  }
});

test('c1-l1 Know Your Citadel can be solved with the commands the steps teach', () => {
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  assert.equal(currentStep(lab, s), 0);
  assert.equal(promptFor(s), 'cadet@citadel-core:~/citadel$ ');

  assert.match(sh(lab, s, 'cat README.txt'), /ORDERS FROM ASTRO/);
  assert.match(sh(lab, s, 'cat inventory.txt'), /factory-password/);
  sh(lab, s, 'echo starmap asset >> findings.txt');
  sh(lab, s, 'echo doom-hackers threat >> findings.txt');
  sh(lab, s, 'echo factory-password vulnerability >> findings.txt');
  sh(lab, s, 'echo vex-plan risk >> findings.txt');
  assert.equal(currentStep(lab, s), 1);

  sh(lab, s, 'cd logs');
  assert.equal(promptFor(s), 'cadet@citadel-core:~/citadel/logs$ ');
  assert.match(sh(lab, s, 'cat night.log'), /event-3/);
  sh(lab, s, 'echo event-1 C >> findings.txt');
  sh(lab, s, 'echo event-2 I >> findings.txt');
  sh(lab, s, 'echo event-3 availability >> findings.txt');
  assert.equal(currentStep(lab, s), 2);

  sh(lab, s, 'cd ..');
  assert.match(sh(lab, s, 'ls'), /(^|\s)temp\/(\s|$)/);
  assert.match(sh(lab, s, 'cat temp/note.txt'), /FLAG\{WALLS-ARE-PAPER\}/);
  assert.match(sh(lab, s, 'submit FLAG{WALLS-ARE-PAPER}'), /accepted/);
  const p = progress(lab, s);
  assert.equal(p.passed, p.total);
});

test('a wrong classification does not pass, and fixing it does', () => {
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  sh(lab, s, 'echo starmap threat >> findings.txt');
  const find = () => evaluate(lab, s)[0].find((o) => o.id === 'starmap').pass;
  assert.equal(find(), false);
  sh(lab, s, 'echo starmap asset >> findings.txt');
  assert.equal(find(), true);
  assert.match(sh(lab, s, 'echo moon asset >> findings.txt'), /"moon" is not on this lab's list/);
  assert.match(sh(lab, s, 'submit FLAG{nope}'), /not right/);
});

test('paths, quoting, pipes and Linux behaviour', () => {
  assert.equal(resolvePath(HOME, 'logs'), `${HOME}/logs`);
  assert.equal(resolvePath(`${HOME}/logs`, '..'), HOME);
  assert.equal(resolvePath(HOME, '../../..'), '/');
  assert.equal(resolvePath(`${HOME}/logs`, '~/citadel/temp', '/home/cadet'), `${HOME}/temp`);
  assert.deepEqual(tokenize(`grep -i "shield power" 'night log'`), ['grep', '-i', 'shield power', 'night log']);
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  assert.match(sh(lab, s, 'dir'), /command not found\n\(dir is a Windows command\. On Linux, use: ls\)/);
  assert.match(sh(lab, s, 'type README.txt'), /On Linux, use: cat/);
  assert.match(sh(lab, s, 'LS'), /Linux is case-sensitive\. Try: ls/);
  assert.match(sh(lab, s, 'cat readme.txt'), /No such file or directory\n\(Linux is case-sensitive\. It is called README\.txt\)/);
  assert.match(sh(lab, s, 'cat logs\\night.log'), /Linux paths use \/ not \\\. Try: logs\/night\.log/);
  assert.match(sh(lab, s, 'cd nowhere'), /No such file or directory/);
  assert.match(sh(lab, s, 'cd logs'), /^$/);
  assert.match(sh(lab, s, 'pwd'), /\/home\/cadet\/citadel\/logs/);
  assert.match(sh(lab, s, 'cd'), /^$/);
  assert.equal(s.cwd, '/home/cadet');
  sh(lab, s, 'cd ~/citadel');
  assert.match(sh(lab, s, 'grep -i vex logs/night.log'), /^$/);
  assert.match(sh(lab, s, 'grep -i "unknown account" logs/night.log'), /event-1/);
  assert.match(sh(lab, s, 'cat logs/night.log | grep -i "unknown account"'), /event-1/);
  assert.match(sh(lab, s, 'cat logs/night.log | wc -l'), /^\d+\n$/);
  assert.match(sh(lab, s, 'grep -r FLAG .'), /temp\/note\.txt:.*FLAG\{WALLS-ARE-PAPER\}/);
  assert.match(sh(lab, s, 'whoami'), /^cadet\n$/);
  assert.match(sh(lab, s, 'sudo whoami'), /^root\n$/);
  assert.match(sh(lab, s, 'uname -a'), /^Linux citadel-core/);
  assert.match(sh(lab, s, 'ls -l'), /drwxr-xr-x .* logs/);
  assert.match(sh(lab, s, 'cat < note.txt'), /switched off/);
  sh(lab, s, 'cd logs && cd ..');
  assert.equal(s.cwd, HOME);
});

test('accounts: /etc/passwd, lock with usermod, sudo, and passwd asks twice', () => {
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  assert.match(sh(lab, s, 'cat /etc/passwd'), /^root:x:0:0.*\nastro:x:\d+.*\nrivet:x:.*\nvolt:x:/s);
  assert.match(sh(lab, s, 'cat /etc/shadow'), /Permission denied/);
  assert.match(sh(lab, s, 'sudo passwd -S volt'), /^volt P /);
  assert.match(sh(lab, s, 'usermod -L volt'), /Permission denied[\s\S]*Try: sudo usermod -L volt/);
  assert.match(sh(lab, s, 'sudo usermod -L volt'), /^$/);
  assert.match(sh(lab, s, 'sudo passwd -S volt'), /^volt L /);
  assert.match(sh(lab, s, 'sudo cat /etc/shadow'), /\nvolt:!\$6\$/);
  assert.match(sh(lab, s, 'id astro'), /uid=\d+\(astro\)/);

  // passwd: a weak password is refused, a mismatch is refused, a strong one twice works.
  assert.match(sh(lab, s, 'passwd astro'), /You may not view or modify password information for astro/);
  const first = runCommand(lab, s, 'sudo passwd astro');
  assert.equal(first.prompt, 'New password: ');
  assert.match(sh(lab, s, 'admin'), /BAD PASSWORD/);
  sh(lab, s, 'sudo passwd astro');
  sh(lab, s, 'Sh1eld!Codexia#7');
  assert.match(sh(lab, s, 'Sh1eld!Codexia#8'), /do not match/);
  sh(lab, s, 'sudo passwd astro');
  assert.equal(runCommand(lab, s, 'Sh1eld!Codexia#7').prompt, 'Retype new password: ');
  assert.match(sh(lab, s, 'Sh1eld!Codexia#7'), /updated successfully/);
  assert.ok(checkOne(lab, s, { type: 'password-changed', user: 'astro' }));
  assert.ok(!s.history.some((h) => h.includes('Sh1eld')), 'passwords never go into the history');
  assert.equal(weakReason('Sh1eld!Codexia#7', 'astro'), null);
});

test('kill and pkill stop a program and close its port; root processes need sudo', () => {
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  const relay = lab.processes.find((p) => p.name === 'relayd');
  assert.match(sh(lab, s, 'ss -tlnp'), /0\.0\.0\.0:23 [\s\S]*Try: sudo ss -tlnp/);
  assert.match(sh(lab, s, 'sudo ss -tlnp'), new RegExp(`0\\.0\\.0\\.0:23 .*users:\\(\\("relayd",pid=${relay.pid}`));
  assert.match(sh(lab, s, 'ps aux'), /relayd/);
  assert.match(sh(lab, s, `kill ${relay.pid}`), /Operation not permitted/);
  assert.match(sh(lab, s, `sudo kill -9 ${relay.pid}`), /^$/);
  assert.doesNotMatch(sh(lab, s, 'sudo ss -tlnp'), /0\.0\.0\.0:23 /);
  assert.doesNotMatch(sh(lab, s, 'ps aux'), /relayd/);
  assert.ok(checkOne(lab, s, { type: 'port-closed', port: 23 }));

  const t = createState(lab);
  sh(lab, t, 'sudo pkill relayd');
  assert.ok(checkOne(lab, t, { type: 'process-killed', name: 'relayd' }));
});

test('progress survives a save and reload, and a bad or old Windows save starts fresh', () => {
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  sh(lab, s, 'cat inventory.txt');
  sh(lab, s, 'echo starmap asset >> findings.txt');
  sh(lab, s, 'sudo usermod -L volt');
  sh(lab, s, 'chmod 600 inventory.txt');
  const again = restore(lab, JSON.parse(JSON.stringify(snapshot(s))));
  assert.equal(progress(lab, again).passed, progress(lab, s).passed);
  assert.match(sh(lab, again, 'sudo passwd -S volt'), /^volt L /);
  assert.match(sh(lab, again, 'ls -l inventory.txt'), /^-rw------- /);
  assert.equal(progress(lab, restore(lab, { v: 99 })).passed, 0);
  assert.equal(progress(lab, restore(lab, { v: 1, cwd: 'C:\\Citadel', read: ['c:\\citadel\\inventory.txt'] })).passed, 0);
  assert.equal(progress(lab, restore(lab, null)).passed, 0);
});

test('Tab completes commands and file names', () => {
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  assert.equal(complete(s, 'whoa').line, 'whoami ');
  assert.deepEqual(complete(s, 'ca').options, ['caesar', 'cat'], 'two fit: list both, like bash');
  assert.equal(complete(s, 'cat inv').line, 'cat inventory.txt ');
  assert.equal(complete(s, 'cd lo').line, 'cd logs/');
  assert.equal(complete(s, 'cat logs/ni').line, 'cat logs/night.log ');
  assert.equal(complete(s, 'sudo usermo').line, 'sudo usermod ');
  assert.ok(complete(s, 'c').options.includes('cat'));
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
    'cat inventory.txt', 'echo starmap asset >> findings.txt', 'echo doom-hackers threat >> findings.txt',
    'echo factory-password vulnerability >> findings.txt', 'echo vex-plan risk >> findings.txt',
    'cd logs', 'cat night.log', 'echo event-1 C >> findings.txt', 'echo event-2 I >> findings.txt', 'echo event-3 A >> findings.txt',
  ]) sh(lab, s, line);

  const drill = missionText(lab, s);
  assert.match(drill, /Step 3: Volt's Drill: find the calling card/);
  assert.match(drill, /\[ \] Found and read the intruder's file/);
  assert.doesNotMatch(drill, /WALLS-ARE-PAPER/, 'the real flag is not in the orders (only the FLAG{...} placeholder)');

  sh(lab, s, 'cd ..');
  sh(lab, s, 'cat temp/note.txt');
  sh(lab, s, 'submit FLAG{WALLS-ARE-PAPER}');
  assert.equal(missionText(lab, s), 'Every objective is clear. The attack is repelled.\n');
});

// A synthetic lab exercising the field-kit commands, so new mechanics
// are covered without depending on any specific written lesson's lab file.
const KIT_LAB = {
  id: 'kit-test',
  host: { cwd: '/home/cadet/kit' },
  files: {
    '/home/cadet/kit/secret.txt': 'crew id 4471-2290, access code 998877\n',
    '/home/cadet/kit/shield.cfg': 'shield_power=5\n',
    '/home/cadet/kit/backup/shield.cfg.bak': 'shield_power=100\n',
  },
  users: [{ name: 'relay', fullName: 'Relay Console', password: 'admin' }],
  masks: { '/home/cadet/kit/secret.txt': { pattern: '\\d{4,}', replace: '****', output: '/home/cadet/kit/secret.masked.txt' } },
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
  sh(KIT_LAB, s, 'sudo passwd relay');
  sh(KIT_LAB, s, 'Sh1eld!Codexia#7');
  sh(KIT_LAB, s, 'Sh1eld!Codexia#7');
  assert.match(sh(KIT_LAB, s, 'login relay admin'), /wrong password/);
  assert.ok(checkOne(KIT_LAB, s, { type: 'login', user: 'relay', password: 'admin', expect: 'fail' }));
});

test('setfacl blocks a user from a file, mask redacts it, and both survive save/restore', () => {
  const s = createState(KIT_LAB);
  assert.match(sh(KIT_LAB, s, 'setfacl -m u:doom_intern:--- secret.txt'), /^$/);
  assert.ok(checkOne(KIT_LAB, s, { type: 'denied', file: '/home/cadet/kit/secret.txt', user: 'doom_intern' }));
  assert.match(sh(KIT_LAB, s, 'getfacl secret.txt'), /user:doom_intern:---/);
  assert.match(sh(KIT_LAB, s, 'ls -l secret.txt'), /^-rw-r--r--\+ /);
  sh(KIT_LAB, s, 'mask secret.txt');
  assert.ok(checkOne(KIT_LAB, s, { type: 'masked', file: '/home/cadet/kit/secret.masked.txt', mustNotMatch: '\\d{4,}' }));
  const again = restore(KIT_LAB, JSON.parse(JSON.stringify(snapshot(s))));
  assert.ok(checkOne(KIT_LAB, again, { type: 'denied', file: '/home/cadet/kit/secret.txt', user: 'doom_intern' }));
  assert.ok(checkOne(KIT_LAB, again, { type: 'masked', file: '/home/cadet/kit/secret.masked.txt', mustNotMatch: '\\d{4,}' }));
  sh(KIT_LAB, again, 'setfacl -x u:doom_intern secret.txt');
  assert.ok(!checkOne(KIT_LAB, again, { type: 'denied', file: '/home/cadet/kit/secret.txt', user: 'doom_intern' }));
  sh(KIT_LAB, again, 'setfacl -m u:cadet:--- secret.txt');
  assert.match(sh(KIT_LAB, again, 'cat secret.txt'), /Permission denied/);
});

test('cp restores a tampered file from backup, checked by same-content', () => {
  const s = createState(KIT_LAB);
  assert.ok(!checkOne(KIT_LAB, s, { type: 'same-content', a: '/home/cadet/kit/shield.cfg', b: '/home/cadet/kit/backup/shield.cfg.bak' }));
  assert.match(sh(KIT_LAB, s, 'cp backup/shield.cfg.bak shield.cfg'), /^$/);
  assert.ok(checkOne(KIT_LAB, s, { type: 'same-content', a: '/home/cadet/kit/shield.cfg', b: '/home/cadet/kit/backup/shield.cfg.bak' }));
  assert.match(sh(KIT_LAB, s, 'cp nothing.txt x.txt'), /cannot stat/);
  assert.match(sh(KIT_LAB, s, 'cp shield.cfg /etc/passwd'), /Permission denied/);
});

test('sha256sum fingerprints a file deterministically, and ordered checks command sequence', () => {
  const s = createState(KIT_LAB);
  const out1 = sh(KIT_LAB, s, 'sha256sum shield.cfg');
  const out2 = sh(KIT_LAB, s, 'sha256sum backup/shield.cfg.bak');
  assert.match(out1, /^[0-9a-f]{64} {2}shield\.cfg\n$/);
  assert.equal(out1.slice(0, 64), sh(KIT_LAB, s, 'cat shield.cfg | sha256sum').slice(0, 64), 'same content, same hash');
  assert.notEqual(out1.slice(0, 64), out2.slice(0, 64), 'different content, different hash');
  assert.match(sh(KIT_LAB, s, 'md5sum shield.cfg'), /^[0-9a-f]{32} {2}/);
  const order = { type: 'ordered', before: '^(sudo\\s+)?(sha256sum|sha1sum|md5sum)\\b', after: '^(sudo\\s+)?cp\\b' };
  const t = createState(KIT_LAB);
  sh(KIT_LAB, t, 'cp backup/shield.cfg.bak shield.cfg');
  sh(KIT_LAB, t, 'sha256sum shield.cfg');
  assert.ok(!checkOne(KIT_LAB, t, order), 'hashing after the restore is too late');
  sh(KIT_LAB, s, 'cp backup/shield.cfg.bak shield.cfg');
  assert.ok(checkOne(KIT_LAB, s, order));
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

test('findings.txt accepts free-form answers beyond the built-in CIA/asset shortcuts', () => {
  const lab = { ...KIT_LAB, classify: { 'control-1': 'x' } };
  const s = createState(lab);
  assert.equal(sh(lab, s, 'echo control-1 detect >> findings.txt'), '');
  assert.ok(checkOne(lab, s, { type: 'classified', item: 'control-1', as: 'detect' }));
});

test('> and >> write into files, and findings.txt records answers', () => {
  const lab = loadLab('c1-l1');
  const s = createState(lab);
  assert.equal(sh(lab, s, 'echo "event-1 C" >> findings.txt'), '');
  sh(lab, s, 'echo event-2 I >> findings.txt');
  assert.equal(sh(lab, s, 'cat findings.txt'), 'event-1 C\nevent-2 I\n');
  assert.equal(s.classified['event-1'], 'confidentiality');
  assert.equal(s.classified['event-2'], 'integrity');
  sh(lab, s, 'echo hello > notes.txt');
  sh(lab, s, 'echo again > notes.txt');
  assert.equal(sh(lab, s, 'cat notes.txt'), 'again\n');
  assert.match(sh(lab, s, 'echo event-3 >> findings.txt'), /write an answer after event-3/);
  assert.match(sh(lab, s, 'echo hi > /etc/passwd'), /Permission denied/);
  assert.match(sh(lab, s, 'echo hi >'), /put the file name right after/);
  assert.match(sh(lab, s, 'classify event-3 A'), /no classify command.*findings\.txt/);
  assert.equal(s.classified['event-3'], undefined);
});
