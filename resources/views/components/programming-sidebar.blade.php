<aside class="programming-sidebar">

    {{-- TABS --}}

    <div class="sidebar-tabs">

        <button class="sidebar-tab active" type="button" data-sidebar-tab="outline" aria-selected="true">

            <svg class="tab-icon" viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2.5"/><path d="M8 3v18"/><rect x="11" y="7" width="6" height="4" rx="1"/><path d="M11 15h6"/></svg>

            <span>Course Outline</span>

        </button>

        <button class="sidebar-tab" type="button" data-sidebar-tab="resources" aria-selected="false">

            <svg class="tab-icon" viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h6"/></svg>

            <span>Resources</span>

        </button>

    </div>

    <div class="sidebar-panel active" data-sidebar-panel="outline">

    {{-- SEARCH --}}

    <div class="sidebar-search">

        <div class="search-wrapper">

            <span class="search-icon">⌕</span>

            <input
                type="text"
                placeholder="Search course content..."
            >

        </div>

    </div>

    {{-- COURSE OUTLINE --}}

    <div class="course-outline">

        {{-- One block per module, generated from config/course-structure.php so every planet's
             course (Python, Networking, ...) gets the same sidebar and it can never drift from
             the server-side lesson order. --}}
        @php
            $outlineSlug = $slug ?? 'programming';
            $outlineModules = config("course-structure.{$outlineSlug}.modules", []);
        @endphp

        @foreach ($outlineModules as $moduleKey => $moduleDef)
            @php
                $mId = strtolower($moduleKey);
                $mNum = ltrim($moduleKey, 'Mm');
            @endphp

        <div class="course-module">

            <div class="module-header">

                <div class="module-info">

                    <div class="module-title">
                        {{ $moduleDef['title'] }}
                    </div>

                    <div class="module-progress" data-module-progress="{{ $mId }}">

                        <div class="progress-track">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>

                        <span>0%</span>

                    </div>

                </div>

                <button
                    type="button"
                    class="module-toggle"
                    onclick="toggleModule('{{ $mId }}', this)"
                    aria-expanded="false"
                >
                    <span>⌄</span>
                </button>

            </div>

            {{-- LESSONS --}}

            <div class="module-lessons" id="{{ $mId }}">

                @foreach (array_keys($moduleDef['lessons']) as $i => $lessonKey)
                    @php $lId = str_replace('-', '', strtolower($lessonKey)); @endphp

                <a href="/student/planet/{{ $outlineSlug }}/{{ $mId }}/{{ $lId }}" class="lesson-item" data-module="{{ $mId }}" data-lesson="{{ $lId }}" data-order="{{ $i + 1 }}">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">{{ $mNum }}.{{ $i + 1 }}</span>
                        <span>{{ $moduleDef['lessons'][$lessonKey]['title'] }}</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                @endforeach

            </div>

        </div>

        @endforeach

    </div>{{-- /course-outline --}}

    </div>{{-- /outline panel --}}


    {{-- RESOURCES PANEL
         Read-only for students and faculty. Only admins will manage these
         (future admin panel). Pass $resources as [['title'=>..., 'url'=>..., 'type'=>...], ...]. --}}

    @php $courseResources = $resources ?? config('course-structure.'.($slug ?? 'programming').'.resources', []); @endphp

    <div class="sidebar-panel" data-sidebar-panel="resources">

        <div class="sidebar-search resources-search">
            <div class="search-wrapper">
                <input type="text" id="resourceSearch" placeholder="Search Resources" autocomplete="off">
                <span class="search-icon search-icon-right">⌕</span>
            </div>
        </div>

        <div class="resources-body">

            <div class="resources-header">
                <span>Course Resources</span>
                <button type="button" class="resources-toggle open" id="resourcesToggle" aria-expanded="true" aria-label="Toggle course resources">
                    <span>⌃</span>
                </button>
            </div>

            <div class="resources-content" id="resourcesContent">

                @if (count($courseResources))
                    <ul class="resource-list" id="resourceList">
                        @foreach ($courseResources as $resource)
                            <li class="resource-item" data-title="{{ strtolower($resource['title']) }}">
                                <a href="{{ $resource['url'] }}" target="_blank" rel="noopener">
                                    <span class="resource-type">{{ strtoupper($resource['type'] ?? 'file') }}</span>
                                    <span class="resource-text">
                                        <span>{{ $resource['title'] }}</span>
                                        @if (! empty($resource['source']))
                                            <small class="resource-source">{{ $resource['source'] }}</small>
                                        @endif
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="resources-empty" id="resourcesEmpty" @if (count($courseResources)) hidden @endif>
                    <div class="resources-empty-art" aria-hidden="true">
                        <svg viewBox="0 0 200 200" width="200" height="200">
                            <circle cx="100" cy="100" r="100" fill="#ececec"/>
                            <rect x="82" y="46" width="66" height="88" rx="6" fill="#dcdcdc"/>
                            <rect x="128" y="66" width="12" height="4" rx="2" fill="#2f7de1"/>
                            <rect x="128" y="78" width="12" height="4" rx="2" fill="#2f7de1"/>
                            <rect x="52" y="70" width="70" height="92" rx="6" fill="#ffffff"/>
                            <rect x="64" y="92" width="46" height="5" rx="2.5" fill="#2f7de1"/>
                            <rect x="64" y="104" width="46" height="5" rx="2.5" fill="#2f7de1"/>
                            <rect x="64" y="116" width="46" height="5" rx="2.5" fill="#2f7de1"/>
                            <rect x="64" y="128" width="26" height="5" rx="2.5" fill="#2f7de1"/>
                        </svg>
                    </div>
                    <p id="resourcesEmptyText">No Resources Available</p>
                </div>

            </div>

        </div>

    </div>

</aside>


<style>

.programming-sidebar {
    font-family: 'Inter', system-ui, sans-serif;
    width: 30vw;
    min-width: 380px;
    max-width: 520px;
    height: 100vh;
    background: #f9f9f9;
    border-right: 1px solid rgba(0,0,0,.09);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}


/* =========================
   TABS
========================= */

.sidebar-tabs {
    display: flex;
    flex-shrink: 0;
    border-bottom: 1px solid rgba(0,0,0,.09);
}

.sidebar-tab {
    font-family: 'Space Grotesk', sans-serif;
    flex: 1;
    height: 90px;
    border: none;
    background: #ffffff;
    font-size: 20px;
    font-weight: 600;
    color: #0d0d0d;
    cursor: pointer;
    border-bottom: 4px solid transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    transition: all 0.2s ease;
}

.sidebar-tab:hover {
    background: #f7f7f7;
}

.sidebar-tab.active .tab-icon { color: #2f7de1; }

.sidebar-tab.active {
    color: #2f7de1;
    border-bottom-color: #2f7de1;
}

.tab-icon {
    flex-shrink: 0;
    color: #676767;
    font-size: 24px;
}


/* =========================
   SEARCH
========================= */

.sidebar-search {
    padding: 24px 28px;
    flex-shrink: 0;
}

.search-wrapper {
    position: relative;
}

.search-wrapper input {
    width: 100%;
    height: 56px;
    box-sizing: border-box;
    padding: 0 18px 0 48px;
    border: 2px solid rgba(0,0,0,.09);
    border-radius: 12px;
    font-size: 18px;
    outline: none;
    transition: border-color 0.2s ease;
}

.search-wrapper input:focus {
    border-color: #2f7de1;
}

.search-icon {
    position: absolute;
    left: 17px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 28px;
    color: #888888;
    pointer-events: none;
}


/* =========================
   COURSE OUTLINE
========================= */

.course-outline {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
}


/* =========================
   MODULE
========================= */

.course-module {
    border-top: 1px solid rgba(0,0,0,.09);
}


/* =========================
   MODULE HEADER
========================= */

.module-header {
    position: relative;
    min-height: 145px;
    padding: 22px 78px 22px 28px;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    background: #ffffff;
}


/* =========================
   MODULE INFO
========================= */

.module-info {
    width: 100%;
}

.module-title {
    font-family: 'Space Grotesk', sans-serif;
    letter-spacing: -0.02em;
    font-size: 22px;
    line-height: 1.35;
    font-weight: 700;
    color: #0d0d0d;
    margin-bottom: 18px;
}

.module-progress {
    display: flex;
    align-items: center;
    gap: 12px;
}

.progress-track {
    flex: 1;
    height: 8px;
    background: #f3f3f3;
    border-radius: 20px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #2f7de1;
    border-radius: 20px;
    transition: width 0.3s ease;
}

.module-progress span {
    min-width: 40px;
    font-size: 15px;
    color: #555555;
}


/* =========================
   DROPDOWN BUTTON
========================= */

.module-toggle {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    width: 48px;
    height: 48px;
    border: none;
    border-radius: 8px;
    background: #f3f3f3;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition:
        background 0.2s ease,
        color 0.2s ease;
}

.module-toggle span {
    font-size: 26px;
    transition: transform 0.25s ease;
}

.module-toggle:hover {
    background: #2f7de1;
    color: #ffffff;
}

.module-toggle.open {
    background: #2f7de1;
    color: #ffffff;
}

.module-toggle.open span {
    transform: rotate(180deg);
}


/* =========================
   LESSON CONTAINER
========================= */

.module-lessons {
    display: none;
    background: #f6f9fe;
    padding: 8px 18px 16px 28px;
}

.module-lessons.open {
    display: block;
}


/* =========================
   LESSON
========================= */

.lesson-item {
    display: flex;
    min-height: 64px;
    position: relative;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.2s ease;

    /* Preserve original appearance after converting div to a link */
    text-decoration: none;
    color: inherit;
}

.lesson-item:hover {
    background: #eaf2fd;
}

.lesson-item.active {
    background: #eaf2fd;
}

.lesson-item.active .lesson-dot {
    background: #2f7de1;
    border-color: #2f7de1;
}

/* Completed lessons: solid green filled dot */
.lesson-item.completed .lesson-dot {
    background: #2f7de1;
    border-color: #2f7de1;
}

/* Locked lessons: dim + not-allowed cursor */
.lesson-item.locked {
    opacity: 0.55;
    cursor: not-allowed;
    pointer-events: none;  /* ← added: belt-and-suspenders block */
}

.lesson-item.locked:hover {
    background: transparent;
}

.lesson-lock {
    display: none;
    margin-left: auto;
    font-size: 15px;
    flex-shrink: 0;
}

.lesson-item.locked .lesson-lock {
    display: inline-block;
}


/* =========================
   CONNECTOR
========================= */

.lesson-connector {
    width: 48px;
    position: relative;
    display: flex;
    justify-content: center;
    flex-shrink: 0;
}

.lesson-connector::after {
    content: "";
    position: absolute;
    top: 30px;
    bottom: -30px;
    left: 23px;
    border-left: 2px dashed #b7b7b7;
}

.lesson-item:last-child .lesson-connector::after {
    display: none;
}


/* =========================
   LESSON DOT
========================= */

.lesson-dot {
    width: 28px;
    height: 28px;
    margin-top: 17px;
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid #b9dcb8;
    position: relative;
    z-index: 2;
    transition: background 0.2s ease, border-color 0.2s ease;
}


/* =========================
   LESSON TEXT
========================= */

.lesson-name {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 14px 8px;
    font-size: 18px;
    line-height: 1.35;
    color: #0d0d0d;
}

.lesson-number {
    font-weight: 600;
    color: #2f7de1;
}


/* =========================
   SCROLLBAR
========================= */

.course-outline::-webkit-scrollbar {
    width: 8px;
}

.course-outline::-webkit-scrollbar-track {
    background: #f4f4f4;
}

.course-outline::-webkit-scrollbar-thumb {
    background: #cccccc;
    border-radius: 10px;
}

.course-outline::-webkit-scrollbar-thumb:hover {
    background: #aaaaaa;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 900px) {

    .programming-sidebar {
        width: 38vw;
        min-width: 320px;
    }

    .module-title {
        font-size: 20px;
    }

    .lesson-name {
        font-size: 16px;
    }

}

/* =========================
   SIDEBAR PANELS + RESOURCES
========================= */

.sidebar-panel {
    display: none;
    flex: 1;
    min-height: 0;
    flex-direction: column;
}

.sidebar-panel.active {
    display: flex;
}

.resources-search .search-wrapper input {
    padding: 0 52px 0 18px;
}

.search-icon-right {
    left: auto;
    right: 17px;
}

.resources-body {
    flex: 1;
    overflow-y: auto;
    border-top: 1px solid rgba(0,0,0,.09);
}

.resources-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px 28px;
    background: #ffffff;
    border-bottom: 1px solid rgba(0,0,0,.09);
    font-family: 'Space Grotesk', sans-serif;
    font-size: 22px;
    font-weight: 700;
    color: #0d0d0d;
}

.resources-toggle {
    width: 48px;
    height: 48px;
    border: none;
    border-radius: 8px;
    background: #2f7de1;
    color: #ffffff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.resources-toggle span {
    font-size: 26px;
    transition: transform 0.25s ease;
}

.resources-toggle:not(.open) span {
    transform: rotate(180deg);
}

.resources-content[hidden] { display: none; }

.resources-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 48px 28px;
    background: #ffffff;
    color: #0d0d0d;
    font-size: 22px;
    text-align: center;
}

.resources-empty[hidden] { display: none; }

.resource-list {
    list-style: none;
    margin: 0;
    padding: 8px 28px 16px;
    background: #ffffff;
}

.resource-item a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 0;
    font-size: 18px;
    color: #0d0d0d;
    text-decoration: none;
    border-bottom: 1px solid rgba(0,0,0,.06);
}

.resource-item a:hover { color: #2f7de1; }

.resource-text {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.resource-source {
    font-size: 13px;
    line-height: 1.4;
    color: #676767;
}

.resource-type {
    font-size: 12px;
    font-weight: 700;
    color: #2f7de1;
    background: #eaf2fd;
    border-radius: 6px;
    padding: 4px 8px;
}


</style>


<script>

function toggleModule(moduleId, button) {

    const lessons = document.getElementById(moduleId);
    const isOpen = lessons.classList.contains('open');

    if (isOpen) {
        lessons.classList.remove('open');
        button.classList.remove('open');
        button.setAttribute('aria-expanded', 'false');
    } else {
        lessons.classList.add('open');
        button.classList.add('open');
        button.setAttribute('aria-expanded', 'true');
    }

}

/*
|--------------------------------------------------------------------------
| LESSON PROGRESS SYSTEM
|--------------------------------------------------------------------------
|
| Reads the student's completed lessons from the database (rendered by the
| server into SERVER_COMPLETED below — it follows the account across browsers
| and survives clearing localStorage), then derives:
|   - which lessons are unlocked (a lesson is unlocked when the lesson right
|     before it, course-wide, is completed; module 1 lesson 1 is always unlocked)
|   - module completion percentages
|
| This is display only. The real lock is enforced on the server by the
| EnsureLessonUnlocked middleware, so editing the DOM/classes here cannot
| open a locked lesson.
|
| Exposed globally as window.TechLab.markLessonComplete(module, lesson) so
| the Python editor's "Submit"/"Next" flow can call it directly without the
| editor needing to know anything about the sidebar's storage format.
|
| NEW ─ after marking complete, the system also:
|   1. auto-navigates to the next lesson via TechLab.loadLesson (or href fallback)
|   2. updates the active-lesson highlight in the sidebar
*/

(function () {
    // Completed lessons as "m1/lesson01" keys — the database is the source of truth.
    const SERVER_COMPLETED = @json(auth()->check() ? \App\Services\CourseProgressService::completedKeys(auth()->user(), $slug ?? 'programming') : []);

    // ─── Build flat ordered list of all lessons from the DOM ───────────────
    function buildLessonOrder() {
        return Array.from(document.querySelectorAll('.lesson-item')).map(function (el) {
            return { module: el.dataset.module, lesson: el.dataset.lesson, el: el };
        });
    }

    function keyFor(moduleId, lessonId) {
        return moduleId + '/' + lessonId;
    }

    // ─── State ──────────────────────────────────────────────────────────────
    let completed = SERVER_COMPLETED.slice();   // ["m1/lesson01", …]
    const order   = buildLessonOrder();

    function isCompleted(moduleId, lessonId) {
        return completed.indexOf(keyFor(moduleId, lessonId)) !== -1;
    }

    // A lesson is unlocked when it is the very first one, or the lesson
    // immediately before it (course-wide) has been completed.
    function isUnlocked(index) {
        if (index === 0) return true;
        const prev = order[index - 1];
        return isCompleted(prev.module, prev.lesson);
    }

    // ─── DOM render ─────────────────────────────────────────────────────────
    function renderLockState() {
        order.forEach(function (item, index) {
            const unlocked = isUnlocked(index);
            const done     = isCompleted(item.module, item.lesson);

            item.el.classList.toggle('locked',    !unlocked);
            item.el.classList.toggle('completed',  done);

            if (!unlocked) {
                item.el.setAttribute('aria-disabled', 'true');
            } else {
                item.el.removeAttribute('aria-disabled');
            }
        });
    }

    function renderModulePercentages() {
        const modules = {};

        order.forEach(function (item) {
            if (!modules[item.module]) modules[item.module] = { total: 0, done: 0 };
            modules[item.module].total++;
            if (isCompleted(item.module, item.lesson)) modules[item.module].done++;
        });

        Object.keys(modules).forEach(function (moduleId) {
            const stats = modules[moduleId];
            const pct   = stats.total > 0 ? Math.round((stats.done / stats.total) * 100) : 0;

            const progressEl = document.querySelector('[data-module-progress="' + moduleId + '"]');
            if (!progressEl) return;

            const fill  = progressEl.querySelector('.progress-fill');
            const label = progressEl.querySelector('span');

            if (fill)  fill.style.width   = pct + '%';
            if (label) label.textContent   = pct;
        });
    }

    // Mark the sidebar item that matches the currently-open lesson as active.
    function renderActiveLesson(moduleId, lessonId) {
        order.forEach(function (item) {
            const isCurrent = item.module === moduleId && item.lesson === lessonId;
            item.el.classList.toggle('active', isCurrent);
        });
    }

    function refresh() {
        renderLockState();
        renderModulePercentages();
    }

    // ─── Next-lesson helper ─────────────────────────────────────────────────
    // Returns the {module, lesson, el} entry that comes immediately after the
    // supplied lesson, or null if it is the last lesson in the course.
    function getNextLesson(moduleId, lessonId) {
        const index = order.findIndex(function (item) {
            return item.module === moduleId && item.lesson === lessonId;
        });
        if (index === -1 || index === order.length - 1) return null;
        return order[index + 1];
    }

    // ─── Public: markLessonComplete ─────────────────────────────────────────
    //
    // Marks a lesson done in the sidebar's in-memory state.
    //
    //   window.TechLab.markLessonComplete('m1', 'lesson01');
    //
    // What it does:
    //   1. Records the completion in the sidebar's in-memory state.
    //   2. Re-renders lock states + progress bars immediately.
    //   3. Navigates to the next lesson automatically.
    //
    function markLessonComplete(moduleId, lessonId) {
        // 1 ─ Reflect it locally (the server has already recorded the completion)
        const key = keyFor(moduleId, lessonId);
        if (completed.indexOf(key) === -1) {
            completed.push(key);
        }

        // 2 ─ Re-render sidebar
        refresh();

        // 3 ─ Navigate to the next lesson
        const next = getNextLesson(moduleId, lessonId);
        if (!next) return; // last lesson — nothing to navigate to

        if (window.TechLab && typeof window.TechLab.loadLesson === 'function') {
            // The main panel (programming.blade.php) owns lesson loading.
            window.TechLab.loadLesson(next.module, next.lesson);

            // Auto-expand the next lesson's module in the sidebar if it is
            // in a different module than the one just completed.
            if (next.module !== moduleId) {
                const nextModuleLessons = document.getElementById(next.module);
                const nextModuleToggle  = nextModuleLessons
                    ? nextModuleLessons.previousElementSibling
                        && nextModuleLessons.closest('.course-module')
                           .querySelector('.module-toggle')
                    : null;

                if (nextModuleLessons && !nextModuleLessons.classList.contains('open')) {
                    nextModuleLessons.classList.add('open');
                    if (nextModuleToggle) {
                        nextModuleToggle.classList.add('open');
                        nextModuleToggle.setAttribute('aria-expanded', 'true');
                    }
                }
            }
        } else {
            // Fallback: full page navigation using the href already on the element.
            window.location.href = next.el.getAttribute('href');
        }
    }

    // ─── Public: getLessonStatus ────────────────────────────────────────────
    function getLessonStatus(moduleId, lessonId) {
        const index = order.findIndex(function (item) {
            return item.module === moduleId && item.lesson === lessonId;
        });
        if (index === -1) return { unlocked: false, completed: false };
        return { unlocked: isUnlocked(index), completed: isCompleted(moduleId, lessonId) };
    }

    // ─── Block locked lesson clicks (capture phase — runs first) ────────────
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.lesson-item');
        if (!link) return;
        if (link.classList.contains('locked')) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    }, true);

    // ─── Export public API ───────────────────────────────────────────────────
    window.TechLab = window.TechLab || {};
    window.TechLab.recordLessonComplete = function (moduleId, lessonId) {   // mark done + refresh, no auto-navigation
        const key = keyFor(moduleId, lessonId);
        if (completed.indexOf(key) === -1) completed.push(key);
        refresh();
    };
    window.TechLab.markLessonComplete  = markLessonComplete;
    window.TechLab.getLessonStatus     = getLessonStatus;
    window.TechLab.refreshProgress     = refresh;
    window.TechLab.setActiveLesson     = renderActiveLesson;  // ← new, call from programming.blade.php

    // ─── Initial render ──────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', refresh);
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        refresh();
    }

})();

/*
|--------------------------------------------------------------------------
| LESSON CLICK → TELL THE MAIN PANEL WHICH LESSON TO LOAD
|--------------------------------------------------------------------------
|
| Locked lessons are already blocked by the capture-phase listener above.
| This listener handles unlocked clicks and delegates to the main panel.
*/

document.addEventListener('click', function (e) {
    const link = e.target.closest('.lesson-item');
    if (!link) return;
    if (link.classList.contains('locked')) return;

    e.preventDefault();

    const moduleId = link.dataset.module;
    const lessonId = link.dataset.lesson;

    // Update the sidebar's own active highlight immediately.
    document.querySelectorAll('.lesson-item').forEach(function (el) {
        el.classList.remove('active');
    });
    link.classList.add('active');

    if (window.TechLab && typeof window.TechLab.loadLesson === 'function') {
        window.TechLab.loadLesson(moduleId, lessonId);
    } else {
        window.location.href = link.getAttribute('href');
    }
});

/*
|--------------------------------------------------------------------------
| SIDEBAR TABS + RESOURCES
|--------------------------------------------------------------------------
*/

(function () {
    const tabs   = document.querySelectorAll('[data-sidebar-tab]');
    const panels = document.querySelectorAll('[data-sidebar-panel]');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const target = tab.dataset.sidebarTab;
            tabs.forEach(function (t) {
                const on = t === tab;
                t.classList.toggle('active', on);
                t.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            panels.forEach(function (p) {
                p.classList.toggle('active', p.dataset.sidebarPanel === target);
            });
        });
    });

    const toggle  = document.getElementById('resourcesToggle');
    const content = document.getElementById('resourcesContent');
    if (toggle && content) {
        toggle.addEventListener('click', function () {
            const open = toggle.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            content.hidden = !open;
        });
    }

    const search = document.getElementById('resourceSearch');
    if (search) {
        search.addEventListener('input', function () {
            const q     = search.value.trim().toLowerCase();
            const items = document.querySelectorAll('#resourceList .resource-item');
            const empty = document.getElementById('resourcesEmpty');
            const text  = document.getElementById('resourcesEmptyText');
            let shown = 0;

            items.forEach(function (li) {
                const match = li.dataset.title.indexOf(q) !== -1;
                li.hidden = !match;
                if (match) shown++;
            });

            if (empty) {
                empty.hidden = shown > 0;
                if (text) text.textContent = items.length ? 'No matching resources' : 'No Resources Available';
            }
        });
    }
})();

</script>