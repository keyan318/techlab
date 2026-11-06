@extends('auth.layout')

@section('title', 'Choose a new password')
@section('description', 'Choose a new TechLab password.')
@section('action', route('password.new.save'))
@section('audience', $audience)
@section('noindex', '1')

@section('heading')
  <h1><span class="line">Choose a new</span><span class="decode" id="decode">Password</span></h1>
  <p class="sub">Your temporary password worked. Now pick one you'll remember.</p>
@endsection

@section('fields')
  @error('password')
    <div class="err" role="alert">{{ $message }}</div>
  @enderror

  <div class="field">
    <label for="password">New password</label>
    <input id="password" type="password" name="password" placeholder="At least 8 characters" autocomplete="new-password" minlength="8" required autofocus>
  </div>
  <div class="field">
    <label for="password_confirmation">Type it again</label>
    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Same password" autocomplete="new-password" minlength="8" required>
  </div>
  <button class="btn" type="submit">Save my new password</button>
@endsection
