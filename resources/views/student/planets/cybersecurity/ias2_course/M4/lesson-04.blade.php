{{-- M4 · Lesson 4.4: Working With HR: Onboarding, Offboarding & Separation of Duties — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a4-l4.json) completes the lesson.
Syllabus: ITP1333 Unit D.4 Interaction with HR (onboarding, offboarding, separation of duties). --}}

<div class="lesson-fragment" data-module="m4" data-lesson="04">

    <h1 class="lesson-heading">Lesson 4.4: Working With HR: Onboarding, Offboarding & Separation of Duties</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> carry out an offboarding checklist when a crew member leaves, and identify a separation-of-duties violation where one person can both request and approve the same action.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Security depends on HR events happening on time. <strong>Onboarding</strong> means a new crew member gets
        exactly the access their role needs, no more. <strong>Offboarding</strong> means that when someone leaves
        or changes roles, their old access is removed immediately — not "eventually." An account left active after
        someone departs is a door nobody is watching.
    </p>

    <p class="body-text">
        <strong>Separation of duties</strong> means no single person should be able to both request and approve
        the same sensitive action — like ordering supplies and approving their own order. Splitting the two roles
        between different people means a mistake, or an abuse, needs two people to go wrong instead of one.
    </p>

    <h2 class="section-heading">Rivet's Workbench</h2>

    <p class="body-text">
        A cadet transferred to an allied fleet three days ago, but their Citadel account is still active. Rivet
        checks it — nobody offboarded it, because nobody realized it was their job. Separately, Rivet finds the
        supply cadet can still approve their own requisitions, a gap from before the crew grew. "Two different
        problems," Rivet says, "same root cause: nobody re-checked what a person could still do after their role
        changed."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A role change should trigger an access re-check to remove leftover permissions">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#8a4b00">Role Changes</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#8a4b00">promotion, transfer, departure</text>
            <line x1="170" y1="66" x2="237" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="245,66 233,60 233,72" fill="#7c5cff"/>
            <rect x="245" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Access Re-Checked?</text>
            <text x="320" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">offboarding & separation of duties</text>
            <line x1="395" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">No Leftover Access</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">the goal</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Every role change should trigger a re-check of what that person can still do</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What should happen when a crew member leaves or transfers, from a security standpoint?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing changes until someone notices a problem</li>
            <li class="quiz-option quiz-correct">B. Their old access is removed immediately — offboarding, not "eventually" ✓</li>
            <li class="quiz-option">C. Their account password is simply changed, nothing else</li>
            <li class="quiz-option">D. Only their physical badge needs to be returned</li>
        </ul>
        <p class="quiz-explanation"><em>Offboarding means access is removed right away, not left active until someone happens to notice.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is a "separation of duties" violation?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Two different people approving the same request</li>
            <li class="quiz-option quiz-correct">B. One person being able to both request and approve the same sensitive action ✓</li>
            <li class="quiz-option">C. A department having too many employees</li>
            <li class="quiz-option">D. A policy with too many rules</li>
        </ul>
        <p class="quiz-explanation"><em>Separation of duties requires splitting sensitive actions across two different people — one person doing both defeats the purpose.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why is an account left active after someone departs a security risk?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It isn't a risk — accounts don't need to be disabled</li>
            <li class="quiz-option quiz-correct">B. It's a door nobody is watching — access exists with no legitimate user behind it anymore ✓</li>
            <li class="quiz-option">C. It only affects the departed person, not the organization</li>
            <li class="quiz-option">D. Active accounts automatically expire on their own</li>
        </ul>
        <p class="quiz-explanation"><em>An active account with no legitimate owner is exactly the kind of gap an attacker (or the departed person) could exploit.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why did Rivet call the two problems (leftover account, self-approval) the "same root cause"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They were actually unrelated coincidences</li>
            <li class="quiz-option quiz-correct">B. Both happened because nobody re-checked what a person could still do after their role changed ✓</li>
            <li class="quiz-option">C. Both were caused by a broken firewall</li>
            <li class="quiz-option">D. Both were intentional design choices</li>
        </ul>
        <p class="quiz-explanation"><em>Neither offboarding nor separation-of-duties checks happened when the roles changed — the same missing re-check underlies both.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is the benefit of splitting request and approval between two different people?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It makes the process slower for no reason</li>
            <li class="quiz-option quiz-correct">B. A mistake or abuse now needs two people to go wrong, instead of just one ✓</li>
            <li class="quiz-option">C. It removes the need for any approval at all</li>
            <li class="quiz-option">D. It only matters for very large organizations</li>
        </ul>
        <p class="quiz-explanation"><em>Requiring two different people raises the bar — a single person acting alone can no longer complete the sensitive action unchecked.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> A departed cadet's account is still active, and a separation-of-duties gap is still open. Fix both.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a4-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm4', 'lesson' => 'lesson04']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Disable the departed cadet's account, then lock the
            self-approval gap so no single person can both request and approve.
        </p>
    </div>

</div>
