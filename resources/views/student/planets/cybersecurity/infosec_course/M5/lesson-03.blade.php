{{-- M5 · Lesson 5.3: Symmetric Key Cryptography — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c5-l3.json) completes the lesson.
Syllabus: ITP1232 Unit E, Symmetric Key Cryptography. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="03">

    <h1 class="lesson-heading">Lesson 5.3: Symmetric Key Cryptography</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what symmetric-key encryption is, why a single shared key is a
        single point of failure, and why leaking a key means rotating to a new one.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Symmetric-key cryptography</strong> uses one key for both jobs: the same key that scrambles
        (encrypts) a message also unscrambles (decrypts) it. It's fast and simple — but it means everyone who needs
        to read the message needs a copy of that same key. That's the trade-off: the more copies exist, the more
        chances there are for one to leak.
    </p>

    <p class="body-text">
        And once a key leaks, it doesn't matter how strong the cipher is. Anyone holding a copy of the key can
        decrypt every message sent with it — past and future — until the crew stops using that key. The fix isn't a
        cleverer cipher, it's <strong>key rotation</strong>: throw out the compromised key and switch to a brand new
        one that the attacker never had.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        14:20. <strong>Rivet</strong> stares at the radio log. "Our whole crew channel uses one shared key —
        <code>codexia7</code> — and I just found a copy of it circulating on a Doom relay. One key, one copy, and
        now two owners. Guess which one isn't us."
    </p>

    <p class="body-text">
        "First, we prove the leak is real," Rivet says. "If that key still opens our traffic, Doom has been
        reading everything we've said. Then we rotate — new key, and this time Doom doesn't get a copy."
        <strong>Volt</strong> adds: "One key for the whole crew was always a risk. A leak anywhere is a leak
        everywhere."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="One shared symmetric key both encrypts and decrypts; a leaked key is retired and replaced with a new one">
            <rect x="20" y="20" width="180" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="110" y="50" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">plaintext</text>
            <text x="110" y="70" text-anchor="middle" font-size="12.5" fill="#14306b">SHIELDS AT FULL</text>

            <rect x="230" y="20" width="180" height="70" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="50" text-anchor="middle" font-size="14" font-weight="700" fill="#8a4b00">same key</text>
            <text x="320" y="70" text-anchor="middle" font-size="12.5" fill="#8a4b00">encrypts AND decrypts</text>

            <rect x="440" y="20" width="180" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="530" y="50" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">ciphertext</text>
            <text x="530" y="70" text-anchor="middle" font-size="12.5" fill="#14306b">30272d20...</text>

            <line x1="200" y1="55" x2="226" y2="55" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="230,55 220,49 220,61" fill="#7c5cff"/>
            <line x1="410" y1="55" x2="436" y2="55" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="440,55 430,49 430,61" fill="#7c5cff"/>

            <rect x="20" y="130" width="290" height="55" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="165" y="162" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1a14">codexia7 — LEAKED, retired</text>

            <rect x="330" y="130" width="290" height="55" rx="12" fill="#eafaf0" stroke="#1e8e3e" stroke-width="2"/>
            <text x="475" y="162" text-anchor="middle" font-size="14" font-weight="700" fill="#0d5c26">relay-9x — new, Doom-free</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What makes a cipher "symmetric"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It uses two different keys, one to encrypt and one to decrypt</li>
            <li class="quiz-option quiz-correct">B. The same key encrypts and decrypts ✓</li>
            <li class="quiz-option">C. It only works on messages of equal length</li>
            <li class="quiz-option">D. It never needs a key at all</li>
        </ul>
        <p class="quiz-explanation"><em>Symmetric-key crypto uses one shared key for both encrypting and decrypting.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why is a single shared symmetric key risky for a whole crew?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It's not risky — symmetric keys can't leak</li>
            <li class="quiz-option quiz-correct">B. Every copy that exists is another chance for the key to leak ✓</li>
            <li class="quiz-option">C. Symmetric keys are always too short to matter</li>
            <li class="quiz-option">D. It only encrypts, it never decrypts</li>
        </ul>
        <p class="quiz-explanation"><em>Everyone who needs to read the message needs a copy of the same key, so more copies means more chances for a leak.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Doom got a copy of the crew's shared key. What can they now do?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing, unless they also guess the cipher algorithm</li>
            <li class="quiz-option quiz-correct">B. Decrypt any traffic encrypted with that key, until the crew stops using it ✓</li>
            <li class="quiz-option">C. Only decrypt messages sent after today</li>
            <li class="quiz-option">D. Only read messages, never decrypt them</li>
        </ul>
        <p class="quiz-explanation"><em>With symmetric encryption, holding the key is enough to decrypt anything sent with it — no other secret is needed.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. A shared key has leaked. What's the right fix?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Keep using the same key, but encrypt messages twice</li>
            <li class="quiz-option">B. Switch to a cleverer cipher with the same key</li>
            <li class="quiz-option quiz-correct">C. Rotate to a brand new key the attacker never had ✓</li>
            <li class="quiz-option">D. Stop encrypting altogether, since it clearly doesn't work</li>
        </ul>
        <p class="quiz-explanation"><em>A leaked key stays dangerous no matter how strong the cipher is. The fix is key rotation: retire the old key and issue a new one.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why did Rivet decode the intercepted traffic with the leaked key before rotating it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Decoding it was pointless busywork</li>
            <li class="quiz-option quiz-correct">B. To prove the leak was real before treating it as an emergency ✓</li>
            <li class="quiz-option">C. Because the new key wasn't ready yet</li>
            <li class="quiz-option">D. To make the old key stronger</li>
        </ul>
        <p class="quiz-explanation"><em>Confirming the leaked key still opens real traffic proves the compromise, which is what justifies rotating immediately.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> the crew radio's shared key leaked to Doom. Prove the leaked key still decrypts
        intercepted traffic, then rotate the crew to a brand new key Doom never had.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c5-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm5', 'lesson' => 'lesson03']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>xor</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
