{{-- M3 · Lesson 3.3: Redundant Paths & Convergence — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g3-l3.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="03">

    <h1 class="lesson-heading">Lesson 3.3: Redundant Paths & Convergence</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can build redundant Layer 3 paths, explain convergence, and prove a network survives a link failure.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A redundant path is only useful if the network <em>notices</em> a failure and moves traffic onto it. The time that takes is <strong>convergence</strong>: every router learns of the change, recalculates, and agrees on the new paths. Until then, some traffic is lost.
    </p>
    <ul class="body-list">
        <li><strong>Detect</strong>: a link going physically down is noticed at once. A link that stays up but stops passing traffic is only noticed when hellos stop (OSPF's dead timer, 40 seconds by default). <strong>BFD</strong> (Bidirectional Forwarding Detection) can detect it in milliseconds.</li>
        <li><strong>Tell</strong>: OSPF floods the new LSA; EIGRP sends queries (unless a feasible successor is ready).</li>
        <li><strong>Recalculate</strong>: SPF runs, the routing table updates.</li>
    </ul>
    <p class="body-text">
        Redundant paths come at different layers:
    </p>
    <ul class="body-list">
        <li><strong>Layer 3</strong>: a ring or mesh of routed links. Routing protocols choose and re-choose paths. With equal costs, both paths carry traffic (<strong>ECMP</strong>), so the spare isn't wasted.</li>
        <li><strong>Layer 2</strong>: extra links between switches. Spanning tree blocks the extras to stop loops and unblocks one when a link fails (Module 4). <strong>EtherChannel</strong> bundles parallel links into one logical link, so all of them carry traffic and losing one costs only bandwidth.</li>
        <li><strong>First hop</strong>: two gateway routers sharing one virtual address (next lesson).</li>
    </ul>
    <p class="body-text">
        A ring is the cheapest Layer 3 design with no single link failure that cuts anyone off: every node has two ways out. Always <strong>test</strong> failover: a backup nobody tested is a hope, not a design.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What can detect a dead link in milliseconds?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The OSPF dead timer</li>
            <li class="quiz-option">B. ECMP</li>
            <li class="quiz-option">C. EtherChannel</li>
            <li class="quiz-option quiz-correct">D. BFD ✓</li>
        </ul>
        <p class="quiz-explanation"><em>OSPF's dead timer waits 40 seconds by default. BFD (Bidirectional Forwarding Detection) can notice in milliseconds.</em></p>
    </div>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        Four outposts, four links, and Skyport hangs off just one of them. "If the Citadel–Skyport cable goes, Skyport is an island," says <strong>Astro</strong>. "Northwatch has a cable to Skyport sitting idle. Close the ring."
    </p>
    <p class="body-text">
        <strong>Volt</strong> adds the important part. "And then prove it. Pretend Doom cut a cable and see if the traffic finds its way round. Don't actually leave a hole in our network to test it."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">How does Volt want to test the new ring?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Cut a real cable and leave it out for a whole night</li>
            <li class="quiz-option">B. Wait for Doom to cut a cable on their own</li>
            <li class="quiz-option">C. Skip the test since a ring always works</li>
            <li class="quiz-option quiz-correct">D. Fake a cable cut, without leaving a hole ✓</li>
        </ul>
        <p class="quiz-explanation"><em>A backup nobody tested is a hope, not a design. Simulate a cut, then check traffic finds its way around.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 290" role="img" aria-label="Four outposts in a ring: two equal paths from the Landing Site to Skyport, and a failure re-routed around the ring">
            <rect x="20" y="110" width="130" height="54" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="85.0" y="132.5" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">landing</text>
            <text x="85.0" y="149.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">astro&#x27;s LAN</text>
            <rect x="265" y="15" width="130" height="54" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="330.0" y="47.2" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">citadel</text>
            <rect x="265" y="205" width="130" height="54" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="330.0" y="236.9" text-anchor="middle" font-size="14" font-weight="700" fill="#3b2a8a">northwatch</text>
            <rect x="510" y="110" width="130" height="54" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="575.0" y="132.5" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">skyport</text>
            <text x="575.0" y="149.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">rivet&#x27;s LAN</text>
            <line x1="150" y1="125" x2="265" y2="45" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="395" y1="45" x2="510" y2="125" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="150" y1="150" x2="265" y2="230" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="395" y1="230" x2="510" y2="150" stroke="#2fa56b" stroke-width="3" stroke-linecap="round" stroke-dasharray="7 6"/>
            <text x="470" y="215" text-anchor="middle" font-size="11.5" font-weight="600" fill="#14663f">the missing link</text>
            <line x1="196" y1="76" x2="214" y2="94" stroke="#e5484d" stroke-width="4" stroke-linecap="round"/>
            <line x1="196" y1="94" x2="214" y2="76" stroke="#e5484d" stroke-width="4" stroke-linecap="round"/>
            <text x="330" y="140" text-anchor="middle" font-size="12.5" font-weight="600" fill="#3b2a8a">cost 2 either way = load shared;</text>
            <text x="330" y="158" text-anchor="middle" font-size="12.5" font-weight="600" fill="#3b2a8a">one link cut = the other path, in seconds</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what happens when both paths to Skyport cost 2?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. One path is blocked</li>
            <li class="quiz-option">B. The ring stops working</li>
            <li class="quiz-option quiz-correct">C. The load is shared ✓</li>
            <li class="quiz-option">D. Skyport is an island</li>
        </ul>
        <p class="quiz-explanation"><em>With equal costs, both paths carry traffic (ECMP), so the spare isn't wasted. If one is cut, the other takes over.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is convergence?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Two cables joined together</li>
            <li class="quiz-option quiz-correct">B. The time and process for all routers to learn of a change and agree on new paths ✓</li>
            <li class="quiz-option">C. A type of VLAN</li>
            <li class="quiz-option">D. Encryption between routers</li>
        </ul>
        <p class="quiz-explanation"><em>Convergence is complete when every router has recalculated after a change and the network is forwarding on the new paths.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. An OSPF link stays physically up but stops passing traffic. What detects it, and how fast by default?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing ever does</li>
            <li class="quiz-option quiz-correct">B. The dead timer, 40 seconds by default ✓</li>
            <li class="quiz-option">C. The router reboots at once</li>
            <li class="quiz-option">D. DHCP</li>
        </ul>
        <p class="quiz-explanation"><em>Without a physical down event, OSPF only notices when hellos stop arriving for the dead interval. BFD can shorten that to milliseconds.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Two paths to Skyport both cost 2. What does OSPF do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Uses one and keeps the other idle</li>
            <li class="quiz-option quiz-correct">B. Installs both and shares the traffic (ECMP) ✓</li>
            <li class="quiz-option">C. Drops both</li>
            <li class="quiz-option">D. Picks the newest</li>
        </ul>
        <p class="quiz-explanation"><em>Equal-cost multipath puts both in the routing table, so redundancy also adds capacity.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does EtherChannel do with four parallel links between two switches?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Blocks three of them</li>
            <li class="quiz-option quiz-correct">B. Bundles them into one logical link that uses all four ✓</li>
            <li class="quiz-option">C. Turns them into VLANs</li>
            <li class="quiz-option">D. Routes over them with OSPF</li>
        </ul>
        <p class="quiz-explanation"><em>EtherChannel makes parallel links one logical link: all carry traffic, and losing one only reduces bandwidth.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why connect four outposts in a ring rather than a chain?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Rings are cheaper</li>
            <li class="quiz-option quiz-correct">B. In a ring every outpost has two ways out, so one cut link isolates nobody ✓</li>
            <li class="quiz-option">C. Chains are not allowed</li>
            <li class="quiz-option">D. Rings need no routing</li>
        </ul>
        <p class="quiz-explanation"><em>A chain is cut in two by any single failure; a ring survives any one link failure.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> close the ring between Northwatch and Skyport, then prove the Grid survives losing a link. <strong>Success:</strong> the Landing Site has two paths to Skyport, and astro still reaches rivet with either first-hop link cut.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g3-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm3', 'lesson' => 'lesson03']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
