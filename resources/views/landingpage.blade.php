<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TechLab — Learn. Build. Grow.</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <meta name="description" content="TechLab is an EdTech platform where students learn programming, networking, cybersecurity, and more — through courses, projects, practice, and crews." />

  <!-- Flag JS early so reveal-on-scroll elements start hidden from first paint (no flash). -->
  <script>document.documentElement.classList.add('js');</script>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />

  <style>
    /* ============================================================
       TechLab design tokens — sophisticated blue-based system
       ============================================================ */
    :root {
      color-scheme: dark;

      /* Brand blue — vivid but not neon, tuned for dark surfaces.
         --blue-600 IS the TechLab blue on dark; --brand-blue keeps the
         canonical hex for identity accents so the brand stays recognizable. */
      --brand-blue: #0a5bd6;
      --blue-700: #1a4fb0;
      --blue-600: #2f6fe0;   /* white-on-blue passes AA (4.7:1) */
      --blue-650: #2560d0;
      --blue-500: #4a8cf5;
      --blue-400: #6ba3f8;
      --blue-300: #9cc2fb;
      --blue-100: #16233b;
      --blue-50:  #0c1424;

      /* Neutral ink scale — in dark mode these read as light tints (text). */
      --ink-900: #eaeef6;
      --ink-800: #d9dfeb;
      --ink-700: #b7c0d2;
      --ink-600: #9aa6bd;
      --ink-500: #7c8aa3;   /* faint text + neutral avatars */
      --ink-400: #5b6b86;
      --ink-300: #44506a;
      --ink-200: #2b3445;
      --ink-100: #1a2336;
      --ink-50:  #11192a;

      /* Surfaces & background — elevation, not lightness inversion */
      --bg:        #060910;
      --bg-subtle: #0b111d;
      --surface:   #0e1422;
      --surface-2: #121a2b;

      /* Text */
      --text:       var(--ink-900);
      --text-muted: var(--ink-600);
      --text-faint: var(--ink-500);

      /* Borders — subtle, light-on-dark */
      --border:         rgba(255, 255, 255, 0.08);
      --border-strong:  rgba(255, 255, 255, 0.14);

      /* Single accent — --accent is the blue used as a BACKGROUND (buttons,
         avatars) where white text sits on it; --accent-text is the lighter
         blue used as TEXT on dark surfaces so small labels still clear AA. */
      --accent:       var(--blue-600);
      --accent-hover: var(--blue-500);
      --accent-text:  var(--blue-400);

      /* Dark-mode code surface + progress track */
      --code-bg: #090d15;
      --track:   rgba(255, 255, 255, 0.08);

      /* Restrained shadows + a brand-blue glow for premium depth */
      --shadow-sm:  0 1px 2px rgba(0, 0, 0, 0.5);
      --shadow-md:  0 8px 24px rgba(0, 0, 0, 0.55);
      --shadow-lg:  0 20px 60px rgba(0, 0, 0, 0.6);
      --shadow-blue: 0 12px 34px rgba(47, 116, 230, 0.35);
      --glow: 0 0 0 1px rgba(47, 116, 230, 0.22), 0 14px 44px rgba(47, 116, 230, 0.20);

      /* Radii */
      --radius-sm:   10px;
      --radius-md:   16px;
      --radius-lg:   22px;
      --radius-pill: 999px;

      /* Spacing scale */
      --space-1: 4px;
      --space-2: 8px;
      --space-3: 12px;
      --space-4: 16px;
      --space-5: 24px;
      --space-6: 32px;
      --space-7: 48px;
      --space-8: 64px;
      --space-9: 96px;

      /* Typography */
      --font-display: 'Space Grotesk', system-ui, -apple-system, sans-serif;
      --font-body: 'Inter', system-ui, -apple-system, sans-serif;

      /* Layout */
      --maxw: 1120px;
      --nav-h: 72px;

      /* Motion — Apple-flavored easing & durations (from apple-design + animate).
         Strong ease-out per easing.dev; press is instant, hover is quick. */
      --ease-out:    cubic-bezier(0.23, 1, 0.32, 1);   /* strong UI ease-out */
      --ease-in-out: cubic-bezier(0.77, 0, 0.175, 1);  /* on-screen movement */
      --dur-press:   120ms;   /* button press feedback — fast, perceptible */
      --dur-hover:   220ms;   /* hover lift & color — under 300ms */
      --dur-reveal:  0.6s;    /* entrance, rare/first-time tier */
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    html { scroll-behavior: smooth; }

    body {
      font-family: var(--font-body);
      color: var(--text);
      background: var(--bg);
      -webkit-font-smoothing: antialiased;
      text-rendering: optimizeLegibility;
      line-height: 1.6;
    }

    /* Premium dark scrollbar — keeps the canvas feeling like one surface. */
    * { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.14) transparent; }
    ::-webkit-scrollbar { width: 10px; height: 10px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.14);
      border-radius: 999px;
      border: 2px solid transparent;
      background-clip: content-box;
    }
    ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.24); background-clip: content-box; }

    a { color: inherit; text-decoration: none; }

    :focus-visible {
      outline: 2px solid var(--accent);
      outline-offset: 3px;
      border-radius: 4px;
    }

    /* ---------- Navigation ---------- */
    .nav {
      position: sticky; top: 0; z-index: 50;
      height: var(--nav-h);
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 clamp(20px, 5vw, 64px);
      background: rgba(8, 11, 18, 0.66);
      backdrop-filter: saturate(180%) blur(20px);
      -webkit-backdrop-filter: saturate(180%) blur(20px);
      border-bottom: 1px solid var(--border);
    }
    .brand { display: flex; align-items: center; gap: 10px; }
    .brand svg { width: 30px; height: 30px; color: var(--accent); display: block; }
    .brand .name {
      font-family: var(--font-display); font-weight: 700; font-size: 1.2rem;
      letter-spacing: -0.01em; color: var(--text);
    }
    .brand .name span { color: var(--accent); }

    .nav-links { display: flex; align-items: center; gap: 28px; }
    .nav-links a { font-size: 0.95rem; color: var(--text-muted); transition: color .2s; }
    .nav-links a:hover { color: var(--text); }

    .nav-actions { display: flex; align-items: center; gap: 12px; }

    .nav-cta {
      font-family: var(--font-display); font-weight: 600; font-size: 0.9rem;
      padding: 9px 18px; border-radius: var(--radius-pill);
      background: var(--accent); color: #fff;
      border: 1px solid transparent; cursor: pointer;
      position: relative; overflow: hidden;
      transition:
        background   var(--dur-hover) var(--ease-out),
        transform   var(--dur-press)  var(--ease-out),
        box-shadow  var(--dur-hover)  var(--ease-out);
      will-change: transform;
    }
    /* Press highlight — a soft light wash that fades in on touch-down. */
    .nav-cta::after {
      content: ""; position: absolute; inset: 0; border-radius: inherit;
      background: rgba(255, 255, 255, 0);
      transition: background var(--dur-press) var(--ease-out);
      pointer-events: none;
    }
    @media (hover: hover) and (pointer: fine) {
      .nav-cta:hover {
        background: var(--accent-hover);
        transform: translateY(-1px);
        box-shadow: var(--shadow-blue);
      }
    }
    /* Respond on the press itself, not on release — Apple's first rule. */
    .nav-cta:active { transform: scale(0.96); }
    .nav-cta:active::after { background: rgba(255, 255, 255, 0.20); }

    .nav-cta--ghost {
      background: transparent; color: var(--text);
      border: 1px solid var(--border-strong);
    }
    @media (hover: hover) and (pointer: fine) {
      .nav-cta--ghost:hover {
        background: var(--bg-subtle);
        transform: translateY(-1px);
        box-shadow: none;
      }
    }
    .nav-cta--ghost:active::after { background: rgba(11, 18, 32, 0.06); }

    .nav-toggle {
      display: none;
      width: 42px; height: 42px; border-radius: var(--radius-sm);
      border: 1px solid var(--border); background: var(--surface);
      color: var(--text); cursor: pointer;
      align-items: center; justify-content: center;
      transition: background .2s;
    }
    .nav-toggle:hover { background: var(--bg-subtle); }

    /* ---------- Hero ---------- */
    .hero {
      position: relative;
      overflow: hidden;
      max-width: var(--maxw);
      margin: 0 auto;
      padding: clamp(64px, 12vh, 120px) clamp(20px, 5vw, 64px) clamp(48px, 8vh, 80px);
      text-align: center;
      display: flex; flex-direction: column; align-items: center;
    }
    /* Signature — a single brand-blue radial glow + a faint engineering grid
       that fades from the top. The "lab" motif, not decoration: it reads as the
       blueprint behind the product. Restrained on purpose (one bold move). */
    .hero::before {
      content: ""; position: absolute; inset: 0; z-index: 0; pointer-events: none;
      background: radial-gradient(900px 520px at 50% -8%, rgba(47, 116, 230, 0.22), transparent 62%);
    }
    .hero::after {
      content: ""; position: absolute; left: 0; right: 0; top: 0; height: 560px; z-index: 0; pointer-events: none;
      background-image:
        linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
      background-size: 64px 64px;
      -webkit-mask-image: radial-gradient(72% 60% at 50% 0%, #000, transparent 78%);
              mask-image: radial-gradient(72% 60% at 50% 0%, #000, transparent 78%);
    }
    /* Keep hero content above the glow/grid layers. */
    .hero > * { position: relative; z-index: 1; }
    .eyebrow {
      font-size: 0.8rem; font-weight: 600; letter-spacing: 0.08em;
      text-transform: uppercase; color: var(--accent-text);
      background: var(--blue-50); border: 1px solid var(--border);
      padding: 7px 14px; border-radius: var(--radius-pill);
      margin-bottom: var(--space-5);
    }
    .hero h1 {
      font-family: var(--font-display); font-weight: 700;
      font-size: clamp(2.4rem, 5.5vw, 4.4rem);
      line-height: 1.05; letter-spacing: -0.03em;
      max-width: 18ch; margin: 0 auto; color: var(--text);
    }
    .hero h1 .accent {
      background: linear-gradient(120deg, var(--blue-500), var(--blue-400));
      -webkit-background-clip: text; background-clip: text; color: transparent;
    }
    .hero p.sub {
      font-size: clamp(1.05rem, 2vw, 1.25rem); color: var(--text-muted);
      max-width: 60ch; margin: var(--space-5) auto 0; line-height: 1.6;
    }
    .ctas {
      display: flex; gap: 14px; flex-wrap: wrap; justify-content: center;
      margin-top: var(--space-6);
    }
    .btn {
      font-family: var(--font-display); font-weight: 600; font-size: 1rem;
      padding: 14px 28px; border-radius: var(--radius-pill); cursor: pointer; border: none;
      display: inline-flex; align-items: center; gap: 10px;
      position: relative; overflow: hidden;
      transition:
        transform    var(--dur-press)  var(--ease-out),
        box-shadow   var(--dur-hover)  var(--ease-out),
        background   var(--dur-hover)  var(--ease-out),
        border-color var(--dur-hover)  var(--ease-out);
      will-change: transform;
    }
    /* Soft light wash on press — feedback lives on the touch-down, not the release. */
    .btn::after {
      content: ""; position: absolute; inset: 0; border-radius: inherit;
      background: rgba(255, 255, 255, 0);
      transition: background var(--dur-press) var(--ease-out);
      pointer-events: none;
    }
    .btn-primary {
      color: #fff;
      background: linear-gradient(180deg, var(--blue-600), var(--blue-650));
      box-shadow: var(--shadow-blue), inset 0 1px 0 rgba(255, 255, 255, 0.28);
    }
    @media (hover: hover) and (pointer: fine) {
      .btn-primary:hover {
        background: var(--accent-hover);
        transform: translateY(-2px);
        box-shadow: 0 14px 34px rgba(30, 111, 224, 0.28), inset 0 1px 0 rgba(255, 255, 255, 0.32);
      }
    }
    .btn-secondary {
      color: var(--text); background: var(--surface);
      border: 1px solid var(--border-strong);
    }
    @media (hover: hover) and (pointer: fine) {
      .btn-secondary:hover {
        border-color: var(--ink-400); background: var(--bg-subtle);
        transform: translateY(-2px);
      }
    }
    .btn:active { transform: scale(0.96); }
    .btn:active::after { background: rgba(255, 255, 255, 0.16); }
    .btn-secondary:active::after { background: rgba(11, 18, 32, 0.05); }
    .btn .arrow {
      transition: transform var(--dur-hover) var(--ease-out);
    }
    @media (hover: hover) and (pointer: fine) {
      .btn:hover .arrow { transform: translateX(4px); }
    }

    .login-hint { margin-top: var(--space-5); font-size: 0.92rem; color: var(--text-faint); }
    .login-hint a { color: var(--accent-text); font-weight: 600; }
    .login-hint a:hover { text-decoration: underline; }
    .logout-form, .nav-logout { display: inline-flex; margin: 0; }

    /* ---------- Hero product visual (real product UI) ---------- */
    .hero-shot {
      width: 100%; max-width: 1000px;
      margin: var(--space-8) auto 0;
    }
    .app {
      background: var(--surface);
      border: 1px solid var(--border-strong);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
      overflow: hidden; text-align: left;
    }
    /* Hero product window floats on a brand-blue glow for premium depth. */
    .hero-shot .app {
      box-shadow: var(--shadow-lg), var(--glow);
      border-color: rgba(47, 116, 230, 0.28);
    }
    .app-bar {
      display: flex; align-items: center; justify-content: space-between;
      padding: 12px 18px; border-bottom: 1px solid var(--border);
      background: var(--bg-subtle);
    }
    .app-bar .brand-mini {
      display: flex; align-items: center; gap: 8px;
      font-family: var(--font-display); font-weight: 600; font-size: 0.9rem; color: var(--text);
    }
    .app-bar .brand-mini svg { width: 18px; height: 18px; color: var(--accent); }
    .app-bar .app-title { font-size: 0.82rem; color: var(--text-faint); }

    .app-body { display: grid; grid-template-columns: 200px 1fr 244px; min-height: 400px; }
    .app-side {
      display: flex; flex-direction: column;
      border-right: 1px solid var(--border); padding: 18px 14px; background: var(--surface);
    }
    .app-brand {
      display: flex; align-items: center; gap: 8px;
      font-family: var(--font-display); font-weight: 700; font-size: 1rem; color: var(--text);
    }
    .app-brand svg { width: 22px; height: 22px; color: var(--accent); }
    .app-nav { display: flex; flex-direction: column; gap: 4px; margin-top: 20px; }
    .app-nav a { padding: 9px 12px; border-radius: var(--radius-sm); font-size: 0.88rem; color: var(--text-muted); }
    .app-nav a.active { background: var(--blue-50); color: var(--accent-text); font-weight: 600; }
    .app-user {
      display: flex; align-items: center; gap: 10px; margin-top: auto;
      padding-top: 16px; border-top: 1px solid var(--border);
    }
    .app-user .uname { font-size: 0.85rem; font-weight: 600; color: var(--text); }

    .app-main { display: flex; flex-direction: column; gap: 14px; padding: 20px; background: var(--bg-subtle); }
    .chat { display: flex; flex-direction: column; gap: 14px; }
    .msg { max-width: 86%; padding: 12px 14px; border-radius: 14px; font-size: 0.9rem; line-height: 1.5; }
    .msg.user { align-self: flex-end; background: var(--accent); color: #fff; border-bottom-right-radius: 4px; }
    .msg.ai { align-self: flex-start; background: var(--surface); border: 1px solid var(--border); border-bottom-left-radius: 4px; }
    .ai-head {
      font-size: 0.75rem; font-weight: 600; color: var(--accent-text); margin-bottom: 6px;
      display: flex; align-items: center; gap: 6px;
    }
    .msg.ai p { color: var(--text); margin-bottom: 10px; }
    .code {
      background: var(--code-bg); color: #d7e0f0; border: 1px solid var(--border-strong); padding: 12px 14px; border-radius: 10px;
      font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.78rem;
      line-height: 1.5; overflow-x: auto; margin-bottom: 10px;
    }
    .ai-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .chip {
      font-size: 0.78rem; padding: 6px 12px; border-radius: var(--radius-pill);
      border: 1px solid var(--border-strong); background: var(--surface); color: var(--text-muted);
      cursor: default;
    }
    .chip:hover { border-color: var(--accent); color: var(--accent); }

    .app-rail {
      display: flex; flex-direction: column; gap: 14px;
      border-left: 1px solid var(--border); padding: 18px 16px; background: var(--surface);
    }
    .rail-card { border: 1px solid var(--border); border-radius: var(--radius-md); padding: 14px; background: var(--surface); }
    .rc-label { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); margin-bottom: 10px; }
    .rc-track { height: 8px; border-radius: 999px; background: var(--track); overflow: hidden; }
    .rc-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, var(--blue-500), var(--blue-400)); }
    .rc-meta { font-size: 0.75rem; color: var(--text-faint); margin-top: 8px; }
    .xp-num { font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; color: var(--text); }
    .xp-num span { font-size: 0.8rem; color: var(--text-faint); font-weight: 500; }
    .xp-badge {
      display: inline-flex; align-items: center; gap: 6px; margin-top: 8px;
      font-size: 0.78rem; color: var(--accent-text); background: var(--blue-50);
      padding: 5px 10px; border-radius: var(--radius-pill);
    }
    .crew-item { display: flex; align-items: center; gap: 10px; font-size: 0.8rem; color: var(--text-muted); margin-top: 10px; }
    .ava { width: 28px; height: 28px; border-radius: 50%; display: grid; place-items: center;
      font-size: 0.7rem; font-weight: 700; color: #fff; flex: 0 0 auto; }
    .ava--mr { background: var(--blue-600); }
    .ava--ak { background: var(--ink-500); }

    /* ---------- Product-story: three pillars ---------- */
    .pillars {
      max-width: var(--maxw); margin: 0 auto;
      padding: var(--space-9) clamp(20px, 5vw, 64px);
    }
    .pillars-head { text-align: center; max-width: 640px; margin: 0 auto var(--space-8); }
    .pillars-head h2 {
      font-family: var(--font-display); font-weight: 700;
      font-size: clamp(1.9rem, 4vw, 2.8rem); letter-spacing: -0.02em;
      margin: var(--space-4) 0; color: var(--text);
    }
    .pillars-head p { color: var(--text-muted); font-size: 1.05rem; }

    .pillar {
      display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-7);
      align-items: center; padding: var(--space-7) 0; border-top: 1px solid var(--border);
    }
    .pillar--reverse .pillar-visual { order: -1; }
    .pillar-index {
      font-family: var(--font-display); font-weight: 700; font-size: 0.95rem;
      color: var(--accent-text); letter-spacing: 0.12em;
    }
    .pillar h3 {
      font-family: var(--font-display); font-weight: 700;
      font-size: clamp(1.5rem, 3vw, 2rem); letter-spacing: -0.02em;
      margin: var(--space-3) 0; color: var(--text);
    }
    .pillar p { color: var(--text-muted); font-size: 1.05rem; line-height: 1.65; max-width: 46ch; }
    .pillar-visual {
      border: 1px solid var(--border); border-radius: var(--radius-lg);
      padding: 20px; background: var(--surface); box-shadow: var(--shadow-sm);
    }

    /* ---------- Deep product sections ---------- */
    .showcase { max-width: var(--maxw); margin: 0 auto; padding: var(--space-9) clamp(20px, 5vw, 64px); }
    .showcase .pillar { border-top: none; padding: var(--space-8) 0; }

    /* AI Tutor chat window */
    .chat-win .app-main { min-height: 360px; }
    .caps { display: flex; flex-wrap: wrap; gap: 8px; margin-top: var(--space-5); }

    /* Gamified courses */
    .gc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .gc-grid .rail-card { padding: 16px; }
    .gc-stat { display: flex; align-items: baseline; gap: 8px; }
    .gc-stat .num { font-family: var(--font-display); font-size: 1.6rem; font-weight: 700; color: var(--text); }
    .gc-stat .lbl { font-size: 0.8rem; color: var(--text-faint); }
    .ach-row { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }

    /* Crew relationship map */
    .crew-section { max-width: var(--maxw); margin: 0 auto; padding: var(--space-9) clamp(20px, 5vw, 64px); text-align: center; }
    .crew-map { display: flex; align-items: center; justify-content: center; flex-wrap: wrap; margin: var(--space-7) 0; }
    .cnode { display: flex; flex-direction: column; align-items: center; text-align: center; width: 170px; padding: 0 8px; }
    .cava { width: 66px; height: 66px; border-radius: 50%; display: grid; place-items: center; color: #fff;
      font-family: var(--font-display); font-weight: 700; font-size: 1.1rem; box-shadow: var(--shadow-sm); }
    .cava svg { width: 30px; height: 30px; }
    .cava--you { background: var(--blue-600); }
    .cava--ai { background: var(--blue-500); }
    .cava--teacher { background: var(--blue-400); }
    .cava--class { background: var(--ink-500); }
    .cname { font-family: var(--font-display); font-weight: 600; font-size: 0.98rem; margin-top: 12px; color: var(--text); }
    .ccap { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; line-height: 1.4; }
    .clink { width: 46px; height: 2px; background: linear-gradient(90deg, transparent, var(--blue-300), transparent); align-self: center; }
    .crew-feed { max-width: 560px; margin: 0 auto; text-align: left; }

    /* Prevent grid/flex children from overflowing on small screens */
    .pillar, .pillar-text, .pillar-visual { min-width: 0; }
    .app, .app-main, .chat, .msg { min-width: 0; }
    .code { max-width: 100%; }

    /* ---------- Progress strip ---------- */
    .progress-wrap {
      max-width: var(--maxw); margin: 0 auto;
      padding: 0 clamp(20px, 5vw, 64px) var(--space-8);
    }
    .progress {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius-lg); padding: 32px;
      box-shadow: var(--shadow-sm);
      display: flex; align-items: center; gap: 28px; flex-wrap: wrap;
    }
    .progress .ptext { flex: 1 1 280px; }
    .progress .ptext .tag {
      font-size: 0.72rem; font-weight: 600; letter-spacing: 0.1em;
      text-transform: uppercase; color: var(--accent-text);
    }
    .progress .ptext h3 { font-family: var(--font-display); font-size: 1.35rem; margin-top: 6px; color: var(--text); }
    .progress .ptext p { color: var(--text-muted); font-size: 0.95rem; margin-top: 6px; }
    .bar { flex: 1 1 260px; }
    .bar .track {
      height: 12px; border-radius: var(--radius-pill);
      background: var(--track); overflow: hidden; border: 1px solid var(--border);
    }
    .bar .fill {
      height: 100%; width: 0; border-radius: var(--radius-pill);
      background: linear-gradient(90deg, var(--blue-500), var(--blue-400));
      transition: width 1.6s cubic-bezier(.2, .8, .2, 1);
    }
    .bar .meta {
      display: flex; justify-content: space-between; margin-top: 10px;
      font-size: 0.8rem; color: var(--text-faint);
    }

    /* ---------- Footer ---------- */
    footer {
      border-top: 1px solid var(--border);
      max-width: var(--maxw); margin: 0 auto;
      padding: 32px clamp(20px, 5vw, 64px);
      display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;
      color: var(--text-faint); font-size: 0.9rem;
    }
    footer .brand .name { font-size: 1.05rem; }
    footer .links { display: flex; gap: 22px; }
    footer .links a { color: var(--text-muted); }
    footer .links a:hover { color: var(--text); }

    /* ---------- Calm, intentional reveal ---------- */
    .js .reveal { opacity: 0; transform: translateY(20px); transition: opacity var(--dur-reveal) var(--ease-out), transform var(--dur-reveal) var(--ease-out); }
    .js .reveal.in { opacity: 1; transform: none; }

    /* Hero CTAs enter in sequence (rare/first-time tier — the delight budget).
       30–80ms stagger per the animation skill; each button rides the same
       strong ease-out so the group settles as one. */
    .ctas .btn.reveal { transition-delay: calc(var(--i, 0) * 70ms); }
    .ctas .reveal { transition-delay: 0ms; }

    /* ---------- Responsive ---------- */
    @media (max-width: 860px) {
      .pillar { grid-template-columns: 1fr; gap: var(--space-5); }
      .pillar--reverse .pillar-visual { order: 0; }
      .app-body { grid-template-columns: 1fr; }
      .app-side { display: none; }
      .app-rail { border-left: none; border-top: 1px solid var(--border); flex-direction: row; flex-wrap: wrap; }
      .app-rail .rail-card { flex: 1 1 200px; }
      .gc-grid { grid-template-columns: 1fr; }
      .crew-map { flex-direction: column; }
      .clink { width: 2px; height: 26px; background: linear-gradient(180deg, transparent, var(--blue-300), transparent); }
      .cnode { width: auto; }
      .nav-toggle { display: inline-flex; }
      .nav-links {
        position: absolute; top: var(--nav-h); left: 0; right: 0;
        flex-direction: column; align-items: stretch; gap: 0;
        background: rgba(10, 14, 22, 0.97);
        backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid var(--border);
        padding: 8px clamp(20px, 5vw, 64px) 16px;
        display: none;
      }
      .nav.nav-open .nav-links { display: flex; }
      .nav-links a { padding: 14px 4px; border-bottom: 1px solid var(--border); }
      .nav-links a:last-child { border-bottom: none; }
    }

    @media (prefers-reduced-motion: reduce) {
      *, *::after, *::before { animation: none !important; transition: none !important; }
      html { scroll-behavior: auto; }
      .js .reveal { opacity: 1; transform: none; }
      .bar .fill { width: 68% !important; }
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <header class="nav" id="nav">
    <a class="brand" href="/" aria-label="TechLab home">
      <svg viewBox="0 0 108.89 108.89" role="img" aria-hidden="true">
        <polygon fill="currentColor" points="37.55 108.89 52.56 108.89 52.56 90.12 90.12 90.12 90.12 75.09 52.56 75.09 52.56 56.33 37.55 56.33 37.55 108.89"/>
        <polygon fill="currentColor" points="108.89 71.34 108.89 56.33 90.12 56.33 90.12 18.78 75.09 18.78 75.09 56.33 56.33 56.33 56.33 71.34 108.89 71.34"/>
        <polygon fill="currentColor" points="71.34 0 56.33 0 56.33 18.78 18.78 18.78 18.78 33.79 56.33 33.79 56.33 52.56 71.34 52.56 71.34 0"/>
        <polygon fill="currentColor" points="0 37.55 0 52.56 18.78 52.56 18.78 90.12 33.79 90.12 33.79 52.56 52.56 52.56 52.56 37.55 0 37.55"/>
      </svg>
      <span class="name">Tech<span>Lab</span></span>
    </a>

    <nav class="nav-links" id="nav-links" aria-label="Primary">
      <a href="#features">AI Tutor</a>
      <a href="#courses">Courses</a>
      <a href="#crew">Crew</a>
      <a href="#progress">Progress</a>
    </nav>

    <div class="nav-actions">
      @guest
        <a class="nav-cta" href="/register">Get started</a>
      @else
        <form method="POST" action="/logout" class="nav-logout">
          @csrf
          <button class="nav-cta nav-cta--ghost" type="submit">Log out</button>
        </form>
      @endguest
      <button class="nav-toggle" type="button" aria-label="Toggle menu" aria-expanded="false" aria-controls="nav-links">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
          <path d="M4 7h16M4 12h16M4 17h16"/>
        </svg>
      </button>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero" id="top">
    <div class="hero-inner">
      <span class="eyebrow">AI tutor · Courses · Crew</span>
      <h1>Learn to build with <span class="accent">real momentum</span>.</h1>
      <p class="sub">TechLab pairs you with an AI tutor that explains, debugs, and adapts — plus the courses, projects, and people that turn practice into real, demonstrable skill.</p>
      <div class="ctas">
        <a class="btn btn-primary" href="/register">Get started <span class="arrow" aria-hidden="true">→</span></a>
        <a class="btn btn-secondary" href="#features">See how it works</a>
      </div>
      <p class="login-hint">Already a member? <a href="/login">Log in</a></p>
    </div>

    <!-- Product UI centerpiece — the single signature of the page -->
    <div class="hero-shot">
      <div class="app">
        <div class="app-bar">
          <div class="brand-mini">
            <svg viewBox="0 0 108.89 108.89" aria-hidden="true"><polygon fill="currentColor" points="37.55 108.89 52.56 108.89 52.56 90.12 90.12 90.12 90.12 75.09 52.56 75.09 52.56 56.33 37.55 56.33 37.55 108.89"/><polygon fill="currentColor" points="108.89 71.34 108.89 56.33 90.12 56.33 90.12 18.78 75.09 18.78 75.09 56.33 56.33 56.33 56.33 71.34 108.89 71.34"/><polygon fill="currentColor" points="71.34 0 56.33 0 56.33 18.78 18.78 18.78 18.78 33.79 56.33 33.79 56.33 52.56 71.34 52.56 71.34 0"/><polygon fill="currentColor" points="0 37.55 0 52.56 18.78 52.56 18.78 90.12 33.79 90.12 33.79 52.56 52.56 52.56 52.56 37.55 0 37.55"/></svg>
            TechLab
          </div>
          <div class="app-title">AI Tutor</div>
        </div>
        <div class="app-body">
          <aside class="app-side">
            <div class="app-brand">
              <svg viewBox="0 0 108.89 108.89" aria-hidden="true"><polygon fill="currentColor" points="37.55 108.89 52.56 108.89 52.56 90.12 90.12 90.12 90.12 75.09 52.56 75.09 52.56 56.33 37.55 56.33 37.55 108.89"/><polygon fill="currentColor" points="108.89 71.34 108.89 56.33 90.12 56.33 90.12 18.78 75.09 18.78 75.09 56.33 56.33 56.33 56.33 71.34 108.89 71.34"/><polygon fill="currentColor" points="71.34 0 56.33 0 56.33 18.78 18.78 18.78 18.78 33.79 56.33 33.79 56.33 52.56 71.34 52.56 71.34 0"/><polygon fill="currentColor" points="0 37.55 0 52.56 18.78 52.56 18.78 90.12 33.79 90.12 33.79 52.56 52.56 52.56 52.56 37.55 0 37.55"/></svg>
              TechLab
            </div>
            <nav class="app-nav">
              <a class="active" href="#features">Tutor</a>
              <a href="#courses">Courses</a>
              <a href="#crew">Crew</a>
              <a href="#progress">Progress</a>
            </nav>
            <div class="app-user">
              <span class="ava ava--mr">JM</span>
              <span class="uname">Jordan M.</span>
            </div>
          </aside>

          <div class="app-main">
            <div class="chat">
              <div class="msg user">Why does my loop print the same value every time?</div>
              <div class="msg ai">
                <div class="ai-head">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a4 4 0 0 1 4 4 4 4 0 0 1-4 4 4 4 0 0 1-4-4 4 4 0 0 1 4-4z"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                  AI Tutor
                </div>
                <p>You're closing over the same variable. Capture it per iteration:</p>
                <pre class="code">for (let i = 0; i &lt; n; i++) {
  const value = items[i];
  buttons[i].onclick = () =&gt; show(value);
}</pre>
                <div class="ai-actions"><span class="chip">Quiz me</span><span class="chip">Show example</span></div>
              </div>
              <div class="msg user">Can you explain it even simpler?</div>
              <div class="msg ai">
                <div class="ai-head">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a4 4 0 0 1 4 4 4 4 0 0 1-4 4 4 4 0 0 1-4-4 4 4 0 0 1 4-4z"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                  AI Tutor
                </div>
                <p>Think of it like nametags: if everyone shares one, they all answer to the last person. Give each loop its own nametag and it stays correct.</p>
              </div>
            </div>
          </div>

          <aside class="app-rail">
            <div class="rail-card">
              <div class="rc-label">Python Basics</div>
              <div class="rc-track"><div class="rc-fill" style="width:68%"></div></div>
              <div class="rc-meta">68% · 7 of 12 lessons</div>
            </div>
            <div class="rail-card">
              <div class="rc-label">Your streak</div>
              <div class="xp-num">7 <span>days</span></div>
              <div class="xp-badge">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3c1 3-2 4-2 7a2 2 0 0 0 4 0c0-1 0-2-1-3 3 1 5 4 5 7a6 6 0 0 1-12 0c0-4 4-6 6-11z"/></svg>
                7-Day Streak
              </div>
            </div>
          </aside>
        </div>
      </div>
    </div>
  </section>


  <!-- AI Tutor -->
  <section class="showcase" id="features">
    <header class="pillars-head reveal">
      <span class="eyebrow">AI Tutor</span>
      <h2>A tutor that explains, not just answers</h2>
      <p>TechLab's AI tutor meets students where they are — clarifying concepts, debugging real code, and adapting every explanation until it clicks.</p>
    </header>
    <div class="pillar reveal">
      <div class="pillar-text">
        <span class="pillar-index">01</span>
        <h3>Always-on guidance</h3>
        <p>Ask a question, get an explanation you understand, and take the next step — with a tutor that remembers how you learn.</p>
        <div class="caps">
          <span class="chip">Answers questions</span>
          <span class="chip">Debugs your code</span>
          <span class="chip">Adapts to you</span>
          <span class="chip">Guides step by step</span>
        </div>
      </div>
      <div class="pillar-visual">
        <div class="app chat-win">
          <div class="app-bar">
            <div class="brand-mini">
              <svg viewBox="0 0 108.89 108.89" aria-hidden="true"><polygon fill="currentColor" points="37.55 108.89 52.56 108.89 52.56 90.12 90.12 90.12 90.12 75.09 52.56 75.09 52.56 56.33 37.55 56.33 37.55 108.89"/><polygon fill="currentColor" points="108.89 71.34 108.89 56.33 90.12 56.33 90.12 18.78 75.09 18.78 75.09 56.33 56.33 56.33 56.33 71.34 108.89 71.34"/><polygon fill="currentColor" points="71.34 0 56.33 0 56.33 18.78 18.78 18.78 18.78 33.79 56.33 33.79 56.33 52.56 71.34 52.56 71.34 0"/><polygon fill="currentColor" points="0 37.55 0 52.56 18.78 52.56 18.78 90.12 33.79 90.12 33.79 52.56 52.56 52.56 52.56 37.55 0 37.55"/></svg>
              TechLab
            </div>
            <div class="app-title">AI Tutor</div>
          </div>
          <div class="app-main">
            <div class="chat">
              <div class="msg user">Why does my loop print the same value every time?</div>
              <div class="msg ai">
                <div class="ai-head">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a4 4 0 0 1 4 4 4 4 0 0 1-4 4 4 4 0 0 1-4-4 4 4 0 0 1 4-4z"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                  AI Tutor
                </div>
                <p>You're closing over the same variable. Capture it per iteration:</p>
                <pre class="code">for (let i = 0; i &lt; n; i++) {
  const value = items[i];
  buttons[i].onclick = () =&gt; show(value);
}</pre>
                <div class="ai-actions"><span class="chip">Quiz me</span><span class="chip">Show example</span></div>
              </div>
              <div class="msg user">Can you explain it even simpler?</div>
              <div class="msg ai">
                <div class="ai-head">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a4 4 0 0 1 4 4 4 4 0 0 1-4 4 4 4 0 0 1-4-4 4 4 0 0 1 4-4z"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                  AI Tutor
                </div>
                <p>Think of it like nametags: if everyone shares one, they all answer to the last person. Give each loop its own nametag and it stays correct.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Gamified Courses -->
  <section class="showcase" id="courses">
    <div class="pillar pillar--reverse reveal">
      <div class="pillar-text">
        <span class="pillar-index">02</span>
        <h3>Learning has momentum.</h3>
        <p>Progress you can feel: levels, XP, projects, and challenges that turn steady effort into real, demonstrable skill.</p>
      </div>
      <div class="pillar-visual">
        <div class="gc-grid">
          <div class="rail-card">
            <div class="rc-label">Python Basics</div>
            <div class="rc-track"><div class="rc-fill" style="width:68%"></div></div>
            <div class="rc-meta">68% · 7 of 12 lessons</div>
          </div>
          <div class="rail-card">
            <div class="gc-stat"><span class="num">Lv 6</span><span class="lbl">2,480 XP</span></div>
            <div class="rc-track" style="margin-top:10px"><div class="rc-fill" style="width:40%"></div></div>
            <div class="rc-meta">480 XP to Level 7</div>
          </div>
          <div class="rail-card" style="grid-column: 1 / -1">
            <div class="rc-label">Achievements &amp; momentum</div>
            <div class="ach-row">
              <span class="xp-badge"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="9" r="5"/><path d="M9 13l-1 8 4-2 4 2-1-8"/></svg> Bug Hunter</span>
              <span class="xp-badge"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 21V4"/><path d="M5 4h11l-2 4 2 4H5"/></svg> First Project</span>
              <span class="xp-badge"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3c1 3-2 4-2 7a2 2 0 0 0 4 0c0-1 0-2-1-3 3 1 5 4 5 7a6 6 0 0 1-12 0c0-4 4-6 6-11z"/></svg> 7-Day Streak</span>
            </div>
            <div class="crew-item" style="margin-top:14px"><span class="ava ava--mr">!</span> Challenge: Build a REST API — open</div>
            <div class="crew-item"><span class="ava ava--ak">✓</span> Project: CLI To-Do — completed</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Crew -->
  <section class="crew-section" id="crew">
    <header class="pillars-head reveal">
      <span class="eyebrow">Crew</span>
      <h2>Learning is social</h2>
      <p>TechLab isn't just an AI chatbot. It's a connected environment where teachers guide you, classmates build with you, and your AI tutor ties it all together.</p>
    </header>
    <div class="crew-map reveal">
      <div class="cnode">
        <div class="cava cava--you">JM</div>
        <div class="cname">You</div>
        <div class="ccap">The student at the center</div>
      </div>
      <div class="clink"></div>
      <div class="cnode">
        <div class="cava cava--ai">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a4 4 0 0 1 4 4 4 4 0 0 1-4 4 4 4 0 0 1-4-4 4 4 0 0 1 4-4z"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
        </div>
        <div class="cname">AI Tutor</div>
        <div class="ccap">Explains, debugs, quizzes</div>
      </div>
      <div class="clink"></div>
      <div class="cnode">
        <div class="cava cava--teacher">MR</div>
        <div class="cname">Teacher</div>
        <div class="ccap">Guides your track</div>
      </div>
      <div class="clink"></div>
      <div class="cnode">
        <div class="cava cava--class">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="10" r="2.4"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5"/><path d="M15 20c0-2 1-3.5 3-3.5s3 1.5 3 3.5"/></svg>
        </div>
        <div class="cname">Classmates</div>
        <div class="ccap">Build and learn together</div>
      </div>
    </div>
    <div class="crew-feed reveal">
      <div class="crew-item"><span class="ava ava--mr">MR</span> Ms. Rivera shared a weekend challenge</div>
      <div class="crew-item"><span class="ava ava--ak">AK</span> Alex finished the CLI To-Do project</div>
      <div class="crew-item"><span class="ava ava--mr">JM</span> Your AI tutor summarized today's lesson</div>
    </div>
  </section>

  <!-- Progress -->
  <div class="progress-wrap" id="progress">
    <div class="progress reveal">
      <div class="ptext">
        <span class="tag">Your progress</span>
        <h3>Watch your skills grow</h3>
        <p>Every course you finish builds real, demonstrable ability — a portfolio that proves what you can do.</p>
      </div>
      <div class="bar">
        <div class="track"><div class="fill" id="fill"></div></div>
        <div class="meta"><span>In progress</span><span>68% complete</span></div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <a class="brand" href="/">
      <svg viewBox="0 0 108.89 108.89" aria-hidden="true" style="width:26px;height:26px">
        <polygon fill="currentColor" points="37.55 108.89 52.56 108.89 52.56 90.12 90.12 90.12 90.12 75.09 52.56 75.09 52.56 56.33 37.55 56.33 37.55 108.89"/>
        <polygon fill="currentColor" points="108.89 71.34 108.89 56.33 90.12 56.33 90.12 18.78 75.09 18.78 75.09 56.33 56.33 56.33 56.33 71.34 108.89 71.34"/>
        <polygon fill="currentColor" points="71.34 0 56.33 0 56.33 18.78 18.78 18.78 18.78 33.79 56.33 33.79 56.33 52.56 71.34 52.56 71.34 0"/>
        <polygon fill="currentColor" points="0 37.55 0 52.56 18.78 52.56 18.78 90.12 33.79 90.12 33.79 52.56 52.56 52.56 52.56 37.55 0 37.55"/>
      </svg>
      <span class="name">Tech<span>Lab</span></span>
    </a>
    <div class="links">
      <a href="#features">AI Tutor</a>
      <a href="#courses">Courses</a>
      <a href="#crew">Crew</a>
    </div>
    <span>© 2026 TechLab — Learn. Build. Grow.</span>
  </footer>

  <script>
    // Progressive enhancement flag is set early in <head> so reveal elements
    // start hidden from first paint. Content remains visible without JS.

    // Calm scroll reveal — IntersectionObserver, no library.
    (function () {
      const els = document.querySelectorAll('.reveal');
      if (!('IntersectionObserver' in window)) {
        els.forEach(e => e.classList.add('in'));
        return;
      }
      const io = new IntersectionObserver((entries) => {
        entries.forEach(en => {
          if (en.isIntersecting) {
            en.target.classList.add('in');
            io.unobserve(en.target);
          }
        });
      }, { threshold: 0.15, rootMargin: '0px 0px -10% 0px' });
      els.forEach(e => io.observe(e));
    })();

    // Progress bar fill on load (respects reduced motion).
    window.addEventListener('load', () => {
      const fill = document.getElementById('fill');
      if (!fill) return;
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        fill.style.width = '68%';
      } else {
        setTimeout(() => { fill.style.width = '68%'; }, 300);
      }
    });

    // Mobile navigation toggle.
    (function () {
      const nav = document.getElementById('nav');
      const toggle = document.querySelector('.nav-toggle');
      if (!nav || !toggle) return;
      toggle.addEventListener('click', () => {
        const open = nav.classList.toggle('nav-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
      nav.querySelectorAll('.nav-links a').forEach(a => {
        a.addEventListener('click', () => {
          nav.classList.remove('nav-open');
          toggle.setAttribute('aria-expanded', 'false');
        });
      });
    })();
  </script>
</body>
</html>
