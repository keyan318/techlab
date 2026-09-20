{{-- M4 · Lesson 4.2: Physical Issues — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m4-l2.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="02">

    <h1 class="lesson-heading">Lesson 4.2: Physical Issues</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can recognize the symptoms of physical (Layer 1) problems, name the usual causes, and work out whether a fault affects one device or many.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Physical issues</strong> live at Layer 1: the cables, connectors, ports, network cards, power and the signal itself. They are the most common cause of "the network is down", and they are checked first because nothing above them can work.
    </p>
    <p class="body-text">
        Typical causes: a cable that is <strong>unplugged</strong> or <strong>damaged</strong>; a <strong>wrong cable type</strong> or a run longer than about <strong>100 meters</strong> of copper; <strong>interference</strong> from power cables or motors; <strong>bad connectors</strong>; a <strong>dead port</strong> or network card; <strong>no power</strong>; and a <strong>speed or duplex mismatch</strong> between two ends.
    </p>
    <p class="body-text">
        How to find them:
    </p>
    <ul class="body-list">
        <li>Look at the <strong>link lights</strong> on the port and the card. No light usually means no link.</li>
        <li>Use a <strong>cable tester</strong> to check a cable, and a <strong>loopback plug</strong> to test a port or card.</li>
        <li>Do a <strong>swap test</strong>: change one part at a time (cable, port, device) and see if the fault moves.</li>
        <li>Ask <strong>how many devices are affected</strong>. One device points at that device or its cable. Many devices point at a shared part such as a switch or an uplink.</li>
    </ul>
    <p class="body-text">
        On Linux, <code>ip link</code> shows whether an interface is <code>UP</code> or <code>DOWN</code>, and <code>ip link set eth0 up</code> brings it up.
    </p>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        After the last raid, two consoles have gone dark. <strong>Astro</strong> and <strong>Volt</strong> both report the file server is unreachable, and Volt is sure the network itself is broken.
    </p>
    <p class="body-text">
        <strong>Rivet</strong> tests the obvious first. "Astro and Volt can still see each other, so the switch and their own cables are fine. That means the fault is at the far end. Check the links on the devices nobody can reach."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 216" role="img" aria-label="Common physical symptoms and their likely causes">
            <rect x="10" y="14" width="640" height="32" rx="6" fill="#14306b" stroke="#d5def5"/>
            <text x="24" y="35" text-anchor="start" font-size="13" font-weight="700" fill="#ffffff">Symptom</text>
            <text x="260" y="35" text-anchor="start" font-size="13" font-weight="700" fill="#ffffff">Likely physical cause</text>
            <rect x="10" y="48" width="640" height="32" rx="6" fill="#f3f6ff" stroke="#d5def5"/>
            <text x="24" y="69" text-anchor="start" font-size="13" font-weight="700" fill="#14306b">No link light on the port</text>
            <text x="260" y="69" text-anchor="start" font-size="13" font-weight="500" fill="#14306b">cable unplugged or damaged, dead port, dead network card</text>
            <rect x="10" y="82" width="640" height="32" rx="6" fill="#ffffff" stroke="#d5def5"/>
            <text x="24" y="103" text-anchor="start" font-size="13" font-weight="700" fill="#14306b">Works, then drops out</text>
            <text x="260" y="103" text-anchor="start" font-size="13" font-weight="500" fill="#14306b">loose connector, damaged cable, interference</text>
            <rect x="10" y="116" width="640" height="32" rx="6" fill="#f3f6ff" stroke="#d5def5"/>
            <text x="24" y="137" text-anchor="start" font-size="13" font-weight="700" fill="#14306b">Slow, with many errors</text>
            <text x="260" y="137" text-anchor="start" font-size="13" font-weight="500" fill="#14306b">bad cable, speed or duplex mismatch, interference</text>
            <rect x="10" y="150" width="640" height="32" rx="6" fill="#ffffff" stroke="#d5def5"/>
            <text x="24" y="171" text-anchor="start" font-size="13" font-weight="700" fill="#14306b">Fine nearby, bad far away</text>
            <text x="260" y="171" text-anchor="start" font-size="13" font-weight="500" fill="#14306b">copper cable run longer than about 100 m</text>
            <rect x="10" y="184" width="640" height="32" rx="6" fill="#f3f6ff" stroke="#d5def5"/>
            <text x="24" y="205" text-anchor="start" font-size="13" font-weight="700" fill="#14306b">Nothing lights up at all</text>
            <text x="260" y="205" text-anchor="start" font-size="13" font-weight="500" fill="#14306b">no power: adapter, power strip, PoE</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which check comes first when a device cannot reach anything and its link light is off?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Reinstall the operating system</li>
            <li class="quiz-option quiz-correct">B. The cable, port and link ✓</li>
            <li class="quiz-option">C. The DNS server</li>
            <li class="quiz-option">D. The firewall rules</li>
        </ul>
        <p class="quiz-explanation"><em>Start at Layer 1. With no link light there is nothing for higher layers to work with.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. A copper Ethernet run is 150 m long and the connection is unreliable. What is the likely cause?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The run is longer than the roughly 100 m limit ✓</li>
            <li class="quiz-option">B. The IP address is wrong</li>
            <li class="quiz-option">C. DHCP is off</li>
            <li class="quiz-option">D. The router has no gateway</li>
        </ul>
        <p class="quiz-explanation"><em>Copper Ethernet is limited to about 100 m per run. Longer needs fiber or a repeater/switch in between.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is a swap test?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Changing one part at a time to see whether the fault moves ✓</li>
            <li class="quiz-option">B. Swapping IP addresses between two devices</li>
            <li class="quiz-option">C. Replacing the whole network</li>
            <li class="quiz-option">D. Swapping the SSID of a router</li>
        </ul>
        <p class="quiz-explanation"><em>Swap one part (cable, port or device) and watch whether the problem follows it. That isolates the faulty part.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> find and repair the dead links. <strong>Success:</strong> astro reaches rivet and the file server, volt reaches the file server, and astro and volt still reach each other.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m4-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm4', 'lesson' => 'lesson02']) }}">
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
