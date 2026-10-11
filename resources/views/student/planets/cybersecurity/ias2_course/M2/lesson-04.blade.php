{{-- M2 · Lesson 2.4: Best Practices for Network Defense — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a2-l4.json) completes the lesson.
Syllabus: ITP1333 Unit B.4 Best Practices for Network Defense. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="04">

    <h1 class="lesson-heading">Lesson 2.4: Best Practices for Network Defense</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> apply four core network defense practices: least privilege, closing unused services, patching, and monitoring logs.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Four habits catch most network problems before they become breaches:
    </p>

    <ul class="body-list">
        <li><strong>Least privilege</strong>: give each account only the access it needs, nothing more.</li>
        <li><strong>Close unused services and ports</strong>: anything listening that nobody uses is a door left
        open for no reason.</li>
        <li><strong>Patch known issues</strong>: fix documented weaknesses before someone else finds and uses them.</li>
        <li><strong>Monitor the logs</strong>: an attack usually leaves a trace before it succeeds — someone has to
        be watching for it.</li>
    </ul>

    <p class="body-text">
        None of these four is exotic. They are unglamorous, repeatable habits — which is exactly why they work:
        most real breaches exploit a forgotten port, an over-privileged account, an unpatched flaw, or a warning
        sign nobody checked.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Astro reviews the relay console with Rivet. "This diagnostic account has admin rights it hasn't used in a
        year — least privilege says shrink it. Port 6060 is still listening from a test three months ago — close
        it. The patch log shows a fix from last week that nobody applied — apply it. And the access log has three
        failed logins at 2 AM, unreviewed — someone should have looked at that already."
    </p>

    <p class="body-text">
        "None of this is fancy," Astro adds. "It's just four habits, done every week, instead of once and forgotten."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="Four network defense best practices: least privilege, closing unused ports, patching, and monitoring logs">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Least Privilege</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">only the access needed</text>
            <line x1="170" y1="66" x2="162" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="170,66 158,60 158,72" fill="#7c5cff"/>
            <rect x="170" y="30" width="150" height="72" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="245" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#8a4b00">Close Unused</text>
            <text x="245" y="82" text-anchor="middle" font-size="12.5" fill="#8a4b00">ports & services</text>
            <line x1="320" y1="66" x2="312" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="320,66 308,60 308,72" fill="#7c5cff"/>
            <rect x="320" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="395" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">Patch</text>
            <text x="395" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">known weaknesses</text>
            <line x1="470" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">Monitor Logs</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">watch for the trace</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Four unglamorous habits that catch most real breaches</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does "least privilege" mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Giving every account full admin rights, just in case</li>
            <li class="quiz-option quiz-correct">B. Giving each account only the access it actually needs, nothing more ✓</li>
            <li class="quiz-option">C. Removing all accounts from the system</li>
            <li class="quiz-option">D. A rule that only applies to guest accounts</li>
        </ul>
        <p class="quiz-explanation"><em>Least privilege limits each account to exactly what it needs — reducing the damage if that account is ever compromised.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why should unused ports and services be closed?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They slow the network down</li>
            <li class="quiz-option quiz-correct">B. Anything listening that nobody uses is a door left open for no reason, and attackers look for exactly those ✓</li>
            <li class="quiz-option">C. They cost extra credits to keep running</li>
            <li class="quiz-option">D. Closing ports is only cosmetic and has no security value</li>
        </ul>
        <p class="quiz-explanation"><em>An unused open port serves no legitimate purpose but still gives an attacker a way in — it should be closed.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why does patching matter?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It makes the software look newer</li>
            <li class="quiz-option quiz-correct">B. It fixes documented weaknesses before someone else finds and uses them ✓</li>
            <li class="quiz-option">C. Patches are only cosmetic updates</li>
            <li class="quiz-option">D. Patching is unnecessary if a firewall is in place</li>
        </ul>
        <p class="quiz-explanation"><em>A patch closes a known weakness — leaving it unpatched means the flaw is publicly documented and waiting to be used.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why does monitoring logs matter, according to Astro's example?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Logs are only useful after an attack has already succeeded</li>
            <li class="quiz-option quiz-correct">B. An attack usually leaves a trace beforehand, like failed logins, and someone has to be watching for it ✓</li>
            <li class="quiz-option">C. Logs take up too much storage space to be useful</li>
            <li class="quiz-option">D. Monitoring is only necessary once a year</li>
        </ul>
        <p class="quiz-explanation"><em>Warning signs like failed logins often appear before a breach succeeds — monitoring catches them in time to act.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What do all four best practices have in common, according to Astro?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They are expensive, one-time purchases</li>
            <li class="quiz-option quiz-correct">B. They are unglamorous, repeatable habits done regularly, not once and forgotten ✓</li>
            <li class="quiz-option">C. They only apply to large organizations</li>
            <li class="quiz-option">D. They replace the need for a security policy</li>
        </ul>
        <p class="quiz-explanation"><em>Astro emphasizes that these are ordinary habits repeated on a schedule — their value comes from consistency, not sophistication.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> The relay console review turned up four gaps. Close them: shrink the over-privileged account, close the stale port, and confirm the log entries are seen.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a2-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm2', 'lesson' => 'lesson04']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the review notes, then close the unused port, disable the
            unused diagnostic account, and read the flagged log entries.
        </p>
    </div>

</div>
