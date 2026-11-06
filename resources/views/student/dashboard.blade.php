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
@endphp
<x-pixel-app title="Home">
  <div class="mx-auto flex max-w-[1180px] flex-col gap-10 px-4 py-8 sm:px-8">

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
      <section aria-labelledby="jump-h" data-tour="home-hero">
        @php $boss = ($hero['view'] ?? null) === 'boss' ? $hero : null; @endphp
        <h1 id="jump-h" class="px-font mb-5 text-[clamp(1.6rem,3vw,2.2rem)] font-medium">
          {{ $boss ? ($boss['state'] === 'ready' ? 'The Final Boss awaits' : 'Course complete!') : ($started ? 'Jump back in' : 'Start here') }}
        </h1>

        @if ($hero && $hero['view'] === 'planet')
          {{-- Nothing started yet in this planet's course. Not on it yet: the card offers
               Self-paced and Join Class (same as the course picker). On it but no lesson
               done: it points into the planet. --}}
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
                @if ($hero['joined'] ?? null)
                  {{-- The faculty member accepted this student into their class. --}}
                  <p class="mb-6 inline-flex items-center gap-2 rounded-full border border-[#5be38a]/50 bg-[#5be38a]/15 px-3.5 py-1.5 text-[14px] font-semibold text-[#8ff0b0]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                    Accepted into {{ $hero['joined']['captain'] }}'s class
                  </p>
                @endif
                <p class="text-[14px] font-medium uppercase tracking-[0.24em] text-white/75">Planet</p>
                <h2 class="px-font mt-3 text-[clamp(1.75rem,6vw,4.25rem)] font-bold leading-none">{{ $hero['planetTitle'] }}</h2>
                <p class="mt-5 max-w-[46ch] text-[clamp(1.1rem,1.8vw,1.4rem)] leading-relaxed text-white/90">{{ $hero['planetBlurb'] ?: 'Pick a course to get started.' }}</p>

                @if ($hero['enroll'] ?? null)
                  {{-- Not on this course yet: start it Self-paced, or pick an instructor and join their class. --}}
                  <p class="mt-7 text-[15px] font-semibold text-white">{{ $hero['courseTitle'] }}: how do you want to learn?</p>
                  <div class="mt-4">
                    @include('student.partials.enroll-choice', ['e' => $hero['enroll'], 'variant' => 'hero'])
                  </div>
                  <a href="{{ $hero['planetUrl'] }}" class="mt-5 inline-block text-[14px] font-medium text-white/80 underline-offset-4 hover:text-white hover:underline">See every course in {{ $hero['planetTitle'] }} →</a>
                @else
                  <div class="mt-9 flex flex-wrap items-center gap-5">
                    <a href="{{ $hero['planetUrl'] }}" class="px-btn px-btn-lg px-btn-blue">Enter {{ $hero['planetTitle'] }}</a>
                  </div>
                @endif
              </div>
            </div>
          </div>
        @elseif ($boss)
          {{-- Every lesson of the course is done: its final exam is the next thing to beat. --}}
          <div class="px-card px-feature overflow-hidden">
            <div class="relative isolate flex min-h-[clamp(380px,38vw,480px)] items-center px-5 py-10 sm:px-14 sm:py-14" style="background: {{ $boss['banner'] }}">
              @if ($boss['art'])
                <img src="{{ asset($boss['art']) }}" alt="" aria-hidden="true"
                     class="px-art h-full w-full object-cover object-bottom" style="image-rendering: pixelated;"
                     width="1250" height="500" decoding="async">
              @elseif ($boss['scene'])
                <div class="px-art" aria-hidden="true"><x-dynamic-component :component="$boss['scene']" /></div>
              @endif
              <div class="px-scrim" aria-hidden="true"></div>
              {{-- A red-gold glow behind the boss, the Final Boss colours. --}}
              <div class="absolute inset-0" aria-hidden="true" style="background: linear-gradient(110deg, rgba(229,72,77,0.38), rgba(255,196,0,0.12) 55%, transparent 80%)"></div>

              <div class="px-onart relative z-10 min-w-0 max-w-[680px]">
                <div class="flex items-center gap-4">
                  <x-boss-sprite :size="64" class="flex-none {{ $boss['state'] === 'not_set' ? 'opacity-60 grayscale' : '' }}" />
                  <div class="min-w-0">
                    <p class="text-[14px] font-semibold uppercase tracking-[0.24em] text-[#ffc400]">Final Boss</p>
                    <p class="mt-1 text-[15px] text-white/80">{{ $boss['courseTitle'] }} · every lesson cleared</p>
                  </div>
                </div>

                <h2 class="px-font mt-7 text-[clamp(1.75rem,5vw,3.6rem)] font-bold leading-none">{{ $boss['title'] ?? 'The Final Exam' }}</h2>
                <p class="mt-5 max-w-[50ch] text-[clamp(1.05rem,1.7vw,1.3rem)] leading-relaxed text-white/90">
                  @switch($boss['state'])
                    @case('ready')
                      {{ $boss['questions'] }} {{ \Illuminate\Support\Str::plural('question', $boss['questions']) }}@if ($boss['minutes']) · {{ $boss['minutes'] }} min @endif
                      · score {{ $boss['passPercent'] }}% or more to win your certificate. Full screen, no leaving. Fail it and you redo the course.
                      @break
                    @case('not_set')
                      You cleared every lesson! {{ $boss['captainName'] }} hasn't set the final exam yet. It shows up here as soon as they do.
                      @break
                    @default
                      @if ($boss['violation'])
                        The exam ended early: {{ strtolower($boss['violation']) }}.
                      @else
                        You scored {{ $boss['percent'] }}% and needed {{ $boss['passPercent'] }}%.
                      @endif
                      @if ($boss['banned'] ?? false)
                        That was your second strike, so you're banned from the Final Boss. Only {{ $boss['captainName'] }} can lift it.
                      @else
                        Talk to {{ $boss['captainName'] }}: they can let you try again.
                      @endif
                  @endswitch
                </p>

                <div class="mt-9 flex flex-wrap items-center gap-5">
                  @if ($boss['state'] === 'ready')
                    <a href="{{ $boss['url'] }}" class="px-btn px-btn-lg px-btn-blue">Face the Final Boss</a>
                  @endif
                  <a href="{{ $boss['overviewUrl'] }}" class="px-btn px-btn-lg {{ in_array($boss['state'], ['not_set', 'failed']) ? 'px-btn-blue' : 'px-btn-ghost' }}">View course</a>
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
                @if ($hero['joined'] ?? null)
                  {{-- The faculty member accepted this student into their class. --}}
                  <p class="mb-6 inline-flex items-center gap-2 rounded-full border border-[#5be38a]/50 bg-[#5be38a]/15 px-3.5 py-1.5 text-[14px] font-semibold text-[#8ff0b0]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                    Accepted into {{ $hero['joined']['captain'] }}'s class
                  </p>
                @endif
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

    {{-- ═══════════ PROFILE + LEADERBOARD ═══════════ --}}
    {{-- Under the course: your profile card and your crew's leaderboard, side by side (stacked on phones). --}}
    <aside class="grid min-w-0 items-stretch gap-6 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
      <section class="px-card flex min-w-0 flex-col p-6" aria-labelledby="me-h" data-tour="home-profile">
        <div class="flex items-center gap-4">
          @if ($user->avatar)
            <a href="{{ route('student.avatar') }}" class="flex-none" data-tour="home-avatar" title="Change my astronaut" aria-label="Change my astronaut">
              <x-avatar-frame :frame="$frame" :level="$level" :look="$user->avatar" :name="$user->display_name" :size="104" crop="full" />
            </a>
          @else
            <a href="{{ route('student.avatar') }}" class="px-avatar" data-tour="home-avatar" title="Make your astronaut" aria-label="Make your astronaut">{{ $initials }}</a>
          @endif
          <div class="min-w-0">
            <h2 id="me-h" class="truncate text-[1.5rem] font-semibold leading-tight">{{ $user->display_name }}</h2>
            <x-rank-badge :level="$level" size="sm" class="mt-1" />
            @unless ($user->codename)<p class="mt-1 text-[15px] text-muted"><a href="{{ route('student.avatar') }}" class="text-[#4fa8ff] underline-offset-4 hover:underline">Pick a codename</a></p>@endunless
          </div>
        </div>

        <dl class="mt-7 grid grid-cols-3 gap-x-4 gap-y-6">
          @foreach ($stats as [$icon, $value, $label])
            <div class="flex min-w-0 flex-col items-start gap-2 sm:flex-row sm:items-center sm:gap-3">
              <x-px-icon :name="$icon" :size="34" :tier="$icon === 'flame' ? \App\Services\StudentDashboardService::streakTier($streak) : 0" />
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

      <section class="px-card min-w-0 p-6" aria-labelledby="lb-h" data-tour="home-leaderboard">
        <div class="flex items-end justify-between gap-3">
          <h2 id="lb-h" class="px-font min-w-0 text-[1.2rem] font-semibold leading-tight">{{ ($leaderboardTitle ?? null) ? $leaderboardTitle.' leaderboard' : 'Leaderboard' }}</h2>
          @if ($leaderboardSample ?? false)<span class="flex-none text-[12px] text-muted">Sample data · local preview</span>
          @elseif ($board->isNotEmpty())<span class="flex-none text-[12px] text-muted">By level</span>@endif
        </div>
        @if ($board->isEmpty())
          <p class="mt-4 text-[15px] text-muted"><a href="{{ route('student.courses') }}" class="text-[#4fa8ff] underline-offset-4 hover:underline">Join a course</a> to see how you stack up against everyone on the planet.</p>
        @else
          <ol class="mt-4 flex flex-col gap-2 p-1">
            @foreach ($board as $row)
              @include('student.partials.leaderboard-row', ['row' => $row, 'i' => $loop->index, 'frameSize' => 104])
            @endforeach
          </ol>
        @endif
      </section>
    </aside>
  </div>
  @if ($rankUp ?? null)<x-rank-up :rank="$rankUp" :look="$user->avatar" :name="$user->display_name" />
  @elseif ($streakPop ?? null)<x-streak-pop :card="$streakPop" />@endif
</x-pixel-app>
