{{-- M2 · Lesson 2.3: Swap the Tram Cars — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Pointers — pointers as parameters. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="03">

    <h1 class="lesson-heading">Lesson 2.3: Swap the Tram Cars</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can pass addresses into a function so it changes the caller's variables, and explain why a swap with plain <code>int</code> parameters does nothing.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Remember Lesson 1.2: a function gets <em>copies</em> of its arguments. So this swap does nothing to <code>main</code>'s variables. It swaps its own copies, and the copies vanish:
    </p>

    <pre class="code-block"><code>void swapCopies(int a, int b) {   // does NOT work
    int spare = a;
    a = b;
    b = spare;
}</code></pre>

    <p class="body-text">
        The fix is to pass <strong>addresses</strong> instead. The parameters become pointers, and the function works through them with <code>*</code>, which reaches back into <code>main</code>'s own boxes:
    </p>

    <pre class="code-block"><code>void swap(int *a, int *b) {
    int spare = *a;
    *a = *b;
    *b = spare;
}

swap(&amp;trackA, &amp;trackB);          // pass the addresses</code></pre>

    <p class="body-text">
        This is called <strong>passing by address</strong> (or by pointer). It is how a function in C can change variables that belong to someone else. It is also why <code>scanf</code> needs <code>&amp;</code>: it has to put the number into <em>your</em> variable.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        Two tram cars sit at the junction outside the depot, each one on the other's track. Car 2 is where car 1 should be, and car 1 is where car 2 should be. Neither can move.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> writes a swap job in about ten seconds and runs it. Nothing happens. The screen still says car 2 on track A.
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Your job swapped something. Not the cars, though."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> groans. "It swapped the scraps of paper. It only ever got copies. If I want it to move the real cars, I have to give it the addresses of the real tracks."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 220" role="img" aria-label="main passes the addresses of trackA and trackB to swap, which exchanges the values in main's own boxes">
            <rect x="30" y="25" width="200" height="70" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="130" y="56.5" text-anchor="middle" font-size="15" font-weight="700" fill="#7a5a00">trackA</text>
            <text x="130" y="74.5" text-anchor="middle" font-size="13" fill="#7a5a00">1  becomes  2</text>
            <rect x="30" y="125" width="200" height="70" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="130" y="156.5" text-anchor="middle" font-size="15" font-weight="700" fill="#7a5a00">trackB</text>
            <text x="130" y="174.5" text-anchor="middle" font-size="13" fill="#7a5a00">2  becomes  1</text>
            <rect x="400" y="60" width="210" height="100" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="505" y="98" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">swap(int *a, int *b)</text>
            <text x="505" y="115" text-anchor="middle" font-size="12" fill="#4a35b8">a = &amp;trackA</text>
            <text x="505" y="132" text-anchor="middle" font-size="12" fill="#4a35b8">b = &amp;trackB</text>
            <line x1="396" y1="90" x2="243.8" y2="61.8" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="234,60 244.7,68.1 246.9,56.3" fill="#7c5cff"/>
            <line x1="396" y1="130" x2="243.8" y2="158.2" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="234,160 246.9,163.7 244.7,151.9" fill="#7c5cff"/>
            <text x="320" y="210" text-anchor="middle" font-size="13" fill="#475569">With addresses, swap reaches into main's variables, not copies.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

void addCharge(int *cell, int amount) {
    *cell = *cell + amount;        // changes main's battery
}

int main() {
    int battery = 30;
    addCharge(&amp;battery, 25);
    printf("Battery: %d\n", battery);   // Battery: 55
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. Write <code>void swap(int *a, int *b)</code> that swaps the two values through the pointers. In <code>main</code>, <code>int trackA = 1, trackB = 2;</code>, call <code>swap(&amp;trackA, &amp;trackB);</code> and print exactly:
    </p>

    <pre class="code-block"><code>Track A: 2
Track B: 1</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Swap the Tram Cars">
            <input type="hidden" name="filename"     value="tram_swap.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

// define void swap(int *a, int *b) here: keep *a in a spare variable, then swap

int main() {
    int trackA = 1, trackB = 2;

    // call swap with the ADDRESSES of trackA and trackB

    printf(&quot;Track A: %d\n&quot;, trackA);
    printf(&quot;Track B: %d\n&quot;, trackB);
    return 0;
}
">
            <input type="hidden" name="instructions" value="The two tram cars are on each other's tracks. Swap them for real.

1. Define void swap(int *a, int *b):
   int spare = *a; then *a = *b; then *b = spare;
2. In main, call swap(&amp;trackA, &amp;trackB);

The output must be exactly:
Track A: 2
Track B: 1">
            <input type="hidden" name="hint_title"   value="Volt's Hint">
            <input type="hidden" name="hint_body"    value="Inside swap, every use of a and b needs a * in front, because a and b are addresses. Three lines: int spare = *a; *a = *b; *b = spare; In main, the &amp; is what hands over the addresses: swap(&amp;trackA, &amp;trackB);">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

void swap(int *a, int *b) {
    int spare = *a;
    *a = *b;
    *b = spare;
}

int main() {
    int trackA = 1, trackB = 2;

    swap(&amp;trackA, &amp;trackB);

    printf(&quot;Track A: %d\n&quot;, trackA);
    printf(&quot;Track B: %d\n&quot;, trackB);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm2', 'lesson' => 'lesson03']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why does <code>void swapCopies(int a, int b)</code> fail to swap main's variables?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It has a syntax error inside</li>
            <li class="quiz-option">B. void functions can't change values</li>
            <li class="quiz-option quiz-correct">C. It only swaps its own copies ✓</li>
            <li class="quiz-option">D. It swaps the values back twice</li>
        </ul>
        <p class="quiz-explanation"><em>With plain int parameters, the function works on copies; main's variables never change.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. How do you call <code>void swap(int *a, int *b)</code> on <code>x</code> and <code>y</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>swap(x, y);</code></li>
            <li class="quiz-option">B. <code>swap(*x, *y);</code></li>
            <li class="quiz-option quiz-correct">C. <code>swap(&amp;x, &amp;y);</code> ✓</li>
            <li class="quiz-option">D. <code>swap(int x, int y);</code></li>
        </ul>
        <p class="quiz-explanation"><em>The parameters are pointers, so you pass addresses with &.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Inside <code>swap</code>, what does <code>*a = *b;</code> do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Copies the address in b into a</li>
            <li class="quiz-option quiz-correct">B. Copies the value at b into a's box ✓</li>
            <li class="quiz-option">C. Multiplies the value at a by b</li>
            <li class="quiz-option">D. Checks whether a and b are equal</li>
        </ul>
        <p class="quiz-explanation"><em>Both sides are dereferenced, so the value living at b is written into the box living at a.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why does <code>scanf("%d", &amp;fuel)</code> need the <code>&amp;</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. So scanf prints fuel once it is read</li>
            <li class="quiz-option">B. To turn fuel into a pointer variable</li>
            <li class="quiz-option">C. It isn't needed; the & is optional</li>
            <li class="quiz-option quiz-correct">D. So scanf can write into fuel itself ✓</li>
        </ul>
        <p class="quiz-explanation"><em>scanf has to change your variable, so like swap it needs the variable's address.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is the <code>spare</code> variable for in a swap?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. To save a value before it's overwritten ✓</li>
            <li class="quiz-option">B. To count how many swaps have run</li>
            <li class="quiz-option">C. To return the result to main</li>
            <li class="quiz-option">D. To hold the address of the pointer</li>
        </ul>
        <p class="quiz-explanation"><em>Writing *a = *b first would wipe out a's old value, so it is saved in spare beforehand.</em></p>
    </div>

</div>
