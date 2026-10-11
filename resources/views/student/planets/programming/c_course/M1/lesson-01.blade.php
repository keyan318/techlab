{{-- M1 · Lesson 1.1: Orders Astro Can Follow — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Code Example / Quiz).
"Code it yourself" POSTs to the C editor (JSCPP); the answer key lives in config/course-structure.php
and is checked server-side, same contract as the Python course.
Syllabus: CC102 Unit 1 — Basic Programming Concepts (algorithms). --}}

<div class="lesson-fragment" data-module="m1" data-lesson="01">

    <h1 class="lesson-heading">Lesson 1.1: Orders Astro Can Follow</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what a program and an algorithm are, describe why a computer
        needs exact, ordered steps, and write your very first C program that prints a line of text.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        An <strong>algorithm</strong> is a set of steps that solves a problem — written in any words, for any reader,
        human or machine. A <strong>program</strong> is that same set of steps written in a language a computer can
        actually run, with nothing left to guess. A computer does not "figure out" what you meant: it does exactly
        what the program says, in exactly the order it says it, every single time.
    </p>

    <p class="body-text">A good algorithm — and so a good program — has three properties:</p>

    <ul class="body-list">
        <li><strong>Ordered</strong>: the steps happen in a specific sequence, one after another.</li>
        <li><strong>Precise</strong>: each step means exactly one thing, with nothing left open to interpretation.</li>
        <li><strong>Finite</strong>: it has a clear end — it does not run forever.</li>
    </ul>

    <p class="body-text">
        <strong>C</strong> is the language this course uses to write programs. It turns into instructions the
        computer's chip runs directly — which is why ships, consoles and machines are so often built on C.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Three days since the crash. The ship — <strong>the Wanderer</strong> — lies on the edge of Programming
        City with one wing buried in rubble. No heat. No doors. And on Rivet's workbench, open like a patient on
        a table, sits the ship's brain: <strong>the Dead Console</strong>.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> leans in with a light. Then he grins. "It's not broken. Everybody keeps saying
        broken. It's <em>blank</em>. There's a difference and it's the best news all week."
    </p>

    <p class="body-text">
        <strong>Astro</strong> doesn't look up from the window. "Blank means it does nothing until we tell it
        something. So tell it something."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> is already pulling the keyboard over. "Steps. That's all it wants. Say them in
        order, say them exactly, and it does them — every single time, no arguing, no guessing what you meant.
        The plan in your head is an <em>algorithm</em>. The plan written down so a machine can run it is a
        <em>program</em>."
    </p>

    <p class="body-text">
        <strong>Astro</strong> turns around. "Then we start with the smallest order there is. Make it say one
        line. If it can do that, it's alive. <strong>You're writing this one.</strong>"
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="A problem becomes an algorithm, the algorithm becomes a C program, and the program runs on the console">
            <rect x="10" y="35" width="150" height="70" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="85" y="65" text-anchor="middle" font-size="16" font-weight="700" fill="#b3261e">Problem</text>
            <text x="85" y="86" text-anchor="middle" font-size="12" fill="#7a1a14">"Prove it's alive"</text>

            <rect x="240" y="35" width="150" height="70" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="315" y="60" text-anchor="middle" font-size="16" font-weight="700" fill="#8a4b00">Algorithm</text>
            <text x="315" y="80" text-anchor="middle" font-size="12" fill="#8a4b00">"Print a line</text>
            <text x="315" y="94" text-anchor="middle" font-size="12" fill="#8a4b00">of text"</text>

            <rect x="470" y="35" width="150" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="16" font-weight="700" fill="#14306b">C Program</text>
            <text x="545" y="80" text-anchor="middle" font-size="11.5" fill="#14306b">printf("Console</text>
            <text x="545" y="94" text-anchor="middle" font-size="11.5" fill="#14306b">online.");</text>

            <line x1="160" y1="70" x2="236" y2="70" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="240,70 228,64 228,76" fill="#7c5cff"/>
            <line x1="390" y1="70" x2="466" y2="70" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="470,70 458,64 458,76" fill="#7c5cff"/>

            <rect x="240" y="140" width="150" height="50" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="315" y="170" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">Console runs it</text>
            <line x1="545" y1="105" x2="315" y2="136" stroke="#7c5cff" stroke-width="3" stroke-dasharray="4,3"/>
            <polygon points="315,140 306,124 324,127" fill="#7c5cff"/>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    printf("Systems check.\n");
    return 0;
}</code></pre>

    <p class="body-text">
        <code>#include &lt;stdio.h&gt;</code> lends the program <code>printf</code>. <code>int main()</code> is where
        the console starts running. <code>printf("...")</code> prints exactly what's inside the quotes, and
        <code>\n</code> moves to a new line. <code>return 0;</code> tells the console the program finished cleanly.
    </p>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        This is the Wanderer's first word since the crash. Make the console print exactly:
        <code>Console online.</code>
    </p>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Orders Astro Can Follow">
            <input type="hidden" name="filename"     value="console_online.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    // print exactly: Console online.

    return 0;
}
">
            <input type="hidden" name="instructions" value="The whole crew is standing behind you. Add one line inside main() that makes the console print exactly:
Console online.

printf needs the text in quotes, and the line ends with a semicolon.">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="printf(&quot;...&quot;); prints whatever sits between the quotes, character for character. Put \n at the end so the next line starts fresh — and don't forget the semicolon. Rivet forgets it constantly. He will deny this.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    printf(&quot;Console online.\n&quot;);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm1', 'lesson' => 'lesson01']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is an algorithm?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A brand of computer</li>
            <li class="quiz-option quiz-correct">B. A set of steps that solves a problem ✓</li>
            <li class="quiz-option">C. A type of variable</li>
            <li class="quiz-option">D. A C compiler</li>
        </ul>
        <p class="quiz-explanation"><em>An algorithm is the plan — an ordered set of steps that solves a problem, in any language.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What turns an algorithm into something a computer can actually run?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Reading it out loud</li>
            <li class="quiz-option">B. Drawing it as a picture</li>
            <li class="quiz-option quiz-correct">C. Writing it precisely in a programming language, as a program ✓</li>
            <li class="quiz-option">D. Nothing — computers run algorithms directly</li>
        </ul>
        <p class="quiz-explanation"><em>A program is an algorithm written so precisely, in a language like C, that a machine can run it with nothing left to guess.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why can't a computer "figure out" a vague instruction the way a person might?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Computers are too slow</li>
            <li class="quiz-option quiz-correct">B. A computer runs exactly what a program says, with nothing left to interpretation ✓</li>
            <li class="quiz-option">C. Computers cannot read English</li>
            <li class="quiz-option">D. It can — computers understand vague instructions fine</li>
        </ul>
        <p class="quiz-explanation"><em>A program must be precise: every step means exactly one thing, because the computer never guesses at intent.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. In <code>printf("Console online.\n");</code>, what does <code>\n</code> do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Prints the letter n</li>
            <li class="quiz-option">B. Ends the program</li>
            <li class="quiz-option quiz-correct">C. Moves the cursor to a new line ✓</li>
            <li class="quiz-option">D. Declares a variable</li>
        </ul>
        <p class="quiz-explanation"><em>\n is a newline character — it moves whatever prints next onto a new line.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Which property is NOT one of the three properties of a good algorithm from this lesson?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Ordered</li>
            <li class="quiz-option">B. Precise</li>
            <li class="quiz-option">C. Finite</li>
            <li class="quiz-option quiz-correct">D. Colorful ✓</li>
        </ul>
        <p class="quiz-explanation"><em>A good algorithm is ordered, precise and finite — how it looks has nothing to do with whether it works.</em></p>
    </div>

</div>
