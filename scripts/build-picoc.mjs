// Rebuilds public/js/picoc.js from the picoc-js npm package.
// Run with: node scripts/build-picoc.mjs
// See resources/js/vendor/README.md for context.
//
// picoc-js only exports runC(), which gives no way to catch errors, feed scanf()
// input, or stop the program from exiting its host. One line is added so the
// Emscripten module factory itself is exported too (as picocjs.picoc).
import { readFileSync, writeFileSync } from 'node:fs';

const src = readFileSync('node_modules/picoc-js/dist/bundle.umd.js', 'utf8');
const anchor = '\texports.runC = runC;';

if (!src.includes(anchor)) {
    throw new Error('picoc-js changed shape: could not find "exports.runC = runC;" to patch.');
}

const header = '/* picoc-js 1.0.12 (MIT) — PicoC compiled to WebAssembly. Built by scripts/build-picoc.mjs; do not edit. */\n';
writeFileSync('public/js/picoc.js', header + src.replace(anchor, anchor + '\n\texports.picoc = picoc;'));

console.log('Built public/js/picoc.js');
