{{-- The codename input with its dice and live "is it free?" note. Lives inside welcome()'s x-data. $id keeps ids unique. --}}
<div class="w-full">
  <label for="codename-{{ $id }}" class="sect">Codename</label>
  <div class="mt-3 flex items-stretch gap-3">
    <input id="codename-{{ $id }}" type="text" class="cn-input min-w-0 flex-1" x-model="codename" @input="codenameTouched = true; checkCodename()"
           maxlength="20" autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="CosmicPaws42"
           :aria-invalid="(codenameState === 'bad').toString()" aria-describedby="codename-note-{{ $id }}"
           @keydown.enter.prevent="{{ $enter ?? '' }}">
    <button type="button" class="opt flex-none px-4" @click="rollCodename()" title="Give me an idea" aria-label="Give me a codename idea">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8" cy="8" r="1.2" fill="currentColor"/><circle cx="16" cy="8" r="1.2" fill="currentColor"/><circle cx="12" cy="12" r="1.2" fill="currentColor"/><circle cx="8" cy="16" r="1.2" fill="currentColor"/><circle cx="16" cy="16" r="1.2" fill="currentColor"/>
      </svg>
    </button>
  </div>
  <p id="codename-note-{{ $id }}" class="mt-2 min-h-[1.4em] text-[14px]" aria-live="polite"
     :class="{ 'text-[color:var(--px-good)]': codenameState === 'ok', 'text-[#e0405a]': codenameState === 'bad', 'text-muted': codenameState === 'checking' || codenameState === 'idle' }"
     x-text="codenameNote"></p>
</div>
