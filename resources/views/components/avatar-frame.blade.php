{{--
  A student's astronaut inside their rank frame (\App\Services\LevelService::frames, config/ranks.php), with their level on a plaque.
  Every rank has three frames: the free default (round, clean), A (rounded square with corner gems and the rank's
  crest) and B (round, with wings, a crown and the most motion). Each rank adds its own flair behind the frame
  (twinkles, a comet tail, orbit rings, nebula clouds, a spiral galaxy…), livelier as the rank goes up.

  Props: frame (key, e.g. "nova-b"; unknown = the level's default), level (shown on the plaque), look (avatar,
  null = the name's initial), name, size (px), plaque (show the level), crop ('full' = whole body, so the suit
  shows — the profile look, used everywhere incl. leaderboards; 'bust' = tight headshot). Decorative unless given a label.
--}}
@props(['frame' => null, 'level' => 1, 'look' => null, 'name' => '', 'size' => 64, 'plaque' => true, 'label' => null, 'crop' => 'full'])
@php
  $f = \App\Services\LevelService::frame((string) $frame) ?? \App\Services\LevelService::frame(\App\Services\LevelService::defaultFrame((int) $level));
  $tier = \App\Services\LevelService::tiers()[$f['tier']];
  [$c0, $c1, $c2, $c3] = $tier['colors'];
  $v = $f['variant'];
  $motion = min(3, $tier['motion'] + ($v === 'b' ? 1 : 0));
  $glyph = $tier['glyph'];
  $id = 'af'.bin2hex(random_bytes(4));
  $round = $v !== 'a';
  $flair = $v !== 'default' || $motion >= 3;
  $spark = fn ($x, $y, $r = 4) => sprintf('M%1$s %3$sL%4$s %5$sL%6$s %2$sL%4$s %7$sL%1$s %8$sL%9$s %7$sL%10$s %2$sL%9$s %5$sZ',
      $x, $y, $y - $r, $x + $r * .25, $y - $r * .25, $x + $r, $y + $r * .25, $y + $r, $x - $r * .25, $x - $r);
@endphp
<x-rank-styles />
<span {{ $attributes->class(['af', 'af-round' => $round, 'af-sq' => ! $round]) }}
      style="--af-size: {{ (int) $size }}px; --af-c0: {{ $c0 }}; --af-c1: {{ $c1 }};"
      @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" @endif data-frame="{{ $f['key'] }}">

  {{-- Behind the picture: the glow, the rank's flair, and B's wings --}}
  <svg viewBox="0 0 120 120" aria-hidden="true" focusable="false">
    <defs>
      <radialGradient id="{{ $id }}-glow"><stop offset="0" stop-color="{{ $c3 }}" stop-opacity=".75"/><stop offset="1" stop-color="{{ $c3 }}" stop-opacity="0"/></radialGradient>
      <linearGradient id="{{ $id }}-wing" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{{ $c2 }}"/><stop offset=".55" stop-color="{{ $c1 }}"/><stop offset="1" stop-color="{{ $c0 }}"/></linearGradient>
      <filter id="{{ $id }}-blur" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="4"/></filter>
    </defs>
    @if ($motion >= 1 || $v !== 'default')
      <circle cx="60" cy="54" r="{{ $v === 'b' ? 56 : 50 }}" fill="url(#{{ $id }}-glow)" @class(['pulse' => $motion >= 1])/>
    @endif

    @if ($flair)
      <g opacity="{{ $v === 'default' ? .5 : .9 }}">
      @switch($glyph)
        @case('comet')
          <g class="drift" stroke-linecap="round" fill="none">
            <path d="M112 6L84 26" stroke="{{ $c3 }}" stroke-width="6" opacity=".5"/>
            <path d="M104 2L82 18" stroke="{{ $c2 }}" stroke-width="2.5" opacity=".7"/>
            <path d="M116 18L92 32" stroke="{{ $c2 }}" stroke-width="2" opacity=".6"/>
            <circle cx="112" cy="6" r="5" fill="{{ $c2 }}" stroke="none"/>
          </g>
          @break
        @case('meteor')
          @foreach ([[14, 30, -30], [104, 26, 30], [8, 64, -70], [112, 66, 70], [28, 96, -130], [92, 98, 130]] as [$x, $y, $a])
            <path d="M{{ $x }} {{ $y - 7 }}l4 7-4 7-4-7z" transform="rotate({{ $a }} {{ $x }} {{ $y }})" fill="{{ $loop->even ? $c1 : $c3 }}" @class(['flicker' => $motion >= 1]) style="animation-delay: {{ $loop->index * .2 }}s"/>
          @endforeach
          @break
        @case('moon')
          <path d="M30 2a20 20 0 1 0 14 34A17 17 0 0 1 30 2z" fill="{{ $c2 }}" opacity=".55" @class(['pulse' => $motion >= 1])/>
          <path d="{{ $spark(102, 14, 5) }} {{ $spark(110, 84, 3.5) }}" fill="{{ $c2 }}" class="twinkle"/>
          @break
        @case('orbit')
          <g class="spin"><circle cx="60" cy="54" r="51" fill="none" stroke="{{ $c3 }}" stroke-width="1.6" stroke-dasharray="2 6" opacity=".8"/>
            <circle cx="111" cy="54" r="4" fill="{{ $c2 }}"/><circle cx="9" cy="54" r="2.5" fill="{{ $c3 }}"/></g>
          @break
        @case('burst')
          <g class="{{ $motion >= 2 ? 'spin' : '' }}" fill="{{ $c3 }}" opacity=".45">
            @for ($i = 0; $i < 12; $i++)<path d="M57.5 54L60 -4 62.5 54z" transform="rotate({{ $i * 30 }} 60 54)"/>@endfor
          </g>
          @break
        @case('cloud')
          <g filter="url(#{{ $id }}-blur)" class="drift">
            <ellipse cx="22" cy="30" rx="20" ry="14" fill="{{ $c1 }}" opacity=".7"/><ellipse cx="100" cy="76" rx="20" ry="15" fill="{{ $c3 }}" opacity=".6"/>
            <ellipse cx="96" cy="22" rx="14" ry="10" fill="{{ $c2 }}" opacity=".5"/>
          </g>
          <path d="{{ $spark(16, 80, 3.5) }} {{ $spark(106, 46, 3) }}" fill="#fff" class="twinkle"/>
          @break
        @case('pulsar')
          <g class="spin">
            <path d="M60 54L52 -8h16zM60 54l-8 62h16z" fill="{{ $c3 }}" opacity=".45"/>
            <path d="M60 54L57 -8h6zM60 54l-3 62h6z" fill="#fff" opacity=".5"/>
          </g>
          @break
        @case('quasar')
          <g class="spin-rev" fill="none">
            <ellipse cx="60" cy="54" rx="58" ry="15" transform="rotate(30 60 54)" stroke="{{ $c3 }}" stroke-width="2" opacity=".8"/>
            <ellipse cx="60" cy="54" rx="58" ry="15" transform="rotate(-30 60 54)" stroke="{{ $c2 }}" stroke-width="1.5" opacity=".7"/>
          </g>
          @break
        @case('supernova')
          <g class="spin" fill="{{ $c3 }}" opacity=".5">
            @for ($i = 0; $i < 16; $i++)<path d="M57 54L60 {{ $i % 2 ? 4 : -6 }} 63 54z" transform="rotate({{ $i * 22.5 }} 60 54)"/>@endfor
          </g>
          <circle cx="60" cy="54" r="47" fill="none" stroke="{{ $c2 }}" stroke-width="2" opacity=".6" class="pulse"/>
          @break
        @case('galaxy')
          <g class="spin" fill="none" stroke-linecap="round">
            @for ($i = 0; $i < 3; $i++)
              <path d="M60 54c22-2 38 8 44 26" stroke="{{ $i === 1 ? $c2 : $c3 }}" stroke-width="{{ 4 - $i }}" opacity=".75" transform="rotate({{ $i * 120 }} 60 54)"/>
            @endfor
            @foreach ([[14, 20], [108, 30], [98, 100], [10, 88], [60, -2]] as [$x, $y])<circle cx="{{ $x }}" cy="{{ $y }}" r="1.6" fill="#fff" stroke="none"/>@endforeach
          </g>
          @break
        @default
          <path d="{{ $spark(12, 22, 4) }}" fill="{{ $c2 }}" class="twinkle"/>
          <path d="{{ $spark(106, 16, 5) }}" fill="{{ $c2 }}" class="twinkle"/>
          <path d="{{ $spark(110, 80, 3.5) }}" fill="{{ $c2 }}" class="twinkle"/>
          <path d="{{ $spark(10, 84, 3) }}" fill="{{ $c2 }}" class="twinkle"/>
      @endswitch
      </g>
    @endif

    @if ($v === 'b')
      @php $wing = 'M26 46C14 38 6 27 3 13c7 9 13 13 21 15-9 2-15 6-19 13 8-1 14 1 20 4-8 3-13 9-15 17 7-5 13-7 18-7z'; @endphp
      <path d="{{ $wing }}" fill="url(#{{ $id }}-wing)" stroke="{{ $c0 }}" stroke-width="1.2"/>
      <path d="{{ $wing }}" fill="url(#{{ $id }}-wing)" stroke="{{ $c0 }}" stroke-width="1.2" transform="translate(120 0) scale(-1 1)"/>
    @endif
  </svg>

  {{-- The astronaut --}}
  <span class="af-pic">
    @if (! empty($look))
      <x-astronaut :look="$look" :crop="$crop" />
    @else
      <span class="af-initial">{{ mb_strtoupper(mb_substr((string) $name, 0, 1)) ?: '?' }}</span>
    @endif
  </span>

  {{-- In front: the rim, the crest and the level plaque --}}
  <svg viewBox="0 0 120 120" aria-hidden="true" focusable="false">
    <defs>
      <linearGradient id="{{ $id }}-rim" x1="0" y1="0" x2=".4" y2="1"><stop offset="0" stop-color="{{ $c2 }}"/><stop offset=".45" stop-color="{{ $c1 }}"/><stop offset="1" stop-color="{{ $c0 }}"/></linearGradient>
      <linearGradient id="{{ $id }}-plate" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="{{ $c1 }}"/><stop offset="1" stop-color="{{ $c0 }}"/></linearGradient>
    </defs>

    @if ($round)
      <circle cx="60" cy="54" r="38" fill="none" stroke="{{ $c0 }}" stroke-width="8"/>
      <circle cx="60" cy="54" r="38" fill="none" stroke="url(#{{ $id }}-rim)" stroke-width="{{ $v === 'b' ? 6 : 5 }}"/>
      <circle cx="60" cy="54" r="34.6" fill="none" stroke="{{ $c2 }}" stroke-width="1" opacity=".5"/>
      @if ($motion >= 3 || $v === 'b')
        <circle cx="60" cy="54" r="38" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" opacity=".85" class="sweep"/>
      @endif
      @if ($v === 'b')
        <g class="spin-rev"><circle cx="60" cy="54" r="43.5" fill="none" stroke="{{ $c3 }}" stroke-width="1.3" stroke-dasharray="3 4.5"/></g>
      @endif
    @else
      <rect x="22" y="16" width="76" height="76" rx="18" fill="none" stroke="{{ $c0 }}" stroke-width="8"/>
      <rect x="22" y="16" width="76" height="76" rx="18" fill="none" stroke="url(#{{ $id }}-rim)" stroke-width="5"/>
      <rect x="25.6" y="19.6" width="68.8" height="68.8" rx="15" fill="none" stroke="{{ $c2 }}" stroke-width="1" opacity=".5"/>
      @if ($motion >= 3)
        <rect x="22" y="16" width="76" height="76" rx="18" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" opacity=".85" class="sweep" pathLength="240"/>
      @endif
      @foreach ([[24, 18], [96, 18], [24, 90], [96, 90]] as [$x, $y])
        <path d="M{{ $x }} {{ $y - 6 }}l6 6-6 6-6-6z" fill="{{ $c3 }}" stroke="{{ $c0 }}" stroke-width="1.5"/>
        <path d="M{{ $x }} {{ $y - 3 }}l2 3h-4z" fill="#fff" opacity=".7"/>
      @endforeach
    @endif

    {{-- Crest --}}
    @if ($v === 'default')
      <path d="M60 9l6 7-6 7-6-7z" fill="{{ $c2 }}" stroke="{{ $c0 }}" stroke-width="1.5"/>
      <path d="M60 12l2.5 4h-5z" fill="#fff" opacity=".7"/>
    @elseif ($v === 'a')
      <circle cx="60" cy="13" r="10" fill="url(#{{ $id }}-plate)" stroke="{{ $c2 }}" stroke-width="1.6"/>
      <x-rank-glyph :glyph="$glyph" x="60" y="13" scale=".62" :fill="$c2" />
    @else
      <path d="M40 18l6-14 7 9 7-12 7 12 7-9 6 14z" fill="url(#{{ $id }}-rim)" stroke="{{ $c0 }}" stroke-width="1.5" stroke-linejoin="round"/>
      <circle cx="60" cy="1" r="2.6" fill="{{ $c3 }}" @class(['pulse' => $motion >= 1])/>
      <x-rank-glyph :glyph="$glyph" x="60" y="12" scale=".5" fill="#fff" />
    @endif

    {{-- Level plaque --}}
    @if ($plaque)
      @if ($v === 'b')
        <path d="M30 90h10l-4 6 4 6H30l3-6z M90 90H80l4 6-4 6h10l-3-6z" fill="{{ $c0 }}" stroke="{{ $c2 }}" stroke-width="1"/>
        <rect x="38" y="86" width="44" height="18" rx="5" fill="url(#{{ $id }}-plate)" stroke="{{ $c2 }}" stroke-width="1.6"/>
      @elseif ($v === 'a')
        <path d="M44 86h32l6 8.5-6 8.5H44l-6-8.5z" fill="url(#{{ $id }}-plate)" stroke="{{ $c2 }}" stroke-width="1.6"/>
      @else
        <rect x="42" y="86" width="36" height="17" rx="4.5" fill="url(#{{ $id }}-plate)" stroke="{{ $c2 }}" stroke-width="1.4"/>
      @endif
      <text x="60" y="98.6" text-anchor="middle" class="af-num" fill="#fff" stroke="{{ $c0 }}" stroke-width="2.4" paint-order="stroke">{{ (int) $level }}</text>
    @endif
  </svg>
</span>
