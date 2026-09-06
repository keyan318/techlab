<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        {{ $lessonData['title'] ?? 'Programming Course' }}
    </title>

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
           SIDEBAR
        ========================================================= */

        .sidebar {
            width: 360px;
            min-width: 360px;
            height: 100vh;

            background-color: var(--white);

            border-right: 1px solid var(--border-gray);

            overflow-y: auto;
            overflow-x: hidden;
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

        .sidebar::-webkit-scrollbar,
        .content::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar::-webkit-scrollbar-track,
        .content::-webkit-scrollbar-track {
            background: #f7f7f7;
        }

        .sidebar::-webkit-scrollbar-thumb,
        .content::-webkit-scrollbar-thumb {
            background: #cfcfcf;
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover,
        .content::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1200px) {

            .sidebar {
                width: 320px;
                min-width: 320px;
            }

            .content {
                padding: 35px 40px;
            }
        }

        @media (max-width: 900px) {

            .sidebar {
                width: 300px;
                min-width: 300px;
            }

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

            .sidebar {
                width: 100%;
                min-width: 100%;

                height: 280px;

                border-right: none;
                border-bottom: 1px solid var(--border-gray);
            }

            .main-area {
                height: calc(100vh - 280px);
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

    /*
    |--------------------------------------------------------------------------
    | CURRENT LESSON
    |--------------------------------------------------------------------------
    |
    | The sidebar sends the lesson ID through the URL.
    |
    | Example:
    |
    | /student/planet/programming?lesson=m1-lesson01
    |
    */

    $lesson = request('lesson', 'm1-lesson01');


    /*
    |--------------------------------------------------------------------------
    | LESSON FILE MAP
    |--------------------------------------------------------------------------
    |
    | Each lesson ID points to one lesson data file.
    |
    */

    $lessonFiles = [

        // MODULE 1
        'm1-lesson01' => 'python_course/M1/lesson01.blade.php',
        'm1-lesson02' => 'python_course/M1/lesson02.blade.php',
        'm1-lesson03' => 'python_course/M1/lesson03.blade.php',
        'm1-lesson04' => 'python_course/M1/lesson04.blade.php',
        'm1-lesson05' => 'python_course/M1/lesson05.blade.php',
        'm1-lesson06' => 'python_course/M1/lesson06.blade.php',

        // MODULE 2
        'm2-lesson01' => 'python_course/M2/lesson01.blade.php',
        'm2-lesson02' => 'python_course/M2/lesson02.blade.php',
        'm2-lesson03' => 'python_course/M2/lesson03.blade.php',
        'm2-lesson04' => 'python_course/M2/lesson04.blade.php',
        'm2-lesson05' => 'python_course/M2/lesson05.blade.php',
        'm2-lesson06' => 'python_course/M2/lesson06.blade.php',

        // MODULE 3
        'm3-lesson01' => 'python_course/M3/lesson01.blade.php',
        'm3-lesson02' => 'python_course/M3/lesson02.blade.php',
        'm3-lesson03' => 'python_course/M3/lesson03.blade.php',
        'm3-lesson04' => 'python_course/M3/lesson04.blade.php',
        'm3-lesson05' => 'python_course/M3/lesson05.blade.php',

        // MODULE 4
        'm4-lesson01' => 'python_course/M4/lesson01.blade.php',
        'm4-lesson02' => 'python_course/M4/lesson02.blade.php',
        'm4-lesson03' => 'python_course/M4/lesson03.blade.php',
        'm4-lesson04' => 'python_course/M4/lesson04.blade.php',
        'm4-lesson05' => 'python_course/M4/lesson05.blade.php',
        'm4-lesson06' => 'python_course/M4/lesson06.blade.php',
        'm4-lesson07' => 'python_course/M4/lesson07.blade.php',
        'm4-lesson08' => 'python_course/M4/lesson08.blade.php',

        // MODULE 5
        'm5-lesson01' => 'python_course/M5/lesson01.blade.php',
        'm5-lesson02' => 'python_course/M5/lesson02.blade.php',
        'm5-lesson03' => 'python_course/M5/lesson03.blade.php',
        'm5-lesson04' => 'python_course/M5/lesson04.blade.php',
        'm5-lesson05' => 'python_course/M5/lesson05.blade.php',

        // MODULE 6
        'm6-lesson01' => 'python_course/M6/lesson01.blade.php',
        'm6-lesson02' => 'python_course/M6/lesson02.blade.php',
        'm6-lesson03' => 'python_course/M6/lesson03.blade.php',
        'm6-lesson04' => 'python_course/M6/lesson04.blade.php',

        // MODULE 7
        'm7-lesson01' => 'python_course/M7/lesson01.blade.php',
    ];


    /*
    |--------------------------------------------------------------------------
    | SELECT LESSON
    |--------------------------------------------------------------------------
    */

    $lessonFile = $lessonFiles[$lesson] ?? $lessonFiles['m1-lesson01'];


    /*
    |--------------------------------------------------------------------------
    | GET PHYSICAL FILE PATH
    |--------------------------------------------------------------------------
    */

    $lessonPath = resource_path(
        'views/student/planets/programming/' . $lessonFile
    );


    /*
    |--------------------------------------------------------------------------
    | LOAD LESSON DATA
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | We use include because the lesson file returns an array.
    |
    */

    $lessonData = null;

    if (file_exists($lessonPath)) {
        $lessonData = include $lessonPath;
    }

@endphp


<div class="container">

    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}

    @include('components.programming-sidebar')


    {{-- =========================================================
         MAIN AREA
    ========================================================== --}}

    <main class="main-area">

        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <header class="header">

            <div class="header-left">

                <div
                    class="back-icon"
                    onclick="window.history.back()"
                    title="Go back"
                >
                    &#9664;
                </div>

                <div class="lesson-title">
                    {{ $lessonData['title'] ?? 'Programming Fundamentals' }}
                </div>

            </div>


            <div class="header-right">

                <div class="icon" title="Dark mode">
                    🌙
                </div>

                <div class="icon" title="Search">
                    🔍
                </div>

                <div class="icon" title="Language">
                    🌐
                </div>

                <div class="icon" title="Accessibility">
                    ♿
                </div>

                <div
                    class="icon"
                    title="Fullscreen"
                    onclick="toggleFullscreen()"
                >
                    ⛶
                </div>

            </div>

        </header>


        {{-- =====================================================
             LESSON VIEWER
        ====================================================== --}}

        <div class="content">

            <div class="lesson-content">

                @if ($lessonData)

                    {{-- LESSON TITLE --}}

                    <h1 class="lesson-heading">
                        {{ $lessonData['title'] }}
                    </h1>


                    {{-- OBJECTIVE --}}

                    @if (!empty($lessonData['objective']))

                        <h2 class="secondary-heading">
                            Objective
                        </h2>

                        <p class="body-text">
                            {{ $lessonData['objective'] }}
                        </p>

                    @endif


                    {{-- SIMPLE EXPLANATION --}}

                    @if (!empty($lessonData['simple_explanation']))

                        <h2 class="secondary-heading">
                            Explanation
                        </h2>

                        <p class="body-text">
                            {{ $lessonData['simple_explanation'] }}
                        </p>

                    @endif


                    {{-- ASTRO EXPLANATION --}}

                    @if (!empty($lessonData['astro_explanation']))

                        <h2 class="secondary-heading">
                            Astro's Explanation
                        </h2>

                        <p class="body-text">
                            {{ $lessonData['astro_explanation'] }}
                        </p>

                    @endif


                    {{-- CODE EXAMPLE --}}

                    @if (!empty($lessonData['code_example']))

                        <h2 class="secondary-heading">
                            Code Example
                        </h2>

                        <pre class="code-block"><code>{{ $lessonData['code_example'] }}</code></pre>

                    @endif


                    {{-- INTERACTIVE EXERCISE --}}

                    @if (!empty($lessonData['interactive_exercise']))

                        <h2 class="secondary-heading">
                            Interactive Exercise
                        </h2>

                        <div class="exercise-card">

                            <p class="body-text">
                                {{ $lessonData['interactive_exercise']['prompt'] }}
                            </p>

                            <h3>
                                Starter Code
                            </h3>

                            <pre class="code-block"><code>{{ $lessonData['interactive_exercise']['starter_code'] }}</code></pre>

                            @if (!empty($lessonData['interactive_exercise']['expected_output']))

                                <h3>
                                    Expected Output
                                </h3>

                                <pre class="code-block"><code>{{ $lessonData['interactive_exercise']['expected_output'] }}</code></pre>

                            @endif

                        </div>

                    @endif


                    {{-- CHALLENGE --}}

                    @if (!empty($lessonData['challenge']))

                        <h2 class="secondary-heading">
                            Challenge
                        </h2>

                        <div class="exercise-card">

                            <p class="body-text">
                                {{ $lessonData['challenge']['prompt'] }}
                            </p>

                            <h3>
                                Starter Code
                            </h3>

                            <pre class="code-block"><code>{{ $lessonData['challenge']['starter_code'] }}</code></pre>

                        </div>

                    @endif


                    {{-- QUIZ --}}

                    @if (!empty($lessonData['quiz']))

                        <h2 class="secondary-heading">
                            Quiz
                        </h2>

                        @foreach ($lessonData['quiz'] as $index => $question)

                            <div class="quiz-card">

                                <p class="quiz-question">

                                    {{ $index + 1 }}.

                                    {{ $question['question'] }}

                                </p>


                                @foreach ($question['options'] as $key => $option)

                                    <div class="quiz-option">

                                        <strong>
                                            {{ strtoupper($key) }}.
                                        </strong>

                                        {{ $option }}

                                    </div>

                                @endforeach

                            </div>

                        @endforeach

                    @endif


                    {{-- LESSON NAVIGATION --}}

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


                @else

                    {{-- LESSON NOT FOUND --}}

                    <div class="empty-lesson">

                        <div>

                            <h1>
                                Lesson not found
                            </h1>

                            <p style="margin-top: 10px;">
                                The selected lesson file does not exist yet.
                            </p>

                            <p style="margin-top: 10px; font-size: 13px;">
                                {{ $lessonPath }}
                            </p>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </main>

</div>


<script>

    function toggleFullscreen() {

        if (!document.fullscreenElement) {

            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            }

        } else {

            if (document.exitFullscreen) {
                document.exitFullscreen();
            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | LESSON NAVIGATION
    |--------------------------------------------------------------------------
    */

    const lessonOrder = [

        'm1-lesson01',
        'm1-lesson02',
        'm1-lesson03',
        'm1-lesson04',
        'm1-lesson05',
        'm1-lesson06',

        'm2-lesson01',
        'm2-lesson02',
        'm2-lesson03',
        'm2-lesson04',
        'm2-lesson05',
        'm2-lesson06',

        'm3-lesson01',
        'm3-lesson02',
        'm3-lesson03',
        'm3-lesson04',
        'm3-lesson05',

        'm4-lesson01',
        'm4-lesson02',
        'm4-lesson03',
        'm4-lesson04',
        'm4-lesson05',
        'm4-lesson06',
        'm4-lesson07',
        'm4-lesson08',

        'm5-lesson01',
        'm5-lesson02',
        'm5-lesson03',
        'm5-lesson04',
        'm5-lesson05',

        'm6-lesson01',
        'm6-lesson02',
        'm6-lesson03',
        'm6-lesson04',

        'm7-lesson01'

    ];


    const currentLesson = @json($lesson);


    function goToPreviousLesson() {

        const currentIndex =
            lessonOrder.indexOf(currentLesson);

        if (currentIndex > 0) {

            const previousLesson =
                lessonOrder[currentIndex - 1];

            window.location.href =
                '{{ url('/student/planet/programming') }}?lesson='
                + previousLesson;
        }

    }


    function goToNextLesson() {

        const currentIndex =
            lessonOrder.indexOf(currentLesson);

        if (
            currentIndex !== -1 &&
            currentIndex < lessonOrder.length - 1
        ) {

            const nextLesson =
                lessonOrder[currentIndex + 1];

            window.location.href =
                '{{ url('/student/planet/programming') }}?lesson='
                + nextLesson;
        }

    }

</script>

</body>
</html>