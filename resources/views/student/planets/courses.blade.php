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
<style>
  /* Course cards, Codedex-style: a pixel-art banner on top, then COURSE eyebrow, title, blurb and a
     level pill. The art is dark, so the card stays dark (and its text light) in both themes. */
  .course-card {
    --accent: #73b6ff;
    position: relative; display: flex; flex-direction: column; overflow: hidden; isolation: isolate;
    color: #eef1ff; background: #0d1028; text-decoration: none;
    border: 2px solid #252b52; border-radius: 6px;
    box-shadow: 0 5px 0 #1a1f40, 0 18px 36px -20px rgba(0,0,0,.8);
    transition: transform .25s cubic-bezier(.32,.72,0,1), box-shadow .25s cubic-bezier(.32,.72,0,1), border-color .2s ease;
  }
  a.course-card:hover { transform: translateY(-4px); border-color: var(--accent); box-shadow: 0 9px 0 #1a1f40, 0 26px 44px -18px color-mix(in srgb, var(--accent) 45%, transparent); }
  a.course-card:active { transform: translateY(1px); box-shadow: 0 2px 0 #1a1f40; transition-duration: .08s; }
  a.course-card:focus-visible { outline: 3px solid var(--accent); outline-offset: 3px; }
  .course-banner { position: relative; aspect-ratio: 5 / 2; overflow: hidden; border-bottom: 2px solid #252b52; }
  .course-art { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center bottom; image-rendering: pixelated; transition: transform .5s cubic-bezier(.32,.72,0,1); }
  a.course-card:hover .course-art { transform: scale(1.04); }
  .course-banner > svg { position: absolute; right: 18px; bottom: -14px; width: 96px; height: 96px; opacity: .9; }
  .course-banner .scene-python-space { position: absolute; inset: 0; width: 100%; height: 100%; right: auto; bottom: auto; opacity: 1; }
  .ps-star { animation: ps-twinkle 3s steps(2, end) infinite; }
  @keyframes ps-twinkle { 50% { opacity: .25; } }
  .course-eyebrow { font-family: 'Space Mono', monospace; font-size: 11.5px; letter-spacing: .18em; text-transform: uppercase; color: #9aa3d0; }
  .course-title { font-family: 'Space Grotesk', sans-serif; font-size: 1.45rem; line-height: 1.2; letter-spacing: -.01em; font-weight: 700; color: #fff; }
  .course-blurb { font-size: .95rem; line-height: 1.5; color: #c3c9ea; }
  .course-pill { display: inline-flex; align-items: center; gap: 9px; padding: 7px 14px; border-radius: 99px;
    font-family: 'Space Mono', monospace; font-size: 11.5px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: #dfe4ff; background: #1b2042; }
  .course-bars { display: inline-flex; align-items: flex-end; gap: 2px; height: 11px; }
  .course-bars i { width: 3px; background: #48507e; }
  .course-bars i:nth-child(1) { height: 4px; } .course-bars i:nth-child(2) { height: 7px; } .course-bars i:nth-child(3) { height: 11px; }
  .course-bars i.on { background: var(--accent); }
  .course-pct { font-size: 12.5px; font-weight: 600; color: #c3c9ea; }
  .course-bar { height: 4px; background: #1b2042; overflow: hidden; }
  .course-bar > i { display: block; height: 100%; background: var(--accent); transition: width .6s cubic-bezier(.32,.72,0,1); }
  .course-go { display: inline-flex; align-items: center; gap: 6px; font-family: 'Space Grotesk', sans-serif; font-size: .9rem; font-weight: 600; color: var(--accent); white-space: nowrap; }
  .course-go svg { transition: transform .3s cubic-bezier(.32,.72,0,1); }
  a.course-card:hover .course-go svg { transform: translateX(4px); }
  /* Coming soon / locked: same shape, quieter. Not a link, so no hover lift. */
  .course-card.is-soon { cursor: default; border-style: dashed; box-shadow: none; }
  .course-card.is-soon .course-banner { filter: saturate(.7); }
  .course-card.is-closed .course-art { filter: grayscale(.6) brightness(.6); }
  .soon-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 11px; border-radius: 99px; font-size: 12px; font-weight: 600; color: #ffe08a; background: rgba(245,192,74,.12); border: 1px solid rgba(245,192,74,.3); }
  .soon-pill i { width: 6px; height: 6px; border-radius: 50%; background: #f5c04a; animation: soon-pulse 2.4s ease-in-out infinite; }
  @keyframes soon-pulse { 50% { opacity: .35; } }
  .soon-glyph { position: absolute; inset: 0; display: grid; place-items: center; font-family: 'Space Mono', monospace; font-size: 40px; letter-spacing: .05em; color: rgba(255,255,255,.55); text-shadow: 0 0 24px rgba(155,107,255,.6); }
  .captain-line { display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: rgb(190 198 235); margin-top: 2px; }
  .captain-line svg { flex: none; opacity: .8; }
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

    <div class="mt-8 grid gap-6 sm:grid-cols-2">
      @foreach ($courses as $c)
        @php $navigable = (bool) $c['url']; @endphp
        @php $tag = $navigable ? 'a' : 'div'; @endphp
        @php $accent = ['programming' => '#73b6ff', 'networking' => '#5be1ff', 'cybersecurity' => '#b48bff'][$slug] ?? '#73b6ff'; @endphp
        <{{ $tag }} class="course-card {{ $c['soon'] ? 'is-soon' : '' }} {{ ! $navigable && ! $c['soon'] ? 'is-closed' : '' }}" style="--accent: {{ $accent }}"
          @if ($navigable) href="{{ $c['url'] }}" @endif
          aria-label="{{ $c['soon'] ? 'Coming soon' : ($c['title'].' course'.($navigable ? ', '.$c['percent'].'% complete' : '')) }}"
          @if ($c['locked'] ?? false) x-data="{ code: '', busy: false, err: '' }" @endif>
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
                @if (($c['quiz_badge'] ?? 0) > 0)
                  <span style="display:grid;place-items:center;min-width:18px;height:18px;padding:0 5px;border-radius:9999px;background:#e5484d;color:#fff;font-size:11px;font-weight:700;line-height:1"
                        aria-label="{{ $c['quiz_badge'] }} new {{ \Illuminate\Support\Str::plural('quiz', $c['quiz_badge']) }}">{{ $c['quiz_badge'] > 9 ? '9+' : $c['quiz_badge'] }}</span>
                @endif
              </div>
              <div class="course-blurb mt-2">{{ $c['blurb'] }}</div>
              @if ($c['faculty_name'] ?? null)
                <div class="captain-line">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.4"/><path d="M4.5 19.4c.7-3.6 3.6-5.8 7.5-5.8s6.8 2.2 7.5 5.8"/></svg>
                  Captain: {{ $c['faculty_name'] }}
                </div>
              @endif
            @endunless
            <div class="{{ $c['soon'] ? 'my-auto' : 'mt-auto pt-5' }}">
              @if ($c['soon'])
                <span class="soon-pill"><i></i>Coming soon</span>
              @elseif ($c['no_captain'] ?? false)
                <span class="lock-pill">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                  No captain assigned yet
                </span>
                <p class="join-note">Check back once a faculty captain has claimed this course.</p>
              @elseif ($c['locked'] ?? false)
                @php
                  $joinStatus = $c['join_status'] ?? null;
                  $captain = $c['faculty_name'] ?? 'Your faculty member';
                @endphp
                <span class="lock-pill {{ ['accepted' => 'is-ok', 'declined' => 'is-no'][$joinStatus ?? ''] ?? '' }}">
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
                @php
                  // @json()'s output is script-tag-safe, not attribute-safe (its own JSON
                  // quotes are literal ", which would prematurely close this "..." Alpine
                  // directive). Js::from() HTML-escapes for exactly this context.
                  $unlockPayload = ['title' => $c['title'], 'url' => route('student.course.overview', ['slug' => $slug, 'course' => $c['id']])];
                @endphp
                <form class="code-form" @submit.prevent="
                  busy = true; err = '';
                  fetch('{{ route('student.crew.join') }}', {
                    method: 'POST', credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ code: code }),
                  }).then(r => r.json().then(data => ({ ok: r.ok, data })))
                    .then(({ ok, data }) => {
                      if (ok) {
                        window.dispatchEvent(new CustomEvent('course-unlocked', { detail: {{ Illuminate\Support\Js::from($unlockPayload) }} }));
                      } else { err = data.message || 'That code was not found.'; busy = false; }
                    })
                    .catch(() => { err = 'Something went wrong. Try again.'; busy = false; });
                ">
                  <input type="text" x-model="code" maxlength="6" placeholder="Enter code" aria-label="Course code" required>
                  <button type="submit" :disabled="busy || !code">Unlock</button>
                </form>
                @endif
                <p class="code-err" x-show="err" x-text="err" x-cloak></p>
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
