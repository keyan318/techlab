@extends('auth.layout')

@section('title', 'Create faculty account')
@section('description', 'Create your TechLab faculty account with your school email.')
@section('action', '/faculty/register')
@section('audience', 'faculty')
@section('noindex', '1')

@section('heading')
  <h1><span class="line">Join as</span><span class="decode" id="decode">Captain</span></h1>
  <p class="sub">Create your faculty account with your school email.</p>
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
  @error('password')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror

  <div class="field half">
    <label for="first_name">First name</label>
    <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Juan" autocomplete="given-name" maxlength="100" required autofocus>
  </div>
  <div class="field half">
    <label for="last_name">Last name</label>
    <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Dela Cruz" autocomplete="family-name" maxlength="100" required>
  </div>
  <div class="field">
    <label for="email">School email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="{{ 'name@'.(config('faculty.email_domain') ?: 'school.edu') }}" autocomplete="email" required>
  </div>
  <div class="field half">
    <label for="password">Password</label>
    <input id="password" type="password" name="password" placeholder="8+ characters" autocomplete="new-password" required>
  </div>
  <div class="field half">
    <label for="password_confirmation">Confirm password</label>
    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repeat it" autocomplete="new-password" required>
  </div>
  <button class="btn" type="submit">Create account</button>
@endsection

@section('footer')
  <p class="foot">Already have an account? <a href="/faculty/login">Sign in</a></p>
  <p class="foot">Next you'll agree to our <a href="{{ route('terms') }}" target="_blank" rel="noopener">Terms</a>.</p>
@endsection
