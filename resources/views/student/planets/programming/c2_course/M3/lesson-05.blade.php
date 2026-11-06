{{-- M3 · Lesson 3.5: The Bakery Sign — content fragment, no <html>/<head>/<body>.
Season 2 of the Wanderer's story (Season 1 is Computer Programming 1).
"Code it yourself" runs in the PicoC editor; the answer key ('expected') lives in
config/course-structure.php and is checked server-side.
Syllabus: CC103 Arrays — strings. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="05">

    <h1 class="lesson-heading">Lesson 3.5: The Bakery Sign</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain that a C string is a <code>char</code> array ending in <code>'\0'</code>, and use <code>strlen</code>, <code>strcpy</code>, <code>strcat</code> and <code>strcmp</code> from <code>&lt;string.h&gt;</code>.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        C has no separate "text" type. A <strong>string</strong> is an array of <code>char</code>, with one extra character at the end: <code>'\0'</code>, the <strong>null character</strong>. It marks where the text stops.
    </p>

    <pre class="code-block"><code>char sign[20] = "BAK";    // B A K \0 ... room for 20 chars in total</code></pre>

    <p class="body-text">
        The quotes add the <code>'\0'</code> for you. Print a string with <code>%s</code>. Always leave room for the <code>'\0'</code>: the word <code>"BAKERY"</code> has 6 letters but needs 7 <code>char</code>s.
    </p>

    <p class="body-text">
        You cannot join or compare strings with <code>+</code> or <code>==</code>. Use the functions in <code>&lt;string.h&gt;</code>:
    </p>

    <ul class="body-list">
        <li><code>strlen(s)</code>: how many characters, not counting the <code>'\0'</code>.</li>
        <li><code>strcpy(dest, src)</code>: copies <code>src</code> into <code>dest</code>.</li>
        <li><code>strcat(dest, src)</code>: sticks <code>src</code> onto the end of <code>dest</code>.</li>
        <li><code>strcmp(a, b)</code>: returns 0 when the two strings match exactly.</li>
    </ul>

    <p class="body-text">
        The destination must be big enough. <code>strcpy</code> and <code>strcat</code> do not check; if the text does not fit, they write past the end of the array.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Halfway down Lamp Street there is a bakery with a big sign over the door. The sign is a little screen, and all it says now is <code>BAK</code>. The other letters were knocked out years ago.
    </p>

    <p class="body-text">
        <strong>Rivet</strong> finds them in a drawer behind the counter: <code>ERY</code>. "The sign's memory just holds letters in a row, and a stop mark after the last one so it knows where the word ends."
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "And if we stick on more letters than the sign has room for?"
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Then we don't. Join the pieces, check it says what it should, and light it. People should be able to read this street again."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="The char array sign holds B, A, K, E, R, Y and then the null character that ends the string">
            <rect x="25" y="40" width="74" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="62" y="75" text-anchor="middle" font-size="18" font-weight="700" fill="#7a5a00">B</text>
            <rect x="107" y="40" width="74" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="144" y="75" text-anchor="middle" font-size="18" font-weight="700" fill="#7a5a00">A</text>
            <rect x="189" y="40" width="74" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="226" y="75" text-anchor="middle" font-size="18" font-weight="700" fill="#7a5a00">K</text>
            <rect x="271" y="40" width="74" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="308" y="75" text-anchor="middle" font-size="18" font-weight="700" fill="#7a5a00">E</text>
            <rect x="353" y="40" width="74" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="390" y="75" text-anchor="middle" font-size="18" font-weight="700" fill="#7a5a00">R</text>
            <rect x="435" y="40" width="74" height="56" rx="12" fill="#fff8db" stroke="#d4a017" stroke-width="2"/>
            <text x="472" y="75" text-anchor="middle" font-size="18" font-weight="700" fill="#7a5a00">Y</text>
            <rect x="517" y="40" width="74" height="56" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="554" y="75" text-anchor="middle" font-size="18" font-weight="700" fill="#7a1a14">\0</text>
            <text x="62" y="116" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">sign[0]</text>
            <text x="144" y="116" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">sign[1]</text>
            <text x="226" y="116" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">sign[2]</text>
            <text x="308" y="116" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">sign[3]</text>
            <text x="390" y="116" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">sign[4]</text>
            <text x="472" y="116" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">sign[5]</text>
            <text x="554" y="116" text-anchor="middle" font-size="11" fill="#64748b" font-family="ui-monospace, monospace">sign[6]</text>
            <text x="160" y="28" text-anchor="middle" font-size="12" font-weight="700" fill="#7a5a00">was there</text>
            <text x="406" y="28" text-anchor="middle" font-size="12" font-weight="700" fill="#7a5a00">strcat added</text>
            <text x="320" y="155" text-anchor="middle" font-size="13" fill="#475569">strlen(sign) is 6: it stops counting at the \0</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

int main() {
    char street[20];
    strcpy(street, "Lamp");
    strcat(street, " Street");

    printf("%s has %d letters\n", street, (int) strlen(street));   // Lamp Street has 11 letters
    if (strcmp(street, "Lamp Street") == 0) {
        printf("Match\n");
    }
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Start with <code>char sign[20] = "BAK";</code>. Use <code>strcat</code> to add <code>"ERY"</code>, print the sign and its length with <code>strlen</code>, and if <code>strcmp</code> says it now matches <code>"BAKERY"</code>, print <code>Sign fixed.</code> Exactly:
    </p>

    <pre class="code-block"><code>Sign: BAKERY
Letters: 6
Sign fixed.</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-2']) }}">
            @csrf
            <input type="hidden" name="title"        value="The Bakery Sign">
            <input type="hidden" name="filename"     value="bakery_sign.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

int main() {
    char sign[20] = &quot;BAK&quot;;

    // add &quot;ERY&quot; to the end of sign

    // print &quot;Sign: &quot; and the sign, then &quot;Letters: &quot; and its length

    // if sign matches &quot;BAKERY&quot;, print &quot;Sign fixed.&quot;

    return 0;
}
">
            <input type="hidden" name="instructions" value="The bakery sign only says BAK. The missing letters are ERY.

1. strcat(sign, &quot;ERY&quot;);
2. print the sign with %s, and its length with strlen
3. if (strcmp(sign, &quot;BAKERY&quot;) == 0) print Sign fixed.

The output must be exactly:
Sign: BAKERY
Letters: 6
Sign fixed.">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="strcat(sign, &quot;ERY&quot;); joins them. printf(&quot;Sign: %s\n&quot;, sign); prints it. strlen returns a size, so print it as (int) strlen(sign) with %d. strcmp gives 0 for a match, so test == 0, not == 1.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;
#include &lt;string.h&gt;

int main() {
    char sign[20] = &quot;BAK&quot;;

    strcat(sign, &quot;ERY&quot;);
    printf(&quot;Sign: %s\n&quot;, sign);
    printf(&quot;Letters: %d\n&quot;, (int) strlen(sign));
    if (strcmp(sign, &quot;BAKERY&quot;) == 0) {
        printf(&quot;Sign fixed.\n&quot;);
    }
    return 0;
}
">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm3', 'lesson' => 'lesson05']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is a string in C?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A built-in type just for text</li>
            <li class="quiz-option quiz-correct">B. A char array ending in '\0' ✓</li>
            <li class="quiz-option">C. An int array ending in 0</li>
            <li class="quiz-option">D. A pointer to a single number</li>
        </ul>
        <p class="quiz-explanation"><em>C stores text as a char array, and the '\0' at the end marks where the text stops.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. How many chars does <code>"BAKERY"</code> need in memory?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 5</li>
            <li class="quiz-option">B. 6</li>
            <li class="quiz-option quiz-correct">C. 7 ✓</li>
            <li class="quiz-option">D. 20</li>
        </ul>
        <p class="quiz-explanation"><em>Six letters plus one '\0' to end it: 7.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does <code>strlen("BAKERY")</code> return?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 7</li>
            <li class="quiz-option">B. 0</li>
            <li class="quiz-option">C. 20</li>
            <li class="quiz-option quiz-correct">D. 6 ✓</li>
        </ul>
        <p class="quiz-explanation"><em>strlen counts the characters before the '\0', so it returns 6.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does <code>strcmp(a, b)</code> return when the strings match?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. 0 ✓</li>
            <li class="quiz-option">B. 1</li>
            <li class="quiz-option">C. -1</li>
            <li class="quiz-option">D. 6</li>
        </ul>
        <p class="quiz-explanation"><em>strcmp returns 0 for equal strings, so a match is tested with == 0.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why is <code>strcat</code> dangerous on a small array?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It deletes the original text first</li>
            <li class="quiz-option">B. It only works on arrays of numbers</li>
            <li class="quiz-option quiz-correct">C. It can write past the array's end ✓</li>
            <li class="quiz-option">D. It reverses the joined string</li>
        </ul>
        <p class="quiz-explanation"><em>strcat trusts that the destination is big enough; if not, it writes beyond the array into other memory.</em></p>
    </div>

</div>
