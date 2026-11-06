{{-- M3 · Lesson 3.2: Security Standards — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a3-l2.json) completes the lesson.
Syllabus: ITP1333 Unit C.2 Security Standards. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="02">

    <h1 class="lesson-heading">Lesson 3.2: Security Standards</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> describe a standard as a specific, mandatory rule that supports a policy, and apply one (a password standard) to bring an account into compliance.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>standard</strong> takes a policy's high-level direction and turns it into something specific and
        checkable. "Protect crew data" is a policy. "Passwords must be at least 12 characters, mix upper and lower
        case, a number and a symbol, and never contain the account name" is a standard — mandatory, precise, and
        easy to verify: an account either meets it or it doesn't.
    </p>

    <p class="body-text">
        Standards are what auditors actually check. A policy is hard to fail an audit on, because it's abstract;
        a standard either holds or it doesn't, account by account, system by system.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Why do auditors check standards, not policies?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A policy is always far too specific</li>
            <li class="quiz-option">B. Standards are written by the auditors</li>
            <li class="quiz-option quiz-correct">C. A standard either holds or it doesn't ✓</li>
            <li class="quiz-option">D. Policies are never written down at all</li>
        </ul>
        <p class="quiz-explanation"><em>A policy is abstract. A standard is precise and checkable, account by account.</em></p>
    </div>

    <h2 class="section-heading">Rivet's Workbench</h2>

    <p class="body-text">
        Rivet pulls up the relay account. "Password's 'starlight' — eight letters, no number, no symbol. Fails the
        standard on three counts. The policy says protect crew data; the standard says exactly what that means for
        a password. Let's bring this account into compliance right now, not just note it for later."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">How many counts does “starlight” fail the standard on?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. One</li>
            <li class="quiz-option">B. Two</li>
            <li class="quiz-option quiz-correct">C. Three ✓</li>
            <li class="quiz-option">D. None at all</li>
        </ul>
        <p class="quiz-explanation"><em>Eight letters (too short), no number, no symbol: three counts.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A policy becomes a checkable standard, which an account either meets or fails">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Policy</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">"protect crew data"</text>
            <line x1="170" y1="66" x2="237" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="245,66 233,60 233,72" fill="#7c5cff"/>
            <rect x="245" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">Standard</text>
            <text x="320" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">12+ chars, mixed case, number, symbol</text>
            <line x1="395" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Compliant Account</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">checked, passes</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">A standard turns a policy into something you can actually check</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what does the standard require?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 8 chars, all lower case, no symbols at all</li>
            <li class="quiz-option">B. The account name inside the password</li>
            <li class="quiz-option">C. Any password the crew member likes best</li>
            <li class="quiz-option quiz-correct">D. 12+ chars, mixed case, number, symbol ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The standard turns “protect crew data” into something checkable: 12+ characters, mixed case, a number and a symbol.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is a "standard," compared to a policy?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A synonym for policy, used interchangeably</li>
            <li class="quiz-option quiz-correct">B. A specific, mandatory, checkable rule that supports the policy's direction ✓</li>
            <li class="quiz-option">C. A recommendation that's optional to follow</li>
            <li class="quiz-option">D. The exact click-by-click steps for a task</li>
        </ul>
        <p class="quiz-explanation"><em>A standard is specific and mandatory — it's what the policy's direction actually requires in practice.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why are standards what auditors actually check, rather than policies?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Auditors are only interested in policies</li>
            <li class="quiz-option quiz-correct">B. A standard either holds or it doesn't, account by account — a policy is too abstract to fail an audit on directly ✓</li>
            <li class="quiz-option">C. Standards are always shorter than policies</li>
            <li class="quiz-option">D. Policies are classified and cannot be audited</li>
        </ul>
        <p class="quiz-explanation"><em>A specific, checkable rule can be verified pass/fail; a high-level policy statement can't be checked the same concrete way.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which of these is an example of a password standard?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. "Protect crew data"</li>
            <li class="quiz-option quiz-correct">B. "Passwords must be 12+ characters, mixed case, a number and a symbol" ✓</li>
            <li class="quiz-option">C. "Be careful with passwords"</li>
            <li class="quiz-option">D. "Passwords are optional"</li>
        </ul>
        <p class="quiz-explanation"><em>This is specific and checkable — exactly what a standard requires, unlike a vague policy statement.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why did the relay account's password fail the standard?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It was too long</li>
            <li class="quiz-option quiz-correct">B. It was too short and had no number or symbol ✓</li>
            <li class="quiz-option">C. It matched the account name</li>
            <li class="quiz-option">D. It was changed too recently</li>
        </ul>
        <p class="quiz-explanation"><em>"starlight" is eight characters with no number or symbol — failing the length and complexity requirements.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What should happen once an account is found to fail a standard?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Note it for a future review, no action needed now</li>
            <li class="quiz-option quiz-correct">B. Bring it into compliance right away, not just log the finding ✓</li>
            <li class="quiz-option">C. Ignore it since it's only a standard, not a law</li>
            <li class="quiz-option">D. Delete the account entirely</li>
        </ul>
        <p class="quiz-explanation"><em>Rivet's approach — fix the account now rather than just noting the gap — is what makes a standard meaningful in practice.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> The relay account fails the password standard. Bring it into compliance.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a3-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm3', 'lesson' => 'lesson02']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the standard, then set a password that actually meets it.
        </p>
    </div>

</div>
