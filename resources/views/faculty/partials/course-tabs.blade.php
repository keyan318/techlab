@php $onCrew = request()->routeIs('faculty.course.roster'); @endphp
<nav class="mrow" style="margin:22px 0 4px" aria-label="Course sections">
  <a class="mbtn {{ $onCrew ? 'solid' : '' }}" href="{{ route('faculty.course.roster', $crew) }}">Crew</a>
  <a class="mbtn {{ $onCrew ? '' : 'solid' }}" href="{{ route('faculty.course', $crew) }}">Materials</a>
</nav>
