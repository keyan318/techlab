{{-- M6 · Lesson 6.5: The Console Remembers — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Files — input and output of binary data (fwrite, fread). --}}

<div class="lesson-fragment" data-module="m6" data-lesson="05">

    <h1 class="lesson-heading">Lesson 6.5: The Console Remembers</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can save an array of structures to a binary file with <code>fwrite</code>, load it back with <code>fread</code>, and check how many items were really read.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Text files are great for people. For a program's own data, it is often simpler to save the bytes exactly as they sit in memory. That is a <strong>binary file</strong>, opened with <code>"wb"</code> or <code>"rb"</code>.
    </p>

    <pre class="code-block"><code>fwrite(crew, sizeof(Crew), 4, f);     // where from, size of one, how many, which file</code></pre>

    <p class="body-text">
        <code>fwrite</code> copies 4 items of <code>sizeof(Crew)</code> bytes each, starting at the address <code>crew</code>, into the file: every name and every rank, in one call. <code>fread</code> takes the same four arguments and copies the bytes back into memory:
    </p>

    <pre class="code-block"><code>Crew remembered[4];
int got = fread(remembered, sizeof(Crew), 4, f);</code></pre>

    <p class="body-text">
        Both return how many <em>items</em> they managed, so a check like <code>got == 4</code> tells you the whole file came back. A text file would need a <code>%s</code> and a <code>%d</code> for every member; <code>fwrite</code> just copies the memory.
    </p>

    <p class="body-text">
        The trade-off: a binary file is not readable in a notepad, and it is only safe to read back with the same struct it was written with.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Rivet slides the Dead Console's memory board back into place with an archive routine on it. On shutdown, the console will write down who its crew are. On start-up, it will read it back.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Four names. Mine, Rivet's, Volt's. And the one who wrote every program on this ship since the crash."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And it checks it got all four back. Not three. Not 'most of the crew'."
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Then save it, cut the power, and bring it back. This is the last one. You're writing it."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="fwrite copies the crew array from memory into crew.dat; after the power cut, fread copies it back into a new array; 4 items come back">
            <rect x="20" y="30" width="180" height="120" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="110" y="61" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">crew[4]</text>
            <text x="110" y="78" text-anchor="middle" font-size="12" fill="#14306b" font-family="ui-monospace, monospace">ASTRO 1</text>
            <text x="110" y="95" text-anchor="middle" font-size="12" fill="#14306b" font-family="ui-monospace, monospace">RIVET 2</text>
            <text x="110" y="112" text-anchor="middle" font-size="12" fill="#14306b" font-family="ui-monospace, monospace">VOLT 3</text>
            <text x="110" y="129" text-anchor="middle" font-size="12" fill="#14306b" font-family="ui-monospace, monospace">CADET 4</text>
            <rect x="240" y="55" width="160" height="70" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="320" y="86.5" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">crew.dat</text>
            <text x="320" y="103.5" text-anchor="middle" font-size="12" fill="#7a5a00">raw bytes</text>
            <rect x="440" y="30" width="180" height="120" rx="12" fill="#e7f7ee" stroke="#1f9d55" stroke-width="2"/>
            <text x="530" y="61" text-anchor="middle" font-size="14" font-weight="700" fill="#14532d">remembered[4]</text>
            <text x="530" y="78" text-anchor="middle" font-size="12" fill="#14532d" font-family="ui-monospace, monospace">ASTRO 1</text>
            <text x="530" y="95" text-anchor="middle" font-size="12" fill="#14532d" font-family="ui-monospace, monospace">RIVET 2</text>
            <text x="530" y="112" text-anchor="middle" font-size="12" fill="#14532d" font-family="ui-monospace, monospace">VOLT 3</text>
            <text x="530" y="129" text-anchor="middle" font-size="12" fill="#14532d" font-family="ui-monospace, monospace">CADET 4</text>
            <line x1="200" y1="90" x2="226.0" y2="90.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="236,90 224.0,84.0 224.0,96.0" fill="#7c5cff"/>
            <text x="218" y="172" text-anchor="middle" font-size="12" font-weight="700" fill="#4a35b8" font-family="ui-monospace, monospace">fwrite</text>
            <line x1="400" y1="90" x2="426.0" y2="90.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="436,90 424.0,84.0 424.0,96.0" fill="#7c5cff"/>
            <text x="420" y="172" text-anchor="middle" font-size="12" font-weight="700" fill="#4a35b8" font-family="ui-monospace, monospace">fread</text>
            <text x="320" y="200" text-anchor="middle" font-size="13" fill="#475569">power cut in between: memory is wiped, the file is not</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int panels[3] = {12, 45, 20};
    FILE *f = fopen("hull.dat", "wb");
    fwrite(panels, sizeof(int), 3, f);
    fclose(f);

    int back[3];
    f = fopen("hull.dat", "rb");
    int got = fread(back, sizeof(int), 3, f);
    fclose(f);
    printf("%d values: %d %d %d\n", got, back[0], back[1], back[2]);   // 3 values: 12 45 20
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        The crew is set up for you. Save all four with <code>fwrite</code> to <code>crew.dat</code> (mode <code>"wb"</code>) and close it: that is the power cut. Then open it with <code>"rb"</code>, <code>fread</code> into a new <code>Crew remembered[4]</code>, and if 4 came back, greet each one. Exactly:
    </p>

    <pre class="code-block"><code>HELLO, ASTRO.
HELLO, RIVET.
HELLO, VOLT.
HELLO, CADET.</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="The Console Remembers">
            <input type="hidden" name="filename"     value="console_remembers.c">
            <input type="hidden" name="difficulty"   value="Hard">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

typedef struct {
    char name[12];
    int rank;
} Crew;

int main() {
    Crew crew[4];
    strcpy(crew[0].name, &quot;ASTRO&quot;);
    crew[0].rank = 1;
    strcpy(crew[1].name, &quot;RIVET&quot;);
    crew[1].rank = 2;
    strcpy(crew[2].name, &quot;VOLT&quot;);
    crew[2].rank = 3;
    strcpy(crew[3].name, &quot;CADET&quot;);
    crew[3].rank = 4;

    // 1. open crew.dat with &quot;wb&quot;, fwrite all 4 Crew records, fclose (the power cut)

    // 2. make Crew remembered[4]; open crew.dat with &quot;rb&quot;; fread 4 records; fclose

    // 3. if fread returned 4: print &quot;HELLO, &lt;name&gt;.&quot; for each remembered crew member

    return 0;
}
">
            <input type="hidden" name="instructions" value="Teach the Dead Console to remember its crew.

1. FILE *f = fopen(&quot;crew.dat&quot;, &quot;wb&quot;);
   fwrite(crew, sizeof(Crew), 4, f); fclose(f);
2. Crew remembered[4]; f = fopen(&quot;crew.dat&quot;, &quot;rb&quot;);
   int got = fread(remembered, sizeof(Crew), 4, f); fclose(f);
3. if (got == 4), loop over remembered and print:
   HELLO, &lt;name&gt;.

The output must be exactly:
HELLO, ASTRO.
HELLO, RIVET.
HELLO, VOLT.
HELLO, CADET.">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="fwrite and fread take the same four things: the array, sizeof(Crew), 4, and the file. Read into remembered, not crew, so you know the names really came back from the file. Then printf(&quot;HELLO, %s.\n&quot;, remembered[i].name); in a loop.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

typedef struct {
    char name[12];
    int rank;
} Crew;

int main() {
    Crew crew[4];
    strcpy(crew[0].name, &quot;ASTRO&quot;);
    crew[0].rank = 1;
    strcpy(crew[1].name, &quot;RIVET&quot;);
    crew[1].rank = 2;
    strcpy(crew[2].name, &quot;VOLT&quot;);
    crew[2].rank = 3;
    strcpy(crew[3].name, &quot;CADET&quot;);
    crew[3].rank = 4;

    FILE *f = fopen(&quot;crew.dat&quot;, &quot;wb&quot;);
    if (f == NULL) {
        return 1;
    }
    fwrite(crew, sizeof(Crew), 4, f);
    fclose(f);

    Crew remembered[4];
    f = fopen(&quot;crew.dat&quot;, &quot;rb&quot;);
    if (f == NULL) {
        return 1;
    }
    int got = fread(remembered, sizeof(Crew), 4, f);
    fclose(f);

    if (got == 4) {
        for (int i = 0; i &lt; 4; i++) {
            printf(&quot;HELLO, %s.\n&quot;, remembered[i].name);
        }
    }
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm6', 'lesson' => 'lesson05']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <p class="body-text" style="margin-top:2.5rem;">
        Rivet cuts the power. The Bridge goes dark, and quiet, and for a second nobody breathes. Then he switches the Dead Console back on, and for the first time since the crash it does not ask who they are.
    </p>

    <pre class="code-block"><code>HELLO, ASTRO.
HELLO, RIVET.
HELLO, VOLT.
HELLO, CADET.</code></pre>

    <p class="body-text">
        <strong>Rivet</strong> laughs so hard he has to sit down on the floor. <strong>Volt</strong> reads it twice, then pats the console once, like a dog that did well.
    </p>

    <p class="body-text">
        The Wanderer lifts off at dusk. From the air, Programming City is lit from end to end: Gate Road, Lamp Street, the two blocks in their neat rows, the tram crawling along its line and stopping at the end, three bell towers, the fountain going. Down by the gate, the lamp that blinked for years is just on, like nothing ever happened to it.
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "So. Where next?"
    </p>

    <p class="body-text">
        <strong>Astro</strong>, at the cracked window of the Bridge, watching for the next dark patch on the map: "Wherever the lights are out."
    </p>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does <code>fwrite(crew, sizeof(Crew), 4, f)</code> write?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The word "crew" as text</li>
            <li class="quiz-option">B. Only the 4 crew names</li>
            <li class="quiz-option quiz-correct">C. 4 whole Crew records ✓</li>
            <li class="quiz-option">D. Just 4 bytes of data</li>
        </ul>
        <p class="quiz-explanation"><em>It copies 4 items, each sizeof(Crew) bytes long, starting at the address crew.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which mode opens a binary file for writing?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>"w"</code></li>
            <li class="quiz-option">B. <code>"b"</code></li>
            <li class="quiz-option">C. <code>"rb"</code></li>
            <li class="quiz-option quiz-correct">D. <code>"wb"</code> ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The b marks the file as binary, and w means write.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does <code>fread</code> return?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. How many items it read ✓</li>
            <li class="quiz-option">B. The first item it read</li>
            <li class="quiz-option">C. NULL on every success</li>
            <li class="quiz-option">D. The name of the file</li>
        </ul>
        <p class="quiz-explanation"><em>Like fwrite, it reports a count of whole items, so got == 4 means every record came back.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why can a binary file save a whole array of structs in one call?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It squeezes them into text first</li>
            <li class="quiz-option quiz-correct">B. It copies the raw memory as is ✓</li>
            <li class="quiz-option">C. Structs are stored as plain text</li>
            <li class="quiz-option">D. It only saves the first struct</li>
        </ul>
        <p class="quiz-explanation"><em>fwrite copies bytes exactly as they sit in memory, members and all, with no formatting.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is one downside of a binary file compared with a text file?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is slower to write</li>
            <li class="quiz-option">B. It cannot hold numbers</li>
            <li class="quiz-option quiz-correct">C. People can't read it as text ✓</li>
            <li class="quiz-option">D. It is wiped at power off</li>
        </ul>
        <p class="quiz-explanation"><em>Raw bytes are not human-readable, and only make sense with the exact layout they were written from.</em></p>
    </div>

</div>
