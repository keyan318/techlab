{{-- M4 · Lesson 4.1: Network Management — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m4-l1.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="01">

    <h1 class="lesson-heading">Lesson 4.1: Network Management</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can name the five areas of network management, explain why baselines, documentation and change control matter, and use monitoring to find a failed device.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Network management</strong> is everything you do to keep a network working, safe and understood. A classic checklist is <strong>FCAPS</strong>:
    </p>
    <ul class="body-list">
        <li><strong>Fault</strong> management: detect, isolate and fix failures.</li>
        <li><strong>Configuration</strong> management: know and control how every device is set up.</li>
        <li><strong>Accounting</strong>: track who uses what (usage, billing).</li>
        <li><strong>Performance</strong> management: watch speed, delay and load so the network stays healthy.</li>
        <li><strong>Security</strong> management: control access, apply updates, watch the logs.</li>
    </ul>
    <p class="body-text">
        Four habits make it work:
    </p>
    <ul class="body-list">
        <li><strong>Monitoring</strong>: check devices continually with tools such as <code>ping</code>, <code>traceroute</code>, <strong>SNMP</strong> (a manager polling devices for their status) and <strong>syslog</strong> (devices sending event messages to one place).</li>
        <li><strong>Baselines</strong>: record what "normal" looks like, such as usual delay and load, so you can tell when something is wrong.</li>
        <li><strong>Documentation</strong>: a network diagram, an <strong>address plan</strong> (which range is for which kind of device), and an inventory of devices.</li>
        <li><strong>Change control</strong>: plan a change, get it approved, test it, keep a way to undo it, then record it, and keep backups of device configurations.</li>
    </ul>
    <p class="body-text">
        When something fails at 3 a.m., a good diagram and a clean address plan turn a long search into a quick fix.
    </p>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        <strong>Astro</strong> spreads the ship's plans across the workbench. "A captain who cannot see the network cannot defend it. From now on we watch it, write it down, and change it on purpose."
    </p>
    <p class="body-text">
        <strong>Volt</strong> pins the address plan to the wall: servers on <code>.2</code> to <code>.9</code>, printers on <code>.10</code> to <code>.19</code>, crew devices from DHCP at <code>.100</code> to <code>.150</code>. <strong>Rivet</strong> starts a ping sweep. "First job: find out what is actually down."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 172" role="img" aria-label="The five FCAPS areas of network management">
            <text x="330" y="18" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">FCAPS: five jobs of network management</text>
            <rect x="10" y="34" width="120" height="120" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="70" y="80" text-anchor="middle" font-size="34" font-weight="800" fill="#b3261e">F</text>
            <text x="70" y="104" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">Fault</text>
            <text x="70" y="124" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">find and fix</text>
            <text x="70" y="140" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">failures</text>
            <rect x="140" y="34" width="120" height="120" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="200" y="80" text-anchor="middle" font-size="34" font-weight="800" fill="#2f6fe0">C</text>
            <text x="200" y="104" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">Configuration</text>
            <text x="200" y="124" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">track and control</text>
            <text x="200" y="140" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">settings</text>
            <rect x="270" y="34" width="120" height="120" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="330" y="80" text-anchor="middle" font-size="34" font-weight="800" fill="#f08c1a">A</text>
            <text x="330" y="104" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">Accounting</text>
            <text x="330" y="124" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">who uses</text>
            <text x="330" y="140" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">what</text>
            <rect x="400" y="34" width="120" height="120" rx="14" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="460" y="80" text-anchor="middle" font-size="34" font-weight="800" fill="#2fa56b">P</text>
            <text x="460" y="104" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">Performance</text>
            <text x="460" y="124" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">keep it fast</text>
            <text x="460" y="140" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">and healthy</text>
            <rect x="530" y="34" width="120" height="120" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="590" y="80" text-anchor="middle" font-size="34" font-weight="800" fill="#7c5cff">S</text>
            <text x="590" y="104" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">Security</text>
            <text x="590" y="124" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">protect and</text>
            <text x="590" y="140" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">control access</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which FCAPS area is about detecting and fixing failures?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Accounting</li>
            <li class="quiz-option quiz-correct">B. Fault management ✓</li>
            <li class="quiz-option">C. Security</li>
            <li class="quiz-option">D. Configuration</li>
        </ul>
        <p class="quiz-explanation"><em>Fault management is about finding out something failed, isolating it and repairing it.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is a baseline?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A backup cable</li>
            <li class="quiz-option quiz-correct">B. A record of normal performance to compare against ✓</li>
            <li class="quiz-option">C. The first address in a pool</li>
            <li class="quiz-option">D. A type of firewall rule</li>
        </ul>
        <p class="quiz-explanation"><em>A baseline records normal delay, load and behavior, so you can tell when the network is behaving differently.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why keep an address plan and a network diagram?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They make cables faster</li>
            <li class="quiz-option quiz-correct">B. They let anyone understand the network and fix problems quickly ✓</li>
            <li class="quiz-option">C. They replace the need for monitoring</li>
            <li class="quiz-option">D. They are required to power on a switch</li>
        </ul>
        <p class="quiz-explanation"><em>Documentation turns a mystery into a map. During an outage it saves time and prevents mistakes such as address conflicts.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> sweep the network to find what is down, then make the printer follow the address plan. <strong>Success:</strong> astro reaches the server, the printer and the sensor, and the printer uses <code>192.168.1.10/24</code>.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m4-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm4', 'lesson' => 'lesson01']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. You use Linux commands there; the ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
