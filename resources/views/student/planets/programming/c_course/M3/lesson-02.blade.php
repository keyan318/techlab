{{-- M3 · Lesson 3.2: Handing Off the Work — content fragment.
Syllabus: CC102 Unit 3 — Functions (parameters). --}}

<div class="lesson-fragment" data-module="m3" data-lesson="02">

    <h1 class="lesson-heading">Lesson 3.2: Handing Off the Work</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write a function that takes one or more parameters, and pass in
        different values each time you call it.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Lesson 3.1's <code>soundAlarm()</code> always did exactly the same thing — useful, but limited. A
        <strong>parameter</strong> lets a function take input, so it can behave differently depending on what
        you hand it:
    </p>

    <pre class="code-block"><code>void functionName(int paramName) {
    // paramName holds whatever value was passed in
}</code></pre>

    <p class="body-text">
        Call it with <code>functionName(40);</code> and <code>paramName</code> holds <code>40</code> inside the
        function. A function can take more than one parameter, separated by commas — each one is a fresh local
        variable that only exists while that call is running.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Every bay reports its own numbers, and Rivet has just caught himself starting to write a tenth
        near-identical job. He puts the marker down before he does it.
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "No. No, I'm not doing this again. Nine alarms taught me something."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "So write one for each bay."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "That's ten jobs that all do the <em>same thing</em> to a different number. I want
        <em>one</em> job that I can hand a number to." He sketches it. "See this gap in the brackets? That's
        where the number goes in. Call it with forty, it works on forty. Call it with seventy-five, it works on
        seventy-five. Same job, whatever you feed it."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="Calling reportShield with 40, then with 75, passes different values into the same function's parameter">
            <rect x="20" y="20" width="180" height="45" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="110" y="47" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">reportShield(40);</text>

            <rect x="20" y="90" width="180" height="45" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="110" y="117" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">reportShield(75);</text>

            <rect x="380" y="50" width="230" height="75" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="495" y="75" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">void reportShield(int level)</text>
            <text x="495" y="95" text-anchor="middle" font-size="12" fill="#14306b">printf("Shield: %d", level);</text>
            <text x="495" y="112" text-anchor="middle" font-size="11" fill="#14306b">level = whatever was passed in</text>

            <line x1="200" y1="42" x2="376" y2="70" stroke="#7c5cff" stroke-width="2.5"/>
            <line x1="200" y1="112" x2="376" y2="100" stroke="#a68cff" stroke-width="2.5" stroke-dasharray="4,3"/>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

void reportShield(int level) {
    printf("Shield: %d\n", level);
}

int main() {
    reportShield(40);
    reportShield(75);
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Write a function called <code>reportCrew</code> that takes two parameters: an <code>int count</code> and
        a <code>char bay</code>. Inside it, print exactly:
    </p>

    <pre class="code-block"><code>Crew 3 assigned to bay B</code></pre>

    <p class="body-text">Then call it as <code>reportCrew(3, 'B');</code> once from <code>main</code>.</p>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Handing Off the Work">
            <input type="hidden" name="filename"     value="report_crew.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

// define reportCrew(int count, char bay) here

int main() {
    // call reportCrew(3, 'B') here

    return 0;
}
">
            <input type="hidden" name="instructions" value="Define a function reportCrew that takes two parameters, in this order: an int called count, and a char called bay. Inside it, print exactly:
Crew 3 assigned to bay B

Then call it from main as reportCrew(3, 'B');">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="void reportCrew(int count, char bay) { ... } — two parameters, separated by a comma, each with its own type. Use %d for count and %c for bay inside the printf.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

void reportCrew(int count, char bay) {
    printf(&quot;Crew %d assigned to bay %c\n&quot;, count, bay);
}

int main() {
    reportCrew(3, 'B');
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm3', 'lesson' => 'lesson02']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is a parameter?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The name of the function itself</li>
            <li class="quiz-option quiz-correct">B. A variable a function receives from whoever calls it ✓</li>
            <li class="quiz-option">C. A comment describing what the function does</li>
            <li class="quiz-option">D. The value a function sends back</li>
        </ul>
        <p class="quiz-explanation"><em>A parameter is a variable declared in a function's parentheses that gets filled in with whatever value is passed at each call.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In <code>reportShield(40); reportShield(75);</code>, what changes between the two calls?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing — both calls do exactly the same thing</li>
            <li class="quiz-option quiz-correct">B. The value passed into the level parameter — 40, then 75 ✓</li>
            <li class="quiz-option">C. The function's name</li>
            <li class="quiz-option">D. The number of times printf runs inside the function</li>
        </ul>
        <p class="quiz-explanation"><em>Each call passes a different value for the same parameter, so the function's behavior adapts to whatever it's handed.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. How do you separate two parameters when defining a function with more than one?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. With a semicolon</li>
            <li class="quiz-option quiz-correct">B. With a comma ✓</li>
            <li class="quiz-option">C. With a colon</li>
            <li class="quiz-option">D. They must be on separate lines</li>
        </ul>
        <p class="quiz-explanation"><em>Multiple parameters are listed in the parentheses separated by commas, each with its own type — like int count, char bay.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. In <code>reportCrew(3, 'B')</code>, calling a function defined as <code>reportCrew(int count, char bay)</code>, what value does bay receive?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 3</li>
            <li class="quiz-option quiz-correct">B. 'B' ✓</li>
            <li class="quiz-option">C. Nothing — bay is left undefined</li>
            <li class="quiz-option">D. Both 3 and 'B'</li>
        </ul>
        <p class="quiz-explanation"><em>Arguments are matched to parameters in order: 3 goes to count (first), and 'B' goes to bay (second).</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. According to Rivet, why is one function with a parameter better than ten separate functions, one for each bay?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. C doesn't allow more than one function in a file</li>
            <li class="quiz-option quiz-correct">B. One function that takes input can handle every case, instead of needing a near-identical copy for each one ✓</li>
            <li class="quiz-option">C. Parameters make a program run in half the time</li>
            <li class="quiz-option">D. There is no real difference between the two approaches</li>
        </ul>
        <p class="quiz-explanation"><em>A parameter lets a single function adapt to whatever value it's handed, avoiding the need for a near-duplicate function per case.</em></p>
    </div>

</div>
