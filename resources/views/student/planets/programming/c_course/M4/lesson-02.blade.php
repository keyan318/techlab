{{-- M4 · Lesson 4.2: Checking Each Emitter — content fragment.
Syllabus: CC102 Unit 4 — Arrays (array access and manipulation). --}}

<div class="lesson-fragment" data-module="m4" data-lesson="02">

    <h1 class="lesson-heading">Lesson 4.2: Checking Each Emitter</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can use a <code>for</code> loop to visit every element of an array in
        order, using the loop's counter as the array's index.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Writing out <code>emitters[0]</code>, <code>emitters[1]</code>, <code>emitters[2]</code>... by hand
        doesn't scale past a handful of elements. Instead, use the loop's counter itself as the index:
    </p>

    <pre class="code-block"><code>for (int i = 0; i &lt; 5; i++) {
    printf("%d\n", emitters[i]);
}</code></pre>

    <p class="body-text">
        On the first pass, <code>i</code> is 0, so <code>emitters[i]</code> means <code>emitters[0]</code>. On
        the next, <code>i</code> is 1, so it means <code>emitters[1]</code> — and so on, all the way through the
        array, one index per iteration. This pattern — <strong>traversing</strong> an array — is one of the
        most common things you'll write in C.
    </p>

    <p class="body-text">
        The loop's condition (<code>i &lt; 5</code>) must match the array's real size, or you'll either miss
        elements or read past the end of the array into memory that isn't yours — C does not stop you from
        doing this, and the result is undefined.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        The storm didn't damage the panels evenly. Rivet checked two of them, said it looked fine, and went to
        get coffee. Volt is still standing at the panel.
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "You checked two."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "The first and the last! If the ends are fine, the middle's—"
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Check all of them."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> puts the coffee down. "…All of them. Fine. Actually, that's easier anyway — I
        don't have to name each one. I just walk the row: start at the first, do the same thing, step to the
        next, stop at the end. One line of loop, however many panels there are."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A for loop's counter i walks through each index of the array in turn">
            @php $vals = [80, 45, 90, 30, 100]; @endphp
            @foreach ($vals as $i => $v)
                <rect x="{{ 20 + $i * 115 }}" y="30" width="100" height="55" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
                <text x="{{ 70 + $i * 115 }}" y="58" text-anchor="middle" font-size="15" font-weight="700" fill="#4a35b8">{{ $v }}</text>
                <text x="{{ 70 + $i * 115 }}" y="98" text-anchor="middle" font-size="12" fill="#64748b">i = {{ $i }}</text>
            @endforeach
            <text x="320" y="140" text-anchor="middle" font-size="13" fill="#475569">for (int i = 0; i &lt; 5; i++) { printf("%d\n", emitters[i]); }</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int emitters[5] = {80, 45, 90, 30, 100};

    for (int i = 0; i &lt; 5; i++) {
        printf("Emitter %d: %d\n", i, emitters[i]);
    }
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Declare <code>int bays[4] = {12, 45, 78, 20};</code>, then use a <code>for</code> loop to print every
        bay's index and value. Expected output:
    </p>

    <pre class="code-block"><code>Bay 0: 12
Bay 1: 45
Bay 2: 78
Bay 3: 20</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Checking Each Emitter">
            <input type="hidden" name="filename"     value="bay_sweep.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int bays[4] = {12, 45, 78, 20};

    // for loop from i = 0 to i &lt; 4, printing &quot;Bay i: bays[i]&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="bays holds {12, 45, 78, 20} — 4 elements. Write a for loop with i from 0 up to (not including) 4, printing each index and value:

Bay 0: 12
Bay 1: 45
Bay 2: 78
Bay 3: 20">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="for (int i = 0; i &lt; 4; i++) { printf(&quot;Bay %d: %d\n&quot;, i, bays[i]); } — the loop's condition (4) must match the array's real size, not one more or one less.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int bays[4] = {12, 45, 78, 20};

    for (int i = 0; i &lt; 4; i++) {
        printf(&quot;Bay %d: %d\n&quot;, i, bays[i]);
    }
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm4', 'lesson' => 'lesson02']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In <code>for (int i = 0; i &lt; 5; i++) { printf("%d\n", emitters[i]); }</code>, what role does i play?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It's unrelated to the array — just a loop counter</li>
            <li class="quiz-option quiz-correct">B. It's used directly as the array's index each time through the loop ✓</li>
            <li class="quiz-option">C. It holds the array's total size</li>
            <li class="quiz-option">D. It holds the value being printed</li>
        </ul>
        <p class="quiz-explanation"><em>The loop's counter i is plugged straight into emitters[i], so each pass through the loop reaches a different element.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why must the loop's condition match the array's real size?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It doesn't matter — C automatically stops at the array's real end</li>
            <li class="quiz-option quiz-correct">B. A mismatched condition can miss elements or read past the array into memory that isn't yours ✓</li>
            <li class="quiz-option">C. C refuses to compile a for loop with the wrong condition</li>
            <li class="quiz-option">D. Arrays automatically resize to match any loop condition</li>
        </ul>
        <p class="quiz-explanation"><em>C does not check array bounds for you — a loop condition larger than the array's size reads past its end, with undefined results.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is "traversing" an array?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Deleting every element in it</li>
            <li class="quiz-option quiz-correct">B. Visiting every element in order, typically with a loop ✓</li>
            <li class="quiz-option">C. Declaring it for the first time</li>
            <li class="quiz-option">D. Sorting its elements from smallest to largest</li>
        </ul>
        <p class="quiz-explanation"><em>Traversing an array means visiting each of its elements in order — exactly what a for loop with an index does.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. If bays has 4 elements and the loop's condition is written as <code>i &lt;= 4</code> instead of <code>i &lt; 4</code>, what happens?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing — the two conditions behave identically here</li>
            <li class="quiz-option quiz-correct">B. The loop tries to read bays[4], one past the array's last valid index (3) ✓</li>
            <li class="quiz-option">C. The array automatically grows to fit</li>
            <li class="quiz-option">D. The compiler catches and fixes the mistake</li>
        </ul>
        <p class="quiz-explanation"><em>With 4 elements, valid indices are 0-3 — i &lt;= 4 lets the loop reach index 4, which is one past the array's actual end.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. According to Astro, what happens if the array's size changes later?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The whole loop must be rewritten from scratch</li>
            <li class="quiz-option quiz-correct">B. The same loop pattern still works, as long as its condition is updated to match ✓</li>
            <li class="quiz-option">C. Traversal loops only ever work for exactly 5 elements</li>
            <li class="quiz-option">D. The loop keeps using the old size automatically, causing no issues</li>
        </ul>
        <p class="quiz-explanation"><em>The traversal pattern itself doesn't change — only the condition's number needs to track the array's actual size.</em></p>
    </div>

</div>
