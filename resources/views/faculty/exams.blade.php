@extends('layouts.faculty-shell')
@section('title', 'Exams')

@section('content')
  <style>
    .mb-meta { color: var(--muted); font-size: .86rem; margin-top: 4px; }
    .mb-q { list-style: none; margin: 12px 0 0; padding: 0; display: flex; flex-direction: column; gap: 8px; counter-reset: q; }
    .mb-q li { padding: 10px 12px; border-radius: 12px; background: var(--field, rgba(255,255,255,.03)); counter-increment: q; }
    .mb-q li > p { font-weight: 600; }
    .mb-q li > p::before { content: counter(q) '. '; color: var(--muted); font-family: 'Space Mono', monospace; font-size: .8rem; }
    .mb-opts { list-style: none; margin: 6px 0 0; padding: 0; display: grid; gap: 4px; font-size: .88rem; color: var(--muted); }
    .mb-opts .right { color: #8bd83a; font-weight: 600; }
    html[data-theme="light"] .mb-opts .right { color: #2f7a05; }
    .mb-sum { cursor: pointer; margin-top: 14px; font-weight: 600; color: var(--blue); }
    .final-rows { list-style: none; margin: 14px 0 0; padding: 0; display: flex; flex-direction: column; gap: 8px; }
    .final-rows li { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 12px; background: var(--field, rgba(255,255,255,.03)); }
    .final-rows .fname { flex: 1 1 160px; font-weight: 600; }
    .final-rows .fsize { color: var(--muted); font-size: .8rem; }
    .fpill { font-size: .78rem; font-weight: 600; padding: 4px 10px; border-radius: 999px; background: rgba(255,255,255,.07); color: var(--muted); }
    .fpill.good { color: #8bd83a; background: rgba(139,216,58,.12); }
    .fpill.bad { color: #ff6b81; background: rgba(255,107,129,.12); }
    .fpill.live { color: #73b6ff; background: rgba(115,182,255,.14); }
    html[data-theme="light"] .fpill.good { color: #2f7a05; }
    html[data-theme="light"] .fpill.bad { color: #a61b41; }
  </style>
  <p style="margin-bottom:10px"><a href="{{ route('faculty.dashboard') }}" style="color:var(--muted);font-size:.85rem">← My Courses</a></p>

  <div class="page-head">
    <span class="eyebrow">Captain · {{ $crew->name }}</span>
    <h1>Exams</h1>
    <p class="sub" style="margin:0">Mini bosses, module by module, and the Final Boss</p>
  </div>

  @include('faculty.partials.course-tabs', ['crew' => $crew])

  @if (session('status'))
    <p class="mok" role="status" style="margin-top:14px">{{ session('status') }}</p>
  @endif

  <section class="mods" id="final-exam" data-tour="final-exam">
    <h2>Final exam</h2>
    <p class="lead">The Final Boss. Students unlock it after finishing every lesson and take it in full screen, against the clock. They earn the certificate only if they reach your pass mark. Failing it, running out of time, or leaving full screen (switching tab or window, or trying to screenshot) sends them back to the start of the course to redo every lesson before they can try again, and you get an email if they were caught. Getting caught is a strike, and a second strike on a later try bans them from the final: no more tries until you allow a retake. Afterwards they see their score, but not which answers were wrong, so a retry can't be memorised.</p>

    @if (! $final)
      <p class="mrow" style="margin-top:12px">
        <a class="mbtn solid" href="{{ route('faculty.quizzes.build', ['crew' => $crew, 'final' => 1]) }}">Create final exam</a>
      </p>
    @else
      @php
        $taken = collect($finalResults)->whereIn('state', ['passed', 'failed', 'caught', 'banned']);
        $passedCount = $taken->where('state', 'passed')->count();
      @endphp
      <article class="mod">
        <div class="mod-top">
          <div>
            <span class="tagm">Final exam</span>
            <h3>{{ $final->title }}</h3>
            <p class="mdesc">{{ $final->isPublished() ? 'Published' : 'Draft — students cannot see it yet' }}
              · {{ $final->questions_count }} {{ \Illuminate\Support\Str::plural('question', $final->questions_count) }}
              @if ($final->minutes) · {{ $final->minutes }} min @endif
              · pass mark {{ $final->passPercent() }}%
              @if ($final->due_at) · closes {{ $final->due_at->format('j M Y, g:ia') }} @endif
              · {{ $passedCount }} of {{ $taken->count() }} passed</p>
          </div>
          <div class="mrow">
            <a class="mbtn" href="{{ route('faculty.quizzes.edit', $final) }}">{{ $taken->isEmpty() ? 'Edit' : 'View' }}</a>
            <form method="POST" action="{{ route('faculty.quizzes.destroy', $final) }}" onsubmit="return confirm('Delete the final exam and every result for it? Students who passed would lose their certificate.')">
              @csrf @method('DELETE')
              <button class="mbtn danger" type="submit">Delete</button>
            </form>
          </div>
        </div>

        @if (empty($finalResults))
          <p class="mempty">No students in this course yet.</p>
        @else
          <ul class="final-rows">
            @foreach ($finalResults as $row)
              @php $a = $row['attempt']; @endphp
              <li>
                <span class="fname">{{ $row['student']->name }}</span>
                @switch($row['state'])
                  @case('passed')
                    <span class="fpill good">Passed · {{ $a->percent() }}%</span>
                    @break
                  @case('failed')
                    <span class="fpill bad">Failed{{ $a->try_number > 1 ? ' (try '.$a->try_number.')' : '' }} · {{ $a->percent() }}%</span>
                    @break
                  @case('caught')
                    <span class="fpill bad" title="{{ $a->finished_at?->format('j M Y, g:ia') }}">Caught{{ $a->try_number > 1 ? ' (try '.$a->try_number.')' : '' }}: {{ $a->violationText() }} · strike 1 of 2</span>
                    @break
                  @case('banned')
                    <span class="fpill bad" title="{{ $a->finished_at?->format('j M Y, g:ia') }}">Banned (try {{ $a->try_number }}): {{ $a->violationText() }} · 2nd strike</span>
                    @break
                  @case('taking')
                    <span class="fpill live">Taking it now</span>
                    @break
                  @case('ready')
                    <span class="fpill">Ready to take it</span>
                    @break
                  @default
                    <span class="fpill">Lessons {{ $row['lessonsPercent'] }}%</span>
                @endswitch
                @if ($a?->finished_at)
                  <span class="fsize">{{ $a->finished_at->format('j M, g:ia') }}</span>
                @endif
                @if (in_array($row['state'], ['failed', 'caught']) && $a->wiped_progress !== null)
                  <span class="fsize">Redoing lessons · {{ $row['lessonsPercent'] }}%</span>
                @endif
                @if ($row['state'] === 'banned')
                  <form method="POST" action="{{ route('faculty.final-exam.retake', $a) }}" onsubmit="return confirm('Lift {{ addslashes($row['student']->name) }}\'s ban and let them take the final exam again right now? Their strikes go back to zero.')">
                    @csrf
                    <button class="mbtn" type="submit">Lift ban</button>
                  </form>
                @elseif (in_array($row['state'], ['failed', 'caught']))
                  <form method="POST" action="{{ route('faculty.final-exam.retake', $a) }}" onsubmit="return confirm('Let {{ addslashes($row['student']->name) }} take the final exam again right now? Their lessons come back so they don\'t have to redo the course, and their current result (and any strike) is removed.')">
                    @csrf
                    <button class="mbtn" type="submit">Allow retake</button>
                  </form>
                @endif
              </li>
            @endforeach
          </ul>
        @endif
      </article>
    @endif
  </section>

  <section class="mods" id="mini-bosses">
    <h2>Mini bosses</h2>
    <p class="lead" data-tour="mb-lead">Each module ends with a mini boss, ready-made by TechLab with at least 30 questions. Students in your class take it once, in full screen and against the clock, after finishing the module's lessons. They earn {{ \App\Services\MiniBossService::XP_PER_CORRECT }} XP for every right answer and move on whether they pass or not; you see their score here. Leaving full screen, switching tab or window, or trying to screenshot ends it as a fail with no XP, and you get an email. Change the questions, the time or the pass mark with Edit; your changes reach only your class.</p>

    @foreach ($bosses as $b)
      @php $quiz = $b['quiz']; @endphp
      <article class="mod" id="boss-{{ $b['module'] }}" @if ($loop->first) data-tour="mb-module" @endif>
        <div class="mod-top">
          <div>
            <span class="tagm">Module {{ $b['number'] }}</span>
            <h3>{{ $quiz->title }}</h3>
            <p class="mb-meta">
              {{ $quiz->isPublished() ? 'Live' : 'Draft, students skip it until you publish' }}
              · {{ $quiz->questions->count() }} {{ \Illuminate\Support\Str::plural('question', $quiz->questions->count()) }}
              · {{ $quiz->minutes }} min
              · pass mark {{ $quiz->passPercent() }}%
              · {{ $b['taken'] }} taken{{ $b['taken'] ? ', '.$b['passed'].' passed, average '.$b['average'].'%' : '' }}
            </p>
          </div>
          <div class="mrow">
            <a class="mbtn solid" href="{{ route('faculty.quizzes.edit', $quiz) }}">Edit</a>
            @if ($b['hasDefault'])
              <form method="POST" action="{{ route('faculty.mini-bosses.reset', $quiz) }}" onsubmit="return confirm('Put TechLab\'s questions, time and pass mark back for this mini boss? Your own questions are replaced. Students\' results stay.')">
                @csrf
                <button class="mbtn" type="submit">Reset to default</button>
              </form>
            @endif
          </div>
        </div>

        @if ($quiz->questions->isNotEmpty())
          <details>
            <summary class="mb-sum">Show the {{ $quiz->questions->count() }} questions</summary>
            <ol class="mb-q">
              @foreach ($quiz->questions as $q)
                <li>
                  <p>{{ $q->prompt }}</p>
                  <ul class="mb-opts">
                    @foreach ($q->options as $i => $opt)
                      @php $right = $q->isMultiAnswer() ? in_array($i, $q->correct_indexes ?? [], true) : $i === $q->correct_index; @endphp
                      <li class="{{ $right ? 'right' : '' }}">{{ chr(65 + $i) }}. {{ $opt }}@if ($right) <span class="sr-only">(correct)</span> ← right answer @endif</li>
                    @endforeach
                  </ul>
                </li>
              @endforeach
            </ol>
          </details>
        @endif

        @if (empty($b['rows']))
          <p class="mempty">No students in this course yet.</p>
        @else
          <ul class="final-rows" aria-label="Module {{ $b['number'] }} mini boss results">
            @foreach ($b['rows'] as $row)
              @php $a = $row['attempt']; @endphp
              <li>
                <span class="fname">{{ $row['student']->name }}</span>
                @if (! $a)
                  <span class="fpill">Not taken yet</span>
                @elseif ($a->inProgress())
                  <span class="fpill live">Taking it now</span>
                @elseif ($a->cheated())
                  <span class="fpill bad">Caught: {{ $a->violationShort() }} · 0 XP</span>
                @elseif ($a->violation === 'time_up')
                  <span class="fpill bad">Ran out of time · 0 XP</span>
                @elseif ($a->passed())
                  <span class="fpill good">Passed · {{ $a->score }}/{{ $a->total }} ({{ $a->percent() }}%) · +{{ $a->xp }} XP</span>
                @else
                  <span class="fpill bad">Below pass mark · {{ $a->score }}/{{ $a->total }} ({{ $a->percent() }}%) · +{{ $a->xp }} XP</span>
                @endif
                @if ($a?->finished_at)
                  <span class="fsize">{{ $a->finished_at->format('j M, g:ia') }}</span>
                @endif
              </li>
            @endforeach
          </ul>
        @endif
      </article>
    @endforeach
  </section>
@endsection
