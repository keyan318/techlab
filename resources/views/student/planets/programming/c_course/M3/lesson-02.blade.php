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

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">How long does a parameter exist?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. For the whole program</li>
            <li class="quiz-option">B. Until the computer restarts</li>
            <li class="quiz-option quiz-correct">C. Only while that call is running ✓</li>
            <li class="quiz-option">D. Forever after the first call</li>
        </ul>
        <p class="quiz-explanation"><em>A parameter is a fresh local variable for each call, and it disappears when the call ends.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        A bakery has one cake recipe, and the order form has a <strong>blank</strong>: how many candles? Write 5, and you get a cake with 5 candles. Write 12, and you get 12. Same recipe, different cake.
    </p>

    <p class="body-text">
        That blank is a <strong>parameter</strong>. The number you write in, like <code>makeCake(5)</code>, is the value you pass. Inside the recipe, the blank holds that number for <strong>that one order only</strong>. The next order starts with a fresh blank.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 290" role="img" aria-label="A cake order form for void makeCake with int candles, and a blank for the number of candles. makeCake(5) makes a cake with 5 candles; makeCake(12) makes a cake with 12 candles.">
            <rect x="20" y="50" width="220" height="210" rx="12" fill="#fffaf2" stroke="#f08c1a" stroke-width="2"/><rect x="20" y="50" width="220" height="40" rx="12" fill="#fff0d9"/><rect x="21" y="72" width="218" height="18" fill="#fff0d9"/><line x1="20" y1="90" x2="240" y2="90" stroke="#f08c1a" stroke-width="2"/><rect x="20" y="50" width="220" height="210" rx="12" fill="none" stroke="#f08c1a" stroke-width="2"/><text x="38" y="76" font-size="12" fill="#8a4b00" font-weight="800" letter-spacing="1">ORDER FORM</text><text x="36" y="118" font-size="13" fill="#8a4b00" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">void makeCake(</text><text x="36" y="138" font-size="13" fill="#8a4b00" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">    int candles)</text><text x="36" y="182" font-size="15" fill="#8a4b00" font-weight="800">Candles:</text><rect x="110" y="160" width="60" height="32" rx="6" fill="#fff" stroke="#f08c1a" stroke-width="2" stroke-dasharray="5,4"/><text x="140" y="182" font-size="16" fill="#f08c1a" font-weight="800" text-anchor="middle">?</text><text x="130" y="236" font-size="12" fill="#8a4b00" text-anchor="middle" font-style="italic">one recipe, one blank</text><rect x="300" y="52" width="140" height="34" rx="17.0" fill="#f3f0ff" stroke="#7c5cff" stroke-width="1.8"/><text x="370.0" y="73.68" font-size="13" fill="#4a35b8" text-anchor="middle"><tspan font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="800">makeCake(5);</tspan></text><rect x="470" y="52" width="150" height="34" rx="17.0" fill="#e6f8f9" stroke="#0fa3b1" stroke-width="1.8"/><text x="545.0" y="73.68" font-size="13" fill="#0b6670" text-anchor="middle"><tspan font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="800">makeCake(12);</tspan></text><line x1="242" y1="110" x2="286.7" y2="81.9" stroke="#9aa5b8" stroke-width="2.5"/><polygon points="296,76 283.5,76.8 289.9,86.9" fill="#9aa5b8"/><path d="M242 92 Q380 0 470 56" fill="none" stroke="#9aa5b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><polygon points="476,60 471.2,49.3 464.4,59.2" fill="#9aa5b8"/><rect x="310" y="186" width="120" height="60" rx="8" fill="#ffd6e5" stroke="#e8457c" stroke-width="2"/><rect x="320" y="156" width="100" height="32" rx="6" fill="#fff3c4" stroke="#c99a00" stroke-width="2"/><path d="M310 206 Q320 220 330 206 Q340 220 350 206 Q360 220 370 206 Q380 220 390 206 Q400 220 410 206 Q420 220 430 206" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><rect x="325.5" y="134" width="5" height="22" rx="1.5" fill="#7c5cff"/><ellipse cx="328.0" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="346.5" y="134" width="5" height="22" rx="1.5" fill="#0fa3b1"/><ellipse cx="349.0" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="367.5" y="134" width="5" height="22" rx="1.5" fill="#2f6fe0"/><ellipse cx="370.0" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="388.5" y="134" width="5" height="22" rx="1.5" fill="#2fa56b"/><ellipse cx="391.0" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="409.5" y="134" width="5" height="22" rx="1.5" fill="#7c5cff"/><ellipse cx="412.0" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="480" y="186" width="130" height="60" rx="8" fill="#ffd6e5" stroke="#e8457c" stroke-width="2"/><rect x="490" y="156" width="110" height="32" rx="6" fill="#fff3c4" stroke="#c99a00" stroke-width="2"/><path d="M480 206 Q491 220 502 206 Q512 220 523 206 Q534 220 545 206 Q556 220 567 206 Q578 220 588 206 Q599 220 610 206" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><rect x="495.5" y="134" width="5" height="22" rx="1.5" fill="#7c5cff"/><ellipse cx="498.0" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="504.0" y="134" width="5" height="22" rx="1.5" fill="#0fa3b1"/><ellipse cx="506.5" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="512.6" y="134" width="5" height="22" rx="1.5" fill="#2f6fe0"/><ellipse cx="515.1" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="521.1" y="134" width="5" height="22" rx="1.5" fill="#2fa56b"/><ellipse cx="523.6" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="529.7" y="134" width="5" height="22" rx="1.5" fill="#7c5cff"/><ellipse cx="532.2" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="538.2" y="134" width="5" height="22" rx="1.5" fill="#0fa3b1"/><ellipse cx="540.7" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="546.8" y="134" width="5" height="22" rx="1.5" fill="#2f6fe0"/><ellipse cx="549.3" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="555.3" y="134" width="5" height="22" rx="1.5" fill="#2fa56b"/><ellipse cx="557.8" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="563.9" y="134" width="5" height="22" rx="1.5" fill="#7c5cff"/><ellipse cx="566.4" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="572.4" y="134" width="5" height="22" rx="1.5" fill="#0fa3b1"/><ellipse cx="574.9" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="581.0" y="134" width="5" height="22" rx="1.5" fill="#2f6fe0"/><ellipse cx="583.5" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><rect x="589.5" y="134" width="5" height="22" rx="1.5" fill="#2fa56b"/><ellipse cx="592.0" cy="128" rx="3.2" ry="5" fill="#ffb02e"/><text x="370" y="274" font-size="14" fill="#4a35b8" font-weight="800" text-anchor="middle">5 candles</text><text x="545" y="274" font-size="14" fill="#0b6670" font-weight="800" text-anchor="middle">12 candles</text>
        </svg>
    </figure>

    <p class="body-text">
        A form can have more than one blank, like candles <em>and</em> layers. In C, that's more parameters, separated by commas.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Order 1 says 5 candles, order 2 says 12. Inside order 2, what is <code>candles</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 5, because it was filled in first</li>
            <li class="quiz-option quiz-correct">B. 12, each order has its own blank ✓</li>
            <li class="quiz-option">C. 17, the two orders add together</li>
            <li class="quiz-option">D. 0, a blank is always empty inside</li>
        </ul>
        <p class="quiz-explanation"><em>Each call gets its own fresh copy of the parameter. Order 2 only sees the 12 it was given.</em></p>
    </div>

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

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">What does the code example print on its second line?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Shield: 75 ✓</li>
            <li class="quiz-option">B. Shield: 40</li>
            <li class="quiz-option">C. Shield: level</li>
            <li class="quiz-option">D. Shield: 115</li>
        </ul>
        <p class="quiz-explanation"><em>The second call is <code>reportShield(75);</code>, so <code>level</code> holds 75 during that call.</em></p>
    </div>

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
            <li class="quiz-option">B. A comment about what the function does</li>
            <li class="quiz-option">C. The value a function sends back</li>
            <li class="quiz-option quiz-correct">D. A variable the caller fills with a value ✓</li>
        </ul>
        <p class="quiz-explanation"><em>A parameter is a variable declared in a function's parentheses that gets filled in with whatever value is passed at each call.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In <code>reportShield(40); reportShield(75);</code>, what changes between the two calls?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing; both calls do the same thing</li>
            <li class="quiz-option">B. The name of the function being called</li>
            <li class="quiz-option quiz-correct">C. The value passed into the parameter ✓</li>
            <li class="quiz-option">D. How many times printf runs inside it</li>
        </ul>
        <p class="quiz-explanation"><em>Each call passes a different value for the same parameter, so the function's behavior adapts to whatever it's handed.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. How do you separate two parameters when defining a function with more than one?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. With a comma ✓</li>
            <li class="quiz-option">B. With a semicolon</li>
            <li class="quiz-option">C. With a colon</li>
            <li class="quiz-option">D. With a period</li>
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

</div>
