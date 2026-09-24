<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TechLab for Faculty — teach with Astro at your side</title>
    <meta name="description" content="Run a crew, share modules, and give auto-graded quizzes with Astro, TechLab's AI teaching assistant.">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #2f7de1; --blue-dark: #1f5fb4;
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
            --display: 'Space Grotesk', system-ui, sans-serif; --text: 'Inter', system-ui, sans-serif;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: var(--text); background: #06061a; color: var(--body); line-height: 1.6; -webkit-font-smoothing: antialiased; min-height: 100vh; display: flex; flex-direction: column; }
        a { color: inherit; text-decoration: none; }
        :focus-visible { outline: 3px solid var(--blue); outline-offset: 2px; border-radius: 8px; }

        .bar { background: transparent; } /* no bar: logo and button sit straight on the space */
        .bar, main, footer { position: relative; z-index: 1; }
        .bar-in { max-width: 1080px; margin: 0 auto; padding: 0 24px; height: 96px; display: flex; align-items: center; justify-content: space-between; }
        .brand { display: flex; align-items: center; gap: 12px; font-family: var(--display); font-weight: 700; font-size: 22px; letter-spacing: -0.02em; color: var(--ink); }
        .brand img { width: 34px; height: 34px; border-radius: 8px; }
        .brand small { font-family: var(--text); font-weight: 600; font-size: 12px; color: var(--blue); background: var(--blue-tint); padding: 2px 8px; border-radius: 999px; letter-spacing: 0; }

        .btn { font-family: var(--display); font-weight: 600; font-size: 16px; height: 48px; padding: 0 22px; border-radius: 12px; border: 2px solid transparent; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: background .2s ease, border-color .2s ease, color .2s ease; }
        .btn-solid { background: var(--blue); color: #fff; box-shadow: 0 4px 0 var(--blue-dark); }
        .btn-solid:active { transform: translateY(3px); box-shadow: 0 1px 0 var(--blue-dark); }
        .btn-solid:hover { background: #2569c2; }
        .btn-line { background: var(--surface); border-color: var(--line); color: var(--ink); }
        .btn-line:hover { border-color: var(--blue); color: var(--blue); }

        main { flex: 1; }
        .wrap { max-width: 1080px; margin: 0 auto; padding: 0 24px; }
        /* Same as the student page: the hero fills the first screen (viewport minus the 96px top bar), the sections start below the fold */
        .hero { position: relative; text-align: center; padding: 48px 0 72px; min-height: calc(100vh - 96px); min-height: calc(100svh - 96px); display: flex; flex-direction: column; align-items: center; justify-content: center; }
        /* Everything under the hero rises into view as you scroll to it; the arrow points the way */
        .js .reveal { opacity: 0; transform: translateY(28px); transition: opacity .8s cubic-bezier(.16,1,.3,1), transform .8s cubic-bezier(.16,1,.3,1); }
        .js .reveal.in { opacity: 1; transform: none; }
        .scroll-cue { position: absolute; left: 50%; bottom: 20px; translate: -50% 0; color: var(--muted); display: grid; place-items: center; width: 44px; height: 44px; border-radius: 50%; opacity: 0; animation: cueIn .8s ease 1.6s forwards, cueBob 2.2s ease-in-out 1.6s infinite; transition: color .2s ease, background .2s ease; }
        .scroll-cue:hover { color: var(--ink); background: var(--track); }
        .scroll-cue.gone { animation: none; opacity: 0; pointer-events: none; transition: opacity .3s ease; }
        @keyframes cueIn { to { opacity: 1; } }
        @keyframes cueBob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(7px); } }
        .rise { opacity: 0; transform: translateY(12px); animation: land .8s cubic-bezier(.2,.9,.25,1) forwards; animation-delay: var(--d, 0ms); }
        h1 { font-family: var(--display); font-weight: 700; color: var(--ink); font-size: clamp(2.5rem, 7vw, 4.75rem); line-height: 1.04; letter-spacing: -0.04em; }
        h1 .line { display: block; }
        h1 .line-1 { opacity: 0; transform: translateY(.3em); filter: blur(12px); animation: land .9s cubic-bezier(.2,.9,.25,1) 100ms forwards; }
        /* The phrase that decodes itself, in a highlighter block. Width follows the word (set in JS). */
        .decode {
            display: inline-block; margin-top: .08em; padding: 0 .22em .06em; text-align: center;
            background: var(--blue); color: #fff; border-radius: .18em; box-shadow: 0 .07em 0 var(--blue-dark);
            font-variant-ligatures: none; white-space: nowrap; min-width: 6ch;
            opacity: 0; transform: translateY(.3em); filter: blur(12px); animation: land .9s cubic-bezier(.2,.9,.25,1) 260ms forwards;
            transition: width .5s cubic-bezier(.2,.9,.25,1);
        }
        .decode-m { position: absolute; visibility: hidden; pointer-events: none; padding: 0 .22em; white-space: nowrap; font-variant-ligatures: none; }
        @keyframes land { to { opacity: 1; transform: none; filter: blur(0); } }
        @media (prefers-reduced-motion: reduce) { .rise { animation: none !important; opacity: 1; transform: none; } .scroll-cue { animation: none !important; opacity: 1; } .js .reveal { transition: none; } .line-1, .decode { animation: none !important; opacity: 1; transform: none; filter: none; } .decode { transition: none; } }
        .lede { margin: 22px auto 0; max-width: 52ch; font-size: 1.1875rem; }
        .cta-row { margin-top: clamp(40px, 6vh, 64px); display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; }
        .cta-row .btn { height: 58px; font-size: 18px; padding: 0 30px; }

        h2 { font-family: var(--display); font-weight: 700; color: var(--ink); font-size: 1.6rem; letter-spacing: -0.02em; text-align: center; }
        .section { padding: 40px 0; }
        .grid { margin-top: 28px; display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 16px; }
        .card { background: var(--surface); border: 1px solid var(--line); border-radius: 16px; padding: 22px; }
        .card h3 { font-family: var(--display); color: var(--ink); font-size: 1.1rem; margin-bottom: 6px; }
        .card p { font-size: 15px; }
        .icon { width: 40px; height: 40px; border-radius: 10px; background: var(--blue-tint); color: var(--blue); display: grid; place-items: center; margin-bottom: 12px; }
        .icon svg { width: 22px; height: 22px; }

        .steps { margin: 28px auto 0; max-width: 720px; display: grid; gap: 12px; counter-reset: s; }
        .step { display: flex; gap: 16px; align-items: flex-start; background: var(--surface); border: 1px solid var(--line); border-radius: 16px; padding: 18px 20px; }
        .step::before { counter-increment: s; content: counter(s); flex: none; width: 32px; height: 32px; border-radius: 50%; background: var(--blue); color: #fff; font-family: var(--display); font-weight: 700; display: grid; place-items: center; }
        .step b { color: var(--ink); display: block; font-family: var(--display); }
        .step span { font-size: 15px; }

        .students { text-align: center; padding: 8px 0 56px; font-size: 15px; }
        .students a { color: var(--blue); font-weight: 600; }
        .students a:hover, footer a:hover { text-decoration: underline; }

        footer { background: transparent; }
        .foot-in { max-width: 1080px; margin: 0 auto; padding: 24px; display: flex; flex-wrap: wrap; gap: 12px 24px; align-items: center; justify-content: space-between; font-size: 14px; }

        @media (max-width: 640px) { .bar-in, .wrap, .foot-in { padding-left: 20px; padding-right: 20px; } .hero { padding: 48px 0 40px; } .cta-row .btn { flex: 1; } }
    </style>
</head>
<body>
    <x-cosmic-background />
    <header class="bar">
        <div class="bar-in">
            <x-brand :href="url('/faculty')" label="TechLab for faculty" badge="Faculty" />
        </div>
    </header>

    <main>
        <div class="wrap">
            <section class="hero">
                <h1 aria-label="Level up your lessons, automation, AI partner and courses">
                    <span class="line line-1" aria-hidden="true">Level up your</span>
                    <span class="decode" id="decode" aria-hidden="true">Lessons</span>
                </h1>
                <div class="cta-row rise" style="--d:500ms">
                    <a href="{{ route('faculty.register') }}" class="btn btn-solid">Create faculty account</a>
                    <a href="{{ route('faculty.login') }}" class="btn btn-line">Faculty sign in</a>
                </div>
                <a href="#what" class="scroll-cue" aria-label="Scroll down to see more">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                </a>
            </section>

            <section class="section reveal" id="what">
                <h2>Everything for your class, in one place</h2>
                <div class="grid">
                    <div class="card">
                        <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.6-3.6 3.2-5.5 6.5-5.5s5.9 1.9 6.5 5.5"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18 14.8c2 .6 3.2 2.3 3.5 5.2"/></svg></div>
                        <h3>Your crew</h3>
                        <p>Create a crew and share its join code. Students board with the code, and you see them on your roster.</p>
                    </div>
                    <div class="card">
                        <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5z"/><path d="M4 5.5v16M9 8h7"/></svg></div>
                        <h3>Modules and materials</h3>
                        <p>Build learning modules and attach files. Your crew finds them on their own page.</p>
                    </div>
                    <div class="card">
                        <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="m8.5 9 1.5 1.5L13 7.5M8.5 16h7"/></svg></div>
                        <h3>Quizzes that grade themselves</h3>
                        <p>Write a quiz for your crew. Answers are checked on the server, so students never see the key.</p>
                    </div>
                    <div class="card">
                        <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="5" width="17" height="15.5" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/></svg></div>
                        <h3>Class schedule</h3>
                        <p>Upload your timetable as a file or photo. Astro reads it and fills in your weekly calendar.</p>
                    </div>
                    <div class="card">
                        <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20.5l1.4-4.7A8 8 0 1 1 21 12z"/></svg></div>
                        <h3>Astro, your teaching assistant</h3>
                        <p>Ask for lesson plans, quiz questions with answer keys, and grading rubrics.</p>
                    </div>
                </div>
            </section>

            <section class="section reveal">
                <h2>How to get started</h2>
                <div class="steps">
                    <div class="step"><div><b>Create your faculty account</b><span>Sign up with your school email address.</span></div></div>
                    <div class="step"><div><b>Sign in</b><span>Next time, use Faculty sign in. It opens your own dashboard.</span></div></div>
                    <div class="step"><div><b>Create your crew</b><span>Share the join code with your students and start posting modules.</span></div></div>
                </div>
            </section>

            <p class="students reveal">Are you a student? <a href="{{ url('/') }}">Go to the student page</a></p>
        </div>
    </main>

    <footer>
        <div class="foot-in">
            <a href="{{ url('/faculty') }}" class="brand"><img src="/apple-touch-icon.png" alt="" width="26" height="26">TechLab</a>
            <span>&copy; {{ date('Y') }} TechLab</span>
        </div>
    </footer>
    <script>
        // Scroll reveal for everything under the hero, and the little arrow hides once you have scrolled.
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
        // Decode effect: the highlighted phrase scrambles, then locks in letter by letter.
        (function () {
            var el = document.getElementById('decode');
            var words = ['Lessons', 'Automation', 'AI Partner', 'Courses'];
            if (!el || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            var h1 = el.parentNode, glyphs = '01<>/{}#$%&*+=?', i = 0, widths = {};
            function fit(word) { if (widths[word]) el.style.width = widths[word] + 'px'; }
            function measure() {
                var m = document.createElement('span');
                m.className = 'decode-m'; m.setAttribute('aria-hidden', 'true');
                h1.appendChild(m);
                words.forEach(function (w) { m.textContent = w; widths[w] = Math.ceil(m.getBoundingClientRect().width); });
                h1.removeChild(m);
                fit(words[i]);
            }
            function run(word, done) {
                fit(word);
                var n = 0, steps = word.length * 3;
                var t = setInterval(function () {
                    var lock = Math.floor(n / 3), out = '';
                    for (var k = 0; k < word.length; k++) {
                        out += (k < lock || word[k] === ' ') ? word[k] : glyphs[Math.floor(Math.random() * glyphs.length)];
                    }
                    el.textContent = out;
                    if (++n > steps) { clearInterval(t); el.textContent = word; done(); }
                }, 38);
            }
            function next() {
                setTimeout(function () { i = (i + 1) % words.length; run(words[i], next); }, 2200);
            }
            (document.fonts && document.fonts.ready ? document.fonts.ready : Promise.resolve()).then(measure);
            window.addEventListener('resize', measure);
            setTimeout(next, 1800);
        })();
    </script>
</body>
</html>
