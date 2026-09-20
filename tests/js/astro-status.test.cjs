// Run with:  node tests/js/astro-status.test.cjs
// Plain assert, no framework — the status engine is a pure module.
// (.cjs because package.json is "type": "module".) The engine is loaded by evaluating
// the exact file the browser gets, so this tests what actually ships.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const mod = { exports: {} };
new Function('module', fs.readFileSync(path.join(__dirname, '../../public/js/astro-status.js'), 'utf8'))(mod);
const S = mod.exports;

let passed = 0;
const test = (name, fn) => { fn(); passed++; console.log('  ok  ' + name); };
const lesson = [{ planet: 'programming', module: 'M1', lesson: 'lesson-01' }];

console.log('classify — each context from the spec');
test('simple question -> simple', () => {
  assert.equal(S.classify("Explain HTTP like I'm five"), 'simple');
  assert.equal(S.classify('What is 2+2?'), 'simple');
});
test('programming -> code', () => {
  assert.equal(S.classify('Help me debug my first Laravel route'), 'code');
  assert.equal(S.classify('How do I print a message in Python?'), 'code');
  assert.equal(S.classify('what does `for x in items` do'), 'code');
});
test('planet context -> planet', () => {
  assert.equal(S.classify('Quiz me on basic networking'), 'planet');
  assert.equal(S.classify('Tell me about the planets'), 'planet');
});
test('lesson attached -> lesson (beats everything else)', () => {
  assert.equal(S.classify('what is a variable', lesson), 'lesson');
  assert.equal(S.classify('Quiz me on basic networking', lesson), 'lesson');
  assert.equal(S.classify('hi', []), 'simple'); // empty sources = no lesson
});
test('deep reasoning -> deep', () => {
  assert.equal(S.classify('Why does light bend when it passes through water and how does that relate to lenses?'), 'deep');
});
test('short "why" is not deep', () => {
  assert.equal(S.classify('why is the sky blue'), 'simple');
});
test('long multi-part problem -> complex', () => {
  const long = 'I have a group project where we need to plan a schedule for five people across three different time zones and I am not sure where to start with it at all right now';
  assert.equal(S.classify(long), 'complex');
  assert.equal(S.classify('What is a pixel? And what is a byte?'), 'complex');
});
test('empty / junk input never throws', () => {
  assert.equal(S.classify(''), 'simple');
  assert.equal(S.classify(null, null), 'simple');
  assert.equal(S.classify(undefined, 'nope'), 'simple');
});

console.log('lead lines — exactly what the spec asks for');
test('first line per context', () => {
  const lead = { simple: 'Looking for a clue...', code: 'Connecting the dots...', complex: 'Putting the puzzle together...',
                 lesson: 'Checking your lesson...', planet: 'Exploring Codexia...', deep: 'Thinking it through...' };
  for (const [ctx, line] of Object.entries(lead)) assert.equal(S.sequence(ctx, 'x')[0], line, ctx);
});

console.log('determinism + sequence shape');
test('same input -> same output, every time', () => {
  const a = JSON.stringify(S.sequence('code', 'why is my loop skipping'));
  for (let i = 0; i < 5; i++) assert.equal(JSON.stringify(S.sequence('code', 'why is my loop skipping')), a);
  assert.deepEqual(S.at('lesson', 'q', 4000), S.at('lesson', 'q', 4000));
});
test('different questions vary the middle lines but keep the same lead', () => {
  const a = S.sequence('lesson', 'alpha'), b = S.sequence('lesson', 'zeta question');
  assert.equal(a[0], b[0]);
  assert.deepEqual([...a].sort(), [...b].sort());
});
test('no duplicate lines, tail always last', () => {
  for (const ctx of Object.keys(S.POOLS)) {
    const seq = S.sequence(ctx, 'q');
    assert.equal(new Set(seq).size, seq.length, ctx + ' has duplicates');
    assert.deepEqual(seq.slice(-2), S.TAIL);
  }
});
test('every line is a short, plain "…..." sentence', () => {
  for (const ctx of Object.keys(S.POOLS)) for (const line of S.sequence(ctx, 'q')) {
    assert.match(line, /^[A-Z][A-Za-z' ]+\.\.\.$/, line);
    assert.ok(line.length <= 40, line);
  }
});

console.log('timing');
test('advances one line per STEP_MS and then holds on the last line', () => {
  const seq = S.sequence('simple', 'q');
  assert.equal(S.at('simple', 'q', 0).text, seq[0]);
  assert.equal(S.at('simple', 'q', S.STEP_MS - 1).text, seq[0]);
  assert.equal(S.at('simple', 'q', S.STEP_MS).text, seq[1]);
  assert.equal(S.at('simple', 'q', 10 * 60 * 1000).text, seq[seq.length - 1]);
});
test('thinking first, then exploring — sooner for lesson/planet', () => {
  assert.equal(S.at('simple', 'q', 0).state, 'thinking');
  assert.equal(S.at('simple', 'q', 2000).state, 'thinking');
  assert.equal(S.at('simple', 'q', 2600).state, 'exploring');
  assert.equal(S.at('lesson', 'q', 1300).state, 'exploring');
  assert.equal(S.at('planet', 'q', 1300).state, 'exploring');
  assert.equal(S.at('code', 'q', 1300).state, 'thinking');
});
test('first() is never blank', () => {
  assert.ok(S.first('', null).text.length > 0);
});

console.log('track — live updates, settle, stop');
test('emits only on change; settle() -> responding then stops', () => {
  let t = 0; const events = [];
  const timers = [];
  const realSet = global.setInterval, realClear = global.clearInterval;
  global.setInterval = (fn) => { timers.push(fn); return timers.length; };
  global.clearInterval = () => {};
  try {
    const tr = S.track('Help me debug my loop', null, (s) => events.push(s), { now: () => t, tickMs: 400 });
    const tick = () => timers[0]();
    tick(); assert.equal(events.length, 0, 'no change at t=0');
    t = 2600; tick(); assert.equal(events.length, 1); assert.equal(events[0].state, 'exploring');
    tick(); assert.equal(events.length, 1, 'unchanged tick emits nothing');
    t = 3300; tick(); assert.equal(events.length, 2); assert.notEqual(events[1].text, events[0].text);
    tr.settle(); assert.equal(events[events.length - 1].state, 'responding');
    const n = events.length; t = 99999; tick(); assert.equal(events.length, n, 'stopped after settle');
  } finally { global.setInterval = realSet; global.clearInterval = realClear; }
});

console.log('settleDom');
test('is a safe no-op outside a browser', () => {
  assert.doesNotThrow(() => S.settleDom());
  assert.doesNotThrow(() => S.settleDom({}));
});

console.log('\n' + passed + ' tests passed');
