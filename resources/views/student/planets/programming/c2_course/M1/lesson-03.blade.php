{{-- M1 · Lesson 1.3: What Stays in the Toolbox — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Functions — local variables; local vs global scope. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="03">

    <h1 class="lesson-heading">Lesson 1.3: What Stays in the Toolbox</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can tell local and global variables apart, explain where each one can be used (its <em>scope</em>), and use a global counter that several function calls share.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Where you declare a variable decides who can see it. That is called its <strong>scope</strong>.
    </p>

    <ul class="body-list">
        <li>A <strong>local variable</strong> is declared inside a function. Only that function can use it, and it disappears when the function finishes. The next call starts fresh.</li>
        <li>A <strong>global variable</strong> is declared outside every function, usually at the top of the file. Every function can read it and change it, and it keeps its value for the whole program.</li>
    </ul>

    <pre class="code-block"><code>int lampsLit = 0;            // global: every function sees it

void lightLamp() {
    int seconds = 3;         // local: only lightLamp sees it
    lampsLit = lampsLit + 1;
}</code></pre>

    <p class="body-text">
        Two different functions can each have a local variable with the same name; they are separate boxes. If a local has the same name as a global, the local one wins inside that function.
    </p>

    <p class="body-text">
        Globals are handy for a count everyone shares, but use them carefully: because any function can change a global, a mistake anywhere can break it. Most variables should be local. There is also a middle option: a <code>static</code> local, like <code>static int count = 0;</code>, is only visible inside its function but keeps its value between calls.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        The junction box has a little counter in the corner of its screen: <code>LAMPS LIT: 0</code>. Every crew job on Gate Road is meant to add to it.
    </p>

    <p class="body-text">
        <strong>Volt</strong> doesn't like it. "Every job can reach that number? Any of them can change it? Then any of them can break it. One bad job and the whole count is wrong, and you won't know which job did it."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> is scribbling sums on his glove. "That's why it's only that one number. Everything else a job uses, like my scribbles here, lives inside the job. Job ends, scribbles are gone. Nobody else ever sees them."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "So the shared one is on the wall for everyone. The rest stays in each job's own toolbox." <strong>Rivet</strong>: "Exactly. Keep the wall small."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="A global lampsLit counter outside every function is shared by all calls; a local seconds variable lives only inside lightLamp">
            <rect x="200" y="12" width="240" height="52" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="320" y="34.5" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1a14">global: lampsLit</text>
            <text x="320" y="51.5" text-anchor="middle" font-size="12" fill="#7a1a14">every function can see it</text>
            <rect x="30" y="110" width="250" height="80" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="155" y="138.5" text-anchor="middle" font-size="15" font-weight="700" fill="#4a35b8">lightLamp()</text>
            <text x="155" y="155.5" text-anchor="middle" font-size="12" fill="#4a35b8">local: seconds = 3</text>
            <text x="155" y="172.5" text-anchor="middle" font-size="12" fill="#4a35b8">gone when the call ends</text>
            <rect x="360" y="110" width="250" height="80" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="485" y="138.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">main()</text>
            <text x="485" y="155.5" text-anchor="middle" font-size="12" fill="#14306b">calls lightLamp() 3 times</text>
            <text x="485" y="172.5" text-anchor="middle" font-size="12" fill="#14306b">then prints lampsLit</text>
            <line x1="155" y1="106" x2="260.6" y2="69.3" stroke="#b3261e" stroke-width="2.5"/>
            <polygon points="270,66 256.7,64.3 260.6,75.6" fill="#b3261e"/>
            <line x1="485" y1="106" x2="379.4" y2="69.3" stroke="#b3261e" stroke-width="2.5"/>
            <polygon points="370,66 379.4,75.6 383.3,64.3" fill="#b3261e"/>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int visits = 0;              // global

void visitDepot() {
    int boxes = 2;           // local, made fresh each call
    visits = visits + 1;
    printf("Boxes this trip: %d\n", boxes);
}

int main() {
    visitDepot();
    visitDepot();
    printf("Trips: %d\n", visits);   // Trips: 2
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Make a global <code>int lampsLit = 0;</code> above every function. Write <code>void lightLamp()</code> with a local <code>int seconds = 3;</code> inside, and have it add 1 to <code>lampsLit</code>. Call it three times from <code>main</code>, then print exactly:
    </p>

    <pre class="code-block"><code>Lamps lit: 3</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="What Stays in the Toolbox">
            <input type="hidden" name="filename"     value="lamp_counter.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

// declare the global counter lampsLit here, starting at 0

void lightLamp() {
    int seconds = 3;   // local: only lightLamp can see this
    // add 1 to the global counter
}

int main() {
    // call lightLamp() three times, then print &quot;Lamps lit: &quot; and the counter

    return 0;
}
">
            <input type="hidden" name="instructions" value="The junction box keeps one shared count of lamps lit.

1. Above every function, declare a global: int lampsLit = 0;
2. Inside lightLamp(), add 1 to lampsLit (seconds stays local).
3. In main, call lightLamp() three times, then print exactly:
Lamps lit: 3">
            <input type="hidden" name="hint_title"   value="Volt's Hint">
            <input type="hidden" name="hint_body"    value="The global goes at the very top, outside every function: int lampsLit = 0; Inside lightLamp, write lampsLit = lampsLit + 1; (or lampsLit++;). Do not declare int lampsLit again inside the function: that would make a new local box and the global would never change.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int lampsLit = 0;

void lightLamp() {
    int seconds = 3;
    lampsLit = lampsLit + 1;
}

int main() {
    lightLamp();
    lightLamp();
    lightLamp();
    printf(&quot;Lamps lit: %d\n&quot;, lampsLit);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm1', 'lesson' => 'lesson03']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Where is a local variable declared?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Outside every function</li>
            <li class="quiz-option quiz-correct">B. Inside a function ✓</li>
            <li class="quiz-option">C. In a header file only</li>
            <li class="quiz-option">D. Inside printf</li>
        </ul>
        <p class="quiz-explanation"><em>A local variable is declared inside a function, and only that function can use it.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What happens to a normal local variable when its function finishes?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It keeps its value for next time</li>
            <li class="quiz-option">B. It turns into a global variable</li>
            <li class="quiz-option quiz-correct">C. It disappears with the call ✓</li>
            <li class="quiz-option">D. It is printed on the screen</li>
        </ul>
        <p class="quiz-explanation"><em>A normal local only exists while its function runs; the next call starts with a fresh one.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why should you be careful with global variables?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They take longer to type out</li>
            <li class="quiz-option">B. They can only ever hold text</li>
            <li class="quiz-option">C. They are deleted after one use</li>
            <li class="quiz-option quiz-correct">D. Any function can change them ✓</li>
        </ul>
        <p class="quiz-explanation"><em>A global is shared by every function, which makes it easy for one wrong function to spoil it for all.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Inside <code>lightLamp</code>, someone writes <code>int lampsLit = 0;</code> even though a global <code>lampsLit</code> exists. What happens?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. A new local hides the global there ✓</li>
            <li class="quiz-option">B. The global is reset back to 0</li>
            <li class="quiz-option">C. The program will refuse to compile</li>
            <li class="quiz-option">D. Both variables are deleted</li>
        </ul>
        <p class="quiz-explanation"><em>Declaring it again inside the function makes a new local box with the same name; inside that function the local hides the global.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does <code>static int count = 0;</code> inside a function do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Makes count visible to every function</li>
            <li class="quiz-option">B. Makes count a value that can't change</li>
            <li class="quiz-option quiz-correct">C. Keeps count's value between calls ✓</li>
            <li class="quiz-option">D. Resets count to 0 after each call</li>
        </ul>
        <p class="quiz-explanation"><em>A static local is still private to its function, but it remembers its value from one call to the next.</em></p>
    </div>

</div>
