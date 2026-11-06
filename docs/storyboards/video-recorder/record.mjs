// TechLab workflow recording: student (sign-up + a lesson) -> faculty -> admin.
// Usage: OUT=<dir> node record.mjs [mika leo carlo ana]   (default: all, in order)
// Each part restores the practice-db snapshot left by the part before it.
import fs from 'fs';
import path from 'path';
import { launch, close, segment } from './lib.mjs';

const SCR = path.resolve('..');
const DB = path.join(SCR, 'demo.sqlite');
const ORDER = ['mika', 'leo', 'carlo', 'ana'];
const snap = (i) => path.join(SCR, i === 0 ? 'demo-pristine.sqlite' : `demo-after-${ORDER[i - 1]}.sqlite`);

const STUDENT = { role: 'STUDENT', color: '#73b6ff' };
const FACULTY = { role: 'FACULTY', color: '#ffc94a' };
const ADMIN = { role: 'ADMIN', color: '#b99cff' };

async function maybe(s, loc, opts) { if (await loc.count() && await loc.isVisible()) await s.click(loc, opts); }

const parts = {
  async mika() {
    const s = await segment('1-mika', STUDENT); const p = s.page;
    await s.go('/'); await s.start();
    await s.cap('A new student, Mika, opens TechLab: three planets to explore.');
    await s.wait(2200);
    await s.click(s.btn('Start your mission'), { nav: true });
    await s.cap('She signs up with her name, email and a password.');
    await s.type('[name=first_name]', 'Mika');
    await s.type('[name=last_name]', 'Santiago');
    await s.type('[name=email]', 'mika.santiago@demo.techlab.test', { delay: 35 });
    await s.type('[name=password]', 'DemoStudent2026!', { delay: 35 });
    await s.type('[name=password_confirmation]', 'DemoStudent2026!', { delay: 35 });
    await s.cap('She picks her program, her year level and her first planet: Programming.');
    await s.hover('[name=program]'); await p.selectOption('[name=program]', 'IT'); await s.wait(500);
    await s.hover('[name=year_level]'); await p.selectOption('[name=year_level]', '1'); await s.wait(500);
    await s.click(p.getByText('Programming', { exact: true }));
    await s.wait(500);
    await s.click(s.btn('Create account'), { nav: true });

    await s.cap('Before anything else she reads TechLab\'s rules and agrees to them.');
    await s.wait(1500);
    await s.scroll(500); await s.wait(900); await s.scroll(500); await s.wait(900);
    await s.click(p.locator('label:visible').filter({ hasText: /I have read/i }).first());
    await s.click(s.btn('Agree and continue'), { nav: true });

    await s.cap('Astro, TechLab\'s AI tutor, welcomes her aboard.');
    for (let i = 0; i < 6; i++) {
      await s.wait(2300);
      if (await s.btn('Make my astronaut').isVisible()) break;
      await s.click(s.btn('Continue'));
    }
    await s.click(s.btn('Make my astronaut'));
    await s.cap('She builds her astronaut: animal, coat, suit and helmet.');
    await s.wait(1200);
    await s.click(s.btn('Midnight')); await s.wait(900);
    await s.click(s.btn('Suit')); await s.wait(1300);
    await s.click(s.btn('Helmet')); await s.wait(1300);
    await s.click(s.btn('Animal')); await s.wait(800);
    await s.click(s.btn('Looks good!'));
    await s.cap('She picks a codename. Classmates see the codename, not her real name.');
    await s.wait(1000);
    await s.type(p.locator('input[type=text]:visible').first(), 'NovaMika', { clear: true, delay: 90 });
    await s.wait(1500);
    await s.click(s.btn("That's me!"));
    await s.wait(1800);
    await s.click(s.btn('Start my mission'), { nav: true });

    await s.cap('Her dashboard opens with Astro\'s tour of where everything is.');
    await s.wait(2200); await s.click(s.btn('Next')); await s.wait(2000); await s.click(s.btn('Next')); await s.wait(1800);
    await s.click(s.btn('Skip tour')); await s.wait(800);
    await s.cap('Two ways to take a course: Self-paced on her own, or Join Class with an instructor.');
    await s.hover(s.btn('Self-paced')); await s.wait(1800);
    await s.click(s.btn('Join Class')); await s.wait(900);
    await s.cap('She picks Captain Carlo Reyes\' class and asks to join.');
    await s.wait(1500);
    await s.click(s.btn('Ask to join'));
    await s.wait(1500);
    await s.cap('Now she waits for Carlo to accept her. (We\'ll see his side in Part 2.)');
    await s.hover(p.getByText(/Waiting for Carlo/).first()); await s.wait(3500);
    return s.end();
  },

  async leo() {
    const s = await segment('2-leo', STUDENT); const p = s.page;
    await s.go('/login'); await s.start();
    await s.cap('Meanwhile Leo, already in Carlo\'s class, logs in to keep learning.');
    await s.type('input[type=email]', 'leo.cruz@demo.techlab.test', { delay: 35 });
    await s.type('input[type=password]', 'DemoStudent2026!', { delay: 35 });
    await s.click(p.locator('form button[type=submit]').first(), { nav: true });
    await s.cap('Logging in every day keeps his streak alive: +10 XP daily bonus.');
    await s.wait(2600);
    await maybe(s, p.getByRole('button', { name: 'Continue', exact: true }).first());
    await s.cap('His dashboard shows where he left off, his level and the leaderboard.');
    await s.wait(1500); await s.scroll(600); await s.wait(1500); await s.scroll(-600); await s.wait(800);
    await s.click(s.btn('View course'), { nav: true });

    await s.cap('The course page: every module and lesson, his progress and the XP he can earn.');
    await s.wait(1800); await s.scroll(450); await s.wait(1400); await s.scroll(450); await s.wait(1400);
    await s.click(s.btn('Resume Learning'), { nav: true });

    await s.cap('Lesson 1.6 opens. Each lesson has 5 stages that unlock one by one.');
    await s.wait(2500);
    const readIt = () => p.locator('button:visible', { hasText: "I've read it" }).first();
    const answer = (t) => p.locator('.checkpoint-block li:visible, button:visible').filter({ hasText: t }).first();
    await s.reveal(readIt(), 0.62); await s.wait(1800);
    await s.cap('Stage 1, Mission Brief. When he\'s done reading, a checkpoint question unlocks Stage 2.');
    await s.click(readIt()); await s.wait(900);
    await s.click(answer('scanf("%d", &age);')); await s.wait(1600);
    await s.cap('Stage 2, Picture It: the idea as an everyday comparison with a drawing.');
    await s.wait(2600); await s.scroll(300); await s.wait(2600);
    await s.reveal(readIt(), 0.62); await s.click(readIt()); await s.wait(900);
    await s.click(answer('It needs to know where to deliver it')); await s.wait(2000);
    await s.cap('Stage 3: a diagram and a real code example, then one more checkpoint.');
    await s.wait(1500); await s.scroll(350); await s.wait(1500);
    await s.reveal(readIt(), 0.62); await s.click(readIt()); await s.wait(900);
    await s.click(answer('scanf("%d", &n);')); await s.wait(2200);

    await s.cap('Stage 4, Knowledge Check: one try per question, +10 XP for each right answer.');
    for (const a of ['Reads a value the user types', 'The address in memory where n lives', 'scanf has no address to write into', 'So the answer stays on that line', 'Left: 25']) {
      const o = p.locator('li:visible, button:visible').filter({ hasText: a }).first();
      await s.reveal(o, 0.45); await s.wait(500); await s.click(o); await s.wait(1100);
    }
    await s.cap('Stage 5: now he writes real C code himself.');
    const code = p.locator('button:visible', { hasText: 'Code it yourself' }).first();
    await s.reveal(code); await s.wait(1200);
    await s.click(code, { nav: true });

    await s.cap('The code editor runs C right in the browser. Instructions on the left.');
    await s.wait(2500);
    const ta = p.locator('#code-input');
    await s.hover(ta);
    await p.evaluate(() => { const t = document.querySelector('#code-input'); const a = t.value.indexOf('    // print'); const b = t.value.lastIndexOf('newline') + 7; t.focus(); t.setSelectionRange(a, b); });
    await s.wait(500); await p.keyboard.press('Backspace'); await s.wait(400);
    for (const line of ['    printf("Enter fuel: ");', '\n    scanf("%d", &n);', '\n    printf("Left: %d\\n", n - 15);']) {
      for (const ch of line) { if (ch === '\n') await p.keyboard.press('Enter'); else await p.keyboard.type(ch); await s.wait(45 + Math.random() * 40); }
      await s.wait(400);
    }
    await s.cap('He types 40 as the program\'s input and clicks Run.');
    await s.type('#program-input', '40', { delay: 150 });
    await s.click(p.getByRole('button', { name: 'Run' })); await s.wait(3200);
    await s.cap('The output matches. He clicks Submit answer.');
    await s.wait(1200);
    await s.click(p.getByRole('button', { name: 'Submit answer' }));
    await s.cap('Mission accomplished: +100 XP, and Module 1 is complete.');
    await s.wait(4500);
    return s.end();
  },

  async carlo() {
    const s = await segment('3-carlo', FACULTY); const p = s.page;
    await s.go('/faculty/login'); await s.start();
    await s.cap('Faculty have their own sign-in. Carlo Reyes is a Captain: he teaches a class.');
    await s.type('input[type=email]', 'faculty.demo@catsu.edu.ph', { delay: 35 });
    await s.type('input[type=password]', 'CarloDemo2026!', { delay: 35 });
    await s.click(p.locator('form button[type=submit]').first(), { nav: true });
    await maybe(s, s.btn('Skip tour'));
    await s.cap('His dashboard: how his students are doing. Astro says 1 student is waiting.');
    await s.wait(2000);
    await s.hover(p.getByText('on your rosters').first()); await s.wait(1500);
    await s.hover(p.getByText('Activity', { exact: true }).first()); await s.wait(1200);
    await s.scroll(500); await s.wait(1800);
    await s.cap('He sees every student\'s progress, and who needs a nudge.');
    await s.scroll(500); await s.wait(2000);
    const acc = p.locator('button:visible', { hasText: 'Accept' }).first();
    await s.reveal(acc, 0.6);
    await s.cap('Mika is waiting to join his class. He clicks Accept.');
    await s.wait(2200);
    await s.click(acc, { nav: true });
    await s.cap('She\'s in: 5 students now. Her course opens for her right away.');
    await s.wait(2600);

    await s.click(p.locator('a[href$="/faculty/crew"]').first(), { nav: true });
    await s.wait(1000);
    await s.click(p.locator('a[href$="/faculty/courses/1/roster"]').filter({ hasText: 'Open crew' }).first(), { nav: true });
    await s.cap('His crew: XP, gems, streaks and progress. Leo\'s new lesson already shows.');
    await s.wait(1500); await s.scroll(250); await s.wait(3000);
    const tab = (href) => p.locator(`a[href$="${href}"]:visible`).filter({ hasNotText: /^\s*$/ }).last();
    await s.click(tab('/faculty/courses/1'), { nav: true });
    await s.cap('Materials: files and links he adds reach his students, and Astro can answer from them.');
    await s.wait(2500); await s.scroll(300); await s.wait(1500); await s.scroll(-300);
    await s.click(tab('/faculty/courses/1/lesson-quizzes'), { nav: true });
    await s.cap('Lesson quizzes: he can edit any lesson\'s quiz for his class.');
    await s.wait(2000); await s.scroll(400); await s.wait(1500); await s.scroll(-400);
    await s.click(tab('/faculty/courses/1/exams'), { nav: true });
    await s.cap('Exams: the Final Boss and a Mini Boss after every module, with every score.');
    await s.wait(2500); await s.scroll(450); await s.wait(1800); await s.scroll(450); await s.wait(2000);

    await s.go('/student/planet/computer-programming-1/m1/lesson06');
    await s.cap('He can also edit lessons in place. His edits reach only his class.');
    await s.wait(2000);
    const ed = p.locator('button:visible', { hasText: 'Edit checkpoint' }).first();
    await s.reveal(ed, 0.5); await s.wait(800);
    await s.click(ed); await s.wait(1000);
    await s.cap('He rewrites a checkpoint question and saves it.');
    await s.type(p.locator('textarea:visible').first(), 'Which line reads a whole number from the keyboard into `age`?', { clear: true, delay: 40 });
    await s.wait(800);
    await s.click(p.getByRole('button', { name: 'Save', exact: true }));
    await s.wait(3000); await p.waitForLoadState('load'); await s.wait(1500);
    await s.cap('Saved. Self-paced students still get the standard TechLab lesson.');
    await s.move(1000, 500, 400); await s.scroll(560); await s.wait(400);
    await s.reveal(p.locator('button:visible', { hasText: 'Edit checkpoint' }).first(), 0.45);
    await s.wait(3000);
    return s.end();
  },

  async ana() {
    const s = await segment('4-ana', ADMIN); const p = s.page;
    await s.go('/admin/login'); await s.start();
    await s.cap('The admin runs TechLab as a whole. Ana signs in on the admin page.');
    await s.type('input[type=email]', 'admin.demo@techlab.test', { delay: 35 });
    await s.type('input[type=password]', 'AnaAdminDemo2026!', { delay: 35 });
    await s.click(p.locator('form button[type=submit]').first(), { nav: true });
    await s.cap('The Command deck: students, faculty, lessons finished and who\'s active.');
    await s.wait(2500);
    await s.hover(p.getByText('Community growth').first()); await s.wait(1200);
    await s.click(s.btn('Learning')); await s.wait(2200);
    await s.click(s.btn('Growth')); await s.wait(1200);
    await s.scroll(400); await s.wait(2000); await s.scroll(-400);

    await s.click(p.locator('a[href$="/admin"]').first(), { nav: true });
    await s.cap('Course Assignment: she decides which faculty teach which course.');
    await s.wait(2200);
    const row = p.locator('tr').filter({ hasText: 'Computer Programming 2' }).first();
    const sel = row.locator('select');
    await s.cap('She assigns Joy Bautista to Computer Programming 2.');
    await s.hover(sel); await s.wait(500);
    await sel.selectOption({ label: 'Joy Bautista' }); await s.wait(900);
    await s.click(row.getByRole('button', { name: 'Add' }), { nav: true });
    await s.cap('Joy now has her own Computer Programming 2 class, ready for students to join.');
    await s.reveal(p.locator('tr').filter({ hasText: 'Computer Programming 2' }).first(), 0.45);
    await s.wait(3000);

    await s.click(p.locator('a[href$="/admin/classes"]').first(), { nav: true });
    await s.cap('Classes: every faculty member\'s class, its class code and its students.');
    await s.wait(2200);
    const cp1 = p.locator('a:visible, button:visible').filter({ hasText: 'Carlo Reyes' }).filter({ hasText: 'Q1SF4' }).first();
    if (await cp1.count()) { await s.click(cp1); await s.wait(2800); await maybe(s, s.btn('Close')); }
    await s.wait(800);

    await s.click(p.locator('a[href$="/admin/self-paced"]').first(), { nav: true });
    await s.cap('Self-paced: students learning on their own, from studying to passing the final.');
    await s.wait(2500); await s.scroll(450); await s.wait(2000); await s.scroll(-450); await s.wait(1000);
    await s.cap('Student learns, faculty teaches, admin oversees: that\'s TechLab.');
    await s.wait(3000);
    return s.end();
  },
};

const want = process.argv.slice(2).length ? process.argv.slice(2) : ORDER;
await launch();
try {
  for (const name of want) {
    const i = ORDER.indexOf(name);
    fs.copyFileSync(snap(i), DB);
    await parts[name]();
    fs.copyFileSync(DB, snap(i + 1));
  }
} finally { await close(); }
