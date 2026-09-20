{{-- M1 · Lesson 1.6: Reading Error Messages — content fragment. --}}
<div class="lesson-fragment" data-module="m1" data-lesson="06">

    <h1 class="lesson-heading">Lesson 1.6: Reading Error Messages</h1>
    <p class="lesson-objective"><strong>Learning Objective:</strong> The student can read a Python traceback, identify the line and error type, and explain likely causes for common beginner errors.</p>

    <h2 class="section-heading">Simple Explanation</h2>
    <p class="body-text">When Python hits something it can't run, it stops and prints a traceback — a report showing which line failed and what kind of error it was. The last line is the most useful: it names the error type (like SyntaxError, NameError, or TypeError) and gives a short description. Learning to read that last line first, rather than panicking at the whole wall of text, is the single fastest debugging skill a beginner can build.</p>

    <h2 class="section-heading">Astro Explanation</h2>
    <p class="body-text">When something on the ship breaks, the diagnostic panel doesn't just flash a red light — it tells you exactly which subsystem failed and why. A Python traceback is Astro's diagnostic panel: intimidating at first glance, but it's always pointing straight at the problem if you read the bottom line first instead of the top.</p>

    <h2 class="section-heading">Code Example</h2>
    <pre class="code-block"><code># This code has a bug on purpose
fuel_level = "87"
print(fuel_level + 10)

# Running this raises:
# TypeError: can only concatenate str (not "int") to str</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>
    <p class="body-text">This snippet is broken: it crashes with a TypeError. Run it, read the last line of the traceback, then fix the code so it prints the correct result.</p>

    {{-- ── Code it yourself — POST form, data goes via session flash ── --}}
    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'programming']) }}">
            @csrf
            <input type="hidden" name="title"        value="Reading Error Messages">
            <input type="hidden" name="filename"     value="fuel_level.py">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="shield_level = &quot;87&quot;
print(shield_level + 10)
">
            <input type="hidden" name="instructions" value="Alien scouts are close! Volt's shield boost script should add 10 to the shield level, but it crashes and Astro can't wait.

Your task: click Run, read the LAST line of the error (it names the problem), then fix the code.

Once fixed, print exactly:
97">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="The last line says you can't add a str and an int. shield_level is text (&quot;87&quot;), not a number. Convert it with int() before adding.">
            <input type="hidden" name="hint_code"    value="shield_level = &quot;87&quot;
print(int(shield_level) + 10)">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson06']) }}">

            <button type="submit"
                    class="code-btn">
                @include('components.python-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>


    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In a Python traceback, which line is generally most useful to read first?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The very first line</li>
            <li class="quiz-option quiz-correct">B. The last line, which names the error type and gives a short description ✓</li>
            <li class="quiz-option">C. The middle line, showing the file path</li>
            <li class="quiz-option">D. Tracebacks should be read top to bottom only</li>
        </ul>
        <p class="quiz-explanation"><em>The last line of a traceback states the actual error type and message — the lines above it just show how the program got there.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What kind of error is this: <code>print(hp)</code> when hp was never defined anywhere in the program?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. SyntaxError</li>
            <li class="quiz-option">B. TypeError</li>
            <li class="quiz-option quiz-correct">C. NameError ✓</li>
            <li class="quiz-option">D. IndexError</li>
        </ul>
        <p class="quiz-explanation"><em>A NameError happens when Python encounters a name (usually a variable) it has no record of — typically a typo or a variable used before it was created.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What's the most likely cause of a SyntaxError?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Doing math with two different number types</li>
            <li class="quiz-option">B. Using a variable that doesn't exist</li>
            <li class="quiz-option quiz-correct">C. Broken Python grammar, like a missing colon, unmatched quote, or unmatched parenthesis ✓</li>
            <li class="quiz-option">D. Dividing by zero</li>
        </ul>
        <p class="quiz-explanation"><em>SyntaxError means Python couldn't even parse the code as valid Python — it's a structural/grammar problem, not a logic problem.</em></p>
    </div>

</div>