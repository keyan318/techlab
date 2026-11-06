{{-- M6 · Lesson 6.1: Firewalls: Stateless, Stateful & NGFW — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is NetSim with Cisco IOS routers and switches; passing it (resources/netsim-app/public/labs/g6-l1.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m6" data-lesson="01">

    <h1 class="lesson-heading">Lesson 6.1: Firewalls: Stateless, Stateful & NGFW</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can write and apply an IOS access list, explain why a stateless filter blocks replies, and compare stateless, stateful and next-generation firewalls.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A <strong>firewall</strong> decides which packets may cross a boundary. They come in three generations:
    </p>
    <ul class="body-list">
        <li><strong>Stateless packet filters</strong>: look at each packet on its own (addresses, protocol, ports) against a list of rules. Fast and simple, but blind to conversations. A router <strong>access control list</strong> (ACL) is one.</li>
        <li><strong>Stateful firewalls</strong>: keep a <strong>connection table</strong>. When someone inside opens a connection, the reply is recognised and let back in automatically; anything nobody asked for is blocked.</li>
        <li><strong>Next-generation firewalls</strong> (NGFW): stateful, plus they recognise <em>applications</em> and users (not just port numbers), inspect content, include intrusion prevention, and use live threat intelligence.</li>
    </ul>
    <p class="body-text">
        An IOS <strong>extended ACL</strong> matches protocol, source, destination and ports. It is read <strong>top-down</strong>, the <strong>first match wins</strong>, and a packet that matches nothing hits the invisible <strong>deny any</strong> at the end. Apply it to an interface and a direction with <code>ip access-group</code>. Extended ACLs go close to the source of the traffic you want to stop.
    </p>
    <pre class="code-block"><code>access-list 110 permit tcp any host 192.168.20.80 eq 80
access-list 110 permit tcp any 192.168.20.0 0.0.0.255 established
interface g0/1
 ip access-group 110 in</code></pre>
    <p class="body-text">
        The stateless trap: an ACL that only lets the web port in also blocks the <em>replies</em> to the crew's own connections, because those come back to random high ports. The keyword <code>established</code> lets in TCP packets with the ACK or RST flag set, which replies always have. It's a workaround: an attacker can fake those flags, which is why stateful firewalls exist.
    </p>
    <p class="body-text">
        Order matters. To put a rule <em>before</em> existing ones, edit the list by sequence number: <code>ip access-list extended 110</code>, then <code>5 deny ip host 203.0.113.66 any</code>. <code>show access-lists</code> shows each rule and how many packets it matched.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">What does a stateful firewall keep that a stateless one does not?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. A connection table ✓</li>
            <li class="quiz-option">B. A routing table</li>
            <li class="quiz-option">C. A MAC address list</li>
            <li class="quiz-option">D. A DHCP lease pool</li>
        </ul>
        <p class="quiz-explanation"><em>A stateful firewall remembers each connection, so replies are let back in automatically.</em></p>
    </div>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        Commander Vex is back. Overnight, a host at 203.0.113.66 tried every door of the Citadel's web server: SSH, remote desktop, then hundreds of web requests. <strong>Volt</strong> pins the log to the wall. "Our edge router lets anything in. Only the web page should be open to the outside."
    </p>
    <p class="body-text">
        "A router can filter," says <strong>Rivet</strong>, "but it has a short memory. It forgets every packet as soon as it's checked. Watch what that does to our own traffic."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What does Rivet mean by a router's “short memory”?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It loses its whole config after every reboot</li>
            <li class="quiz-option">B. It can only hold ten rules in an ACL</li>
            <li class="quiz-option quiz-correct">C. It forgets each packet once it is checked ✓</li>
            <li class="quiz-option">D. It can't remember the web server's IP</li>
        </ul>
        <p class="quiz-explanation"><em>A stateless ACL checks each packet alone, so replies to the crew's own connections need their own rule.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 300" role="img" aria-label="A stateless access list checks each packet alone; a stateful firewall remembers connections and lets replies back in">
            <rect x="10" y="15" width="310" height="125" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="165.0" y="49.4" text-anchor="middle" font-size="14" font-weight="700" fill="#6b3a00">Stateless (ACL)</text>
            <text x="165.0" y="65.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">checks each packet on its own</text>
            <text x="165.0" y="82.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">top-down, first match wins</text>
            <text x="165.0" y="98.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">invisible &quot;deny any&quot; at the end</text>
            <text x="165.0" y="115.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#6b3a00">replies need their own rule</text>
            <rect x="340" y="15" width="310" height="125" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="495.0" y="49.4" text-anchor="middle" font-size="14" font-weight="700" fill="#14663f">Stateful</text>
            <text x="495.0" y="65.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">remembers each connection</text>
            <text x="495.0" y="82.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">(the connection table)</text>
            <text x="495.0" y="98.9" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">replies are allowed automatically</text>
            <text x="495.0" y="115.4" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14663f">unrequested traffic is not</text>
            <rect x="10" y="160" width="640" height="60" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="330.0" y="185.2" text-anchor="middle" font-size="14" font-weight="700" fill="#3b2a8a">Next-generation firewall (NGFW)</text>
            <text x="330.0" y="201.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">stateful + knows applications and users + intrusion prevention + threat intelligence</text>
            <rect x="10" y="235" width="200" height="50" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="110.0" y="264.4" text-anchor="middle" font-size="12.5" font-weight="700" fill="#7a1820">5 deny ip host Doom</text>
            <rect x="225" y="235" width="210" height="50" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="330.0" y="264.2" text-anchor="middle" font-size="12" font-weight="700" fill="#14306b">10 permit tcp any web eq 80</text>
            <rect x="450" y="235" width="200" height="50" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="550.0" y="264.4" text-anchor="middle" font-size="12.5" font-weight="700" fill="#14306b">20 permit ... established</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, what is at the end of every ACL?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A rule that permits all</li>
            <li class="quiz-option">B. A rule to log each packet</li>
            <li class="quiz-option">C. A rule for all replies</li>
            <li class="quiz-option quiz-correct">D. An invisible “deny any” ✓</li>
        </ul>
        <p class="quiz-explanation"><em>An ACL is read top-down, first match wins, and a packet that matches nothing hits the invisible deny any.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What makes a firewall stateful?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It has more rules</li>
            <li class="quiz-option quiz-correct">B. It keeps a table of connections and allows their replies automatically ✓</li>
            <li class="quiz-option">C. It runs on a router</li>
            <li class="quiz-option">D. It blocks everything</li>
        </ul>
        <p class="quiz-explanation"><em>A stateful firewall remembers outgoing connections and recognises the replies that belong to them.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. An ACL has no rule matching a packet. What happens to it?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. It is permitted</li>
            <li class="quiz-option quiz-correct">B. It is denied by the invisible deny any at the end ✓</li>
            <li class="quiz-option">C. It is logged only</li>
            <li class="quiz-option">D. It is sent to the CPU</li>
        </ul>
        <p class="quiz-explanation"><em>Every ACL ends with an implicit deny any.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. After applying an inbound ACL that only permits the web port, the crew can't browse Earth. Why?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. DNS is down</li>
            <li class="quiz-option quiz-correct">B. Replies to the crew's own connections are stateless packets with no matching rule ✓</li>
            <li class="quiz-option">C. The ACL is outbound</li>
            <li class="quiz-option">D. Earth blocked them</li>
        </ul>
        <p class="quiz-explanation"><em>A stateless ACL doesn't know the replies belong to the crew's connections, so the implicit deny drops them.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. What does <code>established</code> match in a TCP ACL entry?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Only new connections</li>
            <li class="quiz-option quiz-correct">B. Packets with the ACK or RST flag set, like replies ✓</li>
            <li class="quiz-option">C. UDP replies</li>
            <li class="quiz-option">D. Encrypted traffic</li>
        </ul>
        <p class="quiz-explanation"><em>It matches TCP segments with ACK or RST set, which lets replies in. Attackers can fake those flags.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. What does a next-generation firewall add to stateful filtering?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Nothing</li>
            <li class="quiz-option quiz-correct">B. Application and user awareness, intrusion prevention and threat intelligence ✓</li>
            <li class="quiz-option">C. Faster cables</li>
            <li class="quiz-option">D. VLANs</li>
        </ul>
        <p class="quiz-explanation"><em>An NGFW identifies applications regardless of port, ties traffic to users, and inspects content for threats.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> filter the Citadel's edge with an access list, fix the replies it breaks, and shut Vex's scanner out completely. <strong>Success:</strong> Earth reaches the web page, the crew reaches Earth, and 203.0.113.66 reaches nothing.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g6-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm6', 'lesson' => 'lesson01']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the simulator full screen. Routers and switches speak Cisco IOS: double-click one to open its console (<code>enable</code>, then <code>configure terminal</code>). The PCs use Windows commands. Press <strong>Check objectives</strong> after each step.
        </p>
    </div>

</div>
