{{-- M6 · Lesson 6.3: Intrusion Detection & Prevention — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g6-l3.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="03">

    <h1 class="lesson-heading">Lesson 6.3: Intrusion Detection & Prevention</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can compare IDS and IPS, network-based and host-based sensors, and signature-based and anomaly-based detection, and tune a sensor's false positives.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A firewall decides by rules about addresses and ports. An <strong>intrusion detection system</strong> (IDS) looks <em>inside</em> the traffic for signs of an attack. Three choices describe any sensor:
    </p>
    <ul class="body-list">
        <li><strong>IDS or IPS</strong>: an IDS gets a <em>copy</em> of the traffic (from a SPAN port or a tap) and raises alerts; it can't stop anything, and it can't break anything either. An <strong>IPS</strong> (prevention) sits <strong>inline</strong>, so traffic passes through it, and it can drop attacks or block an attacker. The risk: a wrong decision blocks real users.</li>
        <li><strong>Network-based or host-based</strong>: a <strong>NIDS/NIPS</strong> watches a network link and sees everything crossing it. A <strong>HIDS</strong> runs on one computer and sees what happens there, including encrypted traffic after it's decrypted, and file changes.</li>
        <li><strong>Signature-based or anomaly-based</strong>: signatures match known attack patterns (precise, but blind to new attacks). Anomaly detection learns what <em>normal</em> looks like and flags what's different (catches new tricks, but raises more false alarms).</li>
    </ul>
    <p class="body-text">
        Every sensor makes mistakes. A <strong>false positive</strong> is an alert about something harmless (an authorized scan); a <strong>false negative</strong> is an attack nobody noticed, which is worse. <strong>Tuning</strong> means excluding known-good sources from noisy signatures and turning on blocking only for signatures you trust.
    </p>
    <pre class="code-block"><code>sensor mode inline
signature 2001219 action deny-attacker-inline
signature 1000123 exclude source 192.168.20.5</code></pre>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Which mistake is worse for a sensor?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A false positive</li>
            <li class="quiz-option">B. An alert on a scan</li>
            <li class="quiz-option quiz-correct">C. A false negative ✓</li>
            <li class="quiz-option">D. A logged warning</li>
        </ul>
        <p class="quiz-explanation"><em>A false negative is an attack nobody noticed. That is worse than a false alarm about something harmless.</em></p>
    </div>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        Planet Doom has stopped knocking on the front door and started trying the windows. The Citadel's new sensor, SHIELD-IPS, has been watching all night, but only <em>watching</em>. "It saw Vex try four hundred passwords a minute," says <strong>Volt</strong>, "and politely wrote it down."
    </p>
    <p class="body-text">
        "It also flagged your own scanner," <strong>Rivet</strong> points out. "If we let it block things, it'll block you." Volt sighs. "Then we tune it first."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why must the sensor be tuned before it blocks?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It missed every one of Vex's tries</li>
            <li class="quiz-option quiz-correct">B. It flagged the crew's own scanner too ✓</li>
            <li class="quiz-option">C. It can't see any traffic in inline mode</li>
            <li class="quiz-option">D. It only works on encrypted traffic</li>
        </ul>
        <p class="quiz-explanation"><em>An IPS that blocks on a noisy signature would block Volt's own scanner. Exclude known-good sources first.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 290" role="img" aria-label="An IDS watches a copy of the traffic and raises alerts; an IPS sits inline and can drop attacks; signature versus anomaly detection">
            <rect x="10" y="15" width="310" height="120" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="165.0" y="46.9" text-anchor="middle" font-size="14" font-weight="700" fill="#6b3a00">IDS (promiscuous)</text>
            <text x="165.0" y="63.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">gets a COPY of traffic</text>
            <text x="165.0" y="79.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">(SPAN port or tap)</text>
            <text x="165.0" y="96.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">alerts, can&#x27;t stop packets</text>
            <text x="165.0" y="112.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">no risk to the network</text>
            <rect x="340" y="15" width="310" height="120" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="495.0" y="46.9" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1820">IPS (inline)</text>
            <text x="495.0" y="63.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">traffic passes THROUGH it</text>
            <text x="495.0" y="79.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">can drop and block attackers</text>
            <text x="495.0" y="96.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">a mistake blocks real users</text>
            <text x="495.0" y="112.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">must be fast and tuned</text>
            <rect x="10" y="155" width="310" height="120" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="165.0" y="194.7" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">Signature-based</text>
            <text x="165.0" y="211.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">matches known attack patterns</text>
            <text x="165.0" y="227.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">precise, few false alarms</text>
            <text x="165.0" y="244.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">blind to brand-new attacks</text>
            <rect x="340" y="155" width="310" height="120" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="495.0" y="186.9" text-anchor="middle" font-size="14" font-weight="700" fill="#3b2a8a">Anomaly-based</text>
            <text x="495.0" y="203.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">learns what is normal</text>
            <text x="495.0" y="219.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">flags what is unusual</text>
            <text x="495.0" y="236.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">catches new attacks,</text>
            <text x="495.0" y="252.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">more false positives</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, how does an IPS see traffic?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Traffic passes through it ✓</li>
            <li class="quiz-option">B. It gets a SPAN copy</li>
            <li class="quiz-option">C. It reads a copy from a network tap</li>
            <li class="quiz-option">D. It reads only the logs</li>
        </ul>
        <p class="quiz-explanation"><em>An IPS sits inline, so traffic passes through it and it can drop attacks. An IDS only gets a copy.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is the main difference between an IDS and an IPS?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. IDS is faster</li>
            <li class="quiz-option quiz-correct">B. An IDS only alerts on a copy of traffic; an IPS sits inline and can block ✓</li>
            <li class="quiz-option">C. An IPS has no signatures</li>
            <li class="quiz-option">D. They are the same</li>
        </ul>
        <p class="quiz-explanation"><em>Detection systems watch and report; prevention systems are in the traffic path and can drop it.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which detection method can catch a brand-new attack with no known pattern?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Signature-based</li>
            <li class="quiz-option quiz-correct">B. Anomaly-based ✓</li>
            <li class="quiz-option">C. Neither</li>
            <li class="quiz-option">D. Port-based</li>
        </ul>
        <p class="quiz-explanation"><em>Anomaly detection flags unusual behavior, so it can spot attacks no signature describes yet.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. An alert fires for Volt's authorized drill scan. What is it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A false negative</li>
            <li class="quiz-option quiz-correct">B. A false positive ✓</li>
            <li class="quiz-option">C. A true positive</li>
            <li class="quiz-option">D. An anomaly</li>
        </ul>
        <p class="quiz-explanation"><em>An alert about harmless, authorized activity is a false positive.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why is a false negative usually worse than a false positive?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is louder</li>
            <li class="quiz-option quiz-correct">B. A real attack went unnoticed ✓</li>
            <li class="quiz-option">C. It costs more storage</li>
            <li class="quiz-option">D. It blocks users</li>
        </ul>
        <p class="quiz-explanation"><em>A false positive wastes time; a false negative lets an attack through unseen.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Which sensor can see what happens inside encrypted traffic once a server decrypts it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A NIDS on the core link</li>
            <li class="quiz-option quiz-correct">B. A host-based IDS on the server ✓</li>
            <li class="quiz-option">C. A switch</li>
            <li class="quiz-option">D. A firewall ACL</li>
        </ul>
        <p class="quiz-explanation"><em>A HIDS runs on the host itself, after decryption, and also sees file and process changes.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> read SHIELD-IPS's night of alerts, move it from watching to blocking, and tune out the false alarm. <strong>Success:</strong> Vex's brute force is blocked inline, and Volt's drill scanner is left alone.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g6-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm6', 'lesson' => 'lesson03']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
