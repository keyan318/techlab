<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $course['title'] ?? 'Programming City' }} · TechLab</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --void: #06061a;
      --glass: rgba(123,142,220,.07);
      --glass-border: rgba(150,170,255,.18);
      --text: #eaeeff;
      --muted: #98a2d4;
      --blue: #73b6ff;
      --accent: #73b6ff;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { min-height: 100%; }
    body {
      font-family: 'Inter', system-ui, sans-serif;
      color: var(--text);
      background: var(--void);
      -webkit-font-smoothing: antialiased;
      line-height: 1.6;
    }
    a { color: inherit; text-decoration: none; }

    /* Space background — kept consistent with the other planet pages. */
    .space { position: fixed; inset: 0; z-index: -1;
      background:
        radial-gradient(120% 90% at 50% -10%, #241456 0%, rgba(36,20,86,0) 55%),
        linear-gradient(160deg, #0a0826, #120a33 45%, #1e1259);
    }
    .glow { position: absolute; border-radius: 50%; filter: blur(70px); opacity: .5; }
    .glow.g1 { width: 420px; height: 420px; top: -120px; left: -80px;
      background: radial-gradient(circle, rgba(115,182,255,0.6), transparent 70%); }
    .glow.g2 { width: 480px; height: 480px; bottom: -160px; right: -120px;
      background: radial-gradient(circle, rgba(155,107,255,0.4), transparent 70%); }

    /* Top nav — matches the rest of the planet pages. */
    .nav { position: sticky; top: 0; z-index: 50;
      display: flex; align-items: center; justify-content: space-between;
      padding: 22px clamp(20px,5vw,64px);
      backdrop-filter: blur(10px);
      background: linear-gradient(180deg, rgba(6,6,26,.6), rgba(6,6,26,0));
    }
    .brand { display: flex; align-items: center; gap: 12px;
      font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.25rem;
    }
    .brand span { color: var(--blue); }
    .nav-links { display: flex; gap: 26px; }
    .nav-links a { color: var(--muted); font-size: .95rem; }
    .nav-links a:hover { color: var(--text); }

    @media (max-width: 720px) { .nav-links { display: none; } }
  </style>
</head>
<body>
  <div class="space" aria-hidden="true">
    <div class="glow g1"></div>
    <div class="glow g2"></div>
  </div>

  <header class="nav">
    <a class="brand" href="{{ route('home') }}">Tech<span>Lab</span></a>
    <nav class="nav-links">
      <a href="{{ route('student.dashboard') }}">Dashboard</a>
      <a href="#">Quests</a>
      <a href="#">Achievements</a>
      <a href="/student/crew">Crew</a>
    </nav>
  </header>

  <x-course.course-player :course="$course" />
</body>
</html>
