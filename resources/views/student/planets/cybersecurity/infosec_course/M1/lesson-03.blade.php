{{-- M1 · Lesson 1.3: Anatomy of an Attack — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Volt / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c1-l3.json) completes the lesson.
Syllabus: ITP1232 Unit A.3 Anatomy of an Attack. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="03">

    <h1 class="lesson-heading">Lesson 1.3: Anatomy of an Attack</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can describe the four stages a break-in usually follows — recon, entry,
        hideout and data-grab — and use that shape to trace what an attacker did after the fact, then close what
        they left behind.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Most attacks aren't one action, they're a sequence with a shape:
    </p>

    <ul class="body-list">
        <li><strong>Recon</strong>: the attacker looks around first — which doors exist, which ones might be weak.</li>
        <li><strong>Entry</strong>: they get in, usually through a specific vulnerability (a weak password, an unpatched bug).</li>
        <li><strong>Hideout</strong>: once inside, they set up a way back in later, often a new account or a hidden program, so they don't have to break in twice.</li>
        <li><strong>Data-grab</strong>: they take what they came for, and leave.</li>
    </ul>

    <p class="body-text">
        Recognizing this shape matters even after the fact: if you find one stage in a log, the others are probably
        nearby, and the hideout — the account or program left behind — is the part most worth finding, because it's
        the door that stays open after the original break-in is long over.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        <strong>Volt's</strong> sensors tripped at 04:12 on an outbound transfer, and by the time the connection
        dropped, something had already left the building. "I caught the exit, not the entry," Volt tells the crew.
        "That means I need the log, start to finish, before I touch anything."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> pulls up the overnight log. Reading it end to end, the shape is unmistakable: a scan
        of the relay's ports, a login with a weak password, a new account nobody created, then a file copied out
        through a port that shouldn't exist. <strong>Astro</strong>: "Recon, entry, hideout, grab. Volt, that account
        and that port — that's the door they're planning to use again. Shut it."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="An attack in four stages: recon, entry, hideout, data-grab, in sequence">
            <rect x="10" y="50" width="140" height="70" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="80" y="80" text-anchor="middle" font-size="15" font-weight="700" fill="#b3261e">Recon</text>
            <text x="80" y="100" text-anchor="middle" font-size="11.5" fill="#7a1a14">03:40 port scan</text>

            <line x1="150" y1="85" x2="180" y2="85" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="184,85 172,79 172,91" fill="#7c5cff"/>

            <rect x="185" y="50" width="140" height="70" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="255" y="80" text-anchor="middle" font-size="15" font-weight="700" fill="#8a4b00">Entry</text>
            <text x="255" y="100" text-anchor="middle" font-size="11.5" fill="#8a4b00">03:47 weak login</text>

            <line x1="325" y1="85" x2="355" y2="85" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="359,85 347,79 347,91" fill="#7c5cff"/>

            <rect x="360" y="50" width="140" height="70" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="430" y="74" text-anchor="middle" font-size="15" font-weight="700" fill="#4a35b8">Hideout</text>
            <text x="430" y="92" text-anchor="middle" font-size="11.5" fill="#4a35b8">03:52 svc_update</text>
            <text x="430" y="107" text-anchor="middle" font-size="11.5" fill="#4a35b8">planted, port left open</text>

            <line x1="500" y1="85" x2="530" y2="85" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="534,85 522,79 522,91" fill="#7c5cff"/>

            <rect x="535" y="50" width="95" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="582" y="80" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">Data-grab</text>
            <text x="582" y="100" text-anchor="middle" font-size="11" fill="#14306b">03:58</text>

            <text x="320" y="145" text-anchor="middle" font-size="13" fill="#4a35b8">Volt's sensors caught the exit at 04:12 — the hideout is what's left to close</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. An attacker scans the relay's open ports before doing anything else. Which stage is this?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Recon ✓</li>
            <li class="quiz-option">B. Entry</li>
            <li class="quiz-option">C. Hideout</li>
            <li class="quiz-option">D. Data-grab</li>
        </ul>
        <p class="quiz-explanation"><em>Recon is looking around before acting — checking which doors exist and which might be weak.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. The intruder created a new account, svc_update, disguised as a system service. Which stage is this?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Recon</li>
            <li class="quiz-option">B. Entry</li>
            <li class="quiz-option quiz-correct">C. Hideout ✓</li>
            <li class="quiz-option">D. Data-grab</li>
        </ul>
        <p class="quiz-explanation"><em>A hideout is a way back in the attacker sets up after entry, so they don't need to break in the same way twice.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why is finding the hideout usually the most urgent part of tracing a breach?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It's always the easiest stage to find in a log</li>
            <li class="quiz-option quiz-correct">B. It's the door that stays open after the original break-in is over ✓</li>
            <li class="quiz-option">C. It never appears in security logs</li>
            <li class="quiz-option">D. It's the only stage that involves a password</li>
        </ul>
        <p class="quiz-explanation"><em>Recon, entry and the data-grab are already done by the time you're reading the log — the hideout is the part still actively threatening the system.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Volt's sensors tripped on an outbound file transfer. Which stage did that alert catch?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Recon</li>
            <li class="quiz-option">B. Entry</li>
            <li class="quiz-option">C. Hideout</li>
            <li class="quiz-option quiz-correct">D. Data-grab ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The data-grab is the attacker taking what they came for; an outbound transfer of a file is exactly that stage.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. To actually close the hideout svc_update left behind, what has to happen (not just noticing it)?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Reading the log a second time</li>
            <li class="quiz-option quiz-correct">B. Disabling the planted account and stopping the process holding its open port ✓</li>
            <li class="quiz-option">C. Renaming the account to something less obvious</li>
            <li class="quiz-option">D. Waiting for the attacker to log out on their own</li>
        </ul>
        <p class="quiz-explanation"><em>A hideout only stops being a threat once the account is disabled and the process/port it depends on is actually stopped — spotting it isn't the same as closing it.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> trace the overnight break-in through <code>logs\breach.log</code>, start to finish,
        then close what the intruder left behind: disable the planted <code>svc_update</code> account and stop the
        process holding its backdoor port open. <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c1-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm1', 'lesson' => 'lesson03']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>type</code>, <code>net user</code> and <code>taskkill</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
