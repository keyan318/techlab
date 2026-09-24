{{-- M5 · Lesson 5.6: Data & Network Security Services (course finale) — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c5-l6.json) completes the lesson AND the course.
Syllabus: ITP1232 Unit E, Data & Network Security Services (capstone). --}}

<div class="lesson-fragment" data-module="m5" data-lesson="06">

    <h1 class="lesson-heading">Lesson 5.6: Data & Network Security Services — The Final Siege</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can name the security services that work together to defend a system —
        awareness, authentication, incident response, integrity checking, and non-repudiation — and apply all of
        them under pressure, at once.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Every lesson in this course taught one discipline at a time: spotting phishing, locking down weak
        passwords, hunting a rogue process, catching a tampered file, rejecting a forged order. Real attacks rarely
        come one at a time. A serious attacker throws several of these at once, hoping that fixing one leaves you
        too distracted to catch the rest.
    </p>

    <p class="body-text">
        <strong>Data and network security services</strong> is the umbrella term for all of it working together:
        <strong>awareness</strong> (catching phishing before anyone clicks), <strong>authentication</strong>
        (passwords strong enough to resist cracking), <strong>incident response</strong> (finding and stopping an
        active intrusion), <strong>integrity checking</strong> (hashes that expose silent tampering), and
        <strong>non-repudiation</strong> (signatures that expose a forged order). None of them replaces the others.
        The Citadel only holds when all of them hold.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        00:00. Every alarm the Citadel has goes off at once. <strong>Vex</strong>'s voice cuts through the comms:
        "Every wall, every door, every key, all at once. Let's see which one of us breaks first, Captain."
    </p>

    <p class="body-text">
        <strong>Astro</strong> doesn't flinch. "He's not doing anything you haven't already beaten, Cadet — he's
        just doing all of it at the same time. Work the list. Triage the inbox. Prove the weak door, then shut it.
        Kill what shouldn't be running. Restore what he touched. And don't obey a single order that isn't really
        mine." <strong>Volt</strong> adds: "Every lesson you've learned just held the line at once. That's what
        Information Security actually is."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 230" role="img" aria-label="Five security services under attack at once: awareness, authentication, incident response, integrity, and non-repudiation, all defended together">
            <rect x="10" y="20" width="115" height="80" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="67" y="50" text-anchor="middle" font-size="12.5" font-weight="700" fill="#4a35b8">Awareness</text>
            <text x="67" y="70" text-anchor="middle" font-size="11" fill="#4a35b8">triage the inbox</text>

            <rect x="135" y="20" width="115" height="80" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="192" y="50" text-anchor="middle" font-size="12.5" font-weight="700" fill="#4a35b8">Authentication</text>
            <text x="192" y="70" text-anchor="middle" font-size="11" fill="#4a35b8">prove, then fix, the weak login</text>

            <rect x="260" y="20" width="115" height="80" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="317" y="50" text-anchor="middle" font-size="12.5" font-weight="700" fill="#4a35b8">Incident Response</text>
            <text x="317" y="70" text-anchor="middle" font-size="11" fill="#4a35b8">stop the rogue process</text>

            <rect x="385" y="20" width="115" height="80" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="442" y="50" text-anchor="middle" font-size="12.5" font-weight="700" fill="#4a35b8">Integrity</text>
            <text x="442" y="70" text-anchor="middle" font-size="11" fill="#4a35b8">restore the tampered file</text>

            <rect x="510" y="20" width="120" height="80" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="570" y="50" text-anchor="middle" font-size="12.5" font-weight="700" fill="#4a35b8">Non-Repudiation</text>
            <text x="570" y="70" text-anchor="middle" font-size="11" fill="#4a35b8">reject the forged order</text>

            <rect x="120" y="150" width="400" height="60" rx="14" fill="#eafaf0" stroke="#1e8e3e" stroke-width="2"/>
            <text x="320" y="185" text-anchor="middle" font-size="15" font-weight="700" fill="#0d5c26">DOOM ATTACK REPELLED — CODEXIA IS SAFE</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Catching a phishing message before anyone clicks it is an example of which security service?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Awareness ✓</li>
            <li class="quiz-option">B. Non-repudiation</li>
            <li class="quiz-option">C. Integrity checking</li>
            <li class="quiz-option">D. Authentication</li>
        </ul>
        <p class="quiz-explanation"><em>Awareness is about recognizing an attack, like phishing, before it succeeds.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Hashing a file to detect a silent, unauthorized edit is an example of which security service?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Authentication</li>
            <li class="quiz-option quiz-correct">B. Integrity checking ✓</li>
            <li class="quiz-option">C. Awareness</li>
            <li class="quiz-option">D. Non-repudiation</li>
        </ul>
        <p class="quiz-explanation"><em>Comparing hashes to catch a change nobody authorized is exactly what integrity checking does.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Rejecting a "shields down" order because it isn't properly signed is an example of which security service?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Incident response</li>
            <li class="quiz-option">B. Awareness</li>
            <li class="quiz-option quiz-correct">C. Non-repudiation ✓</li>
            <li class="quiz-option">D. Data masking</li>
        </ul>
        <p class="quiz-explanation"><em>Checking a signature to confirm who really sent an order, and refusing to act without one, is non-repudiation at work.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why does Vex attack several weaknesses at the same time instead of just one?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It's a coincidence, not a strategy</li>
            <li class="quiz-option quiz-correct">B. Hoping the crew, distracted fixing one problem, misses the rest ✓</li>
            <li class="quiz-option">C. Multiple attacks are always weaker than one</li>
            <li class="quiz-option">D. Because single attacks aren't technically possible</li>
        </ul>
        <p class="quiz-explanation"><em>A combined attack is a deliberate strategy: it counts on defenders getting overwhelmed and missing something.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What's the real takeaway of "data and network security services"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. One strong defense is always enough</li>
            <li class="quiz-option quiz-correct">B. Several disciplines have to hold together — none of them replaces the others ✓</li>
            <li class="quiz-option">C. Awareness training makes the other defenses unnecessary</li>
            <li class="quiz-option">D. Passwords alone stop every kind of attack</li>
        </ul>
        <p class="quiz-explanation"><em>Awareness, authentication, incident response, integrity, and non-repudiation each cover a different failure mode. A real defense needs all of them.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission — The Final Siege:</strong> Vex hits every layer at once: a phishing wave, a weak console
        password, a rogue process, a tampered shield config, and a forged final order. Work through every alert.
        <strong>Success:</strong> every objective in the simulator is checked off — "DOOM ATTACK REPELLED, Codexia
        is safe." A teaser for Information Security 2 follows.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c5-l6" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm5', 'lesson' => 'lesson06']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt) — everything you've learned across this course, in one final defense. Objectives tick off as you go.
        </p>
    </div>

</div>
