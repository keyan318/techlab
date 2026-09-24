{{-- M4 · Lesson 4.3: Network Security — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab runs in NetSim, not Citadel Sim (public/netsim-app/labs/c4-l3.json), but keeps the
"Defend it yourself" framing and cybersecurity CTA styling of the rest of this course.
Syllabus: ITP1232 Unit D.3 Network Security. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="03">

    <h1 class="lesson-heading">Lesson 4.3: Network Security</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what a port scan reveals, why unused open ports are a
        security risk, and harden a machine's firewall to close the ports it doesn't need while keeping the
        one it does.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Every network service listens on a <strong>port</strong>: a numbered door on a machine. Web servers
        usually listen on port 80, file transfer on port 21, remote desktop on port 3389, and so on. A
        <strong>port scan</strong> checks a range of ports to see which ones answer, which is exactly what an
        attacker does first, before trying anything else: find every open door, then try each one.
    </p>

    <p class="body-text">
        This is why <strong>least functionality</strong> matters: a machine should only run the services it
        actually needs, and its firewall should only allow the ports those services use. Every extra open port
        is one more door an attacker didn't have to pick a lock for — it was just standing open.
    </p>

    <h2 class="section-heading">Volt's Drill</h2>

    <p class="body-text">
        23:50. <strong>Volt</strong> spots Commander Vex's scouts sweeping the Citadel's outer hull, port by
        port. "They're checking every door: twenty-one, eighty, three-three-eight-nine. Three doors found, and
        only one of them should even exist."
    </p>

    <p class="body-text">
        <strong>Astro</strong> gives the order: "Scan our own hull first, so we know exactly what Vex is about
        to find." <strong>Rivet</strong> adds: "The web door stays open, the crew needs it. Everything else that
        answered a knock — weld it shut."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="A port scan checks ports 21, 80 and 3389 on the outer hull; after hardening, only port 80 stays open while 21 and 3389 are filtered">
            <rect x="20" y="20" width="180" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="110" y="45" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">volt (scanner)</text>
            <text x="110" y="65" text-anchor="middle" font-size="12" fill="#14306b">Test-NetConnection</text>

            <rect x="330" y="20" width="290" height="170" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="475" y="42" text-anchor="middle" font-size="14" font-weight="700" fill="#8a4b00">outer-hull</text>

            <rect x="350" y="55" width="120" height="34" rx="8" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="410" y="76" text-anchor="middle" font-size="12.5" fill="#7a1a14">21 (FTP) — blocked</text>

            <rect x="350" y="95" width="120" height="34" rx="8" fill="#e6f6ec" stroke="#3aa876" stroke-width="2"/>
            <text x="410" y="116" text-anchor="middle" font-size="12.5" fill="#1f6e4c">80 (web) — open</text>

            <rect x="350" y="135" width="120" height="34" rx="8" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="410" y="156" text-anchor="middle" font-size="12.5" fill="#7a1a14">3389 (RDP) — blocked</text>

            <line x1="200" y1="50" x2="326" y2="72" stroke="#7c5cff" stroke-width="3"/>
            <line x1="200" y1="50" x2="326" y2="112" stroke="#7c5cff" stroke-width="3"/>
            <line x1="200" y1="50" x2="326" y2="152" stroke="#7c5cff" stroke-width="3"/>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does a port scan do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It deletes files on the target machine</li>
            <li class="quiz-option quiz-correct">B. It checks a range of ports to see which ones respond ✓</li>
            <li class="quiz-option">C. It changes a machine's password</li>
            <li class="quiz-option">D. It encrypts network traffic</li>
        </ul>
        <p class="quiz-explanation"><em>A port scan probes ports one by one (or all at once) to discover which services are listening and reachable.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why do attackers usually port-scan a target before doing anything else?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It's required by law before any attack</li>
            <li class="quiz-option quiz-correct">B. It reveals which doors (services) exist to try next ✓</li>
            <li class="quiz-option">C. It automatically breaks in on its own</li>
            <li class="quiz-option">D. It only works after the attack is already over</li>
        </ul>
        <p class="quiz-explanation"><em>Reconnaissance comes first: a scan maps out what's open before an attacker decides where to focus.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does "least functionality" mean as a security principle?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Machines should run as many services as possible, just in case</li>
            <li class="quiz-option quiz-correct">B. A machine should only run the services it actually needs ✓</li>
            <li class="quiz-option">C. Firewalls should never block any traffic</li>
            <li class="quiz-option">D. Only administrators are allowed to use a computer</li>
        </ul>
        <p class="quiz-explanation"><em>Every unnecessary running service is an unnecessary open door — least functionality means closing the ones you don't need.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. In this lesson's lab, why does port 80 need an explicit "allow" rule, even after only blocking 21 and 3389?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Port 80 is always open no matter what</li>
            <li class="quiz-option quiz-correct">B. Turning the firewall on switches it to block-everything-by-default, so the needed port must be explicitly allowed ✓</li>
            <li class="quiz-option">C. Allow rules are only cosmetic and don't affect traffic</li>
            <li class="quiz-option">D. Port 80 cannot be blocked under any configuration</li>
        </ul>
        <p class="quiz-explanation"><em>Once a firewall is switched on, its default policy blocks everything; you must explicitly allow the service you still want reachable.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. FTP (port 21) and RDP (port 3389) were open on the outer hull with no real use for them. What should happen to those ports?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Leave them open in case they're needed someday</li>
            <li class="quiz-option quiz-correct">B. Block them: an open port with no real use is pure risk with no benefit ✓</li>
            <li class="quiz-option">C. Rename them to confuse attackers</li>
            <li class="quiz-option">D. Open even more ports to distract attackers</li>
        </ul>
        <p class="quiz-explanation"><em>An open port that serves no purpose only adds attack surface — closing it removes risk at no real cost.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> scan the outer hull's ports yourself, then harden it — block every port that
        shouldn't be open, and keep the one that should. <strong>Success:</strong> every objective in the
        simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c4-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm4', 'lesson' => 'lesson03']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the network simulator full screen: real devices you scan and harden with Windows networking
            commands, such as <code>Test-NetConnection &lt;ip&gt; -Port &lt;port&gt;</code> and
            <code>netsh advfirewall firewall add rule name=BlockFTP dir=in action=block protocol=TCP localport=21</code>.
        </p>
    </div>

</div>
