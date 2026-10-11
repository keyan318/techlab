{{-- M1 · Lesson 1.3: Justifying the Security Investment — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a1-l3.json) completes the lesson.
Syllabus: ITP1333 Unit A.3 Justifying Security Investment. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="03">

    <h1 class="lesson-heading">Lesson 1.3: Justifying the Security Investment</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> weigh the cost of a security control against the loss it prevents, and explain why security spending is a risk decision, not a blank check.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Security costs money and time, so nobody gets an unlimited budget for it. Every control has to be justified
        by what it prevents. A simple way to think about it: if a control costs less than the loss it prevents,
        multiplied by how likely that loss is, it is probably worth buying. If it costs more, the crew is better off
        spending on something else, or accepting the smaller risk.
    </p>

    <p class="body-text">
        This does not mean the cheapest option always wins. A low-cost control that stops a high-likelihood,
        high-impact loss is worth far more than an expensive one that barely reduces a rare, minor risk. Justifying
        an investment means comparing cost to the loss it actually prevents — not just picking the fanciest tool.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Rivet wants to buy the top-of-the-line shield diagnostic system: 8,000 credits, prevents a rare failure that
        would cost 2,000 credits to fix. Meanwhile the relay console still runs on its factory password — free to
        fix, and it is the exact door Vex's hackers used to breach the Citadel last time.
    </p>

    <p class="body-text">
        Astro turns down the diagnostic system and orders the password changed today. "Rivet, your tool is expensive
        and the problem it solves is rare and cheap to fix anyway. The password costs nothing to change and it is
        the one thing Vex has already used against us. Security spending isn't about the biggest tool. It's about
        the biggest gap."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="Compare the cost of a security control against the likelihood and impact of the loss it prevents">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">Control Cost</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">what you spend</text>
            <line x1="170" y1="66" x2="237" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="245,66 233,60 233,72" fill="#7c5cff"/>
            <rect x="245" y="30" width="150" height="72" rx="14" fill="#ffffff" stroke="#ffffff" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#9a9a9a">vs.</text>
            <line x1="395" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Loss Prevented</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">likelihood × impact</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Worth it only when the loss prevented outweighs the cost</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is the simple rule for deciding whether a security control is worth buying?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Always buy the most expensive option available</li>
            <li class="quiz-option quiz-correct">B. Compare its cost to the loss it prevents (likelihood × impact) ✓</li>
            <li class="quiz-option">C. Only buy controls that cost nothing</li>
            <li class="quiz-option">D. Ask Planet Doom what they would attack next</li>
        </ul>
        <p class="quiz-explanation"><em>A control is worth it when its cost is less than the loss it prevents, weighted by how likely that loss is.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why did Astro turn down Rivet's expensive diagnostic system?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because Astro dislikes new tools</li>
            <li class="quiz-option quiz-correct">B. Because it was expensive and solved a rare, cheap-to-fix problem, while a bigger gap sat unfixed for free ✓</li>
            <li class="quiz-option">C. Because Rivet asked for it at the wrong time</li>
            <li class="quiz-option">D. Because Codexia law forbids diagnostic systems</li>
        </ul>
        <p class="quiz-explanation"><em>The expensive tool addressed a small, rare risk, while the factory password (free to fix) was the actual gap attackers had already used.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Does the cheapest security option always win?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Yes, cost is the only thing that matters</li>
            <li class="quiz-option quiz-correct">B. No — a cheap control that stops a high-likelihood, high-impact loss can be worth more than an expensive one that barely helps ✓</li>
            <li class="quiz-option">C. No, the most expensive option always wins instead</li>
            <li class="quiz-option">D. Cost is irrelevant to the decision</li>
        </ul>
        <p class="quiz-explanation"><em>What matters is the loss actually prevented relative to cost, not just the price tag by itself.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. A control costs 50 credits and prevents a loss that is unlikely and would only cost 20 credits if it happened. Is it a good investment?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Yes, always fix every risk</li>
            <li class="quiz-option quiz-correct">B. No — the cost outweighs the small, unlikely loss it prevents ✓</li>
            <li class="quiz-option">C. Only if Planet Doom is currently attacking</li>
            <li class="quiz-option">D. Yes, because it is cheap in absolute terms</li>
        </ul>
        <p class="quiz-explanation"><em>50 credits to prevent an unlikely 20-credit loss does not pay for itself — the crew is better off spending elsewhere.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does "justifying a security investment" ultimately mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Buying every available control regardless of cost</li>
            <li class="quiz-option quiz-correct">B. Comparing what a control costs to the loss it actually prevents, not just picking the fanciest tool ✓</li>
            <li class="quiz-option">C. Ignoring security spending altogether</li>
            <li class="quiz-option">D. Letting the vendor decide what the crew needs</li>
        </ul>
        <p class="quiz-explanation"><em>Justifying an investment means grounding the decision in the actual loss prevented, not the tool's reputation or price.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Rivet has three proposed purchases on the table. Read the risk figures for each, then decide which the Citadel should fund.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a1-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm1', 'lesson' => 'lesson03']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the proposal file, then classify each proposed purchase as
            worth-it or skip-it based on cost versus the loss it prevents.
        </p>
    </div>

</div>
