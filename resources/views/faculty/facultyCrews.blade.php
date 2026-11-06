{{-- Sidebar "Crew" when the captain watches several crews (or a course has no class yet): every crew,
     grouped by planet, with its astronauts and their progress. FacultyController::crewShortcut. --}}
@extends('layouts.faculty-shell')
@section('title', 'Crew')

@push('head')
  <style>
    .cw-filter { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 22px; }
    .cw-chip { display: inline-flex; align-items: center; gap: 8px; padding: 7px 14px; border-radius: 99px; border: 1px solid var(--glass-border); background: transparent; color: var(--muted); font: 600 13px 'Inter', sans-serif; cursor: pointer; transition: background-color .18s ease, color .18s ease, border-color .18s ease; }
    .cw-chip:hover { color: var(--text); border-color: rgba(115,182,255,.4); }
    .cw-chip[aria-pressed="true"] { color: var(--text); background: rgba(115,182,255,.14); border-color: rgba(115,182,255,.5); }
    .cw-chip i, .cw-planet-h i { width: 8px; height: 8px; border-radius: 50%; background: var(--dot); flex: none; }
    .cw-chip b { font-weight: 700; font-variant-numeric: tabular-nums; opacity: .7; }

    .cw-planet { margin-top: 34px; text-align: left; }
    .cw-planet-h { display: flex; align-items: center; gap: 10px; margin: 0; font: 700 11.5px 'Space Mono', monospace !important; letter-spacing: .18em; text-transform: uppercase; color: var(--muted); }
    .cw-planet-h::after { content: ''; flex: 1; height: 1px; background: var(--glass-border); }

    .cw-course { margin-top: 14px; padding: 20px 22px; border-radius: 18px; border: 1px solid var(--glass-border); background: var(--glass); }
    html[data-theme="light"] .cw-course { background: #fff; }
    .cw-course-h { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px; }
    .cw-course-h h2 { margin: 0; font-size: 1.3rem !important; }
    .cw-crew { margin-top: 16px; }
    .cw-crew + .cw-crew { padding-top: 16px; border-top: 1px dashed var(--glass-border); }
    .cw-crew-h { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 14px; font-size: .86rem; color: var(--muted); }
    .cw-kind { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 99px; font: 700 10.5px 'Space Mono', monospace; letter-spacing: .12em; text-transform: uppercase; color: var(--blue); background: rgba(115,182,255,.12); }
    .cw-kind.is-self { color: var(--violet); background: rgba(155,107,255,.14); }
    .cw-code { font-family: 'Space Mono', monospace; font-weight: 700; letter-spacing: .12em; color: var(--text); }
    .cw-open { margin-left: auto; color: var(--blue); font-weight: 600; text-decoration: none; white-space: nowrap; }
    .cw-open:hover { text-decoration: underline; }
  </style>
@endpush

@section('content')
  @php
    $dots = ['programming' => '#73b6ff', 'networking' => '#5be1ff', 'cybersecurity' => '#b48bff'];
    $crewCount = fn ($group) => $group->sum(fn ($c) => $c['crews']->count());
  @endphp

  <div class="page-head">
    <span class="eyebrow">Captain · Crew</span>
    <h1>Your <span class="accent">crews</span></h1>
    <p class="sub" style="margin:0">Every astronaut in your classes and how far they've got, sorted by planet.</p>
  </div>

  @if ($planets->isEmpty())
    <section class="roster-card">
      <p class="sub" style="margin:0">No courses assigned yet. An admin will set you up as a course's captain, and its crew will show up here.</p>
    </section>
  @else
    <div x-data="{ planet: null }">
      @if ($planets->count() > 1)
        <div class="cw-filter" role="group" aria-label="Show planet">
          <button type="button" class="cw-chip" :aria-pressed="planet === null" @click="planet = null">All planets <b>{{ $planets->sum($crewCount) }}</b></button>
          @foreach ($planets as $planet => $group)
            <button type="button" class="cw-chip" style="--dot: {{ $dots[$planet] ?? '#73b6ff' }}" :aria-pressed="planet === @js($planet)" @click="planet = @js($planet)">
              <i></i>{{ $group->first()['planetTitle'] }} <b>{{ $crewCount($group) }}</b>
            </button>
          @endforeach
        </div>
      @endif

      @foreach ($planets as $planet => $group)
        <section class="cw-planet" x-show="planet === null || planet === @js($planet)">
          <h2 class="cw-planet-h" style="--dot: {{ $dots[$planet] ?? '#73b6ff' }}"><i></i>{{ $group->first()['planetTitle'] }}</h2>

          @foreach ($group as $c)
            <article class="cw-course" @if ($loop->parent->first && $loop->first) data-tour="crew-list" @endif>
              <div class="cw-course-h">
                <h2>{{ $c['title'] }}</h2>
                @unless ($c['hasClass'])
                  <form method="POST" action="{{ route('faculty.assignments.generate-code', $c['assignment']) }}">
                    @csrf
                    <input type="hidden" name="return" value="crew">
                    <button class="mbtn solid" type="submit">Open class</button>
                  </form>
                @endunless
              </div>

              @unless ($c['hasClass'])
                <p class="sub" style="margin:10px 0 0">No crew yet. Open a class so astronauts can find you under Join Class and ask to join.</p>
              @endunless

              @foreach ($c['crews'] as $row)
                @php
                  $crew = $row['crew'];
                  $students = $row['roster']->filter(fn ($m) => $m->pivot->role === 'student');
                @endphp
                <div class="cw-crew">
                  <div class="cw-crew-h">
                    @if ($crew->isSelfPaced())
                      <span class="cw-kind is-self">Self-paced</span>
                    @else
                      <span class="cw-kind">Class</span>
                      <span>code <span class="cw-code" @if ($loop->parent->parent->first && $loop->parent->first && $loop->first) data-tour="captain-code" @endif>{{ $crew->code }}</span></span>
                    @endif
                    <span>{{ $students->count() }} {{ \Illuminate\Support\Str::plural('astronaut', $students->count()) }}</span>
                    <a class="cw-open" href="{{ route('faculty.course.roster', $crew) }}">Open crew →</a>
                  </div>

                  @if ($students->isEmpty())
                    <p class="sub" style="margin:12px 0 0">
                      @if ($crew->isSelfPaced()) No one has started this course self-paced yet.
                      @else No astronauts yet. Share the code <strong>{{ $crew->code }}</strong>; their join requests show up on your dashboard.
                      @endif
                    </p>
                  @else
                    @include('faculty.partials.roster-rows', ['roster' => $students->sortByDesc('progressPercent')])
                  @endif
                </div>
              @endforeach
            </article>
          @endforeach
        </section>
      @endforeach
    </div>
  @endif
@endsection
