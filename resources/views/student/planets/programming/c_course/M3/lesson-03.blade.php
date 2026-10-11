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

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        The shell is charging again, and Astro wants to know how far off full it is — not printed on a screen
        he has to stand and read, but as a number the rest of the program can actually use.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Printing it isn't the same as knowing it. It flashes up, it's gone, and the
        program is no wiser than before. I want the answer <em>handed back</em>, so the next job can use it."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Then the job has to give something back instead of just doing something." He
        taps the word on the board: <code>return</code>. "It works it out, hands you the number, and it's
        finished. You catch it in a box and it's yours."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And if it works it out backwards?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Then it hands you a wrong number, very confidently, and nothing complains.
        Which is why you check it."
    </p>

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
            <li class="quiz-option quiz-correct">B. It sends a value back to whoever called the function, and exits it ✓</li>
            <li class="quiz-option">C. It declares a new variable</li>
            <li class="quiz-option">D. It restarts the function from the top</li>
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
        <p class="quiz-prompt">Q4. Why couldn't Astro's earlier void functions (Lessons 3.1-3.2) hand a computed value back to main to store and reuse?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. void functions cannot take parameters</li>
            <li class="quiz-option quiz-correct">B. void means no return value is sent back — the function can only act (like printing), not report a result ✓</li>
            <li class="quiz-option">C. void functions cannot be called more than once</li>
            <li class="quiz-option">D. There is no real difference between void and int functions</li>
        </ul>
        <p class="quiz-explanation"><em>void functions perform an action but never send anything back — only a function with a real return type (like int) can hand a computed value to its caller.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If a function is declared to return int, but its return statement tries to send back a value of the wrong kind of meaning (like returning cur - tgt instead of tgt - cur), what actually goes wrong?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The program refuses to compile — C checks the logic automatically</li>
            <li class="quiz-option quiz-correct">B. It compiles and runs fine, but computes the wrong answer — C only checks the type, not whether the logic is correct ✓</li>
            <li class="quiz-option">C. Nothing — return always produces the correct result regardless of the expression</li>
            <li class="quiz-option">D. The function silently returns 0 instead</li>
        </ul>
        <p class="quiz-explanation"><em>C checks that a returned value matches the declared type, but it has no idea what the function is supposed to mean — swapping cur - tgt for tgt - cur would compile fine and just give a wrong (negative) gap.</em></p>
    </div>

</div>
