{{-- M4 · Lesson 4.2: Checking Each Emitter — content fragment.
Syllabus: CC102 Unit 4 — Arrays (array access and manipulation). --}}

<div class="lesson-fragment" data-module="m4" data-lesson="02">

    <h1 class="lesson-heading">Lesson 4.2: Checking Each Emitter</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can use a <code>for</code> loop to visit every element of an array in
        order, using the loop's counter as the array's index.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Writing out <code>emitters[0]</code>, <code>emitters[1]</code>, <code>emitters[2]</code>... by hand
        doesn't scale past a handful of elements. Instead, use the loop's counter itself as the index:
    </p>

    <pre class="code-block"><code>for (int i = 0; i &lt; 5; i++) {
    printf("%d\n", emitters[i]);
}</code></pre>

    <p class="body-text">
        On the first pass, <code>i</code> is 0, so <code>emitters[i]</code> means <code>emitters[0]</code>. On
        the next, <code>i</code> is 1, so it means <code>emitters[1]</code> — and so on, all the way through the
        array, one index per iteration. This pattern — <strong>traversing</strong> an array — is one of the
        most common things you'll write in C.
    </p>

    <p class="body-text">
        The loop's condition (<code>i &lt; 5</code>) must match the array's real size, or you'll either miss
        elements or read past the end of the array into memory that isn't yours — C does not stop you from
        doing this, and the result is undefined.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">In <code>for (int i = 0; i &lt; 5; i++)</code>, on the third pass, what does <code>emitters[i]</code> mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>emitters[3]</code></li>
            <li class="quiz-option quiz-correct">B. <code>emitters[2]</code> ✓</li>
            <li class="quiz-option">C. <code>emitters[1]</code></li>
            <li class="quiz-option">D. <code>emitters[5]</code></li>
        </ul>
        <p class="quiz-explanation"><em>Passes go <code>i</code> = 0, 1, 2, so the third pass reads <code>emitters[2]</code>.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        Instead of opening every locker by name, send a helper <strong>walking down the hallway</strong> with a counter. At locker 0, the counter says 0, so they open locker 0. One step, the counter says 1, they open locker 1. And so on to the end.
    </p>

    <p class="body-text">
        The counter is <code>i</code>, and <code>emitters[i]</code> means <strong>“the locker I'm standing at right now”</strong>. The stop rule <code>i &lt; 5</code> has to match the number of lockers. Walk one step too far and you're opening a door that isn't yours, and C will not stop you.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 330" role="img" aria-label="A helper walks along 5 lockers holding a sign that says i = 2, standing at locker 2. A red stop line after locker 4: i less than 5 is false. for int i = 0, i less than 5, i plus plus, open emitters[i].">
            <rect x="25" y="50" width="90" height="150" rx="6" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><line x1="52.0" y1="62" x2="88.0" y2="62" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="52.0" y1="69" x2="88.0" y2="69" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="52.0" y1="76" x2="88.0" y2="76" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><circle cx="70.0" cy="100" r="15" fill="#7c5cff"/><text x="70.0" y="106" font-size="15" fill="#fff" font-weight="800" text-anchor="middle">0</text><text x="70.0" y="155.0" font-size="22" fill="#14306b" font-weight="800" text-anchor="middle">80</text><rect x="101" y="117.0" width="5" height="22" rx="2" fill="#2f6fe0"/><rect x="125" y="50" width="90" height="150" rx="6" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><line x1="152.0" y1="62" x2="188.0" y2="62" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="152.0" y1="69" x2="188.0" y2="69" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="152.0" y1="76" x2="188.0" y2="76" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><circle cx="170.0" cy="100" r="15" fill="#7c5cff"/><text x="170.0" y="106" font-size="15" fill="#fff" font-weight="800" text-anchor="middle">1</text><text x="170.0" y="155.0" font-size="22" fill="#14306b" font-weight="800" text-anchor="middle">45</text><rect x="201" y="117.0" width="5" height="22" rx="2" fill="#2f6fe0"/><rect x="225" y="50" width="90" height="150" rx="6" fill="#ffe3ec" stroke="#e8457c" stroke-width="2"/><line x1="252.0" y1="62" x2="288.0" y2="62" stroke="#e8457c" stroke-width="1.5" opacity=".5"/><line x1="252.0" y1="69" x2="288.0" y2="69" stroke="#e8457c" stroke-width="1.5" opacity=".5"/><line x1="252.0" y1="76" x2="288.0" y2="76" stroke="#e8457c" stroke-width="1.5" opacity=".5"/><circle cx="270.0" cy="100" r="15" fill="#7c5cff"/><text x="270.0" y="106" font-size="15" fill="#fff" font-weight="800" text-anchor="middle">2</text><text x="270.0" y="155.0" font-size="22" fill="#14306b" font-weight="800" text-anchor="middle">90</text><rect x="301" y="117.0" width="5" height="22" rx="2" fill="#e8457c"/><rect x="325" y="50" width="90" height="150" rx="6" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><line x1="352.0" y1="62" x2="388.0" y2="62" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="352.0" y1="69" x2="388.0" y2="69" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="352.0" y1="76" x2="388.0" y2="76" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><circle cx="370.0" cy="100" r="15" fill="#7c5cff"/><text x="370.0" y="106" font-size="15" fill="#fff" font-weight="800" text-anchor="middle">3</text><text x="370.0" y="155.0" font-size="22" fill="#14306b" font-weight="800" text-anchor="middle">30</text><rect x="401" y="117.0" width="5" height="22" rx="2" fill="#2f6fe0"/><rect x="425" y="50" width="90" height="150" rx="6" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><line x1="452.0" y1="62" x2="488.0" y2="62" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="452.0" y1="69" x2="488.0" y2="69" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="452.0" y1="76" x2="488.0" y2="76" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><circle cx="470.0" cy="100" r="15" fill="#7c5cff"/><text x="470.0" y="106" font-size="15" fill="#fff" font-weight="800" text-anchor="middle">4</text><text x="470.0" y="155.0" font-size="22" fill="#14306b" font-weight="800" text-anchor="middle">100</text><rect x="501" y="117.0" width="5" height="22" rx="2" fill="#2f6fe0"/><line x1="520" y1="40" x2="520" y2="210" stroke="#b3261e" stroke-width="5" stroke-linecap="round"/><text x="570" y="108" font-size="15" fill="#b3261e" font-weight="800" text-anchor="middle">stop!</text><text x="570" y="128" font-size="13" fill="#b3261e" font-weight="700" text-anchor="middle"><tspan font-family="ui-monospace, Menlo, Consolas, monospace">i &lt; 5</tspan></text><text x="570" y="146" font-size="13" fill="#b3261e" font-weight="700" text-anchor="middle">is false</text><ellipse cx="56" cy="232" rx="5" ry="3.5" fill="#cfd7e6"/><ellipse cx="78" cy="238" rx="5" ry="3.5" fill="#cfd7e6"/><ellipse cx="100" cy="232" rx="5" ry="3.5" fill="#cfd7e6"/><ellipse cx="122" cy="238" rx="5" ry="3.5" fill="#cfd7e6"/><ellipse cx="144" cy="232" rx="5" ry="3.5" fill="#cfd7e6"/><ellipse cx="166" cy="238" rx="5" ry="3.5" fill="#cfd7e6"/><ellipse cx="188" cy="232" rx="5" ry="3.5" fill="#cfd7e6"/><ellipse cx="210" cy="238" rx="5" ry="3.5" fill="#cfd7e6"/><ellipse cx="232" cy="232" rx="5" ry="3.5" fill="#cfd7e6"/><circle cx="275" cy="238" r="11" fill="#ffd9b3" stroke="#8a4b00" stroke-width="1.5"/><path d="M260 280 Q260 252 275 252 Q290 252 290 280 Z" fill="#e8457c"/><rect x="300" y="226" width="66" height="28" rx="14" fill="#fff" stroke="#e8457c" stroke-width="2"/><text x="333" y="245" font-size="14" fill="#9c1f4c" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">i = 2</text><line x1="378" y1="240" x2="489.0" y2="240.0" stroke="#9aa5b8" stroke-width="2.5" stroke-dasharray="6,5"/><polygon points="500,240 489.0,234.0 489.0,246.0" fill="#9aa5b8"/><text x="320" y="318" font-size="15" fill="#1b2440" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">for (int i = 0; i &lt; 5; i++)  →  open <tspan fill="#9c1f4c">emitters[i]</tspan></text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">The loop says <code>i &lt;= 5</code> for 5 lockers. What goes wrong?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It skips the very first locker, 0</li>
            <li class="quiz-option">B. It stops one locker too early</li>
            <li class="quiz-option">C. It opens each locker twice in a row</li>
            <li class="quiz-option quiz-correct">D. It reads a locker past the end ✓</li>
        </ul>
        <p class="quiz-explanation"><em>With <code>&lt;= 5</code>, <code>i</code> reaches 5, and <code>emitters[5]</code> is past the last locker (4). Use <code>i &lt; 5</code>.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A for loop's counter i walks through each index of the array in turn">
            @php $vals = [80, 45, 90, 30, 100]; @endphp
            @foreach ($vals as $i => $v)
                <rect x="{{ 20 + $i * 115 }}" y="30" width="100" height="55" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
                <text x="{{ 70 + $i * 115 }}" y="58" text-anchor="middle" font-size="15" font-weight="700" fill="#4a35b8">{{ $v }}</text>
                <text x="{{ 70 + $i * 115 }}" y="98" text-anchor="middle" font-size="12" fill="#64748b">i = {{ $i }}</text>
            @endforeach
            <text x="320" y="140" text-anchor="middle" font-size="13" fill="#475569">for (int i = 0; i &lt; 5; i++) { printf("%d\n", emitters[i]); }</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int emitters[5] = {80, 45, 90, 30, 100};

    for (int i = 0; i &lt; 5; i++) {
        printf("Emitter %d: %d\n", i, emitters[i]);
    }
    return 0;
}</code></pre>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">How many “Emitter” lines does the code example print?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 4</li>
            <li class="quiz-option quiz-correct">B. 5 ✓</li>
            <li class="quiz-option">C. 6</li>
            <li class="quiz-option">D. 1</li>
        </ul>
        <p class="quiz-explanation"><em><code>i</code> goes 0, 1, 2, 3, 4: one line for each of the 5 elements.</em></p>
    </div>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Declare <code>int bays[4] = {12, 45, 78, 20};</code>, then use a <code>for</code> loop to print every
        bay's index and value. Expected output:
    </p>

    <pre class="code-block"><code>Bay 0: 12
Bay 1: 45
Bay 2: 78
Bay 3: 20</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Checking Each Emitter">
            <input type="hidden" name="filename"     value="bay_sweep.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int bays[4] = {12, 45, 78, 20};

    // for loop from i = 0 to i &lt; 4, printing &quot;Bay i: bays[i]&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="bays holds {12, 45, 78, 20} — 4 elements. Write a for loop with i from 0 up to (not including) 4, printing each index and value:

Bay 0: 12
Bay 1: 45
Bay 2: 78
Bay 3: 20">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="for (int i = 0; i &lt; 4; i++) { printf(&quot;Bay %d: %d\n&quot;, i, bays[i]); } — the loop's condition (4) must match the array's real size, not one more or one less.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int bays[4] = {12, 45, 78, 20};

    for (int i = 0; i &lt; 4; i++) {
        printf(&quot;Bay %d: %d\n&quot;, i, bays[i]);
    }
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm4', 'lesson' => 'lesson02']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In <code>for (int i = 0; i &lt; 5; i++) { printf("%d\n", emitters[i]); }</code>, what role does i play?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. It is the index used on each pass ✓</li>
            <li class="quiz-option">B. It holds the array's total size</li>
            <li class="quiz-option">C. It holds the value being printed</li>
            <li class="quiz-option">D. It counts passes but never reads</li>
        </ul>
        <p class="quiz-explanation"><em>The loop's counter i is plugged straight into emitters[i], so each pass through the loop reaches a different element.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why must the loop's condition match the array's real size?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. C stops at the array's end by itself</li>
            <li class="quiz-option">B. C won't compile a mismatched loop</li>
            <li class="quiz-option quiz-correct">C. It can miss items or read past the end ✓</li>
            <li class="quiz-option">D. The array resizes to fit the loop</li>
        </ul>
        <p class="quiz-explanation"><em>C does not check array bounds for you — a loop condition larger than the array's size reads past its end, with undefined results.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is "traversing" an array?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Deleting every element in it</li>
            <li class="quiz-option">B. Declaring it for the first time</li>
            <li class="quiz-option">C. Sorting it from smallest to largest</li>
            <li class="quiz-option quiz-correct">D. Visiting every element in order ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Traversing an array means visiting each of its elements in order — exactly what a for loop with an index does.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. If bays has 4 elements and the loop's condition is written as <code>i &lt;= 4</code> instead of <code>i &lt; 4</code>, what happens?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing; both conditions act the same</li>
            <li class="quiz-option quiz-correct">B. It reads bays[4], past the last item ✓</li>
            <li class="quiz-option">C. The array grows by itself to fit</li>
            <li class="quiz-option">D. It skips bays[0] and starts at 1</li>
        </ul>
        <p class="quiz-explanation"><em>With 4 elements, valid indices are 0-3 — i &lt;= 4 lets the loop reach index 4, which is one past the array's actual end.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If the array's size changes later, what happens to the traversal loop?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It must be rewritten from scratch</li>
            <li class="quiz-option">B. It only ever works for 5 elements</li>
            <li class="quiz-option quiz-correct">C. It still works once its condition matches ✓</li>
            <li class="quiz-option">D. It adjusts to the new size by itself</li>
        </ul>
        <p class="quiz-explanation"><em>The traversal pattern itself doesn't change — only the condition's number needs to track the array's actual size.</em></p>
    </div>

</div>
