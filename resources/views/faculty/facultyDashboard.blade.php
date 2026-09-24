@extends('layouts.faculty-shell')
@section('title', "Captain's Bridge")

@section('content')
  <span class="eyebrow">Captain · My Courses</span>
  <h1>Your <span class="accent">courses</span></h1>

  @if ($courses->isEmpty())
    <p class="sub">No courses assigned yet — an admin will set you up as a course's captain, and it'll show up here.</p>
  @else
    <p class="sub">Generate a join code for each course. Students ask to join; accept them and TechLab emails them the code.</p>

    @if (session('status'))
      <p class="mok" role="status">{{ session('status') }}</p>
    @endif

    <div class="roster-card" style="text-align:left">
      <div class="roster">
        @foreach ($courses as $c)
          <div class="member" style="flex-wrap:wrap">
            <span class="avatar">{{ strtoupper(substr($c['title'], 0, 2)) }}</span>
            <div style="flex:1 1 auto;min-width:200px">
              <span class="mname" style="display:block">{{ $c['title'] }}</span>
              <span style="color:var(--muted);font-size:.82rem">{{ $c['planetTitle'] }}</span>
            </div>

            @if ($c['crew'])
              <div style="text-align:right">
                <div style="font-family:'Space Mono',monospace;letter-spacing:.12em;font-weight:700;color:var(--blue)">{{ $c['crew']->code }}</div>
                <div style="color:var(--muted);font-size:.78rem">
                  {{ $c['crew']->roster()->wherePivot('role', 'student')->count() }} joined
                  @if ($c['waiting'])
                    · <strong style="color:var(--text)">{{ $c['waiting'] }} waiting</strong>
                  @endif
                </div>
              </div>
              <a class="mbtn solid" href="{{ route('faculty.course', $c['crew']) }}">Manage course →</a>
            @else
              <form method="POST" action="{{ route('faculty.assignments.generate-code', $c['assignment']) }}">
                @csrf
                <button class="mbtn solid" type="submit">Generate code</button>
              </form>
            @endif
          </div>
        @endforeach
      </div>
    </div>

    <div class="roster-card">
      <h2>Waiting to join</h2>
      @if ($requests->isEmpty())
        <p class="mempty">Nobody is waiting right now. When a student asks to join one of your courses, they show up here.</p>
      @else
        <div class="roster">
          @foreach ($requests as $r)
            <div class="member" style="flex-wrap:wrap">
              <span class="avatar">{{ strtoupper(substr($r->student->name, 0, 2)) }}</span>
              <div style="flex:1 1 auto;min-width:200px">
                <span class="mname" style="display:block">{{ $r->student->name }}</span>
                <span style="color:var(--muted);font-size:.82rem">{{ $r->student->email }} · {{ $r->crew->name }} · asked {{ $r->updated_at->diffForHumans() }}</span>
              </div>
              <form method="POST" action="{{ route('faculty.join-requests.accept', $r) }}">
                @csrf
                <button class="mbtn solid" type="submit">Accept</button>
              </form>
              <form method="POST" action="{{ route('faculty.join-requests.decline', $r) }}">
                @csrf
                <button class="mbtn danger" type="submit">Decline</button>
              </form>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  @endif
@endsection
