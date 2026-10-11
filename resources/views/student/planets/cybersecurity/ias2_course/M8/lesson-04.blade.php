{{-- M8 · Lesson 8.4: Locks & Entry Controls — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a8-l4.json) completes the lesson.
Syllabus: ITP1333 Unit H.4 Locks and Entry Controls (Biometrics, Smart Cards). --}}

<div class="lesson-fragment" data-module="m8" data-lesson="04">

    <h1 class="lesson-heading">Lesson 8.4: Locks & Entry Controls</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> apply defense in depth to physical entry control by layering a badge system with a stronger control for the most sensitive area, and revoke a lost credential immediately.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Physical entry controls come in layers, same as network defense: a basic lock for a general area, a
        <strong>smart card</strong> or badge system for staff-only zones (each card tied to one person, so entry is
        logged and traceable), and <strong>biometrics</strong> (fingerprint, retina) for the most sensitive spaces,
        since a biometric can't be lost, lent, or stolen the way a card can.
    </p>

    <p class="body-text">
        A lost or stolen credential is an emergency, not a paperwork item — the entry control it unlocked has to be
        treated as compromised until the credential is revoked and, if it's a shared secret like a code, changed.
    </p>

    <h2 class="section-heading">Rivet's Workbench</h2>

    <p class="body-text">
        A cadet reports their access card missing. Rivet doesn't wait for the end of shift. "That card opens the
        engineering bay. Until I revoke it, anyone holding it walks in as you." Rivet disables it on the spot, then
        checks whether the vault behind it — biometric-only — was ever reachable with just a card in the first
        place. It wasn't. "Good. The most sensitive door was never just one layer deep."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 196" role="img" aria-label="Physical entry controls layer from a basic lock, to a badge system, to biometrics for the most sensitive area">
            <rect x="40" y="20" width="560" height="38" rx="10" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="320" y="44" text-anchor="middle" font-size="14.5" font-weight="700" fill="#1c4f8f">General Area — basic lock</text>
            <rect x="40" y="66" width="560" height="38" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="90" text-anchor="middle" font-size="14.5" font-weight="700" fill="#4a35b8">Staff-Only Zone — smart card / badge, logged</text>
            <rect x="40" y="112" width="560" height="38" rx="10" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="320" y="136" text-anchor="middle" font-size="14.5" font-weight="700" fill="#146c3a">Most Sensitive Area — biometric — can't be lost or lent</text>
            <text x="320" y="182" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">A lost card is an emergency the moment it's reported, not at end of shift</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why is a badge or smart card system an improvement over a basic shared lock?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Badges are cheaper to manufacture</li>
            <li class="quiz-option quiz-correct">B. Each card is tied to one person, so entry can be logged and traced to an individual ✓</li>
            <li class="quiz-option">C. Badges never need to be replaced</li>
            <li class="quiz-option">D. Badges eliminate the need for any other control</li>
        </ul>
        <p class="quiz-explanation"><em>Unlike a shared key or code, a badge ties access to a specific individual, supporting traceability.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why is biometric access control used for the most sensitive areas?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It's the cheapest option available</li>
            <li class="quiz-option quiz-correct">B. A biometric can't be lost, lent, or stolen the way a physical card can ✓</li>
            <li class="quiz-option">C. Biometrics require no maintenance</li>
            <li class="quiz-option">D. Biometrics are always faster than a card reader</li>
        </ul>
        <p class="quiz-explanation"><em>Biometrics are tied directly to the person, removing the risk of a credential being lost or handed off, unlike a card.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Rivet disable the missing card immediately instead of waiting until end of shift?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because Rivet had nothing else to do</li>
            <li class="quiz-option quiz-correct">B. Because a lost credential means anyone holding it can enter as that person, until it's revoked ✓</li>
            <li class="quiz-option">C. Because cards expire automatically at end of shift anyway</li>
            <li class="quiz-option">D. Because policy requires disabling all cards daily</li>
        </ul>
        <p class="quiz-explanation"><em>A missing credential represents an active risk the moment it's lost — delaying revocation extends that window of exposure.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why was Rivet relieved that the vault behind the missing card was biometric-only?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because it meant the card was never actually missing</li>
            <li class="quiz-option quiz-correct">B. Because the missing card alone could never have opened the most sensitive door — that layer required more than a card ✓</li>
            <li class="quiz-option">C. Because biometric locks never fail</li>
            <li class="quiz-option">D. Because it meant the vault didn't need any protection</li>
        </ul>
        <p class="quiz-explanation"><em>Layering meant the most sensitive area wasn't exposed by the loss of a single, lower-tier credential.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is the physical security equivalent of "defense in depth" described in this lesson?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Using the exact same lock on every door in the facility</li>
            <li class="quiz-option quiz-correct">B. Layering different kinds of entry control — basic lock, badge, biometric — matched to each area's sensitivity ✓</li>
            <li class="quiz-option">C. Removing all locks to simplify access</li>
            <li class="quiz-option">D. Only ever using biometric locks everywhere</li>
        </ul>
        <p class="quiz-explanation"><em>Just as with technical defense in depth, physical security layers different, independent controls rather than repeating one.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> A cadet's access card is missing. Revoke it immediately, then confirm the vault behind it was never reachable with a card alone.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a8-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm8', 'lesson' => 'lesson04']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Disable the missing card's account right away.
        </p>
    </div>

</div>
