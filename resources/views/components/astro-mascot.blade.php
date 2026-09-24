{{-- Astro, the tutor: a pixel astronaut helmet on a 16x16 grid. Decorative. --}}
@php
    $rows = [
        '....########....',
        '..##wwwwwwww##..',
        '.#wwwwwwwwwwww#.',
        '.#wwvvvvvvvvww#.',
        '#wwvvvvvvvvvvww#',
        '#wwvvhhvvvvvvww#',
        '#wwvvhvvvvvvvww#',
        '#wwvvvvvvvvvvww#',
        '#wwwvvvvvvvvwww#',
        '.#wwwwvvvvwwww#.',
        '.#wwwwwwwwwwww#.',
        '..##wwwwwwww##..',
        '...#gggggggg#...',
        '...#gg#gg#gg#...',
        '....########....',
    ];
    $colors = ['#' => '#1b1750', 'w' => '#eef1ff', 'v' => '#3aa0ff', 'h' => '#c4e8ff', 'g' => '#9aa3d6'];
@endphp
<svg viewBox="0 0 16 15" shape-rendering="crispEdges" aria-hidden="true" focusable="false" {{ $attributes }}>
  @foreach ($rows as $y => $row)
    @foreach (str_split($row) as $x => $ch)
      @if ($ch !== '.')
        <rect x="{{ $x }}" y="{{ $y }}" width="1" height="1" fill="{{ $colors[$ch] }}"/>
      @endif
    @endforeach
  @endforeach
</svg>
