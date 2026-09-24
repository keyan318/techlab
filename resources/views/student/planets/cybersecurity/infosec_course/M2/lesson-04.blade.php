{{-- M2 · Lesson 2.4: Global Surveillance & Privacy — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c2-l4.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="04">

    <h1 class="lesson-heading">Lesson 2.4: Global Surveillance & Privacy</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain how everyday software can quietly track you, find a process
        that's sending data out without permission, and shut it down.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Not every threat breaks in through a door. Some threats are already running, disguised as something
        useful — an app you installed for a convenience, quietly sending your data somewhere you never agreed to.
        This is <strong>surveillance</strong>: someone watching or tracking you without your informed consent, and
        it's a real <strong>privacy</strong> risk even when nothing was "hacked" in the usual sense.
    </p>

    <p class="body-text">
        The warning signs are usually visible if you look: an unexpected program in your process list, a port
        listening that you never opened, network traffic going out to an address you don't recognize. Two commands
        do most of the work here: <code>netstat -an</code> shows what's listening or connected, and
        <code>tasklist</code> shows what's actually running. Whatever's causing both is your suspect.
    </p>

    <p class="body-text">
        Once you find it, the fix is direct: stop the process (<code>taskkill</code>), and its port stops
        listening along with it. "Free" software is a common source of this — if you're not paying for a product,
        sometimes your data is the payment.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        Late at night, <strong>Volt</strong> spots something odd in the traffic logs: outbound connections nobody
        authorized, at regular intervals, going nowhere the crew recognizes. "Something on this machine keeps
        phoning home," Volt reports. "I don't know what yet. But it's been doing it for hours."
    </p>

    <p class="body-text">
        The trail leads to a free star-chart app someone installed weeks ago, useful for navigation, never
        flagged as a risk. Doom's taunt confirms it: <em>"That free star-chart app you installed? We wrote it."</em>
        Volt: "Free things cost something. Find the process, kill it, and make sure its port goes down with it."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 190" role="img" aria-label="starchart-free.exe runs on the Citadel console and sends outbound traffic to Doom on port 8891; killing the process closes the port">
            <rect x="30" y="30" width="180" height="70" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="120" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">CITADEL-CORE</text>
            <text x="120" y="73" text-anchor="middle" font-size="11.5" fill="#14306b">starchart-free.exe running</text>
            <text x="120" y="88" text-anchor="middle" font-size="11" fill="#14306b">port 8891 listening</text>

            <line x1="210" y1="65" x2="290" y2="65" stroke="#b3261e" stroke-width="3" stroke-dasharray="5 4"/>
            <polygon points="294,65 282,59 282,71" fill="#b3261e"/>
            <text x="252" y="52" text-anchor="middle" font-size="11" fill="#b3261e">tracker-beacon</text>

            <rect x="298" y="30" width="140" height="70" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="368" y="60" text-anchor="middle" font-size="13" font-weight="700" fill="#7a1a14">Planet Doom</text>
            <text x="368" y="78" text-anchor="middle" font-size="11" fill="#7a1a14">receiving position data</text>

            <text x="120" y="132" text-anchor="middle" font-size="12" fill="#333">taskkill /im starchart-free.exe /f</text>
            <line x1="120" y1="140" x2="120" y2="155" stroke="#1f7a4d" stroke-width="3"/>
            <polygon points="120,159 114,147 126,147" fill="#1f7a4d"/>
            <rect x="30" y="160" width="180" height="26" rx="8" fill="#e9f7ef" stroke="#1f7a4d" stroke-width="2"/>
            <text x="120" y="178" text-anchor="middle" font-size="12" font-weight="700" fill="#175a3a">process killed — port 8891 closed</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What makes the free star-chart app a privacy risk, even though nobody "hacked" anything?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It uses too much disk space</li>
            <li class="quiz-option quiz-correct">B. It secretly sends the crew's data out without informed consent ✓</li>
            <li class="quiz-option">C. It's slow to load</li>
            <li class="quiz-option">D. It has a confusing interface</li>
        </ul>
        <p class="quiz-explanation"><em>Surveillance doesn't require breaking in — software you installed yourself can still violate your privacy by tracking you without real consent.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which command shows what's listening or connected on the network?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. type</li>
            <li class="quiz-option quiz-correct">B. netstat -an ✓</li>
            <li class="quiz-option">C. classify</li>
            <li class="quiz-option">D. dir</li>
        </ul>
        <p class="quiz-explanation"><em>netstat -an lists active and listening network connections, which is how Volt spots the beacon in the first place.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which command shows what programs are actually running, so you can spot the one that doesn't belong?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. netstat -an</li>
            <li class="quiz-option quiz-correct">B. tasklist ✓</li>
            <li class="quiz-option">C. findstr</li>
            <li class="quiz-option">D. whoami</li>
        </ul>
        <p class="quiz-explanation"><em>tasklist lists running processes; comparing it against what should be running is how you find the intruder.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why does killing starchart-free.exe also close port 8891?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It doesn't; the port stays open forever</li>
            <li class="quiz-option quiz-correct">B. The port was only open because that process was listening on it; stopping the process stops the listener ✓</li>
            <li class="quiz-option">C. Killing a process always closes every port on the machine</li>
            <li class="quiz-option">D. You have to close the port separately with a different command</li>
        </ul>
        <p class="quiz-explanation"><em>A listening port belongs to whatever process opened it — terminate the process and the listener goes with it.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What's the practical lesson from "free things cost something" here?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Never install any software, ever</li>
            <li class="quiz-option quiz-correct">B. If you're not paying for a product, check what it's actually doing — sometimes your data is the payment ✓</li>
            <li class="quiz-option">C. Free software is always safer than paid software</li>
            <li class="quiz-option">D. Only Doom writes free software</li>
        </ul>
        <p class="quiz-explanation"><em>Convenient "free" tools are a common vector for surveillance software — it's worth checking what data they actually send, and where.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> something on the Citadel console keeps phoning home. Check what's listening and
        what's running, find the process that doesn't belong, and shut it down for good.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c2-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm2', 'lesson' => 'lesson04']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. You type Windows commands there (Command Prompt), such as
            <code>netstat -an</code>, <code>tasklist</code> and <code>taskkill</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
