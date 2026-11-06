{{-- M3 · Lesson 3.4: Security Guidelines — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a3-l4.json) completes the lesson.
Syllabus: ITP1333 Unit C.4 Security Guidelines. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="04">

    <h1 class="lesson-heading">Lesson 3.4: Security Guidelines</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> distinguish a guideline (recommended, discretionary) from a standard (mandatory), and decide when discretion is appropriate.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>guideline</strong> is a recommendation, not a requirement. Where a standard says "must" and is
        checked pass/fail, a guideline says "should" and leaves room for judgment based on the situation. Guidelines
        exist because not every situation fits neatly into a rigid rule — someone with good judgment sometimes
        needs to weigh the specifics.
    </p>

    <p class="body-text">
        The danger is treating a guideline as optional in the wrong direction — skipping it out of laziness rather
        than genuine judgment. A good test: could you explain, to someone reviewing your decision later, exactly
        why this case called for a different choice than the guideline suggests?
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">How is a guideline different from a standard?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A guideline is always checked pass/fail</li>
            <li class="quiz-option quiz-correct">B. A guideline says “should”, not “must” ✓</li>
            <li class="quiz-option">C. A guideline can never be set aside</li>
            <li class="quiz-option">D. A guideline lists the exact steps</li>
        </ul>
        <p class="quiz-explanation"><em>A standard is a must, checked pass/fail. A guideline is a should, with room for judgment.</em></p>
    </div>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        The guideline says "prefer encrypted channels for routine reports." A cadet sends an urgent shield-failure
        alert over an unencrypted emergency channel instead, because it's faster and every second counts. Astro
        approves the choice. "That's discretion used correctly — you can explain exactly why speed mattered more
        than the guideline this one time. If you'd done that for a routine supply report just to save a minute,
        that's not judgment, that's just skipping the guideline."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why did Astro approve the unencrypted shield alert?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The guideline says never to encrypt</li>
            <li class="quiz-option">B. Encrypted channels were all broken</li>
            <li class="quiz-option">C. It was only a routine supply report anyway</li>
            <li class="quiz-option quiz-correct">D. Speed mattered, and it can be explained ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Discretion used correctly: the cadet can explain why speed mattered more this one time.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A standard is mandatory; a guideline is a recommendation open to judgment">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">Standard</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">must — checked pass/fail</text>
            <line x1="170" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Guideline</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">should — judgment allowed</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">A guideline can be set aside with good reason; a standard cannot</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what can be set aside with good reason?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. A guideline ✓</li>
            <li class="quiz-option">B. A standard</li>
            <li class="quiz-option">C. A policy</li>
            <li class="quiz-option">D. Both of them</li>
        </ul>
        <p class="quiz-explanation"><em>A guideline can be set aside with good reason; a standard cannot.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is the key difference between a standard and a guideline?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They mean exactly the same thing</li>
            <li class="quiz-option quiz-correct">B. A standard is mandatory ("must"); a guideline is a recommendation ("should") open to judgment ✓</li>
            <li class="quiz-option">C. A guideline is always stricter than a standard</li>
            <li class="quiz-option">D. Only standards are ever written down</li>
        </ul>
        <p class="quiz-explanation"><em>A standard must be followed and is checked pass/fail; a guideline allows discretion based on the situation.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why do guidelines exist instead of making everything a strict standard?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because standards are too expensive to write</li>
            <li class="quiz-option quiz-correct">B. Because not every situation fits a rigid rule — sometimes judgment is genuinely needed ✓</li>
            <li class="quiz-option">C. Guidelines are actually stricter than standards</li>
            <li class="quiz-option">D. Because Astro dislikes standards</li>
        </ul>
        <p class="quiz-explanation"><em>Some situations are specific enough that a fixed rule wouldn't fit every case — a guideline leaves room for good judgment.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Astro approve the cadet's choice to use the unencrypted emergency channel?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because guidelines don't matter at all</li>
            <li class="quiz-option quiz-correct">B. Because the cadet could explain exactly why speed mattered more than the guideline in that specific case ✓</li>
            <li class="quiz-option">C. Because Astro dislikes encrypted channels</li>
            <li class="quiz-option">D. Because the shield-failure alert wasn't important</li>
        </ul>
        <p class="quiz-explanation"><em>Discretion used well means being able to justify the deviation with a specific, defensible reason.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What is the danger Astro warns about with guidelines?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Following them too strictly</li>
            <li class="quiz-option quiz-correct">B. Treating them as optional out of laziness rather than genuine judgment ✓</li>
            <li class="quiz-option">C. Guidelines cause more breaches than standards</li>
            <li class="quiz-option">D. There is no danger — guidelines are always safe to ignore</li>
        </ul>
        <p class="quiz-explanation"><em>A guideline set aside without a real, explainable reason is just corner-cutting, not sound judgment.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is a good test for whether discretion was used correctly on a guideline?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Whether anyone noticed</li>
            <li class="quiz-option quiz-correct">B. Whether you could explain, to someone reviewing later, exactly why this case called for a different choice ✓</li>
            <li class="quiz-option">C. Whether it saved any amount of time at all</li>
            <li class="quiz-option">D. Whether the guideline was inconvenient</li>
        </ul>
        <p class="quiz-explanation"><em>If the deviation can be clearly justified after the fact, that's judgment; if not, it's likely just skipped.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four choices were made this week, some against a guideline. Read them, then decide which used good judgment and which just skipped the guideline.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a3-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm3', 'lesson' => 'lesson04']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the decisions log, then classify each one as good-judgment
            or skipped-guideline.
        </p>
    </div>

</div>
