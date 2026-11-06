{{-- M3 · Lesson 3.5: Many Hands — content fragment.
Syllabus: CC102 Unit 3 — Functions (synthesis: definition, calling, parameters, return values, prototypes together). --}}

<div class="lesson-fragment" data-module="m3" data-lesson="05">

    <h1 class="lesson-heading">Lesson 3.5: Many Hands</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write a program made of several functions that call each other and
        pass values between them, each one handling one clear job.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A real program is rarely one function — it's several, each doing one job well, working together. A
        function that returns a value can feed that value straight into another function's parameter:
    </p>

    <pre class="code-block"><code>int result = computeSomething(a, b);
reportSomething(result);</code></pre>

    <p class="body-text">
        <code>computeSomething</code> handles the math and hands back an answer. <code>reportSomething</code>
        takes that answer and handles the output. Neither function needs to know how the other one works
        internally — <code>reportSomething</code> just needs a value to report, and doesn't care where it came
        from.
    </p>

    <p class="body-text">
        This is the whole point of functions: each one is a self-contained job. <code>main</code>'s role
        becomes coordinating them — call this, take its answer, hand it to that — rather than doing every step
        itself.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">In the explanation, which function handles the output?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>computeSomething</code></li>
            <li class="quiz-option">B. <code>main</code></li>
            <li class="quiz-option quiz-correct">C. <code>reportSomething</code> ✓</li>
            <li class="quiz-option">D. None, <code>main</code> prints everything itself</li>
        </ul>
        <p class="quiz-explanation"><em><code>computeSomething</code> does the math and hands back an answer; <code>reportSomething</code> takes that answer and prints it.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        Think of a <strong>factory line</strong>. The first station works out a number and writes it on a sticker on the box. The box slides along, and the next station reads the sticker and prints the label. A manager with a clipboard decides who does what.
    </p>

    <p class="body-text">
        In C, each station is a function with <strong>one job</strong>, the sticker is the value one function <code>return</code>s and the next one takes as a parameter, and <code>main</code> is the manager. Neither station needs to know how the other one works inside. <code>report</code> just needs a number; it doesn't care how it was worked out.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 335" role="img" aria-label="main, the manager, directs two stations on a factory line. computeTotal(a, b) does the math and hands back 12. The box with result 12 on its sticker slides to report(result), which shows Total: 12. Each station does one job and they only share the value on the sticker.">
            <circle cx="320" cy="36" r="14" fill="#ffd9b3" stroke="#8a4b00" stroke-width="1.5"/><path d="M298 86 Q298 56 320 56 Q342 56 342 86 Z" fill="#7c5cff"/><rect x="334" y="58" width="22" height="28" rx="3" fill="#fff" stroke="#5b6b86" stroke-width="1.5"/><line x1="338" y1="66" x2="352" y2="66" stroke="#9aa5b8" stroke-width="1.5"/><line x1="338" y1="72" x2="352" y2="72" stroke="#9aa5b8" stroke-width="1.5"/><line x1="338" y1="78" x2="352" y2="78" stroke="#9aa5b8" stroke-width="1.5"/><text x="368" y="44" font-size="14" fill="#4a35b8" font-weight="800">main, the manager</text><text x="368" y="64" font-size="12" fill="#4a35b8" font-style="italic">“you work it out, you show it”</text><path d="M300 82 Q180 90 150 122" fill="none" stroke="#7c5cff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="5,4"/><path d="M340 82 Q460 90 490 122" fill="none" stroke="#7c5cff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="5,4"/><rect x="40" y="124" width="210" height="92" rx="12" fill="#f5f9ff" stroke="#2f6fe0" stroke-width="2"/><text x="145" y="152" font-size="14" fill="#14306b" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">computeTotal(a, b)</text><text x="145" y="176" font-size="13" fill="#14306b" text-anchor="middle">job: do the math</text><text x="145" y="198" font-size="12" fill="#5b6b86" text-anchor="middle" font-style="italic">hands back 12</text><rect x="390" y="124" width="210" height="92" rx="12" fill="#f3fbf6" stroke="#2fa56b" stroke-width="2"/><text x="495" y="152" font-size="14" fill="#1d6b43" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">report(result)</text><text x="495" y="176" font-size="13" fill="#1d6b43" text-anchor="middle">job: show it</text><rect x="455" y="186" width="80" height="22" rx="4" fill="#1b2440"/><text x="495" y="202" font-size="11" fill="#86e0b0" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">Total: 12</text><rect x="20" y="252" width="600" height="18" rx="9" fill="#5b6b86"/><circle cx="40" cy="261" r="5" fill="#cfd7e6"/><circle cx="80" cy="261" r="5" fill="#cfd7e6"/><circle cx="120" cy="261" r="5" fill="#cfd7e6"/><circle cx="160" cy="261" r="5" fill="#cfd7e6"/><circle cx="200" cy="261" r="5" fill="#cfd7e6"/><circle cx="240" cy="261" r="5" fill="#cfd7e6"/><circle cx="280" cy="261" r="5" fill="#cfd7e6"/><circle cx="320" cy="261" r="5" fill="#cfd7e6"/><circle cx="360" cy="261" r="5" fill="#cfd7e6"/><circle cx="400" cy="261" r="5" fill="#cfd7e6"/><circle cx="440" cy="261" r="5" fill="#cfd7e6"/><circle cx="480" cy="261" r="5" fill="#cfd7e6"/><circle cx="520" cy="261" r="5" fill="#cfd7e6"/><circle cx="560" cy="261" r="5" fill="#cfd7e6"/><circle cx="600" cy="261" r="5" fill="#cfd7e6"/><rect x="282" y="214" width="76" height="38" rx="4" fill="#e9c48f" stroke="#8a5a2b" stroke-width="2"/><line x1="320" y1="214" x2="320" y2="252" stroke="#8a5a2b" stroke-width="1.5"/><rect x="296" y="222" width="48" height="22" rx="4" fill="#fff" stroke="#e8457c" stroke-width="1.5"/><text x="320" y="238" font-size="14" fill="#9c1f4c" font-weight="800" text-anchor="middle">12</text><line x1="254" y1="232" x2="269.0" y2="232.0" stroke="#e8457c" stroke-width="2.5"/><polygon points="278,232 269.0,226.0 269.0,238.0" fill="#e8457c"/><line x1="362" y1="232" x2="380.6" y2="207.2" stroke="#e8457c" stroke-width="2.5"/><polygon points="386,200 375.8,203.6 385.4,210.8" fill="#e8457c"/><text x="320" y="206" font-size="12" fill="#9c1f4c" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">result</text><rect x="20" y="288" width="600" height="38" rx="19.0" fill="#eef1f5" stroke="#9aa5b8" stroke-width="1.8"/><text x="320.0" y="311.68" font-size="13" fill="#5b6b86" text-anchor="middle">Each station does <tspan font-weight="800">one job</tspan>. They only share the value on the sticker.</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Does <code>report</code> need to know how <code>computeTotal</code> worked out its answer?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Yes, it must redo the math to check</li>
            <li class="quiz-option">B. Yes, they must share all their code</li>
            <li class="quiz-option quiz-correct">C. No, it just needs the value it gets ✓</li>
            <li class="quiz-option">D. No, because report never gets values</li>
        </ul>
        <p class="quiz-explanation"><em>Each function is a self-contained job. <code>report</code> only needs the value handed to it, not the steps that made it.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="main calls shieldGap to get a value, then passes that value into reportGap">
            <rect x="10" y="75" width="130" height="55" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="75" y="107" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">main()</text>

            <rect x="230" y="20" width="180" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="320" y="45" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">shieldGap(cur, tgt)</text>
            <text x="320" y="65" text-anchor="middle" font-size="11.5" fill="#14306b">returns 65</text>

            <rect x="230" y="130" width="180" height="60" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="320" y="155" text-anchor="middle" font-size="13" font-weight="700" fill="#14663f">reportGap(gap)</text>
            <text x="320" y="175" text-anchor="middle" font-size="11.5" fill="#14663f">prints the line</text>

            <line x1="140" y1="90" x2="226" y2="50" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="230,50 218,44 218,56" fill="#7c5cff"/>
            <line x1="226" y1="65" x2="140" y2="100" stroke="#a68cff" stroke-width="2.5" stroke-dasharray="4,3"/>

            <line x1="140" y1="110" x2="226" y2="160" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="230,160 218,154 218,166" fill="#7c5cff"/>

            <text x="500" y="60" text-anchor="middle" font-size="12" fill="#475569">65 flows back to main,</text>
            <text x="500" y="76" text-anchor="middle" font-size="12" fill="#475569">then out to reportGap</text>
            <text x="500" y="92" text-anchor="middle" font-size="12" fill="#475569">as its argument.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int addParts(int a, int b);
void reportTotal(int total);

int main() {
    int total = addParts(12, 30);
    reportTotal(total);
    return 0;
}

int addParts(int a, int b) {
    return a + b;
}

void reportTotal(int total) {
    printf("Salvage total: %d\n", total);
}</code></pre>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the code example, what value is passed into <code>reportTotal</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. 42 ✓</li>
            <li class="quiz-option">B. 12</li>
            <li class="quiz-option">C. 30</li>
            <li class="quiz-option">D. Nothing</li>
        </ul>
        <p class="quiz-explanation"><em><code>addParts(12, 30)</code> returns 42, <code>main</code> stores it in <code>total</code>, and passes <code>total</code> to <code>reportTotal</code>.</em></p>
    </div>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Combine Lesson 3.3's <code>shieldGap</code> with a new function <code>reportGap</code>. Write both with
        prototypes above <code>main</code>: <code>shieldGap(int current, int target)</code> returns
        <code>target - current</code>; <code>reportGap(int gap)</code> prints <code>"Shield gap: "</code>
        followed by <code>gap</code>. In <code>main</code>, call <code>shieldGap(35, 100)</code>, store the
        result, then pass it into <code>reportGap</code>. Expected output:
    </p>

    <pre class="code-block"><code>Shield gap: 65</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Many Hands">
            <input type="hidden" name="filename"     value="crew_report.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

// prototypes for shieldGap(int, int) and reportGap(int) go here

int main() {
    // call shieldGap(35, 100), store it, then pass it into reportGap

    return 0;
}

// full definitions of shieldGap and reportGap go here
">
            <input type="hidden" name="instructions" value="Write two functions, with prototypes above main:

int shieldGap(int current, int target) — returns target - current.
void reportGap(int gap) — prints &quot;Shield gap: &quot; followed by gap.

In main: call shieldGap(35, 100), store the result in a variable, then pass that variable into reportGap.

Expected output:
Shield gap: 65">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Two prototypes above main, two full definitions below it. main only needs: int gap = shieldGap(35, 100); then reportGap(gap);">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int shieldGap(int current, int target);
void reportGap(int gap);

int main() {
    int gap = shieldGap(35, 100);
    reportGap(gap);
    return 0;
}

int shieldGap(int current, int target) {
    return target - current;
}

void reportGap(int gap) {
    printf(&quot;Shield gap: %d\n&quot;, gap);
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm3', 'lesson' => 'lesson05']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In this lesson's exercise, what is passed into reportGap?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The literal numbers 35 and 100</li>
            <li class="quiz-option quiz-correct">B. The value shieldGap returned, stored in gap ✓</li>
            <li class="quiz-option">C. Nothing — reportGap takes no parameters</li>
            <li class="quiz-option">D. The text "Shield gap:"</li>
        </ul>
        <p class="quiz-explanation"><em>main stores shieldGap's return value in gap, then passes that same variable as reportGap's argument.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Does reportGap need to know how shieldGap computed its number?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Yes, it must know current and target</li>
            <li class="quiz-option">B. Yes, but only when the gap is negative</li>
            <li class="quiz-option">C. Yes, it has to repeat the subtraction</li>
            <li class="quiz-option quiz-correct">D. No, it only needs the value it's given ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Each function only needs to know its own job — reportGap just reports whatever number it's given, regardless of how that number was calculated.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is main's role once a program is split across several functions like this?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Calling the jobs and passing results ✓</li>
            <li class="quiz-option">B. Doing all of the real math by itself</li>
            <li class="quiz-option">C. None; main can now be deleted</li>
            <li class="quiz-option">D. Calling just one function in total</li>
        </ul>
        <p class="quiz-explanation"><em>With well-split functions, main's job shifts from doing everything itself to coordinating calls and passing values between them.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Where must the prototypes for shieldGap and reportGap go for this program to work?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Inside main's body, anywhere at all</li>
            <li class="quiz-option">B. At the bottom, after both definitions</li>
            <li class="quiz-option quiz-correct">C. Above main, before any call to them ✓</li>
            <li class="quiz-option">D. Between the two function definitions</li>
        </ul>
        <p class="quiz-explanation"><em>Just like Lesson 3.4, a prototype must appear before any code that calls the function — here, that means above main.</em></p>
    </div>

</div>
