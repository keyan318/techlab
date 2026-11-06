@extends('layouts.faculty-shell')
@section('title', 'Materials')

@section('content')
  <style>
    .final-warn { display: flex; flex-wrap: wrap; align-items: center; gap: 14px; margin-top: 18px; padding: 16px 18px; border-radius: 18px; border: 1px solid rgba(255,196,0,.45); background: rgba(255,196,0,.09); }
    .final-warn p { color: var(--muted); font-size: .88rem; margin-top: 4px; }
    .final-warn-icon { flex: 0 0 34px; height: 34px; border-radius: 50%; display: grid; place-items: center; font-weight: 800; color: #1a1200; background: #ffc400; }
    /* Learning modules */
    .lm-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 12px 20px; margin-bottom: 18px; }
    .lm-head > div { flex: 1 1 360px; }
    .lm-head .lead { margin-bottom: 0; max-width: 68ch; }
    .lm-card { padding: 18px 20px; }
    .lm-top { display: flex; align-items: center; gap: 14px; }
    .lm-num { flex: 0 0 36px; height: 36px; border-radius: 12px; display: grid; place-items: center; font-family: 'Space Grotesk', sans-serif; font-weight: 700; color: var(--blue); background: rgba(115,182,255,.14); }
    .lm-title { flex: 1; min-width: 0; }
    .lm-title h3 { margin: 0; line-height: 1.3; overflow-wrap: anywhere; }
    .lm-meta { color: var(--muted); font-size: .84rem; margin-top: 2px; }
    .lm-icon { flex: 0 0 auto; width: 36px; height: 36px; display: grid; place-items: center; border-radius: 10px; border: 1px solid transparent; background: transparent; color: var(--muted); cursor: pointer; transition: background .15s, color .15s, border-color .15s; }
    .lm-icon:hover { background: rgba(115,182,255,.12); color: var(--text); }
    .lm-icon.danger:hover, .lm-files .lm-icon:hover { color: #ff6b81; background: rgba(255,99,132,.12); }
    .lm-icon:focus-visible, .lm-btn:focus-within, .lm-fname:focus-visible { outline: 2px solid var(--blue); outline-offset: 2px; }
    .lm-files { list-style: none; margin: 16px 0 0; padding: 0; border: 1px solid var(--glass-border); border-radius: 14px; overflow: hidden; }
    .lm-files li { display: flex; align-items: center; gap: 12px; padding: 10px 8px 10px 12px; background: var(--field); }
    .lm-files li + li { border-top: 1px solid var(--glass-border); }
    .lm-ext { flex: 0 0 48px; text-align: center; font-family: 'Space Mono', monospace; font-size: .64rem; font-weight: 700; text-transform: uppercase; padding: 6px 0; border-radius: 8px; background: rgba(115,182,255,.16); color: var(--blue); }
    .lm-fbody { flex: 1; min-width: 0; display: flex; flex-direction: column; }
    .lm-fname { font-weight: 600; font-size: .92rem; color: var(--text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; text-decoration: none; }
    .lm-fname:hover { color: var(--blue); }
    .lm-fmeta { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 10px; font-size: .76rem; color: var(--muted); }
    .lm-astro { display: inline-flex; align-items: center; gap: 5px; }
    .lm-astro::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: currentColor; opacity: .7; }
    .lm-astro.good { color: #3fbf7f; }
    .lm-astro.bad { color: #ff6b81; }
    html[data-theme="light"] .lm-astro.good { color: #0f7a4a; }
    html[data-theme="light"] .lm-astro.bad { color: #a61b41; }
    .lm-empty { margin-top: 14px; padding: 14px; border: 1.5px dashed var(--glass-border); border-radius: 14px; color: var(--muted); font-size: .88rem; text-align: center; }
    .lm-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
    .lm-btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; font-size: .84rem; }
    .lm-btn svg { color: var(--blue); }
    .lm-btn.busy { opacity: .6; pointer-events: none; }
    .lm-link { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.4fr) auto; gap: 8px; margin-top: 10px; }
    .lm-link-btns { display: flex; gap: 8px; }
    .mods input[type=url] { padding: 11px 14px; border-radius: 12px; background: var(--field); border: 1px solid var(--glass-border); color: var(--text); font: inherit; min-width: 0; }
    .lm-link input[type=text] { min-width: 0; }
    .lm-new { border-style: dashed; }
    .lm-new h3 { margin: 0; }
    .lm-new-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 10px; margin: 14px 0 10px; }
    .lm-new-foot { display: flex; justify-content: flex-end; margin-top: 12px; }
    @media (max-width: 640px) {
      .lm-link, .lm-new-grid { grid-template-columns: 1fr; }
      .lm-link-btns > * { flex: 1; }
      .lm-card { padding: 16px 14px; }
      .lm-files li { gap: 10px; padding-left: 10px; }
      .lm-ext { flex-basis: 40px; }
    }
  </style>
  <p style="margin-bottom:10px"><a href="{{ route('faculty.dashboard') }}" style="color:var(--muted);font-size:.85rem">← My Courses</a></p>

  <div class="page-head">
    <span class="eyebrow">Captain · {{ $crew->name }}</span>
    <h1>Learning <span class="accent">materials</span></h1>
    <p class="sub" style="margin:0">code <strong style="font-family:'Space Mono',monospace;letter-spacing:.12em">{{ $crew->code }}</strong></p>
  </div>

  @include('faculty.partials.course-tabs', ['crew' => $crew])

  @if (session('status'))
    <p class="mok" role="status" style="margin-top:14px">{{ session('status') }}</p>
  @endif

  {{-- Until a final exam is live, nobody in this course can earn a certificate. --}}
  @if (! $final?->isPublished())
    <div class="final-warn" role="alert" data-tour="final-warn">
      <span class="final-warn-icon" aria-hidden="true">!</span>
      <div style="flex:1 1 260px">
        <strong>{{ $final ? 'Your final exam is still a draft' : 'Create the final exam for this course' }}</strong>
        <p>Students earn their certificate only by passing the final exam (their "Final Boss"). Until you {{ $final ? 'publish it' : 'create and publish one' }}, students who finish every lesson have to wait.</p>
      </div>
      <a class="mbtn solid" href="{{ $final ? route('faculty.quizzes.edit', $final) : route('faculty.quizzes.build', ['crew' => $crew, 'final' => 1]) }}">{{ $final ? 'Finish the final exam' : 'Create final exam' }}</a>
    </div>
  @endif

  <section class="mods" id="modules">
        <div class="lm-head" data-tour="lm-head">
          <div>
            <h2>Learning modules</h2>
            <p class="lead">Files and links you add here show up for your students right away. Astro answers their questions from PDF, Word, PowerPoint and text files.</p>
          </div>
          @if ($crew->modules->isNotEmpty())
            <a class="mbtn" href="#add-module">+ New module</a>
          @endif
        </div>

        @if ($errors->any())
          <div class="merr">{{ $errors->first() }}</div>
        @endif

        @foreach ($crew->modules as $m)
          <article class="mod lm-card" id="module-{{ $m->id }}" x-data="{ link: false, uploading: false }">
            <header class="lm-top">
              <span class="lm-num" aria-label="Module {{ $loop->iteration }}">{{ $loop->iteration }}</span>
              <div class="lm-title">
                <h3>{{ $m->title }}</h3>
                <p class="lm-meta">
                  {{ $m->materials->count() }} {{ \Illuminate\Support\Str::plural('item', $m->materials->count()) }}@if ($m->description) <span aria-hidden="true">·</span> {{ $m->description }}@endif
                </p>
              </div>
              <form method="POST" action="{{ route('faculty.modules.destroy', $m) }}" onsubmit="return confirm('Delete this module and all its files?')">
                @csrf @method('DELETE')
                <button class="lm-icon danger" type="submit" title="Delete module" aria-label="Delete module {{ $m->title }}">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                </button>
              </form>
            </header>

            @if ($m->materials->isEmpty())
              <p class="lm-empty">Nothing here yet. Upload a file or add a link below.</p>
            @else
              <ul class="lm-files">
                @foreach ($m->materials as $f)
                  <li>
                    <span class="lm-ext">{{ $f->isLink() ? 'LINK' : $f->ext }}</span>
                    <div class="lm-fbody">
                      <a class="lm-fname" href="{{ $f->openUrl() }}" @if ($f->isLink()) target="_blank" rel="noopener noreferrer" @endif title="{{ $f->name }}">{{ $f->name }}</a>
                      <span class="lm-fmeta">
                        @unless ($f->isLink())<span>{{ \Illuminate\Support\Number::fileSize($f->size) }}</span>@endunless
                        {{-- Whether Astro can answer from this one, in plain words. --}}
                        @if ($f->isLink())
                          <span class="lm-astro" title="{{ $f->index_error }}">Astro shares the link</span>
                        @elseif (! $f->isReadable())
                          <span class="lm-astro" title="{{ $f->index_error }}">Students only, Astro can't read this type</span>
                        @elseif ($f->index_error)
                          <span class="lm-astro bad" title="{{ $f->index_error }}">Astro can't read this</span>
                        @elseif ($f->indexed_at)
                          <span class="lm-astro good">Astro can read this</span>
                        @else
                          <span class="lm-astro">Astro is reading…</span>
                        @endif
                      </span>
                    </div>
                    <form method="POST" action="{{ route('faculty.materials.destroy', $f) }}" onsubmit="return confirm('Remove this file?')">
                      @csrf @method('DELETE')
                      <button class="lm-icon" type="submit" title="Remove" aria-label="Remove {{ $f->name }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                      </button>
                    </form>
                  </li>
                @endforeach
              </ul>
            @endif

            <div class="lm-actions" @if ($loop->first) data-tour="lm-upload" @endif>
              {{-- Picking files uploads them straight away: no separate Upload click. --}}
              <form method="POST" action="{{ route('faculty.materials.store', $m) }}" enctype="multipart/form-data" x-ref="up">
                @csrf
                <label class="mbtn lm-btn" :class="{ busy: uploading }">
                  <input type="file" name="files[]" multiple required class="sr-only" aria-label="Upload files to {{ $m->title }}"
                         accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.csv,.zip,.png,.jpg,.jpeg,.gif,.webp,.txt,.md"
                         @change="if ($event.target.files.length) { uploading = true; $refs.up.submit() }">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                  <span x-text="uploading ? 'Uploading…' : 'Upload files'">Upload files</span>
                </label>
              </form>
              <button type="button" class="mbtn lm-btn" x-show="!link" @click="link = true; $nextTick(() => $refs.linkName.focus())">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                Add link
              </button>
            </div>

            {{-- A link counts as a material too: Astro cites it, but does not read the page. --}}
            <form method="POST" action="{{ route('faculty.materials.link', $m) }}" class="lm-link" x-show="link" x-cloak @keydown.escape="link = false">
              @csrf
              <input type="text" name="name" required maxlength="255" placeholder="Link title" x-ref="linkName"
                     aria-label="Link title for {{ $m->title }}">
              <input type="url" name="url" required maxlength="2048" placeholder="https://…"
                     aria-label="Link address for {{ $m->title }}">
              <div class="lm-link-btns">
                <button class="mbtn solid" type="submit">Save link</button>
                <button class="mbtn" type="button" @click="link = false">Cancel</button>
              </div>
            </form>
          </article>
        @endforeach

        <form method="POST" action="{{ route('faculty.modules.store', $crew) }}" enctype="multipart/form-data" id="add-module"
              class="mod lm-new" data-tour="lm-add" x-data="addModuleForm(@js(old('title', '')))">
          @csrf
          <h3>Add a module</h3>
          <p class="lm-meta">Give it a title, and drop in its files now or later.</p>

          <div class="lm-new-grid">
            <input type="text" name="title" placeholder="Module title (e.g. Variables &amp; Data Types)" required maxlength="255" x-model="title" aria-label="Module title">
            <input type="text" name="description" placeholder="Short description (optional)" maxlength="1000" value="{{ old('description') }}" aria-label="Module description">
          </div>

          <label class="mdrop" :class="{ over: dragging }"
                 @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                 @drop.prevent="drop($event)">
            <input type="file" name="files[]" multiple class="sr-only" x-ref="file" @change="pick($event.target.files)"
                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.csv,.zip,.png,.jpg,.jpeg,.gif,.webp,.txt,.md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            <span x-show="!fileNames.length">Drop files here, or click to choose (PDF, Word, PowerPoint, Excel, images, ZIP)</span>
            <span x-show="fileNames.length" x-text="fileNames.join(', ')"></span>
          </label>

          <div class="lm-new-foot">
            <button class="mbtn solid" type="submit">Add module</button>
          </div>
        </form>
      </section>

  @push('scripts')
    <script>
      function addModuleForm(initialTitle) {
        return {
          dragging: false,
          fileNames: [],
          title: initialTitle || '',
          pick(files) {
            this.fileNames = Array.from(files).map(f => f.name);
            if (!this.title && files.length) {
              this.title = this.titleFromFilename(files[0].name);
            }
          },
          drop(event) {
            this.dragging = false;
            this.$refs.file.files = event.dataTransfer.files;
            this.pick(event.dataTransfer.files);
          },
          titleFromFilename(name) {
            return name.replace(/\.[^.]+$/, '').replace(/[_-]+/g, ' ').replace(/\s+/g, ' ').trim();
          },
        };
      }
    </script>
  @endpush
@endsection
