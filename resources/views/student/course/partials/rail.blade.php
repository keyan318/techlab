{{-- Right rail: who you are, how far you've got, what you've earned. --}}
@php
  $parts    = preg_split('/\s+/', trim($user->name), -1, PREG_SPLIT_NO_EMPTY);
  $initials = mb_strtoupper(count($parts) >= 2 ? mb_substr($parts[0], 0, 1).mb_substr(end($parts), 0, 1) : mb_substr($user->name, 0, 2));
@endphp

<aside class="flex min-w-0 flex-col gap-8 xl:sticky xl:top-6" aria-label="Your course progress" data-tour="course-rail">
  <section class="px-card p-6" aria-label="Profile">
    <div class="flex items-center gap-4">
      @if ($user->avatar)
        <div class="px-avatar px-avatar-astro" aria-hidden="true"><x-astronaut :look="$user->avatar" class="h-[86px] w-auto" /></div>
      @else
        <div class="px-avatar" aria-hidden="true">{{ $initials }}</div>
      @endif
      <div class="min-w-0">
        <p class="truncate text-[1.4rem] font-semibold leading-tight">{{ $user->display_name }}</p>
        <p class="text-[15px] text-muted">Level {{ $level }}</p>
      </div>
    </div>
    <a href="{{ route('student.progress') }}" class="px-btn mt-6 w-[calc(100%-6px)]">View progress</a>
  </section>

  @if ($syllabus)
    {{-- The syllabus is the first thing a student should be able to check, before Start Learning.
      Click the card to preview it in place (Google Classroom-style), instead of an inline PDF viewer. --}}
    @php
      $syllabusUrl = url($syllabus['url']);
      $syllabusGoogleUrl = 'https://docs.google.com/viewer?url='.urlencode($syllabusUrl).'&embedded=false';
      $syllabusGoogleEmbedUrl = 'https://docs.google.com/viewer?url='.urlencode($syllabusUrl).'&embedded=true';
    @endphp
    <section class="px-card p-6" aria-labelledby="syl-h">
      <h2 id="syl-h" class="text-[1.35rem] font-semibold">Syllabus</h2>
      <button type="button"
        @click="$store.filePreview.open({
          name: @js($syllabus['title']),
          kind: 'pdf',
          viewUrl: @js($syllabusUrl),
          downloadUrl: @js($syllabusUrl),
          googleUrl: @js($syllabusGoogleUrl),
          googleEmbedUrl: @js($syllabusGoogleEmbedUrl),
        })"
        class="mt-3 flex w-full items-center gap-3 rounded-[10px] border border-[var(--px-border,#e2e2e2)] px-3 py-2.5 text-left transition hover:bg-glass">
        <span class="grid h-9 w-9 flex-none place-items-center rounded-[8px] bg-glass font-mono text-[10px] font-bold uppercase text-[#73b6ff]">PDF</span>
        <span class="min-w-0 flex-1">
          <span class="block truncate text-[15px] font-medium">{{ $syllabus['title'] }}</span>
          <span class="block text-[13px] text-muted">Tap to preview</span>
        </span>
        <x-px-icon name="book" :size="16" />
      </button>
    </section>
  @endif

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
      <h2 id="cb-h" class="text-[1.35rem] font-semibold">Course Gems</h2>
      <span class="text-[15px] text-muted">{{ $badgesEarned }} / {{ count($chapters) }}</span>
    </div>
    <p class="mt-2 text-[15px] leading-snug text-muted">Complete a chapter to earn a gem. Collect 'em all!</p>
    <ul class="mt-5 flex flex-wrap gap-4">
      @foreach ($chapters as $c)
        <li title="Chapter {{ $c['number'] }}: {{ $c['title'] }}{{ $c['complete'] ? ' (earned)' : ' (locked)' }}"
            class="relative {{ $c['complete'] ? '' : 'opacity-40 grayscale' }}">
          <x-px-icon name="gem" :size="44" />
          <span class="sr-only">Chapter {{ $c['number'] }} gem, {{ $c['complete'] ? 'earned' : 'locked' }}</span>
          <span class="px-font pointer-events-none absolute inset-0 grid place-items-center pt-1 text-[13px] font-bold text-[#06284a]" aria-hidden="true">{{ $c['number'] }}</span>
        </li>
      @endforeach
    </ul>
  </section>

  @if ($lessonsTotal > 0)
    <section class="px-card p-6" aria-labelledby="cert-h">
      <div class="flex items-center gap-4">
        <x-px-icon name="medal" :size="44" class="{{ $certificateUrl ? '' : 'opacity-40 grayscale' }}" />
        <div class="min-w-0">
          <h2 id="cert-h" class="text-[1.35rem] font-semibold">Certificate</h2>
          <p class="text-[15px] leading-snug text-muted">
            @if ($certificateUrl)
              Final Boss defeated. Your certificate is ready!
            @elseif ($lessonsDone < $lessonsTotal)
              Finish all {{ $lessonsTotal }} lessons, then pass the final exam to earn it.
            @else
              All lessons done! Pass the final exam to earn it.
            @endif
          </p>
        </div>
      </div>
      @if ($certificateUrl)
        <a href="{{ $certificateUrl }}" class="px-btn px-btn-blue mt-6 w-[calc(100%-6px)]">View certificate</a>
      @elseif ($finalExam['state'] === 'ready')
        <a href="{{ $finalExam['url'] }}" class="px-btn px-btn-blue mt-6 w-[calc(100%-6px)]">Take the final exam</a>
      @endif
    </section>
  @endif
</aside>
