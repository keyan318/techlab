{{-- M1 · Lesson 1.4: Expressions & Operators — content fragment. --}}
<div class="lesson-fragment" data-module="m1" data-lesson="04">

    <h1 class="lesson-heading">Lesson 1.4: Expressions &amp; Operators</h1>
    <p class="lesson-objective"><strong>Learning Objective:</strong> The student can write arithmetic expressions and combine text using operators, understanding basic operator precedence.</p>

    <h2 class="section-heading">Simple Explanation</h2>
    <p class="body-text">An expression is anything Python can evaluate down to a single value, like <code>3 + 4</code> or <code>"Sys" + "tem"</code>. Python supports the usual math operators (+, -, *, /) plus a few extras: ** for exponents, // for division that drops the remainder, and % for the remainder itself (called modulo). Just like in math class, multiplication and division happen before addition and subtraction unless you use parentheses.</p>

    <h2 class="section-heading">Astro Explanation</h2>
    <p class="body-text">Astro's diagnostics constantly combine numbers — total fuel burned, average hull temperature across three sensors, remaining oxygen after a leak. Every one of those is an expression: raw numbers going in, one answer coming out. Get the order of operations wrong and Astro reports the wrong reading to the Captain, which on a real ship would be a very bad day.</p>

    <h2 class="section-heading">Code Example</h2>
    <pre class="code-block"><code>damage = 100 - (15 * 2)
average_temp = (18 + 22 + 20) / 3
remainder_fuel = 47 % 10
print(damage, average_temp, remainder_fuel)</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>
    <p class="body-text">You're given three sensor readings. Write a single expression that computes their average using parentheses correctly, then print it.</p>

    @php
        $editorUrl = route('student.planet.editor', [
            'slug' => 'programming',
            'starter_code' => "reading1 = 18\nreading2 = 22\nreading3 = 20\n\n# compute and print the average using one expression\n",
            'expected' => '20.0',
            'return_to' => route('student.planet.module.lesson', [
                'slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson04',
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
    <p class="body-text">Write a program that calculates how many full crates of 6 emergency ration packs can be made from 155 packs, and how many packs are left over — using // and % on the same input.</p>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does <code>17 // 5</code> evaluate to?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 3.4</li>
            <li class="quiz-option quiz-correct">B. 3 ✓</li>
            <li class="quiz-option">C. 2</li>
            <li class="quiz-option">D. 85</li>
        </ul>
        <p class="quiz-explanation"><em>// is floor (integer) division — it divides and drops anything after the decimal point, giving 3.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does <code>17 % 5</code> evaluate to?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 3.4</li>
            <li class="quiz-option">B. 3</li>
            <li class="quiz-option quiz-correct">C. 2 ✓</li>
            <li class="quiz-option">D. 85</li>
        </ul>
        <p class="quiz-explanation"><em>% (modulo) gives the remainder after division: 17 divided by 5 is 3 with 2 left over.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is the result of <code>2 + 3 * 4</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 20</li>
            <li class="quiz-option quiz-correct">B. 14 ✓</li>
            <li class="quiz-option">C. 24</li>
            <li class="quiz-option">D. 9</li>
        </ul>
        <p class="quiz-explanation"><em>Multiplication happens before addition, so it's 2 + (3 * 4) = 2 + 12 = 14.</em></p>
    </div>

</div>