{{-- COLUMN 1 — Sources (left sidebar, collapsible).
     On lg+: in-flow flex column, collapses to w-0 via `sourcesOpen`.
     On <lg: fixed off-canvas drawer, opened via `mobileDrawer === 'sources'`. --}}
<aside
  class="fixed inset-y-0 left-0 z-40 flex w-80 max-w-[88vw] flex-col border-r border-glassBorder bg-[rgba(6,6,26,0.6)] backdrop-blur-xl transition-transform duration-300 ease-out lg:relative lg:z-auto lg:max-w-none lg:translate-x-0 lg:bg-[rgba(6,6,26,0.6)] lg:transition-[width,opacity]"
  :class="(isMobile ? (mobileDrawer === 'sources' ? 'translate-x-0' : '-translate-x-full') : '') + (sourcesOpen ? ' lg:w-80 lg:opacity-100' : ' lg:w-0 lg:overflow-hidden lg:border-r-0 lg:opacity-0')"
  @keydown.escape.window="mobileDrawer === 'sources' && closeDrawer()"
  aria-label="Sources"
>
  <div class="flex h-full w-80 flex-col">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-2 px-3 pt-3">
      <h2 class="font-display text-sm font-semibold tracking-wide text-ink">Sources</h2>
      <button type="button" @click="toggleSources()" class="grid h-8 w-8 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:border-[rgba(115,182,255,0.5)] hover:text-ink" :title="(isMobile ? 'Close' : (sourcesOpen ? 'Collapse' : 'Expand')) + ' sources'">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" :class="isMobile ? 'rotate-180' : (sourcesOpen ? '' : 'rotate-180')">
          <path d="m15 6-6 6 6 6"/>
        </svg>
      </button>
    </div>

    {{-- Add sources (primary CTA) --}}
    <div class="px-3 pt-2">
      <button type="button" class="flex w-full items-center justify-center gap-2 rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-3 py-2.5 text-sm font-semibold text-[#07142e] shadow-[0_10px_40px_rgba(115,182,255,0.45)] transition hover:-translate-y-[3px] hover:shadow-[0_16px_50px_rgba(115,182,255,0.6)]">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
          <path d="M12 5v14M5 12h14"/>
        </svg>
        Add sources
      </button>
    </div>

    {{-- Search + web toggle --}}
    <div class="px-3 pt-3" x-data="{ web: false }">
      <div class="flex items-center gap-2 rounded-full border border-glassBorder bg-glass px-2.5 py-1.5 focus-within:border-[rgba(115,182,255,0.5)]">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 flex-none text-muted">
          <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
        </svg>
        <input type="text" placeholder="Search sources" class="min-w-0 flex-1 bg-transparent text-sm text-ink placeholder:text-muted/70 focus:outline-none" />
        <button
          type="button"
          @click="web = !web"
          :class="web ? 'border-[rgba(115,182,255,0.5)] bg-[rgba(115,182,255,0.18)] text-[#73b6ff]' : 'border-glassBorder text-muted hover:text-ink'"
          class="flex-none rounded-full border px-2 py-0.5 font-mono text-[11px] uppercase tracking-[0.12em] transition"
          :title="web ? 'Web search on' : 'Web search off'"
        >
          Web
        </button>
      </div>
    </div>

    {{-- Scrollable source list --}}
    <div class="mt-3 flex-1 space-y-4 overflow-y-auto px-3 pb-4">
      {{-- Group: Your uploads --}}
      <section>
        <div class="mb-2 flex items-center gap-2 px-0.5">
          <span class="h-1.5 w-1.5 rounded-full bg-[rgba(150,170,255,0.4)]"></span>
          <h3 class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#5be1ff]">Your uploads</h3>
        </div>

        {{-- Empty state — shown until the student attaches a REAL resource.
             No fake/demo attachments are ever rendered. --}}
        <div class="rounded-[14px] border border-dashed border-glassBorder bg-glass px-4 py-6 text-center">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2 h-8 w-8 text-muted">
            <path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M5 3h9l5 5v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
          </svg>
          <p class="text-sm font-medium text-ink">Saved sources will appear here</p>
          <p class="mx-auto mt-1 max-w-[220px] text-xs leading-relaxed text-muted">
            Drop in links, PDFs, or slides and Astro will read them to give you sharper, sourced answers.
          </p>
        </div>
      </section>
    </div>
  </div>
</aside>
