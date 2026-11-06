{{-- Build your own course: pick a planet, name it, and (for networking/cybersecurity) pick a lab engine.
     FacultyCourseController::store() creates it, makes this faculty member its teacher, and sends them
     to the Courses page to start adding modules and lessons with the same tools used on any course. --}}
@extends('layouts.faculty-shell')
@section('title', 'Build a course')

@push('head')
  <style>
    .cc-form { max-width: 560px; margin-top: 28px; }
    .cc-form label { display: block; margin: 18px 0 6px; font: 700 11.5px 'Space Mono', monospace; letter-spacing: .12em; text-transform: uppercase; color: #9aa3d0; }
    .cc-form input[type=text], .cc-form textarea, .cc-form select {
      width: 100%; padding: 11px 13px; border-radius: 10px; border: 1px solid var(--glass-border);
      background: rgba(255,255,255,.05); color: inherit; font: 500 15px 'Inter', sans-serif;
    }
    .cc-form select option { color: #0b1230; }
    .cc-planets { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 4px; }
    .cc-planet { padding: 14px 10px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(255,255,255,.04); text-align: center; cursor: pointer; font-weight: 600; }
    .cc-planet.on { border-color: #73b6ff; background: rgba(115,182,255,.12); color: #73b6ff; }
    .cc-hint { margin: 4px 0 0; font-size: 13px; color: var(--muted); }
    .cc-actions { margin-top: 26px; display: flex; gap: 10px; }
    .cc-btn { padding: 11px 18px; border-radius: 10px; border: 1px solid var(--glass-border); background: transparent; color: inherit; font: 600 14px 'Inter', sans-serif; cursor: pointer; text-decoration: none; }
    .cc-btn.primary { border: none; background: linear-gradient(100deg, #5be1ff, #73b6ff 55%, #9b6bff); color: #06061a; font-weight: 700; }
    .cc-err { color: #ff9db0; font-size: 13px; margin-top: 4px; }
  </style>
@endpush

@section('content')
  <div class="page-head">
    <span class="eyebrow">Captain · Courses</span>
    <h1>Build your <span class="accent">own course</span></h1>
    <p class="sub" style="margin:0">Pick a planet and give it a name. It starts empty and hidden from students — add modules and lessons with the same editor you use on any course, then publish it when it's ready.</p>
  </div>

  <form class="cc-form" method="POST" action="{{ route('faculty.courses.store') }}" x-data="{ planet: '{{ old('planet', 'programming') }}' }">
    @csrf

    <label>Planet</label>
    <div class="cc-planets">
      @foreach ($planets as $p)
        <div class="cc-planet" :class="planet === '{{ $p }}' ? 'on' : ''" @click="planet = '{{ $p }}'">{{ ucfirst($p) }}</div>
      @endforeach
    </div>
    <input type="hidden" name="planet" x-model="planet">
    @error('planet') <p class="cc-err">{{ $message }}</p> @enderror

    <label for="cc-title">Title</label>
    <input id="cc-title" type="text" name="title" maxlength="80" value="{{ old('title') }}" required placeholder="e.g. Intro to Game Scripting">
    @error('title') <p class="cc-err">{{ $message }}</p> @enderror

    <label for="cc-blurb">Subtitle (optional)</label>
    <textarea id="cc-blurb" name="blurb" maxlength="160" rows="2" placeholder="One line students see on the course card">{{ old('blurb') }}</textarea>
    @error('blurb') <p class="cc-err">{{ $message }}</p> @enderror

    <div x-show="planet !== 'programming'" x-cloak>
      <label for="cc-lab">Hands-on lab</label>
      <select id="cc-lab" name="lab_kind">
        <option value="netsim" {{ old('lab_kind') === 'netsim' ? 'selected' : '' }}>NetSim — packets, routing, devices</option>
        <option value="relay" {{ old('lab_kind') === 'relay' ? 'selected' : '' }}>Relay Console — router CLI (BGP, QoS, VPNs…)</option>
        <option value="citadel" {{ old('lab_kind') === 'citadel' ? 'selected' : '' }}>Citadel Sim — defense scenarios</option>
        <option value="none" {{ old('lab_kind') === 'none' ? 'selected' : '' }}>None — lessons and quizzes only</option>
      </select>
      <p class="cc-hint">Picks which simulator a lesson's "Configure/Defend it yourself" lab can use. You can still choose "None" per lesson later.</p>
      @error('lab_kind') <p class="cc-err">{{ $message }}</p> @enderror
    </div>
    <p class="cc-hint" x-show="planet === 'programming'">Programming courses always use the in-browser code editor for their labs.</p>

    <div class="cc-actions">
      <a class="cc-btn" href="{{ route('faculty.courses.index') }}">Cancel</a>
      <button type="submit" class="cc-btn primary">Create course</button>
    </div>
  </form>
@endsection
