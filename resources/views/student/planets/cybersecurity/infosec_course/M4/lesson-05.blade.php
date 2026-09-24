{{-- M4 · Lesson 4.5: Copyright Infringement — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c4-l5.json) completes the lesson.
Syllabus: ITP1232 Unit D.5 Copyright Infringement. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="05">

    <h1 class="lesson-heading">Lesson 4.5: Copyright Infringement</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what copyright infringement is, tell the difference between
        fair, credited use and infringement, and judge realistic cases using that distinction.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Copyright</strong> gives a creator control over how their original work is copied, shared, or
        sold. <strong>Copyright infringement</strong> is using someone else's work in a way that ignores that
        control — usually copying it wholesale, without credit or permission, especially when it's sold or
        passed off as your own.
    </p>

    <p class="body-text">
        Not every use of someone else's work is infringement. Small, credited excerpts used for learning,
        commentary, or teaching are usually treated as fair use. The line moves when a use becomes: the whole
        work, not an excerpt; no credit given; sold or distributed for profit; or material explicitly marked as
        restricted, leaked and reused anyway.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        12:00. A request comes in from Codexia's High Council: four disputes have landed on their desk, each
        one accusing someone of copying crew work. <strong>Astro</strong> reads them over. "Vex would love for
        us to get this wrong — punish honest reuse, or excuse real theft. Neither one holds up."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> lays out the rule the crew will use: "Small, credited, non-commercial use? Usually
        fine. A whole copy, sold or resold, with no credit? Never fine. Especially if it was marked 'crew use
        only' and someone leaked it anyway."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="A spectrum from fair, credited use on the left to clear copyright infringement on the right, based on how much was copied, whether credit was given, and whether it was sold">
            <rect x="20" y="30" width="280" height="130" rx="14" fill="#e6f6ec" stroke="#3aa876" stroke-width="2"/>
            <text x="160" y="55" text-anchor="middle" font-size="15" font-weight="700" fill="#1f6e4c">Usually allowed</text>
            <text x="160" y="80" text-anchor="middle" font-size="12.5" fill="#1f6e4c">small excerpt</text>
            <text x="160" y="100" text-anchor="middle" font-size="12.5" fill="#1f6e4c">credit given</text>
            <text x="160" y="120" text-anchor="middle" font-size="12.5" fill="#1f6e4c">non-commercial / learning</text>

            <rect x="340" y="30" width="280" height="130" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="480" y="55" text-anchor="middle" font-size="15" font-weight="700" fill="#b3261e">Not allowed</text>
            <text x="480" y="80" text-anchor="middle" font-size="12.5" fill="#7a1a14">whole work copied</text>
            <text x="480" y="100" text-anchor="middle" font-size="12.5" fill="#7a1a14">no credit, no permission</text>
            <text x="480" y="120" text-anchor="middle" font-size="12.5" fill="#7a1a14">sold, or restricted material leaked</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does copyright give a creator control over?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Who is allowed to read the news</li>
            <li class="quiz-option quiz-correct">B. How their original work is copied, shared, or sold ✓</li>
            <li class="quiz-option">C. Which passwords other people can use</li>
            <li class="quiz-option">D. How fast a network connection runs</li>
        </ul>
        <p class="quiz-explanation"><em>Copyright protects a creator's control over the use and distribution of their own original work.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which of these is most likely to count as fair, non-infringing use?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Quoting two credited lines in a class report ✓</li>
            <li class="quiz-option">B. Selling someone else's entire work as your own</li>
            <li class="quiz-option">C. Leaking material marked "crew use only" for profit</li>
            <li class="quiz-option">D. Copying a whole document with no credit at all</li>
        </ul>
        <p class="quiz-explanation"><em>A small, credited excerpt used for learning is the classic example of fair use.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. A trader copies someone's entire blueprint set and sells it as their own, with no credit or permission. Is that infringement?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. No, selling something makes it legal</li>
            <li class="quiz-option quiz-correct">B. Yes: a whole work, resold, with no credit or permission ✓</li>
            <li class="quiz-option">C. No, copyright doesn't apply to blueprints</li>
            <li class="quiz-option">D. Only if the buyer complains</li>
        </ul>
        <p class="quiz-explanation"><em>Copying an entire work and selling it without credit or permission is a textbook case of copyright infringement.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. A manual marked "crew use only" is leaked and reprinted by a rival crew for profit. Is that allowed?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Yes, once something leaks it becomes public</li>
            <li class="quiz-option quiz-correct">B. No: restricted material, leaked and resold for profit, is infringement ✓</li>
            <li class="quiz-option">C. Yes, as long as it's reprinted neatly</li>
            <li class="quiz-option">D. It depends only on how popular the manual is</li>
        </ul>
        <p class="quiz-explanation"><em>Marking something restricted doesn't disappear just because it leaked — reselling leaked restricted material is still infringement.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. A student remixes a public anthem for a school project, doesn't sell it, and credits the original. Is that infringement?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Yes, any remix is automatically infringement</li>
            <li class="quiz-option quiz-correct">B. No: non-commercial, credited, transformative use like this is generally allowed ✓</li>
            <li class="quiz-option">C. Yes, because music can never be remixed</li>
            <li class="quiz-option">D. Only a court can ever decide, there's no general rule</li>
        </ul>
        <p class="quiz-explanation"><em>Non-commercial, credited, educational remixing sits firmly on the fair-use side of the line.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> read Codexia's High Council's four cases and judge each one: allowed, or not
        allowed. <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c4-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm4', 'lesson' => 'lesson05']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>type</code> and <code>classify &lt;case&gt; &lt;allowed|not-allowed&gt;</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
