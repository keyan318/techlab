{{-- M6 · Lesson 6.5: Management Practices — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a6-l5.json) completes the lesson.
Syllabus: ITP1333 Unit F.5 Management Practices. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="05">

    <h1 class="lesson-heading">Lesson 6.5: Management Practices</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> identify leadership behaviors that support a security program in practice, and explain why visible leadership support matters more than a written endorsement alone.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A security program written on paper doesn't run itself — it depends on leadership actually behaving in
        ways that support it. Three practices matter most: <strong>visible support</strong> (leaders follow the
        same rules they ask everyone else to follow), <strong>resourcing</strong> (giving the program the time,
        budget and staff it needs, not just an approving nod), and <strong>leading by example</strong> (a leader
        who skips a rule teaches everyone else it's optional).
    </p>

    <p class="body-text">
        A policy signed by leadership but never resourced, and quietly bypassed by that same leadership when
        inconvenient, sends a louder message than the policy document itself — and the message is "this doesn't
        really matter."
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does a leader who skips a rule teach everyone?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. That the rule is now stronger</li>
            <li class="quiz-option quiz-correct">B. That the rule is optional ✓</li>
            <li class="quiz-option">C. That the rule needs more staff</li>
            <li class="quiz-option">D. That the rule will be reviewed</li>
        </ul>
        <p class="quiz-explanation"><em>Leading by example matters: a leader who skips a rule teaches everyone it's optional.</em></p>
    </div>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Astro is running late and a cadet offers to skip the sign-in log "just this once, for the Captain." Astro
        refuses. "If I skip it, I've just told every cadet watching that the log is optional when you're important
        enough. The fastest way to kill this program isn't a hacker — it's me, deciding the rules don't apply to
        me today."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why does Astro refuse to skip the sign-in log?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The log was already broken that morning</li>
            <li class="quiz-option">B. The cadet was not allowed to offer it</li>
            <li class="quiz-option quiz-correct">C. It would show rules don't apply to the important ✓</li>
            <li class="quiz-option">D. Astro was not really running late at all that day, in fact</li>
        </ul>
        <p class="quiz-explanation"><em>The fastest way to kill the program isn't a hacker, it's a leader deciding the rules don't apply to them.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="Management practices that support security: visible support, resourcing, and leading by example">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Visible Support</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">leaders follow the same rules</text>
            <line x1="170" y1="66" x2="237" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="245,66 233,60 233,72" fill="#7c5cff"/>
            <rect x="245" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">Resourcing</text>
            <text x="320" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">real time, budget, staff</text>
            <line x1="395" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Leading by Example</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">no quiet exceptions</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">A program leadership doesn't visibly follow is a program nobody else follows either</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what does resourcing mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Real time, budget, staff ✓</li>
            <li class="quiz-option">B. An approving nod only</li>
            <li class="quiz-option">C. Leaders quietly skip the rules</li>
            <li class="quiz-option">D. A policy with no owner</li>
        </ul>
        <p class="quiz-explanation"><em>Resourcing means giving the program real time, budget and staff, not just a nod.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does "visible support" from leadership mean, for a security program?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Leadership signs the policy document and takes no further action</li>
            <li class="quiz-option quiz-correct">B. Leaders follow the same rules they ask everyone else to follow ✓</li>
            <li class="quiz-option">C. Leadership only mentions security once a year</li>
            <li class="quiz-option">D. Leaders are exempt from the rules due to their rank</li>
        </ul>
        <p class="quiz-explanation"><em>Visible support means leadership's own behavior models the rules, not just an endorsement on paper.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does "resourcing" a security program require?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. An approving nod with no follow-through</li>
            <li class="quiz-option quiz-correct">B. Actually giving the program the time, budget, and staff it needs ✓</li>
            <li class="quiz-option">C. Only a mention in the company newsletter</li>
            <li class="quiz-option">D. Resourcing isn't necessary if the policy is well written</li>
        </ul>
        <p class="quiz-explanation"><em>A program needs real resources to function — approval alone doesn't provide the capacity to carry it out.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Astro refuse to skip the sign-in log, even briefly?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because it was against Codexia law</li>
            <li class="quiz-option quiz-correct">B. Because skipping it would teach every watching cadet that the rule is optional for important people ✓</li>
            <li class="quiz-option">C. Because Astro personally enjoys signing logs</li>
            <li class="quiz-option">D. Because there was no other reason to sign in that day</li>
        </ul>
        <p class="quiz-explanation"><em>A leader's exception to a rule sends a message that the rule doesn't really apply universally.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What message does a policy send if it's signed by leadership but never resourced or followed by that same leadership?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. That the policy is extremely important and well-supported</li>
            <li class="quiz-option quiz-correct">B. That the policy doesn't really matter, regardless of what the document says ✓</li>
            <li class="quiz-option">C. That the policy only applies to junior staff</li>
            <li class="quiz-option">D. No message is sent either way</li>
        </ul>
        <p class="quiz-explanation"><em>Actions speak louder than the document — an unresourced, unfollowed policy signals it isn't a real priority.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. According to Astro, what is the fastest way to undermine a security program?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A well-funded, persistent external attacker</li>
            <li class="quiz-option quiz-correct">B. Leadership quietly deciding the rules don't apply to them ✓</li>
            <li class="quiz-option">C. A single cadet's honest mistake</li>
            <li class="quiz-option">D. Reviewing the policy too frequently</li>
        </ul>
        <p class="quiz-explanation"><em>Leadership's own quiet exceptions can do more damage to a program's credibility than most external threats.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four leadership actions from this month need review. Classify each as supports-program or undermines-program.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a6-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm6', 'lesson' => 'lesson05']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the leadership actions log, then classify each one.
        </p>
    </div>

</div>
