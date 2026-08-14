<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign in · TechLab</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <meta name="description" content="Sign in to TechLab and continue your journey into tech." />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --void: #06061a; --cosmic-1: #120a33; --cosmic-2: #1e1259; --nebula: #2d1b69;
      --blue: #73b6ff; --violet: #9b6bff; --cyan: #5be1ff; --text: #eaeeff; --muted: #98a2d4;
      --glass: rgba(123,142,220,0.07); --glass-border: rgba(150,170,255,0.18);
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', system-ui, sans-serif; color: var(--text); background: var(--void); min-height: 100vh; overflow-x: hidden; -webkit-font-smoothing: antialiased; }
    a { color: inherit; text-decoration: none; }
    .space { position: fixed; inset: 0; z-index: -1; overflow: hidden;
      background: radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%), radial-gradient(100% 80% at 85% 110%, #1a0f4d 0%, rgba(26,15,77,0) 60%), linear-gradient(160deg, #0a0826 0%, #120a33 45%, #1e1259 100%); }
    .glow { position: absolute; border-radius: 50%; filter: blur(70px); opacity: .55; animation: pulse 9s ease-in-out infinite; }
    .glow.g1 { width: 460px; height: 460px; top: -120px; left: -80px; background: radial-gradient(circle, rgba(155,107,255,.7), transparent 70%); }
    .glow.g2 { width: 520px; height: 520px; bottom: -160px; right: -120px; background: radial-gradient(circle, rgba(91,225,255,.45), transparent 70%); animation-delay: -4s; }
    .glow.g3 { width: 380px; height: 380px; top: 40%; left: 55%; background: radial-gradient(circle, rgba(115,182,255,.35), transparent 70%); animation-delay: -2s; }
    @keyframes pulse { 0%,100% { transform: scale(1); opacity: .5; } 50% { transform: scale(1.12); opacity: .7; } }
    .star { position: absolute; width: 2px; height: 2px; border-radius: 50%; background: #fff; opacity: .8; animation: twinkle var(--dur,4s) ease-in-out infinite; animation-delay: var(--delay,0s); }
    @keyframes twinkle { 0%,100% { opacity: .15; transform: scale(.7); } 50% { opacity: 1; transform: scale(1.2); } }
    .planet { position: absolute; border-radius: 50%; }
    .planet::after { content:""; position: absolute; inset: 0; border-radius: 50%; box-shadow: inset -18px -18px 40px rgba(0,0,0,.45); }
    .p1 { width: 200px; height: 200px; top: 14%; left: 7%; background: radial-gradient(circle at 35% 30%, #b9a3ff, #6c4bd6 55%, #2e1a73); box-shadow: 0 0 80px rgba(124,75,214,.5); animation: float 14s ease-in-out infinite; }
    .p2 { width: 120px; height: 120px; bottom: 12%; right: 9%; background: radial-gradient(circle at 35% 30%, #aef0ff, #4fc8ee 55%, #1d6f99); box-shadow: 0 0 70px rgba(91,225,255,.5); animation: float 11s ease-in-out infinite reverse; }
    @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-26px); } }
    .wrap { position: relative; z-index: 2; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
    .card { width: 100%; max-width: 440px; background: var(--glass); border: 1px solid var(--glass-border); border-radius: 24px; padding: 38px 34px; backdrop-filter: blur(14px); box-shadow: 0 30px 80px rgba(6,6,26,.5); }
    .brand { display: flex; align-items: center; gap: 12px; justify-content: center; margin-bottom: 18px; }
    .brand svg { width: 34px; height: 34px; filter: drop-shadow(0 0 10px rgba(115,182,255,.5)); }
    .brand .name { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.3rem; }
    .brand .name span { color: var(--blue); }
    .eyebrow { display: block; text-align: center; font-family: 'Space Mono', monospace; font-size: .76rem; letter-spacing: .28em; text-transform: uppercase; color: var(--cyan); margin-bottom: 12px; }
    .card h1 { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.85rem; text-align: center; letter-spacing: -.02em; }
    .card h1 .accent { background: linear-gradient(100deg, var(--cyan), var(--blue) 45%, var(--violet)); -webkit-background-clip: text; background-clip: text; color: transparent; }
    .role-select { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 22px 0; }
    .role { display: flex; flex-direction: column; align-items: flex-start; gap: 3px; padding: 14px 16px; border-radius: 14px; cursor: pointer; text-align: left; background: rgba(10,12,40,.5); border: 1px solid var(--glass-border); color: var(--muted); transition: border-color .2s, background .2s, box-shadow .2s; font-family: 'Space Grotesk', sans-serif; }
    .role .role-title { font-weight: 600; font-size: 1rem; color: var(--text); }
    .role .role-sub { font-family: 'Space Mono', monospace; font-size: .68rem; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); }
    .role .role-ico { width: 22px; height: 22px; margin-bottom: 4px; }
    .role.active { border-color: var(--blue); background: rgba(115,182,255,.12); box-shadow: 0 0 0 3px rgba(115,182,255,.15); }
    .role.active .role-sub { color: var(--cyan); }
    .field { margin-bottom: 16px; }
    .field label { display: block; font-size: .85rem; color: var(--muted); margin-bottom: 7px; }
    .field input { width: 100%; padding: 13px 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(10,12,40,.5); color: var(--text); font-family: 'Inter', sans-serif; font-size: .95rem; }
    .field input::placeholder { color: #6f79ad; }
    .field input:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(115,182,255,.15); }
    .row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; font-size: .88rem; }
    .row label { color: var(--muted); display: flex; align-items: center; gap: 7px; }
    .row a { color: var(--blue); font-weight: 600; }
    .btn { display: block; width: 100%; font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: .98rem; padding: 14px; border-radius: 999px; border: none; cursor: pointer; text-align: center; background: linear-gradient(100deg, var(--cyan), var(--blue) 55%, var(--violet)); color: #07142e; box-shadow: 0 12px 30px rgba(115,182,255,.4); transition: transform .2s; }
    .btn:hover { transform: translateY(-2px); }
    .foot { text-align: center; margin-top: 18px; color: var(--muted); font-size: .92rem; }
    .foot a { color: var(--blue); font-weight: 600; }
    .foot a:hover { text-decoration: underline; }
    .err { background: rgba(255,77,109,.12); border: 1px solid rgba(255,77,109,.4); color: #ffb3c4; padding: 10px 14px; border-radius: 12px; font-size: .88rem; margin-bottom: 16px; text-align: center; }
    @media (prefers-reduced-motion: reduce) { *, *::after, *::before { animation: none !important; } }
  </style>
</head>
<body>
  <div class="space" aria-hidden="true">
    <div class="glow g1"></div><div class="glow g2"></div><div class="glow g3"></div>
    <div id="stars"></div>
    <div class="planet p1"></div><div class="planet p2"></div>
  </div>

  <div class="wrap">
    <div class="card">
      <a class="brand" href="/" aria-label="TechLab home">
        <svg viewBox="0 0 108.89 108.89" aria-hidden="true">
          <polygon fill="#73b6ff" points="37.55 108.89 52.56 108.89 52.56 90.12 90.12 90.12 90.12 75.09 52.56 75.09 52.56 56.33 37.55 56.33 37.55 108.89"/>
          <polygon fill="#73b6ff" points="108.89 71.34 108.89 56.33 90.12 56.33 90.12 18.78 75.09 18.78 75.09 56.33 56.33 56.33 56.33 71.34 108.89 71.34"/>
          <polygon fill="#73b6ff" points="71.34 0 56.33 0 56.33 18.78 18.78 18.78 18.78 33.79 56.33 33.79 56.33 52.56 71.34 52.56 71.34 0"/>
          <polygon fill="#73b6ff" points="0 37.55 0 52.56 18.78 52.56 18.78 90.12 33.79 90.12 33.79 52.56 52.56 52.56 52.56 37.55 0 37.55"/>
        </svg>
        <span class="name">Tech<span>Lab</span></span>
      </a>

      <span class="eyebrow">EdTech · Mission Control</span>
      <h1>Welcome back, <span class="accent">astronaut</span>.</h1>

      <form method="POST" action="/login">
        @csrf
        <div class="role-select" role="radiogroup" aria-label="I am a">
          <button type="button" class="role active" data-role="student" aria-pressed="true">
            <svg class="role-ico" viewBox="0 0 24 24" fill="none" stroke="#73b6ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="11" r="6"/><path d="M9 16c.7 1.4 1.9 2.3 3 2.3s2.3-.9 3-2.3"/><path d="M12 3v2"/><path d="M5 11H3M21 11h-2"/></svg>
            <span class="role-title">Student</span>
            <span class="role-sub">Astronaut</span>
          </button>
          <button type="button" class="role" data-role="teacher" aria-pressed="false">
            <svg class="role-ico" viewBox="0 0 24 24" fill="none" stroke="#9b6bff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20h18"/><path d="M5 20V9l7-5 7 5v11"/><path d="M9 20v-5h6v5"/></svg>
            <span class="role-title">Teacher</span>
            <span class="role-sub">Captain</span>
          </button>
        </div>
        <input type="hidden" name="role" id="role" value="student" />

        @error('email')
          <div class="err">{{ $message }}</div>
        @enderror

        <div class="field">
          <label for="email">Email</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@galaxy.edu" required autofocus />
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input id="password" type="password" name="password" placeholder="••••••••" required />
        </div>
        <div class="row">
          <label><input type="checkbox" name="remember" /> Remember me</label>
          <a href="#">Forgot password?</a>
        </div>
        <button class="btn" type="submit">Sign In <span>→</span></button>
      </form>

      <p class="foot">New here? <a href="/register">Create an account</a></p>
    </div>
  </div>

  <script>
    (function () {
      const roles = document.querySelectorAll('.role');
      const roleInput = document.getElementById('role');
      const accent = document.querySelector('.card h1 .accent');
      roles.forEach((btn) => {
        btn.addEventListener('click', () => {
          roles.forEach((r) => { r.classList.remove('active'); r.setAttribute('aria-pressed', 'false'); });
          btn.classList.add('active'); btn.setAttribute('aria-pressed', 'true');
          const role = btn.dataset.role;
          roleInput.value = role;
          if (accent) accent.textContent = role === 'teacher' ? 'captain' : 'astronaut';
        });
      });
    })();
    (function () {
      const field = document.getElementById('stars');
      const count = window.innerWidth < 700 ? 90 : 150;
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
  </script>
</body>
</html>
