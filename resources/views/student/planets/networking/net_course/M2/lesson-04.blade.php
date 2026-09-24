{{-- M2 · Lesson 2.4: IP Addresses — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m2-l4.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="04">

    <h1 class="lesson-heading">Lesson 2.4: IP Addresses</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can split an address into its network and host parts with the subnet mask, find the network and broadcast addresses, and count the usable hosts.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Every IP address has two parts: the <strong>network part</strong> (which network the device is on) and the <strong>host part</strong> (which device it is on that network). The <strong>subnet mask</strong> says where the split is. The mask <code>255.255.255.0</code> is written <code>/24</code> because it has 24 ones: the first 24 bits are the network part.
    </p>
    <p class="body-text">
        For <code>192.168.1.10/24</code>:
    </p>
    <ul class="body-list">
        <li><strong>Network address</strong>: all host bits set to 0, so <code>192.168.1.0</code>. It names the network itself.</li>
        <li><strong>Broadcast address</strong>: all host bits set to 1, so <code>192.168.1.255</code>. A message to it reaches everyone on the network.</li>
        <li><strong>Usable hosts</strong>: everything in between, <code>.1</code> to <code>.254</code>. With <em>h</em> host bits you get <em>2<sup>h</sup> − 2</em> usable addresses, here 2<sup>8</sup> − 2 = 254.</li>
    </ul>
    <p class="body-text">
        A device compares a destination with its own network. <strong>Same network</strong>: send directly. <strong>Different network</strong>: send to the <strong>default gateway</strong>, which is the router. If the mask is wrong, the device draws the network boundary in the wrong place.
    </p>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        <strong>Volt</strong> jabs at his console. "I can hear Astro's console but it never hears me. My mask says /25, and Astro's says /24."
    </p>
    <p class="body-text">
        <strong>Rivet</strong> checks the numbers. "With /25 your network is 192.168.1.128 up to .255, so you believe Astro's 192.168.1.10 lives somewhere else and you send the reply to a gateway that does not exist. The address is fine. The mask is wrong."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 214" role="img" aria-label="An IP address split into network part and host part by the mask">
            <text x="330" y="18" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">192.168.1.10 with mask /24 (255.255.255.0)</text>
            <rect x="90" y="34" width="100" height="44" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="140.0" y="61.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">192</text>
            <text x="205" y="64" text-anchor="middle" font-size="22" font-weight="700" fill="#14306b">.</text>
            <rect x="220" y="34" width="100" height="44" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="270.0" y="61.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">168</text>
            <text x="335" y="64" text-anchor="middle" font-size="22" font-weight="700" fill="#14306b">.</text>
            <rect x="350" y="34" width="100" height="44" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="400.0" y="61.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">1</text>
            <text x="465" y="64" text-anchor="middle" font-size="22" font-weight="700" fill="#14306b">.</text>
            <rect x="480" y="34" width="100" height="44" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="530.0" y="61.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">10</text>
            <line x1="90" y1="96" x2="480" y2="96" stroke="#2f6fe0" stroke-width="4" stroke-linecap="round"/>
            <text x="285" y="118" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">network part (first 24 bits)</text>
            <line x1="480" y1="96" x2="590" y2="96" stroke="#f08c1a" stroke-width="4" stroke-linecap="round"/>
            <text x="535" y="118" text-anchor="middle" font-size="13" font-weight="700" fill="#6b3a00">host part</text>
            <rect x="20" y="140" width="200" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="120.0" y="167.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Network address</text>
            <text x="120.0" y="186.0" text-anchor="middle" font-size="12.5" fill="#14306b">192.168.1.0</text>
            <rect x="240" y="140" width="200" height="60" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="340.0" y="167.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">Usable hosts</text>
            <text x="340.0" y="186.0" text-anchor="middle" font-size="12.5" fill="#14663f">.1 to .254 (254)</text>
            <rect x="460" y="140" width="180" height="60" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="550.0" y="167.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">Broadcast</text>
            <text x="550.0" y="186.0" text-anchor="middle" font-size="12.5" fill="#6b3a00">192.168.1.255</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is the network address of 192.168.1.10/24?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. 192.168.1.0 ✓</li>
            <li class="quiz-option">B. 192.168.1.1</li>
            <li class="quiz-option">C. 192.168.1.10</li>
            <li class="quiz-option">D. 192.168.1.255</li>
        </ul>
        <p class="quiz-explanation"><em>The network address has all host bits set to 0. With /24 the host part is the last octet, so it is 192.168.1.0.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. How many usable host addresses does a /24 network have?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 256</li>
            <li class="quiz-option">B. 255</li>
            <li class="quiz-option quiz-correct">C. 254 ✓</li>
            <li class="quiz-option">D. 253</li>
        </ul>
        <p class="quiz-explanation"><em>2 to the power of 8 is 256, minus the network address and the broadcast address, leaves 254.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Astro is 192.168.1.10/24 and Volt is 192.168.1.130/25. Why can Volt not reach Astro?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The cable is too short</li>
            <li class="quiz-option quiz-correct">B. Volt thinks Astro is on a different network ✓</li>
            <li class="quiz-option">C. Astro's name is too long</li>
            <li class="quiz-option">D. /25 is not allowed</li>
        </ul>
        <p class="quiz-explanation"><em>With /25 Volt's network is 192.168.1.128 to 192.168.1.255. Astro's address falls outside it, so Volt looks for a gateway instead of sending directly.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> repair the mask on <code>volt</code>. <strong>Success:</strong> volt uses <code>/24</code> in <code>192.168.1.0/24</code>, and astro and volt can ping each other.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m2-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm2', 'lesson' => 'lesson04']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. You type Windows commands there (Command Prompt), like on a school PC; the ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
