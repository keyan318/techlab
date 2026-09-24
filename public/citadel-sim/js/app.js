// Citadel Sim page: renders the mission and the Command Prompt, saves per student,
// and tells the TechLab lab page (same origin, parent frame) how the student is doing.
import { createState, currentStep, evaluate, missionText, progress, restore, runCommand, snapshot } from './engine.js';

const params = new URLSearchParams(location.search);
const rawLab = params.get('lab');
const labId = rawLab && /^[a-z0-9-]{1,64}$/.test(rawLab) ? rawLab : null;
const rawUser = params.get('u');
const userId = rawUser && /^[0-9]{1,20}$/.test(rawUser) ? rawUser : null;
const embedded = labId !== null && window.parent !== window;
const saveKey = labId ? `citadel:save:v1:lab:${labId}${userId ? `:u:${userId}` : ''}` : null;

const $ = (id) => document.getElementById(id);
const screen = $('screen');
const input = $('cmd');
const promptEl = $('prompt');

let lab = null;
let state = null;
let lastPassed = -1;
let reportedPass = false;
const history = [];
let hIdx = 0;

function post(msg) {
  if (!embedded) return;
  try {
    window.parent.postMessage({ source: 'citadel', lab: labId, ...msg }, location.origin);
  } catch {
    // Parent unreachable; the lab still works on its own.
  }
}

function load() {
  try {
    const raw = saveKey && localStorage.getItem(saveKey);
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

function save() {
  try {
    if (saveKey) localStorage.setItem(saveKey, JSON.stringify(snapshot(state)));
  } catch {
    // Storage blocked (private mode): progress lives for this visit only.
  }
}

// Lab text marks commands with `backticks`; build them as <code> without ever parsing HTML.
function richText(el, text) {
  el.textContent = '';
  text.split(/(`[^`]+`)/).forEach((part) => {
    if (part.startsWith('`') && part.endsWith('`') && part.length > 1) {
      const c = document.createElement('code');
      c.textContent = part.slice(1, -1);
      el.appendChild(c);
    } else if (part) {
      el.appendChild(document.createTextNode(part));
    }
  });
}

function print(text, cls) {
  if (!text) return;
  const span = document.createElement('span');
  if (cls) span.className = cls;
  span.textContent = text;
  screen.appendChild(span);
  screen.scrollTop = screen.scrollHeight;
}

function promptText() {
  return `${state.cwd.replace(/\\$/, '')}>`;
}

const WHO = { astro: 'Astro', rivet: 'Rivet', volt: 'Volt' };

function renderMission() {
  const res = evaluate(lab, state);
  const cur = currentStep(lab, state);
  const list = $('steps');
  list.textContent = '';
  lab.steps.forEach((st, i) => {
    const li = document.createElement('li');
    li.className = 'step ' + (i < cur ? 'done' : i === cur ? 'current' : 'locked');
    const head = document.createElement('div');
    head.className = 'step-head';
    const who = document.createElement('span');
    who.className = 'who ' + (st.by || 'astro');
    who.textContent = WHO[st.by] || 'Crew';
    const title = document.createElement('span');
    title.textContent = `${i + 1}. ${st.title}`;
    head.append(who, title);
    li.appendChild(head);
    if (i <= cur) {
      const body = document.createElement('p');
      body.className = 'step-body';
      richText(body, st.body);
      li.appendChild(body);
      const ul = document.createElement('ul');
      ul.className = 'objectives';
      st.objectives.forEach((o, k) => {
        const item = document.createElement('li');
        const pass = res[i][k].pass;
        item.className = pass ? 'pass' : '';
        const tick = document.createElement('span');
        tick.className = 'tick';
        tick.textContent = pass ? '✓' : '';
        tick.setAttribute('aria-hidden', 'true');
        const label = document.createElement('span');
        richText(label, o.label);
        item.append(tick, label);
        item.setAttribute('aria-label', `${pass ? 'Done' : 'Not done'}: ${o.label}`);
        ul.appendChild(item);
      });
      li.appendChild(ul);
    }
    list.appendChild(li);
  });
  const { passed, total } = progress(lab, state);
  $('progress').textContent = `Objectives: ${passed} / ${total}`;
  const alert = $('alert');
  if (passed === total) {
    alert.textContent = 'ALL CLEAR · Attack repelled';
    alert.classList.add('calm');
  } else {
    alert.textContent = lab.story?.alert || 'ALERT';
    alert.classList.remove('calm');
  }
  promptEl.textContent = promptText();
}

function report(fromCommand) {
  const { passed, total } = progress(lab, state);
  if (passed === total) {
    if (!reportedPass) {
      reportedPass = true;
      post({ type: 'lab-passed', passed, total });
      if (fromCommand) showVictory();
    }
    return;
  }
  if (passed !== lastPassed) {
    post({ type: 'lab-progress', passed, total });
    const failing = evaluate(lab, state)
      .flatMap((r, i) => r.map((o, k) => (o.pass ? null : lab.steps[i].objectives[k].label)))
      .filter(Boolean);
    post({ type: 'lab-failed', passed, total, failing });
  }
  lastPassed = passed;
}

// ---------- Ask Astro ----------
// Live help from Astro. This frame has the lab state but no login session, so every call goes up to the
// TechLab lab page (parent), which makes the authenticated request and posts the reply back with our reqId.
// Astro is only sent what the student can already see: the current orders and the terminal transcript.

const ASTRO_TIMEOUT_MS = 90000;
const MAX_CONTEXT = 7800;   // the server accepts up to 8000
const MAX_HISTORY = 20;     // the server accepts up to 20 earlier messages
const astro = { status: null, history: [], busy: false, seq: 0, pending: new Map() };

function askParent(type, payload) {
  return new Promise((resolve) => {
    const reqId = `${type}-${++astro.seq}`;
    const timer = setTimeout(() => {
      astro.pending.delete(reqId);
      resolve({ ok: false, error: "Astro didn't answer in time. Try again." });
    }, ASTRO_TIMEOUT_MS);
    astro.pending.set(reqId, (res) => {
      clearTimeout(timer);
      resolve(res);
    });
    post(payload ? { type, reqId, payload } : { type, reqId });
  });
}

window.addEventListener('message', (e) => {
  if (!embedded || e.origin !== location.origin || e.source !== window.parent) return;
  const m = e.data;
  if (!m || m.source !== 'citadel' || m.lab !== labId || !m.reqId) return;
  const done = astro.pending.get(m.reqId);
  if (done) {
    astro.pending.delete(m.reqId);
    done(m);
  }
});

function astroContext() {
  const head = `Lab: ${lab.title}\n\n${missionText(lab, state).trim()}\n\nCurrent folder: ${state.cwd}\n\nTerminal transcript (oldest first):\n`;
  const room = Math.max(0, MAX_CONTEXT - head.length);
  const transcript = screen.textContent.trim();
  return head + (transcript.length > room ? '...' + transcript.slice(transcript.length - room + 3) : transcript);
}

function astroMessage(text, cls) {
  const p = document.createElement('p');
  p.className = `astro-msg ${cls}`;
  if (cls === 'astro') {
    // Keep Astro's `commands` as code; drop markdown the panel doesn't render.
    const clean = text
      .replace(/```[a-z]*\n?([\s\S]*?)```/gi, (_, code) => '`' + code.trim() + '`')
      .replace(/\*\*(.+?)\*\*/g, '$1')
      .trim();
    richText(p, clean);
  } else {
    p.textContent = text;
  }
  $('astro-log').appendChild(p);
  $('astro-log').scrollTop = $('astro-log').scrollHeight;
  return p;
}

function plainHint(html) {
  const div = document.createElement('div');
  div.innerHTML = html || '';
  return div.textContent.trim();
}

function renderAstroGate(res) {
  const gateText = $('astro-gate-text');
  const unlock = $('astro-unlock');
  const hint = $('astro-gate-hint');
  unlock.hidden = true;
  unlock.disabled = false;
  hint.hidden = true;

  const data = res && res.data;
  if (!data || typeof data.cost !== 'number') {
    gateText.textContent = (res && res.error) || "Astro couldn't be reached. Try again in a moment.";
    astro.status = null;   // let the next open try again
    return;
  }

  astro.status = data;
  if (data.unlocked) {
    $('astro-gate').hidden = true;
    $('astro-chat').hidden = false;
    $('astro-toggle-sub').textContent = 'Unlocked for this lab. Ask as many questions as you need.';
    if (!$('astro-log').childElementCount) {
      astroMessage("I can see your terminal and your orders. Tell me what's going wrong, and I'll tell you what to type next.", 'astro');
    }
    $('astro-question').focus();
    return;
  }

  $('astro-gate').hidden = false;
  $('astro-chat').hidden = true;
  if (data.canAfford) {
    gateText.textContent = `Astro reads your terminal and walks you through your next step. It costs ${data.cost} XP, once for this lab, then you can ask as many questions as you need. You have ${data.balance} XP.`;
    unlock.textContent = `Unlock for ${data.cost} XP`;
    unlock.hidden = false;
  } else {
    gateText.textContent = `Astro's help costs ${data.cost} XP and you have ${data.balance}. Finish lessons and quiz questions to earn more.`;
    const simHint = plainHint(res.simHint);
    hint.textContent = `${simHint ? simHint + ' ' : ''}Tip: type mission to see your orders again, or help to list every command.`;
    hint.hidden = false;
  }
}

async function loadAstroStatus() {
  $('astro-gate').hidden = false;
  $('astro-chat').hidden = true;
  $('astro-gate-text').textContent = 'Checking your XP...';
  $('astro-unlock').hidden = true;
  renderAstroGate(await askParent('astro-status-request'));
}

function initAstro() {
  if (!embedded) return;   // standalone: no login session, so no paid help
  $('astro-help').hidden = false;

  $('astro-toggle').addEventListener('click', () => {
    const panel = $('astro-panel');
    panel.hidden = !panel.hidden;
    $('astro-toggle').setAttribute('aria-expanded', String(!panel.hidden));
    if (!panel.hidden && !astro.status) loadAstroStatus();
  });

  $('astro-unlock').addEventListener('click', async () => {
    const cost = astro.status ? astro.status.cost : '';
    if (!confirm(`Spend ${cost} XP to unlock Astro's help for this lab?`)) return;
    $('astro-unlock').disabled = true;
    $('astro-gate-text').textContent = 'Unlocking...';
    const res = await askParent('astro-unlock-request');
    renderAstroGate(res.data && typeof res.data.cost === 'number' ? res : { error: res.error });
  });

  $('astro-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const q = $('astro-question');
    const question = q.value.trim();
    if (!question || astro.busy) return;

    astro.busy = true;
    q.value = '';
    q.disabled = true;
    $('astro-send').disabled = true;
    astroMessage(question, 'user');
    const pending = astroMessage('Astro is reading your terminal...', 'pending');

    const res = await askParent('astro-ask-request', {
      question,
      context: astroContext(),
      history: astro.history.slice(-MAX_HISTORY),
    });

    pending.remove();
    if (res.ok && res.data && res.data.answer) {
      astroMessage(res.data.answer, 'astro');
      astro.history.push({ role: 'user', content: question }, { role: 'assistant', content: res.data.answer });
      astro.history = astro.history.slice(-MAX_HISTORY);
    } else {
      astroMessage(res.error || "Astro couldn't respond. Try again.", 'error');
      if (res.data && /unlock/i.test(res.data.error || '')) loadAstroStatus();
    }

    astro.busy = false;
    q.disabled = false;
    $('astro-send').disabled = false;
    q.focus();
  });
}

function showVictory() {
  $('victory-text').textContent = lab.story?.victory || 'Every objective is clear.';
  $('victory').hidden = false;
  $('victory-close').focus();
}

function run(line) {
  print(`${promptText()}${line}\n`, 'echo');
  const res = runCommand(lab, state, line);
  if (res.clear) screen.textContent = '';
  else print(res.output);
  save();
  renderMission();
  report(true);
}

function boot() {
  $('lab-title').textContent = lab.title;
  $('console-title').textContent = `Command Prompt · ${lab.host?.name || 'CITADEL-CORE'}`;
  if (lab.story?.vex) {
    $('vex-text').textContent = lab.story.vex;
    $('vex').hidden = false;
  }
  state = restore(lab, load());
  print(`Microsoft Windows [Version 10.0.22631] · Citadel Sim\nSigned in as ${lab.host?.user || 'cadet'} on ${lab.host?.name || 'CITADEL-CORE'}.\nType help for commands. Start with: type README.txt\n\n`, 'sys');
  renderMission();
  lastPassed = progress(lab, state).passed;
  // Already solved on an earlier visit: tell the page again (saving is idempotent), no popup.
  report(false);
  initAstro();
  input.focus();
}

$('prompt-form').addEventListener('submit', (e) => {
  e.preventDefault();
  const line = input.value;
  input.value = '';
  if (line.trim()) {
    history.push(line);
    hIdx = history.length;
  }
  run(line);
});

input.addEventListener('keydown', (e) => {
  if (e.key === 'ArrowUp' && hIdx > 0) {
    input.value = history[--hIdx];
    e.preventDefault();
  } else if (e.key === 'ArrowDown') {
    hIdx = Math.min(history.length, hIdx + 1);
    input.value = history[hIdx] || '';
    e.preventDefault();
  }
});

screen.addEventListener('click', () => {
  if (!window.getSelection()?.toString()) input.focus();
});

$('reset').addEventListener('click', () => {
  if (!confirm('Restart this lab from the beginning? Your progress in this lab is cleared.')) return;
  try {
    if (saveKey) localStorage.removeItem(saveKey);
  } catch {
    // nothing saved
  }
  state = createState(lab);
  reportedPass = false;
  lastPassed = -1;
  screen.textContent = '';
  // The old chat was about the old transcript. The unlock itself is kept (it's paid for on the server).
  astro.history = [];
  $('astro-log').textContent = '';
  print('Lab restarted. Type help for commands. Start with: type README.txt\n\n', 'sys');
  renderMission();
  input.focus();
});

$('victory-close').addEventListener('click', () => {
  $('victory').hidden = true;
  input.focus();
});

if (!labId) {
  $('lab-title').textContent = 'No lab selected';
  print('Open this simulator from a lesson\'s "Defend it yourself" button.\n', 'sys');
  input.disabled = true;
} else {
  fetch(`labs/${labId}.json`, { cache: 'no-cache' })
    .then((r) => {
      if (!r.ok) throw new Error(`HTTP ${r.status}`);
      return r.json();
    })
    .then((data) => {
      lab = data;
      boot();
    })
    .catch((err) => {
      $('lab-title').textContent = 'Lab could not load';
      print(`The lab file for "${labId}" could not be loaded (${err.message}). Go back to the lesson and try again.\n`, 'sys');
      input.disabled = true;
    });
}
