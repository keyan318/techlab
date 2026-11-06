{{-- XP · gems · day streak, for the headers of lessons, labs and editors, so a student always knows what they have.
     Students only. The numbers load after the page (the database is remote, so this keeps pages fast);
     call window.techlabStats.refresh() after anything that earns or spends XP and they update in place.
     theme: "light" (white header), "dark" (night header), or "auto" (follows html[data-theme]). --}}
@props(['theme' => 'light'])
@if (auth()->user()?->role === 'student')
<div {{ $attributes->merge(['class' => 'tl-stats tl-stats--'.$theme]) }} data-tl-stats aria-label="Your XP, gems and day streak">
  <span class="tl-stat" title="Total XP"><x-px-icon name="star" :size="16" /><b data-stat="xp">–</b><span class="tl-stat-unit">XP</span></span>
  <span class="tl-stat" title="Gems"><x-px-icon name="gem" :size="16" /><b data-stat="gems">–</b><span class="tl-sr">gems</span></span>
  <span class="tl-stat" title="Day streak"><x-px-icon name="flame" :size="16" /><b data-stat="streak">–</b><span class="tl-sr">day streak</span></span>
</div>

@once
<style>
  .tl-stats { flex: none; display: inline-flex; align-items: center; gap: 4px; padding: 3px; border-radius: 12px;
    font: 600 14px/1 'Space Grotesk', 'Inter', system-ui, sans-serif; font-variant-numeric: tabular-nums; }
  .tl-stat { display: inline-flex; align-items: center; gap: 6px; height: 30px; padding: 0 10px; border-radius: 9px; white-space: nowrap; }
  .tl-stat b { font-weight: 700; min-width: 1ch; }
  .tl-stat-unit { font-size: 12px; opacity: .7; }
  .tl-sr { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
  .tl-stat.tl-bump b { animation: tl-bump .5s cubic-bezier(.34, 1.56, .64, 1); }
  @keyframes tl-bump { 0% { transform: scale(1); } 40% { transform: scale(1.35); } 100% { transform: scale(1); } }
  .tl-stat b { display: inline-block; }

  .tl-stats--light, html[data-theme="light"] .tl-stats--auto { background: #f3f6fb; border: 1px solid rgba(0, 0, 0, .07); color: #0d0d0d; }
  .tl-stats--light .tl-stat:hover, html[data-theme="light"] .tl-stats--auto .tl-stat:hover { background: #fff; }
  .tl-stats--dark, .tl-stats--auto { background: rgba(255, 255, 255, .06); border: 1px solid rgba(255, 255, 255, .12); color: #eaf0ff; }
  .tl-stats--dark .tl-stat:hover, .tl-stats--auto .tl-stat:hover { background: rgba(255, 255, 255, .08); }

  @media (max-width: 700px) { .tl-stat { padding: 0 6px; gap: 4px; } .tl-stat-unit { display: none; } }
  @media (prefers-reduced-motion: reduce) { .tl-stat.tl-bump b { animation: none; } }
</style>
<script>
(function () {
  if (window.techlabStats) return;
  var url = @json(route('student.stats'));
  var last = null, busy = false;

  function paint(data) {
    document.querySelectorAll('[data-tl-stats] [data-stat]').forEach(function (el) {
      var key = el.getAttribute('data-stat');
      if (!(key in data)) return;
      var text = Number(data[key]).toLocaleString();
      if (el.textContent === text) return;
      el.textContent = text;
      // A number that went up after the first load gets a little bounce.
      if (last && data[key] > last[key]) {
        var chip = el.parentNode;
        chip.classList.remove('tl-bump'); void chip.offsetWidth; chip.classList.add('tl-bump');
      }
    });
    last = data;
  }

  function refresh() {
    if (busy) return;
    busy = true;
    fetch(url, { credentials: 'same-origin', cache: 'no-store', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (data) { if (data) paint(data); })
      .catch(function () {})
      .then(function () { busy = false; });
  }

  window.techlabStats = { refresh: refresh };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', refresh); else refresh();
  // Coming back to the tab (e.g. after a quiz in another tab) picks up anything that changed.
  document.addEventListener('visibilitychange', function () { if (document.visibilityState === 'visible') refresh(); });
})();
</script>
@endonce
@endif
