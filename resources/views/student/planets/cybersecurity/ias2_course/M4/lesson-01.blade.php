{{-- M4 · Lesson 4.1: Roles & Responsibilities — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a4-l1.json) completes the lesson.
Syllabus: ITP1333 Unit D.1 Roles & Responsibilities. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="01">

    <h1 class="lesson-heading">Lesson 4.1: Roles & Responsibilities</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> assign a security responsibility to the correct role (security lead, data owner, system administrator, or every crew member) and explain why a task with no owner tends not to get done.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Security only works when specific tasks belong to specific roles, not to "whoever gets to it." A useful
        four-way split: the <strong>security lead</strong> sets direction and owns the overall program; the
        <strong>data owner</strong> decides who should have access to a specific dataset and approves changes to
        that access; the <strong>system administrator</strong> carries out the technical work — accounts,
        patches, configuration; and <strong>every crew member</strong> is responsible for following policy day to
        day (like reporting a suspicious email).
    </p>

    <p class="body-text">
        A task with no clearly assigned role tends not to get done — not because anyone refuses it, but because
        everyone assumes someone else has it covered.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        A cadet asks who's supposed to approve new access to the starmap files. Nobody answers right away — Rivet
        assumed Volt had it, Volt assumed it fell under Astro's general authority. Astro stops the guessing. "Rivet
        maintains the systems that hold it. Volt runs the security program overall. But the starmap itself has an
        owner: whoever is responsible for that specific data decides who gets access to it. From today, that's
        written down, not assumed."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 230" role="img" aria-label="A task assumed to be "someone else's" is a task nobody actually does">
            <rect x="245" y="75" width="150" height="62" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2.5"/>
            <text x="320" y="102" text-anchor="middle" font-size="16" font-weight="700" fill="#4a35b8">A Security Task</text>
            <text x="320" y="123" text-anchor="middle" font-size="12" fill="#4a35b8">needs one clear owner</text>
            <rect x="35" y="20" width="170" height="62" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="120" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#1c4f8f">Security Lead</text>
            <text x="120" y="68" text-anchor="middle" font-size="11.5" fill="#1c4f8f">sets direction</text>
            <line x1="120" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <rect x="235" y="20" width="170" height="62" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="320" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#146c3a">Data Owner</text>
            <text x="320" y="68" text-anchor="middle" font-size="11.5" fill="#146c3a">approves access</text>
            <line x1="320" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <rect x="435" y="20" width="170" height="62" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="520" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#8a4b00">System Admin</text>
            <text x="520" y="68" text-anchor="middle" font-size="11.5" fill="#8a4b00">does the technical work</text>
            <line x1="520" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="220" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">A task assumed to be "someone else's" is a task nobody actually does</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does a "data owner" role typically decide?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The overall direction of the security program</li>
            <li class="quiz-option quiz-correct">B. Who should have access to a specific dataset, and approves changes to that access ✓</li>
            <li class="quiz-option">C. Which patches get installed on servers</li>
            <li class="quiz-option">D. The Citadel's budget for the year</li>
        </ul>
        <p class="quiz-explanation"><em>A data owner is accountable for a specific dataset — deciding and approving who gets access to it.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does a "system administrator" typically do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Sets the overall security direction</li>
            <li class="quiz-option quiz-correct">B. Carries out the technical work: accounts, patches, configuration ✓</li>
            <li class="quiz-option">C. Approves who owns which dataset</li>
            <li class="quiz-option">D. Writes the crew's daily schedule</li>
        </ul>
        <p class="quiz-explanation"><em>The system administrator is the hands-on technical role, executing the work the policy and standards require.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is every crew member's security responsibility, regardless of role?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Approving all access requests</li>
            <li class="quiz-option quiz-correct">B. Following policy day to day, such as reporting a suspicious email ✓</li>
            <li class="quiz-option">C. Configuring the firewall</li>
            <li class="quiz-option">D. Setting the overall security strategy</li>
        </ul>
        <p class="quiz-explanation"><em>Baseline responsibilities like following policy and reporting suspicious activity apply to everyone, not just specialists.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why did nobody know who should approve access to the starmap files?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because access approval isn't a real responsibility</li>
            <li class="quiz-option quiz-correct">B. Because the role was assumed to belong to someone else and was never written down ✓</li>
            <li class="quiz-option">C. Because Astro forbids anyone from having that role</li>
            <li class="quiz-option">D. Because the starmap has no owner by design</li>
        </ul>
        <p class="quiz-explanation"><em>Everyone assumed someone else had it covered — a classic symptom of an unassigned responsibility.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What tends to happen to a security task with no clearly assigned owner?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It gets done twice, by multiple people</li>
            <li class="quiz-option quiz-correct">B. It tends not to get done, because everyone assumes someone else has it covered ✓</li>
            <li class="quiz-option">C. It automatically becomes the security lead's job</li>
            <li class="quiz-option">D. It gets flagged by the system immediately</li>
        </ul>
        <p class="quiz-explanation"><em>Ambiguous ownership usually means the task falls through the cracks rather than getting picked up by anyone.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four security tasks have no assigned owner yet. Read the list, then assign each to its correct role.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a4-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm4', 'lesson' => 'lesson01']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the task list, then classify each task by the role
            responsible for it.
        </p>
    </div>

</div>
