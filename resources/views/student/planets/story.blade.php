{{-- Chapter story host — full-screen dark standalone doc (auth/login pattern).
     Wraps <x-chapter-story />; Begin Mission hands off to the lesson. --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chapter {{ $chapterNumber }} · {{ ucfirst($lesson) }} · TechLab</title>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />
</head>
<body style="margin:0">
  <x-chapter-story
    :chapter-number="$chapterNumber"
    :chapter-title="$chapterTitle"
    :mission-url="route('student.planet.lesson', ['slug' => $slug, 'lesson' => $lesson])" />
</body>
</html>
