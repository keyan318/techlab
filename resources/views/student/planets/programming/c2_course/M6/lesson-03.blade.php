{{-- M6 · Lesson 6.3: Lines in the Logbook — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Files — string input and output (fputs, fgets); append mode. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="03">

    <h1 class="lesson-heading">Lesson 6.3: Lines in the Logbook</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write whole lines with <code>fputs</code>, add to a file with append mode, and read it back line by line with <code>fgets</code>.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Moving one character at a time is slow work. The string functions move a whole line at once:
    </p>

    <ul class="body-list">
        <li><code>fputs(text, f)</code> writes a string. It does <em>not</em> add a newline, so put <code>\n</code> at the end yourself.</li>
        <li><code>fgets(buffer, size, f)</code> reads one line into a char array: up to and including the <code>'\n'</code>, or at most <code>size - 1</code> characters, then adds the <code>'\0'</code>.</li>
    </ul>

    <p class="body-text">
        At the end of the file, <code>fgets</code> returns <code>NULL</code>, which makes a neat loop:
    </p>

    <pre class="code-block"><code>char line[50];
while (fgets(line, 50, f) != NULL) {
    printf("%s", line);     // line already ends in \n
}</code></pre>

    <p class="body-text">
        The <code>size</code> argument is a safety rail: <code>fgets</code> never writes more than the array can hold, which is exactly the check that <code>strcat</code> leaves out.
    </p>

    <p class="body-text">
        To add lines to a file that already exists, open it with <code>"a"</code> (append). Everything new goes on the end; nothing already there is erased. With <code>"w"</code>, the old lines would be wiped.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Astro has decided the Wanderer will keep a logbook, like the city did. Every repair gets one line.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Two lines tonight: Gate Road lit, tram running. Tomorrow we add the bells. And I don't want tomorrow's line to wipe out tonight's."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Then tomorrow we open it to <em>add</em>, not to write. Writing starts a fresh page. Adding goes on the end."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And when we read it back, a line too long for the box gets cut, not spilled into whatever is next to it." <strong>Rivet</strong>: "That's what the size is for."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="Two lines are written with mode w, then a third is added with mode a; fgets reads the three lines back one at a time">
            <rect x="20" y="25" width="260" height="110" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="150" y="58" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">logbook.txt</text>
            <text x="150" y="76" text-anchor="middle" font-size="13" fill="#7a5a00" font-family="ui-monospace, monospace">Gate Road lit</text>
            <text x="150" y="94" text-anchor="middle" font-size="13" fill="#7a5a00" font-family="ui-monospace, monospace">Tram running</text>
            <text x="150" y="112" text-anchor="middle" font-size="13" fill="#7a5a00" font-family="ui-monospace, monospace">Bells rung</text>
            <text x="300" y="66" text-anchor="start" font-size="12" font-weight="700" fill="#7a5a00">"w": lines 1 and 2</text>
            <text x="300" y="106" text-anchor="start" font-size="12" font-weight="700" fill="#7a5a00">"a": line 3 goes on the end</text>
            <rect x="470" y="140" width="150" height="50" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="545" y="161.5" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">fgets loop</text>
            <text x="545" y="177.5" text-anchor="middle" font-size="11" fill="#14306b">one line per call</text>
            <line x1="280" y1="125" x2="456.2" y2="158.2" stroke="#2f6fe0" stroke-width="2.5"/>
            <polygon points="466,160 455.3,151.9 453.1,163.7" fill="#2f6fe0"/>
            <text x="150" y="170" text-anchor="middle" font-size="12" fill="#475569">fputs adds no \n: you write it</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    FILE *f = fopen("notes.txt", "w");
    fputs("Fountain fixed\n", f);
    fclose(f);

    char line[40];
    f = fopen("notes.txt", "r");
    while (fgets(line, 40, f) != NULL) {
        printf("&gt; %s", line);       // &gt; Fountain fixed
    }
    fclose(f);
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. With <code>"w"</code>, write <code>Gate Road lit</code> and <code>Tram running</code> to <code>logbook.txt</code> (one line each). Close it, reopen with <code>"a"</code> and add <code>Bells rung</code>. Then read the file with <code>fgets</code> and print every line numbered. Exactly:
    </p>

    <pre class="code-block"><code>1: Gate Road lit
2: Tram running
3: Bells rung</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Lines in the Logbook">
            <input type="hidden" name="filename"     value="logbook.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    char line[50];
    int number = 1;

    // 1. open logbook.txt with &quot;w&quot;, fputs the first two lines (end each with \n), fclose

    // 2. open it again with &quot;a&quot;, fputs &quot;Bells rung\n&quot;, fclose

    // 3. open it with &quot;r&quot;; while fgets reads a line: print &quot;&lt;number&gt;: &lt;line&gt;&quot;, number++

    return 0;
}
">
            <input type="hidden" name="instructions" value="Start the Wanderer's logbook.

1. &quot;w&quot;: fputs(&quot;Gate Road lit\n&quot;, f); fputs(&quot;Tram running\n&quot;, f); fclose(f);
2. &quot;a&quot;: fputs(&quot;Bells rung\n&quot;, f); fclose(f);
3. &quot;r&quot;: while (fgets(line, 50, f) != NULL) {
      printf(&quot;%d: %s&quot;, number, line); number++; }

The output must be exactly:
1: Gate Road lit
2: Tram running
3: Bells rung">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="fputs writes only what you give it, so each line needs its own \n. In step 2 the mode must be &quot;a&quot;; with &quot;w&quot; the first two lines would be wiped. fgets keeps the \n it read, so print with &quot;%d: %s&quot; and no extra newline.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    char line[50];
    int number = 1;

    FILE *f = fopen(&quot;logbook.txt&quot;, &quot;w&quot;);
    if (f == NULL) {
        return 1;
    }
    fputs(&quot;Gate Road lit\n&quot;, f);
    fputs(&quot;Tram running\n&quot;, f);
    fclose(f);

    f = fopen(&quot;logbook.txt&quot;, &quot;a&quot;);
    if (f == NULL) {
        return 1;
    }
    fputs(&quot;Bells rung\n&quot;, f);
    fclose(f);

    f = fopen(&quot;logbook.txt&quot;, &quot;r&quot;);
    if (f == NULL) {
        return 1;
    }
    while (fgets(line, 50, f) != NULL) {
        printf(&quot;%d: %s&quot;, number, line);
        number++;
    }
    fclose(f);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm6', 'lesson' => 'lesson03']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does <code>fputs("Done", f)</code> write?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Done, with no newline after it ✓</li>
            <li class="quiz-option">B. Done, with a newline after it</li>
            <li class="quiz-option">C. Only the first letter, D</li>
            <li class="quiz-option">D. Done, then a '\0' byte after it</li>
        </ul>
        <p class="quiz-explanation"><em>fputs writes the string as it is; any newline has to be part of the string.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does <code>fgets</code> return when there are no more lines?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. EOF</li>
            <li class="quiz-option">B. ""</li>
            <li class="quiz-option">C. 0</li>
            <li class="quiz-option quiz-correct">D. NULL ✓</li>
        </ul>
        <p class="quiz-explanation"><em>fgets signals the end of the file by returning NULL, which ends a while loop neatly.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why does <code>fgets</code> take a size argument?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. To count how many lines it read</li>
            <li class="quiz-option quiz-correct">B. So it can't overflow the array ✓</li>
            <li class="quiz-option">C. To skip that many characters</li>
            <li class="quiz-option">D. To set how many lines to read</li>
        </ul>
        <p class="quiz-explanation"><em>fgets stops at size - 1 characters, so a long line cannot overflow the buffer.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Which mode adds to the end of an existing file without erasing it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>"w"</code></li>
            <li class="quiz-option">B. <code>"r"</code></li>
            <li class="quiz-option quiz-correct">C. <code>"a"</code> ✓</li>
            <li class="quiz-option">D. <code>"rb"</code></li>
        </ul>
        <p class="quiz-explanation"><em>"a" (append) writes after the existing contents; "w" would wipe them first.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. A line read by <code>fgets</code> is printed with <code>printf("%s\n", line);</code>. What goes wrong?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing; it prints normally</li>
            <li class="quiz-option">B. Each line is cut in half</li>
            <li class="quiz-option quiz-correct">C. An extra blank line appears ✓</li>
            <li class="quiz-option">D. All lines join into one</li>
        </ul>
        <p class="quiz-explanation"><em>fgets keeps the newline it read, so adding another one doubles the spacing.</em></p>
    </div>

</div>
