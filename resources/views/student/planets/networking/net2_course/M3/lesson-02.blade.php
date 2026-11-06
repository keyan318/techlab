{{-- M3 · Lesson 3.2: Scalability & Redundancy Planning — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g3-l2.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="02">

    <h1 class="lesson-heading">Lesson 3.2: Scalability & Redundancy Planning</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can plan addresses so routes summarize, calculate how redundancy raises availability, and spot single points of failure.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Scalability</strong> means the design still works when the network is ten times bigger. The biggest helper is <strong>hierarchical addressing</strong>: give each block (an outpost, a building) one contiguous range. Then a router can advertise one <strong>summary route</strong> instead of every subnet:
    </p>
    <pre class="code-block"><code>10.30.0.0/24, 10.30.1.0/24, 10.30.2.0/24, 10.30.3.0/24
= 10.30.0.0/22   (the first 22 bits are the same in all four)</code></pre>
    <p class="body-text">
        Fewer routes means smaller tables, less work for SPF, and a flapping subnet inside the block doesn't disturb anyone outside it. In OSPF, an ABR summarizes an area with <code>area 1 range 10.30.0.0 255.255.252.0</code>.
    </p>
    <p class="body-text">
        <strong>Redundancy</strong> means no single failure takes the service down. Find every <strong>single point of failure</strong> (SPOF): one device, one link, one power feed that everything depends on. Then add a second one, as independent as possible: different paths, different power, different cables in different trenches.
    </p>
    <p class="body-text">
        Redundancy multiplies: if one link is up 99% of the time, two independent links in parallel are both down only 1% × 1% = 0.01% of the time, so the pair is up <strong>99.99%</strong>. In a chain (series), it is the other way round: every extra device in the chain lowers availability, so keep critical paths short.
    </p>
    <ul class="body-list">
        <li><strong>Plan for growth</strong>: leave room in each block's range, and in switch ports and uplink speed.</li>
        <li><strong>Plan for failure</strong>: two of everything important, and test that failover actually works.</li>
        <li><strong>Keep it simple</strong>: every extra feature is something else that can break.</li>
    </ul>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Two independent links are each up 99% of the time. How often is the pair up?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 98% of the time</li>
            <li class="quiz-option quiz-correct">B. 99.99% of the time ✓</li>
            <li class="quiz-option">C. 99.5% of the time, no more</li>
            <li class="quiz-option">D. 100% of the time</li>
        </ul>
        <p class="quiz-explanation"><em>Both are down only 1% × 1% = 0.01% of the time, so the pair is up 99.99%.</em></p>
    </div>

    <h2 class="section-heading">Astro's Orders</h2>

    <p class="body-text">
        Earth's engineers send a review of the Grid. <strong>Astro</strong> reads it aloud: "Northwatch advertises four routes where one would do. One device carries every outpost's traffic. And nobody has worked out how reliable the radio links really are."
    </p>
    <p class="body-text">
        "Planning homework," groans <strong>Rivet</strong>. "No," says Astro, "planning <em>is</em> the work. The Grid will be ten times this size by next year. Fix it on paper now, or fix it at night later."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What does Astro say about the planning homework?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Planning is the work itself ✓</li>
            <li class="quiz-option">B. Planning can wait until later</li>
            <li class="quiz-option">C. The review can be ignored now</li>
            <li class="quiz-option">D. The radio links are good enough</li>
        </ul>
        <p class="quiz-explanation"><em>The Grid will be ten times bigger next year. Fix it on paper now, or fix it at night later.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 280" role="img" aria-label="Hierarchical addressing: four Northwatch subnets summarized as one /22 route, and two links in parallel giving higher availability">
            <rect x="10" y="15" width="190" height="40" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="105.0" y="39.9" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">10.30.0.0/24</text>
            <line x1="200" y1="35" x2="260" y2="110" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <rect x="10" y="63" width="190" height="40" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="105.0" y="87.9" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">10.30.1.0/24</text>
            <line x1="200" y1="83" x2="260" y2="110" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <rect x="10" y="111" width="190" height="40" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="105.0" y="135.9" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">10.30.2.0/24</text>
            <line x1="200" y1="131" x2="260" y2="110" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <rect x="10" y="159" width="190" height="40" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="105.0" y="183.9" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">10.30.3.0/24</text>
            <line x1="200" y1="179" x2="260" y2="110" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <rect x="260" y="85" width="170" height="52" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="345.0" y="106.5" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">10.30.0.0/22</text>
            <text x="345.0" y="123.0" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">one route to the core</text>
            <text x="470" y="30" text-anchor="start" font-size="12.5" font-weight="700" fill="#7a1820">Without summary: 4 routes</text>
            <text x="470" y="52" text-anchor="start" font-size="12.5" font-weight="700" fill="#14663f">With summary: 1 route</text>
            <rect x="10" y="215" width="300" height="55" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="160.0" y="237.7" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1820">one link: 99% up</text>
            <text x="160.0" y="254.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#7a1820">down 3.65 days a year</text>
            <rect x="350" y="215" width="300" height="55" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="500.0" y="237.7" text-anchor="middle" font-size="14" font-weight="700" fill="#14663f">two independent links: 99.99%</text>
            <text x="500.0" y="254.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">1 − (0.01 × 0.01): down 53 minutes a year</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what summary covers the four Northwatch subnets?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 10.30.0.0/24</li>
            <li class="quiz-option quiz-correct">B. 10.30.0.0/22 ✓</li>
            <li class="quiz-option">C. 10.30.0.0/16</li>
            <li class="quiz-option">D. 10.30.3.0/22</li>
        </ul>
        <p class="quiz-explanation"><em>10.30.0.0 to 10.30.3.0 share their first 22 bits, so one 10.30.0.0/22 route replaces four.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which single route summarizes 10.30.0.0/24 to 10.30.3.0/24?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 10.30.0.0/16</li>
            <li class="quiz-option quiz-correct">B. 10.30.0.0/22 ✓</li>
            <li class="quiz-option">C. 10.30.0.0/23</li>
            <li class="quiz-option">D. 10.30.3.0/24</li>
        </ul>
        <p class="quiz-explanation"><em>Four /24s that start on a multiple of 4 share their first 22 bits, so 10.30.0.0/22 covers exactly 10.30.0.0 to 10.30.3.255.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What makes route summarization possible?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Random addresses</li>
            <li class="quiz-option quiz-correct">B. Hierarchical addressing: one contiguous range per block ✓</li>
            <li class="quiz-option">C. Using only /32 routes</li>
            <li class="quiz-option">D. Static routes</li>
        </ul>
        <p class="quiz-explanation"><em>Summaries only work if each block's subnets sit together in one range.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. One link is up 90% of the time. What about two independent links in parallel?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 90%</li>
            <li class="quiz-option">B. 81%</li>
            <li class="quiz-option quiz-correct">C. 99% ✓</li>
            <li class="quiz-option">D. 180%</li>
        </ul>
        <p class="quiz-explanation"><em>Both fail together only 10% × 10% = 1% of the time, so the pair is up 99%.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Every outpost's traffic passes through one router with one power supply. What is that router?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A summary route</li>
            <li class="quiz-option quiz-correct">B. A single point of failure ✓</li>
            <li class="quiz-option">C. An ABR</li>
            <li class="quiz-option">D. A feasible successor</li>
        </ul>
        <p class="quiz-explanation"><em>If one device or link going down stops everything, it is a single point of failure.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why does a summary route make the network more stable?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. It hides changes inside a block, so a flapping subnet doesn't disturb routers outside it ✓</li>
            <li class="quiz-option">B. It makes links faster</li>
            <li class="quiz-option">C. It removes the need for OSPF</li>
            <li class="quiz-option">D. It encrypts routes</li>
        </ul>
        <p class="quiz-explanation"><em>Outside routers only see the summary, which stays up as long as any subnet in the block does.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> summarize Northwatch's routes on the Citadel ABR, find the Grid's single point of failure, and work out what a second radio link buys. <strong>Success:</strong> the core sees one Northwatch route and every answer is accepted.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g3-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm3', 'lesson' => 'lesson02']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
