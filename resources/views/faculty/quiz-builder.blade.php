@extends('layouts.faculty-shell')

@section('title', $isFinal ? 'Final exam builder' : 'Quiz builder')

@push('head')
<style>
  /* The shell caps pages at 860px; two panels side by side need the room. */
  .wrap:has(.qb-wrap) { max-width: 1320px; }
  .qb-wrap { display: grid; grid-template-columns: minmax(0, 1fr); gap: 20px; padding: 24px; }
  @media (min-width: 1100px) { .qb-wrap { grid-template-columns: minmax(320px, 420px) minmax(0, 1fr); align-items: start; } }
  .qb-panel { border: 1px solid var(--glass-border, rgba(255,255,255,.1)); border-radius: 18px; background: rgba(255,255,255,.03); padding: 18px; }
  @media (min-width: 1100px) { .qb-panel.left { position: sticky; top: 18px; max-height: calc(100vh - 36px); overflow-y: auto; } }
  .qb-panel.right { display: flex; flex-direction: column; max-height: 82vh; }
  @media (min-width: 1100px) { .qb-panel.right { position: sticky; top: 18px; max-height: calc(100vh - 36px); } }
  .qb-panel-head { flex: none; }
  .qb-panel-scroll { flex: 1; min-height: 0; overflow-y: auto; margin-top: 14px; padding-right: 6px; }
  .qb-panel-foot { flex: none; border-top: 1px solid rgba(255,255,255,.08); margin-top: 14px; padding-top: 14px; }
  .qb-mode-toggle { display: flex; gap: 8px; margin-top: 14px; }
  .qb-mode-btn { flex: 1; padding: 9px 12px; border-radius: 999px; border: 1px solid rgba(255,255,255,.16); background: rgba(255,255,255,.04); color: inherit; font-size: .82rem; font-weight: 600; font-family: inherit; cursor: pointer; text-align: center; }
  .qb-mode-btn:hover { background: rgba(255,255,255,.09); }
  .qb-mode-btn.active { background: #73b6ff; border-color: #73b6ff; color: #06122a; }
  .qb-h { font-size: 1.05rem; font-weight: 600; margin-bottom: 4px; }
  .qb-sub { font-size: .82rem; color: var(--muted, #9aa3c7); line-height: 1.5; }
  .qb-file { display: flex; align-items: center; gap: 10px; width: 100%; padding: 8px 10px; border-radius: 10px; border: 1px solid transparent; background: none; color: inherit; cursor: pointer; text-align: left; }
  .qb-file:hover { background: rgba(255,255,255,.05); }
  .qb-file[aria-pressed="true"] { border-color: rgba(115,182,255,.5); background: rgba(115,182,255,.1); }
  .qb-ext { flex: none; display: grid; place-items: center; width: 34px; height: 26px; border-radius: 7px; background: rgba(115,182,255,.14); color: #73b6ff; font: 700 10px/1 ui-monospace, monospace; text-transform: uppercase; }
  .qb-name { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .88rem; }
  .qb-flag { flex: none; font-size: .7rem; color: var(--muted, #9aa3c7); }
  .qb-viewer { width: 100%; height: 520px; border: 1px solid rgba(255,255,255,.1); border-radius: 12px; background: #fff; margin-top: 12px; }
  .qb-card { border: 1px solid rgba(255,255,255,.1); border-left: 4px solid #73b6ff; border-radius: 14px; background: rgba(255,255,255,.03); padding: 16px; margin-top: 14px; }
  .qb-row { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
  .qb-in, .qb-sel, .qb-area { width: 100%; padding: 9px 11px; border-radius: 10px; border: 1px solid rgba(255,255,255,.14); background: rgba(0,0,0,.2); color: inherit; font-size: .92rem; font-family: inherit; }
  .qb-in:focus, .qb-sel:focus, .qb-area:focus { outline: 2px solid #73b6ff; outline-offset: 1px; }
  .qb-opt { display: flex; align-items: center; gap: 10px; margin-top: 8px; }
  .qb-btn { padding: 8px 14px; border-radius: 10px; border: 1px solid rgba(255,255,255,.16); background: rgba(255,255,255,.04); color: inherit; font-size: .85rem; font-weight: 600; cursor: pointer; }
  .qb-btn:hover { background: rgba(255,255,255,.09); }
  .qb-btn.solid { background: #73b6ff; border-color: #73b6ff; color: #06122a; }
  .qb-btn.danger { color: #ff6b81; border-color: rgba(255,107,129,.35); }
  .qb-btn:disabled { opacity: .55; cursor: not-allowed; }
  .qb-note { font-size: .8rem; color: var(--muted, #9aa3c7); }
  .qb-err { margin-top: 10px; padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(255,107,129,.35); background: rgba(255,107,129,.1); color: #ff6b81; font-size: .85rem; }
  .qb-ok { color: #8bd83a; }
  .qb-bar { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between; margin-top: 18px; }
  .qb-final { margin-top: 12px; padding: 12px 14px; border-radius: 12px; border: 1px solid rgba(255,196,0,.35); background: rgba(255,196,0,.08); font-size: .84rem; line-height: 1.5; }
  .qb-final b { color: #ffc400; }
</style>
@endpush

@section('content')
<div x-data="quizBuilder()" x-init="init()">
  <div class="qb-wrap">

    {{-- ═══════════ LEFT: YOUR MATERIAL ═══════════ --}}
    <aside class="qb-panel left" aria-label="Course materials">
      <p class="qb-h">Your material</p>
      <p class="qb-sub">Open a file to read it while you write. Tick the ones Astro should draft questions from.</p>

      @forelse ($modules as $m)
        <section style="margin-top:16px">
          <p style="font-size:.78rem;text-transform:uppercase;letter-spacing:.12em;color:var(--muted,#9aa3c7)">{{ $m['title'] }}</p>
          @forelse ($m['materials'] as $f)
            <div class="qb-row" style="gap:6px;margin-top:4px">
              <button type="button" class="qb-file" :aria-pressed="picked.includes({{ $f['id'] }}) ? 'true' : 'false'"
                      @click="preview({{ Js::from($f) }})">
                <span class="qb-ext">{{ $f['ext'] }}</span>
                <span class="qb-name">{{ $f['name'] }}</span>
                @unless ($f['readable'])
                  <span class="qb-flag" title="Astro can't read the text inside this one">Not readable</span>
                @endunless
              </button>
              @if ($f['readable'])
                <input type="checkbox" value="{{ $f['id'] }}" x-model.number="picked"
                       aria-label="Use {{ $f['name'] }} for the draft" style="accent-color:#73b6ff">
              @endif
            </div>
          @empty
            <p class="qb-sub" style="margin-top:6px">No files in this module.</p>
          @endforelse
        </section>
      @empty
        <p class="qb-sub" style="margin-top:16px">
          You haven't uploaded anything yet.
          <a href="{{ route('faculty.course', $crew) }}#modules" style="color:#73b6ff">Add materials first</a>
          if you want Astro to draft from them.
        </p>
      @endforelse

      {{-- The preview pane: a PDF renders inline, an image shows, a link opens out. --}}
      <template x-if="viewing">
        <div>
          <p class="qb-sub" style="margin-top:16px" x-text="viewing.name"></p>
          <template x-if="viewing.is_link">
            <p class="qb-sub" style="margin-top:8px">
              <a :href="viewing.url" target="_blank" rel="noopener noreferrer" style="color:#73b6ff">Open this link in a new tab →</a>
            </p>
          </template>
          <template x-if="!viewing.is_link && viewing.ext === 'img'">
            <img :src="viewing.url" :alt="viewing.name" class="qb-viewer" style="object-fit:contain;background:#0b0b1e">
          </template>
          <template x-if="!viewing.is_link && viewing.ext === 'pdf'">
            <iframe :src="viewing.url" class="qb-viewer" :title="viewing.name"></iframe>
          </template>
          <template x-if="!viewing.is_link && viewing.ext !== 'img' && viewing.ext !== 'pdf'">
            <p class="qb-sub" style="margin-top:8px">
              This type can't be shown here.
              <a :href="viewing.url" style="color:#73b6ff">Download it</a> to read it.
            </p>
          </template>
        </div>
      </template>
    </aside>

    {{-- ═══════════ RIGHT: THE QUESTIONS ═══════════ --}}
    <div class="qb-panel right">
      <div class="qb-panel-head">
        @if ($isFinal)
          <p class="qb-h">{{ $quiz ? 'Edit final exam' : 'New final exam' }}</p>
          <div class="qb-final">
            <b>The Final Boss.</b> Students unlock it after finishing every lesson, take it once in full screen,
            and earn their certificate only if they pass. Leaving full screen or switching tabs ends it as a fail, and you get an email.
          </div>
          @if ($locked)
            <p class="qb-err">A student has already taken this exam, so it can't be changed any more. To redo someone's exam, use "Allow retake" on the course page.</p>
          @endif
        @else
          <p class="qb-h">{{ $quiz ? 'Edit quiz' : 'New quiz' }}</p>
          <p class="qb-sub">Students only see this once you press Publish.</p>
        @endif

        <div class="qb-mode-toggle" role="tablist" aria-label="How do you want to build this quiz?">
          <button type="button" class="qb-mode-btn" :class="{ active: !showAstro }" @click="showAstro = false"
                  role="tab" :aria-selected="(!showAstro).toString()">{{ $quiz ? 'Edit manually' : 'Create manually' }}</button>
          <button type="button" class="qb-mode-btn" :class="{ active: showAstro }" @click="showAstro = true"
                  role="tab" :aria-selected="showAstro.toString()">{{ $quiz ? 'Ask Astro' : 'Create with Astro' }}</button>
        </div>
      </div>

      <div class="qb-panel-scroll">
        <div style="display:flex;flex-direction:column;gap:10px">
          <input class="qb-in" type="text" maxlength="255" placeholder="{{ $isFinal ? 'Exam title (e.g. Final Exam)' : 'Quiz title' }}" aria-label="Title" x-model="title">
          <textarea class="qb-area" rows="2" maxlength="2000" placeholder="Instructions for your students (optional)" aria-label="Quiz description" x-model="description"></textarea>
          <div class="qb-row">
            <label class="qb-note" style="flex:1;min-width:150px">Time limit (minutes)
              <input class="qb-in" type="number" min="1" max="600" placeholder="No limit" x-model.number="minutes">
            </label>
            <label class="qb-note" style="flex:1;min-width:190px">Deadline — it locks after this
              <input class="qb-in" type="datetime-local" x-model="dueAt">
            </label>
            @if ($isFinal)
              <label class="qb-note" style="flex:1;min-width:150px">Pass mark (%)
                <input class="qb-in" type="number" min="1" max="100" x-model.number="passPercent">
              </label>
            @endif
          </div>
        </div>

        {{-- Ask Astro --}}
        <section x-show="showAstro" x-cloak style="margin-top:20px;border-top:1px solid rgba(255,255,255,.08);padding-top:16px">
          <p class="qb-h" style="font-size:.95rem">Let Astro draft it</p>
          <p class="qb-sub">Pick materials on the left, say what to focus on, and check what comes back — Astro can get a fact wrong.</p>
          <div class="qb-row" style="margin-top:10px">
            <input class="qb-in" style="flex:1;min-width:220px;width:auto" type="text" maxlength="500"
                   placeholder="What should it focus on? (optional)" aria-label="What Astro should focus on" x-model="aiPrompt">
            <label class="qb-note">Questions
              <input class="qb-in" style="width:80px" type="number" min="1" max="20" x-model.number="aiCount">
            </label>
            <button type="button" class="qb-btn solid" @click="askAstro()" :disabled="drafting || !picked.length"
                    x-text="drafting ? 'Drafting…' : 'Ask Astro'"></button>
          </div>
          <p class="qb-sub" x-show="!picked.length" style="margin-top:6px">Tick at least one readable material on the left first.</p>
        </section>

        <p class="qb-err" x-show="error" x-cloak x-text="error"></p>

        {{-- The questions --}}
        <template x-for="(q, qi) in questions" :key="qi">
        <article class="qb-card">
          <div class="qb-row" style="align-items:flex-start">
            <textarea class="qb-area" style="flex:1;min-width:200px;width:auto" rows="2" maxlength="1000"
                      :placeholder="'Question ' + (qi + 1)" :aria-label="'Question ' + (qi + 1)" x-model="q.prompt"></textarea>
            <select class="qb-sel" style="width:auto;min-width:165px" :aria-label="'Answer type for question ' + (qi + 1)"
                    x-model="q.type" @change="onTypeChange(q)">
              <option value="multiple_choice">Multiple choice</option>
              <option value="true_false">True / False</option>
              <option value="checkboxes">Checkboxes</option>
              <option value="dropdown">Drop-down</option>
            </select>
          </div>

          <div style="margin-top:10px">
            <template x-for="(opt, oi) in q.options" :key="oi">
              <div class="qb-opt">
                <template x-if="q.type === 'checkboxes'">
                  <input type="checkbox" :value="oi" x-model.number="q.correct_many" style="accent-color:#8bd83a"
                         :aria-label="'Option ' + (oi + 1) + ' is a correct answer'">
                </template>
                <template x-if="q.type !== 'checkboxes'">
                  <input type="radio" :name="'correct' + qi" :value="oi" x-model.number="q.correct" style="accent-color:#8bd83a"
                         :aria-label="'Option ' + (oi + 1) + ' is the correct answer'">
                </template>
                <input class="qb-in" style="flex:1;width:auto" type="text" maxlength="500"
                       :placeholder="'Option ' + (oi + 1)" :aria-label="'Option ' + (oi + 1) + ' for question ' + (qi + 1)"
                       x-model="q.options[oi]" :disabled="q.type === 'true_false'">
                <button type="button" class="qb-btn danger" style="padding:6px 10px"
                        x-show="q.type !== 'true_false' && q.options.length > 2"
                        @click="removeOption(q, oi)" :aria-label="'Remove option ' + (oi + 1)">×</button>
              </div>
            </template>
          </div>

          <div class="qb-bar">
            <button type="button" class="qb-btn" x-show="q.type !== 'true_false' && q.options.length < 10" @click="q.options.push('')">Add option</button>
            <div class="qb-row">
              <label class="qb-note"><input type="checkbox" x-model="q.required" style="accent-color:#73b6ff"> Required</label>
              <button type="button" class="qb-btn" @click="duplicate(qi)">Duplicate</button>
              <button type="button" class="qb-btn" @click="move(qi, -1)" :disabled="qi === 0" aria-label="Move up">↑</button>
              <button type="button" class="qb-btn" @click="move(qi, 1)" :disabled="qi === questions.length - 1" aria-label="Move down">↓</button>
              <button type="button" class="qb-btn danger" @click="questions.splice(qi, 1)">Delete</button>
            </div>
          </div>
        </article>
      </template>

        <div class="qb-bar">
          <button type="button" class="qb-btn" @click="addQuestion()">Add question</button>
        </div>
      </div>

      <div class="qb-panel-foot">
        <div class="qb-bar" style="margin-top:0">
          <span class="qb-note" x-show="savedAt" x-cloak :class="{ 'qb-ok': true }" x-text="savedAt"></span>
          <div class="qb-row">
            <button type="button" class="qb-btn" @click="save(true)" :disabled="saving || locked">Save draft</button>
            <button type="button" class="qb-btn solid" @click="save(false)" :disabled="saving || locked">Publish</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function quizBuilder() {
    const existing = @json($quiz);

    return {
      title: existing?.title || (@js($isFinal) ? 'Final Exam' : ''),
      isFinal: @js($isFinal), locked: @js($locked),
      passPercent: existing?.pass_percent || {{ \App\Models\CrewQuiz::DEFAULT_PASS_PERCENT }},
      saveUrl: @js($saveUrl), saveMethod: @js($saveMethod),
      description: existing?.description || '',
      minutes: existing?.minutes || null,
      dueAt: existing?.due_at || '',
      questions: existing?.questions || [],
      picked: [], viewing: null,
      aiPrompt: '', aiCount: 5, drafting: false,
      showAstro: false,
      saving: false, savedAt: '', error: '',

      init() {
        if (!this.questions.length) this.addQuestion();
        this.showAstro = @js(request('mode') === 'astro');
      },

      blank() {
        return { prompt: '', type: 'multiple_choice', options: ['', ''], correct: 0, correct_many: [], required: true };
      },
      addQuestion() { this.questions.push(this.blank()); },
      duplicate(i) { this.questions.splice(i + 1, 0, JSON.parse(JSON.stringify(this.questions[i]))); },
      move(i, by) {
        const to = i + by;
        if (to < 0 || to >= this.questions.length) return;
        const [q] = this.questions.splice(i, 1);
        this.questions.splice(to, 0, q);
      },
      removeOption(q, oi) {
        q.options.splice(oi, 1);
        if (q.correct >= q.options.length) q.correct = 0;
        q.correct_many = q.correct_many.filter(i => i < q.options.length);
      },
      /* True/False always has exactly those two options; the others keep what's typed. */
      onTypeChange(q) {
        if (q.type === 'true_false') { q.options = ['True', 'False']; if (q.correct > 1) q.correct = 0; }
        else if (q.options.length < 2) q.options = ['', ''];
      },

      preview(file) { this.viewing = file; },

      async askAstro() {
        if (this.drafting || !this.picked.length) return;
        this.drafting = true; this.error = '';
        try {
          const res = await fetch(@js(route('faculty.quizzes.draft', $crew)), {
            method: 'POST', headers: this.headers(),
            body: JSON.stringify({ material_ids: this.picked, count: this.aiCount, prompt: this.aiPrompt }),
          });
          const body = await res.json().catch(() => ({}));
          if (!res.ok) { this.error = body.message || 'Astro could not draft that. Try again.'; return; }
          if (!this.title && body.title) this.title = body.title;
          // Drop an empty starter question so the draft isn't buried under a blank.
          const onlyBlank = this.questions.length === 1 && !this.questions[0].prompt.trim();
          this.questions = (onlyBlank ? [] : this.questions).concat(body.questions || []);
          if (!body.questions?.length) this.error = 'Astro found nothing to ask about in that material.';
        } catch (e) { this.error = 'Could not reach Astro. Check your connection and try again.'; }
        finally { this.drafting = false; }
      },

      async save(draft) {
        if (this.saving) return;
        this.saving = true; this.error = ''; this.savedAt = '';
        try {
          const res = await fetch(this.saveUrl, {
            method: this.saveMethod, headers: this.headers(),
            body: JSON.stringify({
              title: this.title, description: this.description,
              minutes: this.minutes || null, due_at: this.dueAt || null, draft,
              is_final: this.isFinal, pass_percent: this.isFinal ? (this.passPercent || null) : null,
              questions: this.questions.map(q => ({
                prompt: q.prompt, type: q.type, options: q.options,
                correct: q.correct, correct_many: q.correct_many, required: q.required,
              })),
            }),
          });
          const body = await res.json().catch(() => ({}));
          if (!res.ok) {
            this.error = body.message || Object.values(body.errors || {}).flat()[0] || 'That did not save.';
            return;
          }
          // The first save created it; every save after that updates the same one instead of making another.
          if (this.saveMethod === 'POST' && body.id) {
            this.saveUrl = @js(route('faculty.quizzes.update', '__ID__')).replace('__ID__', body.id);
            this.saveMethod = 'PUT';
          }
          this.savedAt = body.published ? 'Published — your students can see it now.' : 'Draft saved.';
          if (!draft) setTimeout(() => { window.location = @js(route('faculty.course', $crew)) + (this.isFinal ? '#final-exam' : '#quizzes'); }, 700);
        } catch (e) { this.error = 'Could not save. Check your connection and try again.'; }
        finally { this.saving = false; }
      },

      headers() {
        return {
          'Content-Type': 'application/json', 'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        };
      },
    };
  }
</script>
@endpush
