{{-- M4 · Lesson 4.5: Scanning the Grid — content fragment.
Syllabus: CC102 Unit 4 — Arrays (introduction to multidimensional arrays: traversal). --}}

<div class="lesson-fragment" data-module="m4" data-lesson="05">

    <h1 class="lesson-heading">Lesson 4.5: Scanning the Grid</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can use a nested <code>for</code> loop to visit every element of a
        two-dimensional array, and combine that with an accumulator to compute a grid-wide total.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Traversing a 2D array takes two loops, one inside the other — an <strong>outer loop</strong> for the
        row, and an <strong>inner loop</strong> for the column within that row:
    </p>

    <pre class="code-block"><code>for (int r = 0; r &lt; 2; r++) {
    for (int c = 0; c &lt; 3; c++) {
        printf("%d\n", grid[r][c]);
    }
}</code></pre>

    <p class="body-text">
        For every single value of <code>r</code>, the <em>entire</em> inner loop runs — all values of
        <code>c</code>, from start to finish — before <code>r</code> advances to the next row. With 2 rows and
        3 columns, that's 2 × 3 = 6 total visits: <code>(0,0), (0,1), (0,2), (1,0), (1,1), (1,2)</code>, in that
        exact order.
    </p>

    <p class="body-text">
        Combine this with Lesson 4.3's accumulator pattern, and you can sum an entire grid: declare
        <code>total = 0</code> outside both loops, and add <code>grid[r][c]</code> into it on every visit.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">For each single value of <code>r</code>, how much of the inner loop runs?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. All of it: every value of <code>c</code> ✓</li>
            <li class="quiz-option">B. One step: a single value of <code>c</code></li>
            <li class="quiz-option">C. None of it: <code>c</code> waits for the end</li>
            <li class="quiz-option">D. Half of it: every other value of <code>c</code></li>
        </ul>
        <p class="quiz-explanation"><em>The inner loop finishes a whole row before <code>r</code> moves to the next one.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        An usher counting tickets doesn't jump around the cinema. They pick a row, walk <strong>every seat in it</strong> from left to right, then move to the next row and do it all again.
    </p>

    <p class="body-text">
        The row is the <strong>outer loop</strong> (<code>r</code>), and the seat is the <strong>inner loop</strong> (<code>c</code>). For every single row, the inner loop walks the whole row before <code>r</code> moves on. It's like a clock: the minute hand goes all the way round before the hour hand moves one step.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 315" role="img" aria-label="An usher visits seats in order 1 to 6: row 0 seats 0, 1, 2, then on to row 1 seats 0, 1, 2. The outer loop r picks the row, the inner loop c walks every seat, 2 rows times 3 seats is 6 visits.">
            <text x="210" y="40" font-size="14" fill="#0b6670" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">c = 0</text><text x="330" y="40" font-size="14" fill="#0b6670" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">c = 1</text><text x="450" y="40" font-size="14" fill="#0b6670" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">c = 2</text><text x="118" y="101" font-size="14" fill="#4a35b8" font-weight="800" text-anchor="end" font-family="ui-monospace, Menlo, Consolas, monospace">r = 0</text><rect x="160" y="60" width="100" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><rect x="168" y="66" width="84" height="14" rx="6" fill="#2f6fe0"/><text x="210" y="116" font-size="20" fill="#14306b" font-weight="800" text-anchor="middle">1</text><circle cx="164" cy="64" r="13" fill="#e8457c" stroke="#fff" stroke-width="2"/><text x="164" y="69" font-size="13" fill="#fff" font-weight="800" text-anchor="middle">1</text><rect x="280" y="60" width="100" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><rect x="288" y="66" width="84" height="14" rx="6" fill="#2f6fe0"/><text x="330" y="116" font-size="20" fill="#14306b" font-weight="800" text-anchor="middle">2</text><circle cx="284" cy="64" r="13" fill="#e8457c" stroke="#fff" stroke-width="2"/><text x="284" y="69" font-size="13" fill="#fff" font-weight="800" text-anchor="middle">2</text><rect x="400" y="60" width="100" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><rect x="408" y="66" width="84" height="14" rx="6" fill="#2f6fe0"/><text x="450" y="116" font-size="20" fill="#14306b" font-weight="800" text-anchor="middle">3</text><circle cx="404" cy="64" r="13" fill="#e8457c" stroke="#fff" stroke-width="2"/><text x="404" y="69" font-size="13" fill="#fff" font-weight="800" text-anchor="middle">3</text><text x="118" y="201" font-size="14" fill="#4a35b8" font-weight="800" text-anchor="end" font-family="ui-monospace, Menlo, Consolas, monospace">r = 1</text><rect x="160" y="160" width="100" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><rect x="168" y="166" width="84" height="14" rx="6" fill="#2f6fe0"/><text x="210" y="216" font-size="20" fill="#14306b" font-weight="800" text-anchor="middle">4</text><circle cx="164" cy="164" r="13" fill="#e8457c" stroke="#fff" stroke-width="2"/><text x="164" y="169" font-size="13" fill="#fff" font-weight="800" text-anchor="middle">4</text><rect x="280" y="160" width="100" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><rect x="288" y="166" width="84" height="14" rx="6" fill="#2f6fe0"/><text x="330" y="216" font-size="20" fill="#14306b" font-weight="800" text-anchor="middle">5</text><circle cx="284" cy="164" r="13" fill="#e8457c" stroke="#fff" stroke-width="2"/><text x="284" y="169" font-size="13" fill="#fff" font-weight="800" text-anchor="middle">5</text><rect x="400" y="160" width="100" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><rect x="408" y="166" width="84" height="14" rx="6" fill="#2f6fe0"/><text x="450" y="216" font-size="20" fill="#14306b" font-weight="800" text-anchor="middle">6</text><circle cx="404" cy="164" r="13" fill="#e8457c" stroke="#fff" stroke-width="2"/><text x="404" y="169" font-size="13" fill="#fff" font-weight="800" text-anchor="middle">6</text><line x1="262" y1="105" x2="270.0" y2="105.0" stroke="#e8457c" stroke-width="2.5"/><polygon points="278,105 270.0,99.0 270.0,111.0" fill="#e8457c"/><line x1="382" y1="105" x2="390.0" y2="105.0" stroke="#e8457c" stroke-width="2.5"/><polygon points="398,105 390.0,99.0 390.0,111.0" fill="#e8457c"/><line x1="262" y1="205" x2="270.0" y2="205.0" stroke="#e8457c" stroke-width="2.5"/><polygon points="278,205 270.0,199.0 270.0,211.0" fill="#e8457c"/><line x1="382" y1="205" x2="390.0" y2="205.0" stroke="#e8457c" stroke-width="2.5"/><polygon points="398,205 390.0,199.0 390.0,211.0" fill="#e8457c"/><path d="M502 95 H540 V145 H140 V195 H150" fill="none" stroke="#e8457c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="6,5"/><polygon points="157,195 148.0,189.0 148.0,201.0" fill="#e8457c"/><text x="548" y="112" font-size="12" fill="#9c1f4c" font-weight="700">row done:</text><text x="548" y="128" font-size="12" fill="#9c1f4c" font-weight="700">next row</text><rect x="20" y="268" width="190" height="36" rx="18.0" fill="#f3f0ff" stroke="#7c5cff" stroke-width="1.8"/><text x="115.0" y="290.32" font-size="12" fill="#4a35b8" text-anchor="middle">outer <tspan font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="800">r</tspan> · picks the row</text><rect x="225" y="268" width="190" height="36" rx="18.0" fill="#e6f8f9" stroke="#0fa3b1" stroke-width="1.8"/><text x="320.0" y="290.32" font-size="12" fill="#0b6670" text-anchor="middle">inner <tspan font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="800">c</tspan> · walks every seat</text><rect x="430" y="268" width="190" height="36" rx="18.0" fill="#e9f7ef" stroke="#2fa56b" stroke-width="1.8"/><text x="525.0" y="290.32" font-size="12" fill="#1d6b43" text-anchor="middle">2 rows × 3 seats = 6 visits</text>
        </svg>
    </figure>

    <p class="body-text">
        Add a running total like the checkout screen from Lesson 4.3, and the usher can add up every seat in the whole cinema.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">2 rows, 3 seats each. Which seat does the usher visit right after <code>(0,2)</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. (0,3), the next seat along</li>
            <li class="quiz-option quiz-correct">B. (1,0), start of the next row ✓</li>
            <li class="quiz-option">C. (1,2), the seat right below</li>
            <li class="quiz-option">D. (0,0), back to the first seat</li>
        </ul>
        <p class="quiz-explanation"><em>Seat 2 is the end of row 0, so the inner loop is done. The outer loop moves to row 1, and the inner loop starts again at seat 0.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 500 230" role="img" aria-label="Nested loops visit every grid cell: all of row 0's columns first, then all of row 1's">
            @php $rows = [[1,2,3],[4,5,6]]; $order = 1; @endphp
            @foreach ($rows as $r => $row)
                @foreach ($row as $c => $v)
                    <rect x="{{ 100 + $c * 100 }}" y="{{ 40 + $r * 70 }}" width="90" height="60" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
                    <text x="{{ 145 + $c * 100 }}" y="{{ 65 + $r * 70 }}" text-anchor="middle" font-size="16" font-weight="700" fill="#4a35b8">{{ $v }}</text>
                    <text x="{{ 145 + $c * 100 }}" y="{{ 86 + $r * 70 }}" text-anchor="middle" font-size="11" fill="#64748b">visit #{{ $order }}</text>
                    @php $order++; @endphp
                @endforeach
            @endforeach
            <text x="20" y="75" font-size="13" font-weight="700" fill="#475569">r=0</text>
            <text x="20" y="145" font-size="13" font-weight="700" fill="#475569">r=1</text>
            <text x="250" y="200" text-anchor="middle" font-size="13" fill="#475569">Row 0 finishes fully before row 1 begins.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int grid[2][3] = @{{1, 2, 3}, {4, 5, 6}};

    for (int r = 0; r &lt; 2; r++) {
        for (int c = 0; c &lt; 3; c++) {
            printf("Cell %d,%d: %d\n", r, c, grid[r][c]);
        }
    }
    return 0;
}</code></pre>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">What is the last line the code example prints?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Cell 0,2: 3</li>
            <li class="quiz-option">B. Cell 2,3: 6</li>
            <li class="quiz-option">C. Cell 1,0: 4</li>
            <li class="quiz-option quiz-correct">D. Cell 1,2: 6 ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The last visit is row 1, column 2, which holds 6.</em></p>
    </div>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Declare <code>int panels[2][2] = @{{5, 10}, {20, 25}};</code> and an accumulator <code>total</code>
        starting at 0. Use a nested loop to add every cell into <code>total</code>, then print exactly:
    </p>

    <pre class="code-block"><code>Grid total: 60</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Scanning the Grid">
            <input type="hidden" name="filename"     value="grid_total.c">
            <input type="hidden" name="difficulty"   value="Hard">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int panels[2][2] = @{{5, 10}, {20, 25}};
    int total = 0;

    // nested for loop: add every panels[r][c] into total

    // print &quot;Grid total: &quot; + total

    return 0;
}
">
            <input type="hidden" name="instructions" value="panels is a 2x2 grid: @{{5, 10}, {20, 25}}. total starts at 0.

Write two nested for loops (r from 0 to 1, c from 0 to 1 inside it) that add every panels[r][c] into total. Then print:
Grid total: 60">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="for (int r = 0; r &lt; 2; r++) { for (int c = 0; c &lt; 2; c++) { total = total + panels[r][c]; } } — the inner loop's braces must be fully closed before the outer loop's braces close.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int panels[2][2] = @{{5, 10}, {20, 25}};
    int total = 0;

    for (int r = 0; r &lt; 2; r++) {
        for (int c = 0; c &lt; 2; c++) {
            total = total + panels[r][c];
        }
    }
    printf(&quot;Grid total: %d\n&quot;, total);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm4', 'lesson' => 'lesson05']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <p class="body-text" style="margin-top:2.5rem;">
        The panels light up one after another down the hull, and for the first time since the crash the
        Wanderer is warm. Rivet falls asleep at the workbench. Nobody wakes him.
    </p>

    <p class="body-text">
        In the morning he switches the console on, and it says:
    </p>

    <pre class="code-block"><code>HELLO. WHO ARE YOU?</code></pre>

    <p class="body-text">
        <strong>Volt</strong>, very quietly: "…It doesn't know us."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> is already typing, and stops. "It can think. It just can't <em>keep</em>
        anything. Every time we cut the power, it loses the lot."
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Then that's the next job. We teach it to remember."
    </p>

    <p class="body-text">
        Up on the Lookout, Volt is watching the city. Down in the dark, one streetlamp blinks on, off, on —
        still trying, after all this time, with nobody left to fix it.
    </p>

    <p class="body-text">
        <strong>Astro</strong>, following his eyes: "And then we go down there and switch that city back on."
    </p>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In a nested for loop traversing a 2D array, what does the outer loop typically control?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The column</li>
            <li class="quiz-option quiz-correct">B. The row ✓</li>
            <li class="quiz-option">C. The total sum</li>
            <li class="quiz-option">D. The size</li>
        </ul>
        <p class="quiz-explanation"><em>By convention, the outer loop's counter (often r) selects the row, and the inner loop's counter (often c) selects the column within it.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. For a grid with 2 rows and 3 columns, how many total cells does a full nested traversal visit?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 2</li>
            <li class="quiz-option">B. 3</li>
            <li class="quiz-option">C. 5</li>
            <li class="quiz-option quiz-correct">D. 6 ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Every row's columns are visited in full, so the total number of visits is rows times columns: 2 x 3 = 6.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. In what order does a nested loop visit a grid's cells?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. All of column 0 first, then all of column 1</li>
            <li class="quiz-option quiz-correct">B. All of row 0 first, then all of row 1 ✓</li>
            <li class="quiz-option">C. Only the first cell of every row, in order</li>
            <li class="quiz-option">D. Row 0 and row 1 together, one cell each</li>
        </ul>
        <p class="quiz-explanation"><em>The inner loop finishes completely for each value of the outer loop — so all of row 0's columns run before row 1 even begins.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. To sum every cell of a 2D array, where must the accumulator total be declared?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Inside the inner loop only</li>
            <li class="quiz-option">B. Inside the outer loop, but outside the inner loop</li>
            <li class="quiz-option quiz-correct">C. Outside both loops entirely ✓</li>
            <li class="quiz-option">D. It doesn't matter where it's declared</li>
        </ul>
        <p class="quiz-explanation"><em>Just like a single-loop accumulator, total must be declared outside both loops so it keeps its running value across every cell, not just one row.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If the inner loop's closing brace is accidentally placed one line too early — closing before total is updated for a cell — what happens?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing, since C ignores brace placement</li>
            <li class="quiz-option">B. The compiler moves the brace back for you</li>
            <li class="quiz-option">C. total is reset to 0 on every single pass</li>
            <li class="quiz-option quiz-correct">D. The update no longer runs for every cell ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Brace placement decides exactly what's inside a loop — a line left outside the inner loop's braces only runs once (or as part of the outer loop) instead of for every cell.</em></p>
    </div>

</div>
