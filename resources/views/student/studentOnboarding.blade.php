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
      max-width: 1180px;
      margin: 0 auto;
      padding: clamp(40px, 7vw, 90px) clamp(20px, 5vw, 64px) 80px;
    }

    .planets { animation: fadeUp .6s ease both; }
    /* Planet cards, Codedex-style: pixel-art banner on top, then an eyebrow, title, blurb and a pill.
       The cards are dark in both themes (the art is dark), so their text is always light. */
    .planet-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }

    .planet-card {
      --accent: #73b6ff;
      position: relative; display: flex; flex-direction: column; overflow: hidden; isolation: isolate;
      color: #eef1ff; background: #0d1028;
      border: 2px solid #252b52; border-radius: 6px;
      box-shadow: 0 5px 0 #1a1f40, 0 18px 36px -20px rgba(0,0,0,.8);
      transition: transform .25s cubic-bezier(.32,.72,0,1), border-color .2s, box-shadow .25s cubic-bezier(.32,.72,0,1);
      animation: fadeUp .6s ease both;
    }
    .planet-card.net   { --accent: #5be1ff; }
    .planet-card.cyber { --accent: #b48bff; }
    .planet-grid .planet-card:nth-child(1) { animation-delay: .1s; }
    .planet-grid .planet-card:nth-child(2) { animation-delay: .2s; }
    .planet-grid .planet-card:nth-child(3) { animation-delay: .3s; }
    a.planet-card:hover { transform: translateY(-4px); border-color: var(--accent); box-shadow: 0 9px 0 #1a1f40, 0 26px 44px -18px color-mix(in srgb, var(--accent) 45%, transparent); }
    a.planet-card:active { transform: translateY(1px); box-shadow: 0 2px 0 #1a1f40; transition-duration: .08s; }
    a.planet-card:focus-visible { outline: 3px solid var(--accent); outline-offset: 3px; }

    /* The art is the star of the card, so the banner is taller than the text below it. The 5:2 art is
       cropped at the sides (cover); each planet's subject sits in the middle, so it stays whole. */
    .pc-scene { position: relative; aspect-ratio: 4 / 3; overflow: hidden; border-bottom: 2px solid #252b52; background: #0b0820; }
    .pc-art { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center bottom; image-rendering: pixelated; transition: transform .5s cubic-bezier(.32,.72,0,1); }
    a.planet-card:hover .pc-art { transform: scale(1.04); }

    .pc-body { display: flex; flex: 1; flex-direction: column; padding: 14px 18px 16px; }
    .pc-eyebrow { font-family: 'Space Mono', monospace; font-size: 11.5px; letter-spacing: .18em; text-transform: uppercase; color: #9aa3d0; }
    .planet-card h3 { margin-top: 4px; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.35rem; line-height: 1.2; letter-spacing: -.01em; color: #fff; }
    .planet-card p { margin-top: 6px; font-size: .95rem; line-height: 1.5; color: #c3c9ea;
      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .pc-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: auto; padding-top: 14px; }
    .pc-go { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: .9rem; color: var(--accent); white-space: nowrap; }

    /* Pill: signal-bars icon + uppercase label, like Codedex's "BEGINNER" chip. Bars light up with progress. */
    .pc-pill { display: inline-flex; align-items: center; gap: 9px; padding: 7px 14px; border-radius: 99px;
      font-family: 'Space Mono', monospace; font-size: 11.5px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
      color: #dfe4ff; background: #1b2042; }
    .pc-bars { display: inline-flex; align-items: flex-end; gap: 2px; height: 11px; }
    .pc-bars i { width: 3px; background: #48507e; }
    .pc-bars i:nth-child(1) { height: 4px; } .pc-bars i:nth-child(2) { height: 7px; } .pc-bars i:nth-child(3) { height: 11px; }
    .pc-bars i.on { background: var(--accent); }
    .pc-bar { height: 4px; margin-top: 14px; overflow: hidden; background: #1b2042; }
    .pc-bar > i { display: block; height: 100%; background: var(--accent); }

    /* Minimal twinkling stars over each banner's sky: steady, one quick soft twinkle, long rest. */
    .pc-tw { position: absolute; width: var(--s); aspect-ratio: 1; min-width: 2px; margin: calc(var(--s) / -2) 0 0 calc(var(--s) / -2);
      background: #fff; pointer-events: none; opacity: .85; animation: pc-tw var(--t, 6s) ease-in-out var(--d, 0s) infinite; }
    @keyframes pc-tw { 0%,55%,100% { opacity: .85; scale: 1; } 70% { opacity: .1; scale: .5; } 82% { opacity: 1; scale: 1.15; } }

    /* Locked planet: same card, art drained of colour behind a lock plate. XP/Gem numbers are static
       placeholders (the real unlock cost isn't calculated yet). */
    .planet-card.is-locked { cursor: default; }
    .planet-card.is-locked .pc-art { filter: grayscale(.85) brightness(.45); }
    .planet-card.is-locked .pc-tw { display: none; }
    .pc-lock { position: absolute; inset: 0; display: grid; place-items: center; }
    .pc-lock span { display: grid; place-items: center; width: 46px; height: 46px; border-radius: 10px; color: #dfe4ff;
      background: rgba(13,16,40,.82); border: 2px solid #3a4170; box-shadow: 0 4px 0 #1a1f40; }
    .planet-card .lock-req { margin-top: 6px; font-size: 12.5px; line-height: 1.5; color: #9aa3d0; }
    .planet-card .lock-req b { color: #fff; font-weight: 600; }

    @media (max-width: 1080px) { .planet-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 640px)  { .planet-grid { grid-template-columns: 1fr; } .pc-scene { aspect-ratio: 6 / 5; } }
    @media (prefers-reduced-motion: reduce) {
      .planets, .planet-card, .crew-join, .join-btn { animation: none !important; }
      .pc-tw { animation: none; }
      .planet-card, .pc-art { transition: none; }
    }

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
  </style>
  <link rel="stylesheet" href="{{ asset('css/cursor.css') }}?v={{ filemtime(public_path('css/cursor.css')) }}">
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
          @php
            // One entry per planet. Twinkle stars sit in each banner's open sky: [left %, top %, size %].
            $planetCards = [
              [
                'slug' => 'programming', 'class' => 'prog', 'name' => 'Programming City',
                'blurb' => config('course-catalog.programming.description'),
                'art' => 'images/programming-city-pixel.png', 'w' => 1250, 'h' => 500,
                'lock' => 'Pick a course, Self-paced or Join Class, to land here.',
                'stars' => [[4, 30, .45], [22, 6, .5], [34, 18, .4], [64, 4, .5], [77, 12, .4], [94, 6, .45]],
              ],
              [
                'slug' => 'networking', 'class' => 'net', 'name' => 'Networking Nebula',
                'blurb' => config('course-catalog.networking.description'),
                'art' => 'images/networking-nebula.png', 'w' => 1250, 'h' => 500,
                'lock' => 'Finish more of Programming City to unlock — needs <b>500 XP</b> and <b>3 Gems</b>.',
                'stars' => [[30, 8, .45], [56, 30, .4], [70, 6, .5], [83, 44, .4], [97, 8, .45], [3, 62, .4]],
              ],
              [
                'slug' => 'cybersecurity', 'class' => 'cyber', 'name' => 'Cybersecurity Citadel',
                'blurb' => config('course-catalog.cybersecurity.description'),
                'art' => 'images/cybersecurity-citadel.png', 'w' => 1250, 'h' => 500,
                'lock' => 'Finish Networking Nebula to unlock — needs <b>900 XP</b> and <b>4 Gems</b>.',
                'stars' => [[16, 6, .45], [42, 18, .4], [55, 4, .5], [66, 22, .4], [80, 40, .45], [96, 52, .4]],
              ],
            ];
          @endphp

          <div class="planet-grid" data-tour="planet-list">
            @foreach ($planetCards as $card)
              @php
                $progress = $planetProgress[$card['slug']] ?? null;   // null = not enrolled yet
                $pct = $progress['percent'] ?? 0;
                $barsOn = $pct >= 67 ? 3 : ($pct >= 34 ? 2 : 1);
              @endphp

              @if ($progress)
              @php $tourMark = empty($tourMarked); $tourMarked = true; // Astro's tour points at the student's own planet @endphp
              <a class="planet-card {{ $card['class'] }}" @if ($tourMark) data-tour="planet-card" @endif href="{{ route('student.planet', $card['slug']) }}"
                 aria-label="{{ $card['name'] }}, {{ $pct }}% complete">
                <div class="pc-scene" aria-hidden="true">
                  <img class="pc-art" src="{{ asset($card['art']) }}" width="{{ $card['w'] }}" height="{{ $card['h'] }}" alt="" decoding="async">
                  @foreach ($card['stars'] as $i => [$l, $t, $sz])
                    <i class="pc-tw" style="left: {{ $l }}%; top: {{ $t }}%; --s: {{ $sz }}%; --d: {{ number_format(-fmod($i * 1.9, 7), 2) }}s; --t: {{ number_format(5.5 + ($i % 5) * 0.9, 1) }}s"></i>
                  @endforeach
                </div>
                <div class="pc-body">
                  <span class="pc-eyebrow">Planet</span>
                  <h3>{{ $card['name'] }}</h3>
                  <p>{{ $card['blurb'] }}</p>
                  <div class="pc-foot">
                    <span class="pc-pill">
                      <span class="pc-bars" aria-hidden="true">@for ($b = 1; $b <= 3; $b++)<i class="{{ $b <= $barsOn ? 'on' : '' }}"></i>@endfor</span>
                      {{ $pct > 0 ? $pct.'% complete' : 'Beginner' }}
                    </span>
                    <span class="pc-go">{{ $pct > 0 ? 'Continue' : 'Start' }} <span aria-hidden="true">→</span></span>
                  </div>
                  @if ($pct > 0)
                    <div class="pc-bar" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"><i style="width: {{ $pct }}%"></i></div>
                  @endif
                </div>
              </a>
              @else
              <div class="planet-card {{ $card['class'] }} is-locked" aria-label="{{ $card['name'] }}, locked">
                <div class="pc-scene" aria-hidden="true">
                  <img class="pc-art" src="{{ asset($card['art']) }}" width="{{ $card['w'] }}" height="{{ $card['h'] }}" alt="" decoding="async">
                  <div class="pc-lock"><span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                  </span></div>
                </div>
                <div class="pc-body">
                  <span class="pc-eyebrow">Planet</span>
                  <h3>{{ $card['name'] }}</h3>
                  <p>{{ $card['blurb'] }}</p>
                  <p class="lock-req">{!! $card['lock'] !!}</p>
                  <div class="pc-foot">
                    <span class="pc-pill">
                      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                      Locked
                    </span>
                  </div>
                </div>
              </div>
              @endif
            @endforeach
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