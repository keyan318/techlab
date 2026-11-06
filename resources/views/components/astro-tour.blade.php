{{--
  Astro's tour: a step-by-step guide for students and captains (faculty), narrated by Astro.
  The words live in config/astro-tour.php (one chapter per page); this draws them:
  the page dims, a spotlight lands on one thing at a time, and Astro explains it in a
  speech bubble (typing as he talks).

  A chapter plays by itself the first time a student opens its page, but only in the session where
  their account was just created (session 'tour_fresh', set by AuthController's register/facultyRegister) (?tour=1 forces it, ?tour=0 stops it);
  finishing or skipping saves it (users.tour_seen, plus this browser) so it only comes back from the
  Tour button, which calls window.astroTour.start().

  Plain CSS + plain JS on purpose: it also runs on the lesson player, which has no Tailwind or Alpine.
  Included by components/shell/side-bar (every app page) and student/planets/course-player.
--}}
@php
  $tourUser    = auth()->user();
  $tourCaptain = $tourUser?->role === 'faculty';
  $tourKey     = null;
  foreach (config($tourCaptain ? 'astro-tour.faculty_chapters' : 'astro-tour.chapters') as $key => $chapter) {
      if (request()->routeIs(...$chapter['routes'])) { $tourKey = $key; break; }
  }
@endphp
@if ($tourUser && $tourUser->role !== 'admin' && $tourKey)
@php
  $tourChapter = config(($tourCaptain ? 'astro-tour.faculty_chapters.' : 'astro-tour.chapters.').$tourKey);
  $tourFirst   = explode(' ', trim((string) $tourUser->name))[0];
  $tourName    = $tourCaptain ? ($tourFirst ?: 'Captain') : ($tourUser->codename ?: ($tourFirst ?: 'astronaut'));
  // A step's `next` button carries the student on to the next chapter's page.
  $tourSteps   = collect($tourChapter['steps'])->map(function (array $s) {
      if (isset($s['next'])) {
          $s['next']['url'] = route($s['next']['route'], $s['next']['params'] ?? []).'?tour=1';
      }

      return $s;
  })->all();
@endphp
<div class="at-root" id="astro-tour" hidden>
  <div class="at-shade" data-at-shade></div>
  <div class="at-spot" data-at-spot aria-hidden="true"></div>

  <div class="at-bubble" data-at-bubble role="dialog" aria-modal="true" aria-labelledby="at-title" aria-describedby="at-say">
    <div class="at-astro" data-at-astro aria-hidden="true"><x-astro-mascot class="at-mascot" /></div>
    <div class="at-body">
      <p class="at-kicker"><span>Astro's tour · {{ $tourChapter['label'] }}</span><span data-at-count></span></p>
      <h2 class="at-title" id="at-title" data-at-title></h2>
      {{-- What you see types out; screen readers get the whole line at once. --}}
      <div class="at-text" data-at-text aria-hidden="true"></div>
      <p class="at-sr" id="at-say" data-at-say aria-live="polite"></p>
      <div class="at-dots" data-at-dots aria-hidden="true"></div>
      <div class="at-actions">
        <button type="button" class="at-btn at-btn-quiet" data-at-skip>Skip tour</button>
        <span class="at-gap"></span>
        <button type="button" class="at-btn at-btn-ghost" data-at-back>Back</button>
        <button type="button" class="at-btn at-btn-main" data-at-next>Next</button>
        <a class="at-btn at-btn-main" data-at-go hidden></a>
      </div>
    </div>
  </div>
</div>

<style>
  .at-root { position: fixed; inset: 0; z-index: 9000; font-family: Inter, system-ui, -apple-system, sans-serif; }
  .at-root[hidden] { display: none; }
  .at-shade { position: absolute; inset: 0; background: rgba(4, 5, 22, 0.74); transition: background-color .3s ease; }
  .at-root.has-spot .at-shade { background: transparent; }

  /* The spotlight: a see-through hole, everything around it dimmed by its giant shadow. */
  .at-spot {
    position: fixed; left: 50%; top: 50%; width: 0; height: 0; border-radius: 14px; pointer-events: none; opacity: 0;
    box-shadow: 0 0 0 3px rgba(115, 182, 255, 0.95), 0 0 26px 6px rgba(115, 182, 255, 0.45), 0 0 0 200vmax rgba(4, 5, 22, 0.74);
    transition: left .38s cubic-bezier(.32,.72,0,1), top .38s cubic-bezier(.32,.72,0,1),
                width .38s cubic-bezier(.32,.72,0,1), height .38s cubic-bezier(.32,.72,0,1), opacity .25s ease;
  }
  .at-root.has-spot .at-spot { opacity: 1; animation: at-pulse 2.2s ease-in-out infinite; }
  @keyframes at-pulse {
    0%, 100% { box-shadow: 0 0 0 3px rgba(115,182,255,.95), 0 0 26px 6px rgba(115,182,255,.45), 0 0 0 200vmax rgba(4,5,22,.74); }
    50%      { box-shadow: 0 0 0 5px rgba(245,192,74,.95), 0 0 34px 10px rgba(245,192,74,.35), 0 0 0 200vmax rgba(4,5,22,.74); }
  }

  .at-bubble {
    position: fixed; left: 16px; top: 16px; width: min(420px, calc(100vw - 32px)); box-sizing: border-box;
    display: flex; gap: 14px; padding: 18px 18px 14px; border-radius: 22px; color: #eef1ff;
    background: linear-gradient(160deg, rgba(30, 24, 86, 0.97), rgba(12, 12, 40, 0.97));
    border: 1px solid rgba(150, 170, 255, 0.28);
    box-shadow: 0 30px 70px -20px rgba(0, 0, 0, 0.8), inset 0 1px 0 rgba(255, 255, 255, 0.08);
    transition: left .38s cubic-bezier(.32,.72,0,1), top .38s cubic-bezier(.32,.72,0,1);
  }
  .at-bubble.is-pop { animation: at-pop .42s cubic-bezier(.34,1.56,.64,1); }
  @keyframes at-pop { from { opacity: 0; transform: translateY(10px) scale(.94); } to { opacity: 1; transform: none; } }
  .at-root.is-center .at-bubble { width: min(480px, calc(100vw - 32px)); flex-direction: column; align-items: center; text-align: center; padding: 26px 24px 18px; }

  .at-astro { flex: none; width: 58px; height: 58px; animation: at-float 3s ease-in-out infinite; }
  .at-root.is-center .at-astro { width: 104px; height: 104px; }
  .at-mascot { width: 100%; height: 100%; display: block; filter: drop-shadow(0 6px 14px rgba(58, 160, 255, 0.45)); }
  @keyframes at-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }

  .at-body { min-width: 0; flex: 1; }
  .at-kicker { display: flex; justify-content: space-between; gap: 10px; margin: 0; font-size: 11px; font-weight: 600;
               letter-spacing: .14em; text-transform: uppercase; color: #8fc6ff; }
  .at-root.is-center .at-kicker { justify-content: center; }
  .at-root.is-center .at-kicker span + span::before { content: '· '; }
  .at-title { margin: 6px 0 0; font-family: 'Space Grotesk', Inter, sans-serif; font-size: 19px; font-weight: 700; line-height: 1.25; letter-spacing: -0.01em; color: #fff; }
  .at-root.is-center .at-title { font-size: 23px; }
  .at-text { margin-top: 8px; font-size: 14.5px; line-height: 1.6; color: rgba(232, 236, 255, 0.92); }
  .at-text p { margin: 0 0 .55em; }
  .at-text p:last-child { margin-bottom: 0; }
  .at-text b { color: #ffd76e; font-weight: 600; }
  .at-sr { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }

  .at-dots { display: flex; gap: 5px; margin-top: 14px; flex-wrap: wrap; }
  .at-root.is-center .at-dots { justify-content: center; }
  .at-dots i { width: 7px; height: 7px; border-radius: 2px; background: rgba(150, 170, 255, 0.25); transition: background-color .2s, width .2s; }
  .at-dots i.on { width: 18px; background: #73b6ff; }
  .at-dots i.done { background: rgba(115, 182, 255, 0.6); }

  .at-actions { display: flex; align-items: center; gap: 6px; margin-top: 14px; flex-wrap: wrap; }
  .at-root.is-center .at-actions { width: 100%; }
  .at-gap { flex: 1; }
  .at-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 36px; padding: 0 14px; border-radius: 11px;
            font: 600 13.5px/1 Inter, system-ui, sans-serif; cursor: pointer; text-decoration: none; border: 0; white-space: nowrap;
            transition: background-color .15s, color .15s, transform .1s; }
  .at-btn:active { transform: scale(.96); }
  .at-btn:focus-visible { outline: 2px solid #ffd76e; outline-offset: 2px; }
  .at-btn[hidden] { display: none; }
  .at-btn-main { background: #73b6ff; color: #07142e; box-shadow: 0 4px 0 #3d7fd0; }
  .at-btn-main:hover { background: #8cc3ff; }
  .at-btn-ghost { background: rgba(255, 255, 255, 0.08); color: #eef1ff; }
  .at-btn-ghost:hover { background: rgba(255, 255, 255, 0.15); }
  .at-btn-quiet { background: transparent; color: rgba(200, 208, 240, 0.75); padding: 0 8px; }
  .at-btn-quiet:hover { color: #fff; }

  /* Phones: the bubble docks to the bottom (or the top, when the spotlight is down there). */
  @media (max-width: 640px) {
    .at-bubble { padding: 14px 14px 12px; gap: 10px; }
    .at-astro { width: 44px; height: 44px; }
    .at-text { font-size: 14px; }
  }
  @media (prefers-reduced-motion: reduce) {
    .at-spot, .at-bubble { transition: none; }
    .at-root.has-spot .at-spot, .at-astro, .at-bubble.is-pop { animation: none; }
  }
</style>

<script>
(function () {
  if (window.astroTour) return;

  var CHAPTER   = @json($tourKey);
  var STEPS     = @json($tourSteps);
  var NAME      = @json($tourName);
  var SEEN      = @json(in_array($tourKey, $tourUser->tour_seen ?? [], true));
  var FRESH     = @json((bool) session('tour_fresh'));
  var SEEN_URL  = @json(route($tourCaptain ? 'faculty.tour.seen' : 'student.tour.seen'));
  var CSRF      = @json(csrf_token());
  var LOCAL_KEY = 'astro_tour_seen_' + @json($tourUser->id);

  var root   = document.getElementById('astro-tour');
  var $      = function (sel) { return root.querySelector(sel); };
  var bubble = $('[data-at-bubble]'), spot = $('[data-at-spot]'), astro = $('[data-at-astro]');
  var titleEl = $('[data-at-title]'), textEl = $('[data-at-text]'), sayEl = $('[data-at-say]');
  var countEl = $('[data-at-count]'), dotsEl = $('[data-at-dots]');
  var nextBtn = $('[data-at-next]'), backBtn = $('[data-at-back]'), goLink = $('[data-at-go]');
  var skipBtn = $('[data-at-skip]');

  var still = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var steps = [], index = 0, target = null, typing = null, frame = null, lastRect = '', returnFocus = null;
  // Where every box the tour scrolled was before it scrolled it, so closing puts the page back (a lesson
  // would otherwise be left at Stage 5, where the step about the arrows took it).
  var scrolled = new Map();
  function rememberScroll(el) {
    for (var n = el.parentElement; n; n = n.parentElement) {
      if (n.scrollHeight > n.clientHeight && !scrolled.has(n)) scrolled.set(n, n.scrollTop);
    }
    if (!scrolled.has(window)) scrolled.set(window, window.scrollY);
  }
  function restoreScroll() {
    scrolled.forEach(function (top, el) {
      if (el === window) window.scrollTo({ top: top, behavior: 'auto' });
      else el.scrollTo({ top: top, behavior: 'auto' });
    });
    scrolled.clear();
  }

  function store(get, key, value) {
    try { return get ? localStorage.getItem(key) : localStorage.setItem(key, value); } catch (e) { return null; }
  }
  function seenHere() {
    return (store(true, LOCAL_KEY) || '').split(',').indexOf(CHAPTER) !== -1;
  }
  function markSeen() {
    var list = (store(true, LOCAL_KEY) || '').split(',').filter(Boolean);
    if (list.indexOf(CHAPTER) === -1) { list.push(CHAPTER); store(false, LOCAL_KEY, list.join(',')); }
    if (SEEN) return;
    SEEN = true;
    fetch(SEEN_URL, {
      method: 'POST', credentials: 'same-origin', keepalive: true,
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify({ chapter: CHAPTER }),
    }).catch(function () {});
  }

  /* ── Words: "**bold**" and blank lines become HTML; {name} becomes the student's name. ── */
  function escapeHtml(s) {
    return s.replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; });
  }
  function fill(s) { return String(s || '').split('{name}').join(NAME); }
  function toHtml(s) {
    return escapeHtml(fill(s)).split(/\n{2,}/).map(function (p) {
      return '<p>' + p.replace(/\*\*(.+?)\*\*/g, '<b>$1</b>').replace(/\n/g, '<br>') + '</p>';
    }).join('');
  }
  function toPlain(s) { return fill(s).replace(/\*\*/g, ''); }

  /* ── Finding a step's target: it has to be on the page and actually showing. ── */
  function visible(el) {
    if (!el) return false;
    var r = el.getBoundingClientRect();
    if (r.width < 4 || r.height < 4) return false;
    if (r.right < 0 || r.left > innerWidth) return false;   // an off-canvas drawer
    var cs = getComputedStyle(el);
    return cs.visibility !== 'hidden' && cs.display !== 'none' && parseFloat(cs.opacity || '1') > 0.05;
  }
  function find(step) {
    if (!step.target) return null;
    var list = document.querySelectorAll(step.target);
    for (var i = 0; i < list.length; i++) if (visible(list[i])) return list[i];
    return null;
  }
  /* The steps that can play right now: a missing target is skipped, or said in the middle. */
  function playable() {
    return STEPS.filter(function (s) { return !s.target || find(s) || s.missing === 'center'; });
  }

  /* ── Typing: the words appear one letter at a time while Astro's mouth moves. ── */
  function type(html) {
    clearInterval(typing);
    textEl.innerHTML = html;
    if (still) return;
    var nodes = [], walker = document.createTreeWalker(textEl, NodeFilter.SHOW_TEXT);
    while (walker.nextNode()) nodes.push({ node: walker.currentNode, full: walker.currentNode.nodeValue });
    nodes.forEach(function (n) { n.node.nodeValue = ''; });
    var at = 0, pos = 0;
    astro.classList.add('astro-talking');
    typing = setInterval(function () {
      for (var k = 0; k < 2; k++) {                       // two letters per tick: quick, still readable
        if (at >= nodes.length) { finishTyping(); return; }
        var n = nodes[at];
        if (pos >= n.full.length) { at++; pos = 0; continue; }
        pos += n.full.codePointAt(pos) > 0xffff ? 2 : 1;      // never split an emoji in half
        n.node.nodeValue = n.full.slice(0, pos);
      }
    }, 22);
    textEl._nodes = nodes;
  }
  function finishTyping() {
    if (typing === null) return false;
    clearInterval(typing);
    typing = null;
    (textEl._nodes || []).forEach(function (n) { n.node.nodeValue = n.full; });
    astro.classList.remove('astro-talking');
    return true;
  }

  /* ── Placing the spotlight and the bubble ── */
  function clamp(v, lo, hi) { return Math.max(lo, Math.min(v, hi)); }
  function place() {
    var vw = innerWidth, vh = innerHeight, m = 16, pad = 8;
    var bw = bubble.offsetWidth, bh = bubble.offsetHeight;
    if (!target) {
      root.classList.remove('has-spot');
      bubble.style.left = Math.round((vw - bw) / 2) + 'px';
      bubble.style.top  = Math.round(clamp((vh - bh) / 2, m, vh - bh - m)) + 'px';
      return;
    }
    var r = target.getBoundingClientRect();
    // Spotlight: the part of the target that is on screen, with a little breathing room.
    var s = { l: Math.max(r.left - pad, 4), t: Math.max(r.top - pad, 4), r: Math.min(r.right + pad, vw - 4), b: Math.min(r.bottom + pad, vh - 4) };
    root.classList.add('has-spot');
    spot.style.left = s.l + 'px'; spot.style.top = s.t + 'px';
    spot.style.width = Math.max(s.r - s.l, 0) + 'px'; spot.style.height = Math.max(s.b - s.t, 0) + 'px';

    var gap = 14, x, y;
    if (vw <= 640) {
      // Phones: dock to whichever end of the screen the spotlight isn't in.
      x = (vw - bw) / 2;
      y = (s.t + s.b) / 2 > vh / 2 ? m : vh - bh - m;
    } else if (s.r + gap + bw <= vw - m && (r.right < vw * 0.25 || s.b - s.t > vh * 0.5)) {
      x = s.r + gap; y = clamp(s.t, m, vh - bh - m);                 // right of it (the sidebar, tall things)
    } else if (s.b + gap + bh <= vh - m) {
      x = clamp(s.l, m, vw - bw - m); y = s.b + gap;                  // below it
    } else if (s.t - gap - bh >= m) {
      x = clamp(s.l, m, vw - bw - m); y = s.t - gap - bh;             // above it
    } else if (s.l - gap - bw >= m) {
      x = s.l - gap - bw; y = clamp(s.t, m, vh - bh - m);             // left of it
    } else {
      x = vw - bw - m * 2; y = vh - bh - m * 2;                       // it fills the screen: sit in the corner
    }
    bubble.style.left = Math.round(x) + 'px';
    bubble.style.top  = Math.round(y) + 'px';
  }
  /* Keep up with scrolling, resizing and the sidebar sliding open. */
  function follow() {
    frame = requestAnimationFrame(follow);
    var r = target ? target.getBoundingClientRect() : null;
    var key = (r ? [r.left, r.top, r.width, r.height].map(Math.round).join() : '') + '|' + innerWidth + 'x' + innerHeight + '|' + bubble.offsetHeight;
    if (key !== lastRect) { lastRect = key; place(); }
  }

  /* ── Showing one step ── */
  function show(i) {
    index = i;
    var step = steps[i];
    target = find(step);
    var centered = !target;
    root.classList.toggle('is-center', centered);

    countEl.textContent = (i + 1) + ' / ' + steps.length;
    titleEl.textContent = fill(step.title);
    sayEl.textContent = toPlain(step.title) + '. ' + toPlain(step.text);
    dotsEl.innerHTML = steps.map(function (_, k) { return '<i class="' + (k === i ? 'on' : k < i ? 'done' : '') + '"></i>'; }).join('');

    var last = i === steps.length - 1;
    backBtn.hidden = i === 0;
    skipBtn.hidden = last;
    nextBtn.textContent = last ? (step.next ? 'Finish' : "Let's go!") : 'Next';
    nextBtn.className = 'at-btn ' + (last && step.next ? 'at-btn-ghost' : 'at-btn-main');
    goLink.hidden = !(last && step.next);
    if (last && step.next) { goLink.href = step.next.url; goLink.textContent = step.next.label + ' →'; }

    // Lock the bubble to the size of the full text, so it doesn't jump around while Astro types.
    textEl.innerHTML = toHtml(step.text);
    bubble.style.minHeight = '';
    bubble.style.minHeight = bubble.offsetHeight + 'px';

    if (target) {
      var r = target.getBoundingClientRect();
      if (r.top < 0 || r.bottom > innerHeight) { rememberScroll(target); target.scrollIntoView({ block: r.height > innerHeight * 0.8 ? 'start' : 'center', behavior: still ? 'auto' : 'smooth' }); }
    }
    lastRect = '';
    bubble.classList.remove('is-pop'); void bubble.offsetWidth; bubble.classList.add('is-pop');
    type(toHtml(step.text));
    ((last && step.next) ? goLink : nextBtn).focus({ preventScroll: true });
  }

  function next() {
    if (finishTyping()) return;                 // first press finishes the sentence, the second moves on
    if (index < steps.length - 1) show(index + 1); else close(true);
  }
  function back() { finishTyping(); if (index > 0) show(index - 1); }

  function onKey(e) {
    if (root.hidden) return;
    if (e.key === 'Escape') { e.preventDefault(); close(true); }
    else if (e.key === 'ArrowRight') { e.preventDefault(); next(); }
    else if (e.key === 'ArrowLeft') { e.preventDefault(); back(); }
    else if (e.key === 'Tab') {                   // keep keyboard focus inside the bubble
      var f = Array.from(bubble.querySelectorAll('button, a[href]')).filter(function (el) { return !el.hidden && el.offsetParent; });
      if (!f.length) return;
      var at = f.indexOf(document.activeElement);
      if (e.shiftKey && at <= 0) { e.preventDefault(); f[f.length - 1].focus(); }
      else if (!e.shiftKey && at === f.length - 1) { e.preventDefault(); f[0].focus(); }
    }
  }

  function start() {
    steps = playable();
    if (!steps.length) return;
    returnFocus = document.activeElement;
    root.hidden = false;
    document.addEventListener('keydown', onKey, true);
    cancelAnimationFrame(frame);
    follow();
    show(0);
  }
  function close(done) {
    finishTyping();
    cancelAnimationFrame(frame);
    root.hidden = true;
    root.classList.remove('has-spot');
    document.removeEventListener('keydown', onKey, true);
    if (done) markSeen();
    restoreScroll();
    if (returnFocus && returnFocus.focus) returnFocus.focus({ preventScroll: true });
  }

  nextBtn.addEventListener('click', next);
  backBtn.addEventListener('click', back);
  skipBtn.addEventListener('click', function () { close(true); });
  goLink.addEventListener('click', function () { markSeen(); });
  // Clicking the dim area finishes Astro's sentence, so a click never feels ignored.
  $('[data-at-shade]').addEventListener('click', function () { finishTyping(); });

  window.astroTour = { start: start, close: close, chapter: CHAPTER };

  /* ── First visit to this page? Astro starts by himself, once the page has settled. ── */
  var params = new URLSearchParams(location.search);
  var forced = params.get('tour') === '1', stopped = params.get('tour') === '0';
  if (params.has('tour')) {
    params.delete('tour');
    var q = params.toString();
    history.replaceState(history.state, '', location.pathname + (q ? '?' + q : '') + location.hash);
  }
  if (forced || (FRESH && !SEEN && !seenHere() && !stopped)) {
    var go = function () { setTimeout(start, forced ? 450 : 900); };
    if (document.readyState === 'complete') go(); else addEventListener('load', go, { once: true });
  }
})();
</script>
@endif
