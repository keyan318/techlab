{{--
  "Rank up!" (StudentController::rankUp): once per new rank. Light rays spin behind the new rank's badge, which
  bounces in, then the free frame that comes with it pops in around the student's astronaut, with a burst of sparks.
  $rank is LevelService::rank() plus the rank's default 'frame'. Continue, ✕, Escape or the backdrop closes it.
--}}
@props(['rank', 'look' => null, 'name' => ''])
@php
  [$c0, $c1, $c2, $c3] = $rank['colors'];
  $article = preg_match('/^[AEIOU]/i', $rank['name']) ? 'an' : 'a';
@endphp
<x-rank-styles />
<div class="rup" x-data="{ open: true }" x-show="open" x-cloak x-init="$nextTick(() => { $refs.go.focus(); window.techlabSfx?.play('win') })"
     @keydown.escape.window="open = false" style="--r0: {{ $c0 }}; --r1: {{ $c1 }}; --r2: {{ $c2 }}; --r3: {{ $c3 }};">
  <div class="rup-backdrop" @click="open = false"></div>
  <div class="rup-card px-card" role="dialog" aria-modal="true" aria-labelledby="rup-title" aria-describedby="rup-msg">
    <button type="button" class="rup-close" @click="open = false" aria-label="Close">✕</button>
    <div class="rup-stage" aria-hidden="true">
      <div class="rup-rays"></div>
      @for ($i = 0; $i < 14; $i++)
        <i class="rup-spark" style="--a: {{ $i * 360 / 14 }}deg; --d: {{ 70 + ($i % 3) * 22 }}px; --t: {{ .5 + ($i % 4) * .08 }}s"></i>
      @endfor
      <div class="rup-badge"><x-rank-badge :level="$rank['level']" size="lg" :name="false" /></div>
      <div class="rup-frame"><x-avatar-frame :frame="$rank['frame']" :level="$rank['level']" :look="$look" :name="$name" :size="120" /></div>
    </div>
    <p class="rup-kicker px-font">Rank up!</p>
    <h2 id="rup-title" class="px-font rup-name">{{ $rank['label'] }}</h2>
    <p id="rup-msg" class="rup-msg">You're {{ $article }} {{ $rank['name'] }} now, astronaut! Your new frame is on, and two more {{ $rank['name'] }} frames just went on sale in the Shop.</p>
    <div class="rup-actions">
      <a href="{{ route('student.shop') }}#frames" class="px-btn">See the frames</a>
      <button type="button" x-ref="go" class="px-btn px-btn-blue" @click="open = false">Continue</button>
    </div>
  </div>
</div>
@once
<style>
  .rup { position: fixed; inset: 0; z-index: 90; display: grid; place-items: center; padding: 16px; }
  .rup-backdrop { position: absolute; inset: 0; background: radial-gradient(circle at 50% 40%, color-mix(in srgb, var(--r0) 70%, transparent), rgba(4, 4, 18, .88) 70%); backdrop-filter: blur(3px); animation: rup-fade .4s ease both; }
  .rup-card { position: relative; width: min(440px, 100%); padding: 28px 24px 24px; text-align: center; background: var(--px-solid); animation: rup-pop .55s cubic-bezier(.2, 1.4, .4, 1) both; }
  .rup-close { position: absolute; top: 10px; right: 14px; font-size: 18px; opacity: .7; }
  .rup-close:hover { opacity: 1; }
  .rup-stage { position: relative; height: 230px; display: grid; place-items: center; }
  .rup-rays { position: absolute; width: 300px; height: 300px; border-radius: 50%; opacity: .55;
    background: repeating-conic-gradient(from 0deg, var(--r3) 0 8deg, transparent 8deg 22deg);
    -webkit-mask: radial-gradient(circle, #000 15%, transparent 68%); mask: radial-gradient(circle, #000 15%, transparent 68%);
    animation: rup-spin 18s linear infinite, rup-fade .8s ease both; }
  .rup-badge { position: absolute; top: 4px; animation: rup-drop .7s cubic-bezier(.2, 1.5, .4, 1) .15s both; }
  .rup-frame { position: absolute; bottom: 0; animation: rup-pop .6s cubic-bezier(.2, 1.5, .4, 1) .55s both; }
  .rup-spark { position: absolute; left: 50%; top: 55%; width: 7px; height: 7px; background: var(--r2); box-shadow: 0 0 8px var(--r3);
    transform: rotate(var(--a)) translateX(0) scale(0); animation: rup-burst .9s ease-out var(--t) both; }
  .rup-kicker { margin-top: 14px; font-size: 14px; letter-spacing: .3em; text-transform: uppercase; color: var(--r3); }
  .rup-name { margin-top: 4px; font-size: clamp(1.8rem, 6vw, 2.4rem); font-weight: 700; line-height: 1.05;
    background: linear-gradient(180deg, #fff, var(--r2) 45%, var(--r1)); -webkit-background-clip: text; background-clip: text; color: transparent; }
  html[data-theme="light"] .rup-name { background: none; color: var(--r0); }
  .rup-msg { margin: 10px auto 0; max-width: 34ch; font-size: 15px; color: rgb(var(--c-muted)); }
  .rup-actions { margin-top: 22px; display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; }
  @keyframes rup-fade { from { opacity: 0; } }
  @keyframes rup-pop { from { opacity: 0; transform: scale(.6); } }
  @keyframes rup-drop { from { opacity: 0; transform: translateY(-40px) scale(1.6); } }
  @keyframes rup-spin { to { transform: rotate(360deg); } }
  @keyframes rup-burst { 0% { opacity: 1; transform: rotate(var(--a)) translateX(0) scale(1); } 100% { opacity: 0; transform: rotate(var(--a)) translateX(var(--d)) scale(.4); } }
  @media (prefers-reduced-motion: reduce) { .rup *, .rup-card, .rup-backdrop { animation: none !important; } .rup-spark { display: none; } }
</style>
@endonce
