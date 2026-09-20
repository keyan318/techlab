{{-- M4 · Lesson 4.3: Logical Issues — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m4-l3.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m4" data-lesson="03">

    <h1 class="lesson-heading">Lesson 4.3: Logical Issues</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can name the common logical (configuration) faults, use the results of simple tests to tell them apart, and fix them in order.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        <strong>Logical issues</strong> are configuration and protocol problems above the cable: the hardware works, but the settings or services are wrong. The usual suspects:
    </p>
    <ul class="body-list">
        <li><strong>Wrong IP address or mask</strong>: the device is on the wrong network.</li>
        <li><strong>Missing or wrong default gateway</strong>, or missing routes: the device cannot leave its network.</li>
        <li><strong>Duplicate IP address</strong>: two devices fight over one address, causing random drops.</li>
        <li><strong>Wrong DNS server</strong>: names fail while numbers work.</li>
        <li><strong>VLAN mismatch</strong>: a port is in the wrong virtual network.</li>
        <li><strong>DHCP problems</strong>: no lease, wrong scope, or missing options.</li>
        <li><strong>Firewall or ACL</strong> blocking a port, or a <strong>service that is not running</strong>.</li>
    </ul>
    <p class="body-text">
        The trick is to test in a <strong>ladder</strong>, from near to far. Each rung tells you where to look: you can reach the gateway but not a remote address, so suspect routing; you can reach a remote address by number but not by name, so suspect DNS; you can reach the host but not the service, so suspect the service or a firewall.
    </p>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        <strong>Volt</strong> reads the configs and sees Doom's fingerprints. "Someone has been in the settings. Astro's address is on the wrong network, the gateway is gone, and the name server points at nothing. Three small edits, and the whole console is cut off."
    </p>
    <p class="body-text">
        <strong>Astro</strong> stays calm. "Then we peel them off one at a time, nearest first. Fix, test, move on."
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 248" role="img" aria-label="A ladder of tests for logical network problems">
            <text x="170" y="16" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">Test, in this order</text>
            <text x="500" y="16" text-anchor="middle" font-size="13.5" font-weight="700" fill="#14306b">If it fails, suspect</text>
            <rect x="10" y="28" width="320" height="34" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="170.0" y="50.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">1   ip addr, ping your own address</text>
            <line x1="330" y1="45" x2="366" y2="45" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="372,45 361,39 361,51" fill="#7c5cff"/>
            <rect x="376" y="28" width="274" height="34" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="513.0" y="50.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">NIC or IP setup</text>
            <rect x="10" y="70" width="320" height="34" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="170.0" y="92.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">2   ping the default gateway</text>
            <line x1="330" y1="87" x2="366" y2="87" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="372,87 361,81 361,93" fill="#7c5cff"/>
            <rect x="376" y="70" width="274" height="34" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="513.0" y="92.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">wrong address or mask, VLAN, cable</text>
            <rect x="10" y="112" width="320" height="34" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="170.0" y="134.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">3   ping a remote IP address</text>
            <line x1="330" y1="129" x2="366" y2="129" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="372,129 361,123 361,135" fill="#7c5cff"/>
            <rect x="376" y="112" width="274" height="34" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="513.0" y="134.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">gateway, routes, the far end</text>
            <rect x="10" y="154" width="320" height="34" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="170.0" y="176.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">4   ping or dig the name</text>
            <line x1="330" y1="171" x2="366" y2="171" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="372,171 361,165 361,177" fill="#7c5cff"/>
            <rect x="376" y="154" width="274" height="34" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="513.0" y="176.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">DNS setting or DNS server</text>
            <rect x="10" y="196" width="320" height="34" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="170.0" y="218.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">5   open the service (curl)</text>
            <line x1="330" y1="213" x2="366" y2="213" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <polygon points="372,213 361,207 361,219" fill="#7c5cff"/>
            <rect x="376" y="196" width="274" height="34" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="513.0" y="218.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">service down or firewall</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. You can ping 203.0.113.10 but not earth.lan. What is the most likely problem?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The cable is loose</li>
            <li class="quiz-option quiz-correct">B. DNS ✓</li>
            <li class="quiz-option">C. The switch is broken</li>
            <li class="quiz-option">D. The IP address is a duplicate</li>
        </ul>
        <p class="quiz-explanation"><em>A name that fails while the number works points at DNS: the client's DNS setting or the DNS server.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. You can ping the gateway but not a remote IP address. Where do you look?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. The default route and routing on the path ✓</li>
            <li class="quiz-option">B. The link light on your own cable</li>
            <li class="quiz-option">C. The SSID</li>
            <li class="quiz-option">D. The DHCP lease time</li>
        </ul>
        <p class="quiz-explanation"><em>Local traffic works and remote traffic does not. Suspect the default gateway, routes, or the far end's return route.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What problem do two devices with the same IP address cause?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A faster connection</li>
            <li class="quiz-option quiz-correct">B. Random or intermittent connectivity for one or both ✓</li>
            <li class="quiz-option">C. Nothing at all</li>
            <li class="quiz-option">D. A blown fuse</li>
        </ul>
        <p class="quiz-explanation"><em>Both devices answer for the same address, so traffic goes to the wrong one at different times.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> peel off three stacked faults in order. <strong>Success:</strong> astro reaches the local server, then Earth's server by number, then opens <code>http://earth.lan/</code> by name.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m4-l3" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm4', 'lesson' => 'lesson03']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. You use Linux commands there; the ideas are the same on Cisco gear.
            Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
