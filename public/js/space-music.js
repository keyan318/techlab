/*
 * TechLab background music for the student's everyday pages (Home, Chat, Planets): the same soft
 * space tune as the landing page (public/js/game-sound.js), turned down low so it sits behind
 * reading and chatting.
 *
 * Played live with the Web Audio API (no audio files). Browsers only allow sound after a click or
 * key press on the page, so the tune fades in with the student's first press. It follows the
 * sidebar's Sound switch from public/js/sfx.js ("techlab_sfx" in localStorage, "off" = muted) and
 * hears it flip through the "techlab:sound" event.
 *
 * Moving between these pages reloads the page, so the tune remembers when it started (in
 * sessionStorage) and picks up at the same spot in the song instead of starting over each time.
 *
 * Load with: <script defer src="js/space-music.js"></script>
 */
(function () {
  var AC = window.AudioContext || window.webkitAudioContext;
  if (!AC) return;

  var KEY = 'techlab_sfx', T0_KEY = 'techlab_music_t0';
  var VOLUME = 0.32;
  var BPM = 84, STEP = 60 / BPM / 2;          // one 8th note
  var LOOP = 32;                              // 4 chords x 8 steps
  var ctx = null, master = null, echo = null, timer = null, step = 0, nextAt = 0;

  function midi(m) { return 440 * Math.pow(2, (m - 69) / 12); }
  function muted() { try { return localStorage.getItem(KEY) === 'off'; } catch (e) { return false; } }

  // Where in the song we are, so the next page carries on from here.
  function songStep() {
    var t0 = 0;
    try { t0 = +sessionStorage.getItem(T0_KEY) || 0; } catch (e) {}
    if (!t0) { t0 = Date.now(); try { sessionStorage.setItem(T0_KEY, String(t0)); } catch (e) {} }
    return Math.floor((Date.now() - t0) / 1000 / STEP) % LOOP;
  }

  function tone(o) {
    var t = o.at, osc = ctx.createOscillator(), g = ctx.createGain();
    osc.type = o.type;
    osc.frequency.setValueAtTime(o.f, t);
    g.gain.setValueAtTime(0.0001, t);
    g.gain.exponentialRampToValueAtTime(o.v, t + o.a);
    g.gain.exponentialRampToValueAtTime(0.0001, t + o.d);
    osc.connect(g); g.connect(master);
    if (o.wet) g.connect(echo);
    osc.start(t); osc.stop(t + o.d + 0.05);
  }

  // Am - F - C - G: a soft pad under an echoing arpeggio, and now and then a star twinkles up high.
  var CHORDS = [[57, 60, 64], [53, 57, 60], [55, 60, 64], [55, 59, 62]], ARP = [0, 1, 2, 3, 2, 1, 2, 1], TWINKLE = [81, 84, 86, 88, 91, 93];
  function beat(s, at) {
    var ch = CHORDS[Math.floor(s / 8) % CHORDS.length], i = s % 8;
    if (i === 0) ch.forEach(function (m) { tone({ type: 'sine', f: midi(m), at: at, d: STEP * 8 + 0.3, a: 0.5, v: 0.035 }); });
    var notes = [ch[0] + 12, ch[1] + 12, ch[2] + 12, ch[0] + 24];
    if (i === 0 || Math.random() < 0.9) tone({ type: 'triangle', f: midi(notes[ARP[i]]), at: at, d: STEP * 1.8, a: 0.01, v: 0.06, wet: true });
    if (Math.random() < 0.1) tone({ type: 'sine', f: midi(TWINKLE[Math.floor(Math.random() * TWINKLE.length)]), at: at + STEP / 2, d: 1, a: 0.02, v: 0.03, wet: true });
  }

  // Notes are booked a moment ahead, so a busy page never makes the beat stutter.
  function tick() {
    if (nextAt < ctx.currentTime) nextAt = ctx.currentTime + 0.05;   // tab was asleep: carry on from now, no burst
    while (nextAt < ctx.currentTime + 0.3) { beat(step, nextAt); step = (step + 1) % LOOP; nextAt += STEP; }
  }

  function build() {
    ctx = new AC();
    var limit = ctx.createDynamicsCompressor();
    limit.threshold.value = -10; limit.ratio.value = 12;
    limit.connect(ctx.destination);
    master = ctx.createGain(); master.gain.value = 0; master.connect(limit);
    // Echo, so notes trail off into space
    echo = ctx.createDelay(2); echo.delayTime.value = STEP * 3;
    var fb = ctx.createGain(); fb.gain.value = 0.38;
    var dull = ctx.createBiquadFilter(); dull.type = 'lowpass'; dull.frequency.value = 2200;
    echo.connect(dull); dull.connect(fb); fb.connect(echo); dull.connect(master);
  }

  function play() {
    if (muted() || document.hidden) return;
    if (!ctx) { try { build(); } catch (e) { return; } }
    if (ctx.state === 'suspended') ctx.resume();
    if (timer) return;
    step = songStep();
    nextAt = ctx.currentTime + 0.1;
    timer = setInterval(tick, 80);
    var now = ctx.currentTime;
    master.gain.cancelScheduledValues(now);
    master.gain.setValueAtTime(master.gain.value, now);
    master.gain.linearRampToValueAtTime(VOLUME, now + 2);           // fades in, never starts with a jolt
  }

  function stop() {
    if (!ctx) return;
    clearInterval(timer); timer = null;
    var now = ctx.currentTime;
    master.gain.cancelScheduledValues(now);
    master.gain.setValueAtTime(master.gain.value, now);
    master.gain.linearRampToValueAtTime(0, now + 0.4);
    setTimeout(function () { if (!timer) ctx.suspend(); }, 500);
  }

  // Try straight away (some browsers allow it once you've used the site), else wait for the first press.
  play();
  function firstPress() { if (!timer || (ctx && ctx.state === 'suspended')) play(); }
  document.addEventListener('pointerdown', firstPress, true);
  document.addEventListener('keydown', firstPress, true);

  // The sidebar's Sound button.
  window.addEventListener('techlab:sound', function (e) { e.detail && e.detail.on ? play() : stop(); });

  // Quiet in a background tab; back on when the student returns.
  document.addEventListener('visibilitychange', function () { document.hidden ? stop() : (ctx && play()); });
  window.addEventListener('pageshow', function (e) { if (e.persisted && ctx) play(); });
})();
