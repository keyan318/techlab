{{-- M2 · Lesson 2.1: The CIA Triad & Other Security Models — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a2-l1.json) completes the lesson.
Syllabus: ITP1333 Unit B.1 The CIA Triad and Other Security Models. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="01">

    <h1 class="lesson-heading">Lesson 2.1: The CIA Triad & Other Security Models</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> classify a security event using the CIA triad, and describe the AAA model (authentication, authorization, accounting) as a second useful lens.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        The <strong>CIA triad</strong> — confidentiality, integrity, availability — is the most common way to
        describe what security protects. But it isn't the only model. The <strong>AAA model</strong> looks at
        access instead of protection: <strong>Authentication</strong> (proving who you are), <strong>Authorization</strong>
        (deciding what you're allowed to do), and <strong>Accounting</strong> (recording what you actually did).
    </p>

    <p class="body-text">
        The two models answer different questions. CIA asks "what got broken?" AAA asks "who did it, were they
        allowed to, and is there a record?" A mature security design uses both: CIA to judge the damage, AAA to
        control and trace who could cause it.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Rivet reviews last week's relay log with a new cadet. "See this login at 03:00? First question: did they
        prove who they were — authentication. Second: were they even allowed on that console — authorization.
        Third: is it in the log at all — accounting. If any one of those three is missing, we have a gap, even
        before we ask whether confidentiality, integrity or availability got hurt."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 230" role="img" aria-label="CIA judges the damage; AAA controls and traces who could cause it">
            <rect x="245" y="75" width="150" height="62" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2.5"/>
            <text x="320" y="102" text-anchor="middle" font-size="16" font-weight="700" fill="#4a35b8">Security Models</text>
            <text x="320" y="123" text-anchor="middle" font-size="12" fill="#4a35b8">two lenses, same goal</text>
            <rect x="135" y="20" width="170" height="62" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="220" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#1c4f8f">CIA Triad</text>
            <text x="220" y="68" text-anchor="middle" font-size="11.5" fill="#1c4f8f">C · I · A</text>
            <line x1="220" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <rect x="335" y="20" width="170" height="62" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="420" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#146c3a">AAA Model</text>
            <text x="420" y="68" text-anchor="middle" font-size="11.5" fill="#146c3a">Auth-N · Auth-Z · Accounting</text>
            <line x1="420" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="220" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">CIA judges the damage; AAA controls and traces who could cause it</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does the CIA triad describe?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Who is allowed to log in</li>
            <li class="quiz-option quiz-correct">B. What security protects: confidentiality, integrity, availability ✓</li>
            <li class="quiz-option">C. A list of enemy agents</li>
            <li class="quiz-option">D. A type of firewall</li>
        </ul>
        <p class="quiz-explanation"><em>CIA describes the three properties security protects: keeping data secret, unaltered, and accessible.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In the AAA model, what does "Authentication" mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Recording what a user did</li>
            <li class="quiz-option">B. Deciding what a user is allowed to do</li>
            <li class="quiz-option quiz-correct">C. Proving who a user is ✓</li>
            <li class="quiz-option">D. Encrypting a file</li>
        </ul>
        <p class="quiz-explanation"><em>Authentication is proving identity — that you are who you claim to be.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. In the AAA model, what does "Authorization" mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Proving identity</li>
            <li class="quiz-option quiz-correct">B. Deciding what an already-identified user is allowed to do ✓</li>
            <li class="quiz-option">C. Writing a log entry</li>
            <li class="quiz-option">D. Locking a door</li>
        </ul>
        <p class="quiz-explanation"><em>Authorization comes after identity is proven — it decides what that identified user may actually do.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. In the AAA model, what does "Accounting" mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Billing customers for services</li>
            <li class="quiz-option quiz-correct">B. Recording what a user actually did, for later review ✓</li>
            <li class="quiz-option">C. Counting how many passwords exist</li>
            <li class="quiz-option">D. The same thing as authentication</li>
        </ul>
        <p class="quiz-explanation"><em>Accounting is the record-keeping — a trail of what happened, so it can be reviewed or investigated later.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why does Rivet check all three parts of AAA before even asking about CIA?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because AAA and CIA can't both apply to the same event</li>
            <li class="quiz-option quiz-correct">B. Because a missing authentication, authorization, or accounting step is a gap regardless of what got damaged ✓</li>
            <li class="quiz-option">C. Because CIA only applies to physical security</li>
            <li class="quiz-option">D. Because AAA replaces CIA entirely</li>
        </ul>
        <p class="quiz-explanation"><em>AAA and CIA are complementary — a gap in access control is a problem whether or not it happened to damage confidentiality, integrity or availability.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Rivet flagged four log entries. Read the log, then classify each entry by which AAA step it's missing.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a2-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm2', 'lesson' => 'lesson01']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the access log, then classify each flagged entry by the AAA
            step that's missing.
        </p>
    </div>

</div>
