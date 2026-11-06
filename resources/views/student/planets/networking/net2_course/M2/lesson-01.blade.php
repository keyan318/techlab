{{-- M2 · Lesson 2.1: Link-State Routing with OSPF — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g2-l1.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="01">

    <h1 class="lesson-heading">Lesson 2.1: Link-State Routing with OSPF</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain how OSPF finds paths, turn it on with network statements and wildcard masks, and check neighbors and learned routes.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        In Networking 1 you typed every route by hand (static routes). That works for three routers. For a planet, it breaks: add one outpost and you must edit every router, and when a link fails nothing moves by itself. A <strong>dynamic routing protocol</strong> lets routers tell each other what they know.
    </p>
    <p class="body-text">
        <strong>OSPF</strong> (Open Shortest Path First) is a <strong>link-state</strong> protocol. It works in three moves:
    </p>
    <ul class="body-list">
        <li><strong>Meet the neighbors.</strong> Routers send small <em>hello</em> messages out of each OSPF interface. Two routers on the same link, in the same subnet and the same area become <strong>neighbors</strong> and reach the <strong>FULL</strong> state.</li>
        <li><strong>Share the map.</strong> Each router describes its own links in <strong>LSAs</strong> (link-state advertisements) and floods them, so every router ends up with the same <strong>link-state database</strong>: a map of the whole network.</li>
        <li><strong>Find the shortest paths.</strong> Each router runs the <strong>SPF</strong> (Dijkstra) algorithm on that map. Every link has a <strong>cost</strong>; the path with the lowest total cost goes into the routing table.</li>
    </ul>
    <p class="body-text">
        You tell OSPF which interfaces to use with <code>network</code> statements. They use a <strong>wildcard mask</strong>, the opposite of a subnet mask: 0 means "this bit must match", 1 means "any value". So <code>0.0.0.3</code> matches a /30 and <code>0.0.0.255</code> a /24.
    </p>
    <pre class="code-block"><code>router ospf 1
 network 10.0.12.0 0.0.0.3 area 0
 network 192.168.10.0 0.0.0.255 area 0
 passive-interface g0/0</code></pre>
    <p class="body-text">
        <code>show ip ospf neighbor</code> lists neighbors, and <code>show ip route</code> marks routes OSPF learned with an <strong>O</strong>. A <strong>passive interface</strong> still advertises its network but sends no hellos: use it on ports facing PCs, where no router should ever answer.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does a passive interface do in OSPF?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Sends hellos but hides its own network away</li>
            <li class="quiz-option">B. Blocks every OSPF route it hears from</li>
            <li class="quiz-option">C. Becomes the root of the OSPF area</li>
            <li class="quiz-option quiz-correct">D. Advertises its network, sends no hellos ✓</li>
        </ul>
        <p class="quiz-explanation"><em>A passive interface still advertises its network but sends no hellos. Use it on ports facing PCs.</em></p>
    </div>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        The Landing Site, the Citadel and Northwatch each have a router now, cabled and addressed. But a router only knows the networks plugged into it. "Static routes again?" asks <strong>Rivet</strong>. "Three routers, six routes. Add Deep Well and Skyport, and we're typing routes until next season."
    </p>
    <p class="body-text">
        <strong>Astro</strong> shakes his head. "We teach the routers to talk to each other. They share what they see, and each one works out the best path on its own. That's OSPF. Start with the Landing Site and the Citadel."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why doesn't Astro want static routes for the outposts?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Static routes cannot reach the Citadel</li>
            <li class="quiz-option">B. Routers forget static routes each night</li>
            <li class="quiz-option">C. OSPF only works with exactly two routers in it</li>
            <li class="quiz-option quiz-correct">D. Each new outpost means editing every router ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Static routes work for three routers, but each new outpost means editing every router, and nothing reroutes on its own.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 270" role="img" aria-label="Three outpost routers forming OSPF neighbors, each building the same map and running shortest path first">
            <rect x="10" y="30" width="110" height="52" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="65.0" y="51.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">astro</text>
            <text x="65.0" y="68.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14306b">192.168.10.10</text>
            <rect x="170" y="30" width="110" height="52" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="225.0" y="51.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">landing</text>
            <text x="225.0" y="68.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">router</text>
            <rect x="330" y="30" width="110" height="52" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="385.0" y="51.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">citadel</text>
            <text x="385.0" y="68.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">router</text>
            <rect x="490" y="30" width="150" height="52" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="565.0" y="51.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">northwatch</text>
            <text x="565.0" y="68.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">router</text>
            <line x1="120" y1="56" x2="170" y2="56" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="280" y1="56" x2="330" y2="56" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="440" y1="56" x2="490" y2="56" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <text x="305" y="22" text-anchor="middle" font-size="11.5" font-weight="600" fill="#4a35b8">10.0.12.0/30</text>
            <text x="465" y="22" text-anchor="middle" font-size="11.5" font-weight="600" fill="#4a35b8">10.0.23.0/30</text>
            <line x1="240" y1="92" x2="351.0" y2="92.0" stroke="#2fa56b" stroke-width="2.5" stroke-linecap="round"/>
            <polygon points="360,92 351.0,98.0 351.0,86.0" fill="#2fa56b"/>
            <line x1="360" y1="104" x2="249.0" y2="104.0" stroke="#2fa56b" stroke-width="2.5" stroke-linecap="round"/>
            <polygon points="240,104 249.0,98.0 249.0,110.0" fill="#2fa56b"/>
            <line x1="400" y1="92" x2="511.0" y2="92.0" stroke="#2fa56b" stroke-width="2.5" stroke-linecap="round"/>
            <polygon points="520,92 511.0,98.0 511.0,86.0" fill="#2fa56b"/>
            <line x1="520" y1="104" x2="409.0" y2="104.0" stroke="#2fa56b" stroke-width="2.5" stroke-linecap="round"/>
            <polygon points="400,104 409.0,98.0 409.0,110.0" fill="#2fa56b"/>
            <text x="300" y="124" text-anchor="middle" font-size="11.5" font-weight="600" fill="#14663f">hello: neighbors (FULL)</text>
            <text x="460" y="124" text-anchor="middle" font-size="11.5" font-weight="600" fill="#14663f">hello: neighbors (FULL)</text>
            <rect x="10" y="145" width="300" height="115" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="160.0" y="181.8" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">1. Share link states</text>
            <text x="160.0" y="198.8" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">each router floods what it is</text>
            <text x="160.0" y="215.8" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">connected to (LSAs)</text>
            <text x="160.0" y="232.8" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">every router builds the same map</text>
            <rect x="340" y="145" width="310" height="115" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="495.0" y="181.8" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">2. Run SPF (Dijkstra)</text>
            <text x="495.0" y="198.8" text-anchor="middle" font-size="12" font-weight="500" fill="#14663f">each router finds its own</text>
            <text x="495.0" y="215.8" text-anchor="middle" font-size="12" font-weight="500" fill="#14663f">shortest path to every network</text>
            <text x="495.0" y="232.8" text-anchor="middle" font-size="12" font-weight="500" fill="#14663f">lowest total cost wins</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what wins when each router runs SPF?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The fewest neighbors</li>
            <li class="quiz-option">B. The highest router ID</li>
            <li class="quiz-option quiz-correct">C. The lowest total cost ✓</li>
            <li class="quiz-option">D. The longest cable run</li>
        </ul>
        <p class="quiz-explanation"><em>Each router runs SPF (Dijkstra) on the shared map. The path with the lowest total cost goes into its table.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What do two OSPF routers need in common to become neighbors?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The same hostname</li>
            <li class="quiz-option quiz-correct">B. The same link, subnet and area ✓</li>
            <li class="quiz-option">C. The same number of interfaces</li>
            <li class="quiz-option">D. The same static routes</li>
        </ul>
        <p class="quiz-explanation"><em>Neighbors must hear each other's hellos on a shared link, with addresses in the same subnet and the same area number (and matching timers).</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does every OSPF router in an area end up with?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A different map each</li>
            <li class="quiz-option quiz-correct">B. The same link-state database ✓</li>
            <li class="quiz-option">C. Only its own routes</li>
            <li class="quiz-option">D. A list of MAC addresses</li>
        </ul>
        <p class="quiz-explanation"><em>LSAs are flooded so every router in the area has the identical link-state database, then each runs SPF from its own point of view.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which wildcard mask matches exactly the /30 network 10.0.12.0?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 255.255.255.252</li>
            <li class="quiz-option quiz-correct">B. 0.0.0.3 ✓</li>
            <li class="quiz-option">C. 0.0.0.255</li>
            <li class="quiz-option">D. 0.0.3.255</li>
        </ul>
        <p class="quiz-explanation"><em>A wildcard is the inverse of the subnet mask: 255.255.255.252 becomes 0.0.0.3.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. In <code>show ip route</code>, which letter marks a route learned from OSPF?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. S</li>
            <li class="quiz-option">B. C</li>
            <li class="quiz-option quiz-correct">C. O ✓</li>
            <li class="quiz-option">D. R</li>
        </ul>
        <p class="quiz-explanation"><em>O is OSPF. S is static, C is connected, R is RIP.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why make the interface facing the PCs passive?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It turns the port off</li>
            <li class="quiz-option">B. It stops OSPF advertising that network</li>
            <li class="quiz-option quiz-correct">C. It still advertises the network but sends no hellos where no router should answer ✓</li>
            <li class="quiz-option">D. It makes the PCs faster</li>
        </ul>
        <p class="quiz-explanation"><em>A passive interface keeps its network in OSPF but sends no hellos, so no device on that LAN can try to become a neighbor.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> turn on OSPF at the Landing Site, the Citadel and Northwatch, and keep hellos off the PC networks. <strong>Success:</strong> astro reaches the Citadel archive and volt at Northwatch with no static routes at all.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g2-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm2', 'lesson' => 'lesson01']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
