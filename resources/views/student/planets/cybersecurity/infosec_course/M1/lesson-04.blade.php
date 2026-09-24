{{-- M1 · Lesson 1.4: Awareness & Management Commitment — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c1-l4.json) completes the lesson.
Syllabus: ITP1232 Unit A.4 Awareness & Management Commitment. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="04">

    <h1 class="lesson-heading">Lesson 1.4: Awareness &amp; Management Commitment</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can spot the tells of a phishing message — a spoofed sender, urgency that
        skips normal steps, a link to somewhere the organization doesn't own — and explain why security awareness
        training matters even when the technical defenses are strong.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Phishing</strong> is a message pretending to be from someone trustworthy, trying to get you to click,
        reply, or hand over something you shouldn't. It doesn't break through a firewall — it walks past every
        technical defense by convincing a person to open the door themselves.
    </p>

    <p class="body-text">Three tells show up again and again:</p>

    <ul class="body-list">
        <li><strong>A spoofed sender</strong>: an address that's almost right — a swapped letter, a zero for an O, an extra word in the domain.</li>
        <li><strong>Urgency</strong>: "right now," "immediately," "or you'll lose access" — designed to make you skip the normal steps you'd otherwise take.</li>
        <li><strong>A mismatched link or ask</strong>: a link to a domain the organization doesn't own, or a request the normal chain of command wouldn't ask for over a message.</li>
    </ul>

    <p class="body-text">
        This is why <strong>security awareness</strong> is management's job as much as any firewall: a crew that
        knows what to look for stops a phishing attempt that no amount of hardware alone would catch, because the
        target was never the network — it was a person's judgment in the moment.
    </p>

    <h2 class="section-heading">Astro Says</h2>

    <p class="body-text">
        Six messages land in the crew inbox overnight, all claiming to matter right now. <strong>Astro</strong>
        gathers the crew before anyone replies to anything. "A hacker only needs one of you to click. That's it.
        One click, and every wall Volt built doesn't matter."
    </p>

    <p class="body-text">
        <strong>Volt</strong> walks through it. "Real Citadel mail comes from <code>@codexia.fleet</code>, never asks
        you to skip a step, and never rushes you. Doom's messages almost always get one of those three things wrong
        — look closely, not quickly." <strong>Astro</strong>: "This isn't a one-time drill. Every crew member checks
        every message like this, every time. That commitment is the actual defense."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 190" role="img" aria-label="Three phishing tells: a spoofed sender, urgency, and a mismatched link, versus a real message with none of them">
            <rect x="20" y="20" width="280" height="150" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="160" y="46" text-anchor="middle" font-size="15" font-weight="700" fill="#b3261e">Doom's message</text>
            <text x="38" y="72" font-size="12.5" fill="#7a1a14">From: astro@c0dexia.fleet</text>
            <text x="38" y="94" font-size="12.5" fill="#7a1a14">"URGENT — reply right now"</text>
            <text x="38" y="116" font-size="12.5" fill="#7a1a14">Link: doom-relay.example</text>
            <text x="38" y="145" font-size="12" fill="#b3261e" font-weight="700">3 tells: spoofed, urgent, wrong link</text>

            <rect x="340" y="20" width="280" height="150" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="480" y="46" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Real message</text>
            <text x="358" y="72" font-size="12.5" fill="#14306b">From: astro@codexia.fleet</text>
            <text x="358" y="94" font-size="12.5" fill="#14306b">"Standard rotation, 12:00"</text>
            <text x="358" y="116" font-size="12.5" fill="#14306b">No link, no rush</text>
            <text x="358" y="145" font-size="12" fill="#14306b" font-weight="700">0 tells: report vs. keep is clear</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. A message claims to be from Astro but arrives from "astro@c0dexia.fleet" (with a zero). What tell is this?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. A spoofed sender ✓</li>
            <li class="quiz-option">B. Urgency</li>
            <li class="quiz-option">C. A valid Citadel address</li>
            <li class="quiz-option">D. A firewall rule</li>
        </ul>
        <p class="quiz-explanation"><em>A spoofed sender is an address that's almost right — here, a zero standing in for the letter O in "codexia".</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. A message says "reply with the override code right away, skip the usual verification." What tell is this?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A mismatched link</li>
            <li class="quiz-option quiz-correct">B. Urgency, designed to make you skip normal steps ✓</li>
            <li class="quiz-option">C. A spoofed sender</li>
            <li class="quiz-option">D. A quiz explanation</li>
        </ul>
        <p class="quiz-explanation"><em>Urgency pressures you to act before you'd normally stop and check — that pressure is itself a warning sign.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why doesn't a strong firewall stop phishing on its own?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Firewalls don't exist for email</li>
            <li class="quiz-option quiz-correct">B. Phishing targets a person's judgment, not the network itself ✓</li>
            <li class="quiz-option">C. Phishing messages are always encrypted</li>
            <li class="quiz-option">D. Firewalls only work at night</li>
        </ul>
        <p class="quiz-explanation"><em>Phishing walks past technical defenses by convincing a person to act — that's why awareness training is a defense in its own right, not a backup to hardware.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. A message from supply@codexia.fleet contains the normal weekly manifest, no rush, no odd links. What should you do with it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Report it, just in case</li>
            <li class="quiz-option quiz-correct">B. Keep it — nothing about it matches a phishing tell ✓</li>
            <li class="quiz-option">C. Forward it to Commander Vex</li>
            <li class="quiz-option">D. Delete it without reading</li>
        </ul>
        <p class="quiz-explanation"><em>Not every message is an attack — a legitimate address, no urgency, and a normal, expected ask is exactly what real Citadel mail looks like.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Astro insists every crew member checks every message this way, every time, not just once. What is he describing?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Data masking</li>
            <li class="quiz-option">B. A risk register</li>
            <li class="quiz-option quiz-correct">C. Security awareness as an ongoing commitment, not a one-time drill ✓</li>
            <li class="quiz-option">D. Public key cryptography</li>
        </ul>
        <p class="quiz-explanation"><em>Management commitment to security means awareness is a standing practice — one email one time doesn't create the habit that actually stops phishing.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> six messages landed in the crew inbox overnight. Open each one, then <code>keep</code>
        the real orders and <code>report</code> the Doom phish — look for a spoofed sender, urgency, or a link the
        Citadel doesn't own. <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c1-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm1', 'lesson' => 'lesson04']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>inbox</code>, <code>open</code>, <code>report</code> and <code>keep</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
