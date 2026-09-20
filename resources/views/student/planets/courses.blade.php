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
  /* Pixel-cut card like the reference: stepped corners + a stacked "shelf" underneath */
  .course-card { position: relative; display: block; background: #fff; color: #0d0d0d; border: 3px solid #c8d3e0; box-shadow: 0 6px 0 -1px #c8d3e0; transition: transform .15s ease, box-shadow .15s ease; }
  .course-card:hover, .course-card:focus-visible { transform: translateY(-3px); box-shadow: 0 9px 0 -1px #c8d3e0; outline: none; }
  .course-banner { height: 130px; background-size: cover; position: relative; overflow: hidden; }
  .course-banner svg { position: absolute; right: 18px; bottom: -14px; width: 96px; height: 96px; opacity: .9; }
  .course-ring { width: 44px; height: 44px; border-radius: 50%; flex: none; }
  @media (prefers-reduced-motion: reduce) { .course-card { transition: none; } }
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

    <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
      @foreach ($courses as $c)
        <a href="{{ $c['url'] }}" class="course-card" aria-label="{{ $c['title'] }} course, {{ $c['percent'] }}% complete">
          <div class="course-banner" style="background: {{ $c['banner'] }}">
            @if (! empty($c['logo']))
              <x-dynamic-component :component="$c['logo']" />
            @endif
          </div>
          <div class="px-6 pb-6 pt-5">
            <div class="text-[13px] font-medium uppercase tracking-[0.2em] text-slate-500">Course</div>
            <div class="mt-2 font-display text-2xl font-bold">{{ $c['title'] }}</div>
            <div class="mt-1 text-sm text-slate-500">{{ $c['blurb'] }}</div>
            <div class="mt-5 flex items-center gap-3">
              <div class="course-ring" style="background: conic-gradient(#f5c04a {{ $c['percent'] }}%, #e2e8f0 0)">
                <div class="m-[6px] h-[32px] w-[32px] rounded-full bg-white"></div>
              </div>
              <span class="text-base font-semibold text-slate-500">{{ $c['percent'] }}% Complete</span>
            </div>
          </div>
        </a>
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
