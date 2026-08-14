@php
  $s = $student ?? [];
  $name = $s['name'] ?? 'Explorer';
  $level = $s['level'] ?? 1;
  $levelTitle = $s['levelTitle'] ?? 'Explorer';
  $xp = $s['xp'] ?? 0;
  $xpForNext = $s['xpForNext'] ?? 1000;
  $streak = $s['streak'] ?? 0;
  $dq = $s['dailyQuest'] ?? ['label' => 'Complete 1 lesson', 'progress' => 0, 'goal' => 1];
  $achievements = $s['achievements'] ?? [];
@endphp

<div class="sidebar">
  <div class="stat-card profile">
    <div class="avatar">{{ strtoupper(substr($name, 0, 1)) }}</div>
    <div>
      <div class="hey">Hey, {{ $name }}!</div>
      <div class="lvl">Level {{ $level }} · {{ $levelTitle }}</div>
    </div>
  </div>

  <div class="stat-card">
    <x-level-progress :level="$level" :level-title="$levelTitle" :xp="$xp" :xp-for-next="$xpForNext" />
  </div>

  <x-daily-quest :label="$dq['label']" :progress="$dq['progress']" :goal="$dq['goal']" />

  <div class="stat-card streak">
    <div class="k">🔥 Streak</div>
    <div class="streak-num">{{ $streak }} Day Streak</div>
    <div class="streak-sub">Keep it going!</div>
  </div>

  <div class="stat-card">
    <div class="k">Achievements</div>
    <div class="achs">
      @foreach($achievements as $a)
        <x-achievement-card :icon="$a['icon']" :label="$a['label']" />
      @endforeach
    </div>
  </div>
</div>
