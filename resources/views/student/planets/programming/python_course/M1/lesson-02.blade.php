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
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="10">
            <input type="hidden" name="starter_code" value="">
            <input type="hidden" name="instructions" value="Astro's fuel gauge is showing a reading, but nobody labeled it!

Create a variable called fuel_percent and assign it the value 42.
Then use print() to display it.

Your output must be exactly:
42">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Creating a variable is just: name = value

The name goes on the left, the value goes on the right. Then pass the variable name into print() — no quotes needed this time, since it's not text, it's a variable.">
            <input type="hidden" name="hint_code"    value="fuel_percent = 42
print(fuel_percent)">
            <input type="hidden" name="challenge"    value="Create three variables that describe a ship system:
• A name (text)
• A number reading
• An online status (True or False)

Then print all three, each on its own line.">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson02']) }}">

            <button type="submit"
                    style="display:inline-block;padding:12px 28px;border-radius:8px;background:#22c98a;color:#0e1230;font-weight:700;border:none;cursor:pointer;font-family:'Space Grotesk',sans-serif;font-size:1rem;">
                🐍 Code it yourself
            </button>
        </form>
    </div>

</div>