{{-- M8 · Lesson 8.1: Classification of Assets — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a8-l1.json) completes the lesson.
Syllabus: ITP1333 Unit H.1 Classification of Assets. --}}

<div class="lesson-fragment" data-module="m8" data-lesson="01">

    <h1 class="lesson-heading">Lesson 8.1: Classification of Assets</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> classify a physical or informational asset into a sensitivity tier (public, internal, confidential, or restricted) so its protection matches its actual value.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Not every asset deserves the same protection, and treating everything as equally sensitive wastes effort
        on low-value items while under-protecting the ones that matter most. A common four-tier scheme:
        <strong>public</strong> (no harm if anyone sees it), <strong>internal</strong> (meant for the crew, mild
        harm if it leaked), <strong>confidential</strong> (real harm if exposed — needs real access control), and
        <strong>restricted</strong> (severe harm if exposed or lost — the highest level of protection).
    </p>

    <p class="body-text">
        Classification isn't a one-time label. An asset's tier should be reviewed if its context changes — an
        internal document can become confidential the moment it starts containing something sensitive it didn't
        before.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        A cadet locks the crew's public recruiting flyer in the same vault as the starmap, "to be safe." Astro
        redirects the effort. "The flyer is public — anyone can see it, no harm done. Put your vault-level effort
        where it matters: the starmap is restricted, and the crew roster with everyone's personal details is
        confidential. Protecting everything the same way means protecting nothing well."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 242" role="img" aria-label="Four asset classification tiers: public, internal, confidential, and restricted">
            <rect x="40" y="20" width="560" height="38" rx="10" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="320" y="44" text-anchor="middle" font-size="14.5" font-weight="700" fill="#146c3a">Public — no harm if seen by anyone</text>
            <rect x="40" y="66" width="560" height="38" rx="10" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="320" y="90" text-anchor="middle" font-size="14.5" font-weight="700" fill="#1c4f8f">Internal — mild harm if it leaked</text>
            <rect x="40" y="112" width="560" height="38" rx="10" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="136" text-anchor="middle" font-size="14.5" font-weight="700" fill="#8a4b00">Confidential — real harm — needs access control</text>
            <rect x="40" y="158" width="560" height="38" rx="10" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="320" y="182" text-anchor="middle" font-size="14.5" font-weight="700" fill="#7a1a14">Restricted — severe harm — highest protection</text>
            <text x="320" y="228" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">Protection should match the tier — not every asset needs a vault</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does asset classification into tiers accomplish?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It makes every asset equally protected regardless of value</li>
            <li class="quiz-option quiz-correct">B. It matches the level of protection to how much harm exposure or loss would actually cause ✓</li>
            <li class="quiz-option">C. It only applies to physical documents, never digital ones</li>
            <li class="quiz-option">D. It removes the need for any access control</li>
        </ul>
        <p class="quiz-explanation"><em>Classification lets protection effort go where it matters most, instead of spreading it evenly regardless of actual value.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What tier would a public recruiting flyer belong in?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Restricted</li>
            <li class="quiz-option">B. Confidential</li>
            <li class="quiz-option quiz-correct">C. Public — no harm if anyone sees it ✓</li>
            <li class="quiz-option">D. Internal</li>
        </ul>
        <p class="quiz-explanation"><em>A flyer meant for public distribution carries no sensitivity, so it belongs at the lowest tier.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Astro redirect the cadet's effort away from locking the flyer in the vault?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because the vault was already full</li>
            <li class="quiz-option quiz-correct">B. Because protecting a public, harmless item the same way as the most sensitive asset wastes effort that should go where it matters ✓</li>
            <li class="quiz-option">C. Because flyers are never worth protecting at all</li>
            <li class="quiz-option">D. Because Astro dislikes vaults</li>
        </ul>
        <p class="quiz-explanation"><em>Treating a low-sensitivity asset like a high-sensitivity one misallocates protective effort.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What tier would the starmap likely belong in?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Public</li>
            <li class="quiz-option">B. Internal</li>
            <li class="quiz-option">C. Confidential</li>
            <li class="quiz-option quiz-correct">D. Restricted — severe harm if exposed or lost ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The starmap is described as the crew's most valuable, sensitive asset — warranting the highest protection tier.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why isn't classification a one-time label?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because classification never needs to change once set</li>
            <li class="quiz-option quiz-correct">B. Because an asset's context can change, and its tier should be reviewed accordingly ✓</li>
            <li class="quiz-option">C. Because tiers are randomly reassigned every year</li>
            <li class="quiz-option">D. Because only restricted items are ever reclassified</li>
        </ul>
        <p class="quiz-explanation"><em>A document's sensitivity can shift as its content or context changes, so classification requires ongoing review.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four Citadel items need classifying into their correct sensitivity tier.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a8-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm8', 'lesson' => 'lesson01']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the item list, then classify each one into its tier.
        </p>
    </div>

</div>
