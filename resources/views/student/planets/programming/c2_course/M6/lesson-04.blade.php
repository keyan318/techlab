{{-- M6 · Lesson 6.4: Neat Columns — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Files — formatted input and output (fprintf, fscanf). --}}

<div class="lesson-fragment" data-module="m6" data-lesson="04">

    <h1 class="lesson-heading">Lesson 6.4: Neat Columns</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write values to a file in columns with <code>fprintf</code>, read them back into variables with <code>fscanf</code>, and use <code>fscanf</code>'s return value to stop.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <code>fprintf</code> and <code>fscanf</code> are <code>printf</code> and <code>scanf</code> with one extra first argument: the file. Same <code>%d</code>, <code>%s</code>, <code>%lf</code> codes.
    </p>

    <pre class="code-block"><code>fprintf(f, "%s %d\n", "Station", 40);      // writes: Station 40</code></pre>

    <p class="body-text">
        Reading it back puts the values straight into variables, numbers as numbers:
    </p>

    <pre class="code-block"><code>char name[20];
int riders;
fscanf(f, "%s %d", name, &amp;riders);         // name is "Station", riders is 40</code></pre>

    <p class="body-text">
        As with <code>scanf</code>, a number needs <code>&amp;</code>, while a char array does not (its name is already an address, Lesson 3.4). <code>%s</code> reads one word: it stops at a space, so keep names to one word in a file read this way. For decimals use <code>double</code> with <code>%lf</code>.
    </p>

    <p class="body-text">
        <code>fscanf</code> returns how many values it managed to read. At the end of the file it reads none, so a loop can run "while I got both values":
    </p>

    <pre class="code-block"><code>while (fscanf(f, "%s %d", name, &amp;riders) == 2) {
    // use name and riders
}</code></pre>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        The archive's busiest shelf is the tram records: one line per stop, the stop's name and how many people got on, in neat columns. Rivet wants the Wanderer's console to keep today's numbers the same way.
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "If we save them as a sentence, we'll never get the numbers back out. Columns. Name, space, number. Then reading it is just the same pattern backwards."
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "And the total for the day."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Stop reading when a line doesn't have both a name and a number. Don't guess at half a line."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="fprintf writes name and number columns to riders.txt; fscanf reads each pair back into variables until it cannot read two values">
            <rect x="20" y="25" width="180" height="110" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="110" y="58" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">arrays</text>
            <text x="110" y="76" text-anchor="middle" font-size="13" fill="#14306b" font-family="ui-monospace, monospace">Station 40</text>
            <text x="110" y="94" text-anchor="middle" font-size="13" fill="#14306b" font-family="ui-monospace, monospace">Park 25</text>
            <text x="110" y="112" text-anchor="middle" font-size="13" fill="#14306b" font-family="ui-monospace, monospace">Plant 15</text>
            <rect x="240" y="25" width="160" height="110" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="320" y="58" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">riders.txt</text>
            <text x="320" y="76" text-anchor="middle" font-size="13" fill="#7a5a00" font-family="ui-monospace, monospace">Station 40</text>
            <text x="320" y="94" text-anchor="middle" font-size="13" fill="#7a5a00" font-family="ui-monospace, monospace">Park 25</text>
            <text x="320" y="112" text-anchor="middle" font-size="13" fill="#7a5a00" font-family="ui-monospace, monospace">Plant 15</text>
            <rect x="440" y="25" width="180" height="110" rx="12" fill="#e7f7ee" stroke="#1f9d55" stroke-width="2"/>
            <text x="530" y="67" text-anchor="middle" font-size="14" font-weight="700" fill="#14532d">name, riders</text>
            <text x="530" y="85" text-anchor="middle" font-size="13" fill="#14532d">read back</text>
            <text x="530" y="103" text-anchor="middle" font-size="13" fill="#14532d">total = 80</text>
            <line x1="200" y1="80" x2="226.0" y2="80.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="236,80 224.0,74.0 224.0,86.0" fill="#7c5cff"/>
            <text x="218" y="155" text-anchor="middle" font-size="12" font-weight="700" fill="#4a35b8" font-family="ui-monospace, monospace">fprintf</text>
            <line x1="400" y1="80" x2="426.0" y2="80.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="436,80 424.0,74.0 424.0,86.0" fill="#7c5cff"/>
            <text x="420" y="155" text-anchor="middle" font-size="12" font-weight="700" fill="#4a35b8" font-family="ui-monospace, monospace">fscanf</text>
            <text x="320" y="190" text-anchor="middle" font-size="13" fill="#475569">while (fscanf(...) == 2) stops at the end of the file</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    FILE *f = fopen("pump.txt", "w");
    fprintf(f, "%d %.1lf\n", 3, 2.5);
    fclose(f);

    int pump;
    double flow;
    f = fopen("pump.txt", "r");
    fscanf(f, "%d %lf", &amp;pump, &amp;flow);
    fclose(f);
    printf("Pump %d: %.1f\n", pump, flow);   // Pump 3: 2.5
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Write the three stops from the arrays in the starter code to <code>riders.txt</code> with <code>fprintf</code>, one <code>name number</code> line each. Then read the file back with <code>while (fscanf(...) == 2)</code>, print each line and add up the riders. Exactly:
    </p>

    <pre class="code-block"><code>Station 40
Park 25
Plant 15
Riders: 80</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Neat Columns">
            <input type="hidden" name="filename"     value="tram_records.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    char stops[3][12] = {&quot;Station&quot;, &quot;Park&quot;, &quot;Plant&quot;};
    int counts[3] = {40, 25, 15};
    char name[12];
    int riders;
    int total = 0;

    // 1. open riders.txt with &quot;w&quot;; for each stop: fprintf &quot;&lt;name&gt; &lt;count&gt;\n&quot;; fclose

    // 2. open it with &quot;r&quot;; while fscanf reads a name and a number (== 2):
    //    print &quot;&lt;name&gt; &lt;riders&gt;&quot; and add riders to total; then fclose

    printf(&quot;Riders: %d\n&quot;, total);
    return 0;
}
">
            <input type="hidden" name="instructions" value="Save today's tram records in columns, then read them back.

1. &quot;w&quot;: for i from 0 to 2:
   fprintf(f, &quot;%s %d\n&quot;, stops[i], counts[i]);
2. &quot;r&quot;: while (fscanf(f, &quot;%s %d&quot;, name, &amp;riders) == 2) {
      printf(&quot;%s %d\n&quot;, name, riders); total += riders; }

The output must be exactly:
Station 40
Park 25
Plant 15
Riders: 80">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="fprintf is printf with the file first. In fscanf, riders needs an &amp; because it is an int, but name does not, because a char array is already an address. Compare fscanf's answer with 2, the number of values on each line.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    char stops[3][12] = {&quot;Station&quot;, &quot;Park&quot;, &quot;Plant&quot;};
    int counts[3] = {40, 25, 15};
    char name[12];
    int riders;
    int total = 0;

    FILE *f = fopen(&quot;riders.txt&quot;, &quot;w&quot;);
    if (f == NULL) {
        return 1;
    }
    for (int i = 0; i &lt; 3; i++) {
        fprintf(f, &quot;%s %d\n&quot;, stops[i], counts[i]);
    }
    fclose(f);

    f = fopen(&quot;riders.txt&quot;, &quot;r&quot;);
    if (f == NULL) {
        return 1;
    }
    while (fscanf(f, &quot;%s %d&quot;, name, &amp;riders) == 2) {
        printf(&quot;%s %d\n&quot;, name, riders);
        total += riders;
    }
    fclose(f);

    printf(&quot;Riders: %d\n&quot;, total);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm6', 'lesson' => 'lesson04']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. How is <code>fprintf</code> different from <code>printf</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It can only print numbers</li>
            <li class="quiz-option">B. It reads values from a file</li>
            <li class="quiz-option">C. It has no format codes at all</li>
            <li class="quiz-option quiz-correct">D. It writes into a file you give it ✓</li>
        </ul>
        <p class="quiz-explanation"><em>fprintf uses the same format codes as printf, but sends the text to the file you give it.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In <code>fscanf(f, "%s %d", name, &amp;riders)</code>, why does <code>name</code> have no <code>&amp;</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. An array's name is already an address ✓</li>
            <li class="quiz-option">B. It is a mistake that happens to work</li>
            <li class="quiz-option">C. Strings are never given an address</li>
            <li class="quiz-option">D. The %s code adds the & by itself</li>
        </ul>
        <p class="quiz-explanation"><em>An array name stands for the address of its first element, so it can be passed straight in.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does <code>fscanf</code> return?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The last value it read</li>
            <li class="quiz-option">B. The size of the file</li>
            <li class="quiz-option quiz-correct">C. How many values it read ✓</li>
            <li class="quiz-option">D. The line it stopped on</li>
        </ul>
        <p class="quiz-explanation"><em>It reports how many of the requested values it filled, which drops below 2 at the end of the file.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why can <code>%s</code> not read the name "Gate Road" in one go?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The name is too long for %s</li>
            <li class="quiz-option quiz-correct">B. %s stops at the first space ✓</li>
            <li class="quiz-option">C. %s can't read capital letters</li>
            <li class="quiz-option">D. Names have to be read with %d</li>
        </ul>
        <p class="quiz-explanation"><em>%s reads one word; the space ends it, so keep column values to single words.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Which format reads a decimal into a <code>double</code> with fscanf?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>%d</code></li>
            <li class="quiz-option">B. <code>%f</code></li>
            <li class="quiz-option quiz-correct">C. <code>%lf</code> ✓</li>
            <li class="quiz-option">D. <code>%s</code></li>
        </ul>
        <p class="quiz-explanation"><em>For reading, a double needs %lf; %f is for float.</em></p>
    </div>

</div>
