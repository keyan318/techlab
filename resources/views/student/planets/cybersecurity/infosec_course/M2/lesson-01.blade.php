{{-- M2 · Lesson 2.1: Data-Centric Security — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c2-l1.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="01">

    <h1 class="lesson-heading">Lesson 2.1: Data-Centric Security</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what data-centric security means, classify a file as
        secret or public, and lock a file down so it protects itself even if someone slips past the door.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Most security so far has protected the <strong>door</strong>: a password, a locked account, a closed port.
        <strong>Data-centric security</strong> protects the <strong>data itself</strong>. Instead of only trusting
        that nobody gets in, you make sure that even someone who gets in still can't read what they shouldn't.
    </p>

    <p class="body-text">
        Two steps make this real. First, <strong>classify</strong> your files: decide which are
        <strong>secret</strong> (only the crew who need it should read it) and which are <strong>public</strong>
        (fine for anyone logged in to see). Second, <strong>lock</strong> the secret ones: attach a permission to
        the file so only approved accounts can open it, no matter who else is standing at the console.
    </p>

    <p class="body-text">
        This matters because doors fail. Passwords leak, accounts get captured, and guests wander further than they
        should. A file that protects itself survives a mistake at the door.
    </p>

    <h2 class="section-heading">Volt's Drill</h2>

    <p class="body-text">
        <strong>Volt</strong> flags something during a routine sweep: "We captured a Doom intern's account weeks
        ago, for intel. I just tested it. It can still read files sitting on this console — including the starmap
        copy." <strong>Astro</strong> doesn't like what that means: "So the door was never the only way in. Anyone
        logged in at all could have read it."
    </p>

    <p class="body-text">
        "Astro authorized this," Volt says. "I logged in as <code>doom_intern</code> myself, on our own machine, to
        prove the leak before we fix it. That's the drill: test your own weakness first, so Doom doesn't get to
        test it for you."
    </p>

    <p class="body-text">
        <strong>Astro</strong> gives the order: "Sort what we have. The starmap copy is secret — if Doom gets it,
        they get everything. The crew roster is public — names only, nothing to protect. Classify both, then lock
        the secret one down so `doom_intern` is denied, even while logged in."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 220" role="img" aria-label="Door-only security lets anyone past the door read every file; data-centric security locks the secret file itself, so even doom_intern is denied">
            <text x="150" y="24" text-anchor="middle" font-size="14" font-weight="700" fill="#b3261e">Door-only security</text>
            <rect x="30" y="40" width="110" height="55" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="85" y="72" text-anchor="middle" font-size="13" fill="#7a1a14">doom_intern</text>
            <line x1="140" y1="67" x2="205" y2="67" stroke="#b3261e" stroke-width="3"/>
            <polygon points="210,67 198,61 198,73" fill="#b3261e"/>
            <rect x="215" y="40" width="150" height="55" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="290" y="62" text-anchor="middle" font-size="13" font-weight="700" fill="#8a4b00">starmap-copy.txt</text>
            <text x="290" y="80" text-anchor="middle" font-size="12" fill="#8a4b00">no lock — readable</text>

            <text x="150" y="130" text-anchor="middle" font-size="14" font-weight="700" fill="#1f7a4d">Data-centric security</text>
            <rect x="30" y="146" width="110" height="55" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="85" y="178" text-anchor="middle" font-size="13" fill="#7a1a14">doom_intern</text>
            <line x1="140" y1="173" x2="205" y2="173" stroke="#8a8a8a" stroke-width="3" stroke-dasharray="4 4"/>
            <text x="172" y="163" text-anchor="middle" font-size="16" fill="#b3261e">✕</text>
            <rect x="215" y="146" width="150" height="55" rx="12" fill="#e9f7ef" stroke="#1f7a4d" stroke-width="2"/>
            <text x="290" y="168" text-anchor="middle" font-size="13" font-weight="700" fill="#175a3a">starmap-copy.txt</text>
            <text x="290" y="186" text-anchor="middle" font-size="12" fill="#175a3a">icacls /deny doom_intern</text>

            <rect x="430" y="93" width="180" height="55" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="520" y="115" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">crew-roster.txt</text>
            <text x="520" y="133" text-anchor="middle" font-size="12" fill="#4a35b8">classified public — no lock needed</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does data-centric security protect, that door-only security might miss?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The Wi-Fi password</li>
            <li class="quiz-option quiz-correct">B. The data itself, even from someone already logged in ✓</li>
            <li class="quiz-option">C. The building's front door</li>
            <li class="quiz-option">D. Nothing; it's the same as a strong login</li>
        </ul>
        <p class="quiz-explanation"><em>Data-centric security locks the file itself, so it stays protected even if someone gets past the login.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Volt logs in as the captured `doom_intern` account, on the crew's own machine, with Astro's permission, just to prove a file is readable. What is that called?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A real attack on Doom</li>
            <li class="quiz-option quiz-correct">B. Volt's Drill — authorized testing of the crew's own weakness ✓</li>
            <li class="quiz-option">C. A data leak</li>
            <li class="quiz-option">D. A password reset</li>
        </ul>
        <p class="quiz-explanation"><em>Testing your own systems, with authorization, to find a weakness before an attacker does is ethical hacking — here, Volt's Drill.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. The starmap copy would hand Doom the crew's route home if leaked. How should it be classified?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Secret ✓</li>
            <li class="quiz-option">B. Public</li>
            <li class="quiz-option">C. Deleted</li>
            <li class="quiz-option">D. Unclassified is fine either way</li>
        </ul>
        <p class="quiz-explanation"><em>A file whose exposure would cause real harm gets classified secret, so it can be locked down.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. The crew roster has names only, nothing sensitive. How should it be classified?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Secret</li>
            <li class="quiz-option quiz-correct">B. Public ✓</li>
            <li class="quiz-option">C. It cannot be classified</li>
            <li class="quiz-option">D. Secret, just to be safe, always</li>
        </ul>
        <p class="quiz-explanation"><em>Not every file needs locking. Classifying correctly means the crew doesn't waste effort locking things that carry no real risk.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. After classifying starmap-copy.txt secret, what makes it actually protected from `doom_intern`?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Renaming the file</li>
            <li class="quiz-option">B. Classifying it is enough by itself</li>
            <li class="quiz-option quiz-correct">C. Locking it with a permission that denies that account, e.g. icacls /deny ✓</li>
            <li class="quiz-option">D. Turning off the console</li>
        </ul>
        <p class="quiz-explanation"><em>Classification tells you what a file is; a permission lock is what actually stops the wrong account from reading it.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Volt already proved the leak — the captured `doom_intern` account can read files sitting
        unsorted on the console. Read both files to see what's exposed, classify the starmap copy secret and the
        crew roster public, then lock the starmap copy so `doom_intern` is denied.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c2-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm2', 'lesson' => 'lesson01']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. You type Windows commands there (Command Prompt), such as
            <code>type</code>, <code>classify</code> and <code>icacls</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
