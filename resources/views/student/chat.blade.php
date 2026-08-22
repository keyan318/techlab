{{--
  /chat — TechLab design shell (DESIGN ONLY), rebranded to the landing page.
  Three-column Gemini-Notebook-style layout: Sources | Chat | Studio.
  Visual tokens pulled 1:1 from resources/views/landingpage.blade.php:
  cosmic palette (#06061a void, blue/violet/cyan accents), glass surfaces,
  Inter body + Space Grotesk headings + Space Mono labels, 20px cards /
  14px icon chips / 999px pills, gradient primary CTA. No lime.
  Built with Blade partials + Alpine.js (CDN) + Tailwind (Play CDN, tokens
  inline). For production: move tokens into tailwind.config.js, build via
  Vite, and load Alpine from resources/js/app.js.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TechLab · Chat with Astro</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Fonts: match landing page (Space Grotesk + Inter + Space Mono) --}}
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />

  {{-- Tailwind (Play CDN) + TechLab landing-page tokens --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            void: '#06061a',
            blue: '#73b6ff',
            violet: '#9b6bff',
            cyan: '#5be1ff',
            ink: '#eaeeff',
            muted: '#98a2d4',
            glass: 'rgba(123,142,220,0.07)',
            glassBorder: 'rgba(150,170,255,0.18)',
          },
          fontFamily: {
            sans: ['Inter', 'system-ui', 'sans-serif'],
            display: ['Space Grotesk', 'sans-serif'],
            mono: ['Space Mono', 'monospace'],
          },
        },
      },
    };
  </script>

  {{-- Alpine (core + collapse plugin) --}}
  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Infographic Studio store (global, shared across Studio sidebar + chat) --}}
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.store('infographic', {
        isOpen: false,
        state: 'idle',            // idle | configuring | generating | ready | error
        transcript: '',           // synced from the live chat thread
        data: null,               // the generated infographic plan
        error: '',
        errorKind: '',
        current: 0,
        phase: 0,
        phases: [
          'Reading your learning material',
          'Mapping the important concepts',
          'Designing the visual lesson',
          'Preparing the illustrations',
          'Launching your infographic',
        ],
        _timer: null,

        open() {
          if (this.isOpen) return;
          this.isOpen = true;
          this.state = 'configuring';
          this.error = '';
          this.errorKind = '';
        },

        close() {
          this.isOpen = false;
          this.state = 'idle';
          this.data = null;
          this.error = '';
          this.errorKind = '';
          this.current = 0;
          this.phase = 0;
          this.stopPhases();
        },

        async generate() {
          const text = (this.transcript || '').trim();
          if (! text) {
            this.error = "Chat with Astro first — the infographic is built from your current conversation.";
            this.errorKind = 'empty_source';
            this.state = 'configuring';
            return;
          }

          this.state = 'generating';
          this.phase = 0;
          this.data = null;
          this.error = '';
          this.errorKind = '';
          this.current = 0;
          this.startPhases();

          // Build payload from the live chat transcript (no paste/upload).
          const messages = (() => {
            try {
              const raw = (this.transcript || '').trim();
              if (!raw) return [];
              // transcriptText() is "Role: content" joined by blank lines.
              // Parse back to {role, content} pairs for the transcript resolver.
              return raw.split(/\n\n+/).map(b => {
                const i = b.indexOf(':');
                if (i === -1) return { role: 'user', content: b.trim() };
                const label = b.slice(0, i).trim();
                const content = b.slice(i + 1).trim();
                return { role: label === 'Astro' ? 'assistant' : 'user', content };
              }).filter(m => m.content);
            } catch (e) { return []; }
          })();

          const csrf = document.querySelector('meta[name="csrf-token"]')
            ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            : '';

          try {
            const res = await fetch('/chat/infographic', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
              body: JSON.stringify({ transcript: messages }),
            });
            const j = await res.json().catch(() => ({}));
            this.stopPhases();

            if (! res.ok || ! j.ok) {
              this.error = (j && j.error) ? j.error : "Astro couldn't build your infographic just now. Please try again.";
              this.errorKind = (j && j.kind) ? j.kind : 'generation';
              this.state = 'error';
              return;
            }

            this.data = j.infographic;
            this.current = 0;
            this.state = 'ready';
          } catch (e) {
            this.stopPhases();
            this.error = "Astro couldn't connect right now. Please try again in a moment.";
            this.errorKind = 'network';
            this.state = 'error';
          }
        },

        startPhases() {
          this.phase = 0;
          this._timer = setInterval(() => {
            if (this.phase < this.phases.length - 1) this.phase++;
          }, 2600);
        },

        stopPhases() {
          if (this._timer) { clearInterval(this._timer); this._timer = null; }
        },

        retry() { this.generate(); },

        next() { if (this.current < this.slideCount - 1) this.current++; },
        prev() { if (this.current > 0) this.current--; },
        goto(i) { if (i >= 0 && i < this.slideCount) this.current = i; },

        get slideCount() {
          return (this.data && Array.isArray(this.data.slides)) ? this.data.slides.length : 0;
        },
      });

      Alpine.store('quiz', {
        isOpen: false,
        // configuring | generating | answering | results | error
        state: 'idle',
        quiz: null,               // normalized { title, description, topic, question_count, questions }
        error: '',
        errorKind: '',
        conversationId: null,     // synced from astroChat
        // answering state
        current: 0,
        answers: {},              // { qId: 'A'|'B'|'C'|'D' }
        revealed: false,          // whether current question's correctness has been revealed (for review flow, not enforced)
        generating: false,

        open() {
          if (this.isOpen) return;
          // sync conversationId from astroChat if not yet set
          const ctrl = document.querySelector('[x-data="astroChat()"]');
          // fallback: try to read from the astroChat component via Alpine
          try {
            const el = document.querySelector('[x-data="astroChat()"]');
            if (el && el._x_dataStack) {
              const d = el._x_dataStack[0];
              if (d && d.conversationId) this.conversationId = d.conversationId;
            }
          } catch(e) {}
          this.isOpen = true;
          if (!this.quiz) this.state = 'configuring';
          else if (Object.keys(this.answers).length > 0 && this.current >= this.total) this.state = 'results';
          else if (this.quiz) this.state = 'answering';
          else this.state = 'configuring';
          this.error = '';
          this.errorKind = '';
        },

        close() {
          this.isOpen = false;
        },

        reset() {
          this.quiz = null;
          this.current = 0;
          this.answers = {};
          this.revealed = false;
          this.error = '';
          this.errorKind = '';
          this.state = 'configuring';
        },

        get total() { return this.quiz && Array.isArray(this.quiz.questions) ? this.quiz.questions.length : 0; },
        get score() {
          if (!this.quiz || !Array.isArray(this.quiz.questions)) return 0;
          let s = 0;
          for (const q of this.quiz.questions) {
            const a = this.answers[q.id];
            if (a && a === q.correct_key) s += (q.points || 1);
          }
          return s;
        },
        get maxScore() {
          if (!this.quiz || !Array.isArray(this.quiz.questions)) return 0;
          return this.quiz.questions.reduce((n,q)=> n + (q.points||1), 0);
        },
        get answeredCount() { return Object.keys(this.answers).length; },
        get canGenerate() { return !!this.conversationId; },

        selectAnswer(key) {
          const q = this.quiz && this.quiz.questions ? this.quiz.questions[this.current] : null;
          if (!q) return;
          this.answers[q.id] = key;
        },

        next() {
          if (!this.quiz) return;
          if (this.current < this.total - 1) {
            this.current++;
            this.revealed = false;
          } else {
            this.state = 'results';
          }
        },

        prev() {
          if (this.current > 0) { this.current--; this.revealed = false; }
        },

        retry() {
          this.reset();
          this.generate();
        },

        async generate() {
          if (!this.conversationId) {
            this.error = 'Chat with Astro first — the quiz is built from your current conversation.';
            this.errorKind = 'insufficient_content';
            this.state = 'error';
            return;
          }
          this.generating = true;
          this.state = 'generating';
          this.error = '';
          this.errorKind = '';
          this.quiz = null;
          this.current = 0;
          this.answers = {};
          this.revealed = false;
          const csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
          try {
            const res = await fetch('/chat/quiz', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
              body: JSON.stringify({ conversation_id: this.conversationId }),
            });
            const j = await res.json().catch(()=>({}));
            this.generating = false;
            if (!res.ok || !j.ok) {
              this.error = (j && j.error) ? j.error : "Astro couldn't build your quiz just now. Please try again.";
              this.errorKind = (j && j.kind) ? j.kind : 'generation';
              this.state = 'error';
              return;
            }
            this.quiz = j.quiz;
            this.current = 0;
            this.answers = {};
            this.state = 'answering';
          } catch(e) {
            this.generating = false;
            this.error = "Astro couldn't connect right now. Please try again in a moment.";
            this.errorKind = 'network';
            this.state = 'error';
          }
        },
      });
    });
  </script>

  {{-- Markdown rendering for Astro's streamed replies (marked) + XSS-safe
       sanitization (DOMPurify). Loaded before Alpine so they are ready when a
       reply first arrives. --}}
  <script src="https://cdn.jsdelivr.net/npm/marked@12/marked.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dompurify@3/dist/purify.min.js"></script>

  <style>
    [x-cloak] { display: none !important; }
    .overflow-y-auto::-webkit-scrollbar { width: 8px; }
    .overflow-y-auto::-webkit-scrollbar-thumb { background: rgba(150,170,255,0.18); border-radius: 8px; }
    .overflow-y-auto::-webkit-scrollbar-track { background: transparent; }
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { animation-duration: .001ms !important; transition-duration: .001ms !important; }
    }
  </style>

  <script>
    function chatShell() {
      return {
        sourcesOpen: true,
        studioOpen: true,
        mobileDrawer: null,
        started: true,
        bannerDismissed: false,
        notifyDismissed: false,
        isMobile: window.matchMedia('(max-width: 1023px)').matches,
        toggleSources() {
          if (this.isMobile) { this.mobileDrawer = this.mobileDrawer === 'sources' ? null : 'sources'; }
          else { this.sourcesOpen = !this.sourcesOpen; }
        },
        toggleStudio() {
          if (this.isMobile) { this.mobileDrawer = this.mobileDrawer === 'studio' ? null : 'studio'; }
          else { this.studioOpen = !this.studioOpen; }
        },
        closeDrawer() { this.mobileDrawer = null; },
        init() {
          window.addEventListener('resize', () => {
            this.isMobile = window.matchMedia('(max-width: 1023px)').matches;
            if (!this.isMobile) this.mobileDrawer = null;
          });
        },
      };
    }

    /**
     * Astro chat controller — the REAL client for the /chat/message endpoint.
     *
     * Flow: push the user's message + an empty Astro bubble, POST to the
     * backend (which talks to NVIDIA NIM server-side), then read the streamed
     * text/plain response token-by-token and render it live with markdown.
     * The backend owns the conversation/memory; we just keep the conversation
     * id so follow-up turns are threaded.
     */
    function astroChat() {
      return {
        messages: [],            // { role, content, html, pending, error, errorMessage, id }
        input: '',
        sending: false,
        sourceCount: 0,          // real sources attached to this chat (0 for now)
        conversationId: null,
        assistantMessageId: null,
        bannerDismissed: false,

        init() {
          // Keep the Infographic + Quiz stores in sync so Studio outputs can
          // use the current conversation (infographic uses transcript, quiz uses conversation_id).
          const syncStores = () => {
            const ig = this.$store.infographic;
            if (ig) ig.transcript = this.transcriptText();
            const qz = this.$store.quiz;
            if (qz) {
              qz.conversationId = this.conversationId;
              // keep a short preview for the configuring screen
              qz._preview = (this.messages || []).filter(m=>m.content && m.content!=='').slice(-2).map(m=> (m.role==='assistant'?'Astro':'You')+': '+m.content.slice(0,120)).join(' — ');
            }
          };
          this.$watch('messages', syncStores);
          this.$watch('conversationId', syncStores);
          if (this.messages.length) syncStores();
          // also sync once conversationId is set after first message
          this.$watch('conversationId', (v) => {
            const qz = this.$store.quiz;
            if (qz) qz.conversationId = v;
          });
        },

        transcriptText() {
          return this.messages
            .filter((m) => m.content && m.content !== '')
            .map((m) => (m.role === 'assistant' ? 'Astro' : 'Student') + ': ' + m.content)
            .join('\n\n');
        },

        scrollToBottom() {
          this.$nextTick(() => {
            const el = document.getElementById('chat-scroll');
            if (el) el.scrollTop = el.scrollHeight;
          });
        },

        sendSuggestion(text) {
          this.input = text;
          this.send();
        },

        newChat() {
          this.messages = [];
          this.conversationId = null;
          this.assistantMessageId = null;
        },

        async send() {
          const text = (this.input || '').trim();
          if (!text || this.sending) return;

          this.input = '';
          this.messages.push({ role: 'user', content: text });
          // Push the assistant placeholder, then grab the REACTIVE proxy element
          // (this.messages[last]) — mutating the raw object we pushed would NOT
          // trigger Alpine's reactivity, so the streamed text would never reach
          // the DOM. Mutating the proxy element does.
          this.messages.push({
            role: 'assistant',
            content: '',
            html: '',
            pending: true,
            error: false,
            errorMessage: '',
          });
          const idx = this.messages.length - 1;
          // Mutating a nested object property (e.g. astro.content = ...) does NOT
          // reliably trigger Alpine's reactivity for x-html/x-show, so the
          // streamed text never reaches the DOM. Reassigning the array element
          // via its index IS tracked, so the bubble re-renders on every chunk.
          const setAstro = (patch) => {
            this.messages[idx] = { ...this.messages[idx], ...patch };
          };
          this.sending = true;
          this.scrollToBottom();

          const csrf = document.querySelector('meta[name="csrf-token"]')
            ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            : '';

          try {
            const res = await fetch('/chat/message', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'text/plain, */*',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
              },
              body: JSON.stringify({
                message: text,
                conversation_id: this.conversationId,
                level: 'auto',
                context: '',
              }),
            });

            if (!res.ok || !res.body) {
              let msg = "Astro couldn't respond right now. Please try again.";
              try {
                const j = await res.json();
                if (j && j.error) msg = j.error;
              } catch (e) { /* keep default */ }
              setAstro({ pending: false, error: true, content: msg, html: this.md(msg) });
              this.sending = false;
              this.scrollToBottom();
              return;
            }

            const reader = res.body.getReader();
            const decoder = new TextDecoder();
            let raw = '';
            let metaParsed = false;

            while (true) {
              const { value, done } = await reader.read();
              if (done) break;
              raw += decoder.decode(value, { stream: true });

              // The first line is JSON meta: { conversation_id, user_message_id, assistant_message_id }.
              if (!metaParsed) {
                const nl = raw.indexOf('\n');
                if (nl === -1) continue;
                const metaLine = raw.slice(0, nl);
                raw = raw.slice(nl + 1);
                try {
                  const meta = JSON.parse(metaLine);
                  if (meta.conversation_id) { this.conversationId = meta.conversation_id; const qz=this.$store.quiz; if(qz) qz.conversationId = meta.conversation_id; }
                  if (meta.assistant_message_id) this.assistantMessageId = meta.assistant_message_id;
                } catch (e) { /* non-fatal */ }
                metaParsed = true;
              }

              const content = this.stripSentinel(raw);
              setAstro({ content, html: this.md(content) });
              this.scrollToBottom();
            }

            // Finalize.
            const finalContent = this.stripSentinel(raw);
            const patch = { content: finalContent, html: this.md(finalContent), pending: false };

            const errIdx = raw.indexOf('__ERROR__:');
            if (errIdx !== -1) {
              let ej = null;
              try { ej = JSON.parse(raw.slice(errIdx + '__ERROR__:'.length)); } catch (e) { /* ignore */ }
              patch.error = true;
              patch.errorMessage = (ej && ej.message)
                ? ej.message
                : "Astro couldn't respond right now. Please try again.";
              patch.content = patch.errorMessage;
              patch.html = this.md(patch.errorMessage);
            }
            setAstro(patch);
          } catch (e) {
            setAstro({
              pending: false,
              error: true,
              content: "Astro couldn't connect right now. Please try again.",
              html: this.md("Astro couldn't connect right now. Please try again."),
            });
          }

          this.sending = false;
          this.scrollToBottom();
        },

        // Drop the terminal markers the backend appends (__END__ / __ERROR__).
        stripSentinel(raw) {
          let c = raw;
          const endIdx = c.indexOf('__END__');
          if (endIdx !== -1) c = c.slice(0, endIdx);
          const errIdx = c.indexOf('__ERROR__');
          if (errIdx !== -1) c = c.slice(0, errIdx);
          return c;
        },

        // Markdown -> sanitized HTML (never trusts raw model output).
        md(text) {
          if (!text) return '';
          try {
            const dirty = marked.parse(text);
            return DOMPurify.sanitize(dirty);
          } catch (e) {
            return text
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/\n/g, '<br>');
          }
        },

        copy(text) {
          if (navigator.clipboard) {
            navigator.clipboard.writeText(text || '').catch(() => {});
          }
        },
      };
    }
  </script>
</head>

<body class="bg-void font-sans text-ink antialiased" x-data="chatShell()">
  {{-- Space background (landing-page gradient + brand glows) --}}
  <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
    <div class="absolute inset-0" style="background: radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%), radial-gradient(100% 80% at 85% 110%, #1a0f4d 0%, rgba(26,15,77,0) 60%), linear-gradient(160deg, #0a0826 0%, #120a33 45%, #1e1259 100%);"></div>
    <div class="absolute -left-24 -top-28 h-[460px] w-[460px] rounded-full opacity-50 blur-[70px]" style="background: radial-gradient(circle, rgba(155,107,255,0.5), transparent 70%);"></div>
    <div class="absolute -bottom-36 -right-28 h-[520px] w-[520px] rounded-full opacity-50 blur-[70px]" style="background: radial-gradient(circle, rgba(91,225,255,0.35), transparent 70%);"></div>
  </div>

  <div class="flex h-screen w-screen overflow-hidden">

    {{-- COLUMN 1 — Sources --}}
    @include('student.chat.partials.sources-panel')

    {{-- COLUMN 2 — Chat --}}
    @include('student.chat.partials.chat-panel')

    {{-- COLUMN 3 — Studio --}}
    @include('student.chat.partials.studio-panel')

    {{-- Infographic Studio overlay (source modal + deck viewer) --}}
    @include('student.chat.partials.infographic-viewer')
    @include('student.chat.partials.quiz-viewer')

    {{-- Mobile backdrop for off-canvas drawers --}}
    <div
      x-show="mobileDrawer" x-cloak @click="closeDrawer()"
      x-transition.opacity
      class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm lg:hidden"
    ></div>

    {{-- Desktop reopen tabs (shown only when a side column is collapsed) --}}
    <button
      x-show="!sourcesOpen" x-cloak @click="sourcesOpen = true"
      class="fixed left-0 top-1/2 z-20 hidden h-16 w-7 -translate-y-1/2 items-center justify-center rounded-r-lg border border-l-0 border-glassBorder bg-[rgba(6,6,26,0.6)] text-muted backdrop-blur-xl transition hover:text-[#73b6ff] lg:flex"
      title="Open sources" aria-label="Open sources"
    >
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m9 6 6 6-6 6"/></svg>
    </button>
    <button
      x-show="!studioOpen" x-cloak @click="studioOpen = true"
      class="fixed right-0 top-1/2 z-20 hidden h-16 w-7 -translate-y-1/2 items-center justify-center rounded-l-lg border border-r-0 border-glassBorder bg-[rgba(6,6,26,0.6)] text-muted backdrop-blur-xl transition hover:text-[#73b6ff] lg:flex"
      title="Open studio" aria-label="Open studio"
    >
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m15 6-6 6 6 6"/></svg>
    </button>

  </div>
</body>
</html>
