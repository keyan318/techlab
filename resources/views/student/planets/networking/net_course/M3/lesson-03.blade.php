{{-- M3 · Lesson 3.3: Centralized DHCP — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m3-l3.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="03">

    <h1 class="lesson-heading">Lesson 3.3: Centralized DHCP</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain why one DHCP server per subnet is not always practical, what a relay agent does, and how a broadcast is limited by routers.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A small network can have one DHCP server on the same segment as its clients. A bigger network has many subnets. <strong>Centralized DHCP</strong> means running one (or a redundant pair of) DHCP servers that manage the address pools for <em>all</em> subnets in one place. The benefit is that pools, options and reservations are managed together and stay consistent.
    </p>
    <p class="body-text">
        There is a catch. A DHCP Discover is a <strong>broadcast</strong>, and <strong>routers do not forward broadcasts</strong>. A client on another subnet would never reach the central server.
    </p>
    <p class="body-text">
        The fix is a <strong>DHCP relay agent</strong>, usually configured on the router (on Cisco routers with <code>ip helper-address</code>). It listens for the broadcast, adds the address of the subnet it came from, and forwards the request as a <strong>unicast</strong> to the central server. The server uses that address to choose the right pool and sends the offer back through the relay.
    </p>
    <p class="body-text">
        <em>The lab simulator has no relay agent.</em> So the lab shows the problem for real (volt gets no lease) and uses the other classic answer: a DHCP server running on the router itself, serving the far subnet.
    </p>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        <strong>Volt</strong> runs <code>ipconfig /renew</code> on the shield deck. Nothing. "My Discover goes out and dies at the router. The central server is on the other deck and never hears it."
    </p>
    <p class="body-text">
        <strong>Rivet</strong> nods. "Right, routers do not carry broadcasts. Proper fix is a relay. We have no relay gear, so the router itself will give out the shield deck's addresses."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 196" role="img" aria-label="A DHCP relay agent forwarding a broadcast to a central server">
            <rect x="10" y="24" width="190" height="60" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="105.0" y="51.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">Central DHCP server</text>
            <text x="105.0" y="70.0" text-anchor="middle" font-size="12.5" fill="#14663f">192.168.1.2</text>
            <rect x="10" y="124" width="190" height="56" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="105.0" y="149.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Comms clients</text>
            <text x="105.0" y="168.0" text-anchor="middle" font-size="12.5" fill="#14306b">astro, rivet</text>
            <line x1="200" y1="54" x2="250" y2="54" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="200" y1="152" x2="250" y2="152" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="250" y1="54" x2="250" y2="152" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="250" y1="103" x2="300" y2="103" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <rect x="300" y="72" width="120" height="62" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="360.0" y="100.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">gate</text>
            <text x="360.0" y="119.0" text-anchor="middle" font-size="12.5" fill="#3b2a8a">router</text>
            <rect x="510" y="72" width="140" height="62" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="580.0" y="100.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">Shield client</text>
            <text x="580.0" y="119.0" text-anchor="middle" font-size="12.5" fill="#6b3a00">volt, other subnet</text>
            <line x1="420" y1="103" x2="510" y2="103" stroke="#f08c1a" stroke-width="3" stroke-linecap="round"/>
            <text x="465" y="158" text-anchor="middle" font-size="13" font-weight="700" fill="#b3261e">A broadcast stops at the router</text>
            <text x="465" y="176" text-anchor="middle" font-size="12" font-weight="500" fill="#b3261e">so volt's Discover never reaches the server</text>
            <path d="M 360 72 C 350 30, 290 22, 204 40" fill="none" stroke="#7c5cff" stroke-width="2.5" stroke-dasharray="6 5"/>
            <text x="372" y="30" text-anchor="start" font-size="12" font-weight="700" fill="#4a35b8">a relay agent forwards it as a unicast</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why can a DHCP Discover not reach a server on another subnet by itself?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Routers do not forward broadcasts ✓</li>
            <li class="quiz-option">B. The cable is too long</li>
            <li class="quiz-option">C. DHCP only works on Wi-Fi</li>
            <li class="quiz-option">D. The server is asleep</li>
        </ul>
        <p class="quiz-explanation"><em>A Discover is a broadcast, and a router stops broadcasts at the network boundary.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does a DHCP relay agent do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Blocks DHCP traffic</li>
            <li class="quiz-option">B. Assigns static addresses</li>
            <li class="quiz-option quiz-correct">C. Forwards a client's broadcast to a central DHCP server as a unicast ✓</li>
            <li class="quiz-option">D. Encrypts leases</li>
        </ul>
        <p class="quiz-explanation"><em>The relay agent turns the local broadcast into a unicast to the central server and passes the answer back.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is the main benefit of centralized DHCP?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It removes the need for routers</li>
            <li class="quiz-option">B. It makes DHCP faster than TCP</li>
            <li class="quiz-option quiz-correct">C. One place to manage address pools and settings for many subnets ✓</li>
            <li class="quiz-option">D. It gives every device the same IP</li>
        </ul>
        <p class="quiz-explanation"><em>A central server keeps every subnet's pool, options and reservations in one place.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> serve the comms deck from the central server, then bring the shield deck online despite the router. <strong>Success:</strong> astro and rivet lease from <code>ship-server</code>, volt leases <code>192.168.2.x</code> from <code>gate</code>, and volt can reach astro.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m3-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm3', 'lesson' => 'lesson03']) }}">
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
