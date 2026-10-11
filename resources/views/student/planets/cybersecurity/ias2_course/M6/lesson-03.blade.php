{{-- M6 · Lesson 6.3: Acceptable Use Enforcement — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Sections are grouped into stages by heading keywords (Explanation / story / Diagram / Quiz / Defense Lab).
The Defense Lab is the Citadel Sim; passing it (public/citadel-sim/labs/a6-l3.json) completes the lesson.
Syllabus: ITP1333 Unit F.3 Acceptable Use Enforcement. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="03">

    <h1 class="lesson-heading">Lesson 6.3: Acceptable Use Enforcement</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong> detect an acceptable use policy violation and apply the correct enforcement action, distinguishing a first-time reminder from a repeated, deliberate breach.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Writing an acceptable use policy (AUP) is only half the job — <strong>enforcement</strong> is what makes it
        real. Enforcement should fit the violation: a first-time, minor, accidental breach usually calls for a
        reminder and a note in the record. A repeated or deliberate breach — especially one that risks real harm —
        calls for a firmer response: revoking the specific access that was misused, or disabling the account
        outright.
    </p>

    <p class="body-text">
        Consistency matters as much as firmness. Enforcing a rule against one person and quietly ignoring it for
        another erodes the policy faster than having no policy at all.
    </p>

    <h2 class="section-heading">Volt Says</h2>

    <p class="body-text">
        A cadet installs an unauthorized game on a shield-duty console — a first offense, and they remove it the
        moment Volt mentions it. A reminder and a log entry, nothing more. A different cadet does the same thing
        for the third time this month, after two prior warnings. Volt's response is different: revoke their
        console access until they complete a policy refresher. "Same rule," Volt says, "but the response has to
        match the pattern, not just the single act."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 170" role="img" aria-label="A first minor violation gets a reminder; a repeated or deliberate one gets access revoked">
            <rect x="20" y="30" width="150" height="72" rx="14" fill="#eafaf0" stroke="#1f9d55" stroke-width="2"/>
            <text x="95" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#146c3a">First, Minor, Accidental</text>
            <text x="95" y="82" text-anchor="middle" font-size="12.5" fill="#146c3a">reminder + log entry</text>
            <line x1="170" y1="66" x2="462" y2="66" stroke="#7c5cff" stroke-width="2.5"/>
            <polygon points="470,66 458,60 458,72" fill="#7c5cff"/>
            <rect x="470" y="30" width="150" height="72" rx="14" fill="#fdeaea" stroke="#b3261e" stroke-width="2"/>
            <text x="545" y="60" text-anchor="middle" font-size="15.5" font-weight="700" fill="#7a1a14">Repeated or Deliberate</text>
            <text x="545" y="82" text-anchor="middle" font-size="12.5" fill="#7a1a14">revoke access / disable account</text>
            <text x="320" y="154" text-anchor="middle" font-size="13.5" font-weight="700" fill="#4a35b8">Enforcement fits the pattern, not just the single act — applied consistently</text>
        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Why isn't writing an acceptable use policy enough by itself?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because policies are automatically enforced by software</li>
            <li class="quiz-option quiz-correct">B. Because enforcement is what makes the policy real — an unenforced policy is just a suggestion ✓</li>
            <li class="quiz-option">C. Because policies expire after one use</li>
            <li class="quiz-option">D. Because AUPs don't need enforcement, only awareness</li>
        </ul>
        <p class="quiz-explanation"><em>A policy with no enforcement behind it carries no real weight — enforcement is what gives it teeth.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is an appropriate response to a first-time, minor, accidental AUP violation?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Immediately disable the account</li>
            <li class="quiz-option quiz-correct">B. A reminder and a note in the record ✓</li>
            <li class="quiz-option">C. No response at all, ignore it</li>
            <li class="quiz-option">D. Permanent revocation of all access</li>
        </ul>
        <p class="quiz-explanation"><em>A minor first offense, especially one corrected quickly, typically warrants a lighter response than a repeated or deliberate one.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Why did Volt respond differently to the second cadet's third offense?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Because the second cadet was less liked</li>
            <li class="quiz-option quiz-correct">B. Because the enforcement should match the pattern (repeated, after two prior warnings), not just treat it like a first offense ✓</li>
            <li class="quiz-option">C. Because the first cadet's offense wasn't a real violation</li>
            <li class="quiz-option">D. Because Volt was required to escalate every third offense automatically with no judgment</li>
        </ul>
        <p class="quiz-explanation"><em>A repeated violation after prior warnings signals a different situation than an isolated, quickly-corrected mistake, and calls for firmer action.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What erodes an acceptable use policy faster than having no policy at all?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Enforcing it too strictly on a first offense</li>
            <li class="quiz-option quiz-correct">B. Enforcing it against one person while quietly ignoring the same violation from another ✓</li>
            <li class="quiz-option">C. Writing the policy too briefly</li>
            <li class="quiz-option">D. Reviewing the policy too often</li>
        </ul>
        <p class="quiz-explanation"><em>Inconsistent enforcement teaches people the rule doesn't really apply to everyone, undermining it more than no policy would.</em></p>
    </div>

    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What should enforcement fit, according to Volt?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Only the mood of the person enforcing it</li>
            <li class="quiz-option quiz-correct">B. The pattern of the behavior, not just the single act in isolation ✓</li>
            <li class="quiz-option">C. Whichever punishment is easiest to apply</li>
            <li class="quiz-option">D. The cadet's rank, regardless of the violation</li>
        </ul>
        <p class="quiz-explanation"><em>Volt's approach considers history and intent — a repeated, deliberate pattern warrants a different response than an isolated mistake.</em></p>
    </div>

    <h2 class="section-heading">Defense Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> Four AUP violations came in this week. Read them, then decide the correct enforcement for each.
        <strong>Success:</strong> every objective in the simulator is checked off.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="a6-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'information-assurance-security-2', 'module' => 'm6', 'lesson' => 'lesson03']) }}">
            @include('components.cybersecurity-logo')
            <span>Defend it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Citadel Sim full screen. Read the violation log, then classify each one by the
            correct enforcement response.
        </p>
    </div>

</div>
