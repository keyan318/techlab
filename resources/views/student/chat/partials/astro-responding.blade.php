{{-- Astro's responding indicator.
     The existing TechLab logo (public/apple-touch-icon.png — the same pinwheel used as the
     app icon), brought to life while Astro works. Styles live in chat-panel.blade.php
     (.astro-resp); the words come from public/js/astro-status.js.
     Expects the `m` message object from the surrounding x-for. --}}
<div class="astro-resp" :data-state="m.status ? m.status.state : 'thinking'" role="status">
  <span class="sr-only">Astro is working on your answer</span>

  <span class="astro-logo" aria-hidden="true">
    <span class="astro-glow"><span class="astro-halo"></span></span>
    <span class="astro-orbit"></span>
    <img src="{{ asset('apple-touch-icon.png') }}" alt="" width="24" height="24" draggable="false">
  </span>

  {{-- The rotating line is decorative for assistive tech (the sr-only label above is the
       stable announcement), so it doesn't re-announce every few seconds. --}}
  <span class="astro-status" aria-hidden="true">
    <span class="astro-status-text"
          x-text="m.status ? m.status.text : 'Getting ready to help...'"
          x-effect="m.status && m.status.text; $el.classList.remove('astro-swap'); void $el.offsetWidth; $el.classList.add('astro-swap')"></span>
  </span>
</div>
