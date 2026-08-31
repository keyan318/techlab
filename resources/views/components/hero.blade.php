{{--
  Codexia hero — drop-in component.
  Usage:  @include('components.hero')
  Self-contained: carries its own scoped <style> (Codexia tokens + @keyframes)
  and loads Alpine via CDN for the portal-flare interaction.
  NOTE: if the parent page later loads Alpine through the Vite build, remove the
  <script defer ... alpinejs ...> line at the bottom to avoid double-loading.
  All classes are prefixed `cx-` to avoid clashing with the host page.
--}}
<section class="cx-hero" x-data="{ launched: false }" aria-label="Codexia hero">

  <style>
    .cx-hero {
      /* Codexia tokens — scoped here so they never collide with the host page's vars */
      --void:    #08071a;
      --cosmic:  #141329;
      --violet:  #9b6bff;
      --blue:    #73b6ff;
      --cyan:    #5be1ff;
      --coral:   #ff8f6b;
      --text:    #EAEDFF;
      --muted:   rgba(234, 237, 255, 0.66);
      --ease:    cubic-bezier(0.23, 1, 0.32, 1);

      position: relative;
      overflow: hidden;
      isolation: isolate;
      background:
        radial-gradient(120% 80% at 50% -10%, var(--cosmic) 0%, var(--void) 58%);
      color: var(--text);
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      padding: clamp(72px, 12vh, 128px) clamp(20px, 5vw, 64px) clamp(56px, 9vh, 104px);
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      min-height: 88vh;
    }
    /* Blend the dark portal scene into the page's light background below */
    .cx-hero::after {
      content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 120px; z-index: 6;
      background: linear-gradient(to bottom, rgba(251, 252, 254, 0), #fbfcfe);
      pointer-events: none;
    }

    /* ---------- Starfield (ambient, twinkling) ---------- */
    .cx-starfield {
      position: absolute; inset: 0; z-index: 0; pointer-events: none;
      background-image:
        radial-gradient(1px 1px at 12% 18%, rgba(255,255,255,.85), transparent),
        radial-gradient(1px 1px at 28% 62%, rgba(255,255,255,.6), transparent),
        radial-gradient(1.5px 1.5px at 44% 12%, rgba(123,182,255,.9), transparent),
        radial-gradient(1px 1px at 63% 30%, rgba(255,255,255,.7), transparent),
        radial-gradient(1px 1px at 78% 70%, rgba(255,255,255,.6), transparent),
        radial-gradient(1.5px 1.5px at 88% 22%, rgba(91,225,255,.8), transparent),
        radial-gradient(1px 1px at 18% 84%, rgba(255,255,255,.6), transparent),
        radial-gradient(1px 1px at 52% 80%, rgba(255,255,255,.7), transparent),
        radial-gradient(1.5px 1.5px at 70% 48%, rgba(155,107,255,.7), transparent),
        radial-gradient(1px 1px at 36% 40%, rgba(255,255,255,.5), transparent),
        radial-gradient(1px 1px at 92% 58%, rgba(255,255,255,.6), transparent),
        radial-gradient(1.5px 1.5px at 8% 50%, rgba(123,182,255,.7), transparent);
      animation: cx-twinkle 5.5s ease-in-out infinite;
    }

    /* ---------- Portal glow + flare ---------- */
    .cx-portal-wrap {
      position: relative;
      width: min(520px, 86vw);
      margin: clamp(28px, 5vh, 56px) auto 0;
      aspect-ratio: 4 / 3;
    }
    .cx-glow {
      position: absolute; left: 50%; top: 50%;
      width: 150%; aspect-ratio: 1; z-index: 0;
      transform: translate(-50%, -50%);
      background: radial-gradient(closest-side,
        rgba(155,107,255,.55) 0%,
        rgba(91,225,255,.32) 42%,
        rgba(123,182,255,0) 72%);
      filter: blur(18px);
      animation: cx-pulse 6s ease-in-out infinite;
      pointer-events: none;
    }
    /* CTA-triggered flare — a brighter halo that blooms when "Start your mission" is focused/hovered */
    .cx-flare-glow {
      position: absolute; left: 50%; top: 50%;
      width: 165%; aspect-ratio: 1; z-index: 1;
      transform: translate(-50%, -50%) scale(1);
      opacity: 0;
      background: radial-gradient(closest-side,
        rgba(255,255,255,.55) 0%,
        rgba(155,107,255,.5) 30%,
        rgba(91,225,255,.28) 55%,
        rgba(123,182,255,0) 74%);
      filter: blur(14px);
      transition: opacity .5s var(--ease), transform .5s var(--ease);
      pointer-events: none;
    }
    .cx-flare-glow.cx-flare { opacity: 1; transform: translate(-50%, -50%) scale(1.12); }

    /* Ping the fox-alien, orbiting the portal */
    .cx-orbit {
      position: absolute; left: 50%; top: 50%;
      width: 118%; aspect-ratio: 1; z-index: 2;
      transform: translate(-50%, -50%);
      border: 1px dashed rgba(123, 182, 255, 0.22);
      border-radius: 50%;
      animation: cx-orbit 24s linear infinite;
      pointer-events: none;
    }
    .cx-orbit-dot {
      position: absolute; top: -5px; left: 50%; margin-left: -5px;
      width: 10px; height: 10px; border-radius: 50%;
      background: radial-gradient(circle at 40% 35%, #fff, var(--cyan));
      box-shadow: 0 0 12px var(--cyan);
    }

    .cx-portal {
      position: relative; z-index: 3;
      width: 100%; height: 100%; object-fit: contain;
      filter: drop-shadow(0 24px 60px rgba(8, 7, 26, 0.6));
      animation: cx-float 7s ease-in-out infinite;
      will-change: transform;
    }

    /* ---------- Copy ---------- */
    .cx-inner { position: relative; z-index: 4; max-width: 760px; }
    .cx-eyebrow {
      font-size: .8rem; font-weight: 600; letter-spacing: .22em;
      text-transform: uppercase; color: var(--cyan);
      margin-bottom: 18px;
    }
    .cx-title {
      font-family: 'Space Grotesk', system-ui, -apple-system, sans-serif;
      font-weight: 700; letter-spacing: -0.02em; line-height: 1.05;
      font-size: clamp(2.2rem, 6vw, 4rem);
      margin: 0;
    }
    .cx-grad {
      background: linear-gradient(120deg, var(--violet), var(--cyan));
      -webkit-background-clip: text; background-clip: text; color: transparent;
    }
    .cx-sub {
      color: var(--muted); font-size: clamp(1rem, 2vw, 1.18rem);
      line-height: 1.65; max-width: 60ch; margin: 20px auto 0;
    }

    /* ---------- Planet chips (structure encodes the 3 real worlds) ---------- */
    .cx-planets {
      display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;
      margin-top: 26px;
    }
    .cx-chip {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 7px 14px; border-radius: 999px;
      border: 1px solid rgba(255, 255, 255, 0.14);
      background: rgba(255, 255, 255, 0.03);
      font-size: .85rem; color: var(--muted);
      transition: border-color .2s var(--ease), color .2s var(--ease), background .2s var(--ease);
    }
    @media (hover: hover) and (pointer: fine) {
      .cx-chip:hover { border-color: var(--cyan); color: var(--text); background: rgba(91,225,255,.06); }
    }
    .cx-dot { width: 9px; height: 9px; border-radius: 50%; flex: 0 0 auto; }
    .cx-dot--prog  { background: var(--blue);  box-shadow: 0 0 8px var(--blue); }
    .cx-dot--net   { background: var(--cyan);  box-shadow: 0 0 8px var(--cyan); }
    .cx-dot--cyber { background: var(--coral); box-shadow: 0 0 8px var(--coral); }

    /* ---------- CTAs ---------- */
    .cx-cta {
      display: flex; gap: 14px; flex-wrap: wrap; justify-content: center;
      margin-top: 30px;
    }
    .cx-btn {
      font-family: 'Space Grotesk', system-ui, -apple-system, sans-serif;
      font-weight: 600; font-size: 1rem;
      padding: 14px 26px; border-radius: 999px; cursor: pointer; border: 1px solid transparent;
      display: inline-flex; align-items: center; gap: 10px;
      text-decoration: none;
      transition: transform .15s var(--ease), box-shadow .25s var(--ease), background .25s var(--ease), border-color .25s var(--ease);
      will-change: transform;
    }
    .cx-btn-primary {
      color: #fff;
      background: linear-gradient(120deg, var(--violet), var(--blue));
      box-shadow: 0 10px 30px rgba(155,107,255,.35), inset 0 1px 0 rgba(255,255,255,.3);
    }
    @media (hover: hover) and (pointer: fine) {
      .cx-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 16px 40px rgba(155,107,255,.5), inset 0 1px 0 rgba(255,255,255,.35); }
    }
    .cx-btn-ghost {
      color: var(--text); background: rgba(255,255,255,.04);
      border-color: rgba(255,255,255,.18);
    }
    @media (hover: hover) and (pointer: fine) {
      .cx-btn-ghost:hover { background: rgba(255,255,255,.09); border-color: rgba(255,255,255,.32); transform: translateY(-2px); }
    }
    .cx-btn:active { transform: scale(.96); }
    .cx-btn:focus-visible { outline: 2px solid var(--cyan); outline-offset: 3px; }
    .cx-arrow { transition: transform .25s var(--ease); }
    @media (hover: hover) and (pointer: fine) {
      .cx-btn-primary:hover .cx-arrow { transform: translateX(4px); }
    }

    /* ---------- Entrance (staggered rise on load) ---------- */
    .cx-anim { animation: cx-rise .7s var(--ease) both; animation-delay: var(--d, 0s); }

    /* ---------- Keyframes ---------- */
    @keyframes cx-pulse {
      0%, 100% { opacity: .55; transform: translate(-50%, -50%) scale(1); }
      50%      { opacity: .9;  transform: translate(-50%, -50%) scale(1.08); }
    }
    @keyframes cx-float {
      0%, 100% { transform: translateY(0); }
      50%      { transform: translateY(-14px); }
    }
    @keyframes cx-orbit { to { transform: translate(-50%, -50%) rotate(360deg); } }
    @keyframes cx-twinkle { 0%, 100% { opacity: .55; } 50% { opacity: 1; } }
    @keyframes cx-rise {
      from { opacity: 0; transform: translateY(22px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ---------- Reduced motion: keep the scene, drop the movement ---------- */
    @media (prefers-reduced-motion: reduce) {
      .cx-starfield, .cx-glow, .cx-orbit, .cx-portal { animation: none !important; }
      .cx-anim { animation: none !important; opacity: 1 !important; transform: none !important; }
      .cx-flare-glow { display: none; } /* no flare flash for reduced-motion users */
    }
  </style>

  <div class="cx-starfield" aria-hidden="true"></div>

  <div class="cx-inner">
    <p class="cx-eyebrow cx-anim" style="--d:0s">Codexia Universe</p>

    <h1 class="cx-title cx-anim" style="--d:.1s">
      Your mission begins in <span class="cx-grad">Codexia</span>.
    </h1>

    <p class="cx-sub cx-anim" style="--d:.22s">
      Astro, your robot-astronaut guide, leads you through three worlds —
      Programming, Networking, and Cybersecurity — with Ping the fox-alien at
      your side and Glitchlings to outwit.
    </p>

    <div class="cx-planets cx-anim" style="--d:.34s">
      <span class="cx-chip"><i class="cx-dot cx-dot--prog"></i>Programming</span>
      <span class="cx-chip"><i class="cx-dot cx-dot--net"></i>Networking</span>
      <span class="cx-chip"><i class="cx-dot cx-dot--cyber"></i>Cybersecurity</span>
    </div>
  </div>

  <div class="cx-portal-wrap cx-anim" style="--d:.46s">
    <div class="cx-glow" aria-hidden="true"></div>
    {{-- Flare blooms when the primary CTA is hovered or focused (Alpine state) --}}
    <div class="cx-flare-glow" :class="launched ? 'cx-flare' : ''" aria-hidden="true"></div>
    <div class="cx-orbit" aria-hidden="true"><span class="cx-orbit-dot"></span></div>
    <img
      class="cx-portal"
      src="{{ asset('portal-hero.jpg') }}"
      alt="A figure steps into a glowing portal in the Codexia universe, surrounded by bioluminescent alien flora and creatures."
      width="1000" height="750" />
  </div>

  <div class="cx-cta cx-anim" style="--d:.56s">
    <a class="cx-btn cx-btn-primary"
       href="/register"
       @mouseenter="launched = true" @mouseleave="launched = false"
       @focus="launched = true" @blur="launched = false">
      Start your mission <span class="cx-arrow">→</span>
    </a>
    {{-- Point this at your planets overview/section when it exists (e.g. #planets or /planets/programming). --}}
    <a class="cx-btn cx-btn-ghost" href="#planets">See the planets</a>
  </div>

  {{-- Alpine (CDN) — remove this line if the host page starts loading Alpine via Vite. --}}
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
</section>
