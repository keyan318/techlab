{{-- M8 · Lesson 8.6: Regulatory Standards Compliance — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a8-l6.json) completes the lesson.
Syllabus: ITP1333 Unit H.6 Regulatory Standards Compliance. --}}

<div class="lesson-fragment" data-module="m8" data-lesson="06">

    <h1 class="lesson-heading">Lesson 8.6: Regulatory Standards Compliance</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> audit a facility's physical security controls against a regulatory-style checklist, and distinguish a documented, evidenced compliance from an unverified claim.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Physical security has to satisfy outside standards too — the same way disaster recovery plans do. A
        regulatory checklist typically lists specific, checkable requirements: entry points logged, cameras retain
        footage for a minimum period, restricted areas require multi-factor physical access. Compliance means each
        requirement has evidence behind it — a log that actually exists, footage that's actually retained — not
        just a claim that it's handled.
    </p>

    <p class="body-text">
        As with any audit, "it's probably fine" and "here is the documented evidence" are different claims, and
        only the second one satisfies a real compliance review.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does compliance need behind each requirement?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A spoken promise</li>
            <li class="quiz-option quiz-correct">B. Documented evidence ✓</li>
            <li class="quiz-option">C. A good feeling</li>
            <li class="quiz-option">D. A longer checklist to sign</li>
        </ul>
        <p class="quiz-explanation"><em>A log that actually exists and footage actually retained. “It's probably fine” isn't evidence.</em></p>
    </div>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Astro reviews the physical security checklist ahead of Codexia's annual inspection. Rivet insists every
        item is "basically covered." Astro asks for evidence instead: the entry log for the vault — it exists.
        Ninety days of camera footage retention — the system only actually keeps thirty. "Basically covered" isn't
        what the inspector will accept. Only the log and footage that actually exist count.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Which item fails Astro's evidence check?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The vault's entry log book</li>
            <li class="quiz-option quiz-correct">B. Camera footage retention ✓</li>
            <li class="quiz-option">C. The inspector's visit</li>
            <li class="quiz-option">D. The annual checklist</li>
        </ul>
        <p class="quiz-explanation"><em>The standard wants 90 days of footage, but the system only keeps 30.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A compliance requirement is only satisfied by documented evidence, not an unverified claim">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Requirement</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">what the standard specifies</text>
            <line x1="170" y1="66" x2="237" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="245,66 233,60 233,72" fill="#7c5cff"/>
            <rect x="245" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">"Probably Fine"</text>
            <text x="320" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">a claim, not evidence</text>
            <line x1="395" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Documented Evidence</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">what actually satisfies compliance</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Only documented evidence satisfies a real compliance review</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what is “probably fine”?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. A claim, not evidence ✓</li>
            <li class="quiz-option">B. Documented evidence</li>
            <li class="quiz-option">C. What satisfies a review</li>
            <li class="quiz-option">D. What the standard asks for</li>
        </ul>
        <p class="quiz-explanation"><em>“Probably fine” is just a claim. Only documented evidence satisfies a real compliance review.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does a regulatory checklist for physical security typically specify?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Vague, unmeasurable goals only</li>
            <li class="quiz-option quiz-correct">B. Specific, checkable requirements, like minimum camera footage retention periods ✓</li>
            <li class="quiz-option">C. Only requirements for digital systems, never physical ones</li>
            <li class="quiz-option">D. A list of employees to hire</li>
        </ul>
        <p class="quiz-explanation"><em>Regulatory standards tend to specify concrete, verifiable requirements rather than vague aspirations.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does "compliance" require, beyond a requirement simply existing on paper?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing further — listing the requirement is sufficient</li>
            <li class="quiz-option quiz-correct">B. Evidence that the requirement is actually met — a real log, real retained footage ✓</li>
            <li class="quiz-option">C. A verbal assurance that it's handled</li>
            <li class="quiz-option">D. Only the signature of the facility manager</li>
        </ul>
        <p class="quiz-explanation"><em>Compliance means demonstrable, documented proof that a requirement is genuinely satisfied, not just acknowledged.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Astro reject Rivet's "basically covered" assessment?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because Rivet was being dishonest on purpose</li>
            <li class="quiz-option quiz-correct">B. Because "basically covered" is a claim, not verified evidence, and an inspector needs the evidence ✓</li>
            <li class="quiz-option">C. Because Astro dislikes Rivet's reports in general</li>
            <li class="quiz-option">D. Because the checklist itself was invalid</li>
        </ul>
        <p class="quiz-explanation"><em>An inspection requires documented proof, not an informal assurance that things are probably fine.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. The standard requires 90 days of camera footage retention, but the system only keeps 30. Is this compliant?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Yes, close enough counts</li>
            <li class="quiz-option quiz-correct">B. No — the actual retention period falls short of what the standard requires ✓</li>
            <li class="quiz-option">C. Compliance doesn't apply to footage retention</li>
            <li class="quiz-option">D. Yes, since some footage exists at all</li>
        </ul>
        <p class="quiz-explanation"><em>Falling short of a specific, measurable requirement means the facility is not compliant on that point, regardless of partial effort.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is the same lesson this physical security audit shares with the earlier DR plan audit?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Physical security and DR have nothing in common</li>
            <li class="quiz-option quiz-correct">B. A written or assumed claim isn't the same as documented, verified evidence of actually meeting the standard ✓</li>
            <li class="quiz-option">C. Only DR plans require any auditing at all</li>
            <li class="quiz-option">D. Compliance is only relevant to digital systems</li>
        </ul>
        <p class="quiz-explanation"><em>Both audits hinge on the same principle: real compliance requires evidence, not just an assumption or a claim that something is handled.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> The Codexia inspection is coming. Audit four physical security requirements against the actual evidence.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a8-l6" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm8', 'lesson' => 'lesson06']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the checklist and the evidence file, then classify each
            requirement as compliant or non-compliant.
        </p>
    </div>

</div>
