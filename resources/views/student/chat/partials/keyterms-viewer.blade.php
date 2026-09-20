{{-- Key Terms Studio — /chat overlay viewer, driven by the `keyterms` Alpine store.
  States: reading · error. (Generation runs in the background; the Studio panel shows progress.)
  Definitions are escaped, then only balanced **bold** is turned into <strong> (keyterms.fmt). --}}
<div
  x-data="{ kt: $store.keyterms }"
  x-show="kt.isOpen"
  x-cloak
  @keydown.escape.window="kt.isOpen && kt.close()"
  class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
  role="dialog"
  aria-modal="true"
  aria-label="Key terms"
>
  <div class="absolute inset-0 bg-[rgba(3,3,15,0.74)]" @click="kt.close()"></div>

  <div class="relative flex max-h-[94vh] w-full max-w-2xl flex-col overflow-hidden rounded-[24px] border border-glassBorder bg-[rgba(10,8,30,0.97)] shadow-[0_40px_120px_rgba(0,0,0,0.6)]">

    {{-- ============ READING ============ --}}
    <section x-show="kt.state === 'reading' && kt.data" class="flex min-h-0 flex-1 flex-col">
      <div class="flex items-center justify-between gap-2 border-b border-glassBorder px-5 py-4 sm:px-6">
        <div class="flex min-w-0 items-center gap-3">
          <span class="grid h-10 w-10 flex-none place-items-center rounded-[12px] bg-[linear-gradient(135deg,rgba(115,182,255,0.18),rgba(155,107,255,0.18))] text-[#73b6ff] ring-1 ring-[rgba(115,182,255,0.22)]">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M4 5h9a3 3 0 0 1 3 3v11H7a3 3 0 0 1-3-3z"/><path d="M9 10h4M9 14h3"/><path d="M18 8v11"/></svg>
          </span>
          <div class="min-w-0">
            <h2 class="truncate font-display text-base font-semibold text-ink" x-text="kt.data ? kt.data.title : 'Key terms'"></h2>
            <p class="truncate text-xs text-muted" x-text="kt.data ? ((kt.data.topic ? kt.data.topic + ' · ' : '') + kt.data.terms.length + ' terms') : ''"></p>
          </div>
        </div>
        <button type="button" @click="kt.close()" class="grid h-9 w-9 flex-none place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Close key terms">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="min-h-0 flex-1 space-y-3 overflow-y-auto px-5 py-5 sm:px-6">
        <template x-for="t in (kt.data ? kt.data.terms : [])" :key="t.id">
          <article class="rounded-[16px] border border-glassBorder bg-glass p-4">
            <h3 class="font-display text-base font-semibold text-[#73b6ff]" x-text="t.term"></h3>
            <p class="mt-1.5 text-sm leading-relaxed text-muted" x-html="kt.fmt(t.definition)"></p>
            <p class="mt-2.5 flex gap-2 rounded-[12px] bg-[rgba(115,182,255,0.08)] px-3 py-2 text-sm leading-relaxed text-ink">
              <span aria-hidden="true">💡</span>
              <span x-text="t.analogy"></span>
            </p>
          </article>
        </template>
      </div>

      <div class="flex items-center justify-between gap-3 border-t border-glassBorder px-5 py-3">
        <button type="button" @click="kt.again()" class="rounded-full border border-glassBorder bg-glass px-4 py-1.5 text-sm text-muted transition hover:text-ink">Pick new terms</button>
        <button type="button" @click="kt.close()" class="rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-5 py-1.5 text-sm font-semibold text-[#07142e] transition hover:-translate-y-[1px]">Done</button>
      </div>
    </section>

    {{-- ============ ERROR ============ --}}
    <section x-show="kt.state === 'error'" class="flex flex-col items-center justify-center px-6 py-16 text-center">
      <div class="grid h-16 w-16 place-items-center rounded-full bg-[rgba(255,138,160,0.12)] text-[#ff8aa0]">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>
      </div>
      <h3 class="mt-4 font-display text-lg font-semibold text-ink">Couldn't pick your key terms</h3>
      <p class="mt-2 max-w-md text-sm text-muted" x-text="kt.error"></p>
      <p x-show="kt.errorKind === 'insufficient_content'" x-cloak class="mt-2 max-w-md text-xs text-[#ffcf85]">Tip: chat a bit more with Astro about the topic first, then try again.</p>
      <div class="mt-6 flex items-center gap-2">
        <button type="button" @click="kt.again()" :class="kt.errorKind === 'insufficient_content' ? 'hidden' : ''" class="rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-5 py-2 text-sm font-semibold text-[#07142e] transition hover:-translate-y-[2px]">Try again</button>
        <button type="button" @click="kt.close()" class="rounded-full px-4 py-2 text-sm text-muted transition hover:text-ink">Close</button>
      </div>
    </section>

  </div>
</div>
