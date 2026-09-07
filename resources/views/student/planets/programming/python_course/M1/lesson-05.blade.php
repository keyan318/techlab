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
    <p class="body-text">Write a program that asks the user for a ship name and a crew count (as text), converts the crew count to an int, and prints a formatted status line combining both.</p>

    @php
        // No 'expected' here on purpose — input() output depends on what
        // the student types, so there's no single fixed right answer to
        // check against. The editor just shows Run, no Check Answer button.
        $editorUrl = route('student.planet.editor', [
            'slug' => 'programming',
            'starter_code' => "ship_name = input(\"Enter ship name: \")\ncrew_text = input(\"Enter crew count: \")\n\n# convert crew_text to an int, then print a combined status line\n",
            'return_to' => route('student.planet.module.lesson', [
                'slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson05',
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