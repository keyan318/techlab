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
            <div class="px-avatar" aria-hidden="true">{{ $ci }}</div>
            <div class="min-w-0">
              <p class="truncate text-[1.25rem] font-semibold leading-tight">{{ $captain->name }}</p>
              <p class="text-[15px] text-muted">Captain of {{ $crew->name }}</p>
            </div>
          </div>
        @else
          <p class="mt-4 text-[15px] text-muted">No captain has claimed this course yet.</p>
        @endif
      </section>

      {{-- The crew: everyone else on the roster. --}}
      <section class="px-card p-5 sm:p-8" aria-labelledby="roster-h">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
          <h2 id="roster-h" class="px-font text-[clamp(1.4rem,3vw,2rem)] font-medium leading-tight">Crew</h2>
          <span class="text-[15px] text-muted">{{ $roster->count() }} {{ \Illuminate\Support\Str::plural('member', $roster->count()) }}</span>
        </div>

        @if ($roster->isNotEmpty())
          <ul class="mt-6 flex flex-col gap-1">
            @foreach ($roster as $member)
              @php
                $mp = preg_split('/\s+/', trim($member->name), -1, PREG_SPLIT_NO_EMPTY);
                $mi = mb_strtoupper(count($mp) >= 2 ? mb_substr($mp[0], 0, 1).mb_substr(end($mp), 0, 1) : mb_substr($member->name, 0, 2));
              @endphp
              <li class="flex items-center gap-4 rounded-[10px] px-3 py-2.5 {{ $member->is($user) ? 'bg-glass' : '' }}">
                @if ($member->avatar)
                  <div class="px-avatar overflow-hidden" style="place-items:end center;background:#1d1660" aria-hidden="true"><x-astronaut :look="$member->avatar" crop="bust" class="h-12 w-12" /></div>
                @else
                  <div class="px-avatar" aria-hidden="true">{{ $mi }}</div>
                @endif
                <span class="min-w-0 flex-1 truncate text-[16px] {{ $member->is($user) ? 'font-semibold' : '' }}">{{ $member->display_name }}</span>
                @if ($member->is($user))
                  <span class="rounded-full px-3 py-1 text-[12px] font-semibold uppercase tracking-[0.12em] text-[#73b6ff]">You</span>
                @endif
              </li>
            @endforeach
          </ul>
        @else
          <p class="mt-4 text-[15px] text-muted">You're the first one here.</p>
        @endif
      </section>
    </div>

    @include('student.course.partials.rail')
  </div>
</x-pixel-app>
