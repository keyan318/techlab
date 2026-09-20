@php
  $first     = explode(' ', trim($user->name))[0] ?: 'Explorer';
  $parts     = preg_split('/\s+/', trim($user->name), -1, PREG_SPLIT_NO_EMPTY);
  $initials  = mb_strtoupper(count($parts) >= 2 ? mb_substr($parts[0], 0, 1).mb_substr(end($parts), 0, 1) : mb_substr($user->name, 0, 2));
  $fmt       = fn (int $m) => $m >= 60 ? intdiv($m, 60).'h '.str_pad($m % 60, 2, '0', STR_PAD_LEFT).'m' : $m.'m';
  $axisMins  = $weekly['axisMax'] * 60;
  $perf      = $performance;
  $medal     = ['#f5c04a', '#c4ccdd', '#d99562'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard · TechLab</title>
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
    .space { position: fixed; inset: 0; z-index: -1; overflow: hidden;
      background: radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%),
                  radial-gradient(100% 80% at 85% 110%, #1a0f4d 0%, rgba(26,15,77,0) 60%),
                  linear-gradient(160deg, #0a0826 0%, #120a33 45%, #1e1259 100%); }
    .card { background: rgba(123,142,220,0.07); border: 1px solid rgba(150,170,255,0.16); border-radius: 22px; backdrop-filter: blur(12px); }
    .card-inner { background: rgba(255,255,255,0.035); border: 1px solid rgba(150,170,255,0.10); border-radius: 16px; }
    .h-display { font-family: 'Space Grotesk', sans-serif; letter-spacing: -0.02em; }
    .btn-solid { background: linear-gradient(100deg, #5be1ff, #73b6ff 55%, #9b6bff); color: #07142e; }
    .ring-track { stroke: rgba(150,170,255,0.16); }
    .bar-fill { transition: height .6s cubic-bezier(.32,.72,0,1); }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
    .rise { animation: fadeUp .55s ease both; }
    @media (prefers-reduced-motion: reduce) { .rise { animation: none; } .bar-fill { transition: none; } }
  </style>
</head>

<body class="bg-void font-sans text-ink antialiased" x-data="techlabShell()" x-init="init()">

  <div class="space" aria-hidden="true"></div>

  <div class="flex h-screen w-screen overflow-hidden">

    @include('components.shell.side-bar')

    <main class="min-w-0 flex-1 overflow-y-auto">
      @php $me = collect($leaderboard)->firstWhere('me', true); @endphp
      <div class="mx-auto grid max-w-[1500px] gap-4 px-4 py-5 sm:px-6 lg:py-6 xl:grid-cols-[minmax(0,1fr)_340px]">

        {{-- ═══════════ CENTER ═══════════ --}}
        <div class="flex min-w-0 flex-col gap-4">

          {{-- Greeting + overall --}}
          <header class="rise flex flex-wrap items-end justify-between gap-x-6 gap-y-2">
            <div>
              <h1 class="h-display text-[clamp(1.5rem,2.6vw,2rem)] font-bold leading-tight">Hello, <span class="font-normal text-blue">{{ $first }}</span> 👋</h1>
              <p class="text-[14px] text-muted">
                @if($current) Ready for your next mission? Pick up where you left off.
                @elseif($lessonsDone > 0) You've cleared every tracked lesson. Nice work, astronaut.
                @else Welcome aboard. Complete your first lesson to start earning XP. @endif
              </p>
            </div>
            <div class="flex min-w-[220px] items-center gap-3" role="progressbar" aria-valuenow="{{ $overall }}" aria-valuemin="0" aria-valuemax="100" aria-label="Overall progress">
              <div class="h-2 flex-1 overflow-hidden rounded-full bg-white/10"><div class="btn-solid h-full rounded-full" style="width: {{ $overall }}%"></div></div>
              <span class="text-[13px] text-muted"><b class="text-ink">{{ $overall }}%</b> overall</span>
            </div>
          </header>

          {{-- Overview --}}
          <section aria-label="Overview" class="rise grid grid-cols-2 gap-3 lg:grid-cols-4" style="animation-delay:.04s">
            @php
              $stats = [
                ['Total XP', number_format($xp), 'XP', '#73b6ff', 'from-[rgba(115,182,255,.16)]', min(100, $overall).'%'],
                ['Lessons done', $lessonsDone, '/'.$lessonsTotal, '#5be1ff', 'from-[rgba(91,225,255,.14)]', ($lessonsTotal ? $lessonsDone / $lessonsTotal * 100 : 0).'%'],
                ['Modules done', $modulesDone, '/'.$modulesTotal, '#9b6bff', 'from-[rgba(155,107,255,.18)]', ($modulesTotal ? $modulesDone / $modulesTotal * 100 : 0).'%'],
                ['Crew rank', $me ? '#'.$me['rank'] : '—', $me ? 'of '.count($leaderboard) : ($crew ? '' : 'no crew'), '#5fe0a0', 'from-[rgba(95,224,160,.14)]', $me && count($leaderboard) ? (100 - ($me['rank'] - 1) / max(1, count($leaderboard)) * 100).'%' : '0%'],
              ];
            @endphp
            @foreach($stats as [$label, $value, $suffix, $color, $tint, $bar])
              <div class="card bg-gradient-to-br {{ $tint }} to-transparent px-4 pb-3 pt-3.5">
                <p class="text-[12px] font-medium text-muted">{{ $label }}</p>
                <p class="h-display mt-1 text-[1.9rem] font-bold leading-none" style="color: {{ $color }}">{{ $value }}<span class="ml-1 text-[0.8rem] font-medium text-muted">{{ $suffix }}</span></p>
                <div class="mt-3 h-[3px] w-full rounded-full bg-white/10"><div class="h-full rounded-full" style="width: {{ $bar }}; background: {{ $color }}"></div></div>
              </div>
            @endforeach
          </section>

          {{-- Charts --}}
          <div class="grid gap-4 lg:grid-cols-2">
            {{-- Active hours --}}
            <section class="card rise p-4" style="animation-delay:.08s" aria-labelledby="ah-h">
              <div class="flex items-center justify-between">
                <h2 id="ah-h" class="h-display text-[1.02rem] font-semibold">Active hours</h2>
                <span class="rounded-full bg-white/[0.06] px-2.5 py-0.5 text-[11px] text-muted">This week</span>
              </div>
              <div class="mt-3 flex gap-2">
                <div class="flex h-[170px] flex-col justify-between pb-5 text-right text-[10px] text-muted" aria-hidden="true">
                  <span>{{ $weekly['axisMax'] }}h</span><span>0</span>
                </div>
                <div class="grid flex-1 grid-cols-7 gap-1.5" role="img"
                     aria-label="Active hours per day this week: {{ collect($weekly['days'])->map(fn ($d) => $d['name'].' '.$fmt($d['minutes']))->implode(', ') }}">
                  @foreach($weekly['days'] as $d)
                    <div class="flex flex-col items-center gap-1.5">
                      <div class="relative h-[150px] w-full max-w-[24px] overflow-hidden rounded-full bg-white/[0.06]" title="{{ $d['name'] }}: {{ $fmt($d['minutes']) }}">
                        <div class="bar-fill absolute inset-x-0 bottom-0 rounded-full bg-gradient-to-t from-violet to-blue" style="height: {{ min(100, $d['minutes'] / $axisMins * 100) }}%"></div>
                      </div>
                      <span class="text-[10px] {{ $d['today'] ? 'font-semibold text-blue' : 'text-muted' }}">{{ $d['label'] }}</span>
                    </div>
                  @endforeach
                </div>
              </div>
              <dl class="mt-3 grid grid-cols-3 gap-2 border-t border-white/[0.07] pt-3">
                <div><dt class="text-[11px] text-muted">Time spent</dt><dd class="h-display text-[1.1rem] font-bold">{{ $fmt($weekly['totalMinutes']) }}</dd></div>
                <div><dt class="text-[11px] text-muted">Lessons taken</dt><dd class="h-display text-[1.1rem] font-bold">{{ $weekly['lessonsWeek'] }}</dd></div>
                <div><dt class="text-[11px] text-muted">Modules done</dt><dd class="h-display text-[1.1rem] font-bold">{{ $modulesDone }}<span class="text-[0.75rem] font-medium text-muted">/{{ $modulesTotal }}</span></dd></div>
              </dl>
            </section>

            {{-- Performance --}}
            <section class="card rise p-4" style="animation-delay:.12s" aria-labelledby="pf-h">
              <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 id="pf-h" class="h-display text-[1.02rem] font-semibold">Performance</h2>
                <div class="flex gap-3 text-[11px] text-muted">
                  <span class="flex items-center gap-1.5"><i class="h-2 w-2 rounded-full bg-violet"></i>XP</span>
                  <span class="flex items-center gap-1.5"><i class="h-2 w-2 rounded-full bg-cyan"></i>Active time</span>
                </div>
              </div>
              <div class="relative mt-3">
                <svg viewBox="0 0 {{ $perf['w'] }} {{ $perf['h'] }}" preserveAspectRatio="none" class="h-[150px] w-full overflow-visible" role="img"
                     aria-label="XP earned and active time per week over the last {{ count($perf['labels']) }} weeks">
                  <defs><linearGradient id="xpfill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#9b6bff" stop-opacity=".35"/><stop offset="1" stop-color="#9b6bff" stop-opacity="0"/></linearGradient></defs>
                  @for($g = 0; $g <= 2; $g++)
                    <line x1="0" x2="{{ $perf['w'] }}" y1="{{ 8 + $g * (($perf['h'] - 16) / 2) }}" y2="{{ 8 + $g * (($perf['h'] - 16) / 2) }}" stroke="rgba(150,170,255,.10)" vector-effect="non-scaling-stroke"/>
                  @endfor
                  @if($perf['hasData'])
                    <path d="{{ $perf['xpPath'] }} L{{ $perf['w'] }} {{ $perf['h'] }} L0 {{ $perf['h'] }} Z" fill="url(#xpfill)"/>
                    <path d="{{ $perf['minPath'] }}" fill="none" stroke="#5be1ff" stroke-width="2" stroke-linecap="round" vector-effect="non-scaling-stroke" opacity=".85"/>
                    <path d="{{ $perf['xpPath'] }}" fill="none" stroke="#9b6bff" stroke-width="2.5" stroke-linecap="round" vector-effect="non-scaling-stroke"/>
                  @endif
                </svg>
                @unless($perf['hasData'])
                  <p class="absolute inset-0 flex items-center justify-center text-[12px] text-muted">Your trend will appear after your first lesson.</p>
                @endunless
              </div>
              <div class="mt-1 flex justify-between text-[10px] text-muted" aria-hidden="true">
                @foreach($perf['labels'] as $i => $label)
                  <span class="{{ $i === count($perf['labels']) - 1 ? 'font-semibold text-blue' : '' }} {{ $i % 2 ? 'hidden sm:inline' : '' }}">{{ $label }}</span>
                @endforeach
              </div>
              <div class="mt-3 flex items-center gap-3 border-t border-white/[0.07] pt-3">
                @if($perf['changePct'] !== null)
                  <p class="h-display text-[1.3rem] font-bold {{ $perf['changePct'] >= 0 ? 'text-[#5fe0a0]' : 'text-[#ff8f8f]' }}">{{ $perf['changePct'] >= 0 ? '+' : '' }}{{ $perf['changePct'] }}%</p>
                  <p class="text-[12px] leading-snug text-muted">XP this week ({{ $perf['thisWeekXp'] }}) vs last week ({{ $perf['lastWeekXp'] }}).</p>
                @elseif($perf['thisWeekXp'] > 0)
                  <p class="h-display text-[1.3rem] font-bold text-[#5fe0a0]">{{ $perf['thisWeekXp'] }} XP</p>
                  <p class="text-[12px] leading-snug text-muted">earned this week — nothing to compare against last week yet.</p>
                @else
                  <p class="text-[12px] text-muted">No XP earned this week yet. Complete a lesson to get moving.</p>
                @endif
              </div>
            </section>
          </div>

          {{-- Planets --}}
          <section class="rise" style="animation-delay:.16s" aria-labelledby="pl-h">
            <h2 id="pl-h" class="h-display mb-2 text-[1.02rem] font-semibold">Your planets</h2>
            <div class="grid gap-3 md:grid-cols-3">
              @foreach($planets as $p)
                <div class="card flex items-center gap-3 p-3.5">
                  <div class="relative h-[52px] w-[52px] flex-none">
                    <svg viewBox="0 0 36 36" class="h-full w-full -rotate-90" aria-hidden="true">
                      <circle class="ring-track" cx="18" cy="18" r="15.9155" fill="none" stroke-width="3.6"/>
                      @if($p['percent'] > 0)
                      <circle cx="18" cy="18" r="15.9155" fill="none" stroke-width="3.6" stroke-linecap="round"
                              stroke="{{ $p['accent'] }}" pathLength="100" stroke-dasharray="{{ $p['percent'] }} 100"/>
                      @endif
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-[11.5px] font-semibold">{{ $p['tracked'] ? $p['percent'].'%' : '—' }}</span>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="h-display text-[0.95rem] font-semibold">{{ $p['name'] }}</p>
                    <p class="text-[11.5px] leading-snug text-muted">
                      @if($p['tracked']) {{ $p['completed'] }}/{{ $p['total'] }} lessons · {{ $p['modulesDone'] }}/{{ $p['modulesTotal'] }} modules
                      @else Progress tracking isn't available yet. @endif
                    </p>
                    <a href="{{ route('student.planet', $p['slug']) }}" class="mt-1 inline-block text-[12px] font-medium text-blue hover:underline">{{ $p['completed'] > 0 ? 'Continue →' : 'Explore →' }}</a>
                  </div>
                </div>
              @endforeach
            </div>
          </section>
        </div>

        {{-- ═══════════ RIGHT RAIL ═══════════ --}}
        <aside class="flex min-w-0 flex-col gap-4 xl:min-h-0">

          {{-- Profile + crew --}}
          <section class="card rise overflow-hidden" style="animation-delay:.06s" aria-label="Profile">
            <div class="h-14" style="background: radial-gradient(circle at 20% 30%, rgba(155,107,255,.75), transparent 55%), radial-gradient(circle at 80% 70%, rgba(91,225,255,.6), transparent 55%), #1a1150;"></div>
            <div class="flex items-center gap-3 px-4 pb-4">
              <div class="-mt-6 flex h-14 w-14 flex-none items-center justify-center rounded-2xl border-4 border-[#120a33] bg-gradient-to-br from-blue to-violet text-[18px] font-bold text-[#06061a]" aria-hidden="true">{{ $initials }}</div>
              <div class="min-w-0 pt-2">
                <p class="h-display truncate text-[1.02rem] font-semibold">{{ $user->name }}</p>
                <p class="truncate text-[12px] text-muted">Astronaut · {{ $crew ? $crew->name : 'No crew yet' }}</p>
              </div>
            </div>
            @if($crew)
              <p class="border-t border-white/[0.07] px-4 py-2 text-[12px] text-muted">{{ count($leaderboard) }} {{ \Illuminate\Support\Str::plural('student', count($leaderboard)) }} in your crew{{ $me ? " · you're #".$me['rank'] : '' }}</p>
            @endif
          </section>

          {{-- Current learning --}}
          <section class="card rise p-4" style="animation-delay:.1s" aria-labelledby="cur-h">
            <h2 id="cur-h" class="h-display text-[1.02rem] font-semibold">Current learning</h2>
            @if($current)
              <p class="mt-2 truncate text-[11px] font-medium uppercase tracking-[0.08em] text-blue">Programming · {{ $current['module'] }}</p>
              <div class="mt-1 flex items-center justify-between gap-3">
                <p class="h-display min-w-0 truncate text-[1.05rem] font-semibold">{{ $current['title'] }}</p>
                <a href="{{ $current['url'] }}" class="btn-solid flex-none rounded-full px-4 py-2 text-[13px] font-semibold transition hover:brightness-110">Continue</a>
              </div>
            @else
              <p class="mt-2 text-[13px] text-muted">
                {{ $lessonsDone > 0 ? 'No lessons left in the tracked courses.' : 'No lesson in progress yet.' }}
                <a href="{{ route('student.planets') }}" class="text-blue underline-offset-4 hover:underline">Browse planets</a>
              </p>
            @endif
          </section>

          {{-- Crew leaderboard --}}
          <section class="card rise flex min-h-0 flex-1 flex-col p-4" style="animation-delay:.14s" aria-labelledby="lb-h">
            <div class="flex items-end justify-between gap-3">
              <h2 id="lb-h" class="h-display truncate text-[1.02rem] font-semibold">{{ $crew ? $crew->name.' leaderboard' : 'Crew leaderboard' }}</h2>
              @if($crew)<span class="flex-none text-[11px] text-muted">Ranked by XP</span>@endif
            </div>
            @if(! $crew)
              <p class="mt-3 text-[13px] text-muted">You haven't joined a crew yet. <a href="{{ route('student.crew') }}" class="text-blue underline-offset-4 hover:underline">Join with a crew code</a> to compete with your classmates.</p>
            @elseif(! count($leaderboard))
              <p class="mt-3 text-[13px] text-muted">No students in this crew yet.</p>
            @else
              <ol class="mt-3 flex max-h-[260px] flex-col gap-1.5 overflow-y-auto pr-0.5 xl:max-h-none">
                @foreach($leaderboard as $row)
                  <li class="flex items-center gap-2.5 rounded-[12px] px-2.5 py-2 {{ $row['me'] ? 'bg-[rgba(115,182,255,0.14)] ring-1 ring-[rgba(115,182,255,0.4)]' : 'bg-white/[0.03]' }}"
                      @if($row['me']) aria-current="true" @endif>
                    <span class="flex h-6 w-6 flex-none items-center justify-center rounded-full text-[11px] font-bold {{ $row['rank'] <= 3 ? 'text-[#0b0b22]' : 'bg-white/10 text-muted' }}"
                          @if($row['rank'] <= 3) style="background: {{ $medal[$row['rank'] - 1] }}" @endif>{{ $row['rank'] }}</span>
                    <span class="min-w-0 flex-1 truncate text-[13px] font-medium">{{ $row['name'] }}@if($row['me']) <span class="text-muted">(you)</span>@endif</span>
                    <span class="h-display text-[13px] font-semibold text-blue">{{ number_format($row['xp']) }} XP</span>
                  </li>
                @endforeach
              </ol>
            @endif
          </section>
        </aside>

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
