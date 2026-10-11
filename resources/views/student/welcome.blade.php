{{--
  Onboarding right after sign-up ($intro = true): Astro says hello, the student builds their animal astronaut,
  picks a codename, then "welcome to the crew" saves both. With $intro = false it is just the builder with
  the codename under it (change your look or codename).
  The astronaut is drawn here in the browser from the same layers AvatarService draws on the server.
--}}
@php
  $first = explode(' ', trim($user->name))[0] ?: 'Cadet';
  $lines = [
    "Hey there, {$first}! I'm Astro, Captain of the TechLab crew.",
    'Our home planet, Codexia, is under threat from the aliens of Planet Doom. We need new cadets, fast!',
    "You'll learn to code, build networks and defend systems, one mission at a time, earning XP and gems along the way.",
    "But every cadet needs a spacesuit first. Pick your animal and let's suit you up!",
  ];
  // Back from a failed save: open on the step that needs fixing.
  $startStep = $errors->any() ? ($intro && $errors->has('codename') ? 'name' : 'build') : null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $intro ? 'Welcome aboard' : 'Your astronaut' }} · TechLab</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Pixelify+Sans:wght@500;600;700&display=swap" rel="stylesheet" />
  <meta name="theme-color" content="#06061a">
  <link rel="stylesheet" href="{{ asset('css/theme.css') }}?v={{ filemtime(public_path('css/theme.css')) }}">
  <script src="{{ asset('js/theme.js') }}?v={{ filemtime(public_path('js/theme.js')) }}"></script>
  <script defer src="{{ asset('js/sfx.js') }}?v={{ filemtime(public_path('js/sfx.js')) }}"></script>
  <script defer src="{{ asset('js/onboarding-music.js') }}?v={{ filemtime(public_path('js/onboarding-music.js')) }}" data-intro="{{ $intro ? '1' : '0' }}"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: {
      colors: { void: 'rgb(var(--c-void) / <alpha-value>)', ink: 'rgb(var(--c-ink) / <alpha-value>)', muted: 'rgb(var(--c-muted) / <alpha-value>)' },
      fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'], display: ['Space Grotesk', 'sans-serif'] },
    } } };
  </script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
  <style>@include('student.partials.pixel-ui')</style>
  <style>
    [x-cloak] { display: none !important; }
    body { background: #06061a; -webkit-font-smoothing: antialiased; }
    .space { position: fixed; inset: 0; z-index: -1; background: radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%), linear-gradient(160deg, #0a0826 0%, #120a33 45%, #1e1259 100%); }
    .pix { image-rendering: pixelated; shape-rendering: crispEdges; }

    /* Astro bobs on a 2-step beat, like a sprite */
    .bob { animation: bob 1.6s steps(2, jump-none) infinite; }
    @keyframes bob { 50% { transform: translateY(-4px); } }

    /* Progress bar: chunky track, green fill that springs forward */
    .ob-bar { height: 14px; background: var(--px-track); box-shadow: 0 -2px 0 0 var(--px-line), 0 2px 0 0 var(--px-line), -2px 0 0 0 var(--px-line), 2px 0 0 0 var(--px-line); }
    .ob-bar > i { display: block; height: 100%; background: #9be85a; box-shadow: inset 0 -3px 0 rgba(0,0,0,.18), inset 0 3px 0 rgba(255,255,255,.35); transition: width .5s cubic-bezier(.3,1.25,.5,1); }

    /* Speech bubble with its tail pointing up at Astro (talk) or left at Astro (builder) */
    .bubble-up { position: relative; }
    .bubble-up::before { content: ""; position: absolute; top: -14px; left: 50%; margin-left: -10px; border: 10px solid transparent; border-bottom: 10px solid var(--px-line); border-top: 0; }
    .bubble-up::after  { content: ""; position: absolute; top: -9px; left: 50%; margin-left: -8px; border: 8px solid transparent; border-bottom: 8px solid var(--px-solid); border-top: 0; }
    .px-bubble, .bubble-up { background: var(--px-solid); }
    .caret { display: inline-block; width: .5em; height: 1em; margin-left: 2px; vertical-align: -2px; background: currentColor; animation: caret .8s steps(2, jump-none) infinite; }
    @keyframes caret { 50% { opacity: 0; } }

    /* The stage: night sky over a strip of Codexia, the astronaut standing on it */
    .stage { position: relative; overflow: hidden; background: radial-gradient(90% 70% at 50% 18%, #2b1f7a 0%, #150f45 55%, #0b0826 100%); }
    .stage::before { content: ""; position: absolute; inset: 0; background-image: radial-gradient(1.5px 1.5px at 12% 20%, #fff 50%, transparent 51%), radial-gradient(1.5px 1.5px at 82% 14%, #fff 50%, transparent 51%), radial-gradient(1px 1px at 64% 32%, #cfd8ff 50%, transparent 51%), radial-gradient(1px 1px at 28% 44%, #cfd8ff 50%, transparent 51%), radial-gradient(1.5px 1.5px at 90% 52%, #fff 50%, transparent 51%), radial-gradient(1px 1px at 8% 62%, #fff 50%, transparent 51%); opacity: .8; }
    .ground { position: absolute; left: -10%; right: -10%; bottom: -64%; height: 90%; border-radius: 50%; background: radial-gradient(60% 40% at 50% 8%, #3d2f9c 0%, #261c6e 60%, #1a1350 100%); box-shadow: inset 0 6px 0 rgba(155,107,255,.35); }
    .shadow { position: absolute; left: 50%; width: 46%; height: 18px; margin-left: -23%; border-radius: 50%; background: rgba(6,6,26,.55); }
    .hero { position: relative; display: block; width: min(240px, 54vw); height: auto; margin: 0 auto; filter: drop-shadow(0 6px 0 rgba(6,6,26,.35)); }
    .hero-wrap { animation: idle 2.4s steps(2, jump-none) infinite; }
    @keyframes idle { 50% { transform: translateY(-5px); } }

    /* Tabs + option tiles: press answers on pointer-down, the chosen one is ringed */
    .tab { position: relative; display: grid; place-items: center; gap: 4px; height: 64px; color: rgb(var(--c-muted)); transition: color .2s ease; }
    .tab[aria-selected="true"] { color: rgb(var(--c-ink)); }
    .tab[aria-selected="true"]::after { content: ""; position: absolute; left: 10%; right: 10%; bottom: 0; height: 4px; background: #14a0ff; }
    .tab:active svg { transform: scale(.9); }
    .tab svg { transition: transform .1s ease; }
    .opt { position: relative; display: grid; place-items: center; background: var(--px-inner); cursor: pointer; transition: transform .12s ease, box-shadow .2s ease, background-color .2s ease;
      box-shadow: 0 -3px 0 0 var(--px-line), 0 3px 0 0 var(--px-line), -3px 0 0 0 var(--px-line), 3px 0 0 0 var(--px-line); margin: 3px; }
    .opt:hover { background: rgba(123,142,220,.16); }
    .opt:active { transform: scale(.94); }
    .opt[aria-checked="true"] { background: rgba(20,160,255,.16); box-shadow: 0 -3px 0 0 #14a0ff, 0 3px 0 0 #14a0ff, -3px 0 0 0 #14a0ff, 3px 0 0 0 #14a0ff, 0 0 0 6px rgba(20,160,255,.18); }
    .opt:focus-visible { outline: 3px solid #9ad0ff; outline-offset: 5px; }
    .swatch { width: 100%; aspect-ratio: 1; }
    .swatch > i { position: absolute; inset: 7px; box-shadow: inset 0 -4px 0 rgba(0,0,0,.18); }
    .opt-label { font-family: 'Pixelify Sans', sans-serif; font-size: 14px; line-height: 1.1; text-align: center; color: rgb(var(--c-ink)); }
    .cn-input { height: 58px; padding: 0 16px; background: var(--px-inner); color: rgb(var(--c-ink)); font-family: 'Pixelify Sans', sans-serif; font-size: 22px;
      box-shadow: 0 -3px 0 0 var(--px-line), 0 3px 0 0 var(--px-line), -3px 0 0 0 var(--px-line), 3px 0 0 0 var(--px-line); margin: 3px; outline: none; }
    .cn-input:focus { box-shadow: 0 -3px 0 0 #14a0ff, 0 3px 0 0 #14a0ff, -3px 0 0 0 #14a0ff, 3px 0 0 0 #14a0ff, 0 0 0 6px rgba(20,160,255,.18); }
    .cn-input[aria-invalid="true"] { box-shadow: 0 -3px 0 0 #ff6b81, 0 3px 0 0 #ff6b81, -3px 0 0 0 #ff6b81, 3px 0 0 0 #ff6b81; }
    .px-btn:disabled { opacity: .5; cursor: not-allowed; filter: none; transform: none; }
    .sect { font-family: 'Space Mono', monospace; font-size: 12px; letter-spacing: .18em; text-transform: uppercase; color: rgb(var(--c-muted)); }

    /* "Welcome to the crew": stars burst out once around the finished astronaut */
    .burst i { position: absolute; left: 50%; top: 45%; width: 10px; height: 10px; background: #f5c04a; clip-path: polygon(50% 0, 62% 38%, 100% 50%, 62% 62%, 50% 100%, 38% 62%, 0 50%, 38% 38%); opacity: 0; animation: burst 1.1s cubic-bezier(.2,.8,.3,1) forwards; }
    @keyframes burst { 0% { opacity: 1; transform: translate(-50%, -50%) scale(.4); } 100% { opacity: 0; transform: translate(calc(-50% + var(--dx)), calc(-50% + var(--dy))) scale(1.2); } }

    @media (prefers-reduced-motion: reduce) {
      .bob, .hero-wrap, .caret, .burst i { animation: none !important; }
      .ob-bar > i, .opt, .tab, .tab svg { transition: none; }
      .burst { display: none; }
    }
  </style>
</head>
<body class="bg-void font-sans text-ink antialiased">
  <div class="space" aria-hidden="true"></div>

  <main class="mx-auto flex min-h-screen max-w-[1100px] flex-col px-4 pb-12 pt-8 sm:px-8"
        x-data="welcome(@js($avatarConfig), @js($look), @js($lines), @js($intro), @js($codename), @js(route('student.codename.check')), @js($startStep), @js($errors->first('codename') ?: null), @js($user->codename !== null || old('codename') !== null))" x-cloak>

    {{-- Progress + Skip (onboarding) / Back (changing your look later) --}}
    <div class="flex items-center gap-6">
      @if ($intro)
        <div class="ob-bar flex-1" role="progressbar" aria-label="Onboarding progress" :aria-valuenow="Math.round(progress)" aria-valuemin="0" aria-valuemax="100"><i :style="`width:${progress}%`"></i></div>
        <button type="button" class="font-display text-[17px] font-medium text-muted transition-colors hover:text-ink" x-show="step === 'talk'" @click="toBuilder()">Skip</button>
      @else
        <a href="{{ route('student.dashboard') }}" class="font-display text-[17px] font-medium text-muted transition-colors hover:text-ink">← Back</a>
      @endif
    </div>

    {{-- ─── 1. Astro says hello ─── --}}
    @if ($intro)
    <section x-show="step === 'talk'" class="flex flex-1 flex-col items-center justify-center py-10"
             x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 translate-y-3"
             x-transition:leave="transition duration-200 ease-in" x-transition:leave-end="opacity-0 -translate-y-3">
      <x-astro-mascot class="bob pix h-auto w-[min(180px,40vw)]" />
      <div class="px-card bubble-up mt-10 w-full max-w-[860px] px-7 py-6" aria-live="polite">
        <p class="px-font min-h-[3.2em] text-[clamp(1.05rem,2.2vw,1.45rem)] leading-snug"><span x-text="typed"></span><span class="caret" x-show="typing" aria-hidden="true"></span></p>
      </div>
      <button type="button" class="px-btn px-btn-blue mt-10 w-[min(320px,80vw)]" @click="next()" x-text="typing ? 'Continue' : (line === lines.length - 1 ? 'Make my astronaut' : 'Continue')"></button>
    </section>
    @endif

    {{-- ─── 2. Build your astronaut ─── --}}
    <section x-show="step === 'build'" class="flex flex-1 flex-col py-6"
             x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 translate-y-3">
      <header class="flex items-center gap-5">
        <x-astro-mascot class="bob pix h-auto w-16 flex-none sm:w-20" />
        <p class="px-card px-bubble px-font min-w-0 flex-1 px-5 py-4 text-[clamp(1rem,2vw,1.3rem)]">
          {{ $intro ? "First, let's choose your look. You can switch this up later, too." : 'Looking sharp, Cadet. Change anything you like.' }}
        </p>
      </header>

      <div class="px-card mt-8 grid overflow-hidden md:grid-cols-[minmax(0,5fr)_minmax(0,6fr)]">
        {{-- Live preview --}}
        <div class="stage flex min-h-[360px] flex-col items-center justify-end pb-6 pt-10">
          <div class="ground" aria-hidden="true"></div>
          <div class="relative" style="margin-bottom: 18px">
            <div class="shadow" style="bottom: -8px" aria-hidden="true"></div>
            <div class="hero-wrap"><div x-ref="hero">
              <svg class="hero pix" viewBox="0 0 24 30" role="img" :aria-label="describe()" x-html="svg(look)"></svg>
            </div></div>
          </div>
          <button type="button" class="px-btn px-btn-ghost relative mt-2" @click="shuffle()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 0 1 15.5-6.2L21 8M21 3v5h-5M21 12a9 9 0 0 1-15.5 6.2L3 16M3 21v-5h5"/></svg>
            Random
          </button>
        </div>

        {{-- Options --}}
        <div class="flex min-w-0 flex-col border-t-2 border-[color:var(--px-track)] md:border-l-2 md:border-t-0">
          <div class="grid grid-cols-3 border-b-2 border-[color:var(--px-track)]" role="tablist" aria-label="Customize">
            <template x-for="t in tabs" :key="t.id">
              <button type="button" class="tab" role="tab" :id="`tab-${t.id}`" :aria-controls="`panel-${t.id}`" :aria-selected="(tab === t.id).toString()" :tabindex="tab === t.id ? 0 : -1"
                      @click="tab = t.id" @keydown.arrow-right.prevent="moveTab(1)" @keydown.arrow-left.prevent="moveTab(-1)">
                <span x-html="t.icon" aria-hidden="true"></span>
                <span class="text-[12px] font-medium" x-text="t.label"></span>
              </button>
            </template>
          </div>

          <div class="flex-1 overflow-y-auto p-6" style="max-height: 470px">
            {{-- Animal --}}
            <div x-show="tab === 'animal'" id="panel-animal" role="tabpanel" aria-labelledby="tab-animal">
              <p class="sect">Animal</p>
              <div class="mt-3 grid grid-cols-3 gap-2 sm:grid-cols-5" role="radiogroup" aria-label="Animal">
                <template x-for="(a, key) in cfg.animals" :key="key">
                  <button type="button" class="opt gap-1 px-1 pb-2 pt-3" role="radio" :aria-checked="(look.animal === key).toString()" @click="setAnimal(key)">
                    <svg class="pix h-16 w-auto" viewBox="3 0 18 18" aria-hidden="true" x-html="svg({ ...withAnimal(key), helmet: false })"></svg>
                    <span class="opt-label" x-text="a.label"></span>
                  </button>
                </template>
              </div>
              <p class="sect mt-7">Coat</p>
              <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4" role="radiogroup" aria-label="Coat">
                <template x-for="(c, key) in cfg.animals[look.animal].coats" :key="look.animal + key">
                  <button type="button" class="opt gap-2 px-2 py-3" role="radio" :aria-checked="(look.coat === key).toString()" @click="set('coat', key)">
                    <i class="block h-6 w-6" :style="`background:linear-gradient(135deg, ${c.f} 0 58%, ${c.k} 58% 100%);box-shadow:inset 0 -3px 0 rgba(0,0,0,.2)`"></i>
                    <span class="opt-label" x-text="c.label"></span>
                  </button>
                </template>
              </div>
            </div>

            {{-- Suit --}}
            <div x-show="tab === 'suit'" id="panel-suit" role="tabpanel" aria-labelledby="tab-suit">
              <p class="sect">Design</p>
              <div class="mt-3 grid grid-cols-3 gap-2" role="radiogroup" aria-label="Suit design">
                <template x-for="(s, key) in cfg.suits" :key="key">
                  <button type="button" class="opt gap-1 px-1 pb-2 pt-3" role="radio" :aria-checked="(look.suit === key).toString()" @click="set('suit', key)">
                    <svg class="pix h-24 w-auto" viewBox="0 0 24 30" aria-hidden="true" x-html="svg({ ...look, suit: key, helmet: false })"></svg>
                    <span class="opt-label" x-text="s.label"></span>
                  </button>
                </template>
              </div>
              <p class="sect mt-7">Color</p>
              <div class="mt-3 grid grid-cols-4 gap-2 sm:grid-cols-8" role="radiogroup" aria-label="Suit color">
                <template x-for="(c, key) in cfg.suitColors" :key="key">
                  <button type="button" class="opt swatch" role="radio" :aria-label="key" :aria-checked="(look.suit_color === key).toString()" @click="set('suit_color', key)"><i :style="`background:${c.s}`"></i></button>
                </template>
              </div>
              <p class="sect mt-7">Patch</p>
              <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4" role="radiogroup" aria-label="Chest patch">
                <template x-for="(p, key) in cfg.patches" :key="key">
                  <button type="button" class="opt gap-2 px-2 py-3" role="radio" :aria-checked="(look.patch === key).toString()" @click="set('patch', key)">
                    <i class="block h-5 w-5" :style="`background:${p.p};box-shadow:inset 0 -3px 0 rgba(0,0,0,.2)`"></i>
                    <span class="opt-label" x-text="p.label"></span>
                  </button>
                </template>
              </div>
            </div>

            {{-- Helmet --}}
            <div x-show="tab === 'helmet'" id="panel-helmet" role="tabpanel" aria-labelledby="tab-helmet">
              <p class="sect">Helmet</p>
              <div class="mt-3 grid grid-cols-2 gap-2" role="radiogroup" aria-label="Helmet">
                <template x-for="on in [true, false]" :key="String(on)">
                  <button type="button" class="opt gap-2 px-2 pb-3 pt-4" role="radio" :aria-checked="(look.helmet === on).toString()" @click="set('helmet', on)">
                    <svg class="pix h-20 w-auto" viewBox="3 0 18 18" aria-hidden="true" x-html="svg({ ...look, helmet: on })"></svg>
                    <span class="opt-label" x-text="on ? 'Helmet on' : 'Helmet off'"></span>
                  </button>
                </template>
              </div>
              <p class="sect mt-7">Visor</p>
              <div class="mt-3 grid grid-cols-5 gap-2" role="radiogroup" aria-label="Visor color">
                <template x-for="(c, key) in cfg.visors" :key="key">
                  <button type="button" class="opt swatch" role="radio" :aria-label="key" :aria-checked="(look.visor === key).toString()" @click="set('visor', key); set('helmet', true)"><i :style="`background:${c}`"></i></button>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>

      @unless ($intro)
        <div class="px-card mt-6 p-6">
          @include('student.partials.codename-field', ['id' => 'edit'])
          <p class="text-[14px] text-muted">Your crew sees this on the leaderboard instead of your full name.</p>
        </div>
      @endunless

      <div class="mt-8 flex flex-col items-center gap-4">
        @if ($intro)
          <button type="button" class="px-btn px-btn-blue w-[min(320px,80vw)]" @click="toName()">Looks good!</button>
          <a href="{{ route('student.dashboard') }}" class="text-[14px] text-muted underline-offset-4 hover:text-ink hover:underline">I'll do this later</a>
        @else
          <button type="submit" form="avatar-form" class="px-btn px-btn-blue w-[min(320px,80vw)]" :disabled="codenameState === 'bad'">Save changes</button>
        @endif
      </div>
    </section>

    {{-- ─── 3. Pick a codename ─── --}}
    @if ($intro)
      <section x-show="step === 'name'" class="flex flex-1 flex-col items-center justify-center py-8"
               x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 translate-y-3">
        <header class="flex w-full max-w-[860px] items-center gap-5">
          <x-astro-mascot class="bob pix h-auto w-16 flex-none sm:w-20" />
          <p class="px-card px-bubble px-font min-w-0 flex-1 px-5 py-4 text-[clamp(1rem,2vw,1.3rem)]">Every astronaut needs a codename. It's what your crew sees on the leaderboard, so make it cool!</p>
        </header>
        <div class="px-card mt-8 grid w-full max-w-[860px] overflow-hidden sm:grid-cols-[220px_minmax(0,1fr)]">
          <div class="stage flex min-h-[220px] items-end justify-center pb-5">
            <div class="ground" aria-hidden="true"></div>
            <div class="hero-wrap relative"><svg class="pix relative block h-auto w-[120px]" viewBox="0 0 24 30" role="img" :aria-label="describe()" x-html="svg(look)"></svg></div>
          </div>
          <div class="flex flex-col justify-center p-6">
            @include('student.partials.codename-field', ['id' => 'intro', 'enter' => 'finish()'])
            <p class="text-[14px] text-muted">Letters, numbers and _ only. Hit the dice for an idea.</p>
          </div>
        </div>
        <div class="mt-8 flex flex-col items-center gap-4">
          <button type="button" class="px-btn px-btn-blue w-[min(320px,80vw)]" @click="finish()" :disabled="codenameState !== 'ok'">That's me!</button>
          <button type="button" class="text-[14px] text-muted underline-offset-4 hover:text-ink hover:underline" @click="step = 'build'">Back to my look</button>
        </div>
      </section>
    @endif

    {{-- ─── 4. Welcome to the crew ─── --}}
    @if ($intro)
      <section x-show="step === 'done'" class="flex flex-1 flex-col items-center justify-center py-10"
               x-transition:enter="transition duration-500 ease-out" x-transition:enter-start="opacity-0 scale-95">
        <div class="relative">
          <div class="burst" aria-hidden="true">
            <template x-for="(d, i) in burst" :key="i"><i :style="`--dx:${d[0]}px;--dy:${d[1]}px;animation-delay:${i * 30}ms`"></i></template>
          </div>
          <div class="hero-wrap"><svg class="hero pix" viewBox="0 0 24 30" role="img" :aria-label="describe()" x-html="svg(look)"></svg></div>
        </div>
        <div class="mt-8 flex w-full max-w-[860px] items-center gap-5">
          <x-astro-mascot class="bob pix h-auto w-16 flex-none" />
          <p class="px-card px-bubble px-font min-w-0 flex-1 px-5 py-4 text-[clamp(1.05rem,2.2vw,1.4rem)]">Welcome to the crew, <span x-text="codename"></span>! Your first mission is waiting.</p>
        </div>
        <div class="mt-10 flex flex-col items-center gap-4">
          <button type="submit" form="avatar-form" class="px-btn px-btn-yellow w-[min(320px,80vw)]">Start my mission</button>
          <button type="button" class="text-[14px] text-muted underline-offset-4 hover:text-ink hover:underline" @click="step = 'build'">Change something</button>
        </div>
      </section>
    @endif

    <form id="avatar-form" method="POST" action="{{ route('student.avatar.save') }}" class="hidden">
      @csrf
      @if ($intro)<input type="hidden" name="onboarding" value="1">@endif
      <template x-for="key in ['animal', 'coat', 'suit', 'suit_color', 'patch', 'visor']" :key="key">
        <input type="hidden" :name="key" :value="look[key]">
      </template>
      <input type="hidden" name="helmet" :value="look.helmet ? 1 : 0">
      <input type="hidden" name="codename" :value="codename">
    </form>
  </main>

  <script>
    function welcome(cfg, look, lines, intro, codename, checkUrl, startStep, codenameError, codenameTouched) {
      const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const icon = (d) => `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">${d}</svg>`;

      return {
        cfg, look, lines, intro, codename, codenameTouched,
        step: startStep || (intro ? 'talk' : 'build'),
        line: 0, typed: '', typing: false, timer: null,
        // Codename check: 'idle' (not checked yet), 'checking', 'ok' (free) or 'bad' (with the reason in codenameNote).
        codenameState: codenameError ? 'bad' : 'idle', codenameNote: codenameError || '', checkTimer: null, checkSeq: 0,
        tab: 'animal',
        tabs: [
          { id: 'animal', label: 'Animal', icon: icon('<path d="M5 4l3 4h8l3-4v9a7 7 0 0 1-14 0z"/><path d="M9.5 13h.01M14.5 13h.01M11 16.5h2"/>') },
          { id: 'suit', label: 'Suit', icon: icon('<path d="M8 3h8l4 4-3 3-1-1v12H8V9l-1 1-3-3z"/>') },
          { id: 'helmet', label: 'Helmet', icon: icon('<circle cx="12" cy="11" r="8"/><path d="M7 11a5 5 0 0 1 5-5"/><path d="M6 19h12"/>') },
        ],
        burst: Array.from({ length: 14 }, (_, i) => { const a = (i / 14) * Math.PI * 2; const r = 130 + (i % 3) * 30; return [Math.round(Math.cos(a) * r), Math.round(Math.sin(a) * r * .8)]; }),

        init() {
          if (this.step === 'talk') this.say(0);
          if (!codenameError) this.checkCodename(true);
        },

        get progress() {
          const total = this.lines.length + 3;
          const at = this.step === 'talk' ? this.line + 1 : this.step === 'build' ? this.lines.length + 1 : this.step === 'name' ? this.lines.length + 2 : total;
          return (at / total) * 100;
        },

        // Astro types each line out; pressing Continue mid-line finishes it instead of skipping it.
        say(i) {
          clearInterval(this.timer);
          this.line = i;
          const text = this.lines[i];
          if (reduce) { this.typed = text; this.typing = false; return; }
          this.typed = ''; this.typing = true;
          let n = 0;
          this.timer = setInterval(() => {
            this.typed = text.slice(0, ++n);
            if (n >= text.length) { clearInterval(this.timer); this.typing = false; }
          }, 22);
        },
        next() {
          if (this.typing) { clearInterval(this.timer); this.typed = this.lines[this.line]; this.typing = false; return; }
          if (this.line < this.lines.length - 1) this.say(this.line + 1); else this.toBuilder();
        },
        toBuilder() { clearInterval(this.timer); this.typing = false; this.step = 'build'; window.scrollTo({ top: 0 }); },
        // The first idea was rolled before they picked an animal: roll one for their animal instead.
        toName() { if (!this.codenameTouched) this.rollCodename(); this.step = 'name'; window.scrollTo({ top: 0 }); this.$nextTick(() => document.getElementById('codename-intro')?.focus()); },
        finish() { if (this.codenameState !== 'ok') return; this.step = 'done'; window.scrollTo({ top: 0 }); },
        moveTab(d) {
          const i = (this.tabs.findIndex(t => t.id === this.tab) + d + this.tabs.length) % this.tabs.length;
          this.tab = this.tabs[i].id;
          this.$nextTick(() => document.getElementById(`tab-${this.tab}`)?.focus());
        },

        // ─── choosing ───
        set(key, value) { if (this.look[key] === value) return; this.look[key] = value; this.pop(); },
        // Switching animal keeps the same position in the coat list (2nd coat stays 2nd coat).
        withAnimal(animal) {
          const from = Object.keys(this.cfg.animals[this.look.animal].coats), to = Object.keys(this.cfg.animals[animal].coats);
          return { ...this.look, animal, coat: to[Math.max(0, Math.min(from.indexOf(this.look.coat), to.length - 1))] };
        },
        setAnimal(animal) { if (this.look.animal === animal) return; this.look = this.withAnimal(animal); this.pop(); },
        shuffle() {
          const any = (o) => { const k = Object.keys(o); return k[Math.floor(Math.random() * k.length)]; };
          const animal = any(this.cfg.animals);
          this.look = { animal, coat: any(this.cfg.animals[animal].coats), suit: any(this.cfg.suits), suit_color: any(this.cfg.suitColors),
            patch: any(this.cfg.patches), helmet: Math.random() < .5, visor: any(this.cfg.visors) };
          this.pop(true);
        },

        // ─── codename (same rules as App\Services\AvatarService::codenameProblem; the server has the last word) ───
        rollCodename() {
          const any = (a) => a[Math.floor(Math.random() * a.length)];
          const c = this.cfg.codenames;
          this.codename = any(c.adjectives) + any(c.nouns[this.look.animal]) + (10 + Math.floor(Math.random() * 90));
          this.codenameTouched = true;
          this.checkCodename(true);
        },
        checkCodename(now) {
          clearTimeout(this.checkTimer);
          const name = this.codename.trim();
          if (!/^[A-Za-z0-9_]{3,20}$/.test(name)) {
            this.codenameState = 'bad';
            this.codenameNote = name.length < 3 ? 'At least 3 characters.' : 'Use 3 to 20 letters, numbers or _ (no spaces).';
            return;
          }
          this.codenameState = 'checking'; this.codenameNote = 'Checking…';
          const seq = ++this.checkSeq;
          this.checkTimer = setTimeout(async () => {
            try {
              const res = await fetch(`${checkUrl}?codename=${encodeURIComponent(name)}`, { headers: { Accept: 'application/json' } });
              const data = await res.json();
              if (seq !== this.checkSeq) return; // they typed again since
              this.codenameState = data.available ? 'ok' : 'bad';
              this.codenameNote = data.available ? `${name} is all yours!` : data.message;
            } catch (e) {
              if (seq !== this.checkSeq) return;
              // Can't reach the server: let them try; saving checks again.
              this.codenameState = 'ok'; this.codenameNote = '';
            }
          }, now ? 0 : 350);
        },
        // A quick squash-and-settle on the preview so every change is felt. Starts from wherever the last one is.
        pop(big) {
          const el = this.$refs.hero;
          if (!el || reduce || !el.animate) return;
          el.animate(big
            ? [{ transform: 'scale(1) rotate(0)' }, { transform: 'scale(.86) rotate(-6deg)' }, { transform: 'scale(1.06) rotate(3deg)' }, { transform: 'scale(1) rotate(0)' }]
            : [{ transform: 'scale(1)' }, { transform: 'scale(1.06, .94)' }, { transform: 'scale(.98, 1.03)' }, { transform: 'scale(1)' }],
            { duration: big ? 420 : 260, easing: 'cubic-bezier(.3,1.3,.5,1)', composite: 'replace' });
        },
        describe() {
          const l = this.look;
          const a = this.cfg.animals[l.animal];
          return `Your astronaut: a ${a.coats[l.coat].label.toLowerCase()} ${a.label.toLowerCase()} in a ${l.suit_color} ${this.cfg.suits[l.suit].label.toLowerCase()} suit, ${l.helmet ? 'helmet on' : 'helmet off'}`;
        },

        // ─── drawing (same rules as App\Services\AvatarService::pixels) ───
        svg(l) {
          const c = this.cfg;
          const animal = c.animals[l.animal], coat = animal.coats[l.coat];
          const pal = { ...c.ink, f: coat.f, F: coat.F, k: coat.k, K: coat.K, ...c.suitColors[l.suit_color], p: c.patches[l.patch].p };
          const helmet = l.helmet ? c.helmet : {};
          const inGlass = (x, y) => helmet[y] !== undefined && helmet[y][x] !== '.';
          const grid = {};
          const layers = [[animal.rows, !!l.helmet], [c.suits[l.suit].rows, false]];
          for (const [rows, clip] of layers) {
            for (const y in rows) {
              [...rows[y]].forEach((ch, x) => { if (ch !== '.' && (!clip || inGlass(x, +y))) (grid[y] ??= {})[x] = pal[ch]; });
            }
          }
          const solid = [];
          Object.keys(grid).map(Number).sort((a, b) => a - b).forEach(y => Object.keys(grid[y]).map(Number).sort((a, b) => a - b).forEach(x => solid.push([x, y, grid[y][x], 1])));
          const glass = [];
          for (const y in helmet) {
            [...helmet[y]].forEach((ch, x) => { if (ch !== '.') glass.push([x, +y, (ch === 'v' || ch === 'V') ? c.visors[l.visor] : pal[ch], c.glass[ch] ?? 1]); });
          }
          let out = '';
          for (const pass of [solid, glass]) {
            let run = null;
            for (const px of [...pass, null]) {
              if (run && px && px[1] === run[1] && px[0] === run[0] + run[4] && px[2] === run[2] && px[3] === run[3]) { run[4]++; continue; }
              if (run) out += `<rect x="${run[0]}" y="${run[1]}" width="${run[4]}" height="1" fill="${run[2]}"${run[3] < 1 ? ` fill-opacity="${run[3]}"` : ''}/>`;
              run = px ? [...px, 1] : null;
            }
          }
          return out;
        },
      };
    }
  </script>
</body>
</html>
