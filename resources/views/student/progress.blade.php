@php
  $first    = $user->codename ?: (explode(' ', trim($user->name))[0] ?: 'Explorer');
  $parts    = preg_split('/\s+/', trim($user->name), -1, PREG_SPLIT_NO_EMPTY);
  $initials = mb_strtoupper(count($parts) >= 2 ? mb_substr($parts[0], 0, 1).mb_substr(end($parts), 0, 1) : mb_substr($user->name, 0, 2));
  $fmt      = fn (int $m) => $m >= 60 ? intdiv($m, 60).'h '.str_pad($m % 60, 2, '0', STR_PAD_LEFT).'m' : $m.'m';
  $axisMins = $weekly['axisMax'] * 60;
  $perf     = $performance;
  $medal    = ['#f5c04a', '#c4ccdd', '#d99562'];
  $me       = collect($leaderboard)->firstWhere('me', true);
  // The classmate just above you: the lowest XP that still beats yours.
  $ahead    = $me ? collect($leaderboard)->filter(fn ($r) => $r['xp'] > $me['xp'])->last() : null;
  $gemsLeft = max(0, $modulesTotal - $modulesDone);
@endphp
<x-pixel-app title="Progress">
  <div class="mx-auto grid max-w-[1400px] gap-10 px-4 py-8 sm:px-8 xl:grid-cols-[minmax(0,1fr)_340px]">

    {{-- ═══════════ MAIN ═══════════ --}}
    <div class="flex min-w-0 flex-col gap-10">

      <a href="{{ route('student.dashboard') }}" class="w-fit text-[15px] text-muted underline-offset-4 hover:text-ink hover:underline">← Home</a>

      {{-- Who you are: astronaut, level and how far to the next one --}}
      <header class="px-card px-feature p-6 sm:p-8" aria-labelledby="hello-h">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:gap-8">
          @if ($user->avatar)
            <a href="{{ route('student.avatar') }}" class="px-avatar px-avatar-astro px-avatar-xl" title="Change my astronaut" aria-label="Change my astronaut">
              <x-astronaut :look="$user->avatar" class="h-[132px] w-auto" />
            </a>
          @else
            <a href="{{ route('student.avatar') }}" class="px-avatar px-avatar-xl" title="Make your astronaut" aria-label="Make your astronaut">{{ $initials }}</a>
          @endif

          <div class="min-w-0 flex-1">
            <h1 id="hello-h" class="px-font text-[clamp(2rem,4vw,3rem)] font-bold leading-none">Hello, <span class="text-[#4fa8ff]">{{ $first }}</span></h1>
            <p class="mt-3 text-[clamp(1rem,1.5vw,1.15rem)] text-muted">
              @if ($current) Ready for your next mission? Pick up where you left off.
              @elseif ($lessonsDone > 0) You've cleared every tracked lesson. Nice work, astronaut.
              @else Welcome aboard. Complete your first lesson to start earning XP. @endif
            </p>

            <div class="mt-6 flex flex-wrap items-center gap-x-4 gap-y-2">
              <span class="px-font text-[1.25rem] font-semibold">Level {{ $level }}</span>
              <span class="text-[15px] text-muted">Astronaut · {{ $crew ? $crew->name : 'No crew yet' }}</span>
            </div>
            <div class="mt-3 flex max-w-[520px] items-center gap-3" role="progressbar" aria-valuenow="{{ $levelXp }}" aria-valuemin="0" aria-valuemax="{{ $xpPerLevel }}" aria-label="XP toward level {{ $level + 1 }}">
              <div class="px-bar flex-1"><i style="width: {{ $levelXp / $xpPerLevel * 100 }}%"></i></div>
              <span class="flex-none text-[14px] text-muted"><b class="text-ink">{{ $levelXp }}</b> / {{ $xpPerLevel }} XP to Level {{ $level + 1 }}</span>
            </div>
          </div>
        </div>
      </header>

      {{-- The four numbers that matter, each with the detail behind it --}}
      <section aria-label="Your stats" class="grid grid-cols-1 gap-6 sm:grid-cols-2 2xl:grid-cols-4">
        {{-- XP --}}
        <div class="px-card flex flex-col p-6">
          <div class="flex items-center gap-4">
            <x-px-icon name="star" :size="40" />
            <div class="flex min-w-0 flex-col-reverse">
              <p class="mt-1 text-[14px] text-muted">Total XP</p>
              <p class="px-font text-[2rem] font-semibold leading-none">{{ number_format($xp) }}</p>
            </div>
          </div>
          <p class="mt-auto pt-5 text-[14px] text-muted">
            @if ($perf['thisWeekXp'] > 0) <b class="text-[#5fe0a0]">+{{ $perf['thisWeekXp'] }} XP</b> this week
            @else No XP yet this week. Finish a lesson to earn {{ \App\Services\StudentDashboardService::XP_PER_LESSON }}. @endif
          </p>
        </div>

        {{-- Streak: which days this week counted --}}
        <div class="px-card flex flex-col p-6">
          <div class="flex items-center gap-4">
            <x-px-icon name="flame" :size="40" />
            <div class="flex min-w-0 flex-col-reverse">
              <p class="mt-1 text-[14px] text-muted">Day streak</p>
              <p class="px-font text-[2rem] font-semibold leading-none">{{ $streak }} <span class="text-[1rem] text-muted">{{ \Illuminate\Support\Str::plural('day', $streak) }}</span></p>
            </div>
          </div>
          <ol class="mt-auto grid grid-cols-7 gap-1.5 pt-5" aria-label="Days you learned this week">
            @foreach ($weekly['days'] as $d)
              <li class="flex flex-col items-center gap-1.5" title="{{ $d['name'] }}: {{ $d['active'] ? 'learned' : ($d['future'] ? 'still to come' : 'no learning') }}">
                <span @class(['px-day', 'is-on' => $d['active'], 'is-today' => $d['today'], 'is-future' => $d['future']])>
                  @if ($d['active'])<x-px-icon name="flame" :size="14" />@endif
                </span>
                <span class="text-[11px] {{ $d['today'] ? 'font-semibold text-[#4fa8ff]' : 'text-muted' }}">{{ $d['label'] }}</span>
                <span class="sr-only">{{ $d['name'] }}: {{ $d['active'] ? 'learned' : 'no learning' }}</span>
              </li>
            @endforeach
          </ol>
        </div>

        {{-- Gems: one per module cleared --}}
        <div class="px-card flex flex-col p-6">
          <div class="flex items-center gap-4">
            <x-px-icon name="gem" :size="40" />
            <div class="flex min-w-0 flex-col-reverse">
              <p class="mt-1 text-[14px] text-muted">{{ \Illuminate\Support\Str::plural('Gem', $modulesDone) }} · 1 per module</p>
              <p class="px-font text-[2rem] font-semibold leading-none">{{ $modulesDone }} <span class="text-[1rem] text-muted">/ {{ $modulesTotal }}</span></p>
            </div>
          </div>
          <p class="mt-auto pt-5 text-[14px] text-muted">
            @if ($modulesTotal === 0) Join a planet to start collecting gems.
            @elseif ($gemsLeft === 0) Every gem collected. Legendary!
            @else Clear {{ $gemsLeft }} more {{ \Illuminate\Support\Str::plural('module', $gemsLeft) }} to collect the rest. @endif
          </p>
        </div>

        {{-- Crew rank: and how far to the next spot --}}
        <div class="px-card flex flex-col p-6">
          <div class="flex items-center gap-4">
            <x-px-icon name="medal" :size="40" />
            <div class="flex min-w-0 flex-col-reverse">
              <p class="mt-1 text-[14px] text-muted">{{ $me ? 'Crew rank · of '.count($leaderboard) : 'Crew rank' }}</p>
              <p class="px-font text-[2rem] font-semibold leading-none">{{ $me ? '#'.$me['rank'] : '—' }}</p>
            </div>
          </div>
          <p class="mt-auto pt-5 text-[14px] text-muted">
            @if (! $crew) Join a course crew to get ranked.
            @elseif ($ahead) <b class="text-ink">{{ number_format($ahead['xp'] - $me['xp']) }} XP</b> behind #{{ $ahead['rank'] }}, {{ explode(' ', $ahead['name'])[0] }}
            @elseif ($me) You're at the top of your crew!
            @else Not ranked yet. @endif
          </p>
        </div>
      </section>

      {{-- Charts --}}
      <div class="grid gap-8 lg:grid-cols-2">
        {{-- Active hours --}}
        <section class="px-card p-6" aria-labelledby="ah-h">
          <div class="flex items-center justify-between gap-3">
            <h2 id="ah-h" class="px-font text-[1.4rem] font-semibold">Active hours</h2>
            <span class="px-inner px-3 py-1 text-[13px] text-muted">This week</span>
          </div>
          <div class="mt-5 flex gap-3">
            <div class="flex h-[180px] flex-col justify-between pb-6 text-right text-[12px] text-muted" aria-hidden="true">
              <span>{{ $weekly['axisMax'] }}h</span><span>0</span>
            </div>
            <div class="grid flex-1 grid-cols-7 gap-2" role="img"
                 aria-label="Active hours per day this week: {{ collect($weekly['days'])->map(fn ($d) => $d['name'].' '.$fmt($d['minutes']))->implode(', ') }}">
              @foreach ($weekly['days'] as $d)
                <div class="flex flex-col items-center gap-2">
                  <div class="px-col relative h-[156px] w-full max-w-[28px]" title="{{ $d['name'] }}: {{ $fmt($d['minutes']) }}">
                    <i style="height: {{ min(100, $d['minutes'] / $axisMins * 100) }}%"></i>
                  </div>
                  <span class="text-[12px] {{ $d['today'] ? 'font-semibold text-[#4fa8ff]' : 'text-muted' }}">{{ $d['label'] }}</span>
                </div>
              @endforeach
            </div>
          </div>
          <dl class="mt-5 grid grid-cols-3 gap-3 border-t-2 border-[var(--px-track)] pt-5">
            <div><dt class="text-[13px] text-muted">Time spent</dt><dd class="px-font mt-1 text-[1.35rem] font-semibold">{{ $fmt($weekly['totalMinutes']) }}</dd></div>
            <div><dt class="text-[13px] text-muted">Lessons taken</dt><dd class="px-font mt-1 text-[1.35rem] font-semibold">{{ $weekly['lessonsWeek'] }}</dd></div>
            <div><dt class="text-[13px] text-muted">Lessons done</dt><dd class="px-font mt-1 text-[1.35rem] font-semibold">{{ $lessonsDone }}<span class="text-[0.9rem] text-muted">/{{ $lessonsTotal }}</span></dd></div>
          </dl>
        </section>

        {{-- Performance --}}
        <section class="px-card p-6" aria-labelledby="pf-h">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 id="pf-h" class="px-font text-[1.4rem] font-semibold">Performance</h2>
            <div class="flex gap-4 text-[13px] text-muted">
              <span class="flex items-center gap-2"><i class="h-2.5 w-2.5 bg-[#9b6bff]"></i>XP</span>
              <span class="flex items-center gap-2"><i class="h-2.5 w-2.5 bg-[#5be1ff]"></i>Active time</span>
            </div>
          </div>
          <div class="relative mt-5">
            <svg viewBox="0 0 {{ $perf['w'] }} {{ $perf['h'] }}" preserveAspectRatio="none" class="h-[156px] w-full overflow-visible" role="img"
                 aria-label="XP earned and active time per week over the last {{ count($perf['labels']) }} weeks">
              <defs><linearGradient id="xpfill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#9b6bff" stop-opacity=".35"/><stop offset="1" stop-color="#9b6bff" stop-opacity="0"/></linearGradient></defs>
              @for ($g = 0; $g <= 2; $g++)
                <line x1="0" x2="{{ $perf['w'] }}" y1="{{ 8 + $g * (($perf['h'] - 16) / 2) }}" y2="{{ 8 + $g * (($perf['h'] - 16) / 2) }}" stroke="var(--px-track)" vector-effect="non-scaling-stroke"/>
              @endfor
              @if ($perf['hasData'])
                <path d="{{ $perf['xpPath'] }} L{{ $perf['w'] }} {{ $perf['h'] }} L0 {{ $perf['h'] }} Z" fill="url(#xpfill)"/>
                <path d="{{ $perf['minPath'] }}" fill="none" stroke="#5be1ff" stroke-width="2" stroke-linecap="round" vector-effect="non-scaling-stroke" opacity=".85"/>
                <path d="{{ $perf['xpPath'] }}" fill="none" stroke="#9b6bff" stroke-width="3" stroke-linecap="round" vector-effect="non-scaling-stroke"/>
              @endif
            </svg>
            @unless ($perf['hasData'])
              <p class="absolute inset-0 flex items-center justify-center text-[14px] text-muted">Your trend will appear after your first lesson.</p>
            @endunless
          </div>
          <div class="mt-2 flex justify-between text-[12px] text-muted" aria-hidden="true">
            @foreach ($perf['labels'] as $i => $label)
              <span class="{{ $i === count($perf['labels']) - 1 ? 'font-semibold text-[#4fa8ff]' : '' }} {{ $i % 2 ? 'hidden sm:inline' : '' }}">{{ $label }}</span>
            @endforeach
          </div>
          <div class="mt-5 flex items-center gap-4 border-t-2 border-[var(--px-track)] pt-5">
            @if ($perf['changePct'] !== null)
              <p class="px-font flex-none whitespace-nowrap text-[1.6rem] font-semibold {{ $perf['changePct'] >= 0 ? 'text-[#5fe0a0]' : 'text-[#ff8f8f]' }}">{{ $perf['changePct'] >= 0 ? '+' : '' }}{{ $perf['changePct'] }}%</p>
              <p class="text-[14px] leading-snug text-muted">XP this week ({{ $perf['thisWeekXp'] }}) vs last week ({{ $perf['lastWeekXp'] }}).</p>
            @elseif ($perf['thisWeekXp'] > 0)
              <p class="px-font flex-none whitespace-nowrap text-[1.6rem] font-semibold text-[#5fe0a0]">{{ $perf['thisWeekXp'] }} XP</p>
              <p class="text-[14px] leading-snug text-muted">earned this week. Nothing to compare against last week yet.</p>
            @else
              <p class="text-[14px] text-muted">No XP earned this week yet. Complete a lesson to get moving.</p>
            @endif
          </div>
        </section>
      </div>

      {{-- Planets: picture, progress and a gem per module --}}
      <section aria-labelledby="pl-h">
        <h2 id="pl-h" class="px-font mb-5 text-[clamp(1.6rem,3vw,2.2rem)] font-medium">Your planets</h2>
        @if (! count($planets))
          <div class="px-card p-6 text-[15px] text-muted">You haven't joined a planet yet. <a href="{{ route('student.planets') }}" class="text-[#4fa8ff] underline-offset-4 hover:underline">Pick one</a> to start.</div>
        @else
          <div class="grid gap-8 md:grid-cols-2 2xl:grid-cols-3">
            @foreach ($planets as $p)
              <article class="px-card flex flex-col overflow-hidden">
                <div class="relative h-[120px] overflow-hidden" style="background: #120a33">
                  @if ($p['art'])
                    <img src="{{ asset($p['art']) }}" alt="" aria-hidden="true" width="1250" height="500" decoding="async" loading="lazy"
                         class="absolute inset-0 h-full w-full object-cover object-bottom" style="image-rendering: pixelated;">
                  @endif
                </div>
                <div class="flex flex-1 flex-col p-6">
                  <p class="text-[12px] font-medium uppercase tracking-[0.2em] text-muted">{{ $p['world'] }}</p>
                  <h3 class="px-font mt-1 text-[1.5rem] font-semibold leading-tight">{{ $p['name'] }}</h3>

                  @if ($p['tracked'])
                    <div class="mt-4 flex items-center gap-3" role="progressbar" aria-valuenow="{{ $p['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $p['name'] }} progress">
                      <div class="px-bar flex-1"><i style="width: {{ $p['percent'] }}%; background: {{ $p['accent'] }}"></i></div>
                      <span class="px-font flex-none text-[15px] font-semibold">{{ $p['percent'] }}%</span>
                    </div>
                    <p class="mt-2 text-[14px] text-muted">{{ $p['completed'] }}/{{ $p['total'] }} lessons · {{ $p['modulesDone'] }}/{{ $p['modulesTotal'] }} modules</p>

                    <ul class="mt-4 flex flex-wrap gap-1.5" aria-label="{{ $p['modulesDone'] }} of {{ $p['modulesTotal'] }} gems collected">
                      @for ($i = 0; $i < $p['modulesTotal']; $i++)
                        <li @class(['px-gem', 'is-off' => $i >= $p['modulesDone']]) aria-hidden="true"><x-px-icon name="gem" :size="22" /></li>
                      @endfor
                    </ul>
                  @else
                    <p class="mt-4 text-[14px] text-muted">Progress tracking isn't available yet.</p>
                  @endif

                  <a href="{{ route('student.planet', $p['slug']) }}" class="px-btn mt-6 w-fit">{{ $p['completed'] > 0 ? 'Continue' : 'Explore' }}</a>
                </div>
              </article>
            @endforeach
          </div>
        @endif
      </section>
    </div>

    {{-- ═══════════ RIGHT RAIL ═══════════ --}}
    <aside class="flex min-w-0 flex-col gap-8">

      {{-- Current learning --}}
      <section class="px-card p-6" aria-labelledby="cur-h">
        <h2 id="cur-h" class="px-font text-[1.4rem] font-semibold">Current learning</h2>
        @if ($current)
          <p class="mt-3 text-[12px] font-medium uppercase tracking-[0.16em] text-[#4fa8ff]">{{ $planets[$current['planet']]['name'] ?? '' }} · {{ $current['module'] }}</p>
          <p class="mt-1 text-[1.15rem] font-semibold leading-snug">{{ $current['title'] }}</p>
          <a href="{{ $current['url'] }}" class="px-btn px-btn-blue mt-5 w-[calc(100%-6px)]">Continue</a>
        @else
          <p class="mt-3 text-[15px] text-muted">
            {{ $lessonsDone > 0 ? 'No lessons left in the tracked courses.' : 'No lesson in progress yet.' }}
            <a href="{{ route('student.planets') }}" class="text-[#4fa8ff] underline-offset-4 hover:underline">Browse planets</a>
          </p>
        @endif
      </section>

      {{-- Crew leaderboard --}}
      <section class="px-card p-6" aria-labelledby="lb-h">
        <div class="flex items-end justify-between gap-3">
          <h2 id="lb-h" class="px-font min-w-0 text-[1.2rem] font-semibold leading-tight">{{ $crew ? $crew->name.' leaderboard' : 'Crew leaderboard' }}</h2>
          @if ($crew)<span class="flex-none text-[12px] text-muted">By XP</span>@endif
        </div>
        @if (! $crew)
          <p class="mt-4 text-[15px] text-muted">You haven't joined a crew yet. <a href="{{ route('student.courses') }}" class="text-[#4fa8ff] underline-offset-4 hover:underline">Join a course</a> to compete with your classmates.</p>
        @elseif (! count($leaderboard))
          <p class="mt-4 text-[15px] text-muted">No students in this crew yet.</p>
        @else
          <ol class="mt-5 flex max-h-[420px] flex-col gap-2 overflow-y-auto p-1">
            @foreach ($leaderboard as $row)
              <li @class(['px-inner flex items-center gap-3 px-3 py-2.5', 'px-me' => $row['me']]) @if ($row['me']) aria-current="true" @endif>
                <span class="px-font flex h-7 w-7 flex-none items-center justify-center text-[13px] font-bold {{ $row['rank'] <= 3 ? 'text-[#0b0b22]' : 'bg-[var(--px-track)] text-muted' }}"
                      @if ($row['rank'] <= 3) style="background: {{ $medal[$row['rank'] - 1] }}" @endif>{{ $row['rank'] }}</span>
                <span class="flex h-9 w-9 flex-none items-end justify-center overflow-hidden bg-[#1d1660] text-[12px] font-semibold text-white/80" aria-hidden="true">
                  @if (! empty($row['avatar']))<x-astronaut :look="$row['avatar']" crop="bust" class="h-10 w-10" />@else<span class="mb-2">{{ mb_strtoupper(mb_substr($row['name'], 0, 1)) }}</span>@endif
                </span>
                <span class="min-w-0 flex-1 truncate text-[14px] font-medium">{{ $row['name'] }}@if ($row['me']) <span class="text-muted">(you)</span>@endif</span>
                <span class="px-font flex-none text-[14px] font-semibold text-[#4fa8ff]">{{ number_format($row['xp']) }} XP</span>
              </li>
            @endforeach
          </ol>
        @endif
      </section>
    </aside>

  </div>
</x-pixel-app>
