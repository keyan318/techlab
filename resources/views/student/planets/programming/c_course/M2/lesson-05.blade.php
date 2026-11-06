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

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">A <code>for</code> loop is really which other loop, with its bookkeeping gathered in one line?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A <code>do-while</code> loop</li>
            <li class="quiz-option">B. A <code>switch</code></li>
            <li class="quiz-option quiz-correct">C. A <code>while</code> loop ✓</li>
            <li class="quiz-option">D. An <code>if</code> statement</li>
        </ul>
        <p class="quiz-explanation"><em>Start, condition and step are the same parts a <code>while</code> loop needs, just written together.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        A <code>for</code> loop is <strong>running laps on a track</strong> with a lap counter in your hand. Before you start, the counter is set to lap 1. Before each lap you check: have I done all 4 yet? After each lap, you click the counter <strong>+1</strong>.
    </p>

    <p class="body-text">
        Those are the three parts of a <code>for</code> loop, all in one line: the <strong>start</strong> (<code>int lap = 1</code>), the <strong>keep going?</strong> check (<code>lap &lt;= 4</code>), and the <strong>step</strong> after every lap (<code>lap++</code>). Counting down works the same way, just clicking −1 instead.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 335" role="img" aria-label="A running track with a start line, a runner and a lap counter showing lap 2. Start line: int lap = 1, once, before the first lap. Keep running: lap less than or equal to 4, checked before every lap. Click plus 1: lap plus plus, after every lap. Together: for, int lap = 1, lap less than or equal to 4, lap plus plus.">
            <ellipse cx="175" cy="150" rx="150" ry="105" fill="#ffe3ec" stroke="#e8457c" stroke-width="2"/><ellipse cx="175" cy="150" rx="105" ry="62" fill="#fff" stroke="#e8457c" stroke-width="2"/><ellipse cx="175" cy="150" rx="128" ry="84" fill="none" stroke="#fff" stroke-width="2" stroke-dasharray="10,8"/><rect x="168" y="212.0" width="7" height="5.4" fill="#fff"/><rect x="175" y="212.0" width="7" height="5.4" fill="#1b2440"/><rect x="168" y="217.4" width="7" height="5.4" fill="#1b2440"/><rect x="175" y="217.4" width="7" height="5.4" fill="#fff"/><rect x="168" y="222.8" width="7" height="5.4" fill="#fff"/><rect x="175" y="222.8" width="7" height="5.4" fill="#1b2440"/><rect x="168" y="228.2" width="7" height="5.4" fill="#1b2440"/><rect x="175" y="228.2" width="7" height="5.4" fill="#fff"/><rect x="168" y="233.6" width="7" height="5.4" fill="#fff"/><rect x="175" y="233.6" width="7" height="5.4" fill="#1b2440"/><rect x="168" y="239.0" width="7" height="5.4" fill="#1b2440"/><rect x="175" y="239.0" width="7" height="5.4" fill="#fff"/><rect x="168" y="244.4" width="7" height="5.4" fill="#fff"/><rect x="175" y="244.4" width="7" height="5.4" fill="#1b2440"/><rect x="168" y="249.8" width="7" height="5.4" fill="#1b2440"/><rect x="175" y="249.8" width="7" height="5.4" fill="#fff"/><text x="175" y="278" font-size="11" fill="#9c1f4c" font-weight="800" text-anchor="middle" letter-spacing="1">START / FINISH</text><circle cx="47" cy="150" r="10" fill="#2f6fe0" stroke="#fff" stroke-width="2"/><path d="M40 178 Q30 150 40 122" fill="none" stroke="#2f6fe0" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><polygon points="40,120 31.3,126.4 42.6,130.5" fill="#2f6fe0"/><rect x="125" y="118" width="100" height="62" rx="12" fill="#14306b"/><circle cx="175" cy="112" r="7" fill="#e8457c"/><rect x="137" y="130" width="76" height="38" rx="6" fill="#0b1a3d"/><text x="175" y="156" font-size="16" fill="#86e0b0" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">LAP 2</text><rect x="352" y="40" width="268" height="66" rx="12" fill="#f8f6ff" stroke="#7c5cff" stroke-width="2"/><text x="368" y="66" font-size="15" fill="#4a35b8" font-weight="800">Start line</text><text x="368" y="88" font-size="12" fill="#5b6b86">once, before the first lap</text><text x="604" y="67" font-size="14" fill="#4a35b8" font-weight="800" text-anchor="end" font-family="ui-monospace, Menlo, Consolas, monospace">int lap = 1</text><rect x="352" y="118" width="268" height="66" rx="12" fill="#f2fbfc" stroke="#0fa3b1" stroke-width="2"/><text x="368" y="144" font-size="15" fill="#0b6670" font-weight="800">Keep running?</text><text x="368" y="166" font-size="12" fill="#5b6b86">checked before every lap</text><text x="604" y="145" font-size="14" fill="#0b6670" font-weight="800" text-anchor="end" font-family="ui-monospace, Menlo, Consolas, monospace">lap &lt;= 4</text><rect x="352" y="196" width="268" height="66" rx="12" fill="#f3fbf6" stroke="#2fa56b" stroke-width="2"/><text x="368" y="222" font-size="15" fill="#1d6b43" font-weight="800">Click +1</text><text x="368" y="244" font-size="12" fill="#5b6b86">after every lap</text><text x="604" y="223" font-size="14" fill="#1d6b43" font-weight="800" text-anchor="end" font-family="ui-monospace, Menlo, Consolas, monospace">lap++</text><text x="320" y="318" font-size="16" fill="#1b2440" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">for (<tspan fill="#4a35b8">int lap = 1</tspan>; <tspan fill="#0b6670">lap &lt;= 4</tspan>; <tspan fill="#1d6b43">lap++</tspan>)</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">In <code>for (int lap = 1; lap &lt;= 4; lap++)</code>, when does <code>lap++</code> happen?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Once, before the very first lap starts</li>
            <li class="quiz-option">B. Only when lap finally reaches 4</li>
            <li class="quiz-option">C. Before the condition is ever checked</li>
            <li class="quiz-option quiz-correct">D. After each lap, like clicking +1 ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The step runs at the end of every pass through the loop body, then the condition is checked again.</em></p>
    </div>

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

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">What is the first line the code example prints?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. T-minus 5 ✓</li>
            <li class="quiz-option">B. T-minus 1</li>
            <li class="quiz-option">C. T-minus 0</li>
            <li class="quiz-option">D. Contact</li>
        </ul>
        <p class="quiz-explanation"><em><code>t</code> starts at 5, so the first pass prints “T-minus 5”, then counts down.</em></p>
    </div>

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
            <li class="quiz-option">D. printf(...)</li>
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
        <p class="quiz-prompt">Q4. What change turns a counting-down for loop into a counting-up one?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Start high, check with &gt;=, and step with ++</li>
            <li class="quiz-option">B. Start low, check with &gt;=, and step with --</li>
            <li class="quiz-option">C. Keep the start, drop the condition, step with ++</li>
            <li class="quiz-option quiz-correct">D. Start low, check with &lt;=, and step with ++ ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The same three-part structure works for counting up: just start low, check with &lt;=, and increment instead of decrement.</em></p>
    </div>

</div>
