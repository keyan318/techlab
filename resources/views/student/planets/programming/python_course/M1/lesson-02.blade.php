{{-- M1 · Lesson 1.2: Variables & Memory — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by programming.blade.php's fetch() logic.
Exercise data is POSTed to the editor via session flash — no query params. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="02">

    <h1 class="lesson-heading">Lesson 1.2: Variables &amp; Memory</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can create a variable, assign it a value,
        and use it later in the program.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A variable is a labeled box that holds a value so you can use it again
        later instead of retyping it. You create one by writing a name, an equals
        sign, and the value:
    </p>

    <pre class="code-block"><code>hull_status = "stable"</code></pre>

    <p class="body-text">
        After that, writing <code>hull_status</code> anywhere in the program means
        "go look in that box and use what's inside." You never have to retype
        <code>"stable"</code> — just use the name.
    </p>

    <h2 class="section-heading">Astro's Explanation</h2>

    <p class="body-text">
        🤖 Astro has hundreds of readings coming in — hull integrity, oxygen levels,
        fuel — and no way to remember any of them between checks.
    </p>

    <p class="body-text">
        A variable is a labeled storage bin on the ship. You put a value in it
        once, slap a name on the bin, and Astro can check that bin any time
        without re-measuring from scratch. Change the reading? Just update the bin.
        Every part of the program that reads from that bin automatically sees
        the new value. 📦
    </p>

    <h2 class="section-heading">Code Example</h2>

    <p class="body-text">
        Here, two variables are created and then printed. Python goes to each
        labeled bin, takes out what's inside, and displays it.
    </p>

    <pre class="code-block"><code>hull_status = "stable"
oxygen_level = 87
print(hull_status)
print(oxygen_level)</code></pre>

    <p class="body-text">Output:</p>

    <pre class="code-block"><code>stable
87</code></pre>

    <p class="body-text">
        Notice that <code>hull_status</code> holds text (a string) while
        <code>oxygen_level</code> holds a number. Variables can store
        different kinds of values — Python figures out the type automatically.
    </p>

    {{-- ── Code it yourself — POST form, data goes via session flash ── --}}
    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'programming']) }}">
            @csrf
            <input type="hidden" name="title"        value="Variables &amp; Memory">
            <input type="hidden" name="filename"     value="fuel_gauge.py">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="# label Volt's shield gauge, then print it
">
            <input type="hidden" name="instructions" value="Volt is calibrating the shield generator, but its gauge shows a number with no label. Astro asks you to store the reading in a variable.

Your task: create a variable called shield_power with the value 42, then use print() to display it.

Print exactly:
42">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Creating a variable is just: name = value

The name goes on the left, the value goes on the right. Then pass the variable name into print(). No quotes this time, because it's a variable, not text.">
            <input type="hidden" name="hint_code"    value="shield_power = 42
print(shield_power)">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson02']) }}">

            <button type="submit"
                    class="code-btn">
                @include('components.python-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    {{-- DRAFT quiz — written for review, edit freely. Format: .quiz-correct marks the answer. --}}
    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which line creates a variable named <code>fuel_percent</code> that holds the number 42?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. <code>fuel_percent = 42</code> ✓</li>
            <li class="quiz-option">B. <code>42 = fuel_percent</code></li>
            <li class="quiz-option">C. <code>print fuel_percent 42</code></li>
            <li class="quiz-option">D. <code>variable fuel_percent 42</code></li>
        </ul>
        <p class="quiz-explanation"><em>A variable is created by writing the name first, then an equals sign, then the value. The name always goes on the left.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. A program runs <code>hull_status = "stable"</code>, then <code>hull_status = "damaged"</code>, then <code>print(hull_status)</code>. What is printed?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. stable</li>
            <li class="quiz-option">B. stable damaged</li>
            <li class="quiz-option quiz-correct">C. damaged ✓</li>
            <li class="quiz-option">D. An error, because the name was used twice</li>
        </ul>
        <p class="quiz-explanation"><em>Assigning a new value replaces the old one. The bin is updated, so print() reads the latest value: damaged.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which sentence best describes a variable?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A value that can never change</li>
            <li class="quiz-option quiz-correct">B. A labeled box that holds a value so you can use it later ✓</li>
            <li class="quiz-option">C. A command that displays text on the screen</li>
            <li class="quiz-option">D. A note that Python ignores</li>
        </ul>
        <p class="quiz-explanation"><em>A variable is a name attached to a value. Use the name anywhere and Python looks in the box for whatever is inside.</em></p>
    </div>

</div>