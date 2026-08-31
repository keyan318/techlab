{{-- Chapter story experience — full-screen comic panel viewer.
     One scene at a time over the dark cosmic backdrop; Next advances,
     progress dots up top, final panel swaps Next for "Begin Mission",
     which hands off to the interactive lesson (mission-url).
     Captions are VERBATIM from the approved Chapter 1 reference art. --}}
@props([
  'chapterNumber' => '1',
  'chapterTitle' => 'The Landing',
  'panels' => null,
  'missionUrl' => '#',
])

@php
  // Scene placeholders — stylized SVG vignettes standing in for comic art.
  $sceneStars = function ($seed = 7) {
    $dots = '';
    mt_srand($seed);
    for ($i = 0; $i < 42; $i++) {
      $x = mt_rand(4, 96);
      $y = mt_rand(4, 70);
      $r = mt_rand(5, 13) / 10;
      $o = mt_rand(35, 90) / 100;
      $dots .= "<circle cx='$x' cy='$y' r='$r' fill='#dfe8ff' opacity='$o'/>";
    }
    return $dots;
  };

  // 1 — drifting ship among dead stars / debris field.
  $sceneDrift = '<svg viewBox="0 0 100 56" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
    '.$sceneStars(11).'
    <g opacity=".5"><circle cx="18" cy="18" r="7" fill="#39315f"/><circle cx="30" cy="30" r="4" fill="#2c2750"/><rect x="60" y="14" width="10" height="4" rx="2" fill="#332c58" transform="rotate(24 65 16)"/></g>
    <g transform="rotate(-8 50 34)">
      <ellipse cx="50" cy="34" rx="13" ry="5.5" fill="#aab6dd"/>
      <path d="M37 34c3-4 8-6 13-6l-3 6Z" fill="#73b6ff"/>
      <circle cx="52" cy="32.5" r="2.2" fill="#5be1ff"/>
      <path d="M62 33h6" stroke="#f0803f" stroke-width="1.6" stroke-linecap="round"/>
      <path d="M68 31.4l4 1.6-4 1.6Z" fill="#ffb066"/>
    </g>
  </svg>';

  // 2 — Codexia looming: planet fills the frame.
  $sceneCodexia = '<svg viewBox="0 0 100 56" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
    '.$sceneStars(23).'
    <defs><radialGradient id="stCx" cx="38%" cy="30%" r="80%"><stop offset="0%" stop-color="#cfe6ff"/><stop offset="55%" stop-color="#73b6ff"/><stop offset="100%" stop-color="#235bbf"/></radialGradient></defs>
    <circle cx="72" cy="46" r="34" fill="url(#stCx)"/>
    <path d="M40 20q10-6 22-3t16 8" stroke="#eaf4ff" stroke-width="1.4" fill="none" opacity=".5" stroke-linecap="round"/>
    <g transform="translate(26 18) scale(.5) rotate(-14)">
      <ellipse cx="0" cy="0" rx="13" ry="5.5" fill="#aab6dd"/><circle cx="2" cy="-1.5" r="2.2" fill="#5be1ff"/>
    </g>
  </svg>';

  // 3 — engines out: dark ship, dead glow.
  $sceneEnginesOut = '<svg viewBox="0 0 100 56" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
    '.$sceneStars(31).'
    <g transform="rotate(6 48 28)">
      <ellipse cx="48" cy="28" rx="14" ry="6" fill="#8d97ba"/>
      <circle cx="51" cy="26" r="2.4" fill="#39415e"/>
      <path d="M61 27h5" stroke="#3a4160" stroke-width="1.6" stroke-linecap="round"/>
      <circle cx="68" cy="27" r="1" fill="#f0803f" opacity=".45"/>
    </g>
    <path d="M20 46q14-3 28-1" stroke="#2c2750" stroke-width="1" fill="none"/>
  </svg>';

  // 4 — skid across open dust toward two moons.
  $sceneSkid = '<svg viewBox="0 0 100 56" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
    <defs><linearGradient id="stSky" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#120a33"/><stop offset="100%" stop-color="#1e1259"/></linearGradient></defs>
    <rect width="100" height="56" fill="url(#stSky)"/>
    '.$sceneStars(43).'
    <circle cx="24" cy="12" r="5" fill="#cfe6ff" opacity=".9"/>
    <circle cx="36" cy="9" r="3.4" fill="#8fa8d8" opacity=".85"/>
    <path d="M0 44q25-6 50-3t50 2v13H0Z" fill="#241d49"/>
    <path d="M8 47q30-4 84-1" stroke="#4a3f86" stroke-width="2" fill="none" stroke-linecap="round"/>
    <g transform="translate(58 38) rotate(-4)">
      <ellipse cx="0" cy="0" rx="11" ry="4.6" fill="#aab6dd"/><circle cx="1.6" cy="-1.2" r="1.8" fill="#5be1ff"/>
    </g>
    <path d="M30 41q12 2 16 1" stroke="#6b5fb0" stroke-width="1.6" fill="none" stroke-linecap="round" opacity=".8"/>
  </svg>';

  // 5 — empty field stretching out.
  $sceneField = '<svg viewBox="0 0 100 56" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
    <defs><linearGradient id="stFl" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#16103a"/><stop offset="58%" stop-color="#241d49"/><stop offset="100%" stop-color="#120a33"/></linearGradient></defs>
    <rect width="100" height="56" fill="url(#stFl)"/>
    '.$sceneStars(57).'
    <circle cx="80" cy="10" r="4" fill="#cfe6ff" opacity=".8"/>
    <path d="M0 40h100v16H0Z" fill="#1a1440"/>
    <path d="M0 40h100" stroke="#4a3f86" stroke-width="1" opacity=".7"/>
    <path d="M50 40v16M30 40L14 56M70 40l16 16" stroke="#241d49" stroke-width=".8"/>
  </svg>';

  // 6 — Astro climbs out of the wreck.
  $sceneClimb = '<svg viewBox="0 0 100 56" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
    <defs><linearGradient id="stCl" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#141038"/><stop offset="100%" stop-color="#1e1259"/></linearGradient></defs>
    <rect width="100" height="56" fill="url(#stCl)"/>
    '.$sceneStars(71).'
    <circle cx="18" cy="10" r="4.4" fill="#cfe6ff" opacity=".85"/>
    <circle cx="29" cy="7" r="3" fill="#8fa8d8" opacity=".8"/>
    <path d="M0 42q30-5 62-2t38 3v13H0Z" fill="#191343"/>
    <g transform="translate(64 30) skewX(-14)">
      <path d="M-10 8 8 4l6 3-4 4-16 1Z" fill="#5a6288"/>
      <ellipse cx="-2" cy="2" rx="8" ry="3.4" fill="#8d97ba"/>
    </g>
    <g transform="translate(38 30)">
      <circle cx="0" cy="-4" r="4.6" fill="#c9d4ff"/><ellipse cx="0" cy="-4.4" rx="3.2" ry="2.9" fill="#73b6ff"/>
      <path d="M-4 1q4 3 8 0l1.4 6.4Q0 10.4-5.4 7.4Z" fill="#c9d4ff"/>
    </g>
  </svg>';

  // 7 — close-up: Astro declares he will build.
  $sceneResolve = '<svg viewBox="0 0 100 56" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
    <defs><radialGradient id="stRs" cx="50%" cy="40%" r="75%"><stop offset="0%" stop-color="#2d1b69"/><stop offset="100%" stop-color="#0d0827"/></radialGradient></defs>
    <rect width="100" height="56" fill="url(#stRs)"/>
    '.$sceneStars(89).'
    <circle cx="82" cy="12" r="5" fill="#cfe6ff" opacity=".7"/>
    <g transform="translate(50 34) scale(1.9)">
      <circle cx="0" cy="-5" r="6.2" fill="#c9d4ff" stroke="#8a98d8" stroke-width=".7"/>
      <ellipse cx="0" cy="-5.4" rx="4.4" ry="4" fill="#73b6ff"/>
      <ellipse cx="0" cy="-5.4" rx="4.4" ry="4" fill="none" stroke="#9b6bff" stroke-width=".8"/>
      <line x1="0" y1="-12.6" x2="0" y2="-15" stroke="#9b6bff" stroke-width=".9"/>
      <circle cx="0" y="-15.8" cy="-15.8" r="1" fill="#5be1ff"/>
    </g>
  </svg>';

  // Panels — captions VERBATIM from the Chapter 1 reference art.
  $samplePanels = [
    ['caption' => 'His ship had been drifting for longer than he could count - past dead stars, through a field of debris that used to be someone else\'s planet, running on fumes and stubbornness.', 'align' => 'right', 'art' => $sceneDrift],
    ['caption' => "Astro didn't choose Codexia.\nCodexia chose him.", 'align' => 'left', 'emph' => true, 'art' => $sceneCodexia],
    ['caption' => 'Then, one quiet rotation, the engines gave out entirely.', 'align' => 'left', 'art' => $sceneEnginesOut],
    ['caption' => "He didn't crash so much as arrive badly. The ship skidded across open dust and came to a stop at the edge of a wide, empty field, under a sky he didn't recognize - two moons instead of one.", 'align' => 'right', 'art' => $sceneSkid],
    ['caption' => 'No signal. No rescue beacon. No map. Just land, stretching out in every direction.', 'align' => 'left', 'art' => $sceneField],
    ['caption' => 'Astro climbed out of the wreck, dusted himself off, and looked at the empty field like it was a question waiting for an answer.', 'align' => 'center', 'astro' => true, 'art' => $sceneClimb],
    ['caption' => '"Alright. If I\'m stuck here - I\'m not just going to survive here. I\'m going to build something here."', 'align' => 'center', 'astro' => true, 'speech' => true, 'art' => $sceneResolve],
  ];
  $panels ??= $samplePanels;
  $total = max(1, count($panels));
@endphp

<section class="cst" data-total="{{ $total }}">
  <div class="cst-space" aria-hidden="true"></div>

  <header class="cst-top">
    <span class="cst-chapter">Chapter {{ $chapterNumber }} —</span>
    <div class="cst-dots" role="progressbar" aria-label="Story progress"
         aria-valuemin="1" aria-valuemax="{{ $total }}" aria-valuenow="1">
      @for ($i = 1; $i <= $total; $i++)
        <button type="button" class="cst-dot {{ $i === 1 ? 'is-on' : '' }}" data-cst-dot="{{ $i }}"
                aria-label="Panel {{ $i }}"></button>
      @endfor
    </div>
    <a class="cst-exit" href="{{ url()->previous() ?: route('student.planet.overview', ['slug' => 'programming']) }}" aria-label="Back to learning plan">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 6 12 12 6 18"/><polyline points="13 6 19 12 13 18"/></svg>
    </a>
  </header>

  <p class="cst-sr" data-cst-live role="status" aria-live="polite">Panel 1 of {{ $total }}</p>

  <main class="cst-stage">
    @foreach ($panels as $pIndex => $panel)
      <article class="cst-panel {{ $pIndex === 0 ? 'is-active' : '' }}" data-cst-panel
               @unless($pIndex === 0) hidden @endunless>
        <div class="cst-art">{!! $panel['art'] !!}</div>

        @if (!empty($panel['astro']))
          <span class="cst-mascot" aria-hidden="true">@include('student.chat.partials.astro-mascot')</span>
        @endif

        <figcaption class="cst-caption align-{{ $panel['align'] ?? 'left' }}
                          {{ !empty($panel['emph']) ? 'is-emph' : '' }}
                          {{ !empty($panel['speech']) ? 'is-speech' : '' }}">
          {!! nl2br(e(strtoupper($panel['caption']))) !!}
        </figcaption>
      </article>
    @endforeach
  </main>

  <footer class="cst-foot">
    <button type="button" class="cst-next" data-cst-next>Next</button>
    <a class="cst-next is-final" data-cst-begin href="{{ $missionUrl }}" hidden>Begin Mission</a>
  </footer>
</section>

<style>
  .cst {
    /* Cosmic tokens — same recipe as the planet pages. */
    --cst-void: #06061a;
    --cst-text: #eaeeff;
    --cst-muted: #98a2d4;
    --cst-blue: #73b6ff;
    --cst-violet: #9b6bff;
    --cst-cyan: #5be1ff;
    --cst-glass: rgba(123, 142, 220, .07);
    --cst-glass-border: rgba(150, 170, 255, .18);

    position: relative;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    min-height: 100vh;
    overflow: clip;
    font-family: 'Inter', system-ui, sans-serif;
    color: var(--cst-text);
    background: var(--cst-void);
  }
  .cst *, .cst *::before, .cst *::after { box-sizing: border-box; }

  .cst-space {
    position: fixed;
    inset: 0;
    z-index: 0;
    background:
      radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36, 20, 86, 0) 55%),
      linear-gradient(160deg, #0a0826, #120a33 45%, #1e1259);
  }

  /* ---------- Top bar: chapter label · dots · exit ---------- */
  .cst-top {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 20px clamp(18px, 4vw, 44px) 8px;
  }
  .cst-chapter {
    font-family: 'Space Mono', monospace;
    font-weight: 700;
    font-size: .74rem;
    letter-spacing: .2em;
    color: var(--cst-blue);
    white-space: nowrap;
  }
  .cst-dots { display: flex; gap: 9px; flex: 1 1 auto; justify-content: center; }
  .cst-dot {
    width: 10px;
    height: 10px;
    padding: 0;
    border-radius: 50%;
    border: 1.5px solid var(--cst-glass-border);
    background: transparent;
    cursor: pointer;
    transition: background-color .2s ease, border-color .2s ease, transform .2s ease;
  }
  .cst-dot.is-on {
    background: var(--cst-blue);
    border-color: var(--cst-blue);
    transform: scale(1.15);
  }
  .cst-dot:focus-visible { outline: 2px solid var(--cst-cyan); outline-offset: 3px; }

  .cst-exit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 12px;
    color: var(--cst-muted);
    transition: background-color .15s ease, color .15s ease;
  }
  .cst-exit:hover { background: var(--cst-glass); color: var(--cst-text); }
  .cst-exit svg { width: 18px; height: 18px; }

  /* ---------- Stage ---------- */
  .cst-stage {
    position: relative;
    z-index: 1;
    flex: 1 1 auto;
    display: grid;
    padding: 10px clamp(16px, 4vw, 44px) 8px;
  }
  .cst-panel {
    grid-area: 1 / 1;
    position: relative;
    display: flex;
    flex-direction: column;
    border: 1px solid var(--cst-glass-border);
    border-radius: 24px;
    background: rgba(10, 8, 38, .55);
    box-shadow: 0 30px 80px rgba(0, 0, 0, .45);
    overflow: hidden;
    min-height: 0;
  }
  .cst-panel[hidden] { display: none !important; }
  .cst-panel.is-active { animation: cst-fade .35s ease both; }
  @keyframes cst-fade {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .cst-art { flex: 1 1 auto; min-height: 0; }
  .cst-art svg { width: 100%; height: 100%; display: block; }

  .cst-mascot {
    position: absolute;
    left: 50%;
    bottom: calc(min(30vh, 300px));
    transform: translateX(-50%);
    width: clamp(84px, 12vw, 128px);
    filter: drop-shadow(0 10px 30px rgba(115, 182, 255, .45));
  }

  /* Caption plate — uppercase overlay, per-reference voice. */
  .cst-caption {
    margin: 0;
    align-self: flex-start;
    max-width: min(46ch, 88%);
    padding: 16px 20px;
    font-family: 'Space Mono', monospace;
    font-weight: 700;
    font-size: clamp(.78rem, 1.6vw, .98rem);
    line-height: 1.75;
    letter-spacing: .06em;
    color: var(--cst-text);
    background: linear-gradient(180deg, rgba(6, 6, 26, .88), rgba(6, 6, 26, .72));
    border-top: 1px solid var(--cst-glass-border);
  }
  .cst-caption.align-right { align-self: flex-end; text-align: right; }
  .cst-caption.align-center { align-self: center; text-align: center; }
  .cst-caption.is-emph {
    font-family: 'Space Grotesk', sans-serif;
    font-size: clamp(1.3rem, 3.4vw, 2rem);
    line-height: 1.35;
    letter-spacing: .02em;
    background: none;
    border-top: none;
    padding-top: 0;
  }
  .cst-caption.is-emph.align-left { align-self: flex-start; }
  .cst-caption.is-speech {
    border: 1px solid rgba(115, 182, 255, .45);
    border-radius: 18px;
    background: linear-gradient(180deg, rgba(20, 16, 56, .92), rgba(12, 9, 40, .85));
    box-shadow: 0 16px 44px rgba(0, 0, 0, .5), inset 0 0 0 1px rgba(91, 225, 255, .12);
    margin-bottom: 18px;
    text-align: center;
  }

  /* ---------- Footer nav ---------- */
  .cst-foot {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: flex-end;
    padding: 12px clamp(16px, 4vw, 44px) calc(18px + env(safe-area-inset-bottom));
  }
  .cst-next {
    min-width: 168px;
    padding: 14px 34px;
    border: none;
    border-radius: 999px;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 600;
    font-size: .98rem;
    letter-spacing: .01em;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
    color: var(--cst-void);
    background: linear-gradient(135deg, var(--cst-cyan), var(--cst-blue) 55%, var(--cst-violet));
    box-shadow: 0 14px 34px rgba(91, 130, 255, .35);
    transition: transform .15s ease, box-shadow .2s ease, filter .15s ease;
  }
  .cst-next:hover { transform: translateY(-1px); filter: brightness(1.06); }
  .cst-next.is-final {
    background: linear-gradient(135deg, var(--cst-blue), var(--cst-violet));
    color: #ffffff;
  }
  .cst-next[hidden] { display: none !important; }
  .cst-next:focus-visible { outline: 3px solid var(--cst-cyan); outline-offset: 2px; }

  .cst-sr {
    position: absolute;
    width: 1px; height: 1px;
    margin: -1px; padding: 0;
    overflow: hidden;
    clip: rect(0 0 0 0);
    white-space: nowrap;
    border: 0;
  }

  @media (max-width: 640px) {
    .cst-caption { max-width: 94%; }
    .cst-mascot { bottom: min(34vh, 320px); }
  }
  @media (prefers-reduced-motion: reduce) {
    .cst-panel.is-active, .cst-dot, .cst-next { animation: none !important; transition: none !important; }
    .cst-next:hover { transform: none; }
  }
</style>

<script>
  /* Panel advance: Next steps forward, dots jump, last panel swaps the
     button for the Begin Mission link into the interactive lesson. */
  (function () {
    document.querySelectorAll('.cst').forEach(function (root) {
      if (root.dataset.cstBound) return;
      root.dataset.cstBound = '1';

      var panels = Array.prototype.slice.call(root.querySelectorAll('[data-cst-panel]'));
      var dots = Array.prototype.slice.call(root.querySelectorAll('[data-cst-dot]'));
      var bar = root.querySelector('.cst-dots');
      var live = root.querySelector('[data-cst-live]');
      var nextBtn = root.querySelector('[data-cst-next]');
      var beginLink = root.querySelector('[data-cst-begin]');
      var total = panels.length;
      var index = 0;

      function show(n) {
        index = Math.max(0, Math.min(total - 1, n));
        panels.forEach(function (panel, i) {
          var active = i === index;
          panel.hidden = !active;
          panel.classList.toggle('is-active', active);
        });
        dots.forEach(function (dot, i) {
          dot.classList.toggle('is-on', i <= index);
          dot.setAttribute('aria-current', i === index ? 'true' : 'false');
        });
        if (bar) bar.setAttribute('aria-valuenow', String(index + 1));
        if (live) live.textContent = 'Panel ' + (index + 1) + ' of ' + total;
        var last = index === total - 1;
        nextBtn.hidden = last;
        beginLink.hidden = !last;
      }

      nextBtn.addEventListener('click', function () { show(index + 1); });
      dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
          show(parseInt(dot.getAttribute('data-cst-dot'), 10) - 1);
        });
      });
      document.addEventListener('keydown', function (e) {
        if (document.activeElement && /INPUT|TEXTAREA/.test(document.activeElement.tagName)) return;
        if (e.key === 'ArrowRight') show(index + 1);
        if (e.key === 'ArrowLeft') show(index - 1);
      });

      show(0);
    });
  })();
</script>
