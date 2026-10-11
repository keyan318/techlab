{{--
  TechLab launch screen: shown while the browser waits for the next page.

  Pages are server-rendered and every request goes to a far-away database, so a click can
  sit for a second with nothing happening. This covers that wait: a same-site link click or
  form submit shows it (after a short delay, so fast pages never flash it), and arriving on
  the next page — or coming back with the Back button — hides it.

  The TechLab logo sits in the middle tile, a blue arc spins round the inner ring, a gold planet orbits
  the outer ring, and the words fit where you're heading ("Flying to the planet...").

  Opt out per link/form with data-no-loader; set your own words with data-loader-text="...".
  Included once per page (the shared side-bar, plus the lesson/lab/editor pages that have
  no side-bar); @once keeps a double include harmless.
--}}
@once
<div id="tl-loader" class="tl-loader" role="status" aria-live="polite" aria-hidden="true">
  <div class="tl-loader-stage">
    <div class="tl-loader-orbit" aria-hidden="true"><span class="tl-loader-planet"></span></div>
    <div class="tl-loader-ring" aria-hidden="true"></div>
    <div class="tl-loader-tile" aria-hidden="true">
      <img class="tl-loader-logo" src="{{ asset('apple-touch-icon.png') }}" alt="" width="64" height="64" draggable="false">
    </div>
  </div>
  <p class="tl-loader-text"><span id="tl-loader-label">Launching</span><span class="tl-loader-dots" aria-hidden="true"><i>.</i><i>.</i><i>.</i></span></p>
  <div class="tl-loader-bar" aria-hidden="true"><span></span></div>
</div>

<style>
  .tl-loader {
    --tl-blue: #3aa0ff; --tl-sky: #73b6ff; --tl-gold: #f5c04a;
    --tl-ring: min(168px, 44vw); --tl-orbit: min(300px, 78vw);
    position: fixed; inset: 0; z-index: 9999;
    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 44px;
    background:
      radial-gradient(1px 1px at 12% 18%, rgba(255,255,255,.55) 50%, transparent 51%),
      radial-gradient(1px 1px at 82% 26%, rgba(255,255,255,.45) 50%, transparent 51%),
      radial-gradient(1.5px 1.5px at 68% 78%, rgba(255,255,255,.5) 50%, transparent 51%),
      radial-gradient(1px 1px at 24% 72%, rgba(255,255,255,.35) 50%, transparent 51%),
      radial-gradient(1px 1px at 90% 62%, rgba(255,255,255,.4) 50%, transparent 51%),
      radial-gradient(1px 1px at 40% 10%, rgba(255,255,255,.35) 50%, transparent 51%),
      radial-gradient(55% 45% at 50% 42%, rgba(58,160,255,.12), transparent 70%),
      #04051a;
    opacity: 0; visibility: hidden; pointer-events: none;
    transition: opacity .25s ease, visibility 0s linear .25s;
  }
  .tl-loader.on { opacity: 1; visibility: visible; pointer-events: auto; transition: opacity .25s ease; }

  .tl-loader-stage { position: relative; display: grid; place-items: center; width: var(--tl-orbit); height: var(--tl-orbit); }

  /* Outer orbit: a faint ring with a gold planet travelling round it */
  .tl-loader-orbit { position: absolute; inset: 0; border-radius: 50%; border: 1px solid rgba(115,182,255,.12); animation: tl-spin 7s linear infinite; }
  .tl-loader-planet { position: absolute; top: -6px; left: 50%; width: 12px; height: 12px; margin-left: -6px; border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, #ffe7a3, var(--tl-gold) 55%, #c98a1c);
    box-shadow: 0 0 12px 2px rgba(245,192,74,.55); }

  /* Inner ring: a faint track plus a bright blue arc that spins */
  .tl-loader-ring { position: absolute; width: var(--tl-ring); height: var(--tl-ring); border-radius: 50%;
    border: 1px solid rgba(115,182,255,.16);
    box-shadow: 0 0 40px 4px rgba(58,160,255,.16), inset 0 0 30px rgba(58,160,255,.12); }
  .tl-loader-ring::before { content: ''; position: absolute; inset: -1px; border-radius: 50%;
    background: conic-gradient(from 0deg, transparent 0 55%, rgba(58,160,255,.15) 65%, var(--tl-blue) 88%, var(--tl-sky) 100%);
    -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 2.5px), #000 calc(100% - 2px));
            mask: radial-gradient(farthest-side, transparent calc(100% - 2.5px), #000 calc(100% - 2px));
    filter: drop-shadow(0 0 6px rgba(58,160,255,.8));
    animation: tl-spin 1.4s linear infinite; }

  /* Middle tile holding the TechLab logo */
  .tl-loader-tile { position: relative; display: grid; place-items: center; width: calc(var(--tl-ring) * .66); height: calc(var(--tl-ring) * .66);
    border-radius: 28%; background: linear-gradient(160deg, #15183a, #0d0f29);
    border: 1px solid rgba(255,255,255,.08);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.06), 0 10px 30px -10px rgba(0,0,0,.8); }
  .tl-loader-logo { width: 52%; height: auto; user-select: none; animation: tl-glow 2.4s ease-in-out infinite; }

  .tl-loader-text { margin: 0; max-width: 90vw; text-align: center;
    font: 700 13px/1.4 'Space Grotesk', system-ui, sans-serif; letter-spacing: .38em; text-transform: uppercase;
    color: var(--tl-sky); text-shadow: 0 0 14px rgba(58,160,255,.45); }
  .tl-loader-dots i { font-style: normal; animation: tl-dot 1.2s ease-in-out infinite; }
  .tl-loader-dots i:nth-child(2) { animation-delay: .2s; }
  .tl-loader-dots i:nth-child(3) { animation-delay: .4s; }

  /* Fuel bar: a blue-to-gold streak sliding along a thin track */
  .tl-loader-bar { position: relative; width: min(300px, 70vw); height: 3px; margin-top: -24px; overflow: hidden; border-radius: 3px; background: rgba(255,255,255,.07); }
  .tl-loader-bar span { position: absolute; top: 0; bottom: 0; width: 40%; border-radius: 3px;
    background: linear-gradient(90deg, transparent, var(--tl-blue), var(--tl-gold));
    animation: tl-fuel 1.6s cubic-bezier(.45,0,.55,1) infinite; }

  @keyframes tl-spin { to { transform: rotate(360deg); } }
  @keyframes tl-glow { 0%, 100% { filter: drop-shadow(0 0 6px rgba(115,182,255,.35)); } 50% { filter: drop-shadow(0 0 14px rgba(115,182,255,.75)); } }
  @keyframes tl-dot  { 0%, 100% { opacity: .2; } 40% { opacity: 1; } }
  @keyframes tl-fuel { from { left: -40%; } to { left: 100%; } }

  /* Reduced motion: nothing spins or travels — the screen just fades in, the dots breathe. */
  @media (prefers-reduced-motion: reduce) {
    .tl-loader-orbit, .tl-loader-ring::before, .tl-loader-logo { animation: none; }
    .tl-loader-bar span { animation: tl-dot 1.6s ease-in-out infinite; left: 30%; }
  }
</style>

<script>
  (function () {
    var el = document.getElementById('tl-loader');
    var label = document.getElementById('tl-loader-label');
    var showTimer = null, safetyTimer = null;

    // The everyday places — Home, Chat, Planets (and the faculty/admin Home and Chat) — open
    // without the launch screen; it's kept for logging in and heading into courses and lessons.
    var NO_LOADER = /^\/((faculty|admin)\/dashboard|dashboard|(faculty\/)?chat|planets)\/?$/;

    // Words for where you're heading. First match wins.
    var DESTINATIONS = [
      [/\/lab\//,                                    'Booting the simulator'],
      [/\/editor$/,                                  'Starting the code editor'],
      [/\/certificate$/,                             'Printing your certificate'],
      [/\/quiz/,                                     'Loading your quiz'],
      [/^\/student\/(course\/|planet\/[^/]+\/course\/)/, 'Docking at your course'],
      [/^\/student\/planet\/[^/]+\/[^/]+\/[^/]+|^\/python\//, 'Opening your lesson'],
      [/^\/(planets|student\/planet)/,               'Flying to the planet'],
      [/^\/progress/,                                'Counting your XP'],
      [/^\/faculty/,                                 'Opening the bridge'],
    ];
    function wordsFor(path) {
      for (var i = 0; i < DESTINATIONS.length; i++) if (DESTINATIONS[i][0].test(path)) return DESTINATIONS[i][1];
      return 'Launching';
    }

    function show(text) {
      if (showTimer) return;
      label.textContent = text;
      // Fast pages arrive before this fires, so they never flash the screen.
      showTimer = setTimeout(function () { el.classList.add('on'); el.setAttribute('aria-hidden', 'false'); }, 180);
      // A link that turns out to be a download never leaves the page; don't trap the student.
      safetyTimer = setTimeout(hide, 15000);
    }
    function hide() {
      clearTimeout(showTimer); clearTimeout(safetyTimer); showTimer = null;
      el.classList.remove('on'); el.setAttribute('aria-hidden', 'true');
    }

    document.addEventListener('click', function (e) {
      if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
      var a = e.target.closest && e.target.closest('a[href]');
      if (!a || a.hasAttribute('download') || a.hasAttribute('data-no-loader')) return;
      if (a.target && a.target !== '_self') return;
      var url = new URL(a.href, location.href);
      if (url.origin !== location.origin) return;
      if (url.pathname === location.pathname && url.search === location.search) return; // #anchor / same page
      if (NO_LOADER.test(url.pathname)) return;
      if (/^\/materials\//.test(url.pathname) || /\.(pdf|zip|docx?|pptx?|xlsx?|png|jpe?g|gif|svg|mp4)$/i.test(url.pathname)) return;
      show(a.dataset.loaderText || wordsFor(url.pathname));
    });

    document.addEventListener('submit', function (e) {
      var f = e.target;
      if (e.defaultPrevented || f.hasAttribute('data-no-loader')) return; // JS-handled forms stay put
      if (f.target && f.target !== '_self') return;
      show(f.dataset.loaderText || 'Launching');
    });

    // Back/forward can restore this page from memory with the screen still up.
    window.addEventListener('pageshow', hide);
  })();
</script>
@endonce
