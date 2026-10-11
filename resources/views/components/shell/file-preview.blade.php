{{-- Google Classroom-style "click a file, preview it right here" overlay.
  Any page can trigger it: $store.filePreview.open({ name, kind, viewUrl, downloadUrl, googleUrl, googleEmbedUrl }).
  kind is the badge/extension group from ModuleMaterial::KINDS (pdf, docx, pptx, xlsx, img, txt, zip). --}}
<div
  x-data
  x-show="$store.filePreview.isOpen"
  x-cloak
  @keydown.escape.window="$store.filePreview.isOpen && $store.filePreview.close()"
  class="fixed inset-0 z-[70] flex items-center justify-center p-3 sm:p-6"
  role="dialog"
  aria-modal="true"
  :aria-label="$store.filePreview.name"
>
  <div class="absolute inset-0 bg-[rgba(3,3,15,0.78)]" @click="$store.filePreview.close()"></div>

  <div class="relative flex h-[92vh] max-h-[94vh] w-full max-w-5xl flex-col overflow-hidden rounded-[20px] border border-glassBorder bg-[rgba(10,8,30,0.98)] shadow-[0_40px_120px_rgba(0,0,0,0.6)]">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-3 border-b border-glassBorder px-4 py-3 sm:px-6">
      <div class="flex min-w-0 items-center gap-3">
        <span class="grid h-9 w-9 flex-none place-items-center rounded-[8px] bg-glass font-mono text-[10px] font-bold uppercase text-[#73b6ff]" x-text="$store.filePreview.kind"></span>
        <p class="min-w-0 truncate text-[15px] font-medium text-ink" x-text="$store.filePreview.name"></p>
      </div>
      <div class="flex flex-none items-center gap-2">
        <a x-show="$store.filePreview.googleUrl" x-cloak :href="$store.filePreview.googleUrl" target="_blank" rel="noopener noreferrer"
           class="hidden items-center gap-2 rounded-full border border-glassBorder bg-glass px-3 py-1.5 text-xs font-medium text-ink transition hover:bg-white/10 sm:flex">
          <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"/><path d="M14 3v6h6"/></svg>
          Open with Google Docs
        </a>
        <a x-show="$store.filePreview.downloadUrl" x-cloak :href="$store.filePreview.downloadUrl" download
           class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Download">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 21h16"/></svg>
        </a>
        <button type="button" @click="$store.filePreview.close()" class="grid h-9 w-9 place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:text-ink" aria-label="Close">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>
    </div>

    {{-- Body --}}
    <div class="min-h-0 flex-1 bg-[#0c0a24]">
      <template x-if="$store.filePreview.kind === 'pdf'">
        <iframe :src="$store.filePreview.viewUrl" class="h-full w-full" :title="$store.filePreview.name"></iframe>
      </template>

      <template x-if="$store.filePreview.kind === 'img'">
        <div class="flex h-full items-center justify-center overflow-auto p-4">
          <img :src="$store.filePreview.viewUrl" :alt="$store.filePreview.name" class="max-h-full max-w-full rounded-[10px] object-contain">
        </div>
      </template>

      <template x-if="['docx', 'pptx', 'xlsx'].includes($store.filePreview.kind) && $store.filePreview.googleEmbedUrl">
        <div class="flex h-full flex-col">
          <iframe :src="$store.filePreview.googleEmbedUrl" class="h-full w-full" :title="$store.filePreview.name"></iframe>
          <p class="border-t border-glassBorder px-6 py-2 text-center text-xs text-muted">Preview blank or slow? Open with Google Docs or Download above works every time.</p>
        </div>
      </template>

      <template x-if="!['pdf', 'img'].includes($store.filePreview.kind) && !(['docx', 'pptx', 'xlsx'].includes($store.filePreview.kind) && $store.filePreview.googleEmbedUrl)">
        <div class="flex h-full flex-col items-center justify-center gap-3 px-6 text-center">
          <span class="grid h-16 w-16 place-items-center rounded-full bg-glass font-mono text-xs font-bold uppercase text-[#73b6ff]" x-text="$store.filePreview.kind"></span>
          <p class="text-sm text-muted">No inline preview for this file type.<br>Use Download above to view it.</p>
        </div>
      </template>
    </div>
  </div>
</div>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.store('filePreview', {
      isOpen: false,
      name: '',
      kind: '',
      viewUrl: '',
      downloadUrl: '',
      googleUrl: '',
      googleEmbedUrl: '',
      open(file) {
        this.name = file.name || '';
        this.kind = file.kind || '';
        this.viewUrl = file.viewUrl || '';
        this.downloadUrl = file.downloadUrl || '';
        this.googleUrl = file.googleUrl || '';
        this.googleEmbedUrl = file.googleEmbedUrl || '';
        this.isOpen = true;
      },
      close() {
        this.isOpen = false;
      },
    });
  });
</script>
