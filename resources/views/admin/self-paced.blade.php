@extends('layouts.admin-shell')
@section('title', 'Self-paced')

@php
  $planetColors = ['programming' => '#58c24a', 'networking' => '#1b84c9', 'cybersecurity' => '#7a5af5'];
  $planetLogos = ['programming' => 'programming-logo', 'networking' => 'networking-logo', 'cybersecurity' => 'cybersecurity-logo'];
  $stateMeta = [
    'studying' => ['label' => 'Studying', 'color' => '#73b6ff', 'icon' => 'M12 6v6l4 2'],
    'ready' => ['label' => 'Ready for final', 'color' => '#5be1ff', 'icon' => 'M5 12.5l4.5 4.5L19 7.5'],
    'taking' => ['label' => 'Taking final', 'color' => '#9b6bff', 'icon' => 'M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83'],
    'passed' => ['label' => 'Passed', 'color' => '#3ddc84', 'icon' => 'M5 12.5l4.5 4.5L19 7.5'],
    'failed' => ['label' => 'Failed final', 'color' => '#ff6b6b', 'icon' => 'M6 6l12 12M18 6L6 18'],
    'caught' => ['label' => 'Caught leaving', 'color' => '#f5a623', 'icon' => 'M12 9v4M12 17h.01M10.3 3.9 1.8 18.5A1.5 1.5 0 0 0 3.1 21h17.8a1.5 1.5 0 0 0 1.3-2.5L13.7 3.9a1.5 1.5 0 0 0-2.6 0Z'],
    'banned' => ['label' => 'Banned', 'color' => '#ff4d6d', 'icon' => 'M5 5l14 14M19 5 5 19'],
  ];
@endphp

@section('content')
  <span class="eyebrow">Admin · Self-paced</span>
  <h1>Self-paced <span class="accent">learners</span></h1>
  <p class="sub">Students who took a course on their own — the default course, default final exam and a TechLab certificate. Faculty edits never reach them.</p>

  @if (session('status'))
    <div class="code-card" style="margin-top:18px;padding:14px 20px"><p class="sub" style="margin:0">{{ session('status') }}</p></div>
  @endif

  @if ($courses->isEmpty())
    <div class="code-card" style="margin-top:22px;padding:28px;text-align:center">
      <p class="sub" style="margin:0">No courses are live yet.</p>
    </div>
  @else
    <div class="sp-grid">
      @foreach ($courses as $c)
        @php
          $color = $planetColors[$c['planetSlug']] ?? '#73b6ff';
          $total = count($c['learners']);
          $counts = collect($c['learners'])->countBy('state');
          $breakdown = collect($stateMeta)->map(fn ($meta, $key) => ['key' => $key] + $meta + ['count' => $counts[$key] ?? 0])->filter(fn ($s) => $s['count'] > 0)->values();
        @endphp
        <section class="sp-card" style="--pc: {{ $color }}">
          <div class="sp-banner">
            <span class="sp-banner-pattern" aria-hidden="true"></span>
            <span class="sp-logo">
              @if (isset($planetLogos[$c['planetSlug']]))<x-dynamic-component :component="$planetLogos[$c['planetSlug']]" />@endif
            </span>
            <span class="sp-banner-text">
              <span class="sp-title">{{ $c['title'] }}</span>
              <span class="sp-sub">{{ $c['planet'] }}</span>
            </span>
            <span class="sp-count"><span class="sp-count-n">{{ $total }}</span><span class="sp-count-l">{{ Str::plural('learner', $total) }}</span></span>
          </div>

          <div class="sp-stats">
            @if ($c['final'])
              <span class="sp-chip"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 5h11a1 1 0 0 1 1 1v12.5L16 15H8a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/><path d="M4 9v9.5L8 15"/></svg>{{ $c['final']->questions()->count() }} questions</span>
              <span class="sp-chip"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.2"/><circle cx="12" cy="12" r=".6" fill="currentColor"/></svg>{{ $c['final']->passPercent() }}% to pass</span>
            @else
              <span class="sp-chip muted"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/></svg>Final auto-builds on first finish</span>
            @endif
          </div>

          @if ($total)
            <div class="sp-breakdown">
              @foreach ($breakdown as $s)
                <span class="sp-seg" style="--w: {{ round($s['count'] / $total * 100, 2) }}%; --sc: {{ $s['color'] }}" title="{{ $s['label'] }}: {{ $s['count'] }}"></span>
              @endforeach
            </div>
            <div class="sp-legend">
              @foreach ($breakdown as $s)
                <span class="sp-legend-item"><i style="background: {{ $s['color'] }}"></i>{{ $s['label'] }} <b>{{ $s['count'] }}</b></span>
              @endforeach
            </div>

            <div class="sp-learners">
              @foreach ($c['learners'] as $l)
                @php
                  $meta = $stateMeta[$l['state']] ?? ['label' => $l['state'], 'color' => '#73b6ff', 'icon' => ''];
                  $initials = mb_strtoupper(mb_substr($l['student']->name, 0, 2));
                @endphp
                <div class="sp-learner">
                  <div class="sp-ring" style="--p: {{ $l['percent'] }}; --rc: {{ $meta['color'] }}">
                    <span class="sp-avatar">{{ $initials }}</span>
                  </div>
                  <div class="sp-who">
                    <p class="sp-name">{{ $l['student']->name }}</p>
                    <span class="sp-pill" style="--sc: {{ $meta['color'] }}">
                      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $meta['icon'] }}"/></svg>
                      {{ $meta['label'] }}@if ($l['attempt']?->passed()) · {{ $l['attempt']->percent() }}%@endif
                    </span>
                  </div>
                  @if ($l['attempt'] && ! $l['attempt']->passed() && ! $l['attempt']->inProgress())
                    <form method="POST" action="{{ route('faculty.final-exam.retake', $l['attempt']) }}">
                      @csrf
                      <button class="sp-retake" type="submit" title="{{ $l['attempt']->banned() ? 'Lift ban' : 'Allow retake' }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 1 2.6 6.4M3 12v5.5M3 12H8.5"/></svg>
                      </button>
                    </form>
                  @endif
                </div>
              @endforeach
            </div>
          @else
            <div class="sp-empty">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4ZM7 6H4v1a3 3 0 0 0 3 3M17 6h3v1a3 3 0 0 1-3 3"/></svg>
              Nobody is taking it self-paced yet.
            </div>
          @endif
        </section>
      @endforeach
    </div>
  @endif
@endsection

@push('head')
<style>
  .sp-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(330px, 1fr)); gap: 18px; margin-top: 24px; align-items: start; }
  .sp-card { border-radius: 20px; border: 1px solid var(--glass-border); background: var(--glass); overflow: hidden;
    backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); }
  html[data-theme="light"] .sp-card { background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.04), 0 8px 24px -12px rgba(16,24,64,.12); }

  .sp-banner { position: relative; display: flex; align-items: center; gap: 12px; padding: 16px 18px;
    background: linear-gradient(135deg, var(--pc), color-mix(in srgb, var(--pc) 55%, #0b0b22)); overflow: hidden; }
  .sp-banner-pattern { position: absolute; inset: -50% -15% auto auto; width: 200px; height: 200px; border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,.22), transparent 70%); pointer-events: none; }
  .sp-logo { position: relative; width: 42px; height: 42px; border-radius: 12px; flex: none; display: grid; place-items: center; background: rgba(255,255,255,.22); }
  .sp-logo svg { width: 24px; height: 24px; }
  .sp-banner-text { position: relative; min-width: 0; flex: 1; }
  .sp-title { display: block; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.05rem; color: #fff; line-height: 1.25;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-shadow: 0 1px 3px rgba(0,0,0,.2); }
  .sp-sub { display: block; font-size: .78rem; color: rgba(255,255,255,.85); margin-top: 2px; }
  .sp-count { position: relative; flex: none; text-align: right; }
  .sp-count-n { display: block; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.3rem; color: #fff; line-height: 1; }
  .sp-count-l { display: block; font-size: .68rem; color: rgba(255,255,255,.8); margin-top: 2px; }

  .sp-stats { display: flex; flex-wrap: wrap; gap: 8px; padding: 14px 18px 0; }
  .sp-chip { display: inline-flex; align-items: center; gap: 6px; font-size: .78rem; font-weight: 600; color: var(--text);
    padding: 5px 10px; border-radius: 999px; background: color-mix(in srgb, var(--pc) 14%, transparent); }
  .sp-chip.muted { color: var(--muted); background: rgba(150,170,255,.1); }

  .sp-breakdown { display: flex; height: 8px; border-radius: 999px; overflow: hidden; margin: 14px 18px 0; background: rgba(150,170,255,.12); }
  .sp-seg { display: block; width: var(--w); background: var(--sc); }
  .sp-legend { display: flex; flex-wrap: wrap; gap: 10px 14px; padding: 8px 18px 2px; font-size: .74rem; color: var(--muted); }
  .sp-legend-item { display: inline-flex; align-items: center; gap: 5px; }
  .sp-legend-item i { display: inline-block; width: 7px; height: 7px; border-radius: 50%; }
  .sp-legend-item b { color: var(--text); font-weight: 700; }

  .sp-learners { display: grid; gap: 4px; padding: 10px 10px 14px; }
  .sp-learner { display: flex; align-items: center; gap: 10px; padding: 7px 8px; border-radius: 14px; }
  .sp-learner:hover { background: rgba(150,170,255,.08); }
  .sp-ring { --p: 0; width: 42px; height: 42px; border-radius: 50%; flex: none; display: grid; place-items: center;
    background: conic-gradient(var(--rc) calc(var(--p) * 1%), rgba(150,170,255,.16) 0); }
  .sp-avatar { width: 34px; height: 34px; border-radius: 50%; display: grid; place-items: center; background: var(--void);
    font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: .68rem; color: var(--text); }
  html[data-theme="light"] .sp-avatar { background: #fff; }
  .sp-who { min-width: 0; flex: 1; }
  .sp-name { font-weight: 600; font-size: .88rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .sp-pill { display: inline-flex; align-items: center; gap: 4px; margin-top: 2px; font-size: .72rem; font-weight: 600; color: var(--sc); }
  .sp-retake { flex: none; width: 30px; height: 30px; border-radius: 50%; display: grid; place-items: center; color: var(--muted);
    background: rgba(150,170,255,.1); border: 0; cursor: pointer; }
  .sp-retake:hover { background: rgba(115,182,255,.22); color: var(--blue); }

  .sp-empty { display: flex; align-items: center; gap: 10px; margin: 14px 18px 18px; padding: 14px; border-radius: 14px;
    border: 1px dashed var(--glass-border); color: var(--muted); font-size: .86rem; }
</style>
@endpush
