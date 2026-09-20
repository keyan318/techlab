{{--
  resources/views/teacher/partials/calendar-drawer.blade.php
  The teacher's weekly calendar: a right-hand drawer opened from the "Calendar" rail icon or the
  dashboard's "View Calendar" link. Both drive the same Alpine store ($store.calendar), which is also
  the single source of truth for the schedule — the dashboard card seeds it, the drawer reads it.
  Included by components/shell/side-bar.blade.php for teachers only, so it exists on every teacher page.
--}}

<style>
  .cal-scrim { position: fixed; inset: 0; z-index: 60; background: var(--overlay); }
  .cal-panel { position: fixed; top: 0; right: 0; bottom: 0; z-index: 61; width: min(100vw, 540px); display: flex; flex-direction: column;
               background: var(--panel-solid); border-left: 1px solid var(--glass-border); color: var(--text);
               box-shadow: -24px 0 60px -18px rgba(0,0,0,.6); backdrop-filter: blur(24px) saturate(160%); }
  .cal-t { transition: transform 320ms cubic-bezier(.32,.72,0,1), opacity 220ms ease; }
  .cal-out { transform: translateX(28px); opacity: 0; }
  .cal-in { transform: none; opacity: 1; }
  .cal-fade { transition: opacity 200ms ease; }
  .cal-fade-out { opacity: 0; } .cal-fade-in { opacity: 1; }
  @media (prefers-reduced-motion: reduce) { .cal-t { transition: opacity 150ms linear; } .cal-out { transform: none; } }

  .cal-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 20px 20px 12px; }
  .cal-head h2 { font: 700 1.25rem 'Space Grotesk', sans-serif; letter-spacing: -.02em; margin: 0; }
  .cal-sub { font: 500 .8rem 'Inter', sans-serif; color: var(--muted); margin: 2px 0 0; }
  .cal-x { display: grid; place-items: center; width: 34px; height: 34px; border-radius: 10px; border: 0; background: transparent; color: var(--muted); cursor: pointer; transition: background .15s ease-out, color .15s ease-out, transform .12s ease-out; }
  .cal-x:hover { background: color-mix(in srgb, var(--text) 8%, transparent); color: var(--text); }
  .cal-x:active { transform: scale(.94); }
  .cal-x:focus-visible, .cal-seg button:focus-visible, .cal-link:focus-visible { outline: 2px solid var(--blue); outline-offset: 2px; }

  .cal-seg { display: inline-flex; margin: 0 20px 12px; padding: 3px; border-radius: 11px; background: color-mix(in srgb, var(--text) 6%, transparent); align-self: flex-start; }
  .cal-seg button { border: 0; background: transparent; color: var(--muted); font: 600 .8rem 'Inter', sans-serif; padding: 6px 14px; border-radius: 8px; cursor: pointer; transition: background .15s ease-out, color .15s ease-out; }
  .cal-seg button[aria-pressed="true"] { background: color-mix(in srgb, var(--blue) 18%, transparent); color: var(--blue); }

  .cal-scroll { flex: 1; min-height: 0; overflow-y: auto; padding: 0 12px 12px 20px; }
  .cal-days { display: grid; grid-template-columns: 44px repeat(7, 1fr); position: sticky; top: 0; z-index: 3; background: var(--panel-solid); padding: 4px 0 8px; }
  .cal-dh { text-align: center; font: 700 .66rem 'Space Mono', monospace; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); }
  .cal-dh b { display: grid; place-items: center; width: 26px; height: 26px; margin: 4px auto 0; border-radius: 999px; font: 600 .8rem 'Inter', sans-serif; letter-spacing: 0; color: var(--text); }
  .cal-dh.today { color: var(--blue); } .cal-dh.today b { background: var(--blue); color: #fff; }
  .cal-body { display: grid; grid-template-columns: 44px repeat(7, 1fr); padding-top: 10px; }   /* room for the first hour label under the sticky header */
  .cal-hrs > div { height: var(--h); position: relative; font: 500 .66rem 'Inter', sans-serif; color: var(--muted); }
  .cal-hrs > div span { position: absolute; top: -7px; right: 8px; white-space: nowrap; }
  .cal-col { position: relative; height: calc(var(--h) * var(--n)); border-left: 1px solid color-mix(in srgb, var(--text) 7%, transparent);
             background-image: linear-gradient(to bottom, color-mix(in srgb, var(--text) 7%, transparent) 1px, transparent 1px); background-size: 100% var(--h); }
  .cal-col.today { background-color: color-mix(in srgb, var(--blue) 6%, transparent); }
  .cal-blk { position: absolute; box-sizing: border-box; padding: 3px 4px 3px 5px; overflow: hidden; border-radius: 7px; border-left: 3px solid var(--k);
             background: color-mix(in srgb, var(--k) 22%, var(--panel-solid)); color: var(--text); font: 600 .68rem/1.2 'Inter', sans-serif; cursor: default; }
  .cal-blk small { display: block; margin-top: 1px; font-weight: 500; font-size: .62rem; opacity: .75; }
  .cal-blk:hover, .cal-blk:focus-visible { z-index: 2; overflow: visible; outline: 2px solid var(--k); }
  .cal-now { position: absolute; left: 0; right: 0; height: 0; border-top: 2px solid var(--red); z-index: 2; pointer-events: none; }
  .cal-now::before { content: ""; position: absolute; left: -4px; top: -5px; width: 8px; height: 8px; border-radius: 999px; background: var(--red); }

  .cal-ag { display: grid; gap: 6px; padding-top: 4px; }
  .cal-ag h3 { font: 700 .68rem 'Space Mono', monospace; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); margin: 14px 2px 2px; display: flex; gap: 8px; align-items: center; }
  .cal-ag h3 em { font-style: normal; color: #fff; background: var(--blue); border-radius: 999px; padding: 1px 8px; letter-spacing: .06em; }
  .cal-row { display: grid; grid-template-columns: 70px 1fr; border-radius: 12px; overflow: hidden; background: color-mix(in srgb, var(--text) 4%, transparent); border-left: 3px solid var(--k); }
  .cal-row .t { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px 6px; background: color-mix(in srgb, var(--k) 16%, transparent); font: 600 .9rem/1.25 'Inter', sans-serif; }
  .cal-row .t small { font-size: .72rem; opacity: .8; }
  .cal-row .m { padding: 10px 12px; min-width: 0; }
  .cal-row .m b { display: block; font: 600 .98rem 'Space Grotesk', sans-serif; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .cal-row .m span { font-size: .82rem; color: var(--muted); }

  .cal-empty { text-align: center; color: var(--muted); padding: 56px 24px; font-size: .95rem; }
  .cal-link { color: var(--blue); font-weight: 600; text-decoration: none; }
  .cal-link:hover { text-decoration: underline; }
  .cal-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 20px 16px; border-top: 1px solid var(--glass-border); font-size: .8rem; color: var(--muted); }
  .cal-foot button { border: 0; background: transparent; color: var(--muted); font: 500 .8rem 'Inter', sans-serif; padding: 6px 10px; border-radius: 999px; cursor: pointer; transition: background .15s ease-out, color .15s ease-out; }
  .cal-foot button:hover { background: color-mix(in srgb, var(--red) 14%, transparent); color: var(--red); }
</style>

<script>
  document.addEventListener('alpine:init', () => {
    // Single source of truth for the teacher's schedule. The dashboard card seeds it via set(); other pages load it on first open.
    Alpine.store('calendar', {
      open: false, classes: [], loaded: false, loading: false, error: '', view: 'week', now: new Date(), trigger: null, _tick: null,
      set(list) { this.classes = Array.isArray(list) ? list : []; this.loaded = true; },
      async load() {
        if (this.loading) return;
        this.loading = true; this.error = '';
        try {
          const r = await fetch(@json(route('teacher.schedule.index')), { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
          if (!r.ok) throw new Error('bad status');
          this.set((await r.json()).classes);
        } catch (e) { this.error = 'Could not load your schedule. Please try again.'; }
        finally { this.loading = false; }
      },
      show(el) {
        this.trigger = el || document.activeElement;
        this.now = new Date();
        if (window.innerWidth < 640) this.view = 'agenda';
        this.open = true;
        if (!this.loaded) this.load();
        clearInterval(this._tick); this._tick = setInterval(() => (this.now = new Date()), 30000);
      },
      hide() {
        this.open = false; clearInterval(this._tick); this._tick = null;
        const t = this.trigger; this.trigger = null;
        if (t && t.focus && document.contains(t)) t.focus();
      },
      toggle(el) { this.open ? this.hide() : this.show(el); },
      async remove() {
        if (!confirm('Remove your schedule?')) return false;
        try {
          const r = await fetch(@json(route('teacher.schedule.destroy')), { method: 'DELETE', credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()), 'X-Requested-With': 'XMLHttpRequest' } });
          if (!r.ok) throw new Error('bad status');
          this.set([]); return true;
        } catch (e) { this.error = 'Could not remove it. Please try again.'; return false; }
      },
    });

    Alpine.data('calendarDrawer', () => {
      const DAYS = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
      const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      const COLORS = ['--blue', '--violet', '--cyan', '--green', '--red'];
      const H = 48;   // px per hour
      const mins = t => { const [h, m] = t.split(':').map(Number); return h * 60 + m; };
      const colorOf = s => 'var(' + COLORS[[...String(s)].reduce((a, ch) => (a * 31 + ch.charCodeAt(0)) >>> 0, 7) % COLORS.length] + ')';
      return {
        H,
        get cal() { return Alpine.store('calendar'); },
        init() { this.$watch(() => Alpine.store('calendar').open, v => { if (v) this.$nextTick(() => this.$refs.close && this.$refs.close.focus()); }); },
        hm(t) { return String(Math.floor(mins(t) / 60) % 12 || 12).padStart(2, '0') + ':' + t.slice(3, 5); },
        ap(t) { return mins(t) >= 720 ? 'PM' : 'AM'; },
        span(c) { return c.start ? this.hm(c.start) + ' ' + this.ap(c.start) + (c.end ? ' – ' + this.hm(c.end) + ' ' + this.ap(c.end) : '') : ''; },
        get todayIso() { const d = this.cal.now.getDay(); return d === 0 ? 7 : d; },
        get monday() { const n = this.cal.now; return new Date(n.getFullYear(), n.getMonth(), n.getDate() - ((n.getDay() + 6) % 7)); },
        get weekDays() {
          return [1, 2, 3, 4, 5, 6, 7].map(i => { const d = new Date(this.monday); d.setDate(d.getDate() + i - 1); return { day: i, name: DAYS[i].slice(0, 3), date: d.getDate(), today: i === this.todayIso }; });
        },
        get rangeLabel() {
          const a = this.monday, b = new Date(a); b.setDate(b.getDate() + 6);
          return MONTHS[a.getMonth()] + ' ' + a.getDate() + ' – ' + (a.getMonth() === b.getMonth() ? '' : MONTHS[b.getMonth()] + ' ') + b.getDate();
        },
        // Visible hour range: from the earliest start to the latest end, at least 6 hours tall.
        get range() {
          const cs = this.cal.classes;
          if (!cs.length) return { from: 8, to: 14 };
          let from = Math.floor(Math.min(...cs.map(c => mins(c.start))) / 60);
          let to = Math.ceil(Math.max(...cs.map(c => c.end ? mins(c.end) : mins(c.start) + 60)) / 60);
          to = Math.min(24, Math.max(to, from + 6));
          from = Math.max(0, Math.min(from, to - 6));
          return { from, to };
        },
        get hours() { const r = this.range; return Array.from({ length: r.to - r.from }, (_, i) => r.from + i); },
        hourLabel(h) { return (h % 12 || 12) + ' ' + (h < 12 ? 'AM' : 'PM'); },
        blocks(day) {
          const from = this.range.from * 60;
          const items = this.cal.classes.filter(c => c.day === day)
            .map(c => ({ c, s: mins(c.start), e: c.end ? mins(c.end) : mins(c.start) + 60 }))
            .sort((a, b) => a.s - b.s || a.e - b.e);
          const lanes = [];   // overlapping classes sit side by side
          items.forEach(it => { let l = lanes.findIndex(end => end <= it.s); if (l < 0) { l = lanes.length; lanes.push(0); } lanes[l] = it.e; it.lane = l; });
          const n = Math.max(1, lanes.length);
          return items.map(it => {
            const height = Math.max((it.e - it.s) / 60 * H - 2, 24);
            return { id: it.c.id, c: it.c, tall: height >= 40,
              style: 'top:' + ((it.s - from) / 60 * H + 1) + 'px;height:' + height + 'px;left:' + (it.lane / n * 100) + '%;width:calc(' + (100 / n) + '% - 2px);--k:' + colorOf(it.c.subject) };
          });
        },
        get nowTop() {
          const n = this.cal.now, m = n.getHours() * 60 + n.getMinutes(), from = this.range.from * 60;
          return m >= from && m <= this.range.to * 60 ? (m - from) / 60 * H : null;
        },
        // Agenda: today first, then the rest of the week in order.
        get agenda() {
          const by = {}; this.cal.classes.forEach(c => (by[c.day] ||= []).push(c));
          return Object.keys(by).map(Number)
            .sort((a, b) => ((a - this.todayIso + 7) % 7) - ((b - this.todayIso + 7) % 7))
            .map(day => ({ day, name: DAYS[day], today: day === this.todayIso, items: by[day].sort((a, b) => mins(a.start) - mins(b.start)).map(c => ({ ...c, k: colorOf(c.subject) })) }));
        },
        label(c) { return [c.subject, c.class, c.room].filter(Boolean).join(' · ') + ', ' + DAYS[c.day] + ' ' + this.span(c); },
        trap(e) {
          const f = [...this.$root.querySelectorAll('button, a[href], [tabindex="0"]')].filter(x => x.offsetParent !== null);
          if (!f.length) return;
          const first = f[0], last = f[f.length - 1];
          if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
          else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        },
      };
    });
  });
</script>

<template x-teleport="body">
  <div x-data="calendarDrawer()" x-cloak>
    <div class="cal-scrim" x-show="$store.calendar.open" @click="$store.calendar.hide()"
         x-transition:enter="cal-fade" x-transition:enter-start="cal-fade-out" x-transition:enter-end="cal-fade-in"
         x-transition:leave="cal-fade" x-transition:leave-start="cal-fade-in" x-transition:leave-end="cal-fade-out" aria-hidden="true"></div>

    <aside class="cal-panel" x-show="$store.calendar.open" role="dialog" aria-modal="true" aria-labelledby="calTitle"
           @keydown.escape.window="$store.calendar.open && $store.calendar.hide()" @keydown.tab="trap($event)"
           x-transition:enter="cal-t" x-transition:enter-start="cal-out" x-transition:enter-end="cal-in"
           x-transition:leave="cal-t" x-transition:leave-start="cal-in" x-transition:leave-end="cal-out">

      <header class="cal-head">
        <div>
          <h2 id="calTitle">Calendar</h2>
          <p class="cal-sub" x-text="rangeLabel"></p>
        </div>
        <button type="button" class="cal-x" x-ref="close" @click="$store.calendar.hide()" aria-label="Close calendar">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
      </header>

      <div class="cal-seg" role="group" aria-label="Calendar view" x-show="$store.calendar.classes.length">
        <button type="button" :aria-pressed="($store.calendar.view === 'week').toString()" @click="$store.calendar.view = 'week'">Week</button>
        <button type="button" :aria-pressed="($store.calendar.view === 'agenda').toString()" @click="$store.calendar.view = 'agenda'">Agenda</button>
      </div>

      <div class="cal-scroll">
        <p class="cal-empty" x-show="$store.calendar.loading && !$store.calendar.loaded" role="status">Loading your schedule…</p>
        <p class="cal-empty" x-show="$store.calendar.error" x-text="$store.calendar.error" role="alert"></p>
        <p class="cal-empty" x-show="$store.calendar.loaded && !$store.calendar.classes.length && !$store.calendar.error">
          No classes yet. <a class="cal-link" href="{{ route('teacher.dashboard') }}">Upload your schedule</a> on the dashboard and Astro will lay it out here.
        </p>

        {{-- Week: a time grid, Monday → Sunday --}}
        {{-- object-form :style — a string :style would overwrite the display:none that x-show sets --}}
        <div x-show="$store.calendar.classes.length && $store.calendar.view === 'week'" :style="{ '--h': H + 'px', '--n': hours.length }">
          <div class="cal-days">
            <div></div>
            <template x-for="d in weekDays" :key="d.day">
              <div class="cal-dh" :class="d.today ? 'today' : ''"><span x-text="d.name"></span><b x-text="d.date"></b></div>
            </template>
          </div>
          <div class="cal-body">
            <div class="cal-hrs" aria-hidden="true">
              <template x-for="h in hours" :key="h"><div><span x-text="hourLabel(h)"></span></div></template>
            </div>
            <template x-for="d in weekDays" :key="d.day">
              <div class="cal-col" :class="d.today ? 'today' : ''" role="list" :aria-label="d.name">
                <template x-for="b in blocks(d.day)" :key="b.id">
                  <div class="cal-blk" role="listitem" tabindex="0" :style="b.style" :title="label(b.c)" :aria-label="label(b.c)">
                    <span x-text="b.c.subject"></span>
                    <small x-show="b.tall" x-text="b.c.class || hm(b.c.start)"></small>
                  </div>
                </template>
                <div class="cal-now" x-show="d.today && nowTop !== null" :style="{ top: nowTop + 'px' }" aria-hidden="true"></div>
              </div>
            </template>
          </div>
        </div>

        {{-- Agenda: a list, best on phones --}}
        <div class="cal-ag" x-show="$store.calendar.classes.length && $store.calendar.view === 'agenda'">
          <template x-for="g in agenda" :key="g.day">
            <div style="display:contents">
              <h3><span x-text="g.name"></span><em x-show="g.today">Today</em></h3>
              <template x-for="c in g.items" :key="c.id">
                <div class="cal-row" :style="'--k:' + c.k">
                  <div class="t"><span x-text="hm(c.start)"></span><small x-text="ap(c.start)"></small></div>
                  <div class="m"><b x-text="c.subject"></b><span x-text="[c.class, c.room].filter(Boolean).join(' · ') + (c.end ? (c.class || c.room ? ' · ' : '') + 'until ' + hm(c.end) + ' ' + ap(c.end) : '') || ' '"></span></div>
                </div>
              </template>
            </div>
          </template>
        </div>
      </div>

      <footer class="cal-foot" x-show="$store.calendar.classes.length">
        <span x-text="$store.calendar.classes.length + ($store.calendar.classes.length === 1 ? ' class' : ' classes') + ' per week'"></span>
        <button type="button" @click="$store.calendar.remove()">Remove schedule</button>
      </footer>
    </aside>
  </div>
</template>
