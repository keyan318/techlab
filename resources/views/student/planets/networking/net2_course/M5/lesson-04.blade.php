{{-- M5 · Lesson 5.4: Congestion Management: RED & Load Balancing — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g5-l4.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="04">

    <h1 class="lesson-heading">Lesson 5.4: Congestion Management: RED & Load Balancing</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain tail drop and TCP global synchronization, configure WRED, and choose a load-balancing strategy that spreads traffic without reordering it.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        When a queue is full, a router has no choice: new packets are dropped. That is <strong>tail drop</strong>, and it hurts TCP in a sneaky way. Every TCP flow loses packets at the same moment, so they all slow down together, the link goes half empty, they all speed up together, the queue overflows again. This sawtooth is <strong>TCP global synchronization</strong>, and it wastes the link.
    </p>
    <p class="body-text">
        <strong>RED</strong> (Random Early Detection) drops a few packets <em>before</em> the queue is full. Below a <strong>minimum threshold</strong> nothing is dropped; between the minimum and <strong>maximum threshold</strong> a growing random share is dropped; above the maximum, everything new is dropped. Random drops hit different flows at different times, so they back off one by one and the link stays busy.
    </p>
    <ul class="body-list">
        <li><strong>WRED</strong> (Weighted RED) uses different thresholds per DSCP value, so scavenger traffic is dropped early and important traffic last. <code>random-detect dscp-based</code>.</li>
        <li><strong>ECN</strong> (Explicit Congestion Notification) marks packets instead of dropping them, if both ends support it.</li>
        <li>Never use RED on voice: voice doesn't back off, it just breaks. Voice belongs in the priority queue.</li>
    </ul>
    <pre class="code-block"><code>policy-map EARTH-OUT
 class class-default
  fair-queue
  random-detect dscp-based</code></pre>
    <p class="body-text">
        The other way to handle more traffic is to <strong>spread</strong> it: over equal-cost routes (ECMP), over bundled links (EtherChannel), or over servers (load balancers). The important choice is the unit you spread:
    </p>
    <ul class="body-list">
        <li><strong>Per-flow</strong> (per-destination): a hash of the addresses (and sometimes ports) picks one link for each flow. A flow's packets stay in order. This is the default and almost always right.</li>
        <li><strong>Per-packet</strong>: packets take turns. The spread is perfect, but packets of one flow arrive <em>out of order</em>, which TCP treats as loss.</li>
    </ul>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Why should RED never be used on voice?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Voice packets have no DSCP marking</li>
            <li class="quiz-option quiz-correct">B. Voice doesn't back off, it just breaks ✓</li>
            <li class="quiz-option">C. RED makes all voice calls go much too fast</li>
            <li class="quiz-option">D. Voice is always sent per-packet</li>
        </ul>
        <p class="quiz-explanation"><em>RED works by making TCP slow down. Voice doesn't back off, so it just breaks. It belongs in the priority queue.</em></p>
    </div>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        The Citadel's link to Earth hits 100% every evening, and the graph looks like a saw blade: full, half empty, full, half empty. "Every download on the Grid is slowing down and speeding up in step," says <strong>Volt</strong>. "Like a crowd that all stops at once and all starts at once."
    </p>
    <p class="body-text">
        <strong>Rivet</strong> suggests a fix of his own: "Two links to Earth. Just send every other packet down the second one." Volt winces. "Let's check what that does to TCP first."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why does Volt wince at sending every other packet down the second link?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The second link to Earth is much too slow</li>
            <li class="quiz-option">B. Every flow would stay on just the one link</li>
            <li class="quiz-option">C. It would turn on RED for all the voice calls</li>
            <li class="quiz-option quiz-correct">D. One flow's packets would arrive out of order ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Per-packet balancing spreads perfectly but reorders a flow's packets, which TCP treats as loss. Per-flow is the safe default.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="Tail drop makes all TCP flows slow down together; RED drops a few packets early so flows back off at different times">
            <rect x="10" y="15" width="310" height="130" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="165.0" y="51.9" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1820">Tail drop</text>
            <text x="165.0" y="68.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">queue full → drop everything new</text>
            <text x="165.0" y="84.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">every TCP flow slows at once,</text>
            <text x="165.0" y="101.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">then all speed up together:</text>
            <text x="165.0" y="117.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">global synchronization</text>
            <rect x="340" y="15" width="310" height="130" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="495.0" y="51.9" text-anchor="middle" font-size="14" font-weight="700" fill="#14663f">RED / WRED</text>
            <text x="495.0" y="68.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">queue filling → drop a few at random</text>
            <text x="495.0" y="84.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">flows back off one at a time</text>
            <text x="495.0" y="101.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">WRED: drop low-priority DSCP first</text>
            <text x="495.0" y="117.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">ECN: mark instead of drop</text>
            <rect x="10" y="165" width="640" height="50" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="330.0" y="185.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#3a4352">min threshold  ·  max threshold  ·  mark probability</text>
            <text x="330.0" y="201.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">below min: no drops · between: random drops, rising · above max: drop all</text>
            <rect x="10" y="230" width="310" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="165.0" y="255.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">Per-flow load balancing</text>
            <text x="165.0" y="271.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">one flow stays on one link: no reordering</text>
            <rect x="340" y="230" width="310" height="60" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="495.0" y="255.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#6b3a00">Per-packet load balancing</text>
            <text x="495.0" y="271.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">even spread, but packets arrive out of order</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what does RED do as the queue fills?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Drops a few at random ✓</li>
            <li class="quiz-option">B. Drops everything new at once</li>
            <li class="quiz-option">C. Speeds up every flow</li>
            <li class="quiz-option">D. Marks all as voice</li>
        </ul>
        <p class="quiz-explanation"><em>RED drops a few packets early and at random, so flows back off one at a time and the link stays busy.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is TCP global synchronization?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Clocks agreeing</li>
            <li class="quiz-option quiz-correct">B. Many TCP flows losing packets together, slowing and speeding up in step, wasting the link ✓</li>
            <li class="quiz-option">C. A routing protocol</li>
            <li class="quiz-option">D. Encrypted TCP</li>
        </ul>
        <p class="quiz-explanation"><em>Tail drop hits every flow at once, so they all back off and recover together, leaving the link underused.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. How does RED prevent it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. By buying more bandwidth</li>
            <li class="quiz-option quiz-correct">B. By dropping a few random packets early, so flows back off at different times ✓</li>
            <li class="quiz-option">C. By blocking TCP</li>
            <li class="quiz-option">D. By prioritizing voice</li>
        </ul>
        <p class="quiz-explanation"><em>Early, random drops spread the slow-downs over time.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does WRED add to RED?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Encryption</li>
            <li class="quiz-option quiz-correct">B. Different drop thresholds per DSCP value, so less important traffic is dropped first ✓</li>
            <li class="quiz-option">C. More queues</li>
            <li class="quiz-option">D. Voice support</li>
        </ul>
        <p class="quiz-explanation"><em>Weighted RED looks at the marking and drops scavenger traffic before important traffic.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why is per-packet load balancing usually a bad idea?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is slow</li>
            <li class="quiz-option quiz-correct">B. Packets of one flow arrive out of order, which TCP treats as loss ✓</li>
            <li class="quiz-option">C. It uses only one link</li>
            <li class="quiz-option">D. It needs MPLS</li>
        </ul>
        <p class="quiz-explanation"><em>Different links have different delays, so a flow split per packet gets reordered.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why should RED never be used on voice traffic?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Voice is encrypted</li>
            <li class="quiz-option quiz-correct">B. Voice doesn't slow down when dropped; it just breaks up ✓</li>
            <li class="quiz-option">C. Voice uses TCP</li>
            <li class="quiz-option">D. Voice has no DSCP</li>
        </ul>
        <p class="quiz-explanation"><em>RED works by making TCP back off. Real-time voice has no such reaction, so drops only hurt it. Voice goes in the priority queue.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> diagnose the Citadel's saw-blade traffic graph, turn on WRED, and decide how to spread traffic over two links to Earth. <strong>Success:</strong> random early drops replace tail drops, and the load-balancing choice keeps every flow in order.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g5-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm5', 'lesson' => 'lesson04']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
