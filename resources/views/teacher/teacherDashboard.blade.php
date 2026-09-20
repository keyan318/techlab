@extends('layouts.teacher-shell')
@section('title', "Captain's Bridge")

@section('content')
  @if ($crew)
    @include('teacher.partials.crew-launched')
  @else
    <span class="eyebrow">Captain · Ready to launch</span>
      <h1>Create your <span class="accent">crew</span></h1>
      <p class="sub">Every squad needs a ship. Launch your crew to get a join code you can share with your students.</p>

      <div class="ship-stage">
        <div class="orbit"></div>
        <div class="dock"></div>
        <svg class="ship" viewBox="0 0 120 220" aria-hidden="true" style="opacity:.55">
          <defs>
            <linearGradient id="hull0" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#eaf2ff"/><stop offset="100%" stop-color="#9fb4e6"/></linearGradient>
            <linearGradient id="flame0" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#fff3b0"/><stop offset="60%" stop-color="#ff9b3d"/><stop offset="100%" stop-color="#ff4d6d"/></linearGradient>
          </defs>
          <g class="thruster"><path d="M48 168 q12 34 12 46 q0 -12 12 -46 z" fill="url(#flame0)"/></g>
          <path d="M60 8 q32 42 32 96 q0 44 -32 56 q-32 -12 -32 -56 q0 -54 32 -96z" fill="url(#hull0)" stroke="#7c8cc8" stroke-width="2"/>
          <circle cx="60" cy="74" r="15" fill="#5be1ff" stroke="#1b3a5c" stroke-width="2"/>
          <path d="M28 120 q-22 8 -22 44 q22 -12 32 -22z" fill="#9b6bff"/>
          <path d="M92 120 q22 8 22 44 q-22 -12 -32 -22z" fill="#9b6bff"/>
        </svg>
      </div>

      <form method="POST" action="/teacher/crew">
        @csrf
        <input type="text" name="name" class="crew-name-input" placeholder="Name your classroom (e.g. Space Crew)" required autofocus />
        <button class="btn btn-primary" type="submit" style="margin-top:16px">🚀 Create your crew</button>
      </form>
  @endif
@endsection
