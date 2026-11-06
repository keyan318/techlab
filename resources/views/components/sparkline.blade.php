{{--
  A tiny chart for a stat tile: a line with a soft fill under it, or bars. Drawn on the server, no JS.
  Props: values (list of numbers), type ("line" | "bars"), color (any CSS colour), width, height.
--}}
@props(['values' => [], 'type' => 'line', 'color' => 'var(--green)', 'width' => 120, 'height' => 44])
@php
  $values = array_values($values) ?: [0];
  $n = count($values);
  $max = max(max($values), 1);
  $pad = 3;
  $x = fn ($i) => $n > 1 ? round($pad + $i * ($width - 2 * $pad) / ($n - 1), 1) : $width / 2;
  $y = fn ($v) => round($height - $pad - ($v / $max) * ($height - 2 * $pad), 1);
  $id = 'spark'.substr(md5(json_encode($values).$color.$type), 0, 8);
@endphp
<svg {{ $attributes->merge(['class' => 'sparkline']) }} viewBox="0 0 {{ $width }} {{ $height }}" width="{{ $width }}" height="{{ $height }}" aria-hidden="true" focusable="false" style="color: {{ $color }}">
  @if ($type === 'bars')
    @php $bw = ($width - 2 * $pad) / $n; @endphp
    @foreach ($values as $i => $v)
      @php $h = max(2, ($v / $max) * ($height - 2 * $pad)); @endphp
      <rect class="spark-bar" x="{{ round($pad + $i * $bw + $bw * .18, 1) }}" y="{{ round($height - $pad - $h, 1) }}" width="{{ round($bw * .64, 1) }}" height="{{ round($h, 1) }}"
            rx="1.5" fill="currentColor" opacity="{{ $i === $n - 1 ? 1 : .45 }}" style="--i: {{ $i }}" />
    @endforeach
  @else
    @php
      $line = collect($values)->map(fn ($v, $i) => ($i ? 'L' : 'M').$x($i).' '.$y($v))->implode(' ');
      $area = $line.' L'.$x($n - 1).' '.($height - $pad).' L'.$x(0).' '.($height - $pad).' Z';
    @endphp
    <defs>
      <linearGradient id="{{ $id }}" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0" stop-color="currentColor" stop-opacity=".32" />
        <stop offset="1" stop-color="currentColor" stop-opacity="0" />
      </linearGradient>
    </defs>
    <path d="{{ $area }}" fill="url(#{{ $id }})" />
    <path class="spark-line" d="{{ $line }}" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" pathLength="1" />
    <circle cx="{{ $x($n - 1) }}" cy="{{ $y($values[$n - 1]) }}" r="3" fill="currentColor" />
  @endif
</svg>
