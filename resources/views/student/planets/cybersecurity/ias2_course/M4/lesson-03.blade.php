{{-- M4 · Lesson 4.3: Security Councils & Steering Committees — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a4-l3.json) completes the lesson.
Syllabus: ITP1333 Unit D.3 Security Councils, Steering Committees, or Boards. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="03">

    <h1 class="lesson-heading">Lesson 4.3: Security Councils & Steering Committees</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> describe the purpose of a security council (cross-department governance and priority-setting) and recognize when one is missing a required voice.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>security council</strong> (or steering committee) is a standing group that reviews risk and sets
        security priorities across an entire organization, not just within one department. Its value comes from
        having the right voices in the room: security cannot set priorities in isolation from the departments
        actually affected by them, and departments cannot set their own priorities without a security perspective
        weighing the risk.
    </p>

    <p class="body-text">
        A council missing a key department tends to make decisions that look fine on paper but don't survive
        contact with how that department actually works — a policy nobody who does the daily work was consulted on.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What makes a security council valuable?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Only security people in it</li>
            <li class="quiz-option">B. Meeting just once a year</li>
            <li class="quiz-option">C. Letting each team choose alone</li>
            <li class="quiz-option quiz-correct">D. The right voices in the room ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Security can't set priorities without the affected departments, and departments need a security view.</em></p>
    </div>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        The security council meets to approve a new access policy for the supply bay — but nobody from Supply is
        in the room. Astro pauses the vote. "We can approve this today, and Supply can tell us next week exactly
        why it doesn't work for their shift schedule. Or we can invite them now and get it right the first time.
        A council that only hears from security isn't a council — it's just security talking to itself."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why did Astro pause the supply bay vote?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Nobody from Supply was there ✓</li>
            <li class="quiz-option">B. The policy was too short</li>
            <li class="quiz-option">C. Volt had not written the policy yet</li>
            <li class="quiz-option">D. Supply had already voted no</li>
        </ul>
        <p class="quiz-explanation"><em>A council that only hears from security is just security talking to itself. Invite Supply first.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 230" role="img" aria-label="A council missing an affected department isn't hearing the whole picture">
            <rect x="245" y="75" width="150" height="62" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2.5"/>
            <text x="320" y="102" text-anchor="middle" font-size="16" font-weight="700" fill="#4a35b8">Security Council</text>
            <text x="320" y="123" text-anchor="middle" font-size="12" fill="#4a35b8">sets priorities together</text>
            <rect x="135" y="20" width="170" height="62" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="220" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#1c4f8f">Security Lead</text>
            <text x="220" y="68" text-anchor="middle" font-size="11.5" fill="#1c4f8f">risk perspective</text>
            <line x1="220" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <rect x="335" y="20" width="170" height="62" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="420" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#146c3a">Department Reps</text>
            <text x="420" y="68" text-anchor="middle" font-size="11.5" fill="#146c3a">how the work actually happens</text>
            <line x1="420" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="220" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">A council missing an affected department isn't hearing the whole picture</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what do department reps bring?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The risk perspective</li>
            <li class="quiz-option">B. The final approval</li>
            <li class="quiz-option quiz-correct">C. How the work happens ✓</li>
            <li class="quiz-option">D. The firewall rules</li>
        </ul>
        <p class="quiz-explanation"><em>The security lead brings the risk perspective. Department reps bring how the work actually happens.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is the main purpose of a security council or steering committee?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. To handle only technical patching decisions</li>
            <li class="quiz-option quiz-correct">B. To review risk and set security priorities across the whole organization, with cross-department input ✓</li>
            <li class="quiz-option">C. To replace the need for a security policy</li>
            <li class="quiz-option">D. To approve the security team's lunch schedule</li>
        </ul>
        <p class="quiz-explanation"><em>A council's value is bringing multiple departments' perspectives together to set priorities, not just one department deciding alone.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why did Astro pause the vote on the new supply-bay access policy?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because the policy was badly written</li>
            <li class="quiz-option quiz-correct">B. Because nobody from the Supply department, the one actually affected, was in the room ✓</li>
            <li class="quiz-option">C. Because Astro dislikes new policies</li>
            <li class="quiz-option">D. Because the vote happened too quickly</li>
        </ul>
        <p class="quiz-explanation"><em>A decision affecting Supply's daily work, made without anyone from Supply present, risks missing how that department actually operates.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What tends to happen to a policy approved by a council missing a key department's voice?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It always works perfectly regardless</li>
            <li class="quiz-option quiz-correct">B. It can look fine on paper but not survive contact with how that department actually works ✓</li>
            <li class="quiz-option">C. It becomes automatically illegal</li>
            <li class="quiz-option">D. It gets approved faster than usual</li>
        </ul>
        <p class="quiz-explanation"><em>Without the affected department's input, real-world details the council didn't consider can undermine the decision later.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why can't security set priorities in isolation from the departments affected?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Security should always decide alone for speed</li>
            <li class="quiz-option quiz-correct">B. Because departments understand how the work actually happens day to day, which security alone may not see ✓</li>
            <li class="quiz-option">C. Departments have no relevant knowledge to contribute</li>
            <li class="quiz-option">D. It's a Codexia legal requirement with no practical reason</li>
        </ul>
        <p class="quiz-explanation"><em>Departments bring operational reality; without their input, priorities can be set that don't fit how the work is really done.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does Astro mean by "a council that only hears from security isn't a council"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Security should never be part of the council</li>
            <li class="quiz-option quiz-correct">B. A council needs multiple perspectives in the room to be a real governance body, not just security talking to itself ✓</li>
            <li class="quiz-option">C. Councils are unnecessary if security is competent enough alone</li>
            <li class="quiz-option">D. Only department heads should attend, never security</li>
        </ul>
        <p class="quiz-explanation"><em>The whole value of a council is cross-department input — without it, it's just one group's opinion, not shared governance.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Three upcoming council decisions are on the agenda. Read it, then flag which ones are missing a required department's voice.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a4-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm4', 'lesson' => 'lesson03']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the agenda, then classify each decision as
            ready or missing-a-voice.
        </p>
    </div>

</div>
