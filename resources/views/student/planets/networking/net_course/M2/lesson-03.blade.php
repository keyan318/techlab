{{-- M2 · Lesson 2.3: IPv4 &amp; IPv6 — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m2-l3.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="03">

    <h1 class="lesson-heading">Lesson 2.3: IPv4 &amp; IPv6</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can read an IPv4 and an IPv6 address, name the private IPv4 ranges, and explain why IPv6 exists.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        An <strong>IPv4</strong> address is <strong>32 bits</strong>, written as four numbers from 0 to 255 separated by dots, such as <code>192.168.1.10</code>. Each number is an <strong>octet</strong> of 8 bits. That allows about 4.3 billion addresses, which is not enough for every phone, laptop and sensor in the world.
    </p>
    <p class="body-text">
        Some IPv4 ranges are <strong>private</strong>. Anyone can use them inside their own network, and the Internet does not route them: <code>10.0.0.0/8</code>, <code>172.16.0.0/12</code> and <code>192.168.0.0/16</code>. Home routers and this ship use them. Two more special ones: <code>127.0.0.1</code> is <strong>loopback</strong> (the device itself) and <code>169.254.x.x</code> means a device could not get an address automatically.
    </p>
    <p class="body-text">
        <strong>IPv6</strong> is the fix. An address is <strong>128 bits</strong>, written as eight groups of hexadecimal digits separated by colons. Leading zeros can be dropped and one run of all-zero groups can become <code>::</code>, so <code>2001:0db8:0000:0000:0000:0000:0000:0001</code> is written <code>2001:db8::1</code>. It gives an almost unlimited number of addresses, so every device can have its own.
    </p>
    <p class="body-text">
        IPv4 and IPv6 live side by side today, which is called <strong>dual stack</strong>. <em>Note: the lab simulator only speaks IPv4, so this lesson practises IPv4 addresses and covers IPv6 as concepts.</em>
    </p>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        <strong>Rivet</strong> squints at two consoles that refuse to talk. "Astro is 192.168.<strong>0</strong>.10. Rivet's console says 192.168.<strong>1</strong>.20. One digit, and it is a whole different network."
    </p>
    <p class="body-text">
        <strong>Astro</strong> sighs. "Earth gives us public addresses for the long link. Inside the ship we use private ones, and we read every octet carefully."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 282" role="img" aria-label="IPv4 and IPv6 address anatomy and the private IPv4 ranges">
            <text x="330" y="18" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">IPv4: 32 bits, four octets</text>
            <rect x="100" y="30" width="90" height="44" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="145.0" y="57.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">192</text>
            <text x="145" y="92" text-anchor="middle" font-size="12" font-weight="500" fill="#33406b">8 bits</text>
            <text x="205" y="60" text-anchor="middle" font-size="22" font-weight="700" fill="#14306b">.</text>
            <rect x="220" y="30" width="90" height="44" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="265.0" y="57.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">168</text>
            <text x="265" y="92" text-anchor="middle" font-size="12" font-weight="500" fill="#33406b">8 bits</text>
            <text x="325" y="60" text-anchor="middle" font-size="22" font-weight="700" fill="#14306b">.</text>
            <rect x="340" y="30" width="90" height="44" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="385.0" y="57.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">1</text>
            <text x="385" y="92" text-anchor="middle" font-size="12" font-weight="500" fill="#33406b">8 bits</text>
            <text x="445" y="60" text-anchor="middle" font-size="22" font-weight="700" fill="#14306b">.</text>
            <rect x="460" y="30" width="90" height="44" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="505.0" y="57.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">10</text>
            <text x="505" y="92" text-anchor="middle" font-size="12" font-weight="500" fill="#33406b">8 bits</text>
            <text x="330" y="122" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">IPv6: 128 bits, eight groups (shown shortened as 2001:db8::1)</text>
            <rect x="30" y="134" width="66" height="38" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="63.0" y="158.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">2001</text>
            <text x="101" y="159" text-anchor="middle" font-size="18" font-weight="700" fill="#3b2a8a">:</text>
            <rect x="107" y="134" width="66" height="38" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="140.0" y="158.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">0db8</text>
            <text x="178" y="159" text-anchor="middle" font-size="18" font-weight="700" fill="#3b2a8a">:</text>
            <rect x="184" y="134" width="66" height="38" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="217.0" y="158.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">0000</text>
            <text x="255" y="159" text-anchor="middle" font-size="18" font-weight="700" fill="#3b2a8a">:</text>
            <rect x="261" y="134" width="66" height="38" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="294.0" y="158.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">0000</text>
            <text x="332" y="159" text-anchor="middle" font-size="18" font-weight="700" fill="#3b2a8a">:</text>
            <rect x="338" y="134" width="66" height="38" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="371.0" y="158.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">0000</text>
            <text x="409" y="159" text-anchor="middle" font-size="18" font-weight="700" fill="#3b2a8a">:</text>
            <rect x="415" y="134" width="66" height="38" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="448.0" y="158.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">0000</text>
            <text x="486" y="159" text-anchor="middle" font-size="18" font-weight="700" fill="#3b2a8a">:</text>
            <rect x="492" y="134" width="66" height="38" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="525.0" y="158.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">0000</text>
            <text x="563" y="159" text-anchor="middle" font-size="18" font-weight="700" fill="#3b2a8a">:</text>
            <rect x="569" y="134" width="66" height="38" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="602.0" y="158.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">0001</text>
            <text x="330" y="208" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">Private IPv4 ranges (not routed on the Internet)</text>
            <rect x="20" y="222" width="205" height="46" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="122.5" y="242.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">10.0.0.0/8</text>
            <text x="122.5" y="261.0" text-anchor="middle" font-size="12.5" fill="#6b3a00">very large</text>
            <rect x="235" y="222" width="205" height="46" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="337.5" y="242.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">172.16.0.0/12</text>
            <text x="337.5" y="261.0" text-anchor="middle" font-size="12.5" fill="#6b3a00">medium</text>
            <rect x="450" y="222" width="205" height="46" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="552.5" y="242.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">192.168.0.0/16</text>
            <text x="552.5" y="261.0" text-anchor="middle" font-size="12.5" fill="#6b3a00">home and small networks</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. How many bits is an IPv6 address?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 32</li>
            <li class="quiz-option">B. 64</li>
            <li class="quiz-option quiz-correct">C. 128 ✓</li>
            <li class="quiz-option">D. 256</li>
        </ul>
        <p class="quiz-explanation"><em>IPv6 addresses are 128 bits long. IPv4 addresses are 32 bits.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which of these is a private IPv4 address?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 8.8.8.8</li>
            <li class="quiz-option">B. 203.0.113.10</li>
            <li class="quiz-option quiz-correct">C. 172.16.4.9 ✓</li>
            <li class="quiz-option">D. 1.1.1.1</li>
        </ul>
        <p class="quiz-explanation"><em>172.16.0.0 to 172.31.255.255 (172.16.0.0/12) is private. The others are public addresses.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why was IPv6 created?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. IPv4 is running out of addresses ✓</li>
            <li class="quiz-option">B. IPv4 is too slow to boot</li>
            <li class="quiz-option">C. IPv6 addresses are shorter</li>
            <li class="quiz-option">D. Routers were banned</li>
        </ul>
        <p class="quiz-explanation"><em>IPv4 only has about 4.3 billion addresses. IPv6 has vastly more.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> find the typo that put <code>rivet</code> on the wrong network and correct it. <strong>Success:</strong> rivet is in <code>192.168.0.0/24</code>, and astro and rivet can ping each other.
    </p>

    <div class="relay-lab" data-lab="m2-l3">
        <iframe src="{{ asset('netsim-app/app.html') }}?lab=m2-l3" title="Relay Lab: fix the address" loading="lazy"></iframe>
        <p class="relay-lab-note">
            Double-click a device to open its terminal. The simulator uses Linux commands. The ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
