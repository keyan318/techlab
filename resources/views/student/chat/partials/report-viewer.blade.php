{{-- Reports Studio — /chat overlay viewer, driven by the `reports` Alpine store.
  States: generating · reading (TOC + one section at a time) · error.
  Section bodies are markdown rendered through marked + DOMPurify (store.bodyHtml); all other model text uses x-text. --}}
<div
  x-data="{ rp: $store.reports }"
  x-show="rp.isOpen"
  x-cloak
  @keydown.escape.window="rp.isOpen && rp.close()"
  @keydown.right.window="rp.isOpen && rp.state === 'reading' && rp.next()"
  @keydown.left.window="rp.isOpen && rp.state === 'reading' && rp.prev()"
  class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
  role="dialog"
  aria-modal="true"
  aria-label="Astro report"
>
  <div class="absolute inset-0 bg-[rgba(3,3,15,0.74)]" @click="rp.close()"></div>

  <div class="relative flex h-[90vh] max-h-[94vh] w-full flex-col overflow-hidden rounded-[24px] border border-glassBorder bg-[rgba(10,8,30,0.97)] shadow-[0_40px_120px_rgba(0,0,0,0.6)]"
       :class="rp.expanded ? 'max-w-6xl' : 'max-w-3xl'">

    {{-- ============ GENERATING ============ --}}
    <section x-show="rp.state === 'generating'" class="flex flex-1 flex-col items-center justify-center px-6 py-16 text-center">
      <div class="relative mb-8 h-24 w-24">
        <svg viewBox="0 0 100 100" class="h-full w-full animate-spin" style="animation-duration: 8s">
          <circle cx="50" cy="50" r="40" fill="none" stroke="rgba(115,182,255,0.18)" stroke-width="4"/>
          <circle cx="50" cy="50" r="40" fill="none" stroke="#73b6ff" stroke-width="4" stroke-dasharray="60 200" stroke-linecap="round"/>
        </svg>
      </div>
      <p class="font-display text-lg font-semibold text-ink">Writing your report…</p>
      <p class="mt-2 max-w-sm text-sm text-muted">Astro is turning your conversation into a study report. You can close this and keep chatting — we'll tell you when it's ready.</p>
      <button type="button" @click="rp.close()" class="mt-6 rounded-full border border-glassBorder bg-glass px-4 py-2 text-sm text-muted transition hover:text-ink">Close</button>
    </section>

    {{-- ============ ERROR ============ --}}
    <section x-show="rp.state === 'error'" class="flex flex-1 flex-col items-center justify-center px-6 py-16 text-center">
      <span class="mb-4 grid h-14 w-14 place-items-center rounded-full bg-[rgba(255,138,160,0.12)] text-[#ff8aa0]">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
      </span>
      <p class="font-display text-lg font-semibold text-ink">Couldn't write the report</p>
      <p class="mt-2 max-w-sm text-sm text-muted" x-text="rp.error"></p>
      <div class="mt-6 flex items-center gap-3">
        <button type="button" @click="rp.close()" class="rounded-full border border-glassBorder bg-glass px-4 py-2 text-sm text-muted transition hover:text-ink">Close</button>
        <button type="button" x-show="rp.errorKind !== 'insufficient_content'" @click="rp.generate()" class="rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-5 py-2 text-sm font-semibold text-[#07142e]">Try again</button>
      </div>
    </section>

    {{-- ============ READING ============ --}}
    <template x-if="rp.state === 'reading' && rp.report">
      <section class="flex min-h-0 flex-1 flex-col">
        <div class="flex items-center justify-between gap-2 border-b border-glassBorder px-4 py-3 sm:px-6">
          <div class="flex min-w-0 items-center gap-3">
            <button type="button" @click="rp.tocOpen = !rp.tocOpen" class="grid h-9 w-9 flex-none place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Toggle contents">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
            </button>
            <div class="min-w-0">
              <h2 class="truncate font-display text-base font-semibold text-ink" x-text="rp.report.title"></h2>
              <p class="truncate text-xs text-muted" x-text="rp.report.topic || 'Study report'"></p>
            </div>
          </div>
          <div class="relative flex flex-none items-center gap-2">
            <button type="button" @click="rp.share()" class="hidden rounded-full border border-glassBorder bg-glass px-3 py-1.5 text-xs text-muted transition hover:text-ink sm:inline-block" x-text="rp.copied ? 'Copied' : 'Copy'"></button>
            <button type="button" @click="rp.expanded = !rp.expanded" class="hidden h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink lg:grid" aria-label="Toggle width">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
            </button>
            <button type="button" @click="rp.menuOpen = !rp.menuOpen" class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="More">
              <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><circle cx="5" cy="12" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="19" cy="12" r="1.6"/></svg>
            </button>
            <div x-show="rp.menuOpen" x-cloak @click.outside="rp.menuOpen = false" class="absolute right-11 top-11 z-10 w-44 overflow-hidden rounded-[12px] border border-glassBorder bg-[rgba(10,8,30,0.98)] py-1 text-sm shadow-xl">
              <button type="button" @click="rp.download()" class="block w-full px-4 py-2 text-left text-ink transition hover:bg-glass">Download .md</button>
              <button type="button" @click="rp.share(); rp.menuOpen = false" class="block w-full px-4 py-2 text-left text-ink transition hover:bg-glass">Copy as Markdown</button>
              <button type="button" x-show="rp.sources.length" @click="rp.sourcesOpen = !rp.sourcesOpen; rp.menuOpen = false" class="block w-full px-4 py-2 text-left text-ink transition hover:bg-glass">Sources</button>
            </div>
            <button type="button" @click="rp.close()" class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Close">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
          </div>
        </div>

        <div class="flex min-h-0 flex-1">
          {{-- Table of contents --}}
          <nav x-show="rp.tocOpen" x-cloak class="hidden w-56 flex-none overflow-y-auto border-r border-glassBorder p-3 sm:block" aria-label="Contents">
            <p class="px-2 pb-2 font-mono text-[11px] uppercase tracking-[0.16em] text-[#5be1ff]">Contents</p>
            <template x-for="(s, i) in rp.report.sections" :key="s.id">
              <button type="button" @click="rp.go(i)"
                :class="rp.idx === i ? 'bg-glass text-ink' : 'text-muted hover:text-ink'"
                class="block w-full rounded-[10px] px-2 py-2 text-left text-sm transition">
                <span class="mr-1 font-mono text-xs opacity-60" x-text="i + 1 + '.'"></span><span x-text="s.heading"></span>
              </button>
            </template>
          </nav>

          {{-- Section --}}
          <div id="report-scroll" class="min-w-0 flex-1 overflow-y-auto px-5 py-6 sm:px-8">
            <div x-show="rp.sourcesOpen" x-cloak class="mb-5 rounded-[14px] border border-glassBorder bg-glass p-4">
              <p class="mb-2 font-mono text-[11px] uppercase tracking-[0.16em] text-[#5be1ff]">Sources</p>
              <template x-for="(src, i) in rp.sources" :key="i">
                <p class="text-sm text-muted">
                  <template x-if="src.url"><a :href="src.url" target="_blank" rel="noopener noreferrer" class="text-[#73b6ff] hover:underline" x-text="src.title"></a></template>
                  <template x-if="!src.url"><span class="text-ink" x-text="src.title"></span></template>
                  <span x-show="src.sub" x-text="' · ' + src.sub"></span>
                </p>
              </template>
            </div>

            <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-muted" x-text="'Section ' + (rp.idx + 1) + ' of ' + rp.total"></p>
            <h3 class="mt-1 font-display text-xl font-semibold text-ink" x-text="rp.section && rp.section.heading"></h3>
            <div class="prose prose-invert mt-4 max-w-none text-sm leading-relaxed text-ink [&_code]:rounded [&_code]:bg-glass [&_code]:px-1 [&_li]:ml-5 [&_li]:list-disc [&_p]:mb-3 [&_pre]:overflow-x-auto [&_pre]:rounded-[10px] [&_pre]:bg-[rgba(6,6,26,0.7)] [&_pre]:p-3" x-html="rp.bodyHtml"></div>

            <div class="mt-8 flex items-center gap-2 text-xs text-muted">
              <span>Helpful?</span>
              <button type="button" @click="rp.rating = rp.rating === 'good' ? null : 'good'" :class="rp.rating === 'good' ? 'text-[#7cffb2]' : 'hover:text-ink'" class="rounded-full border border-glassBorder bg-glass px-3 py-1 transition">Yes</button>
              <button type="button" @click="rp.rating = rp.rating === 'bad' ? null : 'bad'" :class="rp.rating === 'bad' ? 'text-[#ff8aa0]' : 'hover:text-ink'" class="rounded-full border border-glassBorder bg-glass px-3 py-1 transition">No</button>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between gap-3 border-t border-glassBorder px-4 py-3 sm:px-6">
          <button type="button" @click="rp.prev()" :disabled="rp.idx === 0" class="rounded-full border border-glassBorder bg-glass px-4 py-2 text-sm text-muted transition hover:text-ink disabled:cursor-not-allowed disabled:opacity-40">← Previous</button>
          <span class="font-mono text-xs text-muted" x-text="(rp.idx + 1) + ' / ' + rp.total"></span>
          <button type="button" @click="rp.idx === rp.total - 1 ? rp.close() : rp.next()" class="rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-5 py-2 text-sm font-semibold text-[#07142e]" x-text="rp.idx === rp.total - 1 ? 'Done' : 'Next →'"></button>
        </div>
      </section>
    </template>
  </div>
</div>
