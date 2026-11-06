{{-- M2 · Lesson 2.2: Defense in Depth — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a2-l2.json) completes the lesson.
Syllabus: ITP1333 Unit B.2 Defense Models (Defense in Depth). --}}

<div class="lesson-fragment" data-module="m2" data-lesson="02">

    <h1 class="lesson-heading">Lesson 2.2: Defense in Depth</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> explain defense in depth as multiple, independent layers of protection, and identify what a single layer of defense misses.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Defense in depth</strong> means no single control is trusted to stop every attack. Instead, several
        independent layers stand between an attacker and what matters: a perimeter (firewall), the network itself
        (segmentation), the individual machine (antivirus, disabled unused accounts), and the data (encryption,
        access control). If one layer fails or is bypassed, the next layer is still there.
    </p>

    <p class="body-text">
        The key word is <strong>independent</strong>. Ten copies of the same lock don't add depth — an attacker who
        picks one picks them all. Depth comes from different kinds of controls, so a single trick or a single stolen
        password doesn't unravel everything at once.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What makes defense in depth work?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Many copies of the same lock</li>
            <li class="quiz-option">B. One very strong firewall alone</li>
            <li class="quiz-option">C. Layers that share one password</li>
            <li class="quiz-option quiz-correct">D. Layers that are independent ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Ten copies of the same lock add no depth. Different kinds of controls mean one trick doesn't beat them all.</em></p>
    </div>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        A cadet suggests putting three firewalls in a row, all with the same rules. Rivet stops them. "That's not
        depth, that's the same door three times. If Vex's crew finds a way past one, they're past all three. Real
        depth is a firewall at the edge, a locked-down account on the console, and an encrypted file even if
        someone reaches it. Three <em>different</em> kinds of walls, not one wall copied three times."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why does Rivet reject three identical firewalls?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Three firewalls cost far too much power</li>
            <li class="quiz-option">B. Firewalls can't be put in a single row</li>
            <li class="quiz-option quiz-correct">C. Get past one, and you're past all three ✓</li>
            <li class="quiz-option">D. Only two firewalls are allowed at once</li>
        </ul>
        <p class="quiz-explanation"><em>That's the same door three times. Real depth is different kinds of walls.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 242" role="img" aria-label="Defense in depth stacks independent layers: perimeter, network, host, and data">
            <rect x="40" y="20" width="560" height="38" rx="10" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="320" y="44" text-anchor="middle" font-size="14.5" font-weight="700" fill="#1c4f8f">Perimeter — firewall at the edge</text>
            <rect x="40" y="66" width="560" height="38" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="90" text-anchor="middle" font-size="14.5" font-weight="700" fill="#4a35b8">Network — segmented zones</text>
            <rect x="40" y="112" width="560" height="38" rx="10" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="136" text-anchor="middle" font-size="14.5" font-weight="700" fill="#8a4b00">Host — disabled unused accounts, closed ports</text>
            <rect x="40" y="158" width="560" height="38" rx="10" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="320" y="182" text-anchor="middle" font-size="14.5" font-weight="700" fill="#146c3a">Data — encryption, access control</text>
            <text x="320" y="228" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">Independent layers — bypassing one still leaves the next standing</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, which layer holds encryption?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Host</li>
            <li class="quiz-option">B. Network</li>
            <li class="quiz-option">C. Perimeter</li>
            <li class="quiz-option quiz-correct">D. Data ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The data layer holds encryption and access control. The perimeter is the firewall at the edge.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is the core idea behind defense in depth?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Buy the single most expensive firewall available</li>
            <li class="quiz-option quiz-correct">B. No single control is trusted alone — several independent layers stand between an attacker and what matters ✓</li>
            <li class="quiz-option">C. Only defend the network perimeter</li>
            <li class="quiz-option">D. Rely entirely on the crew's honesty</li>
        </ul>
        <p class="quiz-explanation"><em>Defense in depth spreads protection across multiple independent layers, so no one control has to be perfect.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why did Rivet reject three identical firewalls in a row?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Three firewalls are always too expensive</li>
            <li class="quiz-option quiz-correct">B. Identical layers don't add depth — bypassing one bypasses all of them ✓</li>
            <li class="quiz-option">C. Firewalls only work in pairs</li>
            <li class="quiz-option">D. Astro said firewalls were banned</li>
        </ul>
        <p class="quiz-explanation"><em>True depth comes from different kinds of controls, not multiple copies of the same one.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which of these is an example of a "host" layer of defense?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A firewall at the network edge</li>
            <li class="quiz-option">B. Encrypting a file</li>
            <li class="quiz-option quiz-correct">C. Disabling an unused account on a single machine ✓</li>
            <li class="quiz-option">D. A written security policy</li>
        </ul>
        <p class="quiz-explanation"><em>The host layer protects the individual machine itself — disabling unused accounts and closing unused ports are host-level controls.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. If an attacker bypasses the perimeter firewall, what does defense in depth guarantee?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing — the attack instantly succeeds</li>
            <li class="quiz-option quiz-correct">B. The network, host, and data layers are still there to stop or slow them ✓</li>
            <li class="quiz-option">C. The attacker automatically loses access to everything</li>
            <li class="quiz-option">D. The firewall was the only layer that mattered</li>
        </ul>
        <p class="quiz-explanation"><em>The whole point of layering is that bypassing one layer still leaves the others standing between the attacker and the goal.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What kind of layer is "encrypting a file" an example of?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Perimeter layer</li>
            <li class="quiz-option">B. Network layer</li>
            <li class="quiz-option">C. Host layer</li>
            <li class="quiz-option quiz-correct">D. Data layer ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Encryption protects the data itself, even if every other layer has already been bypassed.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> The relay console has only one layer of defense right now. Read the audit, then add the missing layers so a single trick can't unravel everything.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a2-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm2', 'lesson' => 'lesson02']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the audit, close the unused port, disable the unused account,
            and lock the sensitive file — three different layers, not one repeated.
        </p>
    </div>

</div>
