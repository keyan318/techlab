// TechLab "Face the Mini Boss" recording: Kim (self-paced, Computer Programming 1) finishes Module 1's last
// lesson, the Mini Boss opens, she beats it in full screen and Module 2 unlocks.
// Usage: OUT=<dir> node mini-boss.mjs   (restores the practice db from mb-base.sqlite first)
import fs from 'fs';
import path from 'path';
import { launch, close, segment } from './lib.mjs';

const SCR = path.resolve('..');
fs.copyFileSync(path.join(SCR, 'mb-base.sqlite'), path.join(SCR, 'demo.sqlite'));

const KEY = JSON.parse(fs.readFileSync('/Users/keyanjaoricrima/techlab/resources/mini-bosses/computer-programming-1/m1.json', 'utf8')).questions;
const correct = Object.fromEntries(KEY.map(q => [q.prompt, q.options[q.correct.charCodeAt(0) - 65]]));
const MISS = 2; // she gets a couple wrong, like a real student

const STUDENT = { role: 'STUDENT', color: '#73b6ff' };

async function maybe(s, loc, opts) { if (await loc.count() && await loc.isVisible()) await s.click(loc, opts); }

async function cards() {
  const DIR = new URL('../cards/', import.meta.url).href;
  for (const [k, t] of Object.entries({ 'mb-intro': 6, 'mb-outro': 6.5 })) {
    const s = await segment('card-' + k, { overlay: false });
    await s.page.goto(DIR + k + '.html'); await s.page.evaluate(() => document.fonts.ready);
    await s.page.reload(); await s.page.evaluate(() => document.fonts.ready);
    await s.start(); await s.wait(t * 1000); await s.end();
  }
}

async function kim() {
  const s = await segment('mb-kim', STUDENT); const p = s.page;
  await s.go('/login'); await s.start();
  await s.cap('Kim learns self-paced. One lesson left in Module 1, then the Mini Boss.');
  await s.type('input[type=email]', 'kim.delacruz@demo.techlab.test', { delay: 35 });
  await s.type('input[type=password]', 'DemoStudent2026!', { delay: 35 });
  await s.click(p.locator('form button[type=submit]').first(), { nav: true });
  await s.wait(2000);
  await maybe(s, p.getByRole('button', { name: 'Continue', exact: true }).first());
  await maybe(s, s.btn('Skip tour'));
  await s.click(s.btn('View course'), { nav: true });

  await s.cap('Lessons 1.1 to 1.5 are done. Under them waits Module 1\'s Mini Boss, still locked.');
  const boss = () => p.locator('a[aria-label^="Chapter 1 mini boss"], li:has-text("Mini Boss")').first();
  await s.reveal(boss(), 0.55); await s.hover(boss()); await s.wait(3000);
  await s.cap('Module 2 stays locked too, until she beats the boss.');
  await s.scroll(260); await s.wait(2600);
  await s.reveal(s.btn('Resume Learning'), 0.5); await s.wait(600);
  await s.click(s.btn('Resume Learning'), { nav: true });

  await s.cap('Lesson 1.6, the last one of Module 1.');
  await s.wait(2200);
  const readIt = () => p.locator('button:visible', { hasText: "I've read it" }).first();
  const answer = (t) => p.locator('.checkpoint-block li:visible, button:visible').filter({ hasText: t }).first();
  await s.reveal(readIt(), 0.62); await s.wait(1000);
  await s.cap('She reads each stage and answers its checkpoint to open the next one.');
  await s.click(readIt()); await s.wait(700);
  await s.click(answer('scanf("%d", &age);')); await s.wait(1300);
  await s.scroll(300); await s.wait(1200);
  await s.reveal(readIt(), 0.62); await s.click(readIt()); await s.wait(700);
  await s.click(answer('It needs to know where to deliver it')); await s.wait(1300);
  await s.scroll(350); await s.wait(1000);
  await s.reveal(readIt(), 0.62); await s.click(readIt()); await s.wait(700);
  await s.click(answer('scanf("%d", &n);')); await s.wait(1600);

  await s.cap('Knowledge Check: five quick questions, +10 XP each.');
  for (const a of ['Reads a value the user types', 'The address in memory where n lives', 'scanf has no address to write into', 'So the answer stays on that line', 'Left: 25']) {
    const o = p.locator('li:visible, button:visible').filter({ hasText: a }).first();
    await s.reveal(o, 0.45); await s.wait(300); await s.click(o); await s.wait(800);
  }
  await s.cap('Last stage: she writes the C program herself.');
  const code = p.locator('button:visible', { hasText: 'Code it yourself' }).first();
  await s.reveal(code); await s.wait(900);
  await s.click(code, { nav: true });
  await s.wait(1500);
  const ta = p.locator('#code-input');
  await s.hover(ta);
  await p.evaluate(() => { const t = document.querySelector('#code-input'); const a = t.value.indexOf('    // print'); const b = t.value.lastIndexOf('newline') + 7; t.focus(); t.setSelectionRange(a, b); });
  await s.wait(400); await p.keyboard.press('Backspace'); await s.wait(300);
  for (const line of ['    printf("Enter fuel: ");', '\n    scanf("%d", &n);', '\n    printf("Left: %d\\n", n - 15);']) {
    for (const ch of line) { if (ch === '\n') await p.keyboard.press('Enter'); else await p.keyboard.type(ch); await s.wait(30 + Math.random() * 30); }
    await s.wait(250);
  }
  await s.type('#program-input', '40', { delay: 120 });
  await s.click(p.getByRole('button', { name: 'Run' })); await s.wait(2400);
  await s.click(p.getByRole('button', { name: 'Submit answer' }));
  await s.cap('Lesson done: every lesson of Module 1 is finished.');
  await s.wait(4000);

  await s.go('/student/course/programming/computer-programming-1');
  const stats = (await p.locator('li:has(a[aria-label^="Chapter 1 mini boss"]) span.text-muted').first().textContent()).trim().replace(/ · beat it to go on$/, '');
  await s.cap('Back on the course page, the Mini Boss is awake: ' + stats + '.');
  await s.reveal(boss(), 0.55); await s.wait(1500);
  await s.click(p.locator('a[aria-label^="Chapter 1 mini boss"]').first(), { nav: true });

  await s.cap('Before the fight, the rules of the arena.');
  await s.wait(2200);
  await s.hover(p.getByText('Rules of the arena').first()); await s.wait(1500);
  await s.cap('Full screen only. Leave it, switch tabs or reload, and she fails on the spot.');
  await s.scroll(250); await s.wait(3200);
  await s.cap('Win and she keeps 10 XP per right answer and Module 2 opens. Lose: no XP, and a 60-minute wait to study.');
  await s.wait(3800);
  await s.click(p.locator('label').filter({ hasText: 'I understand the rules' }).first());
  await s.wait(600);
  await s.click(s.btn('Enter full screen and start'));
  await p.locator('[data-q]').first().waitFor({ state: 'visible', timeout: 15000 });
  await s.cap('The arena: the clock runs top right, the map shows which questions she has answered.', 'top');
  await s.wait(2800);

  const qs = p.locator('[data-q]');
  const n = await qs.count();
  const miss = new Set([6, 19].slice(0, MISS));
  for (let i = 0; i < n; i++) {
    if (i === 4) await s.cap('She works through all ' + n + ' questions about Module 1.', 'top');
    if (i === 12) await s.cap('No answers are shown during the fight, only the score at the end.', 'top');
    if (i === 24) await s.cap('Almost there.', 'top');
    const q = qs.nth(i);
    const prompt = (await q.locator('p').first().locator('span').nth(1).textContent()).trim();
    const opts = await q.locator('label.opt span').allTextContents();
    let idx = opts.findIndex(o => o.trim() === correct[prompt]);
    if (idx < 0) throw new Error('no answer for: ' + prompt);
    if (miss.has(i)) idx = (idx + 1) % opts.length;
    const fast = i >= 3;
    await s.reveal(q, 0.42);
    if (!fast) await s.wait(1400);
    await s.click(q.locator('label.opt').nth(idx), { pause: fast ? 120 : 280 });
    await s.wait(fast ? 250 : 700);
  }
  await s.cap('All answered. She hands them in.', 'top');
  await s.click(s.btn('Hand in my answers'));
  await s.wait(1200);
  await s.click(s.btn('Yes, hand in'));
  await p.getByText('Mini boss defeated!').waitFor({ timeout: 15000 });
  const won = (await p.getByText(/You scored/).first().textContent()).replace(/\s+/g, ' ').trim();
  await s.cap('Victory! ' + won);
  await s.hover(p.getByText('Mini boss defeated!').first()); await s.wait(4000);

  await s.click(s.btn('Back to the course'), { nav: true });
  await s.cap('The boss is marked Beaten, and Module 2 is open.');
  await s.reveal(boss(), 0.45); await s.hover(boss()); await s.wait(2500);
  await s.scroll(300); await s.wait(3500);
  return s.end();
}

await launch();
try {
  const want = process.argv.slice(2);
  if (!want.length || want.includes('cards')) await cards();
  if (!want.length || want.includes('kim')) await kim();
} finally { await close(); }
