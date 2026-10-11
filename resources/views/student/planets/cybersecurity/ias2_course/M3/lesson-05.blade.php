{{-- M3 · Lesson 3.5: Ongoing Maintenance — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a3-l5.json) completes the lesson.
Syllabus: ITP1333 Unit C.5 Ongoing Maintenance. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="05">

    <h1 class="lesson-heading">Lesson 3.5: Ongoing Maintenance</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> explain why policies, standards, and procedures must be reviewed on a schedule, and recognize when a document is stale.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A policy written once and never revisited slowly stops matching reality. Systems change, threats change,
        crew members change — a document frozen from years ago can actively mislead people who trust it.
        <strong>Ongoing maintenance</strong> means every policy, standard, and procedure has an owner and a review
        date, and gets checked against current reality on a schedule, not just when something breaks.
    </p>

    <p class="body-text">
        A good sign a document needs review: it names a system that no longer exists, a role nobody holds anymore,
        or a rule that's quietly been ignored for months because it no longer fits how the crew actually works.
    </p>

    <h2 class="section-heading">Volt's Drill</h2>

    <p class="body-text">
        Volt pulls the shield-duty procedure from the archive. "Last reviewed three years ago. It still names
        'the night-watch officer' — we retired that role two years back. It references the old relay console — we
        replaced that too. Nobody updated it because nobody was ever assigned to review it. That's the real gap:
        not a bad procedure, a procedure with no owner and no review date."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A document written once, with no owner or review schedule, eventually goes stale">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Written</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">matches reality on day one</text>
            <line x1="170" y1="66" x2="237" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="245,66 233,60 233,72" fill="#7c5cff"/>
            <rect x="245" y="30" width="150" height="72" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#8a4b00">Time Passes</text>
            <text x="320" y="82" text-anchor="middle" font-size="12.5" fill="#8a4b00">systems & roles change</text>
            <line x1="395" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">Stale</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">no owner, no review date</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Without an owner and a review date, every document eventually goes stale</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does "ongoing maintenance" of security documents mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Writing a policy once and never touching it again</li>
            <li class="quiz-option quiz-correct">B. Giving every policy, standard, and procedure an owner and a review date, checked on a schedule ✓</li>
            <li class="quiz-option">C. Deleting old policies instead of updating them</li>
            <li class="quiz-option">D. Only reviewing documents after a breach occurs</li>
        </ul>
        <p class="quiz-explanation"><em>Ongoing maintenance means documents are actively kept current through scheduled review, not left frozen.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is a good sign that a document needs review?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It was written very recently</li>
            <li class="quiz-option quiz-correct">B. It names a system or role that no longer exists, or a rule quietly being ignored ✓</li>
            <li class="quiz-option">C. It is very short</li>
            <li class="quiz-option">D. It has Astro's name on it</li>
        </ul>
        <p class="quiz-explanation"><em>References to retired systems or roles, or rules nobody actually follows anymore, are classic signs of a stale document.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What did Volt identify as the real gap in the shield-duty procedure?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It was too detailed</li>
            <li class="quiz-option quiz-correct">B. It had no owner and no review date, so nobody ever updated it as things changed ✓</li>
            <li class="quiz-option">C. It was written in the wrong format</li>
            <li class="quiz-option">D. It referenced too many other documents</li>
        </ul>
        <p class="quiz-explanation"><em>The underlying problem wasn't the content itself — it was the missing ownership and review schedule that let it go stale.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why can a stale document actively mislead people, rather than just being outdated harmlessly?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Stale documents are always deleted automatically</li>
            <li class="quiz-option quiz-correct">B. People trust that a written procedure is current, so following an outdated one can lead them to do the wrong thing ✓</li>
            <li class="quiz-option">C. Nobody ever reads old documents</li>
            <li class="quiz-option">D. Stale documents are illegal to keep</li>
        </ul>
        <p class="quiz-explanation"><em>A document is trusted at face value — if it's out of date, following it exactly can produce the wrong action.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What two things does every policy, standard, and procedure need to avoid going stale?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A long title and a cover page</li>
            <li class="quiz-option quiz-correct">B. An owner and a scheduled review date ✓</li>
            <li class="quiz-option">C. A password and a signature only</li>
            <li class="quiz-option">D. Nothing — documents don't need maintenance once written</li>
        </ul>
        <p class="quiz-explanation"><em>Ownership and a review schedule are what keep a document actively maintained instead of frozen in time.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four Citadel documents haven't been reviewed in years. Read the register, then flag which ones are overdue for review.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a3-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm3', 'lesson' => 'lesson05']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the document register, then classify each one as
            current or overdue.
        </p>
    </div>

</div>
