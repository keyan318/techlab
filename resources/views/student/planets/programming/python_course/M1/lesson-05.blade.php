{{-- M1 · Lesson 1.5: Talking to the Program — content fragment. --}}
<div class="lesson-fragment" data-module="m1" data-lesson="05">

    <h1 class="lesson-heading">Lesson 1.5: Talking to the Program</h1>
    <p class="lesson-objective"><strong>Learning Objective:</strong> The student can use input() to accept text from the user and use it inside a program.</p>

    <h2 class="section-heading">Simple Explanation</h2>
    <p class="body-text"><code>input()</code> pauses the program and waits for the user to type something, then hands that text back as a string. Whatever the user types always comes back as a str, even if it looks like a number — so you'll often need <code>int()</code> or <code>float()</code> right after <code>input()</code> if you plan to do math with it.</p>

    <h2 class="section-heading">Astro Explanation</h2>
    <p class="body-text">Right now Astro can only talk at the Captain, never listen. <code>input()</code> gives Astro ears: the program stops, waits for the Captain to type a command, and only continues once something is entered. Forgetting that input() always hands back text (not a number) is a classic rookie mistake that trips up even seasoned engineers.</p>

    <h2 class="section-heading">Code Example</h2>
    <pre class="code-block"><code>pilot_name = input("Enter your callsign: ")
print("Welcome aboard, " + pilot_name + ".")

age_text = input("Enter your age: ")
age = int(age_text)
print("In 10 years you'll be", age + 10)</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>
    <p class="body-text">Astro stores a ship name and a crew count as text. Convert the crew count to an int, work out how many ration packs are needed (2 per crew member), and print a combined status line.</p>

    {{-- ── Code it yourself — POST form, data goes via session flash ── --}}
    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'programming']) }}">
            @csrf
            <input type="hidden" name="title"        value="Talking to the Program">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="10">
            <input type="hidden" name="starter_code" value="ship_name = &quot;Wanderer&quot;
crew_text = &quot;4&quot;

# convert crew_text to an int, then print the status line
">
            <input type="hidden" name="instructions" value="Astro&#x27;s ship name and crew count are stored as text:
ship_name = &quot;Wanderer&quot;
crew_text = &quot;4&quot;

Convert crew_text to an int. Each crew member needs 2 ration packs. Print one status line combining the ship name and the total packs.

Your output must be exactly:
Wanderer needs 8 ration packs">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="You can&#x27;t multiply or add to text like a number, so convert first with int(crew_text). To glue numbers into a sentence, turn the result back into text with str() — or use an f-string.">
            <input type="hidden" name="hint_code"    value="total = int(crew_text) * 2
print(ship_name + &quot; needs &quot; + str(total) + &quot; ration packs&quot;)">
            <input type="hidden" name="challenge"    value="Build a mini &quot;launch checklist&quot; that asks 3 separate input() questions (fuel amount, crew ready y/n, destination) and prints a final go/no-go style summary using all three answers.">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson05']) }}">

            <button type="submit"
                    style="display:inline-block;padding:12px 28px;border-radius:8px;background:#22c98a;color:#0e1230;font-weight:700;border:none;cursor:pointer;font-family:'Space Grotesk',sans-serif;font-size:1rem;">
                🐍 Code it yourself
            </button>
        </form>
    </div>

    <h2 class="section-heading">Challenge</h2>
    <p class="body-text">Build a mini "launch checklist" that asks 3 separate input() questions (fuel amount, crew ready y/n, destination) and prints a final go/no-go style summary using all three answers.</p>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What type of value does input() always return, no matter what the user types?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. int</li>
            <li class="quiz-option quiz-correct">B. str ✓</li>
            <li class="quiz-option">C. float</li>
            <li class="quiz-option">D. bool</li>
        </ul>
        <p class="quiz-explanation"><em>input() always returns a string — even if the user types digits, you must manually convert it to a number if you need to do math.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why would this line cause an error: <code>age = input("Age: ") + 5</code></p>
        <ul class="quiz-options">
            <li class="quiz-option">A. input() can't take a string argument</li>
            <li class="quiz-option quiz-correct">B. You're adding a string and an int, which Python doesn't allow directly ✓</li>
            <li class="quiz-option">C. You must always store input() results in a variable named age</li>
            <li class="quiz-option">D. There's no error, this works fine</li>
        </ul>
        <p class="quiz-explanation"><em>input() returns a string, and Python won't automatically add a string and an integer — you'd need int(input(...)) + 5.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does the text inside input()'s parentheses do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing, it's optional and ignored</li>
            <li class="quiz-option quiz-correct">B. It's shown to the user as a prompt before they type ✓</li>
            <li class="quiz-option">C. It sets the maximum number of characters allowed</li>
            <li class="quiz-option">D. It's the default answer if the user types nothing</li>
        </ul>
        <p class="quiz-explanation"><em>The string passed to input() is displayed to the user as a prompt, letting them know what to type.</em></p>
    </div>

</div>