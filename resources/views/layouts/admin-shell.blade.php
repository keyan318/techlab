<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Admin') · TechLab</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&family=Pixelify+Sans:wght@500;600;700&display=swap" rel="stylesheet" />

  {{-- Theme tokens + Tailwind (the shared sidebar component needs both) --}}
  <meta name="theme-color" content="#06061a">
  <link rel="stylesheet" href="{{ asset('css/theme.css') }}?v={{ filemtime(public_path('css/theme.css')) }}">
  <script src="{{ asset('js/theme.js') }}?v={{ filemtime(public_path('js/theme.js')) }}"></script>
  <script defer src="{{ asset('js/sfx.js') }}?v={{ filemtime(public_path('js/sfx.js')) }}"></script>
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
      --field: rgba(10,12,40,.5);
      --px-line: rgba(150,170,255,.40); --px-bg: rgba(123,142,220,.09); --px-solid: #17143f;
      --px-shadow: rgba(0,0,0,.4);
    }
    html[data-theme="light"] {
      --field: #f4f4f5;
      --px-line: #c3cfe0; --px-bg: #fff; --px-solid: #fff; --px-shadow: rgba(20,24,60,.16);
    }
    html[data-theme="light"] .member .avatar { color: #fff; }

    .px-font, h1, h2, h3, .brand-name, .roster-card h2, .mbtn, .mname { font-family: 'Pixelify Sans', 'Space Grotesk', sans-serif; }
    .brand-name { font-weight: 700; letter-spacing: -0.01em; }
    .brand-name span { color: var(--blue); }
    .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 14px; margin-top: 20px; }
    .stat { background: var(--px-bg); border-radius: 4px; padding: 16px 18px; text-align: left;
      box-shadow: 0 -3px 0 0 var(--px-line), 0 3px 0 0 var(--px-line), -3px 0 0 0 var(--px-line), 3px 0 0 0 var(--px-line); }
    .stat .n { font-family: 'Pixelify Sans', sans-serif; font-weight: 700; font-size: 1.9rem; line-height: 1.1; }
    .stat .l { font-family: 'Space Mono', monospace; font-size: .68rem; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); margin-top: 4px; }
    .page-head { text-align: left; }
    .page-head h1 { margin: 0 0 6px; font-size: clamp(1.7rem, 3.6vw, 2.3rem); }
    .roster-card { margin-top: 26px; text-align: left; }
    .roster-card h2 { font-size: 1.5rem; letter-spacing: -.01em; }
    /* Students by program × year level (admin dashboard) */
    .cohort-wrap { margin-top: 14px; overflow-x: auto; border: 1px solid var(--px-line); border-radius: 4px; background: var(--px-bg); }
    .cohort { width: 100%; border-collapse: collapse; font-size: .92rem; min-width: 560px; }
    .cohort th, .cohort td { padding: 11px 14px; border-bottom: 1px solid var(--glass-border); text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
    .cohort thead th { font-family: 'Space Mono', monospace; font-size: .66rem; font-weight: 400; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); }
    .cohort th:first-child { text-align: left; }
    .cohort tbody th { font-weight: 400; }
    .cohort tbody th b { font-family: 'Space Grotesk', sans-serif; font-weight: 700; margin-right: 6px; }
    .cohort tbody th span { color: var(--muted); font-size: .84rem; }
    .cohort td.zero { color: var(--muted); opacity: .55; }
    .cohort .sum { font-weight: 700; }
    .cohort tfoot th, .cohort tfoot td { border-bottom: 0; font-weight: 700; }
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

    .wrap { position: relative; z-index: 2; max-width: 900px; margin: 0 auto; padding: 44px clamp(20px,5vw,64px) 80px; text-align: left; }
    .eyebrow { font-family: 'Space Mono', monospace; font-size: .78rem; letter-spacing: .32em; text-transform: uppercase; color: var(--cyan); padding: 8px 16px; border: 1px solid var(--glass-border); border-radius: 999px; background: var(--glass); display: inline-block; }
    h1 { font-weight: 700; font-size: clamp(2rem, 5vw, 3.2rem); letter-spacing: -.01em; margin: 20px 0 8px; }
    h1 .accent { color: var(--blue); }
    .sub { color: var(--muted); max-width: 60ch; font-size: 1.02rem; }

    .code-card { background: var(--px-bg); border-radius: 4px; padding: 26px; -webkit-backdrop-filter: blur(12px); backdrop-filter: blur(12px); margin-top: 26px;
      box-shadow: 0 -3px 0 0 var(--px-line), 0 3px 0 0 var(--px-line), -3px 0 0 0 var(--px-line), 3px 0 0 0 var(--px-line); }
    .code-card .k { font-family: 'Space Mono', monospace; font-size: .72rem; letter-spacing: .16em; text-transform: uppercase; color: var(--muted); }

    .roster { margin-top: 22px; display: grid; gap: 10px; text-align: left; }
    .member { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 4px; background: var(--px-bg); border: 0;
      box-shadow: 0 -2px 0 0 var(--px-line), 0 2px 0 0 var(--px-line), -2px 0 0 0 var(--px-line), 2px 0 0 0 var(--px-line); }
    .member .avatar { width: 38px; height: 38px; border-radius: 2px; display: grid; place-items: center; font-family: 'Pixelify Sans', sans-serif; font-weight: 700; font-size: .82rem; color: #08051f; background: #73b6ff; flex: 0 0 38px;
      box-shadow: inset 0 -4px 0 rgba(0,0,0,.18); }
    .member .mname { font-weight: 600; flex: 1 1 auto; }
    .member .tag { font-family: 'Space Mono', monospace; font-size: .68rem; letter-spacing: .12em; text-transform: uppercase; padding: 5px 10px; border-radius: 2px; }
    .member .tag.faculty { color: var(--violet); background: rgba(155,107,255,.15); }
    .member .tag.student { color: var(--cyan); background: rgba(91,225,255,.13); }

    .field-select { padding: 9px 12px; border-radius: 2px; border: 1px solid var(--px-line); background: var(--field); color: var(--text); font: inherit; font-size: .88rem; }
    .field-select:focus { outline: none; border-color: var(--blue); }
    .mbtn {
      font-weight: 600; font-size: .88rem; padding: 0 18px; min-height: 40px; display: inline-flex; align-items: center; justify-content: center;
      border-radius: 2px; cursor: pointer; color: var(--text); background: var(--px-bg); border: 0;
      box-shadow: 0 -2px 0 0 var(--px-line), 0 2px 0 0 var(--px-line), -2px 0 0 0 var(--px-line), 2px 0 0 0 var(--px-line),
                  inset 0 -3px 0 0 rgba(0,0,0,.14), inset 0 2px 0 0 rgba(255,255,255,.14);
      transition: transform .1s ease, filter .15s ease;
    }
    .mbtn:hover { filter: brightness(1.1); }
    .mbtn:active { transform: translateY(2px); }
    .mbtn.solid {
      color: #fff; background: #14a0ff;
      box-shadow: 0 -3px 0 0 #0a5fb5, 0 3px 0 0 #0a5fb5, -3px 0 0 0 #0a5fb5, 3px 0 0 0 #0a5fb5,
                  inset 0 -4px 0 0 rgba(0,0,0,.2), inset 0 3px 0 0 rgba(255,255,255,.22);
    }

    footer { position: relative; z-index: 1; text-align: center; color: var(--muted); font-size: .9rem; padding: 20px; }

    @media (prefers-reduced-motion: reduce) { *,*::after,*::before { animation: none !important; } }
  </style>
  @stack('head')
  <link rel="stylesheet" href="{{ asset('css/cursor.css') }}?v={{ filemtime(public_path('css/cursor.css')) }}">
</head>
<body class="bg-void font-sans text-ink antialiased" x-data="techlabShell()" x-init="init()">

  <div class="space" aria-hidden="true">
    <div class="glow g1"></div><div class="glow g2"></div>
    <div id="stars"></div>
  </div>

  <div class="flex h-screen w-screen overflow-hidden">
    @include('components.shell.side-bar')

    <main class="min-w-0 flex-1 overflow-y-auto">
      <div class="wrap">
        @yield('content')
      </div>
      <footer>© 2026 <span class="brand-name">Tech<span>Lab</span></span> — Learn. Build. Explore.</footer>
    </main>
  </div>

  <script>
    function techlabShell() {
      return {
        collapsed: true,
        init() {
          const saved = localStorage.getItem('techlab_sidebar_collapsed');
          this.collapsed = saved === null ? true : saved === '1';
          this.$watch('collapsed', v => localStorage.setItem('techlab_sidebar_collapsed', v ? '1' : '0'));
        },
        toggleTheme() { window.techlabTheme.toggle(); },
      };
    }

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
  </script>
  @stack('scripts')
</body>
</html>
