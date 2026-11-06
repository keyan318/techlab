{{-- M3 · Lesson 3.3: Procedures — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a3-l3.json) completes the lesson.
Syllabus: ITP1333 Unit C.3 Procedures. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="03">

    <h1 class="lesson-heading">Lesson 3.3: Procedures</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> describe a procedure as an exact, ordered sequence of steps, and explain why the order of steps can matter as much as the steps themselves.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>procedure</strong> is the exact sequence of steps someone follows to carry out a standard —
        specific enough that two different people, following it, do the same thing in the same order. Procedures
        turn "what must be true" (a standard) into "what to actually do, and in what order."
    </p>

    <p class="body-text">
        Order matters more than people expect. Preserving evidence before restoring a system, or verifying a
        backup before deleting the original, are procedures where doing steps out of order can undo the whole
        point — restore first, and the evidence you needed is already overwritten.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What makes a good procedure?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Two people doing it act the same way ✓</li>
            <li class="quiz-option">B. It leaves every step up to judgment</li>
            <li class="quiz-option">C. It says only why the work matters</li>
            <li class="quiz-option">D. It can be done in any order you like</li>
        </ul>
        <p class="quiz-explanation"><em>A procedure is specific enough that two people following it do the same thing in the same order.</em></p>
    </div>

    <h2 class="section-heading">Volt's Drill</h2>

    <p class="body-text">
        A tampered log file needs restoring from backup. A cadet reaches straight for the backup command. Volt
        stops them. "Wrong order. Fingerprint the tampered file first — that's our evidence of what happened.
        Restore it after, not before. Same two steps, but do them backwards and we lose the one thing that proves
        what Vex's crew actually did."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why does Volt stop the cadet from restoring the log first?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The backup is always broken</li>
            <li class="quiz-option">B. Restoring takes too long</li>
            <li class="quiz-option">C. The cadet has no access</li>
            <li class="quiz-option quiz-correct">D. The evidence would be lost ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Fingerprint the tampered file first. Restore first, and the proof of what Vex did is overwritten.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A procedure's step order matters: preserve evidence before restoring">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">Step 1</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">preserve evidence (hash)</text>
            <line x1="170" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Step 2</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">restore from backup</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Same two steps, wrong order — and the evidence is gone before it's captured</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what is step 1?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Restore backup</li>
            <li class="quiz-option">B. Report to Astro now</li>
            <li class="quiz-option quiz-correct">C. Preserve evidence ✓</li>
            <li class="quiz-option">D. Delete the file</li>
        </ul>
        <p class="quiz-explanation"><em>Step 1 is preserve the evidence (hash). Step 2 is restore from backup.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is a "procedure"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A high-level statement of why security matters</li>
            <li class="quiz-option quiz-correct">B. The exact, ordered sequence of steps someone follows to carry out a standard ✓</li>
            <li class="quiz-option">C. A synonym for policy</li>
            <li class="quiz-option">D. A list of enemies to watch for</li>
        </ul>
        <p class="quiz-explanation"><em>A procedure is the concrete, step-by-step how — specific enough that anyone following it does the same thing.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why did Volt stop the cadet from restoring the file immediately?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Restoring files is against policy</li>
            <li class="quiz-option quiz-correct">B. The order was wrong — evidence needed to be preserved (hashed) before the restore, not after ✓</li>
            <li class="quiz-option">C. The cadet used the wrong command entirely</li>
            <li class="quiz-option">D. Volt wanted to restore it personally</li>
        </ul>
        <p class="quiz-explanation"><em>Restoring first would overwrite the tampered state before it could be fingerprinted as evidence.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What turns a standard into a procedure?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Making it vaguer</li>
            <li class="quiz-option quiz-correct">B. Spelling out the exact steps and their order to carry the standard out ✓</li>
            <li class="quiz-option">C. Removing all the specific steps</li>
            <li class="quiz-option">D. Adding a punishment clause</li>
        </ul>
        <p class="quiz-explanation"><em>A procedure is the standard made actionable — the literal steps, in the correct sequence.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why can step order matter as much as the steps themselves?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It never matters, order is just a formality</li>
            <li class="quiz-option quiz-correct">B. Doing steps out of order can undo the whole point, e.g. losing evidence by restoring before preserving it ✓</li>
            <li class="quiz-option">C. Only the last step in any procedure ever matters</li>
            <li class="quiz-option">D. Order only matters for cooking recipes, not security</li>
        </ul>
        <p class="quiz-explanation"><em>Some procedures depend on order to work at all — evidence preservation before restoration is a classic example.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Two different cadets follow the same well-written procedure. What should be true?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They will likely do different things, since procedures are just suggestions</li>
            <li class="quiz-option quiz-correct">B. They should do the same thing, in the same order, because the procedure is specific enough ✓</li>
            <li class="quiz-option">C. Only the more experienced cadet will get it right</li>
            <li class="quiz-option">D. Procedures are only for command staff, not cadets</li>
        </ul>
        <p class="quiz-explanation"><em>A good procedure is specific enough that following it produces the same result regardless of who follows it.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> A tampered log needs restoring — but evidence comes first. Follow the procedure in the correct order.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a3-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm3', 'lesson' => 'lesson03']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Fingerprint the tampered file before restoring it — order
            matters for this procedure.
        </p>
    </div>

</div>
