/*
 * TechLab code highlighter + chat code-block renderer.
 *
 * Pure string -> HTML, no dependencies. Works in the browser (window.CodeHighlight)
 * and in Node (module.exports) so it can be unit-tested (tests/js/code-highlight.test.cjs).
 *
 *   highlight(code, lang)  -> HTML with <span class="tok-*"> around each token
 *   blockHtml(code, lang)  -> the full ChatGPT-style code box (header, Copy, code)
 *   wire(root)             -> one delegated click listener that powers every Copy button
 *
 * Token classes are coloured by rules injected once via injectStyles(); the Python
 * editor overlay uses the same classes, so chat and editor share one palette.
 */
(function (root, factory) {
  const api = factory();
  if (typeof module === 'object' && module.exports) module.exports = api;
  else root.CodeHighlight = api;
})(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  const KEYWORDS = new Set([
    'False', 'None', 'True', 'and', 'as', 'assert', 'async', 'await', 'break', 'class',
    'continue', 'def', 'del', 'elif', 'else', 'except', 'finally', 'for', 'from', 'global',
    'if', 'import', 'in', 'is', 'lambda', 'nonlocal', 'not', 'or', 'pass', 'raise',
    'return', 'try', 'while', 'with', 'yield', 'match', 'case',
  ]);
  const BUILTINS = new Set([
    'print', 'input', 'range', 'len', 'int', 'float', 'str', 'bool', 'list', 'dict', 'set',
    'tuple', 'type', 'sum', 'min', 'max', 'abs', 'round', 'sorted', 'reversed', 'enumerate',
    'zip', 'map', 'filter', 'open', 'isinstance', 'any', 'all', 'format', 'repr', 'id',
    'iter', 'next', 'chr', 'ord', 'pow', 'divmod', 'super', 'object', 'bytes', 'hex', 'bin',
  ]);
  const PY_LANGS = new Set(['python', 'py', 'python3', 'py3', '']);

  const esc = (s) => s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  const span = (cls, text) => '<span class="tok-' + cls + '">' + esc(text) + '</span>';

  // Body of an f-string: literal text stays string-coloured, {expr} is broken out.
  function fstringBody(text) {
    let out = '';
    let i = 0;
    let lit = '';
    const flush = () => { if (lit) { out += span('str', lit); lit = ''; } };
    while (i < text.length) {
      const c = text[i];
      if ((c === '{' || c === '}') && text[i + 1] === c) { lit += c + c; i += 2; continue; }   // {{ }} escapes
      if (c === '{') {
        let depth = 1;
        let j = i + 1;
        while (j < text.length && depth) { if (text[j] === '{') depth++; else if (text[j] === '}') depth--; j++; }
        if (depth === 0) {
          flush();
          out += span('interp', '{') + highlightPython(text.slice(i + 1, j - 1)) + span('interp', '}');
          i = j;
          continue;
        }
      }
      lit += c;
      i++;
    }
    flush();
    return out;
  }

  const TOKEN = new RegExp([
    '(#[^\\n]*)',                                                                   // 1 comment
    '([rRbBuUfF]{0,2})("""|\'\'\')',                                                // 2 prefix, 3 triple-quote open
    '([rRbBuUfF]{0,2})("|\')',                                                      // 4 prefix, 5 quote open
    '(\\b\\d[\\d_]*(?:\\.\\d[\\d_]*)?(?:[eE][+-]?\\d+)?\\b|\\.\\d+\\b)',            // 6 number
    '([A-Za-z_][A-Za-z0-9_]*)',                                                     // 7 identifier
    '(==|!=|<=|>=|\\*\\*=?|//=?|->|:=|[-+*/%=<>&|^~]=?)',                           // 8 operator
    '([()\\[\\]{},:.;@])',                                                          // 9 punctuation
  ].join('|'), 'g');

  function highlightPython(code) {
    let out = '';
    let last = 0;
    TOKEN.lastIndex = 0;
    let m;
    while ((m = TOKEN.exec(code)) !== null) {
      if (m.index > last) out += esc(code.slice(last, m.index));
      last = TOKEN.lastIndex;

      if (m[1] !== undefined) { out += span('comment', m[1]); continue; }

      const prefix = m[2] !== undefined && m[3] !== undefined ? m[2] : m[4];
      const quote = m[3] || m[5];
      if (quote) {
        // Find the closing quote (honouring backslashes; single-quoted strings stop at newline).
        const triple = quote.length === 3;
        let j = TOKEN.lastIndex;
        let closed = false;
        while (j < code.length) {
          if (code[j] === '\\') { j += 2; continue; }
          if (triple ? code.startsWith(quote, j) : code[j] === quote[0]) { closed = true; break; }
          if (!triple && code[j] === '\n') break;
          j++;
        }
        j = Math.min(j, code.length);
        const bodyEnd = j;
        const end = closed ? j + quote.length : j;
        const body = code.slice(TOKEN.lastIndex, bodyEnd);
        const isF = /f/i.test(prefix || '');
        out += (prefix ? span('str', prefix) : '') + span('str', quote) +
               (isF ? fstringBody(body) : span('str', body)) + (closed ? span('str', quote) : '');
        last = end;
        TOKEN.lastIndex = end;
        continue;
      }

      if (m[6] !== undefined) { out += span('num', m[6]); continue; }
      if (m[7] !== undefined) {
        const w = m[7];
        const after = code.slice(TOKEN.lastIndex).match(/^\s*\(/);
        if (KEYWORDS.has(w)) out += span(w === 'True' || w === 'False' || w === 'None' ? 'const' : 'kw', w);
        else if (BUILTINS.has(w)) out += span('builtin', w);
        else if (/^\s*def\s+$/.test(code.slice(Math.max(0, m.index - 12), m.index)) || after) out += span('fn', w);
        else out += span('var', w);
        continue;
      }
      if (m[8] !== undefined) { out += span('op', m[8]); continue; }
      if (m[9] !== undefined) { out += span('punct', m[9]); continue; }
    }
    if (last < code.length) out += esc(code.slice(last));
    return out;
  }

  function highlight(code, lang) {
    const l = String(lang || '').toLowerCase();
    return PY_LANGS.has(l) ? highlightPython(code) : esc(code);   // unknown languages: safe, uncoloured
  }

  const LABELS = { py: 'Python', python: 'Python', python3: 'Python', js: 'JavaScript', javascript: 'JavaScript',
    ts: 'TypeScript', sh: 'Shell', bash: 'Bash', html: 'HTML', css: 'CSS', json: 'JSON', php: 'PHP', sql: 'SQL' };
  const label = (lang) => LABELS[String(lang || '').toLowerCase()] || (lang ? String(lang) : 'Code');

  const ICON_CODE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="m8 8-4 4 4 4M16 8l4 4-4 4"/></svg>';
  const ICON_COPY = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><rect x="9" y="9" width="12" height="12" rx="2.5"/><path d="M5 15V6a2 2 0 0 1 2-2h8"/></svg>';

  // Static, view-and-copy only. Deliberately no Run control.
  function blockHtml(code, lang) {
    const src = String(code).replace(/\n$/, '');
    return '<div class="tl-code" data-code="' + esc(src) + '">' +
      '<div class="tl-code-head"><span class="tl-code-lang">' + ICON_CODE + esc(label(lang)) + '</span>' +
      '<button type="button" class="tl-code-copy" data-tl-copy aria-label="Copy code">' + ICON_COPY + '<span>Copy</span></button></div>' +
      '<pre class="tl-code-pre"><code>' + highlight(src, lang) + '</code></pre></div>';
  }

  const CSS = [
    '.tl-code{margin:.75rem 0;border:1px solid rgba(150,170,255,.22);border-radius:14px;background:#0b0f24;overflow:hidden;text-align:left}',
    '.tl-code-head{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:8px 10px 8px 14px;background:rgba(150,170,255,.07);border-bottom:1px solid rgba(150,170,255,.14)}',
    '.tl-code-lang{display:inline-flex;align-items:center;gap:7px;font:600 12px/1 "Space Mono",ui-monospace,monospace;letter-spacing:.02em;color:#a9b4e6}',
    '.tl-code-copy{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border:1px solid rgba(150,170,255,.25);border-radius:9px;background:transparent;color:#c7d0f5;font:500 12px/1 inherit;cursor:pointer;transition:background .15s,color .15s,border-color .15s}',
    '.tl-code-copy:hover{background:rgba(115,182,255,.14);border-color:rgba(115,182,255,.5);color:#fff}',
    '.tl-code-copy.is-copied{color:#22c98a;border-color:rgba(34,201,138,.5)}',
    '.tl-code-pre{margin:0;padding:14px 16px;overflow-x:auto;font:400 13.5px/1.7 "Space Mono",ui-monospace,SFMono-Regular,Menlo,monospace;color:#e6e9ff;white-space:pre;tab-size:4;background:transparent}',
    '.tl-code .tl-code-pre code{all:unset;font:inherit;white-space:pre;color:inherit;background:none;padding:0}',
    '.tok-comment{color:#7c86ad;font-style:italic}.tok-kw{color:#ff79c6}.tok-const{color:#ffb86c}',
    '.tok-builtin{color:#8be9fd}.tok-fn{color:#82aaff}.tok-str{color:#7ee787}.tok-num{color:#ffb86c}',
    '.tok-var{color:#e6e9ff}.tok-op{color:#ff79c6}.tok-punct{color:#a9b4e6}.tok-interp{color:#ffb86c}',
  ].join('');

  function injectStyles() {
    if (typeof document === 'undefined' || document.getElementById('tl-code-css')) return;
    const s = document.createElement('style');
    s.id = 'tl-code-css';
    s.textContent = CSS;
    document.head.appendChild(s);
  }

  // Delegated so it survives x-html re-renders while a reply streams in.
  function wire(rootEl) {
    injectStyles();
    (rootEl || document).addEventListener('click', function (e) {
      const btn = e.target.closest && e.target.closest('[data-tl-copy]');
      if (!btn) return;
      const text = btn.closest('.tl-code').getAttribute('data-code') || '';
      const done = () => {
        const label = btn.querySelector('span');
        btn.classList.add('is-copied');
        if (label) label.textContent = 'Copied';
        setTimeout(() => { btn.classList.remove('is-copied'); if (label) label.textContent = 'Copy'; }, 1600);
      };
      const fallback = () => {
        const t = document.createElement('textarea');
        t.value = text; t.style.position = 'fixed'; t.style.opacity = '0';
        document.body.appendChild(t); t.select();
        try { document.execCommand('copy'); done(); } catch (_) {}
        t.remove();
      };
      if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(text).then(done, fallback);
      else fallback();
    });
  }

  return { highlight, blockHtml, wire, injectStyles, CSS, label };
});
