{{-- M4 · Lesson 4.1: Cryptography Basics — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c4-l1.json) completes the lesson.
Syllabus: ITP1232 Unit D.1 Cryptography basics. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="01">

    <h1 class="lesson-heading">Lesson 4.1: Cryptography Basics</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what a cipher does, decode a message encrypted with a
        Caesar (shift) cipher, and explain why a simple shift cipher is weak while a real modern cipher is strong.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Cryptography</strong> turns readable text (<strong>plaintext</strong>) into scrambled text
        (<strong>ciphertext</strong>) using a rule called a <strong>cipher</strong>, so that only someone who
        knows the rule (and often a secret <strong>key</strong>) can turn it back.
        <strong>Encoding</strong> (or <em>encrypting</em>) is plaintext → ciphertext.
        <strong>Decoding</strong> (or <em>decrypting</em>) is ciphertext → plaintext.
    </p>

    <p class="body-text">
        One of the oldest ciphers is the <strong>Caesar cipher</strong>, or <strong>shift cipher</strong>: every
        letter is shifted a fixed number of places through the alphabet. Shift by 3, and A becomes D, B becomes E,
        and so on. It is simple to use, and just as simple to break: there are only 25 possible shifts, so anyone
        can try them all in seconds, or count which letter shows up most often (in English, usually "E") and guess
        the shift from there. Real, modern ciphers use a much larger secret key so that trying every possibility
        would take longer than the age of the universe.
    </p>

    <h2 class="section-heading">Volt's Drill</h2>

    <p class="body-text">
        05:55. The Citadel's comms relay catches a transmission that isn't ours. <strong>Volt</strong> flags it
        immediately: "Vex is talking to someone. I can't read a word of it."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> looks over the intercept. "It's shifted. A basic letter-shift cipher — the kind
        first used by Roman generals, and about as hard to crack today. Try shifting it back a few letters."
    </p>

    <p class="body-text">
        <strong>Astro</strong> nods at the Cadet. "Read what Vex thinks we can't. Then answer back in something
        Vex can't undo so easily."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 210" role="img" aria-label="A Caesar cipher shifts each letter of the plaintext by a fixed amount to produce ciphertext, and shifting back by the same amount recovers the plaintext">
            <rect x="20" y="30" width="180" height="70" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="110" y="60" text-anchor="middle" font-size="16" font-weight="700" fill="#14306b">Plaintext</text>
            <text x="110" y="82" text-anchor="middle" font-size="13" fill="#14306b">SHIELDS HOLD</text>

            <rect x="230" y="30" width="180" height="70" rx="14" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="320" y="55" text-anchor="middle" font-size="15" font-weight="700" fill="#8a4b00">Shift Cipher</text>
            <text x="320" y="75" text-anchor="middle" font-size="12.5" fill="#8a4b00">key = shift amount</text>
            <text x="320" y="92" text-anchor="middle" font-size="12.5" fill="#8a4b00">(e.g. shift by 12)</text>

            <rect x="440" y="30" width="180" height="70" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="530" y="60" text-anchor="middle" font-size="16" font-weight="700" fill="#b3261e">Ciphertext</text>
            <text x="530" y="82" text-anchor="middle" font-size="13" fill="#7a1a14">ETUQXPE TAXP</text>

            <line x1="200" y1="65" x2="226" y2="65" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="230,65 218,59 218,71" fill="#7c5cff"/>
            <line x1="410" y1="65" x2="436" y2="65" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="440,65 428,59 428,71" fill="#7c5cff"/>

            <line x1="440" y1="150" x2="200" y2="150" stroke="#3aa876" stroke-width="3" marker-end="url(#arrow)"/>
            <polygon points="200,150 212,144 212,156" fill="#3aa876"/>
            <text x="320" y="175" text-anchor="middle" font-size="13" font-weight="700" fill="#1f6e4c">Decoding: shift back by the same amount to recover the plaintext</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is ciphertext?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The secret key itself</li>
            <li class="quiz-option quiz-correct">B. The scrambled result of encrypting plaintext ✓</li>
            <li class="quiz-option">C. Readable, unencrypted text</li>
            <li class="quiz-option">D. A type of firewall rule</li>
        </ul>
        <p class="quiz-explanation"><em>Ciphertext is what you get after a cipher scrambles the original, readable plaintext.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. A Caesar cipher shifts every letter by 3. What does it do to turn ciphertext back into plaintext?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing, Caesar ciphers can't be undone</li>
            <li class="quiz-option quiz-correct">B. Shifts every letter back by the same 3 places ✓</li>
            <li class="quiz-option">C. Deletes every third letter</li>
            <li class="quiz-option">D. Reverses the whole message</li>
        </ul>
        <p class="quiz-explanation"><em>Decoding a shift cipher just shifts every letter the same distance in the opposite direction.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why is a Caesar cipher considered weak by modern standards?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It only works on numbers</li>
            <li class="quiz-option quiz-correct">B. There are only 25 possible shifts, so every one can be tried in seconds ✓</li>
            <li class="quiz-option">C. It requires a computer to use at all</li>
            <li class="quiz-option">D. It cannot be written down</li>
        </ul>
        <p class="quiz-explanation"><em>A cipher with so few possible keys falls instantly to brute force: trying every option.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Besides trying every shift, what other trick helps crack a Caesar cipher quickly?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Counting how many words the message has</li>
            <li class="quiz-option quiz-correct">B. Counting which letter appears most often, since it's likely "E" in English, and working out the shift from that ✓</li>
            <li class="quiz-option">C. Measuring the file size of the message</li>
            <li class="quiz-option">D. There is no faster trick than trying every shift</li>
        </ul>
        <p class="quiz-explanation"><em>This is frequency analysis: English text has predictable letter frequencies, and a shift cipher doesn't hide that pattern, only relabels it.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why should the crew reply to allies using something stronger than a shift cipher, not another Caesar cipher?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Shift ciphers only work on numbers, not letters</li>
            <li class="quiz-option">B. Stronger ciphers are slower to type</li>
            <li class="quiz-option quiz-correct">C. A shift cipher has so few keys that anyone can break it quickly, while a strong cipher's key space is too large to search ✓</li>
            <li class="quiz-option">D. There is no real difference between ciphers</li>
        </ul>
        <p class="quiz-explanation"><em>Cipher strength comes from the size of the key space: a shift cipher's 25 options fall instantly, a real cipher's key does not.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> decode Vex's intercepted transmission, then reply to the crew's allies using a
        cipher Doom can't casually read. <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c4-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm4', 'lesson' => 'lesson01']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>type</code> and <code>caesar decode &lt;shift&gt; &lt;text&gt;</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
