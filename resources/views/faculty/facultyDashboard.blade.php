@extends('layouts.faculty-shell')
@section('title', "Captain's Bridge")

@section('content')
  <style>
    /* Join-request row: name/meta and the Accept/Decline pair each wrap as one unit, so the
       two actions never split across lines the way two independent flex children would. */
    .request-row { display: flex; align-items: center; flex-wrap: wrap; gap: 14px; padding: 14px 16px; border-radius: 16px; background: var(--field); border: 1px solid var(--glass-border); transition: border-color .2s ease, background-color .2s ease; }
    .request-row:hover { border-color: rgba(115,182,255,.35); }
    .request-info { flex: 1 1 220px; min-width: 200px; }
    .request-meta { display: block; margin-top: 2px; color: var(--muted); font-size: .82rem; }
    .request-actions { display: flex; align-items: center; gap: 8px; flex: 0 0 auto; margin-left: auto; }
    .final-flag { display: inline-flex; align-items: center; gap: 6px; margin-top: 6px; padding: 3px 10px; border-radius: 999px; font-size: .76rem; font-weight: 600; color: #ffc400; background: rgba(255,196,0,.12); border: 1px solid rgba(255,196,0,.35); text-decoration: none; }
    .final-flag:hover { background: rgba(255,196,0,.2); }
    html[data-theme="light"] .final-flag { color: #8a5a00; }
    .alert-row { border-color: rgba(255,107,129,.4); }
    .alert-dot { flex: 0 0 34px; height: 34px; border-radius: 50%; display: grid; place-items: center; font-weight: 800; color: #fff; background: #e5484d; }
  </style>

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
              {{-- No live final exam means nobody in this course can earn a certificate yet. --}}
              @if ($c['final'] === 'none')
                <a class="final-flag" href="{{ route('faculty.quizzes.build', ['crew' => $c['crew'], 'final' => 1]) }}">⚠ Create the final exam so students can pass</a>
              @elseif ($c['final'] === 'draft')
                <a class="final-flag" href="{{ route('faculty.course', $c['crew']) }}#final-exam">⚠ Final exam is a draft, publish it</a>
              @endif
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

    @if ($alerts->isNotEmpty())
      <div class="roster-card">
        <h2>Final exam alerts</h2>
        <p class="sub" style="margin:4px 0 12px">These students left their final exam before finishing, so it ended as a fail. You can let them try again from the course page.</p>
        <div class="roster">
          @foreach ($alerts as $al)
            <div class="request-row alert-row">
              <span class="alert-dot" aria-hidden="true">!</span>
              <div class="request-info">
                <span class="mname" style="display:block">{{ $al->user?->name ?? 'A student' }}</span>
                <span class="request-meta">{{ $al->violationText() }} · {{ $al->quiz->crew->name }} · {{ $al->finished_at?->diffForHumans() }}</span>
              </div>
              <div class="request-actions">
                <a class="mbtn solid" href="{{ route('faculty.course', $al->quiz->crew) }}#final-exam">Review</a>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    <div class="roster-card">
      <h2>Waiting to join</h2>
      @if ($requests->isEmpty())
        <p class="mempty">Nobody is waiting right now. When a student asks to join one of your courses, they show up here.</p>
      @else
        <div class="roster">
          @foreach ($requests as $r)
            <div class="request-row">
              <span class="avatar">{{ strtoupper(substr($r->student->name, 0, 2)) }}</span>
              <div class="request-info">
                <span class="mname" style="display:block">{{ $r->student->name }}</span>
                <span class="request-meta">{{ $r->student->email }} · {{ $r->crew->name }} · asked {{ $r->updated_at->diffForHumans() }}</span>
              </div>
              <div class="request-actions">
                <form method="POST" action="{{ route('faculty.join-requests.accept', $r) }}">
                  @csrf
                  <button class="mbtn solid" type="submit">Accept</button>
                </form>
                <form method="POST" action="{{ route('faculty.join-requests.decline', $r) }}">
                  @csrf
                  <button class="mbtn danger" type="submit">Decline</button>
                </form>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  @endif
@endsection
