@php
  $thoughts = $thoughts ?? '';
  $answer = $answer ?? '';
@endphp

<div x-data="{ thoughts: false }" class="flex gap-3">
  {{-- Astro mascot avatar --}}
  <div class="grid h-10 w-10 flex-none place-items-center rounded-full bg-glass p-1 ring-1 ring-[rgba(115,182,255,0.4)]">
    @include('student.chat.partials.astro-mascot')
  </div>

  <div class="min-w-0 flex-1">
    <div class="mb-1 flex items-center gap-2">
      <span class="font-display text-sm font-semibold text-ink">Astro</span>
      <span class="text-[11px] text-muted">your AI teacher</span>
    </div>

    {{-- Thoughts toggle --}}
    @if($thoughts)
      <button
        type="button"
        @click="thoughts = !thoughts"
        class="mb-2 inline-flex items-center gap-1.5 rounded-full border border-glassBorder bg-glass px-2.5 py-1 font-mono text-[11px] uppercase tracking-[0.12em] text-muted transition hover:border-[rgba(115,182,255,0.5)] hover:text-ink"
        :aria-expanded="thoughts"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="h-3.5 w-3.5 transition-transform duration-200" :class="thoughts ? 'rotate-90' : ''">
          <path d="m9 6 6 6-6 6"/>
        </svg>
        Thoughts
      </button>

      <div x-show="thoughts" x-collapse x-cloak
        class="mb-3 rounded-[14px] border border-glassBorder bg-glass px-3 py-2 text-xs leading-relaxed text-muted">
        {{ $thoughts }}
      </div>
    @endif

    {{-- Final answer --}}
    <div class="text-[15px] leading-relaxed text-ink/90 [&_a]:text-[#73b6ff] [&_a]:underline [&_blockquote]:my-2 [&_blockquote]:border-l-2 [&_blockquote]:border-[#73b6ff] [&_blockquote]:bg-[rgba(115,182,255,0.08)] [&_blockquote]:py-1 [&_blockquote]:pl-3 [&_blockquote]:pr-2 [&_blockquote]:text-sm [&_code]:rounded [&_code]:bg-[rgba(115,182,255,0.12)] [&_code]:px-1 [&_code]:py-0.5 [&_code]:text-[#5be1ff] [&_h3]:mb-1 [&_h3]:mt-3 [&_h3]:font-display [&_h3]:text-base [&_h3]:font-semibold [&_li]:ml-5 [&_li]:list-disc [&_p]:mb-2 [&_strong]:font-semibold [&_strong]:text-ink [&_ul]:mb-2">
      {!! $answer !!}
    </div>

    {{-- Action row --}}
    <div
      x-data="{ saved: false, copied: false }"
      class="mt-2 flex items-center gap-1 text-muted"
    >
      <button
        type="button"
        @click="saved = !saved"
        :class="saved ? 'text-[#73b6ff]' : 'hover:text-ink'"
        class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-xs transition"
        :title="saved ? 'Saved to note' : 'Save to note'"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" :fill="saved ? 'currentColor' : 'none'">
          <path d="M12 17v5M9 10.5V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v6.5l2 6H7z"/>
        </svg>
        <span x-show="!saved">Save to note</span>
        <span x-show="saved" x-cloak>Saved</span>
      </button>

      <button
        type="button"
        @click="copied = true; setTimeout(() => copied = false, 1500)"
        class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-xs transition hover:text-ink"
        title="Copy"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
          <rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/>
        </svg>
        <span x-show="!copied">Copy</span>
        <span x-show="copied" x-cloak>Copied</span>
      </button>

      <button type="button" class="rounded-lg px-2 py-1.5 text-xs transition hover:text-[#73b6ff]" title="Good response">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
          <path d="M7 11v9H4a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1zM7 11l4-8a2 2 0 0 1 2 2v5h5a2 2 0 0 1 2 2.3l-1.2 6A2 2 0 0 1 18.8 20H7"/>
        </svg>
      </button>

      <button type="button" class="rounded-lg px-2 py-1.5 text-xs transition hover:text-[#ff8aa0]" title="Bad response">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
          <path d="M17 13V4h3a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1zM17 13l-4 8a2 2 0 0 1-2-2v-5H6a2 2 0 0 1-2-2.3l1.2-6A2 2 0 0 1 7.2 4H17"/>
        </svg>
      </button>
    </div>
  </div>
</div>
