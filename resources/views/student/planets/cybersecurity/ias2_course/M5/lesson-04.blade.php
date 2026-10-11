{{-- M5 · Lesson 5.4: Client Application Security — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a5-l4.json) completes the lesson.
Syllabus: ITP1333 Unit E.4 Client Application Security. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="04">

    <h1 class="lesson-heading">Lesson 5.4: Client Application Security</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> explain why client-side checks alone are not enough, and identify safe versus risky client application practices.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>client application</strong> — the program running on a user's own device — cannot be fully
        trusted, because the user (or an attacker on that device) controls everything happening there. A check that
        only runs on the client can always be bypassed by someone who controls the client. The rule: the client can
        offer convenience (instant feedback, a friendlier interface), but every rule that actually matters must
        also be enforced again on the server, where the user can't tamper with it.
    </p>

    <p class="body-text">
        Other client-side risks worth watching: storing a password or session token in an easily readable local
        file, and a session that never expires, letting anyone who later uses that device continue as the last
        user forever.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Rivet's new supply app checks a requisition's total client-side, before sending it. "Fast," Rivet says,
        "instant feedback." Volt points out the gap: "Fast, sure — but nothing stops someone from editing the
        request after the client-side check passes, before it reaches the server. If the server doesn't check the
        total again, the client-side check was just a suggestion, not a rule."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A client-side check can be bypassed; the server must enforce the rule again">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#8a4b00">Client Check</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#8a4b00">convenience only</text>
            <line x1="170" y1="66" x2="237" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="245,66 233,60 233,72" fill="#7c5cff"/>
            <rect x="245" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">Can Be Bypassed</text>
            <text x="320" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">user controls their own device</text>
            <line x1="395" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Server Check</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">the rule that actually holds</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">A client-side check is a convenience, not a rule — the server has to check again</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why can't a client application be fully trusted?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because client applications are always slower</li>
            <li class="quiz-option quiz-correct">B. Because the user, or an attacker on that device, controls everything happening on the client ✓</li>
            <li class="quiz-option">C. Because clients never have an interface</li>
            <li class="quiz-option">D. Because servers are always more secure by default</li>
        </ul>
        <p class="quiz-explanation"><em>Anything running on a device the user controls can be modified or bypassed by that user.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is the purpose of a client-side check, if it can be bypassed?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It has no legitimate purpose at all</li>
            <li class="quiz-option quiz-correct">B. It offers convenience, like instant feedback, but must be backed up by a server-side check ✓</li>
            <li class="quiz-option">C. It replaces the need for any server-side validation</li>
            <li class="quiz-option">D. It is the only check that matters</li>
        </ul>
        <p class="quiz-explanation"><em>Client-side checks are useful for user experience, but any rule that actually matters must be enforced again on the server.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What did Volt point out was wrong with Rivet's client-only total check?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It was too slow</li>
            <li class="quiz-option quiz-correct">B. Someone could edit the request after the client check passed, and the server never checked again ✓</li>
            <li class="quiz-option">C. The client-side interface looked unprofessional</li>
            <li class="quiz-option">D. There was nothing wrong with it</li>
        </ul>
        <p class="quiz-explanation"><em>Without a matching server-side check, the client-side validation is only a suggestion an attacker can simply skip.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why is storing a password in an easily readable local file a risk?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It isn't a risk at all</li>
            <li class="quiz-option quiz-correct">B. Anyone with access to that device or file can read the password directly ✓</li>
            <li class="quiz-option">C. It only matters for very old devices</li>
            <li class="quiz-option">D. It makes the application load faster</li>
        </ul>
        <p class="quiz-explanation"><em>A readable local file exposes the credential to anyone who can access that file on the device.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is the risk of a session that never expires?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It saves battery life</li>
            <li class="quiz-option quiz-correct">B. Anyone who later uses that device can continue acting as the previous user indefinitely ✓</li>
            <li class="quiz-option">C. It has no downside at all</li>
            <li class="quiz-option">D. It only affects the application's loading speed</li>
        </ul>
        <p class="quiz-explanation"><em>A session with no expiry leaves the account open to whoever next uses the device, with no time limit.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> The supply app's total check only runs client-side. Read the design note, then classify each proposed fix as safe or still-risky.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a5-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm5', 'lesson' => 'lesson04']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the design note, then classify each proposed fix.
        </p>
    </div>

</div>
