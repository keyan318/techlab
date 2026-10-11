/*
 * TechLab click sounds: a little retro "pop" when you press things, so the app feels like a game.
 *
 * Every sound is synthesised with the Web Audio API (no audio files to download). Which sound
 * plays depends on what was pressed:
 *   coin  — the main (yellow / solid) buttons
 *   tick  — checkboxes, radio buttons, answer choices, drop-downs
 *   thud  — something locked or disabled
 *   pop   — every other button or link
 * An element (or any parent) can pick its own with data-sfx="coin|tick|thud|pop|win|lose",
 * or stay silent with data-sfx="off". Pages can also call window.techlabSfx.play('win').
 *
 * Muted state lives in localStorage under "techlab_sfx" ("off" = muted); the sidebar's Sound
 * button flips it.
 */
(function () {
  var KEY = 'techlab_sfx';
  var ctx = null;

  function muted() {
    try { return localStorage.getItem(KEY) === 'off'; } catch (e) { return false; }
  }

  function audio() {
    var AC = window.AudioContext || window.webkitAudioContext;
    if (!AC) return null;
    if (!ctx) ctx = new AC();
    if (ctx.state === 'suspended') ctx.resume();
    return ctx;
  }

  /* One short note: a waveform sliding from one pitch to another, fading out fast. */
  function note(type, from, to, start, length, volume) {
    var a = audio();
    if (!a) return;
    var t = a.currentTime + start;
    var osc = a.createOscillator();
    var gain = a.createGain();
    osc.type = type;
    osc.frequency.setValueAtTime(from, t);
    osc.frequency.exponentialRampToValueAtTime(to, t + length);
    gain.gain.setValueAtTime(0.0001, t);
    gain.gain.exponentialRampToValueAtTime(volume, t + 0.008);
    gain.gain.exponentialRampToValueAtTime(0.0001, t + length);
    osc.connect(gain).connect(a.destination);
    osc.start(t);
    osc.stop(t + length + 0.02);
  }

  var SOUNDS = {
    pop: function () { note('sine', 520, 880, 0, 0.07, 0.12); },
    tick: function () { note('triangle', 1300, 1100, 0, 0.035, 0.08); },
    coin: function () { note('square', 988, 988, 0, 0.07, 0.035); note('square', 1319, 1319, 0.07, 0.16, 0.035); },
    thud: function () { note('square', 160, 90, 0, 0.1, 0.04); },
    win: function () {
      [523, 659, 784, 1047].forEach(function (f, i) { note('square', f, f, i * 0.11, i === 3 ? 0.4 : 0.12, 0.04); });
    },
    lose: function () {
      [392, 330, 262].forEach(function (f, i) { note('triangle', f, f * 0.97, i * 0.18, i === 2 ? 0.45 : 0.18, 0.1); });
    },
  };

  function play(name) {
    if (muted() || !SOUNDS[name]) return;
    try { SOUNDS[name](); } catch (e) {}
  }

  var PRESSABLE = 'a[href], button, summary, select, label, [role="button"], [role="tab"], [role="menuitem"], [role="option"], input[type="checkbox"], input[type="radio"], input[type="submit"], input[type="button"], .px-btn';
  var PRIMARY = '.px-btn-yellow, .mbtn.solid, .qb-btn.solid, .btn-primary';

  /* Which sound a press on this element should make, or null for none. */
  function soundFor(target) {
    if (!target || !target.closest) return null;
    var el = target.closest(PRESSABLE);
    if (!el) return null;

    var picked = target.closest('[data-sfx]');
    if (picked) {
      var name = picked.getAttribute('data-sfx');
      return name === 'off' ? null : name;
    }
    if (el.disabled || el.getAttribute('aria-disabled') === 'true' || el.classList.contains('px-btn-locked')) return 'thud';
    if (el.matches('input[type="checkbox"], input[type="radio"], select, label, [role="option"]')) return 'tick';
    if (el.matches(PRIMARY)) return 'coin';
    return 'pop';
  }

  document.addEventListener('pointerdown', function (e) {
    if (e.button !== 0) return;
    // A disabled button swallows the event, so look at what is under the pointer instead.
    play(soundFor(document.elementFromPoint(e.clientX, e.clientY) || e.target));
  }, true);

  // Keyboard presses count too (Enter or Space on a focused button / link).
  document.addEventListener('keydown', function (e) {
    if ((e.key === 'Enter' || e.key === ' ') && !e.repeat && document.activeElement && document.activeElement.matches(PRESSABLE)) {
      play(soundFor(document.activeElement));
    }
  }, true);

  window.techlabSfx = {
    play: play,
    muted: muted,
    /* Flips sound on/off; returns true when sound is now on. */
    toggle: function () {
      var on = muted();
      try { localStorage.setItem(KEY, on ? 'on' : 'off'); } catch (e) {}
      if (on) play('pop');
      // Background music (public/js/space-music.js) listens for this to start or stop.
      window.dispatchEvent(new CustomEvent('techlab:sound', { detail: { on: on } }));
      return on;
    },
  };
})();
