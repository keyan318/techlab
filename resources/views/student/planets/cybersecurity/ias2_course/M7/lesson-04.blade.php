{{-- M7 · Lesson 7.4: High Availability Architectures — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a7-l4.json) completes the lesson.
Syllabus: ITP1333 Unit G.4 High Availability (HA) Architectures & Load Balancing. --}}

<div class="lesson-fragment" data-module="m7" data-lesson="04">

    <h1 class="lesson-heading">Lesson 7.4: High Availability Architectures</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> identify a single point of failure in a system architecture, and explain how redundancy and load balancing remove it.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>High availability (HA)</strong> designs a system so it keeps running even when one part fails. The
        core idea is eliminating a <strong>single point of failure</strong> — any one component whose failure alone
        would take the whole system down. The fix is <strong>redundancy</strong>: a second relay if the first
        fails, a second power source if the first cuts out. <strong>Load balancing</strong> spreads work across
        multiple redundant components during normal operation, so failover is instant rather than needing someone
        to notice and manually switch over.
    </p>

    <p class="body-text">
        HA and backups solve different problems. A backup recovers data after a failure, with some downtime while
        it's restored. HA tries to prevent the downtime in the first place, by making sure a single failure never
        takes the whole system down at all.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">How is HA different from a backup?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. HA recovers data after a failure</li>
            <li class="quiz-option quiz-correct">B. HA tries to prevent the downtime ✓</li>
            <li class="quiz-option">C. HA is one copy kept offsite only</li>
            <li class="quiz-option">D. HA needs no redundant parts at all</li>
        </ul>
        <p class="quiz-explanation"><em>A backup recovers data after a failure, with downtime. HA keeps the system running when one part fails.</em></p>
    </div>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Rivet points at the relay diagram: one relay, one power feed, one antenna — if any single piece fails, the
        whole Citadel loses contact with its allies. "That's three single points of failure in one system. Add a
        second relay and a second power feed, load-balanced so both run all the time, and losing one doesn't lose
        the connection — it just runs a little leaner until we fix it."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">How many single points of failure did Rivet find in the relay?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. One</li>
            <li class="quiz-option">B. Two</li>
            <li class="quiz-option">C. None at all</li>
            <li class="quiz-option quiz-correct">D. Three ✓</li>
        </ul>
        <p class="quiz-explanation"><em>One relay, one power feed, one antenna: three single points of failure in one system.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A single relay is a single point of failure; redundant, load-balanced relays remove it">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">Single Relay</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">one failure = total loss</text>
            <line x1="170" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Redundant Relays</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">load-balanced, both active</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Removing the single point of failure — one failure no longer means total loss</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, how do the redundant relays run?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Load-balanced, both active ✓</li>
            <li class="quiz-option">B. One active, one switched off</li>
            <li class="quiz-option">C. Both off until someone acts</li>
            <li class="quiz-option">D. Only one relay, kept spare</li>
        </ul>
        <p class="quiz-explanation"><em>Load-balanced relays both run all the time, so failover is instant.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is a "single point of failure"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A component that is always the most expensive part of a system</li>
            <li class="quiz-option quiz-correct">B. Any one component whose failure alone would take the whole system down ✓</li>
            <li class="quiz-option">C. A backup that has been tested successfully</li>
            <li class="quiz-option">D. The last step in a recovery plan</li>
        </ul>
        <p class="quiz-explanation"><em>A single point of failure is a component with no redundancy — if it fails, the whole system fails with it.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. How does redundancy address a single point of failure?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. By removing the component entirely</li>
            <li class="quiz-option quiz-correct">B. By adding a second, equivalent component that can take over if the first fails ✓</li>
            <li class="quiz-option">C. By making backups more frequent</li>
            <li class="quiz-option">D. Redundancy has nothing to do with single points of failure</li>
        </ul>
        <p class="quiz-explanation"><em>A redundant component provides an alternative path, so one failure doesn't bring down the whole system.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does load balancing add on top of simple redundancy?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing — it's the same as redundancy</li>
            <li class="quiz-option quiz-correct">B. It spreads work across the redundant components during normal operation, so failover is instant ✓</li>
            <li class="quiz-option">C. It reduces the total number of components needed</li>
            <li class="quiz-option">D. It replaces the need for any backups</li>
        </ul>
        <p class="quiz-explanation"><em>Load balancing keeps redundant components actively running together, so a failure doesn't require a manual, delayed switchover.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. How is high availability different from a backup strategy?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They are the same thing</li>
            <li class="quiz-option quiz-correct">B. HA tries to prevent downtime in the first place; a backup recovers data after a failure, with some downtime while restoring ✓</li>
            <li class="quiz-option">C. Backups prevent downtime; HA recovers data</li>
            <li class="quiz-option">D. HA only applies to physical hardware, never software</li>
        </ul>
        <p class="quiz-explanation"><em>HA and backups solve different problems — avoiding the outage versus recovering from one that already happened.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. How many single points of failure did Rivet identify in the original relay system?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. None — the system was already redundant</li>
            <li class="quiz-option quiz-correct">B. Three: the relay, the power feed, and the antenna ✓</li>
            <li class="quiz-option">C. One: only the antenna</li>
            <li class="quiz-option">D. It's impossible to have more than one single point of failure</li>
        </ul>
        <p class="quiz-explanation"><em>Each of the three components (relay, power feed, antenna) had no backup — any one of them failing alone would break the whole system.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> The relay system has three single points of failure. Read the architecture note, then classify which components need redundancy.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a7-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm7', 'lesson' => 'lesson04']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the architecture note, then classify each component as
            single-point-of-failure or already-redundant.
        </p>
    </div>

</div>
