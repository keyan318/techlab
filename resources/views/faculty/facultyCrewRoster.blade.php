@extends('layouts.faculty-shell')
@section('title', 'Crew')

@section('content')
  <p style="margin-bottom:10px"><a href="{{ route('faculty.dashboard') }}" style="color:var(--muted);font-size:.85rem">← My Courses</a></p>

  <div class="page-head">
    <span class="eyebrow">Captain · {{ $crew->name }}</span>
    <h1>Your <span class="accent">crew</span></h1>
    <p class="sub" style="margin:0">{{ $crew->roster->count() }} aboard · code <strong style="font-family:'Space Mono',monospace;letter-spacing:.12em">{{ $crew->code }}</strong></p>
  </div>

  @include('faculty.partials.course-tabs', ['crew' => $crew])

  <section class="roster-card">
    <div class="roster">
      @foreach ($roster as $m)
        <div class="member">
          @if ($m->avatar)
            <span class="avatar" style="overflow:hidden;place-items:end center;background:#1d1660"><x-astronaut :look="$m->avatar" crop="bust" style="width:44px;height:44px" /></span>
          @else
            <span class="avatar">{{ strtoupper(substr($m->name, 0, 2)) }}</span>
          @endif
          <span class="mname">{{ $m->name }}@if ($m->codename) <span style="color:var(--muted);font-weight:400">· {{ $m->codename }}</span>@endif</span>
          @if ($m->pivot->role !== 'faculty' && $m->progressPercent !== null)
            <span style="color:var(--muted);font-size:.82rem">{{ $m->progressPercent }}% complete</span>
          @endif
          @if ($m->pivot->role !== 'faculty' && $m->xp !== null)
            <span class="stat-chip xp">{{ number_format($m->xp) }} XP</span>
            <span class="stat-chip gems">{{ $m->gems }} {{ \Illuminate\Support\Str::plural('Gem', $m->gems) }}</span>
          @endif
          <span class="tag {{ $m->pivot->role === 'faculty' ? 'faculty' : 'student' }}">{{ $m->pivot->role === 'faculty' ? 'Captain' : 'Astronaut' }}</span>
        </div>
      @endforeach
    </div>
  </section>
@endsection
