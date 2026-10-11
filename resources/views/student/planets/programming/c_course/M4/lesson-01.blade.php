{{-- M4 · Lesson 4.1: The Emitter Line — content fragment.
Syllabus: CC102 Unit 4 — Arrays (declaration and initialization). --}}

<div class="lesson-fragment" data-module="m4" data-lesson="01">

    <h1 class="lesson-heading">Lesson 4.1: The Emitter Line</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can declare and initialize a one-dimensional array, and read an
        individual element out of it by its index.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Every variable you've written so far has held exactly one value. An <strong>array</strong> holds a fixed
        number of values of the <em>same type</em>, all under one name, one after another in memory:
    </p>

    <pre class="code-block"><code>int emitters[5] = {80, 45, 90, 30, 100};</code></pre>

    <p class="body-text">
        <code>[5]</code> says the array holds 5 <code>int</code> values. The list in braces sets each one, in
        order. Every element has an <strong>index</strong> — its position — and C indexes start at
        <strong>0</strong>, not 1. So <code>emitters[0]</code> is the first value (<code>80</code>), and
        <code>emitters[4]</code> is the last one in a 5-element array (<code>100</code>) — the highest valid
        index is always one less than the array's size.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Last dead system on the list: the hull panels. They're what keeps the weather out, and there isn't one
        of them — there's a whole row, and the crash flattened one in the middle.
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "I am not making four separate boxes with four separate names. I'd mix them up
        by Thursday."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "So what, then?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "One name. One row. Then I just point at the one I want by where it sits —
        first, second, third." He catches himself and holds up a finger. "Ah — except counting starts at zero.
        So the first one is number zero."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "That's stupid."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>, delighted: "I know. Everyone hates it for about a week and then never thinks
        about it again. Promise."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 150" role="img" aria-label="An array of 5 emitter values, indexed 0 through 4">
            <text x="20" y="30" font-size="13" font-weight="700" fill="#475569">emitters</text>
            @php $vals = [80, 45, 90, 30, 100]; @endphp
            @foreach ($vals as $i => $v)
                <rect x="{{ 20 + $i * 115 }}" y="45" width="100" height="60" rx="10" fill="{{ $i === 0 || $i === 4 ? '#e8f0ff' : '#f3f0ff' }}" stroke="{{ $i === 0 || $i === 4 ? '#2f6fe0' : '#7c5cff' }}" stroke-width="2"/>
                <text x="{{ 70 + $i * 115 }}" y="75" text-anchor="middle" font-size="17" font-weight="700" fill="{{ $i === 0 || $i === 4 ? '#14306b' : '#4a35b8' }}">{{ $v }}</text>
                <text x="{{ 70 + $i * 115 }}" y="122" text-anchor="middle" font-size="12" fill="#64748b">[{{ $i }}]</text>
            @endforeach
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int emitters[5] = {80, 45, 90, 30, 100};

    printf("First: %d\n", emitters[0]);
    printf("Last: %d\n", emitters[4]);
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Declare <code>int bays[4] = {12, 45, 78, 20};</code>, then print bay 0, bay 1 and bay 3 by index —
        skipping bay 2 entirely. Expected output:
    </p>

    <pre class="code-block"><code>Bay 0: 12
Bay 1: 45
Bay 3: 20</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="The Emitter Line">
            <input type="hidden" name="filename"     value="bay_readout.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int bays[4] = {12, 45, 78, 20};

    // print &quot;Bay 0: 12&quot;, &quot;Bay 1: 45&quot;, &quot;Bay 3: 20&quot; using bays[0], bays[1], bays[3]

    return 0;
}
">
            <input type="hidden" name="instructions" value="bays holds {12, 45, 78, 20}. Print, each on its own line:

Bay 0: 12
Bay 1: 45
Bay 3: 20

Use bays[0], bays[1] and bays[3] — skip index 2 (the value 78) entirely.">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="bays[0] is the first value, bays[1] the second, and so on — the index is always one less than the position you'd count by hand. bays[3] is the fourth and last value, 20.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int bays[4] = {12, 45, 78, 20};

    printf(&quot;Bay 0: %d\n&quot;, bays[0]);
    printf(&quot;Bay 1: %d\n&quot;, bays[1]);
    printf(&quot;Bay 3: %d\n&quot;, bays[3]);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm4', 'lesson' => 'lesson01']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does an array hold?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Exactly one value, like any other variable</li>
            <li class="quiz-option quiz-correct">B. A fixed number of values of the same type, under one name ✓</li>
            <li class="quiz-option">C. Values of any mix of types</li>
            <li class="quiz-option">D. Only text, never numbers</li>
        </ul>
        <p class="quiz-explanation"><em>An array groups several values of the same type together under a single name, stored one after another.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In <code>int emitters[5] = {80, 45, 90, 30, 100};</code>, what is <code>emitters[0]</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 5 — the array's size</li>
            <li class="quiz-option quiz-correct">B. 80 — the first value ✓</li>
            <li class="quiz-option">C. 100 — the last value</li>
            <li class="quiz-option">D. It causes an error — arrays start counting at 1</li>
        </ul>
        <p class="quiz-explanation"><em>C array indexing starts at 0, so index 0 is always the first element — here, 80.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. In that same 5-element array, what is the highest valid index?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 5</li>
            <li class="quiz-option quiz-correct">B. 4 ✓</li>
            <li class="quiz-option">C. 6</li>
            <li class="quiz-option">D. 1</li>
        </ul>
        <p class="quiz-explanation"><em>Since indexing starts at 0, an array of 5 elements has valid indices 0, 1, 2, 3 and 4 — one less than its size.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does <code>[5]</code> mean in <code>int emitters[5]</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The array's first value is 5</li>
            <li class="quiz-option quiz-correct">B. The array holds 5 elements ✓</li>
            <li class="quiz-option">C. The array can never change</li>
            <li class="quiz-option">D. It's a comment and has no effect</li>
        </ul>
        <p class="quiz-explanation"><em>The number in square brackets when declaring an array sets how many elements it holds.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why does Astro call index-0-is-first "automatic fast" but still worth mentioning up front?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because C actually starts counting arrays at 1 in some cases</li>
            <li class="quiz-option quiz-correct">B. Because it's a common early mixup, and knowing it in advance avoids a whole class of off-by-one mistakes ✓</li>
            <li class="quiz-option">C. Because indices don't matter at all once you're used to arrays</li>
            <li class="quiz-option">D. Because it only applies to arrays larger than 10 elements</li>
        </ul>
        <p class="quiz-explanation"><em>Zero-based indexing trips up almost everyone the first time — expecting it up front is what prevents off-by-one bugs later.</em></p>
    </div>

</div>
