{{-- Astro, the tutor: a pixel astronaut helmet on a 20x19 grid, with a living face
     on a big visor. Decorative.

     The face is layers of pixels that CSS shows and hides:
       - eyes blink on their own, glance around, and follow the pointer (script below)
       - hovering makes Astro grin and blush
       - clicking makes Astro react: giggle, wow, wink or love (click fast and it gets dizzy)
       - an ancestor with .astro-talking makes the mouth chatter (chat sets it while a reply streams)
       - an ancestor with .astro-thinking makes Astro glance around, mouth going "hmm"
     Under prefers-reduced-motion the face still changes, but nothing moves. --}}
@php
    $rows = [
        '.....##########.....',
        '...##wwwwwwwwww##...',
        '..#wwwwwwwwwwwwww#..',
        '.#wwwwhhvvvvvvwwww#.',
        '.#wwwhvvvvvvvvvwww#.',
        '#wwwvvvvvvvvvvvvwww#',
        '#wwvvvvvvvvvvvvvvww#',
        '#wwvvvvvvvvvvvvvvww#',
        '#wwvvvvvvvvvvvvvvww#',
        '#wwvvvvvvvvvvvvvvww#',
        '#wwvvvvvvvvvvvvvvww#',
        '#wwwvvvvvvvvvvvvwww#',
        '.#wwwvvvvvvvvvvwww#.',
        '.#wwwwddddddddwwww#.',
        '..#wwwwwwwwwwwwww#..',
        '...##wwwwwwwwww##...',
        '....#gggggggggg#....',
        '....#ggg#gg#ggg#....',
        '.....##########.....',
    ];
    $colors = ['#' => '#1b1750', 'w' => '#eef1ff', 'v' => '#3aa0ff', 'd' => '#2b86e6', 'h' => '#c4e8ff', 'g' => '#9aa3d6'];

    // Face layers: colour => [x, y] pixels. k = eye/mouth black, s = eye shine, p = pink, b = blush.
    $ink = ['k' => '#14112e', 's' => '#ffffff', 'p' => '#ff5c8a', 'b' => '#ff9ec4'];
    $eyes = [
        'eye-open'  => ['k' => [[6,6],[6,7],[7,7],[6,8],[7,8], [12,6],[12,7],[13,7],[12,8],[13,8]], 's' => [[7,6],[13,6]]],
        'eye-shut'  => ['k' => [[6,8],[7,8], [12,8],[13,8]]],
        'eye-happy' => ['k' => [[6,6],[7,6],[5,7],[8,7], [12,6],[13,6],[11,7],[14,7]]],
        'eye-wink'  => ['k' => [[6,6],[6,7],[7,7],[6,8],[7,8], [12,6],[13,6],[11,7],[14,7]], 's' => [[7,6]]],
        'eye-big'   => ['k' => [[5,5],[6,5],[5,6],[6,6],[7,6],[5,7],[6,7],[7,7],[6,8],[7,8],
                                [12,5],[13,5],[12,6],[13,6],[14,6],[12,7],[13,7],[14,7],[13,8],[14,8]],
                        's' => [[7,5],[5,8],[14,5],[12,8]]],
        'eye-heart' => ['p' => [[5,6],[7,6],[5,7],[6,7],[7,7],[6,8], [12,6],[14,6],[12,7],[13,7],[14,7],[13,8]]],
        'eye-x'     => ['k' => [[5,6],[7,6],[6,7],[5,8],[7,8], [12,6],[14,6],[13,7],[12,8],[14,8]]],
    ];
    $mouths = [
        'm-smile' => ['k' => [[7,11],[12,11],[8,12],[9,12],[10,12],[11,12]]],
        'm-grin'  => ['k' => [[7,11],[8,11],[9,11],[10,11],[11,11],[12,11],[8,12],[11,12],[9,13],[10,13]], 'p' => [[9,12],[10,12]]],
        'm-hmm'   => ['k' => [[8,12],[9,12],[10,12],[11,11]]],
        'm-wavy'  => ['k' => [[7,12],[8,11],[9,12],[10,11],[11,12],[12,11]]],
        // Talking frames: wide, half, shut, round "o" (also the "wow" mouth)
        'm-wide'  => ['k' => [[8,11],[9,11],[10,11],[11,11],[8,12],[11,12],[9,13],[10,13]], 'p' => [[9,12],[10,12]]],
        'm-half'  => ['k' => [[8,11],[9,11],[10,11],[11,11],[9,12],[10,12]]],
        'm-shut'  => ['k' => [[8,12],[9,12],[10,12],[11,12]]],
        'm-o'     => ['k' => [[9,11],[10,11],[8,12],[9,12],[10,12],[11,12],[9,13],[10,13]]],
    ];
    $blush = ['b' => [[4,10],[5,10],[14,10],[15,10]]];

    // Each Astro on a page blinks on its own beat.
    $delay = '-'.random_int(0, 6900).'ms';
@endphp
<svg viewBox="0 0 20 19" shape-rendering="crispEdges" aria-hidden="true" focusable="false" {{ $attributes->class('astro-mascot') }} style="--am-delay: {{ $delay }}">
  <g class="am-body">
    @foreach ($rows as $y => $row)
      @foreach (str_split($row) as $x => $ch)
        @if ($ch !== '.')
          <rect x="{{ $x }}" y="{{ $y }}" width="1" height="1" fill="{{ $colors[$ch] }}"/>
        @endif
      @endforeach
    @endforeach
    <g class="am-face"><g class="am-wander">
      <g class="am-blush">@foreach ($blush as $c => $px)@foreach ($px as [$x, $y])<rect x="{{ $x }}" y="{{ $y }}" width="1" height="1" fill="{{ $ink[$c] }}"/>@endforeach @endforeach</g>
      <g class="am-eyes">
        @foreach ($eyes as $layer => $pixels)
          <g class="am-{{ $layer }}">@foreach ($pixels as $c => $px)@foreach ($px as [$x, $y])<rect x="{{ $x }}" y="{{ $y }}" width="1" height="1" fill="{{ $ink[$c] }}"/>@endforeach @endforeach</g>
        @endforeach
      </g>
      <g class="am-mouth">
        @foreach ($mouths as $layer => $pixels)
          <g class="am-{{ $layer }}">@foreach ($pixels as $c => $px)@foreach ($px as [$x, $y])<rect x="{{ $x }}" y="{{ $y }}" width="1" height="1" fill="{{ $ink[$c] }}"/>@endforeach @endforeach</g>
        @endforeach
      </g>
    </g></g>
  </g>
</svg>
@once
<style>
  .astro-mascot { --ex: 0; --ey: 0; overflow: visible; cursor: pointer; -webkit-tap-highlight-color: transparent; }
  .astro-mascot .am-face { transform: translateX(calc(var(--ex) * 1px)); transition: transform .12s steps(1, end); }
  .astro-mascot .am-eyes { transform: translateY(calc(var(--ey) * 1px)); transition: transform .12s steps(1, end); }
  .astro-mascot .am-body { transform-box: fill-box; transform-origin: 50% 100%; }

  /* Every face layer starts hidden; the moods below switch the right ones on. */
  .astro-mascot .am-blush, .astro-mascot .am-eyes > g, .astro-mascot .am-mouth > g { opacity: 0; }

  /* ── Normal face (no click reaction playing) ── */
  .astro-mascot:not([data-react]) .am-eye-open { animation: am-blink-open 7s steps(1, end) infinite var(--am-delay); }
  .astro-mascot:not([data-react]) .am-eye-shut { animation: am-blink-shut 7s steps(1, end) infinite var(--am-delay); }
  .astro-mascot:not([data-react]) .am-m-smile { opacity: 1; }
  /* With no pointer around, the eyes wander now and then */
  .astro-mascot:not([data-react]):not(.am-tracking) .am-wander { animation: am-wander 11s steps(1, end) infinite var(--am-delay); }

  /* Hover: a big grin and rosy cheeks */
  .astro-mascot:not([data-react]):hover .am-m-smile { opacity: 0; }
  .astro-mascot:not([data-react]):hover .am-m-grin,
  .astro-mascot:not([data-react]):hover .am-blush { opacity: 1; }

  /* Thinking: eyes glance side to side, mouth goes "hmm" */
  .astro-thinking .astro-mascot:not([data-react]) .am-wander { animation: am-ponder 1.8s steps(1, end) infinite; }
  .astro-thinking .astro-mascot:not([data-react]) .am-m-smile,
  .astro-thinking .astro-mascot:not([data-react]) .am-m-grin { opacity: 0; }
  .astro-thinking .astro-mascot:not([data-react]) .am-m-hmm { opacity: 1; }

  /* Talking: the mouth runs through wide, shut, half, o, shut, half */
  .astro-talking .astro-mascot:not([data-react]) .am-m-smile,
  .astro-talking .astro-mascot:not([data-react]) .am-m-grin { opacity: 0; }
  .astro-talking .astro-mascot:not([data-react]) .am-m-wide { animation: am-talk-wide 1.1s steps(1, end) infinite; }
  .astro-talking .astro-mascot:not([data-react]) .am-m-shut { animation: am-talk-shut 1.1s steps(1, end) infinite; }
  .astro-talking .astro-mascot:not([data-react]) .am-m-half { animation: am-talk-half 1.1s steps(1, end) infinite; }
  .astro-talking .astro-mascot:not([data-react]) .am-m-o    { animation: am-talk-o 1.1s steps(1, end) infinite; }

  /* ── Click reactions (the script sets data-react for about a second) ── */
  .astro-mascot[data-react] .am-face, .astro-mascot[data-react] .am-eyes { transform: none; }
  .astro-mascot[data-react="giggle"] .am-eye-happy, .astro-mascot[data-react="giggle"] .am-m-grin, .astro-mascot[data-react="giggle"] .am-blush,
  .astro-mascot[data-react="wow"] .am-eye-big, .astro-mascot[data-react="wow"] .am-m-o,
  .astro-mascot[data-react="wink"] .am-eye-wink, .astro-mascot[data-react="wink"] .am-m-smile, .astro-mascot[data-react="wink"] .am-blush,
  .astro-mascot[data-react="love"] .am-eye-heart, .astro-mascot[data-react="love"] .am-m-grin, .astro-mascot[data-react="love"] .am-blush,
  .astro-mascot[data-react="dizzy"] .am-eye-x, .astro-mascot[data-react="dizzy"] .am-m-wavy { opacity: 1; }
  .astro-mascot[data-react="giggle"] .am-body { animation: am-giggle .9s cubic-bezier(.3, 1.4, .5, 1); }
  .astro-mascot[data-react="wow"] .am-body    { animation: am-wow .8s cubic-bezier(.3, 1.4, .5, 1); }
  .astro-mascot[data-react="wink"] .am-body   { animation: am-wink .8s ease-in-out; }
  .astro-mascot[data-react="love"] .am-body   { animation: am-love 1.1s ease-in-out; transform-origin: 50% 50%; }
  .astro-mascot[data-react="dizzy"] .am-body  { animation: am-dizzy 1.4s ease-out; transform-origin: 50% 50%; }

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
  @keyframes am-dizzy { 0% { transform: none } 55% { transform: rotate(720deg) } 70% { transform: rotate(708deg) } 85% { transform: rotate(728deg) } 100% { transform: rotate(720deg) } }

  @media (prefers-reduced-motion: reduce) {
    .astro-mascot .am-face, .astro-mascot .am-eyes, .astro-mascot .am-wander, .astro-mascot .am-body { animation: none !important; transform: none !important; transition: none; }
    .astro-mascot:not([data-react]) .am-eye-open { animation: none; opacity: 1; }
    .astro-mascot:not([data-react]) .am-eye-shut { animation: none; opacity: 0; }
    .astro-talking .astro-mascot:not([data-react]) .am-mouth > g { animation: none; opacity: 0; }
    .astro-talking .astro-mascot:not([data-react]) .am-mouth > .am-m-half { opacity: 1; }
  }
</style>
<script>
  // Astro comes alive: eyes follow the pointer, and a click gets a reaction.
  // One set of listeners serves every Astro on the page.
  (function () {
    if (window.__astroFace) return;
    window.__astroFace = true;
    var still = matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ── Eyes follow the pointer ──
    var px = 0, py = 0, queued = false, idle;
    var step = function (d, size) { return d > size * 0.6 ? 1 : d < -size * 0.6 ? -1 : 0; };
    function look() {
      queued = false;
      document.querySelectorAll('svg.astro-mascot').forEach(function (el) {
        var r = el.getBoundingClientRect();
        if (!r.width || r.bottom < 0 || r.top > innerHeight) return;
        el.style.setProperty('--ex', step(px - (r.left + r.width / 2), r.width));
        el.style.setProperty('--ey', step(py - (r.top + r.height * 0.37), r.height));
        el.classList.add('am-tracking');
      });
      clearTimeout(idle);
      idle = setTimeout(function () {
        document.querySelectorAll('svg.astro-mascot').forEach(function (el) {
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

    // Little pixel bits that pop out of Astro and float away.
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
      var el = e.target.closest && e.target.closest('svg.astro-mascot');
      if (el) react(el);
    });
  })();
</script>
@endonce
