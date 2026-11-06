{{-- M5 · Lesson 5.2: Application Security & Threat Modeling — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a5-l2.json) completes the lesson.
Syllabus: ITP1333 Unit E.2 Application Security Practices (Threat Modeling). --}}

<div class="lesson-fragment" data-module="m5" data-lesson="02">

    <h1 class="lesson-heading">Lesson 5.2: Application Security & Threat Modeling</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> apply a simplified STRIDE threat model to sort application threats into categories, and explain why threat modeling happens before code is written.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Threat modeling</strong> means systematically asking "how could this be attacked?" before writing
        code, so defenses can be designed in rather than patched on later. One widely used checklist is
        <strong>STRIDE</strong>: <strong>S</strong>poofing (pretending to be someone else), <strong>T</strong>ampering
        (changing data without permission), <strong>R</strong>epudiation (denying you did something, with no way to
        prove otherwise), <strong>I</strong>nformation disclosure (exposing data to those who shouldn't see it),
        <strong>D</strong>enial of service (making a system unavailable), and <strong>E</strong>levation of
        privilege (gaining more access than intended).
    </p>

    <p class="body-text">
        Running through each category against a design reveals gaps a single "is this secure?" question would
        miss — it forces a specific answer for each kind of misuse, not just a general feeling of confidence.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">In STRIDE, what does the R stand for?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Repudiation ✓</li>
            <li class="quiz-option">B. Ransomware</li>
            <li class="quiz-option">C. Replication</li>
            <li class="quiz-option">D. Recovery</li>
        </ul>
        <p class="quiz-explanation"><em>Repudiation is denying you did something, with no way to prove otherwise.</em></p>
    </div>

    <h2 class="section-heading">Volt's Drill</h2>

    <p class="body-text">
        Volt walks Rivet's new supply tool through STRIDE before a line of code is written. "Could someone
        <em>spoof</em> a requisition as coming from Command? Could someone <em>tamper</em> with a quantity in
        transit? Could a cadet later <em>deny</em> submitting a request with no log to prove it? Could the
        inventory count be <em>disclosed</em> to a rival crew? Could someone flood it to cause a <em>denial of
        service</em>? Could a regular cadet <em>elevate</em> to approver access?" Six questions, six specific
        answers — not one vague "seems fine."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">“Could a regular cadet get approver access?” Which STRIDE threat is that?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Spoofing</li>
            <li class="quiz-option quiz-correct">B. Elevation of privilege ✓</li>
            <li class="quiz-option">C. Tampering</li>
            <li class="quiz-option">D. Denial of service attack</li>
        </ul>
        <p class="quiz-explanation"><em>Gaining more access than intended is elevation of privilege.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 334" role="img" aria-label="STRIDE threat categories: spoofing, tampering, repudiation, information disclosure, denial of service, elevation of privilege">
            <rect x="40" y="20" width="560" height="38" rx="10" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="320" y="44" text-anchor="middle" font-size="14.5" font-weight="700" fill="#7a1a14">Spoofing — pretending to be someone else</text>
            <rect x="40" y="66" width="560" height="38" rx="10" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="90" text-anchor="middle" font-size="14.5" font-weight="700" fill="#8a4b00">Tampering — changing data without permission</text>
            <rect x="40" y="112" width="560" height="38" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="136" text-anchor="middle" font-size="14.5" font-weight="700" fill="#4a35b8">Repudiation — denying an action, no proof either way</text>
            <rect x="40" y="158" width="560" height="38" rx="10" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="320" y="182" text-anchor="middle" font-size="14.5" font-weight="700" fill="#1c4f8f">Info Disclosure — exposing data to the wrong people</text>
            <rect x="40" y="204" width="560" height="38" rx="10" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="320" y="228" text-anchor="middle" font-size="14.5" font-weight="700" fill="#7a1a14">Denial of Service — making a system unavailable</text>
            <rect x="40" y="250" width="560" height="38" rx="10" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="274" text-anchor="middle" font-size="14.5" font-weight="700" fill="#8a4b00">Elevation of Privilege — gaining more access than intended</text>
            <text x="320" y="320" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">STRIDE: six specific questions, not one vague feeling of confidence</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what is tampering?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Pretending to be someone else</li>
            <li class="quiz-option quiz-correct">B. Changing data without permission ✓</li>
            <li class="quiz-option">C. Exposing data to the wrong people</li>
            <li class="quiz-option">D. Making a system unavailable</li>
        </ul>
        <p class="quiz-explanation"><em>Tampering is changing data without permission. Spoofing is pretending to be someone else.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does "threat modeling" mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Testing software after it has already shipped</li>
            <li class="quiz-option quiz-correct">B. Systematically asking how a system could be attacked, before writing code ✓</li>
            <li class="quiz-option">C. Writing user documentation</li>
            <li class="quiz-option">D. A synonym for a firewall</li>
        </ul>
        <p class="quiz-explanation"><em>Threat modeling is a design-phase activity — anticipating attacks before implementation, so defenses can be built in.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In STRIDE, what does the "S" stand for?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Security</li>
            <li class="quiz-option quiz-correct">B. Spoofing — pretending to be someone else ✓</li>
            <li class="quiz-option">C. Server</li>
            <li class="quiz-option">D. Storage</li>
        </ul>
        <p class="quiz-explanation"><em>Spoofing is impersonating another identity to gain trust or access that shouldn't be granted.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. A cadet submits a request and later denies ever doing so, with no log to prove otherwise. Which STRIDE category is this?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Tampering</li>
            <li class="quiz-option quiz-correct">B. Repudiation ✓</li>
            <li class="quiz-option">C. Denial of service</li>
            <li class="quiz-option">D. Spoofing</li>
        </ul>
        <p class="quiz-explanation"><em>Repudiation is the ability to deny an action with no way to prove it actually happened — a logging/accountability gap.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. A regular cadet finds a way to gain approver-level access they were never granted. Which STRIDE category is this?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Information disclosure</li>
            <li class="quiz-option quiz-correct">B. Elevation of privilege ✓</li>
            <li class="quiz-option">C. Tampering</li>
            <li class="quiz-option">D. Spoofing</li>
        </ul>
        <p class="quiz-explanation"><em>Elevation of privilege is gaining more access or capability than was intended for that role.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why does Volt ask six specific STRIDE questions instead of one general "is this secure?" question?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. To make the review take longer for no reason</li>
            <li class="quiz-option quiz-correct">B. Because a specific answer for each kind of misuse reveals gaps a vague question would miss ✓</li>
            <li class="quiz-option">C. Because STRIDE only has one real category that matters</li>
            <li class="quiz-option">D. General questions are always more thorough than specific ones</li>
        </ul>
        <p class="quiz-explanation"><em>Each STRIDE category targets a distinct kind of misuse, so working through all six surfaces gaps a single vague question would overlook.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four possible misuses of the supply tool need sorting into their STRIDE category before the design is finalized.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a5-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm5', 'lesson' => 'lesson02']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the threat list, then classify each one by its STRIDE
            category.
        </p>
    </div>

</div>
