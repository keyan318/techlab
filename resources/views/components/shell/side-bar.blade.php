{{--
  resources/views/components/shell/sidebar.blade.php
  The persistent TechLab remote/sidebar.
  Rendered inside layouts/techlab-shell.blade.php.
  Expects the parent x-data="techlabShell()" to be live on <body>.
  No props needed — reads collapsed + active route via request()->routeIs().
--}}

<aside
  class="relative z-10 flex flex-col border-r border-glassBorder
         bg-[rgba(6,6,26,0.72)] backdrop-blur-xl
         transition-[width] duration-200 ease-out flex-shrink-0"
  :class="collapsed ? 'w-[68px]' : 'w-60'"
>

  {{-- ── Logo mark ── --}}
  <div class="flex items-center gap-3 px-4 py-5 border-b border-glassBorder/50">
    {{-- Geometric abstract mark: nested rhombus lines --}}
    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"
         class="h-7 w-7 flex-none">
      <polygon points="16,2 30,10 30,22 16,30 2,22 2,10"
               stroke="url(#sg)" stroke-width="1.5" fill="none"/>
      <polygon points="16,8 24,13 24,19 16,24 8,19 8,13"
               stroke="url(#sg2)" stroke-width="1.5" fill="none" opacity="0.7"/>
      <circle cx="16" cy="16" r="2.5" fill="url(#sg)" opacity="0.9"/>
      <defs>
        <linearGradient id="sg" x1="2" y1="2" x2="30" y2="30" gradientUnits="userSpaceOnUse">
          <stop offset="0%" stop-color="#9b6bff"/>
          <stop offset="100%" stop-color="#5be1ff"/>
        </linearGradient>
        <linearGradient id="sg2" x1="8" y1="8" x2="24" y2="24" gradientUnits="userSpaceOnUse">
          <stop offset="0%" stop-color="#73b6ff"/>
          <stop offset="100%" stop-color="#9b6bff"/>
        </linearGradient>
      </defs>
    </svg>
    <span
      x-show="!collapsed" x-cloak
      class="font-display text-sm font-700 tracking-tight text-ink leading-none"
      style="font-weight:700"
    >TechLab</span>
  </div>

  {{-- ── Primary nav ── --}}
  <nav class="flex flex-1 flex-col gap-0.5 p-2.5 pt-3">

    {{-- Chat --}}
    <a href="{{ route('student.chat') }}"
       title="Chat"
       class="group flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 transition-colors duration-150
              {{ request()->routeIs('student.chat')
                   ? 'bg-[rgba(115,182,255,0.13)] text-[#73b6ff]'
                   : 'text-muted hover:bg-[rgba(123,142,220,0.07)] hover:text-ink' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
           stroke-linecap="round" stroke-linejoin="round"
           class="h-[18px] w-[18px] flex-none">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
      <span x-show="!collapsed" x-cloak class="text-[13px] font-medium leading-none">Chat</span>
      {{-- Active pip when collapsed --}}
      @if(request()->routeIs('student.chat'))
        <span x-show="collapsed" x-cloak
              class="absolute left-[62px] h-1.5 w-1.5 rounded-full bg-[#73b6ff]"></span>
      @endif
    </a>

    {{-- Planets / Dashboard --}}
    <a href="{{ route('student.dashboard') }}"
       title="Planets"
       class="group flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 transition-colors duration-150
              {{ request()->routeIs('student.dashboard')
                   ? 'bg-[rgba(155,107,255,0.13)] text-[#9b6bff]'
                   : 'text-muted hover:bg-[rgba(123,142,220,0.07)] hover:text-ink' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
           stroke-linecap="round" stroke-linejoin="round"
           class="h-[18px] w-[18px] flex-none">
        <circle cx="12" cy="12" r="9"/>
        <path d="M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18M3 12h18"/>
      </svg>
      <span x-show="!collapsed" x-cloak class="text-[13px] font-medium leading-none">Planets</span>
    </a>

    {{-- Crew --}}
    <a href="{{ route('student.crew') }}"
       title="Crew"
       class="group flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 transition-colors duration-150
              {{ request()->routeIs('student.crew')
                   ? 'bg-[rgba(91,225,255,0.10)] text-[#5be1ff]'
                   : 'text-muted hover:bg-[rgba(123,142,220,0.07)] hover:text-ink' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
           stroke-linecap="round" stroke-linejoin="round"
           class="h-[18px] w-[18px] flex-none">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
      <span x-show="!collapsed" x-cloak class="text-[13px] font-medium leading-none">Crew</span>
    </a>

    {{-- Divider --}}
    <div class="my-2 border-t border-glassBorder/40"></div>

    {{-- Doubloon XP counter (decorative, wires to real XP later) --}}
    <div
      class="flex items-center gap-3 rounded-[10px] px-2.5 py-2 text-muted"
      title="Doubloons earned"
    >
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
           stroke-linecap="round" stroke-linejoin="round"
           class="h-[18px] w-[18px] flex-none text-[#E8B84B]">
        <circle cx="12" cy="12" r="9"/>
        <path d="M12 6v6l4 2"/>
      </svg>
      <span x-show="!collapsed" x-cloak class="text-[12px] font-mono text-[#E8B84B]">0 doubloons</span>
    </div>

  </nav>

  {{-- ── Footer controls ── --}}
  <div class="border-t border-glassBorder/50 p-2.5 space-y-0.5">

    {{-- Theme toggle --}}
    <button type="button"
            @click="toggleTheme()"
            title="Toggle theme"
            class="flex w-full items-center gap-3 rounded-[10px] px-2.5 py-2.5
                   text-muted transition-colors hover:bg-[rgba(123,142,220,0.07)] hover:text-ink">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
           stroke-linecap="round" stroke-linejoin="round"
           class="h-[18px] w-[18px] flex-none">
        <circle cx="12" cy="12" r="4"/>
        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
      </svg>
      <span x-show="!collapsed" x-cloak class="text-[13px] font-medium leading-none">Theme</span>
    </button>

    {{-- Collapse toggle --}}
    <button type="button"
            @click="collapsed = !collapsed"
            title="Toggle sidebar"
            class="flex w-full items-center gap-3 rounded-[10px] px-2.5 py-2.5
                   text-muted transition-colors hover:bg-[rgba(123,142,220,0.07)] hover:text-ink">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
           stroke-linecap="round" stroke-linejoin="round"
           class="h-[18px] w-[18px] flex-none transition-transform duration-200"
           :class="collapsed ? '' : 'rotate-180'">
        <path d="M9 6l6 6-6 6"/>
      </svg>
      <span x-show="!collapsed" x-cloak class="text-[13px] font-medium leading-none">Collapse</span>
    </button>

  </div>

</aside>