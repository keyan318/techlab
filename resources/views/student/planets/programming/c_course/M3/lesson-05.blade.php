{{-- M3 · Lesson 3.5: Many Hands — content fragment.
Syllabus: CC102 Unit 3 — Functions (synthesis: definition, calling, parameters, return values, prototypes together). --}}

<div class="lesson-fragment" data-module="m3" data-lesson="05">

    <h1 class="lesson-heading">Lesson 3.5: Many Hands</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write a program made of several functions that call each other and
        pass values between them, each one handling one clear job.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A real program is rarely one function — it's several, each doing one job well, working together. A
        function that returns a value can feed that value straight into another function's parameter:
    </p>

    <pre class="code-block"><code>int result = computeSomething(a, b);
reportSomething(result);</code></pre>

    <p class="body-text">
        <code>computeSomething</code> handles the math and hands back an answer. <code>reportSomething</code>
        takes that answer and handles the output. Neither function needs to know how the other one works
        internally — <code>reportSomething</code> just needs a value to report, and doesn't care where it came
        from.
    </p>

    <p class="body-text">
        This is the whole point of functions: each one is a self-contained job. <code>main</code>'s role
        becomes coordinating them — call this, take its answer, hand it to that — rather than doing every step
        itself.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Two jobs now. One works out how far off full the shell is. The other says it out loud. Astro wants to
        see them working together.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "The one that measures shouldn't care how the answer gets read out. The one that
        reads it out shouldn't care how the number was worked out. Hand off, and trust it."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "That's just a crew."
    </p>

    <p class="body-text">
        <strong>Astro</strong> nods slowly. "That's exactly a crew. You don't check my navigation and I don't
        check your watch. We each do one job properly and pass it on." He looks at the screen a moment longer.
        "The ship's finally working the way we do."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="main calls shieldGap to get a value, then passes that value into reportGap">
            <rect x="10" y="75" width="130" height="55" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="75" y="107" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">main()</text>

            <rect x="230" y="20" width="180" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="320" y="45" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">shieldGap(cur, tgt)</text>
            <text x="320" y="65" text-anchor="middle" font-size="11.5" fill="#14306b">returns 65</text>

            <rect x="230" y="130" width="180" height="60" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="320" y="155" text-anchor="middle" font-size="13" font-weight="700" fill="#14663f">reportGap(gap)</text>
            <text x="320" y="175" text-anchor="middle" font-size="11.5" fill="#14663f">prints the line</text>

            <line x1="140" y1="90" x2="226" y2="50" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="230,50 218,44 218,56" fill="#7c5cff"/>
            <line x1="226" y1="65" x2="140" y2="100" stroke="#a68cff" stroke-width="2.5" stroke-dasharray="4,3"/>

            <line x1="140" y1="110" x2="226" y2="160" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="230,160 218,154 218,166" fill="#7c5cff"/>

            <text x="500" y="60" text-anchor="middle" font-size="12" fill="#475569">65 flows back to main,</text>
            <text x="500" y="76" text-anchor="middle" font-size="12" fill="#475569">then out to reportGap</text>
            <text x="500" y="92" text-anchor="middle" font-size="12" fill="#475569">as its argument.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int addParts(int a, int b);
void reportTotal(int total);

int main() {
    int total = addParts(12, 30);
    reportTotal(total);
    return 0;
}

int addParts(int a, int b) {
    return a + b;
}

void reportTotal(int total) {
    printf("Salvage total: %d\n", total);
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Combine Lesson 3.3's <code>shieldGap</code> with a new function <code>reportGap</code>. Write both with
        prototypes above <code>main</code>: <code>shieldGap(int current, int target)</code> returns
        <code>target - current</code>; <code>reportGap(int gap)</code> prints <code>"Shield gap: "</code>
        followed by <code>gap</code>. In <code>main</code>, call <code>shieldGap(35, 100)</code>, store the
        result, then pass it into <code>reportGap</code>. Expected output:
    </p>

    <pre class="code-block"><code>Shield gap: 65</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Many Hands">
            <input type="hidden" name="filename"     value="crew_report.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

// prototypes for shieldGap(int, int) and reportGap(int) go here

int main() {
    // call shieldGap(35, 100), store it, then pass it into reportGap

    return 0;
}

// full definitions of shieldGap and reportGap go here
">
            <input type="hidden" name="instructions" value="Write two functions, with prototypes above main:

int shieldGap(int current, int target) — returns target - current.
void reportGap(int gap) — prints &quot;Shield gap: &quot; followed by gap.

In main: call shieldGap(35, 100), store the result in a variable, then pass that variable into reportGap.

Expected output:
Shield gap: 65">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Two prototypes above main, two full definitions below it. main only needs: int gap = shieldGap(35, 100); then reportGap(gap);">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int shieldGap(int current, int target);
void reportGap(int gap);

int main() {
    int gap = shieldGap(35, 100);
    reportGap(gap);
    return 0;
}

int shieldGap(int current, int target) {
    return target - current;
}

void reportGap(int gap) {
    printf(&quot;Shield gap: %d\n&quot;, gap);
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm3', 'lesson' => 'lesson05']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In this lesson's exercise, what is passed into reportGap?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The literal numbers 35 and 100</li>
            <li class="quiz-option quiz-correct">B. The value shieldGap returned, stored in gap ✓</li>
            <li class="quiz-option">C. Nothing — reportGap takes no parameters</li>
            <li class="quiz-option">D. The text "Shield gap:"</li>
        </ul>
        <p class="quiz-explanation"><em>main stores shieldGap's return value in gap, then passes that same variable as reportGap's argument.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Does reportGap need to know how shieldGap computed its number?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Yes — it must know current and target to work</li>
            <li class="quiz-option quiz-correct">B. No — it only needs the final value it's handed, not how it was computed ✓</li>
            <li class="quiz-option">C. Yes, but only if the gap is negative</li>
            <li class="quiz-option">D. Functions can never be separated like this in C</li>
        </ul>
        <p class="quiz-explanation"><em>Each function only needs to know its own job — reportGap just reports whatever number it's given, regardless of how that number was calculated.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is main's role once a program is split across several functions like this?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. main must still do all the real computation itself</li>
            <li class="quiz-option quiz-correct">B. Coordinating: calling functions in order and passing results between them ✓</li>
            <li class="quiz-option">C. main becomes unnecessary and can be deleted</li>
            <li class="quiz-option">D. main can only ever call one function, total</li>
        </ul>
        <p class="quiz-explanation"><em>With well-split functions, main's job shifts from doing everything itself to coordinating calls and passing values between them.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Where must the prototypes for shieldGap and reportGap go for this program to work?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Inside main's body, anywhere</li>
            <li class="quiz-option quiz-correct">B. Above main, before any code that calls those functions ✓</li>
            <li class="quiz-option">C. At the very bottom of the file, after both real definitions</li>
            <li class="quiz-option">D. Prototypes are optional and never actually needed</li>
        </ul>
        <p class="quiz-explanation"><em>Just like Lesson 3.4, a prototype must appear before any code that calls the function — here, that means above main.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does Volt's "Rivet's team measures, my team reports" comment describe about good function design?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Every function should try to do as many jobs as possible</li>
            <li class="quiz-option quiz-correct">B. Each function should have one clear responsibility, and hand off to another for the rest ✓</li>
            <li class="quiz-option">C. Only one function should exist per program</li>
            <li class="quiz-option">D. Functions should never call each other</li>
        </ul>
        <p class="quiz-explanation"><em>Splitting "measure" and "report" into separate functions, each with one job, is exactly the kind of clean division that makes a program easier to build and fix.</em></p>
    </div>

</div>
