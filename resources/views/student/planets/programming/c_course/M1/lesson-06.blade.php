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

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Which line correctly reads a whole number into <code>age</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>scanf("%d", age);</code></li>
            <li class="quiz-option">B. <code>scanf("%f", &amp;age);</code></li>
            <li class="quiz-option quiz-correct">C. <code>scanf("%d", &amp;age);</code> ✓</li>
            <li class="quiz-option">D. <code>printf("%d", &amp;age);</code></li>
        </ul>
        <p class="quiz-explanation"><em><code>%d</code> matches an <code>int</code>, and <code>&amp;age</code> tells <code>scanf</code> where in memory to put the number.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        <code>scanf</code> is a <strong>delivery driver</strong>. Someone types a number on the keyboard, and the driver has to bring that number to the right house in memory.
    </p>

    <p class="body-text">
        A driver needs an <strong>address</strong>, not a description of what's inside the house. <code>&amp;n</code> means <strong>“the address of n”</strong>. Give the driver <code>&amp;n</code> and the number lands in <code>n</code>. Give it plain <code>n</code> and the driver has a package but nowhere to take it.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 325" role="img" aria-label="You type 42 on the keyboard. scanf, the delivery driver, carries 42 to a house with the address and n, and n gets 42. If you write n without the and sign, the driver has the package but no address.">
            <text x="320" y="34" font-size="14" fill="#1d6b43" text-anchor="middle"><tspan font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="800">scanf("%d", &amp;n);</tspan>  the driver knows where to go</text><text x="95" y="100" font-size="14" fill="#1b2440" font-weight="700" text-anchor="middle">You type: 42</text><rect x="20" y="115" width="150" height="70" rx="10" fill="#eef1f5" stroke="#5b6b86" stroke-width="2"/><rect x="30.0" y="124" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="49.5" y="124" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="69.0" y="124" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="88.5" y="124" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="108.0" y="124" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="127.5" y="124" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="147.0" y="124" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="30.0" y="142" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="49.5" y="142" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="69.0" y="142" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="88.5" y="142" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="108.0" y="142" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="127.5" y="142" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="147.0" y="142" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="30.0" y="160" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="49.5" y="160" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="69.0" y="160" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="88.5" y="160" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="108.0" y="160" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="127.5" y="160" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><rect x="147.0" y="160" width="15" height="13" rx="3" fill="#fff" stroke="#9aa5b8" stroke-width="1"/><line x1="176" y1="150" x2="211.0" y2="150.0" stroke="#e8457c" stroke-width="3"/><polygon points="222,150 211.0,144.0 211.0,156.0" fill="#e8457c"/><text x="310" y="92" font-size="12" fill="#9c1f4c" font-weight="700" text-anchor="middle">scanf, the delivery driver</text><rect x="230" y="108" width="110" height="62" rx="8" fill="#ffe3ec" stroke="#e8457c" stroke-width="2"/><text x="285" y="148" font-size="24" fill="#9c1f4c" font-weight="800" text-anchor="middle">42</text><rect x="340" y="126" width="50" height="44" rx="8" fill="#ffb3cb" stroke="#e8457c" stroke-width="2"/><rect x="352" y="133" width="26" height="16" rx="3" fill="#fff" stroke="#e8457c" stroke-width="1.5"/><circle cx="262" cy="174" r="11" fill="#1b2440"/><circle cx="364" cy="174" r="11" fill="#1b2440"/><line x1="398" y1="150" x2="445.0" y2="150.0" stroke="#e8457c" stroke-width="3"/><polygon points="456,150 445.0,144.0 445.0,156.0" fill="#e8457c"/><polygon points="462,128 538,72 614,128" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><rect x="474" y="128" width="128" height="94" rx="4" fill="#f5f9ff" stroke="#2f6fe0" stroke-width="2"/><rect x="483" y="136" width="110" height="24" rx="5" fill="#fff" stroke="#2f6fe0" stroke-width="1.5"/><text x="538" y="153" font-size="12" fill="#14306b" font-weight="700" text-anchor="middle">&amp;n = the address</text><rect x="518" y="168" width="40" height="54" rx="4" fill="#ffc94d" stroke="#c99a00" stroke-width="2"/><text x="538" y="202" font-size="20" fill="#7a5a00" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">n</text><text x="538" y="242" font-size="13" fill="#14306b" font-weight="700" text-anchor="middle">n gets 42</text><rect x="20" y="262" width="600" height="52" rx="12" fill="#fff7f7" stroke="#b3261e" stroke-width="2" stroke-dasharray="6,4"/><text x="40" y="285" font-size="14" fill="#b3261e"><tspan font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="800">scanf("%d", n);</tspan>  ✗ forgot the &amp;</text><text x="40" y="304" font-size="13" fill="#7a1a14">The driver has the package but no address. Nowhere to deliver it.</text>
        </svg>
    </figure>

    <p class="body-text">
        The <code>%d</code> on the package matters too: it tells the driver it's carrying a whole number, the same rule as <code>printf</code>.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why does <code>scanf</code> need <code>&amp;n</code> instead of just <code>n</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The &amp; makes the number print faster</li>
            <li class="quiz-option">B. The &amp; turns the number into a letter</li>
            <li class="quiz-option">C. The &amp; is just style and can be skipped</li>
            <li class="quiz-option quiz-correct">D. It needs to know where to deliver it ✓</li>
        </ul>
        <p class="quiz-explanation"><em><code>&amp;n</code> is the address of <code>n</code>. <code>scanf</code> needs that address so it knows where to put the value it reads.</em></p>
    </div>

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

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the code example, which line waits for the user to type something?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. <code>scanf("%d", &amp;n);</code> ✓</li>
            <li class="quiz-option">B. <code>int n;</code></li>
            <li class="quiz-option">C. <code>printf("Enter your age: ");</code></li>
            <li class="quiz-option">D. <code>printf("You are %d.\n", n);</code></li>
        </ul>
        <p class="quiz-explanation"><em><code>scanf</code> is the one that waits and catches what you type. The <code>printf</code> before it only asks the question.</em></p>
    </div>

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
            <li class="quiz-option">A. Prints text out to the screen</li>
            <li class="quiz-option">B. Declares a brand-new variable</li>
            <li class="quiz-option">C. Compiles the program to run</li>
            <li class="quiz-option quiz-correct">D. Reads a value the user types ✓</li>
        </ul>
        <p class="quiz-explanation"><em>scanf reads input from the user during the program's run, unlike printf which sends output out.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In <code>scanf("%d", &amp;n);</code>, what does <code>&amp;n</code> mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The address in memory where n lives ✓</li>
            <li class="quiz-option">B. The value currently stored in n</li>
            <li class="quiz-option">C. A note to the reader, like a comment</li>
            <li class="quiz-option">D. The same thing as writing just n</li>
        </ul>
        <p class="quiz-explanation"><em>& gives scanf n's address, not its value — scanf needs to know where to write to, not what's already there.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What happens if you forget the & before a variable in scanf?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing, because & is always optional</li>
            <li class="quiz-option">B. The program prints its output faster</li>
            <li class="quiz-option quiz-correct">C. scanf has no address to write into ✓</li>
            <li class="quiz-option">D. Every printf in the program stops too</li>
        </ul>
        <p class="quiz-explanation"><em>Without &, scanf is not told where in memory to store what it reads, and the variable is not reliably updated.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why does the prompt <code>printf("Enter fuel: ");</code> have no <code>\n</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is just a typo in the code</li>
            <li class="quiz-option quiz-correct">B. So the answer stays on that line ✓</li>
            <li class="quiz-option">C. \n is not allowed before scanf</li>
            <li class="quiz-option">D. It makes the scanf call faster</li>
        </ul>
        <p class="quiz-explanation"><em>Leaving off \n keeps the cursor on the same line as the prompt, so the user's answer reads naturally right after it.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If the user types 40 for fuel and the program computes n - 15, what should print?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Left: 40</li>
            <li class="quiz-option">B. Left: 15</li>
            <li class="quiz-option quiz-correct">C. Left: 25 ✓</li>
            <li class="quiz-option">D. Left: 55</li>
        </ul>
        <p class="quiz-explanation"><em>40 - 15 is 25 — scanf reads 40 into n, and printf reports n - 15.</em></p>
    </div>

</div>
