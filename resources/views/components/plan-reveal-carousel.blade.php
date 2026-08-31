{{-- Plan reveal carousel — shown after the onboarding questions finish.
     Three hardcoded slides, each teasing one real TechLab feature inside a
     floating card mockup. Purely static: no backend, not yet wired to the
     onboarding flow (the Continue on the last slide fires
     `plan-reveal:complete` / redirects via `completeUrl` when that day comes).
     Reuses the onboarding-question visual language: same segmented progress,
     tokens, column layout, and pill button. --}}
@props([
  'slides' => null,
  'completedSegments' => 3,   // segments already earned by the finished questions
  'backHref' => '#',
  'completeUrl' => null,
  'planEndpoint' => null,      // POST fired when the carousel starts (background)
  'destinationUrl' => null,    // where to land once slides are done AND plan is ready
])

@php
  $sampleSlides = [
    [
      'eyebrow' => 'Mission briefing',
      'badge' => 'Skill check',
      'headline' => 'Learn by doing — then prove it sticks',
    ],
    [
      'eyebrow' => 'Mission briefing',
      'badge' => 'Practice',
      'headline' => 'Smart practice, picked just for you',
    ],
    [
      'eyebrow' => 'Mission briefing',
      'badge' => 'First lesson',
      'headline' => 'Mission 01 is waiting, explorer',
    ],
  ];
  $slides ??= $sampleSlides;
  $totalSegments = $completedSegments + max(1, count($slides));
@endphp

<section class="prc" data-complete-url="{{ $completeUrl }}"
         @if($planEndpoint) data-plan-endpoint="{{ $planEndpoint }}" @endif
         @if($destinationUrl) data-destination-url="{{ $destinationUrl }}" @endif>
  <div class="prc-col">
  <header class="prc-top">
    <a class="prc-back" data-prc-back href="{{ $backHref }}" aria-label="Go back">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 5 8 12 15 19"/></svg>
    </a>
    {{-- Same segmented bar as onboarding — keeps advancing past the questions. --}}
    <div class="prc-progress" role="progressbar" aria-label="Onboarding progress"
         aria-valuemin="1" aria-valuemax="{{ $totalSegments }}" aria-valuenow="{{ $completedSegments + 1 }}">
      @for ($i = 1; $i <= $totalSegments; $i++)
        <span class="prc-seg {{ $i <= $completedSegments + 1 ? 'is-filled' : '' }}"></span>
      @endfor
    </div>
    <button type="button" class="prc-sound" data-prc-sound aria-label="Toggle sound" aria-pressed="false">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 9.5v5h3l4.5 3.8V5.7L7 9.5H4Z"/>
        <path class="prc-wave" d="M15.5 9.2a4 4 0 0 1 0 5.6"/>
        <path class="prc-wave" d="M17.8 7a7.2 7.2 0 0 1 0 10"/>
        <line class="prc-slash" x1="15" y1="9.5" x2="20" y2="14.5"/>
      </svg>
    </button>
  </header>

  <main class="prc-body">
    <p class="prc-sr" data-prc-live role="status" aria-live="polite">Slide 1 of {{ count($slides) }}</p>

    @foreach ($slides as $sIndex => $slide)
      <fieldset class="prc-slide {{ $sIndex === 0 ? 'is-active' : '' }}" data-prc-slide
                @unless($sIndex === 0) hidden @endunless>
        <legend class="prc-sr">Slide {{ $sIndex + 1 }}</legend>

        <p class="prc-eyebrow">{{ strtoupper($slide['eyebrow']) }}</p>
        <h2 class="prc-headline">{{ $slide['headline'] }}</h2>

        <div class="prc-stage">
          <span class="prc-badge">{{ strtoupper($slide['badge']) }}</span>

          @if ($sIndex === 0)
            {{-- Mockup: Skill check — fill-in-the-blank code challenge. --}}
            <div class="prc-mock" data-prc-mock="skill-check">
              <div class="prc-codebar">
                <span class="prc-dot"></span><span class="prc-dot"></span><span class="prc-dot"></span>
                <span class="prc-codefile">mission_01.check</span>
              </div>
              <pre class="prc-code">greeting = <span class="prc-blank">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>(<span class="prc-str">"Hello, explorer!"</span>)
<span class="prc-fn">print</span>(greeting)</pre>
              <div class="prc-choices">
                <span class="prc-chip">input()</span>
                <span class="prc-chip is-picked">print()</span>
                <span class="prc-chip">greet()</span>
              </div>
              <div class="prc-checkrow"><span class="prc-checkbtn">Check answer</span></div>
            </div>
          @elseif ($sIndex === 1)
            {{-- Mockup: Studio-generated practice flashcard. --}}
            <div class="prc-mock" data-prc-mock="practice">
              <div class="prc-mockhead">
                <span class="prc-studiochip">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.7 4.6L18 9l-4.3 1.4L12 15l-1.7-4.6L6 9l4.3-1.4L12 3Z"/><path d="M19 14l.8 2.2L22 17l-2.2.8L19 20l-.8-2.2L16 17l2.2-.8L19 14Z"/></svg>
                  Studio · Practice set
                </span>
              </div>
              <p class="prc-flash-q">What does a variable store?</p>
              <p class="prc-flash-hint">A value under a name you choose</p>
              <div class="prc-flashfoot">
                <span class="prc-flashcount">Card 3 of 10</span>
                <span class="prc-flipbtn">Flip card</span>
              </div>
            </div>
          @else
            {{-- Mockup: First lesson node. --}}
            <div class="prc-mock" data-prc-mock="lesson">
              <span class="prc-mission-tag">Mission 01</span>
              <p class="prc-lesson-title">Welcome to Programming City</p>
              <p class="prc-lesson-sub">Meet Astro and write your very first line of code.</p>
              <div class="prc-lessonfoot">
                <span class="prc-xp">+50 XP · ≈ 15 min</span>
                <span class="prc-startbtn">Start learning</span>
              </div>
            </div>
          @endif

          {{-- Astro peeking over the card's top-right corner. --}}
          <span class="prc-astro" aria-hidden="true">
            @include('student.chat.partials.astro-mascot')
          </span>
        </div>
      </fieldset>
    @endforeach
  </main>

  <footer class="prc-foot">
    <p class="prc-retry-hint" data-prc-retry-hint hidden>
      We couldn't generate your learning plan. Check your connection and try again.
    </p>
    <button type="button" class="prc-continue" data-prc-continue>Continue</button>
  </footer>
  </div>
</section>

<style>
  .prc {
    /* Scoped tokens — mirror onboarding-question. */
    --prc-ink: #0e1230;
    --prc-muted: #6a7190;
    --prc-faint: #a9b0ca;
    --prc-surface: #f2f4fb;
    --prc-border: #e4e8f5;
    --prc-blue: #73b6ff;
    --prc-violet: #9b6bff;
    --prc-cyan: #5be1ff;

    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    width: 100%;
    min-height: 100vh;          /* full-bleed white layer, edge to edge */
    overflow-x: clip;           /* Astro's corner-peek may exceed the gutter */
    font-family: 'Inter', system-ui, sans-serif;
    color: var(--prc-ink);
    background: #ffffff;
  }
  .prc-col {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    width: 100%;
    max-width: 620px;
    margin: 0 auto;
    padding: 0 20px calc(16px + env(safe-area-inset-bottom));
    box-sizing: border-box;
  }
  .prc *, .prc *::before, .prc *::after { box-sizing: border-box; }

  /* ---------- Top bar (same anatomy as onboarding) ---------- */
  .prc-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 18px 0 26px;
  }
  .prc-back, .prc-sound {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    flex: 0 0 auto;
    padding: 0;
    border: none;
    border-radius: 12px;
    background: transparent;
    color: var(--prc-ink);
    cursor: pointer;
    transition: background-color .15s ease;
  }
  .prc-back:hover, .prc-sound:hover { background: var(--prc-surface); }
  .prc-back svg, .prc-sound svg { width: 22px; height: 22px; }

  .prc-progress { display: flex; flex: 1 1 auto; gap: 6px; }
  .prc-seg {
    height: 9px;
    flex: 1 1 0;
    border-radius: 999px;
    background: #e9edf7;
    transition: background-color .25s ease;
  }
  .prc-seg.is-filled { background: var(--prc-blue); }

  .prc-sound .prc-slash { opacity: 0; transition: opacity .15s ease; }
  .prc-sound.is-muted .prc-wave { opacity: 0; }
  .prc-sound.is-muted .prc-slash { opacity: 1; }

  /* ---------- Slides ---------- */
  .prc-body {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 30px;
    padding-bottom: 28px;
  }
  .prc-slide {
    display: flex;
    flex-direction: column;
    gap: 26px;
    border: none;
    margin: 0;
    padding: 0;
    min-width: 0;
  }
  .prc-slide[hidden] { display: none !important; }
  .prc-slide.is-active { animation: prc-fade .28s ease both; }
  @keyframes prc-fade {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .prc-sr {
    position: absolute;
    width: 1px; height: 1px;
    margin: -1px; padding: 0;
    overflow: hidden;
    clip: rect(0 0 0 0);
    white-space: nowrap;
    border: 0;
  }

  /* ---------- Copy ---------- */
  .prc-eyebrow {
    margin: 0;
    font-family: 'Space Mono', monospace;
    font-weight: 700;
    font-size: .72rem;
    letter-spacing: .22em;
    color: var(--prc-blue);
    text-align: center;
  }
  .prc-headline {
    margin: -8px auto 0;
    max-width: 21ch;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700;
    font-size: clamp(1.45rem, 4.6vw, 1.95rem);
    line-height: 1.18;
    letter-spacing: -0.02em;
    text-align: center;
    text-wrap: balance;
  }

  /* ---------- Floating card stage ---------- */
  .prc-stage {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    padding-top: 10px;
  }
  .prc-badge {
    padding: 7px 16px;
    border-radius: 999px;
    background: linear-gradient(135deg, rgba(115,182,255,.16), rgba(155,107,255,.13));
    border: 1px solid rgba(115, 182, 255, .38);
    color: #3d6ea8;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 600;
    font-size: .7rem;
    letter-spacing: .16em;
  }
  .prc-mock {
    position: relative;
    width: min(420px, 100%);
    background: #ffffff;
    border: 1px solid var(--prc-border);
    border-radius: 22px;
    padding: 22px;
    box-shadow: 0 24px 60px rgba(14, 18, 48, .13),
                0 6px 18px rgba(115, 182, 255, .10);
    animation: prc-float 5s ease-in-out infinite;
  }
  @keyframes prc-float {
    0%, 100% { transform: translateY(0); }
    50%      { transform: translateY(-7px); }
  }

  /* Astro peeking over the card's top-right edge. */
  .prc-astro {
    position: absolute;
    top: 0;
    right: 0;
    z-index: 1;
    width: 64px;
    height: 64px;
    display: grid;
    place-items: center;
    padding: 11px;
    transform: translate(26%, -46%) rotate(8deg);
    border-radius: 50%;
    background: linear-gradient(135deg, #eaf3ff, #f2ecff 55%, #e9fbff);
    border: 1px solid rgba(115, 182, 255, .45);
    box-shadow: 0 10px 24px rgba(14, 18, 48, .16);
  }

  /* --- Mock 1: skill check code challenge --- */
  .prc-codebar {
    display: flex;
    align-items: center;
    gap: 6px;
    padding-bottom: 12px;
  }
  .prc-dot { width: 9px; height: 9px; border-radius: 50%; background: #dfe4f1; }
  .prc-codefile {
    margin-left: auto;
    font-family: 'Space Mono', monospace;
    font-size: .68rem;
    color: var(--prc-faint);
  }
  .prc-code {
    margin: 0;
    padding: 16px;
    border-radius: 14px;
    background: var(--prc-ink);
    color: #dfe6ff;
    font-family: 'Space Mono', monospace;
    font-size: .82rem;
    line-height: 1.7;
    overflow-x: auto;
  }
  .prc-str { color: var(--prc-cyan); }
  .prc-fn { color: var(--prc-violet); }
  .prc-blank {
    display: inline-block;
    min-width: 74px;
    border-radius: 6px;
    background: rgba(91, 225, 255, .22);
    outline: 2px dashed rgba(91, 225, 255, .65);
    outline-offset: -2px;
    vertical-align: baseline;
  }
  .prc-choices {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
    padding-top: 16px;
  }
  .prc-chip {
    padding: 8px 14px;
    border-radius: 999px;
    border: 1.5px solid var(--prc-border);
    background: var(--prc-surface);
    font-family: 'Space Mono', monospace;
    font-size: .78rem;
    color: var(--prc-muted);
  }
  .prc-chip.is-picked {
    border-color: rgba(115, 182, 255, .65);
    background: linear-gradient(135deg, rgba(115,182,255,.16), rgba(155,107,255,.13));
    color: var(--prc-ink);
    font-weight: 700;
  }
  .prc-checkrow { display: grid; place-items: center; padding-top: 16px; }
  .prc-checkbtn {
    padding: 11px 26px;
    border-radius: 999px;
    background: var(--prc-ink);
    color: #fff;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 600;
    font-size: .85rem;
  }

  /* --- Mock 2: Studio practice flashcard --- */
  .prc-mockhead { display: flex; justify-content: flex-start; }
  .prc-studiochip {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 12px;
    border-radius: 999px;
    background: linear-gradient(135deg, rgba(115,182,255,.14), rgba(155,107,255,.14));
    color: #3d6ea8;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 600;
    font-size: .72rem;
  }
  .prc-studiochip svg { width: 14px; height: 14px; color: var(--prc-violet); }
  .prc-flash-q {
    margin: 18px 0 0;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700;
    font-size: 1.25rem;
    letter-spacing: -0.01em;
  }
  .prc-flash-hint {
    margin: 8px 0 0;
    color: var(--prc-muted);
    font-size: .92rem;
  }
  .prc-flashfoot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 20px;
  }
  .prc-flashcount { color: var(--prc-faint); font-size: .78rem; }
  .prc-flipbtn {
    padding: 9px 18px;
    border-radius: 999px;
    border: 1.5px solid var(--prc-border);
    background: var(--prc-surface);
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 600;
    font-size: .8rem;
    color: var(--prc-ink);
  }

  /* --- Mock 3: first lesson node --- */
  .prc-mission-tag {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 999px;
    background: rgba(91, 225, 255, .14);
    border: 1px solid rgba(91, 225, 255, .45);
    color: #1e7f99;
    font-family: 'Space Mono', monospace;
    font-weight: 700;
    font-size: .66rem;
    letter-spacing: .16em;
  }
  .prc-lesson-title {
    margin: 14px 0 0;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700;
    font-size: 1.25rem;
    letter-spacing: -0.01em;
  }
  .prc-lesson-sub { margin: 8px 0 0; color: var(--prc-muted); font-size: .92rem; }
  .prc-lessonfoot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    padding-top: 20px;
  }
  .prc-xp { color: var(--prc-faint); font-size: .8rem; }
  .prc-startbtn {
    padding: 11px 22px;
    border-radius: 999px;
    background: var(--prc-ink);
    color: #fff;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 600;
    font-size: .85rem;
  }

  /* ---------- Continue ---------- */
  .prc-foot { padding-top: 6px; }
  .prc-retry-hint {
    margin: 0 0 10px;
    text-align: center;
    font-size: .84rem;
    color: #b0483f;
  }
  .prc-spin {
    display: inline-block;
    width: 16px;
    height: 16px;
    margin-right: 9px;
    vertical-align: -3px;
    border-radius: 50%;
    border: 2.5px solid rgba(255, 255, 255, .35);
    border-top-color: #ffffff;
    animation: prc-rotate .7s linear infinite;
  }
  @keyframes prc-rotate { to { transform: rotate(360deg); } }
  .prc-continue.is-busy {
    cursor: wait;
    opacity: .85;
  }
  .prc-continue.is-busy:hover { transform: none; }
  .prc-continue {
    width: 100%;
    padding: 16px 24px;
    border: none;
    border-radius: 999px;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 600;
    font-size: 1rem;
    letter-spacing: .01em;
    cursor: pointer;
    background: var(--prc-ink);
    color: #ffffff;
    box-shadow: 0 14px 30px rgba(14, 18, 48, .22);
    transition: background-color .2s ease, color .2s ease,
                transform .15s ease, box-shadow .2s ease;
  }
  .prc-continue:hover { transform: translateY(-1px); }
  .prc-continue:active { transform: translateY(0); }
  .prc-continue:focus-visible {
    outline: 3px solid var(--prc-cyan);
    outline-offset: 2px;
  }

  @media (prefers-reduced-motion: reduce) {
    .prc-mock, .prc-slide.is-active, .prc-continue, .prc-seg, .prc-spin { animation: none !important; transition: none !important; }
    .prc-continue:hover { transform: none; }
  }
</style>

<script>
  /* Slide-through carousel. Continue advances, chevron steps back.
     With `planEndpoint` set, a POST fires as soon as the carousel starts
     (listen for `plan-reveal:start`, or it self-starts when rendered visible).
     The slides never block on it; if the student reaches the last button
     before the response lands, it flips to a disabled "Finishing up..." state,
     then redirects once BOTH are done. Failures surface a retry state —
     no silent failures. Without `planEndpoint` everything stays static and
     finishing just dispatches `plan-reveal:complete`. */
  (function () {
    document.querySelectorAll('.prc').forEach(function (root) {
      if (root.dataset.prcBound) return;
      root.dataset.prcBound = '1';

      var slides = Array.prototype.slice.call(root.querySelectorAll('[data-prc-slide]'));
      var segs = Array.prototype.slice.call(root.querySelectorAll('.prc-seg'));
      var baseFilled = Math.max(0, segs.length - slides.length); // segments earned before this carousel
      var bar = root.querySelector('.prc-progress');
      var live = root.querySelector('[data-prc-live]');
      var next = root.querySelector('[data-prc-continue]');
      var back = root.querySelector('[data-prc-back]');
      var hint = root.querySelector('[data-prc-retry-hint]');
      var planEndpoint = root.getAttribute('data-plan-endpoint');
      var destinationUrl = root.getAttribute('data-destination-url');
      var index = 0;
      var slidesDone = false;
      var planState = 'idle'; // idle | pending | ready | failed

      function lastLabel() { return index === slides.length - 1 ? "Let's go" : 'Continue'; }

      function refreshButton() {
        next.classList.remove('is-busy');
        hint.hidden = true;
        next.disabled = false;

        /* Busy only after the student has clicked through every slide —
           never pre-lock the button while they're still reading. */
        if (planState === 'pending' && slidesDone) {
          next.disabled = true;
          next.classList.add('is-busy');
          next.innerHTML = '<span class="prc-spin" aria-hidden="true"></span>Finishing up...';
          return;
        }
        if (planState === 'failed' && index === slides.length - 1 && slidesDone) {
          hint.hidden = false;
          next.textContent = 'Try again';
          return;
        }
        next.textContent = lastLabel();
      }

      function show(n) {
        index = Math.max(0, Math.min(slides.length - 1, n));
        slides.forEach(function (slide, i) {
          var active = i === index;
          slide.hidden = !active;
          slide.classList.toggle('is-active', active);
        });
        segs.forEach(function (seg, i) {
          seg.classList.toggle('is-filled', i <= baseFilled + index);
        });
        if (bar) bar.setAttribute('aria-valuenow', String(baseFilled + index + 1));
        if (live) live.textContent = 'Slide ' + (index + 1) + ' of ' + slides.length;
        refreshButton();
      }

      function finish() {
        root.dispatchEvent(new CustomEvent('plan-reveal:complete', { bubbles: true }));
        if (destinationUrl) {
          window.location.href = destinationUrl;
        }
      }

      function startPlanRequest() {
        if (!planEndpoint || planState === 'pending' || planState === 'ready') return;
        planState = 'pending';
        refreshButton();

        var headers = { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' };
        var csrf = document.querySelector('meta[name="csrf-token"]');
        if (csrf) headers['X-CSRF-TOKEN'] = csrf.content;

        fetch(planEndpoint, { method: 'POST', headers: headers, credentials: 'same-origin' })
          .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.status === 204 ? null : res.json().catch(function () { return null; });
          })
          .then(function () {
            planState = 'ready';
            /* Redirect only once the student has also clicked through every slide. */
            if (slidesDone) finish();
            else refreshButton();
          })
          .catch(function (err) {
            planState = 'failed';
            console.error('[plan-reveal] plan generation failed:', err);
            refreshButton();
          });
      }

      next.addEventListener('click', function () {
        if (next.disabled) return;

        if (index < slides.length - 1) {
          show(index + 1);
          return;
        }

        /* Last slide. */
        slidesDone = true;
        if (planState === 'ready') { finish(); return; }
        if (planState === 'failed') { startPlanRequest(); return; } // retry → goes busy
        if (planState === 'pending') { refreshButton(); return; }   // keep waiting
        /* No endpoint wired — legacy static behaviour. */
        finish();
      });

      if (back) {
        back.addEventListener('click', function (e) {
          if (index > 0) {
            e.preventDefault();
            show(index - 1);
          }
          /* On the first slide, fall through to `backHref`. */
        });
      }

      var sound = root.querySelector('[data-prc-sound]');
      if (sound) {
        sound.addEventListener('click', function () {
          var muted = sound.classList.toggle('is-muted');
          sound.setAttribute('aria-pressed', muted ? 'true' : 'false');
        });
      }

      if (planEndpoint) {
        /* Started by the host page via `plan-reveal:start`; self-starts if
           already visible at bind time. */
        root.addEventListener('plan-reveal:start', startPlanRequest);
        if (root.offsetParent !== null) startPlanRequest();
      }

      show(0);
    });
  })();
</script>
