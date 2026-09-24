{{-- Space backdrop for the public pages: parallax stars, shooting stars and a clickable planet.
     Pages that use it keep their content above it with `position: relative; z-index: 1`.
     The planet is decorative and mouse/touch only, so it is hidden from screen readers and skipped by Tab. --}}
@props(['planet' => true])
<link rel="stylesheet" href="{{ asset('css/cosmic-background.css') }}?v={{ filemtime(public_path('css/cosmic-background.css')) }}">
<div class="cosmic" aria-hidden="true">
  <div class="cosmic-stars"></div>
  <div class="cosmic-stars-medium"></div>
  <div class="cosmic-stars-large"></div>
</div>
@if ($planet)
<button type="button" class="cosmic-planet" aria-hidden="true" tabindex="-1" title="Give it a spin">
  <span class="cosmic-planet-float"><canvas></canvas></span>
</button>
@endif
<script>
  (function () {
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ---- Star layers: each is one long box-shadow (x y colour, ...). Fewer stars on small screens.
    var w = Math.max(window.innerWidth, 1200), small = window.innerWidth < 700;
    function field(count) {
      var s = [];
      for (var i = 0; i < count; i++) s.push(Math.floor(Math.random() * w) + 'px ' + Math.floor(Math.random() * 2000) + 'px #fff');
      return s.join(',');
    }
    [['.cosmic-stars', 420], ['.cosmic-stars-medium', 130], ['.cosmic-stars-large', 55]].forEach(function (l) {
      var el = document.querySelector(l[0]);
      if (el) el.style.setProperty('--stars', field(small ? Math.round(l[1] / 2) : l[1]));
    });

    // ---- Shooting stars: one fast streak at a time, then a random 6-14 s quiet gap. Off for reduced motion.
    var sky = document.querySelector('.cosmic');
    function launch(manual) {
      if (!sky || reduce || document.hidden || sky.querySelector('.cosmic-shoot')) { if (!manual) next(); return; }
      var pw = window.innerWidth, ph = window.innerHeight;
      var dir = Math.random() < 0.5 ? 1 : -1;               // flies to the right or to the left
      var tilt = 20 + Math.random() * 25;                   // degrees below horizontal
      var dist = 380 + Math.random() * 320;                 // px travelled
      var rad = tilt * Math.PI / 180;
      var el = document.createElement('div');
      el.className = 'cosmic-shoot';
      el.style.setProperty('--x', Math.round(pw * (dir > 0 ? 0.05 + Math.random() * 0.55 : 0.4 + Math.random() * 0.55)) + 'px');
      el.style.setProperty('--y', Math.round(ph * (0.04 + Math.random() * 0.36)) + 'px');
      el.style.setProperty('--dx', Math.round(Math.cos(rad) * dist * dir) + 'px');
      el.style.setProperty('--dy', Math.round(Math.sin(rad) * dist) + 'px');
      el.style.setProperty('--a', (dir > 0 ? tilt : 180 - tilt) + 'deg');
      el.style.setProperty('--dur', (0.7 + Math.random() * 0.5).toFixed(2) + 's');
      el.addEventListener('animationend', function () { el.remove(); if (!manual) next(); });
      sky.appendChild(el);
    }
    function next() { setTimeout(launch, 6000 + Math.random() * 8000); }
    if (sky && !reduce) setTimeout(launch, 2500 + Math.random() * 2500);

    // ---- The planet: a violet gas giant in pixel art (a 64x64 canvas, flat colours, no smoothing), spinning, with a tilted ring.
    var btn = document.querySelector('.cosmic-planet'), cv = btn && btn.querySelector('canvas');
    if (!cv || !cv.getContext) return;
    var ctx = cv.getContext('2d'), TAU = Math.PI * 2;
    var TW = 512, TH = 256, tex = null;          // surface texture (wraps around the globe)
    var lut = new Uint8Array(256 * 3);           // texture value -> violet colour (same palette as before)
    var geo = null, rot = 0, omega = 0.12, IDLE = 0.12, running = false, last = 0, acc = 0;
    var N = 64, R = 16, RATIO = .34, img = null;   // pixel grid: the canvas is N x N, the planet is 2R wide (half the canvas)

    // Palette: deep indigo -> violet -> lavender highlights
    (function () {
      var stops = [[0, 38, 20, 104], [.32, 84, 48, 190], [.58, 155, 107, 255], [.84, 201, 176, 255], [1, 238, 226, 255]];
      for (var i = 0; i < 256; i++) {
        var t = i / 255, k = 1; while (k < stops.length - 1 && t > stops[k][0]) k++;
        var a = stops[k - 1], b = stops[k], f = (t - a[0]) / (b[0] - a[0] || 1);
        for (var c = 0; c < 3; c++) lut[i * 3 + c] = Math.round(a[c + 1] + (b[c + 1] - a[c + 1]) * f);
      }
    })();

    // Noise that repeats in x, so the texture wraps around the planet without a seam
    function hash(x, y) { var n = Math.sin(x * 127.1 + y * 311.7) * 43758.5453; return n - Math.floor(n); }
    function vnoise(x, y, px) {
      var xi = Math.floor(x), yi = Math.floor(y), xf = x - xi, yf = y - yi;
      var sx = xf * xf * (3 - 2 * xf), sy = yf * yf * (3 - 2 * yf);
      var x0 = ((xi % px) + px) % px, x1 = (x0 + 1) % px;
      var a = hash(x0, yi), b = hash(x1, yi), c = hash(x0, yi + 1), d = hash(x1, yi + 1);
      return a + (b - a) * sx + (c - a) * sy + (a - b - c + d) * sx * sy;
    }
    function fbm(x, y, px) { var s = 0, amp = .5, f = 1; for (var o = 0; o < 5; o++) { s += amp * vnoise(x * f, y * f, px * f); amp *= .5; f *= 2; } return s; }

    function makeTexture() {
      tex = new Uint8Array(TW * TH);
      for (var y = 0; y < TH; y++) {
        var v = y / TH;
        for (var x = 0; x < TW; x++) {
          var u = x / TW;
          var warp = fbm(u * 5, v * 9, 5), warp2 = fbm(u * 8 + 3, v * 14, 8);
          var band = Math.sin((v + .07 * warp) * TAU * 3.4 + 2.2 * warp2);       // horizontal cloud bands
          var detail = fbm(u * 22, v * 42, 22);                                  // fine swirls inside the bands
          var t = .5 + .3 * band + .1 * (detail - .5);
          // A big storm, like Jupiter's red spot: a bright swirl with a dark collar around it
          var du = Math.abs(u - .3); du = Math.min(du, 1 - du);
          var d = (du / .075) * (du / .075) + ((v - .6) / .045) * ((v - .6) / .045);
          if (d < 1) t += (.95 - t + (fbm(u * 60, v * 90, 60) - .5) * .3) * Math.pow(1 - d, .55);
          else if (d < 2.2) t *= 1 - .18 * (1 - (d - 1) / 1.2);
          tex[y * TW + x] = Math.max(0, Math.min(255, Math.round(t * 255)));
        }
      }
    }

    // Everything that does not change while the planet spins (which pixel is planet, ring, light) is worked out once.
    // Each pixel is fully on or off, so edges stay hard like real pixel art.
    var RING = [[1.3, 1.6, .85], [1.78, 2.0, .6]], RC = [205, 184, 255], LEVELS = [10, 70, 130, 190, 245];
    function setup() {
      btn.style.width = '';
      var D = btn.clientWidth;
      if (!D) { geo = null; return; }
      var k = Math.max(2, Math.floor(D / (2 * R)));                          // whole screen pixels per art pixel, so none look wider than others
      btn.style.width = (2 * R * k) + 'px';
      cv.width = cv.height = N; img = ctx.createImageData(N, N);
      var cap = N * N, pl = new Uint8Array(cap), lon = new Float32Array(cap), row = new Int32Array(cap),
          shade = new Float32Array(cap), ringA = new Float32Array(cap), front = new Uint8Array(cap);
      var tau = -.32, ct = Math.cos(tau), st = Math.sin(tau);                // the planet's axis is tilted
      var lx = -.6, ly = .55, lz = .58, ll = Math.hypot(lx, ly, lz); lx /= ll; ly /= ll; lz /= ll; // light from the upper left
      var ca = Math.cos(.26), sa = Math.sin(.26);
      for (var j = 0; j < N; j++) for (var i = 0; i < N; i++) {
        var o = j * N + i, x = (i + .5 - N / 2) / R, y = (j + .5 - N / 2) / R, r2 = x * x + y * y;
        var u = x * ca - y * sa, v = x * sa + y * ca;                        // into the ring's own tilted frame
        for (var q = 0; q < RING.length; q++) {
          var bo = RING[q][1], bi = RING[q][0];
          var eo = (u / bo) * (u / bo) + (v / (bo * RATIO)) * (v / (bo * RATIO));
          var ei = (u / bi) * (u / bi) + (v / (bi * RATIO)) * (v / (bi * RATIO));
          if (eo <= 1 && ei > 1) { ringA[o] = RING[q][2]; front[o] = v >= 0 ? 1 : 0; }
        }
        if (r2 > 1) continue;
        var nx = x, ny = -y, nz = Math.sqrt(1 - r2);
        var mx = nx * ct - ny * st, my = nx * st + ny * ct;                   // into the planet's own (tilted) frame
        var dif = Math.max(0, Math.min(1, (nx * lx + ny * ly + nz * lz + .12) / 1.12));
        pl[o] = 1;
        lon[o] = Math.atan2(mx, nz);
        row[o] = Math.max(0, Math.min(TH - 1, Math.floor((.5 - Math.asin(Math.max(-1, Math.min(1, my))) / Math.PI) * TH))) * TW;
        shade[o] = dif < .18 ? .3 : dif < .42 ? .52 : dif < .7 ? .78 : 1;    // four flat light steps, no smooth gradient
      }
      geo = { n: cap, pl: pl, lon: lon, row: row, shade: shade, ringA: ringA, front: front };
    }

    function draw() {
      var g = geo; if (!g || !tex) return;
      var d = img.data, s = rot / TAU;
      for (var o = 0; o < g.n; o++) {
        var p = o * 4, ra = g.ringA[o], r, gr, b, a;
        if (g.pl[o]) {
          var u = (((g.lon[o] / TAU + s) % 1) + 1) % 1, lv = Math.min(4, (tex[g.row[o] + ((u * TW) | 0)] * 5) >> 8), ti = LEVELS[lv] * 3, sh = g.shade[o];
          r = lut[ti] * sh; gr = lut[ti + 1] * sh; b = lut[ti + 2] * sh; a = 255;
          if (ra && g.front[o]) { r += (RC[0] - r) * ra; gr += (RC[1] - gr) * ra; b += (RC[2] - b) * ra; }   // near half of the ring passes in front
        } else if (ra) { r = RC[0]; gr = RC[1]; b = RC[2]; a = Math.round(ra * 255); }
        else { d[p + 3] = 0; continue; }
        d[p] = r; d[p + 1] = gr; d[p + 2] = b; d[p + 3] = a;
      }
      ctx.putImageData(img, 0, 0);
    }

    // Spin loop (about 30 fps): a slow idle turn, and a click adds speed that eases back down
    function tick(t) {
      if (!running) return;
      requestAnimationFrame(tick);
      var dt = Math.min((t - last) / 1000, .1); last = t; acc += dt;
      if (acc < 1 / 30) return;
      omega = IDLE + (omega - IDLE) * Math.exp(-acc * 1.6); rot += omega * acc; acc = 0; draw();
    }
    function start() { if (running || reduce || !geo) return; running = true; last = performance.now(); acc = 0; requestAnimationFrame(tick); }

    btn.addEventListener('click', function () {
      if (!geo) return;
      if (reduce) { rot += .8; draw(); return; }                             // reduced motion: a single step, no animation
      omega += 5;                                                            // give it a flick
      btn.animate([{ transform: 'scale(.94)' }, { transform: 'scale(1.12)' }, { transform: 'scale(.98)' }, { transform: 'scale(1)' }],
        { duration: 650, easing: 'cubic-bezier(.2,.9,.25,1)' });             // a little springy, because the click is a poke
      var pulse = document.createElement('span'); pulse.className = 'cosmic-pulse';
      pulse.addEventListener('animationend', function () { pulse.remove(); });
      btn.firstElementChild.appendChild(pulse);
      launch(true);                                                          // and the sky answers with a shooting star
    });

    // Build the pixel planet at the size the page CSS gives it; again after load (CSS applied) and on resize
    function boot() { setup(); if (geo) { if (!tex) makeTexture(); draw(); start(); } }
    var timer; window.addEventListener('resize', function () { clearTimeout(timer); timer = setTimeout(boot, 250); });
    window.addEventListener('load', boot);
    setTimeout(boot, 0);                                                     // after first paint, so the page shows up first
  })();
</script>
