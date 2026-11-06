{{-- M2 · Lesson 2.3: The Switchboard — content fragment.
Syllabus: CC102 Unit 2 — Decision Making Statements (switch/case). --}}

<div class="lesson-fragment" data-module="m2" data-lesson="03">

    <h1 class="lesson-heading">Lesson 2.3: The Switchboard</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write a <code>switch</code> statement with <code>case</code>,
        <code>break</code> and <code>default</code>, and explain when it reads better than a long
        <code>if</code>/<code>else if</code> chain.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        When one variable can equal several specific values, and each value needs its own response, a
        <code>switch</code> often reads more clearly than a long <code>if</code>/<code>else if</code> chain:
    </p>

    <pre class="code-block"><code>switch (value) {
    case 1:
        // runs if value == 1
        break;
    case 2:
        // runs if value == 2
        break;
    default:
        // runs if value matched no case
}</code></pre>

    <p class="body-text">
        Each <code>case</code> is a possible value to match. <code>break;</code> exits the switch once a match's
        code has run — leave it out and C falls through into the <em>next</em> case's code too, whether or not it
        matches. <code>default</code> is optional, and catches anything that matched no case.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What is each <code>case</code> in a <code>switch</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A loop that repeats the code</li>
            <li class="quiz-option">B. A new variable to declare</li>
            <li class="quiz-option">C. A separate function to call</li>
            <li class="quiz-option quiz-correct">D. One possible value to match ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Each <code>case</code> is a value the switch compares against. The code under the matching one runs.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        A <code>switch</code> works like a <strong>vending machine</strong>. You press a button, and the machine jumps straight to the slot with that number. Each slot is a <code>case</code>.
    </p>

    <p class="body-text">
        After dropping your snack, the machine needs to be told to stop. That's <code>break;</code>. Forget it, and the machine keeps going: it drops your snack <em>and</em> the one in the next slot, whether you wanted it or not. That is called <strong>falling through</strong>. <code>default</code> is the “Sorry, no such button” screen for anything that matched no slot.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 345" role="img" aria-label="A vending machine with snacks in slots 1, 2 and 3, and button 2 pressed. Case 2 with break drops snack 2, then stops. Case 2 with no break drops snack 2 and snack 3 too. Default handles a button with no slot: sorry.">
            <rect x="30" y="30" width="250" height="300" rx="16" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><rect x="48" y="48" width="158" height="206" rx="8" fill="#fff" stroke="#2f6fe0" stroke-width="2"/><line x1="56" y1="112" x2="198" y2="112" stroke="#cfd7e6" stroke-width="3"/><text x="68" y="90" font-size="14" fill="#14306b" font-weight="800" text-anchor="middle">1</text><path d="M110 60 h40.0 l-4.0 44.0 h-32.0 z" fill="#ffc46b" stroke="#f08c1a" stroke-width="2"/><line x1="56" y1="178" x2="198" y2="178" stroke="#cfd7e6" stroke-width="3"/><text x="68" y="156" font-size="14" fill="#14306b" font-weight="800" text-anchor="middle">2</text><rect x="110" y="140.0" width="48.0" height="18.0" rx="4" fill="#b6a3ff" stroke="#7c5cff" stroke-width="2"/><line x1="56" y1="244" x2="198" y2="244" stroke="#cfd7e6" stroke-width="3"/><text x="68" y="222" font-size="14" fill="#14306b" font-weight="800" text-anchor="middle">3</text><rect x="116.0" y="192" width="30.0" height="44.0" rx="6" fill="#6fe0e8" stroke="#0fa3b1" stroke-width="2"/><rect x="216" y="56" width="50" height="150" rx="8" fill="#f5f9ff" stroke="#2f6fe0" stroke-width="2"/><circle cx="241" cy="86" r="15" fill="#fff" stroke="#2f6fe0" stroke-width="2"/><text x="241" y="91" font-size="14" fill="#14306b" font-weight="800" text-anchor="middle">1</text><circle cx="241" cy="131" r="15" fill="#e8457c" stroke="#e8457c" stroke-width="2"/><text x="241" y="136" font-size="14" fill="#fff" font-weight="800" text-anchor="middle">2</text><circle cx="241" cy="176" r="15" fill="#fff" stroke="#2f6fe0" stroke-width="2"/><text x="241" y="181" font-size="14" fill="#14306b" font-weight="800" text-anchor="middle">3</text><rect x="48" y="268" width="158" height="46" rx="8" fill="#1b2440"/><rect x="100" y="284.0" width="48.0" height="18.0" rx="4" fill="#b6a3ff" stroke="#7c5cff" stroke-width="2"/><text x="241" y="240" font-size="11" fill="#9c1f4c" font-weight="700" text-anchor="middle">you</text><text x="241" y="254" font-size="11" fill="#9c1f4c" font-weight="700" text-anchor="middle">press 2</text><rect x="306" y="30" width="314" height="88" rx="12" fill="#f3fbf6" stroke="#2fa56b" stroke-width="2"/><text x="324" y="62" font-size="14" fill="#1d6b43" font-weight="800" font-family="ui-monospace, Menlo, Consolas, monospace">case 2: … break;</text><text x="324" y="92" font-size="13" fill="#1d6b43">Drops snack 2, then stops.</text><rect x="548" y="60.4" width="28.799999999999997" height="10.799999999999999" rx="4" fill="#b6a3ff" stroke="#7c5cff" stroke-width="2"/><rect x="306" y="132" width="314" height="88" rx="12" fill="#fff7f7" stroke="#b3261e" stroke-width="2"/><text x="324" y="164" font-size="14" fill="#7a1a14" font-weight="800" font-family="ui-monospace, Menlo, Consolas, monospace">case 2: … (no break)</text><text x="324" y="194" font-size="13" fill="#7a1a14">Drops snack 2, and snack 3 too!</text><rect x="531" y="162.4" width="28.799999999999997" height="10.799999999999999" rx="4" fill="#b6a3ff" stroke="#7c5cff" stroke-width="2"/><rect x="568.6" y="154" width="18.0" height="26.4" rx="6" fill="#6fe0e8" stroke="#0fa3b1" stroke-width="2"/><rect x="306" y="234" width="314" height="88" rx="12" fill="#f7f8fa" stroke="#9aa5b8" stroke-width="2"/><text x="324" y="266" font-size="14" fill="#5b6b86" font-weight="800" font-family="ui-monospace, Menlo, Consolas, monospace">default:</text><text x="324" y="296" font-size="13" fill="#5b6b86">No such button? “Sorry!”</text>
        </svg>
    </figure>

    <p class="body-text">
        A <code>switch</code> is handy when one value can be several exact choices, like buttons 1, 2 and 3. For ranges like “taller than 120”, an <code>if</code> chain still fits better.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">You press 2 on a machine where <code>case 2</code> has no <code>break</code>. What comes out?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Only snack 2, then it stops</li>
            <li class="quiz-option quiz-correct">B. Snack 2, and snack 3 falls too ✓</li>
            <li class="quiz-option">C. Nothing, since 2 needs a default</li>
            <li class="quiz-option">D. Every snack in the machine, 1 to 3</li>
        </ul>
        <p class="quiz-explanation"><em>Without <code>break</code>, C falls through into the next case's code. It starts at case 2, so snack 1 is safe, but snack 3 drops too.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="A bay number flows into a switch, matching one of four cases or falling to default">
            <rect x="20" y="70" width="130" height="55" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="85" y="102" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">switch (bay)</text>

            <rect x="230" y="10" width="150" height="42" rx="10" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="305" y="35" text-anchor="middle" font-size="12.5" font-weight="700" fill="#14306b">case 1: Engineering</text>

            <rect x="230" y="60" width="150" height="42" rx="10" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="305" y="85" text-anchor="middle" font-size="12.5" font-weight="700" fill="#14306b">case 2: Medical</text>

            <rect x="230" y="110" width="150" height="42" rx="10" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="305" y="135" text-anchor="middle" font-size="12.5" font-weight="700" fill="#14306b">case 3: Cargo</text>

            <rect x="230" y="160" width="150" height="34" rx="10" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="305" y="182" text-anchor="middle" font-size="12.5" font-weight="700" fill="#b3261e">default: unknown</text>

            <line x1="150" y1="97" x2="226" y2="30" stroke="#7c5cff" stroke-width="2"/>
            <line x1="150" y1="97" x2="226" y2="80" stroke="#7c5cff" stroke-width="2"/>
            <line x1="150" y1="97" x2="226" y2="130" stroke="#7c5cff" stroke-width="2"/>
            <line x1="150" y1="97" x2="226" y2="176" stroke="#7c5cff" stroke-width="2"/>

            <text x="490" y="90" text-anchor="middle" font-size="13" fill="#475569">break; stops the</text>
            <text x="490" y="107" text-anchor="middle" font-size="13" fill="#475569">switch right after</text>
            <text x="490" y="124" text-anchor="middle" font-size="13" fill="#475569">a matching case runs.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int bay = 2;

    switch (bay) {
        case 1:
            printf("Bay 1: Engineering\n");
            break;
        case 2:
            printf("Bay 2: Medical\n");
            break;
        case 3:
            printf("Bay 3: Cargo\n");
            break;
        default:
            printf("Bay unknown\n");
    }
    return 0;
}</code></pre>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt"><code>bay</code> is 2 in the code example. What prints?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Bay 1: Engineering</li>
            <li class="quiz-option">B. Bay 2: Medical, then Bay 3: Cargo</li>
            <li class="quiz-option">C. Bay unknown</li>
            <li class="quiz-option quiz-correct">D. Bay 2: Medical ✓</li>
        </ul>
        <p class="quiz-explanation"><em><code>case 2</code> matches, prints its line, and its <code>break;</code> stops the switch before it falls into <code>case 3</code>.</em></p>
    </div>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        A request comes in for <code>int bay = 4;</code> — a bay that doesn't exist. Write the same switch, with
        cases 1 through 3 and a <code>default</code>. Since 4 matches no case, print exactly:
    </p>

    <pre class="code-block"><code>Bay unknown</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="The Switchboard">
            <input type="hidden" name="filename"     value="bay_routing.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int bay = 4;

    // switch on bay: case 1 Engineering, case 2 Medical, case 3 Cargo, default unknown

    return 0;
}
">
            <input type="hidden" name="instructions" value="bay holds 4. Write a switch on bay with:

case 1: Bay 1: Engineering
case 2: Bay 2: Medical
case 3: Bay 3: Cargo
default: Bay unknown

Don't forget break; after each case. Since 4 matches nothing, exactly one line should print:
Bay unknown">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Every case except default needs its own break; right after the printf, or the switch keeps running into the next case even after it already matched.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int bay = 4;

    switch (bay) {
        case 1:
            printf(&quot;Bay 1: Engineering\n&quot;);
            break;
        case 2:
            printf(&quot;Bay 2: Medical\n&quot;);
            break;
        case 3:
            printf(&quot;Bay 3: Cargo\n&quot;);
            break;
        default:
            printf(&quot;Bay unknown\n&quot;);
    }
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm2', 'lesson' => 'lesson03']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does <code>break;</code> do inside a switch case?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Ends the whole program right away</li>
            <li class="quiz-option">B. Jumps straight to the default case</li>
            <li class="quiz-option">C. Restarts the switch from the top</li>
            <li class="quiz-option quiz-correct">D. Exits the switch at that point ✓</li>
        </ul>
        <p class="quiz-explanation"><em>break exits the switch statement immediately once a matching case's code has run, preventing the next case from also running.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What happens if you forget break; at the end of a matching case?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The program refuses to compile</li>
            <li class="quiz-option">B. Nothing, since break has no effect</li>
            <li class="quiz-option quiz-correct">C. The next case's code runs too ✓</li>
            <li class="quiz-option">D. The switch jumps to default</li>
        </ul>
        <p class="quiz-explanation"><em>Without break, C "falls through" into the next case's code even though it didn't match — a classic switch bug.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is default for in a switch statement?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. It runs when no case matched ✓</li>
            <li class="quiz-option">B. It runs before every case</li>
            <li class="quiz-option">C. It must be the first case</li>
            <li class="quiz-option">D. It is needed to compile</li>
        </ul>
        <p class="quiz-explanation"><em>default is the optional catch-all: it runs only when nothing else in the switch matched the value.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. When does a switch statement usually read more clearly than an if/else if chain?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Never, since if/else if is always clearer</li>
            <li class="quiz-option quiz-correct">B. When one variable matches exact values ✓</li>
            <li class="quiz-option">C. When two different variables are compared</li>
            <li class="quiz-option">D. When the check is a range like x &gt; 10</li>
        </ul>
        <p class="quiz-explanation"><em>switch shines when a single value can equal several specific cases — a chain of range checks (like &gt;= 70) still needs if/else if.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If bay = 1 and the case 1 block is missing its break;, what would print?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Bay 1: Engineering, and nothing else</li>
            <li class="quiz-option">B. Bay 1: Engineering, then Bay unknown</li>
            <li class="quiz-option quiz-correct">C. Bay 1: Engineering, then Bay 2: Medical ✓</li>
            <li class="quiz-option">D. Bay unknown, and nothing else</li>
        </ul>
        <p class="quiz-explanation"><em>Without break, execution falls through from case 1 straight into case 2's code, printing both lines even though bay was only 1.</em></p>
    </div>

</div>
