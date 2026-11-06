{{-- M2 · Lesson 2.6: EIGRP: Metrics, Load Balancing & Convergence — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g2-l6.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="06">

    <h1 class="lesson-heading">Lesson 2.6: EIGRP: Metrics, Load Balancing & Convergence</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can calculate an EIGRP metric, find the successor and feasible successor in a topology table, and turn on unequal-cost load balancing.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>EIGRP</strong> (Enhanced Interior Gateway Routing Protocol) is Cisco's advanced distance-vector protocol. It is fast to converge and easy to set up, and it is still running in many older networks. Its <strong>metric</strong> uses the slowest bandwidth on the path and the total delay:
    </p>
    <pre class="code-block"><code>metric = 256 × ( 10,000,000 ÷ slowest bandwidth in kbps
               + total delay in microseconds ÷ 10 )</code></pre>
    <p class="body-text">
        Example: slowest link 100,000 kbps (100 Mbps), total delay 2,000 µs. 10,000,000 ÷ 100,000 = 100, and 2,000 ÷ 10 = 200, so the metric is 256 × 300 = <strong>76,800</strong>.
    </p>
    <p class="body-text">
        EIGRP keeps every path it hears in a <strong>topology table</strong>, with two numbers per path:
    </p>
    <ul class="body-list">
        <li><strong>Reported distance (RD)</strong>: the neighbor's own metric to the network.</li>
        <li><strong>Feasible distance (FD)</strong>: the best total metric from this router.</li>
        <li>The best path is the <strong>successor</strong>. A backup path is a <strong>feasible successor</strong> only if its RD is <em>lower</em> than the FD. That rule (the feasibility condition) guarantees the backup cannot loop back through this router.</li>
    </ul>
    <p class="body-text">
        That is EIGRP's convergence trick: when the successor dies, a feasible successor takes over <em>instantly</em>, with no recalculation. EIGRP load-balances across equal paths by default, and with <code>variance</code> across <em>unequal</em> ones: <code>variance 2</code> uses every feasible successor whose metric is less than 2 × FD.
    </p>
    <pre class="code-block"><code>router eigrp 100
 network 10.1.0.0 0.0.255.255
 variance 2</code></pre>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">When is a backup path a feasible successor?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. When its FD is higher than the RD</li>
            <li class="quiz-option">B. When its RD is equal to the variance</li>
            <li class="quiz-option">C. When it has the slowest link bandwidth</li>
            <li class="quiz-option quiz-correct">D. When its RD is lower than the FD ✓</li>
        </ul>
        <p class="quiz-explanation"><em>The feasibility condition: the backup's reported distance must be lower than the feasible distance, so it can't loop back.</em></p>
    </div>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        Deep Well's mining company built its network years ago, on <strong>EIGRP</strong>, and nobody is rebuilding it. "Old doesn't mean bad," says <strong>Rivet</strong>, plugging into DEEPWELL-R1. "EIGRP keeps a spare route ready before it needs one. When a link drops in the mines, the backup is already loaded."
    </p>
    <p class="body-text">
        "Three cables run to the mine floor," he adds, "and only one carries anything. Let's find out which spares are safe, and put the good one to work."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What does Rivet like about EIGRP in the mines?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It was built only last week by the mine crew itself</li>
            <li class="quiz-option quiz-correct">B. The backup route is ready before it's needed ✓</li>
            <li class="quiz-option">C. It sends no hellos to the other routers</li>
            <li class="quiz-option">D. It uses only one cable to the mine floor</li>
        </ul>
        <p class="quiz-explanation"><em>EIGRP keeps a feasible successor ready. When a link drops, the backup takes over instantly.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="EIGRP topology: three paths to the mine network with their feasible and reported distances, the successor and the feasible successor">
            <rect x="10" y="120" width="120" height="56" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="70.0" y="152.6" text-anchor="middle" font-size="13" font-weight="700" fill="#3b2a8a">DEEPWELL-R1</text>
            <rect x="270" y="20" width="140" height="50" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="340.0" y="40.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14663f">via 10.1.1.2</text>
            <text x="340.0" y="56.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">3072 / RD 2816</text>
            <rect x="270" y="123" width="140" height="50" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="340.0" y="143.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#6b3a00">via 10.1.2.2</text>
            <text x="340.0" y="159.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">5120 / RD 2816</text>
            <rect x="270" y="226" width="140" height="50" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="340.0" y="246.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#3a4352">via 10.1.3.2</text>
            <text x="340.0" y="262.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">6656 / RD 3328</text>
            <rect x="520" y="120" width="130" height="56" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="585.0" y="143.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">mine LAN</text>
            <text x="585.0" y="159.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">192.168.60.0/24</text>
            <line x1="130" y1="148" x2="270" y2="45" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="410" y1="45" x2="520" y2="148" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="130" y1="148" x2="270" y2="148" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="410" y1="148" x2="520" y2="148" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="130" y1="148" x2="270" y2="251" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="410" y1="251" x2="520" y2="148" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <text x="470" y="30" text-anchor="middle" font-size="11.5" font-weight="600" fill="#14663f">successor (FD 3072)</text>
            <text x="470" y="205" text-anchor="middle" font-size="11.5" font-weight="600" fill="#c06a00">feasible successor</text>
            <text x="470" y="290" text-anchor="middle" font-size="11.5" font-weight="600" fill="#3a4352">not loop-safe: RD 3328 ≥ FD</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, why is the path via 10.1.3.2 not loop-safe?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Its RD 3328 is not below the FD ✓</li>
            <li class="quiz-option">B. Its total metric is the lowest of all</li>
            <li class="quiz-option">C. It is the successor for the mine LAN</li>
            <li class="quiz-option">D. Its RD 2816 is lower than the FD 3072</li>
        </ul>
        <p class="quiz-explanation"><em>The FD is 3072. The path via 10.1.3.2 has RD 3328, which is not lower, so it fails the feasibility condition.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Slowest link 10,000 kbps, total delay 1,000 µs. What is the EIGRP metric?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. 256 × 1,100 = 281,600 ✓</li>
            <li class="quiz-option">B. 256 × 1,000 = 256,000</li>
            <li class="quiz-option">C. 1,100</li>
            <li class="quiz-option">D. 256 × 110 = 28,160</li>
        </ul>
        <p class="quiz-explanation"><em>10,000,000 ÷ 10,000 = 1,000, and 1,000 ÷ 10 = 100. 1,000 + 100 = 1,100, times 256 = 281,600.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is the feasibility condition for a feasible successor?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Its FD equals the successor's FD</li>
            <li class="quiz-option quiz-correct">B. Its reported distance is lower than the current feasible distance ✓</li>
            <li class="quiz-option">C. It has the most bandwidth</li>
            <li class="quiz-option">D. It was learned first</li>
        </ul>
        <p class="quiz-explanation"><em>RD < FD proves the neighbor's path does not run back through this router, so it is loop-free.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why does EIGRP converge so fast when the successor fails?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It reboots</li>
            <li class="quiz-option quiz-correct">B. A feasible successor is already in the topology table and takes over at once ✓</li>
            <li class="quiz-option">C. It floods LSAs</li>
            <li class="quiz-option">D. It waits for a timer</li>
        </ul>
        <p class="quiz-explanation"><em>The feasible successor was chosen in advance, so EIGRP switches to it immediately without asking neighbors.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. FD is 3,072. A feasible successor has metric 5,120. Which <code>variance</code> lets EIGRP share traffic over both?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. variance 1</li>
            <li class="quiz-option quiz-correct">B. variance 2 ✓</li>
            <li class="quiz-option">C. variance 0</li>
            <li class="quiz-option">D. No variance can</li>
        </ul>
        <p class="quiz-explanation"><em>variance 2 allows paths with a metric under 2 × 3,072 = 6,144. 5,120 qualifies.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. A path has metric 6,656 and RD 3,328, with FD 3,072. Why will <code>variance 3</code> still not use it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Its metric is too high</li>
            <li class="quiz-option quiz-correct">B. It is not a feasible successor: its RD is not lower than the FD ✓</li>
            <li class="quiz-option">C. Variance only works on equal paths</li>
            <li class="quiz-option">D. It is the successor already</li>
        </ul>
        <p class="quiz-explanation"><em>Variance only uses feasible successors. RD 3,328 is not below FD 3,072, so the path could loop and is never used.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> read Deep Well's EIGRP topology table, work out a link's metric by hand, and put the safe spare path to work. <strong>Success:</strong> the mine network is reached over two paths at once.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g2-l6" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm2', 'lesson' => 'lesson06']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
