@php
  $pct = ($xpForNext ?? 0) > 0 ? min(100, round(($xp ?? 0) / $xpForNext * 100)) : 0;
@endphp

<div class="level-progress">
  <div class="lp-head">
    <span class="lp-level">LEVEL {{ $level ?? 1 }}</span>
    <span class="lp-title">{{ $levelTitle ?? 'Explorer' }}</span>
  </div>
  <div class="level-bar">
    <div class="level-fill" style="width:{{ $pct }}%"></div>
  </div>
  <div class="lp-xp">{{ $xp ?? 0 }} / {{ $xpForNext ?? 0 }} XP · {{ ($xpForNext ?? 0) - ($xp ?? 0) }} XP to Level {{ ($level ?? 1) + 1 }}</div>
</div>
