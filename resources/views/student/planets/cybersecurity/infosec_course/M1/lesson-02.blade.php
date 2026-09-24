{{-- M1 · Lesson 1.2: Attacks & Vulnerabilities — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Volt / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c1-l2.json) completes the lesson.
Syllabus: ITP1232 Unit A.2 Attacks & Vulnerabilities. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="02">

    <h1 class="lesson-heading">Lesson 1.2: Attacks &amp; Vulnerabilities</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain the difference between an attack and a vulnerability,
        name why default (factory) passwords are one of the first things an attacker tries, and describe
        how ethical hacking — testing your own systems with permission — finds a hole before an attacker does.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>vulnerability</strong> is a weakness. An <strong>attack</strong> is someone actually using that
        weakness to cause harm. A locked door with a spare key taped under the mat is a vulnerability whether or
        not anyone ever finds it; the moment someone uses that key, it's an attack.
    </p>

    <p class="body-text">
        Some of the oldest, easiest attacks use <strong>default credentials</strong>: the username and password a
        device or account ships with before anyone changes them. Every attacker's toolkit starts with a list of
        factory usernames and passwords, because so many systems never get past the factory settings.
    </p>

    <p class="body-text">
        <strong>Ethical hacking</strong> flips this around: with clear permission from whoever owns the system, you
        attack it yourself first — the same way a real attacker would — so you find the hole before they do. Without
        permission, the exact same actions are a crime. Permission is the entire difference.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        Volt's sensors picked up something probing the relay console overnight — nothing got through, just a knock
        on the door. <strong>Volt</strong> pulls up the maintenance card. "This console went in three tours ago, in
        a hurry, before a supply run. Rivet, tell me we changed the password after."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> checks. "...We did not." <strong>Astro</strong> doesn't wait. "Volt, you have my
        authorization: attack our own door before Vex's scouts come back for it. If the factory password still
        opens it, we need to know today, not after they've used it."
    </p>

    <p class="body-text">
        <strong>Volt</strong> nods. "That's the job. Test it, prove it, then Rivet closes it. We never touch a
        system without the order to — but this one, Astro just gave it."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="A vulnerability sits quietly until an attack uses it; ethical hacking tests it first, with permission, so the crew can patch it">
            <rect x="20" y="70" width="170" height="70" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="105" y="100" text-anchor="middle" font-size="16" font-weight="700" fill="#8a4b00">Vulnerability</text>
            <text x="105" y="122" text-anchor="middle" font-size="12.5" fill="#8a4b00">factory password, unused</text>

            <line x1="190" y1="105" x2="240" y2="105" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="244,105 232,99 232,111" fill="#7c5cff"/>
            <text x="217" y="90" text-anchor="middle" font-size="12" fill="#4a35b8">used by</text>

            <rect x="248" y="20" width="150" height="70" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="323" y="50" text-anchor="middle" font-size="16" font-weight="700" fill="#4a35b8">Volt's Drill</text>
            <text x="323" y="72" text-anchor="middle" font-size="12" fill="#4a35b8">ethical, authorized</text>

            <rect x="248" y="105" width="150" height="70" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="323" y="135" text-anchor="middle" font-size="16" font-weight="700" fill="#b3261e">Doom's Attack</text>
            <text x="323" y="157" text-anchor="middle" font-size="12" fill="#7a1a14">unauthorized</text>

            <line x1="398" y1="55" x2="450" y2="55" stroke="#2f6fe0" stroke-width="3"/>
            <polygon points="454,55 442,49 442,61" fill="#2f6fe0"/>
            <rect x="456" y="20" width="164" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="538" y="50" text-anchor="middle" font-size="16" font-weight="700" fill="#14306b">Patched First</text>
            <text x="538" y="72" text-anchor="middle" font-size="12" fill="#14306b">door welded shut</text>

            <line x1="398" y1="140" x2="450" y2="140" stroke="#b3261e" stroke-width="3" stroke-dasharray="5,4"/>
            <polygon points="454,140 442,134 442,146" fill="#b3261e"/>
            <rect x="456" y="105" width="164" height="70" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="538" y="135" text-anchor="middle" font-size="16" font-weight="700" fill="#b3261e">Breach</text>
            <text x="538" y="157" text-anchor="middle" font-size="12" fill="#7a1a14">too late to patch</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. The relay console still has its factory password, but nobody has tried it yet. What is this, on its own?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. An attack</li>
            <li class="quiz-option quiz-correct">B. A vulnerability ✓</li>
            <li class="quiz-option">C. A risk register</li>
            <li class="quiz-option">D. A firewall policy</li>
        </ul>
        <p class="quiz-explanation"><em>A vulnerability is a weakness sitting there, used or not. It only becomes an attack once someone actually uses it.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why do attackers try default (factory) passwords first?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They're required by law to try it first</li>
            <li class="quiz-option quiz-correct">B. Because so many systems never get their password changed, it works surprisingly often ✓</li>
            <li class="quiz-option">C. Default passwords are stronger than custom ones</li>
            <li class="quiz-option">D. Default passwords bypass every firewall automatically</li>
        </ul>
        <p class="quiz-explanation"><em>Default credentials are public knowledge (they're in the manual), and enough devices are left unchanged that trying them first pays off often enough to be step one.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Volt logs into the relay console with its factory password, with Astro's written authorization, purely to test it. What is this called?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Cybercrime</li>
            <li class="quiz-option quiz-correct">B. Ethical hacking ✓</li>
            <li class="quiz-option">C. Data masking</li>
            <li class="quiz-option">D. Social engineering</li>
        </ul>
        <p class="quiz-explanation"><em>Ethical hacking is testing a system's defenses with the owner's explicit permission, to find and fix holes before someone without permission finds them.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What's the one thing that separates Volt's Drill from an actual crime, when both involve the exact same login attempt?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. How strong the password is</li>
            <li class="quiz-option">B. Whether it's done at night</li>
            <li class="quiz-option quiz-correct">C. Whether the system's owner authorized it ✓</li>
            <li class="quiz-option">D. Whether it succeeds</li>
        </ul>
        <p class="quiz-explanation"><em>The action can be identical. Authorization is the entire legal and ethical difference between a security test and an attack.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. After Volt proves the factory password still works, what should happen next?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Leave it, since testing already proved it once and that's enough</li>
            <li class="quiz-option">B. Tell Commander Vex so he knows not to bother</li>
            <li class="quiz-option quiz-correct">C. Rivet changes it to a strong, unique password right away ✓</li>
            <li class="quiz-option">D. Disable the whole relay console permanently</li>
        </ul>
        <p class="quiz-explanation"><em>Finding a vulnerability isn't the goal — fixing it is. A test that isn't followed by a fix hasn't protected anything.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Volt's sensors caught a probe against the relay console. Read Rivet's maintenance
        card, then run Volt's Drill: try the factory password yourself with <code>login relay admin</code>. If it
        works, patch it with <code>net user relay &lt;new-password&gt;</code>, then try the old password one more
        time to prove the door is really shut. <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c1-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm1', 'lesson' => 'lesson02']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>login</code> and <code>net user</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
