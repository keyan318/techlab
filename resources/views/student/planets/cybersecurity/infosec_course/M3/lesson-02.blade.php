{{-- M3 · Lesson 3.2: Cybercrime & the Law — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c3-l2.json) completes the lesson.
Syllabus: ITP1232 Unit C.2 Cybercrime & the Law (incl. Philippine RA 10175). --}}

<div class="lesson-fragment" data-module="m3" data-lesson="02">

    <h1 class="lesson-heading">Lesson 3.2: Cybercrime & the Law</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain why ransomware is a crime and not a negotiation, why
        evidence must be preserved before you touch anything, and when to report an attack to the authorities.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Cybercrime</strong> is a crime committed using computers or networks — stealing data, breaking
        into systems, or holding files hostage for payment (a <strong>ransomware</strong> attack). It is not a
        business dispute. It is handled by law, the same way theft or extortion is.
    </p>

    <p class="body-text">
        Codexia has its own cybercrime law, the same way many real countries do (the Philippines' Republic Act
        10175, the Cybercrime Prevention Act, is one example). Two rules matter most for a crew responding to
        an attack:
    </p>

    <ul class="body-list">
        <li><strong>Preserve evidence first.</strong> Fingerprint (hash) anything an attacker touched before you restore or clean it up, so there's proof of exactly what happened.</li>
        <li><strong>Never pay or negotiate.</strong> Paying a ransom funds the next attack and rarely guarantees anything back. Restore from a clean backup instead.</li>
    </ul>

    <p class="body-text">
        Once the evidence is preserved and service is restored, that's the right moment to report to the
        authorities — not mid-crisis, and not only after paying (which you never do).
    </p>

    <h2 class="section-heading">Rivet's Workbench</h2>

    <p class="body-text">
        The supply logs come back scrambled, with a message demanding 500 credits to unlock them. <strong>Rivet</strong>
        doesn't reach for the crew's credits. "We never pay pirates," he says. "First, we prove what this looked
        like right now — hash it, so nobody can say later that we tampered with it ourselves."
    </p>

    <p class="body-text">
        <strong>Astro</strong> nods. "Then restore from backup. We keep clean copies exactly for days like this."
    </p>

    <p class="body-text">
        <strong>Volt</strong> adds the last piece: "Once we're stable — evidence hashed, logs restored — that's
        when we report to Codexia's authorities. Not while we're still mid-crisis, and not after paying. We
        never pay."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="Three ordered steps: preserve evidence by hashing, restore from backup, then report to authorities">
            <rect x="20" y="60" width="180" height="80" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="110" y="95" text-anchor="middle" font-size="15" font-weight="700" fill="#8a4b00">1. Preserve</text>
            <text x="110" y="115" text-anchor="middle" font-size="12.5" fill="#8a4b00">hash the evidence</text>

            <rect x="230" y="60" width="180" height="80" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="320" y="95" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">2. Restore</text>
            <text x="320" y="115" text-anchor="middle" font-size="12.5" fill="#14306b">copy the clean backup</text>

            <rect x="440" y="60" width="180" height="80" rx="14" fill="#eafaf0" stroke="#1e8a4c" stroke-width="2"/>
            <text x="530" y="95" text-anchor="middle" font-size="15" font-weight="700" fill="#146633">3. Report</text>
            <text x="530" y="115" text-anchor="middle" font-size="12.5" fill="#146633">call the authorities</text>

            <line x1="200" y1="100" x2="226" y2="100" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="230,100 218,94 218,106" fill="#7c5cff"/>
            <line x1="410" y1="100" x2="436" y2="100" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="440,100 428,94 428,106" fill="#7c5cff"/>

            <text x="320" y="175" text-anchor="middle" font-size="13.5" font-weight="700" fill="#b3261e">Never pay. Never negotiate. This order, every time.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is ransomware?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A discount offer from a vendor</li>
            <li class="quiz-option quiz-correct">B. An attack that holds files hostage until a payment is made ✓</li>
            <li class="quiz-option">C. A type of firewall rule</li>
            <li class="quiz-option">D. A backup schedule</li>
        </ul>
        <p class="quiz-explanation"><em>Ransomware scrambles or locks files and demands payment to restore them — it's extortion, not a service.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Before restoring a compromised file from backup, what should you do first?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Pay the ransom to be safe</li>
            <li class="quiz-option quiz-correct">B. Hash it, to preserve evidence of exactly what happened ✓</li>
            <li class="quiz-option">C. Delete it immediately</li>
            <li class="quiz-option">D. Nothing, restoring first is fine</li>
        </ul>
        <p class="quiz-explanation"><em>Hashing the compromised file before touching it preserves proof of what the attacker did, which restoring first would lose.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Should the crew pay the 500-credit ransom?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Yes, it's the fastest fix</li>
            <li class="quiz-option quiz-correct">B. No — paying funds the next attack and rarely guarantees anything back ✓</li>
            <li class="quiz-option">C. Yes, but only half</li>
            <li class="quiz-option">D. Only if Astro personally approves it</li>
        </ul>
        <p class="quiz-explanation"><em>Paying a ransom is never the answer — restore from a clean backup instead.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What is the right moment to report the attack to Codexia's authorities?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Immediately, before securing anything</li>
            <li class="quiz-option">B. Only after paying the ransom</li>
            <li class="quiz-option quiz-correct">C. After evidence is preserved and service is restored, once the crew is stable ✓</li>
            <li class="quiz-option">D. Never; the crew should handle it alone</li>
        </ul>
        <p class="quiz-explanation"><em>Reporting works best once you have solid evidence and aren't still mid-crisis — not immediately, and never only after paying.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. A ransomware attack is best understood as:</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A business negotiation between two parties</li>
            <li class="quiz-option quiz-correct">B. A crime, handled by law like theft or extortion ✓</li>
            <li class="quiz-option">C. A misunderstanding that resolves itself</li>
            <li class="quiz-option">D. A billing dispute</li>
        </ul>
        <p class="quiz-explanation"><em>Cybercrime laws (like the Philippines' RA 10175) treat these attacks as crimes, not negotiations — that framing decides how you respond.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> a pirate crew hired by Doom has scrambled the supply logs and wants payment.
        Preserve the evidence first, restore from the clean backup, and choose the right moment to report to
        Codexia's authorities. <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c3-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm3', 'lesson' => 'lesson02']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. You type Windows commands there, including
            <code>certutil -hashfile &lt;file&gt; SHA256</code> to fingerprint evidence and
            <code>copy &lt;backup&gt; &lt;file&gt;</code> to restore it. Order matters: hash before you restore.
        </p>
    </div>

</div>
