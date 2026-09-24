@extends('layouts.admin-shell')
@section('title', 'Dashboard')

@section('content')
  <span class="eyebrow">Admin · Overview</span>
  <h1>Command <span class="accent">deck</span></h1>
  <p class="sub">A quick look at who's covering what. Head to Course Assignment to change any of it.</p>

  <div class="stats">
    <div class="stat"><div class="n">{{ $facultyCount }}</div><div class="l">Faculty registered</div></div>
    <div class="stat"><div class="n">{{ $assigned }}</div><div class="l">Courses assigned</div></div>
    <div class="stat"><div class="n">{{ $unassigned }}</div><div class="l">Courses unassigned</div></div>
    <div class="stat"><div class="n">{{ $totalCourses }}</div><div class="l">Total courses</div></div>
  </div>

  <section class="roster-card">
    <h2>Recent activity</h2>
    @if ($activity->isEmpty())
      <p class="sub" style="margin-top:10px">No assignments made yet.</p>
    @else
      <div class="roster">
        @foreach ($activity as $entry)
          <div class="member">
            <span class="avatar">📋</span>
            <span class="mname">{{ $entry['text'] }}</span>
            <span class="tag student">{{ $entry['when'] }}</span>
          </div>
        @endforeach
      </div>
    @endif
  </section>

  <p style="margin-top:26px"><a class="mbtn solid" href="{{ route('admin.home') }}" style="display:inline-block">Go to Course Assignment →</a></p>
@endsection
