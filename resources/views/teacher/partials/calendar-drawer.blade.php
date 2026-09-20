{{--
  resources/views/teacher/partials/calendar-drawer.blade.php
  The teacher's calendar: opened from the "Calendar" rail icon or the Classes page's "View Calendar" link.
  Both drive the same Alpine store ($store.calendar), which is also the single source of truth for the schedule —
  the Classes page seeds it, the calendar reads it and edits it (add / edit / delete).

  Year view  → the whole year, 12 small months (today + days with classes marked).
  Month view → click a month: big month grid on the left, the selected day's classes on the right.
  A weekly class shows on its weekday from its `from` date onward (the day the schedule was added).
  Included by components/shell/side-bar.blade.php for teachers only, so it exists on every teacher page.
--}}

<style>
  .cal-scrim { position: fixed; inset: 0; z-index: 60; background: var(--overlay); }
  .cal-wrap { position: fixed; inset: 0; z-index: 61; display: grid; place-items: center; padding: 16px; pointer-events: none; }
  .cal-panel { pointer-events: auto; position: relative; width: min(100%, 1120px); height: min(100%, 800px); display: flex; flex-direction: column; overflow: hidden;
               background: var(--panel-solid); border: 1px solid var(--glass-border); border-radius: 24px; color: var(--text);
               box-shadow: 0 30px 90px -20px rgba(0,0,0,.55); }
  .cal-t { transition: transform 280ms cubic-bezier(.32,.72,0,1), opacity 200ms ease; }
  .cal-out { transform: translateY(14px) scale(.985); opacity: 0; }
  .cal-in { transform: none; opacity: 1; }
  .cal-fade { transition: opacity 200ms ease; }
  .cal-fade-out { opacity: 0; } .cal-fade-in { opacity: 1; }
  @media (prefers-reduced-motion: reduce) { .cal-t { transition: opacity 150ms linear; } .cal-out { transform: none; } }

  /* ---- header ---- */
  .cal-head { display: flex; align-items: center; gap: 10px; padding: 16px 20px; border-bottom: 1px solid var(--glass-border); flex: none; }
  .cal-title { flex: 1; min-width: 0; margin: 0; text-align: center; font: 700 1.3rem 'Space Grotesk', sans-serif; letter-spacing: -.02em; }
  .cal-title small { display: block; font: 500 .74rem 'Inter', sans-serif; letter-spacing: 0; color: var(--muted); margin-top: 1px; }
  .cal-ib { display: grid; place-items: center; width: 36px; height: 36px; flex: none; border-radius: 999px; border: 0; background: transparent; color: var(--muted); cursor: pointer;
            transition: background .15s ease-out, color .15s ease-out, transform .12s ease-out; }
  .cal-ib:hover { background: color-mix(in srgb, var(--text) 8%, transparent); color: var(--text); }
  .cal-ib:active { transform: scale(.94); }
  .cal-add { background: var(--blue); color: #fff; }
  .cal-add:hover { background: color-mix(in srgb, var(--blue) 86%, #000); color: #fff; }
  .cal-pill { border: 1px solid var(--glass-border); background: transparent; color: var(--text); font: 600 .8rem 'Inter', sans-serif; padding: 7px 14px; border-radius: 999px; cursor: pointer; transition: background .15s ease-out; }
  .cal-pill:hover { background: color-mix(in srgb, var(--text) 7%, transparent); }
  .cal-back { display: inline-flex; align-items: center; gap: 4px; border: 0; background: transparent; color: var(--blue); font: 600 .9rem 'Inter', sans-serif; padding: 6px 10px 6px 6px; border-radius: 999px; cursor: pointer; }
  .cal-back:hover { background: color-mix(in srgb, var(--blue) 10%, transparent); }
  .cal-panel button:focus-visible, .cal-panel input:focus-visible, .cal-panel select:focus-visible, .cal-link:focus-visible { outline: 2px solid var(--blue); outline-offset: 2px; }

  /* ---- year ---- */
  .cal-year { flex: 1; min-height: 0; overflow-y: auto; padding: 22px 24px 28px; display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 18px 22px; align-content: start; }
  .cal-mini { display: flex; flex-direction: column; justify-content: flex-start; text-align: left; border: 1px solid transparent; background: transparent; border-radius: 16px; padding: 12px 12px 10px; cursor: pointer; color: var(--text); transition: background .15s ease-out, border-color .15s ease-out, transform .15s ease-out; }
  .cal-mini:hover { background: color-mix(in srgb, var(--text) 5%, transparent); border-color: var(--glass-border); }
  .cal-mini:active { transform: scale(.985); }
  .cal-mini h3 { margin: 0 0 8px; font: 700 1rem 'Space Grotesk', sans-serif; }
  .cal-mini.now h3 { color: var(--blue); }
  .cal-mg { display: grid; grid-template-columns: repeat(7, 1fr); row-gap: 2px; text-align: center; font: 500 .72rem 'Inter', sans-serif; }
  .cal-mg i { font-style: normal; color: var(--muted); font-weight: 600; font-size: .62rem; padding-bottom: 3px; }
  .cal-mg span { position: relative; display: grid; place-items: center; height: 24px; }
  .cal-mg span.today b { display: grid; place-items: center; width: 22px; height: 22px; border-radius: 999px; background: var(--blue); color: #fff; font-weight: 700; }
  .cal-mg span b { font-weight: 500; }
  .cal-mg span u { position: absolute; bottom: 0; width: 4px; height: 4px; border-radius: 999px; background: var(--violet); }

  /* ---- month ---- */
  .cal-month { flex: 1; min-height: 0; display: grid; grid-template-columns: minmax(0, 1.25fr) minmax(320px, 1fr); }
  .cal-left { padding: 18px 24px 24px; overflow-y: auto; border-right: 1px solid var(--glass-border); }
  .cal-wk { display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font: 600 .78rem 'Inter', sans-serif; color: var(--muted); padding: 4px 0 10px; }
  .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); row-gap: 6px; }
  .cal-day { position: relative; display: grid; place-items: center; height: 58px; border: 0; background: transparent; padding: 0; cursor: pointer; color: var(--text); }
  .cal-day b { display: grid; place-items: center; width: 46px; height: 46px; border-radius: 999px; font: 500 1.02rem 'Inter', sans-serif; transition: background .15s ease-out, color .15s ease-out, transform .12s ease-out; }
  .cal-day:hover b { background: color-mix(in srgb, var(--text) 7%, transparent); }
  .cal-day:active b { transform: scale(.94); }
  .cal-day.sel b { background: color-mix(in srgb, var(--blue) 18%, transparent); color: var(--blue); font-weight: 700; }
  .cal-day.today b { background: var(--blue); color: #fff; font-weight: 700; }
  .cal-day.today.sel b { box-shadow: 0 0 0 3px color-mix(in srgb, var(--blue) 30%, transparent); }
  .cal-day u { position: absolute; bottom: 2px; display: flex; gap: 3px; text-decoration: none; }
  .cal-day u s { width: 5px; height: 5px; border-radius: 999px; background: var(--violet); }
  .cal-day.today u s { background: var(--blue); }
  .cal-day.blank { pointer-events: none; }

  .cal-right { display: flex; flex-direction: column; min-height: 0; }
  .cal-dayhead { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 18px 22px 10px; flex: none; }
  .cal-dayhead h3 { margin: 0; font: 700 1.1rem 'Space Grotesk', sans-serif; letter-spacing: -.01em; }
  .cal-dayhead small { display: block; font: 500 .78rem 'Inter', sans-serif; color: var(--muted); margin-top: 2px; }
  .cal-list { flex: 1; min-height: 0; overflow-y: auto; padding: 4px 22px 22px; display: grid; gap: 12px; align-content: start; }
  .cal-slot { display: grid; grid-template-columns: 64px 1fr; gap: 10px; align-items: start; }
  .cal-slot > time { font: 600 .74rem 'Inter', sans-serif; color: var(--muted); padding-top: 12px; text-align: right; white-space: nowrap; }
  .cal-card { position: relative; border-radius: 16px; padding: 12px 14px; background: color-mix(in srgb, var(--k) 15%, transparent); border: 1px solid color-mix(in srgb, var(--k) 26%, transparent); }
  .cal-card b { display: block; padding-right: 64px; font: 700 1rem 'Space Grotesk', sans-serif; color: color-mix(in srgb, var(--k) 70%, var(--text)); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .cal-card p { margin: 4px 0 0; display: flex; align-items: center; gap: 6px; font: 500 .8rem 'Inter', sans-serif; color: var(--muted); }
  .cal-card p + p { margin-top: 2px; }
  .cal-acts { position: absolute; top: 8px; right: 8px; display: flex; gap: 2px; }
  .cal-acts button { display: grid; place-items: center; width: 28px; height: 28px; border: 0; border-radius: 999px; background: transparent; color: var(--muted); cursor: pointer; transition: background .15s ease-out, color .15s ease-out; }
  .cal-acts button:hover { background: color-mix(in srgb, var(--text) 10%, transparent); color: var(--text); }
  .cal-acts button.del:hover { background: color-mix(in srgb, var(--red) 16%, transparent); color: var(--red); }

  .cal-empty { text-align: center; color: var(--muted); padding: 44px 24px; font-size: .92rem; }
  .cal-empty .cal-pill { margin-top: 12px; }
  .cal-link { color: var(--blue); font-weight: 600; text-decoration: none; }
  .cal-link:hover { text-decoration: underline; }

  /* ---- form ---- */
  .cal-formscrim { position: absolute; inset: 0; z-index: 5; display: grid; place-items: center; padding: 16px; background: color-mix(in srgb, var(--panel-solid) 55%, rgba(0,0,0,.45)); backdrop-filter: blur(3px); }
  .cal-form { width: min(100%, 440px); max-height: 100%; overflow-y: auto; border-radius: 20px; padding: 20px; background: var(--panel-solid); border: 1px solid var(--glass-border); box-shadow: 0 24px 70px -18px rgba(0,0,0,.5); }
  .cal-form h3 { margin: 0 0 14px; font: 700 1.1rem 'Space Grotesk', sans-serif; }
  .cal-form label { display: block; margin-bottom: 12px; font: 600 .74rem 'Inter', sans-serif; color: var(--muted); }
  .cal-form input, .cal-form select { display: block; width: 100%; margin-top: 5px; box-sizing: border-box; padding: 9px 11px; border-radius: 11px; border: 1px solid var(--glass-border); background: color-mix(in srgb, var(--text) 4%, transparent); color: var(--text); font: 500 .9rem 'Inter', sans-serif; color-scheme: inherit; }
  .cal-two { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .cal-err { margin: 0 0 10px; color: var(--red); font: 500 .82rem 'Inter', sans-serif; }
  .cal-frow { display: flex; align-items: center; justify-content: flex-end; gap: 8px; margin-top: 6px; }
  .cal-primary { border: 0; background: var(--blue); color: #fff; font: 600 .85rem 'Inter', sans-serif; padding: 9px 18px; border-radius: 999px; cursor: pointer; transition: background .15s ease-out, opacity .15s; }
  .cal-primary:hover { background: color-mix(in srgb, var(--blue) 86%, #000); }
  .cal-primary:disabled { opacity: .55; cursor: default; }

  .cal-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 20px 14px; border-top: 1px solid var(--glass-border); font-size: .8rem; color: var(--muted); flex: none; }
  .cal-foot button { border: 0; background: transparent; color: var(--muted); font: 500 .8rem 'Inter', sans-serif; padding: 6px 10px; border-radius: 999px; cursor: pointer; transition: background .15s ease-out, color .15s ease-out; }
  .cal-foot button:hover { background: color-mix(in srgb, var(--red) 14%, transparent); color: var(--red); }

  @media (max-width: 860px) {
    .cal-wrap { padding: 0; }
    .cal-panel { border-radius: 0; height: 100%; }
    .cal-month { grid-template-columns: 1fr; grid-template-rows: auto minmax(0, 1fr); overflow-y: auto; }
    .cal-left { border-right: 0; border-bottom: 1px solid var(--glass-border); overflow: visible; }
    .cal-day { height: 50px; } .cal-day b { width: 40px; height: 40px; }
  }
</style>

<script>
  document.addEventListener('alpine:init', () => {
    const pad = n => String(n).padStart(2, '0');
    const iso = d => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    const csrf = @json(csrf_token());

    // Single source of truth for the teacher's schedule. The Classes page seeds it via set(); other pages load it on first open.
    Alpine.store('calendar', {
      open: false, classes: [], loaded: false, loading: false, error: '', now: new Date(), trigger: null, _tick: null,
      year: new Date().getFullYear(), month: null, sel: iso(new Date()), form: null,

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
        this.year = this.now.getFullYear(); this.month = null; this.sel = iso(this.now); this.form = null;   // always opens on the whole year
        this.open = true;
        if (!this.loaded) this.load();
        clearInterval(this._tick); this._tick = setInterval(() => (this.now = new Date()), 30000);
      },
      hide() {
        this.open = false; this.form = null; clearInterval(this._tick); this._tick = null;
        const t = this.trigger; this.trigger = null;
        if (t && t.focus && document.contains(t)) t.focus();
      },
      toggle(el) { this.open ? this.hide() : this.show(el); },

      // ---- CRUD ----
      async api(method, url, body) {
        const r = await fetch(url, { method, credentials: 'same-origin', body: body ? JSON.stringify(body) : undefined,
          headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' } });
        const j = await r.json().catch(() => ({}));
        if (!r.ok) throw new Error(j.error || (j.errors && Object.values(j.errors)[0][0]) || 'Something went wrong. Please try again.');
        return j;
      },
      async saveClass() {
        const f = this.form; if (!f || f.saving) return;
        if (!f.subject.trim()) { f.err = 'Add a subject.'; return; }
        if (!f.start) { f.err = 'Pick a start time.'; return; }
        if (f.end && f.end <= f.start) { f.err = 'The end time must be after the start time.'; return; }
        f.saving = true; f.err = '';
        const body = { subject: f.subject, class: f.class, day: Number(f.day), start: f.start, end: f.end || null, room: f.room, from: f.from || null };
        try {
          const base = @json(route('teacher.schedule.class.store'));
          const j = f.id ? await this.api('PUT', base + '/' + f.id, body) : await this.api('POST', base, body);
          this.set(j.classes);
          this.form = null;
        } catch (e) { f.err = e.message; f.saving = false; }
      },
      async removeClass(c) {
        if (!confirm('Delete "' + c.subject + '"? It will be removed from every week.')) return;
        try { const j = await this.api('DELETE', @json(route('teacher.schedule.class.store')) + '/' + c.id); this.set(j.classes); this.form = null; }
        catch (e) { this.error = e.message; }
      },
      async remove() {
        if (!confirm('Remove your whole schedule?')) return false;
        try { await this.api('DELETE', @json(route('teacher.schedule.destroy'))); this.set([]); return true; }
        catch (e) { this.error = 'Could not remove it. Please try again.'; return false; }
      },
    });

    Alpine.data('calendarDrawer', () => {
      const DAYS = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
      const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      const COLORS = ['--violet', '--green', '--blue', '--cyan', '--red'];
      const mins = t => { const [h, m] = t.split(':').map(Number); return h * 60 + m; };
      const colorOf = s => 'var(' + COLORS[[...String(s)].reduce((a, ch) => (a * 31 + ch.charCodeAt(0)) >>> 0, 7) % COLORS.length] + ')';
      const isoDay = d => ((d.getDay() + 6) % 7) + 1;                          // JS Sun=0 → ISO Mon=1 … Sun=7
      const parse = s => { const [y, m, d] = s.split('-').map(Number); return new Date(y, m - 1, d); };

      return {
        MONTHS,
        get cal() { return Alpine.store('calendar'); },
        init() { this.$watch(() => Alpine.store('calendar').open, v => { if (v) this.$nextTick(() => this.$refs.close && this.$refs.close.focus()); }); },

        // ---- schedule lookups ----
        classesOn(dateStr) {
          const day = isoDay(parse(dateStr));
          return this.cal.classes.filter(c => c.day === day && (!c.from || dateStr >= c.from)).sort((a, b) => mins(a.start) - mins(b.start));
        },
        t12(t) { const h = Math.floor(mins(t) / 60); return (h % 12 || 12) + ':' + t.slice(3, 5) + ' ' + (h < 12 ? 'AM' : 'PM'); },
        span(c) { return this.t12(c.start) + (c.end ? ' – ' + this.t12(c.end) : ''); },
        dur(c) {
          if (!c.end) return '';
          const m = mins(c.end) - mins(c.start); if (m <= 0) return '';
          const h = Math.floor(m / 60), r = m % 60;
          return (h ? h + ' hr' + (h > 1 ? 's' : '') : '') + (h && r ? ' ' : '') + (r ? r + ' min' : '');
        },
        k(c) { return colorOf(c.subject); },

        // ---- navigation ----
        get todayStr() { return iso(this.cal.now); },
        get yearLabel() { return String(this.cal.year); },
        get title() { return this.cal.month === null ? String(this.cal.year) : MONTHS[this.cal.month] + ' ' + this.cal.year; },
        get subtitle() { return this.cal.month === null ? 'Tap a month to see your classes' : ''; },
        openMonth(m) {
          const c = this.cal; c.month = m;
          const t = this.cal.now;
          // land on today when it is in this month, otherwise on the 1st
          c.sel = (t.getFullYear() === c.year && t.getMonth() === m) ? iso(t) : iso(new Date(c.year, m, 1));
        },
        toYear() { this.cal.month = null; this.cal.form = null; },
        step(dir) {
          const c = this.cal;
          if (c.month === null) { c.year += dir; return; }
          let m = c.month + dir, y = c.year;
          if (m < 0) { m = 11; y--; } else if (m > 11) { m = 0; y++; }
          c.year = y; c.month = m;
          const t = c.now;
          c.sel = (t.getFullYear() === y && t.getMonth() === m) ? iso(t) : iso(new Date(y, m, 1));
        },
        goToday() { const t = this.cal.now = new Date(); this.cal.year = t.getFullYear(); if (this.cal.month !== null) { this.cal.month = t.getMonth(); this.cal.sel = iso(t); } },
        back() { this.cal.form ? (this.cal.form = null) : (this.cal.month !== null ? this.toYear() : this.cal.hide()); },

        // ---- grids ----
        // Sun-first month matrix: leading blanks, then every day with today / has-class flags.
        monthCells(y, m, withCounts = true) {
          const lead = new Date(y, m, 1).getDay(), n = new Date(y, m + 1, 0).getDate(), out = [];
          for (let i = 0; i < lead; i++) out.push({ key: 'b' + i, blank: true });
          for (let d = 1; d <= n; d++) {
            const s = y + '-' + pad(m + 1) + '-' + pad(d);
            out.push({ key: s, d, s, today: s === this.todayStr, sel: s === this.cal.sel, n: withCounts ? this.classesOn(s).length : 0 });
          }
          return out;
        },
        get bigCells() { return this.cal.month === null ? [] : this.monthCells(this.cal.year, this.cal.month); },
        get selDate() { return parse(this.cal.sel); },
        get selTitle() { const d = this.selDate; return DAYS[isoDay(d)] + ', ' + MONTHS[d.getMonth()].slice(0, 3) + ' ' + d.getDate(); },
        get selItems() { return this.classesOn(this.cal.sel); },
        get selSub() {
          const n = this.selItems.length;
          return (this.cal.sel === this.todayStr ? 'Today · ' : '') + (n ? n + (n === 1 ? ' class' : ' classes') : 'No classes');
        },
        cellLabel(c) { const d = parse(c.s); return MONTHS[d.getMonth()] + ' ' + c.d + ', ' + d.getFullYear() + (c.n ? ', ' + c.n + (c.n === 1 ? ' class' : ' classes') : ''); },

        // ---- form ----
        blank(dateStr) {
          const d = parse(dateStr);
          return { id: null, subject: '', class: '', day: isoDay(d), start: '08:00', end: '09:00', room: '', from: dateStr, err: '', saving: false };
        },
        add() { this.cal.form = this.blank(this.cal.month === null ? this.todayStr : this.cal.sel); this.focusForm(); },
        edit(c) { this.cal.form = { id: c.id, subject: c.subject, class: c.class || '', day: c.day, start: c.start, end: c.end || '', room: c.room || '', from: c.from || this.todayStr, err: '', saving: false }; this.focusForm(); },
        focusForm() { this.$nextTick(() => this.$refs.fSubject && this.$refs.fSubject.focus()); },
        DAYS,
        get formNote() { const f = this.cal.form; return f ? 'Repeats every ' + DAYS[Number(f.day)] + (f.from ? ' from ' + MONTHS[parse(f.from).getMonth()].slice(0, 3) + ' ' + parse(f.from).getDate() + ', ' + parse(f.from).getFullYear() : '') + '.' : ''; },

        trap(e) {
          const f = [...this.$root.querySelectorAll('button, a[href], input, select, [tabindex="0"]')].filter(x => x.offsetParent !== null && !x.disabled);
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

    <div class="cal-wrap" x-show="$store.calendar.open"
         x-transition:enter="cal-t" x-transition:enter-start="cal-out" x-transition:enter-end="cal-in"
         x-transition:leave="cal-t" x-transition:leave-start="cal-in" x-transition:leave-end="cal-out">
      <section class="cal-panel" role="dialog" aria-modal="true" aria-labelledby="calTitle"
               @keydown.escape.window="$store.calendar.open && back()" @keydown.tab="trap($event)">

        <header class="cal-head">
          <button type="button" class="cal-back" x-show="$store.calendar.month !== null" @click="toYear()" aria-label="Back to the whole year">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
            <span x-text="yearLabel"></span>
          </button>
          <button type="button" class="cal-ib" @click="step(-1)" :aria-label="$store.calendar.month === null ? 'Previous year' : 'Previous month'">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
          </button>
          <h2 id="calTitle" class="cal-title"><span x-text="title"></span><small x-show="subtitle" x-text="subtitle"></small></h2>
          <button type="button" class="cal-ib" @click="step(1)" :aria-label="$store.calendar.month === null ? 'Next year' : 'Next month'">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
          </button>
          <button type="button" class="cal-pill" @click="goToday()">Today</button>
          <button type="button" class="cal-ib cal-add" @click="add()" aria-label="Add a class" title="Add a class">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
          </button>
          <button type="button" class="cal-ib" x-ref="close" @click="$store.calendar.hide()" aria-label="Close calendar">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
          </button>
        </header>

        <p class="cal-empty" x-show="$store.calendar.loading && !$store.calendar.loaded" role="status">Loading your schedule…</p>
        <p class="cal-empty" x-show="$store.calendar.error" x-text="$store.calendar.error" role="alert"></p>

        {{-- ================= YEAR: all 12 months ================= --}}
        <div class="cal-year" x-show="$store.calendar.month === null && ($store.calendar.loaded || !$store.calendar.loading)">
          <template x-for="(name, m) in MONTHS" :key="name + $store.calendar.year">
            <button type="button" class="cal-mini" :class="($store.calendar.now.getFullYear() === $store.calendar.year && $store.calendar.now.getMonth() === m) ? 'now' : ''"
                    @click="openMonth(m)" :aria-label="name + ' ' + $store.calendar.year">
              <h3 x-text="name"></h3>
              <div class="cal-mg" aria-hidden="true">
                <template x-for="(l, i) in ['S','M','T','W','T','F','S']" :key="i"><i x-text="l"></i></template>
                <template x-for="c in monthCells($store.calendar.year, m)" :key="c.key">
                  <span :class="c.today ? 'today' : ''"><b x-show="!c.blank" x-text="c.d"></b><u x-show="c.n && !c.today"></u></span>
                </template>
              </div>
            </button>
          </template>
        </div>

        {{-- ================= MONTH: grid + the selected day ================= --}}
        <div class="cal-month" x-show="$store.calendar.month !== null" x-cloak>
          <div class="cal-left">
            <div class="cal-wk" aria-hidden="true">
              <template x-for="w in ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']" :key="w"><span x-text="w"></span></template>
            </div>
            <div class="cal-grid">
              <template x-for="c in bigCells" :key="c.key">
                <button type="button" class="cal-day" :class="[c.blank ? 'blank' : '', c.today ? 'today' : '', c.sel ? 'sel' : ''].join(' ')"
                        :tabindex="c.blank ? -1 : 0" :aria-hidden="c.blank ? 'true' : null" :aria-label="c.blank ? null : cellLabel(c)" :aria-pressed="c.blank ? null : c.sel.toString()"
                        @click="!c.blank && ($store.calendar.sel = c.s)">
                  <b x-show="!c.blank" x-text="c.d"></b>
                  <u x-show="c.n"><s></s></u>
                </button>
              </template>
            </div>
          </div>

          <div class="cal-right">
            <div class="cal-dayhead">
              <div><h3 x-text="selTitle"></h3><small x-text="selSub"></small></div>
              <button type="button" class="cal-pill" @click="add()">+ Add class</button>
            </div>
            <div class="cal-list">
              <template x-for="c in selItems" :key="c.id">
                <div class="cal-slot">
                  <time x-text="t12(c.start)"></time>
                  <div class="cal-card" :style="'--k:' + k(c)">
                    <b x-text="c.subject" :title="c.subject"></b>
                    <p>
                      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                      <span x-text="span(c) + (dur(c) ? ' · ' + dur(c) : '')"></span>
                    </p>
                    <p x-show="c.class || c.room">
                      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                      <span x-text="[c.class, c.room].filter(Boolean).join(' · ')"></span>
                    </p>
                    <div class="cal-acts">
                      <button type="button" @click="edit(c)" :aria-label="'Edit ' + c.subject" title="Edit">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4L19 9l-4-4L4 16z"/><path d="m13.5 6.5 4 4"/></svg>
                      </button>
                      <button type="button" class="del" @click="$store.calendar.removeClass(c)" :aria-label="'Delete ' + c.subject" title="Delete">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
                      </button>
                    </div>
                  </div>
                </div>
              </template>

              <div class="cal-empty" x-show="!selItems.length">
                <template x-if="!$store.calendar.classes.length">
                  <span>No classes yet. <a class="cal-link" href="{{ route('teacher.classes') }}">Upload your schedule</a> and Astro will lay it out here, or add one yourself.</span>
                </template>
                <template x-if="$store.calendar.classes.length">
                  <span>Nothing scheduled this day.</span>
                </template>
                <div><button type="button" class="cal-pill" @click="add()">+ Add a class</button></div>
              </div>
            </div>
          </div>
        </div>

        <footer class="cal-foot" x-show="$store.calendar.classes.length">
          <span x-text="$store.calendar.classes.length + ($store.calendar.classes.length === 1 ? ' class' : ' classes') + ' per week'"></span>
          <button type="button" @click="$store.calendar.remove()">Remove schedule</button>
        </footer>

        {{-- ================= ADD / EDIT ================= --}}
        <div class="cal-formscrim" x-show="$store.calendar.form" x-cloak @click.self="$store.calendar.form = null">
          <form class="cal-form" x-show="$store.calendar.form" @submit.prevent="$store.calendar.saveClass()" aria-labelledby="calFormTitle">
            <template x-if="$store.calendar.form">
              <div>
                <h3 id="calFormTitle" x-text="$store.calendar.form.id ? 'Edit class' : 'Add a class'"></h3>
                <label>Subject
                  <input type="text" x-ref="fSubject" x-model="$store.calendar.form.subject" maxlength="120" placeholder="e.g. Computer Science" required>
                </label>
                <div class="cal-two">
                  <label>Class / section
                    <input type="text" x-model="$store.calendar.form.class" maxlength="120" placeholder="e.g. 8-A">
                  </label>
                  <label>Room
                    <input type="text" x-model="$store.calendar.form.room" maxlength="60" placeholder="e.g. Lab 2">
                  </label>
                </div>
                <div class="cal-two">
                  <label>Starts
                    <input type="time" x-model="$store.calendar.form.start" required>
                  </label>
                  <label>Ends
                    <input type="time" x-model="$store.calendar.form.end">
                  </label>
                </div>
                <div class="cal-two">
                  <label>Repeats every
                    <select x-model="$store.calendar.form.day">
                      <template x-for="i in [1,2,3,4,5,6,7]" :key="i"><option :value="i" x-text="DAYS[i]" :selected="Number($store.calendar.form.day) === i"></option></template>
                    </select>
                  </label>
                  <label>Starting on
                    <input type="date" x-model="$store.calendar.form.from">
                  </label>
                </div>
                <p class="cal-sub" style="margin:-2px 0 12px;font:500 .78rem 'Inter',sans-serif;color:var(--muted)" x-text="formNote"></p>
                <p class="cal-err" x-show="$store.calendar.form.err" x-text="$store.calendar.form.err" role="alert"></p>
                <div class="cal-frow">
                  <button type="button" class="cal-pill" @click="$store.calendar.form = null">Cancel</button>
                  <button type="submit" class="cal-primary" :disabled="$store.calendar.form.saving" x-text="$store.calendar.form.saving ? 'Saving…' : 'Save'"></button>
                </div>
              </div>
            </template>
          </form>
        </div>

      </section>
    </div>
  </div>
</template>
