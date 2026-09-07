{{-- Python Interactive Editor Page — reusable Pyodide-powered Python editor.
     This page knows NOTHING about lessons, modules, or exercises. It only
     knows: starter code (optional), an expected output to check against
     (optional), and where "Back" should go. Every lesson page builds its
     own link to this page with its own starter_code / expected / return_to
     values — this file never needs to change again when new lessons are added. --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Python Editor · TechLab</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #0e1230;
            color: #e2e8f0;
            min-height: 100vh;
        }
        .editor-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 32px 24px;
        }
        .editor-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }
        .editor-header h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            color: #fff;
        }
        .editor-back {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 999px;
            background: #1e293b;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            border: 1px solid #334155;
            cursor: pointer;
        }
        .editor-back:hover { background: #334155; color: #fff; }
        .editor-textarea {
            width: 100%;
            min-height: 320px;
            padding: 20px;
            border: 1px solid #334155;
            border-radius: 12px;
            background: #1e293b;
            color: #22c98a;
            font-family: 'Space Mono', monospace;
            font-size: 0.95rem;
            line-height: 1.6;
            resize: vertical;
            outline: none;
        }
        .editor-textarea:focus { border-color: #22c98a; }
        .editor-hint {
            color: #64748b;
            font-size: 0.8rem;
            margin-top: 8px;
        }
        .editor-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 16px;
        }
        .editor-run-btn {
            padding: 12px 28px;
            border-radius: 8px;
            background: #22c98a;
            color: #0e1230;
            border: none;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .editor-run-btn:hover { opacity: 0.85; }
        .editor-run-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .editor-test-btn {
            padding: 12px 28px;
            border-radius: 8px;
            background: #3b82f6;
            color: #fff;
            border: none;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .editor-test-btn:hover { opacity: 0.85; }
        .editor-test-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .editor-status {
            font-size: 0.85rem;
            color: #94a3b8;
        }
        .editor-output {
            margin-top: 16px;
            padding: 20px;
            border-radius: 12px;
            background: #1e293b;
            border: 1px solid #334155;
            font-family: 'Space Mono', monospace;
            font-size: 0.9rem;
            line-height: 1.6;
            white-space: pre-wrap;
            min-height: 60px;
            color: #e2e8f0;
        }
        .editor-output .error { color: #f87171; }
        .editor-output .success { color: #22c98a; }
        .test-result {
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 12px;
            font-family: 'Space Mono', monospace;
            font-size: 0.85rem;
        }
        .test-result.pass {
            background: rgba(34, 201, 138, 0.15);
            border: 1px solid #22c98a;
            color: #22c98a;
        }
        .test-result.fail {
            background: rgba(248, 113, 113, 0.15);
            border: 1px solid #f87171;
            color: #f87171;
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <div class="editor-header">
            <h1>🐍 Python Editor</h1>
            <a href="{{ $returnTo ?? route('student.planet', ['slug' => $slug]) }}" class="editor-back">← Back to lesson</a>
        </div>

        <textarea class="editor-textarea" id="python-code">{{ $starterCode ?? '' }}</textarea>
        <p class="editor-hint">Write your Python code below and click Run to execute it in your browser using Pyodide.</p>

        <div class="editor-actions">
            <button id="run-btn" class="editor-run-btn">Run Code</button>
            @if(!empty($expected))
            <button id="test-btn" class="editor-test-btn">Check Answer</button>
            @endif
            <span id="editor-status" class="editor-status"></span>
        </div>

        <div id="editor-output" class="editor-output"></div>
        <div id="test-result" class="test-result" style="display: none;"></div>
    </div>

    {{-- Load Pyodide --}}
    <script src="https://cdn.jsdelivr.net/pyodide/v0.26.4/full/pyodide.js"></script>
    <script>
        (async function() {
            const runBtn = document.getElementById('run-btn');
            const testBtn = document.getElementById('test-btn');
            const output = document.getElementById('editor-output');
            const testResult = document.getElementById('test-result');
            const status = document.getElementById('editor-status');
            const codeArea = document.getElementById('python-code');

            // The expected answer, if any. Never shown on screen — only
            // used internally to compare against what the code prints.
            const expectedOutput = @json($expected ?? null);

            let pyodide = null;
            let loading = false;

            status.textContent = 'Loading Python runtime...';
            try {
                pyodide = await window.loadPyodide();
                status.textContent = 'Python ready!';
            } catch (err) {
                status.textContent = 'Failed to load Python';
                output.innerHTML = '<span class="error">Error: ' + (err.message || 'Failed to load Pyodide') + '</span>';
                runBtn.disabled = true;
                if (testBtn) testBtn.disabled = true;
                return;
            }

            async function runCode(code) {
                if (loading) return null;

                loading = true;
                runBtn.disabled = true;
                if (testBtn) testBtn.disabled = true;
                status.textContent = 'Running...';
                output.textContent = '';
                testResult.style.display = 'none';

                try {
                    let captured = '';
                    pyodide.setStdout({ batched: (msg) => { captured += msg; } });
                    pyodide.setStderr({ batched: (msg) => { captured += msg; } });

                    await pyodide.runPythonAsync(code);
                    const result = captured || '(no output)';
                    output.innerHTML = '<span class="success">' + result.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</span>';
                    status.textContent = 'Done!';
                    return result;
                } catch (err) {
                    const errMsg = (err.message || err.toString() || 'Unknown error');
                    output.innerHTML = '<span class="error">' + errMsg.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</span>';
                    status.textContent = 'Error';
                    return null;
                } finally {
                    loading = false;
                    runBtn.disabled = false;
                    if (testBtn) testBtn.disabled = false;
                }
            }

            runBtn.addEventListener('click', async function() {
                const code = codeArea.value;
                if (!code.trim()) {
                    output.innerHTML = '<span class="error">Please write some Python code first.</span>';
                    return;
                }
                await runCode(code);
            });

            if (testBtn) {
                testBtn.addEventListener('click', async function() {
                    const code = codeArea.value;
                    if (!code.trim()) {
                        output.innerHTML = '<span class="error">Please write some Python code first.</span>';
                        return;
                    }

                    const result = await runCode(code);
                    if (result === null) return;

                    const normalizedResult = result.trim();
                    const normalizedExpected = (expectedOutput || '').trim();

                    testResult.style.display = 'block';
                    if (normalizedResult === normalizedExpected) {
                        testResult.className = 'test-result pass';
                        testResult.innerHTML = '✅ Test Passed! Output matches expected.';
                    } else {
                        testResult.className = 'test-result fail';
                        testResult.innerHTML = '❌ Not quite yet. Run your code and check the output above, then try again.';
                    }
                });
            }
        })();
    </script>
</body>
</html>