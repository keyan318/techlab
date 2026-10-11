{{-- M4 · Lesson 4.3: Patching the Grid — content fragment.
Syllabus: CC102 Unit 4 — Arrays (array access and manipulation). --}}

<div class="lesson-fragment" data-module="m4" data-lesson="03">

    <h1 class="lesson-heading">Lesson 4.3: Patching the Grid</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can combine a traversal loop with an accumulator variable to compute a
        single result — like a total — from every element of an array.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A traversal loop doesn't just have to print — it can build up a result as it goes. An
        <strong>accumulator</strong> is a variable declared <em>before</em> the loop, starting at a known value,
        that the loop updates on every pass:
    </p>

    <pre class="code-block"><code>int total = 0;
for (int i = 0; i &lt; 5; i++) {
    total = total + emitters[i];
}</code></pre>

    <p class="body-text">
        <code>total</code> starts at 0. Each time through the loop, it's reassigned to itself plus the current
        element — so after all five passes, it holds the sum of every element in the array. The accumulator
        must be declared <em>outside</em> the loop, or it would reset to 0 on every single pass instead of
        building up.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Volt was right — more than one panel took damage. Now Astro has to decide whether to spend the last of
        the salvage on them, and he wants one number, not four.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Don't read me a list. Tell me what the whole repair costs."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Same walk down the row as yesterday. Only this time I carry a running total
        with me." He mimes it, hand moving along the bench. "Start at nothing. Add each one as I pass it. By the
        end of the row, I'm holding the answer."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And you start it at nothing every time?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Start it <em>before</em> the walk. Start it inside the walk and it resets at
        every panel and you end up with the last number instead of the total. Ask me how I know."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 190" role="img" aria-label="total starts at 0 and accumulates each array element as the loop runs">
            <rect x="20" y="20" width="130" height="50" rx="10" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="85" y="50" text-anchor="middle" font-size="14" font-weight="700" fill="#8a4b00">total = 0</text>

            @php $vals = [80, 45, 90, 30, 100]; $running = 0; @endphp
            @foreach ($vals as $i => $v)
                @php $running += $v; @endphp
                <rect x="{{ 20 + $i * 115 }}" y="100" width="100" height="50" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
                <text x="{{ 70 + $i * 115 }}" y="122" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">+{{ $v }}</text>
                <text x="{{ 70 + $i * 115 }}" y="140" text-anchor="middle" font-size="11" fill="#64748b">total={{ $running }}</text>
            @endforeach

            <line x1="85" y1="70" x2="85" y2="96" stroke="#7c5cff" stroke-width="2.5"/>

            <text x="320" y="180" text-anchor="middle" font-size="13" fill="#475569">After the loop, total holds 345 — the sum of all five emitters.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int emitters[5] = {80, 45, 90, 30, 100};
    int total = 0;

    for (int i = 0; i &lt; 5; i++) {
        total = total + emitters[i];
    }
    printf("Total output: %d\n", total);
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Declare <code>int costs[4] = {12, 45, 78, 20};</code> and an accumulator <code>total</code> starting at
        0. Loop through <code>costs</code>, adding each value into <code>total</code>, then print exactly:
    </p>

    <pre class="code-block"><code>Repair total: 155</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Patching the Grid">
            <input type="hidden" name="filename"     value="repair_total.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int costs[4] = {12, 45, 78, 20};
    int total = 0;

    // loop through costs, adding each value into total

    // print &quot;Repair total: &quot; + total

    return 0;
}
">
            <input type="hidden" name="instructions" value="costs holds {12, 45, 78, 20}. total already starts at 0.

Write a for loop that adds every element of costs into total (total = total + costs[i];), then print:
Repair total: 155">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Inside the loop: total = total + costs[i];. total must be declared before (outside) the loop, or it would reset to 0 every single pass instead of building up.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int costs[4] = {12, 45, 78, 20};
    int total = 0;

    for (int i = 0; i &lt; 4; i++) {
        total = total + costs[i];
    }
    printf(&quot;Repair total: %d\n&quot;, total);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm4', 'lesson' => 'lesson03']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is an accumulator?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Another name for an array</li>
            <li class="quiz-option quiz-correct">B. A variable that builds up a result across repeated passes of a loop ✓</li>
            <li class="quiz-option">C. A type of function</li>
            <li class="quiz-option">D. The loop's condition</li>
        </ul>
        <p class="quiz-explanation"><em>An accumulator starts at a known value and is updated on every loop pass, gradually building toward a final result like a sum.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Where must an accumulator like total be declared?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Inside the loop's body, so it resets every pass</li>
            <li class="quiz-option quiz-correct">B. Outside (before) the loop, so it keeps its value across every pass ✓</li>
            <li class="quiz-option">C. Inside the array's brackets</li>
            <li class="quiz-option">D. It doesn't matter where it's declared</li>
        </ul>
        <p class="quiz-explanation"><em>Declaring total outside the loop means it's set to 0 only once — if it were inside the loop, it would reset to 0 every single pass instead of accumulating.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does <code>total = total + emitters[i];</code> mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. total is compared to emitters[i]</li>
            <li class="quiz-option quiz-correct">B. total is reassigned to its own old value plus the current element ✓</li>
            <li class="quiz-option">C. emitters[i] is replaced with the value of total</li>
            <li class="quiz-option">D. It causes an infinite loop</li>
        </ul>
        <p class="quiz-explanation"><em>This is a running update: the right side computes with total's current value, and the result becomes total's new value.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Before the loop runs at all, what must total already hold for the sum to come out correct?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The array's last element</li>
            <li class="quiz-option quiz-correct">B. 0 — starting anywhere else would throw off every element added afterward ✓</li>
            <li class="quiz-option">C. The array's size</li>
            <li class="quiz-option">D. It doesn't matter what it starts at</li>
        </ul>
        <p class="quiz-explanation"><em>Since every element gets added onto whatever total already holds, it must start at 0 — any other starting value would shift the final sum.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why did Rivet want one combined total instead of five separate printouts?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Printing five lines is not possible in C</li>
            <li class="quiz-option quiz-correct">B. The decision they needed to make (can the shield hold) depended on one combined number, not five separate readings ✓</li>
            <li class="quiz-option">C. Arrays can only ever be printed as a single sum</li>
            <li class="quiz-option">D. Accumulators are required by C syntax whenever a for loop is used</li>
        </ul>
        <p class="quiz-explanation"><em>The real question — is the total output enough — needs one combined number, which is exactly what an accumulator is for.</em></p>
    </div>

</div>
