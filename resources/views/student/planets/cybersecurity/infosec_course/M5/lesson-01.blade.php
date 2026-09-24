{{-- M5 · Lesson 5.1: Confidentiality & Data Integrity — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c5-l1.json) completes the lesson.
Syllabus: ITP1232 Unit E, Confidentiality & Data Integrity. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="01">

    <h1 class="lesson-heading">Lesson 5.1: Confidentiality & Data Integrity</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain the difference between confidentiality and integrity,
        describe what a cryptographic hash is, and use one to detect a file that was silently tampered with.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Confidentiality</strong> means only the right people can read something. <strong>Integrity</strong>
        means nobody changes it without permission, and if they try, you can prove it. A leak breaks confidentiality.
        A silent edit breaks integrity — and integrity attacks are the sneaky ones, because nothing looks broken.
    </p>

    <p class="body-text">
        The tool that catches a silent edit is a <strong>hash</strong>: a short fingerprint computed from a file's
        exact contents. Change even one character and the hash changes completely. Hash the same file twice without
        touching it, and you get the exact same fingerprint both times. So if you hash a file today, save that
        fingerprint, and hash it again next week, a mismatch proves something changed — even if you have no idea
        what, and even if whoever changed it tried to hide it.
    </p>

    <p class="body-text">
        Hashing does not keep a secret secret — that is confidentiality's job, handled by things like encryption
        and access control. Hashing only proves whether something has changed. The two protections are different,
        and the Citadel needs both.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        03:03. Shield control looks normal — power at 100%, arc steady — but <strong>Volt</strong> doesn't trust
        "looks normal" anymore. "Doom didn't break anything loud enough to trip an alarm. If they got in quiet,
        the only way we catch it is by fingerprinting what we've got and comparing it to what we know was clean."
    </p>

    <p class="body-text">
        <strong>Astro</strong> nods. "A quiet change is still a change. Hash the live file. Hash the backup. If
        they don't match, we know exactly where to look — and Rivet can put the clean copy back before it costs us
        anything."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 220" role="img" aria-label="Hashing the live file and the clean backup produces different fingerprints when the live file was tampered with">
            <rect x="20" y="20" width="220" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="130" y="48" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">shield-live.cfg</text>
            <text x="130" y="70" text-anchor="middle" font-size="12.5" fill="#14306b">(tampered, arc changed)</text>

            <rect x="400" y="20" width="220" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="510" y="48" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">backup\shield-live.cfg.bak</text>
            <text x="510" y="70" text-anchor="middle" font-size="12.5" fill="#14306b">(clean copy)</text>

            <line x1="130" y1="90" x2="130" y2="130" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="130,134 124,122 136,122" fill="#7c5cff"/>
            <line x1="510" y1="90" x2="510" y2="130" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="510,134 504,122 516,122" fill="#7c5cff"/>

            <rect x="20" y="138" width="220" height="55" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="130" y="170" text-anchor="middle" font-size="14" font-family="monospace" fill="#7a1a14">hash: 9F03A1...</text>

            <rect x="400" y="138" width="220" height="55" rx="12" fill="#eafaf0" stroke="#1e8e3e" stroke-width="2"/>
            <text x="510" y="170" text-anchor="middle" font-size="14" font-family="monospace" fill="#0d5c26">hash: 4B77E2...</text>

            <text x="320" y="120" text-anchor="middle" font-size="14" font-weight="700" fill="#b3261e">different hashes = something changed</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Doom read the crew's private files without changing anything in them. Which is broken?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Confidentiality ✓</li>
            <li class="quiz-option">B. Integrity</li>
            <li class="quiz-option">C. Availability</li>
            <li class="quiz-option">D. Nothing was broken</li>
        </ul>
        <p class="quiz-explanation"><em>Confidentiality means only the right people can read something. Reading it without permission breaks that, even if nothing was changed.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Someone quietly edited the shield config without permission. Which is broken?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Confidentiality</li>
            <li class="quiz-option quiz-correct">B. Integrity ✓</li>
            <li class="quiz-option">C. Availability</li>
            <li class="quiz-option">D. Authentication</li>
        </ul>
        <p class="quiz-explanation"><em>Integrity means nobody changes something without permission. An unauthorized edit, even a quiet one, breaks integrity.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is a cryptographic hash, in plain terms?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A password used to unlock a file</li>
            <li class="quiz-option quiz-correct">B. A short fingerprint computed from a file's exact contents ✓</li>
            <li class="quiz-option">C. A backup copy stored somewhere else</li>
            <li class="quiz-option">D. A list of who is allowed to read the file</li>
        </ul>
        <p class="quiz-explanation"><em>A hash is a fingerprint: the same input always produces the same hash, and any change to the input changes the hash completely.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. You hash a file today and hash it again next week, and the two hashes don't match. What does that prove?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The file was read by someone unauthorized</li>
            <li class="quiz-option quiz-correct">B. The file's contents changed at some point in between ✓</li>
            <li class="quiz-option">C. The file was deleted and restored</li>
            <li class="quiz-option">D. Nothing — hashes change on their own over time</li>
        </ul>
        <p class="quiz-explanation"><em>A hash only depends on the file's exact contents. A mismatch proves the contents changed; it says nothing about who read it.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Hashing a file protects which of the two?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Confidentiality only</li>
            <li class="quiz-option quiz-correct">B. Integrity, by letting you detect a change ✓</li>
            <li class="quiz-option">C. Both equally</li>
            <li class="quiz-option">D. Neither — hashing is only for passwords</li>
        </ul>
        <p class="quiz-explanation"><em>Hashing doesn't hide anything, so it doesn't protect confidentiality. It lets you detect tampering, which is what protects integrity.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> shield control looks normal, but Volt doesn't trust it. Hash the live shield config
        and the clean backup, confirm they don't match, then restore the clean copy over the tampered one.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c5-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm5', 'lesson' => 'lesson01']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>certutil -hashfile</code> and <code>copy</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
