// Rebuilds public/js/jscpp.bundle.js from the JSCPP npm package.
// Run with: node scripts/build-jscpp.mjs
// See resources/js/vendor/README.md for context.
import { build } from 'esbuild';
import { polyfillNode } from 'esbuild-plugin-polyfill-node';

await build({
    entryPoints: ['resources/js/vendor/jscpp-entry.js'],
    bundle: true,
    minify: true,
    format: 'iife',
    outfile: 'public/js/jscpp.bundle.js',
    plugins: [polyfillNode()],
});

console.log('Built public/js/jscpp.bundle.js');
