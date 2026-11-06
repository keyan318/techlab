@extends('layouts.faculty-shell')
@section('title', 'Crew')

@section('content')
  <p style="margin-bottom:10px;text-align:left"><a href="{{ route('faculty.crew') }}" style="color:var(--muted);font-size:.85rem">← All crews</a></p>

  <div class="page-head">
    <span class="eyebrow">Captain · {{ $crew->name }}</span>
    <h1>Your <span class="accent">crew</span></h1>
    @if ($crew->isSelfPaced())
      {{-- The self-paced run of a course this faculty member built: watch only, no code, no class tabs. --}}
      <p class="sub" style="margin:0">{{ $crew->roster->where('pivot.role', 'student')->count() }} learning on their own · anyone can start this course self-paced, no code needed</p>
    @else
      <p class="sub" style="margin:0">{{ $crew->roster->count() }} aboard · code <strong data-tour="captain-code" style="font-family:'Space Mono',monospace;letter-spacing:.12em">{{ $crew->code }}</strong></p>
    @endif
  </div>

  @unless ($crew->isSelfPaced())
    @include('faculty.partials.course-tabs', ['crew' => $crew])
  @endunless

  @php
    $captains = $roster->filter(fn ($m) => $m->pivot->role === 'faculty');
    $astronauts = $roster->filter(fn ($m) => $m->pivot->role !== 'faculty')->sortByDesc('progressPercent');
  @endphp

  @if ($captains->isNotEmpty())
  <section class="roster-card">
    <h2 class="crew-h">Captain</h2>
    @foreach ($captains as $c)
      <div class="captain-card">
        @if ($c->avatar)
          <span class="avatar has-look"><x-astronaut :look="$c->avatar" crop="bust" style="width:52px;height:52px" /></span>
        @else
          <span class="avatar">{{ strtoupper(substr($c->name, 0, 2)) }}</span>
        @endif
        <span class="captain-names">
          <b>{{ $c->name }}</b>
          <span>Leads this course{{ $c->id === auth()->id() ? ' (you)' : '' }}</span>
        </span>
      </div>
    @endforeach
  </section>
  @endif

  <section class="roster-card" data-tour="crew-list">
    <h2 class="crew-h">Crew members <span class="crew-count">{{ $astronauts->count() }}</span></h2>
    @if ($astronauts->isEmpty())
      <p class="sub" style="margin:12px 0 0">@if ($crew->isSelfPaced()) No one has started this course self-paced yet. @else No astronauts yet. Share the code <strong>{{ $crew->code }}</strong>; their join requests show up on your dashboard. @endif</p>
    @else
      @include('faculty.partials.roster-rows', ['roster' => $astronauts])
    @endif
  </section>

  <style>
    .crew-h { display: flex; align-items: center; gap: 10px; font-size: 1.15rem !important; }
    .crew-count { font-size: .78rem; font-weight: 700; padding: 2px 9px; border-radius: 999px; color: var(--blue); background: rgba(115,182,255,.14); }
    .captain-card { display: flex; align-items: center; gap: 14px; margin-top: 12px; padding: 14px 18px; border-radius: 18px; border: 1px solid rgba(155,107,255,.35); background: rgba(155,107,255,.08); }
    .captain-card .avatar { width: 46px; height: 46px; border-radius: 50%; flex: 0 0 46px; display: grid; place-items: center; overflow: hidden; font-family: 'Space Grotesk', sans-serif; font-weight: 700; color: #fff; background: var(--violet); }
    .captain-card .avatar.has-look { place-items: end center; background: #1d1660; }
    .captain-names { display: flex; flex-direction: column; line-height: 1.3; min-width: 0; }
    .captain-names b { font-weight: 600; }
    .captain-names span { color: var(--muted); font-size: .82rem; }
  </style>
@endsection
