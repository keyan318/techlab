{{-- M2 · Lesson 2.4: BGP: eBGP vs iBGP — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g2-l4.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="04">

    <h1 class="lesson-heading">Lesson 2.4: BGP: eBGP vs iBGP</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what an autonomous system is, tell eBGP from iBGP, and configure BGP neighbors and advertised networks.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        OSPF is built for routing <em>inside</em> one organization. Between organizations, the internet runs on <strong>BGP</strong> (Border Gateway Protocol). Each organization is an <strong>autonomous system</strong> (AS) with a number, like AS 64500. BGP doesn't look for the lowest cost; it is a <strong>path-vector</strong> protocol that follows <em>policy</em>: which neighbor to trust, which routes to prefer, which to refuse.
    </p>
    <p class="body-text">
        Every BGP route carries an <strong>AS path</strong>: the list of AS numbers it crossed. If a router sees its own AS in the path, it drops the route, which stops loops.
    </p>
    <ul class="body-list">
        <li><strong>eBGP</strong> (external): a session between routers in <em>different</em> ASes, usually directly connected. Each router adds its own AS to the path when it passes a route on.</li>
        <li><strong>iBGP</strong> (internal): a session between routers in the <em>same</em> AS, so every border router knows what the others learned. iBGP does not change the AS path or the next hop.</li>
    </ul>
    <p class="body-text">
        That last point causes a classic problem: a route learned from Earth still says "next hop 203.0.113.1" when it reaches the Citadel over iBGP, but the Citadel has no route to that address. The fix on the border router is <code>neighbor 10.0.0.2 next-hop-self</code>.
    </p>
    <pre class="code-block"><code>router bgp 65010
 neighbor 203.0.113.1 remote-as 64500
 neighbor 10.0.0.2 remote-as 65010
 network 192.168.0.0 mask 255.255.0.0</code></pre>
    <p class="body-text">
        <code>remote-as</code> decides the session type: a different AS than yours means eBGP, the same means iBGP. The <code>network</code> command advertises a prefix, but only if that exact prefix is already in the routing table. <code>show ip bgp summary</code> shows each neighbor; a number in the last column means the session is up (Established) and how many prefixes it sent.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">How does BGP stop routing loops?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. It drops routes that list its own AS ✓</li>
            <li class="quiz-option">B. It picks the lowest total link cost</li>
            <li class="quiz-option">C. It floods LSAs to every router in turn</li>
            <li class="quiz-option">D. It sends hellos out of every interface</li>
        </ul>
        <p class="quiz-explanation"><em>Every route carries an AS path. A router that sees its own AS in the path drops the route.</em></p>
    </div>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        Earth sends its paperwork: the Grid is now <strong>AS 65010</strong>, and Earth Relay is <strong>AS 64500</strong>. "From now on, Codexia is on the internet's map," says <strong>Astro</strong>. "Skyport speaks BGP to Earth, and to the Citadel inside our own AS."
    </p>
    <p class="body-text">
        <strong>Rivet</strong> opens the Skyport console. "Two neighbors, two kinds of BGP. And one trap that bites everyone the first time: the next hop."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What trap does Rivet warn about with Skyport's two neighbors?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The next hop ✓</li>
            <li class="quiz-option">B. The AS path</li>
            <li class="quiz-option">C. The MED value</li>
            <li class="quiz-option">D. The router ID</li>
        </ul>
        <p class="quiz-explanation"><em>iBGP doesn't change the next hop, so the Citadel gets Earth's address as next hop and can't reach it. Use next-hop-self.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 270" role="img" aria-label="The Grid as autonomous system 65010 peering with Earth Relay, autonomous system 64500, over eBGP, and iBGP inside the Grid">
            <rect x="10" y="20" width="330" height="150" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2" stroke-dasharray="6 5"/>
            <text x="175.0" y="100.2" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b"></text>
            <text x="30" y="42" text-anchor="start" font-size="13" font-weight="700" fill="#14306b">The Grid: AS 65010</text>
            <rect x="400" y="20" width="250" height="150" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2" stroke-dasharray="6 5"/>
            <text x="525.0" y="100.2" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f"></text>
            <text x="420" y="42" text-anchor="start" font-size="13" font-weight="700" fill="#14663f">Earth Relay: AS 64500</text>
            <rect x="30" y="80" width="120" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="90.0" y="103.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">citadel</text>
            <text x="90.0" y="120.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">10.0.0.2</text>
            <rect x="200" y="80" width="120" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="260.0" y="103.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">skyport</text>
            <text x="260.0" y="120.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">10.0.0.1</text>
            <rect x="450" y="80" width="150" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="525.0" y="103.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">earth-edge</text>
            <text x="525.0" y="120.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#3b2a8a">203.0.113.1</text>
            <line x1="150" y1="108" x2="200" y2="108" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <line x1="320" y1="108" x2="450" y2="108" stroke="#2fa56b" stroke-width="3" stroke-linecap="round"/>
            <text x="175" y="150" text-anchor="middle" font-size="11.5" font-weight="600" fill="#2f6fe0">iBGP</text>
            <text x="385" y="100" text-anchor="middle" font-size="11.5" font-weight="600" fill="#14663f">eBGP</text>
            <rect x="10" y="185" width="310" height="75" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="165.0" y="209.2" text-anchor="middle" font-size="13.5" font-weight="700" fill="#6b3a00">iBGP: same AS</text>
            <text x="165.0" y="226.2" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">routes keep their outside next hop</text>
            <text x="165.0" y="243.2" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">→ use next-hop-self</text>
            <rect x="340" y="185" width="310" height="75" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="495.0" y="209.2" text-anchor="middle" font-size="13.5" font-weight="700" fill="#6b3a00">eBGP: different AS</text>
            <text x="495.0" y="226.2" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">directly connected peers</text>
            <text x="495.0" y="243.2" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">adds its AS number to the path</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, which session type joins skyport and the citadel?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. eBGP</li>
            <li class="quiz-option">B. OSPF</li>
            <li class="quiz-option">C. EIGRP</li>
            <li class="quiz-option quiz-correct">D. iBGP ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Skyport and the Citadel are both in AS 65010, so their session is iBGP. The one to Earth is eBGP.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is an autonomous system?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A router that needs no admin</li>
            <li class="quiz-option quiz-correct">B. A network under one organization's control, with its own AS number ✓</li>
            <li class="quiz-option">C. A type of LSA</li>
            <li class="quiz-option">D. A BGP password</li>
        </ul>
        <p class="quiz-explanation"><em>An AS is a group of networks under one administration, identified by an AS number, that presents one routing policy to the outside.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Skyport is in AS 65010. Which neighbor statement creates an iBGP session?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. <code>neighbor 203.0.113.1 remote-as 64500</code></li>
            <li class="quiz-option quiz-correct">B. <code>neighbor 10.0.0.2 remote-as 65010</code> ✓</li>
            <li class="quiz-option">C. <code>network 192.168.0.0 mask 255.255.0.0</code></li>
            <li class="quiz-option">D. <code>router bgp 64500</code></li>
        </ul>
        <p class="quiz-explanation"><em>iBGP means the neighbor is in the same AS as you: remote-as 65010 on a router in AS 65010.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. How does BGP stop a route from looping between ASes?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. TTL</li>
            <li class="quiz-option quiz-correct">B. It drops any route whose AS path already contains its own AS ✓</li>
            <li class="quiz-option">C. Split horizon on every link</li>
            <li class="quiz-option">D. It uses the lowest cost</li>
        </ul>
        <p class="quiz-explanation"><em>The AS path records every AS a route crossed. Seeing your own AS in it means the route has looped.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. A route from Earth reaches the Citadel over iBGP with next hop 203.0.113.1, which the Citadel cannot reach. What fixes it on Skyport?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. <code>neighbor 10.0.0.2 next-hop-self</code> ✓</li>
            <li class="quiz-option">B. A second eBGP session</li>
            <li class="quiz-option">C. <code>passive-interface</code></li>
            <li class="quiz-option">D. A bigger AS number</li>
        </ul>
        <p class="quiz-explanation"><em>next-hop-self makes Skyport put its own address as the next hop for routes it passes to its iBGP peer.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. BGP's <code>network 192.168.0.0 mask 255.255.0.0</code> is typed, but nothing is advertised. What is the usual reason?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. BGP is too slow</li>
            <li class="quiz-option quiz-correct">B. That exact prefix is not in the routing table ✓</li>
            <li class="quiz-option">C. The mask must be a wildcard</li>
            <li class="quiz-option">D. eBGP is down</li>
        </ul>
        <p class="quiz-explanation"><em>BGP only advertises a network statement if an exact matching route exists in the routing table, often a static route to Null0 for a summary.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> sort Skyport's two neighbors into eBGP and iBGP, bring both sessions up and advertise the Grid, then fix the next-hop trap. <strong>Success:</strong> both sessions are Established and the Citadel gets routes it can use.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g2-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm2', 'lesson' => 'lesson04']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
