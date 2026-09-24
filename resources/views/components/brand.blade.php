{{-- TechLab logo + animated wordmark for the public pages. Styles live in public/css/cosmic-background.css
     (loaded by <x-cosmic-background />). The logo image itself is unchanged. --}}
@props(['href' => '/', 'label' => 'TechLab home', 'badge' => null])
<a href="{{ $href }}" class="tl-brand" aria-label="{{ $label }}">
  <img class="tl-logo" src="{{ asset('apple-touch-icon.png') }}" alt="" width="56" height="56">
  <span class="tl-word" aria-hidden="true">@foreach (str_split('TechLab') as $i => $c)<span style="--i:{{ $i }}">{{ $c }}</span>@endforeach</span>
  @if ($badge)<span class="tl-badge">{{ $badge }}</span>@endif
</a>
