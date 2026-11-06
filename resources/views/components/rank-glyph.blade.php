{{-- A rank's symbol (config ranks.tiers.*.glyph), drawn in a 24×24 box centred on ($x, $y) at $scale. --}}
@props(['glyph' => 'star', 'x' => 12, 'y' => 12, 'scale' => 1, 'fill' => '#fff', 'stroke' => null])
@php $stroke ??= $fill; @endphp
<g transform="translate({{ $x - 12 * $scale }} {{ $y - 12 * $scale }}) scale({{ $scale }})" fill="{{ $fill }}" stroke="{{ $stroke }}" stroke-linecap="round" stroke-linejoin="round" {{ $attributes }}>
  @switch($glyph)
    @case('comet')
      <circle cx="15.5" cy="8.5" r="4.6" stroke="none"/>
      <path d="M12.2 11.8L3 21M14.2 13.6L8.5 19.5M10.4 9.8L4.5 15.5" fill="none" stroke-width="2.2"/>
      @break
    @case('meteor')
      <path d="M8 11.5l4.5-3.5 5.5 1.5 2 5-3.5 5H11l-4-3.5z" stroke="none"/>
      <path d="M8.5 9L3 3.5M11.5 7.5L8 2.5M6.5 12L2 9" fill="none" stroke-width="2"/>
      @break
    @case('moon')
      <path d="M15.5 2.5a9.5 9.5 0 1 0 6 16.5A8 8 0 0 1 15.5 2.5z" stroke="none"/>
      @break
    @case('orbit')
      <circle cx="12" cy="12" r="5.2" stroke="none"/>
      <ellipse cx="12" cy="12" rx="11" ry="4" transform="rotate(-25 12 12)" fill="none" stroke-width="1.7"/>
      @break
    @case('burst')
      <path d="M12 .5l2.3 6.4 6.5-2.6-3 6.2 6.7 1.5-6.7 1.5 3 6.2-6.5-2.6L12 23.5l-2.3-6.4-6.5 2.6 3-6.2L-.5 12l6.7-1.5-3-6.2 6.5 2.6z" stroke="none"/>
      @break
    @case('cloud')
      <path d="M6.8 19a4.6 4.6 0 0 1-.7-9.1 6.2 6.2 0 0 1 11.7-1.7A5.4 5.4 0 0 1 17.5 19z" stroke="none"/>
      <circle cx="9" cy="14" r="1.2" fill="#fff" stroke="none" opacity=".8"/>
      <circle cx="14.5" cy="12.5" r=".9" fill="#fff" stroke="none" opacity=".8"/>
      @break
    @case('pulsar')
      <circle cx="12" cy="12" r="4" stroke="none"/>
      <path d="M12 .8v6M12 17.2v6M4.2 4.2l3.6 3.6M16.2 16.2l3.6 3.6" fill="none" stroke-width="2.2"/>
      @break
    @case('quasar')
      <circle cx="12" cy="12" r="3.4" stroke="none"/>
      <ellipse cx="12" cy="12" rx="11" ry="3.4" transform="rotate(30 12 12)" fill="none" stroke-width="1.6"/>
      <ellipse cx="12" cy="12" rx="11" ry="3.4" transform="rotate(-30 12 12)" fill="none" stroke-width="1.6"/>
      @break
    @case('supernova')
      <path d="M12 0l1.9 5.3 4.6-3.3-1.4 5.5 5.6-.4-4.3 3.6L23.6 13l-5.6 1.2 3.4 4.5-5.4-1.6.4 5.6L12 19.2l-4.4 3.5.4-5.6-5.4 1.6 3.4-4.5L.4 13l5.2-2.3L1.3 7.1l5.6.4L5.5 2l4.6 3.3z" stroke="none"/>
      <circle cx="12" cy="11.6" r="3.6" fill="#fff" stroke="none" opacity=".9"/>
      @break
    @case('galaxy')
      <path d="M12 12c0-2.2 2.3-3.2 3.9-2s1.4 4.5-1.7 5.8-7.6-.8-7.5-5.4S11 2.6 15.2 3.2s7.6 4.3 6.4 9.6" fill="none" stroke-width="2.3"/>
      <circle cx="12" cy="12" r="2.1" stroke="none"/>
      @break
    @default
      <path d="M12 1.8l2.9 6.7 7.3.7-5.5 4.9 1.6 7.2L12 17.6l-6.3 3.7 1.6-7.2-5.5-4.9 7.3-.7z" stroke="none"/>
  @endswitch
</g>
