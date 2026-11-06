{{--
  One leaderboard row (StudentDashboardService::rank): place, the astronaut in their frame, name, and rank badge
  with level. The top 3 glow in their medal colour; rows slide in one after another.
  $row, $i (row index, for the slide-in delay), $frameSize (px).
--}}
@php
  $medals = ['#f5c04a', '#c4ccdd', '#d99562'];
  $medal = $row['rank'] <= 3 ? $medals[$row['rank'] - 1] : null;
@endphp
<li @class(['lb-row px-inner flex items-center gap-3 px-3 py-2 sm:gap-4', 'px-me' => $row['me'], 'lb-top' => $medal])
    style="--i: {{ min($i, 12) }}; @if ($medal) --medal: {{ $medal }}; @endif" @if ($row['me']) aria-current="true" @endif>
  <span class="px-font flex h-7 w-7 flex-none items-center justify-center text-[13px] font-bold {{ $medal ? 'text-[#0b0b22]' : 'bg-[var(--px-track)] text-muted' }}"
        @if ($medal) style="background: {{ $medal }}" @endif>{{ $row['rank'] }}</span>
  <x-avatar-frame :frame="$row['frame']" :level="$row['level']" :look="$row['avatar']" :name="$row['name']" :size="$frameSize ?? 104" />
  <div class="min-w-0 flex-1">
    <p class="truncate text-[15px] {{ $row['me'] ? 'font-semibold' : 'font-medium' }}">{{ $row['name'] }}@if ($row['me']) <span class="text-muted">(you)</span>@endif</p>
    <x-rank-badge :level="$row['level']" size="sm" class="mt-1" />
  </div>
</li>
