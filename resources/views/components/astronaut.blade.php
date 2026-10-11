{{-- The student's pixel astronaut (AvatarService). `crop`: "full" body or "bust" (head and shoulders). Decorative unless given a `label`. --}}
@props(['look' => null, 'crop' => 'full', 'label' => null])
<svg viewBox="{{ \App\Services\AvatarService::CROPS[$crop] ?? \App\Services\AvatarService::CROPS['full'] }}" shape-rendering="crispEdges"
     @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" focusable="false" @endif
     {{ $attributes }}>{!! \App\Services\AvatarService::rects($look) !!}</svg>
