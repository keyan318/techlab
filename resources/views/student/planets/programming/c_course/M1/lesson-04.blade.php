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

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Crates from the wreck are stacked across the Engine Room floor, and nobody can find anything. Astro drops
        the salvage list on the workbench.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Four of us. Seventy-six and a half units of fuel left. All of it goes in bay A.
        I want the console holding those three facts by tonight, not a sheet of paper I keep losing."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> is already typing. "Three facts, three shapes of box. Counting things you can't cut
        in half — that's an <code>int</code>. Something with a decimal point — <code>float</code>. One single
        letter — <code>char</code>."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And if you put the wrong thing in the wrong box?"
    </p>

    <p class="body-text">
        <strong>Rivet</strong>, cheerfully: "Then it prints absolute nonsense and doesn't warn you once. It trusts
        you completely. It's a bit of a problem, honestly."
    </p>

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
            <li class="quiz-option">D. Any of the above works equally well</li>
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
            <li class="quiz-option">A. The compiler automatically fixes it</li>
            <li class="quiz-option quiz-correct">B. printf prints garbage — it trusts the specifier to match the type ✓</li>
            <li class="quiz-option">C. The program refuses to compile</li>
            <li class="quiz-option">D. Nothing — C ignores format specifiers</li>
        </ul>
        <p class="quiz-explanation"><em>printf does not check that a specifier matches the variable's real type — a mismatch just produces garbled output.</em></p>
    </div>

</div>
