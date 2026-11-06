{{-- M4 · Lesson 4.3: To the Workshop and Back — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Structures — structures as function parameters (by value and by pointer). --}}

<div class="lesson-fragment" data-module="m4" data-lesson="03">

    <h1 class="lesson-heading">Lesson 4.3: To the Workshop and Back</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can pass a structure to a function by value to read it, pass its address to change it, and use the arrow operator <code>-&gt;</code>.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A structure can be passed to a function like any other variable. Passed normally, the function gets a <strong>copy</strong> (Lesson 1.2 again). That is fine for jobs that only <em>read</em> the card:
    </p>

    <pre class="code-block"><code>void show(struct Machine m) {
    printf("%s: %d\n", m.name, m.power);
}</code></pre>

    <p class="body-text">
        To <em>change</em> the caller's card, pass its address and take a pointer (Lesson 2.3 again):
    </p>

    <pre class="code-block"><code>void repair(struct Machine *m) {
    m-&gt;power = 100;
    m-&gt;working = 1;
}

repair(&amp;clock);</code></pre>

    <p class="body-text">
        The <strong>arrow operator</strong> <code>-&gt;</code> reaches a member through a pointer. <code>m-&gt;power</code> means exactly the same as <code>(*m).power</code>: go to the address, then pick the member. Everyone uses the arrow because it is easier to read.
    </p>

    <p class="body-text">
        Passing the address has a second advantage: a big struct is not copied at all, only its address, which is small. So a pointer is also the usual choice for large structs, even when the function only reads.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        The third card is the <strong>clock tower</strong>: power 20, broken. The tower's clock face is stuck at twenty past four.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> sets up two jobs on the registry console. "The display job just reads a card, so it can have a copy. The workshop job has to fix the real one."
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "So the workshop gets the address."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "And a new arrow for it! When you've got a card by its address, you don't use a dot, you use <code>-&gt;</code>. It literally looks like it's pointing. I love it."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="show gets a copy of the clock card and only reads it; repair gets the address of clock and changes the real card through the arrow">
            <rect x="220" y="70" width="200" height="78" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="320" y="97" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">clock</text>
            <text x="320" y="114" text-anchor="middle" font-size="12" fill="#7a5a00" font-family="ui-monospace, monospace">power: 20 to 100</text>
            <text x="320" y="131" text-anchor="middle" font-size="12" fill="#7a5a00" font-family="ui-monospace, monospace">working: 0 to 1</text>
            <rect x="20" y="20" width="160" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="100" y="46.5" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">show(clock)</text>
            <text x="100" y="63.5" text-anchor="middle" font-size="12" fill="#14306b">gets a copy</text>
            <rect x="460" y="20" width="160" height="60" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="540" y="46.5" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">repair(&amp;clock)</text>
            <text x="540" y="63.5" text-anchor="middle" font-size="12" fill="#4a35b8">gets the address</text>
            <line x1="180" y1="60" x2="208.3" y2="83.6" stroke="#2f6fe0" stroke-width="2.5" stroke-dasharray="4,3"/>
            <polygon points="216,90 210.6,77.7 202.9,86.9" fill="#2f6fe0"/>
            <line x1="456" y1="60" x2="431.3" y2="83.2" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="424,90 436.9,86.2 428.7,77.4" fill="#7c5cff"/>
            <text x="540" y="110" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8" font-family="ui-monospace, monospace">m-&gt;power = 100;</text>
            <text x="320" y="195" text-anchor="middle" font-size="13" fill="#475569">m-&gt;power is the same as (*m).power</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

struct Pump {
    int flow;
};

void boost(struct Pump *p, int extra) {
    p-&gt;flow = p-&gt;flow + extra;
}

int main() {
    struct Pump harbor;
    harbor.flow = 10;
    boost(&amp;harbor, 15);
    printf("Flow: %d\n", harbor.flow);    // Flow: 25
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. Write <code>void show(struct Machine m)</code> that prints <code>&lt;name&gt;: &lt;power&gt;</code>, and <code>void repair(struct Machine *m)</code> that sets power to 100 and working to 1 with <code>-&gt;</code>. In <code>main</code>: show the clock, repair it, show it again. Exactly:
    </p>

    <pre class="code-block"><code>Clock: 20
Clock: 100</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="To the Workshop and Back">
            <input type="hidden" name="filename"     value="workshop.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

struct Machine {
    char name[20];
    int power;
    int working;     // 1 working, 0 broken
};

// define void show(struct Machine m): print &quot;&lt;name&gt;: &lt;power&gt;&quot;

// define void repair(struct Machine *m): power 100, working 1, using -&gt;

int main() {
    struct Machine clock;
    strcpy(clock.name, &quot;Clock&quot;);
    clock.power = 20;
    clock.working = 0;

    // show the clock, repair it (pass its address), show it again

    return 0;
}
">
            <input type="hidden" name="instructions" value="Send the clock tower's card to the workshop.

1. void show(struct Machine m) prints: &lt;name&gt;: &lt;power&gt;
2. void repair(struct Machine *m) sets m-&gt;power = 100 and m-&gt;working = 1
3. In main: show(clock); repair(&amp;clock); show(clock);

The output must be exactly:
Clock: 20
Clock: 100">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="show takes a copy, so inside it you use dots: m.name, m.power. repair takes an address, so inside it you use arrows: m-&gt;power = 100; m-&gt;working = 1; The &amp; in repair(&amp;clock) is what lets the change stick.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

struct Machine {
    char name[20];
    int power;
    int working;     // 1 working, 0 broken
};

void show(struct Machine m) {
    printf(&quot;%s: %d\n&quot;, m.name, m.power);
}

void repair(struct Machine *m) {
    m-&gt;power = 100;
    m-&gt;working = 1;
}

int main() {
    struct Machine clock;
    strcpy(clock.name, &quot;Clock&quot;);
    clock.power = 20;
    clock.working = 0;

    show(clock);
    repair(&amp;clock);
    show(clock);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm4', 'lesson' => 'lesson03']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. When a struct is passed to a function normally (not by pointer), what does the function get?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The original struct</li>
            <li class="quiz-option">B. Only the first member</li>
            <li class="quiz-option">C. The struct's address</li>
            <li class="quiz-option quiz-correct">D. A copy of the struct ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Like every other argument in C, a struct passed by value arrives as a copy.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does <code>m-&gt;power</code> mean when <code>m</code> is a pointer to a struct?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The power member m points at ✓</li>
            <li class="quiz-option">B. The value of m minus power</li>
            <li class="quiz-option">C. The address of the power member</li>
            <li class="quiz-option">D. Whether m is greater than power</li>
        </ul>
        <p class="quiz-explanation"><em>The arrow follows the pointer to the struct and picks the member, the same as (*m).power.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which is the same as <code>m-&gt;working</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>*m.working</code></li>
            <li class="quiz-option">B. <code>(&amp;m).working</code></li>
            <li class="quiz-option quiz-correct">C. <code>(*m).working</code> ✓</li>
            <li class="quiz-option">D. <code>*(m.working)</code></li>
        </ul>
        <p class="quiz-explanation"><em>Dereference first, then use the dot; the brackets matter because . binds tighter than *.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. How do you call <code>void repair(struct Machine *m)</code> on <code>clock</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>repair(clock);</code></li>
            <li class="quiz-option quiz-correct">B. <code>repair(&amp;clock);</code> ✓</li>
            <li class="quiz-option">C. <code>repair(*clock);</code></li>
            <li class="quiz-option">D. <code>repair(clock-&gt;);</code></li>
        </ul>
        <p class="quiz-explanation"><em>The parameter is a pointer, so pass the struct's address with &.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why is passing a large struct by pointer often better, even when the function only reads it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Pointers are quicker to type out</li>
            <li class="quiz-option">B. C requires it for large structs</li>
            <li class="quiz-option">C. A copied struct cannot be read</li>
            <li class="quiz-option quiz-correct">D. Only an address gets copied ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Passing by value copies every member; passing the address copies just one small value.</em></p>
    </div>

</div>
