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
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />

    <meta name="theme-color" content="#06061a">
  <link rel="stylesheet" href="{{ asset('css/theme.css') }}?v={{ filemtime(public_path('css/theme.css')) }}">
  <script src="{{ asset('js/theme.js') }}?v={{ filemtime(public_path('js/theme.js')) }}"></script>
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
<style>
  /* Course cards: a dark frosted-glass material (Apple-style) that sits on the space backdrop.
     Bright top edge = light catching the glass; text stays high-contrast on the darker surface. */
  .course-card {
    --lift: 0px;
    position: relative; display: flex; flex-direction: column; overflow: hidden; border-radius: 24px;
    background: linear-gradient(180deg, rgba(255,255,255,.075), rgba(255,255,255,.03));
    border: 1px solid rgba(150,170,255,.16); border-top-color: rgba(255,255,255,.28);
    -webkit-backdrop-filter: blur(20px) saturate(160%); backdrop-filter: blur(20px) saturate(160%);
    box-shadow: 0 1px 0 rgba(255,255,255,.06) inset, 0 18px 40px -18px rgba(0,0,0,.65);
    transform: translateY(var(--lift));
    transition: transform .35s cubic-bezier(.32,.72,0,1), box-shadow .35s cubic-bezier(.32,.72,0,1), border-color .2s ease;
    color: rgb(234 238 255); text-decoration: none;
  }
  a.course-card:hover, a.course-card:focus-visible { --lift: -6px; border-color: rgba(115,182,255,.5); box-shadow: 0 1px 0 rgba(255,255,255,.08) inset, 0 28px 50px -18px rgba(0,0,0,.75), 0 0 0 1px rgba(115,182,255,.25); outline: none; }
  a.course-card:active { --lift: -2px; transition-duration: .1s; }
  a.course-card:focus-visible { box-shadow: 0 0 0 3px rgba(115,182,255,.55); }
  .course-banner { height: 148px; position: relative; overflow: hidden; margin: 8px 8px 0; border-radius: 18px; }
  .course-banner::after { content: ""; position: absolute; inset: 0; border-radius: inherit; box-shadow: inset 0 0 0 1px rgba(255,255,255,.14); pointer-events: none; }
  .course-banner svg { position: absolute; right: 18px; bottom: -14px; width: 96px; height: 96px; opacity: .9; }
  .course-banner .scene-python-space { position: absolute; inset: 0; width: 100%; height: 100%; right: auto; bottom: auto; opacity: 1; }
  .ps-star { animation: ps-twinkle 3s steps(2, end) infinite; }
  @keyframes ps-twinkle { 50% { opacity: .25; } }
  .course-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .16em; text-transform: uppercase; color: rgb(152 162 212); }
  .course-title { font-family: 'Space Grotesk', sans-serif; font-size: 26px; line-height: 1.1; letter-spacing: -.02em; font-weight: 700; }
  .course-blurb { font-size: 14px; line-height: 1.5; color: rgb(190 198 235); }
  .course-bar { height: 6px; border-radius: 99px; background: rgba(150,170,255,.16); overflow: hidden; }
  .course-bar > i { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #5be1ff, #73b6ff 55%, #9b6bff); transition: width .6s cubic-bezier(.32,.72,0,1); }
  .course-go { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #73b6ff; }
  .course-go svg { transition: transform .3s cubic-bezier(.32,.72,0,1); }
  a.course-card:hover .course-go svg { transform: translateX(4px); }
  /* Coming soon: same shape, quieter. Not a link, so no hover lift. */
  .course-card.is-soon { cursor: default; background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.015)); border-style: dashed; border-color: rgba(150,170,255,.22); box-shadow: none; }
  .course-card.is-soon .course-banner { filter: saturate(.7); }
  .soon-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 11px; border-radius: 99px; font-size: 12px; font-weight: 600; color: #ffe08a; background: rgba(245,192,74,.12); border: 1px solid rgba(245,192,74,.3); }
  .soon-pill i { width: 6px; height: 6px; border-radius: 50%; background: #f5c04a; animation: soon-pulse 2.4s ease-in-out infinite; }
  @keyframes soon-pulse { 50% { opacity: .35; } }
  .soon-glyph { position: absolute; inset: 0; display: grid; place-items: center; font-family: 'Space Mono', monospace; font-size: 40px; letter-spacing: .05em; color: rgba(255,255,255,.55); text-shadow: 0 0 24px rgba(155,107,255,.6); }
  .captain-line { display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: rgb(190 198 235); margin-top: 2px; }
  .captain-line svg { flex: none; opacity: .8; }
  html[data-theme="light"] .captain-line { color: #5b6488; }
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
  html[data-theme="light"] .lock-pill.is-ok { color: #0f7a4a; background: rgba(15,122,74,.1); border-color: rgba(15,122,74,.3); }
  html[data-theme="light"] .lock-pill.is-no { color: #a61b41; background: rgba(214,50,90,.08); border-color: rgba(214,50,90,.3); }
  html[data-theme="light"] .join-note { color: #5b6488; }
  html[data-theme="light"] .page-status { color: #2f4fbf; background: rgba(47,79,191,.08); border-color: rgba(47,79,191,.25); }
  html[data-theme="light"] .lock-pill { color: #2f4fbf; background: rgba(47,79,191,.1); border-color: rgba(47,79,191,.3); }
  html[data-theme="light"] .code-form input { background: #f3f4fb; border-color: rgba(0,0,0,.14); color: #0d0d0d; }
  /* Light theme: the glass card becomes a clean white card with dark, readable text */
  html[data-theme="light"] .course-card { color: rgb(var(--c-ink)); background: #fff; border-color: rgba(0,0,0,.09); -webkit-backdrop-filter: none; backdrop-filter: none; box-shadow: 0 1px 2px rgba(0,0,0,.05), 0 14px 32px -16px rgba(20,24,60,.28); }
  html[data-theme="light"] a.course-card:hover, html[data-theme="light"] a.course-card:focus-visible { border-color: rgba(36,104,196,.45); box-shadow: 0 1px 2px rgba(0,0,0,.05), 0 24px 44px -18px rgba(20,24,60,.35), 0 0 0 1px rgba(36,104,196,.2); }
  html[data-theme="light"] a.course-card:focus-visible { box-shadow: 0 0 0 3px rgba(36,104,196,.45); }
  html[data-theme="light"] .course-eyebrow { color: #5b6488; }
  html[data-theme="light"] .course-blurb { color: #4a5070; }
  html[data-theme="light"] .course-bar { background: rgba(0,0,0,.08); }
  html[data-theme="light"] .course-go { color: #2468c4; }
  html[data-theme="light"] .course-card.is-soon { background: #f6f6fb; border-color: rgba(0,0,0,.16); box-shadow: none; }
  html[data-theme="light"] .soon-pill { color: #8a5300; background: rgba(245,192,74,.2); border-color: rgba(200,140,20,.4); }
  html[data-theme="light"] .soon-pill i { background: #d88f00; }
  @media (prefers-reduced-motion: reduce) {
    .course-card, .course-go svg, .course-bar > i { transition: none; }
    .ps-star, .soon-pill i { animation: none; }
    a.course-card:hover { --lift: 0px; }
  }
  @media (prefers-reduced-transparency: reduce) { .course-card { background: #15123f; -webkit-backdrop-filter: none; backdrop-filter: none; } }
</style>
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

    <div class="mt-8 grid gap-6 sm:grid-cols-2">
      @foreach ($courses as $c)
        @php $navigable = ! $c['soon'] && empty($c['locked']); @endphp
        @php $tag = $navigable ? 'a' : 'div'; @endphp
        <{{ $tag }} class="course-card {{ $c['soon'] ? 'is-soon' : '' }}"
          @if ($navigable) href="{{ $c['url'] }}" @endif
          aria-label="{{ $c['soon'] ? 'Coming soon' : ($c['title'].' course'.($navigable ? ', '.$c['percent'].'% complete' : '')) }}"
          @if ($c['locked'] ?? false) x-data="{ code: '', busy: false, err: '' }" @endif>
          <div class="course-banner" style="background: {{ $c['banner'] }}">
            @if ($c['soon'])
              <div class="soon-glyph" aria-hidden="true">&lt;/&gt;</div>
            @elseif (! empty($c['scene']))
              <x-dynamic-component :component="$c['scene']" />
            @elseif (! empty($c['logo']))
              <x-dynamic-component :component="$c['logo']" />
            @endif
          </div>
          <div class="flex flex-1 flex-col px-6 pb-6 pt-5">
            @unless ($c['soon'])
              <div class="course-eyebrow">Course</div>
              <div class="course-title mt-2">{{ $c['title'] }}</div>
              <div class="course-blurb mt-1.5">{{ $c['blurb'] }}</div>
              @if ($c['faculty_name'] ?? null)
                <div class="captain-line">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.4"/><path d="M4.5 19.4c.7-3.6 3.6-5.8 7.5-5.8s6.8 2.2 7.5 5.8"/></svg>
                  Captain: {{ $c['faculty_name'] }}
                </div>
              @endif
            @endunless
            <div class="{{ $c['soon'] ? 'my-auto' : 'mt-auto pt-6' }}">
              @if ($c['soon'])
                <span class="soon-pill"><i></i>Coming soon</span>
              @elseif ($c['locked'] ?? false)
                @php
                  $joinStatus = $c['join_status'] ?? null;
                  $captain = $c['faculty_name'] ?? 'Your faculty member';
                @endphp
                <span class="lock-pill {{ ['accepted' => 'is-ok', 'declined' => 'is-no'][$joinStatus] ?? '' }}">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                  @switch ($joinStatus)
                    @case ('pending') Waiting for {{ $captain }} to accept you @break
                    @case ('accepted') Accepted! Check your email for the code @break
                    @case ('declined') {{ $captain }} declined your request @break
                    @default {{ $captain }} decides who joins
                  @endswitch
                </span>
                @if ($joinStatus === 'pending')
                  <p class="join-note">We'll email {{ auth()->user()->email }} when they decide.</p>
                @elseif ($joinStatus !== 'accepted')
                  <button type="button" class="join-btn" :disabled="busy" @click.prevent="
                    busy = true; err = '';
                    fetch('{{ $c['join_url'] }}', {
                      method: 'POST', credentials: 'same-origin',
                      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    }).then(r => { if (r.ok) { location.reload(); } else { err = r.status === 429 ? 'Too many tries. Wait a minute.' : 'Something went wrong. Try again.'; busy = false; } })
                      .catch(() => { err = 'Something went wrong. Try again.'; busy = false; });
                  ">{{ $joinStatus === 'declined' ? 'Ask again' : 'Ask to join' }}</button>
                @endif
                @if ($joinStatus === 'accepted')
                <p class="join-note">We sent it to {{ auth()->user()->email }}. Not there? Check your spam folder.</p>
                <form class="code-form" @click.prevent @submit.prevent="
                  busy = true; err = '';
                  fetch('{{ route('student.crew.join') }}', {
                    method: 'POST', credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ code: code }),
                  }).then(r => r.json().then(data => ({ ok: r.ok, data })))
                    .then(({ ok, data }) => { if (ok) { location.reload(); } else { err = data.message || 'That code was not found.'; busy = false; } })
                    .catch(() => { err = 'Something went wrong. Try again.'; busy = false; });
                ">
                  <input type="text" x-model="code" maxlength="6" placeholder="Enter code" aria-label="Course code" required>
                  <button type="submit" :disabled="busy || !code">Unlock</button>
                </form>
                @endif
                <p class="code-err" x-show="err" x-text="err" x-cloak></p>
              @else
                <div class="mb-3 flex items-baseline justify-between">
                  <span class="text-sm font-semibold text-ink">{{ $c['percent'] }}% complete</span>
                  <span class="course-go">{{ $c['percent'] > 0 ? 'Continue' : 'Start' }}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                  </span>
                </div>
                <div class="course-bar" role="progressbar" aria-valuenow="{{ $c['percent'] }}" aria-valuemin="0" aria-valuemax="100"><i style="width: {{ $c['percent'] }}%"></i></div>
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
  </script>
</body>
</html>
