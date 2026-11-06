{{-- M5 · Lesson 5.3: MPLS & Traffic Engineering — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g5-l3.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="03">

    <h1 class="lesson-heading">Lesson 5.3: MPLS & Traffic Engineering</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain how MPLS label switching works, read a label forwarding table, configure a VRF for an MPLS VPN customer, and say what traffic engineering adds.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>MPLS</strong> (Multiprotocol Label Switching) is how big providers like Earth Relay build their backbones. Instead of every router looking up the destination IP, the <em>edge</em> router decides the path once and sticks a short <strong>label</strong> on the packet. Routers in the core just look at the label.
    </p>
    <ul class="body-list">
        <li><strong>LER / PE</strong> (provider edge): <strong>pushes</strong> a label onto packets entering the MPLS network, and handles plain IP leaving it.</li>
        <li><strong>LSR / P</strong> (provider core): <strong>swaps</strong> the incoming label for an outgoing one and forwards.</li>
        <li>The second-to-last router usually <strong>pops</strong> the label (penultimate hop popping), so the last router gets a plain IP packet.</li>
        <li>The path a labelled packet follows is an <strong>LSP</strong> (label switched path). Routers agree on labels with <strong>LDP</strong>.</li>
    </ul>
    <p class="body-text">
        <code>show mpls forwarding-table</code> (the LFIB) lists: the label that arrives (Local), the label that leaves (Outgoing, or "Pop Label"), the destination prefix, and the outgoing interface.
    </p>
    <p class="body-text">
        MPLS's killer feature is the <strong>MPLS L3 VPN</strong>: many customers share one backbone, each in its own <strong>VRF</strong> (a separate routing table on the PE router). Customers can even use the same private addresses. A <strong>route distinguisher</strong> (RD) makes each customer's routes unique inside the provider; <strong>route targets</strong> (RT) say which VRFs share routes. One gotcha: putting an interface into a VRF erases its IP address, so re-enter it.
    </p>
    <pre class="code-block"><code>ip vrf GRID
 rd 64500:65010
 route-target both 64500:65010
interface g0/3
 ip vrf forwarding GRID
 ip address 172.20.0.1 255.255.255.252</code></pre>
    <p class="body-text">
        <strong>Traffic engineering</strong> (MPLS TE, with RSVP-TE) lets the provider send traffic along a path it chooses, not only the shortest one: around a congested or storm-damaged link, for example.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does a core (P) router do with a label?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Pushes it onto plain IP packets</li>
            <li class="quiz-option">B. Looks up the full destination IP</li>
            <li class="quiz-option quiz-correct">C. Swaps it for an outgoing label ✓</li>
            <li class="quiz-option">D. Erases the IP address of the VRF</li>
        </ul>
        <p class="quiz-explanation"><em>The PE pushes a label at the edge. Core routers just swap the incoming label for an outgoing one.</em></p>
    </div>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        Earth Relay's engineers invite the Grid onto their backbone: an MPLS VPN, so the Grid's outposts on Codexia and its servers on Earth can share one private network. <strong>Rivet</strong> gets a login to EARTH-P1, a provider router. "Moonbase is on the same backbone with the same private addresses as us. And it works, somehow."
    </p>
    <p class="body-text">
        "Labels and VRFs," says <strong>Astro</strong>. "Earth's routers never mix up the two customers. Learn to read the labels, then set up our VRF."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">How can Moonbase and the Grid use the same private addresses?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Earth's routers drop Moonbase's traffic</li>
            <li class="quiz-option">B. The Grid renumbers all of its outposts</li>
            <li class="quiz-option quiz-correct">C. Each customer has its own VRF ✓</li>
            <li class="quiz-option">D. Moonbase uses only IPv6 addresses now</li>
        </ul>
        <p class="quiz-explanation"><em>Each customer gets its own VRF (routing table) on the PE, and the RD keeps the overlapping routes unique.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="An MPLS label switched path: the ingress edge router pushes a label, core routers swap it, the next-to-last router pops it">
            <rect x="10" y="30" width="110" height="56" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="65.0" y="53.8" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Grid CE</text>
            <text x="65.0" y="69.8" text-anchor="middle" font-size="11" font-weight="500" fill="#14306b">customer</text>
            <rect x="150" y="30" width="110" height="56" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="205.0" y="53.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">PE1</text>
            <text x="205.0" y="70.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">push 17</text>
            <rect x="290" y="30" width="110" height="56" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="345.0" y="53.5" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">P1</text>
            <text x="345.0" y="70.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">swap 17→22</text>
            <rect x="430" y="30" width="110" height="56" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="485.0" y="53.5" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">P2</text>
            <text x="485.0" y="70.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">pop (PHP)</text>
            <rect x="570" y="30" width="80" height="56" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="610.0" y="53.2" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14663f">PE2</text>
            <text x="610.0" y="69.2" text-anchor="middle" font-size="11" font-weight="500" fill="#14663f">plain IP</text>
            <line x1="120" y1="58" x2="141.0" y2="58.0" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="150,58 141.0,64.0 141.0,52.0" fill="#7c5cff"/>
            <line x1="260" y1="58" x2="281.0" y2="58.0" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="290,58 281.0,64.0 281.0,52.0" fill="#7c5cff"/>
            <line x1="400" y1="58" x2="421.0" y2="58.0" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="430,58 421.0,64.0 421.0,52.0" fill="#7c5cff"/>
            <line x1="540" y1="58" x2="561.0" y2="58.0" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="570,58 561.0,64.0 561.0,52.0" fill="#7c5cff"/>
            <text x="330" y="110" text-anchor="middle" font-size="12.5" font-weight="600" fill="#3b2a8a">the LSP: decided once at the edge, label lookups after that</text>
            <rect x="10" y="135" width="310" height="150" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="165.0" y="174.2" text-anchor="middle" font-size="14" font-weight="700" fill="#3b2a8a">MPLS L3 VPN</text>
            <text x="165.0" y="190.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">each customer gets a VRF:</text>
            <text x="165.0" y="207.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">its own routing table on the PE</text>
            <text x="165.0" y="223.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">RD makes overlapping addresses</text>
            <text x="165.0" y="240.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">unique; RT says which VRFs</text>
            <text x="165.0" y="256.6" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">import each other&#x27;s routes</text>
            <rect x="340" y="135" width="310" height="150" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="495.0" y="181.9" text-anchor="middle" font-size="14" font-weight="700" fill="#3a4352">Traffic engineering</text>
            <text x="495.0" y="198.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">RSVP-TE tunnels follow a path</text>
            <text x="495.0" y="214.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">you choose, not just the</text>
            <text x="495.0" y="231.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">shortest one: steer traffic</text>
            <text x="495.0" y="247.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">around a congested link</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what does P2 do with the label?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Pushes label 17</li>
            <li class="quiz-option">B. Swaps 17 for 22</li>
            <li class="quiz-option">C. Adds a VRF tag</li>
            <li class="quiz-option quiz-correct">D. Pops it (PHP) ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The next-to-last router pops the label (penultimate hop popping), so PE2 gets a plain IP packet.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does a core (P) router do with an MPLS packet?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Looks up the destination IP</li>
            <li class="quiz-option quiz-correct">B. Swaps the incoming label for an outgoing one and forwards it ✓</li>
            <li class="quiz-option">C. Removes the IP header</li>
            <li class="quiz-option">D. Encrypts it</li>
        </ul>
        <p class="quiz-explanation"><em>Core label switch routers only swap labels, using the label forwarding table.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In the LFIB, the outgoing label is "Pop Label". What does that router do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Drops the packet</li>
            <li class="quiz-option quiz-correct">B. Removes the label and sends the packet on (penultimate hop popping) ✓</li>
            <li class="quiz-option">C. Adds two labels</li>
            <li class="quiz-option">D. Sends it to the CPU</li>
        </ul>
        <p class="quiz-explanation"><em>The next-to-last router pops the label so the egress router receives a plain IP packet.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Two customers use 10.0.0.0/8 on the same provider backbone. What keeps their routes apart?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. NAT</li>
            <li class="quiz-option quiz-correct">B. Separate VRFs, with route distinguishers making the routes unique ✓</li>
            <li class="quiz-option">C. Different cables</li>
            <li class="quiz-option">D. OSPF areas</li>
        </ul>
        <p class="quiz-explanation"><em>Each customer has its own VRF (routing table); the RD keeps overlapping prefixes unique inside the provider's BGP.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What do route targets control?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Interface speed</li>
            <li class="quiz-option quiz-correct">B. Which VRFs import each other's routes ✓</li>
            <li class="quiz-option">C. Label values</li>
            <li class="quiz-option">D. QoS markings</li>
        </ul>
        <p class="quiz-explanation"><em>Route targets are tags on VPN routes; a VRF imports routes carrying the targets it is configured for.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does MPLS traffic engineering allow?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Faster CPUs</li>
            <li class="quiz-option quiz-correct">B. Sending traffic along a chosen path, not only the shortest, for example around congestion ✓</li>
            <li class="quiz-option">C. Bigger labels</li>
            <li class="quiz-option">D. Free bandwidth</li>
        </ul>
        <p class="quiz-explanation"><em>TE tunnels follow explicit or constraint-based paths set up with RSVP-TE.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> read Earth's label table, set up the Grid's VRF on the provider edge, and find out what the TE tunnel avoids. <strong>Success:</strong> the Grid has its own VRF on Earth's backbone, with its routes inside it.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g5-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm5', 'lesson' => 'lesson03']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
