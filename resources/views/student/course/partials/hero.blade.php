{{-- The course header + tab bar, shared by all three hub tabs. --}}
<section class="relative isolate overflow-hidden" style="background: {{ $banner }}" aria-labelledby="course-h">
  @if ($art)
    {{-- The course's pixel art, the same picture as its card on the course list. --}}
    <img src="{{ asset($art) }}" alt="" aria-hidden="true"
         class="px-art h-full w-full object-cover object-bottom" style="image-rendering: pixelated;"
         width="1250" height="500" decoding="async">
  @elseif ($scene)
    <div class="px-art" aria-hidden="true"><x-dynamic-component :component="$scene" /></div>
  @endif
  <div class="px-scrim" aria-hidden="true"></div>

  <div class="px-onart relative z-10 mx-auto max-w-[1200px] px-4 py-10 sm:px-8 sm:py-14">
    <a href="{{ $backUrl }}" class="text-[14px] text-white/80 underline-offset-4 hover:text-white hover:underline">← All courses</a>

    <div class="mt-6 flex flex-wrap items-center gap-3 text-[13px] font-medium uppercase tracking-[0.2em]">
      @if ($courseLevel)
        <span class="px-pill rounded-full px-4 py-1.5">{{ $courseLevel }}</span>
      @endif
      <span class="text-white/85">Course</span>
    </div>

    <h1 id="course-h" class="px-font mt-5 text-[clamp(2.6rem,7vw,4.6rem)] font-bold leading-none">{{ $title }}</h1>
    <p class="mt-5 max-w-[56ch] text-[clamp(1rem,1.7vw,1.25rem)] leading-relaxed text-white/90">{{ $description }}</p>

    <div class="mt-8 flex flex-wrap items-center gap-x-8 gap-y-5">
      @php $bossReady = $finalExam['state'] === 'ready'; @endphp
      @if ($certificateUrl)
        <a href="{{ $certificateUrl }}" class="px-btn px-btn-yellow"><x-px-icon name="medal" :size="20" /> View Certificate</a>
      @elseif ($bossReady)
        <a href="{{ $finalExam['url'] }}" class="px-btn px-btn-yellow">Face the Final Boss</a>
      @endif
      @if ($resume)
        <a href="{{ $resume['url'] }}" class="px-btn {{ $certificateUrl || $bossReady ? 'px-btn-ghost' : 'px-btn-yellow' }}">{{ $resume['label'] }}</a>
      @endif
      <p class="text-[15px] text-white/90">
        {{ count($chapters) }} {{ \Illuminate\Support\Str::plural('chapter', count($chapters)) }} · {{ $lessonsTotal }} {{ \Illuminate\Support\Str::plural('lesson', $lessonsTotal) }} · <b>{{ $percent }}%</b> complete
      </p>
    </div>
  </div>
</section>

<x-course-tabs :slug="$slug" :course="$course" :tab="$tab" :quiz-badge="$quizBadge" :materials-badge="$materialsBadge" />
