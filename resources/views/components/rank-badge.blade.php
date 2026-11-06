{{--
  A student's rank: a shield emblem in the rank's colours with its symbol, the level, and the rank's name
  (LevelService::rank). It comes alive with the rank: a shine passes over it from Comet, it glows from Orbiter,
  and from Pulsar sparks orbit it. Galaxy Legend shows its stars (★N, one per level past 99).

  Props: level, size ("sm" | "md" | "lg"), name (show the rank's name next to the level).
--}}
@props(['level' => 1, 'size' => 'md', 'name' => true])
@php
  $r = \App\Services\LevelService::rank((int) $level);
  [$c0, $c1, $c2, $c3] = $r['colors'];
  $px = ['sm' => 22, 'md' => 30, 'lg' => 56][$size] ?? 30;
  $font = ['sm' => '13px', 'md' => '16px', 'lg' => '26px'][$size] ?? '16px';
  $id = 'rb'.bin2hex(random_bytes(4));
  $shield = 'M20 2l16 7v13c0 10.5-7 17.5-16 20.5C11 39.5 4 32.5 4 22V9z';
@endphp
<x-rank-styles />
<span {{ $attributes->class(['rb']) }} style="--rb-size: {{ $px }}px; --rb-c0: {{ $c0 }}; --rb-c1: {{ $c1 }}; --rb-c2: {{ $c2 }}; --rb-c3: {{ $c3 }}; font-size: {{ $font }};"
      title="{{ $r['label'] }} · Level {{ $r['level'] }}">
  <span class="rb-emblem" aria-hidden="true">
    <svg viewBox="0 0 40 44" @class(['rb-glow' => $r['motion'] >= 2])>
      <defs>
        <linearGradient id="{{ $id }}-g" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="{{ $c1 }}"/><stop offset="1" stop-color="{{ $c0 }}"/></linearGradient>
        <clipPath id="{{ $id }}-c"><path d="{{ $shield }}"/></clipPath>
      </defs>
      @if ($r['motion'] >= 3)
        <g class="spin">
          <circle cx="20" cy="22" r="21.5" fill="none" stroke="{{ $c3 }}" stroke-width="1" stroke-dasharray="2 4" opacity=".8"/>
          <circle cx="20" cy=".5" r="2" fill="{{ $c2 }}"/><circle cx="20" cy="43.5" r="1.5" fill="{{ $c3 }}"/>
        </g>
      @endif
      <path d="{{ $shield }}" fill="url(#{{ $id }}-g)" stroke="{{ $c2 }}" stroke-width="1.6" stroke-linejoin="round"/>
      <path d="M20 6l12.5 5.5v10.5c0 8.2-5.5 13.8-12.5 16.3C13 35.8 7.5 30.2 7.5 22V11.5z" fill="none" stroke="{{ $c3 }}" stroke-width=".9" opacity=".55"/>
      <x-rank-glyph :glyph="$r['glyph']" x="20" y="21" scale=".74" :fill="$c2" />
      @if ($r['motion'] >= 1)
        <g clip-path="url(#{{ $id }}-c)"><rect class="rb-shine" x="8" y="-6" width="7" height="56" fill="#fff" opacity=".35"/></g>
      @endif
    </svg>
  </span>
  <span class="rb-text">
    <span class="rb-level">Lv {{ $r['level'] }}</span>
    @if ($name)<span class="rb-name">{{ $r['label'] }}</span>@endif
  </span>
</span>
