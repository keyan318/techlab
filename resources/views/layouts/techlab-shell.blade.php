<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'TechLab')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>[x-cloak]{display:none!important}</style>
  @stack('head')
</head>
<body class="bg-void text-ink" x-data="techlabShell()" x-init="init()">

  <div class="flex h-screen w-screen overflow-hidden">

    {{-- ===== REMOTE / SIDEBAR (persistent) ===== --}}
    <aside
      class="flex flex-col border-r border-glassBorder bg-[rgba(6,6,26,0.6)] backdrop-blur-xl transition-[width] duration-200 ease-out"
      :class="collapsed ? 'w-[72px]' : 'w-64'"
    >
      <nav class="flex flex-1 flex-col gap-1 p-3">
        <a href="{{ route('student.chat') }}"
           class="flex items-center gap-3 rounded-[12px] px-3 py-2.5 transition {{ request()->routeIs('student.chat') ? 'bg-[rgba(115,182,255,0.14)] text-[#73b6ff]' : 'text-muted hover:bg-glass hover:text-ink' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5 flex-none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          <span x-show="!collapsed" x-cloak class="text-sm font-medium">Chat</span>
        </a>

        <a href="{{ route('student.dashboard') }}"
           class="flex items-center gap-3 rounded-[12px] px-3 py-2.5 transition {{ request()->routeIs('student.dashboard') ? 'bg-[rgba(115,182,255,0.14)] text-[#73b6ff]' : 'text-muted hover:bg-glass hover:text-ink' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5 flex-none"><circle cx="12" cy="12" r="9"/><path d="M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18M3 12h18"/></svg>
          <span x-show="!collapsed" x-cloak class="text-sm font-medium">Planets</span>
        </a>

        <a href="{{ route('student.crew') }}"
           class="flex items-center gap-3 rounded-[12px] px-3 py-2.5 transition {{ request()->routeIs('student.crew') ? 'bg-[rgba(115,182,255,0.14)] text-[#73b6ff]' : 'text-muted hover:bg-glass hover:text-ink' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5 flex-none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          <span x-show="!collapsed" x-cloak class="text-sm font-medium">Crew</span>
        </a>
      </nav>

      {{-- Theme + Collapse — UI controls, never navigate --}}
      <div class="border-t border-glassBorder p-3 space-y-1">
        <button type="button" @click="toggleTheme()"
                class="flex w-full items-center gap-3 rounded-[12px] px-3 py-2.5 text-muted transition hover:bg-glass hover:text-ink">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5 flex-none"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
          <span x-show="!collapsed" x-cloak class="text-sm font-medium">Theme</span>
        </button>

        <button type="button" @click="collapsed = !collapsed"
                class="flex w-full items-center gap-3 rounded-[12px] px-3 py-2.5 text-muted transition hover:bg-glass hover:text-ink">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5 flex-none transition-transform" :class="collapsed ? '' : 'rotate-180'"><path d="M9 6 15 12 9 18"/></svg>
          <span x-show="!collapsed" x-cloak class="text-sm font-medium">Collapse</span>
        </button>
      </div>
    </aside>

    {{-- ===== TV / MAIN CONTENT ===== --}}
    <main class="min-w-0 flex-1 overflow-hidden">
      @yield('channel')
    </main>

  </div>

  <script>
    function techlabShell() {
      return {
        collapsed: true, // default state
        init() {
          const saved = localStorage.getItem('techlab_sidebar_collapsed');
          this.collapsed = saved === null ? true : saved === '1';
          this.$watch('collapsed', v => localStorage.setItem('techlab_sidebar_collapsed', v ? '1' : '0'));
        },
        toggleTheme() {
          document.documentElement.classList.toggle('dark');
          // wire this to your existing theme system
        },
      };
    }
  </script>
  @stack('scripts')
</body>
</html>