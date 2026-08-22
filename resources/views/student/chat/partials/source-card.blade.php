@php
  $type = $type ?? 'link';
  $meta = $meta ?? '';
  $guideSummary = $guideSummary ?? '';
  $guideLinks = $guideLinks ?? [];
@endphp

@php
  $typeIcon = match($type) {
    'pdf' => '<path d="M6 2h8l4 4v16H6z"/><path d="M14 2v4h4"/><path d="M9 13h1.4a1.6 1.6 0 0 1 0 3.2H9v-3.2zm0 3.2v2"/>',
    'ppt' => '<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M12 17v4M8 21h8M9 9h2M9 13h2"/>',
    default => '<path d="M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1"/><path d="M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/>',
  };
@endphp

<div x-data="{ expanded: false }" class="rounded-[14px] border border-glassBorder bg-glass transition hover:border-[rgba(115,182,255,0.5)]">
  <button
    type="button"
    @click="expanded = !expanded"
    class="flex w-full items-center gap-3 p-3 text-left"
    :aria-expanded="expanded"
  >
    <span class="grid h-9 w-9 flex-none place-items-center rounded-[10px] bg-[rgba(115,182,255,0.12)] text-[#73b6ff]">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-[18px] w-[18px]">
        {!! $typeIcon !!}
      </svg>
    </span>
    <span class="min-w-0 flex-1">
      <span class="block truncate text-sm font-medium text-ink">{{ $title }}</span>
      @if($meta)<span class="block truncate text-xs text-muted">{{ $meta }}</span>@endif
    </span>
    <span class="flex flex-none items-center gap-1.5 text-muted">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 opacity-70">
        <path d="M14 4h6v6M20 4l-9 9M19 13v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h6"/>
      </svg>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        class="h-4 w-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''">
        <path d="m6 9 6 6 6-6"/>
      </svg>
    </span>
  </button>

  <div x-show="expanded" x-collapse x-cloak class="border-t border-glassBorder px-3 py-3">
    <p class="text-xs leading-relaxed text-muted">{{ $guideSummary }}</p>
    @if(count($guideLinks))
      <p class="mt-2 font-mono text-[10px] uppercase tracking-[0.18em] text-[#5be1ff]">Extracted</p>
      <ul class="mt-1.5 space-y-1">
        @foreach($guideLinks as $link)
          <li>
            <a href="{{ $link['href'] ?? '#' }}" class="inline-flex items-center gap-1.5 text-xs text-[#73b6ff] hover:underline">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5">
                <path d="M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1"/><path d="M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/>
              </svg>
              {{ $link['label'] }}
            </a>
          </li>
        @endforeach
      </ul>
    @endif
  </div>
</div>
