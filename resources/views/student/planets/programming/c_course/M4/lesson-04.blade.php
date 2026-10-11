{{-- M4 · Lesson 4.4: The Grid Has Rows — content fragment.
Syllabus: CC102 Unit 4 — Arrays (introduction to multidimensional arrays). --}}

<div class="lesson-fragment" data-module="m4" data-lesson="04">

    <h1 class="lesson-heading">Lesson 4.4: The Grid Has Rows</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can declare and initialize a two-dimensional array, and read an
        individual element out of it using its row and column indices.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Every array so far has been a single line of values. A <strong>two-dimensional array</strong> is a grid
        — rows and columns — declared with two sizes in brackets:
    </p>

    <pre class="code-block"><code>int grid[2][3] = {
    {1, 2, 3},
    {4, 5, 6}
};</code></pre>

    <p class="body-text">
        <code>[2][3]</code> means 2 rows, 3 columns each. The initializer is a list of rows, each one itself a
        list of values in braces. To reach one element, give both indices — row first, then column:
        <code>grid[0][1]</code> is row 0, column 1, which is <code>2</code>. <code>grid[1][2]</code> is row 1,
        column 2, which is <code>6</code>.
    </p>

    <p class="body-text">
        Think of it as an array of arrays: <code>grid[0]</code> is the whole first row (<code>{1, 2, 3}</code>),
        and adding a second index picks one value out of that row.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Astro comes back from walking the outside of the ship with rain in his hair and a correction.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "It isn't a row. I've just walked it. The panels go across <em>and</em> down —
        two rows of three, all over the hull. Your one-line list can't say where anything actually is."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> stares at the ceiling for a second. "…Two numbers. I need two numbers. Which row
        down, then which one across." He starts drawing a grid on the board, fast. "Like seats. Row two, seat
        three. You can't find a seat with one number."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Row first, then across. Every time, same order."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Row first, then across. Swap them and you'll be staring at the wrong panel in
        the rain wondering why it looks fine."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 500 200" role="img" aria-label="A 2-row, 3-column grid with values 1 through 6, showing row and column indices">
            @php $rows = [[1,2,3],[4,5,6]]; @endphp
            @foreach ($rows as $r => $row)
                @foreach ($row as $c => $v)
                    <rect x="{{ 100 + $c * 100 }}" y="{{ 40 + $r * 70 }}" width="90" height="60" rx="10"
                          fill="{{ ($r === 0 && $c === 1) || ($r === 1 && $c === 2) ? '#e8f0ff' : '#f3f0ff' }}"
                          stroke="{{ ($r === 0 && $c === 1) || ($r === 1 && $c === 2) ? '#2f6fe0' : '#7c5cff' }}" stroke-width="2"/>
                    <text x="{{ 145 + $c * 100 }}" y="{{ 68 + $r * 70 }}" text-anchor="middle" font-size="18" font-weight="700"
                          fill="{{ ($r === 0 && $c === 1) || ($r === 1 && $c === 2) ? '#14306b' : '#4a35b8' }}">{{ $v }}</text>
                    <text x="{{ 145 + $c * 100 }}" y="{{ 88 + $r * 70 }}" text-anchor="middle" font-size="10.5" fill="#64748b">[{{ $r }}][{{ $c }}]</text>
                @endforeach
            @endforeach
            <text x="20" y="75" font-size="13" font-weight="700" fill="#475569">row 0</text>
            <text x="20" y="145" font-size="13" font-weight="700" fill="#475569">row 1</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int grid[2][3] = @{{1, 2, 3}, {4, 5, 6}};

    printf("Row0Col1: %d\n", grid[0][1]);
    printf("Row1Col2: %d\n", grid[1][2]);
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Declare <code>int panels[2][3] = @{{5, 10, 15}, {20, 25, 30}};</code>, then print the value at row 0,
        column 2, and the value at row 1, column 0. Expected output:
    </p>

    <pre class="code-block"><code>Panel[0][2]: 15
Panel[1][0]: 20</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="The Grid Has Rows">
            <input type="hidden" name="filename"     value="panel_grid.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int panels[2][3] = @{{5, 10, 15}, {20, 25, 30}};

    // print panels[0][2] labeled &quot;Panel[0][2]: &quot;
    // print panels[1][0] labeled &quot;Panel[1][0]: &quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="panels is a 2-row, 3-column grid: @{{5, 10, 15}, {20, 25, 30}}.

Print exactly:
Panel[0][2]: 15
Panel[1][0]: 20

Row index first, then column index, in panels[row][col].">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Row 0 is {5, 10, 15}, so column 2 of it is 15. Row 1 is {20, 25, 30}, so column 0 of it is 20. panels[0][2] and panels[1][0] read exactly those two values.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int panels[2][3] = @{{5, 10, 15}, {20, 25, 30}};

    printf(&quot;Panel[0][2]: %d\n&quot;, panels[0][2]);
    printf(&quot;Panel[1][0]: %d\n&quot;, panels[1][0]);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm4', 'lesson' => 'lesson04']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does <code>int grid[2][3]</code> declare?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A single array of 6 values</li>
            <li class="quiz-option quiz-correct">B. A grid with 2 rows and 3 columns each ✓</li>
            <li class="quiz-option">C. 2 separate arrays of unrelated sizes</li>
            <li class="quiz-option">D. An array that can hold either 2 or 3 values</li>
        </ul>
        <p class="quiz-explanation"><em>Two sizes in brackets declare a two-dimensional array: the first is the number of rows, the second the number of columns in each row.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In <code>grid[0][1]</code>, which index comes first — row or column?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Column, then row</li>
            <li class="quiz-option quiz-correct">B. Row, then column ✓</li>
            <li class="quiz-option">C. It doesn't matter, they're interchangeable</li>
            <li class="quiz-option">D. Neither — the two brackets mean the same thing</li>
        </ul>
        <p class="quiz-explanation"><em>By convention (and how it's declared), the first index selects the row and the second selects the column within that row.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Given <code>int grid[2][3] = @{{1, 2, 3}, {4, 5, 6}};</code>, what is <code>grid[1][2]</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 3</li>
            <li class="quiz-option">B. 4</li>
            <li class="quiz-option quiz-correct">C. 6 ✓</li>
            <li class="quiz-option">D. 2</li>
        </ul>
        <p class="quiz-explanation"><em>Row 1 is {4, 5, 6}, and column 2 within that row (the third value, since indexing starts at 0) is 6.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. How is a 2D array's initializer written?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. As one flat list of all the values with no inner braces</li>
            <li class="quiz-option quiz-correct">B. As a list of rows, each row itself a list of values in braces ✓</li>
            <li class="quiz-option">C. As a single number representing the total count</li>
            <li class="quiz-option">D. 2D arrays cannot be initialized at declaration</li>
        </ul>
        <p class="quiz-explanation"><em>Each inner {} groups one row's values, and the outer {} groups the rows themselves — matching the grid's shape.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why did Astro need a 2D array instead of the 1D arrays from earlier lessons for the ship's real hull panel layout?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 1D arrays cannot hold more than 3 values</li>
            <li class="quiz-option quiz-correct">B. The real grid is arranged in rows and columns, and a 2D array can represent that shape directly ✓</li>
            <li class="quiz-option">C. 2D arrays are always faster to search than 1D arrays</li>
            <li class="quiz-option">D. There's no real difference — 1D would have worked identically</li>
        </ul>
        <p class="quiz-explanation"><em>A 2D array's row/column structure matches how the emitters are actually laid out — a 1D array would lose that shape entirely.</em></p>
    </div>

</div>
