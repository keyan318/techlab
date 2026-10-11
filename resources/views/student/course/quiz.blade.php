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
              @if ($q['due_at'])
                ·
                <span class="{{ $q['status'] === 'missed' ? 'text-[#ff6b81]' : '' }}">
                  {{ $q['status'] === 'done' ? 'Was due' : ($q['status'] === 'missed' ? 'Closed' : 'Due') }}
                  {{ $q['due_at']->format('j M, g:ia') }}
                </span>
              @endif
            </p>
          </div>

          @if ($q['status'] === 'done')
            <span class="rounded-full px-3 py-1 text-[13px] font-semibold px-good">Done — {{ $q['score'] }}/{{ $q['total'] }} ({{ $q['percent'] }}%)</span>
          @elseif ($q['status'] === 'missed')
            <span class="px-btn px-btn-locked min-w-[128px]"><x-px-icon name="lock" :size="16" /> Missed</span>
          @else
            <button type="button" class="px-btn px-btn-yellow min-w-[128px]" @click="open({{ $q['id'] }})">Take quiz</button>
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
      <div class="px-card mx-auto w-full max-w-[760px] p-5 sm:p-8" @click.outside="close()">
        <header class="flex flex-wrap items-start justify-between gap-4">
          <div class="min-w-0">
            <h2 class="px-font text-[1.6rem] font-medium leading-tight" x-text="quiz?.title"></h2>
            <p class="mt-1 text-[14px] text-muted" x-show="quiz?.description" x-text="quiz?.description"></p>
          </div>
          <button type="button" class="px-btn" @click="close()">Close</button>
        </header>

        <p x-show="error" x-cloak class="mt-4 rounded-[10px] border border-[rgba(255,107,129,0.35)] bg-[rgba(255,107,129,0.1)] px-4 py-3 text-[14px] text-[#ff6b81]" x-text="error"></p>

        {{-- Answering --}}
        <form x-show="quiz && !result" x-cloak @submit.prevent="submit()" class="mt-6 flex flex-col gap-6">
          <template x-for="(q, qi) in (quiz?.questions || [])" :key="q.id">
            <fieldset class="px-inner rounded-sm p-4 sm:p-6">
              <legend class="sr-only" x-text="'Question ' + (qi + 1)"></legend>
              <p class="text-[1.05rem] font-semibold"><span class="text-muted" x-text="(qi + 1) + '. '"></span><span x-text="q.prompt"></span></p>

              {{-- Drop-down --}}
              <template x-if="q.type === 'dropdown'">
                <select class="mt-4 w-full rounded-[10px] border border-glassBorder bg-glass px-3 py-2.5 text-[15px]"
                        x-model.number="answers[q.id]" :aria-label="q.prompt">
                  <option :value="null">Choose an answer…</option>
                  <template x-for="(opt, oi) in q.options" :key="oi">
                    <option :value="oi" x-text="opt"></option>
                  </template>
                </select>
              </template>

              {{-- Checkboxes: several answers may be right --}}
              <template x-if="q.type === 'checkboxes'">
                <div class="mt-4 flex flex-col gap-2">
                  <p class="text-[13px] text-muted">Tick every answer that applies.</p>
                  <template x-for="(opt, oi) in q.options" :key="oi">
                    <label class="flex cursor-pointer items-center gap-3 rounded-[10px] px-3 py-2 transition hover:bg-glass">
                      <input type="checkbox" class="accent-[#73b6ff]" :value="oi" x-model.number="answers[q.id]">
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
                      <input type="radio" class="accent-[#73b6ff]" :name="'q' + q.id" :value="oi" x-model.number="answers[q.id]">
                      <span class="text-[15px]" x-text="opt"></span>
                    </label>
                  </template>
                </div>
              </template>
            </fieldset>
          </template>

          <div class="flex flex-wrap items-center gap-4">
            <button type="submit" class="px-btn px-btn-yellow" :disabled="sending" x-text="sending ? 'Submitting…' : 'Submit answers'"></button>
            <p class="text-[14px] text-muted">You only get one attempt.</p>
          </div>
        </form>

        {{-- Result --}}
        <div x-show="result" x-cloak class="mt-6">
          <p class="px-font text-[2rem] font-medium">
            <span x-text="result?.score"></span> / <span x-text="result?.total"></span>
            <span class="text-muted text-[1.2rem]">(<span x-text="result?.percent"></span>%)</span>
          </p>

          <ul class="mt-6 flex flex-col gap-3">
            <template x-for="(r, ri) in (result?.review || [])" :key="r.id">
              <li class="px-inner rounded-sm p-4">
                <p class="text-[15px] font-medium">
                  <span class="text-muted" x-text="(ri + 1) + '. '"></span>
                  <span x-text="questionById(r.id)?.prompt"></span>
                </p>
                <p class="mt-2 text-[14px]" :class="wasRight(r) ? 'text-[#8bd83a]' : 'text-[#ff6b81]'">
                  <span x-text="wasRight(r) ? 'Correct' : 'Not quite'"></span>
                  — the answer is <span class="font-medium" x-text="answerText(r)"></span>
                </p>
              </li>
            </template>
          </ul>

          <button type="button" class="px-btn px-btn-yellow mt-6" @click="reload()">Done</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function crewQuiz() {
      return {
        quiz: null, answers: {}, result: null, sending: false, error: '',

        async open(id) {
          this.error = ''; this.result = null; this.answers = {};
          try {
            const res = await fetch(`/student/quizzes/${id}`, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) { this.error = (await res.json().catch(() => ({}))).message || 'This quiz is not available.'; return; }
            const q = await res.json();
            // Checkboxes collect a list; every other type holds one index.
            q.questions.forEach(qq => { this.answers[qq.id] = qq.type === 'checkboxes' ? [] : null; });
            this.quiz = q;
            this.result = q.result || null;
          } catch (e) { this.error = 'Could not open the quiz. Check your connection and try again.'; }
        },

        close() { this.quiz = null; this.result = null; this.error = ''; },
        reload() { window.location.reload(); },
        questionById(id) { return (this.quiz?.questions || []).find(q => q.id === id); },

        wasRight(r) {
          if (Array.isArray(r.correct)) {
            const a = [...(r.chosen || [])].sort(), b = [...r.correct].sort();
            return a.length === b.length && a.every((v, i) => v === b[i]);
          }
          return r.chosen === r.correct;
        },

        answerText(r) {
          const q = this.questionById(r.id);
          if (!q) return '';
          const idx = Array.isArray(r.correct) ? r.correct : [r.correct];
          return idx.map(i => q.options[i]).filter(Boolean).join(', ');
        },

        async submit() {
          if (this.sending) return;
          this.sending = true; this.error = '';
          try {
            const res = await fetch(`/student/quizzes/${this.quiz.id}/submit`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json', 'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
              },
              body: JSON.stringify({ answers: this.answers }),
            });
            if (!res.ok) { this.error = (await res.json().catch(() => ({}))).message || 'That did not go through.'; return; }
            this.result = (await res.json()).result;
          } catch (e) { this.error = 'Could not submit. Check your connection and try again.'; }
          finally { this.sending = false; }
        },
      };
    }
  </script>
</x-pixel-app>
