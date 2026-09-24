{{--
  Pixel-art banner for the Python course card: a green scaly python arching out of the grass
  on a small alien world at night, with mossy rocks, drippy-canopy palms and a ringed planet.
  Drawn on a coarse pixel grid with crisp edges to match the pixel style.
--}}
@php
    $ground = 134;
    $stars = [[20,14],[60,30],[110,10],[150,26],[200,12],[240,28],[30,60],[130,52],[270,8],[370,14],[350,32],[180,44],[90,44],[386,50]];

    // Snake body arches (SVG path data). Each is drawn as outline + skin + scale pattern + highlight.
    $arches = [
        'M6 144C16 104 86 98 124 144',        // left hump
        'M176 144C196 112 262 108 300 144',   // right hump
        'M288 144C300 128 330 124 352 144',   // small far hump
        'M118 144C128 114 146 104 160 90C166 84 172 84 178 88', // neck
    ];

    // Tall thin palm with a flat mossy canopy that drips, like the reference.
    $tree = function (int $cx, int $h) use ($ground) {
        $top = $ground - $h;
        $o = '<rect x="'.($cx - 3).'" y="'.$top.'" width="7" height="'.$h.'" fill="#d2703f"/>';
        $o .= '<rect x="'.($cx + 1).'" y="'.$top.'" width="3" height="'.$h.'" fill="#8f3f22"/>';
        for ($y = $top + 10; $y < $ground - 4; $y += 14) {
            $o .= '<rect x="'.($cx - 3).'" y="'.$y.'" width="7" height="3" fill="#8f3f22"/>';
        }
        $o .= '<rect x="'.($cx - 22).'" y="'.($top - 4).'" width="46" height="8" fill="#2fb856"/>';
        $o .= '<rect x="'.($cx - 16).'" y="'.($top - 10).'" width="34" height="6" fill="#7bf06a"/>';
        $o .= '<rect x="'.($cx - 8).'" y="'.($top - 14).'" width="16" height="4" fill="#a6f78a"/>';
        foreach ([[-22, 8], [-14, 12], [-4, 6], [8, 10], [18, 8]] as [$dx, $dh]) {
            $o .= '<rect x="'.($cx + $dx).'" y="'.($top + 4).'" width="3" height="'.$dh.'" fill="#2fb856"/>';
        }
        return $o;
    };

    // Blue-grey rock with a mossy cap.
    $rock = function (int $x, int $w, int $h) use ($ground) {
        $y = $ground - $h;
        $o = '<rect x="'.$x.'" y="'.$y.'" width="'.$w.'" height="'.$h.'" fill="#33426e"/>';
        $o .= '<rect x="'.($x + $w - 8).'" y="'.($y + 6).'" width="8" height="'.($h - 6).'" fill="#232f57"/>';
        $o .= '<rect x="'.($x - 2).'" y="'.($y - 4).'" width="'.($w + 4).'" height="8" fill="#2fb856"/>';
        $o .= '<rect x="'.($x + 2).'" y="'.($y - 6).'" width="'.($w - 6).'" height="4" fill="#7bf06a"/>';
        $o .= '<rect x="'.($x + 4).'" y="'.($y + 4).'" width="3" height="8" fill="#2fb856"/>';
        return $o;
    };
@endphp
<svg class="scene-python-space" viewBox="0 0 400 160" preserveAspectRatio="xMidYMid slice" shape-rendering="crispEdges" aria-hidden="true" focusable="false">
  <defs>
    <linearGradient id="psSky" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#0a0830"/>
      <stop offset=".65" stop-color="#15104f"/>
      <stop offset="1" stop-color="#241a7a"/>
    </linearGradient>
    {{-- fish-scale pattern laid over the snake's skin --}}
    <pattern id="psScales" width="8" height="8" patternUnits="userSpaceOnUse">
      <rect width="8" height="8" fill="#1fa06a"/>
      <rect x="3" y="0" width="2" height="2" fill="#0c5d59"/>
      <rect x="1" y="2" width="2" height="2" fill="#0c5d59"/>
      <rect x="5" y="2" width="2" height="2" fill="#0c5d59"/>
      <rect x="3" y="4" width="2" height="2" fill="#0c5d59"/>
      <rect x="1" y="6" width="2" height="2" fill="#0c5d59"/>
      <rect x="5" y="6" width="2" height="2" fill="#0c5d59"/>
    </pattern>
  </defs>

  <rect width="400" height="160" fill="url(#psSky)"/>

  {{-- stepped nebula waves --}}
  <rect y="70" width="400" height="14" fill="#2b2a9a" opacity=".22"/>
  <rect y="84" width="400" height="18" fill="#3a2fb0" opacity=".2"/>
  <rect y="102" width="400" height="30" fill="#4a3ac0" opacity=".16"/>

  {{-- stars + a shooting star --}}
  @foreach ($stars as $i => [$sx, $sy])
    <rect class="ps-star" style="animation-delay: {{ ($i % 5) * .5 }}s" x="{{ $sx }}" y="{{ $sy }}" width="{{ $i % 3 === 0 ? 4 : 2 }}" height="{{ $i % 3 === 0 ? 4 : 2 }}" fill="#fff"/>
  @endforeach
  <rect x="208" y="54" width="4" height="3" fill="#73b6ff"/>
  <rect x="202" y="58" width="4" height="3" fill="#5a8cf0" opacity=".8"/>
  <rect x="196" y="62" width="4" height="3" fill="#4a6ad8" opacity=".6"/>
  <rect x="190" y="66" width="4" height="3" fill="#3a4ab8" opacity=".4"/>

  {{-- ringed planet, top right --}}
  <g>
    <circle cx="322" cy="62" r="38" fill="#73b6ff"/>
    <circle cx="336" cy="74" r="30" fill="#4d8fe0"/>
    <rect x="296" y="46" width="40" height="8" fill="#a9d3ff"/>
    <rect x="290" y="66" width="60" height="6" fill="#a9d3ff" opacity=".6"/>
    <rect x="262" y="60" width="120" height="8" fill="#f5c04a" opacity=".9"/>
    <rect x="272" y="68" width="100" height="4" fill="#c58a1c" opacity=".8"/>
    <rect x="298" y="72" width="48" height="8" fill="#f5c04a"/>
  </g>

  {{-- back trees --}}
  {!! $tree(118, 96) !!}
  {!! $tree(228, 44) !!}

  {{-- python: body arches --}}
  @foreach ($arches as $d)
    <path d="{{ $d }}" fill="none" stroke="#0a4a3a" stroke-width="26"/>
    <path d="{{ $d }}" fill="none" stroke="url(#psScales)" stroke-width="22"/>
    <path d="{{ $d }}" fill="none" stroke="#5be08a" stroke-width="4" transform="translate(0 -9)"/>
  @endforeach

  {{-- head, raised and looking right --}}
  <polygon points="166,84 188,74 212,78 226,88 214,96 186,98 168,94" fill="#0a4a3a"/>
  <polygon points="168,84 188,76 210,80 222,88 212,94 186,96 170,92" fill="url(#psScales)"/>
  <polygon points="168,84 188,76 210,80 214,83 190,81 172,88" fill="#5be08a"/>
  <rect x="192" y="82" width="8" height="8" fill="#f5e04a"/>
  <rect x="195" y="83" width="3" height="6" fill="#0a2a20"/>
  <rect x="216" y="86" width="3" height="3" fill="#0a2a20"/>
  <rect x="214" y="92" width="8" height="2" fill="#ff4d6d"/>

  {{-- rocks --}}
  {!! $rock(72, 26, 34) !!}
  {!! $rock(244, 26, 24) !!}
  {!! $rock(318, 24, 30) !!}
  {!! $rock(372, 20, 20) !!}

  {{-- ground: bright grass with a darker soil band --}}
  <rect y="{{ $ground }}" width="400" height="8" fill="#3ddc4a"/>
  <rect y="{{ $ground }}" width="400" height="3" fill="#8bf07a"/>
  <rect y="{{ $ground + 8 }}" width="400" height="10" fill="#157a3c"/>
  <rect y="{{ $ground + 18 }}" width="400" height="8" fill="#0d5a2c"/>
  @foreach ([14, 60, 104, 150, 202, 262, 300, 346, 388] as $gx)
    <rect x="{{ $gx }}" y="{{ $ground - 6 }}" width="3" height="6" fill="#3ddc4a"/>
    <rect x="{{ $gx + 5 }}" y="{{ $ground - 4 }}" width="3" height="4" fill="#8bf07a"/>
  @endforeach

  {{-- front trees --}}
  {!! $tree(30, 90) !!}
  {!! $tree(340, 58) !!}
</svg>
