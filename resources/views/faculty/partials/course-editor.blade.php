{{--
    Course editing inside the course player, for the course's assigned faculty member only
    (CourseAssignment::canEdit). Included at the end of student/planets/course-player.blade.php.

    · Every lesson stage gets a pencil: the card turns into a small rich-text editor (bold, lists, code,
      links, images). Interactive blocks (the code exercise, lab, checkpoints, quiz, built-in diagrams)
      show as locked chips: they can be moved, and diagrams replaced with an uploaded image.
    · The hero's title and objective, the stage list (add / rename / reorder / remove) and "reset lesson".
    · The quiz stage's questions (LessonQuizEditorController).
    · Stage 5's lab, in a full-screen lab studio that looks like what students get: the code editor (the answer
      code is run, and must print the expected output, before the lab saves) or the simulator (a draft is played
      in NetSim / Relay Console / Citadel Sim; a change to what the lab checks saves only once it was passed).
    · The sidebar outline: rename, reorder, hide and add modules and lessons (FacultyCourseController).

    Everything is saved on top of the course's files, so any change can be reset to the default.
--}}
@php
    $ceCrewId = \App\Models\Crew::where('planet', \App\Services\CourseProgressService::planetFor($slug))
        ->where('course_slug', \App\Services\CourseProgressService::catalogFor($slug))->value('id');
@endphp

<style>
    .ce-bar {
        display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
        margin: 0 0 16px; padding: 10px 14px;
        border-radius: 14px; background: #fff8e6; border: 1px solid #f4d48a;
        font-size: 14px; color: #6b4a00;
    }
    .ce-bar strong { font-family: 'Space Grotesk', sans-serif; margin-right: 4px; }
    .ce-bar .ce-status { margin-left: auto; font-size: 13px; }
    .ce-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 12px; border-radius: 10px; cursor: pointer;
        font: 600 13px 'Inter', system-ui, sans-serif;
        background: #fff; color: #1e3a8a; border: 1px solid #c9d8f5;
        transition: background .15s, border-color .15s, transform .1s;
    }
    .ce-btn:hover { background: #eef4ff; border-color: #2f7de1; }
    .ce-btn:active { transform: scale(.97); }
    .ce-btn:disabled { opacity: .5; cursor: default; }
    .ce-btn.primary { background: #2f7de1; color: #fff; border-color: #2f7de1; }
    .ce-btn.primary:hover { background: #2167c4; }
    .ce-btn.danger { color: #b3261e; border-color: #f1c4c0; }
    .ce-btn.danger:hover { background: #fdecea; border-color: #b3261e; }
    .ce-btn.small { padding: 4px 9px; font-size: 12px; }
    .ce-pen { margin-left: auto; flex-shrink: 0; }
    .ce-pen + .ce-pen { margin-left: 0; }
    .quest-hero .ce-pen { position: absolute; top: 18px; right: 18px; background: rgba(255,255,255,.12); color: #fff; border-color: rgba(255,255,255,.3); }
    .quest-hero .ce-pen:hover { background: rgba(255,255,255,.22); border-color: #fff; }
    .quest-hero { position: relative; }
    .quest-sub[data-sec] { display: flex; align-items: center; gap: 10px; }
    .ce-editing > .quest-body > :not(.ce-panel) { display: none !important; }

    .ce-panel {
        margin: 6px 0 4px; padding: 16px; border-radius: 14px;
        background: #f7faff; border: 1px solid #c9d8f5;
    }
    .ce-panel label { display: block; font: 700 12px 'Space Grotesk', sans-serif; letter-spacing: .06em; text-transform: uppercase; color: #4a5a85; margin: 0 0 6px; }
    .ce-input {
        width: 100%; padding: 9px 12px; margin-bottom: 12px;
        border-radius: 10px; border: 1px solid #c9d8f5; background: #fff;
        font: 500 15px 'Inter', system-ui, sans-serif; color: #0b1230;
    }
    .ce-input:focus, .ce-rich:focus { outline: 2px solid #2f7de1; outline-offset: 1px; }
    .ce-tools { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 6px; }
    .ce-tools button { min-width: 34px; padding: 5px 9px; border-radius: 8px; border: 1px solid #d4def2; background: #fff; cursor: pointer; font: 600 13px 'Inter', sans-serif; color: #1e3a8a; }
    .ce-tools button:hover { background: #eef4ff; }
    .ce-rich {
        min-height: 160px; max-height: 60vh; overflow: auto;
        padding: 12px 14px; border-radius: 10px; border: 1px solid #c9d8f5; background: #fff;
        font-size: 16px; line-height: 1.7; color: #1b2440;
    }
    .ce-rich img { max-width: 100%; height: auto; border-radius: 8px; }
    .ce-rich figure { margin: 10px 0; }
    .ce-chip {
        display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
        margin: 10px 0; padding: 10px 12px; border-radius: 10px;
        background: #eef1f7; border: 1px dashed #98a6c4; color: #3c4a6b;
        font: 600 13px 'Inter', sans-serif; user-select: none;
    }
    .ce-chip .ce-chip-preview { flex-basis: 100%; max-height: 140px; overflow: hidden; opacity: .8; pointer-events: none; }
    .ce-chip .ce-chip-preview svg { width: 100%; max-height: 140px; height: auto; }
    .ce-hint { margin: 8px 0 0; font-size: 13px; color: #5d6a8a; line-height: 1.5; }
    .ce-actions { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; margin-top: 12px; }
    .ce-error { color: #b3261e; font-size: 13px; margin-right: auto; align-self: center; }

    .ce-modal-back { position: fixed; inset: 0; z-index: 9000; background: rgba(8,14,40,.45); display: flex; align-items: flex-start; justify-content: center; padding: 6vh 16px; overflow: auto; }
    .ce-modal { width: min(720px, 100%); background: #fff; border-radius: 18px; padding: 22px; box-shadow: 0 30px 80px rgba(8,14,40,.35); }
    .ce-modal h3 { font: 700 20px 'Space Grotesk', sans-serif; color: #0b1230; margin: 0 0 4px; }
    .ce-modal > p { color: #5d6a8a; font-size: 14px; margin: 0 0 14px; }
    .ce-row { display: flex; align-items: center; gap: 6px; padding: 6px 0; }
    .ce-row .ce-input { margin: 0; }
    .ce-row.is-hidden .ce-input { text-decoration: line-through; color: #98a2b8; }
    .ce-module { border: 1px solid #e1e8f5; border-radius: 12px; padding: 8px 10px; margin-bottom: 10px; }
    .ce-module .ce-lessons { padding-left: 18px; border-left: 2px solid #e1e8f5; margin: 4px 0 4px 8px; }
    .ce-tag { font: 700 11px 'Space Grotesk', sans-serif; letter-spacing: .05em; padding: 2px 7px; border-radius: 999px; background: #e8f0ff; color: #1e3a8a; white-space: nowrap; }

    .ce-q { border: 1px solid #e1e8f5; border-radius: 12px; padding: 12px; margin-bottom: 10px; background: #fff; }
    .ce-q-head { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font: 700 13px 'Space Grotesk', sans-serif; color: #1e3a8a; }
    .ce-q-head .ce-btn { margin-left: auto; }
    .ce-opt { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
    .ce-opt .ce-input { margin: 0; }
    .ce-opt input[type=radio] { width: 18px; height: 18px; accent-color: #1f9d55; flex-shrink: 0; }
    .ce-opt b { width: 18px; color: #4a5a85; }
    .checkpoint-block { position: relative; }
    .ce-cp-pen { position: absolute; top: 12px; right: 12px; z-index: 2; }
    .ce-chip.ce-cp { background: #fff7e0; border-color: #f0b84a; color: #6b4a00; }
    .ce-outline-btn { margin: 0 16px 10px; width: calc(100% - 32px); justify-content: center; }

    .ce-lab-group { border: 1px solid #dbe5f7; border-radius: 14px; padding: 14px 14px 4px; margin-bottom: 12px; }
    .ce-lab-group h4 { font: 700 14px 'Space Grotesk', sans-serif; color: #0b1230; margin: 0 0 2px; }
    .ce-lab-group > p { font-size: 13px; color: #5d6a8a; margin: 0 0 10px; line-height: 1.45; }
    .ce-lab-group label { display: block; font: 600 13px 'Inter', sans-serif; color: #33416b; margin: 0 0 5px; }
    .ce-lab-row { display: grid; grid-template-columns: minmax(0, 1fr) 140px; gap: 10px; }
    .ce-lab-group textarea.ce-input { resize: vertical; line-height: 1.5; }
    .ce-lab-group .ce-mono { font: 500 13.5px/1.5 'Space Mono', ui-monospace, monospace; background: #0f1630; color: #e6edff; border-color: #0f1630; tab-size: 4; }
    .ce-lab-group .ce-mono.out { background: #f3f7ff; color: #0b1230; border-color: #c9d8f5; }
    .ce-lab-pen { margin-top: 10px; }
    .ce-obj { border-top: 1px dashed #dbe5f7; padding-top: 10px; margin-top: 4px; }
    .ce-obj-kind { font: 600 11px 'Inter', sans-serif; letter-spacing: .08em; text-transform: uppercase; color: #2f7de1; margin: 0 0 6px; }
    .ce-obj-fields { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 0 10px; background: #f6f9ff; border-radius: 10px; padding: 10px 10px 0; margin-bottom: 10px; }
    .ce-obj-fields label { font-size: 12px; }
    @media (max-width: 560px) { .ce-lab-row { grid-template-columns: 1fr; } }

    /* The lab studio: the lab as students get it (the code editor or the simulator), editable, full screen. */
    .ce-studio { position: fixed; inset: 0; z-index: 9000; display: flex; flex-direction: column; background: #0e1230; color: #e2e8f0; font-family: 'Inter', system-ui, sans-serif; }
    .ce-studio-top { display: flex; align-items: center; gap: 14px; padding: 10px 16px; background: #090d1f; border-bottom: 1px solid #1e2a45; flex-wrap: wrap; }
    .ce-studio-top .ce-studio-name { display: flex; flex-direction: column; min-width: 0; }
    .ce-studio-kicker { font: 700 11px 'Space Grotesk', sans-serif; letter-spacing: .1em; text-transform: uppercase; color: #f7a531; }
    .ce-studio-top strong { font: 700 17px 'Space Grotesk', sans-serif; color: #fff; }
    .ce-studio-acts { margin-left: auto; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .ce-studio-acts .ce-error { color: #ff9b93; max-width: 46ch; line-height: 1.4; }
    .ce-studio-back { background: none; border: 0; color: #9fb3d9; font: 600 14px 'Space Grotesk', sans-serif; cursor: pointer; padding: 6px 8px; border-radius: 8px; }
    .ce-studio-back:hover { color: #73b6ff; background: rgba(115,182,255,.08); }
    .ce-studio-main { flex: 1; min-height: 0; display: grid; grid-template-columns: minmax(300px, 400px) minmax(0, 1fr); }
    .ce-studio-side { overflow: auto; padding: 18px; background: #101833; border-right: 1px solid #1e2a45; }
    .ce-studio-work { display: flex; flex-direction: column; min-height: 0; overflow: auto; }
    .ce-studio label, .ce-studio .ce-s-label { display: block; font: 700 11px 'Space Grotesk', sans-serif; letter-spacing: .1em; text-transform: uppercase; color: #8796b8; margin: 0 0 6px; }
    .ce-studio .ce-input { background: #0a1024; border-color: #24325a; color: #e6edff; font-size: 14px; }
    .ce-studio .ce-input::placeholder { color: #5f6f96; }
    .ce-studio textarea.ce-input { resize: vertical; line-height: 1.55; }
    .ce-studio .ce-hint { color: #8796b8; }
    .ce-studio .ce-lab-group { border-color: #24325a; }
    .ce-studio .ce-lab-group h4 { color: #fff; }
    .ce-studio .ce-lab-group > p, .ce-studio .ce-obj-kind { color: #8796b8; }
    .ce-studio .ce-lab-group label { color: #b9c6e4; font: 600 12.5px 'Inter', sans-serif; letter-spacing: 0; text-transform: none; }
    .ce-studio .ce-obj { border-color: #24325a; }
    .ce-studio .ce-obj-fields { background: #0c1430; }
    .ce-studio .ce-btn { background: #16204a; color: #dbe6ff; border-color: #2b3a6b; }
    .ce-studio .ce-btn:hover { background: #1d2a5c; border-color: #73b6ff; }
    .ce-studio .ce-btn.primary { background: #73b6ff; color: #0e1230; border-color: #73b6ff; }
    .ce-studio .ce-btn.primary:hover { background: #8cc4ff; }
    .ce-studio .ce-btn.danger { color: #ff9b93; border-color: #5a2a35; }
    .ce-studio .ce-btn.go { background: #2fa56b; border-color: #2fa56b; color: #fff; }
    .ce-check { display: flex; gap: 8px; align-items: flex-start; font-size: 13px; color: #b9c6e4; line-height: 1.45; margin: 4px 0 0; }
    .ce-check input { margin-top: 3px; accent-color: #73b6ff; }

    .ce-tabs { display: flex; gap: 2px; padding: 0 12px; background: #090d1f; border-bottom: 1px solid #1e2a45; }
    .ce-tabs button { background: none; border: 0; border-bottom: 2px solid transparent; color: #8796b8; font: 500 13.5px 'Space Mono', monospace; padding: 11px 14px; cursor: pointer; }
    .ce-tabs button[aria-selected=true] { color: #fff; border-bottom-color: #73b6ff; }
    .ce-code { display: flex; flex: 1 1 280px; min-height: 240px; background: #0b0f24; }
    .ce-code-gutter { padding: 14px 10px 14px 14px; text-align: right; color: #3f4d74; font: 500 14px/1.6 'Space Mono', monospace; user-select: none; overflow: hidden; white-space: pre; }
    /* Syntax colouring like the students' editor: a highlighted <pre> sits behind a see-through textarea, same metrics. */
    .ce-code-stack { position: relative; flex: 1; min-width: 0; }
    .ce-code-stack pre, .ce-code textarea { position: absolute; inset: 0; margin: 0; resize: none; border: 0; outline: 0; padding: 14px 14px 14px 10px; background: transparent; font: 500 14px/1.6 'Space Mono', monospace; letter-spacing: normal; tab-size: 4; white-space: pre; word-wrap: normal; overflow: auto; }
    .ce-code-stack pre { pointer-events: none; overflow: hidden; color: #e6edff; }
    .ce-code textarea { color: transparent; caret-color: #73b6ff; }
    .ce-code textarea::selection { background: rgba(115,182,255,0.28); }
    .ce-code textarea::placeholder { color: #5f6f96; }
    .ce-run-bar { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-top: 1px solid #1e2a45; background: #090d1f; flex-wrap: wrap; }
    .ce-run-status { font: 500 13px 'Space Mono', monospace; color: #8796b8; margin-right: auto; }
    .ce-run-status.ready { color: #73b6ff; } .ce-run-status.bad { color: #ff9b93; }
    .ce-term { padding: 12px 14px; background: #070a18; border-top: 1px solid #1e2a45; }
    .ce-term pre { margin: 6px 0 0; min-height: 3.2em; max-height: 220px; overflow: auto; font: 500 13.5px/1.55 'Space Mono', monospace; color: #b8f2d2; white-space: pre-wrap; }
    .ce-term pre.err { color: #ff9b93; }
    .ce-expect { padding: 12px 14px 16px; border-top: 1px solid #1e2a45; }
    .ce-expect-head { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 6px; }
    .ce-expect-head label { margin: 0; }
    .ce-expect .ce-input { font: 500 13.5px/1.55 'Space Mono', monospace; margin: 0; }
    .ce-badge { font: 700 12px 'Inter', sans-serif; padding: 3px 9px; border-radius: 999px; background: #1a2347; color: #8796b8; }
    .ce-badge.ok { background: rgba(47,165,107,.18); color: #b8f2d2; }
    .ce-badge.bad { background: rgba(255,155,147,.14); color: #ff9b93; }
    .ce-sim-bar { display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: #090d1f; border-bottom: 1px solid #1e2a45; flex-wrap: wrap; }
    .ce-sim-msg { font-size: 13.5px; color: #b9c6e4; margin-right: auto; }
    .ce-sim-msg.ok { color: #b8f2d2; }
    .ce-sim-frame { flex: 1; min-height: 420px; border: 0; width: 100%; background: #070a18; }
    .ce-sim-empty { flex: 1; display: grid; place-items: center; text-align: center; padding: 30px; color: #8796b8; font-size: 14.5px; line-height: 1.6; }
    .ce-sim-empty strong { display: block; color: #fff; font: 700 18px 'Space Grotesk', sans-serif; margin-bottom: 6px; }
    @media (max-width: 900px) {
        .ce-studio-main { grid-template-columns: 1fr; overflow: auto; }
        .ce-studio-side { border-right: 0; border-bottom: 1px solid #1e2a45; overflow: visible; }
        .ce-studio-work { overflow: visible; }
    }

    @media (prefers-reduced-motion: reduce) { .ce-btn { transition: none; } }
</style>

<script>
(function () {
    const SLUG    = @json($slug);
    const BASE    = @json(url('/faculty/course-editor/'.$slug));
    const QUIZ    = @json($ceCrewId ? url('/faculty/courses/'.$ceCrewId.'/lesson-quizzes') : null);
    const CSRF    = @json(csrf_token());
    const LABS    = @json(\App\Services\LessonLabService::supports($slug));
    const SIMLABS = @json(\App\Services\SimLabService::supports($slug));
    const stage   = document.getElementById('lesson-stage');

    let doc = null;        // the lesson being edited: {title, objective, sections, locked, edited, custom}
    let loaded = null;     // "m1/lesson01" the doc belongs to

    // ── helpers ────────────────────────────────────────────────────────────
    const esc = function (t) { const d = document.createElement('div'); d.textContent = t == null ? '' : String(t); return d.innerHTML; };

    async function api(method, url, body) {
        const opts = { method: method, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' } };
        if (body instanceof FormData) {
            opts.body = body;
        } else if (body !== undefined) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(body);
        }
        const res = await fetch(url, opts);
        const data = await res.json().catch(function () { return {}; });
        if (!res.ok) {
            const first = data.errors ? Object.values(data.errors)[0] : null;
            throw new Error((first && first[0]) || data.message || 'Something went wrong. Try again.');
        }
        return data;
    }

    function lessonUrl(cur) { return BASE + '/lessons/' + cur.module + '/' + cur.lesson; }

    function status(text) {
        const el = stage.querySelector('.ce-status');
        if (el) el.textContent = text;
    }

    function reload() {
        const cur = window.TechLab.currentLesson();
        return window.TechLab.loadLesson(cur.module, cur.lesson, { pushState: false });
    }

    async function save(next) {
        const cur = window.TechLab.currentLesson();
        await api('PUT', lessonUrl(cur), { title: next.title, objective: next.objective, sections: next.sections, checkpoints: next.checkpoints });
        await reload();
        status('Saved ✓ Students see this version now.');
    }

    function lockedInfo(n) {
        return (doc && doc.locked || []).find(function (b) { return b.index === n; }) || { index: n, kind: 'interactive', label: 'Built-in block', required: true };
    }

    // ── rich text: placeholders <-> chips ─────────────────────────────────
    function chipHtml(n) {
        const b = lockedInfo(n);
        let html = '<div class="ce-chip" contenteditable="false" data-keep="' + n + '"><span>🔒 ' + esc(b.label) + '</span>';
        if (b.kind === 'diagram') html += '<button type="button" class="ce-btn small" data-act="replace-img">Replace with an image</button>';
        if (b.kind === 'quiz') html += '<span style="font-weight:500">(edit the questions with “Edit quiz”)</span>';
        if (!b.required) html += '<button type="button" class="ce-btn small danger" data-act="remove-chip">Remove</button>';
        if (b.preview) html += '<div class="ce-chip-preview">' + b.preview + '</div>';
        return html + '</div>';
    }

    function checkpointChipHtml(id, q) {
        return '<div class="ce-chip ce-cp" contenteditable="false" data-checkpoint="' + esc(id) + '"><span>❓ Checkpoint: <span class="ce-cp-q">' + esc((q && q.prompt) || 'New question') + '</span></span>' +
            '<button type="button" class="ce-btn small" data-act="edit-cp">Edit</button>' +
            '<button type="button" class="ce-btn small danger" data-act="remove-chip">Remove</button></div>';
    }

    function toEditable(html, cps) {
        return (html || '')
            .replace(/<div[^>]*\bdata-keep="(\d+)"[^>]*>\s*<\/div>/g, function (_, n) { return chipHtml(Number(n)); })
            .replace(/<div[^>]*\bdata-checkpoint="([a-z0-9_-]+)"[^>]*>\s*<\/div>/g, function (_, id) { return checkpointChipHtml(id, cps && cps[id]); });
    }

    function fromEditable(el) {
        const copy = el.cloneNode(true);
        copy.querySelectorAll('.ce-chip').forEach(function (c) {
            const ph = document.createElement('div');
            if (c.dataset.checkpoint) ph.setAttribute('data-checkpoint', c.dataset.checkpoint);
            else ph.setAttribute('data-keep', c.dataset.keep);
            c.replaceWith(ph);
        });
        return copy.innerHTML.trim();
    }

    function pickImage() {
        return new Promise(function (resolve) {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/png,image/jpeg,image/webp,image/gif';
            input.onchange = function () { resolve(input.files[0] || null); };
            input.click();
        });
    }

    async function uploadImage() {
        const file = await pickImage();
        if (!file) return null;
        if (file.size > 5 * 1024 * 1024) throw new Error('Images can be at most 5 MB.');
        const fd = new FormData();
        fd.append('image', file);
        status('Uploading image…');
        const r = await api('POST', BASE + '/images', fd);
        status('Image uploaded. Save to keep it.');
        return r.url;
    }

    function figureHtml(url) {
        return '<figure class="net-diagram"><img src="' + esc(url) + '" alt="Diagram"></figure><p class="body-text"><br></p>';
    }

    function richEditor(html, opts) {
        opts = opts || {};
        const wrap = document.createElement('div');
        const tools = document.createElement('div');
        tools.className = 'ce-tools';
        const buttons = [
            ['B', 'Bold', function () { document.execCommand('bold'); }],
            ['I', 'Italic', function () { document.execCommand('italic'); }],
            ['</>', 'Code', function () {
                const t = String(window.getSelection() || '');
                document.execCommand('insertHTML', false, '<code>' + esc(t || 'code') + '</code>');
            }],
            ['• List', 'Bulleted list', function () { document.execCommand('insertUnorderedList'); }],
            ['1. List', 'Numbered list', function () { document.execCommand('insertOrderedList'); }],
            ['Link', 'Add a link', function () {
                const url = prompt('Link address (https://…)');
                if (url && /^https?:\/\//i.test(url.trim())) document.execCommand('createLink', false, url.trim());
            }],
        ];
        if (!opts.inline) {
            buttons.push(['Code block', 'Code block', function () {
                const t = String(window.getSelection() || '');
                document.execCommand('insertHTML', false, '<pre class="code-block"><code>' + esc(t || 'code here') + '</code></pre><p class="body-text"><br></p>');
            }]);
            if (opts.cps) buttons.push(['❓ Checkpoint', 'Add a checkpoint question here', function () {
                const range = saveRange();
                const id = 'n' + Date.now().toString(36);
                const q = { prompt: '', options: ['', '', '', ''], correct: 'A', explanation: '' };
                editCheckpointModal(q, function (saved) {
                    opts.cps[id] = saved;
                    restoreRange(range);
                    rich.focus();
                    document.execCommand('insertHTML', false, checkpointChipHtml(id, saved) + '<p class="body-text"><br></p>');
                });
            }]);
            buttons.push(['🖼 Image', 'Upload an image (diagram)', async function () {
                const range = saveRange();
                try {
                    const url = await uploadImage();
                    if (!url) return;
                    restoreRange(range);
                    document.execCommand('insertHTML', false, figureHtml(url));
                } catch (e) { status(e.message); }
            }]);
        }
        buttons.push(['Clear', 'Clear formatting', function () { document.execCommand('removeFormat'); }]);

        const rich = document.createElement('div');
        rich.className = 'ce-rich';
        rich.contentEditable = 'true';
        rich.innerHTML = opts.inline ? (html || '') : toEditable(html, opts.cps);
        if (opts.inline) rich.style.minHeight = '70px';

        buttons.forEach(function (b) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = b[0];
            btn.title = b[1];
            btn.addEventListener('mousedown', function (e) { e.preventDefault(); });   // keep the selection
            btn.addEventListener('click', function () { rich.focus(); b[2](); });
            tools.appendChild(btn);
        });

        // Pasted text comes in plain: no stray fonts and colours from other apps.
        rich.addEventListener('paste', function (e) {
            e.preventDefault();
            document.execCommand('insertText', false, (e.clipboardData || window.clipboardData).getData('text/plain'));
        });

        rich.addEventListener('click', async function (e) {
            const act = e.target.closest('[data-act]');
            if (!act) return;
            const chip = act.closest('.ce-chip');
            if (act.dataset.act === 'remove-chip') chip.remove();
            if (act.dataset.act === 'edit-cp' && opts.cps) {
                const id = chip.dataset.checkpoint;
                const q = JSON.parse(JSON.stringify(opts.cps[id] || { prompt: '', options: ['', '', '', ''], correct: 'A', explanation: '' }));
                editCheckpointModal(q, function (saved) {
                    opts.cps[id] = saved;
                    chip.querySelector('.ce-cp-q').textContent = saved.prompt;
                });
            }
            if (act.dataset.act === 'replace-img') {
                try {
                    const url = await uploadImage();
                    if (!url) return;
                    const tmp = document.createElement('div');
                    tmp.innerHTML = figureHtml(url);
                    chip.replaceWith.apply(chip, Array.from(tmp.childNodes));
                } catch (err) { status(err.message); }
            }
        });

        wrap.appendChild(tools);
        wrap.appendChild(rich);
        wrap.rich = rich;
        return wrap;
    }

    function saveRange() {
        const sel = window.getSelection();
        return sel.rangeCount ? sel.getRangeAt(0).cloneRange() : null;
    }
    function restoreRange(range) {
        if (!range) return;
        const sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    }

    function actions(onSave, onCancel) {
        const row = document.createElement('div');
        row.className = 'ce-actions';
        row.innerHTML = '<span class="ce-error" role="alert"></span><button type="button" class="ce-btn" data-a="cancel">Cancel</button><button type="button" class="ce-btn primary" data-a="save">Save</button>';
        const err = row.querySelector('.ce-error');
        row.querySelector('[data-a=cancel]').addEventListener('click', onCancel);
        row.querySelector('[data-a=save]').addEventListener('click', async function () {
            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Saving…';
            err.textContent = '';
            try { await onSave(); } catch (e) { err.textContent = e.message; btn.disabled = false; btn.textContent = 'Save'; }
        });
        return row;
    }

    // Sections that have an <h2> on the page, in order: the k-th h2[data-sec] is the k-th of these.
    function headed() {
        return doc.sections.map(function (s, i) { return { s: s, i: i }; }).filter(function (x) { return (x.s.heading || '').trim() !== ''; });
    }

    function cloneDoc() {
        return {
            title: doc.title, objective: doc.objective,
            sections: doc.sections.map(function (s) { return { heading: s.heading, html: s.html }; }),
            checkpoints: JSON.parse(JSON.stringify(doc.checkpoints || {})),
        };
    }

    // ── one stage section ──────────────────────────────────────────────────
    function editSection(h2) {
        const entry = headed()[Number(h2.dataset.sec)];
        if (!entry) return;
        const card = h2.closest('.quest-card');
        if (!card || card.classList.contains('ce-editing')) return;
        const body = card.querySelector('.quest-body');

        const panel = document.createElement('div');
        panel.className = 'ce-panel';
        panel.innerHTML = '<label>Section heading</label>';
        const heading = document.createElement('input');
        heading.className = 'ce-input';
        heading.maxLength = 120;
        heading.value = entry.s.heading;
        panel.appendChild(heading);
        const lab = document.createElement('label');
        lab.textContent = 'Content';
        panel.appendChild(lab);
        const working = cloneDoc();
        const ed = richEditor(entry.s.html, { cps: working.checkpoints });
        panel.appendChild(ed);
        const hint = document.createElement('p');
        hint.className = 'ce-hint';
        hint.innerHTML = 'Locked blocks (exercise buttons, labs, the quiz) are built into the lesson — write around them freely. Checkpoints are questions that unlock the next stage: <b>Edit</b> one, or add a new one with <b>Checkpoint</b>. Swap a built-in diagram for an uploaded image with <b>Replace with an image</b>, or add one anywhere with <b>Image</b>.';
        panel.appendChild(hint);

        function close() { card.classList.remove('ce-editing'); panel.remove(); }
        panel.appendChild(actions(async function () {
            if (!heading.value.trim()) throw new Error('The section needs a heading.');
            const next = working;
            next.sections[entry.i] = { heading: heading.value.trim(), html: fromEditable(ed.rich) };
            await save(next);
        }, close));

        body.prepend(panel);
        card.classList.add('ce-editing');
        card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        ed.rich.focus();
    }

    // ── title + objective ─────────────────────────────────────────────────
    function editHero() {
        const hero = stage.querySelector('.quest-hero');
        modal('Lesson title & objective', 'The title shows at the top of the lesson. The objective is what a student can do by the end of it.', function (box, close) {
            box.insertAdjacentHTML('beforeend', '<label>Title</label>');
            const title = document.createElement('input');
            title.className = 'ce-input';
            title.maxLength = 200;
            title.value = doc.title;
            box.appendChild(title);
            box.insertAdjacentHTML('beforeend', '<label>Learning objective</label>');
            const ed = richEditor(doc.objective, { inline: true });
            box.appendChild(ed);
            box.appendChild(actions(async function () {
                if (!title.value.trim()) throw new Error('The lesson needs a title.');
                const next = cloneDoc();
                next.title = title.value.trim();
                next.objective = ed.rich.innerHTML.trim();
                await save(next);
                close();
            }, close));
        });
        if (hero) hero.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // ── the stage list ────────────────────────────────────────────────────
    const STAGES = @json(\App\Services\LessonContentService::stages(\App\Services\CourseProgressService::planetFor($slug)));
    const PRESETS = STAGES.map(function (s) { return s.heading; });

    function editStages() {
        modal('Lesson stages', 'Every lesson in this course follows the same stages, in this order: ' + PRESETS.join(' → ') + '. Keep these names so students see the right stage cards.', function (box, close) {
            const list = doc.sections.map(function (s) { return { heading: s.heading, html: s.html }; });
            const rank = function (s) {
                const i = PRESETS.findIndex(function (p) { return p.toLowerCase() === s.heading.trim().toLowerCase(); });
                return s.heading.trim() === '' ? -1 : (i === -1 ? PRESETS.length : i);
            };

            const std = document.createElement('div');
            std.className = 'ce-row';
            std.innerHTML = '<button type="button" class="ce-btn small">Use standard stages</button><span style="font-size:.85rem;opacity:.75;">Adds any missing stage and puts them all in order. Nothing you wrote is removed.</span>';
            std.querySelector('button').addEventListener('click', function () {
                STAGES.forEach(function (s) {
                    if (!list.some(function (x) { return x.heading.trim().toLowerCase() === s.heading.toLowerCase(); })) {
                        list.push({ heading: s.heading, html: s.html });
                    }
                });
                const sorted = list.map(function (s, i) { return [rank(s), i, s]; })
                    .sort(function (a, b) { return a[0] - b[0] || a[1] - b[1]; })
                    .map(function (x) { return x[2]; });
                list.splice(0, list.length, ...sorted);
                paint();
            });
            box.appendChild(std);
            const holder = document.createElement('div');
            box.appendChild(holder);

            function hasRequired(s) {
                return (s.html.match(/data-keep="(\d+)"/g) || []).some(function (m) { return lockedInfo(Number(m.replace(/\D/g, ''))).required; });
            }

            function paint() {
                holder.innerHTML = '';
                list.forEach(function (s, i) {
                    const row = document.createElement('div');
                    row.className = 'ce-row';
                    const input = document.createElement('input');
                    input.className = 'ce-input';
                    input.value = s.heading;
                    input.placeholder = i === 0 && s.heading === '' ? '(intro, no heading)' : 'Stage name';
                    input.addEventListener('input', function () { s.heading = input.value; });
                    row.appendChild(input);
                    [['↑', i > 0, function () { list.splice(i - 1, 0, list.splice(i, 1)[0]); paint(); }],
                     ['↓', i < list.length - 1, function () { list.splice(i + 1, 0, list.splice(i, 1)[0]); paint(); }],
                     ['Remove', true, function () {
                         if (hasRequired(s) && !confirm('This stage holds a built-in block (an exercise button, lab or checkpoint). The block will move to the last stage. Remove the stage?')) return;
                         if (!hasRequired(s) && !confirm('Remove the stage “' + (s.heading || 'intro') + '”?')) return;
                         list.splice(i, 1); paint();
                     }]].forEach(function (b) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'ce-btn small' + (b[0] === 'Remove' ? ' danger' : '');
                        btn.textContent = b[0];
                        btn.disabled = !b[1];
                        btn.addEventListener('click', b[2]);
                        row.appendChild(btn);
                    });
                    holder.appendChild(row);
                });
            }
            paint();

            const add = document.createElement('div');
            add.className = 'ce-row';
            add.innerHTML = '<select class="ce-input">' + PRESETS.map(function (p) { return '<option>' + esc(p) + '</option>'; }).join('') + '<option value="">Other…</option></select><button type="button" class="ce-btn small">+ Add stage</button>';
            add.querySelector('button').addEventListener('click', function () {
                let name = add.querySelector('select').value;
                if (!name) name = (prompt('Stage name') || '').trim();
                if (!name) return;
                const preset = STAGES.find(function (s) { return s.heading === name; });
                list.push({ heading: name, html: preset ? preset.html : (/quiz/i.test(name) ? '' : '<p class="body-text">Write this stage here.</p>') });
                paint();
            });
            box.appendChild(add);

            box.appendChild(actions(async function () {
                if (list.some(function (s, i) { return i > 0 && !s.heading.trim(); })) throw new Error('Every stage needs a name.');
                const next = cloneDoc();
                next.sections = list.map(function (s) { return { heading: s.heading.trim(), html: s.html }; });
                await save(next);
                close();
            }, close));
        });
    }

    // ── one multiple-choice question (quiz questions and checkpoints) ─────
    function questionFields(q, name, title, onRemove) {
        const box = document.createElement('div');
        box.className = 'ce-q';
        function paint() {
            box.innerHTML = '<div class="ce-q-head">' + esc(title) + '</div>';
            if (onRemove) {
                const del = document.createElement('button');
                del.type = 'button';
                del.className = 'ce-btn small danger';
                del.textContent = 'Remove';
                del.addEventListener('click', onRemove);
                box.firstChild.appendChild(del);
            }

            const prompt = document.createElement('textarea');
            prompt.className = 'ce-input';
            prompt.rows = 2;
            prompt.placeholder = 'The question';
            prompt.value = q.prompt;
            prompt.addEventListener('input', function () { q.prompt = prompt.value; });
            box.appendChild(prompt);

            q.options.forEach(function (opt, j) {
                const letter = String.fromCharCode(65 + j);
                const row = document.createElement('div');
                row.className = 'ce-opt';
                row.innerHTML = '<input type="radio" name="' + esc(name) + '" title="Right answer"' + (q.correct === letter ? ' checked' : '') + '><b>' + letter + '</b>';
                row.firstChild.addEventListener('change', function () { q.correct = letter; });
                const input = document.createElement('input');
                input.className = 'ce-input';
                input.value = opt;
                input.placeholder = 'Choice ' + letter;
                input.addEventListener('input', function () { q.options[j] = input.value; });
                row.appendChild(input);
                if (q.options.length > 2) {
                    const x = document.createElement('button');
                    x.type = 'button';
                    x.className = 'ce-btn small';
                    x.textContent = '✕';
                    x.title = 'Remove this choice';
                    x.addEventListener('click', function () {
                        q.options.splice(j, 1);
                        const k = q.correct.charCodeAt(0) - 65;
                        q.correct = String.fromCharCode(65 + (k === j ? 0 : (k > j ? k - 1 : k)));
                        paint();
                    });
                    row.appendChild(x);
                }
                box.appendChild(row);
            });
            if (q.options.length < 4) {
                const more = document.createElement('button');
                more.type = 'button';
                more.className = 'ce-btn small';
                more.textContent = '+ Choice';
                more.addEventListener('click', function () { q.options.push(''); paint(); });
                box.appendChild(more);
            }

            const ex = document.createElement('textarea');
            ex.className = 'ce-input';
            ex.rows = 2;
            ex.style.marginTop = '8px';
            ex.placeholder = 'Why the right answer is right (shown after answering)';
            ex.value = q.explanation;
            ex.addEventListener('input', function () { q.explanation = ex.value; });
            box.appendChild(ex);
        }
        paint();
        return box;
    }

    function checkQuestion(q, what) {
        if (!q.prompt.trim()) throw new Error(what + ' needs its question.');
        if (q.options.some(function (o) { return !o.trim(); })) throw new Error('Fill in every choice of ' + what.toLowerCase() + ' (or remove the empty one).');
    }

    const QUESTION_HINT = 'Pick the right answer with the round button. Keep all the choices about the same length, and don’t make the right one the longest: students notice. Write <code>`backticks`</code> around code.';

    // A checkpoint question in a modal. onDone(q) gets the edited question; onRemove (optional) deletes it.
    function editCheckpointModal(q, onDone, onRemove) {
        modal('Checkpoint', 'A checkpoint ends a stage: students answer it right to unlock the next stage. A wrong answer sends them back to re-read, without showing the right one.', function (box, close) {
            box.appendChild(questionFields(q, 'ce-cp', 'Question', null));
            const hint = document.createElement('p');
            hint.className = 'ce-hint';
            hint.innerHTML = QUESTION_HINT;
            box.appendChild(hint);
            const row = actions(async function () {
                checkQuestion(q, 'The checkpoint');
                await onDone({ prompt: q.prompt.trim(), options: q.options.map(function (o) { return o.trim(); }), correct: q.correct, explanation: q.explanation.trim() });
                close();
            }, close);
            if (onRemove) {
                const del = document.createElement('button');
                del.type = 'button';
                del.className = 'ce-btn danger';
                del.textContent = 'Remove checkpoint';
                del.addEventListener('click', async function () {
                    if (!confirm('Remove this checkpoint? The next stage will no longer wait for it.')) return;
                    try { await onRemove(); close(); } catch (e) { row.querySelector('.ce-error').textContent = e.message; }
                });
                row.insertBefore(del, row.querySelector('[data-a=cancel]'));
            }
            box.appendChild(row);
        });
    }

    // The ✏️ on a checkpoint shown on the page: edit (or remove) it and save the lesson.
    function editPageCheckpoint(block) {
        const id = doc.checkpointsByNumber[block.dataset.cp];
        if (!id || !doc.checkpoints[id]) { status('Could not find this checkpoint. Reload the page and try again.'); return; }
        const q = JSON.parse(JSON.stringify(doc.checkpoints[id]));
        editCheckpointModal(q, async function (saved) {
            const next = cloneDoc();
            next.checkpoints[id] = saved;
            await save(next);
            status('Checkpoint saved ✓');
        }, async function () {
            const next = cloneDoc();
            delete next.checkpoints[id];
            next.sections.forEach(function (s) {
                s.html = s.html.replace(new RegExp('<div[^>]*data-checkpoint="' + id + '"[^>]*>\\s*<\\/div>', 'g'), '');
            });
            await save(next);
            status('Checkpoint removed.');
        });
    }

    // ── the quiz ──────────────────────────────────────────────────────────
    async function editQuiz(card) {
        if (!QUIZ) { alert('Open your class for this course on your dashboard first: quizzes are saved for the class.'); return; }
        if (card.classList.contains('ce-editing')) return;
        const cur = window.TechLab.currentLesson();
        const url = QUIZ + '/' + cur.module + '/' + cur.lesson;
        let data;
        try { data = await api('GET', url + '/data'); } catch (e) { status(e.message); return; }

        const qs = data.questions.map(function (q) { return { prompt: q.prompt, options: q.options.slice(), correct: q.correct, explanation: q.explanation || '' }; });
        const body = card.querySelector('.quest-body');
        const panel = document.createElement('div');
        panel.className = 'ce-panel';
        const holder = document.createElement('div');
        panel.appendChild(holder);

        function paint() {
            holder.innerHTML = '';
            qs.forEach(function (q, i) {
                holder.appendChild(questionFields(q, 'ce-q' + i, 'Question ' + (i + 1), qs.length < 2 ? null : function () { qs.splice(i, 1); paint(); }));
            });
        }
        paint();

        const add = document.createElement('button');
        add.type = 'button';
        add.className = 'ce-btn';
        add.textContent = '+ Add question';
        add.addEventListener('click', function () { qs.push({ prompt: '', options: ['', '', '', ''], correct: 'A', explanation: '' }); paint(); });
        panel.appendChild(add);

        const hint = document.createElement('p');
        hint.className = 'ce-hint';
        hint.innerHTML = QUESTION_HINT;
        panel.appendChild(hint);

        function close() { card.classList.remove('ce-editing'); panel.remove(); }
        const row = actions(async function () {
            qs.forEach(function (q, i) { checkQuestion(q, 'Question ' + (i + 1)); });
            await api('PUT', url, { questions: qs });
            await reload();
            status('Quiz saved ✓');
        }, close);
        if (data.edited && data.default.length) {
            const reset = document.createElement('button');
            reset.type = 'button';
            reset.className = 'ce-btn danger';
            reset.textContent = 'Reset to the default quiz';
            reset.addEventListener('click', async function () {
                if (!confirm('Throw away your questions and use the lesson\'s default quiz again?')) return;
                try { await api('DELETE', url); await reload(); status('Quiz reset to the default.'); } catch (e) { status(e.message); }
            });
            row.insertBefore(reset, row.querySelector('[data-a=cancel]'));
        }
        panel.appendChild(row);

        body.prepend(panel);
        card.classList.add('ce-editing');
        card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // ── the lab studio: the lab as students get it, editable, full screen ─────────
    function studio(kicker, title, build) {
        const back = document.createElement('div');
        back.className = 'ce-studio';
        back.setAttribute('role', 'dialog');
        back.setAttribute('aria-modal', 'true');
        back.innerHTML =
            '<header class="ce-studio-top"><button type="button" class="ce-studio-back">← Back to lesson</button>' +
            '<div class="ce-studio-name"><span class="ce-studio-kicker">' + esc(kicker) + '</span><strong>' + esc(title) + '</strong></div>' +
            '<div class="ce-studio-acts"></div></header>' +
            '<div class="ce-studio-main"><aside class="ce-studio-side"></aside><section class="ce-studio-work"></section></div>';
        const cleanups = [];
        function close() {
            back.remove();
            document.body.style.overflow = '';
            document.removeEventListener('keydown', onKey);
            cleanups.forEach(function (f) { f(); });
        }
        function onKey(e) { if (e.key === 'Escape' && !e.target.closest('textarea')) close(); }
        document.addEventListener('keydown', onKey);
        back.querySelector('.ce-studio-back').addEventListener('click', close);
        build({
            side: back.querySelector('.ce-studio-side'),
            work: back.querySelector('.ce-studio-work'),
            acts: back.querySelector('.ce-studio-acts'),
            close: close,
            onClose: function (f) { cleanups.push(f); },
        });
        document.body.appendChild(back);
        document.body.style.overflow = 'hidden';
        const first = back.querySelector('.ce-studio-side input, .ce-studio-side textarea');
        if (first) first.focus();
    }

    // Labelled fields for the studio's side panel.
    let fieldN = 0;
    function field(parent, label, el) {
        const id = 'ce-f-' + (++fieldN);
        const wrap = document.createElement('div');
        wrap.innerHTML = '<label for="' + id + '">' + esc(label) + '</label>';
        el.id = id;
        wrap.appendChild(el);
        parent.appendChild(wrap);
        return el;
    }
    function textInput(value, max) {
        const el = document.createElement('input');
        el.className = 'ce-input';
        el.maxLength = max;
        el.value = value == null ? '' : value;
        return el;
    }
    function textArea(value, rows, max, placeholder) {
        const el = document.createElement('textarea');
        el.className = 'ce-input';
        el.rows = rows;
        if (max) el.maxLength = max;
        el.value = value || '';
        if (placeholder) el.placeholder = placeholder;
        return el;
    }
    function selectInput(options, value) {
        const el = document.createElement('select');
        el.className = 'ce-input';
        options.forEach(function (o) { el.add(new Option(o, o, false, o === value)); });
        return el;
    }
    function group(parent, title, intro) {
        const g = document.createElement('div');
        g.className = 'ce-lab-group';
        g.innerHTML = '<h4>' + esc(title) + '</h4>' + (intro ? '<p>' + esc(intro) + '</p>' : '');
        parent.appendChild(g);
        return g;
    }
    // Save / cancel (and reset) in the studio's top bar.
    function studioActions(s, onSave, reset) {
        s.acts.innerHTML = '<span class="ce-error" role="alert"></span>';
        const err = s.acts.firstChild;
        if (reset) {
            const r = document.createElement('button');
            r.type = 'button';
            r.className = 'ce-btn danger';
            r.textContent = 'Reset to the original lab';
            r.addEventListener('click', reset);
            s.acts.appendChild(r);
        }
        const cancel = document.createElement('button');
        cancel.type = 'button';
        cancel.className = 'ce-btn';
        cancel.textContent = 'Cancel';
        cancel.addEventListener('click', s.close);
        const save = document.createElement('button');
        save.type = 'button';
        save.className = 'ce-btn primary';
        save.textContent = 'Save lab';
        save.addEventListener('click', async function () {
            save.disabled = true;
            save.textContent = 'Checking…';
            err.textContent = '';
            try { await onSave(); } catch (e) { err.textContent = e.message; save.disabled = false; save.textContent = 'Save lab'; }
        });
        s.acts.appendChild(cancel);
        s.acts.appendChild(save);
        return err;
    }

    // ── running code the way the students' editor does (code-editor.blade.php) ──
    const RUN_TIMEOUT_MS = 5000;
    let pyodideReady = null;

    // C: a fresh worker per run (JSCPP or PicoC, as the course's editor uses), killed if it runs too long.
    function runC(workerUrl, code, input) {
        return new Promise(function (resolve, reject) {
            const w = new Worker(workerUrl);
            let started = false;
            const timer = setTimeout(function () {
                w.terminate();
                reject(new Error('The program did not finish in ' + (RUN_TIMEOUT_MS / 1000) + ' seconds. Check for an infinite loop.'));
            }, RUN_TIMEOUT_MS);
            w.onmessage = function (e) {
                const d = e.data || {};
                if (d.ready && !started) { started = true; w.postMessage({ code: code, input: input }); return; }
                clearTimeout(timer);
                w.terminate();
                if (d.ok) resolve(d.output || ''); else reject(new Error(d.error || 'The program stopped with an error.'));
            };
            w.onerror = function (e) {
                clearTimeout(timer);
                w.terminate();
                reject(new Error(e.message || 'The C runtime crashed.'));
            };
        });
    }

    function loadPyodide() {
        if (!pyodideReady) {
            pyodideReady = new Promise(function (resolve, reject) {
                const done = function () { window.loadPyodide().then(resolve, reject); };
                if (window.loadPyodide) return done();
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/pyodide/v0.26.4/full/pyodide.js';
                s.onload = done;
                s.onerror = function () { pyodideReady = null; reject(new Error('Could not load Python.')); };
                document.head.appendChild(s);
            });
        }
        return pyodideReady;
    }

    async function runPython(code) {
        const py = await loadPyodide();
        let captured = '';
        py.setStdout({ batched: function (m) { captured += m; } });
        py.setStderr({ batched: function (m) { captured += m; } });
        await py.runPythonAsync(code);
        return captured;
    }

    // The lab checks output the way CourseProgressService::outputMatches does.
    function cleanOutput(s) { return String(s || '').replace(/\r\n/g, '\n').trim(); }

    // The Stage 5 section of the lesson page ("Coding Exercise"), when the lesson has one of its own.
    function practiceSection() {
        const h2 = stage.querySelector('.quest-card[data-kind=practice] .quest-head h2[data-sec]');
        return h2 && doc ? (headed()[Number(h2.dataset.sec)] || null) : null;
    }

    // The task as the lesson page shows it: the instructions and the output to print, keeping the section's
    // built-in blocks (the "Code it yourself" button, checkpoints) where they were.
    function taskHtml(instructions, expected, oldHtml) {
        const keep = (oldHtml.match(/<div[^>]*\bdata-(?:keep|checkpoint)="[^"]*"[^>]*>\s*<\/div>/g) || []).join('\n');
        const paras = instructions.trim().split(/\n\s*\n/).map(function (p) {
            return '<p class="body-text">' + esc(p.trim()).replace(/\n/g, '<br>') + '</p>';
        }).join('\n');
        return paras + '\n<p class="body-text"><strong>Expected output:</strong></p>\n<pre class="code-block"><code>' + esc(expected.trim()) + '</code></pre>' + (keep ? '\n' + keep : '');
    }

    // ── the coding lab ("Code it yourself") ───────────────────────────────
    async function editLab() {
        const cur = window.TechLab.currentLesson();
        const url = lessonUrl(cur) + '/lab';
        let data;
        try { data = await api('GET', url); } catch (e) { status(e.message); return; }
        const lab = data.lab;
        const isC = data.lang === 'c';

        studio('Stage 5 · Coding exercise', data.exists ? 'Edit the coding lab' : 'Add a coding lab', function (s) {
            // Side: what students read.
            const row = document.createElement('div');
            row.className = 'ce-lab-row';
            s.side.appendChild(row);
            const title = field(row, 'Title', textInput(lab.title, 120));
            const level = field(row, 'Difficulty', selectInput(data.difficulties, lab.difficulty));
            const instructions = field(s.side, 'Instructions', textArea(lab.instructions, 10, 5000, 'Example: Print your name and your age, each on its own line.'));
            const hintBody = field(s.side, 'Astro\'s hint (optional)', textArea(lab.hint_body, 3, 3000, 'A nudge in the right direction. Students buy it with XP, and see your answer code with it.'));
            const section = practiceSection();
            let showTask = null;
            if (section) {
                const p = document.createElement('label');
                p.className = 'ce-check';
                p.innerHTML = '<input type="checkbox" checked> <span>Also show this task and its expected output in Stage 5 of the lesson page (replaces the text there).</span>';
                s.side.appendChild(p);
                showTask = p.querySelector('input');
            }
            const note = document.createElement('p');
            note.className = 'ce-hint';
            note.textContent = 'Students pass when their program prints exactly the expected output (spelling, capitals and punctuation count; spaces and blank lines at the very start and end don\'t). Saving runs your answer code first, so a wrong expected output can\'t go live.';
            s.side.appendChild(note);

            // Work: the editor, like the students'.
            // A new C lab starts from the skeleton every C program needs, so faculty only write the middle.
            const skeleton = function (body) { return isC ? '#include <stdio.h>\n\nint main() {\n    ' + body + '\n\n    return 0;\n}\n' : ''; };
            const orSkeleton = function (code, body) { return (code || '').trim() ? code : skeleton(body); };
            const codes = {
                answer: orSkeleton(lab.hint_code, '// Your solution: print exactly the expected output.'),
                starter: orSkeleton(lab.starter_code, '// Write your code here.'),
            };
            const ext = isC ? '.c' : '.py';
            let tab = 'answer';
            const tabs = document.createElement('div');
            tabs.className = 'ce-tabs';
            tabs.setAttribute('role', 'tablist');
            tabs.innerHTML = '<button type="button" role="tab" data-t="answer" aria-selected="true">answer' + ext + '</button><button type="button" role="tab" data-t="starter" aria-selected="false">starter' + ext + '</button>';
            s.work.appendChild(tabs);

            const codeBox = document.createElement('div');
            codeBox.className = 'ce-code';
            codeBox.innerHTML = '<div class="ce-code-gutter" aria-hidden="true"></div>';
            const code = document.createElement('textarea');
            code.spellcheck = false;
            code.setAttribute('aria-label', 'Code');
            const stack = document.createElement('div');
            stack.className = 'ce-code-stack';
            const hl = document.createElement('pre');
            hl.setAttribute('aria-hidden', 'true');
            stack.appendChild(hl);
            stack.appendChild(code);
            codeBox.appendChild(stack);
            s.work.appendChild(codeBox);
            const gutter = codeBox.firstChild;
            if (window.CodeHighlight) CodeHighlight.injectStyles();
            const placeholders = {
                answer: 'Your solution: a program that solves the task. It is run to check the expected output.',
                starter: 'Code already in the editor when students open it (optional).',
            };
            function paintGutter() {
                const n = code.value.split('\n').length;
                let g = '';
                for (let i = 1; i <= n; i++) g += i + '\n';
                gutter.textContent = g;
                gutter.scrollTop = code.scrollTop;
                // Trailing newline keeps the last (empty) line the same height in both layers.
                if (window.CodeHighlight) hl.innerHTML = CodeHighlight.highlight(code.value, isC ? 'c' : 'python') + '\n';
                else hl.textContent = code.value + '\n';
                hl.scrollTop = code.scrollTop;
                hl.scrollLeft = code.scrollLeft;
            }
            function showTab(t) {
                // The first call fills the still-empty box, so only keep the old tab's code when switching.
                if (code.dataset.ready) codes[tab] = code.value;
                code.dataset.ready = '1';
                tab = t;
                code.value = codes[t];
                code.placeholder = placeholders[t];
                tabs.querySelectorAll('button').forEach(function (b) { b.setAttribute('aria-selected', String(b.dataset.t === t)); });
                runBtn.textContent = t === 'answer' ? '▶ Run answer code' : '▶ Run starter code';
                paintGutter();
            }
            code.addEventListener('input', function () { codes[tab] = code.value; paintGutter(); });
            code.addEventListener('scroll', function () { gutter.scrollTop = hl.scrollTop = code.scrollTop; hl.scrollLeft = code.scrollLeft; });
            code.addEventListener('keydown', function (e) {
                if (e.key !== 'Tab' || e.shiftKey) return;
                e.preventDefault();
                code.setRangeText('    ', code.selectionStart, code.selectionEnd, 'end');
                codes[tab] = code.value;
                paintGutter();
            });
            tabs.addEventListener('click', function (e) { const b = e.target.closest('[data-t]'); if (b) showTab(b.dataset.t); });

            let stdin = null;
            if (isC) {
                const box = document.createElement('div');
                box.className = 'ce-term';
                box.style.background = '#090d1f';
                stdin = textArea('', 2, 2000, 'Input for scanf(), one value per line (only for testing here)');
                stdin.style.margin = '0';
                field(box, 'Program input', stdin);
                s.work.appendChild(box);
            }

            const bar = document.createElement('div');
            bar.className = 'ce-run-bar';
            bar.innerHTML = '<span class="ce-run-status ready">' + esc(data.language) + ' ready</span>';
            const runBtn = document.createElement('button');
            runBtn.type = 'button';
            runBtn.className = 'ce-btn go';
            bar.appendChild(runBtn);
            s.work.appendChild(bar);
            const runStatus = bar.firstChild;

            const term = document.createElement('div');
            term.className = 'ce-term';
            term.innerHTML = '<span class="ce-s-label">Terminal</span><pre>Press Run to see what the program prints.</pre>';
            s.work.appendChild(term);
            const out = term.querySelector('pre');

            const exp = document.createElement('div');
            exp.className = 'ce-expect';
            exp.innerHTML = '<div class="ce-expect-head"><label for="ce-expected">Expected output</label><span class="ce-badge">Not checked yet</span></div>';
            const useBtn = document.createElement('button');
            useBtn.type = 'button';
            useBtn.className = 'ce-btn small';
            useBtn.textContent = 'Use the answer\'s output';
            useBtn.disabled = true;
            exp.firstChild.appendChild(useBtn);
            const expected = textArea(lab.expected_output, 4, 5000, 'What the program must print. Run your answer code and press “Use the answer\'s output”, or type it.');
            expected.id = 'ce-expected';
            expected.spellcheck = false;
            exp.appendChild(expected);
            s.work.appendChild(exp);
            const badge = exp.querySelector('.ce-badge');

            let answerOutput = null;   // what the answer code printed on its last run, when that is still its code
            let answerRunOf = null;
            function paintBadge() {
                if (answerOutput === null || answerRunOf !== codes.answer) {
                    badge.className = 'ce-badge';
                    badge.textContent = 'Not checked yet';
                    useBtn.disabled = true;
                    return;
                }
                useBtn.disabled = false;
                const ok = cleanOutput(answerOutput) !== '' && cleanOutput(answerOutput) === cleanOutput(expected.value);
                badge.className = 'ce-badge ' + (ok ? 'ok' : 'bad');
                badge.textContent = ok ? '✓ Your answer code prints this' : '✗ Your answer code prints something else';
            }
            expected.addEventListener('input', paintBadge);
            code.addEventListener('input', paintBadge);
            useBtn.addEventListener('click', function () { expected.value = cleanOutput(answerOutput); paintBadge(); });

            let busy = false;
            async function run(which) {
                if (busy) return null;
                const src = which === 'answer' ? codes.answer : codes.starter;
                if (!src.trim()) {
                    out.className = 'err';
                    out.textContent = which === 'answer' ? 'Write the answer code first.' : 'There is no starter code to run.';
                    return null;
                }
                busy = true;
                runBtn.disabled = true;
                runStatus.className = 'ce-run-status';
                runStatus.textContent = isC ? 'Running…' : 'Loading Python…';
                try {
                    const result = isC ? await runC(data.worker, src, stdin ? stdin.value : '') : await runPython(src);
                    out.className = '';
                    out.textContent = result || '(no output)';
                    runStatus.className = 'ce-run-status ready';
                    runStatus.textContent = 'Done';
                    if (which === 'answer') { answerOutput = result; answerRunOf = src; paintBadge(); }
                    return result;
                } catch (e) {
                    out.className = 'err';
                    out.textContent = e.message || String(e);
                    runStatus.className = 'ce-run-status bad';
                    runStatus.textContent = 'Error';
                    if (which === 'answer') { answerOutput = null; paintBadge(); }
                    return false;
                } finally {
                    busy = false;
                    runBtn.disabled = false;
                }
            }
            runBtn.addEventListener('click', function () { run(tab); });
            showTab('answer');

            const reset = data.edited ? async function () {
                if (!confirm('Throw away your version of this lab and use the lesson\'s original one again?')) return;
                try { await api('DELETE', url); s.close(); await reload(); status('Lab reset to the original.'); } catch (e) { alert(e.message); }
            } : null;

            studioActions(s, async function () {
                codes[tab] = code.value;
                if (!title.value.trim()) throw new Error('Give the lab a title.');
                if (!instructions.value.trim()) throw new Error('Tell students what to build.');
                if (!codes.answer.trim()) { showTab('answer'); throw new Error('Write the answer code: it is run to make sure the expected output is right.'); }
                if (!expected.value.trim()) throw new Error('Add the expected output, or run your answer code and press “Use the answer\'s output”.');
                // Test first: the answer code must really print the expected output before students get it.
                if (tab !== 'answer') showTab('answer');
                const result = await run('answer');
                if (result === false) throw new Error('Your answer code stopped with an error (see the terminal). Fix it before saving.');
                if (result === null) throw new Error('The code is still running. Try again in a moment.');
                if (cleanOutput(result) !== cleanOutput(expected.value)) {
                    throw new Error('Your answer code prints something different from the expected output (see the terminal). Fix one of them, or press “Use the answer\'s output”.');
                }

                await api('PUT', url, {
                    title: title.value, difficulty: level.value, instructions: instructions.value,
                    starter_code: codes.starter, expected_output: expected.value,
                    hint_body: hintBody.value, hint_code: codes.answer,
                });
                if (showTask && showTask.checked) {
                    const next = cloneDoc();
                    next.sections[section.i] = { heading: section.s.heading, html: taskHtml(instructions.value, expected.value, section.s.html) };
                    await api('PUT', lessonUrl(cur), { title: next.title, objective: next.objective, sections: next.sections, checkpoints: next.checkpoints });
                }
                s.close();
                await reload();
                status(data.exists ? 'Lab tested and saved ✓ Students get this version now.' : 'Lab tested and added ✓ Students see a “Code it yourself” button in this lesson.');
            }, reset);
        });
    }

    // ── the simulator lab ("Configure it yourself" / "Defend it yourself": NetSim, Relay Console, Citadel Sim) ──
    async function editSimLab() {
        const cur = window.TechLab.currentLesson();
        const url = lessonUrl(cur) + '/sim-lab';
        let data;
        try { data = await api('GET', url); } catch (e) { status(e.message); return; }

        studio('Stage 5 · ' + data.simulator + ' lab', 'Edit the lab activity', function (s) {
            const intro = document.createElement('p');
            intro.className = 'ce-hint';
            intro.style.margin = '0 0 14px';
            intro.textContent = 'Change what the lab tells students to do and what it checks. ' + (data.sim === 'citadel'
                ? 'You can also rewrite the files on the lab\'s computer, but not add or remove them: if a check depends on what a file says (a category, a flag), change both together.'
                : 'The starting network stays the same, so make sure every objective can still be reached in it.') + ' New wording saves straight away; if you change what it checks, play it once with “Test this version” before saving.';
            s.side.appendChild(intro);

            // One input per check value, matching the kind of value it is.
            function valueInput(f) {
                if (f.input === 'select') return { el: selectInput(f.options, f.value), read: function (el) { return el.value; } };
                if (f.input === 'bool') return { el: selectInput(['yes', 'no'], f.value ? 'yes' : 'no'), read: function (el) { return el.value === 'yes'; } };
                if (f.input === 'number') {
                    const el = textInput(f.value, 6);
                    el.type = 'number';
                    el.min = 0;
                    return { el: el, read: function (el) { return el.value; } };
                }
                if (f.input === 'lines') return { el: textArea((f.value || []).join('\n'), 2, 3000), read: function (el) { return el.value.split('\n'); } };
                return { el: textInput(f.value, 300), read: function (el) { return el.value; } };
            }

            const main = group(s.side, 'The lab', null);
            const title = field(main, 'Title', textInput(data.title, 160));
            const blurb = data.blurb !== null ? field(main, 'Mission summary', textArea(data.blurb, 2, 500, 'One line shown under the title')) : null;
            const STORY = { alert: 'Alert line at the top', vex: 'Villain\'s taunt', victory: 'Message when every objective is clear' };
            const story = {};
            Object.keys(data.story || {}).forEach(function (k) { story[k] = field(main, STORY[k] || k, textArea(data.story[k], 2, 1000)); });

            const steps = data.steps.map(function (step, i) {
                const g = group(s.side, 'Step ' + (i + 1), i === 0 ? 'Instructions are plain text. Put commands in `backticks` to show them as code.' : null);
                const st = { title: field(g, 'Step title', textInput(step.title, 160)), body: field(g, 'Instructions', textArea(step.body, 5, 5000)), objectives: [] };
                step.objectives.forEach(function (o, k) {
                    const box = document.createElement('div');
                    box.className = 'ce-obj';
                    box.innerHTML = '<p class="ce-obj-kind">Objective ' + (k + 1) + ' · checks ' + esc(o.kind.replace(/-/g, ' ')) + '</p>';
                    g.appendChild(box);
                    const ob = { label: field(box, 'What students see', textInput(o.label, 200)), values: [] };
                    if (o.fields.length) {
                        const grid = document.createElement('div');
                        grid.className = 'ce-obj-fields';
                        box.appendChild(grid);
                        o.fields.forEach(function (f) {
                            const v = valueInput(f);
                            field(grid, f.label, v.el);
                            ob.values.push({ path: f.path, el: v.el, read: v.read });
                        });
                    } else {
                        const n = document.createElement('p');
                        n.className = 'ce-hint';
                        n.style.margin = '0 0 10px';
                        n.textContent = 'The simulator checks this one itself (a command that must be run), so only its wording can change.';
                        box.appendChild(n);
                    }
                    st.objectives.push(ob);
                });
                return st;
            });

            // Citadel Sim: the text files students read on the lab's computer (`type <file>`).
            const files = {};
            if (data.files && data.files.length) {
                const g = group(s.side, 'Files on the lab\'s computer', 'What students see when they open each file. Keep names, codes and answers the checks above look for.');
                data.files.forEach(function (f) {
                    const el = textArea(f.content, Math.min(12, Math.max(3, f.content.split('\n').length + 1)), 20000);
                    el.classList.add('ce-mono');
                    el.style.fontFamily = "'Space Mono', monospace";
                    el.spellcheck = false;
                    files[f.path] = field(g, f.path, el);
                });
            }

            function body() {
                const b = {
                    title: title.value,
                    steps: steps.map(function (st) {
                        return {
                            title: st.title.value,
                            body: st.body.value,
                            objectives: st.objectives.map(function (ob) {
                                const values = {};
                                ob.values.forEach(function (v) { values[v.path] = v.read(v.el); });
                                return { label: ob.label.value, values: values };
                            }),
                        };
                    }),
                };
                if (blurb) b.blurb = blurb.value;
                b.story = {};
                Object.keys(story).forEach(function (k) { b.story[k] = story[k].value; });
                b.files = {};
                Object.keys(files).forEach(function (p) { b.files[p] = files[p].value; });
                return b;
            }

            // Work: the simulator, playing the draft like a student would.
            const bar = document.createElement('div');
            bar.className = 'ce-sim-bar';
            bar.innerHTML = '<span class="ce-sim-msg">Play your version before students get it.</span>';
            const testBtn = document.createElement('button');
            testBtn.type = 'button';
            testBtn.className = 'ce-btn go';
            testBtn.textContent = '▶ Test this version';
            bar.appendChild(testBtn);
            s.work.appendChild(bar);
            const msg = bar.firstChild;
            const empty = document.createElement('div');
            empty.className = 'ce-sim-empty';
            empty.innerHTML = '<div><strong>Try it like a student</strong>Press “Test this version” to open your lab in ' + esc(data.simulator) + '.<br>Clear every objective once: if you changed what the lab checks, that is how you know students can pass it.</div>';
            s.work.appendChild(empty);

            let frame = null;
            let draft = null;        // {token, file} of the version being played
            let tested = null;       // token of a draft played through, while the form still matches it
            let sent = '';           // the form as it was when the draft was made
            let saveKeyUser = null;  // the simulator's autosave id for this test (cleared afterwards)

            function dropAutosave() {
                if (!saveKeyUser) return;
                try {
                    Object.keys(localStorage).forEach(function (k) { if (k.endsWith(':u:' + saveKeyUser)) localStorage.removeItem(k); });
                } catch (e) { /* storage blocked */ }
            }
            s.onClose(dropAutosave);

            function say(text, ok) { msg.textContent = text; msg.className = 'ce-sim-msg' + (ok ? ' ok' : ''); }

            s.side.addEventListener('input', function () {
                if (draft && JSON.stringify(body()) !== sent) {
                    tested = null;
                    say('You changed the lab since this test. Press “Test this version” again to play the new one.', false);
                }
            });

            testBtn.addEventListener('click', async function () {
                testBtn.disabled = true;
                try {
                    const b = body();
                    draft = await api('POST', url + '/test', b);
                    sent = JSON.stringify(b);
                    tested = null;
                    dropAutosave();
                    saveKeyUser = String(Date.now());
                    if (!frame) {
                        frame = document.createElement('iframe');
                        frame.className = 'ce-sim-frame';
                        frame.title = data.simulator + ' test of this lab';
                        frame.allow = 'clipboard-read; clipboard-write';
                        empty.replaceWith(frame);
                    }
                    frame.src = data.frame + '?lab=' + encodeURIComponent(data.lab) + '&u=' + saveKeyUser + '&file=' + encodeURIComponent(draft.file);
                    say('Testing your version. Clear every objective to pass it.', false);
                } catch (e) {
                    say(e.message, false);
                } finally {
                    testBtn.disabled = false;
                }
            });

            function onMessage(e) {
                if (!frame || e.origin !== window.location.origin || e.source !== frame.contentWindow) return;
                const m = e.data;
                if (!m || m.source !== data.source || m.lab !== data.lab || !draft) return;
                if (m.type === 'lab-progress') say('Objectives cleared: ' + m.passed + ' of ' + m.total + '.', false);
                else if (m.type === 'lab-failed') say('Not yet: ' + (m.failing || []).slice(0, 2).join(' · '), false);
                else if (m.type === 'lab-passed') {
                    const token = draft.token;
                    api('POST', draft.file.replace(/\.json$/, '/passed'), {}).then(function () {
                        if (!draft || draft.token !== token || JSON.stringify(body()) !== sent) return;
                        tested = token;
                        say('✓ You cleared every objective. This version can be passed: save it.', true);
                    }).catch(function (err) { say(err.message, false); });
                }
            }
            window.addEventListener('message', onMessage);
            s.onClose(function () { window.removeEventListener('message', onMessage); });

            const reset = data.edited ? async function () {
                if (!confirm('Throw away your version of this lab and use the original one again?')) return;
                try { await api('DELETE', url); s.close(); status('Lab reset to the original.'); } catch (e) { alert(e.message); }
            } : null;

            studioActions(s, async function () {
                if (!title.value.trim()) throw new Error('Give the lab a title.');
                const b = body();
                if (tested && JSON.stringify(b) === sent) b.draft = tested;
                try {
                    await api('PUT', url, b);
                } catch (e) {
                    if (/Test this version/.test(e.message)) testBtn.focus();
                    throw e;
                }
                s.close();
                status('Lab saved ✓ Students get this version the next time they open the lab.');
            }, reset);
        });
    }

    // ── modal ─────────────────────────────────────────────────────────────
    function modal(title, intro, build) {
        const back = document.createElement('div');
        back.className = 'ce-modal-back';
        const box = document.createElement('div');
        box.className = 'ce-modal';
        box.setAttribute('role', 'dialog');
        box.setAttribute('aria-modal', 'true');
        box.innerHTML = '<h3>' + esc(title) + '</h3><p>' + esc(intro) + '</p>';
        back.appendChild(box);
        function close() { back.remove(); document.removeEventListener('keydown', onKey); }
        function onKey(e) { if (e.key === 'Escape') close(); }
        document.addEventListener('keydown', onKey);
        back.addEventListener('mousedown', function (e) { if (e.target === back) close(); });
        build(box, close);
        document.body.appendChild(back);
        const first = box.querySelector('input, textarea, select, [contenteditable]');
        if (first) first.focus();
    }

    // ── decorate a rendered lesson ────────────────────────────────────────
    function pen(label, onClick) {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'ce-btn small ce-pen';
        b.innerHTML = '✏️ ' + esc(label);
        b.addEventListener('click', onClick);
        return b;
    }

    async function decorate(cur) {
        const frag = stage.querySelector('.lesson-fragment');
        if (!frag) return;
        const key = cur.module + '/' + cur.lesson;

        const bar = document.createElement('div');
        bar.className = 'ce-bar';
        bar.innerHTML = '<strong>✏️ Editing mode</strong><span>Changes go live for your students as soon as you save.</span><span class="ce-status" aria-live="polite">Loading…</span>';
        frag.prepend(bar);

        try {
            doc = await api('GET', lessonUrl(cur));
            loaded = key;
        } catch (e) {
            status(e.message);
            return;
        }
        if (!stage.contains(bar)) return;   // another lesson was opened meanwhile

        const tools = document.createElement('span');
        tools.style.display = 'contents';
        const stagesBtn = document.createElement('button');
        stagesBtn.type = 'button';
        stagesBtn.className = 'ce-btn small';
        stagesBtn.textContent = '☰ Stages';
        stagesBtn.title = 'Add, rename, reorder or remove stages';
        stagesBtn.addEventListener('click', editStages);
        tools.appendChild(stagesBtn);
        if (doc.edited) {
            const reset = document.createElement('button');
            reset.type = 'button';
            reset.className = 'ce-btn small danger';
            reset.textContent = doc.custom ? 'Start over' : 'Reset lesson to default';
            reset.addEventListener('click', async function () {
                if (!confirm(doc.custom ? 'Clear everything you wrote in this lesson?' : 'Throw away your changes to this lesson page and show the original again? (The quiz is reset separately.)')) return;
                try { await api('DELETE', lessonUrl(cur)); await reload(); status('Lesson reset.'); } catch (e) { status(e.message); }
            });
            tools.appendChild(reset);
        }
        bar.insertBefore(tools, bar.querySelector('.ce-status'));
        status(doc.edited ? 'You have edited this lesson.' : 'This is the original lesson.');

        const hero = frag.querySelector('.quest-hero');
        if (hero) hero.appendChild(pen('Edit', editHero));

        frag.querySelectorAll('h2[data-sec]').forEach(function (h2) {
            // The quiz section is just the questions: "Edit quiz" (below) is its one, Google-Forms-style
            // door in. The raw text editor doesn't apply there, so it doesn't get this pencil too.
            if (h2.closest('.quest-card[data-kind=quiz]')) return;
            const b = pen('Edit', function () { editSection(h2); });
            if (h2.classList.contains('quest-sub')) {
                h2.appendChild(b);
            } else {
                const head = h2.closest('.quest-head');
                (head || h2.parentNode).appendChild(b);
            }
        });

        frag.querySelectorAll('.checkpoint-block[data-cp]').forEach(function (block) {
            const b = pen('Edit checkpoint', function () { editPageCheckpoint(block); });
            b.classList.add('ce-cp-pen');
            block.prepend(b);
        });

        // Stage 5 is the lab: its pencil opens the lab studio, and the stage's page text keeps a smaller one.
        function labStage(label, onClick) {
            const card = frag.querySelector('.quest-card[data-kind=practice], .quest-card[data-kind=lab]');
            const head = card && card.querySelector('.quest-head');
            if (!head) return;
            const textPen = head.querySelector(':scope > .ce-pen');
            if (textPen) textPen.textContent = '✏️ Edit text';
            const b = pen(label, onClick);
            b.classList.add('primary');
            head.insertBefore(b, textPen);
        }

        if (LABS) {
            const launch = frag.querySelector('form[action*="/editor/launch"]');
            labStage(launch ? 'Edit lab' : 'Add coding lab', editLab);
            if (launch) {
                const b = pen('Edit lab', editLab);
                b.classList.add('ce-lab-pen');
                (launch.closest('.cta-wrap') || launch).appendChild(b);
            } else {
                const add = document.createElement('button');
                add.type = 'button';
                add.className = 'ce-btn small';
                add.textContent = '+ Coding lab';
                add.title = 'Add a "Code it yourself" lab to this lesson';
                add.addEventListener('click', editLab);
                tools.appendChild(add);
            }
        }

        if (SIMLABS) {
            const sim = frag.querySelector('a.code-btn[href*="/lab/"]');
            if (sim) {
                labStage('Edit lab', editSimLab);
                const b = pen('Edit lab', editSimLab);
                b.classList.add('ce-lab-pen');
                (sim.closest('.cta-wrap') || sim.parentNode).appendChild(b);
            }
        }

        frag.querySelectorAll('.quest-card[data-kind=quiz]').forEach(function (card) {
            const head = card.querySelector('.quest-head');
            if (head) head.appendChild(pen('Edit quiz', function () { editQuiz(card); }));
        });
    }

    document.addEventListener('techlab:lesson-rendered', function (e) { decorate(e.detail); });

    // ── the sidebar outline ───────────────────────────────────────────────
    function editOutline() {
        api('GET', BASE + '/outline').then(function (data) {
            const outline = data.outline.map(function (m) {
                return { key: m.key, title: m.title, hidden: m.hidden, custom: m.custom, lessons: m.lessons.map(function (l) { return { key: l.key, title: l.title, hidden: l.hidden, custom: l.custom }; }) };
            });
            modal('Course outline', 'Rename, reorder, hide or add modules and lessons. Hidden ones disappear for students but keep their progress, so you can bring them back. New lessons start with empty stages for you to fill in.', function (box, close) {
                const holder = document.createElement('div');
                box.appendChild(holder);

                function rowFor(item, list, i, isModule) {
                    const row = document.createElement('div');
                    row.className = 'ce-row' + (item.hidden ? ' is-hidden' : '');
                    const input = document.createElement('input');
                    input.className = 'ce-input';
                    input.value = item.title;
                    input.maxLength = 120;
                    input.style.fontWeight = isModule ? '700' : '500';
                    input.addEventListener('input', function () { item.title = input.value; });
                    row.appendChild(input);
                    if (item.custom || !item.key) row.insertAdjacentHTML('beforeend', '<span class="ce-tag">NEW</span>');
                    [['↑', i > 0, function () { list.splice(i - 1, 0, list.splice(i, 1)[0]); paint(); }],
                     ['↓', i < list.length - 1, function () { list.splice(i + 1, 0, list.splice(i, 1)[0]); paint(); }],
                     [item.hidden ? 'Restore' : (item.key ? 'Hide' : 'Remove'), true, function () {
                         if (!item.key) { list.splice(i, 1); paint(); return; }
                         item.hidden = !item.hidden; paint();
                     }]].forEach(function (b) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'ce-btn small' + (/Hide|Remove/.test(b[0]) ? ' danger' : '');
                        btn.textContent = b[0];
                        btn.disabled = !b[1];
                        btn.addEventListener('click', b[2]);
                        row.appendChild(btn);
                    });
                    return row;
                }

                function paint() {
                    holder.innerHTML = '';
                    outline.forEach(function (m, i) {
                        const mod = document.createElement('div');
                        mod.className = 'ce-module';
                        mod.appendChild(rowFor(m, outline, i, true));
                        const lessons = document.createElement('div');
                        lessons.className = 'ce-lessons';
                        m.lessons.forEach(function (l, j) { lessons.appendChild(rowFor(l, m.lessons, j, false)); });
                        const add = document.createElement('button');
                        add.type = 'button';
                        add.className = 'ce-btn small';
                        add.textContent = '+ Add lesson';
                        add.addEventListener('click', function () { m.lessons.push({ key: null, title: 'New lesson', hidden: false }); paint(); });
                        lessons.appendChild(add);
                        mod.appendChild(lessons);
                        holder.appendChild(mod);
                    });
                    const addModule = document.createElement('button');
                    addModule.type = 'button';
                    addModule.className = 'ce-btn';
                    addModule.textContent = '+ Add module';
                    addModule.addEventListener('click', function () {
                        outline.push({ key: null, title: 'M' + (outline.length + 1) + ' — New module', hidden: false, lessons: [{ key: null, title: 'New lesson', hidden: false }] });
                        paint();
                    });
                    holder.appendChild(addModule);
                }
                paint();

                const row = actions(async function () {
                    await api('PUT', BASE + '/outline', { outline: outline.map(function (m) {
                        return { key: m.key, title: m.title.trim(), hidden: !!m.hidden, lessons: m.lessons.map(function (l) { return { key: l.key, title: l.title.trim(), hidden: !!l.hidden }; }) };
                    }) });
                    location.reload();
                }, close);
                const reset = document.createElement('button');
                reset.type = 'button';
                reset.className = 'ce-btn danger';
                reset.textContent = 'Reset outline';
                reset.title = 'Back to the course\'s original modules and lessons';
                reset.addEventListener('click', async function () {
                    if (!confirm('Bring back the original modules and lessons? Lessons you added are hidden (their pages are kept).')) return;
                    try { await api('DELETE', BASE + '/outline'); location.reload(); } catch (e) { alert(e.message); }
                });
                row.insertBefore(reset, row.querySelector('[data-a=cancel]'));
                box.appendChild(row);
            });
        }).catch(function (e) { alert(e.message); });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const outline = document.querySelector('.programming-sidebar .course-outline');
        if (!outline) return;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'ce-btn ce-outline-btn';
        btn.dataset.tour = 'ce-outline';   // Astro's captain tour points here
        btn.textContent = '✏️ Edit modules & lessons';
        btn.addEventListener('click', editOutline);
        outline.parentNode.insertBefore(btn, outline);
    });
})();
</script>
