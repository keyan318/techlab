{{-- M6 · Lesson 6.2: Change Management — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a6-l2.json) completes the lesson.
Syllabus: ITP1333 Unit F.2 Change Management. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="02">

    <h1 class="lesson-heading">Lesson 6.2: Change Management</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> follow the correct order of a change management process — request, approve, implement, verify — and explain why an unapproved change is a risk even if it's technically correct.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Change management</strong> is the formal process a system change goes through before it happens:
        <strong>request</strong> (someone proposes the change and why), <strong>approve</strong> (someone with
        authority reviews and signs off, weighing risk against benefit), <strong>implement</strong> (the change is
        made), and <strong>verify</strong> (confirm it worked as intended and nothing else broke).
    </p>

    <p class="body-text">
        Skipping approval — even for a change that turns out fine — is still a risk, because nobody with a broader
        view got the chance to catch a conflict the implementer couldn't see. An unapproved change that happens to
        work is luck, not process.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Rivet reconfigures the shield relay overnight without waiting for sign-off, reasoning "it was obviously the
        right fix." It worked — this time. Astro is not satisfied. "You got lucky. If Volt had known you were
        touching the relay, they'd have told you it conflicts with a scheduled diagnostic tomorrow. Being right
        this time doesn't make skipping approval safe next time."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="Change management follows request, approve, implement, and verify in order">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">Request</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">propose it, and why</text>
            <line x1="170" y1="66" x2="162" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="170,66 158,60 158,72" fill="#7c5cff"/>
            <rect x="170" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="245" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Approve</text>
            <text x="245" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">weighed against other work</text>
            <line x1="320" y1="66" x2="312" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="320,66 308,60 308,72" fill="#7c5cff"/>
            <rect x="320" y="30" width="150" height="72" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="395" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#8a4b00">Implement</text>
            <text x="395" y="82" text-anchor="middle" font-size="12.5" fill="#8a4b00">the change is made</text>
            <line x1="470" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Verify</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">confirm it worked</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Skipping approval is a risk even when the change happens to work</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What are the four steps of a formal change management process?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Implement, verify, request, approve, in any order</li>
            <li class="quiz-option quiz-correct">B. Request, approve, implement, verify, in that order ✓</li>
            <li class="quiz-option">C. Only implement and verify — approval isn't necessary</li>
            <li class="quiz-option">D. Approve, implement, request, verify</li>
        </ul>
        <p class="quiz-explanation"><em>A change is proposed, then reviewed and approved, then made, then confirmed to have worked as intended.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why is skipping the approval step a risk, even if the change turns out fine?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It isn't a risk if the change works</li>
            <li class="quiz-option quiz-correct">B. Nobody with a broader view got the chance to catch a conflict the implementer couldn't see ✓</li>
            <li class="quiz-option">C. Approval is only a formality with no real purpose</li>
            <li class="quiz-option">D. Approval always takes too long to be worthwhile</li>
        </ul>
        <p class="quiz-explanation"><em>Approval exists to catch conflicts or risks the person making the change might not be aware of — skipping it removes that safeguard.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What conflict did Rivet's unapproved change risk, according to Astro?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It risked nothing, since the fix was correct</li>
            <li class="quiz-option quiz-correct">B. It could have conflicted with a scheduled diagnostic Volt knew about but Rivet didn't ✓</li>
            <li class="quiz-option">C. It risked violating a law</li>
            <li class="quiz-option">D. It risked using too much power</li>
        </ul>
        <p class="quiz-explanation"><em>Approval would have surfaced information (the scheduled diagnostic) that the person making the change didn't have.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does the "verify" step confirm?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. That the change was approved before it happened</li>
            <li class="quiz-option quiz-correct">B. That the change worked as intended and nothing else broke ✓</li>
            <li class="quiz-option">C. That the change request was written clearly</li>
            <li class="quiz-option">D. That the implementer followed instructions exactly</li>
        </ul>
        <p class="quiz-explanation"><em>Verification checks the outcome — did the change achieve its goal without unintended side effects.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why does Astro say Rivet "got lucky" rather than did the right thing?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because the fix itself was wrong</li>
            <li class="quiz-option quiz-correct">B. Because an unapproved change that happens to work is luck, not a safe process — it could easily have gone differently ✓</li>
            <li class="quiz-option">C. Because Astro dislikes fast fixes on principle</li>
            <li class="quiz-option">D. Because Rivet used the wrong tool</li>
        </ul>
        <p class="quiz-explanation"><em>The outcome being fine doesn't retroactively make skipping the process safe — the next unapproved change might not be so fortunate.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> A change to the comms relay needs to go through the process properly this time: request, then approval, then implementation.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a6-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm6', 'lesson' => 'lesson02']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Submit the request, get it approved, then implement the
            change — in the correct order.
        </p>
    </div>

</div>
