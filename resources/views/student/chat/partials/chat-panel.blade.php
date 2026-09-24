{{-- Astro responding indicator: the TechLab logo, alive while Astro works.
     data-state: idle (calm) -> thinking (gentle pulse) -> exploring (a bit more active)
     -> responding (settles as the answer fades in). Only transform/opacity are animated. --}}
<style>
  /* Astro answer typography — ChatGPT-like: solid ink text, bold headings, tidy lists. */
  .astro-md { font-size: 16px; line-height: 1.75; color: rgb(var(--c-ink)); }
  .astro-md > :first-child { margin-top: 0; }
  .astro-md > :last-child { margin-bottom: 0; }
  .astro-md p { margin: 0 0 1rem; }
  .astro-md strong, .astro-md b { font-weight: 700; color: rgb(var(--c-ink)); }
  .astro-md h1, .astro-md h2, .astro-md h3, .astro-md h4 { font-weight: 700; color: rgb(var(--c-ink)); line-height: 1.35; margin: 1.5rem 0 .5rem; }
  .astro-md h1 { font-size: 1.5rem; } .astro-md h2 { font-size: 1.3rem; } .astro-md h3 { font-size: 1.15rem; } .astro-md h4 { font-size: 1rem; }
  .astro-md ul, .astro-md ol { margin: 0 0 1rem; padding-left: 1.6rem; }
  .astro-md ul { list-style: disc; } .astro-md ol { list-style: decimal; }
  .astro-md li { margin: .3rem 0; padding-left: .3rem; }
  .astro-md li::marker { color: rgb(var(--c-ink)); font-weight: 600; }
  .astro-md li > ul, .astro-md li > ol { margin: .3rem 0 0; }
  .astro-md a { color: #73b6ff; text-decoration: underline; text-underline-offset: 2px; }
  .astro-md blockquote { margin: 0 0 1rem; padding: .25rem 0 .25rem 1rem; border-left: 3px solid rgb(var(--c-muted) / .5); color: rgb(var(--c-muted)); }
  .astro-md :not(pre) > code { padding: .15rem .4rem; border-radius: 6px; font-size: .875em; background: rgba(115,182,255,.14); color: #5be1ff; }
  .astro-md hr { margin: 1.5rem 0; border-color: rgb(var(--c-muted) / .3); }
  html[data-theme="light"] .astro-md a { color: #2468c4; }
  html[data-theme="light"] .astro-md :not(pre) > code { background: rgba(0,0,0,.06); color: #0d0d0d; }
</style>

<style>
  .astro-resp { display: flex; align-items: center; gap: 14px; min-height: 28px; padding: 2px 0; } /* gap clears the orbiting spark */
  .astro-logo { position: relative; width: 24px; height: 24px; flex: none; transform-origin: 50% 50%; }
  .astro-logo img { position: relative; display: block; width: 100%; height: 100%; transform-origin: 50% 50%;
    user-select: none; filter: drop-shadow(0 0 4px rgba(115, 182, 255, 0.28)); }

  /* soft glow behind the logo; the wrapper fades on settle, the halo inside pulses */
  .astro-glow { position: absolute; inset: -7px; pointer-events: none; transition: opacity 350ms ease; }
  .astro-halo { position: absolute; inset: 0; border-radius: 50%; opacity: 0;
    background: radial-gradient(closest-side, rgba(115, 182, 255, 0.32), rgba(115, 182, 255, 0) 72%); }

  /* a tiny spark that circles the logo while exploring */
  .astro-orbit { position: absolute; inset: -6px; border-radius: 50%; pointer-events: none; opacity: 0; transition: opacity 350ms ease; }
  .astro-orbit::before { content: ""; position: absolute; top: 0; left: 50%; width: 3px; height: 3px; margin-left: -1.5px;
    border-radius: 50%; background: #5be1ff; box-shadow: 0 0 6px 1px rgba(91, 225, 255, 0.7); }

  /* IDLE — calm */
  .astro-resp[data-state="idle"] img { opacity: 0.85; }

  /* THINKING — gentle breathing */
  .astro-resp[data-state="thinking"] img { animation: astro-breath 2.6s ease-in-out infinite; }
  .astro-resp[data-state="thinking"] .astro-halo { animation: astro-halo 2.6s ease-in-out infinite; }

  /* EXPLORING — slightly more active: quicker breath, slow sway, orbiting spark */
  .astro-resp[data-state="exploring"] .astro-logo { animation: astro-sway 4.8s ease-in-out infinite; }
  .astro-resp[data-state="exploring"] img { animation: astro-breath 1.8s ease-in-out infinite; }
  .astro-resp[data-state="exploring"] .astro-halo { animation: astro-halo 1.8s ease-in-out infinite; }
  .astro-resp[data-state="exploring"] .astro-orbit { opacity: 1; animation: astro-orbit 3.4s linear infinite; }

  /* RESPONDING — settle: freeze mid-motion (no snap back), fade the glow and spark */
  .astro-resp[data-state="responding"] .astro-logo,
  .astro-resp[data-state="responding"] img,
  .astro-resp[data-state="responding"] .astro-halo,
  .astro-resp[data-state="responding"] .astro-orbit,
  .astro-resp[data-state="responding"] .astro-status-text { animation-play-state: paused; }
  .astro-resp[data-state="responding"] .astro-glow,
  .astro-resp[data-state="responding"] .astro-orbit { opacity: 0; }

  /* the words: a soft highlight drifts across; each new line eases in */
  .astro-status { display: inline-block; min-width: 0; overflow: hidden; }
  .astro-status-text { display: inline-block; font-size: 13px; line-height: 20px; font-weight: 500;
    letter-spacing: -0.006em; white-space: nowrap; color: #98a2d4;
    background: linear-gradient(100deg, #98a2d4 35%, #eaeeff 50%, #98a2d4 65%); background-size: 200% 100%;
    -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
    animation: astro-shimmer 2.8s linear infinite; }
  .astro-status-text.astro-swap { animation: astro-swap 420ms cubic-bezier(0.32, 0.72, 0, 1) both, astro-shimmer 2.8s linear infinite; }

  @keyframes astro-breath  { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.07); } }
  @keyframes astro-halo    { 0%, 100% { opacity: 0.35; transform: scale(0.92); } 50% { opacity: 0.9; transform: scale(1.08); } }
  @keyframes astro-sway    { 0%, 100% { transform: rotate(-5deg); } 50% { transform: rotate(5deg); } }
  @keyframes astro-orbit   { to { transform: rotate(360deg); } }
  @keyframes astro-swap    { from { opacity: 0; transform: translateY(3px); } to { opacity: 1; transform: none; } }
  @keyframes astro-shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
  @keyframes astro-fade    { 0%, 100% { opacity: 0.6; } 50% { opacity: 1; } }
  @keyframes astro-fade-in { from { opacity: 0; } to { opacity: 1; } }

  /* Reduced motion: no movement at all — a gentle opacity breath and plain fades instead. */
  @media (prefers-reduced-motion: reduce) {
    .astro-logo, .astro-halo, .astro-orbit { animation: none !important; }
    .astro-resp[data-state="thinking"] img,
    .astro-resp[data-state="exploring"] img { animation: astro-fade 2.4s ease-in-out infinite !important; }
    .astro-orbit { display: none; }
    .astro-status-text { animation: none !important; background: none; -webkit-text-fill-color: #98a2d4; color: #98a2d4; }
    .astro-status-text.astro-swap { animation: astro-fade-in 200ms linear both !important; }
  }
</style>

{{-- COLUMN 2 — Chat (center, primary focus, widest column).
     Driven by the `astroChat()` Alpine component (defined in chat.blade.php).
     Messages are rendered dynamically from the `messages` array. /chat/message returns the
     finished reply as one JSON response (it does not stream), so while Astro works we show
     the responding indicator and then cross-fade into the answer. --}}
<main class="flex min-w-0 flex-1 flex-col bg-transparent" x-data="astroChat()" x-init="init()">
  {{-- Header --}}
  <header class="flex items-center gap-2 border-b border-glassBorder px-3 py-2.5">
    <button type="button" @click="toggleSources()" class="grid h-9 w-9 flex-none place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:border-[rgba(115,182,255,0.5)] hover:text-ink" title="Toggle sources" aria-label="Toggle sources">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16"/></svg>
    </button>
    <h1 class="font-display text-sm font-semibold text-ink">Chat</h1>

    <div class="ml-auto flex items-center gap-1.5">
      <button type="button" @click="newChat()" class="hidden items-center gap-1.5 rounded-full border border-glassBorder bg-glass px-3 py-1.5 text-xs font-medium text-muted transition hover:border-[rgba(115,182,255,0.5)] hover:text-ink sm:inline-flex">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M12 5v14M5 12h14"/></svg>
        New chat
      </button>

      <button type="button" @click="toggleStudio()" class="grid h-9 w-9 flex-none place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:border-[rgba(115,182,255,0.5)] hover:text-ink" title="Toggle studio" aria-label="Toggle studio">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M15 4v16"/></svg>
      </button>
    </div>
  </header>

  {{-- Scroll area --}}
  <div class="relative flex-1 overflow-y-auto" id="chat-scroll">
    <div class="mx-auto w-full max-w-3xl px-4 py-6 sm:px-6">

      {{-- ===== Empty state (Astro-voiced) ===== --}}
      <section x-show="messages.length === 0" x-cloak class="flex flex-col items-center pt-6 text-center">
        <div class="grid h-24 w-24 place-items-center rounded-full bg-glass p-3 ring-1 ring-[rgba(115,182,255,0.4)]">
          @include('student.chat.partials.astro-mascot')
        </div>
        @if($isFacultyChat ?? false)
        <h2 class="mt-4 font-display text-[1.75rem] font-bold leading-tight tracking-[-0.02em] text-ink">Hi Captain {{ $userName ?? '' }}! I'm Astro</h2>
        <p class="mt-1.5 text-sm font-medium text-muted">Plan lessons, draft quizzes, build rubrics. 🚀</p>

        <div class="mt-6 w-full max-w-md space-y-2">
          <button type="button" @click="sendSuggestion('Plan a 45-minute lesson on Python loops')" class="flex w-full items-center gap-3 rounded-full border border-glassBorder bg-glass px-4 py-3 text-left text-sm text-ink transition hover:border-[rgba(115,182,255,0.5)] hover:bg-[rgba(115,182,255,0.08)]">
            <span class="tl-cyan">✦</span> Plan a 45-minute lesson on Python loops
          </button>
          <button type="button" @click="sendSuggestion('Write 5 quiz questions on basic networking')" class="flex w-full items-center gap-3 rounded-full border border-glassBorder bg-glass px-4 py-3 text-left text-sm text-ink transition hover:border-[rgba(115,182,255,0.5)] hover:bg-[rgba(115,182,255,0.08)]">
            <span class="tl-cyan">✦</span> Write 5 quiz questions on basic networking
          </button>
          <button type="button" @click="sendSuggestion('Create a grading rubric for a first Python project')" class="flex w-full items-center gap-3 rounded-full border border-glassBorder bg-glass px-4 py-3 text-left text-sm text-ink transition hover:border-[rgba(115,182,255,0.5)] hover:bg-[rgba(115,182,255,0.08)]">
            <span class="tl-cyan">✦</span> Create a grading rubric for a first Python project
          </button>
        </div>
        @else
        <h2 class="mt-4 font-display text-[1.75rem] font-bold leading-tight tracking-[-0.02em] text-ink">Hi {{ $userName ?? 'Explorer' }}, I'm Astro</h2>

        {{-- Suggested prompt chips — these actually send a message. --}}
        <div class="mt-6 w-full max-w-md space-y-2">
          <button type="button" @click="sendSuggestion('Explain HTTP like I\'m five')" class="flex w-full items-center gap-3 rounded-full border border-glassBorder bg-glass px-4 py-3 text-left text-sm text-ink transition hover:border-[rgba(115,182,255,0.5)] hover:bg-[rgba(115,182,255,0.08)]">
            <span class="tl-cyan">✦</span> Explain HTTP like I'm five
          </button>
          <button type="button" @click="sendSuggestion('Quiz me on basic networking')" class="flex w-full items-center gap-3 rounded-full border border-glassBorder bg-glass px-4 py-3 text-left text-sm text-ink transition hover:border-[rgba(115,182,255,0.5)] hover:bg-[rgba(115,182,255,0.08)]">
            <span class="tl-cyan">✦</span> Quiz me on basic networking
          </button>
          <button type="button" @click="sendSuggestion('Help me debug my first Laravel route')" class="flex w-full items-center gap-3 rounded-full border border-glassBorder bg-glass px-4 py-3 text-left text-sm text-ink transition hover:border-[rgba(115,182,255,0.5)] hover:bg-[rgba(115,182,255,0.08)]">
            <span class="tl-cyan">✦</span> Help me debug my first Laravel route
          </button>
        </div>
        @endif
      </section>

      {{-- ===== Live thread ===== --}}
      <section x-show="messages.length > 0" x-cloak class="space-y-6">
        @unless($isFacultyChat ?? false)
        {{-- Dismissible feature-tip banner --}}
        <div x-show="!bannerDismissed" x-cloak class="flex items-start gap-2.5 rounded-[14px] border border-[rgba(115,182,255,0.35)] bg-[rgba(115,182,255,0.08)] px-3 py-2.5">
          <svg viewBox="0 0 24 24" fill="none" stroke="#5be1ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-4 w-4 flex-none"><path d="M15 4V2M15 16v-2M8 9h2M20 9h2M17.8 11.8 19 13M15 9h.01M17.8 6.2 19 5M3 21l9-9M12.2 6.2 11 5"/></svg>
          <p class="flex-1 text-xs leading-relaxed text-ink">Tip: ask Astro to turn any answer into a <span class="font-semibold tl-cyan">Quiz</span> or <span class="font-semibold tl-cyan">Flashcards</span> from the Studio panel.</p>
          <button type="button" @click="bannerDismissed = true" class="text-muted transition hover:text-ink" title="Dismiss" aria-label="Dismiss">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
          </button>
        </div>
        @endunless

        <template x-for="(m, i) in messages" :key="i">
          <div>
            {{-- User message --}}
            <div x-show="m.role === 'user'" class="flex justify-end">
              <div class="max-w-[85%] rounded-2xl rounded-tr-md border border-[rgba(115,182,255,0.35)] bg-[rgba(115,182,255,0.12)] px-4 py-2.5 text-sm leading-relaxed text-ink">
                <div x-show="m.attachments && m.attachments.length" class="mb-2 flex flex-wrap justify-end gap-2">
                  <template x-for="(a, i) in (m.attachments || [])" :key="i">
                    <div class="relative">
        <template x-if="a.kind === 'image' && a.preview">
          <img :src="a.preview" :alt="a.name" class="h-[120px] max-w-[220px] rounded-2xl border border-white/10 object-cover">
        </template>
        <template x-if="!(a.kind === 'image' && a.preview)">
          <div class="flex w-[230px] items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.05] p-2.5">
            <span class="grid h-10 w-10 flex-none place-items-center rounded-xl text-[11px] font-bold tracking-wide text-white" :style="'background:' + meta(a).color" x-text="meta(a).badge"></span>
            <span class="min-w-0"><span class="block truncate text-[13px] font-semibold leading-tight text-ink" x-text="a.name"></span><span class="block text-[11.5px] text-muted" x-text="meta(a).label"></span></span>
          </div>
        </template>
      </div>
                  </template>
                </div>
                <div x-show="m.content" class="whitespace-pre-wrap" x-text="m.content"></div>
              </div>
            </div>

            {{-- Astro message --}}
            <div x-show="m.role === 'assistant'" class="flex gap-3">
              <div class="grid h-10 w-10 flex-none place-items-center rounded-full bg-glass p-1 ring-1 ring-[rgba(115,182,255,0.4)]">
                @include('student.chat.partials.astro-mascot')
              </div>

              <div class="min-w-0 flex-1">
                <div class="mb-1 flex items-center gap-2">
                  <span class="font-display text-sm font-semibold text-ink">Astro</span>
                  <span class="text-[11px] text-muted">your AI teacher</span>
                </div>

                {{-- Indicator and answer share one relative box. While the indicator leaves it is
                     taken out of flow (absolute), so the answer fading in never gets pushed
                     down — a cross-fade with no layout jump. --}}
                <div class="relative">
                {{-- Responding indicator (until the reply arrives) --}}
                <div x-show="m.pending && m.content === ''"
                     x-transition:leave="transition ease-out duration-200 absolute inset-x-0 top-0"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                  @include('student.chat.partials.astro-responding')
                </div>

                {{-- Answer --}}
                <div
                  x-show="m.content !== ''"
                  x-transition:enter="transition ease-[cubic-bezier(0.32,0.72,0,1)] duration-[380ms] delay-75"
                  x-transition:enter-start="opacity-0 translate-y-1"
                  x-transition:enter-end="opacity-100 translate-y-0"
                  class="astro-md"
                  x-html="m.html"
                  :class="{ 'animate-pulse opacity-70': m.pending }"
                ></div>
                </div>{{-- /indicator + answer box --}}

                {{-- Planet lessons Astro was grounded in --}}
                <div x-show="!m.pending && !m.streaming && m.sources && m.sources.length" x-cloak class="mt-2 flex flex-wrap items-center gap-1.5">
                  <span class="font-mono text-[10px] uppercase tracking-[0.18em] tl-cyan">Sources</span>
                  <template x-for="src in (m.sources || [])" :key="src">
                    <span class="rounded-full border border-[rgba(115,182,255,0.35)] bg-[rgba(115,182,255,0.1)] px-2.5 py-0.5 text-[11px] text-ink" x-text="src"></span>
                  </template>
                </div>

                {{-- Action row (only once the reply is settled) --}}
                <div
                  x-show="!m.pending && !m.streaming && m.content !== ''"
                  x-data="{ saved: false, copied: false }"
                  class="mt-2 flex items-center gap-1 text-muted"
                >
                  <button
                    type="button"
                    @click="saved = !saved"
                    :class="saved ? 'text-[#73b6ff]' : 'hover:text-ink'"
                    class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-xs transition"
                    :title="saved ? 'Saved to note' : 'Save to note'"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" :fill="saved ? 'currentColor' : 'none'">
                      <path d="M12 17v5M9 10.5V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v6.5l2 6H7z"/>
                    </svg>
                    <span x-show="!saved">Save to note</span>
                    <span x-show="saved" x-cloak>Saved</span>
                  </button>

                  <button
                    type="button"
                    @click="copied = true; copy(m.content); setTimeout(() => copied = false, 1500)"
                    class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-xs transition hover:text-ink"
                    title="Copy"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                      <rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/>
                    </svg>
                    <span x-show="!copied">Copy</span>
                    <span x-show="copied" x-cloak>Copied</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </template>
      </section>
    </div>
  </div>

  {{-- Input bar --}}
  <div class="border-t border-glassBorder px-4 py-3 sm:px-6"
       @dragover.prevent @drop.prevent="addFiles($event.dataTransfer.files)">
    <form @submit.prevent="send()" class="mx-auto max-w-3xl">

      <div class="rounded-[28px] border border-glassBorder bg-glass transition focus-within:border-[rgba(115,182,255,0.5)]"
           :class="listening ? '!border-[rgba(255,107,129,0.6)]' : ''">

        {{-- Attachments live inside the composer card, above the input row --}}
        <div x-show="attachments.length" x-cloak class="flex flex-wrap gap-3 px-4 pb-1 pt-4" aria-label="Attachments">
          <template x-for="(a, i) in attachments" :key="a.id">
            <div class="relative">
        <template x-if="a.kind === 'image' && a.preview">
          <img :src="a.preview" :alt="a.name" class="h-[120px] max-w-[220px] rounded-2xl border border-white/10 object-cover">
        </template>
        <template x-if="!(a.kind === 'image' && a.preview)">
          <div class="flex w-[230px] items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.05] p-2.5">
            <span class="grid h-10 w-10 flex-none place-items-center rounded-xl text-[11px] font-bold tracking-wide text-white" :style="'background:' + meta(a).color" x-text="meta(a).badge"></span>
            <span class="min-w-0"><span class="block truncate text-[13px] font-semibold leading-tight text-ink" x-text="a.name"></span><span class="block text-[11.5px] text-muted" x-text="meta(a).label"></span></span>
          </div>
        </template>
        <button type="button" @click="removeAttachment(i)" class="absolute -right-1.5 -top-1.5 grid h-6 w-6 place-items-center rounded-full border border-white/15 bg-[rgba(14,16,44,0.95)] text-ink shadow-md transition hover:bg-[#3a1d3f] hover:text-[#ff8fa3] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]" :aria-label="'Remove ' + a.name">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" class="h-3 w-3"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>
          </template>
        </div>

        <div class="flex items-end gap-1 py-1.5 pl-1.5 pr-1.5">

          {{-- + menu --}}
          <div class="relative flex-none" @click.outside="attachMenu = false" @keydown.escape.window="attachMenu = false">
            <button type="button" @click="attachMenu = !attachMenu" :aria-expanded="attachMenu.toString()" aria-haspopup="menu"
                    class="grid h-10 w-10 place-items-center rounded-full text-muted transition hover:bg-white/10 hover:text-ink focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]"
                    aria-label="Attach files" title="Attach files or photos">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-5 w-5 transition-transform" :class="attachMenu ? 'rotate-45' : ''"><path d="M12 5v14M5 12h14"/></svg>
            </button>
            <div x-show="attachMenu" x-cloak x-transition.opacity.duration.120ms role="menu"
                 class="absolute bottom-full left-0 z-30 mb-2 w-64 overflow-hidden rounded-2xl border border-white/10 bg-[rgba(14,16,44,0.96)] p-1.5 shadow-[0_18px_50px_-12px_rgba(0,0,0,0.7)] backdrop-blur-xl">
              <button type="button" role="menuitem" @click="$refs.filePick.click(); attachMenu = false"
                      class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[13.5px] text-ink transition hover:bg-white/[0.07]">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-[18px] w-[18px] flex-none text-muted"><path d="m21 11-8.5 8.5a5 5 0 0 1-7-7L14 4a3.3 3.3 0 0 1 4.7 4.7L10.2 17.2a1.7 1.7 0 0 1-2.4-2.4L15 7.5"/></svg>
                <span>Upload a file<span class="block text-[11px] text-muted">PDF, Word, PowerPoint, text or code</span></span>
              </button>
              <button type="button" role="menuitem" @click="pickPhoto()"
                      class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[13.5px] transition hover:bg-white/[0.07]"
                      :class="visionEnabled ? 'text-ink' : 'text-muted'">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-[18px] w-[18px] flex-none text-muted"><rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="9" cy="10" r="1.8"/><path d="m21 16-5-5-8 8"/></svg>
                <span>Upload a photo<span class="block text-[11px] text-muted" x-text="visionEnabled ? 'JPG, PNG, WebP — Astro can see it' : 'Image understanding is off'"></span></span>
              </button>
              <p class="mt-1 border-t border-white/[0.07] px-3 pb-1.5 pt-2 text-[11px] leading-snug text-muted" x-text="'Up to ' + maxFiles + ' files per message, ' + maxFileLabel + ' each.'"></p>
            </div>
            <input type="file" x-ref="filePick" multiple class="hidden" accept="{{ '.'.implode(',.', array_merge(\App\Services\AttachmentService::DOC_EXTENSIONS, \App\Services\AttachmentService::TEXT_EXTENSIONS)) }}"
                   @change="addFiles($event.target.files); $event.target.value = ''">
            <input type="file" x-ref="photoPick" multiple class="hidden" accept="image/jpeg,image/png,image/webp,image/gif"
                   @change="addFiles($event.target.files); $event.target.value = ''">
          </div>

          <textarea
            x-ref="box"
            x-model="input"
            rows="1"
            @keydown.enter.prevent="if (!($event.shiftKey)) send()"
            @paste="pasteFiles($event)"
            :placeholder="listening ? 'Listening…' : (attachments.length ? 'Add a message about your attachment (optional)' : '{{ ($isFacultyChat ?? false) ? "Ask Astro to help you teach" : "Ask Astro or create something" }}')"
            class="max-h-32 min-w-0 flex-1 resize-none self-center bg-transparent px-1 py-2 text-[15px] text-ink placeholder:text-muted/70 focus:outline-none"
            oninput="this.style.height='auto'; this.style.height=Math.min(this.scrollHeight,128)+'px'"
            aria-label="Message Astro"
          ></textarea>

          {{-- Source count indicator — only shows when real sources are attached. --}}
          <span x-show="sourceCount > 0" x-cloak class="flex flex-none items-center gap-1.5 self-center rounded-full bg-[rgba(115,182,255,0.12)] px-2 py-0.5 text-xs text-muted" title="Sources attached to this chat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M5 3h9l5 5v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/></svg>
            <span x-text="sourceCount"></span> sources
          </span>

          {{-- Voice dictation --}}
          <button type="button" @click="toggleMic()"
                  class="relative grid h-10 w-10 flex-none place-items-center rounded-full transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#73b6ff]"
                  :class="listening ? 'bg-[rgba(255,107,129,0.18)] text-[#ff6b81]' : 'text-muted hover:bg-white/10 hover:text-ink'"
                  :aria-pressed="listening.toString()" :aria-label="listening ? 'Stop recording' : 'Start voice input'" :title="listening ? 'Stop recording' : 'Dictate with your voice'">
            <span x-show="listening" x-cloak class="absolute inset-0 animate-ping rounded-full bg-[rgba(255,107,129,0.25)] motion-reduce:hidden"></span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="relative h-5 w-5"><rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3"/></svg>
          </button>

          <button type="submit" :disabled="sending || (!input.trim() && !attachments.length)" class="tl-cta grid h-10 w-10 flex-none place-items-center rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] text-[#07142e] shadow-[0_8px_28px_rgba(115,182,255,0.4)] transition hover:-translate-y-[2px] hover:shadow-[0_12px_36px_rgba(115,182,255,0.55)] disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:translate-y-0" aria-label="Send" title="Send">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M12 19V5M6 11l6-6 6 6"/></svg>
          </button>
        </div>
      </div>

      <div x-show="notice" x-cloak role="alert" class="mt-2 flex items-start gap-2 rounded-2xl border border-[rgba(255,107,129,0.35)] bg-[rgba(255,107,129,0.10)] px-3.5 py-2 text-[12.5px] text-[#ffc2cc]">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-4 w-4 flex-none"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16.5h.01"/></svg>
        <span x-text="notice"></span>
      </div>
    </form>
    <p class="mx-auto mt-2 max-w-3xl text-center text-[11px] text-muted">
      Astro can make mistakes. Double-check important info — and never share passwords or personal data.
    </p>
  </div>
</main>
