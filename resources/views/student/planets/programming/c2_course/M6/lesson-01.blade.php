{{-- M6 · Lesson 6.1: Paper That Remembers — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Files — bytes and characters; files and file types; file pointers, fopen and fclose. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="01">

    <h1 class="lesson-heading">Lesson 6.1: Paper That Remembers</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain why files outlast a program, tell text files from binary files, and open and close a file safely with <code>fopen</code>, <code>fclose</code> and a <code>NULL</code> check.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Every variable you have made lives in the computer's main memory, and main memory is wiped when a program ends or the power goes off. A <strong>file</strong> is data kept on storage (a disk, a memory card), and it stays there after the program ends. Files are how programs remember.
    </p>

    <p class="body-text">
        Inside, every file is just a long row of <strong>bytes</strong>. A <code>char</code> is one byte, so a file of text is a row of characters. There are two kinds of file:
    </p>

    <ul class="body-list">
        <li><strong>Text files</strong> hold readable characters, in lines that end with <code>'\n'</code>. You could open one in a notepad.</li>
        <li><strong>Binary files</strong> hold bytes exactly as they sit in memory, like the raw bytes of an <code>int</code> or a whole struct. They are compact and fast, but not readable as text.</li>
    </ul>

    <p class="body-text">
        You work with a file through a <strong>file pointer</strong>, of type <code>FILE *</code> from <code>&lt;stdio.h&gt;</code>. <code>fopen</code> opens the file and gives you one:
    </p>

    <pre class="code-block"><code>FILE *log = fopen("archive.txt", "w");</code></pre>

    <ul class="body-list">
        <li><code>"r"</code>: read an existing file.</li>
        <li><code>"w"</code>: write. Creates the file, or empties it if it already exists.</li>
        <li><code>"a"</code>: append. Adds to the end without erasing what is there.</li>
        <li>Add a <code>b</code> for a binary file: <code>"rb"</code>, <code>"wb"</code>, <code>"ab"</code>.</li>
    </ul>

    <p class="body-text">
        If the file cannot be opened (for example, <code>"r"</code> on a file that does not exist), <code>fopen</code> returns <code>NULL</code>. Check it every time, just like <code>malloc</code>. When you are done, <code>fclose(log);</code> finishes writing and lets the file go.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        The City Archive is quiet and very dry. Shelf after shelf of logbooks, and at the back, sealed cases of tape. Nobody has touched any of it for years, and none of it has forgotten a word.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> opens a logbook from the first year of the city. "The console's memory is like writing on a steamed-up window. Lovely, until the power blinks and it's gone. A file is paper. You switch off, you come back, it still says the same thing."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And if you ask for a logbook that isn't on the shelf?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Then the archive tells you so, with a <code>NULL</code>. Same rule as the tram yard: look at what you were handed before you start reading it."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="Main memory is wiped when the program ends; fopen connects the program to a file on storage, which stays after the program ends">
            <rect x="20" y="30" width="200" height="110" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="120" y="73.5" text-anchor="middle" font-size="15" font-weight="700" fill="#7a1a14">main memory</text>
            <text x="120" y="90.5" text-anchor="middle" font-size="12" fill="#7a1a14">variables</text>
            <text x="120" y="107.5" text-anchor="middle" font-size="12" fill="#7a1a14">wiped at power off</text>
            <rect x="420" y="30" width="200" height="110" rx="12" fill="#e7f7ee" stroke="#1f9d55" stroke-width="2"/>
            <text x="520" y="73.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14532d">storage</text>
            <text x="520" y="90.5" text-anchor="middle" font-size="12" fill="#14532d">archive.txt</text>
            <text x="520" y="107.5" text-anchor="middle" font-size="12" fill="#14532d">stays after power off</text>
            <line x1="220" y1="70" x2="406.0" y2="70.0" stroke="#1f9d55" stroke-width="2.5"/>
            <polygon points="416,70 404.0,64.0 404.0,76.0" fill="#1f9d55"/>
            <text x="318" y="60" text-anchor="middle" font-size="12" font-weight="700" fill="#14532d" font-family="ui-monospace, monospace">fopen(..., "w")</text>
            <line x1="416" y1="105" x2="230.0" y2="105.0" stroke="#1f9d55" stroke-width="2.5" stroke-dasharray="4,3"/>
            <polygon points="220,105 232.0,111.0 232.0,99.0" fill="#1f9d55"/>
            <text x="318" y="125" text-anchor="middle" font-size="12" font-weight="700" fill="#14532d" font-family="ui-monospace, monospace">fopen(..., "r")</text>
            <text x="320" y="185" text-anchor="middle" font-size="13" fill="#475569">FILE * is your handle on an open file; fclose lets it go</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    FILE *book = fopen("first_year.txt", "w");
    if (book == NULL) {
        printf("Cannot open the logbook\n");
        return 1;
    }

    printf("Logbook ready\n");
    fclose(book);
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. Open <code>archive.txt</code> for writing and check it (print <code>Cannot open</code> and return 1 on <code>NULL</code>). Print, close it, print again. Then try to open <code>missing.txt</code> for <em>reading</em>: it does not exist, so report that. Exactly:
    </p>

    <pre class="code-block"><code>Archive open.
Archive closed.
No file called missing.txt</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Paper That Remembers">
            <input type="hidden" name="filename"     value="city_archive.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    // 1. FILE *log = fopen(&quot;archive.txt&quot;, &quot;w&quot;);  if it is NULL: print &quot;Cannot open&quot;, return 1

    // 2. print &quot;Archive open.&quot;, close the file, print &quot;Archive closed.&quot;

    // 3. try fopen(&quot;missing.txt&quot;, &quot;r&quot;); if it is NULL, print &quot;No file called missing.txt&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="Open the archive, close it, and check for a logbook that is not there.

1. FILE *log = fopen(&quot;archive.txt&quot;, &quot;w&quot;);
   if (log == NULL) { print Cannot open, return 1 }
2. print Archive open.  then fclose(log);  then print Archive closed.
3. FILE *missing = fopen(&quot;missing.txt&quot;, &quot;r&quot;);
   if it is NULL, print No file called missing.txt

The output must be exactly:
Archive open.
Archive closed.
No file called missing.txt">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="&quot;w&quot; creates archive.txt, so that open works. &quot;r&quot; needs the file to exist already, and missing.txt does not, so fopen returns NULL: if (missing == NULL) { printf(...); } Only fclose a file that actually opened.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    FILE *log = fopen(&quot;archive.txt&quot;, &quot;w&quot;);
    if (log == NULL) {
        printf(&quot;Cannot open\n&quot;);
        return 1;
    }

    printf(&quot;Archive open.\n&quot;);
    fclose(log);
    printf(&quot;Archive closed.\n&quot;);

    FILE *missing = fopen(&quot;missing.txt&quot;, &quot;r&quot;);
    if (missing == NULL) {
        printf(&quot;No file called missing.txt\n&quot;);
    }
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm6', 'lesson' => 'lesson01']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why do programs use files?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Files are faster than variables</li>
            <li class="quiz-option">B. Variables cannot hold any text</li>
            <li class="quiz-option quiz-correct">C. File data stays after it ends ✓</li>
            <li class="quiz-option">D. C needs one for every program</li>
        </ul>
        <p class="quiz-explanation"><em>Main memory forgets when the program stops; a file on storage keeps its data for next time.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is the difference between a text file and a binary file?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Text is readable; binary is raw bytes ✓</li>
            <li class="quiz-option">B. Text is raw bytes; binary is readable</li>
            <li class="quiz-option">C. Binary files only hold 0s and 1s</li>
            <li class="quiz-option">D. Text files can't hold any numbers</li>
        </ul>
        <p class="quiz-explanation"><em>Text files are readable lines of characters; binary files store values' raw bytes directly.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does <code>fopen("log.txt", "w")</code> do if log.txt already has text in it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Adds new text to the end</li>
            <li class="quiz-option">B. Refuses to open the file</li>
            <li class="quiz-option">C. Opens it to read the text</li>
            <li class="quiz-option quiz-correct">D. Empties it and starts again ✓</li>
        </ul>
        <p class="quiz-explanation"><em>"w" creates a new empty file, wiping one that already exists; use "a" to keep the old contents.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does <code>fopen</code> return if it cannot open the file?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. NULL ✓</li>
            <li class="quiz-option">B. 0</li>
            <li class="quiz-option">C. -1</li>
            <li class="quiz-option">D. EOF</li>
        </ul>
        <p class="quiz-explanation"><em>A NULL FILE pointer means the open failed, so always check before reading or writing.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Which mode opens a binary file for reading?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>"r"</code></li>
            <li class="quiz-option quiz-correct">B. <code>"rb"</code> ✓</li>
            <li class="quiz-option">C. <code>"b"</code></li>
            <li class="quiz-option">D. <code>"wb"</code></li>
        </ul>
        <p class="quiz-explanation"><em>Adding b to a mode marks the file as binary: "rb" reads one, "wb" writes one.</em></p>
    </div>

</div>
