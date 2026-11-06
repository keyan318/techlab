@extends('layouts.admin-shell')
@section('title', 'Classes')

@php
  // Planet colours follow each planet's logo, same palette as the dashboard.
  $planetColors = ['programming' => '#58c24a', 'networking' => '#1b84c9', 'cybersecurity' => '#7a5af5'];
@endphp

@section('content')
  <span class="eyebrow">Admin · Classes</span>
  <h1>Your <span class="accent">classes</span></h1>
  <p class="sub">One card per course. A course can have several faculty members teaching it, each with their own class, code and roster — they're all listed on the course's card. Self-paced runs live on the <a class="link-btn" href="{{ route('admin.self-paced') }}" style="display:inline">Self-paced</a> page instead, since nobody teaches those.</p>

  @php
    $initialsOf = function (?string $name) {
        $name = $name ?? 'Unassigned';
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);
        $initials = count($parts) >= 2 ? mb_substr($parts[0], 0, 1).mb_substr(end($parts), 0, 1) : mb_substr($name, 0, 2);

        return mb_strtoupper($initials !== '' ? $initials : '?');
    };
  @endphp

  <div x-data="adminClasses()">
    @if ($courses->isEmpty())
      <div class="code-card" style="margin-top:22px;padding:28px;text-align:center">
        <p class="sub" style="margin:0">No classes are open yet. Assign a faculty member to a course from <a class="link-btn" href="{{ route('admin.home') }}" style="display:inline">Course Assignment</a>, and once they open it, it'll show up here.</p>
      </div>
    @else
      <div class="class-grid">
        @foreach ($courses as $course)
          @php
            $color = $planetColors[$course['planetSlug']] ?? '#73b6ff';
            $count = $course['classes']->count();
          @endphp
          <div class="class-card" style="--pc: {{ $color }}">
            <div class="banner">
              <span class="banner-clip" aria-hidden="true"><span class="banner-pattern"></span></span>
              <span class="banner-text">
                <span class="ctitle">{{ $course['title'] }}</span>
                <span class="csub">{{ $course['planet'] }}</span>
              </span>
              <span class="tstack" aria-hidden="true">
                @foreach ($course['classes']->take(3) as $c)
                  <span class="tavatar">{{ $initialsOf($c['faculty']->name ?? null) }}</span>
                @endforeach
                @if ($count > 3)
                  <span class="tavatar more">+{{ $count - 3 }}</span>
                @endif
              </span>
            </div>
            <div class="card-body">
              <p class="csummary">
                {{ $count }} {{ Str::plural('faculty member', $count) }}
                · {{ $course['learners'] }} {{ Str::plural('astronaut', $course['learners']) }} in all
              </p>
              <ul class="tlist">
                @foreach ($course['classes'] as $c)
                  <li>
                    <button type="button" class="trow" @click="open({{ $c['id'] }})" title="View {{ $c['faculty']->name ?? 'this class' }}'s roster">
                      <span class="ravatar">{{ $initialsOf($c['faculty']->name ?? null) }}</span>
                      <span class="rmain">
                        <span class="tname">{{ $c['faculty']->name ?? 'Unassigned' }}</span>
                        <span class="rmeta">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8.2" r="3.3"/><path d="M3.2 19.6c.5-3.1 2.9-5 5.8-5s5.3 1.9 5.8 5z"/><path d="M15.6 5.3a3 3 0 0 1 0 5.8"/><path d="M17.4 14.9c1.9.6 3.1 2.3 3.4 4.7"/></svg>
                          {{ $c['learners'] }} {{ Str::plural('astronaut', $c['learners']) }}
                          <span class="code">· Code {{ $c['code'] }}</span>
                        </span>
                      </span>
                      <span class="view">Roster <span aria-hidden="true">→</span></span>
                    </button>
                  </li>
                @endforeach
              </ul>
            </div>
          </div>
        @endforeach
      </div>
    @endif

    {{-- Roster popover — reused across every card --}}
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
  .class-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 18px; margin-top: 24px; align-items: start; }
  .class-card { display: flex; flex-direction: column; border-radius: 18px; border: 1px solid var(--glass-border); background: var(--glass); overflow: hidden;
    backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); }
  html[data-theme="light"] .class-card { background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.04), 0 8px 24px -12px rgba(16,24,64,.12); }
  .class-card .banner { position: relative; height: 92px; padding: 16px 18px; display: flex; flex-direction: column; justify-content: flex-start;
    background: linear-gradient(135deg, var(--pc), color-mix(in srgb, var(--pc) 55%, #0b0b22)); }
  .class-card .banner-pattern { position: absolute; inset: -40% -20% auto auto; width: 180px; height: 180px; border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,.22), transparent 70%); }
  .class-card .banner-clip { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
  .class-card .banner-text { position: relative; min-width: 0; }
  .class-card .ctitle { display: block; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.05rem; color: #fff; line-height: 1.25;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-shadow: 0 1px 3px rgba(0,0,0,.2); }
  .class-card .csub { display: block; font-size: .8rem; color: rgba(255,255,255,.85); margin-top: 3px; }
  .class-card .tstack { position: absolute; right: 16px; bottom: -20px; display: flex; }
  .class-card .tavatar { width: 44px; height: 44px; border-radius: 50%; display: grid; place-items: center; margin-left: -12px; position: relative;
    font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: .86rem; color: #0b0b22; background: #fff; border: 3px solid var(--void);
    box-shadow: 0 2px 6px rgba(0,0,0,.25); }
  .class-card .tavatar.more { background: color-mix(in srgb, var(--pc) 22%, #fff); }
  html[data-theme="light"] .class-card .tavatar { border-color: #fff; }
  .class-card .card-body { padding: 26px 10px 10px; }
  .class-card .csummary { margin: 0 8px 8px; color: var(--muted); font-size: .84rem; }
  .class-card .tlist { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 2px; }
  .class-card .trow { width: 100%; display: flex; align-items: center; gap: 12px; padding: 10px 8px; border-radius: 12px; border: 0; background: transparent;
    text-align: left; font: inherit; color: inherit; cursor: pointer; transition: background .15s ease; }
  .class-card .trow:hover, .class-card .trow:focus-visible { background: color-mix(in srgb, var(--pc) 12%, transparent); outline: none; }
  .class-card .ravatar { flex: none; width: 34px; height: 34px; border-radius: 50%; display: grid; place-items: center; font-family: 'Space Grotesk', sans-serif;
    font-weight: 700; font-size: .74rem; color: #fff; background: var(--pc); }
  .class-card .rmain { flex: 1; min-width: 0; }
  .class-card .tname { display: block; font-weight: 600; font-size: .94rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .class-card .rmeta { display: flex; align-items: center; gap: 5px; margin-top: 2px; color: var(--muted); font-size: .78rem; flex-wrap: wrap; }
  .class-card .code { font-family: 'Space Mono', monospace; font-size: .7rem; letter-spacing: .06em; }
  .class-card .view { flex: none; font-size: .8rem; font-weight: 600; color: var(--pc); }
</style>
@endpush

@push('scripts')
<script>
  function adminClasses() {
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
