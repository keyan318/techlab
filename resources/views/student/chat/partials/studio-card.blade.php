{{-- Studio output-type card.
     Icon chip uses the landing-page treatment: blue/violet gradient chip + blue outline icon.
     $label  : card label (e.g. "Quiz")
     $iconSvg: inner SVG paths (24x24, stroke=currentColor) --}}
<button
  type="button"
  @click="{{ $wire ?? '' }}"
  class="group flex items-center gap-3 rounded-[14px] border border-glassBorder bg-glass p-3 text-left transition duration-200 hover:-translate-y-[3px] hover:border-[rgba(115,182,255,0.5)] hover:shadow-[0_20px_50px_rgba(10,10,40,0.5)]"
>
  <span class="grid h-12 w-12 flex-none place-items-center rounded-[14px] bg-[linear-gradient(135deg,rgba(115,182,255,0.18),rgba(155,107,255,0.18))] text-[#73b6ff]">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
      {!! $iconSvg !!}
    </svg>
  </span>
  <span class="flex-1 font-display text-sm font-medium text-ink">{{ $label }}</span>
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 flex-none text-muted transition group-hover:translate-x-0.5 group-hover:text-ink">
    <path d="m9 6 6 6-6 6"/>
  </svg>
</button>
