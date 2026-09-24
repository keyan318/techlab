{{-- M1 · Lesson 1.3: Cables & Signals — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m1-l3.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="03">

    <h1 class="lesson-heading">Lesson 1.3: Cables & Signals</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can name the three kinds of transmission media, explain what a network card (NIC) does, and bring a dead network link back up.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A message needs a road to travel on. That road is the <strong>transmission medium</strong>:
    </p>
    <ul class="body-list">
        <li><strong>Twisted-pair copper</strong> (the Ethernet cable): carries electrical signals, reaches about 100 meters per run, and is cheap.</li>
        <li><strong>Fiber-optic</strong>: carries pulses of light through glass. It is very fast, reaches kilometers and is not bothered by electrical noise, but costs more.</li>
        <li><strong>Wireless</strong>: uses radio waves, as in Wi-Fi. No cable is needed, but the air is shared and the signal can be blocked or jammed.</li>
    </ul>
    <p class="body-text">
        Every device connects to the medium through a <strong>NIC</strong> (network interface card). Each NIC has a unique hardware address called a <strong>MAC address</strong>. On Windows the first NIC is called <code>Ethernet</code>, the second <code>Ethernet 2</code>, and <code>ipconfig /all</code> shows each one's MAC address (its <em>Physical Address</em>).
    </p>
    <p class="body-text">
        Two numbers describe a link. <strong>Bandwidth</strong> is how much data it can carry per second (for example 1 Gbps). <strong>Latency</strong> is the delay for one trip, which is the time <code>ping</code> reports.
    </p>
    <p class="body-text">
        One more piece of hardware: a <strong>hub</strong> repeats every signal to every port, while a <strong>switch</strong> learns which device is on which port and sends traffic only there.
    </p>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        Overhead, Planet Doom's jamming ship pours radio noise across the site. <strong>Rivet</strong> shakes his head at the Wi-Fi gear. "Wireless is useless while they jam us. We go wired."
    </p>
    <p class="body-text">
        Then a red light goes out on his workbench. "A cable worked loose and the link is down. The NIC has an address but sees no signal. Let's find it."
    </p>
    <p class="body-text">
        <strong>Astro</strong> reminds the crew: "A network problem is not always a software problem. Check the cable and the link first."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 190" role="img" aria-label="Three transmission media: copper, fiber and wireless">
            <rect x="10" y="14" width="200" height="160" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="110" y="44" text-anchor="middle" font-size="16" font-weight="700" fill="#6b3a00">Twisted-pair copper</text>
            <text x="110" y="74" text-anchor="middle" font-size="12.5" font-weight="500" fill="#6b3a00">Electrical signal</text>
            <text x="110" y="100" text-anchor="middle" font-size="12.5" font-weight="500" fill="#6b3a00">Up to about 100 m a run</text>
            <text x="110" y="126" text-anchor="middle" font-size="12.5" font-weight="500" fill="#6b3a00">Cheap, can pick up noise</text>
            <text x="110" y="152" text-anchor="middle" font-size="12.5" font-weight="500" fill="#6b3a00">Ethernet cable</text>
            <rect x="230" y="14" width="200" height="160" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="330" y="44" text-anchor="middle" font-size="16" font-weight="700" fill="#14306b">Fiber-optic</text>
            <text x="330" y="74" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14306b">Pulses of light</text>
            <text x="330" y="100" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14306b">Kilometers of reach</text>
            <text x="330" y="126" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14306b">Immune to electrical noise</text>
            <text x="330" y="152" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14306b">Costs more</text>
            <rect x="450" y="14" width="200" height="160" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="550" y="44" text-anchor="middle" font-size="16" font-weight="700" fill="#3b2a8a">Wireless</text>
            <text x="550" y="74" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">Radio waves</text>
            <text x="550" y="100" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">No cable needed</text>
            <text x="550" y="126" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">Shared, can be jammed</text>
            <text x="550" y="152" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">Wi-Fi</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which medium is not affected by electrical interference?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Twisted-pair copper</li>
            <li class="quiz-option quiz-correct">B. Fiber-optic ✓</li>
            <li class="quiz-option">C. A hub</li>
            <li class="quiz-option">D. A MAC address</li>
        </ul>
        <p class="quiz-explanation"><em>Fiber carries light, not electricity, so electrical noise does not disturb it.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does a NIC do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It stores your files</li>
            <li class="quiz-option quiz-correct">B. It connects a device to the network medium ✓</li>
            <li class="quiz-option">C. It assigns IP addresses to everyone</li>
            <li class="quiz-option">D. It boosts the Wi-Fi from space</li>
        </ul>
        <p class="quiz-explanation"><em>The network interface card is the part of a device that plugs into the medium. Each one has its own MAC address.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Rivet's console has an address but astro cannot ping it. What should you check first?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Whether the link is up and the cable is seated ✓</li>
            <li class="quiz-option">B. Whether the sky is cloudy</li>
            <li class="quiz-option">C. Whether the ship has a name</li>
            <li class="quiz-option">D. Nothing, pings never fail</li>
        </ul>
        <p class="quiz-explanation"><em>Start at the bottom: cable and link. A device with an address but a dead link cannot send or receive anything.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Rivet's link is down. Prove it with a failed ping, then bring the link back up. <strong>Success:</strong> astro can ping rivet and rivet can ping astro.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m1-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson03']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. You type Windows commands there (Command Prompt), like on a school PC; the ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
