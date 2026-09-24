{{-- M5 · Lesson 5.5: Cryptanalysis & Strong Cryptography — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c5-l5.json) completes the lesson.
Syllabus: ITP1232 Unit E, Cryptanalysis & Strong Cryptography. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="05">

    <h1 class="lesson-heading">Lesson 5.5: Cryptanalysis & Strong Cryptography</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what cryptanalysis is, why a shift cipher is weak enough to
        break by trial, and what makes a cipher strong enough to resist that kind of attack.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Cryptanalysis</strong> is the practice of breaking ciphers — finding a weakness that lets you
        recover the plaintext without already knowing the key. A shift cipher (like the Caesar cipher from Lesson
        4.1) is a favorite target, because it only has 25 possible shifts. An attacker doesn't need to be clever:
        they can just try every shift, or count which letter appears most often in the ciphertext and guess it
        stands for "E," the most common letter in English. Either way, the cipher falls in minutes.
    </p>

    <p class="body-text">
        A <strong>strong cipher</strong> doesn't have that weakness. Instead of 25 possible keys, it has an
        astronomically large key space, so trying every key isn't remotely practical — and it doesn't leak
        patterns the way a simple shift does. The lesson isn't "never use a shift cipher": it's that a cipher's
        strength has to match what it's protecting. A puzzle for a cadet is not a wall for a Citadel.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        15:15. Doom's codebreakers are testing the crew's old comms cipher — the same shift cipher from the
        intercepted transmission back in Lesson 4.1. <strong>Volt</strong> isn't surprised. "A shift cipher again?
        They're not even trying, and honestly, they don't have to. Let's break their message the same way they'd
        break ours, so we know exactly how exposed we've been."
    </p>

    <p class="body-text">
        <strong>Rivet</strong> is already working on the fix. "A shift cipher only has 25 keys. Ours needs a real
        one — something with a key space too large to brute-force and no obvious letter patterns to count. Once
        we're on it, the same trick that broke Vex's cipher won't touch ours."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="A weak shift cipher with only 25 keys falls to trial and letter counting, while a strong cipher's huge key space resists the same attack">
            <rect x="20" y="20" width="280" height="80" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="160" y="50" text-anchor="middle" font-size="15" font-weight="700" fill="#7a1a14">shift cipher</text>
            <text x="160" y="72" text-anchor="middle" font-size="12.5" fill="#7a1a14">25 possible keys — try them all</text>
            <text x="160" y="90" text-anchor="middle" font-size="12.5" fill="#7a1a14">falls in minutes</text>

            <rect x="340" y="20" width="280" height="80" rx="14" fill="#eafaf0" stroke="#1e8e3e" stroke-width="2"/>
            <text x="480" y="50" text-anchor="middle" font-size="15" font-weight="700" fill="#0d5c26">strong cipher</text>
            <text x="480" y="72" text-anchor="middle" font-size="12.5" fill="#0d5c26">huge key space — no shortcut</text>
            <text x="480" y="90" text-anchor="middle" font-size="12.5" fill="#0d5c26">resists the same attack</text>

            <text x="320" y="140" text-anchor="middle" font-size="14" font-weight="700" fill="#4a35b8">cryptanalysis: finding the weakness that skips needing the key</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is cryptanalysis?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Writing new ciphers from scratch</li>
            <li class="quiz-option quiz-correct">B. Breaking ciphers by finding a weakness, without already knowing the key ✓</li>
            <li class="quiz-option">C. Hashing a file to detect tampering</li>
            <li class="quiz-option">D. Signing a message with a private key</li>
        </ul>
        <p class="quiz-explanation"><em>Cryptanalysis is the practice of breaking ciphers, typically by exploiting a weakness rather than guessing the key directly.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why does a shift cipher fall so easily?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It doesn't actually change the letters</li>
            <li class="quiz-option quiz-correct">B. It only has 25 possible shifts, so trying them all is fast ✓</li>
            <li class="quiz-option">C. It requires a password to even read</li>
            <li class="quiz-option">D. It can only encode numbers, not letters</li>
        </ul>
        <p class="quiz-explanation"><em>With only 25 possible keys, an attacker can simply try every one until the plaintext reads as English.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Besides trying every shift, what's another way to break a shift cipher?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Guessing the recipient's name</li>
            <li class="quiz-option quiz-correct">B. Counting which letter appears most often and guessing it stands for "E" ✓</li>
            <li class="quiz-option">C. Waiting for the sender to explain it</li>
            <li class="quiz-option">D. It cannot be broken any other way</li>
        </ul>
        <p class="quiz-explanation"><em>Letter-frequency analysis uses the fact that some letters (like "E" in English) appear far more often than others, exposing the shift.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What makes a cipher "strong" against this kind of attack?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It uses longer words</li>
            <li class="quiz-option quiz-correct">B. A key space too large to try every key, with no obvious pattern to exploit ✓</li>
            <li class="quiz-option">C. It never gets used twice</li>
            <li class="quiz-option">D. It's written by a trusted crew member</li>
        </ul>
        <p class="quiz-explanation"><em>A strong cipher removes the shortcut: too many possible keys to brute-force, and no telltale pattern like letter frequency to exploit.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What's the real lesson about using a shift cipher at all?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Shift ciphers should never be used for anything</li>
            <li class="quiz-option quiz-correct">B. A cipher's strength has to match what it's protecting ✓</li>
            <li class="quiz-option">C. Any cipher is fine as long as it's used twice</li>
            <li class="quiz-option">D. Strong ciphers are only needed for passwords</li>
        </ul>
        <p class="quiz-explanation"><em>A shift cipher might be fine for a puzzle, but real Citadel traffic needs a cipher whose key space actually resists an attacker.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> break Doom's weak shift-cipher intercept the same way an attacker would, then
        upgrade the crew's own traffic to a cipher that same trick can't touch.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c5-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm5', 'lesson' => 'lesson05']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>caesar</code> and <code>xor</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
