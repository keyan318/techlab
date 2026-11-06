{{-- M4 · Lesson 4.2: Filling In the Card — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Structures — storing and retrieving values in a structure variable. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="02">

    <h1 class="lesson-heading">Lesson 4.2: Filling In the Card</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can update and read back the members of a structure, and explain that assigning one structure to another copies every member.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Each member of a struct variable is an ordinary variable of its own type. You can read it, print it, do sums with it and change it, any time:
    </p>

    <pre class="code-block"><code>tram.power = tram.power + 30;
if (tram.power &gt; 50) {
    tram.working = 1;
}</code></pre>

    <p class="body-text">
        You can also copy a whole structure in one go with <code>=</code>. Every member is copied across, even the <code>char</code> array:
    </p>

    <pre class="code-block"><code>struct Machine backup = tram;   // backup is a full copy of tram</code></pre>

    <p class="body-text">
        After that, the two are separate cards. Changing <code>tram</code> does not change <code>backup</code>, and changing <code>backup</code> does not change <code>tram</code>.
    </p>

    <p class="body-text">
        That makes a quick "before" snapshot easy. Two things <code>=</code> cannot do with structures: you cannot compare two structs with <code>==</code> (compare their members instead), and you cannot print a whole struct with one <code>%</code>; print each member.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        The second card in the registry is for <strong>Tram 7</strong>, the tram that goes round and round its loop with nobody aboard. The card says power 0, broken.
    </p>

    <p class="body-text">
        Rivet is about to rewrite the card after the repair. <strong>Volt</strong> stops his hand. "Copy it first. If the repair goes wrong, I want to know exactly what it said before we touched it."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "A copy of the whole card, one line. Then we fix the real one."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And the copy stays exactly as it was. Doesn't matter what you do to the real one."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="backup = tram copies every member; then repairing tram changes tram only, while backup keeps power 0 and broken">
            <rect x="20" y="30" width="240" height="100" rx="12" fill="#e7f7ee" stroke="#1f9d55" stroke-width="2"/>
            <text x="140" y="59.5" text-anchor="middle" font-size="14" font-weight="700" fill="#14532d">tram (after repair)</text>
            <text x="140" y="76.5" text-anchor="middle" font-size="12" fill="#14532d" font-family="ui-monospace, monospace">name: "Tram 7"</text>
            <text x="140" y="93.5" text-anchor="middle" font-size="12" fill="#14532d" font-family="ui-monospace, monospace">power: 90</text>
            <text x="140" y="110.5" text-anchor="middle" font-size="12" fill="#14532d" font-family="ui-monospace, monospace">working: 1</text>
            <rect x="380" y="30" width="240" height="100" rx="12" fill="#f1f5f9" stroke="#94a3b8" stroke-width="2"/>
            <text x="500" y="59.5" text-anchor="middle" font-size="14" font-weight="700" fill="#334155">backup (copy)</text>
            <text x="500" y="76.5" text-anchor="middle" font-size="12" fill="#334155" font-family="ui-monospace, monospace">name: "Tram 7"</text>
            <text x="500" y="93.5" text-anchor="middle" font-size="12" fill="#334155" font-family="ui-monospace, monospace">power: 0</text>
            <text x="500" y="110.5" text-anchor="middle" font-size="12" fill="#334155" font-family="ui-monospace, monospace">working: 0</text>
            <line x1="262" y1="80" x2="366.0" y2="80.0" stroke="#7c5cff" stroke-width="2.5" stroke-dasharray="4,3"/>
            <polygon points="376,80 364.0,74.0 364.0,86.0" fill="#7c5cff"/>
            <text x="320" y="70" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8" font-family="ui-monospace, monospace">backup = tram;</text>
            <text x="320" y="175" text-anchor="middle" font-size="13" fill="#475569">Copied before the repair, so the copy keeps the old values.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

struct Gauge {
    int reading;
    int limit;
};

int main() {
    struct Gauge pump;
    pump.reading = 70;
    pump.limit = 100;

    struct Gauge before = pump;
    pump.reading = pump.reading + 20;

    printf("Now %d, was %d\n", pump.reading, before.reading);   // Now 90, was 70
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Fill in a <code>struct Machine tram</code> as <code>Tram 7</code>, power 0, working 0. Copy it into <code>backup</code>. Then repair <code>tram</code>: power 90, working 1. Print both cards, with <code>working</code> or <code>broken</code>, exactly:
    </p>

    <pre class="code-block"><code>Tram 7: 90, working
Backup: 0, broken</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Filling In the Card">
            <input type="hidden" name="filename"     value="tram_card.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

struct Machine {
    char name[20];
    int power;
    int working;     // 1 working, 0 broken
};

int main() {
    struct Machine tram;
    strcpy(tram.name, &quot;Tram 7&quot;);
    tram.power = 0;
    tram.working = 0;

    // 1. copy the whole card into a new struct Machine called backup

    // 2. repair tram: power 90, working 1

    // 3. print &quot;&lt;name&gt;: &lt;power&gt;, working/broken&quot; for tram, then &quot;Backup: &lt;power&gt;, working/broken&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="Snapshot the Tram 7 card before the repair.

1. struct Machine backup = tram;
2. Repair tram: power = 90, working = 1
3. Print tram, then backup. Show &quot;working&quot; for 1 and &quot;broken&quot; for 0.

The output must be exactly:
Tram 7: 90, working
Backup: 0, broken">
            <input type="hidden" name="hint_title"   value="Volt's Hint">
            <input type="hidden" name="hint_body"    value="The copy has to happen before the repair, or backup copies the fixed card. Printing &quot;working&quot; or &quot;broken&quot; is an if/else on each card's working member. tram.name already holds &quot;Tram 7&quot;, so print it with %s.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

struct Machine {
    char name[20];
    int power;
    int working;     // 1 working, 0 broken
};

int main() {
    struct Machine tram;
    strcpy(tram.name, &quot;Tram 7&quot;);
    tram.power = 0;
    tram.working = 0;

    struct Machine backup = tram;

    tram.power = 90;
    tram.working = 1;

    if (tram.working == 1) {
        printf(&quot;%s: %d, working\n&quot;, tram.name, tram.power);
    } else {
        printf(&quot;%s: %d, broken\n&quot;, tram.name, tram.power);
    }
    if (backup.working == 1) {
        printf(&quot;Backup: %d, working\n&quot;, backup.power);
    } else {
        printf(&quot;Backup: %d, broken\n&quot;, backup.power);
    }
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm4', 'lesson' => 'lesson02']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does <code>struct Machine backup = tram;</code> do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Makes backup point at tram's card</li>
            <li class="quiz-option">B. Copies only tram's name into backup</li>
            <li class="quiz-option quiz-correct">C. Copies every member of tram to backup ✓</li>
            <li class="quiz-option">D. Moves tram into backup, emptying tram</li>
        </ul>
        <p class="quiz-explanation"><em>Assigning one struct to another copies all of its members, including char arrays.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. After the copy, <code>tram.power</code> is set to 90. What is <code>backup.power</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Still what it was at the copy ✓</li>
            <li class="quiz-option">B. 90, the same as tram's power</li>
            <li class="quiz-option">C. Always reset back to 0</li>
            <li class="quiz-option">D. An error, until it is set</li>
        </ul>
        <p class="quiz-explanation"><em>backup is a separate card; changing tram afterwards does not touch it.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. How do you check whether two Machine structs <code>a</code> and <code>b</code> have the same power?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>a.power = b.power</code></li>
            <li class="quiz-option">B. <code>a == b</code></li>
            <li class="quiz-option">C. <code>strcmp(a.power, b.power)</code></li>
            <li class="quiz-option quiz-correct">D. <code>a.power == b.power</code> ✓</li>
        </ul>
        <p class="quiz-explanation"><em>C cannot compare whole structs with ==, so compare the members you care about.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Which line correctly adds 10 to a machine's power?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>tram = tram.power + 10;</code></li>
            <li class="quiz-option quiz-correct">B. <code>tram.power = tram.power + 10;</code> ✓</li>
            <li class="quiz-option">C. <code>power.tram = power.tram + 10;</code></li>
            <li class="quiz-option">D. <code>tram[power] = tram[power] + 10;</code></li>
        </ul>
        <p class="quiz-explanation"><em>A member is an ordinary variable, so you can do sums with it through the dot.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. How do you print a whole struct with printf?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Use %s, which prints every member</li>
            <li class="quiz-option">B. Use %struct, made for whole structs</li>
            <li class="quiz-option quiz-correct">C. Print each member on its own ✓</li>
            <li class="quiz-option">D. Pass the struct with no format</li>
        </ul>
        <p class="quiz-explanation"><em>printf has no format for a whole struct, so each member is printed with its own %d or %s.</em></p>
    </div>

</div>
