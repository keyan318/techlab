{{-- M5 · Lesson 5.5: Closing a Stop — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Linked Lists — operations: deleting a node, counting, freeing a whole list. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="05">

    <h1 class="lesson-heading">Lesson 5.5: Closing a Stop</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can count the nodes in a list, remove a node by re-linking around it and freeing it, and free a whole list without leaking memory.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Counting</strong> is a walk with a counter: add 1 at every node until <code>NULL</code>.
    </p>

    <p class="body-text">
        <strong>Removing</strong> a node means making the node <em>before</em> it skip over it, then freeing it:
    </p>

    <pre class="code-block"><code>Stop *gone = p-&gt;next;      // p is the stop before the one to remove
p-&gt;next = gone-&gt;next;      // skip over it
free(gone);                // give its memory back</code></pre>

    <p class="body-text">
        Removing the <em>first</em> node is a special case, because nothing comes before it: the head itself has to move to the second node. A remove function therefore returns the (possibly new) head, just like <code>addEnd</code>.
    </p>

    <p class="body-text">
        <strong>Freeing a whole list</strong> needs care. You cannot <code>free(p)</code> and <em>then</em> read <code>p-&gt;next</code>: that memory is gone. Save the next address first:
    </p>

    <pre class="code-block"><code>while (head != NULL) {
    Stop *next = head-&gt;next;   // save it first
    free(head);
    head = next;
}</code></pre>

    <p class="body-text">
        Every node that was <code>malloc</code>'d must be freed exactly once. Freeing only the head, or forgetting the list entirely, leaks every other node.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        A bridge on the way to the market has come down in the night. The Market stop is cut off, and the tram must not stop there.
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Station's sign has to point past Market, straight to Park. And Market's sign comes down. Properly. I don't want it lying around for some job to read later."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "Re-link around it, then free it. And tonight, when the depot shuts, every stop's memory goes back to the yard. One at a time, and you read where the next one is <em>before</em> you take the current one apart."
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Then run it. The power plant is the last stop on that line, and we need it."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="Market is removed: Station next now points straight to Park, Market is freed; the line counts 2 stops">
            <rect x="20" y="75" width="140" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="90" y="105.5" text-anchor="middle" font-size="15" font-weight="700" fill="#7a5a00">Station</text>
            <rect x="250" y="75" width="140" height="50" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="320" y="97.5" text-anchor="middle" font-size="15" font-weight="700" fill="#7a1a14">Market</text>
            <text x="320" y="113.5" text-anchor="middle" font-size="11" fill="#7a1a14">free(gone)</text>
            <rect x="480" y="75" width="140" height="50" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="550" y="105.5" text-anchor="middle" font-size="15" font-weight="700" fill="#7a5a00">Park</text>
            <path d="M90 75 V45 H550 V61" fill="none" stroke="#1f9d55" stroke-width="2.5"/>
            <polygon points="550,73 544,61 556,61" fill="#1f9d55"/>
            <text x="320" y="37" text-anchor="middle" font-size="13" font-weight="700" fill="#14532d" font-family="ui-monospace, monospace">p-&gt;next = gone-&gt;next;</text>
            <line x1="160" y1="100" x2="236.0" y2="100.0" stroke="#94a3b8" stroke-width="2.5" stroke-dasharray="4,3"/>
            <polygon points="246,100 234.0,94.0 234.0,106.0" fill="#94a3b8"/>
            <line x1="390" y1="100" x2="466.0" y2="100.0" stroke="#94a3b8" stroke-width="2.5" stroke-dasharray="4,3"/>
            <polygon points="476,100 464.0,94.0 464.0,106.0" fill="#94a3b8"/>
            <text x="320" y="160" text-anchor="middle" font-size="13" fill="#475569">Station -&gt; Park, and the closed stop's memory goes back to the heap</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>int countStops(Stop *head) {
    int count = 0;
    Stop *p = head;
    while (p != NULL) {
        count++;
        p = p-&gt;next;
    }
    return count;
}</code></pre>

    <p class="body-text">
        <code>removeStop</code> is in the starter code below: read it. It handles the first-node case, then walks with <code>p</code> until <code>p-&gt;next</code> is the stop to close.
    </p>
    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. The line <code>Station → Market → Park</code> is built for you. Close <code>Market</code> with <code>removeStop</code> and print the line. Write <code>countStops</code> and print its answer. Then free every remaining node with the save-next loop, and print the last line. Exactly:
    </p>

    <pre class="code-block"><code>Station -&gt; Park
Stops: 2
Line cleared.</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Closing a Stop">
            <input type="hidden" name="filename"     value="close_stop.c">
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

Stop *removeStop(Stop *head, char *name) {
    if (head == NULL) {
        return NULL;
    }
    if (strcmp(head-&gt;name, name) == 0) {
        Stop *rest = head-&gt;next;
        free(head);
        return rest;
    }
    Stop *p = head;
    while (p-&gt;next != NULL &amp;&amp; strcmp(p-&gt;next-&gt;name, name) != 0) {
        p = p-&gt;next;
    }
    if (p-&gt;next != NULL) {
        Stop *gone = p-&gt;next;
        p-&gt;next = gone-&gt;next;
        free(gone);
    }
    return head;
}

// define int countStops(Stop *head): walk the list and count the nodes

int main() {
    Stop *head = NULL;
    head = addEnd(head, newStop(&quot;Station&quot;));
    head = addEnd(head, newStop(&quot;Market&quot;));
    head = addEnd(head, newStop(&quot;Park&quot;));

    // 1. close the Market stop with removeStop, then printLine(head)

    // 2. print &quot;Stops: &quot; and countStops(head)

    // 3. free every node: save head-&gt;next, free(head), move on

    printf(&quot;Line cleared.\n&quot;);
    return 0;
}
">
            <input type="hidden" name="instructions" value="The bridge to Market is down. Close the stop, then shut the depot for the night.

1. head = removeStop(head, &quot;Market&quot;); then printLine(head);
2. Write int countStops(Stop *head) and print: Stops: &lt;count&gt;
3. Free the whole line:
   while (head != NULL) { Stop *next = head-&gt;next; free(head); head = next; }

The output must be exactly:
Station -&gt; Park
Stops: 2
Line cleared.">
            <input type="hidden" name="hint_title"   value="Volt's Hint">
            <input type="hidden" name="hint_body"    value="removeStop may change the head, so catch its answer: head = removeStop(head, &quot;Market&quot;); countStops is the walking loop with count++ inside. In the freeing loop, read head-&gt;next into a variable before free(head), never after.">
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

Stop *removeStop(Stop *head, char *name) {
    if (head == NULL) {
        return NULL;
    }
    if (strcmp(head-&gt;name, name) == 0) {
        Stop *rest = head-&gt;next;
        free(head);
        return rest;
    }
    Stop *p = head;
    while (p-&gt;next != NULL &amp;&amp; strcmp(p-&gt;next-&gt;name, name) != 0) {
        p = p-&gt;next;
    }
    if (p-&gt;next != NULL) {
        Stop *gone = p-&gt;next;
        p-&gt;next = gone-&gt;next;
        free(gone);
    }
    return head;
}

int countStops(Stop *head) {
    int count = 0;
    Stop *p = head;
    while (p != NULL) {
        count++;
        p = p-&gt;next;
    }
    return count;
}

int main() {
    Stop *head = NULL;
    head = addEnd(head, newStop(&quot;Station&quot;));
    head = addEnd(head, newStop(&quot;Market&quot;));
    head = addEnd(head, newStop(&quot;Park&quot;));

    head = removeStop(head, &quot;Market&quot;);
    printLine(head);

    printf(&quot;Stops: %d\n&quot;, countStops(head));

    while (head != NULL) {
        Stop *next = head-&gt;next;
        free(head);
        head = next;
    }

    printf(&quot;Line cleared.\n&quot;);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm5', 'lesson' => 'lesson05']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <p class="body-text" style="margin-top:2.5rem;">
        Tram 7 leaves the depot in the morning with Astro, Rivet and Volt on board: Station, then Park, and at the end of the line, where a stop has not been used in years, the <strong>power plant</strong>. Rivet runs a cable from the plant's outlet all the way up the hill. By evening, the Wanderer's cells read full.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "We can fly."
    </p>

    <p class="body-text">
        But when Rivet switches the Dead Console off to swap a cable and back on again, it says what it has said every morning since the crash:
    </p>

    <pre class="code-block"><code>HELLO. WHO ARE YOU?</code></pre>

    <p class="body-text">
        <strong>Volt</strong>: "We're not leaving with a ship that forgets us every time it blinks."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> is already looking at the map. In the very middle of the city is a building with no machines marked inside it at all: the <strong>City Archive</strong>. "It's the one place here that never forgot anything. Everything in it is written down."
    </p>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. To remove the node after <code>p</code>, which line makes the list skip it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>p = p-&gt;next-&gt;next;</code></li>
            <li class="quiz-option">B. <code>p-&gt;next-&gt;next = p;</code></li>
            <li class="quiz-option quiz-correct">C. <code>p-&gt;next = p-&gt;next-&gt;next;</code> ✓</li>
            <li class="quiz-option">D. <code>p-&gt;next = NULL;</code></li>
        </ul>
        <p class="quiz-explanation"><em>Pointing p at the node after the removed one cuts it out of the chain.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why is removing the first node a special case?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The head itself has to move ✓</li>
            <li class="quiz-option">B. The first node can't be freed</li>
            <li class="quiz-option">C. The first node is always NULL</li>
            <li class="quiz-option">D. The first node has no name</li>
        </ul>
        <p class="quiz-explanation"><em>There is no previous node to re-link, so the head moves to the second node instead.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is wrong with <code>free(head); head = head-&gt;next;</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing; that order is fine</li>
            <li class="quiz-option">B. free needs two arguments</li>
            <li class="quiz-option">C. It frees the second node</li>
            <li class="quiz-option quiz-correct">D. It reads memory already freed ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Once freed, the node's memory is not yours; save next before freeing.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. A list of 3 nodes is "freed" with just <code>free(head);</code>. What happens?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. All 3 nodes are freed</li>
            <li class="quiz-option quiz-correct">B. Only 1 is freed; 2 leak ✓</li>
            <li class="quiz-option">C. Only the last is freed</li>
            <li class="quiz-option">D. None of them is freed</li>
        </ul>
        <p class="quiz-explanation"><em>Each malloc'd node must be freed; freeing the head alone leaks the rest.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why does <code>removeStop</code> return a Stop pointer?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. So the caller can print it</li>
            <li class="quiz-option">B. C requires it of every function</li>
            <li class="quiz-option quiz-correct">C. Because the head may change ✓</li>
            <li class="quiz-option">D. So it can count the stops</li>
        </ul>
        <p class="quiz-explanation"><em>If the first node is removed, the head changes, so the function hands back the head to use from then on.</em></p>
    </div>

</div>
