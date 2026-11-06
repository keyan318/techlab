{{-- M3 · Lesson 3.1: Six Lamps, One Name — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Arrays — structured data types; defining an array; array initialization. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="01">

    <h1 class="lesson-heading">Lesson 3.1: Six Lamps, One Name</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can tell a simple data type from a structured one, define and initialise an array, and work out how many elements it has with <code>sizeof</code>.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        An <code>int</code>, a <code>double</code> or a <code>char</code> is a <strong>simple data type</strong>: one variable holds one value. A <strong>structured data type</strong> groups many values under one name. The first one you meet is the <strong>array</strong>: a row of values that all have the same type, side by side in memory.
    </p>

    <pre class="code-block"><code>int lamps[6];                                  // six ints, all called lamps
int lamps[6] = {30, 0, 55, 80, 0, 45};          // defined and filled in</code></pre>

    <p class="body-text">
        Each value is an <strong>element</strong>, reached by its <strong>index</strong> in square brackets. Indexes start at 0, so a 6-element array runs from <code>lamps[0]</code> to <code>lamps[5]</code>. There is no <code>lamps[6]</code>; using it reads past the end of the array.
    </p>

    <p class="body-text">
        Ways to initialise an array:
    </p>

    <ul class="body-list">
        <li><code>int a[4] = {1, 2, 3, 4};</code> sets every element.</li>
        <li><code>int a[4] = {1, 2};</code> sets the first two; the rest become 0.</li>
        <li><code>int a[] = {1, 2, 3};</code> leaves out the size; C counts the values and makes it 3.</li>
    </ul>

    <p class="body-text">
        <code>sizeof(lamps)</code> gives the size of the whole array in bytes, and <code>sizeof(lamps[0])</code> the size of one element. Divide them and you get the number of elements:
    </p>

    <pre class="code-block"><code>int count = sizeof(lamps) / sizeof(lamps[0]);   // 6</code></pre>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Lamp Street is long and straight. Six lamps stand down one side of it, all the same model, evenly spaced. Two of them are completely dark.
    </p>

    <p class="body-text">
        The control box at the top of the street shows them as one line: <code>30 0 55 80 0 45</code>.
    </p>

    <p class="body-text">
        <strong>Rivet</strong>, grinning: "One name for the whole street! Not six separate variables, one row. And counting starts at zero. I know. I <em>know</em>. Everyone hates it for a week and then never thinks about it again."
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "First thing. How many lamps does this box think it has? And what do the two ends read?"
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 130" role="img" aria-label="An array of six ints called lamps, indexes 0 to 5, holding 30, 0, 55, 80, 0 and 45">
            <rect x="25" y="20" width="90" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="70" y="54.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a5a00">30</text>
            <text x="70" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[0]</text>
            <rect x="125" y="20" width="90" height="56" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="170" y="54.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a1a14">0</text>
            <text x="170" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[1]</text>
            <rect x="225" y="20" width="90" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="270" y="54.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a5a00">55</text>
            <text x="270" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[2]</text>
            <rect x="325" y="20" width="90" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="370" y="54.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a5a00">80</text>
            <text x="370" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[3]</text>
            <rect x="425" y="20" width="90" height="56" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="470" y="54.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a1a14">0</text>
            <text x="470" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[4]</text>
            <rect x="525" y="20" width="90" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="570" y="54.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a5a00">45</text>
            <text x="570" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[5]</text>
            <text x="320" y="125" text-anchor="middle" font-size="13" fill="#475569">one name, six elements, indexes 0 to 5</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int fountains[] = {4, 9, 2};      // C counts: 3 elements
    int count = sizeof(fountains) / sizeof(fountains[0]);

    printf("Fountains: %d\n", count);         // 3
    printf("Middle: %d\n", fountains[1]);     // 9
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. Define <code>int lamps[6] = {30, 0, 55, 80, 0, 45};</code>. Work out the number of lamps with <code>sizeof</code> (do not just write 6), then print the count, the first lamp and the last lamp. Exactly:
    </p>

    <pre class="code-block"><code>Lamps: 6
First: 30
Last: 45</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Six Lamps, One Name">
            <input type="hidden" name="filename"     value="lamp_street.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int lamps[6] = {30, 0, 55, 80, 0, 45};

    // work out how many lamps there are with sizeof

    // print &quot;Lamps: &quot;, &quot;First: &quot; and &quot;Last: &quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="Lamp Street's box shows: 30 0 55 80 0 45

1. Count the lamps with:
   int count = sizeof(lamps) / sizeof(lamps[0]);
2. Print the count, the first lamp and the last lamp.

The output must be exactly:
Lamps: 6
First: 30
Last: 45">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="The first lamp is lamps[0]. The last is lamps[count - 1], not lamps[count]: with 6 lamps the indexes stop at 5. Three printfs, one for each line.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int lamps[6] = {30, 0, 55, 80, 0, 45};
    int count = sizeof(lamps) / sizeof(lamps[0]);

    printf(&quot;Lamps: %d\n&quot;, count);
    printf(&quot;First: %d\n&quot;, lamps[0]);
    printf(&quot;Last: %d\n&quot;, lamps[count - 1]);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm3', 'lesson' => 'lesson01']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What makes an array a <em>structured</em> data type?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It can only ever hold text</li>
            <li class="quiz-option">B. It is always a global variable</li>
            <li class="quiz-option">C. It holds exactly one value</li>
            <li class="quiz-option quiz-correct">D. It groups many values under one name ✓</li>
        </ul>
        <p class="quiz-explanation"><em>A simple type holds one value; an array holds a whole row of same-type values under a single name.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In <code>int lamps[6];</code>, what is the index of the last element?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. 5 ✓</li>
            <li class="quiz-option">B. 6</li>
            <li class="quiz-option">C. 1</li>
            <li class="quiz-option">D. 7</li>
        </ul>
        <p class="quiz-explanation"><em>Indexes start at 0, so six elements are numbered 0 to 5.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does <code>int a[4] = {1, 2};</code> leave in <code>a[3]</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 1</li>
            <li class="quiz-option">B. 2</li>
            <li class="quiz-option quiz-correct">C. 0 ✓</li>
            <li class="quiz-option">D. A random number</li>
        </ul>
        <p class="quiz-explanation"><em>When you give fewer values than the size, the remaining elements are set to 0.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does <code>sizeof(lamps) / sizeof(lamps[0])</code> work out?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The total of all the values</li>
            <li class="quiz-option">B. The biggest value in it</li>
            <li class="quiz-option quiz-correct">C. The number of elements ✓</li>
            <li class="quiz-option">D. The address of the array</li>
        </ul>
        <p class="quiz-explanation"><em>The whole array's bytes divided by one element's bytes is the number of elements.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. How big is the array made by <code>int a[] = {7, 8, 9};</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 0 elements</li>
            <li class="quiz-option quiz-correct">B. 3 elements ✓</li>
            <li class="quiz-option">C. 1 element</li>
            <li class="quiz-option">D. 9 elements</li>
        </ul>
        <p class="quiz-explanation"><em>With the size left out, C counts the values in the braces and sizes the array to fit: 3.</em></p>
    </div>

</div>
