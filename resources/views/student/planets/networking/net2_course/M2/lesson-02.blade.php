{{-- M2 · Lesson 2.2: OSPF Areas & LSA Types — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g2-l2.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="02">

    <h1 class="lesson-heading">Lesson 2.2: OSPF Areas & LSA Types</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain why OSPF uses areas, name the LSA types, fix an area mismatch and add a router to a non-backbone area.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        In one big OSPF area, every router stores the whole map and reruns SPF every time any link anywhere flaps. That gets heavy as the network grows. OSPF's answer is <strong>areas</strong>: groups of routers that share a detailed map among themselves and only <em>summaries</em> with everyone else.
    </p>
    <ul class="body-list">
        <li><strong>Area 0</strong> is the <strong>backbone</strong>. Every other area must connect to it, and traffic between areas crosses it.</li>
        <li>An <strong>ABR</strong> (Area Border Router) has interfaces in area 0 and in another area. It summarizes each side to the other.</li>
        <li>An <strong>ASBR</strong> (Autonomous System Boundary Router) brings in routes from outside OSPF, such as a static default route to Earth.</li>
    </ul>
    <p class="body-text">
        The map is built from different <strong>LSA types</strong>:
    </p>
    <ul class="body-list">
        <li><strong>Type 1 (Router)</strong>: every router describes its own links. Stays inside its area.</li>
        <li><strong>Type 2 (Network)</strong>: the DR (designated router) of a shared segment lists the routers on it. Stays inside its area.</li>
        <li><strong>Type 3 (Summary)</strong>: an ABR tells one area about the networks in the other. These routes show as <strong>O IA</strong> (inter-area).</li>
        <li><strong>Type 4</strong> points to an ASBR, and <strong>Type 5 (External)</strong> carries outside routes (shown as <strong>O E2</strong>).</li>
    </ul>
    <p class="body-text">
        A classic mistake: two ends of one link put in different areas. Hellos carry the area number, so the routers never become neighbors. <code>show ip ospf interface brief</code> on both ends shows the mismatch. To move a network to another area, remove the old statement and add the new one:
    </p>
    <pre class="code-block"><code>router ospf 1
 no network 10.0.23.0 0.0.0.3 area 0
 network 10.0.23.0 0.0.0.3 area 1</code></pre>
    <p class="body-text">
        <code>show ip ospf database</code> shows the LSAs a router holds, grouped by type and area.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does an ABR do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Brings in routes from outside of OSPF entirely</li>
            <li class="quiz-option">B. Floods Type 1 LSAs across every OSPF area</li>
            <li class="quiz-option">C. Picks the designated router on one segment</li>
            <li class="quiz-option quiz-correct">D. Sits in area 0 and another, summarizing each ✓</li>
        </ul>
        <p class="quiz-explanation"><em>An Area Border Router has interfaces in area 0 and another area, and summarizes each side to the other.</em></p>
    </div>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        The Grid is getting big enough to need neighborhoods. Skyport and the Citadel form the backbone, <strong>area 0</strong>. Northwatch and the mines at Deep Well become <strong>area 1</strong>, with the Citadel as the border between them.
    </p>
    <p class="body-text">
        "Somebody set this up in a hurry," says <strong>Rivet</strong>, reading the Northwatch config. "The Citadel says the link to Northwatch is area 1. Northwatch says it's area 0. They'll argue forever and never talk. Then Deep Well's new router needs joining up, and it's blank."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why will the Citadel and Northwatch never become neighbors?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The link between them is shut down</li>
            <li class="quiz-option">B. Northwatch has no OSPF process running at all</li>
            <li class="quiz-option quiz-correct">C. Each end puts the link in another area ✓</li>
            <li class="quiz-option">D. Both of them have the same router ID</li>
        </ul>
        <p class="quiz-explanation"><em>Hellos carry the area number. One end says area 1, the other says area 0, so they never become neighbors.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="OSPF area 0 with Skyport and the Citadel, area 1 with Northwatch and Deep Well, the Citadel as the area border router">
            <rect x="10" y="20" width="300" height="150" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2" stroke-dasharray="6 5"/>
            <text x="160.0" y="100.2" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b"></text>
            <text x="30" y="42" text-anchor="start" font-size="13" font-weight="700" fill="#14306b">Area 0 (backbone)</text>
            <rect x="350" y="20" width="300" height="150" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2" stroke-dasharray="6 5"/>
            <text x="500.0" y="100.2" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00"></text>
            <text x="370" y="42" text-anchor="start" font-size="13" font-weight="700" fill="#6b3a00">Area 1</text>
            <rect x="30" y="70" width="110" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="85.0" y="93.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">skyport</text>
            <text x="85.0" y="110.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">router</text>
            <rect x="250" y="70" width="140" height="56" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="320.0" y="93.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">citadel</text>
            <text x="320.0" y="110.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">ABR (area 0 + 1)</text>
            <rect x="420" y="70" width="100" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="470.0" y="102.7" text-anchor="middle" font-size="13.5" font-weight="700" fill="#3b2a8a">northwatch</text>
            <rect x="545" y="70" width="95" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="592.5" y="102.7" text-anchor="middle" font-size="13.5" font-weight="700" fill="#3b2a8a">deepwell</text>
            <line x1="140" y1="98" x2="250" y2="98" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="390" y1="98" x2="420" y2="98" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="520" y1="98" x2="545" y2="98" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <rect x="10" y="185" width="155" height="105" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="87.5" y="216.8" text-anchor="middle" font-size="13" font-weight="700" fill="#3a4352">Type 1 Router</text>
            <text x="87.5" y="233.3" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">every router,</text>
            <text x="87.5" y="249.8" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">its own links,</text>
            <text x="87.5" y="266.3" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">inside its area</text>
            <rect x="175" y="185" width="155" height="105" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="252.5" y="216.8" text-anchor="middle" font-size="13" font-weight="700" fill="#3a4352">Type 2 Network</text>
            <text x="252.5" y="233.3" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">from the DR of a</text>
            <text x="252.5" y="249.8" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">shared segment</text>
            <text x="252.5" y="266.3" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">inside its area</text>
            <rect x="340" y="185" width="155" height="105" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="417.5" y="216.8" text-anchor="middle" font-size="13" font-weight="700" fill="#3a4352">Type 3 Summary</text>
            <text x="417.5" y="233.3" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">an ABR tells one</text>
            <text x="417.5" y="249.8" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">area about the</text>
            <text x="417.5" y="266.3" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">other area&#x27;s networks</text>
            <rect x="505" y="185" width="145" height="105" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="577.5" y="216.8" text-anchor="middle" font-size="13" font-weight="700" fill="#3a4352">Type 5 External</text>
            <text x="577.5" y="233.3" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">an ASBR brings in</text>
            <text x="577.5" y="249.8" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">outside routes,</text>
            <text x="577.5" y="266.3" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">like a default</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, which LSA type does an ABR send about the other area?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Type 1 Router</li>
            <li class="quiz-option">B. Type 2 Network</li>
            <li class="quiz-option quiz-correct">C. Type 3 Summary ✓</li>
            <li class="quiz-option">D. Type 5 External</li>
        </ul>
        <p class="quiz-explanation"><em>An ABR uses Type 3 Summary LSAs to tell one area about the networks in the other.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which area must every other OSPF area connect to?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Area 1</li>
            <li class="quiz-option quiz-correct">B. Area 0, the backbone ✓</li>
            <li class="quiz-option">C. The largest area</li>
            <li class="quiz-option">D. Any area with a DR</li>
        </ul>
        <p class="quiz-explanation"><em>Area 0 is the backbone. Inter-area traffic always crosses it, so every area must touch it through an ABR.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. A router has one interface in area 0 and two in area 1. What is it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. An ASBR</li>
            <li class="quiz-option">B. A DR</li>
            <li class="quiz-option quiz-correct">C. An ABR ✓</li>
            <li class="quiz-option">D. A stub router</li>
        </ul>
        <p class="quiz-explanation"><em>An Area Border Router connects area 0 to another area and summarizes routes between them.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which LSA type does an ABR use to tell one area about another area's networks?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Type 1</li>
            <li class="quiz-option">B. Type 2</li>
            <li class="quiz-option quiz-correct">C. Type 3 ✓</li>
            <li class="quiz-option">D. Type 5</li>
        </ul>
        <p class="quiz-explanation"><em>Type 3 summary LSAs carry inter-area routes, which appear as O IA in the routing table.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Both ends of a link run OSPF and are in the same subnet, but they never become neighbors. One says area 0, the other area 1. Why?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Hellos carry the area number, and the areas must match ✓</li>
            <li class="quiz-option">B. Area 1 is too small</li>
            <li class="quiz-option">C. The wildcard mask is wrong</li>
            <li class="quiz-option">D. OSPF needs a static route first</li>
        </ul>
        <p class="quiz-explanation"><em>An area mismatch stops the adjacency: the hello packets disagree, so the neighbor is never formed.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why split a big OSPF network into areas?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. To make addresses longer</li>
            <li class="quiz-option quiz-correct">B. So each router keeps a smaller detailed map and reruns SPF less often ✓</li>
            <li class="quiz-option">C. To turn off SPF</li>
            <li class="quiz-option">D. Because OSPF only allows 10 routers</li>
        </ul>
        <p class="quiz-explanation"><em>Areas keep detailed LSAs local. Routers outside an area only see summaries, so databases stay small and a flapping link disturbs fewer routers.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> fix the area mismatch between the Citadel and Northwatch, then bring Deep Well into area 1. <strong>Success:</strong> rivet at Skyport reaches volt at Northwatch and the miners at Deep Well through the backbone.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g2-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm2', 'lesson' => 'lesson02']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
