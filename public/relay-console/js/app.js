// Relay Console page: renders the mission and the router console, saves per student, and tells the
// TechLab lab page (same origin, parent frame) how the student is doing. Modelled on Citadel Sim's page.
import { createState, currentStep, evaluate, progress, promptText, restore, runCommand, snapshot } from './engine.js';

const params = new URLSearchParams(location.search);
const rawLab = params.get('lab');
const labId = rawLab && /^[a-z0-9-]{1,64}$/.test(rawLab) ? rawLab : null;
// TechLab passes ?file= when the course's faculty member rewrote this lab (or is test-playing a draft of it in
// the lab editor): load their version instead.
const rawFile = params.get('file');
const labFile = rawFile && /^\/(student\/planet\/[a-z0-9-]+\/lab\/m\d+\/lesson\d+\/file|faculty\/course-editor\/[a-z0-9-]+\/sim-lab-draft\/[A-Za-z0-9]{40})\.json$/.test(rawFile) ? rawFile : null;
const rawUser = params.get('u');
const userId = rawUser && /^[0-9]{1,20}$/.test(rawUser) ? rawUser : null;
const embedded = labId !== null && window.parent !== window;
const saveKey = labId ? `relay-console:save:v1:lab:${labId}${userId ? `:u:${userId}` : ''}` : null;

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
    window.parent.postMessage({ source: 'console', lab: labId, ...msg }, location.origin);
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
    alert.textContent = 'RELAY ONLINE · All objectives clear';
    alert.classList.add('calm');
  } else {
    alert.textContent = lab.story?.alert || 'RELAY CONSOLE';
    alert.classList.remove('calm');
  }
  promptEl.textContent = promptText(lab, state);
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

function showVictory() {
  $('victory-text').textContent = lab.story?.victory || 'Every objective is clear.';
  $('victory').hidden = false;
  $('victory-close').focus();
}

function run(line) {
  print(`${promptText(lab, state)}${line}\n`, 'echo');
  const res = runCommand(lab, state, line);
  if (res.clear) screen.textContent = '';
  else print(res.output);
  save();
  renderMission();
  report(true);
}

function boot() {
  $('lab-title').textContent = lab.title;
  $('console-title').textContent = `Console · ${lab.host?.name || 'Router'} (${lab.host?.platform || 'Cisco IOS'})`;
  state = restore(lab, load());
  print(`${lab.host?.banner || 'Cisco IOS Software, Relay Console for TechLab'}\n\nPress RETURN to get started. Type ? to list the commands you can use here, or mission for your orders.\n\n`, 'sys');
  renderMission();
  lastPassed = progress(lab, state).passed;
  report(false);
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
  print('Lab restarted. Type ? to list commands, or mission for your orders.\n\n', 'sys');
  renderMission();
  input.focus();
});

$('victory-close').addEventListener('click', () => {
  $('victory').hidden = true;
  input.focus();
});

if (!labId) {
  $('lab-title').textContent = 'No lab selected';
  print('Open this console from a lesson\'s "Configure it yourself" button.\n', 'sys');
  input.disabled = true;
} else {
  fetch(labFile || `labs/${labId}.json`, { cache: 'no-cache', credentials: 'same-origin' })
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
