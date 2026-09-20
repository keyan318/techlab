{{-- COLUMN 3 — Studio (right sidebar, collapsible).
     On lg+: in-flow flex column, collapses to w-0 via `studioOpen`.
     On <lg: fixed off-canvas drawer, opened via `mobileDrawer === 'studio'`. --}}
<aside
  class="fixed inset-y-0 right-0 z-40 flex w-80 max-w-[88vw] flex-col border-l border-glassBorder bg-[rgba(6,6,26,0.6)] backdrop-blur-xl transition-transform duration-300 ease-out lg:relative lg:z-auto lg:max-w-none lg:translate-x-0 lg:bg-[rgba(6,6,26,0.6)] lg:transition-[width,opacity]"
  :class="(isMobile ? (mobileDrawer === 'studio' ? 'translate-x-0' : 'translate-x-full') : '') + (studioOpen ? ' lg:w-80 lg:opacity-100' : ' lg:w-0 lg:overflow-hidden lg:border-l-0 lg:opacity-0')"
  @keydown.escape.window="mobileDrawer === 'studio' && closeDrawer()"
  aria-label="Studio"
>
  <div class="flex h-full w-80 flex-col" x-data="studioNote()">
    {{-- Header --}}
    <div x-show="!noteOpen" class="flex items-center justify-between gap-2 px-3 pt-3">
      <h2 class="font-display text-sm font-semibold tracking-wide text-ink">Studio</h2>
      <button type="button" @click="toggleStudio()" class="grid h-8 w-8 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:border-[rgba(115,182,255,0.5)] hover:text-ink" :title="(isMobile ? 'Close' : (studioOpen ? 'Collapse' : 'Expand')) + ' studio'">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" :class="isMobile ? '' : (studioOpen ? 'rotate-180' : '')">
          <path d="m9 6 6 6-6 6"/>
        </svg>
      </button>
    </div>

    {{-- Notification-permission card (dismissible) --}}
    <div x-show="!notifyDismissed && !noteOpen" x-cloak class="mx-3 mt-3 rounded-[14px] border border-[rgba(115,182,255,0.35)] bg-[rgba(115,182,255,0.08)] p-3">
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
    <div x-show="!noteOpen" class="mt-3 flex-1 overflow-y-auto px-3 pb-20">
      @if($isTeacherChat ?? false)
      <p class="px-0.5 pb-2 text-xs leading-relaxed text-muted">
        Tell Astro your topic and the look you want (for example “dark and bold, for 14-year-olds”), then tap PPT.
      </p>

      {{-- PPT: builds a designed .pptx from this chat (POST /teacher/chat/ppt) --}}
      <div x-data="{
             busy: false, msg: '', ok: false,
             async make() {
               const id = $store.conversation.currentId;
               this.ok = false;
               if (!id) { this.msg = 'Chat with Astro first — tell it the topic and the look you want.'; return; }
               this.busy = true; this.msg = '';
               try {
                 const csrf = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
                 const res = await fetch('{{ route('teacher.chat.ppt') }}', {
                   method: 'POST',
                   headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                   body: JSON.stringify({ conversation_id: id }),
                 });
                 if (!res.ok) {
                   const j = await res.json().catch(() => ({}));
                   this.msg = j.error || 'Astro couldn\'t build the presentation just now. Please try again.';
                   return;
                 }
                 const blob = await res.blob();
                 const name = (/filename=\W*([\w.-]+)/.exec(res.headers.get('Content-Disposition') || '') || [])[1] || 'presentation.pptx';
                 const url = URL.createObjectURL(blob);
                 const a = document.createElement('a'); a.href = url; a.download = name; document.body.appendChild(a); a.click(); a.remove();
                 setTimeout(() => URL.revokeObjectURL(url), 10000);
                 this.ok = true;
                 this.msg = 'Your ' + (res.headers.get('X-Ppt-Slides') || '') + '-slide presentation was downloaded.';
               } catch (e) {
                 this.msg = 'Something went wrong. Check your connection and try again.';
               } finally { this.busy = false; }
             }
           }">
        <button type="button" @click="make()" :disabled="busy" :aria-busy="busy.toString()"
          class="group flex w-full items-center gap-3 rounded-[14px] border border-glassBorder bg-glass p-3 text-left transition duration-200 hover:-translate-y-[3px] hover:border-[rgba(115,182,255,0.5)] hover:shadow-[0_20px_50px_rgba(10,10,40,0.5)] disabled:cursor-wait disabled:opacity-70 disabled:hover:translate-y-0">
          <span class="grid h-12 w-12 flex-none place-items-center rounded-[14px] bg-[linear-gradient(135deg,rgba(115,182,255,0.18),rgba(155,107,255,0.18))] text-[#73b6ff]">
            <svg x-show="!busy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M12 17v4M8 21h8M9 9h2M9 13h2"/></svg>
            <svg x-show="busy" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 animate-spin"><path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/></svg>
          </span>
          <span class="min-w-0 flex-1">
            <span class="block font-display text-sm font-medium text-ink" x-text="busy ? 'Designing your slides…' : 'PPT'">PPT</span>
            <span class="block truncate text-xs text-muted" x-text="busy ? 'this can take up to a minute' : 'Presentation from this chat'">Presentation from this chat</span>
          </span>
        </button>
        <p x-show="msg" x-cloak x-text="msg" role="status" class="mt-2 px-1 text-xs leading-relaxed" :class="ok ? 'text-[#7cffb2]' : 'text-[#ff8aa0]'"></p>
      </div>
      @else
      <p class="px-0.5 pb-2 text-xs leading-relaxed text-muted">
        Turn what you're learning into study-ready outputs.
      </p>

      {{-- 2-column grid, education-flavored ordering (Quiz / Flashcards / Mind Map first) --}}
      <div class="grid grid-cols-2 gap-2">
        @include('student.chat.partials.studio-card', ['label' => 'Quiz', 'wire' => "\$store.quiz.open()", 'iconSvg' => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 0 1 4.5 1.5c0 1.5-2 2-2 2.5"/><path d="M12 17h.01"/>'])
        @include('student.chat.partials.studio-card', ['label' => 'Flashcards', 'wire' => "\$store.flashcards.open()", 'iconSvg' => '<rect x="4" y="6" width="16" height="12" rx="2"/><path d="M8 10h8M8 14h5"/>'])
        @include('student.chat.partials.studio-card', ['label' => 'Key terms', 'wire' => "\$store.keyterms.open()", 'iconSvg' => '<path d="M4 5h9a3 3 0 0 1 3 3v11H7a3 3 0 0 1-3-3z"/><path d="M9 10h4M9 14h3"/><path d="M18 8v11"/>'])
        @include('student.chat.partials.studio-card', ['label' => 'Infographic', 'wire' => '$store.infographic.open()', 'iconSvg' => '<path d="M4 20V4M4 20h16M8 16v-5M12 16V8M16 16v-8"/>'])
        @include('student.chat.partials.studio-card', ['label' => 'Reports', 'wire' => '$store.reports.open()', 'iconSvg' => '<path d="M6 2h8l4 4v16H6z"/><path d="M14 2v4h4M9 12h6M9 16h6"/>'])

      </div>

      {{-- Quiz progress / ready card (generation runs in the background) --}}
      <div x-data="{ qz: $store.quiz }" x-show="qz.generating || qz.ready" x-cloak class="mt-3">
        <button type="button" @click="!qz.generating && qz.open()" :disabled="qz.generating"
          class="flex w-full items-center gap-3 rounded-[14px] border border-glassBorder bg-[linear-gradient(135deg,rgba(115,182,255,0.10),rgba(155,107,255,0.06))] p-3 text-left">
          <span class="grid h-9 w-9 flex-none place-items-center" :class="qz.generating ? 'text-[#5be1ff]' : (qz.quiz ? 'text-[#7cffb2]' : 'text-[#ff8aa0]')">
            <svg x-show="qz.generating" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 animate-spin"><path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/></svg>
            <svg x-show="!qz.generating && qz.quiz" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="m5 12 5 5L20 7"/></svg>
            <svg x-show="!qz.generating && !qz.quiz" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
          </span>
          <span class="min-w-0 flex-1">
            <span class="block text-sm font-medium text-ink" x-text="qz.generating ? 'Generating quiz…' : (qz.quiz ? 'Quiz ready' : 'Quiz failed')"></span>
            <span class="block truncate text-xs text-muted" x-text="qz.generating ? ('based on this chat · ' + qz.count + ' questions') : (qz.quiz ? ((qz.quiz.title || 'Tap to start') + ' · tap to start') : 'Tap to see why')"></span>
          </span>
        </button>
      </div>

      {{-- Flashcards progress / ready card (generation runs in the background) --}}
      <div x-data="{ fc: $store.flashcards }" x-show="fc.generating || fc.ready" x-cloak class="mt-3">
        <button type="button" @click="!fc.generating && fc.open()" :disabled="fc.generating"
          class="flex w-full items-center gap-3 rounded-[14px] border border-glassBorder bg-[linear-gradient(135deg,rgba(115,182,255,0.10),rgba(155,107,255,0.06))] p-3 text-left">
          <span class="grid h-9 w-9 flex-none place-items-center" :class="fc.generating ? 'text-[#5be1ff]' : (fc.deck ? 'text-[#7cffb2]' : 'text-[#ff8aa0]')">
            <svg x-show="fc.generating" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 animate-spin"><path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/></svg>
            <svg x-show="!fc.generating && fc.deck" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="m5 12 5 5L20 7"/></svg>
            <svg x-show="!fc.generating && !fc.deck" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
          </span>
          <span class="min-w-0 flex-1">
            <span class="block text-sm font-medium text-ink" x-text="fc.generating ? 'Generating flashcards…' : (fc.deck ? 'Flashcards ready' : 'Flashcards failed')"></span>
            <span class="block truncate text-xs text-muted" x-text="fc.generating ? ('based on this chat · ' + fc.count + ' cards') : (fc.deck ? ((fc.deck.title || 'Tap to study') + ' · tap to study') : 'Tap to see why')"></span>
          </span>
        </button>
      </div>

      {{-- Key terms progress / ready card (generation runs in the background) --}}
      <div x-data="{ kt: $store.keyterms }" x-show="kt.generating || kt.ready" x-cloak class="mt-3">
        <button type="button" @click="!kt.generating && kt.open()" :disabled="kt.generating"
          class="flex w-full items-center gap-3 rounded-[14px] border border-glassBorder bg-[linear-gradient(135deg,rgba(115,182,255,0.10),rgba(155,107,255,0.06))] p-3 text-left">
          <span class="grid h-9 w-9 flex-none place-items-center" :class="kt.generating ? 'text-[#5be1ff]' : (kt.data ? 'text-[#7cffb2]' : 'text-[#ff8aa0]')">
            <svg x-show="kt.generating" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 animate-spin"><path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/></svg>
            <svg x-show="!kt.generating && kt.data" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="m5 12 5 5L20 7"/></svg>
            <svg x-show="!kt.generating && !kt.data" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
          </span>
          <span class="min-w-0 flex-1">
            <span class="block text-sm font-medium text-ink" x-text="kt.generating ? 'Picking key terms…' : (kt.data ? 'Key terms ready' : 'Key terms failed')"></span>
            <span class="block truncate text-xs text-muted" x-text="kt.generating ? 'based on this chat' : (kt.data ? ((kt.data.title || 'Tap to read') + ' · tap to read') : 'Tap to see why')"></span>
          </span>
        </button>
      </div>

      {{-- Infographic progress / ready card (generation runs in the background) --}}
      <div x-data="{ ig: $store.infographic }" x-show="ig.generating || ig.ready" x-cloak class="mt-3">
        <button type="button" @click="!ig.generating && ig.open()" :disabled="ig.generating"
          class="flex w-full items-center gap-3 rounded-[14px] border border-glassBorder bg-[linear-gradient(135deg,rgba(115,182,255,0.10),rgba(155,107,255,0.06))] p-3 text-left">
          <span class="grid h-9 w-9 flex-none place-items-center" :class="ig.generating ? 'text-[#5be1ff]' : (ig.state === 'ready' && ig.data ? 'text-[#7cffb2]' : 'text-[#ff8aa0]')">
            <svg x-show="ig.generating" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 animate-spin"><path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/></svg>
            <svg x-show="!ig.generating && ig.state === 'ready' && ig.data" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="m5 12 5 5L20 7"/></svg>
            <svg x-show="!ig.generating && !(ig.state === 'ready' && ig.data)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
          </span>
          <span class="min-w-0 flex-1">
            <span class="block text-sm font-medium text-ink" x-text="ig.generating ? 'Generating infographic…' : (ig.state === 'ready' && ig.data ? 'Infographic ready' : 'Infographic failed')"></span>
            <span class="block truncate text-xs text-muted" x-text="ig.generating ? (ig.sourceCount ? 'based on ' + ig.sourceCount + ' source' + (ig.sourceCount === 1 ? '' : 's') : 'based on this chat') : (ig.state === 'ready' && ig.data ? ((ig.data.title || 'Tap to view') + ' · tap to view') : 'Tap to see why')"></span>
          </span>
        </button>
      </div>

      {{-- Report progress / ready card (generation runs in the background) --}}
      <div x-data="{ rp: $store.reports }" x-show="rp.generating || rp.ready" x-cloak class="mt-3">
        <button type="button" @click="!rp.generating && rp.open()" :disabled="rp.generating"
          class="flex w-full items-center gap-3 rounded-[14px] border border-glassBorder bg-[linear-gradient(135deg,rgba(115,182,255,0.10),rgba(155,107,255,0.06))] p-3 text-left">
          <span class="grid h-9 w-9 flex-none place-items-center" :class="rp.generating ? 'text-[#5be1ff]' : (rp.state !== 'error' && rp.report ? 'text-[#7cffb2]' : 'text-[#ff8aa0]')">
            <svg x-show="rp.generating" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 animate-spin"><path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/></svg>
            <svg x-show="!rp.generating && rp.state !== 'error' && rp.report" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="m5 12 5 5L20 7"/></svg>
            <svg x-show="!rp.generating && (rp.state === 'error' || !rp.report)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
          </span>
          <span class="min-w-0 flex-1">
            <span class="block text-sm font-medium text-ink" x-text="rp.generating ? 'Writing report…' : (rp.state !== 'error' && rp.report ? 'Report ready' : 'Report failed')"></span>
            <span class="block truncate text-xs text-muted" x-text="rp.generating ? 'based on this chat' : (rp.state !== 'error' && rp.report ? ((rp.report.title || 'Tap to read') + ' · tap to read') : 'Tap to see why')"></span>
          </span>
        </button>
      </div>
      @endif

      {{-- Divider --}}
      <div class="my-4 border-t border-glassBorder"></div>

      {{-- Saved outputs --}}
      <div x-data="{ so: $store.studioOutputs }" x-show="so.items.length" x-cloak class="space-y-2">
        <p class="px-0.5 font-mono text-[11px] uppercase tracking-[0.16em] text-[#5be1ff]">Saved outputs</p>
        <template x-for="item in so.items" :key="item.id">
          <div class="group flex items-center gap-2 rounded-[14px] border border-glassBorder bg-glass p-2.5 transition hover:border-[rgba(115,182,255,0.5)]">
            <button type="button" @click="item.type === 'note' ? openNote(item) : (item.type === 'flashcards' ? $store.flashcards.openSaved(item) : (item.type === 'keyterms' ? $store.keyterms.openSaved(item) : (item.type === 'infographic' ? $store.infographic.openSaved(item) : $store.reports.openSaved(item))))" class="flex min-w-0 flex-1 items-center gap-3 text-left">
              <span class="grid h-9 w-9 flex-none place-items-center rounded-[10px] bg-[linear-gradient(135deg,rgba(115,182,255,0.18),rgba(155,107,255,0.18))] text-[#73b6ff]">
                <svg x-show="item.type === 'flashcards'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><rect x="4" y="6" width="16" height="12" rx="2"/><path d="M8 10h8M8 14h5"/></svg>
                <svg x-show="item.type === 'keyterms'" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M4 5h9a3 3 0 0 1 3 3v11H7a3 3 0 0 1-3-3z"/><path d="M9 10h4M9 14h3"/><path d="M18 8v11"/></svg>
                <svg x-show="item.type === 'note'" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M4 4h16v12l-4 4H4z"/><path d="M16 20v-4h4M8 9h8M8 13h4"/></svg>
                <svg x-show="item.type === 'infographic'" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M4 20V4M4 20h16M8 16v-5M12 16V8M16 16v-8"/></svg>
                <svg x-show="item.type === 'report'" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M6 2h8l4 4v16H6z"/><path d="M14 2v4h4M9 12h6M9 16h6"/></svg>
              </span>
              <span class="min-w-0">
                <span class="block truncate text-sm font-medium text-ink" x-text="item.title"></span>
                <span class="block truncate text-xs text-muted" x-text="(item.type === 'note' ? 'Note · ' : (item.type === 'report' ? 'Report · ' : (item.type === 'infographic' ? 'Infographic · ' : (item.type === 'keyterms' ? 'Key terms · ' : 'Flashcards · ')))) + item.subtitle"></span>
              </span>
            </button>
            <button type="button" @click="so.remove(item.id)" class="grid h-7 w-7 flex-none place-items-center rounded-full text-muted opacity-0 transition hover:text-ink group-hover:opacity-100 focus:opacity-100" aria-label="Remove saved output" title="Remove">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
          </div>
        </template>
      </div>

      {{-- Empty state --}}
      <div x-data x-show="!$store.studioOutputs.items.length" class="rounded-[14px] border border-dashed border-glassBorder bg-glass px-4 py-6 text-center">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2 h-8 w-8 text-muted">
          <path d="M3 7l9-4 9 4-9 4z"/><path d="M3 7v10l9 4 9-4V7"/><path d="M12 11v10"/>
        </svg>
        <p class="text-sm font-medium text-ink">Studio output will be saved here</p>
        <p class="mx-auto mt-1 max-w-[220px] text-xs leading-relaxed text-muted">
          Generate a quiz, flashcards, key terms or report and it'll land here so you can revisit it anytime.
        </p>
      </div>
    </div>

    {{-- Add note — fixed bottom-right of the panel --}}
    <button
      type="button"
      @click="newNote()"
      x-show="(studioOpen || mobileDrawer === 'studio') && !noteOpen"
      class="studio-add-note absolute bottom-4 right-4 z-20 flex items-center gap-2 rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-4 py-2.5 text-sm font-semibold text-[#07142e] shadow-[0_10px_40px_rgba(115,182,255,0.45)] transition hover:-translate-y-[3px] hover:shadow-[0_16px_50px_rgba(115,182,255,0.6)]"
    >
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M4 4h16v12l-4 4H4z"/><path d="M16 20v-4h4M8 9h8M8 13h4"/></svg>
      Add note
    </button>

    {{-- Note editor (replaces the Studio body while open) --}}
    <div x-show="noteOpen" x-cloak class="flex h-full min-h-0 flex-col">
      {{-- Breadcrumb --}}
      <div class="flex items-center justify-between gap-2 border-b border-glassBorder px-3 py-3">
        <div class="flex items-center gap-1.5 text-sm">
          <button type="button" @click="closeNote()" class="text-muted transition hover:text-ink">Studio</button>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 text-muted"><path d="m9 6 6 6-6 6"/></svg>
          <span class="font-medium text-ink">Note</span>
        </div>
        <button type="button" @click="closeNote()" class="grid h-8 w-8 place-items-center rounded-[10px] text-muted transition hover:bg-glass hover:text-ink" title="Close note" aria-label="Close note">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M4 14h6v6M20 10h-6V4M14 10l7-7M3 21l7-7"/></svg>
        </button>
      </div>

      {{-- Title + delete --}}
      <div class="flex items-center gap-2 border-b border-glassBorder px-3 py-3">
        <input type="text" x-model="noteTitle" @input="save()" maxlength="120" placeholder="New note" aria-label="Note title"
          class="min-w-0 flex-1 border-0 bg-transparent p-0 font-display text-xl text-ink placeholder:text-muted focus:outline-none focus:ring-0">
        <button type="button" @click="deleteNote()" class="grid h-8 w-8 flex-none place-items-center rounded-[10px] text-muted transition hover:bg-glass hover:text-[#ff8aa0]" title="Delete note" aria-label="Delete note">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
        </button>
      </div>

      {{-- Toolbar --}}
      <div class="relative flex items-center gap-0.5 border-b border-glassBorder px-2 py-2 text-muted">
        <button type="button" @mousedown.prevent @click="cmd('undo')" class="grid h-8 w-8 place-items-center rounded-lg transition hover:bg-glass hover:text-ink" title="Undo" aria-label="Undo">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-[18px] w-[18px]"><path d="M9 14 4 9l5-5"/><path d="M4 9h10a6 6 0 0 1 0 12h-3"/></svg>
        </button>
        <button type="button" @mousedown.prevent @click="cmd('redo')" class="grid h-8 w-8 place-items-center rounded-lg transition hover:bg-glass hover:text-ink" title="Redo" aria-label="Redo">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-[18px] w-[18px]"><path d="m15 14 5-5-5-5"/><path d="M20 9H10a6 6 0 0 0 0 12h3"/></svg>
        </button>
        <span class="mx-1 h-5 w-px bg-glassBorder"></span>
        <div class="relative">
          <button type="button" @mousedown.prevent @click="blockMenu = !blockMenu; moreMenu = false" class="flex h-8 items-center gap-1 rounded-lg px-2 text-sm text-ink transition hover:bg-glass" aria-haspopup="listbox">
            <span x-text="blockLabel"></span>
            <svg viewBox="0 0 24 24" fill="currentColor" class="h-3 w-3"><path d="M7 10l5 5 5-5z"/></svg>
          </button>
          <div x-show="blockMenu" x-cloak @click.outside="blockMenu = false" class="absolute left-0 top-9 z-30 w-36 overflow-hidden rounded-xl border border-glassBorder bg-[rgba(14,16,44,0.92)] py-1 shadow-xl backdrop-blur-xl">
            <template x-for="b in blocks" :key="b.tag">
              <button type="button" @mousedown.prevent @click="setBlock(b)" class="block w-full px-3 py-1.5 text-left text-sm text-ink transition hover:bg-glass" x-text="b.label"></button>
            </template>
          </div>
        </div>
        <span class="mx-1 h-5 w-px bg-glassBorder"></span>
        <button type="button" @mousedown.prevent @click="cmd('bold')" :class="fmt.bold ? 'bg-glass text-ink' : ''" class="grid h-8 w-8 place-items-center rounded-lg text-sm font-bold transition hover:bg-glass hover:text-ink" title="Bold" aria-label="Bold">B</button>
        <button type="button" @mousedown.prevent @click="cmd('italic')" :class="fmt.italic ? 'bg-glass text-ink' : ''" class="grid h-8 w-8 place-items-center rounded-lg font-serif text-sm font-semibold italic transition hover:bg-glass hover:text-ink" title="Italic" aria-label="Italic">I</button>
        <span class="mx-1 h-5 w-px bg-glassBorder"></span>
        <div class="relative">
          <button type="button" @mousedown.prevent @click="moreMenu = !moreMenu; blockMenu = false" class="grid h-8 w-8 place-items-center rounded-lg transition hover:bg-glass hover:text-ink" title="More" aria-label="More formatting">
            <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><circle cx="5" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="19" cy="12" r="1.8"/></svg>
          </button>
          <div x-show="moreMenu" x-cloak @click.outside="moreMenu = false" class="absolute right-0 top-9 z-30 w-40 overflow-hidden rounded-xl border border-glassBorder bg-[rgba(14,16,44,0.92)] py-1 shadow-xl backdrop-blur-xl">
            <button type="button" @mousedown.prevent @click="cmd('underline'); moreMenu = false" class="block w-full px-3 py-1.5 text-left text-sm text-ink transition hover:bg-glass">Underline</button>
            <button type="button" @mousedown.prevent @click="cmd('insertUnorderedList'); moreMenu = false" class="block w-full px-3 py-1.5 text-left text-sm text-ink transition hover:bg-glass">Bulleted list</button>
            <button type="button" @mousedown.prevent @click="cmd('insertOrderedList'); moreMenu = false" class="block w-full px-3 py-1.5 text-left text-sm text-ink transition hover:bg-glass">Numbered list</button>
            <button type="button" @mousedown.prevent @click="cmd('removeFormat'); moreMenu = false" class="block w-full px-3 py-1.5 text-left text-sm text-ink transition hover:bg-glass">Clear formatting</button>
          </div>
        </div>
      </div>

      {{-- Editable body --}}
      <div
        x-ref="noteBody" contenteditable="true" role="textbox" aria-multiline="true" aria-label="Note body"
        @input="save(); syncFmt()" @keyup="syncFmt()" @mouseup="syncFmt()"
        @paste.prevent="document.execCommand('insertText', false, ($event.clipboardData || window.clipboardData).getData('text/plain'))"
        class="min-h-0 flex-1 overflow-y-auto px-3 py-3 text-sm leading-relaxed text-ink focus:outline-none [&_blockquote]:border-l-2 [&_blockquote]:border-glassBorder [&_blockquote]:pl-3 [&_blockquote]:text-muted [&_h2]:text-lg [&_h2]:font-semibold [&_h3]:text-base [&_h3]:font-semibold [&_ol]:list-decimal [&_ol]:pl-5 [&_ul]:list-disc [&_ul]:pl-5"
      ></div>

      {{-- Footer: commit the note to Saved outputs and return to Studio --}}
      <div class="border-t border-glassBorder p-3">
        <button type="button" @click="addNote()"
          class="studio-add-note flex w-full items-center justify-center gap-2 rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-4 py-2.5 text-sm font-semibold text-[#07142e] shadow-[0_10px_40px_rgba(115,182,255,0.45)] transition hover:-translate-y-[3px] hover:shadow-[0_16px_50px_rgba(115,182,255,0.6)]">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M4 4h16v12l-4 4H4z"/><path d="M16 20v-4h4M8 9h8M8 13h4"/></svg>
          Save note
        </button>
      </div>
    </div>
  </div>
</aside>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('studioNote', () => ({
      noteOpen: false,
      noteId: null,
      noteTitle: '',
      blockMenu: false,
      moreMenu: false,
      blockLabel: 'Normal',
      fmt: { bold: false, italic: false },
      blocks: [
        { label: 'Normal', tag: 'p' },
        { label: 'Heading', tag: 'h2' },
        { label: 'Subheading', tag: 'h3' },
        { label: 'Quote', tag: 'blockquote' },
      ],
      _t: null,

      newNote() { this.noteId = 'note-' + Date.now(); this.noteTitle = ''; this._show(''); },
      openNote(item) { this.noteId = item.id; this.noteTitle = item.title === 'Untitled note' ? '' : item.title; this._show(item.html || ''); },
      _show(html) {
        this.noteOpen = true; this.blockMenu = this.moreMenu = false; this.blockLabel = 'Normal';
        this.$nextTick(() => { this.$refs.noteBody.innerHTML = html; this.$refs.noteBody.focus(); });
      },
      closeNote() { this.flush(); this.noteOpen = false; },
      addNote() { this.closeNote(); },   // flush() saves it to Saved outputs (empty notes are dropped)
      deleteNote() { clearTimeout(this._t); Alpine.store('studioOutputs').remove(this.noteId); this.noteOpen = false; },

      cmd(name) { this.$refs.noteBody.focus(); document.execCommand(name, false, null); this.save(); this.syncFmt(); },
      setBlock(b) {
        this.$refs.noteBody.focus();
        document.execCommand('formatBlock', false, b.tag);
        this.blockMenu = false; this.save(); this.syncFmt();
      },
      syncFmt() {
        try {
          this.fmt.bold = document.queryCommandState('bold');
          this.fmt.italic = document.queryCommandState('italic');
          const v = (document.queryCommandValue('formatBlock') || '').toLowerCase();
          this.blockLabel = (this.blocks.find(b => b.tag === v) || this.blocks[0]).label;
        } catch (e) {}
      },

      save() { clearTimeout(this._t); this._t = setTimeout(() => this.flush(), 400); },
      flush() {
        clearTimeout(this._t);
        if (!this.noteOpen || !this.noteId) return;
        const el = this.$refs.noteBody;
        const text = (el.innerText || '').trim();
        const title = this.noteTitle.trim();
        const store = Alpine.store('studioOutputs');
        if (!title && !text) { store.remove(this.noteId); return; }   // never keep an empty note
        store.add({
          id: this.noteId, type: 'note',
          title: title || 'Untitled note',
          subtitle: text ? text.slice(0, 60) : 'Empty note',
          html: el.innerHTML,
        });
      },
    }));
  });
</script>
