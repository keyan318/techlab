{{-- M1 · Lesson 1.4: Labelling the Cargo — content fragment.
Syllabus: CC102 Unit 1 — Core Syntax (datatypes, variables). --}}

<div class="lesson-fragment" data-module="m1" data-lesson="04">

    <h1 class="lesson-heading">Lesson 1.4: Labelling the Cargo</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can declare variables of type <code>int</code>, <code>float</code> and
        <code>char</code>, and print each with the matching format specifier.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>variable</strong> is a labelled box that holds a value while a program runs. In C, every variable
        has a fixed <strong>type</strong>, declared before you can use it — the type decides what kind of value the
        box can hold, and how much space it takes.
    </p>

    <ul class="body-list">
        <li><strong><code>int</code></strong>: a whole number, no decimal point — e.g. <code>4</code>.</li>
        <li><strong><code>float</code></strong>: a number with a decimal point — e.g. <code>76.5</code>.</li>
        <li><strong><code>char</code></strong>: a single character, in single quotes — e.g. <code>'A'</code>.</li>
    </ul>

    <p class="body-text">
        Declare a variable with <code>type name = value;</code>. To print one with <code>printf</code>, its format
        specifier must match its type: <code>%d</code> for <code>int</code>, <code>%f</code> for <code>float</code>,
        <code>%c</code> for <code>char</code>. Get the specifier wrong and <code>printf</code> prints garbage — it
        trusts you to tell it what kind of value is coming.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">In C, what must you do before you can use a variable?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Print it once</li>
            <li class="quiz-option">B. Give it a <code>char</code> value</li>
            <li class="quiz-option">C. Nothing, C works out the type</li>
            <li class="quiz-option quiz-correct">D. Declare it with a fixed type ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Every C variable is declared with its type first, like <code>int crew = 4;</code>. The type decides what the box can hold.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        A variable is a <strong>jar with a label</strong>. The label is the variable's <strong>name</strong>, and what's inside is its <strong>value</strong>.
    </p>

    <p class="body-text">
        In C, the jar's <strong>shape</strong> is its <strong>type</strong>. A cookie jar holds whole cookies (<code>int</code>). A measuring jug holds amounts with a decimal point (<code>float</code>). A tiny jar holds one letter tile (<code>char</code>). You pick the jar before you fill it, and it can't change shape later.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 355" role="img" aria-label="Three labelled jars on a shelf. A cookie jar labelled crew holds 4 cookies: int crew = 4, print with %d. A measuring jug labelled fuel holds 76.5: float fuel = 76.5, print with %f. A small letter jar labelled bay holds the letter A: char bay = A, print with %c.">
            <line x1="20" y1="252" x2="620" y2="252" stroke="#b58b5a" stroke-width="6" stroke-linecap="round"/><text x="130" y="66" font-size="12" fill="#8a4b00" font-weight="800" text-anchor="middle" letter-spacing="0.5">COOKIE JAR · <tspan font-family="ui-monospace, Menlo, Consolas, monospace">int</tspan></text><rect x="60" y="110" width="140" height="139" rx="18" fill="#fff8ee" stroke="#8a4b00" stroke-width="2"/><rect x="70" y="92" width="120" height="20" rx="5" fill="#f08c1a" stroke="#8a4b00" stroke-width="2"/><rect x="85" y="128" width="90" height="28" rx="5" fill="#fff" stroke="#8a4b00" stroke-width="1.5"/><text x="130" y="147" font-size="15" fill="#8a4b00" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">crew</text><circle cx="95" cy="218" r="15" fill="#d9a066" stroke="#8a5a2b" stroke-width="1.5"/><circle cx="90" cy="214" r="2.2" fill="#5b3415"/><circle cx="99" cy="212" r="2.2" fill="#5b3415"/><circle cx="100" cy="222" r="2.2" fill="#5b3415"/><circle cx="92" cy="224" r="2.2" fill="#5b3415"/><circle cx="130" cy="226" r="15" fill="#d9a066" stroke="#8a5a2b" stroke-width="1.5"/><circle cx="125" cy="222" r="2.2" fill="#5b3415"/><circle cx="134" cy="220" r="2.2" fill="#5b3415"/><circle cx="135" cy="230" r="2.2" fill="#5b3415"/><circle cx="127" cy="232" r="2.2" fill="#5b3415"/><circle cx="165" cy="216" r="15" fill="#d9a066" stroke="#8a5a2b" stroke-width="1.5"/><circle cx="160" cy="212" r="2.2" fill="#5b3415"/><circle cx="169" cy="210" r="2.2" fill="#5b3415"/><circle cx="170" cy="220" r="2.2" fill="#5b3415"/><circle cx="162" cy="222" r="2.2" fill="#5b3415"/><circle cx="128" cy="190" r="15" fill="#d9a066" stroke="#8a5a2b" stroke-width="1.5"/><circle cx="123" cy="186" r="2.2" fill="#5b3415"/><circle cx="132" cy="184" r="2.2" fill="#5b3415"/><circle cx="133" cy="194" r="2.2" fill="#5b3415"/><circle cx="125" cy="196" r="2.2" fill="#5b3415"/><text x="320" y="66" font-size="12" fill="#14306b" font-weight="800" text-anchor="middle" letter-spacing="0.5">MEASURING JUG · <tspan font-family="ui-monospace, Menlo, Consolas, monospace">float</tspan></text><path d="M378 132 C412 132 412 200 371 200" fill="none" stroke="#2f6fe0" stroke-width="5"/><path d="M262 110 L378 110 L368 249 L272 249 Z" fill="#f5f9ff" stroke="#2f6fe0" stroke-width="2"/><path d="M265.6 165 L374.4 165 L368 249 L272 249 Z" fill="#cfe2ff"/><path d="M262 110 L378 110 L368 249 L272 249 Z" fill="none" stroke="#2f6fe0" stroke-width="2"/><line x1="270.1" y1="140" x2="284.1" y2="140" stroke="#2f6fe0" stroke-width="1.5"/><line x1="272.2" y1="170" x2="286.2" y2="170" stroke="#2f6fe0" stroke-width="1.5"/><line x1="274.3" y1="200" x2="288.3" y2="200" stroke="#2f6fe0" stroke-width="1.5"/><line x1="276.4" y1="230" x2="290.4" y2="230" stroke="#2f6fe0" stroke-width="1.5"/><rect x="286" y="120" width="68" height="26" rx="5" fill="#fff" stroke="#2f6fe0" stroke-width="1.5"/><text x="320" y="138" font-size="15" fill="#14306b" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">fuel</text><text x="320" y="220" font-size="24" fill="#14306b" font-weight="800" text-anchor="middle">76.5</text><text x="505" y="66" font-size="12" fill="#4a35b8" font-weight="800" text-anchor="middle" letter-spacing="0.5">LETTER JAR · <tspan font-family="ui-monospace, Menlo, Consolas, monospace">char</tspan></text><rect x="470" y="168" width="70" height="81" rx="12" fill="#f8f6ff" stroke="#7c5cff" stroke-width="2"/><rect x="476" y="154" width="58" height="16" rx="4" fill="#7c5cff"/><rect x="482" y="176" width="46" height="22" rx="4" fill="#fff" stroke="#7c5cff" stroke-width="1.5"/><text x="505" y="192" font-size="13" fill="#4a35b8" font-weight="800" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">bay</text><rect x="490" y="208" width="30" height="30" rx="5" fill="#fff3c4" stroke="#c99a00" stroke-width="2"/><text x="505" y="230" font-size="18" fill="#7a5a00" font-weight="800" text-anchor="middle">A</text><text x="130" y="284" font-size="14" fill="#8a4b00" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">int crew = 4;</text><text x="130" y="304" font-size="12" fill="#5b6b86" text-anchor="middle">whole number</text><rect x="75" y="316" width="110" height="30" rx="15.0" fill="#fff0d9" stroke="#f08c1a" stroke-width="1.8"/><text x="130.0" y="335.32" font-size="12" fill="#8a4b00" text-anchor="middle">print with <tspan font-weight="800" font-family="ui-monospace, Menlo, Consolas, monospace">%d</tspan></text><text x="320" y="284" font-size="14" fill="#14306b" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">float fuel = 76.5;</text><text x="320" y="304" font-size="12" fill="#5b6b86" text-anchor="middle">has a decimal point</text><rect x="265" y="316" width="110" height="30" rx="15.0" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="1.8"/><text x="320.0" y="335.32" font-size="12" fill="#14306b" text-anchor="middle">print with <tspan font-weight="800" font-family="ui-monospace, Menlo, Consolas, monospace">%f</tspan></text><text x="505" y="284" font-size="14" fill="#4a35b8" font-weight="700" text-anchor="middle" font-family="ui-monospace, Menlo, Consolas, monospace">char bay = 'A';</text><text x="505" y="304" font-size="12" fill="#5b6b86" text-anchor="middle">one single letter</text><rect x="450" y="316" width="110" height="30" rx="15.0" fill="#f3f0ff" stroke="#7c5cff" stroke-width="1.8"/><text x="505.0" y="335.32" font-size="12" fill="#4a35b8" text-anchor="middle">print with <tspan font-weight="800" font-family="ui-monospace, Menlo, Consolas, monospace">%c</tspan></text>
        </svg>
    </figure>

    <p class="body-text">
        <code>printf</code> needs to be told which kind of jar it's reading: <code>%d</code> for cookies, <code>%f</code> for the jug, <code>%c</code> for the letter. Tell it the wrong one and it's like counting cookies in a jug of juice. You get nonsense.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">You need to store 76.5 litres of fuel. Which jar do you pick?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The cookie jar, int, for whole things</li>
            <li class="quiz-option">B. The letter jar, char, for one letter</li>
            <li class="quiz-option quiz-correct">C. The jug, float, for decimal amounts ✓</li>
            <li class="quiz-option">D. Any jar, C changes the shape for you</li>
        </ul>
        <p class="quiz-explanation"><em>76.5 has a decimal point, so it needs the measuring jug: a <code>float</code>. An <code>int</code> jar would drop the .5.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 190" role="img" aria-label="Three labelled boxes: int crew holds 4, float fuel holds 76.5, char bay holds A">
            <rect x="10" y="40" width="190" height="90" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="105" y="66" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">int crew</text>
            <text x="105" y="86" text-anchor="middle" font-size="22" font-weight="700" fill="#14306b">4</text>
            <text x="105" y="110" text-anchor="middle" font-size="11" fill="#14306b">whole number → %d</text>

            <rect x="225" y="40" width="190" height="90" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="66" text-anchor="middle" font-size="13" font-weight="700" fill="#8a4b00">float fuel</text>
            <text x="320" y="86" text-anchor="middle" font-size="22" font-weight="700" fill="#8a4b00">76.5</text>
            <text x="320" y="110" text-anchor="middle" font-size="11" fill="#8a4b00">decimal → %f</text>

            <rect x="440" y="40" width="190" height="90" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="535" y="66" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">char bay</text>
            <text x="535" y="86" text-anchor="middle" font-size="22" font-weight="700" fill="#4a35b8">'A'</text>
            <text x="535" y="110" text-anchor="middle" font-size="11" fill="#4a35b8">one letter → %c</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int crew = 4;
    float fuel = 76.5;
    char bay = 'A';

    printf("%d\n", crew);
    printf("%.1f\n", fuel);
    printf("%c\n", bay);
    return 0;
}</code></pre>

    <p class="body-text">
        <code>%.1f</code> is <code>%f</code> with a precision — it prints exactly one digit after the decimal point,
        instead of <code>float</code>'s noisy default of six.
    </p>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">Which line in the code example prints the bay letter?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>printf("%d\n", crew);</code></li>
            <li class="quiz-option">B. <code>printf("%.1f\n", fuel);</code></li>
            <li class="quiz-option quiz-correct">C. <code>printf("%c\n", bay);</code> ✓</li>
            <li class="quiz-option">D. <code>char bay = 'A';</code></li>
        </ul>
        <p class="quiz-explanation"><em><code>%c</code> is the specifier for a <code>char</code>, and <code>bay</code> is the <code>char</code> variable. Declaring it does not print it.</em></p>
    </div>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Log the salvage manifest. Declare an <code>int</code> named <code>crew</code> holding <code>4</code>, a
        <code>float</code> named <code>fuel</code> holding <code>76.5</code>, and a <code>char</code> named
        <code>bay</code> holding <code>'A'</code>. Print each on its own line, in that order, labelled exactly:
    </p>

    <pre class="code-block"><code>Crew: 4
Fuel: 76.50
Bay: A</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Labelling the Cargo">
            <input type="hidden" name="filename"     value="manifest.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    // declare crew (int, 4), fuel (float, 76.5), bay (char, 'A')

    // print &quot;Crew: 4&quot;, &quot;Fuel: 76.50&quot;, &quot;Bay: A&quot; — one per line

    return 0;
}
">
            <input type="hidden" name="instructions" value="Declare three variables:
int crew = 4;
float fuel = 76.5;
char bay = 'A';

Then print, each on its own line, exactly:
Crew: 4
Fuel: 76.50
Bay: A

Match the type to the right format specifier: %d for crew, %.2f for fuel (two decimal places), and %c for bay.">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int crew = 4;
    float fuel = 76.5;
    char bay = 'A';

    printf(&quot;Crew: %d\n&quot;, crew);
    printf(&quot;Fuel: %.2f\n&quot;, fuel);
    printf(&quot;Bay: %c\n&quot;, bay);
    return 0;
}">
            <input type="hidden" name="hint_body"    value="%.2f prints a float with exactly two digits after the decimal point — that is what turns 76.5 into 76.50 in the output.">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm1', 'lesson' => 'lesson04']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which type should hold the value 4 (a crew count)?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. int ✓</li>
            <li class="quiz-option">B. float</li>
            <li class="quiz-option">C. char</li>
            <li class="quiz-option">D. string</li>
        </ul>
        <p class="quiz-explanation"><em>A whole number with no decimal point belongs in an int.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which format specifier matches a float?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. %d</li>
            <li class="quiz-option quiz-correct">B. %f ✓</li>
            <li class="quiz-option">C. %c</li>
            <li class="quiz-option">D. %s</li>
        </ul>
        <p class="quiz-explanation"><em>%f is the format specifier for a float (a decimal number).</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. How must a char's value be written in C?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. In double quotes, like "A"</li>
            <li class="quiz-option quiz-correct">B. In single quotes, like 'A' ✓</li>
            <li class="quiz-option">C. With no quotes at all</li>
            <li class="quiz-option">D. In square brackets, like [A]</li>
        </ul>
        <p class="quiz-explanation"><em>A single character literal in C is written in single quotes — double quotes are for text strings, a later topic.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does %.1f print for the value 76.5?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 76</li>
            <li class="quiz-option">B. 76.500000</li>
            <li class="quiz-option quiz-correct">C. 76.5 ✓</li>
            <li class="quiz-option">D. 77</li>
        </ul>
        <p class="quiz-explanation"><em>%.1f prints a float with exactly one digit after the decimal point.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What happens if a variable's format specifier does not match its actual type?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The compiler quietly fixes the type</li>
            <li class="quiz-option quiz-correct">B. printf prints a garbage value ✓</li>
            <li class="quiz-option">C. The program refuses to compile</li>
            <li class="quiz-option">D. printf skips that value entirely</li>
        </ul>
        <p class="quiz-explanation"><em>printf does not check that a specifier matches the variable's real type — a mismatch just produces garbled output.</em></p>
    </div>

</div>
