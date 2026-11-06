{{-- M5 · Lesson 5.1: Classification & Marking: DSCP and CoS — content fragment, no <html>/<head>/<body>.
Swapped into #lesson-stage by course-player.blade.php's fetch() logic.
Stages come from the headings: Explanation / crew voice / Diagram / Quiz / Relay Lab.
The Relay Lab is the Relay Console, a Cisco-style router console; passing it (public/relay-console/labs/g5-l1.json) completes the lesson. --}}

<div class="lesson-fragment" data-module="m5" data-lesson="01">

    <h1 class="lesson-heading">Lesson 5.1: Classification & Marking: DSCP and CoS</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can explain why networks need QoS, mark traffic with DSCP and CoS values, and build a marking policy with class-maps and policy-maps.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        When a link is full, packets wait in a queue, and some get dropped. A file download barely notices. A voice call breaks up: voice needs low <strong>delay</strong> (under about 150 ms one way), low <strong>jitter</strong> (steady delay) and almost no <strong>loss</strong>. <strong>Quality of Service</strong> (QoS) gives important traffic better treatment when links are busy. It can't create bandwidth; it only decides who waits.
    </p>
    <p class="body-text">
        Step one is to <strong>classify</strong> traffic (decide what each packet is: by protocol, port, address) and <strong>mark</strong> it, so every later device can just read the mark. Two marking fields:
    </p>
    <ul class="body-list">
        <li><strong>CoS</strong> (Class of Service): 3 bits in the 802.1Q tag, values 0-7. Layer 2, so it only exists on trunks and disappears at the first router. Voice is usually CoS 5.</li>
        <li><strong>DSCP</strong> (Differentiated Services Code Point): 6 bits in the IP header, values 0-63. It travels end to end. Common values: <strong>EF</strong> (46, expedited forwarding) for voice, <strong>AF41</strong> (34, assured forwarding) for video, <strong>CS1</strong> (8, scavenger) for bulk traffic that can wait, and <strong>0</strong> (best effort) for the rest.</li>
    </ul>
    <p class="body-text">
        Mark as close to the source as possible, at the <strong>trust boundary</strong>: the point where the network stops believing marks set by end devices (a PC could mark everything EF). Cisco builds QoS in three pieces, the <strong>MQC</strong> (Modular QoS CLI):
    </p>
    <pre class="code-block"><code>class-map match-any VOICE
 match protocol rtp audio
policy-map MARK
 class VOICE
  set dscp ef
interface g0/1
 service-policy input MARK</code></pre>
    <p class="body-text">
        The <strong>class-map</strong> says <em>what</em> traffic, the <strong>policy-map</strong> says <em>what to do</em> with each class, and <strong>service-policy</strong> says <em>where</em> and in which direction. <code>show policy-map interface g0/1</code> shows how many packets each class matched.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">Why does a CoS mark disappear at the first router?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. It lives in the 802.1Q tag, at Layer 2 ✓</li>
            <li class="quiz-option">B. It is only 6 bits long in the IP header itself</li>
            <li class="quiz-option">C. Routers reset every mark to EF 46</li>
            <li class="quiz-option">D. It is only used for bulk traffic</li>
        </ul>
        <p class="quiz-explanation"><em>CoS is 3 bits in the 802.1Q tag. Tags exist only on trunks, so CoS is lost at a router. DSCP travels end to end.</em></p>
    </div>

    <h2 class="section-heading">Volt's Warning</h2>

    <p class="body-text">
        Doom's jammers are back over Northwatch, and the ridge's only link to the Citadel is a 10 Mbps radio. Then Earth's nightly backup starts, fills the radio, and Northwatch's voice channel turns to noise in the middle of a patrol report. <strong>Volt</strong> slams the desk. "A file copy just drowned out a distress call."
    </p>
    <p class="body-text">
        "The network treats every packet the same," says <strong>Astro</strong>. "We have to tell it which ones matter. Label them first. The queues come next lesson."
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">What drowned out Northwatch's voice channel?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. A Doom jammer alone</li>
            <li class="quiz-option quiz-correct">B. Earth's nightly backup ✓</li>
            <li class="quiz-option">C. A broken radio mast</li>
            <li class="quiz-option">D. Too many voice calls at once</li>
        </ul>
        <p class="quiz-explanation"><em>The backup filled the 10 Mbps radio, and the network treated every packet the same, so voice broke up.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 660 290" role="img" aria-label="An IP header&#x27;s DSCP field and an 802.1Q tag&#x27;s CoS bits, with the standard markings for voice, video and bulk traffic">
            <rect x="10" y="15" width="310" height="110" rx="12" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/>
            <text x="165.0" y="49.6" text-anchor="middle" font-size="14" font-weight="700" fill="#14306b">Layer 2: CoS</text>
            <text x="165.0" y="66.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">3 bits in the 802.1Q tag (0-7)</text>
            <text x="165.0" y="82.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">only on trunks, lost at a router</text>
            <text x="165.0" y="99.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#14306b">voice = CoS 5</text>
            <rect x="340" y="15" width="310" height="110" rx="12" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
            <text x="495.0" y="49.6" text-anchor="middle" font-size="14" font-weight="700" fill="#3b2a8a">Layer 3: DSCP</text>
            <text x="495.0" y="66.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">6 bits in the IP header (0-63)</text>
            <text x="495.0" y="82.7" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">travels end to end</text>
            <text x="495.0" y="99.2" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3b2a8a">voice = EF (46)</text>
            <rect x="10" y="145" width="152" height="56" rx="12" fill="#ffe8ea" stroke="#e5484d" stroke-width="2"/>
            <text x="86.0" y="168.4" text-anchor="middle" font-size="14" font-weight="700" fill="#7a1820">EF  46</text>
            <text x="86.0" y="184.4" text-anchor="middle" font-size="11" font-weight="500" fill="#7a1820">voice: never wait</text>
            <rect x="172" y="145" width="152" height="56" rx="12" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="248.0" y="168.4" text-anchor="middle" font-size="14" font-weight="700" fill="#6b3a00">AF41  34</text>
            <text x="248.0" y="184.4" text-anchor="middle" font-size="11" font-weight="500" fill="#6b3a00">video</text>
            <rect x="334" y="145" width="152" height="56" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="410.0" y="168.4" text-anchor="middle" font-size="14" font-weight="700" fill="#3a4352">0  BE</text>
            <text x="410.0" y="184.4" text-anchor="middle" font-size="11" font-weight="500" fill="#3a4352">best effort</text>
            <rect x="496" y="145" width="152" height="56" rx="12" fill="#e6f7ee" stroke="#2fa56b" stroke-width="2"/>
            <text x="572.0" y="168.4" text-anchor="middle" font-size="14" font-weight="700" fill="#14663f">CS1  8</text>
            <text x="572.0" y="184.4" text-anchor="middle" font-size="11" font-weight="500" fill="#14663f">scavenger: bulk</text>
            <rect x="10" y="220" width="640" height="60" rx="12" fill="#f1f3f7" stroke="#8a94a6" stroke-width="2"/>
            <text x="330.0" y="245.0" text-anchor="middle" font-size="13.5" font-weight="700" fill="#3a4352">MQC: class-map (what) → policy-map (do what) → service-policy (where)</text>
            <text x="330.0" y="261.5" text-anchor="middle" font-size="11.5" font-weight="500" fill="#3a4352">mark once, at the trust boundary, as traffic enters</text>

        </svg>
    </figure>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">In the diagram, which DSCP marking is used for voice?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. AF41 34</li>
            <li class="quiz-option">B. CS1 8</li>
            <li class="quiz-option quiz-correct">C. EF 46 ✓</li>
            <li class="quiz-option">D. BE 0</li>
        </ul>
        <p class="quiz-explanation"><em>Voice gets EF (46, expedited forwarding). AF41 is for video and CS1 is scavenger bulk traffic.</em></p>
    </div>

    <h2 class="section-heading">Quiz</h2>

    {{-- DRAFT quiz — written for review, edit freely. --}}
    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. Which three things does voice traffic need from the network?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Lots of bandwidth, encryption, compression</li>
            <li class="quiz-option quiz-correct">B. Low delay, low jitter and very low loss ✓</li>
            <li class="quiz-option">C. A dedicated VLAN, a VPN and NAT</li>
            <li class="quiz-option">D. Only high bandwidth</li>
        </ul>
        <p class="quiz-explanation"><em>Voice uses little bandwidth but is ruined by delay, uneven delay (jitter) and lost packets.</em></p>
    </div>
    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Which marking survives across routers end to end?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. CoS, in the 802.1Q tag</li>
            <li class="quiz-option quiz-correct">B. DSCP, in the IP header ✓</li>
            <li class="quiz-option">C. The MAC address</li>
            <li class="quiz-option">D. The VLAN number</li>
        </ul>
        <p class="quiz-explanation"><em>CoS lives in the Layer 2 tag and is gone after the first router. DSCP is in the IP header and travels the whole way.</em></p>
    </div>
    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What DSCP marking is standard for voice?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. AF41 (34)</li>
            <li class="quiz-option">B. CS1 (8)</li>
            <li class="quiz-option quiz-correct">C. EF (46) ✓</li>
            <li class="quiz-option">D. 0</li>
        </ul>
        <p class="quiz-explanation"><em>Expedited Forwarding, DSCP 46, is the standard marking for real-time voice.</em></p>
    </div>
    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. In the MQC, what does the class-map define?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Where the policy applies</li>
            <li class="quiz-option quiz-correct">B. Which traffic belongs to a class ✓</li>
            <li class="quiz-option">C. How much bandwidth to give</li>
            <li class="quiz-option">D. The VLAN</li>
        </ul>
        <p class="quiz-explanation"><em>class-map = what traffic; policy-map = what to do with it; service-policy = where to apply it.</em></p>
    </div>
    <div class="quiz-block" data-q="5">
        <p class="quiz-prompt">Q5. Why mark at a trust boundary instead of trusting every PC's marks?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. PCs can't mark</li>
            <li class="quiz-option quiz-correct">B. Any PC could mark all its traffic EF and jump the queue ✓</li>
            <li class="quiz-option">C. Routers ignore marks</li>
            <li class="quiz-option">D. Marks slow PCs down</li>
        </ul>
        <p class="quiz-explanation"><em>The network decides at its edge which marks to believe, and re-marks the rest.</em></p>
    </div>

    <h2 class="section-heading">Relay Lab</h2>

    <p class="body-text">
        <strong>Mission:</strong> match each Northwatch flow to its marking, build the class-maps and the MARK policy, and apply it where crew traffic enters. <strong>Success:</strong> voice leaves Northwatch marked EF and video AF41.
    </p>

    <div class="cta-wrap relay-lab-cta" data-lab="g5-l1" style="margin-top:1.5rem;">
        <a class="code-btn" href="{{ route('student.planet.lab', ['slug' => 'networking-2', 'module' => 'm5', 'lesson' => 'lesson01']) }}">
            @include('components.networking-logo')
            <span>Configure it yourself</span>
            <span class="code-btn-arrow" aria-hidden="true">→</span>
        </a>
        <p class="body-text relay-lab-note">
            Opens the Relay Console full screen: a Cisco-style router prompt. Commands can be shortened like on a real router (<code>sh ip int br</code>, <code>conf t</code>), and <code>?</code> lists what works. The objectives tick as you go.
        </p>
    </div>

</div>
