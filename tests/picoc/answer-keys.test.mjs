// PicoC runner + Computer Programming 2 answer keys. Run: node --test "tests/picoc/*.test.mjs"
//
// Loads public/js/picoc-worker.js the way a browser Web Worker would (a fake `self` and
// importScripts), then:
//   1. runs every Programming 2 lesson's model answer (its hint_code) and checks the output
//      matches that lesson's answer key in config/course-structure.php exactly;
//   2. pins the worker's fixes for PicoC's gaps (array parameters, typedef return types,
//      prompts without a newline), so a future picoc.js rebuild can't quietly break them.
// Needs `php` on the PATH to read the answer keys from the config.
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { execFileSync } from 'node:child_process';
import { readFileSync, readdirSync } from 'node:fs';
import { createRequire } from 'node:module';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const root = fileURLToPath(new URL('../../', import.meta.url));
const js = path.join(root, 'public/js');

// --- a fake Web Worker global for the worker script
const require = createRequire(import.meta.url);
const inbox = [];
globalThis.self = globalThis;
// picoc.js's UMD wrapper looks these up on the global when there is no CommonJS module.
Object.assign(globalThis, { require$$0: require('path'), require$$1: require('fs'), require$$2: require('crypto'), require$$3: require('child_process') });
globalThis.importScripts = (file) => (0, eval)(readFileSync(path.join(js, file), 'utf8'));
globalThis.postMessage = (message) => inbox.push(message);
(0, eval)(readFileSync(path.join(js, 'picoc-worker.js'), 'utf8'));

function run(code, input = '') {
    return new Promise((resolve) => {
        const before = inbox.length;
        const wait = setInterval(() => {
            if (inbox.length > before) {
                clearInterval(wait);
                resolve(inbox[inbox.length - 1]);
            }
        }, 1);
        globalThis.onmessage({ data: { code, input } });
    });
}

const decode = (s) => s.replace(/&quot;/g, '"').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&#039;/g, "'").replace(/&amp;/g, '&');

// --- the answer keys, straight from the config the server checks against
const blueprint = JSON.parse(execFileSync('php', ['-r', 'echo json_encode((require "config/course-structure.php")["computer-programming-2"]["modules"]);'], { cwd: root }));
const lessonDir = path.join(root, 'resources/views/student/planets/programming/c2_course');

test('the worker says it is ready', () => {
    assert.deepEqual(inbox[0], { ready: true });
});

for (const [module, { lessons }] of Object.entries(blueprint)) {
    for (const [lesson, { title, expected }] of Object.entries(lessons)) {
        test(`${module} ${lesson} "${title}": the model answer prints the answer key`, async () => {
            const html = readFileSync(path.join(lessonDir, module, `${lesson}.blade.php`), 'utf8');
            const hint = html.match(/<input type="hidden" name="hint_code"\s+value="([^"]*)">/);
            assert.ok(hint, 'the lesson has a hint_code');
            const result = await run(decode(hint[1]));
            assert.equal(result.ok, true, result.error);
            assert.equal(result.output, expected);
        });
    }
}

test('every lesson file belongs to the blueprint', () => {
    const files = readdirSync(lessonDir).flatMap((m) => readdirSync(path.join(lessonDir, m)).map((f) => `${m}/${f.replace('.blade.php', '')}`));
    const keys = Object.entries(blueprint).flatMap(([m, { lessons }]) => Object.keys(lessons).map((l) => `${m}/${l}`));
    assert.deepEqual(files.sort(), keys.sort());
});

// --- the worker's fixes for PicoC

test('an array parameter is the caller\'s array, as in real C', async () => {
    const r = await run('#include <stdio.h>\nvoid relight(int lamps[], int n) { for (int i = 0; i < n; i++) { if (lamps[i] == 0) lamps[i] = 50; } }\nint main() { int l[3] = {0, 5, 0}; relight(l, 3); printf("%d %d %d\\n", l[0], l[1], l[2]); return 0; }');
    assert.deepEqual(r, { ok: true, output: '50 5 50\n' });
});

test('char[] and struct-array parameters write back too, and indexing is left alone', async () => {
    const r = await run('#include <stdio.h>\n#include <string.h>\nstruct M { int ok; };\nvoid name(char out[]) { strcpy(out, "Park"); }\nvoid fix(struct M r[], int n) { r[1].ok = 1; }\nint main() { char s[10]; struct M r[2]; r[1].ok = 0; name(s); fix(r, 2); int c = sizeof(r) / sizeof(r[0]); printf("%s %d %d\\n", s, r[1].ok, c); return 0; }');
    assert.deepEqual(r, { ok: true, output: 'Park 1 2\n' });
});

test('a function may return a typedef name, tagged or not', async () => {
    const r = await run('#include <stdio.h>\n#include <stdlib.h>\n#include <string.h>\ntypedef struct { char name[12]; int bells; } Tower;\ntypedef struct Node { int v; struct Node *next; } Node;\nTower makeTower(char *n, int b) { Tower t; strcpy(t.name, n); t.bells = b; return t; }\nNode *push(Node *h, int v) { Node *n = (Node *) malloc(sizeof(Node)); n->v = v; n->next = h; return n; }\nint main() { Tower t = makeTower("Clock", 3); Node *h = push(push(NULL, 1), 2); printf("%s %d %d %d\\n", t.name, t.bells, h->v, h->next->v); return 0; }');
    assert.deepEqual(r, { ok: true, output: 'Clock 3 2 1\n' });
});

test('a prompt with no newline still shows, and scanf reads the program input', async () => {
    const r = await run('#include <stdio.h>\nint main() { int f; printf("Enter fuel: "); scanf("%d", &f); printf("Left: %d", 100 - f); return 0; }', '75');
    assert.deepEqual(r, { ok: true, output: 'Enter fuel: Left: 25' });
});

test('a compile error names the student\'s own line number', async () => {
    const r = await run('#include <stdio.h>\nvoid f(int a[]) {\n    a[0] = 1\n}\nint main() { return 0; }');
    assert.equal(r.ok, false);
    assert.match(r.error, /^Error on line 4: ';' expected/);
});

test('every run starts with no files left over from the last one', async () => {
    const code = '#include <stdio.h>\nint main() { FILE *f = fopen("log.txt", "r"); printf(f == NULL ? "fresh\\n" : "leaked\\n"); f = fopen("log.txt", "w"); fputs("x", f); fclose(f); return 0; }';
    await run(code);
    assert.deepEqual(await run(code), { ok: true, output: 'fresh\n' });
});
