{{-- M8 · Lesson 8.2: Physical Vulnerability Assessment — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a8-l2.json) completes the lesson.
Syllabus: ITP1333 Unit H.2 Physical Vulnerability Assessment (PVA). --}}

<div class="lesson-fragment" data-module="m8" data-lesson="02">

    <h1 class="lesson-heading">Lesson 8.2: Physical Vulnerability Assessment</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> conduct a physical vulnerability assessment by finding and severity-rating gaps in access control, surveillance, and structure.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>physical vulnerability assessment</strong> systematically walks a site the way an intruder would,
        looking for weaknesses across three areas: <strong>access control</strong> (an unlocked door, a propped-open
        emergency exit), <strong>surveillance</strong> (a blind spot no camera covers, a sensor that's been
        disabled), and <strong>structure</strong> (a weak wall, a window that doesn't lock). Each finding gets a
        severity — critical, moderate, or low — based on how easily it could be exploited and how much damage that
        would cause.
    </p>

    <p class="body-text">
        The value of the assessment comes from finding gaps before an intruder does, and fixing the critical ones
        first — not treating every finding with the same urgency regardless of severity.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">How should assessment findings be fixed?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Critical ones first ✓</li>
            <li class="quiz-option">B. All at the same time</li>
            <li class="quiz-option">C. Cheapest ones first</li>
            <li class="quiz-option">D. In the order found</li>
        </ul>
        <p class="quiz-explanation"><em>Each finding gets a severity. Fix the critical gaps first.</em></p>
    </div>

    <h2 class="section-heading">Volt's Drill</h2>

    <p class="body-text">
        Volt walks the Citadel's perimeter like an intruder would. "This side door hasn't locked properly in
        weeks — critical, that's a way straight in. This camera has a two-meter blind spot near the cargo bay —
        moderate, it's a gap but the door there is still locked. This decorative panel is loose but leads nowhere
        useful — low. Rank them honestly, or we'll spend a week fixing the low-severity panel while the door stays
        broken."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why is the side door critical but the camera only moderate?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The camera costs more to repair</li>
            <li class="quiz-option quiz-correct">B. The door is a way straight in ✓</li>
            <li class="quiz-option">C. The door is newer than the camera</li>
            <li class="quiz-option">D. The camera covers the starmap</li>
        </ul>
        <p class="quiz-explanation"><em>A broken door is a direct way in. The blind spot is a gap, but the door there is still locked.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 230" role="img" aria-label="Rank findings by severity — fix the critical gap before the cosmetic one">
            <rect x="245" y="75" width="150" height="62" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2.5"/>
            <text x="320" y="102" text-anchor="middle" font-size="16" font-weight="700" fill="#4a35b8">Physical Assessment</text>
            <text x="320" y="123" text-anchor="middle" font-size="12" fill="#4a35b8">walk it like an intruder would</text>
            <rect x="35" y="20" width="170" height="62" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="120" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1a14">Access Control</text>
            <text x="120" y="68" text-anchor="middle" font-size="11.5" fill="#7a1a14">unlocked doors, propped exits</text>
            <line x1="120" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <rect x="235" y="20" width="170" height="62" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#8a4b00">Surveillance</text>
            <text x="320" y="68" text-anchor="middle" font-size="11.5" fill="#8a4b00">blind spots, disabled sensors</text>
            <line x1="320" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <rect x="435" y="20" width="170" height="62" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="520" y="47" text-anchor="middle" font-size="14" font-weight="700" fill="#1c4f8f">Structure</text>
            <text x="520" y="68" text-anchor="middle" font-size="11.5" fill="#1c4f8f">weak walls, broken locks</text>
            <line x1="520" y1="82" x2="320" y2="75" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="220" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">Rank findings by severity — fix the critical gap before the cosmetic one</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, which area covers blind spots?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Access control</li>
            <li class="quiz-option">B. Structure</li>
            <li class="quiz-option">C. Severity</li>
            <li class="quiz-option quiz-correct">D. Surveillance ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Surveillance covers blind spots and disabled sensors. Access control covers doors and exits.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does a physical vulnerability assessment do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Reviews only the organization's written policies</li>
            <li class="quiz-option quiz-correct">B. Systematically walks a site looking for weaknesses in access control, surveillance, and structure ✓</li>
            <li class="quiz-option">C. Only checks digital systems, not physical spaces</li>
            <li class="quiz-option">D. Assesses employee morale</li>
        </ul>
        <p class="quiz-explanation"><em>A PVA looks specifically at physical weaknesses across access, surveillance, and structural categories.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why does each finding in a PVA get a severity rating?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. To make the report longer</li>
            <li class="quiz-option quiz-correct">B. So critical gaps get fixed first, instead of treating every finding with equal urgency ✓</li>
            <li class="quiz-option">C. Severity ratings are purely decorative and don't affect priority</li>
            <li class="quiz-option">D. Because regulations require exactly three findings per assessment</li>
        </ul>
        <p class="quiz-explanation"><em>Ranking severity ensures the most exploitable, most damaging gaps get addressed before less urgent ones.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Volt rate the side door that hasn't locked properly as "critical"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because it's the newest door in the Citadel</li>
            <li class="quiz-option quiz-correct">B. Because it's a direct, easy way in, with high potential for damage ✓</li>
            <li class="quiz-option">C. Because it's the most decorative door</li>
            <li class="quiz-option">D. All findings are automatically rated critical</li>
        </ul>
        <p class="quiz-explanation"><em>An unlocked door presents an easy, direct entry point — high likelihood and high impact, hence critical.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why did Volt rate the loose decorative panel as "low," despite it also being a physical flaw?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because low-severity findings don't need to be documented</li>
            <li class="quiz-option quiz-correct">B. Because it leads nowhere useful, so exploiting it wouldn't cause much harm ✓</li>
            <li class="quiz-option">C. Because Volt didn't have time to look at it closely</li>
            <li class="quiz-option">D. Because decorative items are never assessed</li>
        </ul>
        <p class="quiz-explanation"><em>Even a real flaw can be low severity if exploiting it wouldn't lead to meaningful access or damage.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What did Volt warn against doing with the findings?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Fixing the critical door first</li>
            <li class="quiz-option quiz-correct">B. Spending a week on the low-severity panel while the critical door stays broken ✓</li>
            <li class="quiz-option">C. Ranking findings by severity at all</li>
            <li class="quiz-option">D. Walking the perimeter more than once</li>
        </ul>
        <p class="quiz-explanation"><em>Misallocating effort toward a low-severity item while a critical gap remains open defeats the purpose of ranking findings.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four PVA findings need severity ratings before repairs are scheduled.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a8-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm8', 'lesson' => 'lesson02']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the findings, then classify each by severity.
        </p>
    </div>

</div>
