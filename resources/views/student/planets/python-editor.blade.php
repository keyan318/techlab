{{-- Python Interactive Editor Page — full Pyodide-powered Python editor.
     This is a standalone page that loads Pyodide and provides a
     complete code editing and execution experience. --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Python Editor | {{ $lesson['title'] ?? 'Python' }} · TechLab</title>
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
        .editor-header p {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-top: 4px;
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
        .editor-body {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .editor-textarea {
            width: 100%;
            min-height: 300px;
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
        .editor-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
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
        .editor-run-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
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
        .editor-test-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .editor-status {
            font-size: 0.85rem;
            color: #94a3b8;
        }
        .editor-output {
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
        .editor-output .info { color: #3b82f6; }
        .editor-loading {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #94a3b8;
            font-size: 0.85rem;
        }
        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #334155;
            border-top-color: #22c98a;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .editor-hint {
            color: #64748b;
            font-size: 0.8rem;
            margin-top: 8px;
        }
        .lesson-info {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
        }
        .lesson-info h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.25rem;
            color: #fff;
            margin-bottom: 8px;
        }
        .lesson-info p {
            color: #94a3b8;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .exercise-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
            border-bottom: 1px solid #334155;
            padding-bottom: 8px;
        }
        .exercise-tab {
            padding: 8px 16px;
            border-radius: 8px 8px 0 0;
            background: transparent;
            color: #94a3b8;
            border: none;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .exercise-tab.active {
            background: #1e293b;
            color: #22c98a;
            border-color: #334155;
        }
        .exercise-tab:hover:not(.active) {
            color: #fff;
        }
        .exercise-content {
            display: none;
        }
        .exercise-content.active {
            display: block;
        }
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
        .expected-output {
            background: #0e1230;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 12px;
            margin-top: 8px;
            font-family: 'Space Mono', monospace;
            font-size: 0.85rem;
            color: #94a3b8;
        }
        @media (min-width: 768px) {
            .editor-body {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <div class="editor-header">
            <div>
                <h1>🐍 Python Editor</h1>
                <p>{{ $lesson['title'] ?? 'Interactive Python Exercise' }}</p>
            </div>
            <a href="{{ route('student.planet', ['slug' => $slug]) }}" class="editor-back">← Back to lesson</a>
        </div>

        @php
            $hasInteractiveExercise = isset($lesson['interactive_exercise']);
            $hasChallenge = isset($lesson['challenge']);
        @endphp

        @if($hasInteractiveExercise || $hasChallenge)
        <div class="lesson-info">
            <h2>Exercise</h2>
            <div class="exercise-tabs">
                @if($hasInteractiveExercise)
                <button class="exercise-tab active" data-tab="interactive">Interactive Exercise</button>
                @endif
                @if($hasChallenge)
                <button class="exercise-tab" data-tab="challenge">Challenge</button>
                @endif
            </div>

            @if($hasInteractiveExercise)
            <div class="exercise-content active" id="tab-interactive">
                <p>{{ $lesson['interactive_exercise']['prompt'] }}</p>
                @if(isset($lesson['interactive_exercise']['expected_output']))
                <div class="expected-output">
                    <strong>Expected Output:</strong><br>
                    {{ $lesson['interactive_exercise']['expected_output'] }}
                </div>
                @endif
            </div>
            @endif

            @if($hasChallenge)
            <div class="exercise-content" id="tab-challenge">
                <p>{{ $lesson['challenge']['prompt'] }}</p>
                @if(isset($lesson['challenge']['expected_output']))
                <div class="expected-output">
                    <strong>Expected Output:</strong><br>
                    {{ $lesson['challenge']['expected_output'] }}
                </div>
                @endif
            </div>
            @endif
        </div>
        @endif

        <div class="editor-body">
            <div>
                <textarea class="editor-textarea" id="python-code">{{ $starterCode ?? '' }}</textarea>
                <p class="editor-hint">Write your Python code below and click Run to execute it in your browser using Pyodide.</p>

                <div class="editor-actions">
                    <button id="run-btn" class="editor-run-btn">Run Code</button>
                    @if($hasInteractiveExercise && isset($lesson['interactive_exercise']['expected_output']))
                    <button id="test-btn" class="editor-test-btn">Test Exercise</button>
                    @endif
                    <span id="editor-status" class="editor-status"></span>
                </div>

                <div id="editor-output" class="editor-output"></div>
                <div id="test-result" class="test-result" style="display: none;"></div>
            </div>
        </div>
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

            // Exercise tab switching
            document.querySelectorAll('.exercise-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    document.querySelectorAll('.exercise-tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.exercise-content').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('tab-' + this.dataset.tab).classList.add('active');

                    // Update starter code based on selected tab
                    @if($hasInteractiveExercise)
                    const interactiveCode = @json($lesson['interactive_exercise']['starter_code'] ?? '');
                    @endif
                    @if($hasChallenge)
                    const challengeCode = @json($lesson['challenge']['starter_code'] ?? '');
                    @endif

                    if (this.dataset.tab === 'interactive') {
                        codeArea.value = interactiveCode;
                    } else if (this.dataset.tab === 'challenge') {
                        codeArea.value = challengeCode;
                    }
                });
            });

            let pyodide = null;
            let loading = false;

            // Load Pyodide
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

            // Run button handler
            async function runCode(code) {
                if (loading) return null;

                loading = true;
                runBtn.disabled = true;
                if (testBtn) testBtn.disabled = true;
                status.textContent = 'Running...';
                output.textContent = '';
                testResult.style.display = 'none';

                try {
                    // Capture stdout
                    let captured = '';
                    pyodide.setStdout({ batched: (msg) => { captured += msg; } });
                    pyodide.setStderr({ batched: (msg) => { captured += msg; } });

                    await pyodide.runPython(code);
                    const result = captured || '(no output)';
                    output.innerHTML = '<span class="success">' + result.replace(/</g, '<').replace(/>/g, '>') + '</span>';
                    status.textContent = 'Done!';
                    return result;
                } catch (err) {
                    const errMsg = (err.message || err.toString() || 'Unknown error');
                    output.innerHTML = '<span class="error">' + errMsg.replace(/</g, '<').replace(/>/g, '>') + '</span>';
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

            // Test button handler - compares output with expected
            if (testBtn) {
                testBtn.addEventListener('click', async function() {
                    const code = codeArea.value;
                    if (!code.trim()) {
                        output.innerHTML = '<span class="error">Please write some Python code first.</span>';
                        return;
                    }

                    const result = await runCode(code);
                    if (result === null) return;

                    const expectedOutput = @json($lesson['interactive_exercise']['expected_output'] ?? '');
                    const normalizedResult = result.trim();
                    const normalizedExpected = expectedOutput.trim();

                    testResult.style.display = 'block';
                    if (normalizedResult === normalizedExpected) {
                        testResult.className = 'test-result pass';
                        testResult.innerHTML = '✅ Test Passed! Output matches expected.';
                    } else {
                        testResult.className = 'test-result fail';
                        testResult.innerHTML = '❌ Test Failed.<br>Expected: <code>' + normalizedExpected.replace(/</g, '<').replace(/>/g, '>') + '</code><br>Got: <code>' + normalizedResult.replace(/</g, '<').replace(/>/g, '>') + '</code>';
                    }
                });
            }
        })();
    </script>
</body>
</html>