{{-- M3 · Lesson 3.4: First Hop Redundancy: HSRP, VRRP & GLBP — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g3-l4.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="04">

    <h1 class="lesson-heading">Lesson 3.4: First Hop Redundancy: HSRP, VRRP & GLBP</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain why PCs need a redundant default gateway, configure HSRP with priority and preemption, and compare HSRP, VRRP and GLBP.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A PC has exactly one default gateway address, typed in or handed out by DHCP. Give it two routers and it still only knows one address. If that router dies, the PC is cut off, even though a perfectly good second router sits right next to it. <strong>First Hop Redundancy Protocols</strong> (FHRPs) fix that.
    </p>
    <p class="body-text">
        The routers share a <strong>virtual IP address</strong> (and a virtual MAC). PCs use the virtual address as their gateway. One router is <strong>active</strong> and answers for it; the other is <strong>standby</strong>, listening to the active one's hellos. If the hellos stop, the standby takes over the virtual address, and the PCs never notice.
    </p>
    <ul class="body-list">
        <li><strong>HSRP</strong> (Cisco): active and standby. The highest <strong>priority</strong> (default 100) wins. A router that comes back with a higher priority only takes over again if it has <strong>preempt</strong> set.</li>
        <li><strong>VRRP</strong> (open standard): the same idea, with master and backup. Preemption is on by default, and the virtual IP can even be one router's real address.</li>
        <li><strong>GLBP</strong> (Cisco): one virtual IP, but each router answers ARP with its own virtual MAC, so different PCs use different routers: load balancing as well as redundancy.</li>
    </ul>
    <p class="body-text">
        One more trap: if the active router loses its <em>uplink</em>, its LAN port is still up, so it stays active and drops the traffic. <strong>Interface tracking</strong> fixes that: <code>standby 10 track g0/0 20</code> lowers its priority by 20 while g0/0 is down, and a standby with <code>preempt</code> takes over.
    </p>
    <pre class="code-block"><code>interface g0/1
 standby 10 ip 192.168.60.1
 standby 10 priority 110
 standby 10 preempt
 standby 10 track g0/0 20</code></pre>
    <p class="body-text">
        <code>show standby brief</code> shows who is active, who is standby, the current priority and the virtual IP. Configure the same group number and virtual IP on both routers; the priority and tracking differ.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does interface tracking fix in HSRP?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. An active router that lost its uplink ✓</li>
            <li class="quiz-option">B. Two routers with the same priority</li>
            <li class="quiz-option">C. A PC with the wrong default gateway address</li>
            <li class="quiz-option">D. A standby router with no LAN cable</li>
        </ul>
        <p class="quiz-explanation"><em>If the active router loses its uplink, its LAN port is still up. Tracking lowers its priority so the standby takes over.</em></p>
    </div>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        The hangar has two distribution routers now, but every PC points at dist-a's real address. "Last night dist-a rebooted for an update," says <strong>Rivet</strong>, "and the whole hangar went dark for five minutes, with dist-b sitting right there doing nothing."
    </p>
    <p class="body-text">
        <strong>Astro</strong> already has the fix. "The hangar's DHCP now hands out 192.168.60.1 as the gateway. Nobody owns that address yet. Make it a virtual gateway that both routers share."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why did the hangar go dark when dist-a rebooted?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. dist-b had no power at all during the update</li>
            <li class="quiz-option">B. The DHCP server handed out no leases</li>
            <li class="quiz-option quiz-correct">C. Every PC pointed at dist-a's real address ✓</li>
            <li class="quiz-option">D. The core router was rebooting as well</li>
        </ul>
        <p class="quiz-explanation"><em>PCs know only one gateway. With dist-a's real address, they were cut off even though dist-b was right there.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 290" role="img" aria-label="Two routers share virtual gateway 192.168.60.1 with HSRP: dist-a active, dist-b standby, PCs always use the virtual address">
            <rect x="20" y="20" width="280" height="70" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="160.0" y="42.4" text-anchor="middle" font-size="14" font-weight="700" fill="#14663f">dist-a  (priority 110)</text>
            <text x="160.0" y="58.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">real 192.168.60.2</text>
            <text x="160.0" y="75.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">ACTIVE: answers for .1</text>
            <rect x="360" y="20" width="280" height="70" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="500.0" y="42.4" text-anchor="middle" font-size="14" font-weight="700" fill="#6b3a00">dist-b  (priority 100)</text>
            <text x="500.0" y="58.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">real 192.168.60.3</text>
            <text x="500.0" y="75.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">STANDBY: ready to take over</text>
            <rect x="200" y="125" width="260" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="330.0" y="148.2" text-anchor="middle" font-size="14" font-weight="700" fill="#3b2a8a">virtual gateway 192.168.60.1</text>
            <text x="330.0" y="164.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">MAC 0000.0c07.ac0a (HSRP group 10)</text>
            <line x1="160" y1="90" x2="280" y2="125" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="500" y1="90" x2="380" y2="125" stroke="#f08c1a" stroke-width="3" stroke-linecap="round" stroke-dasharray="7 6"/>
            <rect x="110" y="215" width="200" height="56" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="210.0" y="238.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">pc1</text>
            <text x="210.0" y="255.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">gateway 192.168.60.1</text>
            <rect x="350" y="215" width="200" height="56" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="450.0" y="238.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">pc2</text>
            <text x="450.0" y="255.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">gateway 192.168.60.1</text>
            <line x1="210" y1="215" x2="300" y2="181" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="450" y1="215" x2="360" y2="181" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, which router is active?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. dist-a ✓</li>
            <li class="quiz-option">B. dist-b</li>
            <li class="quiz-option">C. pc1</li>
            <li class="quiz-option">D. pc2</li>
        </ul>
        <p class="quiz-explanation"><em>dist-a has priority 110, higher than dist-b's 100, so it is active and answers for 192.168.60.1.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What problem do first hop redundancy protocols solve?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Slow DNS</li>
            <li class="quiz-option quiz-correct">B. A PC only knows one gateway address, so a failed gateway cuts it off even if another router exists ✓</li>
            <li class="quiz-option">C. Too many VLANs</li>
            <li class="quiz-option">D. Routing loops in OSPF</li>
        </ul>
        <p class="quiz-explanation"><em>FHRPs let several routers share one gateway address, so PCs keep working when one router fails.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. In HSRP, which router becomes active?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The one with the lowest IP</li>
            <li class="quiz-option quiz-correct">B. The one with the highest priority (then highest IP) ✓</li>
            <li class="quiz-option">C. The one configured first</li>
            <li class="quiz-option">D. Both at once</li>
        </ul>
        <p class="quiz-explanation"><em>Highest priority wins, with the highest interface IP as the tie-breaker. The default priority is 100.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. dist-a (priority 110) reboots, dist-b takes over, then dist-a comes back. dist-a has no preempt. What happens?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. dist-a takes over at once</li>
            <li class="quiz-option quiz-correct">B. dist-b stays active ✓</li>
            <li class="quiz-option">C. Both become active</li>
            <li class="quiz-option">D. The hangar goes offline</li>
        </ul>
        <p class="quiz-explanation"><em>Without preempt, a recovered router does not take the active role back, even with a higher priority.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Which FHRP is an open standard rather than Cisco-only?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. HSRP</li>
            <li class="quiz-option">B. GLBP</li>
            <li class="quiz-option quiz-correct">C. VRRP ✓</li>
            <li class="quiz-option">D. EIGRP</li>
        </ul>
        <p class="quiz-explanation"><em>VRRP is defined by the IETF (RFC 5798) and runs on many vendors' routers.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does GLBP add over HSRP?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Encryption</li>
            <li class="quiz-option quiz-correct">B. Load balancing: different PCs are sent to different routers ✓</li>
            <li class="quiz-option">C. IPv6 only</li>
            <li class="quiz-option">D. Nothing</li>
        </ul>
        <p class="quiz-explanation"><em>GLBP hands out different virtual MACs for the same virtual IP, so all routers forward traffic at the same time.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> give the hangar a virtual gateway with HSRP, make dist-a the preferred router, and make it hand over when it loses its uplink. <strong>Success:</strong> the PCs reach the Grid server through 192.168.60.1 whatever fails.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g3-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm3', 'lesson' => 'lesson04']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
