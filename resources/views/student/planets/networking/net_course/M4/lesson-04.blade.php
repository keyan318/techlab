{{-- M4 · Lesson 4.4: Wireless Issues — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m4-l4.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="04">

    <h1 class="lesson-heading">Lesson 4.4: Wireless Issues</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can name the common wireless problems, tell radio-side faults from wired-side faults, and fix a Wi-Fi client that is connected but has no network.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Wi-Fi problems come from two places: the <strong>radio side</strong> (the air between the client and the access point) and the <strong>wired side</strong> (everything behind the access point).
    </p>
    <p class="body-text">
        <strong>Radio side.</strong> The client is too far away or blocked by walls (weak signal). Other Wi-Fi, microwaves or a jammer add <strong>interference</strong>. On the 2.4 GHz band only channels <strong>1, 6 and 11</strong> do not overlap, so neighbours on the same or overlapping channels slow each other down. The <strong>SSID</strong>, <strong>password</strong> or <strong>security mode</strong> (WPA2, WPA3) may be wrong. Too many clients can overload one access point. Remember the bands: <strong>2.4 GHz</strong> reaches farther but is slower, and <strong>5 GHz</strong> is faster but reaches less far.
    </p>
    <p class="body-text">
        <strong>Wired side.</strong> The access point is only a bridge to the wired network. If its <strong>uplink cable</strong> or switch port is down, if that port is in the wrong <strong>VLAN</strong>, or if <strong>DHCP</strong> has no addresses left, then a laptop shows "connected" to Wi-Fi yet gets no address or no internet.
    </p>
    <p class="body-text">
        A quick test: if the client <strong>cannot even join</strong> the network, suspect the radio side. If it <strong>joins but cannot get an address or reach anything</strong>, suspect the wired side. <em>The lab simulator models the wired side of a Wi-Fi network (the access point behaves as a bridge), not signal strength or interference, so the lab practises the wired-side faults.</em>
    </p>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        The crew's laptop and tablet show the <code>codexia-crew</code> Wi-Fi as connected, and neither one works. <strong>Volt</strong> checks the radio. "Signal is strong, no jamming on this channel. The radio side is clean."
    </p>
    <p class="body-text">
        <strong>Rivet</strong> follows the cable instead. "Then the fault is behind the access point. Is its uplink on the right VLAN, and does the DHCP server have addresses left?"
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 226" role="img" aria-label="Wireless faults on the radio side and on the wired side">
            <text x="125" y="20" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">Radio side (in the air)</text>
            <text x="535" y="20" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">Wired side (behind the AP)</text>
            <rect x="10" y="34" width="230" height="42" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="125.0" y="59.5" text-anchor="middle" font-size="13" font-weight="700" fill="#3b2a8a">Signal strength, distance</text>
            <rect x="10" y="86" width="230" height="42" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="125.0" y="111.5" text-anchor="middle" font-size="13" font-weight="700" fill="#3b2a8a">Interference, channels</text>
            <rect x="10" y="138" width="230" height="42" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="125.0" y="163.5" text-anchor="middle" font-size="13" font-weight="700" fill="#3b2a8a">SSID, password, security</text>
            <rect x="420" y="34" width="230" height="42" rx="10" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="535.0" y="59.5" text-anchor="middle" font-size="13" font-weight="700" fill="#14663f">AP uplink cable and port</text>
            <rect x="420" y="86" width="230" height="42" rx="10" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="535.0" y="111.5" text-anchor="middle" font-size="13" font-weight="700" fill="#14663f">VLAN of the AP's port</text>
            <rect x="420" y="138" width="230" height="42" rx="10" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="535.0" y="163.5" text-anchor="middle" font-size="13" font-weight="700" fill="#14663f">DHCP pool, DNS, gateway</text>
            <rect x="275" y="76" width="110" height="64" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="330.0" y="105.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Access point</text>
            <text x="330.0" y="124.0" text-anchor="middle" font-size="12.5" fill="#14306b">wireless bridge</text>
            <line x1="240" y1="108" x2="275" y2="108" stroke="#7c5cff" stroke-width="3" stroke-dasharray="7 5"/>
            <line x1="385" y1="108" x2="420" y2="108" stroke="#2fa56b" stroke-width="3" stroke-linecap="round"/>
            <text x="330" y="212" text-anchor="middle" font-size="13" font-weight="700" fill="#14663f">"Connected, no internet" is often the wired side</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. A laptop shows it is connected to Wi-Fi but has no IP address. Which side is most likely at fault?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The wired side: DHCP, the AP's VLAN or its uplink ✓</li>
            <li class="quiz-option">B. The color of the laptop</li>
            <li class="quiz-option">C. The keyboard layout</li>
            <li class="quiz-option">D. The screen brightness</li>
        </ul>
        <p class="quiz-explanation"><em>Being connected means the radio link works. Not getting an address points at what is behind the access point, such as DHCP or the VLAN.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which 2.4 GHz channels do not overlap?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 1, 2 and 3</li>
            <li class="quiz-option quiz-correct">B. 1, 6 and 11 ✓</li>
            <li class="quiz-option">C. 5, 6 and 7</li>
            <li class="quiz-option">D. 2, 4 and 8</li>
        </ul>
        <p class="quiz-explanation"><em>Channels 1, 6 and 11 are spaced far enough apart not to overlap, so networks on them do not interfere.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which band reaches farther but is slower?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 5 GHz</li>
            <li class="quiz-option quiz-correct">B. 2.4 GHz ✓</li>
            <li class="quiz-option">C. 10 GHz</li>
            <li class="quiz-option">D. 1 GHz</li>
        </ul>
        <p class="quiz-explanation"><em>2.4 GHz passes through walls better and reaches farther, but it is slower and more crowded than 5 GHz.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> get the laptop and the tablet online through the access point. <strong>Success:</strong> both get addresses in <code>192.168.1.0/24</code>, the laptop reaches the server, and the laptop and tablet reach each other.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m4-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm4', 'lesson' => 'lesson04']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. You type Windows commands there (Command Prompt), like on a school PC; the ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
