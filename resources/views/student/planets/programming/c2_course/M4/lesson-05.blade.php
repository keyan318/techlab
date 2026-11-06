{{-- M4 · Lesson 4.5: Shorter Names — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Structures — type definitions (typedef). --}}

<div class="lesson-fragment" data-module="m4" data-lesson="05">

    <h1 class="lesson-heading">Lesson 4.5: Shorter Names</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can use <code>typedef</code> to give a type a shorter name, define a structure type with <code>typedef struct</code>, and use it in variables, arrays and functions.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Writing <code>struct Machine</code> every single time gets long. <code>typedef</code> gives an existing type a new, shorter name:
    </p>

    <pre class="code-block"><code>typedef int Watts;      // Watts now means int
Watts lampPower = 60;</code></pre>

    <p class="body-text">
        The most common use is naming a structure, so you never have to write the word <code>struct</code> again:
    </p>

    <pre class="code-block"><code>typedef struct {
    char name[20];
    int bells;
} Tower;

Tower clock;            // no "struct" needed</code></pre>

    <p class="body-text">
        The new name comes <em>after</em> the closing brace. Now <code>Tower</code> works everywhere a type does: in variables, arrays (<code>Tower towers[3];</code>), parameters (<code>Tower list[]</code>), and pointers (<code>Tower *t</code>).
    </p>

    <p class="body-text">
        You can also keep a struct name as well: <code>typedef struct Node { ... } Node;</code>. That version matters when the structure has to mention itself, as you will see in Module 5.
    </p>

    <p class="body-text">
        <code>typedef</code> does not create anything new in memory. It only adds a name, so the code reads more like what it means.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        The last drawer in the registry is labelled in Rivet's favourite handwriting: someone, long ago, got tired of writing "struct" on every card and just wrote <strong>TOWER</strong>.
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "They gave the card type a nickname! Same card, shorter name. Honestly, the best thing anyone in this city ever did."
    </p>

    <p class="body-text">
        The city has three bell towers, and Astro wants them ringing again, together, before dark. First he wants to know how many bells that is.
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Three towers. Count every bell in every one. I want to hear them from the Wanderer."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="typedef struct names the card type Tower; an array of three Towers holds 3, 4 and 5 bells, 12 in total">
            <rect x="20" y="25" width="230" height="110" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="135" y="66.5" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">typedef struct { ... } Tower;</text>
            <text x="135" y="84.5" text-anchor="middle" font-size="13" fill="#4a35b8">Tower clock;</text>
            <text x="135" y="102.5" text-anchor="middle" font-size="13" fill="#4a35b8">Tower towers[3];</text>
            <rect x="290" y="25" width="105" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="342.5" y="49.5" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">Clock</text>
            <text x="342.5" y="66.5" text-anchor="middle" font-size="12" fill="#7a5a00" font-family="ui-monospace, monospace">bells: 3</text>
            <rect x="405" y="25" width="105" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="457.5" y="49.5" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">Harbor</text>
            <text x="457.5" y="66.5" text-anchor="middle" font-size="12" fill="#7a5a00" font-family="ui-monospace, monospace">bells: 4</text>
            <rect x="520" y="25" width="105" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="572.5" y="49.5" text-anchor="middle" font-size="14" font-weight="700" fill="#7a5a00">Market</text>
            <text x="572.5" y="66.5" text-anchor="middle" font-size="12" fill="#7a5a00" font-family="ui-monospace, monospace">bells: 5</text>
            <text x="462" y="120" text-anchor="middle" font-size="15" font-weight="700" fill="#14532d">total bells: 12</text>
            <text x="320" y="180" text-anchor="middle" font-size="13" fill="#475569">typedef adds a name; it does not change the struct</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

typedef struct {
    int x;
    int y;
} Point;

void move(Point *p, int dx) {
    p-&gt;x = p-&gt;x + dx;
}

int main() {
    Point tram;
    tram.x = 2;
    tram.y = 5;
    move(&amp;tram, 3);
    printf("Tram at %d,%d\n", tram.x, tram.y);   // Tram at 5,5
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. Using the <code>Tower</code> type, write <code>int totalBells(Tower list[], int n)</code> that returns the sum of every tower's bells. In <code>main</code> (the three towers are set up for you), print each tower, then the total. Exactly:
    </p>

    <pre class="code-block"><code>Clock Tower: 3
Harbor Tower: 4
Market Tower: 5
Total bells: 12</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="Shorter Names">
            <input type="hidden" name="filename"     value="bell_towers.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

typedef struct {
    char name[20];
    int bells;
} Tower;

// define int totalBells(Tower list[], int n): add up every tower's bells

int main() {
    Tower towers[3];
    strcpy(towers[0].name, &quot;Clock Tower&quot;);
    towers[0].bells = 3;
    strcpy(towers[1].name, &quot;Harbor Tower&quot;);
    towers[1].bells = 4;
    strcpy(towers[2].name, &quot;Market Tower&quot;);
    towers[2].bells = 5;

    // print each tower as &quot;&lt;name&gt;: &lt;bells&gt;&quot;, then &quot;Total bells: &quot; and totalBells(towers, 3)

    return 0;
}
">
            <input type="hidden" name="instructions" value="Three bell towers, one total.

1. int totalBells(Tower list[], int n) returns list[0].bells + ... + list[n-1].bells
2. In main, loop over the 3 towers and print &quot;&lt;name&gt;: &lt;bells&gt;&quot;
3. Then print &quot;Total bells: &quot; and totalBells(towers, 3)

The output must be exactly:
Clock Tower: 3
Harbor Tower: 4
Market Tower: 5
Total bells: 12">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="Tower is just a type name now, so the parameter is Tower list[] and each element is list[i]. Inside totalBells, a loop adds list[i].bells into a total. In main, a second loop prints towers[i].name and towers[i].bells.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

typedef struct {
    char name[20];
    int bells;
} Tower;

int totalBells(Tower list[], int n) {
    int total = 0;
    for (int i = 0; i &lt; n; i++) {
        total += list[i].bells;
    }
    return total;
}

int main() {
    Tower towers[3];
    strcpy(towers[0].name, &quot;Clock Tower&quot;);
    towers[0].bells = 3;
    strcpy(towers[1].name, &quot;Harbor Tower&quot;);
    towers[1].bells = 4;
    strcpy(towers[2].name, &quot;Market Tower&quot;);
    towers[2].bells = 5;

    for (int i = 0; i &lt; 3; i++) {
        printf(&quot;%s: %d\n&quot;, towers[i].name, towers[i].bells);
    }
    printf(&quot;Total bells: %d\n&quot;, totalBells(towers, 3));
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm4', 'lesson' => 'lesson05']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <p class="body-text" style="margin-top:2.5rem;">
        At sunset Rivet runs the bell job. The clock tower goes first, then the harbor, then the market, twelve bells rolling across the city, and up on the hill the Wanderer's hull rings back very faintly. Volt hears it. He doesn't say anything. He just nods.
    </p>

    <p class="body-text">
        But the registry has a problem, and Volt is the one who finds it. Tram 7 runs a loop of stops, and the city keeps changing them. A stop reopens at the harbor. A bridge has fallen and its stop must close.
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "This drawer row holds four cards. Not five. Not three. What happens when the tram needs one more stop?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>, slowly: "Then a fixed row isn't good enough. We need memory we can ask for while the program is running. As much as it takes."
    </p>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does <code>typedef</code> do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Creates a new variable of a type</li>
            <li class="quiz-option">B. Copies one struct into another</li>
            <li class="quiz-option quiz-correct">C. Gives a type a second name ✓</li>
            <li class="quiz-option">D. Deletes a type that is unused</li>
        </ul>
        <p class="quiz-explanation"><em>typedef adds another name for a type; nothing new is created in memory.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In <code>typedef struct { ... } Tower;</code>, where does the new name go?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. After the closing brace ✓</li>
            <li class="quiz-option">B. Before the word struct</li>
            <li class="quiz-option">C. Inside the braces, at the top</li>
            <li class="quiz-option">D. Right after the word typedef</li>
        </ul>
        <p class="quiz-explanation"><em>The new type name is written after the closing brace.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. After that typedef, which declaration is correct?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>struct Tower t;</code></li>
            <li class="quiz-option quiz-correct">B. <code>Tower t;</code> ✓</li>
            <li class="quiz-option">C. <code>typedef t;</code></li>
            <li class="quiz-option">D. <code>Tower = t;</code></li>
        </ul>
        <p class="quiz-explanation"><em>Tower is now a type name by itself, so you declare variables with it directly.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. After <code>typedef int Watts;</code>, what type is <code>Watts lamp;</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A new number type</li>
            <li class="quiz-option">B. A struct</li>
            <li class="quiz-option">C. A pointer to int</li>
            <li class="quiz-option quiz-correct">D. An int ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Watts is only another name for int, so lamp is an ordinary int.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why might you write <code>typedef struct Node { ... } Node;</code>, keeping the struct name too?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. C requires it for every typedef</li>
            <li class="quiz-option">B. It makes the struct faster to use</li>
            <li class="quiz-option quiz-correct">C. So the struct can refer to itself ✓</li>
            <li class="quiz-option">D. So printf can print the struct</li>
        </ul>
        <p class="quiz-explanation"><em>Inside the braces the typedef name does not exist yet, so a struct that refers to itself uses struct Node.</em></p>
    </div>

</div>
