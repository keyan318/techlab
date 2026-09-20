{{-- M1 · Lesson 1.1: First Contact — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Crew / Diagram / Quiz / Relay Lab).
The Relay Lab is the NetSim simulator; passing it (see public/netsim-app/labs/m1-l1.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="01">

    <h1 class="lesson-heading">Lesson 1.1: First Contact</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what a network is, name the five parts of every
        data communication, and build a two-device network that can talk to itself.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>network</strong> is two or more devices connected so they can share information.
        Your phone, your laptop and your home router are already a small network.
    </p>

    <p class="body-text">
        Every time two devices talk, five things are involved. This is called <strong>data communication</strong>:
    </p>

    <ul class="body-list">
        <li><strong>Sender</strong>: the device that starts the conversation.</li>
        <li><strong>Receiver</strong>: the device the message is meant for.</li>
        <li><strong>Message</strong>: the information itself (text, a picture, a video).</li>
        <li><strong>Medium</strong>: the path the message travels on (a cable, radio waves, light in a fiber).</li>
        <li><strong>Protocol</strong>: the agreed rules, so both sides understand each other.</li>
    </ul>

    <p class="body-text">
        Some words you will hear all course: a <strong>host</strong> is a device on the network,
        a <strong>link</strong> is the connection between two devices, and an
        <strong>address</strong> is what lets other devices find one host among many.
    </p>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        The crash scattered Codexia's comms gear across the landing site, and the sky is full of ships from
        Planet Doom. <strong>Astro</strong> makes the call: "We cannot reach Earth until we can reach each other.
        Rivet, connect my console to your workbench."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> holds up two dead consoles. "Two devices, one relay switch in the middle. But
        they have no addresses yet, so neither one can be found. Give each one an address and we are a network."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 190" role="img" aria-label="Sender sends a message over a medium to a receiver, following a protocol">
            <rect x="20" y="55" width="140" height="80" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="90" y="88" text-anchor="middle" font-size="17" font-weight="700" fill="#14306b">Sender</text>
            <text x="90" y="112" text-anchor="middle" font-size="14" fill="#14306b">Astro's console</text>

            <rect x="480" y="55" width="140" height="80" rx="14" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="550" y="88" text-anchor="middle" font-size="17" font-weight="700" fill="#14663f">Receiver</text>
            <text x="550" y="112" text-anchor="middle" font-size="14" fill="#14663f">Rivet's workbench</text>

            <line x1="160" y1="95" x2="480" y2="95" stroke="#7c5cff" stroke-width="4"/>
            <polygon points="480,95 464,86 464,104" fill="#7c5cff"/>
            <text x="320" y="82" text-anchor="middle" font-size="15" font-weight="700" fill="#4a35b8">Medium (cable, radio, fiber)</text>

            <rect x="250" y="106" width="140" height="30" rx="15" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="126" text-anchor="middle" font-size="14" font-weight="700" fill="#8a4b00">Message</text>

            <rect x="180" y="150" width="280" height="30" rx="15" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="170" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">Protocol: the rules both sides follow</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which part of data communication is the set of agreed rules?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The medium</li>
            <li class="quiz-option">B. The sender</li>
            <li class="quiz-option quiz-correct">C. The protocol ✓</li>
            <li class="quiz-option">D. The message</li>
        </ul>
        <p class="quiz-explanation"><em>A protocol is the shared set of rules, like a language both devices speak. The medium is only the path the message travels on.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Astro's console sends a picture to Rivet's workbench over a cable. What is the cable?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The receiver</li>
            <li class="quiz-option quiz-correct">B. The medium ✓</li>
            <li class="quiz-option">C. The message</li>
            <li class="quiz-option">D. The protocol</li>
        </ul>
        <p class="quiz-explanation"><em>The medium is the path the message travels on. It can be a cable, radio waves or light in a fiber.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which of these is the smallest complete network?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. One laptop with no cables</li>
            <li class="quiz-option">B. One switch with nothing plugged in</li>
            <li class="quiz-option quiz-correct">C. Two devices connected so they can share information ✓</li>
            <li class="quiz-option">D. A single cable on a shelf</li>
        </ul>
        <p class="quiz-explanation"><em>A network needs at least two devices connected so they can share information.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> give <code>astro</code> and <code>rivet</code> each an address in
        <code>192.168.1.0/24</code>, then make each one answer the other's ping.
        <strong>Success:</strong> <code>astro</code> can ping <code>rivet</code> and <code>rivet</code> can ping <code>astro</code>.
    </p>

    <div class="relay-lab" data-lab="m1-l1">
        <iframe src="{{ asset('netsim-app/app.html') }}?lab=m1-l1" title="Relay Lab: build the first network" loading="lazy"></iframe>
        <p class="relay-lab-note">
            Double-click a device to open its terminal. The simulator uses Linux commands such as
            <code>ip addr add</code> and <code>ping</code>. The ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> when you are ready.
        </p>
    </div>

</div>
