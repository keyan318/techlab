{{-- M4 · Lesson 4.2: Enterprise Security Architecture — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab runs in NetSim, not Citadel Sim (public/netsim-app/labs/c4-l2.json), but keeps the
"Defend it yourself" framing and cybersecurity CTA styling of the rest of this course.
Syllabus: ITP1232 Unit D.2 Enterprise Security Architecture (Zones of Trust). --}}

<div class="lesson-fragment" data-module="m4" data-lesson="02">

    <h1 class="lesson-heading">Lesson 4.2: Enterprise Security Architecture</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what a "zone of trust" is, why networks are split into
        segments like a DMZ, and configure a default-block firewall policy that only allows the one service
        a less-trusted zone actually needs.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A network doesn't have to be one flat room where every device can reach every other device.
        <strong>Enterprise security architecture</strong> splits a network into <strong>zones of trust</strong>:
        groups of devices that share the same level of risk. A public-facing zone (often called a
        <strong>DMZ</strong>, short for demilitarized zone) holds things strangers might touch, like a web server.
        An internal zone holds what strangers should never reach directly.
    </p>

    <p class="body-text">
        A firewall sits between the zones and enforces the rule. The safest starting point is
        <strong>default-deny</strong>: block everything by default, then allow only the specific, named
        traffic a zone actually needs. That way, even if one zone is compromised, the blast radius stops at
        the firewall instead of spreading to everything behind it.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        06:20. Volt's sensors catch a device on the Citadel's public dock, the open landing area where any
        visiting ship can plug in. It's not ours. <strong>Astro</strong> doesn't panic — the dock was always
        meant to be public. "The dock was never the problem. Whether it can reach the command deck is."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> checks the network. "Right now it's one flat room, Captain. Anything on the dock
        can walk straight to the command deck." <strong>Astro</strong>: "Then we build the wall. Public stays
        public. The command deck stays ours, with one door open for the one service the dock still needs."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 230" role="img" aria-label="A firewall sits between the public dock zone and the internal command-deck zone, blocking everything by default except an explicit allow rule for the web service">
            <rect x="20" y="30" width="170" height="90" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="105" y="58" text-anchor="middle" font-size="15" font-weight="700" fill="#b3261e">Public Dock</text>
            <text x="105" y="78" text-anchor="middle" font-size="12.5" fill="#7a1a14">dock-gate</text>
            <text x="105" y="96" text-anchor="middle" font-size="12.5" fill="#7a1a14">(untrusted)</text>

            <rect x="250" y="30" width="140" height="170" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="58" text-anchor="middle" font-size="15" font-weight="700" fill="#8a4b00">citadel-fw</text>
            <text x="320" y="78" text-anchor="middle" font-size="12" fill="#8a4b00">default: block</text>
            <text x="320" y="96" text-anchor="middle" font-size="12" fill="#8a4b00">allow: tcp/80 only</text>

            <rect x="450" y="30" width="170" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="535" y="58" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">web-server :80</text>
            <text x="535" y="78" text-anchor="middle" font-size="12" fill="#14306b">allowed through</text>

            <rect x="450" y="130" width="170" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="535" y="158" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">command-deck</text>
            <text x="535" y="178" text-anchor="middle" font-size="12" fill="#b3261e">blocked</text>

            <line x1="190" y1="75" x2="246" y2="65" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="250,63 238,58 240,70" fill="#7c5cff"/>

            <line x1="390" y1="65" x2="446" y2="65" stroke="#3aa876" stroke-width="3"/>
            <polygon points="450,65 438,59 438,71" fill="#3aa876"/>

            <line x1="390" y1="140" x2="446" y2="160" stroke="#b3261e" stroke-width="3" stroke-dasharray="5,4"/>
            <text x="410" y="122" font-size="12" fill="#b3261e">✕ blocked by default policy</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is a "zone of trust" on a network?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A physical room where servers are stored</li>
            <li class="quiz-option quiz-correct">B. A group of devices that share the same level of risk, separated from other groups ✓</li>
            <li class="quiz-option">C. Any device with a strong password</li>
            <li class="quiz-option">D. The name of a specific brand of firewall</li>
        </ul>
        <p class="quiz-explanation"><em>Zones of trust group devices by risk level, so a compromise in one zone doesn't automatically spread to another.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is a DMZ in networking?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A type of encryption</li>
            <li class="quiz-option quiz-correct">B. A public-facing network zone that holds services strangers might touch, like a web server ✓</li>
            <li class="quiz-option">C. A backup power supply</li>
            <li class="quiz-option">D. A password policy</li>
        </ul>
        <p class="quiz-explanation"><em>A DMZ (demilitarized zone) is where public-facing services live, kept separate from the trusted internal network.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does a "default-deny" firewall policy mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Every connection is allowed unless specifically blocked</li>
            <li class="quiz-option quiz-correct">B. Every connection is blocked unless specifically allowed ✓</li>
            <li class="quiz-option">C. The firewall is turned off by default</li>
            <li class="quiz-option">D. Only outbound traffic is ever checked</li>
        </ul>
        <p class="quiz-explanation"><em>Default-deny blocks everything first, then allows only the specific traffic you've explicitly named — the safest starting point.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. In this lesson's lab, why should the firewall allow port 80 specifically instead of allowing all traffic from the dock?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Port 80 is the only port that exists</li>
            <li class="quiz-option quiz-correct">B. Allowing only the one service actually needed keeps every other door shut, even if the dock is compromised ✓</li>
            <li class="quiz-option">C. Allowing all traffic makes the network faster</li>
            <li class="quiz-option">D. It doesn't matter which ports are open</li>
        </ul>
        <p class="quiz-explanation"><em>A narrow allow-list is the whole point of default-deny: open exactly what's needed, nothing more.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why does splitting the network into zones limit the damage of an attack?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It doesn't; attackers can always reach everything</li>
            <li class="quiz-option quiz-correct">B. A firewall between zones stops a compromise in one zone from automatically reaching the next ✓</li>
            <li class="quiz-option">C. Zones make the network use less electricity</li>
            <li class="quiz-option">D. Zones are only a visual label with no real effect</li>
        </ul>
        <p class="quiz-explanation"><em>The firewall is the enforcement point: without it, one compromised device means every device is reachable.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> build the wall between the public dock and the command deck. Block routed traffic
        by default, then open exactly the one service the dock still needs. <strong>Success:</strong> every
        objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c4-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm4', 'lesson' => 'lesson02']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the network simulator full screen: real devices split into zones, wired through a firewall. You
            configure it with Windows networking commands, such as
            <code>netsh advfirewall set allprofiles firewallpolicy blockinbound,allowoutbound</code> and
            <code>netsh advfirewall firewall add rule name=AllowWeb dir=in action=allow protocol=TCP localport=80</code>.
        </p>
    </div>

</div>
