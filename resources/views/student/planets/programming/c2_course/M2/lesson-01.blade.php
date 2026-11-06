{{-- M2 · Lesson 2.1: Every Machine Has an Address — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Pointers — memory addresses; pointer variables and the & operator. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="01">

    <h1 class="lesson-heading">Lesson 2.1: Every Machine Has an Address</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain that every variable lives at a memory address, get that address with <code>&amp;</code>, and store it in a pointer variable.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Your computer's memory is like one very long street of numbered boxes. Every variable you make lives in one of those boxes, and the box's number is its <strong>memory address</strong>.
    </p>

    <p class="body-text">
        The <strong><code>&amp;</code></strong> operator, read "address of", gives you a variable's address. You have used it already: in <code>scanf("%d", &amp;fuel);</code> the <code>&amp;</code> told <code>scanf</code> <em>where</em> to put the number.
    </p>

    <p class="body-text">
        A <strong>pointer</strong> is a variable that holds an address instead of a normal value. You declare one with a <code>*</code> after the type:
    </p>

    <pre class="code-block"><code>int lamp = 7;
int *where = &amp;lamp;    // where holds the address of lamp</code></pre>

    <p class="body-text">
        Read <code>int *where</code> as "where is a pointer to an int". The type matters: an <code>int *</code> points at an <code>int</code>, a <code>double *</code> at a <code>double</code>.
    </p>

    <p class="body-text">
        To use the value at the address, put <code>*</code> in front of the pointer: <code>*where</code> means "the value that lives at the address in <code>where</code>", here 7. You can print an address itself with <code>%p</code>, but the number changes every time the program runs, so programs rarely print it.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        The map on the depot wall has no street names, only numbers. Next to each number is a tiny drawing: a lamp, a fountain, a traffic light.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> has his nose almost on the paper. "Every machine has an address. That's how the city finds things. Not 'the lamp by the bakery'. The lamp at <em>this number</em>."
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "And our console works the same way?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Every computer does! Memory is one long street of numbered boxes. Every variable lives in one. Put an <code>&amp;</code> in front of a variable and C tells you its address. Write that address on a tag and keep the tag, and that's a pointer."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="Memory as numbered boxes: lamp holds 7 at address 1004; the pointer where holds 1004, pointing at lamp">
            <rect x="30" y="20" width="90" height="56" rx="12" fill="#f1f5f9" stroke="#94a3b8" stroke-width="2"/>
            <text x="75" y="54" text-anchor="middle" font-size="16" font-weight="700" fill="#334155">..</text>
            <text x="75" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">1000</text>
            <rect x="130" y="20" width="90" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="175" y="54" text-anchor="middle" font-size="16" font-weight="700" fill="#7a5a00">7</text>
            <text x="175" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">1004</text>
            <rect x="230" y="20" width="90" height="56" rx="12" fill="#f1f5f9" stroke="#94a3b8" stroke-width="2"/>
            <text x="275" y="54" text-anchor="middle" font-size="16" font-weight="700" fill="#334155">..</text>
            <text x="275" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">1008</text>
            <rect x="330" y="20" width="90" height="56" rx="12" fill="#f1f5f9" stroke="#94a3b8" stroke-width="2"/>
            <text x="375" y="54" text-anchor="middle" font-size="16" font-weight="700" fill="#334155">1004</text>
            <text x="375" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">1012</text>
            <rect x="430" y="20" width="90" height="56" rx="12" fill="#f1f5f9" stroke="#94a3b8" stroke-width="2"/>
            <text x="475" y="54" text-anchor="middle" font-size="16" font-weight="700" fill="#334155">..</text>
            <text x="475" y="94" text-anchor="middle" font-size="12" fill="#64748b" font-family="ui-monospace, monospace">1016</text>
            <text x="175" y="14" text-anchor="middle" font-size="13" font-weight="700" fill="#7a5a00">lamp</text>
            <text x="375" y="14" text-anchor="middle" font-size="13" font-weight="700" fill="#334155">where</text>
            <line x1="370" y1="110" x2="200.0" y2="110.0" stroke="#d4a017" stroke-width="2.5"/>
            <polygon points="190,110 202.0,116.0 202.0,104.0" fill="#d4a017"/>
            <text x="280" y="135" text-anchor="middle" font-size="13" font-weight="700" fill="#7a5a00" font-family="ui-monospace, monospace">where = &amp;lamp</text>
            <text x="320" y="185" text-anchor="middle" font-size="13" fill="#475569">*where reads the box at address 1004, so *where is 7</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int fountain = 12;
    int *tag = &amp;fountain;          // tag holds fountain's address

    printf("Value: %d\n", fountain);   // 12
    printf("Via tag: %d\n", *tag);     // 12, read through the address
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. Make <code>int lamp = 7;</code> and a pointer <code>int *where = &amp;lamp;</code>. Print the lamp's value <em>through the pointer</em> with <code>*where</code>. Then, if <code>where == &amp;lamp</code>, print <code>Same place: yes</code>. The output must be exactly:
    </p>

    <pre class="code-block"><code>Lamp reads: 7
Same place: yes</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Every Machine Has an Address">
            <input type="hidden" name="filename"     value="depot_map.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int lamp = 7;
    // make a pointer called where that holds the address of lamp

    // print &quot;Lamp reads: &quot; and the value, read through the pointer

    // if where holds the same address as &amp;lamp, print &quot;Same place: yes&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="The depot map finds machines by address.

1. Make a pointer: int *where = &amp;lamp;
2. Print the value through the pointer (*where):
Lamp reads: 7
3. If where == &amp;lamp, print:
Same place: yes">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="int *where = &amp;lamp; makes the tag. printf(&quot;Lamp reads: %d\n&quot;, *where); reads through it: the * in front means the value at that address. Then if (where == &amp;lamp) { printf(&quot;Same place: yes\n&quot;); } compares two addresses.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int lamp = 7;
    int *where = &amp;lamp;

    printf(&quot;Lamp reads: %d\n&quot;, *where);
    if (where == &amp;lamp) {
        printf(&quot;Same place: yes\n&quot;);
    }
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm2', 'lesson' => 'lesson01']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is a memory address?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The name given to a variable</li>
            <li class="quiz-option">B. The value stored in a variable</li>
            <li class="quiz-option">C. How many bytes a variable uses</li>
            <li class="quiz-option quiz-correct">D. The number of a variable's box ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Memory is a long row of numbered boxes; a variable's address is the number of the box it lives in.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does <code>&amp;lamp</code> give you?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The value of lamp</li>
            <li class="quiz-option quiz-correct">B. The address of lamp ✓</li>
            <li class="quiz-option">C. lamp times 2</li>
            <li class="quiz-option">D. A copy of lamp</li>
        </ul>
        <p class="quiz-explanation"><em>The & operator means "address of", so &lamp is where lamp lives in memory.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is a pointer?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. A variable that holds an address ✓</li>
            <li class="quiz-option">B. A function that prints an address</li>
            <li class="quiz-option">C. Another name for an array</li>
            <li class="quiz-option">D. A loop that walks through memory</li>
        </ul>
        <p class="quiz-explanation"><em>A pointer stores a memory address, so it can point at another variable.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. After <code>int lamp = 7; int *where = &amp;lamp;</code>, what is <code>*where</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The address of where</li>
            <li class="quiz-option">B. 0</li>
            <li class="quiz-option quiz-correct">C. 7 ✓</li>
            <li class="quiz-option">D. An error</li>
        </ul>
        <p class="quiz-explanation"><em>*where reads the value at the address stored in where, which is lamp's box: 7.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why do programs rarely print an address with <code>%p</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. C does not allow it at all</li>
            <li class="quiz-option">B. It always prints the number 0</li>
            <li class="quiz-option quiz-correct">C. It can change on every run ✓</li>
            <li class="quiz-option">D. It deletes the variable after</li>
        </ul>
        <p class="quiz-explanation"><em>Where a variable lands in memory can change from run to run, so the printed number is not something to rely on.</em></p>
    </div>

</div>
