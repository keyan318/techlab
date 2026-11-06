{{-- M4 · Lesson 4.2: Managed Security Services — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a4-l2.json) completes the lesson.
Syllabus: ITP1333 Unit D.2 Managed Security Services. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="02">

    <h1 class="lesson-heading">Lesson 4.2: Managed Security Services</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> explain what a managed security service provider (MSSP) does, and identify which security functions are reasonable to outsource versus which must stay in-house.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>managed security service provider</strong> is an outside specialist hired to run certain
        security functions — most commonly 24/7 monitoring, since no small crew can watch logs around the clock
        alone. Outsourcing the <em>work</em> does not outsource the <em>accountability</em>: if something goes
        wrong, the Citadel is still responsible for the outcome, even if an MSSP was watching.
    </p>

    <p class="body-text">
        Good candidates for outsourcing are functions that need constant coverage or specialized tools the crew
        can't reasonably build itself, like round-the-clock alert monitoring. Poor candidates are functions that
        require deep, specific knowledge of the Citadel's own priorities and judgment calls — like deciding
        whether to grant access to the starmap. Those stay in-house.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does outsourcing security work never outsource?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The monitoring</li>
            <li class="quiz-option">B. The alert tools</li>
            <li class="quiz-option quiz-correct">C. The accountability ✓</li>
            <li class="quiz-option">D. The night shifts to cover</li>
        </ul>
        <p class="quiz-explanation"><em>If something goes wrong, the Citadel is still responsible, even if an MSSP was watching.</em></p>
    </div>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Rivet proposes hiring an outside crew to watch the Citadel's alerts overnight, since nobody on watch can
        stay awake for every shift forever. Astro agrees — that's a reasonable function to outsource, if the
        contract is clear about who gets alerted and how fast. But when the same proposal suggests letting that
        outside crew also decide who gets starmap access, Astro stops it cold. "Watching alerts, yes. Deciding who
        touches our most sensitive data — that judgment call stays with us."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Which part of Rivet's proposal did Astro stop?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Letting outsiders decide starmap access ✓</li>
            <li class="quiz-option">B. Watching the alerts all through the night shift</li>
            <li class="quiz-option">C. Saying who gets alerted and how fast</li>
            <li class="quiz-option">D. Hiring any outside crew to help them</li>
        </ul>
        <p class="quiz-explanation"><em>Watching alerts can be outsourced. Judging who touches the most sensitive data stays in-house.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="24/7 monitoring is a good outsourcing candidate; access decisions must stay in-house">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">24/7 Monitoring</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">good candidate to outsource</text>
            <line x1="170" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">Access Decisions</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">stays in-house</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Outsourcing the work never outsources the accountability</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what is a good outsourcing candidate?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Access decisions</li>
            <li class="quiz-option">B. Data ownership</li>
            <li class="quiz-option quiz-correct">C. 24/7 monitoring ✓</li>
            <li class="quiz-option">D. Starmap approvals</li>
        </ul>
        <p class="quiz-explanation"><em>Round-the-clock monitoring needs constant coverage, so it's a good fit. Access decisions stay in-house.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does a managed security service provider (MSSP) typically do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Sets the Citadel's overall priorities</li>
            <li class="quiz-option quiz-correct">B. Runs certain security functions on the crew's behalf, most commonly 24/7 monitoring ✓</li>
            <li class="quiz-option">C. Replaces the need for any internal security roles</li>
            <li class="quiz-option">D. Only handles physical security</li>
        </ul>
        <p class="quiz-explanation"><em>An MSSP is an outside specialist providing specific security functions, most classically round-the-clock monitoring.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. If an MSSP is watching the logs and something is still missed, who is accountable for the outcome?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nobody — accountability transfers entirely to the MSSP</li>
            <li class="quiz-option quiz-correct">B. The Citadel remains accountable, even though the MSSP did the watching ✓</li>
            <li class="quiz-option">C. Only the MSSP's individual employee who missed it</li>
            <li class="quiz-option">D. Accountability doesn't apply to outsourced functions</li>
        </ul>
        <p class="quiz-explanation"><em>Outsourcing the work never outsources the accountability — the organization remains responsible for the outcome.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why is 24/7 alert monitoring a good candidate for outsourcing?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It requires deep knowledge of the Citadel's specific priorities</li>
            <li class="quiz-option quiz-correct">B. It needs constant coverage that a small crew can't reasonably staff alone ✓</li>
            <li class="quiz-option">C. It's the least important security function</li>
            <li class="quiz-option">D. It should never be trusted to an outside party</li>
        </ul>
        <p class="quiz-explanation"><em>Round-the-clock coverage is exactly the kind of function that's impractical for a small internal team to sustain alone.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why did Astro refuse to let the outside crew decide who gets starmap access?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because outside crews are never trustworthy</li>
            <li class="quiz-option quiz-correct">B. Because that decision requires specific judgment about the Citadel's own priorities, which should stay in-house ✓</li>
            <li class="quiz-option">C. Because it was too expensive</li>
            <li class="quiz-option">D. Because the outside crew didn't ask permission</li>
        </ul>
        <p class="quiz-explanation"><em>Access decisions require judgment specific to the organization's own priorities and risk tolerance — not a good fit for outsourcing.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is a poor candidate function for outsourcing to an MSSP?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Round-the-clock log monitoring</li>
            <li class="quiz-option quiz-correct">B. A judgment call requiring deep, specific knowledge of the organization's own priorities ✓</li>
            <li class="quiz-option">C. Any function at all — nothing should ever be outsourced</li>
            <li class="quiz-option">D. Reading standard alert dashboards</li>
        </ul>
        <p class="quiz-explanation"><em>Functions needing organization-specific judgment are poor fits for an outside provider, unlike routine, well-defined monitoring work.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Rivet has three functions on the table for the new MSSP contract. Decide which stay in-house and which are safe to outsource.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a4-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm4', 'lesson' => 'lesson02']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the proposal, then classify each function as
            outsource-ok or keep-in-house.
        </p>
    </div>

</div>
