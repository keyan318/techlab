{{--
  The TechLab Terms and Conditions (TermsController). Anyone can read them at /terms.
  $mustAgree: a signed-in student or captain who hasn't agreed to this version (config/terms.php) yet.
  EnsureTermsAccepted sends them here before onboarding (/welcome) or any other page, and the
  "I agree" form at the bottom carries them on. Change the text in a way people must re-agree to?
  Bump config('terms.version').
--}}
@php
  $captain = $user?->role === 'faculty';
  $first = $user ? (explode(' ', trim($user->name))[0] ?: null) : null;
  $updated = \Illuminate\Support\Carbon::parse($version)->format('F j, Y');
  $rules = [
    ['Be yourself', 'Use your real name and keep your account to yourself. Classmates see your codename, so keep it friendly.'],
    ['Play fair', 'Quizzes are one try with a timer. In the Final Boss, leaving the exam (switching tabs, leaving full screen, screenshots) fails it and gives you a strike. Any failed final means redoing the course. Two strikes and you\'re banned from it.'],
    ['Astro is an AI', 'What you type to Astro is sent to NVIDIA\'s AI to get an answer, and it can be wrong. Don\'t share private info with it.'],
    ['Hack only the labs', 'Cybersecurity skills are for TechLab\'s labs only. Never use them on real systems without permission.'],
    ['Rewards are just for fun', 'XP, gems and suits have no money value. Cheating can lose them, and your certificate.'],
    ['Your captain can see your work', 'Captains see the progress, scores and exam results of students in their classes. We don\'t sell your info.'],
    ['Be kind', 'No bullying, spam or messing with the site. Breaking the rules can get you removed.'],
  ];
  if ($captain) {
    $rules[] = ['Captain rules', 'Keep student info private, use it only for teaching, and only upload stuff you\'re allowed to share.'];
  }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Terms and Conditions · TechLab</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <meta name="description" content="The rules of the TechLab crew: accounts, Astro the AI tutor, quizzes, the Final Boss, labs, XP and your data.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Same deep-space tokens as the sign-in pages (auth/layout) */
    :root {
      --ink: #eaeeff; --body: #b6bfe8; --muted: #98a2d4;
      --blue: #2f7de1; --blue-dark: #1f5fb4; --blue-tint: rgba(47,125,225,.22); --blue-wash: rgba(47,125,225,.10);
      --gold: #f5c04a; --surface: rgba(12,10,42,.9); --page: #06061a; --line: rgba(150,170,255,.22);
      --display: 'Space Grotesk', system-ui, sans-serif; --text: 'Inter', system-ui, sans-serif;
      --ease: cubic-bezier(.2,.9,.25,1);
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { font-family: var(--text); background: var(--page); color: var(--body); line-height: 1.65; min-height: 100vh; -webkit-font-smoothing: antialiased; }
    a { color: var(--blue); font-weight: 600; text-decoration: none; }
    a:hover { text-decoration: underline; }
    :focus-visible { outline: 3px solid var(--blue); outline-offset: 2px; border-radius: 8px; }

    main { position: relative; z-index: 1; max-width: 720px; margin: 0 auto; padding: 24px 20px 40px; }
    .top { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px; }
    .top .brand, .top .tl-brand { color: var(--ink); }

    .card { background: rgba(12,10,42,.72); -webkit-backdrop-filter: blur(24px) saturate(160%); backdrop-filter: blur(24px) saturate(160%); border: 1px solid var(--line); border-top-color: rgba(255,255,255,.28); border-radius: 22px; box-shadow: 0 28px 56px -24px rgba(0,0,0,.6); opacity: 0; transform: translateY(14px); animation: land .7s var(--ease) 60ms forwards; }
    @keyframes land { to { opacity: 1; transform: none; } }

    .hero { display: flex; gap: 20px; align-items: center; padding: 28px 32px 22px; border-bottom: 1px solid var(--line); background: radial-gradient(120% 120% at 0% 0%, rgba(47,125,225,.22), transparent 60%); border-radius: 22px 22px 0 0; }
    .hero .mascot svg { width: 76px; height: auto; animation: bob 3.6s ease-in-out infinite; }
    @keyframes bob { 0%, 100% { translate: 0 0; } 50% { translate: 0 -6px; } }
    h1 { font-family: var(--display); font-weight: 700; color: var(--ink); font-size: clamp(1.6rem, 3.4vw, 2.1rem); line-height: 1.1; letter-spacing: -0.03em; }
    .hero p { margin-top: 6px; font-size: 15px; }
    .stamp { margin-top: 8px; font-size: 12.5px; color: var(--muted); }

    .rules { list-style: none; counter-reset: r; padding: 22px 32px 6px; display: grid; gap: 14px; }
    .rules li { counter-increment: r; display: grid; grid-template-columns: 32px 1fr; column-gap: 12px; font-size: 15px; }
    .rules li::before { content: counter(r); grid-row: span 2; width: 28px; height: 28px; display: grid; place-items: center; border-radius: 50%; background: var(--blue-tint); color: var(--ink); font-family: var(--display); font-weight: 700; font-size: 14px; }
    .rules b { font-family: var(--display); font-weight: 600; color: var(--ink); font-size: 16px; }
    .fine { padding: 4px 32px 18px 76px; font-size: 13.5px; color: var(--muted); }

    /* The agree bar: sticks to the bottom of the screen while reading */
    .agree { position: sticky; bottom: 0; z-index: 2; margin-top: 8px; padding: 16px 32px 20px; border-top: 1px solid var(--line); background: rgba(9,8,34,.96); border-radius: 0 0 22px 22px; }
    .agree form { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
    .agree label { flex: 1 1 320px; display: flex; gap: 10px; align-items: flex-start; cursor: pointer; font-size: 14.5px; color: var(--ink); }
    .agree input[type=checkbox] { accent-color: var(--blue); width: 20px; height: 20px; margin-top: 2px; flex: none; }
    .btn { height: 48px; padding: 0 22px; border: 0; border-radius: 12px; cursor: pointer; background: var(--blue); color: #fff; font-family: var(--display); font-weight: 600; font-size: 16px; box-shadow: 0 4px 0 var(--blue-dark); transition: background .2s ease, transform .1s ease, box-shadow .1s ease, opacity .2s ease; }
    .btn:hover { background: #2569c2; }
    .btn:active { transform: translateY(3px); box-shadow: 0 1px 0 var(--blue-dark); }
    .btn[disabled] { opacity: .45; cursor: not-allowed; transform: none; box-shadow: 0 4px 0 var(--blue-dark); }
    .ghost { background: none; border: 0; color: var(--muted); font: inherit; font-size: 14px; cursor: pointer; text-decoration: underline; }
    .ghost:hover { color: var(--ink); }
    .err { width: 100%; background: #fff1f0; border: 1px solid #f5c2bd; color: #b42318; padding: 10px 14px; border-radius: 12px; font-size: 14px; }
    .done { padding: 16px 32px 22px; border-top: 1px solid var(--line); font-size: 14px; color: var(--muted); }

    @media (prefers-reduced-transparency: reduce) { .card { background: #0c0a2a; -webkit-backdrop-filter: none; backdrop-filter: none; } }
    @media (max-width: 820px) {
      .rules { padding: 18px 20px 4px; } .fine { padding: 4px 20px 16px; }
      .hero { padding: 22px 20px 18px; } .hero .mascot svg { width: 60px; }
      .agree, .done { padding-left: 20px; padding-right: 20px; }
      .btn { width: 100%; }
    }
    @media (max-width: 480px) { .hero { align-items: flex-start; } }
    @media (prefers-reduced-motion: reduce) {
      html { scroll-behavior: auto; }
      *, *::before, *::after { animation: none !important; transition: none !important; }
      .card { opacity: 1; transform: none; }
    }
  </style>
  <link rel="stylesheet" href="{{ asset('css/cursor.css') }}?v={{ filemtime(public_path('css/cursor.css')) }}">
</head>
<body>
  <x-cosmic-background :planet="false" />

  <main>
    <div class="top"><x-brand href="/" /></div>

    <div class="card">
      <header class="hero">
        <div class="mascot" aria-hidden="true"><x-astro-mascot class="astro" /></div>
        <div>
          <h1>@if ($mustAgree)Before we launch{{ $first ? ", {$first}" : '' }}!@else TechLab Terms @endif</h1>
          <p>{{ count($rules) }} quick rules for the TechLab crew.</p>
          <p class="stamp">Last updated {{ $updated }}</p>
        </div>
      </header>

      <ol class="rules">
        @foreach ($rules as [$title, $text])
          <li><b>{{ $title }}</b><span>{{ $text }}</span></li>
        @endforeach
      </ol>
      <p class="fine">You can delete your account any time from your account menu. If these rules change, we'll ask you to agree again.</p>

      @if ($mustAgree)
        <div class="agree">
          <form method="POST" action="{{ route('terms.accept') }}">
            @csrf
            @error('agree')<div class="err" role="alert">{{ $message }}</div>@enderror
            <label>
              <input type="checkbox" name="agree" value="1" id="agree" required>
              <span>I have read and agree to the TechLab rules.</span>
            </label>
            <button class="btn" type="submit" id="agree-btn" disabled>Agree and continue</button>
          </form>
          <form method="POST" action="{{ route('logout') }}" style="margin-top:10px">
            @csrf
            <button class="ghost" type="submit">I don't agree, log me out</button>
          </form>
        </div>
      @else
        <p class="done">
          @if ($user)
            You agreed to these Terms{{ $user->terms_accepted_at ? ' on '.$user->terms_accepted_at->format('F j, Y') : '' }}. <a href="{{ \App\Http\Controllers\AuthController::homeFor($user) }}">Back to TechLab</a>
          @else
            You'll be asked to agree to these Terms when you join. <a href="{{ route('register') }}">Create an account</a> · <a href="{{ url('/') }}">Home</a>
          @endif
        </p>
      @endif
    </div>
  </main>

  <script>
    (function () {
      var box = document.getElementById('agree'), btn = document.getElementById('agree-btn');
      if (!box || !btn) return;
      function sync() { btn.disabled = !box.checked; }
      box.addEventListener('change', sync);
      sync();
    })();
  </script>
<x-page-loader />
</body>
</html>
