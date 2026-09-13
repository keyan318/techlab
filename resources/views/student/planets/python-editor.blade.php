{{-- Python Interactive Editor — Codedex-style split layout.
     Left panel  → instruction card (flippable: instructions ↔ Astro's hint)
     Right panel → line-numbered code editor + Run / Submit + Terminal

     Data arrives via session flash (POSTed from each lesson's form).
     This file never needs to know which lesson it is — zero hardcoded content. --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('editor_title', 'Exercise') }} · Python Editor · TechLab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #0e1230;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Top nav bar ─────────────────────────────────────────────── */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            height: 52px;
            background: #090d1f;
            border-bottom: 1px solid #1e2a45;
            flex-shrink: 0;
        }
        .top-bar-left { display: flex; align-items: center; gap: 12px; }
        .top-bar-logo {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: #22c98a;
            text-decoration: none;
        }
        .top-bar-breadcrumb { color: #475569; font-size: 0.8rem; }
        .top-bar-breadcrumb span { color: #94a3b8; }
        .top-bar-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            background: #1e293b;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid #334155;
            transition: background 0.15s, color 0.15s;
        }
        .top-bar-back:hover { background: #334155; color: #fff; }

        /* ── Split layout ────────────────────────────────────────────── */
        .editor-layout {
            display: grid;
            grid-template-columns: 360px 1fr;
            flex: 1;
            overflow: hidden;
            height: calc(100vh - 52px);
        }

        /* ── LEFT PANEL ──────────────────────────────────────────────── */
        .left-panel {
            display: flex;
            flex-direction: column;
            background: #090d1f;
            border-right: 1px solid #1e2a45;
            overflow: hidden;
        }

        .card-scene {
            flex: 1;
            perspective: 1200px;
            padding: 20px;
            overflow-y: auto;
        }
        .card-scene::-webkit-scrollbar { width: 4px; }
        .card-scene::-webkit-scrollbar-thumb { background: #1e2a45; border-radius: 4px; }

        .card-flipper {
            position: relative;
            width: 100%;
            min-height: 480px;
            transform-style: preserve-3d;
            transition: transform 0.55s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-flipper.flipped { transform: rotateY(180deg); }

        .card-face {
            position: absolute;
            top: 0; left: 0; right: 0;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            border-radius: 16px;
            padding: 24px;
            min-height: 480px;
        }

        /* Front — instructions */
        .card-front {
            background: linear-gradient(145deg, #131d3b 0%, #0e1a36 100%);
            border: 1px solid #1e3a5f;
        }
        .card-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .card-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-xp {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            color: #fbbf24;
        }
        .card-difficulty {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 999px;
            background: rgba(34,201,138,0.12);
            border: 1px solid rgba(34,201,138,0.35);
            color: #22c98a;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }
        .card-section-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 8px;
        }
        .card-instructions {
            font-size: 0.9rem;
            line-height: 1.7;
            color: #cbd5e1;
            white-space: pre-line;
        }
        .card-instructions code {
            background: rgba(34,201,138,0.12);
            color: #22c98a;
            padding: 1px 5px;
            border-radius: 4px;
            font-family: 'Space Mono', monospace;
            font-size: 0.82em;
        }
        .card-divider {
            height: 1px;
            background: #1e2a45;
            margin: 18px 0;
        }
        .card-challenge-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #7c3aed;
            margin-bottom: 8px;
        }
        .card-challenge {
            font-size: 0.88rem;
            line-height: 1.65;
            color: #a78bfa;
            white-space: pre-line;
        }

        /* Back — Astro hint */
        .card-back {
            background: linear-gradient(145deg, #1a1040 0%, #160d38 100%);
            border: 1px solid #2d1f6e;
            transform: rotateY(180deg);
        }
        .astro-avatar {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed, #22c98a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 14px;
        }
        .hint-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 10px;
        }
        .hint-body {
            font-size: 0.88rem;
            line-height: 1.7;
            color: #c4b5fd;
            margin-bottom: 18px;
            white-space: pre-line;
        }
        .hint-code-block {
            background: #0e0a2a;
            border: 1px solid #2d1f6e;
            border-radius: 10px;
            padding: 14px 16px;
            font-family: 'Space Mono', monospace;
            font-size: 0.82rem;
            color: #22c98a;
            white-space: pre-wrap;
            line-height: 1.6;
        }

        /* Flip button */
        .flip-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: calc(100% - 40px);
            margin: 0 20px 20px;
            padding: 11px 0;
            border-radius: 10px;
            background: transparent;
            border: 1px solid #1e2a45;
            color: #64748b;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: border-color 0.15s, color 0.15s, background 0.15s;
        }
        .flip-btn:hover {
            border-color: #7c3aed;
            color: #a78bfa;
            background: rgba(124,58,237,0.08);
        }
        .flip-icon { display: inline-block; transition: transform 0.3s; }
        .flip-btn:hover .flip-icon { transform: rotate(180deg); }

        /* ── RIGHT PANEL ─────────────────────────────────────────────── */
        .right-panel {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #0b0f24;
        }

        /* File tab */
        .file-tab-bar {
            display: flex;
            align-items: center;
            background: #090d1f;
            border-bottom: 1px solid #1e2a45;
            padding: 0 16px;
            height: 40px;
            flex-shrink: 0;
        }
        .file-tab {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 0 16px;
            height: 40px;
            border-bottom: 2px solid #22c98a;
            font-family: 'Space Mono', monospace;
            font-size: 0.8rem;
            color: #e2e8f0;
        }

        /* Code area */
        .code-area-wrapper {
            display: flex;
            flex: 1;
            overflow: hidden;
            min-height: 0;
        }
        .line-numbers {
            padding: 16px 12px 16px 16px;
            font-family: 'Space Mono', monospace;
            font-size: 0.85rem;
            line-height: 1.6;
            color: #334155;
            text-align: right;
            user-select: none;
            background: #090d1f;
            min-width: 48px;
            overflow: hidden;
            border-right: 1px solid #1e2a45;
        }
        #python-code {
            flex: 1;
            padding: 16px 20px;
            border: none;
            outline: none;
            resize: none;
            background: #0b0f24;
            color: #22c98a;
            font-family: 'Space Mono', monospace;
            font-size: 0.875rem;
            line-height: 1.6;
            caret-color: #22c98a;
            overflow-y: auto;
        }
        #python-code::selection { background: rgba(34,201,138,0.2); }

        /* Action bar */
        .action-bar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding: 10px 20px;
            background: #090d1f;
            border-top: 1px solid #1e2a45;
            flex-shrink: 0;
        }
        .editor-status {
            font-size: 0.78rem;
            color: #475569;
            margin-right: auto;
            font-family: 'Space Mono', monospace;
        }
        .editor-status.ready   { color: #22c98a; }
        .editor-status.running { color: #fbbf24; }
        .editor-status.error   { color: #f87171; }

        .btn-run {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 22px;
            border-radius: 8px;
            background: #1e293b;
            border: 1px solid #334155;
            color: #e2e8f0;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
        }
        .btn-run:hover { background: #263348; border-color: #475569; }
        .btn-run:disabled { opacity: 0.45; cursor: not-allowed; }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 22px;
            border-radius: 8px;
            background: #22c98a;
            border: none;
            color: #0e1230;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            transition: opacity 0.15s;
        }
        .btn-submit:hover { opacity: 0.85; }
        .btn-submit:disabled { opacity: 0.45; cursor: not-allowed; }

        .btn-next {
            display: none;
            align-items: center;
            gap: 7px;
            padding: 9px 22px;
            border-radius: 8px;
            background: linear-gradient(100deg, #5be1ff, #73b6ff 55%, #9b6bff);
            border: none;
            color: #0e1230;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.15s, opacity 0.15s;
        }
        .btn-next.visible { display: inline-flex; }
        .btn-next:hover { transform: translateY(-1px); opacity: 0.92; }

        /* Toast stack (XP + success), Codedex-style */
        .toast-stack {
            position: fixed;
            top: 64px;
            right: 20px;
            z-index: 50;
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 320px;
            pointer-events: none;
        }
        .toast-card {
            pointer-events: auto;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 18px;
            border-radius: 14px;
            opacity: 0;
            transform: translateY(-10px) scale(0.97);
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        .toast-card.show {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        .toast-card.xp-toast {
            background: #eaf3ff;
            border: 1px solid #bcdcff;
        }
        .toast-card.success-toast {
            background: #f2fbe6;
            border: 1px solid #cdeaa0;
        }
        .toast-icon {
            font-size: 1.6rem;
            line-height: 1;
            flex-shrink: 0;
        }
        .toast-body { flex: 1; min-width: 0; }
        .toast-title {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: #0e1230;
            margin-bottom: 4px;
        }
        .toast-text {
            font-size: 0.85rem;
            line-height: 1.5;
            color: #475569;
        }
        .toast-close {
            flex-shrink: 0;
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 1rem;
            cursor: pointer;
            padding: 2px;
            line-height: 1;
        }
        .toast-close:hover { color: #475569; }

        /* Test toast (inline fail state) */
        .test-toast {
            display: none;
            margin: 0 20px 14px;
            padding: 10px 16px;
            border-radius: 8px;
            font-family: 'Space Mono', monospace;
            font-size: 0.82rem;
        }
        .test-toast.fail {
            display: block;
            background: rgba(248,113,113,0.12);
            border: 1px solid #f87171;
            color: #f87171;
        }

        /* Terminal */
        .terminal-panel {
            flex-shrink: 0;
            border-top: 1px solid #1e2a45;
            background: #060913;
        }
        .terminal-header {
            display: flex;
            align-items: center;
            padding: 8px 20px;
            border-bottom: 1px solid #1e2a45;
            gap: 8px;
        }
        .terminal-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #1e2a45;
        }
        .terminal-dot.active { background: #22c98a; }
        .terminal-label {
            font-family: 'Space Mono', monospace;
            font-size: 0.72rem;
            color: #475569;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .terminal-output {
            padding: 14px 20px;
            font-family: 'Space Mono', monospace;
            font-size: 0.84rem;
            line-height: 1.6;
            white-space: pre-wrap;
            min-height: 100px;
            max-height: 180px;
            overflow-y: auto;
            color: #94a3b8;
        }
        .terminal-output::-webkit-scrollbar { width: 4px; }
        .terminal-output::-webkit-scrollbar-thumb { background: #1e2a45; border-radius: 4px; }
        .terminal-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 100px;
            color: #334155;
        }
        .terminal-empty-text {
            font-family: 'Space Mono', monospace;
            font-size: 0.75rem;
        }
        .out-success { color: #22c98a; }
        .out-error   { color: #f87171; }

        @media (max-width: 768px) {
            .editor-layout { grid-template-columns: 1fr; grid-template-rows: auto 1fr; height: auto; }
            .left-panel { border-right: none; border-bottom: 1px solid #1e2a45; }
            .card-flipper { min-height: unset; }
            .card-face { position: relative; min-height: unset; }
            .right-panel { height: 70vh; }
            .toast-stack { left: 12px; right: 12px; width: auto; top: 60px; }
        }
    </style>
</head>
<body>

@php
    $title       = session('editor_title',        'Exercise');
    $difficulty  = session('editor_difficulty',   '');
    $xp          = session('editor_xp',           '');
    $expected    = session('editor_expected',      '');
    $starterCode = session('editor_starter_code', '');
    $instructions= session('editor_instructions', 'Write your Python code in the editor and click Run to test it.');
    $hintTitle   = session('editor_hint_title',   "Astro's Hint");
    $hintBody    = session('editor_hint_body',    '');
    $hintCode    = session('editor_hint_code',    '');
    $challenge   = session('editor_challenge',    '');
    $returnTo    = session('editor_return_to',    route('student.planet', ['slug' => $slug ?? 'programming']));
    $nextLesson  = session('editor_next_lesson',  null);

    // Lesson identity — flashed by the controller from the lesson form's
    // hidden "module" and "lesson" fields. Used by JS to mark the lesson
    // complete in localStorage when the student gets the answer right.
    // Defaults to null so older lesson files that haven't added the fields
    // yet fail gracefully without errors.
    $lessonModule = session('editor_module', null);   // e.g. "m1"
    $lessonId     = session('editor_lesson', null);   // e.g. "lesson01"
@endphp

{{-- Top nav --}}
<nav class="top-bar">
    <div class="top-bar-left">
        <a href="{{ route('student.planet', ['slug' => $slug ?? 'programming']) }}"
           class="top-bar-logo">🚀 TechLab</a>
        <span class="top-bar-breadcrumb">
            / Python / <span>{{ $title }}</span>
        </span>
    </div>
    <a href="{{ $returnTo }}" class="top-bar-back">← Back to lesson</a>
</nav>

{{-- Toast stack: XP + success, appear together on a correct submission --}}
<div class="toast-stack" id="toast-stack">
    <div class="toast-card xp-toast" id="xp-toast">
        <span class="toast-icon">✴️</span>
        <div class="toast-body">
            <div class="toast-title">+{{ $xp ?: 0 }} XP</div>
            <div class="toast-text">You earned XP for this exercise. Keep it up!</div>
        </div>
        <button class="toast-close" type="button" data-dismiss="xp-toast">✕</button>
    </div>
    <div class="toast-card success-toast" id="success-toast">
        <span class="toast-icon">🎉</span>
        <div class="toast-body">
            <div class="toast-title">You got it!</div>
            <div class="toast-text">Press "Next" to continue.</div>
        </div>
        <button class="toast-close" type="button" data-dismiss="success-toast">✕</button>
    </div>
</div>

<main class="editor-layout">

    {{-- LEFT — instruction card --}}
    <aside class="left-panel">
        <div class="card-scene">
            <div class="card-flipper" id="card-flipper">

                {{-- FRONT: Instructions --}}
                <div class="card-face card-front">
                    <div class="card-meta">
                        <div class="card-title">🐍 {{ $title }}</div>
                        @if($xp)
                            <div class="card-xp">{{ $xp }} XP</div>
                        @endif
                    </div>

                    @if($difficulty)
                        <div class="card-difficulty">{{ $difficulty }}</div>
                    @endif

                    <div class="card-section-label">Instructions</div>
                    <div class="card-instructions">{{ $instructions }}</div>

                    @if($challenge)
                        <div class="card-divider"></div>
                        <div class="card-challenge-label">🚀 Captain's Challenge</div>
                        <div class="card-challenge">{{ $challenge }}</div>
                    @endif
                </div>

                {{-- BACK: Astro's hint --}}
                <div class="card-face card-back">
                    <div class="astro-avatar">🤖</div>
                    <div class="hint-title">{{ $hintTitle }}</div>
                    <div class="hint-body">{{ $hintBody }}</div>
                    @if($hintCode)
                        <div class="card-section-label" style="margin-top:4px;">Example</div>
                        <div class="hint-code-block">{{ $hintCode }}</div>
                    @endif
                </div>

            </div>
        </div>

        <button class="flip-btn" id="flip-btn" type="button">
            <span class="flip-icon">🔄</span>
            <span id="flip-label">Flip to get help from Astro</span>
        </button>
    </aside>

    {{-- RIGHT — code editor --}}
    <section class="right-panel">

        <div class="file-tab-bar">
            <div class="file-tab">🐍 script.py</div>
        </div>

        <div class="code-area-wrapper">
            <div class="line-numbers" id="line-numbers">1</div>
            <textarea
                id="python-code"
                spellcheck="false"
                autocorrect="off"
                autocapitalize="off"
                autocomplete="off"
            >{{ $starterCode }}</textarea>
        </div>

        <div class="action-bar">
            <span id="editor-status" class="editor-status">Loading Python…</span>
            <button id="run-btn" class="btn-run" disabled>▶ Run</button>
            @if($expected)
                <button id="submit-btn" class="btn-submit" disabled>Submit answer</button>
                <a id="next-btn" class="btn-next" href="{{ $nextLesson ?: $returnTo }}">Next →</a>
            @endif
        </div>

        <div id="test-toast" class="test-toast"></div>

        <div class="terminal-panel">
            <div class="terminal-header">
                <div class="terminal-dot" id="terminal-dot"></div>
                <span class="terminal-label">Terminal</span>
            </div>
            <div id="terminal-output" class="terminal-output">
                <div class="terminal-empty">
                    <span class="terminal-empty-text">Click Run to view your results</span>
                </div>
            </div>
        </div>

    </section>
</main>

<script src="https://cdn.jsdelivr.net/pyodide/v0.26.4/full/pyodide.js"></script>
<script>
(async function () {
    const codeArea  = document.getElementById('python-code');
    const lineNums  = document.getElementById('line-numbers');
    const runBtn    = document.getElementById('run-btn');
    const submitBtn = document.getElementById('submit-btn');
    const nextBtn   = document.getElementById('next-btn');
    const statusEl  = document.getElementById('editor-status');
    const termOut   = document.getElementById('terminal-output');
    const termDot   = document.getElementById('terminal-dot');
    const toast     = document.getElementById('test-toast');
    const flipper   = document.getElementById('card-flipper');
    const flipBtn   = document.getElementById('flip-btn');
    const flipLabel = document.getElementById('flip-label');

    const xpToast      = document.getElementById('xp-toast');
    const successToast = document.getElementById('success-toast');

    const expectedOutput = @json($expected);

    // ── Which lesson this editor was launched from.
    //    Blade stamps these from session so the editor never needs to
    //    parse URLs or know anything about the sidebar internals.
    const LESSON_MODULE = @json($lessonModule);  // e.g. "m1"  or null
    const LESSON_ID     = @json($lessonId);      // e.g. "lesson01" or null

    /* Line numbers */
    function updateLineNumbers() {
        const count = codeArea.value.split('\n').length;
        lineNums.innerHTML = Array.from({ length: count }, (_, i) => i + 1).join('<br>');
    }
    codeArea.addEventListener('input', updateLineNumbers);
    codeArea.addEventListener('keydown', function (e) {
        if (e.key === 'Tab') {
            e.preventDefault();
            const s = this.selectionStart;
            this.value = this.value.substring(0, s) + '    ' + this.value.substring(this.selectionEnd);
            this.selectionStart = this.selectionEnd = s + 4;
            updateLineNumbers();
        }
    });
    updateLineNumbers();

    /* Sync line-number scroll with textarea scroll */
    codeArea.addEventListener('scroll', () => {
        lineNums.scrollTop = codeArea.scrollTop;
    });

    /* Card flip */
    let isFlipped = false;
    flipBtn.addEventListener('click', () => {
        isFlipped = !isFlipped;
        flipper.classList.toggle('flipped', isFlipped);
        flipLabel.textContent = isFlipped ? 'Flip back to instructions' : 'Flip to get help from Astro';
    });

    /* Toasts */
    function showToast(el) {
        el.classList.add('show');
    }
    function hideToast(el) {
        el.classList.remove('show');
    }
    document.querySelectorAll('[data-dismiss]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-dismiss');
            hideToast(document.getElementById(id));
        });
    });

    // ── Called once when the student's answer is confirmed correct.
    //    Order matters: mark the lesson complete FIRST (updates
    //    localStorage + sidebar state), THEN show the success UI.
    function showSuccessState() {

        // ── THE KEY LINE ─────────────────────────────────────────────
        // Tell the sidebar this lesson is done. This single call:
        //   ✓ Turns the lesson dot green
        //   ✓ Removes the 🔒 from the next lesson
        //   ✓ Updates the module progress percentage
        //   ✓ Persists everything to localStorage (survives refresh)
        //
        // It does NOT auto-navigate here because the student is in the
        // standalone editor page, not inside programming.blade.php.
        // Navigation is handled by the "Next →" button below.
        if (LESSON_MODULE && LESSON_ID &&
            window.TechLab && typeof window.TechLab.markLessonComplete === 'function') {
            // Same-window flow: sidebar script is available in this window
            window.TechLab.markLessonComplete(LESSON_MODULE, LESSON_ID);
        } else if (LESSON_MODULE && LESSON_ID &&
                   window.opener && window.opener.TechLab &&
                   typeof window.opener.TechLab.markLessonComplete === 'function') {
            // If the editor was opened in a new tab/window, reach back
            // to the parent tab's sidebar.
            window.opener.TechLab.markLessonComplete(LESSON_MODULE, LESSON_ID);
        } else if (LESSON_MODULE && LESSON_ID) {
            // Fallback: update localStorage directly (sidebar will sync on next load)
            try {
                const STORAGE_KEY = 'techlab_progress_programming';
                const key = LESSON_MODULE + '/' + LESSON_ID;
                const raw = localStorage.getItem(STORAGE_KEY);
                const completed = (raw ? JSON.parse(raw) : []);
                if (!Array.isArray(completed)) { /* skip */ }
                else if (completed.indexOf(key) === -1) {
                    completed.push(key);
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(completed));
                }
            } catch (e) {
                // localStorage unavailable — fail silently.
            }
        }
        // ── END KEY LINE ─────────────────────────────────────────────

        showToast(xpToast);
        showToast(successToast);
        if (nextBtn) {
            nextBtn.classList.add('visible');
        }
        if (submitBtn) {
            submitBtn.disabled = true;
        }
    }

    /* Pyodide */
    let pyodide = null;
    let busy = false;

    function setStatus(text, type = '') {
        statusEl.textContent = text;
        statusEl.className = 'editor-status' + (type ? ' ' + type : '');
    }
    function setButtons(on) {
        runBtn.disabled = !on;
        if (submitBtn && !submitBtn.disabled) submitBtn.disabled = !on;
        else if (submitBtn && on === false) submitBtn.disabled = true;
    }

    setStatus('Loading Python…');
    try {
        pyodide = await window.loadPyodide();
        setStatus('Python ready', 'ready');
        runBtn.disabled = false;
        if (submitBtn) submitBtn.disabled = false;
    } catch (err) {
        setStatus('Failed to load Python', 'error');
        termOut.innerHTML = '<span class="out-error">' + esc(err.message || 'Pyodide failed') + '</span>';
    }

    async function runCode() {
        if (busy || !pyodide) return null;
        const code = codeArea.value;
        if (!code.trim()) {
            termOut.innerHTML = '<span class="out-error">Please write some Python code first.</span>';
            return null;
        }
        busy = true;
        runBtn.disabled = true;
        if (submitBtn) submitBtn.disabled = true;
        setStatus('Running…', 'running');
        toast.style.display = 'none';
        termDot.classList.remove('active');

        let captured = '';
        try {
            pyodide.setStdout({ batched: m => { captured += m; } });
            pyodide.setStderr({ batched: m => { captured += m; } });
            await pyodide.runPythonAsync(code);
            const result = captured || '(no output)';
            termOut.innerHTML = '<span class="out-success">' + esc(result) + '</span>';
            termDot.classList.add('active');
            setStatus('Done', 'ready');
            return result;
        } catch (err) {
            termOut.innerHTML = '<span class="out-error">' + esc(err.message || err.toString()) + '</span>';
            setStatus('Error', 'error');
            return null;
        } finally {
            busy = false;
            runBtn.disabled = false;
            if (submitBtn) submitBtn.disabled = false;
        }
    }

    function esc(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    runBtn.addEventListener('click', runCode);

    if (submitBtn) {
        submitBtn.addEventListener('click', async () => {
            const result = await runCode();
            if (result === null) return;
            const pass = result.trim() === (expectedOutput || '').trim();

            if (pass) {
                toast.style.display = 'none';
                showSuccessState();   // ← this now also marks the lesson complete
            } else {
                toast.style.display = 'block';
                toast.className = 'test-toast fail';
                toast.textContent = '❌ Not quite — check the output above and try again.';
            }
        });
    }
})();
</script>

</body>
</html>