<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Course Structure Blueprint
    |--------------------------------------------------------------------------
    |
    | This array defines the structure of the programming course with modules
    | and lessons. Each module contains lessons with their metadata.
    |
    | Structure:
    |   Each planet also has 'view_base': the view folder its lesson files live in
    |   (<view_base>.M1.lesson-01). A lesson may set 'lab' => '<lab-id>': it is then
    |   completed by passing that hands-on simulator lab instead of a coding challenge.
    |
    |   [
    |       'module_id' => [
    |           'title' => 'Module Title',
    |           'lessons' => [
    |               'lesson_id' => [
    |                   'title' => 'Lesson Title',
    |                   'slug' => 'lesson-slug',
    |                   'order' => 1,
    |                   'view' => 'folder.path.to.view',
    |                   'type' => 'lesson' // or 'quiz' or 'project'
    |               ],
    |               // ... more lessons
    |           ]
    |       ],
    |       // ... more modules
    |   ]
    |
    */

    'programming' => [
        'title' => 'Python: Zero to Automation',
        'view_base' => 'student.planets.programming.python_course',
        'modules' => [
            'M1' => [
                'title' => 'M1 — Python Foundations',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'First Signal',
                        'slug' => 'first-signal',
                        'expected' => 'Astro to Codexia: comms online.',
                        'order' => 1,
                        'view' => 'student.programming.M1.lesson-01',
                        'type' => 'lesson',
                    ],
                    'lesson-02' => [
                        'title' => 'Variables & Memory',
                        'slug' => 'variables-memory',
                        'expected' => '42',
                        'order' => 2,
                        'view' => 'student.programming.M1.lesson-02',
                        'type' => 'lesson',
                    ],
                    'lesson-03' => [
                        'title' => 'Data Types',
                        'slug' => 'data-types',
                        'expected' => '20',
                        'order' => 3,
                        'view' => 'student.programming.M1.lesson-03',
                        'type' => 'lesson',
                    ],
                    'lesson-04' => [
                        'title' => 'Expressions & Operators',
                        'slug' => 'expressions-operators',
                        'expected' => '20.0',
                        'order' => 4,
                        'view' => 'student.programming.M1.lesson-04',
                        'type' => 'lesson',
                    ],
                    'lesson-05' => [
                        'title' => 'Talking to the Program',
                        'slug' => 'talking-program',
                        'expected' => "Astro's crew needs 8 ration packs",
                        'order' => 5,
                        'view' => 'student.programming.M1.lesson-05',
                        'type' => 'lesson',
                    ],
                    'lesson-06' => [
                        'title' => 'Reading Error Messages',
                        'slug' => 'reading-errors',
                        'expected' => '97',
                        'order' => 6,
                        'view' => 'student.programming.M1.lesson-06',
                        'type' => 'lesson',
                    ],
                ],
            ],
            'M2' => [
                'title' => 'M2 — Conditions & Loops',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Decision Points',
                        'slug' => 'decision-points',
                        'order' => 1,
                        'view' => 'student.programming.M2.lesson-01',
                        'type' => 'lesson',
                    ],
                    'lesson-02' => [
                        'title' => 'Branching Paths',
                        'slug' => 'branching-paths',
                        'order' => 2,
                        'view' => 'student.programming.M2.lesson-02',
                        'type' => 'lesson',
                    ],
                    'lesson-03' => [
                        'title' => 'Combining Conditions',
                        'slug' => 'combining-conditions',
                        'order' => 3,
                        'view' => 'student.programming.M2.lesson-03',
                        'type' => 'lesson',
                    ],
                    'lesson-04' => [
                        'title' => 'Repeating Signals',
                        'slug' => 'repeating-signals',
                        'order' => 4,
                        'view' => 'student.programming.M2.lesson-04',
                        'type' => 'lesson',
                    ],
                    'lesson-05' => [
                        'title' => 'Repeating With for',
                        'slug' => 'counting-loops',
                        'order' => 5,
                        'view' => 'student.programming.M2.lesson-05',
                        'type' => 'lesson',
                    ],
                    'lesson-06' => [
                        'title' => 'Breaking the Loop',
                        'slug' => 'breaking-loop',
                        'order' => 6,
                        'view' => 'student.programming.M2.lesson-06',
                        'type' => 'lesson',
                    ],
                ],
            ],
            'M3' => [
                'title' => 'M3 — Functions & Error Handling',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Reusable Routines',
                        'slug' => 'reusable-routines',
                        'order' => 1,
                        'view' => 'student.programming.M3.lesson-01',
                        'type' => 'lesson',
                    ],
                    'lesson-02' => [
                        'title' => 'Passing Information',
                        'slug' => 'passing-information',
                        'order' => 2,
                        'view' => 'student.programming.M3.lesson-02',
                        'type' => 'lesson',
                    ],
                    'lesson-03' => [
                        'title' => 'Returning Results',
                        'slug' => 'scope',
                        'order' => 3,
                        'view' => 'student.programming.M3.lesson-03',
                        'type' => 'lesson',
                    ],
                    'lesson-04' => [
                        'title' => 'Anticipating Failure',
                        'slug' => 'anticipating-failure',
                        'order' => 4,
                        'view' => 'student.programming.M3.lesson-04',
                        'type' => 'lesson',
                    ],
                    'lesson-05' => [
                        'title' => 'Sanity Checks',
                        'slug' => 'sanity-checks',
                        'order' => 5,
                        'view' => 'student.programming.M3.lesson-05',
                        'type' => 'lesson',
                    ],
                ],
            ],
            'M4' => [
                'title' => 'M4 — Working With Data & Files',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Collections',
                        'slug' => 'collections',
                        'order' => 1,
                        'view' => 'student.programming.M4.lesson-01',
                        'type' => 'lesson',
                    ],
                    'lesson-02' => [
                        'title' => 'List Operations',
                        'slug' => 'list-operations',
                        'order' => 2,
                        'view' => 'student.programming.M4.lesson-02',
                        'type' => 'lesson',
                    ],
                    'lesson-03' => [
                        'title' => 'Labeled Data',
                        'slug' => 'labeled-data',
                        'order' => 3,
                        'view' => 'student.programming.M4.lesson-03',
                        'type' => 'lesson',
                    ],
                    'lesson-04' => [
                        'title' => 'Nested Structures',
                        'slug' => 'nested-structures',
                        'order' => 4,
                        'view' => 'student.programming.M4.lesson-04',
                        'type' => 'lesson',
                    ],
                    'lesson-05' => [
                        'title' => 'Text Manipulation',
                        'slug' => 'text-manipulation',
                        'order' => 5,
                        'view' => 'student.programming.M4.lesson-05',
                        'type' => 'lesson',
                    ],
                    'lesson-06' => [
                        'title' => 'Reading Files',
                        'slug' => 'reading-files',
                        'order' => 6,
                        'view' => 'student.programming.M4.lesson-06',
                        'type' => 'lesson',
                    ],
                    'lesson-07' => [
                        'title' => 'Writing Files',
                        'slug' => 'writing-files',
                        'order' => 7,
                        'view' => 'student.programming.M4.lesson-07',
                        'type' => 'lesson',
                    ],
                    'lesson-08' => [
                        'title' => 'Structured Data Formats',
                        'slug' => 'structured-data',
                        'order' => 8,
                        'view' => 'student.programming.M4.lesson-08',
                        'type' => 'lesson',
                    ],
                ],
            ],
            'M5' => [
                'title' => 'M5 — Python Automation',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Pattern Matching',
                        'slug' => 'pattern-matching',
                        'order' => 1,
                        'view' => 'student.programming.M5.lesson-01',
                        'type' => 'lesson',
                    ],
                    'lesson-02' => [
                        'title' => 'Practical Regex',
                        'slug' => 'practical-regex',
                        'order' => 2,
                        'view' => 'student.programming.M5.lesson-02',
                        'type' => 'lesson',
                    ],
                    'lesson-03' => [
                        'title' => 'File System Basics',
                        'slug' => 'file-system-basics',
                        'order' => 3,
                        'view' => 'student.programming.M5.lesson-03',
                        'type' => 'lesson',
                    ],
                    'lesson-04' => [
                        'title' => 'Building a CLI Tool',
                        'slug' => 'building-cli',
                        'order' => 4,
                        'view' => 'student.programming.M5.lesson-04',
                        'type' => 'lesson',
                    ],
                    'lesson-05' => [
                        'title' => 'Time & Scheduling Concepts',
                        'slug' => 'time-scheduling',
                        'order' => 5,
                        'view' => 'student.programming.M5.lesson-05',
                        'type' => 'lesson',
                    ],
                ],
            ],
            'M6' => [
                'title' => 'M6 — Real-World Projects',
                'lessons' => [
                    'lesson-01' => [
                        'title' => '6A: Web Basics',
                        'slug' => 'web-basics',
                        'order' => 1,
                        'view' => 'student.programming.M6.lesson-01',
                        'type' => 'lesson',
                    ],
                    'lesson-02' => [
                        'title' => '6B: Spreadsheet Automation',
                        'slug' => 'spreadsheet-automation',
                        'order' => 2,
                        'view' => 'student.programming.M6.lesson-02',
                        'type' => 'lesson',
                    ],
                    'lesson-03' => [
                        'title' => '6C: Mini Database',
                        'slug' => 'mini-database',
                        'order' => 3,
                        'view' => 'student.programming.M6.lesson-03',
                        'type' => 'lesson',
                    ],
                    'lesson-04' => [
                        'title' => '6D: Document Automation',
                        'slug' => 'document-automation',
                        'order' => 4,
                        'view' => 'student.programming.M6.lesson-04',
                        'type' => 'lesson',
                    ],
                ],
            ],
            'M7' => [
                'title' => 'M7 — Final Automation Project',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Proposal & Design',
                        'slug' => 'proposal-design',
                        'order' => 1,
                        'view' => 'student.programming.M7.lesson-01',
                        'type' => 'lesson',
                    ],
                ],
            ],
        ],
    ],

    'networking' => [
        'title' => 'Networking 1',
        'resources' => [
            [
                'title' => 'Networking 1 (ITP422) OBE Course Syllabus — Catanduanes State University',
                'url' => '/resources/networking-1-syllabus-ITP422.pdf',
                'type' => 'pdf',
                'source' => 'Source: Catanduanes State University, OBE Course Syllabus (CatSU-F-ACAD-07, AY 2026–2027)',
            ],
        ],
        'view_base' => 'student.planets.networking.net_course',
        'modules' => [
            'M1' => [
                'title' => 'M1 — Networking Basics',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'First Contact',
                        'lab' => 'm1-l1',
                    ],
                    'lesson-02' => [
                        'title' => 'Ship\'s Wiring',
                        'lab' => 'm1-l2',
                    ],
                    'lesson-03' => [
                        'title' => 'Cables & Signals',
                        'lab' => 'm1-l3',
                    ],
                    'lesson-04' => [
                        'title' => 'Switches, Routers & VLANs',
                        'lab' => 'm1-l4',
                    ],
                    'lesson-05' => [
                        'title' => 'Servers & Virtualization',
                        'lab' => 'm1-l5',
                    ],
                    'lesson-06' => [
                        'title' => 'Cloud Relay',
                        'lab' => 'm1-l6',
                    ],
                ],
            ],
            'M2' => [
                'title' => 'M2 — Networking Protocols',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Protocol Models',
                        'lab' => 'm2-l1',
                    ],
                    'lesson-02' => [
                        'title' => 'Protocols & Standards',
                        'lab' => 'm2-l2',
                    ],
                    'lesson-03' => [
                        'title' => 'IPv4 & IPv6',
                        'lab' => 'm2-l3',
                    ],
                    'lesson-04' => [
                        'title' => 'IP Addresses',
                        'lab' => 'm2-l4',
                    ],
                    'lesson-05' => [
                        'title' => 'Subnetting',
                        'lab' => 'm2-l5',
                    ],
                    'lesson-06' => [
                        'title' => 'Routing Tables & Static Routes',
                        'lab' => 'm2-l6',
                    ],
                    'lesson-07' => [
                        'title' => 'Midterm Mission',
                        'lab' => 'm2-l7',
                    ],
                ],
            ],
            'M3' => [
                'title' => 'M3 — TCP/IP Services',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Static & Dynamic Addressing',
                        'lab' => 'm3-l1',
                    ],
                    'lesson-02' => [
                        'title' => 'The DHCP Lease Process',
                        'lab' => 'm3-l2',
                    ],
                    'lesson-03' => [
                        'title' => 'Centralized DHCP',
                        'lab' => 'm3-l3',
                    ],
                    'lesson-04' => [
                        'title' => 'DHCP Server Settings',
                        'lab' => 'm3-l4',
                    ],
                    'lesson-05' => [
                        'title' => 'DNS Overview',
                        'lab' => 'm3-l5',
                    ],
                ],
            ],
            'M4' => [
                'title' => 'M4 — Network Management & Troubleshooting',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Network Management',
                        'lab' => 'm4-l1',
                    ],
                    'lesson-02' => [
                        'title' => 'Physical Issues',
                        'lab' => 'm4-l2',
                    ],
                    'lesson-03' => [
                        'title' => 'Logical Issues',
                        'lab' => 'm4-l3',
                    ],
                    'lesson-04' => [
                        'title' => 'Wireless Issues',
                        'lab' => 'm4-l4',
                    ],
                    'lesson-05' => [
                        'title' => 'Troubleshooting Method',
                        'lab' => 'm4-l5',
                    ],
                    'lesson-06' => [
                        'title' => 'Final Mission: Call Earth',
                        'lab' => 'm4-l6',
                    ],
                ],
            ],
        ],
    ],

    // Networking 2 (ITP831): "Light the Grid", Season 2 of Networking 1's "Call Earth".
    // Earth answered; now the crew links Codexia's outposts into one planet-wide network that keeps
    // running under Planet Doom's attacks. Second course on the Networking Nebula, keyed by its own
    // catalog id. `sim` picks the lab: netsim = NetSim with Cisco IOS routers/switches
    // (resources/netsim-app/public/labs/g*.json); console = Relay Console, a Cisco-style router console
    // for what a packet simulator can't show, like BGP, QoS or VPNs (public/relay-console/labs/g*.json).
    'networking-2' => [
        'title' => 'Networking 2',
        'planet' => 'networking',
        'catalog' => 'networking-2',
        'view_base' => 'student.planets.networking.net2_course',
        'resources' => [
            [
                'title' => 'Networking 2 (ITP831) OBE Course Syllabus — Catanduanes State University',
                'url' => '/resources/networking-2-syllabus-ITP831.pdf',
                'type' => 'pdf',
                'source' => 'Source: Catanduanes State University, OBE Course Syllabus (CatSU-F-ACAD-07, AY 2025–2026, 2nd Semester)',
            ],
            [
                'title' => 'Fundamentals of Communications and Networking, Third Edition',
                'type' => 'book',
                'source' => 'Solomon M. & Kim D. (2022), Jones and Bartlett Learning',
            ],
            [
                'title' => 'Networking All-in-One For Dummies, 8th Edition',
                'type' => 'book',
                'source' => 'Lowe D. (2021), John Wiley & Sons',
            ],
            [
                'title' => 'Fundamentals of Computer Networks',
                'type' => 'book',
                'source' => 'Sadiku M. & Akujuobi C. (2022), Springer Nature Switzerland',
            ],
            [
                'title' => 'Computer Networking Beginners Guide',
                'type' => 'book',
                'source' => 'Axel R. (2022)',
            ],
            [
                'title' => 'OSPF Version 2 (RFC 2328)',
                'url' => 'https://www.rfc-editor.org/rfc/rfc2328',
                'type' => 'web',
                'source' => 'IETF',
            ],
            [
                'title' => 'A Border Gateway Protocol 4 (RFC 4271)',
                'url' => 'https://www.rfc-editor.org/rfc/rfc4271',
                'type' => 'web',
                'source' => 'IETF',
            ],
            [
                'title' => 'IP Version 6 Addressing Architecture (RFC 4291)',
                'url' => 'https://www.rfc-editor.org/rfc/rfc4291',
                'type' => 'web',
                'source' => 'IETF',
            ],
        ],
        'modules' => [
            'M1' => [
                'title' => 'M1 — Back Online: Networking Review',
                'lessons' => [
                    'lesson-01' => ['title' => 'Switches, Routers & VLANs Revisited', 'lab' => 'g1-l1', 'sim' => 'netsim'],
                    'lesson-02' => ['title' => 'Cloud Computing in Networking', 'lab' => 'g1-l2', 'sim' => 'console'],
                    'lesson-03' => ['title' => 'IPv6 (and the IPv5 That Never Was)', 'lab' => 'g1-l3', 'sim' => 'console'],
                ],
            ],
            'M2' => [
                'title' => 'M2 — Advanced Routing',
                'lessons' => [
                    'lesson-01' => ['title' => 'Link-State Routing with OSPF', 'lab' => 'g2-l1', 'sim' => 'netsim'],
                    'lesson-02' => ['title' => 'OSPF Areas & LSA Types', 'lab' => 'g2-l2', 'sim' => 'netsim'],
                    'lesson-03' => ['title' => 'OSPF Configuration & Optimization', 'lab' => 'g2-l3', 'sim' => 'netsim'],
                    'lesson-04' => ['title' => 'BGP: eBGP vs iBGP', 'lab' => 'g2-l4', 'sim' => 'console'],
                    'lesson-05' => ['title' => 'BGP Path Selection, Communities & Filtering', 'lab' => 'g2-l5', 'sim' => 'console'],
                    'lesson-06' => ['title' => 'EIGRP: Metrics, Load Balancing & Convergence', 'lab' => 'g2-l6', 'sim' => 'console'],
                ],
            ],
            'M3' => [
                'title' => 'M3 — Network Design Patterns',
                'lessons' => [
                    'lesson-01' => ['title' => 'Hierarchical Design: Core, Distribution & Access', 'lab' => 'g3-l1', 'sim' => 'netsim'],
                    'lesson-02' => ['title' => 'Scalability & Redundancy Planning', 'lab' => 'g3-l2', 'sim' => 'console'],
                    'lesson-03' => ['title' => 'Redundant Paths & Convergence', 'lab' => 'g3-l3', 'sim' => 'netsim'],
                    'lesson-04' => ['title' => 'First Hop Redundancy: HSRP, VRRP & GLBP', 'lab' => 'g3-l4', 'sim' => 'netsim'],
                    'lesson-05' => ['title' => 'Midterm Mission: The Northwatch Storm', 'lab' => 'g3-l5', 'sim' => 'netsim'],
                ],
            ],
            'M4' => [
                'title' => 'M4 — Advanced Switching & VLANs',
                'lessons' => [
                    'lesson-01' => ['title' => 'VLAN Trunking & 802.1Q', 'lab' => 'g4-l1', 'sim' => 'netsim'],
                    'lesson-02' => ['title' => 'Inter-VLAN Routing: Router-on-a-Stick vs Layer 3 Switch', 'lab' => 'g4-l2', 'sim' => 'netsim'],
                    'lesson-03' => ['title' => 'Spanning Tree: RSTP & MST', 'lab' => 'g4-l3', 'sim' => 'netsim'],
                    'lesson-04' => ['title' => 'PortFast, BPDU Guard & Root Guard', 'lab' => 'g4-l4', 'sim' => 'netsim'],
                    'lesson-05' => ['title' => 'Port Security & 802.1X', 'lab' => 'g4-l5', 'sim' => 'netsim'],
                    'lesson-06' => ['title' => 'VLAN Security Best Practices', 'lab' => 'g4-l6', 'sim' => 'netsim'],
                ],
            ],
            'M5' => [
                'title' => 'M5 — Quality of Service & Traffic Management',
                'lessons' => [
                    'lesson-01' => ['title' => 'Classification & Marking: DSCP and CoS', 'lab' => 'g5-l1', 'sim' => 'console'],
                    'lesson-02' => ['title' => 'Policing, Shaping & Queuing', 'lab' => 'g5-l2', 'sim' => 'console'],
                    'lesson-03' => ['title' => 'MPLS & Traffic Engineering', 'lab' => 'g5-l3', 'sim' => 'console'],
                    'lesson-04' => ['title' => 'Congestion Management: RED & Load Balancing', 'lab' => 'g5-l4', 'sim' => 'console'],
                ],
            ],
            'M6' => [
                'title' => 'M6 — Network Security & Advanced Protocols',
                'lessons' => [
                    'lesson-01' => ['title' => 'Firewalls: Stateless, Stateful & NGFW', 'lab' => 'g6-l1', 'sim' => 'netsim'],
                    'lesson-02' => ['title' => 'DMZ Design', 'lab' => 'g6-l2', 'sim' => 'netsim'],
                    'lesson-03' => ['title' => 'Intrusion Detection & Prevention', 'lab' => 'g6-l3', 'sim' => 'console'],
                    'lesson-04' => ['title' => 'Site-to-Site VPN: IPsec & GRE', 'lab' => 'g6-l4', 'sim' => 'console'],
                    'lesson-05' => ['title' => 'Remote Access VPN, TLS & PKI', 'lab' => 'g6-l5', 'sim' => 'console'],
                    'lesson-06' => ['title' => '802.1X, RADIUS & WPA3-Enterprise', 'lab' => 'g6-l6', 'sim' => 'console'],
                    'lesson-07' => ['title' => 'DNS Security: DNSSEC, DoH/DoT & Filtering', 'lab' => 'g6-l7', 'sim' => 'netsim'],
                    'lesson-08' => ['title' => 'Final Mission: Hold the Grid', 'lab' => 'g6-l8', 'sim' => 'netsim'],
                ],
            ],
        ],
    ],

    // Information Assurance and Security 1 (ITP1232): "Siege of the Citadel".
    // Every lesson ends in a "Defend it yourself" lab; `sim` picks the simulator (citadel = Citadel Sim,
    // netsim = the network simulator). Citadel lab files: public/citadel-sim/labs/<lab>.json.
    'cybersecurity' => [
        'title' => 'Information Assurance and Security 1',
        'view_base' => 'student.planets.cybersecurity.infosec_course',
        'resources' => [
            [
                'title' => 'Information Assurance and Security 1 (ITP1232) OBE Course Syllabus',
                'url' => '/resources/infosec-1-syllabus-ITP1232.pdf',
                'type' => 'pdf',
                'source' => 'Source: OBE Course Syllabus (ITP1232)',
            ],
        ],
        'modules' => [
            'M1' => [
                'title' => 'M1 — First Contact with the Enemy',
                'lessons' => [
                    'lesson-01' => ['title' => 'Know Your Citadel', 'lab' => 'c1-l1', 'sim' => 'citadel'],
                    'lesson-02' => ['title' => 'Attacks & Vulnerabilities', 'lab' => 'c1-l2', 'sim' => 'citadel'],
                    'lesson-03' => ['title' => 'Anatomy of an Attack', 'lab' => 'c1-l3', 'sim' => 'citadel'],
                    'lesson-04' => ['title' => 'Awareness & Management Commitment', 'lab' => 'c1-l4', 'sim' => 'citadel'],
                    'lesson-05' => ['title' => 'Security Policy (AUP)', 'lab' => 'c1-l5', 'sim' => 'citadel'],
                ],
            ],
            'M2' => [
                'title' => 'M2 — The Data Heist',
                'lessons' => [
                    'lesson-01' => ['title' => 'Data-Centric Security', 'lab' => 'c2-l1', 'sim' => 'citadel'],
                    'lesson-02' => ['title' => 'Data Masking', 'lab' => 'c2-l2', 'sim' => 'citadel'],
                    'lesson-03' => ['title' => 'Cybersecurity Standards', 'lab' => 'c2-l3', 'sim' => 'citadel'],
                    'lesson-04' => ['title' => 'Global Surveillance & Privacy', 'lab' => 'c2-l4', 'sim' => 'citadel'],
                    'lesson-05' => ['title' => 'Computer Security', 'lab' => 'c2-l5', 'sim' => 'citadel'],
                ],
            ],
            'M3' => [
                'title' => 'M3 — Traitors and Pirates',
                'lessons' => [
                    'lesson-01' => ['title' => 'IT Risks', 'lab' => 'c3-l1', 'sim' => 'citadel'],
                    'lesson-02' => ['title' => 'Cybercrime & the Law', 'lab' => 'c3-l2', 'sim' => 'citadel'],
                    'lesson-03' => ['title' => 'ITIL Security Management', 'lab' => 'c3-l3', 'sim' => 'citadel'],
                ],
            ],
            'M4' => [
                'title' => 'M4 — The Network War',
                'lessons' => [
                    'lesson-01' => ['title' => 'Cryptography Basics', 'lab' => 'c4-l1', 'sim' => 'citadel'],
                    'lesson-02' => ['title' => 'Enterprise Security Architecture', 'lab' => 'c4-l2', 'sim' => 'netsim'],
                    'lesson-03' => ['title' => 'Network Security', 'lab' => 'c4-l3', 'sim' => 'netsim'],
                    'lesson-04' => ['title' => 'Digital Rights Management', 'lab' => 'c4-l4', 'sim' => 'citadel'],
                    'lesson-05' => ['title' => 'Copyright Infringement', 'lab' => 'c4-l5', 'sim' => 'citadel'],
                ],
            ],
            'M5' => [
                'title' => 'M5 — The Final Siege',
                'lessons' => [
                    'lesson-01' => ['title' => 'Confidentiality & Data Integrity', 'lab' => 'c5-l1', 'sim' => 'citadel'],
                    'lesson-02' => ['title' => 'Authentication & Non-Repudiation', 'lab' => 'c5-l2', 'sim' => 'citadel'],
                    'lesson-03' => ['title' => 'Symmetric Key Cryptography', 'lab' => 'c5-l3', 'sim' => 'citadel'],
                    'lesson-04' => ['title' => 'Public Key Cryptography', 'lab' => 'c5-l4', 'sim' => 'citadel'],
                    'lesson-05' => ['title' => 'Cryptanalysis & Strong Cryptography', 'lab' => 'c5-l5', 'sim' => 'citadel'],
                    'lesson-06' => ['title' => 'Data & Network Security Services', 'lab' => 'c5-l6', 'sim' => 'citadel'],
                ],
            ],
        ],
    ],

    // Information Assurance and Security 2 (ITP1333): the rebuild after the siege — governance,
    // secure design, and standing watch. Second course on the Citadel, keyed by its own catalog
    // id (not the planet slug) — see CourseProgressService::planetFor()/catalogFor(). Every
    // lesson ends in a "Defend it yourself" Citadel Sim lab. Lab files: public/citadel-sim/labs/<lab>.json.
    'information-assurance-security-2' => [
        'title' => 'Information Assurance and Security 2',
        'planet' => 'cybersecurity',
        'catalog' => 'information-assurance-security-2',
        'view_base' => 'student.planets.cybersecurity.ias2_course',
        'resources' => [
            [
                'title' => 'Information Assurance and Security 2 (ITP1333) OBE Course Syllabus',
                'url' => '/resources/ias-2-syllabus-ITP1333.pdf',
                'type' => 'pdf',
                'source' => 'Source: College of Information and Communications Technology, OBE Course Syllabus (AY 2026–2027)',
            ],
            [
                'title' => 'Introduction to Security Defense Models',
                'url' => 'https://www.geeksforgeeks.org/ethical-hacking/introduction-to-security-defense-models/',
                'type' => 'web',
                'source' => 'GeeksforGeeks (2022)',
            ],
            [
                'title' => 'Four Zones of Trust in Cloud Native Enterprise Architecture',
                'url' => 'https://louwersj.medium.com/four-zones-of-trust-in-cloud-native-enterprise-architecture-b9bca4177dc8',
                'type' => 'web',
                'source' => 'Louwers J. (2022), Medium',
            ],
            [
                'title' => 'What Is Physical Security? How to Keep Your Facilities and Devices Safe',
                'url' => 'https://www.csoonline.com/article/566635/what-is-physical-security-how-to-keep-your-facilities-and-devices-safe-from-on-site-attackers.html',
                'type' => 'web',
                'source' => 'Swinhoe D. (2022), CSO Online',
            ],
            [
                'title' => 'Mastering Disaster Recovery: Ensuring Business Continuity',
                'url' => 'https://drj.com/journal_main/mastering-disaster-recovery-for-sap-and-hana-ensuring-business-continuity-in-the-face-of-disruptions/',
                'type' => 'web',
                'source' => 'DRJ Editorial Team (2025), Disaster Recovery Journal',
            ],
            [
                'title' => 'The NIST Cybersecurity Framework',
                'url' => 'https://www.nist.gov/cyberframework',
                'type' => 'web',
                'source' => 'NIST',
            ],
            [
                'title' => 'Zero Trust Architecture (SP 800-207)',
                'url' => 'https://csrc.nist.gov/pubs/sp/800/207/final',
                'type' => 'web',
                'source' => 'NIST Special Publication 800-207',
            ],
            [
                'title' => 'Information Security Policy Templates',
                'url' => 'https://www.sans.org/information-security-policy/',
                'type' => 'web',
                'source' => 'SANS Institute',
            ],
            [
                'title' => 'An Introduction to Information Security (SP 800-12)',
                'url' => 'https://csrc.nist.gov/pubs/sp/800/12/r1/final',
                'type' => 'web',
                'source' => 'NIST Special Publication 800-12 Rev. 1',
            ],
            [
                'title' => 'OWASP Top 10',
                'url' => 'https://owasp.org/www-project-top-ten/',
                'type' => 'web',
                'source' => 'OWASP Foundation',
            ],
            [
                'title' => 'Threat Modeling',
                'url' => 'https://owasp.org/www-community/Threat_Modeling',
                'type' => 'web',
                'source' => 'OWASP Foundation',
            ],
            [
                'title' => 'Security and Privacy Controls (SP 800-53)',
                'url' => 'https://csrc.nist.gov/pubs/sp/800/53/r5/upd1/final',
                'type' => 'web',
                'source' => 'NIST Special Publication 800-53 Rev. 5',
            ],
            [
                'title' => 'Contingency Planning Guide for Federal Information Systems (SP 800-34)',
                'url' => 'https://csrc.nist.gov/pubs/sp/800/34/r1/final',
                'type' => 'web',
                'source' => 'NIST Special Publication 800-34 Rev. 1',
            ],
            [
                'title' => 'Physical Security',
                'url' => 'https://www.cisa.gov/topics/physical-security',
                'type' => 'web',
                'source' => 'CISA',
            ],
            [
                'title' => 'Information Security Policies, Procedures, and Standards',
                'type' => 'book',
                'source' => 'Landoll D. (2020/2023), Auerbach Publications',
            ],
            [
                'title' => 'Foundations of Information Security',
                'type' => 'book',
                'source' => 'Andress J. (2019), No Starch Press',
            ],
            [
                'title' => 'Network Defense and Countermeasures',
                'type' => 'book',
                'source' => 'Chuck W. (2023), Pearson',
            ],
            [
                'title' => 'Information Security Architecture: An Integrated Approach to Security in the Organization',
                'type' => 'book',
                'source' => 'Killmeyer J. (2023, 2nd Ed), Auerbach Publications',
            ],
            [
                'title' => 'Designing Secure Software: A Guide for Developers',
                'type' => 'book',
                'source' => 'Kohnfelder L. (2022), No Starch Press',
            ],
            [
                'title' => 'Security Operations Management',
                'type' => 'book',
                'source' => 'McCrie R. (2022, 4th Ed), Butterworth-Heinemann',
            ],
            [
                'title' => 'Business Continuity & IT Disaster Recovery',
                'type' => 'book',
                'source' => 'The Art of Service (2021)',
            ],
            [
                'title' => 'Architecting for Scale',
                'type' => 'book',
                'source' => 'Atchison L. (2020), O\'Reilly Media',
            ],
            [
                'title' => 'Advanced Physical Security and Asset Protection Techniques',
                'type' => 'book',
                'source' => 'Valenzuela M. (2023)',
            ],
        ],
        'modules' => [
            'M1' => [
                'title' => 'M1 — Rebuilding After the Siege',
                'lessons' => [
                    'lesson-01' => ['title' => 'Why Information Protection Matters', 'lab' => 'a1-l1', 'sim' => 'citadel'],
                    'lesson-02' => ['title' => 'How Security Grew Up', 'lab' => 'a1-l2', 'sim' => 'citadel'],
                    'lesson-03' => ['title' => 'Justifying the Security Investment', 'lab' => 'a1-l3', 'sim' => 'citadel'],
                    'lesson-04' => ['title' => 'Security Methodology & Building a Program', 'lab' => 'a1-l4', 'sim' => 'citadel'],
                    'lesson-05' => ['title' => 'The Weakest Link: Strategy, Tactics & Process', 'lab' => 'a1-l5', 'sim' => 'citadel'],
                ],
            ],
            'M2' => [
                'title' => 'M2 — Walls Within Walls',
                'lessons' => [
                    'lesson-01' => ['title' => 'The CIA Triad & Other Security Models', 'lab' => 'a2-l1', 'sim' => 'citadel'],
                    'lesson-02' => ['title' => 'Defense in Depth', 'lab' => 'a2-l2', 'sim' => 'citadel'],
                    'lesson-03' => ['title' => 'Zones of Trust', 'lab' => 'a2-l3', 'sim' => 'citadel'],
                    'lesson-04' => ['title' => 'Best Practices for Network Defense', 'lab' => 'a2-l4', 'sim' => 'citadel'],
                ],
            ],
            'M3' => [
                'title' => 'M3 — The Citadel Charter',
                'lessons' => [
                    'lesson-01' => ['title' => 'Security Policies', 'lab' => 'a3-l1', 'sim' => 'citadel'],
                    'lesson-02' => ['title' => 'Security Standards', 'lab' => 'a3-l2', 'sim' => 'citadel'],
                    'lesson-03' => ['title' => 'Procedures', 'lab' => 'a3-l3', 'sim' => 'citadel'],
                    'lesson-04' => ['title' => 'Security Guidelines', 'lab' => 'a3-l4', 'sim' => 'citadel'],
                    'lesson-05' => ['title' => 'Ongoing Maintenance', 'lab' => 'a3-l5', 'sim' => 'citadel'],
                ],
            ],
            'M4' => [
                'title' => 'M4 — Who Guards What',
                'lessons' => [
                    'lesson-01' => ['title' => 'Roles & Responsibilities', 'lab' => 'a4-l1', 'sim' => 'citadel'],
                    'lesson-02' => ['title' => 'Managed Security Services', 'lab' => 'a4-l2', 'sim' => 'citadel'],
                    'lesson-03' => ['title' => 'Security Councils & Steering Committees', 'lab' => 'a4-l3', 'sim' => 'citadel'],
                    'lesson-04' => ['title' => 'Working With HR: Onboarding, Offboarding & Separation of Duties', 'lab' => 'a4-l4', 'sim' => 'citadel'],
                ],
            ],
            'M5' => [
                'title' => 'M5 — Safe by Design',
                'lessons' => [
                    'lesson-01' => ['title' => 'Secure SDLC Integration', 'lab' => 'a5-l1', 'sim' => 'citadel'],
                    'lesson-02' => ['title' => 'Application Security & Threat Modeling', 'lab' => 'a5-l2', 'sim' => 'citadel'],
                    'lesson-03' => ['title' => 'Web Application Security: the OWASP Top 10', 'lab' => 'a5-l3', 'sim' => 'citadel'],
                    'lesson-04' => ['title' => 'Client Application Security', 'lab' => 'a5-l4', 'sim' => 'citadel'],
                    'lesson-05' => ['title' => 'Remote Administration Security', 'lab' => 'a5-l5', 'sim' => 'citadel'],
                ],
            ],
            'M6' => [
                'title' => 'M6 — Running the Watch',
                'lessons' => [
                    'lesson-01' => ['title' => 'Communications & Reporting', 'lab' => 'a6-l1', 'sim' => 'citadel'],
                    'lesson-02' => ['title' => 'Change Management', 'lab' => 'a6-l2', 'sim' => 'citadel'],
                    'lesson-03' => ['title' => 'Acceptable Use Enforcement', 'lab' => 'a6-l3', 'sim' => 'citadel'],
                    'lesson-04' => ['title' => 'Administrative Security Controls', 'lab' => 'a6-l4', 'sim' => 'citadel'],
                    'lesson-05' => ['title' => 'Management Practices', 'lab' => 'a6-l5', 'sim' => 'citadel'],
                    'lesson-06' => ['title' => 'Operational Accountability', 'lab' => 'a6-l6', 'sim' => 'citadel'],
                ],
            ],
            'M7' => [
                'title' => 'M7 — When the Lights Go Out',
                'lessons' => [
                    'lesson-01' => ['title' => 'Disaster Recovery Concepts', 'lab' => 'a7-l1', 'sim' => 'citadel'],
                    'lesson-02' => ['title' => 'Business Continuity Planning', 'lab' => 'a7-l2', 'sim' => 'citadel'],
                    'lesson-03' => ['title' => 'Backups: Strategies, Scheduling & Storage', 'lab' => 'a7-l3', 'sim' => 'citadel'],
                    'lesson-04' => ['title' => 'High Availability Architectures', 'lab' => 'a7-l4', 'sim' => 'citadel'],
                    'lesson-05' => ['title' => 'Compliance & Auditing the Recovery Plan', 'lab' => 'a7-l5', 'sim' => 'citadel'],
                ],
            ],
            'M8' => [
                'title' => 'M8 — Boots on the Ground',
                'lessons' => [
                    'lesson-01' => ['title' => 'Classification of Assets', 'lab' => 'a8-l1', 'sim' => 'citadel'],
                    'lesson-02' => ['title' => 'Physical Vulnerability Assessment', 'lab' => 'a8-l2', 'sim' => 'citadel'],
                    'lesson-03' => ['title' => 'Site Location Selection', 'lab' => 'a8-l3', 'sim' => 'citadel'],
                    'lesson-04' => ['title' => 'Locks & Entry Controls', 'lab' => 'a8-l4', 'sim' => 'citadel'],
                    'lesson-05' => ['title' => 'Physical Intrusion Detection', 'lab' => 'a8-l5', 'sim' => 'citadel'],
                    'lesson-06' => ['title' => 'Regulatory Standards Compliance', 'lab' => 'a8-l6', 'sim' => 'citadel'],
                ],
            ],
        ],
    ],
    // Computer Programming 1 (CC102): "The Dead Console" — Season 1 of the Wanderer's story.
    // Astro's crew crash-lands on the edge of Programming City and wakes their ship's blank
    // computer one program at a time. No villain: every problem is the crash, the weather,
    // or a broken part. (Season 2 / Programming 2 is fixing the city itself.)
    // A planet's second+ course is keyed by its own catalog id (not by planet slug) and names
    // its planet with 'planet'/'catalog' — see CourseProgressService::planetFor()/catalogFor().
    // Every lesson ends in "Code it yourself"; the answer key ('expected') is checked server-side.
    'computer-programming-1' => [
        'title' => 'Computer Programming 1',
        'planet' => 'programming',
        'catalog' => 'computer-programming-1',
        'view_base' => 'student.planets.programming.c_course',
        'resources' => [
            [
                'title' => 'CC102 — Computer Programming 1 OBE Course Syllabus',
                'url' => '/resources/computer-programming-1-syllabus-CC102.pdf',
                'type' => 'pdf',
                'source' => 'Source: College of Information and Communications Technology, OBE Course Syllabus (AY 2025–2026)',
            ],
            [
                'title' => 'Learn C Programming',
                'url' => 'https://www.programiz.com/c-programming',
                'type' => 'web',
                'source' => 'Programiz',
            ],
            [
                'title' => 'C Tutorial',
                'url' => 'https://www.w3schools.com/c/c_intro.php',
                'type' => 'web',
                'source' => 'W3Schools',
            ],
            [
                'title' => 'C Programming Language Tutorial',
                'url' => 'https://www.geeksforgeeks.org/c-programming-language/',
                'type' => 'web',
                'source' => 'GeeksforGeeks',
            ],
            [
                'title' => 'Getting Started with C Programming: A Beginner\'s Guide',
                'url' => 'https://www.simplilearn.com/tutorials/c-tutorial/use-of-c-language',
                'type' => 'web',
                'source' => 'Simplilearn',
            ],
            [
                'title' => 'C Programming for Beginners',
                'url' => 'https://www.youtube.com/watch?v=KnvbUiSxvbM&list=PL98qAXLA6aftD9ZlnjpLhdQAOFI8xIB6e',
                'type' => 'video',
                'source' => 'Programiz, YouTube',
            ],
            [
                'title' => 'Learn C — interactive exercises',
                'url' => 'https://www.learn-c.org/',
                'type' => 'web',
                'source' => 'learn-c.org',
            ],
            [
                'title' => 'C standard library reference',
                'url' => 'https://en.cppreference.com/w/c',
                'type' => 'web',
                'source' => 'cppreference.com',
            ],
            [
                'title' => "Beej's Guide to C Programming",
                'url' => 'https://beej.us/guide/bgc/',
                'type' => 'web',
                'source' => 'beej.us',
            ],
            [
                'title' => 'Modern C for Absolute Beginners',
                'type' => 'book',
                'source' => 'Slobodan Dmitrović (2024), Apress',
            ],
            [
                'title' => 'Practical C',
                'type' => 'book',
                'source' => 'Rafal Jackiewicz (2023), Independently published',
            ],
        ],
        'modules' => [
            'M1' => [
                'title' => 'M1 — The Dead Console',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Orders Astro Can Follow',
                        'expected' => "Console online.\n",
                    ],
                    'lesson-02' => [
                        'title' => 'Drawing the Plan',
                        'expected' => "Power: online\nCoolant: stable\nSystems: ready\n",
                    ],
                    'lesson-03' => [
                        'title' => 'Anatomy of a Console',
                        'expected' => "Diagnostics: PASS\n",
                    ],
                    'lesson-04' => [
                        'title' => 'Labelling the Cargo',
                        'expected' => "Crew: 4\nFuel: 76.50\nBay: A\n",
                    ],
                    'lesson-05' => [
                        'title' => 'Doing the Math',
                        'expected' => "Total: 47\nEach: 9\nLeft: 2\n",
                    ],
                    'lesson-06' => [
                        'title' => 'Talking to the Crew',
                        'expected' => "Enter fuel: Left: 25\n",
                    ],
                ],
            ],
            'M2' => [
                'title' => 'M2 — The Long Night',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Reading the Warnings',
                        'expected' => "Hull critical: 0\nShield critical: 0\nSafe: 1\n",
                    ],
                    'lesson-02' => [
                        'title' => 'Safe, Warning, Critical',
                        'expected' => "Status: critical\n",
                    ],
                    'lesson-03' => [
                        'title' => 'The Switchboard',
                        'expected' => "Bay unknown\n",
                    ],
                    'lesson-04' => [
                        'title' => 'Standing Watch',
                        'expected' => "Level: 2\nLevel: 3\nLevel: 4\nLevel: 5\nBoost complete\n",
                    ],
                    'lesson-05' => [
                        'title' => 'Counting Down to Contact',
                        'expected' => "T-minus 3\nT-minus 2\nT-minus 1\nContact\n",
                    ],
                ],
            ],
            'M3' => [
                'title' => 'M3 — Divide the Crew',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Astro Splits the Work',
                        'expected' => "Alarm: intruder detected.\n",
                    ],
                    'lesson-02' => [
                        'title' => 'Handing Off the Work',
                        'expected' => "Crew 3 assigned to bay B\n",
                    ],
                    'lesson-03' => [
                        'title' => 'Reporting Back',
                        'expected' => "Gap: 65\n",
                    ],
                    'lesson-04' => [
                        'title' => 'The Blueprint',
                        'expected' => "Boosted: 30\n",
                    ],
                    'lesson-05' => [
                        'title' => 'Many Hands',
                        'expected' => "Shield gap: 65\n",
                    ],
                ],
            ],
            'M4' => [
                'title' => 'M4 — The Hull Grid',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'The Emitter Line',
                        'expected' => "Bay 0: 12\nBay 1: 45\nBay 3: 20\n",
                    ],
                    'lesson-02' => [
                        'title' => 'Checking Each Emitter',
                        'expected' => "Bay 0: 12\nBay 1: 45\nBay 2: 78\nBay 3: 20\n",
                    ],
                    'lesson-03' => [
                        'title' => 'Patching the Grid',
                        'expected' => "Repair total: 155\n",
                    ],
                    'lesson-04' => [
                        'title' => 'The Grid Has Rows',
                        'expected' => "Panel[0][2]: 15\nPanel[1][0]: 20\n",
                    ],
                    'lesson-05' => [
                        'title' => 'Scanning the Grid',
                        'expected' => "Grid total: 60\n",
                    ],
                ],
            ],
        ],
    ],

    // Computer Programming 2 (CC103): "Switch the City On" — Season 2 of the Wanderer's story.
    // Picks up the morning after Programming 1's last lesson: the ship is fixed but its console
    // forgets the crew at every power cut, and Programming City below is still dark. The crew
    // switches the city back on street by street (functions, pointers, arrays, structures,
    // linked lists) and ends by teaching the console to remember them with files. Vex never
    // appears: the city was wrecked long before the crew arrived.
    // 'c_runtime' => 'picoc': this course's editor runs PicoC instead of JSCPP, because CC103
    // needs structs, malloc/free and FILE*, which JSCPP can't run (see public/js/picoc-worker.js).
    // Every lesson ends in "Code it yourself"; the answer key ('expected') is checked server-side.
    'computer-programming-2' => [
        'title' => 'Computer Programming 2',
        'planet' => 'programming',
        'catalog' => 'computer-programming-2',
        'view_base' => 'student.planets.programming.c2_course',
        'c_runtime' => 'picoc',
        'resources' => [
            [
                'title' => 'CC103 — Computer Programming 2 OBE Course Syllabus',
                'url' => '/resources/computer-programming-2-syllabus-CC103.pdf',
                'type' => 'pdf',
                'source' => 'Source: College of Information and Communications Technology, OBE Course Syllabus (AY 2025–2026)',
            ],
            [
                'title' => 'Learn C Programming',
                'url' => 'https://www.programiz.com/c-programming',
                'type' => 'web',
                'source' => 'Programiz',
            ],
            [
                'title' => 'C Tutorial',
                'url' => 'https://www.w3schools.com/c/',
                'type' => 'web',
                'source' => 'W3Schools',
            ],
            [
                'title' => 'C Programming Tutorial',
                'url' => 'https://www.tutorialspoint.com/cprogramming/index.htm',
                'type' => 'web',
                'source' => 'Tutorialspoint',
            ],
            [
                'title' => 'The C Programming Handbook for Beginners',
                'url' => 'https://www.freecodecamp.org/news/the-c-programming-handbook-for-beginners/',
                'type' => 'web',
                'source' => 'Dionysia Lemonaki, freeCodeCamp',
            ],
            [
                'title' => 'What is C Language: Introduction to C Programming',
                'url' => 'https://www.simplilearn.com/tutorials/c-tutorial/use-of-c-language',
                'type' => 'web',
                'source' => 'Ravikiran A S, Simplilearn',
            ],
            [
                'title' => 'C standard library reference',
                'url' => 'https://en.cppreference.com/w/c',
                'type' => 'web',
                'source' => 'cppreference.com',
            ],
            [
                'title' => 'C Programming: A Modern Approach (2nd Edition)',
                'type' => 'book',
                'source' => 'K. N. King, W.W. Norton & Company',
            ],
            [
                'title' => 'C: A Reference Manual (5th Edition)',
                'type' => 'book',
                'source' => 'Samuel P. Harbison & Guy L. Steele, Pearson',
            ],
            [
                'title' => 'C How to Program (8th Edition)',
                'type' => 'book',
                'source' => 'P. J. Deitel & H. M. Deitel (2020), Pearson',
            ],
            [
                'title' => 'The C Programming Language',
                'type' => 'book',
                'source' => 'Brian W. Kernighan & Dennis M. Ritchie',
            ],
            [
                'title' => 'Algorithmic Thinking (2nd Edition)',
                'url' => 'https://nostarch.com/algorithmic-thinking-2nd-edition',
                'type' => 'book',
                'source' => 'Daniel Zingaro, No Starch Press',
            ],
        ],
        'modules' => [
            'M1' => [
                'title' => 'M1 — Down Into the City',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'The First Street',
                        'expected' => "Lamp 1: on\nLamp 2: on\nLamp 3: on\nStreet lit.\n",
                    ],
                    'lesson-02' => [
                        'title' => 'Orders and Answers',
                        'expected' => "Cable needed: 72\nLamps still: 6\n",
                    ],
                    'lesson-03' => [
                        'title' => 'What Stays in the Toolbox',
                        'expected' => "Lamps lit: 3\n",
                    ],
                    'lesson-04' => [
                        'title' => 'Crates Inside Crates',
                        'expected' => "Crates in the pile: 10\n",
                    ],
                    'lesson-05' => [
                        'title' => 'The Job Board',
                        'expected' => "Cable: 5.0 m\n",
                    ],
                ],
            ],
            'M2' => [
                'title' => 'M2 — Street Addresses',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Every Machine Has an Address',
                        'expected' => "Lamp reads: 7\nSame place: yes\n",
                    ],
                    'lesson-02' => [
                        'title' => 'Reaching In from the Depot',
                        'expected' => "Before: 0\nAfter: 2\n",
                    ],
                    'lesson-03' => [
                        'title' => 'Swap the Tram Cars',
                        'expected' => "Track A: 2\nTrack B: 1\n",
                    ],
                    'lesson-04' => [
                        'title' => 'Two Answers at Once',
                        'expected' => "Each crew: 9\nLeft over: 2\n",
                    ],
                    'lesson-05' => [
                        'title' => 'The Blank Tag',
                        'expected' => "Reading: 42\nNo signal\n",
                    ],
                ],
            ],
            'M3' => [
                'title' => 'M3 — Lamp Street',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Six Lamps, One Name',
                        'expected' => "Lamps: 6\nFirst: 30\nLast: 45\n",
                    ],
                    'lesson-02' => [
                        'title' => 'Walking Lamp Street',
                        'expected' => "Dead lamps: 2\nBrightest: lamp 3\n",
                    ],
                    'lesson-03' => [
                        'title' => 'Handing Over the Street',
                        'expected' => "Street power: 310\n",
                    ],
                    'lesson-04' => [
                        'title' => 'The Address of the First Lamp',
                        'expected' => "Lamp 0: 15\nLamp 1: 25\nLamp 2: 35\n",
                    ],
                    'lesson-05' => [
                        'title' => 'The Bakery Sign',
                        'expected' => "Sign: BAKERY\nLetters: 6\nSign fixed.\n",
                    ],
                    'lesson-06' => [
                        'title' => 'The Block Map',
                        'expected' => "Main: 60\nHarbor: 15\n",
                    ],
                ],
            ],
            'M4' => [
                'title' => 'M4 — The Machine Registry',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'One Card for One Machine',
                        'expected' => "Machine: Fountain\nPower: 40\nWorking: no\n",
                    ],
                    'lesson-02' => [
                        'title' => 'Filling In the Card',
                        'expected' => "Tram 7: 90, working\nBackup: 0, broken\n",
                    ],
                    'lesson-03' => [
                        'title' => 'To the Workshop and Back',
                        'expected' => "Clock: 20\nClock: 100\n",
                    ],
                    'lesson-04' => [
                        'title' => 'The Registry',
                        'expected' => "Broken: Fountain\nBroken: Clock\nWorking: 2\n",
                    ],
                    'lesson-05' => [
                        'title' => 'Shorter Names',
                        'expected' => "Clock Tower: 3\nHarbor Tower: 4\nMarket Tower: 5\nTotal bells: 12\n",
                    ],
                ],
            ],
            'M5' => [
                'title' => 'M5 — The Tram Line',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Memory on Request',
                        'expected' => "Spare part: 12\nPart returned.\n",
                    ],
                    'lesson-02' => [
                        'title' => 'As Many Cars as It Takes',
                        'expected' => "Cars: 4\nSeats: 52\n",
                    ],
                    'lesson-03' => [
                        'title' => 'Each Stop Knows the Next',
                        'expected' => "Stop: Station\nStop: Market\nStop: Park\nEnd of the line.\n",
                    ],
                    'lesson-04' => [
                        'title' => 'A New Stop at the Harbor',
                        'expected' => "Harbor -> Station -> Market -> Park\n",
                    ],
                    'lesson-05' => [
                        'title' => 'Closing a Stop',
                        'expected' => "Station -> Park\nStops: 2\nLine cleared.\n",
                    ],
                ],
            ],
            'M6' => [
                'title' => 'M6 — The City Archive',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Paper That Remembers',
                        'expected' => "Archive open.\nArchive closed.\nNo file called missing.txt\n",
                    ],
                    'lesson-02' => [
                        'title' => 'One Letter at a Time',
                        'expected' => "Read: LAMP\nBytes: 4\n",
                    ],
                    'lesson-03' => [
                        'title' => 'Lines in the Logbook',
                        'expected' => "1: Gate Road lit\n2: Tram running\n3: Bells rung\n",
                    ],
                    'lesson-04' => [
                        'title' => 'Neat Columns',
                        'expected' => "Station 40\nPark 25\nPlant 15\nRiders: 80\n",
                    ],
                    'lesson-05' => [
                        'title' => 'The Console Remembers',
                        'expected' => "HELLO, ASTRO.\nHELLO, RIVET.\nHELLO, VOLT.\nHELLO, CADET.\n",
                    ],
                ],
            ],
        ],
    ],

];
