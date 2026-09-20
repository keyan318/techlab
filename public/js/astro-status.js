/*
 * Astro status engine — picks the short, kid-friendly line shown while Astro works.
 *
 * Deterministic on purpose: no AI request, no Math.random(). The same question and
 * context always produce the same sequence of lines, so behaviour is predictable
 * and unit-testable (see tests/js/astro-status.test.cjs).
 *
 * Works in the browser (window.AstroStatus) and in Node (module.exports).
 *
 * States (drive the logo animation via data-state):
 *   idle       calm, nothing running
 *   thinking   gentle pulse — the first moments
 *   exploring  slightly more active — a longer wait, or lesson/planet context
 *   responding settles — the answer has arrived and is fading in
 */
(function (root, factory) {
  if (typeof module === 'object' && module.exports) module.exports = factory();
  else root.AstroStatus = factory();
})(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  // First line of each context = the line the spec asks for in that situation.
  var POOLS = {
    lesson: [
      'Checking your lesson...',
      'Looking through the lesson...',
      'Exploring this topic...',
      'Finding the important bits...',
      'Connecting this to your lesson...',
      'Getting your lesson ready...'
    ],
    planet: [
      'Exploring Codexia...',
      'Following the stars...',
      'Checking the space map...',
      'Searching the galaxy...',
      'Looking for a clue...'
    ],
    code: [
      'Connecting the dots...',
      'Untangling the problem...',
      'Figuring it out...',
      'Putting the pieces together...',
      'Looking for the answer...'
    ],
    deep: [
      'Thinking it through...',
      'Putting the pieces together...',
      'Working on it...',
      'Figuring it out...',
      'Looking for the answer...'
    ],
    complex: [
      'Putting the puzzle together...',
      'Solving the puzzle...',
      'Connecting the dots...',
      'Working on it...',
      'Finding the answer...'
    ],
    simple: [
      'Looking for a clue...',
      'Following the stars...',
      'Finding the answer...',
      'Looking for the answer...'
    ]
  };

  // Shown last if Astro is still working after the pool runs out.
  var TAIL = ['Getting ready to help...', 'Bringing back an answer...'];

  var STEP_MS = 3200;              // how long each line stays before the next one
  var EXPLORE_AFTER_MS = 2500;     // thinking -> exploring, in general
  var EXPLORE_AFTER_FAST_MS = 1200; // lesson / planet questions are "exploring" by nature

  var RE_PLANET = /\b(planets?|codexia|missions?|galaxy|universe|networking|cyber\s?security|programming city|networking nebula|cybersecurity citadel)\b/i;
  var RE_CODE = /```|`[^`\n]+`|\b(python|javascript|typescript|java|php|laravel|html|css|sql|json|api|c\+\+|c#|code|coding|program(?:ming|s)?|script|function|method|variable|loop|array|syntax|compile[rd]?|debug(?:ging)?|bug|error|exception|traceback|algorithm|recursion|class|import|return|print)\b|\bdef\s|=>|==|!=/i;
  var RE_DEEP = /\b(why|prove|derive|optimi[sz]e|analy[sz]e|compare|contrast|trade-?offs?|pros and cons|difference between|step[- ]by[- ]step|in depth|in detail|evaluate|justify|reasoning|how does .{3,} work|explain (?:why|how))\b/i;

  function words(text) {
    var t = String(text || '').trim();
    return t ? t.split(/\s+/).length : 0;
  }

  // djb2 — stable across runs, used only to vary the order of the middle lines.
  function hash(text) {
    var h = 5381, s = String(text || '').trim().toLowerCase();
    for (var i = 0; i < s.length; i++) h = ((h << 5) + h + s.charCodeAt(i)) >>> 0;
    return h;
  }

  /**
   * Which situation is this question in?
   * Priority: lesson > planet > code > deep > complex > simple.
   * @param {string} text  the student's message
   * @param {Array}  refs  attached lesson sources ({planet, module, lesson}), if any
   */
  function classify(text, refs) {
    var t = String(text || '');
    var n = words(t);

    if (Array.isArray(refs) && refs.length > 0) return 'lesson';
    if (RE_PLANET.test(t)) return 'planet';
    if (RE_CODE.test(t)) return 'code';
    if ((RE_DEEP.test(t) && n >= 8) || n >= 60) return 'deep';
    if (n >= 25 || (t.match(/\?/g) || []).length >= 2 || t.split(/\n/).filter(Boolean).length >= 3) return 'complex';
    return 'simple';
  }

  /** Ordered lines for a context: the lead line first, the rest rotated by hash(text). */
  function sequence(ctx, text) {
    var pool = POOLS[ctx] || POOLS.simple;
    var rest = pool.slice(1);
    if (rest.length) {
      var k = hash(text) % rest.length;
      rest = rest.slice(k).concat(rest.slice(0, k));
    }
    var out = [], seen = {};
    [pool[0]].concat(rest, TAIL).forEach(function (line) {
      if (!seen[line]) { seen[line] = true; out.push(line); }
    });
    return out;
  }

  /** The status for a given moment in the wait. Pure: same inputs, same output. */
  function at(ctx, text, elapsedMs) {
    var seq = sequence(ctx, text);
    var i = Math.min(Math.floor(Math.max(0, elapsedMs) / STEP_MS), seq.length - 1);
    var after = (ctx === 'lesson' || ctx === 'planet') ? EXPLORE_AFTER_FAST_MS : EXPLORE_AFTER_MS;
    return { ctx: ctx, state: elapsedMs >= after ? 'exploring' : 'thinking', text: seq[i] };
  }

  /** Status at t=0 — used to seed the pending message so it never renders blank. */
  function first(text, refs) {
    return at(classify(text, refs), text, 0);
  }

  /**
   * Keep a pending message's status up to date until stop()/settle().
   * onUpdate(status) is only called when something actually changed.
   * Returns { stop(), settle() }.
   */
  function track(text, refs, onUpdate, opts) {
    opts = opts || {};
    var ctx = classify(text, refs);
    var now = opts.now || function () { return Date.now(); };
    var tickMs = opts.tickMs || 400;
    var start = now();
    var cur = at(ctx, text, 0);
    var done = false;

    var timer = setInterval(function () {
      if (done) return;
      var next = at(ctx, text, now() - start);
      if (next.text !== cur.text || next.state !== cur.state) {
        cur = next;
        onUpdate(cur);
      }
    }, tickMs);

    function stop() { done = true; clearInterval(timer); }
    function settle() {
      if (done) return;
      cur = { ctx: ctx, state: 'responding', text: cur.text };
      onUpdate(cur);
      stop();
    }
    return { stop: stop, settle: settle };
  }

  /**
   * Ease the animated logo home instead of snapping. When data-state flips to
   * "responding" the CSS animations are removed, and browsers can't transition
   * out of an animated value — so grab the live transform first and animate it to
   * rest with the Web Animations API. Call this just BEFORE the state changes.
   * A no-op outside a browser and under prefers-reduced-motion.
   */
  function settleDom(scope) {
    if (typeof document === 'undefined' || typeof getComputedStyle !== 'function') return;
    if (typeof matchMedia === 'function' && matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    var root = scope || document;
    var rows = root.querySelectorAll('.astro-resp');
    for (var i = 0; i < rows.length; i++) {
      if (rows[i].offsetParent === null) continue; // hidden rows (other messages) aren't animating
      var parts = rows[i].querySelectorAll('.astro-logo, .astro-logo img');
      for (var j = 0; j < parts.length; j++) {
        var el = parts[j];
        var t = getComputedStyle(el).transform;
        if (!t || t === 'none' || typeof el.animate !== 'function') continue;
        el.animate([{ transform: t }, { transform: 'none' }], { duration: 380, easing: 'cubic-bezier(0.32, 0.72, 0, 1)' });
      }
    }
  }

  return {
    classify: classify,
    sequence: sequence,
    at: at,
    first: first,
    track: track,
    settleDom: settleDom,
    POOLS: POOLS,
    TAIL: TAIL,
    STEP_MS: STEP_MS
  };
});
