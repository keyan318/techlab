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
    <p class="body-text">You're shown 3 short broken snippets (a missing quote causing SyntaxError, a misspelled variable causing NameError, and a str+int causing TypeError). Match each traceback's last line to the correct bug.</p>

    @php
        // No 'expected' — the point of this exercise is reading the error,
        // not matching an output string.
        $editorUrl = route('student.planet.editor', [
            'slug' => 'programming',
            'starter_code' => "fuel_level = \"87\"\nprint(fuel_level + 10)\n",
            'return_to' => route('student.planet.module.lesson', [
                'slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson06',
            ]),
        ]);
    @endphp
    <div class="cta-wrap">
        <a href="{{ $editorUrl }}"
           class="btn-code-yourself"
           style="display:inline-block;padding:12px 28px;border-radius:8px;background:#22c98a;color:#0e1230;font-weight:700;text-decoration:none;font-family:'Space Grotesk',sans-serif;">
            🐍 Code it yourself
        </a>
    </div>

    <h2 class="section-heading">Challenge</h2>
    <p class="body-text">Given a 10-line program with two intentional bugs, run it, read both tracebacks, fix both bugs, and get it running cleanly.</p>

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