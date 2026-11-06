{{-- M2 · Lesson 2.3: OSPF Configuration & Optimization — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g2-l3.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="03">

    <h1 class="lesson-heading">Lesson 2.3: OSPF Configuration & Optimization</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can tune OSPF costs to steer traffic, use passive interfaces, and share a default route with the whole network.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        OSPF picks the path with the lowest total <strong>cost</strong>. By default, cost = <strong>reference bandwidth ÷ interface bandwidth</strong>, and the reference is only 100 Mbps. So a 100 Mbps link and a 10 Gbps link both cost 1: OSPF can't tell them apart. Two fixes:
    </p>
    <ul class="body-list">
        <li>Raise the reference on <em>every</em> router: <code>auto-cost reference-bandwidth 10000</code> (in Mbps), so faster links get lower costs.</li>
        <li>Or set a cost by hand on an interface: <code>ip ospf cost 50</code>. Set it on both ends, because each router counts the cost of its own outgoing interface.</li>
    </ul>
    <p class="body-text">
        Other everyday tuning:
    </p>
    <ul class="body-list">
        <li><strong>Passive interfaces</strong>: no hellos toward PCs, network still advertised. <code>passive-interface default</code> makes every port passive, then <code>no passive-interface g0/1</code> opens only the router links.</li>
        <li><strong>Router ID</strong>: each router has a unique 32-bit ID (<code>router-id 1.1.1.1</code>); otherwise it picks its highest interface address.</li>
        <li><strong>DR election</strong>: on a shared Ethernet segment one router becomes DR to cut down flooding. <code>ip ospf priority 0</code> keeps a router out of the election.</li>
        <li><strong>Default route</strong>: a router with a default route to the outside can share it with <code>default-information originate</code>. Every other router learns it as <strong>O*E2</strong>.</li>
        <li><strong>Equal costs</strong>: if two paths tie, OSPF installs both (up to four) and shares the traffic between them.</li>
    </ul>
    <pre class="code-block"><code>interface g0/2
 ip ospf cost 50
router ospf 1
 passive-interface g0/0
 default-information originate</code></pre>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Why do a 100 Mbps link and a 10 Gbps link cost the same by default?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. OSPF counts the cable length instead</li>
            <li class="quiz-option quiz-correct">B. The reference bandwidth is only 100 Mbps ✓</li>
            <li class="quiz-option">C. Every link has a fixed cost of 10 hops in OSPF</li>
            <li class="quiz-option">D. OSPF ignores links faster than 1 Gbps</li>
        </ul>
        <p class="quiz-explanation"><em>Cost = reference ÷ interface bandwidth, and the reference is 100 Mbps, so both come out as the minimum, 1.</em></p>
    </div>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        <strong>Volt</strong> is watching the traffic meter on the Landing Site's radio link to Northwatch. "Everything to Northwatch goes over the radio. It's slow, and Doom can jam it. We have fiber through the Citadel, two hops instead of one, and OSPF ignores it."
    </p>
    <p class="body-text">
        "Because OSPF counts hops of cost 1," says <strong>Astro</strong>. "Make the radio expensive and it will take the fiber. And while you're in there: only the Citadel has the link to Earth. Teach everyone else the way out."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">How does Astro get OSPF to use the fiber through the Citadel?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Make the radio link expensive ✓</li>
            <li class="quiz-option">B. Shut down the fiber link first</li>
            <li class="quiz-option">C. Lower the fiber's priority to 0</li>
            <li class="quiz-option">D. Delete the network statements</li>
        </ul>
        <p class="quiz-explanation"><em>Set a high cost on the radio (like 50). Then the two fiber hops (total 3) beat the radio path (51).</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 290" role="img" aria-label="A triangle of outposts: fiber links cost 1, the slow radio link costs 50, so OSPF routes over the two fiber links">
            <rect x="20" y="110" width="130" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="85.0" y="143.2" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">landing</text>
            <rect x="265" y="20" width="130" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="330.0" y="43.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">citadel</text>
            <text x="330.0" y="60.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">to Earth</text>
            <rect x="510" y="110" width="130" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="575.0" y="143.2" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">northwatch</text>
            <line x1="150" y1="120" x2="265" y2="60" stroke="#2fa56b" stroke-width="3" stroke-linecap="round"/>
            <line x1="395" y1="60" x2="510" y2="120" stroke="#2fa56b" stroke-width="3" stroke-linecap="round"/>
            <line x1="150" y1="150" x2="510" y2="150" stroke="#e5484d" stroke-width="3" stroke-linecap="round" stroke-dasharray="7 6"/>
            <text x="190" y="80" text-anchor="middle" font-size="11.5" font-weight="600" fill="#14663f">fiber cost 1</text>
            <text x="470" y="80" text-anchor="middle" font-size="11.5" font-weight="600" fill="#14663f">fiber cost 1</text>
            <text x="330" y="170" text-anchor="middle" font-size="11.5" font-weight="600" fill="#a3262b">radio: cost 50 after tuning</text>
            <rect x="20" y="200" width="300" height="80" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="170.0" y="226.7" text-anchor="middle" font-size="13.5" font-weight="700" fill="#6b3a00">cost = reference bandwidth</text>
            <text x="170.0" y="243.7" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">÷ interface bandwidth</text>
            <text x="170.0" y="260.7" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">(100 Mbps ÷ 1 Gbps → 1, the minimum)</text>
            <rect x="340" y="200" width="300" height="80" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="490.0" y="226.7" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14663f">landing → northwatch</text>
            <text x="490.0" y="243.7" text-anchor="middle" font-size="12" font-weight="500" fill="#14663f">radio: 50 + 1 = 51</text>
            <text x="490.0" y="260.7" text-anchor="middle" font-size="12" font-weight="500" fill="#14663f">via citadel: 1 + 1 + 1 = 3  ✔</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what is the cost from landing to northwatch via the citadel?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 1</li>
            <li class="quiz-option">B. 50</li>
            <li class="quiz-option">C. 51</li>
            <li class="quiz-option quiz-correct">D. 3 ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Via the citadel: 1 + 1 + 1 = 3. The radio path costs 50 + 1 = 51, so the fiber path wins.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. With the default 100 Mbps reference, what OSPF cost do a 1 Gbps and a 10 Gbps interface get?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 10 and 1</li>
            <li class="quiz-option quiz-correct">B. Both 1 ✓</li>
            <li class="quiz-option">C. 1 and 10</li>
            <li class="quiz-option">D. 100 and 1000</li>
        </ul>
        <p class="quiz-explanation"><em>100 Mbps ÷ 1 Gbps and 100 Mbps ÷ 10 Gbps are both below 1, and the minimum cost is 1. Raise the reference bandwidth to tell them apart.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. You set <code>ip ospf cost 50</code> on only one end of a link. What happens?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Both directions use cost 50</li>
            <li class="quiz-option quiz-correct">B. Only traffic leaving that router through that interface sees cost 50 ✓</li>
            <li class="quiz-option">C. The neighbor goes down</li>
            <li class="quiz-option">D. OSPF ignores it</li>
        </ul>
        <p class="quiz-explanation"><em>Each router adds the cost of its own outgoing interface, so set the cost on both ends to steer both directions.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which command shares a router's default route with all other OSPF routers?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>network 0.0.0.0 255.255.255.255 area 0</code></li>
            <li class="quiz-option quiz-correct">B. <code>default-information originate</code> ✓</li>
            <li class="quiz-option">C. <code>ip route 0.0.0.0 0.0.0.0 null0</code></li>
            <li class="quiz-option">D. <code>passive-interface default</code></li>
        </ul>
        <p class="quiz-explanation"><em><code>default-information originate</code> advertises the router's default route. Others see it as O*E2.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Two paths to a network have the same total cost. What does OSPF do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Picks one at random forever</li>
            <li class="quiz-option quiz-correct">B. Installs both and shares traffic between them ✓</li>
            <li class="quiz-option">C. Drops the network</li>
            <li class="quiz-option">D. Asks the administrator</li>
        </ul>
        <p class="quiz-explanation"><em>OSPF supports equal-cost multipath: it installs up to four equal paths and spreads traffic across them.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does <code>ip ospf priority 0</code> on an interface do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Makes it the DR</li>
            <li class="quiz-option quiz-correct">B. Keeps that router out of the DR/BDR election on that segment ✓</li>
            <li class="quiz-option">C. Turns OSPF off</li>
            <li class="quiz-option">D. Sets the cost to 0</li>
        </ul>
        <p class="quiz-explanation"><em>Priority 0 means the router never becomes DR or BDR on that segment.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> move Northwatch traffic off the slow radio, keep hellos off the LANs, and share the Citadel's way to Earth. <strong>Success:</strong> traffic between astro and volt goes through the Citadel, and astro opens Earth's relay page.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g2-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm2', 'lesson' => 'lesson03']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
