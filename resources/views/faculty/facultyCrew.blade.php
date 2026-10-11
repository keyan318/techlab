@extends('layouts.faculty-shell')
@section('title', 'Materials')

@section('content')
  <style>
    .final-warn { display: flex; flex-wrap: wrap; align-items: center; gap: 14px; margin-top: 18px; padding: 16px 18px; border-radius: 18px; border: 1px solid rgba(255,196,0,.45); background: rgba(255,196,0,.09); }
    .final-warn p { color: var(--muted); font-size: .88rem; margin-top: 4px; }
    .final-warn-icon { flex: 0 0 34px; height: 34px; border-radius: 50%; display: grid; place-items: center; font-weight: 800; color: #1a1200; background: #ffc400; }
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
    <h1>Learning <span class="accent">materials</span></h1>
    <p class="sub" style="margin:0">code <strong style="font-family:'Space Mono',monospace;letter-spacing:.12em">{{ $crew->code }}</strong></p>
  </div>

  @include('faculty.partials.course-tabs', ['crew' => $crew])

  @if (session('status'))
    <p class="mok" role="status" style="margin-top:14px">{{ session('status') }}</p>
  @endif

  {{-- Until a final exam is live, nobody in this course can earn a certificate. --}}
  @if (! $final?->isPublished())
    <div class="final-warn" role="alert">
      <span class="final-warn-icon" aria-hidden="true">!</span>
      <div style="flex:1 1 260px">
        <strong>{{ $final ? 'Your final exam is still a draft' : 'Create the final exam for this course' }}</strong>
        <p>Students earn their certificate only by passing the final exam (their "Final Boss"). Until you {{ $final ? 'publish it' : 'create and publish one' }}, students who finish every lesson have to wait.</p>
      </div>
      <a class="mbtn solid" href="{{ $final ? route('faculty.quizzes.edit', $final) : route('faculty.quizzes.build', ['crew' => $crew, 'final' => 1]) }}">{{ $final ? 'Finish the final exam' : 'Create final exam' }}</a>
    </div>
  @endif

  <section class="mods" id="modules">
        <h2>Learning modules</h2>
        <p class="lead">Add modules and upload your materials (PDF, Word, PowerPoint, Excel, images, ZIP) or paste a link. Students in your crew see them instantly, and Astro answers their questions from whatever it can read — PDF, Word, PowerPoint and text files.</p>

        @if ($errors->any())
          <div class="merr">{{ $errors->first() }}</div>
        @endif

        @foreach ($crew->modules as $m)
          <article class="mod" id="module-{{ $m->id }}">
            <div class="mod-top">
              <div>
                <span class="tagm">Module {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <h3>{{ $m->title }}</h3>
                @if ($m->description)<p class="mdesc">{{ $m->description }}</p>@endif
              </div>
              <form method="POST" action="{{ route('faculty.modules.destroy', $m) }}" onsubmit="return confirm('Delete this module and all its files?')">
                @csrf @method('DELETE')
                <button class="mbtn danger" type="submit">Delete module</button>
              </form>
            </div>

            @if ($m->materials->isEmpty())
              <p class="mempty">No materials yet.</p>
            @else
              <ul class="mfiles">
                @foreach ($m->materials as $f)
                  <li>
                    <span class="ext">{{ $f->ext }}</span>
                    <a class="fname" href="{{ $f->openUrl() }}" @if ($f->isLink()) target="_blank" rel="noopener noreferrer" @endif title="{{ $f->name }}">{{ $f->name }}</a>
                    {{-- Whether Astro can answer from this one, in plain words. --}}
                    @if ($f->index_error)
                      <span class="fsize" title="{{ $f->index_error }}">Astro can't read this</span>
                    @elseif ($f->indexed_at)
                      <span class="fsize">Astro can read this</span>
                    @else
                      <span class="fsize">Reading…</span>
                    @endif
                    <span class="fsize">{{ $f->isLink() ? 'Link' : \Illuminate\Support\Number::fileSize($f->size) }}</span>
                    <form method="POST" action="{{ route('faculty.materials.destroy', $f) }}" onsubmit="return confirm('Remove this file?')">
                      @csrf @method('DELETE')
                      <button class="mbtn danger" type="submit" aria-label="Remove {{ $f->name }}">Remove</button>
                    </form>
                  </li>
                @endforeach
              </ul>
            @endif

            <form method="POST" action="{{ route('faculty.materials.store', $m) }}" enctype="multipart/form-data" class="mrow">
              @csrf
              <input type="file" name="files[]" multiple required aria-label="Files for {{ $m->title }}"
                     accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.csv,.zip,.png,.jpg,.jpeg,.gif,.webp,.txt,.md">
              <button class="mbtn solid" type="submit">Upload</button>
            </form>

            {{-- A link counts as a material too: Astro cites it, but does not read the page. --}}
            <form method="POST" action="{{ route('faculty.materials.link', $m) }}" class="mrow">
              @csrf
              <input type="text" name="name" required maxlength="255" placeholder="Link title"
                     aria-label="Link title for {{ $m->title }}">
              <input type="url" name="url" required maxlength="2048" placeholder="https://…"
                     aria-label="Link address for {{ $m->title }}">
              <button class="mbtn" type="submit">Add link</button>
            </form>
          </article>
        @endforeach

        <form method="POST" action="{{ route('faculty.modules.store', $crew) }}" enctype="multipart/form-data"
              class="mod mnew" x-data="addModuleForm(@js(old('title', '')))">
          @csrf
          <h3 style="margin-bottom:12px">Add a module</h3>

          <label class="mdrop" :class="{ over: dragging }"
                 @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                 @drop.prevent="drop($event)">
            <input type="file" name="files[]" multiple class="sr-only" x-ref="file" @change="pick($event.target.files)"
                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.csv,.zip,.png,.jpg,.jpeg,.gif,.webp,.txt,.md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            <span x-show="!fileNames.length">Drop a PDF, Word, PowerPoint, Excel, image or ZIP here, or click to choose</span>
            <span x-show="fileNames.length" x-text="fileNames.join(', ')"></span>
          </label>

          <div class="mrow" style="margin-top:12px">
            <input type="text" name="title" placeholder="Module title (e.g. Variables &amp; Data Types)" required maxlength="255" x-model="title">
            <input type="text" name="description" placeholder="Short description (optional)" maxlength="1000" value="{{ old('description') }}">
            <button class="mbtn solid" type="submit">Add module</button>
          </div>
        </form>
      </section>

  <section class="mods" id="final-exam">
        <h2>Final exam</h2>
        <p class="lead">The Final Boss. Students unlock it after finishing every lesson and take it once, in full screen. They earn the certificate only if they reach your pass mark. If a student leaves full screen, switches tab or window, or tries to screenshot, the exam ends as a fail and you get an email.</p>

        @if (! $final)
          <p class="mrow" style="margin-top:12px">
            <a class="mbtn" href="{{ route('faculty.quizzes.build', ['crew' => $crew, 'final' => 1, 'mode' => 'manual']) }}">Create final exam manually</a>
            <a class="mbtn solid" href="{{ route('faculty.quizzes.build', ['crew' => $crew, 'final' => 1, 'mode' => 'astro']) }}">Create final exam with Astro</a>
          </p>
        @else
          @php
            $taken = collect($finalResults)->whereIn('state', ['passed', 'failed', 'caught']);
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
                        <span class="fpill bad">Failed · {{ $a->percent() }}%</span>
                        @break
                      @case('caught')
                        <span class="fpill bad" title="{{ $a->finished_at?->format('j M Y, g:ia') }}">Caught: {{ $a->violationText() }}</span>
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
                    @if (in_array($row['state'], ['failed', 'caught']))
                      <form method="POST" action="{{ route('faculty.final-exam.retake', $a) }}" onsubmit="return confirm('Let {{ addslashes($row['student']->name) }} take the final exam again? Their current result will be removed.')">
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

  <section class="mods" id="quizzes">
        <h2>Quizzes</h2>
        <p class="lead">Write a quiz yourself, or have Astro draft one from a material you uploaded. Students see it on their course page once you publish, and it is graded automatically.</p>
        <p class="mrow" style="margin-top:12px">
          <a class="mbtn" href="{{ route('faculty.quizzes.build', ['crew' => $crew, 'mode' => 'manual']) }}">Create quiz manually</a>
          <a class="mbtn solid" href="{{ route('faculty.quizzes.build', ['crew' => $crew, 'mode' => 'astro']) }}">Create quiz with Astro</a>
        </p>

        @foreach ($crew->quizzes()->regular()->withCount(['questions', 'attempts'])->get() as $qz)
          @php $avg = $qz->attempts()->selectRaw('AVG(score * 100.0 / NULLIF(total, 0)) as a')->value('a'); @endphp
          <article class="mod">
            <div class="mod-top">
              <div>
                <span class="tagm">Quiz {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <h3>{{ $qz->title }}</h3>
                <p class="mdesc">{{ $qz->isPublished() ? 'Published' : 'Draft — students cannot see it yet' }}
                  · {{ $qz->questions_count }} {{ \Illuminate\Support\Str::plural('question', $qz->questions_count) }}
                  @if ($qz->minutes) · {{ $qz->minutes }} min @endif
                  @if ($qz->due_at) · due {{ $qz->due_at->format('j M Y, g:ia') }} @endif
                  · {{ $qz->attempts_count }} {{ \Illuminate\Support\Str::plural('submission', $qz->attempts_count) }}
                  @if ($avg !== null) · class average {{ round($avg) }}% @endif</p>
              </div>
              <div class="mrow">
                <a class="mbtn" href="{{ route('faculty.quizzes.edit', $qz) }}">Edit</a>
                <form method="POST" action="{{ route('faculty.quizzes.destroy', $qz) }}" onsubmit="return confirm('Delete this quiz and all student results for it?')">
                  @csrf @method('DELETE')
                  <button class="mbtn danger" type="submit">Delete quiz</button>
                </form>
              </div>
            </div>
          </article>
        @endforeach

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
