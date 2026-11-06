{{-- M4 · Lesson 4.1: One Card for One Machine — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Structures — structures vs arrays; defining a structure. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="01">

    <h1 class="lesson-heading">Lesson 4.1: One Card for One Machine</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain how a structure differs from an array, define a <code>struct</code> with members of different types, and set and read its members with the dot operator.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        An array holds many values of the <em>same</em> type. But a real thing is usually described by values of <em>different</em> types: a machine has a name (text), a power level (a number) and a working or broken flag. A <strong>structure</strong>, or <code>struct</code>, groups those different values into one thing.
    </p>

    <pre class="code-block"><code>struct Machine {
    char name[20];
    int power;
    int working;     // 1 working, 0 broken
};</code></pre>

    <p class="body-text">
        That defines a new type called <code>struct Machine</code>, with three <strong>members</strong>. The definition is only the blueprint, so it needs a semicolon after the closing brace. To make an actual machine, declare a variable of that type:
    </p>

    <pre class="code-block"><code>struct Machine fountain;</code></pre>

    <p class="body-text">
        Reach a member with a dot: <code>fountain.power = 40;</code>. A <code>char</code> array member is still a string, so copy text into it with <code>strcpy</code>:
    </p>

    <pre class="code-block"><code>strcpy(fountain.name, "Fountain");
fountain.power = 40;
fountain.working = 0;</code></pre>

    <p class="body-text">
        So: an array is a row of same-type values reached by <em>index</em>; a structure is a bundle of different-type values reached by <em>name</em>.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        City Hall stands at the end of Harbor block, its doors hanging open. Inside, behind the front desk, is a wall of little drawers: the <strong>machine registry</strong>. Every drawer once held a card about one of the city's machines. Most of the drawers are empty now.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> pulls out one of the few cards left and holds it up like a prize. "Look at this. Name. Power. Working or not. Different kinds of thing, all on <em>one card</em>, about <em>one</em> machine."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And a row of numbers can't do that?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "A row of numbers is only numbers! A fountain has a name. The card keeps the name and the numbers together, so you never lose track of which number belongs to which machine."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="An array holds same-type values reached by index; a struct Machine card holds a name, a power level and a working flag reached by name">
            <rect x="20" y="20" width="270" height="50" rx="12" fill="#f1f5f9" stroke="#94a3b8" stroke-width="2"/>
            <text x="155" y="50" text-anchor="middle" font-size="14" font-weight="700" fill="#334155">array: int lamps[3]</text>
            <rect x="20" y="80" width="80" height="44" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="60" y="107.5" text-anchor="middle" font-size="15" font-weight="700" fill="#7a5a00">30</text>
            <rect x="110" y="80" width="80" height="44" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="150" y="107.5" text-anchor="middle" font-size="15" font-weight="700" fill="#7a5a00">55</text>
            <rect x="200" y="80" width="80" height="44" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="240" y="107.5" text-anchor="middle" font-size="15" font-weight="700" fill="#7a5a00">80</text>
            <text x="155" y="150" text-anchor="middle" font-size="12" fill="#475569">same type, reached by index</text>
            <rect x="360" y="20" width="260" height="100" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="490" y="49.5" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">struct Machine fountain</text>
            <text x="490" y="66.5" text-anchor="middle" font-size="12" fill="#4a35b8" font-family="ui-monospace, monospace">name: "Fountain"</text>
            <text x="490" y="83.5" text-anchor="middle" font-size="12" fill="#4a35b8" font-family="ui-monospace, monospace">power: 40</text>
            <text x="490" y="100.5" text-anchor="middle" font-size="12" fill="#4a35b8" font-family="ui-monospace, monospace">working: 0</text>
            <text x="490" y="150" text-anchor="middle" font-size="12" fill="#475569">different types, reached by name</text>
            <text x="320" y="190" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8" font-family="ui-monospace, monospace">fountain.power is 40</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

struct Lamp {
    char street[20];
    int number;
    int brightness;
};

int main() {
    struct Lamp corner;
    strcpy(corner.street, "Gate Road");
    corner.number = 3;
    corner.brightness = 80;

    printf("%s lamp %d: %d\n", corner.street, corner.number, corner.brightness);
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        You're writing this one. Using the <code>struct Machine</code> above, make a variable <code>fountain</code>, give it the name <code>Fountain</code>, power 40 and working 0, then print its card. Print <code>yes</code> or <code>no</code> for working. Exactly:
    </p>

    <pre class="code-block"><code>Machine: Fountain
Power: 40
Working: no</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="One Card for One Machine">
            <input type="hidden" name="filename"     value="machine_card.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

struct Machine {
    char name[20];
    int power;
    int working;     // 1 working, 0 broken
};

int main() {
    // make a struct Machine called fountain

    // fill it in: name &quot;Fountain&quot;, power 40, working 0

    // print &quot;Machine: &quot;, &quot;Power: &quot; and &quot;Working: yes/no&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="Write the first card in the registry.

1. struct Machine fountain;
2. strcpy(fountain.name, &quot;Fountain&quot;); power = 40; working = 0
3. Print the card. For working, print yes if it is 1, no if it is 0.

The output must be exactly:
Machine: Fountain
Power: 40
Working: no">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="Every member is reached with a dot: fountain.power = 40; The name is text, so use strcpy(fountain.name, &quot;Fountain&quot;); instead of =. For the last line, an if/else on fountain.working picks yes or no.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

struct Machine {
    char name[20];
    int power;
    int working;     // 1 working, 0 broken
};

int main() {
    struct Machine fountain;
    strcpy(fountain.name, &quot;Fountain&quot;);
    fountain.power = 40;
    fountain.working = 0;

    printf(&quot;Machine: %s\n&quot;, fountain.name);
    printf(&quot;Power: %d\n&quot;, fountain.power);
    if (fountain.working == 1) {
        printf(&quot;Working: yes\n&quot;);
    } else {
        printf(&quot;Working: no\n&quot;);
    }
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm4', 'lesson' => 'lesson01']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is the main difference between an array and a structure?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A structure holds one type; an array mixes types</li>
            <li class="quiz-option">B. An array is reached by name; a structure by index</li>
            <li class="quiz-option">C. A structure can only hold numbers, never text</li>
            <li class="quiz-option quiz-correct">D. A structure mixes types, each with its own name ✓</li>
        </ul>
        <p class="quiz-explanation"><em>An array is a row of one type; a struct bundles members of different types, each with its own name.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What goes right after the closing brace of a <code>struct</code> definition?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing</li>
            <li class="quiz-option quiz-correct">B. A semicolon ✓</li>
            <li class="quiz-option">C. A comma</li>
            <li class="quiz-option">D. The word end</li>
        </ul>
        <p class="quiz-explanation"><em>A struct definition is a declaration, so it ends with }; like any other.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. How do you set the power member of <code>fountain</code> to 40?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>fountain[power] = 40;</code></li>
            <li class="quiz-option">B. <code>power.fountain = 40;</code></li>
            <li class="quiz-option quiz-correct">C. <code>fountain.power = 40;</code> ✓</li>
            <li class="quiz-option">D. <code>struct.power = 40;</code></li>
        </ul>
        <p class="quiz-explanation"><em>The dot operator reaches a member: variable.member.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why does the name need <code>strcpy(fountain.name, "Fountain");</code> instead of <code>=</code>?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. C can't assign a whole array with = ✓</li>
            <li class="quiz-option">B. strcpy runs faster than = does</li>
            <li class="quiz-option">C. = only works on whole structs</li>
            <li class="quiz-option">D. Struct members can't use = at all</li>
        </ul>
        <p class="quiz-explanation"><em>A char array member is still an array, so text is copied into it with strcpy.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. After <code>struct Machine { ... };</code>, what has been created?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A variable called Machine</li>
            <li class="quiz-option quiz-correct">B. A new type, like a blueprint ✓</li>
            <li class="quiz-option">C. Three new global variables</li>
            <li class="quiz-option">D. An array of machine cards</li>
        </ul>
        <p class="quiz-explanation"><em>The definition only describes the shape; you still declare variables like struct Machine fountain; to get real cards.</em></p>
    </div>

</div>
