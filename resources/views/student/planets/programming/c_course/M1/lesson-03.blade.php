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

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">How many <code>main</code> functions does a C program have?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. None, it is optional</li>
            <li class="quiz-option">B. One for every <code>printf</code></li>
            <li class="quiz-option">C. As many as you like</li>
            <li class="quiz-option quiz-correct">D. Exactly one ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Every C program has exactly one <code>main</code>, and that is where the console starts running.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        Remember the robot chef from Lesson 1.1? Every recipe card it reads has the same layout, every single time: the <strong>tools</strong> to grab first, a line that says <strong>“start cooking here”</strong>, the <strong>steps</strong> in order, and <strong>“done, all good”</strong> at the end.
    </p>

    <p class="body-text">
        A C program is that card. <code>#include &lt;stdio.h&gt;</code> grabs the tools, <code>int main()</code> is where cooking starts, the lines inside <code>{ }</code> are the steps, and <code>return 0;</code> says it all went fine.
    </p>

    <p class="body-text">
        Before the robot cooks anything, a strict kitchen inspector reads the card. That inspector is the <strong>compiler</strong>. If one step has no ending, like a missing semicolon, the card is sent back with the line number and nothing gets cooked. If the card passes, the inspector turns it into the robot's own language.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 355" role="img" aria-label="A recipe card next to a C file. Tools: grab a printer matches include stdio.h. Start cooking here matches int main. Step 1, say PASS, matches printf. Done, all good matches return 0. The recipe card goes to the compiler, which checks it, then the robot runs it.">
            <rect x="20" y="30" width="260" height="260" rx="12" fill="#fffaf2" stroke="#f08c1a" stroke-width="2"/><rect x="20" y="30" width="260" height="40" rx="12" fill="#fff0d9"/><rect x="21" y="52" width="258" height="18" fill="#fff0d9"/><line x1="20" y1="70" x2="280" y2="70" stroke="#f08c1a" stroke-width="2"/><rect x="20" y="30" width="260" height="260" rx="12" fill="none" stroke="#f08c1a" stroke-width="2"/><text x="38" y="56" font-size="12" fill="#8a4b00" font-weight="800" letter-spacing="1">RECIPE CARD</text><circle cx="44" cy="95" r="7" fill="#f08c1a"/><text x="60" y="100" font-size="15" fill="#8a4b00"><tspan font-weight="800">Tools:</tspan> grab a printer</text><line x1="36" y1="120" x2="264" y2="120" stroke="#f3d9b5" stroke-width="1.5" stroke-dasharray="4,4"/><line x1="286" y1="95" x2="329.0" y2="95.0" stroke="#f08c1a" stroke-width="2.5" stroke-dasharray="5,4"/><polygon points="340,95 329.0,89.0 329.0,101.0" fill="#f08c1a"/><circle cx="44" cy="145" r="7" fill="#7c5cff"/><text x="60" y="150" font-size="15" fill="#4a35b8"><tspan font-weight="800">Start cooking here</tspan> </text><line x1="36" y1="170" x2="264" y2="170" stroke="#f3d9b5" stroke-width="1.5" stroke-dasharray="4,4"/><line x1="286" y1="145" x2="329.0" y2="145.0" stroke="#7c5cff" stroke-width="2.5" stroke-dasharray="5,4"/><polygon points="340,145 329.0,139.0 329.0,151.0" fill="#7c5cff"/><circle cx="44" cy="195" r="7" fill="#2f6fe0"/><text x="60" y="200" font-size="15" fill="#14306b"><tspan font-weight="800">Step 1:</tspan> say “PASS”</text><line x1="36" y1="220" x2="264" y2="220" stroke="#f3d9b5" stroke-width="1.5" stroke-dasharray="4,4"/><line x1="286" y1="195" x2="329.0" y2="195.0" stroke="#2f6fe0" stroke-width="2.5" stroke-dasharray="5,4"/><polygon points="340,195 329.0,189.0 329.0,201.0" fill="#2f6fe0"/><circle cx="44" cy="245" r="7" fill="#2fa56b"/><text x="60" y="250" font-size="15" fill="#1d6b43"><tspan font-weight="800">Done,</tspan> all good!</text><line x1="286" y1="245" x2="329.0" y2="245.0" stroke="#2fa56b" stroke-width="2.5" stroke-dasharray="5,4"/><polygon points="340,245 329.0,239.0 329.0,251.0" fill="#2fa56b"/><rect x="346" y="30" width="274" height="260" rx="14" fill="#f6f8fb" stroke="#cfd7e6" stroke-width="2"/><text x="364" y="56" font-size="12" fill="#5b6b86" font-weight="800" letter-spacing="1">YOUR .c FILE</text><text x="362" y="100" font-size="14" fill="#8a4b00" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">#include&#160;&lt;stdio.h&gt;</text><text x="362" y="150" font-size="14" fill="#4a35b8" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">int&#160;main()&#160;{</text><text x="362" y="200" font-size="14" fill="#14306b" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">&#160;&#160;printf("PASS\n");</text><text x="362" y="250" font-size="14" fill="#1d6b43" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">&#160;&#160;return&#160;0;</text><text x="362" y="275" font-size="14" fill="#5b6b86" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">}</text><rect x="20" y="310" width="170" height="36" rx="18.0" fill="#eef1f5" stroke="#9aa5b8" stroke-width="1.8"/><text x="105.0" y="332.68" font-size="13" fill="#5b6b86" text-anchor="middle">recipe card (.c file)</text><line x1="194" y1="328" x2="213.0" y2="328.0" stroke="#9aa5b8" stroke-width="2.5"/><polygon points="222,328 213.0,322.0 213.0,334.0" fill="#9aa5b8"/><rect x="226" y="310" width="190" height="36" rx="18.0" fill="#ffe3ec" stroke="#e8457c" stroke-width="1.8"/><text x="321.0" y="332.68" font-size="13" fill="#9c1f4c" text-anchor="middle"><tspan font-weight="800">compiler</tspan> checks it</text><line x1="420" y1="328" x2="439.0" y2="328.0" stroke="#9aa5b8" stroke-width="2.5"/><polygon points="448,328 439.0,322.0 439.0,334.0" fill="#9aa5b8"/><rect x="452" y="310" width="168" height="36" rx="18.0" fill="#e9f7ef" stroke="#2fa56b" stroke-width="1.8"/><text x="536.0" y="332.68" font-size="13" fill="#1d6b43" text-anchor="middle">robot runs it</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">On the recipe card, which part is <code>return 0;</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The “done, all good” line at the end ✓</li>
            <li class="quiz-option">B. The list of tools to grab before cooking</li>
            <li class="quiz-option">C. The heading that says where to start</li>
            <li class="quiz-option">D. One of the cooking steps in the middle</li>
        </ul>
        <p class="quiz-explanation"><em><code>return 0;</code> is the last line of <code>main</code>. It reports that the program finished without a problem, like “done, all good” at the bottom of the card.</em></p>
    </div>

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

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the code example, which part is the body (the statements the console actually runs)?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The <code>#include &lt;stdio.h&gt;</code> line at the top</li>
            <li class="quiz-option">B. The comment <code>// headers: borrowed tools</code></li>
            <li class="quiz-option">C. The <code>int main()</code> line before the brace</li>
            <li class="quiz-option quiz-correct">D. The <code>printf</code> line inside the braces ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The body is everything between <code>{</code> and <code>}</code> in <code>main</code>: here, the <code>printf</code> and <code>return 0;</code>.</em></p>
    </div>

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
            <li class="quiz-option">A. Declares where the main function starts</li>
            <li class="quiz-option quiz-correct">B. Borrows ready-made tools like printf ✓</li>
            <li class="quiz-option">C. Compiles the program into an executable</li>
            <li class="quiz-option">D. Tells the console the program has ended</li>
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
            <li class="quiz-option">A. It runs your source code line by line</li>
            <li class="quiz-option quiz-correct">B. It checks the code, then builds an executable ✓</li>
            <li class="quiz-option">C. It writes the algorithm for you from a goal</li>
            <li class="quiz-option">D. It draws a flowchart from your source code</li>
        </ul>
        <p class="quiz-explanation"><em>The compiler reads source code, enforces every rule of the language, and builds an executable — or refuses and reports the error.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does <code>return 0;</code> at the end of main() signal?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The program crashed partway</li>
            <li class="quiz-option quiz-correct">B. The program finished with no errors ✓</li>
            <li class="quiz-option">C. The program should start over</li>
            <li class="quiz-option">D. Nothing; the line does no work</li>
        </ul>
        <p class="quiz-explanation"><em>Returning 0 from main is the standard way a C program reports that it finished without errors.</em></p>
    </div>

</div>
