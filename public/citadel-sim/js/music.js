// Citadel Sim background music, played live with the Web Audio API: no audio files to download or license.
// Two moods: "threat" (a dark, pulsing hacker loop) while objectives are open, and "success" (a short
// fanfare, then a bright, calm loop) once every objective is clear. The more objectives the student
// clears, the brighter the threat loop's synths get.
//
// Click sounds come from TechLab's site-wide public/js/sfx.js (loaded by index.html), and the music shares
// its on/off switch ("techlab_sfx" in localStorage), so the sidebar's Sound button and this simulator's
// button are the same setting.
//
// Browsers only allow sound after the student interacts with the page, so the music starts on the first
// key press or click.

const MUTE_KEY = 'techlab_sfx';   // shared with public/js/sfx.js: "off" = muted
const VOLUME = 0.2;
const LOOKAHEAD = 0.12;   // seconds of notes scheduled ahead of the audio clock
const TICK_MS = 25;
const FANFARE = 2.2;      // seconds before the success loop starts

const freq = (midi) => 440 * 2 ** ((midi - 69) / 12);

// 4 bars of 16 steps (16th notes). `chord` holds the arp/pad notes, `root` the bass note (MIDI numbers).
const MOODS = {
  // D minor, i - i - VI - V: the V (A major) keeps it unresolved.
  threat: {
    bpm: 124,
    bars: [
      { root: 38, chord: [62, 65, 69] },
      { root: 38, chord: [62, 65, 69] },
      { root: 34, chord: [58, 62, 65] },
      { root: 33, chord: [57, 61, 64] },
    ],
  },
  // D major, I - V - vi - IV.
  success: {
    bpm: 96,
    bars: [
      { root: 38, chord: [62, 66, 69] },
      { root: 33, chord: [57, 61, 64] },
      { root: 35, chord: [59, 62, 66] },
      { root: 31, chord: [55, 59, 62] },
    ],
  },
};

const THREAT_BASS = [1, 0, 1, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1, 0, 2, 0];   // 2 = octave up
const THREAT_ARP = [0, 1, 2, 3, 2, 1, 0, 1, 3, 2, 1, 2, 0, 2, 3, 1];    // 3 = root an octave up
const SUCCESS_BELL = [0, 1, 2, 3, 2, 1, 2, 3];                           // one per 8th note
const GLITCH = [74, 77, 81, 86, 89, 93];

function readMuted() {
  try {
    return localStorage.getItem(MUTE_KEY) === 'off';
  } catch {
    return false;
  }
}

function writeMuted(muted) {
  try {
    localStorage.setItem(MUTE_KEY, muted ? 'off' : 'on');
  } catch {
    // Storage blocked: the choice lasts for this visit only.
  }
}

export function createMusic({ enabled, button }) {
  const AudioCtx = window.AudioContext || window.webkitAudioContext;
  if (!enabled || !button || !AudioCtx) return { update() {} };

  let muted = readMuted();
  let ctx = null;
  let master = null;
  let noiseBuf = null;
  let buses = null;
  let timer = null;
  let mood = 'threat';
  let intensity = 0;
  let step = 0;
  let loop = 0;
  let nextTime = 0;

  // ---------- instruments ----------

  // One oscillator note. With `release` it holds, then fades (pads, brass); without, it decays (plucks).
  function voice(bus, t, o) {
    const osc = ctx.createOscillator();
    osc.type = o.type || 'sine';
    osc.frequency.setValueAtTime(o.f, t);
    if (o.to) osc.frequency.exponentialRampToValueAtTime(o.to, t + (o.glide || o.dur));
    if (o.detune) osc.detune.value = o.detune;

    const amp = ctx.createGain();
    const attack = o.attack || 0.004;
    amp.gain.setValueAtTime(0.0001, t);
    amp.gain.linearRampToValueAtTime(o.gain, t + attack);
    if (o.release) {
      amp.gain.setValueAtTime(o.gain, t + o.dur - o.release);
      amp.gain.linearRampToValueAtTime(0.0001, t + o.dur);
    } else {
      amp.gain.exponentialRampToValueAtTime(0.0001, t + o.dur);
    }

    let head = osc;
    if (o.cutoff) {
      const filter = ctx.createBiquadFilter();
      filter.type = 'lowpass';
      filter.Q.value = o.q || 1;
      filter.frequency.setValueAtTime(o.cutoff, t);
      if (o.cutoffTo) filter.frequency.exponentialRampToValueAtTime(o.cutoffTo, t + (o.sweep || o.dur));
      osc.connect(filter);
      head = filter;
    }
    head.connect(amp);
    amp.connect(bus.out);
    if (o.echo) amp.connect(bus.echo);

    osc.start(t);
    osc.stop(t + o.dur + 0.05);
  }

  // Filtered white noise: drums, hats, risers, cymbals. `rise` swells up instead of decaying.
  function noise(bus, t, o) {
    const src = ctx.createBufferSource();
    src.buffer = noiseBuf;
    src.loop = true;
    const filter = ctx.createBiquadFilter();
    filter.type = o.filter;
    filter.Q.value = o.q || 1;
    filter.frequency.setValueAtTime(o.cutoff, t);
    if (o.cutoffTo) filter.frequency.exponentialRampToValueAtTime(o.cutoffTo, t + o.dur);

    const amp = ctx.createGain();
    amp.gain.setValueAtTime(0.0001, t);
    if (o.rise) {
      amp.gain.exponentialRampToValueAtTime(o.gain, t + o.dur - 0.02);
      amp.gain.linearRampToValueAtTime(0.0001, t + o.dur);
    } else {
      amp.gain.linearRampToValueAtTime(o.gain, t + 0.002);
      amp.gain.exponentialRampToValueAtTime(0.0001, t + o.dur);
    }

    src.connect(filter).connect(amp).connect(bus.out);
    src.start(t, Math.random() * 0.5);
    src.stop(t + o.dur + 0.05);
  }

  function kick(bus, t, gain) {
    voice(bus, t, { f: 140, to: 42, glide: 0.12, dur: 0.3, gain });
  }

  function snare(bus, t, gain) {
    noise(bus, t, { filter: 'bandpass', cutoff: 1900, q: 0.8, dur: 0.14, gain });
    voice(bus, t, { type: 'triangle', f: 190, to: 150, dur: 0.08, gain: gain * 0.5 });
  }

  function pad(bus, t, notes, dur, type, cutoff, gain) {
    notes.forEach((n) => {
      [-9, 9].forEach((detune) => voice(bus, t, { type, f: freq(n), detune, dur, gain, attack: 0.45, release: 0.35, cutoff }));
    });
  }

  // ---------- the two moods ----------

  function threatStep(t, i) {
    const bus = buses.threat;
    const barNo = i >> 4;
    const bar = MOODS.threat.bars[barNo];
    const s = i & 15;
    const beat = 60 / MOODS.threat.bpm;

    if (s === 0) pad(bus, t, bar.chord.map((n) => n - 12), beat * 4, 'sawtooth', 650, 0.028);

    const b = THREAT_BASS[s];
    if (b) {
      voice(bus, t, { type: 'sawtooth', f: freq(bar.root + (b === 2 ? 12 : 0)), dur: 0.18, gain: 0.3, cutoff: 900, cutoffTo: 160, q: 6 });
    }

    if (s === 0 || s === 8 || (s === 10 && barNo % 2 === 1)) kick(bus, t, 0.85);
    if (s === 4 || s === 12) snare(bus, t, 0.26);
    if (s % 2 === 1) noise(bus, t, { filter: 'highpass', cutoff: 8000, dur: 0.035, gain: 0.05 + 0.04 * intensity });

    // The "hacking" synth: fast square-wave notes that open up as objectives get cleared.
    const a = THREAT_ARP[s];
    const note = a === 3 ? bar.chord[0] + 12 : bar.chord[a];
    voice(bus, t, { type: 'square', f: freq(note), dur: 0.09, gain: 0.06, cutoff: 900 + 2600 * intensity, q: 8, echo: true });

    // Data blips, like a computer chattering in the background.
    if (Math.random() < 0.12) {
      voice(bus, t, { type: 'square', f: freq(GLITCH[Math.floor(Math.random() * GLITCH.length)]), dur: 0.03, gain: 0.025, echo: true });
    }

    // A soft two-tone siren every 4th loop (about every half minute), starting with the very first bar.
    if (i === 0 && loop % 4 === 0) {
      voice(bus, t, { type: 'triangle', f: 520, to: 780, dur: beat * 2, gain: 0.045, attack: 0.1, release: 0.2, cutoff: 2000, echo: true });
      voice(bus, t + beat * 2, { type: 'triangle', f: 780, to: 520, dur: beat * 2, gain: 0.045, attack: 0.1, release: 0.3, cutoff: 2000, echo: true });
    }

    // Tension riser into the top of the loop.
    if (barNo === 3 && s === 8) {
      noise(bus, t, { filter: 'bandpass', cutoff: 300, cutoffTo: 5000, q: 2, dur: beat * 2, gain: 0.12, rise: true });
    }
  }

  function successStep(t, i) {
    const bus = buses.success;
    const bar = MOODS.success.bars[i >> 4];
    const s = i & 15;
    const beat = 60 / MOODS.success.bpm;

    if (s === 0) pad(bus, t, bar.chord, beat * 4, 'triangle', 1800, 0.045);
    if (s === 0 || s === 6 || s === 8) voice(bus, t, { type: 'triangle', f: freq(bar.root + 12), dur: 0.35, gain: 0.2 });
    if (s === 0 || s === 8) kick(bus, t, 0.5);
    if (s === 4 || s === 12) snare(bus, t, 0.1);
    if (s % 2 === 0) noise(bus, t, { filter: 'highpass', cutoff: 6000, dur: 0.05, gain: 0.03 });

    if (s % 2 === 0) {
      const a = SUCCESS_BELL[s / 2];
      const note = (a === 3 ? bar.chord[0] + 12 : bar.chord[a]) + 12;
      voice(bus, t, { f: freq(note), dur: 0.5, gain: 0.08, echo: true });
      voice(bus, t, { type: 'triangle', f: freq(note + 12), dur: 0.25, gain: 0.015 });
    }
  }

  // "Da-da-da DAAA": a rising brass pickup into a big D major chord with a cymbal.
  function fanfare(t) {
    const bus = buses.success;
    [57, 62, 66].forEach((n, k) => {
      voice(bus, t + k * 0.12, { type: 'sawtooth', f: freq(n), dur: 0.14, gain: 0.12, cutoff: 600, cutoffTo: 2400, sweep: 0.08, release: 0.04 });
    });
    const hit = t + 0.38;
    [50, 57, 62, 66, 69, 74].forEach((n) => {
      voice(bus, hit, { type: 'sawtooth', f: freq(n), dur: 1.6, gain: 0.06, attack: 0.02, release: 0.9, cutoff: 500, cutoffTo: 3000, sweep: 0.3 });
    });
    kick(bus, hit, 0.9);
    noise(bus, hit, { filter: 'highpass', cutoff: 5000, dur: 1.6, gain: 0.12 });
  }

  // ---------- engine ----------

  function makeBus() {
    const out = ctx.createGain();
    out.gain.value = 0;
    out.connect(master);
    const echo = ctx.createGain();
    const delay = ctx.createDelay(1);
    const feedback = ctx.createGain();
    const wet = ctx.createGain();
    feedback.gain.value = 0.32;
    wet.gain.value = 0.3;
    echo.connect(delay);
    delay.connect(feedback).connect(delay);
    delay.connect(wet).connect(out);
    return { out, echo, delay };
  }

  function fade(bus, to, seconds) {
    const now = ctx.currentTime;
    bus.out.gain.cancelScheduledValues(now);
    bus.out.gain.setValueAtTime(bus.out.gain.value, now);
    bus.out.gain.linearRampToValueAtTime(to, now + seconds);
  }

  // Bring in `mood`'s loop; with `withFanfare`, play the fanfare first.
  function enter(withFanfare) {
    const other = mood === 'threat' ? 'success' : 'threat';
    const bpm = MOODS[mood].bpm;
    fade(buses[other], 0, 0.8);
    fade(buses[mood], 1, mood === 'threat' ? 0.6 : 0.05);
    buses[mood].delay.delayTime.value = (60 / bpm) * 0.75;   // dotted 8th echo
    step = 0;
    loop = 0;
    const start = ctx.currentTime + 0.06;
    if (withFanfare) fanfare(start);
    nextTime = start + (withFanfare ? FANFARE : 0);
  }

  function tick() {
    const now = ctx.currentTime;
    if (nextTime < now - 0.05) nextTime = now + 0.05;   // fell behind (tab was asleep): skip ahead, don't burst
    while (nextTime < now + LOOKAHEAD) {
      if (nextTime >= now) (mood === 'threat' ? threatStep : successStep)(nextTime, step);
      nextTime += 60 / MOODS[mood].bpm / 4;
      step = (step + 1) % 64;
      if (step === 0) loop++;
    }
  }

  function startTimer() {
    if (!timer) timer = setInterval(tick, TICK_MS);
  }

  function stopTimer() {
    clearInterval(timer);
    timer = null;
  }

  function start() {
    if (ctx || muted) return;
    try {
      ctx = new AudioCtx();
    } catch {
      return;   // no audio device: the lab works the same without music
    }
    master = ctx.createGain();
    master.gain.value = VOLUME;
    const comp = ctx.createDynamicsCompressor();
    master.connect(comp).connect(ctx.destination);

    noiseBuf = ctx.createBuffer(1, ctx.sampleRate, ctx.sampleRate);
    const data = noiseBuf.getChannelData(0);
    for (let k = 0; k < data.length; k++) data[k] = Math.random() * 2 - 1;

    buses = { threat: makeBus(), success: makeBus() };
    enter(false);
    startTimer();
    document.removeEventListener('pointerdown', start, true);
    document.removeEventListener('keydown', start, true);
  }

  function paintButton() {
    button.setAttribute('aria-pressed', String(!muted));
    button.textContent = muted ? '♪ Sound off' : '♪ Sound on';
    button.title = muted ? 'Turn the music and sound effects on' : 'Turn the music and sound effects off';
  }

  function setMuted(next) {
    muted = next;
    writeMuted(muted);
    paintButton();
    if (!ctx) {
      if (!muted) start();
      return;
    }
    const now = ctx.currentTime;
    master.gain.cancelScheduledValues(now);
    master.gain.setValueAtTime(master.gain.value, now);
    if (muted) {
      master.gain.linearRampToValueAtTime(0, now + 0.25);
      stopTimer();
      setTimeout(() => { if (muted) ctx.suspend(); }, 300);
    } else {
      ctx.resume();
      master.gain.linearRampToValueAtTime(VOLUME, now + 0.25);
      startTimer();
    }
  }

  button.hidden = false;
  paintButton();
  button.addEventListener('click', () => setMuted(!muted));
  document.addEventListener('pointerdown', start, true);
  document.addEventListener('keydown', start, true);

  // Silence the music while the tab is in the background.
  document.addEventListener('visibilitychange', () => {
    if (!ctx || muted) return;
    if (document.hidden) {
      stopTimer();
      ctx.suspend();
    } else {
      ctx.resume();
      startTimer();
    }
  });

  return {
    // Call after every command: switches mood when the lab is solved (or restarted) and sets the intensity.
    update(passed, total) {
      intensity = total ? passed / total : 0;
      const next = total > 0 && passed === total ? 'success' : 'threat';
      if (next === mood) return;
      mood = next;
      if (ctx) enter(mood === 'success' && !muted);
    },
  };
}
