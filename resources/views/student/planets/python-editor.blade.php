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
    <meta name="theme-color" content="#06061a">
    <script src="{{ asset('js/theme.js') }}?v={{ filemtime(public_path('js/theme.js')) }}"></script>
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


        /* ── Back link (top of left panel) ──────────────────────────── */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 16px 20px 0;
            padding: 6px 10px 6px 6px;
            align-self: flex-start;
            border-radius: 8px;
            color: #8aa0c0;
            text-decoration: none;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            transition: color 0.15s, background 0.15s;
        }
        .back-link:hover { color: #73b6ff; background: rgba(115,182,255,0.08); }
        .back-link svg { width: 16px; height: 16px; }

        /* ── Split layout ────────────────────────────────────────────── */
        .editor-layout {
            display: grid;
            grid-template-columns: 360px 1fr;
            flex: 1;
            overflow: hidden;
            height: 100vh;
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
            background: #101833;
            border: 1px solid rgba(255,255,255,0.08);
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
            background: rgba(115,182,255,0.12);
            border: 1px solid rgba(115,182,255,0.35);
            color: #73b6ff;
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
            background: rgba(115,182,255,0.12);
            color: #73b6ff;
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

        /* Back — Astro hint */
        .card-back {
            background: #101833;
            border: 1px solid rgba(115,182,255,0.22);
            transform: rotateY(180deg);
        }
        .astro-avatar {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: rgba(115,182,255,0.14);
            border: 1px solid rgba(115,182,255,0.3);
            color: #73b6ff;
            display: flex;
            align-items: center;
            justify-content: center;
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
            color: #b9c6dd;
            margin-bottom: 18px;
            white-space: pre-line;
        }
        .hint-code-block {
            background: #0a1024;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 14px 16px;
            font-family: 'Space Mono', monospace;
            font-size: 0.82rem;
            color: #73b6ff;
            white-space: pre-wrap;
            line-height: 1.6;
        }

        /* Hint shop: Astro's hint costs XP */
        .hint-price {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            margin: 0 0 14px; padding: 10px 14px; border-radius: 10px;
            background: rgba(255,212,59,.08); border: 1px solid rgba(255,212,59,.25);
            font-size: .85rem; color: #ffe58a;
        }
        .hint-price b { font-family: 'Space Grotesk', sans-serif; font-weight: 700; }
        .hint-balance-row { background: rgba(115,182,255,.08); border-color: rgba(115,182,255,.25); color: #e2e8f0; }
        .hint-buy {
            width: 100%; justify-content: center;
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 16px; border-radius: 10px; border: none; cursor: pointer;
            background: #73b6ff; color: #0e1230;
            font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: .9rem;
            transition: background .15s, transform .12s cubic-bezier(.34,1.56,.64,1), opacity .15s;
        }
        .hint-buy:hover { background: #8cc4ff; }
        .hint-buy:active { transform: scale(.97); }
        .hint-buy:disabled { opacity: .45; cursor: not-allowed; }
        .hint-buy:focus-visible { outline: 2px solid #73b6ff; outline-offset: 2px; }
        .hint-msg { min-height: 1.2em; margin-top: 10px; font-size: .82rem; line-height: 1.5; color: #b9c6dd; }
        .hint-msg.err { color: #ff8aa0; }
        .hint-msg.ok  { color: #7cffb2; }
        [hidden] { display: none !important; }

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
            border-color: rgba(115,182,255,0.5);
            color: #73b6ff;
            background: rgba(115,182,255,0.08);
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
        .file-tab .python-logo { width: 16px; height: 16px; }
        .file-tab {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 0 16px;
            height: 40px;
            border-bottom: 2px solid #73b6ff;
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
        /* Syntax colouring: a highlighted <pre> sits behind a transparent textarea.
           Both layers share identical metrics so the colours line up under the caret. */
        .code-stack { position: relative; flex: 1; min-width: 0; background: #0b0f24; }
        #code-hl, #python-code {
            position: absolute; inset: 0;
            margin: 0; padding: 16px 20px;
            border: none; outline: none; resize: none;
            font-family: 'Space Mono', monospace;
            font-size: 0.875rem;
            line-height: 1.6;
            letter-spacing: normal;
            tab-size: 4;
            white-space: pre;
            word-wrap: normal;
            overflow: auto;
        }
        #code-hl { pointer-events: none; overflow: hidden; color: #e6e9ff; background: transparent; }
        #python-code {
            background: transparent;
            color: transparent;
            caret-color: #73b6ff;
        }
        #python-code::selection { background: rgba(115,182,255,0.28); }

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
        .editor-status.ready   { color: #73b6ff; }
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
            background: #73b6ff;
            border: none;
            color: #0e1230;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            transition: opacity 0.15s;
        }
        .btn-submit:hover { background: #8cc4ff; }
        .btn-submit:active, .btn-run:active, .btn-next:active { transform: scale(0.97); }
        .btn-submit, .btn-run, .btn-next { transition: background 0.15s, transform 0.12s cubic-bezier(.34,1.56,.64,1), opacity 0.15s; }
        .btn-submit:focus-visible, .btn-run:focus-visible, .btn-next:focus-visible, .flip-btn:focus-visible { outline: 2px solid #73b6ff; outline-offset: 2px; }
        .btn-submit:disabled { opacity: 0.45; cursor: not-allowed; }

        .btn-next {
            display: none;
            align-items: center;
            gap: 7px;
            padding: 9px 22px;
            border-radius: 8px;
            background: #73b6ff;
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
            top: 52px;   /* below the tab bar so the Theme button stays clickable */
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
            background: #f4f9ff;
            border: 1px solid #cfe4ff;
        }
        .toast-icon {
            width: 28px; height: 28px;
            display: grid; place-items: center;
            color: #2f7de1;
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
        .terminal-dot.active { background: #73b6ff; }
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
        .out-success { color: #73b6ff; }
        .out-error   { color: #f87171; }

        /* ── Theme button (matches the sidebar's Theme item) ─────────── */
        .theme-btn {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            height: 32px;
            padding: 0 12px;
            border: none;
            border-radius: 11px;
            background: transparent;
            color: #8aa0c0;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 13.5px;
            font-weight: 500;
            letter-spacing: -0.006em;
            cursor: pointer;
            transition: background 0.15s, color 0.15s, transform 0.12s cubic-bezier(.34,1.56,.64,1);
        }
        .theme-btn svg { width: 20px; height: 20px; flex: none; transition: transform 0.4s cubic-bezier(.34,1.56,.64,1); }
        .theme-btn:hover { background: rgba(123,142,220,0.09); color: #e2e8f0; }
        .theme-btn:hover svg { transform: rotate(180deg); }
        .theme-btn:active { transform: scale(0.97); }
        .theme-btn:focus-visible { outline: 2px solid #73b6ff; outline-offset: 2px; }

        /* ── Light theme (same palette as the sidebar light theme) ───── */
        html.theme-anim, html.theme-anim *, html.theme-anim *::before, html.theme-anim *::after {
            transition: background-color .3s ease, border-color .3s ease, color .3s ease !important;
        }
        html[data-theme="light"] body            { background: #ffffff; color: #0d0d0d; }
        html[data-theme="light"] .left-panel,
        html[data-theme="light"] .file-tab-bar,
        html[data-theme="light"] .action-bar,
        html[data-theme="light"] .line-numbers   { background: #f9f9f9; }
        html[data-theme="light"] .left-panel,
        html[data-theme="light"] .file-tab-bar,
        html[data-theme="light"] .action-bar,
        html[data-theme="light"] .line-numbers,
        html[data-theme="light"] .terminal-panel,
        html[data-theme="light"] .terminal-header,
        html[data-theme="light"] .card-divider   { border-color: rgba(0,0,0,.09); }
        html[data-theme="light"] .right-panel,
        html[data-theme="light"] .code-stack     { background: #ffffff; }
        html[data-theme="light"] .card-front,
        html[data-theme="light"] .card-back      { background: #ffffff; border: 1px solid rgba(0,0,0,.09); box-shadow: 0 1px 2px rgba(0,0,0,.05); }
        html[data-theme="light"] .card-back      { border-color: rgba(47,125,225,.3); }
        html[data-theme="light"] .card-title     { color: #0d0d0d; }
        /* Same neutral surfaces + black primary button as the chat's light theme */
        html[data-theme="light"] .hint-price     { background: rgba(0,0,0,.045); border-color: rgba(0,0,0,.09); color: #676767; }
        html[data-theme="light"] .hint-price b   { color: #0d0d0d; }
        html[data-theme="light"] .hint-balance-row { background: rgba(0,0,0,.045); border-color: rgba(0,0,0,.09); color: #676767; }
        html[data-theme="light"] .hint-buy       { background: #0d0d0d; color: #fff; }
        html[data-theme="light"] .hint-buy:hover { background: #2a2a2a; }
        html[data-theme="light"] .hint-msg       { color: #676767; }
        html[data-theme="light"] .hint-msg.err   { color: #d6325a; }
        html[data-theme="light"] .hint-msg.ok    { color: #0f9d63; }
        html[data-theme="light"] .hint-title     { color: #0d0d0d; }
        html[data-theme="light"] .card-instructions,
        html[data-theme="light"] .hint-body      { color: #3d3d3d; }
        html[data-theme="light"] .card-section-label,
        html[data-theme="light"] .editor-status,
        html[data-theme="light"] .terminal-label { color: #676767; }
        html[data-theme="light"] .card-xp        { color: #b7791f; }
        html[data-theme="light"] .card-difficulty { background: rgba(47,125,225,.1); border-color: rgba(47,125,225,.3); color: #2f7de1; }
        html[data-theme="light"] .card-instructions code { background: rgba(47,125,225,.1); color: #2f7de1; }
        html[data-theme="light"] .astro-avatar   { background: rgba(47,125,225,.1); border-color: rgba(47,125,225,.3); color: #2f7de1; }
        html[data-theme="light"] .hint-code-block { background: #f3f3f3; border-color: rgba(0,0,0,.09); color: #1f4fa8; }
        html[data-theme="light"] .back-link      { color: #676767; }
        html[data-theme="light"] .back-link:hover,
        html[data-theme="light"] .flip-btn:hover { color: #2f7de1; background: rgba(47,125,225,.08); }
        html[data-theme="light"] .flip-btn       { border-color: rgba(0,0,0,.12); color: #676767; }
        html[data-theme="light"] .flip-btn:hover { border-color: rgba(47,125,225,.5); }
        html[data-theme="light"] .file-tab       { color: #0d0d0d; border-bottom-color: #2f7de1; }
        html[data-theme="light"] .theme-btn      { color: #676767; }
        html[data-theme="light"] .theme-btn:hover { background: rgba(0,0,0,.05); color: #0d0d0d; }
        html[data-theme="light"] .line-numbers   { color: #a0a0a0; }
        html[data-theme="light"] #code-hl        { color: #0d0d0d; }
        html[data-theme="light"] #python-code    { caret-color: #2f7de1; }
        html[data-theme="light"] #python-code::selection { background: rgba(47,125,225,.22); }
        html[data-theme="light"] .btn-run        { background: #ffffff; border-color: rgba(0,0,0,.15); color: #0d0d0d; }
        html[data-theme="light"] .btn-run:hover  { background: #f3f3f3; border-color: rgba(0,0,0,.25); }
        html[data-theme="light"] .btn-submit,
        html[data-theme="light"] .btn-next       { background: #2f7de1; color: #ffffff; }
        html[data-theme="light"] .btn-submit:hover { background: #276cc4; }
        html[data-theme="light"] .editor-status.ready,
        html[data-theme="light"] .out-success    { color: #2f7de1; }
        html[data-theme="light"] .terminal-panel { background: #f9f9f9; }
        html[data-theme="light"] .terminal-output { color: #3d3d3d; }
        html[data-theme="light"] .terminal-dot   { background: rgba(0,0,0,.15); }
        html[data-theme="light"] .terminal-dot.active { background: #2f7de1; }
        html[data-theme="light"] .terminal-empty { color: #a0a0a0; }
        html[data-theme="light"] .card-scene::-webkit-scrollbar-thumb,
        html[data-theme="light"] .terminal-output::-webkit-scrollbar-thumb { background: rgba(0,0,0,.15); }
        /* code colours tuned for a white background */
        html[data-theme="light"] .tok-comment { color: #7a8194; }
        html[data-theme="light"] .tok-kw, html[data-theme="light"] .tok-op { color: #c2255c; }
        html[data-theme="light"] .tok-const, html[data-theme="light"] .tok-num, html[data-theme="light"] .tok-interp { color: #b25e00; }
        html[data-theme="light"] .tok-builtin { color: #0b7285; }
        html[data-theme="light"] .tok-fn       { color: #2f5fd0; }
        html[data-theme="light"] .tok-str      { color: #2b8a3e; }
        html[data-theme="light"] .tok-var      { color: #0d0d0d; }
        html[data-theme="light"] .tok-punct    { color: #676767; }

        @media (max-width: 768px) {
            .editor-layout { grid-template-columns: 1fr; grid-template-rows: auto 1fr; height: auto; }
            .left-panel { border-right: none; border-bottom: 1px solid #1e2a45; }
            .card-flipper { min-height: unset; }
            .card-face { position: relative; min-height: unset; }
            .right-panel { height: 70vh; }
            .toast-stack { left: 12px; right: 12px; width: auto; top: 52px; }
        }
    </style>
</head>
<body>

@php
    $title       = session('editor_title',        'Exercise');
    $difficulty  = session('editor_difficulty',   '');
    $xp          = session('editor_xp',           '');
    $starterCode = session('editor_starter_code', '');
    $instructions= session('editor_instructions', 'Write your Python code in the editor and click Run to test it.');
    $hintTitle   = session('editor_hint_title',   "Astro's Hint");
    $hintBody    = session('editor_hint_body',    '');
    $hintCode    = session('editor_hint_code',    '');
    $fileName    = session('editor_filename', '') ?: (\Illuminate\Support\Str::snake($title) ?: 'main');
    $fileName    = preg_replace('/[^a-z0-9_]/', '', strtolower(pathinfo($fileName, PATHINFO_FILENAME))) ?: 'main';
    $fileName   .= '.py';
    $returnTo    = session('editor_return_to',    route('student.planet.play', ['slug' => $slug ?? 'programming', 'course' => array_key_first(config('course-catalog.'.($slug ?? 'programming').'.courses', [])) ?? 'python']));

    // Server-verified completion endpoint for this lesson's challenge, flashed
    // by PlanetController::launchEditor. Null when the exercise has no
    // output-checkable challenge — the editor then only offers Run.
    // The answer key itself is never sent to the browser.
    $completeUrl = session('editor_complete_url', null);

    // Programming lessons: Astro's hint is bought with XP (text served by the server after purchase).
    $hintStatusUrl = session('editor_hint_status_url', null);
    $hintBuyUrl    = session('editor_hint_buy_url', null);
    $hintCost      = \App\Services\StudentDashboardService::XP_HINT_COST;
@endphp

{{-- Toast stack: XP + success, appear together on a correct submission --}}
<div class="toast-stack" id="toast-stack">
    <div class="toast-card xp-toast" id="xp-toast">
        <span class="toast-icon"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l2.4 5.6L20 11l-5.6 2.4L12 19l-2.4-5.6L4 11l5.6-2.4z"/></svg></span>
        <div class="toast-body">
            <div class="toast-title">+{{ $xp ?: 0 }} XP</div>
            <div class="toast-text">You earned XP for this exercise. Keep it up!</div>
        </div>
        <button class="toast-close" type="button" data-dismiss="xp-toast">✕</button>
    </div>
    <div class="toast-card success-toast" id="success-toast">
        <span class="toast-icon"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.7 2.7L16 9.5"/></svg></span>
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
        <a href="{{ $returnTo }}" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
            Back to lesson
        </a>
        <div class="card-scene">
            <div class="card-flipper" id="card-flipper">

                {{-- FRONT: Instructions --}}
                <div class="card-face card-front">
                    <div class="card-meta">
                        <div class="card-title">{{ $title }}</div>
                        @if($xp)
                            <div class="card-xp">{{ $xp }} XP</div>
                        @endif
                    </div>

                    @if($difficulty)
                        <div class="card-difficulty">{{ $difficulty }}</div>
                    @endif

                    <div class="card-section-label">Instructions</div>
                    <div class="card-instructions">{{ $instructions }}</div>

                </div>

                {{-- BACK: Astro's hint --}}
                <div class="card-face card-back">
                    <div class="astro-avatar"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l2.4 5.6L20 11l-5.6 2.4L12 19l-2.4-5.6L4 11l5.6-2.4z"/></svg></div>
                    @if($hintBuyUrl)
                        {{-- Earned the hard way: the hint text is not in this page until it is bought. --}}
                        <div id="hint-locked">
                            <div class="hint-title">{{ $hintTitle }}</div>
                            <div class="hint-body">Stuck? Astro can help, but XP is earned the hard way. A hint costs XP, and it comes out of your total.</div>
                            <div class="hint-price"><span>Price</span><b>{{ $hintCost }} XP</b></div>
                            <div class="hint-price hint-balance-row"><span>Your XP</span><b id="hint-balance">…</b></div>
                            <button class="hint-buy" id="hint-buy" type="button" disabled>Unlock hint · −{{ $hintCost }} XP</button>
                            <div class="hint-msg" id="hint-msg" role="status" aria-live="polite"></div>
                        </div>
                        <div id="hint-open" hidden>
                            <div class="hint-title" id="hint-open-title"></div>
                            <div class="hint-body" id="hint-open-body"></div>
                            <div id="hint-open-code-wrap" hidden>
                                <div class="card-section-label" style="margin-top:4px;">Example</div>
                                <div class="hint-code-block" id="hint-open-code"></div>
                            </div>
                        </div>
                    @else
                        <div class="hint-title">{{ $hintTitle }}</div>
                        <div class="hint-body">{{ $hintBody }}</div>
                        @if($hintCode)
                            <div class="card-section-label" style="margin-top:4px;">Example</div>
                            <div class="hint-code-block">{{ $hintCode }}</div>
                        @endif
                    @endif
                </div>

            </div>
        </div>

        <button class="flip-btn" id="flip-btn" type="button">
            <span class="flip-icon"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 0 1-15.5 6.2M3 12A9 9 0 0 1 18.5 5.8"/><path d="M18.5 2v4h-4M5.5 22v-4h4"/></svg></span>
            <span id="flip-label">Flip to get help from Astro{{ $hintBuyUrl ? ' · '.$hintCost.' XP' : '' }}</span>
        </button>
    </aside>

    {{-- RIGHT — code editor --}}
    <section class="right-panel">

        <div class="file-tab-bar">
            <div class="file-tab">@include('components.python-logo') <span>{{ $fileName }}</span></div>
            <button type="button" class="theme-btn" onclick="window.techlabTheme.toggle()" aria-label="Toggle theme">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="8.5"/>
                    <path d="M12 3.5a8.5 8.5 0 0 1 0 17z" fill="currentColor" fill-opacity="0.85"/>
                </svg>
                <span>Theme</span>
            </button>
        </div>

        <div class="code-area-wrapper">
            <div class="line-numbers" id="line-numbers">1</div>
            <div class="code-stack">
                <pre id="code-hl" aria-hidden="true"></pre>
                <textarea
                    id="python-code"
                    wrap="off"
                    spellcheck="false"
                    autocorrect="off"
                    autocapitalize="off"
                    autocomplete="off"
                >{{ $starterCode }}</textarea>
            </div>
        </div>

        <div class="action-bar">
            <span id="editor-status" class="editor-status">Loading Python…</span>
            <button id="run-btn" class="btn-run" disabled><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M7 4.5v15l13-7.5z"/></svg> Run</button>
            @if($completeUrl)
                <button id="submit-btn" class="btn-submit" disabled>Submit answer</button>
                {{-- href is set from the server's response once the answer is verified --}}
                <a id="next-btn" class="btn-next" href="{{ $returnTo }}">Next →</a>
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

<script src="{{ asset('js/code-highlight.js') }}?v={{ filemtime(public_path('js/code-highlight.js')) }}"></script>
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

    // Server endpoint that verifies the output and records the completion.
    const COMPLETE_URL = @json($completeUrl);
    const CSRF_TOKEN   = @json(csrf_token());

    /* Line numbers */
    function updateLineNumbers() {
        const count = codeArea.value.split('\n').length;
        lineNums.innerHTML = Array.from({ length: count }, (_, i) => i + 1).join('<br>');
    }
    /* Syntax colouring layer (display only — the textarea stays the source of truth) */
    const hlLayer = document.getElementById('code-hl');
    CodeHighlight.injectStyles();
    function updateHighlight() {
        // Trailing newline keeps the last (empty) line the same height in both layers.
        hlLayer.innerHTML = CodeHighlight.highlight(codeArea.value, 'python') + '\n';
        hlLayer.scrollTop = codeArea.scrollTop;
        hlLayer.scrollLeft = codeArea.scrollLeft;
    }
    codeArea.addEventListener('input', updateHighlight);
    codeArea.addEventListener('input', updateLineNumbers);
    codeArea.addEventListener('keydown', function (e) {
        if (e.key === 'Tab') {
            e.preventDefault();
            const s = this.selectionStart;
            this.value = this.value.substring(0, s) + '    ' + this.value.substring(this.selectionEnd);
            this.selectionStart = this.selectionEnd = s + 4;
            updateLineNumbers();
            updateHighlight();
        }
    });
    updateLineNumbers();
    updateHighlight();

    /* Sync line-number scroll with textarea scroll */
    codeArea.addEventListener('scroll', () => {
        lineNums.scrollTop = codeArea.scrollTop;
        hlLayer.scrollTop = codeArea.scrollTop;
        hlLayer.scrollLeft = codeArea.scrollLeft;
    });

    /* Card flip */
    let isFlipped = false;
    let hintBought = false;
    const HINT_STATUS_URL = @json($hintStatusUrl);
    const HINT_BUY_URL    = @json($hintBuyUrl);
    const HINT_COST       = @json($hintCost);
    const flipLabelFront  = () => 'Flip to get help from Astro' + (HINT_BUY_URL && !hintBought ? ' · ' + HINT_COST + ' XP' : '');
    flipBtn.addEventListener('click', () => {
        isFlipped = !isFlipped;
        flipper.classList.toggle('flipped', isFlipped);
        flipLabel.textContent = isFlipped ? 'Flip back to instructions' : flipLabelFront();
    });

    /* Astro's hint costs XP: the text only comes from the server once it is bought. */
    if (HINT_BUY_URL) {
        const hBuy = document.getElementById('hint-buy'), hMsg = document.getElementById('hint-msg'), hBal = document.getElementById('hint-balance');
        const hLocked = document.getElementById('hint-locked'), hOpen = document.getElementById('hint-open');
        const say = (t, cls) => { hMsg.textContent = t || ''; hMsg.className = 'hint-msg' + (cls ? ' ' + cls : ''); };
        const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' };

        function render(st) {
            if (st.bought && st.hint) {
                hintBought = true;
                document.getElementById('hint-open-title').textContent = st.hint.title;
                document.getElementById('hint-open-body').textContent  = st.hint.body;
                document.getElementById('hint-open-code').textContent  = st.hint.code;
                document.getElementById('hint-open-code-wrap').hidden  = !st.hint.code;
                hLocked.hidden = true; hOpen.hidden = false;
                if (!isFlipped) flipLabel.textContent = flipLabelFront();
                return;
            }
            hBal.textContent = st.balance + ' XP';
            if (st.hasHint === false) { hBuy.disabled = true; say('No hint for this exercise.', ''); return; }
            hBuy.disabled = !st.canAfford;
            say(st.canAfford ? '' : 'Not enough XP yet. You have ' + st.balance + ' and need ' + st.cost + '. Finish lessons and answer quiz questions to earn more.', st.canAfford ? '' : 'err');
        }

        fetch(HINT_STATUS_URL, { headers })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(render)
            .catch(() => { hBal.textContent = '—'; say('Could not load your XP. Tap Unlock to try again.', 'err'); hBuy.disabled = false; });

        hBuy.addEventListener('click', async () => {
            hBuy.disabled = true;
            say('Unlocking…');
            try {
                const r = await fetch(HINT_BUY_URL, { method: 'POST', headers, body: '{}' });
                const st = await r.json().catch(() => ({}));
                if (r.status === 402) { render(st); return; }
                if (!r.ok || !st.ok) throw new Error('bad');
                render(st);
                if (st.spent) { const t = document.getElementById('flip-label'); t.textContent = 'Flip back to instructions'; }
            } catch (e) { say('Could not unlock the hint. Please try again.', 'err'); hBuy.disabled = false; }
        });
    }

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

    // ── Called once when the SERVER has verified the answer and recorded the
    //    completion. Progress lives in the database, so there is nothing to
    //    persist client-side; `nextUrl` (from the server) unlocks the Next link.
    function showSuccessState(nextUrl) {
        showToast(xpToast);
        showToast(successToast);
        if (nextBtn) {
            if (nextUrl) nextBtn.setAttribute('href', nextUrl);
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

    function showFail(message) {
        toast.style.display = 'block';
        toast.className = 'test-toast fail';
        toast.textContent = message;
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', async () => {
            const result = await runCode();
            if (result === null) return;

            // The server holds the answer key: it compares the output, records
            // the completion, and tells us where "Next" goes.
            submitBtn.disabled = true;
            try {
                const res = await fetch(COMPLETE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify({ output: result }),
                });

                if (res.ok) {
                    const data = await res.json();
                    toast.style.display = 'none';
                    showSuccessState(data.next);
                    return;
                }

                submitBtn.disabled = false;
                showFail(res.status === 422
                    ? '❌ Not quite — check the output above and try again.'
                    : 'Could not verify your answer (' + res.status + '). Please try again.');
            } catch (e) {
                submitBtn.disabled = false;
                showFail('Could not reach the server. Check your connection and try again.');
            }
        });
    }
})();
</script>

</body>
</html>