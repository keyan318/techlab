{{-- M1 · Lesson 1.6: Talking to the Crew — content fragment.
Syllabus: CC102 Unit 1 — Core Syntax (input/output).
This lesson's exercise reads stdin (scanf) — the editor's "Program input" box feeds it. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="06">

    <h1 class="lesson-heading">Lesson 1.6: Talking to the Crew</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can read a value from the crew with <code>scanf</code> and use it in a
        program's output, completing the full loop: print a prompt, read a number, print a response.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Every program so far has known its values in advance. A real console has to ask the crew for information
        while it's running — that's what <code>scanf</code> is for.
    </p>

    <pre class="code-block"><code>int n;
scanf("%d", &amp;n);</code></pre>

    <p class="body-text">
        Two things to notice: the format specifier (<code>%d</code>) must match the variable's type, the same rule
        as <code>printf</code> — and there is an <code>&amp;</code> in front of the variable name. <code>&amp;n</code>
        means "the address of <code>n</code>" — where in memory to <em>write</em> the value that comes in, not the
        value itself. Forget the <code>&amp;</code> and <code>scanf</code> has nowhere to put what it reads.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        The console can talk. It can count. But it has never once asked the crew a question — it only ever says
        back what it was already told.
    </p>

    <p class="body-text">
        <strong>Volt</strong> folds his arms. "Fuel changes every day. If I have to open the code and retype the
        number every time it changes, it isn't helping me. It's homework."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "So we give it ears." He taps two keys. "Print the question. Then
        <code>scanf</code> — that's the one that waits, and catches whatever you type."
    </p>

    <p class="body-text">
        <strong>Astro</strong>, quietly, watching the screen: "Ask, listen, answer. That's a conversation. That's
        the first thing this ship has done that feels alive."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 180" role="img" aria-label="printf prompts the crew, scanf reads their typed value into a variable, printf responds using it">
            <rect x="10" y="30" width="180" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="100" y="58" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">printf</text>
            <text x="100" y="76" text-anchor="middle" font-size="11.5" fill="#14306b">"Enter fuel: "</text>

            <rect x="230" y="30" width="180" height="70" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="52" text-anchor="middle" font-size="13" font-weight="700" fill="#8a4b00">scanf</text>
            <text x="320" y="70" text-anchor="middle" font-size="11" fill="#8a4b00">reads crew's typed</text>
            <text x="320" y="85" text-anchor="middle" font-size="11" fill="#8a4b00">number into &amp;n</text>

            <rect x="450" y="30" width="180" height="70" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="540" y="52" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">printf</text>
            <text x="540" y="70" text-anchor="middle" font-size="11" fill="#4a35b8">responds using</text>
            <text x="540" y="85" text-anchor="middle" font-size="11" fill="#4a35b8">the value of n</text>

            <line x1="190" y1="65" x2="226" y2="65" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="230,65 218,59 218,71" fill="#7c5cff"/>
            <line x1="410" y1="65" x2="446" y2="65" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="450,65 438,59 438,71" fill="#7c5cff"/>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int n;
    printf("Enter your age: ");
    scanf("%d", &amp;n);
    printf("You are %d.\n", n);
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        This program has to ask before it can answer. Print exactly <code>Enter fuel: </code> (no newline — the
        crew types on the same line), read an <code>int</code> into a variable with <code>scanf</code>, then print
        exactly <code>Left: </code> followed by that number minus <code>15</code>, then a newline.
        Type a number into the <strong>Program input</strong> box below the editor before you run.
    </p>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Talking to the Crew">
            <input type="hidden" name="filename"     value="fuel_check.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int n;

    // print &quot;Enter fuel: &quot; (no newline), read n with scanf,
    // then print &quot;Left: &quot; followed by n - 15, then a newline

    return 0;
}
">
            <input type="hidden" name="instructions" value="Test this one with Program input = 40 (type it in the box under the editor, then Run).

1. printf(&quot;Enter fuel: &quot;); — no \n, the crew types right after it.
2. scanf(&quot;%d&quot;, &amp;n); — read their number into n.
3. printf(&quot;Left: %d\n&quot;, n - 15); — print what remains after using 15 units.

With input 40, the full output should read exactly:
Enter fuel: Left: 25">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Don't forget the &amp; before n in scanf — without it, scanf has no address to write the value into. And the prompt has no \n, so scanf's read happens right after it on the same line of output.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int n;
    printf(&quot;Enter fuel: &quot;);
    scanf(&quot;%d&quot;, &amp;n);
    printf(&quot;Left: %d\n&quot;, n - 15);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm1', 'lesson' => 'lesson06']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does scanf do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Prints text to the screen</li>
            <li class="quiz-option quiz-correct">B. Reads a value the crew types in, while the program runs ✓</li>
            <li class="quiz-option">C. Compiles the program</li>
            <li class="quiz-option">D. Declares a new variable</li>
        </ul>
        <p class="quiz-explanation"><em>scanf reads input from the crew during the program's run, unlike printf which sends output out.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In <code>scanf("%d", &amp;n);</code>, what does <code>&amp;n</code> mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The value currently stored in n</li>
            <li class="quiz-option quiz-correct">B. The address in memory where n lives, so scanf knows where to write the value it reads ✓</li>
            <li class="quiz-option">C. A comment</li>
            <li class="quiz-option">D. It means the same thing as just n</li>
        </ul>
        <p class="quiz-explanation"><em>& gives scanf n's address, not its value — scanf needs to know where to write to, not what's already there.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What happens if you forget the & before a variable in scanf?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing — & is always optional</li>
            <li class="quiz-option quiz-correct">B. scanf has no address to write the value into, so the read fails to update the variable correctly ✓</li>
            <li class="quiz-option">C. The program prints faster</li>
            <li class="quiz-option">D. printf stops working too</li>
        </ul>
        <p class="quiz-explanation"><em>Without &, scanf is not told where in memory to store what it reads, and the variable is not reliably updated.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why does the prompt <code>printf("Enter fuel: ");</code> have no <code>\n</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is a typo</li>
            <li class="quiz-option quiz-correct">B. So the crew's typed answer appears on the same line as the prompt ✓</li>
            <li class="quiz-option">C. \n is not allowed before scanf</li>
            <li class="quiz-option">D. It makes scanf run faster</li>
        </ul>
        <p class="quiz-explanation"><em>Leaving off \n keeps the cursor on the same line as the prompt, so the crew's answer reads naturally right after it.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If the crew types 40 for fuel and the program computes n - 15, what should print?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Left: 40</li>
            <li class="quiz-option">B. Left: 15</li>
            <li class="quiz-option quiz-correct">C. Left: 25 ✓</li>
            <li class="quiz-option">D. Left: 55</li>
        </ul>
        <p class="quiz-explanation"><em>40 - 15 is 25 — scanf reads 40 into n, and printf reports n - 15.</em></p>
    </div>

</div>
