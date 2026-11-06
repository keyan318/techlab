{{-- M1 · Lesson 1.2: Orders and Answers — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Functions — return values and parameters. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="02">

    <h1 class="lesson-heading">Lesson 1.2: Orders and Answers</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can pass values into a function through parameters, send a result back with <code>return</code>, and explain why a function only gets a <em>copy</em> of what you pass in.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Values go <strong>into</strong> a function through its parameters, and one value can come <strong>out</strong> through <code>return</code>:
    </p>

    <pre class="code-block"><code>int cableNeeded(int lamps, int metresEach) {
    return lamps * metresEach;
}</code></pre>

    <p class="body-text">
        The <code>int</code> before the name promises the function will hand back an <code>int</code>. The values you write in the call, like <code>cableNeeded(6, 12)</code>, are called <strong>arguments</strong>. Each argument fills the parameter in the same position: <code>lamps</code> gets 6, <code>metresEach</code> gets 12.
    </p>

    <p class="body-text">
        A call to a function that returns something can be used like a value. You can store it, print it, or use it in a sum:
    </p>

    <pre class="code-block"><code>int cable = cableNeeded(6, 12);   // cable is now 72</code></pre>

    <p class="body-text">
        One rule surprises people. In C, arguments are passed <strong>by value</strong>: the function gets its own <em>copy</em> of each value. If the function changes its copy, the variable back in <code>main</code> does not change at all.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        At the crossing on Gate Road there is a junction box, a fat metal cabinet with cables going off down every street. Before anyone pulls cable off the drum, the box needs one number: how much.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> taps its screen. "You give it two numbers, how many lamps and how far apart they are. It does the sum and hands you back one number. Orders in, answer out."
    </p>

    <p class="body-text">
        <strong>Volt</strong> folds his arms. "And while it's doing the sum, can it mess up the numbers I gave it? I wrote six lamps. I want it to still say six."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "It can't touch yours. It gets its own copy, like writing the number on a scrap of paper and handing over the scrap. It can scribble on the scrap all it wants. Your six stays six."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="main passes copies of 6 and 12 into cableNeeded, which returns 72 to main">
            <rect x="20" y="60" width="190" height="80" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="115" y="87.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">main()</text>
            <text x="115" y="105.5" text-anchor="middle" font-size="13" fill="#14306b" font-family="ui-monospace, monospace">lamps = 6</text>
            <text x="115" y="123.5" text-anchor="middle" font-size="13" fill="#14306b" font-family="ui-monospace, monospace">cable = ?</text>
            <rect x="410" y="50" width="210" height="100" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="515" y="87.5" text-anchor="middle" font-size="15" font-weight="700" fill="#4a35b8">cableNeeded</text>
            <text x="515" y="105.5" text-anchor="middle" font-size="13" fill="#4a35b8">gets copies: 6, 12</text>
            <text x="515" y="123.5" text-anchor="middle" font-size="13" fill="#4a35b8">return 6 * 12</text>
            <line x1="210" y1="85" x2="396.0" y2="80.3" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="406,80 393.9,74.3 394.2,86.3" fill="#7c5cff"/>
            <text x="308" y="72" text-anchor="middle" font-size="12" fill="#475569">arguments in (copies)</text>
            <line x1="406" y1="125" x2="220.0" y2="120.3" stroke="#1f9d55" stroke-width="2.5" stroke-dasharray="4,3"/>
            <polygon points="210,120 221.8,126.3 222.1,114.3" fill="#1f9d55"/>
            <text x="308" y="142" text-anchor="middle" font-size="12" font-weight="700" fill="#14532d">return 72</text>
            <text x="320" y="188" text-anchor="middle" font-size="13" fill="#475569">Changing a copy inside the function never changes main's lamps.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int doubleIt(int n) {
    n = n * 2;      // changes only the function's copy
    return n;
}

int main() {
    int parts = 5;
    int more = doubleIt(parts);
    printf("More: %d\n", more);     // More: 10
    printf("Parts: %d\n", parts);   // Parts: 5
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Write <code>int cableNeeded(int lamps, int metresEach)</code>. Inside, work out <code>lamps * metresEach</code> into a variable, then set <code>lamps = 0;</code> (that only changes the copy), and return the total. In <code>main</code>, make <code>int lamps = 6;</code>, call <code>cableNeeded(lamps, 12)</code>, and print exactly:
    </p>

    <pre class="code-block"><code>Cable needed: 72
Lamps still: 6</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Orders and Answers">
            <input type="hidden" name="filename"     value="junction_box.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

// define int cableNeeded(int lamps, int metresEach) here
// work out the total, set lamps = 0, then return the total

int main() {
    int lamps = 6;

    // call cableNeeded(lamps, 12), store the answer, print both lines

    return 0;
}
">
            <input type="hidden" name="instructions" value="The junction box needs to know how much cable to pull.

Define int cableNeeded(int lamps, int metresEach):
1. store lamps * metresEach in a variable
2. set lamps = 0; (only the copy changes)
3. return the total

In main, with int lamps = 6;, call cableNeeded(lamps, 12) and print exactly:
Cable needed: 72
Lamps still: 6">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="int total = lamps * metresEach; then lamps = 0; then return total;. In main, catch the answer: int cable = cableNeeded(lamps, 12); and print cable, then print main's lamps. It is still 6 because the function only ever had a copy.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int cableNeeded(int lamps, int metresEach) {
    int total = lamps * metresEach;
    lamps = 0;
    return total;
}

int main() {
    int lamps = 6;
    int cable = cableNeeded(lamps, 12);
    printf(&quot;Cable needed: %d\n&quot;, cable);
    printf(&quot;Lamps still: %d\n&quot;, lamps);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm1', 'lesson' => 'lesson02']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In <code>cableNeeded(6, 12)</code>, what are 6 and 12 called?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Parameters</li>
            <li class="quiz-option quiz-correct">B. Arguments ✓</li>
            <li class="quiz-option">C. Return values</li>
            <li class="quiz-option">D. Prototypes</li>
        </ul>
        <p class="quiz-explanation"><em>The values written in a call are arguments; they fill the function's parameters in order.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does <code>return</code> do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Prints a value on the screen</li>
            <li class="quiz-option">B. Restarts the function from the top</li>
            <li class="quiz-option quiz-correct">C. Sends a value back to the caller ✓</li>
            <li class="quiz-option">D. Declares a brand-new variable</li>
        </ul>
        <p class="quiz-explanation"><em>return hands one value back to the code that called the function, and the function stops right there.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does "passed by value" mean in C?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The function gets its own copy of each argument ✓</li>
            <li class="quiz-option">B. The function can change the caller's variables directly</li>
            <li class="quiz-option">C. Only numbers can be passed</li>
            <li class="quiz-option">D. The value is passed to the screen</li>
        </ul>
        <p class="quiz-explanation"><em>C copies each argument into the parameter, so the function works on its own copy.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. <code>int parts = 5;</code> is passed to a function that sets its parameter to 0. What is <code>parts</code> in main afterwards?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 0</li>
            <li class="quiz-option quiz-correct">B. 5 ✓</li>
            <li class="quiz-option">C. 10</li>
            <li class="quiz-option">D. It depends on the return value</li>
        </ul>
        <p class="quiz-explanation"><em>The function changed only its copy, so main's parts is still 5.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Which line correctly stores what <code>cableNeeded</code> returns?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>cableNeeded(6, 12) = cable;</code></li>
            <li class="quiz-option">B. <code>int cable == cableNeeded(6, 12);</code></li>
            <li class="quiz-option">C. <code>int cable = cableNeeded;</code></li>
            <li class="quiz-option quiz-correct">D. <code>int cable = cableNeeded(6, 12);</code> ✓</li>
        </ul>
        <p class="quiz-explanation"><em>A call that returns a value can sit on the right of =, so its answer lands in the variable.</em></p>
    </div>

</div>
