{{-- A living pixel mascot. <x-astro-mascot /> and <x-captain-mascot /> pass in their art;
     this draws it and brings the face to life. Decorative.

     Props:
       rows    pixel-art strings, one character per pixel ('.' = empty), keyed by $colors
       eyes    face layers that move with the gaze: 'eye-open', 'eye-shut', 'eye-happy',
               'eye-wink', 'eye-big', 'eye-heart', 'eye-x', and optional 'brow-flat',
               'brow-up', 'brow-think'
       mouths  'm-smile', 'm-grin', 'm-hmm', 'm-wavy', 'm-ooh', plus talking frames
               'm-wide', 'm-half', 'm-shut', 'm-o'
       blush   cheek pixels
       ink     colours for the face layers; each layer is colour key => [[x, y], ...]
       gazeY   'both', or 'up' when looking down would bump the eyes into the mouth

     What the face does:
       - eyes blink on their own, glance around, and follow the pointer
       - hovering gives a big grin and rosy cheeks
       - clicking plays a reaction: giggle, wow, wink or love (five fast clicks: dizzy)
       - an ancestor with .astro-talking makes the mouth chatter (chat sets it while a reply streams)
       - an ancestor with .astro-thinking makes the eyes glance around, mouth going "hmm"
       - an ancestor with .astro-cheer makes it jump for joy with a happy face
     Under prefers-reduced-motion the face still changes, but nothing moves.

     Same-colour pixels are drawn as one <path>, so no hairline seams show between
     rows when the art is scaled to an odd size. --}}
@props(['rows', 'colors', 'ink', 'eyes', 'mouths', 'blush' => [], 'gazeY' => 'both'])
@php
    $d = fn (array $pixels) => implode('', array_map(fn ($p) => "M{$p[0]} {$p[1]}h1v1h-1z", $pixels));
    $art = [];
    foreach ($rows as $y => $row) {
        foreach (str_split($row) as $x => $ch) {
            if ($ch !== '.') $art[$ch][] = [$x, $y];
        }
    }
    // Each mascot on a page blinks on its own beat.
    $delay = '-'.random_int(0, 6900).'ms';
@endphp
<svg viewBox="0 0 {{ strlen($rows[0]) }} {{ count($rows) }}" shape-rendering="crispEdges" aria-hidden="true" focusable="false" data-gaze-y="{{ $gazeY }}" {{ $attributes->class('px-mascot') }}>
  <g class="am-body" style="--am-delay: {{ $delay }}">
    @foreach ($art as $ch => $pixels)<path d="{{ $d($pixels) }}" fill="{{ $colors[$ch] }}"/>@endforeach
    <g class="am-face"><g class="am-wander">
      <g class="am-blush">@foreach ($blush as $c => $px)<path d="{{ $d($px) }}" fill="{{ $ink[$c] }}"/>@endforeach</g>
      <g class="am-eyes">
        @foreach ($eyes as $layer => $pixels)
          <g class="am-{{ $layer }}">@foreach ($pixels as $c => $px)<path d="{{ $d($px) }}" fill="{{ $ink[$c] }}"/>@endforeach</g>
        @endforeach
      </g>
      <g class="am-mouth">
        @foreach ($mouths as $layer => $pixels)
          <g class="am-{{ $layer }}">@foreach ($pixels as $c => $px)<path d="{{ $d($px) }}" fill="{{ $ink[$c] }}"/>@endforeach</g>
        @endforeach
      </g>
    </g></g>
  </g>
</svg>
@once
<style>
  .px-mascot { --ex: 0; --ey: 0; overflow: visible; cursor: pointer; -webkit-tap-highlight-color: transparent; }
  .px-mascot .am-face { transform: translateX(calc(var(--ex) * 1px)); transition: transform .12s steps(1, end); }
  .px-mascot .am-eyes { transform: translateY(calc(var(--ey) * 1px)); transition: transform .12s steps(1, end); }
  .px-mascot[data-gaze-y="up"] .am-eyes { transform: translateY(calc(min(var(--ey), 0) * 1px)); }
  .px-mascot .am-body { transform-box: fill-box; transform-origin: 50% 100%; }

  /* Every face layer starts hidden; the moods below switch the right ones on. */
  .px-mascot .am-blush, .px-mascot .am-eyes > g, .px-mascot .am-mouth > g { opacity: 0; }

  /* ── Normal face (no click reaction playing) ── */
  .px-mascot:not([data-react]) .am-eye-open { opacity: 1; animation: am-blink-open 7s steps(1, end) infinite var(--am-delay); }
  .px-mascot:not([data-react]) .am-eye-shut { animation: am-blink-shut 7s steps(1, end) infinite var(--am-delay); }
  .px-mascot:not([data-react]) .am-brow-flat,
  .px-mascot:not([data-react]) .am-m-smile { opacity: 1; }
  /* With no pointer around, the eyes wander now and then */
  .px-mascot:not([data-react]):not(.am-tracking) .am-wander { animation: am-wander 11s steps(1, end) infinite var(--am-delay); }

  /* Hover: a big grin, raised brows and rosy cheeks */
  .px-mascot:not([data-react]):hover .am-m-smile,
  .px-mascot:not([data-react]):hover .am-brow-flat { opacity: 0; }
  .px-mascot:not([data-react]):hover .am-m-grin,
  .px-mascot:not([data-react]):hover .am-brow-up,
  .px-mascot:not([data-react]):hover .am-blush { opacity: 1; }

  /* Thinking: eyes glance side to side, one brow up, mouth goes "hmm" */
  .astro-thinking .px-mascot:not([data-react]) .am-wander { animation: am-ponder 1.8s steps(1, end) infinite; }
  .astro-thinking .px-mascot:not([data-react]) .am-eyes { transform: none; }
  .astro-thinking .px-mascot:not([data-react]) .am-m-smile,
  .astro-thinking .px-mascot:not([data-react]) .am-m-grin,
  .astro-thinking .px-mascot:not([data-react]) .am-brow-flat,
  .astro-thinking .px-mascot:not([data-react]) .am-brow-up { opacity: 0; }
  .astro-thinking .px-mascot:not([data-react]) .am-m-hmm,
  .astro-thinking .px-mascot:not([data-react]) .am-brow-think { opacity: 1; }

  /* Shy: eyes squeezed shut and rosy cheeks while someone types a password, hidden or
     shown, so Astro never sees it. (Sign-in pages set this.) */
  .astro-shy .px-mascot:not([data-react]) .am-face, .astro-shy .px-mascot:not([data-react]) .am-eyes, .astro-shy .px-mascot:not([data-react]) .am-wander { transform: none; animation: none; }
  .astro-shy .px-mascot:not([data-react]) .am-eye-open { opacity: 0; animation: none; }
  .astro-shy .px-mascot:not([data-react]) .am-eye-shut { opacity: 1; animation: none; }
  .astro-shy .px-mascot:not([data-react]) .am-blush { opacity: 1; }

  /* Cheering: happy squint, big grin, rosy cheeks, jumping for joy (Astro's "you passed!" pop-up sets this). */
  .astro-cheer .px-mascot:not([data-react]) .am-face, .astro-cheer .px-mascot:not([data-react]) .am-eyes, .astro-cheer .px-mascot:not([data-react]) .am-wander { transform: none; animation: none; }
  .astro-cheer .px-mascot:not([data-react]) .am-eye-open, .astro-cheer .px-mascot:not([data-react]) .am-eye-shut,
  .astro-cheer .px-mascot:not([data-react]) .am-m-smile, .astro-cheer .px-mascot:not([data-react]) .am-brow-flat { opacity: 0; animation: none; }
  .astro-cheer .px-mascot:not([data-react]) .am-eye-happy, .astro-cheer .px-mascot:not([data-react]) .am-m-grin,
  .astro-cheer .px-mascot:not([data-react]) .am-brow-up, .astro-cheer .px-mascot:not([data-react]) .am-blush { opacity: 1; }
  .astro-cheer .px-mascot:not([data-react]) .am-body { animation: am-cheer .8s cubic-bezier(.3, 1.3, .5, 1) infinite; }

  /* Talking: the mouth runs through wide, shut, half, o, shut, half */
  .astro-talking .px-mascot:not([data-react]) .am-m-smile,
  .astro-talking .px-mascot:not([data-react]) .am-m-grin { opacity: 0; }
  .astro-talking .px-mascot:not([data-react]) .am-m-wide { animation: am-talk-wide 1.1s steps(1, end) infinite; }
  .astro-talking .px-mascot:not([data-react]) .am-m-shut { animation: am-talk-shut 1.1s steps(1, end) infinite; }
  .astro-talking .px-mascot:not([data-react]) .am-m-half { animation: am-talk-half 1.1s steps(1, end) infinite; }
  .astro-talking .px-mascot:not([data-react]) .am-m-o    { animation: am-talk-o 1.1s steps(1, end) infinite; }

  /* ── Click reactions (the script sets data-react for about a second) ── */
  .px-mascot[data-react] .am-face, .px-mascot[data-react] .am-eyes { transform: none; }
  .px-mascot[data-react="giggle"] .am-eye-happy, .px-mascot[data-react="giggle"] .am-m-grin, .px-mascot[data-react="giggle"] .am-blush, .px-mascot[data-react="giggle"] .am-brow-up,
  .px-mascot[data-react="wow"] .am-eye-big, .px-mascot[data-react="wow"] .am-m-ooh, .px-mascot[data-react="wow"] .am-brow-up,
  .px-mascot[data-react="wink"] .am-eye-wink, .px-mascot[data-react="wink"] .am-m-smile, .px-mascot[data-react="wink"] .am-blush, .px-mascot[data-react="wink"] .am-brow-think,
  .px-mascot[data-react="love"] .am-eye-heart, .px-mascot[data-react="love"] .am-m-grin, .px-mascot[data-react="love"] .am-blush, .px-mascot[data-react="love"] .am-brow-up,
  .px-mascot[data-react="dizzy"] .am-eye-x, .px-mascot[data-react="dizzy"] .am-m-wavy, .px-mascot[data-react="dizzy"] .am-brow-flat { opacity: 1; }
  .px-mascot[data-react="giggle"] .am-body { animation: am-giggle .9s cubic-bezier(.3, 1.4, .5, 1); }
  .px-mascot[data-react="wow"] .am-body    { animation: am-wow .8s cubic-bezier(.3, 1.4, .5, 1); }
  .px-mascot[data-react="wink"] .am-body   { animation: am-wink .8s ease-in-out; }
  .px-mascot[data-react="love"] .am-body   { animation: am-love 1.1s ease-in-out; transform-origin: 50% 50%; }
  .px-mascot[data-react="dizzy"] .am-body  { animation: am-dizzy 1.4s ease-out; transform-origin: 50% 50%; }

  @keyframes am-blink-open { 0%, 100% { opacity: 1 } 45% { opacity: 0 } 47% { opacity: 1 } 93% { opacity: 0 } 94.5% { opacity: 1 } 96% { opacity: 0 } 97.5% { opacity: 1 } }
  @keyframes am-blink-shut { 0%, 100% { opacity: 0 } 45% { opacity: 1 } 47% { opacity: 0 } 93% { opacity: 1 } 94.5% { opacity: 0 } 96% { opacity: 1 } 97.5% { opacity: 0 } }
  @keyframes am-wander { 0%, 100% { transform: none } 30% { transform: translateX(-1px) } 40% { transform: none } 62% { transform: translateX(1px) } 70% { transform: none } 84% { transform: translateX(1px) } 90% { transform: none } }
  @keyframes am-ponder { 0%, 100% { transform: translateX(1px) } 50% { transform: translateX(-1px) } }
  @keyframes am-talk-wide { 0% { opacity: 1 } 16.66%, 100% { opacity: 0 } }
  @keyframes am-talk-shut { 0% { opacity: 0 } 16.66% { opacity: 1 } 33.33% { opacity: 0 } 66.66% { opacity: 1 } 83.33%, 100% { opacity: 0 } }
  @keyframes am-talk-half { 0% { opacity: 0 } 33.33% { opacity: 1 } 50% { opacity: 0 } 83.33%, 100% { opacity: 1 } }
  @keyframes am-talk-o    { 0% { opacity: 0 } 50% { opacity: 1 } 66.66%, 100% { opacity: 0 } }
  @keyframes am-giggle { 0% { transform: none } 15% { transform: scale(1.08, .9) } 35% { transform: translateY(-2px) scale(.95, 1.06) rotate(-4deg) } 55% { transform: scale(1.04, .96) rotate(4deg) } 75% { transform: translateY(-1px) rotate(-3deg) } 100% { transform: none } }
  @keyframes am-wow { 0% { transform: none } 20% { transform: scale(1.1, .85) } 45% { transform: translateY(-4px) scale(.92, 1.1) } 70% { transform: scale(1.06, .94) } 100% { transform: none } }
  @keyframes am-wink { 0%, 100% { transform: none } 30%, 70% { transform: rotate(-10deg) } }
  @keyframes am-love { 0%, 100% { transform: none } 20% { transform: scale(1.12) } 40% { transform: scale(.96) } 60% { transform: scale(1.08) } 80% { transform: scale(1) } }
  @keyframes am-cheer { 0%, 100% { transform: none } 12% { transform: scale(1.12, .86) } 34% { transform: translateY(-5px) scale(.94, 1.08) rotate(-6deg) } 50% { transform: translateY(-6px) } 66% { transform: translateY(-5px) scale(.94, 1.08) rotate(6deg) } 88% { transform: scale(1.1, .9) } }
  @keyframes am-dizzy { 0% { transform: none } 55% { transform: rotate(720deg) } 70% { transform: rotate(708deg) } 85% { transform: rotate(728deg) } 100% { transform: rotate(720deg) } }

  @media (prefers-reduced-motion: reduce) {
    .px-mascot .am-face, .px-mascot .am-eyes, .px-mascot .am-wander, .px-mascot .am-body { animation: none !important; transform: none !important; transition: none; }
    .px-mascot:not([data-react]) .am-eye-open { animation: none; opacity: 1; }
    .px-mascot:not([data-react]) .am-eye-shut { animation: none; opacity: 0; }
    .astro-talking .px-mascot:not([data-react]) .am-mouth > g { animation: none; opacity: 0; }
    .astro-talking .px-mascot:not([data-react]) .am-mouth > .am-m-half { opacity: 1; }
  }
</style>
<script>
  // The mascots come alive: eyes follow the pointer, and a click gets a reaction.
  // One set of listeners serves every mascot on the page.
  (function () {
    if (window.__pxMascot) return;
    window.__pxMascot = true;
    var still = matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ── Eyes follow the pointer ──
    var px = 0, py = 0, queued = false, idle;
    var step = function (d, size) { return d > size * 0.6 ? 1 : d < -size * 0.6 ? -1 : 0; };
    function look() {
      queued = false;
      document.querySelectorAll('svg.px-mascot').forEach(function (el) {
        var r = el.getBoundingClientRect();
        if (!r.width || r.bottom < 0 || r.top > innerHeight) return;
        el.style.setProperty('--ex', step(px - (r.left + r.width / 2), r.width));
        el.style.setProperty('--ey', step(py - (r.top + r.height * 0.45), r.height));
        el.classList.add('am-tracking');
      });
      clearTimeout(idle);
      idle = setTimeout(function () {
        document.querySelectorAll('svg.px-mascot').forEach(function (el) {
          el.style.setProperty('--ex', 0); el.style.setProperty('--ey', 0);
          el.classList.remove('am-tracking');
        });
      }, 2500);
    }
    if (!still) addEventListener('pointermove', function (e) {
      px = e.clientX; py = e.clientY;
      if (!queued) { queued = true; requestAnimationFrame(look); }
    }, { passive: true });

    // ── Click reactions ──
    var REACTIONS = {
      giggle: { ms: 900,  sound: 'pop',  bits: 'star' },
      wow:    { ms: 800,  sound: 'tick', bits: 'spark' },
      wink:   { ms: 800,  sound: 'pop',  bits: 'star' },
      love:   { ms: 1100, sound: 'coin', bits: 'heart' },
      dizzy:  { ms: 1400, sound: 'thud', bits: 'star' },
    };
    var NAMES = ['giggle', 'wow', 'wink', 'love'];
    var SHAPES = {
      star:  [[1,0],[0,1],[1,1],[2,1],[1,2]],
      spark: [[1,0],[1,1],[1,3]],
      heart: [[0,0],[2,0],[0,1],[1,1],[2,1],[1,2]],
    };
    var COLORS = { star: ['#ffd84a', '#73b6ff', '#ffffff'], spark: ['#ffd84a'], heart: ['#ff5c8a', '#ff9ec4'] };

    // Little pixel bits that pop out of the mascot and float away.
    function burst(el, kind) {
      var r = el.getBoundingClientRect(), unit = Math.max(2, r.width / 16);
      var count = kind === 'spark' ? 3 : 5, w = 3, h = kind === 'spark' ? 4 : 3;
      for (var i = 0; i < count; i++) {
        var color = COLORS[kind][i % COLORS[kind].length];
        var bit = document.createElement('div');
        bit.setAttribute('aria-hidden', 'true');
        bit.style.cssText = 'position:fixed;pointer-events:none;z-index:9999;left:' + (r.left + r.width / 2) + 'px;top:' + (r.top + r.height * 0.3) + 'px;width:' + w * unit + 'px;height:' + h * unit + 'px;';
        bit.innerHTML = '<svg viewBox="0 0 ' + w + ' ' + h + '" width="100%" height="100%" shape-rendering="crispEdges">' +
          SHAPES[kind].map(function (p) { return '<rect x="' + p[0] + '" y="' + p[1] + '" width="1" height="1" fill="' + color + '"/>'; }).join('') + '</svg>';
        document.body.appendChild(bit);
        var angle = -Math.PI / 2 + (i - (count - 1) / 2) * (kind === 'spark' ? 0.7 : 0.55);
        var dist = r.width * (0.55 + Math.random() * 0.3);
        var dx = Math.cos(angle) * dist, dy = Math.sin(angle) * dist;
        bit.animate([
          { transform: 'translate(-50%, -50%) scale(.4)', opacity: 1 },
          { transform: 'translate(calc(-50% + ' + dx + 'px), calc(-50% + ' + dy + 'px)) scale(1)', opacity: 1, offset: .6 },
          { transform: 'translate(calc(-50% + ' + dx * 1.1 + 'px), calc(-50% + ' + (dy - r.width * 0.15) + 'px)) scale(.8)', opacity: 0 },
        ], { duration: 700 + i * 60, easing: 'cubic-bezier(.2, .8, .3, 1)' }).onfinish = bit.remove.bind(bit);
      }
    }

    function react(el) {
      var now = Date.now();
      el._amClicks = (el._amClicks || []).filter(function (t) { return now - t < 1500; });
      el._amClicks.push(now);
      var name;
      if (el._amClicks.length >= 5) { name = 'dizzy'; el._amClicks = []; }
      else {
        // A different reaction each time, so clicking again always shows something new.
        var options = NAMES.filter(function (n) { return n !== el._amLast; });
        name = options[Math.floor(Math.random() * options.length)];
      }
      el._amLast = name;
      el.removeAttribute('data-react');
      void el.getBoundingClientRect(); // restart the animation even if one is mid-play
      el.setAttribute('data-react', name);
      clearTimeout(el._amTimer);
      el._amTimer = setTimeout(function () { el.removeAttribute('data-react'); }, REACTIONS[name].ms);
      if (window.techlabSfx) window.techlabSfx.play(REACTIONS[name].sound);
      if (!still) burst(el, REACTIONS[name].bits);
    }

    addEventListener('click', function (e) {
      var el = e.target.closest && e.target.closest('svg.px-mascot');
      if (el) react(el);
    });
  })();
</script>
@endonce
