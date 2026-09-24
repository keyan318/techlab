@php
  $parts    = preg_split('/\s+/', trim($user->name), -1, PREG_SPLIT_NO_EMPTY);
  $initials = mb_strtoupper(count($parts) >= 2 ? mb_substr($parts[0], 0, 1).mb_substr(end($parts), 0, 1) : mb_substr($user->name, 0, 2));
  $chapterCount = count($chapters);
@endphp
<x-pixel-app :title="$title">

  {{-- ═══════════ COURSE HERO ═══════════ --}}
  <section class="relative isolate overflow-hidden" style="background: {{ $banner }}" aria-labelledby="course-h">
    @if ($scene)
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
        @if ($resume)
          <a href="{{ $resume['url'] }}" class="px-btn px-btn-yellow">{{ $resume['label'] }}</a>
        @endif
        <p class="text-[15px] text-white/90">
          {{ $chapterCount }} {{ \Illuminate\Support\Str::plural('chapter', $chapterCount) }} · {{ $lessonsTotal }} {{ \Illuminate\Support\Str::plural('lesson', $lessonsTotal) }} · <b>{{ $percent }}%</b> complete
        </p>
      </div>
    </div>
  </section>

  {{-- ═══════════ CHAPTERS + RAIL ═══════════ --}}
  <div class="mx-auto grid max-w-[1200px] items-start gap-10 px-4 py-10 sm:px-8 xl:grid-cols-[minmax(0,1fr)_340px]">

    <ol class="flex min-w-0 flex-col gap-10" aria-label="Chapters">
      @forelse ($chapters as $c)
        <li class="relative {{ ! $loop->last ? 'px-chain' : '' }}">
          <article class="px-card p-5 sm:p-8" aria-labelledby="ch-{{ $c['number'] }}">
            <header class="flex items-center gap-5">
              <div class="grid h-[60px] w-[60px] flex-none place-items-center rounded-full" style="background: conic-gradient(#8bd83a {{ $c['percent'] }}%, var(--px-track) 0)"
                   role="img" aria-label="Chapter {{ $c['number'] }}: {{ $c['done'] }} of {{ $c['total'] }} lessons done">
                <span class="grid h-[48px] w-[48px] place-items-center rounded-full bg-[var(--px-solid)]"><span class="px-font text-[1.5rem] font-semibold text-muted">{{ $c['number'] }}</span></span>
              </div>
              <h2 id="ch-{{ $c['number'] }}" class="px-font min-w-0 flex-1 text-[clamp(1.4rem,3vw,2rem)] font-medium leading-tight">{{ $c['title'] }}</h2>
              @if ($c['complete'])
                <span class="hidden flex-none items-center gap-1.5 rounded-full px-3 py-1 text-[13px] font-semibold px-good sm:inline-flex">
                  <x-px-icon name="check" :size="14" /> Cleared
                </span>
              @endif
            </header>

            <ul class="px-inner px-rows mt-6 rounded-sm px-4 py-2 sm:px-6">
              @foreach ($c['lessons'] as $lesson)
                <li class="flex items-center gap-4 py-3">
                  <span class="hidden w-20 flex-none text-[15px] text-muted sm:block">Lesson {{ $lesson['number'] }}</span>
                  <div class="min-w-0 flex-1">
                    <span class="block text-[12px] text-muted sm:hidden">Lesson {{ $lesson['number'] }}</span>
                    <span class="block text-[16px] {{ $lesson['state'] === 'locked' ? 'text-muted' : 'font-medium' }}">{{ $lesson['title'] }}</span>
                  </div>

                  @if ($lesson['state'] === 'done')
                    <a href="{{ $lesson['url'] }}" class="px-btn min-w-[128px]" aria-label="Lesson {{ $lesson['number'] }}, {{ $lesson['title'] }}: done, open to review">
                      <x-px-icon name="check" :size="16" /> Done!
                    </a>
                  @elseif ($lesson['state'] === 'current')
                    <a href="{{ $lesson['url'] }}" class="px-btn px-btn-yellow min-w-[128px]" aria-label="Lesson {{ $lesson['number'] }}, {{ $lesson['title'] }}: start, earns {{ $lesson['xp'] }} XP">+{{ $lesson['xp'] }} XP</a>
                  @else
                    <span class="px-btn px-btn-locked min-w-[128px]" aria-label="Lesson {{ $lesson['number'] }}, {{ $lesson['title'] }}: locked until the lesson before it is done">
                      <x-px-icon name="lock" :size="16" /> Locked
                    </span>
                  @endif
                </li>
              @endforeach
            </ul>
          </article>
        </li>
      @empty
        <li class="px-card p-8">
          <p class="px-font text-2xl font-medium">Lessons are on the way</p>
          <p class="mt-2 text-muted">This course doesn't have tracked lessons yet. Check back soon.</p>
        </li>
      @endforelse
    </ol>

    {{-- Rail: you, your progress, your badges --}}
    <aside class="flex min-w-0 flex-col gap-8 xl:sticky xl:top-6" aria-label="Your course progress">
      <section class="px-card p-6" aria-label="Profile">
        <div class="flex items-center gap-4">
          <div class="px-avatar" aria-hidden="true">{{ $initials }}</div>
          <div class="min-w-0">
            <p class="truncate text-[1.4rem] font-semibold leading-tight">{{ $user->name }}</p>
            <p class="text-[15px] text-muted">Level {{ $level }}</p>
          </div>
        </div>
        <a href="{{ route('student.progress') }}" class="px-btn mt-6 w-[calc(100%-6px)]">View progress</a>
      </section>

      <section class="px-card p-6" aria-labelledby="cp-h">
        <h2 id="cp-h" class="text-[1.35rem] font-semibold">Course Progress</h2>
        <div class="mt-6 flex flex-col gap-6">
          <div class="flex items-center gap-4" role="progressbar" aria-valuenow="{{ $lessonsDone }}" aria-valuemin="0" aria-valuemax="{{ $lessonsTotal }}" aria-label="Lessons done">
            <x-px-icon name="book" :size="32" />
            <div class="min-w-0 flex-1">
              <div class="flex justify-between text-[15px] font-medium"><span>Lessons</span><span class="text-muted">{{ $lessonsDone }} / {{ $lessonsTotal }}</span></div>
              <div class="px-bar mt-2"><i style="width: {{ $lessonsTotal ? $lessonsDone / $lessonsTotal * 100 : 0 }}%"></i></div>
            </div>
          </div>
          <div class="flex items-center gap-4" role="progressbar" aria-valuenow="{{ $xpEarned }}" aria-valuemin="0" aria-valuemax="{{ $xpTotal }}" aria-label="XP earned">
            <x-px-icon name="star" :size="32" />
            <div class="min-w-0 flex-1">
              <div class="flex justify-between text-[15px] font-medium"><span>XP Earned</span><span class="text-muted">{{ number_format($xpEarned) }} / {{ number_format($xpTotal) }}</span></div>
              <div class="px-bar mt-2"><i style="width: {{ $xpTotal ? $xpEarned / $xpTotal * 100 : 0 }}%"></i></div>
            </div>
          </div>
        </div>
      </section>

      <section class="px-card p-6" aria-labelledby="cb-h">
        <div class="flex items-baseline justify-between">
          <h2 id="cb-h" class="text-[1.35rem] font-semibold">Course Badges</h2>
          <span class="text-[15px] text-muted">{{ $badgesEarned }} / {{ $chapterCount }}</span>
        </div>
        <p class="mt-2 text-[15px] leading-snug text-muted">Complete a chapter to earn a badge. Collect 'em all!</p>
        <ul class="mt-5 flex flex-wrap gap-4">
          @foreach ($chapters as $c)
            <li title="Chapter {{ $c['number'] }}: {{ $c['title'] }}{{ $c['complete'] ? ' (earned)' : ' (locked)' }}"
                class="relative {{ $c['complete'] ? '' : 'opacity-40 grayscale' }}">
              <x-px-icon name="gem" :size="44" />
              <span class="sr-only">Chapter {{ $c['number'] }} badge, {{ $c['complete'] ? 'earned' : 'locked' }}</span>
              <span class="px-font pointer-events-none absolute inset-0 grid place-items-center pt-1 text-[13px] font-bold text-[#06284a]" aria-hidden="true">{{ $c['number'] }}</span>
            </li>
          @endforeach
        </ul>
      </section>
    </aside>
  </div>
</x-pixel-app>
