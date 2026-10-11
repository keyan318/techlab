{{-- M6 · Lesson 6.4: Administrative Security Controls — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a6-l4.json) completes the lesson.
Syllabus: ITP1333 Unit F.4 Administrative Security Controls. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="04">

    <h1 class="lesson-heading">Lesson 6.4: Administrative Security Controls</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> distinguish an administrative control (a rule, review, or sign-off carried out by people and process) from a technical control (a tool that enforces a rule automatically).
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        An <strong>administrative control</strong> is a rule, review, or process carried out by people —
        background checks before hiring, a periodic access review, a documented sign-off before a sensitive
        action. A <strong>technical control</strong> is a tool that enforces a rule automatically — a firewall
        rule, an access-control list, a password policy the system itself rejects weak passwords for.
    </p>

    <p class="body-text">
        Both matter, and one rarely covers what the other misses. A technical control can enforce "this file needs
        a password" instantly and consistently; it cannot judge "should this specific person be trusted with this
        specific file," which is exactly the kind of question an administrative review answers.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Rivet proposes a new firewall rule to solve every access problem. Astro agrees it will help — for the
        problems a rule can solve. "A firewall can enforce which port is open. It cannot tell us whether the new
        supply officer should be trusted with starmap access — that's a judgment call, made by a person, with a
        documented sign-off. We need both: your technical control, and an administrative review neither of us can
        automate away."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 230" role="img" aria-label="A technical control can't judge trust; an administrative control can't enforce it automatically">
            <rect x="245" y="75" width="150" height="62" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2.5"/>
            <text x="320" y="102" text-anchor="middle" font-size="16" font-weight="700" fill="#4a35b8">Security Control</text>
            <text x="320" y="123" text-anchor="middle" font-size="12" fill="#4a35b8">two different kinds</text>
            <rect x="135" y="20" width="170" height="62" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="220" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#1c4f8f">Administrative</text>
            <text x="220" y="68" text-anchor="middle" font-size="11.5" fill="#1c4f8f">reviews, sign-offs, done by people</text>
            <line x1="220" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <rect x="335" y="20" width="170" height="62" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="420" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#146c3a">Technical</text>
            <text x="420" y="68" text-anchor="middle" font-size="11.5" fill="#146c3a">tools that enforce automatically</text>
            <line x1="420" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="220" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">A technical control can't judge trust; an administrative control can't enforce it automatically</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is an example of an administrative control?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A firewall rule blocking a port</li>
            <li class="quiz-option quiz-correct">B. A periodic access review carried out by a person ✓</li>
            <li class="quiz-option">C. A password policy enforced automatically by the system</li>
            <li class="quiz-option">D. An access-control list on a file</li>
        </ul>
        <p class="quiz-explanation"><em>Administrative controls are carried out by people through process — a review is a classic example.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is an example of a technical control?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A background check before hiring</li>
            <li class="quiz-option">B. A documented sign-off before a sensitive action</li>
            <li class="quiz-option quiz-correct">C. A firewall rule that automatically blocks a port ✓</li>
            <li class="quiz-option">D. A periodic policy review meeting</li>
        </ul>
        <p class="quiz-explanation"><em>A technical control is enforced automatically by a tool or system, like a firewall rule.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why couldn't Rivet's proposed firewall rule solve every access problem?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Firewalls don't actually block anything</li>
            <li class="quiz-option quiz-correct">B. A firewall can enforce which port is open, but can't judge whether a specific person should be trusted with specific data ✓</li>
            <li class="quiz-option">C. Firewall rules are always too expensive</li>
            <li class="quiz-option">D. Astro dislikes firewalls in general</li>
        </ul>
        <p class="quiz-explanation"><em>Technical controls enforce mechanical rules; they can't make the judgment calls administrative review is designed for.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why does a mature security program need both administrative and technical controls?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because one always replaces the need for the other eventually</li>
            <li class="quiz-option quiz-correct">B. Because each covers gaps the other can't — technical enforces automatically, administrative judges trust and context ✓</li>
            <li class="quiz-option">C. Because using only one type is illegal</li>
            <li class="quiz-option">D. Administrative controls are obsolete now that technical controls exist</li>
        </ul>
        <p class="quiz-explanation"><em>Neither type alone covers everything a security program needs — they complement each other.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What did Astro decide the Citadel needed for the starmap access question?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Only a technical control, nothing else</li>
            <li class="quiz-option quiz-correct">B. Both: a technical control where applicable, and an administrative review for the trust judgment ✓</li>
            <li class="quiz-option">C. Neither — the decision should be left unmade</li>
            <li class="quiz-option">D. Only an administrative control, no technical enforcement at all</li>
        </ul>
        <p class="quiz-explanation"><em>Astro's conclusion was that both kinds of control were needed together, each doing the part the other couldn't.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four proposed Citadel controls need sorting: administrative or technical.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a6-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm6', 'lesson' => 'lesson04']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the list of proposed controls, then classify each one.
        </p>
    </div>

</div>
