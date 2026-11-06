@extends('layouts.admin-shell')
@section('title', 'Dashboard')

@php
  // Planet colours follow each planet's logo; green is kept for growth.
  $planetColors = ['programming' => '#9df58f', 'networking' => '#5be1ff', 'cybersecurity' => '#b59bff'];
  $planetLogos = ['programming' => 'programming-logo', 'networking' => 'networking-logo', 'cybersecurity' => 'cybersecurity-logo'];
  $s = $stats;
  $tiles = [
    ['label' => 'Students', 'n' => $s['students']['total'], 'week' => $s['students']['thisWeek'], 'prev' => $s['students']['lastWeek'], 'spark' => $s['students']['cumulative'], 'type' => 'line', 'unit' => 'joined', 'icon' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8a7 7 0 0 1 14 0'],
    ['label' => 'Faculty', 'n' => $s['faculty']['total'], 'week' => $s['faculty']['thisWeek'], 'prev' => $s['faculty']['lastWeek'], 'spark' => $s['faculty']['cumulative'], 'type' => 'line', 'unit' => 'joined', 'icon' => 'M3 9l9-5 9 5-9 5-9-5Zm4 2.5V16c0 1.5 2.5 3 5 3s5-1.5 5-3v-4.5'],
    ['label' => 'Lessons finished', 'n' => $s['lessons']['total'], 'week' => $s['lessons']['thisWeek'], 'prev' => $s['lessons']['lastWeek'], 'spark' => $s['lessons']['perWeek'], 'type' => 'bars', 'unit' => 'done', 'icon' => 'M5 12.5l4.5 4.5L19 7.5'],
    ['label' => 'Active this week', 'n' => $s['active'], 'week' => null, 'prev' => null, 'spark' => null, 'type' => 'ring', 'unit' => null, 'icon' => 'M13 3L5 13.5h6L10 21l8-10.5h-6L13 3Z'],
  ];
  $activePct = $s['students']['total'] ? min(100, round($s['active'] / $s['students']['total'] * 100)) : 0;
  $coverPct = $totalCourses ? round($assigned / $totalCourses * 100) : 0;
  $planetTotal = max(1, collect($s['boards'])->sum('count'));
  $chart = [
    'labels' => $s['labels'],
    'growth' => ['students' => $s['students']['cumulative'], 'faculty' => $s['faculty']['cumulative']],
    'learning' => collect($s['lessons']['byPlanet'])->map(fn ($data, $planet) => ['label' => $planetTitles[$planet] ?? ucfirst($planet), 'color' => $planetColors[$planet] ?? '#73b6ff', 'data' => $data])->values(),
  ];
@endphp

@push('head')
<style>
  .wrap { max-width: 1240px; }
  .dash-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px; }
  .dash-head h1 { margin: 18px 0 6px; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: clamp(2rem, 4.2vw, 2.9rem); line-height: 1.05; letter-spacing: -.035em; }
  .range-chip { font-family: 'Space Mono', monospace; font-size: .7rem; letter-spacing: .12em; text-transform: uppercase; color: var(--muted);
    padding: 8px 14px; border-radius: 999px; border: 1px solid var(--glass-border); background: var(--glass); white-space: nowrap; }

  .panel { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 22px; padding: 22px; backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); min-width: 0; }
  html[data-theme="light"] .panel { background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.04), 0 8px 24px -12px rgba(16,24,64,.12); }
  .panel h2 { font-family: 'Space Grotesk', sans-serif; font-size: 1.12rem; font-weight: 600; letter-spacing: -.01em; line-height: 1.25; }
  .panel .hint { color: var(--muted); font-size: .86rem; margin-top: 2px; }

  /* Stat tiles */
  .kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-top: 26px; }
  .kpi { position: relative; display: flex; flex-direction: column; gap: 14px; overflow: hidden; }
  .kpi-top { display: flex; align-items: center; gap: 10px; }
  .kpi-icon { width: 34px; height: 34px; border-radius: 11px; display: grid; place-items: center; flex: none; color: var(--blue); background: rgba(115,182,255,.14); }
  .kpi-label { font-family: 'Space Mono', monospace; font-size: .66rem; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); }
  .kpi-body { display: flex; align-items: flex-end; justify-content: space-between; gap: 10px; }
  .kpi-n { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 2.35rem; line-height: 1; letter-spacing: -.03em; font-variant-numeric: tabular-nums; }
  .delta { white-space: nowrap; display: inline-flex; align-items: center; gap: 5px; font-size: .78rem; font-weight: 600; padding: 4px 10px 4px 8px; border-radius: 999px; width: fit-content; font-variant-numeric: tabular-nums; }
  .delta.up { color: var(--green); background: color-mix(in srgb, var(--green) 14%, transparent); }
  .delta.flat { color: var(--muted); background: rgba(150,170,255,.1); }
  .delta small { font-weight: 500; opacity: .8; font-size: .74rem; }
  .kpi.is-up::after { content: ''; position: absolute; inset: auto -30% -60% auto; width: 160px; height: 160px; border-radius: 50%;
    background: radial-gradient(circle, color-mix(in srgb, var(--green) 22%, transparent), transparent 70%); pointer-events: none; }
  .ring { --p: 0; width: 56px; height: 56px; border-radius: 50%; flex: none; display: grid; place-items: center;
    background: conic-gradient(var(--green) calc(var(--p) * 1%), rgba(150,170,255,.16) 0); transition: --p 1s cubic-bezier(.22,1,.36,1); }
  .ring span { width: 44px; height: 44px; border-radius: 50%; display: grid; place-items: center; background: var(--void); font-size: .72rem; font-weight: 700; font-variant-numeric: tabular-nums; }
  html[data-theme="light"] .ring span { background: #fff; }
  @property --p { syntax: '<number>'; inherits: false; initial-value: 0; }

  .spark-line { stroke-dasharray: 1; stroke-dashoffset: 1; animation: draw 1.1s cubic-bezier(.22,1,.36,1) .15s forwards; }
  .spark-bar { transform-box: fill-box; transform-origin: bottom; transform: scaleY(0); animation: grow .6s cubic-bezier(.22,1,.36,1) forwards; animation-delay: calc(var(--i) * 35ms + .1s); }
  @keyframes draw { to { stroke-dashoffset: 0; } }
  @keyframes grow { to { transform: scaleY(1); } }

  /* Main chart + side column */
  .grid-main { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 14px; margin-top: 14px; }
  .chart-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 12px; }
  .seg { display: inline-flex; padding: 3px; border-radius: 12px; background: rgba(150,170,255,.1); border: 1px solid var(--glass-border); position: relative; }
  .seg button { position: relative; z-index: 1; font: 600 .82rem 'Inter', sans-serif; color: var(--muted); padding: 7px 14px; border-radius: 9px; border: 0; background: none; cursor: pointer; transition: color .2s; }
  .seg button[aria-pressed="true"] { color: var(--text); }
  .seg .thumb { position: absolute; top: 3px; bottom: 3px; border-radius: 9px; background: rgba(115,182,255,.22); box-shadow: 0 1px 2px rgba(0,0,0,.15);
    transition: left .35s cubic-bezier(.32,.72,0,1), width .35s cubic-bezier(.32,.72,0,1); }
  html[data-theme="light"] .seg .thumb { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.12); }
  .seg button:active { transform: scale(.97); }
  .legend { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 14px; font-size: .82rem; color: var(--muted); }
  .legend i { display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: 6px; vertical-align: -1px; }
  .chart-box { position: relative; height: 290px; margin-top: 10px; }
  .side { display: grid; gap: 14px; align-content: start; }

  .donut-row { display: flex; align-items: center; gap: 18px; margin-top: 14px; }
  .donut { width: 112px; height: 112px; flex: none; transform: rotate(-90deg); }
  .donut circle { fill: none; stroke-width: 4.5; }
  .donut .track { stroke: rgba(150,170,255,.16); }
  .donut .fill { stroke: var(--green); stroke-linecap: round; stroke-dasharray: 0 100; animation: donut 1.1s cubic-bezier(.22,1,.36,1) .2s forwards; }
  @keyframes donut { to { stroke-dasharray: var(--d) 100; } }
  .donut-wrap { position: relative; }
  .donut-wrap b { position: absolute; inset: 0; display: grid; place-items: center; font-family: 'Space Grotesk', sans-serif; font-size: 1.35rem; }
  .kv { display: grid; gap: 8px; font-size: .88rem; flex: 1; }
  .kv div { display: flex; justify-content: space-between; gap: 8px; }
  .kv span { color: var(--muted); }
  .kv strong { font-variant-numeric: tabular-nums; }
  .dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 7px; }

  .pbar { margin-top: 12px; }
  .pbar-top { display: flex; justify-content: space-between; align-items: center; font-size: .86rem; gap: 8px; }
  .pbar-top span { display: inline-flex; align-items: center; gap: 8px; min-width: 0; }
  .pbar-top strong { font-variant-numeric: tabular-nums; }
  .track-bar { height: 8px; border-radius: 99px; background: rgba(150,170,255,.14); margin-top: 7px; overflow: hidden; }
  .track-bar i { display: block; height: 100%; border-radius: inherit; transform-origin: left; transform: scaleX(0); animation: fillx .9s cubic-bezier(.22,1,.36,1) forwards; animation-delay: calc(var(--i, 0) * 80ms + .2s); }
  @keyframes fillx { to { transform: scaleX(var(--w)); } }

  /* Leaderboards */
  .sec-title { display: flex; flex-wrap: wrap; align-items: baseline; justify-content: space-between; gap: 12px; margin-top: 34px; }
  .sec-title h2 { font-family: 'Space Grotesk', sans-serif; font-size: 1.5rem; letter-spacing: -.02em; }
  .boards { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-top: 14px; }
  .board-head { display: flex; align-items: center; gap: 12px; padding-bottom: 14px; border-bottom: 1px solid var(--glass-border); }
  .board-head .logo { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; flex: none; background: #141a3f; box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--pc) 40%, transparent), 0 6px 16px -8px var(--pc); }
  .board-head .logo svg { width: 22px; height: 22px; }
  .board-list { list-style: none; display: grid; gap: 4px; margin-top: 10px; }
  .board-row { display: flex; align-items: center; gap: 10px; padding: 7px 8px; border-radius: 14px; opacity: 0; transform: translateY(6px);
    animation: rowin .5s cubic-bezier(.22,1,.36,1) forwards; animation-delay: calc(var(--i) * 60ms + .25s); }
  .board-row:hover { background: rgba(150,170,255,.08); }
  @keyframes rowin { to { opacity: 1; transform: none; } }
  .place { width: 26px; height: 26px; border-radius: 8px; display: grid; place-items: center; flex: none; font-family: 'Space Grotesk', sans-serif; font-size: .8rem; font-weight: 700;
    color: var(--muted); background: rgba(150,170,255,.1); font-variant-numeric: tabular-nums; }
  .place.medal { color: #0b0b22; background: var(--medal); box-shadow: 0 0 0 3px color-mix(in srgb, var(--medal) 22%, transparent); }
  .board-row .who { min-width: 0; flex: 1; }
  .board-row .who p { font-weight: 600; font-size: .92rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .board-row .who p.real-name { font-weight: 500; font-size: .78rem; color: var(--muted, #64748b); margin-bottom: 2px; }
  .board-row .xp { font-family: 'Space Mono', monospace; font-size: .74rem; color: var(--muted); white-space: nowrap; font-variant-numeric: tabular-nums; }
  .board-row .xp b { color: var(--text); font-family: 'Space Grotesk', sans-serif; font-size: .92rem; }
  .empty { display: grid; place-items: center; text-align: center; gap: 6px; padding: 30px 10px; color: var(--muted); font-size: .9rem; }

  /* Bottom row: cohorts + activity */
  .grid-bottom { display: grid; grid-template-columns: minmax(0, 3fr) minmax(0, 2fr); gap: 14px; margin-top: 14px; }
  .grid-bottom .cohort-wrap { position: relative; border: 0; background: none; margin-top: 10px; }
  .grid-bottom .cohort { min-width: 0; }
  .grid-bottom .cohort tbody th { white-space: normal; }
  .grid-bottom .cohort tbody th span { display: block; font-size: .76rem; line-height: 1.3; }
  .grid-bottom .cohort th, .grid-bottom .cohort td { padding: 10px 8px; }
  .cohort .bar-cell { width: 18%; min-width: 70px; }
  .cohort .bar-cell .track-bar { margin: 0; }
  .feed { list-style: none; margin-top: 14px; display: grid; }
  .feed li { position: relative; display: flex; gap: 12px; padding: 0 0 16px; }
  .feed li:not(:last-child)::before { content: ''; position: absolute; left: 15px; top: 34px; bottom: 4px; width: 2px; border-radius: 2px; background: var(--glass-border); }
  .feed .ic { width: 32px; height: 32px; border-radius: 10px; display: grid; place-items: center; flex: none; }
  .feed .ic.assign { color: var(--green); background: color-mix(in srgb, var(--green) 14%, transparent); }
  .feed .ic.reassign { color: var(--blue); background: rgba(115,182,255,.14); }
  .feed .ic.unassign { color: var(--red); background: color-mix(in srgb, var(--red) 14%, transparent); }
  .feed p { font-size: .88rem; line-height: 1.45; }
  .feed time { display: block; font-family: 'Space Mono', monospace; font-size: .66rem; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); margin-top: 3px; }
  .link-btn { display: inline-flex; align-items: center; gap: 6px; font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: .86rem; color: var(--blue); }
  .link-btn:hover { color: var(--cyan); }

  @media (max-width: 1100px) {
    .kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .grid-main, .grid-bottom { grid-template-columns: minmax(0, 1fr); }
    .side { grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); }
    .boards { grid-template-columns: minmax(0, 1fr); }
  }
  @media (max-width: 560px) {
    .kpis { grid-template-columns: minmax(0, 1fr); }
    .panel { padding: 18px; }
    .chart-box { height: 240px; }
  }
  @media (prefers-reduced-motion: reduce) {
    .spark-line { stroke-dashoffset: 0; }
    .spark-bar, .track-bar i { transform: none; }
    .track-bar i { transform: scaleX(var(--w)); }
    .donut .fill { stroke-dasharray: var(--d) 100; }
    .board-row { opacity: 1; transform: none; }
  }
</style>
@endpush

@section('content')
  <div class="dash-head">
    <div>
      <span class="eyebrow">Admin · Overview</span>
      <h1>Command <span class="accent">deck</span></h1>
      <p class="sub">How TechLab is growing: who joined, what they're learning and who's leading each planet.</p>
    </div>
    <span class="range-chip">Last {{ \App\Services\AdminAnalyticsService::WEEKS }} weeks</span>
  </div>

  {{-- Stat tiles: the number, how many joined/finished this week (green when it grew) and a mini chart --}}
  <div class="kpis">
    @foreach ($tiles as $t)
      @php $up = $t['week'] > 0; @endphp
      <section @class(['panel kpi', 'is-up' => $up || ($t['type'] === 'ring' && $t['n'] > 0)])>
        <div class="kpi-top">
          <span class="kpi-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $t['icon'] }}"/></svg></span>
          <span class="kpi-label">{{ $t['label'] }}</span>
        </div>
        <div class="kpi-body">
          <div class="kpi-n" x-data="countUp({{ (int) $t['n'] }})" x-text="shown">{{ number_format($t['n']) }}</div>
          @if ($t['type'] === 'ring')
            <div class="ring" x-data x-init="requestAnimationFrame(() => $el.style.setProperty('--p', {{ $activePct }}))" title="{{ $activePct }}% of students studied in the last 7 days">
              <span>{{ $activePct }}%</span>
            </div>
          @else
            <x-sparkline :values="$t['spark']" :type="$t['type']" :color="$up ? 'var(--green)' : 'var(--blue)'" />
          @endif
        </div>
        @if ($t['type'] === 'ring')
          <span @class(['delta', 'up' => $t['n'] > 0, 'flat' => ! $t['n']])>of {{ number_format($s['students']['total']) }} students studied in 7 days</span>
        @elseif ($up)
          <span class="delta up">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
            +{{ number_format($t['week']) }} {{ $t['unit'] }} this week
          </span>
        @else
          <span class="delta flat">No new {{ $t['unit'] === 'done' ? 'lessons' : 'sign-ups' }} this week</span>
        @endif
      </section>
    @endforeach
  </div>

  {{-- The big chart (growth or learning) and the side column --}}
  <div class="grid-main">
    <section class="panel" x-data="dashChart(@js($chart))" x-init="init()">
      <div class="chart-head">
        <div>
          <h2 x-text="tab === 'growth' ? 'Community growth' : 'Lessons finished per planet'">Community growth</h2>
          <p class="hint" x-text="tab === 'growth' ? 'Total students and faculty, adding up week by week.' : 'Lessons completed each week on every planet.'">Total students and faculty, adding up week by week.</p>
        </div>
        <div class="seg" role="group" aria-label="Chart" x-ref="seg">
          <span class="thumb" :style="thumb"></span>
          <button type="button" x-ref="growth" :aria-pressed="tab === 'growth'" @click="show('growth')">Growth</button>
          <button type="button" x-ref="learning" :aria-pressed="tab === 'learning'" @click="show('learning')">Learning</button>
        </div>
      </div>
      <div class="legend">
        <template x-for="item in legend" :key="item.label">
          <span><i :style="{ background: item.color }"></i><span x-text="item.label"></span></span>
        </template>
      </div>
      <div class="chart-box"><canvas x-ref="canvas" role="img" aria-label="Weekly chart of TechLab growth and learning"></canvas></div>
    </section>

    <div class="side">
      <section class="panel">
        <h2>Course coverage</h2>
        <p class="hint">Courses with at least one faculty member.</p>
        <div class="donut-row">
          <div class="donut-wrap">
            <svg class="donut" viewBox="0 0 42 42" aria-hidden="true">
              <circle class="track" cx="21" cy="21" r="15.915" />
              @if ($coverPct)<circle class="fill" cx="21" cy="21" r="15.915" pathLength="100" style="--d: {{ $coverPct }}" />@endif
            </svg>
            <b>{{ $coverPct }}%</b>
          </div>
          <div class="kv">
            <div><span><i class="dot" style="background: var(--green)"></i>Assigned</span><strong>{{ $assigned }}</strong></div>
            <div><span><i class="dot" style="background: rgba(150,170,255,.35)"></i>Unassigned</span><strong>{{ $unassigned }}</strong></div>
            <div><span>Total courses</span><strong>{{ $totalCourses }}</strong></div>
          </div>
        </div>
      </section>

      <section class="panel">
        <h2>Students per planet</h2>
        <p class="hint">Students on at least one course there.</p>
        @foreach ($s['boards'] as $planet => $board)
          <div class="pbar">
            <div class="pbar-top">
              <span><i class="dot" style="background: {{ $planetColors[$planet] ?? 'var(--blue)' }}"></i>{{ $planetTitles[$planet] ?? ucfirst($planet) }}</span>
              <strong>{{ $board['count'] }}</strong>
            </div>
            <div class="track-bar"><i style="--w: {{ round($board['count'] / $planetTotal, 3) }}; --i: {{ $loop->index }}; background: {{ $planetColors[$planet] ?? 'var(--blue)' }}"></i></div>
          </div>
        @endforeach
      </section>
    </div>
  </div>

  {{-- One leaderboard per planet: the top five, ranked like the students' own boards --}}
  <div class="sec-title">
    <h2>Planet leaderboards</h2>
    <span class="range-chip">Top 5 · by level</span>
  </div>
  <div class="boards">
    @foreach ($s['boards'] as $planet => $board)
      <section class="panel" style="--pc: {{ $planetColors[$planet] ?? '#73b6ff' }}">
        <div class="board-head">
          <span class="logo">@if (isset($planetLogos[$planet]))<x-dynamic-component :component="$planetLogos[$planet]" />@endif</span>
          <div class="min-w-0">
            <h2>{{ $planetTitles[$planet] ?? ucfirst($planet) }}</h2>
            <p class="hint">{{ $board['count'] }} {{ \Illuminate\Support\Str::plural('astronaut', $board['count']) }}</p>
          </div>
        </div>
        @if ($board['rows'])
          <ol class="board-list">
            @foreach (array_slice($board['rows'], 0, 5) as $i => $row)
              @php $medal = $row['rank'] <= 3 ? ['#f5c04a', '#c4ccdd', '#d99562'][$row['rank'] - 1] : null; @endphp
              <li class="board-row" style="--i: {{ $i }}">
                <span @class(['place', 'medal' => $medal]) @if ($medal) style="--medal: {{ $medal }}" @endif>{{ $row['rank'] }}</span>
                <x-avatar-frame :frame="$row['frame']" :level="$row['level']" :look="$row['avatar']" :name="$row['name']" :size="40" :plaque="false" />
                <div class="who">
                  <p>{{ $row['name'] }}</p>
                  @if ($row['real_name'] !== $row['name'])
                    <p class="real-name">{{ $row['real_name'] }}</p>
                  @endif
                  <x-rank-badge :level="$row['level']" size="sm" />
                </div>
                <span class="xp"><b>{{ number_format($row['xp']) }}</b> XP</span>
              </li>
            @endforeach
          </ol>
        @else
          <div class="empty">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4ZM7 6H4v1a3 3 0 0 0 3 3M17 6h3v1a3 3 0 0 1-3 3"/></svg>
            No astronauts here yet.
          </div>
        @endif
      </section>
    @endforeach
  </div>

  {{-- Students by program and year level (picked at sign-up, changeable in Settings), and recent admin actions --}}
  @php
    $showYears = array_filter($years, fn ($label, $y) => $y !== 0 || collect($grid)->contains(fn ($r) => isset($r[0])), ARRAY_FILTER_USE_BOTH);
    $showPrograms = array_filter($programs, fn ($title, $code) => $code !== '' || isset($grid['']), ARRAY_FILTER_USE_BOTH);
    $maxRow = max(1, ...array_map(fn ($code) => array_sum($grid[$code] ?? []), array_keys($showPrograms)));
  @endphp
  <div class="grid-bottom">
    <section class="panel">
      <h2>Students by program and year</h2>
      <p class="hint">What each student picked at sign-up.</p>
      <div class="cohort-wrap">
        <table class="cohort">
          <thead>
            <tr>
              <th scope="col">Program</th>
              @foreach ($showYears as $label)<th scope="col">{{ $label }}</th>@endforeach
              <th scope="col">Total</th>
              <th scope="col" class="bar-cell"><span class="sr-only">Share</span></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($showPrograms as $code => $title)
              @php $row = $grid[$code] ?? []; @endphp
              <tr>
                <th scope="row">@if ($code !== '')<b>{{ $code }}</b> <span>{{ $title }}</span>@else<span>{{ $title }}</span>@endif</th>
                @foreach ($showYears as $y => $label)<td @class(['zero' => empty($row[$y])])>{{ $row[$y] ?? 0 }}</td>@endforeach
                <td class="sum">{{ array_sum($row) }}</td>
                <td class="bar-cell"><div class="track-bar"><i style="--w: {{ round(array_sum($row) / $maxRow, 3) }}; --i: {{ $loop->index }}; background: linear-gradient(90deg, var(--blue), var(--cyan))"></i></div></td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th scope="row">Total</th>
              @foreach ($showYears as $y => $label)<td class="sum">{{ collect($grid)->sum(fn ($r) => $r[$y] ?? 0) }}</td>@endforeach
              <td class="sum">{{ $studentCount }}</td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </section>

    <section class="panel">
      <div class="chart-head">
        <h2>Recent activity</h2>
        <a class="link-btn" href="{{ route('admin.home') }}">Course Assignment <span aria-hidden="true">→</span></a>
      </div>
      @if ($activity->isEmpty())
        <p class="hint" style="margin-top:12px">No assignments made yet.</p>
      @else
        <ol class="feed">
          @foreach ($activity as $entry)
            @php $kind = ['course.reassign' => 'reassign', 'course.unassign' => 'unassign'][$entry['action']] ?? 'assign'; @endphp
            <li>
              <span class="ic {{ $kind }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="{{ ['assign' => 'M12 5v14M5 12h14', 'reassign' => 'M17 2l4 4-4 4M3 11V9a3 3 0 0 1 3-3h15M7 22l-4-4 4-4M21 13v2a3 3 0 0 1-3 3H3', 'unassign' => 'M5 12h14'][$kind] }}"/>
                </svg>
              </span>
              <div><p>{{ $entry['text'] }}</p><time>{{ $entry['when'] }}</time></div>
            </li>
          @endforeach
        </ol>
      @endif
    </section>
  </div>
@endsection

@push('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
  const reduceMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Numbers roll up from 0 once, quickly; skipped when the user asks for less motion.
  function countUp(target) {
    return {
      shown: target.toLocaleString(),
      init() {
        if (reduceMotion() || target === 0) return;
        const t0 = performance.now(), dur = 700;
        const step = (now) => {
          const k = Math.min(1, (now - t0) / dur), eased = 1 - Math.pow(1 - k, 3);
          this.shown = Math.round(target * eased).toLocaleString();
          if (k < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
      },
    };
  }

  // The big chart: "Growth" (running totals) or "Learning" (lessons per planet). Colours come from the theme and follow a theme switch.
  function dashChart(data) {
    let chart = null;
    return {
      tab: 'growth',
      thumb: {},
      legend: [],
      init() {
        this.$nextTick(() => { this.moveThumb(); this.draw(); });
        new MutationObserver(() => this.draw()).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
        window.addEventListener('resize', () => this.moveThumb());
      },
      show(tab) { this.tab = tab; this.moveThumb(); this.draw(); },
      moveThumb() {
        const b = this.$refs[this.tab];
        this.thumb = { left: b.offsetLeft + 'px', width: b.offsetWidth + 'px' };
      },
      css(name) { return getComputedStyle(document.documentElement).getPropertyValue(name).trim(); },
      sets() {
        const green = this.css('--green'), blue = this.css('--blue');
        if (this.tab === 'growth') {
          return [
            { label: 'Students', data: data.growth.students, color: green, fill: true },
            { label: 'Faculty', data: data.growth.faculty, color: blue, fill: false },
          ];
        }
        return data.learning.map(p => ({ label: p.label, data: p.data, color: p.color, fill: false }));
      },
      draw() {
        if (!window.Chart) return;
        const sets = this.sets(), ctx = this.$refs.canvas.getContext('2d');
        const muted = this.css('--muted'), grid = this.css('--glass-border') || 'rgba(150,170,255,.18)';
        this.legend = sets.map(s => ({ label: s.label, color: s.color }));
        const datasets = sets.map(s => {
          const g = ctx.createLinearGradient(0, 0, 0, 290);
          g.addColorStop(0, s.color + '55'); g.addColorStop(1, s.color + '00');
          return {
            label: s.label, data: s.data, borderColor: s.color, backgroundColor: s.fill ? g : s.color,
            fill: s.fill, tension: .38, borderWidth: 2.5, pointRadius: 0, pointHoverRadius: 5,
            pointBackgroundColor: s.color, pointBorderColor: 'transparent',
          };
        });
        chart?.destroy();
        chart = new Chart(ctx, {
          type: 'line',
          data: { labels: data.labels, datasets },
          options: {
            responsive: true, maintainAspectRatio: false,
            animation: reduceMotion() ? false : { duration: 700, easing: 'easeOutQuart' },
            interaction: { mode: 'index', intersect: false },
            plugins: {
              legend: { display: false },
              tooltip: { padding: 10, cornerRadius: 10, boxPadding: 4, usePointStyle: true, titleFont: { family: 'Space Grotesk', weight: '600' }, bodyFont: { family: 'Inter' } },
            },
            scales: {
              x: { grid: { display: false }, border: { display: false }, ticks: { color: muted, font: { family: 'Space Mono', size: 10 }, maxRotation: 0, autoSkipPadding: 14 } },
              y: { beginAtZero: true, grid: { color: grid }, border: { display: false }, ticks: { color: muted, precision: 0, font: { family: 'Space Mono', size: 10 }, maxTicksLimit: 5 } },
            },
          },
        });
      },
    };
  }
</script>
@endpush
