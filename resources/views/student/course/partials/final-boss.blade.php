{{-- The last stop after every chapter: the faculty's final exam. Beat it to earn the certificate. --}}
@php
  $fx = $finalExam;
  $captainName = $crew?->faculty?->name ?? ($crew?->isSelfPaced() ? "TechLab's admin" : 'your captain');
@endphp

<section class="px-card relative overflow-hidden p-5 sm:p-8" aria-labelledby="boss-h" data-tour="course-boss"
         style="background: linear-gradient(135deg, rgba(229,72,77,0.18), rgba(255,196,0,0.10) 60%, transparent), var(--px-solid, #12102e)">
  <div class="flex flex-wrap items-center gap-5 sm:gap-7">
    <x-boss-sprite :size="76" class="flex-none {{ in_array($fx['state'], ['locked', 'not_set']) ? 'opacity-50 grayscale' : '' }}" />

    <div class="min-w-0 flex-1">
      <p class="text-[13px] font-semibold uppercase tracking-[0.2em] text-[#ffc400]">Final Boss</p>
      <h2 id="boss-h" class="px-font mt-1 text-[clamp(1.4rem,3vw,2rem)] font-medium leading-tight">
        {{ $fx['title'] ?? 'The Final Exam' }}
      </h2>

      <p class="mt-2 text-[15px] leading-relaxed text-muted">
        @switch($fx['state'])
          @case('locked')
            Clear every chapter to face the Final Boss. Beat it and the certificate is yours.
            @break
          @case('not_set')
            You cleared every chapter! {{ $captainName }} hasn't set the final exam yet. It shows up here as soon as they do.
            @break
          @case('ready')
            {{ $fx['questions'] }} {{ \Illuminate\Support\Str::plural('question', $fx['questions']) }}
            @if ($fx['minutes']) · {{ $fx['minutes'] }} min @endif
            · score {{ $fx['passPercent'] }}% or more to win. Full screen, no leaving. Fail it and you redo the course.
            @break
          @case('passed')
            Defeated with {{ $fx['percent'] }}%. Your certificate is ready!
            @break
          @case('failed')
            @if ($fx['violation'])
              The exam ended early: {{ strtolower($fx['violation']) }}.
            @else
              You scored {{ $fx['percent'] }}% and needed {{ $fx['passPercent'] }}%.
            @endif
            @if ($fx['banned'] ?? false)
              That was your second strike, so you're banned from the Final Boss. Only {{ $captainName }} can lift it.
            @elseif ($fx['redo'] ?? false)
              Redo every chapter from the start and the Final Boss opens again.
            @else
              Talk to {{ $captainName }}: they can let you try again.
            @endif
            @break
        @endswitch
      </p>
    </div>

    @if ($fx['state'] === 'ready')
      <a href="{{ $fx['url'] }}" class="px-btn px-btn-blue min-w-[160px]">Face the boss</a>
    @elseif ($fx['state'] === 'passed')
      <a href="{{ $certificateUrl }}" class="px-btn px-btn-blue min-w-[160px]"><x-px-icon name="medal" :size="18" /> Certificate</a>
    @elseif ($fx['state'] === 'failed')
      <span class="rounded-full px-3 py-1 text-[13px] font-semibold text-[#ff6b81]" style="background: rgba(255,107,129,0.12)">Not passed</span>
    @else
      <span class="px-btn px-btn-locked min-w-[160px]"><x-px-icon name="lock" :size="16" /> Locked</span>
    @endif
  </div>
</section>
