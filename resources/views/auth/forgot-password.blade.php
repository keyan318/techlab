@extends('auth.layout')

@section('title', 'Forgot password')
@section('description', 'Get a temporary TechLab password by email.')
@section('action', route('password.email'))
@section('audience', $audience)
@section('noindex', '1')

@section('heading')
  <h1><span class="line">Lost your</span><span class="decode" id="decode">Password?</span></h1>
  <p class="sub">Type your email and we'll send you a temporary one.</p>
@endsection

@section('fields')
  @if (session('status'))
    <div class="ok" role="status">{{ session('status') }}</div>
  @endif
  @error('email')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror

  <input type="hidden" name="audience" value="{{ $audience }}">
  <div class="field">
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="{{ $audience === 'faculty' ? 'captain@school.edu' : 'astro@gmail.com' }}" autocomplete="email" required autofocus>
  </div>
  <button class="btn" type="submit">Email me a temporary password</button>
@endsection

@section('footer')
  <p class="foot">Remembered it? <a href="{{ $audience === 'faculty' ? route('faculty.login') : route('login') }}">Back to sign in</a></p>
@endsection
