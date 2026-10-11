{{-- M6 · Lesson 6.1: Communications & Reporting — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a6-l1.json) completes the lesson.
Syllabus: ITP1333 Unit F.1 Communications & Reporting. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="01">

    <h1 class="lesson-heading">Lesson 6.1: Communications & Reporting</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> route a security event to the correct level of urgency, and explain why a clear escalation path matters more than raw speed.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Not every security event needs the same response speed. A useful three-way split: <strong>immediate
        escalation</strong> (wake someone up right now — an active intrusion, a system down), <strong>routine
        report</strong> (log it, review at the next regular check-in — a minor policy exception, a resolved false
        alarm), and <strong>council-level</strong> (not urgent today, but needs a decision from the group that sets
        priorities — a recurring pattern, a proposed policy change).
    </p>

    <p class="body-text">
        The goal of a reporting structure isn't to make everything feel urgent — that just trains people to ignore
        alerts. It's to make sure the right event reaches the right person at the right speed, every time, not just
        when someone happens to notice.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        A cadet reports every event to Astro directly, at 2 AM, regardless of severity. Astro appreciates the
        diligence but redirects it. "An active intrusion — wake me, always. A resolved false alarm from last
        week — that goes in the routine log, I'll see it at the morning briefing. If you escalate everything the
        same way, I stop being able to tell which alert actually needs me awake at 2 AM."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="Security events route to immediate escalation, routine reporting, or council-level decision, based on urgency">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">Immediate</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">wake someone now</text>
            <line x1="170" y1="66" x2="237" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="245,66 233,60 233,72" fill="#7c5cff"/>
            <rect x="245" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">Routine Report</text>
            <text x="320" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">log it, review later</text>
            <line x1="395" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Council-Level</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">needs a group decision</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">The right event, to the right person, at the right speed</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What kind of event calls for immediate escalation?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A minor, already-resolved false alarm</li>
            <li class="quiz-option quiz-correct">B. An active intrusion or a system down right now ✓</li>
            <li class="quiz-option">C. A proposed change to next quarter's policy</li>
            <li class="quiz-option">D. A routine weekly log entry</li>
        </ul>
        <p class="quiz-explanation"><em>Immediate escalation is reserved for events that need action right now, like an active intrusion.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What kind of event belongs in a routine report instead of immediate escalation?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. An active, ongoing intrusion</li>
            <li class="quiz-option quiz-correct">B. A minor policy exception or a resolved false alarm ✓</li>
            <li class="quiz-option">C. A system that is currently down</li>
            <li class="quiz-option">D. Nothing — every event should be escalated immediately</li>
        </ul>
        <p class="quiz-explanation"><em>Lower-severity, already-handled, or minor events are logged for later review rather than treated as urgent.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Astro redirect the cadet away from escalating everything at the same urgency?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because Astro dislikes being woken up ever</li>
            <li class="quiz-option quiz-correct">B. Because treating every event the same way makes it impossible to tell which alert actually needs immediate attention ✓</li>
            <li class="quiz-option">C. Because the cadet was reporting too rarely</li>
            <li class="quiz-option">D. Because routine reports are not allowed</li>
        </ul>
        <p class="quiz-explanation"><em>If every event is escalated at maximum urgency, the signal that something truly urgent has happened gets lost in the noise.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What is a "council-level" report, in this three-way split?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The most urgent category, requiring immediate action</li>
            <li class="quiz-option quiz-correct">B. Not urgent today, but needing a decision from the group that sets priorities ✓</li>
            <li class="quiz-option">C. A category that doesn't require any response at all</li>
            <li class="quiz-option">D. The same thing as a routine report</li>
        </ul>
        <p class="quiz-explanation"><em>Council-level events aren't emergencies, but they need a considered, cross-department decision rather than one person's snap judgment.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is the real goal of a good reporting structure?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Making every event feel equally urgent</li>
            <li class="quiz-option quiz-correct">B. Ensuring the right event reaches the right person at the right speed, every time ✓</li>
            <li class="quiz-option">C. Reducing the total number of reports to zero</li>
            <li class="quiz-option">D. Only reporting events that are already resolved</li>
        </ul>
        <p class="quiz-explanation"><em>A well-designed reporting structure routes each event appropriately, rather than treating everything the same.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four events came in overnight. Route each to its correct level: immediate, routine, or council.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a6-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm6', 'lesson' => 'lesson01']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the overnight log, then classify each event by how it
            should be routed.
        </p>
    </div>

</div>
