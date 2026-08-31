{{-- Lesson placeholder — temporary "Start learning" target until real
     lessons are built. Minimal standalone doc, family styling. --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ ucfirst($slug) }} · {{ ucwords(str_replace('-', ' ', $lesson)) }} · TechLab</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />
</head>
<body style="margin:0;background:#ffffff;display:grid;place-items:center;min-height:100vh;font-family:'Inter',system-ui,sans-serif;color:#0e1230">
  <main style="text-align:center;padding:32px 20px;max-width:420px">
    <p style="margin:0;font-family:'Space Mono',monospace;font-weight:700;font-size:.72rem;letter-spacing:.22em;color:#73b6ff">MISSION 01</p>
    <h1 style="margin:14px 0 8px;font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:1.7rem;letter-spacing:-0.02em">{{ ucwords(str_replace('-', ' ', $lesson)) }}</h1>
    <p style="margin:0;color:#6a7190;font-size:.95rem">This lesson is still being built. Astro will beam it down soon.</p>
    <a href="{{ route('student.planet.overview', ['slug' => $slug]) }}"
       style="display:inline-block;margin-top:26px;padding:13px 30px;border-radius:999px;background:#0e1230;color:#fff;text-decoration:none;font-family:'Space Grotesk',sans-serif;font-weight:600;font-size:.92rem">Back to learning plan</a>
  </main>
</body>
</html>
