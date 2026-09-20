@extends('layouts.teacher-shell')
@section('title', 'Classes')

@section('content')
  {{-- CLASSES: teacher uploads a schedule (file or photo); Astro organizes it into upcoming classes.
       "View Calendar" opens the calendar drawer (partials/calendar-drawer, mounted by the sidebar). --}}
    <style>
      .classes-card { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 24px; padding: 24px 26px; backdrop-filter: blur(20px) saturate(180%); box-shadow: 0 1px 2px rgba(0,0,0,.05), 0 12px 32px -12px rgba(0,0,0,.18); max-width: 720px; text-align: left; }
      html[data-theme="light"] .classes-card { background: #fff; }
      .classes-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
      .classes-head h2 { font-family: 'Space Grotesk', sans-serif; font-size: 1.25rem; font-weight: 700; letter-spacing: -.02em; margin: 0; }
      .classes-actions { display: flex; align-items: center; gap: 6px; }
      .classes-btn { font: 600 .85rem 'Inter', sans-serif; color: var(--blue); background: transparent; border: 0; padding: 7px 10px; border-radius: 999px; cursor: pointer; transition: background .15s ease-out, opacity .1s ease-out; }
      .classes-btn:hover { background: color-mix(in srgb, var(--blue) 12%, transparent); }
      .classes-btn:active { opacity: .55; }
      .classes-btn[disabled] { opacity: .5; cursor: wait; }
      .classes-btn.up { border: 1px solid var(--glass-border); color: var(--text); }
      .classes-btn.muted { color: var(--muted); font-weight: 500; }
      .cls-list { display: grid; gap: 8px; }
      .cls-day { font: 700 .68rem 'Space Mono', monospace; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); margin: 14px 2px 2px; }
      .cls-row { display: grid; grid-template-columns: 76px 1fr auto; align-items: stretch; gap: 14px; border-radius: 14px; background: color-mix(in srgb, var(--text) 4%, transparent); overflow: hidden; }
      .cls-time { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 12px 6px; background: color-mix(in srgb, var(--blue) 14%, transparent); color: var(--blue); font-weight: 600; font-size: .95rem; line-height: 1.25; position: relative; }
      .cls-time small { font-size: .8rem; font-weight: 600; color: var(--text); opacity: .85; }
      .cls-row.next .cls-time::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: var(--blue); }
      .cls-main { padding: 12px 0; min-width: 0; }
      .cls-main b { display: block; font-family: 'Space Grotesk', sans-serif; font-size: 1.02rem; font-weight: 600; color: var(--text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
      .cls-main span { font-size: .85rem; color: var(--muted); }
      .cls-pill { align-self: center; margin-right: 12px; padding: 8px 14px; border-radius: 12px; font-size: .82rem; font-weight: 600; white-space: nowrap; background: color-mix(in srgb, var(--blue) 14%, transparent); color: var(--blue); }
      .cls-pill.now { background: var(--blue); color: #fff; }
      .classes-empty { color: var(--muted); font-size: .95rem; margin: 0; padding: 8px 0 4px; text-align: center; }
      .classes-msg { font-size: .85rem; margin: 12px 2px 0; }
      .classes-msg.err { color: #ff8aa0; } .classes-msg.ok { color: #7cffb2; }
      html[data-theme="light"] .classes-msg.err { color: #c62550; } html[data-theme="light"] .classes-msg.ok { color: #0c8552; }
      @media (max-width: 560px) { .cls-row { grid-template-columns: 68px 1fr; } .cls-pill { display: none; } }
    </style>
    <section class="classes-card" aria-labelledby="classesTitle" x-data="scheduleCard()" x-cloak>
      <header class="classes-head">
        <h2 id="classesTitle">My Classes</h2>
        <div class="classes-actions">
          <input type="file" x-ref="file" class="sr-only" style="display:none" @change="upload($event)"
                 accept="image/*,.pdf,.docx,.pptx,.csv,.txt,.md" aria-label="Upload your schedule">
          <button type="button" class="classes-btn up" :disabled="busy" @click="$refs.file.click()">
            <span x-text="busy ? label : (classes.length ? 'Replace schedule' : 'Upload schedule')"
                  :style="busy ? 'transition: opacity .18s ease; opacity:' + (fade ? 1 : 0) : ''"></span>
          </button>
          <button type="button" class="classes-btn" x-show="classes.length" @click="$store.calendar.show($el)"
                  aria-haspopup="dialog" :aria-expanded="$store.calendar.open.toString()">View Calendar</button>
        </div>
      </header>

      <p class="classes-empty" x-show="!classes.length">No schedule yet. Upload yours (a file or a photo) and Astro will organize it here.</p>

      {{-- Upcoming (next 4 slots, live "In 30 min" from the teacher's own clock) --}}
      <div class="cls-list" x-show="classes.length">
        <template x-for="(c, i) in upcoming" :key="c.id">
          <div class="cls-row" :class="i === 0 ? 'next' : ''">
            <div class="cls-time"><span x-text="hm(c.start)"></span><small x-text="ap(c.start)"></small></div>
            <div class="cls-main"><b x-text="c.subject"></b><span x-text="[c.class, c.room].filter(Boolean).join(' · ') || dayName(c.day)"></span></div>
            <div class="cls-pill" :class="c.diff <= 0 ? 'now' : ''" x-text="pill(c)"></div>
          </div>
        </template>
      </div>

      <p class="classes-msg" x-show="msg" x-text="msg" role="status" :class="ok ? 'ok' : 'err'"></p>
    </section>
@endsection

@push('scripts')
<script>
  function scheduleCard() {
    const DAYS = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const mins = t => { const [h, m] = t.split(':').map(Number); return h * 60 + m; };
    return {
      // The schedule lives in $store.calendar (shared with the sidebar's calendar drawer); this card just reads it.
      get classes() { return Alpine.store('calendar').classes; },
      busy: false, msg: '', ok: false, now: new Date(),
      // Status line on the upload button: follows the server's real stages (reading → organizing), not a timer.
      labels: { reading: 'Astro is reading it…', organizing: 'Astro is organizing your week…' },
      label: 'Astro is reading it…', fade: true,
      setStage(stage) {
        const next = this.labels[stage]; if (!next || next === this.label) return;
        this.fade = false; setTimeout(() => { this.label = next; this.fade = true; }, 180);
      },
      init() {
        Alpine.store('calendar').set(@json($classes->values()));
        setInterval(() => (this.now = new Date()), 30000);
      },
      dayName(d) { return DAYS[d] || ''; },
      hm(t) { const h = Math.floor(mins(t) / 60) % 12 || 12; return String(h).padStart(2, '0') + ':' + t.slice(3, 5); },
      ap(t) { return mins(t) >= 720 ? 'PM' : 'AM'; },
      // Minutes until this weekly slot next starts, by the browser's clock (negative = in progress).
      when(c) {
        const jsDay = this.now.getDay(), today = jsDay === 0 ? 7 : jsDay;
        const ahead = (c.day - today + 7) % 7;
        const nowMin = this.now.getHours() * 60 + this.now.getMinutes();
        const length = c.end ? mins(c.end) - mins(c.start) : 60;
        let diff = ahead * 1440 + mins(c.start) - nowMin;
        let days = ahead;
        if (diff < -length) { diff += 7 * 1440; days = (ahead + 7) % 7 || 7; }   // already over → next week
        return { diff, days };
      },
      get upcoming() {
        return this.classes.map(c => ({ ...c, ...this.when(c) })).sort((a, b) => a.diff - b.diff).slice(0, 4);
      },
      pill(c) {
        if (c.diff <= 0) return 'Now';
        if (c.diff < 60) return 'In ' + c.diff + ' min';
        if (c.diff < 1440 && c.days === 0) { const h = Math.floor(c.diff / 60), m = c.diff % 60; return 'In ' + h + ' h' + (m ? ' ' + m + ' min' : ''); }
        return c.days === 1 ? 'Tomorrow' : this.dayName(c.day).slice(0, 3);
      },
      get week() {
        const by = {}; this.classes.forEach(c => (by[c.day] ||= []).push(c));
        return Object.keys(by).map(Number).sort((a, b) => a - b).map(day => ({ day, items: by[day].sort((a, b) => mins(a.start) - mins(b.start)) }));
      },
      // Upload with live progress: the server streams one JSON line per stage, then a final {done, classes|error, status} line.
      // Validation errors (bad/too-big file) come back as plain JSON before the stream starts.
      async upload_(fd) {
        const res = await fetch(@json(route('teacher.schedule.store')), {
          method: 'POST', body: fd,
          headers: { 'Accept': 'application/x-ndjson', 'X-CSRF-TOKEN': @json(csrf_token()), 'X-Requested-With': 'XMLHttpRequest' },
        });
        if (!(res.headers.get('content-type') || '').includes('ndjson') || !res.body) {
          const j = await res.json().catch(() => ({}));
          return { ok: res.ok, j };
        }
        const reader = res.body.getReader(), dec = new TextDecoder();
        let buf = '', last = null;
        const take = line => { if (!line.trim()) return; const m = JSON.parse(line); if (m.stage) this.setStage(m.stage); else last = m; };
        for (;;) {
          const { value, done } = await reader.read();
          if (done) break;
          buf += dec.decode(value, { stream: true });
          const lines = buf.split('\n'); buf = lines.pop();
          lines.forEach(take);
        }
        take(buf);
        return last ? { ok: last.done === true, j: last } : { ok: false, j: {} };
      },
      // Instant checks + shrink big photos (phone shots are 4000px / 5MB; 1200px is plenty to read a timetable, and fewer pixels = a faster vision call).
      async prepare(file) {
        const isImage = file.type.startsWith('image/');
        if (!isImage && !/\.(pdf|docx|pptx|csv|txt|md)$/i.test(file.name)) throw new Error('Astro can read PDF, Word, PowerPoint, CSV or text files, or a photo/screenshot of your timetable.');
        if (file.size > 10 * 1024 * 1024) throw new Error('That file is over 10 MB. Try a smaller file or a screenshot.');
        if (!isImage) return file;
        const bmp = await createImageBitmap(file).catch(() => null);
        if (!bmp) return file;
        if (bmp.width < 500 || bmp.height < 300) throw new Error('That image is too small to read clearly (' + bmp.width + '×' + bmp.height + '). Use a bigger, sharper photo or screenshot.');
        const scale = Math.min(1, 1200 / Math.max(bmp.width, bmp.height));
        if (scale === 1 && file.size < 1024 * 1024 && file.type === 'image/jpeg') return file;
        const canvas = document.createElement('canvas');
        canvas.width = Math.round(bmp.width * scale); canvas.height = Math.round(bmp.height * scale);
        const ctx = canvas.getContext('2d'); ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height); ctx.drawImage(bmp, 0, 0, canvas.width, canvas.height);
        const blob = await new Promise(r => canvas.toBlob(r, 'image/jpeg', 0.82));
        return blob ? new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg' }) : file;
      },
      async upload(e) {
        const picked = e.target.files[0]; e.target.value = '';
        if (!picked) return;
        this.msg = ''; this.ok = false;
        let file;
        try { file = await this.prepare(picked); } catch (err) { this.msg = err.message; return; }   // tell them right away
        this.busy = true; this.label = this.labels.reading; this.fade = true;
        try {
          const fd = new FormData(); fd.append('file', file);
          const t = new Date(); fd.append('today', t.getFullYear() + '-' + String(t.getMonth() + 1).padStart(2, '0') + '-' + String(t.getDate()).padStart(2, '0'));
          const { ok, j } = await this.upload_(fd);
          if (!ok) { this.msg = j.error || (j.errors && Object.values(j.errors)[0][0]) || 'Astro could not read that file. Please try again.'; return; }
          Alpine.store('calendar').set(j.classes); this.ok = true;
          this.msg = 'Done — Astro found ' + j.classes.length + ' class' + (j.classes.length === 1 ? '' : 'es') + ' in your schedule.';
        } catch (err) { this.msg = 'Something went wrong. Check your connection and try again.'; }
        finally { this.busy = false; }
      },
    };
  }
</script>
@endpush
