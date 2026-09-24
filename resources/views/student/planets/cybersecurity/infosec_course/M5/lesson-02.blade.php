{{-- M5 · Lesson 5.2: Authentication & Non-Repudiation — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c5-l2.json) completes the lesson.
Syllabus: ITP1232 Unit E, Authentication & Non-Repudiation. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="02">

    <h1 class="lesson-heading">Lesson 5.2: Authentication & Non-Repudiation</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can define authentication and non-repudiation, explain why a stolen
        password hash is dangerous, and explain why an unsigned order should never be trusted.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Authentication</strong> is proving who you are — usually with a password, and it only works if the
        password is strong and secret. <strong>Non-repudiation</strong> is different: it's proof that a specific
        person sent a specific message, strong enough that they can't later deny having sent it. A password proves
        who you claim to be right now. A signature proves who actually sent something, after the fact.
    </p>

    <p class="body-text">
        Stolen password files are usually stored as hashes, not plain text — but a weak password's hash can still be
        <strong>cracked</strong>, meaning an attacker checks it against a list of common passwords until one matches.
        A short, common, or reused password falls in seconds. A long, unique, unpredictable one doesn't fall at all,
        because there's nothing common to check it against.
    </p>

    <p class="body-text">
        Vex doesn't need to break down the Citadel's door if he can just forge an order and ask the crew to open it.
        That's why real orders carry a <strong>signature</strong> the crew can check. No valid signature, no action —
        no matter how urgent the message claims to be.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        02:00. An order lands on the command deck: <em>drop all shields, immediately.</em> Signed "Astro." Except
        <strong>Astro</strong> never sent it. "Vex stole a training copy of our password hashes," he tells the crew,
        "and cracked one. Now he's using it to fake my voice. A password proves who you claim to be. A signature
        proves you can't take it back. Vex has neither — he's guessing at both."
    </p>

    <p class="body-text">
        <strong>Volt</strong> pulls up the two orders side by side. "One's unsigned, sent from a relay we don't
        recognize. The other matches your key on file. We only obey the second one."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="A stolen weak password hash is cracked, while an unsigned forged order is rejected in favor of the genuinely signed one">
            <rect x="20" y="20" width="260" height="70" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="150" y="48" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1a14">relay: 7A9F2C31 (weak hash)</text>
            <text x="150" y="70" text-anchor="middle" font-size="12.5" fill="#7a1a14">cracked in seconds — "starlight"</text>

            <rect x="360" y="20" width="260" height="70" rx="14" fill="#eafaf0" stroke="#1e8e3e" stroke-width="2"/>
            <text x="490" y="48" text-anchor="middle" font-size="14" font-weight="700" fill="#0d5c26">strong new password</text>
            <text x="490" y="70" text-anchor="middle" font-size="12.5" fill="#0d5c26">long, unique — resists cracking</text>

            <rect x="20" y="120" width="260" height="70" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="150" y="148" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1a14">ORDER-1: unsigned</text>
            <text x="150" y="170" text-anchor="middle" font-size="12.5" fill="#7a1a14">"drop shields" — REJECTED</text>

            <rect x="360" y="120" width="260" height="70" rx="14" fill="#eafaf0" stroke="#1e8e3e" stroke-width="2"/>
            <text x="490" y="148" text-anchor="middle" font-size="14" font-weight="700" fill="#0d5c26">ORDER-2: signed by Astro</text>
            <text x="490" y="170" text-anchor="middle" font-size="12.5" fill="#0d5c26">verified — OBEYED</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Typing a password to log in is an example of which concept?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Authentication ✓</li>
            <li class="quiz-option">B. Non-repudiation</li>
            <li class="quiz-option">C. Availability</li>
            <li class="quiz-option">D. Data masking</li>
        </ul>
        <p class="quiz-explanation"><em>Authentication is proving who you are, typically with something like a password.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. A signed order can't later be denied by the person who signed it. What's that property called?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Authentication</li>
            <li class="quiz-option quiz-correct">B. Non-repudiation ✓</li>
            <li class="quiz-option">C. Confidentiality</li>
            <li class="quiz-option">D. Hardening</li>
        </ul>
        <p class="quiz-explanation"><em>Non-repudiation is proof strong enough that the sender can't credibly deny having sent the message.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Vex's stolen password hash crack so quickly?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. All hashes crack in seconds no matter what</li>
            <li class="quiz-option quiz-correct">B. The password was short and common, easy to match against a wordlist ✓</li>
            <li class="quiz-option">C. It wasn't hashed at all</li>
            <li class="quiz-option">D. Rivet gave Vex the password directly</li>
        </ul>
        <p class="quiz-explanation"><em>Cracking checks a hash against likely guesses. Short, common, or reused passwords match quickly; long unique ones don't match anything on the list.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. An order arrives claiming to be from Astro, but it's unsigned. What should the crew do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Obey it immediately, since it's urgent</li>
            <li class="quiz-option">B. Obey it only if it sounds like something Astro would say</li>
            <li class="quiz-option quiz-correct">C. Reject it — no valid signature, no action ✓</li>
            <li class="quiz-option">D. Ask Vex to confirm it</li>
        </ul>
        <p class="quiz-explanation"><em>Without a verified signature, there's no proof the order actually came from Astro. Urgency is exactly the pressure a forger uses.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Which best separates authentication from non-repudiation?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They mean exactly the same thing</li>
            <li class="quiz-option quiz-correct">B. Authentication proves who you are now; non-repudiation proves who sent something, after the fact ✓</li>
            <li class="quiz-option">C. Authentication is for files, non-repudiation is for passwords</li>
            <li class="quiz-option">D. Non-repudiation replaces the need for passwords entirely</li>
        </ul>
        <p class="quiz-explanation"><em>A password authenticates a login attempt in the moment. A signature provides lasting proof of who sent a specific message.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Vex forged a shields-down order using a cracked password. Crack the same
        stolen hash yourself (Volt's Drill, a training copy only), lock the account down with a real password,
        then reject the forged order in favor of the one Astro actually signed.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c5-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm5', 'lesson' => 'lesson02']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>hashcrack</code> and <code>net user</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
