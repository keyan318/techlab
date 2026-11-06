// Shared builder for the TechLab user manuals (.docx). Each role's file (student.cjs, faculty.cjs…)
// calls kit(role) for the helpers, builds its content array, then calls save().
const fs = require('fs');
const path = require('path');
const {
  Document, Packer, Paragraph, TextRun, ImageRun, HeadingLevel, AlignmentType, Table, TableRow, TableCell,
  WidthType, ShadingType, BorderStyle, LevelFormat, PageBreak, TableOfContents, Footer, PageNumber, Header,
} = require('docx');

const BLUE = '2F7FE0', GOLD = 'B57A00', INK = '141A33', MUTED = '586079', RED = 'B3261E';
const FONT = 'Arial';

module.exports = function kit(role) {
  const SHOTS = path.join(__dirname, 'shots', role.toLowerCase());
  const numberRefs = [];
  let listId = 0;
  let stepNo = 0;

  // Inline markup: **bold**, [[button label]]
  function runs(text, base = {}) {
    const out = [];
    const re = /(\*\*[^*]+\*\*|\[\[[^\]]+\]\])/g;
    let last = 0, m;
    while ((m = re.exec(text))) {
      if (m.index > last) out.push(new TextRun({ text: text.slice(last, m.index), ...base }));
      const t = m[0];
      if (t.startsWith('**')) out.push(new TextRun({ text: t.slice(2, -2), bold: true, ...base }));
      else out.push(new TextRun({ text: t.slice(2, -2), bold: true, color: BLUE, ...base }));
      last = m.index + t.length;
    }
    if (last < text.length) out.push(new TextRun({ text: text.slice(last), ...base }));
    return out;
  }

  const p = (text, opts = {}) => new Paragraph({ children: runs(text), spacing: { after: 120 }, ...opts });
  const bullet = (text) => new Paragraph({ children: runs(text), numbering: { reference: 'bullets', level: 0 }, spacing: { after: 60 } });
  const numbered = (items) => {
    const ref = 'num' + (listId++);
    numberRefs.push(ref);
    return items.map(t => new Paragraph({ children: runs(t), numbering: { reference: ref, level: 0 }, spacing: { after: 60 } }));
  };

  function note(label, text, warn = false) {
    const color = warn ? RED : GOLD;
    return new Paragraph({
      children: [new TextRun({ text: label.toUpperCase() + (/[?!]$/.test(label) ? ' ' : ': '), bold: true, color }), ...runs(text)],
      shading: { type: ShadingType.CLEAR, color: 'auto', fill: warn ? 'FDECEB' : 'FFF4D6' },
      border: { left: { style: BorderStyle.SINGLE, size: 24, color, space: 8 } },
      spacing: { before: 80, after: 160 },
      indent: { left: 120, right: 120 },
    });
  }

  // width: shown size in px; tall images such as a certificate page pass a smaller one.
  function shot(file, caption, width = 600) {
    const data = fs.readFileSync(path.join(SHOTS, file));
    // PNG width/height from the IHDR chunk
    const w = data.readUInt32BE(16), h = data.readUInt32BE(20);
    const height = Math.round(width * h / w);
    return [
      new Paragraph({
        alignment: AlignmentType.CENTER, spacing: { before: 120, after: 60 },
        children: [new ImageRun({ type: 'png', data, transformation: { width, height }, altText: { title: caption, description: caption, name: file } })],
      }),
      new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 240 },
        children: [new TextRun({ text: caption, italics: true, size: 18, color: MUTED })] }),
    ];
  }

  function table(headers, rows, widths) {
    const total = widths.reduce((a, b) => a + b, 0);
    const border = { style: BorderStyle.SINGLE, size: 4, color: 'D6DCEC' };
    const borders = { top: border, bottom: border, left: border, right: border };
    const cell = (text, i, head) => new TableCell({
      borders, width: { size: widths[i], type: WidthType.DXA },
      shading: head ? { type: ShadingType.CLEAR, color: 'auto', fill: 'E4EFFD' } : undefined,
      margins: { top: 80, bottom: 80, left: 120, right: 120 },
      children: [new Paragraph({ children: runs(text, head ? { bold: true, color: INK } : {}) })],
    });
    return [new Table({
      width: { size: total, type: WidthType.DXA }, columnWidths: widths,
      rows: [new TableRow({ tableHeader: true, children: headers.map((h, i) => cell(h, i, true)) }),
        ...rows.map(r => new TableRow({ children: r.map((c, i) => cell(c, i, false)) }))],
    }), new Paragraph({ spacing: { after: 200 }, children: [] })];
  }

  function step(title, body, shots) {
    stepNo++;
    return [
      new Paragraph({ heading: HeadingLevel.HEADING_2, keepNext: true, children: [new TextRun(`Step ${stepNo}. ${title}`)] }),
      ...body,
      ...shots.flatMap(([f, c, w]) => shot(f, c, w)),
    ];
  }

  function part(n, title, intro) {
    return [
      new Paragraph({ children: [new PageBreak()] }),
      new Paragraph({ spacing: { after: 40 }, children: [new TextRun({ text: `${role.toUpperCase()} · PART ${n}`, bold: true, color: GOLD, size: 18 })] }),
      new Paragraph({ heading: HeadingLevel.HEADING_1, children: [new TextRun(title)] }),
      p(intro),
    ];
  }

  // Cover page + contents list for a guide.
  function cover(title, lede, facts) {
    return [
      new Paragraph({ spacing: { before: 2400, after: 120 }, children: [new TextRun({ text: 'TechLab', bold: true, color: BLUE, size: 28 })] }),
      new Paragraph({ heading: HeadingLevel.TITLE, children: [new TextRun(title)] }),
      p(lede),
      new Paragraph({ spacing: { after: 360 }, children: [] }),
      ...facts.map(f => p(f)),
      new Paragraph({ children: [new PageBreak()] }),
      new Paragraph({ spacing: { after: 160 }, children: [new TextRun({ text: 'Contents', bold: true, size: 36 })] }),
      new TableOfContents('Contents', { hyperlink: true, headingStyleRange: '1-1' }),
      new Paragraph({ children: [new TextRun({ text: 'If the contents list is empty, right-click it in Word and choose Update Field.', italics: true, size: 18, color: MUTED })] }),
    ];
  }

  function save(content, out, title) {
    const doc = new Document({
      creator: 'TechLab',
      title,
      styles: {
        default: { document: { run: { font: FONT, size: 22, color: INK } } },
        paragraphStyles: [
          { id: 'Title', name: 'Title', basedOn: 'Normal', next: 'Normal', run: { size: 56, bold: true, font: FONT, color: INK }, paragraph: { spacing: { after: 240 } } },
          { id: 'Heading1', name: 'Heading 1', basedOn: 'Normal', next: 'Normal', quickFormat: true, run: { size: 36, bold: true, font: FONT, color: INK }, paragraph: { spacing: { before: 120, after: 160 }, outlineLevel: 0 } },
          { id: 'Heading2', name: 'Heading 2', basedOn: 'Normal', next: 'Normal', quickFormat: true, run: { size: 26, bold: true, font: FONT, color: BLUE }, paragraph: { spacing: { before: 360, after: 120 }, outlineLevel: 1 } },
        ],
      },
      numbering: {
        config: [
          { reference: 'bullets', levels: [{ level: 0, format: LevelFormat.BULLET, text: '•', alignment: AlignmentType.LEFT, style: { paragraph: { indent: { left: 720, hanging: 360 } } } }] },
          ...numberRefs.map(ref => ({ reference: ref, levels: [{ level: 0, format: LevelFormat.DECIMAL, text: '%1.', alignment: AlignmentType.LEFT, style: { paragraph: { indent: { left: 720, hanging: 360 } } } }] })),
        ],
      },
      sections: [{
        properties: { page: { size: { width: 11906, height: 16838 }, margin: { top: 1440, right: 1440, bottom: 1440, left: 1440 } } },
        headers: { default: new Header({ children: [new Paragraph({ alignment: AlignmentType.RIGHT, children: [new TextRun({ text: title, size: 16, color: MUTED })] })] }) },
        footers: { default: new Footer({ children: [new Paragraph({ alignment: AlignmentType.CENTER, children: [new TextRun({ children: ['Page ', PageNumber.CURRENT], size: 16, color: MUTED })] })] }) },
        children: content,
      }],
    });
    return Packer.toBuffer(doc).then(buf => { fs.writeFileSync(out, buf); console.log('wrote', out, buf.length); });
  }

  return { p, bullet, numbered, note, shot, table, step, part, cover, save, PageBreak, Paragraph, TextRun, HeadingLevel };
};
