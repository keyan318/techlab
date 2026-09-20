// Builds a designed .pptx from a slide spec.
//   node scripts/pptx/build.mjs <out.pptx>   (spec JSON on stdin)
// The spec is produced (and cleaned) by App\Services\Ppt\PptContentService, so text lengths
// and slide counts are already bounded here; this file only decides how things LOOK.
import PptxGenJS from 'pptxgenjs';

const W = 13.333, H = 7.5, MX = 0.8, CW = W - MX * 2;

// Each theme: light/dark page colours + two accents. `dark` is used for cover / section / quote / closing.
const THEMES = {
  midnight: { bg: '0B1020', surface: '161D38', text: 'F2F4FF', muted: '9AA4CC', accent: '73B6FF', accent2: '9B6BFF', dark: '070B18', onDark: 'F2F4FF', onDarkMuted: '9AA4CC', onAccent: '07142E', head: 'Georgia', body: 'Calibri' },
  paper:    { bg: 'FAF7F2', surface: 'FFFFFF', text: '1B1B1F', muted: '6B6B73', accent: 'D9482B', accent2: '1F3A5F', dark: '1F3A5F', onDark: 'FFFFFF', onDarkMuted: 'C9D3E3', onAccent: 'FFFFFF', head: 'Georgia', body: 'Calibri' },
  sunrise:  { bg: 'FFF6EC', surface: 'FFFFFF', text: '2B1B3D', muted: '7A6A8A', accent: 'FF6B35', accent2: '7C3AED', dark: '2B1B3D', onDark: 'FFFFFF', onDarkMuted: 'D4C8E2', onAccent: 'FFFFFF', head: 'Trebuchet MS', body: 'Calibri' },
  forest:   { bg: 'F1F5F0', surface: 'FFFFFF', text: '14261C', muted: '5C7266', accent: '2F9E6B', accent2: 'E0A100', dark: '0F2A1E', onDark: 'FFFFFF', onDarkMuted: 'B8D4C4', onAccent: 'FFFFFF', head: 'Cambria', body: 'Calibri' },
};

const spec = JSON.parse(await new Promise((res, rej) => {
  let s = ''; process.stdin.setEncoding('utf8');
  process.stdin.on('data', c => (s += c)); process.stdin.on('end', () => res(s)); process.stdin.on('error', rej);
}));
const out = process.argv[2];
if (!out) throw new Error('usage: build.mjs <out.pptx>');

const T = { ...(THEMES[spec.theme] || THEMES.midnight) };
if (/^[0-9A-Fa-f]{6}$/.test(spec.accent || '')) T.accent = spec.accent.toUpperCase();

const pptx = new PptxGenJS();
pptx.layout = 'LAYOUT_WIDE';
pptx.title = spec.title || 'Presentation';
pptx.author = 'TechLab';

const size = (text, tiers) => { const n = (text || '').length; for (const [max, pt] of tiers) if (n <= max) return pt; return tiers[tiers.length - 1][1]; };
const round = { shape: pptx.ShapeType.roundRect, rectRadius: 0.14 };

function frame(s, { dark = false, notes, n } = {}) {
  s.background = { color: dark ? T.dark : T.bg };
  if (notes) s.addNotes(notes);
  if (!dark) {
    s.addText((spec.title || '').toUpperCase(), { x: MX, y: 6.98, w: 8, h: 0.3, fontFace: T.body, fontSize: 9, color: T.muted, charSpacing: 3, margin: 0 });
    s.addText(String(n), { x: W - MX - 1, y: 6.98, w: 1, h: 0.3, fontFace: T.body, fontSize: 10, color: T.muted, align: 'right', margin: 0 });
  }
}
const kicker = (s, text, y, color) => text && s.addText(text.toUpperCase(), { x: MX, y, w: CW, h: 0.35, fontFace: T.body, fontSize: 13, bold: true, color, charSpacing: 6, margin: 0 });
const heading = (s, text, y = 0.7) => s.addText(text, { x: MX, y, w: CW, h: 1.1, fontFace: T.head, fontSize: size(text, [[34, 38], [60, 32], [999, 28]]), bold: true, color: T.text, valign: 'top', margin: 0, fit: 'shrink' });
const dot = (s, x, y, d, color) => s.addShape(pptx.ShapeType.ellipse, { x, y, w: d, h: d, fill: { color }, line: { color, width: 0 } });

const L = {
  cover(s, d) {
    s.addShape(pptx.ShapeType.ellipse, { x: 8.4, y: -1.5, w: 7.6, h: 7.6, fill: { color: T.accent, transparency: 62 }, line: { color: T.accent, width: 0 } });
    s.addShape(pptx.ShapeType.ellipse, { x: 10.6, y: 3.9, w: 4.2, h: 4.2, fill: { color: T.accent2, transparency: 72 }, line: { color: T.accent2, width: 0 } });
    s.addShape(pptx.ShapeType.rect, { x: MX, y: 1.9, w: 0.9, h: 0.09, fill: { color: T.accent }, line: { color: T.accent, width: 0 } });
    kicker(s, d.kicker, 2.2, T.accent);
    s.addText(d.title, { x: MX, y: 2.7, w: 7.4, h: 2.4, fontFace: T.head, fontSize: size(d.title, [[26, 60], [46, 48], [999, 40]]), bold: true, color: T.onDark, valign: 'top', margin: 0, fit: 'shrink' });
    if (d.subtitle) s.addText(d.subtitle, { x: MX, y: 5.3, w: 7, h: 0.9, fontFace: T.body, fontSize: 20, color: T.onDarkMuted, valign: 'top', margin: 0, fit: 'shrink' });
  },
  section(s, d) {
    s.addText(String(d.number).padStart(2, '0'), { x: MX, y: 0.9, w: 6, h: 2.8, fontFace: T.head, fontSize: 150, bold: true, color: T.accent, margin: 0, valign: 'top' });
    s.addShape(pptx.ShapeType.rect, { x: MX, y: 4.0, w: 0.9, h: 0.09, fill: { color: T.accent2 }, line: { color: T.accent2, width: 0 } });
    s.addText(d.title, { x: MX, y: 4.3, w: 10.5, h: 1.3, fontFace: T.head, fontSize: size(d.title, [[30, 46], [999, 38]]), bold: true, color: T.onDark, valign: 'top', margin: 0, fit: 'shrink' });
    if (d.subtitle) s.addText(d.subtitle, { x: MX, y: 5.65, w: 9.5, h: 0.8, fontFace: T.body, fontSize: 18, color: T.onDarkMuted, valign: 'top', margin: 0, fit: 'shrink' });
  },
  statement(s, d) {
    kicker(s, d.kicker, 1.5, T.accent);
    s.addShape(pptx.ShapeType.rect, { x: MX, y: 2.2, w: 0.12, h: 3.2, fill: { color: T.accent }, line: { color: T.accent, width: 0 } });
    s.addText(d.text, { x: MX + 0.5, y: 2.1, w: CW - 0.9, h: 3.4, fontFace: T.head, fontSize: size(d.text, [[60, 44], [110, 38], [999, 32]]), bold: true, color: T.text, valign: 'middle', margin: 0, fit: 'shrink' });
  },
  bigStat(s, d) {
    heading(s, d.title);
    const n = d.stats.length, gap = 0.4, w = (CW - gap * (n - 1)) / n;
    d.stats.forEach((st, i) => {
      const x = MX + i * (w + gap);
      s.addShape(round.shape, { x, y: 2.3, w, h: 3.7, fill: { color: T.surface }, line: { color: T.muted, width: 0.5, transparency: 70 }, rectRadius: round.rectRadius });
      s.addText(st.value, { x: x + 0.2, y: 2.75, w: w - 0.4, h: 1.5, fontFace: T.head, fontSize: size(st.value, [[4, 66], [7, 52], [999, 40]]), bold: true, color: T.accent, align: 'center', valign: 'middle', margin: 0, fit: 'shrink' });
      s.addText(st.label, { x: x + 0.3, y: 4.35, w: w - 0.6, h: 1.4, fontFace: T.body, fontSize: 21, color: T.text, align: 'center', valign: 'top', margin: 0, fit: 'shrink' });
    });
  },
  split(s, d) {
    // Every other split mirrors (panel left, text right) so two in a row never look identical.
    const flip = (splitCount++ % 2) === 1;
    const textX = flip ? 5.5 : MX, panelX = flip ? MX : 8.2;
    heading(s, d.title);
    const n = d.points.length, rowH = Math.min(1.05, 4.4 / n);
    d.points.forEach((p, i) => {
      const y = 2.15 + i * rowH;
      dot(s, textX, y + 0.13, 0.16, T.accent);
      s.addText(p, { x: textX + 0.4, y, w: flip ? 6.6 : 6.5, h: rowH - 0.1, fontFace: T.body, fontSize: 26, color: T.text, valign: 'top', margin: 0, fit: 'shrink' });
    });
    s.addShape(round.shape, { x: panelX, y: 2.0, w: 4.33, h: 4.5, fill: { color: T.dark }, line: { color: T.dark, width: 0 }, rectRadius: round.rectRadius });
    s.addText((d.asideLabel || 'Remember').toUpperCase(), { x: panelX + 0.4, y: 2.4, w: 3.6, h: 0.35, fontFace: T.body, fontSize: 12, bold: true, color: T.accent, charSpacing: 5, margin: 0 });
    s.addText(d.aside, { x: panelX + 0.4, y: 2.9, w: 3.55, h: 3.3, fontFace: T.head, fontSize: size(d.aside, [[70, 30], [130, 25], [999, 21]]), color: T.onDark, valign: 'top', margin: 0, fit: 'shrink' });
  },
  cards(s, d) {
    heading(s, d.title);
    const n = d.items.length, gap = 0.35, w = (CW - gap * (n - 1)) / n;
    d.items.forEach((it, i) => {
      const x = MX + i * (w + gap);
      s.addShape(round.shape, { x, y: 2.2, w, h: 3.75, fill: { color: T.surface }, line: { color: T.muted, width: 0.5, transparency: 70 }, rectRadius: round.rectRadius });
      s.addShape(pptx.ShapeType.rect, { x: x + 0.35, y: 2.2, w: 0.7, h: 0.08, fill: { color: i % 2 ? T.accent2 : T.accent }, line: { color: T.accent, width: 0 } });
      s.addText(String(i + 1).padStart(2, '0'), { x: x + 0.35, y: 2.55, w: 1.5, h: 0.6, fontFace: T.head, fontSize: 30, bold: true, color: i % 2 ? T.accent2 : T.accent, margin: 0 });
      s.addText(it.title, { x: x + 0.35, y: 3.25, w: w - 0.7, h: 0.9, fontFace: T.head, fontSize: 25, bold: true, color: T.text, valign: 'top', margin: 0, fit: 'shrink' });
      s.addText(it.text, { x: x + 0.35, y: 4.15, w: w - 0.7, h: 2.0, fontFace: T.body, fontSize: 19, color: T.muted, valign: 'top', margin: 0, fit: 'shrink' });
    });
  },
  steps(s, d) {
    heading(s, d.title);
    const n = d.items.length, w = CW / n, lineY = 3.05;
    s.addShape(pptx.ShapeType.line, { x: MX + w / 2, y: lineY, w: CW - w, h: 0, line: { color: T.accent, width: 2, dashType: 'dash' } });
    d.items.forEach((it, i) => {
      const cx = MX + i * w + w / 2;
      s.addShape(pptx.ShapeType.ellipse, { x: cx - 0.3, y: lineY - 0.3, w: 0.6, h: 0.6, fill: { color: i % 2 ? T.accent2 : T.accent }, line: { color: T.bg, width: 3 } });
      s.addText(String(i + 1), { x: cx - 0.3, y: lineY - 0.3, w: 0.6, h: 0.6, fontFace: T.head, fontSize: 16, bold: true, color: T.onAccent, align: 'center', valign: 'middle', margin: 0 });
      s.addText(it.title, { x: cx - w / 2 + 0.15, y: 3.65, w: w - 0.3, h: 0.9, fontFace: T.head, fontSize: 23, bold: true, color: T.text, align: 'center', valign: 'top', margin: 0, fit: 'shrink' });
      s.addText(it.text, { x: cx - w / 2 + 0.2, y: 4.55, w: w - 0.4, h: 1.9, fontFace: T.body, fontSize: 18, color: T.muted, align: 'center', valign: 'top', margin: 0, fit: 'shrink' });
    });
  },
  compare(s, d) {
    heading(s, d.title);
    const gap = 0.4, w = (CW - gap) / 2;
    [d.left, d.right].forEach((col, i) => {
      const x = MX + i * (w + gap), c = i ? T.accent2 : T.accent;
      s.addShape(round.shape, { x, y: 2.1, w, h: 3.9, fill: { color: T.surface }, line: { color: T.muted, width: 0.5, transparency: 70 }, rectRadius: round.rectRadius });
      s.addShape(pptx.ShapeType.rect, { x: x + 0.4, y: 2.1, w: 0.9, h: 0.09, fill: { color: c }, line: { color: c, width: 0 } });
      s.addText(col.heading, { x: x + 0.4, y: 2.4, w: w - 0.8, h: 0.6, fontFace: T.head, fontSize: 30, bold: true, color: c, margin: 0, fit: 'shrink' });
      const rowH = Math.min(0.95, 3.2 / col.points.length);
      col.points.forEach((p, j) => {
        const y = 3.2 + j * rowH;
        dot(s, x + 0.4, y + 0.11, 0.13, c);
        s.addText(p, { x: x + 0.7, y, w: w - 1.1, h: rowH - 0.08, fontFace: T.body, fontSize: 21, color: T.text, valign: 'top', margin: 0, fit: 'shrink' });
      });
    });
  },
  quote(s, d) {
    s.addText('“', { x: MX, y: 0.5, w: 3, h: 2.6, fontFace: T.head, fontSize: 200, bold: true, color: T.accent, margin: 0, valign: 'top' });
    s.addText(d.text, { x: MX + 0.6, y: 2.2, w: CW - 1.4, h: 2.7, fontFace: T.head, fontSize: size(d.text, [[80, 36], [140, 30], [999, 26]]), italic: true, color: T.onDark, valign: 'top', margin: 0, fit: 'shrink' });
    if (d.by) s.addText('— ' + d.by, { x: MX + 0.6, y: 5.2, w: CW - 1.4, h: 0.5, fontFace: T.body, fontSize: 20, color: T.onDarkMuted, margin: 0 });
  },
  code(s, d) {
    heading(s, d.title);
    const h = d.caption ? 4.0 : 4.5;
    s.addShape(round.shape, { x: MX, y: 1.95, w: CW, h, fill: { color: '0D1117' }, line: { color: '30363D', width: 1 }, rectRadius: 0.12 });
    ['FF5F56', 'FFBD2E', '27C93F'].forEach((c, i) => dot(s, MX + 0.3 + i * 0.3, 2.17, 0.15, c));
    s.addText(d.code, { x: MX + 0.4, y: 2.6, w: CW - 0.8, h: h - 0.85, fontFace: 'Courier New', fontSize: size(d.code, [[240, 18], [420, 16], [999, 14]]), color: 'E6EDF3', valign: 'top', margin: 0, fit: 'shrink', lineSpacingMultiple: 1.15 });
    if (d.caption) s.addText(d.caption, { x: MX, y: 6.15, w: CW, h: 0.6, fontFace: T.body, fontSize: 15, color: T.muted, valign: 'top', margin: 0, fit: 'shrink' });
  },
  closing(s, d) {
    s.addShape(pptx.ShapeType.ellipse, { x: 9.8, y: 3.2, w: 6.5, h: 6.5, fill: { color: T.accent2, transparency: 45 }, line: { color: T.accent2, width: 0 } });
    kicker(s, d.kicker || 'Key takeaways', 0.9, T.accent);
    s.addText(d.title, { x: MX, y: 1.4, w: 9, h: 1.3, fontFace: T.head, fontSize: size(d.title, [[30, 46], [999, 38]]), bold: true, color: T.onDark, valign: 'top', margin: 0, fit: 'shrink' });
    const n = d.points.length, rowH = Math.min(0.95, 3.4 / n);
    d.points.forEach((p, i) => {
      const y = 3.1 + i * rowH;
      s.addText(String(i + 1).padStart(2, '0'), { x: MX, y, w: 0.8, h: rowH - 0.1, fontFace: T.head, fontSize: 20, bold: true, color: T.accent, margin: 0, valign: 'top' });
      s.addText(p, { x: MX + 0.9, y, w: 8.4, h: rowH - 0.1, fontFace: T.body, fontSize: 24, color: T.onDark, valign: 'top', margin: 0, fit: 'shrink' });
    });
  },
};
let splitCount = 0;
const DARK = new Set(['cover', 'section', 'quote', 'closing']);

spec.slides.forEach((d, i) => {
  const s = pptx.addSlide();
  frame(s, { dark: DARK.has(d.layout), notes: d.notes, n: i + 1 });
  (L[d.layout] || L.statement)(s, d);
});

await pptx.writeFile({ fileName: out });
