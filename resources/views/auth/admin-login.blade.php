@extends('auth.layout')

@section('title', 'Admin sign in')
@section('description', 'Admin sign in to TechLab.')
@section('action', '/admin/login')
@section('audience', 'admin')
@section('noindex', '1')

@section('heading')
  <h1><span class="line">Admin</span><span class="decode" id="decode">Sign in</span></h1>
  <p class="sub">Authorized staff only.</p>
@endsection

@section('fields')
  @error('email')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror

  <div class="field">
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
  </div>
  <div class="field">
    <label for="password">Password</label>
    <input id="password" type="password" name="password" autocomplete="current-password" required>
  </div>
  <button class="btn" type="submit">Sign in</button>
@endsection

@section('footer')
@endsection
