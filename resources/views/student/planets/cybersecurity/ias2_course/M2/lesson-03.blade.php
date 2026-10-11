{{-- M2 · Lesson 2.3: Zones of Trust — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a2-l3.json) completes the lesson.
Syllabus: ITP1333 Unit B.3 Zones of Trust. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="03">

    <h1 class="lesson-heading">Lesson 2.3: Zones of Trust</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> describe why systems are grouped into zones of increasing trust, and identify which zone a given system or file belongs in.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Not every system deserves the same trust. A <strong>zone of trust</strong> groups systems by how much they
        are trusted and how exposed they are, then controls what can cross between zones. A common pattern has
        three zones: a <strong>public zone</strong> (open to the outside, trusted least), a <strong>DMZ</strong>
        or buffer zone (limited, carefully filtered access, used for systems that must talk to both sides), and a
        <strong>restricted zone</strong> (the most sensitive systems, trusted most, exposed least).
    </p>

    <p class="body-text">
        The rule that keeps zones meaningful: traffic only crosses a boundary when there's a specific reason, and
        never straight from public to restricted. A public-facing system that needs to reach something sensitive
        goes through the DMZ, never directly.
    </p>

    <h2 class="section-heading">Volt's Drill</h2>

    <p class="body-text">
        Volt runs a drill: "Where does each of these belong — public, DMZ, or restricted?" A cadet answers fast:
        "The comms relay talks to allied fleets, so... public?" Volt shakes their head. "It talks to the outside,
        yes, but it also needs a controlled path inward. That's a DMZ system — filtered on both sides. Now, the
        starmap vault?" "Restricted," the cadet says immediately. "Exactly. It never talks to the outside directly,
        and nothing reaches it except through the DMZ first."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="Zones of trust run from public through a filtered DMZ to a restricted zone">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">Public Zone</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">least trusted</text>
            <line x1="170" y1="66" x2="237" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="245,66 233,60 233,72" fill="#7c5cff"/>
            <rect x="245" y="30" width="150" height="72" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#8a4b00">DMZ</text>
            <text x="320" y="82" text-anchor="middle" font-size="12.5" fill="#8a4b00">filtered both ways</text>
            <line x1="395" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Restricted Zone</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">most trusted, least exposed</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Traffic crosses one boundary at a time — never straight from public to restricted</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does a "zone of trust" group systems by?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Alphabetical order of their names</li>
            <li class="quiz-option quiz-correct">B. How much they are trusted and how exposed they are ✓</li>
            <li class="quiz-option">C. Who purchased them</li>
            <li class="quiz-option">D. Their physical color</li>
        </ul>
        <p class="quiz-explanation"><em>Zones group systems by trust level and exposure, then control what can move between zones.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is a DMZ zone used for?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The most sensitive systems, fully isolated from everything</li>
            <li class="quiz-option quiz-correct">B. Systems that must talk to both the outside and the inside, with filtered access on both sides ✓</li>
            <li class="quiz-option">C. A zone with no security controls at all</li>
            <li class="quiz-option">D. Public web browsing only</li>
        </ul>
        <p class="quiz-explanation"><em>A DMZ is a buffer zone for systems that legitimately need limited, filtered access to both the public side and the internal side.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Volt say the comms relay belongs in the DMZ, not the public zone?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because it never talks to anyone outside the Citadel</li>
            <li class="quiz-option quiz-correct">B. Because it needs a controlled path inward as well as outward, which the DMZ provides ✓</li>
            <li class="quiz-option">C. Because Volt dislikes the public zone</li>
            <li class="quiz-option">D. Because DMZ systems are the least trusted</li>
        </ul>
        <p class="quiz-explanation"><em>A system that must talk to both outside and inside belongs in the DMZ, where both directions are filtered.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What is the rule about traffic crossing zone boundaries?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Traffic can go straight from public to restricted if it's urgent</li>
            <li class="quiz-option quiz-correct">B. Traffic only crosses a boundary when there's a specific reason, never straight from public to restricted ✓</li>
            <li class="quiz-option">C. There is no rule — any system can reach any other</li>
            <li class="quiz-option">D. Only Astro is allowed to cross zone boundaries</li>
        </ul>
        <p class="quiz-explanation"><em>The whole point of zoning breaks down if traffic can skip straight from the least trusted zone to the most trusted one.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why does the starmap vault belong in the restricted zone?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because it needs to talk directly to allied fleets</li>
            <li class="quiz-option quiz-correct">B. Because it never talks to the outside directly, and only the DMZ can reach it ✓</li>
            <li class="quiz-option">C. Because it is the least sensitive system on the Citadel</li>
            <li class="quiz-option">D. Because Volt prefers it there for no reason</li>
        </ul>
        <p class="quiz-explanation"><em>The restricted zone holds the most sensitive systems, which are reached only through the filtered DMZ, never directly.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Volt's drill sheet lists five Citadel systems with no zone assigned. Read it, then place each system in its correct zone.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a2-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm2', 'lesson' => 'lesson03']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the system list, then classify each one into public, dmz,
            or restricted.
        </p>
    </div>

</div>
