{{-- M2 · Lesson 2.2: Safe, Warning, Critical — content fragment.
Syllabus: CC102 Unit 2 — Decision Making Statements (if, else, else-if). --}}

<div class="lesson-fragment" data-module="m2" data-lesson="02">

    <h1 class="lesson-heading">Lesson 2.2: Safe, Warning, Critical</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write <code>if</code>, <code>if</code>/<code>else</code>, and
        <code>if</code>/<code>else if</code>/<code>else</code> chains to make a program choose between branches.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        An <code>if</code> statement runs a block of code only when its condition is true:
    </p>

    <pre class="code-block"><code>if (condition) {
    // runs only if condition is true
}</code></pre>

    <p class="body-text">Add <code>else</code> for what happens when it's false, and chain <code>else if</code> to test more conditions in order — C checks each one top to bottom and runs the <em>first</em> branch that matches, skipping the rest:</p>

    <pre class="code-block"><code>if (conditionA) {
    // A
} else if (conditionB) {
    // B, only checked if A was false
} else {
    // neither A nor B was true
}</code></pre>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">In an <code>if</code> / <code>else if</code> / <code>else</code> chain where two conditions are both true, which branch runs?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Both of the matching ones</li>
            <li class="quiz-option">B. Only the last one that matches</li>
            <li class="quiz-option">C. None, because it is an error</li>
            <li class="quiz-option quiz-correct">D. Only the first one that matches ✓</li>
        </ul>
        <p class="quiz-explanation"><em>C checks top to bottom and runs the first branch that matches, then skips the rest.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        At a water park, an attendant checks your height against a list, <strong>top to bottom</strong>. Taller than 140 cm? Big slide. If not, taller than 120 cm? Medium slide. If not that either: kiddie pool.
    </p>

    <p class="body-text">
        The moment one answer is yes, you go to that slide and the attendant stops checking. That is an <code>if</code> / <code>else if</code> / <code>else</code> chain: C runs the <strong>first</strong> branch that matches and skips the rest. The <code>else</code> at the bottom catches everyone else, so nobody is left standing at the top.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 350" role="img" aria-label="A water-slide height check for someone 130 cm tall. If taller than 140: big slide. No. Else if taller than 120: yes, medium slide, and checking stops. Else: kiddie pool. The first yes wins and nothing below it is checked.">
            <rect x="60" y="20" width="140" height="32" rx="16.0" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="1.8"/><text x="130.0" y="40.68" font-size="13" fill="#14306b" text-anchor="middle"><tspan font-weight="800">You</tspan> · 130 cm</text><line x1="130" y1="52" x2="130.0" y2="60.0" stroke="#e8457c" stroke-width="3"/><polygon points="130,71 136.0,60.0 124.0,60.0" fill="#e8457c"/><text x="22" y="116" font-size="15" fill="#9c1f4c" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">if</text><polygon points="45,110 130,72 215,110 130,148" fill="#eef1f5" stroke="#9aa5b8" stroke-width="2"/><text x="130" y="115" font-size="13" fill="#5b6b86" font-weight="700" text-anchor="middle">taller than 140?</text><line x1="215" y1="110" x2="287.0" y2="110.0" stroke="#c3cad6" stroke-width="3"/><polygon points="298,110 287.0,104.0 287.0,116.0" fill="#c3cad6"/><text x="256" y="101" font-size="12" fill="#9aa5b8" font-weight="700" text-anchor="middle">yes</text><rect x="300" y="90" width="160" height="40" rx="10" fill="#f7f8fa" stroke="#c3cad6" stroke-width="2"/><text x="380" y="115" font-size="14" fill="#9aa5b8" font-weight="700" text-anchor="middle">Big slide</text><line x1="130" y1="148" x2="130.0" y2="175.0" stroke="#e8457c" stroke-width="3"/><polygon points="130,186 136.0,175.0 124.0,175.0" fill="#e8457c"/><text x="146" y="172" font-size="12" fill="#e8457c" font-weight="700">no</text><text x="22" y="231" font-size="13" fill="#9c1f4c" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">else</text><text x="22" y="246" font-size="13" fill="#9c1f4c" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">if</text><polygon points="45,225 130,187 215,225 130,263" fill="#ffe3ec" stroke="#e8457c" stroke-width="2"/><text x="130" y="230" font-size="13" fill="#9c1f4c" font-weight="700" text-anchor="middle">taller than 120?</text><line x1="215" y1="225" x2="287.0" y2="225.0" stroke="#e8457c" stroke-width="3"/><polygon points="298,225 287.0,219.0 287.0,231.0" fill="#e8457c"/><text x="256" y="216" font-size="12" fill="#e8457c" font-weight="700" text-anchor="middle">yes</text><rect x="300" y="205" width="160" height="40" rx="10" fill="#ffe3ec" stroke="#e8457c" stroke-width="2"/><text x="380" y="230" font-size="14" fill="#9c1f4c" font-weight="800" text-anchor="middle">Medium slide</text><text x="472" y="222" font-size="12" fill="#9c1f4c" font-weight="700">you go here,</text><text x="472" y="238" font-size="12" fill="#9c1f4c" font-weight="700">checking stops</text><line x1="130" y1="263" x2="130.0" y2="287.0" stroke="#c3cad6" stroke-width="3"/><polygon points="130,298 136.0,287.0 124.0,287.0" fill="#c3cad6"/><text x="146" y="287" font-size="12" fill="#9aa5b8" font-weight="700">no</text><text x="22" y="325" font-size="15" fill="#9c1f4c" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">else</text><rect x="60" y="300" width="140" height="40" rx="10" fill="#f7f8fa" stroke="#c3cad6" stroke-width="2"/><text x="130" y="325" font-size="14" fill="#9aa5b8" font-weight="700" text-anchor="middle">Kiddie pool</text><rect x="300" y="300" width="320" height="40" rx="20.0" fill="#e9f7ef" stroke="#2fa56b" stroke-width="1.8"/><text x="460.0" y="324.68" font-size="13" fill="#1d6b43" text-anchor="middle"><tspan font-weight="800">First yes wins.</tspan> Nothing below it is checked.</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">You are 150 cm, so both “taller than 140” and “taller than 120” are true. Which slide do you get?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Medium, because it's the last match</li>
            <li class="quiz-option">B. Both slides, one after the other</li>
            <li class="quiz-option quiz-correct">C. Big, the first rule that says yes ✓</li>
            <li class="quiz-option">D. Kiddie pool, the else catches you</li>
        </ul>
        <p class="quiz-explanation"><em>C checks the chain from the top and runs only the first branch that matches. 140 comes first, so it's the big slide, and the rest is skipped.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 230" role="img" aria-label="Shield reading flows through three checks in order: safe, warning, critical">
            <rect x="240" y="15" width="160" height="50" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="45" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">shield reading</text>

            <rect x="20" y="95" width="150" height="55" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="95" y="118" text-anchor="middle" font-size="13" font-weight="700" fill="#14663f">&gt;= 70?</text>
            <text x="95" y="136" text-anchor="middle" font-size="12" fill="#14663f">→ "safe"</text>

            <rect x="245" y="95" width="150" height="55" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="118" text-anchor="middle" font-size="13" font-weight="700" fill="#8a4b00">&gt;= 30?</text>
            <text x="320" y="136" text-anchor="middle" font-size="12" fill="#8a4b00">→ "warning"</text>

            <rect x="470" y="95" width="150" height="55" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="545" y="118" text-anchor="middle" font-size="13" font-weight="700" fill="#b3261e">else</text>
            <text x="545" y="136" text-anchor="middle" font-size="12" fill="#b3261e">→ "critical"</text>

            <line x1="320" y1="65" x2="95" y2="93" stroke="#7c5cff" stroke-width="2.5"/>
            <line x1="320" y1="65" x2="320" y2="93" stroke="#7c5cff" stroke-width="2.5"/>
            <line x1="320" y1="65" x2="545" y2="93" stroke="#7c5cff" stroke-width="2.5"/>

            <text x="320" y="195" text-anchor="middle" font-size="13" fill="#475569">Checked in order — the first true branch runs, and the rest are skipped.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int shield = 40;

    if (shield &gt;= 70) {
        printf("Status: safe\n");
    } else if (shield &gt;= 30) {
        printf("Status: warning\n");
    } else {
        printf("Status: critical\n");
    }
    return 0;
}</code></pre>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt"><code>shield</code> is 40 in the code example. What prints?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Status: safe</li>
            <li class="quiz-option">B. Status: critical</li>
            <li class="quiz-option quiz-correct">C. Status: warning ✓</li>
            <li class="quiz-option">D. Nothing</li>
        </ul>
        <p class="quiz-explanation"><em>40 is not <code>&gt;= 70</code>, but it is <code>&gt;= 30</code>, so the <code>else if</code> branch runs.</em></p>
    </div>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        A new reading comes in: <code>int shield = 15;</code>. Write the same three-branch check and print the
        matching status. Since 15 is below 30, print exactly:
    </p>

    <pre class="code-block"><code>Status: critical</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Safe, Warning, Critical">
            <input type="hidden" name="filename"     value="shield_status.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int shield = 15;

    // if shield &gt;= 70: &quot;Status: safe&quot;
    // else if shield &gt;= 30: &quot;Status: warning&quot;
    // else: &quot;Status: critical&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="shield holds 15. Write the three-way check from the lesson:

if shield is 70 or more, print Status: safe
else if shield is 30 or more, print Status: warning
else print Status: critical

15 is below 30, so exactly one line should print:
Status: critical">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Check the highest threshold first. C tests each condition top to bottom and stops at the first true one, so order the branches from &gt;= 70 down to else.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int shield = 15;

    if (shield &gt;= 70) {
        printf(&quot;Status: safe\n&quot;);
    } else if (shield &gt;= 30) {
        printf(&quot;Status: warning\n&quot;);
    } else {
        printf(&quot;Status: critical\n&quot;);
    }
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm2', 'lesson' => 'lesson02']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does the code inside an <code>if (condition) { ... }</code> block do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It always runs, regardless of the condition</li>
            <li class="quiz-option quiz-correct">B. It runs only when the condition is true ✓</li>
            <li class="quiz-option">C. It runs only when the condition is false</li>
            <li class="quiz-option">D. It never runs</li>
        </ul>
        <p class="quiz-explanation"><em>The block after an if only executes when the condition inside the parentheses evaluates to true (non-zero).</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In an if/else if/else chain, in what order does C check the conditions?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Bottom to top, stopping at the first true one</li>
            <li class="quiz-option">B. All at once, then the last true one runs</li>
            <li class="quiz-option quiz-correct">C. Top to bottom, stopping at the first true one ✓</li>
            <li class="quiz-option">D. Top to bottom, running every true one</li>
        </ul>
        <p class="quiz-explanation"><em>C checks each condition in the order it's written and runs only the first branch whose condition is true — the rest are skipped.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. In the three-branch status check, what does the final <code>else</code> (with no condition) mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It runs only if shield is exactly 0</li>
            <li class="quiz-option">B. It always runs, whatever came before</li>
            <li class="quiz-option">C. It is an error without a condition</li>
            <li class="quiz-option quiz-correct">D. It runs when no earlier one was true ✓</li>
        </ul>
        <p class="quiz-explanation"><em>A plain else with no condition is the catch-all — it runs when every earlier if/else if in the chain was false.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why must the &gt;= 70 check come before the &gt;= 30 check in this lesson's code?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. A high reading like 80 also passes &gt;= 30 ✓</li>
            <li class="quiz-option">B. C always checks the smallest number first</li>
            <li class="quiz-option">C. C needs conditions from smallest to largest</li>
            <li class="quiz-option">D. The other order would be a syntax error</li>
        </ul>
        <p class="quiz-explanation"><em>Since C stops at the first true branch, a high shield value must hit the &gt;= 70 check before it ever reaches &gt;= 30, or it would be reported as only a "warning".</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If <code>shield</code> is exactly 30, which branch runs in this lesson's code?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. "Status: safe"</li>
            <li class="quiz-option quiz-correct">B. "Status: warning" ✓</li>
            <li class="quiz-option">C. "Status: critical"</li>
            <li class="quiz-option">D. Nothing prints</li>
        </ul>
        <p class="quiz-explanation"><em>shield &gt;= 70 is false for 30, but shield &gt;= 30 is true (30 is "or equal to" 30), so the warning branch runs.</em></p>
    </div>

</div>
