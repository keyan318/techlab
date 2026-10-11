// Runs C code through JSCPP off the main thread.
// JSCPP.run() is synchronous — a student's infinite loop would otherwise freeze
// the whole editor tab. Running it in a Worker lets the page kill it on a timeout
// (see code-editor.blade.php) by simply terminating this worker (and starting a
// fresh one for the next Run — a terminated worker can't be reused).
importScripts('jscpp.bundle.js');

// Lets the page show "C ready" as soon as the worker (and JSCPP) has actually loaded,
// the same way the Python editor waits for Pyodide, instead of assuming it's instant.
self.postMessage({ ready: true });

self.onmessage = function (e) {
    const { code, input } = e.data;
    let out = '';
    try {
        const exitCode = self.JSCPP.run(code, input || '', {
            stdio: { write: function (s) { out += s; } },
        });
        self.postMessage({ ok: true, output: out, exitCode: exitCode });
    } catch (err) {
        self.postMessage({ ok: false, output: out, error: (err && err.message) || String(err) });
    }
};
