{{-- M2 · Lesson 2.3: Cybersecurity Standards — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c2-l3.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="03">

    <h1 class="lesson-heading">Lesson 2.3: Cybersecurity Standards</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain why security teams use a standard framework instead of ad-hoc
        guesses, and map a real defense to the correct NIST Cybersecurity Framework function.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        So far, the Citadel's defenses grew one crisis at a time: a password fixed here, a backdoor closed there.
        That works, but it leaves gaps nobody notices until Doom finds them. A <strong>cybersecurity standard</strong>
        (or framework) is a shared, tested rulebook that organizes defenses so nothing important gets skipped.
    </p>

    <p class="body-text">
        One widely used framework is the <strong>NIST Cybersecurity Framework (CSF)</strong>, built around five
        functions:
    </p>

    <ul class="body-list">
        <li><strong>Identify</strong>: know your assets and risks (Lesson 1.1's inventory).</li>
        <li><strong>Protect</strong>: lock things down before an attack (passwords, permissions, policy).</li>
        <li><strong>Detect</strong>: notice an attack while it's happening (sensors, logs, monitoring).</li>
        <li><strong>Respond</strong>: act once you've detected something (disabling accounts, killing processes).</li>
        <li><strong>Recover</strong>: get back to normal after (restoring from backup).</li>
    </ul>

    <p class="body-text">
        Mapping your real defenses to these five functions shows you, at a glance, where the gaps are — not by
        guessing, but by checking against a rulebook that's already been through the wringer elsewhere.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        <strong>Astro</strong> calls a review: "We've patched a lot of holes these last few weeks. I want proof
        we're actually covered, not just a list of things we fixed by luck." He hands the crew a rulebook: NIST's
        five functions. "Every defense we've built should fit somewhere on this list. If it doesn't fit anywhere,
        that's not a gap in the rulebook — that's a gap in us."
    </p>

    <p class="body-text">
        The crew works through it: the asset inventory is clearly <em>Identify</em>. The strong password policy on
        the relay console is <em>Protect</em>. Volt's sensors, which first caught the 02:47 intrusion, are
        <em>Detect</em>. Disabling `svc_update` and closing its port was <em>Respond</em>. But one function comes
        up empty: nothing the crew has built actually <em>detects</em> a tampered backup before it's restored.
        Astro: "Found our gap. Guesses don't scale. A framework does."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="The NIST CSF five functions, each with one of the Citadel's defenses mapped to it">
            <rect x="10" y="20" width="110" height="90" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="65" y="42" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">Identify</text>
            <text x="65" y="68" text-anchor="middle" font-size="11" fill="#14306b">asset</text>
            <text x="65" y="82" text-anchor="middle" font-size="11" fill="#14306b">inventory</text>

            <rect x="130" y="20" width="110" height="90" rx="12" fill="#e9f7ef" stroke="#1f7a4d" stroke-width="2"/>
            <text x="185" y="42" text-anchor="middle" font-size="13" font-weight="700" fill="#175a3a">Protect</text>
            <text x="185" y="68" text-anchor="middle" font-size="11" fill="#175a3a">relay</text>
            <text x="185" y="82" text-anchor="middle" font-size="11" fill="#175a3a">password</text>

            <rect x="250" y="20" width="110" height="90" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="305" y="42" text-anchor="middle" font-size="13" font-weight="700" fill="#8a4b00">Detect</text>
            <text x="305" y="68" text-anchor="middle" font-size="11" fill="#8a4b00">Volt's</text>
            <text x="305" y="82" text-anchor="middle" font-size="11" fill="#8a4b00">sensors</text>

            <rect x="370" y="20" width="110" height="90" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="425" y="42" text-anchor="middle" font-size="13" font-weight="700" fill="#7a1a14">Respond</text>
            <text x="425" y="68" text-anchor="middle" font-size="11" fill="#7a1a14">disable</text>
            <text x="425" y="82" text-anchor="middle" font-size="11" fill="#7a1a14">svc_update</text>

            <rect x="490" y="20" width="140" height="90" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2" stroke-dasharray="5 4"/>
            <text x="560" y="42" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">Recover</text>
            <text x="560" y="65" text-anchor="middle" font-size="11" fill="#4a35b8">backup-restore</text>
            <text x="560" y="90" text-anchor="middle" font-size="10.5" fill="#4a35b8">(needs a Detect</text>
            <text x="560" y="102" text-anchor="middle" font-size="10.5" fill="#4a35b8">step too — gap!)</text>

            <text x="320" y="145" text-anchor="middle" font-size="13" fill="#333">Five functions, five real defenses — one gap found by checking the framework, not luck.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why use a framework like NIST CSF instead of just fixing problems as they come up?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Frameworks are required by every company, no other reason</li>
            <li class="quiz-option quiz-correct">B. A framework organizes defenses so gaps are found systematically, not by luck ✓</li>
            <li class="quiz-option">C. Frameworks make attacks impossible</li>
            <li class="quiz-option">D. It's faster than fixing individual problems</li>
        </ul>
        <p class="quiz-explanation"><em>A tested framework reveals gaps a crew might never notice by only reacting to whatever breaks next.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. The crew's asset inventory (what to protect) maps to which NIST CSF function?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Identify ✓</li>
            <li class="quiz-option">B. Protect</li>
            <li class="quiz-option">C. Detect</li>
            <li class="quiz-option">D. Recover</li>
        </ul>
        <p class="quiz-explanation"><em>Identify is about knowing your assets and risks — exactly what an inventory captures.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Volt's sensors, which caught the 02:47 intrusion while it was happening, map to which function?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Protect</li>
            <li class="quiz-option quiz-correct">B. Detect ✓</li>
            <li class="quiz-option">C. Respond</li>
            <li class="quiz-option">D. Identify</li>
        </ul>
        <p class="quiz-explanation"><em>Detect means noticing an attack while it's in progress — that's exactly what an alarm/sensor system does.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Disabling the planted `svc_update` account and closing its port, after the breach was found, is which function?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Identify</li>
            <li class="quiz-option">B. Protect</li>
            <li class="quiz-option quiz-correct">C. Respond ✓</li>
            <li class="quiz-option">D. Recover</li>
        </ul>
        <p class="quiz-explanation"><em>Respond is the action taken once something has already been detected — disabling and closing come after the alarm, not before.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. The crew has a backup-restore process (Recover), but Astro finds it's missing coverage for one other function. Which one?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Identify</li>
            <li class="quiz-option">B. Protect</li>
            <li class="quiz-option quiz-correct">C. Detect — nothing currently checks whether a backup was tampered with before it's restored ✓</li>
            <li class="quiz-option">D. Respond</li>
        </ul>
        <p class="quiz-explanation"><em>Recovering from a backup is only safe if you can also detect that the backup itself wasn't tampered with — that's the gap the framework revealed.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Astro wants the Citadel's defenses proven against a real framework, not guesses.
        Read the mapping notes, then classify each of the five listed defenses to its correct NIST CSF function —
        and fix the one gap the note flags. <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c2-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm2', 'lesson' => 'lesson03']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. You type Windows commands there (Command Prompt), such as
            <code>type</code> and <code>classify</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
