@php
    $labConfig = [
        'lab' => $lab,
        'source' => $sim['source'],
        'completeUrl' => $completeUrl,
        'lessonUrl' => $lessonUrl,
        'nextUrl' => $nextUrl,
        'xp' => \App\Services\StudentDashboardService::XP_PER_LESSON,
        'alreadyDone' => $alreadyDone,
        'labHelpStatusUrl' => $labHelpStatusUrl,
        'labHelpUnlockUrl' => $labHelpUnlockUrl,
        'labHelpAskUrl' => $labHelpAskUrl,
        'simHint' => $sim['hint'],
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lesson {{ $number }}: {{ $title }} · {{ $sim['name'] }} · TechLab</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <meta name="theme-color" content="#06061a">

    <style>
        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            display: flex;
            flex-direction: column;
            background: #06061a;
            color: #eaf0ff;
            font-family: 'Inter', system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* Slim header: everything else on the screen belongs to the simulator. */
        .lab-bar {
            flex: none;
            display: flex;
            align-items: center;
            gap: 16px;
            min-height: 56px;
            padding: 8px 18px;
            background: linear-gradient(180deg, #14204a 0%, #0c1436 100%);
            border-bottom: 1px solid rgba(255, 255, 255, .1);
        }
        .lab-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, .16);
            background: rgba(255, 255, 255, .06);
            color: #eaf0ff;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            white-space: nowrap;
            transition: background .2s ease, border-color .2s ease;
        }
        .lab-back:hover { background: rgba(255, 255, 255, .14); border-color: rgba(115, 182, 255, .6); }
        .lab-back:focus-visible, .lab-next:focus-visible { outline: 2px solid #73b6ff; outline-offset: 2px; }

        .lab-heading { min-width: 0; }
        .lab-kicker { font-size: 11px; letter-spacing: .16em; text-transform: uppercase; color: #73b6ff; font-weight: 700; }
        .lab-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lab-status {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 14px;
            border-radius: 12px;
            background: rgba(115, 182, 255, .12);
            border: 1px solid rgba(115, 182, 255, .3);
            font-weight: 600;
            font-size: 14px;
            min-height: 40px;
        }
        .lab-status.passed { background: rgba(47, 165, 107, .18); border-color: rgba(47, 165, 107, .55); color: #b8f2d2; }
        .lab-next {
            padding: 8px 14px;
            border-radius: 9px;
            background: #2f6fe0;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            white-space: nowrap;
        }
        .lab-next:hover { background: #2559b8; }

        .lab-frame { flex: 1 1 auto; min-height: 0; display: block; width: 100%; border: 0; background: #0b1226; }

        @media (max-width: 720px) {
            .lab-bar { flex-wrap: wrap; }
            .lab-status { margin-left: 0; width: 100%; }
        }
        @media (prefers-reduced-motion: reduce) { .lab-back { transition: none; } }
    </style>
</head>
<body>

<header class="lab-bar">
    <a class="lab-back" href="{{ $lessonUrl }}" title="Back to the lesson">
        <span aria-hidden="true">←</span> Back to lesson
    </a>

    <div class="lab-heading">
        <div class="lab-kicker">{{ $sim['name'] }} · Lesson {{ $number }}</div>
        <div class="lab-title">{{ $title }}</div>
    </div>

    <div class="lab-status {{ $alreadyDone ? 'passed' : '' }}" id="lab-status" role="status" aria-live="polite">
        @if ($alreadyDone)
            <span>Lab complete. You can keep experimenting.</span>
            @if ($nextUrl)
                <a class="lab-next" href="{{ $nextUrl }}">Next lesson →</a>
            @endif
        @else
            <span>{!! $sim['hint'] !!}</span>
        @endif
    </div>
</header>

<iframe
    class="lab-frame"
    id="lab-frame"
    src="{{ asset($sim['path']) }}?lab={{ $lab }}&u={{ auth()->id() }}"
    title="{{ $sim['name'] }} simulator for lesson {{ $number }}"
    allow="clipboard-read; clipboard-write"
></iframe>

<script>
(function () {
    const CFG = @json($labConfig);
    const frame = document.getElementById('lab-frame');
    const status = document.getElementById('lab-status');
    let csrf = document.querySelector('meta[name="csrf-token"]').content;
    let done = CFG.alreadyDone;
    let posting = false;

    const sleep = function (ms) { return new Promise(function (resolve) { setTimeout(resolve, ms); }); };

    function say(text, passed) {
        status.className = 'lab-status' + (passed ? ' passed' : '');
        status.textContent = text;
    }

    function showPassed(next) {
        status.className = 'lab-status passed';
        status.innerHTML = '';
        const msg = document.createElement('span');
        msg.innerHTML = 'Signal received. Lab complete <strong>+' + CFG.xp + ' XP</strong>';
        status.appendChild(msg);
        const target = next || CFG.nextUrl;
        if (target) {
            const a = document.createElement('a');
            a.className = 'lab-next';
            a.href = target;
            a.textContent = 'Next lesson \u2192';
            status.appendChild(a);
        }
    }

    function showFailed(reason) {
        status.className = 'lab-status';
        status.innerHTML = '';
        const msg = document.createElement('span');
        msg.textContent = reason === 'HTTP 401'
            ? 'Your session ended, so this pass was not saved. Log in again, then reopen the lab.'
            : 'Your lab is solved, but saving it failed (' + reason + ').';
        status.appendChild(msg);
        if (reason !== 'HTTP 401') {
            const retry = document.createElement('button');
            retry.type = 'button';
            retry.className = 'lab-next';
            retry.textContent = 'Try again';
            retry.onclick = finish;
            status.appendChild(retry);
        }
    }

    // A long-open or restored page can hold an old CSRF token. The server hands out a fresh one with the page.
    async function freshToken() {
        const r = await fetch(window.location.href, { credentials: 'same-origin', cache: 'no-store', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!r.ok) throw new Error('HTTP ' + r.status);
        const m = (await r.text()).match(/name="csrf-token" content="([^"]+)"/);
        if (!m) throw new Error('no token');
        return m[1];
    }

    // Authenticated JSON fetch with CSRF-refresh-and-retry, shared by lab-complete and the Ask-Astro
    // bridge below. Retries only a network error or an expired token; a real API error (402/403/422...)
    // comes straight back with its body so the caller can show the server's own message.
    async function authFetch(url, method, body) {
        let reason = 'network error';
        for (let attempt = 0; attempt < 3; attempt++) {
            try {
                const r = await fetch(url, {
                    method: method,
                    credentials: 'same-origin',
                    headers: Object.assign(
                        { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                        body !== undefined ? { 'Content-Type': 'application/json' } : {}
                    ),
                    body: body !== undefined ? JSON.stringify(body) : undefined,
                });
                let data = null;
                try { data = await r.json(); } catch (e) { /* unreadable/empty body */ }
                if (r.ok) return { ok: true, status: r.status, data: data };
                if (r.status === 419) { csrf = await freshToken(); continue; }
                return { ok: false, status: r.status, data: data, reason: 'HTTP ' + r.status };
            } catch (e) {
                reason = (e && e.message) ? e.message : reason;
            }
            await sleep(700 * (attempt + 1));
        }
        return { ok: false, status: 0, data: null, reason: reason };
    }

    // Saving is idempotent on the server, so retrying is always safe.
    async function save() {
        const res = await authFetch(CFG.completeUrl, 'POST', { lab: CFG.lab });
        return res.ok ? { ok: true, next: res.data && res.data.next } : { ok: false, reason: res.reason };
    }

    // Ask-Astro bridge: the citadel-sim iframe has the lab state (mission + transcript) but no Laravel
    // session; this page has the session but no lab state. Each request/response pair carries the sim's
    // own reqId so the iframe can match a reply to the call that triggered it.
    function postToFrame(type, extra) {
        frame.contentWindow.postMessage(Object.assign({ source: CFG.source, lab: CFG.lab, type: type }, extra), window.location.origin);
    }

    async function relay(url, method, body) {
        if (!url) return { ok: false, data: null, error: 'Ask Astro is not available for this lab.' };
        const res = await authFetch(url, method, body);

        return { ok: res.ok, data: res.data, error: (res.data && res.data.error) || (!res.ok ? "Astro couldn't respond — try again." : null) };
    }

    async function finish() {
        if (posting) return;
        posting = true;
        say('Saving your progress...', false);
        const res = await save();
        posting = false;
        if (res.ok) { done = true; showPassed(res.next); } else { showFailed(res.reason); }
    }

    // The simulator (same origin) reports progress with postMessage.
    window.addEventListener('message', function (e) {
        if (e.origin !== window.location.origin || e.source !== frame.contentWindow) return;
        const m = e.data;
        if (!m || m.source !== CFG.source || m.lab !== CFG.lab) return;

        if (m.type === 'lab-progress' && !done && !posting) {
            say('Objectives passed: ' + m.passed + ' of ' + m.total + '. Keep going, Captain.', false);
        } else if (m.type === 'lab-failed' && !done && !posting) {
            say('Not yet: ' + m.failing.slice(0, 2).join(' \u00b7 '), false);
        } else if (m.type === 'lab-passed' && !done) {
            finish();
        } else if (m.type === 'astro-status-request') {
            relay(CFG.labHelpStatusUrl, 'GET').then(function (res) {
                postToFrame('astro-status-response', Object.assign({ reqId: m.reqId, simHint: CFG.simHint }, res));
            });
        } else if (m.type === 'astro-unlock-request') {
            relay(CFG.labHelpUnlockUrl, 'POST', {}).then(function (res) {
                postToFrame('astro-unlock-response', Object.assign({ reqId: m.reqId, simHint: CFG.simHint }, res));
            });
        } else if (m.type === 'astro-ask-request') {
            relay(CFG.labHelpAskUrl, 'POST', Object.assign({ lab: CFG.lab }, m.payload)).then(function (res) {
                postToFrame('astro-ask-response', Object.assign({ reqId: m.reqId }, res));
            });
        }
    });
})();
</script>

</body>
</html>
