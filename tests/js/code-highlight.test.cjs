// Run with:  node tests/js/code-highlight.test.cjs
// Loads the exact file the browser gets (see astro-status.test.cjs for the pattern).
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const mod = { exports: {} };
new Function('module', fs.readFileSync(path.join(__dirname, '../../public/js/code-highlight.js'), 'utf8'))(mod);
const H = mod.exports;

let passed = 0;
const test = (name, fn) => { fn(); passed++; console.log('  ok  ' + name); };

const SAMPLE = `# Simple Python program: Greet the user

print("Hello! Welcome to Python!")

name = input("What’s your name? ")
print(f"Nice to meet you, {name}! 😊")

# Bonus: Count to 3
print("Let’s count together:")
for i in range(1, 4):
    print(i)`;

const out = H.highlight(SAMPLE, 'python');
const has = (cls, text) => assert.ok(out.includes(`<span class="tok-${cls}">${text}</span>`), `${cls}: ${text}`);
const textOf = (html) => html.replace(/<[^>]+>/g, '').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&amp;/g, '&');

test('keywords', () => { has('kw', 'for'); has('kw', 'in'); });
test('builtins', () => { has('builtin', 'print'); has('builtin', 'input'); has('builtin', 'range'); });
test('numbers', () => { has('num', '1'); has('num', '4'); });
test('variables', () => { has('var', 'name'); has('var', 'i'); });
test('comments', () => { has('comment', '# Simple Python program: Greet the user'); has('comment', '# Bonus: Count to 3'); });
test('strings', () => { has('str', 'Hello! Welcome to Python!'); });
test('operators and punctuation', () => { has('op', '='); has('punct', '('); has('punct', ':'); });
test('f-string interpolation is broken out', () => {
  has('interp', '{'); has('interp', '}');
  assert.ok(out.includes('<span class="tok-interp">{</span><span class="tok-var">name</span><span class="tok-interp">}</span>'));
  has('str', 'Nice to meet you, ');
});
test('text is preserved exactly (indentation, blank lines, emoji)', () => { assert.equal(textOf(out), SAMPLE); });
test('not one colour', () => { assert.ok(new Set(out.match(/tok-[a-z]+/g)).size >= 8); });
test('a # inside a string is not a comment', () => {
  const h = H.highlight('x = "a # b"', 'python');
  assert.ok(!h.includes('tok-comment'));
});
test('HTML in code is escaped (no injection)', () => {
  const h = H.highlight('print("<script>alert(1)</script>")', 'python');
  assert.ok(!h.includes('<script>'));
  const b = H.blockHtml('"><img src=x onerror=alert(1)>', 'python');
  assert.ok(!b.includes('<img'));
});
test('unclosed string while streaming does not throw', () => { H.highlight('print("hello', 'python'); H.highlight('"""abc', 'python'); });
test('unknown language: escaped, uncoloured', () => {
  const h = H.highlight('<b>x</b>', 'brainfuck');
  assert.equal(h, '&lt;b&gt;x&lt;/b&gt;');
});
test('block has Copy, carries the whole source, and has NO Run control', () => {
  const b = H.blockHtml(SAMPLE + '\n', 'python');
  assert.ok(b.includes('data-tl-copy'));
  assert.ok(!/run/i.test(b.replace(/tok-[a-z]+/g, '')) || !/>\s*Run\s*</.test(b));
  assert.ok(!/<button[^>]*>[^<]*Run/i.test(b));
  const attr = b.match(/data-code="([^"]*)"/)[1].replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&amp;/g, '&');
  assert.equal(attr, SAMPLE);
  assert.ok(b.includes('>Python<') || b.includes('Python</span>'));
});

console.log(`\n${passed} passed`);
