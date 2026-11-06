{{-- M2 · Lesson 2.2: Reaching In from the Depot — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Pointers — pointer variables and operators (& and *). --}}

<div class="lesson-fragment" data-module="m2" data-lesson="02">

    <h1 class="lesson-heading">Lesson 2.2: Reaching In from the Depot</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can use the <code>*</code> operator to read <em>and change</em> a variable through a pointer, and explain the two different jobs <code>*</code> does in C.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Pointers have two operators, and they are opposites:
    </p>

    <ul class="body-list">
        <li><code>&amp;x</code> ("address of"): gives the address where <code>x</code> lives.</li>
        <li><code>*p</code> ("value at"): goes to the address in <code>p</code> and uses what is there. This is called <strong>dereferencing</strong> the pointer.</li>
    </ul>

    <p class="body-text">
        Dereferencing works on the left of <code>=</code> too. That lets you <em>change</em> a variable without using its name, only its address:
    </p>

    <pre class="code-block"><code>int light = 0;
int *signal = &amp;light;
*signal = 2;        // light is now 2</code></pre>

    <p class="body-text">
        Watch out: <code>*</code> means two different things. In a declaration, <code>int *signal</code> says "signal is a pointer". In an expression, <code>*signal</code> says "the value at signal". Same symbol, different job.
    </p>

    <p class="body-text">
        A pointer has to point somewhere real before you dereference it. A pointer you declared but never gave an address holds a random number, and writing through it can break your program.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        The traffic light at the big crossing is stuck on red. It has been red, Rivet guesses, for about three years.
    </p>

    <p class="body-text">
        The depot can talk to it. On the depot's screen, the light is just an address, and the value in that box says what colour it shows: 0 for red, 1 for amber, 2 for green.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "We don't walk to every light in this city with a spanner. We change them from here. Go to the address, put a new number in the box."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Make sure it's the right address first. Write into the wrong box and some other machine does something nobody asked for."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="The pointer signal holds the address of light; writing *signal = 2 changes light from 0 to 2">
            <rect x="30" y="40" width="160" height="80" rx="12" fill="#f1f5f9" stroke="#94a3b8" stroke-width="2"/>
            <text x="110" y="77.5" text-anchor="middle" font-size="16" font-weight="700" fill="#334155">signal</text>
            <text x="110" y="94.5" text-anchor="middle" font-size="12" fill="#334155">holds &amp;light</text>
            <rect x="420" y="40" width="190" height="80" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="515" y="76.5" text-anchor="middle" font-size="16" font-weight="700" fill="#7a5a00">light</text>
            <text x="515" y="95.5" text-anchor="middle" font-size="14" fill="#7a5a00">0  becomes  2</text>
            <line x1="190" y1="80" x2="406.0" y2="80.0" stroke="#d4a017" stroke-width="2.5"/>
            <polygon points="416,80 404.0,74.0 404.0,86.0" fill="#d4a017"/>
            <text x="303" y="70" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00" font-family="ui-monospace, monospace">*signal = 2;</text>
            <text x="320" y="160" text-anchor="middle" font-size="13" fill="#475569">Write through the address, and the variable it points at changes.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int pressure = 40;
    int *gauge = &amp;pressure;

    *gauge = *gauge + 10;          // read through it, then write through it
    printf("Pressure: %d\n", pressure);   // Pressure: 50
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Make <code>int light = 0;</code> (red) and a pointer <code>int *signal</code> to it. Print the light's value, then set it to green (2) <em>through the pointer</em>, and print the light again by its own name. Exactly:
    </p>

    <pre class="code-block"><code>Before: 0
After: 2</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Reaching In from the Depot">
            <input type="hidden" name="filename"     value="crossing_light.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int light = 0;     // 0 red, 1 amber, 2 green
    // make a pointer signal that holds the address of light

    printf(&quot;Before: %d\n&quot;, light);

    // set the light to 2 through the pointer, not by writing light = 2

    printf(&quot;After: %d\n&quot;, light);
    return 0;
}
">
            <input type="hidden" name="instructions" value="The crossing light is stuck on red (0). Change it to green (2) from the depot.

1. Make int *signal = &amp;light;
2. Set it through the pointer: *signal = 2;
   (do not write light = 2)

The output must be exactly:
Before: 0
After: 2">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Two new lines. int *signal = &amp;light; goes right after light is made. *signal = 2; goes between the two printfs. The second printf uses light, and it prints 2, because you changed the box light lives in.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int light = 0;
    int *signal = &amp;light;

    printf(&quot;Before: %d\n&quot;, light);
    *signal = 2;
    printf(&quot;After: %d\n&quot;, light);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm2', 'lesson' => 'lesson02']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does dereferencing a pointer mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Deleting the pointer for good</li>
            <li class="quiz-option">B. Printing the address it holds</li>
            <li class="quiz-option">C. Making a copy of the pointer</li>
            <li class="quiz-option quiz-correct">D. Using the value at its address ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Dereferencing (*p) follows the address in p and reads or writes the value that lives there.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. After <code>int light = 0; int *signal = &amp;light; *signal = 2;</code>, what is <code>light</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 0</li>
            <li class="quiz-option">B. 1</li>
            <li class="quiz-option quiz-correct">C. 2 ✓</li>
            <li class="quiz-option">D. An address</li>
        </ul>
        <p class="quiz-explanation"><em>*signal = 2 writes 2 into the box at light's address, so light itself is now 2.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. In <code>int *signal;</code>, what does the <code>*</code> mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Multiply</li>
            <li class="quiz-option">B. The value at signal</li>
            <li class="quiz-option quiz-correct">C. signal is a pointer ✓</li>
            <li class="quiz-option">D. signal is a global</li>
        </ul>
        <p class="quiz-explanation"><em>In a declaration, * marks the variable as a pointer; it is only "value at" when used in an expression.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Which pair are opposites?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. <code>&amp;</code> and <code>*</code> ✓</li>
            <li class="quiz-option">B. <code>+</code> and <code>*</code></li>
            <li class="quiz-option">C. <code>=</code> and <code>==</code></li>
            <li class="quiz-option">D. <code>%d</code> and <code>%p</code></li>
        </ul>
        <p class="quiz-explanation"><em>& turns a variable into its address; * turns an address back into the value living there.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why is it dangerous to write through a pointer that was never given an address?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It isn't; C sets it to a safe address</li>
            <li class="quiz-option quiz-correct">B. The write can land in a random place ✓</li>
            <li class="quiz-option">C. The compiler refuses to build the file</li>
            <li class="quiz-option">D. It only makes the program run slower</li>
        </ul>
        <p class="quiz-explanation"><em>An unset pointer holds whatever number was in its box; writing there can corrupt memory you never meant to touch.</em></p>
    </div>

</div>
