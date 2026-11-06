// Screen-recording helpers: CDP screencast -> JPEG frames -> MP4, plus an on-page
// Astro cursor, click ripple and caption bar so viewers can follow along.
import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { execFileSync } from 'child_process';

export const B = 'http://localhost:8001';
export const OUT = process.env.OUT || path.resolve('out');
const CHROME = process.env.HOME + '/Library/Caches/ms-playwright/chromium-1234/chrome-mac-arm64/Google Chrome for Testing.app/Contents/MacOS/Google Chrome for Testing';
const VW = 1600, VH = 900, DPR = 1.2;
const sleep = (ms) => new Promise(r => setTimeout(r, ms));

const OVERLAY = String.raw`(() => {
  const ARROW = '/images/cursors/astro-arrow.svg', GLOVE = '/images/cursors/astro-glove.svg';
  const ss = (k, v) => { try { if (v === undefined) return sessionStorage.getItem(k); sessionStorage.setItem(k, v); } catch (e) { return null; } };
  function mount() {
    if (document.getElementById('__rec_cur')) return;
    const st = document.createElement('style');
    st.textContent = '#__rec_cur{position:fixed;left:0;top:0;width:32px;height:32px;z-index:2147483647;pointer-events:none;image-rendering:pixelated;filter:drop-shadow(0 2px 3px rgba(0,0,0,.35));transition:transform .08s}'
      + '.__rec_rip{position:fixed;width:44px;height:44px;margin:-22px 0 0 -22px;border-radius:50%;border:3px solid #ffc94a;background:rgba(255,201,74,.25);z-index:2147483646;pointer-events:none;animation:__rec_rip .5s ease-out forwards}'
      + '@keyframes __rec_rip{from{transform:scale(.3);opacity:1}to{transform:scale(1.4);opacity:0}}'
      + '#__rec_cap{position:fixed;left:50%;bottom:22px;transform:translateX(-50%);z-index:2147483645;pointer-events:none;display:flex;align-items:center;gap:12px;max-width:1180px;padding:12px 22px 12px 12px;border-radius:16px;background:rgba(11,16,48,.92);color:#fff;font:600 19px/1.35 Poppins,system-ui,-apple-system,sans-serif;box-shadow:0 10px 30px rgba(0,0,0,.35);border:2px solid rgba(115,182,255,.5);transition:opacity .25s}'
      + '#__rec_cap.top{bottom:auto;top:18px}#__rec_cap.hide{opacity:0}'
      + '#__rec_cap b{flex:none;font:800 13px/1 Poppins,system-ui,sans-serif;letter-spacing:.12em;padding:8px 11px;border-radius:9px;color:#0b1030}';
    document.documentElement.appendChild(st);
    const c = document.createElement('img'); c.id = '__rec_cur'; c.src = ARROW;
    const cap = document.createElement('div'); cap.id = '__rec_cap';
    document.documentElement.appendChild(cap); document.documentElement.appendChild(c);
    let glove = false;
    const place = (x, y) => {
      const el = document.elementFromPoint(x, y);
      let g = false; try { g = !!el && getComputedStyle(el).cursor === 'pointer'; } catch (e) {}
      if (g !== glove) { glove = g; c.src = g ? GLOVE : ARROW; }
      c.style.transform = 'translate(' + (x - (g ? 11 : 0)) + 'px,' + (y - (g ? 1 : 0)) + 'px)';
    };
    const p = JSON.parse(ss('__rec_pos') || '[800,450]'); place(p[0], p[1]);
    document.addEventListener('mousemove', e => { place(e.clientX, e.clientY); ss('__rec_pos', JSON.stringify([e.clientX, e.clientY])); }, true);
    document.addEventListener('mousedown', e => {
      const r = document.createElement('div'); r.className = '__rec_rip'; r.style.left = e.clientX + 'px'; r.style.top = e.clientY + 'px';
      document.documentElement.appendChild(r); setTimeout(() => r.remove(), 600);
    }, true);
    window.__recCap = (role, color, text, pos) => {
      ss('__rec_capv', JSON.stringify([role, color, text, pos]));
      if (!text) { cap.className = 'hide'; return; }
      cap.className = pos === 'top' ? 'top' : '';
      cap.innerHTML = '<b style="background:' + color + '">' + role + '</b><span></span>';
      cap.querySelector('span').textContent = text;
    };
    const v = ss('__rec_capv'); if (v) window.__recCap(...JSON.parse(v)); else cap.className = 'hide';
  }
  if (document.documentElement) mount(); else document.addEventListener('DOMContentLoaded', mount);
  document.addEventListener('DOMContentLoaded', mount);
})();`;

let browser;
export async function launch() { browser = await chromium.launch({ executablePath: CHROME }); return browser; }
export async function close() { await browser?.close(); }

// One recorded segment: a fresh browser context (= a fresh person) and one page.
export async function segment(key, { role = '', color = '#73b6ff', overlay = true } = {}) {
  const ctx = await browser.newContext({ viewport: { width: VW, height: VH }, deviceScaleFactor: DPR });
  if (overlay) await ctx.addInitScript(OVERLAY);
  const page = await ctx.newPage();
  page.on('pageerror', () => {});
  const dir = path.join(OUT, key); fs.rmSync(dir, { recursive: true, force: true }); fs.mkdirSync(dir, { recursive: true });
  const cdp = await ctx.newCDPSession(page);
  const frames = [];
  cdp.on('Page.screencastFrame', f => {
    const file = path.join(dir, String(frames.length).padStart(6, '0') + '.jpg');
    fs.writeFileSync(file, Buffer.from(f.data, 'base64'));
    frames.push({ file, t: f.metadata.timestamp });
    cdp.send('Page.screencastFrameAck', { sessionId: f.sessionId }).catch(() => {});
  });
  let mx = 800, my = 450, recording = false;

  const s = {
    page,
    async start() { await cdp.send('Page.startScreencast', { format: 'jpeg', quality: 88, maxWidth: VW * DPR, maxHeight: VH * DPR }); recording = true; },
    wait: (ms) => sleep(ms),
    async go(url) { await page.goto(url.startsWith('http') || url.startsWith('file') ? url : B + url, { waitUntil: 'load' }); await sleep(700); },
    async cap(text, pos) { await page.evaluate(([r, c, t, p]) => window.__recCap && window.__recCap(r, c, t, p), [role, color, text, pos]).catch(() => {}); },
    async move(x, y, ms = 650) {
      const n = Math.max(12, Math.round(ms / 16)), x0 = mx, y0 = my;
      for (let i = 1; i <= n; i++) {
        const k = i / n, e = k < .5 ? 2 * k * k : 1 - Math.pow(-2 * k + 2, 2) / 2;
        await page.mouse.move(x0 + (x - x0) * e, y0 + (y - y0) * e); await sleep(14);
      }
      mx = x; my = y;
    },
    // Smoothly wheel-scroll until the locator's centre sits at `at` (fraction of viewport height).
    async reveal(loc, at = 0.5) {
      loc = typeof loc === 'string' ? page.locator(loc).first() : loc;
      await loc.waitFor({ state: 'visible', timeout: 15000 });
      let last = null, stuck = 0; const t0 = Date.now();
      for (let i = 0; i < 200 && Date.now() - t0 < 2500; i++) {
        const b = await loc.boundingBox(); if (!b) break;
        const d = b.y + b.height / 2 - VH * at;
        const fits = b.y >= 70 && b.y + b.height <= VH - 90;
        if (Math.abs(d) < 30 || (fits && Math.abs(d) < 200)) break;
        stuck = last !== null && Math.abs(last - b.y) < 1 ? stuck + 1 : 0;
        if (stuck > 12) break; // can't scroll further
        last = b.y;
        const wx = Math.min(Math.max(b.x + b.width / 2, 40), VW - 40);
        if (Math.abs(mx - wx) > 5 || my < 100 || my > VH - 100) await s.move(wx, Math.min(Math.max(my, 200), VH - 200), 300);
        await page.mouse.wheel(0, Math.max(-70, Math.min(70, d))); await sleep(16);
      }
      await sleep(250);
    },
    async scroll(dy, steps) {
      steps = steps || Math.ceil(Math.abs(dy) / 45);
      for (let i = 0; i < steps; i++) { await page.mouse.wheel(0, dy / steps); await sleep(16); }
      await sleep(300);
    },
    async hover(loc) {
      loc = typeof loc === 'string' ? page.locator(loc).first() : loc;
      await s.reveal(loc);
      const b = await loc.boundingBox();
      await s.move(b.x + b.width / 2, b.y + b.height / 2);
    },
    async click(loc, { nav = false, pause = 280 } = {}) {
      loc = typeof loc === 'string' ? page.locator(loc).first() : loc;
      await s.hover(loc); await sleep(pause);
      const go = nav ? page.waitForNavigation({ waitUntil: 'load' }) : null;
      await page.mouse.down(); await sleep(70); await page.mouse.up();
      if (go) { await go; await sleep(700); } else await sleep(350);
    },
    async type(loc, text, { delay = 55, clear = false } = {}) {
      loc = typeof loc === 'string' ? page.locator(loc).first() : loc;
      await s.click(loc);
      if (clear) { await page.keyboard.press('Meta+A'); await page.keyboard.press('Backspace'); await sleep(200); }
      for (const ch of text) { if (ch === '\n') await page.keyboard.press('Enter'); else await page.keyboard.type(ch); await sleep(delay * (0.6 + Math.random() * 0.8)); }
      await sleep(250);
    },
    btn: (text, scope = 'button:visible, a:visible') => page.locator(scope).filter({ hasText: text }).first(),
    async end() {
      await sleep(400);
      const tEnd = Date.now() / 1000;
      if (recording) await cdp.send('Page.stopScreencast').catch(() => {});
      await sleep(300);
      await ctx.close();
      if (!frames.length) throw new Error('no frames for ' + key);
      const list = frames.map((f, i) => `file '${f.file}'\nduration ${Math.max(0.001, ((frames[i + 1]?.t ?? tEnd) - f.t)).toFixed(4)}`).join('\n') + `\nfile '${frames.at(-1).file}'\n`;
      fs.writeFileSync(path.join(dir, 'list.txt'), list);
      const mp4 = path.join(OUT, key + '.mp4');
      execFileSync('ffmpeg', ['-y', '-loglevel', 'error', '-f', 'concat', '-safe', '0', '-i', path.join(dir, 'list.txt'),
        '-vf', `fps=30,scale=1920:1080:flags=lanczos,format=yuv420p`, '-c:v', 'libx264', '-preset', 'medium', '-crf', '18', mp4]);
      console.log('segment', key, frames.length, 'frames ->', mp4);
      return mp4;
    },
  };
  return s;
}
