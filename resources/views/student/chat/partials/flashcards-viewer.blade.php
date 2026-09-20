{{-- Flashcards Studio — /chat overlay viewer, driven by the `flashcards` Alpine store.
  States: configuring · generating · studying · finished · error.
  Model text is rendered with x-text (escaped). Click the card / Space / Enter to flip, ← → to move. --}}
<div
  x-data="{ fc: $store.flashcards }"
  x-show="fc.isOpen"
  x-cloak
  @keydown.escape.window="fc.isOpen && fc.close()"
  @keydown.right.window="fc.isOpen && fc.state === 'studying' && fc.next()"
  @keydown.left.window="fc.isOpen && fc.state === 'studying' && fc.prev()"
  @keydown.space.window="if (fc.isOpen && fc.state === 'studying') { $event.preventDefault(); fc.flip(); }"
  class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
  role="dialog"
  aria-modal="true"
  aria-label="Astro flashcards"
>
  <div class="absolute inset-0 bg-[rgba(3,3,15,0.74)]" @click="fc.state === 'generating' ? null : fc.close()"></div>

  <div class="relative flex max-h-[94vh] w-full max-w-2xl flex-col overflow-hidden rounded-[24px] border border-glassBorder bg-[rgba(10,8,30,0.97)] shadow-[0_40px_120px_rgba(0,0,0,0.6)]">

    {{-- ============ CONFIGURING ============ --}}
    <section x-show="fc.state === 'configuring'" class="flex flex-col">
      <div class="flex items-center justify-between gap-2 border-b border-glassBorder px-5 py-4 sm:px-6">
        <div class="flex items-center gap-3">
          <span class="grid h-10 w-10 place-items-center rounded-[12px] bg-[linear-gradient(135deg,rgba(115,182,255,0.18),rgba(155,107,255,0.18))] text-[#73b6ff] ring-1 ring-[rgba(115,182,255,0.22)]">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect x="4" y="6" width="16" height="12" rx="2"/><path d="M8 10h8M8 14h5"/></svg>
          </span>
          <div>
            <h2 class="font-display text-base font-semibold text-ink">Flashcards</h2>
            <p class="text-xs text-muted">Turn your conversation with Astro into study cards.</p>
          </div>
        </div>
        <button type="button" @click="fc.close()" class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Close">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="px-5 py-5 sm:px-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-[#5be1ff]">Configuration</p>
          <div class="inline-flex items-center gap-1 rounded-full border border-glassBorder bg-glass p-1">
            <template x-for="n in fc.counts" :key="n">
              <button type="button" @click="fc.count = n"
                :class="fc.count === n ? 'bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] font-semibold text-[#07142e]' : 'text-muted hover:text-ink'"
                class="rounded-full px-3 py-1 text-xs transition" x-text="n"></button>
            </template>
            <span class="px-2.5 py-1 text-xs text-muted">cards</span>
          </div>
        </div>
        <div class="mt-4 flex items-center justify-between gap-3 rounded-[14px] border border-glassBorder bg-glass px-4 py-3">
          <div>
            <p class="text-sm font-medium text-ink">Current conversation</p>
            <p class="text-xs text-muted">Astro builds the cards from what you and Astro just discussed.</p>
          </div>
          <span class="rounded-full border border-glassBorder bg-[rgba(6,6,26,0.5)] px-2.5 py-1 font-mono text-[11px] text-muted">Locked</span>
        </div>
        <div class="mt-6 flex items-center justify-between gap-3 border-t border-glassBorder pt-4">
          <button type="button" @click="fc.close()" class="rounded-full border border-glassBorder bg-glass px-4 py-2 text-sm text-muted transition hover:text-ink">Cancel</button>
          <button type="button" @click="fc.start()" :disabled="!fc.canGenerate" class="inline-flex items-center gap-2 rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-6 py-2.5 text-sm font-semibold text-[#07142e] shadow-[0_10px_40px_rgba(115,182,255,0.4)] transition hover:-translate-y-[2px] disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><rect x="4" y="6" width="16" height="12" rx="2"/><path d="M8 10h8M8 14h5"/></svg>
            Generate flashcards
          </button>
        </div>
        <p x-show="!fc.canGenerate" x-cloak class="mt-2 text-center text-xs text-muted">Send a message to Astro first, then generate.</p>
      </div>
    </section>

    {{-- ============ GENERATING ============ --}}
    <section x-show="fc.state === 'generating'" class="flex flex-col items-center justify-center px-6 py-16 text-center">
      <div class="relative mb-8 h-24 w-24">
        <svg viewBox="0 0 100 100" class="h-full w-full animate-spin" style="animation-duration: 8s">
          <circle cx="50" cy="50" r="40" fill="none" stroke="rgba(115,182,255,0.18)" stroke-width="4"/>
          <circle cx="50" cy="50" r="40" fill="none" stroke="#73b6ff" stroke-width="4" stroke-dasharray="60 200" stroke-linecap="round"/>
        </svg>
      </div>
      <p class="font-display text-lg font-semibold text-ink">Making your flashcards…</p>
      <p class="mt-2 max-w-sm text-sm text-muted">Astro is pulling the key ideas out of your conversation. You can close this and keep chatting — we'll tell you when it's ready.</p>
    </section>

    {{-- ============ STUDYING ============ --}}
    <section x-show="fc.state === 'studying'" class="flex min-h-0 flex-1 flex-col">
      <div class="flex items-center justify-between gap-2 border-b border-glassBorder px-5 py-3">
        <div class="min-w-0">
          <h2 class="truncate font-display text-sm font-semibold text-ink" x-text="fc.deck ? fc.deck.title : 'Flashcards'"></h2>
          <p class="truncate text-xs text-muted" x-text="fc.deck ? (fc.deck.topic || 'Based on your chat with Astro') : ''"></p>
        </div>
        <div class="flex items-center gap-2">
          <span class="rounded-full border border-glassBorder bg-glass px-2.5 py-1 font-mono text-xs text-muted"><span x-text="fc.pos + 1"></span> / <span x-text="fc.total"></span></span>
          <button type="button" @click="fc.shuffle()" class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Shuffle cards" title="Shuffle">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M16 3h5v5M4 20 21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/></svg>
          </button>
          <button type="button" @click="fc.close()" class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Close flashcards">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
          </button>
        </div>
      </div>

      <div class="h-1 bg-[rgba(150,170,255,0.12)]">
        <div class="h-1 bg-[linear-gradient(90deg,#5be1ff,#73b6ff_55%,#9b6bff)] transition-all duration-300" :style="`width:${fc.total ? ((fc.pos+1)/fc.total*100) : 0}%`"></div>
      </div>

      <div class="min-h-0 flex-1 overflow-y-auto px-5 py-6 sm:px-8">
        <template x-if="fc.card">
          <div>
            {{-- Flip card --}}
            <button type="button" @click="fc.flip()" class="block w-full text-left" style="perspective: 1200px" :aria-label="fc.flipped ? 'Show term' : 'Show answer'">
              <div class="relative h-64 w-full transition-transform duration-500 sm:h-72" :style="`transform-style: preserve-3d; transform: rotateY(${fc.flipped ? 180 : 0}deg)`">
                <div class="absolute inset-0 flex flex-col items-center justify-center rounded-[20px] border border-[rgba(115,182,255,0.3)] bg-[linear-gradient(135deg,rgba(115,182,255,0.10),rgba(155,107,255,0.08))] px-6 text-center" style="backface-visibility: hidden">
                  <span class="mb-3 font-mono text-[11px] uppercase tracking-[0.16em] text-[#5be1ff]">Term</span>
                  <p class="font-display text-xl font-semibold leading-snug text-ink" x-text="fc.card.front"></p>
                  <p x-show="fc.card.hint" class="mt-3 text-xs text-muted" x-text="'Hint: ' + fc.card.hint"></p>
                  <span class="absolute bottom-3 font-mono text-[11px] text-muted">Click or press Space to flip</span>
                </div>
                <div class="absolute inset-0 flex flex-col items-center justify-center overflow-y-auto rounded-[20px] border border-[rgba(124,255,178,0.3)] bg-[linear-gradient(135deg,rgba(124,255,178,0.08),rgba(115,182,255,0.08))] px-6 py-4 text-center" style="backface-visibility: hidden; transform: rotateY(180deg)">
                  <span class="mb-3 font-mono text-[11px] uppercase tracking-[0.16em] text-[#7cffb2]">Answer</span>
                  <p class="text-base leading-relaxed text-ink" x-text="fc.card.back"></p>
                </div>
              </div>
            </button>

            <div class="mt-4 flex items-center justify-center gap-2">
              <button type="button" @click="fc.mark(false)" class="rounded-full border border-[rgba(255,138,160,0.4)] bg-[rgba(255,138,160,0.06)] px-4 py-2 text-sm text-[#ff8aa0] transition hover:bg-[rgba(255,138,160,0.12)]">Still learning</button>
              <button type="button" @click="fc.mark(true)" class="rounded-full border border-[rgba(124,255,178,0.4)] bg-[rgba(124,255,178,0.06)] px-4 py-2 text-sm text-[#7cffb2] transition hover:bg-[rgba(124,255,178,0.12)]">Got it</button>
            </div>
          </div>
        </template>
      </div>

      <div class="flex items-center justify-between gap-3 border-t border-glassBorder px-5 py-3">
        <button type="button" @click="fc.prev()" :disabled="fc.pos === 0" class="inline-flex items-center gap-1.5 rounded-full border border-glassBorder bg-glass px-3 py-1.5 text-sm text-muted transition enabled:hover:text-ink disabled:opacity-40">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m15 6-6 6 6 6"/></svg>
          Prev
        </button>
        <span class="font-mono text-xs text-muted"><span x-text="fc.knownCount"></span>/<span x-text="fc.total"></span> known</span>
        <div class="flex items-center gap-2">
          <button type="button" @click="fc.restart()" x-show="fc.knownCount > 0" x-cloak class="rounded-full border border-glassBorder bg-glass px-3 py-1.5 text-sm text-muted transition hover:text-ink">Restart</button>
          <button type="button" @click="fc.next()" class="inline-flex items-center gap-1.5 rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-4 py-1.5 text-sm font-semibold text-[#07142e] shadow-[0_8px_24px_rgba(115,182,255,0.35)] transition hover:-translate-y-[1px] disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:translate-y-0">
            <span x-text="fc.isLast ? 'Finish' : 'Next'"></span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m9 6 6 6-6 6"/></svg>
          </button>
        </div>
      </div>
      <div class="border-t border-glassBorder px-5 py-2 text-center">
        <button type="button" @click="fc.newDeck()" class="text-xs text-muted transition hover:text-ink">Make a new deck</button>
      </div>
    </section>

    {{-- ============ FINISHED ============ --}}
    <section x-show="fc.state === 'finished'" x-cloak class="flex min-h-0 flex-1 flex-col">
      <div class="flex items-center justify-between gap-2 border-b border-glassBorder px-5 py-3">
        <h2 class="font-display text-sm font-semibold text-ink" x-text="fc.deck ? fc.deck.title : 'Flashcards'"></h2>
        <button type="button" @click="fc.close()" class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Close flashcards">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <div class="flex flex-col items-center px-6 py-10 text-center">
        <div class="grid h-20 w-20 place-items-center rounded-full bg-[linear-gradient(135deg,rgba(115,182,255,0.18),rgba(155,107,255,0.18))] ring-1 ring-[rgba(115,182,255,0.22)]">
          <span class="font-display text-2xl font-bold text-ink" x-text="fc.knownCount + '/' + fc.total"></span>
        </div>
        <p class="mt-4 font-display text-lg font-semibold text-ink" x-text="fc.missedCount === 0 ? 'You know them all!' : 'Deck complete'"></p>
        <p class="mt-1 text-sm text-muted" x-text="fc.missedCount === 0 ? 'Every card marked as known. Nice work.' : (fc.missedCount + ' card' + (fc.missedCount === 1 ? '' : 's') + ' still to review')"></p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
          <button type="button" @click="fc.reviewMissed()" x-show="fc.missedCount > 0" class="rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-5 py-2 text-sm font-semibold text-[#07142e] transition hover:-translate-y-[1px]">Review missed cards</button>
          <button type="button" @click="fc.restart()" class="rounded-full border border-glassBorder bg-glass px-4 py-2 text-sm text-ink transition hover:border-[rgba(115,182,255,0.35)]">Restart deck</button>
          <button type="button" @click="fc.newDeck()" class="rounded-full border border-glassBorder bg-glass px-4 py-2 text-sm text-muted transition hover:text-ink">New deck</button>
        </div>
        <button type="button" @click="fc.close()" class="mt-4 text-xs text-muted transition hover:text-ink">Close</button>
      </div>
    </section>

    {{-- ============ ERROR ============ --}}
    <section x-show="fc.state === 'error'" class="flex flex-col items-center justify-center px-6 py-16 text-center">
      <div class="grid h-16 w-16 place-items-center rounded-full bg-[rgba(255,138,160,0.12)] text-[#ff8aa0]">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>
      </div>
      <h3 class="mt-4 font-display text-lg font-semibold text-ink">Couldn't make your flashcards</h3>
      <p class="mt-2 max-w-md text-sm text-muted" x-text="fc.error"></p>
      <p x-show="fc.errorKind === 'insufficient_content'" x-cloak class="mt-2 max-w-md text-xs text-[#ffcf85]">Tip: chat a bit more with Astro about the topic first, then try again.</p>
      <div class="mt-6 flex items-center gap-2">
        <button type="button" @click="fc.generate()" :class="fc.errorKind === 'insufficient_content' ? 'hidden' : ''" class="rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-5 py-2 text-sm font-semibold text-[#07142e] transition hover:-translate-y-[2px]">Try again</button>
        <button type="button" @click="fc.close()" class="rounded-full px-4 py-2 text-sm text-muted transition hover:text-ink">Close</button>
      </div>
    </section>

  </div>
</div>
