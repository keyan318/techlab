<x-pixel-app :title="$title">
  @include('student.course.partials.hero')

  <div class="mx-auto grid max-w-[1200px] items-start gap-10 px-4 py-10 sm:px-8 xl:grid-cols-[minmax(0,1fr)_340px]">
    <div class="flex min-w-0 flex-col gap-10">

      {{-- ═══════════ THE LESSONS ═══════════ --}}
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

      @if ($lessonsTotal > 0)
        @include('student.course.partials.final-boss')
      @endif
    </div>

    @include('student.course.partials.rail')
  </div>
</x-pixel-app>
