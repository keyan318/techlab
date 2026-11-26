// Citadel Sim engine: a small, fake Linux machine for security labs.
// Pure logic with no DOM, so it runs in the browser and under `node --test`.
// Nothing here touches a real system: files, users and services are data from the lab JSON.

const DATE = 'Sep 23 02:13';
const NOW = 'Wed Sep 23 02:13:00 +0000 2026';
const KERNEL = '6.8.0-citadel';

const CATEGORY = {
  asset: 'asset', threat: 'threat', vulnerability: 'vulnerability', vuln: 'vulnerability', risk: 'risk',
  c: 'confidentiality', confidentiality: 'confidentiality',
  i: 'integrity', integrity: 'integrity',
  a: 'availability', availability: 'availability',
};

const GROUP_IDS = { root: 0, adm: 4, sudo: 27, users: 100, guests: 1500 };

// ---------- paths ----------

// An absolute, tidy path: no //, no trailing /, no . or .. parts. Case-sensitive, like Linux.
export function normPath(p) {
  const parts = [];
  for (const seg of String(p || '').split('/')) {
    if (!seg || seg === '.') continue;
    if (seg === '..') parts.pop();
    else parts.push(seg);
  }
  return '/' + parts.join('/');
}

export function resolvePath(cwd, input, home = '/root') {
  let p = input || '';
  if (!p) return cwd;
  if (p === '~' || p.startsWith('~/')) p = home + p.slice(1);
  return normPath(p.startsWith('/') ? p : `${cwd}/${p}`);
}

// What the prompt shows for a folder: the home folder is ~.
export function displayPath(path, home) {
  if (path === home) return '~';
  if (path.startsWith(home + '/')) return '~' + path.slice(home.length);
  return path;
}

export function promptFor(state) {
  if (state.pending) return state.pending.prompt;
  return `${state.user}@${state.host}:${displayPath(state.cwd, state.home)}$ `;
}

const dirname = (p) => p.slice(0, p.lastIndexOf('/')) || '/';
const basename = (p) => p.slice(p.lastIndexOf('/') + 1);

// ---------- state ----------

export function createState(lab) {
  const user = lab.host?.user || 'cadet';
  const home = `/home/${user}`;
  const files = {};
  for (const [path, content] of Object.entries(lab.files || {})) {
    const key = normPath(path);
    files[key] = { path: key, content };
  }
  return {
    user,
    host: (lab.host?.name || 'citadel-core').toLowerCase(),
    home,
    cwd: lab.host?.cwd ? normPath(lab.host.cwd) : home,
    oldCwd: null,
    files,
    users: (lab.users || []).map((u) => ({ enabled: true, groups: ['users'], ...u, changed: false })),
    services: (lab.services || []).map((s) => ({ running: true, ...s })),
    processes: (lab.processes || []).map((p) => ({ ...p })),
    read: [],
    classified: {},
    flags: [],
    history: [],
    loginAttempts: [],
    acl: {},
    modes: {},
    opened: [],
    triaged: {},
    pending: null,
  };
}

const VIRTUAL_FILES = ['/etc/hostname', '/etc/passwd', '/etc/shadow'];

function systemDirs(state) {
  return new Set(['/', '/etc', '/home', '/root', '/tmp', state.home]);
}

function knownPaths(state) {
  return [...Object.keys(state.files), ...VIRTUAL_FILES, ...systemDirs(state)];
}

function virtualFile(state, abs) {
  if (abs === '/etc/hostname') return { path: abs, content: `${state.host}\n`, virtual: true };
  if (abs === '/etc/passwd') return { path: abs, content: passwdText(state), virtual: true };
  if (abs === '/etc/shadow') return { path: abs, content: shadowText(state), virtual: true };
  return null;
}

function getFile(state, abs) {
  return state.files[abs] || virtualFile(state, abs);
}

function isDir(state, abs) {
  if (systemDirs(state).has(abs)) return true;
  const pre = abs === '/' ? '/' : abs + '/';
  return knownPaths(state).some((k) => k.startsWith(pre));
}

function children(state, abs) {
  const pre = abs === '/' ? '/' : abs + '/';
  const dirs = systemDirs(state);
  const found = new Map();
  for (const k of knownPaths(state)) {
    if (!k.startsWith(pre) || k === abs) continue;
    const rest = k.slice(pre.length);
    if (!rest) continue;
    const slash = rest.indexOf('/');
    const name = slash < 0 ? rest : rest.slice(0, slash);
    const dir = slash >= 0 || dirs.has(k);
    found.set(name, found.get(name) || dir);
  }
  return [...found].sort(([a], [b]) => a.localeCompare(b)).map(([name, dir]) => ({ name, dir }));
}

function ownerOf(state, abs) {
  return abs === state.home || abs.startsWith(state.home + '/') ? state.user : 'root';
}

function modeOf(state, abs, dir) {
  if (state.modes[abs]) return state.modes[abs];
  if (abs === '/etc/shadow') return 'rw-r-----';
  if (abs === '/root') return 'rwx------';
  return dir ? 'rwxr-xr-x' : 'rw-r--r--';
}

function canRead(state, file, root) {
  if (root) return true;
  if (file.path === '/etc/shadow') return false;
  const acl = state.acl[file.path];
  if (acl && acl.denied.includes(state.user.toLowerCase())) return false;
  const owner = ownerOf(state, file.path) === state.user;
  return modeOf(state, file.path, false)[owner ? 0 : 6] === 'r';
}

function canWrite(state, abs, root) {
  if (root) return true;
  if (ownerOf(state, abs) !== state.user) return false;
  const acl = state.acl[abs];
  if (acl && acl.denied.includes(state.user.toLowerCase())) return false;
  return !state.files[abs] || modeOf(state, abs, false)[1] === 'w';
}

function markRead(state, file) {
  if (file.virtual) return;
  if (!state.read.includes(file.path)) state.read.push(file.path);
}

// A friendly nudge under "No such file or directory".
function pathHint(state, arg) {
  if (arg.includes('\\')) return `(Linux paths use / not \\. Try: ${arg.replace(/\\/g, '/')})\n`;
  const abs = resolvePath(state.cwd, arg, state.home);
  const base = basename(abs).toLowerCase();
  const hit = children(state, dirname(abs)).find((c) => c.name.toLowerCase() === base);
  return hit ? `(Linux is case-sensitive. It is called ${hit.name})\n` : '';
}

// Opens a file named on the command line, or explains why it can't.
function openFile(ctx, arg, cmd) {
  const { state, root } = ctx;
  const abs = resolvePath(state.cwd, arg, state.home);
  const f = getFile(state, abs);
  if (!f && isDir(state, abs)) return { err: `${cmd}: ${arg}: Is a directory\n` };
  if (!f) return { err: `${cmd}: ${arg}: No such file or directory\n` + pathHint(state, arg) };
  if (!canRead(state, f, root)) return { err: `${cmd}: ${arg}: Permission denied\n` + sudoHint(ctx) };
  markRead(state, f);
  return { file: f };
}

const withNl = (s) => (s && !s.endsWith('\n') ? s + '\n' : s);
const sudoHint = (ctx) => (ctx.root ? '' : `(That needs admin rights. Try: sudo ${ctx.line})\n`);

// ---------- accounts ----------

function findUser(state, name) {
  return state.users.find((u) => u.name.toLowerCase() === (name || '').toLowerCase()) || null;
}

function uidOf(state, name) {
  if (name === 'root') return 0;
  if (name === state.user) return 1000;
  const others = state.users.filter((u) => u.name !== state.user);
  const i = others.findIndex((u) => u.name === name);
  return i < 0 ? 1000 : 1001 + i;
}

function groupsOf(state, name) {
  const u = findUser(state, name);
  const groups = u ? [...u.groups] : ['users'];
  if (name === state.user && !groups.includes('sudo')) groups.push('sudo');
  return groups;
}

function passwdText(state) {
  let s = 'root:x:0:0:root:/root:/bin/bash\n';
  const names = [state.user, ...state.users.map((u) => u.name).filter((n) => n !== state.user)];
  for (const name of names) {
    const u = findUser(state, name);
    const id = uidOf(state, name);
    s += `${name}:x:${id}:${id}:${u?.fullName || ''}:/home/${name}:${u?.shell || '/bin/bash'}\n`;
  }
  return s;
}

function shadowText(state) {
  let s = 'root:*:20354:0:99999:7:::\n';
  for (const u of state.users) {
    const hash = `$6$citadel$${fakeHash(u.changed ? u.password : u.password || u.name, 'sha512', 43)}`;
    s += `${u.name}:${u.enabled ? '' : '!'}${hash}:20354:0:99999:7:::\n`;
  }
  return s;
}

function isoDate(value) {
  const m = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(value || '');
  return m ? `${m[3]}-${m[1]}-${m[2]}` : value || 'never';
}

export function weakReason(pw, user) {
  if (pw.length < 12) return 'use at least 12 characters.';
  if (!/[a-z]/.test(pw) || !/[A-Z]/.test(pw) || !/\d/.test(pw) || !/[^A-Za-z0-9]/.test(pw))
    return 'mix upper and lower case letters, a number and a symbol.';
  if (user && pw.toLowerCase().includes(user.toLowerCase())) return 'do not put the account name in the password.';
  if (/password|admin|12345|qwerty|citadel/i.test(pw)) return 'avoid common words that attackers guess first.';
  return null;
}

// The two questions `passwd` asks. The answers never go into the history.
function answerPrompt(state, line) {
  const p = state.pending;
  if (p.step === 1) {
    const why = weakReason(line, p.user);
    if (why) {
      state.pending = null;
      return { output: `BAD PASSWORD: ${why}\npasswd: password unchanged\n` };
    }
    p.first = line;
    p.step = 2;
    p.prompt = 'Retype new password: ';
    return { output: '', prompt: p.prompt, secret: true };
  }
  state.pending = null;
  if (line !== p.first) {
    return { output: 'Sorry, passwords do not match.\npasswd: Authentication token manipulation error\npasswd: password unchanged\n' };
  }
  const u = findUser(state, p.user);
  u.password = line;
  u.changed = true;
  return { output: 'passwd: password updated successfully\n' };
}

// ---------- commands ----------

const HELP = `Citadel Sim commands (Linux). Commands and file names are case-sensitive.
Press Tab to finish a command or file name, and the up arrow for the last command.

Look around
  ls [-l] [-a] [folder]              list files (-l shows permissions, -a hidden ones)
  cd <folder>  |  cd ..  |  cd ~     change folder
  pwd                                show which folder you are in
  cat <file>                         show a file
  head | tail [-n 5] <file>          first or last lines of a file
  grep [-i] [-n] [-r] <text> <file>  find lines that contain text (-i: any case)
  <command> | grep <text>            send one command's output into another
  echo <text> >> <file>              add a line to the end of a file (> replaces the file)
  whoami | id | hostname | uname -a | history | clear

Accounts (admin commands need sudo in front)
  cat /etc/passwd                    list every account
  id <user>  |  lastlog              one account's groups  |  last logins
  sudo passwd -S <user>              password status (P = usable, L = locked)
  sudo usermod -L <user>             lock (disable) an account   (-U unlocks it)
  sudo passwd <user>                 set a new password (it asks twice, typing stays hidden)

Processes and network
  ps aux                             running programs and their PIDs
  sudo ss -tlnp                      listening ports and the program behind each
  sudo kill <PID>                    stop a program by its PID
  sudo pkill <name>                  stop a program by its name

Files and permissions
  cp <from> <to>                     copy a file, e.g. restore a backup over a tampered file
  setfacl -m u:<user>:--- <file>     block one user from a file   (getfacl <file> shows it)
  chmod <mode> <file>                change permissions, e.g. chmod 600 secret.txt
  sha256sum <file>                   fingerprint a file (also md5sum, sha1sum)

Writing down your answers
  echo <item> <answer> >> findings.txt   one answer per line, e.g. echo event-1 C >> findings.txt
                                         answers: asset, threat, vulnerability, risk,
                                         C (confidentiality), I (integrity), A (availability),
                                         or any answer the lab asks for
  cat findings.txt                       check what you wrote (a new line fixes an old answer)

Lab tools (the crew's field kit)
  login <user> <password>            try to log in as an account (Volt's Drill: test it)
  mask <file>                        write a redacted copy of a file, hiding sensitive patterns
  inbox                              list messages in the crew inbox
  open <id>                          read one inbox message
  report <id>                        flag an inbox message as phishing
  keep <id>                          mark an inbox message as legitimate
  caesar <encode|decode> <shift> <text>   shift-cipher a message
  xor <key> <text|hex>               XOR-cipher a message with a shared key (symmetric)
  hashcrack <hash>                   look up a hash in the crew's cracked-password list
  submit <flag>                      hand in a flag or answer you found, e.g. submit FLAG{...}
  mission                            show your current orders again
`;

// Windows habits, and what to type on Linux instead.
const WINDOWS = {
  dir: 'ls',
  type: 'cat <file>',
  findstr: 'grep <text> <file>',
  net: 'cat /etc/passwd  |  sudo usermod -L <user>  |  sudo passwd <user>',
  tasklist: 'ps aux',
  taskkill: 'sudo kill <PID>  or  sudo pkill <name>',
  icacls: 'setfacl -m u:<user>:--- <file>',
  copy: 'cp <from> <to>',
  xcopy: 'cp <from> <to>',
  certutil: 'sha256sum <file>',
  cls: 'clear',
  ver: 'uname -a',
  ipconfig: 'sudo ss -tlnp (to see open ports)',
  chdir: 'cd',
  del: 'rm (not needed in these labs)',
  move: 'mv (not needed in these labs)',
  hash: 'sha256sum <file>',
};

// Real Linux commands this training computer doesn't have.
const MISSING = {
  top: 'ps aux', htop: 'ps aux', nano: 'cat <file> (this computer has no editor)',
  vi: 'cat <file> (this computer has no editor)', vim: 'cat <file> (this computer has no editor)',
  less: 'cat <file>', more: 'cat <file>', find: 'ls -l or grep -r <text> .', su: 'sudo <command>',
  userdel: 'sudo usermod -L <user> (lock it, keep it for the investigation)',
  deluser: 'sudo usermod -L <user> (lock it, keep it for the investigation)',
  ip: 'sudo ss -tlnp (to see open ports)', ifconfig: 'sudo ss -tlnp (to see open ports)',
};

const COMMANDS = {
  help: () => HELP,
  man: (ctx) => `No manual entry for ${ctx.args[0] || 'that'}. Type help to see every command on this computer.\n`,
  clear: () => ({ output: '', clear: true }),
  reset: () => ({ output: '', clear: true }),
  whoami: (ctx) => `${ctx.root ? 'root' : ctx.state.user}\n`,
  hostname: (ctx) => `${ctx.state.host}\n`,
  uname: (ctx) => (ctx.args.some((a) => /^-\w*a/.test(a))
    ? `Linux ${ctx.state.host} ${KERNEL} #1 SMP PREEMPT_DYNAMIC x86_64 GNU/Linux\n`
    : ctx.args.includes('-r') ? `${KERNEL}\n` : 'Linux\n'),
  id: idCmd,
  pwd: (ctx) => `${ctx.state.cwd}\n`,
  cd: cd,
  ls: ls,
  cat: cat,
  head: (ctx) => headTail(ctx, 'head'),
  tail: (ctx) => headTail(ctx, 'tail'),
  grep: grep,
  wc: wc,
  sort: (ctx) => textFilter(ctx, 'sort', (lines) => [...lines].sort()),
  uniq: (ctx) => textFilter(ctx, 'uniq', (lines) => lines.filter((l, i) => l !== lines[i - 1])),
  echo: (ctx) => `${ctx.args.join(' ')}\n`,
  history: (ctx) => ctx.state.history.map((h, i) => `${String(i + 1).padStart(5)}  ${h}`).join('\n') + '\n',
  exit: () => 'This is a training computer: leave the lab with the button above the mission.\n',
  logout: () => 'This is a training computer: leave the lab with the button above the mission.\n',
  getent: (ctx) => (ctx.args[0] === 'passwd' ? passwdText(ctx.state) : 'Usage: getent passwd\n'),
  lastlog: lastlog,
  passwd: passwdCmd,
  usermod: usermod,
  ps: ps,
  kill: kill,
  pkill: (ctx) => pkill(ctx, false),
  killall: (ctx) => pkill(ctx, true),
  ss: (ctx) => sockets(ctx, 'ss'),
  netstat: (ctx) => sockets(ctx, 'netstat'),
  cp: cp,
  chmod: chmod,
  setfacl: setfacl,
  getfacl: getfacl,
  sha256sum: (ctx) => hashsum(ctx, 'sha256', 64),
  sha1sum: (ctx) => hashsum(ctx, 'sha1', 40),
  md5sum: (ctx) => hashsum(ctx, 'md5', 32),
  login: (ctx) => login(ctx.state, ctx.args),
  mask: (ctx) => mask(ctx.lab, ctx.state, ctx.args),
  inbox: (ctx) => inboxList(ctx.lab, ctx.state),
  open: (ctx) => inboxOpen(ctx.lab, ctx.state, ctx.args),
  report: (ctx) => triage(ctx.lab, ctx.state, ctx.args, 'report'),
  keep: (ctx) => triage(ctx.lab, ctx.state, ctx.args, 'keep'),
  caesar: (ctx) => caesarCmd(ctx.args),
  xor: (ctx) => xorCmd(ctx.args),
  hashcrack: (ctx) => hashcrack(ctx.lab, ctx.args),
  submit: (ctx) => submit(ctx.lab, ctx.state, ctx.args),
  mission: (ctx) => missionText(ctx.lab, ctx.state),
};

export const COMMAND_NAMES = Object.keys(COMMANDS).sort();

// Splits a command line into words and operators (| ; && || > < &), honouring '...' and "..." quotes.
function lex(line) {
  const out = [];
  let cur = '';
  let has = false;
  let quote = null;
  const push = () => {
    if (has) out.push({ w: cur });
    cur = '';
    has = false;
  };
  for (let i = 0; i < line.length; i++) {
    const ch = line[i];
    if (quote) {
      if (ch === quote) quote = null;
      else cur += ch;
      continue;
    }
    if (ch === '"' || ch === "'") {
      quote = ch;
      has = true;
    } else if (ch === '\\' && line[i + 1] === ' ') {
      cur += ' ';
      has = true;
      i++;
    } else if (/\s/.test(ch)) {
      push();
    } else if (ch === '|' || ch === '&') {
      push();
      if (line[i + 1] === ch) {
        out.push({ op: ch + ch });
        i++;
      } else out.push({ op: ch });
    } else if (ch === ';' || ch === '<') {
      push();
      out.push({ op: ch });
    } else if (ch === '>') {
      push();
      if (line[i + 1] === '>') {
        out.push({ op: '>>' });
        i++;
      } else out.push({ op: '>' });
    } else {
      cur += ch;
      has = true;
    }
  }
  push();
  return out;
}

export function tokenize(line) {
  return lex(line).filter((t) => 'w' in t).map((t) => t.w);
}

export function runCommand(lab, state, line) {
  if (state.pending) return answerPrompt(state, line);
  const trimmed = line.trim();
  if (!trimmed) return { output: '' };
  const toks = lex(trimmed);

  if (toks.some((t) => ['<', '&'].includes(t.op))) {
    remember(state, trimmed);
    return { output: 'bash: < and & are switched off on this training computer. To copy a file, use cp <from> <to>.\n' };
  }

  // `a ; b`, `a && b` and `a || b` run one after another; each part is one history entry.
  const groups = [[]];
  for (const t of toks) {
    if (t.op === ';' || t.op === '&&' || t.op === '||') groups.push([]);
    else groups[groups.length - 1].push(t);
  }
  const parts = groups.filter((g) => g.length);
  let output = '';
  for (let g of parts) {
    // `command > file` writes the output into a file, `>> file` adds it to the end.
    let redirect = null;
    const at = g.findIndex((t) => t.op === '>' || t.op === '>>');
    if (at >= 0) {
      const target = g[at + 1];
      if (!target || !('w' in target) || at + 2 !== g.length) {
        remember(state, trimmed);
        return { output: output + "bash: put the file name right after > or >>, at the end, e.g. echo event-1 C >> findings.txt\n" };
      }
      redirect = { append: g[at].op === '>>', file: target.w };
      g = g.slice(0, at);
      if (!g.length) {
        remember(state, trimmed);
        return { output: output + "bash: syntax error near unexpected token `>'\n" };
      }
    }
    const stages = [[]];
    for (const t of g) {
      if (t.op === '|') stages.push([]);
      else stages[stages.length - 1].push(t.w);
    }
    remember(state, parts.length === 1 ? trimmed : stages.map((s) => s.join(' ')).join(' | '));
    if (stages.some((s) => !s.length)) return { output: output + "bash: syntax error near unexpected token `|'\n" };
    let stdin = null;
    for (const words of stages) {
      const res = exec(lab, state, words, stdin);
      if (res.clear && stages.length === 1 && parts.length === 1) return res;
      if (res.prompt) return { ...res, output: output + (res.output || '') };
      stdin = res.output || '';
    }
    output += redirect ? writeOut(lab, state, redirect, stdin) : stdin;
  }
  return { output };
}

// Saves a command's output into a file (> replaces it, >> adds to the end).
// Lines written to findings.txt are the crew's answers: `<item> <answer>`, e.g. `event-1 C`.
function writeOut(lab, state, redirect, text) {
  const abs = resolvePath(state.cwd, redirect.file, state.home);
  if (isDir(state, abs) && !state.files[abs]) return `bash: ${redirect.file}: Is a directory\n`;
  if (!isDir(state, dirname(abs))) return `bash: ${redirect.file}: No such file or directory\n` + pathHint(state, redirect.file);
  if (VIRTUAL_FILES.includes(abs) || !canWrite(state, abs, false)) return `bash: ${redirect.file}: Permission denied\n`;
  const old = redirect.append && state.files[abs] ? state.files[abs].content : '';
  state.files[abs] = { path: abs, content: old + text };
  return basename(abs) === FINDINGS ? recordFindings(lab, state, text) : '';
}

const FINDINGS = 'findings.txt';

function recordFindings(lab, state, text) {
  const items = lab.classify || {};
  const names = Object.keys(items);
  if (!names.length) return '';
  let out = '';
  for (const line of text.split('\n')) {
    const words = line.replace(/[:=,]/g, ' ').trim().split(/\s+/).filter(Boolean);
    if (!words.length) continue;
    const item = words[0].toLowerCase();
    if (!(item in items)) {
      out += `(findings.txt: "${words[0]}" is not on this lab's list. Items: ${names.join(', ')})\n`;
      continue;
    }
    if (words.length < 2) {
      out += `(findings.txt: write an answer after ${words[0]}, e.g. echo ${words[0]} <answer> >> findings.txt)\n`;
      continue;
    }
    const raw = words[words.length - 1].toLowerCase();
    state.classified[item] = CATEGORY[raw] || raw;
  }
  return out;
}

function remember(state, line) {
  state.history.push(line);
  if (state.history.length > 200) state.history.shift();
}

function exec(lab, state, words, stdin) {
  let argv = words;
  let root = false;
  if (argv[0] === 'sudo') {
    argv = argv.slice(1);
    while (argv[0] && argv[0].startsWith('-')) argv = argv.slice(1);
    if (!argv.length) return { output: 'usage: sudo <command>\n' };
    if (argv[0] === 'su' || argv[0] === 'bash' || argv[0] === 'sh' || argv[0] === 'sudo') {
      return { output: `You stay signed in as ${state.user} here. Put sudo in front of the one command that needs admin rights.\n` };
    }
    root = true;
  }
  const name = argv[0];
  const fn = Object.hasOwn(COMMANDS, name) ? COMMANDS[name] : null;
  if (!fn) return { output: unknown(name) };
  const r = fn({ lab, state, args: argv.slice(1), stdin, root, cmd: name, line: argv.join(' ') });
  return typeof r === 'string' ? { output: r } : r;
}

function unknown(name) {
  let s = `bash: ${name}: command not found\n`;
  const lower = name.toLowerCase().replace(/\.exe$/, '');
  if (Object.hasOwn(WINDOWS, lower)) s += `(${name} is a Windows command. On Linux, use: ${WINDOWS[lower]})\n`;
  else if (lower !== name && Object.hasOwn(COMMANDS, lower)) s += `(Linux is case-sensitive. Try: ${lower})\n`;
  else if (Object.hasOwn(MISSING, lower)) s += `(This training computer doesn't have ${lower}. Try: ${MISSING[lower]})\n`;
  else if (/^clas+s?if/.test(lower)) s += '(Linux has no classify command. Write your answer into findings.txt instead, e.g. echo event-1 C >> findings.txt)\n';
  else s += "(Type help to see the commands you can use.)\n";
  return s;
}

function splitFlags(args) {
  let flags = '';
  const rest = [];
  for (const a of args) {
    if (/^-[A-Za-z]+$/.test(a)) flags += a.slice(1);
    else rest.push(a);
  }
  return { flags, rest };
}

function idCmd(ctx) {
  const { state, root } = ctx;
  const name = ctx.args.find((a) => !a.startsWith('-')) || (root ? 'root' : state.user);
  if (name !== 'root' && name !== state.user && !findUser(state, name)) return `id: '${name}': no such user\n`;
  const uid = uidOf(state, name);
  const groups = name === 'root' ? [] : groupsOf(state, name);
  const list = [`${uid}(${name})`, ...groups.map((g) => `${GROUP_IDS[g] ?? 1600}(${g})`)];
  return `uid=${uid}(${name}) gid=${uid}(${name}) groups=${list.join(',')}\n`;
}

function cd(ctx) {
  const { state, root } = ctx;
  const arg = ctx.args.find((a) => a !== '--');
  let target;
  if (!arg) target = state.home;
  else if (arg === '-') {
    if (!state.oldCwd) return 'bash: cd: OLDPWD not set\n';
    target = state.oldCwd;
  } else target = resolvePath(state.cwd, arg, state.home);
  if (!isDir(state, target)) {
    if (getFile(state, target)) return `bash: cd: ${arg}: Not a directory\n`;
    return `bash: cd: ${arg}: No such file or directory\n` + pathHint(state, arg);
  }
  if (target === '/root' && !root) return `bash: cd: ${arg}: Permission denied\n`;
  state.oldCwd = state.cwd;
  state.cwd = target;
  return arg === '-' ? `${target}\n` : '';
}

function longLine(state, abs, name, dir) {
  const f = dir ? null : getFile(state, abs);
  const owner = ownerOf(state, abs);
  const group = abs === '/etc/shadow' ? 'shadow' : owner;
  const acl = state.acl[abs] && (state.acl[abs].denied.length || Object.keys(state.acl[abs].users || {}).length) ? '+' : ' ';
  const size = dir ? 4096 : f.content.length;
  return `${dir ? 'd' : '-'}${modeOf(state, abs, dir)}${acl} ${dir ? 2 : 1} ${owner.padEnd(6)} ${group.padEnd(6)} ${String(size).padStart(5)} ${DATE} ${name}`;
}

function ls(ctx) {
  const { state, root } = ctx;
  const { flags, rest } = splitFlags(ctx.args);
  const long = flags.includes('l');
  const all = flags.includes('a');
  const targets = rest.length ? rest : ['.'];
  let out = '';
  targets.forEach((t, idx) => {
    const abs = resolvePath(state.cwd, t, state.home);
    if (isDir(state, abs) && !getFile(state, abs)) {
      if (abs === '/root' && !root) {
        out += `ls: cannot open directory '${t}': Permission denied\n`;
        return;
      }
      if (targets.length > 1) out += `${idx ? '\n' : ''}${t}:\n`;
      let list = children(state, abs);
      if (!all) list = list.filter((k) => !k.name.startsWith('.'));
      else list = [{ name: '.', dir: true }, { name: '..', dir: true }, ...list];
      if (long) {
        out += `total ${list.length * 4}\n`;
        for (const k of list) {
          const kAbs = k.name === '.' ? abs : k.name === '..' ? dirname(abs) : normPath(`${abs}/${k.name}`);
          out += longLine(state, kAbs, k.name, k.dir) + '\n';
        }
      } else if (list.length) {
        out += list.map((k) => (k.dir && k.name !== '.' && k.name !== '..' ? `${k.name}/` : k.name)).join('  ') + '\n';
      }
    } else if (getFile(state, abs)) {
      out += long ? longLine(state, abs, t, false) + '\n' : `${t}\n`;
    } else {
      out += `ls: cannot access '${t}': No such file or directory\n` + pathHint(state, t);
    }
  });
  return out;
}

function cat(ctx) {
  const names = ctx.args.filter((a) => !a.startsWith('-'));
  if (!names.length) return ctx.stdin ?? 'Usage: cat <file>   e.g. cat README.txt\n';
  let out = '';
  for (const a of names) {
    const r = openFile(ctx, a, 'cat');
    out += r.err || withNl(r.file.content);
  }
  return out;
}

function headTail(ctx, which) {
  let n = 10;
  const names = [];
  const args = ctx.args;
  for (let i = 0; i < args.length; i++) {
    const a = args[i];
    if (a === '-n') n = parseInt(args[++i], 10);
    else if (/^-n\d+$/.test(a)) n = parseInt(a.slice(2), 10);
    else if (/^-\d+$/.test(a)) n = parseInt(a.slice(1), 10);
    else if (!a.startsWith('-')) names.push(a);
  }
  if (Number.isNaN(n)) return `${which}: invalid number of lines\n`;
  const pick = (text) => {
    if (!text) return '';
    const lines = text.replace(/\n$/, '').split('\n');
    return (which === 'head' ? lines.slice(0, n) : lines.slice(Math.max(0, lines.length - n))).join('\n') + '\n';
  };
  if (!names.length) return ctx.stdin !== null ? pick(ctx.stdin) : `Usage: ${which} -n 5 <file>\n`;
  let out = '';
  names.forEach((a, i) => {
    const r = openFile(ctx, a, which);
    if (r.err) out += r.err;
    else out += (names.length > 1 ? `${i ? '\n' : ''}==> ${a} <==\n` : '') + pick(r.file.content);
  });
  return out;
}

function grep(ctx) {
  const { state } = ctx;
  const { flags, rest } = splitFlags(ctx.args);
  if (!rest.length) return 'Usage: grep [-i] [-n] [-v] [-c] [-r] <text> [file...]   e.g. grep -i shield night.log\n';
  const [pat, ...names] = rest;
  const ci = flags.includes('i');
  const num = flags.includes('n');
  const inv = flags.includes('v');
  const count = flags.includes('c');
  const rec = flags.includes('r') || flags.includes('R');
  let re;
  try {
    re = new RegExp(pat, ci ? 'i' : '');
  } catch {
    re = new RegExp(pat.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), ci ? 'i' : '');
  }
  const scan = (text, label) => {
    const lines = text.replace(/\n$/, '').split('\n');
    let c = 0;
    let out = '';
    lines.forEach((l, i) => {
      if (re.test(l) !== inv) {
        c++;
        if (!count) out += `${label ? label + ':' : ''}${num ? i + 1 + ':' : ''}${l}\n`;
      }
    });
    return count ? `${label ? label + ':' : ''}${c}\n` : out;
  };
  if (!names.length) {
    if (rec) names.push('.');
    else if (ctx.stdin !== null) return scan(ctx.stdin, '');
    else return 'grep: give it a file to search, e.g. grep -i shield night.log\n';
  }
  const targets = [];
  let out = '';
  for (const a of names) {
    const abs = resolvePath(state.cwd, a, state.home);
    if (isDir(state, abs) && !getFile(state, abs)) {
      if (!rec) {
        out += `grep: ${a}: Is a directory\n`;
        continue;
      }
      const pre = abs === '/' ? '/' : abs + '/';
      for (const key of Object.keys(state.files).filter((k) => k.startsWith(pre)).sort()) {
        const label = (a === '.' ? '' : a.replace(/\/$/, '') + '/') + key.slice(pre.length);
        const r = openFile(ctx, key, 'grep');
        if (r.err) out += r.err;
        else targets.push([r.file, label]);
      }
    } else {
      const r = openFile(ctx, a, 'grep');
      if (r.err) out += r.err;
      else targets.push([r.file, a]);
    }
  }
  const many = targets.length > 1 || rec;
  for (const [f, label] of targets) out += scan(f.content, many ? label : '');
  return out;
}

function textFilter(ctx, cmd, fn) {
  const name = ctx.args.find((a) => !a.startsWith('-'));
  let text;
  if (name) {
    const r = openFile(ctx, name, cmd);
    if (r.err) return r.err;
    text = r.file.content;
  } else if (ctx.stdin !== null) text = ctx.stdin;
  else return `Usage: ${cmd} <file>   or   <command> | ${cmd}\n`;
  if (!text) return '';
  return fn(text.replace(/\n$/, '').split('\n')).join('\n') + '\n';
}

function wc(ctx) {
  const { flags, rest } = splitFlags(ctx.args);
  const pickAll = !/[lwc]/.test(flags);
  const count = (text, label) => {
    const n = [];
    if (pickAll || flags.includes('l')) n.push((text.match(/\n/g) || []).length);
    if (pickAll || flags.includes('w')) n.push(text.split(/\s+/).filter(Boolean).length);
    if (pickAll || flags.includes('c')) n.push(text.length);
    return n.map((x) => String(x).padStart(pickAll ? 7 : 1)).join(' ') + (label ? ` ${label}` : '') + '\n';
  };
  if (!rest.length) return ctx.stdin !== null ? count(ctx.stdin, '') : 'Usage: wc -l <file>\n';
  let out = '';
  for (const a of rest) {
    const r = openFile(ctx, a, 'wc');
    out += r.err || count(r.file.content, a);
  }
  return out;
}

function lastlog(ctx) {
  const { state } = ctx;
  const i = ctx.args.indexOf('-u');
  const only = i >= 0 ? ctx.args[i + 1] : null;
  let users = state.users;
  if (only) {
    users = users.filter((u) => u.name.toLowerCase() === only.toLowerCase());
    if (!users.length) return `lastlog: Unknown user or range: ${only}\n`;
  }
  let s = 'Username         Port     From             Latest\n';
  for (const u of users) {
    const last = u.lastLogon || 'Never';
    const never = /^never$/i.test(last);
    const latest = never ? '**Never logged in**' : /^now$/i.test(last) ? NOW : last;
    s += `${u.name.padEnd(17)}${(never ? '' : 'pts/0').padEnd(9)}${(never ? '' : '10.0.0.7').padEnd(17)}${latest}\n`;
  }
  return s;
}

function passwdCmd(ctx) {
  const { state, root } = ctx;
  const { flags, rest } = splitFlags(ctx.args.filter((a) => !a.startsWith('--')));
  const long = ctx.args.filter((a) => a.startsWith('--'));
  const name = rest[0] || (root ? 'root' : state.user);
  if (name === 'root') return 'passwd: the root account is not part of this lab.\n';
  if (name !== state.user && !root) {
    return `passwd: You may not view or modify password information for ${name}.\n` + sudoHint(ctx);
  }
  const u = findUser(state, name);
  if (!u) return `passwd: user '${name}' does not exist\n`;
  if (flags.includes('S') || long.includes('--status')) {
    return `${u.name} ${u.enabled ? 'P' : 'L'} ${u.changed ? '2026-09-23' : isoDate(u.passwordSet)} 0 99999 7 -1\n`;
  }
  if (flags.includes('l') || long.includes('--lock')) {
    u.enabled = false;
    return 'passwd: password expiry information changed.\n';
  }
  if (flags.includes('u') || long.includes('--unlock')) {
    u.enabled = true;
    return 'passwd: password expiry information changed.\n';
  }
  if (flags || long.length) return 'Usage: passwd <user>   |   passwd -S <user>   |   passwd -l <user>\n';
  state.pending = { kind: 'passwd', user: u.name, step: 1, prompt: 'New password: ' };
  return { output: '', prompt: state.pending.prompt, secret: true };
}

function usermod(ctx) {
  const { state, root, args } = ctx;
  if (!root) return 'usermod: Permission denied.\nusermod: cannot lock /etc/passwd; try again later.\n' + sudoHint(ctx);
  let action = null;
  const names = [];
  for (let i = 0; i < args.length; i++) {
    const a = args[i];
    if (a === '-L' || a === '--lock') action = 'lock';
    else if (a === '-U' || a === '--unlock') action = 'unlock';
    else if (a === '-s' || a === '--shell') {
      const shell = args[++i] || '';
      if (/(nologin|false)$/.test(shell)) action = 'lock';
      else if (/(bash|sh)$/.test(shell)) action = 'unlock';
    } else if (a === '-e' || a === '--expiredate') {
      const when = args[++i] || '';
      action = when === '' || when === '-1' ? 'unlock' : 'lock';
    } else if (!a.startsWith('-')) names.push(a);
  }
  const name = names[names.length - 1];
  if (!name) return 'Usage: sudo usermod -L <user>   (lock)    sudo usermod -U <user>   (unlock)\n';
  const u = findUser(state, name);
  if (!u) return `usermod: user '${name}' does not exist\n`;
  if (!action) return 'This training computer supports usermod -L (lock) and -U (unlock).\n';
  u.enabled = action === 'unlock';
  return '';
}

function procOwner(state, p) {
  return p.user || (p.name === 'bash' ? state.user : 'root');
}

function rss(p) {
  return parseInt(String(p.mem || '12,480 K').replace(/[^\d]/g, ''), 10) || 12480;
}

function ps(ctx) {
  const { state } = ctx;
  const everyone = ctx.args.some((a) => /a|e|x/.test(a));
  const list = state.processes.filter((p) => !p.killed && (everyone || procOwner(state, p) === state.user));
  let s = 'USER         PID %CPU %MEM    RSS TTY      STAT START   TIME COMMAND\n';
  for (const p of list) {
    const own = procOwner(state, p) === state.user;
    s += `${procOwner(state, p).padEnd(8)}${String(p.pid).padStart(8)}  0.0  0.1 ${String(rss(p)).padStart(6)} ${(own ? 'pts/0' : '?').padEnd(8)} ${(own ? 'Ss' : 'Ssl').padEnd(4)} 02:13   0:00 ${p.cmd || (own ? '-bash' : p.name)}\n`;
  }
  return s;
}

function stopProcess(state, p) {
  p.killed = true;
  for (const sv of state.services) if (sv.process && sv.process === p.name) sv.running = false;
}

function kill(ctx) {
  const { state, root } = ctx;
  const pids = ctx.args.filter((a) => /^\d+$/.test(a));
  if (!pids.length) return 'Usage: kill <PID>   (find the PID with ps aux)\n';
  let out = '';
  for (const pid of pids) {
    const p = state.processes.find((x) => !x.killed && String(x.pid) === pid);
    if (!p) out += `bash: kill: (${pid}) - No such process\n`;
    else if (p.name === 'bash' && procOwner(state, p) === state.user) out += '(That is your own shell. Stopping it would log you out, so leave it running.)\n';
    else if (procOwner(state, p) !== state.user && !root) out += `bash: kill: (${pid}) - Operation not permitted\n` + sudoHint(ctx);
    else stopProcess(state, p);
  }
  return out;
}

// pkill matches part of the name (like the real one); killall needs the exact name.
function pkill(ctx, exact) {
  const { state, root } = ctx;
  const cmd = exact ? 'killall' : 'pkill';
  const name = ctx.args.find((a) => !a.startsWith('-'));
  if (!name) return `Usage: ${cmd} <name>   (see names with ps aux)\n`;
  let re;
  try {
    re = new RegExp(name);
  } catch {
    re = null;
  }
  const hits = state.processes.filter((p) => !p.killed && p.name !== 'bash' && (exact || !re ? p.name === name : re.test(p.name)));
  if (!hits.length) return exact ? `${name}: no process found\n` : '';
  let out = '';
  for (const p of hits) {
    if (procOwner(state, p) !== state.user && !root) {
      out += exact ? `${name}(${p.pid}): Operation not permitted\n` : `${cmd}: killing pid ${p.pid} failed: Operation not permitted\n`;
    } else stopProcess(state, p);
  }
  return out ? out + sudoHint(ctx) : '';
}

// `ss -tlnp` (and the older `netstat -tlnp`): listening ports, and with -p the program behind each.
function sockets(ctx, style) {
  const { state, root } = ctx;
  const showProc = ctx.args.some((a) => /^-\w*p/.test(a));
  const rows = state.services.filter((x) => x.running).map((sv) => {
    const p = state.processes.find((x) => !x.killed && x.name === sv.process);
    const udp = /udp/i.test(sv.proto || '');
    return { sv, p, udp, mine: p && procOwner(state, p) === state.user };
  });
  let s;
  if (style === 'ss') {
    s = 'State    Recv-Q  Send-Q   Local Address:Port    Peer Address:Port  Process\n';
    for (const { sv, p, udp, mine } of rows) {
      const proc = showProc && p && (root || mine) ? `users:(("${p.name}",pid=${p.pid},fd=3))` : '';
      s += `${(udp ? 'UNCONN' : 'LISTEN').padEnd(9)}0       ${udp ? '0      ' : '128    '}${`0.0.0.0:${sv.port}`.padStart(19)}  ${'0.0.0.0:*'.padStart(15)}    ${proc}\n`;
    }
    if (showProc && !root && rows.some((r) => !r.mine)) s += '(Some programs are hidden: only root sees them all. Try: sudo ss -tlnp)\n';
    return s;
  }
  s = 'Active Internet connections (only servers)\nProto Recv-Q Send-Q Local Address           Foreign Address         State       PID/Program name\n';
  for (const { sv, p, udp, mine } of rows) {
    const proc = showProc ? (p && (root || mine) ? `${p.pid}/${p.name}` : '-') : '';
    s += `${(udp ? 'udp' : 'tcp').padEnd(6)}${'0'.padStart(6)}${'0'.padStart(7)} ${`0.0.0.0:${sv.port}`.padEnd(24)}${'0.0.0.0:*'.padEnd(24)}${(udp ? '' : 'LISTEN').padEnd(12)}${proc}\n`;
  }
  if (showProc && !root && rows.some((r) => !r.mine)) {
    s = '(Not all processes could be identified, non-owned process info\n will not be shown, you would have to be root to see it all.)\n' + s;
  }
  return s;
}

function cp(ctx) {
  const { state, root } = ctx;
  const paths = ctx.args.filter((a) => !a.startsWith('-'));
  if (paths.length < 2) return paths.length ? `cp: missing destination file operand after '${paths[0]}'\n` : 'cp: missing file operand\n';
  const [srcArg, dstArg] = paths;
  const srcAbs = resolvePath(state.cwd, srcArg, state.home);
  const src = getFile(state, srcAbs);
  if (!src) {
    if (isDir(state, srcAbs)) return `cp: -r not specified; omitting directory '${srcArg}'\n`;
    return `cp: cannot stat '${srcArg}': No such file or directory\n` + pathHint(state, srcArg);
  }
  if (!canRead(state, src, root)) return `cp: cannot open '${srcArg}' for reading: Permission denied\n` + sudoHint(ctx);
  let dst = resolvePath(state.cwd, dstArg, state.home);
  if (isDir(state, dst) && !getFile(state, dst)) dst = normPath(`${dst}/${basename(src.path)}`);
  if (dst === src.path) return `cp: '${srcArg}' and '${dstArg}' are the same file\n`;
  if (!isDir(state, dirname(dst))) return `cp: cannot create regular file '${dstArg}': No such file or directory\n` + pathHint(state, dstArg);
  if (!canWrite(state, dst, root) || VIRTUAL_FILES.includes(dst)) return `cp: cannot create regular file '${dstArg}': Permission denied\n`;
  state.files[dst] = { path: dst, content: src.content };
  markRead(state, src);
  return '';
}

function applyMode(mode, spec) {
  if (/^[0-7]{3,4}$/.test(spec)) {
    return [...spec.slice(-3)].map((d) => {
      const n = Number(d);
      return (n & 4 ? 'r' : '-') + (n & 2 ? 'w' : '-') + (n & 1 ? 'x' : '-');
    }).join('');
  }
  const bits = mode.split('');
  for (const part of spec.split(',')) {
    const m = /^([ugoa]*)([+\-=])([rwx]*)$/.exec(part);
    if (!m) return null;
    const who = new Set((m[1] || 'a').replace(/a/g, 'ugo'));
    for (const w of who) {
      const base = { u: 0, g: 3, o: 6 }[w];
      ['r', 'w', 'x'].forEach((b, i) => {
        const has = m[3].includes(b);
        if (m[2] === '+' && has) bits[base + i] = b;
        else if (m[2] === '-' && has) bits[base + i] = '-';
        else if (m[2] === '=') bits[base + i] = has ? b : '-';
      });
    }
  }
  return bits.join('');
}

function chmod(ctx) {
  const { state, root } = ctx;
  const [spec, ...names] = ctx.args.filter((a) => !/^-[Rv]+$/.test(a));
  if (!spec || !names.length) return 'Usage: chmod <mode> <file>   e.g. chmod 600 secret.txt  or  chmod o-r secret.txt\n';
  let out = '';
  for (const a of names) {
    const abs = resolvePath(state.cwd, a, state.home);
    const dir = isDir(state, abs) && !getFile(state, abs);
    if (!dir && !getFile(state, abs)) {
      out += `chmod: cannot access '${a}': No such file or directory\n` + pathHint(state, a);
      continue;
    }
    if (ownerOf(state, abs) !== state.user && !root) {
      out += `chmod: changing permissions of '${a}': Operation not permitted\n`;
      continue;
    }
    const next = applyMode(modeOf(state, abs, dir), spec);
    if (!next) return `chmod: invalid mode: '${spec}'\n`;
    state.modes[abs] = next;
  }
  return out;
}

function setfacl(ctx) {
  const { state, root, args } = ctx;
  const usage = 'Usage: setfacl -m u:<user>:--- <file>   (block a user)\n       setfacl -x u:<user> <file>       (remove that rule)\n';
  const mi = args.findIndex((a) => a === '-m' || a === '--modify');
  const xi = args.findIndex((a) => a === '-x' || a === '--remove');
  const bi = args.findIndex((a) => a === '-b' || a === '--remove-all');
  const opt = mi >= 0 ? mi : xi >= 0 ? xi : bi;
  if (opt < 0) return usage;
  const spec = bi >= 0 && opt === bi ? null : args[opt + 1];
  const names = args.filter((a, i) => !a.startsWith('-') && !(spec !== null && i === opt + 1));
  if ((opt !== bi && !spec) || !names.length) return usage;
  let rule = null;
  if (spec !== null) {
    rule = (mi >= 0 ? /^(?:u|user):([^:]+):([rwx-]*)$/ : /^(?:u|user):([^:]+)$/).exec(spec);
    if (!rule) return `setfacl: Option ${args[opt]}: Invalid argument near character 1\n` + usage;
  }
  let out = '';
  for (const a of names) {
    const abs = resolvePath(state.cwd, a, state.home);
    if (!getFile(state, abs) && !isDir(state, abs)) {
      out += `setfacl: ${a}: No such file or directory\n` + pathHint(state, a);
      continue;
    }
    if (ownerOf(state, abs) !== state.user && !root) {
      out += `setfacl: ${a}: Operation not permitted\n`;
      continue;
    }
    const acl = (state.acl[abs] = state.acl[abs] || { denied: [] });
    acl.users = acl.users || {};
    if (bi >= 0 && opt === bi) {
      acl.denied = [];
      acl.users = {};
      continue;
    }
    const user = rule[1].toLowerCase();
    acl.denied = acl.denied.filter((x) => x !== user);
    delete acl.users[user];
    if (mi >= 0) {
      const perms = rule[2] || '';
      if (/[rwx]/.test(perms)) acl.users[user] = ['r', 'w', 'x'].map((b) => (perms.includes(b) ? b : '-')).join('');
      else acl.denied.push(user);
    }
  }
  return out;
}

function getfacl(ctx) {
  const { state } = ctx;
  const names = ctx.args.filter((a) => !a.startsWith('-'));
  if (!names.length) return 'Usage: getfacl <file>\n';
  let out = '';
  for (const a of names) {
    const abs = resolvePath(state.cwd, a, state.home);
    const dir = isDir(state, abs) && !getFile(state, abs);
    if (!dir && !getFile(state, abs)) {
      out += `getfacl: ${a}: No such file or directory\n`;
      continue;
    }
    const mode = modeOf(state, abs, dir);
    const owner = ownerOf(state, abs);
    const acl = state.acl[abs] || { denied: [], users: {} };
    out += `# file: ${abs.slice(1)}\n# owner: ${owner}\n# group: ${owner}\nuser::${mode.slice(0, 3)}\n`;
    for (const [u, p] of Object.entries(acl.users || {})) out += `user:${u}:${p}\n`;
    for (const u of acl.denied) out += `user:${u}:---\n`;
    out += `group::${mode.slice(3, 6)}\n`;
    if (acl.denied.length || Object.keys(acl.users || {}).length) {
      const bits = [mode.slice(3, 6), ...Object.values(acl.users || {})];
      out += `mask::${['r', 'w', 'x'].map((b, i) => (bits.some((x) => x[i] === b) ? b : '-')).join('')}\n`;
    }
    out += `other::${mode.slice(6)}\n\n`;
  }
  return out;
}

// Not a real hash: a stable fingerprint of the content, the right length for the algorithm.
function fakeHash(content, algo = 'sha256', len = 64) {
  let h = 2166136261;
  const s = `${algo}:${content}`;
  for (let i = 0; i < s.length; i++) {
    h ^= s.charCodeAt(i);
    h = Math.imul(h, 16777619);
  }
  let out = '';
  let x = h >>> 0;
  while (out.length < len) {
    x = Math.imul(x ^ 0x9e3779b9, 16777619) >>> 0;
    x ^= x >>> 13;
    out += (x >>> 0).toString(16).padStart(8, '0');
  }
  return out.slice(0, len);
}

function hashsum(ctx, algo, len) {
  const names = ctx.args.filter((a) => !a.startsWith('-'));
  if (!names.length) {
    if (ctx.stdin !== null) return `${fakeHash(ctx.stdin, algo, len)}  -\n`;
    return `Usage: ${ctx.cmd} <file>   e.g. ${ctx.cmd} README.txt\n`;
  }
  let out = '';
  for (const a of names) {
    const r = openFile(ctx, a, ctx.cmd);
    out += r.err || `${fakeHash(r.file.content, algo, len)}  ${a}\n`;
  }
  return out;
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
  if (!u) return 'Login incorrect (no such user).\n';
  if (!u.enabled) return 'Login incorrect: this account is locked.\n';
  return success ? `Login successful. Welcome, ${u.fullName || u.name}.\n` : 'Login incorrect (wrong password).\n';
}

function mask(lab, state, args) {
  if (!args.length) return 'Usage: mask <file>\n';
  const f = getFile(state, resolvePath(state.cwd, args.join(' '), state.home));
  if (!f) return `mask: ${args.join(' ')}: No such file or directory\n`;
  const rule = (lab.masks || {})[f.path];
  if (!rule) return 'Nothing in this lab says how to mask that file.\n';
  markRead(state, f);
  const masked = f.content.replace(new RegExp(rule.pattern, 'g'), rule.replace);
  const outPath = rule.output ? normPath(rule.output) : f.path.replace(/(\.[A-Za-z0-9]+)?$/, '.masked$1');
  state.files[outPath] = { path: outPath, content: masked };
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

// ---------- Tab completion ----------

// Finishes the last word of the line: a command name first, a file or folder after that.
export function complete(state, line) {
  const m = /^(.*?)(\S*)$/.exec(line);
  const before = m[1];
  const word = m[2];
  const head = before.trim();
  let options;
  if (!head || /(\||;|&&|^sudo)$/.test(head)) {
    options = COMMAND_NAMES.filter((c) => c.startsWith(word)).map((c) => ({ text: c, dir: false }));
  } else {
    const slash = word.lastIndexOf('/');
    const dirPart = slash >= 0 ? word.slice(0, slash + 1) : '';
    const base = word.slice(slash + 1);
    const dirAbs = resolvePath(state.cwd, dirPart || '.', state.home);
    if (!isDir(state, dirAbs)) return { line, options: [] };
    options = children(state, dirAbs)
      .filter((c) => c.name.startsWith(base) && (base.startsWith('.') || !c.name.startsWith('.')))
      .map((c) => ({ text: dirPart + c.name, dir: c.dir }));
  }
  if (!options.length) return { line, options: [] };
  if (options.length === 1) {
    const o = options[0];
    return { line: before + o.text + (o.dir ? '/' : ' '), options: [] };
  }
  let common = options[0].text;
  for (const o of options) while (!o.text.startsWith(common)) common = common.slice(0, -1);
  return { line: before + common, options: options.map((o) => o.text.slice(o.text.lastIndexOf('/') + 1) + (o.dir ? '/' : '')) };
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
// v2 is the Linux machine. A v1 save (the old Windows machine, C:\ paths) starts the lab fresh.

export function snapshot(state) {
  return {
    v: 2,
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
    modes: state.modes,
    opened: state.opened,
    triaged: state.triaged,
    files: Object.fromEntries(Object.entries(state.files).map(([k, f]) => [k, { path: f.path, content: f.content }])),
  };
}

export function restore(lab, saved) {
  const state = createState(lab);
  if (!saved || saved.v !== 2) return state;
  if (typeof saved.cwd === 'string' && saved.cwd.startsWith('/')) state.cwd = normPath(saved.cwd);
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
  if (saved.acl && typeof saved.acl === 'object') {
    for (const [k, a] of Object.entries(saved.acl)) {
      if (a && Array.isArray(a.denied)) state.acl[k] = { denied: a.denied.filter((x) => typeof x === 'string'), users: a.users && typeof a.users === 'object' ? { ...a.users } : {} };
    }
  }
  if (saved.modes && typeof saved.modes === 'object') {
    for (const [k, m] of Object.entries(saved.modes)) if (typeof m === 'string' && /^[rwx-]{9}$/.test(m)) state.modes[k] = m;
  }
  state.opened = Array.isArray(saved.opened) ? saved.opened.filter((x) => typeof x === 'string') : [];
  state.triaged = saved.triaged && typeof saved.triaged === 'object' ? { ...saved.triaged } : {};
  for (const [k, f] of Object.entries(saved.files || {})) {
    if (f && typeof f.path === 'string' && typeof f.content === 'string' && k.startsWith('/')) state.files[k] = { path: f.path, content: f.content };
  }
  return state;
}
