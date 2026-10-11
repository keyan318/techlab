{{-- M1 · Lesson 1.2: Drawing the Plan — content fragment.
Syllabus: CC102 Unit 1 — Basic Programming Concepts (flowchart, pseudocode). --}}

<div class="lesson-fragment" data-module="m1" data-lesson="02">

    <h1 class="lesson-heading">Lesson 1.2: Drawing the Plan</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can read a flowchart, write simple pseudocode, and translate a short ordered
        plan into a matching sequence of C statements.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Before writing real code, it helps to plan it in a way that is easy to check for mistakes. Two common ways:
    </p>

    <ul class="body-list">
        <li><strong>Flowchart</strong>: a diagram. Ovals mark start/end, rectangles mark steps, arrows show the order.</li>
        <li><strong>Pseudocode</strong>: the plan written in plain, structured sentences — not real code, but close enough in shape that turning it into C is mostly a direct swap.</li>
    </ul>

    <p class="body-text">
        Both exist for the same reason: it is far cheaper to catch a missing or out-of-order step on paper than after
        it has already caused a real system to misbehave.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        The console can say one line. Now it has to run the Wanderer's restart sequence: power first, then
        coolant, then everything else.
    </p>

    <p class="body-text">
        Rivet reaches for the keyboard. <strong>Volt</strong> puts a hand flat on it.
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Coolant before power and that line blows out through the floor we're standing on.
        Write the order down first. Then type."
    </p>

    <p class="body-text">
        <strong>Rivet</strong>: "I know the order."
    </p>

    <p class="body-text">
        <strong>Volt</strong> doesn't move his hand. "Write it down."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> sighs, drops the keyboard, and picks up a marker instead. "Power. Coolant.
        Systems. Plan first, picture second, code last. Happy?"
    </p>

    <p class="body-text">
        <strong>Volt</strong>: "Getting there."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 300 400" role="img" aria-label="Flowchart: start, print power online, print coolant stable, print systems ready, end">
            <ellipse cx="150" cy="30" rx="70" ry="26" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="150" y="36" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">Start</text>

            <rect x="60" y="90" width="180" height="55" rx="10" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="150" y="113" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">Print</text>
            <text x="150" y="131" text-anchor="middle" font-size="12" fill="#14306b">"Power: online"</text>

            <rect x="60" y="175" width="180" height="55" rx="10" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="150" y="198" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">Print</text>
            <text x="150" y="216" text-anchor="middle" font-size="12" fill="#14306b">"Coolant: stable"</text>

            <rect x="60" y="260" width="180" height="55" rx="10" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="150" y="283" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">Print</text>
            <text x="150" y="301" text-anchor="middle" font-size="12" fill="#14306b">"Systems: ready"</text>

            <ellipse cx="150" cy="365" rx="70" ry="26" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="150" y="371" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">End</text>

            <line x1="150" y1="56" x2="150" y2="86" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="150,90 144,78 156,78" fill="#7c5cff"/>
            <line x1="150" y1="145" x2="150" y2="171" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="150,175 144,163 156,163" fill="#7c5cff"/>
            <line x1="150" y1="230" x2="150" y2="256" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="150,260 144,248 156,248" fill="#7c5cff"/>
            <line x1="150" y1="315" x2="150" y2="335" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="150,339 144,327 156,327" fill="#7c5cff"/>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <p class="body-text">Pseudocode for the flowchart above:</p>

    <pre class="code-block"><code>START
    PRINT "Power: online"
    PRINT "Coolant: stable"
    PRINT "Systems: ready"
END</code></pre>

    <p class="body-text">Turned into C, one pseudocode line becomes one <code>printf</code> call, in the same order:</p>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    printf("Power: online\n");
    printf("Coolant: stable\n");
    printf("Systems: ready\n");
    return 0;
}</code></pre>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Turn the restart-sequence plan into C. Print, in this exact order, each on its own line:
        <code>Power: online</code>, then <code>Coolant: stable</code>, then <code>Systems: ready</code>.
    </p>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Drawing the Plan">
            <input type="hidden" name="filename"     value="restart_sequence.c">
            <input type="hidden" name="difficulty"   value="Easy">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    // Follow the plan: Power, then Coolant, then Systems.

    return 0;
}
">
            <input type="hidden" name="instructions" value="Volt has the order up on the board. Now turn it into code. Print each line in this exact order, each ending in a newline:

Power: online
Coolant: stable
Systems: ready

One printf() call per line, in that order.">
            <input type="hidden" name="hint_title"   value="Rivet's Hint">
            <input type="hidden" name="hint_body"    value="Each pseudocode line becomes its own printf(&quot;...\n&quot;); call. Keep them in the same order as the plan — printf runs top to bottom, exactly the way you wrote it.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    printf(&quot;Power: online\n&quot;);
    printf(&quot;Coolant: stable\n&quot;);
    printf(&quot;Systems: ready\n&quot;);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm1', 'lesson' => 'lesson02']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In a flowchart, what does an oval usually mark?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A loop</li>
            <li class="quiz-option quiz-correct">B. The start or end of the plan ✓</li>
            <li class="quiz-option">C. A variable</li>
            <li class="quiz-option">D. An error</li>
        </ul>
        <p class="quiz-explanation"><em>Ovals mark start and end points; rectangles mark steps, and arrows show the order between them.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is pseudocode?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Code that intentionally does not compile</li>
            <li class="quiz-option quiz-correct">B. A plan written in plain, structured sentences, not real code ✓</li>
            <li class="quiz-option">C. A type of C syntax error</li>
            <li class="quiz-option">D. A flowchart shape</li>
        </ul>
        <p class="quiz-explanation"><em>Pseudocode is a structured plan in plain language — close enough in shape to real code that turning it into C is mostly direct.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why plan with a flowchart or pseudocode before writing real code?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. C requires it to compile</li>
            <li class="quiz-option quiz-correct">B. It is cheaper to catch a missing or out-of-order step on paper than after it causes real problems ✓</li>
            <li class="quiz-option">C. It makes the program run faster</li>
            <li class="quiz-option">D. It is not useful — most programmers skip it</li>
        </ul>
        <p class="quiz-explanation"><em>Planning first catches mistakes while they are cheap to fix — before the console is actually running the sequence.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. In the restart-sequence flowchart, what comes immediately after "Coolant: stable"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Start</li>
            <li class="quiz-option">B. Power: online</li>
            <li class="quiz-option quiz-correct">C. Systems: ready ✓</li>
            <li class="quiz-option">D. End, with no further steps</li>
        </ul>
        <p class="quiz-explanation"><em>The plan is Power, then Coolant, then Systems, in that exact order — Systems: ready comes right after Coolant: stable.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If a flowchart's arrows show steps happening out of order, what is the biggest risk?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. None — order never matters in C</li>
            <li class="quiz-option quiz-correct">B. The program runs the steps in the wrong sequence, since C runs statements top to bottom exactly as written ✓</li>
            <li class="quiz-option">C. The program will refuse to compile</li>
            <li class="quiz-option">D. The console will crash instantly</li>
        </ul>
        <p class="quiz-explanation"><em>C runs each statement in the order it is written — a plan drawn out of order becomes code that runs out of order too.</em></p>
    </div>

</div>
