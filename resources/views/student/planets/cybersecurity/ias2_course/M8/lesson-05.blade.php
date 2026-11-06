{{-- M8 · Lesson 8.5: Physical Intrusion Detection — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a8-l5.json) completes the lesson.
Syllabus: ITP1333 Unit H.5 Physical Intrusion Detection (Sensors, CCTV, Alarms). --}}

<div class="lesson-fragment" data-module="m8" data-lesson="05">

    <h1 class="lesson-heading">Lesson 8.5: Physical Intrusion Detection</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> explain intrusion detection as a compensating control for when prevention fails, and identify a detection gap that leaves an entry point unmonitored.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Locks and badges try to <strong>prevent</strong> entry; <strong>intrusion detection</strong> — motion
        sensors, CCTV, alarms — exists for when prevention fails anyway, so a breach is noticed instead of going
        unnoticed indefinitely. Detection without a fast, real response is close to useless: a camera nobody
        watches, or an alarm that rings with no one assigned to respond, still lets an intruder finish before
        anyone acts.
    </p>

    <p class="body-text">
        A door with a strong lock but no camera and no alarm has prevention but no detection — if the lock is ever
        beaten, nothing else notices. Detection and prevention are different jobs, and a facility needs both.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Why is detection without response close to useless?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Cameras cost too much money to keep running</li>
            <li class="quiz-option quiz-correct">B. The intruder finishes before anyone acts ✓</li>
            <li class="quiz-option">C. Alarms always ring at the wrong time</li>
            <li class="quiz-option">D. Detection stops every intruder anyway</li>
        </ul>
        <p class="quiz-explanation"><em>A camera nobody watches or an alarm nobody answers still lets an intruder finish.</em></p>
    </div>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        Volt reviews the cargo bay: a solid lock, but no camera covers the loading door and no alarm is wired to
        it. "If someone gets past that lock — and locks do get beaten — nobody notices until the next scheduled
        walk-through, maybe hours later. Prevention without detection means we only find out after the damage is
        already done."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What is missing at the cargo bay?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A lock</li>
            <li class="quiz-option">B. A door</li>
            <li class="quiz-option quiz-correct">C. Detection ✓</li>
            <li class="quiz-option">D. A guard post</li>
        </ul>
        <p class="quiz-explanation"><em>There is a solid lock (prevention), but no camera and no alarm, so a beaten lock goes unnoticed.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="Physical security needs prevention, detection, and response together">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">Prevention</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">locks, badges — stop entry</text>
            <line x1="170" y1="66" x2="237" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="245,66 233,60 233,72" fill="#7c5cff"/>
            <rect x="245" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Detection</text>
            <text x="320" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">cameras, alarms — notice a breach</text>
            <line x1="395" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Response</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">someone assigned to act on it</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Detection without a fast response still lets an intruder finish before anyone acts</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what is the response part?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Locks that stop anyone entering</li>
            <li class="quiz-option quiz-correct">B. Someone assigned to act ✓</li>
            <li class="quiz-option">C. Cameras that notice</li>
            <li class="quiz-option">D. Badges for the staff</li>
        </ul>
        <p class="quiz-explanation"><em>Prevention stops entry, detection notices a breach, and response means someone is assigned to act.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is the role of intrusion detection (sensors, CCTV, alarms), compared to a lock?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It prevents entry the same way a lock does</li>
            <li class="quiz-option quiz-correct">B. It notices a breach when prevention (like a lock) fails, so it doesn't go unnoticed indefinitely ✓</li>
            <li class="quiz-option">C. It has no relationship to physical security at all</li>
            <li class="quiz-option">D. It replaces the need for any locks</li>
        </ul>
        <p class="quiz-explanation"><em>Detection is a separate layer that catches what prevention missed, rather than stopping entry itself.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why is a camera nobody watches, or an alarm nobody responds to, "close to useless"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because cameras and alarms are always unreliable technology</li>
            <li class="quiz-option quiz-correct">B. Because detection without a fast response still lets an intruder finish the job before anyone acts ✓</li>
            <li class="quiz-option">C. Because unmonitored cameras are illegal</li>
            <li class="quiz-option">D. Because alarms should never actually be triggered</li>
        </ul>
        <p class="quiz-explanation"><em>Detection only has value if it leads to a timely response — otherwise the intrusion is simply noticed too late to matter.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What gap did Volt identify at the cargo bay's loading door?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The lock itself was broken</li>
            <li class="quiz-option quiz-correct">B. No camera and no alarm covered it, so a defeated lock would go unnoticed ✓</li>
            <li class="quiz-option">C. There was no lock installed at all</li>
            <li class="quiz-option">D. The door was propped open constantly</li>
        </ul>
        <p class="quiz-explanation"><em>The door had prevention (a lock) but no detection — if the lock failed, nothing else would notice.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why does a facility need both prevention and detection, not just one?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because prevention alone is always sufficient by itself</li>
            <li class="quiz-option quiz-correct">B. Because locks can be beaten, and detection is what catches that when it happens ✓</li>
            <li class="quiz-option">C. Because detection alone always stops an intruder from entering</li>
            <li class="quiz-option">D. Because regulations require exactly two security measures</li>
        </ul>
        <p class="quiz-explanation"><em>No lock is unbeatable — detection provides a second layer that notices when prevention has failed.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What happens without detection if a lock is ever defeated?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The breach is caught immediately regardless</li>
            <li class="quiz-option quiz-correct">B. Nobody notices until the next scheduled walk-through, possibly hours later ✓</li>
            <li class="quiz-option">C. The alarm sounds automatically without any sensors</li>
            <li class="quiz-option">D. The facility automatically locks down</li>
        </ul>
        <p class="quiz-explanation"><em>Without detection, a successful breach can go unnoticed for a long time, giving an intruder ample time to act.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four Citadel entry points need reviewing for detection coverage gaps.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a8-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm8', 'lesson' => 'lesson05']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the coverage audit, then classify each entry point as
            covered or detection-gap.
        </p>
    </div>

</div>
