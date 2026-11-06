// Relay Console engine: a Cisco IOS-style router console for Networking 2 labs that a packet
// simulator can't show (BGP, EIGRP, QoS, MPLS, VPNs, IDS, IPv6, cloud).
// Pure logic with no DOM, so it runs in the browser and under `node --test`.
//
// A lab is data: what each `show` command prints (which can change as the student configures),
// which configuration lines the router accepts and what they change ("facts"), and the
// objectives. Commands can be abbreviated like real IOS (`sh ip bgp`, `conf t`, `nei`).

const BUILTIN_SUFFIX = { user: '>', priv: '#', config: '(config)#' };
const CONFIG_MODES = (lab) => ['config', ...Object.keys(lab.modes || {})];

// ---------- matching ----------

const IP = /^\d{1,3}(\.\d{1,3}){3}$/;
const IP6 = /^[0-9a-f:]+(\/\d{1,3})?$/i;

// Interface names: "g0/0", "gi0/0", "GigabitEthernet0/0", "Tu1", "lo0" -> "GigabitEthernet0/0", "Tunnel1", "Loopback0".
const IF_TYPES = [
  ['gigabitethernet', 'GigabitEthernet'],
  ['fastethernet', 'FastEthernet'],
  ['serial', 'Serial'],
  ['tunnel', 'Tunnel'],
  ['loopback', 'Loopback'],
  ['vlan', 'Vlan'],
];

export function ifName(text) {
  const m = String(text).toLowerCase().match(/^([a-z]+)\s*(\d+(?:\/\d+)*(?:\.\d+)?)$/);
  if (!m) return null;
  const hit = IF_TYPES.find(([full]) => full.startsWith(m[1]));
  return hit ? hit[1] + m[2] : null;
}

function paramOk(type, tok) {
  if (type === 'ip') return IP.test(tok) && tok.split('.').every((n) => Number(n) <= 255);
  if (type === 'cidr') return /^\d{1,3}(\.\d{1,3}){3}\/\d{1,2}$/.test(tok);
  if (type === 'ipv6') return IP6.test(tok) && tok.includes(':');
  if (type === 'n') return /^\d+$/.test(tok);
  return true; // word
}

// Matches typed tokens against a pattern like "neighbor <ip> remote-as <n>".
// Keywords may be abbreviated to any prefix. Returns { status, args, exact, at }.
export function match(pattern, toks) {
  const parts = pattern.split(' ');
  let ti = 0;
  let exact = 0;
  const args = [];
  for (const part of parts) {
    if (ti >= toks.length) return { status: 'incomplete', args, exact, at: ti };
    const tok = toks[ti];
    if (part.startsWith('<') && part.endsWith('>')) {
      const type = part.slice(1, -1);
      if (type === 'rest') {
        args.push(toks.slice(ti).join(' '));
        ti = toks.length;
        continue;
      }
      if (type === 'if') {
        let joined = tok;
        if (/^[a-z]+$/i.test(tok) && toks[ti + 1] && /^\d/.test(toks[ti + 1])) {
          joined = tok + toks[ti + 1];
          ti++;
        }
        const name = ifName(joined);
        if (!name) return { status: 'fail', args, exact, at: ti };
        args.push(name);
        ti++;
        continue;
      }
      if (!paramOk(type, tok)) return { status: 'fail', args, exact, at: ti };
      args.push(tok);
      ti++;
      continue;
    }
    const t = tok.toLowerCase();
    if (part.toLowerCase() === t) exact++;
    else if (!part.toLowerCase().startsWith(t)) return { status: 'fail', args, exact, at: ti };
    ti++;
  }
  if (ti < toks.length) return { status: 'fail', args, exact, at: ti };
  return { status: 'full', args, exact, at: ti };
}

// The pattern written out in full with the typed values: "nei 1.1.1.1 rem 100" -> "neighbor 1.1.1.1 remote-as 100".
export function canonical(pattern, args) {
  let k = 0;
  return pattern
    .split(' ')
    .map((p) => (p.startsWith('<') ? (p === '<rest>' ? args[k++] : args[k++]) : p))
    .join(' ');
}

function argsOk(entry, args) {
  if (!entry.args) return true;
  return entry.args.every((want, i) => want === null || want === undefined || String(want).toLowerCase() === String(args[i] ?? '').toLowerCase());
}

function factsOk(state, entry) {
  return (entry.when || []).every((f) => state.facts.includes(f)) && !(entry.unless || []).some((f) => state.facts.includes(f));
}

// The best entry for the typed tokens (most exact keywords first; lab order breaks ties).
function best(entries, toks, state) {
  const tried = entries.map((e, i) => ({ e, i, r: match(e.cmd, toks) }));
  const full = tried
    .filter((x) => x.r.status === 'full' && argsOk(x.e, x.r.args) && factsOk(state, x.e))
    .sort((a, b) => b.r.exact - a.r.exact || a.i - b.i);
  return { hit: full[0] || null, tried };
}

// ---------- state ----------

export function createState(lab) {
  return {
    mode: lab.start || 'user',
    section: null,
    facts: [...(lab.facts || [])],
    answers: [],
    history: [],
    config: [],
  };
}

export function promptText(lab, state) {
  const host = lab.host?.name || 'Router';
  if (BUILTIN_SUFFIX[state.mode]) return host + BUILTIN_SUFFIX[state.mode];
  const m = (lab.modes || {})[state.mode];
  return `${host}(${m?.prompt || 'config-' + state.mode})#`;
}

const HELP = `Relay Console: a Cisco-style router console. Commands can be shortened (sh ip bgp, conf t).

  enable | disable            move between user (>) and privileged (#) mode
  configure terminal          enter configuration mode (conf t)
  exit                        leave the current mode
  end                         back to privileged mode from any configuration mode
  do <command>                run a show command from configuration mode
  show running-config         this router's configuration
  submit <answer>             hand in an answer the crew asked for
  mission                     show your current orders again
  ?                           list the commands this lab understands right here
`;

function setFacts(state, entry) {
  for (const f of entry.sets || []) if (!state.facts.includes(f)) state.facts.push(f);
  if (entry.clears) state.facts = state.facts.filter((f) => !entry.clears.includes(f));
}

// A mode's parent: where `exit` goes, and where a command not found in the mode is looked up next.
function parentOf(lab, mode) {
  if (mode === 'config') return 'priv';
  return (lab.modes || {})[mode]?.parent || 'config';
}

// Sections nest like IOS ("policy-map MARK" then " class VOICE"); each line is indented one step deeper.
function runningConfig(lab, state) {
  const base = (lab.running || 'Building configuration...\n\nCurrent configuration:\n!\nend\n').replace(/\s*end\s*$/, '');
  const groups = [];
  for (const c of state.config) {
    const g = groups.find((x) => x.section === c.section);
    if (g) g.lines.push(c.line);
    else groups.push({ section: c.section, lines: [c.line] });
  }
  let out = base + '\n';
  let prev = [];
  for (const g of groups) {
    if (!g.section) {
      out += `${g.lines.join('\n')}\n!\n`;
      prev = [];
      continue;
    }
    const head = g.section.split('\n');
    let same = 0;
    while (same < head.length && same < prev.length && head[same] === prev[same]) same++;
    for (const h of head.slice(same)) out += h + '\n';
    const pad = ' '.repeat(head.length);
    const body = g.lines.filter((l) => l !== '!');
    if (body.length) out += body.map((l) => pad + l).join('\n') + '\n';
    prev = head;
  }
  return out + '!\nend\n';
}

function helpFor(lab, state) {
  const rows = new Set();
  if (state.mode === 'user') rows.add('enable');
  if (state.mode === 'priv') ['configure terminal', 'disable', 'show running-config'].forEach((r) => rows.add(r));
  const exec = state.mode === 'user' || state.mode === 'priv';
  if (exec) for (const o of lab.outputs || []) if (factsOk(state, o) && (!o.priv || state.mode === 'priv')) rows.add(o.cmd);
  else for (const c of lab.config || []) if ((c.mode || 'config') === state.mode && !c.hidden) rows.add(c.cmd);
  if (!exec) ['exit', 'end', 'do <command>'].forEach((r) => rows.add(r));
  rows.add('submit <answer>');
  return [...rows].map((r) => '  ' + r).join('\n') + '\n';
}

function invalid(prompt, line, at) {
  const toks = line.trim().split(/\s+/);
  const col = toks.slice(0, at).join(' ').length + (at > 0 ? 1 : 0);
  return `${' '.repeat(prompt.length + col)}^\n% Invalid input detected at '^' marker.\n`;
}

function normAnswer(s) {
  return String(s).trim().replace(/\s+/g, ' ').toLowerCase();
}

function submit(lab, state, text) {
  const want = lab.steps.flatMap((s) => s.objectives).filter((o) => o.check.type === 'answer');
  const got = normAnswer(text);
  if (!got) return 'Usage: submit <answer>\n';
  const hit = want.find((o) => [o.check.value, ...(o.check.alt || [])].some((v) => normAnswer(v) === got));
  if (!hit) return 'Not the answer the crew needs. Read the output again and check every part.\n';
  if (!state.answers.includes(normAnswer(hit.check.value))) state.answers.push(normAnswer(hit.check.value));
  return `Accepted: ${text.trim()}\n`;
}

// Runs one line. Returns { output, clear? }.
export function runCommand(lab, state, line) {
  const trimmed = line.trim();
  if (!trimmed || trimmed.startsWith('!')) return { output: '' };
  const prompt = promptText(lab, state);
  state.history.push(trimmed);
  if (state.history.length > 200) state.history.shift();

  if (trimmed.endsWith('?')) return { output: helpFor(lab, state) };
  const lower = trimmed.toLowerCase();
  if (lower === 'clear' || lower === 'cls') return { output: '', clear: true };
  if (lower === 'help') return { output: HELP };
  if (lower === 'mission') return { output: missionText(lab, state) };
  const toks = trimmed.split(/\s+/);
  if (match('submit <rest>', toks).status === 'full' && toks[0].length >= 3) return { output: submit(lab, state, toks.slice(1).join(' ')) };

  const configModes = CONFIG_MODES(lab);
  if (configModes.includes(state.mode) && toks[0].toLowerCase() === 'do' && toks.length > 1) {
    const saved = state.mode;
    state.mode = 'priv';
    const r = execMode(lab, state, toks.slice(1), trimmed.slice(3), prompt.length + 3);
    state.mode = saved;
    return r;
  }
  if (state.mode === 'user' || state.mode === 'priv') return execMode(lab, state, toks, trimmed, prompt.length);
  return configMode(lab, state, toks, trimmed, prompt);
}

function execMode(lab, state, toks, line, promptLen) {
  const t = (p) => match(p, toks).status === 'full';
  if (state.mode === 'user' && t('enable')) {
    state.mode = 'priv';
    return { output: '' };
  }
  if (state.mode === 'priv' && t('disable')) {
    state.mode = 'user';
    return { output: '' };
  }
  if (state.mode === 'priv' && t('configure terminal')) {
    state.mode = 'config';
    return { output: 'Enter configuration commands, one per line.  End with CNTL/Z.\n' };
  }
  if (t('exit') || t('logout')) {
    state.mode = 'user';
    return { output: '' };
  }
  if (state.mode === 'priv' && t('show running-config')) return { output: runningConfig(lab, state) };
  if (state.mode === 'priv' && (t('copy running-config startup-config') || t('write memory'))) {
    return { output: 'Building configuration...\n[OK]\n' };
  }
  const outputs = (lab.outputs || []).filter((o) => !o.priv || state.mode === 'priv');
  const { hit, tried } = best(outputs, toks, state);
  if (hit) {
    setFacts(state, hit.e);
    return { output: hit.e.text.endsWith('\n') ? hit.e.text : hit.e.text + '\n' };
  }
  if (state.mode === 'user' && (lab.outputs || []).some((o) => o.priv && match(o.cmd, toks).status === 'full')) {
    return { output: '% This command needs privileged mode. Type enable first.\n' };
  }
  if (tried.some((x) => x.r.status === 'incomplete') || t('show')) return { output: '% Incomplete command.\n' };
  const at = Math.max(0, ...tried.map((x) => x.r.at));
  return { output: invalid(' '.repeat(promptLen), line, at) };
}

function configMode(lab, state, toks, line, prompt) {
  const t = (p) => match(p, toks).status === 'full';
  if (t('end')) {
    state.mode = 'priv';
    state.section = null;
    return { output: '' };
  }
  if (t('exit')) {
    const up = parentOf(lab, state.mode);
    state.mode = up;
    state.section = up === 'config' || up === 'priv' ? null : state.section?.split('\n').slice(0, -1).join('\n') || null;
    return { output: '' };
  }
  const here = (lab.config || []).filter((c) => (c.mode || 'config') === state.mode);
  let { hit, tried } = best(here, toks, state);
  // A command not found here is tried in the parent modes, up to global config, like IOS.
  let m = state.mode;
  let section = state.section;
  while (!hit && m !== 'config') {
    m = parentOf(lab, m);
    section = m === 'config' ? null : section?.split('\n').slice(0, -1).join('\n') || null;
    const parent = best((lab.config || []).filter((c) => (c.mode || 'config') === m), toks, state);
    if (parent.hit) {
      hit = parent.hit;
      tried = parent.tried;
      state.mode = m;
      state.section = section;
    }
  }
  if (!hit) {
    if (tried.some((x) => x.r.status === 'incomplete')) return { output: '% Incomplete command.\n' };
    const at = Math.max(0, ...tried.map((x) => x.r.at));
    return { output: invalid(prompt, line, at) };
  }
  const e = hit.e;
  const full = canonical(e.cmd, hit.r.args);
  state.history[state.history.length - 1] = `${line}\u0000${full}`;
  setFacts(state, e);
  if (e.store !== false && !e.enters) state.config.push({ section: state.section, line: full });
  if (e.enters) {
    // Entering a child mode (its parent is the current mode) nests the section under the current one.
    const nested = state.section && parentOf(lab, e.enters) === state.mode;
    state.mode = e.enters;
    state.section = nested ? `${state.section}\n${' '.repeat(state.section.split('\n').length)}${full}` : full;
    if (e.store !== false && !state.config.some((c) => c.section === state.section)) state.config.push({ section: state.section, line: '!' });
  }
  return { output: e.reply ? (e.reply.endsWith('\n') ? e.reply : e.reply + '\n') : '' };
}

// ---------- objectives ----------

export function checkOne(lab, state, check) {
  switch (check.type) {
    case 'fact':
      return state.facts.includes(check.fact);
    case 'no-fact':
      return !state.facts.includes(check.fact);
    case 'answer':
      return state.answers.includes(normAnswer(check.value));
    case 'ran':
      return state.history.some((h) => h.split('\u0000').some((x) => new RegExp(check.pattern, 'i').test(x)));
    default:
      return false;
  }
}

export function evaluate(lab, state) {
  return lab.steps.map((step) => step.objectives.map((o) => ({ id: o.id, pass: checkOne(lab, state, o.check) })));
}

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
  if (i >= lab.steps.length) return 'Every objective is clear. The relay is holding.\n';
  const st = lab.steps[i];
  const res = evaluate(lab, state)[i];
  let s = `\nStep ${i + 1}: ${st.title}\n${st.body.replace(/`/g, '')}\n\n`;
  st.objectives.forEach((o, k) => (s += `  [${res[k].pass ? 'x' : ' '}] ${o.label}\n`));
  return s;
}

// ---------- save / restore ----------

export function snapshot(state) {
  return {
    v: 1,
    mode: state.mode,
    section: state.section,
    facts: state.facts,
    answers: state.answers,
    history: state.history.slice(-80),
    config: state.config.slice(-200),
  };
}

export function restore(lab, saved) {
  const state = createState(lab);
  if (!saved || saved.v !== 1) return state;
  const modes = ['user', 'priv', ...CONFIG_MODES(lab)];
  if (modes.includes(saved.mode)) state.mode = saved.mode;
  if (typeof saved.section === 'string' || saved.section === null) state.section = saved.section ?? null;
  const strs = (a) => (Array.isArray(a) ? a.filter((x) => typeof x === 'string') : []);
  state.facts = strs(saved.facts);
  state.answers = strs(saved.answers);
  state.history = strs(saved.history);
  state.config = Array.isArray(saved.config)
    ? saved.config.filter((c) => c && typeof c.line === 'string' && (c.section === null || typeof c.section === 'string'))
    : [];
  return state;
}
