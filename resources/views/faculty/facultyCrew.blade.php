@extends('layouts.faculty-shell')
@section('title', 'Crew')

@section('content')
  <p style="margin-bottom:10px"><a href="{{ route('faculty.dashboard') }}" style="color:var(--muted);font-size:.85rem">← My Courses</a></p>

  <div class="page-head">
    <span class="eyebrow">Captain · {{ $crew->name }}</span>
    <h1>Your <span class="accent">crew</span></h1>
    <p class="sub" style="margin:0">{{ $crew->roster->count() }} aboard · code <strong style="font-family:'Space Mono',monospace;letter-spacing:.12em">{{ $crew->code }}</strong></p>
  </div>

  <section class="roster-card">
    <div class="roster">
      @foreach ($roster as $m)
        <div class="member">
          <span class="avatar">{{ strtoupper(substr($m->name, 0, 2)) }}</span>
          <span class="mname">{{ $m->name }}</span>
          @if ($m->pivot->role !== 'faculty' && $m->progressPercent !== null)
            <span style="color:var(--muted);font-size:.82rem">{{ $m->progressPercent }}% complete</span>
          @endif
          <span class="tag {{ $m->pivot->role === 'faculty' ? 'faculty' : 'student' }}">{{ $m->pivot->role === 'faculty' ? 'Captain' : 'Astronaut' }}</span>
        </div>
      @endforeach
    </div>
  </section>

  <section class="mods" id="modules">
        <h2>Learning modules</h2>
        <p class="lead">Add modules and upload your materials (PDF, Word, PowerPoint, Excel, images, ZIP). Students in your crew see them instantly.</p>

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
                    <a class="fname" href="{{ route('materials.download', $f) }}" title="{{ $f->name }}">{{ $f->name }}</a>
                    <span class="fsize">{{ \Illuminate\Support\Number::fileSize($f->size) }}</span>
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
          </article>
        @endforeach

        <form method="POST" action="{{ route('faculty.modules.store', $crew) }}" class="mod mnew">
          @csrf
          <h3 style="margin-bottom:12px">Add a module</h3>
          <div class="mrow">
            <input type="text" name="title" placeholder="Module title (e.g. Variables &amp; Data Types)" required maxlength="255" value="{{ old('title') }}">
            <input type="text" name="description" placeholder="Short description (optional)" maxlength="1000" value="{{ old('description') }}">
            <button class="mbtn solid" type="submit">Add module</button>
          </div>
        </form>
      </section>

  <section class="mods" id="quizzes">
        <h2>Quizzes</h2>
        <p class="lead">Create multiple-choice quizzes for your crew. Students see them on their crew page, are graded automatically, and their grades are based on these results.</p>

        @foreach ($crew->quizzes()->withCount(['questions', 'attempts'])->get() as $qz)
          @php $avg = $qz->attempts()->selectRaw('AVG(score * 100.0 / NULLIF(total, 0)) as a')->value('a'); @endphp
          <article class="mod">
            <div class="mod-top">
              <div>
                <span class="tagm">Quiz {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <h3>{{ $qz->title }}</h3>
                <p class="mdesc">{{ $qz->questions_count }} {{ \Illuminate\Support\Str::plural('question', $qz->questions_count) }}
                  @if ($qz->minutes) · {{ $qz->minutes }} min @endif
                  · {{ $qz->attempts_count }} {{ \Illuminate\Support\Str::plural('submission', $qz->attempts_count) }}
                  @if ($avg !== null) · class average {{ round($avg) }}% @endif</p>
              </div>
              <form method="POST" action="{{ route('faculty.quizzes.destroy', $qz) }}" onsubmit="return confirm('Delete this quiz and all student results for it?')">
                @csrf @method('DELETE')
                <button class="mbtn danger" type="submit">Delete quiz</button>
              </form>
            </div>
          </article>
        @endforeach

        <form method="POST" action="{{ route('faculty.quizzes.store', $crew) }}" class="mod mnew" id="quizForm">
          @csrf
          <h3 style="margin-bottom:12px">Create a quiz</h3>
          <div class="mrow">
            <input type="text" name="title" placeholder="Quiz title (e.g. Variables &amp; Types)" required maxlength="255">
            <input type="text" name="minutes" inputmode="numeric" placeholder="Minutes (optional)" maxlength="3" style="flex:0 1 150px">
          </div>
          <div id="qList"></div>
          <div class="mrow" style="margin-top:14px">
            <button class="mbtn" type="button" id="addQ">+ Add question</button>
            <button class="mbtn solid" type="submit">Publish quiz</button>
          </div>
        </form>
      </section>
@endsection

@push('scripts')
<script>
  , add = document.getElementById('addQ');
      if (!list) return;
      let n = 0;
      function question() {
        const qi = n++;
        const box = document.createElement('div'); box.className = 'qb';
        box.innerHTML = `<div class="mrow"><input type="text" name="questions[${qi}][prompt]" placeholder="Question" required maxlength="1000">
          <button type="button" class="mbtn danger rm">Remove</button></div><div class="opts"></div>
          <div class="mrow"><button type="button" class="mbtn addo">+ Add option</button></div>`;
        const opts = box.querySelector('.opts');
        const option = () => {
          const i = opts.children.length;
          const row = document.createElement('div'); row.className = 'mrow';
          row.innerHTML = `<label class="ok"><input type="radio" name="questions[${qi}][correct]" value="${i}" ${i === 0 ? 'checked' : ''}> correct</label>
            <input type="text" name="questions[${qi}][options][${i}]" placeholder="Option ${i + 1}" required maxlength="500">`;
          opts.appendChild(row);
        };
        option(); option();
        box.querySelector('.addo').onclick = () => { if (opts.children.length < 6) option(); };
        box.querySelector('.rm').onclick = () => box.remove();
        list.appendChild(box);
      }
      add.onclick = question;
      question();
    })();
</script>
@endpush
