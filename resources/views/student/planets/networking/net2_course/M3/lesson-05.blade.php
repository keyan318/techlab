{{-- M3 · Lesson 3.5: Midterm Mission: The Northwatch Storm — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g3-l5.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="05">

    <h1 class="lesson-heading">Lesson 3.5: Midterm Mission: The Northwatch Storm</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this mission, you can troubleshoot a network layer by layer, combining VLANs, OSPF areas and HSRP, and prove it survives a failure.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        This mission brings together everything since the start of the season. Nothing is new; everything is mixed. Work <strong>from the bottom up</strong>, like Networking 1 taught you, and test after each fix:
    </p>
    <ul class="body-list">
        <li><strong>Layer 2</strong>: <code>show vlan brief</code> on the switch. Every PC port and every router port must be in the VLAN for that subnet.</li>
        <li><strong>Routing</strong>: <code>show ip ospf neighbor</code>. A missing neighbor means a shut port, a wrong subnet, a passive interface or an area mismatch. <code>show ip ospf interface brief</code> on both ends tells you which.</li>
        <li><strong>Gateway</strong>: <code>show standby brief</code> on both routers. They must agree on the group number and the virtual IP.</li>
        <li><strong>Resilience</strong>: when it all works, ask what happens if one router fails.</li>
    </ul>
    <p class="body-text">
        Don't change things at random. Read the output, form a theory, make <em>one</em> change, test again. If a change doesn't help, undo it.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">A change doesn't help. What should you do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Keep it and add more</li>
            <li class="quiz-option quiz-correct">B. Undo it ✓</li>
            <li class="quiz-option">C. Reboot every router</li>
            <li class="quiz-option">D. Change it at random</li>
        </ul>
        <p class="quiz-explanation"><em>Read the output, form a theory, make one change, test. If it doesn't help, undo it.</em></p>
    </div>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        A storm rolls over the ridge at Northwatch. Lightning hits the mast, the outpost reboots, and when the gear comes back, half of it has the wrong settings: someone restored an old backup in the dark. <strong>Volt</strong> watches the shield meter. "Northwatch can't see the Grid. If Doom notices, that's our blind spot."
    </p>
    <p class="body-text">
        <strong>Astro</strong> hands you the console. "Midterm, Cadet. Two routers, one switch, three problems at least. Bottom up. When Northwatch's crew can reach the Citadel, and keep reaching it if a router dies, the storm is over."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What caused the Northwatch gear to have wrong settings?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. An old backup was restored in the dark ✓</li>
            <li class="quiz-option">B. Doom hackers rewrote every router</li>
            <li class="quiz-option">C. The routers were never configured</li>
            <li class="quiz-option">D. Lightning erased all of the switch's VLANs</li>
        </ul>
        <p class="quiz-explanation"><em>After the reboot, someone restored an old backup in the dark, so half the gear came back wrong.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="Midterm checklist: Layer 2 VLANs, then OSPF routing, then the HSRP gateway, tested from the bottom up">
            <rect x="10" y="15" width="640" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="330.0" y="40.1" text-anchor="middle" font-size="14.5" font-weight="700" fill="#14306b">1. Layer 2: are the ports in the right VLAN?</text>
            <text x="330.0" y="57.1" text-anchor="middle" font-size="12" font-weight="500" fill="#14306b">show vlan brief  ·  switchport access vlan 30</text>
            <rect x="10" y="90" width="640" height="60" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="330.0" y="115.1" text-anchor="middle" font-size="14.5" font-weight="700" fill="#3b2a8a">2. Routing: are the OSPF neighbors up, in the right area?</text>
            <text x="330.0" y="132.1" text-anchor="middle" font-size="12" font-weight="500" fill="#3b2a8a">show ip ospf neighbor  ·  show ip ospf interface brief</text>
            <rect x="10" y="165" width="640" height="60" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="330.0" y="190.1" text-anchor="middle" font-size="14.5" font-weight="700" fill="#6b3a00">3. Gateway: one virtual IP, the same on both routers?</text>
            <text x="330.0" y="207.1" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">show standby brief  ·  standby 30 ip 192.168.30.1</text>
            <rect x="10" y="240" width="640" height="50" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="330.0" y="270.1" text-anchor="middle" font-size="14.5" font-weight="700" fill="#14663f">4. Test end to end, then test with a failure</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the checklist, what is step 1?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. OSPF routing</li>
            <li class="quiz-option">B. HSRP gateway</li>
            <li class="quiz-option">C. A failure test</li>
            <li class="quiz-option quiz-correct">D. Layer 2 VLANs ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Work from the bottom up: check the ports are in the right VLAN first, then routing, then the gateway.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Volt's PC is in VLAN 1 but the Northwatch subnet lives in VLAN 30. What is the symptom?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It works normally</li>
            <li class="quiz-option quiz-correct">B. Volt can't even reach the gateway, because it is in a different broadcast domain ✓</li>
            <li class="quiz-option">C. Only DNS fails</li>
            <li class="quiz-option">D. OSPF goes down</li>
        </ul>
        <p class="quiz-explanation"><em>A port in the wrong VLAN puts the PC in a different Layer 2 network from its gateway, so even ARP for the gateway fails.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Two OSPF routers on the same link never become neighbors; one says area 0, the other area 1. Fix?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Raise the cost</li>
            <li class="quiz-option quiz-correct">B. Put both ends of the link in the same area ✓</li>
            <li class="quiz-option">C. Add a static route</li>
            <li class="quiz-option">D. Reboot</li>
        </ul>
        <p class="quiz-explanation"><em>Both ends of a link must agree on the area. Remove the wrong network statement and add the right one.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. nw-a and nw-b are both "Active" for HSRP group 30, each with a different virtual IP. Why?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. That is normal</li>
            <li class="quiz-option quiz-correct">B. Their virtual IPs differ, so they are two separate groups that never see each other ✓</li>
            <li class="quiz-option">C. Priority is too high</li>
            <li class="quiz-option">D. Preempt is missing</li>
        </ul>
        <p class="quiz-explanation"><em>Each router thinks it is alone. The group number and the virtual IP must match on both routers.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What is the right order to troubleshoot?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Gateway, routing, Layer 2</li>
            <li class="quiz-option quiz-correct">B. Bottom up: Layer 2, then routing, then services and gateways ✓</li>
            <li class="quiz-option">C. Random</li>
            <li class="quiz-option">D. Restart everything first</li>
        </ul>
        <p class="quiz-explanation"><em>A Layer 2 problem hides everything above it, so check the bottom first.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Everything pings. What still has to be checked before the job is done?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing</li>
            <li class="quiz-option quiz-correct">B. That traffic still flows if one router fails ✓</li>
            <li class="quiz-option">C. The cable colors</li>
            <li class="quiz-option">D. The hostname</li>
        </ul>
        <p class="quiz-explanation"><em>A redundant design that hasn't been tested may not fail over. Prove it.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> after the storm, fix Northwatch's VLANs, OSPF and HSRP. <strong>Success:</strong> volt reaches the Citadel server, and still does if either Northwatch router fails.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g3-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm3', 'lesson' => 'lesson05']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
