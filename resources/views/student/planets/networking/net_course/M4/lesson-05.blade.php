{{-- M4 · Lesson 4.5: Troubleshooting Method — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m4-l5.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="05">

    <h1 class="lesson-heading">Lesson 4.5: Troubleshooting Method</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can follow the seven-step troubleshooting method, choose a search strategy, and read a traceroute to find where a path breaks.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Good troubleshooters follow a method instead of guessing. A widely used <strong>seven steps</strong>:
    </p>
    <ul class="body-list">
        <li><strong>1. Identify the problem.</strong> Gather information, question the user, note the symptoms, find out what changed, and try to reproduce it.</li>
        <li><strong>2. Establish a theory of probable cause.</strong> Question the obvious first.</li>
        <li><strong>3. Test the theory.</strong> If it is confirmed, go on. If not, make a new theory (or escalate).</li>
        <li><strong>4. Establish a plan of action</strong>, thinking about side effects.</li>
        <li><strong>5. Implement the solution</strong> (or escalate).</li>
        <li><strong>6. Verify full functionality</strong> and add preventive measures.</li>
        <li><strong>7. Document</strong> the findings, the actions and the outcome.</li>
    </ul>
    <p class="body-text">
        Three ways to search: <strong>bottom-up</strong> (start at the Physical layer and climb), <strong>top-down</strong> (start at the application and go down) and <strong>divide and conquer</strong> (test the middle of the path and see which half holds the fault).
    </p>
    <p class="body-text">
        Two rules save hours: <strong>change one thing at a time</strong>, or you will not know what fixed it, and remember that <strong>one fault can hide another</strong>, so re-test after every fix. Useful tools: <code>ping</code>, <code>traceroute</code>, <code>ip addr</code>, <code>ip route</code>, <code>ip neigh</code>, <code>dig</code> and <code>curl</code>.
    </p>
    <p class="body-text">
        <strong>Reading traceroute.</strong> Each line is one router on the path. Where the addresses stop moving forward, or the same router answers again and the trace ends early, that router is where the packets stop. Fix there, then trace again.
    </p>

    <h2 class="section-heading">Crew Briefing</h2>

    <p class="body-text">
        Earth is silent, and three relay towers stand between the ship and home. <strong>Astro</strong> refuses to guess. "Method, not luck. Trace the path, find the first break, fix it, and trace again."
    </p>
    <p class="body-text">
        <strong>Rivet</strong> runs the trace. It gets to the first tower, then the second, and then goes cold. <strong>Volt</strong> warns him. "Fix that one and test. There may be a second fault hiding behind it."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 220" role="img" aria-label="The seven steps of the troubleshooting method">
            <rect x="8" y="14" width="146" height="62" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="81" y="40" text-anchor="middle" font-size="18" font-weight="800" fill="#2f6fe0">1</text>
            <text x="81" y="62" text-anchor="middle" font-size="11.5" font-weight="700" fill="#14306b">Identify the problem</text>
            <rect x="174" y="14" width="146" height="62" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="247" y="40" text-anchor="middle" font-size="18" font-weight="800" fill="#2f6fe0">2</text>
            <text x="247" y="62" text-anchor="middle" font-size="11.5" font-weight="700" fill="#14306b">Theory of probable cause</text>
            <rect x="340" y="14" width="146" height="62" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="413" y="40" text-anchor="middle" font-size="18" font-weight="800" fill="#2f6fe0">3</text>
            <text x="413" y="62" text-anchor="middle" font-size="11.5" font-weight="700" fill="#14306b">Test the theory</text>
            <rect x="506" y="14" width="146" height="62" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="579" y="40" text-anchor="middle" font-size="18" font-weight="800" fill="#f08c1a">4</text>
            <text x="579" y="62" text-anchor="middle" font-size="11.5" font-weight="700" fill="#14306b">Make a plan of action</text>
            <rect x="91" y="116" width="146" height="62" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="164" y="142" text-anchor="middle" font-size="18" font-weight="800" fill="#f08c1a">5</text>
            <text x="164" y="164" text-anchor="middle" font-size="11.5" font-weight="700" fill="#14306b">Implement the fix</text>
            <rect x="257" y="116" width="146" height="62" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="330" y="142" text-anchor="middle" font-size="18" font-weight="800" fill="#2fa56b">6</text>
            <text x="330" y="164" text-anchor="middle" font-size="11.5" font-weight="700" fill="#14306b">Verify everything works</text>
            <rect x="423" y="116" width="146" height="62" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="496" y="142" text-anchor="middle" font-size="18" font-weight="800" fill="#2fa56b">7</text>
            <text x="496" y="164" text-anchor="middle" font-size="11.5" font-weight="700" fill="#14306b">Document it</text>
            <line x1="154" y1="45" x2="168" y2="45" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="174,45 163,39 163,51" fill="#7c5cff"/>
            <line x1="320" y1="45" x2="334" y2="45" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="340,45 329,39 329,51" fill="#7c5cff"/>
            <line x1="486" y1="45" x2="500" y2="45" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="506,45 495,39 495,51" fill="#7c5cff"/>
            <line x1="579" y1="76" x2="579" y2="96" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="579" y1="96" x2="164" y2="96" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="164" y1="96" x2="164" y2="116" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <line x1="237" y1="147" x2="251" y2="147" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="257,147 246,141 246,153" fill="#7c5cff"/>
            <line x1="403" y1="147" x2="417" y2="147" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="423,147 412,141 412,153" fill="#7c5cff"/>
            <text x="330" y="208" text-anchor="middle" font-size="12.5" font-weight="600" fill="#4a35b8">If the test fails, go back to step 2 with a new theory</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is the first step of the troubleshooting method?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Implement the solution</li>
            <li class="quiz-option quiz-correct">B. Identify the problem ✓</li>
            <li class="quiz-option">C. Document the outcome</li>
            <li class="quiz-option">D. Replace the router</li>
        </ul>
        <p class="quiz-explanation"><em>Start by understanding what is actually wrong: gather information, ask questions and note the symptoms.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. You change three settings at once and the problem disappears. What went wrong with your method?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing, that is the best approach</li>
            <li class="quiz-option quiz-correct">B. You changed more than one thing, so you do not know which change fixed it ✓</li>
            <li class="quiz-option">C. You should have rebooted first</li>
            <li class="quiz-option">D. You skipped the cable</li>
        </ul>
        <p class="quiz-explanation"><em>Change one thing at a time. Otherwise you cannot tell which change was the fix, and you may leave unneeded changes behind.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which strategy starts at the Physical layer and works up?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Top-down</li>
            <li class="quiz-option quiz-correct">B. Bottom-up ✓</li>
            <li class="quiz-option">C. Divide and conquer</li>
            <li class="quiz-option">D. Random guessing</li>
        </ul>
        <p class="quiz-explanation"><em>Bottom-up begins with cables and links (Layer 1) and climbs the layers.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> find every break between astro and Earth using a trace. <strong>Success:</strong> astro reaches <code>earth-cloud</code> and opens its page. Watch what happens after the first fix.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m4-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm4', 'lesson' => 'lesson05']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. You use Linux commands there; the ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
