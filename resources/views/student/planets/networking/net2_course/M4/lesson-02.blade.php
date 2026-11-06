{{-- M4 · Lesson 4.2: Inter-VLAN Routing: Router-on-a-Stick vs Layer 3 Switch — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g4-l2.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="02">

    <h1 class="lesson-heading">Lesson 4.2: Inter-VLAN Routing: Router-on-a-Stick vs Layer 3 Switch</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can route between VLANs with router sub-interfaces over one trunk, and compare that with a Layer 3 switch.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        VLANs are separate networks, so traffic between them must be routed. In lesson 1.1 the router had one cable per VLAN. With ten VLANs, that's ten router ports. Two better ways:
    </p>
    <p class="body-text">
        <strong>Router-on-a-stick</strong>: one router port, connected to a switch trunk, split into <strong>sub-interfaces</strong>, one per VLAN. Each sub-interface gets <code>encapsulation dot1Q &lt;vlan&gt;</code> (which tag it handles) and the VLAN's gateway address. The physical port itself needs no address, just <code>no shutdown</code>.
    </p>
    <pre class="code-block"><code>interface g0/0
 no shutdown
interface g0/0.10
 encapsulation dot1Q 10
 ip address 192.168.10.1 255.255.255.0</code></pre>
    <p class="body-text">
        A frame from VLAN 10 to VLAN 20 goes up the trunk tagged 10, the router routes it, and it comes back down the same cable tagged 20. Cheap and simple, but every routed packet crosses that one cable twice, so it becomes the bottleneck.
    </p>
    <p class="body-text">
        <strong>Layer 3 switch</strong>: a switch that can also route. You give each VLAN a virtual interface, an <strong>SVI</strong> (<code>interface vlan 10</code> with an address), and turn on <code>ip routing</code>. Routing happens inside the switch at full speed. This is what most distribution layers use today.
    </p>
    <ul class="body-list">
        <li>Router-on-a-stick: one link, few VLANs, low traffic, small sites.</li>
        <li>Layer 3 switch: many VLANs, lots of traffic between them, the distribution layer of a bigger network.</li>
    </ul>
    <p class="body-text">
        Either way, every PC's default gateway is its VLAN's router address, and the switch port toward a router-on-a-stick must be a trunk.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What is the weak point of router-on-a-stick?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Every routed packet crosses one cable twice ✓</li>
            <li class="quiz-option">B. It needs one router port per VLAN in use</li>
            <li class="quiz-option">C. It cannot route between VLANs on a trunk</li>
            <li class="quiz-option">D. Each sub-interface needs its own separate cable run</li>
        </ul>
        <p class="quiz-explanation"><em>Traffic goes up the trunk and back down the same cable, so that one cable becomes the bottleneck.</em></p>
    </div>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        The deck has three VLANs now: crew, workshop and a new one for guests from Earth's ships. The deck router has exactly one free port, and one cable to the switch. "One cable, three VLANs," says <strong>Rivet</strong>. "Same trick as the trunk, on the router side."
    </p>
    <p class="body-text">
        <strong>Astro</strong> nods. "Router-on-a-stick. When the Grid is bigger, the distribution layer gets Layer 3 switches. For a deck, one smart cable is plenty."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">How does Rivet fit three VLANs onto one router port?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. With three more cables to the switch</li>
            <li class="quiz-option">B. With a hub between the decks</li>
            <li class="quiz-option quiz-correct">C. With sub-interfaces on a trunk ✓</li>
            <li class="quiz-option">D. With one VLAN on each floor</li>
        </ul>
        <p class="quiz-explanation"><em>Router-on-a-stick: one router port, split into sub-interfaces, one per VLAN, over a trunk to the switch.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 290" role="img" aria-label="Router-on-a-stick: one router port split into sub-interfaces .10 .20 .30 over a trunk; compared with a Layer 3 switch using SVIs">
            <rect x="20" y="20" width="300" height="70" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="170.0" y="42.2" text-anchor="middle" font-size="13.5" font-weight="700" fill="#3b2a8a">deck-rtr g0/0 (one cable)</text>
            <text x="170.0" y="58.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">g0/0.10  192.168.10.1  dot1Q 10</text>
            <text x="170.0" y="75.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">g0/0.20  192.168.20.1  dot1Q 20</text>
            <rect x="95" y="140" width="150" height="50" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="170.0" y="160.5" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">deck-sw</text>
            <text x="170.0" y="177.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">trunk Gi0/8</text>
            <line x1="170" y1="90" x2="170" y2="140" stroke="#7c5cff" stroke-width="5" stroke-linecap="round"/>
            <text x="225" y="120" text-anchor="middle" font-size="11.5" font-weight="600" fill="#4a35b8">tagged frames, every VLAN</text>
            <rect x="10" y="220" width="95" height="50" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="57.5" y="240.2" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">astro</text>
            <text x="57.5" y="256.2" text-anchor="middle" font-size="11" font-weight="500" fill="#14306b">VLAN 10</text>
            <rect x="120" y="220" width="95" height="50" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="167.5" y="240.2" text-anchor="middle" font-size="13.5" font-weight="700" fill="#6b3a00">rivet</text>
            <text x="167.5" y="256.2" text-anchor="middle" font-size="11" font-weight="500" fill="#6b3a00">VLAN 20</text>
            <rect x="230" y="220" width="95" height="50" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="277.5" y="240.2" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14663f">guest</text>
            <text x="277.5" y="256.2" text-anchor="middle" font-size="11" font-weight="500" fill="#14663f">VLAN 30</text>
            <line x1="57" y1="220" x2="140" y2="190" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="167" y1="220" x2="170" y2="190" stroke="#f08c1a" stroke-width="3" stroke-linecap="round"/>
            <line x1="277" y1="220" x2="200" y2="190" stroke="#2fa56b" stroke-width="3" stroke-linecap="round"/>
            <rect x="370" y="20" width="280" height="250" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="510.0" y="85.9" text-anchor="middle" font-size="14" font-weight="700" fill="#3a4352">Layer 3 switch</text>
            <text x="510.0" y="102.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">ip routing</text>
            <text x="510.0" y="118.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">interface vlan 10</text>
            <text x="510.0" y="135.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352"> ip address 192.168.10.1 ...</text>
            <text x="510.0" y="151.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">interface vlan 20</text>
            <text x="510.0" y="168.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352"> ip address 192.168.20.1 ...</text>
            <text x="510.0" y="184.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352"></text>
            <text x="510.0" y="201.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">routes in hardware: faster,</text>
            <text x="510.0" y="217.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">no single cable bottleneck</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what does a Layer 3 switch use for each VLAN?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. An SVI ✓</li>
            <li class="quiz-option">B. A trunk</li>
            <li class="quiz-option">C. A hub port</li>
            <li class="quiz-option">D. A sub-cable</li>
        </ul>
        <p class="quiz-explanation"><em>A Layer 3 switch gives each VLAN a virtual interface (an SVI, like interface vlan 10) and routes inside the switch.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does <code>encapsulation dot1Q 20</code> on sub-interface g0/0.20 do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Gives it an IP address</li>
            <li class="quiz-option quiz-correct">B. Makes it handle frames tagged with VLAN 20 ✓</li>
            <li class="quiz-option">C. Turns the port on</li>
            <li class="quiz-option">D. Creates VLAN 20 on the switch</li>
        </ul>
        <p class="quiz-explanation"><em>It ties the sub-interface to VLAN 20: frames tagged 20 belong to it, and it tags what it sends with 20.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. The router side is right, but no VLAN works. What must the switch port to the router be?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. An access port in VLAN 1</li>
            <li class="quiz-option quiz-correct">B. A trunk ✓</li>
            <li class="quiz-option">C. Shut down</li>
            <li class="quiz-option">D. PortFast</li>
        </ul>
        <p class="quiz-explanation"><em>Router-on-a-stick needs tagged frames for every VLAN on that one cable, so the switch port must be a trunk.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is the main weakness of router-on-a-stick?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It can't route</li>
            <li class="quiz-option quiz-correct">B. Every routed packet crosses the one cable twice, so it is a bottleneck ✓</li>
            <li class="quiz-option">C. It needs one port per VLAN</li>
            <li class="quiz-option">D. It only works with VLAN 1</li>
        </ul>
        <p class="quiz-explanation"><em>Traffic between VLANs goes up and back down the same link, which limits throughput.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. On a Layer 3 switch, what is an SVI?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A trunk port</li>
            <li class="quiz-option quiz-correct">B. A virtual interface for a VLAN (interface vlan 10) that acts as its gateway ✓</li>
            <li class="quiz-option">C. A type of cable</li>
            <li class="quiz-option">D. A spanning-tree role</li>
        </ul>
        <p class="quiz-explanation"><em>A switched virtual interface gives the VLAN a routed interface inside the switch.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What should a PC in VLAN 20 use as its default gateway with router-on-a-stick?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The router's physical port address</li>
            <li class="quiz-option quiz-correct">B. The IP address on sub-interface g0/0.20 ✓</li>
            <li class="quiz-option">C. Any address in VLAN 10</li>
            <li class="quiz-option">D. The switch's MAC address</li>
        </ul>
        <p class="quiz-explanation"><em>Each VLAN's gateway is the address on its own sub-interface.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> trunk the deck switch to the router, then give the router one sub-interface per VLAN. <strong>Success:</strong> crew, workshop and guest PCs reach each other through one cable.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g4-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm4', 'lesson' => 'lesson02']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
