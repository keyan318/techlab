{{-- M7 · Lesson 7.2: Business Continuity Planning — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a7-l2.json) completes the lesson.
Syllabus: ITP1333 Unit G.2 Business Continuity Planning (BCP). --}}

<div class="lesson-fragment" data-module="m7" data-lesson="02">

    <h1 class="lesson-heading">Lesson 7.2: Business Continuity Planning</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> distinguish business continuity planning (keeping the whole organization running) from disaster recovery (restoring IT systems), and place a given activity in its correct scope.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Disaster recovery</strong> restores IT systems. <strong>Business continuity planning (BCP)</strong>
        is broader: keeping the entire organization functioning during a disruption, whether or not IT is even
        involved. BCP covers things DR doesn't: an alternate physical work location if the main site is
        unavailable, backup personnel who can step into a critical role if someone is unreachable, and a
        communication plan for keeping everyone — staff, customers, partners — informed while things are disrupted.
    </p>

    <p class="body-text">
        A useful way to remember the difference: DR asks "how do we get the systems back?" BCP asks "how does the
        organization keep operating at all, systems or no systems?"
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does BCP cover that DR doesn't?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Restoring a server from the last backup</li>
            <li class="quiz-option quiz-correct">B. Keeping the whole organization running ✓</li>
            <li class="quiz-option">C. Replacing one broken hard drive in IT</li>
            <li class="quiz-option">D. Rebooting the relay console's systems</li>
        </ul>
        <p class="quiz-explanation"><em>DR restores IT systems. BCP keeps the whole organization working, with or without systems.</em></p>
    </div>

    <h2 class="section-heading">Volt's Drill</h2>

    <p class="body-text">
        Volt runs a scenario: the Citadel's main hall is unusable after a hull breach, unrelated to any computer
        failure. "The relay console is fine. Every system works. But where does command operate from now? Who
        speaks for the crew to our allies while we're relocated? That's not a DR question — DR would have nothing
        to restore here. That's business continuity."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why is the hull breach a BCP question, not DR?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The relay console is badly damaged</li>
            <li class="quiz-option">B. Every backup was lost in the breach</li>
            <li class="quiz-option quiz-correct">C. Every system works; the hall doesn't ✓</li>
            <li class="quiz-option">D. The firewall stopped all of the traffic</li>
        </ul>
        <p class="quiz-explanation"><em>DR would have nothing to restore. The question is where command works from and who speaks for the crew.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 230" role="img" aria-label="DR asks how systems come back; BCP asks how the organization keeps running at all">
            <rect x="245" y="75" width="150" height="62" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2.5"/>
            <text x="320" y="102" text-anchor="middle" font-size="16" font-weight="700" fill="#4a35b8">A Disruption</text>
            <text x="320" y="123" text-anchor="middle" font-size="12" fill="#4a35b8">something breaks</text>
            <rect x="135" y="20" width="170" height="62" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="220" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#1c4f8f">Disaster Recovery</text>
            <text x="220" y="68" text-anchor="middle" font-size="11.5" fill="#1c4f8f">restore the IT systems</text>
            <line x1="220" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <rect x="335" y="20" width="170" height="62" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="420" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#146c3a">Business Continuity</text>
            <text x="420" y="68" text-anchor="middle" font-size="11.5" fill="#146c3a">keep the whole crew operating</text>
            <line x1="420" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="220" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">DR asks how systems come back; BCP asks how the organization keeps running at all</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what does disaster recovery do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Keeps the crew going</li>
            <li class="quiz-option">B. Finds a new location</li>
            <li class="quiz-option quiz-correct">C. Restores IT systems ✓</li>
            <li class="quiz-option">D. Talks to the allies</li>
        </ul>
        <p class="quiz-explanation"><em>DR restores the IT systems. BCP keeps the whole crew operating.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is the main difference between disaster recovery and business continuity planning?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They are the same thing with different names</li>
            <li class="quiz-option quiz-correct">B. DR restores IT systems specifically; BCP keeps the whole organization running, IT or not ✓</li>
            <li class="quiz-option">C. BCP only applies to small organizations</li>
            <li class="quiz-option">D. DR is broader than BCP</li>
        </ul>
        <p class="quiz-explanation"><em>DR is IT-focused restoration; BCP covers the organization's overall ability to keep operating.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which of these is a BCP concern rather than a DR concern?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Restoring a failed server from backup</li>
            <li class="quiz-option quiz-correct">B. An alternate physical work location if the main site is unusable ✓</li>
            <li class="quiz-option">C. Recovering a corrupted database</li>
            <li class="quiz-option">D. Fixing a crashed application</li>
        </ul>
        <p class="quiz-explanation"><em>An alternate work location addresses keeping people operating, not restoring a specific IT system.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. In Volt's hull-breach scenario, why was this a BCP question and not a DR question?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because the systems needed extensive repair</li>
            <li class="quiz-option quiz-correct">B. Because every system worked fine — the problem was where people could physically operate from, which DR doesn't address ✓</li>
            <li class="quiz-option">C. Because DR only applies during hull breaches</li>
            <li class="quiz-option">D. Because Volt prefers BCP terminology</li>
        </ul>
        <p class="quiz-explanation"><em>With no IT systems actually broken, there was nothing for DR to restore — the challenge was purely about keeping operations going.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What question does DR ask, according to the lesson's memory aid?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. "How does the organization keep operating at all?"</li>
            <li class="quiz-option quiz-correct">B. "How do we get the systems back?" ✓</li>
            <li class="quiz-option">C. "Who communicates with our allies?"</li>
            <li class="quiz-option">D. "Where does command relocate to?"</li>
        </ul>
        <p class="quiz-explanation"><em>DR is specifically focused on restoring systems, not the organization's broader operational continuity.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does a communication plan, as part of BCP, cover?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Only internal IT system alerts</li>
            <li class="quiz-option quiz-correct">B. Keeping staff, customers, and partners informed while normal operations are disrupted ✓</li>
            <li class="quiz-option">C. The technical steps to restore a database</li>
            <li class="quiz-option">D. The RTO for a failed server</li>
        </ul>
        <p class="quiz-explanation"><em>BCP's communication plan is about people staying informed during a disruption, not a technical system alert.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four activities from the hull-breach drill need sorting: DR-scope or BCP-scope.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a7-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm7', 'lesson' => 'lesson02']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the drill activity list, then classify each one.
        </p>
    </div>

</div>
