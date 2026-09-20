{{-- M1 · Lesson 1.5: Servers &amp; Virtualization — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim; passing it (public/netsim-app/labs/m1-l5.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m1" data-lesson="05">

    <h1 class="lesson-heading">Lesson 1.5: Servers &amp; Virtualization</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain the client-server model, name common server roles, and describe how virtualization lets one machine act as many.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        Most network conversations are <strong>client-server</strong>. A <strong>client</strong> asks (a browser wants a page), and a <strong>server</strong> answers. A server is simply a machine, or a program, that waits for requests and replies.
    </p>
    <p class="body-text">
        Common server roles: a <strong>web server</strong> serves pages, a <strong>DNS server</strong> turns names into IP addresses, a <strong>DHCP server</strong> hands out addresses, a <strong>file server</strong> stores shared files, and a <strong>mail server</strong> moves email. One machine can hold several roles at once.
    </p>
    <p class="body-text">
        <strong>Virtualization</strong> takes this further. A piece of software called a <strong>hypervisor</strong> splits one physical computer into several <strong>virtual machines (VMs)</strong>. Each VM runs its own operating system and has its own virtual NIC and IP address, so on the network it looks like a separate computer.
    </p>
    <p class="body-text">
        Why it matters: fewer machines to buy and power, workloads kept apart from each other, and a VM can be copied or restored in minutes. Common tools are VirtualBox, VMware, Hyper-V and KVM.
    </p>

    <h2 class="section-heading">Rivet's Briefing</h2>

    <p class="body-text">
        <strong>Rivet</strong> drags the only working server out of the wreckage. "One box. I need a web page, a name service and a mail relay."
    </p>
    <p class="body-text">
        <strong>Astro</strong> raises an eyebrow. "That is three machines." Rivet grins. "Not with virtualization. One server, three jobs, and each one thinks it has the machine to itself."
    </p>
    <p class="body-text">
        The simulator has no full virtual machines, but the idea is the same: one server, several services at once.
    </p>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 250" role="img" aria-label="One physical server running three virtual machines, serving clients">
            <rect x="150" y="196" width="300" height="44" rx="12" fill="#eceff7" stroke="#6b7699" stroke-width="2"/>
            <text x="300.0" y="223.0" text-anchor="middle" font-size="15" font-weight="700" fill="#33406b">Physical server hardware</text>
            <rect x="150" y="146" width="300" height="42" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="300.0" y="164.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">Hypervisor</text>
            <text x="300.0" y="183.0" text-anchor="middle" font-size="12.5" fill="#3b2a8a">software that splits the machine</text>
            <rect x="150" y="60" width="92" height="78" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="196.0" y="96.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14306b">Web VM</text>
            <text x="196.0" y="115.0" text-anchor="middle" font-size="12.5" fill="#14306b">own OS + IP</text>
            <rect x="254" y="60" width="92" height="78" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="300.0" y="96.0" text-anchor="middle" font-size="15" font-weight="700" fill="#14663f">DNS VM</text>
            <text x="300.0" y="115.0" text-anchor="middle" font-size="12.5" fill="#14663f">own OS + IP</text>
            <rect x="358" y="60" width="92" height="78" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="404.0" y="96.0" text-anchor="middle" font-size="15" font-weight="700" fill="#6b3a00">Mail VM</text>
            <text x="404.0" y="115.0" text-anchor="middle" font-size="12.5" fill="#6b3a00">own OS + IP</text>
            <rect x="520" y="100" width="120" height="50" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="580.0" y="122.0" text-anchor="middle" font-size="15" font-weight="700" fill="#3b2a8a">Clients</text>
            <text x="580.0" y="141.0" text-anchor="middle" font-size="12.5" fill="#3b2a8a">astro, rivet, volt</text>
            <line x1="464" y1="60" x2="464" y2="240" stroke="#7c5cff" stroke-width="2" stroke-linecap="round"/>
            <line x1="464" y1="125" x2="520" y2="125" stroke="#7c5cff" stroke-width="3" stroke-linecap="round"/>
            <text x="300" y="34" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">One machine, many servers</text>

        </svg>
    </figure>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. In the client-server model, what does a server do?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Waits for requests and answers them ✓</li>
            <li class="quiz-option">B. Sends requests to clients</li>
            <li class="quiz-option">C. Only stores cables</li>
            <li class="quiz-option">D. Only shows pictures</li>
        </ul>
        <p class="quiz-explanation"><em>The client asks and the server answers. A server waits for requests and replies.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. What is a hypervisor?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A very fast cable</li>
            <li class="quiz-option quiz-correct">B. Software that splits one machine into several virtual machines ✓</li>
            <li class="quiz-option">C. A kind of firewall</li>
            <li class="quiz-option">D. A server that only serves email</li>
        </ul>
        <p class="quiz-explanation"><em>The hypervisor is the layer that creates and runs virtual machines on one physical computer.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. Which is a benefit of virtualization?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Every VM needs its own building</li>
            <li class="quiz-option quiz-correct">B. One physical machine can do the work of several ✓</li>
            <li class="quiz-option">C. It removes the need for networks</li>
            <li class="quiz-option">D. It makes cables faster</li>
        </ul>
        <p class="quiz-explanation"><em>Several VMs share one physical machine, which saves hardware and power and keeps workloads separate.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> bring <code>ship-server</code> online as a web server, then give it a second job as a name server. <strong>Success:</strong> astro fetches the page by IP address, and then by the name <code>codexia.lan</code>.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="m1-l5" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson05']) }}">
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
