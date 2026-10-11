<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TechLab — Coding, networking, cybersecurity and AI with Astro</title>
    <meta name="description" content="TechLab teaches programming, networking and cybersecurity through short lessons, in-browser labs, and Astro, an AI tutor that answers when you get stuck.">
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
            --edge: clamp(20px, 4vw, 64px);   /* side gap for the full-width top bar, hero and footer */
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
        /* Full screen width: the logo sits near the left edge, mirroring the planet on the right */
        .bar-in {
            padding: 0 var(--edge);
            height: 120px; display: flex; align-items: center; justify-content: space-between;
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

        /* Sound toggle: a chunky game button with a pixel speaker. While it is off it gives a little nudge, like a "press start" prompt. */
        .sound-btn {
            font-family: var(--display); font-weight: 600; font-size: 15px; color: var(--ink);
            height: 44px; padding: 0 16px 0 13px; border-radius: 12px; border: 2px solid var(--line);
            background: var(--surface); box-shadow: 0 3px 0 rgba(150,170,255,.18);
            display: inline-flex; align-items: center; gap: 9px; cursor: pointer;
            transition: border-color .2s ease, background .2s ease, transform .1s ease, box-shadow .1s ease;
        }
        .sound-btn[hidden] { display: none; }
        .sound-btn:hover { border-color: var(--blue); }
        .sound-btn:active { transform: translateY(2px); box-shadow: 0 1px 0 rgba(150,170,255,.18); }
        .sound-btn[aria-pressed="true"] { border-color: var(--blue); background: var(--blue-tint); }
        .sound-btn[aria-pressed="true"] .s-off, .sound-btn[aria-pressed="false"] .s-on { display: none; }
        .sound-btn[aria-pressed="false"] { animation: nudge .7s ease 2.6s 2; }
        @keyframes nudge { 0%, 100% { rotate: 0deg; } 20% { rotate: -6deg; } 45% { rotate: 5deg; } 70% { rotate: -3deg; } }
        .sound-btn .w { animation: ping 1.4s ease-in-out infinite; animation-delay: var(--wd); }
        @keyframes ping { 0%, 100% { opacity: 1; } 50% { opacity: .25; } }

        /* Layout mirrors the faculty page: 1080px column, centered hero, then card-grid sections */
        main { flex: 1; }
        .wrap { max-width: 1080px; margin: 0 auto; padding: 0 24px; }
        /* The hero fills the first screen edge to edge (viewport minus the 120px top bar), so the coins start below the fold */
        .hero { text-align: center; padding: 48px var(--edge) 72px; min-height: calc(100vh - 120px); min-height: calc(100svh - 120px); display: flex; flex-direction: column; align-items: center; justify-content: center; }

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
        .coin svg { width: 100%; height: 100%; overflow: visible; shape-rendering: crispEdges; filter: drop-shadow(0 10px 8px rgba(0,0,0,.35)); }
        .token:active .coin { transform: rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg)) scale(.94); }
        @keyframes bob { 0%, 100% { translate: 0 0; } 50% { translate: 0 -8px; } }
        .floor { width: 64px; height: 10px; border-radius: 50%; background: radial-gradient(closest-side, rgba(13,13,13,.16), transparent); animation: floor 4.5s ease-in-out infinite; animation-delay: var(--b, 0s); }
        @keyframes floor { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(.78); opacity: .6; } }
        .cap { font-family: var(--display); font-weight: 600; font-size: 18px; color: var(--ink); letter-spacing: -0.01em; text-align: center; line-height: 1.2; }
        .cap small { display: block; margin-top: 2px; font-family: var(--text); font-weight: 400; font-size: 13px; color: var(--muted); }

        /* Icon life: each one moves the way the thing it depicts moves */
        /* Pixel sprites move in whole pixels (steps), like an old game, so the squares never blur. */
        .bolt { animation: zap 3.2s steps(1, end) infinite; }
        .bolt-flash { opacity: 0; animation: flash 3.2s steps(1, end) infinite; }
        @keyframes zap { 0%, 78%, 96%, 100% { transform: translateY(0); } 80%, 90% { transform: translateY(-1px); } }
        @keyframes flash { 0%, 80%, 86%, 100% { opacity: 0; } 82%, 84% { opacity: .85; } }
        .flame { animation: frame-a .9s steps(1, end) infinite; }
        .flame-in { animation: frame-b .9s steps(1, end) infinite; }
        @keyframes frame-a { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
        @keyframes frame-b { 0%, 100% { opacity: 0; } 50% { opacity: 1; } }
        .gem-shine { animation: glint 3.6s steps(9, end) 1s infinite; opacity: 0; }
        @keyframes glint { 0%, 70% { opacity: 0; transform: translateX(-6px); } 72% { opacity: .9; transform: translateX(-6px); } 95% { opacity: .9; transform: translateX(16px); } 96%, 100% { opacity: 0; transform: translateX(16px); } }
        .spark { opacity: 0; animation: twinkle 3.6s steps(1, end) infinite; }
        .spark.b { animation-delay: 1.4s; }
        @keyframes twinkle { 0%, 60%, 90%, 100% { opacity: 0; } 66%, 84% { opacity: 1; } }

        @media (max-width: 640px) { .coin { width: 88px; height: 88px; } }

        h1 {
            font-family: var(--display); color: var(--ink); font-weight: 700;
            /* Grows with the screen (width and height both count), so the headline fills big displays too */
            font-size: clamp(2.5rem, min(7.5vw, 13vh), 8rem);
            font-size: clamp(2.5rem, min(7.5vw, 13svh), 8rem);
            line-height: 1.04; letter-spacing: -0.04em;
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

        /* Planet cards: the same Codedex-style cards as the in-app planet picker
           (pixel-art banner, PLANET eyebrow, title, blurb, pill). */
        .planet-grid { margin-top: 28px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; text-align: left; }
        .planet-card {
            --accent: #73b6ff;
            position: relative; display: flex; flex-direction: column; overflow: hidden; isolation: isolate;
            color: #eef1ff; background: #0d1028; border: 2px solid #252b52; border-radius: 6px;
            box-shadow: 0 5px 0 #1a1f40, 0 18px 36px -20px rgba(0,0,0,.8);
            transition: transform .25s cubic-bezier(.32,.72,0,1), border-color .2s, box-shadow .25s cubic-bezier(.32,.72,0,1);
        }
        .planet-card.net   { --accent: #5be1ff; }
        .planet-card.cyber { --accent: #b48bff; }
        .planet-card:hover { transform: translateY(-4px); border-color: var(--accent); box-shadow: 0 9px 0 #1a1f40, 0 26px 44px -18px color-mix(in srgb, var(--accent) 45%, transparent); }
        .planet-card:active { transform: translateY(1px); box-shadow: 0 2px 0 #1a1f40; transition-duration: .08s; }
        .planet-card:focus-visible { outline: 3px solid var(--accent); outline-offset: 3px; border-radius: 6px; }
        .pc-scene { position: relative; aspect-ratio: 5 / 2; overflow: hidden; border-bottom: 2px solid #252b52; background: #0b0820; }
        .pc-art { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center bottom; image-rendering: pixelated; transition: transform .5s cubic-bezier(.32,.72,0,1); }
        .planet-card:hover .pc-art { transform: scale(1.04); }
        .pc-body { display: flex; flex: 1; flex-direction: column; padding: 18px 18px 20px; }
        .pc-eyebrow { font-family: var(--display); font-size: 11.5px; font-weight: 600; letter-spacing: .18em; text-transform: uppercase; color: #9aa3d0; }
        .pc-title { margin-top: 4px; font-family: var(--display); font-weight: 700; font-size: 1.3rem; line-height: 1.2; letter-spacing: -.01em; color: #fff; }
        .pc-blurb { margin-top: 8px; font-size: .95rem; line-height: 1.5; color: #c3c9ea; }
        .pc-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: auto; padding-top: 18px; }
        .pc-pill { display: inline-flex; align-items: center; gap: 9px; padding: 7px 14px; border-radius: 99px; font-family: var(--display); font-size: 11.5px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: #dfe4ff; background: #1b2042; white-space: nowrap; }
        .pc-bars { display: inline-flex; align-items: flex-end; gap: 2px; height: 11px; }
        .pc-bars i { width: 3px; background: var(--accent); }
        .pc-bars i:nth-child(1) { height: 4px; } .pc-bars i:nth-child(2) { height: 7px; background: #48507e; } .pc-bars i:nth-child(3) { height: 11px; background: #48507e; }
        .pc-meta { font-size: 13px; color: #9aa3d0; font-variant-numeric: tabular-nums; white-space: nowrap; }
        @media (max-width: 900px) { .planet-grid { grid-template-columns: 1fr; max-width: 460px; margin-left: auto; margin-right: auto; } }

        /* Chunky button: presses down into its own edge */
        .cta-row { margin-top: clamp(40px, 6vh, 64px); display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; }
        .cta-row .btn { height: clamp(58px, 8vh, 68px); font-size: clamp(18px, 2.4vh, 21px); padding: 0 clamp(30px, 3vw, 40px); }
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
        .foot-in { padding: 24px var(--edge); display: flex; flex-wrap: wrap; gap: 12px 24px; align-items: center; justify-content: space-between; font-size: 14px; }
        .foot-in .brand { font-size: 18px; }
        .foot-in .brand img { width: 26px; height: 26px; border-radius: 6px; }

        @media (max-width: 640px) {
            .hero { padding-top: 48px; padding-bottom: 40px; }
            .wrap { padding-left: 20px; padding-right: 20px; }
            .btn-quiet { display: none; }
            .cta-row .btn { flex: 1; padding: 0 16px; white-space: nowrap; }
        }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; scroll-behavior: auto !important; } h1 .line-1, .decode, .rise, .pc-art, .coin, .floor, .bolt, .flame, .flame-in, .gem-shine, .spark, .sound-btn, .sound-btn .w { animation: none !important; opacity: 1; transform: none; filter: none; } .spark { transform: none; opacity: 1; } .flame-in, .bolt-flash, .gem-shine { display: none; } }
        [x-cloak] { display: none !important; }
    </style>
  <link rel="stylesheet" href="{{ asset('css/cursor.css') }}?v={{ filemtime(public_path('css/cursor.css')) }}">
</head>
<body>
    <x-cosmic-background />
    <header class="bar">
        <div class="bar-in">
            <x-brand :href="url('/')" />
            <button type="button" class="sound-btn" id="sound-btn" aria-pressed="false" title="Sound effects and music">
                <svg viewBox="0 0 16 16" width="20" height="20" shape-rendering="crispEdges" fill="currentColor" aria-hidden="true">
                    <rect x="1" y="6" width="3" height="4"/><rect x="4" y="5" width="1" height="6"/><rect x="5" y="4" width="1" height="8"/><rect x="6" y="3" width="1" height="10"/><rect x="7" y="2" width="1" height="12"/>
                    <g class="s-on">
                        <rect class="w" style="--wd:0s" x="10" y="6" width="1" height="4"/>
                        <g class="w" style="--wd:.2s"><rect x="12" y="4" width="1" height="8"/><rect x="11" y="3" width="1" height="1"/><rect x="11" y="12" width="1" height="1"/></g>
                        <g class="w" style="--wd:.4s"><rect x="14" y="3" width="1" height="10"/><rect x="13" y="2" width="1" height="1"/><rect x="13" y="13" width="1" height="1"/></g>
                    </g>
                    <g class="s-off"><rect x="10" y="6" width="1" height="1"/><rect x="11" y="7" width="1" height="1"/><rect x="12" y="8" width="1" height="1"/><rect x="13" y="9" width="1" height="1"/><rect x="14" y="10" width="1" height="1"/><rect x="14" y="6" width="1" height="1"/><rect x="13" y="7" width="1" height="1"/><rect x="11" y="9" width="1" height="1"/><rect x="10" y="10" width="1" height="1"/></g>
                </svg>
                Sound
            </button>
        </div>
    </header>

    <main>
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

        <div class="wrap">
            <section class="section reveal" id="more">
                <h2>Gamify your learning</h2>
                @php
                    // Pixel sprites, drawn as text: each character is one square, '.' is empty.
                    // Runs of the same colour on a row merge into one <rect>, so the SVG stays small.
                    // $depth paints a darker copy 1..n squares lower, the chunky 3D edge under the icon.
                    $sprite = function (array $rows, array $pal, string $edge = '', int $depth = 0) {
                        $out = '';
                        $paint = function (array $rows, callable $color, int $dy) use (&$out) {
                            foreach ($rows as $y => $row) {
                                for ($x = 0, $n = strlen($row); $x < $n; $x++) {
                                    $c = $color($row[$x]);
                                    if ($c === null) { continue; }
                                    $w = 1;
                                    while ($x + $w < $n && $color($row[$x + $w]) === $c) { $w++; }
                                    $out .= '<rect x="'.$x.'" y="'.($y + $dy).'" width="'.$w.'" height="1" fill="'.$c.'"/>';
                                    $x += $w - 1;
                                }
                            }
                        };
                        for ($d = $depth; $d >= 1; $d--) {
                            $paint($rows, fn ($ch) => $ch === '.' ? null : $edge, $d);
                        }
                        $paint($rows, fn ($ch) => $pal[$ch] ?? null, 0);
                        return $out;
                    };

                    $bolt = [
                        '........OOOOOO..',
                        '.......OWLLLYO..',
                        '.......OLLLYO...',
                        '......OWLLYYO...',
                        '......OLLYYO....',
                        '.....OLLYYYO....',
                        '.....OLLYYOOOOO.',
                        '....OLLYYYYYYYO.',
                        '....OLYYYYYYYO..',
                        '...OOOOOOYYYYO..',
                        '........OYYYO...',
                        '.......OYYYO....',
                        '.......OYYO.....',
                        '......OYYO......',
                        '......OYO.......',
                        '.....OYO........',
                        '.....OO.........',
                    ];
                    $boltPal = ['O' => '#8a4a06', 'Y' => '#f5a623', 'L' => '#ffd54a', 'W' => '#fff3c4'];

                    $flameA = [
                        '......O.........',
                        '......OO........',
                        '.....ORO........',
                        '.....ORRO.......',
                        '....ORRRO...O...',
                        '....ORRRRO.OO...',
                        '...ORRARRRORO...',
                        '...ORRAARRRRRO..',
                        '..ORRAAARRRRRO..',
                        '..ORAAAAARRRRRO.',
                        '..ORAAYAAAARRRO.',
                        '..ORAAYYAAAARRO.',
                        '..ORAYYYYAAARRO.',
                        '..ORAYYWYYAARRO.',
                        '...ORAYYYYAARO..',
                        '....ORAAAAARO...',
                        '.....OORRROO....',
                        '.......OOO......',
                    ];
                    // Second frame: the tips lean the other way and the core jumps up a square.
                    $flameB = [
                        '.........O......',
                        '........OO......',
                        '........ORO.....',
                        '.......ORRO.....',
                        '...O...ORRRO....',
                        '...OO.ORRRRO....',
                        '...OROORRARRO...',
                        '..ORRRRRAARRO...',
                        '..ORRRRAAARRRO..',
                        '.ORRRAAAAAARRO..',
                        '.ORRAAAYAAAARRO.',
                        '.ORRAAYYYAAARRO.',
                        '..ORAAYYWYAAARO.',
                        '..ORAAYYYYAARRO.',
                        '...ORAYYYYAARO..',
                        '....ORAAAAARO...',
                        '.....OORRROO....',
                        '.......OOO......',
                    ];
                    $flamePal = ['O' => '#a82a0a', 'R' => '#e0510f', 'A' => '#ff8a1f', 'Y' => '#ffd23a', 'W' => '#fff3b0'];

                    $gem = [
                        '....OOOOOOOO....',
                        '...OWLLLLBBLO...',
                        '..OWLLLLBBBBLO..',
                        '.OWLLLLBBBBBBLO.',
                        'OLLLLLBBBBBBBLLO',
                        'OKKKKKKKKKKKKKKO',
                        '.OBBBBDBBDBBBBO.',
                        '..OBBBDBBDBBBO..',
                        '...OBBDBBDBBO...',
                        '....OBDBBDBO....',
                        '.....ODBBDO.....',
                        '......ODDO......',
                        '.......OO.......',
                    ];
                    $gemPal = ['O' => '#0a4f80', 'K' => '#1a78c4', 'B' => '#3ec8ff', 'D' => '#1e9bd8', 'L' => '#a6ebff', 'W' => '#ffffff'];
                    $spark = ['.W.', 'WWW', '.W.'];
                @endphp
                <div class="grid trio">
                <div class="token card">
                    <div class="coin" style="--b:0s">
                        <svg viewBox="-2 -2 20 23" aria-hidden="true">
                            <g class="bolt">
                                {!! $sprite($bolt, $boltPal, '#b9700a', 2) !!}
                                <g class="bolt-flash">{!! $sprite($bolt, ['O' => '#fff3c4', 'Y' => '#ffffff', 'L' => '#ffffff', 'W' => '#ffffff']) !!}</g>
                            </g>
                        </svg>
                    </div>
                    <div class="floor"></div>
                    <div class="cap">XP<small>Earn per lesson</small></div>
                </div>
                <div class="token card">
                    <div class="coin" style="--b:.5s">
                        <svg viewBox="-2 -2 20 23" aria-hidden="true">
                            <g class="flame">{!! $sprite($flameA, $flamePal, '#6e1a06', 2) !!}</g>
                            <g class="flame-in">{!! $sprite($flameB, $flamePal, '#6e1a06', 2) !!}</g>
                        </svg>
                    </div>
                    <div class="floor"></div>
                    <div class="cap">Streak<small>Come back daily</small></div>
                </div>
                <div class="token card">
                    <div class="coin" style="--b:1s">
                        <svg viewBox="-2 -5 20 23" aria-hidden="true">
                            <defs><clipPath id="gc"><polygon points="4,0 12,0 16,4 16,6 8,13 0,6 0,4"/></clipPath></defs>
                            {!! $sprite($gem, $gemPal, '#125a96', 2) !!}
                            <g clip-path="url(#gc)"><g class="gem-shine"><rect x="0" y="-2" width="2" height="18" fill="#fff" transform="skewX(-20)"/></g></g>
                            <g class="spark" transform="translate(14 -4)">{!! $sprite($spark, ['W' => '#ffffff']) !!}</g>
                            <g class="spark b" transform="translate(-2 8)">{!! $sprite($spark, ['W' => '#bff0ff']) !!}</g>
                        </svg>
                    </div>
                    <div class="floor"></div>
                    <div class="cap">Gems<small>Unlock rewards</small></div>
                </div>
                </div>
            </section>

            <section class="section reveal">
                <h2>Pick your planet</h2>
                <div class="planet-grid">
                    @foreach ($skills as $planet)
                        <a class="planet-card {{ $planet['class'] }}" href="{{ route('register') }}"
                           aria-label="{{ $planet['name'] }}: {{ $planet['courses'] }} courses, {{ $planet['lessons'] }} lessons. Start your mission">
                            <div class="pc-scene" aria-hidden="true">
                                <img class="pc-art" src="{{ asset($planet['art']) }}" width="{{ $planet['w'] }}" height="{{ $planet['h'] }}" alt="" loading="lazy" decoding="async">
                            </div>
                            <div class="pc-body">
                                <span class="pc-eyebrow">Planet</span>
                                <span class="pc-title">{{ $planet['name'] }}</span>
                                <span class="pc-blurb">{{ $planet['blurb'] }}</span>
                                <span class="pc-foot">
                                    <span class="pc-pill"><span class="pc-bars" aria-hidden="true"><i></i><i></i><i></i></span>Beginner</span>
                                    <span class="pc-meta">{{ $planet['courses'] }} courses · {{ $planet['lessons'] }} lessons</span>
                                </span>
                            </div>
                        </a>
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
    <script src="{{ asset('js/game-sound.js') }}?v={{ filemtime(public_path('js/game-sound.js')) }}"></script>
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
                entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); GameSound.play('whoosh'); } });
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
                // Each letter that locks in blips (only while the headline is on screen), and the whole word lands with a chime
                function sfx(name, x) { if (el.getBoundingClientRect().bottom > 0) GameSound.play(name, x); }
                var t = setInterval(function () {
                    var lock = Math.floor(n / 3), out = '';
                    for (var k = 0; k < word.length; k++) {
                        out += k < lock ? word[k] : glyphs[Math.floor(Math.random() * glyphs.length)];
                    }
                    el.textContent = out;
                    if (n % 3 === 0 && lock) sfx('type', lock);
                    if (++n > steps) { clearInterval(t); el.textContent = word; sfx('lock'); done(); }
                }, 38);
            }
            function next() {
                setTimeout(function () { i = (i + 1) % words.length; run(words[i], next); }, 2200);
            }
            setTimeout(next, 1400);
        })();
    </script>
    <script>
        // Game sound: the toggle in the top bar, plus a sound for each thing you can poke on the page.
        (function () {
            var S = window.GameSound, btn = document.getElementById('sound-btn');
            if (!S.supported) { btn.hidden = true; return; }
            btn.setAttribute('aria-pressed', S.on);
            btn.addEventListener('click', function () { S.set(!S.on); btn.setAttribute('aria-pressed', S.on); });

            // Mouse users hear it on hover, touch users when they tap.
            function sound(el, name, x) {
                el.addEventListener('pointerenter', function (e) { if (e.pointerType === 'mouse') S.play(name, x); });
                el.addEventListener('pointerdown', function (e) { if (e.pointerType !== 'mouse') S.play(name, x); });
            }
            document.querySelectorAll('.tl-brand, .scroll-cue, .sound-btn, footer a').forEach(function (el) { sound(el, 'hover'); });

            // The two big buttons: a level-start fanfare and a menu select. The page waits a beat so the sound is heard before it moves on.
            document.querySelectorAll('.cta-row .btn').forEach(function (a, i) {
                sound(a, 'hover', i ? -3 : 0);
                a.addEventListener('click', function (e) {
                    if (!S.on || e.button || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
                    e.preventDefault();
                    S.play(i ? 'select' : 'start');
                    setTimeout(function () { location.href = a.href; }, i ? 220 : 380);
                });
            });

            // XP zaps, the streak flame whooshes, the gem sparkles, and pressing any of them collects a coin.
            ['zap', 'fire', 'gem'].forEach(function (name, i) {
                var t = document.querySelectorAll('.token')[i];
                if (!t) return;
                sound(t, name);
                t.addEventListener('click', function () { S.play('coin'); });
            });

            // Each planet card blips a little higher than the one before it.
            document.querySelectorAll('.planet-card').forEach(function (el, i) { sound(el, 'hover', i * 4); });
            document.querySelectorAll('.astro-note').forEach(function (el) { sound(el, 'robot'); });

            // The background planet whirs when you spin it, and every shooting star whistles past.
            var planet = document.querySelector('.cosmic-planet'), sky = document.querySelector('.cosmic');
            if (planet) planet.addEventListener('click', function () { S.play('spin'); });
            if (sky) new MutationObserver(function (list) {
                list.forEach(function (m) { m.addedNodes.forEach(function (n) { if (n.classList && n.classList.contains('cosmic-shoot')) S.play('star'); }); });
            }).observe(sky, { childList: true });
        })();
    </script>
</body>
</html>
