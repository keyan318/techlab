{{-- A Shop item's button: Wearing / Wear (owned) / Buy for N XP / Need N more XP. Lives inside the shop() Alpine component. --}}
<template x-if="s.worn[@js($kind)] === @js($key)">
  <span class="px-btn pointer-events-none text-[#8ff0b0]">Wearing</span>
</template>
<template x-if="s.worn[@js($kind)] !== @js($key) && owns(@js($kind), @js($key))">
  <button type="button" class="px-btn px-btn-blue" :disabled="busy" @click="equip(@js($kind), @js($key))" aria-label="Wear {{ $label }}">Wear</button>
</template>
<template x-if="!owns(@js($kind), @js($key))">
  <button type="button" class="px-btn" data-sfx="off" :disabled="busy || s.xp < {{ (int) $price }}" @click="buy(@js($kind), @js($key), @js($label))"
          aria-label="Buy {{ $label }} for {{ number_format($price) }} XP"
          x-text="s.xp >= {{ (int) $price }} ? 'Buy for {{ number_format($price) }} XP' : `Need ${fmt({{ (int) $price }} - s.xp)} more XP`"></button>
</template>
