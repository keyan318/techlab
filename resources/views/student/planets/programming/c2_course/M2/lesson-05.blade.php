{{-- M2 · Lesson 2.5: The Blank Tag — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Pointers — pointer variables and operators (NULL and safe use). --}}

<div class="lesson-fragment" data-module="m2" data-lesson="05">

    <h1 class="lesson-heading">Lesson 2.5: The Blank Tag</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can use <code>NULL</code> to mean "points at nothing", check a pointer before you dereference it, and explain why skipping that check crashes programs.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Sometimes a pointer has nothing to point at yet. C has a special value for that: <strong><code>NULL</code></strong>, an address that means "nowhere". It comes from <code>&lt;stdio.h&gt;</code> and <code>&lt;stdlib.h&gt;</code>.
    </p>

    <pre class="code-block"><code>int *reading = NULL;     // a tag with no address on it yet</code></pre>

    <p class="body-text">
        Dereferencing <code>NULL</code> (writing <code>*reading</code> when <code>reading</code> is <code>NULL</code>) is an error: there is no box there to read. On most computers the program crashes on the spot.
    </p>

    <p class="body-text">
        So whenever a pointer <em>might</em> be <code>NULL</code>, check it first:
    </p>

    <pre class="code-block"><code>if (reading == NULL) {
    printf("No signal\n");
} else {
    printf("Reading: %d\n", *reading);
}</code></pre>

    <p class="body-text">
        Good habits: set a pointer to <code>NULL</code> when you declare it if you have nothing for it yet, and check any pointer you were handed before you use <code>*</code> on it. This check becomes very important in Module 5.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        Half the machines on the depot map have a number next to them. The other half have a blank tag. Nothing written on it at all.
    </p>

    <p class="body-text">
        <strong>Volt</strong> holds one up. "This is the one that worries me. The job reads the tag, goes to the address, and there is no address. What does it do then?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Then it reads from nowhere and the whole console falls over."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Then the job checks the tag first. Blank tag, it says so and moves on. It doesn't go walking off a cliff because nobody wrote an address down."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="report checks its pointer: a real address prints the reading, a NULL pointer prints No signal">
            <rect x="20" y="60" width="200" height="70" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="120" y="100" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">report(int *reading)</text>
            <rect x="330" y="20" width="290" height="60" rx="12" fill="#e7f7ee" stroke="#1f9d55" stroke-width="2"/>
            <text x="475" y="46" text-anchor="middle" font-size="14" font-weight="700" fill="#14532d">reading has an address</text>
            <text x="475" y="64" text-anchor="middle" font-size="13" fill="#14532d">Reading: 42</text>
            <rect x="330" y="110" width="290" height="60" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="475" y="136" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1a14">reading == NULL</text>
            <text x="475" y="154" text-anchor="middle" font-size="13" fill="#7a1a14">No signal</text>
            <line x1="220" y1="85" x2="316.5" y2="53.1" stroke="#1f9d55" stroke-width="2.5"/>
            <polygon points="326,50 312.7,48.1 316.5,59.5" fill="#1f9d55"/>
            <line x1="220" y1="105" x2="316.5" y2="136.9" stroke="#b3261e" stroke-width="2.5"/>
            <polygon points="326,140 316.5,130.5 312.7,141.9" fill="#b3261e"/>
            <text x="320" y="195" text-anchor="middle" font-size="13" fill="#475569">Check before you dereference.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int *spare = NULL;

    if (spare == NULL) {
        printf("Nothing to read yet\n");
    }
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. Write <code>void report(int *reading)</code>: if <code>reading</code> is <code>NULL</code>, print <code>No signal</code>; otherwise print <code>Reading: </code> and the value. In <code>main</code>, call <code>report(&amp;meter)</code> with <code>int meter = 42;</code>, then <code>report(NULL)</code>. Exactly:
    </p>

    <pre class="code-block"><code>Reading: 42
No signal</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="The Blank Tag">
            <input type="hidden" name="filename"     value="blank_tag.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

void report(int *reading) {
    // if reading is NULL, print &quot;No signal&quot;
    // otherwise print &quot;Reading: &quot; and the value it points at
}

int main() {
    int meter = 42;

    // call report with the address of meter, then call report(NULL)

    return 0;
}
">
            <input type="hidden" name="instructions" value="Some machines on the map have a blank tag. The job must not walk off a cliff.

In report(int *reading):
- if reading == NULL, print: No signal
- otherwise print: Reading: &lt;the value&gt;

In main, call report(&amp;meter); then report(NULL);
The output must be exactly:
Reading: 42
No signal">
            <input type="hidden" name="hint_title"   value="Volt's Hint">
            <input type="hidden" name="hint_body"    value="Check first, read second. if (reading == NULL) { printf(&quot;No signal\n&quot;); } else { printf(&quot;Reading: %d\n&quot;, *reading); } The * only happens in the else, where you know there is a real address.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

void report(int *reading) {
    if (reading == NULL) {
        printf(&quot;No signal\n&quot;);
    } else {
        printf(&quot;Reading: %d\n&quot;, *reading);
    }
}

int main() {
    int meter = 42;

    report(&amp;meter);
    report(NULL);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm2', 'lesson' => 'lesson05']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <p class="body-text" style="margin-top:2.5rem;">
        Rivet runs it on the real thing. One by one, the depot reaches every machine on the map that has an address, and leaves the blank tags alone. At the big crossing, the light that was red for three years goes amber, then green, then red, then green again, all by itself.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "That's every machine we can reach from here. Now the streets."
    </p>

    <p class="body-text">
        The next street on the map is a long straight one. Rivet reads the label. "Lamp Street. Six lamps in a row. Same kind of lamp, one after the other, all the way down."
    </p>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does <code>NULL</code> mean for a pointer?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It points at the number 0</li>
            <li class="quiz-option">B. It points at main()</li>
            <li class="quiz-option quiz-correct">C. It points at nothing ✓</li>
            <li class="quiz-option">D. It points at itself</li>
        </ul>
        <p class="quiz-explanation"><em>NULL is the special "nowhere" address, used when a pointer has nothing to point at.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What happens if you dereference a NULL pointer?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. You safely get the value 0</li>
            <li class="quiz-option quiz-correct">B. The program usually crashes ✓</li>
            <li class="quiz-option">C. C moves it somewhere safe</li>
            <li class="quiz-option">D. Nothing happens at all</li>
        </ul>
        <p class="quiz-explanation"><em>There is no box at NULL, so reading or writing through it is an error.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which check makes <code>*reading</code> safe to use?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>if (*reading == NULL)</code></li>
            <li class="quiz-option">B. <code>if (&amp;reading != NULL)</code></li>
            <li class="quiz-option">C. <code>if (reading = NULL)</code></li>
            <li class="quiz-option quiz-correct">D. <code>if (reading != NULL)</code> ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Compare the pointer itself (not *reading) against NULL before you follow it.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. A good habit when you declare a pointer you cannot fill yet is to...</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Set it to NULL ✓</li>
            <li class="quiz-option">B. Set it to 1</li>
            <li class="quiz-option">C. Leave it unset</li>
            <li class="quiz-option">D. Dereference it</li>
        </ul>
        <p class="quiz-explanation"><em>Setting it to NULL means it is clearly "nowhere", so a check can catch it, instead of holding a random address.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why does <code>if (reading = NULL)</code> not work as a check?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. NULL can never be compared</li>
            <li class="quiz-option quiz-correct">B. A single = sets reading to NULL ✓</li>
            <li class="quiz-option">C. It needs brackets around NULL</li>
            <li class="quiz-option">D. It reads the value at reading</li>
        </ul>
        <p class="quiz-explanation"><em>A single = assigns; the comparison needs ==, otherwise the pointer is wiped out.</em></p>
    </div>

</div>
