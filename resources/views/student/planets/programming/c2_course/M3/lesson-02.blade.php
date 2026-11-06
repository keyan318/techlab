{{-- M3 · Lesson 3.2: Walking Lamp Street — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Arrays — operations on arrays (traversal, counting, finding the largest). --}}

<div class="lesson-fragment" data-module="m3" data-lesson="02">

    <h1 class="lesson-heading">Lesson 3.2: Walking Lamp Street</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can loop through every element of an array to count the ones that match a test, and find the position of the largest value.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Most work on an array is a <strong>traversal</strong>: a <code>for</code> loop that visits every index from 0 up to the count, one at a time.
    </p>

    <pre class="code-block"><code>for (int i = 0; i &lt; count; i++) {
    // lamps[i] is the lamp you are looking at
}</code></pre>

    <p class="body-text">
        Two common jobs during a traversal:
    </p>

    <ul class="body-list">
        <li><strong>Counting</strong>: start a counter at 0 and add 1 each time an element passes a test, like <code>lamps[i] == 0</code>.</li>
        <li><strong>Finding the largest</strong>: remember the <em>index</em> of the biggest so far, starting with 0. Each time you meet a bigger element, remember its index instead.</li>
    </ul>

    <pre class="code-block"><code>int best = 0;
for (int i = 1; i &lt; count; i++) {
    if (lamps[i] &gt; lamps[best]) {
        best = i;
    }
}</code></pre>

    <p class="body-text">
        Keeping the index, not just the value, tells you <em>where</em> the biggest one is as well as how big it is. Keep the loop condition <code>i &lt; count</code>; <code>i &lt;= count</code> visits one past the end.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        Astro sends Volt to walk the street. Not to look at a couple of lamps and guess. Every one, top to bottom.
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Two things before we touch anything. How many are dead. And which one is the brightest, because that one's working hardest and it'll be next to go."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Easy. A loop that walks the row. Keep a tally of dead ones as you go, and keep a finger on the brightest you've seen so far."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And it stops at the last lamp. Not one step past it into the road."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 150" role="img" aria-label="A loop visits every lamp in order: two dead lamps at indexes 1 and 4, the brightest at index 3">
            <rect x="25" y="20" width="90" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="70" y="54.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a5a00">30</text>
            <text x="70" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[0]</text>
            <rect x="125" y="20" width="90" height="56" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="170" y="54.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a1a14">0</text>
            <text x="170" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[1]</text>
            <rect x="225" y="20" width="90" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="270" y="54.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a5a00">55</text>
            <text x="270" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[2]</text>
            <rect x="325" y="20" width="90" height="56" rx="12" fill="#e7f7ee" stroke="#1f9d55" stroke-width="2"/>
            <text x="370" y="54.5" text-anchor="middle" font-size="17" font-weight="700" fill="#14532d">80</text>
            <text x="370" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[3]</text>
            <rect x="425" y="20" width="90" height="56" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="470" y="54.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a1a14">0</text>
            <text x="470" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[4]</text>
            <rect x="525" y="20" width="90" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="570" y="54.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a5a00">45</text>
            <text x="570" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[5]</text>
            <text x="320" y="140" text-anchor="middle" font-size="13" font-weight="700" fill="#334155">dead: index 1 and 4  |  brightest: index 3 (80)</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int benches[5] = {2, 0, 3, 0, 1};    // people waiting
    int empty = 0;

    for (int i = 0; i &lt; 5; i++) {
        if (benches[i] == 0) {
            empty++;
        }
    }
    printf("Empty benches: %d\n", empty);   // 2
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        With <code>int lamps[6] = {30, 0, 55, 80, 0, 45};</code>, use one loop to count the lamps that read 0 and to find the <em>index</em> of the brightest lamp. Print exactly:
    </p>

    <pre class="code-block"><code>Dead lamps: 2
Brightest: lamp 3</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Walking Lamp Street">
            <input type="hidden" name="filename"     value="walk_the_street.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int lamps[6] = {30, 0, 55, 80, 0, 45};
    int dead = 0;
    int best = 0;     // index of the brightest lamp so far

    // loop over every lamp: count the 0s, and move best when you find a brighter lamp

    printf(&quot;Dead lamps: %d\n&quot;, dead);
    printf(&quot;Brightest: lamp %d\n&quot;, best);
    return 0;
}
">
            <input type="hidden" name="instructions" value="Walk all of Lamp Street: 30 0 55 80 0 45

In one for loop (i from 0 while i &lt; 6):
- if lamps[i] == 0, add 1 to dead
- if lamps[i] &gt; lamps[best], set best = i

The output must be exactly:
Dead lamps: 2
Brightest: lamp 3">
            <input type="hidden" name="hint_title"   value="Volt's Hint">
            <input type="hidden" name="hint_body"    value="Inside the loop, two separate ifs: one adds to dead when the lamp reads 0, the other moves best when lamps[i] &gt; lamps[best]. best stores the position, so it ends on 3, where the 80 is.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int lamps[6] = {30, 0, 55, 80, 0, 45};
    int dead = 0;
    int best = 0;

    for (int i = 0; i &lt; 6; i++) {
        if (lamps[i] == 0) {
            dead++;
        }
        if (lamps[i] &gt; lamps[best]) {
            best = i;
        }
    }

    printf(&quot;Dead lamps: %d\n&quot;, dead);
    printf(&quot;Brightest: lamp %d\n&quot;, best);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm3', 'lesson' => 'lesson02']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is a traversal of an array?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Visiting every element in order ✓</li>
            <li class="quiz-option">B. Deleting every element in turn</li>
            <li class="quiz-option">C. Sorting it from small to large</li>
            <li class="quiz-option">D. Printing only the first element</li>
        </ul>
        <p class="quiz-explanation"><em>A traversal walks the array from the first index to the last, one element at a time.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. For a 6-element array, which loop condition visits every element and no more?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>i &lt;= 6</code></li>
            <li class="quiz-option quiz-correct">B. <code>i &lt; 6</code> ✓</li>
            <li class="quiz-option">C. <code>i &lt; 5</code></li>
            <li class="quiz-option">D. <code>i &gt; 6</code></li>
        </ul>
        <p class="quiz-explanation"><em>Indexes 0 to 5 are visited by i < 6; i <= 6 would also try index 6, past the end.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why does the "brightest" search remember an index instead of only the value?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Indexes take up less memory</li>
            <li class="quiz-option">B. Values cannot be compared in C</li>
            <li class="quiz-option">C. C requires it for every search</li>
            <li class="quiz-option quiz-correct">D. So you know where that lamp is ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The index gives you the position, and lamps[best] still gives you the value whenever you want it.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. In the task's street, which lamp index is the brightest?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 0</li>
            <li class="quiz-option">B. 2</li>
            <li class="quiz-option quiz-correct">C. 3 ✓</li>
            <li class="quiz-option">D. 5</li>
        </ul>
        <p class="quiz-explanation"><em>The values are 30, 0, 55, 80, 0, 45; the 80 sits at index 3.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. A counter for "dead lamps" should start at...</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 1</li>
            <li class="quiz-option">B. 6</li>
            <li class="quiz-option quiz-correct">C. 0 ✓</li>
            <li class="quiz-option">D. -1</li>
        </ul>
        <p class="quiz-explanation"><em>Before the loop you have counted nothing yet, so the tally starts at 0.</em></p>
    </div>

</div>
