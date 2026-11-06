{{-- M2 · Lesson 2.4: Two Answers at Once — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Pointers — pointers as parameters (returning more than one result). --}}

<div class="lesson-fragment" data-module="m2" data-lesson="04">

    <h1 class="lesson-heading">Lesson 2.4: Two Answers at Once</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can use pointer parameters as <em>output parameters</em> so one function can hand back two results.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <code>return</code> sends back exactly one value. But sometimes a job has two answers. You could write two functions, or you can let one function write its answers straight into the caller's variables, through pointers. Parameters used this way are called <strong>output parameters</strong>.
    </p>

    <pre class="code-block"><code>void shareParts(int parts, int crews, int *each, int *left) {
    *each = parts / crews;     // answer 1
    *left = parts % crews;     // answer 2
}</code></pre>

    <p class="body-text">
        The caller makes empty boxes for the answers and passes their addresses:
    </p>

    <pre class="code-block"><code>int each, left;
shareParts(38, 4, &amp;each, &amp;left);   // each is 9, left is 2</code></pre>

    <p class="body-text">
        Notice the mix: <code>parts</code> and <code>crews</code> are normal parameters going <em>in</em>, while <code>each</code> and <code>left</code> are addresses for answers coming <em>out</em>. A clear name, like <code>each</code> or <code>result</code>, helps whoever reads it see which is which.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        The parts shed next to the depot has 38 good bulbs left in it. Four crews are about to head off down four streets.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Two numbers. How many each crew gets, and how many stay in the shed. I want both, from one job, at the same time."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "A job can only <em>return</em> one thing. But it can write into as many boxes as you give it addresses for! Give it two empty boxes, it fills both, done."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Remind me to check the shed count isn't negative before anyone hands that job a number." <strong>Rivet</strong>: "Noted. Next lesson."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="shareParts takes 38 and 4 in, and writes 9 into each and 2 into left through their addresses">
            <rect x="20" y="30" width="170" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="105" y="56" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">parts = 38</text>
            <text x="105" y="74" text-anchor="middle" font-size="13" fill="#14306b">crews = 4</text>
            <rect x="235" y="60" width="190" height="90" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="330" y="93.5" text-anchor="middle" font-size="15" font-weight="700" fill="#4a35b8">shareParts</text>
            <text x="330" y="110.5" text-anchor="middle" font-size="12" fill="#4a35b8">*each = 38 / 4</text>
            <text x="330" y="127.5" text-anchor="middle" font-size="12" fill="#4a35b8">*left = 38 % 4</text>
            <rect x="470" y="30" width="150" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="545" y="60.5" text-anchor="middle" font-size="15" font-weight="700" fill="#7a5a00">each = 9</text>
            <rect x="470" y="120" width="150" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="545" y="150.5" text-anchor="middle" font-size="15" font-weight="700" fill="#7a5a00">left = 2</text>
            <line x1="190" y1="70" x2="222.0" y2="85.6" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="231,90 222.8,79.3 217.6,90.1" fill="#7c5cff"/>
            <line x1="425" y1="85" x2="457.5" y2="65.2" stroke="#d4a017" stroke-width="2.5"/>
            <polygon points="466,60 452.6,61.1 458.9,71.4" fill="#d4a017"/>
            <line x1="425" y1="125" x2="456.6" y2="136.6" stroke="#d4a017" stroke-width="2.5"/>
            <polygon points="466,140 456.8,130.2 452.7,141.5" fill="#d4a017"/>
            <text x="320" y="195" text-anchor="middle" font-size="13" fill="#475569">Values go in by copy; answers come out through addresses.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

void minMax(int a, int b, int *small, int *big) {
    if (a &lt; b) {
        *small = a;
        *big = b;
    } else {
        *small = b;
        *big = a;
    }
}

int main() {
    int lo, hi;
    minMax(70, 25, &amp;lo, &amp;hi);
    printf("%d to %d\n", lo, hi);     // 25 to 70
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Write <code>void shareParts(int parts, int crews, int *each, int *left)</code> that puts <code>parts / crews</code> into <code>*each</code> and <code>parts % crews</code> into <code>*left</code>. In <code>main</code>, call it with 38 parts and 4 crews and print exactly:
    </p>

    <pre class="code-block"><code>Each crew: 9
Left over: 2</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Two Answers at Once">
            <input type="hidden" name="filename"     value="parts_shed.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

// define void shareParts(int parts, int crews, int *each, int *left) here

int main() {
    int each = 0, left = 0;

    // call shareParts(38, 4, ...) with the addresses of each and left

    printf(&quot;Each crew: %d\n&quot;, each);
    printf(&quot;Left over: %d\n&quot;, left);
    return 0;
}
">
            <input type="hidden" name="instructions" value="38 bulbs, 4 crews. One job, two answers.

Define void shareParts(int parts, int crews, int *each, int *left):
  *each = parts / crews;
  *left = parts % crews;

In main, call shareParts(38, 4, &amp;each, &amp;left); and print exactly:
Each crew: 9
Left over: 2">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="The two answers go out through the pointers, so both lines in the function start with a *: *each = parts / crews; and *left = parts % crews; In main, the call hands over where to write: shareParts(38, 4, &amp;each, &amp;left);">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

void shareParts(int parts, int crews, int *each, int *left) {
    *each = parts / crews;
    *left = parts % crews;
}

int main() {
    int each = 0, left = 0;

    shareParts(38, 4, &amp;each, &amp;left);

    printf(&quot;Each crew: %d\n&quot;, each);
    printf(&quot;Left over: %d\n&quot;, left);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm2', 'lesson' => 'lesson04']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. How many values can a C function send back with <code>return</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. As many as it likes</li>
            <li class="quiz-option">B. Exactly two at most</li>
            <li class="quiz-option quiz-correct">C. Exactly one at most ✓</li>
            <li class="quiz-option">D. One per parameter</li>
        </ul>
        <p class="quiz-explanation"><em>return carries a single value; for more answers, a function writes through pointer parameters.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is an output parameter?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A parameter that prints its value</li>
            <li class="quiz-option">B. The return type of the function</li>
            <li class="quiz-option">C. A global the function changes</li>
            <li class="quiz-option quiz-correct">D. A pointer the answer is written into ✓</li>
        </ul>
        <p class="quiz-explanation"><em>An output parameter is an address the caller passes in so the function can store a result there.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. After <code>shareParts(38, 4, &amp;each, &amp;left);</code>, what are each and left?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. 9 and 2 ✓</li>
            <li class="quiz-option">B. 9.5 and 0</li>
            <li class="quiz-option">C. 4 and 38</li>
            <li class="quiz-option">D. 2 and 9</li>
        </ul>
        <p class="quiz-explanation"><em>38 / 4 is 9 in whole numbers, and 38 % 4 leaves 2 over.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Inside <code>shareParts</code>, which line stores the first answer correctly?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>each = parts / crews;</code></li>
            <li class="quiz-option quiz-correct">B. <code>*each = parts / crews;</code> ✓</li>
            <li class="quiz-option">C. <code>&amp;each = parts / crews;</code></li>
            <li class="quiz-option">D. <code>return parts / crews;</code></li>
        </ul>
        <p class="quiz-explanation"><em>each is an address, so *each is the caller's box; writing to each would only change the local copy of the address.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. In <code>shareParts(int parts, int crews, int *each, int *left)</code>, which parameters carry values <em>in</em>?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. parts and crews ✓</li>
            <li class="quiz-option">B. each and left</li>
            <li class="quiz-option">C. parts and left</li>
            <li class="quiz-option">D. crews and each</li>
        </ul>
        <p class="quiz-explanation"><em>parts and crews are normal ints copied in; each and left are addresses for the answers coming out.</em></p>
    </div>

</div>
