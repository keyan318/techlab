{{-- M6 · Lesson 6.6: 802.1X, RADIUS & WPA3-Enterprise — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g6-l6.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="06">

    <h1 class="lesson-heading">Lesson 6.6: 802.1X, RADIUS & WPA3-Enterprise</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain the three roles in 802.1X, configure a switch for RADIUS authentication and dynamic VLANs, and choose secure wireless settings.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>802.1X</strong> is port-based network access control: nothing but login messages passes a port until the device proves who it is. Three roles:
    </p>
    <ul class="body-list">
        <li><strong>Supplicant</strong>: the laptop or phone that wants in.</li>
        <li><strong>Authenticator</strong>: the switch (or Wi-Fi access point). It relays the login and opens the port only if told to.</li>
        <li><strong>Authentication server</strong>: usually a <strong>RADIUS</strong> server (UDP 1812 for authentication, 1813 for accounting), which checks the credentials.</li>
    </ul>
    <p class="body-text">
        The login messages use <strong>EAP</strong> (Extensible Authentication Protocol), which comes in many methods. <strong>EAP-TLS</strong> uses certificates on both sides: the strongest, with no passwords to steal. <strong>PEAP</strong> wraps a username and password inside a TLS tunnel. RADIUS can also send back attributes, like which <strong>VLAN</strong> to put this user in: <strong>dynamic VLAN assignment</strong>.
    </p>
    <pre class="code-block"><code>aaa new-model
radius server GRID-RAD
 address ipv4 192.168.100.20 auth-port 1812 acct-port 1813
 key &lt;secret&gt;
aaa authentication dot1x default group radius
aaa authorization network default group radius
dot1x system-auth-control
interface g0/5
 switchport mode access
 authentication port-control auto
 dot1x pae authenticator</code></pre>
    <p class="body-text">
        <code>aaa authorization network</code> is what lets RADIUS assign the VLAN. <code>show authentication sessions</code> lists each port's user, status and VLAN.
    </p>
    <p class="body-text">
        Wireless uses the same idea. <strong>WPA2/WPA3-Personal</strong> uses one shared passphrase for everyone: anyone who knows it is in, and a leaver takes it with them. <strong>WPA3-Enterprise</strong> uses 802.1X, so every user logs in personally and can be locked out individually. WPA3 also protects against offline password guessing and requires protected management frames.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Which EAP method uses certificates on both sides?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. EAP-TLS ✓</li>
            <li class="quiz-option">B. PEAP</li>
            <li class="quiz-option">C. WPA2-PSK</li>
            <li class="quiz-option">D. RADIUS-1813</li>
        </ul>
        <p class="quiz-explanation"><em>EAP-TLS uses certificates on both sides: the strongest, with no passwords to steal. PEAP uses a password in a TLS tunnel.</em></p>
    </div>

    <h2 class="section-heading">Astro's Orders</h2>

    <p class="body-text">
        Port security stopped Doom's probe in Module 4, but it trusts MAC addresses, and those can be copied. <strong>Astro</strong> wants more. "From today, a hangar port asks <em>who</em> you are, not just which MAC you have. The RADIUS server at the Citadel decides, and puts each person in the right VLAN."
    </p>
    <p class="body-text">
        <strong>Volt</strong> has a second list: the hangar Wi-Fi. "One password, printed on the wall. Half of Earth's pilots know it."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why does Astro want more than port security?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Port security is too slow</li>
            <li class="quiz-option">B. The switch has no free ports</li>
            <li class="quiz-option quiz-correct">C. MAC addresses can be copied ✓</li>
            <li class="quiz-option">D. RADIUS replaced every VLAN</li>
        </ul>
        <p class="quiz-explanation"><em>Port security trusts MACs, and MACs can be faked. 802.1X asks who you are, not just which MAC you have.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 280" role="img" aria-label="802.1X: the laptop (supplicant) sends EAP to the switch (authenticator), which asks the RADIUS server; the server can assign a VLAN">
            <rect x="10" y="30" width="170" height="70" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="95.0" y="60.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">laptop</text>
            <text x="95.0" y="77.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">supplicant</text>
            <rect x="245" y="30" width="170" height="70" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="330.0" y="60.5" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">HANGAR-SW</text>
            <text x="330.0" y="77.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">authenticator</text>
            <rect x="480" y="30" width="170" height="70" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="565.0" y="60.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">GRID-RAD</text>
            <text x="565.0" y="77.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">RADIUS server</text>
            <line x1="180" y1="55" x2="236.0" y2="55.0" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="245,55 236.0,61.0 236.0,49.0" fill="#7c5cff"/>
            <line x1="245" y1="80" x2="189.0" y2="80.0" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="180,80 189.0,74.0 189.0,86.0" fill="#7c5cff"/>
            <text x="212" y="118" text-anchor="middle" font-size="11.5" font-weight="600" fill="#4a35b8">EAP</text>
            <line x1="415" y1="55" x2="471.0" y2="55.0" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="480,55 471.0,61.0 471.0,49.0" fill="#7c5cff"/>
            <line x1="480" y1="80" x2="424.0" y2="80.0" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="415,80 424.0,74.0 424.0,86.0" fill="#7c5cff"/>
            <text x="447" y="118" text-anchor="middle" font-size="11.5" font-weight="600" fill="#4a35b8">RADIUS 1812</text>
            <rect x="10" y="145" width="310" height="125" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="165.0" y="179.4" text-anchor="middle" font-size="14" font-weight="700" fill="#6b3a00">The answer</text>
            <text x="165.0" y="195.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">Access-Accept + VLAN 10</text>
            <text x="165.0" y="212.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">→ port opens in VLAN 10</text>
            <text x="165.0" y="228.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">Access-Reject</text>
            <text x="165.0" y="245.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">→ port stays closed</text>
            <rect x="340" y="145" width="310" height="125" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="495.0" y="179.4" text-anchor="middle" font-size="14" font-weight="700" fill="#3a4352">Wi-Fi does the same</text>
            <text x="495.0" y="195.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">WPA3-Enterprise = 802.1X</text>
            <text x="495.0" y="212.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">each user logs in personally</text>
            <text x="495.0" y="228.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">EAP-TLS: certificates on</text>
            <text x="495.0" y="245.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">both sides, no passwords</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what happens on Access-Reject?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It opens in VLAN 10</li>
            <li class="quiz-option">B. It joins the guest VLAN</li>
            <li class="quiz-option quiz-correct">C. The port stays closed ✓</li>
            <li class="quiz-option">D. It retries with a MAC</li>
        </ul>
        <p class="quiz-explanation"><em>Access-Accept opens the port (maybe in a set VLAN). Access-Reject keeps it closed.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In 802.1X, which device is the authenticator?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The laptop</li>
            <li class="quiz-option quiz-correct">B. The switch or access point ✓</li>
            <li class="quiz-option">C. The RADIUS server</li>
            <li class="quiz-option">D. The CA</li>
        </ul>
        <p class="quiz-explanation"><em>The authenticator controls the port and relays EAP between the supplicant and the authentication server.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which UDP port does RADIUS use for authentication?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 53</li>
            <li class="quiz-option">B. 443</li>
            <li class="quiz-option quiz-correct">C. 1812 ✓</li>
            <li class="quiz-option">D. 3389</li>
        </ul>
        <p class="quiz-explanation"><em>RADIUS uses UDP 1812 for authentication and 1813 for accounting.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which EAP method uses certificates on both client and server, with no passwords?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. PEAP</li>
            <li class="quiz-option quiz-correct">B. EAP-TLS ✓</li>
            <li class="quiz-option">C. EAP-MD5</li>
            <li class="quiz-option">D. PAP</li>
        </ul>
        <p class="quiz-explanation"><em>EAP-TLS authenticates both sides with certificates.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. How does the switch know which VLAN to put an authenticated user in?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It guesses</li>
            <li class="quiz-option quiz-correct">B. The RADIUS server returns the VLAN as an attribute (dynamic VLAN assignment) ✓</li>
            <li class="quiz-option">C. From DHCP</li>
            <li class="quiz-option">D. From the MAC address only</li>
        </ul>
        <p class="quiz-explanation"><em>With authorization enabled, RADIUS can send the VLAN for that user, and the switch applies it to the port.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why is WPA3-Enterprise better than a shared Wi-Fi passphrase for the crew?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is faster</li>
            <li class="quiz-option quiz-correct">B. Each user logs in with their own credentials and can be removed individually ✓</li>
            <li class="quiz-option">C. It needs no server</li>
            <li class="quiz-option">D. It has no encryption</li>
        </ul>
        <p class="quiz-explanation"><em>Enterprise mode uses 802.1X, so there is no shared secret to leak and access is per user.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> point the hangar switch at the Grid's RADIUS server, turn on 802.1X on a crew port, and pick secure settings for the hangar Wi-Fi. <strong>Success:</strong> the crew laptop is authorized into VLAN 10 and Doom's laptop stays outside.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g6-l6" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm6', 'lesson' => 'lesson06']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
