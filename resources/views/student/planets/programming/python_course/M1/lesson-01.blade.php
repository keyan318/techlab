{{-- M1 · Lesson 1.1: First Signal — content fragment, no <html>/<head>/<body>.
     Swapped into #lesson-stage by programming.blade.php's fetch() logic. --}}
<div class="lesson-fragment" data-module="m1" data-lesson="01">

    <h1 class="lesson-heading">Lesson 1.1: First Signal</h1>
    <p class="lesson-objective"><strong>Learning Objective:</strong> The student can write and run a Python program that produces output, and can explain what <code>print()</code> does.</p>

    <h2 class="section-heading">Simple Explanation</h2>
    <p class="body-text">A computer program is just a list of instructions the computer runs one line at a time, top to bottom. The <code>print()</code> function is how a Python program talks back to you — whatever you put inside the parentheses gets shown on the screen. Text you want printed exactly as-is goes inside quote marks, and that's called a string.</p>

    <h2 class="section-heading">Astro Explanation</h2>
    <p class="body-text">Astro's speakers have power but no signal. The first thing it needs isn't a full sentence — it just needs to know that pressing one button (<code>print()</code>) makes a sound come out. Think of <code>print()</code> as Astro's "speak" button: whatever text you hand it, it says out loud.</p>

    <h2 class="section-heading">Code Example</h2>
    <pre class="code-block"><code>print("Astro is online.")
print("Signal strength: nominal")</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>
    <p class="body-text">You're given a broken snippet missing the <code>print()</code> call around a string. Fix it so running the code outputs exactly: <em>Ship systems rebooting...</em></p>

    @php
        // This lesson's own starter code + correct answer, built right here
        // on the lesson page. The editor page itself has no idea any of
        // this exists — it just receives it through the link below.
        $editorUrl = route('student.planet.editor', [
            'slug' => 'programming',
            'starter_code' => "\"Ship systems rebooting...\"\n",
            'expected' => 'Ship systems rebooting...',
            'return_to' => route('student.planet.module.lesson', [
                'slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01',
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
    <p class="body-text">Write a 3-line program that prints Astro's name, its current status ("awake"), and a one-line greeting to the Captain — using three separate <code>print()</code> calls.</p>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does <code>print("Hello")</code> do when the program runs?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Stores the word Hello in memory</li>
            <li class="quiz-option quiz-correct">B. Displays the word Hello on the screen ✓</li>
            <li class="quiz-option">C. Deletes the word Hello</li>
            <li class="quiz-option">D. Asks the user to type Hello</li>
        </ul>
        <p class="quiz-explanation"><em>print() outputs whatever is inside its parentheses to the screen — it doesn't store or delete anything.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which of these is written correctly as Python code?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. print(Astro is online)</li>
            <li class="quiz-option">B. print "Astro is online"</li>
            <li class="quiz-option quiz-correct">C. print("Astro is online") ✓</li>
            <li class="quiz-option">D. PRINT(Astro is online)</li>
        </ul>
        <p class="quiz-explanation"><em>Text meant to be printed exactly as written needs to be inside quote marks, and the whole thing goes inside print()'s parentheses.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. If you write two separate print() lines, what happens when the program runs?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Only the second line shows</li>
            <li class="quiz-option quiz-correct">B. Both lines show, one after another, top to bottom ✓</li>
            <li class="quiz-option">C. Python picks one at random</li>
            <li class="quiz-option">D. It causes an error</li>
        </ul>
        <p class="quiz-explanation"><em>Python runs instructions in order from top to bottom, so both print() calls execute and both lines of output appear in sequence.</em></p>
    </div>

</div>