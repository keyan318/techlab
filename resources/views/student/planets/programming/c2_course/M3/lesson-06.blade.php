{{-- M3 · Lesson 3.6: The Block Map — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Arrays — array of arrays (2D arrays, arrays of strings, 2D arrays as parameters). --}}

<div class="lesson-fragment" data-module="m3" data-lesson="06">

    <h1 class="lesson-heading">Lesson 3.6: The Block Map</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can store a table in an array of arrays, pass it to a function, and keep a list of names as an array of strings.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        An <strong>array of arrays</strong> is a table: rows and columns. Each row is itself an array.
    </p>

    <pre class="code-block"><code>int blocks[2][3] = { {10, 20, 30},     // row 0
                     {5, 5, 5} };      // row 1</code></pre>

    <p class="body-text">
        <code>blocks[r][c]</code> is row <code>r</code>, column <code>c</code>. <code>blocks[0][2]</code> is 30.
    </p>

    <p class="body-text">
        To pass a table to a function, the parameter must give the <strong>number of columns</strong>; the rows can stay empty. C needs the column count to know where each row starts:
    </p>

    <pre class="code-block"><code>int blockPower(int grid[][3], int row) {
    int total = 0;
    for (int c = 0; c &lt; 3; c++) {
        total += grid[row][c];
    }
    return total;
}</code></pre>

    <p class="body-text">
        Strings fit the same pattern. A string is a <code>char</code> array, so a list of strings is an array of char arrays:
    </p>

    <pre class="code-block"><code>char names[2][10] = {"Main", "Harbor"};   // 2 names, up to 9 letters each</code></pre>

    <p class="body-text">
        <code>names[1]</code> is the whole string <code>"Harbor"</code>, ready for <code>%s</code>.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Lamp Street is glowing. The bakery sign says <code>BAKERY</code>, and somebody, probably Volt, has propped the bakery door open.
    </p>

    <p class="body-text">
        From the top of the street, the control box can see further: two whole blocks of the city, each with three lamp posts, laid out like a table. Rows and columns.
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "A table is just a row of rows! Row zero is Main block, row one is Harbor block. And the block names are a row of words, and each word is a row of letters. It's rows all the way down."
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "One number for each block. Then we know which one to fix next."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="A 2 by 3 array: row Main holds 10, 20, 30 for a total of 60; row Harbor holds 5, 5, 5 for a total of 15">
            <text x="70" y="62" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">Main</text>
            <text x="70" y="132" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">Harbor</text>
            <rect x="130" y="35" width="90" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="175" y="66" text-anchor="middle" font-size="16" font-weight="700" fill="#7a5a00">10</text>
            <rect x="230" y="35" width="90" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="275" y="66" text-anchor="middle" font-size="16" font-weight="700" fill="#7a5a00">20</text>
            <rect x="330" y="35" width="90" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="375" y="66" text-anchor="middle" font-size="16" font-weight="700" fill="#7a5a00">30</text>
            <rect x="130" y="105" width="90" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="175" y="136" text-anchor="middle" font-size="16" font-weight="700" fill="#7a5a00">5</text>
            <rect x="230" y="105" width="90" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="275" y="136" text-anchor="middle" font-size="16" font-weight="700" fill="#7a5a00">5</text>
            <rect x="330" y="105" width="90" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="375" y="136" text-anchor="middle" font-size="16" font-weight="700" fill="#7a5a00">5</text>
            <line x1="430" y1="60" x2="476.0" y2="60.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="486,60 474.0,54.0 474.0,66.0" fill="#7c5cff"/>
            <line x1="430" y1="130" x2="476.0" y2="130.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="486,130 474.0,124.0 474.0,136.0" fill="#7c5cff"/>
            <text x="550" y="65" text-anchor="middle" font-size="16" font-weight="700" fill="#14532d">= 60</text>
            <text x="550" y="135" text-anchor="middle" font-size="16" font-weight="700" fill="#14532d">= 15</text>
            <text x="320" y="190" text-anchor="middle" font-size="13" fill="#475569">blockPower(blocks, row) adds one row</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    char stops[3][10] = {"Station", "Market", "Park"};
    int riders[3][2] = { {4, 6}, {2, 2}, {7, 1} };

    for (int s = 0; s &lt; 3; s++) {
        printf("%s: %d\n", stops[s], riders[s][0] + riders[s][1]);
    }
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. Write <code>int blockPower(int grid[][3], int row)</code> that returns the total of one row. In <code>main</code>, use <code>int blocks[2][3] = { {10, 20, 30}, {5, 5, 5} };</code> and <code>char names[2][10] = {"Main", "Harbor"};</code>, and loop over both blocks to print exactly:
    </p>

    <pre class="code-block"><code>Main: 60
Harbor: 15</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="The Block Map">
            <input type="hidden" name="filename"     value="block_map.c">
            <input type="hidden" name="difficulty"   value="Hard">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

// define int blockPower(int grid[][3], int row): add up the 3 columns of that row

int main() {
    int blocks[2][3] = { {10, 20, 30}, {5, 5, 5} };
    char names[2][10] = {&quot;Main&quot;, &quot;Harbor&quot;};

    // loop over the 2 blocks: print &quot;&lt;name&gt;: &lt;total&gt;&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="Two blocks of the city, three lamp posts each.

1. Define int blockPower(int grid[][3], int row) that adds grid[row][0..2].
2. In main, loop b from 0 while b &lt; 2 and print:
   names[b] with %s, then blockPower(blocks, b)

The output must be exactly:
Main: 60
Harbor: 15">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="In blockPower, loop c from 0 to 2 and add grid[row][c] into a total. In main, one loop does both blocks: printf(&quot;%s: %d\n&quot;, names[b], blockPower(blocks, b)); The [3] in the parameter is required; the first [] can stay empty.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int blockPower(int grid[][3], int row) {
    int total = 0;
    for (int c = 0; c &lt; 3; c++) {
        total += grid[row][c];
    }
    return total;
}

int main() {
    int blocks[2][3] = { {10, 20, 30}, {5, 5, 5} };
    char names[2][10] = {&quot;Main&quot;, &quot;Harbor&quot;};

    for (int b = 0; b &lt; 2; b++) {
        printf(&quot;%s: %d\n&quot;, names[b], blockPower(blocks, b));
    }
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm3', 'lesson' => 'lesson06']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <p class="body-text" style="margin-top:2.5rem;">
        Rivet adds a cable from Lamp Street's box to Main block, then Harbor block. Lights come on in rows, block by block, until the whole middle of the city is lit. From up here, it looks like somebody drew it with a ruler.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Half the city. Good."
    </p>

    <p class="body-text">
        But the next part of the map is different. Not rows of lamps that all work the same way: a fountain, a tram, a clock tower, a pump. Each one has a name, a power level, an on or off, a street. <strong>Volt</strong>: "You can't put a fountain in a row of numbers."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "No. You need a card for each one. City Hall used to keep them all."
    </p>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In <code>int blocks[2][3];</code>, how many ints are there in total?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 2</li>
            <li class="quiz-option">B. 3</li>
            <li class="quiz-option">C. 5</li>
            <li class="quiz-option quiz-correct">D. 6 ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Two rows of three columns: 2 x 3 = 6 ints.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. With <code>blocks = { {10, 20, 30}, {5, 5, 5} }</code>, what is <code>blocks[0][2]</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 10</li>
            <li class="quiz-option">B. 20</li>
            <li class="quiz-option quiz-correct">C. 30 ✓</li>
            <li class="quiz-option">D. 5</li>
        </ul>
        <p class="quiz-explanation"><em>Row 0 is 10, 20, 30, and column 2 of that row is 30.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which parameter correctly receives a table with 3 columns?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>int grid[][]</code></li>
            <li class="quiz-option quiz-correct">B. <code>int grid[][3]</code> ✓</li>
            <li class="quiz-option">C. <code>int grid[3][]</code></li>
            <li class="quiz-option">D. <code>int grid</code></li>
        </ul>
        <p class="quiz-explanation"><em>C needs the number of columns to find each row; the number of rows can be left out.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What is <code>char names[2][10]</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Two strings of up to 9 letters ✓</li>
            <li class="quiz-option">B. Ten strings of up to 1 letter</li>
            <li class="quiz-option">C. One string of up to 20 letters</li>
            <li class="quiz-option">D. Two strings of up to 10 letters</li>
        </ul>
        <p class="quiz-explanation"><em>It is 2 char arrays of 10, so 2 strings, each with room for 9 letters plus the '\0'.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. In the task, what does <code>names[1]</code> hold?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. "Main"</li>
            <li class="quiz-option quiz-correct">B. "Harbor" ✓</li>
            <li class="quiz-option">C. 'H'</li>
            <li class="quiz-option">D. 15</li>
        </ul>
        <p class="quiz-explanation"><em>names[1] is the whole second row of letters: the string "Harbor".</em></p>
    </div>

</div>
