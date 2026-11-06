{{-- M5 · Lesson 5.2: As Many Cars as It Takes — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Dynamic Variables — creating dynamic arrays at run time. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="02">

    <h1 class="lesson-heading">Lesson 5.2: As Many Cars as It Takes</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can create an array whose size is decided while the program runs, use it with normal indexing, and free it.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A normal array's size is fixed when you write the code. But often you only find out how many you need while the program is running: how many cars, how many students, how many readings in a file.
    </p>

    <p class="body-text">
        <code>malloc</code> solves it. Ask for <em>n</em> elements' worth of bytes, and the pointer it gives back works exactly like an array:
    </p>

    <pre class="code-block"><code>int cars = 4;     // could come from scanf, a file, anywhere
int *seats = (int *) malloc(cars * sizeof(int));

seats[0] = 10;    // index it like any array</code></pre>

    <p class="body-text">
        That works because of Lesson 3.4: <code>seats[i]</code> is just <code>*(seats + i)</code>, and pointer arithmetic does not care whether the memory came from an array or from <code>malloc</code>.
    </p>

    <p class="body-text">
        Two relatives of <code>malloc</code>, also in <code>&lt;stdlib.h&gt;</code>:
    </p>

    <ul class="body-list">
        <li><code>calloc(n, sizeof(int))</code> reserves <em>n</em> elements and sets them all to 0.</li>
        <li><code>realloc(p, newBytes)</code> resizes a block you already have, keeping what was in it.</li>
    </ul>

    <p class="body-text">
        One <code>free(seats);</code> gives back the whole block, however many elements it had.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        The line needs cars again. How many depends on the day: more when the market is busy, fewer at night. The old depot console decided every morning.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Today it's four cars. Tomorrow it might be six. I don't want a program that only ever knows one number."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "So we ask the yard for room for however many we need, worked out while the program runs. And then it's just a row, like Lamp Street. Same brackets and everything."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And at the end of the day, all of it goes back. Not three cars and one left rusting on the track."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="malloc reserves room for cars times sizeof int on the heap; seats indexes it like an array: 10, 12, 14 and 16, totalling 52">
            <rect x="20" y="60" width="150" height="70" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="95" y="92" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">seats</text>
            <text x="95" y="109" text-anchor="middle" font-size="12" fill="#14306b">int *</text>
            <rect x="240" y="60" width="85" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="282.5" y="94" text-anchor="middle" font-size="16" font-weight="700" fill="#7a5a00">10</text>
            <rect x="335" y="60" width="85" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="377.5" y="94" text-anchor="middle" font-size="16" font-weight="700" fill="#7a5a00">12</text>
            <rect x="430" y="60" width="85" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="472.5" y="94" text-anchor="middle" font-size="16" font-weight="700" fill="#7a5a00">14</text>
            <rect x="525" y="60" width="85" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="567.5" y="94" text-anchor="middle" font-size="16" font-weight="700" fill="#7a5a00">16</text>
            <text x="282" y="134" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">seats[0]</text>
            <text x="377" y="134" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">seats[1]</text>
            <text x="472" y="134" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">seats[2]</text>
            <text x="567" y="134" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">seats[3]</text>
            <line x1="170" y1="90" x2="226.0" y2="88.3" stroke="#d4a017" stroke-width="2.5"/>
            <polygon points="236,88 223.8,82.4 224.2,94.4" fill="#d4a017"/>
            <text x="420" y="40" text-anchor="middle" font-size="12" font-weight="700" fill="#7a5a00" font-family="ui-monospace, monospace">malloc(cars * sizeof(int)), cars = 4</text>
            <text x="320" y="180" text-anchor="middle" font-size="13" fill="#475569">total seats: 52, then one free(seats) gives the whole block back</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;
#include &lt;stdlib.h&gt;

int main() {
    int n = 3;
    int *readings = (int *) calloc(n, sizeof(int));   // all start at 0
    if (readings == NULL) {
        return 1;
    }

    readings[2] = 9;
    printf("%d %d %d\n", readings[0], readings[1], readings[2]);   // 0 0 9
    free(readings);
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        With <code>int cars = 4;</code>, <code>malloc</code> an array of <code>cars</code> ints called <code>seats</code> and check it. Fill it so car <code>i</code> has <code>10 + i * 2</code> seats, add them up, print the results, then <code>free</code> the block. Exactly:
    </p>

    <pre class="code-block"><code>Cars: 4
Seats: 52</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="As Many Cars as It Takes">
            <input type="hidden" name="filename"     value="tram_cars.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;
#include &lt;stdlib.h&gt;

int main() {
    int cars = 4;
    int total = 0;

    // malloc room for cars ints, called seats, and check it is not NULL

    // loop: seats[i] = 10 + i * 2, and add each one into total

    // print &quot;Cars: &quot; and &quot;Seats: &quot;, then free the block

    return 0;
}
">
            <input type="hidden" name="instructions" value="Build today's tram: 4 cars.

1. int *seats = (int *) malloc(cars * sizeof(int));
   (if it is NULL, return 1)
2. For i from 0 while i &lt; cars: seats[i] = 10 + i * 2; total += seats[i];
3. Print the count and the total, then free(seats);

The output must be exactly:
Cars: 4
Seats: 52">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="The size is cars * sizeof(int), so the block grows with the number of cars. After that, seats[i] works like any array. 10 + 12 + 14 + 16 is 52. One free(seats); at the end gives back all four.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;
#include &lt;stdlib.h&gt;

int main() {
    int cars = 4;
    int total = 0;

    int *seats = (int *) malloc(cars * sizeof(int));
    if (seats == NULL) {
        return 1;
    }

    for (int i = 0; i &lt; cars; i++) {
        seats[i] = 10 + i * 2;
        total += seats[i];
    }

    printf(&quot;Cars: %d\n&quot;, cars);
    printf(&quot;Seats: %d\n&quot;, total);
    free(seats);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm5', 'lesson' => 'lesson02']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why use <code>malloc</code> for an array instead of <code>int seats[4];</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It takes less code to write</li>
            <li class="quiz-option">B. malloc arrays are always faster</li>
            <li class="quiz-option quiz-correct">C. The size can be set while it runs ✓</li>
            <li class="quiz-option">D. Normal arrays cannot hold ints</li>
        </ul>
        <p class="quiz-explanation"><em>A malloc'd block can be exactly as big as a number only known at run time.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. How many bytes does <code>malloc(cars * sizeof(int))</code> ask for?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Exactly cars bytes</li>
            <li class="quiz-option">B. One int's worth</li>
            <li class="quiz-option">C. Always 4 bytes</li>
            <li class="quiz-option quiz-correct">D. Enough for cars ints ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Multiplying the count by one element's size gives room for the whole row.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why can you write <code>seats[2]</code> on a pointer from malloc?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. malloc turns it into a real array</li>
            <li class="quiz-option quiz-correct">B. seats[2] just means *(seats + 2) ✓</li>
            <li class="quiz-option">C. Only calloc'd memory allows it</li>
            <li class="quiz-option">D. seats[2] means seats times 2</li>
        </ul>
        <p class="quiz-explanation"><em>Indexing is pointer arithmetic, so it works the same on malloc'd memory.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does <code>calloc(n, sizeof(int))</code> do that malloc does not?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Sets every element to 0 ✓</li>
            <li class="quiz-option">B. Frees the memory</li>
            <li class="quiz-option">C. Makes the block twice as big</li>
            <li class="quiz-option">D. Returns an int</li>
        </ul>
        <p class="quiz-explanation"><em>calloc reserves n elements and clears them all to zero.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. How do you give back a block of 4 ints made with one malloc?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. One call: <code>free(seats);</code> ✓</li>
            <li class="quiz-option">B. Four calls, one per element</li>
            <li class="quiz-option">C. Set it: <code>seats = 0;</code></li>
            <li class="quiz-option">D. Nothing; it frees itself</li>
        </ul>
        <p class="quiz-explanation"><em>One malloc, one free: the whole block goes back together.</em></p>
    </div>

</div>
