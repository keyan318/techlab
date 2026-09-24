{{-- M1 · Lesson 1.5: Security Policy (AUP) — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c1-l5.json) completes the lesson.
Syllabus: ITP1232 Unit A.5 Security Policy (Acceptable Use Policy). --}}

<div class="lesson-fragment" data-module="m1" data-lesson="05">

    <h1 class="lesson-heading">Lesson 1.5: Security Policy (AUP)</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what an Acceptable Use Policy (AUP) is, why a rule that
        nobody actually follows isn't really a rule, and identify real behavior that breaks a written policy.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        An <strong>Acceptable Use Policy (AUP)</strong> is a written set of rules for how people are allowed to use
        an organization's systems — one account per person, what software belongs on a work machine, who's allowed
        to see which files. It's usually short, and it's supposed to be the thing everyone actually follows, not a
        document that exists only to be filed away.
    </p>

    <p class="body-text">
        A policy only does its job if it's enforced. A rule that says "no shared logins" while three people are
        actively using one shared login isn't protecting anyone — it's just a sentence. Writing the rule is the
        easy part; finding and fixing every place it's already being broken is the part that actually matters.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        <strong>Astro</strong> calls a policy review, and it doesn't take long to find problems. A login meant for
        one shift is being used by three different crew members. A game is running on a console meant for shield
        duty only. A file marked CONFIDENTIAL is readable by anyone logged in.
    </p>

    <p class="body-text">
        "We already wrote the rules," <strong>Astro</strong> says, holding up the AUP. "One account per person. Shield
        consoles run shield software only. Confidential files stay with their owner. None of that is new. What's new
        is that today, we actually make it true." <strong>Volt</strong>: "A rule nobody follows isn't a rule. Let's fix that."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 190" role="img" aria-label="Three AUP rules, each currently broken, each with a specific fix">
            <rect x="10" y="15" width="200" height="150" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="110" y="42" text-anchor="middle" font-size="14" font-weight="700" fill="#b3261e">Rule 1</text>
            <text x="110" y="62" text-anchor="middle" font-size="11.5" fill="#7a1a14">One account per person</text>
            <text x="110" y="90" text-anchor="middle" font-size="11.5" fill="#7a1a14">Broken: shift-shared login</text>
            <text x="110" y="128" text-anchor="middle" font-size="11.5" font-weight="700" fill="#14306b">Fix: disable it</text>

            <rect x="220" y="15" width="200" height="150" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="42" text-anchor="middle" font-size="14" font-weight="700" fill="#8a4b00">Rule 2</text>
            <text x="320" y="62" text-anchor="middle" font-size="11.5" fill="#8a4b00">Shield software only</text>
            <text x="320" y="90" text-anchor="middle" font-size="11.5" fill="#8a4b00">Broken: a game running</text>
            <text x="320" y="128" text-anchor="middle" font-size="11.5" font-weight="700" fill="#14306b">Fix: stop the process</text>

            <rect x="430" y="15" width="200" height="150" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="530" y="42" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">Rule 3</text>
            <text x="530" y="62" text-anchor="middle" font-size="11.5" fill="#4a35b8">Confidential stays locked</text>
            <text x="530" y="90" text-anchor="middle" font-size="11.5" fill="#4a35b8">Broken: readable by all</text>
            <text x="530" y="128" text-anchor="middle" font-size="11.5" font-weight="700" fill="#14306b">Fix: lock the file</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is an Acceptable Use Policy (AUP)?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A type of firewall rule</li>
            <li class="quiz-option quiz-correct">B. A written set of rules for how people are allowed to use an organization's systems ✓</li>
            <li class="quiz-option">C. A password-strength checker</li>
            <li class="quiz-option">D. A backup schedule</li>
        </ul>
        <p class="quiz-explanation"><em>An AUP is policy, not a technical control: it's the written rules for acceptable behavior on the systems people use.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Three crew members are actively using one shared login, even though the AUP says one account per person. What's true here?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The policy is being followed, since everyone can still log in</li>
            <li class="quiz-option quiz-correct">B. The policy is written but not enforced, so it isn't actually protecting anyone ✓</li>
            <li class="quiz-option">C. Shared logins are recommended for backup access</li>
            <li class="quiz-option">D. This only matters if the login has a weak password</li>
        </ul>
        <p class="quiz-explanation"><em>A rule that exists on paper but isn't followed in practice provides no real protection — enforcement is what makes a policy meaningful.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. A game is running on a console meant for shield duty only. Which part of the AUP does this break?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. One account per person</li>
            <li class="quiz-option quiz-correct">B. Shield-duty consoles run shield software only ✓</li>
            <li class="quiz-option">C. Confidential files stay locked</li>
            <li class="quiz-option">D. None of the rules cover this</li>
        </ul>
        <p class="quiz-explanation"><em>The policy specifically restricts what runs on shield-duty consoles — unrelated software, even something harmless-looking, is still a violation.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. A file marked CONFIDENTIAL can currently be read by anyone logged in, not just its owner. What does the AUP require here?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Deleting the file</li>
            <li class="quiz-option">B. Emailing it to the whole crew for transparency</li>
            <li class="quiz-option quiz-correct">C. Locking it so only its owner can access it ✓</li>
            <li class="quiz-option">D. Renaming it to something less obvious</li>
        </ul>
        <p class="quiz-explanation"><em>The AUP's third rule is specific: confidential files are locked to the crew member who owns them, not left open to everyone.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Volt says "a rule nobody follows isn't a rule." What is that actually about?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Rules should be rewritten every year</li>
            <li class="quiz-option quiz-correct">B. A policy only matters if it's enforced, not just written down ✓</li>
            <li class="quiz-option">C. The Citadel doesn't need written policies</li>
            <li class="quiz-option">D. Only Astro needs to follow the AUP</li>
        </ul>
        <p class="quiz-explanation"><em>Writing a policy is the easy part. Finding and fixing every place it's currently being broken is what actually makes it real.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> read Astro's three Citadel rules, then find and fix every place the crew is
        already breaking one: disable the shared login, stop the game running on shield duty, and lock the
        confidential file to its owner. <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c1-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm1', 'lesson' => 'lesson05']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>net user</code>, <code>taskkill</code> and <code>icacls</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
