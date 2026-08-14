<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Captain's Bridge · TechLab</title>
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
    .p1 { width: 200px; height: 200px; top: 14%; right: 7%; background: radial-gradient(circle at 35% 30%, #b9a3ff, #6c4bd6 55%, #2e1a73); box-shadow: 0 0 80px rgba(124,75,214,.55); }
    .p2 { width: 120px; height: 120px; bottom: 12%; left: 8%; background: radial-gradient(circle at 35% 30%, #aef0ff, #4fc8ee 55%, #1d6f99); box-shadow: 0 0 70px rgba(91,225,255,.5); animation-direction: reverse; }
    @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-24px); } }

    .nav { position: sticky; top: 0; z-index: 50; display: flex; align-items: center; justify-content: space-between; padding: 22px clamp(20px,5vw,64px); backdrop-filter: blur(10px); background: linear-gradient(180deg, rgba(6,6,26,.6), rgba(6,6,26,0)); }
    .brand { display: flex; align-items: center; gap: 12px; }
    .brand svg { width: 34px; height: 34px; filter: drop-shadow(0 0 10px rgba(115,182,255,.5)); }
    .brand .name { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.25rem; letter-spacing: -0.01em; }
    .brand .name span { color: var(--blue); }
    .brand-name { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1rem; letter-spacing: -0.01em; }
    .brand-name span { color: var(--blue); }
    .nav-cta { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 0.9rem; padding: 10px 20px; border-radius: 999px; background: rgba(115,182,255,0.12); border: 1px solid var(--glass-border); color: var(--text); cursor: pointer; transition: all .25s; }
    .nav-cta:hover { background: rgba(115,182,255,0.22); transform: translateY(-1px); }

    .wrap { position: relative; z-index: 2; max-width: 860px; margin: 0 auto; padding: 44px clamp(20px,5vw,64px) 80px; text-align: center; }
    .eyebrow { font-family: 'Space Mono', monospace; font-size: .78rem; letter-spacing: .32em; text-transform: uppercase; color: var(--cyan); padding: 8px 16px; border: 1px solid var(--glass-border); border-radius: 999px; background: var(--glass); display: inline-block; }
    h1 { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: clamp(2rem, 5vw, 3.2rem); letter-spacing: -.03em; margin: 20px 0 8px; }
    h1 .accent { background: linear-gradient(100deg, var(--cyan), var(--blue) 45%, var(--violet)); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .sub { color: var(--muted); max-width: 54ch; margin: 0 auto; font-size: 1.02rem; }

    .ship-stage { position: relative; height: 300px; margin: 26px 0 10px; display: flex; align-items: center; justify-content: center; }
    .ship { width: clamp(120px, 20vw, 190px); animation: shipfloat 5s ease-in-out infinite; filter: drop-shadow(0 18px 40px rgba(115,182,255,.45)); }
    @keyframes shipfloat { 0%,100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-20px) rotate(2deg); } }
    .thruster { transform-origin: 50% 0; animation: flame .22s alternate infinite; }
    @keyframes flame { from { transform: scaleY(.55); opacity: .6; } to { transform: scaleY(1.15); opacity: 1; } }
    .dock { position: absolute; bottom: 10px; width: 120px; height: 120px; border-radius: 50%; background: radial-gradient(circle at 35% 30%, #d7c4ff, #6c4bd6 60%, #2e1a73); box-shadow: 0 0 60px rgba(124,75,214,.6); }
    .orbit { position: absolute; bottom: -10px; width: 220px; height: 220px; border: 1.5px dashed rgba(150,170,255,.28); border-radius: 50%; animation: spin 18s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .code-card { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 22px; padding: 26px; backdrop-filter: blur(12px); margin-top: 26px; }
    .code-card .k { font-family: 'Space Mono', monospace; font-size: .72rem; letter-spacing: .16em; text-transform: uppercase; color: var(--muted); }
    .code-row { display: flex; align-items: center; justify-content: center; gap: 14px; margin-top: 12px; flex-wrap: wrap; }
    .code-val { font-family: 'Space Mono', monospace; font-size: 2.2rem; font-weight: 700; letter-spacing: .3em; color: var(--blue); }
    .copy { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: .85rem; padding: 10px 16px; border-radius: 999px; border: 1px solid var(--glass-border); background: rgba(115,182,255,.12); color: var(--text); cursor: pointer; transition: background .2s; }
    .copy:hover { background: rgba(115,182,255,.22); }
    .code-card .note { color: var(--muted); font-size: .92rem; margin-top: 14px; }

    .crew-name-input { width: 100%; max-width: 440px; padding: 14px 16px; border-radius: 14px; border: 1px solid var(--glass-border); background: rgba(10,12,40,.5); color: var(--text); font-family: 'Space Grotesk', sans-serif; font-size: 1.05rem; text-align: center; margin: 18px auto 0; display: block; }
    .crew-name-input::placeholder { color: #6f79ad; }
    .crew-name-input:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(115,182,255,.15); }
    .roster { margin-top: 22px; display: grid; gap: 10px; text-align: left; }
    .member { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 14px; background: rgba(10,12,40,.45); border: 1px solid var(--glass-border); }
    .member .avatar { width: 38px; height: 38px; border-radius: 50%; display: grid; place-items: center; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: .82rem; color: #07142e; background: linear-gradient(135deg, var(--cyan), var(--blue)); flex: 0 0 38px; }
    .member .mname { font-weight: 600; flex: 1 1 auto; }
    .member .tag { font-family: 'Space Mono', monospace; font-size: .68rem; letter-spacing: .12em; text-transform: uppercase; padding: 5px 10px; border-radius: 999px; }
    .member .tag.teacher { color: var(--violet); background: rgba(155,107,255,.15); }
    .member .tag.student { color: var(--cyan); background: rgba(91,225,255,.13); }

    .btn { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 1.05rem; padding: 16px 34px; border-radius: 999px; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 10px; transition: transform .25s, box-shadow .25s; }
    .btn-primary { color: #07142e; background: linear-gradient(100deg, var(--cyan), var(--blue) 55%, var(--violet)); box-shadow: 0 10px 40px rgba(115,182,255,.45); }
    .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 16px 50px rgba(115,182,255,.6); }

    footer { position: relative; z-index: 2; text-align: center; color: var(--muted); font-size: .9rem; padding: 20px; }

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
    <a class="nav-cta" href="/teacher/dashboard">Mission Path</a>
    <form method="POST" action="/logout" style="margin:0">
      @csrf
      <button class="nav-cta" type="submit">Log out</button>
    </form>
  </header>

  <main class="wrap">
    @if ($crew)
      {{-- CREW LAUNCHED --}}
      <span class="eyebrow">Captain · Crew Launched</span>
      <h1>Your <span class="accent">spaceship</span> is ready</h1>
      <p class="sub">Share the crew code with your students. When they board, your squad's shared planet begins to grow.</p>

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

      <div class="code-card">
        <div class="k">Crew code — give this to your students</div>
        <div class="code-row">
          <span class="code-val" id="codeVal">{{ $crew->code }}</span>
          <button class="copy" id="copyBtn" type="button">Copy</button>
        </div>
        <p class="note">Crew name: <strong>{{ $crew->name }}</strong> · {{ $crew->roster->count() }} aboard</p>

        <div class="roster">
          @foreach ($crew->roster as $m)
            <div class="member">
              <span class="avatar">{{ strtoupper(substr($m->name, 0, 2)) }}</span>
              <span class="mname">{{ $m->name }}</span>
              <span class="tag {{ $m->pivot->role === 'teacher' ? 'teacher' : 'student' }}">{{ $m->pivot->role === 'teacher' ? 'Captain' : 'Astronaut' }}</span>
            </div>
          @endforeach
        </div>
      </div>

    @else
      {{-- NO CREW YET --}}
      <span class="eyebrow">Captain · Ready to launch</span>
      <h1>Create your <span class="accent">crew</span></h1>
      <p class="sub">Every squad needs a ship. Launch your crew to get a join code you can share with your students.</p>

      <div class="ship-stage">
        <div class="orbit"></div>
        <div class="dock"></div>
        <svg class="ship" viewBox="0 0 120 220" aria-hidden="true" style="opacity:.55">
          <defs>
            <linearGradient id="hull0" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#eaf2ff"/><stop offset="100%" stop-color="#9fb4e6"/></linearGradient>
            <linearGradient id="flame0" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#fff3b0"/><stop offset="60%" stop-color="#ff9b3d"/><stop offset="100%" stop-color="#ff4d6d"/></linearGradient>
          </defs>
          <g class="thruster"><path d="M48 168 q12 34 12 46 q0 -12 12 -46 z" fill="url(#flame0)"/></g>
          <path d="M60 8 q32 42 32 96 q0 44 -32 56 q-32 -12 -32 -56 q0 -54 32 -96z" fill="url(#hull0)" stroke="#7c8cc8" stroke-width="2"/>
          <circle cx="60" cy="74" r="15" fill="#5be1ff" stroke="#1b3a5c" stroke-width="2"/>
          <path d="M28 120 q-22 8 -22 44 q22 -12 32 -22z" fill="#9b6bff"/>
          <path d="M92 120 q22 8 22 44 q-22 -12 -32 -22z" fill="#9b6bff"/>
        </svg>
      </div>

      <form method="POST" action="/teacher/crew">
        @csrf
        <input type="text" name="name" class="crew-name-input" placeholder="Name your classroom (e.g. Space Crew)" required autofocus />
        <button class="btn btn-primary" type="submit" style="margin-top:16px">🚀 Create your crew</button>
      </form>
    @endif
  </main>

  <footer>© 2026 <span class="brand-name">Tech<span>Lab</span></span> — Learn. Build. Explore.</footer>

  <script>
    (function () {
      const field = document.getElementById('stars');
      const count = window.innerWidth < 700 ? 90 : 160;
      let seed = 5151; const rnd = () => { seed = (seed * 1103515245 + 12345) & 0x7fffffff; return seed / 0x7fffffff; };
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

    const copyBtn = document.getElementById('copyBtn');
    if (copyBtn) {
      copyBtn.addEventListener('click', () => {
        const code = document.getElementById('codeVal').textContent.trim();
        navigator.clipboard?.writeText(code).then(() => {
          copyBtn.textContent = 'Copied!';
          setTimeout(() => (copyBtn.textContent = 'Copy'), 1600);
        });
      });
    }
  </script>
</body>
</html>
