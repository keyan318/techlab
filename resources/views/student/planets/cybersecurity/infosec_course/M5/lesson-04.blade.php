{{-- M5 · Lesson 5.4: Public Key Cryptography — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c5-l4.json) completes the lesson.
Syllabus: ITP1232 Unit E, Public Key Cryptography. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="04">

    <h1 class="lesson-heading">Lesson 5.4: Public Key Cryptography</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain the difference between a private key and a public key, and
        explain why a message that doesn't verify against the sender's public key can't be trusted.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Public-key cryptography</strong> uses two different keys instead of one shared secret. Each person
        keeps a <strong>private key</strong> that never leaves their hands, and publishes a matching
        <strong>public key</strong> that anyone can have. To sign a message, you use your private key. To
        <strong>verify</strong> that signature, anyone can check it against your public key — but only your private
        key could have produced a signature that checks out.
    </p>

    <p class="body-text">
        That's the whole trick: forging a valid signature requires the private key, and nobody but the real owner
        has it. Doom can copy someone's name, their writing style, even their letterhead — but without the private
        key, any signature they attach will fail to verify. A message that doesn't verify against the claimed
        sender's public key should never be trusted, no matter how convincing it looks.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        18:45. Two messages arrive, both claiming to be from Codexia's allied fleet. <strong>Rivet</strong> pulls up
        the signature report on each one. "With a shared key, if it leaks, anyone can forge a message that looks
        real. Public-key crypto doesn't have that weak spot — Doom would need Codexia's actual private key, and
        they don't have it, no matter how well they copy the letterhead."
    </p>

    <p class="body-text">
        <strong>Astro</strong> reads the two reports side by side. "One verifies against the Allied Fleet's key on
        file. The other has no matching key at all. Act only on what's actually signed."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="A private key signs a message; the matching public key verifies it, exposing a forged message that has no matching key">
            <rect x="20" y="20" width="180" height="70" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="110" y="50" text-anchor="middle" font-size="14" font-weight="700" fill="#8a4b00">private key</text>
            <text x="110" y="70" text-anchor="middle" font-size="12.5" fill="#8a4b00">kept secret, signs</text>

            <line x1="200" y1="55" x2="226" y2="55" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="230,55 220,49 220,61" fill="#7c5cff"/>

            <rect x="230" y="20" width="180" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="320" y="50" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">signed message</text>
            <text x="320" y="70" text-anchor="middle" font-size="12.5" fill="#14306b">"rendezvous confirmed"</text>

            <line x1="410" y1="55" x2="436" y2="55" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="440,55 430,49 430,61" fill="#7c5cff"/>

            <rect x="440" y="20" width="180" height="70" rx="14" fill="#eafaf0" stroke="#1e8e3e" stroke-width="2"/>
            <text x="530" y="50" text-anchor="middle" font-size="14" font-weight="700" fill="#0d5c26">public key</text>
            <text x="530" y="70" text-anchor="middle" font-size="12.5" fill="#0d5c26">shared, verifies it</text>

            <rect x="20" y="130" width="290" height="55" rx="12" fill="#eafaf0" stroke="#1e8e3e" stroke-width="2"/>
            <text x="165" y="162" text-anchor="middle" font-size="14" font-weight="700" fill="#0d5c26">MESSAGE-A: verified — trust it</text>

            <rect x="330" y="130" width="290" height="55" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="475" y="162" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1a14">MESSAGE-B: no matching key — forged</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In public-key cryptography, which key do you use to sign a message?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Your public key</li>
            <li class="quiz-option quiz-correct">B. Your private key ✓</li>
            <li class="quiz-option">C. The recipient's public key</li>
            <li class="quiz-option">D. A shared symmetric key</li>
        </ul>
        <p class="quiz-explanation"><em>Only your private key, kept secret, can produce a signature that verifies against your public key.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Who is allowed to have a copy of your public key?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Only you</li>
            <li class="quiz-option">B. Only trusted allies, never anyone else</li>
            <li class="quiz-option quiz-correct">C. Anyone — that's why it's called "public" ✓</li>
            <li class="quiz-option">D. No one; public keys are never shared</li>
        </ul>
        <p class="quiz-explanation"><em>A public key is meant to be shared freely — it's the private key that must stay secret.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why can't Doom forge a message that verifies as Codexia's allied fleet?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Doom doesn't know the fleet's public key</li>
            <li class="quiz-option quiz-correct">B. Doom doesn't have the fleet's private key, which is the only thing that can produce a valid signature ✓</li>
            <li class="quiz-option">C. Forged messages are always written in bad grammar</li>
            <li class="quiz-option">D. Public-key messages can't be copied</li>
        </ul>
        <p class="quiz-explanation"><em>A valid signature can only be produced with the matching private key, which stays with its real owner.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. MESSAGE-B claims to be from the allied fleet but has no matching public key on file. What should the crew do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Trust it, since it uses the right letterhead</li>
            <li class="quiz-option quiz-correct">B. Distrust it — it can't be verified, so it shouldn't be acted on ✓</li>
            <li class="quiz-option">C. Ask Doom to confirm it</li>
            <li class="quiz-option">D. Trust it only if it sounds urgent enough</li>
        </ul>
        <p class="quiz-explanation"><em>Without a verifying signature, there's no proof the message came from the claimed sender, regardless of how it reads.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. How is public-key crypto different from the shared-key crypto in the last lesson?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They are the same thing with different names</li>
            <li class="quiz-option quiz-correct">B. Public-key crypto uses two separate keys instead of one shared secret everyone must protect ✓</li>
            <li class="quiz-option">C. Public-key crypto never needs a key at all</li>
            <li class="quiz-option">D. Shared-key crypto is only used for signatures, never encryption</li>
        </ul>
        <p class="quiz-explanation"><em>A shared symmetric key is one secret everyone must protect. Public-key crypto splits the job: a private key stays with one owner, a public key is shared freely.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> two messages claim to be from Codexia's allied fleet. Check each one's signature
        report, and act only on the one that actually verifies.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c5-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm5', 'lesson' => 'lesson04']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>classify</code> and <code>submit</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
