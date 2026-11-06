{{-- M1 · Lesson 1.2: How Security Grew Up — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a1-l2.json) completes the lesson.
Syllabus: ITP1333 Unit A.2 Evolution of Info Security. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="02">

    <h1 class="lesson-heading">Lesson 1.2: How Security Grew Up</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> describe how information security evolved from physical guarding to today's networked defenses, and explain why older layers never fully disappear.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Long before computers, security meant guards, locks and vaults — keep the wrong people out of the room.
        When the Citadel's first computers arrived, security meant a single admin password on one shared machine.
        Then machines started talking to each other, and security had to cover the wires between them too: firewalls,
        antivirus, encrypted channels. Today the Citadel's systems talk to allied fleets across open space, so
        security also has to assume any of those channels could already be compromised — check every request, not
        just the ones from outside.
    </p>

    <p class="body-text">
        Notice what didn't happen: nothing got thrown away. The vault door is still locked. The admin password still
        matters. The firewall is still up. Each era added a layer on top of the one before it, because each new way
        of connecting created a new way to attack.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What happened to the old security layers as new eras came?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. They stayed, and new ones were added ✓</li>
            <li class="quiz-option">B. They were removed to save money and time</li>
            <li class="quiz-option">C. They were replaced by a single firewall</li>
            <li class="quiz-option">D. They moved to the allied fleets instead</li>
        </ul>
        <p class="quiz-explanation"><em>Nothing got thrown away. Each era added a layer on top, because each new connection made a new way to attack.</em></p>
    </div>

    <h2 class="section-heading">Volt's Archive</h2>

    <p class="body-text">
        Volt pulls up the Citadel's oldest maintenance logs for the new cadets. "Look — this console from Codexia's
        founding only needed one thing: a locked door. Then someone put a password on the terminal, because the door
        wasn't always locked. Then we networked every terminal together, and a password wasn't enough anymore —
        Rivet had to build the first firewall. Now our systems talk to three allied fleets, and even a firewall
        assumes too much. We verify every request, from inside or outside, every time."
    </p>

    <p class="body-text">
        "None of the old layers came down," Volt adds. "We just kept building higher."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why isn't a firewall enough for the Citadel today?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The firewall was removed two years ago</li>
            <li class="quiz-option">B. Passwords have replaced all firewalls now</li>
            <li class="quiz-option">C. Doom has no way to reach the Citadel at all</li>
            <li class="quiz-option quiz-correct">D. It talks to allied fleets over open space ✓</li>
        </ul>
        <p class="quiz-explanation"><em>With channels to three fleets, even a firewall assumes too much. The crew verifies every request, inside or out.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 242" role="img" aria-label="Security evolved in layers from physical guarding to zero trust, and no layer was removed">
            <rect x="40" y="20" width="560" height="38" rx="10" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="320" y="44" text-anchor="middle" font-size="14.5" font-weight="700" fill="#1c4f8f">Era 1: Physical — locked doors, guards</text>
            <rect x="40" y="66" width="560" height="38" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="90" text-anchor="middle" font-size="14.5" font-weight="700" fill="#4a35b8">Era 2: Single Machine — admin password</text>
            <rect x="40" y="112" width="560" height="38" rx="10" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="136" text-anchor="middle" font-size="14.5" font-weight="700" fill="#8a4b00">Era 3: Networked — firewalls, antivirus</text>
            <rect x="40" y="158" width="560" height="38" rx="10" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="320" y="182" text-anchor="middle" font-size="14.5" font-weight="700" fill="#146c3a">Era 4: Zero Trust — verify every request, always</text>
            <text x="320" y="228" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">Each era adds a layer — none of the old ones come down</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what is Era 4?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Zero Trust ✓</li>
            <li class="quiz-option">B. Physical guards</li>
            <li class="quiz-option">C. Single machine</li>
            <li class="quiz-option">D. Networked firewalls</li>
        </ul>
        <p class="quiz-explanation"><em>Era 4, Zero Trust, means verifying every request, always.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What did information security mean before computers existed?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Firewalls and antivirus</li>
            <li class="quiz-option quiz-correct">B. Physical guarding — locked doors, vaults, guards ✓</li>
            <li class="quiz-option">C. Encrypted email</li>
            <li class="quiz-option">D. Zero trust networking</li>
        </ul>
        <p class="quiz-explanation"><em>Before computers, protecting information meant physically keeping the wrong people out of the room.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. When Citadel systems first became networked, what new layer became necessary?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A locked door</li>
            <li class="quiz-option quiz-correct">B. Firewalls and antivirus to cover the wires between machines ✓</li>
            <li class="quiz-option">C. Nothing — the old protections were already enough</li>
            <li class="quiz-option">D. A single admin password</li>
        </ul>
        <p class="quiz-explanation"><em>Networking machines together meant attacks could now travel over the wires, so firewalls and antivirus became necessary.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does "zero trust" mean, in the way Volt describes it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Trusting every request that comes from inside the network</li>
            <li class="quiz-option">B. Never connecting to any other fleet</li>
            <li class="quiz-option quiz-correct">C. Verifying every request, whether it comes from inside or outside ✓</li>
            <li class="quiz-option">D. Removing all passwords since they can be stolen</li>
        </ul>
        <p class="quiz-explanation"><em>Zero trust assumes any channel could be compromised, so every request is checked, not just ones from outside.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. According to Volt, what happens to an older security layer (like the locked door) once a newer layer (like the firewall) is added?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It gets removed, since the new layer replaces it</li>
            <li class="quiz-option quiz-correct">B. It stays in place — the new layer is added on top ✓</li>
            <li class="quiz-option">C. It is disabled until the newer layer fails</li>
            <li class="quiz-option">D. It becomes illegal under Codexia law</li>
        </ul>
        <p class="quiz-explanation"><em>Each era's security keeps the older layers and adds a new one on top — layers accumulate, they don't replace each other.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why did each new way of connecting create a new way to attack?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It didn't — connecting has never introduced new risk</li>
            <li class="quiz-option quiz-correct">B. Because every new channel or system is also a new path an attacker could use ✓</li>
            <li class="quiz-option">C. Because passwords stopped working entirely</li>
            <li class="quiz-option">D. Because guards refused to work with computers</li>
        </ul>
        <p class="quiz-explanation"><em>Every new way for legitimate users to connect is also a new path an attacker might try to use — hence a new layer of defense.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Volt wants the cadets to trace the Citadel's own security history before adding today's next layer. Read the archive, then order the eras correctly and confirm which layer is active today.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a1-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm1', 'lesson' => 'lesson02']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. Read the archive files in
            order, then classify what era each control belongs to. Objectives tick off as you go.
        </p>
    </div>

</div>
