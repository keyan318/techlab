// TechLab game sound: 8-bit blips and a soft space tune, synthesised live with the Web Audio API (no sound files).
// Browsers only let a page make sound after a click or key press, so nothing plays until the player turns sound on.
// The choice is remembered, and on the next visit sound wakes up with the player's first click or key press.
//
//   GameSound.play('coin')   one effect (see SOUNDS below); does nothing while sound is off
//   GameSound.set(true)      turn sound and music on or off
//   GameSound.on             is sound on right now
window.GameSound = (function () {
    var AC = window.AudioContext || window.webkitAudioContext, KEY = 'techlab-sound';
    var ctx = null, sfxBus, musicBus, echo, noise, on = false, last = {};

    try { on = !!AC && localStorage.getItem(KEY) === 'on'; } catch (e) {}

    function midi(m) { return 440 * Math.pow(2, (m - 69) / 12); }

    // The music is a slow arpeggio: one eighth note per step.
    var BPM = 84, STEP = 60 / BPM / 2;

    // Built on the first click or key press: effects and music each get a volume knob, then a limiter so stacked sounds never crackle.
    function wake() {
        if (!AC) return false;
        if (!ctx) {
            ctx = new AC();
            var limit = ctx.createDynamicsCompressor();
            limit.threshold.value = -10; limit.ratio.value = 12;
            limit.connect(ctx.destination);
            var soft = ctx.createBiquadFilter(); soft.type = 'lowpass'; soft.frequency.value = 7000;   // takes the fizz off the square waves
            soft.connect(limit);
            sfxBus = ctx.createGain(); sfxBus.gain.value = .55; sfxBus.connect(soft);
            musicBus = ctx.createGain(); musicBus.gain.value = 0; musicBus.connect(limit);
            // Echo for the music, so notes trail off into space
            echo = ctx.createDelay(2); echo.delayTime.value = STEP * 3;
            var fb = ctx.createGain(); fb.gain.value = .38;
            var dull = ctx.createBiquadFilter(); dull.type = 'lowpass'; dull.frequency.value = 2200;
            echo.connect(dull); dull.connect(fb); fb.connect(echo); dull.connect(musicBus);
            // One second of white noise, reused for whooshes and flames
            noise = ctx.createBuffer(1, ctx.sampleRate, ctx.sampleRate);
            var d = noise.getChannelData(0);
            for (var i = 0; i < d.length; i++) d[i] = Math.random() * 2 - 1;
        }
        if (ctx.state === 'suspended' && !document.hidden) ctx.resume();
        return true;
    }

    // One note: a wave that can slide from f to f2, with a quick attack and a fade out.
    function tone(o) {
        var t = (o.at || ctx.currentTime) + (o.t || 0), d = o.d || .1;
        var osc = ctx.createOscillator(), g = ctx.createGain();
        osc.type = o.type || 'square';
        osc.frequency.setValueAtTime(o.f, t);
        if (o.f2) osc.frequency.exponentialRampToValueAtTime(o.f2, t + d);
        g.gain.setValueAtTime(.0001, t);
        g.gain.exponentialRampToValueAtTime(o.v || .1, t + (o.a || .005));
        g.gain.exponentialRampToValueAtTime(.0001, t + d);
        osc.connect(g); g.connect(o.to || sfxBus);
        if (o.wet) g.connect(echo);
        osc.start(t); osc.stop(t + d + .02);
    }

    // A burst of filtered noise: air, fire, a whoosh.
    function hiss(o) {
        var t = ctx.currentTime + (o.t || 0), d = o.d || .3;
        var src = ctx.createBufferSource(), f = ctx.createBiquadFilter(), g = ctx.createGain();
        src.buffer = noise; src.loop = true;
        f.type = o.type || 'bandpass'; f.Q.value = o.q || 1;
        f.frequency.setValueAtTime(o.f, t);
        f.frequency.exponentialRampToValueAtTime(o.f2 || o.f, t + d);
        g.gain.setValueAtTime(.0001, t);
        g.gain.exponentialRampToValueAtTime(o.v || .2, t + d * .3);
        g.gain.exponentialRampToValueAtTime(.0001, t + d);
        src.connect(f); f.connect(g); g.connect(sfxBus);
        src.start(t, Math.random() * .5); src.stop(t + d + .02);
    }

    var SOUNDS = {
        hover:  function (x) { var f = midi(81 + (x || 0)); tone({ f: f, f2: f * 1.5, d: .05, v: .05 }); },
        select: function () { tone({ f: midi(76), d: .08, v: .08 }); tone({ f: midi(83), t: .07, d: .14, v: .08 }); },
        start:  function () {                                                  // "level start" fanfare: C E G C
            [72, 76, 79, 84].forEach(function (m, i) { tone({ f: midi(m), t: i * .07, d: i < 3 ? .09 : .45, v: .09 }); });
            tone({ type: 'triangle', f: midi(48), t: .21, d: .5, v: .22 });
        },
        on:     function () { [67, 72, 76, 79].forEach(function (m, i) { tone({ f: midi(m), t: i * .06, d: .12, v: .07 }); }); },
        off:    function () { tone({ f: midi(72), d: .09, v: .07 }); tone({ f: midi(64), t: .08, d: .18, v: .07 }); },
        type:   function (x) { tone({ f: midi(72 + Math.min(x || 0, 14)), d: .03, v: .03 }); },   // pitch climbs as each letter locks in
        lock:   function () { tone({ type: 'triangle', f: midi(88), d: .25, v: .12 }); tone({ type: 'triangle', f: midi(93), t: .06, d: .4, v: .1 }); },
        coin:   function () { tone({ f: midi(83), d: .08, v: .08 }); tone({ f: midi(88), t: .08, d: .4, v: .08 }); },   // B to E, the classic pickup
        zap:    function () { tone({ type: 'sawtooth', f: 1800, f2: 160, d: .2, v: .06 }); tone({ f: 2400, f2: 900, t: .02, d: .08, v: .03 }); },
        fire:   function () { hiss({ f: 300, f2: 1600, d: .45, v: .22, q: .8 }); tone({ type: 'triangle', f: 110, f2: 240, d: .3, v: .08 }); },
        gem:    function () { [91, 96, 100, 103].forEach(function (m, i) { tone({ type: 'sine', f: midi(m), t: i * .05, d: .3, v: .06 }); }); },
        robot:  function () { tone({ f: 520, f2: 880, d: .09, v: .06 }); tone({ f: 880, f2: 440, t: .11, d: .12, v: .06 }); },
        deny:   function () { tone({ f: midi(55), d: .1, v: .07 }); tone({ f: midi(50), t: .1, d: .2, v: .07 }); },
        whoosh: function () { hiss({ type: 'lowpass', f: 300, f2: 2400, d: .5, v: .1, q: .7 }); },
        spin:   function () {
            tone({ type: 'triangle', f: 160, f2: 640, d: .32, v: .14 });
            tone({ type: 'triangle', f: 640, f2: 260, t: .28, d: .6, v: .1 });
            hiss({ f: 500, f2: 2000, d: .5, v: .1 });
        },
        star:   function () { tone({ type: 'sine', f: 2600, f2: 1100, d: .7, v: .025, a: .15 }); hiss({ type: 'highpass', f: 3000, f2: 6000, d: .6, v: .03 }); }
    };

    function play(name, x) {
        if (!on || !ctx || document.hidden || !SOUNDS[name]) return;
        var now = performance.now();
        if (now - (last[name] || 0) < 60) return;              // no machine-gun repeats when the pointer skims across things
        last[name] = now;
        SOUNDS[name](x);
    }

    // ---- The space tune: Am - F - C - G, a soft pad under an echoing arpeggio, and the odd twinkle up high.
    // Notes are booked a moment ahead of time, so a busy page never makes the beat stutter.
    var CHORDS = [[57, 60, 64], [53, 57, 60], [55, 60, 64], [55, 59, 62]], ARP = [0, 1, 2, 3, 2, 1, 2, 1], TWINKLE = [81, 84, 86, 88, 91, 93];
    var step = 0, nextAt = 0, timer = null;

    function beat(s, at) {
        var ch = CHORDS[Math.floor(s / 8) % CHORDS.length], i = s % 8;
        if (i === 0) ch.forEach(function (m) { tone({ type: 'sine', f: midi(m), at: at, d: STEP * 8 + .3, a: .5, v: .035, to: musicBus }); });
        var notes = [ch[0] + 12, ch[1] + 12, ch[2] + 12, ch[0] + 24];
        if (i === 0 || Math.random() < .9) tone({ type: 'triangle', f: midi(notes[ARP[i]]), at: at, d: STEP * 1.8, a: .01, v: .06, to: musicBus, wet: true });
        if (Math.random() < .1) tone({ type: 'sine', f: midi(TWINKLE[Math.floor(Math.random() * TWINKLE.length)]), at: at + STEP / 2, d: 1, a: .02, v: .03, to: musicBus, wet: true });
    }
    function schedule() {
        if (nextAt < ctx.currentTime) nextAt = ctx.currentTime + .05;     // fell behind (busy tab): carry on from now instead of playing the backlog
        while (nextAt < ctx.currentTime + .3) { beat(step++, nextAt); nextAt += STEP; }
    }
    function music(play) {
        var g = musicBus.gain, now = ctx.currentTime;
        g.cancelScheduledValues(now); g.setValueAtTime(g.value, now);
        if (play) {
            if (!timer) { step = 0; nextAt = now + .6; timer = setInterval(schedule, 80); }
            g.linearRampToValueAtTime(.55, now + 3);                         // fades in, so it never starts with a jolt
        } else {
            g.linearRampToValueAtTime(0, now + .4);
            clearInterval(timer); timer = null;
        }
    }

    function set(v) {
        if (!wake()) return;
        on = !!v;
        try { localStorage.setItem(KEY, on ? 'on' : 'off'); } catch (e) {}
        SOUNDS[on ? 'on' : 'off']();
        music(on);
    }

    // Sound was left on last visit: wake up with the first click or key press (the browser blocks it before that).
    function unlock() {
        if (!on || (ctx && ctx.state === 'running' && timer)) return;
        if (wake() && !timer) music(true);
    }
    ['pointerdown', 'pointerup', 'keydown'].forEach(function (t) { document.addEventListener(t, unlock, true); });

    // Hush in a background tab or after leaving the page; pick up again on the way back.
    function hush() { if (ctx) document.hidden ? ctx.suspend() : on && ctx.resume(); }
    document.addEventListener('visibilitychange', hush);
    addEventListener('pagehide', function () { if (ctx) ctx.suspend(); });
    addEventListener('pageshow', hush);

    return {
        supported: !!AC,
        play: play,
        set: set,
        get on() { return on; }
    };
})();
