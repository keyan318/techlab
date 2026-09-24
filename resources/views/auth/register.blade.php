@extends('auth.layout')

@section('title', 'Create account')
@section('description', 'Create your TechLab account and start your first mission.')
@section('action', '/register')

@section('heading')
  <h1><span class="line">Join the crew,</span><span class="decode" id="decode">Astronaut</span></h1>
  <p class="sub">Create your account to start your first mission.</p>
@endsection

@section('fields')
  @error('name')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror
  @error('email')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror
  @error('planets')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror
  @error('password')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror

  <div class="field">
    <label for="name">Full name</label>
    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Your full name" autocomplete="name" required>
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
  <fieldset class="planet-pick">
    <legend>What do you want to learn first?</legend>
    <p class="hint">Pick one planet to start. Finish a module there to earn a gem — collect one and the next planet unlocks.</p>
    <div class="tiles">
    @foreach ([
      'programming' => ['Programming', 'Python and more'],
      'networking' => ['Networking', 'How machines talk'],
      'cybersecurity' => ['Cybersecurity', 'Stay safe online'],
    ] as $slug => [$label, $note])
      <label>
        <input type="radio" name="planets[]" value="{{ $slug }}" @checked(in_array($slug, old('planets', []), true))>
        <span><b>{{ $label }}</b><small>{{ $note }}</small></span>
      </label>
    @endforeach
    </div>
  </fieldset>
  <button class="btn" type="submit">Create account</button>
@endsection

@section('footer')
  <p class="foot">Already part of the crew? <a href="/login">Sign in</a></p>
@endsection
