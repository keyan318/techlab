{{-- Studio output-type card.
     Icon chip uses the landing-page treatment: blue/violet gradient chip + blue outline icon.
     $label        : card label (e.g. "Quiz")
     $iconSvg      : inner SVG paths (24x24, stroke=currentColor)
     $wire         : Alpine action string for standard cards (e.g., "$store.quiz.open()")
     $customAction : if true, this card handles its own click (PPT/Slide deck)
     $conversationId: conversation ID passed for custom actions --}}
<button
  type="button"
  @click="{{ $customAction ?? false ? 'generateDeck($event)' : ($wire ?? '') }}"
  :class="{
    'opacity-50 cursor-not-allowed': {{ ($customAction ?? false) ? 'generatingDeck' : 'false' }},
  }"
  :disabled="{{ $customAction ?? false ? 'generatingDeck' : 'false' }}"
  class="group flex items-center gap-3 rounded-[14px] border border-glassBorder bg-glass p-3 text-left transition duration-200 hover:-translate-y-[3px] hover:border-[rgba(115,182,255,0.5)] hover:shadow-[0_20px_50px_rgba(10,10,40,0.5)]"
  x-data="studioCard({ isPpt: {{ ($customAction ?? false) ? 'true' : 'false' }} })"
>
  <span class="grid h-12 w-12 flex-none place-items-center rounded-[14px] bg-[linear-gradient(135deg,rgba(115,182,255,0.18),rgba(155,107,255,0.18))] text-[#73b6ff]">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
      {!! $iconSvg !!}
    </svg>
  </span>
  <span class="flex-1 font-display text-sm font-medium text-ink">{{ $label }}</span>
  <template x-if="isPpt">
    <span x-show="!generatingDeck" class="flex items-center gap-1.5">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 flex-none text-muted transition group-hover:translate-x-0.5 group-hover:text-ink">
        <path d="m9 6 6 6-6 6"/>
      </svg>
    </span>
    <span x-show="generatingDeck" class="flex items-center gap-1.5 text-blue">
      <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
      </svg>
      <span class="text-xs font-medium">Generating...</span>
    </span>
    <span x-show="deckError" class="flex items-center gap-1.5 text-red-400" x-transition:enter="transition-opacity duration-200" x-transition:leave="transition-opacity duration-200">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
        <circle cx="12" cy="12" r="10"/>
        <path d="M12 8v4M12 16h.01"/>
      </svg>
      <span class="text-xs" x-text="deckErrorMsg"></span>
    </span>
  </template>
  <template x-if="!isPpt">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 flex-none text-muted transition group-hover:translate-x-0.5 group-hover:text-ink">
      <path d="m9 6 6 6-6 6"/>
    </svg>
  </template>
</button>

<script>
  function studioCard({ isPpt, conversationId }) {
    if (!isPpt) return {};

    return {
      isPpt: true,
      generatingDeck: false,
      deckError: false,
      deckErrorMsg: '',

      async generateDeck(event) {
        if (this.generatingDeck) return;
        const convId = this.$store.conversation?.currentId;
        if (!convId) {
          this.deckError = true;
          this.deckErrorMsg = 'Start a chat first';
          setTimeout(() => { this.deckError = false; }, 3000);
          return;
        }

        this.generatingDeck = true;
        this.deckError = false;
        this.deckErrorMsg = '';

        const csrf = document.querySelector('meta[name="csrf-token"]')
          ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          : '';

        try {
          const res = await fetch(`/conversations/${convId}/generate-deck`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf,
              'X-Requested-With': 'XMLHttpRequest',
            },
          });

          const j = await res.json().catch(() => ({}));

          if (!res.ok || !j.deck_id) {
            this.deckError = true;
            this.deckErrorMsg = j.error || 'Generation failed';
            this.generatingDeck = false;
            setTimeout(() => { this.deckError = false; }, 4000);
            return;
          }

          // Success — open deck in new tab
          window.open(`/decks/${j.deck_id}`, '_blank', 'noopener,noreferrer');

        } catch (e) {
          this.deckError = true;
          this.deckErrorMsg = 'Connection failed';
        } finally {
          this.generatingDeck = false;
        }
      },
    };
  }
</script>
