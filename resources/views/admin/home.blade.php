@extends('layouts.admin-shell')
@section('title', 'Course Assignment')

@section('content')
  <span class="eyebrow">Admin · Course Assignment</span>
  <h1>Course <span class="accent">assignments</span></h1>
  <p class="sub">Pick who captains each course — the faculty member generates their own join code once assigned. Click a captain's name to see who's joined.</p>

  @if (session('status'))
    <div class="code-card" style="margin-top:18px;padding:14px 20px"><p class="sub" style="margin:0">{{ session('status') }}</p></div>
  @endif

  @if ($faculty->isEmpty())
    <p class="sub" style="margin-top:18px">No faculty accounts have registered yet — nobody to assign.</p>
  @endif

  <div x-data="adminRoster()">
    @foreach ($planets as $slug => $planet)
      <section class="roster-card">
        <h2>{{ $planet['title'] }}</h2>
        <div class="roster">
          @foreach ($planet['courses'] as $course)
            @php $current = $course['assignment']; $crew = $current?->crew(); @endphp
            <div class="member" style="flex-wrap:wrap;align-items:center;padding:16px">
              <span class="avatar">{{ strtoupper(substr($course['title'] ?? $course['slug'], 0, 2)) }}</span>
              <div style="flex:1 1 220px">
                <span class="mname" style="display:block">{{ $course['title'] ?? $course['slug'] }}</span>
                @if ($current)
                  @if ($crew)
                    <button type="button" @click="open({{ $crew->id }})"
                            style="background:none;border:0;padding:0;font:inherit;color:var(--blue);cursor:pointer;text-decoration:underline;text-underline-offset:2px">
                      Captain: {{ $current->faculty->name }}
                    </button>
                    <span style="color:var(--muted);font-size:.82rem;margin-left:6px">code {{ $crew->code }} · {{ $crew->roster()->wherePivot('role', 'student')->count() }} joined</span>
                  @else
                    <span style="color:var(--text);font-size:.9rem">Captain: {{ $current->faculty->name }}</span>
                    <span style="color:var(--muted);font-size:.82rem;margin-left:6px">no code generated yet</span>
                  @endif
                @else
                  <span style="color:var(--muted);font-size:.85rem">Unassigned</span>
                @endif
              </div>
              <form method="POST" action="{{ route('admin.courses.assign') }}" style="display:flex;gap:8px;align-items:center;flex:0 0 auto">
                @csrf
                <input type="hidden" name="planet" value="{{ $slug }}">
                <input type="hidden" name="course_slug" value="{{ $course['slug'] }}">
                <select name="faculty_id" class="field-select" required @disabled($faculty->isEmpty())>
                  <option value="" disabled {{ ! $current ? 'selected' : '' }}>Choose faculty…</option>
                  @foreach ($faculty as $f)
                    <option value="{{ $f->id }}" @selected($current && $current->faculty_id === $f->id)>{{ $f->name }}</option>
                  @endforeach
                </select>
                <button class="mbtn solid" type="submit" @disabled($faculty->isEmpty())>{{ $current ? 'Reassign' : 'Assign' }}</button>
              </form>
            </div>
          @endforeach
        </div>
      </section>
    @endforeach

    {{-- Crew roster popover — opened by clicking a captain's name above --}}
    <div x-show="modalOpen" x-cloak x-transition.opacity.duration.150ms
         class="fixed inset-0 z-50 grid place-items-center bg-black/60 p-4" @click.self="close()" @keydown.escape.window="close()">
      <div class="w-full max-w-md rounded-[18px] border border-white/10 bg-[rgba(14,16,44,0.92)] p-6 backdrop-blur-2xl shadow-[0_24px_60px_-14px_rgba(0,0,0,0.75)]">
        <template x-if="loading"><p class="sub">Loading…</p></template>
        <template x-if="!loading && data">
          <div>
            <h3 style="font-family:'Space Grotesk',sans-serif;font-size:1.2rem;color:var(--text)" x-text="data.title"></h3>
            <p class="sub" style="margin:4px 0 14px">code <span x-text="data.code" style="font-family:'Space Mono',monospace"></span></p>
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
