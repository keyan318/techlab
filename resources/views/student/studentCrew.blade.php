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

  {{-- Tailwind Play CDN + tokens and Alpine: needed only by the shared shell sidebar
       (components/shell/side-bar). The page's own styling below is plain CSS. --}}
    <meta name="theme-color" content="#06061a">
  <link rel="stylesheet" href="{{ asset('css/theme.css') }}?v={{ filemtime(public_path('css/theme.css')) }}">
  <script src="{{ asset('js/theme.js') }}?v={{ filemtime(public_path('js/theme.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            void:        'rgb(var(--c-void) / <alpha-value>)',
            blue:        'rgb(var(--c-blue) / <alpha-value>)',
            violet:      'rgb(var(--c-violet) / <alpha-value>)',
            cyan:        'rgb(var(--c-cyan) / <alpha-value>)',
            ink:         'rgb(var(--c-ink) / <alpha-value>)',
            muted:       'rgb(var(--c-muted) / <alpha-value>)',
            glass:       'var(--glass)',
            glassBorder: 'var(--glass-border)',
          },
          fontFamily: {
            sans:    ['Inter', 'system-ui', 'sans-serif'],
            display: ['Space Grotesk', 'sans-serif'],
            mono:    ['Space Mono', 'monospace'],
          },
        },
      },
    };
  </script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    [x-cloak] { display: none !important; }
    :root {
      --void: #06061a; --cosmic: #120a33; --cosmic-2: #1e1259;
      --blue: #73b6ff; --violet: #9b6bff; --cyan: #5be1ff; --green: #7cffb2;
      --red: #ff9bb0;
      --text: #eaeeff; --muted: #98a2d4;
      --glass: rgba(123, 142, 220, 0.07); --glass-border: rgba(150, 170, 255, 0.18);
      /* Tailwind's reset (loaded for the shell sidebar) forces html/body to 1.5 and
         inputs/buttons to `inherit`; pin the page's original rhythm back at the root. */
      line-height: 1.6;
    }
    .app input, .app button, .wrap input, .wrap button { line-height: normal; }
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
    .side-label { font-family: 'Space Mono', monospace; font-size: .68rem; letter-spacing: .14em; text-transform: uppercase;
      color: var(--muted); padding: 6px 14px 14px; }
    .nav { display: flex; flex-direction: column; gap: 4px; }
    .nav a { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 12px; color: var(--muted);
      font-weight: 600; font-size: .95rem; transition: all .2s; }
    .nav a svg { width: 20px; height: 20px; flex: 0 0 20px; }
    .nav a:hover { color: var(--text); background: var(--glass); }
    .nav a.active { color: var(--text); background: var(--glass); }
    .nav a.active svg { stroke: currentColor; }
    .side-foot { margin-top: auto; }
    .side-foot form { margin: 0; }

    /* Collapsible in-page nav: starts collapsed (icon rail) to leave room for the course content */
    .sidebar { transition: width .3s cubic-bezier(.32,.72,0,1), flex-basis .3s cubic-bezier(.32,.72,0,1), padding .3s cubic-bezier(.32,.72,0,1); overflow: hidden; }
    .nav-txt { white-space: nowrap; overflow: hidden; max-width: 160px; transition: opacity .2s, max-width .3s cubic-bezier(.32,.72,0,1); }
    .side-label { white-space: nowrap; overflow: hidden; transition: opacity .2s, max-height .3s; max-height: 40px; }
    .sidebar.is-collapsed { width: 68px; flex-basis: 68px; padding-left: 10px; padding-right: 10px; }
    .sidebar.is-collapsed .side-label { opacity: 0; max-height: 0; padding-top: 0; padding-bottom: 0; }
    .sidebar.is-collapsed .nav a { justify-content: center; gap: 0; padding: 11px 0; }
    .sidebar.is-collapsed .nav-txt { max-width: 0; opacity: 0; }
    .side-toggle { display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; margin-top: auto; padding: 10px 14px; border-radius: 12px;
      border: 0; background: transparent; color: var(--muted); font: 600 .85rem 'Inter', sans-serif; cursor: pointer; transition: color .2s, background .2s; }
    .side-toggle:hover { color: var(--text); background: var(--glass); }
    .side-toggle:focus-visible { outline: 2px solid var(--blue); outline-offset: 2px; }
    .side-toggle svg { width: 20px; height: 20px; flex: 0 0 20px; transition: transform .3s; }
    .sidebar:not(.is-collapsed) .side-toggle svg { transform: rotate(180deg); }
    .sidebar.is-collapsed .side-toggle .nav-txt { max-width: 0; opacity: 0; }
    .sidebar.is-collapsed .side-toggle { gap: 0; padding: 10px 0; }
    @media (prefers-reduced-motion: reduce) { .sidebar, .nav-txt, .side-label { transition: none !important; } }
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
    .empty-state { text-align: center; padding: 40px 24px; }
    a.file { color: inherit; }
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
    .ship-stage { position: relative; height: 340px; margin: 22px 0 6px; display: flex; align-items: center; justify-content: center; }
    .ship { width: clamp(200px, 26vw, 270px); aspect-ratio: 900 / 960; position: relative; z-index: 1; }
    .ship svg { width: 100%; height: 100%; display: block; overflow: visible; }
    .ship.deny { animation: shipdeny .4s linear; }
    @keyframes shipdeny { 0%,100% { transform: translateX(0); } 20%,60% { transform: translateX(-7px); } 40%,80% { transform: translateX(7px); } }
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
      .sidebar.is-collapsed { width: 100%; flex: none; padding: 22px 18px; }
      .sidebar.is-collapsed .nav a { justify-content: flex-start; gap: 12px; padding: 9px 12px; }
      .sidebar.is-collapsed .nav-txt { max-width: 160px; opacity: 1; }
      .sidebar.is-collapsed .side-label { opacity: 1; max-height: 40px; }
      .side-toggle { display: none; }
    }
    .cta-btn { background: none; border: 0; cursor: pointer; padding: 0; }
    .empty-card { color: var(--muted); font-size: .92rem; text-align: center; padding: 26px 18px; }
    .qz-back { position: fixed; inset: 0; z-index: 100; background: rgba(5,6,24,.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; padding: 20px; }
    .qz-back[hidden] { display: none; }
    .qz-box { background: var(--void); color: var(--text); border: 1px solid var(--glass-border); border-radius: 22px; width: min(680px, 100%); max-height: 88vh; overflow: auto; padding: 24px 26px; }
    .qz-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
    .qz-head h3 { font-family: 'Space Grotesk', sans-serif; font-size: 1.25rem; }
    .qz-x { background: none; border: 0; font-size: 1.6rem; line-height: 1; cursor: pointer; color: var(--muted); }
    .qz-q { margin: 18px 0; }
    .qz-q p { font-weight: 600; margin-bottom: 8px; }
    .qz-opt { display: flex; gap: 10px; align-items: center; padding: 10px 12px; border: 1px solid var(--glass-border); border-radius: 12px; margin-top: 8px; cursor: pointer; }
    .qz-opt input { accent-color: var(--violet); }
    .qz-opt.ok { border-color: #1fa971; background: rgba(31,169,113,.12); }
    .qz-opt.bad { border-color: #e5484d; background: rgba(229,72,77,.10); }
    .qz-score { font-family: 'Space Grotesk', sans-serif; font-size: 1.6rem; font-weight: 700; margin: 4px 0 8px; }
    .qz-err { color: #e5484d; font-size: .9rem; margin-top: 10px; }
    .qz-submit { margin-top: 8px; }
    @media (prefers-reduced-motion: reduce) { *,*::after,*::before { animation: none !important; } }
  </style>
</head>
<body x-data="techlabShell()" x-init="init()">

  <div class="space" aria-hidden="true">
    <div class="glow g1"></div><div class="glow g2"></div>
    <div id="stars"></div>
    <div class="planet p1"></div><div class="planet p2"></div>
  </div>

  {{-- Shell: persistent TechLab sidebar on the left, page content on the right.
       The wrapper is sticky so the rail stays put while the page scrolls. --}}
  <div class="flex items-start">
    <div class="sticky top-0 z-30 flex h-screen flex-shrink-0">
      @include('components.shell.side-bar')
    </div>
    <div class="min-w-0 flex-1">

  @if ($crew)
    {{-- ============ JOINED: COURSE HUB ============ --}}
    <div class="app" x-data="{
           navCollapsed: true,
           init() {
             try { const v = localStorage.getItem('techlab_crew_nav_collapsed'); if (v !== null) this.navCollapsed = v === '1'; } catch (e) {}
             this.$watch('navCollapsed', v => { try { localStorage.setItem('techlab_crew_nav_collapsed', v ? '1' : '0'); } catch (e) {} });
           }
         }">
      {{-- In-page section nav. Logo and Log out live in the shell sidebar now.
           Collapsed by default so the course content gets the room; the toggle at the bottom expands it. --}}
      <aside class="sidebar is-collapsed" :class="{ 'is-collapsed': navCollapsed }" aria-label="On this page">
        <p class="side-label">On this page</p>
        <nav class="nav">
          <a class="active" href="#top" title="Course"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-8 9 8M5 10v10h14V10"/></svg><span class="nav-txt">Course</span></a>
          <a href="#modules" title="Modules"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v14H4zM4 9h16M9 5v14"/></svg><span class="nav-txt">Modules</span></a>
          <a href="#quizzes" title="Quizzes"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l2 2 4-4"/><rect x="4" y="3" width="16" height="18" rx="2"/></svg><span class="nav-txt">Quizzes</span></a>
          <a href="#labs" title="Labs"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3h6M10 3v6l-5 9a2 2 0 0 0 2 3h10a2 2 0 0 0 2-3l-5-9V3"/></svg><span class="nav-txt">Labs</span></a>
          <a href="#grades" title="Grades"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5M4 19h16M8 15l3-4 3 2 4-6"/></svg><span class="nav-txt">Grades</span></a>
        </nav>
        <button type="button" class="side-toggle" @click="navCollapsed = !navCollapsed"
                :aria-expanded="(!navCollapsed).toString()" :aria-label="navCollapsed ? 'Expand page menu' : 'Collapse page menu'"
                :title="navCollapsed ? 'Expand' : 'Collapse'">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
          <span class="nav-txt">Collapse</span>
        </button>
      </aside>

      <main class="main" id="top">
        {{-- Course header --}}
        <header class="course-head">
          <span class="eyebrow">TechLab Course</span>
          <h1>{{ $crew->name }}</h1>
          <div class="ch-meta">Instructor: <strong>{{ $crew->faculty->name ?? 'Captain' }}</strong></div>
          <p class="ch-desc">{{ $course['description'] }}</p>
        </header>

        {{-- 1. Learning Modules --}}
        <section class="section" id="modules">
          <div class="section-head">
            <h2>Learning Modules</h2>
            <p>Course materials &amp; study resources</p>
          </div>
          @if (empty($course['modules']))
            <div class="card empty-state">
              <h3>No modules yet</h3>
              <p class="m-desc">{{ $crew->faculty->name ?? 'Your captain' }} hasn't published any learning modules. Check back soon.</p>
            </div>
          @else
          <div class="module-grid">
            @foreach ($course['modules'] as $m)
              <article class="card module">
                <span class="tag">Module {{ $m['number'] }}</span>
                <h3>{{ $m['title'] }}</h3>
                @if ($m['description'])<p class="m-desc">{{ $m['description'] }}</p>@endif
                @if ($m['materials'])
                <ul class="files">
                  @foreach ($m['materials'] as $file)
                    <li>
                      <a class="file" data-ext="{{ $file['ext'] }}" href="{{ $file['url'] }}">
                        <span class="file-ico">{{ strtoupper(pathinfo($file['name'], PATHINFO_EXTENSION)) }}</span>
                        <span class="file-name">{{ $file['name'] }}</span>
                        <span class="file-go">Download ↓</span>
                      </a>
                    </li>
                  @endforeach
                </ul>
                @else
                  <p class="m-desc">No materials uploaded yet.</p>
                @endif
                <div class="card-foot">
                  <span class="count">{{ $m['material_count'] }} {{ Str::plural('Material', $m['material_count']) }}</span>
                </div>
              </article>
            @endforeach
          </div>
          @endif
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
                @forelse ($course['quizzes'] as $q)
                  <article class="card ql">
                    <div class="card-top">
                      <span class="tag">Quiz {{ $q['number'] }}</span>
                      <span class="pill pill-{{ $q['status'] }}">{{ $q['status'] === 'completed' ? 'Completed' : 'Available' }}</span>
                    </div>
                    <h3>{{ $q['title'] }}</h3>
                    <div class="meta">
                      <span>📝 {{ $q['questions'] }} {{ Str::plural('Question', $q['questions']) }}</span>
                      @if ($q['minutes'])<span>⏱ {{ $q['minutes'] }} Minutes</span>@endif
                    </div>
                    <div class="card-foot">
                      @if ($q['status'] === 'completed')
                        <span class="score">Score <strong>{{ $q['score'] }}/{{ $q['total'] }}</strong> · {{ $q['percent'] }}%</span>
                        <button type="button" class="cta cta-btn" data-quiz="{{ $q['id'] }}">View result →</button>
                      @else
                        <span class="count">Ready to start</span>
                        <button type="button" class="cta cta-btn" data-quiz="{{ $q['id'] }}">Start quiz →</button>
                      @endif
                    </div>
                  </article>
                @empty
                  <div class="card empty-card">No quizzes yet. When your faculty member publishes one, it will show up here.</div>
                @endforelse
              </div>
            </div>

            <div class="col" id="labs">
              <div class="section-head">
                <h2>Lab Activities</h2>
                <p>Hands-on practice &amp; application</p>
              </div>
              <div class="ql-grid">
                <div class="card empty-card">No lab activities yet.</div>
              </div>
            </div>
          </div>
        </section>

        {{-- 4. My Grades: computed from this student's own quiz attempts --}}
        <section class="section" id="grades">
          <div class="section-head">
            <h2>My Grades</h2>
            <p>Your academic performance</p>
          </div>
          @php $gr = $course['grades']; @endphp
          <div class="grade-grid">
            <div class="card grade-hero">
              <div class="g-label">Current Grade</div>
              <div class="g-big">{{ $gr['current'] !== null ? rtrim(rtrim(number_format($gr['current'], 1), '0'), '.').'%' : '—' }}</div>
              <div class="g-tag">{{ $gr['label'] }}</div>
            </div>
            <div class="card grade-break">
              <div class="g-row">
                <div class="g-row-top">
                  <span>Quizzes</span>
                  <span class="gv">{{ $gr['done'] }} of {{ $gr['count'] }} taken</span>
                </div>
                <div class="bar"><span style="width:{{ $gr['current'] ?? 0 }}%"></span></div>
              </div>
              <div class="g-overall">
                <span>Overall</span>
                <strong>{{ $gr['current'] !== null ? rtrim(rtrim(number_format($gr['current'], 1), '0'), '.').'%' : '—' }}</strong>
              </div>
            </div>
          </div>
        </section>

        {{-- Quiz player (opened from the cards above) --}}
        <div class="qz-back" id="qzModal" hidden>
          <div class="qz-box" role="dialog" aria-modal="true" aria-labelledby="qzTitle">
            <div class="qz-head">
              <h3 id="qzTitle"></h3>
              <button type="button" class="qz-x" id="qzClose" aria-label="Close">×</button>
            </div>
            <div id="qzBody"></div>
          </div>
        </div>

        {{-- Join another crew --}}
        <section class="section" style="margin-bottom: 0;">
          <button class="join-another-toggle" id="joinAnotherBtn" type="button" aria-expanded="false">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Join another crew
          </button>
          <div class="code-card join-another" id="joinAnother" hidden>
            <label for="anotherCode">Join another crew</label>
            <p class="sub" style="margin: 0 0 14px;">Got a code from another faculty member or captain? Board that crew too.</p>
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
      <p class="sub">Got a crew code from your faculty member or captain? Enter it below to board the ship and join your class.</p>

      <div class="ship-stage">
        <div class="orbit"></div>
        <div class="ship" id="rocket" data-src="{{ asset('images/rocket.svg') }}" aria-hidden="true"></div>
      </div>

      @if ($errors->any())
        <div class="alert">
          @foreach ($errors->all() as $error) {{ $error }} @endforeach
        </div>
      @endif

      <form class="code-card" id="joinForm" method="POST" action="/student/crew/join">
        @csrf
        <label for="code">Crew code</label>
        <div class="code-row">
          <input id="code" name="code" type="text" class="code-input" placeholder="ABC123" value="{{ old('code') }}" maxlength="6" autocomplete="off" required />
          <button class="btn btn-primary" type="submit">Board the ship →</button>
        </div>
      </form>
    </main>
  @endif

    </div>{{-- /content column --}}
  </div>{{-- /shell --}}

  <script>
    // techlabShell: sidebar collapsed state, persisted via localStorage and shared
    // with the chat + planets pages (same key).
    function techlabShell() {
      return {
        collapsed: true,

        init() {
          const saved = localStorage.getItem('techlab_sidebar_collapsed');
          this.collapsed = saved === null ? true : saved === '1';
          this.$watch('collapsed', v =>
            localStorage.setItem('techlab_sidebar_collapsed', v ? '1' : '0')
          );
        },

        toggleTheme() { window.techlabTheme.toggle(); },
      };
    }

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


    (function () {
      const holder = document.getElementById('rocket');
      const form = document.getElementById('joinForm');
      if (!holder || !form) return;
      let svg = null, t = 0.9, dir = 1, rate = 1, last = 0, launching = false, busy = false;
      const LO = 0.9, HI = 1.65, END = 2.95;
      const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      function tick(now) {
        const dt = Math.min((now - last) / 1000, 0.05); last = now;
        t += dir * rate * dt;
        if (!launching) {
          if (t >= HI) { t = HI; dir = -1; } else if (t <= LO) { t = LO; dir = 1; }
        } else if (t >= END) { t = END; }
        svg.setCurrentTime(t);
        requestAnimationFrame(tick);
      }
      fetch(holder.dataset.src).then(r => r.text()).then(txt => {
        holder.innerHTML = txt;
        svg = holder.querySelector('svg');
        svg.pauseAnimations(); svg.setCurrentTime(t);
        if (!reduce) { last = performance.now(); requestAnimationFrame(tick); }
      }).catch(() => {});

      let alertEl = document.querySelector('.alert');
      function showError(msg) {
        if (!alertEl) {
          alertEl = document.createElement('div'); alertEl.className = 'alert';
          form.parentNode.insertBefore(alertEl, form);
        }
        alertEl.textContent = msg;
      }

      form.addEventListener('submit', async (e) => {
        if (!svg || reduce) return;              // fall back to a normal submit
        e.preventDefault();
        if (busy || launching) return;
        busy = true;
        try {
          const res = await fetch(form.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form),
            credentials: 'same-origin',
          });
          const data = await res.json().catch(() => null);
          if (res.ok && data && data.redirect) {
            // Boarded: only now does the rocket lift off.
            launching = true; dir = 1; rate = 1.4;
            setTimeout(() => { window.location.href = data.redirect; }, 1500);
            return;
          }
          const msg = (data && (data.message || (data.errors && Object.values(data.errors)[0][0])))
            || 'Could not board the ship. Please try again.';
          showError(msg);
          holder.classList.remove('deny'); void holder.offsetWidth; holder.classList.add('deny');
        } catch (err) {
          form.submit();                          // network hiccup: let the server decide
          return;
        }
        busy = false;
      });
    })();


    (function () {
      const modal = document.getElementById('qzModal');
      if (!modal) return;
      const body = document.getElementById('qzBody'), title = document.getElementById('qzTitle');
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
      const esc = (t) => { const d = document.createElement('div'); d.textContent = t ?? ''; return d.innerHTML; };
      const close = () => { modal.hidden = true; };
      document.getElementById('qzClose').onclick = close;
      modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });

      function renderResult(quiz, r) {
        const byId = Object.fromEntries(r.review.map(x => [x.id, x]));
        body.innerHTML = `<div class="qz-score">${r.score}/${r.total} · ${r.percent}%</div>` + quiz.questions.map((q, n) => {
          const rv = byId[q.id] || {};
          return `<div class="qz-q"><p>${n + 1}. ${esc(q.prompt)}</p>` + q.options.map((o, i) =>
            `<div class="qz-opt ${i === rv.correct ? 'ok' : (i === rv.chosen ? 'bad' : '')}">${esc(o)}${i === rv.chosen ? ' <em>(your answer)</em>' : ''}</div>`).join('') + '</div>';
        }).join('') + '<button type="button" class="mbtn-close cta cta-btn" id="qzDone">Close</button>';
        document.getElementById('qzDone').onclick = () => location.reload();
      }

      function renderForm(quiz) {
        body.innerHTML = (quiz.minutes ? `<p style="color:var(--muted);font-size:.85rem">Suggested time: ${quiz.minutes} minutes. You can take this quiz once.</p>` : '<p style="color:var(--muted);font-size:.85rem">You can take this quiz once.</p>')
          + '<form id="qzForm">' + quiz.questions.map((q, n) =>
            `<div class="qz-q"><p>${n + 1}. ${esc(q.prompt)}</p>` + q.options.map((o, i) =>
              `<label class="qz-opt"><input type="radio" name="q${q.id}" value="${i}"> <span>${esc(o)}</span></label>`).join('') + '</div>').join('')
          + '<button class="btn btn-primary qz-submit" type="submit">Submit answers</button><div class="qz-err" id="qzErr"></div></form>';
        document.getElementById('qzForm').onsubmit = async (e) => {
          e.preventDefault();
          const answers = {};
          quiz.questions.forEach(q => { const c = e.target.querySelector(`input[name="q${q.id}"]:checked`); if (c) answers[q.id] = +c.value; });
          if (Object.keys(answers).length < quiz.questions.length && !confirm('Some questions are unanswered. Submit anyway?')) return;
          const res = await fetch(`/student/quizzes/${quiz.id}/submit`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ answers }) });
          if (!res.ok) { document.getElementById('qzErr').textContent = 'Could not submit. Please try again.'; return; }
          renderResult(quiz, (await res.json()).result);
        };
      }

      document.querySelectorAll('[data-quiz]').forEach(btn => btn.addEventListener('click', async () => {
        title.textContent = 'Loading…'; body.innerHTML = ''; modal.hidden = false;
        const res = await fetch(`/student/quizzes/${btn.dataset.quiz}`, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) { title.textContent = 'Quiz unavailable'; return; }
        const quiz = await res.json();
        title.textContent = quiz.title;
        quiz.result ? renderResult(quiz, quiz.result) : renderForm(quiz);
      }));
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
