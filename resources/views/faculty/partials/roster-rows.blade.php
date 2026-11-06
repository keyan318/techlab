{{-- The course's astronauts as a table: avatar + name, course progress, XP, gems and day streak.
     Captains are left out (the roster page shows them in their own card).
     $roster comes from FacultyController::rosterFor(). --}}
@php $students = $roster->filter(fn ($m) => $m->pivot->role !== 'faculty'); @endphp

@once
<style>
  .crew-table { margin-top: 12px; border: 1px solid var(--glass-border); border-radius: 18px; overflow: hidden; background: var(--glass); text-align: left; }
  html[data-theme="light"] .crew-table { background: #fff; }
  .crew-row { display: grid; grid-template-columns: minmax(0, 1fr) 190px 84px 72px 72px; align-items: center; gap: 16px; padding: 12px 18px; }
  .crew-row + .crew-row { border-top: 1px solid var(--glass-border); }
  .crew-row.crew-th { padding-block: 10px; background: var(--field); color: var(--muted); font-size: .78rem; font-weight: 600; }
  .crew-who { display: flex; align-items: center; gap: 12px; min-width: 0; }
  .crew-who .avatar { width: 40px; height: 40px; border-radius: 50%; flex: 0 0 40px; display: grid; place-items: center; overflow: hidden; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: .82rem; color: #07142e; background: linear-gradient(135deg, var(--cyan), var(--blue)); }
  .crew-who .avatar.has-look { place-items: end center; background: #1d1660; }
  .crew-names { min-width: 0; display: flex; flex-direction: column; line-height: 1.3; }
  .crew-names b { font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .crew-names span { color: var(--muted); font-size: .8rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .crew-prog { display: flex; align-items: center; gap: 10px; }
  .crew-prog-track { flex: 1; height: 8px; border-radius: 999px; background: var(--glass-border); overflow: hidden; }
  .crew-prog-track > i { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--cyan), var(--blue)); }
  .crew-prog-n { width: 3.2em; text-align: right; font-size: .84rem; font-weight: 600; font-variant-numeric: tabular-nums; }
  .crew-num { display: inline-flex; align-items: center; gap: 6px; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-variant-numeric: tabular-nums; white-space: nowrap; }
  .crew-num.is-zero { color: var(--muted); font-weight: 500; }
  .crew-num.is-zero svg { opacity: .35; filter: grayscale(1); }
  .crew-unit { font-size: .74rem; font-weight: 500; color: var(--muted); }
  .crew-lbl { display: none; }
  @media (max-width: 760px) {
    .crew-row { grid-template-columns: repeat(3, auto) 1fr; gap: 10px 14px; padding: 14px; }
    .crew-row.crew-th { display: none; }
    .crew-who { grid-column: 1 / -1; }
    .crew-prog { grid-column: 1 / -1; }
  }
</style>
@endonce

<div class="crew-table" role="table" aria-label="Crew members">
  <div class="crew-row crew-th" role="row">
    <span role="columnheader">Astronaut</span>
    <span role="columnheader">Course progress</span>
    <span role="columnheader">XP</span>
    <span role="columnheader">Gems</span>
    <span role="columnheader">Streak</span>
  </div>
  @foreach ($students as $m)
    <div class="crew-row" role="row">
      <span class="crew-who" role="cell">
        @if ($m->avatar)
          <span class="avatar has-look"><x-astronaut :look="$m->avatar" crop="bust" style="width:46px;height:46px" /></span>
        @else
          <span class="avatar">{{ strtoupper(substr($m->name, 0, 2)) }}</span>
        @endif
        <span class="crew-names">
          <b>{{ $m->name }}</b>
          @if ($m->codename)<span>{{ $m->codename }}</span>@endif
        </span>
      </span>
      <span class="crew-prog" role="cell">
        @if ($m->progressPercent !== null)
          <span class="crew-prog-track" role="progressbar" aria-label="{{ $m->name }}'s course progress" aria-valuenow="{{ $m->progressPercent }}" aria-valuemin="0" aria-valuemax="100"><i style="width: {{ $m->progressPercent }}%"></i></span>
          <span class="crew-prog-n">{{ $m->progressPercent }}%</span>
        @endif
      </span>
      <span role="cell" title="Total XP">
        <span class="crew-num {{ $m->xp ? '' : 'is-zero' }}"><x-px-icon name="star" :size="16" />{{ number_format($m->xp ?? 0) }}<span class="crew-unit">XP</span></span>
      </span>
      <span role="cell" title="Gems earned in this course">
        <span class="crew-num {{ $m->gems ? '' : 'is-zero' }}"><x-px-icon name="gem" :size="16" />{{ $m->gems ?? 0 }}<span class="sr-only">{{ \Illuminate\Support\Str::plural('gem', $m->gems ?? 0) }}</span></span>
      </span>
      <span role="cell" title="Day streak">
        <span class="crew-num {{ $m->streak ? '' : 'is-zero' }}"><x-px-icon name="flame" :size="16" />{{ $m->streak ?? 0 }}<span class="crew-unit">{{ \Illuminate\Support\Str::plural('day', $m->streak ?? 0) }}</span></span>
      </span>
    </div>
  @endforeach
</div>
