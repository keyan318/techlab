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

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does a prototype leave out?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The return type</li>
            <li class="quiz-option">B. The function's name</li>
            <li class="quiz-option quiz-correct">C. The function's body ✓</li>
            <li class="quiz-option">D. The parameter types</li>
        </ul>
        <p class="quiz-explanation"><em>A prototype is the one-line promise: return type, name and parameter types, ending in a semicolon, with no body.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        A book's <strong>table of contents</strong> tells you a chapter exists, what it's called, and which page it's on, before you ever get there. You trust it and keep reading.
    </p>

    <p class="body-text">
        A <strong>prototype</strong> is a contents line for a function: its name, what goes in, what comes out, and a semicolon. No body. Put it above <code>main</code>, and <code>main</code> can call the function even though the full chapter, the <strong>definition</strong>, is further down. The compiler later checks that the chapter really matches what the contents page promised.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 315" role="img" aria-label="An open book. The contents page lists 1, main, page 2, and 2, addParts(a, b), page 40: a promise that the chapter is coming later. Page 40 holds the full chapter. The prototype int addParts(int a, int b) with a semicolon is the contents line; the definition with the body is the full chapter.">
            <rect x="36" y="40" width="284" height="210" rx="8" fill="#fffdf7" stroke="#c9b98f" stroke-width="2"/><rect x="320" y="40" width="284" height="210" rx="8" fill="#fffdf7" stroke="#c9b98f" stroke-width="2"/><line x1="320" y1="40" x2="320" y2="250" stroke="#a8956a" stroke-width="3"/><text x="178" y="74" font-size="14" fill="#8a6a2b" font-weight="800" text-anchor="middle" letter-spacing="2">CONTENTS</text><text x="56" y="112" font-size="15" fill="#5b6b86">1. main</text><text x="300" y="112" font-size="14" fill="#5b6b86" text-anchor="end">p. 2</text><line x1="118" y1="108" x2="262" y2="108" stroke="#c9b98f" stroke-width="1.5" stroke-dasharray="2,4"/><rect x="48" y="128" width="262" height="32" rx="6" fill="#ffe3ec"/><text x="56" y="150" font-size="15" fill="#9c1f4c" font-weight="800">2. addParts(a, b)</text><text x="300" y="150" font-size="14" fill="#9c1f4c" font-weight="800" text-anchor="end">p. 40</text><line x1="200" y1="146" x2="254" y2="146" stroke="#e8457c" stroke-width="1.5" stroke-dasharray="2,4"/><text x="178" y="196" font-size="13" fill="#9c1f4c" text-anchor="middle" font-style="italic">a promise: this chapter</text><text x="178" y="214" font-size="13" fill="#9c1f4c" text-anchor="middle" font-style="italic">is coming later</text><text x="586" y="70" font-size="12" fill="#8a6a2b" text-anchor="end" font-style="italic">page 40</text><text x="340" y="106" font-size="15" fill="#14306b" font-weight="800">Chapter 2: addParts</text><rect x="340" y="124" width="240" height="7" rx="3" fill="#e7e0cc"/><rect x="340" y="142" width="220" height="7" rx="3" fill="#e7e0cc"/><rect x="340" y="160" width="236" height="7" rx="3" fill="#e7e0cc"/><rect x="340" y="178" width="180" height="7" rx="3" fill="#e7e0cc"/><rect x="340" y="196" width="228" height="7" rx="3" fill="#e7e0cc"/><rect x="340" y="214" width="150" height="7" rx="3" fill="#e7e0cc"/><path d="M310 144 C340 144 330 100 336 100" fill="none" stroke="#e8457c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><polygon points="338,101 328.1,96.7 330.2,108.5" fill="#e8457c"/><text x="178" y="280" font-size="13" fill="#9c1f4c" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">int addParts(int a, int b);</text><text x="178" y="300" font-size="12" fill="#5b6b86" text-anchor="middle">prototype · the contents line</text><text x="462" y="280" font-size="13" fill="#14306b" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">int addParts(int a, int b) { … }</text><text x="462" y="300" font-size="12" fill="#5b6b86" text-anchor="middle">definition · the full chapter</text>
        </svg>
    </figure>

    <p class="body-text">
        Why bother? It lets <code>main</code> sit near the top of the file, where it reads like a contents page, while the long function bodies wait further down.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why can <code>main</code> call <code>addParts</code> before its full body appears?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. A prototype above main promised it ✓</li>
            <li class="quiz-option">B. C reads files from the bottom up</li>
            <li class="quiz-option">C. main can call any name at all</li>
            <li class="quiz-option">D. The body gets copied into main</li>
        </ul>
        <p class="quiz-explanation"><em>The prototype is the contents line. It tells the compiler the function exists and how to call it, so the full body can come later.</em></p>
    </div>

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

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">Where is the real definition of <code>addParts</code> in the code example?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Above <code>main</code></li>
            <li class="quiz-option">B. Inside <code>main</code></li>
            <li class="quiz-option quiz-correct">C. Below <code>main</code> ✓</li>
            <li class="quiz-option">D. There isn't one</li>
        </ul>
        <p class="quiz-explanation"><em>Only the prototype is above <code>main</code>. The full definition, with its body, comes after it.</em></p>
    </div>

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
            <li class="quiz-option">A. A backup copy of the whole function</li>
            <li class="quiz-option">B. A comment saying what a function does</li>
            <li class="quiz-option quiz-correct">C. A function's first line, with no body ✓</li>
            <li class="quiz-option">D. The very first call to a function</li>
        </ul>
        <p class="quiz-explanation"><em>A prototype is just the function's signature — return type, name, and parameter types — ending in a semicolon instead of a body.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does a prototype let you do that you couldn't do otherwise?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Call a function above its full definition ✓</li>
            <li class="quiz-option">B. Skip writing the function's real body</li>
            <li class="quiz-option">C. Run a function without ever calling it</li>
            <li class="quiz-option">D. Give two functions the very same name</li>
        </ul>
        <p class="quiz-explanation"><em>The prototype's promise is enough for the compiler to accept a call — the real definition just has to show up somewhere later in the file.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What ends a function prototype, instead of a body in curly braces?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A colon</li>
            <li class="quiz-option quiz-correct">B. A semicolon ✓</li>
            <li class="quiz-option">C. A period</li>
            <li class="quiz-option">D. A comma</li>
        </ul>
        <p class="quiz-explanation"><em>A prototype is one line ending in a semicolon — no { } body at all, unlike the real definition.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. If a function's real definition returns a different type than its prototype promised, what happens?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. C quietly fixes the mismatch for you</li>
            <li class="quiz-option">B. The prototype wins, and the body is ignored</li>
            <li class="quiz-option quiz-correct">C. The compiler flags it as an error ✓</li>
            <li class="quiz-option">D. Nothing, since prototypes are just comments</li>
        </ul>
        <p class="quiz-explanation"><em>The compiler holds the real definition to the prototype's promise — a mismatched return type or parameter list is flagged as an error.</em></p>
    </div>

</div>
