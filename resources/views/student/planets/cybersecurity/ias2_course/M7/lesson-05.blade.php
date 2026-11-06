{{-- M7 · Lesson 7.5: Compliance & Auditing the Recovery Plan — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a7-l5.json) completes the lesson.
Syllabus: ITP1333 Unit G.5 Compliance with Regulatory Standards and Auditing DR Plans. --}}

<div class="lesson-fragment" data-module="m7" data-lesson="05">

    <h1 class="lesson-heading">Lesson 7.5: Compliance & Auditing the Recovery Plan</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> audit a disaster recovery plan against a regulatory-style requirement, and explain why an untested plan cannot be considered compliant.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A disaster recovery plan usually has to meet outside requirements too — regulatory standards, contracts
        with allies, or Codexia law — that specify things like how often the plan must be tested and how long
        records of those tests must be kept. <strong>Auditing</strong> means checking the plan and its evidence
        against those requirements, not just trusting that the plan exists.
    </p>

    <p class="body-text">
        A plan that has never actually been tested cannot be called compliant, no matter how well it's written —
        most standards require documented proof of a real test within a set period, and "we have a plan" is not
        the same claim as "we tested this plan and it worked."
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">When can a DR plan be called compliant?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. When it is written in great detail</li>
            <li class="quiz-option">B. When it is the longest plan on file in the base</li>
            <li class="quiz-option quiz-correct">C. When it has been tested and documented ✓</li>
            <li class="quiz-option">D. When the crew has heard of it once</li>
        </ul>
        <p class="quiz-explanation"><em>Most standards require documented proof of a real test. “We have a plan” is not enough.</em></p>
    </div>

    <h2 class="section-heading">Volt's Drill</h2>

    <p class="body-text">
        Volt audits four Citadel systems against the standard: "DR plan tested within the last 12 months, with a
        written record." The shield generator's plan is well-written, detailed, thorough — and has never actually
        been run as a drill. "This fails the audit," Volt says. "Not because the plan is bad. Because 'we wrote
        it' isn't the same claim as 'we tested it,' and the standard asks for the second one."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why does the shield generator's plan fail the audit?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. It was never run as a drill ✓</li>
            <li class="quiz-option">B. It was written very badly by Rivet</li>
            <li class="quiz-option">C. It was tested last month</li>
            <li class="quiz-option">D. It had too many details</li>
        </ul>
        <p class="quiz-explanation"><em>The plan is well written, but “we wrote it” isn't “we tested it”, and the standard asks for the second.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A written plan is not compliant until it has been tested and documented">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#8a4b00">Plan Written</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#8a4b00">looks complete on paper</text>
            <line x1="170" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Plan Tested + Documented</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">what the standard actually requires</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">"We have a plan" and "we tested this plan" are different claims</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what does the standard actually require?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Plan written down on paper</li>
            <li class="quiz-option quiz-correct">B. Plan tested + documented ✓</li>
            <li class="quiz-option">C. Plan kept on a shelf</li>
            <li class="quiz-option">D. Plan that looks good</li>
        </ul>
        <p class="quiz-explanation"><em>The standard requires a plan that was tested and documented, not just written.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does "auditing" a disaster recovery plan mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Writing the plan for the first time</li>
            <li class="quiz-option quiz-correct">B. Checking the plan and its evidence against outside requirements, not just trusting it exists ✓</li>
            <li class="quiz-option">C. Deleting the plan and starting over</li>
            <li class="quiz-option">D. Auditing only applies to financial records, never DR plans</li>
        </ul>
        <p class="quiz-explanation"><em>An audit verifies compliance with actual requirements using real evidence, rather than assuming a plan is adequate.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why did the shield generator's DR plan fail Volt's audit?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It was poorly written</li>
            <li class="quiz-option quiz-correct">B. It had never actually been tested, even though it was well-written ✓</li>
            <li class="quiz-option">C. It was missing entirely</li>
            <li class="quiz-option">D. It was too short</li>
        </ul>
        <p class="quiz-explanation"><em>A thorough, well-written plan still fails an audit that requires documented proof of an actual test.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What kind of requirements might a DR plan need to satisfy, beyond the organization's own preferences?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. None — DR plans are entirely internal with no outside requirements</li>
            <li class="quiz-option quiz-correct">B. Regulatory standards, contracts with partners, or applicable law ✓</li>
            <li class="quiz-option">C. Only the personal opinion of the person who wrote it</li>
            <li class="quiz-option">D. Requirements set by rival organizations</li>
        </ul>
        <p class="quiz-explanation"><em>DR plans often have to satisfy external standards, contractual obligations, or legal requirements, not just internal preference.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why can't "we have a plan" be treated as the same claim as "we tested this plan and it worked"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They are exactly the same claim</li>
            <li class="quiz-option quiz-correct">B. A plan can look complete on paper while still failing in practice — only a real test proves it works ✓</li>
            <li class="quiz-option">C. Testing a plan is unnecessary once it's written</li>
            <li class="quiz-option">D. Written plans are always more reliable than tested ones</li>
        </ul>
        <p class="quiz-explanation"><em>Only an actual test surfaces problems that a plan's text alone can't reveal — the two claims are genuinely different.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does most compliance auditing require as evidence, beyond the plan's existence?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing further is ever required</li>
            <li class="quiz-option quiz-correct">B. Documented proof of a real test within a set period ✓</li>
            <li class="quiz-option">C. A verbal assurance from whoever wrote the plan</li>
            <li class="quiz-option">D. The plan being the longest document in the archive</li>
        </ul>
        <p class="quiz-explanation"><em>Standards typically require a documented, dated test record — not just the plan document itself.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four Citadel DR plans need auditing against the 12-month test requirement.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a7-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm7', 'lesson' => 'lesson05']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the audit standard and each plan's test record, then
            classify each as compliant or non-compliant.
        </p>
    </div>

</div>
