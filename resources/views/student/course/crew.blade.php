<x-pixel-app :title="$title.' — Crew'">
  @include('student.course.partials.hero')

  <div class="mx-auto grid max-w-[1200px] items-start gap-10 px-4 py-10 sm:px-8 xl:grid-cols-[minmax(0,1fr)_340px]">
    <div class="flex min-w-0 flex-col gap-10">

      {{-- The captain: the faculty member who runs this course. --}}
      <section class="px-card p-5 sm:p-8" aria-labelledby="captain-h">
        <h2 id="captain-h" class="px-font text-[clamp(1.4rem,3vw,2rem)] font-medium leading-tight">Captain</h2>
        @if ($captain)
          @php
            $cp = preg_split('/\s+/', trim($captain->name), -1, PREG_SPLIT_NO_EMPTY);
            $ci = mb_strtoupper(count($cp) >= 2 ? mb_substr($cp[0], 0, 1).mb_substr(end($cp), 0, 1) : mb_substr($captain->name, 0, 2));
          @endphp
          <div class="mt-6 flex items-center gap-4">
            @if ($captain->avatar)
              <div class="px-avatar px-avatar-astro flex-none" aria-hidden="true"><x-astronaut :look="$captain->avatar" class="h-[86px] w-auto" /></div>
            @else
              <div class="px-avatar" aria-hidden="true">{{ $ci }}</div>
            @endif
            <div class="min-w-0">
              <p class="truncate text-[1.25rem] font-semibold leading-tight">{{ $captain->name }}</p>
              <p class="text-[15px] text-muted">Captain of {{ $crew->name }}</p>
            </div>
          </div>
        @else
          <p class="mt-4 text-[15px] text-muted">No captain has claimed this course yet.</p>
        @endif
      </section>

      {{-- The crew's leaderboard, ranked by level (Home ranks the whole planet). --}}
      <section class="px-card p-5 sm:p-8" aria-labelledby="roster-h">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
          <h2 id="roster-h" class="px-font text-[clamp(1.4rem,3vw,2rem)] font-medium leading-tight">Crew leaderboard</h2>
          <span class="text-[15px] text-muted">{{ count($board) }} {{ \Illuminate\Support\Str::plural('member', count($board)) }} · By level</span>
        </div>

        @if ($board)
          <ol class="mt-6 flex flex-col gap-2 p-1">
            @foreach ($board as $row)
              @include('student.partials.leaderboard-row', ['row' => $row, 'i' => $loop->index, 'frameSize' => 104])
            @endforeach
          </ol>
        @else
          <p class="mt-4 text-[15px] text-muted">You're the first one here.</p>
        @endif
      </section>
    </div>

    @include('student.course.partials.rail')
  </div>
</x-pixel-app>
