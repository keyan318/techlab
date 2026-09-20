{{-- M3 · Lesson 3.1: Static and Dynamic Addressing — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m3-l1.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="01">

    <h1 class="lesson-heading">Lesson 3.1: Static and Dynamic Addressing</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can compare static and dynamic addressing, choose the right one for a device, and set up a DHCP server so the crew gets addresses automatically.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Every device on an IP network needs an address. There are two ways to give it one.
    </p>
    <p class="body-text">
        <strong>Static addressing</strong> means you type the address into the device yourself. It never changes, so other devices can always find it. The cost is effort, and a typo can put a device on the wrong network or give two devices the same address (an <strong>address conflict</strong>).
    </p>
    <p class="body-text">
        <strong>Dynamic addressing</strong> means a <strong>DHCP server</strong> (Dynamic Host Configuration Protocol) hands out an address automatically when a device joins. It is quick, avoids conflicts, and can also hand out the gateway and the DNS server. The address may be different next time.
    </p>
    <p class="body-text">
        A simple rule: give <strong>static</strong> addresses to things other devices must find (servers, routers, printers) and <strong>dynamic</strong> addresses to everything that just needs to get online (laptops, phones, workstations). If a device expects DHCP but finds no server, it may give itself an address that starts with <code>169.254</code>. That is a sign DHCP failed. Many networks also use a <strong>reservation</strong>: DHCP always gives the same address to one device, chosen by its MAC address.
    </p>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        Another raid. Every console on the ship reboots, and every address is gone. <strong>Rivet</strong> is on his fourth console. "Typing addresses by hand does not scale. Two more raids and I will still be typing."
    </p>
    <p class="body-text">
        <strong>Astro</strong> makes a rule. "The server keeps a static address, because everyone must find it. Everyone else gets an address automatically. Set up DHCP."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 224" role="img" aria-label="Static addressing compared with dynamic addressing">
            <rect x="10" y="14" width="310" height="196" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="165" y="44" text-anchor="middle" font-size="17" font-weight="700" fill="#6b3a00">Static (typed by hand)</text>
            <text x="165" y="76" text-anchor="middle" font-size="13" font-weight="500" fill="#6b3a00">You choose every address</text>
            <text x="165" y="101" text-anchor="middle" font-size="13" font-weight="500" fill="#6b3a00">The address never changes</text>
            <text x="165" y="126" text-anchor="middle" font-size="13" font-weight="500" fill="#6b3a00">Easy to find, but slow to set up</text>
            <text x="165" y="151" text-anchor="middle" font-size="13" font-weight="500" fill="#6b3a00">Typos can cause conflicts</text>
            <rect x="30" y="166" width="270" height="32" rx="16" fill="#ffffff" stroke="#f08c1a"/>
            <text x="165" y="187" text-anchor="middle" font-size="12.5" font-weight="700" fill="#6b3a00">Use for: servers, routers, printers</text>
            <rect x="340" y="14" width="310" height="196" rx="14" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="495" y="44" text-anchor="middle" font-size="17" font-weight="700" fill="#14663f">Dynamic (DHCP)</text>
            <text x="495" y="76" text-anchor="middle" font-size="13" font-weight="500" fill="#14663f">A server hands out addresses</text>
            <text x="495" y="101" text-anchor="middle" font-size="13" font-weight="500" fill="#14663f">The address can change</text>
            <text x="495" y="126" text-anchor="middle" font-size="13" font-weight="500" fill="#14663f">Fast to set up, fewer mistakes</text>
            <text x="495" y="151" text-anchor="middle" font-size="13" font-weight="500" fill="#14663f">Needs a DHCP server running</text>
            <rect x="360" y="166" width="270" height="32" rx="16" fill="#ffffff" stroke="#2fa56b"/>
            <text x="495" y="187" text-anchor="middle" font-size="12.5" font-weight="700" fill="#14663f">Use for: laptops, phones, consoles</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which device should normally have a static address?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A visitor's phone</li>
            <li class="quiz-option quiz-correct">B. A web server that everyone must find ✓</li>
            <li class="quiz-option">C. A laptop that moves between rooms</li>
            <li class="quiz-option">D. A tablet</li>
        </ul>
        <p class="quiz-explanation"><em>Other devices must always be able to find a server, so its address must not change. Portable devices can take dynamic addresses.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is a main benefit of dynamic addressing?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Addresses never change</li>
            <li class="quiz-option quiz-correct">B. Less manual work and fewer typing mistakes ✓</li>
            <li class="quiz-option">C. No server is needed</li>
            <li class="quiz-option">D. It makes cables faster</li>
        </ul>
        <p class="quiz-explanation"><em>DHCP does the work automatically, so there are fewer typos and address conflicts. It does need a DHCP server.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does an address that starts with 169.254 usually mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The device got an address from DHCP</li>
            <li class="quiz-option quiz-correct">B. The device could not reach a DHCP server ✓</li>
            <li class="quiz-option">C. It is a public address</li>
            <li class="quiz-option">D. It is a router</li>
        </ul>
        <p class="quiz-explanation"><em>169.254.x.x is a self-assigned address. A device uses it when it expected DHCP but no server answered.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> give the server a static address, then let it hand out addresses to the crew. <strong>Success:</strong> <code>ship-server</code> has <code>192.168.1.2/24</code>, and astro, rivet and volt all get addresses automatically and can reach each other.
    </p>

    <div class="relay-lab" data-lab="m3-l1">
        <iframe src="{{ asset('netsim-app/app.html') }}?lab=m3-l1" title="Relay Lab: static server, dynamic crew" loading="lazy"></iframe>
        <p class="relay-lab-note">
            Double-click a device to open its terminal. The simulator uses Linux commands. The ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
