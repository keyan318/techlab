@extends('layouts.faculty-shell')
@section('title', 'Edit lesson quiz')

@section('content')
  <style>
    .wrap:has(.lqe) { text-align: left; }
    .lqe-bar { position: sticky; top: 0; z-index: 5; display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-top: 18px; padding: 12px 14px; border-radius: 16px; background: var(--glass); border: 1px solid var(--glass-border); backdrop-filter: blur(12px); }
    .lqe-bar .grow { flex: 1 1 160px; color: var(--muted); font-size: .86rem; }
    .lqe-bar .err { color: #ff9bb0; }
    html[data-theme="light"] .lqe-bar .err { color: #c62550; }
    .lqe-q { position: relative; }
    .lqe-q .mod-top { align-items: center; }
    .lqe-tools { display: flex; gap: 4px; }
    .lqe-icon { width: 34px; height: 34px; border-radius: 50%; border: 1px solid var(--glass-border); background: transparent; color: var(--muted); cursor: pointer; font-size: .95rem; line-height: 1; }
    .lqe-icon:hover:not(:disabled) { color: var(--text); background: rgba(115,182,255,.14); }
    .lqe-icon:disabled { opacity: .35; cursor: not-allowed; }
    .lqe-label { display: block; font-size: .78rem; font-weight: 600; color: var(--muted); margin: 14px 0 6px; }
    .lqe textarea, .lqe input[type=text] { width: 100%; padding: 10px 13px; border-radius: 12px; background: var(--field); border: 1px solid var(--glass-border); color: var(--text); font: inherit; resize: vertical; }
    .lqe textarea:focus, .lqe input[type=text]:focus { outline: none; border-color: var(--blue); }
    .lqe-opt { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
    .lqe-opt .letter { flex: 0 0 30px; height: 30px; border-radius: 50%; display: grid; place-items: center; font-weight: 700; font-size: .85rem; background: rgba(255,255,255,.07); color: var(--muted); }
    .lqe-opt.right .letter { background: #8bd83a; color: #0b1a00; }
    .lqe-opt.right input[type=text] { border-color: rgba(139,216,58,.6); }
    .lqe-opt label.pick { display: flex; align-items: center; gap: 5px; font-size: .78rem; color: var(--muted); cursor: pointer; white-space: nowrap; }
    .lqe-opt.right label.pick { color: #8bd83a; font-weight: 600; }
    html[data-theme="light"] .lqe-opt.right label.pick { color: #2f7a05; }
    .lqe-tip { margin-top: 8px; font-size: .8rem; color: #ffc400; }
    html[data-theme="light"] .lqe-tip { color: #8a5a00; }
    .lqe-help { color: var(--muted); font-size: .84rem; }
    .lqe-help code { font-family: 'Space Mono', monospace; font-size: .8rem; }
  </style>

  <p style="margin-bottom:10px"><a href="{{ route('faculty.lesson-quizzes.index', $crew) }}" style="color:var(--muted);font-size:.85rem">← All lesson quizzes</a></p>

  <div class="page-head">
    <span class="eyebrow">Captain · {{ $crew->name }} · Lesson {{ (int) preg_replace('/\D/', '', $module) }}.{{ (int) preg_replace('/\D/', '', $lesson) }}</span>
    <h1>{{ $title }} <span class="accent">quiz</span></h1>
    <p class="sub" style="margin:0">{{ $edited ? 'You have edited this quiz.' : 'This is the default quiz.' }} Changes apply to every student in the course.</p>
  </div>

  @if (session('status'))
    <p class="mok" role="status" style="margin-top:14px">{{ session('status') }}</p>
  @endif

  <div class="lqe mods" x-data="lessonQuizEditor(@js($questions), @js(route('faculty.lesson-quizzes.update', ['crew' => $crew, 'module' => $module, 'lesson' => $lesson])))"
       x-init="window.addEventListener('beforeunload', e => { if (dirty) { e.preventDefault(); e.returnValue = ''; } })">

    <p class="lqe-help">Write <code>`backticks`</code> around code, like <code>`print(x)`</code>, to show it as code. Students who already answered this quiz keep their results.</p>

    <div class="lqe-bar" data-tour="lqe-bar">
      <span class="grow" :class="{ err: error }" x-text="error || (dirty ? 'You have unsaved changes.' : (saved ? 'Saved. Students now get this quiz.' : questions.length + ' questions'))"></span>
      <button class="mbtn" type="button" @click="add()" :disabled="questions.length >= 50">+ Add question</button>
      <button class="mbtn solid" type="button" @click="save()" :disabled="saving || !dirty" x-text="saving ? 'Saving…' : 'Save quiz'"></button>
    </div>

    <template x-for="(q, i) in questions" :key="q.id">
      <article class="mod lqe-q" style="margin-top:16px" :data-tour="i === 0 ? 'lqe-q' : null">
        <div class="mod-top">
          <span class="tagm" x-text="'Question ' + (i + 1)"></span>
          <div class="lqe-tools">
            <button class="lqe-icon" type="button" title="Move up" aria-label="Move up" @click="move(i, -1)" :disabled="i === 0">↑</button>
            <button class="lqe-icon" type="button" title="Move down" aria-label="Move down" @click="move(i, 1)" :disabled="i === questions.length - 1">↓</button>
            <button class="lqe-icon" type="button" title="Delete question" aria-label="Delete question" @click="remove(i)" :disabled="questions.length === 1">✕</button>
          </div>
        </div>

        <label class="lqe-label" :for="'p' + q.id">Question</label>
        <textarea :id="'p' + q.id" rows="2" maxlength="1000" x-model="q.prompt" @input="dirty = true" placeholder="What do you want to ask?"></textarea>

        <span class="lqe-label">Choices: tick the right answer</span>
        <template x-for="(opt, j) in q.options" :key="j">
          <div class="lqe-opt" :class="{ right: q.correct === letter(j) }">
            <span class="letter" x-text="letter(j)"></span>
            <input type="text" maxlength="500" :value="opt" @input="q.options[j] = $event.target.value; dirty = true" :aria-label="'Choice ' + letter(j)">
            <label class="pick"><input type="radio" :name="'c' + q.id" :checked="q.correct === letter(j)" @change="q.correct = letter(j); dirty = true"> Right answer</label>
            <button class="lqe-icon" type="button" title="Remove choice" aria-label="Remove choice" @click="removeOption(q, j)" :disabled="q.options.length <= 2">✕</button>
          </div>
        </template>
        <button class="mbtn" type="button" style="margin-top:2px" @click="q.options.push(''); dirty = true" x-show="q.options.length < 4">+ Add choice</button>
        <p class="lqe-tip" x-show="longestIsRight(q)">Tip: the right answer is clearly the longest choice, so students may guess it. Try making the choices a similar length.</p>

        <label class="lqe-label" :for="'e' + q.id">Explanation (shown after the student answers)</label>
        <textarea :id="'e' + q.id" rows="2" maxlength="1000" x-model="q.explanation" @input="dirty = true" placeholder="Why is this the right answer?"></textarea>
      </article>
    </template>

    @if ($edited)
      <article class="mod" style="margin-top:28px">
        <h3>Reset to default</h3>
        <p class="mdesc">Throw away your version and go back to the {{ count($default) }} questions this lesson came with.</p>
        <form method="POST" style="margin-top:12px" action="{{ route('faculty.lesson-quizzes.destroy', ['crew' => $crew, 'module' => $module, 'lesson' => $lesson]) }}"
              onsubmit="return confirm('Reset this quiz to the default questions? Your edits will be lost.')">
          @csrf @method('DELETE')
          <button class="mbtn danger" type="submit">Reset to default</button>
        </form>
      </article>
    @endif
  </div>
@endsection

@push('scripts')
  <script>
    function lessonQuizEditor(initial, url) {
      let next = 0;
      const withId = q => ({ id: ++next, prompt: q.prompt || '', options: [...(q.options || ['', '', '', ''])], correct: q.correct || 'A', explanation: q.explanation || '' });

      return {
        questions: initial.map(withId),
        dirty: false, saving: false, saved: false, error: '',

        letter: j => String.fromCharCode(65 + j),

        add() {
          this.questions.push(withId({}));
          this.dirty = true;
          this.$nextTick(() => document.getElementById('p' + next)?.focus());
        },
        remove(i) {
          if (confirm('Delete question ' + (i + 1) + '?')) { this.questions.splice(i, 1); this.dirty = true; }
        },
        move(i, by) {
          const [q] = this.questions.splice(i, 1);
          this.questions.splice(i + by, 0, q);
          this.dirty = true;
        },
        removeOption(q, j) {
          const right = q.correct.charCodeAt(0) - 65;
          q.options.splice(j, 1);
          // Keep the same choice marked right; if that was the one removed, fall back to A.
          q.correct = this.letter(right === j ? 0 : right > j ? right - 1 : right);
          this.dirty = true;
        },
        longestIsRight(q) {
          const lengths = q.options.map(o => o.trim().length);
          const right = lengths[q.correct.charCodeAt(0) - 65] ?? 0;
          const others = lengths.filter((_, j) => this.letter(j) !== q.correct);
          return others.length > 0 && right > 20 && right > 1.5 * Math.max(...others);
        },

        async save() {
          this.error = '';
          const blank = this.questions.findIndex(q => !q.prompt.trim() || q.options.some(o => !o.trim()));
          if (blank !== -1) { this.error = 'Question ' + (blank + 1) + ' has an empty question or choice.'; return; }

          this.saving = true;
          try {
            const res = await fetch(url, {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
              body: JSON.stringify({ questions: this.questions.map(({ prompt, options, correct, explanation }) => ({ prompt, options, correct, explanation })) }),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) { this.error = data.message || 'Could not save. Try again.'; return; }
            this.dirty = false;
            this.saved = true;
          } catch (e) {
            this.error = 'Could not reach the server. Check your connection and try again.';
          } finally {
            this.saving = false;
          }
        },
      };
    }
  </script>
@endpush
