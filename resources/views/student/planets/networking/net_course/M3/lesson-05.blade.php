{{-- M3 · Lesson 3.5: DNS Overview — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m3-l5.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="05">

    <h1 class="lesson-heading">Lesson 3.5: DNS Overview</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what DNS does, describe how a name is resolved, name the common record types, and set up a small DNS server.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        People remember names, and networks route with numbers. The <strong>Domain Name System (DNS)</strong> is the phone book that turns a name such as <code>portal.codexia.lan</code> into an IP address. DNS uses <strong>UDP port 53</strong> (and TCP for large answers).
    </p>
    <p class="body-text">
        A name reads from right to left as a hierarchy: <code>.lan</code> is the top level, <code>codexia</code> is the domain, and <code>portal</code> is the host. When a client wants an address it asks its <strong>resolver</strong> (the DNS server it was told about, often by DHCP). The resolver may go to a <strong>root server</strong>, then the <strong>TLD server</strong>, then the <strong>authoritative server</strong> that owns the answer, and finally hands the result back. Resolvers <strong>cache</strong> answers for a time called the <strong>TTL</strong>, so repeat lookups are fast.
    </p>
    <p class="body-text">
        A DNS server stores <strong>records</strong>. The common types:
    </p>
    <ul class="body-list">
        <li><strong>A</strong>: name to IPv4 address.</li>
        <li><strong>AAAA</strong>: name to IPv6 address.</li>
        <li><strong>CNAME</strong>: an alias that points to another name.</li>
        <li><strong>MX</strong>: which server handles mail for the domain.</li>
        <li><strong>NS</strong>: which servers are authoritative for the domain.</li>
        <li><strong>PTR</strong>: address back to name (reverse lookup).</li>
    </ul>
    <p class="body-text">
        If a name does not exist the server answers <strong>NXDOMAIN</strong>. <em>The simulator supports A records only.</em>
    </p>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        The crew's consoles now come online by themselves. <strong>Astro</strong> wants the next step. "Nobody on this deck should have to remember a number. The mission portal should just be <em>portal</em>."
    </p>
    <p class="body-text">
        <strong>Rivet</strong> switches the name service on. <strong>Volt</strong> adds one rule. "And a name that does not exist must fail cleanly, so nobody can be tricked into trusting a wrong answer."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 224" role="img" aria-label="A DNS lookup: the client asks the resolver, which asks root, TLD and authoritative servers">
            <rect x="10" y="80" width="130" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="75.0" y="107.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Client</text>
            <text x="75.0" y="126.0" text-anchor="middle" font-size="12.5" fill="#14306b">astro</text>
            <rect x="200" y="80" width="150" height="60" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="275.0" y="107.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">DNS resolver</text>
            <text x="275.0" y="126.0" text-anchor="middle" font-size="12.5" fill="#14663f">ship-server</text>
            <rect x="450" y="8" width="200" height="52" rx="12" fill="#eceff7" stroke="#6b7699" stroke-width="2"/>
            <text x="550.0" y="31.0" text-anchor="middle" font-size="15" font-weight="700" fill="#33406b">Root servers</text>
            <text x="550.0" y="50.0" text-anchor="middle" font-size="12.5" fill="#33406b">who runs .lan?</text>
            <rect x="450" y="80" width="200" height="60" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="550.0" y="107.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">TLD server (.lan)</text>
            <text x="550.0" y="126.0" text-anchor="middle" font-size="12.5" fill="#3b2a8a">who runs codexia.lan?</text>
            <rect x="450" y="160" width="200" height="52" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="550.0" y="183.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">Authoritative server</text>
            <text x="550.0" y="202.0" text-anchor="middle" font-size="12.5" fill="#6b3a00">portal = 192.168.1.2</text>
            <line x1="140" y1="110" x2="200" y2="110" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="350" y1="100" x2="450" y2="34" stroke="#6b7699" stroke-width="2.5" stroke-linecap="round"/>
            <line x1="350" y1="110" x2="450" y2="110" stroke="#7c5cff" stroke-width="2.5" stroke-linecap="round"/>
            <line x1="350" y1="122" x2="450" y2="186" stroke="#f08c1a" stroke-width="2.5" stroke-linecap="round"/>
            <text x="170" y="74" text-anchor="middle" font-size="12" font-weight="700" fill="#14306b">1  portal.codexia.lan?</text>
            <text x="170" y="158" text-anchor="middle" font-size="12" font-weight="700" fill="#14306b">6  192.168.1.2</text>
            <text x="396" y="58" text-anchor="middle" font-size="12" font-weight="700" fill="#33406b">2</text>
            <text x="400" y="104" text-anchor="middle" font-size="12" font-weight="700" fill="#3b2a8a">3</text>
            <text x="396" y="160" text-anchor="middle" font-size="12" font-weight="700" fill="#6b3a00">4-5</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does a DNS server return for the name portal.codexia.lan?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The IP address of that host ✓</li>
            <li class="quiz-option">B. The MAC address of the switch</li>
            <li class="quiz-option">C. The lease time</li>
            <li class="quiz-option">D. The subnet mask</li>
        </ul>
        <p class="quiz-explanation"><em>DNS maps names to IP addresses.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which record type maps a name to an IPv4 address?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. MX</li>
            <li class="quiz-option quiz-correct">B. A ✓</li>
            <li class="quiz-option">C. CNAME</li>
            <li class="quiz-option">D. PTR</li>
        </ul>
        <p class="quiz-explanation"><em>An A record maps a name to an IPv4 address. AAAA is the IPv6 version.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. A DNS server answers NXDOMAIN. What does that mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The name does not exist ✓</li>
            <li class="quiz-option">B. The server is on fire</li>
            <li class="quiz-option">C. The name is IPv6</li>
            <li class="quiz-option">D. The lookup was slow</li>
        </ul>
        <p class="quiz-explanation"><em>NXDOMAIN means the queried name does not exist in DNS.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> get astro online with DHCP, then turn the ship server into a name server. <strong>Success:</strong> astro resolves <code>portal.codexia.lan</code> to <code>192.168.1.2</code>, an unknown name gets NXDOMAIN, and astro opens the portal page by name.
    </p>

    <div class="relay-lab" data-lab="m3-l5">
        <iframe src="{{ asset('netsim-app/app.html') }}?lab=m3-l5" title="Relay Lab: give the crew names" loading="lazy"></iframe>
        <p class="relay-lab-note">
            Double-click a device to open its terminal. The simulator uses Linux commands. The ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
