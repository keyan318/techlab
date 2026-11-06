{{--
  Settings body, shared by student/settings (pixel-app shell), faculty/settings (faculty shell) and admin/settings (admin shell),
  so it sticks to the theme tokens both shells give Tailwind (ink, muted, glass, glassBorder).
  Name and password post to SettingsController; sound and theme flip the same browser switches as the sidebar.
  Admins get no Delete account; they get the Admins list, where the first admin adds and removes other admins.
--}}
@php
  $captain = $user->role !== 'student';
  $admin = $user->role === 'admin';
  $minPassword = $admin ? 12 : 8;
  $field = 'mt-1.5 block h-11 w-full rounded-[10px] border border-glassBorder bg-glass px-3.5 text-[15px] text-ink placeholder:text-muted
            focus:border-[#73b6ff] focus:outline-none focus:ring-2 focus:ring-[#73b6ff]/40';
  $card = 'rounded-[18px] border border-glassBorder bg-glass p-5 sm:p-6';
  $save = 'inline-flex h-10 items-center rounded-[10px] bg-[#14a0ff] px-5 text-[14px] font-semibold text-white hover:brightness-110
           focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#73b6ff]';
@endphp
<div class="mx-auto flex w-full max-w-[760px] flex-col gap-6 px-4 py-8 sm:px-8">

  <header class="flex items-center gap-4">
    <span class="flex h-16 w-16 flex-none items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-[#73b6ff] to-[#9b6bff] text-[20px] font-semibold text-[#06061a]" aria-hidden="true">
      @if ($user->avatar)<x-astronaut :look="$user->avatar" crop="bust" class="mt-3 h-[72px] w-[72px] flex-none" />@else{{ strtoupper(mb_substr($user->name, 0, 1)) }}@endif
    </span>
    <div class="min-w-0">
      <h1 class="font-display text-[28px] font-bold leading-tight tracking-[-0.02em] text-ink">Settings</h1>
      <p class="truncate text-[14px] text-muted">{{ $user->email }}</p>
    </div>
  </header>

  @if (session('settingsStatus'))
    <p role="status" class="rounded-[12px] border border-[#3ddc84]/40 bg-[#3ddc84]/10 px-4 py-3 text-[14px] font-medium text-[#3ddc84]">{{ session('settingsStatus') }}</p>
  @endif

  {{-- ── Profile ── --}}
  <section class="{{ $card }}" aria-labelledby="set-profile" data-tour="set-profile">
    <h2 id="set-profile" class="text-[17px] font-semibold text-ink">Profile</h2>
    <form method="POST" action="{{ route('settings.profile') }}" class="mt-4 flex flex-col gap-4">
      @csrf
      @method('PUT')
      <div class="grid gap-4 sm:grid-cols-2">
        <label class="block text-[13.5px] font-medium text-muted">First name
          <input name="first_name" type="text" required maxlength="100" autocomplete="given-name" value="{{ $errors->admin->any() ? $firstName : old('first_name', $firstName) }}" class="{{ $field }}">
        </label>
        <label class="block text-[13.5px] font-medium text-muted">Last name
          <input name="last_name" type="text" required maxlength="100" autocomplete="family-name" value="{{ $errors->admin->any() ? $lastName : old('last_name', $lastName) }}" class="{{ $field }}">
        </label>
      </div>
      @error('first_name')<p class="-mt-2 text-[13px] text-[#ff6b81]">{{ $message }}</p>@enderror
      @error('last_name')<p class="-mt-2 text-[13px] text-[#ff6b81]">{{ $message }}</p>@enderror
      @unless ($captain)
        @if (! $user->program || ! $user->year_level)
          <p class="rounded-[10px] border border-[#ffc94d]/40 bg-[#ffc94d]/10 px-3.5 py-2.5 text-[13.5px] text-ink">Pick your program and year level, then save.</p>
        @endif
        <div class="grid gap-4 sm:grid-cols-2">
          <label class="block text-[13.5px] font-medium text-muted">Program
            <select name="program" required class="{{ $field }} cursor-pointer">
              <option value="" disabled @selected(! old('program', $user->program))>Pick your program</option>
              @foreach (\App\Models\User::PROGRAMS as $code => $title)
                <option value="{{ $code }}" @selected(old('program', $user->program) === $code)>{{ $code }} · {{ $title }}</option>
              @endforeach
            </select>
          </label>
          <label class="block text-[13.5px] font-medium text-muted">Year level
            <select name="year_level" required class="{{ $field }} cursor-pointer">
              <option value="" disabled @selected(! old('year_level', $user->year_level))>Pick your year</option>
              @foreach (\App\Models\User::YEAR_LEVELS as $year => $label)
                <option value="{{ $year }}" @selected((int) old('year_level', $user->year_level) === $year)>{{ $label }}</option>
              @endforeach
            </select>
          </label>
        </div>
        @error('program')<p class="-mt-2 text-[13px] text-[#ff6b81]">{{ $message }}</p>@enderror
        @error('year_level')<p class="-mt-2 text-[13px] text-[#ff6b81]">{{ $message }}</p>@enderror
      @endunless
      <label class="block text-[13.5px] font-medium text-muted">Email
        <input type="email" value="{{ $user->email }}" disabled class="{{ $field }} cursor-not-allowed opacity-60">
      </label>
      <p class="-mt-2 text-[13px] text-muted">Your email is how you log in, so it can't be changed here.</p>
      <div><button type="submit" class="{{ $save }}">Save profile</button></div>
    </form>
  </section>

  {{-- ── Look ── --}}
  <section class="{{ $card }} flex flex-wrap items-center justify-between gap-4" aria-labelledby="set-look" data-tour="set-look">
    <div class="min-w-0">
      <h2 id="set-look" class="text-[17px] font-semibold text-ink">{{ $captain ? 'Your captain' : 'Your astronaut' }}</h2>
      <p class="mt-1 text-[14px] text-muted">
        @if ($admin)
          Your animal and suit, shown in the sidebar.
        @elseif ($captain)
          Your animal and suit. Crews see your real name.
        @else
          Your animal, suit and codename. Classmates see you as <b class="text-ink">{{ $user->display_name }}</b>.
        @endif
      </p>
    </div>
    <a href="{{ route($user->role.'.avatar') }}"
       class="inline-flex h-10 flex-none items-center rounded-[10px] border border-glassBorder px-4 text-[14px] font-semibold text-ink hover:bg-glass
              focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#73b6ff]">
      {{ $captain ? ($user->avatar ? 'Change my captain' : 'Make my captain') : 'Change my astronaut' }}
    </a>
  </section>

  {{-- ── Preferences (saved in this browser, same switches as the sidebar) ── --}}
  <section class="{{ $card }}" aria-labelledby="set-prefs" data-tour="set-prefs"
           x-data="{
             sound: !(window.techlabSfx && window.techlabSfx.muted()),
             light: window.techlabTheme && window.techlabTheme.get() === 'light',
           }"
           @techlab:sound.window="sound = $event.detail.on"
           @techlab:theme.window="light = $event.detail === 'light'">
    <h2 id="set-prefs" class="text-[17px] font-semibold text-ink">Preferences</h2>
    <p class="mt-1 text-[13px] text-muted">These are saved in this browser only.</p>
    <ul class="mt-4 divide-y divide-[var(--glass-border)]">
      <li class="flex items-center justify-between gap-4 py-3">
        <div>
          <p class="text-[15px] font-medium text-ink">Sound and music</p>
          <p class="text-[13px] text-muted">Button clicks and the space music.</p>
        </div>
        <button type="button" role="switch" :aria-checked="sound.toString()" aria-label="Sound and music"
                @click="sound = window.techlabSfx ? window.techlabSfx.toggle() : sound"
                class="relative h-7 w-12 flex-none rounded-full transition-colors duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#73b6ff]"
                :class="sound ? 'bg-[#14a0ff]' : 'bg-[rgba(150,160,200,0.35)]'">
          <span class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow transition-transform duration-200 motion-reduce:transition-none"
                :class="sound ? 'translate-x-5' : 'translate-x-0'"></span>
        </button>
      </li>
      <li class="flex items-center justify-between gap-4 py-3">
        <div>
          <p class="text-[15px] font-medium text-ink">Light mode</p>
          <p class="text-[13px] text-muted">Off means the dark space theme.</p>
        </div>
        <button type="button" role="switch" :aria-checked="light.toString()" aria-label="Light mode"
                @click="window.techlabTheme && window.techlabTheme.toggle()"
                class="relative h-7 w-12 flex-none rounded-full transition-colors duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#73b6ff]"
                :class="light ? 'bg-[#14a0ff]' : 'bg-[rgba(150,160,200,0.35)]'">
          <span class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow transition-transform duration-200 motion-reduce:transition-none"
                :class="light ? 'translate-x-5' : 'translate-x-0'"></span>
        </button>
      </li>
    </ul>
  </section>

  {{-- ── Password ── --}}
  <section class="{{ $card }}" aria-labelledby="set-password" data-tour="set-password">
    <h2 id="set-password" class="text-[17px] font-semibold text-ink">Password</h2>
    <form method="POST" action="{{ route('settings.password') }}" class="mt-4 flex flex-col gap-4">
      @csrf
      @method('PUT')
      <label class="block text-[13.5px] font-medium text-muted">Current password
        <input name="current_password" type="password" required autocomplete="current-password" class="{{ $field }}">
      </label>
      @error('current_password', 'password')<p class="-mt-2 text-[13px] text-[#ff6b81]">{{ $message }}</p>@enderror
      <label class="block text-[13.5px] font-medium text-muted">New password
        <input name="password" type="password" required minlength="{{ $minPassword }}" autocomplete="new-password" class="{{ $field }}">
      </label>
      @error('password', 'password')<p class="-mt-2 text-[13px] text-[#ff6b81]">{{ $message }}</p>@enderror
      <label class="block text-[13.5px] font-medium text-muted">Type the new password again
        <input name="password_confirmation" type="password" required minlength="{{ $minPassword }}" autocomplete="new-password" class="{{ $field }}">
      </label>
      <p class="-mt-2 text-[13px] text-muted">At least {{ $minPassword }} characters.</p>
      <div><button type="submit" class="{{ $save }}">Change password</button></div>
    </form>
  </section>

  @if ($admin)
  {{-- ── Admins (only the first admin can add or remove them) ── --}}
  <section class="{{ $card }}" aria-labelledby="set-admins">
    <h2 id="set-admins" class="text-[17px] font-semibold text-ink">Admins</h2>
    <p class="mt-1 text-[14px] text-muted">
      @if ($isOwner)
        You're the first admin, so you can add other admins and make their login for them.
      @else
        Only the first admin can add or remove admins.
      @endif
    </p>
    <ul class="mt-4 divide-y divide-[var(--glass-border)]">
      @foreach ($admins as $a)
        <li class="flex items-center justify-between gap-4 py-3">
          <div class="min-w-0">
            <p class="truncate text-[15px] font-medium text-ink">
              {{ $a->name }}
              @if ($loop->first)<span class="ml-1.5 rounded-full bg-[#14a0ff]/15 px-2 py-0.5 text-[11.5px] font-semibold text-[#73b6ff]">First admin</span>@endif
              @if ($a->id === $user->id)<span class="ml-1.5 text-[12.5px] font-normal text-muted">(you)</span>@endif
            </p>
            <p class="truncate text-[13px] text-muted">{{ $a->email }}</p>
          </div>
          @if ($isOwner && ! $loop->first)
            <form method="POST" action="{{ route('admin.admins.destroy', $a) }}"
                  onsubmit="return confirm('Remove {{ e($a->email) }} as an admin? They won\'t be able to log in any more.')">
              @csrf
              @method('DELETE')
              <button type="submit"
                      class="inline-flex h-9 flex-none items-center rounded-[10px] border border-[#ff6b81]/60 px-3.5 text-[13.5px] font-semibold text-[#ff6b81] hover:bg-[#ff6b81]/10
                             focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ff6b81]">
                Remove
              </button>
            </form>
          @endif
        </li>
      @endforeach
    </ul>

    @if ($isOwner)
      <form method="POST" action="{{ route('admin.admins.store') }}" class="mt-5 flex flex-col gap-4 border-t border-glassBorder pt-5">
        @csrf
        <h3 class="text-[15px] font-semibold text-ink">Add an admin</h3>
        <div class="grid gap-4 sm:grid-cols-2">
          <label class="block text-[13.5px] font-medium text-muted">First name
            <input name="first_name" type="text" required maxlength="100" autocomplete="off" value="{{ $errors->admin->any() ? old('first_name') : '' }}" class="{{ $field }}">
          </label>
          <label class="block text-[13.5px] font-medium text-muted">Last name
            <input name="last_name" type="text" required maxlength="100" autocomplete="off" value="{{ $errors->admin->any() ? old('last_name') : '' }}" class="{{ $field }}">
          </label>
        </div>
        @error('first_name', 'admin')<p class="-mt-2 text-[13px] text-[#ff6b81]">{{ $message }}</p>@enderror
        @error('last_name', 'admin')<p class="-mt-2 text-[13px] text-[#ff6b81]">{{ $message }}</p>@enderror
        <label class="block text-[13.5px] font-medium text-muted">Email
          <input name="email" type="email" required maxlength="255" autocomplete="off" value="{{ $errors->admin->any() ? old('email') : '' }}" class="{{ $field }}">
        </label>
        @error('email', 'admin')<p class="-mt-2 text-[13px] text-[#ff6b81]">{{ $message }}</p>@enderror
        <div class="grid gap-4 sm:grid-cols-2">
          <label class="block text-[13.5px] font-medium text-muted">Password
            <input name="password" type="password" required minlength="12" autocomplete="new-password" class="{{ $field }}">
          </label>
          <label class="block text-[13.5px] font-medium text-muted">Type it again
            <input name="password_confirmation" type="password" required minlength="12" autocomplete="new-password" class="{{ $field }}">
          </label>
        </div>
        @error('password', 'admin')<p class="-mt-2 text-[13px] text-[#ff6b81]">{{ $message }}</p>@enderror
        <p class="-mt-2 text-[13px] text-muted">At least 12 characters. Give these to the new admin; they log in at /admin/login and can change the password in their own Settings.</p>
        <div><button type="submit" class="{{ $save }}">Add admin</button></div>
      </form>
    @endif
  </section>
  @else
  {{-- ── Delete account ── --}}
  <section class="rounded-[18px] border border-[#ff6b81]/35 bg-[#ff6b81]/[0.06] p-5 sm:p-6" aria-labelledby="set-delete" data-tour="set-delete">
    <h2 id="set-delete" class="text-[17px] font-semibold text-[#ff6b81]">Delete account</h2>
    <p class="mt-1 text-[14px] text-muted">
      {{ $captain ? 'Hand off your crews and courses first.' : 'This removes your progress, XP, chats and gems.' }} It can't be undone.
    </p>
    @if (session('accountDeleteError'))
      <p role="alert" class="mt-3 text-[14px] font-medium text-[#ff6b81]">{{ session('accountDeleteError') }}</p>
    @endif
    <form method="POST" action="{{ route('account.destroy') }}" class="mt-4"
          onsubmit="return confirm('Delete your account for good? This removes your progress, XP, chats and gems — it can\'t be undone.')">
      @csrf
      @method('DELETE')
      <button type="submit"
              class="inline-flex h-10 items-center rounded-[10px] border border-[#ff6b81]/60 px-5 text-[14px] font-semibold text-[#ff6b81] hover:bg-[#ff6b81]/10
                     focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ff6b81]">
        Delete my account
      </button>
    </form>
  </section>
  @endif
</div>
