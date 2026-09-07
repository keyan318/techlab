{{-- M1 · Lesson 1.2: Variables & Memory — content fragment. --}}
<div class="lesson-fragment" data-module="m1" data-lesson="02">

    <h1 class="lesson-heading">Lesson 1.2: Variables &amp; Memory</h1>
    <p class="lesson-objective"><strong>Learning Objective:</strong> The student can create a variable, assign it a value, and use it later in the program.</p>

    <h2 class="section-heading">Simple Explanation</h2>
    <p class="body-text">A variable is a labeled box that holds a value so you can use it again later instead of retyping it. You create one by writing a name, an equals sign, and the value: <code>hull_status = "stable"</code>. After that, writing <code>hull_status</code> anywhere in the program means "go look in that box and use what's inside."</p>

    <h2 class="section-heading">Astro Explanation</h2>
    <p class="body-text">Astro has hundreds of readings coming in — hull integrity, oxygen levels, fuel — and no way to remember any of them between checks. A variable is a labeled storage bin on the ship: you put a value in it once, slap a name on the bin, and Astro can check that bin any time without re-measuring from scratch.</p>

    <h2 class="section-heading">Code Example</h2>
    <pre class="code-block"><code>hull_status = "stable"
oxygen_level = 87
print(hull_status)
print(oxygen_level)</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>
    <p class="body-text">You're given starter code with an unnamed variable holding the number 42. Assign it the name <code>fuel_percent</code>, then print it.</p>

    @php
        $editorUrl = route('student.planet.editor', [
            'slug' => 'programming',
            'starter_code' => "mystery_number = 42\n\nprint(mystery_number)\n",
            'expected' => '42',
            'return_to' => route('student.planet.module.lesson', [
                'slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson02',
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

    <h2 class="section-heading">Challenge</h2>
    <p class="body-text">Create three variables representing a ship system (name, a number reading, and a True/False online status), then print a single combined status line using all three.</p>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does the line <code>crew_count = 5</code> do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Prints the number 5</li>
            <li class="quiz-option quiz-correct">B. Creates a variable named crew_count and stores 5 in it ✓</li>
            <li class="quiz-option">C. Compares crew_count to 5</li>
            <li class="quiz-option">D. Deletes crew_count</li>
        </ul>
        <p class="quiz-explanation"><em>The = sign assigns the value on the right (5) to the variable name on the left (crew_count), storing it for later use.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. After running <code>x = 10</code> followed by <code>x = 20</code>, what value does x hold?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 10</li>
            <li class="quiz-option quiz-correct">B. 20 ✓</li>
            <li class="quiz-option">C. Both 10 and 20 at once</li>
            <li class="quiz-option">D. An error occurs</li>
        </ul>
        <p class="quiz-explanation"><em>Assigning a new value to an existing variable overwrites the old one — the box now holds only the newest value.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why use a variable instead of just retyping a value every time?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Python requires it for every program</li>
            <li class="quiz-option">B. It makes the program run faster on any machine</li>
            <li class="quiz-option quiz-correct">C. It lets you store, reuse, and update a value by name instead of repeating it ✓</li>
            <li class="quiz-option">D. It prevents all errors</li>
        </ul>
        <p class="quiz-explanation"><em>Variables exist for readability and reuse — you can change a value in one place and every use of that variable name reflects the update.</em></p>
    </div>

</div>