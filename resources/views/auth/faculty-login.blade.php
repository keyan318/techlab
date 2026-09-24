@extends('auth.layout')

@section('title', 'Faculty sign in')
@section('description', 'Faculty sign in to TechLab.')
@section('action', '/faculty/login')
@section('audience', 'faculty')
@section('noindex', '1')

@section('heading')
  <h1><span class="line">Welcome back,</span><span class="decode" id="decode">Captain</span></h1>
  <p class="sub">Sign in to your faculty account.</p>
@endsection

@section('fields')
  @error('email')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror

  <div class="field">
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="captain@school.edu" autocomplete="email" required autofocus>
  </div>
  <div class="field">
    <label for="password">Password</label>
    <input id="password" type="password" name="password" placeholder="Your password" autocomplete="current-password" required>
  </div>
  <div class="row">
    <label><input type="checkbox" name="remember"> Remember me</label>
  </div>
  <button class="btn" type="submit">Sign in</button>
@endsection

@section('footer')
  <p class="foot">New faculty? <a href="/faculty/register">Create an account</a></p>
  <p class="foot"><a href="/faculty">About TechLab for faculty</a> · <a href="/login">Student sign in</a></p>
@endsection
