@extends('auth.layout')

@section('title', 'Sign in')
@section('description', 'Sign in to TechLab and pick up your next lesson.')
@section('action', '/login')

@section('heading')
  <h1><span class="line">Welcome back,</span><span class="decode" id="decode">Astronaut</span></h1>
  <p class="sub">Sign in to pick up where you left off.</p>
@endsection

@section('fields')
  @error('email')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror

  <div class="field">
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="astro@gmail.com" autocomplete="email" required autofocus>
  </div>
  <div class="field">
    <label for="password">Password</label>
    <input id="password" type="password" name="password" placeholder="Your password" autocomplete="current-password" required>
  </div>
  <div class="row">
    <label><input type="checkbox" name="remember"> Remember me</label>
    <a href="#">Forgot password?</a>
  </div>
  <button class="btn" type="submit">Sign in</button>
@endsection

@section('footer')
  <p class="foot">New here? <a href="/register">Create an account</a></p>
@endsection
