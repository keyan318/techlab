<x-pixel-app :title="$title.' — Supplementary Materials'">
  @include('student.course.partials.hero')

  <div class="mx-auto grid max-w-[1200px] items-start gap-10 px-4 py-10 sm:px-8 xl:grid-cols-[minmax(0,1fr)_340px]">
    <section class="px-card min-w-0 p-5 sm:p-8" aria-labelledby="materials-h">
      <header class="flex flex-wrap items-baseline justify-between gap-2">
        <h2 id="materials-h" class="px-font text-[clamp(1.4rem,3vw,2rem)] font-medium leading-tight">Supplementary materials</h2>
        <p class="text-[15px] text-muted">Added by {{ $crew?->faculty?->name ?? 'your captain' }}</p>
      </header>

      @forelse ($modules as $m)
        <article class="px-inner mt-6 rounded-sm p-4 sm:p-6" aria-labelledby="mod-{{ $m['number'] }}">
          <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
            <span class="px-font text-[15px] text-muted">{{ $m['number'] }}</span>
            <h3 id="mod-{{ $m['number'] }}" class="min-w-0 flex-1 text-[1.15rem] font-semibold">{{ $m['title'] }}</h3>
            <span class="text-[13px] text-muted">{{ $m['material_count'] }} {{ \Illuminate\Support\Str::plural('file', $m['material_count']) }}</span>
          </div>

          @if ($m['description'])
            <p class="mt-2 text-[15px] leading-relaxed text-muted">{{ $m['description'] }}</p>
          @endif

          @if ($m['materials'])
            <ul class="mt-4 flex flex-col gap-2">
              @foreach ($m['materials'] as $f)
                <li>
                  @if ($f['is_link'])
                    <a href="{{ $f['url'] }}" target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-3 rounded-[10px] px-3 py-2.5 transition hover:bg-glass">
                      <span class="grid h-8 w-8 flex-none place-items-center rounded-[8px] bg-glass font-mono text-[10px] font-bold uppercase text-[#73b6ff]">{{ $f['ext'] }}</span>
                      <span class="min-w-0 flex-1 truncate text-[15px]">{{ $f['name'] }}</span>
                      <x-px-icon name="book" :size="16" />
                    </a>
                  @else
                    {{-- Click to preview it right here, Google Classroom-style, instead of downloading straight away. --}}
                    <button type="button"
                       @click="$store.filePreview.open({
                         name: @js($f['name']),
                         kind: @js($f['ext']),
                         viewUrl: @js($f['previewUrl']),
                         downloadUrl: @js($f['downloadUrl']),
                         googleUrl: @js($f['googleUrl']),
                         googleEmbedUrl: @js($f['googleEmbedUrl']),
                       })"
                       class="flex w-full items-center gap-3 rounded-[10px] px-3 py-2.5 text-left transition hover:bg-glass">
                      <span class="grid h-8 w-8 flex-none place-items-center rounded-[8px] bg-glass font-mono text-[10px] font-bold uppercase text-[#73b6ff]">{{ $f['ext'] }}</span>
                      <span class="min-w-0 flex-1 truncate text-[15px]">{{ $f['name'] }}</span>
                      <x-px-icon name="book" :size="16" />
                    </button>
                  @endif
                </li>
              @endforeach
            </ul>
          @endif
        </article>
      @empty
        <p class="mt-4 text-[15px] leading-relaxed text-muted">No supplementary materials yet. Anything your captain uploads — slides, handouts, readings — shows up here, and Astro will answer from it.</p>
      @endforelse
    </section>

    @include('student.course.partials.rail')
  </div>
</x-pixel-app>
