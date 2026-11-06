{{-- M3 · Lesson 3.1: Hierarchical Design: Core, Distribution & Access — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g3-l1.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="01">

    <h1 class="lesson-heading">Lesson 3.1: Hierarchical Design: Core, Distribution & Access</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can describe the three layers of a hierarchical network, say what belongs in each, and configure each layer's typical features.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A network that grows one cable at a time turns into spaghetti: nobody can predict what breaks when something fails. Big networks are built in <strong>layers</strong>, each with one job:
    </p>
    <ul class="body-list">
        <li><strong>Access layer</strong>: where users and devices plug in. Switch ports with VLANs, <strong>PortFast</strong> for PCs, port security, and later 802.1X. Lots of ports, cheap switches.</li>
        <li><strong>Distribution layer</strong>: collects the access switches. This is where routing between VLANs happens, where gateways live (often with HSRP), and where <strong>policy</strong> goes: filtering, QoS, route summarization.</li>
        <li><strong>Core layer</strong>: the backbone between distribution blocks. Its only job is to move traffic <em>fast</em> and never go down: no filtering, no fiddly features, and redundant links everywhere.</li>
    </ul>
    <p class="body-text">
        Small sites often merge core and distribution into a <strong>collapsed core</strong>. The rules that make the layers work:
    </p>
    <ul class="body-list">
        <li><strong>Redundancy</strong>: every access switch has two uplinks, to two different distribution devices, and each distribution device has two paths into the core.</li>
        <li><strong>Modularity</strong>: a new building is a new distribution block. Nothing else changes.</li>
        <li><strong>Hierarchical addressing</strong>: give each block one range so routes can be summarized (next lesson).</li>
        <li><strong>No special cases in the core</strong>: hand-made static routes there override the routing protocol and quietly undo the redundancy.</li>
    </ul>
    <pre class="code-block"><code>! access switch, PC ports
interface range g0/1 - 2
 switchport mode access
 spanning-tree portfast</code></pre>
    <p class="body-text">
        <code>spanning-tree portfast</code> lets a port facing a single PC start forwarding immediately, instead of waiting for spanning tree. Only use it toward end devices, never toward another switch.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Which layer's only job is to move traffic fast and never go down?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The access layer</li>
            <li class="quiz-option">B. The distribution layer</li>
            <li class="quiz-option">C. The VLAN layer</li>
            <li class="quiz-option quiz-correct">D. The core layer ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The core is the backbone: fast, simple, redundant, with no filtering or fiddly features.</em></p>
    </div>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        Skyport is turning into a real hub: a hangar full of crew terminals, two distribution routers and a core router that ties it to the rest of the Grid. "This is how Earth builds its stations," says <strong>Astro</strong>. "Access, distribution, core. Each layer does one job."
    </p>
    <p class="body-text">
        <strong>Rivet</strong> walks the racks. "Access switch: PC ports still on factory settings. Distribution: dist-b was never switched on, so the hangar hangs off one router. Core: someone patched it by hand during an outage and never cleaned up."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What did Rivet find wrong in the core?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Someone patched it by hand and left it ✓</li>
            <li class="quiz-option">B. dist-b was set as the root for the hangar</li>
            <li class="quiz-option">C. The core had no cable to the distribution routers</li>
            <li class="quiz-option">D. The PC ports on it were set to PortFast</li>
        </ul>
        <p class="quiz-explanation"><em>Hand-made static routes in the core override the routing protocol and quietly undo the redundancy.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="Three-layer hierarchical design: core at the top, two distribution routers, an access switch with PCs, each layer with its own job">
            <rect x="250" y="15" width="160" height="52" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="330.0" y="36.5" text-anchor="middle" font-size="15" font-weight="700" fill="#7a1820">core</text>
            <text x="330.0" y="53.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">fast, simple, redundant</text>
            <rect x="110" y="115" width="160" height="52" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="190.0" y="136.8" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">dist-a</text>
            <text x="190.0" y="152.8" text-anchor="middle" font-size="11" font-weight="500" fill="#6b3a00">routing, policy, gateways</text>
            <rect x="390" y="115" width="160" height="52" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="470.0" y="136.8" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">dist-b</text>
            <text x="470.0" y="152.8" text-anchor="middle" font-size="11" font-weight="500" fill="#6b3a00">routing, policy, gateways</text>
            <rect x="250" y="215" width="160" height="52" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="330.0" y="236.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">acc</text>
            <text x="330.0" y="253.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">PCs plug in here</text>
            <line x1="290" y1="67" x2="190" y2="115" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="370" y1="67" x2="470" y2="115" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="190" y1="167" x2="300" y2="215" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="470" y1="167" x2="360" y2="215" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <text x="20" y="45" text-anchor="start" font-size="14" font-weight="800" fill="#7a1820">CORE</text>
            <text x="20" y="145" text-anchor="start" font-size="14" font-weight="800" fill="#6b3a00">DISTRIBUTION</text>
            <text x="20" y="245" text-anchor="start" font-size="14" font-weight="800" fill="#14306b">ACCESS</text>
            <text x="640" y="290" text-anchor="end" font-size="12" font-weight="600" fill="#4a35b8">every lower device has two ways up</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, where do the PCs plug in?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The core layer</li>
            <li class="quiz-option">B. The distribution layer</li>
            <li class="quiz-option">C. The dist-b router</li>
            <li class="quiz-option quiz-correct">D. The access layer ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Users and devices plug into the access switch. Distribution collects the access switches, and the core ties it all together.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which layer is where PCs, phones and printers plug in?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Core</li>
            <li class="quiz-option">B. Distribution</li>
            <li class="quiz-option quiz-correct">C. Access ✓</li>
            <li class="quiz-option">D. Transport</li>
        </ul>
        <p class="quiz-explanation"><em>The access layer connects end devices and applies per-port features like VLANs, PortFast and port security.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Where does inter-VLAN routing and policy (filtering, QoS) usually live?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Access</li>
            <li class="quiz-option quiz-correct">B. Distribution ✓</li>
            <li class="quiz-option">C. Core</li>
            <li class="quiz-option">D. In each PC</li>
        </ul>
        <p class="quiz-explanation"><em>The distribution layer aggregates access switches, routes between VLANs and applies policy.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What should the core layer do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Filter every packet carefully</li>
            <li class="quiz-option quiz-correct">B. Move traffic as fast and reliably as possible, with redundancy and no fiddly features ✓</li>
            <li class="quiz-option">C. Host user PCs</li>
            <li class="quiz-option">D. Assign VLANs to ports</li>
        </ul>
        <p class="quiz-explanation"><em>The core is the high-speed backbone. Anything that slows it down or can break it belongs lower in the hierarchy.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Where is <code>spanning-tree portfast</code> safe to use?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. On links between switches</li>
            <li class="quiz-option quiz-correct">B. On ports facing single end devices like PCs ✓</li>
            <li class="quiz-option">C. On the core routers</li>
            <li class="quiz-option">D. Everywhere</li>
        </ul>
        <p class="quiz-explanation"><em>PortFast skips the spanning-tree wait. On a link to another switch it could create a temporary loop, so it is only for end-device ports.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why connect each access switch to two distribution devices?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. To double the VLANs</li>
            <li class="quiz-option quiz-correct">B. So one failure does not cut the access switch off ✓</li>
            <li class="quiz-option">C. Because cables are cheap</li>
            <li class="quiz-option">D. To skip the core</li>
        </ul>
        <p class="quiz-explanation"><em>Two uplinks to two different devices remove the single point of failure at the distribution layer.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> set up the hangar's access ports, switch on the second distribution router, and check the core carries the traffic. <strong>Success:</strong> the hangar reaches the Grid server through the core, with both distribution routers in OSPF.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g3-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm3', 'lesson' => 'lesson01']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
