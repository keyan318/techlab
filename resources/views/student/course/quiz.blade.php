<x-pixel-app :title="$title.' — Quiz'">
  @include('student.course.partials.hero')

  <div class="mx-auto grid max-w-[1200px] items-start gap-10 px-4 py-10 sm:px-8 xl:grid-cols-[minmax(0,1fr)_340px]"
       x-data="crewQuiz()">
    <section class="px-card min-w-0 p-5 sm:p-8" aria-labelledby="quiz-h">
      <header class="flex flex-wrap items-baseline justify-between gap-2">
        <h2 id="quiz-h" class="px-font text-[clamp(1.4rem,3vw,2rem)] font-medium leading-tight">Quizzes</h2>
        <p class="text-[15px] text-muted">Set by {{ $crew?->faculty?->name ?? 'your captain' }}</p>
      </header>

      @forelse ($quizzes as $q)
        <article class="px-inner mt-6 flex flex-wrap items-center gap-x-5 gap-y-3 rounded-sm p-4 sm:p-6" aria-labelledby="quiz-{{ $q['id'] }}">
          <span class="px-font text-[15px] text-muted">{{ $q['number'] }}</span>

          <div class="min-w-0 flex-1">
            <h3 id="quiz-{{ $q['id'] }}" class="flex flex-wrap items-center gap-2 text-[1.15rem] font-semibold">
              {{ $q['title'] }}
              @if ($q['is_new'])
                <span class="rounded-full bg-[#e5484d] px-2 py-0.5 text-[11px] font-bold uppercase tracking-[0.1em] text-white">New</span>
              @endif
            </h3>

            @if ($q['description'])
              <p class="mt-1 text-[14px] leading-relaxed text-muted">{{ $q['description'] }}</p>
            @endif

            <p class="mt-1 text-[14px] text-muted">
              {{ $q['questions'] }} {{ \Illuminate\Support\Str::plural('question', $q['questions']) }}
              @if ($q['minutes']) · {{ $q['minutes'] }} min @endif
              @if ($q['status'] === 'taking') · <span class="text-[#ffc400]">Started, the clock is running</span> @endif
              @if ($q['due_at'])
                ·
                <span class="{{ $q['status'] === 'missed' ? 'text-[#ff6b81]' : '' }}">
                  {{ $q['status'] === 'done' ? 'Was due' : ($q['status'] === 'missed' ? 'Closed' : 'Due') }}
                  {{ $q['due_at']->format('j M, g:ia') }}
                </span>
              @endif
            </p>
          </div>

          @if ($q['status'] === 'taking')
            <button type="button" class="px-btn px-btn-blue min-w-[128px]" @click="open({{ $q['id'] }})">Continue</button>
          @elseif ($q['status'] === 'done')
            <span class="rounded-full px-3 py-1 text-[13px] font-semibold px-good">Done — {{ $q['score'] }}/{{ $q['total'] }} ({{ $q['percent'] }}%)</span>
          @elseif ($q['status'] === 'missed')
            <span class="px-btn px-btn-locked min-w-[128px]"><x-px-icon name="lock" :size="16" /> Missed</span>
          @else
            <button type="button" class="px-btn px-btn-blue min-w-[128px]" @click="open({{ $q['id'] }})">Take quiz</button>
          @endif
        </article>
      @empty
        <p class="mt-4 text-[15px] leading-relaxed text-muted">No quizzes yet. When your captain sets one it appears here, and the Quiz tab shows a red dot so you don't miss it.</p>
      @endforelse
    </section>

    @include('student.course.partials.rail')

    {{-- ═══════════ THE QUIZ ITSELF ═══════════ --}}
    <div x-show="quiz" x-cloak @keydown.escape.window="close()"
         class="fixed inset-0 z-50 grid place-items-start overflow-y-auto bg-[rgba(4,4,18,0.82)] p-4 backdrop-blur-sm sm:p-8"
         role="dialog" aria-modal="true" :aria-label="quiz?.title">
      <div class="px-card mx-auto w-full max-w-[760px] p-5 sm:p-8">
        <header class="flex flex-wrap items-start justify-between gap-4">
          <div class="min-w-0">
            <h2 class="px-font text-[1.6rem] font-medium leading-tight" x-text="quiz?.title"></h2>
            <p class="mt-1 text-[14px] text-muted" x-show="quiz?.description" x-text="quiz?.description"></p>
          </div>
          <button type="button" class="px-btn" @click="close()">Close</button>
        </header>

        {{-- The clock: sticks to the top while answering, red in the last minute. --}}
        <div x-show="phase === 'taking' && left !== null" x-cloak
             class="sticky top-0 z-10 -mx-1 mt-4 flex items-center justify-between gap-3 rounded-[10px] px-4 py-3 text-[15px] font-semibold backdrop-blur"
             :class="left <= 60 ? 'bg-[rgba(229,72,77,0.22)] text-[#ff6b81]' : 'bg-[rgba(115,182,255,0.14)]'"
             role="timer" aria-live="off">
          <span>Time left <span class="px-font text-[1.15rem]" x-text="clock()"></span></span>
          <span class="text-[13px] font-normal text-muted" x-text="saveState"></span>
        </div>

        <p x-show="error" x-cloak class="mt-4 rounded-[10px] border border-[rgba(255,107,129,0.35)] bg-[rgba(255,107,129,0.1)] px-4 py-3 text-[14px] text-[#ff6b81]" x-text="error"></p>

        {{-- Before starting: the rules, then Start --}}
        <div x-show="phase === 'intro'" x-cloak class="mt-6">
          <ul class="flex flex-col gap-2 text-[15px] leading-relaxed">
            <li>📝 <span x-text="quiz?.question_count"></span> questions<span x-show="quiz?.minutes"> · <b x-text="quiz?.minutes + ' minutes'"></b></span></li>
            <li>🎯 <b>One try only.</b> You can't retake it.</li>
            <li x-show="quiz?.minutes">⏱️ The clock starts when you press Start and <b>keeps running even if you close this page</b>. When it hits zero, your answers are handed in.</li>
            <li>💾 Your answers are saved as you go.</li>
            <li>✅ You see your score and every mistake as soon as you finish.</li>
          </ul>
          <button type="button" class="px-btn px-btn-blue mt-6 min-w-[160px]" @click="start()" :disabled="busy"
                  x-text="busy ? 'Starting…' : (quiz?.started ? 'Continue' : 'Start quiz')"></button>
        </div>

        {{-- Answering --}}
        <form x-show="phase === 'taking'" x-cloak @submit.prevent="submit()" class="mt-6 flex flex-col gap-6">
          <template x-for="(q, qi) in questions" :key="q.id">
            <fieldset class="px-inner rounded-sm p-4 sm:p-6">
              <legend class="sr-only" x-text="'Question ' + (qi + 1)"></legend>
              <p class="text-[1.05rem] font-semibold"><span class="text-muted" x-text="(qi + 1) + '. '"></span><span x-text="q.prompt"></span></p>

              {{-- Drop-down --}}
              <template x-if="q.type === 'dropdown'">
                <select class="mt-4 w-full rounded-[10px] border border-glassBorder bg-glass px-3 py-2.5 text-[15px]"
                        x-model.number="answers[q.id]" @change="changed()" :aria-label="q.prompt">
                  <option :value="null">Choose an answer…</option>
                  <template x-for="(opt, oi) in q.options" :key="oi">
                    <option :value="oi" x-text="opt" :selected="answers[q.id] === oi"></option>
                  </template>
                </select>
              </template>

              {{-- Checkboxes: several answers may be right --}}
              <template x-if="q.type === 'checkboxes'">
                <div class="mt-4 flex flex-col gap-2">
                  <p class="text-[13px] text-muted">Tick every answer that applies.</p>
                  <template x-for="(opt, oi) in q.options" :key="oi">
                    <label class="flex cursor-pointer items-center gap-3 rounded-[10px] px-3 py-2 transition hover:bg-glass">
                      <input type="checkbox" class="accent-[#73b6ff]" :value="oi" x-model.number="answers[q.id]" @change="changed()">
                      <span class="text-[15px]" x-text="opt"></span>
                    </label>
                  </template>
                </div>
              </template>

              {{-- Multiple choice and true/false both pick exactly one --}}
              <template x-if="q.type !== 'dropdown' && q.type !== 'checkboxes'">
                <div class="mt-4 flex flex-col gap-2">
                  <template x-for="(opt, oi) in q.options" :key="oi">
                    <label class="flex cursor-pointer items-center gap-3 rounded-[10px] px-3 py-2 transition hover:bg-glass">
                      <input type="radio" class="accent-[#73b6ff]" :name="'q' + q.id" :value="oi" x-model.number="answers[q.id]" @change="changed()">
                      <span class="text-[15px]" x-text="opt"></span>
                    </label>
                  </template>
                </div>
              </template>
            </fieldset>
          </template>

          <div class="flex flex-wrap items-center gap-4">
            <button type="submit" class="px-btn px-btn-blue" :disabled="busy" x-text="busy ? 'Handing in…' : 'Hand it in'"></button>
            <p class="text-[14px] text-muted">One try only. You can't change your answers after this.</p>
          </div>
        </form>

        {{-- Result: the score, then every question with what they picked and the right answer --}}
        <div x-show="phase === 'result'" x-cloak class="mt-6">
          <p class="text-[14px] text-muted" x-show="timedOut">Time ran out, so your saved answers were handed in.</p>
          <p class="px-font text-[2rem] font-medium">
            <span x-text="result?.score"></span> / <span x-text="result?.total"></span>
            <span class="text-muted text-[1.2rem]">(<span x-text="result?.percent"></span>%)</span>
          </p>
          <p class="mt-1 text-[14px] text-muted" x-show="mistakes() > 0"><span x-text="mistakes()"></span> to look over below.</p>

          <ul class="mt-6 flex flex-col gap-3">
            <template x-for="(r, ri) in (result?.review || [])" :key="r.id">
              <li class="px-inner rounded-sm p-4" :style="r.right ? '' : 'border-left: 4px solid #ff6b81'">
                <p class="text-[15px] font-medium">
                  <span class="text-muted" x-text="(ri + 1) + '. '"></span>
                  <span x-text="r.prompt"></span>
                </p>
                <p class="mt-2 text-[14px]" :class="r.right ? 'text-[#8bd83a]' : 'text-[#ff6b81]'">
                  <span x-text="r.right ? '✓ Correct' : '✗ Your answer: ' + (pickedText(r) || 'no answer')"></span>
                </p>
                <p class="mt-1 text-[14px]" x-show="!r.right">
                  The right answer: <span class="font-medium" x-text="answerText(r)"></span>
                </p>
              </li>
            </template>
          </ul>

          <button type="button" class="px-btn px-btn-blue mt-6" @click="reload()">Done</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function crewQuiz() {
      const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
      const json = (method, body) => ({
        method, body: JSON.stringify(body),
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf() },
      });

      return {
        quiz: null, phase: null, questions: [], answers: {}, result: null,
        busy: false, error: '', left: null, endsAt: null, tick: null, saveTimer: null, saveState: '', timedOut: false,

        async open(id) {
          this.error = ''; this.result = null; this.phase = null; this.timedOut = false;
          try {
            const res = await fetch(`/student/quizzes/${id}`, { headers: { 'Accept': 'application/json' } });
            const body = await res.json().catch(() => ({}));
            if (!res.ok) { this.quiz = { title: 'Quiz' }; this.error = body.message || 'This quiz is not available.'; return; }
            this.quiz = body;
            if (body.result) { this.result = body.result; this.phase = 'result'; }
            else this.phase = 'intro';
          } catch (e) { this.quiz = { title: 'Quiz' }; this.error = 'Could not open the quiz. Check your connection and try again.'; }
        },

        /* Start (or pick back up): the server opens the attempt and starts the clock. */
        async start() {
          if (this.busy) return;
          this.busy = true; this.error = '';
          try {
            const res = await fetch(`/student/quizzes/${this.quiz.id}/start`, json('POST', {}));
            const body = await res.json().catch(() => ({}));
            if (!res.ok) { this.error = body.message || 'This quiz could not start.'; return; }
            if (body.result) { this.showResult(body.result, true); return; }
            this.questions = body.questions || [];
            // Checkboxes collect a list; every other type holds one index. Saved answers come back as they were.
            const saved = body.answers || {};
            this.answers = {};
            this.questions.forEach(q => {
              const v = saved[q.id];
              this.answers[q.id] = q.type === 'checkboxes' ? (Array.isArray(v) ? v.map(Number) : []) : (v === null || v === undefined ? null : Number(v));
            });
            this.phase = 'taking';
            this.startClock(body.seconds_left);
          } catch (e) { this.error = 'Could not start. Check your connection and try again.'; }
          finally { this.busy = false; }
        },

        startClock(seconds) {
          clearInterval(this.tick);
          if (seconds === null || seconds === undefined) { this.left = null; return; }
          this.endsAt = Date.now() + seconds * 1000;
          const step = () => {
            this.left = Math.max(0, Math.round((this.endsAt - Date.now()) / 1000));
            if (this.left <= 0) { clearInterval(this.tick); this.submit(true); }
          };
          step();
          this.tick = setInterval(step, 1000);
        },
        clock() {
          const s = this.left ?? 0;
          return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
        },

        /* Save as they go (debounced), so closing the page loses nothing. */
        changed() {
          clearTimeout(this.saveTimer);
          this.saveState = 'Saving…';
          this.saveTimer = setTimeout(() => this.saveAnswers(), 700);
        },
        async saveAnswers() {
          if (this.phase !== 'taking') return;
          try {
            const res = await fetch(`/student/quizzes/${this.quiz.id}/answers`, json('PUT', { answers: this.answers }));
            const body = await res.json().catch(() => ({}));
            if (res.status === 409 && body.result) { this.showResult(body.result, true); return; }
            this.saveState = res.ok ? 'Saved' : 'Not saved — check your connection';
          } catch (e) { this.saveState = 'Not saved — check your connection'; }
        },

        async submit(auto = false) {
          if (this.busy || this.phase !== 'taking') return;
          this.busy = true; this.error = '';
          clearTimeout(this.saveTimer);
          try {
            const res = await fetch(`/student/quizzes/${this.quiz.id}/submit`, json('POST', { answers: this.answers }));
            const body = await res.json().catch(() => ({}));
            if (!res.ok) { this.error = body.message || 'That did not go through. Try again.'; return; }
            this.showResult(body.result, auto);
          } catch (e) { this.error = 'Could not hand it in. Check your connection and try again — your answers are saved.'; }
          finally { this.busy = false; }
        },

        showResult(result, timedOut) {
          clearInterval(this.tick); clearTimeout(this.saveTimer);
          this.result = result; this.phase = 'result'; this.timedOut = !!timedOut && this.left === 0;
        },

        close() {
          if (!this.quiz) return;
          if (this.phase === 'taking' && !confirm('The clock keeps running while this is closed. Close it anyway?')) return;
          if (this.phase === 'taking') this.saveAnswers();
          clearInterval(this.tick);
          const finished = this.phase === 'result';
          this.quiz = null; this.phase = null; this.error = '';
          if (finished) this.reload();
        },
        reload() { window.location.reload(); },

        mistakes() { return (this.result?.review || []).filter(r => !r.right).length; },
        textFor(r, value) {
          const idx = Array.isArray(value) ? value : (value === null || value === undefined ? [] : [value]);
          return idx.map(i => r.options?.[i]).filter(Boolean).join(', ');
        },
        pickedText(r) { return this.textFor(r, r.chosen); },
        answerText(r) { return this.textFor(r, r.correct); },
      };
    }
  </script>
</x-pixel-app>
