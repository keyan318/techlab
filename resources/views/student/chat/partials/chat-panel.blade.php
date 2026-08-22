{{-- COLUMN 2 — Chat (center, primary focus, widest column).
     Driven by the `astroChat()` Alpine component (defined in chat.blade.php).
     Messages are rendered dynamically from the `messages` array and Astro's
     reply is streamed live from the /chat/message endpoint. --}}
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

      {{-- Overflow menu --}}
      <div x-data="{ menu: false }" @click.outside="menu = false" class="relative">
        <button type="button" @click="menu = !menu" class="grid h-9 w-9 flex-none place-items-center rounded-[10px] border border-glassBorder bg-glass text-muted transition hover:border-[rgba(115,182,255,0.5)] hover:text-ink" aria-label="More options" :aria-expanded="menu">
          <svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><circle cx="5" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="19" cy="12" r="1.8"/></svg>
        </button>
        <div x-show="menu" x-cloak @click="menu = false" class="absolute right-0 top-11 z-30 w-44 overflow-hidden rounded-[14px] border border-glassBorder bg-[rgba(6,6,26,0.85)] py-1 shadow-[0_20px_50px_rgba(10,10,40,0.6)] backdrop-blur-xl">
          <button class="block w-full px-3 py-2 text-left text-sm text-ink transition hover:bg-glass" @click="newChat()">Clear conversation</button>
          <button class="block w-full px-3 py-2 text-left text-sm text-ink transition hover:bg-glass" @click="newChat()">New chat</button>
          <button class="block w-full px-3 py-2 text-left text-sm text-ink transition hover:bg-glass">Chat settings</button>
        </div>
      </div>

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
        <h2 class="mt-4 font-display text-2xl font-bold tracking-tight text-ink">Hi, I'm Astro — let's figure this out together</h2>
        <p class="mt-2 max-w-md text-[15px] leading-relaxed text-muted">
          No question is too small. Ask me anything from your lessons and I'll explain it like we're just hanging out. Stuck? I'll break it down, step by step. 🚀
        </p>

        {{-- Suggested prompt chips — these actually send a message. --}}
        <div class="mt-6 w-full max-w-md space-y-2">
          <button type="button" @click="sendSuggestion('Explain HTTP like I\'m five')" class="flex w-full items-center gap-3 rounded-full border border-glassBorder bg-glass px-4 py-3 text-left text-sm text-ink transition hover:border-[rgba(115,182,255,0.5)] hover:bg-[rgba(115,182,255,0.08)]">
            <span class="text-[#5be1ff]">✦</span> Explain HTTP like I'm five
          </button>
          <button type="button" @click="sendSuggestion('Quiz me on basic networking')" class="flex w-full items-center gap-3 rounded-full border border-glassBorder bg-glass px-4 py-3 text-left text-sm text-ink transition hover:border-[rgba(115,182,255,0.5)] hover:bg-[rgba(115,182,255,0.08)]">
            <span class="text-[#5be1ff]">✦</span> Quiz me on basic networking
          </button>
          <button type="button" @click="sendSuggestion('Help me debug my first Laravel route')" class="flex w-full items-center gap-3 rounded-full border border-glassBorder bg-glass px-4 py-3 text-left text-sm text-ink transition hover:border-[rgba(115,182,255,0.5)] hover:bg-[rgba(115,182,255,0.08)]">
            <span class="text-[#5be1ff]">✦</span> Help me debug my first Laravel route
          </button>
        </div>
      </section>

      {{-- ===== Live thread ===== --}}
      <section x-show="messages.length > 0" x-cloak class="space-y-6">
        {{-- Dismissible feature-tip banner --}}
        <div x-show="!bannerDismissed" x-cloak class="flex items-start gap-2.5 rounded-[14px] border border-[rgba(115,182,255,0.35)] bg-[rgba(115,182,255,0.08)] px-3 py-2.5">
          <svg viewBox="0 0 24 24" fill="none" stroke="#5be1ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-4 w-4 flex-none"><path d="M15 4V2M15 16v-2M8 9h2M20 9h2M17.8 11.8 19 13M15 9h.01M17.8 6.2 19 5M3 21l9-9M12.2 6.2 11 5"/></svg>
          <p class="flex-1 text-xs leading-relaxed text-ink">Tip: ask Astro to turn any answer into a <span class="font-semibold text-[#5be1ff]">Quiz</span> or <span class="font-semibold text-[#5be1ff]">Flashcards</span> from the Studio panel.</p>
          <button type="button" @click="bannerDismissed = true" class="text-muted transition hover:text-ink" title="Dismiss" aria-label="Dismiss">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18M6 6l12 12"/></svg>
          </button>
        </div>

        <template x-for="(m, i) in messages" :key="i">
          <div>
            {{-- User message --}}
            <div x-show="m.role === 'user'" class="flex justify-end">
              <div class="max-w-[85%] whitespace-pre-wrap rounded-2xl rounded-tr-md border border-[rgba(115,182,255,0.35)] bg-[rgba(115,182,255,0.12)] px-4 py-2.5 text-sm leading-relaxed text-ink" x-text="m.content"></div>
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

                {{-- Responding indicator (before any token arrives) --}}
                <div x-show="m.pending && m.content === ''" class="flex items-center gap-1.5 py-1 text-muted">
                  <span class="h-2 w-2 animate-pulse rounded-full bg-[#73b6ff]" style="animation-delay:0ms"></span>
                  <span class="h-2 w-2 animate-pulse rounded-full bg-[#73b6ff]" style="animation-delay:150ms"></span>
                  <span class="h-2 w-2 animate-pulse rounded-full bg-[#73b6ff]" style="animation-delay:300ms"></span>
                  <span class="ml-1 text-xs">Astro is thinking…</span>
                </div>

                {{-- Streamed answer --}}
                <div
                  x-show="m.content !== ''"
                  class="text-[15px] leading-relaxed text-ink/90 [&_a]:text-[#73b6ff] [&_a]:underline [&_blockquote]:my-2 [&_blockquote]:border-l-2 [&_blockquote]:border-[#73b6ff] [&_blockquote]:bg-[rgba(115,182,255,0.08)] [&_blockquote]:py-1 [&_blockquote]:pl-3 [&_blockquote]:pr-2 [&_blockquote]:text-sm [&_code]:rounded [&_code]:bg-[rgba(115,182,255,0.12)] [&_code]:px-1 [&_code]:py-0.5 [&_code]:text-[#5be1ff] [&_h3]:mb-1 [&_h3]:mt-3 [&_h3]:font-display [&_h3]:text-base [&_h3]:font-semibold [&_li]:ml-5 [&_li]:list-disc [&_p]:mb-2 [&_strong]:font-semibold [&_strong]:text-ink [&_ul]:mb-2"
                  x-html="m.html"
                  :class="{ 'animate-pulse opacity-70': m.pending }"
                ></div>

                {{-- Action row (only once the reply is settled) --}}
                <div
                  x-show="!m.pending && m.content !== ''"
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
  <div class="border-t border-glassBorder px-4 py-3 sm:px-6">
    <form @submit.prevent="send()" class="mx-auto flex max-w-3xl items-end gap-2">
      <div class="flex flex-1 items-center gap-2 rounded-full border border-glassBorder bg-glass px-4 py-2 transition focus-within:border-[rgba(115,182,255,0.5)]">
        <textarea
          x-model="input"
          rows="1"
          @keydown.enter.prevent="if (!($event.shiftKey)) send()"
          placeholder="Ask Astro or create something"
          class="max-h-32 flex-1 resize-none bg-transparent text-[15px] text-ink placeholder:text-muted/70 focus:outline-none"
          oninput="this.style.height='auto'; this.style.height=Math.min(this.scrollHeight,128)+'px'"
          aria-label="Message Astro"
        ></textarea>
        {{-- Source count indicator — only shows when real sources are attached. --}}
        <span x-show="sourceCount > 0" x-cloak class="flex flex-none items-center gap-1.5 rounded-full bg-[rgba(115,182,255,0.12)] px-2 py-0.5 text-xs text-muted" title="Sources attached to this chat">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M5 3h9l5 5v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/></svg>
          <span x-text="sourceCount"></span> sources
        </span>
      </div>
      <button type="submit" :disabled="sending || !input.trim()" class="grid h-11 w-11 flex-none place-items-center rounded-full bg-[linear-gradient(100deg,#5be1ff,#73b6ff_55%,#9b6bff)] text-[#07142e] shadow-[0_10px_40px_rgba(115,182,255,0.45)] transition hover:-translate-y-[3px] hover:shadow-[0_16px_50px_rgba(115,182,255,0.6)] disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:translate-y-0" aria-label="Send" title="Send">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M12 19V5M6 11l6-6 6 6"/></svg>
      </button>
    </form>
    <p class="mx-auto mt-2 max-w-3xl text-center text-[11px] text-muted">
      Astro can make mistakes. Double-check important info — and never share passwords or personal data.
    </p>
  </div>
</main>
