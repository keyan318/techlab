{{-- M2 · Lesson 2.4: Standing Watch — content fragment.
Syllabus: CC102 Unit 2 — Looping Statements (while, do-while). --}}

<div class="lesson-fragment" data-module="m2" data-lesson="04">

    <h1 class="lesson-heading">Lesson 2.4: Standing Watch</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write a <code>while</code> loop and a <code>do-while</code> loop, and
        explain the one situation where they behave differently.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <code>while</code> loop repeats a block of code as long as its condition stays true, checking
        <em>before</em> each run:
    </p>

    <pre class="code-block"><code>while (condition) {
    // repeats while condition is true
}</code></pre>

    <p class="body-text">
        If the condition is false the very first time, a <code>while</code> loop's body never runs at all. A
        <code>do-while</code> loop is nearly identical, but checks <em>after</em> each run instead — so its body
        always executes at least once, even if the condition was false from the start:
    </p>

    <pre class="code-block"><code>do {
    // runs once, THEN checks condition
} while (condition);</code></pre>

    <p class="body-text">
        Whichever loop you use, something inside the body must eventually make the condition false — otherwise
        the loop never stops.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Which loop checks its condition after each run, so its body always runs at least once?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>while</code></li>
            <li class="quiz-option">B. <code>for</code></li>
            <li class="quiz-option quiz-correct">C. <code>do-while</code> ✓</li>
            <li class="quiz-option">D. <code>if</code></li>
        </ul>
        <p class="quiz-explanation"><em>A <code>do-while</code> runs the body first and checks afterwards. A <code>while</code> checks first, so it may never run.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        A <code>while</code> loop is a <strong>bouncer at a party</strong>. The bouncer checks your ticket <em>before</em> you go in, every time you come back to the door. No ticket the very first time? You never get in at all.
    </p>

    <p class="body-text">
        A <code>do-while</code> loop is a <strong>roller coaster</strong>. You ride first, and only <em>after</em> the ride does someone ask “go again?”. So even if the answer is no, you always get at least one ride.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 350" role="img" aria-label="Left, while: check first. Got a ticket? Yes: enjoy the party, then back to the door. No: go home. No ticket at the door means the party runs 0 times. Right, do-while: go first. Ride the coaster, then go again? Yes loops back, no is done. You always get 1 ride. If nothing changes the answer, the loop never ends.">
            <text x="162" y="36" font-size="15" fill="#4a35b8" font-weight="800" text-anchor="middle"><tspan font-family="ui-monospace, Menlo, Consolas, monospace">while</tspan> · check first</text><line x1="162" y1="46" x2="162.0" y2="59.0" stroke="#7c5cff" stroke-width="3"/><polygon points="162,70 168.0,59.0 156.0,59.0" fill="#7c5cff"/><polygon points="80,106 162,72 244,106 162,140" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/><text x="162" y="111" font-size="13" fill="#4a35b8" font-weight="700" text-anchor="middle">Got a ticket?</text><line x1="162" y1="140" x2="162.0" y2="161.0" stroke="#7c5cff" stroke-width="3"/><polygon points="162,172 168.0,161.0 156.0,161.0" fill="#7c5cff"/><text x="176" y="162" font-size="12" fill="#4a35b8" font-weight="700">yes</text><rect x="97" y="174" width="130" height="40" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/><text x="162" y="199" font-size="14" fill="#4a35b8" font-weight="700" text-anchor="middle">Enjoy the party</text><path d="M97 194 H58 V106 H74" fill="none" stroke="#7c5cff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><polygon points="80,106 69.0,100.0 69.0,112.0" fill="#7c5cff"/><line x1="244" y1="106" x2="287.0" y2="106.0" stroke="#b3261e" stroke-width="3"/><polygon points="298,106 287.0,100.0 287.0,112.0" fill="#b3261e"/><text x="270" y="97" font-size="12" fill="#b3261e" font-weight="700" text-anchor="middle">no</text><text x="270" y="128" font-size="11" fill="#b3261e" font-weight="700" text-anchor="middle">go home</text><rect x="20" y="238" width="285" height="50" rx="25.0" fill="#f3f0ff" stroke="#7c5cff" stroke-width="1.8"/><text x="162.5" y="267.68" font-size="13" fill="#4a35b8" text-anchor="middle"></text><text x="162" y="259" font-size="13" fill="#4a35b8" font-weight="800" text-anchor="middle">No ticket at the door?</text><text x="162" y="277" font-size="13" fill="#4a35b8" text-anchor="middle">The party runs 0 times.</text><line x1="320" y1="20" x2="320" y2="290" stroke="#e3e7ee" stroke-width="2"/><text x="478" y="36" font-size="15" fill="#0b6670" font-weight="800" text-anchor="middle"><tspan font-family="ui-monospace, Menlo, Consolas, monospace">do-while</tspan> · go first</text><line x1="478" y1="46" x2="478.0" y2="59.0" stroke="#0fa3b1" stroke-width="3"/><polygon points="478,70 484.0,59.0 472.0,59.0" fill="#0fa3b1"/><rect x="408" y="72" width="140" height="40" rx="10" fill="#e6f8f9" stroke="#0fa3b1" stroke-width="2"/><text x="478" y="97" font-size="14" fill="#0b6670" font-weight="700" text-anchor="middle">Ride the coaster</text><line x1="478" y1="112" x2="478.0" y2="133.0" stroke="#0fa3b1" stroke-width="3"/><polygon points="478,144 484.0,133.0 472.0,133.0" fill="#0fa3b1"/><polygon points="402,178 478,146 554,178 478,210" fill="#e6f8f9" stroke="#0fa3b1" stroke-width="2"/><text x="478" y="183" font-size="13" fill="#0b6670" font-weight="700" text-anchor="middle">Go again?</text><path d="M402 178 H370 V92 H400" fill="none" stroke="#0fa3b1" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><polygon points="406,92 395.0,86.0 395.0,98.0" fill="#0fa3b1"/><text x="386" y="196" font-size="12" fill="#0b6670" font-weight="700" text-anchor="middle">yes</text><line x1="554" y1="178" x2="599.0" y2="178.0" stroke="#b3261e" stroke-width="3"/><polygon points="610,178 599.0,172.0 599.0,184.0" fill="#b3261e"/><text x="582" y="169" font-size="12" fill="#b3261e" font-weight="700" text-anchor="middle">no</text><text x="582" y="200" font-size="11" fill="#b3261e" font-weight="700" text-anchor="middle">done</text><rect x="335" y="238" width="285" height="50" rx="25.0" fill="#e6f8f9" stroke="#0fa3b1" stroke-width="1.8"/><text x="477.5" y="267.68" font-size="13" fill="#0b6670" text-anchor="middle"></text><text x="478" y="259" font-size="13" fill="#0b6670" font-weight="800" text-anchor="middle">Even if the answer is no,</text><text x="478" y="277" font-size="13" fill="#0b6670" text-anchor="middle">you always get 1 ride.</text><rect x="20" y="304" width="600" height="38" rx="19.0" fill="#fdeaea" stroke="#b3261e" stroke-width="1.8"/><text x="320.0" y="327.68" font-size="13" fill="#7a1a14" text-anchor="middle">Nothing inside ever changes the answer? The loop <tspan font-weight="800">never ends</tspan>.</text>
        </svg>
    </figure>

    <p class="body-text">
        For both loops, something inside has to change the answer eventually: the ticket runs out, you get tired of the coaster. Otherwise you're stuck in the loop forever.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">The condition is false from the very start. What happens?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. while runs 0 times, do-while runs once ✓</li>
            <li class="quiz-option">B. Both loops run once and then they stop</li>
            <li class="quiz-option">C. Both loops skip their body completely</li>
            <li class="quiz-option">D. while runs once, do-while runs 0 times</li>
        </ul>
        <p class="quiz-explanation"><em><code>while</code> checks first, so a false condition means its body never runs. <code>do-while</code> runs the body first, then checks, so it always runs once.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="A while loop checks the condition first, then repeats the body until it becomes false">
            <ellipse cx="90" cy="45" rx="70" ry="26" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="90" y="51" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">charge = 0</text>

            <rect x="220" y="20" width="170" height="50" rx="10" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="305" y="42" text-anchor="middle" font-size="13" font-weight="700" fill="#14663f">charge &lt; 5?</text>
            <text x="305" y="60" text-anchor="middle" font-size="11" fill="#14663f">check BEFORE body</text>

            <rect x="220" y="110" width="170" height="55" rx="10" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="305" y="133" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">print charge;</text>
            <text x="305" y="151" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">charge++;</text>

            <rect x="470" y="20" width="150" height="50" rx="10" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="545" y="50" text-anchor="middle" font-size="13" font-weight="700" fill="#b3261e">false → exit loop</text>

            <line x1="160" y1="45" x2="216" y2="45" stroke="#7c5cff" stroke-width="2.5"/>
            <line x1="390" y1="45" x2="466" y2="45" stroke="#7c5cff" stroke-width="2.5"/>
            <text x="428" y="35" text-anchor="middle" font-size="11" fill="#4a35b8">false</text>
            <line x1="305" y1="70" x2="305" y2="106" stroke="#7c5cff" stroke-width="2.5"/>
            <text x="330" y="90" text-anchor="middle" font-size="11" fill="#4a35b8">true</text>
            <line x1="220" y1="137" x2="150" y2="137" stroke="#7c5cff" stroke-width="2.5"/>
            <line x1="150" y1="137" x2="150" y2="58" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="150,45 143,58 157,58" fill="#7c5cff"/>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int charge = 0;

    while (charge &lt; 5) {
        printf("Charge: %d\n", charge);
        charge++;
    }
    printf("Shield ready\n");
    return 0;
}</code></pre>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the code example, what is the last “Charge:” line before “Shield ready”?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Charge: 4 ✓</li>
            <li class="quiz-option">B. Charge: 5</li>
            <li class="quiz-option">C. Charge: 0</li>
            <li class="quiz-option">D. Charge: 6</li>
        </ul>
        <p class="quiz-explanation"><em>When <code>charge</code> reaches 5, <code>charge &lt; 5</code> is false, so the loop stops before printing 5.</em></p>
    </div>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Report the shield's boost cycle, starting from a partial charge. Declare <code>int level = 2;</code>,
        then print each level from 2 up to and including 5, and finally a completion line:
    </p>

    <pre class="code-block"><code>Level: 2
Level: 3
Level: 4
Level: 5
Boost complete</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Standing Watch">
            <input type="hidden" name="filename"     value="boost_watch.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int level = 2;

    // while level &lt; 6: print &quot;Level: N&quot;, then level++
    // after the loop, print &quot;Boost complete&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="level starts at 2. Use a while loop that keeps going while level &lt; 6:

Print &quot;Level: &quot; followed by the current level, then increase level by 1. After the loop ends, print &quot;Boost complete&quot;.

Expected output:
Level: 2
Level: 3
Level: 4
Level: 5
Boost complete">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="while (level &lt; 6) keeps looping while level is 2, 3, 4, or 5 — it stops as soon as level reaches 6. Print level, then level++; inside the loop, and put the &quot;Boost complete&quot; printf after the closing brace, outside the loop.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int level = 2;

    while (level &lt; 6) {
        printf(&quot;Level: %d\n&quot;, level);
        level++;
    }
    printf(&quot;Boost complete\n&quot;);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm2', 'lesson' => 'lesson04']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. When does a while loop check its condition?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Only after the body has run once</li>
            <li class="quiz-option">B. Only at the very end of the program</li>
            <li class="quiz-option quiz-correct">C. Before every run of the body ✓</li>
            <li class="quiz-option">D. Only on the loop's very first run</li>
        </ul>
        <p class="quiz-explanation"><em>while checks its condition before every iteration, including the first — if it's false immediately, the body never runs at all.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What must happen inside a while loop's body to avoid an infinite loop?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing; while loops stop on their own</li>
            <li class="quiz-option">B. It must contain a printf statement</li>
            <li class="quiz-option">C. It must contain a second while loop</li>
            <li class="quiz-option quiz-correct">D. Something must make the condition false ✓</li>
        </ul>
        <p class="quiz-explanation"><em>A while loop keeps running as long as its condition is true — if nothing in the body ever changes that, it never stops.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is the one key difference between while and do-while?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. do-while cannot use any variables</li>
            <li class="quiz-option quiz-correct">B. do-while always runs at least once ✓</li>
            <li class="quiz-option">C. while counts up, do-while counts down</li>
            <li class="quiz-option">D. do-while checks before the body runs</li>
        </ul>
        <p class="quiz-explanation"><em>do-while runs its body first and checks the condition after, guaranteeing at least one run even if the condition starts false.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. If <code>charge</code> starts at 5 in <code>while (charge &lt; 5) { ... }</code>, how many times does the loop body run?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Exactly once</li>
            <li class="quiz-option">B. Five times</li>
            <li class="quiz-option quiz-correct">C. Zero times ✓</li>
            <li class="quiz-option">D. Forever</li>
        </ul>
        <p class="quiz-explanation"><em>while checks before running — since 5 &lt; 5 is false from the start, the body never executes.</em></p>
    </div>

</div>
