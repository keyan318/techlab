@extends('layouts.faculty-shell')
@section('title', 'Lesson quizzes')

@section('content')
  <style>
    .lq-list { list-style: none; margin: 12px 0 0; padding: 0; display: flex; flex-direction: column; gap: 8px; }
    .lq-list li { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 14px; padding: 12px 14px; border-radius: 14px; background: var(--field, rgba(255,255,255,.03)); }
    .lq-num { font-family: 'Space Mono', monospace; font-size: .78rem; color: var(--muted); min-width: 34px; }
    .lq-name { flex: 1 1 200px; font-weight: 600; }
    .lq-meta { color: var(--muted); font-size: .82rem; }
    .lq-pill { font-size: .75rem; font-weight: 600; padding: 4px 10px; border-radius: 999px; background: rgba(255,255,255,.07); color: var(--muted); }
    .lq-pill.edited { color: #ffc400; background: rgba(255,196,0,.13); }
    html[data-theme="light"] .lq-pill.edited { color: #8a5a00; }
  </style>
  <p style="margin-bottom:10px"><a href="{{ route('faculty.dashboard') }}" style="color:var(--muted);font-size:.85rem">← My Courses</a></p>

  <div class="page-head">
    <span class="eyebrow">Captain · {{ $crew->name }}</span>
    <h1>Lesson <span class="accent">quizzes</span></h1>
    <p class="sub" style="margin:0">The Knowledge Check at the end of each lesson</p>
  </div>

  @include('faculty.partials.course-tabs', ['crew' => $crew])

  <section class="mods">
    <p class="lead">Every lesson comes with a ready-made quiz. You can keep it, or change the questions, the choices and the right answer for your course. You can always reset a quiz back to the default.</p>

    @forelse ($modules as $module)
      <article class="mod" @if ($loop->first) data-tour="lq-list" @endif>
        <span class="tagm">Module {{ $loop->iteration }}</span>
        <h3>{{ $module['title'] }}</h3>
        <ul class="lq-list">
          @foreach ($module['lessons'] as $l)
            <li>
              <span class="lq-num">{{ (int) preg_replace('/\D/', '', $l['module']) }}.{{ (int) preg_replace('/\D/', '', $l['lesson']) }}</span>
              <span class="lq-name">{{ $l['title'] }}</span>
              <span class="lq-meta">{{ $l['count'] }} {{ \Illuminate\Support\Str::plural('question', $l['count']) }}</span>
              @if ($l['edited'])
                <span class="lq-pill edited" title="Edited {{ $l['updated_at']?->diffForHumans() }}">Edited by you</span>
              @else
                <span class="lq-pill">Default</span>
              @endif
              <a class="mbtn" @if ($loop->parent->first && $loop->first) data-tour="lq-edit" @endif href="{{ route('faculty.lesson-quizzes.edit', ['crew' => $crew, 'module' => $l['module'], 'lesson' => $l['lesson']]) }}">Edit</a>
            </li>
          @endforeach
        </ul>
      </article>
    @empty
      <p class="lead">This course has no lesson quizzes yet.</p>
    @endforelse
  </section>
@endsection
