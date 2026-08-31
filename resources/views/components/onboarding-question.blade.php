{{-- Onboarding question flow — Duolingo-style stepped question screen.
     Reusable: pass `questions` = [['question' => ..., 'options' => [['icon','label'], ...]], ...].
     Called bare (`<x-onboarding-question />`) it renders three hardcoded sample
     questions so the flow can be verified before backend wiring lands.
     Pass `completeUrl` to redirect after the final question; otherwise the
     component emits an `onboarding-question:complete` event and stays put.
     Light theme by design; brand accents come from the cosmic tokens. --}}
@props([
  'questions' => null,
  'question' => null,
  'options' => null,
  'backHref' => '#',
  'mascot' => null,
  'completeUrl' => null,
])

@php
  // Sample content — replace via props once real questions exist.
  // Colorful flat icons (not brand-token strokes) to match the reference mocks.

  $iconBookGrad = '<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <linearGradient id="obqBook" x1="6" y1="8" x2="34" y2="40" gradientUnits="userSpaceOnUse">
        <stop offset="0" stop-color="#8fd9d2"/>
        <stop offset="1" stop-color="#4fa79e"/>
      </linearGradient>
    </defs>
    <path d="M10 8h20a4 4 0 0 1 4 4v26l-4-2-4 2-4-2-4 2-4-2-4 2V12a4 4 0 0 1 4-4Z" fill="url(#obqBook)"/>
    <rect x="10" y="8" width="7" height="30" fill="#000" opacity=".12"/>
    <rect x="21" y="14" width="9" height="4" rx="1" fill="#fff" opacity=".85"/>
  </svg>';

  $iconGrowthGrad = '<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect x="6" y="6" width="34" height="34" rx="3" fill="none" stroke="#d7dbe4" stroke-width="1.5"/>
    <line x1="6" y1="16" x2="40" y2="16" stroke="#e3e6ee" stroke-width="1.5"/>
    <line x1="6" y1="26" x2="40" y2="26" stroke="#e3e6ee" stroke-width="1.5"/>
    <line x1="16" y1="6" x2="16" y2="40" stroke="#e3e6ee" stroke-width="1.5"/>
    <line x1="27" y1="6" x2="27" y2="40" stroke="#e3e6ee" stroke-width="1.5"/>
    <path d="M9 32 L18 22 L24 27 L39 9" stroke="#3ecf6e" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
    <path d="M30 9 H39 V18" stroke="#3ecf6e" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
  </svg>';

  $iconTargetGrad = '<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <radialGradient id="obqTarget" cx="0.5" cy="0.5" r="0.5">
        <stop offset="0" stop-color="#f6b8ea"/>
        <stop offset="1" stop-color="#c07de8"/>
      </radialGradient>
    </defs>
    <circle cx="24" cy="24" r="17" fill="url(#obqTarget)"/>
    <circle cx="24" cy="24" r="11.5" fill="#fff" opacity=".55"/>
    <circle cx="24" cy="24" r="6.5" fill="#a259d9"/>
    <path d="M8 6 L24 24 L36 12 L30 10 L28 4 L22 12 L14 8Z" fill="#7a4bc9"/>
  </svg>';

  $iconRocketGrad = '<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <linearGradient id="obqRocketBody" x1="14" y1="4" x2="34" y2="34" gradientUnits="userSpaceOnUse">
        <stop offset="0" stop-color="#eef1f6"/>
        <stop offset="1" stop-color="#aab1c2"/>
      </linearGradient>
    </defs>
    <path d="M24 4c6 4 9 12 8 22l-8 8-8-8c-1-10 2-18 8-22Z" fill="url(#obqRocketBody)"/>
    <circle cx="24" cy="18" r="4" fill="#5da9e0"/>
    <path d="M16 24 8 30l2 8 8-6" fill="#e06a5a"/>
    <path d="M32 24l8 6-2 8-8-6" fill="#e06a5a"/>
    <path d="M20 34h8l-2 8-2 2-2-2Z" fill="#f0803f"/>
  </svg>';

  $iconClockFactory = function ($fraction) {
    // fraction: 0..1 fill of the dial, for the 10/20/30/60-min stopwatch variants.
    $angle = 360 * $fraction;
    $rad = deg2rad($angle - 90);
    $x = 24 + 13 * cos($rad);
    $y = 24 + 13 * sin($rad);
    $largeArc = $angle > 180 ? 1 : 0;
    $wedge = $fraction >= 0.999
      ? '<circle cx="24" cy="24" r="13" fill="url(#obqClockFill)"/>'
      : '<path d="M24 24 L24 11 A13 13 0 '.$largeArc.' 1 '.round($x, 2).' '.round($y, 2).' Z" fill="url(#obqClockFill)"/>';
    return '<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="obqClockFill" x1="11" y1="11" x2="37" y2="37" gradientUnits="userSpaceOnUse">
          <stop offset="0" stop-color="#c8a4f5"/>
          <stop offset="1" stop-color="#8a5cf0"/>
        </linearGradient>
        <linearGradient id="obqClockBody" x1="8" y1="8" x2="40" y2="40" gradientUnits="userSpaceOnUse">
          <stop offset="0" stop-color="#f3f4f7"/>
          <stop offset="1" stop-color="#aab0bd"/>
        </linearGradient>
      </defs>
      <rect x="19" y="3" width="10" height="5" rx="1.5" fill="#9aa1af"/>
      <rect x="16" y="1" width="16" height="4" rx="2" fill="#9aa1af"/>
      <circle cx="24" cy="24" r="17.5" fill="url(#obqClockBody)"/>
      <circle cx="24" cy="24" r="13" fill="#fff"/>
      '.$wedge.'
      <circle cx="24" cy="24" r="17.5" fill="none" stroke="#8b93a3" stroke-width="1.4"/>
    </svg>';
  };

  $iconMountainSun = '<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <radialGradient id="obqMSun" cx="0.5" cy="0.5" r="0.5">
        <stop offset="0" stop-color="#ffd166"/>
        <stop offset="1" stop-color="#ff7a8a"/>
      </radialGradient>
    </defs>
    <g stroke="#f78fa7" stroke-width="1.6" stroke-linecap="round">
      <line x1="24" y1="4" x2="24" y2="9"/>
      <line x1="34.5" y1="8" x2="31.5" y2="12"/>
      <line x1="13.5" y1="8" x2="16.5" y2="12"/>
      <line x1="39" y1="18" x2="34" y2="19"/>
      <line x1="9" y1="18" x2="14" y2="19"/>
    </g>
    <circle cx="24" cy="18" r="8.5" fill="url(#obqMSun)"/>
    <path d="M2 40 16 20 24 30 30 22 46 40Z" fill="#c3c8d1"/>
    <path d="M24 30 30 22 46 40H24Z" fill="#a9afbc"/>
  </svg>';

  $iconSunOnly = '<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <radialGradient id="obqSun2" cx="0.5" cy="0.5" r="0.5">
        <stop offset="0" stop-color="#ffd166"/>
        <stop offset="1" stop-color="#ff7a8a"/>
      </radialGradient>
    </defs>
    <g stroke="#f78fa7" stroke-width="1.8" stroke-linecap="round">
      <line x1="24" y1="4" x2="24" y2="10"/>
      <line x1="24" y1="38" x2="24" y2="44"/>
      <line x1="4" y1="24" x2="10" y2="24"/>
      <line x1="38" y1="24" x2="44" y2="24"/>
      <line x1="9" y1="9" x2="13" y2="13"/>
      <line x1="35" y1="35" x2="39" y2="39"/>
      <line x1="35" y1="13" x2="39" y2="9"/>
      <line x1="9" y1="39" x2="13" y2="35"/>
    </g>
    <circle cx="24" cy="24" r="11" fill="url(#obqSun2)"/>
  </svg>';

  $iconMoonStars = '<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <linearGradient id="obqMoon" x1="10" y1="8" x2="34" y2="38" gradientUnits="userSpaceOnUse">
        <stop offset="0" stop-color="#8fc7ea"/>
        <stop offset="1" stop-color="#5a4fc2"/>
      </linearGradient>
    </defs>
    <path d="M30 6a18 18 0 1 0 12 30A15 15 0 0 1 30 6Z" fill="url(#obqMoon)"/>
    <path d="M15 12 l2 5 5 2 -5 2 -2 5 -2 -5 -5 -2 5 -2Z" fill="#dfe6f5"/>
    <path d="M35 30 l1.4 3.4 3.4 1.4 -3.4 1.4 -1.4 3.4 -1.4 -3.4 -3.4 -1.4 3.4 -1.4Z" fill="#dfe6f5"/>
  </svg>';

  $iconSunMoon = '<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <radialGradient id="obqSun3" cx="0.5" cy="0.5" r="0.5">
        <stop offset="0" stop-color="#ffd166"/>
        <stop offset="1" stop-color="#ff7a8a"/>
      </radialGradient>
      <linearGradient id="obqMoon2" x1="4" y1="6" x2="26" y2="30" gradientUnits="userSpaceOnUse">
        <stop offset="0" stop-color="#8fc7ea"/>
        <stop offset="1" stop-color="#5a4fc2"/>
      </linearGradient>
    </defs>
    <g stroke="#f78fa7" stroke-width="1.6" stroke-linecap="round">
      <line x1="30" y1="6" x2="30" y2="11"/>
      <line x1="43" y1="19" x2="38" y2="20"/>
      <line x1="39" y1="9" x2="36" y2="13"/>
    </g>
    <circle cx="30" cy="20" r="9" fill="url(#obqSun3)"/>
    <path d="M20 4a15 15 0 1 0 9 26 12.5 12.5 0 0 1-9-26Z" fill="url(#obqMoon2)"/>
  </svg>';

  $sampleQuestions = [
    ['question' => 'What motivates you to learn?', 'options' => [
      ['icon' => $iconBookGrad,   'label' => 'Excelling in school'],
      ['icon' => $iconGrowthGrad,'label' => 'Professional growth'],
      ['icon' => $iconTargetGrad,'label' => 'Staying sharp'],
      ['icon' => $iconRocketGrad,'label' => 'Helping my child learn'],
    ]],
    ['question' => "What's your daily learning goal?", 'options' => [
      ['icon' => $iconClockFactory(0.17), 'label' => '10 min'],
      ['icon' => $iconClockFactory(0.33), 'label' => '20 min'],
      ['icon' => $iconClockFactory(0.5),  'label' => '30 min'],
      ['icon' => $iconClockFactory(1.0),  'label' => '60 min'],
    ]],
    ['question' => 'How will learning fit into your day?', 'options' => [
      ['icon' => $iconMountainSun, 'label' => 'Morning routine'],
      ['icon' => $iconSunOnly,     'label' => 'Afternoon break'],
      ['icon' => $iconMoonStars,   'label' => 'Nightly ritual'],
      ['icon' => $iconSunMoon,     'label' => 'Another time'],
    ]],
  ];

  // Accept a single question/options pair as a one-step flow.
  if ($questions === null && $question !== null) {
    $questions = [['question' => $question, 'options' => $options ?? []]];
  }
  $questions ??= $sampleQuestions;
  $totalSteps = max(1, count($questions));
@endphp

<section class="obq" data-complete-url="{{ $completeUrl }}">
  <div class="obq-col">
  <header class="obq-top">
    <a class="obq-back" data-obq-back href="{{ $backHref }}" aria-label="Go back">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 5 8 12 15 19"/></svg>
    </a>
    <div class="obq-progress" role="progressbar" aria-label="Onboarding progress"
         aria-valuemin="1" aria-valuemax="{{ $totalSteps }}" aria-valuenow="1">
      @for ($i = 1; $i <= $totalSteps; $i++)
        <span class="obq-seg {{ $i === 1 ? 'is-filled' : '' }}"></span>
      @endfor
    </div>
    <button type="button" class="obq-sound" data-obq-sound aria-label="Toggle sound" aria-pressed="false">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 9.5v5h3l4.5 3.8V5.7L7 9.5H4Z"/>
        <path class="obq-wave" d="M15.5 9.2a4 4 0 0 1 0 5.6"/>
        <path class="obq-wave" d="M17.8 7a7.2 7.2 0 0 1 0 10"/>
        <line class="obq-slash" x1="15" y1="9.5" x2="20" y2="14.5"/>
      </svg>
    </button>
  </header>

  <main class="obq-body">
    <p class="obq-sr" data-obq-live role="status" aria-live="polite">Question 1 of {{ $totalSteps }}</p>

    @foreach ($questions as $qIndex => $step)
      <fieldset class="obq-step {{ $qIndex === 0 ? 'is-active' : '' }}" data-obq-step
                @unless($qIndex === 0) hidden @endunless>
        <legend class="obq-sr">Question {{ $qIndex + 1 }}</legend>

        <div class="obq-head">
          <div class="obq-mascot" aria-hidden="true">
            @if ($mascot)
              {!! $mascot !!}
            @else
              @include('student.chat.partials.astro-mascot')
            @endif
          </div>
          <h2 class="obq-question">{{ $step['question'] }}</h2>
        </div>

        <div class="obq-options" role="radiogroup" aria-label="{{ $step['question'] }}">
          @foreach ($step['options'] as $option)
            <button type="button" class="obq-card" role="radio" aria-checked="false"
                    data-obq-option tabindex="0">
              <span class="obq-card-icon">{!! $option['icon'] !!}</span>
              <span class="obq-card-label">{{ $option['label'] }}</span>
            </button>
          @endforeach
        </div>
      </fieldset>
    @endforeach
  </main>

  <footer class="obq-foot">
    <button type="button" class="obq-continue" data-obq-continue disabled>Continue</button>
  </footer>
  </div>
</section>

<style>
  .obq {
    /* Scoped tokens — light screen, cosmic accents. */
    --obq-ink: #0e1230;
    --obq-muted: #6a7190;
    --obq-faint: #a9b0ca;
    --obq-surface: #f2f4fb;
    --obq-border: #e4e8f5;
    --obq-blue: #73b6ff;
    --obq-violet: #9b6bff;
    --obq-cyan: #5be1ff;

    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    width: 100%;
    min-height: 100vh;          /* full-bleed white layer, edge to edge */
    font-family: 'Inter', system-ui, sans-serif;
    color: var(--obq-ink);
    background: #ffffff;
  }
  /* Centered content column inside the full-bleed section. */
  .obq-col {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    width: 100%;
    max-width: 620px;
    margin: 0 auto;
    padding: 0 20px calc(16px + env(safe-area-inset-bottom));
    box-sizing: border-box;
  }
  .obq *, .obq *::before, .obq *::after { box-sizing: border-box; }

  /* ---------- Top bar ---------- */
  .obq-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 18px 0 26px;
  }
  .obq-back, .obq-sound {
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
    color: var(--obq-ink);
    cursor: pointer;
    transition: background-color .15s ease;
  }
  .obq-back:hover, .obq-sound:hover { background: var(--obq-surface); }
  .obq-back svg, .obq-sound svg { width: 22px; height: 22px; }

  .obq-progress {
    display: flex;
    flex: 1 1 auto;
    gap: 6px;
  }
  .obq-seg {
    height: 9px;
    flex: 1 1 0;
    border-radius: 999px;
    background: #e9edf7;               /* unfilled: light gray */
    transition: background-color .25s ease;
  }
  .obq-seg.is-filled { background: var(--obq-blue); }  /* filled: solid blue */

  .obq-sound .obq-slash { opacity: 0; transition: opacity .15s ease; }
  .obq-sound.is-muted .obq-wave { opacity: 0; }
  .obq-sound.is-muted .obq-slash { opacity: 1; }

  /* ---------- Steps ---------- */
  .obq-body {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 34px;
    padding-bottom: 28px;
  }
  .obq-step {
    display: flex;
    flex-direction: column;
    gap: 34px;
    border: none;
    margin: 0;
    padding: 0;
    min-width: 0;
  }
  .obq-step[hidden] { display: none !important; }
  .obq-step.is-active { animation: obq-fade .28s ease both; }
  @keyframes obq-fade {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .obq-sr {
    position: absolute;
    width: 1px; height: 1px;
    margin: -1px; padding: 0;
    overflow: hidden;
    clip: rect(0 0 0 0);
    white-space: nowrap;
    border: 0;
  }

  /* ---------- Question ---------- */
  .obq-head {
    display: flex;
    align-items: center;
    gap: 16px;
  }
  .obq-mascot {
    width: 68px;
    height: 68px;
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    padding: 10px;
    border-radius: 20px;
    background: linear-gradient(135deg, #eaf3ff, #f2ecff 55%, #e9fbff);
    border: 1px solid rgba(115, 182, 255, .38);
  }
  .obq-question {
    margin: 0;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700;
    font-size: clamp(1.2rem, 3.4vw, 1.5rem);
    line-height: 1.25;
    letter-spacing: -0.01em;
  }

  /* ---------- Option cards ---------- */
  .obq-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
  }
  @media (min-width: 640px) {
    .obq-options { grid-template-columns: repeat(4, 1fr); }
  }
  .obq-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    min-height: 132px;
    padding: 18px 10px 16px;
    border: 1.5px solid var(--obq-border);
    border-radius: 18px;
    background: var(--obq-surface);
    color: var(--obq-ink);
    cursor: pointer;
    font-family: inherit;
    transition: transform .15s ease, border-color .15s ease,
                box-shadow .15s ease, background .15s ease;
  }
  .obq-card:hover { transform: translateY(-2px); }
  .obq-card:focus-visible {
    outline: 3px solid var(--obq-cyan);
    outline-offset: 2px;
  }
  .obq-card-icon {
    display: grid;
    place-items: center;
    width: 48px;
    height: 48px;
    transition: transform .15s ease;
  }
  .obq-card-icon svg { width: 100%; height: 100%; }
  .obq-card-label {
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 600;
    font-size: .95rem;
    text-align: center;
  }

  /* Selected: light gradient tint from our tokens. */
  .obq-card.is-selected {
    border-color: rgba(115, 182, 255, .65);
    background: linear-gradient(135deg,
                rgba(115, 182, 255, .16),
                rgba(155, 107, 255, .13) 52%,
                rgba(91, 225, 255, .16));
    box-shadow: 0 10px 26px rgba(115, 182, 255, .22);
  }
  .obq-card.is-selected .obq-card-icon { transform: scale(1.06); }

  /* ---------- Continue ---------- */
  .obq-foot { padding-top: 6px; }
  .obq-continue {
    width: 100%;
    padding: 16px 24px;
    border: none;
    border-radius: 999px;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 600;
    font-size: 1rem;
    letter-spacing: .01em;
    cursor: not-allowed;
    background: #eef1f8;
    color: var(--obq-faint);
    transition: background-color .2s ease, color .2s ease,
                transform .15s ease, box-shadow .2s ease;
  }
  .obq-continue:not(:disabled) {
    cursor: pointer;
    background: var(--obq-ink);
    color: #ffffff;
    box-shadow: 0 14px 30px rgba(14, 18, 48, .22);
  }
  .obq-continue:not(:disabled):hover { transform: translateY(-1px); }
  .obq-continue:not(:disabled):active { transform: translateY(0); }
  .obq-continue:focus-visible {
    outline: 3px solid var(--obq-cyan);
    outline-offset: 2px;
  }

  @media (prefers-reduced-motion: reduce) {
    .obq-card, .obq-continue, .obq-seg, .obq-step.is-active { transition: none !important; animation: none !important; }
    .obq-card:hover, .obq-continue:not(:disabled):hover { transform: none; }
  }
</style>

<script>
  /* Stepped selection flow: pick an answer, Continue advances, chevron goes
     back. Final step fires `onboarding-question:complete` (and redirects if
     the `completeUrl` prop was passed) — backend wiring comes later. */
  (function () {
    document.querySelectorAll('.obq').forEach(function (root) {
      if (root.dataset.obqBound) return;
      root.dataset.obqBound = '1';

      var steps = Array.prototype.slice.call(root.querySelectorAll('[data-obq-step]'));
      var segs = Array.prototype.slice.call(root.querySelectorAll('.obq-seg'));
      var bar = root.querySelector('.obq-progress');
      var live = root.querySelector('[data-obq-live]');
      var next = root.querySelector('[data-obq-continue]');
      var back = root.querySelector('[data-obq-back]');
      var index = 0;

      function show(n) {
        index = Math.max(0, Math.min(steps.length - 1, n));
        steps.forEach(function (step, i) {
          var active = i === index;
          step.hidden = !active;
          step.classList.toggle('is-active', active);
        });
        segs.forEach(function (seg, i) {
          seg.classList.toggle('is-filled', i <= index);
        });
        if (bar) bar.setAttribute('aria-valuenow', String(index + 1));
        if (live) live.textContent = 'Question ' + (index + 1) + ' of ' + steps.length;
        next.disabled = true;
        next.textContent = index === steps.length - 1 ? 'Finish' : 'Continue';
      }

      steps.forEach(function (step) {
        var cards = step.querySelectorAll('[data-obq-option]');
        cards.forEach(function (card) {
          card.addEventListener('click', function () {
            cards.forEach(function (other) {
              var on = other === card;
              other.classList.toggle('is-selected', on);
              other.setAttribute('aria-checked', on ? 'true' : 'false');
            });
            next.disabled = false;
          });
          card.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              card.click();
            }
          });
        });
      });

      next.addEventListener('click', function () {
        if (next.disabled) return;
        if (index < steps.length - 1) {
          show(index + 1);
          return;
        }
        /* Last step finished — hand off to integration later. */
        root.dispatchEvent(new CustomEvent('onboarding-question:complete', {
          bubbles: true,
          detail: { answers: collectAnswers() }
        }));
        var url = root.getAttribute('data-complete-url');
        if (url) {
          window.location.href = url;
        } else {
          console.info('[onboarding-question] complete — pass `completeUrl` to navigate.');
        }
      });

      function collectAnswers() {
        return steps.map(function (step) {
          var sel = step.querySelector('.obq-card.is-selected .obq-card-label');
          return sel ? sel.textContent.trim() : null;
        });
      }

      if (back) {
        back.addEventListener('click', function (e) {
          if (index > 0) {
            e.preventDefault();
            show(index - 1);
          }
          /* On the first step, fall through to `backHref`. */
        });
      }

      var sound = root.querySelector('[data-obq-sound]');
      if (sound) {
        sound.addEventListener('click', function () {
          var muted = sound.classList.toggle('is-muted');
          sound.setAttribute('aria-pressed', muted ? 'true' : 'false');
        });
      }
    });
  })();
</script>