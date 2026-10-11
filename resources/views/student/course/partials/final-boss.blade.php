{{-- The last stop after every chapter: the faculty's final exam. Beat it to earn the certificate. --}}
@php
  $fx = $finalExam;
  $captainName = $crew?->faculty?->name ?? 'your captain';
@endphp

<section class="px-card relative overflow-hidden p-5 sm:p-8" aria-labelledby="boss-h"
         style="background: linear-gradient(135deg, rgba(229,72,77,0.18), rgba(255,196,0,0.10) 60%, transparent), var(--px-solid, #12102e)">
  <div class="flex flex-wrap items-center gap-5 sm:gap-7">
    {{-- A little pixel boss. --}}
    <svg viewBox="0 0 16 16" width="76" height="76" shape-rendering="crispEdges" aria-hidden="true"
         class="flex-none {{ in_array($fx['state'], ['locked', 'not_set']) ? 'opacity-50 grayscale' : '' }}">
      <rect x="3" y="1" width="2" height="2" fill="#ffc400"/><rect x="11" y="1" width="2" height="2" fill="#ffc400"/>
      <rect x="4" y="3" width="8" height="1" fill="#e5484d"/>
      <rect x="2" y="4" width="12" height="7" fill="#e5484d"/>
      <rect x="1" y="6" width="1" height="4" fill="#e5484d"/><rect x="14" y="6" width="1" height="4" fill="#e5484d"/>
      <rect x="4" y="6" width="3" height="2" fill="#fff"/><rect x="9" y="6" width="3" height="2" fill="#fff"/>
      <rect x="5" y="7" width="1" height="1" fill="#06061a"/><rect x="10" y="7" width="1" height="1" fill="#06061a"/>
      <rect x="5" y="9" width="6" height="1" fill="#06061a"/>
      <rect x="5" y="9" width="1" height="1" fill="#fff"/><rect x="7" y="9" width="1" height="1" fill="#fff"/><rect x="9" y="9" width="1" height="1" fill="#fff"/>
      <rect x="3" y="11" width="10" height="2" fill="#b3262b"/>
      <rect x="3" y="13" width="2" height="2" fill="#b3262b"/><rect x="7" y="13" width="2" height="2" fill="#b3262b"/><rect x="11" y="13" width="2" height="2" fill="#b3262b"/>
    </svg>

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
            · score {{ $fx['passPercent'] }}% or more to win. One try, full screen, no leaving.
            @break
          @case('passed')
            Defeated with {{ $fx['percent'] }}%. Your certificate is ready!
            @break
          @case('failed')
            @if ($fx['violation'])
              The exam ended early: {{ strtolower($fx['violation']) }}. {{ $captainName }} has been told and can let you try again.
            @else
              You scored {{ $fx['percent'] }}% and needed {{ $fx['passPercent'] }}%. Talk to {{ $captainName }}: they can let you try again.
            @endif
            @break
        @endswitch
      </p>
    </div>

    @if ($fx['state'] === 'ready')
      <a href="{{ $fx['url'] }}" class="px-btn px-btn-yellow min-w-[160px]">Face the boss</a>
    @elseif ($fx['state'] === 'passed')
      <a href="{{ $certificateUrl }}" class="px-btn px-btn-yellow min-w-[160px]"><x-px-icon name="medal" :size="18" /> Certificate</a>
    @elseif ($fx['state'] === 'failed')
      <span class="rounded-full px-3 py-1 text-[13px] font-semibold text-[#ff6b81]" style="background: rgba(255,107,129,0.12)">Not passed</span>
    @else
      <span class="px-btn px-btn-locked min-w-[160px]"><x-px-icon name="lock" :size="16" /> Locked</span>
    @endif
  </div>
</section>
