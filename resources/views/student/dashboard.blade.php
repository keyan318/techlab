@php
  $first    = explode(' ', trim($user->name))[0] ?: 'Explorer';
  $parts    = preg_split('/\s+/', trim($user->name), -1, PREG_SPLIT_NO_EMPTY);
  $initials = mb_strtoupper(count($parts) >= 2 ? mb_substr($parts[0], 0, 1).mb_substr(end($parts), 0, 1) : mb_substr($user->name, 0, 2));
  $started  = $xp > 0 || ($hero['started'] ?? false);
  $stats    = [
    ['star',  number_format($xp),           'Total XP'],
    ['medal', $rank ? '#'.$rank : '—',      $rank ? 'Crew rank · of '.$crewSize : 'No crew yet'],
    ['gem',   $badges,                       \Illuminate\Support\Str::plural('Badge', $badges)],
    ['flame', $streak,                       'Day streak'],
  ];
@endphp
<x-pixel-app title="Dashboard">
  <div class="mx-auto grid max-w-[1400px] gap-10 px-4 py-8 sm:px-8 xl:grid-cols-[minmax(0,1fr)_340px]">

    {{-- ═══════════ MAIN ═══════════ --}}
    <div class="flex min-w-0 flex-col gap-10">

      {{-- Astro greets you --}}
      <header class="flex items-center gap-6">
        <x-astro-mascot class="h-20 w-20 flex-none" />
        <p class="px-card px-bubble px-font min-w-0 flex-1 px-6 py-5 text-[clamp(1rem,2vw,1.35rem)]">
          @if ($started) Welcome back, {{ $first }}! Let's get it. @else Welcome aboard, {{ $first }}! Ready for your first mission? @endif
        </p>
      </header>

      {{-- Jump back in --}}
      <section aria-labelledby="jump-h">
        <h1 id="jump-h" class="px-font mb-5 text-[clamp(1.6rem,3vw,2.2rem)] font-medium">{{ $started ? 'Jump back in' : 'Start here' }}</h1>

        @if ($hero)
          <div class="px-card overflow-hidden">
            <div class="relative isolate flex min-h-[320px] items-center px-6 py-8 sm:px-12" style="background: {{ $hero['banner'] }}">
              @if ($hero['scene'])
                <div class="px-art" aria-hidden="true"><x-dynamic-component :component="$hero['scene']" /></div>
              @endif
              <div class="px-scrim" aria-hidden="true"></div>

              <div class="px-onart relative z-10 max-w-[520px]">
                <div class="flex items-center gap-3" role="progressbar" aria-valuenow="{{ $hero['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $hero['title'] }} progress">
                  <div class="px-bar w-[220px] max-w-[55vw]"><i style="width: {{ $hero['percent'] }}%"></i></div>
                  <span class="text-[15px] font-medium">{{ $hero['percent'] }}%</span>
                </div>

                <p class="mt-8 text-[12px] font-medium uppercase tracking-[0.22em] text-white/75">Course</p>
                <h2 class="px-font mt-1 text-[clamp(2.2rem,5vw,3.2rem)] font-bold leading-none">{{ $hero['title'] }}</h2>
                <p class="mt-3 text-[clamp(1rem,1.6vw,1.2rem)] text-white/90">{{ $started ? 'Next lesson' : 'First lesson' }}: {{ $hero['next'] }}</p>

                <div class="mt-7 flex flex-wrap items-center gap-4">
                  <a href="{{ $hero['continueUrl'] }}" class="px-btn px-btn-blue">{{ $started ? 'Continue Learning' : 'Start Learning' }}</a>
                  <a href="{{ $hero['overviewUrl'] }}" class="px-btn px-btn-ghost">View course</a>
                </div>
              </div>
            </div>
          </div>
        @else
          <div class="px-card p-8">
            <p class="px-font text-2xl font-medium">All caught up!</p>
            <p class="mt-2 max-w-[46ch] text-muted">You've cleared every lesson we track so far. Explore another planet to keep your streak going.</p>
            <a href="{{ route('student.planets') }}" class="px-btn px-btn-blue mt-6">Browse planets</a>
          </div>
        @endif
      </section>
    </div>

    {{-- ═══════════ PROFILE RAIL ═══════════ --}}
    <aside class="min-w-0">
      <section class="px-card p-6" aria-labelledby="me-h">
        <div class="flex items-center gap-4">
          <div class="px-avatar" aria-hidden="true">{{ $initials }}</div>
          <div class="min-w-0">
            <h2 id="me-h" class="truncate text-[1.5rem] font-semibold leading-tight">{{ $user->name }}</h2>
            <p class="text-[15px] text-muted">Level {{ $level }}</p>
          </div>
        </div>

        <dl class="mt-7 grid grid-cols-2 gap-x-4 gap-y-6">
          @foreach ($stats as [$icon, $value, $label])
            <div class="flex items-center gap-3">
              <x-px-icon :name="$icon" :size="34" />
              <div class="flex min-w-0 flex-col-reverse">
                <dt class="mt-1 text-[13px] leading-snug text-muted">{{ $label }}</dt>
                <dd class="px-font text-[1.3rem] font-semibold leading-none">{{ $value }}</dd>
              </div>
            </div>
          @endforeach
        </dl>

        <a href="{{ route('student.progress') }}" class="px-btn mt-8 w-[calc(100%-6px)]">View progress</a>
      </section>
    </aside>
  </div>
</x-pixel-app>
