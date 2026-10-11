<x-pixel-app title="My Courses">
  <div class="mx-auto max-w-[1200px] px-4 py-10 sm:px-8">
    <header>
      <h1 class="px-font text-[clamp(2rem,5vw,3.2rem)] font-bold leading-none">My Courses</h1>
      <p class="mt-4 max-w-[60ch] text-[16px] leading-relaxed text-muted">Every course you've been accepted into. Open one for its lessons, quizzes and crew.</p>
    </header>

    @if ($courses)
      <ul class="mt-10 grid gap-6 sm:grid-cols-2 xl:grid-cols-3" aria-label="Your courses">
        @foreach ($courses as $c)
          <li>
            <a href="{{ $c['url'] }}" class="px-card flex h-full flex-col overflow-hidden transition hover:-translate-y-0.5">
              {{-- Same pixel-art banner as the course's card on its planet page. --}}
              <span class="relative block aspect-[5/2] w-full overflow-hidden" style="background: {{ $c['banner'] }}" aria-hidden="true">
                @if ($c['art'])
                  <img src="{{ asset($c['art']) }}" width="1250" height="500" alt="" decoding="async" class="absolute inset-0 h-full w-full object-cover object-bottom [image-rendering:pixelated]">
                @elseif ($c['scene'])
                  <span class="absolute inset-0 [&>svg]:h-full [&>svg]:w-full"><x-dynamic-component :component="$c['scene']" /></span>
                @endif
              </span>
              <span class="flex min-w-0 flex-1 flex-col gap-2 p-5">
                <span class="text-[12px] font-medium uppercase tracking-[0.18em] text-muted">{{ $c['planet'] }}</span>
                <span class="px-font text-[1.3rem] font-medium leading-tight">{{ $c['name'] }}</span>
                @if ($c['blurb'])
                  <span class="line-clamp-2 text-[14px] leading-relaxed text-muted">{{ $c['blurb'] }}</span>
                @endif
                @if ($c['captain'])
                  <span class="mt-auto pt-3 text-[13px] text-muted">Captain: {{ $c['captain'] }}</span>
                @endif
              </span>
            </a>
          </li>
        @endforeach
      </ul>
    @else
      <section class="px-card mt-10 p-8">
        <p class="px-font text-2xl font-medium">You haven't joined a course yet</p>
        <p class="mt-2 max-w-[56ch] text-muted">Pick a planet, ask to join a course, and your captain will send you a code. Once you're in, the course shows up here.</p>
        <a href="{{ route('student.planets') }}" class="px-btn px-btn-yellow mt-6">Browse planets</a>
      </section>
    @endif
  </div>
</x-pixel-app>
