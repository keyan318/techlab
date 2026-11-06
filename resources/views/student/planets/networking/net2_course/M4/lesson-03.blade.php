{{-- M4 · Lesson 4.3: Spanning Tree: RSTP & MST — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g4-l3.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="03">

    <h1 class="lesson-heading">Lesson 4.3: Spanning Tree: RSTP & MST</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain how spanning tree stops loops, choose the root bridge, use Rapid PVST+, and share load between VLANs with different roots.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Redundant links between switches make loops. Ethernet frames have no time-to-live, so one broadcast would circle forever and multiply: a <strong>broadcast storm</strong> that brings the network down in seconds. <strong>Spanning Tree Protocol</strong> (STP) finds the loops and blocks just enough ports to break them, then unblocks one if a link fails.
    </p>
    <ul class="body-list">
        <li><strong>Root bridge</strong>: the switch with the lowest bridge ID (priority first, then MAC address). Left alone, that is usually the <em>oldest</em> switch, because it has the lowest MAC. Choose it yourself: <code>spanning-tree vlan 10 root primary</code> (priority 24576), and a backup with <code>root secondary</code> (28672). Priorities go in steps of 4096.</li>
        <li><strong>Root port</strong>: on every other switch, the port with the lowest cost to the root.</li>
        <li><strong>Designated port</strong>: on each link, the side closer to the root forwards.</li>
        <li><strong>Alternate port</strong>: everything else blocks, ready to take over.</li>
    </ul>
    <p class="body-text">
        The original STP (802.1D) took 30 to 50 seconds to react. <strong>RSTP</strong> (802.1w) does it in about a second, because alternate ports are pre-chosen. Cisco runs a separate spanning tree per VLAN: <strong>Rapid PVST+</strong>. That lets you make different switches root for different VLANs, so each uplink carries some VLANs instead of one sitting idle.
    </p>
    <pre class="code-block"><code>spanning-tree mode rapid-pvst
spanning-tree vlan 1,10 root primary
spanning-tree vlan 20 root secondary</code></pre>
    <p class="body-text">
        With hundreds of VLANs, one tree per VLAN is a lot of work. <strong>MST</strong> (802.1s) maps many VLANs onto a few <em>instances</em>, for example instance 1 = VLANs 10-19 and instance 2 = VLANs 20-29, each with its own root. All switches in an MST region must have the same mapping. <code>show spanning-tree</code> shows the root, and each port's role and state.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">How is the root bridge chosen?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Highest priority, then highest MAC</li>
            <li class="quiz-option">B. The newest switch that joins the tree</li>
            <li class="quiz-option">C. The switch with the most VLAN ports</li>
            <li class="quiz-option quiz-correct">D. Lowest priority, then lowest MAC ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The switch with the lowest bridge ID wins: priority first, then MAC address.</em></p>
    </div>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        The Citadel's three switches are cabled in a triangle, for redundancy. <strong>Rivet</strong> runs <code>show spanning-tree</code> and frowns. "The root bridge is old-sw. The oldest, slowest switch in the building, chosen because it has the lowest MAC address. Every frame between floors takes a detour through it."
    </p>
    <p class="body-text">
        "And it's running the old protocol," <strong>Volt</strong> adds. "Fifty seconds to recover from a cut cable. Doom only needs ten."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why is old-sw a bad root bridge?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It has the highest priority of all</li>
            <li class="quiz-option">B. It runs Rapid PVST+ on every VLAN</li>
            <li class="quiz-option quiz-correct">C. It won only because of its low MAC ✓</li>
            <li class="quiz-option">D. It is the newest switch in the room</li>
        </ul>
        <p class="quiz-explanation"><em>Left alone, the oldest switch wins with the lowest MAC. Every frame between floors detours through it.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="Three switches in a loop: core-sw is root for VLAN 10 and dist-sw for VLAN 20, so old-sw blocks a different uplink per VLAN">
            <rect x="250" y="15" width="160" height="56" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="330.0" y="38.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">core-sw</text>
            <text x="330.0" y="55.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">root VLAN 1, 10</text>
            <rect x="30" y="200" width="160" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="110.0" y="223.5" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">old-sw</text>
            <text x="110.0" y="240.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">access</text>
            <rect x="470" y="200" width="160" height="56" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="550.0" y="223.5" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">dist-sw</text>
            <text x="550.0" y="240.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">root VLAN 20</text>
            <line x1="280" y1="71" x2="120" y2="200" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="380" y1="71" x2="540" y2="200" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="190" y1="228" x2="470" y2="228" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="146" y1="151" x2="164" y2="169" stroke="#e5484d" stroke-width="4" stroke-linecap="round"/>
            <line x1="146" y1="169" x2="164" y2="151" stroke="#e5484d" stroke-width="4" stroke-linecap="round"/>
            <text x="95" y="150" text-anchor="middle" font-size="11.5" font-weight="600" fill="#a3262b">VLAN 20 blocks</text>
            <line x1="411" y1="219" x2="429" y2="237" stroke="#e5484d" stroke-width="4" stroke-linecap="round"/>
            <line x1="411" y1="237" x2="429" y2="219" stroke="#e5484d" stroke-width="4" stroke-linecap="round"/>
            <text x="420" y="255" text-anchor="middle" font-size="11.5" font-weight="600" fill="#a3262b">VLAN 10 blocks</text>
            <rect x="220" y="95" width="220" height="85" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="330.0" y="116.8" text-anchor="middle" font-size="13" font-weight="700" fill="#3a4352">Root bridge election</text>
            <text x="330.0" y="133.3" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">lowest priority, then lowest MAC</text>
            <text x="330.0" y="149.8" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">root primary = 24576</text>
            <text x="330.0" y="166.3" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">root secondary = 28672</text>
            <text x="330" y="290" text-anchor="middle" font-size="12.5" font-weight="600" fill="#3b2a8a">RSTP: alternate ports take over in about a second instead of 30-50</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what priority does <code>root primary</code> set?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 28672</li>
            <li class="quiz-option">B. 32768</li>
            <li class="quiz-option quiz-correct">C. 24576 ✓</li>
            <li class="quiz-option">D. 4096</li>
        </ul>
        <p class="quiz-explanation"><em>root primary sets 24576, and root secondary sets 28672. Priorities go in steps of 4096.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why does a Layer 2 loop cause a broadcast storm?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Switches are slow</li>
            <li class="quiz-option quiz-correct">B. Ethernet frames have no TTL, so broadcasts circle and multiply forever ✓</li>
            <li class="quiz-option">C. Routers drop them</li>
            <li class="quiz-option">D. VLANs overlap</li>
        </ul>
        <p class="quiz-explanation"><em>Without a hop limit, a looping broadcast is copied again at every switch until the links are full.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. No priorities are set. Which switch becomes root?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The newest</li>
            <li class="quiz-option">B. The one with the most ports</li>
            <li class="quiz-option quiz-correct">C. The one with the lowest MAC address ✓</li>
            <li class="quiz-option">D. The one closest to the router</li>
        </ul>
        <p class="quiz-explanation"><em>With equal priorities, the lowest MAC address wins, which is often the oldest switch.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does <code>spanning-tree vlan 10 root primary</code> set?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Priority 0</li>
            <li class="quiz-option quiz-correct">B. Priority 24576 for VLAN 10 ✓</li>
            <li class="quiz-option">C. Priority 32768</li>
            <li class="quiz-option">D. PortFast</li>
        </ul>
        <p class="quiz-explanation"><em>root primary sets priority 24576 (or lower if needed to win); root secondary sets 28672.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What is the main improvement of RSTP over the original STP?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. More VLANs</li>
            <li class="quiz-option quiz-correct">B. Much faster convergence, about a second instead of 30-50 ✓</li>
            <li class="quiz-option">C. No root bridge</li>
            <li class="quiz-option">D. Encryption</li>
        </ul>
        <p class="quiz-explanation"><em>RSTP pre-selects alternate ports and handshakes with neighbors, so failover is nearly immediate.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why make core-sw root for VLAN 10 and dist-sw root for VLAN 20?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. To confuse attackers</li>
            <li class="quiz-option quiz-correct">B. Each VLAN blocks a different link, so both uplinks carry traffic ✓</li>
            <li class="quiz-option">C. STP requires it</li>
            <li class="quiz-option">D. To save power</li>
        </ul>
        <p class="quiz-explanation"><em>Per-VLAN roots spread the VLANs over different paths, so no link sits idle.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> switch the Citadel to Rapid PVST+, take the root role away from old-sw, and split the VLANs between two roots. <strong>Success:</strong> core-sw is root for VLANs 1 and 10, dist-sw for VLAN 20, and old-sw uses both its uplinks.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g4-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm4', 'lesson' => 'lesson03']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
