@php
  $sections = $course['sections'] ?? [];
  $lessonNumber = 0;
@endphp
<style>
  .cp-wrap { min-height: 100vh; background: #0b1020; color: #e6ebf5; font-family: Inter, system-ui, sans-serif; padding: 32px 16px 64px; }
  .cp-inner { max-width: 820px; margin: 0 auto; }
  .cp-back { color: #73b6ff; text-decoration: none; font-size: 14px; }
  .cp-tag { margin-top: 24px; font: 700 12px 'Space Mono', monospace; letter-spacing: .12em; text-transform: uppercase; color: #73b6ff; }
  .cp-title { margin: 6px 0 8px; font: 700 36px 'Space Grotesk', sans-serif; }
  .cp-blurb { margin: 0 0 32px; color: #9aa7c2; line-height: 1.6; }
  .cp-section { margin-bottom: 20px; border: 1px solid #1e2a4a; border-radius: 12px; background: #111a33; overflow: hidden; }
  .cp-section h2 { margin: 0; padding: 14px 18px; font: 600 15px 'Space Grotesk', sans-serif; border-bottom: 1px solid #1e2a4a; }
  .cp-lesson { display: flex; gap: 14px; align-items: center; padding: 12px 18px; border-bottom: 1px solid #16213f; }
  .cp-lesson:last-child { border-bottom: 0; }
  .cp-num { flex: none; width: 26px; height: 26px; border-radius: 50%; background: #1b2848; color: #73b6ff; font: 700 12px 'Space Mono', monospace; display: grid; place-items: center; }
</style>
<div class="cp-wrap">
  <div class="cp-inner">
    <a class="cp-back" href="{{ route('student.dashboard') }}">← Mission Control</a>
    <div class="cp-tag">{{ $course['tag'] ?? 'Course' }}</div>
    <h1 class="cp-title">{{ $course['title'] ?? 'Course' }}</h1>
    <p class="cp-blurb">{{ $course['blurb'] ?? '' }}</p>

    @foreach ($sections as $section)
      <section class="cp-section">
        <h2>{{ $section['title'] ?? '' }}</h2>
        @foreach ($section['lessons'] ?? [] as $lesson)
          @php $lessonNumber++; @endphp
          <div class="cp-lesson">
            <span class="cp-num">{{ $lessonNumber }}</span>
            <span>{{ $lesson['title'] ?? '' }}</span>
          </div>
        @endforeach
      </section>
    @endforeach
  </div>
</div>
