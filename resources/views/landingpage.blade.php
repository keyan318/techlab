<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TechLab — Learn. Build. Explore.</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <meta name="description" content="TechLab is an EdTech platform where students learn programming, networking, cybersecurity, and more — through courses, projects, practice, and crews." />

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
      color: var(--text);
      background: var(--void);
      overflow-x: hidden;
      -webkit-font-smoothing: antialiased;
      line-height: 1.6;
    }

    a { color: inherit; text-decoration: none; }

    /* ---------- Space background layers ---------- */
    .space {
      position: fixed;
      inset: 0;
      z-index: -1;
      overflow: hidden;
      background:
        radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%),
        radial-gradient(100% 80% at 85% 110%, #1a0f4d 0%, rgba(26,15,77,0) 60%),
        linear-gradient(160deg, #0a0826 0%, #120a33 45%, #1e1259 100%);
    }

    .layer { position: absolute; inset: 0; will-change: transform; }

    .glow {
      position: absolute;
      border-radius: 50%;
      filter: blur(70px);
      opacity: 0.55;
      animation: pulse 9s ease-in-out infinite;
    }
    .glow.g1 { width: 460px; height: 460px; top: -120px; left: -80px;
      background: radial-gradient(circle, rgba(155,107,255,0.7), transparent 70%); }
    .glow.g2 { width: 520px; height: 520px; bottom: -160px; right: -120px;
      background: radial-gradient(circle, rgba(91,225,255,0.45), transparent 70%); animation-delay: -4s; }
    .glow.g3 { width: 380px; height: 380px; top: 40%; left: 55%;
      background: radial-gradient(circle, rgba(115,182,255,0.35), transparent 70%); animation-delay: -2s; }

    @keyframes pulse {
      0%, 100% { transform: scale(1); opacity: 0.5; }
      50% { transform: scale(1.12); opacity: 0.7; }
    }

    .star {
      position: absolute;
      width: 2px; height: 2px;
      border-radius: 50%;
      background: #fff;
      opacity: 0.8;
      animation: twinkle var(--dur, 4s) ease-in-out infinite;
      animation-delay: var(--delay, 0s);
    }
    @keyframes twinkle {
      0%, 100% { opacity: 0.15; transform: scale(0.7); }
      50% { opacity: 1; transform: scale(1.2); }
    }

    /* ---------- Planets ---------- */
    .planet { position: absolute; border-radius: 50%; will-change: transform; }
    .planet::after {
      content: ""; position: absolute; inset: 0; border-radius: 50%;
      box-shadow: inset -18px -18px 40px rgba(0,0,0,0.45);
    }

    .p1 {
      width: 220px; height: 220px; top: 12%; left: 8%;
      background: radial-gradient(circle at 35% 30%, #b9a3ff, #6c4bd6 55%, #2e1a73);
      box-shadow: 0 0 80px rgba(124,75,214,0.55);
      animation: float 14s ease-in-out infinite;
    }
    .p2 {
      width: 130px; height: 130px; bottom: 14%; right: 10%;
      background: radial-gradient(circle at 35% 30%, #aef0ff, #4fc8ee 55%, #1d6f99);
      box-shadow: 0 0 70px rgba(91,225,255,0.5);
      animation: float 11s ease-in-out infinite reverse;
    }
    .p3 {
      width: 70px; height: 70px; top: 62%; left: 18%;
      background: radial-gradient(circle at 35% 30%, #ffd0e8, #ff7bc4 55%, #b23a86);
      box-shadow: 0 0 50px rgba(255,123,196,0.45);
      animation: float 9s ease-in-out infinite;
    }
    .p4 {
      width: 46px; height: 46px; top: 22%; right: 24%;
      background: radial-gradient(circle at 35% 30%, #fff0c2, #ffcf6b 55%, #c98a2e);
      box-shadow: 0 0 40px rgba(255,207,107,0.4);
      animation: float 7s ease-in-out infinite reverse;
    }

    /* ringed planet */
    .ringed { position: absolute; top: 30%; right: 6%; width: 90px; height: 90px; will-change: transform; animation: float 13s ease-in-out infinite; }
    .ringed .core {
      position: absolute; inset: 0; border-radius: 50%;
      background: radial-gradient(circle at 35% 30%, #d7c4ff, #8b6bff 60%, #3a2a8f);
      box-shadow: 0 0 50px rgba(139,107,255,0.5);
    }
    .ringed .ring {
      position: absolute; top: 50%; left: 50%; width: 170px; height: 46px;
      border: 6px solid rgba(155,107,255,0.45); border-radius: 50%;
      transform: translate(-50%,-50%) rotate(-22deg);
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-26px); }
    }

    /* ---------- Nav ---------- */
    .nav {
      position: sticky; top: 0; z-index: 50;
      display: flex; align-items: center; justify-content: space-between;
      padding: 22px clamp(20px, 5vw, 64px);
      backdrop-filter: blur(10px);
      background: linear-gradient(180deg, rgba(6,6,26,0.6), rgba(6,6,26,0));
    }
    .brand { display: flex; align-items: center; gap: 12px; }
    .brand svg { width: 34px; height: 34px; filter: drop-shadow(0 0 10px rgba(115,182,255,0.5)); }
    .brand .name { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.25rem; letter-spacing: -0.01em; }
    .brand .name span { color: var(--blue); }

    .nav-links { display: flex; align-items: center; gap: 30px; }
    .nav-links a { font-size: 0.95rem; color: var(--muted); transition: color .2s; }
    .nav-links a:hover { color: var(--text); }

    .nav-cta {
      font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 0.9rem;
      padding: 10px 20px; border-radius: 999px;
      background: rgba(115,182,255,0.12); border: 1px solid var(--glass-border);
      color: var(--text); transition: all .25s;
    }
    .nav-cta:hover { background: rgba(115,182,255,0.22); transform: translateY(-1px); }

    /* ---------- Hero ---------- */
    .hero {
      position: relative;
      min-height: calc(100vh - 86px);
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      text-align: center;
      padding: 40px clamp(20px, 5vw, 64px) 80px;
    }

    .eyebrow {
      font-family: 'Space Mono', monospace; font-size: 0.78rem; letter-spacing: 0.32em;
      text-transform: uppercase; color: var(--cyan);
      padding: 8px 16px; border: 1px solid var(--glass-border); border-radius: 999px;
      background: var(--glass); margin-bottom: 28px;
    }

    .hero h1 {
      font-family: 'Space Grotesk', sans-serif; font-weight: 700;
      font-size: clamp(2.6rem, 6.4vw, 5.2rem); line-height: 1.03; letter-spacing: -0.03em;
      max-width: 16ch; margin: 0 auto;
    }
    .hero h1 .journey {
      background: linear-gradient(100deg, var(--cyan), var(--blue) 45%, var(--violet));
      -webkit-background-clip: text; background-clip: text; color: transparent;
    }

    .hero p.sub {
      font-size: clamp(1.02rem, 2vw, 1.3rem); color: var(--muted);
      max-width: 56ch; margin: 24px auto 0;
    }

    .ctas { display: flex; gap: 16px; flex-wrap: wrap; justify-content: center; margin-top: 40px; }

    .btn {
      font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 1rem;
      padding: 15px 30px; border-radius: 999px; cursor: pointer; border: none;
      transition: transform .25s, box-shadow .25s, background .25s;
      display: inline-flex; align-items: center; gap: 10px;
    }
    .btn-primary {
      color: #07142e;
      background: linear-gradient(100deg, var(--cyan), var(--blue) 55%, var(--violet));
      box-shadow: 0 10px 40px rgba(115,182,255,0.45);
    }
    .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 16px 50px rgba(115,182,255,0.6); }
    .btn-secondary {
      color: var(--text); background: transparent; border: 1px solid var(--glass-border);
      backdrop-filter: blur(6px);
    }
    .btn-secondary:hover { background: var(--glass); transform: translateY(-3px); }

    .btn .arrow { transition: transform .25s; }
    .btn:hover .arrow { transform: translateX(4px); }

    .login-hint { margin-top: 22px; font-size: 0.92rem; color: var(--muted); }
    .login-hint a { color: var(--blue); font-weight: 600; }
    .login-hint a:hover { text-decoration: underline; }
    .logout-form, .nav-logout { display: inline-flex; margin: 0; }

    /* ---------- Helmet mascot (foreground, no hands) ---------- */
    .helmet {
      position: absolute; z-index: 2;
      width: clamp(90px, 14vw, 180px);
      top: 8%; right: 12%;
      animation: bob 6s ease-in-out infinite;
      filter: drop-shadow(0 20px 50px rgba(115,182,255,0.35));
      will-change: transform;
    }
    @keyframes bob {
      0%, 100% { transform: translateY(0) rotate(-3deg); }
      50% { transform: translateY(-22px) rotate(3deg); }
    }

    /* ---------- Features ---------- */
    .features {
      position: relative; z-index: 2;
      max-width: 1100px; margin: 0 auto;
      padding: 40px clamp(20px, 5vw, 64px) 40px;
      display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px;
    }
    .card {
      background: var(--glass); border: 1px solid var(--glass-border);
      border-radius: 20px; padding: 30px 26px;
      backdrop-filter: blur(12px);
      transition: transform .3s, border-color .3s, box-shadow .3s;
    }
    .card:hover { transform: translateY(-6px); border-color: rgba(115,182,255,0.5);
      box-shadow: 0 20px 50px rgba(10,10,40,0.5); }
    .card .ico {
      width: 48px; height: 48px; border-radius: 14px; margin-bottom: 18px;
      display: grid; place-items: center;
      background: linear-gradient(135deg, rgba(115,182,255,0.18), rgba(155,107,255,0.18));
      border: 1px solid var(--glass-border);
    }
    .card .ico svg { width: 24px; height: 24px; stroke: var(--blue); }
    .card h3 { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 1.2rem; margin-bottom: 8px; }
    .card .tag { font-family: 'Space Mono', monospace; font-size: 0.72rem; letter-spacing: 0.18em;
      text-transform: uppercase; color: var(--cyan); margin-bottom: 12px; }
    .card p { color: var(--muted); font-size: 0.95rem; }

    /* ---------- Progress strip ---------- */
    .progress-wrap {
      position: relative; z-index: 2;
      max-width: 1100px; margin: 10px auto 0;
      padding: 0 clamp(20px, 5vw, 64px) 90px;
    }
    .progress {
      background: var(--glass); border: 1px solid var(--glass-border);
      border-radius: 20px; padding: 28px 32px; backdrop-filter: blur(12px);
      display: flex; align-items: center; gap: 28px; flex-wrap: wrap;
    }
    .progress .ptext { flex: 1 1 280px; }
    .progress .ptext .tag { font-family: 'Space Mono', monospace; font-size: 0.72rem;
      letter-spacing: 0.18em; text-transform: uppercase; color: var(--violet); }
    .progress .ptext h3 { font-family: 'Space Grotesk', sans-serif; font-size: 1.35rem; margin-top: 6px; }
    .progress .ptext p { color: var(--muted); font-size: 0.95rem; margin-top: 6px; }
    .bar { flex: 1 1 260px; }
    .bar .track { height: 14px; border-radius: 999px; background: rgba(150,170,255,0.12);
      overflow: hidden; border: 1px solid var(--glass-border); }
    .bar .fill { height: 100%; width: 0; border-radius: 999px;
      background: linear-gradient(90deg, var(--cyan), var(--blue), var(--violet));
      box-shadow: 0 0 20px rgba(115,182,255,0.6);
      transition: width 1.6s cubic-bezier(.2,.8,.2,1); }
    .bar .meta { display: flex; justify-content: space-between; margin-top: 10px;
      font-family: 'Space Mono', monospace; font-size: 0.78rem; color: var(--muted); }

    /* ---------- Footer ---------- */
    footer {
      position: relative; z-index: 2;
      border-top: 1px solid var(--glass-border);
      padding: 30px clamp(20px, 5vw, 64px);
      display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;
      color: var(--muted); font-size: 0.9rem;
    }
    footer .brand .name { font-size: 1.05rem; }
    footer .links { display: flex; gap: 22px; }
    footer .links a:hover { color: var(--text); }

    /* ---------- Entrance animation ---------- */
    .reveal { opacity: 0; transform: translateY(24px); transition: opacity .8s ease, transform .8s ease; }
    body.loaded .reveal { opacity: 1; transform: translateY(0); }
    body.loaded .reveal.d1 { transition-delay: .1s; }
    body.loaded .reveal.d2 { transition-delay: .25s; }
    body.loaded .reveal.d3 { transition-delay: .4s; }
    body.loaded .reveal.d4 { transition-delay: .55s; }

    /* ---------- Responsive ---------- */
    @media (max-width: 860px) {
      .features { grid-template-columns: 1fr; }
      .nav-links { display: none; }
      .helmet { top: auto; bottom: -10px; right: 6%; width: 90px; opacity: 0.85; }
    }

    @media (prefers-reduced-motion: reduce) {
      *, *::after, *::before { animation: none !important; transition: none !important; }
      .reveal { opacity: 1; transform: none; }
      .bar .fill { width: 68% !important; }
    }
  </style>
</head>
<body>

  <!-- Space background -->
  <div class="space" aria-hidden="true">
    <div class="layer layer-glow">
      <div class="glow g1"></div>
      <div class="glow g2"></div>
      <div class="glow g3"></div>
    </div>
    <div class="layer layer-stars" id="stars"></div>
    <div class="layer layer-planets" id="planets">
      <div class="planet p1"></div>
      <div class="planet p2"></div>
      <div class="planet p3"></div>
      <div class="planet p4"></div>
      <div class="ringed"><div class="core"></div><div class="ring"></div></div>
    </div>
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
      <a href="#features">Courses</a>
      <a href="#features">Missions</a>
      <a href="#features">Crews</a>
      <a href="#progress">Progress</a>
    </nav>
    @guest
      <a class="nav-cta" href="/register">Start Exploring</a>
    @else
      <form method="POST" action="/logout" class="nav-logout">
        @csrf
        <button class="nav-cta" type="submit">Log out</button>
      </form>
    @endguest
  </header>

  <!-- Hero -->
  <main class="hero" id="cta">
    <!-- Foreground helmet mascot (no hands) -->
    <svg class="helmet" viewBox="0 0 200 200" aria-hidden="true">
      <defs>
        <radialGradient id="dome" cx="38%" cy="32%" r="70%">
          <stop offset="0%" stop-color="#fdfdff"/>
          <stop offset="60%" stop-color="#c9d4ff"/>
          <stop offset="100%" stop-color="#8a98d8"/>
        </radialGradient>
        <linearGradient id="visor" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%" stop-color="#5be1ff"/>
          <stop offset="50%" stop-color="#73b6ff"/>
          <stop offset="100%" stop-color="#9b6bff"/>
        </linearGradient>
      </defs>
      <circle cx="100" cy="100" r="78" fill="url(#dome)" stroke="#aeb9ef" stroke-width="3"/>
      <path d="M52 96 a48 48 0 0 1 96 0 a48 40 0 0 1 -96 0 z" fill="url(#visor)" opacity="0.92"/>
      <ellipse cx="82" cy="80" rx="16" ry="10" fill="#ffffff" opacity="0.55"/>
      <circle cx="150" cy="58" r="7" fill="#9b6bff"/>
      <line x1="150" y1="58" x2="150" y2="34" stroke="#9b6bff" stroke-width="4" stroke-linecap="round"/>
      <circle cx="150" cy="32" r="5" fill="#5be1ff"/>
    </svg>

    <span class="eyebrow reveal d1">EdTech · Mission Control</span>
    <h1 class="reveal d2">Your <span class="journey">Journey</span> Into Tech Starts Here.</h1>
    <p class="sub reveal d3">Learn technical skills, build real projects, and explore your potential.</p>
    @guest
      <div class="ctas reveal d4">
        <a class="btn btn-primary" href="/register">Start Exploring <span class="arrow">→</span></a>
        <a class="btn btn-secondary" href="#features">Explore Courses</a>
      </div>
      <p class="login-hint reveal d4">Already have an account? <a href="/login">Log in →</a></p>
    @else
      <div class="ctas reveal d4">
        <a class="btn btn-primary" href="/dashboard">Go to Dashboard <span class="arrow">→</span></a>
        <form method="POST" action="/logout" class="logout-form">
          @csrf
          <button class="btn btn-secondary" type="submit">Log out</button>
        </form>
      </div>
      <p class="login-hint reveal d4">Signed in as <strong>{{ auth()->user()->name }}</strong>.</p>
    @endguest
  </main>

  <!-- Features -->
  <section class="features" id="features">
    <div class="card">
      <div class="ico">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="9"/><path d="M12 3v18M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>
        </svg>
      </div>
      <div class="tag">Explore</div>
      <h3>Learn by exploring</h3>
      <p>Students are astronauts. Chart courses in programming, networking, and cybersecurity through guided lessons and hands-on coding practice.</p>
    </div>

    <div class="card">
      <div class="ico">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 14c-1 1-1 4-1 4s3 0 4-1"/><path d="M14 4l6 6-8 8-6-6 8-8z"/><path d="M11 7l6 6"/>
        </svg>
      </div>
      <div class="tag">Missions</div>
      <h3>Build real projects</h3>
      <p>Turn learning into missions. Ship real-world projects, earn exams and badges, and assemble a portfolio that proves what you can do.</p>
    </div>

    <div class="card">
      <div class="ico">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="8" r="3"/><circle cx="17" cy="10" r="2.4"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5"/><path d="M15 20c0-2 1-3.5 3-3.5s3 1.5 3 3.5"/>
        </svg>
      </div>
      <div class="tag">Crews</div>
      <h3>Learn together</h3>
      <p>Every team is a crew. Collaborate on team-based learning, pair up on missions, and grow faster with peers by your side.</p>
    </div>
  </section>

  <!-- Progress -->
  <div class="progress-wrap" id="progress">
    <div class="progress">
      <div class="ptext">
        <div class="tag">Planet Development</div>
        <h3>Watch your world grow</h3>
        <p>Progress isn't a number — it's a planet taking shape. Every course completed builds your world.</p>
      </div>
      <div class="bar">
        <div class="track"><div class="fill" id="fill"></div></div>
        <div class="meta"><span>Terraforming</span><span>68% developed</span></div>
      </div>
    </div>
  </div>

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
      <a href="#features">Courses</a>
      <a href="#features">Missions</a>
      <a href="#features">Crews</a>
    </div>
    <span>© 2026 TechLab — Learn. Build. Explore.</span>
  </footer>

  <script>
    // Twinkling starfield
    (function () {
      const field = document.getElementById('stars');
      const count = window.innerWidth < 700 ? 90 : 170;
      const frag = document.createDocumentFragment();
      // deterministic-ish spread without Math.random dependency concerns
      let seed = 1337;
      const rnd = () => { seed = (seed * 1103515245 + 12345) & 0x7fffffff; return seed / 0x7fffffff; };
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

    // Entrance
    window.addEventListener('load', () => {
      document.body.classList.add('loaded');
      const fill = document.getElementById('fill');
      if (fill && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        setTimeout(() => { fill.style.width = '68%'; }, 400);
      } else if (fill) {
        fill.style.width = '68%';
      }
    });

    // Gentle parallax on pointer move
    (function () {
      const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      if (reduce) return;
      const stars = document.getElementById('stars');
      const planets = document.getElementById('planets');
      let tx = 0, ty = 0, cx = 0, cy = 0;
      window.addEventListener('pointermove', (e) => {
        tx = (e.clientX / window.innerWidth - 0.5);
        ty = (e.clientY / window.innerHeight - 0.5);
      });
      function loop() {
        cx += (tx - cx) * 0.06;
        cy += (ty - cy) * 0.06;
        stars.style.transform = `translate(${cx * -12}px, ${cy * -12}px)`;
        planets.style.transform = `translate(${cx * 26}px, ${cy * 26}px)`;
        requestAnimationFrame(loop);
      }
      loop();
    })();
  </script>
</body>
</html>
