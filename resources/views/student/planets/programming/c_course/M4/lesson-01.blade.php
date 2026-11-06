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

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">An array has 8 elements. What is its highest valid index?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. 7 ✓</li>
            <li class="quiz-option">B. 8</li>
            <li class="quiz-option">C. 9</li>
            <li class="quiz-option">D. 0</li>
        </ul>
        <p class="quiz-explanation"><em>Indexes start at 0, so the highest valid index is always one less than the size.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        An <strong>array</strong> is a row of <strong>lockers</strong> in a hallway. They're all the same size, side by side, with one name on the sign above them: <code>emitters</code>. Each locker holds one value, and every locker holds the same kind of thing (here, all <code>int</code>s).
    </p>

    <p class="body-text">
        The surprise: the first locker is number <strong>0</strong>, not 1. So 5 lockers are numbered 0 to 4, and <code>emitters[4]</code> is the last one. Ask for <code>emitters[5]</code> and you're reaching past the end of the hallway, into a locker that isn't yours.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 315" role="img" aria-label="A row of 5 lockers under the sign emitters, numbered 0 to 4, holding 80, 45, 90, 30 and 100. emitters[0] is 80 and emitters[4] is 100. A sixth spot, emitters[5], is crossed out: not yours.">
            <rect x="170" y="18" width="300" height="38" rx="8" fill="#1b2440"/><text x="320" y="43" font-size="18" fill="#fff" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">emitters</text><rect x="25" y="76" width="90" height="170" rx="6" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><line x1="52.0" y1="88" x2="88.0" y2="88" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="52.0" y1="95" x2="88.0" y2="95" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="52.0" y1="102" x2="88.0" y2="102" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><circle cx="70.0" cy="126" r="15" fill="#7c5cff"/><text x="70.0" y="132" font-size="15" fill="#fff" font-weight="800" text-anchor="middle">0</text><text x="70.0" y="191.0" font-size="22" fill="#14306b" font-weight="800" text-anchor="middle">80</text><rect x="101" y="153.0" width="5" height="22" rx="2" fill="#2f6fe0"/><text x="70" y="272" font-size="12" fill="#14306b" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">emitters[0]</text><rect x="125" y="76" width="90" height="170" rx="6" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><line x1="152.0" y1="88" x2="188.0" y2="88" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="152.0" y1="95" x2="188.0" y2="95" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="152.0" y1="102" x2="188.0" y2="102" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><circle cx="170.0" cy="126" r="15" fill="#7c5cff"/><text x="170.0" y="132" font-size="15" fill="#fff" font-weight="800" text-anchor="middle">1</text><text x="170.0" y="191.0" font-size="22" fill="#14306b" font-weight="800" text-anchor="middle">45</text><rect x="201" y="153.0" width="5" height="22" rx="2" fill="#2f6fe0"/><text x="170" y="272" font-size="12" fill="#14306b" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">emitters[1]</text><rect x="225" y="76" width="90" height="170" rx="6" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><line x1="252.0" y1="88" x2="288.0" y2="88" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="252.0" y1="95" x2="288.0" y2="95" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="252.0" y1="102" x2="288.0" y2="102" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><circle cx="270.0" cy="126" r="15" fill="#7c5cff"/><text x="270.0" y="132" font-size="15" fill="#fff" font-weight="800" text-anchor="middle">2</text><text x="270.0" y="191.0" font-size="22" fill="#14306b" font-weight="800" text-anchor="middle">90</text><rect x="301" y="153.0" width="5" height="22" rx="2" fill="#2f6fe0"/><text x="270" y="272" font-size="12" fill="#14306b" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">emitters[2]</text><rect x="325" y="76" width="90" height="170" rx="6" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><line x1="352.0" y1="88" x2="388.0" y2="88" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="352.0" y1="95" x2="388.0" y2="95" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="352.0" y1="102" x2="388.0" y2="102" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><circle cx="370.0" cy="126" r="15" fill="#7c5cff"/><text x="370.0" y="132" font-size="15" fill="#fff" font-weight="800" text-anchor="middle">3</text><text x="370.0" y="191.0" font-size="22" fill="#14306b" font-weight="800" text-anchor="middle">30</text><rect x="401" y="153.0" width="5" height="22" rx="2" fill="#2f6fe0"/><text x="370" y="272" font-size="12" fill="#14306b" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">emitters[3]</text><rect x="425" y="76" width="90" height="170" rx="6" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><line x1="452.0" y1="88" x2="488.0" y2="88" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="452.0" y1="95" x2="488.0" y2="95" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><line x1="452.0" y1="102" x2="488.0" y2="102" stroke="#2f6fe0" stroke-width="1.5" opacity=".5"/><circle cx="470.0" cy="126" r="15" fill="#7c5cff"/><text x="470.0" y="132" font-size="15" fill="#fff" font-weight="800" text-anchor="middle">4</text><text x="470.0" y="191.0" font-size="22" fill="#14306b" font-weight="800" text-anchor="middle">100</text><rect x="501" y="153.0" width="5" height="22" rx="2" fill="#2f6fe0"/><text x="470" y="272" font-size="12" fill="#14306b" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">emitters[4]</text><rect x="525" y="76" width="90" height="170" rx="6" fill="#fff7f7" stroke="#b3261e" stroke-width="2" stroke-dasharray="6,4"/><text x="570.0" y="126" font-size="18" fill="#b3261e" font-weight="800" text-anchor="middle">5</text><text x="570.0" y="181.0" font-size="24" fill="#b3261e" font-weight="800" text-anchor="middle">✗</text><text x="570.0" y="228" font-size="12" fill="#b3261e" font-weight="700" text-anchor="middle">not yours</text><text x="570" y="272" font-size="12" fill="#b3261e" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">emitters[5]</text><line x1="530" y1="268" x2="610" y2="268" stroke="#b3261e" stroke-width="1.5"/><text x="320" y="302" font-size="13" fill="#5b6b86" text-anchor="middle" font-style="italic">5 lockers, numbered 0 to 4. Same size, same type, one name.</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">A row has 5 lockers. What is the number of the last one?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 5, because there are five lockers</li>
            <li class="quiz-option quiz-correct">B. 4, because counting starts at 0 ✓</li>
            <li class="quiz-option">C. 6, one extra is kept at the end</li>
            <li class="quiz-option">D. 1, the last is numbered first</li>
        </ul>
        <p class="quiz-explanation"><em>Counting starts at 0, so the highest number is always one less than the size: 0, 1, 2, 3, 4.</em></p>
    </div>

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

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">What does the code example print on the “Last:” line?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Last: 80</li>
            <li class="quiz-option">B. Last: 30</li>
            <li class="quiz-option">C. Last: 5</li>
            <li class="quiz-option quiz-correct">D. Last: 100 ✓</li>
        </ul>
        <p class="quiz-explanation"><em><code>emitters[4]</code> is the fifth and last value in the list, which is 100.</em></p>
    </div>

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
            <li class="quiz-option">A. One value, like any other variable</li>
            <li class="quiz-option">B. Values of any mix of types together</li>
            <li class="quiz-option quiz-correct">C. Several values of one type, one name ✓</li>
            <li class="quiz-option">D. Only text values, never numbers</li>
        </ul>
        <p class="quiz-explanation"><em>An array groups several values of the same type together under a single name, stored one after another.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In <code>int emitters[5] = {80, 45, 90, 30, 100};</code>, what is <code>emitters[0]</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 5 — the array's size</li>
            <li class="quiz-option quiz-correct">B. 80 — the first value ✓</li>
            <li class="quiz-option">C. 100 — the last value</li>
            <li class="quiz-option">D. 45 — the second value</li>
        </ul>
        <p class="quiz-explanation"><em>C array indexing starts at 0, so index 0 is always the first element — here, 80.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. In that same 5-element array, what is the highest valid index?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 5</li>
            <li class="quiz-option">B. 6</li>
            <li class="quiz-option">C. 1</li>
            <li class="quiz-option quiz-correct">D. 4 ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Since indexing starts at 0, an array of 5 elements has valid indices 0, 1, 2, 3 and 4 — one less than its size.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does <code>[5]</code> mean in <code>int emitters[5]</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The array holds 5 elements ✓</li>
            <li class="quiz-option">B. The array's first value is 5</li>
            <li class="quiz-option">C. The highest index is 5</li>
            <li class="quiz-option">D. Each value can be at most 5</li>
        </ul>
        <p class="quiz-explanation"><em>The number in square brackets when declaring an array sets how many elements it holds.</em></p>
    </div>

</div>
