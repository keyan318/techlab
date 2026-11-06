{{-- M1 · Lesson 1.1: The First Street — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Functions — functions as subprograms. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="01">

    <h1 class="lesson-heading">Lesson 1.1: The First Street</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what a function is as a <em>subprogram</em>, split one big job into small named jobs, and define and call your own <code>void</code> function in C.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>function</strong> is a small, named program that lives inside your program. People also call it a <strong>subprogram</strong>. You write the steps once, give them a name, and then run them whenever you need them, just by calling the name.
    </p>

    <p class="body-text">
        <code>main()</code> is a function too. It is the one the computer runs first. In a well-built program, <code>main()</code> works like a manager: it does not do every job itself, it calls other functions to do the jobs. Breaking a big problem into smaller subprograms like this is called <strong>modular</strong> or <strong>top-down</strong> design.
    </p>

    <p class="body-text">
        Every function has four parts:
    </p>

    <pre class="code-block"><code>void lightLamp(int number) {      // return type, name, parameter
    printf("Lamp %d: on\n", number);   // body: the steps
}</code></pre>

    <ul class="body-list">
        <li><strong>Return type</strong>: what the function hands back. <code>void</code> means "nothing".</li>
        <li><strong>Name</strong>: what you call it by, like <code>lightLamp</code>.</li>
        <li><strong>Parameters</strong>: the values it needs, in brackets, like <code>int number</code>.</li>
        <li><strong>Body</strong>: the steps, between <code>{</code> and <code>}</code>.</li>
    </ul>

    <p class="body-text">
        To <strong>call</strong> it, write its name with a value in the brackets: <code>lightLamp(1);</code>. In C, a function has to be defined <em>above</em> the code that calls it (or announced there; you will see how in Lesson 1.5).
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        The morning after the hull grid lit up, the Dead Console is still saying the same thing every time Rivet switches it on: <code>HELLO. WHO ARE YOU?</code>
    </p>

    <p class="body-text">
        <strong>Rivet</strong> has its memory board out on the workbench. "I can fix this. Probably. I need parts, and the parts are down there."
    </p>

    <p class="body-text">
        <strong>Astro</strong> is at the cracked window of the Bridge, looking at the city. "So is the power. The Wanderer won't lift off on its own cells. The plant in the middle of that city could charge us, if the city were running. So we switch it on. Street by street."
    </p>

    <p class="body-text">
        They walk down the slope onto <strong>Gate Road</strong>, the first street of Programming City. Whatever Vex's aliens did here, they did it long ago, and they left. Under the blinking streetlamp there is a grey control box with a tiny screen and a keyboard.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> wipes the dust off and laughs. "It's C. The whole city runs on C. We already speak its language."
    </p>

    <p class="body-text">
        Gate Road has three lamps, and each one needs the same steps to light. <strong>Astro</strong>: "Then it's one job. Give the job a name, and call it for every lamp. Don't write it three times."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 220" role="img" aria-label="main calls the lightLamp function three times, once for each lamp, then prints Street lit">
            <rect x="20" y="70" width="170" height="80" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="105" y="107" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">main()</text>
            <text x="105" y="124" text-anchor="middle" font-size="12" fill="#14306b">the manager</text>
            <rect x="330" y="15" width="170" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="415" y="45" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">lightLamp(1)</text>
            <rect x="330" y="85" width="170" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="415" y="115" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">lightLamp(2)</text>
            <rect x="330" y="155" width="170" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="415" y="185" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">lightLamp(3)</text>
            <line x1="190" y1="95" x2="316.7" y2="43.7" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="326,40 312.6,38.9 317.1,50.1" fill="#7c5cff"/>
            <line x1="190" y1="110" x2="316.0" y2="110.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="326,110 314.0,104.0 314.0,116.0" fill="#7c5cff"/>
            <line x1="190" y1="125" x2="316.7" y2="176.3" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="326,180 317.1,169.9 312.6,181.1" fill="#7c5cff"/>
            <text x="570" y="45" text-anchor="middle" font-size="13" fill="#7a5a00" font-family="ui-monospace, monospace">Lamp 1: on</text>
            <text x="570" y="115" text-anchor="middle" font-size="13" fill="#7a5a00" font-family="ui-monospace, monospace">Lamp 2: on</text>
            <text x="570" y="185" text-anchor="middle" font-size="13" fill="#7a5a00" font-family="ui-monospace, monospace">Lamp 3: on</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

void checkBulb(int number) {
    printf("Checking bulb %d\n", number);
}

int main() {
    checkBulb(1);
    checkBulb(2);
    return 0;
}</code></pre>

    <p class="body-text">
        The steps inside <code>checkBulb</code> are written once and run twice. Each call hands the function a different <code>number</code>, so each time it prints a different line.
    </p>
    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. Define a function <code>void lightLamp(int number)</code> that prints <code>Lamp &lt;number&gt;: on</code>. In <code>main</code>, call it for lamps 1, 2 and 3, then print <code>Street lit.</code> The output must be exactly:
    </p>

    <pre class="code-block"><code>Lamp 1: on
Lamp 2: on
Lamp 3: on
Street lit.</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="The First Street">
            <input type="hidden" name="filename"     value="gate_road.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

// define lightLamp(int number) here: it prints &quot;Lamp &lt;number&gt;: on&quot;

int main() {
    // call lightLamp for lamps 1, 2 and 3, then print &quot;Street lit.&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="Gate Road has three lamps and every one needs the same steps.

Define void lightLamp(int number) above main. It prints:
Lamp &lt;number&gt;: on

In main, call lightLamp(1), lightLamp(2) and lightLamp(3), then print:
Street lit.">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="Put the function above main so C knows it before main calls it. Inside, one printf does it: printf(&quot;Lamp %d: on\n&quot;, number); Then main is just four lines: three calls and the last printf.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

void lightLamp(int number) {
    printf(&quot;Lamp %d: on\n&quot;, number);
}

int main() {
    lightLamp(1);
    lightLamp(2);
    lightLamp(3);
    printf(&quot;Street lit.\n&quot;);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm1', 'lesson' => 'lesson01']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why is a function also called a <em>subprogram</em>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It only runs on small computers</li>
            <li class="quiz-option quiz-correct">B. It is a small program inside a program ✓</li>
            <li class="quiz-option">C. It runs before the main program starts</li>
            <li class="quiz-option">D. It takes the place of <code>main()</code></li>
        </ul>
        <p class="quiz-explanation"><em>A function is a named set of steps inside your program, a little program of its own that you can run by calling its name.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In a well-built program, what is <code>main()</code>'s job?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. To hold every line of the program</li>
            <li class="quiz-option">B. To wait to be called by other functions</li>
            <li class="quiz-option quiz-correct">C. To call other functions to do the work ✓</li>
            <li class="quiz-option">D. Nothing, since it is optional</li>
        </ul>
        <p class="quiz-explanation"><em>main() is where the program starts, and in modular design it hands the real jobs to other functions.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does the return type <code>void</code> mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The function hands nothing back ✓</li>
            <li class="quiz-option">B. The function is empty</li>
            <li class="quiz-option">C. The function has an error</li>
            <li class="quiz-option">D. The function returns 0</li>
        </ul>
        <p class="quiz-explanation"><em>void means the function does its job but returns no value to the code that called it.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. In <code>void lightLamp(int number)</code>, what is <code>int number</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The return type</li>
            <li class="quiz-option">B. The function's name</li>
            <li class="quiz-option quiz-correct">C. A parameter ✓</li>
            <li class="quiz-option">D. The function's body</li>
        </ul>
        <p class="quiz-explanation"><em>Parameters sit inside the brackets and carry the values the function needs each time it is called.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. A program needs the same five steps for 20 different lamps. What is the best plan?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Copy the five steps out 20 times</li>
            <li class="quiz-option">B. Put all 100 lines inside main()</li>
            <li class="quiz-option">C. Write 20 separate small programs</li>
            <li class="quiz-option quiz-correct">D. Write one function, call it 20 times ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Writing the steps once in a function means one place to read, one place to fix, and as many calls as you need.</em></p>
    </div>

</div>
