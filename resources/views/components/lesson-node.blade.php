@php
  $type = $node['type'] ?? 'locked';
  $title = $node['title'] ?? '';
  $sub = $node['sub'] ?? '';
  $num = str_pad(($index ?? 0) + 1, 2, '0');
  $isCurrent = $isCurrent ?? ($type === 'current');
  $stateClass = match ($type) {
    'completed' => 'completed',
    'current'   => 'current',
    'locked'    => 'locked',
    'milestone' => 'milestone',
    'loot'      => 'loot',
    default     => 'locked',
  };
  $tag = match ($type) {
    'milestone' => '🏆 CHECKPOINT',
    'loot'      => '⚡ BONUS QUEST',
    default     => 'MISSION ' . $num,
  };
@endphp

<div class="node {{ $stateClass }}">
  <div class="node-top">
    <span class="node-tag">{{ $tag }}</span>
    @if($type === 'completed')
      <span class="node-status">✓</span>
    @elseif($type === 'locked')
      <span class="node-status">🔒</span>
    @elseif($type === 'milestone')
      <span class="node-status">🏆</span>
    @elseif($type === 'loot')
      <span class="node-status">⚡</span>
    @elseif($isCurrent)
      <span class="node-status live">●</span>
    @endif
  </div>

  <h3 class="node-title">{{ $title }}</h3>
  <p class="node-sub">{{ $sub }}</p>

  @if($type === 'completed')
    <p class="node-desc">Nicely done — replay it anytime to sharpen your skills.</p>
    <div class="node-foot">
      <span class="xp">+50 XP earned</span>
      <a class="node-btn ghost" href="#">Review</a>
    </div>
  @elseif($isCurrent)
    <p class="node-desc">Complete this mission to bank XP and push toward the next level.</p>
    <div class="node-foot">
      <span class="xp">+50 XP · ≈ 15 min</span>
      <a class="node-btn" href="#">Continue Learning</a>
    </div>
  @elseif($type === 'locked')
    <p class="node-desc">Locked — finish the mission before this one to unlock it.</p>
    <div class="node-foot">
      <span class="xp locked">🔒 Locked</span>
    </div>
  @elseif($type === 'milestone')
    <p class="node-desc">A checkpoint that tests everything you've learned so far.</p>
    <div class="node-foot">
      <span class="xp">🏆 Milestone</span>
      <a class="node-btn ghost" href="#">View</a>
    </div>
  @elseif($type === 'loot')
    <p class="node-desc">Bonus practice to level up your mastery faster.</p>
    <div class="node-foot">
      <span class="xp">⚡ Bonus</span>
      <a class="node-btn ghost" href="#">Start</a>
    </div>
  @endif
</div>
