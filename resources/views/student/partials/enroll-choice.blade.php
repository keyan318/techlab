{{--
  The two ways into a course a student isn't on yet (PlanetController::enrollment):
    Self-paced — start now, on your own, with the default course (needs the course before it finished).
    Join Class — pick a faculty member teaching it; they accept or decline.
  $e: the enrollment array · $variant: 'card' (course picker) or 'hero' (dashboard).
--}}
@php
  $variant ??= 'card';
  $tourMark ??= false;
  $hero = $variant === 'hero';
  // Already on a track (switching from the course hub): leave out the one they're on.
  $classes = array_values(array_filter($e['classes'] ?? [], fn ($c) => $c['status'] !== 'member'));
  $switching = ($e['track'] ?? null) !== null;
  $pending = collect($classes)->firstWhere('status', 'pending');
@endphp
@once
<style>
  .enroll { position: relative; display: flex; flex-direction: column; gap: 10px; }
  .enroll-row { display: flex; flex-wrap: wrap; gap: 14px; }
  /* Card buttons copy the pixel buttons (.px-btn in pixel-ui), which the course pages don't load. */
  .enroll-btn { --bg: rgba(255,255,255,.1); --edge: rgba(255,255,255,.4); --c: #fff;
    flex: 1 1 140px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; padding: 0 16px; margin: 3px;
    border: 0; border-radius: 2px; font-family: 'Pixelify Sans', 'Space Grotesk', sans-serif; font-size: 17px; font-weight: 600; line-height: 1;
    color: var(--c); background: var(--bg); cursor: pointer; white-space: nowrap;
    box-shadow: 0 -3px 0 0 var(--edge), 0 3px 0 0 var(--edge), -3px 0 0 0 var(--edge), 3px 0 0 0 var(--edge),
                inset 0 -4px 0 0 rgba(0,0,0,.2), inset 0 3px 0 0 rgba(255,255,255,.22);
    transition: transform .1s ease, filter .15s ease; }
  .enroll-btn:active { transform: translateY(2px); }
  .enroll-btn:disabled { opacity: .55; cursor: default; transform: none; filter: none; }
  .enroll-btn:focus-visible { outline: 3px solid #9ad0ff; outline-offset: 5px; }
  .enroll-self { --bg: #14a0ff; --edge: #0a5fb5; }
  .enroll-class:hover:not(:disabled), .enroll-self:hover:not(:disabled) { filter: brightness(1.08); }
  /* Opens in place (not floating): both the course card and the dashboard hero clip overflow. */
  .enroll-menu { padding: 6px; border-radius: 12px; background: #15123f; border: 1px solid rgba(150,170,255,.3); box-shadow: 0 24px 60px -20px rgba(0,0,0,.7); }
  .enroll-menu-head { padding: 6px 10px 8px; font-size: 11.5px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: rgb(190 198 235 / .8); }
  .enroll-opt { display: flex; width: 100%; align-items: center; justify-content: space-between; gap: 10px; padding: 9px 10px; border-radius: 8px; color: #e8ecff; font-size: 14px; text-align: left; background: none; border: none; cursor: pointer; }
  .enroll-opt:hover:not(:disabled), .enroll-opt:focus-visible { background: rgba(115,142,255,.16); outline: none; }
  .enroll-opt:disabled { cursor: default; opacity: .7; }
  .enroll-chip { flex: none; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 600; color: #9fb4ff; background: rgba(115,142,255,.14); }
  .enroll-chip.is-no { color: #ffb3c2; background: rgba(255,99,132,.12); }
  .enroll-note { font-size: 12.5px; line-height: 1.45; color: rgb(190 198 235); }
  .enroll-err { font-size: 12.5px; color: #ff9db0; }
  .enroll.is-hero .enroll-row { gap: 18px; }
  .enroll.is-hero .px-btn:disabled { opacity: .55; cursor: default; filter: none; transform: none; }
  .enroll.is-hero .enroll-note { color: rgba(255,255,255,.82); font-size: 14px; }
  .enroll.is-hero .enroll-menu { max-width: 420px; }
  /* The course card stays dark in both themes (course-card-styles), so only the hero's menu goes light. */
  html[data-theme="light"] .enroll.is-hero .enroll-menu { background: #fff; border-color: rgba(0,0,0,.1); }
  html[data-theme="light"] .enroll.is-hero .enroll-opt { color: #1d2350; }
  html[data-theme="light"] .enroll.is-hero .enroll-menu-head { color: #4a5070; }
  @media (prefers-reduced-motion: reduce) { .enroll-btn { transition: none; } .enroll-btn:active { transform: none; } }
</style>
<script>
  // POST to an enrollment URL; reload (or follow the redirect) on success, say why on failure.
  function enrollChoice() {
    return {
      open: false, busy: false, err: '',
      post(url) {
        this.busy = true; this.err = '';
        fetch(url, { method: 'POST', credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
          .then(r => r.json().catch(() => ({})).then(body => ({ ok: r.ok, status: r.status, body })))
          .then(({ ok, status, body }) => {
            if (ok) { body.redirect ? location.assign(body.redirect) : location.reload(); return; }
            this.err = status === 429 ? 'Too many tries. Wait a minute.' : (body.message || 'Something went wrong. Try again.');
            this.busy = false;
          })
          .catch(() => { this.err = 'Something went wrong. Try again.'; this.busy = false; });
      },
    };
  }
</script>
@endonce

<div class="enroll {{ $hero ? 'is-hero' : '' }}" @if ($tourMark) data-tour="course-enroll" @endif x-data="enrollChoice()" @click.outside="open = false" @keydown.escape="open = false" @click.stop>
  <div class="enroll-row">
    @if ($e['self_paced_url'])
    <button type="button" class="{{ $hero ? 'px-btn px-btn-lg px-btn-blue' : 'enroll-btn enroll-self' }}"
            :disabled="busy" @disabled($e['self_paced_blocked'])
            @click.prevent="post('{{ $e['self_paced_url'] }}')">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="7" r="3.2"/><path d="M12 10.5v5.5l-3 5M12 16l3 5M7 13.5h10"/></svg>
      {{ $switching ? 'Switch to Self-paced' : 'Self-paced' }}
    </button>
    @endif
    <button type="button" class="{{ $hero ? 'px-btn px-btn-lg px-btn-ghost' : 'enroll-btn enroll-class' }}"
            :disabled="busy" @disabled(! $classes)
            @click.prevent="open = ! open" :aria-expanded="open" aria-haspopup="true">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l9-5 9 5-9 5z"/><path d="M7 11.2V16c0 1.4 2.2 3 5 3s5-1.6 5-3v-4.8"/></svg>
      {{ $switching ? 'Join a class' : 'Join Class' }}
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
    </button>
  </div>

  @if ($classes)
    <div class="enroll-menu" x-show="open" x-cloak x-transition.opacity.duration.150ms role="menu">
      <div class="enroll-menu-head">Who is your instructor?</div>
      @foreach ($classes as $class)
        @php $status = $class['status']; @endphp
        <button type="button" class="enroll-opt" role="menuitem" @disabled($status === 'pending')
                @click.prevent="open = false; post('{{ $class['join_url'] }}')">
          <span>{{ $class['teacher'] }}</span>
          @if ($status === 'pending')
            <span class="enroll-chip">Waiting</span>
          @elseif ($status === 'declined')
            <span class="enroll-chip is-no">Declined · ask again</span>
          @else
            <span class="enroll-chip">Ask to join</span>
          @endif
        </button>
      @endforeach
    </div>
  @endif

  <p class="enroll-err" x-show="err" x-text="err" x-cloak role="alert"></p>
  <p class="enroll-note">
    @if ($switching)
      @if ($pending) Waiting for {{ $pending['teacher'] }} to accept you. @endif
      Switching starts the new track at 0%. Your progress here is kept, and comes back if you return.
    @elseif ($pending)
      Waiting for {{ $pending['teacher'] }} to accept you. Once they do, refresh and the course opens.
    @elseif ($e['self_paced_blocked'] && ($e['requires_kind'] ?? 'course') === 'module')
      Finish Module 1 of {{ $e['requires_title'] }} first: Self-paced on faculty-made courses opens once you pass its mini boss. A class's instructor can still let you in.
    @elseif ($e['self_paced_blocked'])
      Finish {{ $e['requires_title'] }} first: Self-paced opens once you pass its final exam. A class's instructor can still let you in.
    @elseif (! $classes)
      Self-paced: learn on your own and earn a TechLab certificate. No class is open yet.
    @else
      Self-paced: on your own, TechLab certificate. Class: your instructor's version, with their signed certificate.
    @endif
  </p>
</div>
