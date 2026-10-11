# public/js/jscpp.bundle.js

Self-hosted build of [JSCPP](https://github.com/felixhao28/JSCPP) `2.0.9`, the
pure-JS C/C++ interpreter that powers the C course's in-browser code runner
(`resources/views/student/planets/code-editor.blade.php`), the same way
Pyodide powers Python's.

JSCPP ships as CommonJS with no browser build, so it's bundled here with
esbuild into a single IIFE that exposes `window.JSCPP`, and the output is
committed straight to `public/js/` like `code-highlight.js` and `theme.js` —
this app loads all of its page scripts as plain `<script src>` tags, not
through Vite.

To rebuild after bumping the `JSCPP` npm version:

```bash
npx esbuild resources/js/vendor/jscpp-entry.js --bundle --minify --format=iife \
  --outfile=public/js/jscpp.bundle.js
```
