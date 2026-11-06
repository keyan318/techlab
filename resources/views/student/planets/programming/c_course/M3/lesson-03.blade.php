{{-- M3 · Lesson 3.3: Reporting Back — content fragment.
Syllabus: CC102 Unit 3 — Functions (return values). --}}

<div class="lesson-fragment" data-module="m3" data-lesson="03">

    <h1 class="lesson-heading">Lesson 3.3: Reporting Back</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write a function that computes a value and sends it back with
        <code>return</code>, and use that value in the code that called it.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <code>void</code> function does something but hands nothing back. A function with a real return type —
        <code>int</code>, <code>float</code>, and so on — computes a value and sends it back to whoever called
        it with <code>return</code>:
    </p>

    <pre class="code-block"><code>int functionName(int a, int b) {
    return a + b;
}</code></pre>

    <p class="body-text">
        The type before the function's name — <code>int</code> here — promises what kind of value
        <code>return</code> will send back. Calling the function is now an <em>expression</em>: it stands in for
        whatever value it returns, so you can store it in a variable or print it directly.
    </p>

    <pre class="code-block"><code>int total = functionName(3, 4);   // total now holds 7</code></pre>

    <p class="body-text">
        <code>return</code> also immediately exits the function — any code written after it in the same block
        never runs.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What happens to code written after <code>return</code> in the same block?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It runs afterwards</li>
            <li class="quiz-option">B. It runs first</li>
            <li class="quiz-option">C. The compiler moves it above <code>return</code></li>
            <li class="quiz-option quiz-correct">D. It never runs ✓</li>
        </ul>
        <p class="quiz-explanation"><em><code>return</code> exits the function immediately, so nothing after it in that block runs.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        Picture a <strong>calculator machine</strong>. You drop in two numbers, 3 and 4, and it hands back <strong>one slip of paper</strong> with the answer: 7. Handing back that slip is <code>return</code>.
    </p>

    <p class="body-text">
        You can keep the slip in a labelled jar (<code>int total = add(3, 4);</code>) or read it out loud straight away (<code>printf</code>). The <code>int</code> in front of the function's name is a promise about what kind of answer will be on the slip.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 355" role="img" aria-label="A calculator machine called add with inputs a = 3 and b = 4. It runs return a + b and hands back a slip that says 7. You can keep it in a jar called total, with total = add(3, 4), or show it on the screen right away.">
            <circle cx="270" cy="34" r="17" fill="#fff3c4" stroke="#c99a00" stroke-width="2"/><text x="270" y="41" font-size="18" fill="#7a5a00" font-weight="800" text-anchor="middle">3</text><text x="296" y="40" font-size="13" fill="#5b6b86" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">a</text><polygon points="244,60 296,60 280,100 260,100" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><circle cx="370" cy="34" r="17" fill="#fff3c4" stroke="#c99a00" stroke-width="2"/><text x="370" y="41" font-size="18" fill="#7a5a00" font-weight="800" text-anchor="middle">4</text><text x="396" y="40" font-size="13" fill="#5b6b86" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">b</text><polygon points="344,60 396,60 380,100 360,100" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><rect x="190" y="100" width="260" height="130" rx="16" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><text x="320" y="152" font-size="20" fill="#14306b" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">add(a, b)</text><text x="320" y="178" font-size="12" fill="#5b6b86" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">int add(int a, int b)</text><circle cx="214" cy="124" r="6" fill="#86e0b0"/><rect x="300" y="230" width="40" height="18" rx="3" fill="#2f6fe0"/><text x="352" y="246" font-size="12" fill="#9c1f4c" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">return a + b;</text><rect x="288" y="252" width="64" height="46" rx="6" fill="#fff" stroke="#e8457c" stroke-width="2"/><text x="320" y="284" font-size="24" fill="#9c1f4c" font-weight="800" text-anchor="middle">7</text><line x1="284" y1="276" x2="191.0" y2="276.0" stroke="#e8457c" stroke-width="3"/><polygon points="180,276 191.0,282.0 191.0,270.0" fill="#e8457c"/><line x1="356" y1="276" x2="447.0" y2="276.0" stroke="#e8457c" stroke-width="3"/><polygon points="458,276 447.0,270.0 447.0,282.0" fill="#e8457c"/><rect x="66" y="252" width="104" height="72" rx="14" fill="#fff8ee" stroke="#8a4b00" stroke-width="2"/><rect x="74" y="240" width="88" height="14" rx="4" fill="#f08c1a" stroke="#8a4b00" stroke-width="2"/><rect x="84" y="262" width="68" height="22" rx="4" fill="#fff" stroke="#8a4b00" stroke-width="1.5"/><text x="118" y="278" font-size="13" fill="#8a4b00" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">total</text><text x="118" y="312" font-size="18" fill="#8a4b00" font-weight="800" text-anchor="middle">7</text><rect x="462" y="252" width="158" height="64" rx="10" fill="#1b2440"/><text x="476" y="290" font-size="18" fill="#86e0b0" font-weight="800" font-family="ui-monospace, Menlo, Consolas, monospace">&gt; 7</text><text x="118" y="346" font-size="11" fill="#8a4b00" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">keep it: total = add(3, 4);</text><text x="541" y="346" font-size="12" fill="#14306b" font-weight="700" text-anchor="middle">or show it right away</text>
        </svg>
    </figure>

    <p class="body-text">
        Once the slip comes out, the machine is finished. Any code written after <code>return</code> in the same function never runs. A <code>void</code> function from Lesson 3.1 is like a doorbell: it does something, but hands nothing back.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What happens to code written after <code>return</code> inside the same function?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It runs after the value is sent back</li>
            <li class="quiz-option">B. It runs first, then the value is sent</li>
            <li class="quiz-option">C. It runs only if the value is zero</li>
            <li class="quiz-option quiz-correct">D. It never runs, the machine is done ✓</li>
        </ul>
        <p class="quiz-explanation"><em><code>return</code> sends the value back and ends the function right away. Nothing after it in that block gets a turn.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="shieldGap computes a value and returns it; main stores that value in a variable">
            <rect x="20" y="55" width="170" height="60" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="105" y="82" text-anchor="middle" font-size="12.5" font-weight="700" fill="#4a35b8">int gap = shieldGap</text>
            <text x="105" y="98" text-anchor="middle" font-size="12.5" font-weight="700" fill="#4a35b8">(35, 100);</text>

            <rect x="400" y="30" width="220" height="90" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="510" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">int shieldGap(cur, tgt)</text>
            <text x="510" y="78" text-anchor="middle" font-size="12" fill="#14306b">return tgt - cur;</text>
            <text x="510" y="98" text-anchor="middle" font-size="11" fill="#14306b">→ sends 65 back</text>

            <line x1="190" y1="75" x2="396" y2="70" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="400,70 388,64 388,76" fill="#7c5cff"/>
            <line x1="396" y1="95" x2="190" y2="95" stroke="#a68cff" stroke-width="2.5" stroke-dasharray="4,3"/>
            <polygon points="190,95 202,89 202,101" fill="#a68cff"/>

            <text x="320" y="150" text-anchor="middle" font-size="13" fill="#475569">gap now holds 65 — the returned value is stored, not just printed once.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int addParts(int a, int b) {
    return a + b;
}

int main() {
    int total = addParts(12, 30);
    printf("Total: %d\n", total);
    return 0;
}</code></pre>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">Which line in the code example catches the returned value in a box?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>return a + b;</code></li>
            <li class="quiz-option">B. <code>printf("Total: %d\n", total);</code></li>
            <li class="quiz-option quiz-correct">C. <code>int total = addParts(12, 30);</code> ✓</li>
            <li class="quiz-option">D. <code>int addParts(int a, int b) {</code></li>
        </ul>
        <p class="quiz-explanation"><em>The call stands in for its returned value, and <code>int total = ...</code> stores it.</em></p>
    </div>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Write a function <code>shieldGap</code> that takes two <code>int</code> parameters, <code>current</code>
        and <code>target</code>, and returns <code>target - current</code>. In <code>main</code>, call it with
        <code>shieldGap(35, 100)</code>, store the result in a variable, and print exactly:
    </p>

    <pre class="code-block"><code>Gap: 65</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Reporting Back">
            <input type="hidden" name="filename"     value="shield_gap.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

// define shieldGap(int current, int target) here, returning target - current

int main() {
    // call shieldGap(35, 100), store it, print &quot;Gap: &quot; + the result

    return 0;
}
">
            <input type="hidden" name="instructions" value="Define shieldGap(int current, int target) that returns target - current (an int).

In main, call shieldGap(35, 100), store the result in a variable, and print exactly:
Gap: 65">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="int shieldGap(int current, int target) { return target - current; } — the return type before the name (int) must match what return sends back. Store the call's result with int gap = shieldGap(35, 100);.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int shieldGap(int current, int target) {
    return target - current;
}

int main() {
    int gap = shieldGap(35, 100);
    printf(&quot;Gap: %d\n&quot;, gap);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm3', 'lesson' => 'lesson03']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does the return statement do inside a function?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It prints a value to the screen</li>
            <li class="quiz-option">B. It declares a new variable</li>
            <li class="quiz-option">C. It restarts the function from the top</li>
            <li class="quiz-option quiz-correct">D. It sends a value back to the caller ✓</li>
        </ul>
        <p class="quiz-explanation"><em>return hands a value back to the caller and immediately ends the function — nothing after it in the same block runs.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In <code>int addParts(int a, int b) { ... }</code>, what does the first <code>int</code> (before the function name) mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The function takes an int as its only parameter</li>
            <li class="quiz-option quiz-correct">B. The function's return value will be an int ✓</li>
            <li class="quiz-option">C. It is required but has no real meaning</li>
            <li class="quiz-option">D. The function can only be called once</li>
        </ul>
        <p class="quiz-explanation"><em>The type written before a function's name declares its return type — the kind of value its return statement must send back.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. After <code>int total = addParts(12, 30);</code>, what does total hold?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing — void functions can't be stored</li>
            <li class="quiz-option">B. The text "addParts(12, 30)"</li>
            <li class="quiz-option quiz-correct">C. 42, the value addParts returned ✓</li>
            <li class="quiz-option">D. 12, the first argument only</li>
        </ul>
        <p class="quiz-explanation"><em>A function call that returns a value acts as an expression — total is assigned whatever addParts's return statement sent back, which is 12 + 30 = 42.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why couldn't the void functions from Lessons 3.1-3.2 hand a computed value back to main to store and reuse?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. void means no value is sent back ✓</li>
            <li class="quiz-option">B. void functions cannot take parameters</li>
            <li class="quiz-option">C. void functions can only be called once</li>
            <li class="quiz-option">D. void functions cannot use printf</li>
        </ul>
        <p class="quiz-explanation"><em>void functions perform an action but never send anything back — only a function with a real return type (like int) can hand a computed value to its caller.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If a function is declared to return int, but its return statement tries to send back a value of the wrong kind of meaning (like returning cur - tgt instead of tgt - cur), what actually goes wrong?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It fails to compile, since C checks the logic</li>
            <li class="quiz-option">B. It runs, but C flips the result to positive</li>
            <li class="quiz-option quiz-correct">C. It runs fine but gives the wrong answer ✓</li>
            <li class="quiz-option">D. It runs, but the function returns 0 instead</li>
        </ul>
        <p class="quiz-explanation"><em>C checks that a returned value matches the declared type, but it has no idea what the function is supposed to mean — swapping cur - tgt for tgt - cur would compile fine and just give a wrong (negative) gap.</em></p>
    </div>

</div>
