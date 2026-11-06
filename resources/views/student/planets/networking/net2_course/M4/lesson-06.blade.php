{{-- M4 · Lesson 4.6: VLAN Security Best Practices — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g4-l6.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="06">

    <h1 class="lesson-heading">Lesson 4.6: VLAN Security Best Practices</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain VLAN hopping attacks and harden switch ports and trunks against them, and describe private VLANs and dynamic VLAN assignment.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        VLANs separate traffic, but only if an attacker can't jump between them. <strong>VLAN hopping</strong> comes in two kinds:
    </p>
    <ul class="body-list">
        <li><strong>Switch spoofing</strong>: by default, Cisco ports run <strong>DTP</strong> (Dynamic Trunking Protocol) and will become a trunk if the other side asks. An attacker's laptop that speaks DTP gets a trunk, and with it every VLAN. Fix: set user ports to <code>switchport mode access</code>, and turn DTP off everywhere with <code>switchport nonegotiate</code>.</li>
        <li><strong>Double tagging</strong>: the attacker sends a frame with two 802.1Q tags, the outer one matching the trunk's native VLAN. The first switch removes the outer tag and forwards the frame untagged across the trunk; the next switch reads the inner tag and drops it into the victim's VLAN. Fix: make the native VLAN an unused one (like 999) that no user port is in, and allow only the VLANs the trunk needs.</li>
    </ul>
    <pre class="code-block"><code>interface g0/8
 switchport mode trunk
 switchport nonegotiate
 switchport trunk native vlan 999
 switchport trunk allowed vlan 10</code></pre>
    <p class="body-text">
        More good habits:
    </p>
    <ul class="body-list">
        <li><strong>Never use VLAN 1</strong> for users or management. Park unused ports in a dead VLAN and shut them down.</li>
        <li><strong>Private VLANs</strong> split one VLAN into ports that can only reach the gateway (<em>isolated</em>), or only their own group (<em>community</em>). Great for guest networks, where guests must not see each other.</li>
        <li><strong>Dynamic VLAN assignment</strong>: with 802.1X, the RADIUS server tells the switch which VLAN each user belongs in, so the VLAN follows the person, not the port.</li>
    </ul>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">How do you stop switch spoofing on a user port?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Set it to access mode and turn DTP off ✓</li>
            <li class="quiz-option">B. Make the port a trunk for all the VLANs</li>
            <li class="quiz-option">C. Put the port into the native VLAN only</li>
            <li class="quiz-option">D. Turn on PortFast and leave DTP running</li>
        </ul>
        <p class="quiz-explanation"><em>Set user ports to switchport mode access and use switchport nonegotiate so DTP can't make a trunk.</em></p>
    </div>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        Doom's probe from yesterday left a log behind. <strong>Volt</strong> reads it out: "It tried to talk DTP to the switch, to become a trunk. Then it sent frames with two VLAN tags. It was trying to hop into the Citadel's crew VLAN."
    </p>
    <p class="body-text">
        "Both tricks work on factory settings," says <strong>Rivet</strong>. "Our edge switch is still on factory settings." Volt hands you the checklist. "Then let's not be on factory settings."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why did Doom's probe tricks work on the edge switch?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It had no VLANs configured at all</li>
            <li class="quiz-option quiz-correct">B. It was still on factory settings ✓</li>
            <li class="quiz-option">C. It was running an old DNS server</li>
            <li class="quiz-option">D. Its trunk port was shut down</li>
        </ul>
        <p class="quiz-explanation"><em>Both DTP spoofing and double tagging work on factory settings. Change the defaults to block them.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 290" role="img" aria-label="Two VLAN hopping attacks and their defences: switch spoofing stopped by static access ports, double tagging stopped by an unused native VLAN">
            <rect x="10" y="15" width="310" height="120" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="165.0" y="39.1" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1820">Switch spoofing</text>
            <text x="165.0" y="55.6" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">attacker&#x27;s PC speaks DTP:</text>
            <text x="165.0" y="72.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">&quot;let&#x27;s be a trunk&quot;</text>
            <text x="165.0" y="88.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">gets every VLAN</text>
            <text x="165.0" y="105.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">fix: switchport mode access</text>
            <text x="165.0" y="121.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">+ switchport nonegotiate</text>
            <rect x="340" y="15" width="310" height="120" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="495.0" y="39.1" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1820">Double tagging</text>
            <text x="495.0" y="55.6" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">two tags, outer = native VLAN</text>
            <text x="495.0" y="72.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">first switch strips it, sends</text>
            <text x="495.0" y="88.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">the inner tag across the trunk</text>
            <text x="495.0" y="105.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">fix: native VLAN = unused 999</text>
            <text x="495.0" y="121.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">+ only needed VLANs allowed</text>
            <rect x="10" y="155" width="310" height="120" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="165.0" y="194.7" text-anchor="middle" font-size="14" font-weight="700" fill="#14663f">Unused ports</text>
            <text x="165.0" y="211.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">parked in a dead VLAN</text>
            <text x="165.0" y="227.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">(VLAN 666 PARKING)</text>
            <text x="165.0" y="244.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">and shut down</text>
            <rect x="340" y="155" width="310" height="120" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="495.0" y="186.9" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">Private VLANs</text>
            <text x="495.0" y="203.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">isolated ports in one subnet</text>
            <text x="495.0" y="219.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">can only talk to the gateway,</text>
            <text x="495.0" y="236.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">not to each other</text>
            <text x="495.0" y="252.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">(guest Wi-Fi, hotel rooms)</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what stops double tagging?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A shorter trunk cable</li>
            <li class="quiz-option quiz-correct">B. An unused native VLAN ✓</li>
            <li class="quiz-option">C. Turning PortFast on</li>
            <li class="quiz-option">D. Using VLAN 1 for all</li>
        </ul>
        <p class="quiz-explanation"><em>Make the native VLAN an unused one like 999, and allow only the VLANs the trunk needs.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. How does switch spoofing work?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It fakes an IP address</li>
            <li class="quiz-option quiz-correct">B. The attacker negotiates a trunk with DTP and receives every VLAN ✓</li>
            <li class="quiz-option">C. It floods the MAC table</li>
            <li class="quiz-option">D. It cuts the trunk</li>
        </ul>
        <p class="quiz-explanation"><em>If a port is dynamic, an attacker that speaks DTP can turn it into a trunk carrying all VLANs.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which two commands stop switch spoofing on a user port?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>switchport mode trunk</code> and <code>no shutdown</code></li>
            <li class="quiz-option quiz-correct">B. <code>switchport mode access</code> and <code>switchport nonegotiate</code> ✓</li>
            <li class="quiz-option">C. <code>spanning-tree portfast</code> and <code>bpduguard</code></li>
            <li class="quiz-option">D. <code>vlan 1</code> and <code>name USERS</code></li>
        </ul>
        <p class="quiz-explanation"><em>A static access port never becomes a trunk, and nonegotiate stops DTP messages altogether.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Double tagging relies on which trunk setting?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The allowed VLAN list</li>
            <li class="quiz-option quiz-correct">B. The native VLAN matching the attacker's access VLAN ✓</li>
            <li class="quiz-option">C. PortFast</li>
            <li class="quiz-option">D. Port security</li>
        </ul>
        <p class="quiz-explanation"><em>The outer tag is stripped because it matches the native VLAN. An unused native VLAN breaks the attack.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What should happen to unused switch ports?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Leave them in VLAN 1</li>
            <li class="quiz-option quiz-correct">B. Park them in an unused VLAN and shut them down ✓</li>
            <li class="quiz-option">C. Make them trunks</li>
            <li class="quiz-option">D. Give them PortFast only</li>
        </ul>
        <p class="quiz-explanation"><em>A dead VLAN plus shutdown means a plugged-in device gets nothing.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. A guest network must stop guests from seeing each other, while all can reach the gateway. What fits?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A trunk</li>
            <li class="quiz-option quiz-correct">B. Private VLANs with isolated ports ✓</li>
            <li class="quiz-option">C. Root Guard</li>
            <li class="quiz-option">D. A bigger subnet</li>
        </ul>
        <p class="quiz-explanation"><em>Isolated private VLAN ports can only talk to the promiscuous port (the gateway), not to each other.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> harden the Citadel's edge switch against both VLAN hopping tricks and park every unused port. <strong>Success:</strong> crew PCs still reach the server, and none of the hardening steps is missing.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g4-l6" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm4', 'lesson' => 'lesson06']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
