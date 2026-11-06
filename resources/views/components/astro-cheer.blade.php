{{-- Astro's "you did it!" pop-up: Astro jumps for joy (the .astro-cheer mood in <x-pixel-mascot>),
     pixel stars fly out, and a card shows the XP and the way on. It sits in the middle of the screen
     over a dark, blurred backdrop, so the celebration is the only thing to look at.

     Pages call window.astroCheer.show({ kicker, xp, nextUrl }) once a pass is saved; the caller
     plays the sound, because a lab with music already plays its own fanfare. Escape, ✕, "Keep
     exploring" or a click on the backdrop closes it. --}}
<div class="acheer" id="astro-cheer" hidden>
  <div class="acheer-backdrop" data-acheer-close></div>
  <div class="acheer-card" role="dialog" aria-modal="true" aria-labelledby="acheer-title">
    <button type="button" class="acheer-close" data-acheer-close aria-label="Close">✕</button>
    <div class="acheer-astro astro-cheer"><x-astro-mascot /></div>
    <div class="acheer-body">
      <p class="acheer-kicker" data-acheer-kicker>Lab complete</p>
      <p class="acheer-title" id="acheer-title" data-acheer-title>Woohoo! You did it, astronaut!</p>
      <p class="acheer-xp" data-acheer-xp hidden></p>
      <div class="acheer-actions">
        <a class="acheer-next" data-acheer-next href="#" hidden>Next lesson →</a>
        <button type="button" class="acheer-stay" data-acheer-close>Keep exploring</button>
      </div>
    </div>
  </div>
</div>
@once
<style>
  .acheer { position: fixed; inset: 0; z-index: 9000; display: grid; place-items: center; padding: 16px; font-family: 'Inter', system-ui, sans-serif; }
  .acheer[hidden] { display: none; }
  .acheer-backdrop {
    position: absolute; inset: 0; background: rgba(4, 6, 24, .78);
    -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px);
    animation: acheer-fade .3s ease both;
  }
  .acheer.leaving .acheer-backdrop { animation: acheer-fade-out .22s ease-in both; }
  .acheer-card {
    position: relative; width: min(480px, 100%); display: flex; align-items: center; gap: 16px; padding: 20px 22px 20px 16px;
    border-radius: 20px; border: 2px solid rgba(245, 192, 74, .7);
    background: linear-gradient(160deg, #18245a 0%, #0c1436 100%); color: #eaf0ff;
    box-shadow: 0 18px 50px rgba(5, 8, 30, .7), 0 0 0 6px rgba(115, 182, 255, .12);
    animation: acheer-in .55s cubic-bezier(.3, 1.5, .5, 1) both;
  }
  .acheer.leaving .acheer-card { animation: acheer-out .22s ease-in both; }
  .acheer-astro { flex: none; width: 104px; height: 104px; display: grid; place-items: end center; }
  .acheer-astro svg { width: 96px; height: auto; image-rendering: pixelated; }
  .acheer-body { min-width: 0; flex: 1; }
  .acheer-kicker { margin: 0; font-size: 11px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: #73b6ff; }
  .acheer-title { margin: 2px 0 0; font-family: 'Space Grotesk', 'Inter', sans-serif; font-size: 19px; font-weight: 700; line-height: 1.2; }
  .acheer-xp { display: inline-block; margin: 8px 0 0; padding: 3px 10px; border-radius: 999px; background: #f5c04a; color: #2a1a00; font-weight: 800; font-size: 13px; }
  .acheer-xp[hidden] { display: none; }
  .acheer-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
  .acheer-next, .acheer-stay { padding: 8px 14px; border-radius: 10px; font: 700 14px/1.2 'Inter', system-ui, sans-serif; text-decoration: none; cursor: pointer; }
  .acheer-next { background: #73b6ff; color: #07142e; border: 0; }
  .acheer-next:hover { background: #8cc3ff; }
  .acheer-next[hidden] { display: none; }
  .acheer-stay { background: rgba(255, 255, 255, .06); color: #eaf0ff; border: 1px solid rgba(255, 255, 255, .18); }
  .acheer-stay:hover { background: rgba(255, 255, 255, .14); }
  .acheer-close { position: absolute; top: 8px; right: 10px; border: 0; background: none; color: #9aa6d6; font-size: 14px; cursor: pointer; padding: 4px; }
  .acheer-close:hover { color: #fff; }
  .acheer-next:focus-visible, .acheer-stay:focus-visible, .acheer-close:focus-visible { outline: 2px solid #f5c04a; outline-offset: 2px; }
  @keyframes acheer-in { 0% { opacity: 0; transform: translateY(24px) scale(.8) } 100% { opacity: 1; transform: none } }
  @keyframes acheer-fade { from { opacity: 0 } to { opacity: 1 } }
  @keyframes acheer-fade-out { to { opacity: 0 } }
  @keyframes acheer-out { to { opacity: 0; transform: translateY(24px) scale(.95) } }
  @media (max-width: 420px) { .acheer-astro { width: 68px; height: 68px; } .acheer-astro svg { width: 62px; } .acheer-title { font-size: 17px; } }
  @media (prefers-reduced-motion: reduce) { .acheer-card, .acheer.leaving .acheer-card, .acheer-backdrop, .acheer.leaving .acheer-backdrop { animation: none; } }
</style>
<script>
  (function () {
    var root = document.getElementById('astro-cheer');
    if (!root || window.astroCheer) return;
    var still = matchMedia('(prefers-reduced-motion: reduce)').matches;
    var TITLES = ['Woohoo! You did it, astronaut!', 'Mission accomplished, astronaut!', 'Stellar work, astronaut!', 'Yes! That was out of this world!'];
    var BITS = ['#f5c04a', '#73b6ff', '#ffffff', '#ff9ec4'];
    var bursts = [];
    var returnFocus = null;

    // Pixel stars that pop out of Astro and float away.
    function burst() {
      var astro = root.querySelector('.acheer-astro');
      var r = astro.getBoundingClientRect();
      if (!r.width) return;
      for (var i = 0; i < 7; i++) {
        var bit = document.createElement('div');
        var size = 6 + Math.round(Math.random() * 6);
        bit.setAttribute('aria-hidden', 'true');
        bit.style.cssText = 'position:fixed;pointer-events:none;z-index:9001;width:' + size + 'px;height:' + size + 'px;left:' + (r.left + r.width / 2) + 'px;top:' + (r.top + r.height * 0.35) + 'px;background:' + BITS[i % BITS.length] + ';';
        document.body.appendChild(bit);
        var angle = -Math.PI / 2 + (i - 3) * 0.42 + (Math.random() - 0.5) * 0.3;
        var dist = 60 + Math.random() * 50;
        var dx = Math.cos(angle) * dist, dy = Math.sin(angle) * dist;
        bit.animate([
          { transform: 'translate(-50%, -50%) scale(.4) rotate(0)', opacity: 1 },
          { transform: 'translate(calc(-50% + ' + dx + 'px), calc(-50% + ' + dy + 'px)) scale(1) rotate(90deg)', opacity: 1, offset: .6 },
          { transform: 'translate(calc(-50% + ' + dx * 1.1 + 'px), calc(-50% + ' + (dy + 30) + 'px)) scale(.7) rotate(180deg)', opacity: 0 },
        ], { duration: 900 + i * 50, easing: 'cubic-bezier(.2, .8, .3, 1)' }).onfinish = bit.remove.bind(bit);
      }
    }

    function hide() {
      if (root.hidden) return;
      bursts.forEach(clearTimeout); bursts = [];
      if (returnFocus && returnFocus.focus) returnFocus.focus();
      if (still) { root.hidden = true; return; }
      root.classList.add('leaving');
      setTimeout(function () { root.hidden = true; root.classList.remove('leaving'); }, 220);
    }

    function show(opts) {
      opts = opts || {};
      root.querySelector('[data-acheer-kicker]').textContent = opts.kicker || 'Lab complete';
      root.querySelector('[data-acheer-title]').textContent = TITLES[Math.floor(Math.random() * TITLES.length)];
      var xp = root.querySelector('[data-acheer-xp]');
      xp.hidden = !opts.xp;
      xp.textContent = '+' + opts.xp + ' XP';
      var next = root.querySelector('[data-acheer-next]');
      next.hidden = !opts.nextUrl;
      if (opts.nextUrl) next.href = opts.nextUrl;
      returnFocus = document.activeElement;
      root.classList.remove('leaving');
      root.hidden = false;
      (opts.nextUrl ? next : root.querySelector('.acheer-stay')).focus();
      bursts.forEach(clearTimeout);
      bursts = still ? [] : [150, 950, 1750].map(function (ms) { return setTimeout(burst, ms); });
    }

    root.querySelectorAll('[data-acheer-close]').forEach(function (b) { b.addEventListener('click', hide); });
    document.addEventListener('keydown', function (e) {
      if (root.hidden) return;
      if (e.key === 'Escape') { hide(); return; }
      if (e.key !== 'Tab') return;
      var items = Array.prototype.filter.call(root.querySelectorAll('a[href], button'), function (el) { return !el.hidden && el.offsetParent !== null; });
      var first = items[0], last = items[items.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    });

    window.astroCheer = { show: show, hide: hide };
  })();
</script>
@endonce
