{{--
  The final exam ("Final Boss"): rules first, then the exam itself, full screen.

  Leaving full screen (Esc), switching tab or window (Alt+Tab, Cmd+Tab), reloading, or reaching
  for a screenshot ends the exam as a fail and emails the faculty member (FinalExamController).
  Copy, paste, right-click, text selection and printing are blocked, and every question carries
  a watermark with the student's name. A browser cannot stop a phone camera, so the watermark is
  what makes a leaked photo traceable.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Final Exam · {{ $courseTitle }} · TechLab</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&family=Pixelify+Sans:wght@500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/theme.css') }}?v={{ filemtime(public_path('css/theme.css')) }}">
  <script src="{{ asset('js/theme.js') }}?v={{ filemtime(public_path('js/theme.js')) }}"></script>
  <script defer src="{{ asset('js/sfx.js') }}?v={{ filemtime(public_path('js/sfx.js')) }}"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: {
      colors: { ink: 'rgb(var(--c-ink) / <alpha-value>)', muted: 'rgb(var(--c-muted) / <alpha-value>)', glass: 'var(--glass)', glassBorder: 'var(--glass-border)' },
      fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
    } } };
  </script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    [x-cloak] { display: none !important; }
    html, body { background: #06061a; }
    body { -webkit-font-smoothing: antialiased; }
    .space { position: fixed; inset: 0; z-index: -1; background: radial-gradient(120% 90% at 50% -10%, #3a1030 0%, rgba(58,16,48,0) 55%), linear-gradient(160deg, #0a0826 0%, #180a26 45%, #2a0e2e 100%); }
    /* Nothing on the exam can be selected, dragged, long-pressed or printed. */
    .no-copy { -webkit-user-select: none; user-select: none; -webkit-touch-callout: none; }
    @media print { body * { visibility: hidden !important; } body::after { content: 'Printing the final exam is not allowed.'; visibility: visible; } }
    .watermark { position: absolute; inset: 0; pointer-events: none; overflow: hidden; z-index: 1; }
    .watermark span { position: absolute; white-space: nowrap; transform: rotate(-24deg); font: 600 13px/1 'Space Mono', monospace; color: rgb(var(--c-ink)); opacity: .07; }
    .shield { position: fixed; inset: 0; z-index: 60; display: grid; place-items: center; background: #06061a; }
    .opt { display: flex; cursor: pointer; align-items: center; gap: 12px; border-radius: 10px; padding: 10px 12px; border: 1px solid transparent; }
    .opt:hover { background: var(--glass); }
    .opt:has(input:checked) { border-color: rgba(115,182,255,.55); background: rgba(115,182,255,.1); }
  </style>
  <style>@include('student.partials.pixel-ui')</style>
  <link rel="stylesheet" href="{{ asset('css/cursor.css') }}?v={{ filemtime(public_path('css/cursor.css')) }}">
</head>
<body class="no-copy font-sans text-ink" x-data="finalExam()" x-init="init()">
  <div class="space" aria-hidden="true"></div>

  {{-- ═══════════ LOBBY / STATUS ═══════════ --}}
  <main x-show="phase === 'lobby' || phase === 'starting'" class="mx-auto flex min-h-screen max-w-[760px] flex-col justify-center px-4 py-10 sm:px-8">
    <a href="{{ $backUrl }}" class="text-[14px] text-muted underline-offset-4 hover:underline">← Back to {{ $courseTitle }}</a>

    <section class="px-card mt-6 p-6 sm:p-10">
      <p class="text-[13px] font-semibold uppercase tracking-[0.2em] text-[#ffc400]">Final Boss · {{ $courseTitle }}</p>
      <h1 class="px-font mt-2 text-[clamp(2rem,6vw,3.2rem)] font-bold leading-none">{{ $exam?->title ?? 'The Final Exam' }}</h1>

      @if ($state === 'ready' && $closed)
        <p class="mt-5 text-[16px] leading-relaxed text-muted">This final exam closed on {{ $exam->due_at->format('j M Y, g:ia') }}. Talk to {{ $captain ?? 'your captain' }} about what happens next.</p>
      @elseif ($state === 'ready')
        @if ($exam->description)
          <p class="mt-4 text-[16px] leading-relaxed text-muted">{{ $exam->description }}</p>
        @endif

        <p class="mt-5 text-[15px] text-muted">
          {{ $exam->questions_count }} {{ \Illuminate\Support\Str::plural('question', $exam->questions_count) }}
          @if ($exam->minutes) · {{ $exam->minutes }} minutes @endif
          · pass mark <b class="text-ink">{{ $exam->passPercent() }}%</b>
          @if ($exam->due_at) · closes {{ $exam->due_at->format('j M, g:ia') }} @endif
        </p>

        <div class="px-inner mt-6 rounded-sm p-5">
          <p class="font-semibold">Rules of the arena</p>
          <ul class="mt-3 flex list-disc flex-col gap-2 pl-5 text-[15px] leading-relaxed">
            <li>The exam runs in <b>full screen</b>. You get <b>one try</b>.</li>
            <li><b>Leaving full screen</b> (Esc), <b>switching tabs or windows</b> (Alt+Tab, Cmd+Tab), or <b>reloading the page</b> ends the exam right away, and you <b>fail</b>.</li>
            <li>Screenshots, copy, paste and right-click are blocked. Trying to take a screenshot also ends the exam.</li>
            <li>{{ $captain ?? 'Your captain' }} gets an email if your exam ends early.</li>
            @if ($exam->minutes)
              <li>When the timer reaches zero, your answers are handed in automatically.</li>
            @endif
            <li>Close other apps and turn off pop-up notifications before you start.</li>
          </ul>
        </div>

        <label class="mt-6 flex cursor-pointer items-start gap-3 text-[15px]">
          <input type="checkbox" class="mt-1 accent-[#ffc400]" x-model="agreed">
          <span>I understand the rules. If I leave the exam, I fail.</span>
        </label>

        <p x-show="error" x-cloak class="mt-4 rounded-[10px] border border-[rgba(255,107,129,0.35)] bg-[rgba(255,107,129,0.1)] px-4 py-3 text-[14px] text-[#ff6b81]" x-text="error"></p>

        <button type="button" class="px-btn px-btn-yellow mt-6" :disabled="!agreed || phase === 'starting'" @click="start()"
                x-text="phase === 'starting' ? 'Entering the arena…' : 'Enter full screen and start'"></button>
      @elseif ($state === 'locked')
        <p class="mt-5 text-[16px] leading-relaxed text-muted">Clear every lesson first. The Final Boss only opens after that.</p>
        <a href="{{ $backUrl }}" class="px-btn mt-6">Back to the lessons</a>
      @elseif ($state === 'not_set')
        <p class="mt-5 text-[16px] leading-relaxed text-muted">You cleared every lesson! {{ $captain ?? 'Your captain' }} hasn't set the final exam yet. Check back soon.</p>
        <a href="{{ $backUrl }}" class="px-btn mt-6">Back to the course</a>
      @elseif ($state === 'passed')
        <p class="mt-5 text-[16px] leading-relaxed">You defeated the Final Boss with <b>{{ $result['percent'] }}%</b>. 🎉</p>
        <a href="{{ $certificateUrl }}" class="px-btn px-btn-yellow mt-6">View your certificate</a>
      @else
        <p class="mt-5 text-[16px] leading-relaxed text-muted">
          @if ($result['violation'])
            Your exam ended early: <b class="text-[#ff6b81]">{{ strtolower($result['violation']) }}</b>. {{ $captain ?? 'Your captain' }} has been told.
          @else
            You scored <b class="text-ink">{{ $result['percent'] }}%</b> and needed {{ $result['pass_percent'] }}%.
          @endif
          {{ $captain ?? 'Your captain' }} can let you try again.
        </p>
        <a href="{{ $backUrl }}" class="px-btn mt-6">Back to the course</a>
      @endif
    </section>
  </main>

  {{-- ═══════════ THE EXAM ═══════════ --}}
  <div x-show="phase === 'exam' || phase === 'submitting'" x-cloak class="min-h-screen">
    <header class="sticky top-0 z-20 border-b border-glassBorder bg-[rgba(6,6,26,0.92)] backdrop-blur">
      <div class="mx-auto flex max-w-[860px] flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-8">
        <p class="px-font truncate text-[1.15rem] font-medium">{{ $exam?->title ?? 'Final Exam' }}</p>
        <div class="flex items-center gap-4 text-[15px]">
          <span class="text-muted"><span x-text="answeredCount()"></span> / <span x-text="questions.length"></span> answered</span>
          <span x-show="secondsLeft !== null" class="px-font rounded-full px-3 py-1 text-[1.05rem] font-semibold"
                :class="secondsLeft <= 60 ? 'bg-[rgba(255,107,129,0.18)] text-[#ff6b81]' : 'bg-glass'" x-text="clock()"></span>
        </div>
      </div>
    </header>

    <main class="relative mx-auto max-w-[860px] px-4 py-8 sm:px-8">
      <div class="watermark" aria-hidden="true">
        <template x-for="(m, i) in marks" :key="i">
          <span :style="`left:${m.x}%;top:${m.y}%`" x-text="stamp"></span>
        </template>
      </div>

      <form @submit.prevent="askSubmit()" class="relative z-[2] flex flex-col gap-6">
        <template x-for="(q, qi) in questions" :key="q.id">
          <fieldset class="px-card p-5 sm:p-7">
            <legend class="sr-only" x-text="'Question ' + (qi + 1)"></legend>
            <p class="text-[1.08rem] font-semibold leading-relaxed"><span class="text-muted" x-text="(qi + 1) + '. '"></span><span x-text="q.prompt"></span></p>
            <p x-show="q.type === 'checkboxes'" class="mt-2 text-[13px] text-muted">Tick every answer that applies.</p>

            {{-- Every type is shown as a list here: a native drop-down opens outside the page and can look like leaving it. --}}
            <div class="mt-4 flex flex-col gap-1.5">
              <template x-for="(opt, oi) in q.options" :key="oi">
                <label class="opt">
                  <template x-if="q.type === 'checkboxes'">
                    <input type="checkbox" class="accent-[#73b6ff]" :value="oi" x-model.number="answers[q.id]">
                  </template>
                  <template x-if="q.type !== 'checkboxes'">
                    <input type="radio" class="accent-[#73b6ff]" :name="'q' + q.id" :value="oi" x-model.number="answers[q.id]">
                  </template>
                  <span class="text-[15px]" x-text="opt"></span>
                </label>
              </template>
            </div>
          </fieldset>
        </template>

        <div class="px-card flex flex-wrap items-center gap-4 p-5">
          <template x-if="!confirming">
            <button type="submit" class="px-btn px-btn-yellow" :disabled="phase === 'submitting'">Hand in my answers</button>
          </template>
          <template x-if="confirming">
            <div class="flex flex-wrap items-center gap-3">
              <span class="text-[15px]" x-text="unanswered() ? `${unanswered()} question(s) still blank. Hand in anyway?` : 'Hand in your answers? You cannot change them after.'"></span>
              <button type="button" class="px-btn px-btn-yellow" :disabled="phase === 'submitting'" @click="submit()"
                      x-text="phase === 'submitting' ? 'Handing in…' : 'Yes, hand in'"></button>
              <button type="button" class="px-btn" @click="confirming = false">Keep going</button>
            </div>
          </template>
          <p x-show="error" x-cloak class="w-full text-[14px] text-[#ff6b81]" x-text="error"></p>
        </div>
      </form>
    </main>
  </div>

  {{-- Shown the instant a screenshot key combination starts, so the capture comes out blank. --}}
  <div x-show="shielded && phase === 'exam'" x-cloak class="shield" aria-hidden="true">
    <p class="px-font text-2xl text-muted">Screenshots are not allowed</p>
  </div>

  {{-- ═══════════ THE OUTCOME ═══════════ --}}
  <main x-show="phase === 'ended'" x-cloak class="mx-auto flex min-h-screen max-w-[680px] flex-col justify-center px-4 py-10 sm:px-8">
    <section class="px-card p-6 text-center sm:p-10">
      <template x-if="result && result.violation">
        <div>
          <p class="text-[13px] font-semibold uppercase tracking-[0.2em] text-[#ff6b81]">Exam ended</p>
          <h1 class="px-font mt-3 text-[clamp(2rem,6vw,3rem)] font-bold leading-none">You left the arena</h1>
          <p class="mt-5 text-[16px] leading-relaxed text-muted">
            <span x-text="result.violation"></span>. That counts as a fail, and {{ $captain ?? 'your captain' }} has been notified.
            If it was an accident, talk to them: they can let you try again.
          </p>
        </div>
      </template>
      <template x-if="result && !result.violation && result.status === 'passed'">
        <div>
          <p class="text-[13px] font-semibold uppercase tracking-[0.2em] text-[#ffc400]">Victory</p>
          <h1 class="px-font mt-3 text-[clamp(2rem,6vw,3rem)] font-bold leading-none">Final Boss defeated!</h1>
          <p class="mt-5 text-[18px]">You scored <b x-text="result.percent + '%'"></b> (<span x-text="result.score"></span>/<span x-text="result.total"></span>). Your certificate is ready.</p>
        </div>
      </template>
      <template x-if="result && !result.violation && result.status === 'failed'">
        <div>
          <p class="text-[13px] font-semibold uppercase tracking-[0.2em] text-[#ff6b81]">Not this time</p>
          <h1 class="px-font mt-3 text-[clamp(2rem,6vw,3rem)] font-bold leading-none">The boss survived</h1>
          <p class="mt-5 text-[16px] leading-relaxed text-muted">
            You scored <b class="text-ink" x-text="result.percent + '%'"></b> and needed <span x-text="result.pass_percent + '%'"></span>.
            Talk to {{ $captain ?? 'your captain' }}: they can let you try again.
          </p>
        </div>
      </template>
      <template x-if="!result">
        <div>
          <h1 class="px-font text-[2rem] font-bold">Exam ended</h1>
          <p class="mt-4 text-muted">We couldn't reach TechLab to confirm your result. Reload this page to see it.</p>
        </div>
      </template>

      <div class="mt-8 flex flex-wrap justify-center gap-3">
        <template x-if="result?.certificate_url">
          <a :href="result.certificate_url" class="px-btn px-btn-yellow">View your certificate</a>
        </template>
        <a href="{{ $backUrl }}" class="px-btn">Back to the course</a>
      </div>
    </section>
  </main>

  <script>
    function finalExam() {
      const urls = @js($exam ? [
        'start' => route('student.final-exam.start', $exam),
        'violation' => route('student.final-exam.violation', $exam),
        'submit' => route('student.final-exam.submit', $exam),
      ] : null);
      const csrf = document.querySelector('meta[name="csrf-token"]').content;
      const stamp = @js($student->name.' · '.$student->email);

      return {
        phase: 'lobby', agreed: false, error: '', confirming: false,
        questions: [], answers: {}, result: null,
        secondsLeft: null, timer: null,
        armed: false, shielded: false,
        stamp: stamp + ' · ' + new Date().toLocaleString(),
        marks: Array.from({ length: 28 }, (_, i) => ({ x: (i % 4) * 26 - 4, y: Math.floor(i / 4) * 15 + 2 })),

        init() {
          document.addEventListener('fullscreenchange', () => { if (!document.fullscreenElement) this.violate('left_fullscreen'); });
          document.addEventListener('visibilitychange', () => { if (document.hidden) this.violate('switched_away'); });
          window.addEventListener('blur', () => this.violate('switched_away'));
          // Closing or reloading the tab: the page is going away, so tell the server with a beacon.
          window.addEventListener('pagehide', () => this.violate('left_page', true));

          ['copy', 'cut', 'paste', 'contextmenu', 'selectstart', 'dragstart'].forEach(type =>
            document.addEventListener(type, e => { if (this.phase === 'exam') e.preventDefault(); }));
          window.addEventListener('beforeprint', () => this.violate('screenshot'));

          document.addEventListener('keydown', e => this.onKey(e), true);
          document.addEventListener('keyup', e => {
            // Windows' Print Screen only ever reports its key-up, after the capture.
            if (e.key === 'PrintScreen') { this.wipeClipboard(); this.violate('screenshot'); }
            if (!e.metaKey || !e.shiftKey) this.shielded = false;
          }, true);
        },

        onKey(e) {
          if (this.phase !== 'exam') return;
          const k = (e.key || '').toLowerCase();
          // Mac screenshots start with Cmd+Shift; Windows' Snipping Tool with Win+Shift. Blank the screen first.
          if (e.metaKey && e.shiftKey) this.shielded = true;
          if (e.metaKey && e.shiftKey && ['3', '4', '5', '6', 's'].includes(k)) { e.preventDefault(); this.violate('screenshot'); return; }
          if (k === 'printscreen') { e.preventDefault(); this.violate('screenshot'); return; }
          if (k === 'escape') { e.preventDefault(); this.violate('left_fullscreen'); return; }
          // Copy, paste, select all, print, save, view source, and the developer tools.
          const mod = e.ctrlKey || e.metaKey;
          if ((mod && ['c', 'x', 'v', 'a', 'p', 's', 'u'].includes(k)) || k === 'f12' || (mod && e.shiftKey && ['i', 'j', 'c'].includes(k))) {
            e.preventDefault();
            e.stopPropagation();
          }
        },

        async start() {
          if (!urls || this.phase !== 'lobby') return;
          this.error = '';
          const root = document.documentElement;
          if (!root.requestFullscreen) {
            this.error = "This browser can't go full screen. Take the exam on a computer with Chrome, Edge or Firefox.";
            return;
          }
          this.phase = 'starting';
          try {
            await root.requestFullscreen({ navigationUI: 'hide' });
          } catch (e) {
            this.error = 'Full screen was blocked. Allow it and press Start again.';
            this.phase = 'lobby';
            return;
          }

          try {
            const res = await this.post(urls.start, {});
            const body = await res.json().catch(() => ({}));
            if (!res.ok) {
              await this.leaveFullscreen();
              if (body.result) { this.result = body.result; this.phase = 'ended'; return; }
              this.error = body.message || 'The exam could not start. Try again.';
              this.phase = 'lobby';
              return;
            }
            body.questions.forEach(q => { this.answers[q.id] = q.type === 'checkboxes' ? [] : null; });
            this.questions = body.questions;
            this.secondsLeft = body.seconds_left;
            this.phase = 'exam';
            window.scrollTo(0, 0);
            if (this.secondsLeft !== null) this.timer = setInterval(() => this.tick(), 1000);
            // A moment for the full-screen switch to settle before anything counts as leaving.
            // If they already slipped out of full screen in that moment, that still counts.
            setTimeout(() => { if (document.fullscreenElement) this.armed = true; else this.violate('left_fullscreen', false, true); }, 1200);
          } catch (e) {
            await this.leaveFullscreen();
            this.error = 'Could not reach TechLab. Check your connection and try again.';
            this.phase = 'lobby';
          }
        },

        tick() {
          this.secondsLeft = Math.max(0, this.secondsLeft - 1);
          if (this.secondsLeft === 0) { clearInterval(this.timer); this.submit(); }
        },

        clock() {
          const s = this.secondsLeft ?? 0;
          return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
        },

        answeredCount() {
          return this.questions.filter(q => { const a = this.answers[q.id]; return Array.isArray(a) ? a.length > 0 : a !== null && a !== undefined; }).length;
        },
        unanswered() { return this.questions.length - this.answeredCount(); },

        askSubmit() { this.confirming = true; },

        async submit() {
          if (this.phase !== 'exam') return;
          this.armed = false;
          this.phase = 'submitting';
          clearInterval(this.timer);
          try {
            const res = await this.post(urls.submit, { answers: this.answers });
            const body = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(body.message || 'submit failed');
            this.result = body.result;
          } catch (e) {
            this.result = null;
          }
          await this.leaveFullscreen();
          this.phase = 'ended';
          if (this.result) window.techlabSfx?.play(this.result.status === 'passed' ? 'win' : 'lose');
        },

        /** The student broke a rule: end the exam on screen at once, then tell the server. */
        async violate(reason, pageIsClosing = false, force = false) {
          if (this.phase !== 'exam' || (!this.armed && !force)) return;
          this.armed = false;
          this.phase = 'ended';
          clearInterval(this.timer);
          this.questions = [];
          window.techlabSfx?.play('lose');

          const form = new FormData();
          form.append('_token', csrf);
          form.append('reason', reason);

          if (pageIsClosing && navigator.sendBeacon) {
            navigator.sendBeacon(urls.violation, form);
            return;
          }

          try {
            const res = await fetch(urls.violation, { method: 'POST', body: form, keepalive: true, headers: { 'Accept': 'application/json' } });
            this.result = (await res.json()).result;
          } catch (e) {
            // Offline: the server still fails the attempt the next time any page loads.
            this.result = { status: 'failed', violation: 'The exam ended because you left it' };
          }
          await this.leaveFullscreen();
        },

        async leaveFullscreen() {
          try { if (document.fullscreenElement) await document.exitFullscreen(); } catch (e) {}
        },

        wipeClipboard() {
          try { navigator.clipboard?.writeText(''); } catch (e) {}
        },

        post(url, data) {
          return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(data),
          });
        },
      };
    }
  </script>
</body>
</html>
