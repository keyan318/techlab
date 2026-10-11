{{-- M8 · Lesson 8.3: Site Location Selection — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a8-l3.json) completes the lesson.
Syllabus: ITP1333 Unit H.3 Site Location Selection. --}}

<div class="lesson-fragment" data-module="m8" data-lesson="03">

    <h1 class="lesson-heading">Lesson 8.3: Site Location Selection</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> evaluate a candidate site location against four factors — natural hazard exposure, approach visibility, utility redundancy, and proximity to other risks — and decide whether it is suitable.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Where a facility sits shapes its security before a single lock is installed. Four factors matter most:
        <strong>natural hazard exposure</strong> (flood zones, unstable ground), <strong>approach visibility</strong>
        (can guards see someone approaching before they arrive, or does terrain hide the approach), <strong>utility
        redundancy</strong> (is there more than one path for power and communications to reach the site), and
        <strong>proximity to other risks</strong> (is it next to something hazardous or a high-value target that
        draws unwanted attention).
    </p>

    <p class="body-text">
        A site can fail on just one of these factors and still be a poor choice overall — good visibility doesn't
        compensate for being built in a flood zone with a single power line running in.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Command proposes a new outpost site with excellent sightlines in every direction — but it sits in a known
        flood basin with a single supply line running in from one direction. Astro rejects it. "Good visibility
        doesn't matter if the whole site floods every wet season, and one damaged supply line cuts us off
        completely. One good factor doesn't offset a genuine disqualifier."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="Site selection weighs hazard exposure, approach visibility, utility redundancy, and proximity to risk">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">Hazard Exposure</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">floods, unstable ground</text>
            <line x1="170" y1="66" x2="162" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="170,66 158,60 158,72" fill="#7c5cff"/>
            <rect x="170" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="245" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">Approach Visibility</text>
            <text x="245" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">can threats be seen coming</text>
            <line x1="320" y1="66" x2="312" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="320,66 308,60 308,72" fill="#7c5cff"/>
            <rect x="320" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="395" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Utility Redundancy</text>
            <text x="395" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">more than one supply path</text>
            <line x1="470" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#8a4b00">Proximity to Risk</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#8a4b00">nearby hazards or targets</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">One good factor doesn't offset a genuine disqualifier</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why does site location matter for security, before any locks are even installed?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It doesn't — location has no security impact</li>
            <li class="quiz-option quiz-correct">B. Where a facility sits shapes its exposure to hazards, visibility, and supply risk from the start ✓</li>
            <li class="quiz-option">C. Only the building's interior design matters</li>
            <li class="quiz-option">D. Location only matters for cost, never security</li>
        </ul>
        <p class="quiz-explanation"><em>Physical location determines baseline risks (hazards, visibility, supply lines) that no amount of interior security can fully offset.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does "approach visibility" refer to in site selection?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. How attractive the building looks</li>
            <li class="quiz-option quiz-correct">B. Whether guards or sensors can see someone approaching before they arrive ✓</li>
            <li class="quiz-option">C. The color of the building's exterior</li>
            <li class="quiz-option">D. How many windows the building has</li>
        </ul>
        <p class="quiz-explanation"><em>Approach visibility is about detecting a threat's approach early, which terrain or building placement can help or hinder.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Astro reject the outpost site despite its excellent sightlines?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because sightlines don't matter at all</li>
            <li class="quiz-option quiz-correct">B. Because it sat in a flood zone with only a single supply line — a genuine disqualifier that good visibility couldn't offset ✓</li>
            <li class="quiz-option">C. Because Command proposed it, and Astro disagreed on principle</li>
            <li class="quiz-option">D. Because the site was too expensive</li>
        </ul>
        <p class="quiz-explanation"><em>A serious hazard and single point of utility failure outweighed the one strong factor (visibility).</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does "utility redundancy" mean in site selection?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Having backup food supplies</li>
            <li class="quiz-option quiz-correct">B. More than one path for power and communications to reach the site ✓</li>
            <li class="quiz-option">C. A site with no utilities at all</li>
            <li class="quiz-option">D. The number of guards stationed at a site</li>
        </ul>
        <p class="quiz-explanation"><em>Multiple supply paths for power and communications prevent a single damaged line from cutting the site off entirely.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Can a site with one very strong factor (like visibility) still be a poor overall choice?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. No, one strong factor always outweighs any weakness</li>
            <li class="quiz-option quiz-correct">B. Yes — a genuine disqualifier in another factor, like flood risk, isn't offset by strength elsewhere ✓</li>
            <li class="quiz-option">C. Only if all four factors are weak</li>
            <li class="quiz-option">D. Site selection only considers one factor at a time, never combined</li>
        </ul>
        <p class="quiz-explanation"><em>The four factors must all be reasonably sound — a serious weakness in one area can disqualify a site regardless of strength elsewhere.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Three candidate sites for a new outpost need evaluating against the four site-selection factors.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a8-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm8', 'lesson' => 'lesson03']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the site survey, then classify each candidate as
            suitable or unsuitable.
        </p>
    </div>

</div>
