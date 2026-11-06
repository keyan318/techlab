import { launch, close, segment } from './lib.mjs';
const DIR = new URL('../cards/', import.meta.url).href;
const secs = { intro: 6, p1: 4.5, p2: 4.5, p3: 4.5, outro: 6.5 };
await launch();
for (const [k, t] of Object.entries(secs)) {
  const s = await segment('card-' + k, { overlay: false });
  await s.page.goto(DIR + k + '.html'); await s.page.evaluate(() => document.fonts.ready);
  await s.page.reload(); await s.page.evaluate(() => document.fonts.ready);
  await s.start(); await s.wait(t * 1000); await s.end();
}
await close();
