{{-- M2 · Lesson 2.5: Computer Security — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c2-l5.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="05">

    <h1 class="lesson-heading">Lesson 2.5: Computer Security</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can name the basic steps of hardening a computer — disabling unused
        accounts, enforcing strong passwords, and stopping unnecessary services — and apply all three yourself.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Computer security</strong> (sometimes called endpoint hardening) is the set of basic steps that
        make a single machine harder to break into, before anything specific has even gone wrong. It's the
        equivalent of locking your doors and windows before you leave the house, not just calling the police
        after someone gets in.
    </p>

    <p class="body-text">
        Three habits do most of the work:
    </p>

    <ul class="body-list">
        <li><strong>Disable unused accounts</strong>, especially built-in ones like Guest — an account nobody uses is an account nobody's watching either.</li>
        <li><strong>Enforce strong passwords</strong> on every account that stays active — at least 12 characters, mixed case, a number, a symbol, nothing guessable.</li>
        <li><strong>Stop unnecessary services</strong> — a program running that nobody needs anymore is a door nobody remembered to lock.</li>
    </ul>

    <p class="body-text">
        None of these three fixes one specific attack. Together, they shrink the number of ways in — which is
        exactly why attackers look for the machine that skipped them.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        Doom's next target catches <strong>Volt</strong> off guard: "It's my own post. My guard station. Vex is
        going after the machine that's supposed to be watching everything else." A quick look confirms it: the
        Guest account is still enabled from setup, Volt's own password was never changed from the default, and an
        old relay-legacy service nobody uses is still running, quietly listening on a port.
    </p>

    <p class="body-text">
        <strong>Astro</strong>: "Even the shield guard leaves a door open. Especially the shield guard — that's
        exactly why Vex is targeting it. Harden your own post first, Volt. If the watchtower isn't secure, nothing
        it watches is either." Volt doesn't argue: disable Guest, set a real password, kill the legacy service —
        then test it. Try to log in as Guest again. If it fails, the wall holds.
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="Three hardening steps on Volt's guard post: disable Guest, strong password for volt, stop relay-legacy service">
            <rect x="20" y="20" width="600" height="34" rx="10" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="320" y="42" text-anchor="middle" font-size="13" font-weight="700" fill="#7a1a14">Before: Guest enabled · weak volt password · relay-legacy running</text>

            <line x1="150" y1="54" x2="150" y2="80" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="150,84 144,72 156,72" fill="#7c5cff"/>
            <line x1="320" y1="54" x2="320" y2="80" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="320,84 314,72 326,72" fill="#7c5cff"/>
            <line x1="490" y1="54" x2="490" y2="80" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="490,84 484,72 496,72" fill="#7c5cff"/>

            <rect x="40" y="88" width="220" height="46" rx="10" fill="#e9f7ef" stroke="#1f7a4d" stroke-width="2"/>
            <text x="150" y="108" text-anchor="middle" font-size="12" font-weight="700" fill="#175a3a">net user guest /active:no</text>
            <text x="150" y="124" text-anchor="middle" font-size="11" fill="#175a3a">Guest disabled</text>

            <rect x="210" y="88" width="220" height="46" rx="10" fill="#e9f7ef" stroke="#1f7a4d" stroke-width="2" transform="translate(0,0)"/>
            <text x="320" y="108" text-anchor="middle" font-size="12" font-weight="700" fill="#175a3a">net user volt &lt;new-pw&gt;</text>
            <text x="320" y="124" text-anchor="middle" font-size="11" fill="#175a3a">strong password set</text>

            <rect x="380" y="88" width="220" height="46" rx="10" fill="#e9f7ef" stroke="#1f7a4d" stroke-width="2"/>
            <text x="490" y="108" text-anchor="middle" font-size="12" font-weight="700" fill="#175a3a">taskkill relaylegacy.exe</text>
            <text x="490" y="124" text-anchor="middle" font-size="11" fill="#175a3a">legacy service stopped</text>

            <rect x="140" y="150" width="360" height="34" rx="10" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="320" y="172" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">Volt's Drill: login guest anything → fails</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is computer security / hardening?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Only reacting after an attack happens</li>
            <li class="quiz-option quiz-correct">B. Basic steps that make a machine harder to break into before anything has gone wrong ✓</li>
            <li class="quiz-option">C. Buying a faster computer</li>
            <li class="quiz-option">D. Deleting all user accounts</li>
        </ul>
        <p class="quiz-explanation"><em>Hardening is proactive: locking down a machine's obvious weaknesses ahead of time, not just responding to an incident.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why disable an unused built-in account like Guest?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It saves disk space</li>
            <li class="quiz-option quiz-correct">B. An account nobody uses is also an account nobody's watching — a door left unlocked ✓</li>
            <li class="quiz-option">C. Guest accounts are illegal</li>
            <li class="quiz-option">D. It makes the computer faster</li>
        </ul>
        <p class="quiz-explanation"><em>Unused accounts are prime targets precisely because their activity doesn't stand out — nobody expects them to be used at all.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Volt's own password had never been changed from the default. What hardening step fixes that?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Disabling the account entirely</li>
            <li class="quiz-option quiz-correct">B. Enforcing a strong password (12+ characters, mixed case, a number, a symbol) ✓</li>
            <li class="quiz-option">C. Renaming the account</li>
            <li class="quiz-option">D. Nothing; default passwords are fine if nobody knows them</li>
        </ul>
        <p class="quiz-explanation"><em>Default or weak passwords are one of the first things an attacker tries — setting a real, strong password closes that gap.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why stop the old relay-legacy service if nobody uses it anymore?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It has no effect on security either way</li>
            <li class="quiz-option quiz-correct">B. A running service nobody needs is a door nobody remembered to lock — still an entry point ✓</li>
            <li class="quiz-option">C. It's required to free up memory</li>
            <li class="quiz-option">D. Old services are automatically disabled by Windows</li>
        </ul>
        <p class="quiz-explanation"><em>Any listening service is a potential way in, whether or not anyone still uses it for its original purpose.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. After hardening, Volt tries `login guest anything` again. What proves the fix worked?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The login succeeds, showing Guest still works for convenience</li>
            <li class="quiz-option quiz-correct">B. The login fails, proving the disabled account can no longer be used ✓</li>
            <li class="quiz-option">C. The computer restarts</li>
            <li class="quiz-option">D. Nothing; you can't test a fix without an attacker</li>
        </ul>
        <p class="quiz-explanation"><em>Re-testing your own fix (Volt's Drill) is how you confirm the door is actually shut, not just assumed to be.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Vex is targeting Volt's own guard post, the weakest machine in the Citadel.
        Disable the Guest account, give Volt a strong password, stop the unused relay-legacy service, then
        confirm the fix by trying to log in as Guest again. <strong>Success:</strong> every objective in the
        simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c2-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm2', 'lesson' => 'lesson05']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. You type Windows commands there (Command Prompt), such as
            <code>net user</code>, <code>taskkill</code> and <code>login</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
