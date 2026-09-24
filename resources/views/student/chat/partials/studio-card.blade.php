{{-- Studio output-type card.
     Icon chip uses the landing-page treatment: blue/violet gradient chip + blue outline icon.
     $label        : card label (e.g. "Quiz")
     $iconSvg      : inner SVG paths (24x24, stroke=currentColor)
     $wire         : Alpine action string, e.g. "$store.quiz.open()" --}}
<button
  type="button"
  @click="{{ $wire ?? '' }}"
  class="group flex items-center gap-2 rounded-[14px] border border-glassBorder bg-glass p-2 text-left transition duration-200 hover:-translate-y-[3px] hover:border-[rgba(115,182,255,0.5)] hover:shadow-[0_20px_50px_rgba(10,10,40,0.5)]"
>
  <span class="grid h-9 w-9 flex-none place-items-center rounded-[14px] bg-[linear-gradient(135deg,rgba(115,182,255,0.18),rgba(155,107,255,0.18))] text-[#73b6ff]">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
      {!! $iconSvg !!}
    </svg>
  </span>
  <span class="min-w-0 flex-1 truncate font-display text-xs font-medium text-ink">{{ $label }}</span>
</button>
