{{--
  The Shop (ShopController): frames for every rank, animals and suits, all bought with XP, and the gem exchange.
  Buying spends XP but never lowers the level. A rank's frames go on sale once the student reaches that rank.
  Buying updates the page in place; wearing something reloads it so every preview shows the new look.
--}}
@php
  $tabs = ['frames' => 'Frames', 'animals' => 'Animals', 'suits' => 'Suits', 'gems' => 'Gem exchange'];
  $next = $progress['level'] + 1;
@endphp
<x-pixel-app title="Shop">
  <x-rank-styles />
  <div class="mx-auto flex max-w-[1200px] flex-col gap-8 px-4 py-8 sm:px-8"
       x-data="shop(@js($state), @js(['buy' => route('student.shop.buy', ['kind' => '__kind__', 'key' => '__key__']), 'equip' => route('student.shop.equip'), 'exchange' => route('student.shop.exchange')]), @js($xpPerGem))">

    {{-- ═══════════ YOU ═══════════ --}}
    <header class="px-card flex flex-col gap-6 p-6 sm:flex-row sm:items-center sm:p-8" data-tour="shop-you">
      <x-avatar-frame :frame="$state['worn']['frame']" :level="$level" :look="$look" :name="$user->display_name" :size="132" :label="$user->display_name.', level '.$level" crop="full" />
      <div class="min-w-0 flex-1">
        <p class="text-[13px] font-medium uppercase tracking-[0.2em] text-muted">The Shop</p>
        <h1 class="px-font mt-1 truncate text-[clamp(1.8rem,4vw,2.6rem)] font-bold leading-none">{{ $user->display_name }}</h1>
        <div class="mt-4"><x-rank-badge :level="$level" size="md" /></div>
        <div class="mt-4 flex max-w-[460px] items-center gap-3" role="progressbar" aria-valuenow="{{ $progress['into'] }}" aria-valuemin="0" aria-valuemax="{{ $progress['needed'] }}" aria-label="XP toward level {{ $next }}">
          <div class="px-bar flex-1"><i style="width: {{ $progress['into'] / max(1, $progress['needed']) * 100 }}%"></i></div>
          <span class="flex-none text-[13px] text-muted"><b class="text-ink">{{ $progress['into'] }}</b> / {{ $progress['needed'] }} XP to Level {{ $next }}</span>
        </div>
      </div>
      <dl class="flex flex-none gap-6 sm:flex-col sm:gap-4">
        <div class="flex items-center gap-3">
          <x-px-icon name="star" :size="34" />
          <div class="flex flex-col-reverse"><dt class="text-[13px] text-muted">XP to spend</dt><dd class="px-font text-[1.5rem] font-semibold leading-none" x-text="fmt(s.xp)">{{ number_format($xp) }}</dd></div>
        </div>
        <div class="flex items-center gap-3">
          <x-px-icon name="gem" :size="34" />
          <div class="flex flex-col-reverse"><dt class="text-[13px] text-muted">Gems</dt><dd class="px-font text-[1.5rem] font-semibold leading-none" x-text="fmt(s.gems)">{{ $gems }}</dd></div>
        </div>
      </dl>
    </header>

    <p class="sr-only" aria-live="polite" x-text="message"></p>
    <div x-show="message" x-cloak x-transition.opacity class="px-card flex items-center gap-3 px-5 py-3 text-[15px]" :class="good ? 'text-[#8ff0b0]' : 'text-[#ff8da0]'">
      <span x-text="message"></span>
    </div>

    {{-- ═══════════ TABS ═══════════ --}}
    <div class="flex flex-wrap gap-2" role="tablist" aria-label="Shop sections" data-tour="shop-tabs">
      @foreach ($tabs as $id => $label)
        <button type="button" role="tab" id="tab-{{ $id }}" aria-controls="panel-{{ $id }}" class="px-btn"
                :class="tab === '{{ $id }}' ? 'px-btn-blue' : ''" :aria-selected="(tab === '{{ $id }}').toString()" @click="go('{{ $id }}')">{{ $label }}</button>
      @endforeach
    </div>

    {{-- Frames: one row per rank --}}
    <section id="panel-frames" role="tabpanel" aria-labelledby="tab-frames" x-show="tab === 'frames'" class="flex flex-col gap-6">
      <p class="text-[15px] text-muted">Every rank has a free frame and two to buy. They go on sale when you reach that rank, and stay yours forever.</p>
      @foreach ($frameTiers as $row)
        <div @class(['px-card p-5 sm:p-6', 'opacity-60' => ! $row['unlocked']]) @if ($loop->first) data-tour="shop-frames" @endif>
          <div class="flex flex-wrap items-center justify-between gap-3">
            <x-rank-badge :level="$row['from']" size="md" />
            <span class="text-[14px] text-muted">
              @if ($row['unlocked']) Unlocked @else <span class="inline-flex items-center gap-1.5"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>Reach level {{ $row['from'] }}</span> @endif
            </span>
          </div>
          <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
            @foreach ($row['frames'] as $f)
              <div class="px-inner flex flex-col items-center gap-3 p-4 text-center" :class="s.worn.frame === @js($f['key']) && 'px-me'">
                <x-avatar-frame :frame="$f['key']" :level="$level" :look="$look" :name="$user->display_name" :size="112" />
                <div>
                  <p class="px-font text-[16px] font-semibold">{{ $f['name'] }}</p>
                  <p class="text-[13px] text-muted">{{ $f['variant'] === 'default' ? 'Free with the rank' : number_format($f['price']).' XP' }}</p>
                </div>
                @if ($row['unlocked'])
                  @include('student.partials.shop-action', ['kind' => 'frame', 'key' => $f['key'], 'price' => $f['price'], 'label' => $f['name'].' frame'])
                @else
                  <span class="px-btn pointer-events-none opacity-70">Locked</span>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endforeach
    </section>

    {{-- Animals --}}
    <section id="panel-animals" role="tabpanel" aria-labelledby="tab-animals" x-show="tab === 'animals'" x-cloak class="px-card p-5 sm:p-6">
      <p class="text-[15px] text-muted">The cat is free. Every other animal is yours to keep once you buy it.</p>
      <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        @foreach ($animals as $a)
          <div class="px-inner flex flex-col items-center gap-3 p-4 text-center" :class="s.worn.animal === @js($a['key']) && 'px-me'">
            <span class="flex h-24 w-24 items-end justify-center overflow-hidden bg-[#1d1660]" aria-hidden="true"><x-astronaut :look="$a['look']" crop="bust" class="h-[104px] w-[104px]" /></span>
            <div>
              <p class="px-font text-[16px] font-semibold">{{ $a['label'] }}</p>
              <p class="text-[13px] text-muted">{{ $a['price'] ? number_format($a['price']).' XP' : 'Free' }}</p>
            </div>
            @include('student.partials.shop-action', ['kind' => 'animal', 'key' => $a['key'], 'price' => $a['price'], 'label' => $a['label']])
          </div>
        @endforeach
      </div>
    </section>

    {{-- Suits --}}
    <section id="panel-suits" role="tabpanel" aria-labelledby="tab-suits" x-show="tab === 'suits'" x-cloak class="px-card p-5 sm:p-6">
      <p class="text-[15px] text-muted">The Explorer suit is free. Change its colour and patch any time in <a href="{{ route('student.avatar') }}" class="text-[#4fa8ff] underline-offset-4 hover:underline">Settings → Change my astronaut</a>.</p>
      <div class="mt-5 grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($suits as $su)
          <div class="px-inner flex flex-col items-center gap-3 p-4 text-center" :class="s.worn.suit === @js($su['key']) && 'px-me'">
            <x-astronaut :look="$su['look']" class="h-32 w-auto" />
            <div>
              <p class="px-font text-[16px] font-semibold">{{ $su['label'] }}</p>
              <p class="text-[13px] text-muted">{{ $su['price'] ? number_format($su['price']).' XP' : 'Free' }}</p>
            </div>
            @include('student.partials.shop-action', ['kind' => 'suit', 'key' => $su['key'], 'price' => $su['price'], 'label' => $su['label'].' suit'])
          </div>
        @endforeach
      </div>
    </section>

    {{-- Gem exchange --}}
    <section id="panel-gems" role="tabpanel" aria-labelledby="tab-gems" x-show="tab === 'gems'" x-cloak class="px-card p-5 sm:p-8" data-tour="shop-gems">
      <h2 class="px-font text-[1.5rem] font-semibold">Trade gems for XP</h2>
      <p class="mt-2 max-w-[60ch] text-[15px] text-muted">
        Every gem is worth <b class="text-ink">{{ $xpPerGem }} XP</b>. You earn {{ \App\Services\StudentDashboardService::GEMS_PER_MODULE }} gems for every module you finish.
        Traded XP is for spending in the Shop. It doesn't raise your level, because levels only count XP you earn by learning.
      </p>
      <div class="mt-6 flex flex-wrap items-center gap-4">
        <div class="px-inner flex items-center gap-2 p-2">
          <button type="button" class="px-btn h-10 min-h-0 w-10 px-0" @click="trade = Math.max(1, trade - 1)" aria-label="One gem fewer">−</button>
          <label class="sr-only" for="trade-gems">Gems to trade</label>
          <input id="trade-gems" type="number" min="1" :max="Math.max(1, s.gems)" x-model.number="trade" class="w-20 bg-transparent text-center text-[1.3rem] font-semibold outline-none">
          <button type="button" class="px-btn h-10 min-h-0 w-10 px-0" @click="trade = Math.min(Math.max(1, s.gems), trade + 1)" aria-label="One gem more">+</button>
          <button type="button" class="px-btn h-10 min-h-0 px-3 text-[14px]" @click="trade = Math.max(1, s.gems)">Max</button>
        </div>
        <p class="px-font flex items-center gap-2 text-[1.3rem]">
          <x-px-icon name="gem" :size="22" /><span x-text="trade || 0"></span>
          <span class="text-muted" aria-hidden="true">→</span>
          <x-px-icon name="star" :size="22" /><span x-text="fmt((trade || 0) * rate) + ' XP'"></span>
        </p>
        <button type="button" class="px-btn px-btn-blue" data-sfx="off" @click="exchange()" :disabled="busy || !(trade >= 1) || trade > s.gems"
                x-text="s.gems < 1 ? 'No gems yet' : (trade > s.gems ? 'Not enough gems' : 'Trade')"></button>
      </div>
    </section>
  </div>

  <script>
    function shop(state, urls, rate) {
      return {
        s: state, urls, rate, tab: 'frames', busy: false, message: '', good: true, trade: Math.max(1, Math.min(state.gems, 10)),
        init() {
          const fromHash = () => { const tab = location.hash.slice(1); if (['frames', 'animals', 'suits', 'gems'].includes(tab)) this.tab = tab; };
          fromHash();
          window.addEventListener('hashchange', fromHash);
        },
        go(tab) { this.tab = tab; history.replaceState(null, '', '#' + tab); },
        fmt(n) { return Number(n).toLocaleString(); },
        owns(kind, key) { return this.s.owned[kind].includes(key); },
        say(text, good) { this.message = text; this.good = good; window.techlabSfx?.play(good ? 'win' : 'lose'); },
        async post(url, body) {
          this.busy = true;
          try {
            const res = await fetch(url, { method: 'POST', headers: { Accept: 'application/json', 'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify(body || {}) });
            const data = await res.json().catch(() => ({}));
            for (const k of ['xp', 'gems', 'owned', 'worn']) if (data[k] !== undefined) this.s[k] = data[k];
            return res.ok && data.ok ? data : (this.say(data.error || data.message || 'Something went wrong. Try again!', false), null);
          } catch (e) {
            this.say("Couldn't reach the Shop. Check your connection and try again.", false);
            return null;
          } finally {
            this.busy = false;
          }
        },
        async buy(kind, key, label) {
          const data = await this.post(this.urls.buy.replace('__kind__', kind).replace('__key__', encodeURIComponent(key)));
          if (data) this.say(`${label} is yours! Hit Wear to put it on.`, true);
        },
        async equip(kind, key) {
          if (await this.post(this.urls.equip, { kind, key })) location.reload();
        },
        async exchange() {
          const data = await this.post(this.urls.exchange, { gems: this.trade });
          if (data) { this.say(`Traded ${data.traded} gems for ${this.fmt(data.gained)} XP!`, true); this.trade = Math.max(1, Math.min(this.s.gems, this.trade)); }
        },
      };
    }
  </script>
</x-pixel-app>
