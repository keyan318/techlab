{{-- M1 Mini Project: Wake Astro Up — content fragment.
     Treated as "lesson 07" in the m1 sequence so it slots into the same
     resolveLessonView() naming scheme as the numbered lessons. --}}
<div class="lesson-fragment" data-module="m1" data-lesson="07">

    <h1 class="lesson-heading">M1 Mini Project: Wake Astro Up</h1>

    <h2 class="section-heading">Brief</h2>
    <p class="body-text">Build an interactive script that greets the user, asks for their name and how many days until launch (using <code>input()</code>), stores the answers in variables, converts the day count to an int, and prints a personalized 3-line status report. Must use at least one <code>print()</code>, one <code>input()</code>, one type conversion, and one arithmetic expression.</p>

    <h2 class="section-heading">Completion Rubric</h2>
    <ul class="body-text">
        <li>Runs without errors</li>
        <li>Asks at least 2 questions</li>
        <li>Converts and uses input correctly</li>
        <li>Final output is personalized (uses the entered name)</li>
        <li>Includes at least one calculated value (e.g., days converted to hours)</li>
    </ul>

    @php
        $editorUrl = route('student.planet.editor', [
            'slug' => 'programming',
            'starter_code' => "# ask for the user's name\n\n# ask how many days until launch\n\n# convert the day count to an int\n\n# print a personalized 3-line status report\n",
            'return_to' => route('student.planet.module.lesson', [
                'slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson07',
            ]),
        ]);
    @endphp
    <div class="cta-wrap">
        <a href="{{ $editorUrl }}"
           class="btn-code-yourself"
           style="display:inline-block;padding:12px 28px;border-radius:8px;background:#22c98a;color:#0e1230;font-weight:700;text-decoration:none;font-family:'Space Grotesk',sans-serif;">
            🐍 Code it yourself
        </a>
    </div>

</div>