{{-- M6 · Lesson 6.7: DNS Security: DNSSEC, DoH/DoT & Filtering — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g6-l7.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="07">

    <h1 class="lesson-heading">Lesson 6.7: DNS Security: DNSSEC, DoH/DoT & Filtering</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain DNS spoofing and how DNSSEC, DoH and DoT respond to it, sinkhole a malicious domain, and stop clients from bypassing a filtering resolver.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Almost every connection starts with a DNS lookup, and classic DNS has no protection at all: answers travel in plain text and nothing proves they are real. An attacker who can forge or <strong>poison</strong> an answer sends you to their server, even when you typed the right name.
    </p>
    <ul class="body-list">
        <li><strong>DNSSEC</strong> adds digital signatures to DNS records. A validating resolver checks the chain of signatures from the root down, and rejects forged answers. It protects <em>integrity</em>, not privacy: the queries are still readable.</li>
        <li><strong>DoT</strong> (DNS over TLS, port 853) and <strong>DoH</strong> (DNS over HTTPS, port 443) encrypt the queries between a device and its resolver, so nobody on the path can read or change them.</li>
        <li>But encryption cuts both ways: a laptop using DoH to a public resolver skips your company's resolver, and with it your filtering.</li>
    </ul>
    <p class="body-text">
        <strong>DNS filtering</strong> turns the resolver into a security tool. It knows lists of malicious domains (threat intelligence) and refuses them, or answers with the address of a <strong>sinkhole</strong>: a safe server that shows a warning page and logs which machine asked. Infected machines trying to call home show up in the sinkhole's log.
    </p>
    <p class="body-text">
        A filter only works if clients can't go around it. At the network edge:
    </p>
    <pre class="code-block"><code>ip access-list extended GRID-DNS
 permit udp host 192.168.100.53 any eq domain
 deny udp any any eq domain
 deny tcp any any eq 853
 deny tcp any host 198.51.100.53 eq 443
 permit ip any any
interface g0/0
 ip access-group GRID-DNS in</code></pre>
    <p class="body-text">
        Only the Grid's resolver may send DNS out; plain DNS and DoT from anyone else are blocked, and so is DoH to known public resolvers. The Grid's DNS server runs Windows Server, so its records are managed with PowerShell: <code>Get-DnsServerResourceRecord</code>, <code>Remove-DnsServerResourceRecord</code> and <code>Add-DnsServerResourceRecordA</code>.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does DNSSEC protect?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Privacy, not integrity</li>
            <li class="quiz-option quiz-correct">B. Integrity, not privacy ✓</li>
            <li class="quiz-option">C. Neither, it only logs</li>
            <li class="quiz-option">D. Only the query speed</li>
        </ul>
        <p class="quiz-explanation"><em>DNSSEC signs records so forged answers are rejected, but queries are still readable. DoT/DoH add privacy.</em></p>
    </div>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        A message reaches every crew terminal: <em>"Urgent Grid update. Download now from codexia-update.net."</em> <strong>Volt</strong> traces the name: it resolves to a Doom server, 198.51.100.66. "Vex didn't break our firewall. He just got us to look up the wrong name."
    </p>
    <p class="body-text">
        "So the Grid's own DNS server tells the truth from now on," says <strong>Astro</strong>. "Doom's names go to the sinkhole. And no terminal gets to ask anyone else."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">How did Vex get the crew to the Doom server?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. He broke through the Grid's edge firewall</li>
            <li class="quiz-option quiz-correct">B. They looked up a name that led to Doom ✓</li>
            <li class="quiz-option">C. He stole a laptop from the crew's hangar</li>
            <li class="quiz-option">D. He cut the radio link to the Citadel</li>
        </ul>
        <p class="quiz-explanation"><em>Vex didn't break the firewall. codexia-update.net simply resolved to a Doom server.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="DNS protections: DNSSEC signs answers, DoH and DoT encrypt queries, a sinkhole answers bad names with a safe address, and an ACL forces clients to use the filtered resolver">
            <rect x="10" y="15" width="310" height="120" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="165.0" y="46.9" text-anchor="middle" font-size="14" font-weight="700" fill="#14663f">DNSSEC</text>
            <text x="165.0" y="63.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">zones sign their records</text>
            <text x="165.0" y="79.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">resolvers check the signature</text>
            <text x="165.0" y="96.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">→ forged answers are rejected</text>
            <text x="165.0" y="112.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">(integrity, not secrecy)</text>
            <rect x="340" y="15" width="310" height="120" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="495.0" y="46.9" text-anchor="middle" font-size="14" font-weight="700" fill="#3b2a8a">DoH / DoT</text>
            <text x="495.0" y="63.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">DNS over HTTPS (443)</text>
            <text x="495.0" y="79.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">DNS over TLS (853)</text>
            <text x="495.0" y="96.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">→ queries encrypted</text>
            <text x="495.0" y="112.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">…and invisible to your filter</text>
            <rect x="10" y="155" width="310" height="130" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="165.0" y="191.9" text-anchor="middle" font-size="14" font-weight="700" fill="#6b3a00">Filtering + sinkhole</text>
            <text x="165.0" y="208.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">the Grid resolver knows</text>
            <text x="165.0" y="224.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">Doom&#x27;s domains, answers</text>
            <text x="165.0" y="241.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">with the sinkhole address</text>
            <text x="165.0" y="257.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">→ a warning page instead</text>
            <rect x="340" y="155" width="310" height="130" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="495.0" y="191.9" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1820">Stop the bypass</text>
            <text x="495.0" y="208.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">ACL at the edge: only the</text>
            <text x="495.0" y="224.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">Grid resolver may send DNS</text>
            <text x="495.0" y="241.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">out; block 853, and DoH to</text>
            <text x="495.0" y="257.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">known public resolvers</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what does a sinkhole show instead of the bad site?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. A warning page ✓</li>
            <li class="quiz-option">B. The real site</li>
            <li class="quiz-option">C. A Doom server</li>
            <li class="quiz-option">D. A blank screen</li>
        </ul>
        <p class="quiz-explanation"><em>The Grid's resolver answers Doom's names with the sinkhole address, which shows a warning page and logs who asked.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does DNSSEC protect?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The privacy of DNS queries</li>
            <li class="quiz-option quiz-correct">B. The integrity of DNS answers, with signatures ✓</li>
            <li class="quiz-option">C. Web pages</li>
            <li class="quiz-option">D. IP addresses from DHCP</li>
        </ul>
        <p class="quiz-explanation"><em>DNSSEC lets resolvers verify answers are genuine. It does not encrypt queries.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which ports do DoT and DoH use?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 53 and 80</li>
            <li class="quiz-option quiz-correct">B. 853 and 443 ✓</li>
            <li class="quiz-option">C. 1812 and 1813</li>
            <li class="quiz-option">D. 22 and 23</li>
        </ul>
        <p class="quiz-explanation"><em>DNS over TLS uses 853; DNS over HTTPS uses 443 like any web traffic.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does a DNS sinkhole do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Deletes the domain from the internet</li>
            <li class="quiz-option quiz-correct">B. Answers a malicious name with the address of a safe server that warns and logs ✓</li>
            <li class="quiz-option">C. Encrypts DNS</li>
            <li class="quiz-option">D. Speeds up lookups</li>
        </ul>
        <p class="quiz-explanation"><em>The sinkhole redirects bad names to a harmless server, protecting users and revealing infected machines.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why block outbound DNS from every device except the Grid's resolver?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. To save bandwidth</li>
            <li class="quiz-option quiz-correct">B. Otherwise devices could use another resolver and bypass the filtering ✓</li>
            <li class="quiz-option">C. DNS is insecure everywhere</li>
            <li class="quiz-option">D. Routers require it</li>
        </ul>
        <p class="quiz-explanation"><em>Filtering only works if clients must use the filtering resolver.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why is DoH hard for a network filter to stop?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It uses UDP 53</li>
            <li class="quiz-option quiz-correct">B. It looks like ordinary HTTPS on port 443 ✓</li>
            <li class="quiz-option">C. It is unencrypted</li>
            <li class="quiz-option">D. It only works on Earth</li>
        </ul>
        <p class="quiz-explanation"><em>DoH hides DNS inside normal-looking HTTPS, so the usual answer is to block known DoH resolvers or manage devices centrally.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> sinkhole Doom's fake update domain on the Grid's DNS server, then make sure no terminal can ask anyone else. <strong>Success:</strong> codexia-update.net shows a warning page, and the rogue laptop can't reach outside DNS, DoT or DoH.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g6-l7" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm6', 'lesson' => 'lesson07']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
