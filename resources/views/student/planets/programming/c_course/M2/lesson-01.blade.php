{{-- M2 · Lesson 2.1: Reading the Warnings — content fragment.
Syllabus: CC102 Unit 2 — Operators Control Structures (relational & logical operators). --}}

<div class="lesson-fragment" data-module="m2" data-lesson="01">

    <h1 class="lesson-heading">Lesson 2.1: Reading the Warnings</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can use C's relational operators to compare values and its logical
        operators to combine conditions, and predict what each one evaluates to.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>relational operator</strong> compares two values and produces either <code>1</code> (true) or
        <code>0</code> (false) — C has no separate boolean type, so true and false are just the numbers 1 and 0.
    </p>

    <ul class="body-list">
        <li><strong><code>==</code></strong> equal to (not <code>=</code>, which assigns instead of compares)</li>
        <li><strong><code>!=</code></strong> not equal to</li>
        <li><strong><code>&lt;</code></strong>, <strong><code>&gt;</code></strong>, <strong><code>&lt;=</code></strong>, <strong><code>&gt;=</code></strong> — less than, greater than, and their "or equal to" versions</li>
    </ul>

    <p class="body-text">A <strong>logical operator</strong> combines conditions that are already true/false:</p>

    <ul class="body-list">
        <li><strong><code>&amp;&amp;</code></strong> (AND) — true only if <em>both</em> sides are true</li>
        <li><strong><code>||</code></strong> (OR) — true if <em>at least one</em> side is true</li>
        <li><strong><code>!</code></strong> (NOT) — flips true to false and false to true</li>
    </ul>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does <code>!1</code> evaluate to in C?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 1</li>
            <li class="quiz-option">B. -1</li>
            <li class="quiz-option">C. It is an error</li>
            <li class="quiz-option quiz-correct">D. 0 ✓</li>
        </ul>
        <p class="quiz-explanation"><em><code>!</code> flips true to false. In C true is 1 and false is 0, so <code>!1</code> is 0.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        Think of the rules on a sign at a theme-park ride. Each rule is a yes-or-no question: <em>“Are you at least 120 cm?”</em> C answers every question like that with <strong>1 for yes</strong> and <strong>0 for no</strong>.
    </p>

    <p class="body-text">
        Rules can be combined. <code>&amp;&amp;</code> (AND) is “ticket <em>and</em> tall enough”: both must be yes. <code>||</code> (OR) is “cash <em>or</em> card”: one yes is enough. <code>!</code> (NOT) flips the answer, so yes becomes no.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 330" role="img" aria-label="Three ride gates. AND: has a ticket yes, tall enough no, so the gate stays shut: ticket and tall is 0. OR: paid with cash no, paid with card yes, so the gate opens: cash or card is 1. NOT: it is raining is yes, not raining is 0. One equals sign puts a value in; two equals signs ask if two values are equal.">
            <rect x="20" y="30" width="190" height="228" rx="14" fill="#f8f6ff" stroke="#7c5cff" stroke-width="2"/><text x="115" y="58" font-size="15" fill="#4a35b8" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">AND · &amp;&amp;</text><text x="115" y="78" font-size="12" fill="#5b6b86" text-anchor="middle">both must be yes</text><text x="36" y="116" font-size="14" fill="#1b2440">Has a ticket</text><circle cx="188" cy="111" r="11" fill="#e9f7ef" stroke="#2fa56b" stroke-width="1.8"/><path d="M183 111 L186.5 115 L193 107" fill="none" stroke="#2fa56b" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/><text x="36" y="152" font-size="14" fill="#1b2440">Tall enough</text><circle cx="188" cy="147" r="11" fill="#fdeaea" stroke="#b3261e" stroke-width="1.8"/><text x="188" y="152" font-size="13" fill="#b3261e" font-weight="800" text-anchor="middle">✗</text><rect x="34" y="178" width="162" height="36" rx="10" fill="#fdeaea" stroke="#b3261e" stroke-width="1.8"/><text x="115" y="201" font-size="14" fill="#7a1a14" font-weight="700" text-anchor="middle">Gate stays shut → <tspan font-weight="800">0</tspan></text><text x="115" y="240" font-size="14" fill="#4a35b8" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">ticket &amp;&amp; tall</text><rect x="225" y="30" width="190" height="228" rx="14" fill="#f2fbfc" stroke="#0fa3b1" stroke-width="2"/><text x="320" y="58" font-size="15" fill="#0b6670" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">OR · ||</text><text x="320" y="78" font-size="12" fill="#5b6b86" text-anchor="middle">one yes is enough</text><text x="241" y="116" font-size="14" fill="#1b2440">Paid with cash</text><circle cx="393" cy="111" r="11" fill="#fdeaea" stroke="#b3261e" stroke-width="1.8"/><text x="393" y="116" font-size="13" fill="#b3261e" font-weight="800" text-anchor="middle">✗</text><text x="241" y="152" font-size="14" fill="#1b2440">Paid with card</text><circle cx="393" cy="147" r="11" fill="#e9f7ef" stroke="#2fa56b" stroke-width="1.8"/><path d="M388 147 L391.5 151 L398 143" fill="none" stroke="#2fa56b" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/><rect x="239" y="178" width="162" height="36" rx="10" fill="#e9f7ef" stroke="#2fa56b" stroke-width="1.8"/><text x="320" y="201" font-size="14" fill="#1d6b43" font-weight="700" text-anchor="middle">Gate opens → <tspan font-weight="800">1</tspan></text><text x="320" y="240" font-size="14" fill="#0b6670" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">cash || card</text><rect x="430" y="30" width="190" height="228" rx="14" fill="#fffaf2" stroke="#f08c1a" stroke-width="2"/><text x="525" y="58" font-size="15" fill="#8a4b00" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">NOT · !</text><text x="525" y="78" font-size="12" fill="#5b6b86" text-anchor="middle">flips the answer</text><text x="446" y="116" font-size="14" fill="#1b2440">It's raining</text><circle cx="598" cy="111" r="11" fill="#e9f7ef" stroke="#2fa56b" stroke-width="1.8"/><path d="M593 111 L596.5 115 L603 107" fill="none" stroke="#2fa56b" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/><text x="446" y="152" font-size="14" fill="#8a4b00" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">!raining</text><circle cx="598" cy="147" r="11" fill="#fdeaea" stroke="#b3261e" stroke-width="1.8"/><text x="598" y="152" font-size="13" fill="#b3261e" font-weight="800" text-anchor="middle">✗</text><rect x="444" y="178" width="162" height="36" rx="10" fill="#fdeaea" stroke="#b3261e" stroke-width="1.8"/><text x="525" y="201" font-size="14" fill="#7a1a14" font-weight="700" text-anchor="middle">Go outside? No → <tspan font-weight="800">0</tspan></text><text x="525" y="240" font-size="14" fill="#8a4b00" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">!raining</text><rect x="20" y="276" width="600" height="40" rx="20" fill="#eef1f5" stroke="#9aa5b8" stroke-width="1.8"/><text x="320" y="301" font-size="13" fill="#5b6b86" text-anchor="middle"><tspan font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="800">=</tspan> puts a value in   ·   <tspan font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="800">==</tspan> asks “are these equal?” and answers 1 or 0</text>
        </svg>
    </figure>

    <p class="body-text">
        <strong>Careful:</strong> <code>=</code> is not a question. <code>height = 120</code> <em>puts</em> 120 into height. <code>height == 120</code> <em>asks</em> “is it 120?”
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">The sign says: ticket AND tall enough. You have a ticket but you're too short. What is the result?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. 0, because both rules must be yes ✓</li>
            <li class="quiz-option">B. 1, because one of the rules is yes</li>
            <li class="quiz-option">C. 1, because the ticket rule comes first</li>
            <li class="quiz-option">D. 0, because tickets don't count in C</li>
        </ul>
        <p class="quiz-explanation"><em><code>&amp;&amp;</code> is only 1 when <em>both</em> sides are 1. One “no” is enough to make the whole thing 0.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="AND requires both conditions true, OR requires at least one condition true">
            <rect x="20" y="30" width="130" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="85" y="65" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">shield &lt; 50</text>

            <rect x="20" y="110" width="130" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="85" y="145" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">hull &lt; 50</text>

            <rect x="230" y="65" width="100" height="70" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="280" y="95" text-anchor="middle" font-size="15" font-weight="700" fill="#8a4b00">&amp;&amp;</text>
            <text x="280" y="115" text-anchor="middle" font-size="11" fill="#8a4b00">both true</text>

            <rect x="410" y="65" width="100" height="70" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="460" y="95" text-anchor="middle" font-size="15" font-weight="700" fill="#4a35b8">||</text>
            <text x="460" y="115" text-anchor="middle" font-size="11" fill="#4a35b8">either true</text>

            <line x1="150" y1="60" x2="226" y2="90" stroke="#7c5cff" stroke-width="2.5"/>
            <line x1="150" y1="140" x2="226" y2="110" stroke="#7c5cff" stroke-width="2.5"/>
            <line x1="150" y1="60" x2="406" y2="90" stroke="#a68cff" stroke-width="2.5" stroke-dasharray="4,3"/>
            <line x1="150" y1="140" x2="406" y2="110" stroke="#a68cff" stroke-width="2.5" stroke-dasharray="4,3"/>

            <text x="560" y="95" text-anchor="middle" font-size="13" fill="#475569">critical</text>
            <text x="560" y="112" text-anchor="middle" font-size="13" fill="#475569">only if</text>
            <text x="560" y="129" text-anchor="middle" font-size="13" fill="#475569">both fail</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int shield = 40;
    int hull = 90;

    printf("Shield low: %d\n", shield &lt; 50);
    printf("Both safe: %d\n", shield &gt; 50 &amp;&amp; hull &gt; 50);
    printf("Any danger: %d\n", shield &lt; 50 || hull &lt; 50);
    return 0;
}</code></pre>

    <p class="body-text">
        Each comparison prints as <code>1</code> or <code>0</code> because C has no separate boolean type —
        <code>%d</code> works fine for a relational result.
    </p>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt"><code>shield</code> is 40. What does <code>printf("Shield low: %d\n", shield &lt; 50);</code> print?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Shield low: 0</li>
            <li class="quiz-option">B. Shield low: 40</li>
            <li class="quiz-option">C. Shield low: true</li>
            <li class="quiz-option quiz-correct">D. Shield low: 1 ✓</li>
        </ul>
        <p class="quiz-explanation"><em>40 is less than 50, so the comparison is true, and true prints as 1 with <code>%d</code>.</em></p>
    </div>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Two more readings just came in: <code>int hull = 65;</code> and <code>int shield = 55;</code>. Print
        whether the hull is critical (below 50), whether the shield is critical (below 50), and whether the ship
        is fully safe (both at or above 50):
    </p>

    <pre class="code-block"><code>Hull critical: 0
Shield critical: 0
Safe: 1</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Reading the Warnings">
            <input type="hidden" name="filename"     value="trap_check.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int hull = 65;
    int shield = 55;

    // print &quot;Hull critical: 0&quot;, &quot;Shield critical: 0&quot;, &quot;Safe: 1&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="hull holds 65, shield holds 55. Print exactly, each on its own line:

Hull critical: 0
Shield critical: 0
Safe: 1

&quot;Hull critical&quot; is hull &lt; 50. &quot;Shield critical&quot; is shield &lt; 50. &quot;Safe&quot; is hull &gt;= 50 &amp;&amp; shield &gt;= 50. Use %d — the result of a comparison prints as 1 or 0.">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="A comparison like hull &lt; 50 already evaluates to 1 or 0 — just print it directly with %d, no if statement needed yet.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int hull = 65;
    int shield = 55;

    printf(&quot;Hull critical: %d\n&quot;, hull &lt; 50);
    printf(&quot;Shield critical: %d\n&quot;, shield &lt; 50);
    printf(&quot;Safe: %d\n&quot;, hull &gt;= 50 &amp;&amp; shield &gt;= 50);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm2', 'lesson' => 'lesson01']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does the comparison <code>5 == 5</code> evaluate to in C?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. true</li>
            <li class="quiz-option">B. "yes"</li>
            <li class="quiz-option quiz-correct">C. 1 ✓</li>
            <li class="quiz-option">D. 5</li>
        </ul>
        <p class="quiz-explanation"><em>C has no separate boolean type — a true comparison evaluates to the int 1, and a false one to 0.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What's the key difference between <code>=</code> and <code>==</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They mean exactly the same thing</li>
            <li class="quiz-option quiz-correct">B. = assigns a value; == compares two values ✓</li>
            <li class="quiz-option">C. == is only for text, = is only for numbers</li>
            <li class="quiz-option">D. = is used in loops, == is used in functions</li>
        </ul>
        <p class="quiz-explanation"><em>A single = stores a value into a variable. A double == checks whether two values are equal — mixing them up is one of the most common C mistakes.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. <code>shield &gt; 50 &amp;&amp; hull &gt; 50</code> is true only when:</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Either condition is true</li>
            <li class="quiz-option quiz-correct">B. Both conditions are true ✓</li>
            <li class="quiz-option">C. Neither condition is true</li>
            <li class="quiz-option">D. shield is greater than hull</li>
        </ul>
        <p class="quiz-explanation"><em>&amp;&amp; (AND) only evaluates to true when every condition joined by it is also true.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. <code>shield &lt; 50 || hull &lt; 50</code> is true when:</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Only when both are below 50</li>
            <li class="quiz-option">B. Never — || always evaluates to 0</li>
            <li class="quiz-option quiz-correct">C. At least one of them is below 50 ✓</li>
            <li class="quiz-option">D. Only when neither is below 50</li>
        </ul>
        <p class="quiz-explanation"><em>|| (OR) is true the moment any one of its conditions is true — it does not need all of them.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does <code>!(shield &lt; 50)</code> mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. True when shield is less than 50</li>
            <li class="quiz-option">B. It causes a compiler error</li>
            <li class="quiz-option quiz-correct">C. True when shield is 50 or more ✓</li>
            <li class="quiz-option">D. True only when shield is above 50</li>
        </ul>
        <p class="quiz-explanation"><em>! flips a condition's result: it turns a true (shield is low) into false, and false into true.</em></p>
    </div>

</div>
