{{-- The last row of a chapter: its mini boss. Self-paced has to beat it to open the next chapter. --}}
@php
  $label = "Chapter {$chapter} mini boss";
  $stats = $boss['questions'].' '.\Illuminate\Support\Str::plural('question', $boss['questions']).' · '.$boss['minutes'].' min · '.$boss['passPercent'].'% to win';
@endphp
<li class="flex items-center gap-4 py-3">
  <span class="hidden w-20 flex-none text-[15px] font-semibold text-[#ffc400] sm:block">Boss</span>
  <x-boss-sprite :size="34" class="flex-none {{ in_array($boss['state'], ['locked', 'not_set']) ? 'opacity-50 grayscale' : '' }}" />
  <div class="min-w-0 flex-1">
    <span class="block text-[16px] {{ $boss['state'] === 'locked' ? 'text-muted' : 'font-medium' }}">Mini Boss</span>
    <span class="block text-[13px] text-muted">
      @switch($boss['state'])
        @case('passed')
          Beaten with {{ $boss['percent'] }}% · +{{ $boss['xp'] }} XP
          @break
        @case('failed')
          @if ($boss['violation']) Ended early: {{ strtolower($boss['violation']) }} @else Scored {{ $boss['percent'] }}% · +{{ $boss['xp'] }} XP @endif · recorded
          @break
        @case('cooldown')
          Study this chapter, then try again
          @break
        @case('not_set')
          Your captain hasn't set this one yet
          @break
        @default
          {{ $stats }}{{ $boss['selfPaced'] ? ' · beat it to go on' : '' }}
      @endswitch
    </span>
  </div>

  @if ($boss['state'] === 'ready')
    <a href="{{ $boss['url'] }}" class="px-btn px-btn-blue min-w-[128px]" aria-label="{{ $label }}: face it, {{ $stats }}">Face it</a>
  @elseif ($boss['state'] === 'passed')
    <a href="{{ $boss['url'] }}" class="px-btn min-w-[128px]" aria-label="{{ $label }}: beaten"><x-px-icon name="check" :size="16" /> Beaten</a>
  @elseif ($boss['state'] === 'failed')
    <a href="{{ $boss['url'] }}" class="px-btn min-w-[128px]" aria-label="{{ $label }}: taken, score recorded">{{ $boss['percent'] }}%</a>
  @elseif ($boss['state'] === 'cooldown')
    <a href="{{ $boss['url'] }}" class="px-btn px-btn-locked min-w-[128px]" aria-label="{{ $label }}: try again later"
       x-data="{ at: new Date(@js($boss['retryAt']?->toISOString())) }"
       x-text="at.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })">{{ $boss['retryAt']?->format('g:i A') }}</a>
  @else
    <span class="px-btn px-btn-locked min-w-[128px]" aria-label="{{ $label }}: locked until every lesson of the chapter is done">
      <x-px-icon name="lock" :size="16" /> Locked
    </span>
  @endif
</li>
