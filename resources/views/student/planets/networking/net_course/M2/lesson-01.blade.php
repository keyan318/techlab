{{-- M2 · Lesson 2.1: Protocol Models — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m2-l1.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="01">

    <h1 class="lesson-heading">Lesson 2.1: Protocol Models</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can name the layers of the OSI and TCP/IP models, say what each layer does, and explain encapsulation.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>protocol</strong> is a set of rules for communicating. Networking is too big to solve in one piece, so it is split into <strong>layers</strong>. Each layer has one job and talks only to the layers above and below it.
    </p>
    <p class="body-text">
        The <strong>OSI model</strong> has 7 layers. From the bottom: <strong>1 Physical</strong> (signals on the medium), <strong>2 Data Link</strong> (frames, MAC addresses, switches), <strong>3 Network</strong> (IP addresses, routers), <strong>4 Transport</strong> (TCP, UDP, ports), <strong>5 Session</strong>, <strong>6 Presentation</strong> and <strong>7 Application</strong> (HTTP, DNS). A memory trick from the top: <em>All People Seem To Need Data Processing</em>.
    </p>
    <p class="body-text">
        The <strong>TCP/IP model</strong> is what the Internet really uses. It squeezes the same ideas into 4 layers: Application, Transport, Internet and Network Access.
    </p>
    <p class="body-text">
        When you send data it moves <em>down</em> the stack. Each layer wraps the data with its own header. This wrapping is called <strong>encapsulation</strong>. The receiver unwraps it on the way <em>up</em>. The unit of data has a different name at each layer: bits, frames, packets, segments.
    </p>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        Earth's relay does not speak one language. It answers in <strong>layers</strong>, like a stack of envelopes with a letter inside the last one.
    </p>
    <p class="body-text">
        <strong>Volt</strong> studies the relay's signal. "Every fault lives in exactly one layer. Find the layer and you have found the fix." <strong>Astro</strong> nods. "Then we build the ship's server from the bottom up: cable first, address second, port third, service last."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 292" role="img" aria-label="The OSI model and the TCP/IP model side by side">
            <text x="120" y="20" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">OSI model (7 layers)</text>
            <text x="395" y="20" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">TCP/IP model (4 layers)</text>
            <text x="575" y="20" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">Examples</text>
            <rect x="20" y="30" width="200" height="32" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="120.0" y="51.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">7  Application</text>
            <rect x="20" y="66" width="200" height="32" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="120.0" y="87.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">6  Presentation</text>
            <rect x="20" y="102" width="200" height="32" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="120.0" y="123.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">5  Session</text>
            <rect x="20" y="138" width="200" height="32" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="120.0" y="159.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">4  Transport</text>
            <rect x="20" y="174" width="200" height="32" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="120.0" y="195.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">3  Network</text>
            <rect x="20" y="210" width="200" height="32" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="120.0" y="231.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">2  Data Link</text>
            <rect x="20" y="246" width="200" height="32" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="120.0" y="267.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">1  Physical</text>
            <rect x="300" y="30" width="190" height="104" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="395.0" y="79.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Application</text>
            <text x="395.0" y="98.0" text-anchor="middle" font-size="12.5" fill="#14306b">layers 7, 6, 5</text>
            <rect x="300" y="138" width="190" height="32" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="395.0" y="159.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">Transport</text>
            <rect x="300" y="174" width="190" height="32" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="395.0" y="195.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">Internet</text>
            <rect x="300" y="210" width="190" height="68" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="395.0" y="241.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">Network Access</text>
            <text x="395.0" y="260.0" text-anchor="middle" font-size="12.5" fill="#3b2a8a">layers 2, 1</text>
            <text x="575" y="86" text-anchor="middle" font-size="12.5" font-weight="600" fill="#33406b">HTTP, DNS</text>
            <text x="575" y="158" text-anchor="middle" font-size="12.5" font-weight="600" fill="#33406b">TCP, UDP</text>
            <text x="575" y="194" text-anchor="middle" font-size="12.5" font-weight="600" fill="#33406b">IP, routers</text>
            <text x="575" y="248" text-anchor="middle" font-size="12.5" font-weight="600" fill="#33406b">Ethernet, switch, cable</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. A router makes its forwarding decisions at which OSI layer?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Layer 1, Physical</li>
            <li class="quiz-option">B. Layer 2, Data Link</li>
            <li class="quiz-option quiz-correct">C. Layer 3, Network ✓</li>
            <li class="quiz-option">D. Layer 7, Application</li>
        </ul>
        <p class="quiz-explanation"><em>Routers read IP addresses, and IP lives at Layer 3, the Network layer. Switches work at Layer 2.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is encapsulation?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Wrapping data in a new header at each layer as it moves down the stack ✓</li>
            <li class="quiz-option">B. Encrypting every message</li>
            <li class="quiz-option">C. Copying a file to two servers</li>
            <li class="quiz-option">D. Removing the cable</li>
        </ul>
        <p class="quiz-explanation"><em>Each layer adds its own header around the data on the way down. The receiver removes them one by one on the way up.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. In the TCP/IP model, which layer handles TCP and UDP?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Application</li>
            <li class="quiz-option quiz-correct">B. Transport ✓</li>
            <li class="quiz-option">C. Internet</li>
            <li class="quiz-option">D. Network Access</li>
        </ul>
        <p class="quiz-explanation"><em>TCP and UDP are Transport protocols. They provide ports and, for TCP, reliable delivery.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> bring <code>ship-server</code> to life one layer at a time. <strong>Success:</strong> astro can ping the server (Layers 1 to 3), TCP port 80 is open (Layer 4), and astro can fetch the web page (Layer 7).
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m2-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm2', 'lesson' => 'lesson01']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. You use Linux commands there; the ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
