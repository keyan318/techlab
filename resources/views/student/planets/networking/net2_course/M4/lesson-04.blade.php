{{-- M4 · Lesson 4.4: PortFast, BPDU Guard & Root Guard — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g4-l4.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="04">

    <h1 class="lesson-heading">Lesson 4.4: PortFast, BPDU Guard & Root Guard</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can protect spanning tree with PortFast, BPDU Guard and Root Guard, and recover an err-disabled port.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Spanning tree trusts every switch that speaks it. That is a weakness: plug in a switch with priority 0 and it becomes the root bridge, and the whole network reroutes through it, maybe through an attacker. Three features keep spanning tree safe:
    </p>
    <ul class="body-list">
        <li><strong>PortFast</strong> (on ports to PCs): skip the listening and learning wait, so a PC gets its network at once. Such a port is an <em>edge</em> port.</li>
        <li><strong>BPDU Guard</strong> (on PortFast ports): BPDUs are spanning tree's messages, and only switches send them. If one arrives on a PC port, someone plugged in a switch. The port is shut down at once: <strong>err-disabled</strong>.</li>
        <li><strong>Root Guard</strong> (on distribution or core ports facing down): if a port hears a <em>better</em> root than the current one, it is blocked as <strong>root-inconsistent</strong>, so no switch below it can take over the root role. It recovers by itself when the better BPDUs stop.</li>
    </ul>
    <pre class="code-block"><code>interface range g0/1 - 4
 spanning-tree portfast
 spanning-tree bpduguard enable
!
interface range g0/1 - 2      ! on the core, facing down
 spanning-tree guard root</code></pre>
    <p class="body-text">
        <code>spanning-tree portfast default</code> and <code>spanning-tree portfast bpduguard default</code> turn them on for every access port at once. An err-disabled port stays down until you fix the cause and type <code>shutdown</code> then <code>no shutdown</code>. <code>show interfaces status</code> lists err-disabled ports.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does BPDU Guard do when a BPDU reaches a PC port?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Makes that port the new root port</li>
            <li class="quiz-option quiz-correct">B. Shuts the port down, err-disabled ✓</li>
            <li class="quiz-option">C. Turns PortFast off for that port</li>
            <li class="quiz-option">D. Blocks the port as root-inconsistent</li>
        </ul>
        <p class="quiz-explanation"><em>Only switches send BPDUs. If one arrives on a PC port, BPDU Guard shuts it down at once: err-disabled.</em></p>
    </div>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        <strong>Volt</strong> finds two problems in one walk round the Citadel. "Someone plugged a little switch into a visitor port on acc1, set to priority 0. It's the root bridge now. Half our traffic goes through a box Doom left behind."
    </p>
    <p class="body-text">
        "And acc2?" asks <strong>Astro</strong>. Volt shows the config: "Someone set its priority to 4096 too. That one is ours, but it shouldn't be root either. We need guards on both edges of the tree."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why does Volt want guards on both edges of the tree?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The core switch has a dead downlink</li>
            <li class="quiz-option">B. PortFast is missing on all of the PC ports</li>
            <li class="quiz-option">C. acc2 is one of Doom's switches too</li>
            <li class="quiz-option quiz-correct">D. Two switches are wrongly claiming root ✓</li>
        </ul>
        <p class="quiz-explanation"><em>A Doom switch at priority 0 took root, and acc2 was set to 4096. Guards stop any switch below from taking over.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 290" role="img" aria-label="Where each spanning tree protection goes: PortFast and BPDU Guard on access ports, Root Guard on the core switch&#x27;s downlinks">
            <rect x="230" y="15" width="200" height="56" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="330.0" y="38.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">core-sw</text>
            <text x="330.0" y="55.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">the legitimate root</text>
            <rect x="60" y="130" width="160" height="50" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="140.0" y="160.2" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">acc1</text>
            <rect x="440" y="130" width="160" height="50" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="520.0" y="150.8" text-anchor="middle" font-size="15" font-weight="700" fill="#7a1820">acc2</text>
            <text x="520.0" y="166.8" text-anchor="middle" font-size="11" font-weight="500" fill="#7a1820">priority set to 4096!</text>
            <line x1="290" y1="71" x2="140" y2="130" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="370" y1="71" x2="520" y2="130" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="220" y1="155" x2="440" y2="155" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <rect x="230" y="85" width="200" height="34" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="330.0" y="106.2" text-anchor="middle" font-size="12" font-weight="700" fill="#6b3a00">Root Guard on both downlinks</text>
            <rect x="10" y="220" width="120" height="50" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="70.0" y="240.5" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">pc1</text>
            <text x="70.0" y="256.0" text-anchor="middle" font-size="10.5" font-weight="500" fill="#14306b">PortFast + BPDU Guard</text>
            <rect x="150" y="220" width="120" height="50" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="210.0" y="240.2" text-anchor="middle" font-size="13.5" font-weight="700" fill="#7a1820">doom-sw</text>
            <text x="210.0" y="256.2" text-anchor="middle" font-size="11" font-weight="500" fill="#7a1820">priority 0</text>
            <line x1="70" y1="220" x2="110" y2="180" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="210" y1="220" x2="170" y2="180" stroke="#e5484d" stroke-width="3" stroke-linecap="round"/>
            <line x1="181" y1="191" x2="199" y2="209" stroke="#e5484d" stroke-width="4" stroke-linecap="round"/>
            <line x1="181" y1="209" x2="199" y2="191" stroke="#e5484d" stroke-width="4" stroke-linecap="round"/>
            <text x="470" y="230" text-anchor="middle" font-size="12.5" font-weight="700" fill="#7a1820">BPDU Guard: a switch on a PC port</text>
            <text x="470" y="250" text-anchor="middle" font-size="12.5" font-weight="700" fill="#7a1820">= port err-disabled</text>
            <text x="470" y="275" text-anchor="middle" font-size="12.5" font-weight="700" fill="#6b3a00">Root Guard: a better root = port blocked</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, where does Root Guard go?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The PC-facing access ports</li>
            <li class="quiz-option quiz-correct">B. The core's downlinks ✓</li>
            <li class="quiz-option">C. The doom-sw uplink</li>
            <li class="quiz-option">D. The acc1 PC ports</li>
        </ul>
        <p class="quiz-explanation"><em>Root Guard goes on the core switch's ports facing down, so no switch below can become root.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does PortFast do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Speeds up the link</li>
            <li class="quiz-option quiz-correct">B. Lets an end-device port forward immediately instead of waiting for spanning tree ✓</li>
            <li class="quiz-option">C. Blocks BPDUs</li>
            <li class="quiz-option">D. Makes the switch root</li>
        </ul>
        <p class="quiz-explanation"><em>PortFast skips the listening and learning states on edge ports facing PCs.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. A BPDU arrives on a port with BPDU Guard. What happens?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing</li>
            <li class="quiz-option quiz-correct">B. The port is err-disabled (shut down) ✓</li>
            <li class="quiz-option">C. The switch becomes root</li>
            <li class="quiz-option">D. The BPDU is forwarded</li>
        </ul>
        <p class="quiz-explanation"><em>Only switches send BPDUs, so one on a PC port means an unauthorized switch. BPDU Guard shuts the port.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Where does Root Guard belong?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. On PC ports</li>
            <li class="quiz-option quiz-correct">B. On distribution or core ports facing switches that must never become root ✓</li>
            <li class="quiz-option">C. On the root bridge's uplink to nothing</li>
            <li class="quiz-option">D. On router ports</li>
        </ul>
        <p class="quiz-explanation"><em>Root Guard on downward-facing ports stops any switch below from becoming root.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. A Root Guard port shows "root-inconsistent". What does it mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The cable is cut</li>
            <li class="quiz-option quiz-correct">B. It heard a better root bridge than the current one and is blocked ✓</li>
            <li class="quiz-option">C. It is the root port</li>
            <li class="quiz-option">D. PortFast is on</li>
        </ul>
        <p class="quiz-explanation"><em>The port received superior BPDUs. It stays blocked until they stop, then recovers automatically.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. How do you bring an err-disabled port back?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Wait a minute</li>
            <li class="quiz-option quiz-correct">B. Remove the cause, then <code>shutdown</code> and <code>no shutdown</code> ✓</li>
            <li class="quiz-option">C. Reboot the PC</li>
            <li class="quiz-option">D. Change its VLAN</li>
        </ul>
        <p class="quiz-explanation"><em>An err-disabled port stays down until an admin fixes the cause and bounces the port.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> lock the access ports with PortFast and BPDU Guard, put Root Guard on the core's downlinks, and fix the switch that wanted to be root. <strong>Success:</strong> core-sw stays root, Doom's switch is cut off, and every PC reaches the Citadel server.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g4-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm4', 'lesson' => 'lesson04']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
