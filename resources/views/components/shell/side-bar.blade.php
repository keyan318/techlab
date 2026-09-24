{{--
  resources/views/components/shell/side-bar.blade.php
  The persistent TechLab remote/sidebar.
  Included by student/chat.blade.php and student/studentOnboarding.blade.php.
  Expects the parent x-data="techlabShell()" to be live (collapsed + toggleTheme()).

  Layout grid: every icon / the logo / the avatar is centred on the same vertical
  axis (x = 34px in the 68px collapsed rail), so collapsing never makes anything jump.
--}}

@php
  $user = auth()->user();

  if ($user) {
      $parts    = preg_split('/\s+/', trim((string) $user->name), -1, PREG_SPLIT_NO_EMPTY);
      $initials = count($parts) >= 2
          ? mb_substr($parts[0], 0, 1).mb_substr(end($parts), 0, 1)
          : mb_substr((string) $user->name, 0, 2);
      $initials  = mb_strtoupper($initials !== '' ? $initials : '?');
      $roleLabel = match ($user->role) {
          'faculty' => 'Captain',
          'admin' => 'Admin',
          default => 'Astronaut',
      };
  }

  // Faculty get Dashboard / Chat (no Planets, no Crew — "My Courses" on their
  // dashboard covers what Crew covers for students, since a faculty member can
  // now be captain of several courses at once, not just one crew). Admin gets
  // Dashboard / Course Assignment only — no Chat, Planets or Crew, none of which
  // apply to the admin role.
  $isFaculty  = $user && $user->role === 'faculty';
  $isAdmin    = $user && $user->role === 'admin';
  $homeRoute  = $isFaculty ? 'faculty.dashboard' : ($isAdmin ? 'admin.dashboard' : 'student.dashboard');
  $chatRoute  = $isFaculty ? 'faculty.chat' : 'student.chat';
@endphp

<style>
  /* ── Material + motion tokens (scoped with tl- so nothing leaks into pages) ── */
  .tl-side   { transition: width 360ms cubic-bezier(0.32, 0.72, 0, 1); }
  .tl-label  { transition: opacity 200ms ease, max-width 360ms cubic-bezier(0.32, 0.72, 0, 1),
                           margin 360ms cubic-bezier(0.32, 0.72, 0, 1), transform 360ms cubic-bezier(0.32, 0.72, 0, 1); }
  .tl-item   { transition: background-color 180ms ease, color 180ms ease,
                           transform 120ms cubic-bezier(0.2, 0.8, 0.2, 1); }
  .tl-item:active { transform: scale(0.97); }              /* feedback on pointer-down, not release */

  /* Duotone icons: a soft fill under the stroke that swells on hover / active */
  .tl-ico .duo { fill: currentColor; fill-opacity: 0; transition: fill-opacity 220ms ease; }
  .tl-item:hover .tl-ico .duo,
  .tl-item:focus-visible .tl-ico .duo      { fill-opacity: 0.12; }
  .tl-item[aria-current="page"] .tl-ico .duo,
  .tl-item[data-on="true"] .tl-ico .duo    { fill-opacity: 0.24; }

  /* Optical weight: dark translucent panel wants slightly heavier, tighter small type */
  .tl-type { font-feature-settings: "ss01", "cv11"; -webkit-font-smoothing: antialiased; }

  @media (prefers-reduced-motion: reduce) {
    .tl-side, .tl-item { transition: none !important; }
    .tl-item:active    { transform: none; }
    /* gentler equivalent: cross-fade only, no sliding/resizing choreography */
    .tl-label { transition: opacity 150ms linear !important; transform: none !important; }
  }
  @media (prefers-reduced-transparency: reduce) {
    .tl-glass { background: rgb(9, 10, 30) !important; -webkit-backdrop-filter: none !important; backdrop-filter: none !important; }
  }
  @media (prefers-contrast: more) {
    .tl-glass { border-color: rgba(255, 255, 255, 0.55) !important; }
    .tl-item  { --tl-hover: rgba(255, 255, 255, 0.16); }
  }
</style>

<aside
  class="tl-side tl-glass tl-type relative z-30 flex flex-shrink-0 flex-col
         border-r border-glassBorder bg-[rgba(6,6,26,0.72)] backdrop-blur-xl backdrop-saturate-150
         shadow-[inset_-1px_0_0_rgba(255,255,255,0.03)]"
  style="width:68px"
  :style="{ width: collapsed ? '68px' : '240px' }"
  aria-label="TechLab navigation"
>

  {{-- ── Brand ── --}}
  <a href="{{ route($homeRoute) }}"
     class="tl-item group relative mx-2.5 mt-4 mb-2 flex h-12 items-center rounded-[12px] px-[10px]
            hover:bg-[rgba(123,142,220,0.08)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]"
     aria-label="TechLab home">
    <img src="{{ asset('apple-touch-icon.png') }}" alt="" width="28" height="28" draggable="false"
         class="h-7 w-7 flex-none select-none drop-shadow-[0_0_10px_rgba(115,182,255,0.35)]">
    <span class="tl-label ml-3 max-w-0 overflow-hidden whitespace-nowrap font-display text-[16px] font-bold leading-none tracking-[-0.02em] text-ink opacity-0"
          :class="collapsed ? 'max-w-0 opacity-0 !ml-0' : '!max-w-[150px] !opacity-100'"
          :style="collapsed ? '' : 'transition-delay:90ms'">TechLab</span>
  </a>

  {{-- ── Primary nav ── --}}
  <nav class="flex flex-1 flex-col gap-1 px-2.5 pt-2" aria-label="Primary">

    {{-- Dashboard --}}
    @php $on = request()->routeIs($homeRoute); @endphp
    <a href="{{ route($homeRoute) }}"
       @if($on) aria-current="page" @endif
       aria-label="Dashboard"
       class="tl-item group relative flex h-10 items-center rounded-[11px] px-[14px]
              focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]
              {{ $on ? 'bg-[rgba(115,182,255,0.14)] text-[#73b6ff]' : 'text-muted hover:bg-[rgba(123,142,220,0.09)] hover:text-ink' }}">
      @if($on)<span class="absolute -left-2.5 top-1/2 h-4 w-[3px] -translate-y-1/2 rounded-r-full bg-[#73b6ff] shadow-[0_0_10px_rgba(115,182,255,0.7)]"></span>@endif
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
           stroke-linecap="round" stroke-linejoin="round" class="tl-ico h-5 w-5 flex-none" aria-hidden="true">
        <rect class="duo" x="3.5" y="3.5" width="7.5" height="7.5" rx="2.2"/>
        <rect class="duo" x="13" y="3.5" width="7.5" height="7.5" rx="2.2"/>
        <rect class="duo" x="3.5" y="13" width="7.5" height="7.5" rx="2.2"/>
        <rect class="duo" x="13" y="13" width="7.5" height="7.5" rx="2.2"/>
      </svg>
      <span class="tl-label ml-3 max-w-0 overflow-hidden whitespace-nowrap text-[13.5px] font-medium leading-none tracking-[-0.006em] opacity-0"
            :class="collapsed ? 'max-w-0 opacity-0 !ml-0' : '!max-w-[150px] !opacity-100'"
            :style="collapsed ? '' : 'transition-delay:90ms'">Dashboard</span>
      <span x-show="collapsed" x-cloak aria-hidden="true"
            class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 -translate-y-1/2 whitespace-nowrap rounded-lg border border-white/10 bg-[rgba(14,16,44,0.92)] px-2.5 py-1.5 text-[12px] font-medium text-ink opacity-0 shadow-[0_8px_24px_-8px_rgba(0,0,0,0.6)] transition-opacity duration-150 group-hover:opacity-100 group-hover:delay-500 group-focus-visible:opacity-100">Dashboard</span>
    </a>

    @unless($isAdmin)
    {{-- Chat --}}
    @php $on = $chatOn = request()->routeIs($chatRoute); @endphp
    <a href="{{ route($chatRoute) }}"
       @if($on) aria-current="page"
          {{-- On the chat page, Chat toggles the Recents list (and opens the rail if it is collapsed). --}}
          @click.prevent="if (collapsed) { collapsed = false; $store.recents.open = true } else { $store.recents.open = !$store.recents.open }"
          :aria-expanded="($store.recents.open && !collapsed).toString()" @endif
       aria-label="Chat"
       class="tl-item group relative flex h-10 items-center rounded-[11px] px-[14px]
              focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]
              {{ $on ? 'bg-[rgba(115,182,255,0.14)] text-[#73b6ff]' : 'text-muted hover:bg-[rgba(123,142,220,0.09)] hover:text-ink' }}">
      @if($on)<span class="absolute -left-2.5 top-1/2 h-4 w-[3px] -translate-y-1/2 rounded-r-full bg-[#73b6ff] shadow-[0_0_10px_rgba(115,182,255,0.7)]"></span>@endif
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
           stroke-linecap="round" stroke-linejoin="round" class="tl-ico h-5 w-5 flex-none" aria-hidden="true">
        <path class="duo" d="M7.5 4h9a4 4 0 0 1 4 4v4.5a4 4 0 0 1-4 4h-5l-4 3.2v-3.2a4 4 0 0 1-4-4V8a4 4 0 0 1 4-4z"/>
        <path d="M12 7.4l.85 1.9 1.9.85-1.9.85L12 12.9l-.85-1.9-1.9-.85 1.9-.85z" stroke-width="1.3"/>
      </svg>
      <span class="tl-label ml-3 max-w-0 overflow-hidden whitespace-nowrap text-[13.5px] font-medium leading-none tracking-[-0.006em] opacity-0"
            :class="collapsed ? 'max-w-0 opacity-0 !ml-0' : '!max-w-[150px] !opacity-100'"
            :style="collapsed ? '' : 'transition-delay:90ms'">Chat</span>
      <span x-show="collapsed" x-cloak aria-hidden="true"
            class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 -translate-y-1/2 whitespace-nowrap rounded-lg border border-white/10 bg-[rgba(14,16,44,0.92)] px-2.5 py-1.5 text-[12px] font-medium text-ink opacity-0 shadow-[0_8px_24px_-8px_rgba(0,0,0,0.6)] transition-opacity duration-150 group-hover:opacity-100 group-hover:delay-500 group-focus-visible:opacity-100">Chat</span>
    </a>

    @if($chatOn)
    {{-- Recents: Astro-named conversations, newest first --}}
    <div x-show="!collapsed && $store.recents.open" x-cloak
         x-transition.opacity.duration.150ms
         class="mb-1 mt-1 max-h-[38vh] overflow-y-auto pl-3 pr-0.5" aria-label="Recent conversations">
      <p x-show="!$store.recents.list.length" class="px-2.5 py-1.5 text-[12.5px] text-muted">No conversations yet.</p>
      <div x-show="$store.recents.pinned.length" class="mb-3">
      <template x-for="c in $store.recents.pinned" :key="c.id">
        <div class="group/row relative">
          <button type="button" @click="$store.recents.pick(c.id)" :title="c.title"
                  :aria-current="$store.conversation.currentId === c.id ? 'true' : null"
                  class="tl-item flex h-8 w-full items-center rounded-[9px] py-0 pl-2.5 pr-8 text-left text-[13px] tracking-[-0.006em]
                         focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]"
                  :class="$store.conversation.currentId === c.id
                    ? 'bg-[rgba(115,182,255,0.14)] text-ink'
                    : 'text-muted hover:bg-[rgba(123,142,220,0.09)] hover:text-ink'">
            <svg x-show="c.pinned" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-[15px] w-[15px] flex-none" aria-hidden="true"><path d="M7.5 4h9a4 4 0 0 1 4 4v4.5a4 4 0 0 1-4 4h-5l-4 3.2v-3.2a4 4 0 0 1-4-4V8a4 4 0 0 1 4-4z"/></svg>
            <span class="truncate" x-text="c.title || 'New chat'"></span>
          </button>
          <button type="button" @click.stop="$store.recents.openMenu(c.id, $el)" aria-label="Chat options" aria-haspopup="menu"
                  :aria-expanded="($store.recents.menu && $store.recents.menu.id === c.id).toString()"
                  class="absolute right-1 top-1/2 grid h-6 w-6 -translate-y-1/2 place-items-center rounded-md text-muted opacity-0
                         hover:bg-white/10 hover:text-ink focus-visible:opacity-100 group-hover/row:opacity-100"
                  :class="$store.recents.menu && $store.recents.menu.id === c.id ? '!opacity-100 bg-white/10 text-ink' : ''">
            <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true"><circle cx="5.5" cy="12" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="18.5" cy="12" r="1.6"/></svg>
          </button>
        </div>
      </template>
      </div>
      <p x-show="$store.recents.recent.length" class="px-2.5 pb-1 pt-1 text-[11px] font-medium uppercase tracking-[0.08em] text-muted/80">Recents</p>
      <template x-for="c in $store.recents.recent" :key="c.id">
        <div class="group/row relative">
          <button type="button" @click="$store.recents.pick(c.id)" :title="c.title"
                  :aria-current="$store.conversation.currentId === c.id ? 'true' : null"
                  class="tl-item flex h-8 w-full items-center rounded-[9px] py-0 pl-2.5 pr-8 text-left text-[13px] tracking-[-0.006em]
                         focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]"
                  :class="$store.conversation.currentId === c.id
                    ? 'bg-[rgba(115,182,255,0.14)] text-ink'
                    : 'text-muted hover:bg-[rgba(123,142,220,0.09)] hover:text-ink'">
            <svg x-show="c.pinned" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-[15px] w-[15px] flex-none" aria-hidden="true"><path d="M7.5 4h9a4 4 0 0 1 4 4v4.5a4 4 0 0 1-4 4h-5l-4 3.2v-3.2a4 4 0 0 1-4-4V8a4 4 0 0 1 4-4z"/></svg>
            <span class="truncate" x-text="c.title || 'New chat'"></span>
          </button>
          <button type="button" @click.stop="$store.recents.openMenu(c.id, $el)" aria-label="Chat options" aria-haspopup="menu"
                  :aria-expanded="($store.recents.menu && $store.recents.menu.id === c.id).toString()"
                  class="absolute right-1 top-1/2 grid h-6 w-6 -translate-y-1/2 place-items-center rounded-md text-muted opacity-0
                         hover:bg-white/10 hover:text-ink focus-visible:opacity-100 group-hover/row:opacity-100"
                  :class="$store.recents.menu && $store.recents.menu.id === c.id ? '!opacity-100 bg-white/10 text-ink' : ''">
            <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true"><circle cx="5.5" cy="12" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="18.5" cy="12" r="1.6"/></svg>
          </button>
        </div>
      </template>
    </div>

    {{-- Row menu: Pin / Delete (teleported: the rail's backdrop-filter would otherwise trap position:fixed) --}}
    <template x-teleport="body">
    <div x-show="$store.recents.menu" x-cloak x-transition.opacity.duration.120ms
         @click.outside="$store.recents.menu = null" @keydown.escape.window="$store.recents.menu = null"
         :style="$store.recents.menu ? 'left:' + $store.recents.menu.x + 'px;top:' + $store.recents.menu.y + 'px' : ''"
         role="menu" aria-label="Chat options"
         class="tl-glass fixed z-50 w-[176px] rounded-[14px] border border-white/10 bg-[rgba(14,16,44,0.92)] p-1.5 backdrop-blur-2xl
                shadow-[0_24px_60px_-14px_rgba(0,0,0,0.75),inset_0_1px_0_rgba(255,255,255,0.07)]">
      <button type="button" role="menuitem" @click="$store.recents.togglePin($store.recents.menu.id)"
              class="tl-item flex h-9 w-full items-center gap-3 rounded-[9px] px-2.5 text-[13.5px] font-medium text-ink hover:bg-white/[0.08] focus-visible:bg-white/[0.08] focus-visible:outline-none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-[17px] w-[17px] flex-none" aria-hidden="true"><path d="M14.5 4.5l5 5-3 1-3 3 .5 4-1.5 1.5-3.5-3.5L4 20.5M9.5 9.5l-1 3.5 4 4"/></svg>
        <span x-text="($store.recents.list.find(c => $store.recents.menu && c.id === $store.recents.menu.id) || {}).pinned ? 'Unpin chat' : 'Pin chat'"></span>
      </button>
      <button type="button" role="menuitem" @click="$store.recents.remove($store.recents.menu.id)"
              class="tl-item flex h-9 w-full items-center gap-3 rounded-[9px] px-2.5 text-[13.5px] font-medium text-[#ff6b81] hover:bg-[rgba(255,107,129,0.12)] focus-visible:bg-[rgba(255,107,129,0.12)] focus-visible:outline-none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-[17px] w-[17px] flex-none" aria-hidden="true"><path d="M4.5 7h15M9.5 7V5h5v2M6.5 7l.8 11.5a1.5 1.5 0 0 0 1.5 1.5h6.4a1.5 1.5 0 0 0 1.5-1.5L17.5 7M10 11v6M14 11v6"/></svg>
        Delete
      </button>
    </div>
    </template>
    @endif
    @endunless

    @if($isAdmin)
    {{-- Course Assignment: pick who captains each course --}}
    @php $on = request()->routeIs('admin.home'); @endphp
    <a href="{{ route('admin.home') }}"
       @if($on) aria-current="page" @endif
       aria-label="Course Assignment"
       class="tl-item group relative flex h-10 items-center rounded-[11px] px-[14px]
              focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]
              {{ $on ? 'bg-[rgba(115,182,255,0.14)] text-[#73b6ff]' : 'text-muted hover:bg-[rgba(123,142,220,0.09)] hover:text-ink' }}">
      @if($on)<span class="absolute -left-2.5 top-1/2 h-4 w-[3px] -translate-y-1/2 rounded-r-full bg-[#73b6ff] shadow-[0_0_10px_rgba(115,182,255,0.7)]"></span>@endif
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
           stroke-linecap="round" stroke-linejoin="round" class="tl-ico h-5 w-5 flex-none" aria-hidden="true">
        <rect class="duo" x="4" y="3.5" width="16" height="17" rx="2.6"/>
        <path d="M8.5 3.5v3.5h7V3.5"/>
        <path d="M8 12h8M8 15.5h5.5"/>
      </svg>
      <span class="tl-label ml-3 max-w-0 overflow-hidden whitespace-nowrap text-[13.5px] font-medium leading-none tracking-[-0.006em] opacity-0"
            :class="collapsed ? 'max-w-0 opacity-0 !ml-0' : '!max-w-[150px] !opacity-100'"
            :style="collapsed ? '' : 'transition-delay:90ms'">Course Assignment</span>
      <span x-show="collapsed" x-cloak aria-hidden="true"
            class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 -translate-y-1/2 whitespace-nowrap rounded-lg border border-white/10 bg-[rgba(14,16,44,0.92)] px-2.5 py-1.5 text-[12px] font-medium text-ink opacity-0 shadow-[0_8px_24px_-8px_rgba(0,0,0,0.6)] transition-opacity duration-150 group-hover:opacity-100 group-hover:delay-500 group-focus-visible:opacity-100">Course Assignment</span>
    </a>
    @endif

    @if($isFaculty)
    {{-- Classes: the weekly schedule page --}}
    @php $on = request()->routeIs('faculty.classes'); @endphp
    <a href="{{ route('faculty.classes') }}"
       @if($on) aria-current="page" @endif
       aria-label="Classes"
       class="tl-item group relative flex h-10 items-center rounded-[11px] px-[14px]
              focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]
              {{ $on ? 'bg-[rgba(115,182,255,0.14)] text-[#73b6ff]' : 'text-muted hover:bg-[rgba(123,142,220,0.09)] hover:text-ink' }}">
      @if($on)<span class="absolute -left-2.5 top-1/2 h-4 w-[3px] -translate-y-1/2 rounded-r-full bg-[#73b6ff] shadow-[0_0_10px_rgba(115,182,255,0.7)]"></span>@endif
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
           stroke-linecap="round" stroke-linejoin="round" class="tl-ico h-5 w-5 flex-none" aria-hidden="true">
        <rect class="duo" x="3.5" y="4" width="17" height="11.5" rx="2.8"/>
        <path d="M7.6 8.4h6.2M7.6 11.6h3.8"/>
        <path d="M12 15.5v3.2M8.4 20h7.2"/>
      </svg>
      <span class="tl-label ml-3 max-w-0 overflow-hidden whitespace-nowrap text-[13.5px] font-medium leading-none tracking-[-0.006em] opacity-0"
            :class="collapsed ? 'max-w-0 opacity-0 !ml-0' : '!max-w-[150px] !opacity-100'"
            :style="collapsed ? '' : 'transition-delay:90ms'">Classes</span>
      <span x-show="collapsed" x-cloak aria-hidden="true"
            class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 -translate-y-1/2 whitespace-nowrap rounded-lg border border-white/10 bg-[rgba(14,16,44,0.92)] px-2.5 py-1.5 text-[12px] font-medium text-ink opacity-0 shadow-[0_8px_24px_-8px_rgba(0,0,0,0.6)] transition-opacity duration-150 group-hover:opacity-100 group-hover:delay-500 group-focus-visible:opacity-100">Classes</span>
    </a>

    {{-- Calendar: toggles the schedule drawer (a panel, not a page — so data-on, not aria-current) --}}
    <button type="button"
       @click="$store.calendar.toggle($el)"
       :data-on="$store.calendar.open.toString()"
       :aria-expanded="$store.calendar.open.toString()"
       aria-haspopup="dialog" aria-label="Calendar"
       class="tl-item group relative flex h-10 w-full items-center rounded-[11px] px-[14px] text-left
              focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]"
       :class="$store.calendar.open ? 'bg-[rgba(115,182,255,0.14)] text-[#73b6ff]' : 'text-muted hover:bg-[rgba(123,142,220,0.09)] hover:text-ink'">
      <span x-show="$store.calendar.open" x-cloak class="absolute -left-2.5 top-1/2 h-4 w-[3px] -translate-y-1/2 rounded-r-full bg-[#73b6ff] shadow-[0_0_10px_rgba(115,182,255,0.7)]"></span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
           stroke-linecap="round" stroke-linejoin="round" class="tl-ico h-5 w-5 flex-none" aria-hidden="true">
        <rect class="duo" x="3.5" y="5" width="17" height="15.5" rx="3.2"/>
        <path d="M3.5 9.8h17M8 3.5v3M16 3.5v3"/>
        <circle cx="12" cy="15" r="1.3" fill="currentColor" stroke="none"/>
      </svg>
      <span class="tl-label ml-3 max-w-0 overflow-hidden whitespace-nowrap text-[13.5px] font-medium leading-none tracking-[-0.006em] opacity-0"
            :class="collapsed ? 'max-w-0 opacity-0 !ml-0' : '!max-w-[150px] !opacity-100'"
            :style="collapsed ? '' : 'transition-delay:90ms'">Calendar</span>
      <span x-show="collapsed" x-cloak aria-hidden="true"
            class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 -translate-y-1/2 whitespace-nowrap rounded-lg border border-white/10 bg-[rgba(14,16,44,0.92)] px-2.5 py-1.5 text-[12px] font-medium text-ink opacity-0 shadow-[0_8px_24px_-8px_rgba(0,0,0,0.6)] transition-opacity duration-150 group-hover:opacity-100 group-hover:delay-500 group-focus-visible:opacity-100">Calendar</span>
    </button>
    @endif

    @unless($isFaculty || $isAdmin)
    {{-- Planets --}}
    @php $on = request()->routeIs('student.planets'); @endphp
    <a href="{{ route('student.planets') }}"
       @if($on) aria-current="page" @endif
       aria-label="Planets"
       class="tl-item group relative flex h-10 items-center rounded-[11px] px-[14px]
              focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]
              {{ $on ? 'bg-[rgba(115,182,255,0.14)] text-[#73b6ff]' : 'text-muted hover:bg-[rgba(123,142,220,0.09)] hover:text-ink' }}">
      @if($on)<span class="absolute -left-2.5 top-1/2 h-4 w-[3px] -translate-y-1/2 rounded-r-full bg-[#73b6ff] shadow-[0_0_10px_rgba(115,182,255,0.7)]"></span>@endif
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
           stroke-linecap="round" stroke-linejoin="round" class="tl-ico h-5 w-5 flex-none" aria-hidden="true">
        <defs>
          <mask id="tl-planet-mask" maskUnits="userSpaceOnUse" x="-2" y="-2" width="28" height="28">
            <rect x="-2" y="-2" width="28" height="28" fill="#fff"/>
            <circle cx="12" cy="12" r="5.9" fill="#000"/>
          </mask>
        </defs>
        <circle class="duo" cx="12" cy="12" r="5.2"/>
        <g transform="rotate(-24 12 12)">
          <path d="M22 12a10 3.6 0 0 0-20 0" mask="url(#tl-planet-mask)"/>
          <path d="M2 12a10 3.6 0 0 0 20 0"/>
        </g>
      </svg>
      <span class="tl-label ml-3 max-w-0 overflow-hidden whitespace-nowrap text-[13.5px] font-medium leading-none tracking-[-0.006em] opacity-0"
            :class="collapsed ? 'max-w-0 opacity-0 !ml-0' : '!max-w-[150px] !opacity-100'"
            :style="collapsed ? '' : 'transition-delay:90ms'">Planets</span>
      <span x-show="collapsed" x-cloak aria-hidden="true"
            class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 -translate-y-1/2 whitespace-nowrap rounded-lg border border-white/10 bg-[rgba(14,16,44,0.92)] px-2.5 py-1.5 text-[12px] font-medium text-ink opacity-0 shadow-[0_8px_24px_-8px_rgba(0,0,0,0.6)] transition-opacity duration-150 group-hover:opacity-100 group-hover:delay-500 group-focus-visible:opacity-100">Planets</span>
    </a>
    @endunless

    @unless($isFaculty || $isAdmin)
    {{-- Crew --}}
    @php $on = request()->routeIs('student.crew'); @endphp
    <a href="{{ route('student.crew') }}"
       @if($on) aria-current="page" @endif
       aria-label="Crew"
       class="tl-item group relative flex h-10 items-center rounded-[11px] px-[14px]
              focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]
              {{ $on ? 'bg-[rgba(115,182,255,0.14)] text-[#73b6ff]' : 'text-muted hover:bg-[rgba(123,142,220,0.09)] hover:text-ink' }}">
      @if($on)<span class="absolute -left-2.5 top-1/2 h-4 w-[3px] -translate-y-1/2 rounded-r-full bg-[#73b6ff] shadow-[0_0_10px_rgba(115,182,255,0.7)]"></span>@endif
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
           stroke-linecap="round" stroke-linejoin="round" class="tl-ico h-5 w-5 flex-none" aria-hidden="true">
        <circle class="duo" cx="9" cy="8.2" r="3.3"/>
        <path class="duo" d="M3.2 19.6c.5-3.1 2.9-5 5.8-5s5.3 1.9 5.8 5z"/>
        <path d="M15.6 5.3a3 3 0 0 1 0 5.8"/>
        <path d="M17.4 14.9c1.9.6 3.1 2.3 3.4 4.7"/>
      </svg>
      <span class="tl-label ml-3 max-w-0 overflow-hidden whitespace-nowrap text-[13.5px] font-medium leading-none tracking-[-0.006em] opacity-0"
            :class="collapsed ? 'max-w-0 opacity-0 !ml-0' : '!max-w-[150px] !opacity-100'"
            :style="collapsed ? '' : 'transition-delay:90ms'">Crew</span>
      <span x-show="collapsed" x-cloak aria-hidden="true"
            class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 -translate-y-1/2 whitespace-nowrap rounded-lg border border-white/10 bg-[rgba(14,16,44,0.92)] px-2.5 py-1.5 text-[12px] font-medium text-ink opacity-0 shadow-[0_8px_24px_-8px_rgba(0,0,0,0.6)] transition-opacity duration-150 group-hover:opacity-100 group-hover:delay-500 group-focus-visible:opacity-100">Crew</span>
    </a>
    @endunless

  </nav>

  {{-- ── Footer: controls + profile ── --}}
  <div class="border-t border-[rgba(150,170,255,0.09)] px-2.5 pb-3 pt-2.5">

    <div class="space-y-1">
      {{-- Theme --}}
      <button type="button" @click="toggleTheme()" aria-label="Toggle theme"
              class="tl-item group relative flex h-10 w-full items-center rounded-[11px] px-[14px] text-muted
                     hover:bg-[rgba(123,142,220,0.09)] hover:text-ink
                     focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
             stroke-linecap="round" stroke-linejoin="round" class="tl-ico h-5 w-5 flex-none" aria-hidden="true">
          <circle cx="12" cy="12" r="8.5"/>
          <path d="M12 3.5a8.5 8.5 0 0 1 0 17z" fill="currentColor" fill-opacity="0.85"/>
        </svg>
        <span class="tl-label ml-3 max-w-0 overflow-hidden whitespace-nowrap text-[13.5px] font-medium leading-none tracking-[-0.006em] opacity-0"
              :class="collapsed ? 'max-w-0 opacity-0 !ml-0' : '!max-w-[150px] !opacity-100'"
              :style="collapsed ? '' : 'transition-delay:90ms'">Theme</span>
        <span x-show="collapsed" x-cloak aria-hidden="true"
              class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 -translate-y-1/2 whitespace-nowrap rounded-lg border border-white/10 bg-[rgba(14,16,44,0.92)] px-2.5 py-1.5 text-[12px] font-medium text-ink opacity-0 shadow-[0_8px_24px_-8px_rgba(0,0,0,0.6)] transition-opacity duration-150 group-hover:opacity-100 group-hover:delay-500 group-focus-visible:opacity-100">Theme</span>
      </button>

      {{-- Collapse / expand --}}
      <button type="button" @click="collapsed = !collapsed"
              :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'" :aria-expanded="(!collapsed).toString()"
              class="tl-item group relative flex h-10 w-full items-center rounded-[11px] px-[14px] text-muted
                     hover:bg-[rgba(123,142,220,0.09)] hover:text-ink
                     focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
             stroke-linecap="round" stroke-linejoin="round" class="tl-ico h-5 w-5 flex-none" aria-hidden="true">
          <rect x="3.5" y="4.5" width="17" height="15" rx="4"/>
          <path d="M9.5 4.5v15"/>
          <path d="M7.2 10.4 6 12l1.2 1.6" stroke-width="1.3" class="transition-opacity duration-200" :class="collapsed ? 'opacity-0' : 'opacity-100'"/>
          <path d="M6 10.4 7.2 12 6 13.6" stroke-width="1.3" class="transition-opacity duration-200" :class="collapsed ? 'opacity-100' : 'opacity-0'"/>
        </svg>
        <span class="tl-label ml-3 max-w-0 overflow-hidden whitespace-nowrap text-[13.5px] font-medium leading-none tracking-[-0.006em] opacity-0"
              :class="collapsed ? 'max-w-0 opacity-0 !ml-0' : '!max-w-[150px] !opacity-100'"
              :style="collapsed ? '' : 'transition-delay:90ms'">Collapse</span>
        <span x-show="collapsed" x-cloak aria-hidden="true"
              class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 -translate-y-1/2 whitespace-nowrap rounded-lg border border-white/10 bg-[rgba(14,16,44,0.92)] px-2.5 py-1.5 text-[12px] font-medium text-ink opacity-0 shadow-[0_8px_24px_-8px_rgba(0,0,0,0.6)] transition-opacity duration-150 group-hover:opacity-100 group-hover:delay-500 group-focus-visible:opacity-100">Expand</span>
      </button>
    </div>

    {{-- ── Profile ── --}}
    @auth
    <div class="relative mt-2.5 border-t border-[rgba(150,170,255,0.07)] pt-2.5"
         x-data="{ open: false }"
         @keydown.escape.window="if (open) { open = false; $refs.trigger.focus(); }"
         @click.outside="open = false">

      {{-- Popover: grows out of its trigger (origin-aware), materialises via blur+scale --}}
      <div x-show="open" x-cloak
           x-transition:enter="transition ease-[cubic-bezier(0.32,0.72,0,1)] duration-200 motion-reduce:duration-100"
           x-transition:enter-start="opacity-0 scale-95 translate-y-1 blur-[2px] motion-reduce:scale-100 motion-reduce:translate-y-0 motion-reduce:blur-0"
           x-transition:enter-end="opacity-100 scale-100 translate-y-0 blur-0"
           x-transition:leave="transition ease-in duration-120 motion-reduce:duration-100"
           x-transition:leave-start="opacity-100 scale-100"
           x-transition:leave-end="opacity-0 scale-95 motion-reduce:scale-100"
           role="menu" aria-label="Account"
           class="tl-glass absolute z-50 w-[224px] overflow-hidden rounded-[16px] border border-white/10
                  bg-[rgba(14,16,44,0.86)] backdrop-blur-2xl backdrop-saturate-150
                  shadow-[0_24px_60px_-14px_rgba(0,0,0,0.75),inset_0_1px_0_rgba(255,255,255,0.07)]"
           :class="collapsed ? 'left-full bottom-2.5 ml-3 origin-bottom-left' : 'bottom-full left-0 mb-2.5 origin-bottom-left'">
        <div class="border-b border-white/[0.07] px-3.5 py-3">
          <p class="truncate text-[13px] font-semibold leading-tight tracking-[-0.006em] text-ink">{{ $user->name }}</p>
          <p class="mt-0.5 truncate text-[12px] leading-tight text-muted">{{ $user->email }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="p-1.5">
          @csrf
          <button type="submit" role="menuitem" x-ref="logout"
                  class="tl-item flex h-10 w-full items-center gap-3 rounded-[10px] px-2.5 text-[13.5px] font-medium tracking-[-0.006em] text-ink
                         hover:bg-[rgba(255,255,255,0.08)] focus-visible:bg-[rgba(255,255,255,0.08)] focus-visible:outline-none">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                 stroke-linecap="round" stroke-linejoin="round" class="h-[18px] w-[18px] flex-none" aria-hidden="true">
              <path d="M10 4H8a3.5 3.5 0 0 0-3.5 3.5v9A3.5 3.5 0 0 0 8 20h2"/>
              <path d="M10.5 12H20M16.5 8.3 20 12l-3.5 3.7"/>
            </svg>
            Log out
          </button>
        </form>
      </div>

      {{-- Trigger --}}
      <button type="button" x-ref="trigger"
              @click="open = !open; if (open) $nextTick(() => $refs.logout.focus())"
              :aria-expanded="open.toString()" aria-haspopup="menu" aria-label="Account menu"
              class="tl-item group relative flex h-12 w-full items-center rounded-[12px] px-2 text-left
                     hover:bg-[rgba(123,142,220,0.09)]"
              :class="open ? 'bg-[rgba(123,142,220,0.09)]' : ''"
              :data-on="open.toString()"
              style="outline-offset:2px">
        <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full bg-gradient-to-br from-[#73b6ff] to-[#9b6bff]
                     text-[11.5px] font-semibold tracking-[0.02em] text-[#06061a] shadow-[0_0_0_1px_rgba(255,255,255,0.18),0_4px_14px_-4px_rgba(115,182,255,0.55)]"
              aria-hidden="true">{{ $initials }}</span>
        <span class="tl-label ml-3 flex min-w-0 max-w-0 flex-col overflow-hidden whitespace-nowrap leading-none opacity-0"
              :class="collapsed ? 'max-w-0 opacity-0 !ml-0' : '!max-w-[130px] !opacity-100'"
              :style="collapsed ? '' : 'transition-delay:90ms'">
          <span class="truncate text-[13.5px] font-semibold tracking-[-0.006em] text-ink">{{ $user->name }}</span>
          <span class="mt-1 truncate text-[11.5px] font-medium tracking-[0.01em] text-muted">{{ $roleLabel }}</span>
        </span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
             stroke-linecap="round" stroke-linejoin="round"
             class="tl-label ml-auto h-4 w-4 flex-none text-muted opacity-0" aria-hidden="true"
             :class="collapsed ? 'opacity-0 w-0 !ml-0' : '!opacity-100'"
             :style="collapsed ? '' : 'transition-delay:90ms'">
          <path d="M8 9.5 12 6l4 3.5M8 14.5 12 18l4-3.5"/>
        </svg>
        <span x-show="collapsed && !open" x-cloak aria-hidden="true"
              class="pointer-events-none absolute left-full top-1/2 z-50 ml-3 -translate-y-1/2 whitespace-nowrap rounded-lg border border-white/10 bg-[rgba(14,16,44,0.92)] px-2.5 py-1.5 text-[12px] font-medium text-ink opacity-0 shadow-[0_8px_24px_-8px_rgba(0,0,0,0.6)] transition-opacity duration-150 group-hover:opacity-100 group-hover:delay-500 group-focus-visible:opacity-100">{{ $user->name }}</span>
      </button>
    </div>
    @endauth

  </div>

</aside>

@if($isFaculty)
  @include('faculty.partials.calendar-drawer')
@endif

{{-- Active-hours heartbeat: one ping a minute while a signed-in student has this tab visible. --}}
@auth
@if($user->role !== 'faculty' && $user->role !== 'admin')
<script>
  (function () {
    if (window.__tlActivity) return;
    window.__tlActivity = true;
    var url = @json(route('student.activity')), token = @json(csrf_token());
    setInterval(function () {
      if (document.visibilityState !== 'visible') return;
      fetch(url, { method: 'POST', credentials: 'same-origin', keepalive: true,
                   headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' } }).catch(function () {});
    }, 60000);
  })();
</script>
@endif
@endauth
