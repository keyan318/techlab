{{-- How a mini boss ended, on the exam page (filled in from the server's `result`). --}}
@php $next = $mini['last'] ? 'the Final Boss' : 'Module '.($mini['module'] + 1); @endphp
<template x-if="result && result.caught">
  <div>
    <p class="text-[13px] font-semibold uppercase tracking-[0.2em] text-[#ff6b81]">Mini boss ended</p>
    <h1 class="px-font mt-3 text-[clamp(2rem,6vw,3rem)] font-bold leading-none">You left the arena</h1>
    <p class="mt-5 text-[16px] leading-relaxed text-muted">
      <span x-text="result.violation"></span>. That counts as a fail with no XP, and {{ $mini['selfPaced'] ? "TechLab's admin" : ($captain ?? 'your captain') }} has been told.
      @if ($mini['selfPaced'])
        Study Module {{ $mini['module'] }} again: you can try again in {{ $mini['cooldown'] }} minutes.
      @else
        Your score is recorded. Carry on to {{ $next }}.
      @endif
    </p>
  </div>
</template>
<template x-if="result && !result.caught && result.status === 'passed'">
  <div>
    <p class="text-[13px] font-semibold uppercase tracking-[0.2em] text-[#ffc400]">Victory</p>
    <h1 class="px-font mt-3 text-[clamp(2rem,6vw,3rem)] font-bold leading-none">Mini boss defeated!</h1>
    <p class="mt-5 text-[18px]">You scored <b x-text="result.percent + '%'"></b> (<span x-text="result.score"></span>/<span x-text="result.total"></span>) and earned <b x-text="'+' + result.xp + ' XP'"></b>.</p>
    <p class="mt-2 text-[16px] text-muted">{{ ucfirst($next) }} is open.</p>
  </div>
</template>
<template x-if="result && !result.caught && result.status === 'failed'">
  <div>
    <p class="text-[13px] font-semibold uppercase tracking-[0.2em] text-[#ff6b81]">Not this time</p>
    <h1 class="px-font mt-3 text-[clamp(2rem,6vw,3rem)] font-bold leading-none">The mini boss survived</h1>
    <p class="mt-5 text-[16px] leading-relaxed text-muted">
      <span x-show="result.violation">Time ran out before you handed in.</span>
      <span x-show="!result.violation">You scored <b class="text-ink" x-text="result.percent + '%'"></b> (<span x-text="result.score"></span>/<span x-text="result.total"></span>) and needed <span x-text="result.pass_percent + '%'"></span>.</span>
      @if ($mini['selfPaced'])
        No XP this time. Study Module {{ $mini['module'] }} again: you can try again in {{ $mini['cooldown'] }} minutes.
      @else
        <span x-show="result.xp">You still earned <b class="text-ink" x-text="result.xp + ' XP'"></b>.</span> Your score is recorded. Carry on to {{ $next }}.
      @endif
    </p>
  </div>
</template>
<template x-if="!result">
  <div>
    <h1 class="px-font text-[2rem] font-bold">Mini boss ended</h1>
    <p class="mt-4 text-muted">We couldn't reach TechLab to confirm your result. Reload this page to see it.</p>
  </div>
</template>
