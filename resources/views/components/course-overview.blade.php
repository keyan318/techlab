{{-- Course overview — Programming planet's learning-plan screen.
     Horizontal node path: first node unlocked and prominent ("START HERE"),
     the rest locked, connected by a line, with a chevron hinting more nodes
     scroll into view. Purely static — no routing or click behaviour yet.
     Reuses the onboarding/plan-reveal visual language (light theme, cosmic
     accents, Space Grotesk headings, full-width pill CTA). --}}
@props([
  'eyebrow' => 'Your learning plan',
  'courseTitle' => 'Programming & CS',
  'courseSubtitle' => 'Speak fluent computer',
  'nodes' => null,
])

@php
  $iconSparkWhite = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 7 4 12 9 17"/><polyline points="15 7 20 12 15 17"/></svg>';
  $iconLock = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5.5" y="10.5" width="13" height="9" rx="2.5"/><path d="M8.5 10.5V8a3.5 3.5 0 0 1 7 0v2.5"/></svg>';
  $iconVar = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4C5 8.5 5 15.5 7 20"/><path d="M17 4c2 4.5 2 11.5 0 16"/><path d="M9.5 9.5l5 5M14.5 9.5l-5 5"/></svg>';
  $iconFn = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19V7a3 3 0 0 1 3-3h1"/><path d="M7 11h6"/><path d="M14.5 8l-2 8.5A2.4 2.4 0 0 0 15 19c1.4 0 2.3-.7 3-1.6"/></svg>';
  $iconFlow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="6" height="5" rx="1.2"/><rect x="15" y="15" width="6" height="5" rx="1.2"/><path d="M9 6.5h6.5A2.5 2.5 0 0 1 18 9v3.5"/><path d="M18 15v-.5"/><circle cx="18" cy="12.5" r=".4"/><path d="M6 9v6.5A2.5 2.5 0 0 0 8.5 18H12"/></svg>';
  $iconRocket = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15c5-4 6.5-8 6.5-12-4 0-8 1.5-12 6.5L4 12l4 1 4 2Z"/><circle cx="13" cy="8.5" r="1.4"/><path d="M6 16c-1.5.5-2.5 2-3 4 2-.5 3.5-1.5 4-3"/></svg>';

  // Sample curriculum — swap via the `nodes` prop when real lessons exist.
  // Each node may carry a `url`; "Start learning" heads to the first
  // unlocked node's url (placeholder lesson stub for now).
  $sampleNodes = [
    ['label' => 'Thinking in Code',            'locked' => false, 'icon' => $iconSparkWhite,
     'url' => route('student.planet.story', ['slug' => 'programming', 'lesson' => 'thinking-in-code'])],
    ['label' => 'Programming with Variables',  'locked' => true,  'icon' => $iconVar],
    ['label' => 'Programming with Functions',  'locked' => true,  'icon' => $iconFn],
    ['label' => 'Algorithmic Thinking',        'locked' => true,  'icon' => $iconFlow],
    ['label' => 'Build Your First Project',    'locked' => true,  'icon' => $iconRocket],
  ];
  $nodes ??= $sampleNodes;

  $startHref = '#';
  foreach ($nodes as $node) {
    if (empty($node['locked']) && !empty($node['url'])) {
      $startHref = $node['url'];
      break;
    }
  }
@endphp

<section class="co">
  <div class="co-col">
  <main class="co-body">
    <p class="co-eyebrow">{{ strtoupper($eyebrow) }}</p>

    <div class="co-head">
      <span class="co-course-icon" aria-hidden="true">
        {{-- Mini Programming City planet, from the Mission Control card art. --}}
        <svg viewBox="0 0 48 48" fill="none">
          <defs>
            <radialGradient id="coPlanet" cx="35%" cy="30%" r="80%">
              <stop offset="0%" stop-color="#cfe6ff"/>
              <stop offset="55%" stop-color="#73b6ff"/>
              <stop offset="100%" stop-color="#235bbf"/>
            </radialGradient>
          </defs>
          <circle cx="24" cy="24" r="15" fill="url(#coPlanet)"/>
          <g opacity=".55">
            <path d="M12 23q6-4 12-1t11 1" stroke="#eaf4ff" stroke-width="2.2" fill="none" stroke-linecap="round"/>
            <rect x="18" y="28" width="6" height="3.4" rx="1" fill="#0b2c63"/>
            <rect x="27" y="30" width="4" height="2.6" rx=".8" fill="#0b2c63"/>
          </g>
          <ellipse cx="24" cy="25" rx="21" ry="6.5" fill="none"
                   stroke="#9b6bff" stroke-width="1.6" opacity=".75"
                   transform="rotate(-16 24 24)"/>
        </svg>
      </span>
      <div class="co-title-block">
        <h1 class="co-title">{{ $courseTitle }}</h1>
        <p class="co-subtitle">{{ $courseSubtitle }}</p>
      </div>
    </div>

    <div class="co-path" role="list" aria-label="{{ $courseTitle }} lessons">
      <div class="co-track">
        @foreach ($nodes as $node)
          <div class="co-node {{ empty($node['locked']) ? 'is-current' : 'is-locked' }}" role="listitem">
            @if (empty($node['locked']))
              <span class="co-here">Start here</span>
            @endif
            <button type="button" class="co-node-btn" aria-disabled="{{ !empty($node['locked']) ? 'true' : 'false' }}"
                    aria-label="{{ $node['label'] }}{{ !empty($node['locked']) ? ' (locked)' : '' }}">
              <span class="co-node-icon">{!! $node['icon'] ?? $iconLock !!}</span>
            </button>
            <span class="co-node-label">{{ $node['label'] }}</span>
          </div>
        @endforeach
      </div>
      <span class="co-fade" aria-hidden="true"></span>
      <span class="co-more" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 5 16 12 9 19"/></svg>
      </span>
    </div>
  </main>

  <footer class="co-foot">
    <span class="co-astro" aria-hidden="true">
      @include('student.chat.partials.astro-mascot')
    </span>
    <a class="co-start" href="{{ $startHref }}">Start learning</a>
  </footer>
  </div>
</section>

<style>
  .co {
    /* Scoped tokens — mirror the onboarding-question / plan-reveal family. */
    --co-ink: #0e1230;
    --co-muted: #6a7190;
    --co-faint: #a9b0ca;
    --co-surface: #f2f4fb;
    --co-border: #e4e8f5;
    --co-blue: #73b6ff;
    --co-violet: #9b6bff;
    --co-cyan: #5be1ff;

    /* Node geometry — keep in sync: connectors + chevron read these. */
    --co-node-w: 96px;
    --co-circle: 76px;
    --co-gap: 30px;
    --co-trackpad-top: 34px;

    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    width: 100%;
    min-height: 100vh;
    font-family: 'Inter', system-ui, sans-serif;
    color: var(--co-ink);
    background: #ffffff;
    overflow-x: clip;
  }
  .co-col {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    width: 100%;
    max-width: 620px;
    margin: 0 auto;
    padding: 0 20px calc(16px + env(safe-area-inset-bottom));
    box-sizing: border-box;
  }
  .co *, .co *::before, .co *::after { box-sizing: border-box; }

  /* ---------- Copy ---------- */
  .co-body {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 30px;
    padding-bottom: 24px;
  }
  .co-eyebrow {
    margin: 0;
    font-family: 'Space Mono', monospace;
    font-weight: 700;
    font-size: .72rem;
    letter-spacing: .22em;
    color: var(--co-blue);
    text-align: center;
  }
  .co-head {
    display: flex;
    align-items: center;
    gap: 16px;
  }
  .co-course-icon {
    width: 58px;
    height: 58px;
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    padding: 7px;
    border-radius: 18px;
    background: linear-gradient(135deg, #eaf3ff, #f2ecff 55%, #e9fbff);
    border: 1px solid rgba(115, 182, 255, .38);
  }
  .co-course-icon svg { width: 100%; height: 100%; }
  .co-title {
    margin: 0;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700;
    font-size: clamp(1.45rem, 4.4vw, 1.85rem);
    letter-spacing: -0.02em;
    line-height: 1.15;
  }
  .co-subtitle {
    margin: 5px 0 0;
    color: var(--co-muted);
    font-size: .95rem;
  }

  /* ---------- Horizontal node path ---------- */
  .co-path { position: relative; }
  .co-track {
    display: flex;
    align-items: flex-start;
    gap: var(--co-gap);
    padding: var(--co-trackpad-top) 4px 4px;
    margin: 0 -4px;                 /* let hover rings breathe past the gutter */
    overflow-x: auto;
    scrollbar-width: none;
  }
  .co-track::-webkit-scrollbar { display: none; }

  .co-node {
    position: relative;
    flex: 0 0 auto;
    width: var(--co-node-w);
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  /* Connector segment bridging circle-edge to circle-edge across the gap
     (the circles are inset (node-w − circle)/2 inside each column). */
  .co-node:not(:last-child)::after {
    content: '';
    position: absolute;
    top: calc(var(--co-trackpad-top) + var(--co-circle) / 2 - 1.5px);
    left: calc(50% + var(--co-circle) / 2);
    width: calc(var(--co-gap) + var(--co-node-w) - var(--co-circle));
    height: 3px;
    border-radius: 2px;
    background: #e2e7f3;
  }
  .co-node.is-current:not(:last-child)::after {
    background: linear-gradient(90deg, rgba(115, 182, 255, .85), #e2e7f3);
  }

  .co-here {
    position: absolute;
    top: calc(var(--co-trackpad-top) - 24px);
    left: 50%;
    transform: translateX(-50%);
    padding: 5px 11px;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--co-blue), var(--co-violet));
    color: #ffffff;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700;
    font-size: .62rem;
    letter-spacing: .14em;
    text-transform: uppercase;
    white-space: nowrap;
    box-shadow: 0 8px 18px rgba(115, 182, 255, .40);
  }

  .co-node-btn {
    width: var(--co-circle);
    height: var(--co-circle);
    display: grid;
    place-items: center;
    padding: 0;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-family: inherit;
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .co-node-btn:focus-visible {
    outline: 3px solid var(--co-cyan);
    outline-offset: 3px;
  }
  .co-node.is-current .co-node-btn {
    background:
      radial-gradient(120% 120% at 30% 22%, rgba(255, 255, 255, .5) 0%, rgba(255, 255, 255, 0) 42%),
      linear-gradient(135deg, var(--co-blue), var(--co-violet) 58%, var(--co-cyan));
    color: #ffffff;
    box-shadow: 0 18px 38px rgba(115, 182, 255, .45),
                inset 0 -3px 0 rgba(14, 18, 48, .18);
    transform: scale(1.05);
  }
  .co-node.is-current .co-node-btn:hover { transform: scale(1.09); }
  .co-node.is-locked .co-node-btn {
    background: var(--co-surface);
    color: var(--co-faint);
    box-shadow: inset 0 -3px 0 #e6eaf5;
    cursor: default;
  }

  .co-node-icon { display: grid; place-items: center; width: 32px; height: 32px; }
  .co-node-icon svg { width: 100%; height: 100%; }
  .co-node.is-locked .co-node-icon svg { width: 26px; height: 26px; }

  .co-node-label {
    margin-top: 10px;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 600;
    font-size: .78rem;
    line-height: 1.3;
    text-align: center;
    color: var(--co-ink);
  }
  .co-node.is-locked .co-node-label { color: var(--co-faint); }

  /* Scroll affordance: soft fade + chevron on the right edge. */
  .co-fade {
    position: absolute;
    top: calc(var(--co-trackpad-top) - 26px);
    bottom: -4px;
    right: -4px;
    width: 64px;
    pointer-events: none;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0), #ffffff 78%);
  }
  .co-more {
    position: absolute;
    top: calc(var(--co-trackpad-top) + var(--co-circle) / 2);
    right: 6px;
    transform: translateY(-50%);
    width: 34px;
    height: 34px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: #ffffff;
    border: 1.5px solid var(--co-border);
    color: var(--co-muted);
    box-shadow: 0 6px 16px rgba(14, 18, 48, .10);
    pointer-events: none;
  }
  .co-more svg { width: 16px; height: 16px; }

  /* ---------- Footer CTA ---------- */
  .co-foot { padding-top: 6px; }
  .co-astro {
    display: grid;
    place-items: center;
    width: 64px;
    height: 64px;
    margin: 0 auto 10px;
    animation: co-bob 4s ease-in-out infinite;
  }
  @keyframes co-bob {
    0%, 100% { transform: translateY(0); }
    50%      { transform: translateY(-5px); }
  }
  .co-start {
    display: block;                 /* anchor so Start learning can navigate */
    width: 100%;
    padding: 16px 24px;
    border-radius: 999px;
    text-align: center;
    text-decoration: none;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 600;
    font-size: 1rem;
    letter-spacing: .01em;
    cursor: pointer;
    background: var(--co-ink);
    color: #ffffff;
    box-shadow: 0 14px 30px rgba(14, 18, 48, .22);
    transition: transform .15s ease, box-shadow .2s ease;
  }
  .co-start:hover { transform: translateY(-1px); }
  .co-start:active { transform: translateY(0); }
  .co-start:focus-visible {
    outline: 3px solid var(--co-cyan);
    outline-offset: 2px;
  }

  @media (prefers-reduced-motion: reduce) {
    .co-astro, .co-node-btn, .co-start { animation: none !important; transition: none !important; }
    .co-node.is-current .co-node-btn:hover, .co-start:hover { transform: none; }
  }
</style>
