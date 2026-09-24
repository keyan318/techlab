{{-- M4 · Lesson 4.4: Digital Rights Management — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / Astro / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/c4-l4.json) completes the lesson.
Syllabus: ITP1232 Unit D.4 Digital Rights Management. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="04">

    <h1 class="lesson-heading">Lesson 4.4: Digital Rights Management</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain what Digital Rights Management (DRM) is for, what a
        watermark does, and use one to prove a leaked copy of a file came from a specific original.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Digital Rights Management (DRM)</strong> is any technique used to control how a digital work
        (software, a document, a design, media) can be copied, shared or used, so the creator's rights over
        it are respected. One common DRM technique is a <strong>watermark</strong>: a hidden or subtle mark
        embedded in a file that doesn't change how it works, but proves where it came from.
    </p>

    <p class="body-text">
        A watermark turns "I think this was stolen" into "I can prove this was stolen": if the exact same
        hidden mark shows up in a copy you never authorized, that copy is traceable back to the original,
        no matter how many times it's been passed around.
    </p>

    <h2 class="section-heading">Rivet Says</h2>

    <p class="body-text">
        17:40. <strong>Rivet</strong> spots something circulating on a Doom relay: a spec sheet for the crew's
        engine, word for word. "That's mine. I designed that engine."
    </p>

    <p class="body-text">
        <strong>Astro</strong> asks the obvious question: "Can you prove it?" <strong>Rivet</strong> grins.
        "Every blueprint I ship has a watermark hidden in it. If it's really my design, it'll be right there
        in the leaked copy too — Doom copied the mark along with everything else, because they didn't know it
        was there."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 200" role="img" aria-label="The original blueprint and the leaked copy both contain the same hidden watermark, proving the leaked copy came from the original">
            <rect x="30" y="30" width="230" height="90" rx="14" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="145" y="58" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Original blueprint</text>
            <text x="145" y="80" text-anchor="middle" font-size="12.5" fill="#14306b">Rivet's file, licensed</text>
            <rect x="60" y="90" width="170" height="20" rx="6" fill="#c9d8ff"/>
            <text x="145" y="105" text-anchor="middle" font-size="11" fill="#14306b">watermark: WMK-RIVET-7734</text>

            <rect x="380" y="30" width="230" height="90" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="495" y="58" text-anchor="middle" font-size="15" font-weight="700" fill="#b3261e">Leaked copy</text>
            <text x="495" y="80" text-anchor="middle" font-size="12.5" fill="#7a1a14">found on a Doom relay</text>
            <rect x="410" y="90" width="170" height="20" rx="6" fill="#f6c9c9"/>
            <text x="495" y="105" text-anchor="middle" font-size="11" fill="#7a1a14">watermark: WMK-RIVET-7734</text>

            <line x1="260" y1="75" x2="376" y2="75" stroke="#7c5cff" stroke-width="3" stroke-dasharray="6,4"/>
            <text x="320" y="150" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Same hidden mark in both = the leaked copy is proven to be Rivet's</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is Digital Rights Management (DRM)?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A type of firewall</li>
            <li class="quiz-option quiz-correct">B. Techniques used to control how a digital work can be copied, shared or used ✓</li>
            <li class="quiz-option">C. A password strength requirement</li>
            <li class="quiz-option">D. A backup schedule</li>
        </ul>
        <p class="quiz-explanation"><em>DRM covers any method that protects a creator's control over how their digital work is used and distributed.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What does a watermark do in a file?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It deletes the file after one use</li>
            <li class="quiz-option quiz-correct">B. It embeds a hidden mark that proves where a copy came from ✓</li>
            <li class="quiz-option">C. It encrypts the entire file so no one can open it</li>
            <li class="quiz-option">D. It blocks the file from being copied at all</li>
        </ul>
        <p class="quiz-explanation"><em>A watermark doesn't stop copying — it proves origin after the fact, even in a copy that's been passed around.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Rivet found the same watermark in both the original blueprint and the leaked copy. What does that prove?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing, watermarks can appear in any file</li>
            <li class="quiz-option quiz-correct">B. The leaked copy traces back to Rivet's original file ✓</li>
            <li class="quiz-option">C. Doom created an entirely new blueprint</li>
            <li class="quiz-option">D. The watermark was added after the leak</li>
        </ul>
        <p class="quiz-explanation"><em>A matching hidden watermark is strong proof of origin, since the copier had no way to know it was there to remove.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Why is a watermark usually hidden or subtle, rather than a big visible label?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Visible labels are illegal</li>
            <li class="quiz-option quiz-correct">B. A hidden mark survives being copied by someone who doesn't know to remove it ✓</li>
            <li class="quiz-option">C. Hidden marks make the file load faster</li>
            <li class="quiz-option">D. It doesn't matter, visible and hidden work the same way</li>
        </ul>
        <p class="quiz-explanation"><em>If a mark is obvious, a copier can strip it out; a hidden one is far more likely to travel along with every copy.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What should the crew do once they classify the leaked file as "stolen" and confirm the shared watermark?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Ignore it, since nothing can be done once a file is leaked</li>
            <li class="quiz-option">B. Delete the original so no one can compare copies</li>
            <li class="quiz-option quiz-correct">C. Treat it as proven theft and act on that evidence, since the watermark ties the copy directly to the original ✓</li>
            <li class="quiz-option">D. Assume the watermark is a coincidence</li>
        </ul>
        <p class="quiz-explanation"><em>A verified matching watermark is real evidence, turning suspicion of theft into something the crew can act on.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> compare Rivet's original blueprint to the leaked copy, and prove the theft using
        the hidden watermark. <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="c4-l4" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'cybersecurity', 'module' => 'm4', 'lesson' => 'lesson04']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen: a training copy of the Citadel's main computer. You type Windows commands there
            (Command Prompt), such as <code>type</code>, <code>classify</code> and <code>submit</code>. Objectives tick off as you go.
        </p>
    </div>

</div>
