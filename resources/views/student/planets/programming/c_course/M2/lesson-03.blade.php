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

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        There isn't enough power to run the whole ship at once, so it goes to one bay at a time. Right now that
        means Rivet walking down and throwing a switch by hand, every time.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "A number comes in. One to three. Send the power to that bay and nowhere else —
        and if the number is anything other than one, two or three, it sends power <em>nowhere</em> and tells me."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "That's a switchboard. One little block for each bay, and a catch-all at the
        bottom for numbers that shouldn't exist." He pauses, then points hard at the screen. "And you put a
        <code>break;</code> at the end of every one. I mean it. Forget one and a request for bay 1 runs bay 1
        <em>and then keeps going</em> into bay 2 like nothing happened."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "It just... keeps going?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "It just keeps going."
    </p>

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
            <li class="quiz-option">A. Ends the entire program</li>
            <li class="quiz-option quiz-correct">B. Exits the switch, so it doesn't fall into the next case ✓</li>
            <li class="quiz-option">C. Skips to the default case</li>
            <li class="quiz-option">D. Restarts the switch from the top</li>
        </ul>
        <p class="quiz-explanation"><em>break exits the switch statement immediately once a matching case's code has run, preventing the next case from also running.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What happens if you forget break; at the end of a matching case?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The program refuses to compile</li>
            <li class="quiz-option quiz-correct">B. Execution falls through and runs the next case's code too ✓</li>
            <li class="quiz-option">C. Nothing — break is always optional and has no effect</li>
            <li class="quiz-option">D. The switch immediately jumps to default</li>
        </ul>
        <p class="quiz-explanation"><em>Without break, C "falls through" into the next case's code even though it didn't match — a classic switch bug.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is default for in a switch statement?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It must always be the first case listed</li>
            <li class="quiz-option quiz-correct">B. It runs when the value matched none of the listed cases ✓</li>
            <li class="quiz-option">C. It runs before every other case, every time</li>
            <li class="quiz-option">D. It is required — a switch cannot compile without one</li>
        </ul>
        <p class="quiz-explanation"><em>default is the optional catch-all: it runs only when nothing else in the switch matched the value.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. When does a switch statement usually read more clearly than an if/else if chain?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Never — if/else if is always clearer</li>
            <li class="quiz-option quiz-correct">B. When one variable is checked against several specific, exact values ✓</li>
            <li class="quiz-option">C. When you need to compare two different variables</li>
            <li class="quiz-option">D. When the condition involves a range like x &gt; 10</li>
        </ul>
        <p class="quiz-explanation"><em>switch shines when a single value can equal several specific cases — a chain of range checks (like &gt;= 70) still needs if/else if.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If bay = 1 and the case 1 block is missing its break;, what would print?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Bay 1: Engineering, then Bay 2: Medical ✓</li>
            <li class="quiz-option">B. Only Bay 1: Engineering</li>
            <li class="quiz-option">C. Only Bay unknown</li>
            <li class="quiz-option">D. Nothing prints at all</li>
        </ul>
        <p class="quiz-explanation"><em>Without break, execution falls through from case 1 straight into case 2's code, printing both lines even though bay was only 1.</em></p>
    </div>

</div>
