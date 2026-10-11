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

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Forty-seven good parts came out of the wreck. There are five repair jobs waiting. Rivet works it out on
        the back of his hand.
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Nine each. Two spares nobody gets yet."
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Do that on paper every time and you'll get it wrong at three in the morning when
        it actually matters. Give it to the console."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> grins and holds up two fingers. "Two signs. <code>/</code> gives me how many times
        it goes in. <code>%</code> gives me what's left over. Nine, and two. It never rounds wrong and it never
        forgets the spares."
    </p>

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
            <li class="quiz-option quiz-correct">B. 3 ✓</li>
            <li class="quiz-option">C. 4</li>
            <li class="quiz-option">D. 1</li>
        </ul>
        <p class="quiz-explanation"><em>Integer division truncates — the remainder is dropped, so 7 / 2 is 3, not 3.5.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does the % operator compute?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A percentage</li>
            <li class="quiz-option quiz-correct">B. The remainder left over after integer division ✓</li>
            <li class="quiz-option">C. The square root</li>
            <li class="quiz-option">D. The average</li>
        </ul>
        <p class="quiz-explanation"><em>% is the modulus operator — it gives you what division leaves behind, not a percentage.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is 47 % 5?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 9</li>
            <li class="quiz-option quiz-correct">B. 2 ✓</li>
            <li class="quiz-option">C. 5</li>
            <li class="quiz-option">D. 0</li>
        </ul>
        <p class="quiz-explanation"><em>47 divides into 5 nine whole times (9 × 5 = 45), leaving a remainder of 2.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why did Rivet need both / and % to split the salvage fairly?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. / alone is always wrong</li>
            <li class="quiz-option quiz-correct">B. / gives the whole share each crew gets, and % gives what's left over that / alone would silently drop ✓</li>
            <li class="quiz-option">C. % alone would have been enough</li>
            <li class="quiz-option">D. They do the exact same thing</li>
        </ul>
        <p class="quiz-explanation"><em>/ and % answer one question in two parts: how many whole times, and what remains — together, not separately.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If quotient = 47 / 5 and remainder = 47 % 5, what does quotient × 5 + remainder equal?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 45</li>
            <li class="quiz-option quiz-correct">B. 47 ✓</li>
            <li class="quiz-option">C. 50</li>
            <li class="quiz-option">D. It's unpredictable</li>
        </ul>
        <p class="quiz-explanation"><em>The quotient and remainder always reconstruct the original number: 9 × 5 + 2 = 47.</em></p>
    </div>

</div>
