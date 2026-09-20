{{-- Quiz Studio — /chat overlay viewer.
  ONE overlay (no redirect) rendering five states from the global `quiz` Alpine store:
    - configuring : confirm source (current conversation, locked) + preview
    - generating  : spinner + staged copy
    - answering   : one question at a time, select A–D, Next/Prev
    - results     : score + per-question review + retry
    - error       : safe message + retry
  Model text is rendered with x-text (escaped). No inline JS besides Alpine. --}}
<div
  x-data="{ qz: $store.quiz }"
  x-show="qz.isOpen"
  x-cloak
  @keydown.escape.window="qz.isOpen && (qz.state === 'generating' ? null : (qz.state === 'answering' || qz.state === 'results' ? qz.close() : qz.close()))"
  @keydown.right.window="qz.isOpen && qz.state === 'answering' && qz.next()"
  @keydown.left.window="qz.isOpen && qz.state === 'answering' && qz.prev()"
  class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
  role="dialog"
  aria-modal="true"
  aria-label="Astro quiz studio"
>
  <div class="absolute inset-0 bg-[rgba(3,3,15,0.74)]"
       @click="qz.state === 'generating' ? null : qz.close()"></div>

  <div class="relative flex max-h-[94vh] w-full max-w-3xl flex-col overflow-hidden rounded-[24px] border border-glassBorder bg-[rgba(10,8,30,0.97)] shadow-[0_40px_120px_rgba(0,0,0,0.6)]">

    {{-- ============ CONFIGURING ============ --}}
    <section x-show="qz.state === 'configuring'" class="flex flex-col">
      <div class="flex items-center justify-between gap-2 border-b border-glassBorder px-5 py-4 sm:px-6">
        <div class="flex items-center gap-3">
          <span class="grid h-10 w-10 place-items-center rounded-[12px] bg-[linear-gradient(135deg,rgba(115,182,255,0.18),rgba(155,107,255,0.18))] text-[#73b6ff] ring-1 ring-[rgba(115,182,255,0.22)]">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 0 1 4.5 1.5c0 1.5-2 2-2 2.5"/><path d="M12 17h.01"/></svg>
          </span>
          <div>
            <h2 class="font-display text-base font-semibold text-ink">Quiz</h2>
            <p class="text-xs text-muted">Turn your conversation with Astro into a quick check.</p>
          </div>
        </div>
        <button type="button" @click="qz.close()" class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Close">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="px-5 py-5 sm:px-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-[#5be1ff]">Configuration</p>
          <div class="inline-flex items-center gap-1 rounded-full border border-glassBorder bg-glass p-1">
            <template x-for="n in qz.counts" :key="n">
              <button type="button" @click="qz.count = n"
                :class="qz.count === n ? 'bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] font-semibold text-[#07142e]' : 'text-muted hover:text-ink'"
                class="rounded-full px-3 py-1 text-xs transition" x-text="n"></button>
            </template>
            <span class="px-2.5 py-1 text-xs text-muted">questions</span>
          </div>
        </div>

        <div class="mt-4">
          <div class="mb-1.5 flex items-center justify-between">
            <label class="font-mono text-[11px] uppercase tracking-[0.16em] text-[#5be1ff]">Sources</label>
            <span class="font-mono text-[11px] text-muted">1 source</span>
          </div>
          <div class="flex items-center justify-between gap-3 rounded-[14px] border border-glassBorder bg-glass px-4 py-3">
            <div class="flex items-center gap-3">
              <span class="grid h-8 w-8 place-items-center rounded-[10px] bg-[rgba(115,182,255,0.12)] text-[#73b6ff]">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              </span>
              <div>
                <p class="text-sm font-medium text-ink">Current conversation</p>
                <p class="text-xs text-muted">with Astro</p>
              </div>
            </div>
            <span class="rounded-full border border-glassBorder bg-[rgba(6,6,26,0.5)] px-2.5 py-1 font-mono text-[11px] text-muted">Locked</span>
          </div>
          <p class="mt-2 text-xs leading-relaxed text-muted">Astro builds the quiz from what you and Astro just discussed — no extra upload needed.</p>
        </div>

        <div class="mt-4">
          <label class="font-mono text-[11px] uppercase tracking-[0.16em] text-[#5be1ff]">What will the quiz cover?</label>
          <div class="mt-1.5 rounded-[14px] border border-glassBorder bg-[rgba(6,6,26,0.55)] px-4 py-3">
            <p class="text-sm font-medium text-ink">Based on your current conversation with Astro</p>
            <p x-show="qz._preview && qz._preview.trim() !== ''" x-cloak class="mt-1 line-clamp-2 text-xs leading-relaxed text-muted" x-text="(qz._preview || '').slice(0, 160)"></p>
            <p x-show="!qz._preview || qz._preview.trim() === ''" x-cloak class="mt-1 text-xs text-[#ffcf85]">Chat with Astro first — add a few messages so there is something to quiz.</p>
          </div>
        </div>

        <div class="mt-6 flex items-center justify-between gap-3 border-t border-glassBorder pt-4">
          <button type="button" @click="qz.close()" class="rounded-full border border-glassBorder bg-glass px-4 py-2 text-sm text-muted transition hover:text-ink">Cancel</button>
          <button type="button" @click="qz.start()" :disabled="!qz.canGenerate" class="inline-flex items-center gap-2 rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-6 py-2.5 text-sm font-semibold text-[#07142e] shadow-[0_10px_40px_rgba(115,182,255,0.4)] transition hover:-translate-y-[2px] disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 0 1 4.5 1.5c0 1.5-2 2-2 2.5"/><path d="M12 17h.01"/></svg>
            Generate quiz
          </button>
        </div>
        <p x-show="!qz.canGenerate" x-cloak class="mt-2 text-center text-xs text-muted">Send a message to Astro first, then generate.</p>
      </div>
    </section>

    {{-- ============ GENERATING ============ --}}
    <section x-show="qz.state === 'generating'" class="flex flex-col items-center justify-center px-6 py-16 text-center">
      <div class="relative mb-8 h-24 w-24">
        <svg viewBox="0 0 100 100" class="h-full w-full animate-spin" style="animation-duration: 8s">
          <circle cx="50" cy="50" r="40" fill="none" stroke="rgba(115,182,255,0.18)" stroke-width="4"/>
          <circle cx="50" cy="50" r="40" fill="none" stroke="#73b6ff" stroke-width="4" stroke-dasharray="60 200" stroke-linecap="round"/>
        </svg>
        <span class="absolute inset-0 grid place-items-center font-display text-sm font-bold text-ink">?</span>
      </div>
      <p class="font-display text-lg font-semibold text-ink">Building your quiz…</p>
      <p class="mt-2 max-w-sm text-sm text-muted">Astro is turning your conversation into quick questions. You can close this and keep chatting — we'll tell you when it's ready.</p>
    </section>

    {{-- ============ ANSWERING ============ --}}
    <section x-show="qz.state === 'answering'" class="flex min-h-0 flex-1 flex-col">
      <div class="flex items-center justify-between gap-2 border-b border-glassBorder px-5 py-3">
        <div class="min-w-0">
          <h2 class="truncate font-display text-sm font-semibold text-ink" x-text="qz.quiz ? qz.quiz.title : 'Quick Check'"></h2>
          <p class="truncate text-xs text-muted" x-text="qz.quiz ? (qz.quiz.description || qz.quiz.topic || '') : ''"></p>
        </div>
        <div class="flex items-center gap-2">
          <span class="rounded-full border border-glassBorder bg-glass px-2.5 py-1 font-mono text-xs text-muted"><span x-text="qz.current + 1"></span> / <span x-text="qz.total"></span></span>
          <button type="button" @click="qz.close()" class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Close quiz">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
          </button>
        </div>
      </div>

      <div class="h-1 bg-[rgba(150,170,255,0.12)]">
        <div class="h-1 bg-[linear-gradient(90deg,#5be1ff,#73b6ff_55%,#9b6bff)] transition-all duration-300" :style="`width:${qz.total ? ((qz.current+1)/qz.total*100) : 0}%`"></div>
      </div>

      <div class="min-h-0 flex-1 overflow-y-auto px-5 py-6 sm:px-8">
        <template x-if="qz.quiz && qz.quiz.questions && qz.quiz.questions[qz.current]">
          <div>
            <div class="mb-2 flex items-center gap-2">
              <span class="rounded-full bg-[rgba(115,182,255,0.12)] px-2 py-0.5 font-mono text-[11px] uppercase tracking-wide text-[#73b6ff]" x-text="(qz.quiz.questions[qz.current].difficulty || 'easy')"></span>
              <span class="font-mono text-[11px] text-muted"><span x-text="qz.quiz.questions[qz.current].points || 1"></span> pt</span>
              <span x-show="qz.answers[qz.quiz.questions[qz.current].id]" x-cloak class="ml-auto font-mono text-[11px] text-[#7cffb2]">Answered</span>
            </div>
            <h3 class="font-display text-[17px] font-semibold leading-relaxed text-ink" x-text="qz.quiz.questions[qz.current].question"></h3>

            <div class="mt-5 grid gap-2.5">
              <template x-for="choice in qz.quiz.questions[qz.current].choices" :key="choice.key">
                <button type="button" @click="qz.selectAnswer(choice.key)"
                  class="flex items-start gap-3 rounded-[14px] border px-4 py-3 text-left transition"
                  :class="(() => {
                    const q = qz.quiz.questions[qz.current];
                    const sel = qz.answers[q.id];
                    const isSelected = sel === choice.key;
                    const hasAnswered = !!sel;
                    // Before answering: neutral glass; after answering: highlight selected + correct
                    if (!hasAnswered) return isSelected ? 'border-[rgba(115,182,255,0.5)] bg-[rgba(115,182,255,0.10)]' : 'border-glassBorder bg-glass hover:border-[rgba(115,182,255,0.35)]';
                    const isCorrect = choice.key === q.correct_key;
                    if (isSelected && isCorrect) return 'border-[rgba(124,255,178,0.5)] bg-[rgba(124,255,178,0.10)]';
                    if (isSelected && !isCorrect) return 'border-[rgba(255,138,160,0.5)] bg-[rgba(255,138,160,0.08)]';
                    if (!isSelected && isCorrect) return 'border-[rgba(124,255,178,0.35)] bg-[rgba(124,255,178,0.06)]';
                    return 'border-glassBorder bg-glass opacity-60';
                  })()">
                  <span class="grid h-7 w-7 flex-none place-items-center rounded-full border text-xs font-bold"
                    :class="(() => {
                      const q = qz.quiz.questions[qz.current];
                      const sel = qz.answers[q.id];
                      const isSelected = sel === choice.key;
                      const hasAnswered = !!sel;
                      if (!hasAnswered) return isSelected ? 'border-[#73b6ff] bg-[#73b6ff] text-[#07142e]' : 'border-glassBorder bg-[rgba(6,6,26,0.6)] text-muted';
                      const isCorrect = choice.key === q.correct_key;
                      if (isSelected && isCorrect) return 'border-[#7cffb2] bg-[#7cffb2] text-[#07142e]';
                      if (isSelected && !isCorrect) return 'border-[#ff8aa0] bg-[#ff8aa0] text-[#07142e]';
                      if (!isSelected && isCorrect) return 'border-[#7cffb2] bg-[rgba(124,255,178,0.18)] text-[#7cffb2]';
                      return 'border-glassBorder bg-[rgba(6,6,26,0.6)] text-muted';
                    })()" x-text="choice.key"></span>
                  <span class="pt-0.5 text-sm leading-relaxed text-ink" x-text="choice.text"></span>
                  <span x-show="(() => { const q=qz.quiz.questions[qz.current]; const sel=qz.answers[q.id]; return !!sel && choice.key===q.correct_key; })()" class="ml-auto pt-1 text-[#7cffb2]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12l5 5L20 7"/></svg></span>
                  <span x-show="(() => { const q=qz.quiz.questions[qz.current]; const sel=qz.answers[q.id]; return !!sel && sel===choice.key && choice.key!==q.correct_key; })()" class="ml-auto pt-1 text-[#ff8aa0]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg></span>
                </button>
              </template>
            </div>

            <div x-show="qz.answers[qz.quiz.questions[qz.current].id]" x-cloak class="mt-4 rounded-[12px] border px-4 py-3"
              :class="qz.answers[qz.quiz.questions[qz.current].id] === qz.quiz.questions[qz.current].correct_key ? 'border-[rgba(124,255,178,0.3)] bg-[rgba(124,255,178,0.08)]' : 'border-[rgba(255,138,160,0.3)] bg-[rgba(255,138,160,0.06)]'">
              <p class="text-xs font-semibold" :class="qz.answers[qz.quiz.questions[qz.current].id] === qz.quiz.questions[qz.current].correct_key ? 'text-[#7cffb2]' : 'text-[#ff8aa0]'" x-text="qz.answers[qz.quiz.questions[qz.current].id] === qz.quiz.questions[qz.current].correct_key ? 'Correct!' : 'Not quite — the answer is ' + qz.quiz.questions[qz.current].correct_key"></p>
              <p class="mt-1 text-sm leading-relaxed text-ink/90" x-text="qz.quiz.questions[qz.current].explanation"></p>
            </div>
          </div>
        </template>
      </div>

      <div class="flex items-center justify-between gap-3 border-t border-glassBorder px-5 py-3">
        <button type="button" @click="qz.prev()" :disabled="qz.current === 0" class="inline-flex items-center gap-1.5 rounded-full border border-glassBorder bg-glass px-3 py-1.5 text-sm text-muted transition enabled:hover:text-ink disabled:opacity-40">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m15 6-6 6 6 6"/></svg>
          Prev
        </button>
        <span class="font-mono text-xs text-muted"><span x-text="qz.answeredCount"></span>/<span x-text="qz.total"></span> answered</span>
        <button type="button" @click="qz.next()" :disabled="!qz.answers[qz.quiz && qz.quiz.questions ? qz.quiz.questions[qz.current].id : '']" class="inline-flex items-center gap-1.5 rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-4 py-1.5 text-sm font-semibold text-[#07142e] shadow-[0_8px_24px_rgba(115,182,255,0.35)] transition hover:-translate-y-[1px] disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:translate-y-0">
          <span x-text="qz.current === qz.total - 1 ? 'See results' : 'Next'"></span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m9 6 6 6-6 6"/></svg>
        </button>
      </div>
    </section>

    {{-- ============ RESULTS ============ --}}
    <section x-show="qz.state === 'results'" class="flex min-h-0 flex-1 flex-col">
      <div class="flex items-center justify-between gap-2 border-b border-glassBorder px-5 py-3">
        <div>
          <h2 class="font-display text-sm font-semibold text-ink">Your results</h2>
          <p class="text-xs text-muted" x-text="(qz.quiz ? qz.quiz.title + ' · ' : '') + qz.score + ' / ' + qz.maxScore + ' points'"></p>
        </div>
        <button type="button" @click="qz.close()" class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Close quiz">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="min-h-0 flex-1 overflow-y-auto px-5 py-6 sm:px-8">
        <div class="flex flex-col items-center py-2 text-center">
          <div class="grid h-20 w-20 place-items-center rounded-full bg-[linear-gradient(135deg,rgba(115,182,255,0.18),rgba(155,107,255,0.18))] ring-1 ring-[rgba(115,182,255,0.22)]">
            <span class="font-display text-2xl font-bold text-ink" x-text="qz.score + '/' + qz.maxScore"></span>
          </div>
          <p class="mt-3 font-display text-lg font-semibold text-ink" x-text="qz.score === qz.maxScore ? 'Perfect — you nailed it!' : (qz.score >= Math.ceil(qz.maxScore*0.6) ? 'Nice work!' : 'Good try — review and try again')"></p>
          <p class="mt-1 text-sm text-muted" x-text="qz.answeredCount + ' of ' + qz.total + ' answered · ' + (qz.quiz ? (qz.quiz.topic || 'based on your chat with Astro') : '')"></p>
        </div>

        <div class="mt-6 space-y-3">
          <template x-for="(q, i) in (qz.quiz ? qz.quiz.questions : [])" :key="q.id">
            <div class="rounded-[14px] border px-4 py-3"
              :class="qz.answers[q.id] === q.correct_key ? 'border-[rgba(124,255,178,0.28)] bg-[rgba(124,255,178,0.06)]' : 'border-[rgba(255,138,160,0.28)] bg-[rgba(255,138,160,0.05)]'">
              <div class="flex items-start gap-2">
                <span class="grid h-6 w-6 flex-none place-items-center rounded-full text-xs font-bold"
                  :class="qz.answers[q.id] === q.correct_key ? 'bg-[#7cffb2] text-[#07142e]' : 'bg-[#ff8aa0] text-[#07142e]'"
                  x-text="qz.answers[q.id] === q.correct_key ? '✓' : '✗'"></span>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium text-ink"><span class="font-mono text-xs text-muted" x-text="(i+1)+'. '"></span><span x-text="q.question"></span></p>
                  <p class="mt-1 text-xs text-muted">You answered <span class="font-semibold text-ink" x-text="qz.answers[q.id] || '—'"></span> · Correct is <span class="font-semibold text-[#7cffb2]" x-text="q.correct_key"></span></p>
                  <p class="mt-1.5 text-xs leading-relaxed text-muted" x-text="q.explanation"></p>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>

      <div class="flex items-center justify-between gap-3 border-t border-glassBorder px-5 py-3">
        <button type="button" @click="qz.close()" class="rounded-full border border-glassBorder bg-glass px-4 py-2 text-sm text-muted transition hover:text-ink">Close</button>
        <div class="flex items-center gap-2">
          <button type="button" @click="qz.current = 0; qz.state = 'answering'" class="rounded-full border border-glassBorder bg-glass px-4 py-2 text-sm text-ink transition hover:border-[rgba(115,182,255,0.35)]">Review answers</button>
          <button type="button" @click="qz.retry()" class="rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-5 py-2 text-sm font-semibold text-[#07142e] transition hover:-translate-y-[1px]">New quiz</button>
        </div>
      </div>
    </section>

    {{-- ============ ERROR ============ --}}
    <section x-show="qz.state === 'error'" class="flex flex-col items-center justify-center px-6 py-16 text-center">
      <div class="grid h-16 w-16 place-items-center rounded-full bg-[rgba(255,138,160,0.12)] text-[#ff8aa0]">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>
      </div>
      <h3 class="mt-4 font-display text-lg font-semibold text-ink">Couldn't build your quiz</h3>
      <p class="mt-2 max-w-md text-sm text-muted" x-text="qz.error"></p>
      <p x-show="qz.errorKind === 'insufficient_content'" x-cloak class="mt-2 max-w-md text-xs text-[#ffcf85]">Tip: chat a bit more with Astro about the topic first, then try again.</p>
      <div class="mt-6 flex items-center gap-2">
        <button type="button" @click="qz.generate()" :class="qz.errorKind === 'insufficient_content' ? 'hidden' : ''" class="rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-5 py-2 text-sm font-semibold text-[#07142e] transition hover:-translate-y-[2px]">Try again</button>
        <button type="button" @click="qz.close()" class="rounded-full px-4 py-2 text-sm text-muted transition hover:text-ink">Close</button>
      </div>
    </section>

  </div>
</div>
