{{-- M6 · Lesson 6.2: One Letter at a Time — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Files — reading and writing bytes (fputc, fgetc, EOF). --}}

<div class="lesson-fragment" data-module="m6" data-lesson="02">

    <h1 class="lesson-heading">Lesson 6.2: One Letter at a Time</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write a file one character at a time with <code>fputc</code>, read it back with <code>fgetc</code>, and stop at <code>EOF</code>.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        The smallest file operations move one byte, one character, at a time:
    </p>

    <ul class="body-list">
        <li><code>fputc(c, f)</code> writes the character <code>c</code> to the file.</li>
        <li><code>fgetc(f)</code> reads the next character and moves along by one.</li>
    </ul>

    <p class="body-text">
        When there is nothing left to read, <code>fgetc</code> returns a special value called <strong><code>EOF</code></strong> ("end of file"). <code>EOF</code> is not a character: it is a number outside the range of chars, usually -1. That is why the variable you read into must be an <code>int</code>, not a <code>char</code>, or you might never see it.
    </p>

    <pre class="code-block"><code>int c;
while ((c = fgetc(f)) != EOF) {
    printf("%c", c);
}</code></pre>

    <p class="body-text">
        Read that loop condition from the inside out: read a character into <code>c</code>, then check it is not <code>EOF</code>. Every pass reads exactly one byte.
    </p>

    <p class="body-text">
        A file remembers where you are in it. After writing, close it and open it again in <code>"r"</code> mode to start reading from the beginning.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        The oldest record in the archive is on a strip of punched tape, one letter per row of holes. Rivet wants the console to save something the same way, as a first test: one word, letter by letter.
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And reading it back, how does it know when the word is finished? The tape doesn't have a little sign on the end saying 'stop'."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "It does, sort of! Ask for one more letter past the end and you don't get a letter. You get a special number that can't be a letter. End of file."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Then the box you read into has to be big enough to hold that number. Not just letters." <strong>Rivet</strong>: "Exactly. An <code>int</code>, not a <code>char</code>."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 180" role="img" aria-label="fputc writes L, A, M, P into the file one byte at a time; fgetc reads them back and returns EOF after the last one">
            <rect x="40" y="50" width="80" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="80" y="85" text-anchor="middle" font-size="18" font-weight="700" fill="#7a5a00">L</text>
            <rect x="140" y="50" width="80" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="180" y="85" text-anchor="middle" font-size="18" font-weight="700" fill="#7a5a00">A</text>
            <rect x="240" y="50" width="80" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="280" y="85" text-anchor="middle" font-size="18" font-weight="700" fill="#7a5a00">M</text>
            <rect x="340" y="50" width="80" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="380" y="85" text-anchor="middle" font-size="18" font-weight="700" fill="#7a5a00">P</text>
            <rect x="440" y="50" width="110" height="56" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="495" y="84" text-anchor="middle" font-size="16" font-weight="700" fill="#7a1a14">EOF</text>
            <text x="80" y="125" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">byte 0</text>
            <text x="180" y="125" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">byte 1</text>
            <text x="280" y="125" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">byte 2</text>
            <text x="380" y="125" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">byte 3</text>
            <text x="495" y="125" text-anchor="middle" font-size="11" fill="#b3261e">no more bytes</text>
            <text x="320" y="30" text-anchor="middle" font-size="13" font-weight="700" fill="#334155">fgetc reads left to right, one byte per call</text>
            <text x="320" y="165" text-anchor="middle" font-size="13" fill="#475569">Bytes read: 4</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    FILE *f = fopen("tape.txt", "w");
    fputc('O', f);
    fputc('K', f);
    fclose(f);

    f = fopen("tape.txt", "r");
    int c;
    while ((c = fgetc(f)) != EOF) {
        printf("%c", c);
    }
    fclose(f);
    printf("\n");            // OK
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Write the word <code>LAMP</code> to <code>tape.txt</code> one character at a time, looping over <code>char word[] = "LAMP";</code> with <code>fputc</code>. Close it, open it for reading, and read it back with <code>fgetc</code> until <code>EOF</code>, printing each character and counting the bytes. Exactly:
    </p>

    <pre class="code-block"><code>Read: LAMP
Bytes: 4</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="One Letter at a Time">
            <input type="hidden" name="filename"     value="punched_tape.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    char word[] = &quot;LAMP&quot;;
    int bytes = 0;

    // 1. open tape.txt with &quot;w&quot;, fputc each letter of word (stop at '\0'), then fclose

    // 2. open tape.txt with &quot;r&quot;, print &quot;Read: &quot;, then read with fgetc until EOF:
    //    print each character with %c and count it

    // 3. fclose, then print a newline and &quot;Bytes: &lt;count&gt;&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="Save one word letter by letter, then read it back.

1. FILE *f = fopen(&quot;tape.txt&quot;, &quot;w&quot;);
   for (int i = 0; word[i] != '\0'; i++) fputc(word[i], f);
   fclose(f);
2. f = fopen(&quot;tape.txt&quot;, &quot;r&quot;); printf(&quot;Read: &quot;);
   int c; while ((c = fgetc(f)) != EOF) { print c with %c; bytes++; }
3. fclose(f); then print &quot;\n&quot; and &quot;Bytes: &lt;count&gt;&quot;

The output must be exactly:
Read: LAMP
Bytes: 4">
            <input type="hidden" name="hint_title"   value="Volt's Hint">
            <input type="hidden" name="hint_body"    value="The writing loop stops at the '\0' that ends the string, so the null itself is never written. Read into an int c, not a char, so EOF fits. Print the newline after the reading loop, then the byte count.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    char word[] = &quot;LAMP&quot;;
    int bytes = 0;

    FILE *f = fopen(&quot;tape.txt&quot;, &quot;w&quot;);
    if (f == NULL) {
        return 1;
    }
    for (int i = 0; word[i] != '\0'; i++) {
        fputc(word[i], f);
    }
    fclose(f);

    f = fopen(&quot;tape.txt&quot;, &quot;r&quot;);
    if (f == NULL) {
        return 1;
    }
    printf(&quot;Read: &quot;);
    int c;
    while ((c = fgetc(f)) != EOF) {
        printf(&quot;%c&quot;, c);
        bytes++;
    }
    fclose(f);

    printf(&quot;\n&quot;);
    printf(&quot;Bytes: %d\n&quot;, bytes);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm6', 'lesson' => 'lesson02']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does <code>fgetc(f)</code> return after the last character?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 0</li>
            <li class="quiz-option">B. '\0'</li>
            <li class="quiz-option">C. NULL</li>
            <li class="quiz-option quiz-correct">D. EOF ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Past the end there are no more bytes, and fgetc signals that with EOF.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why should the variable holding <code>fgetc</code>'s result be an <code>int</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. An int is faster to compare</li>
            <li class="quiz-option">B. fgetc only reads numbers</li>
            <li class="quiz-option quiz-correct">C. A char might not hold EOF ✓</li>
            <li class="quiz-option">D. A char can't hold letters</li>
        </ul>
        <p class="quiz-explanation"><em>EOF (usually -1) must be told apart from every real character, which needs the wider int.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does <code>fputc('A', f)</code> do?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Writes an A to the file ✓</li>
            <li class="quiz-option">B. Reads an A from the file</li>
            <li class="quiz-option">C. Finds the first A in f</li>
            <li class="quiz-option">D. Writes A, then closes f</li>
        </ul>
        <p class="quiz-explanation"><em>fputc writes a single character, one byte, to the file.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. In <code>while ((c = fgetc(f)) != EOF)</code>, what happens first on each pass?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. c is checked against EOF</li>
            <li class="quiz-option quiz-correct">B. A character is read into c ✓</li>
            <li class="quiz-option">C. c is printed on the screen</li>
            <li class="quiz-option">D. The file is closed and reopened</li>
        </ul>
        <p class="quiz-explanation"><em>The inner brackets run first: read into c, then compare c with EOF.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. How many bytes does the word LAMP take in the file in this lesson?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 3</li>
            <li class="quiz-option">B. 5</li>
            <li class="quiz-option quiz-correct">C. 4 ✓</li>
            <li class="quiz-option">D. 8</li>
        </ul>
        <p class="quiz-explanation"><em>One byte per character, and the loop stops before the '\0', so 4 bytes.</em></p>
    </div>

</div>
