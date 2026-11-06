{{-- M1 · Lesson 1.4: Crates Inside Crates — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Functions — recursive functions. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="04">

    <h1 class="lesson-heading">Lesson 1.4: Crates Inside Crates</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain recursion, name a recursive function's base case and recursive case, and write a recursive function that adds up a pile.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>recursive function</strong> is a function that calls itself. It solves a big problem by solving a slightly smaller copy of the same problem, again and again, until the problem is so small the answer is obvious.
    </p>

    <p class="body-text">
        Every recursive function needs two parts:
    </p>

    <ul class="body-list">
        <li>A <strong>base case</strong>: the smallest problem, answered straight away with no more calls. This is what makes it stop.</li>
        <li>A <strong>recursive case</strong>: the step that calls the same function on a smaller problem.</li>
    </ul>

    <pre class="code-block"><code>int factorial(int n) {
    if (n &lt;= 1) {
        return 1;                  // base case
    }
    return n * factorial(n - 1);   // recursive case
}</code></pre>

    <p class="body-text">
        <code>factorial(3)</code> waits for <code>factorial(2)</code>, which waits for <code>factorial(1)</code>. That one returns 1 straight away, and the answers come back up: 2 × 1 = 2, then 3 × 2 = 6. Each waiting call sits on the <strong>call stack</strong> until the one below it answers.
    </p>

    <p class="body-text">
        Forget the base case, or make a call that never gets smaller, and the function calls itself forever until the program runs out of memory.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Behind the old repair depot there is a pile of spare bulb crates, stacked like a pyramid: one crate on top, two under it, three under those, and four along the bottom. Astro wants to know how many bulbs they have, which means: how many crates.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> is delighted. "Okay, look. A pile of four rows is the bottom row, four crates, sitting on a pile of three rows. And a pile of three rows is its bottom row on a pile of two. Same question, smaller every time!"
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And it stops where? If it keeps asking about smaller piles forever, we're here all night."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "A pile with zero rows has zero crates. Nobody has to think about that one. That's the floor, and every question lands on it eventually."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 230" role="img" aria-label="crates(4) calls crates(3), which calls crates(2), then crates(1), then crates(0) returns 0 and the answers add back up to 10">
            <rect x="20" y="20" width="170" height="44" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="105" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">crates(4)</text>
            <rect x="110" y="72" width="170" height="44" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="195" y="99" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">crates(3)</text>
            <rect x="200" y="124" width="170" height="44" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="285" y="151" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">crates(2)</text>
            <rect x="290" y="176" width="170" height="44" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="375" y="203" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">crates(1)</text>
            <rect x="470" y="176" width="150" height="44" rx="12" fill="#e7f7ee" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="203" text-anchor="middle" font-size="14" font-weight="700" fill="#14532d">crates(0) = 0</text>
            <line x1="150" y1="64" x2="155.7" y2="66.3" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="165,70 156.1,60.0 151.6,71.1" fill="#7c5cff"/>
            <line x1="240" y1="116" x2="245.7" y2="118.3" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="255,122 246.1,112.0 241.6,123.1" fill="#7c5cff"/>
            <line x1="330" y1="168" x2="335.7" y2="170.3" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="345,174 336.1,164.0 331.6,175.1" fill="#7c5cff"/>
            <line x1="460" y1="198" x2="456.0" y2="198.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="466,198 454.0,192.0 454.0,204.0" fill="#7c5cff"/>
            <text x="300" y="42" text-anchor="start" font-size="13" font-weight="700" fill="#14532d">4 + 6 = 10</text>
            <text x="390" y="94" text-anchor="start" font-size="13" font-weight="700" fill="#14532d">3 + 3 = 6</text>
            <text x="480" y="146" text-anchor="start" font-size="13" font-weight="700" fill="#14532d">2 + 1 = 3</text>
            <text x="545" y="168" text-anchor="middle" font-size="12" fill="#14532d">base case</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

void countDown(int n) {
    if (n == 0) {
        printf("Doors open\n");   // base case
        return;
    }
    printf("%d\n", n);
    countDown(n - 1);              // smaller problem
}

int main() {
    countDown(3);    // prints 3, 2, 1, Doors open
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. Write a recursive <code>int crates(int rows)</code>: if <code>rows</code> is 0, return 0; otherwise return <code>rows + crates(rows - 1)</code>. In <code>main</code>, call <code>crates(4)</code> and print exactly:
    </p>

    <pre class="code-block"><code>Crates in the pile: 10</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Crates Inside Crates">
            <input type="hidden" name="filename"     value="crate_pile.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int crates(int rows) {
    // base case: a pile with 0 rows has 0 crates

    // recursive case: this row plus the smaller pile under it
    return 0;
}

int main() {
    // print &quot;Crates in the pile: &quot; and crates(4)

    return 0;
}
">
            <input type="hidden" name="instructions" value="Count the crates in a pyramid pile with 4 rows (1 + 2 + 3 + 4).

Write int crates(int rows):
- if rows is 0, return 0 (the base case)
- otherwise return rows + crates(rows - 1)

In main, print crates(4) like this:
Crates in the pile: 10">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="Two returns. First: if (rows == 0) { return 0; } Then, under it: return rows + crates(rows - 1); Write it with a plain if like that, not with ? :. Each call asks about a pile one row smaller, until it reaches 0.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int crates(int rows) {
    if (rows == 0) {
        return 0;
    }
    return rows + crates(rows - 1);
}

int main() {
    printf(&quot;Crates in the pile: %d\n&quot;, crates(4));
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm1', 'lesson' => 'lesson04']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is a recursive function?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A function that runs only once</li>
            <li class="quiz-option">B. A function with no parameters</li>
            <li class="quiz-option quiz-correct">C. A function that calls itself ✓</li>
            <li class="quiz-option">D. A function that returns void</li>
        </ul>
        <p class="quiz-explanation"><em>Recursion means a function solves a problem by calling itself on a smaller version of the same problem.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is the job of the base case?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. To make the function call itself</li>
            <li class="quiz-option quiz-correct">B. To give the calls a place to stop ✓</li>
            <li class="quiz-option">C. To print the final result</li>
            <li class="quiz-option">D. To declare the parameters</li>
        </ul>
        <p class="quiz-explanation"><em>The base case is the smallest problem, answered straight away, so the chain of calls has somewhere to stop.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does <code>crates(2)</code> return, if <code>crates(0)</code> is 0 and <code>crates(n)</code> is <code>n + crates(n - 1)</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 2</li>
            <li class="quiz-option">B. 4</li>
            <li class="quiz-option">C. 1</li>
            <li class="quiz-option quiz-correct">D. 3 ✓</li>
        </ul>
        <p class="quiz-explanation"><em>crates(2) = 2 + crates(1) = 2 + (1 + crates(0)) = 2 + 1 + 0 = 3.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What happens if a recursive function has no base case?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It returns 0 straight away</li>
            <li class="quiz-option">B. It runs one time, then stops</li>
            <li class="quiz-option quiz-correct">C. It calls itself until memory runs out ✓</li>
            <li class="quiz-option">D. The compiler adds one for you</li>
        </ul>
        <p class="quiz-explanation"><em>With nothing to stop it, every call makes another call, and the waiting calls pile up until memory runs out.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. In <code>factorial(n)</code>, why must the recursive call use <code>n - 1</code> and not <code>n</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. So each call gets closer to the base case ✓</li>
            <li class="quiz-option">B. Because C does not allow n to be used twice</li>
            <li class="quiz-option">C. So the final answer comes out bigger</li>
            <li class="quiz-option">D. So the function runs faster each call</li>
        </ul>
        <p class="quiz-explanation"><em>Each call must move closer to the base case; calling factorial(n) again would ask the same question forever.</em></p>
    </div>

</div>
