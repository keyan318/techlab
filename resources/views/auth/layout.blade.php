<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title') · TechLab</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <meta name="description" content="@yield('description')">
  @hasSection('noindex')<meta name="robots" content="noindex, nofollow">@endif
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Same tokens as the landing page and the course sidebar */
    :root {
      /* Deep-space look: the light tokens flip, the brand blue and the logo stay as they were. */
      --ink: #eaeeff; --body: #b6bfe8; --muted: #98a2d4;
      --blue: #2f7de1; --blue-dark: #1f5fb4; --blue-tint: rgba(47,125,225,.22); --blue-wash: rgba(47,125,225,.10);
      --surface: rgba(12,10,42,.9); --page: #06061a; --track: rgba(255,255,255,.10); --line: rgba(150,170,255,.22);
      --display: 'Space Grotesk', system-ui, sans-serif; --text: 'Inter', system-ui, sans-serif;
      --ease: cubic-bezier(.2,.9,.25,1);
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: var(--text); background: var(--page); color: var(--body); line-height: 1.6; min-height: 100vh; -webkit-font-smoothing: antialiased; display: flex; flex-direction: column; }
    a { color: inherit; text-decoration: none; }
    :focus-visible { outline: 3px solid var(--blue); outline-offset: 2px; border-radius: 8px; }

    .bar { background: transparent; } /* no bar: the logo sits straight on the space */
    main { position: relative; z-index: 1; }
    .bar-in { max-width: 1180px; margin: 0 auto; padding: 0 24px; height: 96px; display: flex; align-items: center; }
    .brand { display: flex; align-items: center; gap: 12px; font-family: var(--display); font-weight: 700; font-size: 22px; letter-spacing: -0.02em; color: var(--ink); }
    .brand img { width: 34px; height: 34px; border-radius: 8px; }

    main { flex: 1; display: grid; place-items: center; padding: 24px 20px 40px; }
    /* Two panels: brand + mascot + heading on the left, the form on the right */
    .card { width: 100%; max-width: 820px; display: grid; grid-template-columns: minmax(0, 5fr) minmax(0, 6fr); background: rgba(12,10,42,.72); -webkit-backdrop-filter: blur(24px) saturate(160%); backdrop-filter: blur(24px) saturate(160%); border: 1px solid var(--line); border-top-color: rgba(255,255,255,.28); /* light catching the top edge */ border-radius: 22px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,.2), 0 28px 56px -24px rgba(0,0,0,.6); opacity: 0; transform: translateY(14px) scale(.985); filter: blur(8px); animation: land .8s var(--ease) 60ms forwards; }
    .side { position: relative; display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 24px 24px 30px; text-align: center; background: radial-gradient(120% 80% at 50% 0%, rgba(47,125,225,.22), transparent 70%), rgba(255,255,255,.03); border-right: 1px solid var(--line); }
    .side .tl-brand { align-self: flex-start; }
    .side-body { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; }
    .panel { padding: 32px 32px 26px; display: flex; flex-direction: column; justify-content: center; }
    @keyframes land { to { opacity: 1; transform: none; filter: blur(0); } }

    /* Stage: the active role's mascot floats and reacts */
    .stage { position: relative; width: 96px; height: 92px; margin: 0 auto 10px; }
    .mascot { position: absolute; inset: 0; display: grid; place-items: center; opacity: 0; transform: translateY(14px) scale(.7); transition: opacity .3s ease, transform .55s cubic-bezier(.3,1.35,.5,1); pointer-events: none; }
    .mascot.on { opacity: 1; transform: none; }
    .mascot svg { width: 88px; height: auto; animation: bob 3.6s ease-in-out infinite; filter: drop-shadow(0 8px 6px rgba(13,13,13,.14)); }
    .mascot.on svg.astro { animation: bob 3.6s ease-in-out infinite, tilt 5s ease-in-out infinite; }
    @keyframes bob { 0%, 100% { translate: 0 0; } 50% { translate: 0 -6px; } }
    @keyframes tilt { 0%, 100% { rotate: -3deg; } 50% { rotate: 3deg; } }
    .eye { transform-box: fill-box; transform-origin: center; animation: blink 4.2s infinite; }
    @keyframes blink { 0%, 92%, 100% { transform: scaleY(1); } 95% { transform: scaleY(.1); } }
    .twinkle { position: absolute; width: 8px; height: 8px; background: #f5c04a; clip-path: polygon(50% 0, 62% 38%, 100% 50%, 62% 62%, 50% 100%, 38% 62%, 0 50%, 38% 38%); animation: twinkle 3s ease-in-out infinite; opacity: 0; }
    .twinkle.a { top: 6px; right: 4px; } .twinkle.b { bottom: 14px; left: 2px; animation-delay: 1.4s; width: 6px; height: 6px; }
    @keyframes twinkle { 0%, 55%, 100% { transform: scale(0); opacity: 0; } 72% { transform: scale(1); opacity: 1; } 88% { transform: scale(0); opacity: 0; } }

    h1 { font-family: var(--display); font-weight: 700; color: var(--ink); font-size: clamp(1.75rem, 3.6vw, 2.2rem); line-height: 1.1; letter-spacing: -0.03em; }
    h1 .line { display: block; }
    .decode { display: inline-block; margin-top: .1em; padding: 0 .22em .06em; min-width: 6ch; background: var(--blue); color: #fff; border-radius: .18em; box-shadow: 0 .06em 0 var(--blue-dark); white-space: nowrap; font-variant-ligatures: none; }
    .sub { margin-top: 10px; font-size: 14px; line-height: 1.5; max-width: 26ch; margin-inline: auto; }

    /* The form is a two-column grid: most fields span both columns, .half fields sit side by side */
    form { text-align: left; display: grid; grid-template-columns: 1fr 1fr; column-gap: 14px; }
    form > * { grid-column: 1 / -1; }
    form > .half { grid-column: auto; }
    .field { margin-bottom: 12px; }
    .field label { display: block; margin-bottom: 5px; font-family: var(--display); font-weight: 600; font-size: 13px; letter-spacing: .01em; color: var(--ink); }
    .field input { width: 100%; height: 46px; padding: 0 14px; border: 1.5px solid var(--line); border-radius: 12px; font: inherit; font-size: 16px; color: var(--ink); background: var(--surface); outline: none; transition: border-color .2s ease, box-shadow .2s ease; }
    .field input::placeholder { color: #a3a3a3; }
    .field input:focus { border-color: var(--blue); box-shadow: 0 0 0 4px var(--blue-tint); }
    .planet-pick { border: 0; padding: 0; margin: 0 0 16px; min-width: 0; }
    .planet-pick legend { padding: 0; margin-bottom: 4px; font-family: var(--display); font-weight: 600; font-size: 14px; color: var(--ink); }
    .planet-pick .hint { margin: 0 0 8px; font-size: 13px; color: var(--muted); }
    .planet-pick .tiles { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
    .planet-pick label { display: flex; flex-direction: column; align-items: flex-start; gap: 8px; padding: 12px; margin: 0; min-width: 0; border: 2px solid var(--line); border-radius: 12px; cursor: pointer; background: var(--surface); transition: border-color .2s ease, background .2s ease; }
    .planet-pick label:has(input:checked) { border-color: var(--blue); background: var(--blue-tint); }
    .planet-pick label:has(input:focus-visible) { box-shadow: 0 0 0 4px var(--blue-tint); }
    .planet-pick input { accent-color: var(--blue); width: 18px; height: 18px; flex: none; }
    .planet-pick b { display: block; font-size: 14px; overflow-wrap: anywhere; line-height: 1.25; }
    .planet-pick small { display: block; font-size: 12px; line-height: 1.3; margin-top: 2px; color: var(--muted); }
    .row { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin: 4px 0 20px; font-size: 14px; }
    .row label { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
    .row input { accent-color: var(--blue); width: 16px; height: 16px; }
    .row a { color: var(--blue); font-weight: 600; }
    .row a:hover, .foot a:hover { text-decoration: underline; }

    .btn { width: 100%; height: 50px; border: 0; border-radius: 12px; cursor: pointer; background: var(--blue); color: #fff; font-family: var(--display); font-weight: 600; font-size: 17px; box-shadow: 0 4px 0 var(--blue-dark); transition: background .2s ease, transform .1s ease, box-shadow .1s ease; margin-top: 4px; }
    .btn:hover { background: #2569c2; }
    .btn:active { transform: translateY(3px); box-shadow: 0 1px 0 var(--blue-dark); }
    .foot { text-align: center; margin-top: 14px; font-size: 14px; color: var(--muted); }
    .foot a { color: var(--blue); font-weight: 600; }
    .err { background: #fff1f0; border: 1px solid #f5c2bd; color: #b42318; padding: 10px 14px; border-radius: 12px; font-size: 14px; margin-bottom: 14px; }

    @media (prefers-reduced-transparency: reduce) { .card { background: #0c0a2a; -webkit-backdrop-filter: none; backdrop-filter: none; } }
    @media (max-width: 800px) {
      .card { grid-template-columns: 1fr; max-width: 480px; }
      .side { padding: 20px 20px 24px; border-right: 0; border-bottom: 1px solid var(--line); }
      .stage { width: 84px; height: 80px; } .mascot svg { width: 76px; }
      .panel { padding: 24px 20px 22px; }
    }
    @media (max-width: 480px) { form > .half { grid-column: 1 / -1; } .planet-pick .tiles { grid-template-columns: 1fr; } .planet-pick label { flex-direction: row; align-items: center; } }
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { animation: none !important; transition: none !important; }
      .card { opacity: 1; transform: none; filter: none; }
      .twinkle { display: none; }
    }
  </style>
</head>
<body>
  <x-cosmic-background :planet="false" />

  <main>
    <div class="card">
      <aside class="side">
        <x-brand href="/" />
        <div class="side-body">
          <div class="stage" aria-hidden="true">
            @if (trim($__env->yieldContent('audience', 'student')) !== 'student')
              <div class="mascot on"><x-captain-mascot /><i class="twinkle a"></i><i class="twinkle b"></i></div>
            @else
              <div class="mascot on"><x-astro-mascot class="astro" /><i class="twinkle a"></i><i class="twinkle b"></i></div>
            @endif
          </div>
          @yield('heading')
        </div>
      </aside>

      <div class="panel">
        <form method="POST" action="@yield('action')">
          @csrf
          @yield('fields')
        </form>

        @yield('footer')
      </div>
    </div>
  </main>

  <script>
    (function () {
      var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      var word = document.getElementById('decode');
      var glyphs = '01<>/{}#$%&*+=?', timer;

      // Decode: scramble, then lock letters in one by one.
      function decode(target) {
        if (!word) return;
        clearInterval(timer);
        if (reduce) { word.textContent = target; return; }
        var n = 0, steps = target.length * 3;
        timer = setInterval(function () {
          var lock = Math.floor(n / 3), out = '';
          for (var k = 0; k < target.length; k++) out += k < lock ? target[k] : glyphs[Math.floor(Math.random() * glyphs.length)];
          word.textContent = out;
          if (++n > steps) { clearInterval(timer); word.textContent = target; }
        }, 38);
      }

      if (word) { var first = word.textContent; word.textContent = ''; setTimeout(function () { decode(first); }, 350); }
    })();
  </script>
</body>
</html>
