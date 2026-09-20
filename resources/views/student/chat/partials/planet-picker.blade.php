{{-- "Add from Planets" picker: Planet -> Modules -> Lessons.
     State lives in Alpine.store('planetSources') (chat.blade.php). --}}
<div x-data x-show="$store.planetSources.pickerOpen" x-cloak
     @keydown.escape.window="$store.planetSources.close()"
     class="fixed inset-0 z-50 grid place-items-center p-4">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$store.planetSources.close()"></div>

  <div class="relative flex max-h-[85vh] w-full max-w-lg flex-col overflow-hidden rounded-[20px] border border-glassBorder bg-[rgba(10,8,38,0.92)] shadow-[0_30px_80px_rgba(10,10,40,0.7)] backdrop-blur-xl"
       role="dialog" aria-modal="true" aria-label="Add from Planets">

    <div class="flex items-center gap-2 border-b border-glassBorder px-4 py-3">
      <button type="button" x-show="$store.planetSources.step === 'lessons'" @click="$store.planetSources.step = 'planets'"
              class="grid h-8 w-8 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted hover:text-ink" aria-label="Back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m15 6-6 6 6 6"/></svg>
      </button>
      <h2 class="font-display text-base font-semibold text-ink"
          x-text="$store.planetSources.step === 'planets' ? 'Add from Planets' : $store.planetSources.planet.title"></h2>
      <button type="button" @click="$store.planetSources.close()" class="ml-auto text-muted hover:text-ink" aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-5 w-5"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
    </div>

    {{-- Step 1: choose a Planet --}}
    <div x-show="$store.planetSources.step === 'planets'" class="space-y-2 overflow-y-auto p-4">
      <p class="pb-1 text-sm text-muted">Pick a Planet, then choose the lessons Astro should read.</p>
      <template x-for="p in $store.planetSources.catalog" :key="p.slug">
        <button type="button" @click="$store.planetSources.choose(p.slug)"
                class="flex w-full items-center gap-3 rounded-[14px] border border-glassBorder bg-glass px-4 py-3 text-left transition hover:border-[rgba(115,182,255,0.5)] hover:bg-[rgba(115,182,255,0.08)]">
          <span class="min-w-0 flex-1">
            <span class="block font-display text-sm font-semibold text-ink" x-text="p.title"></span>
            <span class="block text-xs text-muted"
                  x-text="p.modules.length ? p.modules.length + ' modules · ' + p.modules.reduce((n, m) => n + m.lessons.length, 0) + ' lessons' : 'Lessons coming soon'"></span>
          </span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-muted"><path d="m9 6 6 6-6 6"/></svg>
        </button>
      </template>
    </div>

    {{-- Step 2: modules + lessons --}}
    <div x-show="$store.planetSources.step === 'lessons'" class="flex-1 space-y-3 overflow-y-auto p-4">
      <p x-show="!$store.planetSources.planet || !$store.planetSources.planet.modules.length" class="py-6 text-center text-sm text-muted">
        No lessons are available for this Planet yet.
      </p>
      <template x-for="m in ($store.planetSources.planet ? $store.planetSources.planet.modules : [])" :key="m.key">
        <section>
          <h3 class="mb-1.5 font-mono text-[10px] uppercase tracking-[0.18em] text-[#5be1ff]" x-text="m.title"></h3>
          <ul class="space-y-1">
            <template x-for="l in m.lessons" :key="l.key">
              <li>
                <label class="flex cursor-pointer items-center gap-2.5 rounded-[10px] px-2.5 py-2 text-sm text-ink transition hover:bg-glass"
                       :class="$store.planetSources.has($store.planetSources.planet.slug, m.key, l.key) ? 'opacity-50' : ''">
                  <input type="checkbox" class="accent-[#73b6ff]"
                         :disabled="$store.planetSources.has($store.planetSources.planet.slug, m.key, l.key)"
                         :checked="$store.planetSources.inDraft(m.key, l.key) || $store.planetSources.has($store.planetSources.planet.slug, m.key, l.key)"
                         @change="$store.planetSources.toggleDraft(m.key, l.key)">
                  <span class="min-w-0 flex-1 truncate" x-text="l.title"></span>
                  <span x-show="$store.planetSources.has($store.planetSources.planet.slug, m.key, l.key)" class="text-[11px] text-muted">Added</span>
                </label>
              </li>
            </template>
          </ul>
        </section>
      </template>
    </div>

    <div x-show="$store.planetSources.step === 'lessons'" class="flex items-center justify-between gap-3 border-t border-glassBorder px-4 py-3">
      <span class="text-xs text-muted" x-text="$store.planetSources.draft.length + ' selected'"></span>
      <button type="button" @click="$store.planetSources.commit()" :disabled="!$store.planetSources.draft.length"
              class="rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] px-4 py-2 text-sm font-semibold text-[#07142e] transition disabled:cursor-not-allowed disabled:opacity-40">
        Add to Astro
      </button>
    </div>
  </div>
</div>
