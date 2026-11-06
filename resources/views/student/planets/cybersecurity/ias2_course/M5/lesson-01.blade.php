{{-- M5 · Lesson 5.1: Secure SDLC Integration — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a5-l1.json) completes the lesson.
Syllabus: ITP1333 Unit E.1 Secure Development Life Cycle (SDLC) Integration. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="01">

    <h1 class="lesson-heading">Lesson 5.1: Secure SDLC Integration</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> place a security activity into the correct phase of the software development life cycle, and explain why security bolted on at the end costs more than security built in from the start.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>secure SDLC</strong> means security tasks are built into every phase of building software, not
        added at the very end. A simplified version has five phases: <strong>requirements</strong> (decide what
        security properties the software must have), <strong>design</strong> (model threats before writing code),
        <strong>build</strong> (review code for known mistakes as it's written), <strong>test</strong> (actively try
        to break it before release), and <strong>maintain</strong> (patch it after release, for as long as it runs).
    </p>

    <p class="body-text">
        A flaw caught in requirements or design costs little to fix — it's still just a decision on paper. The same
        flaw found after release can mean rewriting shipped code, notifying users, and repairing the damage a real
        attacker already caused. Earlier is always cheaper.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Why fix security flaws early in the SDLC?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Earlier is always cheaper ✓</li>
            <li class="quiz-option">B. Flaws vanish after release</li>
            <li class="quiz-option">C. Testing finds nothing later</li>
            <li class="quiz-option">D. Users like rewrites better</li>
        </ul>
        <p class="quiz-explanation"><em>A flaw in design is just a decision on paper. After release it can mean rewrites, notices and real damage.</em></p>
    </div>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        Rivet built the crew's new supply-tracking tool fast, then asked Volt to "check it for security stuff" the
        day before launch. Volt found three problems that would have taken minutes to avoid in design, but now
        meant reworking finished code under a deadline. "Next time," Volt says, "loop me in at requirements, not
        the night before launch. A five-minute conversation now saves a five-hour rewrite later."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">When does Volt want to be looped in next time?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The night before launch</li>
            <li class="quiz-option quiz-correct">B. At requirements ✓</li>
            <li class="quiz-option">C. After the first breach</li>
            <li class="quiz-option">D. Only during testing</li>
        </ul>
        <p class="quiz-explanation"><em>A five-minute talk at requirements saves a five-hour rewrite later.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A secure SDLC integrates security into requirements, design, build, and test phases">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">Requirements</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">decide security needs</text>
            <line x1="170" y1="66" x2="162" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="170,66 158,60 158,72" fill="#7c5cff"/>
            <rect x="170" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="245" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Design</text>
            <text x="245" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">model threats</text>
            <line x1="320" y1="66" x2="312" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="320,66 308,60 308,72" fill="#7c5cff"/>
            <rect x="320" y="30" width="150" height="72" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="395" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#8a4b00">Build</text>
            <text x="395" y="82" text-anchor="middle" font-size="12.5" fill="#8a4b00">review code</text>
            <line x1="470" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Test</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">try to break it</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Security built into every phase — cheaper the earlier it's caught</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what happens in the design phase?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Review code</li>
            <li class="quiz-option">B. Try to break it</li>
            <li class="quiz-option quiz-correct">C. Model threats ✓</li>
            <li class="quiz-option">D. Patch after release</li>
        </ul>
        <p class="quiz-explanation"><em>Requirements decide security needs, design models threats, build reviews code, test tries to break it.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does a "secure SDLC" mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Adding a security check only right before release</li>
            <li class="quiz-option quiz-correct">B. Building security tasks into every phase of software development, from requirements onward ✓</li>
            <li class="quiz-option">C. Hiring only security specialists to write code</li>
            <li class="quiz-option">D. Skipping the testing phase entirely</li>
        </ul>
        <p class="quiz-explanation"><em>A secure SDLC integrates security throughout the whole process, not as a single last-minute step.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What happens in the "design" phase of a secure SDLC?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The software is released to users</li>
            <li class="quiz-option quiz-correct">B. Threats are modeled before any code is written ✓</li>
            <li class="quiz-option">C. Code is reviewed line by line</li>
            <li class="quiz-option">D. The software is actively attacked to find flaws</li>
        </ul>
        <p class="quiz-explanation"><em>Design is where potential threats are identified and planned for, before implementation begins.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why does a flaw found in requirements or design cost less to fix than one found after release?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because early flaws are always smaller</li>
            <li class="quiz-option quiz-correct">B. Because it's still just a decision on paper, not code that's already been shipped and used ✓</li>
            <li class="quiz-option">C. Because requirements documents are free to edit</li>
            <li class="quiz-option">D. Costs are actually the same regardless of when a flaw is found</li>
        </ul>
        <p class="quiz-explanation"><em>Fixing a decision before it becomes code is far cheaper than reworking shipped software and repairing damage already done.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What went wrong with Rivet's approach to the supply-tracking tool?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Rivet built it too slowly</li>
            <li class="quiz-option quiz-correct">B. Security was only checked the night before launch, instead of throughout the process ✓</li>
            <li class="quiz-option">C. Volt refused to review the code at all</li>
            <li class="quiz-option">D. The tool had no requirements phase at all</li>
        </ul>
        <p class="quiz-explanation"><em>Waiting until the last moment to involve security meant fixes were far more expensive under deadline pressure.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is the "maintain" phase of the SDLC responsible for?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Writing the original requirements</li>
            <li class="quiz-option quiz-correct">B. Patching the software for as long as it continues to run after release ✓</li>
            <li class="quiz-option">C. Only the initial code review</li>
            <li class="quiz-option">D. Deciding whether to build the software at all</li>
        </ul>
        <p class="quiz-explanation"><em>Maintain covers the software's entire lifetime after release — ongoing patching as new issues are discovered.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four security activities from the supply-tracking project need to be filed under the correct SDLC phase.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a5-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm5', 'lesson' => 'lesson01']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the activity list, then classify each one by its SDLC phase.
        </p>
    </div>

</div>
