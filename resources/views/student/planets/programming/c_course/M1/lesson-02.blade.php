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

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">In a flowchart, which shape marks a step like “Print Power: online”?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. An oval</li>
            <li class="quiz-option">B. An arrow</li>
            <li class="quiz-option quiz-correct">C. A rectangle ✓</li>
            <li class="quiz-option">D. A circle with a cross</li>
        </ul>
        <p class="quiz-explanation"><em>Rectangles are steps. Ovals mark the start and end, and arrows show the order between them.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        Before a road trip, you plan the route. You can draw it on a <strong>map</strong>, with the stops in boxes and arrows between them. That is a <strong>flowchart</strong>. Or you can write it as <strong>turn-by-turn directions</strong> on a sticky note. That is <strong>pseudocode</strong>.
    </p>

    <p class="body-text">
        Both describe the exact same trip. Writing the C code is actually getting in the car and driving it.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 345" role="img" aria-label="Planning a road trip two ways. A map flowchart: Home, get gas, buy snacks, Beach. The same trip as written directions: START, go to the gas station, buy snacks, END. Writing C code is actually driving. Ovals are start or end, boxes are one step, arrows show what comes next.">
            <rect x="20" y="40" width="270" height="250" rx="12" fill="#fffaf2" stroke="#f08c1a" stroke-width="2"/><rect x="20" y="40" width="270" height="40" rx="12" fill="#fff0d9"/><rect x="21" y="62" width="268" height="18" fill="#fff0d9"/><line x1="20" y1="80" x2="290" y2="80" stroke="#f08c1a" stroke-width="2"/><rect x="20" y="40" width="270" height="250" rx="12" fill="none" stroke="#f08c1a" stroke-width="2"/><text x="38" y="66" font-size="12" fill="#8a4b00" font-weight="800" letter-spacing="1">MAP · FLOWCHART</text><ellipse cx="155" cy="108" rx="55" ry="18" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/><text x="155" y="113" font-size="14" fill="#4a35b8" font-weight="700" text-anchor="middle">Home</text><rect x="90" y="146" width="130" height="32" rx="8" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><text x="155" y="167" font-size="14" fill="#14306b" font-weight="700" text-anchor="middle">Get gas</text><rect x="90" y="196" width="130" height="32" rx="8" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><text x="155" y="217" font-size="14" fill="#14306b" font-weight="700" text-anchor="middle">Buy snacks</text><ellipse cx="155" cy="262" rx="55" ry="18" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/><text x="155" y="267" font-size="14" fill="#4a35b8" font-weight="700" text-anchor="middle">Beach</text><line x1="155" y1="126" x2="155.0" y2="136.0" stroke="#7c5cff" stroke-width="3"/><polygon points="155,145 161.0,136.0 149.0,136.0" fill="#7c5cff"/><line x1="155" y1="178" x2="155.0" y2="186.0" stroke="#7c5cff" stroke-width="3"/><polygon points="155,195 161.0,186.0 149.0,186.0" fill="#7c5cff"/><line x1="155" y1="228" x2="155.0" y2="234.0" stroke="#7c5cff" stroke-width="3"/><polygon points="155,243 161.0,234.0 149.0,234.0" fill="#7c5cff"/><line x1="300" y1="125" x2="331.0" y2="125.0" stroke="#e8457c" stroke-width="3"/><polygon points="342,125 331.0,119.0 331.0,131.0" fill="#e8457c"/><line x1="342" y1="125" x2="309.0" y2="125.0" stroke="#e8457c" stroke-width="3"/><polygon points="298,125 309.0,131.0 309.0,119.0" fill="#e8457c"/><text x="320" y="112" font-size="11" fill="#e8457c" font-weight="700" text-anchor="middle">same</text><text x="320" y="146" font-size="11" fill="#e8457c" font-weight="700" text-anchor="middle">trip</text><rect x="350" y="40" width="270" height="170" rx="12" fill="#f5f9ff" stroke="#2f6fe0" stroke-width="2"/><rect x="350" y="40" width="270" height="40" rx="12" fill="#e8f0ff"/><rect x="351" y="62" width="268" height="18" fill="#e8f0ff"/><line x1="350" y1="80" x2="620" y2="80" stroke="#2f6fe0" stroke-width="2"/><rect x="350" y="40" width="270" height="170" rx="12" fill="none" stroke="#2f6fe0" stroke-width="2"/><text x="368" y="66" font-size="12" fill="#14306b" font-weight="800" letter-spacing="1">DIRECTIONS · PSEUDOCODE</text><text x="370" y="108" font-size="14" fill="#14306b" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">START</text><text x="370" y="134" font-size="14" fill="#14306b" font-family="ui-monospace, Menlo, Consolas, monospace">&#160;&#160;go&#160;to&#160;the&#160;gas&#160;station</text><text x="370" y="160" font-size="14" fill="#14306b" font-family="ui-monospace, Menlo, Consolas, monospace">&#160;&#160;buy&#160;snacks</text><text x="370" y="186" font-size="14" fill="#14306b" font-weight="700" font-family="ui-monospace, Menlo, Consolas, monospace">END</text><line x1="360" y1="252" x2="612" y2="252" stroke="#9aa5b8" stroke-width="3"/><line x1="368" y1="252" x2="604" y2="252" stroke="#fff" stroke-width="1.5" stroke-dasharray="10,8"/><rect x="445" y="222" width="80" height="22" rx="7" fill="#ffb3cb" stroke="#e8457c" stroke-width="2"/><path d="M460 222 L470 206 H500 L512 222 Z" fill="#ffe3ec" stroke="#e8457c" stroke-width="2"/><circle cx="463" cy="246" r="8" fill="#1b2440"/><circle cx="507" cy="246" r="8" fill="#1b2440"/><text x="485" y="280" font-size="13" fill="#9c1f4c" font-weight="700" text-anchor="middle">C code = actually driving</text><rect x="20" y="298" width="190" height="36" rx="18" fill="#f3f0ff" stroke="#7c5cff" stroke-width="1.8"/><ellipse cx="46" cy="316" rx="13" ry="7" fill="#fff" stroke="#7c5cff" stroke-width="1.8"/><text x="68" y="321" font-size="13" fill="#4a35b8"><tspan font-weight="800">Oval</tspan> · start or end</text><rect x="225" y="298" width="190" height="36" rx="18" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="1.8"/><rect x="238" y="309" width="24" height="14" rx="3" fill="#fff" stroke="#2f6fe0" stroke-width="1.8"/><text x="272" y="321" font-size="13" fill="#14306b"><tspan font-weight="800">Box</tspan> · one step</text><rect x="430" y="298" width="190" height="36" rx="18" fill="#ffe3ec" stroke="#e8457c" stroke-width="1.8"/><line x1="443" y1="316" x2="459.0" y2="316.0" stroke="#e8457c" stroke-width="2.5"/><polygon points="467,316 459.0,310.0 459.0,322.0" fill="#e8457c"/><text x="477" y="321" font-size="13" fill="#9c1f4c"><tspan font-weight="800">Arrow</tspan> · what's next</text>
        </svg>
    </figure>

    <p class="body-text">
        <strong>Why plan first?</strong> If the plan forgot the gas stop, you fix it with a pencil in two seconds. If you only find out halfway down the highway with an empty tank, it is a much bigger problem. Programmers plan on paper for the same reason.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why plan the route before you start driving?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Maps make the car drive faster on the road</li>
            <li class="quiz-option">B. Drivers are not allowed to leave without one</li>
            <li class="quiz-option">C. The car can only follow routes drawn in ink</li>
            <li class="quiz-option quiz-correct">D. A missing stop is easier to fix on paper ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Catching a forgotten step on paper costs a pencil mark. Catching it after the program (or the car) is already running costs a lot more.</em></p>
    </div>

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

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">When this pseudocode becomes C, what does each PRINT line turn into?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. One comment, in the same order</li>
            <li class="quiz-option">B. One variable, in the same order</li>
            <li class="quiz-option quiz-correct">C. One <code>printf</code> call, in the same order ✓</li>
            <li class="quiz-option">D. One <code>printf</code> that prints all three lines</li>
        </ul>
        <p class="quiz-explanation"><em>One pseudocode line becomes one <code>printf</code>, in the same order as the plan.</em></p>
    </div>

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
            <li class="quiz-option">A. A loop that repeats</li>
            <li class="quiz-option quiz-correct">B. The start or the end ✓</li>
            <li class="quiz-option">C. A step to carry out</li>
            <li class="quiz-option">D. An error to fix</li>
        </ul>
        <p class="quiz-explanation"><em>Ovals mark start and end points; rectangles mark steps, and arrows show the order between them.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is pseudocode?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. C code that is meant not to compile</li>
            <li class="quiz-option quiz-correct">B. A plan written in plain sentences ✓</li>
            <li class="quiz-option">C. A kind of C syntax error message</li>
            <li class="quiz-option">D. A shape used inside a flowchart</li>
        </ul>
        <p class="quiz-explanation"><em>Pseudocode is a structured plan in plain language — close enough in shape to real code that turning it into C is mostly direct.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why plan with a flowchart or pseudocode before writing real code?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. C needs a plan before it can compile</li>
            <li class="quiz-option quiz-correct">B. Mistakes are cheaper to fix on paper ✓</li>
            <li class="quiz-option">C. It makes the finished program run faster</li>
            <li class="quiz-option">D. It isn't useful; most coders skip it</li>
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
            <li class="quiz-option">A. None, because order never matters in C</li>
            <li class="quiz-option quiz-correct">B. The code runs the steps in the wrong order ✓</li>
            <li class="quiz-option">C. The program will refuse to compile at all</li>
            <li class="quiz-option">D. The console will crash the moment it starts</li>
        </ul>
        <p class="quiz-explanation"><em>C runs each statement in the order it is written — a plan drawn out of order becomes code that runs out of order too.</em></p>
    </div>

</div>
