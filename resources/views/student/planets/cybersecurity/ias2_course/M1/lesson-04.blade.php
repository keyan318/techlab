{{-- M1 · Lesson 1.4: Security Methodology & Building a Program — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a1-l4.json) completes the lesson.
Syllabus: ITP1333 Unit A.4 Security Methodology & A.5 Building a Security Program. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="04">

    <h1 class="lesson-heading">Lesson 1.4: Security Methodology & Building a Program</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> describe the top-down flow from policy to standards to procedures, and list the core pieces a security program needs to hold together.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A security methodology is the order things get decided in. The best programs run <strong>top-down</strong>:
        leadership sets a <strong>policy</strong> (the "what" and "why" — high level, rarely changes), which is
        supported by <strong>standards</strong> (the specific rules everyone must follow, e.g. "passwords must be
        12+ characters"), which are carried out through <strong>procedures</strong> (the exact steps someone
        follows to do a task). Bottom-up security — a technician deciding rules on their own with no policy behind
        them — tends to be inconsistent and hard to defend later.
    </p>

    <p class="body-text">
        A real security program needs more than rules on paper. It needs: a written policy, people trained to
        follow it (awareness), technology that enforces it (tools), and someone accountable for keeping it all
        running. Miss any one piece and the others don't hold.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">In top-down security, what does a standard give you?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The exact steps for doing one task</li>
            <li class="quiz-option">B. The high-level what and why of the whole plan</li>
            <li class="quiz-option">C. A technician's own idea of the rules</li>
            <li class="quiz-option quiz-correct">D. The specific rules everyone must follow ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Policy sets the what and why. Standards are the specific rules. Procedures are the exact steps.</em></p>
    </div>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        A new cadet asks why they can't just tell each department "be careful" and let them figure out their own
        rules. Astro shakes their head. "That's how the Citadel got hit last time — every station had a different
        idea of 'careful.' Now it flows one way: I set the policy — protect crew data, full stop. Rivet turns that
        into standards — exactly how strong a password must be, exactly which ports stay closed. Volt turns those
        into procedures — the steps a cadet follows on their first watch. Nobody improvises the rules. They follow
        what flows down from the top."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why did the Citadel get hit last time, according to Astro?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The policy was written by too many people</li>
            <li class="quiz-option quiz-correct">B. Each station had its own idea of “careful” ✓</li>
            <li class="quiz-option">C. Volt's procedures were followed too exactly</li>
            <li class="quiz-option">D. Rivet's standards had too many exact rules</li>
        </ul>
        <p class="quiz-explanation"><em>Bottom-up rules are inconsistent. Now policy flows down: Astro's policy, Rivet's standards, Volt's procedures.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="Policy flows down into standards, which flow down into procedures">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Policy</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">the what & why</text>
            <line x1="170" y1="66" x2="237" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="245,66 233,60 233,72" fill="#7c5cff"/>
            <rect x="245" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">Standards</text>
            <text x="320" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">the specific rules</text>
            <line x1="395" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Procedures</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">the exact steps</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Top-down: policy sets direction, standards make it specific, procedures make it doable</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what sits at the bottom?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Policy</li>
            <li class="quiz-option">B. Standards</li>
            <li class="quiz-option quiz-correct">C. Procedures ✓</li>
            <li class="quiz-option">D. Guidelines</li>
        </ul>
        <p class="quiz-explanation"><em>Policy at the top, then standards, then procedures: the exact steps that make it doable.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In the top-down flow, what is a "policy"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The exact step-by-step task instructions</li>
            <li class="quiz-option quiz-correct">B. The high-level "what and why," set by leadership and rarely changed ✓</li>
            <li class="quiz-option">C. A single technician's personal preference</li>
            <li class="quiz-option">D. A list of passwords</li>
        </ul>
        <p class="quiz-explanation"><em>A policy is the high-level direction leadership sets — the goal, not the detail.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What are "standards", in this flow?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The step-by-step instructions a cadet follows</li>
            <li class="quiz-option quiz-correct">B. The specific, mandatory rules that support the policy (e.g. minimum password length) ✓</li>
            <li class="quiz-option">C. A synonym for policy</li>
            <li class="quiz-option">D. Something only Planet Doom uses</li>
        </ul>
        <p class="quiz-explanation"><em>Standards translate the policy's direction into specific, checkable rules everyone must follow.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What are "procedures"?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The exact steps someone follows to carry out a standard ✓</li>
            <li class="quiz-option">B. The Citadel's mission statement</li>
            <li class="quiz-option">C. A list of enemies</li>
            <li class="quiz-option">D. The budget for security tools</li>
        </ul>
        <p class="quiz-explanation"><em>Procedures are the concrete, repeatable steps that put a standard into practice.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why does Astro insist the rules flow top-down instead of each station inventing its own?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because it takes longer this way</li>
            <li class="quiz-option quiz-correct">B. Because inconsistent, station-by-station rules were exactly how the Citadel got breached before ✓</li>
            <li class="quiz-option">C. Top-down flow is required by tradition only</li>
            <li class="quiz-option">D. Because Rivet prefers paperwork</li>
        </ul>
        <p class="quiz-explanation"><em>Letting every station define its own "careful" led to inconsistent gaps — a shared policy flowing down closes them.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Besides a written policy, what else does a real security program need to actually hold together?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing else — a policy document is sufficient by itself</li>
            <li class="quiz-option quiz-correct">B. Trained people, enforcing technology, and someone accountable for keeping it running ✓</li>
            <li class="quiz-option">C. Only a bigger budget</li>
            <li class="quiz-option">D. A new logo</li>
        </ul>
        <p class="quiz-explanation"><em>A policy without trained people, enforcing tools and accountable ownership tends to fall apart in practice.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Astro handed the crew a stack of loose rules with no order. Sort each one into policy, standard, or procedure so the program actually flows top-down.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a1-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm1', 'lesson' => 'lesson04']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the mixed rule sheet, then classify each item by the level it
            belongs to: policy, standard, or procedure.
        </p>
    </div>

</div>
