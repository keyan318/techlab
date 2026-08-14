<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mission Control · TechLab</title>
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
      --text: #eaeeff; --muted: #98a2d4;
      --glass: rgba(123, 142, 220, 0.07); --glass-border: rgba(150, 170, 255, 0.18);
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { font-family: 'Inter', system-ui, sans-serif; color: var(--text); background: var(--void);
      overflow-x: hidden; -webkit-font-smoothing: antialiased; line-height: 1.6; }

    /* space background */
    .space { position: fixed; inset: 0; z-index: -1; overflow: hidden;
      background:
        radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%),
        radial-gradient(100% 80% at 85% 110%, #1a0f4d 0%, rgba(26,15,77,0) 60%),
        linear-gradient(160deg, #0a0826 0%, #120a33 45%, #1e1259 100%); }
    .glow { position: absolute; border-radius: 50%; filter: blur(70px); opacity: .5; animation: pulse 9s ease-in-out infinite; }
    .glow.g1 { width: 460px; height: 460px; top: -120px; left: -80px; background: radial-gradient(circle, rgba(155,107,255,.7), transparent 70%); }
    .glow.g2 { width: 520px; height: 520px; bottom: -160px; right: -120px; background: radial-gradient(circle, rgba(91,225,255,.4), transparent 70%); animation-delay: -4s; }
    .glow.g3 { width: 380px; height: 380px; top: 40%; left: 55%; background: radial-gradient(circle, rgba(115,182,255,.3), transparent 70%); animation-delay: -2s; }
    @keyframes pulse { 0%,100% { transform: scale(1); opacity: .45; } 50% { transform: scale(1.12); opacity: .65; } }
    .star { position: absolute; width: 2px; height: 2px; border-radius: 50%; background: #fff; opacity: .8; animation: twinkle var(--dur,4s) ease-in-out infinite; animation-delay: var(--delay,0s); }
    @keyframes twinkle { 0%,100% { opacity: .15; transform: scale(.7); } 50% { opacity: 1; transform: scale(1.2); } }
    .planet { position: absolute; border-radius: 50%; animation: float 14s ease-in-out infinite; }
    .planet::after { content: ""; position: absolute; inset: 0; border-radius: 50%; box-shadow: inset -18px -18px 40px rgba(0,0,0,.45); }
    .p1 { width: 180px; height: 180px; top: 12%; right: 6%; background: radial-gradient(circle at 35% 30%, #b9a3ff, #6c4bd6 55%, #2e1a73); box-shadow: 0 0 70px rgba(124,75,214,.5); }
    .p2 { width: 90px; height: 90px; bottom: 10%; left: 4%; background: radial-gradient(circle at 35% 30%, #aef0ff, #4fc8ee 55%, #1d6f99); box-shadow: 0 0 50px rgba(91,225,255,.4); animation-direction: reverse; }
    @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-24px); } }

    .app { display: flex; align-items: flex-start; min-height: 100vh; }

    /* ---------- Sidebar ---------- */
    .sidebar { width: 248px; flex: 0 0 248px; position: sticky; top: 0; height: 100vh; padding: 22px 18px;
      border-right: 1px solid var(--glass-border); background: rgba(10,12,40,0.5); backdrop-filter: blur(12px); display: flex; flex-direction: column; }
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
    .side-foot .nav-cta { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: .9rem; padding: 11px 18px; border-radius: 999px;
      background: rgba(115,182,255,0.12); border: 1px solid var(--glass-border); color: var(--text); cursor: pointer; text-align: center; transition: all .25s; }
    .side-foot form { margin: 0; }
    .side-foot .nav-cta:hover { background: rgba(115,182,255,0.22); }

    /* ---------- Main ---------- */
    .main { flex: 1 1 auto; min-width: 0; padding: 22px clamp(18px, 3vw, 38px) 50px; }

    .topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 26px; flex-wrap: wrap; }
    .topbar .crumb { font-family: 'Space Mono', monospace; font-size: .72rem; letter-spacing: .16em; text-transform: uppercase; color: var(--cyan); }
    .topbar .search { flex: 1 1 220px; max-width: 360px; padding: 11px 16px; border-radius: 999px; background: var(--glass);
      border: 1px solid var(--glass-border); color: var(--muted); font-size: .9rem; }
    .userchip { display: flex; align-items: center; gap: 10px; padding: 6px 14px 6px 6px; border-radius: 999px; background: var(--glass); border: 1px solid var(--glass-border); }
    .userchip .av { width: 32px; height: 32px; border-radius: 50%; background: radial-gradient(circle at 35% 30%, #eaf2ff, #9fb4e6); display: grid; place-items: center; font-weight: 700; color: #16213e; font-size: .8rem; }

    /* welcome */
    .welcome { display: flex; align-items: center; gap: 24px; background: linear-gradient(120deg, rgba(115,182,255,.12), rgba(155,107,255,.10));
      border: 1px solid var(--glass-border); border-radius: 24px; padding: 28px 32px; margin-bottom: 24px; position: relative; overflow: hidden; }
    .welcome .wtext { flex: 1 1 auto; }
    .welcome h1 { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: clamp(1.6rem, 3vw, 2.4rem); letter-spacing: -.02em; }
    .welcome h1 .accent { background: linear-gradient(100deg, var(--cyan), var(--blue) 45%, var(--violet)); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .welcome p { color: var(--muted); margin-top: 6px; }
    .welcome .lvl { display: inline-flex; align-items: center; gap: 8px; margin-top: 14px; font-family: 'Space Mono', monospace; font-size: .8rem;
      padding: 7px 14px; border-radius: 999px; background: rgba(124,255,178,.12); border: 1px solid rgba(124,255,178,.35); color: var(--green); }
    .welcome .mascot { width: clamp(90px, 12vw, 140px); flex: 0 0 auto; filter: drop-shadow(0 16px 36px rgba(115,182,255,.4)); animation: bob 6s ease-in-out infinite; }
    @keyframes bob { 0%,100% { transform: translateY(0) rotate(-3deg); } 50% { transform: translateY(-14px) rotate(3deg); } }

    /* grids + cards */
    .stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 22px; }
    .grid { display: grid; grid-template-columns: repeat(12, 1fr); gap: 20px; }
    .col-12 { grid-column: span 12; } .col-8 { grid-column: span 8; } .col-6 { grid-column: span 6; } .col-4 { grid-column: span 4; }

    .card { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 20px; padding: 22px 24px; backdrop-filter: blur(12px); }
    .stat { padding: 18px 18px; }
    .stat .ico { width: 38px; height: 38px; border-radius: 11px; display: grid; place-items: center; background: rgba(115,182,255,.12); margin-bottom: 12px; }
    .stat .ico svg { width: 20px; height: 20px; stroke: var(--blue); }
    .stat .v { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.6rem; }
    .stat .k { color: var(--muted); font-size: .82rem; margin-top: 2px; }

    .card h3 { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 1.15rem; margin-bottom: 4px; }
    .card .sub { color: var(--muted); font-size: .88rem; }
    .card .tag { font-family: 'Space Mono', monospace; font-size: .68rem; letter-spacing: .16em; text-transform: uppercase; color: var(--cyan); }

    .btn { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: .95rem; padding: 12px 22px; border-radius: 999px; cursor: pointer; border: none;
      display: inline-flex; align-items: center; gap: 8px; transition: transform .25s, box-shadow .25s; text-decoration: none; }
    .btn-primary { color: #07142e; background: linear-gradient(100deg, var(--cyan), var(--blue) 55%, var(--violet)); box-shadow: 0 10px 34px rgba(115,182,255,.4); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 16px 44px rgba(115,182,255,.55); }

    .bar { height: 12px; border-radius: 999px; background: rgba(150,170,255,.12); overflow: hidden; margin-top: 14px; border: 1px solid var(--glass-border); }
    .bar > span { display: block; height: 100%; background: linear-gradient(90deg, var(--cyan), var(--blue), var(--violet)); box-shadow: 0 0 18px rgba(115,182,255,.6); border-radius: 999px; }
    .bar.green > span { background: linear-gradient(90deg, var(--green), #4fd6a0); box-shadow: 0 0 18px rgba(124,255,178,.5); }

    .row { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; }

    /* mission list */
    .mlist { display: grid; gap: 12px; margin-top: 16px; }
    .mitem { display: flex; align-items: center; gap: 14px; padding: 14px; border: 1px solid var(--glass-border); border-radius: 14px; background: rgba(10,12,40,.35); }
    .mitem .dot { width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; font-size: 1.1rem; flex: 0 0 36px; }
    .mitem .mmeta { flex: 1 1 auto; }
    .mitem .mt { font-weight: 600; }
    .mitem .ms { font-size: .8rem; color: var(--muted); }
    .mitem .mp { font-family: 'Space Mono', monospace; font-size: .78rem; color: var(--blue); }

    /* crew */
    .crew-members { display: flex; margin-top: 16px; }
    .crew-members .av { width: 38px; height: 38px; border-radius: 50%; border: 2px solid var(--void); margin-left: -10px; display: grid; place-items: center;
      font-weight: 700; font-size: .78rem; color: #0b1220; }
    .crew-members .av:first-child { margin-left: 0; }

    /* planet progress visual */
    .planet-wrap { display: flex; align-items: center; gap: 20px; margin-top: 14px; }
    .planet-orb { width: 86px; height: 86px; border-radius: 50%; flex: 0 0 86px; position: relative;
      background: radial-gradient(circle at 35% 30%, #d9ff9e, #5f9e1f); box-shadow: 0 0 40px rgba(124,255,178,.4); }
    .planet-orb::after { content:""; position:absolute; inset:0; border-radius:50%; background: radial-gradient(circle at 70% 75%, rgba(11,18,32,.55), transparent 45%); }

    /* exams */
    .exam { display: flex; align-items: center; gap: 14px; padding: 13px 0; border-top: 1px dashed rgba(150,170,255,.12); }
    .exam:first-of-type { border-top: none; }
    .exam .edate { font-family: 'Space Mono', monospace; font-size: .78rem; color: var(--muted); width: 64px; flex: 0 0 64px; }
    .exam .esub { flex: 1 1 auto; font-weight: 600; }
    .exam .estat { font-size: .74rem; padding: 4px 10px; border-radius: 999px; background: rgba(115,182,255,.12); color: var(--blue); }

    @media (max-width: 1100px) { .stats { grid-template-columns: repeat(2, 1fr); } .col-8,.col-6,.col-4 { grid-column: span 12; } }
    @media (max-width: 820px) {
      .app { flex-direction: column; }
      .sidebar { width: 100%; flex: none; height: auto; position: static; flex-direction: row; flex-wrap: wrap; gap: 8px; border-right: none; border-bottom: 1px solid var(--glass-border); }
      .brand { width: 100%; padding-bottom: 12px; }
      .nav { flex-direction: row; flex-wrap: wrap; }
      .side-foot { margin: 0; }
    }
    @media (prefers-reduced-motion: reduce) { *,*::after,*::before { animation: none !important; } }
  </style>
</head>
<body>

  <div class="space" aria-hidden="true">
    <div class="glow g1"></div><div class="glow g2"></div><div class="glow g3"></div>
    <div id="stars"></div>
    <div class="planet p1"></div><div class="planet p2"></div>
  </div>

  <div class="app">
    <!-- SIDEBAR -->
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
        <a class="active" href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg> Dashboard</a>
        <a href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c3 2.2 4.2 6 4.2 10L12 17l-4.2-5C7.8 8 9 4.2 12 2z"/><circle cx="12" cy="9" r="1.6"/><path d="M8.4 16.5 6.5 21l3.5-1.6M15.6 16.5 17.5 21l-3.5-1.6"/></svg> Missions</a>
        <a href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 8l-4 4 4 4M15 8l4 4-4 4"/></svg> Code Lab</a>
        <a href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><ellipse cx="12" cy="12" rx="11" ry="4" transform="rotate(-20 12 12)"/></svg> My Planet</a>
        <a href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="10" r="2.3"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5"/></svg> My Crew</a>
        <a href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l3 3v15H6z"/><path d="M9 12l2 2 4-4"/></svg> Exams</a>
        <a href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5M4 19h16M8 15l3-4 3 2 4-6"/></svg> Progress</a>
        <a href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"/></svg> Settings</a>
      </nav>
      <div class="side-foot">
        <form method="POST" action="/logout">
          @csrf
          <button class="nav-cta" type="submit">Log out</button>
        </form>
      </div>
    </aside>

    <!-- MAIN -->
    <main class="main">
      <div class="topbar">
        <div>
          <div class="crumb">Mission Control</div>
        </div>
        <input class="search" placeholder="Search missions, modules…" />
        <div class="userchip">
          <span class="av">{{ substr(Auth::user()->name, 0, 2) }}</span>
          <span>{{ Auth::user()->name }}</span>
        </div>
      </div>

      <!-- WELCOME -->
      <section class="welcome">
        <div class="wtext">
          <h1>Welcome back, <span class="accent">{{ Auth::user()->name }}</span> 🚀</h1>
          <p>Ready to continue your journey into technology?</p>
          <span class="lvl">★ Level 7 · 1,240 XP</span>
        </div>
        <svg class="mascot" viewBox="0 0 200 200" aria-hidden="true">
          <defs>
            <radialGradient id="dome" cx="38%" cy="32%" r="70%"><stop offset="0%" stop-color="#fdfdff"/><stop offset="60%" stop-color="#c9d4ff"/><stop offset="100%" stop-color="#8a98d8"/></radialGradient>
            <linearGradient id="visor" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#5be1ff"/><stop offset="50%" stop-color="#73b6ff"/><stop offset="100%" stop-color="#9b6bff"/></linearGradient>
          </defs>
          <circle cx="100" cy="100" r="78" fill="url(#dome)" stroke="#aeb9ef" stroke-width="3"/>
          <path d="M52 96a48 48 0 0 1 96 0a48 40 0 0 1 -96 0z" fill="url(#visor)" opacity="0.92"/>
          <ellipse cx="82" cy="80" rx="16" ry="10" fill="#ffffff" opacity="0.55"/>
          <circle cx="150" cy="58" r="7" fill="#9b6bff"/>
          <line x1="150" y1="58" x2="150" y2="34" stroke="#9b6bff" stroke-width="4" stroke-linecap="round"/>
          <circle cx="150" cy="32" r="5" fill="#5be1ff"/>
        </svg>
      </section>

      <!-- STATS -->
      <section class="stats">
        <div class="card stat"><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c3 2.2 4.2 6 4.2 10L12 17l-4.2-5C7.8 8 9 4.2 12 2z"/></svg></div><div class="v">24</div><div class="k">Missions Completed</div></div>
        <div class="card stat"><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><ellipse cx="12" cy="12" rx="11" ry="4" transform="rotate(-20 12 12)"/></svg></div><div class="v">68%</div><div class="k">Planet Progress</div></div>
        <div class="card stat"><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c1 3-1 4-1 6a3 3 0 0 0 6 0c0-1-.5-2-1-3 2 1 4 3 4 6a7 7 0 0 1-14 0c0-4 3-6 6-9z"/></svg></div><div class="v">12</div><div class="k">Learning Streak (days)</div></div>
        <div class="card stat"><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M7 10v4M11 10v4M15 10v4"/></svg></div><div class="v">62<small style="font-size:.7rem"> WPM</small></div><div class="k">Coding Speed</div></div>
        <div class="card stat"><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.5 6 6.5.5-5 4.3 1.6 6.2L12 16.8 6.4 19l1.6-6.2-5-4.3 6.5-.5z"/></svg></div><div class="v">1,240</div><div class="k">XP · Level 7</div></div>
      </section>

      <!-- CONTINUE MISSION + PLANET -->
      <section class="grid" style="margin-bottom:20px">
        <div class="card col-8">
          <div class="tag">Continue Mission</div>
          <h3 style="margin-top:6px">Cybersecurity · Module 2</h3>
          <p class="sub">Network Defense Basics — your captain will attach the full module here.</p>
          <div class="bar"><span style="width:45%"></span></div>
          <div class="row" style="margin-top:18px">
            <span class="sub">45% complete</span>
            <a class="btn btn-primary" href="#">Continue Mission →</a>
          </div>
        </div>
        <div class="card col-4">
          <div class="tag">Planet Progress</div>
          <div class="planet-wrap">
            <div class="planet-orb"></div>
            <div>
              <div style="font-family:'Space Grotesk';font-weight:700;font-size:1.4rem">68%</div>
              <div class="sub">terraformed</div>
            </div>
          </div>
          <div class="bar green" style="margin-top:14px"><span style="width:68%"></span></div>
        </div>
      </section>

      <!-- MY MISSIONS + CODE TRAINING -->
      <section class="grid" style="margin-bottom:20px">
        <div class="card col-8">
          <div class="row"><h3>My Missions</h3><span class="tag">Tracks</span></div>
          <div class="mlist">
            <div class="mitem"><div class="dot" style="background:rgba(115,182,255,.18)">💻</div><div class="mmeta"><div class="mt">Programming</div><div class="ms">Syntax · Functions · Projects</div></div><div class="mp">0%</div></div>
            <div class="mitem"><div class="dot" style="background:rgba(91,225,255,.18)">🌐</div><div class="mmeta"><div class="mt">Networking</div><div class="ms">Routing · Protocols · Security</div></div><div class="mp">0%</div></div>
            <div class="mitem"><div class="dot" style="background:rgba(155,107,255,.18)">🛡️</div><div class="mmeta"><div class="mt">Cybersecurity</div><div class="ms">Defense · Crypto · Response</div></div><div class="mp">45%</div></div>
          </div>
        </div>
        <div class="card col-4">
          <div class="tag">Code Training</div>
          <h3 style="margin-top:6px">Typing Drills</h3>
          <p class="sub">Build muscle memory with real code.</p>
          <div style="margin-top:14px" class="row"><span class="sub">62 WPM</span><span class="sub">94% acc</span></div>
          <div class="bar"><span style="width:62%"></span></div>
          <a class="btn btn-primary" href="#" style="margin-top:18px;width:100%;justify-content:center">Start Training →</a>
        </div>
      </section>

      <!-- MY CREW + UPCOMING EXAMS -->
      <section class="grid">
        <div class="card col-6">
          <div class="tag">My Crew</div>
          <h3 style="margin-top:6px">Nebula-7</h3>
          <p class="sub">Shared planet grows with the squad's progress.</p>
          <div class="crew-members">
            <div class="av" style="background:#73b6ff">{{ substr(Auth::user()->name,0,2) }}</div>
            <div class="av" style="background:#5be1ff">MK</div>
            <div class="av" style="background:#9b6bff">JD</div>
            <div class="av" style="background:#7cffb2">+3</div>
          </div>
          <div class="bar" style="margin-top:16px"><span style="width:40%"></span></div>
          <div class="sub" style="margin-top:8px">40% crew progress</div>
        </div>
        <div class="card col-6">
          <div class="tag">Upcoming Exams</div>
          <h3 style="margin-top:6px">Mission Checkpoints</h3>
          <div class="exam"><span class="edate">Aug 20</span><span class="esub">Cybersecurity · Module 2</span><span class="estat">Scheduled</span></div>
          <div class="exam"><span class="edate">Aug 27</span><span class="esub">Programming · Midterm</span><span class="estat">Scheduled</span></div>
          <div class="exam"><span class="edate">Sep 03</span><span class="esub">Networking · Quiz</span><span class="estat">Open</span></div>
        </div>
      </section>
    </main>
  </div>

  <script>
    (function () {
      const field = document.getElementById('stars');
      const count = window.innerWidth < 700 ? 90 : 170;
      let seed = 2026; const rnd = () => { seed = (seed * 1103515245 + 12345) & 0x7fffffff; return seed / 0x7fffffff; };
      const frag = document.createDocumentFragment();
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
  </script>
</body>
</html>
