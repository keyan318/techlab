@php
  $onQuizzes = request()->routeIs('faculty.lesson-quizzes.*');
  $onExams = request()->routeIs('faculty.exams.*');
@endphp
<nav class="mrow" style="margin:22px 0 4px" data-tour="captain-tabs" aria-label="Course sections">
  <a class="mbtn {{ $onQuizzes || $onExams ? '' : 'solid' }}" href="{{ route('faculty.course', $crew) }}">Materials</a>
  <a class="mbtn {{ $onQuizzes ? 'solid' : '' }}" href="{{ route('faculty.lesson-quizzes.index', $crew) }}">Lesson quizzes</a>
  <a class="mbtn {{ $onExams ? 'solid' : '' }}" href="{{ route('faculty.exams.index', $crew) }}">Exams</a>
</nav>
