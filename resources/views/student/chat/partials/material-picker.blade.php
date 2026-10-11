{{-- "Add from Materials" picker: Course -> Modules -> Materials.
     State lives in Alpine.store('materialSources') (chat.blade.php). --}}
<div x-data x-show="$store.materialSources.pickerOpen" x-cloak
     @keydown.escape.window="$store.materialSources.close()"
     class="fixed inset-0 z-50 grid place-items-center p-4">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$store.materialSources.close()"></div>

  <div class="relative flex max-h-[85vh] w-full max-w-lg flex-col overflow-hidden rounded-[20px] border border-glassBorder bg-[rgba(10,8,38,0.92)] shadow-[0_30px_80px_rgba(10,10,40,0.7)] backdrop-blur-xl"
       role="dialog" aria-modal="true" aria-label="Add from Materials">

    <div class="flex items-center gap-2 border-b border-glassBorder px-4 py-3">
      <button type="button" x-show="$store.materialSources.step === 'materials'" @click="$store.materialSources.step = 'courses'"
              class="grid h-8 w-8 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted hover:text-ink" aria-label="Back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m15 6-6 6 6 6"/></svg>
      </button>
      <h2 class="font-display text-base font-semibold text-ink"
          x-text="$store.materialSources.step === 'courses' ? 'Add from Materials' : $store.materialSources.course.course"></h2>
      <button type="button" @click="$store.materialSources.close()" class="ml-auto text-muted hover:text-ink" aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-5 w-5"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
    </div>

    {{-- Step 1: choose a course --}}
    <div x-show="$store.materialSources.step === 'courses'" class="space-y-2 overflow-y-auto p-4">
      <p class="pb-1 text-sm text-muted">Pick a course, then choose what your captain uploaded for Astro to read.</p>
      <p x-show="!$store.materialSources.catalog.length" class="py-6 text-center text-sm text-muted">
        No readable materials have been uploaded to your courses yet.
      </p>
      <template x-for="c in $store.materialSources.catalog" :key="c.crew_id">
        <button type="button" @click="$store.materialSources.choose(c.crew_id)"
                class="flex w-full items-center gap-3 rounded-[14px] border border-glassBorder bg-glass px-4 py-3 text-left transition hover:border-[rgba(139,216,58,0.5)] hover:bg-[rgba(139,216,58,0.08)]">
          <span class="min-w-0 flex-1">
            <span class="block font-display text-sm font-semibold text-ink" x-text="c.course"></span>
            <span class="block text-xs text-muted"
                  x-text="c.modules.reduce((n, m) => n + m.materials.length, 0) + ' material' + (c.modules.reduce((n, m) => n + m.materials.length, 0) === 1 ? '' : 's')"></span>
          </span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-muted"><path d="m9 6 6 6-6 6"/></svg>
        </button>
      </template>
    </div>

    {{-- Step 2: modules + materials --}}
    <div x-show="$store.materialSources.step === 'materials'" class="flex-1 space-y-3 overflow-y-auto p-4">
      <p x-show="!$store.materialSources.course || !$store.materialSources.course.modules.length" class="py-6 text-center text-sm text-muted">
        No materials are available for this course yet.
      </p>
      <template x-for="m in ($store.materialSources.course ? $store.materialSources.course.modules : [])" :key="m.title">
        <section>
          <h3 class="mb-1.5 font-mono text-[10px] uppercase tracking-[0.18em] text-[#8bd83a]" x-text="m.title"></h3>
          <ul class="space-y-1">
            <template x-for="mat in m.materials" :key="mat.id">
              <li>
                <label class="flex cursor-pointer items-center gap-2.5 rounded-[10px] px-2.5 py-2 text-sm text-ink transition hover:bg-glass"
                       :class="$store.materialSources.has(mat.id) ? 'opacity-50' : ''">
                  <input type="checkbox" class="accent-[#8bd83a]"
                         :disabled="$store.materialSources.has(mat.id)"
                         :checked="$store.materialSources.inDraft(mat.id) || $store.materialSources.has(mat.id)"
                         @change="$store.materialSources.toggleDraft(mat.id)">
                  <span class="min-w-0 flex-1 truncate" x-text="mat.name"></span>
                  <span x-show="$store.materialSources.has(mat.id)" class="text-[11px] text-muted">Added</span>
                </label>
              </li>
            </template>
          </ul>
        </section>
      </template>
    </div>

    <div x-show="$store.materialSources.step === 'materials'" class="flex items-center justify-between gap-3 border-t border-glassBorder px-4 py-3">
      <span class="text-xs text-muted" x-text="$store.materialSources.draft.length + ' selected'"></span>
      <button type="button" @click="$store.materialSources.commit()" :disabled="!$store.materialSources.draft.length"
              class="rounded-full bg-[linear-gradient(100deg,#8bd83a,#5be1ff_75%)] px-4 py-2 text-sm font-semibold text-[#07142e] transition disabled:cursor-not-allowed disabled:opacity-40">
        Add to Astro
      </button>
    </div>
  </div>
</div>
