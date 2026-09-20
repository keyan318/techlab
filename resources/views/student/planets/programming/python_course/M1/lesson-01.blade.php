{{-- M1 · Lesson 1.1: First Signal — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by programming.blade.php's fetch() logic.
Exercise data is POSTed to the editor via session flash — no query params. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="01">

    <h1 class="lesson-heading">Lesson 1.1: First Signal</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write and run a simple Python program,
        use <code>print()</code> to display information, and explain how Python
        executes your instructions.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A computer cannot guess what you want it to do. You have to give it
        instructions. A Python program is a set of instructions that Python
        follows in order, usually from the first line to the last.
    </p>

    <p class="body-text">
        One of the first commands you will learn is <code>print()</code>.
        It tells Python to display something on the screen.
    </p>

    <p class="body-text">
        For example, if you write <code>print("Hello, Captain!")</code>,
        Python displays:
    </p>

    <pre class="code-block"><code>Hello, Captain!</code></pre>

    <p class="body-text">
        The words inside the quotation marks are called a <strong>string</strong>.
        A string is simply text that Python treats as text instead of as a command.
    </p>

    <h2 class="section-heading">Astro's Explanation</h2>

    <p class="body-text">
        🚀 Astro is waking up after a long journey through space.
        His systems are working, but he needs a way to communicate with the Captain.
    </p>

    <p class="body-text">
        That's where <code>print()</code> comes in.
        Think of <code>print()</code> as Astro's communication button —
        you give Astro a message, and <code>print()</code> puts that message on
        the screen.
    </p>

    <pre class="code-block"><code>print("Astro is online.")</code></pre>

    <p class="body-text">
        Python receives the instruction and displays the message.
        Simple, right? You just sent your first signal. 📡
    </p>

    <h2 class="section-heading">Code Example</h2>

    <p class="body-text">
        You can use more than one <code>print()</code> statement.
        Python will execute them in order, from top to bottom.
    </p>

    <pre class="code-block"><code>print("Astro is online.")
print("Signal strength: nominal")</code></pre>

    <p class="body-text">Output:</p>

    <pre class="code-block"><code>Astro is online.
Signal strength: nominal</code></pre>

    <p class="body-text">
        Notice that each <code>print()</code> creates a new line of output.
        Python reads your instructions from top to bottom — just like reading
        a recipe or a checklist.
    </p>

    {{-- ── Code it yourself — POST form, data goes via session flash ── --}}
    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'programming']) }}">
            @csrf
            <input type="hidden" name="title"        value="First Signal">
            <input type="hidden" name="filename"     value="first_signal.py">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="# Astro's first signal goes here
">
            <input type="hidden" name="instructions" value="Astro crash-landed on Codexia and has no idea if anyone can hear him. Rivet and Volt are still asleep in the wreck.

Your task: use print() to send Astro's first signal.

Print exactly:
Astro to Codexia: comms online.">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="print() is Astro's communication button. Whatever you put inside the parentheses, wrapped in quotes, gets displayed on screen.

Make sure your text matches exactly: spelling, spaces, the colon and the period all count.">
            <input type="hidden" name="hint_code"    value="print(&quot;Astro to Codexia: comms online.&quot;)">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01']) }}">

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
        <p class="quiz-prompt">Q1. What does <code>print("Hello, Captain!")</code> display on the screen?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>print("Hello, Captain!")</code></li>
            <li class="quiz-option">B. <code>"Hello, Captain!"</code> with the quotation marks</li>
            <li class="quiz-option quiz-correct">C. Hello, Captain! ✓</li>
            <li class="quiz-option">D. Nothing — print() needs a variable first</li>
        </ul>
        <p class="quiz-explanation"><em>print() shows the text inside the quotation marks, without the quotes. The quotes only tell Python where the text starts and ends.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In <code>print("Astro is online.")</code>, what is <code>"Astro is online."</code> called?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A variable</li>
            <li class="quiz-option quiz-correct">B. A string ✓</li>
            <li class="quiz-option">C. A function</li>
            <li class="quiz-option">D. A comment</li>
        </ul>
        <p class="quiz-explanation"><em>A string is text that Python treats as text instead of as a command. Quotation marks are what make something a string.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. You write two <code>print()</code> lines, one after the other. In what order does Python run them?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Bottom to top</li>
            <li class="quiz-option">B. Randomly</li>
            <li class="quiz-option">C. Only the last one runs</li>
            <li class="quiz-option quiz-correct">D. Top to bottom, one at a time ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Python reads a program from the first line to the last, in order — just like following a checklist.</em></p>
    </div>

</div>