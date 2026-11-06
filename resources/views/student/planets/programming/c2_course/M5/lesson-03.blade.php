{{-- M5 · Lesson 5.3: Each Stop Knows the Next — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Linked Lists — self-referential structures; linked lists; traversal. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="03">

    <h1 class="lesson-heading">Lesson 5.3: Each Stop Knows the Next</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can define a self-referential structure, link nodes into a list with a head pointer and a <code>NULL</code> end, and walk the list from start to finish.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>self-referential structure</strong> has a member that points at another structure of the same type:
    </p>

    <pre class="code-block"><code>typedef struct Stop {
    char name[12];
    struct Stop *next;      // the stop after this one, or NULL
} Stop;</code></pre>

    <p class="body-text">
        That is why the struct keeps its name (Lesson 4.5): inside the braces, the typedef name <code>Stop</code> does not exist yet, so the member is written <code>struct Stop *next</code>.
    </p>

    <p class="body-text">
        Link such structures together and you have a <strong>linked list</strong>. Each one is a <strong>node</strong>. A pointer called the <strong>head</strong> holds the first node; each node's <code>next</code> holds the node after it; the last node's <code>next</code> is <code>NULL</code>, which marks the end.
    </p>

    <p class="body-text">
        To walk the list, start a pointer at the head and keep following <code>next</code> until you hit <code>NULL</code>:
    </p>

    <pre class="code-block"><code>Stop *p = head;
while (p != NULL) {
    printf("%s\n", p-&gt;name);
    p = p-&gt;next;          // step to the next node
}</code></pre>

    <p class="body-text">
        Unlike an array, the nodes do not have to sit side by side in memory, and there is no index. You can only reach node three by going through nodes one and two.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        Every tram stop has a sign, and every sign says only one thing: where the tram goes next. Station's sign says Market. Market's sign says Park. And Park's sign, Volt finds, says <em>Station</em>.
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "There it is. That's why Tram 7 has been going round for years with nobody on it. The last stop points back at the first. It never reaches an end, because there isn't one."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Ohh. It's a list where every stop knows the next stop. Which is lovely. Unless the last one knows the first."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "So the last sign says nothing. Blank. That's the end of the line, and the tram stops there."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="head points at Station; Station next points at Market; Market next points at Park; Park next is NULL">
            <text x="40" y="30" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8" font-family="ui-monospace, monospace">head</text>
            <line x1="40" y1="36" x2="54.2" y2="55.9" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="60,64 57.9,50.7 48.1,57.7" fill="#7c5cff"/>
            <rect x="40" y="70" width="130" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="105" y="100" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">Station</text>
            <line x1="170" y1="95" x2="206.0" y2="95.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="216,95 204.0,89.0 204.0,101.0" fill="#7c5cff"/>
            <rect x="220" y="70" width="130" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="285" y="100" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">Market</text>
            <line x1="350" y1="95" x2="386.0" y2="95.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="396,95 384.0,89.0 384.0,101.0" fill="#7c5cff"/>
            <rect x="400" y="70" width="130" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="465" y="100" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">Park</text>
            <line x1="530" y1="95" x2="550.0" y2="95.0" stroke="#b3261e" stroke-width="2.5"/>
            <polygon points="560,95 548.0,89.0 548.0,101.0" fill="#b3261e"/>
            <text x="588" y="100" text-anchor="middle" font-size="13" font-weight="700" fill="#b3261e" font-family="ui-monospace, monospace">NULL</text>
            <text x="320" y="155" text-anchor="middle" font-size="13" fill="#475569">Each node holds a name and the address of the next node.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

struct Car {
    int number;
    struct Car *next;
};

int main() {
    struct Car front, back;
    front.number = 1;
    back.number = 2;
    front.next = &amp;back;
    back.next = NULL;

    struct Car *p = &amp;front;
    while (p != NULL) {
        printf("Car %d\n", p-&gt;number);
        p = p-&gt;next;
    }
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. Three stops are made for you. Link them <code>Station</code> → <code>Market</code> → <code>Park</code> → <code>NULL</code> by setting each <code>next</code>. Then walk the list from the head, printing each stop, and print the last line after the loop. Exactly:
    </p>

    <pre class="code-block"><code>Stop: Station
Stop: Market
Stop: Park
End of the line.</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Each Stop Knows the Next">
            <input type="hidden" name="filename"     value="tram_line.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

typedef struct Stop {
    char name[12];
    struct Stop *next;      // the stop after this one, or NULL
} Stop;

int main() {
    Stop a, b, c;
    strcpy(a.name, &quot;Station&quot;);
    strcpy(b.name, &quot;Market&quot;);
    strcpy(c.name, &quot;Park&quot;);

    // link them: a -&gt; b -&gt; c -&gt; NULL

    Stop *head = &amp;a;

    // walk from head until NULL, printing &quot;Stop: &lt;name&gt;&quot;

    printf(&quot;End of the line.\n&quot;);
    return 0;
}
">
            <input type="hidden" name="instructions" value="Fix Tram 7's line so it has an end.

1. a.next = &amp;b;  b.next = &amp;c;  c.next = NULL;
2. Stop *p = head; then while (p != NULL):
   print &quot;Stop: &lt;name&gt;&quot; and move p = p-&gt;next;

The output must be exactly:
Stop: Station
Stop: Market
Stop: Park
End of the line.">
            <input type="hidden" name="hint_title"   value="Volt's Hint">
            <input type="hidden" name="hint_body"    value="a, b and c are real structs, so link them with dots and addresses: a.next = &amp;b; and so on, with c.next = NULL; as the end. p is a pointer, so the loop uses arrows: p-&gt;name and p = p-&gt;next; Forget the NULL, and the loop runs off into nowhere.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

typedef struct Stop {
    char name[12];
    struct Stop *next;      // the stop after this one, or NULL
} Stop;

int main() {
    Stop a, b, c;
    strcpy(a.name, &quot;Station&quot;);
    strcpy(b.name, &quot;Market&quot;);
    strcpy(c.name, &quot;Park&quot;);

    a.next = &amp;b;
    b.next = &amp;c;
    c.next = NULL;

    Stop *head = &amp;a;

    Stop *p = head;
    while (p != NULL) {
        printf(&quot;Stop: %s\n&quot;, p-&gt;name);
        p = p-&gt;next;
    }

    printf(&quot;End of the line.\n&quot;);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm5', 'lesson' => 'lesson03']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What makes a structure self-referential?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. A member points to its own type ✓</li>
            <li class="quiz-option">B. A member is a full copy of itself</li>
            <li class="quiz-option">C. It is declared using typedef</li>
            <li class="quiz-option">D. It is declared as a global</li>
        </ul>
        <p class="quiz-explanation"><em>A member like struct Stop *next points at another Stop, which is what lets nodes link up.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What marks the end of a linked list?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A node with the name "End"</li>
            <li class="quiz-option">B. The head pointer is NULL</li>
            <li class="quiz-option">C. A counter of the list's size</li>
            <li class="quiz-option quiz-correct">D. The last node's next is NULL ✓</li>
        </ul>
        <p class="quiz-explanation"><em>NULL in the last node's next means there is no node after it.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is the head of a linked list?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A pointer to the last node</li>
            <li class="quiz-option">B. The node with the biggest value</li>
            <li class="quiz-option quiz-correct">C. A pointer to the first node ✓</li>
            <li class="quiz-option">D. The next member of each node</li>
        </ul>
        <p class="quiz-explanation"><em>The head is where every walk of the list starts: the address of the first node.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Which line moves <code>p</code> to the next node?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>p++;</code></li>
            <li class="quiz-option quiz-correct">B. <code>p = p-&gt;next;</code> ✓</li>
            <li class="quiz-option">C. <code>p-&gt;next = p;</code></li>
            <li class="quiz-option">D. <code>next = p;</code></li>
        </ul>
        <p class="quiz-explanation"><em>Nodes are not side by side in memory, so you follow the next pointer instead of adding 1.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If the last stop's <code>next</code> pointed back to the first stop, what would the walking loop do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Stop once it reaches the last stop</li>
            <li class="quiz-option">B. Crash on the very first stop</li>
            <li class="quiz-option">C. Print nothing, then end at once</li>
            <li class="quiz-option quiz-correct">D. Go round forever, never meeting NULL ✓</li>
        </ul>
        <p class="quiz-explanation"><em>With no NULL at the end, the list is a circle and while (p != NULL) never ends, just like Tram 7.</em></p>
    </div>

</div>
