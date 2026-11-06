{{-- Sidebar "Courses": the faculty member's courses as the same cards students see (FacultyCourseController::index).
     A card opens the course in the course player, where the lessons are edited; "Edit card" changes the card. --}}
@extends('layouts.faculty-shell')
@section('title', 'Courses')

@push('head')
  @include('components.course-card-styles')
  <style>
    .fc-grid { display: grid; gap: 24px; margin-top: 18px; align-items: start; grid-template-columns: repeat(auto-fill, minmax(min(100%, 340px), 1fr)); }
    .fc-meta { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
    .fc-tag { font-size: 11.5px; font-weight: 600; padding: 3px 9px; border-radius: 99px; color: #c3c9ea; background: rgba(255,255,255,.06); border: 1px solid rgba(150,170,255,.2); }
    .fc-tag.is-edited { color: #ffe08a; background: rgba(245,192,74,.1); border-color: rgba(245,192,74,.3); }
    .fc-edit { position: absolute; top: 12px; right: 12px; z-index: 2; display: inline-flex; align-items: center; gap: 7px; padding: 7px 12px; border-radius: 99px; border: 1px solid rgba(255,255,255,.22); background: rgba(8,10,30,.62); color: #fff; font: 600 12.5px 'Inter', sans-serif; cursor: pointer; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); transition: background-color .18s ease, border-color .18s ease, transform .12s ease; }
    .fc-edit:hover { background: rgba(8,10,30,.88); border-color: rgba(255,255,255,.6); }
    .fc-edit:active { transform: scale(.97); }
    .fc-edit:focus-visible { outline: 2px solid #73b6ff; outline-offset: 2px; }
    .fc-edit svg { width: 14px; height: 14px; flex: none; }
    .fc-wrap { position: relative; display: flex; flex-direction: column; }

    /* A course the faculty member built: its status, and Publish while it's still a draft.
       Flush against the card above it (same width, no gap) so it reads as that card's own footer,
       not a separate floating box — important once several courses are in the grid at once. */
    .fc-status { display: flex; align-items: center; gap: 12px; text-align: left; padding: 10px 10px 10px 16px;
      border: 2px solid #252b52; border-top: 0; border-radius: 0 0 6px 6px; background: var(--glass); }
    html[data-theme="light"] .fc-status { background: #fff; }
    .fc-wrap:has(.fc-status) > .course-card { border-radius: 6px 6px 0 0; }
    .fc-status-dot { position: relative; width: 8px; height: 8px; flex: none; border-radius: 50%; background: var(--dot); box-shadow: 0 0 0 4px color-mix(in srgb, var(--dot) 18%, transparent); }
    .fc-status-text { display: flex; flex-direction: column; min-width: 0; flex: 1; line-height: 1.3; }
    .fc-status-text b { font: 700 13px 'Inter', sans-serif; color: var(--text); }
    .fc-status-text span { font-size: 12.5px; color: var(--muted); }
    .fc-publish { display: inline-flex; align-items: center; gap: 7px; flex: none; padding: 8px 14px; border-radius: 9px; border: 0; background: #73b6ff; color: #06142e; font: 700 13px 'Inter', sans-serif; cursor: pointer; transition: background-color .18s ease, transform .12s ease, box-shadow .18s ease; box-shadow: 0 6px 16px -8px rgba(115,182,255,.9); }
    .fc-publish:hover { background: #8fc5ff; }
    .fc-publish:active { transform: scale(.97); }
    .fc-publish:focus-visible { outline: 2px solid #73b6ff; outline-offset: 2px; }
    .fc-publish:disabled { background: var(--glass-border); color: var(--muted); box-shadow: none; cursor: not-allowed; }
    .fc-publish svg { width: 15px; height: 15px; }
    .fc-confirm-list { margin: 0 0 4px; padding: 0; list-style: none; display: grid; gap: 8px; font-size: 13.5px; color: #c3c9ea; }
    .fc-confirm-list li { display: flex; gap: 10px; align-items: flex-start; }
    .fc-confirm-list li::before { content: ''; width: 6px; height: 6px; margin-top: 7px; flex: none; border-radius: 50%; background: #73b6ff; }
    .fc-empty { margin-top: 28px; padding: 22px; border-radius: 16px; border: 1px dashed var(--glass-border); color: var(--muted); }
    .fc-status-msg { margin-top: 14px; padding: 10px 16px; border-radius: 10px; border: 1px solid rgba(115,182,255,.4); background: rgba(115,182,255,.08); color: var(--text); font-size: 13.5px; }

    .fc-back { position: fixed; inset: 0; z-index: 90; display: grid; place-items: center; padding: 16px; background: rgba(6,8,26,.6); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); }
    .fc-modal { width: min(460px, 100%); max-height: 92vh; overflow: auto; padding: 22px; border-radius: 18px; background: #12153a; border: 1px solid rgba(150,170,255,.28); color: #eef1ff; text-align: left; box-shadow: 0 40px 90px -30px rgba(0,0,0,.8); }
    .fc-modal h2 { margin: 0 0 4px; font: 700 1.3rem 'Space Grotesk', sans-serif; }
    .fc-modal p.hint { margin: 0 0 16px; font-size: 13.5px; color: #b9c0e6; }
    .fc-modal label { display: block; margin: 12px 0 6px; font: 700 11.5px 'Space Mono', monospace; letter-spacing: .12em; text-transform: uppercase; color: #9aa3d0; }
    .fc-modal input[type=text], .fc-modal select { width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(150,170,255,.28); background: rgba(255,255,255,.06); color: inherit; font: 500 15px 'Inter', sans-serif; }
    .fc-modal select option { color: #0b1230; }
    .fc-preview { aspect-ratio: 5 / 2; border-radius: 8px; overflow: hidden; border: 1px solid rgba(150,170,255,.25); background: #0d1028 center bottom / cover no-repeat; image-rendering: pixelated; }
    .fc-actions { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; margin-top: 18px; }
    .fc-btn { padding: 9px 14px; border-radius: 10px; border: 1px solid rgba(150,170,255,.3); background: transparent; color: inherit; font: 600 13.5px 'Inter', sans-serif; cursor: pointer; }
    .fc-btn.primary { border: none; border-radius: 2px; background: #14a0ff; color: #fff; font-weight: 700;
      box-shadow: 0 -2px 0 0 #0a5fb5, 0 2px 0 0 #0a5fb5, -2px 0 0 0 #0a5fb5, 2px 0 0 0 #0a5fb5,
                  inset 0 -3px 0 0 rgba(0,0,0,.2), inset 0 2px 0 0 rgba(255,255,255,.22); }
    .fc-btn.primary:hover { filter: brightness(1.08); }
    .fc-btn.primary:active { transform: translateY(2px); }
    .fc-btn.danger { color: #ffb3c2; border-color: rgba(255,99,132,.35); margin-right: auto; }
    .fc-btn:disabled { opacity: .6; cursor: default; }
    .fc-err { color: #ff9db0; font-size: 13px; margin-top: 10px; }
  </style>
@endpush

@section('content')
  <div class="page-head" style="display:flex; align-items:flex-end; justify-content:space-between; gap:16px; flex-wrap:wrap">
    <div>
      <span class="eyebrow">Captain · Courses</span>
      <h1>Your <span class="accent">courses</span></h1>
    </div>
    <a href="{{ route('faculty.courses.create') }}" class="btn btn-primary" style="text-decoration:none; white-space:nowrap">+ Build a course</a>
  </div>

  @if (session('status'))
    <div class="fc-status-msg">{{ session('status') }}</div>
  @endif

  @if (empty($courses))
    <div class="fc-empty">No course has been assigned to you yet. An admin assigns courses on the Course Assignment page.</div>
  @else
    <div class="fc-grid" x-data="cardEditor()">
      @foreach ($courses as $c)
        @php
          $accent = ['programming' => '#73b6ff', 'networking' => '#5be1ff', 'cybersecurity' => '#b48bff'][$c['planet']] ?? '#73b6ff';
          $levelBars = ['Beginner' => 1, 'Intermediate' => 2, 'Advanced' => 3][$c['level']] ?? 1;
        @endphp
        <div class="fc-wrap" @if ($loop->first) data-tour="fc-card" @endif>
          <a class="course-card" href="{{ $c['url'] }}" style="--accent: {{ $accent }}" aria-label="{{ $c['title'] }}: open to manage">
            <div class="course-banner" style="background: {{ $c['banner'] ?? '#1b1650' }}">
              @if (! empty($c['art']))
                <img class="course-art" src="{{ asset($c['art']) }}" width="1250" height="500" alt="" decoding="async">
              @elseif (! empty($c['scene']))
                <x-dynamic-component :component="$c['scene']" />
              @elseif (! empty($c['logo']))
                <x-dynamic-component :component="$c['logo']" />
              @endif
            </div>
            <div class="flex flex-1 flex-col px-5 pb-5 pt-4">
              <div class="course-eyebrow">{{ $c['planet_title'] }}</div>
              <div class="course-title mt-1">{{ $c['title'] }}</div>
              @if (! empty($c['blurb']))
                <div class="course-blurb mt-2">{{ $c['blurb'] }}</div>
              @endif
              <div class="captain-line">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.4"/><path d="M4.5 19.4c.7-3.6 3.6-5.8 7.5-5.8s6.8 2.2 7.5 5.8"/></svg>
                Captain: {{ $c['faculty_name'] }}
              </div>
              <div class="fc-meta">
                <span class="fc-tag">{{ $c['modules'] }} {{ \Illuminate\Support\Str::plural('module', $c['modules']) }}</span>
                <span class="fc-tag">{{ $c['lessons'] }} {{ \Illuminate\Support\Str::plural('lesson', $c['lessons']) }}</span>
                @if ($c['edited_lessons'])
                  <span class="fc-tag is-edited">{{ $c['edited_lessons'] }} edited</span>
                @endif
              </div>
              <div class="mt-auto pt-5 flex items-center justify-between gap-3">
                <span class="course-pill">
                  <span class="course-bars" aria-hidden="true">@for ($b = 1; $b <= 3; $b++)<i class="{{ $b <= $levelBars ? 'on' : '' }}"></i>@endfor</span>
                  {{ $c['level'] }}
                </span>
                <span class="course-go">Manage
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </span>
              </div>
            </div>
          </a>
          <button type="button" class="fc-edit" @if ($loop->first) data-tour="fc-edit" @endif @click="open(@js([
              'url' => $c['card_url'], 'title' => $c['title'], 'blurb' => $c['blurb'] ?? '', 'level' => $c['level'],
              'art' => ! empty($c['art']) ? asset($c['art']) : null, 'banner' => $c['banner'] ?? '#1b1650', 'edited' => $c['card_edited'],
          ]))">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4L19 9a2.8 2.8 0 0 0-4-4L4 16z"/><path d="M13.5 6.5l4 4"/></svg>
            Edit card
          </button>
          @if ($c['own'])
            <div class="fc-status" style="--dot: {{ $c['draft'] ? '#f5c04a' : '#4ade80' }}">
              <span class="fc-status-dot" aria-hidden="true"></span>
              <span class="fc-status-text">
                @if ($c['draft'])
                  <b>Draft</b>
                  <span>{{ $c['lessons'] ? 'Only you can see this course.' : 'Add a lesson before you publish.' }}</span>
                @else
                  <b>Published</b>
                  <span>Students can find and join it.</span>
                @endif
              </span>
              @if ($c['draft'])
                <button type="button" class="fc-publish" @disabled(! $c['lessons'])
                        @click="publishing = @js(['url' => $c['publish_url'], 'title' => $c['title'], 'modules' => $c['modules'], 'lessons' => $c['lessons']])">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                  Publish
                </button>
              @endif
            </div>
          @endif
        </div>
      @endforeach

      <template x-if="publishing">
        <div class="fc-back" @mousedown.self="publishing = null" @keydown.escape.window="publishing = null">
          <form class="fc-modal" method="POST" :action="publishing.url" role="dialog" aria-modal="true" aria-labelledby="fc-pub-h">
            @csrf
            <h2 id="fc-pub-h">Publish <span x-text="publishing.title"></span>?</h2>
            <p class="hint">Once it's live, astronauts can find it in the course list.</p>
            <ul class="fc-confirm-list">
              <li><span>They can ask to join your class, or take it self-paced.</span></li>
              <li><span x-text="publishing.modules + (publishing.modules === 1 ? ' module' : ' modules') + ' and ' + publishing.lessons + (publishing.lessons === 1 ? ' lesson' : ' lessons') + ' go live now. You can keep editing after.'"></span></li>
            </ul>
            <div class="fc-actions">
              <button type="button" class="fc-btn" @click="publishing = null">Not yet</button>
              <button type="submit" class="fc-btn primary" x-init="$nextTick(() => $el.focus())">Publish course</button>
            </div>
          </form>
        </div>
      </template>

      <template x-if="card">
        <div class="fc-back" @mousedown.self="card = null" @keydown.escape.window="card = null">
          <form class="fc-modal" role="dialog" aria-modal="true" aria-label="Edit course card" @submit.prevent="save()">
            <h2>Edit course card</h2>
            <p class="hint">This is the card your students pick the course from.</p>

            <div class="fc-preview" :style="'background-image:url(' + (preview || card.art || '') + ');background-color:' + card.banner"></div>
            <label for="fc-art">Cover image</label>
            <input id="fc-art" type="file" accept="image/png,image/jpeg,image/webp,image/gif" @change="pick($event)">

            <label for="fc-title">Title</label>
            <input id="fc-title" type="text" maxlength="80" required x-model="card.title">

            <label for="fc-blurb">Subtitle</label>
            <input id="fc-blurb" type="text" maxlength="160" x-model="card.blurb">

            <label for="fc-level">Level</label>
            <select id="fc-level" x-model="card.level">
              @foreach ($levels as $l)<option>{{ $l }}</option>@endforeach
            </select>

            <p class="fc-err" x-show="err" x-text="err" role="alert"></p>
            <div class="fc-actions">
              <button type="button" class="fc-btn danger" x-show="card.edited" @click="reset()" :disabled="busy">Reset to default</button>
              <button type="button" class="fc-btn" @click="card = null">Cancel</button>
              <button type="submit" class="fc-btn primary" :disabled="busy" x-text="busy ? 'Saving…' : 'Save'"></button>
            </div>
          </form>
        </div>
      </template>
    </div>
  @endif
@endsection

@push('scripts')
<script>
  function cardEditor() {
    return {
      card: null, publishing: null, file: null, preview: null, busy: false, err: '',
      open(c) { this.card = Object.assign({}, c); this.file = null; this.preview = null; this.err = ''; },
      pick(e) {
        this.file = e.target.files[0] || null;
        if (this.preview) URL.revokeObjectURL(this.preview);
        this.preview = this.file ? URL.createObjectURL(this.file) : null;
      },
      async send(fd) {
        this.busy = true; this.err = '';
        try {
          const res = await fetch(this.card.url, { method: 'POST', body: fd, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) } });
          const data = await res.json().catch(() => ({}));
          if (!res.ok) {
            const first = data.errors ? Object.values(data.errors)[0] : null;
            throw new Error((first && first[0]) || data.message || 'Something went wrong. Try again.');
          }
          location.reload();
        } catch (e) { this.err = e.message; this.busy = false; }
      },
      save() {
        if (this.file && this.file.size > 4 * 1024 * 1024) { this.err = 'The cover image can be at most 4 MB.'; return; }
        const fd = new FormData();
        fd.append('title', this.card.title);
        fd.append('blurb', this.card.blurb || '');
        fd.append('level', this.card.level);
        if (this.file) fd.append('art', this.file);
        this.send(fd);
      },
      reset() {
        if (!confirm('Put the card back to its original title, subtitle, level and cover?')) return;
        const fd = new FormData();
        fd.append('reset', '1');
        this.send(fd);
      },
    };
  }
</script>
@endpush
