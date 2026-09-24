{{-- M3 · Lesson 3.3: ITIL Security Management — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c3-l3.json) completes the lesson.
Syllabus: ITP1232 Unit C.3 ITIL Security Management. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="03">

    <h1 class="lesson-heading">Lesson 3.3: ITIL Security Management</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what change management is and why every change to a
        live system follows a fixed order: request, test, then production.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>ITIL</strong> is a widely used set of practices for running IT services well, and one of its
        most important ideas is <strong>change management</strong>: a process for making changes to a system
        safely, instead of just making them and hoping nothing breaks.
    </p>

    <p class="body-text">Change management has a fixed shape, no matter how urgent the fix feels:</p>

    <ul class="body-list">
        <li><strong>File a change request.</strong> Write down what's changing, why, and who approved it. If something goes wrong, there's a record of exactly what was intended.</li>
        <li><strong>Test first.</strong> Apply the change to a test system that isn't serving anyone yet, and confirm it works.</li>
        <li><strong>Then production.</strong> Only after the test passes does the same change go to the live system everyone depends on.</li>
    </ul>

    <p class="body-text">
        Skipping straight to production because a fix feels urgent is exactly how urgent fixes turn into
        outages. The process exists because "I was sure it would work" is not the same as "I tested it."
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        <strong>Rivet</strong> found the bug during Vex's last probe: shield power can drop below its safe
        floor under a sustained attack. He wants to patch it immediately. <strong>Astro</strong> stops him at
        the door. "Urgent doesn't mean unplanned. File the request first."
    </p>

    <p class="body-text">
        Rivet writes the ticket: what's changing, why, the risk if it's skipped, and Astro's approval. "Now,"
        Astro says, "test node first. If the patch is wrong and something breaks, better it breaks on a system
        nobody's depending on."
    </p>

    <p class="body-text">
        <strong>Volt</strong> watches the shield readouts the whole time. "Test node holds. Patch confirmed
        good. Now production — and not a step before this."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="A change request leads to the test node first, and only after that succeeds does the same patch reach the production node">
            <rect x="20" y="60" width="170" height="80" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="105" y="95" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">Change Request</text>
            <text x="105" y="114" text-anchor="middle" font-size="12" fill="#4a35b8">CR-2201, Astro approved</text>

            <rect x="235" y="60" width="170" height="80" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="95" text-anchor="middle" font-size="14" font-weight="700" fill="#8a4b00">Test Node</text>
            <text x="320" y="114" text-anchor="middle" font-size="12" fill="#8a4b00">patch here first</text>

            <rect x="450" y="60" width="170" height="80" rx="14" fill="#eafaf0" stroke="#1e8a4c" stroke-width="2"/>
            <text x="535" y="95" text-anchor="middle" font-size="14" font-weight="700" fill="#146633">Production</text>
            <text x="535" y="114" text-anchor="middle" font-size="12" fill="#146633">shields stay up throughout</text>

            <line x1="190" y1="100" x2="231" y2="100" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="235,100 223,94 223,106" fill="#7c5cff"/>
            <line x1="405" y1="100" x2="446" y2="100" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="450,100 438,94 438,106" fill="#7c5cff"/>

            <text x="320" y="175" text-anchor="middle" font-size="13.5" font-weight="700" fill="#b3261e">Production is never patched before the test node confirms it's safe.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is change management, in one sentence?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A way to rename files consistently</li>
            <li class="quiz-option quiz-correct">B. A fixed process for making changes to a system safely ✓</li>
            <li class="quiz-option">C. A schedule for staff shift changes</li>
            <li class="quiz-option">D. A backup rotation policy</li>
        </ul>
        <p class="quiz-explanation"><em>Change management is the process — request, test, then production — that keeps changes from becoming outages.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What's the first step before touching any live system?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. File a change request describing what's changing and why ✓</li>
            <li class="quiz-option">B. Patch production immediately</li>
            <li class="quiz-option">C. Tell no one and hope it works</li>
            <li class="quiz-option">D. Delete the old configuration first</li>
        </ul>
        <p class="quiz-explanation"><em>A change request creates a record of what was intended, which matters if something goes wrong later.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Rivet's patch is urgent — shields could drop under attack. Should he patch production first to save time?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Yes, urgency skips the process</li>
            <li class="quiz-option quiz-correct">B. No — test node first, production only after it's confirmed safe ✓</li>
            <li class="quiz-option">C. Yes, but only if Astro is asleep</li>
            <li class="quiz-option">D. It doesn't matter which order</li>
        </ul>
        <p class="quiz-explanation"><em>Urgency is exactly when skipping the test step is most dangerous — a bad patch on production could cause the outage you were trying to prevent.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why test on a non-production node first?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Test nodes are faster to patch</li>
            <li class="quiz-option quiz-correct">B. If the patch is wrong, it breaks a system nobody depends on instead of the live one ✓</li>
            <li class="quiz-option">C. It's required only for cosmetic changes</li>
            <li class="quiz-option">D. Test nodes don't need change requests</li>
        </ul>
        <p class="quiz-explanation"><em>Testing first contains the risk of a bad patch to a system that isn't serving anyone yet.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. "I was sure it would work" is not the same as:</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. "I filed a request"</li>
            <li class="quiz-option quiz-correct">B. "I tested it" ✓</li>
            <li class="quiz-option">C. "I asked Astro"</li>
            <li class="quiz-option">D. "I backed it up"</li>
        </ul>
        <p class="quiz-explanation"><em>Confidence isn't verification — change management replaces "I was sure" with an actual test on a non-production system.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Rivet has an urgent shield patch. File the change request, patch the test
        node first, confirm it, then patch production — never the other way around.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c3-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm3', 'lesson' => 'lesson03']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. You type Windows commands there, including
            <code>copy &lt;patch&gt; test-node.cfg</code> and, only after that, <code>copy &lt;patch&gt; prod-node.cfg</code>.
            The order you run them in is checked.
        </p>
    </div>

</div>
