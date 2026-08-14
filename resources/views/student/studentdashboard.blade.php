<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mission Control · TechLab</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <meta name="description" content="TechLab student dashboard — your learning journey, mission by mission." />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />

  <style>
    :root {
      --void: #06061a;
      --cosmic-1: #120a33;
      --cosmic-2: #1e1259;
      --nebula: #2d1b69;
      --blue: #73b6ff;
      --violet: #9b6bff;
      --cyan: #5be1ff;
      --text: #eaeeff;
      --muted: #98a2d4;
      --glass: rgba(123, 142, 220, 0.07);
      --glass-border: rgba(150, 170, 255, 0.18);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body {
      font-family: 'Inter', system-ui, sans-serif;
      color: var(--text); background: var(--void);
      overflow-x: hidden; -webkit-font-smoothing: antialiased; line-height: 1.6;
    }
    a { color: inherit; text-decoration: none; }

    /* ---------- Space background (matches landing) ---------- */
    .space { position: fixed; inset: 0; z-index: -1; overflow: hidden;
      background:
        radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%),
        radial-gradient(100% 80% at 85% 110%, #1a0f4d 0%, rgba(26,15,77,0) 60%),
        linear-gradient(160deg, #0a0826 0%, #120a33 45%, #1e1259 100%); }
    .glow { position: absolute; border-radius: 50%; filter: blur(70px); opacity: 0.55; animation: pulse 9s ease-in-out infinite; }
    .glow.g1 { width: 460px; height: 460px; top: -120px; left: -80px; background: radial-gradient(circle, rgba(155,107,255,0.7), transparent 70%); }
    .glow.g2 { width: 520px; height: 520px; bottom: -160px; right: -120px; background: radial-gradient(circle, rgba(91,225,255,0.45), transparent 70%); animation-delay: -4s; }
    .glow.g3 { width: 380px; height: 380px; top: 40%; left: 55%; background: radial-gradient(circle, rgba(115,182,255,0.35), transparent 70%); animation-delay: -2s; }
    @keyframes pulse { 0%,100% { transform: scale(1); opacity: .5; } 50% { transform: scale(1.12); opacity: .7; } }
    .star { position: absolute; width: 2px; height: 2px; border-radius: 50%; background: #fff; opacity: .8; animation: twinkle var(--dur,4s) ease-in-out infinite; animation-delay: var(--delay,0s); }
    @keyframes twinkle { 0%,100% { opacity: .15; transform: scale(.7); } 50% { opacity: 1; transform: scale(1.2); } }

    /* ---------- Nav (matches landing) ---------- */
    .nav { position: sticky; top: 0; z-index: 50; display: flex; align-items: center; justify-content: space-between;
      padding: 22px clamp(20px, 5vw, 64px); backdrop-filter: blur(10px);
      background: linear-gradient(180deg, rgba(6,6,26,0.6), rgba(6,6,26,0)); }
    .brand { display: flex; align-items: center; gap: 12px; }
    .brand svg { width: 34px; height: 34px; filter: drop-shadow(0 0 10px rgba(115,182,255,0.5)); }
    .brand .name { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.25rem; letter-spacing: -0.01em; }
    .brand .name span { color: var(--blue); }
    .nav-links { display: flex; align-items: center; gap: 30px; }
    .nav-links a { font-size: 0.95rem; color: var(--muted); transition: color .2s; }
    .nav-links a:hover, .nav-links a.active { color: var(--text); }
    .nav-cta { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 0.9rem; padding: 10px 20px; border-radius: 999px;
      background: rgba(115,182,255,0.12); border: 1px solid var(--glass-border); color: var(--text); transition: all .25s; cursor: pointer; }
    .nav-cta:hover { background: rgba(115,182,255,0.22); transform: translateY(-1px); }

    /* ---------- Dashboard layout ---------- */
    .dash-wrap { position: relative; z-index: 2; max-width: 1180px; margin: 0 auto; padding: 26px clamp(20px,5vw,64px) 80px; }
    .welcome { display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; margin-bottom: 18px; }
    .welcome h1 { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: clamp(1.8rem, 4vw, 2.8rem); letter-spacing: -0.03em; }
    .welcome h1 .accent { background: linear-gradient(100deg, var(--cyan), var(--blue) 45%, var(--violet)); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .level-badge { display: inline-flex; align-items: center; gap: 14px; background: var(--glass); border: 1px solid var(--glass-border); border-radius: 999px; padding: 10px 20px; backdrop-filter: blur(12px); }
    .level-badge .lb-num { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.4rem; color: var(--cyan); }
    .level-badge .lb-lvl { font-size: .7rem; letter-spacing: .18em; text-transform: uppercase; color: var(--muted); }
    .level-badge .lb-title { font-family: 'Space Grotesk', sans-serif; font-weight: 600; }

    .dash-layout { display: grid; grid-template-columns: 1fr 320px; gap: 30px; align-items: start; }

    /* companion mascot callout */
    .companion { display: flex; align-items: center; gap: 16px; background: linear-gradient(135deg, rgba(115,182,255,.16), var(--glass)); border: 1px solid var(--glass-border); border-radius: 20px; padding: 16px 20px; margin-bottom: 18px; backdrop-filter: blur(12px); }
    .companion .mascot { width: 64px; height: 64px; flex: 0 0 64px; filter: drop-shadow(0 10px 24px rgba(115,182,255,.4)); }
    .companion .c-text .c-eyebrow { font-family: 'Space Mono', monospace; font-size: .72rem; letter-spacing: .14em; text-transform: uppercase; color: var(--cyan); }
    .companion .c-text .c-msg { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 1.05rem; margin-top: 2px; }

    /* ---------- Learning path ---------- */
    .path { position: relative; display: flex; flex-direction: column; align-items: center; padding: 6px 0; }
    .path::before { content: ""; position: absolute; top: 24px; bottom: 44px; left: 50%; width: 4px; transform: translateX(-50%);
      background: linear-gradient(180deg, var(--blue), var(--violet), var(--cyan)); opacity: .5; border-radius: 4px; }
    .connector { color: var(--muted); font-size: 1.2rem; margin: 4px 0; z-index: 1; }
    .node { position: relative; z-index: 1; width: min(440px, 94%); margin: 10px 0; border-radius: 22px; padding: 18px 20px;
      background: var(--glass); border: 1px solid var(--glass-border); backdrop-filter: blur(12px); transition: transform .25s, box-shadow .25s, border-color .25s; }
    .node.completed { opacity: .92; }
    .node.current { border-color: var(--blue); box-shadow: 0 0 0 1px var(--blue), 0 22px 54px rgba(115,182,255,.28); transform: scale(1.03); }
    .node.milestone { border-color: var(--violet); background: linear-gradient(135deg, rgba(155,107,255,.20), var(--glass)); }
    .node.loot { border-color: var(--cyan); }
    .node.locked { opacity: .55; }
    .node-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
    .node-tag { font-family: 'Space Mono', monospace; font-size: .72rem; letter-spacing: .14em; text-transform: uppercase; color: var(--cyan); }
    .node.milestone .node-tag { color: var(--violet); }
    .node.loot .node-tag { color: var(--cyan); }
    .node-status { font-size: 1.1rem; }
    .node-status.live { color: var(--blue); animation: pulse 1.6s ease-in-out infinite; }
    .node-title { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.25rem; letter-spacing: -0.01em; }
    .node-sub { color: var(--muted); font-size: .9rem; margin-top: 2px; }
    .node-desc { color: var(--muted); font-size: .92rem; margin: 8px 0; }
    .node-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 12px; flex-wrap: wrap; }
    .xp { font-family: 'Space Mono', monospace; font-size: .8rem; color: var(--blue); letter-spacing: .04em; }
    .xp.locked { color: var(--muted); }
    .node-btn { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: .85rem; padding: 10px 18px; border-radius: 999px; border: none; cursor: pointer; text-decoration: none;
      color: #07142e; background: linear-gradient(100deg, var(--cyan), var(--blue) 55%, var(--violet)); box-shadow: 0 8px 26px rgba(115,182,255,.4); white-space: nowrap; }
    .node-btn:hover { transform: translateY(-2px); }
    .node-btn.ghost { background: rgba(115,182,255,.12); color: var(--text); border: 1px solid var(--glass-border); box-shadow: none; }
    .path-end { margin-top: 14px; font-family: 'Space Grotesk', sans-serif; font-weight: 700; letter-spacing: .2em; color: var(--violet); }

    /* ---------- Sidebar ---------- */
    .sidebar { display: flex; flex-direction: column; gap: 16px; }
    .stat-card { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 18px; padding: 18px 20px; backdrop-filter: blur(12px); }
    .stat-card .k { font-family: 'Space Mono', monospace; font-size: .7rem; letter-spacing: .16em; text-transform: uppercase; color: var(--muted); margin-bottom: 10px; }
    .profile { display: flex; align-items: center; gap: 12px; }
    .profile .avatar { width: 46px; height: 46px; border-radius: 50%; display: grid; place-items: center; font-family: 'Space Grotesk', sans-serif; font-weight: 700; color: #07142e; background: linear-gradient(135deg, var(--cyan), var(--blue)); flex: 0 0 46px; }
    .profile .hey { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 1.05rem; }
    .profile .lvl { color: var(--muted); font-size: .85rem; }

    .level-progress .lp-head { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 8px; }
    .lp-level { font-family: 'Space Grotesk', sans-serif; font-weight: 700; color: var(--cyan); }
    .lp-title { color: var(--muted); font-size: .85rem; }
    .level-bar { height: 10px; border-radius: 999px; background: rgba(150,170,255,.12); overflow: hidden; border: 1px solid var(--glass-border); }
    .level-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--cyan), var(--blue), var(--violet)); box-shadow: 0 0 14px rgba(115,182,255,.6); transition: width 1s ease; }
    .lp-xp { color: var(--muted); font-size: .8rem; margin-top: 8px; font-family: 'Space Mono', monospace; }

    .streak-num { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.1rem; color: #ffb86b; }
    .streak-sub { color: var(--muted); font-size: .82rem; }

    .achs { display: flex; flex-direction: column; gap: 8px; }
    .ach { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 12px; background: rgba(10,12,40,.4); border: 1px solid var(--glass-border); }
    .ach-icon { font-size: 1.1rem; }
    .ach-label { font-size: .88rem; }

    .dq-label { font-family: 'Space Grotesk', sans-serif; font-weight: 600; margin-bottom: 10px; }
    .dq-count { font-family: 'Space Mono', monospace; font-size: .8rem; color: var(--muted); margin-top: 8px; }

    /* ---------- Footer ---------- */
    footer { position: relative; z-index: 2; border-top: 1px solid var(--glass-border); padding: 30px clamp(20px,5vw,64px);
      display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; color: var(--muted); font-size: 0.9rem; }
    footer .brand .name { font-size: 1.05rem; }
    footer .links { display: flex; gap: 22px; }
    footer .links a:hover { color: var(--text); }

    @media (max-width: 900px) {
      .dash-layout { grid-template-columns: 1fr; }
      .sidebar { order: 2; }
      .nav-links { display: none; }
    }
    @media (max-width: 560px) {
      .node { width: 100%; }
      .welcome { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body>

  <!-- Space background -->
  <div class="space" aria-hidden="true">
    <div class="glow g1"></div>
    <div class="glow g2"></div>
    <div class="glow g3"></div>
    <div class="layer-stars" id="stars"></div>
  </div>

  <!-- Nav -->
  <header class="nav">
    <a class="brand" href="/" aria-label="TechLab home">
      <svg viewBox="0 0 108.89 108.89" role="img" aria-hidden="true">
        <polygon fill="#73b6ff" points="37.55 108.89 52.56 108.89 52.56 90.12 90.12 90.12 90.12 75.09 52.56 75.09 52.56 56.33 37.55 56.33 37.55 108.89"/>
        <polygon fill="#73b6ff" points="108.89 71.34 108.89 56.33 90.12 56.33 90.12 18.78 75.09 18.78 75.09 56.33 56.33 56.33 56.33 71.34 108.89 71.34"/>
        <polygon fill="#73b6ff" points="71.34 0 56.33 0 56.33 18.78 18.78 18.78 18.78 33.79 56.33 33.79 56.33 52.56 71.34 52.56 71.34 0"/>
        <polygon fill="#73b6ff" points="0 37.55 0 52.56 18.78 52.56 18.78 90.12 33.79 90.12 33.79 52.56 52.56 52.56 52.56 37.55 0 37.55"/>
      </svg>
      <span class="name">Tech<span>Lab</span></span>
    </a>
    <nav class="nav-links">
      <a href="#" class="active">Learn</a>
      <a href="#">Quests</a>
      <a href="#">Achievements</a>
      <a href="#">Leaderboard</a>
      <a href="/student/crew">Crew</a>
      <a href="#">Profile</a>
    </nav>
    <form method="POST" action="/logout" style="margin:0">
      @csrf
      <button class="nav-cta" type="submit">Log out</button>
    </form>
  </header>

  <!-- Dashboard -->
  <main class="dash-wrap">
    <div class="welcome">
      <h1>Welcome back, <span class="accent">{{ $student['name'] ?? 'Explorer' }}</span>.</h1>
      <div class="level-badge">
        <span class="lb-num">{{ $student['level'] ?? 1 }}</span>
        <span>
          <span class="lb-lvl">Level</span><br>
          <span class="lb-title">{{ $student['levelTitle'] ?? 'Explorer' }}</span>
        </span>
      </div>
    </div>

    <div class="dash-layout">
      <!-- Main learning journey -->
      <section>
        <div class="companion">
          <svg class="mascot" viewBox="0 0 200 200" aria-hidden="true">
            <defs>
              <radialGradient id="domeC" cx="38%" cy="32%" r="70%"><stop offset="0%" stop-color="#fdfdff"/><stop offset="60%" stop-color="#c9d4ff"/><stop offset="100%" stop-color="#8a98d8"/></radialGradient>
              <linearGradient id="visorC" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#5be1ff"/><stop offset="50%" stop-color="#73b6ff"/><stop offset="100%" stop-color="#9b6bff"/></linearGradient>
            </defs>
            <circle cx="100" cy="100" r="78" fill="url(#domeC)" stroke="#aeb9ef" stroke-width="3"/>
            <path d="M52 96 a48 48 0 0 1 96 0 a48 40 0 0 1 -96 0 z" fill="url(#visorC)" opacity="0.92"/>
            <ellipse cx="82" cy="80" rx="16" ry="10" fill="#ffffff" opacity="0.55"/>
            <circle cx="150" cy="58" r="7" fill="#9b6bff"/>
            <line x1="150" y1="58" x2="150" y2="34" stroke="#9b6bff" stroke-width="4" stroke-linecap="round"/>
            <circle cx="150" cy="32" r="5" fill="#5be1ff"/>
          </svg>
          <div class="c-text">
            <div class="c-eyebrow">Mission Control</div>
            <div class="c-msg">Ready for your next challenge?</div>
          </div>
        </div>

        <x-learning-path :nodes="$nodes" />
      </section>

      <!-- Sidebar -->
      <x-student-stats :student="$student" />
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <a class="brand" href="/">
      <svg viewBox="0 0 108.89 108.89" aria-hidden="true" style="width:26px;height:26px">
        <polygon fill="#73b6ff" points="37.55 108.89 52.56 108.89 52.56 90.12 90.12 90.12 90.12 75.09 52.56 75.09 52.56 56.33 37.55 56.33 37.55 108.89"/>
        <polygon fill="#73b6ff" points="108.89 71.34 108.89 56.33 90.12 56.33 90.12 18.78 75.09 18.78 75.09 56.33 56.33 56.33 56.33 71.34 108.89 71.34"/>
        <polygon fill="#73b6ff" points="71.34 0 56.33 0 56.33 18.78 18.78 18.78 18.78 33.79 56.33 33.79 56.33 52.56 71.34 52.56 71.34 0"/>
        <polygon fill="#73b6ff" points="0 37.55 0 52.56 18.78 52.56 18.78 90.12 33.79 90.12 33.79 52.56 52.56 52.56 52.56 37.55 0 37.55"/>
      </svg>
      <span class="name">Tech<span>Lab</span></span>
    </a>
    <div class="links">
      <a href="#">Learn</a>
      <a href="#">Quests</a>
      <a href="#">Achievements</a>
    </div>
    <span>© 2026 TechLab — Learn. Build. Explore.</span>
  </footer>

  <script>
    (function () {
      const field = document.getElementById('stars');
      const count = window.innerWidth < 700 ? 90 : 170;
      const frag = document.createDocumentFragment();
      let seed = 1337;
      const rnd = () => { seed = (seed * 1103515245 + 12345) & 0x3fffffff; return seed / 0x3fffffff; };
      for (let i = 0; i < count; i++) {
        const s = document.createElement('span');
        s.className = 'star';
        const size = rnd() > 0.85 ? 3 : 2;
        s.style.width = s.style.height = size + 'px';
        s.style.left = (rnd() * 100) + 'vw';
        s.style.top = (rnd() * 100) + 'vh';
        s.style.setProperty('--dur', (2.5 + rnd() * 4).toFixed(2) + 's');
        s.style.setProperty('--delay', (rnd() * 5).toFixed(2) + 's');
        frag.appendChild(s);
      }
      field.appendChild(frag);
    })();
  </script>
</body>
</html>
