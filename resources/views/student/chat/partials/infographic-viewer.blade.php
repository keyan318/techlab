{{-- Infographic Studio — in-/chat overlay viewer.
  ONE overlay (no redirect, no new tab, no external viewer) rendering four
  states from the global `infographic` Alpine store:
    - configuring : pick/confirm learning material (source modal)
    - generating  : staged, meaningful loading labels (no fake %)
    - ready       : the slide deck (InfographicViewer)
    - error       : safe message + retry
  Model text is rendered with x-text (escaped) so nothing injected can execute. --}}
<div
  x-data="{ ig: $store.infographic || {} }"
  x-show="ig.isOpen"
  x-cloak
  @keydown.escape.window="ig.isOpen && ig.close()"
  @keydown.right.window="ig.isOpen && ig.state === 'ready' && ig.next()"
  @keydown.left.window="ig.isOpen && ig.state === 'ready' && ig.prev()"
  class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
  role="dialog"
  aria-modal="true"
  aria-label="Astro infographic studio"
>
  <div class="absolute inset-0 bg-[rgba(3,3,15,0.74)]"
       @click="ig.state === 'ready' || ig.state === 'configuring' || ig.state === 'error' ? ig.close() : null"></div>

  <div class="relative flex max-h-[94vh] w-full max-w-5xl flex-col overflow-hidden rounded-[24px] border border-glassBorder bg-[rgba(10,8,30,0.97)] shadow-[0_40px_120px_rgba(0,0,0,0.6)]">

    {{-- ============ CONFIGURING : Flashcards-style setup (infographic) ============ --}}
    <section x-show="ig.state === 'configuring'" class="flex flex-col">
      {{-- Header: icon + title + close --}}
      <div class="flex items-center justify-between gap-2 border-b border-glassBorder px-5 py-4 sm:px-6">
        <div class="flex items-center gap-3">
          <span class="grid h-10 w-10 place-items-center rounded-[12px] bg-[linear-gradient(135deg,rgba(115,182,255,0.18),rgba(155,107,255,0.18))] text-[#73b6ff] ring-1 ring-[rgba(115,182,255,0.22)]">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M4 20V4M4 20h16M8 16v-5M12 16V8M16 16v-8"/></svg>
          </span>
          <div>
            <h2 class="font-display text-base font-semibold text-ink">Infographic</h2>
            <p class="text-xs text-muted">Turn your conversation with Astro into a visual lesson.</p>
          </div>
        </div>
        <button type="button" @click="ig.close()" class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Close">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="px-5 py-5 sm:px-6">
        {{-- Configuration row: depth/length hint (informational — backend chooses 3-12 slides). --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-[#5be1ff]">Configuration</p>
          <div class="inline-flex items-center gap-1 rounded-full border border-glassBorder bg-glass p-1">
            <span class="rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-3 py-1 text-xs font-semibold text-[#07142e]">Standard</span>
            <span class="px-2.5 py-1 text-xs text-muted">Astro chooses 3–12 slides to fit the conversation</span>
          </div>
        </div>

        {{-- Sources (locked to current conversation) --}}
        <div class="mt-4">
          <div class="mb-1.5 flex items-center justify-between">
            <label class="font-mono text-[11px] uppercase tracking-[0.16em] text-[#5be1ff]">Sources</label>
            <span class="font-mono text-[11px] text-muted">1 source</span>
          </div>
          <div class="flex items-center justify-between gap-3 rounded-[14px] border border-glassBorder bg-glass px-4 py-3">
            <div class="flex items-center gap-3">
              <span class="grid h-8 w-8 place-items-center rounded-[10px] bg-[rgba(115,182,255,0.12)] text-[#73b6ff]">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M12 11v1"/><path d="M8 15h8"/></svg>
              </span>
              <div>
                <p class="text-sm font-medium text-ink">Current conversation</p>
                <p class="text-xs text-muted">with Astro</p>
              </div>
            </div>
            <span class="rounded-full border border-glassBorder bg-[rgba(6,6,26,0.5)] px-2.5 py-1 font-mono text-[11px] text-muted">Locked</span>
          </div>
          <p class="mt-2 text-xs leading-relaxed text-muted">Astro will build the infographic from what you and Astro discussed — no extra upload needed.</p>
        </div>

        {{-- Topic / context (read-only, derived from conversation) --}}
        <div class="mt-4">
          <div class="mb-1.5 flex items-center gap-2">
            <label class="font-mono text-[11px] uppercase tracking-[0.16em] text-[#5be1ff]">What should the infographic explain?</label>
          </div>
          <div class="rounded-[14px] border border-glassBorder bg-[rgba(6,6,26,0.55)] px-4 py-3">
            <p class="text-sm font-medium text-ink">Based on your current conversation with Astro</p>
            <p x-show="(ig.transcript || '').trim() !== ''" x-cloak class="mt-1 line-clamp-2 text-xs leading-relaxed text-muted" x-text="(ig.transcript || '').trim().split(/\n\n/).slice(0,2).join(' — ').slice(0, 160)"></p>
            <p x-show="(ig.transcript || '').trim() === ''" x-cloak class="mt-1 text-xs text-[#ffcf85]">Chat with Astro first — add a few messages so there is something to visualize.</p>
          </div>
        </div>

        <p x-show="ig.errorKind === 'empty_source'" x-cloak x-text="ig.error" class="mt-3 rounded-[12px] border border-[rgba(255,138,160,0.35)] bg-[rgba(255,138,160,0.08)] px-3 py-2 text-sm text-[#ffb3c2]"></p>

        {{-- Bottom action --}}
        <div class="mt-6 flex items-center justify-between gap-3 border-t border-glassBorder pt-4">
          <button type="button" @click="ig.close()" class="rounded-full border border-glassBorder bg-glass px-4 py-2 text-sm text-muted transition hover:text-ink">Cancel</button>
          <button type="button" @click="ig.generate()" :disabled="(ig.transcript || '').trim() === ''" class="inline-flex items-center gap-2 rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-6 py-2.5 text-sm font-semibold text-[#07142e] shadow-[0_10px_40px_rgba(115,182,255,0.4)] transition hover:-translate-y-[2px] disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 3v4M3 5h4M6 17v4M4 19h4"/><path d="M13 3l2.5 6.5L22 12l-6.5 2.5L13 21l-2.5-6.5L4 12l6.5-2.5z"/></svg>
            Generate infographic
          </button>
        </div>
        <p x-show="(ig.transcript || '').trim() === ''" x-cloak class="mt-2 text-center text-xs text-muted">Add a conversation above, then generate.</p>
      </div>
    </section>
    {{-- ============ GENERATING ============ --}}
    <section x-show="ig.state === 'generating'" class="flex flex-col items-center justify-center px-6 py-16 text-center">
      <div class="relative mb-8 h-24 w-24">
        <svg viewBox="0 0 100 100" class="h-full w-full animate-spin" style="animation-duration: 8s">
          <circle cx="50" cy="50" r="40" fill="none" stroke="rgba(115,182,255,0.18)" stroke-width="4"/>
          <circle cx="50" cy="50" r="40" fill="none" stroke="#73b6ff" stroke-width="4" stroke-dasharray="60 200" stroke-linecap="round"/>
        </svg>
        <circle cx="50" cy="10" r="6" fill="#9b6bff"/>
      </div>
      <p class="font-display text-lg font-semibold text-ink" x-text="ig.phases[ig.phase]"></p>
      <p class="mt-2 max-w-sm text-sm text-muted">Astro is turning your material into a visual lesson. This can take up to a minute.</p>
      <ul class="mt-6 space-y-1.5 text-left">
        <template x-for="(p, i) in ig.phases" :key="i">
          <li class="flex items-center gap-2 text-xs" :class="i <= ig.phase ? 'text-ink' : 'text-muted/50'">
            <span class="h-1.5 w-1.5 rounded-full" :class="i <= ig.phase ? 'bg-[#5be1ff]' : 'bg-glassBorder'"></span>
            <span x-text="p"></span>
          </li>
        </template>
      </ul>
    </section>

    {{-- ============ READY : the deck ============ --}}
    <section x-show="ig.state === 'ready'" class="flex min-h-0 flex-1 flex-col">
      <div class="flex items-center justify-between gap-2 border-b border-glassBorder px-5 py-3">
        <div class="min-w-0">
          <h2 class="truncate font-display text-sm font-semibold text-ink" x-text="ig.data ? ig.data.title : ''"></h2>
          <p class="truncate text-xs text-muted" x-text="ig.data ? ig.data.subtitle : ''"></p>
        </div>
        <div class="flex items-center gap-3">
          <span class="font-mono text-xs text-muted"><span x-text="ig.current + 1"></span> / <span x-text="ig.slideCount"></span></span>
          <button type="button" @click="ig.close()" class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Close infographic">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
          </button>
        </div>
      </div>

      <div class="relative min-h-0 flex-1 overflow-y-auto px-5 py-6 sm:px-8">
        <svg class="pointer-events-none absolute inset-0 h-full w-full opacity-60" viewBox="0 0 800 500" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
          <defs><radialGradient id="igbg" cx="50%" cy="35%" r="80%"><stop offset="0%" stop-color="#1a1147"/><stop offset="60%" stop-color="#100a2e"/><stop offset="100%" stop-color="#06061a"/></radialGradient></defs>
          <rect width="800" height="500" fill="url(#igbg)"/>
          <circle cx="660" cy="90" r="54" fill="none" stroke="#73b6ff" stroke-opacity="0.22"/>
          <circle cx="120" cy="420" r="40" fill="none" stroke="#9b6bff" stroke-opacity="0.18"/>
        </svg>

        <template x-for="(slide, i) in (ig.data ? ig.data.slides : [])" :key="i">
          <div x-show="i === ig.current" class="relative">
            {{-- COVER --}}
            <template x-if="slide.type === 'cover'">
              <div class="flex flex-col items-center py-6 text-center">
                <div x-show="slide.image" class="mb-5 overflow-hidden rounded-[18px] border border-glassBorder">
                  <img :src="slide.image" alt="" class="h-44 w-auto object-cover" loading="lazy" />
                </div>
                <div x-show="!slide.image" class="mb-5 grid h-40 w-40 place-items-center rounded-full bg-[radial-gradient(circle_at_35%_30%,#73b6ff,#241456)] shadow-[0_0_60px_rgba(115,182,255,0.4)]">
                  <span class="h-10 w-10 rounded-full border-2 border-[rgba(233,238,255,0.5)]"></span>
                </div>
                <h3 class="font-display text-3xl font-bold text-ink sm:text-4xl" x-text="slide.title"></h3>
                <p class="mt-3 max-w-xl bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] bg-clip-text text-lg font-semibold text-transparent sm:text-xl" x-text="slide.subtitle"></p>
                <p class="mt-4 max-w-2xl text-sm text-muted" x-text="ig.data ? ig.data.source_summary : ''"></p>
              </div>
            </template>

            {{-- CONCEPT --}}
            <template x-if="slide.type === 'concept'">
              <div class="mx-auto max-w-2xl">
                <h3 class="font-display text-2xl font-bold text-ink" x-text="slide.title"></h3>
                <p x-show="slide.subtitle" class="mt-1 text-sm text-[#5be1ff]" x-text="slide.subtitle"></p>
                <p x-show="slide.summary" class="mt-4 text-[15px] leading-relaxed text-ink/90" x-text="slide.summary"></p>
                <div x-show="slide.keyPoints.length" class="mt-5 grid gap-2 sm:grid-cols-2">
                  <template x-for="(kp, k) in slide.keyPoints" :key="k">
                    <div class="flex items-start gap-2 rounded-[12px] border border-glassBorder bg-glass px-3 py-2">
                      <span class="mt-1 h-1.5 w-1.5 flex-none rounded-full bg-[#5be1ff]"></span>
                      <span class="text-sm text-ink/90" x-text="kp"></span>
                    </div>
                  </template>
                </div>
                <p x-show="slide.analogy" class="mt-5 rounded-[12px] border border-[rgba(155,107,255,0.3)] bg-[rgba(155,107,255,0.08)] px-4 py-3 text-sm text-ink/90">
                  <span class="font-semibold text-[#9b6bff]">Analogy: </span><span x-text="slide.analogy"></span>
                </p>
              </div>
            </template>

            {{-- ARCHITECTURE / DIAGRAM --}}
            <template x-if="slide.type === 'architecture' || slide.type === 'diagram'">
              <div class="mx-auto max-w-3xl">
                <h3 class="font-display text-2xl font-bold text-ink" x-text="slide.title"></h3>
                <p x-show="slide.summary" class="mt-2 text-[15px] leading-relaxed text-ink/90" x-text="slide.summary"></p>
                <div class="mt-5 space-y-3">
                  <template x-for="(el, k) in slide.elements" :key="k">
                    <div class="flex items-center gap-3 rounded-[14px] border border-glassBorder bg-glass px-4 py-3">
                      <span class="grid h-9 w-9 flex-none place-items-center rounded-full bg-[linear-gradient(135deg,rgba(115,182,255,0.2),rgba(155,107,255,0.2))] font-semibold text-[#73b6ff]" x-text="k + 1"></span>
                      <div class="min-w-0">
                        <p class="font-medium text-ink" x-text="el.name"></p>
                        <p x-show="el.detail" class="text-sm text-muted" x-text="el.detail"></p>
                      </div>
                    </div>
                  </template>
                  <p x-show="!slide.elements.length" class="text-sm text-muted" x-text="slide.summary"></p>
                </div>
              </div>
            </template>

            {{-- PROCESS / TIMELINE --}}
            <template x-if="slide.type === 'process' || slide.type === 'timeline'">
              <div class="mx-auto max-w-3xl">
                <h3 class="font-display text-2xl font-bold text-ink" x-text="slide.title"></h3>
                <p x-show="slide.summary" class="mt-2 text-[15px] leading-relaxed text-ink/90" x-text="slide.summary"></p>
                <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                  <template x-for="(st, k) in (slide.steps.length ? slide.steps : [])" :key="k">
                    <div class="flex-1 rounded-[14px] border border-glassBorder bg-glass p-4">
                      <div class="mb-1 flex items-center gap-2">
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-[#73b6ff] text-xs font-bold text-[#07142e]" x-text="k + 1"></span>
                        <span class="font-medium text-ink" x-text="st.title"></span>
                      </div>
                      <p x-show="st.detail" class="text-sm text-muted" x-text="st.detail"></p>
                    </div>
                  </template>
                  <template x-if="!slide.steps.length">
                    <template x-for="(kp, k) in slide.keyPoints" :key="k">
                      <div class="flex-1 rounded-[14px] border border-glassBorder bg-glass p-4">
                        <div class="mb-1 flex items-center gap-2">
                          <span class="grid h-7 w-7 place-items-center rounded-full bg-[#73b6ff] text-xs font-bold text-[#07142e]" x-text="k + 1"></span>
                          <span class="text-sm text-ink/90" x-text="kp"></span>
                        </div>
                      </div>
                    </template>
                  </template>
                </div>
              </div>
            </template>

            {{-- COMPARISON --}}
            <template x-if="slide.type === 'comparison'">
              <div class="mx-auto max-w-3xl">
                <h3 class="font-display text-2xl font-bold text-ink" x-text="slide.title"></h3>
                <template x-if="slide.comparison">
                  <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[14px] border border-glassBorder bg-glass p-4">
                      <p class="mb-2 font-semibold text-[#73b6ff]" x-text="slide.comparison.left.label"></p>
                      <ul class="space-y-1.5">
                        <template x-for="(p, k) in slide.comparison.left.points" :key="k">
                          <li class="flex gap-2 text-sm text-ink/90"><span class="text-[#73b6ff]">+</span><span x-text="p"></span></li>
                        </template>
                      </ul>
                    </div>
                    <div class="rounded-[14px] border border-[rgba(155,107,255,0.4)] bg-glass p-4">
                      <p class="mb-2 font-semibold text-[#9b6bff]" x-text="slide.comparison.right.label"></p>
                      <ul class="space-y-1.5">
                        <template x-for="(p, k) in slide.comparison.right.points" :key="k">
                          <li class="flex gap-2 text-sm text-ink/90"><span class="text-[#9b6bff]">+</span><span x-text="p"></span></li>
                        </template>
                      </ul>
                    </div>
                  </div>
                </template>
              </div>
            </template>

            {{-- ANALOGY --}}
            <template x-if="slide.type === 'analogy'">
              <div class="mx-auto flex max-w-2xl flex-col items-center py-4 text-center">
                <div x-show="slide.image" class="mb-4 overflow-hidden rounded-[18px] border border-glassBorder">
                  <img :src="slide.image" alt="" class="h-44 w-auto object-cover" loading="lazy" />
                </div>
                <div x-show="!slide.image" class="mb-4 grid h-32 w-32 place-items-center rounded-full bg-[radial-gradient(circle_at_35%_30%,#9b6bff,#241456)] shadow-[0_0_50px_rgba(155,107,255,0.4)]"></div>
                <h3 class="font-display text-2xl font-bold text-ink" x-text="slide.title"></h3>
                <p x-show="slide.analogy" class="mt-3 max-w-xl text-[15px] leading-relaxed text-ink/90" x-text="slide.analogy"></p>
                <p x-show="slide.caption" class="mt-3 text-sm text-muted" x-text="slide.caption"></p>
              </div>
            </template>

            {{-- CODE --}}
            <template x-if="slide.type === 'code'">
              <div class="mx-auto max-w-2xl">
                <h3 class="font-display text-2xl font-bold text-ink" x-text="slide.title"></h3>
                <p x-show="slide.summary" class="mt-2 text-[15px] leading-relaxed text-ink/90" x-text="slide.summary"></p>
                <template x-if="slide.code">
                  <div class="mt-4 overflow-hidden rounded-[14px] border border-glassBorder bg-[#06061a]">
                    <div class="flex items-center gap-2 border-b border-glassBorder px-4 py-2">
                      <span class="h-2.5 w-2.5 rounded-full bg-[#ff8aa0]"></span>
                      <span class="h-2.5 w-2.5 rounded-full bg-[#ffb86b]"></span>
                      <span class="h-2.5 w-2.5 rounded-full bg-[#7cffb2]"></span>
                      <span class="ml-2 font-mono text-xs text-muted" x-text="slide.code.language"></span>
                    </div>
                    <pre class="overflow-x-auto px-4 py-3 text-[13px] leading-relaxed text-[#5be1ff]"><code x-text="slide.code.snippet"></code></pre>
                  </div>
                </template>
              </div>
            </template>

            {{-- SUMMARY --}}
            <template x-if="slide.type === 'summary'">
              <div class="mx-auto max-w-2xl">
                <h3 class="font-display text-2xl font-bold text-ink" x-text="slide.title"></h3>
                <p x-show="slide.summary" class="mt-2 text-[15px] leading-relaxed text-ink/90" x-text="slide.summary"></p>
                <div class="mt-5 grid gap-2 sm:grid-cols-2">
                  <template x-for="(kp, k) in slide.keyPoints" :key="k">
                    <div class="flex items-start gap-2 rounded-[12px] border border-glassBorder bg-glass px-3 py-2">
                      <span class="mt-0.5 font-display font-bold text-[#5be1ff]" x-text="k + 1"></span>
                      <span class="text-sm text-ink/90" x-text="kp"></span>
                    </div>
                  </template>
                </div>
              </div>
            </template>

            {{-- QUIZ (future-ready) --}}
            <template x-if="slide.type === 'quiz'">
              <div class="mx-auto max-w-xl py-4 text-center">
                <h3 class="font-display text-2xl font-bold text-ink" x-text="slide.title"></h3>
                <div class="mt-5 rounded-[16px] border border-dashed border-glassBorder bg-glass px-6 py-8">
                  <p class="text-sm text-muted">Interactive quizzes are coming to the Studio. This slide is reserved for them.</p>
                </div>
              </div>
            </template>
          </div>
        </template>
      </div>

      <div class="flex items-center justify-between gap-3 border-t border-glassBorder px-5 py-3">
        <button type="button" @click="ig.prev()" :disabled="ig.current === 0" class="inline-flex items-center gap-1.5 rounded-full border border-glassBorder bg-glass px-3 py-1.5 text-sm text-muted transition enabled:hover:text-ink disabled:opacity-40">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m15 6-6 6 6 6"/></svg>
          Prev
        </button>
        <div class="flex flex-wrap items-center justify-center gap-1.5">
          <template x-for="(s, i) in (ig.data ? ig.data.slides : [])" :key="i">
            <button type="button" @click="ig.goto(i)" class="h-2 rounded-full transition" :class="i === ig.current ? 'w-5 bg-[#5be1ff]' : 'w-2 bg-glassBorder hover:bg-[rgba(150,170,255,0.5)]'" :aria-label="'Go to slide ' + (i + 1)"></button>
          </template>
        </div>
        <button type="button" @click="ig.next()" :disabled="ig.current === ig.slideCount - 1" class="inline-flex items-center gap-1.5 rounded-full border border-glassBorder bg-glass px-3 py-1.5 text-sm text-muted transition enabled:hover:text-ink disabled:opacity-40">
          Next
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m9 6 6 6-6 6"/></svg>
        </button>
      </div>
    </section>
    {{-- ============ ERROR ============ --}}
    <section x-show="ig.state === 'error'" class="flex flex-col items-center justify-center px-6 py-16 text-center">
      <div class="grid h-16 w-16 place-items-center rounded-full bg-[rgba(255,138,160,0.12)] text-[#ff8aa0]">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>
      </div>
      <h3 class="mt-4 font-display text-lg font-semibold text-ink">Couldn't build your infographic</h3>
      <p class="mt-2 max-w-md text-sm text-muted" x-text="ig.error"></p>
      <div class="mt-6 flex items-center gap-2">
        <button type="button" @click="ig.retry()" class="rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-5 py-2 text-sm font-semibold text-[#07142e] transition hover:-translate-y-[2px]">Try again</button>
        <button type="button" @click="ig.close()" class="rounded-full px-4 py-2 text-sm text-muted transition hover:text-ink">Close</button>
      </div>
    </section>
  </div>
</div>
