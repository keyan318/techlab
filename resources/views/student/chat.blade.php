@php $isFacultyChat = ($chatMode ?? 'student') === 'faculty'; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TechLab · {{ $isFacultyChat ? 'Astro for Captains' : 'Chat with Astro' }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />

    <meta name="theme-color" content="#06061a">
  <link rel="stylesheet" href="{{ asset('css/theme.css') }}?v={{ filemtime(public_path('css/theme.css')) }}">
  <script src="{{ asset('js/theme.js') }}?v={{ filemtime(public_path('js/theme.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            void:        'rgb(var(--c-void) / <alpha-value>)',
            blue:        'rgb(var(--c-blue) / <alpha-value>)',
            violet:      'rgb(var(--c-violet) / <alpha-value>)',
            cyan:        'rgb(var(--c-cyan) / <alpha-value>)',
            ink:         'rgb(var(--c-ink) / <alpha-value>)',
            muted:       'rgb(var(--c-muted) / <alpha-value>)',
            glass:       'var(--glass)',
            glassBorder: 'var(--glass-border)',
          },
          fontFamily: {
            sans:    ['Inter', 'system-ui', 'sans-serif'],
            display: ['Space Grotesk', 'sans-serif'],
            mono:    ['Space Mono', 'monospace'],
          },
        },
      },
    };
  </script>

  {{-- Astro's deterministic status lines + state (public/js/astro-status.js) --}}
  <script src="{{ asset('js/astro-status.js') }}?v={{ filemtime(public_path('js/astro-status.js')) }}"></script>

  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/html-to-image@1.11.11/dist/html-to-image.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Alpine stores: Infographic, Quiz, Conversation --}}
  <script>
    document.addEventListener('alpine:init', () => {

      // Infographic: style picker -> background generation (Studio progress card) -> poster viewer + PNG download.
      Alpine.store('infographic', {
        isOpen: false,       // poster viewer
        configOpen: false,   // style picker
        state: 'idle',       // idle | generating | ready | error
        transcript: '',
        data: null,
        error: '',
        errorKind: '',
        generating: false,
        ready: false,        // finished in the background, not opened yet
        style: 'cartoon',
        styles: [
          { id: 'cartoon', label: 'Cartoon', hint: 'Playful, bold outlines, bright colors' },
          { id: 'anime', label: 'Anime', hint: 'Expressive characters, cel-shaded, vivid' },
        ],
        sourceCount: 0,
        downloading: false,
        downloadError: '',

        // Studio card click: view a finished one, otherwise pick a style.
        open() {
          if (this.generating) return;
          if (this.data && this.state === 'ready') { this.show(); return; }
          if (this.state === 'error') { this.ready = false; this.isOpen = true; return; }
          this.error = ''; this.configOpen = true;
        },
        show() { this.ready = false; this.isOpen = true; },
        close() { this.isOpen = false; this.downloadError = ''; },
        closeConfig() { this.configOpen = false; },
        newOne() { this.isOpen = false; this.state = 'idle'; this.configOpen = true; },
        openSaved(item) { this.data = item.infographic; this.style = item.style || this.style; this.state = 'ready'; this.ready = false; this.isOpen = true; },
        retry() { this.isOpen = false; this.generate(); },

        snapshotSourceCount() {
          try {
            const ps = Alpine.store('planetSources');
            return ps.items.filter(i => i.active).length + Alpine.store('webSources').items.length;
          } catch (e) { return 0; }
        },

        async generate() {
          const text = (this.transcript || '').trim();
          this.configOpen = false;
          if (!text) {
            this.error = "Chat with Astro first — the infographic is built from your current conversation.";
            this.errorKind = 'empty_source';
            this.state = 'error'; this.isOpen = true;
            return;
          }

          this.generating = true; this.ready = false;
          this.state = 'generating'; this.error = ''; this.errorKind = '';
          this.sourceCount = this.snapshotSourceCount();

          const messages = text.split(/\n\n+/).map(b => {
            const i = b.indexOf(':');
            if (i === -1) return { role: 'user', content: b.trim() };
            return { role: b.slice(0, i).trim() === 'Astro' ? 'assistant' : 'user', content: b.slice(i + 1).trim() };
          }).filter(m => m.content);

          const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
          try {
            const res = await fetch('/chat/infographic', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
              body: JSON.stringify({ transcript: messages, style: this.style }),
            });
            const j = await res.json().catch(() => ({}));
            this.generating = false;

            if (!res.ok || !j.ok) {
              this.error = (j && j.error) ? j.error : "Astro couldn't build your infographic just now. Please try again.";
              this.errorKind = (j && j.kind) ? j.kind : 'generation';
              this.state = 'error';
              if (!this.isOpen) this.ready = true;
              return;
            }

            this.data = j.infographic;
            this.state = 'ready';
            Alpine.store('studioOutputs').add({
              id: 'ig-' + Date.now().toString(36), type: 'infographic', title: j.infographic.title,
              subtitle: (this.style === 'anime' ? 'Anime' : 'Cartoon') + ' style',
              createdAt: Date.now(), infographic: j.infographic, style: this.style,
            });
            if (!this.isOpen) this.ready = true;
          } catch (e) {
            this.generating = false;
            this.error = "Astro couldn't connect right now. Please try again in a moment.";
            this.errorKind = 'network';
            this.state = 'error';
            if (!this.isOpen) this.ready = true;
          }
        },

        // Render the poster stage to a PNG at 2x and save it.
        async download() {
          const node = document.getElementById('infographic-poster');
          if (!node || this.downloading) return;
          this.downloading = true; this.downloadError = '';
          try {
            const url = await window.htmlToImage.toPng(node, {
              width: 1280, height: 720, pixelRatio: 2, cacheBust: true,
              style: { transform: 'none', left: '0', top: '0' },
            });
            const a = document.createElement('a');
            const slug = ((this.data && this.data.title) || 'infographic').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            a.href = url; a.download = (slug || 'infographic') + '.png';
            document.body.appendChild(a); a.click(); a.remove();
          } catch (e) {
            this.downloadError = "Couldn't create the image. Please try again.";
          }
          this.downloading = false;
        },
      });

      Alpine.store('quiz', {
        isOpen: false,
        state: 'idle',
        quiz: null,
        error: '',
        errorKind: '',
        conversationId: null,
        current: 0,
        answers: {},
        revealed: false,
        generating: false,
        count: 5,
        ready: false,        // finished in the background, not opened yet
        counts: [5, 10, 15, 20],

        open() {
          if (this.isOpen) return;
          this.ready = false;
          try {
            const el = document.querySelector('[x-data="astroChat()"]');
            if (el && el._x_dataStack) {
              const d = el._x_dataStack[0];
              if (d && d.conversationId) this.conversationId = d.conversationId;
            }
          } catch(e) {}
          this.isOpen = true;
          if (this.generating) this.state = 'generating';
          else if (!this.quiz && this.state === 'error') this.state = 'error';
          else if (!this.quiz) this.state = 'configuring';
          else if (Object.keys(this.answers).length > 0 && this.current >= this.total) this.state = 'results';
          else if (this.quiz) this.state = 'answering';
          else this.state = 'configuring';
          if (this.state !== 'error') { this.error = ''; this.errorKind = ''; }
        },

        close() { this.isOpen = false; },

        reset() {
          this.quiz = null;
          this.current = 0;
          this.answers = {};
          this.revealed = false;
          this.error = '';
          this.errorKind = '';
          this.state = 'configuring';
        },

        get total()         { return this.quiz && Array.isArray(this.quiz.questions) ? this.quiz.questions.length : 0; },
        get score()         {
          if (!this.quiz || !Array.isArray(this.quiz.questions)) return 0;
          let s = 0;
          for (const q of this.quiz.questions) {
            const a = this.answers[q.id];
            if (a && a === q.correct_key) s += (q.points || 1);
          }
          return s;
        },
        get maxScore()      {
          if (!this.quiz || !Array.isArray(this.quiz.questions)) return 0;
          return this.quiz.questions.reduce((n, q) => n + (q.points || 1), 0);
        },
        get answeredCount() { return Object.keys(this.answers).length; },
        get canGenerate()   { return !!this.conversationId; },

        selectAnswer(key) {
          const q = this.quiz && this.quiz.questions ? this.quiz.questions[this.current] : null;
          if (!q) return;
          this.answers[q.id] = key;
        },

        next() {
          if (!this.quiz) return;
          if (this.current < this.total - 1) { this.current++; this.revealed = false; }
          else { this.state = 'results'; }
        },

        prev() {
          if (this.current > 0) { this.current--; this.revealed = false; }
        },

        retry() { this.reset(); this.generate(); },

        // Kick off generation and get out of the way: the modal closes and the
        // Studio panel shows progress, so the student keeps chatting meanwhile.
        start() {
          if (this.generating || !this.canGenerate) return;
          this.isOpen = false;
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
          this.ready = false;
          this.state = 'generating';
          this.error = '';
          this.errorKind = '';
          this.quiz = null;
          this.current = 0;
          this.answers = {};
          this.revealed = false;
          const csrf = document.querySelector('meta[name="csrf-token"]')
            ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            : '';
          try {
            const res = await fetch('/chat/quiz', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
              body: JSON.stringify({ conversation_id: this.conversationId, count: this.count }),
            });
            const j = await res.json().catch(() => ({}));
            this.generating = false;
            if (!res.ok || !j.ok) {
              this.error = (j && j.error) ? j.error : "Astro couldn't build your quiz just now. Please try again.";
              this.errorKind = (j && j.kind) ? j.kind : 'generation';
              this.state = 'error';
              if (!this.isOpen) this.ready = true;   // surface the failure in the Studio card
              return;
            }
            this.quiz = j.quiz;
            this.current = 0;
            this.answers = {};
            this.state = 'answering';
            if (!this.isOpen) this.ready = true;
          } catch(e) {
            this.generating = false;
            if (!this.isOpen) this.ready = true;
            this.error = "Astro couldn't connect right now. Please try again in a moment.";
            this.errorKind = 'network';
            this.state = 'error';
          }
        },
      });

      // Saved Studio outputs (shown under the cards in the Studio panel). Kept in
      // localStorage per browser AND per user, so accounts sharing a browser never
      // see each other's outputs; every access is guarded so private mode still works.
      Alpine.store('studioOutputs', {
        items: [],
        key: 'astro.studio.outputs.v1:u{{ auth()->id() }}',
        init() {
          try { localStorage.removeItem('astro.studio.outputs.v1'); } catch (e) {}
          try { this.items = JSON.parse(localStorage.getItem(this.key) || '[]') || []; } catch (e) { this.items = []; }
          if (!Array.isArray(this.items)) this.items = [];
        },
        persist() { try { localStorage.setItem(this.key, JSON.stringify(this.items)); } catch (e) {} },
        add(item) {
          this.items = [item, ...this.items.filter(i => i.id !== item.id)].slice(0, 20);
          this.persist();
        },
        remove(id) { this.items = this.items.filter(i => i.id !== id); this.persist(); },
      });

      Alpine.store('flashcards', {
        isOpen: false,
        state: 'idle',       // idle | configuring | generating | studying | finished | error
        deck: null,
        deckId: null,
        error: '',
        errorKind: '',
        count: 10,
        counts: @json(config('flashcards.counts')),
        generating: false,
        ready: false,        // finished in the background, not opened yet
        order: [],           // card indexes in study order
        pos: 0,
        flipped: false,
        known: {},           // card id -> true

        get conversationId() { return Alpine.store('quiz').conversationId; },
        get canGenerate()    { return !!this.conversationId; },
        get total()          { return this.order.length; },
        get card()           { return this.deck ? this.deck.cards[this.order[this.pos]] : null; },
        get knownCount()     { return Object.keys(this.known).length; },
        get missedCount()    { return this.total - this.knownCount; },
        get isLast()         { return this.pos >= this.total - 1; },

        open() {
          if (this.isOpen) return;
          this.ready = false;
          this.isOpen = true;
          if (this.generating) this.state = 'generating';
          else if (this.state === 'error') { /* keep the error visible */ }
          else if (this.deck) this.state = this.state === 'finished' ? 'finished' : 'studying';
          else this.state = 'configuring';
        },
        close() { this.isOpen = false; },

        study(deck, id) {
          this.deck = deck;
          this.deckId = id || null;
          this.order = deck.cards.map((_, i) => i);
          this.pos = 0;
          this.flipped = false;
          this.known = {};
          this.error = '';
          this.state = 'studying';
        },
        openSaved(item) { this.isOpen = true; this.study(item.deck, item.id); },

        flip()  { this.flipped = !this.flipped; },
        // Past the last card the deck is done: show the summary instead of dead-ending.
        next()  { if (!this.isLast) { this.pos++; this.flipped = false; } else if (this.total > 0) { this.state = 'finished'; this.flipped = false; } },
        prev()  { if (this.pos > 0) { this.pos--; this.flipped = false; } },
        shuffle() {
          const a = this.order.slice();
          for (let i = a.length - 1; i > 0; i--) { const j = Math.floor(Math.random() * (i + 1)); [a[i], a[j]] = [a[j], a[i]]; }
          this.order = a; this.pos = 0; this.flipped = false;
        },
        mark(isKnown) {
          const c = this.card;
          if (!c) return;
          if (isKnown) this.known[c.id] = true; else delete this.known[c.id];
          this.next();
        },
        restart() { this.order = this.deck.cards.map((_, i) => i); this.pos = 0; this.flipped = false; this.known = {}; this.state = 'studying'; },
        // Second pass over only the cards not marked "Got it".
        reviewMissed() {
          const missed = this.order.filter(i => !this.known[this.deck.cards[i].id]);
          if (!missed.length) { this.restart(); return; }
          this.order = missed; this.pos = 0; this.flipped = false; this.state = 'studying';
        },
        newDeck() { this.deck = null; this.deckId = null; this.state = 'configuring'; },

        // Kick off generation and get out of the way: the modal closes and the
        // Studio panel shows progress, so the student keeps chatting meanwhile.
        start() {
          if (this.generating || !this.canGenerate) return;
          this.isOpen = false;
          this.generate();
        },

        async generate() {
          if (!this.conversationId) {
            this.error = 'Chat with Astro first — flashcards are built from your current conversation.';
            this.errorKind = 'insufficient_content';
            this.state = 'error';
            return;
          }
          this.generating = true;
          this.ready = false;
          this.state = 'generating';
          this.error = ''; this.errorKind = '';
          this.deck = null;
          const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
          try {
            const res = await fetch('/chat/flashcards', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
              body: JSON.stringify({ conversation_id: this.conversationId, count: this.count }),
            });
            const j = await res.json().catch(() => ({}));
            this.generating = false;
            if (!res.ok || !j.ok) {
              this.error = (j && j.error) ? j.error : "Astro couldn't build your flashcards just now. Please try again.";
              this.errorKind = (j && j.kind) ? j.kind : 'generation';
              this.state = 'error';
              if (!this.isOpen) this.ready = true;
              return;
            }
            const id = 'fc-' + Date.now().toString(36);
            this.study(j.deck, id);
            Alpine.store('studioOutputs').add({
              id, type: 'flashcards', title: j.deck.title, subtitle: j.deck.cards.length + ' cards' + (j.deck.topic ? ' · ' + j.deck.topic : ''),
              createdAt: Date.now(), deck: j.deck,
            });
            if (!this.isOpen) this.ready = true;
          } catch (e) {
            this.generating = false;
            this.error = "Astro couldn't connect right now. Please try again in a moment.";
            this.errorKind = 'network';
            this.state = 'error';
            if (!this.isOpen) this.ready = true;
          }
        },
      });

      // Key Terms: the important vocabulary from the chat, each explained to a kid (definition with **bold** key idea + "Imagine..." analogy).
      Alpine.store('keyterms', {
        isOpen: false,
        state: 'idle',       // idle | generating | reading | error
        data: null,
        dataId: null,
        error: '',
        errorKind: '',
        generating: false,
        ready: false,        // finished in the background, not opened yet

        get conversationId() { return Alpine.store('quiz').conversationId; },

        // Model text is escaped first; only balanced **x** becomes <strong>.
        fmt(s) {
          const esc = String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
          return esc.replace(/\*\*([^*]+)\*\*/g, '<strong class="font-semibold text-ink">$1</strong>');
        },

        // Studio card click: read finished terms, otherwise build them in the background.
        open() {
          if (this.generating) return;
          if (this.data) { this.ready = false; this.isOpen = true; this.state = 'reading'; return; }
          if (this.state === 'error') { this.ready = false; this.isOpen = true; return; }
          this.generate();
        },
        close() { this.isOpen = false; },
        openSaved(item) { this.data = item.terms; this.dataId = item.id; this.error = ''; this.state = 'reading'; this.ready = false; this.isOpen = true; },
        again() { this.data = null; this.dataId = null; this.isOpen = false; this.generate(); },

        async generate() {
          if (this.generating) return;
          if (!this.conversationId) {
            this.error = 'Chat with Astro first — key terms are picked from your current conversation.';
            this.errorKind = 'insufficient_content';
            this.state = 'error';
            this.isOpen = true;
            return;
          }
          this.generating = true;
          this.ready = false;
          this.state = 'generating';
          this.error = ''; this.errorKind = '';
          this.data = null;
          const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
          try {
            const res = await fetch('/chat/key-terms', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
              body: JSON.stringify({ conversation_id: this.conversationId }),
            });
            const j = await res.json().catch(() => ({}));
            this.generating = false;
            if (!res.ok || !j.ok) {
              this.error = (j && j.error) ? j.error : "Astro couldn't pick your key terms just now. Please try again.";
              this.errorKind = (j && j.kind) ? j.kind : 'generation';
              this.state = 'error';
              if (!this.isOpen) this.ready = true;
              return;
            }
            const id = 'kt-' + Date.now().toString(36);
            this.data = j.terms; this.dataId = id; this.state = 'reading';
            Alpine.store('studioOutputs').add({
              id, type: 'keyterms', title: j.terms.title, subtitle: j.terms.terms.length + ' terms' + (j.terms.topic ? ' · ' + j.terms.topic : ''),
              createdAt: Date.now(), terms: j.terms,
            });
            if (!this.isOpen) this.ready = true;
          } catch (e) {
            this.generating = false;
            this.error = "Astro couldn't connect right now. Please try again in a moment.";
            this.errorKind = 'network';
            this.state = 'error';
            if (!this.isOpen) this.ready = true;
          }
        },
      });

      // Reports: Astro turns the current chat into a sectioned study report (TOC + one section at a time).
      Alpine.store('reports', {
        isOpen: false,
        state: 'idle',       // idle | generating | reading | error
        report: null,
        reportId: null,
        sources: [],         // [{title, sub, url?}] snapshot taken when the report was generated
        error: '',
        errorKind: '',
        generating: false,
        ready: false,        // finished in the background, not opened yet
        idx: 0,
        tocOpen: true,
        expanded: false,
        sourcesOpen: false,
        menuOpen: false,
        rating: null,        // 'good' | 'bad'
        copied: false,

        get conversationId() { return Alpine.store('quiz').conversationId; },
        get section()        { return this.report ? this.report.sections[this.idx] : null; },
        get total()          { return this.report ? this.report.sections.length : 0; },
        get bodyHtml() {
          if (!this.section) return '';
          try { return DOMPurify.sanitize(marked.parse(this.section.body)); }
          catch (e) { return ''; }
        },

        // Studio card click: read a finished report, otherwise build one in the background.
        open() {
          this.refreshConversation();
          if (this.generating) return;
          if (this.report && this.state !== 'error') { this.show(); return; }
          if (this.state === 'error') { this.ready = false; this.isOpen = true; return; }
          this.generate();
        },
        show()  { this.ready = false; this.isOpen = true; this.state = 'reading'; },
        close() { this.isOpen = false; this.menuOpen = false; this.sourcesOpen = false; },
        openSaved(item) { this.read(item.report, item.id, item.sources || []); this.isOpen = true; },

        read(report, id, sources) {
          this.report = report; this.reportId = id || null; this.sources = sources || [];
          this.idx = 0; this.rating = null; this.error = ''; this.state = 'reading';
          this.menuOpen = false; this.sourcesOpen = false;
        },
        go(i) { if (i >= 0 && i < this.total) { this.idx = i; Alpine.nextTick(() => { const el = document.getElementById('report-scroll'); if (el) el.scrollTop = 0; }); } },
        next() { this.go(this.idx + 1); },
        prev() { this.go(this.idx - 1); },

        refreshConversation() {
          try {
            const el = document.querySelector('[x-data="astroChat()"]');
            const d = el && el._x_dataStack && el._x_dataStack[0];
            if (d && d.conversationId) Alpine.store('quiz').conversationId = d.conversationId;
          } catch (e) {}
        },

        snapshotSources() {
          const out = [];
          try {
            const ps = Alpine.store('planetSources');
            ps.items.filter(i => i.active).forEach(i => {
              const info = ps.labelFor(i.planet, i.module, i.lesson);
              if (info) out.push({ title: info.title, sub: info.sub });
            });
            Alpine.store('webSources').items.forEach(w => out.push({ title: w.title || w.domain, sub: w.domain, url: w.url }));
          } catch (e) {}
          return out;
        },

        async generate() {
          this.refreshConversation();
          if (!this.conversationId) {
            this.error = 'Chat with Astro first — reports are built from your current conversation.';
            this.errorKind = 'insufficient_content';
            this.state = 'error'; this.isOpen = true;
            return;
          }
          this.generating = true; this.ready = false; this.menuOpen = false;
          this.state = 'generating'; this.error = ''; this.errorKind = '';
          const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
          try {
            const res = await fetch('/chat/report', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
              body: JSON.stringify({ conversation_id: this.conversationId }),
            });
            const j = await res.json().catch(() => ({}));
            this.generating = false;
            if (!res.ok || !j.ok) {
              this.error = (j && j.error) ? j.error : "Astro couldn't write your report just now. Please try again.";
              this.errorKind = (j && j.kind) ? j.kind : 'generation';
              this.state = 'error';
              if (!this.isOpen) this.ready = true;
              return;
            }
            const id = 'rp-' + Date.now().toString(36);
            const sources = this.snapshotSources();
            this.read(j.report, id, sources);
            Alpine.store('studioOutputs').add({
              id, type: 'report', title: j.report.title,
              subtitle: j.report.sections.length + ' sections' + (j.report.topic ? ' · ' + j.report.topic : ''),
              createdAt: Date.now(), report: j.report, sources,
            });
            if (!this.isOpen) this.ready = true;
          } catch (e) {
            this.generating = false;
            this.error = "Astro couldn't connect right now. Please try again in a moment.";
            this.errorKind = 'network';
            this.state = 'error';
            if (!this.isOpen) this.ready = true;
          }
        },

        markdown() {
          if (!this.report) return '';
          return '# ' + this.report.title + '\n\n' + this.report.sections.map(s => '## ' + s.heading + '\n\n' + s.body).join('\n\n') + '\n';
        },
        async share() {
          try { await navigator.clipboard.writeText(this.markdown()); this.copied = true; setTimeout(() => { this.copied = false; }, 2000); } catch (e) {}
        },
        download() {
          const blob = new Blob([this.markdown()], { type: 'text/markdown' });
          const a = document.createElement('a');
          a.href = URL.createObjectURL(blob);
          a.download = (this.report.title.replace(/[^\w\- ]+/g, '').trim().replace(/\s+/g, '-').toLowerCase() || 'report') + '.md';
          a.click(); URL.revokeObjectURL(a.href); this.menuOpen = false;
        },
      });

      // Real web results for extensive-research answers (sidebar). Cleared on New chat.
      Alpine.store('webSources', {
        items: [],          // [{title,url,domain}]
        researching: false,
        set(list) {
          const seen = new Set();
          this.items = (list || []).filter(w => w && /^https?:\/\//i.test(w.url) && !seen.has(w.url) && seen.add(w.url));
        },
        clear() { this.items = []; this.researching = false; },
      });

      // Planet lessons connected to Astro as sources. Web sources stay separate.
      Alpine.store('planetSources', {
        catalog: @json($planetCatalog),
        items: [],          // [{planet,module,lesson,label,active}]
        pickerOpen: false,
        step: 'planets',    // 'planets' | 'lessons'
        planet: null,       // catalog entry being browsed
        draft: [],          // keys ticked in the picker, "planet|module|lesson"

        storageKey: 'techlab_planet_sources:u{{ auth()->id() }}',

        init() {
          try { localStorage.removeItem('techlab_planet_sources'); } catch (e) {}
          try {
            const saved = JSON.parse(localStorage.getItem(this.storageKey) || '[]');
            if (Array.isArray(saved)) this.items = saved.filter(i => i && i.planet && i.module && i.lesson);
          } catch (e) {}
          @if($preselectSource)
          this.addByRef(@json($preselectSource));
          @endif
          Alpine.effect(() => {
            try { localStorage.setItem(this.storageKey, JSON.stringify(this.items)); } catch (e) {}
          });
        },

        key(p, m, l) { return p + '|' + m + '|' + l; },
        has(p, m, l) { return this.items.some(i => this.key(i.planet, i.module, i.lesson) === this.key(p, m, l)); },
        // Sources actually sent with the next message.
        get activeRefs() {
          return this.items.filter(i => i.active).map(i => ({ planet: i.planet, module: i.module, lesson: i.lesson }));
        },

        labelFor(p, m, l) {
          const pl = this.catalog.find(x => x.slug === p);
          const mod = pl && pl.modules.find(x => x.key === m);
          const les = mod && mod.lessons.find(x => x.key === l);
          return les ? { title: les.title, sub: pl.title + ' · ' + mod.title } : null;
        },
        addByRef(r) {
          const info = this.labelFor(r.planet, r.module, r.lesson);
          if (!info || this.has(r.planet, r.module, r.lesson)) return;
          this.items.push({ ...r, label: info.title, sub: info.sub, active: true });
        },
        remove(i) { this.items.splice(i, 1); },

        open() { this.pickerOpen = true; this.step = 'planets'; this.planet = null; this.draft = []; },
        close() { this.pickerOpen = false; },
        choose(slug) { this.planet = this.catalog.find(x => x.slug === slug); this.step = 'lessons'; },
        toggleDraft(m, l) {
          const k = this.key(this.planet.slug, m, l);
          this.draft = this.draft.includes(k) ? this.draft.filter(x => x !== k) : [...this.draft, k];
        },
        inDraft(m, l) { return this.draft.includes(this.key(this.planet.slug, m, l)); },
        commit() {
          this.draft.forEach(k => { const [planet, module, lesson] = k.split('|'); this.addByRef({ planet, module, lesson }); });
          this.close();
        },
      });

      // Recents shown under "Chat" in the sidebar (which lives outside astroChat's scope).
      Alpine.store('recents', {
        list: @json($conversations ?? []),   // newest first
        open: true,
        loading: null,
        menu: null,          // { id, x, y } — the open row menu (fixed-positioned so the list can't clip it)
        get pinned() { return this.list.filter(c => c.pinned); },
        get recent() { return this.list.filter(c => !c.pinned); },
        pick(id) { window.dispatchEvent(new CustomEvent('astro:load-conversation', { detail: { id } })); },
        openMenu(id, el) {
          if (this.menu && this.menu.id === id) { this.menu = null; return; }
          const r = el.getBoundingClientRect();
          this.menu = { id, x: r.left, y: r.bottom + 6 };
        },
        _call(id, method, path = '') {
          return fetch('/chat/conversations/' + id + path, {
            method,
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
              'X-Requested-With': 'XMLHttpRequest',
            },
          });
        },
        async togglePin(id) {
          this.menu = null;
          const c = this.list.find(c => c.id === id);
          if (!c) return;
          const was = c.pinned;
          c.pinned = !was;                                   // optimistic
          try { if (!(await this._call(id, 'POST', '/pin')).ok) c.pinned = was; } catch (e) { c.pinned = was; }
        },
        async remove(id) {
          this.menu = null;
          const c = this.list.find(c => c.id === id);
          if (!c || !confirm('Delete "' + (c.title || 'this chat') + '"? This can\'t be undone.')) return;
          try {
            if (!(await this._call(id, 'DELETE')).ok) return;
            this.list = this.list.filter(x => x.id !== id);
            window.dispatchEvent(new CustomEvent('astro:conversation-deleted', { detail: { id } }));
          } catch (e) { /* keep it listed */ }
        },
      });

      Alpine.store('conversation', {
        currentId: null,
        set(id)  { this.currentId = id; },
        clear()  { this.currentId = null; },
      });

    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/marked@12/marked.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dompurify@3/dist/purify.min.js"></script>
  {{-- Syntax highlighting + boxed code blocks with Copy (public/js/code-highlight.js) --}}
  <script src="{{ asset('js/code-highlight.js') }}?v={{ filemtime(public_path('js/code-highlight.js')) }}"></script>
  <script>
    CodeHighlight.wire();
    // Fenced code in Astro's replies -> static, highlighted, copy-only box (never runnable).
    marked.use({ renderer: { code(code, lang) {
      if (code && typeof code === 'object') { lang = code.lang; code = code.text; }   // marked >= 13 passes a token
      return CodeHighlight.blockHtml(code, String(lang || '').split(/\s/)[0]);
    } } });
  </script>

  <style>
    [x-cloak] { display: none !important; }
    .overflow-y-auto::-webkit-scrollbar       { width: 6px; }
    .overflow-y-auto::-webkit-scrollbar-thumb { background: rgba(150,170,255,0.18); border-radius: 6px; }
    .overflow-y-auto::-webkit-scrollbar-track { background: transparent; }
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { animation-duration: .001ms !important; transition-duration: .001ms !important; }
    }
  </style>

  {{-- chatShell: controls Sources + Studio open/close --}}
  <script>
    function chatShell() {
      return {
        studioOpen: false,
        sourcesOpen: false,
        mobileDrawer: null,
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

        initChat() {
          window.addEventListener('resize', () => {
            this.isMobile = window.matchMedia('(max-width: 1023px)').matches;
            if (!this.isMobile) this.mobileDrawer = null;
          });
        },
      };
    }

    /**
     * astroChat() — Astro chat controller.
     *
     * /chat/message is NOT a streaming endpoint. ChatController::send()
     * fully buffers the NVIDIA NIM response and returns ONE JSON object:
     *   { success, conversation_id, message_id, response }
     * Treat it as plain JSON — no SSE / reader-based stream parsing.
     */
    function astroChat() {
      return {
        messages: [],
        input: '',
        sending: false,
        attachments: [],          // [{id, file, name, kind: 'text'|'image', preview}]
        attachMenu: false,
        listening: false,
        rec: null,
        notice: '',
        noticeTimer: null,
        visionEnabled: @json(\App\Services\AttachmentService::visionEnabled()),
        maxFiles: {{ \App\Services\AttachmentService::MAX_FILES }},
        maxFileBytes: {{ \App\Services\AttachmentService::maxFileKb() }} * 1024,
        maxTotalBytes: {{ \App\Services\AttachmentService::maxTotalBytes() }},
        maxFileLabel: @json(\App\Services\AttachmentService::mb(\App\Services\AttachmentService::maxFileKb())),
        allowedExts: @json(array_merge(\App\Services\AttachmentService::DOC_EXTENSIONS, \App\Services\AttachmentService::TEXT_EXTENSIONS)),
        get sourceCount() { return this.$store.planetSources.activeRefs.length; },
        conversationId: null,
        assistantMessageId: null,
        bannerDismissed: false,
        chatGen: 0,          // bumped by newChat() so a stale stream can't touch the new chat
        abortCtl: null,

        init() {
          window.addEventListener('astro:load-conversation', e => this.loadConversation(e.detail.id));
          window.addEventListener('astro:conversation-deleted', e => { if (e.detail.id === this.conversationId) this.newChat(); });
          const syncStores = () => {
            const ig = this.$store.infographic;
            if (ig) ig.transcript = this.transcriptText();
            const qz = this.$store.quiz;
            if (qz) {
              qz.conversationId = this.conversationId;
              qz._preview = (this.messages || [])
                .filter(m => m.content && m.content !== '')
                .slice(-2)
                .map(m => (m.role === 'assistant' ? 'Astro' : 'You') + ': ' + m.content.slice(0, 120))
                .join(' — ');
            }
            const conv = this.$store.conversation;
            if (conv) conv.set(this.conversationId);
          };
          this.$watch('messages', syncStores);
          this.$watch('conversationId', syncStores);
          if (this.messages.length) syncStores();
          this.$watch('conversationId', (v) => {
            const qz = this.$store.quiz;
            if (qz) qz.conversationId = v;
            const conv = this.$store.conversation;
            if (conv) conv.set(v);
          });
        },

        transcriptText() {
          return this.messages
            .filter(m => m.content && m.content !== '')
            .map(m => (m.role === 'assistant' ? 'Astro' : 'Student') + ': ' + m.content)
            .join('\n\n');
        },

        scrollToBottom() {
          this.$nextTick(() => {
            const el = document.getElementById('chat-scroll');
            if (el) el.scrollTop = el.scrollHeight;
          });
        },

        // ── Composer: attachments + voice ───────────────────────────────
        say(msg) {
          this.notice = msg;
          clearTimeout(this.noticeTimer);
          this.noticeTimer = setTimeout(() => { this.notice = ''; }, 5000);
        },

        pickPhoto() {
          this.attachMenu = false;
          if (!this.visionEnabled) { this.say("Astro can't look at images yet — image understanding isn't switched on."); return; }
          this.$refs.photoPick.click();
        },

        pasteFiles(e) {
          const files = [...(e.clipboardData ? e.clipboardData.files : [])];
          if (files.length) { e.preventDefault(); this.addFiles(files); }
        },

        async addFiles(list) {
          for (const file of [...(list || [])]) {
            if (this.attachments.length >= this.maxFiles) { this.say('Too many files — you can attach up to ' + this.maxFiles + ' per message.'); break; }
            const ext = (file.name.split('.').pop() || '').toLowerCase();
            const isImage = /^image\/(jpeg|png|webp|gif)$/.test(file.type);
            const isText = this.allowedExts.includes(ext);
            if (!isImage && !isText) { this.say('"' + file.name + '" isn\'t a supported file type. Try a PDF, Word, PowerPoint, image, or a text/code file.'); continue; }
            if (isImage && !this.visionEnabled) { this.say("Astro can't look at images yet — image understanding isn't switched on."); continue; }

            if (isImage && file.size > 25 * 1048576) { this.say('"' + file.name + '" is too large (' + this.mb(file.size) + '). Photos over 25 MB can\'t be processed.'); continue; }
            if (!isImage && file.size > this.maxFileBytes) { this.say('"' + file.name + '" is too large (' + this.mb(file.size) + '). The limit is ' + this.maxFileLabel + ' per file.'); continue; }

            let f = file;
            if (isImage) {
              try { f = await this.shrinkImage(file); } catch (err) { this.say('Couldn\'t read "' + file.name + '" as an image.'); continue; }
            }
            if (f.size > this.maxFileBytes) { this.say('"' + file.name + '" is too large (' + this.mb(f.size) + ' even after compressing). The limit is ' + this.maxFileLabel + ' per file.'); continue; }

            const total = this.attachments.reduce((n, a) => n + a.file.size, 0) + f.size;
            if (total > this.maxTotalBytes) { this.say('These files are too large together (' + this.mb(total) + '). The most you can send in one message is ' + this.mb(this.maxTotalBytes) + ' — remove one or attach it separately.'); continue; }

            this.attachments.push({ id: Date.now() + Math.random(), file: f, name: file.name, kind: isImage ? 'image' : 'text',
                                    preview: isImage ? URL.createObjectURL(f) : '' });
          }
        },

        mb(bytes) { return (bytes / 1048576).toFixed(1) + ' MB'; },

        // Icon badge + label for an attachment card.
        meta(a) {
          const ext = ((a.name || '').split('.').pop() || '').toLowerCase();
          if (a.kind === 'image') return { badge: 'IMG', label: 'Image', color: '#8b5cf6' };
          if (ext === 'docx') return { badge: 'DOC', label: 'Word document', color: '#2b6fd6' };
          if (ext === 'pdf') return { badge: 'PDF', label: 'PDF document', color: '#d9463e' };
          if (ext === 'pptx') return { badge: 'PPT', label: 'Presentation', color: '#d9772b' };
          if (['csv', 'tsv'].includes(ext)) return { badge: 'CSV', label: 'Spreadsheet data', color: '#2f9d6a' };
          if (['md', 'markdown', 'txt', 'log'].includes(ext)) return { badge: 'TXT', label: 'Text document', color: '#64748b' };
          return { badge: '</>', label: 'Code file', color: '#0e8fa8' };
        },

        removeAttachment(i) {
          const a = this.attachments[i];
          if (a && a.preview) URL.revokeObjectURL(a.preview);
          this.attachments.splice(i, 1);
        },

        // Phone photos are huge; downscale to <=1600px JPEG before upload (also what the model needs).
        async shrinkImage(file) {
          if (file.type === 'image/gif') return file;
          const bmp = await createImageBitmap(file);
          const scale = Math.min(1, 1600 / Math.max(bmp.width, bmp.height));
          const c = document.createElement('canvas');
          c.width = Math.round(bmp.width * scale); c.height = Math.round(bmp.height * scale);
          c.getContext('2d').drawImage(bmp, 0, 0, c.width, c.height);
          const blob = await new Promise(r => c.toBlob(r, 'image/jpeg', 0.85));
          if (!blob) throw new Error('encode failed');
          return new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg' });
        },

        // Dictation through the browser's speech recognition (no audio leaves the app's server).
        toggleMic() {
          const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
          if (!SR) { this.say("Voice input isn't supported in this browser. Try Chrome, Edge or Safari."); return; }
          if (this.listening && this.rec) { this.rec.stop(); return; }

          const rec = new SR();
          rec.lang = navigator.language || 'en-US';
          rec.interimResults = true;
          rec.continuous = true;
          const base = this.input ? this.input.replace(/\s*$/, ' ') : '';
          rec.onresult = (e) => {
            let t = '';
            for (let i = 0; i < e.results.length; i++) t += e.results[i][0].transcript;
            this.input = base + t;
            this.$nextTick(() => { const b = this.$refs.box; if (b) { b.style.height = 'auto'; b.style.height = Math.min(b.scrollHeight, 128) + 'px'; } });
          };
          rec.onerror = (e) => {
            if (e.error === 'not-allowed' || e.error === 'service-not-allowed') this.say('Microphone access is blocked. Allow it in your browser settings and try again.');
            else if (e.error === 'no-speech') this.say("Didn't catch anything — try again.");
            else if (e.error !== 'aborted') this.say('Voice input stopped (' + e.error + ').');
          };
          rec.onend = () => { this.listening = false; this.rec = null; };
          try { rec.start(); this.rec = rec; this.listening = true; } catch (err) { this.listening = false; }
        },

        sendSuggestion(text) { this.input = text; this.send(); },

        newChat() {
          this.chatGen++;
          Alpine.store('webSources').clear();
          if (this.abortCtl) { this.abortCtl.abort(); this.abortCtl = null; }
          this.sending = false;
          this.messages = [];
          this.conversationId = null;
          this.assistantMessageId = null;
        },

        async send() {
          const text = (this.input || '').trim();
          const files = this.attachments;
          if ((!text && !files.length) || this.sending) return;

          if (this.listening && this.rec) this.rec.stop();
          this.input = '';
          this.attachments = [];
          if (this.$refs.box) this.$refs.box.style.height = 'auto';
          const label = text || 'Please take a look at what I attached.';
          const refs = this.$store.planetSources.activeRefs;
          this.messages.push({ role: 'user', content: text,
                               attachments: files.map(a => ({ name: a.name, kind: a.kind, preview: a.preview })) });
          this.messages.push({ role: 'assistant', content: '', html: '', pending: true, error: false, errorMessage: '',
                              status: AstroStatus.first(label, refs) });
          const idx = this.messages.length - 1;
          const setAstro = (patch) => { this.messages[idx] = { ...this.messages[idx], ...patch }; };
          this.sending = true;
          this.scrollToBottom();

          // Deterministic status lines while Astro works (no AI call). Guarded so that
          // "New chat" mid-response can't resurrect this message from the timer.
          const status = AstroStatus.track(label, refs, (s) => {
            if (this.messages[idx] && this.messages[idx].pending) setAstro({ status: s });
          });

          const csrf = document.querySelector('meta[name="csrf-token"]')
            ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            : '';

          const gen = this.chatGen;
          const live = () => gen === this.chatGen;             // false after "New chat"
          const patch = (p) => { if (live() && this.messages[idx]) setAstro(p); };
          this.abortCtl = new AbortController();

          let acc = '';          // the answer so far
          let started = false;   // first delta seen (THINKING -> RESPONDING)
          let ended = false;     // server sent `end` (answer complete)
          let failure = null;    // server sent `error`
          let raf = null;

          // Render at most once per frame; the first chunk renders synchronously.
          const render = () => { raf = null; patch({ content: acc, html: this.md(acc) }); this.followStream(); };
          const begin = () => {
            if (started) return;
            started = true;
            AstroStatus.settleDom(); status.settle();
            patch({ pending: false, streaming: true });
          };
          const interrupted = (fallbackMsg) => {
            if (raf) { cancelAnimationFrame(raf); raf = null; }
            AstroStatus.settleDom(); status.settle();
            const msg = acc
              ? acc + '\n\n_' + (failure || "Astro's reply was interrupted. Please try again.") + '_'
              : (failure || fallbackMsg);
            patch({ pending: false, streaming: false, error: true, content: msg, html: this.md(msg) });
          };

          const wasNew = !this.conversationId;
          try {
            const headers = {
              // JSON first so validation/auth errors still come back as JSON; the server
              // streams SSE whenever text/event-stream is listed.
              'Accept': 'application/json, text/event-stream',
              'X-CSRF-TOKEN': csrf,
              'X-Requested-With': 'XMLHttpRequest',
            };
            let body;
            if (files.length) {
              // Files travel as multipart; the browser sets the boundary header itself.
              body = new FormData();
              body.append('message', text);
              if (this.conversationId) body.append('conversation_id', this.conversationId);
              body.append('level', 'auto');
              body.append('context', '');
              refs.forEach((r, i) => ['planet', 'module', 'lesson'].forEach(k => body.append('sources[' + i + '][' + k + ']', r[k])));
              files.forEach(a => body.append('files[]', a.file, a.file.name));
            } else {
              headers['Content-Type'] = 'application/json';
              body = JSON.stringify({
                message: text,
                conversation_id: this.conversationId,
                level: 'auto',
                context: '',
                sources: refs,
              });
            }
            const res = await fetch(@json($messageUrl ?? '/chat/message'), { method: 'POST', signal: this.abortCtl.signal, headers, body });

            const isSse = (res.headers.get('content-type') || '').includes('text/event-stream');

            if (!res.ok || !isSse) {
              // Errors before streaming starts (401/404/422/500) and the buffered-JSON fallback.
              const j = await res.json().catch(() => null);
              if (res.status === 413) {
                failure = 'That upload is too large for the server. Remove a file or attach smaller ones.';
                interrupted(failure);
                return;
              }
              if (res.ok && j && j.success) {
                if (j.conversation_id && live()) this.setConversation(j.conversation_id);
                this.afterSaved(j.conversation_id, wasNew, text || label);
                AstroStatus.settleDom(); status.settle();
                if (live()) Alpine.store('webSources').set(j.web);
                patch({ content: j.response, html: this.md(j.response), pending: false, sources: j.sources || [] });
              } else {
                failure = (j && j.error) ? j.error
                  : (j && j.errors && Object.values(j.errors)[0]) ? Object.values(j.errors)[0][0]
                  : "Astro couldn't respond right now. Please try again.";
                interrupted(failure);
              }
              return;
            }

            const reader = res.body.getReader();
            const decoder = new TextDecoder();
            let buf = '';

            const handle = (ev, d) => {
              if (ev === 'researching') {
                if (live()) Alpine.store('webSources').researching = true;
              } else if (ev === 'web') {
                if (live()) { const ws = Alpine.store('webSources'); ws.researching = false; ws.set(d.results); }
              } else if (ev === 'delta') {
                acc += d.t || '';
                if (!started) { begin(); render(); }
                else if (!raf) raf = requestAnimationFrame(render);
              } else if (ev === 'end') {
                if (raf) { cancelAnimationFrame(raf); raf = null; }
                begin();
                ended = true;
                patch({ content: acc, html: this.md(acc), streaming: false, sources: d.sources || [] });
                this.followStream();
              } else if (ev === 'done') {
                if (d.conversation_id && live()) this.setConversation(d.conversation_id);
                this.afterSaved(d.conversation_id, wasNew, text || label);
              } else if (ev === 'error') {
                failure = d.error || "Astro couldn't respond right now. Please try again.";
              }
            };

            while (true) {
              const { value, done } = await reader.read();
              if (done) break;
              buf += decoder.decode(value, { stream: true });
              let i;
              while ((i = buf.indexOf('\n\n')) !== -1) {
                const raw = buf.slice(0, i);
                buf = buf.slice(i + 2);
                let ev = 'message', data = '';
                for (const line of raw.split('\n')) {
                  if (line.startsWith('event:')) ev = line.slice(6).trim();
                  else if (line.startsWith('data:')) data += line.slice(5).replace(/^ /, '');
                }
                let d = {};
                try { d = data ? JSON.parse(data) : {}; } catch (e) { continue; }
                handle(ev, d);
              }
            }

            // Stream closed: it must have ended cleanly, otherwise show what we have + a note.
            if (failure || !ended) interrupted("Astro couldn't respond right now. Please try again.");
          } catch (e) {
            if (e && e.name === 'AbortError') return;          // "New chat" cancelled this stream
            interrupted("Astro couldn't connect right now. Please try again.");
          } finally {
            status.stop();
            if (live()) Alpine.store('webSources').researching = false;
            if (raf) cancelAnimationFrame(raf);
            if (live()) { this.abortCtl = null; this.sending = false; this.scrollToBottom(); }
          }
        },

        // ── Recents (sidebar) ─────────────────────────────────────────────
        // After an exchange is saved: put the conversation at the top of Recents and,
        // for a brand-new one, ask Astro to name it from what the chat is about.
        afterSaved(id, wasNew, firstText) {
          if (!id) return;
          const i = this.$store.recents.list.findIndex(c => c.id === id);
          const row = i === -1
            ? { id, title: (firstText || 'New chat').slice(0, 50), updated_at: new Date().toISOString() }
            : { ...this.$store.recents.list[i], updated_at: new Date().toISOString() };
          if (i !== -1) this.$store.recents.list.splice(i, 1);
          this.$store.recents.list.unshift(row);
          if (wasNew) this.nameConversation(id);
        },

        async nameConversation(id) {
          try {
            const res = await fetch('/chat/conversations/' + id + '/title', {
              method: 'POST',
              headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest',
              },
            });
            if (!res.ok) return;
            const j = await res.json();
            const c = this.$store.recents.list.find(c => c.id === id);
            if (c && j.conversation && j.conversation.title) c.title = j.conversation.title;
          } catch (e) { /* keep the placeholder title */ }
        },

        async loadConversation(id) {
          const rc = this.$store.recents;
          if (id === this.conversationId || rc.loading) return;
          rc.loading = id;
          try {
            const res = await fetch('/chat/conversations/' + id, {
              headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) return;
            const j = await res.json();
            this.newChat();
            this.messages = (j.messages || []).map(m => m.role === 'assistant'
              ? { role: 'assistant', content: m.content, html: this.md(m.content), pending: false, streaming: false, error: false, errorMessage: '', sources: [] }
              : { role: 'user', content: m.content, attachments: m.attachments || [] });
            this.setConversation(id);
            this.mobileDrawer = null;
            this.$nextTick(() => this.scrollToBottom());
          } catch (e) { /* leave the current chat as-is */ }
          finally { rc.loading = null; }
        },

        setConversation(id) {
          this.conversationId = id;
          const qz = this.$store.quiz;
          if (qz) qz.conversationId = id;
        },

        // While a reply streams in, keep it in view — but never yank the page if the
        // student has scrolled up to read.
        followStream() {
          const el = document.getElementById('chat-scroll');
          if (el && el.scrollHeight - el.scrollTop - el.clientHeight < 160) el.scrollTop = el.scrollHeight;
        },

        md(text) {
          if (!text) return '';
          try {
            return DOMPurify.sanitize(marked.parse(text));
          } catch (e) {
            return text
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/\n/g, '<br>');
          }
        },

        copy(text) {
          if (navigator.clipboard) navigator.clipboard.writeText(text || '').catch(() => {});
        },
      };
    }

    {{-- TechLab shell: sidebar collapsed state + theme toggle --}}
    function techlabShell() {
      return {
        collapsed: true,

        // NOTE: the body spreads techlabShell() and chatShell() into one Alpine
        // context, so a plain init() here would be overwritten by chatShell's.
        // Each context gets its own name and both are called from x-init.
        initShell() {
          const saved = localStorage.getItem('techlab_sidebar_collapsed');
          this.collapsed = saved === null ? true : saved === '1';
          this.$watch('collapsed', v =>
            localStorage.setItem('techlab_sidebar_collapsed', v ? '1' : '0')
          );
        },

        toggleTheme() { window.techlabTheme.toggle(); },
      };
    }
  </script>
</head>

{{--
  Body carries both Alpine contexts:
    techlabShell() — sidebar collapsed state (persistent across pages via localStorage)
    chatShell()    — Studio panel open/close state (chat-page only)
--}}
<body
  class="bg-void font-sans text-ink antialiased"
  x-data="{ ...techlabShell(), ...chatShell() }"
  x-init="initShell(); initChat(); $store.planetSources.init()"
>

  {{-- Space background --}}
  <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
    <div class="space-bg absolute inset-0" style="background: radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%), radial-gradient(100% 80% at 85% 110%, #1a0f4d 0%, rgba(26,15,77,0) 60%), linear-gradient(160deg, #0a0826 0%, #120a33 45%, #1e1259 100%);"></div>
    <div class="absolute -left-24 -top-28 h-[460px] w-[460px] rounded-full opacity-50 blur-[70px]" style="background: radial-gradient(circle, rgba(155,107,255,0.5), transparent 70%);"></div>
    <div class="absolute -bottom-36 -right-28 h-[520px] w-[520px] rounded-full opacity-50 blur-[70px]" style="background: radial-gradient(circle, rgba(91,225,255,0.35), transparent 70%);"></div>
  </div>

  {{-- ┌──────────┬───────────────────────┬──────────────┐
       │ TECHLAB  │                       │              │
       │ REMOTE   │    ASTRO CHAT / TV    │   STUDIO     │
       │ Sidebar  │                       │   PANEL      │
       └──────────┴───────────────────────┴──────────────┘ --}}
  <div class="flex h-screen w-screen overflow-hidden">

    {{-- ── REMOTE / SIDEBAR ────────────────────────── --}}
    @include('components.shell.side-bar')

    {{-- ── SOURCES (left) ───────────────────────────── --}}
    @include('student.chat.partials.sources-panel')

    {{-- ── TV: ASTRO CHAT (main content) ───────────── --}}
    @include('student.chat.partials.chat-panel')

    {{-- ── STUDIO PANEL ─────────────────────────────── --}}
    @include('student.chat.partials.studio-panel')

    {{-- ── Overlays ──────────────────────────────────── --}}
    @unless($isFacultyChat)
    @include('student.chat.partials.planet-picker')
    @endunless
    @include('student.chat.partials.infographic-viewer')
    @include('student.chat.partials.quiz-viewer')
    @include('student.chat.partials.flashcards-viewer')
    @include('student.chat.partials.keyterms-viewer')
    @include('student.chat.partials.report-viewer')

    {{-- Mobile backdrop --}}
    <div
      x-show="mobileDrawer" x-cloak
      @click="closeDrawer()"
      x-transition.opacity
      class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm lg:hidden"
    ></div>

    {{-- Studio reopen tab (desktop, shown when Studio is collapsed) --}}
    <button
      x-show="!studioOpen" x-cloak
      @click="studioOpen = true"
      class="fixed right-0 top-1/2 z-20 hidden h-16 w-7 -translate-y-1/2 items-center justify-center
             rounded-l-lg border border-r-0 border-glassBorder bg-[rgba(6,6,26,0.6)]
             text-muted backdrop-blur-xl transition hover:text-[#73b6ff] lg:flex"
      title="Open studio" aria-label="Open studio"
    >
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
           stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
        <path d="m15 6-6 6 6 6"/>
      </svg>
    </button>

  </div>
</body>
</html>