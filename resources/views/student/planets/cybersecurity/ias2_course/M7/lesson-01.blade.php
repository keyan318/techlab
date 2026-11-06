{{-- M7 · Lesson 7.1: Disaster Recovery Concepts — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a7-l1.json) completes the lesson.
Syllabus: ITP1333 Unit G.1 Disaster Recovery (DR) Concepts. --}}

<div class="lesson-fragment" data-module="m7" data-lesson="01">

    <h1 class="lesson-heading">Lesson 7.1: Disaster Recovery Concepts</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> apply RTO (recovery time objective) and RPO (recovery point objective) to judge whether an actual recovery met its disaster recovery targets.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Disaster recovery (DR)</strong> is restoring IT systems after a disruption — a hardware failure, an
        attack, a disaster. Two numbers define what "recovered" actually means: <strong>RTO (recovery time
        objective)</strong> is the maximum acceptable time a system can be down before it must be back up.
        <strong>RPO (recovery point objective)</strong> is the maximum acceptable amount of data you can afford to
        lose, measured in time — if your last backup was 45 minutes old when the failure hit, you lose up to 45
        minutes of data.
    </p>

    <p class="body-text">
        Both numbers should come from a real conversation about cost and impact, not a guess. A tighter RTO or RPO
        usually costs more to achieve (more frequent backups, faster failover systems), so the targets should match
        what the system actually needs, not the smallest number that sounds impressive.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does RPO measure?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The most time a system can stay down</li>
            <li class="quiz-option">B. The cost of buying a new backup drive</li>
            <li class="quiz-option">C. The number of backups in storage now</li>
            <li class="quiz-option quiz-correct">D. The most data you can afford to lose ✓</li>
        </ul>
        <p class="quiz-explanation"><em>RPO is the maximum acceptable data loss, measured in time. RTO is the maximum acceptable downtime.</em></p>
    </div>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        The comms relay goes down for three hours during a drill; the last backup was 30 minutes old at the time of
        failure. Astro checks the DR plan: RTO is 4 hours, RPO is 1 hour. "Three hours down is inside our RTO. Thirty
        minutes of data at risk is inside our RPO. This recovery met both targets — that's not luck, that's the plan
        working the way it was built to."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">The relay was down 3 hours with an RTO of 4 hours. Did it meet the RTO?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Yes, it was inside the RTO ✓</li>
            <li class="quiz-option">B. No, it went past the RTO</li>
            <li class="quiz-option">C. No, the RPO was missed</li>
            <li class="quiz-option">D. Only if it was a real attack</li>
        </ul>
        <p class="quiz-explanation"><em>Three hours down is inside a four-hour RTO, and 30 minutes of data is inside a one-hour RPO.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="RTO defines maximum acceptable downtime; RPO defines maximum acceptable data loss">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">RTO</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">max acceptable downtime</text>
            <line x1="170" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">RPO</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">max acceptable data loss (in time)</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Two numbers, agreed in advance, define what "recovered" actually means</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what does RTO define?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Max acceptable data loss</li>
            <li class="quiz-option quiz-correct">B. Max acceptable downtime ✓</li>
            <li class="quiz-option">C. The backup media type</li>
            <li class="quiz-option">D. The offsite copy place</li>
        </ul>
        <p class="quiz-explanation"><em>RTO is maximum acceptable downtime. RPO is maximum acceptable data loss.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does RTO (recovery time objective) measure?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. How much data can be lost</li>
            <li class="quiz-option quiz-correct">B. The maximum acceptable time a system can be down before it must be back up ✓</li>
            <li class="quiz-option">C. The cost of a backup system</li>
            <li class="quiz-option">D. The number of backups taken per day</li>
        </ul>
        <p class="quiz-explanation"><em>RTO is a time target for how quickly a system must be restored after a disruption.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does RPO (recovery point objective) measure?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The maximum acceptable time a system can be down</li>
            <li class="quiz-option quiz-correct">B. The maximum acceptable amount of data loss, measured in time since the last backup ✓</li>
            <li class="quiz-option">C. The number of staff needed for recovery</li>
            <li class="quiz-option">D. The physical location of a backup</li>
        </ul>
        <p class="quiz-explanation"><em>RPO defines how much data (measured by time since the last good backup) is acceptable to lose.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. The comms relay was down for 3 hours with an RTO of 4 hours. Did this recovery meet its RTO?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. No, it exceeded the RTO</li>
            <li class="quiz-option quiz-correct">B. Yes, 3 hours is inside the 4-hour RTO ✓</li>
            <li class="quiz-option">C. RTO doesn't apply to comms systems</li>
            <li class="quiz-option">D. Not enough information to tell</li>
        </ul>
        <p class="quiz-explanation"><em>3 hours of downtime is within the 4-hour maximum acceptable downtime, so the RTO target was met.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why should RTO and RPO targets come from a real conversation about cost and impact, not a guess?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because tighter targets are always free to achieve</li>
            <li class="quiz-option quiz-correct">B. Because a tighter target usually costs more (more frequent backups, faster failover), so it should match actual need ✓</li>
            <li class="quiz-option">C. Because RTO and RPO are legally fixed numbers</li>
            <li class="quiz-option">D. Because guessing is always more accurate than analysis</li>
        </ul>
        <p class="quiz-explanation"><em>Setting unnecessarily aggressive targets wastes resources; setting them too loose risks unacceptable impact — the right number balances both.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why does Astro say meeting both RTO and RPO in the drill "isn't luck"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because the outcome was random and happened to work out</li>
            <li class="quiz-option quiz-correct">B. Because the DR plan was built with specific targets in mind, and the recovery performed as designed ✓</li>
            <li class="quiz-option">C. Because RTO and RPO don't actually matter</li>
            <li class="quiz-option">D. Because the drill was rigged in advance</li>
        </ul>
        <p class="quiz-explanation"><em>A recovery meeting its planned targets reflects the plan working as intended, not chance.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Three recent recovery events need checking against the DR plan's RTO and RPO targets.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a7-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm7', 'lesson' => 'lesson01']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the DR plan and the recovery log, then classify each
            event as within-target or exceeded-target.
        </p>
    </div>

</div>
