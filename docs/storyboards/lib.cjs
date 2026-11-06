// Shared builder for the TechLab storyboards (.docx). Each storyboard file (self-paced.cjs,
// join-class.cjs) lists its scenes and calls build(). Screenshots live in the user manual's
// shots/ folder; a scene's shot is a path inside it, e.g. 'student/01-home.png'.
const fs = require('fs');
const path = require('path');
const {
  Document, Packer, Paragraph, TextRun, ImageRun, AlignmentType, Table, TableRow, TableCell, WidthType,
  ShadingType, BorderStyle, PageBreak, PageOrientation, Footer, Header, PageNumber, VerticalAlign, TableLayoutType,
} = require('docx');

const SHOTS = path.join(__dirname, '..', 'user-manual', 'shots');
const BLUE = '2F7FE0', GOLD = 'B57A00', INK = '141A33', MUTED = '586079', LINE = 'D6DCEC';
const FONT = 'Arial';

// Landscape A4: 16838 × 11906 DXA, 0.5" margins.
const MARGIN = 720, CONTENT_W = 16838 - 2 * MARGIN, GAP = 360, CELL_W = (CONTENT_W - GAP) / 2;

const none = { style: BorderStyle.NONE, size: 0, color: 'FFFFFF' };
const noBorders = { top: none, bottom: none, left: none, right: none, insideHorizontal: none, insideVertical: none };
const line = { style: BorderStyle.SINGLE, size: 6, color: LINE };

const t = (text, o = {}) => new TextRun({ text, font: FONT, size: Math.round((o.pt || 10) * 2), bold: o.bold, italics: o.italics, color: o.color || INK });
const para = (runs, o = {}) => new Paragraph({ children: [].concat(runs), alignment: o.align, spacing: { before: o.before || 0, after: o.after ?? 80 } });
const label = (k, v, pt = 9.5) => para([t(k + '  ', { bold: true, color: BLUE, pt: 9 }), t(v, { pt })], { after: 60 });
const sceneNo = (n, stage) => para([t(`SCENE ${String(n).padStart(2, '0')}`, { bold: true, color: GOLD, pt: 8.5 }), t(`   ${stage}`, { color: MUTED, pt: 8.5 })], { after: 40 });
const emptyCell = (w) => new TableCell({ width: { size: w, type: WidthType.DXA }, borders: noBorders, children: [new Paragraph({ children: [] })] });

function image(file, maxW, maxH) {
  const data = fs.readFileSync(path.join(SHOTS, file));
  const w = data.readUInt32BE(16), h = data.readUInt32BE(20);
  const scale = Math.min(maxW / w, maxH / h);
  return new ImageRun({ type: 'png', data, transformation: { width: Math.round(w * scale), height: Math.round(h * scale) }, altText: { title: file, description: file, name: file } });
}

// One storyboard frame: scene number, title, screenshot, then what the student does and sees.
function frame(n, s) {
  return new TableCell({
    width: { size: CELL_W, type: WidthType.DXA }, borders: { top: line, bottom: line, left: line, right: line },
    margins: { top: 160, bottom: 160, left: 200, right: 200 },
    children: [
      sceneNo(n, s.stage),
      para(t(s.title, { bold: true, pt: 13 }), { after: 120 }),
      new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 140 }, children: [image(s.shot, 470, 294)] }),
      label('ACTION', s.action),
      label('ON SCREEN', s.screen),
      ...(s.note ? [label('NOTE', s.note)] : []),
    ],
  });
}

function spread(a, b, n) {
  return new Table({
    width: { size: CONTENT_W, type: WidthType.DXA }, columnWidths: [CELL_W, GAP, CELL_W], borders: noBorders, layout: TableLayoutType.FIXED,
    rows: [new TableRow({ cantSplit: true, children: [frame(n, a), emptyCell(GAP), b ? frame(n + 1, b) : emptyCell(CELL_W)] })],
  });
}

// The last scene gets a page of its own. A tall picture (a certificate) sits beside the text;
// a wide one (a screen) sits above it.
function finalePage(n, f) {
  const data = fs.readFileSync(path.join(SHOTS, f.shot));
  if (data.readUInt32BE(16) > data.readUInt32BE(20)) {
    return [
      sceneNo(n, f.stage),
      para(t(f.title, { bold: true, pt: 20 }), { after: 160 }),
      new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 160 }, children: [image(f.shot, 860, 350)] }),
      label('ACTION', f.action, 10.5),
      label('ON SCREEN', f.screen, 10.5),
      label('NOTE', f.note, 10.5),
      para(t(f.closing, { italics: true, color: MUTED, pt: 10 }), { before: 120 }),
    ];
  }
  const TEXT_W = 6200;
  return [new Table({
    width: { size: CONTENT_W, type: WidthType.DXA }, columnWidths: [TEXT_W, CONTENT_W - TEXT_W], borders: noBorders, layout: TableLayoutType.FIXED,
    rows: [new TableRow({ children: [
      new TableCell({ width: { size: TEXT_W, type: WidthType.DXA }, borders: noBorders, verticalAlign: VerticalAlign.CENTER, margins: { right: 400 }, children: [
        sceneNo(n, f.stage),
        para(t(f.title, { bold: true, pt: 20 }), { after: 240 }),
        para([t('ACTION  ', { bold: true, color: BLUE, pt: 9 }), t(f.action, { pt: 10.5 })], { after: 140 }),
        para([t('ON SCREEN  ', { bold: true, color: BLUE, pt: 9 }), t(f.screen, { pt: 10.5 })], { after: 140 }),
        para([t('NOTE  ', { bold: true, color: BLUE, pt: 9 }), t(f.note, { pt: 10.5 })], { after: 360 }),
        para(t(f.closing, { italics: true, color: MUTED, pt: 10 })),
      ] }),
      new TableCell({ width: { size: CONTENT_W - TEXT_W, type: WidthType.DXA }, borders: noBorders, children: [
        new Paragraph({ alignment: AlignmentType.CENTER, children: [image(f.shot, 420, 594)] }),
      ] }),
    ] })],
  })];
}

function cover(sb, sceneCount) {
  return [
    para(t('TechLab', { bold: true, color: BLUE, pt: 14 }), { before: 1600, after: 120 }),
    para(t(sb.heading, { bold: true, pt: 30 }), { after: 200 }),
    para(t(sb.intro, { color: MUTED, pt: 12 }), { after: 480 }),
    new Table({
      width: { size: 9000, type: WidthType.DXA }, columnWidths: [2600, 6400], layout: TableLayoutType.FIXED,
      borders: { top: line, bottom: line, left: line, right: line, insideHorizontal: line, insideVertical: line },
      rows: [...sb.facts, ['Scenes', String(sceneCount)]].map(([k, v]) => new TableRow({ children: [
        new TableCell({ width: { size: 2600, type: WidthType.DXA }, shading: { type: ShadingType.CLEAR, color: 'auto', fill: 'E4EFFD' }, margins: { top: 80, bottom: 80, left: 140, right: 140 }, children: [para(t(k, { bold: true }), { after: 0 })] }),
        new TableCell({ width: { size: 6400, type: WidthType.DXA }, margins: { top: 80, bottom: 80, left: 140, right: 140 }, children: [para(t(v), { after: 0 })] }),
      ] })),
    }),
    para(t(sb.flowLabel || 'The journey', { bold: true, pt: 11 }), { before: 480, after: 120 }),
    para(sb.flow.flatMap((s, i) => [t(s, { bold: true, color: i === sb.flow.length - 1 ? GOLD : BLUE, pt: 11 }), ...(i < sb.flow.length - 1 ? [t('  →  ', { color: MUTED, pt: 11 })] : [])])),
    new Paragraph({ children: [new PageBreak()] }),
  ];
}

// A part's title page, for long storyboards split into parts.
function chapterPage(n, c) {
  return [
    para(t(`PART ${n}`, { bold: true, color: GOLD, pt: 11 }), { before: 2600, after: 120 }),
    para(t(c.chapter, { bold: true, pt: 28 }), { after: 200 }),
    para(t(c.intro, { color: MUTED, pt: 12 }), { after: 0 }),
  ];
}

// sb: { title, heading, intro, facts: [[label, value]], flow: [steps], scenes: [...], finale: {...}, out }
// A scenes entry { chapter, intro } starts a new part: it gets its own title page, and the
// scenes after it pair up from there. Scene numbers run on across parts.
function build(sb) {
  const pages = [];
  const pageBreak = () => new Paragraph({ children: [new PageBreak()] });
  let n = 1, part = 0, run = [];
  const flush = () => {
    for (let i = 0; i < run.length; i += 2) {
      if (pages.length) pages.push(pageBreak());
      pages.push(spread(run[i], run[i + 1], n));
      n += run[i + 1] ? 2 : 1;
    }
    run = [];
  };
  for (const item of sb.scenes) {
    if (item.chapter) {
      flush();
      if (pages.length) pages.push(pageBreak());
      pages.push(...chapterPage(++part, item));
    } else {
      run.push(item);
    }
  }
  flush();
  const last = n;

  const doc = new Document({
    creator: 'TechLab', title: sb.title,
    styles: { default: { document: { run: { font: FONT, size: 20, color: INK } } } },
    sections: [{
      properties: { page: { size: { width: 11906, height: 16838, orientation: PageOrientation.LANDSCAPE }, margin: { top: MARGIN, right: MARGIN, bottom: MARGIN, left: MARGIN, header: 360, footer: 360 } } },
      headers: { default: new Header({ children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [t(sb.title, { pt: 8, color: MUTED })] })] }) },
      footers: { default: new Footer({ children: [new Paragraph({ alignment: AlignmentType.CENTER, children: [new TextRun({ children: ['Page ', PageNumber.CURRENT], size: 16, color: MUTED, font: FONT })] })] }) },
      children: [...cover(sb, last), ...pages, new Paragraph({ children: [new PageBreak()] }), ...finalePage(last, sb.finale)],
    }],
  });

  return Packer.toBuffer(doc).then(buf => { fs.writeFileSync(sb.out, buf); console.log('wrote', sb.out, buf.length); });
}

module.exports = { build };
