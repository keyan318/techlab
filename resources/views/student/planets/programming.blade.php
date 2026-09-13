<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Programming Course</title>

    <style>

        :root {
            --green: #4CAF50;
            --light-green: #E8F5E9;
            --gray: #f0f0f0;
            --dark-gray: #333;
            --border-gray: #e0e0e0;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
        }

        body {
            font-family:
                -apple-system,
                BlinkMacSystemFont,
                'Segoe UI',
                Roboto,
                Oxygen,
                Ubuntu,
                Cantarell,
                'Open Sans',
                'Helvetica Neue',
                sans-serif;

            background-color: #f5f5f5;
            color: var(--dark-gray);
            overflow: hidden;
        }

        /* =========================================================
           MAIN CONTAINER
        ========================================================= */

        .container {
            display: flex;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        /* =========================================================
           MAIN AREA
        ========================================================= */

        .main-area {
            flex: 1;
            min-width: 0;

            height: 100vh;

            display: flex;
            flex-direction: column;

            background-color: var(--white);

            overflow: hidden;
        }

        /* =========================================================
           HEADER
        ========================================================= */

        .header {
            height: 72px;
            min-height: 72px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            padding: 0 28px;

            border-bottom: 1px solid var(--border-gray);

            background-color: var(--white);
        }

        .header-left {
            display: flex;
            align-items: center;
            min-width: 0;
        }

        .back-icon {
            width: 34px;
            height: 34px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-right: 14px;

            color: var(--green);

            font-size: 20px;

            cursor: pointer;

            border-radius: 6px;

            transition:
                background-color 0.2s ease,
                transform 0.2s ease;
        }

        .back-icon:hover {
            background-color: var(--light-green);
            transform: translateX(-2px);
        }

        .lesson-title {
            font-size: 20px;
            font-weight: 500;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* =========================================================
           HEADER ICONS
        ========================================================= */

        .header-right {
            display: flex;
            align-items: center;

            gap: 6px;

            margin-left: 20px;
        }

        .icon {
            width: 40px;
            height: 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            cursor: pointer;

            color: var(--dark-gray);

            border-radius: 8px;

            font-size: 19px;

            transition:
                background-color 0.2s ease,
                transform 0.2s ease;
        }

        .icon:hover {
            background-color: var(--gray);
            transform: translateY(-1px);
        }

        /* =========================================================
           LESSON CONTENT
        ========================================================= */

        .content {
            flex: 1;
            min-height: 0;

            padding: 42px 52px;

            overflow-y: auto;
            overflow-x: hidden;

            background-color: var(--white);
        }

        .lesson-content {
            width: 100%;
            max-width: 1100px;

            margin: 0 auto;

            min-height: 100%;

            transition: opacity 0.15s ease;
        }

        .lesson-heading {
            font-size: 32px;
            font-weight: 600;

            margin-bottom: 30px;
        }

        .secondary-heading {
            font-size: 22px;
            font-weight: 600;

            margin-top: 35px;
            margin-bottom: 14px;
        }

        .body-text {
            font-size: 16px;
            line-height: 1.7;

            color: #555;

            margin-bottom: 20px;
        }

        .code-block {
            background: #1e1e1e;
            color: #ffffff;

            padding: 20px;

            border-radius: 8px;

            overflow-x: auto;

            margin-bottom: 40px;

            line-height: 1.6;

            font-family:
                'SFMono-Regular',
                Consolas,
                'Liberation Mono',
                monospace;
        }

        .exercise-card {
            padding: 24px;

            margin-bottom: 25px;

            border: 1px solid var(--border-gray);

            border-radius: 10px;

            background: #fafafa;
        }

        .quiz-card {
            margin-bottom: 30px;

            padding: 24px;

            border: 1px solid var(--border-gray);

            border-radius: 10px;

            background: var(--white);
        }

        .quiz-question {
            font-weight: 600;
            margin-bottom: 15px;
        }

        .quiz-option {
            padding: 10px 0;
        }

        /* =========================================================
           FALLBACK
        ========================================================= */

        .empty-lesson {
            display: flex;

            align-items: center;
            justify-content: center;

            min-height: 60vh;

            color: #777;

            text-align: center;
        }

        /* =========================================================
           NAVIGATION
        ========================================================= */

        .nav-arrows {
            display: flex;

            justify-content: space-between;
            align-items: center;

            width: 100%;

            margin-top: 50px;

            padding-bottom: 30px;
        }

        .nav-arrow {
            width: 46px;
            height: 46px;

            display: flex;
            align-items: center;
            justify-content: center;

            background-color: var(--white);

            border: 1px solid var(--green);

            border-radius: 6px;

            color: var(--green);

            cursor: pointer;

            font-size: 22px;

            transition:
                background-color 0.2s ease,
                color 0.2s ease,
                transform 0.2s ease;
        }

        .nav-arrow:hover {
            background-color: var(--green);

            color: var(--white);

            transform: translateY(-2px);
        }

        /* =========================================================
           SCROLLBAR
        ========================================================= */

        .content::-webkit-scrollbar {
            width: 8px;
        }

        .content::-webkit-scrollbar-track {
            background: #f7f7f7;
        }

        .content::-webkit-scrollbar-thumb {
            background: #cfcfcf;
            border-radius: 10px;
        }

        .content::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1200px) {

            .content {
                padding: 35px 40px;
            }
        }

        @media (max-width: 900px) {

            .content {
                padding: 30px;
            }

            .header {
                padding: 0 20px;
            }
        }

        @media (max-width: 700px) {

            .container {
                flex-direction: column;
            }

            .header {
                height: 64px;
                min-height: 64px;
            }

            .lesson-title {
                font-size: 17px;
            }

            .header-right {
                gap: 0;
            }

            .content {
                padding: 25px 20px;
            }
        }

    </style>
</head>

<body>

@php
    // These come from PlanetController::viewModuleLesson(). Sensible
    // defaults so this view never breaks if hit without them.
    $slug   = $slug   ?? 'programming';
    $module = $module ?? 'm1';
    $lesson = $lesson ?? 'lesson01';

    // lessonView may be explicitly null (controller couldn't find a
    // matching file under either naming style) — leave it null rather
    // than guessing a string, so the check below shows the fallback.
    $lessonView = $lessonView ?? "student.planets.{$slug}.python_course." . strtoupper($module) . ".{$lesson}";
@endphp

{{--
    ┌─────────────────────────────────────────────────────────────┐
    │  CURRENT LESSON — injected by Blade on every page load.     │
    │  Kept in sync by loadLesson() on every client-side swap.    │
    │  The Python editor reads this to know which lesson to mark  │
    │  complete when the student gets the coding challenge right.  │
    └─────────────────────────────────────────────────────────────┘
--}}
<script>
    window.CURRENT_LESSON = {
        module: @json($module),
        lesson: @json($lesson),
    };
</script>


<div class="container">

    {{-- =========================================================
         SIDEBAR — the remote control. It only ever fires
         window.TechLab.loadLesson(module, lesson).
    ========================================================== --}}

    @include('components.programming-sidebar')


    {{-- =========================================================
         MAIN AREA — the TV. It never rebuilds itself, it just
         swaps what's showing on #lesson-stage.
    ========================================================== --}}

    <main class="main-area">

        <header class="header">

            <div class="header-left">

                <div
                    class="back-icon"
                    onclick="window.history.back()"
                    title="Go back"
                >
                    &#9664;
                </div>

                <div class="lesson-title" id="lesson-title-text">
                    Loading…
                </div>

            </div>


            <div class="header-right">

                <div class="icon" title="Dark mode">🌙</div>
                <div class="icon" title="Search">🔍</div>
                <div class="icon" title="Language">🌐</div>
                <div class="icon" title="Accessibility">♿</div>

                <div
                    class="icon"
                    title="Fullscreen"
                    onclick="toggleFullscreen()"
                >
                    ⛶
                </div>

            </div>

        </header>


        <div class="content">

            {{--
                THIS is the TV screen. Its id is what all the JS
                below targets when a sidebar button is pressed.
                It's server-rendered on first load (no flash / no
                extra round trip), then swapped client-side after.
            --}}
            <div class="lesson-content" id="lesson-stage">

                @includeIf($lessonView)

                @unless(view()->exists($lessonView))
                    <div class="empty-lesson">
                        <div>
                            <h1>Lesson not found</h1>
                            <p style="margin-top: 10px;">
                                The selected lesson file does not exist yet.
                            </p>
                        </div>
                    </div>
                @endunless

                <div class="nav-arrows">

                    <div
                        class="nav-arrow"
                        onclick="goToPreviousLesson()"
                        title="Previous lesson"
                    >
                        &#9664;
                    </div>

                    <div
                        class="nav-arrow"
                        onclick="goToNextLesson()"
                        title="Next lesson"
                    >
                        &#9654;
                    </div>

                </div>

            </div>

        </div>

    </main>

</div>


<script>
(function () {

    const slug  = @json($slug);
    let current = { module: @json($module), lesson: @json($lesson) };

    const stage    = document.getElementById('lesson-stage');
    const titleEl  = document.getElementById('lesson-title-text');

    // Ordered list used only for the prev/next arrows.
    const lessonOrder = [
        {module:'m1', lesson:'lesson01'}, {module:'m1', lesson:'lesson02'}, {module:'m1', lesson:'lesson03'},
        {module:'m1', lesson:'lesson04'}, {module:'m1', lesson:'lesson05'}, {module:'m1', lesson:'lesson06'},
        {module:'m2', lesson:'lesson01'}, {module:'m2', lesson:'lesson02'}, {module:'m2', lesson:'lesson03'},
        {module:'m2', lesson:'lesson04'}, {module:'m2', lesson:'lesson05'}, {module:'m2', lesson:'lesson06'},
        {module:'m3', lesson:'lesson01'}, {module:'m3', lesson:'lesson02'}, {module:'m3', lesson:'lesson03'},
        {module:'m3', lesson:'lesson04'}, {module:'m3', lesson:'lesson05'},
        {module:'m4', lesson:'lesson01'}, {module:'m4', lesson:'lesson02'}, {module:'m4', lesson:'lesson03'},
        {module:'m4', lesson:'lesson04'}, {module:'m4', lesson:'lesson05'}, {module:'m4', lesson:'lesson06'},
        {module:'m4', lesson:'lesson07'}, {module:'m4', lesson:'lesson08'},
        {module:'m5', lesson:'lesson01'}, {module:'m5', lesson:'lesson02'}, {module:'m5', lesson:'lesson03'},
        {module:'m5', lesson:'lesson04'}, {module:'m5', lesson:'lesson05'},
        {module:'m6', lesson:'lesson01'}, {module:'m6', lesson:'lesson02'}, {module:'m6', lesson:'lesson03'},
        {module:'m6', lesson:'lesson04'},
        {module:'m7', lesson:'lesson01'},
    ];

    function syncTitleAndSidebar(moduleId, lessonId) {
        const heading = stage.querySelector('h1, .lesson-heading');
        if (titleEl) titleEl.textContent = heading ? heading.textContent.trim() : 'Untitled lesson';

        document.querySelectorAll('.lesson-item').forEach(function (a) {
            a.classList.toggle(
                'active',
                a.dataset.module === moduleId && a.dataset.lesson === lessonId
            );
        });
    }

    // THIS is the core function. Pressing a sidebar "channel button"
    // (or the prev/next arrows) always ends up calling this. It never
    // touches window.location — it fetches the fragment and drops it
    // straight into the TV screen.
    async function loadLesson(moduleId, lessonId, opts) {
        opts = opts || {};
        const pushState = opts.pushState !== false;

        const pageUrl     = `/student/planet/${slug}/${moduleId}/${lessonId}`;
        const fragmentUrl = `${pageUrl}/fragment`;

        stage.style.opacity = '0.35';

        try {
            const res = await fetch(fragmentUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!res.ok) throw new Error('not found');

            const html = await res.text();

            stage.innerHTML = html;
            current = { module: moduleId, lesson: lessonId };

            // ── Keep CURRENT_LESSON in sync so the Python editor always
            //    knows which lesson it is displaying, even after a
            //    client-side swap via the sidebar or prev/next arrows.
            window.CURRENT_LESSON = { module: moduleId, lesson: lessonId };

            syncTitleAndSidebar(moduleId, lessonId);

            // Tell the sidebar to move its active highlight.
            if (window.TechLab && typeof window.TechLab.setActiveLesson === 'function') {
                window.TechLab.setActiveLesson(moduleId, lessonId);
            }

            if (pushState) {
                history.pushState({ module: moduleId, lesson: lessonId }, '', pageUrl);
            }
        } catch (err) {
            stage.innerHTML =
                '<div class="empty-lesson"><div><h1>Lesson not found</h1>' +
                '<p style="margin-top:10px;">Could not load this lesson.</p></div></div>';
        } finally {
            stage.style.opacity = '1';
        }
    }

    function goToPreviousLesson() {
        const idx = lessonOrder.findIndex(function (l) {
            return l.module === current.module && l.lesson === current.lesson;
        });
        if (idx > 0) {
            const prevLesson = lessonOrder[idx - 1];
            // Check if the previous lesson is unlocked before navigating
            if (window.TechLab && typeof window.TechLab.getLessonStatus === 'function') {
                const status = window.TechLab.getLessonStatus(prevLesson.module, prevLesson.lesson);
                if (status.unlocked) {
                    loadLesson(prevLesson.module, prevLesson.lesson);
                }
            } else {
                // Fallback: navigate anyway if TechLab not available
                loadLesson(prevLesson.module, prevLesson.lesson);
            }
        }
    }

    function goToNextLesson() {
        const idx = lessonOrder.findIndex(function (l) {
            return l.module === current.module && l.lesson === current.lesson;
        });
        if (idx !== -1 && idx < lessonOrder.length - 1) {
            const nextLesson = lessonOrder[idx + 1];
            // Check if the next lesson is unlocked before navigating
            if (window.TechLab && typeof window.TechLab.getLessonStatus === 'function') {
                const status = window.TechLab.getLessonStatus(nextLesson.module, nextLesson.lesson);
                if (status.unlocked) {
                    loadLesson(nextLesson.module, nextLesson.lesson);
                }
            } else {
                // Fallback: navigate anyway if TechLab not available
                loadLesson(nextLesson.module, nextLesson.lesson);
            }
        }
    }

    // Back/forward browser buttons should also just re-tune the TV,
    // not reload the page.
    window.addEventListener('popstate', function (e) {
        if (e.state && e.state.module && e.state.lesson) {
            loadLesson(e.state.module, e.state.lesson, { pushState: false });
        }
    });

    // Expose the one function the sidebar is allowed to call.
    // The sidebar knows NOTHING else about how this panel works.
    window.TechLab = window.TechLab || {};
    window.TechLab.loadLesson = loadLesson;

    window.goToPreviousLesson = goToPreviousLesson;
    window.goToNextLesson = goToNextLesson;

    window.toggleFullscreen = function () {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen && document.documentElement.requestFullscreen();
        } else {
            document.exitFullscreen && document.exitFullscreen();
        }
    };

    // Sync the header title + sidebar highlight for whatever lesson
    // was rendered server-side on first page load.
    document.addEventListener('DOMContentLoaded', function () {
        syncTitleAndSidebar(current.module, current.lesson);

        // Tell the sidebar script (which may have already run) about
        // the initial active lesson.
        if (window.TechLab && typeof window.TechLab.setActiveLesson === 'function') {
            window.TechLab.setActiveLesson(current.module, current.lesson);
        }
    });

})();
</script>

</body>
</html>