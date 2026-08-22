{{-- COLUMN 3 — Studio (right sidebar, collapsible).
     On lg+: in-flow flex column, collapses to w-0 via `studioOpen`.
     On <lg: fixed off-canvas drawer, opened via `mobileDrawer === 'studio'`. --}}
<aside
  class="fixed inset-y-0 right-0 z-40 flex w-80 max-w-[88vw] flex-col border-l border-glassBorder bg-[rgba(6,6,26,0.6)] backdrop-blur-xl transition-transform duration-300 ease-out lg:relative lg:z-auto lg:max-w-none lg:translate-x-0 lg:bg-[rgba(6,6,26,0.6)] lg:transition-[width,opacity]"
  :class="(isMobile ? (mobileDrawer === 'studio' ? 'translate-x-0' : 'translate-x-full') : '') + (studioOpen ? ' lg:w-80 lg:opacity-100' : ' lg:w-0 lg:overflow-hidden lg:border-l-0 lg:opacity-0')"
  @keydown.escape.window="mobileDrawer === 'studio' && closeDrawer()"
  aria-label="Studio"
>
  <div class="flex h-full w-80 flex-col">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-2 px-3 pt-3">
      <h2 class="font-display text-sm font-semibold tracking-wide text-ink">Studio</h2>
      <button type="button" @click="toggleStudio()" class="grid h-8 w-8 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:border-[rgba(115,182,255,0.5)] hover:text-ink" :title="(isMobile ? 'Close' : (studioOpen ? 'Collapse' : 'Expand')) + ' studio'">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" :class="isMobile ? '' : (studioOpen ? 'rotate-180' : '')">
          <path d="m9 6 6 6-6 6"/>
        </svg>
      </button>
    </div>

    {{-- Notification-permission card (dismissible) --}}
    <div x-show="!notifyDismissed" x-cloak class="mx-3 mt-3 rounded-[14px] border border-[rgba(115,182,255,0.35)] bg-[rgba(115,182,255,0.08)] p-3">
      <div class="flex items-start gap-2.5">
        <svg viewBox="0 0 24 24" fill="none" stroke="#5be1ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-5 w-5 flex-none">
          <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>
        </svg>
        <div class="min-w-0 flex-1">
          <p class="text-sm font-medium text-ink">Turn on notifications</p>
          <p class="mt-0.5 text-xs leading-relaxed text-muted">Get pinged when Astro finishes generating studio output.</p>
          <div class="mt-2 flex items-center gap-2">
            <button type="button" class="rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-2.5 py-1 text-xs font-semibold text-[#07142e] transition hover:-translate-y-[3px]">Turn on</button>
            <button type="button" @click="notifyDismissed = true" class="rounded-full px-2 py-1 text-xs text-muted transition hover:text-ink">Not now</button>
          </div>
        </div>
        <button type="button" @click="notifyDismissed = true" class="text-muted transition hover:text-ink" title="Dismiss" aria-label="Dismiss">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>
    </div>

    {{-- Scrollable body --}}
    <div class="mt-3 flex-1 overflow-y-auto px-3 pb-20">
      <p class="px-0.5 pb-2 text-xs leading-relaxed text-muted">
        Turn what you're learning into study-ready outputs.
      </p>

      {{-- 2-column grid, education-flavored ordering (Quiz / Flashcards / Mind Map first) --}}
      <div class="grid grid-cols-2 gap-2">
        @include('student.chat.partials.studio-card', ['label' => 'Quiz', 'wire' => "\$store.quiz.open()", 'iconSvg' => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 0 1 4.5 1.5c0 1.5-2 2-2 2.5"/><path d="M12 17h.01"/>'])
        @include('student.chat.partials.studio-card', ['label' => 'Flashcards', 'iconSvg' => '<rect x="4" y="6" width="16" height="12" rx="2"/><path d="M8 10h8M8 14h5"/>'])
        @include('student.chat.partials.studio-card', ['label' => 'Mind Map', 'iconSvg' => '<circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="18" cy="18" r="2.5"/><path d="M8.5 12 15.5 6M8.5 12l7 6"/>'])
        @include('student.chat.partials.studio-card', ['label' => 'Audio', 'iconSvg' => '<path d="M11 5 6 9H2v6h4l5 4z"/><path d="M15.5 8.5a5 5 0 0 1 0 7"/><path d="M19 5a9 9 0 0 1 0 14"/>'])
        @include('student.chat.partials.studio-card', ['label' => 'Slide deck', 'iconSvg' => '<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M12 17v4M8 21h8M9 9h2M9 13h2"/>'])
        @include('student.chat.partials.studio-card', ['label' => 'Video', 'iconSvg' => '<rect x="3" y="5" width="14" height="14" rx="2"/><path d="m10 9 5 3-5 3z"/><path d="M21 9v6"/>'])
        @include('student.chat.partials.studio-card', ['label' => 'Infographic', 'wire' => '$store.infographic.open()', 'iconSvg' => '<path d="M4 20V4M4 20h16M8 16v-5M12 16V8M16 16v-8"/>'])
        @include('student.chat.partials.studio-card', ['label' => 'Reports', 'iconSvg' => '<path d="M6 2h8l4 4v16H6z"/><path d="M14 2v4h4M9 12h6M9 16h6"/>'])
        @include('student.chat.partials.studio-card', ['label' => 'Data table', 'iconSvg' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M3 14h18M9 4v16"/>'])
      </div>

      {{-- Divider --}}
      <div class="my-4 border-t border-glassBorder"></div>

      {{-- Empty state --}}
      <div class="rounded-[14px] border border-dashed border-glassBorder bg-glass px-4 py-6 text-center">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2 h-8 w-8 text-muted">
          <path d="M3 7l9-4 9 4-9 4z"/><path d="M3 7v10l9 4 9-4V7"/><path d="M12 11v10"/>
        </svg>
        <p class="text-sm font-medium text-ink">Studio output will be saved here</p>
        <p class="mx-auto mt-1 max-w-[220px] text-xs leading-relaxed text-muted">
          Generate a quiz or a mind map and it'll land here so you can revisit it anytime.
        </p>
      </div>
    </div>

    {{-- Add note — fixed bottom-right of the panel --}}
    <button
      type="button"
      x-show="studioOpen || mobileDrawer === 'studio'"
      class="absolute bottom-4 right-4 z-20 flex items-center gap-1.5 rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-3.5 py-2 text-sm font-semibold text-[#07142e] shadow-[0_10px_40px_rgba(115,182,255,0.45)] transition hover:-translate-y-[3px] hover:shadow-[0_16px_50px_rgba(115,182,255,0.6)]"
    >
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M12 5v14M5 12h14"/></svg>
      Add note
    </button>
  </div>
</aside>
