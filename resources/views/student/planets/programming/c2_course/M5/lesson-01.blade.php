{{-- M5 · Lesson 5.1: Memory on Request — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Dynamic Variables — main memory management; creating dynamic variables (malloc, free). --}}

<div class="lesson-fragment" data-module="m5" data-lesson="01">

    <h1 class="lesson-heading">Lesson 5.1: Memory on Request</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain the difference between automatic and dynamic memory, ask for memory with <code>malloc</code>, check that you got it, and give it back with <code>free</code>.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Every variable so far was made for you. A local variable is created when its function starts and destroyed when it ends. That memory area is called the <strong>stack</strong>, and the size of everything on it is fixed when you write the code.
    </p>

    <p class="body-text">
        A <strong>dynamic variable</strong> is different: your program asks for it <em>while it runs</em>, from a big pool called the <strong>heap</strong>, and it lasts until your program gives it back. The tools are in <code>&lt;stdlib.h&gt;</code>:
    </p>

    <pre class="code-block"><code>int *spare = (int *) malloc(sizeof(int));   // ask for room for one int</code></pre>

    <ul class="body-list">
        <li><code>malloc(bytes)</code> reserves that many bytes and returns their address. <code>sizeof(int)</code> gives the right number of bytes.</li>
        <li>The memory has no name. You only reach it through the pointer, so <code>*spare = 12;</code>.</li>
        <li>If there is no room left, <code>malloc</code> returns <code>NULL</code>. Always check.</li>
    </ul>

    <pre class="code-block"><code>if (spare == NULL) {
    printf("No room\n");
    return 1;
}
*spare = 12;
free(spare);        // give it back
spare = NULL;       // the address is no good now</code></pre>

    <p class="body-text">
        <code>free</code> returns the memory to the heap. Two mistakes to avoid: forgetting to free (a <strong>memory leak</strong>: the program slowly uses up memory), and using a pointer after you freed it (a <strong>dangling pointer</strong>: the box may now belong to something else). Setting the pointer to <code>NULL</code> after <code>free</code> guards against the second one.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Tram 7's depot sits on the far side of the market: a long shed full of empty track and a pile of parts. There are no spare cars waiting. Whoever ran it built cars only when the line needed one.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> is walking the empty rails with his arms out. "This is how you do it! You don't keep a hundred cars just in case. You ask the yard for one when you need it, and when you're done, you take it apart and give the parts back."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And when the yard's out of parts?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Then it says so. It hands you an empty address, a <code>NULL</code>, instead of a car. Which is why you look at what it gave you before you climb aboard."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="The stack holds fixed local variables like spare; malloc reserves a box on the heap and spare points at it; free gives the box back">
            <rect x="20" y="20" width="220" height="150" rx="12" fill="#f1f5f9" stroke="#94a3b8" stroke-width="2"/>
            <text x="130" y="100" text-anchor="middle" font-size="14" font-weight="700" fill="#334155">stack</text>
            <rect x="45" y="80" width="170" height="50" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="130" y="101.5" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">spare</text>
            <text x="130" y="118.5" text-anchor="middle" font-size="12" fill="#14306b">(a pointer)</text>
            <rect x="400" y="20" width="220" height="150" rx="12" fill="#f1f5f9" stroke="#94a3b8" stroke-width="2"/>
            <text x="510" y="100" text-anchor="middle" font-size="14" font-weight="700" fill="#334155">heap</text>
            <rect x="445" y="80" width="130" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="510" y="103" text-anchor="middle" font-size="16" font-weight="700" fill="#7a5a00">12</text>
            <text x="510" y="119" text-anchor="middle" font-size="11" fill="#7a5a00">no name</text>
            <line x1="215" y1="105" x2="431.0" y2="105.0" stroke="#d4a017" stroke-width="2.5"/>
            <polygon points="441,105 429.0,99.0 429.0,111.0" fill="#d4a017"/>
            <text x="328" y="95" text-anchor="middle" font-size="12" font-weight="700" fill="#7a5a00" font-family="ui-monospace, monospace">malloc(sizeof(int))</text>
            <text x="320" y="200" text-anchor="middle" font-size="12" fill="#475569">free(spare) hands the heap box back; spare = NULL forgets the old address</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;
#include &lt;stdlib.h&gt;

int main() {
    double *reading = (double *) malloc(sizeof(double));
    if (reading == NULL) {
        return 1;
    }

    *reading = 4.5;
    printf("Reading: %.1f\n", *reading);

    free(reading);
    reading = NULL;
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. Ask <code>malloc</code> for one <code>int</code> and keep its address in <code>spare</code>. If it is <code>NULL</code>, print <code>No room</code> and return 1. Otherwise store 12 in it, print it, <code>free</code> it, set the pointer to <code>NULL</code>, and print the last line. Exactly:
    </p>

    <pre class="code-block"><code>Spare part: 12
Part returned.</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Memory on Request">
            <input type="hidden" name="filename"     value="tram_yard.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;
#include &lt;stdlib.h&gt;

int main() {
    // 1. int *spare = (int *) malloc(sizeof(int));

    // 2. if spare is NULL: print &quot;No room&quot; and return 1

    // 3. store 12 through the pointer and print &quot;Spare part: &lt;value&gt;&quot;

    // 4. free it, set spare to NULL, print &quot;Part returned.&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="Ask the yard for one part, use it, give it back.

1. int *spare = (int *) malloc(sizeof(int));
2. if (spare == NULL) { print No room and return 1; }
3. *spare = 12; then print it
4. free(spare); spare = NULL; then print Part returned.

The output must be exactly:
Spare part: 12
Part returned.">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="The memory has no name of its own, so every use goes through the pointer: *spare = 12; and printf(&quot;Spare part: %d\n&quot;, *spare); Check for NULL straight after malloc, before the first *. free takes the pointer itself: free(spare);">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;
#include &lt;stdlib.h&gt;

int main() {
    int *spare = (int *) malloc(sizeof(int));
    if (spare == NULL) {
        printf(&quot;No room\n&quot;);
        return 1;
    }

    *spare = 12;
    printf(&quot;Spare part: %d\n&quot;, *spare);

    free(spare);
    spare = NULL;
    printf(&quot;Part returned.\n&quot;);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm5', 'lesson' => 'lesson01']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is a dynamic variable?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A variable whose value keeps changing</li>
            <li class="quiz-option">B. A global that every function shares</li>
            <li class="quiz-option">C. A variable declared inside a loop</li>
            <li class="quiz-option quiz-correct">D. Memory asked for while it runs ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Dynamic memory is requested at run time with malloc and lasts until it is freed.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does <code>malloc</code> return when there is no memory left?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 0 bytes of memory</li>
            <li class="quiz-option quiz-correct">B. NULL ✓</li>
            <li class="quiz-option">C. A random address</li>
            <li class="quiz-option">D. It stops the program</li>
        </ul>
        <p class="quiz-explanation"><em>malloc signals failure by returning NULL, which is why you check before using the pointer.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why write <code>malloc(sizeof(int))</code> instead of <code>malloc(4)</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. malloc only accepts sizeof</li>
            <li class="quiz-option">B. It makes malloc run faster</li>
            <li class="quiz-option quiz-correct">C. An int isn't 4 bytes everywhere ✓</li>
            <li class="quiz-option">D. 4 bytes is too big for an int</li>
        </ul>
        <p class="quiz-explanation"><em>An int is not 4 bytes everywhere; sizeof always gives the correct size.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What is a memory leak?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Memory malloc'd but never freed ✓</li>
            <li class="quiz-option">B. Memory freed twice by mistake</li>
            <li class="quiz-option">C. A pointer that was set to NULL</li>
            <li class="quiz-option">D. Reading past an array's end</li>
        </ul>
        <p class="quiz-explanation"><em>Memory that is never freed stays reserved, so a long-running program slowly runs out.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why set a pointer to NULL right after <code>free</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. free won't work unless you do</li>
            <li class="quiz-option quiz-correct">B. So the old address isn't reused ✓</li>
            <li class="quiz-option">C. It gives the memory back to C</li>
            <li class="quiz-option">D. It makes the program run faster</li>
        </ul>
        <p class="quiz-explanation"><em>After free the address is dangling; NULL makes any later check catch it instead of touching freed memory.</em></p>
    </div>

</div>
