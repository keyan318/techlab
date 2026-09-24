{{-- M4 · Lesson 4.6: Final Mission: Call Earth — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m4-l6.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="06">

    <h1 class="lesson-heading">Lesson 4.6: Final Mission: Call Earth</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can apply the whole course: diagnose a broken network layer by layer, fix every fault, and get a message from the ship to Earth.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        This is the last mission. The ship's network was damaged in the raids, and <strong>several things are broken at once</strong>. There is no single fix. Use everything you have learned:
    </p>
    <ul class="body-list">
        <li><strong>Method</strong>: identify, theorize, test, fix one thing, verify, document.</li>
        <li><strong>Layers</strong>: start at the bottom. A cable problem hides everything above it.</li>
        <li><strong>Addressing and DHCP</strong>: a lease needs an address, a gateway and a name server, and the pool must be big enough.</li>
        <li><strong>Routing</strong>: traffic needs a route there and a route back.</li>
        <li><strong>Services</strong>: DNS must know the name, and the web server must answer.</li>
    </ul>
    <p class="body-text">
        Work in four steps: Physical, Addressing, Routing, Services. After each fix, run the test again before you move up. Some fixes are in the Command Prompt and some are in the Inspector panels.
    </p>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        This is the window. Planet Doom's fleet is breaking up the ship's signal, and the shield meter reads thirty percent. <strong>Volt</strong> holds the shields. <strong>Rivet</strong> is at the router. <strong>Astro</strong> takes the console.
    </p>
    <p class="body-text">
        "Everything we learned has led here," says Astro. "Fix the network from the bottom up. When the page loads, it is Earth talking to us."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 172" role="img" aria-label="The final mission: four layers, six faults, worked bottom-up">
            <text x="330" y="18" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">Six faults hide in four layers. Work from the bottom up.</text>
            <rect x="10" y="34" width="145" height="92" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="82" y="60" text-anchor="middle" font-size="22" font-weight="800" fill="#b3261e">1</text>
            <text x="82" y="82" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Physical</text>
            <text x="82" y="102" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">link light</text>
            <text x="82" y="117" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">netsh interface</text>
            <line x1="155" y1="80" x2="169" y2="80" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="175,80 164,74 164,86" fill="#7c5cff"/>
            <rect x="175" y="34" width="145" height="92" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="247" y="60" text-anchor="middle" font-size="22" font-weight="800" fill="#f08c1a">2</text>
            <text x="247" y="82" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Addressing</text>
            <text x="247" y="102" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">pool size, router</text>
            <text x="247" y="117" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">and DNS options</text>
            <line x1="320" y1="80" x2="334" y2="80" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="340,80 329,74 329,86" fill="#7c5cff"/>
            <rect x="340" y="34" width="145" height="92" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="412" y="60" text-anchor="middle" font-size="22" font-weight="800" fill="#2f6fe0">3</text>
            <text x="412" y="82" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Routing</text>
            <text x="412" y="102" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">default routes</text>
            <text x="412" y="117" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">and return path</text>
            <line x1="485" y1="80" x2="499" y2="80" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="505,80 494,74 494,86" fill="#7c5cff"/>
            <rect x="505" y="34" width="145" height="92" rx="14" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="577" y="60" text-anchor="middle" font-size="22" font-weight="800" fill="#2fa56b">4</text>
            <text x="577" y="82" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Services</text>
            <text x="577" y="102" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">DNS record</text>
            <text x="577" y="117" text-anchor="middle" font-size="11.5" font-weight="500" fill="#33406b">and web server</text>
            <text x="330" y="156" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">Test after every fix. One fault can hide another.</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Two consoles ask DHCP for an address, but only one gets one, and the pool holds a single address. What is the fix?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Reboot the switch</li>
            <li class="quiz-option quiz-correct">B. Enlarge the DHCP address pool ✓</li>
            <li class="quiz-option">C. Change the cable</li>
            <li class="quiz-option">D. Rename the SSID</li>
        </ul>
        <p class="quiz-explanation"><em>An exhausted pool gives no offer to the extra clients. Widening the range gives everyone an address.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. A console has an address but cannot reach other networks, and DHCP gave it no gateway. What was missing from the lease?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The router (default gateway) option ✓</li>
            <li class="quiz-option">B. The lease time</li>
            <li class="quiz-option">C. The pool start</li>
            <li class="quiz-option">D. The MAC address</li>
        </ul>
        <p class="quiz-explanation"><em>The router option tells clients where to send traffic for other networks.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is the last step of the troubleshooting method?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Identify the problem</li>
            <li class="quiz-option">B. Test the theory</li>
            <li class="quiz-option quiz-correct">C. Document findings, actions and outcome ✓</li>
            <li class="quiz-option">D. Implement the solution</li>
        </ul>
        <p class="quiz-explanation"><em>Finish by documenting what you found, what you did and how it turned out, so the next person can learn from it.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> get the crew online and send the distress call. <strong>Success:</strong> rivet is back on the network, astro and volt lease addresses with a gateway, both reach Earth, and astro reads Earth's reply at <code>http://earth.relay/</code>.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m4-l6" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm4', 'lesson' => 'lesson06']) }}">
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
