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

    @unless($isTeacherChat ?? false)
    {{-- Add sources (primary CTA) --}}
    <div class="px-3 pt-2">
      <button type="button" @click="$store.planetSources.open()" class="tl-cta flex w-full items-center justify-center gap-2 rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-3 py-2.5 text-sm font-semibold text-[#07142e] shadow-[0_10px_40px_rgba(115,182,255,0.45)] transition hover:-translate-y-[3px] hover:shadow-[0_16px_50px_rgba(115,182,255,0.6)]">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
          <path d="M12 5v14M5 12h14"/>
        </svg>
        Add from Planets
      </button>
    </div>
    @endunless

    {{-- Scrollable source list --}}
    <div class="mt-3 flex-1 space-y-4 overflow-y-auto px-3 pb-4">
      @unless($isTeacherChat ?? false)
      {{-- Group: Planet lessons (Astro reads these) --}}
      <section x-data x-show="$store.planetSources.items.length" x-cloak>
        <div class="mb-2 flex items-center justify-between gap-2 px-0.5">
          <div class="flex items-center gap-2">
            <span class="h-1.5 w-1.5 rounded-full bg-[#73b6ff]"></span>
            <h3 class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#5be1ff]">Planet lessons</h3>
          </div>
          <label class="flex cursor-pointer items-center gap-1.5 text-[11px] text-muted">
            Select all
            <input type="checkbox" class="accent-[#73b6ff]"
              :checked="$store.planetSources.items.every(i => i.active)"
              @change="$store.planetSources.items.forEach(i => i.active = $event.target.checked)">
          </label>
        </div>
        <ul class="space-y-1.5">
          <template x-for="(it, i) in $store.planetSources.items" :key="it.planet + it.module + it.lesson">
            <li class="group flex items-center gap-2.5 rounded-[12px] border border-glassBorder bg-glass px-2.5 py-2 transition hover:border-[rgba(115,182,255,0.5)]">
              <span class="grid h-8 w-8 flex-none place-items-center rounded-[9px] bg-[rgba(115,182,255,0.12)] text-[#73b6ff]">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18" transform="rotate(35 12 12)"/></svg>
              </span>
              <span class="min-w-0 flex-1">
                <span class="block truncate text-[13px] font-medium text-ink" x-text="it.label" :title="it.label"></span>
                <span class="block truncate text-[11px] text-muted" x-text="it.sub"></span>
              </span>
              <button type="button" @click="$store.planetSources.remove(i)" class="text-muted opacity-0 transition hover:text-ink focus:opacity-100 group-hover:opacity-100" title="Remove source" aria-label="Remove source">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
              </button>
              <input type="checkbox" x-model="it.active" class="accent-[#73b6ff]" title="Use this source" aria-label="Use this source">
            </li>
          </template>
        </ul>
      </section>
      @endunless

      {{-- Group: Web sources — only real results, only for extensive research --}}
      <section x-data>
        <div class="mb-2 flex items-center gap-2 px-0.5">
          <span class="h-1.5 w-1.5 rounded-full bg-[rgba(150,170,255,0.4)]"></span>
          <h3 class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#5be1ff]">Web sources · research</h3>
        </div>

        <div x-show="$store.webSources.researching" x-cloak class="mb-2 flex items-center gap-2 rounded-[12px] border border-glassBorder bg-glass px-3 py-2 text-xs text-muted">
          <span class="h-2 w-2 animate-pulse rounded-full bg-[#73b6ff]"></span> Searching the web…
        </div>

        <ul class="space-y-1.5" x-show="$store.webSources.items.length" x-cloak>
          <template x-for="(w, i) in $store.webSources.items" :key="w.url">
            <li>
              <a :href="w.url" target="_blank" rel="noopener noreferrer" class="group flex items-start gap-2.5 rounded-[12px] border border-glassBorder bg-glass px-2.5 py-2 transition hover:border-[rgba(115,182,255,0.5)]">
                <span class="grid h-6 w-6 flex-none place-items-center rounded-[8px] bg-[rgba(115,182,255,0.12)] font-mono text-[11px] text-[#73b6ff]" x-text="i + 1"></span>
                <span class="min-w-0 flex-1">
                  <span class="block truncate text-[13px] font-medium text-ink group-hover:underline" x-text="w.title" :title="w.title"></span>
                  <span class="block truncate text-[11px] text-muted" x-text="w.domain"></span>
                </span>
              </a>
            </li>
          </template>
        </ul>

        {{-- Empty state — no fake/demo sources are ever rendered. --}}
        <div x-show="!$store.webSources.items.length && !$store.webSources.researching" class="rounded-[14px] border border-dashed border-glassBorder bg-glass px-4 py-6 text-center">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2 h-8 w-8 text-muted">
            <circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>
          </svg>
          <p class="text-sm font-medium text-ink">Verified sources appear here</p>
          <p class="mx-auto mt-1 max-w-[220px] text-xs leading-relaxed text-muted">
            Quick questions are answered directly. For in-depth research, Astro cites real web pages here.
          </p>
        </div>
      </section>
    </div>
  </div>
</aside>
