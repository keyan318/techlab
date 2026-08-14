<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Crew · TechLab</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />

  <style>
    :root {
      --void: #06061a; --blue: #73b6ff; --violet: #9b6bff; --cyan: #5be1ff;
      --text: #eaeeff; --muted: #98a2d4;
      --glass: rgba(123, 142, 220, 0.07); --glass-border: rgba(150, 170, 255, 0.18);
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; color: var(--text); background: var(--void); overflow-x: hidden; -webkit-font-smoothing: antialiased; line-height: 1.6; }

    .space { position: fixed; inset: 0; z-index: -1; overflow: hidden;
      background: radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%),
        radial-gradient(100% 80% at 85% 110%, #1a0f4d 0%, rgba(26,15,77,0) 60%),
        linear-gradient(160deg, #0a0826 0%, #120a33 45%, #1e1259 100%); }
    .glow { position: absolute; border-radius: 50%; filter: blur(70px); opacity: .55; animation: pulse 9s ease-in-out infinite; }
    .glow.g1 { width: 460px; height: 460px; top: -120px; left: -80px; background: radial-gradient(circle, rgba(155,107,255,.7), transparent 70%); }
    .glow.g2 { width: 520px; height: 520px; bottom: -160px; right: -120px; background: radial-gradient(circle, rgba(91,225,255,.45), transparent 70%); animation-delay: -4s; }
    @keyframes pulse { 0%,100% { transform: scale(1); opacity: .5; } 50% { transform: scale(1.12); opacity: .7; } }
    .star { position: absolute; width: 2px; height: 2px; border-radius: 50%; background: #fff; opacity: .8; animation: twinkle var(--dur,4s) ease-in-out infinite; animation-delay: var(--delay,0s); }
    @keyframes twinkle { 0%,100% { opacity: .15; transform: scale(.7); } 50% { opacity: 1; transform: scale(1.2); } }
    .planet { position: absolute; border-radius: 50%; animation: float 13s ease-in-out infinite; }
    .planet::after { content: ""; position: absolute; inset: 0; border-radius: 50%; box-shadow: inset -18px -18px 40px rgba(0,0,0,.45); }
    .p1 { width: 180px; height: 180px; top: 18%; left: 6%; background: radial-gradient(circle at 35% 30%, #b9a3ff, #6c4bd6 55%, #2e1a73); box-shadow: 0 0 80px rgba(124,75,214,.55); }
    .p2 { width: 110px; height: 110px; bottom: 12%; right: 8%; background: radial-gradient(circle at 35% 30%, #aef0ff, #4fc8ee 55%, #1d6f99); box-shadow: 0 0 70px rgba(91,225,255,.5); animation-direction: reverse; }
    @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-24px); } }

    .nav { position: sticky; top: 0; z-index: 50; display: flex; align-items: center; justify-content: space-between; padding: 22px clamp(20px,5vw,64px); backdrop-filter: blur(10px); background: linear-gradient(180deg, rgba(6,6,26,.6), rgba(6,6,26,0)); }
    .brand { display: flex; align-items: center; gap: 12px; }
    .brand svg { width: 34px; height: 34px; filter: drop-shadow(0 0 10px rgba(115,182,255,.5)); }
    .brand .name { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.25rem; }
    .brand .name span { color: var(--blue); }
    .nav-cta { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 0.9rem; padding: 10px 20px; border-radius: 999px; background: rgba(115,182,255,0.12); border: 1px solid var(--glass-border); color: var(--text); cursor: pointer; transition: all .25s; }
    .nav-cta:hover { background: rgba(115,182,255,0.22); transform: translateY(-1px); }

    .wrap { position: relative; z-index: 2; max-width: 860px; margin: 0 auto; padding: 40px clamp(20px,5vw,64px) 80px; text-align: center; }
    .eyebrow { font-family: 'Space Mono', monospace; font-size: .78rem; letter-spacing: .32em; text-transform: uppercase; color: var(--cyan); padding: 8px 16px; border: 1px solid var(--glass-border); border-radius: 999px; background: var(--glass); display: inline-block; }
    h1 { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: clamp(2rem, 5vw, 3.2rem); letter-spacing: -.03em; margin: 20px 0 8px; }
    h1 .accent { background: linear-gradient(100deg, var(--cyan), var(--blue) 45%, var(--violet)); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .sub { color: var(--muted); max-width: 54ch; margin: 0 auto; font-size: 1.02rem; }
    .welcome-head { margin-bottom: 4px; }

    /* spaceship */
    .ship-stage { position: relative; height: 300px; margin: 30px 0 10px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .ship { width: clamp(120px, 20vw, 190px); animation: shipfloat 5s ease-in-out infinite; filter: drop-shadow(0 18px 40px rgba(115,182,255,.45)); }
    @keyframes shipfloat { 0%,100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-20px) rotate(2deg); } }
    /* launching animation for the docked crew ship */
    .ship.launch { animation: launch 4.6s ease-in infinite; }
    @keyframes launch {
      0%   { transform: translateY(150px) scale(.92); opacity: 0; }
      8%   { opacity: 1; }
      14%  { transform: translateY(150px) scale(1); }
      78%  { transform: translateY(-360px) scale(1); opacity: 1; }
      100% { transform: translateY(-360px) scale(.9); opacity: 0; }
    }
    .thruster { transform-origin: 50% 0; animation: flame .22s alternate infinite; }
    @keyframes flame { from { transform: scaleY(.55); opacity: .6; } to { transform: scaleY(1.15); opacity: 1; } }
    .dock { position: absolute; bottom: 10px; width: 120px; height: 120px; border-radius: 50%; background: radial-gradient(circle at 35% 30%, #d7c4ff, #6c4bd6 60%, #2e1a73); box-shadow: 0 0 60px rgba(124,75,214,.6); }
    .orbit { position: absolute; bottom: -10px; width: 220px; height: 220px; border: 1.5px dashed rgba(150,170,255,.28); border-radius: 50%; animation: spin 18s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 28px; }
    .card { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 18px; padding: 20px; backdrop-filter: blur(12px); }
    .card .k { font-family: 'Space Mono', monospace; font-size: .68rem; letter-spacing: .16em; text-transform: uppercase; color: var(--cyan); }
    .card .v { font-family: 'Space Grotesk', sans-serif; font-size: 1.5rem; margin-top: 6px; }
    .code-card .v { letter-spacing: .25em; color: var(--blue); }

    /* join form */
    .join { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 22px; padding: 30px; backdrop-filter: blur(12px); margin-top: 26px; text-align: left; }
    .join label { display: block; font-family: 'Space Mono', monospace; font-size: .72rem; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); margin-bottom: 10px; }
    .join .row { display: flex; gap: 12px; flex-wrap: wrap; }
    .join input { flex: 1 1 200px; padding: 14px 16px; border-radius: 12px; background: rgba(10,12,40,.55); border: 1px solid var(--glass-border); color: var(--text); font-family: 'Space Mono', monospace; font-size: 1.1rem; letter-spacing: .2em; text-transform: uppercase; }
    .join input:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(115,182,255,.18); }
    .btn { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 1rem; padding: 14px 26px; border-radius: 999px; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 10px; transition: transform .25s, box-shadow .25s; }
    .btn-primary { color: #07142e; background: linear-gradient(100deg, var(--cyan), var(--blue) 55%, var(--violet)); box-shadow: 0 10px 40px rgba(115,182,255,.45); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 16px 50px rgba(115,182,255,.6); }
    .alert { background: rgba(255,99,132,.12); border: 1px solid rgba(255,99,132,.4); color: #ffc2cf; padding: 11px 14px; border-radius: 12px; font-size: .9rem; margin-top: 16px; }

    .launchpad { transition: transform .6s ease, opacity .6s ease; }
    .launching { transform: translateY(-340px) scale(.6); opacity: 0; }

    footer { position: relative; z-index: 2; text-align: center; color: var(--muted); font-size: .9rem; padding: 20px; }

    @media (max-width: 640px) { .cards { grid-template-columns: 1fr; } }
    @media (prefers-reduced-motion: reduce) { *,*::after,*::before { animation: none !important; } }
  </style>
</head>
<body>

  <div class="space" aria-hidden="true">
    <div class="glow g1"></div><div class="glow g2"></div>
    <div id="stars"></div>
    <div class="planet p1"></div><div class="planet p2"></div>
  </div>

  <header class="nav">
    <a class="brand" href="/">
      <svg viewBox="0 0 108.89 108.89" aria-hidden="true">
        <polygon fill="#73b6ff" points="37.55 108.89 52.56 108.89 52.56 90.12 90.12 90.12 90.12 75.09 52.56 75.09 52.56 56.33 37.55 56.33 37.55 108.89"/>
        <polygon fill="#73b6ff" points="108.89 71.34 108.89 56.33 90.12 56.33 90.12 18.78 75.09 18.78 75.09 56.33 56.33 56.33 56.33 71.34 108.89 71.34"/>
        <polygon fill="#73b6ff" points="71.34 0 56.33 0 56.33 18.78 18.78 18.78 18.78 33.79 56.33 33.79 56.33 52.56 71.34 52.56 71.34 0"/>
        <polygon fill="#73b6ff" points="0 37.55 0 52.56 18.78 52.56 18.78 90.12 33.79 90.12 33.79 52.56 52.56 52.56 52.56 37.55 0 37.55"/>
      </svg>
      <span class="name">Tech<span>Lab</span></span>
    </a>
    <a class="nav-cta" href="/dashboard">Mission Path</a>
    <form method="POST" action="/logout" style="margin:0">
      @csrf
      <button class="nav-cta" type="submit">Log out</button>
    </form>
  </header>

  <main class="wrap">
    <div class="welcome-head">
      <span class="eyebrow">Mission Control</span>
      <h1>Welcome, {{ Auth::user()->name }} 🚀</h1>
      <p class="sub">@if ($crew) You're aboard <strong>{{ $crew->name }}</strong>. Ready for your next mission? @else Board your crew ship to begin your journey. @endif</p>
    </div>

    @if ($crew)
      {{-- JOINED STATE --}}
      <span class="eyebrow">Crew · Docked</span>
      <h1>You're aboard <span class="accent">{{ $crew->name }}</span></h1>
      <p class="sub">Welcome to the crew, astronaut. Your squad's shared planet grows as everyone learns. Mission Control is standing by.</p>

      <div class="ship-stage">
        <div class="orbit"></div>
        <div class="dock"></div>
        <svg class="ship launch" viewBox="0 0 120 220" aria-hidden="true">
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

      <div class="cards">
        <div class="card"><div class="k">Captain</div><div class="v">{{ $crew->teacher->name ?? 'Captain' }}</div></div>
        <div class="card code-card"><div class="k">Crew Code</div><div class="v">{{ $crew->code }}</div></div>
        <div class="card"><div class="k">Crew Size</div><div class="v">{{ $crew->members()->count() }}</div></div>
      </div>

      <div style="margin-top:24px; text-align:left;">
        <div class="k" style="font-family:'Space Mono',monospace;font-size:.72rem;letter-spacing:.16em;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">Crew Roster</div>
        @foreach ($crew->roster as $m)
          <div style="display:flex;align-items:center;gap:12px;padding:10px 14px;border-radius:14px;background:rgba(10,12,40,.45);border:1px solid var(--glass-border);margin-top:10px;">
            <span style="width:38px;height:38px;border-radius:50%;display:grid;place-items:center;font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:.82rem;color:#07142e;background:linear-gradient(135deg,var(--cyan),var(--blue));">{{ strtoupper(substr($m->name,0,2)) }}</span>
            <span style="font-weight:600;flex:1 1 auto;">{{ $m->name }}</span>
            <span style="font-family:'Space Mono',monospace;font-size:.68rem;letter-spacing:.12em;text-transform:uppercase;padding:5px 10px;border-radius:999px;{{ $m->pivot->role === 'teacher' ? 'color:var(--violet);background:rgba(155,107,255,.15);' : 'color:var(--cyan);background:rgba(91,225,255,.13);' }}">{{ $m->pivot->role === 'teacher' ? 'Captain' : 'Astronaut' }}</span>
          </div>
        @endforeach
      </div>
      <p class="sub" style="margin-top:22px">Share the crew code with your captain if you ever need to re-board from another device.</p>
      <a class="btn btn-primary" href="/student/crew/home" style="margin-top:24px">Go to Dashboard →</a>

    @else
      {{-- NOT JOINED STATE --}}
      <span class="eyebrow">Crew · Boarding</span>
      <h1>Join your <span class="accent">crew</span></h1>
      <p class="sub">Got a crew code from your captain? Enter it below to board the ship and join your squad's mission.</p>

      <div class="ship-stage">
        <div class="orbit"></div>
        <div class="dock"></div>
        <svg class="ship launchpad" id="rocket" viewBox="0 0 120 220" aria-hidden="true">
          <defs>
            <linearGradient id="hull2" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#eaf2ff"/><stop offset="100%" stop-color="#9fb4e6"/></linearGradient>
            <linearGradient id="flame2" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#fff3b0"/><stop offset="60%" stop-color="#ff9b3d"/><stop offset="100%" stop-color="#ff4d6d"/></linearGradient>
          </defs>
          <g class="thruster"><path d="M48 168 q12 34 12 46 q0 -12 12 -46 z" fill="url(#flame2)"/></g>
          <path d="M60 8 q32 42 32 96 q0 44 -32 56 q-32 -12 -32 -56 q0 -54 32 -96z" fill="url(#hull2)" stroke="#7c8cc8" stroke-width="2"/>
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

      <form class="join" method="POST" action="/student/crew/join" id="joinForm">
        @csrf
        <label for="code">Crew code</label>
        <div class="row">
          <input id="code" name="code" type="text" placeholder="ABC123" value="{{ old('code') }}" maxlength="6" autocomplete="off" required />
          <button class="btn btn-primary" type="submit">Board the ship →</button>
        </div>
      </form>
    @endif
  </main>

  <footer>© 2026 TechLab — Learn. Build. Explore.</footer>

  <script>
    (function () {
      const field = document.getElementById('stars');
      const count = window.innerWidth < 700 ? 90 : 160;
      let seed = 7331; const rnd = () => { seed = (seed * 1103515245 + 12345) & 0x7fffffff; return seed / 0x7fffffff; };
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

    // Launch animation on submit (joined state only)
    const form = document.getElementById('joinForm');
    if (form) {
      form.addEventListener('submit', (e) => {
        const rocket = document.getElementById('rocket');
        if (rocket) rocket.classList.add('launching');
      });
    }
  </script>
</body>
</html>
