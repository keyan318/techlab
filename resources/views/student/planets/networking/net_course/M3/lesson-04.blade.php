{{-- M3 · Lesson 3.4: DHCP Server Settings — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m3-l4.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="04">

    <h1 class="lesson-heading">Lesson 3.4: DHCP Server Settings</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can describe the settings of a DHCP scope, size a pool for the clients it must serve, and explain what happens when it runs out.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A DHCP server is configured with a <strong>scope</strong>: the rules for one subnet. The main settings are:
    </p>
    <ul class="body-list">
        <li><strong>Address pool</strong> (range): the first and last address the server may hand out, such as <code>192.168.1.100</code> to <code>192.168.1.150</code>.</li>
        <li><strong>Subnet mask</strong>: taken from the network the server sits on, such as /24.</li>
        <li><strong>Default gateway</strong> (router option): the way out to other networks. Without it, a client can only talk locally.</li>
        <li><strong>DNS server</strong> option: who the client should ask to turn names into addresses.</li>
        <li><strong>Lease time</strong>: how long an address is lent before it must be renewed.</li>
        <li><strong>Exclusions and reservations</strong>: addresses kept out of the pool for static devices, or tied to one device's MAC address.</li>
    </ul>
    <p class="body-text">
        <strong>Size the pool</strong> for the devices that will ask, with room to grow. If the pool is <strong>exhausted</strong>, the next client gets no offer at all and stays without an address, which is easy to mistake for a broken cable. Keep static devices <em>outside</em> the pool (or excluded) so DHCP never hands out an address a server already uses.
    </p>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        <strong>Rivet</strong> reads the server log with a frown. "Two addresses in the pool. Three consoles asking. Somebody is left out." He widens the range, then notices the second problem. "And the clients have an address but no way out and no name server. We never told the server to hand those out."
    </p>
    <p class="body-text">
        <strong>Astro</strong> summarizes. "A lease is more than an address. It is the address, the gateway and the resolver, all together."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 206" role="img" aria-label="A DHCP scope: excluded addresses, the pool, spare addresses and the options">
            <text x="330" y="20" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">The address space 192.168.1.0/24, as the crew plans it</text>
            <rect x="10" y="34" width="241" height="50" rx="8" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <rect x="251" y="34" width="124" height="50" rx="8" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <rect x="375" y="34" width="255" height="50" rx="8" fill="#eceff7" stroke="#6b7699" stroke-width="2"/>
            <text x="130" y="66" text-anchor="middle" font-size="12.5" font-weight="700" fill="#6b3a00">.1 to .99: static and excluded</text>
            <text x="313" y="66" text-anchor="middle" font-size="12.5" font-weight="700" fill="#14663f">pool .100-.150</text>
            <text x="502" y="66" text-anchor="middle" font-size="12.5" font-weight="700" fill="#33406b">.151 to .254: spare</text>
            <text x="130" y="104" text-anchor="middle" font-size="12" font-weight="500" fill="#6b3a00">router, servers, printers</text>
            <text x="313" y="104" text-anchor="middle" font-size="12" font-weight="500" fill="#14663f">51 addresses</text>
            <text x="502" y="104" text-anchor="middle" font-size="12" font-weight="500" fill="#33406b">room to grow</text>
            <rect x="60" y="130" width="540" height="62" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="330.0" y="158.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">Options handed out with every lease</text>
            <text x="330.0" y="177.0" text-anchor="middle" font-size="12.5" fill="#3b2a8a">mask 255.255.255.0 · gateway 192.168.1.1 · DNS 192.168.1.2 · lease time</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. A pool has 10 addresses and 12 clients ask. What happens to the last two?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They share addresses</li>
            <li class="quiz-option quiz-correct">B. They get no offer and stay without an address ✓</li>
            <li class="quiz-option">C. The server makes new addresses</li>
            <li class="quiz-option">D. They get public addresses</li>
        </ul>
        <p class="quiz-explanation"><em>When the pool is exhausted the server has nothing to offer, so the extra clients remain unaddressed.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which DHCP option gives clients their way out to other networks?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. DNS server</li>
            <li class="quiz-option">B. Lease time</li>
            <li class="quiz-option quiz-correct">C. Default gateway (router) ✓</li>
            <li class="quiz-option">D. Pool start</li>
        </ul>
        <p class="quiz-explanation"><em>The default gateway option tells the client which router to use to leave its subnet.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why keep some addresses out of the pool?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. So they can be used for static devices such as servers without conflicts ✓</li>
            <li class="quiz-option">B. To make DHCP slower</li>
            <li class="quiz-option">C. To hide them from the router</li>
            <li class="quiz-option">D. To save electricity</li>
        </ul>
        <p class="quiz-explanation"><em>Static addresses inside the pool could be leased to someone else, causing an address conflict.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> fix the pool size, then make the server hand out a gateway and a DNS server. <strong>Success:</strong> all three consoles lease addresses, astro has a default route via <code>192.168.1.1</code>, and astro can look up <code>codexia.lan</code>.
    </p>

    <div class="relay-lab" data-lab="m3-l4">
        <iframe src="{{ asset('netsim-app/app.html') }}?lab=m3-l4" title="Relay Lab: size the pool and set the options" loading="lazy"></iframe>
        <p class="relay-lab-note">
            Double-click a device to open its terminal. The simulator uses Linux commands. The ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
