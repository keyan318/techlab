{{-- M6 · Lesson 6.6: Operational Accountability — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a6-l6.json) completes the lesson.
Syllabus: ITP1333 Unit F.6 Operational Accountability. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="06">

    <h1 class="lesson-heading">Lesson 6.6: Operational Accountability</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> eliminate a shared account so every sensitive action can be traced to one specific person, and explain why shared accounts break accountability even when nothing has gone wrong yet.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Operational accountability</strong> means every sensitive action can be traced back to exactly one
        person — not "someone on shield duty," but a specific, named individual. A <strong>shared account</strong>
        (one login used by multiple people) breaks this immediately: if three cadets share a login and something
        goes wrong, there is no way to know which of them did it, or whether it was any of them at all.
    </p>

    <p class="body-text">
        This isn't about assuming anyone is dishonest. Even with a perfectly trustworthy crew, a shared account
        makes it impossible to reconstruct what happened after the fact — and accountability is exactly the thing
        you need most when something eventually does go wrong.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does a shared account break?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The speed of the console itself</li>
            <li class="quiz-option">B. The strength of every password</li>
            <li class="quiz-option">C. The firewall at the network edge</li>
            <li class="quiz-option quiz-correct">D. Tracing an action to one person ✓</li>
        </ul>
        <p class="quiz-explanation"><em>If three people share a login, there's no way to know which of them did something.</em></p>
    </div>

    <h2 class="section-heading">Rivet's Workbench</h2>

    <p class="body-text">
        Rivet finds a shield-duty console still using the login "shift-shared," used by whichever cadet is on
        duty. Nothing bad has happened on it yet, but Rivet disables it anyway. "The moment something does go
        wrong on this console, we won't be able to tell who was at the controls. One account, one person, always
        — that's not paperwork, that's the only way an audit trail means anything."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why does Rivet disable “shift-shared” when nothing bad happened?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The login had been hacked the night before</li>
            <li class="quiz-option">B. The console was being moved to storage</li>
            <li class="quiz-option">C. Doom had asked for that account by name</li>
            <li class="quiz-option quiz-correct">D. When something does, nobody could tell who ✓</li>
        </ul>
        <p class="quiz-explanation"><em>One account, one person, always. That is the only way an audit trail means anything.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A shared account makes actions untraceable; one account per person keeps every action accountable">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">Shared Account</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">"someone" did it — untraceable</text>
            <line x1="170" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">One Account, One Person</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">every action has a name</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Accountability is exactly what you need when something eventually goes wrong</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what does one account per person give?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Faster logins</li>
            <li class="quiz-option quiz-correct">B. Every action a name ✓</li>
            <li class="quiz-option">C. Fewer passwords to remember</li>
            <li class="quiz-option">D. Shared blame</li>
        </ul>
        <p class="quiz-explanation"><em>With one account per person, every action has a name, which is exactly what you need when things go wrong.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does "operational accountability" mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Every sensitive action can be traced back to exactly one specific person ✓</li>
            <li class="quiz-option">B. Only management is ever held responsible for anything</li>
            <li class="quiz-option">C. Accounts should be shared to build crew trust</li>
            <li class="quiz-option">D. A synonym for encryption</li>
        </ul>
        <p class="quiz-explanation"><em>Accountability means a specific individual, not a vague group, can be identified behind any sensitive action.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why does a shared account break accountability?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Shared accounts are always technically broken</li>
            <li class="quiz-option quiz-correct">B. If something goes wrong, there's no way to know which of the people sharing it actually did it ✓</li>
            <li class="quiz-option">C. Shared accounts use more storage space</li>
            <li class="quiz-option">D. It doesn't break accountability at all</li>
        </ul>
        <p class="quiz-explanation"><em>With multiple people behind one login, an action can't be attributed to any specific individual afterward.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Rivet disable the shared console login even though nothing had gone wrong on it yet?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because Rivet dislikes that console specifically</li>
            <li class="quiz-option quiz-correct">B. Because the gap exists regardless of whether anything has gone wrong — it's unfixable after the fact ✓</li>
            <li class="quiz-option">C. Because it was using too many resources</li>
            <li class="quiz-option">D. Because policy requires disabling all consoles weekly</li>
        </ul>
        <p class="quiz-explanation"><em>Fixing accountability only after an incident is too late — the trail needs to exist before anything happens, not after.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Does operational accountability assume the crew is dishonest?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Yes, it exists specifically because people can't be trusted</li>
            <li class="quiz-option quiz-correct">B. No — even with a fully trustworthy crew, a shared account makes it impossible to reconstruct what happened ✓</li>
            <li class="quiz-option">C. It only applies to crew members under investigation</li>
            <li class="quiz-option">D. Accountability and trust are unrelated concepts</li>
        </ul>
        <p class="quiz-explanation"><em>Accountability isn't about assuming dishonesty — it's about preserving the ability to reconstruct events regardless of anyone's character.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What is Rivet's rule for maintaining accountability?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. One account, shared among trusted crew, is fine</li>
            <li class="quiz-option quiz-correct">B. One account, one person, always ✓</li>
            <li class="quiz-option">C. Accounts should be rotated between crew members weekly</li>
            <li class="quiz-option">D. Only command staff need individual accounts</li>
        </ul>
        <p class="quiz-explanation"><em>A strict one-to-one mapping between accounts and individuals is what keeps the audit trail meaningful.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> A shield-duty console still uses a shared login. Disable it so every action can be traced to one person.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a6-l6" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm6', 'lesson' => 'lesson06']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the console audit, then disable the shared account.
        </p>
    </div>

</div>
