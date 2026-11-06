{{-- M6 · Lesson 6.8: Final Mission: Hold the Grid — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g6-l8.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="08">

    <h1 class="lesson-heading">Lesson 6.8: Final Mission: Hold the Grid</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this mission, you can defend and repair a multi-layer network under attack, combining spanning tree protection, OSPF, HSRP tracking and access lists.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        This is the last mission of the season. Everything you have built is in play at once: switching, routing, redundancy and security. Work layer by layer, test after each fix, and read the output before you change anything.
    </p>
    <ul class="body-list">
        <li><strong>Switching</strong>: <code>show spanning-tree</code> on the hangar switch. Who is root? Any port where a switch should never be?</li>
        <li><strong>Routing</strong>: <code>show ip ospf neighbor</code> on the Citadel. Both Skyport routers should be there. If one is missing, <code>show ip protocols</code> on it lists its passive interfaces.</li>
        <li><strong>Gateways</strong>: <code>show standby brief</code>. What happens if the active router loses its uplink? Tracking and preempt.</li>
        <li><strong>Edge</strong>: who is knocking? An access list, with the deny <em>before</em> the permit.</li>
    </ul>
    <p class="body-text">
        When everything works, the grader also tests failure: it cuts links for a moment and follows the traffic. A Grid that only works when nothing breaks isn't finished.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What else does the grader test once everything works?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Speed: it times how fast each page loads for the crew</li>
            <li class="quiz-option">B. Style: it reads the configs for neatness</li>
            <li class="quiz-option">C. Size: it counts the routes in each table</li>
            <li class="quiz-option quiz-correct">D. Failures: it cuts links, follows the traffic ✓</li>
        </ul>
        <p class="quiz-explanation"><em>A Grid that only works when nothing breaks isn't finished. The grader cuts links and follows the traffic.</em></p>
    </div>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        Earth's relay fleet is an hour out from Skyport, and Planet Doom knows it. The raid starts at once: a rogue switch in the hangar, a sabotaged router config, a gateway that would fail silently, and Vex himself hammering the Citadel's door. The shield meter drops below twenty percent.
    </p>
    <p class="body-text">
        <strong>Astro</strong> doesn't raise his voice. "Season one, we called Earth. This season, we built them somewhere to land. Hold the Grid, Cadet. Layer by layer." <strong>Rivet</strong> takes the routers, <strong>Volt</strong> takes the shields, and the console is yours.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What does Astro tell the crew to do during the raid?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Hold the Grid, layer by layer ✓</li>
            <li class="quiz-option">B. Shut down every router fast</li>
            <li class="quiz-option">C. Call Earth and wait for their help</li>
            <li class="quiz-option">D. Leave Skyport and fall back</li>
        </ul>
        <p class="quiz-explanation"><em>Astro stays calm: hold the Grid, layer by layer. Rivet takes the routers, Volt the shields.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="The final mission checklist: spanning tree, routing, gateway redundancy and the edge filter, then an end-to-end test">
            <rect x="10" y="10" width="640" height="56" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="330.0" y="33.1" text-anchor="middle" font-size="14.5" font-weight="700" fill="#14306b">1. Switching</text>
            <text x="330.0" y="50.1" text-anchor="middle" font-size="12" font-weight="500" fill="#14306b">a rogue switch claiming root → BPDU Guard</text>
            <rect x="10" y="76" width="640" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="330.0" y="99.1" text-anchor="middle" font-size="14.5" font-weight="700" fill="#3b2a8a">2. Routing</text>
            <text x="330.0" y="116.1" text-anchor="middle" font-size="12" font-weight="500" fill="#3b2a8a">a missing OSPF neighbor → passive-interface</text>
            <rect x="10" y="142" width="640" height="56" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="330.0" y="165.1" text-anchor="middle" font-size="14.5" font-weight="700" fill="#6b3a00">3. Gateways</text>
            <text x="330.0" y="182.1" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">a router that hides a dead uplink → HSRP tracking</text>
            <rect x="10" y="208" width="640" height="56" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="330.0" y="231.1" text-anchor="middle" font-size="14.5" font-weight="700" fill="#7a1820">4. Edge</text>
            <text x="330.0" y="248.1" text-anchor="middle" font-size="12" font-weight="500" fill="#7a1820">Vex at the door → access list, deny first</text>
            <text x="330" y="290" text-anchor="middle" font-size="13" font-weight="700" fill="#14663f">then: rivet at Skyport opens Earth&#x27;s page, whatever fails</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the checklist, what stops a rogue switch claiming root?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. HSRP tracking</li>
            <li class="quiz-option">B. An access list</li>
            <li class="quiz-option quiz-correct">C. BPDU Guard ✓</li>
            <li class="quiz-option">D. A passive port</li>
        </ul>
        <p class="quiz-explanation"><em>Step 1: a rogue switch claiming root is stopped by BPDU Guard on the access ports.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. A switch with priority 0 is plugged into a hangar PC port. Which feature stops it the moment it sends a BPDU?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Root Guard on the PC port</li>
            <li class="quiz-option quiz-correct">B. BPDU Guard (with PortFast) on the PC port ✓</li>
            <li class="quiz-option">C. Port security</li>
            <li class="quiz-option">D. An ACL</li>
        </ul>
        <p class="quiz-explanation"><em>BPDU Guard err-disables an edge port as soon as a BPDU arrives on it.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. A router never forms an OSPF neighbor on its uplink, and <code>show ip protocols</code> lists that uplink as passive. Fix?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Raise the cost</li>
            <li class="quiz-option quiz-correct">B. <code>no passive-interface</code> for the uplink ✓</li>
            <li class="quiz-option">C. Change the area</li>
            <li class="quiz-option">D. Add a static route</li>
        </ul>
        <p class="quiz-explanation"><em>A passive interface sends no hellos, so no neighbor can form on it.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. The HSRP active router loses its uplink but its LAN port stays up. What makes the standby take over?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing can</li>
            <li class="quiz-option quiz-correct">B. Tracking the uplink on the active router, plus preempt on the standby ✓</li>
            <li class="quiz-option">C. A bigger subnet</li>
            <li class="quiz-option">D. BPDU Guard</li>
        </ul>
        <p class="quiz-explanation"><em>Tracking lowers the active router's priority when its uplink fails; preempt lets the standby claim the role.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. An ACL permits all traffic, then denies Vex's address at the end. Does it stop Vex?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Yes</li>
            <li class="quiz-option quiz-correct">B. No, the permit matches first; the deny must come before it ✓</li>
            <li class="quiz-option">C. Only for TCP</li>
            <li class="quiz-option">D. Only outbound</li>
        </ul>
        <p class="quiz-explanation"><em>ACLs are first-match. A deny after a matching permit is never reached.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Everything works. What final check proves the Grid is ready?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Rebooting all routers</li>
            <li class="quiz-option quiz-correct">B. Testing that traffic still flows when links and routers fail ✓</li>
            <li class="quiz-option">C. Changing all passwords</li>
            <li class="quiz-option">D. Nothing</li>
        </ul>
        <p class="quiz-explanation"><em>Redundancy only counts once failover has been tested.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> stop the hangar takeover, restore the routing, make the gateway fail over properly, and lock Vex out of the Citadel. <strong>Success:</strong> rivet at Skyport opens Earth's page, keeps it if a Skyport router or uplink fails, and Vex reaches nothing.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g6-l8" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm6', 'lesson' => 'lesson08']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
