{{-- M7 · Lesson 7.3: Backups: Strategies, Scheduling & Storage — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a7-l3.json) completes the lesson.
Syllabus: ITP1333 Unit G.3 Backups (Strategies, Scheduling, Testing, and Storage). --}}

<div class="lesson-fragment" data-module="m7" data-lesson="03">

    <h1 class="lesson-heading">Lesson 7.3: Backups: Strategies, Scheduling & Storage</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> apply the 3-2-1 backup rule and restore a system from a tested backup, and explain why an untested backup cannot be trusted.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A simple, durable rule for backups: <strong>3-2-1</strong>. Keep at least <strong>3</strong> copies of
        important data, on <strong>2</strong> different types of storage media, with <strong>1</strong> copy kept
        offsite — so a single disaster (a fire, a single failed drive, a single compromised system) can't destroy
        every copy at once.
    </p>

    <p class="body-text">
        None of that matters if the backup has never been restored and checked. A backup that "should work" is not
        the same as a backup that has actually been tested — corrupted backups, silently failing backup jobs, and
        incompatible formats are all common, and all invisible until the moment you actually need the restore to
        work.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does the 1 in the 3-2-1 rule mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. One copy kept offsite ✓</li>
            <li class="quiz-option">B. One backup made per year</li>
            <li class="quiz-option">C. One type of storage media</li>
            <li class="quiz-option">D. One person who has the key</li>
        </ul>
        <p class="quiz-explanation"><em>3 copies, 2 media types, 1 offsite, so a single disaster can't destroy every copy.</em></p>
    </div>

    <h2 class="section-heading">Rivet's Workbench</h2>

    <p class="body-text">
        Rivet proudly reports the starmap backup runs every night. Volt asks the harder question: "When did we last
        restore it and confirm it actually matches the original?" Rivet has no answer. They restore it together —
        and find the backup job has been silently failing for two weeks. "An untested backup," Volt says, "is a
        guess wearing a backup's uniform."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What did Rivet and Volt find when they restored the backup?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The backup matched the original exactly</li>
            <li class="quiz-option">B. The restore deleted the original starmap</li>
            <li class="quiz-option quiz-correct">C. The job had been failing for two weeks ✓</li>
            <li class="quiz-option">D. The backup ran three times every night</li>
        </ul>
        <p class="quiz-explanation"><em>An untested backup is a guess. The job had been silently failing for two weeks.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="The 3-2-1 backup rule: three copies, two media types, one offsite">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#eaf2fd" stroke="#2f7de1" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#1c4f8f">3 Copies</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#1c4f8f">no single loss wipes everything</text>
            <line x1="170" y1="66" x2="237" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="245,66 233,60 233,72" fill="#7c5cff"/>
            <rect x="245" y="30" width="150" height="72" rx="14" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="320" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#4a35b8">2 Media Types</text>
            <text x="320" y="82" text-anchor="middle" font-size="12.5" fill="#4a35b8">not all copies fail the same way</text>
            <line x1="395" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">1 Offsite</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">survives a local disaster</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">3-2-1 — and none of it counts until the restore has actually been tested</text>
        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, why keep two media types?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. They make every backup go faster</li>
            <li class="quiz-option">B. One is for the starmap only</li>
            <li class="quiz-option">C. It halves the storage cost</li>
            <li class="quiz-option quiz-correct">D. They don't fail the same way ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Different media don't all fail the same way, so one fault can't wipe every copy.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What does the 3-2-1 backup rule specify?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. 3 backups a day, 2 people responsible, 1 report filed</li>
            <li class="quiz-option quiz-correct">B. 3 copies of data, on 2 different types of media, with 1 copy offsite ✓</li>
            <li class="quiz-option">C. 3 offsite locations, 2 backups a year, 1 password</li>
            <li class="quiz-option">D. A rule that only applies to physical documents</li>
        </ul>
        <p class="quiz-explanation"><em>3-2-1 spreads risk across multiple copies, media types, and locations so no single disaster destroys everything.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Why does the rule require at least one copy offsite?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Offsite copies are always faster to restore</li>
            <li class="quiz-option quiz-correct">B. So a local disaster (fire, single-site failure) can't destroy every copy at once ✓</li>
            <li class="quiz-option">C. Offsite storage is required by tradition only</li>
            <li class="quiz-option">D. Because onsite storage is illegal</li>
        </ul>
        <p class="quiz-explanation"><em>An offsite copy survives an event that destroys everything at the primary location.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Volt ask when the starmap backup was last restored and tested, rather than just how often it runs?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because Volt didn't trust Rivet personally</li>
            <li class="quiz-option quiz-correct">B. Because a backup job running is not the same as a backup that has been confirmed to actually restore correctly ✓</li>
            <li class="quiz-option">C. Because backup frequency doesn't matter at all</li>
            <li class="quiz-option">D. Because testing backups is only necessary once, ever</li>
        </ul>
        <p class="quiz-explanation"><em>A running backup job can still be silently corrupted or failing — only an actual test restore confirms it works.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What did Rivet and Volt discover when they actually tested the restore?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The backup was perfect, as expected</li>
            <li class="quiz-option quiz-correct">B. The backup job had been silently failing for two weeks ✓</li>
            <li class="quiz-option">C. There was no backup job at all</li>
            <li class="quiz-option">D. The offsite copy was missing</li>
        </ul>
        <p class="quiz-explanation"><em>The untested backup turned out to have been broken all along — invisible until someone actually tried to use it.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does Volt mean by "an untested backup is a guess wearing a backup's uniform"?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Untested backups always work fine in practice</li>
            <li class="quiz-option quiz-correct">B. It looks like a backup and is treated like one, but without testing there's no actual confidence it would work ✓</li>
            <li class="quiz-option">C. Backups should never be tested, to avoid wearing them out</li>
            <li class="quiz-option">D. Backups need a physical uniform to be valid</li>
        </ul>
        <p class="quiz-explanation"><em>Appearances (a scheduled job running) can mask an unreliable backup — only testing confirms real confidence.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> The starmap backup has never been test-restored. Restore it and confirm it actually matches the original.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a7-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm7', 'lesson' => 'lesson03']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Restore the backup over the working copy, then confirm the
            restore actually matches.
        </p>
    </div>

</div>
