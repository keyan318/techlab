{{-- M2 · Lesson 2.5: BGP Path Selection, Communities & Filtering — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g2-l5.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="05">

    <h1 class="lesson-heading">Lesson 2.5: BGP Path Selection, Communities & Filtering</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain how BGP picks a best path, change it with local preference, filter bad routes with a prefix list, and read BGP communities.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        When BGP hears the same network from two neighbors, it picks <strong>one best path</strong> (marked <code>&gt;</code> in <code>show ip bgp</code>) by walking down a list until something breaks the tie:
    </p>
    <ul class="body-list">
        <li><strong>1. Weight</strong> (Cisco only, this router only): highest wins.</li>
        <li><strong>2. Local preference</strong>: shared by every router in your AS, highest wins. This is how an AS chooses which <em>exit</em> to use.</li>
        <li><strong>3. Locally originated</strong> routes beat learned ones.</li>
        <li><strong>4. Shortest AS path</strong>: fewest ASes crossed.</li>
        <li><strong>5.</strong> Then lowest origin type, lowest <strong>MED</strong> (a hint from the neighbor about which entrance to use), eBGP over iBGP, lowest IGP cost to the next hop, and finally the lowest router ID.</li>
    </ul>
    <p class="body-text">
        You change the result with a <strong>route-map</strong>: a list of "match this, set that" rules applied to a neighbor, inbound or outbound.
    </p>
    <pre class="code-block"><code>route-map MOON-FIRST permit 10
 set local-preference 200
router bgp 65010
 neighbor 198.18.0.1 route-map MOON-FIRST in</code></pre>
    <p class="body-text">
        BGP trusts its neighbors, which makes <strong>filtering</strong> essential. A <strong>prefix list</strong> lists networks to permit or deny. <code>le 32</code> means "and any longer prefix inside it". Typical rule: never accept your own networks from outside, because someone announcing them is a <strong>route hijack</strong>.
    </p>
    <pre class="code-block"><code>ip prefix-list NO-GRID seq 5 deny 192.168.0.0/16 le 32
ip prefix-list NO-GRID seq 10 permit 0.0.0.0/0 le 32
router bgp 65010
 neighbor 203.0.113.1 prefix-list NO-GRID in</code></pre>
    <p class="body-text">
        After changing a policy, <code>clear ip bgp * soft</code> re-applies it without dropping the sessions. <strong>Communities</strong> are tags attached to routes, used to signal policy between ASes. Well-known ones: <code>no-export</code> (don't pass this route to another AS) and <code>no-advertise</code> (don't pass it to any peer).
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Which BGP attribute does a whole AS use to choose its exit?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The router's weight</li>
            <li class="quiz-option">B. The lowest router ID</li>
            <li class="quiz-option">C. The MED from a peer</li>
            <li class="quiz-option quiz-correct">D. Local preference ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Local preference is shared by every router in your AS, and the highest wins. That is how an AS picks its exit.</em></p>
    </div>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        Skyport now has two ways out: Earth Relay and the <strong>Moonbase</strong> relay, AS 64600. Moonbase is faster, but BGP keeps choosing Earth. "BGP isn't picking the <em>fastest</em> path," says <strong>Astro</strong>. "It's following its list. We change the list's answer with local preference."
    </p>
    <p class="body-text">
        Then <strong>Volt</strong> goes pale. "Look at 192.168.20.0. That's the Citadel's own network, and we're learning it from <em>outside</em>, through AS 66666. That's Planet Doom. If we believe it, the Citadel's traffic flies straight to them."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why is learning 192.168.20.0 through AS 66666 dangerous?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. It hijacks the Citadel's own network ✓</li>
            <li class="quiz-option">B. It is a shorter path than Earth's relay</li>
            <li class="quiz-option">C. It makes the Moonbase relay much slower</li>
            <li class="quiz-option">D. It switches BGP over to using the OSPF protocol</li>
        </ul>
        <p class="quiz-explanation"><em>Someone outside announcing your own network is a route hijack. Believe it and the Citadel's traffic flies to Doom.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 290" role="img" aria-label="The first steps of BGP best path selection, in order, with a filter blocking a hijacked route">
            <rect x="10" y="10" width="330" height="48" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="175.0" y="29.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">1. Highest weight</text>
            <text x="175.0" y="45.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">Cisco only, this router</text>
            <rect x="10" y="66" width="330" height="48" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="175.0" y="85.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">2. Highest local preference</text>
            <text x="175.0" y="101.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">whole AS: which exit to use</text>
            <rect x="10" y="122" width="330" height="48" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="175.0" y="141.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">3. Locally originated</text>
            <text x="175.0" y="157.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">our own routes first</text>
            <rect x="10" y="178" width="330" height="48" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="175.0" y="197.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14663f">4. Shortest AS path</text>
            <text x="175.0" y="213.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">fewest ASes crossed</text>
            <rect x="10" y="234" width="330" height="48" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="175.0" y="253.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">5. Lowest origin, MED…</text>
            <text x="175.0" y="269.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">then eBGP over iBGP, IGP cost</text>
            <rect x="370" y="10" width="280" height="130" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="510.0" y="46.5" text-anchor="middle" font-size="13" font-weight="700" fill="#7a1820">Prefix list on Earth&#x27;s session</text>
            <text x="510.0" y="63.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">deny 192.168.0.0/16 le 32</text>
            <text x="510.0" y="79.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">permit 0.0.0.0/0 le 32</text>
            <text x="510.0" y="96.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">&quot;never accept our own</text>
            <text x="510.0" y="112.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">networks from outside&quot;</text>
            <rect x="370" y="155" width="280" height="120" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="510.0" y="194.3" text-anchor="middle" font-size="13" font-weight="700" fill="#6b3a00">Community no-export</text>
            <text x="510.0" y="210.8" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">a tag on a route:</text>
            <text x="510.0" y="227.3" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">&quot;use it, but don&#x27;t pass it</text>
            <text x="510.0" y="243.8" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">to another AS&quot;</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what is step 1 of best path selection?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Shortest AS path</li>
            <li class="quiz-option">B. Lowest origin type</li>
            <li class="quiz-option">C. Highest local preference</li>
            <li class="quiz-option quiz-correct">D. Highest weight ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Step 1 is the highest weight (Cisco only, this router). Local preference is step 2.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Two paths to the same network: one via AS path "64500", one via "64600 64500". Weight and local preference are equal. Which wins?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The longer path</li>
            <li class="quiz-option quiz-correct">B. The path through AS 64500 only, because its AS path is shorter ✓</li>
            <li class="quiz-option">C. Both are used equally</li>
            <li class="quiz-option">D. The newest one</li>
        </ul>
        <p class="quiz-explanation"><em>With weight and local preference tied and neither route local, BGP prefers the shortest AS path.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Your AS wants all its routers to leave through Moonbase. Which attribute do you set?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. MED</li>
            <li class="quiz-option quiz-correct">B. Local preference ✓</li>
            <li class="quiz-option">C. Origin</li>
            <li class="quiz-option">D. Next hop</li>
        </ul>
        <p class="quiz-explanation"><em>Local preference is shared inside your AS and decides which exit point to use. Higher wins.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does <code>le 32</code> add to <code>deny 192.168.0.0/16</code> in a prefix list?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing</li>
            <li class="quiz-option quiz-correct">B. It also matches every longer prefix inside 192.168.0.0/16, like 192.168.20.0/24 ✓</li>
            <li class="quiz-option">C. It matches only /32 hosts</li>
            <li class="quiz-option">D. It permits instead of denies</li>
        </ul>
        <p class="quiz-explanation"><em><code>le 32</code> means prefix lengths from 16 up to 32 inside that range all match.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. A route arrives tagged with the community <code>no-export</code>. What may your border router do with it?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Use it, but not advertise it to another AS ✓</li>
            <li class="quiz-option">B. Drop it</li>
            <li class="quiz-option">C. Advertise it everywhere</li>
            <li class="quiz-option">D. Change its AS path</li>
        </ul>
        <p class="quiz-explanation"><em>no-export routes can be used inside your AS but must not be passed to external BGP peers.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Someone outside announces a more specific route to one of your own networks. What is this called, and the standard defence?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Load balancing; raise MED</li>
            <li class="quiz-option quiz-correct">B. A route hijack; filter your own prefixes inbound with a prefix list ✓</li>
            <li class="quiz-option">C. Convergence; wait</li>
            <li class="quiz-option">D. An iBGP loop; next-hop-self</li>
        </ul>
        <p class="quiz-explanation"><em>Announcing someone else's prefix is a hijack. Inbound filters that refuse your own address space stop it.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> read why BGP prefers Earth, make Moonbase the preferred exit, refuse Doom's hijacked route, and read a community tag. <strong>Success:</strong> traffic leaves through Moonbase and the Citadel's network is never learned from outside.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g2-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm2', 'lesson' => 'lesson05']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
