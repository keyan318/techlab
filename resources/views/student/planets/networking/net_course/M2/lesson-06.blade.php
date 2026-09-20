{{-- M2 · Lesson 2.6: Routing Tables &amp; Static Routes — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m2-l6.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="06">

    <h1 class="lesson-heading">Lesson 2.6: Routing Tables &amp; Static Routes</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can read a routing table, explain how a router chooses a route, and configure static routes in both directions.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A router keeps a <strong>routing table</strong>: a list of networks it knows how to reach. Each line has a <strong>destination network</strong>, the <strong>next hop</strong> (the next router, written <code>via</code>) or <em>connected</em>, and the interface to use. On Linux, <code>ip route</code> shows it.
    </p>
    <p class="body-text">
        For each packet the router picks the <strong>most specific match</strong>: the route with the longest prefix. If a router has <code>10.0.0.0/8</code> and <code>10.1.1.0/24</code>, a packet for <code>10.1.1.5</code> uses the <code>/24</code> route. The <strong>default route</strong> <code>0.0.0.0/0</code> matches everything and is used only when nothing more specific does.
    </p>
    <p class="body-text">
        Routes come from three places: <strong>connected</strong> networks (added automatically), <strong>static</strong> routes (typed by an administrator) and <strong>dynamic</strong> routes (learned from other routers). Static routes are simple and predictable but do not adapt when a link fails. Dynamic <strong>routing protocols</strong> let routers share what they know and recover on their own: <strong>RIP</strong> counts hops (a <em>distance-vector</em> protocol), <strong>OSPF</strong> builds a map of the network (a <em>link-state</em> protocol), and <strong>BGP</strong> joins the networks of the Internet.
    </p>
    <p class="body-text">
        Remember: traffic needs a route <em>there</em> and a route <em>back</em>. Set both directions.
    </p>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        The long link to Earth needs two relay towers, <code>r1</code> and <code>r2</code>. <strong>Rivet</strong> has wired them but each tower only knows the networks it touches. "r1 has never heard of Earth's network. r2 has never heard of ours."
    </p>
    <p class="body-text">
        <strong>Astro</strong> reads the table. "So we teach each tower the far side. One static route each, in both directions."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 210" role="img" aria-label="Two routers each with a static route to the far network">
            <rect x="10" y="40" width="100" height="56" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="60.0" y="65.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">astro</text>
            <text x="60.0" y="84.0" text-anchor="middle" font-size="12.5" fill="#14306b">192.168.1.10</text>
            <rect x="190" y="40" width="90" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="235.0" y="65.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">r1</text>
            <text x="235.0" y="84.0" text-anchor="middle" font-size="12.5" fill="#3b2a8a">router</text>
            <rect x="370" y="40" width="90" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="415.0" y="65.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">r2</text>
            <text x="415.0" y="84.0" text-anchor="middle" font-size="12.5" fill="#3b2a8a">router</text>
            <rect x="540" y="40" width="110" height="56" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="595.0" y="65.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">Earth</text>
            <text x="595.0" y="84.0" text-anchor="middle" font-size="12.5" fill="#14663f">172.16.0.10</text>
            <line x1="110" y1="68" x2="190" y2="68" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="280" y1="68" x2="370" y2="68" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="460" y1="68" x2="540" y2="68" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <text x="150" y="34" text-anchor="middle" font-size="11.5" font-weight="600" fill="#4a35b8">192.168.1.0/24</text>
            <text x="325" y="34" text-anchor="middle" font-size="11.5" font-weight="600" fill="#4a35b8">10.0.12.0/24</text>
            <text x="500" y="34" text-anchor="middle" font-size="11.5" font-weight="600" fill="#4a35b8">172.16.0.0/24</text>
            <rect x="105" y="118" width="230" height="78" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="220" y="140" text-anchor="middle" font-size="13" font-weight="700" fill="#6b3a00">r1 routing table</text>
            <text x="220" y="162" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">192.168.1.0/24  connected</text>
            <text x="220" y="180" text-anchor="middle" font-size="12" font-weight="700" fill="#6b3a00">172.16.0.0/24  via 10.0.12.2</text>
            <rect x="365" y="118" width="230" height="78" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="480" y="140" text-anchor="middle" font-size="13" font-weight="700" fill="#6b3a00">r2 routing table</text>
            <text x="480" y="162" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">172.16.0.0/24  connected</text>
            <text x="480" y="180" text-anchor="middle" font-size="12" font-weight="700" fill="#6b3a00">192.168.1.0/24  via 10.0.12.1</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. A router has routes for 10.0.0.0/8 and 10.1.1.0/24. Where does a packet for 10.1.1.5 go?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The /8 route</li>
            <li class="quiz-option quiz-correct">B. The /24 route ✓</li>
            <li class="quiz-option">C. Both routes</li>
            <li class="quiz-option">D. Neither</li>
        </ul>
        <p class="quiz-explanation"><em>The router picks the most specific match, meaning the longest prefix. /24 is more specific than /8.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does the default route 0.0.0.0/0 mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Drop everything</li>
            <li class="quiz-option quiz-correct">B. Use this route when no more specific route matches ✓</li>
            <li class="quiz-option">C. Only for the local network</li>
            <li class="quiz-option">D. Shut the router down</li>
        </ul>
        <p class="quiz-explanation"><em>0.0.0.0/0 matches every address, so it is the last-resort route used when nothing more specific matches.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is the main difference between static and dynamic routing?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Static routes are faster because they use light</li>
            <li class="quiz-option quiz-correct">B. Static routes are typed by an administrator, and dynamic routes are learned and adapt to changes ✓</li>
            <li class="quiz-option">C. Dynamic routing needs no routers</li>
            <li class="quiz-option">D. There is no difference</li>
        </ul>
        <p class="quiz-explanation"><em>An administrator enters static routes by hand and they never change on their own. Dynamic routing protocols such as RIP and OSPF learn routes and adapt when links fail.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> connect astro to Earth across two routers. <strong>Success:</strong> astro can ping <code>earth-cloud</code> and fetch its page. Watch what happens when only one direction has a route.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m2-l6" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm2', 'lesson' => 'lesson06']) }}">
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
