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

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        The storm is on them now. The shell reading drops, climbs, drops again — and a plain yes-or-no isn't
        enough any more.
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "'Bad' isn't one thing. There's fine, there's keep-an-eye-on-it, and there's
        wake-everybody-up. I need to know which."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Three answers from one number. Easy. Over seventy, we're safe. Thirty to
        seventy, warning. Under thirty —" he stops. "Under thirty you're waking everybody up."
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Then check the top one first. Always the top one first. Ask 'under thirty?'
        before you ask 'over seventy?' and a reading of ninety comes back as a warning — and we sleep through
        the one night we shouldn't."
    </p>

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
            <li class="quiz-option">A. Bottom to top</li>
            <li class="quiz-option">B. All at once, in parallel</li>
            <li class="quiz-option quiz-correct">C. Top to bottom, stopping at the first true one ✓</li>
            <li class="quiz-option">D. In a random order</li>
        </ul>
        <p class="quiz-explanation"><em>C checks each condition in the order it's written and runs only the first branch whose condition is true — the rest are skipped.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. In the three-branch status check, what does the final <code>else</code> (with no condition) mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It only runs if shield is exactly 0</li>
            <li class="quiz-option quiz-correct">B. It runs whenever none of the earlier conditions were true ✓</li>
            <li class="quiz-option">C. It always runs, no matter what</li>
            <li class="quiz-option">D. It causes a compiler error without a condition</li>
        </ul>
        <p class="quiz-explanation"><em>A plain else with no condition is the catch-all — it runs when every earlier if/else if in the chain was false.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why must the &gt;= 70 check come before the &gt;= 30 check in this lesson's code?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It doesn't matter — C always checks the smallest number first</li>
            <li class="quiz-option quiz-correct">B. Because shield = 80 would also satisfy &gt;= 30, so the higher threshold must be checked first to catch it correctly ✓</li>
            <li class="quiz-option">C. C requires conditions to be written from smallest to largest</li>
            <li class="quiz-option">D. Reversing the order would cause a syntax error</li>
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
