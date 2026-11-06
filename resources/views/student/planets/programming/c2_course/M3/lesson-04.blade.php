{{-- M3 · Lesson 3.4: The Address of the First Lamp — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Arrays — pointers and arrays (pointer arithmetic). --}}

<div class="lesson-fragment" data-module="m3" data-lesson="04">

    <h1 class="lesson-heading">Lesson 3.4: The Address of the First Lamp</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can point a pointer at an array, reach any element with <code>*(p + i)</code>, and explain what adding to a pointer really does.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        An array's name, used on its own, stands for the <strong>address of its first element</strong>. So you can point a pointer straight at an array:
    </p>

    <pre class="code-block"><code>int lamps[3] = {10, 20, 30};
int *p = lamps;        // same as  int *p = &amp;lamps[0];</code></pre>

    <p class="body-text">
        Adding a number to a pointer moves it along by that many <em>elements</em>, not bytes. C knows how big an <code>int</code> is and does the sum for you. This is called <strong>pointer arithmetic</strong>:
    </p>

    <ul class="body-list">
        <li><code>p</code> points at <code>lamps[0]</code>, so <code>*p</code> is 10.</li>
        <li><code>p + 1</code> points at <code>lamps[1]</code>, so <code>*(p + 1)</code> is 20.</li>
        <li><code>p + 2</code> points at <code>lamps[2]</code>, so <code>*(p + 2)</code> is 30.</li>
    </ul>

    <p class="body-text">
        That is really what square brackets are. <code>lamps[i]</code> is a short way of writing <code>*(lamps + i)</code>, and with a pointer, <code>p[i]</code> means <code>*(p + i)</code> too. Keep the brackets round <code>p + i</code>: <code>*p + i</code> means "the first lamp's value, plus i", which is a different number.
    </p>

    <p class="body-text">
        You can also move the pointer itself, with <code>p++</code>, to step to the next element.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Rivet opens the back of Lamp Street's control box and finds how it really works. The box doesn't store six separate lamps. It stores <em>one</em> address, where the first lamp lives, and counts along from there.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> is very excited. "It's the depot map again! The street's name is just the address of the first lamp. Lamp two is one step further along. Lamp three, two steps."
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "So the brackets we've been using..."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Were pointers the whole time! Wearing a disguise. A very good disguise."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A pointer p holds the address of lamps[0]; p + 1 points at lamps[1] and p + 2 at lamps[2]">
            <rect x="60" y="55" width="130" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="125" y="89.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a5a00">10</text>
            <text x="125" y="130" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[0]</text>
            <rect x="255" y="55" width="130" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="320" y="89.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a5a00">20</text>
            <text x="320" y="130" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[1]</text>
            <rect x="450" y="55" width="130" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="515" y="89.5" text-anchor="middle" font-size="17" font-weight="700" fill="#7a5a00">30</text>
            <text x="515" y="130" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">lamps[2]</text>
            <text x="125" y="30" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8" font-family="ui-monospace, monospace">p</text>
            <line x1="125" y1="35" x2="125.0" y2="41.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="125,51 131.0,39.0 119.0,39.0" fill="#7c5cff"/>
            <text x="320" y="30" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8" font-family="ui-monospace, monospace">p + 1</text>
            <line x1="320" y1="35" x2="320.0" y2="41.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="320,51 326.0,39.0 314.0,39.0" fill="#7c5cff"/>
            <text x="515" y="30" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8" font-family="ui-monospace, monospace">p + 2</text>
            <line x1="515" y1="35" x2="515.0" y2="41.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="515,51 521.0,39.0 509.0,39.0" fill="#7c5cff"/>
            <text x="320" y="160" text-anchor="middle" font-size="13" fill="#475569">*(p + i) is the same element as lamps[i]</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int bells[4] = {1, 2, 3, 4};
    int *p = bells;

    for (int i = 0; i &lt; 4; i++) {
        printf("%d ", *(p + i));    // 1 2 3 4
    }
    printf("\n");
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. With <code>int lamps[3] = {10, 20, 30};</code> and <code>int *p = lamps;</code>, use a loop and <code>*(p + i)</code>, never <code>lamps[i]</code>, to add 5 to every lamp and print each one. Exactly:
    </p>

    <pre class="code-block"><code>Lamp 0: 15
Lamp 1: 25
Lamp 2: 35</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="The Address of the First Lamp">
            <input type="hidden" name="filename"     value="first_lamp.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int lamps[3] = {10, 20, 30};
    int *p = lamps;     // p points at lamps[0]

    // loop i from 0 to 2: add 5 to *(p + i), then print &quot;Lamp &lt;i&gt;: &lt;value&gt;&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="Work Lamp Street through one pointer only.

For i = 0, 1, 2:
1. *(p + i) = *(p + i) + 5;
2. print &quot;Lamp &lt;i&gt;: &lt;value&gt;&quot; using *(p + i)
(no lamps[i] anywhere in the loop)

The output must be exactly:
Lamp 0: 15
Lamp 1: 25
Lamp 2: 35">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="Inside the loop: *(p + i) = *(p + i) + 5; then printf(&quot;Lamp %d: %d\n&quot;, i, *(p + i)); The brackets round p + i matter. *p + i would read the first lamp and add i to it.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int lamps[3] = {10, 20, 30};
    int *p = lamps;

    for (int i = 0; i &lt; 3; i++) {
        *(p + i) = *(p + i) + 5;
        printf(&quot;Lamp %d: %d\n&quot;, i, *(p + i));
    }
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm3', 'lesson' => 'lesson04']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. On its own, what does an array's name like <code>lamps</code> stand for?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The value of its last element</li>
            <li class="quiz-option">B. The number of elements in it</li>
            <li class="quiz-option">C. A full copy of the whole array</li>
            <li class="quiz-option quiz-correct">D. The address of its first element ✓</li>
        </ul>
        <p class="quiz-explanation"><em>An array name used by itself is the address of element 0, which is why int *p = lamps; works.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. If <code>p</code> points at <code>lamps[0]</code>, what does <code>p + 2</code> point at?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. lamps[2] ✓</li>
            <li class="quiz-option">B. lamps[0] plus 2</li>
            <li class="quiz-option">C. Two bytes further on</li>
            <li class="quiz-option">D. lamps[1]</li>
        </ul>
        <p class="quiz-explanation"><em>Pointer arithmetic moves by whole elements, so p + 2 is the address of lamps[2].</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which is the same as <code>lamps[i]</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>*lamps + i</code></li>
            <li class="quiz-option">B. <code>&amp;lamps + i</code></li>
            <li class="quiz-option quiz-correct">C. <code>*(lamps + i)</code> ✓</li>
            <li class="quiz-option">D. <code>(*lamps) * i</code></li>
        </ul>
        <p class="quiz-explanation"><em>Square brackets are shorthand for adding the index to the address and dereferencing it.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. With <code>int lamps[3] = {10, 20, 30}; int *p = lamps;</code>, what is <code>*p + 1</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 20</li>
            <li class="quiz-option">B. 30</li>
            <li class="quiz-option">C. 10</li>
            <li class="quiz-option quiz-correct">D. 11 ✓</li>
        </ul>
        <p class="quiz-explanation"><em>*p is 10 and then 1 is added: 11. To reach the next lamp you need *(p + 1).</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does <code>p++</code> do to a pointer into an array?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Adds 1 to the value it points at</li>
            <li class="quiz-option quiz-correct">B. Moves it to point at the next element ✓</li>
            <li class="quiz-option">C. Sets it to NULL for safety</li>
            <li class="quiz-option">D. Moves it back to element 0</li>
        </ul>
        <p class="quiz-explanation"><em>Incrementing a pointer moves it one element along the array.</em></p>
    </div>

</div>
