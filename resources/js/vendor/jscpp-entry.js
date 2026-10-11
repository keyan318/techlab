// Entry point for the self-hosted JSCPP bundle (public/js/jscpp.bundle.js).
// JSCPP is a pure-JS C/C++ interpreter (github.com/felixhao28/JSCPP) — the same
// role Pyodide plays for Python, but it needs no CDN wasm blob and runs synchronously.
// Bundled with esbuild into an IIFE exposing window.JSCPP; see resources/js/vendor/README.md.
import * as JSCPP from 'JSCPP';

// `self` so this loads both on the main thread (script tag, where self === window)
// and inside the Web Worker that actually runs student code (see jscpp-worker.js).
self.JSCPP = JSCPP;
