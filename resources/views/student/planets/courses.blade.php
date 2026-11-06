<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $planet['title'] }} · Courses · TechLab</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&family=Pixelify+Sans:wght@500;600;700&display=swap" rel="stylesheet" />

    <meta name="theme-color" content="#06061a">
  <link rel="stylesheet" href="{{ asset('css/theme.css') }}?v={{ filemtime(public_path('css/theme.css')) }}">
  <script src="{{ asset('js/theme.js') }}?v={{ filemtime(public_path('js/theme.js')) }}"></script>
  <script defer src="{{ asset('js/sfx.js') }}?v={{ filemtime(public_path('js/sfx.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: {
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
        fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'], display: ['Space Grotesk', 'sans-serif'], mono: ['Space Mono', 'monospace'] },
      } },
    };
  </script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    [x-cloak] { display: none !important; }
    body { background: #06061a; -webkit-font-smoothing: antialiased; }
    .space { position: fixed; inset: 0; z-index: -1; background: radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%), linear-gradient(160deg, #0a0826 0%, #120a33 45%, #1e1259 100%); }
  </style>
@include('components.course-card-styles')
<style>
  /* Season 2's "finish Season 1 first" tag: gold while it's still to do, green once it's done. */
  .prereq-tag { display: inline-flex; align-items: center; gap: 6px; margin-top: 10px; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; color: #ffe08a; background: rgba(245,192,74,.1); border: 1px solid rgba(245,192,74,.3); }
  .prereq-tag svg { flex: none; }
  .prereq-tag.is-done { color: #8ff0c0; background: rgba(60,210,140,.1); border-color: rgba(60,210,140,.32); }
  .lock-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 11px; border-radius: 99px; font-size: 12px; font-weight: 600; color: #9fb4ff; background: rgba(115,142,255,.12); border: 1px solid rgba(115,142,255,.3); }
  .code-form { display: flex; gap: 8px; margin-top: 10px; }
  .code-form input { flex: 1; min-width: 0; background: rgba(255,255,255,.06); border: 1px solid rgba(150,170,255,.25); border-radius: 10px; padding: 9px 11px; color: inherit; font-family: 'Space Mono', monospace; letter-spacing: .1em; text-transform: uppercase; }
  .code-form input::placeholder { color: rgba(190,198,235,.5); text-transform: none; letter-spacing: normal; font-family: inherit; }
  .code-form button { flex: none; padding: 9px 14px; border-radius: 10px; border: none; background: linear-gradient(100deg, #5be1ff, #73b6ff 55%, #9b6bff); color: #06061a; font-weight: 700; font-size: 13px; cursor: pointer; }
  .code-form button:disabled { opacity: .6; cursor: default; }
  .code-err { color: #ff9db0; font-size: 12.5px; margin-top: 6px; }
  .lock-pill.is-ok { color: #8ff0c0; background: rgba(60,210,140,.12); border-color: rgba(60,210,140,.35); }
  .lock-pill.is-no { color: #ffb3c2; background: rgba(255,99,132,.1); border-color: rgba(255,99,132,.32); }
  .join-note { font-size: 12.5px; color: rgb(190 198 235); margin-top: 8px; }
  .join-btn { display: block; width: 100%; margin-top: 10px; padding: 10px 14px; border-radius: 10px; border: none; background: linear-gradient(100deg, #5be1ff, #73b6ff 55%, #9b6bff); color: #06061a; font-weight: 700; font-size: 13px; cursor: pointer; }
  .join-btn:disabled { opacity: .6; cursor: default; }
  .page-status { margin-top: 16px; padding: 10px 14px; border-radius: 12px; font-size: 14px; color: #cfe0ff; background: rgba(115,142,255,.12); border: 1px solid rgba(115,142,255,.3); }
  /* "You're in!" confirmation — a materializing card, not a plain fade, with a
     checkmark that draws in right after the card settles (causality: it fires the
     instant the server confirmed the code, not before, not after). */
  .unlock-overlay { position: fixed; inset: 0; z-index: 80; display: grid; place-items: center; padding: 24px; }
  .unlock-scrim { position: absolute; inset: 0; background: rgba(6,8,26,.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }
  .unlock-card { position: relative; z-index: 1; width: 100%; max-width: 360px; padding: 40px 32px 32px; border-radius: 28px; text-align: center; background: rgba(21,18,63,.86); border: 1px solid rgba(150,170,255,.28); backdrop-filter: blur(28px) saturate(160%); -webkit-backdrop-filter: blur(28px) saturate(160%); box-shadow: 0 44px 100px -24px rgba(6,6,26,.7), 0 0 0 1px rgba(255,255,255,.04) inset; }
  .unlock-card-enter { transition: opacity .42s cubic-bezier(.16,1,.3,1), transform .42s cubic-bezier(.16,1,.3,1), filter .42s cubic-bezier(.16,1,.3,1); }
  .unlock-card-enter-start { opacity: 0; transform: scale(.92) translateY(8px); filter: blur(10px); }
  .unlock-card-enter-end { opacity: 1; transform: scale(1) translateY(0); filter: blur(0); }
  /* Badge reads as a signal lock, not a generic checkmark: green (the one universal
     "success" color, distinct from the brand's blue/violet CTA below it) with a
     radar-style ping that confirms the connection just went through. */
  .unlock-badge-wrap { position: relative; display: grid; place-items: center; width: 76px; height: 76px; margin: 0 auto; }
  .unlock-ping { position: absolute; inset: 0; border-radius: 50%; border: 1.5px solid rgba(34,227,160,.55); opacity: 0; }
  .unlock-ping-1 { animation: unlock-ping 1.5s cubic-bezier(.25,.8,.4,1) .35s 1; }
  .unlock-ping-2 { animation: unlock-ping 1.5s cubic-bezier(.25,.8,.4,1) .62s 1; }
  @keyframes unlock-ping { 0% { transform: scale(.75); opacity: .6; } 100% { transform: scale(2.1); opacity: 0; } }
  .unlock-check { position: relative; z-index: 1; display: block; animation: unlock-badge-pop .5s cubic-bezier(.34,1.56,.64,1) .08s both; filter: drop-shadow(0 10px 26px rgba(34,227,160,.5)); }
  @keyframes unlock-badge-pop { 0% { transform: scale(.5); opacity: 0; } 60% { transform: scale(1.08); opacity: 1; } 100% { transform: scale(1); opacity: 1; } }
  .unlock-tick { stroke: #06061a; stroke-width: 5; stroke-linecap: round; stroke-linejoin: round; stroke-dasharray: 46; stroke-dashoffset: 46; transition: stroke-dashoffset .48s cubic-bezier(.65,0,.35,1); }
  .unlock-tick.is-drawn { stroke-dashoffset: 0; }
  .unlock-title { margin-top: 22px; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.5rem; color: #fff; letter-spacing: -.01em; animation: unlock-rise .42s cubic-bezier(.16,1,.3,1) .3s both; }
  .unlock-sub { margin-top: 8px; color: rgb(200 208 240); font-size: .95rem; line-height: 1.5; animation: unlock-rise .42s cubic-bezier(.16,1,.3,1) .38s both; }
  .unlock-sub strong { color: #fff; font-weight: 600; }
  @keyframes unlock-rise { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
  .unlock-go { margin-top: 24px; width: 100%; padding: 13px 20px; border-radius: 14px; border: none; background: linear-gradient(100deg, #5be1ff, #73b6ff 55%, #9b6bff); color: #06061a; font-weight: 700; font-size: .95rem; cursor: pointer; transition: transform .12s ease-out; animation: unlock-rise .42s cubic-bezier(.16,1,.3,1) .46s both; }
  .unlock-go:active { transform: scale(.97); }
  html[data-theme="light"] .unlock-card { background: rgba(255,255,255,.92); border-color: rgba(0,0,0,.08); box-shadow: 0 44px 100px -24px rgba(20,24,60,.28), 0 0 0 1px rgba(0,0,0,.02) inset; }
  html[data-theme="light"] .unlock-title { color: #0d0d0d; }
  html[data-theme="light"] .unlock-sub { color: #4a5070; }
  html[data-theme="light"] .unlock-sub strong { color: #0d0d0d; }
  @media (prefers-reduced-transparency: reduce) {
    .unlock-scrim { backdrop-filter: none; -webkit-backdrop-filter: none; background: rgba(6,8,26,.85); }
    .unlock-card { backdrop-filter: none; -webkit-backdrop-filter: none; background: #15123f; }
    html[data-theme="light"] .unlock-card { background: #fff; }
  }
  @media (prefers-reduced-motion: reduce) {
    .unlock-card-enter { transition: opacity .2s ease; }
    .unlock-card-enter-start, .unlock-card-enter-end { transform: none; filter: none; }
    .unlock-card-enter-start { opacity: 0; }
    .unlock-card-enter-end { opacity: 1; }
    .unlock-check { animation: none; }
    .unlock-tick { transition: none; }
    .unlock-ping { display: none; }
    .unlock-title, .unlock-sub, .unlock-go { animation: none; opacity: 1; transform: none; }
  }
  html[data-theme="light"] .page-status { color: #2f4fbf; background: rgba(47,79,191,.08); border-color: rgba(47,79,191,.25); }
  @media (prefers-reduced-motion: reduce) {
    .course-card, .course-art, .course-go svg, .course-bar > i { transition: none; }
    .ps-star, .soon-pill i { animation: none; }
  }
</style>
  <link rel="stylesheet" href="{{ asset('css/cursor.css') }}?v={{ filemtime(public_path('css/cursor.css')) }}">
</head>
<body class="bg-void font-sans text-ink antialiased" x-data="techlabShell()" x-init="init()">
  <div class="space" aria-hidden="true"></div>
  <div class="flex h-screen w-screen overflow-hidden">
    @include('components.shell.side-bar')
    <main class="min-w-0 flex-1 overflow-y-auto">
<div>
  <div class="mx-auto max-w-5xl px-6 py-10">
    <a href="{{ route('student.dashboard') }}" class="text-sm text-muted hover:text-ink">← Planets</a>
    <h1 class="mt-3 font-display text-3xl font-bold text-ink">{{ $planet['title'] }}</h1>
    <p class="mt-1 text-muted">{{ $planet['blurb'] }}</p>
    @if (session('status'))
      <p class="page-status" role="status">{{ session('status') }}</p>
    @endif

    <div class="mt-8 grid gap-6 sm:grid-cols-2" data-tour="course-list">
      @foreach ($courses as $c)
        @php $navigable = (bool) $c['url']; @endphp
        @php $tag = $navigable ? 'a' : 'div'; @endphp
        @php
          // Astro's tour points at the first locked course and the first open one.
          $tourMark = null;
          if (($c['locked'] ?? false) && empty($tourLocked)) { $tourMark = $tourLocked = 'course-locked'; }
          elseif ($navigable && empty($tourOpen)) { $tourMark = $tourOpen = 'course-open'; }
        @endphp
        @php $accent = ['programming' => '#73b6ff', 'networking' => '#5be1ff', 'cybersecurity' => '#b48bff'][$slug] ?? '#73b6ff'; @endphp
        <{{ $tag }} class="course-card {{ $c['soon'] ? 'is-soon' : '' }} {{ ! $navigable && ! $c['soon'] ? 'is-closed' : '' }}" style="--accent: {{ $accent }}"
          @if ($tourMark) data-tour="{{ $tourMark }}" @endif
          @if ($navigable) href="{{ $c['url'] }}" @endif
          aria-label="{{ $c['soon'] ? 'Coming soon' : ($c['title'].' course'.($navigable ? ', '.$c['percent'].'% complete' : '')) }}"
>
          <div class="course-banner" style="background: {{ $c['banner'] }}">
            @if ($c['soon'])
              <div class="soon-glyph" aria-hidden="true">&lt;/&gt;</div>
            @elseif (! empty($c['art']))
              <img class="course-art" src="{{ asset($c['art']) }}" width="1250" height="500" alt="" decoding="async">
            @elseif (! empty($c['scene']))
              <x-dynamic-component :component="$c['scene']" />
            @elseif (! empty($c['logo']))
              <x-dynamic-component :component="$c['logo']" />
            @endif
          </div>
          <div class="flex flex-1 flex-col px-5 pb-5 pt-4">
            @unless ($c['soon'])
              <div class="course-eyebrow">Course</div>
              <div class="course-title mt-1 flex items-center gap-2">
                <span>{{ $c['title'] }}</span>
              </div>
              <div class="course-blurb mt-2">{{ $c['blurb'] }}</div>
              @if (($c['requires_title'] ?? null) && ! $c['track'])
                <div>
                  <span class="prereq-tag {{ $c['requires_done'] ? 'is-done' : '' }}">
                    @if ($c['requires_done'])
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                      Pre-requisite: {{ $c['requires_title'] }} (done)
                    @else
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l2.6 5.6 6.1.7-4.5 4.2 1.2 6L12 16.6 6.6 19.5l1.2-6L3.3 9.3l6.1-.7z"/></svg>
                      Pre-requisite: {{ $c['requires_title'] }}
                    @endif
                  </span>
                </div>
              @endif
              @if ($c['track'])
                <div class="captain-line">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.4"/><path d="M4.5 19.4c.7-3.6 3.6-5.8 7.5-5.8s6.8 2.2 7.5 5.8"/></svg>
                  {{ $c['track']['type'] === 'self_paced' ? 'Self-paced' : 'Class · Captain '.$c['track']['teacher'] }}
                </div>
              @endif
            @endunless
            <div class="{{ $c['soon'] ? 'my-auto' : 'mt-auto pt-5' }}">
              @if ($c['soon'])
                <span class="soon-pill"><i></i>Coming soon</span>
              @elseif ($c['locked'])
                @include('student.partials.enroll-choice', ['e' => $c, 'variant' => 'card', 'tourMark' => $tourMark === 'course-locked'])
              @else
                @php
                  $level = $c['level'] ?? 'Beginner';
                  $levelBars = ['Beginner' => 1, 'Intermediate' => 2, 'Advanced' => 3][$level] ?? 1;
                @endphp
                <div class="flex items-center justify-between gap-3">
                  <span class="course-pill">
                    <span class="course-bars" aria-hidden="true">@for ($b = 1; $b <= 3; $b++)<i class="{{ $b <= $levelBars ? 'on' : '' }}"></i>@endfor</span>
                    {{ $level }}
                  </span>
                  <span class="course-go">{{ $c['percent'] >= 100 ? 'Review' : ($c['percent'] > 0 ? 'Continue' : 'Start') }}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                  </span>
                </div>
                @if ($c['percent'] > 0)
                  <div class="mt-4 flex items-center gap-3">
                    <div class="course-bar flex-1" role="progressbar" aria-valuenow="{{ $c['percent'] }}" aria-valuemin="0" aria-valuemax="100"><i style="width: {{ $c['percent'] }}%"></i></div>
                    <span class="course-pct">{{ $c['percent'] }}%</span>
                  </div>
                @endif
              @endif
            </div>
          </div>
        </{{ $tag }}>
      @endforeach
    </div>
  </div>
</div>
    </main>
  </div>

  {{-- "You're in!" — fires the instant the server confirms the code, not before. --}}
  <div x-data="unlockCelebration()" x-show="open" x-cloak
       class="unlock-overlay" role="dialog" aria-modal="true" aria-label="Course unlocked"
       @keydown.escape.window="open && go()">
    <div class="unlock-scrim" x-show="open" x-transition.opacity.duration.300ms @click="go()"></div>
    <div class="unlock-card"
         x-show="open"
         x-transition:enter="unlock-card-enter"
         x-transition:enter-start="unlock-card-enter-start"
         x-transition:enter-end="unlock-card-enter-end">
      <div class="unlock-badge-wrap">
        <span class="unlock-ping unlock-ping-1" aria-hidden="true"></span>
        <span class="unlock-ping unlock-ping-2" aria-hidden="true"></span>
        <svg class="unlock-check" width="76" height="76" viewBox="0 0 76 76" fill="none" aria-hidden="true">
          <defs>
            <linearGradient id="unlockRingGrad" x1="0" y1="0" x2="76" y2="76">
              <stop offset="0%" stop-color="#7dffce"/>
              <stop offset="55%" stop-color="#22e3a0"/>
              <stop offset="100%" stop-color="#0fae7e"/>
            </linearGradient>
          </defs>
          <circle cx="38" cy="38" r="35" fill="url(#unlockRingGrad)"/>
          <path d="M23 39l10 10 20-22" class="unlock-tick" :class="{ 'is-drawn': drawn }"/>
        </svg>
      </div>
      <h2 class="unlock-title">You're in!</h2>
      <p class="unlock-sub">You're officially enrolled in <strong x-text="title"></strong>.</p>
      <button type="button" class="unlock-go" @click="go()">Enter course →</button>
    </div>
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

    function unlockCelebration() {
      return {
        open: false,
        drawn: false,
        title: '',
        url: '',
        timer: null,
        init() {
          window.addEventListener('course-unlocked', (e) => {
            this.title = e.detail.title;
            this.url = e.detail.url;
            this.drawn = false;
            this.open = true;
            navigator.vibrate?.(12);
            // The checkmark draws in a beat after the card settles — the two motions
            // stay distinct instead of arriving as one indistinguishable flash.
            setTimeout(() => { this.drawn = true; }, 180);
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.go(), 2400);
          });
        },
        go() {
          clearTimeout(this.timer);
          if (this.url) window.location.href = this.url;
        },
      };
    }
  </script>
</body>
</html>
