@extends('auth.layout')

@section('title', 'Create account')
@section('description', 'Create your TechLab account and start your first mission.')
@section('action', '/register')

@section('heading')
  <h1><span class="line">Join the crew,</span><span class="decode" id="decode">Astronaut</span></h1>
  <p class="sub">Create your account to start your first mission.</p>
@endsection

@section('fields')
  @error('first_name')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror
  @error('last_name')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror
  @error('email')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror
  @error('program')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror
  @error('year_level')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror
  @error('planets')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror
  @error('password')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror

  <div class="field half">
    <label for="first_name">First name</label>
    <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Juan" autocomplete="given-name" maxlength="100" required>
  </div>
  <div class="field half">
    <label for="last_name">Last name</label>
    <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Dela Cruz" autocomplete="family-name" maxlength="100" required>
  </div>
  <div class="field">
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="astro@gmail.com" autocomplete="email" required>
  </div>
  <div class="field half">
    <label for="password">Password</label>
    <input id="password" type="password" name="password" placeholder="8+ characters" autocomplete="new-password" required>
  </div>
  <div class="field half">
    <label for="password_confirmation">Confirm password</label>
    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repeat it" autocomplete="new-password" required>
  </div>
  <div class="field half">
    <label for="program">Program</label>
    <select id="program" name="program" required>
      <option value="" disabled @selected(! old('program'))>Pick your program</option>
      @foreach (\App\Models\User::PROGRAMS as $code => $title)
        <option value="{{ $code }}" @selected(old('program') === $code)>{{ $code }} · {{ $title }}</option>
      @endforeach
    </select>
  </div>
  <div class="field half">
    <label for="year_level">Year level</label>
    <select id="year_level" name="year_level" required>
      <option value="" disabled @selected(! old('year_level'))>Pick your year</option>
      @foreach (\App\Models\User::YEAR_LEVELS as $year => $label)
        <option value="{{ $year }}" @selected((int) old('year_level') === $year)>{{ $label }}</option>
      @endforeach
    </select>
  </div>
  <fieldset class="planet-pick">
    <legend>What do you want to learn first?</legend>
    <div class="tiles">
    @foreach ([
      'programming' => ['Programming', 'programming-logo'],
      'networking' => ['Networking', 'networking-logo'],
      'cybersecurity' => ['Cybersecurity', 'cybersecurity-logo'],
    ] as $slug => [$label, $logo])
      <label class="tile tile-{{ $slug }}" style="--i: {{ $loop->index }}">
        <input type="radio" name="planets[]" value="{{ $slug }}" @checked(in_array($slug, old('planets', []), true))>
        <span class="orb" aria-hidden="true"><x-dynamic-component :component="$logo" /></span>
        <i class="tick" aria-hidden="true"></i>
        <span class="copy"><b>{{ $label }}</b></span>
      </label>
    @endforeach
    </div>
  </fieldset>
  <button class="btn" type="submit">Create account</button>
@endsection

@section('footer')
  <p class="foot">Already part of the crew? <a href="/login">Sign in</a></p>
  <p class="foot">Next you'll agree to our <a href="{{ route('terms') }}" target="_blank" rel="noopener">Terms</a>.</p>
@endsection
