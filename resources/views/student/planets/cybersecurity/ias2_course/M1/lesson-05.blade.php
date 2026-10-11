{{-- M1 · Lesson 1.5: The Weakest Link: Strategy, Tactics & Process — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a1-l5.json) completes the lesson.
Syllabus: ITP1333 Units A.6-A.9 The Impossible Job, The Weakest Link, Strategy & Tactics, Process vs Controls. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="05">

    <h1 class="lesson-heading">Lesson 1.5: The Weakest Link: Strategy, Tactics & Process</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> explain why security is never "finished," why people are usually the weakest link, and the difference between strategy, tactics, and a business process versus a technical control.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Security is sometimes called "the impossible job" because it is never finished — new threats appear, new
        systems get added, and yesterday's defense doesn't cover tomorrow's gap. The goal isn't a finish line; it's
        staying ahead.
    </p>

    <p class="body-text">
        Most breaches don't come from a broken lock — they come from a person: a reused password, a rushed click, a
        skipped step. People are usually the <strong>weakest link</strong>, which is why training matters as much as
        tools.
    </p>

    <p class="body-text">
        <strong>Strategy</strong> is the long-term plan ("reduce our reliance on any single relay"). <strong>Tactics</strong>
        are the specific actions that carry it out ("install a backup relay this week"). And a
        <strong>business process</strong> (how the crew actually gets work done — who approves what, in what order)
        is not the same as a <strong>technical control</strong> (a tool that enforces a rule, like a firewall rule).
        Both matter, and neither replaces the other — a perfect firewall doesn't fix a process where anyone can
        approve their own request.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Rivet finishes installing the Citadel's newest firewall and calls it "the last piece — we're done." Astro
        disagrees. "There's no last piece, Rivet. Doom will find a new way in, because they always do. And even this
        firewall won't stop someone from writing their password on a sticky note. Our biggest strength is a wall.
        Our biggest weakness is still the crew, and that never fully goes away."
    </p>

    <p class="body-text">
        Volt adds the process problem: "And a firewall doesn't fix this — right now, the same cadet who requests
        supplies can also approve their own request. No technical control catches that. That's a process fix."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 230" role="img" aria-label="A plan, its actions, and the people who carry them out — none of it ever finishes">
            <rect x="245" y="75" width="150" height="62" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2.5"/>
            <text x="320" y="102" text-anchor="middle" font-size="16" font-weight="700" fill="#4a35b8">Security Program</text>
            <text x="320" y="123" text-anchor="middle" font-size="12" fill="#4a35b8">never finished</text>
            <rect x="35" y="20" width="170" height="62" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="120" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#1c4f8f">Strategy</text>
            <text x="120" y="68" text-anchor="middle" font-size="11.5" fill="#1c4f8f">the long-term plan</text>
            <line x1="120" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <rect x="235" y="20" width="170" height="62" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="320" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#146c3a">Tactics</text>
            <text x="320" y="68" text-anchor="middle" font-size="11.5" fill="#146c3a">this week's actions</text>
            <line x1="320" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <rect x="435" y="20" width="170" height="62" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="520" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1a14">People</text>
            <text x="520" y="68" text-anchor="middle" font-size="11.5" fill="#7a1a14">usually the weakest link</text>
            <line x1="520" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="220" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">A plan, its actions, and the people who carry them out — none of it ever finishes</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why is security sometimes called "the impossible job"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because nobody wants to do it</li>
            <li class="quiz-option quiz-correct">B. Because it is never finished — new threats and systems keep appearing ✓</li>
            <li class="quiz-option">C. Because it costs too much money</li>
            <li class="quiz-option">D. Because only computers can do it</li>
        </ul>
        <p class="quiz-explanation"><em>There's no finish line — the goal is staying ahead of new threats, not completing a checklist once.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is usually the "weakest link" in a security program?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The firewall</li>
            <li class="quiz-option quiz-correct">B. People — reused passwords, rushed clicks, skipped steps ✓</li>
            <li class="quiz-option">C. The building's locks</li>
            <li class="quiz-option">D. The internet connection</li>
        </ul>
        <p class="quiz-explanation"><em>Most breaches trace back to a person making a mistake, not a broken piece of technology.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is the difference between strategy and tactics?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They mean the same thing</li>
            <li class="quiz-option quiz-correct">B. Strategy is the long-term plan; tactics are the specific actions that carry it out ✓</li>
            <li class="quiz-option">C. Tactics is the long-term plan; strategy is the specific action</li>
            <li class="quiz-option">D. Strategy only applies to attackers, never defenders</li>
        </ul>
        <p class="quiz-explanation"><em>Strategy sets the long-term direction; tactics are the concrete steps taken this week to move toward it.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Volt points out that the same cadet can request AND approve their own supply order. What kind of problem is that?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A technical control problem, fixable with a firewall rule</li>
            <li class="quiz-option quiz-correct">B. A business process problem — no technical control catches it ✓</li>
            <li class="quiz-option">C. Not a security problem at all</li>
            <li class="quiz-option">D. A strategy problem only</li>
        </ul>
        <p class="quiz-explanation"><em>Who is allowed to approve what, and in what order, is a process design question — a firewall can't fix it.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why doesn't a perfect firewall make a security program "finished"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because firewalls are always broken</li>
            <li class="quiz-option quiz-correct">B. Because people and processes can still create gaps a technical control can't reach ✓</li>
            <li class="quiz-option">C. Because Astro doesn't trust firewalls</li>
            <li class="quiz-option">D. It does — a perfect firewall means the job is done</li>
        </ul>
        <p class="quiz-explanation"><em>Technology covers technical gaps, but human behavior and process design create separate gaps that no firewall reaches.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Rivet thinks the newest firewall means the job is done. Sort the Citadel's open items into strategy, tactics, or process-fix so the crew sees what's actually still open.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a1-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm1', 'lesson' => 'lesson05']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the open-items list, then classify each one as strategy,
            tactics, or process-fix.
        </p>
    </div>

</div>
