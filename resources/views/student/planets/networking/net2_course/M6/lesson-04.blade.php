{{-- M6 · Lesson 6.4: Site-to-Site VPN: IPsec & GRE — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g6-l4.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="04">

    <h1 class="lesson-heading">Lesson 6.4: Site-to-Site VPN: IPsec & GRE</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain the two phases of an IPsec VPN, configure a site-to-site tunnel with a crypto map, verify it, and say when to wrap traffic in GRE.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>VPN</strong> (virtual private network) builds a private, encrypted path across a network you don't trust. A <strong>site-to-site</strong> VPN joins two whole networks: the routers at each end do the work, and the PCs never know.
    </p>
    <p class="body-text">
        <strong>IPsec</strong> is the standard suite. It gives <strong>confidentiality</strong> (encryption, like AES), <strong>integrity</strong> (a hash, like SHA-256, proves nothing changed), <strong>authentication</strong> (each end proves who it is) and <strong>anti-replay</strong>. It is set up in two phases:
    </p>
    <ul class="body-list">
        <li><strong>Phase 1, IKE</strong> (ISAKMP): the two routers authenticate each other (with a pre-shared key or certificates) and agree on encryption, hash and a <strong>Diffie-Hellman</strong> group, so they can create shared keys without ever sending them. Result: a secure channel for talking.</li>
        <li><strong>Phase 2, IPsec</strong>: inside that channel they agree how to protect the actual data: the <strong>transform set</strong> (ESP with AES and SHA). An ACL says which traffic is "interesting" enough to encrypt.</li>
    </ul>
    <pre class="code-block"><code>crypto isakmp policy 10
 encryption aes 256
 hash sha256
 authentication pre-share
 group 14
crypto isakmp key &lt;secret&gt; address 203.0.113.50
crypto ipsec transform-set GRID-TS esp-aes 256 esp-sha256-hmac
access-list 120 permit ip 192.168.30.0 0.0.0.255 192.168.50.0 0.0.0.255
crypto map GRID-MAP 10 ipsec-isakmp
 set peer 203.0.113.50
 set transform-set GRID-TS
 match address 120
interface g0/2
 crypto map GRID-MAP</code></pre>
    <p class="body-text">
        The tunnel only comes up when interesting traffic flows. Check with <code>show crypto isakmp sa</code> (QM_IDLE means phase 1 is up) and <code>show crypto ipsec sa</code> (packets encrypted and decrypted).
    </p>
    <p class="body-text">
        Plain IPsec carries only unicast IP, so routing protocols like OSPF (which use multicast) can't run through it. <strong>GRE</strong> (Generic Routing Encapsulation) builds a simple tunnel interface that carries anything, including multicast, but encrypts nothing. Put the two together, <strong>GRE over IPsec</strong>, and you get a tunnel that runs OSPF <em>and</em> is encrypted.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Why combine GRE with IPsec?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. So the tunnel needs no shared key or certificate</li>
            <li class="quiz-option">B. So the radio link becomes a lot faster</li>
            <li class="quiz-option">C. So phase 1 can be skipped completely</li>
            <li class="quiz-option quiz-correct">D. So OSPF can run through an encrypted tunnel ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Plain IPsec carries only unicast, so OSPF can't run. GRE carries multicast but doesn't encrypt. Together they do both.</em></p>
    </div>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        <strong>Volt</strong> plays back a recording: Northwatch's patrol reports, word for word, picked up by a Doom listening post. "Our radio link is in the open air. Anyone with an antenna can read everything we send across it."
    </p>
    <p class="body-text">
        "Then we send nothing they can read," says <strong>Astro</strong>. "An IPsec tunnel from Northwatch to Skyport. Doom can record all it wants. It'll be noise."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What problem does the IPsec tunnel solve for Northwatch?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The radio link is far too slow</li>
            <li class="quiz-option">B. Northwatch has no OSPF routes</li>
            <li class="quiz-option">C. The patrol reports are too long</li>
            <li class="quiz-option quiz-correct">D. Doom can read the radio traffic ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The radio is in open air, so anyone with an antenna can listen. Encrypted, the recordings are just noise.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="An IPsec tunnel between Northwatch and Skyport across the radio: phase 1 builds a secure channel, phase 2 encrypts the traffic">
            <rect x="10" y="30" width="160" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="90.0" y="55.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">Northwatch LAN</text>
            <text x="90.0" y="71.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">192.168.30.0/24</text>
            <rect x="190" y="30" width="120" height="60" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="250.0" y="55.2" text-anchor="middle" font-size="13.5" font-weight="700" fill="#3b2a8a">NW-EDGE</text>
            <text x="250.0" y="71.2" text-anchor="middle" font-size="11" font-weight="500" fill="#3b2a8a">203.0.113.30</text>
            <rect x="350" y="30" width="120" height="60" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="410.0" y="55.2" text-anchor="middle" font-size="13.5" font-weight="700" fill="#3b2a8a">SKYPORT</text>
            <text x="410.0" y="71.2" text-anchor="middle" font-size="11" font-weight="500" fill="#3b2a8a">203.0.113.50</text>
            <rect x="490" y="30" width="160" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="570.0" y="55.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">Skyport LAN</text>
            <text x="570.0" y="71.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">192.168.50.0/24</text>
            <line x1="170" y1="60" x2="190" y2="60" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="470" y1="60" x2="490" y2="60" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="310" y1="52" x2="350" y2="52" stroke="#2fa56b" stroke-width="6" stroke-linecap="round"/>
            <line x1="310" y1="68" x2="350" y2="68" stroke="#e5484d" stroke-width="2" stroke-linecap="round" stroke-dasharray="7 6"/>
            <text x="330" y="112" text-anchor="middle" font-size="11.5" font-weight="600" fill="#14663f">encrypted tunnel over the jammed radio</text>
            <rect x="10" y="135" width="310" height="150" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="165.0" y="174.2" text-anchor="middle" font-size="14" font-weight="700" fill="#6b3a00">Phase 1: IKE (ISAKMP)</text>
            <text x="165.0" y="190.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">the two routers prove who they are</text>
            <text x="165.0" y="207.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">(pre-shared key or certificates)</text>
            <text x="165.0" y="223.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">and agree: AES-256, SHA-256,</text>
            <text x="165.0" y="240.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">Diffie-Hellman group 14</text>
            <text x="165.0" y="256.6" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">→ a secure channel to talk in</text>
            <rect x="340" y="135" width="310" height="150" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="495.0" y="174.2" text-anchor="middle" font-size="14" font-weight="700" fill="#14663f">Phase 2: IPsec (ESP)</text>
            <text x="495.0" y="190.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">transform set: how to encrypt data</text>
            <text x="495.0" y="207.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">ACL: which traffic is &quot;interesting&quot;</text>
            <text x="495.0" y="223.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">crypto map ties it all to the</text>
            <text x="495.0" y="240.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">outside interface</text>
            <text x="495.0" y="256.6" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">GRE inside IPsec: for OSPF</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what happens in phase 1?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The data itself gets encrypted</li>
            <li class="quiz-option quiz-correct">B. The routers prove who they are ✓</li>
            <li class="quiz-option">C. The ACL picks interesting traffic</li>
            <li class="quiz-option">D. OSPF runs inside the GRE tunnel</li>
        </ul>
        <p class="quiz-explanation"><em>Phase 1 (IKE) authenticates the routers and agrees on AES, SHA and Diffie-Hellman, making a secure channel.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does IKE phase 1 produce?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Encrypted user data</li>
            <li class="quiz-option quiz-correct">B. An authenticated, secure channel between the two VPN peers ✓</li>
            <li class="quiz-option">C. A VLAN</li>
            <li class="quiz-option">D. A routing table</li>
        </ul>
        <p class="quiz-explanation"><em>Phase 1 authenticates the peers and sets up a protected channel; phase 2 negotiates how to protect the data inside it.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does Diffie-Hellman do in IPsec?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Encrypts the data</li>
            <li class="quiz-option quiz-correct">B. Lets two peers create a shared secret key without sending it ✓</li>
            <li class="quiz-option">C. Checks passwords</li>
            <li class="quiz-option">D. Compresses packets</li>
        </ul>
        <p class="quiz-explanation"><em>Diffie-Hellman key exchange creates the same secret on both sides over an insecure network.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is the job of the crypto ACL (access-list 120)?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Block attackers</li>
            <li class="quiz-option quiz-correct">B. Define which traffic is interesting and must be encrypted ✓</li>
            <li class="quiz-option">C. Allow SSH</li>
            <li class="quiz-option">D. Count packets</li>
        </ul>
        <p class="quiz-explanation"><em>Traffic matching the crypto ACL is sent through the tunnel; the rest goes out unencrypted as usual.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. <code>show crypto isakmp sa</code> shows QM_IDLE. What does that mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The tunnel is down</li>
            <li class="quiz-option quiz-correct">B. Phase 1 is up and the peers are idle between phase 2 negotiations ✓</li>
            <li class="quiz-option">C. The key is wrong</li>
            <li class="quiz-option">D. GRE is missing</li>
        </ul>
        <p class="quiz-explanation"><em>QM_IDLE is the normal state of an established phase 1 security association.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why use GRE over IPsec instead of plain IPsec?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. GRE encrypts better</li>
            <li class="quiz-option quiz-correct">B. GRE carries multicast, so routing protocols like OSPF can run through the encrypted tunnel ✓</li>
            <li class="quiz-option">C. It is faster</li>
            <li class="quiz-option">D. IPsec needs it</li>
        </ul>
        <p class="quiz-explanation"><em>Plain IPsec carries only unicast IP. GRE adds multicast support; IPsec adds the encryption.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> build an IPsec tunnel from Northwatch to Skyport across the radio, bring it up with real traffic, and verify both phases. <strong>Success:</strong> Northwatch's traffic to Skyport is encrypted, and Doom's recordings are noise.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g6-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm6', 'lesson' => 'lesson04']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
