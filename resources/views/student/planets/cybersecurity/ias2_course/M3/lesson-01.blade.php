{{-- M3 · Lesson 3.1: Security Policies — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a3-l1.json) completes the lesson.
Syllabus: ITP1333 Unit C.1 Security Policies. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="01">

    <h1 class="lesson-heading">Lesson 3.1: Security Policies</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> identify the four parts every security policy needs (purpose, scope, roles, enforcement) and recognize a policy missing one of them.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A security policy is only useful if it actually answers four questions. <strong>Purpose</strong>: why does
        this policy exist? <strong>Scope</strong>: who and what does it cover? <strong>Roles</strong>: who is
        responsible for following it and for enforcing it? <strong>Enforcement</strong>: what happens if it isn't
        followed? A policy missing any one of these four tends to be ignored, because nobody is quite sure it
        applies to them or that it's actually enforced.
    </p>

    <p class="body-text">
        A policy is deliberately high-level — it states the "what" and "why," not the exact steps. The specific,
        checkable rules come later, in standards; the exact steps come later still, in procedures (both covered
        next).
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Which question does a policy's scope answer?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Why the policy exists</li>
            <li class="quiz-option quiz-correct">B. Who and what it covers ✓</li>
            <li class="quiz-option">C. What happens if broken</li>
            <li class="quiz-option">D. The exact steps to take</li>
        </ul>
        <p class="quiz-explanation"><em>Purpose says why, scope says who and what, roles say who enforces, enforcement says what happens if broken.</em></p>
    </div>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Rivet drafts a one-line policy: "Protect crew data." Astro hands it back. "This tells nobody what to do.
        Who does it cover — every crew member, or just command staff? What happens if someone ignores it? Add the
        four pieces: why we protect crew data, who it applies to, who enforces it, and what happens if it's
        broken. Then it's a policy the crew can actually use."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What was wrong with Rivet's policy “Protect crew data”?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It covered too many people</li>
            <li class="quiz-option">B. It was far too long to read</li>
            <li class="quiz-option">C. It listed too many exact steps</li>
            <li class="quiz-option quiz-correct">D. It was missing the four pieces ✓</li>
        </ul>
        <p class="quiz-explanation"><em>It tells nobody what to do. It needs purpose, scope, roles and enforcement.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A complete security policy needs purpose, scope, roles, and enforcement">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Purpose</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">why it exists</text>
            <line x1="170" y1="66" x2="162" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="170,66 158,60 158,72" fill="#7c5cff"/>
            <rect x="170" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="245" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">Scope</text>
            <text x="245" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">who & what it covers</text>
            <line x1="320" y1="66" x2="312" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="320,66 308,60 308,72" fill="#7c5cff"/>
            <rect x="320" y="30" width="150" height="72" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="395" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#8a4b00">Roles</text>
            <text x="395" y="82" text-anchor="middle" font-size="12.5" fill="#8a4b00">who enforces it</text>
            <line x1="470" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Enforcement</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">what happens if broken</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">A policy missing any one of these four tends to be ignored</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, which piece says who enforces it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Scope</li>
            <li class="quiz-option">B. Purpose</li>
            <li class="quiz-option quiz-correct">C. Roles ✓</li>
            <li class="quiz-option">D. Enforcement</li>
        </ul>
        <p class="quiz-explanation"><em>Roles say who is responsible for following and enforcing the policy.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does the "purpose" of a policy answer?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The exact steps to follow</li>
            <li class="quiz-option quiz-correct">B. Why the policy exists ✓</li>
            <li class="quiz-option">C. Who enforces the policy</li>
            <li class="quiz-option">D. What punishment applies</li>
        </ul>
        <p class="quiz-explanation"><em>Purpose is the "why" — the reason the policy exists in the first place.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does the "scope" of a policy answer?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Why the policy exists</li>
            <li class="quiz-option quiz-correct">B. Who and what the policy covers ✓</li>
            <li class="quiz-option">C. The exact technical settings required</li>
            <li class="quiz-option">D. The budget for the policy</li>
        </ul>
        <p class="quiz-explanation"><em>Scope defines the boundary — who is covered and what systems or data are included.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Astro reject Rivet's one-line policy, "Protect crew data"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It was too long</li>
            <li class="quiz-option quiz-correct">B. It was missing scope, roles, and enforcement — nobody could tell who it applies to or what happens if it's broken ✓</li>
            <li class="quiz-option">C. It used the wrong font</li>
            <li class="quiz-option">D. Astro dislikes short sentences</li>
        </ul>
        <p class="quiz-explanation"><em>A bare purpose statement with no scope, roles, or enforcement leaves too much unclear to actually be followed.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What is the relationship between a policy and a standard?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They are the same thing</li>
            <li class="quiz-option quiz-correct">B. A policy is high-level ("what and why"); a standard makes it specific and checkable ✓</li>
            <li class="quiz-option">C. A standard always comes before the policy</li>
            <li class="quiz-option">D. Standards replace policies entirely</li>
        </ul>
        <p class="quiz-explanation"><em>A policy sets direction; specific, checkable rules come later as standards.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does "enforcement" in a policy describe?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The purpose of the policy</li>
            <li class="quiz-option quiz-correct">B. What happens if the policy isn't followed ✓</li>
            <li class="quiz-option">C. The list of systems covered</li>
            <li class="quiz-option">D. The technical settings required</li>
        </ul>
        <p class="quiz-explanation"><em>Enforcement is the consequence piece — what happens when the policy is violated.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Rivet's draft policy is missing pieces. Read it, then classify which of the four parts (purpose, scope, roles, enforcement) each is missing.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a3-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm3', 'lesson' => 'lesson01']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the draft policy, then classify which required part each
            gap is missing.
        </p>
    </div>

</div>
