{{-- M3 · Lesson 3.4: The Blueprint — content fragment.
Syllabus: CC102 Unit 3 — Functions (function prototypes). --}}

<div class="lesson-fragment" data-module="m3" data-lesson="04">

    <h1 class="lesson-heading">Lesson 3.4: The Blueprint</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write a function prototype and explain why it lets
        <code>main</code> call a function that is fully defined later in the same file.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Lesson 3.1 said a function must be defined before it's called. That's true — but "defined" doesn't have
        to mean the <em>whole</em> function. A <strong>prototype</strong> is a one-line preview: the function's
        return type, name, and parameter types, ending in a semicolon, with no body:
    </p>

    <pre class="code-block"><code>int addParts(int a, int b);   // prototype: no body, just a promise</code></pre>

    <p class="body-text">
        Put a prototype above <code>main</code>, and <code>main</code> can call that function — even though the
        full definition, with its actual body, doesn't appear until further down the file. The compiler trusts
        the prototype's promise and checks the real definition matches it later.
    </p>

    <p class="body-text">
        Why bother? It lets you write <code>main</code> first, near the top of the file where it's easy to find
        — reading like a table of contents — while the full function bodies live below, out of the way.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Volt opens the console file to check one thing and scrolls for a full minute without finding it.
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Rivet. If you go down with a broken leg, I have to fix this ship. I can't read
        a hundred lines to find out what it even does."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> is quiet for a second. "…That's fair." He scrolls back to the top and starts
        chalking job names in a list. "Right. Names at the top. Just the names — what each job is called, what
        it takes, what it gives back. Five lines, and you know the whole shape of it."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And the actual work?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Further down, out of your way. You read the list, you find the one you want,
        <em>then</em> you go look at it." He taps the top of the screen. "Table of contents. Same as any book."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="A file's layout: includes, then a prototype, then main which calls the function, then the real definition below">
            <rect x="220" y="10" width="200" height="34" rx="8" fill="#f3f0ff" stroke="#7c5cff" stroke-width="1.5"/>
            <text x="320" y="32" text-anchor="middle" font-size="12" fill="#4a35b8">#include &lt;stdio.h&gt;</text>

            <rect x="220" y="54" width="200" height="34" rx="8" fill="#fff4e0" stroke="#f08c1a" stroke-width="1.5"/>
            <text x="320" y="76" text-anchor="middle" font-size="12" font-weight="700" fill="#8a4b00">int doubleShield(int); ← prototype</text>

            <rect x="220" y="98" width="200" height="45" rx="8" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="1.5"/>
            <text x="320" y="118" text-anchor="middle" font-size="12" font-weight="700" fill="#14306b">int main() {</text>
            <text x="320" y="135" text-anchor="middle" font-size="11" fill="#14306b">doubleShield(15); }</text>

            <rect x="220" y="153" width="200" height="45" rx="8" fill="#e6f7ee" stroke="#2fa56b" stroke-width="1.5"/>
            <text x="320" y="173" text-anchor="middle" font-size="12" font-weight="700" fill="#14663f">int doubleShield(int n) {</text>
            <text x="320" y="190" text-anchor="middle" font-size="11" fill="#14663f">return n * 2; } ← real body</text>

            <line x1="420" y1="120" x2="450" y2="175" stroke="#a68cff" stroke-width="2" stroke-dasharray="4,3"/>
            <text x="480" y="150" text-anchor="middle" font-size="11" fill="#4a35b8">the prototype's</text>
            <text x="480" y="164" text-anchor="middle" font-size="11" fill="#4a35b8">promise, kept</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int addParts(int a, int b);   // prototype

int main() {
    printf("Total: %d\n", addParts(20, 5));
    return 0;
}

int addParts(int a, int b) {   // real definition, below main
    return a + b;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Write a function <code>doubleShield</code> that takes one <code>int level</code> and returns
        <code>level * 2</code>. Put its prototype above <code>main</code>, call it as
        <code>doubleShield(15)</code> inside <code>main</code>, and print exactly:
    </p>

    <pre class="code-block"><code>Boosted: 30</code></pre>

    <p class="body-text">Then write the full function definition, with its body, below <code>main</code>.</p>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="The Blueprint">
            <input type="hidden" name="filename"     value="double_shield.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

// prototype for doubleShield(int level) goes here

int main() {
    // call doubleShield(15), print &quot;Boosted: &quot; + the result

    return 0;
}

// full definition of doubleShield goes here, below main
">
            <input type="hidden" name="instructions" value="1. Above main, write a prototype: int doubleShield(int level);
2. Inside main, call doubleShield(15) and print &quot;Boosted: &quot; followed by the result.
3. Below main, write the full definition: it returns level * 2.

Expected output:
Boosted: 30">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="The prototype is just the definition's first line with a semicolon instead of a body: int doubleShield(int level);. The real definition below main looks exactly like any other function you've written.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int doubleShield(int level);

int main() {
    printf(&quot;Boosted: %d\n&quot;, doubleShield(15));
    return 0;
}

int doubleShield(int level) {
    return level * 2;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm3', 'lesson' => 'lesson04']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is a function prototype?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A second, backup copy of the whole function</li>
            <li class="quiz-option quiz-correct">B. A one-line preview of a function's return type, name and parameters, with no body ✓</li>
            <li class="quiz-option">C. A comment describing what a function does</li>
            <li class="quiz-option">D. The first time a function is called</li>
        </ul>
        <p class="quiz-explanation"><em>A prototype is just the function's signature — return type, name, and parameter types — ending in a semicolon instead of a body.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does a prototype let you do that you couldn't do otherwise?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Call a function before its full definition appears in the file</li>
            <li class="quiz-option quiz-correct">B. Exactly that — call a function before its full definition appears in the file ✓</li>
            <li class="quiz-option">C. Skip writing the function's real body entirely</li>
            <li class="quiz-option">D. Run a function without ever calling it</li>
        </ul>
        <p class="quiz-explanation"><em>The prototype's promise is enough for the compiler to accept a call — the real definition just has to show up somewhere later in the file.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What ends a function prototype, instead of a body in curly braces?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A colon</li>
            <li class="quiz-option quiz-correct">B. A semicolon ✓</li>
            <li class="quiz-option">C. Nothing — prototypes have bodies too</li>
            <li class="quiz-option">D. A comma</li>
        </ul>
        <p class="quiz-explanation"><em>A prototype is one line ending in a semicolon — no { } body at all, unlike the real definition.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. According to Rivet, why put every prototype near the top of the file?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. C requires all prototypes to be on the very first line</li>
            <li class="quiz-option quiz-correct">B. It shows the shape of the whole program at a glance, before scrolling through every function's body ✓</li>
            <li class="quiz-option">C. It makes the program run faster</li>
            <li class="quiz-option">D. Prototypes must always come before #include lines</li>
        </ul>
        <p class="quiz-explanation"><em>Grouping prototypes near the top acts like a table of contents for the file, readable before diving into any function's full implementation.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If a function's real definition returns a different type than its prototype promised, what happens?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. C automatically fixes the mismatch</li>
            <li class="quiz-option quiz-correct">B. It's a mismatch the compiler checks for and flags as an error ✓</li>
            <li class="quiz-option">C. The prototype silently wins and the real definition is ignored</li>
            <li class="quiz-option">D. Nothing — prototypes are purely decorative comments</li>
        </ul>
        <p class="quiz-explanation"><em>The compiler holds the real definition to the prototype's promise — a mismatched return type or parameter list is flagged as an error.</em></p>
    </div>

</div>
