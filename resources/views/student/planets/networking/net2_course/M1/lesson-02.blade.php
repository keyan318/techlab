{{-- M1 · Lesson 1.2: Cloud Computing in Networking — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g1-l2.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="02">

    <h1 class="lesson-heading">Lesson 1.2: Cloud Computing in Networking</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can tell IaaS, PaaS and SaaS apart, name the cloud deployment models, and explain what the network has to do to reach a cloud.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Cloud computing</strong> means renting computing power over a network instead of owning it all. You ask for a server, storage or an app, you get it in minutes, and you pay for what you use. The official definition (NIST) lists five traits: on-demand self-service, broad network access, shared resource pools, rapid elasticity (grow and shrink fast) and measured service.
    </p>
    <p class="body-text">
        Clouds come in three <strong>service models</strong>. The difference is who looks after what:
    </p>
    <ul class="body-list">
        <li><strong>IaaS</strong> (Infrastructure as a Service): you rent virtual machines, networks and storage. You install and patch the operating system and everything on it.</li>
        <li><strong>PaaS</strong> (Platform as a Service): the provider runs the servers and the operating system. You bring your code and data.</li>
        <li><strong>SaaS</strong> (Software as a Service): a finished app, like web mail. You just sign in.</li>
    </ul>
    <p class="body-text">
        This split is called the <strong>shared responsibility model</strong>: the more the provider manages, the less you patch, but the less you control.
    </p>
    <p class="body-text">
        And four <strong>deployment models</strong>: a <strong>public</strong> cloud is shared with other customers, a <strong>private</strong> cloud serves one organization, a <strong>community</strong> cloud serves a group with shared needs, and a <strong>hybrid</strong> cloud links your own private systems to a public cloud. Under all of them sits <strong>virtualization</strong>: a hypervisor runs many virtual machines on one physical server.
    </p>
    <p class="body-text">
        For the network, the cloud is just another destination, but an important one. The cloud's virtual network (often called a <strong>VPC</strong>) has its own addresses, and your routers need a route to it, usually through an encrypted <strong>VPN tunnel</strong> or a dedicated link. If that path is slow or down, the cloud is too.
    </p>
    <pre class="code-block"><code>ip route 10.200.0.0 255.255.0.0 172.31.0.2
! &quot;to reach the cloud network, send it into the tunnel&quot;</code></pre>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">In IaaS, who installs and patches the operating system?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The provider does it all</li>
            <li class="quiz-option">B. Nobody, since it is virtual</li>
            <li class="quiz-option quiz-correct">C. You do, as the customer ✓</li>
            <li class="quiz-option">D. The provider's own hypervisor</li>
        </ul>
        <p class="quiz-explanation"><em>In IaaS you rent virtual machines and storage, and you install and patch the OS and everything on it.</em></p>
    </div>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        Earth's next message is an offer: rent space on <strong>Earth Cloud</strong> instead of building every server on Codexia. <strong>Rivet</strong> likes it. "We keep the Citadel's servers, we rent the rest, and we join the two at Skyport. That's a hybrid cloud."
    </p>
    <p class="body-text">
        <strong>Volt</strong> is less sure. "Renting a server doesn't mean Earth guards it. Read the small print, Cadet: who patches what?" The Skyport edge router already has a tunnel to Earth Cloud, but no route through it.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Rivet keeps the Citadel's servers and rents the rest. What is that?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A public cloud only</li>
            <li class="quiz-option">B. A community cloud</li>
            <li class="quiz-option quiz-correct">C. A hybrid cloud ✓</li>
            <li class="quiz-option">D. A private cloud only</li>
        </ul>
        <p class="quiz-explanation"><em>A hybrid cloud links your own private systems to a public cloud. Here they join at Skyport.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 270" role="img" aria-label="The Grid linked to Earth Cloud: a hybrid cloud, with who manages what in IaaS, PaaS and SaaS">
            <rect x="10" y="30" width="180" height="80" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="100.0" y="56.8" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">The Grid</text>
            <text x="100.0" y="74.2" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14306b">Citadel servers</text>
            <text x="100.0" y="91.8" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14306b">private, ours</text>
            <rect x="470" y="30" width="180" height="80" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="560.0" y="56.8" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">Earth Cloud</text>
            <text x="560.0" y="74.2" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14663f">shared data centers</text>
            <text x="560.0" y="91.8" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14663f">public, rented</text>
            <rect x="250" y="45" width="160" height="50" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="330.0" y="65.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#3b2a8a">SKYPORT-EDGE</text>
            <text x="330.0" y="81.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">router + VPN tunnel</text>
            <line x1="190" y1="70" x2="250" y2="70" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="410" y1="70" x2="470" y2="70" stroke="#7c5cff" stroke-width="3" stroke-linecap="round" stroke-dasharray="7 6"/>
            <text x="330" y="30" text-anchor="middle" font-size="11.5" font-weight="600" fill="#4a35b8">together: a hybrid cloud</text>
            <rect x="10" y="140" width="200" height="120" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="110.0" y="180.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">IaaS</text>
            <text x="110.0" y="196.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">Earth: hardware, network</text>
            <text x="110.0" y="213.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">You: OS, apps, data</text>
            <text x="110.0" y="229.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">like renting an empty flat</text>
            <rect x="230" y="140" width="200" height="120" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="330.0" y="180.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">PaaS</text>
            <text x="330.0" y="196.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">Earth: hardware + OS</text>
            <text x="330.0" y="213.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">You: your code, data</text>
            <text x="330.0" y="229.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">like a furnished flat</text>
            <rect x="450" y="140" width="200" height="120" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="550.0" y="180.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">SaaS</text>
            <text x="550.0" y="196.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">Earth: everything</text>
            <text x="550.0" y="213.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">You: just use it</text>
            <text x="550.0" y="229.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">like a hotel room</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, which model is “like a hotel room”?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. SaaS ✓</li>
            <li class="quiz-option">B. IaaS</li>
            <li class="quiz-option">C. PaaS</li>
            <li class="quiz-option">D. VPC</li>
        </ul>
        <p class="quiz-explanation"><em>In SaaS the provider runs everything. You just use it, like a hotel room.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Earth rents you virtual machines and networks; you install and patch the operating system. Which model is that?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. SaaS</li>
            <li class="quiz-option">B. PaaS</li>
            <li class="quiz-option quiz-correct">C. IaaS ✓</li>
            <li class="quiz-option">D. Private cloud</li>
        </ul>
        <p class="quiz-explanation"><em>In IaaS you get the infrastructure (virtual machines, networks, storage) and you manage everything from the operating system up.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. You sign in to a finished web mail app and never touch a server. Which model?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. SaaS ✓</li>
            <li class="quiz-option">B. IaaS</li>
            <li class="quiz-option">C. PaaS</li>
            <li class="quiz-option">D. Hybrid</li>
        </ul>
        <p class="quiz-explanation"><em>Software as a Service: the provider runs the whole app. You only use it.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. The Grid keeps its own servers at the Citadel and also uses Earth Cloud, linked together. Which deployment model is that?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Public cloud</li>
            <li class="quiz-option">B. Community cloud</li>
            <li class="quiz-option">C. Private cloud</li>
            <li class="quiz-option quiz-correct">D. Hybrid cloud ✓</li>
        </ul>
        <p class="quiz-explanation"><em>A hybrid cloud joins private systems you own with a public cloud you rent.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Which trait lets a cloud add ten more servers during a busy hour and remove them afterwards?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Measured service</li>
            <li class="quiz-option quiz-correct">B. Rapid elasticity ✓</li>
            <li class="quiz-option">C. Resource pooling</li>
            <li class="quiz-option">D. Self-service</li>
        </ul>
        <p class="quiz-explanation"><em>Rapid elasticity means capacity grows and shrinks quickly with demand.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. The tunnel to the cloud is up, but nothing on the Grid can reach the cloud network 10.200.0.0/16. What is the likely missing piece?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. A route to 10.200.0.0/16 through the tunnel ✓</li>
            <li class="quiz-option">B. A bigger hypervisor</li>
            <li class="quiz-option">C. A SaaS licence</li>
            <li class="quiz-option">D. A new VLAN</li>
        </ul>
        <p class="quiz-explanation"><em>Routers only send traffic where their routing table says. Without a route to the cloud network, packets have nowhere to go.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> read Earth Cloud's offer and name each service model, route the Grid to the cloud through the Skyport tunnel, and settle who patches what. <strong>Success:</strong> the edge router pings the cloud server.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g1-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm1', 'lesson' => 'lesson02']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
