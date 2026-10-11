{{-- M5 · Lesson 5.3: Web Application Security: the OWASP Top 10 — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a5-l3.json) completes the lesson.
Syllabus: ITP1333 Unit E.3 Web Application Security (OWASP Top 10). --}}

<div class="lesson-fragment" data-module="m5" data-lesson="03">

    <h1 class="lesson-heading">Lesson 5.3: Web Application Security: the OWASP Top 10</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> recognize broken access control and hardcoded credentials as common OWASP Top 10 issues, and fix a hardcoded credential found in a configuration file.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        The <strong>OWASP Top 10</strong> is a widely used list of the most common, most damaging web application
        weaknesses. Two show up constantly in real breaches: <strong>broken access control</strong> (a user reaches
        data or actions their role should never allow) and <strong>security misconfiguration</strong> — including
        the classic mistake of a <strong>hardcoded credential</strong> left sitting in a configuration file, where
        anyone who can read the file can read the password.
    </p>

    <p class="body-text">
        A hardcoded credential is dangerous specifically because it's invisible until someone looks: the
        application works fine, right up until the file (or its backup, or its source history) reaches someone
        who shouldn't have it.
    </p>

    <h2 class="section-heading">Volt's Drill</h2>

    <p class="body-text">
        Volt reviews the supply tool's configuration file and finds the database password typed in plain text,
        right next to the connection settings. "This isn't a clever attack Vex has to work for," Volt tells Rivet.
        "It's a hardcoded credential — a classic OWASP finding. Anyone who reads this file has the password. Pull
        it out, put it somewhere only the application itself can reach, and rotate it — assume it's already been
        seen."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A hardcoded credential in a config file is exposed to anyone who reads it, until it is rotated and secured">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">Config File</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">password typed in plain text</text>
            <line x1="170" y1="66" x2="237" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="245,66 233,60 233,72" fill="#7c5cff"/>
            <rect x="245" y="30" width="150" height="72" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#8a4b00">Anyone Who Reads It</text>
            <text x="320" y="82" text-anchor="middle" font-size="12.5" fill="#8a4b00">now has the password</text>
            <line x1="395" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Rotated & Secured</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">removed, password changed</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">A hardcoded credential is invisible until someone looks — then it's already too late</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is the OWASP Top 10?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A list of the ten best security tools to buy</li>
            <li class="quiz-option quiz-correct">B. A widely used list of the most common, most damaging web application weaknesses ✓</li>
            <li class="quiz-option">C. A certification exam for developers</li>
            <li class="quiz-option">D. A firewall configuration standard</li>
        </ul>
        <p class="quiz-explanation"><em>The OWASP Top 10 catalogs the web application security risks that show up most often and cause the most damage.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is "broken access control"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A firewall that isn't configured</li>
            <li class="quiz-option quiz-correct">B. A user reaching data or actions their role should never allow ✓</li>
            <li class="quiz-option">C. A password that's too short</li>
            <li class="quiz-option">D. A server that's physically unlocked</li>
        </ul>
        <p class="quiz-explanation"><em>Broken access control means the application fails to properly restrict what a user can see or do based on their role.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why is a hardcoded credential in a configuration file a problem?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Configuration files are always encrypted automatically</li>
            <li class="quiz-option quiz-correct">B. Anyone who can read the file can read the password in plain text ✓</li>
            <li class="quiz-option">C. It makes the application run slower</li>
            <li class="quiz-option">D. It's only a problem if the file is deleted</li>
        </ul>
        <p class="quiz-explanation"><em>A password typed directly into a readable file is exposed to anyone with access to that file, its backups, or its history.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What made Volt treat the hardcoded password as already compromised, rather than just "found in time"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Volt always assumes the worst regardless of evidence</li>
            <li class="quiz-option quiz-correct">B. A hardcoded credential is invisible until someone looks, so it may have already been seen before being found ✓</li>
            <li class="quiz-option">C. The password had definitely already been used by an attacker</li>
            <li class="quiz-option">D. It only matters if a breach has already been confirmed</li>
        </ul>
        <p class="quiz-explanation"><em>Since there's no way to know how long the credential sat exposed or who may have already seen it, the safe assumption is to rotate it.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What should be done once a hardcoded credential is found?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Leave it, since finding it means the danger has passed</li>
            <li class="quiz-option quiz-correct">B. Remove it from the file, secure it properly, and rotate the credential itself ✓</li>
            <li class="quiz-option">C. Only document it for a future review</li>
            <li class="quiz-option">D. Delete the configuration file entirely</li>
        </ul>
        <p class="quiz-explanation"><em>Removing the exposed credential from the file and changing its value closes the gap and assumes prior exposure.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> A hardcoded database password was found in the supply tool's configuration. Rotate it.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a5-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm5', 'lesson' => 'lesson03']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the exposed configuration file, then set a new,
            strong password for the account.
        </p>
    </div>

</div>
