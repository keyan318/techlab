{{-- M1 · Lesson 1.3: Data Types — content fragment. --}}
<div class="lesson-fragment" data-module="m1" data-lesson="03">

    <h1 class="lesson-heading">Lesson 1.3: Data Types</h1>
    <p class="lesson-objective"><strong>Learning Objective:</strong> The student can identify str, int, float, and bool values and convert between compatible types.</p>

    <h2 class="section-heading">Simple Explanation</h2>
    <p class="body-text">Every value in Python has a type that determines what you can do with it. Whole numbers are int, decimal numbers are float, text in quotes is str (short for string), and True/False values are bool. You can check a value's type with <code>type()</code>, and sometimes convert between types using functions like <code>int()</code>, <code>float()</code>, and <code>str()</code>.</p>

    <h2 class="section-heading">Astro Explanation</h2>
    <p class="body-text">Not every reading on Astro's dashboard is the same kind of data. Fuel percentage is a number, the ship's name is text, and whether the airlock is sealed is a plain yes-or-no. Astro has to know which "kind of box" it's dealing with, because you can do math with numbers but not with text — mixing them without converting first is how ship computers short-circuit.</p>

    <h2 class="section-heading">Code Example</h2>
    <pre class="code-block"><code>fuel = 76.5
crew = 4
ship_name = "Wanderer"
airlock_sealed = True
print(type(fuel), type(crew), type(ship_name), type(airlock_sealed))</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>
    <p class="body-text">You're given a string <code>"12"</code>. Convert it to an int using <code>int()</code>, then add 8 to it and print the result.</p>

    {{-- ── Code it yourself — POST form, data goes via session flash ── --}}
    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'programming']) }}">
            @csrf
            <input type="hidden" name="title"        value="Data Types">
            <input type="hidden" name="filename"     value="convert_value.py">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="scan_text = &quot;12&quot;

# convert scan_text to an int, add 8, then print the result
">
            <input type="hidden" name="instructions" value="Volt's scanner sends Astro the alien distance as text: &quot;12&quot;. Astro wants to know the distance after the aliens move 8 units closer, but text and numbers can't be added directly.

Your task: convert scan_text to an int with int(), add 8, then print the result.

Print exactly:
20">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="int() turns a numeric-looking string into a whole number. Wrap the variable in int(...) first, then add 8, and print the whole expression.">
            <input type="hidden" name="hint_code"    value="scan_text = &quot;12&quot;
print(int(scan_text) + 8)">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson03']) }}">

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
        <p class="quiz-prompt">Q1. What type is the value 3.14?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. int</li>
            <li class="quiz-option">B. str</li>
            <li class="quiz-option quiz-correct">C. float ✓</li>
            <li class="quiz-option">D. bool</li>
        </ul>
        <p class="quiz-explanation"><em>Numbers with a decimal point are floats in Python; whole numbers without a decimal point are ints.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What happens if you try to run <code>"5" + 3</code> in Python without converting first?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It returns 8</li>
            <li class="quiz-option">B. It returns "53"</li>
            <li class="quiz-option quiz-correct">C. It raises a TypeError because you can't add a string and an int directly ✓</li>
            <li class="quiz-option">D. It returns 5</li>
        </ul>
        <p class="quiz-explanation"><em>Python won't silently mix a string and a number with + — you'd need int("5") + 3 to get 8.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which function converts the string "42" into the integer 42?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. str("42")</li>
            <li class="quiz-option">B. float("42")</li>
            <li class="quiz-option quiz-correct">C. int("42") ✓</li>
            <li class="quiz-option">D. bool("42")</li>
        </ul>
        <p class="quiz-explanation"><em>int() converts a numeric-looking string (or a float) into an integer type.</em></p>
    </div>

</div>