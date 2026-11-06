{{-- M5 · Lesson 5.2: Policing, Shaping & Queuing — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g5-l2.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="02">

    <h1 class="lesson-heading">Lesson 5.2: Policing, Shaping & Queuing</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can choose between policing and shaping, compare priority, weighted fair and class-based queuing, and build a low-latency queuing policy.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Marks are only labels. This lesson makes the router <em>act</em> on them, with three kinds of tools:
    </p>
    <ul class="body-list">
        <li><strong>Policing</strong>: traffic above a rate is dropped (or re-marked) at once. No delay added, but TCP sees losses. Used to enforce limits, often inbound: "bulk traffic gets 1 Mbps, no more".</li>
        <li><strong>Shaping</strong>: traffic above a rate is held in a buffer and sent a little later. Smoother, adds some delay. Used outbound to match a slower contract, for example a 1 Gbps port on a 50 Mbps service.</li>
        <li><strong>Queuing</strong>: when the link is busy, which waiting packet goes next?</li>
    </ul>
    <p class="body-text">
        Queuing strategies, from simple to smart:
    </p>
    <ul class="body-list">
        <li><strong>FIFO</strong>: first in, first out. No priorities at all.</li>
        <li><strong>Priority queuing (PQ)</strong>: a high queue is always emptied first. Great for voice, but if it never empties, lower queues <em>starve</em>.</li>
        <li><strong>Weighted fair queuing (WFQ)</strong>: shares the link fairly between flows, so one big download can't hog it.</li>
        <li><strong>CBWFQ</strong> (class-based WFQ): each class gets a guaranteed share, like <code>bandwidth percent 30</code>.</li>
        <li><strong>LLQ</strong> (low latency queuing): CBWFQ plus a strict priority queue for voice, <em>capped</em> so it can't starve the others: <code>priority percent 20</code>. This is the standard design.</li>
    </ul>
    <pre class="code-block"><code>policy-map RADIO-OUT
 class VOICE
  priority percent 20
 class VIDEO
  bandwidth percent 30
 class BULK
  police 1000000 conform-action transmit exceed-action drop
 class class-default
  fair-queue
interface g0/2
 service-policy output RADIO-OUT</code></pre>
    <p class="body-text">
        Queuing only matters where the queue forms: on the way <em>out</em> of the slowest link. That is why this policy goes outbound on the radio.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">How is shaping different from policing?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Shaping drops the extra traffic straight away</li>
            <li class="quiz-option quiz-correct">B. Shaping buffers the extra and sends it later ✓</li>
            <li class="quiz-option">C. Shaping marks every packet as voice traffic</li>
            <li class="quiz-option">D. Shaping gives the voice queue strict priority</li>
        </ul>
        <p class="quiz-explanation"><em>Policing drops traffic over the rate at once. Shaping holds it in a buffer and sends it a little later.</em></p>
    </div>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        Northwatch's traffic is marked now, but the radio still treats every packet the same. "Labels without rules," says <strong>Rivet</strong>. "Tonight, voice goes first, video gets its share, and Earth's backup gets whatever is left, with a hard limit so it can't take over."
    </p>
    <p class="body-text">
        <strong>Astro</strong> adds one more job. "Earth's new link at Skyport is a gigabit port on a 50 megabit contract. Send faster than 50 and Earth's side throws the extra away. Smooth it out before it leaves."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why must Skyport's traffic to Earth be smoothed out?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Earth's side has no queues for voice</li>
            <li class="quiz-option quiz-correct">B. The contract is 50 Mbps on a gigabit port ✓</li>
            <li class="quiz-option">C. The radio link is jammed every single night</li>
            <li class="quiz-option">D. Earth's port only takes video traffic</li>
        </ul>
        <p class="quiz-explanation"><em>Send faster than 50 Mbps and Earth throws the extra away. Shaping outbound matches the contract.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="Policing cuts traffic above a rate, shaping smooths it by buffering, and LLQ gives voice a strict priority queue in front of the class queues">
            <rect x="10" y="15" width="310" height="90" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="165.0" y="47.4" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1820">Policing</text>
            <text x="165.0" y="63.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">over the limit → drop (or re-mark)</text>
            <text x="165.0" y="80.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">hard cap, no delay added</text>
            <rect x="340" y="15" width="310" height="90" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="495.0" y="47.4" text-anchor="middle" font-size="14" font-weight="700" fill="#6b3a00">Shaping</text>
            <text x="495.0" y="63.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">over the limit → wait in a buffer</text>
            <text x="495.0" y="80.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">smooth, adds a little delay</text>
            <rect x="10" y="125" width="150" height="40" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="85.0" y="149.4" text-anchor="middle" font-size="12.5" font-weight="700" fill="#7a1820">VOICE: priority</text>
            <rect x="10" y="175" width="150" height="40" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="85.0" y="199.4" text-anchor="middle" font-size="12.5" font-weight="700" fill="#6b3a00">VIDEO: 30%</text>
            <rect x="10" y="225" width="150" height="40" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="85.0" y="249.4" text-anchor="middle" font-size="12.5" font-weight="700" fill="#14663f">BULK: policed</text>
            <rect x="170" y="225" width="150" height="40" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="245.0" y="249.4" text-anchor="middle" font-size="12.5" font-weight="700" fill="#3a4352">default: fair</text>
            <rect x="400" y="155" width="120" height="80" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="460.0" y="190.3" text-anchor="middle" font-size="13" font-weight="700" fill="#3b2a8a">scheduler</text>
            <text x="460.0" y="205.8" text-anchor="middle" font-size="10.5" font-weight="500" fill="#3b2a8a">LLQ = CBWFQ + strict PQ</text>
            <line x1="160" y1="145" x2="391.1" y2="173.9" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="400,175 390.3,179.8 391.8,167.9" fill="#7c5cff"/>
            <line x1="160" y1="195" x2="391.0" y2="190.2" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="400,190 391.1,196.2 390.9,184.2" fill="#7c5cff"/>
            <line x1="160" y1="245" x2="391.1" y2="206.5" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="400,205 392.1,212.4 390.1,200.6" fill="#7c5cff"/>
            <line x1="320" y1="245" x2="391.4" y2="222.7" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="400,220 393.2,228.4 389.6,217.0" fill="#7c5cff"/>
            <rect x="560" y="170" width="90" height="50" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="605.0" y="190.1" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">radio</text>
            <text x="605.0" y="206.1" text-anchor="middle" font-size="11" font-weight="500" fill="#14306b">10 Mbps</text>
            <line x1="520" y1="195" x2="551.0" y2="195.0" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="560,195 551.0,201.0 551.0,189.0" fill="#7c5cff"/>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what is LLQ?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. FIFO with no priority</li>
            <li class="quiz-option">B. WFQ with no limits</li>
            <li class="quiz-option quiz-correct">C. CBWFQ plus a strict PQ ✓</li>
            <li class="quiz-option">D. Policing plus shaping, no queue</li>
        </ul>
        <p class="quiz-explanation"><em>LLQ is CBWFQ plus a strict priority queue for voice, capped so it can't starve the other classes.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What happens to traffic above the limit with policing?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is buffered and sent later</li>
            <li class="quiz-option quiz-correct">B. It is dropped or re-marked at once ✓</li>
            <li class="quiz-option">C. It is encrypted</li>
            <li class="quiz-option">D. It gets priority</li>
        </ul>
        <p class="quiz-explanation"><em>A policer enforces a hard rate: excess traffic is dropped (or re-marked), with no buffering.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Your port runs at 1 Gbps but your service contract is 50 Mbps. Which tool fits outbound?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Policing</li>
            <li class="quiz-option quiz-correct">B. Shaping ✓</li>
            <li class="quiz-option">C. FIFO</li>
            <li class="quiz-option">D. Marking</li>
        </ul>
        <p class="quiz-explanation"><em>Shaping buffers bursts and sends at 50 Mbps, so the provider doesn't drop the excess.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is the risk of plain priority queuing?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Voice gets delayed</li>
            <li class="quiz-option quiz-correct">B. Lower queues can starve if the priority queue is always busy ✓</li>
            <li class="quiz-option">C. It uses too much memory</li>
            <li class="quiz-option">D. It needs MPLS</li>
        </ul>
        <p class="quiz-explanation"><em>Strict priority always serves the top queue first; without a cap, everything else can wait forever.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What is LLQ?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A cable type</li>
            <li class="quiz-option quiz-correct">B. CBWFQ plus a capped strict priority queue for voice ✓</li>
            <li class="quiz-option">C. A routing protocol</li>
            <li class="quiz-option">D. A marking</li>
        </ul>
        <p class="quiz-explanation"><em>Low latency queuing gives voice strict priority, limited by a rate, and shares the rest with CBWFQ.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why is the queuing policy applied outbound on the radio interface?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Inbound is not allowed</li>
            <li class="quiz-option quiz-correct">B. Queues form where traffic leaves onto the slowest link ✓</li>
            <li class="quiz-option">C. The radio has no input</li>
            <li class="quiz-option">D. To mark packets</li>
        </ul>
        <p class="quiz-explanation"><em>Congestion happens when more traffic wants to leave than the link can carry, so queuing is configured on the egress interface.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> build a low-latency queuing policy for the Northwatch radio, police Earth's backups, and shape the Skyport contract link. <strong>Success:</strong> voice gets through without drops while the radio is full.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g5-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm5', 'lesson' => 'lesson02']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
