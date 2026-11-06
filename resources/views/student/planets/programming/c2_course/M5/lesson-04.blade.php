{{-- M5 · Lesson 5.4: A New Stop at the Harbor — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Linked Lists — creating nodes dynamically; operations: insert at the front and at the end. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="04">

    <h1 class="lesson-heading">Lesson 5.4: A New Stop at the Harbor</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can create list nodes with <code>malloc</code>, insert a node at the front of a list, and add one at the end by walking to the last node.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Real lists grow while the program runs, so their nodes come from <code>malloc</code>. A small function that makes one ready-to-link node keeps that tidy:
    </p>

    <pre class="code-block"><code>Stop *newStop(char *name) {
    Stop *s = (Stop *) malloc(sizeof(Stop));
    if (s == NULL) {
        return NULL;
    }
    strcpy(s-&gt;name, name);
    s-&gt;next = NULL;
    return s;
}</code></pre>

    <p class="body-text">
        <strong>Inserting at the front</strong> takes two steps, in this order. The new node points at the old first node, and then the head moves to the new node:
    </p>

    <pre class="code-block"><code>harbor-&gt;next = head;
head = harbor;</code></pre>

    <p class="body-text">
        Do it the other way round and you lose the rest of the list: the head would point at the new node, and nothing would point at the old first node any more.
    </p>

    <p class="body-text">
        <strong>Adding at the end</strong> means walking to the last node (the one whose <code>next</code> is <code>NULL</code>) and pointing it at the new node. If the list is empty, the new node simply becomes the head:
    </p>

    <pre class="code-block"><code>Stop *addEnd(Stop *head, Stop *stop) {
    if (head == NULL) {
        return stop;
    }
    Stop *p = head;
    while (p-&gt;next != NULL) {
        p = p-&gt;next;
    }
    p-&gt;next = stop;
    return head;
}</code></pre>

    <p class="body-text">
        Neither operation shifts anything in memory. That is the big advantage over an array: a new stop is two pointer changes, however long the line is.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        The harbor stop has reopened: Rivet and Volt cleared the fallen crates off the platform themselves. And the park at the far end has a stop again too.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Harbor goes on the front of the line, before Station. Park goes on the end. Today."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> is drawing arrows on the depot window with his finger. "The front is easy! New sign points at Station, then we call the new stop the start. The end, you walk to the last sign and write the new stop on it."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "In that order. Move the start first and nothing points at Station any more. You lose the whole line."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="Harbor is inserted at the front by pointing it at Station then moving head; Park is added at the end after Market">
            <text x="30" y="28" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8" font-family="ui-monospace, monospace">head</text>
            <line x1="32" y1="34" x2="37.3" y2="52.4" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="40,62 42.5,48.8 30.9,52.1" fill="#7c5cff"/>
            <rect x="20" y="70" width="100" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="70" y="100" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">Harbor</text>
            <line x1="120" y1="95" x2="142.0" y2="95.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="152,95 140.0,89.0 140.0,101.0" fill="#7c5cff"/>
            <rect x="156" y="70" width="100" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="206" y="100" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">Station</text>
            <line x1="256" y1="95" x2="278.0" y2="95.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="288,95 276.0,89.0 276.0,101.0" fill="#7c5cff"/>
            <rect x="292" y="70" width="100" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="342" y="100" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">Market</text>
            <line x1="392" y1="95" x2="414.0" y2="95.0" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="424,95 412.0,89.0 412.0,101.0" fill="#7c5cff"/>
            <rect x="428" y="70" width="100" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="478" y="100" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">Park</text>
            <line x1="528" y1="95" x2="548.0" y2="95.0" stroke="#b3261e" stroke-width="2.5"/>
            <polygon points="558,95 546.0,89.0 546.0,101.0" fill="#b3261e"/>
            <text x="586" y="100" text-anchor="middle" font-size="13" font-weight="700" fill="#b3261e" font-family="ui-monospace, monospace">NULL</text>
            <text x="70" y="150" text-anchor="middle" font-size="12" font-weight="700" fill="#7a5a00">new at the front</text>
            <text x="478" y="150" text-anchor="middle" font-size="12" font-weight="700" fill="#7a5a00">new at the end</text>
            <text x="320" y="185" text-anchor="middle" font-size="13" fill="#334155" font-family="ui-monospace, monospace">1. harbor-&gt;next = head;   2. head = harbor;</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>// Making a stop and putting it on the front of an empty line
Stop *head = NULL;
Stop *first = newStop("Station");
first-&gt;next = head;     // NULL: nothing after it yet
head = first;</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        <code>newStop</code>, <code>addEnd</code> and <code>printLine</code> are written for you. The line starts as <code>Station → Market</code>. Add <code>Park</code> at the end with <code>addEnd</code>, then put <code>Harbor</code> on the front yourself (two lines), and print the line. Exactly:
    </p>

    <pre class="code-block"><code>Harbor -&gt; Station -&gt; Market -&gt; Park</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="A New Stop at the Harbor">
            <input type="hidden" name="filename"     value="harbor_stop.c">
            <input type="hidden" name="difficulty"   value="Hard">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;
#include &lt;stdlib.h&gt;
#include &lt;string.h&gt;

typedef struct Stop {
    char name[12];
    struct Stop *next;      // the stop after this one, or NULL
} Stop;

Stop *newStop(char *name) {
    Stop *s = (Stop *) malloc(sizeof(Stop));
    if (s == NULL) {
        return NULL;
    }
    strcpy(s-&gt;name, name);
    s-&gt;next = NULL;
    return s;
}

Stop *addEnd(Stop *head, Stop *stop) {
    if (head == NULL) {
        return stop;
    }
    Stop *p = head;
    while (p-&gt;next != NULL) {
        p = p-&gt;next;
    }
    p-&gt;next = stop;
    return head;
}

void printLine(Stop *head) {
    Stop *p = head;
    while (p != NULL) {
        printf(&quot;%s&quot;, p-&gt;name);
        if (p-&gt;next != NULL) {
            printf(&quot; -&gt; &quot;);
        }
        p = p-&gt;next;
    }
    printf(&quot;\n&quot;);
}

int main() {
    Stop *head = NULL;
    head = addEnd(head, newStop(&quot;Station&quot;));
    head = addEnd(head, newStop(&quot;Market&quot;));

    // 1. add a new stop &quot;Park&quot; at the end with addEnd

    // 2. make a new stop &quot;Harbor&quot; and insert it at the FRONT:
    //    its next points at the old head, then head moves to it

    printLine(head);
    return 0;
}
">
            <input type="hidden" name="instructions" value="The harbor stop reopens before Station, and the park stop after Market.

1. head = addEnd(head, newStop(&quot;Park&quot;));
2. Stop *harbor = newStop(&quot;Harbor&quot;);
   harbor-&gt;next = head;
   head = harbor;

The output must be exactly:
Harbor -&gt; Station -&gt; Market -&gt; Park">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="addEnd returns the head, so always catch it: head = addEnd(head, ...); For the front, the order matters: first harbor-&gt;next = head; (point at Station) and only then head = harbor; printLine is already called at the end.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;
#include &lt;stdlib.h&gt;
#include &lt;string.h&gt;

typedef struct Stop {
    char name[12];
    struct Stop *next;      // the stop after this one, or NULL
} Stop;

Stop *newStop(char *name) {
    Stop *s = (Stop *) malloc(sizeof(Stop));
    if (s == NULL) {
        return NULL;
    }
    strcpy(s-&gt;name, name);
    s-&gt;next = NULL;
    return s;
}

Stop *addEnd(Stop *head, Stop *stop) {
    if (head == NULL) {
        return stop;
    }
    Stop *p = head;
    while (p-&gt;next != NULL) {
        p = p-&gt;next;
    }
    p-&gt;next = stop;
    return head;
}

void printLine(Stop *head) {
    Stop *p = head;
    while (p != NULL) {
        printf(&quot;%s&quot;, p-&gt;name);
        if (p-&gt;next != NULL) {
            printf(&quot; -&gt; &quot;);
        }
        p = p-&gt;next;
    }
    printf(&quot;\n&quot;);
}

int main() {
    Stop *head = NULL;
    head = addEnd(head, newStop(&quot;Station&quot;));
    head = addEnd(head, newStop(&quot;Market&quot;));

    head = addEnd(head, newStop(&quot;Park&quot;));

    Stop *harbor = newStop(&quot;Harbor&quot;);
    harbor-&gt;next = head;
    head = harbor;

    printLine(head);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm5', 'lesson' => 'lesson04']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why are linked list nodes usually made with <code>malloc</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The list grows while it runs ✓</li>
            <li class="quiz-option">B. Every struct needs malloc</li>
            <li class="quiz-option">C. It makes the nodes global</li>
            <li class="quiz-option">D. malloc'd nodes are smaller</li>
        </ul>
        <p class="quiz-explanation"><em>A list can gain nodes at run time, so each new node asks the heap for its own room.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. To insert <code>harbor</code> at the front, which order is right?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>head = harbor;</code> then <code>harbor-&gt;next = head;</code></li>
            <li class="quiz-option quiz-correct">B. <code>harbor-&gt;next = head;</code> then <code>head = harbor;</code> ✓</li>
            <li class="quiz-option">C. <code>head = harbor;</code> then <code>harbor-&gt;next = NULL;</code></li>
            <li class="quiz-option">D. <code>harbor-&gt;next = NULL;</code> then <code>head = harbor;</code></li>
        </ul>
        <p class="quiz-explanation"><em>Point the new node at the old first node before moving head, or the rest of the list is lost.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. How does <code>addEnd</code> find the last node?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It jumps there with an index</li>
            <li class="quiz-option">B. It reads it from the head</li>
            <li class="quiz-option">C. It counts the nodes first</li>
            <li class="quiz-option quiz-correct">D. It walks until next is NULL ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The last node is the one with no node after it, so the walk stops where next is NULL.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does <code>addEnd</code> do when the list is empty (head is NULL)?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Returns the new node as head ✓</li>
            <li class="quiz-option">B. Crashes on the NULL head</li>
            <li class="quiz-option">C. Does nothing and returns</li>
            <li class="quiz-option">D. Returns NULL as the head</li>
        </ul>
        <p class="quiz-explanation"><em>With no nodes yet, the new node is the whole list, so it becomes the head.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is a big advantage of inserting into a linked list over an array?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It needs no pointers at all</li>
            <li class="quiz-option">B. It uses less memory per item</li>
            <li class="quiz-option quiz-correct">C. Nothing has to be shifted along ✓</li>
            <li class="quiz-option">D. Each node gets a fixed index</li>
        </ul>
        <p class="quiz-explanation"><em>An array must move every later element to make room; a list just re-links two pointers.</em></p>
    </div>

</div>
