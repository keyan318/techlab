{{-- M1 · Lesson 1.3: Anatomy of a Console — content fragment.
Syllabus: CC102 Unit 1 — Introduction to C (structure, compilation). --}}

<div class="lesson-fragment" data-module="m1" data-lesson="03">

    <h1 class="lesson-heading">Lesson 1.3: Anatomy of a Console</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can name the parts of a C program's structure, explain what compilation does,
        and write a correctly-structured program from an empty file.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">Every C program shares the same basic skeleton:</p>

    <ul class="body-list">
        <li><strong>Headers</strong> (<code>#include &lt;stdio.h&gt;</code>): borrow tools other people already wrote — <code>stdio.h</code> gives you <code>printf</code> and <code>scanf</code>.</li>
        <li><strong><code>int main()</code></strong>: every C program has exactly one <code>main</code> function, and this is where the console starts running.</li>
        <li><strong>The body</strong> (<code>{ ... }</code>): the statements the console actually executes, in order.</li>
        <li><strong><code>return 0;</code></strong>: tells whatever ran the program that it finished without errors.</li>
    </ul>

    <p class="body-text">
        C source code (the text you type) is not what the console actually runs. A program called a
        <strong>compiler</strong> reads your <code>.c</code> file, checks it follows C's rules exactly, and turns it
        into an executable the machine can run directly. If your source breaks a rule — a missing semicolon, an
        unmatched brace — the compiler refuses and reports where.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        One line worked. Rivet adds a second one, hits run, and the screen fills with red.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> squints at it. One missing semicolon, buried halfway down. "It won't just… run it
        anyway? It clearly knows what I meant."
    </p>

    <p class="body-text">
        <strong>Volt</strong>, from the doorway: "Good."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> sighs and types the semicolon. "Fine. Fine! It's fair. A machine that guesses what
        you meant is a machine that flies you into a mountain." He taps the screen, cheerful again. "Look — same
        bones every single time. Headers at the top. One <code>main</code>. The stuff you want done, in the middle.
        <code>return 0;</code> at the bottom. Get the bones right and it stops shouting at you."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="Source code file goes through the compiler to become an executable that the console runs">
            <rect x="10" y="50" width="160" height="80" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="90" y="80" text-anchor="middle" font-size="15" font-weight="700" fill="#8a4b00">console.c</text>
            <text x="90" y="100" text-anchor="middle" font-size="11.5" fill="#8a4b00">your source code</text>
            <text x="90" y="116" text-anchor="middle" font-size="11.5" fill="#8a4b00">(text you typed)</text>

            <rect x="240" y="50" width="160" height="80" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="320" y="80" text-anchor="middle" font-size="15" font-weight="700" fill="#b3261e">Compiler</text>
            <text x="320" y="100" text-anchor="middle" font-size="11.5" fill="#7a1a14">checks every rule,</text>
            <text x="320" y="116" text-anchor="middle" font-size="11.5" fill="#7a1a14">then builds</text>

            <rect x="470" y="50" width="160" height="80" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="550" y="80" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">console.exe</text>
            <text x="550" y="100" text-anchor="middle" font-size="11.5" fill="#14306b">executable —</text>
            <text x="550" y="116" text-anchor="middle" font-size="11.5" fill="#14306b">runs directly</text>

            <line x1="170" y1="90" x2="236" y2="90" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="240,90 228,84 228,96" fill="#7c5cff"/>
            <line x1="400" y1="90" x2="466" y2="90" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="470,90 458,84 458,96" fill="#7c5cff"/>

            <text x="320" y="175" text-anchor="middle" font-size="13" fill="#4a35b8">A broken rule stops the arrow here — no executable, no run.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;     // headers: borrowed tools

int main() {           // every program starts here
    printf("Diagnostics: PASS\n");   // the body: statements to run
    return 0;           // finished, no errors
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Build the skeleton from scratch: include what <code>printf</code> needs, write <code>main</code>, and print
        exactly <code>Diagnostics: PASS</code>.
    </p>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Anatomy of a Console">
            <input type="hidden" name="filename"     value="diagnostics.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="// Build the skeleton yourself: header, main, body, return.
// Print exactly: Diagnostics: PASS
">
            <input type="hidden" name="instructions" value="Nothing is scaffolded this time. Write the full skeleton yourself:

1. Include the header printf needs.
2. Write int main() with a body in braces.
3. Print exactly: Diagnostics: PASS
4. End with return 0;">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Every program needs #include &lt;stdio.h&gt; above main(), then int main() { ... } with your printf inside the braces, then return 0; before the closing brace.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    printf(&quot;Diagnostics: PASS\n&quot;);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm1', 'lesson' => 'lesson03']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does <code>#include &lt;stdio.h&gt;</code> do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Declares the main function</li>
            <li class="quiz-option quiz-correct">B. Borrows tools like printf and scanf that someone else already wrote ✓</li>
            <li class="quiz-option">C. Compiles the program</li>
            <li class="quiz-option">D. Ends the program</li>
        </ul>
        <p class="quiz-explanation"><em>Headers like stdio.h give a program access to functions it did not have to write itself, such as printf.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Where does a C program start running?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The first #include line</li>
            <li class="quiz-option">B. Whichever function is defined first in the file</li>
            <li class="quiz-option quiz-correct">C. int main() ✓</li>
            <li class="quiz-option">D. The last line of the file</li>
        </ul>
        <p class="quiz-explanation"><em>Every C program has exactly one main function, and that is where execution begins.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is a compiler's job?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It runs the program directly, line by line</li>
            <li class="quiz-option quiz-correct">B. It checks the source code follows C's rules, then turns it into an executable ✓</li>
            <li class="quiz-option">C. It writes the algorithm for you</li>
            <li class="quiz-option">D. It draws the flowchart</li>
        </ul>
        <p class="quiz-explanation"><em>The compiler reads source code, enforces every rule of the language, and builds an executable — or refuses and reports the error.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does <code>return 0;</code> at the end of main() signal?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The program crashed</li>
            <li class="quiz-option quiz-correct">B. The program finished successfully, with no errors ✓</li>
            <li class="quiz-option">C. The program should restart</li>
            <li class="quiz-option">D. Nothing — it can be left out</li>
        </ul>
        <p class="quiz-explanation"><em>Returning 0 from main is the standard way a C program reports that it finished without errors.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Rivet's program was missing a semicolon. What happened?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The compiler ran it anyway, guessing the intent</li>
            <li class="quiz-option quiz-correct">B. The compiler refused to build it and reported the error ✓</li>
            <li class="quiz-option">C. The program ran but printed nothing</li>
            <li class="quiz-option">D. The semicolon is optional in C, so nothing happened</li>
        </ul>
        <p class="quiz-explanation"><em>A broken rule — like a missing semicolon — stops the compiler from building an executable at all.</em></p>
    </div>

</div>
