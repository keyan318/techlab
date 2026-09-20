{{-- Infographic Studio — in-/chat overlay viewer.
  Two overlays: (1) a small style picker (Anime / Cartoon) opened from the
  Studio card — generation then runs in the background with a progress card in
  the Studio panel; (2) the finished poster viewer (with PNG download). It renders ONE poster (title, illustrated sections,
  takeaways) drawn on a fixed 1280x720 stage that is scaled to fit its
  container, so nothing scrolls or overflows at any window size.
  States from the global `infographic` Alpine store: ready | error (generating shows in the Studio panel).
  Model text is rendered with x-text (escaped) so nothing injected can execute. --}}
<div
  x-data="{ ig: $store.infographic || {} }"
  x-show="ig.isOpen"
  x-cloak
  @keydown.escape.window="ig.isOpen && ig.close()"
  class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
  role="dialog"
  aria-modal="true"
  aria-label="Astro infographic"
>
  <div class="absolute inset-0 bg-[rgba(3,3,15,0.74)]" @click="ig.close()"></div>

  <div class="relative flex max-h-[94vh] w-fit max-w-full flex-col overflow-hidden rounded-[24px] bg-white shadow-[0_40px_120px_rgba(0,0,0,0.6)]">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-4 px-5 py-3 sm:px-6">
      <div class="min-w-0">
        <h2 class="truncate font-display text-lg font-semibold text-slate-900" x-text="ig.state === 'ready' && ig.data ? ig.data.title : 'Infographic'"></h2>
      </div>
      <div class="flex items-center gap-2">
        <span x-show="ig.downloadError" x-cloak class="text-xs text-rose-500" x-text="ig.downloadError"></span>
        <button type="button" x-show="ig.state === 'ready'" @click="ig.newOne()" class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50">New style</button>
        <button type="button" x-show="ig.state === 'ready'" @click="ig.download()" :disabled="ig.downloading" class="inline-flex items-center gap-1.5 rounded-full bg-slate-900 px-3.5 py-1.5 text-xs font-semibold !text-white transition hover:bg-slate-700 disabled:opacity-60">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M12 4v11M7 11l5 5 5-5M5 20h14"/></svg>
          <span x-text="ig.downloading ? 'Saving…' : 'Download'"></span>
        </button>
        <button type="button" @click="ig.close()" class="grid h-9 w-9 place-items-center rounded-full text-slate-600 transition hover:bg-slate-100" aria-label="Close infographic">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>
    </div>

    {{-- Stage: 16:9, never taller than the viewport allows, poster scales to fill it --}}
    <div
      x-data="{ scale: 1, fit() { this.scale = this.$el.clientWidth / 1280 } }"
      x-init="new ResizeObserver(() => fit()).observe($el)"
      class="relative mx-4 mb-4 overflow-hidden rounded-[18px] sm:mx-6 sm:mb-6"
      style="aspect-ratio:16/9; width:min(calc(100vw - 4rem), 72rem, calc((94vh - 6rem) * 16 / 9));"
    >
      {{-- ERROR --}}
      <div x-show="ig.state === 'error'" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 px-6 text-center">
        <div class="grid h-14 w-14 place-items-center rounded-full bg-rose-100 text-rose-500">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>
        </div>
        <h3 class="mt-3 font-display text-base font-semibold text-slate-900" x-text="ig.errorKind === 'empty_source' ? 'Nothing to visualize yet' : 'Couldn\'t build your infographic'"></h3>
        <p class="mt-1 max-w-md text-sm text-slate-600" x-text="ig.error"></p>
        <div class="mt-4 flex items-center gap-2">
          <button type="button" x-show="ig.errorKind !== 'empty_source'" @click="ig.retry()" class="rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold !text-white transition hover:bg-slate-700">Try again</button>
          <button type="button" @click="ig.close()" class="rounded-full px-4 py-2 text-sm text-slate-600 transition hover:text-slate-900">Close</button>
        </div>
      </div>

      {{-- READY: the poster --}}
      <div x-show="ig.state === 'ready' && ig.data" class="absolute inset-0">
        <div
          id="infographic-poster"
          class="absolute left-0 top-0 flex origin-top-left flex-col bg-[linear-gradient(135deg,#fff4e8,#e8f4ff_55%,#efe8ff)] px-[56px] py-[34px] text-slate-900"
          :style="`width:1280px;height:720px;transform:scale(${scale})`"
          x-data="{ colors: ['#f97316', '#0ea5e9', '#8b5cf6', '#10b981'] }"
        >
          {{-- Title --}}
          <div class="text-center">
            <h1 class="font-display text-[46px] font-extrabold leading-[1.1] tracking-tight text-slate-900" x-text="ig.data ? ig.data.title : ''"></h1>
            <p class="mx-auto mt-2 max-w-[960px] text-[20px] leading-snug text-slate-600" x-text="ig.data ? ig.data.subtitle : ''"></p>
          </div>

          {{-- Sections (flow, left to right) --}}
          <div class="relative mt-5 flex flex-1 items-start justify-center gap-[28px]">
            <div class="pointer-events-none absolute left-[6%] right-[6%] top-[92px] h-[14px] rounded-full opacity-70"
                 style="background:linear-gradient(90deg,#f97316,#0ea5e9,#8b5cf6,#10b981)"></div>
            <template x-for="(sec, i) in (ig.data ? ig.data.sections : [])" :key="i">
              <div class="relative z-10 flex flex-1 flex-col items-center text-center">
                <div class="grid h-[190px] w-[190px] flex-none place-items-center overflow-hidden rounded-full bg-white shadow-[0_12px_32px_rgba(15,23,42,0.16)]"
                     :style="`border:6px solid ${colors[i % 4]}`">
                  <img x-show="sec.image" :src="sec.image" alt="" class="h-full w-full object-cover" />
                  <span x-show="!sec.image" class="font-display text-[64px] font-extrabold" :style="`color:${colors[i % 4]}`" x-text="i + 1"></span>
                </div>
                <span class="-mt-3 rounded-full px-4 py-1 text-[14px] font-bold text-white shadow" :style="`background:${colors[i % 4]}`" x-text="sec.label || ('Step ' + (i + 1))"></span>
                <h3 class="mt-2 font-display text-[24px] font-bold leading-tight text-slate-900" x-text="sec.heading"></h3>
                <p class="mt-1 text-[16px] leading-snug text-slate-600" x-text="sec.detail"></p>
                <ul x-show="sec.points && sec.points.length" class="mt-1.5 space-y-0.5">
                  <template x-for="(pt, k) in sec.points" :key="k">
                    <li class="text-[14px] font-medium text-slate-700" x-text="'• ' + pt"></li>
                  </template>
                </ul>
              </div>
            </template>
          </div>

          {{-- Takeaways --}}
          <div x-show="ig.data && ig.data.takeaways && ig.data.takeaways.length" class="mt-3">
            <p class="mb-2 font-display text-[20px] font-bold text-slate-900">Key takeaways</p>
            <div class="grid grid-cols-3 gap-[20px]">
              <template x-for="(t, i) in (ig.data ? ig.data.takeaways : [])" :key="i">
                <div class="flex items-start gap-3 rounded-[16px] bg-white/75 px-4 py-3 shadow-[0_4px_14px_rgba(15,23,42,0.08)]">
                  <span class="grid h-[32px] w-[32px] flex-none place-items-center rounded-full text-[16px] font-bold text-white" :style="`background:${colors[i % 4]}`" x-text="i + 1"></span>
                  <div class="min-w-0">
                    <p class="text-[17px] font-bold leading-tight text-slate-900" x-text="t.heading"></p>
                    <p class="mt-0.5 text-[14px] leading-snug text-slate-600" x-text="t.detail"></p>
                  </div>
                </div>
              </template>
            </div>
          </div>

          <p class="mt-2 text-right text-[11px] font-medium text-slate-400">Made by Astro · TechLab</p>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Style picker (opened from the Studio "Infographic" card) --}}
<div
  x-data="{ ig: $store.infographic || {} }"
  x-show="ig.configOpen"
  x-cloak
  @keydown.escape.window="ig.configOpen && ig.closeConfig()"
  class="fixed inset-0 z-50 flex items-center justify-center p-4"
  role="dialog"
  aria-modal="true"
  aria-label="Create infographic"
>
  <div class="absolute inset-0 bg-[rgba(3,3,15,0.74)]" @click="ig.closeConfig()"></div>
  <div class="relative w-full max-w-md overflow-hidden rounded-[24px] border border-glassBorder bg-[rgba(10,8,30,0.97)] shadow-[0_40px_120px_rgba(0,0,0,0.6)]">
    <div class="flex items-center justify-between gap-2 border-b border-glassBorder px-5 py-4">
      <div>
        <h2 class="font-display text-base font-semibold text-ink">Infographic</h2>
        <p class="text-xs text-muted">Turn your chat with Astro into a picture lesson.</p>
      </div>
      <button type="button" @click="ig.closeConfig()" class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="px-5 py-5">
      <p class="mb-2 font-mono text-[11px] uppercase tracking-[0.16em] text-[#5be1ff]">Art style</p>
      <div class="grid grid-cols-2 gap-3">
        <template x-for="st in ig.styles" :key="st.id">
          <button type="button" @click="ig.style = st.id"
            class="rounded-[14px] border p-3 text-left transition"
            :class="ig.style === st.id ? 'border-[#73b6ff] bg-[rgba(115,182,255,0.12)]' : 'border-glassBorder bg-glass hover:border-[rgba(115,182,255,0.4)]'"
            :aria-pressed="ig.style === st.id">
            <span class="block text-sm font-semibold text-ink" x-text="st.label"></span>
            <span class="mt-0.5 block text-xs leading-snug text-muted" x-text="st.hint"></span>
          </button>
        </template>
      </div>
      <p x-show="(ig.transcript || '').trim() === ''" x-cloak class="mt-4 text-xs text-[#ffcf85]">Chat with Astro first — add a few messages so there is something to visualize.</p>
      <p class="mt-4 text-xs leading-relaxed text-muted">It builds in the background — keep chatting, and it'll appear in the Studio panel when ready.</p>
      <div class="mt-5 flex items-center justify-between gap-3">
        <button type="button" @click="ig.closeConfig()" class="rounded-full border border-glassBorder bg-glass px-4 py-2 text-sm text-muted transition hover:text-ink">Cancel</button>
        <button type="button" @click="ig.generate()" :disabled="(ig.transcript || '').trim() === ''"
          class="rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-6 py-2.5 text-sm font-semibold text-[#07142e] shadow-[0_10px_40px_rgba(115,182,255,0.4)] transition hover:-translate-y-[2px] disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0">Generate</button>
      </div>
    </div>
  </div>
</div>
