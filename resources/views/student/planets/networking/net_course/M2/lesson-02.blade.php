{{-- M2 · Lesson 2.2: Protocols &amp; Standards — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m2-l2.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="02">

    <h1 class="lesson-heading">Lesson 2.2: Protocols &amp; Standards</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain why standards matter, match common protocols to their ports, and tell TCP from UDP.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>standard</strong> is a rule everyone agrees on, so devices from different makers can work together. Groups like the <strong>IEEE</strong> (Ethernet is 802.3, Wi-Fi is 802.11) and the <strong>IETF</strong> (which publishes the <strong>RFC</strong> documents behind IP, TCP, HTTP and DNS) write them.
    </p>
    <p class="body-text">
        Each service listens on a <strong>port</strong>, a number that says which program on a device should get the data. The common ones are called <strong>well-known ports</strong> (0 to 1023). The table below lists the ones you will meet most.
    </p>
    <p class="body-text">
        Two Transport protocols carry most traffic:
    </p>
    <ul class="body-list">
        <li><strong>TCP</strong> sets up a connection (a handshake), numbers the data and resends anything lost. Reliable, used for web pages, email and file transfer.</li>
        <li><strong>UDP</strong> just sends. No handshake and no guarantee, but it is fast and light. Used for DNS queries, live video and voice.</li>
    </ul>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        Earth's relay is strict: it only accepts standard protocols on their standard ports. <strong>Rivet</strong> reads the list aloud. "Web on 80, secure login on 22."
    </p>
    <p class="body-text">
        <strong>Volt</strong> holds up a hand. "Not Telnet on 23. It sends passwords as plain text, and Doom's scouts are listening. That port stays sealed."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 258" role="img" aria-label="Common protocols with their ports and what they do">
            <rect x="10" y="14" width="640" height="32" rx="6" fill="#14306b" stroke="#d5def5"/>
            <text x="26" y="35" text-anchor="start" font-size="13.5" font-weight="700" fill="#ffffff">Protocol</text>
            <text x="176" y="35" text-anchor="start" font-size="13.5" font-weight="700" fill="#ffffff">Port</text>
            <text x="266" y="35" text-anchor="start" font-size="13.5" font-weight="700" fill="#ffffff">Transport</text>
            <text x="420" y="35" text-anchor="start" font-size="13.5" font-weight="700" fill="#ffffff">What it does</text>
            <rect x="10" y="48" width="640" height="32" rx="6" fill="#f3f6ff" stroke="#d5def5"/>
            <text x="26" y="69" text-anchor="start" font-size="13.5" font-weight="700" fill="#14306b">HTTP</text>
            <text x="176" y="69" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">80</text>
            <text x="266" y="69" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">TCP</text>
            <text x="420" y="69" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">web pages</text>
            <rect x="10" y="82" width="640" height="32" rx="6" fill="#ffffff" stroke="#d5def5"/>
            <text x="26" y="103" text-anchor="start" font-size="13.5" font-weight="700" fill="#14306b">HTTPS</text>
            <text x="176" y="103" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">443</text>
            <text x="266" y="103" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">TCP</text>
            <text x="420" y="103" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">secure web pages</text>
            <rect x="10" y="116" width="640" height="32" rx="6" fill="#f3f6ff" stroke="#d5def5"/>
            <text x="26" y="137" text-anchor="start" font-size="13.5" font-weight="700" fill="#14306b">SSH</text>
            <text x="176" y="137" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">22</text>
            <text x="266" y="137" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">TCP</text>
            <text x="420" y="137" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">secure remote login</text>
            <rect x="10" y="150" width="640" height="32" rx="6" fill="#ffffff" stroke="#d5def5"/>
            <text x="26" y="171" text-anchor="start" font-size="13.5" font-weight="700" fill="#14306b">DNS</text>
            <text x="176" y="171" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">53</text>
            <text x="266" y="171" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">UDP (and TCP)</text>
            <text x="420" y="171" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">names to IP addresses</text>
            <rect x="10" y="184" width="640" height="32" rx="6" fill="#f3f6ff" stroke="#d5def5"/>
            <text x="26" y="205" text-anchor="start" font-size="13.5" font-weight="700" fill="#14306b">DHCP</text>
            <text x="176" y="205" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">67 / 68</text>
            <text x="266" y="205" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">UDP</text>
            <text x="420" y="205" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">hands out addresses</text>
            <rect x="10" y="218" width="640" height="32" rx="6" fill="#ffffff" stroke="#d5def5"/>
            <text x="26" y="239" text-anchor="start" font-size="13.5" font-weight="700" fill="#14306b">ICMP</text>
            <text x="176" y="239" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">none</text>
            <text x="266" y="239" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">(IP itself)</text>
            <text x="420" y="239" text-anchor="start" font-size="13.5" font-weight="500" fill="#14306b">ping and error messages</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which protocol turns a name like codexia.lan into an IP address?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. DHCP</li>
            <li class="quiz-option">B. ICMP</li>
            <li class="quiz-option">C. SSH</li>
            <li class="quiz-option quiz-correct">D. DNS ✓</li>
        </ul>
        <p class="quiz-explanation"><em>DNS (Domain Name System) looks names up and returns IP addresses.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which Transport protocol is fast but gives no delivery guarantee?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. TCP</li>
            <li class="quiz-option quiz-correct">B. UDP ✓</li>
            <li class="quiz-option">C. HTTPS</li>
            <li class="quiz-option">D. SSH</li>
        </ul>
        <p class="quiz-explanation"><em>UDP sends without a handshake or retransmission. That makes it quick, and it is why DNS queries and live video use it.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why does Volt keep Telnet (port 23) closed?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is too fast</li>
            <li class="quiz-option quiz-correct">B. It sends passwords as plain text ✓</li>
            <li class="quiz-option">C. It uses port 80</li>
            <li class="quiz-option">D. Standards forbid it</li>
        </ul>
        <p class="quiz-explanation"><em>Telnet does not encrypt anything, so anyone listening can read the password. SSH is the secure replacement.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> open the standard ports on <code>ship-server</code> and keep Telnet sealed. <strong>Success:</strong> TCP 80 and TCP 22 are open, and TCP 23 stays closed.
    </p>

    <div class="relay-lab" data-lab="m2-l2">
        <iframe src="{{ asset('netsim-app/app.html') }}?lab=m2-l2" title="Relay Lab: open the standard ports" loading="lazy"></iframe>
        <p class="relay-lab-note">
            Double-click a device to open its terminal. The simulator uses Linux commands. The ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
