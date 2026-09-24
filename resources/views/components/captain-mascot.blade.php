{{-- The Captain (faculty): pixel face under a peaked cap, on a 16x15 grid. Decorative; eyes blink via .eye. --}}
@php
    $rows = [
        '....########....',
        '...#bbbbbbbb#...',
        '..#bbbbyybbbb#..',
        '..#bbbbbbbbbb#..',
        '.#dddddddddddd#.',
        '#dddddddddddddd#',
        '.##############.',
        '..#ssssssssss#..',
        '..#ssessssess#..',
        '..#ssssssssss#..',
        '..#ssssmmssss#..',
        '...#ssssssss#...',
        '..#wwwwwwwwww#..',
        '.#wwwwwwwwwwww#.',
        '....########....',
    ];
    $colors = ['#' => '#1b1750', 'b' => '#2f7de1', 'd' => '#1f5fb4', 'y' => '#f5c04a', 's' => '#f2c9a0', 'e' => '#1b1750', 'm' => '#c0705a', 'w' => '#eef1ff'];
@endphp
<svg viewBox="0 0 16 15" shape-rendering="crispEdges" aria-hidden="true" focusable="false" {{ $attributes }}>
  @foreach ($rows as $y => $row)
    @foreach (str_split($row) as $x => $ch)
      @if ($ch !== '.')
        <rect @if ($ch === 'e') class="eye" @endif x="{{ $x }}" y="{{ $y }}" width="1" height="1" fill="{{ $colors[$ch] }}"/>
      @endif
    @endforeach
  @endforeach
</svg>
