{{-- M1 · Lesson 1.2: Ship's Wiring — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m1-l2.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="02">

    <h1 class="lesson-heading">Lesson 1.2: Ship's Wiring</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can tell a LAN from a WAN, name the four common topologies, and explain why the crew wires the ship as a star.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Network infrastructure</strong> is everything that makes a network work: the devices, the cables and the connections between them.
    </p>
    <p class="body-text">
        A <strong>LAN</strong> (local area network) covers one place, such as a ship, a classroom or a house. It is fast and you own it. A <strong>WAN</strong> (wide area network) covers cities or countries and is usually rented from an internet provider. The Internet is the biggest WAN of all.
    </p>
    <p class="body-text">
        The <strong>topology</strong> is the shape of the layout. Four are common:
    </p>
    <ul class="body-list">
        <li><strong>Star</strong>: every device connects to one central switch. Easy to grow. If one cable fails, only that device is lost.</li>
        <li><strong>Bus</strong>: every device shares one backbone cable. Cheap, but one break can split the whole network.</li>
        <li><strong>Ring</strong>: each device connects to two neighbors, forming a loop, and data travels around it.</li>
        <li><strong>Mesh</strong>: devices have many links to each other. Very reliable but expensive. A full mesh of <em>n</em> devices needs <em>n × (n − 1) ÷ 2</em> links.</li>
    </ul>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        Planet Doom's scouts sweep the crash site every night. <strong>Rivet</strong> lays two options on the workbench. "On a bus, one cut cable silences the whole ship. In a star, a cut silences one console."
    </p>
    <p class="body-text">
        <strong>Astro</strong> does not hesitate. "Star it is. Put the relay switch in the middle and run a cable to every console."
    </p>
    <p class="body-text">
        Rivet has already cabled astro, rivet and volt to the relay. Your job is to give the crew addresses and prove every path works.
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 215" role="img" aria-label="Four network topologies: star, bus, ring and mesh">
            <line x1="80" y1="105" x2="80" y2="52" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <line x1="80" y1="105" x2="133" y2="105" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <line x1="80" y1="105" x2="80" y2="158" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <line x1="80" y1="105" x2="27" y2="105" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <circle cx="80" cy="105" r="16" fill="#2f6fe0" stroke="#14306b" stroke-width="2"/>
            <circle cx="80" cy="52" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <circle cx="133" cy="105" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <circle cx="80" cy="158" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <circle cx="27" cy="105" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <line x1="200" y1="105" x2="330" y2="105" stroke="#7c5cff" stroke-width="4" stroke-linecap="round"/>
            <line x1="215" y1="105" x2="215" y2="70" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <circle cx="215" cy="70" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <line x1="250" y1="105" x2="250" y2="140" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <circle cx="250" cy="140" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <line x1="285" y1="105" x2="285" y2="70" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <circle cx="285" cy="70" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <line x1="320" y1="105" x2="320" y2="140" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <circle cx="320" cy="140" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <line x1="430" y1="55" x2="483" y2="105" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <line x1="483" y1="105" x2="430" y2="158" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <line x1="430" y1="158" x2="377" y2="105" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <line x1="377" y1="105" x2="430" y2="55" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <circle cx="430" cy="55" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <circle cx="483" cy="105" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <circle cx="430" cy="158" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <circle cx="377" cy="105" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <line x1="560" y1="55" x2="613" y2="55" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <line x1="560" y1="55" x2="613" y2="158" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <line x1="560" y1="55" x2="560" y2="158" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <line x1="613" y1="55" x2="613" y2="158" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <line x1="613" y1="55" x2="560" y2="158" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <line x1="613" y1="158" x2="560" y2="158" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <circle cx="560" cy="55" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <circle cx="613" cy="55" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <circle cx="613" cy="158" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <circle cx="560" cy="158" r="11" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="80" y="192" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Star</text>
            <text x="80" y="209" text-anchor="middle" font-size="12" font-weight="500" fill="#33406b">one central switch</text>
            <text x="270" y="192" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Bus</text>
            <text x="270" y="209" text-anchor="middle" font-size="12" font-weight="500" fill="#33406b">one shared cable</text>
            <text x="430" y="192" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Ring</text>
            <text x="430" y="209" text-anchor="middle" font-size="12" font-weight="500" fill="#33406b">a loop of neighbors</text>
            <text x="587" y="192" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Mesh</text>
            <text x="587" y="209" text-anchor="middle" font-size="12" font-weight="500" fill="#33406b">links between many</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why does the crew choose a star topology for the ship?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It uses the least cable of any layout</li>
            <li class="quiz-option quiz-correct">B. A cut cable only disconnects one device, not the whole network ✓</li>
            <li class="quiz-option">C. It needs no central device</li>
            <li class="quiz-option">D. It is the only layout that works with a switch</li>
        </ul>
        <p class="quiz-explanation"><em>In a star, each device has its own cable to the center. If a scout cuts one, only that console is lost. On a bus, one break can split everyone.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. How many links does a full mesh of 4 devices need?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 4</li>
            <li class="quiz-option quiz-correct">B. 6 ✓</li>
            <li class="quiz-option">C. 8</li>
            <li class="quiz-option">D. 12</li>
        </ul>
        <p class="quiz-explanation"><em>Use n × (n − 1) ÷ 2. For 4 devices that is 4 × 3 ÷ 2 = 6 links.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which one describes a WAN?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A network inside one classroom</li>
            <li class="quiz-option quiz-correct">B. A network that spans cities or countries ✓</li>
            <li class="quiz-option">C. A cable between two neighbors</li>
            <li class="quiz-option">D. A wireless mouse</li>
        </ul>
        <p class="quiz-explanation"><em>A WAN (wide area network) covers a large area, and the Internet is the biggest one. A LAN covers one place.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> address <code>astro</code>, <code>rivet</code> and <code>volt</code> in <code>192.168.1.0/24</code>, then prove every crew member can reach the others. <strong>Success:</strong> astro can ping rivet and volt, and rivet can ping volt.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m1-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson02']) }}">
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
