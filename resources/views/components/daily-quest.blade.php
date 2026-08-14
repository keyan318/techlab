@php
  $progress = $progress ?? 0;
  $goal = $goal ?? 1;
  $pct = $goal > 0 ? min(100, round($progress / $goal * 100)) : 0;
@endphp

<div class="stat-card">
  <div class="k">Daily Mission</div>
  <div class="dq-label">{{ $label ?? 'Complete 1 lesson' }}</div>
  <div class="level-bar"><div class="level-fill" style="width:{{ $pct }}%"></div></div>
  <div class="dq-count">{{ $progress }} / {{ $goal }}</div>
</div>
