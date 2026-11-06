{{-- Astro, the tutor: a pixel astronaut helmet with a living face on a big visor.
     The art lives here; <x-pixel-mascot> draws it and does the blinking, looking,
     talking and click reactions. Decorative. --}}
@php
    $rows = [
        '.....##########.....',
        '...##wwwwwwwwww##...',
        '..#wwwwwwwwwwwwww#..',
        '.#wwwwhhvvvvvvwwww#.',
        '.#wwwhvvvvvvvvvwww#.',
        '#wwwvvvvvvvvvvvvwww#',
        '#wwvvvvvvvvvvvvvvww#',
        '#wwvvvvvvvvvvvvvvww#',
        '#wwvvvvvvvvvvvvvvww#',
        '#wwvvvvvvvvvvvvvvww#',
        '#wwvvvvvvvvvvvvvvww#',
        '#wwwvvvvvvvvvvvvwww#',
        '.#wwwvvvvvvvvvvwww#.',
        '.#wwwwddddddddwwww#.',
        '..#wwwwwwwwwwwwww#..',
        '...##wwwwwwwwww##...',
        '....#gggggggggg#....',
        '....#ggg#gg#ggg#....',
        '.....##########.....',
    ];
    $colors = ['#' => '#1b1750', 'w' => '#eef1ff', 'v' => '#3aa0ff', 'd' => '#2b86e6', 'h' => '#c4e8ff', 'g' => '#9aa3d6'];

    // Face layers: colour => [x, y] pixels. k = eye/mouth black, s = eye shine, p = pink, b = blush.
    $ink = ['k' => '#14112e', 's' => '#ffffff', 'p' => '#ff5c8a', 'b' => '#ff9ec4'];
    $eyes = [
        'eye-open'  => ['k' => [[6,6],[6,7],[7,7],[6,8],[7,8], [12,6],[12,7],[13,7],[12,8],[13,8]], 's' => [[7,6],[13,6]]],
        'eye-shut'  => ['k' => [[6,8],[7,8], [12,8],[13,8]]],
        'eye-happy' => ['k' => [[6,6],[7,6],[5,7],[8,7], [12,6],[13,6],[11,7],[14,7]]],
        'eye-wink'  => ['k' => [[6,6],[6,7],[7,7],[6,8],[7,8], [12,6],[13,6],[11,7],[14,7]], 's' => [[7,6]]],
        'eye-big'   => ['k' => [[5,5],[6,5],[5,6],[6,6],[7,6],[5,7],[6,7],[7,7],[6,8],[7,8],
                                [12,5],[13,5],[12,6],[13,6],[14,6],[12,7],[13,7],[14,7],[13,8],[14,8]],
                        's' => [[7,5],[5,8],[14,5],[12,8]]],
        'eye-heart' => ['p' => [[5,6],[7,6],[5,7],[6,7],[7,7],[6,8], [12,6],[14,6],[12,7],[13,7],[14,7],[13,8]]],
        'eye-x'     => ['k' => [[5,6],[7,6],[6,7],[5,8],[7,8], [12,6],[14,6],[13,7],[12,8],[14,8]]],
    ];
    $mouths = [
        'm-smile' => ['k' => [[7,11],[12,11],[8,12],[9,12],[10,12],[11,12]]],
        'm-grin'  => ['k' => [[7,11],[8,11],[9,11],[10,11],[11,11],[12,11],[8,12],[11,12],[9,13],[10,13]], 'p' => [[9,12],[10,12]]],
        'm-hmm'   => ['k' => [[8,12],[9,12],[10,12],[11,11]]],
        'm-wavy'  => ['k' => [[8,11],[9,11],[12,11],[7,12],[10,12],[11,12]]],
        'm-ooh'   => ['k' => [[9,10],[10,10],[8,11],[11,11],[8,12],[11,12],[9,13],[10,13]], 'p' => [[9,11],[10,11],[9,12],[10,12]]],
        // Talking frames: wide, half, shut, small "o"
        'm-wide'  => ['k' => [[8,11],[9,11],[10,11],[11,11],[8,12],[11,12],[9,13],[10,13]], 'p' => [[9,12],[10,12]]],
        'm-half'  => ['k' => [[8,11],[9,11],[10,11],[11,11],[9,12],[10,12]]],
        'm-shut'  => ['k' => [[8,12],[9,12],[10,12],[11,12]]],
        'm-o'     => ['k' => [[9,11],[10,11],[9,12],[10,12]]],
    ];
    $blush = ['b' => [[4,10],[5,10],[14,10],[15,10]]];
@endphp
<x-pixel-mascot :rows="$rows" :colors="$colors" :ink="$ink" :eyes="$eyes" :mouths="$mouths" :blush="$blush" {{ $attributes->class('astro-mascot') }} />
