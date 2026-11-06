{{-- M1 · Lesson 1.1: Why Information Protection Matters — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a1-l1.json) completes the lesson.
Syllabus: ITP1333 Unit A.1 Importance of Info Protection. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="01">

    <h1 class="lesson-heading">Lesson 1.1: Why Information Protection Matters</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> explain why organizations keep protecting information even after an attack ends, and describe due care and due diligence.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Information has value even when nobody is attacking it right now. A crew roster, a starmap, a set of engine
        specs — losing any of them costs something, and that cost does not disappear just because the siege is over.
    </p>

    <p class="body-text">When something goes wrong, the cost usually shows up in four places:</p>

    <ul class="body-list">
        <li><strong>Financial</strong>: money spent repairing damage or replacing what was lost.</li>
        <li><strong>Legal</strong>: rules and promises a crew must answer to — regulators, allies, contracts.</li>
        <li><strong>Trust</strong>: allies and customers who stop believing you'll keep their data safe.</li>
        <li><strong>Operational</strong>: missions delayed or paused while the crew cleans up.</li>
    </ul>

    <p class="body-text">
        <strong>Due care</strong> is the reasonable, expected level of protection a careful organization puts in
        place — a lock on the door, a password on the console. <strong>Due diligence</strong> is keeping that
        protection working over time: checking it, updating it, proving it still holds. Due care is the promise;
        due diligence is keeping it.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What is the difference between due care and due diligence?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Care is checking; diligence is the lock itself</li>
            <li class="quiz-option quiz-correct">B. Care is the promise; diligence keeps it ✓</li>
            <li class="quiz-option">C. Care is for money; diligence is for the law only</li>
            <li class="quiz-option">D. They are two names for the very same thing</li>
        </ul>
        <p class="quiz-explanation"><em>Due care is putting reasonable protection in place. Due diligence is keeping it working over time.</em></p>
    </div>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        The morning after the siege ends, a cadet wants to stand down every extra watch. "We won, Astro. Why keep
        doing all this?"
    </p>

    <p class="body-text">
        Astro doesn't answer with more rules — Astro answers with the bill. "The repair crew billed us 12,000
        credits for the shield generator. Codexia's regulators want a report on the crew-records breach within ten
        days, or we lose our operating charter. Three allied crews now double-check every report we send them,
        because one leaked. And every mission paused for a week while we cleaned up. That is what one lapse cost —
        financial, legal, trust, operational. Protecting information isn't the reward for winning a siege. It's
        what keeps that bill from arriving again."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">The regulators want a breach report in ten days. Which cost is that?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Trust</li>
            <li class="quiz-option quiz-correct">B. Legal ✓</li>
            <li class="quiz-option">C. Financial</li>
            <li class="quiz-option">D. Operational</li>
        </ul>
        <p class="quiz-explanation"><em>Rules and promises a crew must answer to, like regulators and contracts, are the legal cost.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="One security lapse costs financially, legally, in trust, and operationally">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">Financial</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">repair & replace</text>
            <line x1="170" y1="66" x2="162" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="170,66 158,60 158,72" fill="#7c5cff"/>
            <rect x="170" y="30" width="150" height="72" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="245" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#8a4b00">Legal</text>
            <text x="245" y="82" text-anchor="middle" font-size="12.5" fill="#8a4b00">regulators & contracts</text>
            <line x1="320" y1="66" x2="312" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="320,66 308,60 308,72" fill="#7c5cff"/>
            <rect x="320" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="395" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">Trust</text>
            <text x="395" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">allies stop believing you</text>
            <line x1="470" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">Operational</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">missions pause</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Four places the cost of one lapse shows up</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what does the operational cost mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Missions pause ✓</li>
            <li class="quiz-option">B. Allies stop believing</li>
            <li class="quiz-option">C. Repair and replace</li>
            <li class="quiz-option">D. Regulators and contracts</li>
        </ul>
        <p class="quiz-explanation"><em>Operational cost is missions delayed or paused while the crew cleans up.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why do organizations keep protecting information even when no attack is happening?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because regulators require a party every quarter</li>
            <li class="quiz-option quiz-correct">B. Because the value of the information and the cost of losing it don't go away ✓</li>
            <li class="quiz-option">C. Because it looks good in a recruiting brochure</li>
            <li class="quiz-option">D. They don't — protection only matters during an attack</li>
        </ul>
        <p class="quiz-explanation"><em>The value of what you protect, and the cost of losing it, exist whether or not someone is attacking right now.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is "due care"?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The reasonable, expected level of protection a careful organization puts in place ✓</li>
            <li class="quiz-option">B. A legal document signed after a breach</li>
            <li class="quiz-option">C. The most expensive security tool on the market</li>
            <li class="quiz-option">D. A synonym for a firewall</li>
        </ul>
        <p class="quiz-explanation"><em>Due care is the baseline of reasonable protection — the locks and passwords a prudent crew is expected to have.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What is "due diligence"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Buying insurance instead of fixing the problem</li>
            <li class="quiz-option quiz-correct">B. The ongoing effort to keep those protections working over time ✓</li>
            <li class="quiz-option">C. A one-time setup step, never repeated</li>
            <li class="quiz-option">D. Firing whoever caused the breach</li>
        </ul>
        <p class="quiz-explanation"><em>Due diligence is the follow-through: checking, updating and proving the protection still holds.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Which of these is NOT typically a cost of an information security failure?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Financial loss</li>
            <li class="quiz-option">B. Legal penalties</li>
            <li class="quiz-option">C. Loss of trust</li>
            <li class="quiz-option quiz-correct">D. Faster engines ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Faster engines has nothing to do with a security failure — the other three are the classic costs.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Crew records leaked to a rival crew, and now three allies double-check every report the Citadel sends. Which cost is this?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Financial cost</li>
            <li class="quiz-option">B. Legal cost</li>
            <li class="quiz-option quiz-correct">C. Trust cost ✓</li>
            <li class="quiz-option">D. Operational cost</li>
        </ul>
        <p class="quiz-explanation"><em>Allies who stop taking your word at face value is a trust cost — the hardest one to repair.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Astro wants the after-action numbers on record before the crew forgets them. Read the report, then sort each cost into its category so command can see exactly what one lapse cost.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a1-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm1', 'lesson' => 'lesson01']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>dir</code>, <code>type</code> and <code>classify</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
