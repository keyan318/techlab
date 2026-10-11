{{-- M2 · Lesson 2.5: Counting Down to Contact — content fragment.
Syllabus: CC102 Unit 2 — Looping Statements (for). --}}

<div class="lesson-fragment" data-module="m2" data-lesson="05">

    <h1 class="lesson-heading">Lesson 2.5: Counting Down to Contact</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write a <code>for</code> loop, naming its start, condition and step
        in one line, and use it to count up or down a known number of times.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <code>for</code> loop packs everything a repeated count needs into one line: where it starts, when it
        stops, and how it changes each time.
    </p>

    <pre class="code-block"><code>for (start; condition; step) {
    // repeats while condition is true
}</code></pre>

    <ul class="body-list">
        <li><strong>start</strong>: runs once, before the loop begins — usually declares a counter, like <code>int i = 0</code>.</li>
        <li><strong>condition</strong>: checked before every run, exactly like a while loop.</li>
        <li><strong>step</strong>: runs after every loop body, usually <code>i++</code> or <code>i--</code>.</li>
    </ul>

    <p class="body-text">
        A <code>for</code> loop is really a <code>while</code> loop with its bookkeeping gathered in one place —
        which is exactly why it's the natural choice whenever you already know how many times to repeat.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Morning. The cells are full, the shell is holding, and Rivet has dragged the ship's old distress beacon
        out of storage. It needs a proper countdown before it fires — not one tick more, not one less.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Three. Two. One. Contact. Every time, exactly the same."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Then this is the easy kind. Last night we didn't know how many rounds it'd take,
        so we just watched and waited. This time I know the number before I start — so I say it once, up front:
        start at three, stop at one, take one off each go."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And it counts backwards?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Down, up, sideways — it doesn't care. You just tell it which way to step."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 150" role="img" aria-label="A for loop's three parts: start once, check the condition, run the step each time">
            <rect x="20" y="20" width="170" height="65" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="105" y="48" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">int t = 5;</text>
            <text x="105" y="68" text-anchor="middle" font-size="11" fill="#4a35b8">runs once, first</text>

            <rect x="235" y="20" width="170" height="65" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="320" y="48" text-anchor="middle" font-size="13" font-weight="700" fill="#14663f">t &gt;= 1;</text>
            <text x="320" y="68" text-anchor="middle" font-size="11" fill="#14663f">checked every time</text>

            <rect x="450" y="20" width="170" height="65" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="535" y="48" text-anchor="middle" font-size="13" font-weight="700" fill="#8a4b00">t--</text>
            <text x="535" y="68" text-anchor="middle" font-size="11" fill="#8a4b00">runs after each body</text>

            <line x1="190" y1="52" x2="230" y2="52" stroke="#7c5cff" stroke-width="2.5"/>
            <line x1="405" y1="52" x2="445" y2="52" stroke="#7c5cff" stroke-width="2.5"/>

            <text x="320" y="125" text-anchor="middle" font-size="13" fill="#475569">for (t = 5; t &gt;= 1; t--) { printf("T-minus %d\n", t); }</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    for (int t = 5; t &gt;= 1; t--) {
        printf("T-minus %d\n", t);
    }
    printf("Contact\n");
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        This transmission only needs a three-tick countdown. Write a <code>for</code> loop that counts down from
        3 to 1, then prints a final line:
    </p>

    <pre class="code-block"><code>T-minus 3
T-minus 2
T-minus 1
Contact</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Counting Down to Contact">
            <input type="hidden" name="filename"     value="countdown.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    // for loop counting down from 3 to 1, printing &quot;T-minus N&quot; each time
    // then print &quot;Contact&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="Write a for loop that starts an int at 3, keeps going while it is at least 1, and subtracts 1 each time. Inside, print &quot;T-minus &quot; followed by the current number. After the loop, print &quot;Contact&quot;.

Expected output:
T-minus 3
T-minus 2
T-minus 1
Contact">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="for (int t = 3; t &gt;= 1; t--) counts 3, 2, 1 and then stops, since t &gt;= 1 becomes false once t reaches 0. Put the &quot;Contact&quot; printf after the loop's closing brace.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    for (int t = 3; t &gt;= 1; t--) {
        printf(&quot;T-minus %d\n&quot;, t);
    }
    printf(&quot;Contact\n&quot;);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm2', 'lesson' => 'lesson05']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In <code>for (int t = 5; t &gt;= 1; t--)</code>, which part runs only once, before the loop starts?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. int t = 5 ✓</li>
            <li class="quiz-option">B. t &gt;= 1</li>
            <li class="quiz-option">C. t--</li>
            <li class="quiz-option">D. None of them — all three run every time</li>
        </ul>
        <p class="quiz-explanation"><em>The start part (int t = 5) runs exactly once, before the first check of the condition — it sets up the counter.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In that same loop, when does t-- run?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Once, before the loop starts</li>
            <li class="quiz-option">B. Only if the loop body prints something</li>
            <li class="quiz-option quiz-correct">C. After every run of the loop's body ✓</li>
            <li class="quiz-option">D. Before the condition is checked for the first time</li>
        </ul>
        <p class="quiz-explanation"><em>The step (t--) runs after each iteration of the body, then the condition is checked again before deciding whether to repeat.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. How many times does <code>for (int t = 5; t &gt;= 1; t--) { printf("T-minus %d\n", t); }</code> print a line?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 4</li>
            <li class="quiz-option quiz-correct">B. 5 ✓</li>
            <li class="quiz-option">C. 6</li>
            <li class="quiz-option">D. Forever — it never stops</li>
        </ul>
        <p class="quiz-explanation"><em>t runs through 5, 4, 3, 2, 1 — five values, each satisfying t &gt;= 1 — before t-- makes it 0 and the loop stops.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why is a for loop a good fit for this lesson's countdown, according to Astro?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. for loops are the only kind of loop that can count down</li>
            <li class="quiz-option quiz-correct">B. The exact number of repeats is already known in advance ✓</li>
            <li class="quiz-option">C. for loops never need a condition</li>
            <li class="quiz-option">D. It is not a good fit — a while loop would be simpler here</li>
        </ul>
        <p class="quiz-explanation"><em>A for loop groups the start, condition and step together, which is most natural exactly when you already know the number of repeats — like a five-count countdown.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What change turns a counting-down for loop into a counting-up one?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It cannot be done — for loops can only count down</li>
            <li class="quiz-option quiz-correct">B. Start at the low number, use &lt;= for the condition, and ++ instead of -- for the step ✓</li>
            <li class="quiz-option">C. Remove the condition entirely</li>
            <li class="quiz-option">D. Replace for with while — for cannot count up</li>
        </ul>
        <p class="quiz-explanation"><em>The same three-part structure works for counting up: just start low, check with &lt;=, and increment instead of decrement.</em></p>
    </div>

</div>
