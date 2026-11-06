// Runs C code through PicoC (public/js/picoc.js) off the main thread.
// Same message contract as jscpp-worker.js, so the editor can use either:
//   page -> worker  { code, input }
//   worker -> page  { ready: true } once loaded, then per run
//                   { ok: true, output } or { ok: false, output, error }
// PicoC, unlike JSCPP, runs structs, malloc/free and FILE* (fopen/fprintf/fread...),
// which Computer Programming 2 needs. A fresh PicoC instance is made for every run,
// so no variables or files carry over from one run to the next.
importScripts('picoc.js');

self.postMessage({ ready: true });

// PicoC reports a compile or runtime error on stdout as three lines, then stops:
//   <the offending source line>
//   <spaces>^
//   file.c:<line>:<column> <message>
const PICOC_ERROR = /(?:^|\n)([^\n]*)\n( *\^)\nfile\.c:(\d+):\d+ ([^\n]*)\n?$/;

// PicoC copies a parameter written as an array (int lamps[], char s[20], struct M r[]),
// but real C passes such a parameter as a pointer to the caller's array, so changes
// made inside the function must reach the caller. Programming 2 teaches exactly that,
// so one-dimensional array parameters are rewritten to pointers before running:
// "int lamps[]" becomes "int *lamps". Only function headers and prototypes are touched
// (a parameter list followed by { or ;), and only on the same line, so the student's
// line numbers stay right. 2D parameters (int grid[][3]) stay as they are: PicoC has no
// pointer-to-array type, so a function can read a 2D argument but its writes stay local.
// type, then a space and/or stars, then the name, then one [] or [N], and nothing else.
const ONE_D_PARAM = /^(\s*(?:const\s+)?(?:struct\s+)?[A-Za-z_]\w*(?:\s+[A-Za-z_]\w*)*)(\s*\*+\s*|\s+)([A-Za-z_]\w*)\s*\[\s*\w*\s*\]\s*$/;

function arrayParamsToPointers(src) {
    return src.replace(/(\b[A-Za-z_]\w*\s*\()([^()]*)(\)\s*(?:\{|;))/g, (all, open, params, close) => {
        if (!params.includes('[')) return all;
        const fixed = params.split(',').map((p) =>
            p.replace(ONE_D_PARAM, (m, type, sep, name) => type + ' ' + sep.replace(/\s/g, '') + '*' + name));
        return open + fixed.join(',') + close;
    });
}

// PicoC can't parse a function whose return type is a typedef name ("Node *createNode(...)",
// "Tower makeTower(...)") although "struct Node *createNode(...)" works, and textbooks write
// the first form all the time. So: give every typedef'd struct a tag (its own name, if it
// had none: legal C, tags live apart from type names) and spell those return types out
// as "struct Tag". Like the rewrite above, it never adds or removes a line.
function typedefReturnTypes(src) {
    const found = [];
    const head = /typedef\s+struct\s*([A-Za-z_]\w*)?\s*\{/g;
    let m;
    while ((m = head.exec(src))) {
        let depth = 0, end = m.index + m[0].length - 1;
        for (; end < src.length; end++) {
            if (src[end] === '{') depth++;
            else if (src[end] === '}' && --depth === 0) break;
        }
        const name = src.slice(end + 1).match(/^\s*([A-Za-z_]\w*)\s*;/);
        if (name) found.push({ name: name[1], tag: m[1] || name[1], tagAt: m[1] ? -1 : m.index + m[0].length - 1 });
    }
    // Insert missing tags from the end backwards, so earlier offsets stay valid.
    for (const t of found.slice().reverse()) {
        if (t.tagAt >= 0) src = src.slice(0, t.tagAt) + t.tag + ' ' + src.slice(t.tagAt);
    }
    for (const t of found) {
        const returns = new RegExp('^(\\s*(?:static\\s+)?)' + t.name + '\\b(\\s*\\**\\s*)([A-Za-z_]\\w*\\s*\\()', 'gm');
        src = src.replace(returns, (all, lead, stars, fn) => lead + 'struct ' + t.tag + (stars.trim() ? ' ' + stars.trim() : ' ') + fn);
    }
    return src;
}

function runOnce(code, input) {
    return new Promise((resolve) => {
        const bytes = [];
        const stdin = new TextEncoder().encode(input || '');
        let next = 0;
        const fixed = typedefReturnTypes(arrayParamsToPointers(code));

        // stdout is line-buffered inside the C library, so a last line with no "\n"
        // (a prompt like "Enter fuel: ") would never arrive. Turning buffering off from
        // a statement placed after main() keeps the student's own line numbers intact;
        // PicoC runs file-level statements before it calls main(). setvbuf needs
        // <stdio.h>, and every program that prints includes it.
        const source = /#\s*include\s*<stdio\.h>/.test(fixed)
            ? fixed + '\nsetvbuf(stdout, NULL, _IONBF, 0);\n'
            : fixed;

        const pc = self.picocjs.picoc({
            stdout: (c) => { bytes.push(c); },
            printErr: () => {},
            stdin: () => (next < stdin.length ? stdin[next++] : null),
            // exit() / main() returning: unwind instead of shutting the worker down.
            quit: (status, toThrow) => { throw toThrow; },
        });

        pc.onRuntimeInitialized = () => {
            let crash = null;
            try {
                pc.runc(source, () => {});
            } catch (e) {
                if (!(e && e.name === 'ExitStatus')) crash = e;
            }
            resolve({ output: new TextDecoder().decode(new Uint8Array(bytes)), crash });
        };
    });
}

self.onmessage = async function (e) {
    const { code, input } = e.data;
    try {
        const { output, crash } = await runOnce(code, input);
        const m = output.match(PICOC_ERROR);

        if (m) {
            const before = output.slice(0, m.index + (output[m.index] === '\n' ? 1 : 0));
            const [, line, caret, lineNo, message] = m;
            // PicoC's "out of memory" almost always means a function calling itself:
            // either hundreds of times deep, or inside a ? : (which this interpreter can't do).
            const tip = message === 'out of memory'
                ? '\n\nTip: a function that calls itself hundreds of times runs out of room here, and so does one that calls itself inside ? : — write that part as an if/else instead.'
                : '';
            const error = (before ? before + '\n' : '') +
                'Error on line ' + lineNo + ': ' + message + '\n\n' + line + '\n' + caret + tip;
            self.postMessage({ ok: false, output: before, error: error });
        } else if (crash) {
            const why = String((crash && crash.message) || crash);
            self.postMessage({
                ok: false,
                output: output,
                error: (output ? output + '\n' : '') +
                    'Your program crashed (' + why + '). A function that calls itself forever, or a pointer to memory that was never set up, can do this.',
            });
        } else {
            self.postMessage({ ok: true, output: output });
        }
    } catch (err) {
        self.postMessage({ ok: false, output: '', error: (err && err.message) || String(err) });
    }
};
