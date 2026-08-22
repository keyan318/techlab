{{-- Astro mascot (Byte/Bolt) — friendly astronaut, recolored to TechLab's brand
     accent (blue/violet/cyan visor), matching the landing-page helmet. No lime. --}}
<svg viewBox="0 0 64 64" fill="none" aria-hidden="true" class="h-full w-full">
  <!-- helmet dome (landing: #fdfdff -> #c9d4ff -> #8a98d8) -->
  <circle cx="32" cy="27" r="17" fill="#c9d4ff" stroke="#8a98d8" stroke-width="1.5"/>
  <!-- visor (landing: cyan -> blue -> violet) -->
  <ellipse cx="32" cy="27" rx="12" ry="11" fill="#73b6ff"/>
  <ellipse cx="32" cy="27" rx="12" ry="11" fill="none" stroke="#9b6bff" stroke-width="2"/>
  <!-- visor shine -->
  <circle cx="27" cy="22" r="3" fill="#eaeeff" opacity="0.55"/>
  <!-- antenna (violet line + cyan dot) -->
  <line x1="32" y1="10" x2="32" y2="5" stroke="#9b6bff" stroke-width="2"/>
  <circle cx="32" cy="4" r="2.2" fill="#5be1ff"/>
  <!-- body -->
  <path d="M20 44 q12 11 24 0 l2.5 14 q-14.5 8 -29 0 z" fill="#c9d4ff"/>
  <!-- chest light (blue) -->
  <circle cx="32" cy="52" r="3" fill="#73b6ff"/>
</svg>
