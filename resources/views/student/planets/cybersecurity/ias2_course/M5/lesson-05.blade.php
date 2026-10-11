{{-- M5 · Lesson 5.5: Remote Administration Security — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a5-l5.json) completes the lesson.
Syllabus: ITP1333 Unit E.5 Remote Administration Security. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="05">

    <h1 class="lesson-heading">Lesson 5.5: Remote Administration Security</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> secure a remote administration channel by closing an unencrypted legacy protocol and enforcing a strong credential on the remaining admin account.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Remote administration</strong> — managing a system from somewhere else — is powerful and
        dangerous in equal measure, because whoever controls that channel can control the system. Two rules keep
        it defensible: only ever administer remotely over an <strong>encrypted</strong> channel (never an old,
        unencrypted protocol where commands and passwords travel in the clear), and hold every admin account to
        the same strong-password standard as everything else — a weak admin password undoes every other control on
        the system it manages.
    </p>

    <p class="body-text">
        A legacy unencrypted admin port left open "just in case it's still needed" is one of the most common ways
        Doom-style attackers get in — it's rarely monitored, and anything sent over it can be read by anyone
        listening on the way.
    </p>

    <h2 class="section-heading">Rivet's Workbench</h2>

    <p class="body-text">
        Rivet finds an old unencrypted remote admin port still open on the shield generator, left over from a
        console retired two years ago. "Nobody uses this anymore," Rivet says, "but it's still listening, and
        anything sent over it — including an admin password — travels in the clear. And the encrypted admin
        account still using its installer's default password doesn't help either." Both close today.
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="Close the unencrypted legacy admin port and enforce a strong password on the remaining admin account">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">Legacy Unencrypted Port</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">commands travel in the clear</text>
            <line x1="170" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Close It</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">+ enforce a strong admin password</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">An open legacy admin channel plus a weak password undoes every other control</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why is remote administration especially dangerous if not secured properly?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It's slower than local administration</li>
            <li class="quiz-option quiz-correct">B. Whoever controls that channel can control the system it manages ✓</li>
            <li class="quiz-option">C. It only works on weekends</li>
            <li class="quiz-option">D. It has no legitimate use at all</li>
        </ul>
        <p class="quiz-explanation"><em>Remote admin access is powerful by design — that power is exactly why the channel controlling it must be tightly secured.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why should remote administration only ever happen over an encrypted channel?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Encryption makes commands run faster</li>
            <li class="quiz-option quiz-correct">B. An unencrypted channel lets anyone listening read commands and passwords as they travel ✓</li>
            <li class="quiz-option">C. Encrypted channels are required by tradition only, with no practical benefit</li>
            <li class="quiz-option">D. It doesn't matter as long as the password is strong</li>
        </ul>
        <p class="quiz-explanation"><em>Without encryption, anything sent over the channel — including credentials — can be intercepted in plain, readable form.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Rivet close the legacy unencrypted admin port instead of leaving it "just in case"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because it was using too much bandwidth</li>
            <li class="quiz-option quiz-correct">B. Because it was rarely monitored and let admin traffic travel in the clear — a common way attackers get in ✓</li>
            <li class="quiz-option">C. Because Astro personally dislikes old ports</li>
            <li class="quiz-option">D. Because closing ports is required by every software license</li>
        </ul>
        <p class="quiz-explanation"><em>An unused, unmonitored, unencrypted admin channel is a classic entry point — closing it removes a real risk with no legitimate downside.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why does a weak admin password undo every other security control on a system?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It doesn't — other controls remain effective regardless</li>
            <li class="quiz-option quiz-correct">B. Because an admin account typically has the power to change or disable most other controls ✓</li>
            <li class="quiz-option">C. Weak passwords only affect the account itself, nothing else</li>
            <li class="quiz-option">D. Admin accounts are never a security concern</li>
        </ul>
        <p class="quiz-explanation"><em>Admin-level access usually grants the ability to modify or turn off other protections, so a weak admin password is a single point of failure.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What are the two fixes Rivet applied to secure remote administration on the shield generator?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Only closing the legacy port, nothing else</li>
            <li class="quiz-option quiz-correct">B. Closing the legacy unencrypted port and enforcing a strong password on the remaining admin account ✓</li>
            <li class="quiz-option">C. Only changing the admin password, leaving the port open</li>
            <li class="quiz-option">D. Disabling remote administration entirely, with no alternative</li>
        </ul>
        <p class="quiz-explanation"><em>Both fixes were needed together — closing the exposed channel and hardening the credential that still had access.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> The shield generator has a legacy unencrypted admin port still open, and its remaining admin account still uses a default password. Fix both.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a5-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm5', 'lesson' => 'lesson05']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Close the legacy port, then set a strong password on the
            remaining admin account.
        </p>
    </div>

</div>
