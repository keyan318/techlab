{{-- M1 · Lesson 1.6: Cloud Relay — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m1-l6.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="06">

    <h1 class="lesson-heading">Lesson 1.6: Cloud Relay</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can define cloud computing, tell IaaS, PaaS and SaaS apart, and reach a remote server through a router by setting default routes.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Cloud computing</strong> means renting computing power, storage or software over the Internet instead of owning the hardware. It is <strong>on demand</strong>, it can <strong>scale</strong> up or down, and you usually pay only for what you use.
    </p>
    <p class="body-text">
        There are three service models, from most control to least:
    </p>
    <ul class="body-list">
        <li><strong>IaaS</strong> (Infrastructure as a Service): you rent raw servers, storage and networks and manage the operating system and apps yourself.</li>
        <li><strong>PaaS</strong> (Platform as a Service): you rent a ready platform and only bring your code. The provider runs the operating system.</li>
        <li><strong>SaaS</strong> (Software as a Service): you use finished software, such as email or online documents.</li>
    </ul>
    <p class="body-text">
        Clouds can be <strong>public</strong> (shared by many customers), <strong>private</strong> (one organization only) or <strong>hybrid</strong> (a mix).
    </p>
    <p class="body-text">
        To reach a cloud your traffic leaves your LAN through the <strong>default gateway</strong>, crosses the WAN, and arrives at the remote server. The remote server also needs a route back, or its reply is lost.
    </p>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        <strong>Astro</strong> unrolls the last piece of the beacon plan. "Earth keeps the fleet's star charts in a cloud relay. If we can reach it, we can reach home."
    </p>
    <p class="body-text">
        <strong>Volt</strong> raises a warning. "Then the road home must not depend on radio, or the jammer will cut it." <strong>Rivet</strong> agrees. "Wired to the router, then out over the long link."
    </p>
    <p class="body-text">
        There is one catch, and it is a classic. A message needs a route <em>out</em>, and the reply needs a route <em>back</em>.
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 210" role="img" aria-label="The ship's LAN reaching the Earth Cloud across a WAN through a router">
            <text x="330" y="30" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">The request goes out, and the reply must find its way back</text>
            <rect x="10" y="62" width="130" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="75.0" y="89.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">astro</text>
            <text x="75.0" y="108.0" text-anchor="middle" font-size="12.5" fill="#14306b">192.168.1.10</text>
            <rect x="215" y="62" width="110" height="60" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="270.0" y="89.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">gate</text>
            <text x="270.0" y="108.0" text-anchor="middle" font-size="12.5" fill="#3b2a8a">router</text>
            <ellipse cx="415" cy="92" rx="52" ry="34" fill="#eef4ff" stroke="#9db7e8" stroke-width="2" stroke-dasharray="6 5"/>
            <text x="415" y="89" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">WAN</text>
            <text x="415" y="107" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">long link</text>
            <rect x="510" y="62" width="140" height="60" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="580.0" y="89.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">Earth Cloud</text>
            <text x="580.0" y="108.0" text-anchor="middle" font-size="12.5" fill="#14663f">203.0.113.10</text>
            <line x1="140" y1="92" x2="215" y2="92" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="325" y1="92" x2="363" y2="92" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="467" y1="92" x2="510" y2="92" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <text x="75" y="158" text-anchor="middle" font-size="12.5" font-weight="600" fill="#4a35b8">astro's default route</text>
            <text x="75" y="176" text-anchor="middle" font-size="12.5" font-weight="700" fill="#4a35b8">via 192.168.1.1</text>
            <text x="580" y="158" text-anchor="middle" font-size="12.5" font-weight="600" fill="#14663f">cloud's default route</text>
            <text x="580" y="176" text-anchor="middle" font-size="12.5" font-weight="700" fill="#14663f">via 203.0.113.1</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Renting a ready-made platform where you only bring your own code is called:</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. IaaS</li>
            <li class="quiz-option quiz-correct">B. PaaS ✓</li>
            <li class="quiz-option">C. SaaS</li>
            <li class="quiz-option">D. LAN</li>
        </ul>
        <p class="quiz-explanation"><em>PaaS gives you the platform, and you supply the code. IaaS gives raw machines, and SaaS gives finished software.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Using online email in your browser is an example of:</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. IaaS</li>
            <li class="quiz-option">B. PaaS</li>
            <li class="quiz-option quiz-correct">C. SaaS ✓</li>
            <li class="quiz-option">D. A hub</li>
        </ul>
        <p class="quiz-explanation"><em>SaaS is finished software you simply use, like webmail or online documents.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Astro can send a request to the cloud but never gets a reply. What is the most likely cause?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The cloud has no route back to astro ✓</li>
            <li class="quiz-option">B. Astro's screen is too small</li>
            <li class="quiz-option">C. The cable is too colorful</li>
            <li class="quiz-option">D. Clouds never reply</li>
        </ul>
        <p class="quiz-explanation"><em>Both directions need a route. If the remote server has no route back, the reply is lost.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> reach the Earth Cloud across the router. <strong>Success:</strong> astro fetches the Earth Cloud page. Watch what happens if only one side has a route.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m1-l6" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson06']) }}">
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
