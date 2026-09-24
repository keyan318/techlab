{{-- Small pixel-style icons drawn on a 16x16 grid. Decorative: pair them with text. --}}
@props(['name', 'size' => 28])
<svg viewBox="0 0 16 16" width="{{ $size }}" height="{{ $size }}" shape-rendering="crispEdges" aria-hidden="true" focusable="false" {{ $attributes->merge(['class' => 'flex-none']) }}>
  @switch($name)
    @case('star')
      <polygon points="8,0 10,6 16,8 10,10 8,16 6,10 0,8 6,6" fill="#b8860b"/>
      <polygon points="8,1.5 9.6,6.4 14.5,8 9.6,9.6 8,14.5 6.4,9.6 1.5,8 6.4,6.4" fill="#f5c04a"/>
      <polygon points="8,3.5 9,7 12,8 9,9 8,12 7,9 4,8 7,7" fill="#ffe08a"/>
      @break
    @case('medal')
      <polygon points="2,1 14,1 14,9 8,16 2,9" fill="#8a4b2a"/>
      <polygon points="3.5,2.5 12.5,2.5 12.5,8.5 8,13.5 3.5,8.5" fill="#d99562"/>
      <polygon points="5.5,4.5 10.5,4.5 10.5,8 8,10.5 5.5,8" fill="#f0b98a"/>
      @break
    @case('gem')
      <polygon points="4,1 12,1 16,6 8,16 0,6" fill="#0e6fa8"/>
      <polygon points="4.6,2.2 11.4,2.2 14.4,6 8,14 1.6,6" fill="#3ec8ff"/>
      <polygon points="4.6,2.2 8,6 11.4,2.2" fill="#a6ebff"/>
      <polygon points="1.6,6 8,6 8,14" fill="#1e9bd8"/>
      @break
    @case('flame')
      <path d="M8 0C9 4 13.5 6 13.5 10.5a5.5 5.5 0 0 1-11 0C2.5 7.5 5 6 6 3c1 1 1.5 2 2 2C8 3.5 7 2 8 0Z" fill="#e0510f"/>
      <path d="M8 3C9 6 12 7 12 10.5a4 4 0 0 1-8 0C4 8.5 6 7.5 6.5 6c.7.7 1 1.2 1.5 1.2C8 5.5 7.5 4.5 8 3Z" fill="#ff8a1f"/>
      <path d="M8 8c1 2 3 2.5 3 4.5a3 3 0 0 1-6 0C5 11 7 10 8 8Z" fill="#ffd23a"/>
      @break
    @case('book')
      <rect x="2" y="0" width="12" height="16" fill="#0b4f9c"/>
      <rect x="3" y="1" width="10" height="14" fill="#1e88e5"/>
      <rect x="5" y="4" width="6" height="2" fill="#bfe0ff"/>
      <rect x="5" y="8" width="6" height="2" fill="#bfe0ff"/>
      @break
    @case('check')
      <polygon points="1,8 3,6 6,9 13,2 15,4 6,13" fill="currentColor"/>
      @break
    @case('lock')
      <rect x="3" y="7" width="10" height="8" fill="currentColor"/>
      <path d="M5 7V5a3 3 0 0 1 6 0v2" fill="none" stroke="currentColor" stroke-width="2"/>
      @break
  @endswitch
</svg>
