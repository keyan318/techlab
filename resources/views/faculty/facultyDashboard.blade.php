@extends('layouts.faculty-shell')
@section('title', "Captain's Bridge")

@push('head')
  <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@500;600&display=swap" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
  <style>
    /* Astro greets the captain, the same way he greets students on their home page. */
    .fd-hello { display: flex; align-items: center; gap: 24px; margin-bottom: 28px; }
    .fd-hello .px-mascot { flex: none; width: 80px; height: 80px; }
    .fd-bubble { position: relative; flex: 1; min-width: 0; margin: 0; padding: 18px 24px; border-radius: 16px; background: var(--panel-solid); border: 2px solid var(--glass-border); font-family: 'Pixelify Sans', 'Space Grotesk', sans-serif; font-weight: 500; font-size: clamp(1rem, 2vw, 1.35rem); line-height: 1.4; }
    html[data-theme="light"] .fd-bubble { background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.05); }
    /* The tail points left at Astro. */
    .fd-bubble::before { content: ""; position: absolute; left: -12px; top: 50%; margin-top: -9px; border: 9px solid transparent; border-right: 11px solid var(--glass-border); border-left: 0; }
    .fd-bubble::after { content: ""; position: absolute; left: -8px; top: 50%; margin-top: -7px; border: 7px solid transparent; border-right: 9px solid var(--panel-solid); border-left: 0; }
    html[data-theme="light"] .fd-bubble::after { border-right-color: #fff; }
    .fd-bubble small { display: block; margin-top: 4px; font-family: 'Inter', sans-serif; font-size: .9rem; color: var(--muted); }
    @media (max-width: 640px) { .fd-hello { gap: 16px; } .fd-hello .px-mascot { width: 56px; height: 56px; } .fd-bubble { padding: 14px 18px; } }

    /* The dashboard needs more room than the shell's 860px reading column. */
    .wrap:has(.fdash) { max-width: 1180px; }
    .fdash { text-align: left; }
    .fdash h1 { margin: 0 0 6px; font-size: clamp(1.8rem, 4vw, 2.6rem); }
    .fdash .sub { margin: 0; max-width: 62ch; }

    .fd-card { background: var(--px-bg); border-radius: 4px; padding: 20px 22px; -webkit-backdrop-filter: blur(12px); backdrop-filter: blur(12px);
      box-shadow: 0 -3px 0 0 var(--px-line), 0 3px 0 0 var(--px-line), -3px 0 0 0 var(--px-line), 3px 0 0 0 var(--px-line); }
    .fd-card h2 { font-family: 'Pixelify Sans', 'Space Grotesk', sans-serif; font-size: 1.2rem; font-weight: 700; letter-spacing: -.01em; }
    .fd-card .hint { color: var(--muted); font-size: .85rem; }
    .fd-grid { display: grid; gap: 16px; margin-top: 16px; }
    @media (min-width: 900px) { .fd-grid.two { grid-template-columns: 1fr 1fr; } }

    /* Filters: one row above everything they scope. */
    .fd-filters { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-top: 22px; }
    .fd-pill { font-family: 'Pixelify Sans', 'Space Grotesk', sans-serif; font-weight: 600; font-size: .85rem; padding: 8px 14px; border-radius: 2px; border: 0; background: var(--field); color: var(--muted); cursor: pointer; transition: color .15s, background-color .15s, box-shadow .15s;
      box-shadow: 0 -2px 0 0 var(--px-line), 0 2px 0 0 var(--px-line), -2px 0 0 0 var(--px-line), 2px 0 0 0 var(--px-line); }
    .fd-pill:hover { color: var(--text); }
    .fd-pill[aria-pressed="true"] { color: #fff; background: #14a0ff;
      box-shadow: 0 -2px 0 0 #0a5fb5, 0 2px 0 0 #0a5fb5, -2px 0 0 0 #0a5fb5, 2px 0 0 0 #0a5fb5; }
    .fd-seg { display: inline-flex; padding: 3px; border-radius: 2px; border: 0; background: var(--field); margin-left: auto;
      box-shadow: 0 -2px 0 0 var(--px-line), 0 2px 0 0 var(--px-line), -2px 0 0 0 var(--px-line), 2px 0 0 0 var(--px-line); }
    .fd-seg button { font-family: 'Space Mono', monospace; font-size: .74rem; padding: 6px 12px; border-radius: 1px; border: 0; background: transparent; color: var(--muted); cursor: pointer; }
    .fd-seg button[aria-pressed="true"] { background: #14a0ff; color: #fff; font-weight: 700; }
    html[data-theme="light"] .fd-seg button[aria-pressed="true"] { color: #fff; }

    /* Stat tiles, dressed up like the admin dashboard's KPI cards: icon, big number, trend graphic, delta pill. */
    .fd-tiles { display: grid; gap: 12px; margin-top: 16px; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); }
    .fd-tile { position: relative; overflow: hidden; display: flex; flex-direction: column; gap: 12px; }
    .fd-tile-top { display: flex; align-items: center; gap: 10px; }
    .fd-tile-icon { width: 32px; height: 32px; border-radius: 2px; display: grid; place-items: center; flex: none; color: var(--blue); background: rgba(115,182,255,.14); }
    .fd-tile.is-up .fd-tile-icon { color: var(--green); background: color-mix(in srgb, var(--green) 14%, transparent); }
    .fd-tile .l { font-family: 'Space Mono', monospace; font-size: .64rem; letter-spacing: .13em; text-transform: uppercase; color: var(--muted); }
    .fd-tile-body { display: flex; align-items: flex-end; justify-content: space-between; gap: 10px; }
    .fd-tile .n { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.9rem; line-height: 1.1; font-variant-numeric: tabular-nums; }
    .fd-tile .n small { font-size: .9rem; color: var(--muted); font-weight: 600; }
    .fd-tile .d { font-size: .78rem; color: var(--muted); }
    .fd-up { color: var(--green); font-weight: 700; }
    .fd-down { color: var(--red); font-weight: 700; }
    .fd-delta { white-space: nowrap; display: inline-flex; align-items: center; gap: 5px; font-size: .74rem; font-weight: 600; padding: 3px 9px; border-radius: 999px; width: fit-content; font-variant-numeric: tabular-nums; }
    .fd-delta.up { color: var(--green); background: color-mix(in srgb, var(--green) 14%, transparent); }
    .fd-delta.down { color: var(--red); background: color-mix(in srgb, var(--red) 14%, transparent); }
    .fd-delta.flat { color: var(--muted); background: var(--field); }
    .fd-spark { flex: none; }
    .fd-spark-bar { transform-box: fill-box; transform-origin: bottom; transition: height .4s cubic-bezier(.22,1,.36,1); }
    .fd-ring { --p: 0; width: 46px; height: 46px; border-radius: 50%; flex: none; display: grid; place-items: center;
      background: conic-gradient(var(--green) calc(var(--p) * 1%), var(--field) 0); transition: --p 1s cubic-bezier(.22,1,.36,1); }
    .fd-ring span { width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; background: var(--void); font-size: .66rem; font-weight: 700; font-variant-numeric: tabular-nums; }
    html[data-theme="light"] .fd-ring span { background: #fff; }
    @property --p { syntax: '<number>'; inherits: false; initial-value: 0; }

    /* Activity chart: a real line chart (Chart.js), one metric at a time so there's only ever one axis. */
    .fd-tabs { display: flex; flex-wrap: wrap; gap: 6px; }
    .fd-chart-box { position: relative; height: 230px; margin-top: 16px; }
    .fd-empty { position: absolute; inset: 0; display: grid; place-items: center; color: var(--muted); font-size: .9rem; text-align: center; padding: 0 20px; }

    /* Average completion donut, next to the progress histogram. */
    .fd-donut-row { display: flex; align-items: center; gap: 18px; margin-top: 14px; }
    .fd-donut { width: 96px; height: 96px; flex: none; transform: rotate(-90deg); }
    .fd-donut circle { fill: none; stroke-width: 4.5; }
    .fd-donut .track { stroke: var(--field); }
    .fd-donut .fill { stroke: var(--blue); stroke-linecap: round; stroke-dasharray: 0 100; transition: stroke-dasharray .9s cubic-bezier(.22,1,.36,1); }
    .fd-donut-wrap { position: relative; }
    .fd-donut-wrap b { position: absolute; inset: 0; display: grid; place-items: center; font-family: 'Space Grotesk', sans-serif; font-size: 1.15rem; }
    .fd-donut-kv { display: grid; gap: 6px; font-size: .84rem; flex: 1; color: var(--muted); }
    .fd-donut-kv strong { color: var(--text); font-variant-numeric: tabular-nums; }

    /* Progress spread */
    .fd-hist { display: flex; align-items: flex-end; gap: 8px; height: 170px; margin-top: 18px; }
    .fd-hcol { flex: 1; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; gap: 6px; border: 0; background: transparent; color: inherit; cursor: pointer; border-radius: 8px; padding: 0; font: inherit; }
    .fd-hcol:hover, .fd-hcol:focus-visible { background: rgba(115,182,255,.08); outline: none; }
    .fd-hcol .v { font-size: .8rem; font-weight: 700; font-variant-numeric: tabular-nums; }
    .fd-hcol .b { width: 70%; border-radius: 4px 4px 0 0; background: var(--violet); transition: height .35s cubic-bezier(.2,.8,.2,1); }
    .fd-hcol[aria-pressed="true"] .b { background: var(--blue); }
    .fd-hcol[aria-pressed="false"].dim .b { opacity: .35; }
    .fd-hlabels { display: flex; gap: 8px; margin-top: 6px; font-size: .72rem; color: var(--muted); }
    .fd-hlabels span { flex: 1; text-align: center; }

    /* Final exam: one stacked bar + a legend that doubles as a filter. */
    .fd-stack { display: flex; gap: 2px; height: 22px; margin-top: 20px; border-radius: 6px; overflow: hidden; background: var(--field); }
    .fd-stack i { display: block; height: 100%; transition: width .35s cubic-bezier(.2,.8,.2,1); }
    .fd-legend { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 6px; margin-top: 16px; }
    .fd-legend button { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 10px; border: 1px solid transparent; background: transparent; color: var(--text); cursor: pointer; font: inherit; font-size: .85rem; text-align: left; }
    .fd-legend button:hover, .fd-legend button:focus-visible { background: var(--field); outline: none; }
    .fd-legend button[aria-pressed="true"] { border-color: var(--blue); background: rgba(115,182,255,.1); }
    .fd-legend .sw { flex: 0 0 12px; height: 12px; border-radius: 3px; }
    .fd-legend b { margin-left: auto; font-variant-numeric: tabular-nums; }
    .st-passed { background: var(--green); } .st-failed { background: var(--red); } .st-caught { background: #ffb020; } .st-banned { background: #8b0f2e; }
    .st-taking { background: var(--violet); } .st-ready { background: var(--blue); } .st-studying { background: rgba(152,162,212,.45); }
    html[data-theme="light"] .st-studying { background: rgba(0,0,0,.18); }

    /* Students table */
    .fd-tools { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-top: 14px; }
    .fd-search { flex: 1 1 220px; padding: 10px 14px; border-radius: 12px; border: 1px solid var(--glass-border); background: var(--field); color: var(--text); font: inherit; font-size: .9rem; }
    .fd-search:focus { outline: none; border-color: var(--blue); }
    .fd-chip { display: inline-flex; align-items: center; gap: 6px; font-size: .78rem; padding: 6px 10px; border-radius: 999px; border: 1px solid var(--blue); background: rgba(115,182,255,.1); color: var(--text); cursor: pointer; }
    .fd-tablewrap { overflow-x: auto; margin-top: 12px; }
    .fd-table { width: 100%; border-collapse: collapse; font-size: .88rem; min-width: 640px; }
    .fd-table th { text-align: left; font-family: 'Space Mono', monospace; font-size: .66rem; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); font-weight: 400; padding: 8px 10px; border-bottom: 1px solid var(--glass-border); white-space: nowrap; }
    .fd-table th button { all: unset; cursor: pointer; }
    .fd-table th button:focus-visible { outline: 2px solid var(--blue); outline-offset: 2px; border-radius: 4px; }
    .fd-table td { padding: 10px; border-bottom: 1px solid var(--glass-border); vertical-align: middle; }
    .fd-table tr:hover td { background: rgba(115,182,255,.05); }
    .fd-table .num { text-align: right; font-variant-numeric: tabular-nums; }
    .fd-table .who a { color: var(--text); font-weight: 600; text-decoration: none; }
    .fd-table .who a:hover { color: var(--blue); }
    .fd-table .who small { display: block; color: var(--muted); font-size: .76rem; }
    .fd-pbar { display: flex; align-items: center; gap: 8px; min-width: 120px; }
    .fd-pbar span { flex: 1; height: 6px; border-radius: 99px; background: var(--field); overflow: hidden; }
    .fd-pbar span i { display: block; height: 100%; border-radius: 99px; background: var(--blue); }
    .fd-pbar b { font-variant-numeric: tabular-nums; min-width: 38px; text-align: right; font-weight: 600; }
    .fd-state { display: inline-flex; align-items: center; gap: 6px; font-size: .78rem; }
    .fd-state i { flex: none; }
    .fd-state i { width: 8px; height: 8px; border-radius: 50%; }
    .fd-nudge { font-size: .72rem; color: #ffb020; font-weight: 600; }
    html[data-theme="light"] .fd-nudge { color: #8a5a00; }

    /* Course list, alerts and join requests (below the charts). */
    .request-row { display: flex; align-items: center; flex-wrap: wrap; gap: 14px; padding: 14px 16px; border-radius: 16px; background: var(--field); border: 1px solid var(--glass-border); transition: border-color .2s ease, background-color .2s ease; }
    .request-row:hover { border-color: rgba(115,182,255,.35); }
    .request-info { flex: 1 1 220px; min-width: 200px; }
    .request-meta { display: block; margin-top: 2px; color: var(--muted); font-size: .82rem; }
    .request-actions { display: flex; align-items: center; gap: 8px; flex: 0 0 auto; margin-left: auto; }
    .final-flag { display: inline-flex; align-items: center; gap: 6px; margin-top: 6px; padding: 3px 10px; border-radius: 999px; font-size: .76rem; font-weight: 600; color: #ffc400; background: rgba(255,196,0,.12); border: 1px solid rgba(255,196,0,.35); text-decoration: none; }
    .final-flag:hover { background: rgba(255,196,0,.2); }
    html[data-theme="light"] .final-flag { color: #8a5a00; }
    .alert-row { border-color: rgba(255,107,129,.4); }
    .alert-dot { flex: 0 0 34px; height: 34px; border-radius: 50%; display: grid; place-items: center; font-weight: 800; color: #fff; background: #e5484d; }
    .fdash .roster-card .sub { margin: 4px 0 0; }

    @media (max-width: 640px) {
      .fd-seg { margin-left: 0; }
      .fd-card { padding: 16px; }
      .fd-yaxis, .fd-gridlines, .fd-cols { height: 160px; }
    }
  </style>
@endpush

@section('content')
<div class="fdash" x-data="facultyDash()" x-init="initChart()">
  @php
    $first   = explode(' ', trim(Auth::user()->name))[0] ?: 'Captain';
    $waiting = $requests->count();
    $caught  = $alerts->count();
  @endphp
  {{-- Astro greets you --}}
  <header class="fd-hello">
    <x-astro-mascot />
    <p class="fd-bubble">
      Welcome back, Captain {{ $first }}! Let's check on the crew.
      <small>
        @if ($caught) {{ $caught }} {{ \Illuminate\Support\Str::plural('student', $caught) }} left an exam early. Take a look below.
        @elseif ($waiting) {{ $waiting }} {{ \Illuminate\Support\Str::plural('student', $waiting) }} {{ $waiting === 1 ? 'is' : 'are' }} waiting to join your courses.
        @elseif ($courses->isEmpty()) You don't have a course yet. Once you do, your students show up here.
        @else Everything's calm on the bridge. Here's how your students are doing.
        @endif
      </small>
    </p>
  </header>

  <h1>Your <span class="accent">crew</span> at a glance</h1>
  <p class="sub">How your students are doing across your courses. Pick a course or a time range, hover a chart for the details, and click a bar to find the students behind it.</p>

  {{-- Filters scope every tile, chart and the table below them. --}}
  <div class="fd-filters" data-tour="fd-filters" role="group" aria-label="Filters">
    <button type="button" class="fd-pill" :aria-pressed="course === null" @click="course = null">All courses</button>
    <template x-for="c in data.courses" :key="c.id">
      <button type="button" class="fd-pill" :aria-pressed="course === c.id" @click="course = c.id" x-text="c.name"></button>
    </template>
    <div class="fd-seg" role="group" aria-label="Time range">
      <template x-for="r in [7, 14, 30]" :key="r">
        <button type="button" :aria-pressed="range === r" @click="range = r" x-text="r + ' days'"></button>
      </template>
    </div>
  </div>

  {{-- Stat tiles: the number, a trend graphic, and a delta pill when there's something to compare. --}}
  <div class="fd-tiles" data-tour="fd-tiles">
    <div class="fd-card fd-tile">
      <div class="fd-tile-top">
        <span class="fd-tile-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8a7 7 0 0 1 14 0"/></svg></span>
        <span class="l">Students</span>
      </div>
      <div class="fd-tile-body"><div class="n" x-text="kpi.students"></div></div>
      <div class="d" x-text="kpi.students === 1 ? 'on your roster' : 'on your rosters'"></div>
    </div>

    <div class="fd-card fd-tile" :class="{ 'is-up': kpi.active > 0 }">
      <div class="fd-tile-top">
        <span class="fd-tile-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13 3L5 13.5h6L10 21l8-10.5h-6L13 3Z"/></svg></span>
        <span class="l">Active students</span>
      </div>
      <div class="fd-tile-body">
        <div class="n"><span x-text="kpi.active"></span><small x-text="' / ' + kpi.students"></small></div>
        <div class="fd-ring" x-data x-init="$watch(() => $root.kpi.activePct, p => $el.style.setProperty('--p', p), { immediate: true })"
             :title="kpi.activePct + '% of students active in ' + range + ' days">
          <span x-text="kpi.activePct + '%'"></span>
        </div>
      </div>
      <div class="d" x-html="delta(kpi.active, kpi.activePrev)"></div>
    </div>

    <div class="fd-card fd-tile" :class="{ 'is-up': kpi.lessons > 0 }">
      <div class="fd-tile-top">
        <span class="fd-tile-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg></span>
        <span class="l">Lessons completed</span>
      </div>
      <div class="fd-tile-body">
        <div class="n" x-text="kpi.lessons"></div>
        <svg class="fd-spark" width="80" height="34" viewBox="0 0 80 34" aria-hidden="true">
          <template x-for="(v, i) in lessonsSpark" :key="i">
            <rect class="fd-spark-bar" :x="i * (80 / lessonsSpark.length) + 1" :width="(80 / lessonsSpark.length) - 2"
                  :y="34 - (sparkMax ? v / sparkMax * 30 : 0)" :height="Math.max(2, sparkMax ? v / sparkMax * 30 : 0)" rx="1.5"
                  :fill="kpi.lessons > 0 ? 'var(--green)' : 'var(--blue)'" :opacity="i === lessonsSpark.length - 1 ? 1 : .45" />
          </template>
        </svg>
      </div>
      <div class="d" x-html="delta(kpi.lessons, kpi.lessonsPrev)"></div>
    </div>

    <div class="fd-card fd-tile">
      <div class="fd-tile-top">
        <span class="fd-tile-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 17l6-6 4 4 8-8"/></svg></span>
        <span class="l">Avg. progress</span>
      </div>
      <div class="fd-tile-body"><div class="n"><span x-text="kpi.avg"></span><small>%</small></div></div>
      <div class="d">of the course's lessons done</div>
    </div>

    <div class="fd-card fd-tile">
      <div class="fd-tile-top">
        <span class="fd-tile-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4ZM7 6H4v1a3 3 0 0 0 3 3M17 6h3v1a3 3 0 0 1-3 3"/></svg></span>
        <span class="l">Passed the final</span>
      </div>
      <div class="fd-tile-body"><div class="n" x-text="kpi.passed"></div></div>
      <div class="d" x-text="(kpi.passed === 1 ? 'certificate' : 'certificates') + ' earned' + (kpi.ready ? ' · ' + kpi.ready + ' ready to take it' : '')"></div>
    </div>
  </div>

  {{-- Activity over time: one measure at a time, so there is only ever one axis. --}}
  <section class="fd-card" style="margin-top:16px" aria-labelledby="act-h" data-tour="fd-activity">
    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px">
      <div>
        <h2 id="act-h">Activity</h2>
        <p class="hint" x-text="'Per day, last ' + range + ' days'"></p>
      </div>
      <div class="fd-tabs" role="group" aria-label="Measure">
        <template x-for="m in metrics" :key="m.key">
          <button type="button" class="fd-pill" :aria-pressed="metric === m.key" @click="metric = m.key" x-text="m.label"></button>
        </template>
      </div>
    </div>

    <div class="fd-chart-box">
      <canvas x-ref="actCanvas" role="img" :aria-label="metricLabel + ' per day, last ' + range + ' days'"></canvas>
      <p class="fd-empty" x-show="!hasActivity" x-text="rows.length ? 'No activity in these ' + range + ' days yet.' : 'This chart fills in once students join your courses and start their lessons.'"></p>
    </div>

    <details style="margin-top:14px">
      <summary class="hint" style="cursor:pointer">Show as a table</summary>
      <div class="fd-tablewrap">
        <table class="fd-table" style="min-width:0">
          <thead><tr><th>Day</th><th class="num">Lessons</th><th class="num">Active time</th><th class="num">Active students</th></tr></thead>
          <tbody>
            <template x-for="d in series" :key="d.date">
              <tr><td x-text="d.weekday + ', ' + d.label"></td><td class="num" x-text="d.lessons"></td><td class="num" x-text="fmtMins(d.minutes)"></td><td class="num" x-text="d.active"></td></tr>
            </template>
          </tbody>
        </table>
      </div>
    </details>
  </section>

  <div class="fd-grid two" data-tour="fd-charts">
    {{-- How far along everyone is --}}
    <section class="fd-card" aria-labelledby="spread-h">
      <h2 id="spread-h">Course progress</h2>
      <p class="hint">Students by how much of the course they've finished. Click a bar to list them.</p>
      <div class="fd-donut-row">
        <div class="fd-donut-wrap">
          <svg class="fd-donut" viewBox="0 0 42 42" aria-hidden="true">
            <circle class="track" cx="21" cy="21" r="15.915" />
            <circle class="fill" cx="21" cy="21" r="15.915" pathLength="100" :style="'stroke-dasharray:' + kpi.avg + ' 100'" />
          </svg>
          <b x-text="kpi.avg + '%'"></b>
        </div>
        <div class="fd-donut-kv">
          <div>Average completion across <strong x-text="kpi.students"></strong> <span x-text="kpi.students === 1 ? 'student' : 'students'"></span></div>
          <div><strong x-text="buckets[buckets.length - 1].count"></strong> finished the whole course</div>
        </div>
      </div>
      <div class="fd-hist">
        <template x-for="(b, i) in buckets" :key="b.label">
          <button type="button" class="fd-hcol" :class="{ dim: bucket !== null }" :aria-pressed="bucket === i"
                  :aria-label="b.count + ' students at ' + b.label" @click="toggle('bucket', i)">
            <span class="v" x-text="b.count"></span>
            <span class="b" :style="'height:' + (bucketMax ? b.count / bucketMax * 120 : 0) + 'px'"></span>
          </button>
        </template>
      </div>
      <div class="fd-hlabels" aria-hidden="true">
        <template x-for="b in buckets" :key="b.label"><span x-text="b.label"></span></template>
      </div>
    </section>

    {{-- Final exam pipeline --}}
    <section class="fd-card" aria-labelledby="final-h">
      <h2 id="final-h">Final exam</h2>
      <p class="hint">Where each student is on the way to a certificate. Click a row to list them.</p>
      <div class="fd-stack" role="img" :aria-label="states.map(s => s.count + ' ' + s.label).join(', ')">
        <template x-for="s in states" :key="s.key">
          <i :class="'st-' + s.key" :style="'width:' + (rows.length ? s.count / rows.length * 100 : 0) + '%'" :title="s.label + ': ' + s.count"></i>
        </template>
      </div>
      <div class="fd-legend">
        <template x-for="s in states" :key="s.key">
          <button type="button" :aria-pressed="state === s.key" @click="toggle('state', s.key)">
            <span class="sw" :class="'st-' + s.key"></span><span x-text="s.label"></span><b x-text="s.count"></b>
          </button>
        </template>
      </div>
    </section>
  </div>

  {{-- Every student, filtered by the charts above --}}
  <section class="fd-card" style="margin-top:16px" aria-labelledby="students-h" data-tour="fd-students">
    <div style="display:flex;flex-wrap:wrap;align-items:baseline;justify-content:space-between;gap:8px">
      <h2 id="students-h">Students</h2>
      <p class="hint" x-text="table.length + ' of ' + rows.length + ' shown'"></p>
    </div>
    <div class="fd-tools">
      <input type="search" class="fd-search" placeholder="Search by name or email" x-model.debounce.150ms="q" aria-label="Search students">
      <button type="button" class="fd-pill" :aria-pressed="nudge" @click="nudge = !nudge" x-text="'Needs a nudge (' + nudgeCount + ')'"
              title="Not active for 7+ days and not finished"></button>
      <button type="button" class="fd-chip" x-show="bucket !== null" @click="bucket = null"><span x-text="'Progress ' + (buckets[bucket]?.label ?? '')"></span> ✕</button>
      <button type="button" class="fd-chip" x-show="state !== null" @click="state = null"><span x-text="'Final: ' + (stateLabel(state))"></span> ✕</button>
    </div>
    <div class="fd-tablewrap">
      <table class="fd-table">
        <thead>
          <tr>
            <th><button type="button" @click="sortBy('name')">Student <span x-text="arrow('name')"></span></button></th>
            <th x-show="course === null && data.courses.length > 1">Course</th>
            <th><button type="button" @click="sortBy('percent')">Progress <span x-text="arrow('percent')"></span></button></th>
            <th class="num"><button type="button" @click="sortBy('lessonsIn')">Lessons <span x-text="arrow('lessonsIn')"></span></button></th>
            <th class="num"><button type="button" @click="sortBy('minutesIn')">Active time <span x-text="arrow('minutesIn')"></span></button></th>
            <th><button type="button" @click="sortBy('lastSeen')">Last active <span x-text="arrow('lastSeen')"></span></button></th>
            <th>Final</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="s in table.slice(0, showAll ? table.length : 10)" :key="s.crew + '-' + s.id">
            <tr>
              <td class="who"><a :href="rosterUrl(s.crew)" x-text="s.name"></a><small x-text="s.email"></small></td>
              <td x-show="course === null && data.courses.length > 1" x-text="courseName(s.crew)"></td>
              <td><div class="fd-pbar"><span><i :style="'width:' + s.percent + '%'"></i></span><b x-text="s.percent + '%'"></b></div></td>
              <td class="num" x-text="s.lessonsIn"></td>
              <td class="num" x-text="fmtMins(s.minutesIn)"></td>
              <td><span x-text="ago(s.lastSeen)"></span> <span class="fd-nudge" x-show="s.nudge">· nudge</span></td>
              <td><span class="fd-state"><i :class="'st-' + s.final"></i><span x-text="rowState(s)"></span></span></td>
            </tr>
          </template>
          <tr x-show="!table.length">
            <td colspan="7" class="hint" style="text-align:center;padding:22px" x-text="rows.length ? 'No students match these filters.' : 'No students yet. Once you accept join requests, they show up here.'"></td>
          </tr>
        </tbody>
      </table>
    </div>
    <button type="button" class="mbtn" style="margin-top:12px" x-show="table.length > 10" @click="showAll = !showAll"
            x-text="showAll ? 'Show fewer' : 'Show all ' + table.length"></button>
  </section>

  {{-- ── Courses, alerts and join requests ── --}}
  <div class="roster-card" data-tour="fd-courses">
    <h2>Your courses</h2>
    @if ($courses->isEmpty())
      <p class="sub">No courses assigned yet — an admin will set you up as a course's captain, and it'll show up here.</p>
    @else
      <p class="sub">Open your class for each course. Its class code is just the class's ID: students don't type it, they pick you under Join Class and ask to join. Accept them and they're in your class right away. Your lesson edits reach only your class: self-paced students keep the default course.</p>

      @if (session('status'))
        <p class="mok" role="status">{{ session('status') }}</p>
      @endif

      <div class="roster">
        @foreach ($courses as $c)
          <div class="member" style="flex-wrap:wrap">
            <span class="avatar">{{ strtoupper(substr($c['title'], 0, 2)) }}</span>
            <div style="flex:1 1 auto;min-width:200px">
              <span class="mname" style="display:block">{{ $c['title'] }}</span>
              <span style="color:var(--muted);font-size:.82rem">{{ $c['planetTitle'] }}</span>
              {{-- No live final exam means nobody in this course can earn a certificate yet. --}}
              @if ($c['final'] === 'none')
                <a class="final-flag" href="{{ route('faculty.quizzes.build', ['crew' => $c['crew'], 'final' => 1]) }}">⚠ Create the final exam so students can pass</a>
              @elseif ($c['final'] === 'draft')
                <a class="final-flag" href="{{ route('faculty.exams.index', $c['crew']) }}#final-exam">⚠ Final exam is a draft, publish it</a>
              @endif
            </div>

            @if ($c['crew'])
              <div style="text-align:right">
                <div style="font-family:'Space Mono',monospace;letter-spacing:.12em;font-weight:700;color:var(--blue)">{{ $c['crew']->code }}</div>
                <div style="color:var(--muted);font-size:.78rem">
                  {{ $joined[$c['crew']->id] ?? 0 }} joined
                  @if ($c['selfPaced'])
                    · <a href="{{ route('faculty.course.roster', $c['selfPaced']) }}" style="color:inherit">{{ $joined[$c['selfPaced']->id] ?? 0 }} self-paced</a>
                  @endif
                  @if ($c['waiting'])
                    · <strong style="color:var(--text)">{{ $c['waiting'] }} waiting</strong>
                  @endif
                </div>
              </div>
              <a class="mbtn solid" href="{{ route('faculty.course', $c['crew']) }}">Manage course →</a>
            @else
              <form method="POST" action="{{ route('faculty.assignments.generate-code', $c['assignment']) }}">
                @csrf
                <button class="mbtn solid" type="submit">Open class</button>
              </form>
            @endif
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- The signature on the certificates of the students who pass a class's final exam. --}}
  @if ($courses->isNotEmpty())
    <div class="roster-card">
      <h2>Certificate signature</h2>
      <p class="sub" style="margin:4px 0 12px">Students who pass your class's final exam get a certificate signed by you. Upload your signature (PNG or JPG, dark ink on a white or clear background).</p>
      <div style="display:flex;flex-wrap:wrap;align-items:center;gap:14px">
        @if (Auth::user()->signature_path)
          <img src="{{ Storage::disk('public')->url(Auth::user()->signature_path) }}" alt="Your signature" style="max-height:56px;max-width:240px;background:#fff;border-radius:8px;padding:6px 10px">
        @else
          <span style="font:600 1.6rem/1 'Caveat',cursive;color:var(--muted)">{{ Auth::user()->name }}</span>
          <span style="color:var(--muted);font-size:.82rem">(no signature yet: your name is written instead)</span>
        @endif
        <form method="POST" action="{{ route('faculty.signature.save') }}" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center">
          @csrf
          <input type="file" name="signature" accept="image/png,image/jpeg" required style="font-size:.85rem;color:var(--muted)">
          <button class="mbtn solid" type="submit">{{ Auth::user()->signature_path ? 'Replace' : 'Upload' }}</button>
        </form>
        @if (Auth::user()->signature_path)
          <form method="POST" action="{{ route('faculty.signature.delete') }}">
            @csrf @method('DELETE')
            <button class="mbtn" type="submit">Remove</button>
          </form>
        @endif
      </div>
      @error('signature')<p class="sub" style="color:#ff9db0;margin-top:8px">{{ $message }}</p>@enderror
    </div>
  @endif

  @if ($alerts->isNotEmpty())
    <div class="roster-card" data-tour="fd-alerts">
      <h2>Exam alerts</h2>
      <p class="sub" style="margin:4px 0 12px">These students were caught leaving their final exam or a mini boss (switching tab or window, leaving full screen or the page, or trying a screenshot), so it ended as a fail. On the final, caught once is a strike; caught twice and they're banned from it. You can let them try the final again from the course page.</p>
      <div class="roster">
        @foreach ($alerts as $al)
          <div class="request-row alert-row">
            <span class="alert-dot" aria-hidden="true">!</span>
            <div class="request-info">
              <span class="mname" style="display:block">{{ $al->user?->name ?? 'A student' }}</span>
              @if ($al->quiz->isMiniBoss())
                <span class="request-meta">Module {{ (int) substr($al->quiz->module, 1) }} mini boss · {{ $al->violationText() }} · 0 XP · {{ $al->quiz->crew->name }} · {{ $al->finished_at?->diffForHumans() }}</span>
              @else
                <span class="request-meta">Final exam · {{ $al->violationText() }} · {{ $al->banned() ? 'Banned (2nd strike)' : ($al->cheated() ? 'Strike 1 of 2' : 'Failed') }} · {{ $al->quiz->crew->name }} · {{ $al->finished_at?->diffForHumans() }}</span>
              @endif
            </div>
            <div class="request-actions">
              <a class="mbtn solid" href="{{ $al->quiz->isMiniBoss() ? route('faculty.exams.index', $al->quiz->crew).'#boss-'.$al->quiz->module : route('faculty.exams.index', $al->quiz->crew).'#final-exam' }}">Review</a>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  @if ($courses->isNotEmpty())
    <div class="roster-card" data-tour="fd-requests">
      <h2>Waiting to join</h2>
      @if ($requests->isEmpty())
        <p class="mempty">Nobody is waiting right now. When a student asks to join one of your courses, they show up here.</p>
      @else
        <div class="roster">
          @foreach ($requests as $r)
            <div class="request-row">
              <span class="avatar">{{ strtoupper(substr($r->student->name, 0, 2)) }}</span>
              <div class="request-info">
                <span class="mname" style="display:block">{{ $r->student->name }}</span>
                <span class="request-meta">{{ $r->student->email }} · {{ $r->crew->name }} · asked {{ $r->updated_at->diffForHumans() }}</span>
              </div>
              <div class="request-actions">
                <form method="POST" action="{{ route('faculty.join-requests.accept', $r) }}">
                  @csrf
                  <button class="mbtn solid" type="submit">Accept</button>
                </form>
                <form method="POST" action="{{ route('faculty.join-requests.decline', $r) }}">
                  @csrf
                  <button class="mbtn danger" type="submit">Decline</button>
                </form>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  @endif
</div>
@endsection

@push('scripts')
  <script>
    function facultyDash() {
      const data = @json($analytics);
      const N = data.days.length;
      const today = data.days[N - 1].date;
      const daysAgo = (date) => Math.round((Date.parse(today) - Date.parse(date)) / 86400000);
      const sum = (a, from, to) => { let t = 0; for (let i = from; i <= to; i++) t += a[i] || 0; return t; };
      const STATES = [
        { key: 'passed', label: 'Passed' }, { key: 'failed', label: 'Failed' }, { key: 'caught', label: 'Caught cheating (1 strike)' },
        { key: 'banned', label: 'Banned (2 strikes)' },
        { key: 'taking', label: 'Taking it now' }, { key: 'ready', label: 'Ready to take it' }, { key: 'studying', label: 'Still on lessons' },
      ];
      const BUCKETS = [[0, 19, '0–19%'], [20, 39, '20–39%'], [40, 59, '40–59%'], [60, 79, '60–79%'], [80, 99, '80–99%'], [100, 100, 'Done']];
      let actChart = null;

      return {
        data,
        course: null, range: 7, metric: 'lessons', hover: null,
        bucket: null, state: null, nudge: false, q: '', sortKey: 'percent', sortDir: -1, showAll: false,
        metrics: [{ key: 'lessons', label: 'Lessons completed' }, { key: 'minutes', label: 'Active time' }, { key: 'active', label: 'Active students' }],

        // Index of the first day in the chosen range, and of the range just before it (for the ▲▼ comparison).
        get from() { return N - this.range; },
        get prevFrom() { return Math.max(0, N - 2 * this.range); },

        // One row per student per course, with this range's totals and a "needs a nudge" flag.
        get rows() {
          return data.students.filter(s => this.course === null || s.crew === this.course).map(s => {
            const mins = data.minutes[s.id] || [];
            const lastSeen = s.lastSeen;
            return {
              ...s,
              lessonsIn: sum(s.lessons, this.from, N - 1),
              minutesIn: sum(mins, this.from, N - 1),
              nudge: s.percent < 100 && (!lastSeen || daysAgo(lastSeen) >= 7),
            };
          });
        },
        get users() { return [...new Set(this.rows.map(s => s.id))]; },

        // Per-day totals for the chart. Minutes are per student, so each student counts once even if in two courses.
        get series() {
          const rows = this.rows, users = this.users, out = [];
          for (let i = this.from; i < N; i++) {
            const lessonsBy = {};
            let lessons = 0;
            rows.forEach(s => { if (s.lessons[i]) { lessons += s.lessons[i]; lessonsBy[s.id] = 1; } });
            let minutes = 0, active = 0;
            users.forEach(u => {
              const m = (data.minutes[u] || [])[i] || 0;
              minutes += m;
              if (m > 0 || lessonsBy[u]) active++;
            });
            out.push({ ...data.days[i], lessons, minutes, active });
          }
          return out;
        },
        get hasActivity() { return this.series.some(d => d.lessons || d.minutes || d.active); },
        get metricLabel() { return this.metrics.find(m => m.key === this.metric).label; },
        // Daily lesson totals for the KPI tile's mini bar chart.
        get lessonsSpark() { return this.series.map(d => d.lessons); },
        get sparkMax() { return Math.max(1, ...this.lessonsSpark); },

        activeBetween(from, to) {
          const seen = new Set(this.rows.filter(s => sum(s.lessons, from, to) > 0).map(s => s.id));
          this.users.forEach(u => { if (sum(data.minutes[u] || [], from, to) > 0) seen.add(u); });
          return seen.size;
        },
        get kpi() {
          const rows = this.rows, prevTo = this.from - 1;
          const hasPrev = prevTo >= this.prevFrom;
          const active = this.activeBetween(this.from, N - 1);
          return {
            students: this.users.length,
            active,
            activePct: this.users.length ? Math.round(active / this.users.length * 100) : 0,
            activePrev: hasPrev ? this.activeBetween(this.prevFrom, prevTo) : null,
            lessons: rows.reduce((t, s) => t + s.lessonsIn, 0),
            lessonsPrev: hasPrev ? rows.reduce((t, s) => t + sum(s.lessons, this.prevFrom, prevTo), 0) : null,
            avg: rows.length ? Math.round(rows.reduce((t, s) => t + s.percent, 0) / rows.length) : 0,
            passed: rows.filter(s => s.final === 'passed').length,
            ready: rows.filter(s => s.final === 'ready').length,
          };
        },

        get buckets() { return BUCKETS.map(([lo, hi, label]) => ({ label, count: this.rows.filter(s => s.percent >= lo && s.percent <= hi).length })); },
        get bucketMax() { return Math.max(0, ...this.buckets.map(b => b.count)); },
        get states() { return STATES.map(s => ({ ...s, count: this.rows.filter(r => r.final === s.key).length })); },
        get nudgeCount() { return this.rows.filter(s => s.nudge).length; },

        get table() {
          const q = this.q.trim().toLowerCase();
          const b = this.bucket === null ? null : BUCKETS[this.bucket];
          const key = this.sortKey, dir = this.sortDir;
          return this.rows
            .filter(s => !q || s.name.toLowerCase().includes(q) || s.email.toLowerCase().includes(q))
            .filter(s => !b || (s.percent >= b[0] && s.percent <= b[1]))
            .filter(s => this.state === null || s.final === this.state)
            .filter(s => !this.nudge || s.nudge)
            .sort((a, z) => {
              const x = a[key] ?? '', y = z[key] ?? '';
              return (typeof x === 'string' ? x.localeCompare(y) : x - y) * dir || a.name.localeCompare(z.name);
            });
        },

        toggle(which, value) { this[which] = this[which] === value ? null : value; this.showAll = false; },
        sortBy(key) { this.sortDir = this.sortKey === key ? -this.sortDir : (key === 'name' ? 1 : -1); this.sortKey = key; },
        arrow(key) { return this.sortKey === key ? (this.sortDir > 0 ? '↑' : '↓') : ''; },

        // The activity chart: a Chart.js line, redrawn whenever the metric, course or range changes, or the theme flips.
        initChart() {
          this.$watch('metric', () => this.drawChart());
          this.$watch('series', () => this.drawChart());
          this.drawChart();
          new MutationObserver(() => this.drawChart()).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
        },
        css(name) { return getComputedStyle(document.documentElement).getPropertyValue(name).trim(); },
        drawChart() {
          if (!window.Chart || !this.$refs.actCanvas) return;
          const ctx = this.$refs.actCanvas.getContext('2d');
          const colors = { lessons: this.css('--green'), minutes: this.css('--violet'), active: this.css('--cyan') };
          const color = colors[this.metric] || this.css('--blue');
          const muted = this.css('--muted'), grid = this.css('--glass-border') || 'rgba(150,170,255,.18)';
          const series = this.series;
          const fmt = (v) => this.metric === 'minutes' ? this.fmtMins(v) : v;
          const g = ctx.createLinearGradient(0, 0, 0, 230);
          g.addColorStop(0, color + '55'); g.addColorStop(1, color + '00');
          const labels = series.map(d => this.range === 7 ? d.weekday : d.label);
          actChart?.destroy();
          actChart = new Chart(ctx, {
            type: 'line',
            data: { labels, datasets: [{
              label: this.metricLabel, data: series.map(d => d[this.metric]),
              borderColor: color, backgroundColor: g, fill: true, tension: .38, borderWidth: 2.5,
              pointRadius: 0, pointHoverRadius: 5, pointBackgroundColor: color, pointBorderColor: 'transparent',
            }] },
            options: {
              responsive: true, maintainAspectRatio: false,
              animation: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? false : { duration: 500, easing: 'easeOutQuart' },
              interaction: { mode: 'index', intersect: false },
              plugins: {
                legend: { display: false },
                tooltip: {
                  padding: 10, cornerRadius: 10, boxPadding: 4, usePointStyle: true,
                  titleFont: { family: 'Space Grotesk', weight: '600' }, bodyFont: { family: 'Inter' },
                  callbacks: { label: (c) => fmt(c.parsed.y) + (this.metric === 'active' ? ' students' : this.metric === 'lessons' ? ' lessons' : '') },
                },
              },
              scales: {
                x: { grid: { display: false }, border: { display: false }, ticks: { color: muted, font: { family: 'Space Mono', size: 10 }, maxRotation: 0, autoSkipPadding: 14 } },
                y: { beginAtZero: true, grid: { color: grid }, border: { display: false }, ticks: { color: muted, font: { family: 'Space Mono', size: 10 }, maxTicksLimit: 5, callback: fmt } },
              },
            },
          });
        },

        fmtMins(m) { m = Math.round(m); return m >= 60 ? Math.floor(m / 60) + 'h' + (m % 60 ? ' ' + (m % 60) + 'm' : '') : m + 'm'; },
        delta(now, prev) {
          if (prev === null) return '<span class="fd-delta flat">in the last ' + this.range + ' days</span>';
          if (now === prev) return '<span class="fd-delta flat">same as the ' + this.range + ' days before</span>';
          const up = now > prev, diff = Math.abs(now - prev);
          return '<span class="fd-delta ' + (up ? 'up' : 'down') + '">' + (up ? '▲' : '▼') + ' ' + diff + '</span> vs the ' + this.range + ' days before';
        },
        ago(date) {
          if (!date) return 'Not yet';
          const d = daysAgo(date);
          return d <= 0 ? 'Today' : d === 1 ? 'Yesterday' : d + ' days ago';
        },
        stateLabel(key) { return (STATES.find(s => s.key === key) || {}).label || ''; },
        // The table says what the student actually did, e.g. "Caught: switched tab or window · strike 1 of 2".
        rowState(s) {
          if (s.final === 'caught') return 'Caught: ' + s.reason + ' · strike 1 of 2';
          if (s.final === 'banned') return 'Banned: ' + s.reason + ' · 2nd strike';
          if (s.final === 'failed' && s.reason) return 'Failed: ' + s.reason;
          if (s.final === 'ready' && s.strikes) return 'Ready to take it · 1 strike';
          return this.stateLabel(s.final);
        },
        courseName(id) { return (data.courses.find(c => c.id === id) || {}).name || ''; },
        rosterUrl(id) { return @json(url('/faculty/courses')) + '/' + id + '/roster'; },
      };
    }
  </script>
@endpush
