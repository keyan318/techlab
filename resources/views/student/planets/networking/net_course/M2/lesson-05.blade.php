{{-- M2 · Lesson 2.5: Subnetting — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m2-l5.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="05">

    <h1 class="lesson-heading">Lesson 2.5: Subnetting</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can split a network into smaller subnets by borrowing host bits, and work out each subnet's range, broadcast address and host count.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Subnetting</strong> cuts one network into smaller ones. Reasons: less broadcast noise, better security, and no wasted addresses. You do it by <strong>borrowing bits</strong> from the host part and giving them to the network part, so the mask grows.
    </p>
    <p class="body-text">
        Two rules do the work. Borrow <em>n</em> bits and you get <strong>2<sup>n</sup> subnets</strong>. Each subnet has <em>2<sup>h</sup> − 2</em> usable hosts, where <em>h</em> is the host bits left.
    </p>
    <ul class="body-list">
        <li><code>/24</code> to <code>/25</code>: borrow 1 bit, 2 subnets, 126 hosts each.</li>
        <li><code>/24</code> to <code>/26</code>: borrow 2 bits, 4 subnets, 62 hosts each.</li>
        <li><code>/24</code> to <code>/27</code>: borrow 3 bits, 8 subnets, 30 hosts each.</li>
        <li><code>/24</code> to <code>/28</code>: borrow 4 bits, 16 subnets, 14 hosts each.</li>
    </ul>
    <p class="body-text">
        A shortcut: the <strong>block size</strong> is 256 minus the last mask octet value. For <code>/25</code> the mask is 255.255.255.128, so the block size is 128 and the subnets start at .0 and .128. Devices in different subnets can only talk through a router.
    </p>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        Earth hands the crew one address block, <code>192.168.10.0/24</code>, and no more. <strong>Rivet</strong> draws a line down the middle. "Half for the comms deck, half for the shields. Two <code>/25</code> subnets, joined by the router."
    </p>
    <p class="body-text">
        <strong>Volt</strong> approves. "Separate blocks mean a jammer in one deck cannot flood the other."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 240" role="img" aria-label="A /24 block split into two /25 subnets">
            <text x="330" y="20" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">One /24 block: 192.168.10.0/24 = 256 addresses</text>
            <rect x="20" y="34" width="310" height="52" rx="10" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <rect x="330" y="34" width="310" height="52" rx="10" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="175" y="66" text-anchor="middle" font-size="16" font-weight="700" fill="#14306b">192.168.10.0/25</text>
            <text x="485" y="66" text-anchor="middle" font-size="16" font-weight="700" fill="#14663f">192.168.10.128/25</text>
            <text x="175" y="116" text-anchor="middle" font-size="13" font-weight="600" fill="#14306b">hosts .1 to .126</text>
            <text x="175" y="134" text-anchor="middle" font-size="13" font-weight="500" fill="#14306b">broadcast .127</text>
            <text x="485" y="116" text-anchor="middle" font-size="13" font-weight="600" fill="#14663f">hosts .129 to .254</text>
            <text x="485" y="134" text-anchor="middle" font-size="13" font-weight="500" fill="#14663f">broadcast .255</text>
            <text x="175" y="168" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">Comms deck (astro)</text>
            <text x="485" y="168" text-anchor="middle" font-size="14" font-weight="700" fill="#14663f">Shield deck (rivet)</text>
            <rect x="60" y="186" width="540" height="40" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="330.0" y="211.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">Borrow 1 host bit: /24 becomes /25, 2 subnets of 126 hosts</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. You borrow 2 bits from a /24 network. What is the new mask and how many subnets do you get?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. /25 and 2</li>
            <li class="quiz-option quiz-correct">B. /26 and 4 ✓</li>
            <li class="quiz-option">C. /27 and 8</li>
            <li class="quiz-option">D. /26 and 2</li>
        </ul>
        <p class="quiz-explanation"><em>Borrowing 2 bits makes the mask /26 and gives 2 to the power of 2, which is 4 subnets.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. How many usable hosts does a /26 subnet have?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 64</li>
            <li class="quiz-option quiz-correct">B. 62 ✓</li>
            <li class="quiz-option">C. 32</li>
            <li class="quiz-option">D. 30</li>
        </ul>
        <p class="quiz-explanation"><em>A /26 leaves 6 host bits. 2 to the power of 6 is 64, minus the network and broadcast addresses, leaves 62.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which /25 subnet does the address 192.168.10.140 belong to?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 192.168.10.0/25</li>
            <li class="quiz-option">B. 192.168.10.64/25</li>
            <li class="quiz-option quiz-correct">C. 192.168.10.128/25 ✓</li>
            <li class="quiz-option">D. 192.168.10.192/25</li>
        </ul>
        <p class="quiz-explanation"><em>/25 subnets start at .0 and .128. 140 is between 128 and 255, so it belongs to 192.168.10.128/25.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> split <code>192.168.10.0/24</code> into two <code>/25</code> subnets and connect them with the router. <strong>Success:</strong> each device has an address in its own subnet, and astro can ping rivet through <code>gate</code>.
    </p>

    <div class="relay-lab" data-lab="m2-l5">
        <iframe src="{{ asset('netsim-app/app.html') }}?lab=m2-l5" title="Relay Lab: split the block" loading="lazy"></iframe>
        <p class="relay-lab-note">
            Double-click a device to open its terminal. The simulator uses Linux commands. The ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
