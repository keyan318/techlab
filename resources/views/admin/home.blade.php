@extends('layouts.admin-shell')
@section('title', 'Course Assignment')

@section('content')
  <span class="eyebrow">Admin · Course Assignment</span>
  <h1>Course <span class="accent">assignments</span></h1>
  <p class="sub"><strong>Self-paced</strong> is the default course you look after. Each faculty member you assign gets their own <strong>class</strong>. Click a count or a class to see who's in.</p>

  @if (session('status'))
    <div class="code-card" style="margin-top:18px;padding:14px 20px"><p class="sub" style="margin:0">{{ session('status') }}</p></div>
  @endif

  @if ($faculty->isEmpty())
    <p class="sub" style="margin-top:18px">No faculty accounts have registered yet — nobody to assign.</p>
  @endif

  <div x-data="adminRoster()">
    <table class="assign-table">
      <colgroup><col class="c-course"><col class="c-self"><col class="c-classes"><col class="c-assign"></colgroup>
      <thead>
        <tr><th>Course</th><th>Self-paced</th><th>Classes</th><th>Assign faculty</th></tr>
      </thead>
      @foreach ($planets as $slug => $planet)
        <tbody>
          <tr class="planet-row"><th colspan="4">{{ $planet['title'] }}</th></tr>
          @foreach ($planet['courses'] as $course)
            @php
              $teaching = $course['assignments'];
              $selfPaced = $course['self_paced'];
              $free = $faculty->whereNotIn('id', $teaching->pluck('faculty_id'));
            @endphp
            <tr class="course-row">
              <td class="course-name" data-label="Course">{{ $course['title'] ?? $course['slug'] }}</td>

              {{-- Self-paced: no faculty member, so the admin looks after it. --}}
              <td data-label="Self-paced">
                @if ($selfPaced)
                  <button type="button" class="link" @click="open({{ $selfPaced->id }})">{{ $selfPaced->learners }} {{ Str::plural('learner', $selfPaced->learners) }}</button>
                @else
                  <span class="muted">0 learners</span>
                @endif
              </td>

              {{-- Join Class: one class per faculty member teaching it. --}}
              <td data-label="Classes">
                @forelse ($teaching as $a)
                  @php $crew = $a->crew(); @endphp
                  <div class="class-line" title="{{ $crew ? 'Class code '.$crew->code : 'Faculty has not opened this class yet' }}">
                    @if ($crew)
                      <button type="button" class="link" @click="open({{ $crew->id }})">{{ $a->faculty->name }}</button>
                      <span class="muted">· {{ $n = $crew->roster()->wherePivot('role', 'student')->count() }} {{ Str::plural('student', $n) }}</span>
                    @else
                      <span>{{ $a->faculty->name }}</span>
                      <span class="pending">Not opened</span>
                    @endif
                    <form method="POST" action="{{ route('admin.courses.unassign', $a) }}" onsubmit="return confirm('Take {{ e($a->faculty->name) }} off this course?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="x" aria-label="Remove {{ $a->faculty->name }}" title="Remove">&times;</button>
                    </form>
                  </div>
                @empty
                  <span class="muted">No classes yet</span>
                @endforelse
              </td>

              <td data-label="Assign faculty">
                <form method="POST" action="{{ route('admin.courses.assign') }}" class="assign-form">
                  @csrf
                  <input type="hidden" name="planet" value="{{ $slug }}">
                  <input type="hidden" name="course_slug" value="{{ $course['slug'] }}">
                  <select name="faculty_id" class="field-select" required @disabled($free->isEmpty())>
                    <option value="" disabled selected>{{ $free->isEmpty() ? 'Everyone assigned' : 'Choose faculty' }}</option>
                    @foreach ($free as $f)
                      <option value="{{ $f->id }}">{{ $f->name }}</option>
                    @endforeach
                  </select>
                  <button class="mbtn solid" type="submit" @disabled($free->isEmpty())>Add</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      @endforeach
    </table>

    {{-- Crew roster popover — opened by clicking a captain's name above --}}
    <div x-show="modalOpen" x-cloak x-transition.opacity.duration.150ms
         class="fixed inset-0 z-50 grid place-items-center bg-black/60 p-4" @click.self="close()" @keydown.escape.window="close()">
      <div class="w-full max-w-md rounded-[18px] border border-white/10 bg-[rgba(14,16,44,0.92)] p-6 backdrop-blur-2xl shadow-[0_24px_60px_-14px_rgba(0,0,0,0.75)]">
        <template x-if="loading"><p class="sub">Loading…</p></template>
        <template x-if="!loading && data">
          <div>
            <h3 style="font-family:'Space Grotesk',sans-serif;font-size:1.2rem;color:var(--text)" x-text="data.title"></h3>
            <p class="sub" style="margin:4px 0 14px" x-show="data.code">Class code <span x-text="data.code" style="font-family:'Space Mono',monospace"></span></p>
            <div class="roster" x-show="data.members.length" style="margin-top:0">
              <template x-for="m in data.members" :key="m.name">
                <div class="member">
                  <span class="avatar" x-text="m.name.slice(0,2).toUpperCase()"></span>
                  <span class="mname" x-text="m.name"></span>
                  <span class="tag" :class="m.role === 'faculty' ? 'faculty' : 'student'" x-text="m.role === 'faculty' ? 'Captain' : 'Astronaut'"></span>
                </div>
              </template>
            </div>
            <p class="sub" x-show="!data.members.length" style="margin-top:0">Nobody has joined yet.</p>
          </div>
        </template>
        <button type="button" class="mbtn" style="margin-top:18px" @click="close()">Close</button>
      </div>
    </div>
  </div>
@endsection

@push('head')
<style>
  .assign-table { width: 100%; margin-top: 28px; border-collapse: collapse; table-layout: fixed; text-align: left; font-size: .92rem; }
  .assign-table .c-course { width: 34%; }
  .assign-table .c-self { width: 14%; }
  .assign-table .c-classes { width: 26%; }
  .assign-table .c-assign { width: 26%; }
  .assign-table thead th { font-family: 'Space Mono', monospace; font-size: .66rem; font-weight: 400; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); padding: 0 12px 10px; border-bottom: 1px solid var(--glass-border); }
  .assign-table th, .assign-table td { padding: 12px; vertical-align: middle; }
  .assign-table th:first-child, .assign-table td:first-child { padding-left: 0; }
  .assign-table th:last-child, .assign-table td:last-child { padding-right: 0; }
  .assign-table .planet-row th { font-family: 'Space Mono', monospace; font-size: .68rem; font-weight: 400; letter-spacing: .14em; text-transform: uppercase; color: var(--cyan); padding-top: 26px; padding-bottom: 6px; }
  .assign-table .course-row td { border-bottom: 1px solid var(--glass-border); }
  .assign-table tbody .course-row:last-child td { border-bottom: 0; }
  .assign-table .course-name { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: .98rem; color: var(--text); }
  .assign-table .muted { color: var(--muted); }
  .assign-table .link { background: none; border: 0; padding: 0; font: inherit; color: var(--blue); cursor: pointer; }
  .assign-table .link:hover { text-decoration: underline; text-underline-offset: 2px; }
  .assign-table .class-line { display: flex; align-items: center; gap: 6px; white-space: nowrap; line-height: 1.9; }
  .assign-table .class-line form { display: contents; }
  .assign-table .pending { font-size: .72rem; color: var(--muted); padding: 1px 8px; border-radius: 999px; border: 1px dashed var(--glass-border); }
  .assign-table .x { margin-left: 2px; width: 20px; height: 20px; border: 0; border-radius: 50%; background: none; color: var(--muted); font-size: 1rem; line-height: 1; cursor: pointer; opacity: 0; transition: opacity .15s, background .15s, color .15s; }
  .assign-table .class-line:hover .x, .assign-table .x:focus-visible { opacity: 1; }
  .assign-table .x:hover { background: rgba(255,90,90,.15); color: #ff6b6b; }
  .assign-table .assign-form { display: flex; gap: 8px; align-items: center; }
  .assign-table .field-select { flex: 1 1 auto; min-width: 0; padding: 7px 10px; font-size: .86rem; }
  .assign-table .mbtn { flex: 0 0 auto; padding: 7px 16px; font-size: .86rem; }
  .assign-table button:disabled { opacity: .45; cursor: not-allowed; }
  @media (hover: none) { .assign-table .x { opacity: 1; } }

  /* Phones: each course becomes a stacked block instead of a squeezed row. */
  @media (max-width: 760px) {
    .assign-table, .assign-table tbody, .assign-table tr, .assign-table td { display: block; width: 100%; }
    .assign-table thead, .assign-table colgroup { display: none; }
    .assign-table .planet-row th { display: block; padding-left: 0; }
    .assign-table .course-row { padding: 12px 0; border-bottom: 1px solid var(--glass-border); }
    .assign-table .course-row td { border: 0; padding: 4px 0; }
    .assign-table td[data-label]:not(.course-name)::before { content: attr(data-label); display: block; font-family: 'Space Mono', monospace; font-size: .62rem; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); margin-bottom: 2px; }
  }
</style>
@endpush

@push('scripts')
<script>
  function adminRoster() {
    return {
      modalOpen: false,
      loading: false,
      data: null,
      open(crewId) {
        this.modalOpen = true;
        this.loading = true;
        this.data = null;
        fetch(`/admin/courses/${crewId}/roster`, { headers: { Accept: 'application/json' } })
          .then((r) => r.json())
          .then((d) => { this.data = d; this.loading = false; });
      },
      close() { this.modalOpen = false; },
    };
  }
</script>
@endpush
