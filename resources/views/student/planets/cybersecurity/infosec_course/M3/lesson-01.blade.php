{{-- M3 · Lesson 3.1: IT Risks — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c3-l1.json) completes the lesson.
Syllabus: ITP1232 Unit C.1 IT Risks. --}}

<div class="lesson-fragment" data-module="m3" data-lesson="01">

    <h1 class="lesson-heading">Lesson 3.1: IT Risks</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can describe a risk as likelihood times impact, and choose the
        right response to a risk: treat it, transfer it, or accept it.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        You already know a <strong>risk</strong> is the chance a threat uses a vulnerability to harm an asset.
        But a crew can't fix everything at once, so security teams <strong>rate</strong> each risk before
        deciding what to do about it.
    </p>

    <p class="body-text">
        A risk's rating comes from two numbers: <strong>likelihood</strong> (how probable is it?) and
        <strong>impact</strong> (how bad would it be?). A risk that is both likely <em>and</em> damaging
        goes to the top of the list. A risk that's unlikely and barely matters goes to the bottom.
    </p>

    <p class="body-text">Once a risk is rated, a team picks one of three responses:</p>

    <ul class="body-list">
        <li><strong>Treat</strong>: fix it yourself, now. Used for risks that are both likely and damaging.</li>
        <li><strong>Transfer</strong>: hand the burden to someone better placed to carry it — insurance, a support contract, or an ally crew. Used for risks with high impact that you can't fully own alone.</li>
        <li><strong>Accept</strong>: note it and move on. Used for risks with low impact, no matter how likely.</li>
    </ul>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        <strong>Astro</strong> spreads five reports across the table. "Too many threats, too few hands. We
        cannot treat all five today, so we rate them first."
    </p>

    <p class="body-text">
        "The relay console's factory password," Astro says, "high likelihood, high impact — Vex's scouts
        already tried it once. That gets treated today. The old cargo bay lock is low likelihood and low
        impact, nothing valuable behind it — we accept that one and move on."
    </p>

    <p class="body-text">
        <strong>Volt</strong> points at the third report. "Only one comms relay. If it drops, we're blind
        mid-siege. Medium likelihood, but the impact is severe enough that we treat it too, not just note it."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> taps the last one. "No offsite backup of the starmap. Low likelihood it's ever
        needed, but if it is, losing the only copy is catastrophic. That's not ours to carry alone — Codexia's
        allied fleet already offered to hold a copy. We transfer that one."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 260" role="img" aria-label="A 2 by 2 grid of likelihood versus impact, with treat, transfer and accept zones marked">
            <text x="320" y="24" text-anchor="middle" font-size="16" font-weight="700" fill="#14306b">Likelihood × Impact</text>

            <line x1="90" y1="50" x2="90" y2="220" stroke="#7c8aa8" stroke-width="2"/>
            <line x1="90" y1="220" x2="590" y2="220" stroke="#7c8aa8" stroke-width="2"/>
            <text x="60" y="70" text-anchor="middle" font-size="12" fill="#4a5a7a">High</text>
            <text x="60" y="205" text-anchor="middle" font-size="12" fill="#4a5a7a">Low</text>
            <text x="45" y="140" text-anchor="middle" font-size="12" fill="#4a5a7a" transform="rotate(-90 45 140)">Impact</text>
            <text x="150" y="238" text-anchor="middle" font-size="12" fill="#4a5a7a">Low</text>
            <text x="530" y="238" text-anchor="middle" font-size="12" fill="#4a5a7a">High</text>
            <text x="340" y="252" text-anchor="middle" font-size="12" fill="#4a5a7a">Likelihood</text>

            <rect x="340" y="50" width="250" height="85" fill="#fdeaea" stroke="#b3261e" stroke-width="1.5"/>
            <text x="465" y="95" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1a14">TREAT</text>
            <text x="465" y="114" text-anchor="middle" font-size="11.5" fill="#7a1a14">relay password</text>

            <rect x="90" y="50" width="250" height="85" fill="#fff4e0" stroke="#f08c1a" stroke-width="1.5"/>
            <text x="215" y="95" text-anchor="middle" font-size="14" font-weight="700" fill="#8a4b00">TRANSFER</text>
            <text x="215" y="114" text-anchor="middle" font-size="11.5" fill="#8a4b00">offsite backup</text>

            <rect x="340" y="135" width="250" height="85" fill="#fff4e0" stroke="#f08c1a" stroke-width="1.5"/>
            <text x="465" y="180" text-anchor="middle" font-size="14" font-weight="700" fill="#8a4b00">TREAT</text>
            <text x="465" y="199" text-anchor="middle" font-size="11.5" fill="#8a4b00">comms single path</text>

            <rect x="90" y="135" width="250" height="85" fill="#eafaf0" stroke="#1e8a4c" stroke-width="1.5"/>
            <text x="215" y="180" text-anchor="middle" font-size="14" font-weight="700" fill="#146633">ACCEPT</text>
            <text x="215" y="199" text-anchor="middle" font-size="11.5" fill="#146633">cargo bay lock</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. A risk's rating is based on which two factors?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Cost and color</li>
            <li class="quiz-option quiz-correct">B. Likelihood and impact ✓</li>
            <li class="quiz-option">C. Speed and size</li>
            <li class="quiz-option">D. Age and location</li>
        </ul>
        <p class="quiz-explanation"><em>A risk is rated by how probable it is (likelihood) and how bad it would be (impact).</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. The relay console's factory password is high likelihood and high impact. What's the right response?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Treat it now ✓</li>
            <li class="quiz-option">B. Transfer it</li>
            <li class="quiz-option">C. Accept it</li>
            <li class="quiz-option">D. Ignore it, it will pass</li>
        </ul>
        <p class="quiz-explanation"><em>High likelihood and high impact means fix it yourself, now — that's what "treat" means.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. The old cargo bay lock is low likelihood and low impact. What's the right response?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Treat it</li>
            <li class="quiz-option">B. Transfer it</li>
            <li class="quiz-option quiz-correct">C. Accept it ✓</li>
            <li class="quiz-option">D. Escalate it to Codexia's council</li>
        </ul>
        <p class="quiz-explanation"><em>When both likelihood and impact are low, the response is to accept it and move on — treating it would waste effort better spent elsewhere.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Losing the only starmap backup is unlikely but catastrophic, and an ally already offered to hold a copy. What's the right response?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Accept it</li>
            <li class="quiz-option quiz-correct">B. Transfer it ✓</li>
            <li class="quiz-option">C. Treat it</li>
            <li class="quiz-option">D. Delete the starmap so there's nothing to lose</li>
        </ul>
        <p class="quiz-explanation"><em>When the impact is high but someone else is better placed to carry it, you transfer the risk instead of owning it alone.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why rate risks instead of just fixing everything at once?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. A crew has limited time and hands, so the worst risks need to go first ✓</li>
            <li class="quiz-option">B. Rating risks is required by tradition, not by need</li>
            <li class="quiz-option">C. Only high-likelihood risks matter, impact never does</li>
            <li class="quiz-option">D. Only high-impact risks matter, likelihood never does</li>
        </ul>
        <p class="quiz-explanation"><em>Rating risks lets a team with limited resources decide what to fix first, transfer, or safely leave alone.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> too many threats, too few hands. Read the Citadel's risk register and rate
        each entry: treat the ones that are both likely and damaging, transfer the ones an ally can help
        carry, and accept the ones too minor to chase.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c3-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm3', 'lesson' => 'lesson01']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. You type Windows commands there (Command Prompt), including
            <code>classify &lt;item&gt; &lt;category&gt;</code> to rate each risk. Objectives tick off as you go.
        </p>
    </div>

</div>
