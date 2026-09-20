@php
    $labConfig = [
        'lab' => $lab,
        'completeUrl' => $completeUrl,
        'lessonUrl' => $lessonUrl,
        'nextUrl' => $nextUrl,
        'xp' => \App\Services\StudentDashboardService::XP_PER_LESSON,
        'alreadyDone' => $alreadyDone,
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lesson {{ $number }}: {{ $title }} · Relay Lab · TechLab</title>
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
        <div class="lab-kicker">Relay Lab · Lesson {{ $number }}</div>
        <div class="lab-title">{{ $title }}</div>
    </div>

    <div class="lab-status {{ $alreadyDone ? 'passed' : '' }}" id="lab-status" role="status" aria-live="polite">
        @if ($alreadyDone)
            <span>Lab complete. You can keep experimenting.</span>
            @if ($nextUrl)
                <a class="lab-next" href="{{ $nextUrl }}">Next lesson →</a>
            @endif
        @else
            <span>Press <strong>Check objectives</strong> in the simulator when you are ready.</span>
        @endif
    </div>
</header>

<iframe
    class="lab-frame"
    id="lab-frame"
    src="{{ asset('netsim-app/app.html') }}?lab={{ $lab }}"
    title="Relay Lab simulator for lesson {{ $number }}"
    allow="clipboard-read; clipboard-write"
></iframe>

<script>
(function () {
    const CFG = @json($labConfig);
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const frame = document.getElementById('lab-frame');
    const status = document.getElementById('lab-status');
    let done = CFG.alreadyDone;
    let posting = false;

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
            a.textContent = 'Next lesson →';
            status.appendChild(a);
        }
    }

    // The simulator (/netsim-app, same origin) reports progress with postMessage.
    window.addEventListener('message', function (e) {
        if (e.origin !== window.location.origin || e.source !== frame.contentWindow) return;
        const m = e.data;
        if (!m || m.source !== 'netsim' || m.lab !== CFG.lab) return;

        if (m.type === 'lab-progress' && !done) {
            say('Objectives passed: ' + m.passed + ' of ' + m.total + '. Keep going, Captain.', false);
        } else if (m.type === 'lab-failed' && !done) {
            say('Not yet: ' + m.failing.slice(0, 2).join(' · '), false);
        } else if (m.type === 'lab-passed' && !done && !posting) {
            posting = true;
            fetch(CFG.completeUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ lab: CFG.lab }),
            }).then(function (r) { return r.ok ? r.json() : Promise.reject(r); }).then(function (data) {
                done = true;
                showPassed(data.next);
            }).catch(function () {
                say('The lab passed, but saving your progress failed. Press Check objectives again.', false);
            }).finally(function () { posting = false; });
        }
    });
})();
</script>

</body>
</html>
