<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TechLab — Coding, networking, cybersecurity and AI with Astro</title>
    <meta name="description" content="TechLab teaches Python and networking through short lessons, in-browser labs, and Astro, an AI tutor that answers when you get stuck.">
    <link rel="icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Tokens lifted from the in-app course sidebar so the front door and the product match. */
        :root {
            --blue: #2f7de1;
            /* Deep-space look: the light tokens flip, the brand blue and the logo stay as they were. */
            --ink: #eaeeff;
            --body: #b6bfe8;
            --muted: #98a2d4;
            --blue-tint: rgba(47,125,225,.22);
            --blue-wash: rgba(47,125,225,.10);
            --surface: rgba(12,10,42,.68);
            --page: rgba(255,255,255,.05);
            --track: rgba(255,255,255,.10);
            --line: rgba(150,170,255,.18);
            --display: 'Space Grotesk', system-ui, sans-serif;
            --text: 'Inter', system-ui, sans-serif;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--text);
            background: #06061a;
            color: var(--body);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        a { color: inherit; text-decoration: none; }
        :focus-visible { outline: 3px solid var(--blue); outline-offset: 2px; border-radius: 8px; }

        /* Top bar: same white strip + hairline as the sidebar tabs */
        .bar {
            /* No bar: the logo and buttons sit straight on the space */
            background: transparent;
        }
        .bar, main, footer { position: relative; z-index: 1; }
        .bar-in {
            max-width: 1080px; margin: 0 auto; padding: 0 24px;
            height: 96px; display: flex; align-items: center; justify-content: space-between;
        }
        .brand { display: flex; align-items: center; gap: 12px; font-family: var(--display); font-weight: 700; font-size: 22px; letter-spacing: -0.02em; color: var(--ink); }
        .brand img { width: 34px; height: 34px; border-radius: 8px; }
        .bar-actions { display: flex; align-items: center; gap: 8px; }

        .btn {
            font-family: var(--display); font-weight: 600; font-size: 16px;
            height: 48px; padding: 0 22px; border-radius: 12px; border: 2px solid transparent;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            cursor: pointer; transition: background .2s ease, color .2s ease, transform .1s ease, border-color .2s ease;
        }
        .btn:active { transform: scale(.97); }
        .btn-solid { background: var(--blue); color: #fff; }
        .btn-solid:hover { background: #2569c2; }
        .btn-quiet { color: var(--ink); }
        .btn-quiet:hover { background: var(--track); }
        .btn-line { background: var(--surface); border-color: var(--line); color: var(--ink); }
        .btn-line:hover { border-color: var(--blue); color: var(--blue); }

        /* Layout mirrors the faculty page: 1080px column, centered hero, then card-grid sections */
        main { flex: 1; }
        .wrap { max-width: 1080px; margin: 0 auto; padding: 0 24px; }
        /* The hero fills the first screen (viewport minus the 96px top bar), so the coins start below the fold */
        .hero { text-align: center; padding: 48px 0 72px; min-height: calc(100vh - 96px); min-height: calc(100svh - 96px); display: flex; flex-direction: column; align-items: center; justify-content: center; }

        .hero { position: relative; }
        /* Everything under the hero rises into view as you scroll to it */
        .js .reveal { opacity: 0; transform: translateY(28px); transition: opacity .8s cubic-bezier(.16,1,.3,1), transform .8s cubic-bezier(.16,1,.3,1); }
        .js .reveal.in { opacity: 1; transform: none; }
        .scroll-cue { position: absolute; left: 50%; bottom: 20px; translate: -50% 0; color: var(--muted); display: grid; place-items: center; width: 44px; height: 44px; border-radius: 50%; opacity: 0; animation: cueIn .8s ease 1.6s forwards, cueBob 2.2s ease-in-out 1.6s infinite; transition: color .2s ease, background .2s ease; }
        .scroll-cue:hover { color: var(--ink); background: var(--track); }
        .scroll-cue.gone { animation: none; opacity: 0; pointer-events: none; transition: opacity .3s ease; }
        @keyframes cueIn { to { opacity: 1; } }
        @keyframes cueBob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(7px); } }
        .rise { opacity: 0; transform: translateY(12px); animation: land .8s cubic-bezier(.2,.9,.25,1) forwards; animation-delay: var(--d, 0ms); }
        @keyframes land { to { opacity: 1; transform: none; filter: blur(0); } }

        h2 { font-family: var(--display); font-weight: 700; color: var(--ink); font-size: 1.6rem; letter-spacing: -0.02em; text-align: center; }
        .section { padding: 40px 0; }
        .grid { margin-top: 28px; display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 16px; }
        .card { background: var(--surface); border: 1px solid var(--line); border-radius: 16px; padding: 22px; }

        /* Three 3D reward tokens, no container: they float in the page */
        .trio { perspective: 700px; }
        .token { display: flex; flex-direction: column; align-items: center; gap: 14px; cursor: default; padding: 28px 22px 24px; }
        .coin { position: relative; width: 104px; height: 104px; transform-style: preserve-3d; transform: rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg)); transition: transform .5s cubic-bezier(.2,.9,.25,1); animation: bob 4.5s ease-in-out infinite; animation-delay: var(--b, 0s); will-change: transform; }
        .coin.live { transition: transform .08s linear; }
        .coin svg { width: 100%; height: 100%; overflow: visible; filter: drop-shadow(0 10px 8px rgba(13,13,13,.12)); }
        .token:active .coin { transform: rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg)) scale(.94); }
        @keyframes bob { 0%, 100% { translate: 0 0; } 50% { translate: 0 -8px; } }
        .floor { width: 64px; height: 10px; border-radius: 50%; background: radial-gradient(closest-side, rgba(13,13,13,.16), transparent); animation: floor 4.5s ease-in-out infinite; animation-delay: var(--b, 0s); }
        @keyframes floor { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(.78); opacity: .6; } }
        .cap { font-family: var(--display); font-weight: 600; font-size: 18px; color: var(--ink); letter-spacing: -0.01em; text-align: center; line-height: 1.2; }
        .cap small { display: block; margin-top: 2px; font-family: var(--text); font-weight: 400; font-size: 13px; color: var(--muted); }

        /* Icon life: each one moves the way the thing it depicts moves */
        .bolt { transform-origin: 50% 60%; animation: zap 3.2s ease-in-out infinite; }
        @keyframes zap { 0%, 78%, 100% { transform: scale(1) rotate(0); } 84% { transform: scale(1.08) rotate(-4deg); } 92% { transform: scale(.98) rotate(2deg); } }
        .flame { transform-origin: 50% 95%; animation: flicker 1.5s ease-in-out infinite; }
        .flame-in { transform-origin: 50% 95%; animation: flicker 1.1s ease-in-out infinite reverse; }
        @keyframes flicker { 0%, 100% { transform: scale(1, 1) skewX(0); } 30% { transform: scale(.96, 1.06) skewX(-3deg); } 60% { transform: scale(1.03, .98) skewX(3deg); } }
        .gem-shine { animation: glint 3.6s ease-in-out 1s infinite; opacity: 0; }
        @keyframes glint { 0%, 70%, 100% { opacity: 0; transform: translateX(-12px); } 80% { opacity: .95; } 92% { opacity: 0; transform: translateX(14px); } }
        .spark { transform-box: fill-box; transform-origin: center; animation: twinkle 3.6s ease-in-out infinite; }
        .spark.b { animation-delay: 1.4s; }
        @keyframes twinkle { 0%, 60%, 100% { transform: scale(0); opacity: 0; } 75% { transform: scale(1); opacity: 1; } 90% { transform: scale(0); opacity: 0; } }

        @media (max-width: 640px) { .coin { width: 88px; height: 88px; } }

        h1 {
            font-family: var(--display); color: var(--ink); font-weight: 700;
            font-size: clamp(2.5rem, 7vw, 4.75rem); line-height: 1.04; letter-spacing: -0.04em;
        }
        h1 .line { display: block; }
        h1 .line-1 { opacity: 0; transform: translateY(.3em); filter: blur(12px); animation: land .9s cubic-bezier(.2,.9,.25,1) 100ms forwards; }
        /* The word that decodes itself, in a game-style highlighter block */
        .decode {
            display: inline-block; min-width: 9.5ch; margin-top: .08em; padding: 0 .22em .06em;
            background: var(--blue); color: #fff; border-radius: .18em;
            box-shadow: 0 .07em 0 #1f5fb4; font-variant-ligatures: none; white-space: nowrap;
            opacity: 0; transform: translateY(.3em); filter: blur(12px); animation: land .9s cubic-bezier(.2,.9,.25,1) 260ms forwards;
        }
        .lede { margin: 22px auto 0; font-size: 1.1875rem; line-height: 1.6; max-width: 52ch; }

        /* Skill bars: one segment per real module */
        .skills { text-align: left; }
        .skill { background: var(--surface); border: 1px solid var(--line); border-radius: 16px; padding: 22px; display: grid; grid-template-columns: 1fr auto; gap: 10px 16px; align-items: center; }
        .skill.locked { background: var(--page); }
        .s-name { font-family: var(--display); font-weight: 700; font-size: 19px; letter-spacing: -0.02em; color: var(--ink); display: flex; align-items: center; gap: 8px; }
        .s-meta { font-size: 14px; color: var(--body); font-variant-numeric: tabular-nums; }
        .segs { grid-column: 1 / -1; display: grid; grid-auto-flow: column; grid-auto-columns: 1fr; gap: 6px; }
        .seg { height: 10px; border-radius: 20px; background: var(--track); overflow: hidden; position: relative; }
        .skill.locked .seg { background: rgba(255,255,255,.14); }
        .seg:first-child:not(.off)::after { content: ""; position: absolute; inset: 0; background: var(--blue); border-radius: inherit; transform-origin: left; transform: scaleX(0); animation: fillup 1.1s cubic-bezier(.2,.9,.25,1) calc(var(--d) + 500ms) forwards; }
        .seg:first-child:not(.off)::before { content: ""; position: absolute; inset: 0; z-index: 1; background: linear-gradient(90deg, transparent, rgba(255,255,255,.6), transparent); transform: translateX(-100%); animation: shine 2.6s ease-in-out calc(var(--d) + 1600ms) infinite; }
        @keyframes fillup { to { transform: scaleX(.55); } }
        @keyframes shine { 0%, 60% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }

        /* Chunky button: presses down into its own edge */
        .cta-row { margin-top: clamp(40px, 6vh, 64px); display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; }
        .cta-row .btn { height: 58px; font-size: 18px; padding: 0 30px; }
        .btn-solid { box-shadow: 0 4px 0 #1f5fb4; }
        .btn-solid:active { transform: translateY(3px); box-shadow: 0 1px 0 #1f5fb4; }
        .fineprint { margin-top: 16px; font-size: 15px; color: var(--muted); }

        .astro-note {
            margin: 0 auto; display: flex; align-items: center; gap: 16px; text-align: left;
            background: var(--surface); border: 1px solid var(--line); border-radius: 16px;
            padding: 20px 24px; max-width: 720px;
        }
        .astro-note svg { width: 48px; height: 45px; flex: none; }
        .astro-note p { font-size: 15px; line-height: 1.5; }
        .astro-note b { font-family: var(--display); color: var(--ink); font-weight: 600; }

        /* Footer */
        footer { background: transparent; }
        .foot-in { max-width: 1080px; margin: 0 auto; padding: 24px; display: flex; flex-wrap: wrap; gap: 12px 24px; align-items: center; justify-content: space-between; font-size: 14px; }
        .foot-in .brand { font-size: 18px; }
        .foot-in .brand img { width: 26px; height: 26px; border-radius: 6px; }

        @media (max-width: 640px) {
            .hero { padding: 48px 0 40px; }
            .bar-in, .foot-in, .wrap { padding-left: 20px; padding-right: 20px; }
            .btn-quiet { display: none; }
            .cta-row .btn { flex: 1; }
        }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; scroll-behavior: auto !important; } h1 .line-1, .decode, .rise, .seg::after, .seg::before, .coin, .floor, .bolt, .flame, .flame-in, .gem-shine, .spark { animation: none !important; opacity: 1; transform: none; filter: none; } .seg:first-child:not(.off)::after { transform: scaleX(.55); } .seg::before { display: none; } .spark { transform: none; opacity: 1; } }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body>
    <x-cosmic-background />
    <header class="bar">
        <div class="bar-in">
            <x-brand :href="url('/')" />
        </div>
    </header>

    <main>
        <div class="wrap">
            <section class="hero">
                <h1 aria-label="Level up your coding, networking and cybersecurity">
                <span class="line line-1" aria-hidden="true">Level up your</span>
                <span class="decode" id="decode" aria-hidden="true">Coding</span>
            </h1>
                <div class="cta-row rise" style="--d:500ms">
                    <a href="{{ route('register') }}" class="btn btn-solid">Start your mission</a>
                    <a href="{{ route('login') }}" class="btn btn-line">Log in</a>
                </div>
                <a href="#more" class="scroll-cue" aria-label="Scroll down to see more">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                </a>
            </section>

            <section class="section reveal" id="more">
                <h2>Gamify your learning</h2>
                <div class="grid trio">
                <div class="token card">
                    <div class="coin" style="--b:0s">
                        <svg viewBox="0 0 32 40" aria-hidden="true"><defs>
                            <path id="sb" d="M18.5 2 6 18h8l-2 12 13-17h-8.2z"/>
                            <linearGradient id="gx" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffe98f"/><stop offset="1" stop-color="#f5a623"/></linearGradient></defs>
                            <use href="#sb" y="7" fill="#b9700a"/><use href="#sb" y="6" fill="#b9700a"/><use href="#sb" y="5" fill="#b9700a"/><use href="#sb" y="4" fill="#b9700a"/><use href="#sb" y="3" fill="#b9700a"/><use href="#sb" y="2" fill="#b9700a"/><use href="#sb" y="1" fill="#b9700a"/>
                            <g class="bolt"><use href="#sb" fill="url(#gx)" stroke="#d98a0e" stroke-width="1.2" stroke-linejoin="round"/><path d="M17 5 9 17h5" fill="none" stroke="#fff8d6" stroke-width="1.6" stroke-linecap="round" opacity=".8"/></g></svg>
                    </div>
                    <div class="floor"></div>
                    <div class="cap">XP<small>Earn per lesson</small></div>
                </div>
                <div class="token card">
                    <div class="coin" style="--b:.5s">
                        <svg viewBox="0 0 32 40" aria-hidden="true"><defs>
                            <path id="sf" d="M16 2c1.2 5.2 8.5 8.6 8.5 16.2A8.5 8.5 0 0 1 16 27a8.5 8.5 0 0 1-8.5-8.8c0-3.6 2-5.7 3.6-7.6.4 2 1.3 3.2 2.6 3.7C13 10.2 14.4 6 16 2z"/>
                            <linearGradient id="gf" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffb02e"/><stop offset="1" stop-color="#f0491a"/></linearGradient></defs>
                            <use href="#sf" y="7" fill="#a82a0a"/><use href="#sf" y="6" fill="#a82a0a"/><use href="#sf" y="5" fill="#a82a0a"/><use href="#sf" y="4" fill="#a82a0a"/><use href="#sf" y="3" fill="#a82a0a"/><use href="#sf" y="2" fill="#a82a0a"/><use href="#sf" y="1" fill="#a82a0a"/>
                            <g class="flame"><use href="#sf" fill="url(#gf)" stroke="#d63a10" stroke-width="1.2" stroke-linejoin="round"/></g>
                            <path class="flame-in" d="M16 15c.7 3 4 4 4 7.3a4 4 0 0 1-8 0c0-1.7.9-2.8 1.8-3.7.4 1 .9 1.5 1.6 1.6C15.2 18.6 15.4 17 16 15z" fill="#ffe27a"/></svg>
                    </div>
                    <div class="floor"></div>
                    <div class="cap">Streak<small>Come back daily</small></div>
                </div>
                <div class="token card">
                    <div class="coin" style="--b:1s">
                        <svg viewBox="0 0 32 40" aria-hidden="true"><defs>
                            <path id="sg" d="M9 4h14l6 8-13 17L3 12z"/>
                            <clipPath id="gc"><path d="M9 4h14l6 8-13 17L3 12z"/></clipPath>
                            <linearGradient id="gg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#8fe3ff"/><stop offset="1" stop-color="#1f8fe0"/></linearGradient></defs>
                            <use href="#sg" y="7" fill="#125a96"/><use href="#sg" y="6" fill="#125a96"/><use href="#sg" y="5" fill="#125a96"/><use href="#sg" y="4" fill="#125a96"/><use href="#sg" y="3" fill="#125a96"/><use href="#sg" y="2" fill="#125a96"/><use href="#sg" y="1" fill="#125a96"/>
                            <use href="#sg" fill="url(#gg)" stroke="#1a78c4" stroke-width="1.2" stroke-linejoin="round"/>
                            <path d="M9 4l4 8h6l4-8M3 12h26M13 12l3 17 3-17" fill="none" stroke="#e6f7ff" stroke-opacity=".75" stroke-width="1.1" stroke-linejoin="round"/>
                            <path d="M9 4h14l-4 8h-6z" fill="#fff" fill-opacity=".35"/>
                            <g clip-path="url(#gc)"><rect class="gem-shine" x="8" y="-2" width="7" height="40" fill="#fff" transform="rotate(20 12 16)"/></g>
                            <path class="spark" d="M27 2l1 3 3 1-3 1-1 3-1-3-3-1 3-1z" fill="#fff" stroke="#7fdcff" stroke-width=".6"/>
                            <path class="spark b" d="M4 20l.8 2.2L7 23l-2.2.8L4 26l-.8-2.2L1 23l2.2-.8z" fill="#fff" stroke="#7fdcff" stroke-width=".6"/></svg>
                    </div>
                    <div class="floor"></div>
                    <div class="cap">Gems<small>Unlock rewards</small></div>
                </div>
                </div>
            </section>

            <section class="section reveal">
                <h2>Pick your skill</h2>
                <div class="grid skills">
                    @foreach ($skills as $i => $skill)
                        <div class="skill {{ $skill['locked'] ? 'locked' : '' }}" style="--d:{{ 1000 + $i * 120 }}ms">
                            <span class="s-name">
                                {{ $skill['name'] }}
                                @if ($skill['locked'])<x-px-icon name="lock" :size="16" style="color:#888" />@endif
                            </span>
                            <span class="s-meta">{{ $skill['locked'] ? 'Unlocks soon' : $skill['lessons'].' lessons' }}</span>
                            <div class="segs" role="img" aria-label="{{ $skill['modules'] }} modules">
                                @for ($m = 0; $m < $skill['modules']; $m++)
                                    <span class="seg {{ $skill['locked'] ? 'off' : '' }}"></span>
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="section reveal">
                <h2>Meet your tutor</h2>
                <div class="astro-note" style="margin-top:28px">
                    <x-astro-mascot />
                    <p><b>Astro</b> is your tutor. Ask a question mid-lesson and get an answer that fits what you're reading.</p>
                </div>
            </section>
        </div>
    </main>

    <footer>
        <div class="foot-in">
            <a href="{{ url('/') }}" class="brand"><img src="/apple-touch-icon.png" alt="" width="26" height="26">TechLab</a>
            <span><a href="{{ url('/faculty') }}">Faculty? Sign in here</a> &nbsp;·&nbsp; &copy; {{ date('Y') }} TechLab</span>
        </div>
    </footer>
    <script>
        // Scroll reveal for the sections under the hero, and the little arrow hides once you have scrolled.
        (function () {
            var els = document.querySelectorAll('.reveal'), cue = document.querySelector('.scroll-cue');
            document.documentElement.classList.add('js');
            if (cue) addEventListener('scroll', function () { cue.classList.toggle('gone', scrollY > 60); }, { passive: true });
            if (!('IntersectionObserver' in window) || matchMedia('(prefers-reduced-motion: reduce)').matches) {
                els.forEach(function (e) { e.classList.add('in'); });
                return;
            }
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
            }, { threshold: .15 });
            els.forEach(function (e) { io.observe(e); });
        })();
    </script>
    <script>
        // Decode effect: the highlighted word scrambles, then locks in letter by letter.
        (function () {
            // 3D tilt: tokens lean toward the pointer, then spring back level on leave.
            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                document.querySelectorAll('.token').forEach(function (t) {
                    var c = t.querySelector('.coin');
                    t.addEventListener('pointermove', function (e) {
                        var r = t.getBoundingClientRect();
                        var x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
                        c.classList.add('live');
                        c.style.setProperty('--ry', (x * 34) + 'deg');
                        c.style.setProperty('--rx', (-y * 34) + 'deg');
                    });
                    t.addEventListener('pointerleave', function () {
                        c.classList.remove('live');
                        c.style.setProperty('--ry', '0deg'); c.style.setProperty('--rx', '0deg');
                    });
                });
            }
            var el = document.getElementById('decode');
            var words = ['Coding', 'Networking', 'Cybersecurity', 'AI Teacher'];
            if (!el || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            var glyphs = '01<>/{}#$%&*+=?', i = 0;
            function run(word, done) {
                var n = 0, steps = word.length * 3;
                var t = setInterval(function () {
                    var lock = Math.floor(n / 3), out = '';
                    for (var k = 0; k < word.length; k++) {
                        out += k < lock ? word[k] : glyphs[Math.floor(Math.random() * glyphs.length)];
                    }
                    el.textContent = out;
                    if (++n > steps) { clearInterval(t); el.textContent = word; done(); }
                }, 38);
            }
            function next() {
                setTimeout(function () { i = (i + 1) % words.length; run(words[i], next); }, 2200);
            }
            setTimeout(next, 1400);
        })();
    </script>
</body>
</html>
