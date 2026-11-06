{{-- M3 · Lesson 3.3: Handing Over the Street — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Arrays — arrays as function parameters. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="03">

    <h1 class="lesson-heading">Lesson 3.3: Handing Over the Street</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can pass an array and its size into a function, and explain why a function can change the caller's array even though C passes by value.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        To hand an array to a function, write the parameter with empty square brackets, and pass the number of elements as a second parameter:
    </p>

    <pre class="code-block"><code>int streetPower(int lamps[], int n) {
    int total = 0;
    for (int i = 0; i &lt; n; i++) {
        total += lamps[i];
    }
    return total;
}

streetPower(lamps, 6);      // no [] in the call</code></pre>

    <p class="body-text">
        Why the extra <code>n</code>? A function receiving an array cannot tell how long it is. Always send the size along with it.
    </p>

    <p class="body-text">
        Here is the surprise. Arrays are <em>not</em> copied. When you pass an array, C actually passes the <strong>address of its first element</strong>, the same idea as Module 2. So the function works on the caller's real array, and any change it makes stays:
    </p>

    <pre class="code-block"><code>void relight(int lamps[], int n) {
    for (int i = 0; i &lt; n; i++) {
        if (lamps[i] == 0) {
            lamps[i] = 50;       // changes main's array
        }
    }
}</code></pre>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Rivet has written the repair as one job, and the totals as another. Now the box needs to hand the whole street to each job.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Two jobs. One relights every dead lamp to half power. One tells me the street's total, after the repair. Give each job the whole street."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "If the repair job only gets a copy, it fixes a copy, and the real lamps stay dark. We learned that one at the tram junction."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Not with a street! Hand over a street and C hands over where it starts. The job walks down the real street. Whatever it fixes, stays fixed."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="main passes the address of lamps[0] and the size to relight, which changes the real lamps, then to streetPower, which sums them to 310">
            <rect x="20" y="70" width="160" height="70" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="100" y="101.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">main()</text>
            <text x="100" y="119.5" text-anchor="middle" font-size="13" fill="#14306b" font-family="ui-monospace, monospace">lamps, 6</text>
            <rect x="300" y="15" width="320" height="60" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="460" y="41.5" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">relight(lamps, 6)</text>
            <text x="460" y="58.5" text-anchor="middle" font-size="12" fill="#4a35b8">0s become 50 in the REAL array</text>
            <rect x="300" y="125" width="320" height="60" rx="12" fill="#e7f7ee" stroke="#1f9d55" stroke-width="2"/>
            <text x="460" y="151.5" text-anchor="middle" font-size="14" font-weight="700" fill="#14532d">streetPower(lamps, 6)</text>
            <text x="460" y="168.5" text-anchor="middle" font-size="12" fill="#14532d">30+50+55+80+50+45 = 310</text>
            <line x1="180" y1="90" x2="286.5" y2="53.3" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="296,50 282.7,48.2 286.6,59.6" fill="#7c5cff"/>
            <line x1="180" y1="120" x2="286.3" y2="147.5" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="296,150 285.9,141.2 282.9,152.8" fill="#7c5cff"/>
            <text x="238" y="202" text-anchor="middle" font-size="12" fill="#475569">an array is passed as the address of its first element</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

void dimAll(int lamps[], int n) {
    for (int i = 0; i &lt; n; i++) {
        lamps[i] = lamps[i] / 2;
    }
}

int main() {
    int lamps[3] = {40, 60, 80};
    dimAll(lamps, 3);
    printf("%d %d %d\n", lamps[0], lamps[1], lamps[2]);   // 20 30 40
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Write <code>void relight(int lamps[], int n)</code> that sets every 0 to 50, and <code>int streetPower(int lamps[], int n)</code> that returns the total. In <code>main</code>, with <code>int lamps[6] = {30, 0, 55, 80, 0, 45};</code>, call <code>relight</code>, then print the total. Exactly:
    </p>

    <pre class="code-block"><code>Street power: 310</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Handing Over the Street">
            <input type="hidden" name="filename"     value="hand_over_street.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

// define void relight(int lamps[], int n): every 0 becomes 50

// define int streetPower(int lamps[], int n): returns the total of all lamps

int main() {
    int lamps[6] = {30, 0, 55, 80, 0, 45};

    // call relight, then print &quot;Street power: &quot; and streetPower's answer

    return 0;
}
">
            <input type="hidden" name="instructions" value="Fix the dark lamps, then report the street's total.

1. void relight(int lamps[], int n): if lamps[i] == 0, set it to 50
2. int streetPower(int lamps[], int n): add every lamp, return the total
3. In main: relight(lamps, 6); then print the total.

The output must be exactly:
Street power: 310">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Both functions loop i from 0 while i &lt; n. relight changes lamps[i] directly, and because the array is passed by its address, main's lamps change too. Call relight before streetPower, or the total is the old 210.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

void relight(int lamps[], int n) {
    for (int i = 0; i &lt; n; i++) {
        if (lamps[i] == 0) {
            lamps[i] = 50;
        }
    }
}

int streetPower(int lamps[], int n) {
    int total = 0;
    for (int i = 0; i &lt; n; i++) {
        total += lamps[i];
    }
    return total;
}

int main() {
    int lamps[6] = {30, 0, 55, 80, 0, 45};

    relight(lamps, 6);
    printf(&quot;Street power: %d\n&quot;, streetPower(lamps, 6));
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm3', 'lesson' => 'lesson03']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. How do you write an array parameter for a function?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>int lamps</code></li>
            <li class="quiz-option">B. <code>int *lamps[6]</code></li>
            <li class="quiz-option quiz-correct">C. <code>int lamps[]</code> ✓</li>
            <li class="quiz-option">D. <code>array lamps</code></li>
        </ul>
        <p class="quiz-explanation"><em>Empty square brackets after the name mark the parameter as an array of ints.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why do array functions usually take a size parameter like <code>n</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. It can't tell how long the array is ✓</li>
            <li class="quiz-option">B. C requires at least two parameters</li>
            <li class="quiz-option">C. It makes the loop run much faster</li>
            <li class="quiz-option">D. n holds the array's first value</li>
        </ul>
        <p class="quiz-explanation"><em>Only the start address is passed, so the function needs the count sent separately.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does C actually pass when you pass an array to a function?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A copy of every element</li>
            <li class="quiz-option quiz-correct">B. The address of its first element ✓</li>
            <li class="quiz-option">C. Only the first element's value</li>
            <li class="quiz-option">D. The size of the whole array</li>
        </ul>
        <p class="quiz-explanation"><em>An array argument becomes the address of element 0, so the function reaches the original elements.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. A function sets <code>lamps[2] = 99;</code> on an array it was passed. What happens to the caller's array?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing in it changes</li>
            <li class="quiz-option">B. Every element becomes 99</li>
            <li class="quiz-option">C. Its element 3 becomes 99</li>
            <li class="quiz-option quiz-correct">D. Its element 2 becomes 99 ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The function worked on the caller's own array through its address, so the change stays.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. How do you call <code>streetPower</code> on an array <code>lamps</code> of 6?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. <code>streetPower(lamps, 6);</code> ✓</li>
            <li class="quiz-option">B. <code>streetPower(lamps[], 6);</code></li>
            <li class="quiz-option">C. <code>streetPower(lamps[6]);</code></li>
            <li class="quiz-option">D. <code>streetPower(&amp;lamps[6], 6);</code></li>
        </ul>
        <p class="quiz-explanation"><em>In the call you use the array's plain name; the [] only belong in the parameter list.</em></p>
    </div>

</div>
