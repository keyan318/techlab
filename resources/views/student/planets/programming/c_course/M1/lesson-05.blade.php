{{-- M1 · Lesson 1.5: Doing the Math — content fragment.
Syllabus: CC102 Unit 1 — Core Syntax (operators). --}}

<div class="lesson-fragment" data-module="m1" data-lesson="05">

    <h1 class="lesson-heading">Lesson 1.5: Doing the Math</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can use C's arithmetic operators — including integer division and the
        modulus operator — to compute and print values.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">C's five basic arithmetic operators:</p>

    <ul class="body-list">
        <li><strong><code>+</code></strong> addition, <strong><code>-</code></strong> subtraction, <strong><code>*</code></strong> multiplication</li>
        <li><strong><code>/</code></strong> division — between two <code>int</code> values, this <em>truncates</em>: <code>7 / 2</code> is <code>3</code>, not <code>3.5</code>. The remainder is simply dropped.</li>
        <li><strong><code>%</code></strong> modulus — the <em>remainder</em> left over after integer division: <code>7 % 2</code> is <code>1</code>.</li>
    </ul>

    <p class="body-text">
        <code>/</code> and <code>%</code> together answer one question in two parts: "how many whole times does this
        divide, and what's left over?" <code>47 / 5</code> is <code>9</code> whole times, and <code>47 % 5</code> is
        <code>2</code> left over — because <code>9 × 5 + 2 = 47</code>.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What is <code>17 / 5</code> when both are <code>int</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 3.4</li>
            <li class="quiz-option quiz-correct">B. 3 ✓</li>
            <li class="quiz-option">C. 4</li>
            <li class="quiz-option">D. 2</li>
        </ul>
        <p class="quiz-explanation"><em>Integer division drops the remainder: 5 goes into 17 three whole times (the 2 left over is what <code>%</code> gives).</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        You have <strong>47 stickers</strong> to share fairly between <strong>5 friends</strong>. Everyone gets 9, and 2 are still in your hand. You can't cut a sticker in half, so those 2 just stay left over.
    </p>

    <p class="body-text">
        That is exactly what C does with whole numbers. <code>47 / 5</code> is <strong>9</strong>: how many each friend gets. <code>47 % 5</code> is <strong>2</strong>: how many are left over.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 295" role="img" aria-label="47 stickers shared between 5 friends. Each friend gets 9 stickers, and 2 are left over. 47 divided by 5 is 9 each, 47 modulo 5 is 2 left over, and 9 times 5 plus 2 is 47.">
            <rect x="20" y="40" width="92" height="135" rx="12" fill="#f5f9ff" stroke="#2f6fe0" stroke-width="2"/><text x="66" y="62" font-size="12" fill="#14306b" font-weight="700" text-anchor="middle">friend 1</text><circle cx="42" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="66" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="90" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="42" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="66" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="90" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="42" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="66" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="90" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><text x="66" y="200" font-size="20" fill="#14306b" font-weight="800" text-anchor="middle">9</text><rect x="122" y="40" width="92" height="135" rx="12" fill="#f5f9ff" stroke="#2f6fe0" stroke-width="2"/><text x="168" y="62" font-size="12" fill="#14306b" font-weight="700" text-anchor="middle">friend 2</text><circle cx="144" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="168" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="192" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="144" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="168" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="192" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="144" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="168" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="192" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><text x="168" y="200" font-size="20" fill="#14306b" font-weight="800" text-anchor="middle">9</text><rect x="224" y="40" width="92" height="135" rx="12" fill="#f5f9ff" stroke="#2f6fe0" stroke-width="2"/><text x="270" y="62" font-size="12" fill="#14306b" font-weight="700" text-anchor="middle">friend 3</text><circle cx="246" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="270" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="294" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="246" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="270" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="294" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="246" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="270" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="294" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><text x="270" y="200" font-size="20" fill="#14306b" font-weight="800" text-anchor="middle">9</text><rect x="326" y="40" width="92" height="135" rx="12" fill="#f5f9ff" stroke="#2f6fe0" stroke-width="2"/><text x="372" y="62" font-size="12" fill="#14306b" font-weight="700" text-anchor="middle">friend 4</text><circle cx="348" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="372" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="396" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="348" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="372" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="396" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="348" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="372" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="396" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><text x="372" y="200" font-size="20" fill="#14306b" font-weight="800" text-anchor="middle">9</text><rect x="428" y="40" width="92" height="135" rx="12" fill="#f5f9ff" stroke="#2f6fe0" stroke-width="2"/><text x="474" y="62" font-size="12" fill="#14306b" font-weight="700" text-anchor="middle">friend 5</text><circle cx="450" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="474" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="498" cy="88" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="450" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="474" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="498" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="450" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="474" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="498" cy="136" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><text x="474" y="200" font-size="20" fill="#14306b" font-weight="800" text-anchor="middle">9</text><rect x="535" y="40" width="85" height="135" rx="12" fill="#fff5f8" stroke="#e8457c" stroke-width="2" stroke-dasharray="6,4"/><text x="577" y="62" font-size="12" fill="#9c1f4c" font-weight="700" text-anchor="middle">left over</text><circle cx="562" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><circle cx="592" cy="112" r="8" fill="#ffc94d" stroke="#c99a00" stroke-width="1.5"/><text x="577" y="200" font-size="20" fill="#9c1f4c" font-weight="800" text-anchor="middle">2</text><text x="320" y="225" font-size="13" fill="#5b6b86" text-anchor="middle" font-style="italic">47 stickers, shared fairly between 5 friends</text><rect x="20" y="245" width="190" height="38" rx="19.0" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="1.8"/><text x="115.0" y="268.68" font-size="13" fill="#14306b" text-anchor="middle"><tspan font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="800">47 / 5</tspan> → 9 each</text><rect x="225" y="245" width="190" height="38" rx="19.0" fill="#ffe3ec" stroke="#e8457c" stroke-width="1.8"/><text x="320.0" y="268.68" font-size="13" fill="#9c1f4c" text-anchor="middle"><tspan font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="800">47 % 5</tspan> → 2 left over</text><rect x="430" y="245" width="190" height="38" rx="19.0" fill="#e9f7ef" stroke="#2fa56b" stroke-width="1.8"/><text x="525.0" y="268.68" font-size="13" fill="#1d6b43" text-anchor="middle">check: 9 × 5 + 2 = 47</text>
        </svg>
    </figure>

    <p class="body-text">
        <strong>Where the picture breaks:</strong> if you really want 9.4, you need numbers with a decimal point (<code>float</code>), not whole-number <code>int</code>s.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">You share 23 stickers between 4 friends. What does <code>23 % 4</code> tell you?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Each friend gets exactly 5 stickers</li>
            <li class="quiz-option quiz-correct">B. 3 stickers are left in your hand ✓</li>
            <li class="quiz-option">C. Each friend gets 5.75 stickers</li>
            <li class="quiz-option">D. 4 friends get no stickers at all</li>
        </ul>
        <p class="quiz-explanation"><em>4 friends × 5 stickers = 20, so 3 are left over. <code>%</code> gives the leftover; <code>/</code> gives the 5.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 190" role="img" aria-label="47 divided by 5: quotient 9 from the slash operator, remainder 2 from the percent operator">
            <rect x="20" y="30" width="150" height="70" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="16" font-weight="700" fill="#4a35b8">47 parts</text>
            <text x="95" y="80" text-anchor="middle" font-size="12" fill="#4a35b8">5 crews</text>

            <rect x="245" y="30" width="170" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="330" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">47 / 5 = 9</text>
            <text x="330" y="75" text-anchor="middle" font-size="11.5" fill="#14306b">each crew gets 9</text>
            <text x="330" y="91" text-anchor="middle" font-size="11.5" fill="#14306b">(remainder dropped)</text>

            <rect x="470" y="30" width="150" height="70" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="545" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#8a4b00">47 % 5 = 2</text>
            <text x="545" y="75" text-anchor="middle" font-size="11.5" fill="#8a4b00">2 spares</text>
            <text x="545" y="91" text-anchor="middle" font-size="11.5" fill="#8a4b00">left over</text>

            <line x1="170" y1="65" x2="241" y2="65" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="245,65 233,59 233,71" fill="#7c5cff"/>
            <line x1="415" y1="65" x2="466" y2="65" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="470,65 458,59 458,71" fill="#7c5cff"/>

            <text x="330" y="155" text-anchor="middle" font-size="13" fill="#475569">9 × 5 + 2 = 47 — the quotient and remainder always add back up.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int parts = 47;
    int crews = 5;

    printf("Quotient: %d\n", parts / crews);
    printf("Remainder: %d\n", parts % crews);
    return 0;
}</code></pre>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">What does the code example print on the “Quotient:” line?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Quotient: 2</li>
            <li class="quiz-option quiz-correct">B. Quotient: 9 ✓</li>
            <li class="quiz-option">C. Quotient: 9.4</li>
            <li class="quiz-option">D. Quotient: 47</li>
        </ul>
        <p class="quiz-explanation"><em><code>parts / crews</code> is <code>47 / 5</code>, which is 9 whole times. The 2 left over goes on the “Remainder:” line.</em></p>
    </div>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Split the salvage. Declare <code>int parts = 47;</code> and <code>int crews = 5;</code>, then print, each on
        its own line, exactly:
    </p>

    <pre class="code-block"><code>Total: 47
Each: 9
Left: 2</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Doing the Math">
            <input type="hidden" name="filename"     value="split_salvage.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int parts = 47;
    int crews = 5;

    // print &quot;Total: 47&quot;, &quot;Each: 9&quot; (parts / crews), &quot;Left: 2&quot; (parts % crews)

    return 0;
}
">
            <input type="hidden" name="instructions" value="parts holds 47, crews holds 5. Print exactly, each on its own line:

Total: 47
Each: 9
Left: 2

&quot;Each&quot; is parts / crews (integer division). &quot;Left&quot; is parts % crews (the remainder).">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="/ between two ints drops the remainder — 47 / 5 is 9. % gives you just the remainder — 47 % 5 is 2. Use %d for both, since the results are whole numbers.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int parts = 47;
    int crews = 5;

    printf(&quot;Total: %d\n&quot;, parts);
    printf(&quot;Each: %d\n&quot;, parts / crews);
    printf(&quot;Left: %d\n&quot;, parts % crews);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm1', 'lesson' => 'lesson05']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does 7 / 2 evaluate to when both are int?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 3.5</li>
            <li class="quiz-option">B. 4</li>
            <li class="quiz-option">C. 1</li>
            <li class="quiz-option quiz-correct">D. 3 ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Integer division truncates — the remainder is dropped, so 7 / 2 is 3, not 3.5.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does the % operator compute?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. What percent one number is of another</li>
            <li class="quiz-option">B. The square root of the left number</li>
            <li class="quiz-option quiz-correct">C. The remainder after integer division ✓</li>
            <li class="quiz-option">D. The average of the two numbers</li>
        </ul>
        <p class="quiz-explanation"><em>% is the modulus operator — it gives you what division leaves behind, not a percentage.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is 47 % 5?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. 2 ✓</li>
            <li class="quiz-option">B. 9</li>
            <li class="quiz-option">C. 5</li>
            <li class="quiz-option">D. 0</li>
        </ul>
        <p class="quiz-explanation"><em>47 divides into 5 nine whole times (9 × 5 = 45), leaving a remainder of 2.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. If quotient = 47 / 5 and remainder = 47 % 5, what does quotient × 5 + remainder equal?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 45</li>
            <li class="quiz-option quiz-correct">B. 47 ✓</li>
            <li class="quiz-option">C. 50</li>
            <li class="quiz-option">D. 52</li>
        </ul>
        <p class="quiz-explanation"><em>The quotient and remainder always reconstruct the original number: 9 × 5 + 2 = 47.</em></p>
    </div>

</div>
