{{-- M2 · Lesson 2.4: Standing Watch — content fragment.
Syllabus: CC102 Unit 2 — Looping Statements (while, do-while). --}}

<div class="lesson-fragment" data-module="m2" data-lesson="04">

    <h1 class="lesson-heading">Lesson 2.4: Standing Watch</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write a <code>while</code> loop and a <code>do-while</code> loop, and
        explain the one situation where they behave differently.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <code>while</code> loop repeats a block of code as long as its condition stays true, checking
        <em>before</em> each run:
    </p>

    <pre class="code-block"><code>while (condition) {
    // repeats while condition is true
}</code></pre>

    <p class="body-text">
        If the condition is false the very first time, a <code>while</code> loop's body never runs at all. A
        <code>do-while</code> loop is nearly identical, but checks <em>after</em> each run instead — so its body
        always executes at least once, even if the condition was false from the start:
    </p>

    <pre class="code-block"><code>do {
    // runs once, THEN checks condition
} while (condition);</code></pre>

    <p class="body-text">
        Whichever loop you use, something inside the body must eventually make the condition false — otherwise
        the loop never stops.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        The storm drained the cells down to nothing. They refill overnight off what's left of the engines — and
        somebody has been sitting up watching the number climb.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Volt. You've been at that panel since midnight."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Somebody has to know when it's full."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>, already typing: "Or the console watches it and you go to bed. It just repeats
        the check — <em>is it full yet, is it full yet</em> — and stops the second it is. Could be five rounds,
        could be fifty. It doesn't get bored and it doesn't fall asleep at four in the morning."
    </p>

    <p class="body-text">
        <strong>Volt</strong>, not moving: "…Show me it works first."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="A while loop checks the condition first, then repeats the body until it becomes false">
            <ellipse cx="90" cy="45" rx="70" ry="26" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="90" y="51" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">charge = 0</text>

            <rect x="220" y="20" width="170" height="50" rx="10" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="305" y="42" text-anchor="middle" font-size="13" font-weight="700" fill="#14663f">charge &lt; 5?</text>
            <text x="305" y="60" text-anchor="middle" font-size="11" fill="#14663f">check BEFORE body</text>

            <rect x="220" y="110" width="170" height="55" rx="10" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="305" y="133" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">print charge;</text>
            <text x="305" y="151" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">charge++;</text>

            <rect x="470" y="20" width="150" height="50" rx="10" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="545" y="50" text-anchor="middle" font-size="13" font-weight="700" fill="#b3261e">false → exit loop</text>

            <line x1="160" y1="45" x2="216" y2="45" stroke="#7c5cff" stroke-width="2.5"/>
            <line x1="390" y1="45" x2="466" y2="45" stroke="#7c5cff" stroke-width="2.5"/>
            <text x="428" y="35" text-anchor="middle" font-size="11" fill="#4a35b8">false</text>
            <line x1="305" y1="70" x2="305" y2="106" stroke="#7c5cff" stroke-width="2.5"/>
            <text x="330" y="90" text-anchor="middle" font-size="11" fill="#4a35b8">true</text>
            <line x1="220" y1="137" x2="150" y2="137" stroke="#7c5cff" stroke-width="2.5"/>
            <line x1="150" y1="137" x2="150" y2="58" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="150,45 143,58 157,58" fill="#7c5cff"/>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int charge = 0;

    while (charge &lt; 5) {
        printf("Charge: %d\n", charge);
        charge++;
    }
    printf("Shield ready\n");
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Report the shield's boost cycle, starting from a partial charge. Declare <code>int level = 2;</code>,
        then print each level from 2 up to and including 5, and finally a completion line:
    </p>

    <pre class="code-block"><code>Level: 2
Level: 3
Level: 4
Level: 5
Boost complete</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Standing Watch">
            <input type="hidden" name="filename"     value="boost_watch.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int level = 2;

    // while level &lt; 6: print &quot;Level: N&quot;, then level++
    // after the loop, print &quot;Boost complete&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="level starts at 2. Use a while loop that keeps going while level &lt; 6:

Print &quot;Level: &quot; followed by the current level, then increase level by 1. After the loop ends, print &quot;Boost complete&quot;.

Expected output:
Level: 2
Level: 3
Level: 4
Level: 5
Boost complete">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="while (level &lt; 6) keeps looping while level is 2, 3, 4, or 5 — it stops as soon as level reaches 6. Print level, then level++; inside the loop, and put the &quot;Boost complete&quot; printf after the closing brace, outside the loop.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int level = 2;

    while (level &lt; 6) {
        printf(&quot;Level: %d\n&quot;, level);
        level++;
    }
    printf(&quot;Boost complete\n&quot;);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm2', 'lesson' => 'lesson04']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. When does a while loop check its condition?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Before every run of the body, including the first ✓</li>
            <li class="quiz-option">B. Only after the body has run once</li>
            <li class="quiz-option">C. Only at the very end of the program</li>
            <li class="quiz-option">D. It never checks — it just runs forever</li>
        </ul>
        <p class="quiz-explanation"><em>while checks its condition before every iteration, including the first — if it's false immediately, the body never runs at all.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What must happen inside a while loop's body to avoid an infinite loop?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing — while loops always stop on their own</li>
            <li class="quiz-option quiz-correct">B. Something must eventually make the condition false ✓</li>
            <li class="quiz-option">C. It must contain a printf statement</li>
            <li class="quiz-option">D. It must contain another while loop</li>
        </ul>
        <p class="quiz-explanation"><em>A while loop keeps running as long as its condition is true — if nothing in the body ever changes that, it never stops.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is the one key difference between while and do-while?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. do-while cannot use variables</li>
            <li class="quiz-option quiz-correct">B. do-while checks its condition after the body runs, so the body always runs at least once ✓</li>
            <li class="quiz-option">C. while can only count up, do-while can only count down</li>
            <li class="quiz-option">D. There is no difference — they always behave identically</li>
        </ul>
        <p class="quiz-explanation"><em>do-while runs its body first and checks the condition after, guaranteeing at least one run even if the condition starts false.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. If <code>charge</code> starts at 5 in <code>while (charge &lt; 5) { ... }</code>, how many times does the loop body run?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Exactly once</li>
            <li class="quiz-option">B. Five times</li>
            <li class="quiz-option quiz-correct">C. Zero times ✓</li>
            <li class="quiz-option">D. Forever</li>
        </ul>
        <p class="quiz-explanation"><em>while checks before running — since 5 &lt; 5 is false from the start, the body never executes.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Astro said a for loop is often better than a while loop when...</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. You are only watching a condition, with no known number of repeats</li>
            <li class="quiz-option quiz-correct">B. You already know how many times to repeat ✓</li>
            <li class="quiz-option">C. The loop body needs to run at least once</li>
            <li class="quiz-option">D. There is never a reason to prefer one over the other</li>
        </ul>
        <p class="quiz-explanation"><em>A for loop groups the start, condition and step together, which reads most naturally when the repeat count is known ahead of time.</em></p>
    </div>

</div>
