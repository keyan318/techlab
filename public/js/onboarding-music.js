/*
 * TechLab onboarding music: a friendly 8-bit space tune while Astro welcomes a new cadet, and a short
 * "welcome to the crew" fanfare when the finished astronaut appears.
 *
 * Played live with the Web Audio API (no audio files). Browsers only allow sound after a click or key
 * press, so the tune starts with the cadet's first press (usually Continue). It follows the site-wide
 * Sound switch from public/js/sfx.js ("techlab_sfx" in localStorage, "off" = muted), and adds its own
 * small music button in the corner because this page has no sidebar.
 *
 * Load with: <script defer src="js/onboarding-music.js" data-intro="1"></script>
 * data-intro="0" (changing your look later, not onboarding) keeps it silent.
 */
(function () {
  var script = document.currentScript;
  var AC = window.AudioContext || window.webkitAudioContext;
  if (!AC || !script || script.getAttribute('data-intro') !== '1') return;

  var KEY = 'techlab_sfx';
  var VOLUME = 0.2;
  var BPM = 104;
  var STEP = 60 / BPM / 4;          // one 16th note
  var ctx = null, master = null, musicBus = null, echoIn = null, noiseBuf = null;
  var timer = null, step = 0, loop = 0, nextAt = 0, celebrated = false;

  function midi(m) { return 440 * Math.pow(2, (m - 69) / 12); }

  function muted() {
    try { return localStorage.getItem(KEY) === 'off'; } catch (e) { return false; }
  }
  function setMutedStore(off) {
    try { localStorage.setItem(KEY, off ? 'off' : 'on'); } catch (e) {}
  }

  // C - Am - F - G, one bar each (16 steps). `chord` = arp/pad notes, `root` = bass.
  var BARS = [
    { root: 48, chord: [60, 64, 67, 72] },
    { root: 45, chord: [57, 60, 64, 69] },
    { root: 41, chord: [53, 57, 60, 65] },
    { root: 43, chord: [55, 59, 62, 67] },
  ];
  // The tune, as { step-in-bar: note } per bar. It plays every other loop so it never wears thin.
  var MELODY = [
    { 0: 76, 2: 79, 4: 84, 6: 79, 8: 81, 10: 79, 12: 76 },
    { 0: 76, 2: 74, 4: 72, 8: 76, 12: 69 },
    { 0: 77, 2: 81, 4: 84, 6: 81, 8: 79, 10: 77, 12: 76 },
    { 0: 74, 4: 79, 8: 71, 10: 74, 12: 79 },
  ];
  var ARP = [0, 1, 2, 3, 2, 1, 2, 3];   // one note per 8th

  // ---------- instruments ----------

  function tone(o) {
    var t = o.at, osc = ctx.createOscillator(), amp = ctx.createGain();
    osc.type = o.type || 'square';
    osc.frequency.setValueAtTime(o.f, t);
    if (o.f2) osc.frequency.exponentialRampToValueAtTime(o.f2, t + (o.glide || o.d));
    amp.gain.setValueAtTime(0.0001, t);
    amp.gain.linearRampToValueAtTime(o.v, t + (o.a || 0.005));
    amp.gain.exponentialRampToValueAtTime(0.0001, t + o.d);
    var head = osc;
    if (o.cut) {
      var lp = ctx.createBiquadFilter();
      lp.type = 'lowpass';
      lp.frequency.value = o.cut;
      osc.connect(lp);
      head = lp;
    }
    head.connect(amp);
    amp.connect(musicBus);
    if (o.wet) amp.connect(echoIn);
    osc.start(t);
    osc.stop(t + o.d + 0.05);
  }

  function hiss(at, d, v, cut) {
    var src = ctx.createBufferSource(), hp = ctx.createBiquadFilter(), amp = ctx.createGain();
    src.buffer = noiseBuf;
    src.loop = true;
    hp.type = 'highpass';
    hp.frequency.value = cut;
    amp.gain.setValueAtTime(0.0001, at);
    amp.gain.linearRampToValueAtTime(v, at + 0.002);
    amp.gain.exponentialRampToValueAtTime(0.0001, at + d);
    src.connect(hp); hp.connect(amp); amp.connect(musicBus);
    src.start(at, Math.random() * 0.5);
    src.stop(at + d + 0.05);
  }

  function beat(i, at) {
    var barNo = (i >> 4) & 3, s = i & 15, bar = BARS[barNo];

    // Soft pad under each bar
    if (s === 0) bar.chord.slice(0, 3).forEach(function (m) {
      tone({ type: 'sine', f: midi(m), at: at, d: STEP * 16 + 0.2, a: 0.4, v: 0.03 });
    });
    // Bouncy bass
    if (s === 0 || s === 6 || s === 8 || s === 14) {
      tone({ type: 'triangle', f: midi(bar.root + (s === 14 ? 12 : 0)), at: at, d: 0.22, v: 0.22 });
    }
    // Twinkly arpeggio on 8th notes
    if (s % 2 === 0) {
      tone({ type: 'triangle', f: midi(bar.chord[ARP[s / 2]] + 12), at: at, d: STEP * 1.6, v: 0.05, wet: true });
    }
    // Light drums: soft kick, tiny hats, a clap on 2 and 4
    if (s === 0 || s === 8) tone({ type: 'sine', f: 130, f2: 45, glide: 0.1, at: at, d: 0.22, v: 0.4 });
    if (s % 2 === 0) hiss(at, 0.03, s % 4 === 2 ? 0.035 : 0.02, 7500);
    if (s === 4 || s === 12) hiss(at, 0.1, 0.06, 1500);
    // The tune, every other loop
    var n = MELODY[barNo][s];
    if (loop % 2 === 0 && n) tone({ type: 'square', f: midi(n), at: at, d: STEP * 2.6, v: 0.05, cut: 2600, wet: true });
    // Now and then a star twinkles up high
    if (Math.random() < 0.05) tone({ type: 'sine', f: midi(96 + [0, 4, 7][Math.floor(Math.random() * 3)]), at: at, d: 0.6, a: 0.02, v: 0.02, wet: true });
  }

  // "Welcome to the crew": a rising C major run into a sparkling chord.
  function fanfare(at) {
    [72, 76, 79, 84].forEach(function (m, k) {
      tone({ type: 'square', f: midi(m), at: at + k * 0.09, d: 0.12, v: 0.07, cut: 3000 });
    });
    var hit = at + 0.38;
    [60, 64, 67, 72, 76].forEach(function (m) { tone({ type: 'square', f: midi(m), at: hit, d: 1.3, a: 0.01, v: 0.035, cut: 2800, wet: true }); });
    [91, 96, 100, 103].forEach(function (m, k) { tone({ type: 'sine', f: midi(m), at: hit + 0.1 + k * 0.06, d: 0.5, v: 0.03, wet: true }); });
    tone({ type: 'sine', f: 130, f2: 45, glide: 0.1, at: hit, d: 0.3, v: 0.5 });
    hiss(hit, 1.2, 0.07, 6000);
  }

  // ---------- engine ----------

  // The page's final "Welcome to the crew" screen is the section shown when step === 'done'.
  function doneShowing() {
    var el = document.querySelector('section[x-show*="\'done\'"]');
    return !!el && el.getClientRects().length > 0;
  }

  function tick() {
    var now = ctx.currentTime;
    if (nextAt < now - 0.05) nextAt = now + 0.05;   // tab was asleep: skip ahead, don't burst
    var done = doneShowing();
    if (done && !celebrated) {
      celebrated = true;
      fanfare(now + 0.05);
      nextAt = now + 2.2;                            // let the fanfare ring, then pick the tune back up
      step = 0;
      loop = 1;                                      // restart on a loop without the lead, under the "welcome" text
    } else if (!done) {
      celebrated = false;                            // "Change something" and back again plays it again
    }
    while (nextAt < now + 0.15) {
      beat(step, nextAt);
      nextAt += STEP;
      step = (step + 1) % 64;
      if (step === 0) loop++;
    }
  }

  function start() {
    if (ctx || muted()) return;
    try { ctx = new AC(); } catch (e) { return; }
    master = ctx.createGain();
    master.gain.value = VOLUME;
    var comp = ctx.createDynamicsCompressor();
    master.connect(comp); comp.connect(ctx.destination);
    musicBus = ctx.createGain();
    musicBus.connect(master);

    // Echo, so notes trail off into space
    echoIn = ctx.createGain();
    var delay = ctx.createDelay(1), fb = ctx.createGain(), wet = ctx.createGain();
    delay.delayTime.value = STEP * 3;
    fb.gain.value = 0.3;
    wet.gain.value = 0.3;
    echoIn.connect(delay); delay.connect(fb); fb.connect(delay); delay.connect(wet); wet.connect(musicBus);

    noiseBuf = ctx.createBuffer(1, ctx.sampleRate, ctx.sampleRate);
    var data = noiseBuf.getChannelData(0);
    for (var k = 0; k < data.length; k++) data[k] = Math.random() * 2 - 1;

    celebrated = doneShowing();   // don't fanfare if we wake up already on the last screen
    nextAt = ctx.currentTime + 0.08;
    timer = setInterval(tick, 25);
  }

  function setOn(on) {
    setMutedStore(!on);
    paint();
    if (!ctx) { if (on) start(); return; }
    var now = ctx.currentTime;
    master.gain.cancelScheduledValues(now);
    master.gain.setValueAtTime(master.gain.value, now);
    if (on) {
      ctx.resume();
      master.gain.linearRampToValueAtTime(VOLUME, now + 0.25);
      if (!timer) timer = setInterval(tick, 25);
    } else {
      master.gain.linearRampToValueAtTime(0, now + 0.25);
      clearInterval(timer); timer = null;
      setTimeout(function () { if (muted()) ctx.suspend(); }, 300);
    }
  }

  // ---------- the corner music button ----------

  var btn = document.createElement('button');
  btn.type = 'button';
  btn.setAttribute('data-sfx', 'off');   // sfx.js: no click sound on this one, the music answers instead
  btn.style.cssText = 'position:fixed;right:16px;bottom:16px;z-index:50;padding:8px 12px;border-radius:10px;'
    + 'border:2px solid;cursor:pointer;font:600 13px/1 "Pixelify Sans","Space Grotesk",system-ui,sans-serif;letter-spacing:.02em';
  function paint() {
    var on = !muted();
    var light = document.documentElement.getAttribute('data-theme') === 'light';   // set by public/js/theme.js
    btn.style.background = light ? 'rgba(255,255,255,.92)' : 'rgba(14,16,44,.85)';
    btn.style.borderColor = light ? '#c5cfe4' : 'rgba(150,170,255,.35)';
    btn.style.color = light ? '#2a2f55' : '#c9d3ff';
    btn.textContent = on ? '♪ Music on' : '♪ Music off';
    btn.setAttribute('aria-pressed', String(on));
    btn.setAttribute('aria-label', on ? 'Turn the music and sounds off' : 'Turn the music and sounds on');
    btn.style.opacity = on ? '1' : '.7';
  }
  btn.addEventListener('click', function () { setOn(muted()); });
  window.addEventListener('techlab:theme', paint);
  paint();

  function mount() {
    document.body.appendChild(btn);
    document.addEventListener('pointerdown', start, true);
    document.addEventListener('keydown', start, true);
  }
  if (document.body) mount(); else document.addEventListener('DOMContentLoaded', mount);

  // Quiet while the tab is in the background.
  document.addEventListener('visibilitychange', function () {
    if (!ctx || muted()) return;
    if (document.hidden) { clearInterval(timer); timer = null; ctx.suspend(); }
    else { ctx.resume(); if (!timer) timer = setInterval(tick, 25); }
  });
})();
