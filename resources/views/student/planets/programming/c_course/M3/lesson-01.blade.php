{{-- M3 · Lesson 3.1: Astro Splits the Work — content fragment.
Syllabus: CC102 Unit 3 — Functions (definition, declaration, and calling). --}}

<div class="lesson-fragment" data-module="m3" data-lesson="01">

    <h1 class="lesson-heading">Lesson 3.1: Astro Splits the Work</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can define a function with no return value, call it as many times as
        you need, and explain why breaking a program into functions makes it easier to work with.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>function</strong> is a named, reusable block of code. Instead of writing the same statements
        over and over, you write them once inside a function, then <em>call</em> the function by name whenever
        you need to run them.
    </p>

    <pre class="code-block"><code>void functionName() {
    // statements to run
}</code></pre>

    <p class="body-text">
        <code>void</code> means this function doesn't send any value back to whoever called it — it just does
        something. <code>main()</code> is itself a function; every C program has been calling functions since
        Lesson 1.1, just built-in ones like <code>printf</code>. Now you'll write your own.
    </p>

    <p class="body-text">
        A function must be <strong>defined</strong> (written out) before it can be <strong>called</strong> (used)
        further down the file — <code>main</code> can only call functions it already knows about.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does it mean to call a function?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Write out its body once</li>
            <li class="quiz-option">B. Delete it from the file</li>
            <li class="quiz-option">C. Give it a brand-new name</li>
            <li class="quiz-option quiz-correct">D. Run it by using its name ✓</li>
        </ul>
        <p class="quiz-explanation"><em>You define a function once, then call it by name, like <code>reportStatus();</code>, whenever you want it to run.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        A song sheet doesn't write the chorus out five times. It writes it <strong>once</strong>, gives it a name, and then just says <strong>“Chorus”</strong> wherever it should be sung.
    </p>

    <p class="body-text">
        A <strong>function</strong> is the same idea. You write the steps once, give them a name, and then <strong>call</strong> that name whenever you need them. <code>void</code> means the chorus just gets sung: it does something, but it doesn't hand anything back. And the chorus has to be written down somewhere above before the sheet can say “sing the chorus”.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 345" role="img" aria-label="A song sheet with the chorus written once at the top, and Chorus pointers after verse 1 and verse 2. The same in C: void chorus defined once, then main calls verse1, chorus, verse2, chorus.">
            <rect x="20" y="30" width="310" height="300" rx="12" fill="#fffdf7" stroke="#d8cfb6" stroke-width="2"/><text x="40" y="56" font-size="12" fill="#8a6a2b" font-weight="800" letter-spacing="1">SONG SHEET</text><rect x="40" y="70" width="270" height="72" rx="10" fill="#ffe3ec" stroke="#e8457c" stroke-width="2"/><text x="56" y="92" font-size="12" fill="#9c1f4c" font-weight="800" letter-spacing="0.5">CHORUS · written once</text><text x="56" y="113" font-size="13" fill="#9c1f4c" font-style="italic">Hands up high, here we go,</text><text x="56" y="132" font-size="13" fill="#9c1f4c" font-style="italic">sing it loud and nice and slow!</text><text x="40" y="176" font-size="13" fill="#5b6b86">Verse 1: Woke up early, sun is out…</text><rect x="40" y="188" width="100" height="28" rx="14.0" fill="#ffe3ec" stroke="#e8457c" stroke-width="1.8"/><text x="90.0" y="206.68" font-size="13" fill="#9c1f4c" text-anchor="middle"><tspan font-weight="800">Chorus</tspan></text><text x="40" y="252" font-size="13" fill="#5b6b86">Verse 2: Grab your friends and…</text><rect x="40" y="264" width="100" height="28" rx="14.0" fill="#ffe3ec" stroke="#e8457c" stroke-width="1.8"/><text x="90.0" y="282.68" font-size="13" fill="#9c1f4c" text-anchor="middle"><tspan font-weight="800">Chorus</tspan></text><path d="M144 202 Q270 196 270 152" fill="none" stroke="#e8457c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="5,4"/><polygon points="270,145 264.0,154.0 276.0,154.0" fill="#e8457c"/><path d="M144 278 Q300 268 296 152" fill="none" stroke="#e8457c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="5,4"/><polygon points="296,145 290.3,154.2 302.3,153.8" fill="#e8457c"/><rect x="352" y="30" width="268" height="300" rx="12" fill="#f6f8fb" stroke="#cfd7e6" stroke-width="2"/><text x="370" y="56" font-size="12" fill="#5b6b86" font-weight="800" letter-spacing="1">THE SAME IN C</text><text x="368" y="88" font-size="13" fill="#9c1f4c" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">void&#160;chorus()&#160;{</text><text x="368" y="111" font-size="13" fill="#9c1f4c" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">&#160;&#160;printf("Hands&#160;up...");</text><text x="368" y="134" font-size="13" fill="#9c1f4c" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">}</text><text x="368" y="180" font-size="13" fill="#1b2440" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">int&#160;main()&#160;{</text><text x="368" y="203" font-size="13" fill="#5b6b86" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">&#160;&#160;verse1();</text><text x="368" y="226" font-size="13" fill="#9c1f4c" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">&#160;&#160;chorus();</text><text x="368" y="249" font-size="13" fill="#5b6b86" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">&#160;&#160;verse2();</text><text x="368" y="272" font-size="13" fill="#9c1f4c" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">&#160;&#160;chorus();</text><text x="368" y="295" font-size="13" fill="#1b2440" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">}</text><text x="604" y="88" font-size="11" fill="#9c1f4c" font-weight="700" text-anchor="end">← write once</text><text x="604" y="226" font-size="11" fill="#9c1f4c" font-weight="700" text-anchor="end">← call it</text><text x="604" y="272" font-size="11" fill="#9c1f4c" font-weight="700" text-anchor="end">← again</text>
        </svg>
    </figure>

    <p class="body-text">
        Bonus: if you want to change the words, you change them in one place and every chorus updates.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why write the chorus once and just say “Chorus” after each verse?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Songs are only allowed one chorus each</li>
            <li class="quiz-option">B. It makes the song play louder each time</li>
            <li class="quiz-option quiz-correct">C. Write it once, reuse it many times ✓</li>
            <li class="quiz-option">D. The chorus changes every time it's sung</li>
        </ul>
        <p class="quiz-explanation"><em>Writing it once means less repeating and only one place to fix. Calling a function by name works the same way.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 190" role="img" aria-label="main calls reportStatus twice; each call jumps to the function, runs it, and returns">
            <rect x="20" y="30" width="170" height="130" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="105" y="55" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">main()</text>
            <text x="105" y="85" text-anchor="middle" font-size="12" fill="#4a35b8">reportStatus();</text>
            <text x="105" y="105" text-anchor="middle" font-size="12" fill="#4a35b8">reportStatus();</text>

            <rect x="420" y="55" width="200" height="80" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="520" y="80" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">reportStatus()</text>
            <text x="520" y="100" text-anchor="middle" font-size="11.5" fill="#14306b">printf("Systems</text>
            <text x="520" y="115" text-anchor="middle" font-size="11.5" fill="#14306b">nominal.");</text>

            <line x1="190" y1="85" x2="416" y2="80" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="420,80 408,74 408,86" fill="#7c5cff"/>
            <line x1="416" y1="105" x2="190" y2="105" stroke="#a68cff" stroke-width="2.5" stroke-dasharray="4,3"/>
            <polygon points="190,105 202,99 202,111" fill="#a68cff"/>

            <text x="320" y="168" text-anchor="middle" font-size="13" fill="#475569">Every call jumps in, runs the function, then returns to right after the call.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

void reportStatus() {
    printf("Systems nominal.\n");
}

int main() {
    reportStatus();
    reportStatus();
    return 0;
}</code></pre>

    <p class="body-text">
        Output: <code>reportStatus()</code> runs twice because <code>main</code> calls it twice, printing the
        same line each time.
    </p>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">If <code>main</code> called <code>reportStatus();</code> three times, how many times would “Systems nominal.” print?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Once</li>
            <li class="quiz-option">B. Twice</li>
            <li class="quiz-option quiz-correct">C. Three times ✓</li>
            <li class="quiz-option">D. Never</li>
        </ul>
        <p class="quiz-explanation"><em>Every call runs the function's body once, so three calls print the line three times.</em></p>
    </div>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Write a function called <code>soundAlarm</code> that takes no parameters and returns nothing, and prints
        exactly this line inside it:
    </p>

    <pre class="code-block"><code>Alarm: intruder detected.</code></pre>

    <p class="body-text">Then call <code>soundAlarm()</code> once from <code>main</code>.</p>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Astro Splits the Work">
            <input type="hidden" name="filename"     value="alarm.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

// define soundAlarm() here, printing &quot;Alarm: intruder detected.&quot;

int main() {
    // call soundAlarm() here

    return 0;
}
">
            <input type="hidden" name="instructions" value="Define a function above main called soundAlarm that takes no parameters, returns nothing (void), and prints exactly:
Alarm: intruder detected.

Then call soundAlarm(); once, inside main.">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="void soundAlarm() { ... } defines the function above main. Inside main, calling it is just soundAlarm(); — the function's name followed by parentheses and a semicolon.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

void soundAlarm() {
    printf(&quot;Alarm: intruder detected.\n&quot;);
}

int main() {
    soundAlarm();
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm3', 'lesson' => 'lesson01']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is a function in C?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A variable that stores some code</li>
            <li class="quiz-option">B. A loop that runs a set number of times</li>
            <li class="quiz-option quiz-correct">C. A named block of code you can reuse ✓</li>
            <li class="quiz-option">D. A comment that explains the program</li>
        </ul>
        <p class="quiz-explanation"><em>A function packages statements under a name, so you can run them again anywhere just by calling that name.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does <code>void</code> mean in <code>void reportStatus() { ... }</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. It sends no value back to its caller ✓</li>
            <li class="quiz-option">B. It cannot be called more than once</li>
            <li class="quiz-option">C. It is empty and does nothing at all</li>
            <li class="quiz-option">D. It can only be called from inside main</li>
        </ul>
        <p class="quiz-explanation"><em>void means the function has no return value — it performs an action (like printing) but hands nothing back.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. In the Code Example, why does "Systems nominal." print twice?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. printf always prints each line twice</li>
            <li class="quiz-option">B. void functions always run exactly twice</li>
            <li class="quiz-option">C. The function body has two printf calls</li>
            <li class="quiz-option quiz-correct">D. main() calls reportStatus() twice ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Every call to reportStatus() re-runs its body from the top — call it twice, and its printf runs twice.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why must a function be defined above main (or otherwise known to it) before main can call it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. No reason; order never matters in C</li>
            <li class="quiz-option quiz-correct">B. C reads the file from top to bottom ✓</li>
            <li class="quiz-option">C. main can only call built-in functions</li>
            <li class="quiz-option">D. Functions below main run automatically</li>
        </ul>
        <p class="quiz-explanation"><em>The compiler processes a file top to bottom, so a function's definition (or at least its prototype, coming in Lesson 3.4) must appear before any code that calls it.</em></p>
    </div>

</div>
