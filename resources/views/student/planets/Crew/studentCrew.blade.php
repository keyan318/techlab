<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Course · TechLab</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />

  <style>
    :root {
      --void: #06061a; --cosmic: #120a33; --cosmic-2: #1e1259;
      --blue: #73b6ff; --violet: #9b6bff; --cyan: #5be1ff; --green: #7cffb2;
      --red: #ff9bb0;
      --text: #eaeeff; --muted: #98a2d4;
      --glass: rgba(123, 142, 220, 0.07); --glass-border: rgba(150, 170, 255, 0.18);
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { font-family: 'Inter', system-ui, sans-serif; color: var(--text); background: var(--void);
      overflow-x: hidden; -webkit-font-smoothing: antialiased; line-height: 1.6; }

    /* ---------- Space background ---------- */
    .space { position: fixed; inset: 0; z-index: -1; overflow: hidden;
      background:
        radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%),
        radial-gradient(100% 80% at 85% 110%, #1a0f4d 0%, rgba(26,15,77,0) 60%),
        linear-gradient(160deg, #0a0826 0%, #120a33 45%, #1e1259 100%); }
    .glow { position: absolute; border-radius: 50%; filter: blur(70px); opacity: .5; animation: pulse 9s ease-in-out infinite; }
    .glow.g1 { width: 460px; height: 460px; top: -120px; left: -80px; background: radial-gradient(circle, rgba(155,107,255,.7), transparent 70%); }
    .glow.g2 { width: 520px; height: 520px; bottom: -160px; right: -120px; background: radial-gradient(circle, rgba(91,225,255,.4), transparent 70%); animation-delay: -4s; }
    @keyframes pulse { 0%,100% { transform: scale(1); opacity: .45; } 50% { transform: scale(1.12); opacity: .65; } }
    .star { position: absolute; width: 2px; height: 2px; border-radius: 50%; background: #fff; opacity: .8; animation: twinkle var(--dur,4s) ease-in-out infinite; animation-delay: var(--delay,0s); }
    @keyframes twinkle { 0%,100% { opacity: .15; transform: scale(.7); } 50% { opacity: 1; transform: scale(1.2); } }
    .planet { position: absolute; border-radius: 50%; animation: float 14s ease-in-out infinite; }
    .planet::after { content: ""; position: absolute; inset: 0; border-radius: 50%; box-shadow: inset -18px -18px 40px rgba(0,0,0,.45); }
    .p1 { width: 200px; height: 200px; top: 12%; right: 5%; background: radial-gradient(circle at 35% 30%, #b9a3ff, #6c4bd6 55%, #2e1a73); box-shadow: 0 0 80px rgba(124,75,214,.5); }
    .p2 { width: 110px; height: 110px; bottom: 10%; left: 6%; background: radial-gradient(circle at 35% 30%, #aef0ff, #4fc8ee 55%, #1d6f99); box-shadow: 0 0 60px rgba(91,225,255,.4); animation-direction: reverse; }
    @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-24px); } }

    a { color: inherit; text-decoration: none; }

    /* ---------- App shell (joined) ---------- */
    .app { display: flex; align-items: flex-start; min-height: 100vh; }

    /* Sidebar */
    .sidebar { width: 250px; flex: 0 0 250px; position: sticky; top: 0; height: 100vh; padding: 22px 18px;
      border-right: 1px solid var(--glass-border); background: rgba(10,12,40,0.5); backdrop-filter: blur(12px);
      display: flex; flex-direction: column; }
    .brand { display: flex; align-items: center; gap: 12px; padding: 4px 8px 22px; }
    .brand svg { width: 32px; height: 32px; filter: drop-shadow(0 0 10px rgba(115,182,255,.5)); }
    .brand .name { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.2rem; }
    .brand .name span { color: var(--blue); }
    .nav { display: flex; flex-direction: column; gap: 4px; }
    .nav a { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 12px; color: var(--muted);
      font-weight: 600; font-size: .95rem; transition: all .2s; }
    .nav a svg { width: 20px; height: 20px; flex: 0 0 20px; }
    .nav a:hover { color: var(--text); background: var(--glass); }
    .nav a.active { color: #07142e; background: linear-gradient(100deg, var(--cyan), var(--blue) 60%, var(--violet)); box-shadow: 0 8px 24px rgba(115,182,255,.3); }
    .nav a.active svg { stroke: #07142e; }
    .side-foot { margin-top: auto; }
    .side-foot form { margin: 0; }
    .nav-cta { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: .9rem; padding: 11px 18px; border-radius: 999px; width: 100%;
      background: rgba(115,182,255,0.12); border: 1px solid var(--glass-border); color: var(--text); cursor: pointer; text-align: center; transition: all .25s; }
    .nav-cta:hover { background: rgba(115,182,255,0.22); }

    /* Main */
    .main { flex: 1 1 auto; min-width: 0; padding: 30px clamp(18px, 3vw, 44px) 60px; }

    /* Course header */
    .course-head { background: linear-gradient(120deg, rgba(115,182,255,.10), rgba(155,107,255,.08));
      border: 1px solid var(--glass-border); border-radius: 24px; padding: 30px 34px; margin-bottom: 36px; }
    .course-head .eyebrow { font-family: 'Space Mono', monospace; font-size: .72rem; letter-spacing: .28em; text-transform: uppercase; color: var(--cyan); }
    .course-head h1 { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: clamp(1.7rem, 3.5vw, 2.5rem); letter-spacing: -.02em; margin: 10px 0 8px; }
    .course-head .ch-meta { color: var(--muted); font-size: .95rem; }
    .course-head .ch-meta strong { color: var(--text); }
    .course-head .ch-desc { color: var(--muted); margin-top: 10px; max-width: 70ch; }

    /* Section scaffolding */
    .section { margin-bottom: 44px; scroll-margin-top: 24px; }
    .section-head { margin-bottom: 18px; }
    .section-head h2 { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: clamp(1.4rem, 3vw, 1.9rem); letter-spacing: -.01em; }
    .section-head p { color: var(--muted); margin-top: 4px; font-size: .95rem; }

    .card { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 20px; padding: 22px 24px; backdrop-filter: blur(12px); }

    /* Module grid */
    .module-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 18px; }
    .module { display: flex; flex-direction: column; gap: 10px; transition: transform .25s, border-color .25s, box-shadow .25s; }
    .module:hover { transform: translateY(-4px); border-color: var(--blue); box-shadow: 0 20px 48px rgba(115,182,255,.22); }
    .tag { font-family: 'Space Mono', monospace; font-size: .7rem; letter-spacing: .14em; text-transform: uppercase; color: var(--cyan); }
    .module h3 { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.18rem; }
    .module .m-desc { color: var(--muted); font-size: .9rem; }

    .files { list-style: none; display: grid; gap: 8px; margin: 6px 0; }
    .file { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 12px; background: rgba(10,12,40,.4); border: 1px solid var(--glass-border);
      cursor: pointer; transition: background .2s, border-color .2s; }
    .file:hover { background: rgba(115,182,255,.10); border-color: var(--blue); }
    .file-ico { width: 40px; height: 40px; flex: 0 0 40px; border-radius: 10px; display: grid; place-items: center;
      font-family: 'Space Mono', monospace; font-size: .62rem; font-weight: 700; letter-spacing: .04em; }
    .file-name { flex: 1 1 auto; font-size: .9rem; font-weight: 500; }
    .file-go { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: .82rem; color: var(--blue); white-space: nowrap; }
    .file[data-ext="pdf"]  .file-ico { background: rgba(255,99,132,.16);  color: #ff9bb0; }
    .file[data-ext="pptx"] .file-ico { background: rgba(255,159,61,.16);  color: #ffc078; }
    .file[data-ext="docx"] .file-ico { background: rgba(115,182,255,.16); color: var(--blue); }
    .file[data-ext="xlsx"] .file-ico { background: rgba(124,255,178,.16); color: var(--green); }
    .file[data-ext="zip"]  .file-ico { background: rgba(155,107,255,.16); color: var(--violet); }
    .file[data-ext="img"]  .file-ico { background: rgba(91,225,255,.16);  color: var(--cyan); }

    .card-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: auto; padding-top: 12px; flex-wrap: wrap; }
    .count { font-family: 'Space Mono', monospace; font-size: .8rem; color: var(--muted); }
    .cta { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: .9rem; color: var(--blue); transition: gap .2s; }
    .cta:hover { color: #fff; }

    /* Two-column split: quizzes + labs */
    .split { display: grid; grid-template-columns: 1fr 1fr; gap: 26px; }
    .split .col { display: flex; flex-direction: column; gap: 16px; }
    .ql-grid { display: grid; gap: 16px; }

    .ql { transition: transform .25s, border-color .25s, box-shadow .25s; }
    .ql:hover { transform: translateY(-3px); }
    .ql .card-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 8px; }
    .ql h3 { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.1rem; }
    .ql .meta { display: flex; gap: 16px; color: var(--muted); font-size: .85rem; margin-top: 6px; flex-wrap: wrap; }
    .ql .due { color: var(--muted); font-size: .85rem; margin-top: 6px; }
    .ql .score { margin-top: 12px; font-size: .95rem; color: var(--text); }
    .ql .score strong { color: var(--green); font-family: 'Space Grotesk', sans-serif; font-size: 1.1rem; }
    .ql .card-foot { padding-top: 14px; }

    /* Status pills */
    .pill { font-family: 'Space Mono', monospace; font-size: .68rem; letter-spacing: .12em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px; white-space: nowrap; }
    .pill-available   { color: var(--blue);   background: rgba(115,182,255,.14); }
    .pill-in_progress,
    .pill-submitted   { color: var(--cyan);   background: rgba(91,225,255,.14); }
    .pill-completed,
    .pill-graded      { color: var(--green);  background: rgba(124,255,178,.14); }
    .pill-locked,
    .pill-not_submitted { color: var(--muted); background: rgba(150,170,255,.10); }
    .pill-late        { color: var(--red);    background: rgba(255,99,132,.15); }

    /* Grades */
    .grade-grid { display: grid; grid-template-columns: 280px 1fr; gap: 20px; }
    .grade-hero { text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 30px 24px;
      background: linear-gradient(135deg, rgba(124,255,178,.10), rgba(115,182,255,.08)); }
    .grade-hero .g-label { font-family: 'Space Mono', monospace; font-size: .72rem; letter-spacing: .18em; text-transform: uppercase; color: var(--muted); }
    .grade-hero .g-big { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: clamp(3rem, 6vw, 4rem); line-height: 1; margin: 10px 0;
      background: linear-gradient(100deg, var(--cyan), var(--blue) 55%, var(--violet)); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .grade-hero .g-tag { font-size: .95rem; color: var(--green); font-weight: 600; }

    .grade-break { display: flex; flex-direction: column; gap: 16px; justify-content: center; }
    .g-row .g-row-top { display: flex; align-items: baseline; justify-content: space-between; font-size: .9rem; margin-bottom: 7px; }
    .g-row .g-row-top .gw { color: var(--muted); font-family: 'Space Mono', monospace; font-size: .76rem; }
    .g-row .g-row-top .gv { font-family: 'Space Grotesk', sans-serif; font-weight: 700; }
    .bar { height: 10px; border-radius: 999px; background: rgba(150,170,255,.12); overflow: hidden; border: 1px solid var(--glass-border); }
    .bar > span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--cyan), var(--blue), var(--violet)); box-shadow: 0 0 14px rgba(115,182,255,.5); }
    .g-overall { display: flex; align-items: center; justify-content: space-between; padding-top: 14px; border-top: 1px dashed var(--glass-border); font-family: 'Space Grotesk', sans-serif; font-weight: 700; }
    .g-overall strong { font-size: 1.4rem; color: var(--text); }

    /* Back link + join another crew */
    .back { display: inline-flex; align-items: center; gap: 8px; color: var(--muted); font-size: .9rem; margin-bottom: 18px; transition: color .2s; }
    .back:hover { color: var(--text); }
    .join-another-toggle { display: inline-flex; align-items: center; gap: 8px; font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: .95rem; padding: 12px 20px; border-radius: 999px; cursor: pointer; color: var(--text); background: rgba(115,182,255,.12); border: 1px solid var(--glass-border); transition: background .25s; }
    .join-another-toggle:hover { background: rgba(115,182,255,.22); }
    .join-another { margin-top: 16px; }

    /* ---------- Join flow (not joined) ---------- */
    .wrap { position: relative; z-index: 2; max-width: 880px; margin: 0 auto; padding: 50px clamp(20px,5vw,64px) 80px; text-align: center; }
    .eyebrow-pill { font-family: 'Space Mono', monospace; font-size: .78rem; letter-spacing: .28em; text-transform: uppercase; color: var(--cyan);
      padding: 8px 16px; border: 1px solid var(--glass-border); border-radius: 999px; background: var(--glass); display: inline-block; }
    h1.big { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: clamp(2rem, 5vw, 3rem); letter-spacing: -.03em; margin: 20px 0 8px; }
    h1.big .accent { background: linear-gradient(100deg, var(--cyan), var(--blue) 45%, var(--violet)); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .sub { color: var(--muted); max-width: 56ch; margin: 0 auto; font-size: 1.02rem; }
    .ship-stage { position: relative; height: 280px; margin: 22px 0 6px; display: flex; align-items: center; justify-content: center; }
    .ship { width: clamp(120px, 20vw, 180px); animation: shipfloat 5s ease-in-out infinite; filter: drop-shadow(0 18px 40px rgba(115,182,255,.45)); }
    @keyframes shipfloat { 0%,100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-18px) rotate(2deg); } }
    .thruster { transform-origin: 50% 0; animation: flame .22s alternate infinite; }
    @keyframes flame { from { transform: scaleY(.55); opacity: .6; } to { transform: scaleY(1.15); opacity: 1; } }
    .dock { position: absolute; bottom: 8px; width: 110px; height: 110px; border-radius: 50%; background: radial-gradient(circle at 35% 30%, #d7c4ff, #6c4bd6 60%, #2e1a73); box-shadow: 0 0 60px rgba(124,75,214,.6); }
    .orbit { position: absolute; bottom: -10px; width: 210px; height: 210px; border: 1.5px dashed rgba(150,170,255,.28); border-radius: 50%; animation: spin 18s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .code-card { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 22px; padding: 30px; backdrop-filter: blur(12px); margin-top: 22px; text-align: left; }
    .code-card label { display: block; font-family: 'Space Mono', monospace; font-size: .72rem; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); margin-bottom: 10px; }
    .code-row { display: flex; gap: 12px; flex-wrap: wrap; }
    .code-input { flex: 1 1 200px; padding: 14px 16px; border-radius: 12px; background: rgba(10,12,40,.55); border: 1px solid var(--glass-border);
      color: var(--text); font-family: 'Space Mono', monospace; font-size: 1.1rem; letter-spacing: .2em; text-transform: uppercase; }
    .code-input:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(115,182,255,.18); }
    .btn { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 1rem; padding: 14px 26px; border-radius: 999px; cursor: pointer; border: none;
      display: inline-flex; align-items: center; gap: 10px; transition: transform .25s, box-shadow .25s; }
    .btn-primary { color: #07142e; background: linear-gradient(100deg, var(--cyan), var(--blue) 55%, var(--violet)); box-shadow: 0 10px 40px rgba(115,182,255,.45); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 16px 50px rgba(115,182,255,.6); }
    .alert { background: rgba(255,99,132,.12); border: 1px solid rgba(255,99,132,.4); color: #ffc2cf; padding: 11px 14px; border-radius: 12px; font-size: .9rem; margin-top: 16px; }

    /* ---------- Responsive ---------- */
    @media (max-width: 1000px) {
      .grade-grid { grid-template-columns: 1fr; }
      .split { grid-template-columns: 1fr; }
    }
    @media (max-width: 820px) {
      .app { flex-direction: column; }
      .sidebar { width: 100%; flex: none; height: auto; position: static; flex-direction: row; flex-wrap: wrap; gap: 8px; border-right: none; border-bottom: 1px solid var(--glass-border); }
      .brand { width: 100%; padding-bottom: 12px; }
      .nav { flex-direction: row; flex-wrap: wrap; }
      .nav a { padding: 9px 12px; }
      .side-foot { margin: 0; }
    }
    @media (prefers-reduced-motion: reduce) { *,*::after,*::before { animation: none !important; } }
  </style>
</head>
<body>

  <div class="space" aria-hidden="true">
    <div class="glow g1"></div><div class="glow g2"></div>
    <div id="stars"></div>
    <div class="planet p1"></div><div class="planet p2"></div>
  </div>

  @if ($crew)
    {{-- ============ JOINED: COURSE HUB ============ --}}
    <div class="app">
      <aside class="sidebar">
        <div class="brand">
          <svg viewBox="0 0 108.89 108.89" aria-hidden="true">
            <polygon fill="#73b6ff" points="37.55 108.89 52.56 108.89 52.56 90.12 90.12 90.12 90.12 75.09 52.56 75.09 52.56 56.33 37.55 56.33 37.55 108.89"/>
            <polygon fill="#73b6ff" points="108.89 71.34 108.89 56.33 90.12 56.33 90.12 18.78 75.09 18.78 75.09 56.33 56.33 56.33 56.33 71.34 108.89 71.34"/>
            <polygon fill="#73b6ff" points="71.34 0 56.33 0 56.33 18.78 18.78 18.78 18.78 33.79 56.33 33.79 56.33 52.56 71.34 52.56 71.34 0"/>
            <polygon fill="#73b6ff" points="0 37.55 0 52.56 18.78 52.56 18.78 90.12 33.79 90.12 33.79 52.56 52.56 52.56 52.56 37.55 0 37.55"/>
          </svg>
          <span class="name">Tech<span>Lab</span></span>
        </div>
        <nav class="nav">
          <a class="active" href="#top"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-8 9 8M5 10v10h14V10"/></svg> Course</a>
          <a href="#modules"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v14H4zM4 9h16M9 5v14"/></svg> Modules</a>
          <a href="#quizzes"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l2 2 4-4"/><rect x="4" y="3" width="16" height="18" rx="2"/></svg> Quizzes</a>
          <a href="#labs"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3h6M10 3v6l-5 9a2 2 0 0 0 2 3h10a2 2 0 0 0 2-3l-5-9V3"/></svg> Labs</a>
          <a href="#grades"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5M4 19h16M8 15l3-4 3 2 4-6"/></svg> Grades</a>
        </nav>
        <div class="side-foot">
          <form method="POST" action="/logout">
            @csrf
            <button class="nav-cta" type="submit">Log out</button>
          </form>
        </div>
      </aside>

      <main class="main" id="top">
        <a class="back" href="{{ route('student.dashboard') }}">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
          Back to Mission Control
        </a>
        {{-- Course header --}}
        <header class="course-head">
          <span class="eyebrow">TechLab Course</span>
          <h1>{{ $crew->name }}</h1>
          <div class="ch-meta">Instructor: <strong>{{ $crew->teacher->name ?? 'Captain' }}</strong></div>
          <p class="ch-desc">{{ $course['description'] }}</p>
        </header>

        {{-- 1. Learning Modules --}}
        <section class="section" id="modules">
          <div class="section-head">
            <h2>Learning Modules</h2>
            <p>Course materials &amp; study resources</p>
          </div>
          <div class="module-grid">
            @foreach ($course['modules'] as $m)
              <article class="card module">
                <span class="tag">Module {{ $m['number'] }}</span>
                <h3>{{ $m['title'] }}</h3>
                <p class="m-desc">{{ $m['description'] }}</p>
                <ul class="files">
                  @foreach ($m['materials'] as $file)
                    <li class="file" data-ext="{{ $file['ext'] }}">
                      <span class="file-ico">{{ strtoupper($file['ext']) }}</span>
                      <span class="file-name">{{ $file['name'] }}</span>
                      <span class="file-go">Open →</span>
                    </li>
                  @endforeach
                </ul>
                <div class="card-foot">
                  <span class="count">{{ $m['material_count'] }} Materials</span>
                  <a class="cta" href="#">View module →</a>
                </div>
              </article>
            @endforeach
          </div>
        </section>

        {{-- 2 + 3. Quizzes & Lab Activities --}}
        <section class="section" id="quizzes">
          <div class="split">
            <div class="col">
              <div class="section-head">
                <h2>Quizzes</h2>
                <p>Test your understanding</p>
              </div>
              <div class="ql-grid">
                @foreach ($course['quizzes'] as $q)
                  <article class="card ql">
                    <div class="card-top">
                      <span class="tag">Quiz {{ $q['number'] }}</span>
                      <span class="pill pill-{{ $q['status'] }}">
                        {{ $q['status'] === 'in_progress' ? 'In Progress' : ucfirst(str_replace('_', ' ', $q['status'])) }}
                      </span>
                    </div>
                    <h3>{{ $q['title'] }}</h3>
                    <div class="meta">
                      <span>📝 {{ $q['questions'] }} Questions</span>
                      <span>⏱ {{ $q['minutes'] }} Minutes</span>
                    </div>
                    <div class="card-foot">
                      @if ($q['status'] === 'completed')
                        <span class="score">Score <strong>{{ $q['score'] }}/{{ $q['total'] }}</strong> · {{ $q['percent'] }}%</span>
                        <a class="cta" href="#">View result →</a>
                      @elseif ($q['status'] === 'available')
                        <span class="count">Ready to start</span>
                        <a class="cta" href="#">Start quiz →</a>
                      @elseif ($q['status'] === 'in_progress')
                        <span class="count">In progress</span>
                        <a class="cta" href="#">Continue →</a>
                      @else
                        <span class="count">Locked</span>
                        <span class="cta" style="opacity:.5">Locked</span>
                      @endif
                    </div>
                  </article>
                @endforeach
              </div>
            </div>

            <div class="col" id="labs">
              <div class="section-head">
                <h2>Lab Activities</h2>
                <p>Hands-on practice &amp; application</p>
              </div>
              <div class="ql-grid">
                @foreach ($course['labs'] as $lab)
                  <article class="card ql">
                    <div class="card-top">
                      <span class="tag">Lab {{ $lab['number'] }}</span>
                      <span class="pill pill-{{ $lab['status'] }}">
                        {{ $lab['status'] === 'not_submitted' ? 'Not Submitted' : ucfirst(str_replace('_', ' ', $lab['status'])) }}
                      </span>
                    </div>
                    <h3>{{ $lab['title'] }}</h3>
                    <p class="m-desc">{{ $lab['description'] }}</p>
                    <div class="due">Due: {{ $lab['due'] }}</div>
                    <div class="card-foot">
                      @if ($lab['status'] === 'graded')
                        <span class="score">Score <strong>{{ $lab['score'] }}/{{ $lab['total'] }}</strong></span>
                        <a class="cta" href="#">View feedback →</a>
                      @elseif ($lab['status'] === 'submitted')
                        <span class="count">Awaiting grade</span>
                        <a class="cta" href="#">View submission →</a>
                      @elseif ($lab['status'] === 'late')
                        <span class="count">Past due</span>
                        <a class="cta" href="#">Submit late →</a>
                      @elseif ($lab['status'] === 'in_progress')
                        <span class="count">In progress</span>
                        <a class="cta" href="#">Continue →</a>
                      @else
                        <span class="count">Not started</span>
                        <a class="cta" href="#">Open lab →</a>
                      @endif
                    </div>
                  </article>
                @endforeach
              </div>
            </div>
          </div>
        </section>

        {{-- 4. My Grades --}}
        <section class="section" id="grades">
          <div class="section-head">
            <h2>My Grades</h2>
            <p>Your academic performance</p>
          </div>
          <div class="grade-grid">
            <div class="card grade-hero">
              <div class="g-label">Current Grade</div>
              <div class="g-big">{{ $course['grades']['current'] }}%</div>
              <div class="g-tag">{{ $course['grades']['label'] }}</div>
            </div>
            <div class="card grade-break">
              @foreach ($course['grades']['breakdown'] as $b)
                <div class="g-row">
                  <div class="g-row-top">
                    <span>{{ $b['label'] }} <span class="gw">· {{ $b['weight'] }}%</span></span>
                    <span class="gv">{{ $b['score'] }}/{{ $b['total'] }}</span>
                  </div>
                  <div class="bar"><span style="width:{{ $b['score'] }}%"></span></div>
                </div>
              @endforeach
              <div class="g-overall">
                <span>Overall</span>
                <strong>{{ $course['grades']['overall'] }}%</strong>
              </div>
            </div>
          </div>
        </section>

        {{-- Join another crew --}}
        <section class="section" style="margin-bottom: 0;">
          <button class="join-another-toggle" id="joinAnotherBtn" type="button" aria-expanded="false">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Join another crew
          </button>
          <div class="code-card join-another" id="joinAnother" hidden>
            <label for="anotherCode">Join another crew</label>
            <p class="sub" style="margin: 0 0 14px;">Got a code from another teacher or captain? Board that crew too.</p>
            <form method="POST" action="/student/crew/join">
              @csrf
              <div class="code-row">
                <input id="anotherCode" name="code" type="text" class="code-input" placeholder="ABC123" maxlength="6" autocomplete="off" required />
                <button class="btn btn-primary" type="submit">Join crew →</button>
              </div>
            </form>
          </div>
        </section>
      </main>
    </div>

  @else
    {{-- ============ NOT JOINED: CREW JOIN FLOW ============ --}}
    <main class="wrap">
      <span class="eyebrow-pill">Crew · Boarding</span>
      <h1 class="big">Join your <span class="accent">crew</span></h1>
      <p class="sub">Got a crew code from your teacher or captain? Enter it below to board the ship and join your class.</p>

      <div class="ship-stage">
        <div class="orbit"></div>
        <div class="dock"></div>
        <svg class="ship" viewBox="0 0 120 220" aria-hidden="true">
          <defs>
            <linearGradient id="hull" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#eaf2ff"/><stop offset="100%" stop-color="#9fb4e6"/></linearGradient>
            <linearGradient id="flame" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#fff3b0"/><stop offset="60%" stop-color="#ff9b3d"/><stop offset="100%" stop-color="#ff4d6d"/></linearGradient>
          </defs>
          <g class="thruster"><path d="M48 168 q12 34 12 46 q0 -12 12 -46 z" fill="url(#flame)"/></g>
          <path d="M60 8 q32 42 32 96 q0 44 -32 56 q-32 -12 -32 -56 q0 -54 32 -96z" fill="url(#hull)" stroke="#7c8cc8" stroke-width="2"/>
          <circle cx="60" cy="74" r="15" fill="#5be1ff" stroke="#1b3a5c" stroke-width="2"/>
          <path d="M28 120 q-22 8 -22 44 q22 -12 32 -22z" fill="#9b6bff"/>
          <path d="M92 120 q22 8 22 44 q-22 -12 -32 -22z" fill="#9b6bff"/>
        </svg>
      </div>

      @if ($errors->any())
        <div class="alert">
          @foreach ($errors->all() as $error) {{ $error }} @endforeach
        </div>
      @endif

      <form class="code-card" method="POST" action="/student/crew/join">
        @csrf
        <label for="code">Crew code</label>
        <div class="code-row">
          <input id="code" name="code" type="text" class="code-input" placeholder="ABC123" value="{{ old('code') }}" maxlength="6" autocomplete="off" required />
          <button class="btn btn-primary" type="submit">Board the ship →</button>
        </div>
      </form>
    </main>
  @endif

  <script>
    (function () {
      const field = document.getElementById('stars');
      const count = window.innerWidth < 700 ? 90 : 170;
      const frag = document.createDocumentFragment();
      let seed = 4242; const rnd = () => { seed = (seed * 1103515245 + 12345) & 0x7fffffff; return seed / 0x7fffffff; };
      for (let i = 0; i < count; i++) {
        const s = document.createElement('span'); s.className = 'star';
        const size = rnd() > 0.85 ? 3 : 2;
        s.style.width = s.style.height = size + 'px';
        s.style.left = (rnd() * 100) + 'vw'; s.style.top = (rnd() * 100) + 'vh';
        s.style.setProperty('--dur', (2.5 + rnd() * 4).toFixed(2) + 's');
        s.style.setProperty('--delay', (rnd() * 5).toFixed(2) + 's');
        frag.appendChild(s);
      }
      field.appendChild(frag);
    })();

    const jaBtn = document.getElementById('joinAnotherBtn');
    const jaPanel = document.getElementById('joinAnother');
    if (jaBtn && jaPanel) {
      jaBtn.addEventListener('click', () => {
        if (jaPanel.hasAttribute('hidden')) {
          jaPanel.removeAttribute('hidden');
          jaBtn.setAttribute('aria-expanded', 'true');
        } else {
          jaPanel.setAttribute('hidden', '');
          jaBtn.setAttribute('aria-expanded', 'false');
        }
      });
    }
  </script>
</body>
</html>
