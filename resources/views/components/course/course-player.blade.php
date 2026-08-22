@php
  /**
   * Course player shell.
   *
   * Renders the two-panel learning interface:
   *   - Left  : outline / resources sidebar with collapsible sections
   *   - Right : lesson viewer with header, body, and previous/next nav
   *
   * Expected $course shape (built by PlanetController::normalizeCourse):
   *   [
   *     'id', 'title', 'tag', 'blurb',
   *     'sections' => [
   *       ['title', 'completed', 'total', 'lessons' => [
   *         ['id', 'title', 'state', 'section', 'resources' => []],
   *       ]],
   *     ],
   *     'lessons', 'current', 'completed', 'total', 'percent',
   *   ]
   */
  $course   = $course   ?? [];
  $sections = $course['sections'] ?? [];
  $total    = (int) ($course['total'] ?? 0);
  $completed = (int) ($course['completed'] ?? 0);
  $percent  = (int) ($course['percent'] ?? 0);
  $currentId = $course['current'] ?? null;

  // Flatten lessons for prev/next navigation.
  $flat = [];
  foreach ($sections as $section) {
      foreach ($section['lessons'] as $lesson) {
          $flat[] = $lesson;
      }
  }
  $currentIndex = 0;
  foreach ($flat as $i => $lesson) {
      if ($lesson['id'] === $currentId) {
          $currentIndex = $i;
          break;
      }
  }
  $prev = $flat[$currentIndex - 1] ?? null;
  $next = $flat[$currentIndex + 1] ?? null;
  $current = $flat[$currentIndex] ?? null;

  // Find which section the current lesson belongs to (for expand state).
  $currentSectionIndex = $current['section'] ?? 0;

  // Per-planet accent color so each planet feels native to its track.
  $accent = match ($course['id'] ?? null) {
      'programming' => '#73b6ff',
      'networking'  => '#5be1ff',
      'cybersecurity' => '#9b6bff',
      default       => '#73b6ff',
  };
@endphp

<div class="cp" style="--accent: {{ $accent }};">
  {{-- ===================== Sidebar (left) ===================== --}}
  <aside class="cp-side" aria-label="Course outline">
    <div class="cp-side-inner">
      <a class="cp-back" href="{{ route('student.dashboard') }}">← Mission Control</a>

      <div class="cp-course-card">
        <span class="cp-course-tag">{{ $course['tag'] ?? '' }}</span>
        <h2 class="cp-course-title">{{ $course['title'] ?? '' }}</h2>
        <p class="cp-course-blurb">{{ $course['blurb'] ?? '' }}</p>
      </div>

      {{-- Tabs --}}
      <div class="cp-tabs" role="tablist">
        <button type="button"
                class="cp-tab is-active"
                role="tab"
                aria-selected="true"
                data-tab="outline">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M4 6h16M4 12h10M4 18h16"/><circle cx="20" cy="12" r="1.2" fill="currentColor" stroke="none"/>
          </svg>
          Course Outline
        </button>
        <button type="button"
                class="cp-tab"
                role="tab"
                aria-selected="false"
                data-tab="resources">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M4 4h12l4 4v12a2 2 0 0 1-2 2H4z"/><path d="M14 4v6h6"/>
          </svg>
          Resources
        </button>
      </div>

      {{-- Outline panel --}}
      <div class="cp-panel is-active" data-panel="outline">
        <label class="cp-search">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
          </svg>
          <input type="search"
                 placeholder="Search course outline"
                 data-cp-search
                 autocomplete="off" />
        </label>

        <ul class="cp-sections" role="list">
          @foreach ($sections as $sectionIndex => $section)
            @php $expanded = $sectionIndex === $currentSectionIndex; @endphp
            <li class="cp-section {{ $expanded ? 'is-open' : '' }}"
                data-section-index="{{ $sectionIndex }}">
              <button type="button"
                      class="cp-section-head"
                      aria-expanded="{{ $expanded ? 'true' : 'false' }}">
                <span class="cp-section-caret" aria-hidden="true">▾</span>
                <span class="cp-section-title">{{ $section['title'] }}</span>
                <span class="cp-section-count">{{ $section['completed'] }} / {{ $section['total'] }}</span>
              </button>

              <ul class="cp-lessons" role="list">
                @foreach ($section['lessons'] as $lesson)
                  @php
                    $isCurrent = $lesson['id'] === $currentId;
                    $isCompleted = $lesson['state'] === 'completed';
                    $marker = match (true) {
                      $isCompleted => 'done',
                      $isCurrent   => 'current',
                      default      => 'todo',
                    };
                  @endphp
                  <li class="cp-lesson cp-lesson--{{ $marker }}"
                      data-lesson-id="{{ $lesson['id'] }}"
                      data-section-index="{{ $sectionIndex }}">
                    <span class="cp-lesson-marker" aria-hidden="true">
                      @if ($marker === 'done')
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 12 10 18 20 6"/></svg>
                      @elseif ($marker === 'current')
                        <span class="cp-dot"></span>
                      @else
                        <span class="cp-ring"></span>
                      @endif
                    </span>
                    <span class="cp-lesson-title">{{ $lesson['title'] }}</span>
                  </li>
                @endforeach
              </ul>
            </li>
          @endforeach
        </ul>

        <div class="cp-progress">
          <div class="cp-progress-head">
            <span>Module Completion</span>
            <span class="cp-progress-count">{{ $completed }} / {{ $total }}</span>
          </div>
          <div class="cp-progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $percent }}">
            <div class="cp-progress-fill" style="width: {{ $percent }}%;"></div>
          </div>
        </div>
      </div>

      {{-- Resources panel --}}
      <div class="cp-panel" data-panel="resources" hidden>
        <div class="cp-resources-empty">
          <div class="cp-resources-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 4h12l4 4v12a2 2 0 0 1-2 2H4z"/><path d="M14 4v6h6"/><path d="M8 14h8M8 18h6"/>
            </svg>
          </div>
          <h3>Resources coming soon</h3>
          <p>Handouts, references, and downloads for this module will land here once the lesson content is authored.</p>
        </div>
      </div>
    </div>
  </aside>

  {{-- ===================== Lesson viewer (right) ===================== --}}
  <section class="cp-main" aria-label="Lesson content">
    <header class="cp-lesson-head">
      <div class="cp-lesson-meta">
        <span class="cp-lesson-eyebrow">{{ $course['title'] ?? '' }}</span>
        <h1 class="cp-lesson-h1">{{ $current['title'] ?? 'Lesson' }}</h1>
      </div>
      <div class="cp-lesson-actions">
        <button type="button" class="cp-iconbtn" title="Bookmark lesson" aria-label="Bookmark lesson">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-6-4-6 4z"/></svg>
        </button>
        <button type="button" class="cp-iconbtn" title="Lesson notes" aria-label="Lesson notes">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h14v16l-7-4-7 4z"/></svg>
        </button>
        <button type="button" class="cp-iconbtn" title="Print lesson" aria-label="Print lesson">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 9V3h10v6"/><rect x="3" y="9" width="18" height="9" rx="1"/><path d="M7 18h10v3H7z"/></svg>
        </button>
      </div>
    </header>

    <article class="cp-lesson-body">
      <div class="cp-empty">
        <div class="cp-empty-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 2"/>
          </svg>
        </div>
        <h2>This lesson is still being written</h2>
        <p>The course player is ready — the actual lesson content will land here as it gets authored. Use the outline on the left to jump between lessons.</p>

        <div class="cp-empty-meta">
          <span class="cp-chip">Section {{ $currentSectionIndex + 1 }}</span>
          <span class="cp-chip">Lesson {{ $currentIndex + 1 }} of {{ $total }}</span>
          <span class="cp-chip cp-chip--muted">Estimated 10–15 min</span>
        </div>
      </div>
    </article>

    <nav class="cp-lesson-nav" aria-label="Lesson navigation">
      <button type="button"
              class="cp-navbtn cp-navbtn--prev"
              @disabled(is_null($prev))>
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
        <span>
          <span class="cp-navbtn-label">Previous</span>
          <span class="cp-navbtn-title">{{ $prev['title'] ?? '—' }}</span>
        </span>
      </button>

      <button type="button"
              class="cp-navbtn cp-navbtn--next"
              @disabled(is_null($next))>
        <span>
          <span class="cp-navbtn-label">Next</span>
          <span class="cp-navbtn-title">{{ $next['title'] ?? '—' }}</span>
        </span>
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
      </button>
    </nav>
  </section>
</div>

<style>
  /* ============== Course Player shell ============== */
  .cp {
    display: grid;
    grid-template-columns: minmax(300px, 360px) 1fr;
    gap: 24px;
    max-width: 1280px;
    margin: 0 auto;
    padding: 28px clamp(16px, 4vw, 40px) 64px;
    position: relative;
    z-index: 2;
  }

  /* Sidebar ============== */
  .cp-side { position: sticky; top: 90px; align-self: start; max-height: calc(100vh - 110px); }
  .cp-side-inner {
    background: var(--glass);
    border: 1px solid var(--glass-border);
    border-radius: 18px;
    padding: 18px;
    backdrop-filter: blur(12px);
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-height: calc(100vh - 110px);
    overflow-y: auto;
  }
  .cp-back {
    display: inline-flex; align-items: center; gap: 6px;
    color: var(--muted); font-size: .82rem;
    text-decoration: none;
  }
  .cp-back:hover { color: var(--text); }

  .cp-course-card {
    border: 1px solid var(--glass-border);
    border-radius: 14px;
    padding: 14px 14px 12px;
    background: rgba(255,255,255,0.02);
  }
  .cp-course-tag {
    display: inline-block;
    font-family: 'Space Mono', monospace;
    font-size: .68rem; letter-spacing: .14em; text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 8px;
  }
  .cp-course-title {
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700; font-size: 1.05rem; letter-spacing: -.01em;
    color: var(--text);
  }
  .cp-course-blurb {
    color: var(--muted); font-size: .82rem; margin-top: 6px;
    line-height: 1.5;
  }

  /* Tabs */
  .cp-tabs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: 4px;
    gap: 4px;
  }
  .cp-tab {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    background: transparent;
    color: var(--muted);
    border: 0;
    border-radius: 9px;
    padding: 9px 10px;
    font: 500 .82rem/1 'Inter', sans-serif;
    cursor: pointer;
    transition: background .2s, color .2s;
  }
  .cp-tab:hover { color: var(--text); }
  .cp-tab.is-active {
    background: linear-gradient(135deg, rgba(115,182,255,.18), rgba(115,182,255,.06));
    color: var(--text);
    box-shadow: inset 0 0 0 1px var(--accent);
  }
  .cp-tab.is-active svg { color: var(--accent); }

  /* Panels */
  .cp-panel { display: none; flex-direction: column; gap: 14px; }
  .cp-panel.is-active { display: flex; }

  /* Search */
  .cp-search {
    display: flex; align-items: center; gap: 8px;
    padding: 9px 12px;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--glass-border);
    border-radius: 10px;
    color: var(--muted);
  }
  .cp-search:focus-within { border-color: var(--accent); color: var(--text); }
  .cp-search input {
    flex: 1; min-width: 0;
    background: transparent; border: 0; outline: none;
    color: var(--text); font: 400 .88rem 'Inter', sans-serif;
  }
  .cp-search input::placeholder { color: var(--muted); }

  /* Sections / lessons */
  .cp-sections, .cp-lessons { list-style: none; padding: 0; margin: 0; }
  .cp-section { display: flex; flex-direction: column; gap: 4px; }
  .cp-section + .cp-section { margin-top: 4px; }
  .cp-section-head {
    display: flex; align-items: center; gap: 8px;
    width: 100%;
    background: transparent; border: 0;
    color: var(--text);
    font: 600 .82rem 'Inter', sans-serif;
    padding: 9px 6px;
    border-radius: 8px;
    cursor: pointer;
    text-align: left;
  }
  .cp-section-head:hover { background: rgba(255,255,255,0.04); }
  .cp-section-caret {
    display: inline-block;
    width: 14px;
    color: var(--muted);
    transition: transform .2s;
  }
  .cp-section:not(.is-open) .cp-section-caret { transform: rotate(-90deg); }
  .cp-section-title { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .cp-section-count {
    color: var(--muted);
    font-family: 'Space Mono', monospace;
    font-size: .7rem;
    letter-spacing: .04em;
  }

  .cp-lessons { display: none; padding: 2px 0 6px 22px; }
  .cp-section.is-open .cp-lessons { display: flex; flex-direction: column; gap: 2px; }

  .cp-lesson {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 10px;
    border-radius: 8px;
    cursor: pointer;
    color: var(--muted);
    font-size: .85rem;
    transition: background .15s, color .15s;
    border: 1px solid transparent;
  }
  .cp-lesson:hover { background: rgba(255,255,255,0.04); color: var(--text); }

  .cp-lesson-marker {
    display: inline-grid; place-items: center;
    width: 18px; height: 18px;
    border-radius: 50%;
    color: var(--muted);
    flex-shrink: 0;
  }
  .cp-lesson--done .cp-lesson-marker { color: var(--accent); }
  .cp-lesson--current {
    background: rgba(115,182,255,0.08);
    color: var(--text);
    border-color: var(--accent);
  }
  .cp-lesson--current .cp-lesson-marker { color: var(--accent); }
  .cp-lesson--current .cp-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--accent);
    box-shadow: 0 0 0 4px rgba(115,182,255,0.18);
  }
  .cp-lesson--todo .cp-ring {
    width: 10px; height: 10px; border-radius: 50%;
    border: 1.5px solid rgba(150,170,255,0.4);
    background: transparent;
  }

  .cp-lesson-title {
    flex: 1; min-width: 0;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  }

  /* Progress (sidebar) */
  .cp-progress {
    margin-top: auto;
    border-top: 1px solid var(--glass-border);
    padding-top: 12px;
    display: flex; flex-direction: column; gap: 8px;
  }
  .cp-progress-head {
    display: flex; justify-content: space-between;
    font-size: .75rem;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .12em;
    font-family: 'Space Mono', monospace;
  }
  .cp-progress-count { color: var(--text); }
  .cp-progress-track {
    height: 6px; border-radius: 999px;
    background: rgba(255,255,255,0.06);
    overflow: hidden;
  }
  .cp-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--accent), color-mix(in oklab, var(--accent) 60%, white));
    transition: width .3s ease;
  }

  /* Resources empty state */
  .cp-resources-empty {
    text-align: center;
    padding: 22px 12px;
    color: var(--muted);
  }
  .cp-resources-icon {
    width: 56px; height: 56px;
    margin: 0 auto 10px;
    border-radius: 14px;
    display: grid; place-items: center;
    background: rgba(115,182,255,0.08);
    color: var(--accent);
  }
  .cp-resources-empty h3 {
    font-family: 'Space Grotesk', sans-serif;
    color: var(--text);
    font-size: 1rem;
    margin-bottom: 4px;
  }
  .cp-resources-empty p { font-size: .85rem; }

  /* Lesson viewer ============== */
  .cp-main {
    display: flex; flex-direction: column; gap: 22px;
    min-width: 0;
  }
  .cp-lesson-head {
    display: flex; align-items: flex-start; gap: 16px;
    justify-content: space-between;
    padding-bottom: 18px;
    border-bottom: 1px solid var(--glass-border);
  }
  .cp-lesson-eyebrow {
    font-family: 'Space Mono', monospace;
    font-size: .72rem; letter-spacing: .14em; text-transform: uppercase;
    color: var(--accent);
  }
  .cp-lesson-h1 {
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 700;
    font-size: clamp(1.6rem, 3vw, 2.2rem);
    letter-spacing: -.02em;
    margin-top: 6px;
  }
  .cp-lesson-actions { display: flex; gap: 6px; }
  .cp-iconbtn {
    background: var(--glass);
    border: 1px solid var(--glass-border);
    color: var(--muted);
    width: 36px; height: 36px;
    border-radius: 10px;
    display: grid; place-items: center;
    cursor: pointer;
    transition: color .2s, border-color .2s, background .2s;
  }
  .cp-iconbtn:hover { color: var(--text); border-color: var(--accent); background: rgba(115,182,255,0.06); }

  .cp-lesson-body {
    background: var(--glass);
    border: 1px solid var(--glass-border);
    border-radius: 18px;
    padding: clamp(28px, 4vw, 48px);
    min-height: 360px;
    backdrop-filter: blur(10px);
  }

  .cp-empty {
    display: flex; flex-direction: column; align-items: center; text-align: center;
    gap: 14px;
    max-width: 560px;
    margin: 24px auto;
  }
  .cp-empty-icon {
    width: 72px; height: 72px;
    border-radius: 18px;
    background: linear-gradient(135deg, rgba(115,182,255,0.16), rgba(115,182,255,0.04));
    border: 1px solid var(--glass-border);
    color: var(--accent);
    display: grid; place-items: center;
    margin-bottom: 6px;
  }
  .cp-empty h2 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--text);
  }
  .cp-empty p {
    color: var(--muted);
    font-size: .95rem;
    line-height: 1.6;
  }
  .cp-empty-meta { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-top: 6px; }
  .cp-chip {
    display: inline-flex; align-items: center;
    padding: 6px 12px;
    border-radius: 999px;
    font: 500 .75rem 'Inter', sans-serif;
    background: rgba(115,182,255,0.10);
    color: var(--accent);
    border: 1px solid color-mix(in oklab, var(--accent) 50%, transparent);
  }
  .cp-chip--muted {
    background: rgba(255,255,255,0.04);
    color: var(--muted);
    border-color: var(--glass-border);
  }

  /* Prev / Next nav */
  .cp-lesson-nav {
    display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
  }
  .cp-navbtn {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 16px;
    background: var(--glass);
    border: 1px solid var(--glass-border);
    border-radius: 14px;
    color: var(--text);
    cursor: pointer;
    text-align: left;
    font: inherit;
    transition: border-color .2s, background .2s, transform .2s;
    min-width: 0;
  }
  .cp-navbtn:hover:not(:disabled) { border-color: var(--accent); background: rgba(115,182,255,0.06); }
  .cp-navbtn:disabled { opacity: .4; cursor: not-allowed; }
  .cp-navbtn--next { justify-content: flex-end; text-align: right; }
  .cp-navbtn > span { display: flex; flex-direction: column; min-width: 0; }
  .cp-navbtn-label {
    font-family: 'Space Mono', monospace;
    font-size: .68rem; letter-spacing: .14em; text-transform: uppercase;
    color: var(--muted);
  }
  .cp-navbtn-title {
    font-size: .92rem; color: var(--text);
    margin-top: 2px;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  }

  /* Responsive */
  @media (max-width: 960px) {
    .cp { grid-template-columns: 1fr; }
    .cp-side { position: static; max-height: none; }
    .cp-side-inner { max-height: none; }
  }
  @media (max-width: 540px) {
    .cp-lesson-nav { grid-template-columns: 1fr; }
    .cp-navbtn--next { justify-content: flex-start; text-align: left; }
  }
</style>

<script>
  (function () {
    const root = document.currentScript ? document.currentScript.previousElementSibling : null;
    const scope = (root && root.classList && root.classList.contains('cp'))
      ? root
      : document.querySelector('.cp');
    if (!scope) return;

    // Tabs
    const tabs   = scope.querySelectorAll('.cp-tab');
    const panels = scope.querySelectorAll('.cp-panel');
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const target = tab.dataset.tab;
        tabs.forEach(t => {
          const active = t === tab;
          t.classList.toggle('is-active', active);
          t.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        panels.forEach(p => {
          const active = p.dataset.panel === target;
          p.classList.toggle('is-active', active);
          if (active) { p.removeAttribute('hidden'); } else { p.setAttribute('hidden', ''); }
        });
      });
    });

    // Section collapse/expand
    scope.querySelectorAll('.cp-section-head').forEach(head => {
      head.addEventListener('click', () => {
        const section = head.closest('.cp-section');
        const open = section.classList.toggle('is-open');
        head.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    });

    // Lesson selection (placeholder - just toggles active state)
    scope.querySelectorAll('.cp-lesson').forEach(lesson => {
      lesson.addEventListener('click', () => {
        scope.querySelectorAll('.cp-lesson--current').forEach(l => {
          if (l !== lesson) l.classList.remove('cp-lesson--current');
        });
        lesson.classList.add('cp-lesson--current');
        // Open the parent section so the lesson is visible.
        const section = lesson.closest('.cp-section');
        if (section && !section.classList.contains('is-open')) {
          section.classList.add('is-open');
          const head = section.querySelector('.cp-section-head');
          if (head) head.setAttribute('aria-expanded', 'true');
        }
      });
    });

    // Outline search (filters lessons by title, hides empty sections).
    const searchInput = scope.querySelector('[data-cp-search]');
    if (searchInput) {
      searchInput.addEventListener('input', () => {
        const term = searchInput.value.trim().toLowerCase();
        scope.querySelectorAll('.cp-section').forEach(section => {
          let anyVisible = false;
          section.querySelectorAll('.cp-lesson').forEach(lesson => {
            const title = (lesson.querySelector('.cp-lesson-title')?.textContent || '').toLowerCase();
            const visible = term === '' || title.includes(term);
            lesson.style.display = visible ? '' : 'none';
            if (visible) anyVisible = true;
          });
          section.style.display = anyVisible ? '' : 'none';
          if (term !== '' && anyVisible) section.classList.add('is-open');
        });
      });
    }
  })();
</script>
