{{-- M1 · Lesson 1.1: Know Your Citadel — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c1-l1.json) completes the lesson.
Syllabus: ITP1232 Unit A.1 Key Terms (information assurance and security). --}}

<div class="lesson-fragment" data-module="m1" data-lesson="01">

    <h1 class="lesson-heading">Lesson 1.1: Know Your Citadel</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what information security protects, use the words
        asset, threat, vulnerability and risk correctly, and sort an attack by the part of the CIA triad it breaks.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Information security</strong> means keeping information and the systems that hold it safe from harm.
        <strong>Information assurance</strong> goes one step further: it means being able to <em>trust</em> that
        information, because you know it is protected, correct and there when you need it.
    </p>

    <p class="body-text">Four words come up in every security conversation:</p>

    <ul class="body-list">
        <li><strong>Asset</strong>: anything valuable you must protect, such as data, a device, or a service.</li>
        <li><strong>Threat</strong>: someone or something that could cause harm, such as a hacker, a virus, or a flood.</li>
        <li><strong>Vulnerability</strong>: a weakness a threat can use, such as a default password or an unpatched program.</li>
        <li><strong>Risk</strong>: the chance that a threat uses a vulnerability to harm an asset, and how bad that harm would be.</li>
    </ul>

    <p class="body-text">
        Security protects three things, called the <strong>CIA triad</strong>:
        <strong>Confidentiality</strong> (only the right people can see it),
        <strong>Integrity</strong> (nobody changes it without permission) and
        <strong>Availability</strong> (it works when you need it).
        Every attack breaks at least one of the three.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        The Citadel is the crew's base on Codexia, and everything they need to get home is inside it. At 02:47 the alarm
        goes off. <strong>Volt</strong> reports that someone from Planet Doom was inside the network for an hour.
    </p>

    <p class="body-text">
        <strong>Astro</strong> calls the crew together. "Planet Doom could not break our walls with ships, so
        Commander Vex sent hackers instead. Before we fight, we name what we protect. The starmap is our
        <em>asset</em>. Vex's hackers are the <em>threat</em>. That old relay console with its factory password?
        That is a <em>vulnerability</em>. And the chance they use it to steal the starmap is our <em>risk</em>."
    </p>

    <p class="body-text">
        <strong>Volt</strong> adds: "Last night they read our crew records, changed the shield settings, and knocked
        the comms relay offline. Three attacks, three broken promises: confidentiality, integrity and availability."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 230" role="img" aria-label="A threat uses a vulnerability to harm an asset; the harm breaks confidentiality, integrity or availability">
            <rect x="20" y="30" width="160" height="70" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="100" y="60" text-anchor="middle" font-size="17" font-weight="700" fill="#b3261e">Threat</text>
            <text x="100" y="82" text-anchor="middle" font-size="13" fill="#7a1a14">Vex's hackers</text>

            <rect x="240" y="30" width="160" height="70" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="17" font-weight="700" fill="#8a4b00">Vulnerability</text>
            <text x="320" y="82" text-anchor="middle" font-size="13" fill="#8a4b00">factory password</text>

            <rect x="460" y="30" width="160" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="540" y="60" text-anchor="middle" font-size="17" font-weight="700" fill="#14306b">Asset</text>
            <text x="540" y="82" text-anchor="middle" font-size="13" fill="#14306b">the starmap</text>

            <line x1="180" y1="65" x2="236" y2="65" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="240,65 228,59 228,71" fill="#7c5cff"/>
            <line x1="400" y1="65" x2="456" y2="65" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="460,65 448,59 448,71" fill="#7c5cff"/>
            <text x="320" y="124" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Risk = the chance this chain happens, and how bad it would be</text>

            <rect x="20" y="150" width="190" height="62" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="115" y="176" text-anchor="middle" font-size="15" font-weight="700" fill="#4a35b8">Confidentiality</text>
            <text x="115" y="197" text-anchor="middle" font-size="12.5" fill="#4a35b8">records were read</text>

            <rect x="225" y="150" width="190" height="62" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="176" text-anchor="middle" font-size="15" font-weight="700" fill="#4a35b8">Integrity</text>
            <text x="320" y="197" text-anchor="middle" font-size="12.5" fill="#4a35b8">shield settings changed</text>

            <rect x="430" y="150" width="190" height="62" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="525" y="176" text-anchor="middle" font-size="15" font-weight="700" fill="#4a35b8">Availability</text>
            <text x="525" y="197" text-anchor="middle" font-size="12.5" fill="#4a35b8">comms relay went down</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. The Citadel's starmap is the only way home. What is it?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. An asset ✓</li>
            <li class="quiz-option">B. A threat</li>
            <li class="quiz-option">C. A vulnerability</li>
            <li class="quiz-option">D. A risk</li>
        </ul>
        <p class="quiz-explanation"><em>An asset is anything valuable you must protect. The starmap is the crew's most valuable one.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. The relay console still uses its factory password, "admin". What is that?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. An asset</li>
            <li class="quiz-option">B. A threat</li>
            <li class="quiz-option quiz-correct">C. A vulnerability ✓</li>
            <li class="quiz-option">D. A protocol</li>
        </ul>
        <p class="quiz-explanation"><em>A vulnerability is a weakness a threat can use. Default passwords are among the first things attackers try.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. A hacker changed the shield power from 100% to 5% without permission. Which part of the CIA triad did that break?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Confidentiality</li>
            <li class="quiz-option quiz-correct">B. Integrity ✓</li>
            <li class="quiz-option">C. Availability</li>
            <li class="quiz-option">D. None of them</li>
        </ul>
        <p class="quiz-explanation"><em>Integrity means data is not changed without permission. The shield setting was changed, so integrity was broken.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Junk traffic knocked the comms relay offline for 7 minutes. Which part of the CIA triad was broken?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Confidentiality</li>
            <li class="quiz-option">B. Integrity</li>
            <li class="quiz-option quiz-correct">C. Availability ✓</li>
            <li class="quiz-option">D. Authentication</li>
        </ul>
        <p class="quiz-explanation"><em>Availability means a system works when you need it. A service that is down has lost availability.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Which sentence best describes a risk?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A hacker from Planet Doom</li>
            <li class="quiz-option">B. The crew's secret records</li>
            <li class="quiz-option quiz-correct">C. The chance that Vex's hackers use the factory password to steal the starmap ✓</li>
            <li class="quiz-option">D. A strong password</li>
        </ul>
        <p class="quiz-explanation"><em>Risk joins the other three words: a threat using a vulnerability to harm an asset, and how likely and how bad that is.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> the intruder is gone, but the Citadel must understand what happened. Name what you protect,
        match each of last night's attacks to the CIA triad, then follow Volt's lead to find what the intruder left behind.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c1-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm1', 'lesson' => 'lesson01']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>dir</code>, <code>type</code> and <code>cd</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
