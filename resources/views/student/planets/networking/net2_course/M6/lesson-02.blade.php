{{-- M6 · Lesson 6.2: DMZ Design — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g6-l2.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="02">

    <h1 class="lesson-heading">Lesson 6.2: DMZ Design</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can design a DMZ, explain which traffic may flow between inside, DMZ and outside, and build it with a default-deny stateful firewall.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Some servers must be reachable from the internet: a public web site, a mail gateway. Putting them on the inside network means that if one is hacked, the attacker is <em>inside</em>, next to everything else. The answer is a <strong>DMZ</strong> (demilitarized zone): a separate network for public servers, between the outside world and the inside.
    </p>
    <p class="body-text">
        The classic design is a <strong>three-legged firewall</strong>, one interface per zone, with rules by direction:
    </p>
    <ul class="body-list">
        <li><strong>Inside → outside and inside → DMZ</strong>: allowed (the crew needs both).</li>
        <li><strong>Outside → DMZ</strong>: only the published services, like TCP 80 to the web server.</li>
        <li><strong>Outside → inside</strong>: never.</li>
        <li><strong>DMZ → inside</strong>: never, or only one tightly limited path. This rule is the whole point: a compromised DMZ server can't reach the crew database.</li>
    </ul>
    <p class="body-text">
        Build it <strong>default deny</strong>: block everything, then allow exactly the flows above. A <strong>stateful</strong> firewall makes that practical, because every reply to an allowed connection gets back in automatically. Bigger sites use two firewalls from different vendors, one on each side of the DMZ (a <strong>screened subnet</strong>).
    </p>
    <pre class="code-block"><code>netsh advfirewall set allprofiles state on
netsh advfirewall set allprofiles firewallpolicy blockinbound,allowoutbound
netsh advfirewall firewall add rule name=CrewOut dir=in action=allow remoteip=192.168.20.0/24
netsh advfirewall firewall add rule name=DMZWeb dir=in action=allow protocol=TCP localport=80 localip=172.16.0.10</code></pre>
    <p class="body-text">
        The Citadel's firewall, <em>shield</em>, runs Windows Server, so its rules are written with <code>netsh advfirewall</code>, like the PCs. On a firewall that routes, an inbound rule matches traffic passing through it as well as traffic to it.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Which DMZ rule is “the whole point”?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Inside to DMZ is never allowed</li>
            <li class="quiz-option">B. Outside to DMZ is always open</li>
            <li class="quiz-option">C. Inside to outside is blocked</li>
            <li class="quiz-option quiz-correct">D. DMZ to inside is never allowed ✓</li>
        </ul>
        <p class="quiz-explanation"><em>If a DMZ server is hacked, it still can't reach the crew database, because DMZ → inside is blocked.</em></p>
    </div>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        Earth wants a public page for the Grid: news, docking times, weather on Codexia. "Not on the inside network," says <strong>Volt</strong> at once. "If Vex breaks the web server, I don't want him one step from the crew database."
    </p>
    <p class="body-text">
        <strong>Astro</strong> points at the new firewall, <em>shield</em>, with three cables: inside, DMZ, outside. "Right now it lets everything through. Make it deny by default, then open exactly what we need, and not one port more."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why does Volt refuse the public page on the inside network?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The inside network has no web port open</li>
            <li class="quiz-option quiz-correct">B. A hacked web server would sit by the crew ✓</li>
            <li class="quiz-option">C. Earth asked for it to be put on the DMZ only</li>
            <li class="quiz-option">D. The inside network has too many printers</li>
        </ul>
        <p class="quiz-explanation"><em>On the inside, a hacked public server puts the attacker one step from the crew database.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="A three-legged firewall: inside, DMZ and outside, with the allowed directions shown as arrows">
            <rect x="250" y="110" width="160" height="70" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="330.0" y="140.5" text-anchor="middle" font-size="15" font-weight="700" fill="#7a1820">shield</text>
            <text x="330.0" y="157.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">stateful firewall</text>
            <rect x="10" y="20" width="200" height="80" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="110.0" y="54.8" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">INSIDE 192.168.20.0/24</text>
            <text x="110.0" y="71.3" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">astro, crew-db</text>
            <rect x="450" y="20" width="200" height="80" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="550.0" y="54.8" text-anchor="middle" font-size="13" font-weight="700" fill="#3a4352">OUTSIDE 203.0.113.0/24</text>
            <text x="550.0" y="71.3" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">Earth, Doom</text>
            <rect x="230" y="215" width="200" height="75" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="330.0" y="247.3" text-anchor="middle" font-size="13" font-weight="700" fill="#6b3a00">DMZ 172.16.0.0/24</text>
            <text x="330.0" y="263.8" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">dmz-web (public site)</text>
            <line x1="170" y1="100" x2="246.4" y2="122.5" stroke="#2fa56b" stroke-width="3" stroke-linecap="round"/>
            <polygon points="255,125 244.7,128.2 248.1,116.7" fill="#2fa56b"/>
            <text x="150" y="130" text-anchor="middle" font-size="11.5" font-weight="600" fill="#14663f">inside → anywhere ✔</text>
            <line x1="490" y1="100" x2="386.2" y2="208.5" stroke="#2fa56b" stroke-width="3" stroke-linecap="round"/>
            <polygon points="380,215 381.9,204.3 390.6,212.6" fill="#2fa56b"/>
            <text x="560" y="150" text-anchor="middle" font-size="11.5" font-weight="600" fill="#14663f">outside → DMZ web only ✔</text>
            <line x1="300" y1="215" x2="127.6" y2="106.8" stroke="#e5484d" stroke-width="3" stroke-linecap="round" stroke-dasharray="7 6"/>
            <polygon points="120,102 130.8,101.7 124.4,111.9" fill="#e5484d"/>
            <line x1="201" y1="151" x2="219" y2="169" stroke="#e5484d" stroke-width="4" stroke-linecap="round"/>
            <line x1="201" y1="169" x2="219" y2="151" stroke="#e5484d" stroke-width="4" stroke-linecap="round"/>
            <text x="120" y="200" text-anchor="middle" font-size="11.5" font-weight="600" fill="#a3262b">DMZ → inside ✘</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what may the outside reach?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The crew database</li>
            <li class="quiz-option">B. Anything inside</li>
            <li class="quiz-option">C. Nothing at all</li>
            <li class="quiz-option quiz-correct">D. The DMZ web only ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Outside → DMZ is allowed only for the published web service. Outside → inside is never allowed.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What belongs in a DMZ?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The crew database</li>
            <li class="quiz-option quiz-correct">B. Servers that must be reachable from outside, like a public web site ✓</li>
            <li class="quiz-option">C. Every PC</li>
            <li class="quiz-option">D. The core switch</li>
        </ul>
        <p class="quiz-explanation"><em>The DMZ holds public-facing servers so a compromise there doesn't put the attacker on the inside network.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which flow should a DMZ design block?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Inside to outside</li>
            <li class="quiz-option">B. Outside to the DMZ web server on port 80</li>
            <li class="quiz-option quiz-correct">C. DMZ to inside ✓</li>
            <li class="quiz-option">D. Inside to DMZ</li>
        </ul>
        <p class="quiz-explanation"><em>A compromised DMZ server must not be able to reach inside systems.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does "default deny" mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Allow everything, block bad things</li>
            <li class="quiz-option quiz-correct">B. Block everything, then allow only listed flows ✓</li>
            <li class="quiz-option">C. Deny only Doom</li>
            <li class="quiz-option">D. Log everything</li>
        </ul>
        <p class="quiz-explanation"><em>Default deny means anything not explicitly allowed is blocked.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why does a stateful firewall make default deny practical?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is faster</li>
            <li class="quiz-option quiz-correct">B. Replies to allowed connections are let back in automatically, with no extra rules ✓</li>
            <li class="quiz-option">C. It ignores rules</li>
            <li class="quiz-option">D. It needs no policy</li>
        </ul>
        <p class="quiz-explanation"><em>With connection tracking, you only write rules for who may start a connection.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is a screened subnet?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A VLAN</li>
            <li class="quiz-option quiz-correct">B. A DMZ between two firewalls, one facing outside and one facing inside ✓</li>
            <li class="quiz-option">C. A wireless network</li>
            <li class="quiz-option">D. A subnet with no hosts</li>
        </ul>
        <p class="quiz-explanation"><em>Two firewalls, ideally from different vendors, put the DMZ between them for defence in depth.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> switch the shield firewall to default deny, let the crew out, and publish only the DMZ web page. <strong>Success:</strong> Earth sees the public page, and neither Doom nor the DMZ can touch the crew database.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g6-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm6', 'lesson' => 'lesson02']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
