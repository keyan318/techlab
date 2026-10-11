@php
  $first    = $user->codename ?: (explode(' ', trim($user->name))[0] ?: 'Explorer');
  $parts    = preg_split('/\s+/', trim($user->name), -1, PREG_SPLIT_NO_EMPTY);
  $initials = mb_strtoupper(count($parts) >= 2 ? mb_substr($parts[0], 0, 1).mb_substr(end($parts), 0, 1) : mb_substr($user->name, 0, 2));
  $started  = $xp > 0 || ($hero['started'] ?? false);
  $stats    = [
    ['star',  number_format($xp),           'Total XP'],
    ['gem',   $badges,                       \Illuminate\Support\Str::plural('Gem', $badges)],
    ['flame', $streak,                       'Day streak'],
  ];
  // Top five of the crew, plus your own row when you're further down.
  $board    = collect($leaderboard)->take(5);
  $mine     = collect($leaderboard)->firstWhere('me', true);
  if ($mine && ! $board->contains('me', true)) { $board->push($mine); }
  $medal    = ['#f5c04a', '#c4ccdd', '#d99562'];
@endphp
<x-pixel-app title="Home">
  <div class="mx-auto grid max-w-[1400px] gap-10 px-4 py-8 sm:px-8 xl:grid-cols-[minmax(0,1fr)_340px]">

    {{-- ═══════════ MAIN ═══════════ --}}
    <div class="flex min-w-0 flex-col gap-10">

      {{-- Astro greets you --}}
      <header class="flex items-center gap-6">
        <x-astro-mascot class="h-20 w-20 flex-none" />
        <p class="px-card px-bubble px-font min-w-0 flex-1 px-6 py-5 text-[clamp(1rem,2vw,1.35rem)]">
          @if (session('status')) {{ session('status') }} @endif
          @if ($started) Welcome back, {{ $first }}! Let's get it. @else Welcome aboard, {{ $first }}! Ready for your first mission? @endif
        </p>
      </header>

      {{-- Jump back in --}}
      <section aria-labelledby="jump-h">
        <h1 id="jump-h" class="px-font mb-5 text-[clamp(1.6rem,3vw,2.2rem)] font-medium">{{ $started ? 'Jump back in' : 'Start here' }}</h1>

        @if ($hero && $hero['view'] === 'planet')
          {{-- Nothing started yet in this planet's course — either the student hasn't
               walked in yet, or (when locked) a faculty captain has claimed it and this
               student isn't on the roster. Either way, no shortcut into lesson content:
               "Ask to join" and the code box only live on the planet's course picker
               (PlanetController::courses), so that's the only place this card points to. --}}
          <div class="px-card px-feature overflow-hidden">
            <div class="relative isolate flex min-h-[clamp(380px,38vw,480px)] items-center px-5 py-10 sm:px-14 sm:py-14" style="background: {{ $hero['banner'] }}">
              @if ($hero['art'] ?? null)
                {{-- The planet's pixel art, the same picture as its card on the planet picker. --}}
                <img src="{{ asset($hero['art']) }}" alt="" aria-hidden="true"
                     class="px-art h-full w-full object-cover object-bottom" style="image-rendering: pixelated;"
                     width="1250" height="500" decoding="async">
              @elseif ($hero['scene'])
                <div class="px-art" aria-hidden="true"><x-dynamic-component :component="$hero['scene']" /></div>
              @endif
              <div class="px-scrim" aria-hidden="true"></div>

              <div class="px-onart relative z-10 min-w-0 max-w-[680px]">
                <p class="text-[14px] font-medium uppercase tracking-[0.24em] text-white/75">Planet</p>
                <h2 class="px-font mt-3 text-[clamp(1.75rem,6vw,4.25rem)] font-bold leading-none">{{ $hero['planetTitle'] }}</h2>
                <p class="mt-5 max-w-[46ch] text-[clamp(1.1rem,1.8vw,1.4rem)] leading-relaxed text-white/90">{{ $hero['planetBlurb'] ?: 'Pick a course to get started.' }}</p>

                <div class="mt-9 flex flex-wrap items-center gap-5">
                  <a href="{{ $hero['planetUrl'] }}" class="px-btn px-btn-lg px-btn-blue">Enter {{ $hero['planetTitle'] }}</a>
                </div>
              </div>
            </div>
          </div>
        @elseif ($hero)
          <div class="px-card px-feature overflow-hidden">
            <div class="relative isolate flex min-h-[clamp(380px,38vw,480px)] items-center px-5 py-10 sm:px-14 sm:py-14" style="background: {{ $hero['banner'] }}">
              {{-- Whatever course the student is on right now, with the same picture as its course card. --}}
              @if ($hero['art'] ?? null)
                <img src="{{ asset($hero['art']) }}" alt="" aria-hidden="true"
                     class="px-art h-full w-full object-cover object-bottom" style="image-rendering: pixelated;"
                     width="1250" height="500" decoding="async">
              @elseif ($hero['scene'])
                <div class="px-art" aria-hidden="true"><x-dynamic-component :component="$hero['scene']" /></div>
              @endif
              <div class="px-scrim" aria-hidden="true"></div>

              <div class="px-onart relative z-10 min-w-0 max-w-[680px]">
                <div class="flex items-center gap-3" role="progressbar" aria-valuenow="{{ $hero['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $hero['title'] }} progress">
                  <div class="px-bar w-[280px] max-w-[55vw]"><i style="width: {{ $hero['percent'] }}%"></i></div>
                  <span class="text-[17px] font-medium">{{ $hero['percent'] }}%</span>
                </div>

                <p class="mt-8 text-[14px] font-medium uppercase tracking-[0.24em] text-white/75">Course</p>
                <h2 class="px-font mt-3 text-[clamp(1.75rem,6vw,4.25rem)] font-bold leading-none">{{ $hero['title'] }}</h2>
                <p class="mt-5 max-w-[46ch] text-[clamp(1.1rem,1.8vw,1.4rem)] leading-relaxed text-white/90">{{ $started ? 'Next lesson' : 'First lesson' }}: {{ $hero['next'] }}</p>

                <div class="mt-9 flex flex-wrap items-center gap-5">
                  <a href="{{ $hero['continueUrl'] }}" class="px-btn px-btn-lg px-btn-blue">{{ $started ? 'Continue Learning' : 'Start Learning' }}</a>
                  <a href="{{ $hero['overviewUrl'] }}" class="px-btn px-btn-lg px-btn-ghost">View course</a>
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
    {{-- Your profile card and your crew's leaderboard card: side by side, stacked in the narrow rail. --}}
    <aside class="grid min-w-0 gap-6 md:grid-cols-2 xl:grid-cols-1 xl:content-start">
      <section class="px-card flex min-w-0 flex-col p-6" aria-labelledby="me-h">
        <div class="flex items-center gap-4">
          @if ($user->avatar)
            <a href="{{ route('student.avatar') }}" class="px-avatar px-avatar-astro" title="Change my astronaut" aria-label="Change my astronaut">
              <x-astronaut :look="$user->avatar" class="h-[86px] w-auto" />
            </a>
          @else
            <a href="{{ route('student.avatar') }}" class="px-avatar" title="Make your astronaut" aria-label="Make your astronaut">{{ $initials }}</a>
          @endif
          <div class="min-w-0">
            <h2 id="me-h" class="truncate text-[1.5rem] font-semibold leading-tight">{{ $user->display_name }}</h2>
            <p class="text-[15px] text-muted">Level {{ $level }}@unless ($user->codename) · <a href="{{ route('student.avatar') }}" class="text-[#4fa8ff] underline-offset-4 hover:underline">Pick a codename</a>@endunless</p>
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

        <div class="flex-1" aria-hidden="true"></div>
        <a href="{{ route('student.progress') }}" class="px-btn mt-8 w-[calc(100%-6px)]">View progress</a>
      </section>

      <section class="px-card min-w-0 p-6" aria-labelledby="lb-h">
        <div class="flex items-end justify-between gap-3">
          <h2 id="lb-h" class="px-font min-w-0 text-[1.2rem] font-semibold leading-tight">Leaderboard</h2>
          @if ($leaderboardSample ?? false)<span class="flex-none text-[12px] text-muted">Sample data · local preview</span>
          @elseif ($board->isNotEmpty())<span class="flex-none text-[12px] text-muted">XP · Gems · Streak</span>@endif
        </div>
        @if ($board->isEmpty())
          <p class="mt-4 text-[15px] text-muted"><a href="{{ route('student.courses') }}" class="text-[#4fa8ff] underline-offset-4 hover:underline">Join a course</a> to see how you stack up against your classmates.</p>
        @else
          <ol class="mt-4 flex flex-col gap-2 p-1">
            @foreach ($board as $row)
              <li @class(['px-inner flex items-center gap-3 px-3 py-2.5', 'px-me' => $row['me']]) @if ($row['me']) aria-current="true" @endif>
                <span class="px-font flex h-7 w-7 flex-none items-center justify-center text-[13px] font-bold {{ $row['rank'] <= 3 ? 'text-[#0b0b22]' : 'bg-[var(--px-track)] text-muted' }}"
                      @if ($row['rank'] <= 3) style="background: {{ $medal[$row['rank'] - 1] }}" @endif>{{ $row['rank'] }}</span>
                <span class="flex h-10 w-10 flex-none items-end justify-center overflow-hidden bg-[#1d1660] text-[12px] font-semibold text-white/80" aria-hidden="true">
                  @if (! empty($row['avatar']))<x-astronaut :look="$row['avatar']" crop="bust" class="h-11 w-11" />@else<span class="mb-2">{{ mb_strtoupper(mb_substr($row['name'], 0, 1)) }}</span>@endif
                </span>
                <div class="min-w-0 flex-1">
                  <p class="truncate text-[14px] font-medium">{{ $row['name'] }}@if ($row['me']) <span class="text-muted">(you)</span>@endif</p>
                  <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[13px] text-muted">
                    <span class="inline-flex items-center gap-1" title="XP"><x-px-icon name="star" :size="14" /><span class="font-semibold text-[#4fa8ff]">{{ number_format($row['xp']) }}</span><span class="sr-only">XP</span></span>
                    <span class="inline-flex items-center gap-1" title="Gems"><x-px-icon name="gem" :size="14" />{{ $row['gems'] }}<span class="sr-only">{{ \Illuminate\Support\Str::plural('gem', $row['gems']) }}</span></span>
                    <span class="inline-flex items-center gap-1" title="Day streak"><x-px-icon name="flame" :size="14" />{{ $row['streak'] }}<span class="sr-only">day streak</span></span>
                  </p>
                </div>
              </li>
            @endforeach
          </ol>
        @endif
      </section>
    </aside>
  </div>
</x-pixel-app>
