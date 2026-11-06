{{-- A mini boss's lobby on the exam page: its rules (self-paced or class), or where the student stands with it. --}}
@php
  $who = $captain ?? 'your captain';
  $next = $mini['last'] ? 'the Final Boss' : 'Module '.($mini['module'] + 1);
@endphp
<p class="text-[13px] font-semibold uppercase tracking-[0.2em] text-[#ffc400]">Mini Boss · Module {{ $mini['module'] }} · {{ $courseTitle }}</p>
<h1 class="px-font mt-2 text-[clamp(2rem,6vw,3.2rem)] font-bold leading-none">{{ $exam?->title ?? 'Mini Boss: '.$mini['moduleTitle'] }}</h1>

@if ($state === 'ready' && $closed)
  <p class="mt-5 text-[16px] leading-relaxed text-muted">This mini boss closed on {{ $exam->due_at->format('j M Y, g:ia') }}. Talk to {{ $who }} about what happens next.</p>
@elseif ($state === 'ready')
  @if ($exam->description)
    <p class="mt-4 text-[16px] leading-relaxed text-muted">{{ $exam->description }}</p>
  @endif

  <p class="mt-5 text-[15px] text-muted">
    {{ $exam->questions_count }} {{ \Illuminate\Support\Str::plural('question', $exam->questions_count) }}
    @if ($exam->minutes) · {{ $exam->minutes }} minutes @endif
    · pass mark <b class="text-ink">{{ $exam->passPercent() }}%</b>
    · <b class="text-ink">+{{ $mini['xpPerCorrect'] }} XP</b> for every right answer
  </p>

  <div class="px-inner mt-6 rounded-sm p-5">
    <p class="font-semibold">Rules of the arena</p>
    <ul class="mt-3 flex list-disc flex-col gap-2 pl-5 text-[15px] leading-relaxed">
      <li>The mini boss runs in <b>full screen</b>, against the clock.</li>
      <li><b>Leaving full screen</b> (Esc), <b>switching tabs or windows</b> (Alt+Tab, Cmd+Tab), or <b>reloading the page</b> ends it right away, and you <b>fail</b>.</li>
      <li>Screenshots, copy, paste and right-click are blocked. Trying to take a screenshot also ends it.</li>
      <li>{{ $mini['selfPaced'] ? "TechLab's admin" : $who }} is told if your mini boss ends early.</li>
      @if ($mini['selfPaced'])
        <li><b>Win</b> ({{ $exam->passPercent() }}% or more) and you keep {{ $mini['xpPerCorrect'] }} XP for every right answer, and {{ $next }} unlocks.</li>
        <li><b>Lose</b> and you get <b>no XP</b>. {{ $next === 'the Final Boss' ? 'The Final Boss stays' : $next.' stays' }} locked, and you can try again after {{ $mini['cooldown'] }} minutes of studying this module.</li>
      @else
        <li>You get <b>one try</b>. Win or lose, you move on to {{ $next }}.</li>
        <li>You keep {{ $mini['xpPerCorrect'] }} XP for every right answer, and {{ $who }} sees your score.</li>
      @endif
      <li>When it's over you see your score, but not the answers.</li>
      @if ($exam->minutes)
        <li>When the timer reaches zero, your answers are handed in automatically.</li>
      @endif
    </ul>
  </div>

  <label class="mt-6 flex cursor-pointer items-start gap-3 text-[15px]">
    <input type="checkbox" class="mt-1 accent-[#ffc400]" x-model="agreed">
    <span>I understand the rules. If I leave the exam, I fail.</span>
  </label>

  <p x-show="error" x-cloak class="mt-4 rounded-[10px] border border-[rgba(255,107,129,0.35)] bg-[rgba(255,107,129,0.1)] px-4 py-3 text-[14px] text-[#ff6b81]" x-text="error"></p>

  <button type="button" class="px-btn px-btn-blue mt-6" :disabled="!agreed || phase === 'starting'" @click="start()"
          x-text="phase === 'starting' ? 'Entering the arena…' : 'Enter full screen and start'"></button>
@elseif ($state === 'locked')
  <p class="mt-5 text-[16px] leading-relaxed text-muted">Clear every lesson of Module {{ $mini['module'] }} first. The mini boss opens after that.</p>
  <a href="{{ $backUrl }}" class="px-btn mt-6">Back to the lessons</a>
@elseif ($state === 'not_set')
  <p class="mt-5 text-[16px] leading-relaxed text-muted">You cleared Module {{ $mini['module'] }}! {{ ucfirst($who) }} hasn't set this mini boss yet, so carry on.</p>
  <a href="{{ $backUrl }}" class="px-btn mt-6">Back to the course</a>
@elseif ($state === 'passed')
  <p class="mt-5 text-[16px] leading-relaxed">You beat this mini boss with <b>{{ $result['percent'] }}%</b> and earned <b>{{ $result['xp'] }} XP</b>. 🎉</p>
  <a href="{{ $backUrl }}" class="px-btn px-btn-blue mt-6">On to {{ $next }}</a>
@elseif ($state === 'cooldown')
  <p class="mt-5 text-[16px] leading-relaxed text-muted">
    @if ($result['violation'])
      Your last try ended early: <b class="text-[#ff6b81]">{{ strtolower($result['violation']) }}</b>.
    @else
      Last time you scored <b class="text-ink">{{ $result['percent'] }}%</b> and needed {{ $result['pass_percent'] }}%.
    @endif
    Study Module {{ $mini['module'] }} again. You can face the mini boss
    <b class="text-ink" x-data="{ at: new Date(@js($mini['retryAt']?->toISOString())) }"
       x-text="at.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })">{{ $mini['retryAt']?->diffForHumans() }}</b>.
  </p>
  <a href="{{ $backUrl }}" class="px-btn mt-6">Back to the lessons</a>
@else
  {{-- A class: one try, recorded, and they carry on. --}}
  <p class="mt-5 text-[16px] leading-relaxed text-muted">
    @if ($result['violation'])
      Your mini boss ended early: <b class="text-[#ff6b81]">{{ strtolower($result['violation']) }}</b>. {{ ucfirst($who) }} has been told.
    @else
      You scored <b class="text-ink">{{ $result['percent'] }}%</b> ({{ $result['pass_percent'] }}% to win) and earned <b class="text-ink">{{ $result['xp'] }} XP</b>.
    @endif
    Your score is recorded. Carry on to {{ $next }}.
  </p>
  <a href="{{ $backUrl }}" class="px-btn px-btn-blue mt-6">On to {{ $next }}</a>
@endif
