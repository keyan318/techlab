{{-- M4 · Lesson 4.1: VLAN Trunking & 802.1Q — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g4-l1.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="01">

    <h1 class="lesson-heading">Lesson 4.1: VLAN Trunking & 802.1Q</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain 802.1Q tagging and the native VLAN, configure trunks, and control which VLANs cross them.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A VLAN usually spans more than one switch: the crew sits on two floors. You could run one cable per VLAN between the switches, but that runs out fast. A <strong>trunk</strong> is one link that carries <em>many</em> VLANs.
    </p>
    <p class="body-text">
        To keep them apart, the sending switch inserts a 4-byte <strong>802.1Q tag</strong> into each frame, holding the VLAN number (1 to 4094). The receiving switch reads the tag, files the frame in the right VLAN and removes the tag before sending it to a PC. PCs never see tags.
    </p>
    <ul class="body-list">
        <li><strong>Native VLAN</strong>: one VLAN per trunk travels <em>untagged</em> (VLAN 1 by default). Both ends must agree on it, or frames leak between VLANs. Cisco's CDP warns about a "native VLAN mismatch".</li>
        <li><strong>Allowed VLANs</strong>: by default a trunk carries every VLAN. <code>switchport trunk allowed vlan 10,20</code> limits it to what is needed.</li>
        <li><strong>The VLAN must exist</strong> on every switch it crosses. A switch silently drops tagged frames for a VLAN it doesn't have, which is a classic "half the VLANs work" fault.</li>
    </ul>
    <pre class="code-block"><code>interface g0/8
 switchport mode trunk
 switchport trunk native vlan 99
 switchport trunk allowed vlan 10,20,99</code></pre>
    <p class="body-text">
        <code>show interfaces trunk</code> is the command to remember. It lists each trunk, its native VLAN, the allowed VLANs and, most usefully, the VLANs "allowed and active": the ones that really cross. Switches can also negotiate trunks automatically with <strong>DTP</strong>, but it is safer to set the mode by hand (lesson 4.6).
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What happens to a tagged frame for a VLAN a switch doesn't have?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It joins the native VLAN</li>
            <li class="quiz-option">B. It floods to every port</li>
            <li class="quiz-option quiz-correct">C. It is silently dropped ✓</li>
            <li class="quiz-option">D. It loses its 802.1Q tag</li>
        </ul>
        <p class="quiz-explanation"><em>A switch silently drops frames for a VLAN it doesn't have. That's a classic “half the VLANs work” fault.</em></p>
    </div>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        The Citadel's archive takes up two floors now, with a switch on each. Crew terminals on both floors are in VLAN 10, the archive machines in VLAN 20. One cable runs between the switches. "And it's an ordinary access port in VLAN 1," sighs <strong>Rivet</strong>. "So neither of our VLANs gets across."
    </p>
    <p class="body-text">
        "One cable can carry them all," says <strong>Volt</strong>, "if you tag every frame. Make it a trunk. Then lock it down: a trunk that carries everything is a trunk an attacker loves."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why can't VLAN 10 or 20 cross between the two floors?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The link is a trunk with no native VLAN</li>
            <li class="quiz-option">B. Both switches are missing VLAN 1 itself</li>
            <li class="quiz-option quiz-correct">C. The link is an access port in VLAN 1 ✓</li>
            <li class="quiz-option">D. The cable between them is fully unplugged</li>
        </ul>
        <p class="quiz-explanation"><em>An access port carries only one VLAN. Make the link a trunk and tag every frame so all VLANs cross.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 290" role="img" aria-label="An 802.1Q trunk between two switches carrying VLAN 10 and VLAN 20, with a 4-byte tag inserted in each frame">
            <rect x="10" y="20" width="150" height="50" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="85.0" y="40.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">astro</text>
            <text x="85.0" y="57.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14306b">VLAN 10</text>
            <rect x="10" y="90" width="150" height="50" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="85.0" y="110.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">scribe</text>
            <text x="85.0" y="127.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#6b3a00">VLAN 20</text>
            <rect x="200" y="45" width="110" height="70" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="255.0" y="85.2" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">arc-sw1</text>
            <rect x="350" y="45" width="110" height="70" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="405.0" y="85.2" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">arc-sw2</text>
            <rect x="500" y="20" width="150" height="50" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="575.0" y="40.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">volt</text>
            <text x="575.0" y="57.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14306b">VLAN 10</text>
            <rect x="500" y="90" width="150" height="50" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="575.0" y="109.5" text-anchor="middle" font-size="13.5" font-weight="700" fill="#6b3a00">archive-srv</text>
            <text x="575.0" y="127.0" text-anchor="middle" font-size="12.5" font-weight="500" fill="#6b3a00">VLAN 20</text>
            <line x1="160" y1="45" x2="200" y2="70" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="160" y1="115" x2="200" y2="95" stroke="#f08c1a" stroke-width="3" stroke-linecap="round"/>
            <line x1="460" y1="70" x2="500" y2="45" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="460" y1="95" x2="500" y2="115" stroke="#f08c1a" stroke-width="3" stroke-linecap="round"/>
            <line x1="310" y1="72" x2="350" y2="72" stroke="#2f6fe0" stroke-width="4" stroke-linecap="round"/>
            <line x1="310" y1="88" x2="350" y2="88" stroke="#f08c1a" stroke-width="4" stroke-linecap="round"/>
            <text x="330" y="135" text-anchor="middle" font-size="11.5" font-weight="600" fill="#3b2a8a">trunk Gi0/8</text>
            <rect x="10" y="170" width="640" height="50" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="330.0" y="200.2" text-anchor="middle" font-size="15" font-weight="700" fill="#3a4352"></text>
            <rect x="20" y="178" width="120" height="34" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="80.0" y="199.4" text-anchor="middle" font-size="12.5" font-weight="700" fill="#3a4352">Dest MAC</text>
            <rect x="145" y="178" width="120" height="34" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="205.0" y="199.4" text-anchor="middle" font-size="12.5" font-weight="700" fill="#3a4352">Src MAC</text>
            <rect x="270" y="178" width="140" height="34" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="340.0" y="199.4" text-anchor="middle" font-size="12.5" font-weight="700" fill="#7a1820">802.1Q tag: VLAN 20</text>
            <rect x="415" y="178" width="90" height="34" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="460.0" y="199.4" text-anchor="middle" font-size="12.5" font-weight="700" fill="#3a4352">Type</text>
            <rect x="510" y="178" width="130" height="34" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="575.0" y="199.4" text-anchor="middle" font-size="12.5" font-weight="700" fill="#3a4352">Data…</text>
            <text x="330" y="250" text-anchor="middle" font-size="12.5" font-weight="600" fill="#3b2a8a">native VLAN: the one VLAN sent untagged. Must match on both ends.</text>
            <text x="330" y="272" text-anchor="middle" font-size="12.5" font-weight="600" fill="#3b2a8a">a VLAN only crosses the trunk if it exists on BOTH switches and is allowed.</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, how big is the 802.1Q tag?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. 4 bytes ✓</li>
            <li class="quiz-option">B. 2 bytes</li>
            <li class="quiz-option">C. 8 bytes</li>
            <li class="quiz-option">D. 12 bits</li>
        </ul>
        <p class="quiz-explanation"><em>The sending switch inserts a 4-byte 802.1Q tag holding the VLAN number into each frame.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does an 802.1Q tag contain?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The destination IP</li>
            <li class="quiz-option quiz-correct">B. The VLAN number the frame belongs to ✓</li>
            <li class="quiz-option">C. The MAC table</li>
            <li class="quiz-option">D. A password</li>
        </ul>
        <p class="quiz-explanation"><em>The 4-byte 802.1Q tag carries the VLAN ID (and a priority), so the far switch knows which VLAN the frame is in.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is the native VLAN on a trunk?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The fastest VLAN</li>
            <li class="quiz-option quiz-correct">B. The one VLAN whose frames travel untagged ✓</li>
            <li class="quiz-option">C. VLAN 4094</li>
            <li class="quiz-option">D. A VLAN with no ports</li>
        </ul>
        <p class="quiz-explanation"><em>Frames in the native VLAN cross the trunk without a tag. Both ends must use the same native VLAN.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. VLAN 10 crosses the trunk but VLAN 20 doesn't. The far switch has ports in VLAN 20. What is a likely cause?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The cable is too long</li>
            <li class="quiz-option quiz-correct">B. VLAN 20 doesn't exist on one switch, or isn't in the trunk's allowed list ✓</li>
            <li class="quiz-option">C. Trunks only carry one VLAN</li>
            <li class="quiz-option">D. PCs can't use VLAN 20</li>
        </ul>
        <p class="quiz-explanation"><em>A VLAN must exist on both switches and be allowed on the trunk. "Vlans allowed and active" in show interfaces trunk shows which ones really cross.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Which command limits a trunk to VLANs 10 and 20?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>switchport access vlan 10,20</code></li>
            <li class="quiz-option quiz-correct">B. <code>switchport trunk allowed vlan 10,20</code> ✓</li>
            <li class="quiz-option">C. <code>vlan 10,20</code></li>
            <li class="quiz-option">D. <code>switchport trunk native vlan 10,20</code></li>
        </ul>
        <p class="quiz-explanation"><em>The allowed list controls which VLANs the trunk carries.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Which command shows trunks with their native and active VLANs?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>show vlan brief</code></li>
            <li class="quiz-option quiz-correct">B. <code>show interfaces trunk</code> ✓</li>
            <li class="quiz-option">C. <code>show ip route</code></li>
            <li class="quiz-option">D. <code>show mac address-table</code></li>
        </ul>
        <p class="quiz-explanation"><em><code>show interfaces trunk</code> lists every trunk, its native VLAN, and allowed and active VLANs.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> turn the link between the archive switches into a trunk, create the missing VLAN, and lock the trunk down. <strong>Success:</strong> crew and archive traffic both cross, on a trunk with an unused native VLAN and only the VLANs it needs.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g4-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm4', 'lesson' => 'lesson01']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
