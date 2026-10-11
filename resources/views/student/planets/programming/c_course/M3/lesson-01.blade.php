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

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Something came in through the broken hatch overnight — something small and fast with too many legs — and
        tripped a sensor at 4 a.m. The alarm worked. Eventually. On the fourth try.
    </p>

    <p class="body-text">
        <strong>Astro</strong> scrolls the file. Then keeps scrolling. "Rivet. How many times have you written
        this same alarm?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>, wincing: "…Nine? Nine times. Slightly different each time, which is the part
        that should worry you."
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "So when it's wrong, it's wrong in nine places and we fix eight of them. No.
        Write it once. Give it a name. Then just say the name."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> brightens immediately. "One job, one name. And when it changes, it changes in
        <em>one</em> place." He's already deleting. "Oh, this is going to feel so good."
    </p>

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
            <li class="quiz-option">A. A type of variable</li>
            <li class="quiz-option quiz-correct">B. A named, reusable block of code you can call by name ✓</li>
            <li class="quiz-option">C. A kind of loop</li>
            <li class="quiz-option">D. A comment that documents the program</li>
        </ul>
        <p class="quiz-explanation"><em>A function packages statements under a name, so you can run them again anywhere just by calling that name.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does <code>void</code> mean in <code>void reportStatus() { ... }</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The function takes no parameters</li>
            <li class="quiz-option quiz-correct">B. The function does not send any value back to its caller ✓</li>
            <li class="quiz-option">C. The function cannot be called more than once</li>
            <li class="quiz-option">D. The function is empty and does nothing</li>
        </ul>
        <p class="quiz-explanation"><em>void means the function has no return value — it performs an action (like printing) but hands nothing back.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. In the Code Example, why does "Systems nominal." print twice?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. printf always runs its line twice by default</li>
            <li class="quiz-option quiz-correct">B. main() calls reportStatus() twice, and each call re-runs the whole function ✓</li>
            <li class="quiz-option">C. It's a typo — it should only print once</li>
            <li class="quiz-option">D. void functions always run exactly twice</li>
        </ul>
        <p class="quiz-explanation"><em>Every call to reportStatus() re-runs its body from the top — call it twice, and its printf runs twice.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why must a function be defined above main (or otherwise known to it) before main can call it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. There is no real reason — order never matters in C</li>
            <li class="quiz-option quiz-correct">B. C reads top to bottom, so main can only call a function it has already seen ✓</li>
            <li class="quiz-option">C. Functions can only be called from other functions, never from main</li>
            <li class="quiz-option">D. void functions are always defined automatically</li>
        </ul>
        <p class="quiz-explanation"><em>The compiler processes a file top to bottom, so a function's definition (or at least its prototype, coming in Lesson 3.4) must appear before any code that calls it.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. According to Rivet, what's the main benefit of writing the alarm logic as one function instead of copying it everywhere it's needed?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Functions make a program run faster, always</li>
            <li class="quiz-option quiz-correct">B. A later change only has to be made in one place, not everywhere it was copied ✓</li>
            <li class="quiz-option">C. It lets the program skip writing printf statements</li>
            <li class="quiz-option">D. It removes the need for main() entirely</li>
        </ul>
        <p class="quiz-explanation"><em>Writing logic once inside a function means fixing or changing it later only takes one edit, instead of hunting down every copy.</em></p>
    </div>

</div>
