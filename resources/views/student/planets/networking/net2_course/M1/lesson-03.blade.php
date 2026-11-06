{{-- M1 · Lesson 1.3: IPv6 (and the IPv5 That Never Was) — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g1-l3.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="03">

    <h1 class="lesson-heading">Lesson 1.3: IPv6 (and the IPv5 That Never Was)</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write IPv6 addresses in short form, recognise the main IPv6 address types, and give a router interface an IPv6 address.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        IPv4 addresses are 32 bits, about 4.3 billion of them, and they ran out years ago. <strong>IPv6</strong> addresses are 128 bits: enough for every grain of sand on Earth to have trillions. They are written as eight groups of four <strong>hexadecimal</strong> digits (0-9, a-f), separated by colons.
    </p>
    <p class="body-text">
        Two rules make them shorter. <strong>Rule 1:</strong> drop the leading zeros in any group (<code>0db8</code> becomes <code>db8</code>). <strong>Rule 2:</strong> replace <em>one</em> run of all-zero groups with <code>::</code>. Only once, or nobody could tell how many zeros each <code>::</code> hides. So <code>2001:0db8:0000:0000:0000:0000:0000:0001</code> is <code>2001:db8::1</code>.
    </p>
    <p class="body-text">
        A LAN almost always gets a <strong>/64</strong>: the first 64 bits say which network, the last 64 bits (the <strong>interface ID</strong>) say which device. The types to know:
    </p>
    <ul class="body-list">
        <li><strong>Global unicast</strong> (starts with 2 or 3, <code>2000::/3</code>): public, routable on the internet.</li>
        <li><strong>Link-local</strong> (<code>fe80::/10</code>): every IPv6 interface has one automatically. It only works on its own link and is never routed.</li>
        <li><strong>Unique local</strong> (<code>fc00::/7</code>, in practice <code>fd..</code>): private, like 192.168.x.x in IPv4.</li>
        <li><strong>Multicast</strong> (<code>ff00::/8</code>): one-to-many. IPv6 has no broadcast at all.</li>
        <li><strong>Loopback</strong>: <code>::1</code>, the device itself.</li>
    </ul>
    <p class="body-text">
        Devices can build their own address with <strong>SLAAC</strong>: the router announces the /64 prefix and the device adds an interface ID, sometimes made from its MAC address (<strong>EUI-64</strong>: put <code>fffe</code> in the middle of the MAC and flip the seventh bit). IPv6 also replaces ARP with <strong>Neighbor Discovery</strong>. Most networks today run <strong>dual stack</strong>: IPv4 and IPv6 side by side.
    </p>
    <pre class="code-block"><code>ipv6 unicast-routing
interface g0/0
 ipv6 address 2001:db8:c0de:10::1/64</code></pre>
    <p class="body-text">
        And IPv5? The number 5 went to the <strong>Internet Stream Protocol (ST)</strong>, an experiment from the late 1970s for streaming voice and video. It never went into wide use, so when the next real version of IP arrived, it became IPv6.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Which IPv6 type works only on its own link and is never routed?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Global unicast, <code>2000::/3</code></li>
            <li class="quiz-option quiz-correct">B. Link-local, <code>fe80::/10</code> ✓</li>
            <li class="quiz-option">C. Unique local, <code>fc00::/7</code></li>
            <li class="quiz-option">D. Multicast, <code>ff00::/8</code> group</li>
        </ul>
        <p class="quiz-explanation"><em>Every IPv6 interface gets a link-local address (fe80::/10) automatically. It is never routed.</em></p>
    </div>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        Earth's relay fleet talks <strong>IPv6</strong>. "Earth ran out of IPv4 addresses before any of us were born," <strong>Rivet</strong> says, scrolling through a list of very long addresses. "If the Grid wants to talk to the fleet, Skyport has to speak it too."
    </p>
    <p class="body-text">
        <strong>Volt</strong> points at the visitor log. "Some of these ships use addresses that only work inside their own network. Doom ships love those: hard to trace. Learn to read the types, Cadet."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why does Skyport need to speak IPv6?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Earth's relay fleet talks IPv6 ✓</li>
            <li class="quiz-option">B. IPv6 needs no addresses at all</li>
            <li class="quiz-option">C. Doom ships cannot use IPv6 yet</li>
            <li class="quiz-option">D. The Grid ran out of fiber cables</li>
        </ul>
        <p class="quiz-explanation"><em>Earth ran out of IPv4 long ago, and its fleet talks IPv6. To talk to the fleet, Skyport must speak it too.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 280" role="img" aria-label="An IPv6 address split into its 64-bit prefix and 64-bit interface ID, with the zero compression rules">
            <rect x="10" y="20" width="640" height="56" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="330.0" y="54.0" text-anchor="middle" font-size="17" font-weight="700" fill="#3a4352">2001:0db8:c0de:0010:0000:0000:0000:0001</text>
            <rect x="10" y="90" width="330" height="56" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="175.0" y="113.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">prefix /64</text>
            <text x="175.0" y="130.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#14306b">2001:db8:c0de:10  which network</text>
            <rect x="350" y="90" width="300" height="56" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="500.0" y="113.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">interface ID</text>
            <text x="500.0" y="130.5" text-anchor="middle" font-size="12.5" font-weight="500" fill="#6b3a00">::1  which device</text>
            <rect x="10" y="160" width="640" height="50" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="330.0" y="189.6" text-anchor="middle" font-size="13" font-weight="700" fill="#3b2a8a">Rule 1: drop leading zeros (0db8 → db8, 0010 → 10)    Rule 2: one run of 0000 groups → ::</text>
            <rect x="10" y="220" width="640" height="50" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="330.0" y="250.9" text-anchor="middle" font-size="17" font-weight="700" fill="#14663f">2001:db8:c0de:10::1</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, how many bits is the prefix of a LAN address?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 32</li>
            <li class="quiz-option quiz-correct">B. 64 ✓</li>
            <li class="quiz-option">C. 48</li>
            <li class="quiz-option">D. 128</li>
        </ul>
        <p class="quiz-explanation"><em>A LAN gets a /64: the first 64 bits say which network, the last 64 bits say which device.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is <code>2001:0db8:0000:0000:0000:0000:0000:0042</code> in short form?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. 2001:db8::42 ✓</li>
            <li class="quiz-option">B. 2001:db8:0:0::42:0</li>
            <li class="quiz-option">C. 2001:db8::0042::</li>
            <li class="quiz-option">D. 2001:db8:42</li>
        </ul>
        <p class="quiz-explanation"><em>Drop leading zeros (0db8 to db8, 0042 to 42) and replace the one run of zero groups with ::.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why can <code>::</code> appear only once in an address?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is too long</li>
            <li class="quiz-option quiz-correct">B. Otherwise you could not tell how many zero groups each one hides ✓</li>
            <li class="quiz-option">C. Routers ignore the second one</li>
            <li class="quiz-option">D. It is reserved for multicast</li>
        </ul>
        <p class="quiz-explanation"><em>With two, the address would be ambiguous: the zeros could be split between them in more than one way.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which address is link-local?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 2001:db8::1</li>
            <li class="quiz-option">B. fd00::5</li>
            <li class="quiz-option quiz-correct">C. fe80::a8bb:ccff:fe00:100 ✓</li>
            <li class="quiz-option">D. ff02::1</li>
        </ul>
        <p class="quiz-explanation"><em>Link-local addresses start with fe80. They work only on their own link and are never routed.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. IPv6 has no broadcast. What does it use instead to reach a group of devices?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Multicast (ff00::/8) ✓</li>
            <li class="quiz-option">B. Anycast to everyone</li>
            <li class="quiz-option">C. ARP</li>
            <li class="quiz-option">D. A bigger subnet</li>
        </ul>
        <p class="quiz-explanation"><em>IPv6 sends one-to-many traffic with multicast addresses, which start with ff.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why is there no IPv5 in use today?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It was banned</li>
            <li class="quiz-option quiz-correct">B. Version 5 went to the experimental Internet Stream Protocol, which never became widely used ✓</li>
            <li class="quiz-option">C. IPv5 is the same as IPv6</li>
            <li class="quiz-option">D. It only worked on ships</li>
        </ul>
        <p class="quiz-explanation"><em>The number 5 was used by the experimental Internet Stream Protocol (ST). It was never widely deployed, so the next real version of IP was numbered 6.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> shorten Earth's long addresses, give Skyport's router its IPv6 address, and spot which ships are hiding behind private addresses. <strong>Success:</strong> Skyport speaks IPv6 and every answer is accepted.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g1-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm1', 'lesson' => 'lesson03']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
