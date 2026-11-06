{{-- M1 · Lesson 1.1: Switches, Routers & VLANs Revisited — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g1-l1.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="01">

    <h1 class="lesson-heading">Lesson 1.1: Switches, Routers & VLANs Revisited</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what switches, routers and VLANs each do, and configure them with Cisco IOS commands.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Season 1 built one small network. Season 2 builds a big one, so we start by checking the three tools everything else stands on.
    </p>
    <ul class="body-list">
        <li><strong>Switch</strong> (Layer 2): connects devices inside one network. It learns which <strong>MAC address</strong> lives on which port and sends each frame only where it needs to go.</li>
        <li><strong>Router</strong> (Layer 3): connects <em>different</em> networks. It reads the <strong>IP address</strong> and picks a path from its routing table. To a PC, the router is its <strong>default gateway</strong>.</li>
        <li><strong>VLAN</strong> (virtual LAN): splits one switch into separate networks. Ports in VLAN 10 and ports in VLAN 20 cannot hear each other at all, as if they were on two different switches. Each VLAN gets its own subnet, and a router connects them.</li>
    </ul>
    <p class="body-text">
        The gear Earth sent runs <strong>Cisco IOS</strong>, the language most real routers and switches speak. IOS has <strong>modes</strong>, like rooms you walk through: <code>Router&gt;</code> lets you look, <code>enable</code> takes you to <code>Router#</code>, <code>configure terminal</code> to <code>Router(config)#</code>, and <code>interface g0/0</code> to the settings of one port. Short forms work: <code>conf t</code>, <code>int g0/0</code>, <code>no shut</code>.
    </p>
    <pre class="code-block"><code>interface g0/1
 ip address 192.168.20.1 255.255.255.0
 no shutdown</code></pre>
    <p class="body-text">
        One trap catches everyone: a Cisco router's ports start <strong>shut down</strong>. An address alone does nothing until you type <code>no shutdown</code>. <code>show ip interface brief</code> tells you: "administratively down" means you forgot it.
    </p>
    <p class="body-text">
        On a switch, a port joins a VLAN with <code>switchport access vlan 10</code>. <code>interface range g0/1 - 2</code> sets several ports at once, and <code>show vlan brief</code> lists which port is in which VLAN.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">A router port has an address but shows “administratively down”. What was forgotten?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Typing <code>enable</code> before <code>conf t</code></li>
            <li class="quiz-option">B. Putting the port into VLAN 10</li>
            <li class="quiz-option quiz-correct">C. Typing <code>no shutdown</code> on the port ✓</li>
            <li class="quiz-option">D. Giving the switch a VLAN name tag</li>
        </ul>
        <p class="quiz-explanation"><em>Cisco router ports start shut down. An address alone does nothing until you type no shutdown.</em></p>
    </div>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        Earth's answer comes in clear: <em>"Codexia, we hear you. Planet Doom is regrouping, and your outposts are islands. Link them into one network and we will route our relay fleet through you."</em> Astro reads it twice. "Season one, we made one network talk to Earth. Now we build one the size of a planet. We call it the <strong>Grid</strong>."
    </p>
    <p class="body-text">
        <strong>Rivet</strong> is already unpacking Earth's supply drop: real routers and switches. "They speak Cisco IOS, Cadet. Same ideas as before, new words. The deck router is asleep and the switch has every port in one big VLAN. Let's wake them up properly."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What does Rivet find wrong with the deck switch?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is missing its Cisco IOS image</li>
            <li class="quiz-option quiz-correct">B. Every port is in one big VLAN ✓</li>
            <li class="quiz-option">C. Its ports are all shut down for good</li>
            <li class="quiz-option">D. It has no cable to the deck router</li>
        </ul>
        <p class="quiz-explanation"><em>The switch has every port in one big VLAN, and the deck router is asleep. Both need setting up properly.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="Three PCs on a switch split into VLAN 10 and VLAN 20, with one router port per VLAN">
            <rect x="10" y="20" width="120" height="52" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="70.0" y="41.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">astro</text>
            <text x="70.0" y="58.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14306b">192.168.10.10</text>
            <rect x="10" y="92" width="120" height="52" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="70.0" y="113.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">volt</text>
            <text x="70.0" y="130.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14306b">192.168.10.11</text>
            <rect x="10" y="176" width="120" height="52" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="70.0" y="197.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">rivet</text>
            <text x="70.0" y="214.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#6b3a00">192.168.20.10</text>
            <rect x="250" y="80" width="120" height="90" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="310.0" y="111.8" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">deck-sw</text>
            <text x="310.0" y="129.2" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">switch</text>
            <text x="310.0" y="146.8" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">VLAN 10 + 20</text>
            <rect x="500" y="80" width="140" height="90" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="570.0" y="105.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">deck-rtr</text>
            <text x="570.0" y="121.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">router</text>
            <text x="570.0" y="138.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">g0/0 .10.1</text>
            <text x="570.0" y="154.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">g0/1 .20.1</text>
            <line x1="130" y1="46" x2="250" y2="100" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="130" y1="118" x2="250" y2="118" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="130" y1="202" x2="250" y2="150" stroke="#f08c1a" stroke-width="3" stroke-linecap="round"/>
            <line x1="370" y1="105" x2="500" y2="105" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="370" y1="145" x2="500" y2="145" stroke="#f08c1a" stroke-width="3" stroke-linecap="round"/>
            <text x="435" y="98" text-anchor="middle" font-size="11.5" font-weight="600" fill="#2f6fe0">VLAN 10</text>
            <text x="435" y="162" text-anchor="middle" font-size="11.5" font-weight="600" fill="#c06a00">VLAN 20</text>
            <rect x="10" y="246" width="640" height="44" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="330.0" y="272.4" text-anchor="middle" font-size="12.5" font-weight="700" fill="#3a4352">IOS modes:  Router&gt;  →enable→  Router#  →conf t→  Router(config)#  →interface g0/0→  Router(config-if)#</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, which command moves you from Router# to Router(config)#?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>enable</code></li>
            <li class="quiz-option">B. <code>no shut</code></li>
            <li class="quiz-option quiz-correct">C. <code>conf t</code> ✓</li>
            <li class="quiz-option">D. <code>int g0/0</code></li>
        </ul>
        <p class="quiz-explanation"><em>enable goes from Router&gt; to Router#, and conf t (configure terminal) goes on to Router(config)#.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which device forwards frames inside one network using MAC addresses?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A router</li>
            <li class="quiz-option quiz-correct">B. A switch ✓</li>
            <li class="quiz-option">C. A DNS server</li>
            <li class="quiz-option">D. A modem</li>
        </ul>
        <p class="quiz-explanation"><em>A switch works at Layer 2: it learns MAC addresses per port and forwards frames inside the LAN. A router works at Layer 3 between networks.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. A PC in VLAN 10 needs to reach a PC in VLAN 20 on the same switch. What does the traffic have to pass through?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing, they share a switch</li>
            <li class="quiz-option">B. A longer cable</li>
            <li class="quiz-option quiz-correct">C. A router (their default gateway) ✓</li>
            <li class="quiz-option">D. A second switch</li>
        </ul>
        <p class="quiz-explanation"><em>VLANs are separate networks. Traffic between them must be routed, so it goes to the default gateway and back.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which prompt means you are configuring one interface?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Router></li>
            <li class="quiz-option">B. Router#</li>
            <li class="quiz-option">C. Router(config)#</li>
            <li class="quiz-option quiz-correct">D. Router(config-if)# ✓</li>
        </ul>
        <p class="quiz-explanation"><em><code>(config-if)</code> is interface configuration mode, reached with <code>interface g0/0</code> from global configuration mode.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. You set an IP address on g0/1, but <code>show ip interface brief</code> says "administratively down". What is missing?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. <code>no shutdown</code> ✓</li>
            <li class="quiz-option">B. <code>enable</code></li>
            <li class="quiz-option">C. A VLAN</li>
            <li class="quiz-option">D. A reboot</li>
        </ul>
        <p class="quiz-explanation"><em>Cisco router ports start shut down. <code>no shutdown</code> turns the port on.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does <code>interface range g0/1 - 2</code> do on a switch?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Deletes ports 1 and 2</li>
            <li class="quiz-option quiz-correct">B. Lets you configure ports 1 and 2 together ✓</li>
            <li class="quiz-option">C. Links port 1 to port 2</li>
            <li class="quiz-option">D. Shows the status of both ports</li>
        </ul>
        <p class="quiz-explanation"><em>Interface range mode applies every command you type to all the listed ports at once.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> wake the deck router, split the deck switch into a CREW VLAN and a WORKSHOP VLAN, and lock the PC ports. <strong>Success:</strong> astro and volt reach rivet's workbench through the router.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g1-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm1', 'lesson' => 'lesson01']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
