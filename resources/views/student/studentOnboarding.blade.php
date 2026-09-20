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

  {{-- Tailwind Play CDN + tokens (needed for the sidebar component) --}}
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

  {{-- Alpine (sidebar needs it) --}}
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    [x-cloak] { display: none !important; }

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

    /* ---------- Space background ---------- */
    .space {
      position: fixed; inset: 0; z-index: -1; overflow: hidden;
      background:
        radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%),
        radial-gradient(100% 80% at 85% 110%, #1a0f4d 0%, rgba(26,15,77,0) 60%),
        linear-gradient(160deg, #0a0826 0%, #120a33 45%, #1e1259 100%);
    }
    .glow { position: absolute; border-radius: 50%; filter: blur(70px); opacity: 0.55; animation: pulse 9s ease-in-out infinite; }
    .glow.g1 { width: 460px; height: 460px; top: -120px; left: -80px;   background: radial-gradient(circle, rgba(155,107,255,0.7), transparent 70%); }
    .glow.g2 { width: 520px; height: 520px; bottom: -160px; right: -120px; background: radial-gradient(circle, rgba(91,225,255,0.45), transparent 70%); animation-delay: -4s; }
    .glow.g3 { width: 380px; height: 380px; top: 40%; left: 55%; background: radial-gradient(circle, rgba(115,182,255,0.35), transparent 70%); animation-delay: -2s; }
    @keyframes pulse { 0%,100% { transform: scale(1); opacity: .5; } 50% { transform: scale(1.12); opacity: .7; } }

    .star { position: absolute; width: 2px; height: 2px; border-radius: 50%; background: #fff; opacity: .8; animation: twinkle var(--dur,4s) ease-in-out infinite; animation-delay: var(--delay,0s); }
    @keyframes twinkle { 0%,100% { opacity: .15; transform: scale(.7); } 50% { opacity: 1; transform: scale(1.2); } }

    @keyframes fadeUp  { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: none; } }
    @keyframes floatY  { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }

    /* ---------- Onboarding content ---------- */
    .onboard-wrap {
      position: relative; z-index: 2;
      max-width: 1080px;
      margin: 0 auto;
      padding: clamp(40px, 7vw, 90px) clamp(20px, 5vw, 64px) 80px;
    }

    .planets { animation: fadeUp .6s ease both; }
    .planets-head { text-align: center; margin-bottom: 30px; }
    .planets-head h2 { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: clamp(1.6rem, 4vw, 2.4rem); letter-spacing: -0.02em; }
    .planets-head p  { color: var(--muted); margin: 10px auto 0; max-width: 620px; }

    .planet-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }

    .planet-card {
      position: relative; display: flex; flex-direction: column; gap: 10px; padding: 22px; border-radius: 20px;
      background: var(--glass); border: 1px solid var(--glass-border); backdrop-filter: blur(12px);
      transform-style: preserve-3d; transition: transform .25s ease, border-color .25s, box-shadow .25s;
      animation: fadeUp .6s ease both;
    }
    .planet-grid .planet-card:nth-child(1) { animation-delay: .22s; }
    .planet-grid .planet-card:nth-child(2) { animation-delay: .34s; }
    .planet-grid .planet-card:nth-child(3) { animation-delay: .46s; }
    .planet-card:hover { transform: translateY(-6px); }
    .planet-card h3   { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.2rem; }
    .planet-card p    { color: var(--muted); font-size: .9rem; }
    .planet-card .pc-go { margin-top: auto; padding-top: 8px; font-family: 'Space Grotesk', sans-serif; font-weight: 600; color: var(--blue); }
    .planet-card.prog:hover  { border-color: var(--blue);   box-shadow: 0 22px 52px rgba(115,182,255,.34); }
    .planet-card.net:hover   { border-color: var(--cyan);   box-shadow: 0 22px 52px rgba(91,225,255,.34);  }
    .planet-card.cyber:hover { border-color: var(--violet); box-shadow: 0 22px 52px rgba(155,107,255,.34); }

    .planet-stage { width: 116px; height: 116px; margin: 2px auto 12px; transform: translateZ(28px); }
    .planet-svg   { width: 100%; height: 100%; animation: floatY 6s ease-in-out infinite; }
    .planet-card.prog  .planet-svg { filter: drop-shadow(0 10px 26px rgba(115,182,255,.45)); }
    .planet-card.net   .planet-svg { filter: drop-shadow(0 10px 26px rgba(91,225,255,.45));  }
    .planet-card.cyber .planet-svg { filter: drop-shadow(0 10px 26px rgba(155,107,255,.45)); }

    .crew-join { text-align: center; margin-top: 44px; animation: fadeUp .6s ease both; animation-delay: .6s; }
    .join-btn {
      display: inline-flex; align-items: center; gap: 10px;
      font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 1.05rem;
      padding: 16px 34px; border-radius: 999px; cursor: pointer; border: none; color: #07142e;
      background: linear-gradient(100deg, var(--cyan), var(--blue) 55%, var(--violet));
      box-shadow: 0 10px 40px rgba(115,182,255,.45); transition: transform .25s, box-shadow .25s;
    }
    .join-btn:hover { transform: translateY(-2px); box-shadow: 0 16px 50px rgba(115,182,255,.6); }
    .crew-join p { color: var(--muted); margin-top: 14px; font-size: .92rem; }

    @media (max-width: 760px) { .planet-grid { grid-template-columns: 1fr; } }
    @media (prefers-reduced-motion: reduce) {
      .planets, .planet-card, .planet-svg, .crew-join, .join-btn { animation: none !important; }
    }
  </style>
</head>

<body
  class="bg-void font-sans text-ink antialiased"
  x-data="techlabShell()"
  x-init="init()"
>

  {{-- Space background --}}
  <div class="space" aria-hidden="true">
    <div class="glow g1"></div>
    <div class="glow g2"></div>
    <div class="glow g3"></div>
    <div class="layer-stars" id="stars"></div>
  </div>

  {{-- ┌──────────┬──────────────────────────────────────────┐
       │ TECHLAB  │                                          │
       │ REMOTE   │         PLANETS / MAIN CONTENT          │
       │ Sidebar  │                                          │
       └──────────┴──────────────────────────────────────────┘ --}}
  <div class="flex h-screen w-screen overflow-hidden">

    {{-- ── REMOTE / SIDEBAR ──────────────────────────── --}}
    @include('components.shell.side-bar')

    {{-- ── MAIN CONTENT: scrollable planet picker ───── --}}
    <main class="min-w-0 flex-1 overflow-y-auto">
      <div class="onboard-wrap">

        <section class="planets">
          <div class="planets-head">
            <h2>Choose your mission planet</h2>
            <p>Pick the world you want to conquer. You can explore them all, one track at a time — your progress is saved per planet.</p>
          </div>

          <div class="planet-grid">

            {{-- Programming City --}}
            <a class="planet-card prog" data-tilt href="{{ route('student.planet', 'programming') }}">
              <div class="planet-stage">
                <svg class="planet-svg" viewBox="0 0 200 200" aria-hidden="true">
                  <defs>
                    <radialGradient id="pg" cx="38%" cy="30%" r="80%">
                      <stop offset="0%"   stop-color="#cfe6ff"/>
                      <stop offset="55%"  stop-color="#73b6ff"/>
                      <stop offset="100%" stop-color="#235bbf"/>
                    </radialGradient>
                  </defs>
                  <circle cx="100" cy="100" r="60" fill="url(#pg)"/>
                  <g opacity=".5">
                    <path d="M52 96 q18 -12 38 -5 t40 3" stroke="#eaf4ff" stroke-width="4" fill="none" stroke-linecap="round"/>
                    <rect x="74"  y="112" width="22" height="11" rx="2" fill="#0b2c63"/>
                    <rect x="104" y="120" width="15" height="9"  rx="2" fill="#0b2c63"/>
                    <circle cx="120" cy="86" r="6" fill="#0b2c63"/>
                  </g>
                  <g>
                    <animateTransform attributeName="transform" type="rotate" from="0 100 100" to="360 100 100" dur="30s" repeatCount="indefinite"/>
                    <circle cx="100" cy="30" r="6" fill="#cfe6ff"/>
                  </g>
                </svg>
              </div>
              <h3>Programming City</h3>
              <p>Build with code — logic, apps, and the foundations of software.</p>
              <span class="pc-go">Enter planet →</span>
            </a>

            {{-- Networking Nebula --}}
            <a class="planet-card net" data-tilt href="{{ route('student.planet', 'networking') }}">
              <div class="planet-stage">
                <svg class="planet-svg" viewBox="0 0 200 200" aria-hidden="true">
                  <defs>
                    <radialGradient id="nw" cx="38%" cy="30%" r="80%">
                      <stop offset="0%"   stop-color="#d6fbff"/>
                      <stop offset="55%"  stop-color="#5be1ff"/>
                      <stop offset="100%" stop-color="#1f8fb0"/>
                    </radialGradient>
                  </defs>
                  <circle cx="100" cy="100" r="56" fill="url(#nw)"/>
                  <g opacity=".55" stroke="#eafcff" stroke-width="3">
                    <path d="M60 100 h22 M118 100 h22 M100 60 v22 M100 118 v22" fill="none"/>
                    <circle cx="60"  cy="100" r="4" fill="#eafcff" stroke="none"/>
                    <circle cx="140" cy="100" r="4" fill="#eafcff" stroke="none"/>
                    <circle cx="100" cy="60"  r="4" fill="#eafcff" stroke="none"/>
                    <circle cx="100" cy="140" r="4" fill="#eafcff" stroke="none"/>
                  </g>
                  <g>
                    <animateTransform attributeName="transform" type="rotate" from="0 100 100" to="360 100 100" dur="18s" repeatCount="indefinite"/>
                    <ellipse cx="100" cy="100" rx="78" ry="30" fill="none" stroke="#9af2ff" stroke-width="2" opacity=".7" transform="rotate(-18 100 100)"/>
                    <circle cx="178" cy="100" r="5" fill="#9af2ff"/>
                  </g>
                </svg>
              </div>
              <h3>Networking Nebula</h3>
              <p>How the internet works — routing, protocols, and connections.</p>
              <span class="pc-go">Enter planet →</span>
            </a>

            {{-- Cybersecurity Citadel --}}
            <a class="planet-card cyber" data-tilt href="{{ route('student.planet', 'cybersecurity') }}">
              <div class="planet-stage">
                <svg class="planet-svg" viewBox="0 0 200 200" aria-hidden="true">
                  <defs>
                    <radialGradient id="cy" cx="38%" cy="30%" r="80%">
                      <stop offset="0%"   stop-color="#e7ddff"/>
                      <stop offset="55%"  stop-color="#9b6bff"/>
                      <stop offset="100%" stop-color="#5a32b8"/>
                    </radialGradient>
                  </defs>
                  <circle cx="100" cy="100" r="58" fill="url(#cy)"/>
                  <g opacity=".5" fill="none" stroke="#f3eeff" stroke-width="4" stroke-linecap="round">
                    <path d="M82 92 v-8 a18 18 0 0 1 36 0 v8"/>
                    <rect x="78" y="92" width="44" height="34" rx="6"/>
                    <circle cx="100" cy="108" r="5" fill="#f3eeff" stroke="none"/>
                    <path d="M100 113 v8"/>
                  </g>
                  <g>
                    <animateTransform attributeName="transform" type="rotate" from="0 100 100" to="360 100 100" dur="24s" repeatCount="indefinite"/>
                    <path d="M100 26 l10 6 v10 l-10 6 l-10 -6 v-10 z" fill="#cbb6ff"/>
                  </g>
                </svg>
              </div>
              <h3>Cybersecurity Citadel</h3>
              <p>Defend systems — threats, encryption, and secure design.</p>
              <span class="pc-go">Enter planet →</span>
            </a>

          </div>
        </section>

       
      </div>
    </main>

  </div>{{-- end flex shell --}}

  {{-- Stars --}}
  <script>
    (function () {
      const field = document.getElementById('stars');
      const count = window.innerWidth < 700 ? 90 : 170;
      const frag  = document.createDocumentFragment();
      let seed = 1337;
      const rnd = () => { seed = (seed * 1103515245 + 12345) & 0x3fffffff; return seed / 0x3fffffff; };
      for (let i = 0; i < count; i++) {
        const s    = document.createElement('span');
        s.className = 'star';
        const size = rnd() > 0.85 ? 3 : 2;
        s.style.width  = s.style.height = size + 'px';
        s.style.left   = (rnd() * 100) + 'vw';
        s.style.top    = (rnd() * 100) + 'vh';
        s.style.setProperty('--dur',   (2.5 + rnd() * 4).toFixed(2) + 's');
        s.style.setProperty('--delay', (rnd() * 5).toFixed(2) + 's');
        frag.appendChild(s);
      }
      field.appendChild(frag);
    })();
  </script>

  {{-- Planet card tilt --}}
  <script>
    (function () {
      var cards = document.querySelectorAll('.planet-card[data-tilt]');
      cards.forEach(function (card) {
        card.addEventListener('mousemove', function (e) {
          var r  = card.getBoundingClientRect();
          var px = (e.clientX - r.left) / r.width  - 0.5;
          var py = (e.clientY - r.top)  / r.height - 0.5;
          card.style.transform = 'translateY(-6px) rotateX(' + (py * -9).toFixed(2) + 'deg) rotateY(' + (px * 11).toFixed(2) + 'deg)';
        });
        card.addEventListener('mouseleave', function () {
          card.style.transform = '';
        });
      });
    })();
  </script>

  {{-- techlabShell: sidebar collapsed state, persisted via localStorage --}}
  <script>
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
  </script>

</body>
</html>