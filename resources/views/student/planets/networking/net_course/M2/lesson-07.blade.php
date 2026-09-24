{{-- M2 · Lesson 2.7: Midterm Mission — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m2-l7.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="07">

    <h1 class="lesson-heading">Lesson 2.7: Midterm Mission</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can plan an address scheme, configure every device and gateway, and prove the network works by reaching a remote server.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        This mission brings together everything from Networking Protocols. Use this checklist:
    </p>
    <ul class="body-list">
        <li><strong>Layers</strong>: a problem lives in one layer, so work from the bottom (link, address, port, service).</li>
        <li><strong>Standards</strong>: services use well-known ports, such as web on 80.</li>
        <li><strong>Private IPv4</strong>: use <code>192.168.0.0/16</code> inside the ship.</li>
        <li><strong>Subnetting</strong>: a <code>/25</code> has 128 addresses and 126 usable hosts. Subnets start at .0 and .128.</li>
        <li><strong>Gateways</strong>: every host needs a default gateway that sits on its own subnet.</li>
        <li><strong>Routes</strong>: traffic needs a route there and a route back.</li>
    </ul>
    <p class="body-text">
        Plan first, then configure, then test. Check each device with <code>ipconfig</code> and <code>route print</code> before you ping.
    </p>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        Planet Doom's jamming ship is directly overhead. <strong>Volt</strong> reads the shield meter. "Forty percent and dropping. We have one clear window."
    </p>
    <p class="body-text">
        <strong>Astro</strong> makes the call. "This is the moment. Build the network, bring the decks up, and send our first message to Earth." <strong>Rivet</strong> is already at the router. "The router is blank. Every address, every gateway. Let's go."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 244" role="img" aria-label="The midterm mission network: two decks, a router and Earth">
            <rect x="10" y="20" width="170" height="70" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="95.0" y="52.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Comms deck</text>
            <text x="95.0" y="71.0" text-anchor="middle" font-size="12.5" fill="#14306b">192.168.20.0/25</text>
            <text x="95" y="112" text-anchor="middle" font-size="12" font-weight="500" fill="#14306b">astro .10   rivet .20</text>
            <rect x="10" y="140" width="170" height="70" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="95.0" y="172.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">Shield deck</text>
            <text x="95.0" y="191.0" text-anchor="middle" font-size="12.5" fill="#6b3a00">192.168.20.128/25</text>
            <text x="95" y="232" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">volt .140</text>
            <rect x="270" y="90" width="130" height="70" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="335.0" y="122.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">gate</text>
            <text x="335.0" y="141.0" text-anchor="middle" font-size="12.5" fill="#3b2a8a">the router</text>
            <rect x="510" y="90" width="140" height="70" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="580.0" y="122.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">Earth relay</text>
            <text x="580.0" y="141.0" text-anchor="middle" font-size="12.5" fill="#14663f">203.0.113.10/24</text>
            <line x1="180" y1="55" x2="270" y2="115" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="180" y1="175" x2="270" y2="135" stroke="#f08c1a" stroke-width="3" stroke-linecap="round"/>
            <line x1="400" y1="125" x2="510" y2="125" stroke="#2fa56b" stroke-width="3" stroke-linecap="round"/>
            <text x="240" y="72" text-anchor="middle" font-size="12" font-weight="700" fill="#4a35b8">.1</text>
            <text x="240" y="168" text-anchor="middle" font-size="12" font-weight="700" fill="#6b3a00">.129</text>
            <text x="455" y="112" text-anchor="middle" font-size="11.5" font-weight="700" fill="#14663f">203.0.113.1</text>
            <text x="330" y="14" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">Your mission map</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. A ping from astro reaches the far router, but the reply never comes back. What is the most likely missing piece?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. A return route on the far side ✓</li>
            <li class="quiz-option">B. A longer cable</li>
            <li class="quiz-option">C. A new hostname</li>
            <li class="quiz-option">D. A bigger switch</li>
        </ul>
        <p class="quiz-explanation"><em>Traffic needs a route there and a route back. If the far side has no route to the source, the reply is lost.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which host address is valid in 192.168.20.128/25?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 192.168.20.10</li>
            <li class="quiz-option">B. 192.168.20.127</li>
            <li class="quiz-option quiz-correct">C. 192.168.20.140 ✓</li>
            <li class="quiz-option">D. 192.168.20.255</li>
        </ul>
        <p class="quiz-explanation"><em>The subnet runs from .128 (network) to .255 (broadcast). Usable hosts are .129 to .254, so .140 is valid. .127 belongs to the first subnet.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which range is private IPv4 space?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 172.32.0.0/16</li>
            <li class="quiz-option quiz-correct">B. 192.168.0.0/16 ✓</li>
            <li class="quiz-option">C. 8.0.0.0/8</li>
            <li class="quiz-option">D. 203.0.113.0/24</li>
        </ul>
        <p class="quiz-explanation"><em>192.168.0.0/16 is private. 172.16.0.0/12 is private, but 172.32.0.0 is outside it.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> build the ship network from a blank router and call Earth. <strong>Success:</strong> every device is addressed in its subnet, every host has its gateway, astro reaches volt, volt reaches Earth, and astro receives Earth's reply.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m2-l7" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm2', 'lesson' => 'lesson07']) }}">
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
