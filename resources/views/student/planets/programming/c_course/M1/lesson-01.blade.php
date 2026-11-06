{{-- M1 · Lesson 1.1: Orders Astro Can Follow — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Picture It / Diagram / Code Example / Quiz).
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

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">A recipe step says “add some salt”. Which property of a good algorithm does it break?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Precise ✓</li>
            <li class="quiz-option">B. Ordered</li>
            <li class="quiz-option">C. Finite</li>
            <li class="quiz-option">D. None, it is a fine step</li>
        </ul>
        <p class="quiz-explanation"><em>“Some” could mean a pinch or a cupful. A precise step means exactly one thing, with nothing left to guess.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        Imagine handing the same recipe to two cooks. A person can read <em>“add some salt”</em> and just
        know what you mean. A <strong>robot chef</strong> can't. It reads every word, does exactly that, in
        exactly that order, and never guesses.
    </p>

    <p class="body-text">
        The plan in your head (“make soup”) is the <strong>algorithm</strong>. The recipe written so exactly
        that the robot can follow it is the <strong>program</strong>. The computer is the robot chef, and C
        is the language the recipe is written in.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 350" role="img" aria-label="Two recipe cards. The recipe for a person says add some salt, cook until it looks done, make it tasty, and is labelled the algorithm. The recipe for a robot chef says 1, add 5 g of salt, 2, cook for 10 minutes, 3, serve, and is labelled the program. Below: ordered, precise, finite.">
            {{-- Left card: the recipe a person can follow --}}
            <rect x="20" y="40" width="260" height="210" rx="14" fill="#fffaf2" stroke="#f08c1a" stroke-width="2"/>
            <rect x="20" y="40" width="260" height="40" rx="14" fill="#fff0d9"/>
            <rect x="21" y="62" width="258" height="18" fill="#fff0d9"/>
            <line x1="20" y1="80" x2="280" y2="80" stroke="#f08c1a" stroke-width="2"/>
            <text x="40" y="66" font-size="13" font-weight="800" letter-spacing="1" fill="#8a4b00">RECIPE FOR A PERSON</text>

            <text x="40" y="122" font-size="15" fill="#3d2a10">• Add <tspan fill="#b3261e" font-weight="700">some</tspan> salt</text>
            <text x="40" y="164" font-size="15" fill="#3d2a10">• Cook until it <tspan fill="#b3261e" font-weight="700">looks done</tspan></text>
            <text x="40" y="206" font-size="15" fill="#3d2a10">• Make it <tspan fill="#b3261e" font-weight="700">tasty</tspan></text>
            <g font-size="13" font-weight="800" fill="#b3261e" text-anchor="middle">
                <circle cx="258" cy="117" r="10" fill="#fdeaea" stroke="#b3261e" stroke-width="1.5"/><text x="258" y="122">?</text>
                <circle cx="258" cy="159" r="10" fill="#fdeaea" stroke="#b3261e" stroke-width="1.5"/><text x="258" y="164">?</text>
                <circle cx="258" cy="201" r="10" fill="#fdeaea" stroke="#b3261e" stroke-width="1.5"/><text x="258" y="206">?</text>
            </g>

            {{-- A person in a chef hat --}}
            <g transform="translate(240,46)">
                <circle cx="-8" cy="-24" r="7" fill="#fff" stroke="#8a4b00" stroke-width="1.5"/>
                <circle cx="8" cy="-24" r="7" fill="#fff" stroke="#8a4b00" stroke-width="1.5"/>
                <circle cx="0" cy="-28" r="8" fill="#fff" stroke="#8a4b00" stroke-width="1.5"/>
                <rect x="-11" y="-23" width="22" height="9" rx="2" fill="#fff" stroke="#8a4b00" stroke-width="1.5"/>
                <circle cx="0" cy="0" r="15" fill="#ffd9b3" stroke="#8a4b00" stroke-width="1.5"/>
                <circle cx="-5" cy="-2" r="1.8" fill="#3d2a10"/>
                <circle cx="5" cy="-2" r="1.8" fill="#3d2a10"/>
                <path d="M-6 5 Q0 10 6 5" fill="none" stroke="#3d2a10" stroke-width="1.6" stroke-linecap="round"/>
            </g>

            {{-- Arrow: write it exactly --}}
            <line x1="290" y1="145" x2="344" y2="145" stroke="#e8457c" stroke-width="3"/>
            <polygon points="350,145 338,139 338,151" fill="#e8457c"/>
            <text x="318" y="133" text-anchor="middle" font-size="11" font-weight="700" fill="#e8457c">write it</text>
            <text x="318" y="166" text-anchor="middle" font-size="11" font-weight="700" fill="#e8457c">exactly</text>

            {{-- Right card: the recipe a robot chef can follow --}}
            <rect x="360" y="40" width="260" height="210" rx="14" fill="#f5f9ff" stroke="#2f6fe0" stroke-width="2"/>
            <rect x="360" y="40" width="260" height="40" rx="14" fill="#e8f0ff"/>
            <rect x="361" y="62" width="258" height="18" fill="#e8f0ff"/>
            <line x1="360" y1="80" x2="620" y2="80" stroke="#2f6fe0" stroke-width="2"/>
            <text x="380" y="66" font-size="13" font-weight="800" letter-spacing="1" fill="#14306b">RECIPE FOR A ROBOT</text>

            <g font-size="12" font-weight="800" fill="#fff" text-anchor="middle">
                <circle cx="390" cy="117" r="11" fill="#7c5cff"/><text x="390" y="121">1</text>
                <circle cx="390" cy="159" r="11" fill="#7c5cff"/><text x="390" y="163">2</text>
                <circle cx="390" cy="201" r="11" fill="#7c5cff"/><text x="390" y="205">3</text>
            </g>
            <text x="408" y="122" font-size="15" fill="#14306b">Add <tspan fill="#0b7f8a" font-weight="700">5 g</tspan> of salt</text>
            <text x="408" y="164" font-size="15" fill="#14306b">Cook for <tspan fill="#0b7f8a" font-weight="700">10 minutes</tspan></text>
            <text x="408" y="206" font-size="15" fill="#14306b"><tspan fill="#24804f" font-weight="800">Serve.</tspan> <tspan font-size="12" fill="#5b6b86">← the last step</tspan></text>

            {{-- A robot in a chef hat --}}
            <g transform="translate(580,48)">
                <circle cx="-8" cy="-26" r="7" fill="#fff" stroke="#2f6fe0" stroke-width="1.5"/>
                <circle cx="8" cy="-26" r="7" fill="#fff" stroke="#2f6fe0" stroke-width="1.5"/>
                <circle cx="0" cy="-30" r="8" fill="#fff" stroke="#2f6fe0" stroke-width="1.5"/>
                <rect x="-11" y="-25" width="22" height="10" rx="2" fill="#fff" stroke="#2f6fe0" stroke-width="1.5"/>
                <rect x="-21" y="-4" width="5" height="9" rx="1.5" fill="#73b6ff"/>
                <rect x="16" y="-4" width="5" height="9" rx="1.5" fill="#73b6ff"/>
                <rect x="-17" y="-15" width="34" height="29" rx="7" fill="#dbe8ff" stroke="#2f6fe0" stroke-width="1.8"/>
                <circle cx="-7" cy="-3" r="3.4" fill="#2f6fe0"/>
                <circle cx="7" cy="-3" r="3.4" fill="#2f6fe0"/>
                <rect x="-7" y="6" width="14" height="3" rx="1.5" fill="#2f6fe0"/>
            </g>

            {{-- What each card is --}}
            <text x="150" y="274" text-anchor="middle" font-size="12" font-weight="800" letter-spacing="1" fill="#8a4b00">ALGORITHM <tspan font-weight="500" letter-spacing="0">· the plan</tspan></text>
            <text x="490" y="274" text-anchor="middle" font-size="12" font-weight="800" letter-spacing="1" fill="#14306b">PROGRAM <tspan font-weight="500" letter-spacing="0">· exact steps a machine runs</tspan></text>

            {{-- The three properties, colour-matched to the robot's card --}}
            <rect x="20" y="298" width="190" height="38" rx="19" fill="#f3f0ff" stroke="#7c5cff" stroke-width="1.8"/>
            <text x="115" y="322" text-anchor="middle" font-size="13" fill="#4a35b8"><tspan font-weight="800">Ordered</tspan> · 1 → 2 → 3</text>
            <rect x="225" y="298" width="190" height="38" rx="19" fill="#e6f8f9" stroke="#0fa3b1" stroke-width="1.8"/>
            <text x="320" y="322" text-anchor="middle" font-size="13" fill="#0b6670"><tspan font-weight="800">Precise</tspan> · 5 g, not “some”</text>
            <rect x="430" y="298" width="190" height="38" rx="19" fill="#e9f7ef" stroke="#2fa56b" stroke-width="1.8"/>
            <text x="525" y="322" text-anchor="middle" font-size="13" fill="#1d6b43"><tspan font-weight="800">Finite</tspan> · it ends at Serve.</text>
        </svg>
    </figure>

    <p class="body-text">
        <strong>Where the picture breaks:</strong> a real chef tastes the soup and fixes it. A computer never
        does. If your recipe says 500 g of salt, it adds 500 g, because that is exactly what you wrote.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">The robot chef reads “cook until it looks done”. What goes wrong?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It cooks much faster than a person would</li>
            <li class="quiz-option quiz-correct">B. It has no exact rule for when to stop ✓</li>
            <li class="quiz-option">C. It skips that step and goes to the next</li>
            <li class="quiz-option">D. It tastes the soup and decides by itself</li>
        </ul>
        <p class="quiz-explanation"><em>“Looks done” is not something a machine can check. It needs an exact rule, like “cook for 10 minutes”, or it never knows when to stop.</em></p>
    </div>

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

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the code example, which line tells the console the program finished cleanly?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. <code>return 0;</code> ✓</li>
            <li class="quiz-option">B. <code>#include &lt;stdio.h&gt;</code></li>
            <li class="quiz-option">C. <code>int main() {</code></li>
            <li class="quiz-option">D. <code>printf("Systems check.\n");</code></li>
        </ul>
        <p class="quiz-explanation"><em><code>return 0;</code> is the last statement in <code>main</code>: it reports that the program finished without errors.</em></p>
    </div>

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
            <li class="quiz-option">A. A brand of computer built for coding</li>
            <li class="quiz-option quiz-correct">B. A set of steps that solves a problem ✓</li>
            <li class="quiz-option">C. A variable that holds a program's answer</li>
            <li class="quiz-option">D. A tool that turns C into chip code</li>
        </ul>
        <p class="quiz-explanation"><em>An algorithm is the plan — an ordered set of steps that solves a problem, in any language.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What turns an algorithm into something a computer can actually run?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Reading it out loud to the computer</li>
            <li class="quiz-option">B. Drawing it as a picture on paper</li>
            <li class="quiz-option quiz-correct">C. Writing it in a programming language ✓</li>
            <li class="quiz-option">D. Nothing; computers run plans directly</li>
        </ul>
        <p class="quiz-explanation"><em>A program is an algorithm written so precisely, in a language like C, that a machine can run it with nothing left to guess.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why can't a computer "figure out" a vague instruction the way a person might?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is too slow to think about meaning</li>
            <li class="quiz-option quiz-correct">B. It runs exactly what the program says ✓</li>
            <li class="quiz-option">C. It only reads very short sentences</li>
            <li class="quiz-option">D. It can; it guesses vague steps fine</li>
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
