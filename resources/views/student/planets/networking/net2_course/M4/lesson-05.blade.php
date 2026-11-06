{{-- M4 · Lesson 4.5: Port Security & 802.1X — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g4-l5.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="05">

    <h1 class="lesson-heading">Lesson 4.5: Port Security & 802.1X</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can limit which devices may use a switch port with port security, choose a violation mode, and explain how 802.1X network access control works.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A switch port normally accepts anyone who plugs in. That is fine in a locked room and dangerous in a hangar full of visitors. <strong>Port security</strong> limits a port to known MAC addresses:
    </p>
    <ul class="body-list">
        <li><code>switchport port-security maximum 1</code>: how many MACs may use the port (default 1).</li>
        <li><code>switchport port-security mac-address sticky</code>: learn the first MAC(s) automatically and save them in the config.</li>
        <li><strong>Violation modes</strong> when an unknown MAC shows up: <strong>shutdown</strong> (the default: port err-disabled), <strong>restrict</strong> (drop the frames and count them), <strong>protect</strong> (drop quietly).</li>
    </ul>
    <pre class="code-block"><code>interface g0/1
 switchport mode access
 switchport port-security
 switchport port-security mac-address sticky</code></pre>
    <p class="body-text">
        Port security only works on a port set to <code>switchport mode access</code>; on a dynamic port IOS rejects it. <code>show port-security</code> lists secured ports and violation counts. And the cheapest protection of all: <strong>shut down unused ports</strong>.
    </p>
    <p class="body-text">
        MAC addresses can be faked, so serious networks use <strong>802.1X</strong>, a form of <strong>network access control</strong> (NAC). The PC (<strong>supplicant</strong>) must log in before the switch (<strong>authenticator</strong>) opens the port; the switch checks the login with an <strong>authentication server</strong>, usually <strong>RADIUS</strong>, using <strong>EAP</strong> messages. The server can even tell the switch which VLAN to put the user in (dynamic VLAN assignment). Lesson 6.6 configures the RADIUS side.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does <code>mac-address sticky</code> do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Blocks every MAC address on the port</li>
            <li class="quiz-option quiz-correct">B. Learns the first MACs and saves them ✓</li>
            <li class="quiz-option">C. Lets any number of MACs use the port</li>
            <li class="quiz-option">D. Copies the MAC from the switch itself</li>
        </ul>
        <p class="quiz-explanation"><em>Sticky learns the first MAC(s) automatically and saves them in the config.</em></p>
    </div>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        Earth's ships dock at Skyport every day now, and their crews wander the hangar. <strong>Volt</strong> found a small grey box under a desk, plugged into a free port: a <em>doom-probe</em>, quietly mapping the network.
    </p>
    <p class="body-text">
        "From today, a hangar port works for its owner and nobody else," says Volt. "Crew desks learn their PC and stop. The hot-desk takes two laptops at most. And every port nobody uses gets switched off."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What does Volt do about the ports nobody uses?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Gives them to guests</li>
            <li class="quiz-option quiz-correct">B. Switches them off ✓</li>
            <li class="quiz-option">C. Turns them into trunks</li>
            <li class="quiz-option">D. Moves them to VLAN 1</li>
        </ul>
        <p class="quiz-explanation"><em>The cheapest protection of all: shut down unused ports so nothing can be plugged into them.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 280" role="img" aria-label="Port security learns the allowed MAC address on each port and acts on violations; 802.1X asks the RADIUS server before opening a port">
            <rect x="10" y="15" width="300" height="120" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="160.0" y="39.1" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">Port security</text>
            <text x="160.0" y="55.6" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">max 1 MAC per port, sticky</text>
            <text x="160.0" y="72.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">unknown MAC arrives →</text>
            <text x="160.0" y="88.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">shutdown: port err-disabled</text>
            <text x="160.0" y="105.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">restrict: drop + count</text>
            <text x="160.0" y="121.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">protect: drop quietly</text>
            <rect x="350" y="15" width="300" height="120" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="500.0" y="46.9" text-anchor="middle" font-size="14" font-weight="700" fill="#6b3a00">802.1X</text>
            <text x="500.0" y="63.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">supplicant (the PC)</text>
            <text x="500.0" y="79.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">authenticator (the switch)</text>
            <text x="500.0" y="96.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">authentication server (RADIUS)</text>
            <text x="500.0" y="112.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">port opens only after login</text>
            <rect x="10" y="165" width="140" height="50" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="80.0" y="185.8" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">astro</text>
            <text x="80.0" y="201.8" text-anchor="middle" font-size="11" font-weight="500" fill="#14663f">learned, allowed</text>
            <rect x="170" y="165" width="140" height="50" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="240.0" y="185.8" text-anchor="middle" font-size="15" font-weight="700" fill="#7a1820">doom-probe</text>
            <text x="240.0" y="201.8" text-anchor="middle" font-size="11" font-weight="500" fill="#7a1820">unknown → blocked</text>
            <rect x="350" y="165" width="90" height="50" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="395.0" y="195.2" text-anchor="middle" font-size="15" font-weight="700" fill="#3a4352">PC</text>
            <rect x="460" y="165" width="90" height="50" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="505.0" y="195.2" text-anchor="middle" font-size="15" font-weight="700" fill="#3a4352">switch</text>
            <rect x="570" y="165" width="80" height="50" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="610.0" y="194.6" text-anchor="middle" font-size="13" font-weight="700" fill="#3a4352">RADIUS</text>
            <line x1="440" y1="190" x2="451.0" y2="190.0" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="460,190 451.0,196.0 451.0,184.0" fill="#7c5cff"/>
            <line x1="550" y1="190" x2="561.0" y2="190.0" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="570,190 561.0,196.0 561.0,184.0" fill="#7c5cff"/>
            <text x="500" y="245" text-anchor="middle" font-size="12" font-weight="600" fill="#6b3a00">EAP over the cable, RADIUS to the server</text>
            <text x="160" y="245" text-anchor="middle" font-size="12" font-weight="600" fill="#14306b">unused ports: shut down</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In 802.1X, what is the switch's role?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The supplicant</li>
            <li class="quiz-option">B. The RADIUS server</li>
            <li class="quiz-option quiz-correct">C. The authenticator ✓</li>
            <li class="quiz-option">D. The doom-probe</li>
        </ul>
        <p class="quiz-explanation"><em>The PC is the supplicant, the switch is the authenticator, and RADIUS is the authentication server.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does <code>switchport port-security mac-address sticky</code> do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Blocks every MAC</li>
            <li class="quiz-option quiz-correct">B. Learns the MAC(s) that connect and keeps them as allowed ✓</li>
            <li class="quiz-option">C. Makes the port a trunk</li>
            <li class="quiz-option">D. Turns off port security</li>
        </ul>
        <p class="quiz-explanation"><em>Sticky learning adds the first allowed MACs to the configuration automatically.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which violation mode is the default, and what does it do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Protect: drops quietly</li>
            <li class="quiz-option">B. Restrict: drops and counts</li>
            <li class="quiz-option quiz-correct">C. Shutdown: err-disables the port ✓</li>
            <li class="quiz-option">D. Ignore</li>
        </ul>
        <p class="quiz-explanation"><em>The default is shutdown: the port goes err-disabled when an unknown MAC appears.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. IOS answers "Command rejected: ... is a dynamic port." What is missing?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. <code>switchport mode access</code> ✓</li>
            <li class="quiz-option">B. <code>no shutdown</code></li>
            <li class="quiz-option">C. A VLAN</li>
            <li class="quiz-option">D. PortFast</li>
        </ul>
        <p class="quiz-explanation"><em>Port security requires a static access (or trunk) port, not a dynamically negotiated one.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. In 802.1X, what is the switch's role?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Supplicant</li>
            <li class="quiz-option quiz-correct">B. Authenticator: it relays the login to the server and opens the port if approved ✓</li>
            <li class="quiz-option">C. Authentication server</li>
            <li class="quiz-option">D. Certificate authority</li>
        </ul>
        <p class="quiz-explanation"><em>The PC is the supplicant, the switch the authenticator, and the RADIUS server the authentication server.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why is 802.1X stronger than port security alone?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is faster</li>
            <li class="quiz-option quiz-correct">B. MAC addresses can be faked, but 802.1X needs real credentials ✓</li>
            <li class="quiz-option">C. It needs no server</li>
            <li class="quiz-option">D. It works without cables</li>
        </ul>
        <p class="quiz-explanation"><em>Port security trusts the MAC address, which is easy to spoof. 802.1X checks who the user or device really is.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> lock the crew desks to their PCs, let the hot-desk take two laptops, and switch off the ports nobody uses. <strong>Success:</strong> every crew device works, and Doom's probe can't reach anything.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g4-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm4', 'lesson' => 'lesson05']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
