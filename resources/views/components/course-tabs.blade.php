{{--
  The course hub's four tabs. Route-based (not JS), so each tab is a real page with its own
  URL and the browser's back button works. Follows the sidebar's active-item convention:
  aria-current="page" plus an underline on the selected tab.

  $quizBadge is the number of quizzes this student has not looked at yet; 0 hides the bubble.
  $materialsBadge is the number of materials the faculty added since this student last looked; 0 hides the bubble.
--}}
@props(['slug', 'course', 'tab', 'quizBadge' => 0, 'materialsBadge' => 0])

@php
  $tabs = [
    ['key' => 'home', 'label' => 'Home', 'route' => 'student.course.overview'],
    ['key' => 'materials', 'label' => 'Materials', 'route' => 'student.course.materials'],
    ['key' => 'quiz', 'label' => 'Quiz', 'route' => 'student.course.quiz'],
    ['key' => 'crew', 'label' => 'Crew', 'route' => 'student.course.crew'],
  ];
  $badges = ['materials' => $materialsBadge, 'quiz' => $quizBadge];
@endphp

<nav class="border-b border-glassBorder" aria-label="Course sections">
  <ul class="mx-auto flex max-w-[1200px] items-center gap-1 px-4 sm:px-8">
    @foreach ($tabs as $t)
      @php $on = $tab === $t['key']; $badge = $badges[$t['key']] ?? 0; @endphp
      <li>
        <a href="{{ route($t['route'], ['slug' => $slug, 'course' => $course]) }}"
           @if ($on) aria-current="page" @endif
           class="relative flex items-center gap-2 px-5 py-4 text-[15px] font-semibold transition
                  {{ $on ? 'text-[#73b6ff]' : 'text-muted hover:text-ink' }}">
          {{ $t['label'] }}

          @if ($badge > 0)
            <span class="grid h-[18px] min-w-[18px] place-items-center rounded-full bg-[#e5484d] px-1 text-[11px] font-bold leading-none text-white"
                  aria-label="{{ $badge }} new {{ \Illuminate\Support\Str::plural($t['key'] === 'quiz' ? 'quiz' : 'material', $badge) }}">{{ $badge > 9 ? '9+' : $badge }}</span>
          @endif

          @if ($on)
            <span class="absolute inset-x-3 -bottom-px h-[3px] rounded-t-full bg-[#73b6ff] shadow-[0_0_10px_rgba(115,182,255,0.7)]"></span>
          @endif
        </a>
      </li>
    @endforeach
  </ul>
</nav>
