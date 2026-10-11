{{--
  Certificate of completion (CourseCertificateController). A page of its own, not inside the
  pixel-app shell, so it can be screenshotted or printed with nothing around it. The certificate
  is laid out at a fixed landscape ratio and sized in container units, so it scales as one piece
  to whatever width the window gives it.
--}}
@php
  $corner = ['11111111', '1.......', '1.111...', '1.1.....', '1.1.....', '1.......', '1.......', '1.......'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Certificate · {{ $courseTitle }} · TechLab</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&family=Pixelify+Sans:wght@500;600;700&family=Caveat:wght@600&display=swap" rel="stylesheet" />
  <meta name="theme-color" content="#06061a">

  <style>
    :root {
      --void: #06061a;
      --paper: #0e0b35;
      --ink: #eef1ff;
      --muted: #a9b0dc;
      --blue: #73b6ff;
      --gold: #f8c81c;
      --gold-soft: #ffe48a;
      --gold-deep: #a36a00;
    }
    * { box-sizing: border-box; }
    html, body { margin: 0; }
    /* Zero specificity, so each element's own margin-top below still applies. */
    :where(.cert) :is(h1, p, ul) { margin: 0; }
    body {
      min-height: 100vh; color: var(--ink); font-family: Inter, system-ui, sans-serif; -webkit-font-smoothing: antialiased;
      background: radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%), linear-gradient(160deg, #0a0826 0%, #120a33 45%, #1e1259 100%) fixed, var(--void);
    }

    /* Toolbar: back, print. Hidden when printing. */
    .bar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; max-width: 1180px; margin: 0 auto; padding: 20px 16px 0; }
    .bar a, .bar button {
      display: inline-flex; align-items: center; gap: 8px; min-height: 42px; padding: 0 18px; margin: 3px; border: 0; border-radius: 2px;
      font: 600 16px/1 'Pixelify Sans', 'Space Grotesk', sans-serif; color: #fff; text-decoration: none; cursor: pointer;
      background: rgba(255,255,255,.08);
      box-shadow: 0 -3px 0 0 rgba(150,170,255,.4), 0 3px 0 0 rgba(150,170,255,.4), -3px 0 0 0 rgba(150,170,255,.4), 3px 0 0 0 rgba(150,170,255,.4);
      transition: transform .1s ease, filter .15s ease;
    }
    .bar button { color: #1a1200; background: var(--gold); box-shadow: 0 -3px 0 0 var(--gold-deep), 0 3px 0 0 var(--gold-deep), -3px 0 0 0 var(--gold-deep), 3px 0 0 0 var(--gold-deep), inset 0 -4px 0 0 rgba(0,0,0,.2); }
    .bar a:hover, .bar button:hover { filter: brightness(1.1); }
    .bar a:active, .bar button:active { transform: translateY(2px); }
    .bar a:focus-visible, .bar button:focus-visible { outline: 3px solid #9ad0ff; outline-offset: 5px; }

    .stage { max-width: 1180px; margin: 0 auto; padding: 28px 16px 56px; }

    /* ── The certificate ── */
    .cert {
      position: relative; container-type: inline-size; width: 100%; aspect-ratio: 1.414 / 1; overflow: hidden; isolation: isolate;
      background:
        radial-gradient(48% 42% at 50% 44%, rgba(122,92,255,.30) 0%, rgba(122,92,255,0) 100%),
        radial-gradient(70% 55% at 50% 125%, rgba(115,182,255,.28) 0%, rgba(115,182,255,0) 70%),
        linear-gradient(165deg, #120f42 0%, var(--paper) 50%, #0a0828 100%);
      box-shadow: 0 30px 80px -20px rgba(0,0,0,.7), 0 0 0 1px rgba(248,200,28,.25);
      animation: cert-in .5s cubic-bezier(.32,.72,0,1) both;
    }
    @keyframes cert-in { from { opacity: 0; transform: translateY(12px) scale(.985); } }

    /* Fixed starfield, so every copy of the certificate looks the same. */
    .stars { position: absolute; inset: 0; z-index: -1; opacity: .9;
      background-image:
        radial-gradient(1.5px 1.5px at 7% 12%, #fff 50%, transparent 51%), radial-gradient(1px 1px at 16% 31%, #cfd8ff 50%, transparent 51%),
        radial-gradient(1.5px 1.5px at 25% 8%, #fff 50%, transparent 51%), radial-gradient(1px 1px at 33% 22%, #cfd8ff 50%, transparent 51%),
        radial-gradient(1px 1px at 44% 9%, #fff 50%, transparent 51%), radial-gradient(1.5px 1.5px at 58% 15%, #ffe48a 50%, transparent 51%),
        radial-gradient(1px 1px at 69% 7%, #fff 50%, transparent 51%), radial-gradient(1.5px 1.5px at 81% 19%, #cfd8ff 50%, transparent 51%),
        radial-gradient(1px 1px at 92% 10%, #fff 50%, transparent 51%), radial-gradient(1px 1px at 6% 58%, #cfd8ff 50%, transparent 51%),
        radial-gradient(1.5px 1.5px at 11% 81%, #fff 50%, transparent 51%), radial-gradient(1px 1px at 22% 67%, #fff 50%, transparent 51%),
        radial-gradient(1px 1px at 88% 62%, #fff 50%, transparent 51%), radial-gradient(1.5px 1.5px at 95% 78%, #cfd8ff 50%, transparent 51%),
        radial-gradient(1px 1px at 79% 88%, #fff 50%, transparent 51%), radial-gradient(1px 1px at 63% 92%, #cfd8ff 50%, transparent 51%),
        radial-gradient(1.5px 1.5px at 36% 90%, #fff 50%, transparent 51%), radial-gradient(1px 1px at 50% 96%, #ffe48a 50%, transparent 51%);
    }

    /* Gold double frame with pixel corners */
    .frame { position: absolute; inset: 2.2cqw; border: .32cqw solid var(--gold); pointer-events: none; }
    .frame::before { content: ""; position: absolute; inset: .7cqw; border: .12cqw solid rgba(248,200,28,.45); }
    .corner { position: absolute; width: 3.4cqw; height: 3.4cqw; }
    .corner.tl { top: -.32cqw; left: -.32cqw; }
    .corner.tr { top: -.32cqw; right: -.32cqw; transform: scaleX(-1); }
    .corner.bl { bottom: -.32cqw; left: -.32cqw; transform: scaleY(-1); }
    .corner.br { bottom: -.32cqw; right: -.32cqw; transform: scale(-1, -1); }

    .body { position: absolute; inset: 4.6cqw 6cqw 4.9cqw; display: flex; flex-direction: column; align-items: center; text-align: center; }

    .top { display: flex; width: 100%; align-items: center; justify-content: space-between; }
    .brand { display: flex; align-items: center; gap: 1cqw; }
    .brand img { width: 3.8cqw; height: 3.8cqw; border-radius: .7cqw; }
    .brand span { font: 700 2.1cqw/1 'Space Grotesk', sans-serif; letter-spacing: -.01em; }
    .planet { display: flex; align-items: center; gap: .7cqw; font: 600 1.05cqw/1 'Space Mono', monospace; letter-spacing: .16em; text-transform: uppercase; color: var(--muted); }
    .planet .logo { width: 2.3cqw; height: 2.3cqw; display: grid; place-items: center; }
    .planet .logo svg { width: 100%; height: 100%; }

    .kicker { margin-top: 3cqw; padding-left: .2em; font: 700 4.6cqw/1 'Space Grotesk', sans-serif; letter-spacing: .2em; color: var(--gold);
      text-shadow: 0 .25cqw 0 var(--gold-deep), 0 0 2.4cqw rgba(248,200,28,.35); }
    .kicker-sub { margin-top: 1.3cqw; font: 500 1.35cqw/1 'Space Mono', monospace; letter-spacing: .42em; text-transform: uppercase; color: var(--gold-soft); }

    .lead { margin-top: 3.4cqw; font-size: 1.45cqw; color: var(--muted); }
    .name { margin-top: .9cqw; padding: 0 3cqw .9cqw; font: 700 4.9cqw/1.05 'Space Grotesk', sans-serif; letter-spacing: -.02em; color: #fff;
      border-bottom: .16cqw solid rgba(248,200,28,.6); max-width: 100%; overflow-wrap: anywhere; }
    .what { margin-top: 2.3cqw; max-width: 64cqw; font-size: 1.45cqw; line-height: 1.5; color: var(--muted); }
    .course { margin-top: .7cqw; font: 700 2.75cqw/1.15 'Space Grotesk', sans-serif; color: var(--blue); text-shadow: 0 0 2cqw rgba(115,182,255,.35); }
    .course-meta { margin-top: 1cqw; font: 500 1.15cqw/1.3 'Space Mono', monospace; letter-spacing: .06em; color: var(--muted); }
    .course-meta b { color: var(--gold-soft); font-weight: 700; }

    .stats { display: flex; gap: 1.6cqw; margin-top: 2.6cqw; padding: 0; list-style: none; }
    .stats li { margin: .3cqw; padding: .8cqw 1.5cqw; font: 500 1.2cqw/1 'Space Grotesk', sans-serif; letter-spacing: .02em; background: rgba(255,255,255,.06);
      box-shadow: 0 -.25cqw 0 0 rgba(150,170,255,.4), 0 .25cqw 0 0 rgba(150,170,255,.4), -.25cqw 0 0 0 rgba(150,170,255,.4), .25cqw 0 0 0 rgba(150,170,255,.4); }
    .stats b { color: var(--gold); font-weight: 700; margin-right: .35em; }

    .foot { margin-top: auto; display: grid; width: 100%; grid-template-columns: 1fr auto 1fr; align-items: end; gap: 3cqw; }
    .sign { display: flex; flex-direction: column; align-items: center; }
    .sign .who { display: flex; align-items: flex-end; gap: .8cqw; min-height: 4.2cqw; }
    .sign .who svg { width: 3.6cqw; height: auto; }
    .sign .script { font: 600 3.1cqw/1 'Caveat', cursive; color: #fff; }
    .sign .line { width: 100%; max-width: 24cqw; margin-top: .5cqw; border-top: .12cqw solid rgba(169,176,220,.6); }
    .sign .role { margin-top: .6cqw; font: 600 1.05cqw/1.2 'Space Mono', monospace; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); }

    .seal { position: relative; width: 12.5cqw; margin-bottom: -.6cqw; }
    .seal svg.medal { display: block; width: 100%; height: auto; filter: drop-shadow(0 .6cqw 1.2cqw rgba(0,0,0,.5)); }
    .seal .logo { position: absolute; left: 50%; top: 41%; width: 3.6cqw; height: 3.6cqw; transform: translate(-50%, -50%); }
    .seal .logo svg { width: 100%; height: 100%; }

    .meta { margin-top: 1.9cqw; font: 500 1cqw/1 'Space Mono', monospace; letter-spacing: .1em; color: var(--muted); }
    .meta b { color: var(--ink); font-weight: 700; }

    @media (prefers-reduced-motion: reduce) { .cert { animation: none; } .bar a, .bar button { transition: none; } }

    @page { size: A4 landscape; margin: 0; }
    @media print {
      body { background: var(--void); -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .bar { display: none; }
      .stage { max-width: none; padding: 0; }
      .cert { box-shadow: none; animation: none; }
    }
  </style>
  <script defer src="{{ asset('js/sfx.js') }}?v={{ filemtime(public_path('js/sfx.js')) }}"></script>
  <link rel="stylesheet" href="{{ asset('css/cursor.css') }}?v={{ filemtime(public_path('css/cursor.css')) }}">
</head>
<body>
  <nav class="bar" aria-label="Certificate actions">
    <a href="{{ $backUrl }}">← Back to course</a>
    <button type="button" onclick="window.print()">Print or save as PDF</button>
  </nav>

  <main class="stage">
    <article class="cert" aria-labelledby="cert-title">
      <div class="stars" aria-hidden="true"></div>

      <div class="frame" aria-hidden="true">
        @foreach (['tl', 'tr', 'bl', 'br'] as $pos)
          <svg class="corner {{ $pos }}" viewBox="0 0 8 8" shape-rendering="crispEdges">
            @foreach ($corner as $y => $row)
              @foreach (str_split($row) as $x => $ch)
                @if ($ch === '1')<rect x="{{ $x }}" y="{{ $y }}" width="1" height="1" fill="#f8c81c"/>@endif
              @endforeach
            @endforeach
          </svg>
        @endforeach
      </div>

      <div class="body">
        <div class="top">
          <div class="brand">
            <img src="{{ asset('apple-touch-icon.png') }}" alt="">
            <span>TechLab</span>
          </div>
          @if ($planetTitle)
            <div class="planet">
              @if ($logo)<span class="logo"><x-dynamic-component :component="$logo" /></span>@endif
              {{ $planetTitle }}
            </div>
          @endif
        </div>

        <h1 id="cert-title" class="kicker">CERTIFICATE</h1>
        <p class="kicker-sub">of Completion</p>

        <p class="lead">This certifies that</p>
        <p class="name">{{ $studentName }}</p>

        <p class="what">
          has completed every lesson{{ $labs > 0 ? ' and every hands-on lab' : '' }} of the TechLab course
        </p>
        <p class="course">{{ $courseTitle }}</p>
        @if ($courseCode || $tagline)
          <p class="course-meta">
            @if ($courseCode)<b>{{ $courseCode }}</b>@endif
            @if ($courseCode && $tagline) · @endif
            {{ $tagline }}
          </p>
        @endif

        <ul class="stats" aria-label="Course totals">
          <li><b>{{ $chapters }}</b>{{ \Illuminate\Support\Str::plural('Chapter', $chapters) }}</li>
          <li><b>{{ $lessons }}</b>{{ \Illuminate\Support\Str::plural('Lesson', $lessons) }}</li>
          @if ($labs > 0)
            <li><b>{{ $labs }}</b>{{ \Illuminate\Support\Str::plural('Lab', $labs) }}</li>
          @endif
          <li><b>{{ number_format($xp) }}</b>XP</li>
        </ul>

        <div class="foot">
          <div class="sign">
            <div class="who"><x-astro-mascot /><span class="script">Astro</span></div>
            <div class="line"></div>
            <p class="role">AI Tutor · TechLab</p>
          </div>

          <div class="seal" aria-hidden="true">
            <svg class="medal" viewBox="0 0 120 132">
              <defs>
                <linearGradient id="seal-gold" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0" stop-color="#ffe48a"/><stop offset=".5" stop-color="#f8c81c"/><stop offset="1" stop-color="#c98a00"/>
                </linearGradient>
                <path id="seal-ring" d="M60 22 a32 32 0 1 1 -0.01 0"/>
              </defs>
              {{-- ribbons --}}
              <path d="M36 82 L24 128 L38 120 L46 131 L56 92 Z" fill="#2f4fd0"/>
              <path d="M84 82 L96 128 L82 120 L74 131 L64 92 Z" fill="#2f4fd0"/>
              <path d="M36 82 L30 105 L48 96 Z" fill="#73b6ff" opacity=".5"/>
              <path d="M84 82 L90 105 L72 96 Z" fill="#73b6ff" opacity=".5"/>
              {{-- starburst rim --}}
              <g fill="url(#seal-gold)">
                @for ($i = 0; $i < 24; $i++)
                  <rect x="55" y="4" width="10" height="10" transform="rotate({{ $i * 15 }} 60 54)"/>
                @endfor
              </g>
              <circle cx="60" cy="54" r="45" fill="url(#seal-gold)"/>
              <circle cx="60" cy="54" r="40" fill="#120f42"/>
              <circle cx="60" cy="54" r="38" fill="none" stroke="#f8c81c" stroke-width="1"/>
              <text font-family="Space Mono, monospace" font-size="7.2" font-weight="700" letter-spacing="1.6" fill="#ffe48a">
                <textPath href="#seal-ring" startOffset="0" textLength="199" lengthAdjust="spacing">COURSE COMPLETE ★ TECHLAB ★&#160;</textPath>
              </text>
            </svg>
            @if ($logo)<span class="logo"><x-dynamic-component :component="$logo" /></span>@endif
          </div>

          <div class="sign">
            <div class="who"><span class="script">{{ $captain ?? 'TechLab' }}</span></div>
            <div class="line"></div>
            <p class="role">{{ $captain ? 'Faculty Captain' : 'TechLab Academy' }}</p>
          </div>
        </div>

        <p class="meta">
          Issued <b>{{ $issuedAt->format('F j, Y') }}</b> &nbsp;·&nbsp; Certificate No. <b>{{ $certificateId }}</b>
        </p>
      </div>
    </article>
  </main>
</body>
</html>
