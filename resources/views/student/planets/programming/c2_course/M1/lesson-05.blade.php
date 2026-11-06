{{-- M1 · Lesson 1.5: The Job Board — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Functions — function prototypes; standard C functions. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="05">

    <h1 class="lesson-heading">Lesson 1.5: The Job Board</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can declare a function prototype so a function can be defined after <code>main</code>, and use ready-made functions from C's standard library, such as <code>sqrt</code> from <code>&lt;math.h&gt;</code>.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        C reads your file from top to bottom. If <code>main</code> calls a function that is defined further down, C has not met it yet. A <strong>function prototype</strong> fixes that: it is the function's first line, ending in a semicolon, written near the top.
    </p>

    <pre class="code-block"><code>double cableLength(double across, double up);   // prototype

int main() { ... cableLength(3.0, 4.0) ... }

double cableLength(double across, double up) {  // definition
    ...
}</code></pre>

    <p class="body-text">
        The prototype tells C the function's name, what it returns and what it needs. The full definition can then come anywhere later. A list of prototypes at the top also lets anyone see every job in the file at a glance.
    </p>

    <p class="body-text">
        You do not have to write every function yourself. C comes with a <strong>standard library</strong> of ready-made functions, grouped in header files. <code>#include</code> a header and you can use its functions:
    </p>

    <ul class="body-list">
        <li><code>&lt;stdio.h&gt;</code>: input and output, like <code>printf</code> and <code>scanf</code>.</li>
        <li><code>&lt;math.h&gt;</code>: maths, like <code>sqrt(x)</code> (square root) and <code>pow(x, y)</code>.</li>
        <li><code>&lt;string.h&gt;</code>: text, like <code>strlen</code> and <code>strcpy</code>.</li>
        <li><code>&lt;ctype.h&gt;</code>: characters, like <code>toupper</code>.</li>
        <li><code>&lt;stdlib.h&gt;</code>: general tools, like <code>abs</code> and <code>rand</code>.</li>
    </ul>

    <p class="body-text">
        Those headers are, in fact, mostly prototypes. That is how your program knows what <code>printf</code> needs.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Inside the repair depot, one whole wall is a chalkboard: the <strong>job board</strong>. Every job the depot ever did is chalked up there, each with its name and what it needs. The tools themselves are on shelves at the back.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> reads it like a book. "This is brilliant. You can see every job in the building before you open a single toolbox."
    </p>

    <p class="body-text">
        The last job on Gate Road: a cable from the top of the corner lamp down to the junction box. It goes 3 metres across and 4 metres up. <strong>Astro</strong>: "How long a cable? Not a guess. A number."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> pulls a box off the shelf with a square-root sign on it. "We don't build this tool. C already has it. Chalk our job on the board first, so everyone knows it's coming, then build it after."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 220" role="img" aria-label="The prototype at the top announces cableLength; main calls it; the definition below uses sqrt from math.h">
            <rect x="20" y="15" width="380" height="44" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="210" y="41.5" text-anchor="middle" font-size="13" font-weight="700" fill="#7a5a00">double cableLength(double, double);</text>
            <text x="410" y="42" text-anchor="start" font-size="12" fill="#7a5a00">prototype: the job board</text>
            <rect x="20" y="85" width="380" height="44" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="210" y="111.5" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">int main() { ... cableLength(3.0, 4.0) ... }</text>
            <rect x="20" y="155" width="380" height="50" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="210" y="184.5" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">double cableLength(...) { sqrt(...) }</text>
            <text x="410" y="112" text-anchor="start" font-size="12" fill="#14306b">calls it before it is built</text>
            <text x="410" y="138" text-anchor="start" font-size="12" fill="#4a35b8">definition, below main</text>
            <rect x="500" y="150" width="120" height="60" rx="12" fill="#e7f7ee" stroke="#1f9d55" stroke-width="2"/>
            <text x="560" y="176.5" text-anchor="middle" font-size="14" font-weight="700" fill="#14532d">math.h</text>
            <text x="560" y="193.5" text-anchor="middle" font-size="12" fill="#14532d">sqrt, pow</text>
            <line x1="496" y1="180" x2="414.0" y2="180.0" stroke="#1f9d55" stroke-width="2.5"/>
            <polygon points="404,180 416.0,186.0 416.0,174.0" fill="#1f9d55"/>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;
#include &lt;math.h&gt;

double area(double side);      // prototype

int main() {
    printf("Area: %.1f\n", area(3.0));          // Area: 9.0
    printf("Root: %.1f\n", sqrt(81.0));         // Root: 9.0
    return 0;
}

double area(double side) {     // definition, after main
    return pow(side, 2.0);
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Write the prototype <code>double cableLength(double across, double up);</code> above <code>main</code>. Define the function <em>after</em> <code>main</code>: it returns <code>sqrt(across * across + up * up)</code>. In <code>main</code>, call <code>cableLength(3.0, 4.0)</code> and print it with <code>%.1f</code>, exactly:
    </p>

    <pre class="code-block"><code>Cable: 5.0 m</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="The Job Board">
            <input type="hidden" name="filename"     value="job_board.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;
#include &lt;math.h&gt;

// write the prototype for cableLength here

int main() {
    // call cableLength(3.0, 4.0) and print &quot;Cable: &lt;length&gt; m&quot; with %.1f

    return 0;
}

// define cableLength here, below main: return sqrt(across * across + up * up)
">
            <input type="hidden" name="instructions" value="The cable runs 3 m across and 4 m up. How long is it?

1. Above main, write the prototype:
double cableLength(double across, double up);
2. Below main, define it: return sqrt(across * across + up * up);
3. In main, print the result of cableLength(3.0, 4.0) with %.1f:
Cable: 5.0 m">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="The prototype is the first line of the function plus a semicolon. The definition repeats that line with a body instead. In main: printf(&quot;Cable: %.1f m\n&quot;, cableLength(3.0, 4.0)); and #include &lt;math.h&gt; is already there, so sqrt is ready to use.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;
#include &lt;math.h&gt;

double cableLength(double across, double up);

int main() {
    printf(&quot;Cable: %.1f m\n&quot;, cableLength(3.0, 4.0));
    return 0;
}

double cableLength(double across, double up) {
    return sqrt(across * across + up * up);
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm1', 'lesson' => 'lesson05']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <p class="body-text" style="margin-top:2.5rem;">
        The cable goes in. Rivet throws the big switch on the junction box, and all three lamps on Gate Road come on together. The one that had been blinking for years stays on. It just stays on.
    </p>

    <p class="body-text">
        <strong>Volt</strong>, quietly: "First street."
    </p>

    <p class="body-text">
        Inside the depot, Rivet has found something better than tools: a map of the whole city, pinned to the wall. There are no street names on it. Every machine in the city is marked with a <strong>number</strong>.
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "They're addresses. The city doesn't find its machines by name. It finds them by where they live."
    </p>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is a function prototype?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The full body of a function</li>
            <li class="quiz-option quiz-correct">B. A function's first line, ending in ; ✓</li>
            <li class="quiz-option">C. A comment describing a function</li>
            <li class="quiz-option">D. A function with no return type</li>
        </ul>
        <p class="quiz-explanation"><em>A prototype gives the name, return type and parameters, so C knows about the function before its definition appears.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why might <code>main</code> need a prototype for a function?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The function is defined below main ✓</li>
            <li class="quiz-option">B. main cannot call functions directly</li>
            <li class="quiz-option">C. It makes the function run faster</li>
            <li class="quiz-option">D. The function is defined above main</li>
        </ul>
        <p class="quiz-explanation"><em>Without a prototype, C meets the call in main before it has seen the function, and complains.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which header gives you <code>sqrt</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>&lt;stdio.h&gt;</code></li>
            <li class="quiz-option">B. <code>&lt;string.h&gt;</code></li>
            <li class="quiz-option quiz-correct">C. <code>&lt;math.h&gt;</code> ✓</li>
            <li class="quiz-option">D. <code>&lt;ctype.h&gt;</code></li>
        </ul>
        <p class="quiz-explanation"><em>sqrt and pow are maths functions from the standard library, declared in math.h.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What is the standard C library?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Functions you must write yourself</li>
            <li class="quiz-option">B. A list of every keyword in C</li>
            <li class="quiz-option">C. A website full of C examples</li>
            <li class="quiz-option quiz-correct">D. Ready-made functions that come with C ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The standard library is C's built-in toolbox: include the right header and its functions are ready to call.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Which prototype matches <code>double cableLength(double across, double up) { ... }</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>double cableLength(int across, int up);</code></li>
            <li class="quiz-option">B. <code>int cableLength(double across, double up);</code></li>
            <li class="quiz-option quiz-correct">C. <code>double cableLength(double across, double up);</code> ✓</li>
            <li class="quiz-option">D. <code>double cableLength(double across, double up)</code></li>
        </ul>
        <p class="quiz-explanation"><em>A prototype copies the definition's first line exactly, with the same types, and ends with a semicolon.</em></p>
    </div>

</div>
