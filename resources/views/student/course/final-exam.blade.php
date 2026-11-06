{{--
  The final exam ("Final Boss"): rules first, then the exam itself, full screen.

  Leaving full screen (Esc), switching tab or window (Alt+Tab, Cmd+Tab), reloading, or reaching
  for a screenshot ends the exam as a fail and emails the faculty member (FinalExamController),
  with a danger siren (public/js/sfx.js "alarm"). A question map and the timer sit on the right.
  Copy, paste, right-click, text selection and printing are blocked, and every question carries
  a watermark with the student's name. A browser cannot stop a phone camera, so the watermark is
  what makes a leaked photo traceable.

  A module's mini boss (MiniBossController, `$mini` set) is taken on this same page under the same
  rules; only its lobby and its outcome screen are its own (partials/mini-boss-*).
--}}
@php $mini ??= null; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $mini ? 'Mini Boss · Module '.$mini['module'] : 'Final Exam' }} · {{ $courseTitle }} · TechLab</title>
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
    /* Question map: one square per question. Filled = answered, gold ring = the one you're on. */
    .qcell { display: grid; place-items: center; height: 40px; min-width: 40px; border-radius: 8px; font: 600 14px/1 'Space Mono', monospace;
      color: rgb(var(--c-muted)); background: transparent; border: 2px solid var(--glass-border); transition: background-color .15s ease, border-color .15s ease, color .15s ease, transform .1s ease; }
    .qcell:hover { border-color: rgba(115,182,255,.6); color: rgb(var(--c-ink)); }
    .qcell:active { transform: scale(.94); }
    .qcell:focus-visible { outline: 3px solid #9ad0ff; outline-offset: 2px; }
    .qcell.is-done { color: #06142e; background: #73b6ff; border-color: #73b6ff; }
    .qcell.is-here { box-shadow: 0 0 0 3px #ffc400; }
    .qkey { display: inline-block; width: 14px; height: 14px; border-radius: 4px; border: 2px solid var(--glass-border); }
    .qkey.is-done { background: #73b6ff; border-color: #73b6ff; }
    .qkey.is-here { box-shadow: 0 0 0 2px #ffc400; }
    .qmap-strip { scrollbar-width: thin; }
    .qcard { scroll-margin-top: 120px; transition: box-shadow .2s ease; }
    /* Timer: calm, then amber under 5 minutes, then red and pulsing in the last minute. */
    .clock-pill { background: var(--glass); }
    .clock-pill.t-warn, .time-track i.t-warn { background: rgba(255,196,0,.2); color: #ffc400; }
    .clock-pill.t-danger { background: rgba(255,107,129,.2); color: #ff6b81; }
    .clock-big.t-warn { color: #ffc400; } .clock-big.t-danger { color: #ff6b81; }
    .t-danger.clock-pill, .t-danger.clock-big { animation: pulse-red 1s steps(2, jump-none) infinite; }
    @keyframes pulse-red { 50% { opacity: .55; } }
    .time-track { height: 10px; border-radius: 999px; background: var(--glass-border); overflow: hidden; }
    .time-track i { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #5be1ff, #73b6ff); transition: width 1s linear; }
    .time-track i.t-warn { background: #ffc400; } .time-track i.t-danger { background: #ff6b81; }
    @media (prefers-reduced-motion: reduce) { .t-danger.clock-pill, .t-danger.clock-big { animation: none; } .time-track i { transition: none; } }
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
      @if ($mini)
        @include('student.course.partials.mini-boss-lobby')
      @else
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
            <li><b>Getting caught is a strike, and strikes carry over to your next try.</b> Caught a <b>second time</b> and you're <b>banned</b> from the Final Boss: no more tries and no certificate, unless {{ $captain ?? 'your captain' }} lifts it.</li>
            <li><b>If you don't pass</b> (a low score, running out of time, or leaving the exam), <b>you go back to the start of the course</b> and redo every lesson before the Final Boss opens again.</li>
            <li>When it's over you see your score, but not the answers: the Final Boss keeps its secrets for your next try.</li>
            @if ($exam->minutes)
              <li>When the timer reaches zero, your answers are handed in automatically.</li>
            @endif
            <li>Close other apps and turn off pop-up notifications before you start.</li>
          </ul>
        </div>

        @if ($strikes)
          <p class="mt-5 rounded-[10px] border border-[rgba(255,107,129,0.35)] bg-[rgba(255,107,129,0.1)] px-4 py-3 text-[15px] text-[#ff6b81]">
            <b>You already have 1 strike.</b> Leave the exam one more time and you're banned from the Final Boss.
          </p>
        @endif

        <label class="mt-6 flex cursor-pointer items-start gap-3 text-[15px]">
          <input type="checkbox" class="mt-1 accent-[#ffc400]" x-model="agreed">
          <span>I understand the rules. If I leave the exam, I fail.</span>
        </label>

        <p x-show="error" x-cloak class="mt-4 rounded-[10px] border border-[rgba(255,107,129,0.35)] bg-[rgba(255,107,129,0.1)] px-4 py-3 text-[14px] text-[#ff6b81]" x-text="error"></p>

        <button type="button" class="px-btn px-btn-blue mt-6" :disabled="!agreed || phase === 'starting'" @click="start()"
                x-text="phase === 'starting' ? 'Entering the arena…' : 'Enter full screen and start'"></button>
      @elseif ($state === 'locked')
        <p class="mt-5 text-[16px] leading-relaxed text-muted">Clear every lesson first. The Final Boss only opens after that.</p>
        <a href="{{ $backUrl }}" class="px-btn mt-6">Back to the lessons</a>
      @elseif ($state === 'not_set')
        <p class="mt-5 text-[16px] leading-relaxed text-muted">You cleared every lesson! {{ $captain ?? 'Your captain' }} hasn't set the final exam yet. Check back soon.</p>
        <a href="{{ $backUrl }}" class="px-btn mt-6">Back to the course</a>
      @elseif ($state === 'passed')
        <p class="mt-5 text-[16px] leading-relaxed">You defeated the Final Boss with <b>{{ $result['percent'] }}%</b>. 🎉</p>
        <a href="{{ $certificateUrl }}" class="px-btn px-btn-blue mt-6">View your certificate</a>
      @else
        <p class="mt-5 text-[16px] leading-relaxed text-muted">
          @if ($result['banned'])
            Your exam ended early: <b class="text-[#ff6b81]">{{ strtolower($result['violation']) }}</b>. That was your second strike, so <b class="text-[#ff6b81]">you're banned from the Final Boss</b>. Only {{ $captain ?? 'your captain' }} can lift it.
          @elseif ($result['violation'])
            Your exam ended early: <b class="text-[#ff6b81]">{{ strtolower($result['violation']) }}</b>. {{ $captain ?? 'Your captain' }} has been told.
          @else
            You scored <b class="text-ink">{{ $result['percent'] }}%</b> and needed {{ $result['pass_percent'] }}%.
          @endif
          @if ($result['banned'])
          @elseif ($result['redo'])
            You're back at the start of the course: redo every lesson, and the Final Boss opens again.
          @else
            {{ $captain ?? 'Your captain' }} can let you try again.
          @endif
        </p>
        <a href="{{ $backUrl }}" class="px-btn {{ $result['redo'] ? 'px-btn-blue' : '' }} mt-6">{{ $result['redo'] ? 'Start the course again' : 'Back to the course' }}</a>
      @endif
      @endif
    </section>

  </main>

  {{-- ═══════════ THE EXAM ═══════════ --}}
  <div x-show="phase === 'exam' || phase === 'submitting'" x-cloak class="min-h-screen">
    <header class="sticky top-0 z-20 border-b border-glassBorder bg-[rgba(6,6,26,0.92)] backdrop-blur">
      <div class="mx-auto flex max-w-[1180px] flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-8">
        <p class="px-font truncate text-[1.15rem] font-medium">{{ $exam?->title ?? 'Final Exam' }}</p>
        <div class="flex items-center gap-4 text-[15px]">
          <span class="text-muted"><span x-text="answeredCount()"></span> / <span x-text="questions.length"></span> answered</span>
          <span x-show="secondsLeft !== null" class="clock-pill px-font rounded-full px-4 py-1.5 text-[1.25rem] font-semibold" :class="timeLevel()"
                role="timer" aria-label="Time left" x-text="clock()"></span>
        </div>
      </div>
      {{-- Phones and small screens: the question map is a strip under the bar. --}}
      <nav class="qmap-strip mx-auto flex max-w-[1180px] gap-1.5 overflow-x-auto px-4 pb-3 sm:px-8 lg:hidden" aria-label="Question map">
        <template x-for="(q, qi) in questions" :key="q.id">
          <button type="button" class="qcell flex-none" :class="cellClass(q, qi)" @click="goTo(qi)"
                  :aria-label="cellLabel(q, qi)" :aria-current="current === qi ? 'step' : null" x-text="qi + 1"></button>
        </template>
      </nav>
    </header>

    <div class="mx-auto grid max-w-[1180px] items-start gap-8 px-4 py-8 sm:px-8 lg:grid-cols-[minmax(0,1fr)_280px]">
      <main class="relative min-w-0">
        <div class="watermark" aria-hidden="true">
          <template x-for="(m, i) in marks" :key="i">
            <span :style="`left:${m.x}%;top:${m.y}%`" x-text="stamp"></span>
          </template>
        </div>

        <form @submit.prevent="askSubmit()" class="relative z-[2] flex flex-col gap-6">
          <template x-for="(q, qi) in questions" :key="q.id">
            <fieldset class="qcard px-card p-5 sm:p-7" :id="'q-' + q.id" data-q :class="current === qi && 'is-current'">
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

          <div id="hand-in" class="px-card flex flex-wrap items-center gap-4 p-5">
            <template x-if="!confirming">
              <button type="submit" class="px-btn px-btn-blue" :disabled="phase === 'submitting'">Hand in my answers</button>
            </template>
            <template x-if="confirming">
              <div class="flex flex-wrap items-center gap-3">
                <span class="text-[15px]" x-text="unanswered() ? `${unanswered()} question(s) still blank. Hand in anyway?` : 'Hand in your answers? You cannot change them after.'"></span>
                <button type="button" class="px-btn px-btn-blue" :disabled="phase === 'submitting'" @click="submit()"
                        x-text="phase === 'submitting' ? 'Handing in…' : 'Yes, hand in'"></button>
                <button type="button" class="px-btn" @click="confirming = false">Keep going</button>
              </div>
            </template>
            <p x-show="error" x-cloak class="w-full text-[14px] text-[#ff6b81]" x-text="error"></p>
          </div>
        </form>
      </main>

      {{-- Big screens: the timer and the question map stay on the right while the questions scroll. --}}
      <aside class="sticky top-[84px] hidden flex-col gap-4 lg:flex" aria-label="Exam progress">
        <section class="px-card p-5" x-show="secondsLeft !== null">
          <p class="text-[12px] font-semibold uppercase tracking-[0.2em] text-muted">Time left</p>
          <p class="clock-big px-font mt-1 text-[2.6rem] font-bold leading-none" :class="timeLevel()" x-text="clock()"></p>
          <div class="time-track mt-3" aria-hidden="true"><i :class="timeLevel()" :style="`width:${timePercent()}%`"></i></div>
          <p class="mt-2 text-[13px] text-muted" x-text="secondsLeft <= 60 ? 'Last minute! Answers hand in at 00:00.' : (secondsLeft <= 300 ? 'Under 5 minutes left.' : 'Answers hand in by themselves at 00:00.')"></p>
        </section>

        <nav class="px-card p-5" aria-label="Question map">
          <div class="flex items-baseline justify-between">
            <p class="text-[12px] font-semibold uppercase tracking-[0.2em] text-muted">Question map</p>
            <p class="text-[13px] text-muted"><span class="text-ink" x-text="answeredCount()"></span>/<span x-text="questions.length"></span></p>
          </div>
          <div class="mt-4 grid grid-cols-5 gap-2">
            <template x-for="(q, qi) in questions" :key="q.id">
              <button type="button" class="qcell" :class="cellClass(q, qi)" @click="goTo(qi)"
                      :aria-label="cellLabel(q, qi)" :aria-current="current === qi ? 'step' : null" x-text="qi + 1"></button>
            </template>
          </div>
          <ul class="mt-4 flex flex-col gap-1.5 text-[12.5px] text-muted">
            <li class="flex items-center gap-2"><i class="qkey is-done"></i> Answered</li>
            <li class="flex items-center gap-2"><i class="qkey"></i> Not answered yet</li>
            <li class="flex items-center gap-2"><i class="qkey is-here"></i> You are here</li>
          </ul>
          <button type="button" class="px-btn mt-5 w-full" @click="goToHandIn()">Review &amp; hand in</button>
        </nav>
      </aside>
    </div>
  </div>

  {{-- Shown the instant a screenshot key combination starts, so the capture comes out blank. --}}
  <div x-show="shielded && phase === 'exam'" x-cloak class="shield" aria-hidden="true">
    <p class="px-font text-2xl text-muted">Screenshots are not allowed</p>
  </div>

  {{-- ═══════════ THE OUTCOME ═══════════ --}}
  <main x-show="phase === 'ended'" x-cloak class="mx-auto flex min-h-screen max-w-[680px] flex-col justify-center px-4 py-10 sm:px-8">
    <section class="px-card p-6 text-center sm:p-10">
      @if ($mini)
        @include('student.course.partials.mini-boss-outcome')
      @else
      <template x-if="result && result.banned">
        <div>
          <p class="text-[13px] font-semibold uppercase tracking-[0.2em] text-[#ff6b81]">Second strike</p>
          <h1 class="px-font mt-3 text-[clamp(2rem,6vw,3rem)] font-bold leading-none">You're banned</h1>
          <p class="mt-5 text-[16px] leading-relaxed text-muted">
            <span x-text="result.violation"></span>. You were caught before, so this was your second strike: you can't face the Final Boss again.
            {{ $captain ?? 'Your captain' }} has been notified, and only they can lift the ban.
          </p>
        </div>
      </template>
      <template x-if="result && result.violation && !result.banned">
        <div>
          <p class="text-[13px] font-semibold uppercase tracking-[0.2em] text-[#ff6b81]" x-text="'Exam ended' + (result.strikes ? ' · strike ' + result.strikes + ' of 2' : '')"></p>
          <h1 class="px-font mt-3 text-[clamp(2rem,6vw,3rem)] font-bold leading-none">You left the arena</h1>
          <p class="mt-5 text-[16px] leading-relaxed text-muted">
            <span x-text="result.violation"></span>. That counts as a fail, and {{ $captain ?? 'your captain' }} has been notified.
            <span x-show="result.redo">You're back at the start of the course: redo every lesson, and the Final Boss opens again.</span>
            <span x-show="result.strikes">Get caught again and you're banned.</span> If it was an accident, talk to them: they can let you try again.
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
            <span x-show="result.redo">You're back at the start of the course: redo every lesson, and the Final Boss opens again.</span>
            <span x-show="!result.redo">Talk to {{ $captain ?? 'your captain' }}: they can let you try again.</span>
          </p>
        </div>
      </template>
      <template x-if="!result">
        <div>
          <h1 class="px-font text-[2rem] font-bold">Exam ended</h1>
          <p class="mt-4 text-muted">We couldn't reach TechLab to confirm your result. Reload this page to see it.</p>
        </div>
      </template>
      @endif

      <div class="mt-8 flex flex-wrap justify-center gap-3">
        <template x-if="result?.certificate_url">
          <a :href="result.certificate_url" class="px-btn px-btn-blue">View your certificate</a>
        </template>
        <a href="{{ $backUrl }}" class="px-btn" x-text="result?.redo ? 'Start the course again' : 'Back to the course'"></a>
      </div>
    </section>

  </main>

  <script>
    function finalExam() {
      const urls = @js($urls ?? ($exam ? [
        'start' => route('student.final-exam.start', $exam),
        'violation' => route('student.final-exam.violation', $exam),
        'submit' => route('student.final-exam.submit', $exam),
      ] : null));
      const csrf = document.querySelector('meta[name="csrf-token"]').content;
      const stamp = @js($student->name.' · '.$student->email);

      return {
        phase: 'lobby', agreed: false, error: '', confirming: false,
        questions: [], answers: {}, result: null,
        secondsLeft: null, totalSeconds: @js($exam?->minutes ? $exam->minutes * 60 : null), timer: null,
        current: 0,
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
          // The question map's "you are here" follows the scroll.
          let ticking = false;
          window.addEventListener('scroll', () => {
            if (ticking || this.phase !== 'exam') return;
            ticking = true;
            requestAnimationFrame(() => { this.trackCurrent(); ticking = false; });
          }, { passive: true });
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
          window.techlabSfx?.unlock?.();
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
            this.totalSeconds = Math.max(this.totalSeconds || 0, body.seconds_left || 0) || null;
            this.current = 0;
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

        // ─── question map ───
        isAnswered(q) { const a = this.answers[q.id]; return Array.isArray(a) ? a.length > 0 : a !== null && a !== undefined; },
        cellClass(q, qi) { return { 'is-done': this.isAnswered(q), 'is-here': this.current === qi }; },
        cellLabel(q, qi) { return `Question ${qi + 1}, ${this.isAnswered(q) ? 'answered' : 'not answered yet'}`; },
        /** The question whose top has passed a line a third of the way down the screen. */
        trackCurrent() {
          const line = window.innerHeight / 3;
          let at = 0;
          document.querySelectorAll('[data-q]').forEach((el, i) => { if (el.getBoundingClientRect().top <= line) at = i; });
          // Scrolled to the very bottom: the last question is the one in view.
          if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 4) at = this.questions.length - 1;
          this.current = at;
        },
        goTo(qi) {
          const q = this.questions[qi];
          const el = q && document.getElementById('q-' + q.id);
          if (!el) return;
          this.current = qi;
          el.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' });
          // Keyboard users land on the question's first choice.
          el.querySelector('input')?.focus({ preventScroll: true });
        },
        goToHandIn() {
          document.getElementById('hand-in')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
          this.confirming = true;
        },

        timeLevel() {
          if (this.secondsLeft === null) return '';
          return this.secondsLeft <= 60 ? 't-danger' : (this.secondsLeft <= 300 ? 't-warn' : '');
        },
        timePercent() {
          if (!this.totalSeconds || this.secondsLeft === null) return 100;
          return Math.max(0, Math.min(100, (this.secondsLeft / this.totalSeconds) * 100));
        },

        clock() {
          const s = this.secondsLeft ?? 0;
          return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
        },

        answeredCount() {
          return this.questions.filter(q => this.isAnswered(q)).length;
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
          // Danger! A siren, even if clicks are muted.
          window.techlabSfx?.play('alarm', true);

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
            this.result = { status: 'failed', caught: true, violation: 'The exam ended because you left it' };
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
