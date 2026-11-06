{{-- Astro's daily streak pop-up on the home page (StudentController::streakPop decides when).
     A pixel flame burns on a tiny canvas, scaled up with crisp pixels; Astro jumps for joy next to it
     (the .astro-cheer mood in <x-pixel-mascot>). Every 10 days the flame evolves to the next of
     StudentDashboardService::STREAK_TIERS: orange → red → deep red → magenta → violet → blue. On the day
     it evolves, the old flame flashes white and comes back as the new one.

     $card['reward'] is today's daily login XP (StudentDashboardService::DAILY_LOGIN_XP): a gold "+10 XP" badge pops in.
     The card never scrolls: on short screens the flame moves beside the text, and if it still doesn't fit it shrinks to fit.

     $card is StudentDashboardService::streakCard(). Continue, ✕, Escape or the backdrop closes it. --}}
@props(['card'])
@php
  $tiers = $card['tiers'];
  $tier = $card['tier'];
  $count = $card['count'];
  $name = $tiers[$tier]['name'];
  $c = $tiers[$tier]['colors'];
  $startTier = $card['evolved'] ? $tier - 1 : $tier;
  $sc = $tiers[$startTier]['colors'];
  $message = $card['evolved'] ? "Your flame evolved into {$name}! Keep it burning, astronaut!"
    : ($card['todayDone'] ? "You're on fire, astronaut! Come back tomorrow to keep it going."
      : 'Finish a lesson today to make it '.($count + 1).' '.\Illuminate\Support\Str::plural('day', $count + 1).'!');
  $cheers = $card['todayDone'] ? ["{$count} days! Whoa!", "Let's goooo!", 'Hot streak!', 'Woohoo!'] : ['Keep it alive!', "Let's go!", 'You got this!'];
  $kicker = fn ($t) => $tiers[$t]['name'].' flame';
  $js = ['count' => $count, 'tier' => $tier, 'start' => $startTier, 'evolved' => $card['evolved'], 'bump' => $card['todayDone'], 'kicker' => $kicker($tier), 'reward' => $card['reward']];
  $palettes = array_column($tiers, 'colors');
@endphp
<div class="spop" id="streak-pop" hidden
     style="--f0: {{ $sc[0] }}; --f1: {{ $sc[1] }}; --f2: {{ $sc[2] }}; --f3: {{ $sc[3] }}; --f4: {{ $sc[4] }};">
  <div class="spop-backdrop" data-spop-close></div>
  <div class="spop-card" role="dialog" aria-modal="true" aria-labelledby="spop-title" aria-describedby="spop-msg">
    <button type="button" class="spop-close" data-spop-close aria-label="Close">✕</button>

    <div class="spop-stage" aria-hidden="true">
      <div class="spop-glow"></div>
      <canvas class="spop-flame" width="28" height="34"></canvas>
      <div class="spop-flash"></div>
      <div class="spop-astro astro-cheer">
        <span class="spop-bubble px-font">{{ $cheers[array_rand($cheers)] }}</span>
        <x-astro-mascot />
      </div>
    </div>

    <div class="spop-main">

    <p class="spop-kicker px-font" data-spop-kicker>{{ $card['evolved'] ? 'Your flame is evolving…' : $kicker($tier) }}</p>
    <p class="spop-num px-font" data-spop-num>{{ $card['todayDone'] ? max(0, $count - 1) : $count }}</p>
    <p class="spop-title px-font" id="spop-title"><span class="sr-only">{{ $count }} </span>Day streak</p>
    <p class="spop-msg" id="spop-msg">{{ $message }}</p>
    @if ($card['reward'] > 0)
      <p class="spop-reward"><span class="spop-reward-xp px-font">+{{ $card['reward'] }} XP</span> Daily login bonus</p>
    @endif

    {{-- The flames to come: lit up to the current one, the rest still locked. --}}
    <ol class="spop-ladder" aria-label="Streak flames">
      @foreach ($tiers as $i => $t)
        <li @class(['spop-rung', 'is-on' => $i <= $startTier, 'is-now' => $i === $startTier]) data-spop-rung="{{ $i }}"
            title="{{ $t['name'] }}: {{ $i * \App\Services\StudentDashboardService::STREAK_DAYS_PER_TIER ?: 1 }}+ days">
          <x-px-icon name="flame" :size="22" :tier="$i" />
          <span class="sr-only">{{ $t['name'] }}{{ $i <= $tier ? ', unlocked' : ', locked' }}</span>
        </li>
      @endforeach
    </ol>
    <p class="spop-next">
      @if ($card['nextName'])
        <b>{{ $card['nextName'] }}</b> flame in {{ $card['nextIn'] }} {{ \Illuminate\Support\Str::plural('day', $card['nextIn']) }}
      @else
        Top flame reached. You're a <b>Nova</b>, astronaut!
      @endif
    </p>

    <ol class="spop-week" aria-label="This week">
      @foreach ($card['week'] as $d)
        <li @class(['spop-day px-font', 'is-done' => $d['done'], 'is-today' => $d['today'], 'is-future' => $d['future']])
            @if ($d['today']) data-spop-today @endif>
          @if ($d['done'])<x-px-icon name="check" :size="14" />@else{{ $d['label'] }}@endif
          <span class="sr-only">{{ $d['name'] }}: {{ $d['done'] ? 'studied' : ($d['future'] ? 'still to come' : ($d['today'] ? 'not yet' : 'missed')) }}</span>
        </li>
      @endforeach
    </ol>
    <p class="spop-longest">Longest streak: <b>{{ $card['longest'] }} {{ \Illuminate\Support\Str::plural('day', $card['longest']) }}</b></p>

    <button type="button" class="spop-go px-font" data-spop-close>Continue</button>
    </div>
  </div>
</div>
<style>
  .spop { position: fixed; inset: 0; z-index: 9000; overflow: hidden; display: grid; place-items: center; padding: 16px; font-family: 'Inter', system-ui, sans-serif; }
  .spop[hidden] { display: none; }
  .spop-backdrop { position: absolute; inset: 0; background: rgba(4, 6, 24, .8); -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px); animation: spop-fade .3s ease both; }
  .spop.leaving .spop-backdrop { animation: spop-fade .2s ease-in reverse both; }
  .spop-card {
    position: relative; width: min(380px, 100%);
    padding: 14px 18px 16px; text-align: center; color: #eaf0ff;
    border-radius: 14px;
    background: linear-gradient(180deg, color-mix(in srgb, var(--f0) 55%, #0c1436) 0%, #0c1436 46%, #090f2b 100%);
    box-shadow: 0 18px 50px rgba(0, 0, 0, .55), 0 0 60px color-mix(in srgb, var(--f2) 30%, transparent);
    animation: spop-in .55s cubic-bezier(.3, 1.5, .5, 1) both;
    transition: box-shadow .3s;
  }
  .spop.leaving .spop-card { animation: spop-out .2s ease-in both; }
  .spop-close { position: absolute; top: 8px; right: 10px; z-index: 2; border: 0; background: none; color: #9aa6d6; font-size: 14px; cursor: pointer; padding: 4px; }
  .spop-close:hover { color: #fff; }

  .spop-stage { position: relative; height: 150px; display: grid; place-items: end center; }
  .spop-glow {
    position: absolute; left: 50%; bottom: 6px; width: 180px; height: 180px; translate: -50% 0;
    background: radial-gradient(circle, color-mix(in srgb, var(--f2) 55%, transparent) 0%, transparent 65%);
    animation: spop-glow 1.6s steps(4) infinite;
  }
  .spop-flame { position: relative; width: 112px; height: 136px; image-rendering: pixelated; image-rendering: crisp-edges; }
  .spop-flash { position: absolute; inset: 0; background: radial-gradient(circle at 50% 60%, #fff 0%, rgba(255, 255, 255, .7) 30%, transparent 62%); opacity: 0; pointer-events: none; }
  .spop.evolving .spop-flash { animation: spop-flash .7s steps(5) both; }
  .spop.evolving .spop-flame { animation: spop-pop .7s steps(6) both; }
  .spop-astro { position: absolute; left: 4px; bottom: 2px; width: 76px; display: grid; justify-items: center; }
  .spop-astro svg { width: 60px; height: auto; image-rendering: pixelated; }
  .spop-bubble {
    position: relative; margin-bottom: 7px; padding: 3px 7px; white-space: nowrap;
    background: #fff; color: #14112e; font-size: 11px; font-weight: 700;
    box-shadow: 0 -2px 0 #fff, 0 2px 0 #fff, -2px 0 0 #fff, 2px 0 0 #fff, 4px 4px 0 rgba(0, 0, 0, .35);
    animation: spop-bob 1.2s steps(2) infinite;
  }
  .spop-bubble::after { content: ''; position: absolute; left: 50%; bottom: -8px; width: 6px; height: 6px; translate: -50% 0; background: #fff; box-shadow: 0 2px 0 #fff; }

  .spop-kicker { margin: 2px 0 0; font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--f3); transition: color .3s; }
  .spop-num { margin: 0; font-size: 60px; font-weight: 700; line-height: 1; color: #fff; text-shadow: 3px 3px 0 var(--f1), 6px 6px 0 rgba(0, 0, 0, .35); transition: text-shadow .3s; }
  .spop-num.bump { animation: spop-bump .5s steps(5) both; }
  .spop-title { margin: 2px 0 0; font-size: 22px; font-weight: 700; }
  .spop-msg { margin: 4px auto 0; max-width: 34ch; font-size: 14px; line-height: 1.4; color: #b8c2ea; }

  .spop-reward { display: inline-flex; align-items: center; gap: 8px; margin: 10px 0 0; padding: 4px 12px 4px 4px; border-radius: 999px; font-size: 13px; font-weight: 600; color: #ffe7a3; background: rgba(245, 192, 74, .14); animation: spop-reward .5s .9s cubic-bezier(.3, 1.6, .5, 1) both; }
  .spop-reward-xp { padding: 3px 9px; border-radius: 999px; font-size: 13px; font-weight: 700; color: #3a2400; background: #f5c04a; box-shadow: inset 0 -2px 0 #c98a17; }

  .spop-ladder { display: flex; justify-content: center; gap: 5px; margin: 12px 0 0; padding: 0; list-style: none; }
  .spop-rung { display: grid; place-items: center; width: 30px; height: 30px; border-radius: 6px; background: rgba(255, 255, 255, .06); filter: grayscale(1) brightness(.45); transition: filter .3s, background .3s; }
  .spop-rung.is-on { filter: none; }
  .spop-rung.is-now { background: color-mix(in srgb, var(--f2) 30%, transparent); animation: spop-bob 1s steps(2) infinite; }
  .spop-next { margin: 6px 0 0; font-size: 12px; color: #9aa6d6; }
  .spop-next b { color: #fff; }

  .spop-week { display: flex; justify-content: center; gap: 5px; margin: 12px 0 0; padding: 0; list-style: none; }
  .spop-day { display: grid; place-items: center; width: 32px; height: 32px; border-radius: 6px; font-size: 13px; font-weight: 700; color: #9aa6d6; background: #1a2350; box-shadow: inset 0 -3px 0 rgba(0, 0, 0, .3); }
  .spop-day.is-done { background: var(--f2); color: #fff; box-shadow: inset 0 -3px 0 var(--f0); transition: background .3s; }
  .spop-day.is-today:not(.is-done) { color: #fff; outline: 2px solid var(--f2); outline-offset: 2px; animation: spop-blink 1s steps(2) infinite; }
  .spop-day.is-today.is-done { outline: 2px solid color-mix(in srgb, var(--f2) 45%, transparent); outline-offset: 2px; }
  .spop-day.is-future { opacity: .45; }
  .spop-longest { margin: 8px 0 0; font-size: 12px; color: #9aa6d6; }
  .spop-longest b { color: #fff; }

  .spop-go {
    display: block; width: 100%; margin-top: 14px; padding: 10px; border: 0; border-radius: 10px; cursor: pointer;
    background: var(--f2); color: #fff; font-size: 17px; font-weight: 700; text-shadow: 2px 2px 0 var(--f0);
    box-shadow: inset 0 -4px 0 var(--f1); transition: background .3s;
  }
  .spop-go:hover { background: var(--f3); }
  .spop-go:active { box-shadow: inset 0 -1px 0 var(--f1); transform: translateY(2px); }
  .spop-go:focus-visible, .spop-close:focus-visible { outline: 3px solid #f5c04a; outline-offset: 3px; }

  /* Short, wide screens: the flame sits beside the text instead of on top of it. */
  @media (max-height: 640px) and (min-width: 620px) {
    .spop-card { width: min(640px, 100%); display: grid; grid-template-columns: 220px 1fr; align-items: center; gap: 8px; }
    .spop-stage { height: 220px; }
    .spop-flame { width: 140px; height: 170px; }
    .spop-astro { left: -6px; }
  }

  @keyframes spop-in { 0% { opacity: 0; transform: translateY(30px) scale(.8) } 100% { opacity: 1; transform: none } }
  @keyframes spop-out { to { opacity: 0; transform: translateY(24px) scale(.95) } }
  @keyframes spop-fade { from { opacity: 0 } to { opacity: 1 } }
  @keyframes spop-glow { 0%, 100% { opacity: .75; scale: 1 } 50% { opacity: 1; scale: 1.08 } }
  @keyframes spop-flash { 0% { opacity: 0 } 40% { opacity: 1 } 100% { opacity: 0 } }
  @keyframes spop-pop { 0% { scale: 1 } 40% { scale: 1.25 } 70% { scale: .92 } 100% { scale: 1 } }
  @keyframes spop-bump { 0% { scale: 1 } 35% { scale: 1.35; translate: 0 -6px } 100% { scale: 1 } }
  @keyframes spop-bob { 50% { translate: 0 -3px } }
  @keyframes spop-blink { 50% { outline-color: transparent } }
  @keyframes spop-reward { 0% { opacity: 0; scale: .5 } 100% { opacity: 1; scale: 1 } }
  @media (max-width: 380px) {
    .spop-card { padding-inline: 12px; }
    .spop-day { width: 30px; height: 30px; } .spop-rung { width: 28px; height: 28px; } .spop-week, .spop-ladder { gap: 4px; }
  }
  @media (prefers-reduced-motion: reduce) {
    .spop-card, .spop.leaving .spop-card, .spop-backdrop, .spop-glow, .spop-bubble, .spop-rung.is-now, .spop-day.is-today:not(.is-done), .spop-reward { animation: none; }
  }
</style>
<script>
  (function () {
    var root = document.getElementById('streak-pop');
    if (!root) return;
    var CARD = @json($js);
    var TIERS = @json($palettes);
    var still = matchMedia('(prefers-reduced-motion: reduce)').matches;
    var canvas = root.querySelector('.spop-flame');
    var ctx = canvas.getContext('2d');
    var W = canvas.width, H = canvas.height;
    var pal = TIERS[CARD.start];
    var frame = 0, embers = [], timer = null, timeouts = [], returnFocus = null;

    // The flame's layers, outside in: base, mid, light, core. Each is a teardrop: a pointy tip
    // that widens to its fullest at 70% down, then a round bottom. Inner layers start lower.
    var LAYERS = [
      { top: 4, bot: 33, half: 11 },
      { top: 11, bot: 32, half: 7.6 },
      { top: 18, bot: 31, half: 4.8 },
      { top: 23, bot: 30, half: 2.6 },
    ];

    function drawFlame(f) {
      var grid = [];
      for (var y = 0; y < H; y++) {
        grid.push(new Array(W).fill(-1));
        for (var k = 0; k < LAYERS.length; k++) {
          var L = LAYERS[k];
          if (y < L.top || y > L.bot) continue;
          var t = (y - L.top) / (L.bot - L.top);
          var shape = t < 0.7 ? Math.pow(t / 0.7, 0.72) : Math.sqrt(1 - Math.pow((t - 0.7) / 0.3, 2));
          // The top flickers and sways more than the bottom; whole-pixel steps keep it looking hand-drawn.
          var half = L.half * shape * (1 + 0.1 * Math.sin(f * 1.9 + y * 0.8 + k));
          var cx = W / 2 + (1 - t) * (1 - t) * (2.4 * Math.sin(f * 0.85 + k * 0.5) + 0.8 * Math.sin(f * 2.3 + y * 0.4));
          for (var x = 0; x < W; x++) if (Math.abs(x + 0.5 - cx) < half) grid[y][x] = k;
        }
      }
      // A loose tongue of fire jumps off the tip now and then.
      if (f % 5 < 3) {
        var tx = Math.round(W / 2 + 2.6 * Math.sin(f * 0.85)), ty = 1 + (f % 5);
        if (grid[ty]) { grid[ty][tx] = 0; if (grid[ty + 1]) grid[ty + 1][tx] = 0; }
      }
      ctx.clearRect(0, 0, W, H);
      for (y = 0; y < H; y++) for (x = 0; x < W; x++) {
        var v = grid[y][x];
        if (v < 0) continue;
        // A base pixel next to empty space is the dark outline.
        var edge = v === 0 && (x === 0 || x === W - 1 || y === 0 || y === H - 1 ||
          grid[y - 1][x] < 0 || grid[y + 1][x] < 0 || grid[y][x - 1] < 0 || grid[y][x + 1] < 0);
        ctx.fillStyle = edge ? pal[0] : pal[v + 1];
        ctx.fillRect(x, y, 1, 1);
      }
      embers.forEach(function (e) {
        ctx.fillStyle = e.life > 4 ? pal[e.c] : pal[1];
        ctx.fillRect(Math.round(e.x), Math.round(e.y), 1, 1);
      });
    }

    function spawn(n, spread) {
      for (var i = 0; i < n; i++) embers.push({
        x: W / 2 + (Math.random() - 0.5) * (spread || 10), y: 10 + Math.random() * 10,
        vx: (Math.random() - 0.5) * 0.6, vy: -0.6 - Math.random() * 0.8, life: 8 + Math.round(Math.random() * 10), c: Math.random() < 0.5 ? 3 : 4,
      });
    }

    function tick() {
      frame++;
      if (Math.random() < 0.45) spawn(1);
      embers = embers.filter(function (e) { e.x += e.vx; e.y += e.vy; return --e.life > 0 && e.y > -1; });
      drawFlame(frame);
    }

    function setTier(i) {
      pal = TIERS[i];
      ['--f0', '--f1', '--f2', '--f3', '--f4'].forEach(function (v, n) { root.style.setProperty(v, pal[n]); });
      root.querySelectorAll('[data-spop-rung]').forEach(function (r) {
        var n = +r.dataset.spopRung;
        r.classList.toggle('is-on', n <= i);
        r.classList.toggle('is-now', n === i);
      });
    }

    // Never scroll: if the card is taller than the screen, shrink the whole card until it fits.
    var card = root.querySelector('.spop-card');
    function fit() {
      if (root.hidden) return;
      card.style.zoom = '';
      var room = window.innerHeight - 32, tall = card.offsetHeight;
      if (tall > room) card.style.zoom = String(Math.max(0.5, room / tall));
    }
    window.addEventListener('resize', fit);

    function sfx(name) { try { window.techlabSfx && window.techlabSfx.play(name); } catch (e) {} }
    function later(fn, ms) { timeouts.push(setTimeout(fn, ms)); }

    function show() {
      returnFocus = document.activeElement;
      root.hidden = false;
      fit();
      root.querySelector('.spop-go').focus();
      var num = root.querySelector('[data-spop-num]');
      if (still) {
        setTier(CARD.tier);
        num.textContent = CARD.count;
        root.querySelector('[data-spop-kicker]').textContent = CARD.kicker;
        drawFlame(0);
        return;
      }
      timer = setInterval(tick, 110);
      sfx('win');
      // Today counted, so the number ticks up from yesterday's.
      if (CARD.bump) later(function () { num.textContent = CARD.count; num.classList.add('bump'); }, 650);
      if (CARD.reward) later(function () { sfx('coin'); }, 950);
      if (CARD.evolved) {
        later(function () { root.classList.add('evolving'); }, 1300);
        later(function () {
          setTier(CARD.tier);
          spawn(22, 18);
          sfx('coin');
          root.querySelector('[data-spop-kicker]').textContent = 'New flame: ' + CARD.kicker + '!';
        }, 1580);
      }
    }

    function hide() {
      if (root.hidden) return;
      clearInterval(timer);
      timeouts.forEach(clearTimeout);
      if (returnFocus && returnFocus.focus) returnFocus.focus();
      if (still) { root.hidden = true; return; }
      root.classList.add('leaving');
      setTimeout(function () { root.hidden = true; root.classList.remove('leaving'); }, 200);
    }

    root.querySelectorAll('[data-spop-close]').forEach(function (b) { b.addEventListener('click', hide); });
    document.addEventListener('keydown', function (e) {
      if (root.hidden) return;
      if (e.key === 'Escape') { hide(); return; }
      if (e.key !== 'Tab') return;
      var first = root.querySelector('.spop-close'), last = root.querySelector('.spop-go');
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    });

    drawFlame(0);
    setTimeout(show, 500);
  })();
</script>
