{{-- M1 · Lesson 1.4: Switches, Routers &amp; VLANs — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m1-l4.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="04">

    <h1 class="lesson-heading">Lesson 1.4: Switches, Routers &amp; VLANs</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what a switch and a router each do, and use a VLAN to isolate one group of devices.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>switch</strong> connects devices inside one network. It learns the MAC address behind each port and sends each frame only where it needs to go.
    </p>
    <p class="body-text">
        A <strong>router</strong> connects different networks. It reads the IP address of each packet and chooses the next hop. The address a host uses to leave its own network is its <strong>default gateway</strong>, which is the router.
    </p>
    <p class="body-text">
        A <strong>VLAN</strong> (virtual LAN) splits one physical switch into separate virtual switches. Devices in different VLANs cannot reach each other directly, even when plugged into the same switch. Two reasons to use them: <strong>security</strong> (keep sensitive devices apart) and <strong>less noise</strong> (broadcasts stay inside one VLAN).
    </p>
    <p class="body-text">
        A simple rule: to talk inside one network, use a switch. To talk between networks, you need a router.
    </p>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        <strong>Volt</strong> taps the shield console. "This controller must never share a wire with the comms deck. If a Doom hack lands on a comms console, it must not reach my shields."
    </p>
    <p class="body-text">
        <strong>Rivet</strong> nods. "Two zones on one switch. Then I will add a router as a guarded door between them, so only the traffic we choose can cross."
    </p>
    <p class="body-text">
        <strong>Astro</strong> approves both. "Segment first, connect second."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 235" role="img" aria-label="A switch split into two VLANs, with a router joining them">
            <rect x="250" y="90" width="150" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="325.0" y="125.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Relay switch</text>
            <rect x="20" y="20" width="130" height="50" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="85.0" y="42.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">astro</text>
            <text x="85.0" y="61.0" text-anchor="middle" font-size="12.5" fill="#14306b">VLAN 1</text>
            <rect x="20" y="90" width="130" height="50" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="85.0" y="112.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">rivet</text>
            <text x="85.0" y="131.0" text-anchor="middle" font-size="12.5" fill="#14306b">VLAN 1</text>
            <rect x="20" y="170" width="130" height="50" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="85.0" y="192.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">volt</text>
            <text x="85.0" y="211.0" text-anchor="middle" font-size="12.5" fill="#6b3a00">VLAN 20</text>
            <line x1="150" y1="45" x2="250" y2="105" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="150" y1="115" x2="250" y2="120" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="150" y1="195" x2="250" y2="138" stroke="#f08c1a" stroke-width="3" stroke-linecap="round"/>
            <rect x="500" y="90" width="140" height="60" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="570.0" y="117.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">Router (gate)</text>
            <text x="570.0" y="136.0" text-anchor="middle" font-size="12.5" fill="#3b2a8a">joins the VLANs</text>
            <line x1="400" y1="110" x2="500" y2="110" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="400" y1="132" x2="500" y2="132" stroke="#f08c1a" stroke-width="3" stroke-linecap="round"/>
            <text x="325" y="60" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">One switch, two virtual switches</text>
            <text x="325" y="190" text-anchor="middle" font-size="12.5" font-weight="500" fill="#33406b">Devices in different VLANs cannot talk directly</text>
            <text x="325" y="208" text-anchor="middle" font-size="12.5" font-weight="500" fill="#33406b">They need the router to cross</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does a VLAN do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Makes cables longer</li>
            <li class="quiz-option quiz-correct">B. Splits one switch into separate virtual networks ✓</li>
            <li class="quiz-option">C. Turns a switch into a router</li>
            <li class="quiz-option">D. Encrypts every message</li>
        </ul>
        <p class="quiz-explanation"><em>A VLAN creates separate virtual switches on one physical switch, so devices in different VLANs are isolated from each other.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Two devices are in different VLANs on the same switch. What do they need to talk?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing, they already can</li>
            <li class="quiz-option">B. A longer cable</li>
            <li class="quiz-option quiz-correct">C. A router between the VLANs ✓</li>
            <li class="quiz-option">D. A faster switch</li>
        </ul>
        <p class="quiz-explanation"><em>Different VLANs are different networks. Only a router can carry traffic between them.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which device makes decisions using IP addresses?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Router ✓</li>
            <li class="quiz-option">B. Hub</li>
            <li class="quiz-option">C. Cable</li>
            <li class="quiz-option">D. NIC light</li>
        </ul>
        <p class="quiz-explanation"><em>A router reads IP addresses to choose the path. A switch works with MAC addresses.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> first isolate <code>volt</code> in VLAN 20, then join the zones with the router <code>gate</code>. <strong>Success:</strong> astro reaches rivet, cannot reach volt's old address, and finally reaches volt through the router.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m1-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson04']) }}">
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
