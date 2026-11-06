{{-- M4 · Lesson 4.4: The Registry — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Structures — array of structures. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="04">

    <h1 class="lesson-heading">Lesson 4.4: The Registry</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can store many structures in an array, reach a member with <code>registry[i].name</code>, and loop over the array to report on every one.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        An array can hold structures, just as it holds ints. Each element is a whole card:
    </p>

    <pre class="code-block"><code>struct Machine registry[4];      // four machine cards</code></pre>

    <p class="body-text">
        Put the index first, then the member: <code>registry[2].power</code> is the power of the third machine. Read it from left to right: "in the registry, card 2, its power".
    </p>

    <p class="body-text">
        That means one loop can visit every card and look at any member:
    </p>

    <pre class="code-block"><code>for (int i = 0; i &lt; 4; i++) {
    if (registry[i].working == 0) {
        printf("Broken: %s\n", registry[i].name);
    }
}</code></pre>

    <p class="body-text">
        To pass one card to a function by address, use <code>&amp;registry[i]</code>. To pass the whole registry, pass the array and its size, exactly like any other array: <code>report(registry, 4)</code> with a parameter like <code>struct Machine list[]</code>.
    </p>

    <p class="body-text">
        This array-of-structs pattern is everywhere: a class list of students, a shop's stock, a game's players.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Rivet has found four cards that still match machines that exist: the fountain, Tram 7, the clock tower, and the harbor pump. He slots them back into one row of drawers.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "One drawer row, four cards. Walk the row and tell me which machines are still broken, by name. Then how many are working."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Some of these were repaired by the jobs we already ran."
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Doesn't matter how they got fixed. I want to know what the city looks like right now. Every card. You're running this one."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="An array of four struct Machine cards; the loop reports Fountain and Clock as broken and counts 2 working">
            <rect x="15" y="20" width="145" height="100" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="87.5" y="49.5" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1a14">registry[0]</text>
            <text x="87.5" y="66.5" text-anchor="middle" font-size="12" fill="#7a1a14" font-family="ui-monospace, monospace">Fountain</text>
            <text x="87.5" y="83.5" text-anchor="middle" font-size="12" fill="#7a1a14" font-family="ui-monospace, monospace">power 0</text>
            <text x="87.5" y="100.5" text-anchor="middle" font-size="12" fill="#7a1a14" font-family="ui-monospace, monospace">working 0</text>
            <rect x="172" y="20" width="145" height="100" rx="12" fill="#e7f7ee" stroke="#1f9d55" stroke-width="2"/>
            <text x="244.5" y="49.5" text-anchor="middle" font-size="14" font-weight="700" fill="#14532d">registry[1]</text>
            <text x="244.5" y="66.5" text-anchor="middle" font-size="12" fill="#14532d" font-family="ui-monospace, monospace">Tram</text>
            <text x="244.5" y="83.5" text-anchor="middle" font-size="12" fill="#14532d" font-family="ui-monospace, monospace">power 90</text>
            <text x="244.5" y="100.5" text-anchor="middle" font-size="12" fill="#14532d" font-family="ui-monospace, monospace">working 1</text>
            <rect x="329" y="20" width="145" height="100" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="401.5" y="49.5" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1a14">registry[2]</text>
            <text x="401.5" y="66.5" text-anchor="middle" font-size="12" fill="#7a1a14" font-family="ui-monospace, monospace">Clock</text>
            <text x="401.5" y="83.5" text-anchor="middle" font-size="12" fill="#7a1a14" font-family="ui-monospace, monospace">power 0</text>
            <text x="401.5" y="100.5" text-anchor="middle" font-size="12" fill="#7a1a14" font-family="ui-monospace, monospace">working 0</text>
            <rect x="486" y="20" width="145" height="100" rx="12" fill="#e7f7ee" stroke="#1f9d55" stroke-width="2"/>
            <text x="558.5" y="49.5" text-anchor="middle" font-size="14" font-weight="700" fill="#14532d">registry[3]</text>
            <text x="558.5" y="66.5" text-anchor="middle" font-size="12" fill="#14532d" font-family="ui-monospace, monospace">Pump</text>
            <text x="558.5" y="83.5" text-anchor="middle" font-size="12" fill="#14532d" font-family="ui-monospace, monospace">power 70</text>
            <text x="558.5" y="100.5" text-anchor="middle" font-size="12" fill="#14532d" font-family="ui-monospace, monospace">working 1</text>
            <text x="320" y="150" text-anchor="middle" font-size="13" font-weight="700" fill="#334155" font-family="ui-monospace, monospace">registry[2].name is "Clock"</text>
            <text x="320" y="180" text-anchor="middle" font-size="13" fill="#475569">broken: Fountain, Clock  |  working: 2</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

struct Stop {
    char name[12];
    int riders;
};

int main() {
    struct Stop line[2];
    strcpy(line[0].name, "Station");
    line[0].riders = 5;
    strcpy(line[1].name, "Market");
    line[1].riders = 3;

    for (int i = 0; i &lt; 2; i++) {
        printf("%s: %d\n", line[i].name, line[i].riders);
    }
    return 0;
}</code></pre>

    <p class="body-text">
        The starter code below gives you a small helper, <code>setCard</code>, that fills in one card through its address, so the four cards take four lines instead of sixteen.
    </p>
    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        The registry is set up for you. Loop over all four cards: print <code>Broken: &lt;name&gt;</code> for every machine whose <code>working</code> is 0, and count the ones that work. After the loop, print the count. Exactly:
    </p>

    <pre class="code-block"><code>Broken: Fountain
Broken: Clock
Working: 2</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="The Registry">
            <input type="hidden" name="filename"     value="registry.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

struct Machine {
    char name[20];
    int power;
    int working;     // 1 working, 0 broken
};

void setCard(struct Machine *m, char *name, int power, int working) {
    strcpy(m-&gt;name, name);
    m-&gt;power = power;
    m-&gt;working = working;
}

int main() {
    struct Machine registry[4];
    setCard(&amp;registry[0], &quot;Fountain&quot;, 0, 0);
    setCard(&amp;registry[1], &quot;Tram&quot;, 90, 1);
    setCard(&amp;registry[2], &quot;Clock&quot;, 0, 0);
    setCard(&amp;registry[3], &quot;Pump&quot;, 70, 1);
    int working = 0;

    // loop over the 4 cards: print &quot;Broken: &lt;name&gt;&quot; for broken ones, count the working ones

    printf(&quot;Working: %d\n&quot;, working);
    return 0;
}
">
            <input type="hidden" name="instructions" value="Walk the whole registry: Fountain, Tram, Clock, Pump.

In one for loop (i from 0 while i &lt; 4):
- if registry[i].working == 0, print: Broken: &lt;name&gt;
- otherwise add 1 to working

The output must be exactly:
Broken: Fountain
Broken: Clock
Working: 2">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Index first, then the member: registry[i].working and registry[i].name. One if/else inside the loop: the if prints the broken name with %s, the else does working++. The final printf is already there.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

struct Machine {
    char name[20];
    int power;
    int working;     // 1 working, 0 broken
};

void setCard(struct Machine *m, char *name, int power, int working) {
    strcpy(m-&gt;name, name);
    m-&gt;power = power;
    m-&gt;working = working;
}

int main() {
    struct Machine registry[4];
    setCard(&amp;registry[0], &quot;Fountain&quot;, 0, 0);
    setCard(&amp;registry[1], &quot;Tram&quot;, 90, 1);
    setCard(&amp;registry[2], &quot;Clock&quot;, 0, 0);
    setCard(&amp;registry[3], &quot;Pump&quot;, 70, 1);
    int working = 0;

    for (int i = 0; i &lt; 4; i++) {
        if (registry[i].working == 0) {
            printf(&quot;Broken: %s\n&quot;, registry[i].name);
        } else {
            working++;
        }
    }

    printf(&quot;Working: %d\n&quot;, working);
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm4', 'lesson' => 'lesson04']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is <code>struct Machine registry[4];</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. One Machine struct with 4 members</li>
            <li class="quiz-option">B. Four separate arrays of members</li>
            <li class="quiz-option">C. A pointer to four Machine cards</li>
            <li class="quiz-option quiz-correct">D. An array of four Machine structs ✓</li>
        </ul>
        <p class="quiz-explanation"><em>It is an array whose every element is a whole struct Machine card.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. How do you reach the name of the third card?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. <code>registry[2].name</code> ✓</li>
            <li class="quiz-option">B. <code>registry.name[2]</code></li>
            <li class="quiz-option">C. <code>registry[3].name</code></li>
            <li class="quiz-option">D. <code>name[2].registry</code></li>
        </ul>
        <p class="quiz-explanation"><em>Index first to pick the card, then the dot to pick the member; the third card is index 2.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. How do you pass card 1 to <code>void repair(struct Machine *m)</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>repair(registry);</code></li>
            <li class="quiz-option">B. <code>repair(registry[1]);</code></li>
            <li class="quiz-option quiz-correct">C. <code>repair(&amp;registry[1]);</code> ✓</li>
            <li class="quiz-option">D. <code>repair(*registry[1]);</code></li>
        </ul>
        <p class="quiz-explanation"><em>registry[1] is the card; & gives its address for the pointer parameter.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. In the task's registry, how many machines are working?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 1</li>
            <li class="quiz-option">B. 3</li>
            <li class="quiz-option">C. 4</li>
            <li class="quiz-option quiz-correct">D. 2 ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Tram and Pump have working set to 1; Fountain and Clock are broken.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Which parameter receives a whole array of machines?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>struct Machine list</code></li>
            <li class="quiz-option quiz-correct">B. <code>struct Machine list[]</code> ✓</li>
            <li class="quiz-option">C. <code>Machine[] list</code></li>
            <li class="quiz-option">D. <code>struct list[]</code></li>
        </ul>
        <p class="quiz-explanation"><em>An array of structs is passed like any array: the element type, the name, and empty brackets, plus a size.</em></p>
    </div>

</div>
