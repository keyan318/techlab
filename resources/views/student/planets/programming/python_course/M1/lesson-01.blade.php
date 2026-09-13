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
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="10">
            <input type="hidden" name="expected"     value="Ship systems rebooting...">
            <input type="hidden" name="starter_code" value="">
            <input type="hidden" name="instructions" value="Astro's first signal is broken! The message exists, but Python doesn't know it should display it.

Fix the code so the output is exactly:
Ship systems rebooting...

Use the print() function to make Python display that message.">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="print() is Astro's communication button. Whatever you put inside the parentheses (wrapped in quotes) gets displayed on screen.

Make sure your text matches exactly — spelling, spaces, and the three dots all count.">
            <input type="hidden" name="hint_code"    value='print("Ship systems rebooting...")'>
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01']) }}">

            <button type="submit"
                    style="display:inline-block;padding:12px 28px;border-radius:8px;background:#22c98a;color:#0e1230;font-weight:700;border:none;cursor:pointer;font-family:'Space Grotesk',sans-serif;font-size:1rem;">
                🐍 Code it yourself
            </button>
        </form>
    </div>

</div>