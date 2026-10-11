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

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Every panel is patched. The last thing anyone needs is one number: what the whole hull is putting out,
        all rows, all panels, added up.
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Walk a row, adding as I go. Then drop down and walk the next one. Same running
        total the whole way — I just don't put it down between rows."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "So a walk inside a walk."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "A walk inside a walk. The outer one picks which row I'm on. The inner one does
        the whole row before the outer one moves down. Nothing gets counted twice, nothing gets missed."
    </p>

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
            <li class="quiz-option">D. Nothing — only the inner loop matters</li>
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
            <li class="quiz-option">A. Every column first, across all rows, before moving to the next column</li>
            <li class="quiz-option quiz-correct">B. Every column of row 0 first, then every column of row 1, and so on ✓</li>
            <li class="quiz-option">C. In a random order each time the program runs</li>
            <li class="quiz-option">D. Only the first cell of each row is ever visited</li>
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
            <li class="quiz-option">A. Nothing — C ignores misplaced braces</li>
            <li class="quiz-option quiz-correct">B. That update line falls outside the inner loop, so it doesn't run for every cell the way it was meant to ✓</li>
            <li class="quiz-option">C. The program automatically fixes the brace placement</li>
            <li class="quiz-option">D. It causes total to reset to 0 every time</li>
        </ul>
        <p class="quiz-explanation"><em>Brace placement decides exactly what's inside a loop — a line left outside the inner loop's braces only runs once (or as part of the outer loop) instead of for every cell.</em></p>
    </div>

</div>
