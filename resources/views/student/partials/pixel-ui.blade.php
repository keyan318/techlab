/*
  Pixel UI kit for the student dashboard + course overview.
  Notched corners come from four hard box-shadows (no blur), so the frame reads as pixels.
  Every colour is a token below, redefined for the light theme, so nothing is hard-coded to dark.
*/
:root {
  --px-line: rgba(150,170,255,.40);
  --px-bg: rgba(123,142,220,.09);
  --px-solid: #17143f;
  --px-inner: rgba(255,255,255,.045);
  --px-track: rgba(255,255,255,.12);
  --px-shadow: rgba(0,0,0,.4);
  --px-good: #b6f06a;
}
html[data-theme="light"] {
  --px-line: #c3cfe0;
  --px-bg: #fff;
  --px-solid: #fff;
  --px-inner: #f4f6fb;
  --px-track: #e4e9f2;
  --px-shadow: rgba(20,24,60,.16);
  --px-good: #2f7a05;
}

.px-font { font-family: 'Pixelify Sans', 'Space Grotesk', sans-serif; letter-spacing: .01em; }

/* Card: notched pixel frame */
.px-card {
  position: relative; margin: 4px; background: var(--px-bg);
  -webkit-backdrop-filter: blur(14px); backdrop-filter: blur(14px);
  box-shadow: 0 -4px 0 0 var(--px-line), 0 4px 0 0 var(--px-line), -4px 0 0 0 var(--px-line), 4px 0 0 0 var(--px-line),
              0 18px 30px -18px var(--px-shadow);
}
.px-inner { background: var(--px-inner); }

/* Speech bubble with a tail pointing left at the mascot */
.px-bubble::before {
  content: ""; position: absolute; left: -16px; top: 50%; margin-top: -8px;
  border: 8px solid transparent; border-right: 10px solid var(--px-line); border-left: 0;
}
.px-bubble::after {
  content: ""; position: absolute; left: -10px; top: 50%; margin-top: -6px;
  border: 6px solid transparent; border-right: 8px solid var(--px-solid); border-left: 0;
}

/* Buttons: pressed = 2px down, instantly (feedback on pointer-down) */
.px-btn {
  --bg: var(--px-bg); --edge: var(--px-line); --c: rgb(var(--c-ink));
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  min-height: 46px; padding: 0 22px; margin: 3px; border-radius: 2px;
  font-family: 'Pixelify Sans', 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 600; line-height: 1;
  color: var(--c); background: var(--bg); text-decoration: none; cursor: pointer; white-space: nowrap;
  box-shadow: 0 -3px 0 0 var(--edge), 0 3px 0 0 var(--edge), -3px 0 0 0 var(--edge), 3px 0 0 0 var(--edge),
              inset 0 -4px 0 0 rgba(0,0,0,.2), inset 0 3px 0 0 rgba(255,255,255,.22);
  transition: transform .1s ease, filter .15s ease;
}
.px-btn:hover { filter: brightness(1.08); }
.px-btn:active { transform: translateY(2px); }
.px-btn:focus-visible { outline: 3px solid #9ad0ff; outline-offset: 5px; }
.px-btn-blue   { --bg: #14a0ff; --edge: #0a5fb5; --c: #fff; }
.px-btn-yellow { --bg: #f8c81c; --edge: #a36a00; --c: #1a1200; }
.px-btn-ghost  { --bg: rgba(255,255,255,.1); --edge: rgba(255,255,255,.4); --c: #fff; }
.px-btn-locked { --bg: transparent; --c: rgb(var(--c-muted)); cursor: default; opacity: .75; box-shadow: 0 -3px 0 0 var(--edge), 0 3px 0 0 var(--edge), -3px 0 0 0 var(--edge), 3px 0 0 0 var(--edge); }
.px-btn-locked:hover { filter: none; }
.px-btn-locked:active { transform: none; }

/* Progress bar: chunky track, yellow fill */
.px-bar {
  height: 14px; margin: 2px; overflow: hidden; background: var(--px-track);
  box-shadow: 0 -2px 0 0 var(--px-line), 0 2px 0 0 var(--px-line), -2px 0 0 0 var(--px-line), 2px 0 0 0 var(--px-line);
}
.px-bar > i {
  display: block; height: 100%; background: #f8c81c;
  box-shadow: inset 0 -3px 0 rgba(0,0,0,.18), inset 0 3px 0 rgba(255,255,255,.35);
  transition: width .6s cubic-bezier(.32,.72,0,1);
}

/* Avatar tile (initials) */
.px-avatar {
  display: grid; place-items: center; flex: none; width: 64px; height: 64px; margin: 3px;
  font-family: 'Pixelify Sans', sans-serif; font-size: 26px; font-weight: 700; color: #08051f;
  background: linear-gradient(135deg, #73b6ff, #9b6bff);
  box-shadow: 0 -3px 0 0 #3b2f8f, 0 3px 0 0 #3b2f8f, -3px 0 0 0 #3b2f8f, 3px 0 0 0 #3b2f8f, inset 0 -5px 0 rgba(0,0,0,.18);
}

/* Chapter list: a connector between chapter cards, hairlines between lessons, a green "cleared" pill */
.px-chain::after { content: ""; position: absolute; left: 38px; top: 100%; width: 3px; height: 40px; background: var(--px-line); }
.px-rows > li + li { border-top: 2px solid var(--px-track); }
.px-good { color: var(--px-good); background: color-mix(in srgb, var(--px-good) 16%, transparent); }

/* Text that sits on the dark course art. The light theme rewrites .text-white / border-white to dark, so these are our own. */
.px-onart { color: #fff; }
.px-pill { border: 1px solid rgba(255,255,255,.75); }

/* Course art (used behind the hero on the dashboard and the overview) */
.px-art { position: absolute; inset: 0; z-index: 0; }
.px-art svg { position: absolute; inset: 0; width: 100%; height: 100%; }
.px-scrim { position: absolute; inset: 0; z-index: 1; background: linear-gradient(90deg, rgba(6,6,26,.94) 0%, rgba(6,6,26,.82) 45%, rgba(6,6,26,.3) 72%, transparent 100%); }
@media (max-width: 640px) { .px-scrim { background: rgba(6,6,26,.72); } }
.ps-star { animation: ps-twinkle 3s steps(2, end) infinite; }
@keyframes ps-twinkle { 50% { opacity: .25; } }

@media (prefers-reduced-motion: reduce) {
  .px-btn, .px-bar > i { transition: none; }
  .px-btn:active { transform: none; }
  .ps-star { animation: none; }
}
@media (prefers-reduced-transparency: reduce) {
  .px-card { -webkit-backdrop-filter: none; backdrop-filter: none; background: var(--px-solid); }
}
@media (prefers-contrast: more) { :root { --px-line: rgba(255,255,255,.7); } html[data-theme="light"] { --px-line: #4a5568; } }
