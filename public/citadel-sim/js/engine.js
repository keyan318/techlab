// Citadel Sim engine: a small, fake Windows machine for security labs.
// Pure logic with no DOM, so it runs in the browser and under `node --test`.
// Nothing here touches a real system: files, users and services are data from the lab JSON.

const DATE = '09/23/2026  02:13 AM';

const CATEGORY = {
  asset: 'asset', threat: 'threat', vulnerability: 'vulnerability', vuln: 'vulnerability', risk: 'risk',
  c: 'confidentiality', confidentiality: 'confidentiality',
  i: 'integrity', integrity: 'integrity',
  a: 'availability', availability: 'availability',
};

// ---------- paths ----------

export function normPath(p) {
  return p.replace(/\//g, '\\').replace(/\\+$/, '').toLowerCase();
}

export function resolvePath(cwd, input) {
  let p = (input || '').replace(/\//g, '\\').replace(/^"|"$/g, '');
  if (!p) return cwd;
  if (/^[a-z]:$/i.test(p)) p += '\\';
  const abs = /^[a-z]:\\/i.test(p) ? p : `${cwd.replace(/\\$/, '')}\\${p}`;
  const [drive, ...rest] = abs.split('\\');
  const parts = [];
  for (const seg of rest) {
    if (!seg || seg === '.') continue;
    if (seg === '..') parts.pop();
    else parts.push(seg);
  }
  return parts.length ? `${drive.toUpperCase()}\\${parts.join('\\')}` : `${drive.toUpperCase()}\\`;
}

// ---------- state ----------

export function createState(lab) {
  const files = {};
  for (const [path, content] of Object.entries(lab.files || {})) files[normPath(path)] = { path, content };
  return {
    cwd: lab.host?.cwd || 'C:\\Citadel',
    files,
    users: (lab.users || []).map((u) => ({ enabled: true, groups: [], ...u, changed: false })),
    services: (lab.services || []).map((s) => ({ running: true, ...s })),
    processes: (lab.processes || []).map((p) => ({ ...p })),
    read: [],
    classified: {},
    flags: [],
    history: [],
    loginAttempts: [],
    acl: {},
    opened: [],
    triaged: {},
  };
}

function findFile(state, path) {
  return state.files[normPath(path)] || null;
}

function isDir(state, path) {
  const key = normPath(path);
  if (/^[a-z]:$/.test(key)) return true;
  return Object.keys(state.files).some((k) => k.startsWith(key + '\\'));
}

function markRead(state, file) {
  const key = normPath(file.path);
  if (!state.read.includes(key)) state.read.push(key);
}

// ---------- commands ----------

const HELP = `Citadel Sim commands (Windows syntax). Commands are not case-sensitive.

  dir [path]                       list files in a folder
  cd <path>  |  cd ..              change folder
  type <file>                      show a file
  findstr /i <text> <file>         find lines that contain text
  whoami | hostname | ver | cls    who and where you are
  net user                         list accounts
  net user <name>                  details of one account
  net user <name> /active:no       disable an account
  net user <name> <new-password>   set a new password
  netstat -an                      listening ports
  tasklist | taskkill /im <name> /f     running programs

Lab tools (the crew's field kit):
  classify <item> <category>       record what something is:
                                   asset, threat, vulnerability, risk,
                                   C (confidentiality), I (integrity), A (availability),
                                   or any category the lab asks for
  login <user> <password>          try to log in as an account (Volt's Drill: test it)
  icacls <file> /deny <user>:F     deny a user access to a file
  icacls <file> /grant <user>:F    grant a user access to a file
  copy <src> <dst>                 copy a file, e.g. restore a backup over a tampered file
  certutil -hashfile <file> [algo] fingerprint a file (also: hash <file>)
  mask <file>                      write a redacted copy of a file, hiding sensitive patterns
  inbox                            list messages in the crew inbox
  open <id>                        read one inbox message
  report <id>                      flag an inbox message as phishing
  keep <id>                        mark an inbox message as legitimate
  caesar <encode|decode> <shift> <text>   shift-cipher a message
  xor <key> <text|hex>             XOR-cipher a message with a shared key (symmetric)
  hashcrack <hash>                 look up a hash in the crew's cracked-password list
  submit <flag>                    hand in a flag or answer you found, e.g. submit FLAG{...}
  mission                          show your current orders again
`;

export function runCommand(lab, state, line) {
  const trimmed = line.trim();
  if (!trimmed) return { output: '' };
  state.history.push(trimmed);
  if (state.history.length > 200) state.history.shift();
  const argv = tokenize(trimmed);
  const cmd = argv[0].toLowerCase().replace(/\.exe$/, '');
  const args = argv.slice(1);
  const host = lab.host?.name || 'CITADEL-CORE';
  const user = lab.host?.user || 'cadet';

  switch (cmd) {
    case 'help':
    case '/?':
      return { output: HELP };
    case 'cls':
    case 'clear':
      return { output: '', clear: true };
    case 'whoami':
      return { output: `${host.toLowerCase()}\\${user}\n` };
    case 'hostname':
      return { output: `${host}\n` };
    case 'ver':
      return { output: '\nMicrosoft Windows [Version 10.0.22631] (Citadel Sim)\n' };
    case 'mission':
      return { output: missionText(lab, state) };
    case 'dir':
      return { output: dir(state, args) };
    case 'cd':
    case 'chdir':
      return { output: cd(state, args) };
    case 'type':
    case 'cat':
      return { output: typeCmd(state, args, cmd) };
    case 'findstr':
      return { output: findstr(state, args) };
    case 'net':
      return { output: net(state, args, host) };
    case 'netstat':
      return { output: netstat(state) };
    case 'tasklist':
      return { output: tasklist(state) };
    case 'taskkill':
      return { output: taskkill(state, args) };
    case 'classify':
      return { output: classify(lab, state, args) };
    case 'login':
      return { output: login(state, args) };
    case 'icacls':
      return { output: icacls(state, args) };
    case 'copy':
    case 'xcopy':
      return { output: copyCmd(state, args) };
    case 'certutil':
      return { output: certutil(state, args) };
    case 'hash':
      return { output: certutil(state, ['-hashfile', ...args]) };
    case 'mask':
      return { output: mask(lab, state, args) };
    case 'inbox':
      return { output: inboxList(lab, state) };
    case 'open':
      return { output: inboxOpen(lab, state, args) };
    case 'report':
      return { output: triage(lab, state, args, 'report') };
    case 'keep':
      return { output: triage(lab, state, args, 'keep') };
    case 'caesar':
      return { output: caesarCmd(args) };
    case 'xor':
      return { output: xorCmd(args) };
    case 'hashcrack':
      return { output: hashcrack(lab, args) };
    case 'submit':
      return { output: submit(lab, state, args) };
    default:
      return {
        output:
          `'${argv[0]}' is not recognized as an internal or external command,\noperable program or batch file.\n` +
          (cmd === 'ls' ? '(This is Windows. Try: dir)\n' : "(Type 'help' to see the commands you can use.)\n"),
      };
  }
}

export function tokenize(line) {
  const out = [];
  let cur = '';
  let inQ = false;
  let has = false;
  for (const ch of line) {
    if (ch === '"') {
      inQ = !inQ;
      has = true;
    } else if (!inQ && /\s/.test(ch)) {
      if (has) out.push(cur);
      cur = '';
      has = false;
    } else {
      cur += ch;
      has = true;
    }
  }
  if (has) out.push(cur);
  return out;
}

function dir(state, args) {
  const target = resolvePath(state.cwd, args.find((a) => !a.startsWith('/')));
  if (!isDir(state, target)) {
    return findFile(state, target) ? `\n Directory of ${target}\n\nFile Not Found (that is a file; use type to read it)\n` : 'File Not Found\n';
  }
  const prefix = normPath(target) + '\\';
  const dirs = new Set();
  const files = [];
  for (const [key, f] of Object.entries(state.files)) {
    if (!key.startsWith(prefix)) continue;
    const rest = f.path.replace(/\//g, '\\').slice(prefix.length);
    const slash = rest.indexOf('\\');
    if (slash >= 0) dirs.add(rest.slice(0, slash));
    else files.push(f);
  }
  let s = `\n Directory of ${target}\n\n`;
  for (const d of [...dirs].sort()) s += `${DATE}    <DIR>          ${d}\n`;
  let bytes = 0;
  for (const f of files.sort((a, b) => a.path.localeCompare(b.path))) {
    const name = f.path.split('\\').pop();
    bytes += f.content.length;
    s += `${DATE}    ${String(f.content.length).padStart(14)} ${name}\n`;
  }
  s += `${String(files.length).padStart(16)} File(s) ${String(bytes).padStart(14)} bytes\n`;
  s += `${String(dirs.size).padStart(16)} Dir(s)\n`;
  return s;
}

function cd(state, args) {
  if (!args.length) return `${state.cwd}\n`;
  const target = resolvePath(state.cwd, args.join(' '));
  if (!isDir(state, target)) return 'The system cannot find the path specified.\n';
  state.cwd = target;
  return '';
}

function typeCmd(state, args, cmd) {
  if (!args.length) return 'The syntax of the command is incorrect.\n';
  const f = findFile(state, resolvePath(state.cwd, args.join(' ')));
  if (!f) return 'The system cannot find the file specified.\n';
  markRead(state, f);
  const note = cmd === 'cat' ? '(cat is Linux; on Windows it is type. Showing it anyway.)\n' : '';
  return note + f.content + (f.content.endsWith('\n') ? '' : '\n');
}

function findstr(state, args) {
  const flags = args.filter((a) => a.startsWith('/')).map((a) => a.toLowerCase());
  const rest = args.filter((a) => !a.startsWith('/'));
  if (rest.length < 2) return 'FINDSTR: Usage: findstr [/i] <text> <file>\n';
  const [text, ...fileParts] = rest;
  const f = findFile(state, resolvePath(state.cwd, fileParts.join(' ')));
  if (!f) return `FINDSTR: Cannot open ${fileParts.join(' ')}\n`;
  markRead(state, f);
  const ci = flags.includes('/i');
  const needle = ci ? text.toLowerCase() : text;
  const hits = f.content.split('\n').filter((l) => (ci ? l.toLowerCase() : l).includes(needle));
  return hits.length ? hits.join('\n') + '\n' : '';
}

function findUser(state, name) {
  return state.users.find((u) => u.name.toLowerCase() === (name || '').toLowerCase()) || null;
}

function net(state, args, host) {
  if ((args[0] || '').toLowerCase() !== 'user') return 'NetSim supports: net user ...\n';
  const name = args[1];
  if (!name) {
    const names = state.users.map((u) => u.name);
    let s = `\nUser accounts for \\\\${host}\n\n-------------------------------------------------------------------------------\n`;
    for (let i = 0; i < names.length; i += 3) s += names.slice(i, i + 3).map((n) => n.padEnd(25)).join('') + '\n';
    return s + 'The command completed successfully.\n';
  }
  const u = findUser(state, name);
  if (!u) return 'The user name could not be found.\n';
  const opt = args[2];
  if (!opt) {
    return (
      `User name                    ${u.name}\n` +
      `Full Name                    ${u.fullName || ''}\n` +
      `Account active               ${u.enabled ? 'Yes' : 'No'}\n` +
      `Password last set            ${u.changed ? 'just now' : u.passwordSet || 'never changed'}\n` +
      `Last logon                   ${u.lastLogon || 'Never'}\n` +
      `Local Group Memberships      ${u.groups.length ? u.groups.map((g) => '*' + g).join(' ') : '*Users'}\n` +
      'The command completed successfully.\n'
    );
  }
  const m = opt.match(/^\/active:(yes|no)$/i);
  if (m) {
    u.enabled = m[1].toLowerCase() === 'yes';
    return 'The command completed successfully.\n';
  }
  if (opt.startsWith('/')) return `NetSim supports: net user ${u.name} /active:no|yes  or  net user ${u.name} <new-password>\n`;
  const why = weakReason(opt, u.name);
  if (why) return `The password does not meet the password policy requirements: ${why}\n`;
  u.password = opt;
  u.changed = true;
  return 'The command completed successfully.\n';
}

export function weakReason(pw, user) {
  if (pw.length < 12) return 'use at least 12 characters.';
  if (!/[a-z]/.test(pw) || !/[A-Z]/.test(pw) || !/\d/.test(pw) || !/[^A-Za-z0-9]/.test(pw))
    return 'mix upper and lower case letters, a number and a symbol.';
  if (user && pw.toLowerCase().includes(user.toLowerCase())) return 'do not put the account name in the password.';
  if (/password|admin|12345|qwerty|citadel/i.test(pw)) return 'avoid common words that attackers guess first.';
  return null;
}

function netstat(state) {
  let s = '\nActive Connections\n\n  Proto  Local Address          Foreign Address        State\n';
  for (const sv of state.services.filter((x) => x.running)) {
    s += `  ${(sv.proto || 'TCP').padEnd(7)}${`0.0.0.0:${sv.port}`.padEnd(23)}${'0.0.0.0:0'.padEnd(23)}LISTENING\n`;
  }
  return s;
}

function tasklist(state) {
  let s = '\nImage Name                     PID Session Name        Mem Usage\n========================= ======== ================ ============\n';
  for (const p of state.processes.filter((x) => !x.killed)) {
    s += `${p.name.padEnd(26)}${String(p.pid).padStart(8)} ${'Console'.padEnd(17)}${(p.mem || '12,480 K').padStart(12)}\n`;
  }
  return s;
}

function taskkill(state, args) {
  const i = args.findIndex((a) => a.toLowerCase() === '/im');
  const name = i >= 0 ? args[i + 1] : null;
  if (!name) return 'ERROR: Invalid syntax. Usage: taskkill /im <name> /f\n';
  const p = state.processes.find((x) => !x.killed && x.name.toLowerCase() === name.toLowerCase());
  if (!p) return `ERROR: The process "${name}" not found.\n`;
  p.killed = true;
  for (const sv of state.services) if (sv.process && sv.process.toLowerCase() === p.name.toLowerCase()) sv.running = false;
  return `SUCCESS: The process "${p.name}" with PID ${p.pid} has been terminated.\n`;
}

function classify(lab, state, args) {
  const items = lab.classify || {};
  if (args.length < 2) {
    const names = Object.keys(items);
    return 'Usage: classify <item> <category>\n' + (names.length ? `Items in this lab: ${names.join(', ')}\n` : '');
  }
  const item = args[0].toLowerCase();
  const raw = args[1].toLowerCase();
  const cat = CATEGORY[raw] || raw;
  if (!(item in items)) return `Unknown item "${args[0]}". Items in this lab: ${Object.keys(items).join(', ')}\n`;
  state.classified[item] = cat;
  return `Recorded: ${item} = ${cat}.\n`;
}

function submit(lab, state, args) {
  const flag = args.join(' ').trim();
  if (!flag) return 'Usage: submit <answer>\n';
  const valid = (lab.flags || []).some((f) => f === flag);
  if (!valid) return 'That is not right. Check the exact spelling.\n';
  if (!state.flags.includes(flag)) state.flags.push(flag);
  return 'Flag accepted.\n';
}

function login(state, args) {
  if (args.length < 2) return 'Usage: login <user> <password>\n';
  const name = args[0];
  const pw = args.slice(1).join(' ');
  const u = findUser(state, name);
  const success = !!u && u.enabled && u.password === pw;
  state.loginAttempts.push({ user: (name || '').toLowerCase(), password: pw, success });
  if (!u) return 'Login failed: unknown user name.\n';
  if (!u.enabled) return 'Login failed: this account is disabled.\n';
  return success ? `Login successful. Welcome, ${u.fullName || u.name}.\n` : 'Login failed: incorrect password.\n';
}

function icacls(state, args) {
  if (!args.length) return 'ICACLS: no file specified.\n';
  const denyIdx = args.findIndex((a) => a.toLowerCase() === '/deny');
  const grantIdx = args.findIndex((a) => a.toLowerCase() === '/grant');
  const filePart = args.slice(0, denyIdx >= 0 ? denyIdx : grantIdx >= 0 ? grantIdx : args.length).join(' ');
  const f = findFile(state, resolvePath(state.cwd, filePart));
  if (!f) return `ICACLS: ${filePart}: The system cannot find the file specified.\n`;
  const key = normPath(f.path);
  state.acl[key] = state.acl[key] || { denied: [] };
  if (denyIdx >= 0) {
    const user = (args[denyIdx + 1] || '').split(':')[0].toLowerCase();
    if (!user) return 'ICACLS: /deny requires <user>:<perm>\n';
    if (!state.acl[key].denied.includes(user)) state.acl[key].denied.push(user);
    return `processed file: ${f.path}\nSuccessfully processed 1 files; Failed processing 0 files\n`;
  }
  if (grantIdx >= 0) {
    const user = (args[grantIdx + 1] || '').split(':')[0].toLowerCase();
    state.acl[key].denied = state.acl[key].denied.filter((u) => u !== user);
    return `processed file: ${f.path}\nSuccessfully processed 1 files; Failed processing 0 files\n`;
  }
  return 'ICACLS: use /deny <user>:F or /grant <user>:F\n';
}

function copyCmd(state, args) {
  if (args.length < 2) return 'The syntax of the command is incorrect.\n';
  const src = findFile(state, resolvePath(state.cwd, args[0]));
  if (!src) return 'The system cannot find the file specified.\n';
  const dstPath = resolvePath(state.cwd, args[1]);
  state.files[normPath(dstPath)] = { path: dstPath, content: src.content };
  markRead(state, src);
  return '        1 file(s) copied.\n';
}

function fakeHash(content) {
  let h = 2166136261;
  for (let i = 0; i < content.length; i++) {
    h ^= content.charCodeAt(i);
    h = Math.imul(h, 16777619);
  }
  const hex = (h >>> 0).toString(16).padStart(8, '0').toUpperCase();
  return hex.repeat(8);
}

function certutil(state, args) {
  const i = args.findIndex((a) => a.toLowerCase() === '-hashfile');
  const filePart = i >= 0 ? args[i + 1] : args[0];
  const algo = (i >= 0 ? args[i + 2] : args[1]) || 'SHA256';
  if (!filePart) return 'Usage: certutil -hashfile <file> [SHA256]\n';
  const f = findFile(state, resolvePath(state.cwd, filePart));
  if (!f) return 'CertUtil: -hashfile command FAILED: 0x80070002 (The system cannot find the file specified.)\n';
  markRead(state, f);
  return `${algo.toUpperCase()} hash of ${f.path}:\n${fakeHash(f.content)}\nCertUtil: -hashfile command completed successfully.\n`;
}

function mask(lab, state, args) {
  if (!args.length) return 'Usage: mask <file>\n';
  const f = findFile(state, resolvePath(state.cwd, args.join(' ')));
  if (!f) return 'The system cannot find the file specified.\n';
  const key = normPath(f.path);
  const rule = (lab.masks || {})[key];
  if (!rule) return 'Nothing in this lab says how to mask that file.\n';
  markRead(state, f);
  const masked = f.content.replace(new RegExp(rule.pattern, 'g'), rule.replace);
  const outPath = rule.output || f.path.replace(/(\.[A-Za-z0-9]+)?$/, '.masked$1');
  state.files[normPath(outPath)] = { path: outPath, content: masked };
  return `Masked. Wrote ${outPath}\n\n${masked}\n`;
}

function inboxList(lab, state) {
  const msgs = lab.inbox || [];
  if (!msgs.length) return 'Inbox is empty.\n';
  let s = '\nID    From                        Subject\n';
  for (const m of msgs) {
    const status = state.triaged[m.id] ? `[${state.triaged[m.id]}] ` : '';
    s += `${m.id.padEnd(6)}${(m.from || '').padEnd(28)}${status}${m.subject}\n`;
  }
  return s;
}

function inboxOpen(lab, state, args) {
  const id = args[0];
  const m = (lab.inbox || []).find((x) => x.id === id);
  if (!m) return `No such message "${id || ''}". Type inbox to list them.\n`;
  if (!state.opened.includes(id)) state.opened.push(id);
  return `\nFrom: ${m.from}\nSubject: ${m.subject}\n\n${m.body}\n`;
}

function triage(lab, state, args, action) {
  const id = args[0];
  const m = (lab.inbox || []).find((x) => x.id === id);
  if (!m) return `No such message "${id || ''}". Type inbox to list them.\n`;
  state.triaged[id] = action;
  return `Marked ${id} as ${action}.\n`;
}

function caesarShift(text, shift) {
  return text.replace(/[a-zA-Z]/g, (c) => {
    const base = c <= 'Z' ? 65 : 97;
    return String.fromCharCode((((c.charCodeAt(0) - base + shift) % 26) + 26) % 26 + base);
  });
}

function caesarCmd(args) {
  const op = (args[0] || '').toLowerCase();
  const shift = parseInt(args[1], 10);
  const text = args.slice(2).join(' ');
  if (!['encode', 'decode'].includes(op) || Number.isNaN(shift) || !text) return 'Usage: caesar <encode|decode> <shift> <text>\n';
  return `${caesarShift(text, op === 'decode' ? -shift : shift)}\n`;
}

function toHex(str) {
  let s = '';
  for (let i = 0; i < str.length; i++) s += str.charCodeAt(i).toString(16).padStart(2, '0');
  return s;
}

function fromHex(hex) {
  let s = '';
  for (let i = 0; i < hex.length; i += 2) s += String.fromCharCode(parseInt(hex.slice(i, i + 2), 16));
  return s;
}

function xorCmd(args) {
  if (args.length < 2) return 'Usage: xor <key> <text-or-hex>\n';
  const key = args[0];
  const rest = args.slice(1).join(' ');
  // Hex input means "decode": the result is shown as plain text.
  // Anything else means "encode": the result is shown as hex, so it is
  // never ambiguous with plain text, no matter what bytes come out.
  const looksHex = /^[0-9a-fA-F]+$/.test(rest) && rest.length % 2 === 0;
  const input = looksHex ? fromHex(rest) : rest;
  let out = '';
  for (let i = 0; i < input.length; i++) out += String.fromCharCode(input.charCodeAt(i) ^ key.charCodeAt(i % key.length));
  return `${looksHex ? out : toHex(out)}\n`;
}

function hashcrack(lab, args) {
  const h = args[0];
  if (!h) return 'Usage: hashcrack <hash>\n';
  const found = (lab.crackable || {})[h];
  if (!found) return `No match in the crew's wordlist for ${h}.\n`;
  return `Match found: ${h} = "${found}"\n`;
}

// ---------- objectives ----------

export function checkOne(lab, state, check) {
  switch (check.type) {
    case 'read':
      return state.read.includes(normPath(check.file));
    case 'classified':
      return state.classified[check.item.toLowerCase()] === check.as;
    case 'flag':
      return state.flags.includes(check.value);
    case 'user-disabled': {
      const u = findUser(state, check.user);
      return !!u && !u.enabled;
    }
    case 'user-enabled': {
      const u = findUser(state, check.user);
      return !!u && u.enabled;
    }
    case 'password-changed': {
      const u = findUser(state, check.user);
      return !!u && u.changed && !weakReason(u.password, u.name);
    }
    case 'process-killed':
      return state.processes.some((p) => p.name.toLowerCase() === check.name.toLowerCase() && p.killed);
    case 'port-closed':
      return !state.services.some((s) => s.port === check.port && s.running);
    case 'ran':
      return state.history.some((h) => new RegExp(check.pattern, 'i').test(h));
    case 'ordered': {
      const beforeIdx = state.history.findIndex((h) => new RegExp(check.before, 'i').test(h));
      const afterIdx = state.history.findIndex((h) => new RegExp(check.after, 'i').test(h));
      return beforeIdx >= 0 && afterIdx >= 0 && beforeIdx < afterIdx;
    }
    case 'login': {
      const atts = state.loginAttempts.filter((a) => a.user === check.user.toLowerCase() && a.password === check.password);
      return check.expect === 'fail' ? atts.some((a) => !a.success) : atts.some((a) => a.success);
    }
    case 'denied': {
      const acl = state.acl[normPath(check.file)];
      return !!acl && acl.denied.includes(check.user.toLowerCase());
    }
    case 'masked': {
      const f = state.files[normPath(check.file)];
      if (!f) return false;
      return check.mustNotMatch ? !new RegExp(check.mustNotMatch).test(f.content) : true;
    }
    case 'same-content': {
      const fa = state.files[normPath(check.a)];
      const fb = state.files[normPath(check.b)];
      if (!fa || !fb) return false;
      const same = fa.content === fb.content;
      return check.expect === 'different' ? !same : same;
    }
    case 'triaged':
      return state.triaged[check.id] === check.as;
    default:
      return false;
  }
}

export function evaluate(lab, state) {
  return lab.steps.map((step) => step.objectives.map((o) => ({ id: o.id, pass: checkOne(lab, state, o.check) })));
}

// The first step with an unmet objective (steps unlock in order), or steps.length when all pass.
export function currentStep(lab, state) {
  const res = evaluate(lab, state);
  const i = res.findIndex((r) => r.some((o) => !o.pass));
  return i < 0 ? lab.steps.length : i;
}

export function progress(lab, state) {
  const flat = evaluate(lab, state).flat();
  return { passed: flat.filter((o) => o.pass).length, total: flat.length };
}

export function missionText(lab, state) {
  const i = currentStep(lab, state);
  if (i >= lab.steps.length) return 'Every objective is clear. The attack is repelled.\n';
  const st = lab.steps[i];
  const res = evaluate(lab, state)[i];
  let s = `\nStep ${i + 1}: ${st.title}\n${st.body.replace(/`/g, '')}\n\n`;
  st.objectives.forEach((o, k) => (s += `  [${res[k].pass ? 'x' : ' '}] ${o.label}\n`));
  return s;
}

// ---------- save / restore (only the student's changes, never the lab itself) ----------

export function snapshot(state) {
  return {
    v: 1,
    cwd: state.cwd,
    users: state.users.map((u) => ({ name: u.name, enabled: u.enabled, changed: u.changed, password: u.changed ? u.password : undefined })),
    killed: state.processes.filter((p) => p.killed).map((p) => p.name),
    stopped: state.services.filter((s) => !s.running).map((s) => s.name),
    read: state.read,
    classified: state.classified,
    flags: state.flags,
    history: state.history.slice(-50),
    loginAttempts: state.loginAttempts.slice(-50),
    acl: state.acl,
    opened: state.opened,
    triaged: state.triaged,
    files: Object.fromEntries(Object.entries(state.files).map(([k, f]) => [k, { path: f.path, content: f.content }])),
  };
}

export function restore(lab, saved) {
  const state = createState(lab);
  if (!saved || saved.v !== 1) return state;
  if (typeof saved.cwd === 'string') state.cwd = saved.cwd;
  for (const su of saved.users || []) {
    const u = findUser(state, su.name);
    if (u) Object.assign(u, { enabled: !!su.enabled, changed: !!su.changed, ...(su.changed ? { password: su.password } : {}) });
  }
  for (const p of state.processes) if ((saved.killed || []).includes(p.name)) p.killed = true;
  for (const s of state.services) if ((saved.stopped || []).includes(s.name)) s.running = false;
  state.read = Array.isArray(saved.read) ? saved.read.filter((x) => typeof x === 'string') : [];
  state.classified = saved.classified && typeof saved.classified === 'object' ? { ...saved.classified } : {};
  state.flags = Array.isArray(saved.flags) ? saved.flags.filter((x) => typeof x === 'string') : [];
  state.history = Array.isArray(saved.history) ? saved.history.filter((x) => typeof x === 'string') : [];
  state.loginAttempts = Array.isArray(saved.loginAttempts)
    ? saved.loginAttempts.filter((a) => a && typeof a.user === 'string' && typeof a.password === 'string')
    : [];
  state.acl = saved.acl && typeof saved.acl === 'object' ? { ...saved.acl } : {};
  state.opened = Array.isArray(saved.opened) ? saved.opened.filter((x) => typeof x === 'string') : [];
  state.triaged = saved.triaged && typeof saved.triaged === 'object' ? { ...saved.triaged } : {};
  for (const [k, f] of Object.entries(saved.files || {})) {
    if (f && typeof f.path === 'string' && typeof f.content === 'string') state.files[k] = { path: f.path, content: f.content };
  }
  return state;
}
