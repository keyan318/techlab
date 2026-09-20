{{-- M3 · Lesson 3.2: The DHCP Lease Process — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m3-l2.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="02">

    <h1 class="lesson-heading">Lesson 3.2: The DHCP Lease Process</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can name the four DHCP steps in order, explain why the first one is a broadcast, and read what a lease contains.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A device that joins a network runs the <strong>DHCP lease process</strong>. It has four steps, remembered as <strong>DORA</strong>:
    </p>
    <ul class="body-list">
        <li><strong>Discover</strong>: the client has no address and does not know any server, so it <em>broadcasts</em> "is there a DHCP server?" to everyone.</li>
        <li><strong>Offer</strong>: a DHCP server replies with an address it is willing to lend, plus settings such as the mask, gateway and DNS server.</li>
        <li><strong>Request</strong>: the client answers "I will take that one." This is also broadcast, so any other server that made an offer knows it was not chosen.</li>
        <li><strong>Acknowledge</strong>: the server confirms. The address is now the client's, and the lease starts.</li>
    </ul>
    <p class="body-text">
        DHCP uses <strong>UDP</strong> port <strong>67</strong> on the server and port <strong>68</strong> on the client.
    </p>
    <p class="body-text">
        The address is <strong>leased</strong>, not given for good. The lease has a length. Halfway through, the client quietly asks the same server to <strong>renew</strong> it. If that server is gone, the client later broadcasts to any server, and if nobody answers by the end, the lease expires and the address is lost. The client can also <strong>release</strong> the address on purpose when it leaves. A client that never finds a server is left with no address, or a <code>169.254.x.x</code> one.
    </p>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        <strong>Rivet</strong> talks the crew through a hail on the radio. "Discover: 'Anyone there?' Offer: 'I have a slot for you.' Request: 'I will take it.' Acknowledge: 'It is yours, here are your settings.' Four short messages, and the console is online."
    </p>
    <p class="body-text">
        <strong>Volt</strong> watches the first attempt fail. "If the first message gets no answer, the server is either off or not on this network. Check that before anything else."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 322" role="img" aria-label="The four DHCP steps: Discover, Offer, Request, Acknowledge">
            <rect x="30" y="14" width="160" height="40" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="110.0" y="39.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Client</text>
            <rect x="470" y="14" width="160" height="40" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="550.0" y="39.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">DHCP server</text>
            <line x1="110" y1="54" x2="110" y2="304" stroke="#9db7e8" stroke-width="2" stroke-dasharray="6 5"/>
            <line x1="550" y1="54" x2="550" y2="304" stroke="#9db7e8" stroke-width="2" stroke-dasharray="6 5"/>
            <line x1="110" y1="100" x2="550" y2="100" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <polygon points="550,100 538,93 538,107" fill="#2f6fe0"/>
            <text x="330.0" y="88" text-anchor="middle" font-size="14" font-weight="700" fill="#2f6fe0">1  DISCOVER</text>
            <text x="330.0" y="121" text-anchor="middle" font-size="12" font-weight="500" fill="#33406b">broadcast: is there a DHCP server?</text>
            <line x1="550" y1="166" x2="110" y2="166" stroke="#2fa56b" stroke-width="3" stroke-linecap="round"/>
            <polygon points="110,166 122,159 122,173" fill="#2fa56b"/>
            <text x="330.0" y="154" text-anchor="middle" font-size="14" font-weight="700" fill="#2fa56b">2  OFFER</text>
            <text x="330.0" y="187" text-anchor="middle" font-size="12" font-weight="500" fill="#33406b">I can give you 192.168.1.100</text>
            <line x1="110" y1="232" x2="550" y2="232" stroke="#2f6fe0" stroke-width="3" stroke-linecap="round"/>
            <polygon points="550,232 538,225 538,239" fill="#2f6fe0"/>
            <text x="330.0" y="220" text-anchor="middle" font-size="14" font-weight="700" fill="#2f6fe0">3  REQUEST</text>
            <text x="330.0" y="253" text-anchor="middle" font-size="12" font-weight="500" fill="#33406b">I will take 192.168.1.100</text>
            <line x1="550" y1="298" x2="110" y2="298" stroke="#2fa56b" stroke-width="3" stroke-linecap="round"/>
            <polygon points="110,298 122,291 122,305" fill="#2fa56b"/>
            <text x="330.0" y="286" text-anchor="middle" font-size="14" font-weight="700" fill="#2fa56b">4  ACK</text>
            <text x="330.0" y="319" text-anchor="middle" font-size="12" font-weight="500" fill="#33406b">Yours: mask, gateway, DNS, lease time</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What are the four DHCP steps in order?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Discover, Offer, Request, Acknowledge ✓</li>
            <li class="quiz-option">B. Request, Offer, Discover, Acknowledge</li>
            <li class="quiz-option">C. Offer, Discover, Acknowledge, Request</li>
            <li class="quiz-option">D. Acknowledge, Request, Offer, Discover</li>
        </ul>
        <p class="quiz-explanation"><em>DORA: Discover, Offer, Request, Acknowledge.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why is the client's first message (Discover) a broadcast?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. It has no address yet and does not know where the server is ✓</li>
            <li class="quiz-option">B. Broadcasts are more secure</li>
            <li class="quiz-option">C. DHCP servers only listen to broadcasts on Tuesdays</li>
            <li class="quiz-option">D. It saves power</li>
        </ul>
        <p class="quiz-explanation"><em>A new client has no IP address and does not know any server, so it shouts to everyone on the network.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which ports does DHCP use?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. UDP 67 (server) and UDP 68 (client) ✓</li>
            <li class="quiz-option">B. TCP 80 and TCP 443</li>
            <li class="quiz-option">C. UDP 53</li>
            <li class="quiz-option">D. TCP 22</li>
        </ul>
        <p class="quiz-explanation"><em>DHCP servers listen on UDP 67 and clients use UDP 68.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> watch a client fail, fix the server, and watch the four steps succeed. <strong>Success:</strong> astro and then rivet lease addresses, and each can reach the other.
    </p>

    <div class="relay-lab" data-lab="m3-l2">
        <iframe src="{{ asset('netsim-app/app.html') }}?lab=m3-l2" title="Relay Lab: run the DHCP lease process" loading="lazy"></iframe>
        <p class="relay-lab-note">
            Double-click a device to open its terminal. The simulator uses Linux commands. The ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
