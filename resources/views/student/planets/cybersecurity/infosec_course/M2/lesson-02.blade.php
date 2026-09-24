{{-- M2 · Lesson 2.2: Data Masking — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c2-l2.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m2" data-lesson="02">

    <h1 class="lesson-heading">Lesson 2.2: Data Masking</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what data masking is and why it's used, and redact
        sensitive fields from a file before it travels somewhere it could be intercepted.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Sometimes data has to move: sent to an ally, shared with a vendor, copied for a report. But it might
        cross a channel you don't fully control — and anyone listening on that channel sees whatever you send.
    </p>

    <p class="body-text">
        <strong>Data masking</strong> means hiding the sensitive parts of a file before it goes out, while keeping
        the rest usable. A crew ID number becomes <code>****</code>. An access code becomes <code>****</code>. The
        names and structure stay readable, so allies can still use the file — but the numbers that would actually
        cause harm if intercepted are gone before the file ever leaves.
    </p>

    <p class="body-text">
        Masking is different from locking a file down with a permission (Lesson 2.1). A permission stops the wrong
        <em>account</em> from opening a file at all. Masking assumes the file <em>will</em> be seen by someone
        outside your control, and strips out the part that matters most before that happens.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        <strong>Rivet</strong> has a job to do: "Codexia's allied fleet wants our crew list, so they know who's
        aboard if we need rescue. Problem is, the only channel open right now runs straight past a relay Doom has
        been tapping." <strong>Astro</strong> doesn't want to cancel the transfer — the allies need the list — but
        he doesn't want Doom reading crew IDs and access codes either.
    </p>

    <p class="body-text">
        Rivet's answer: "We don't stop sending. We stop sending the part that matters. Names, sure, they can have
        those — that's how they'll know who's aboard. Crew IDs and access codes are what Doom could actually use.
        Mask those, and Doom's intercepted copy is just stars — nothing they can act on."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="crew-export.csv passes through a hostile channel; before masking, Doom reads full IDs and codes; after masking, Doom's intercepted copy shows only stars">
            <rect x="20" y="30" width="170" height="60" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="105" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#14306b">crew-export.csv</text>
            <text x="105" y="73" text-anchor="middle" font-size="11.5" fill="#14306b">4471-2290, 998877 …</text>

            <line x1="190" y1="60" x2="250" y2="60" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="254,60 242,54 242,66" fill="#7c5cff"/>
            <rect x="258" y="30" width="120" height="60" rx="12" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="318" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#7a1a14">Doom relay</text>
            <text x="318" y="73" text-anchor="middle" font-size="11" fill="#7a1a14">listening</text>
            <line x1="378" y1="60" x2="438" y2="60" stroke="#8a8a8a" stroke-width="3" stroke-dasharray="4 4"/>
            <rect x="442" y="30" width="180" height="60" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="532" y="55" text-anchor="middle" font-size="13" font-weight="700" fill="#8a4b00">Doom sees</text>
            <text x="532" y="73" text-anchor="middle" font-size="11.5" fill="#8a4b00">full crew IDs — usable</text>

            <text x="20" y="122" font-size="13" font-weight="700" fill="#1f7a4d">After masking:</text>
            <rect x="20" y="135" width="170" height="55" rx="12" fill="#e9f7ef" stroke="#1f7a4d" stroke-width="2"/>
            <text x="105" y="158" text-anchor="middle" font-size="13" font-weight="700" fill="#175a3a">crew-export.masked.csv</text>
            <text x="105" y="176" text-anchor="middle" font-size="11.5" fill="#175a3a">****, **** …</text>
            <line x1="190" y1="162" x2="438" y2="162" stroke="#7c5cff" stroke-width="3"/>
            <polygon points="442,162 430,156 430,168" fill="#7c5cff"/>
            <rect x="442" y="135" width="180" height="55" rx="12" fill="#e9f7ef" stroke="#1f7a4d" stroke-width="2"/>
            <text x="532" y="158" text-anchor="middle" font-size="13" font-weight="700" fill="#175a3a">Doom sees</text>
            <text x="532" y="176" text-anchor="middle" font-size="11.5" fill="#175a3a">only stars — useless</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is data masking?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Deleting a file so nobody can send it</li>
            <li class="quiz-option quiz-correct">B. Redacting sensitive parts of data before it's shared or transmitted ✓</li>
            <li class="quiz-option">C. Renaming a file to hide its purpose</li>
            <li class="quiz-option">D. Encrypting an entire hard drive</li>
        </ul>
        <p class="quiz-explanation"><em>Masking hides the sensitive fields while keeping the rest of the data usable for whoever legitimately receives it.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why does Rivet mask the crew list instead of just canceling the transfer?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Masking is faster than sending the real file</li>
            <li class="quiz-option quiz-correct">B. The allies still need the list; masking removes only the parts Doom could actually use ✓</li>
            <li class="quiz-option">C. It makes the file smaller</li>
            <li class="quiz-option">D. It's required by the AUP</li>
        </ul>
        <p class="quiz-explanation"><em>Masking lets legitimate data sharing continue while removing exactly the information an interceptor could exploit.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. In the crew export, which fields get masked?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The crew names, so nobody knows who's aboard</li>
            <li class="quiz-option quiz-correct">B. Crew IDs and access codes — the numbers Doom could actually use ✓</li>
            <li class="quiz-option">C. The file's date</li>
            <li class="quiz-option">D. Nothing; the whole file is deleted instead</li>
        </ul>
        <p class="quiz-explanation"><em>Names help the allies confirm who's aboard; IDs and access codes are what an attacker could actually exploit, so those are masked.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. How is masking different from Lesson 2.1's file lock (icacls /deny)?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They're exactly the same thing</li>
            <li class="quiz-option quiz-correct">B. A lock stops the wrong account from opening a file; masking assumes the file will be seen and strips the sensitive part first ✓</li>
            <li class="quiz-option">C. Masking is only for images</li>
            <li class="quiz-option">D. A lock is used for sending data, masking is used for storing it</li>
        </ul>
        <p class="quiz-explanation"><em>Locking controls who can open a file at all; masking protects data that's expected to travel or be seen by an outside party.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. If Doom intercepts the masked file, what do they get?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The full crew IDs and access codes</li>
            <li class="quiz-option">B. Nothing at all, the file is empty</li>
            <li class="quiz-option quiz-correct">C. Names and structure, but the IDs and codes replaced with stars — nothing usable ✓</li>
            <li class="quiz-option">D. A virus</li>
        </ul>
        <p class="quiz-explanation"><em>That's the point of masking: the file still looks like a crew list, but the part an attacker could act on is gone.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Doom is listening on the only channel open to send the crew list to Codexia's
        allies. Read the export file to see what's exposed, then mask it so the crew IDs and access codes are
        redacted before it goes out. <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c2-l2" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm2', 'lesson' => 'lesson02']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. You type Windows commands there (Command Prompt), such as
            <code>type</code> and <code>mask</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
