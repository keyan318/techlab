{{-- M6 · Lesson 6.5: Remote Access VPN, TLS & PKI — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g6-l5.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="05">

    <h1 class="lesson-heading">Lesson 6.5: Remote Access VPN, TLS & PKI</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can compare remote access VPN types, describe the TLS handshake, explain how certificates and a PKI create trust, and renew a router's certificate.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A site-to-site VPN joins networks. A <strong>remote access VPN</strong> joins one <em>person</em>: a crew member on a ship or at home, connecting to the Grid. Two common kinds:
    </p>
    <ul class="body-list">
        <li><strong>IPsec / IKEv2 client</strong>: a VPN app on the laptop builds an IPsec tunnel to the gateway.</li>
        <li><strong>TLS (SSL) VPN</strong>: the tunnel runs over HTTPS, port 443, which almost every firewall lets out. Some are <em>clientless</em>: just a web browser.</li>
        <li><strong>Full tunnel</strong> sends all the laptop's traffic through the VPN; <strong>split tunnel</strong> sends only traffic for the company network.</li>
    </ul>
    <p class="body-text">
        <strong>TLS</strong> (Transport Layer Security, the successor of SSL) protects HTTPS and TLS VPNs. In the handshake, client and server agree on a version and ciphers, the server proves its identity with a <strong>certificate</strong>, and a key exchange produces session keys that encrypt everything after.
    </p>
    <p class="body-text">
        Why believe a certificate? Because of a <strong>PKI</strong> (public key infrastructure). A <strong>certificate authority</strong> (CA) signs certificates with its private key. Every device trusts a few <strong>root CAs</strong>, and so trusts whatever they signed: a <strong>chain of trust</strong>. A client checks that the certificate is signed by a trusted CA, has the right name, has not <strong>expired</strong>, and has not been <strong>revoked</strong> (looked up in a CRL, or live with <strong>OCSP</strong>).
    </p>
    <pre class="code-block"><code>crypto pki trustpoint SKYPORT-TP
 enrollment url http://10.0.0.20:80
 subject-name CN=vpn.skyport.grid
 revocation-check ocsp
crypto pki enroll SKYPORT-TP</code></pre>
    <p class="body-text">
        <code>show crypto pki certificates</code> lists the router's certificate and the CA certificate, with issuer, subject and validity dates. An expired certificate is one of the most common reasons a VPN suddenly stops working one morning.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does a client check before trusting a certificate?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Only that the server sent one over port 443</li>
            <li class="quiz-option">B. That it was made by the VPN gateway itself</li>
            <li class="quiz-option">C. That the laptop has seen it once before now</li>
            <li class="quiz-option quiz-correct">D. Trusted CA, right name, valid, not revoked ✓</li>
        </ul>
        <p class="quiz-explanation"><em>A client checks the CA signature, the name, the expiry date and whether it has been revoked (CRL or OCSP).</em></p>
    </div>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        At 06:00 every crew laptop on Earth's ships failed to connect to the Grid. "Not Doom this time," says <strong>Rivet</strong>, half-relieved. "The VPN gateway's certificate. Somebody forgot the renewal."
    </p>
    <p class="body-text">
        <strong>Volt</strong> isn't relaxed at all. "And while it was down, one laptop connected with a certificate for our own gateway name, signed by somebody called DOOM-CA. Check who you trust, Cadet."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why did every crew laptop fail to connect at 06:00?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The gateway's certificate had expired ✓</li>
            <li class="quiz-option">B. Doom jammed the VPN port 443 traffic</li>
            <li class="quiz-option">C. The laptops all lost their VPN client</li>
            <li class="quiz-option">D. The root CA was replaced by DOOM-CA</li>
        </ul>
        <p class="quiz-explanation"><em>Somebody forgot the renewal. An expired certificate is one of the most common reasons a VPN suddenly stops.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 290" role="img" aria-label="A chain of trust from the Grid root CA to the VPN gateway certificate, and a crew laptop connecting with a TLS VPN on port 443">
            <rect x="10" y="15" width="220" height="60" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="120.0" y="40.2" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14663f">GRID-ROOT-CA</text>
            <text x="120.0" y="56.2" text-anchor="middle" font-size="11" font-weight="500" fill="#14663f">trusted by every crew laptop</text>
            <rect x="10" y="105" width="220" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="120.0" y="130.2" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">vpn.skyport.grid</text>
            <text x="120.0" y="146.2" text-anchor="middle" font-size="11" font-weight="500" fill="#14306b">certificate signed by the CA</text>
            <line x1="120" y1="75" x2="120.0" y2="96.0" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="120,105 114.0,96.0 126.0,96.0" fill="#7c5cff"/>
            <text x="185" y="95" text-anchor="middle" font-size="11.5" font-weight="600" fill="#14663f">signs</text>
            <rect x="10" y="195" width="220" height="85" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="120.0" y="217.7" text-anchor="middle" font-size="13.5" font-weight="700" fill="#6b3a00">checks</text>
            <text x="120.0" y="233.7" text-anchor="middle" font-size="11" font-weight="500" fill="#6b3a00">signature from a trusted CA?</text>
            <text x="120.0" y="249.7" text-anchor="middle" font-size="11" font-weight="500" fill="#6b3a00">name matches? not expired?</text>
            <text x="120.0" y="265.7" text-anchor="middle" font-size="11" font-weight="500" fill="#6b3a00">not revoked (CRL / OCSP)?</text>
            <rect x="280" y="15" width="370" height="120" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="465.0" y="46.9" text-anchor="middle" font-size="14" font-weight="700" fill="#3b2a8a">Remote access VPN</text>
            <text x="465.0" y="63.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">one person, not a whole site</text>
            <text x="465.0" y="79.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">IPsec client (IKEv2), or</text>
            <text x="465.0" y="96.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">TLS/SSL VPN over HTTPS port 443:</text>
            <text x="465.0" y="112.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">works through almost any firewall</text>
            <rect x="280" y="155" width="370" height="125" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="465.0" y="189.4" text-anchor="middle" font-size="14" font-weight="700" fill="#3a4352">TLS handshake (short version)</text>
            <text x="465.0" y="205.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">hello: versions and ciphers</text>
            <text x="465.0" y="222.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">server sends its certificate</text>
            <text x="465.0" y="238.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">key exchange → session keys</text>
            <text x="465.0" y="255.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">everything after is encrypted</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, which port does the TLS VPN use?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 22</li>
            <li class="quiz-option quiz-correct">B. 443 ✓</li>
            <li class="quiz-option">C. 853</li>
            <li class="quiz-option">D. 1812</li>
        </ul>
        <p class="quiz-explanation"><em>A TLS/SSL VPN runs over HTTPS on port 443, which works through almost any firewall.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which remote access VPN type runs over port 443 and passes almost any firewall?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. GRE</li>
            <li class="quiz-option quiz-correct">B. TLS (SSL) VPN ✓</li>
            <li class="quiz-option">C. IPsec site-to-site</li>
            <li class="quiz-option">D. Telnet</li>
        </ul>
        <p class="quiz-explanation"><em>A TLS VPN runs over HTTPS on port 443, which is almost always allowed outbound.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does a certificate authority do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Encrypts all traffic</li>
            <li class="quiz-option quiz-correct">B. Signs certificates, vouching that a public key belongs to a name ✓</li>
            <li class="quiz-option">C. Assigns IP addresses</li>
            <li class="quiz-option">D. Runs DNS</li>
        </ul>
        <p class="quiz-explanation"><em>The CA's signature is what lets clients trust a certificate they have never seen before.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. A certificate is signed by a trusted CA and has the right name, but its end date was yesterday. Will clients accept it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Yes</li>
            <li class="quiz-option quiz-correct">B. No, it has expired ✓</li>
            <li class="quiz-option">C. Only on Mondays</li>
            <li class="quiz-option">D. Only with split tunnel</li>
        </ul>
        <p class="quiz-explanation"><em>Clients reject expired certificates; the fix is to renew (re-enroll) before the end date.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. How can a client check, live, whether a certificate was revoked?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Ping the server</li>
            <li class="quiz-option quiz-correct">B. OCSP ✓</li>
            <li class="quiz-option">C. DHCP</li>
            <li class="quiz-option">D. ARP</li>
        </ul>
        <p class="quiz-explanation"><em>OCSP (Online Certificate Status Protocol) asks the CA's responder about one certificate right now; a CRL is a downloaded list.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is split tunneling?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Two VPNs at once</li>
            <li class="quiz-option quiz-correct">B. Only traffic for the company network goes through the VPN; the rest goes straight to the internet ✓</li>
            <li class="quiz-option">C. Encrypting half of each packet</li>
            <li class="quiz-option">D. A failed tunnel</li>
        </ul>
        <p class="quiz-explanation"><em>Split tunnel sends only corporate traffic into the VPN. Full tunnel sends everything.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> find out why the crew VPN broke, renew the gateway's certificate from the Grid CA with revocation checking, and refuse a forged certificate. <strong>Success:</strong> the gateway has a valid certificate and every answer is accepted.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g6-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm6', 'lesson' => 'lesson05']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
