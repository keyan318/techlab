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
                        'type' => 'lesson'
                    ],
                    'lesson-02' => [
                        'title' => 'Variables & Memory',
                        'slug' => 'variables-memory',
                        'expected' => '42',
                        'order' => 2,
                        'view' => 'student.programming.M1.lesson-02',
                        'type' => 'lesson'
                    ],
                    'lesson-03' => [
                        'title' => 'Data Types',
                        'slug' => 'data-types',
                        'expected' => '20',
                        'order' => 3,
                        'view' => 'student.programming.M1.lesson-03',
                        'type' => 'lesson'
                    ],
                    'lesson-04' => [
                        'title' => 'Expressions & Operators',
                        'slug' => 'expressions-operators',
                        'expected' => '20.0',
                        'order' => 4,
                        'view' => 'student.programming.M1.lesson-04',
                        'type' => 'lesson'
                    ],
                    'lesson-05' => [
                        'title' => 'Talking to the Program',
                        'slug' => 'talking-program',
                        'expected' => "Astro's crew needs 8 ration packs",
                        'order' => 5,
                        'view' => 'student.programming.M1.lesson-05',
                        'type' => 'lesson'
                    ],
                    'lesson-06' => [
                        'title' => 'Reading Error Messages',
                        'slug' => 'reading-errors',
                        'expected' => '97',
                        'order' => 6,
                        'view' => 'student.programming.M1.lesson-06',
                        'type' => 'lesson'
                    ],
                ]
            ],
            'M2' => [
                'title' => 'M2 — Conditions & Loops',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Decision Points',
                        'slug' => 'decision-points',
                        'order' => 1,
                        'view' => 'student.programming.M2.lesson-01',
                        'type' => 'lesson'
                    ],
                    'lesson-02' => [
                        'title' => 'Branching Paths',
                        'slug' => 'branching-paths',
                        'order' => 2,
                        'view' => 'student.programming.M2.lesson-02',
                        'type' => 'lesson'
                    ],
                    'lesson-03' => [
                        'title' => 'Combining Conditions',
                        'slug' => 'combining-conditions',
                        'order' => 3,
                        'view' => 'student.programming.M2.lesson-03',
                        'type' => 'lesson'
                    ],
                    'lesson-04' => [
                        'title' => 'Repeating Signals',
                        'slug' => 'repeating-signals',
                        'order' => 4,
                        'view' => 'student.programming.M2.lesson-04',
                        'type' => 'lesson'
                    ],
                    'lesson-05' => [
                        'title' => 'Repeating With for',
                        'slug' => 'counting-loops',
                        'order' => 5,
                        'view' => 'student.programming.M2.lesson-05',
                        'type' => 'lesson'
                    ],
                    'lesson-06' => [
                        'title' => 'Breaking the Loop',
                        'slug' => 'breaking-loop',
                        'order' => 6,
                        'view' => 'student.programming.M2.lesson-06',
                        'type' => 'lesson'
                    ],
                ]
            ],
            'M3' => [
                'title' => 'M3 — Functions & Error Handling',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Reusable Routines',
                        'slug' => 'reusable-routines',
                        'order' => 1,
                        'view' => 'student.programming.M3.lesson-01',
                        'type' => 'lesson'
                    ],
                    'lesson-02' => [
                        'title' => 'Passing Information',
                        'slug' => 'passing-information',
                        'order' => 2,
                        'view' => 'student.programming.M3.lesson-02',
                        'type' => 'lesson'
                    ],
                    'lesson-03' => [
                        'title' => 'Returning Results',
                        'slug' => 'scope',
                        'order' => 3,
                        'view' => 'student.programming.M3.lesson-03',
                        'type' => 'lesson'
                    ],
                    'lesson-04' => [
                        'title' => 'Anticipating Failure',
                        'slug' => 'anticipating-failure',
                        'order' => 4,
                        'view' => 'student.programming.M3.lesson-04',
                        'type' => 'lesson'
                    ],
                    'lesson-05' => [
                        'title' => 'Sanity Checks',
                        'slug' => 'sanity-checks',
                        'order' => 5,
                        'view' => 'student.programming.M3.lesson-05',
                        'type' => 'lesson'
                    ],
                ]
            ],
            'M4' => [
                'title' => 'M4 — Working With Data & Files',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Collections',
                        'slug' => 'collections',
                        'order' => 1,
                        'view' => 'student.programming.M4.lesson-01',
                        'type' => 'lesson'
                    ],
                    'lesson-02' => [
                        'title' => 'List Operations',
                        'slug' => 'list-operations',
                        'order' => 2,
                        'view' => 'student.programming.M4.lesson-02',
                        'type' => 'lesson'
                    ],
                    'lesson-03' => [
                        'title' => 'Labeled Data',
                        'slug' => 'labeled-data',
                        'order' => 3,
                        'view' => 'student.programming.M4.lesson-03',
                        'type' => 'lesson'
                    ],
                    'lesson-04' => [
                        'title' => 'Nested Structures',
                        'slug' => 'nested-structures',
                        'order' => 4,
                        'view' => 'student.programming.M4.lesson-04',
                        'type' => 'lesson'
                    ],
                    'lesson-05' => [
                        'title' => 'Text Manipulation',
                        'slug' => 'text-manipulation',
                        'order' => 5,
                        'view' => 'student.programming.M4.lesson-05',
                        'type' => 'lesson'
                    ],
                    'lesson-06' => [
                        'title' => 'Reading Files',
                        'slug' => 'reading-files',
                        'order' => 6,
                        'view' => 'student.programming.M4.lesson-06',
                        'type' => 'lesson'
                    ],
                    'lesson-07' => [
                        'title' => 'Writing Files',
                        'slug' => 'writing-files',
                        'order' => 7,
                        'view' => 'student.programming.M4.lesson-07',
                        'type' => 'lesson'
                    ],
                    'lesson-08' => [
                        'title' => 'Structured Data Formats',
                        'slug' => 'structured-data',
                        'order' => 8,
                        'view' => 'student.programming.M4.lesson-08',
                        'type' => 'lesson'
                    ],
                ]
            ],
            'M5' => [
                'title' => 'M5 — Python Automation',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Pattern Matching',
                        'slug' => 'pattern-matching',
                        'order' => 1,
                        'view' => 'student.programming.M5.lesson-01',
                        'type' => 'lesson'
                    ],
                    'lesson-02' => [
                        'title' => 'Practical Regex',
                        'slug' => 'practical-regex',
                        'order' => 2,
                        'view' => 'student.programming.M5.lesson-02',
                        'type' => 'lesson'
                    ],
                    'lesson-03' => [
                        'title' => 'File System Basics',
                        'slug' => 'file-system-basics',
                        'order' => 3,
                        'view' => 'student.programming.M5.lesson-03',
                        'type' => 'lesson'
                    ],
                    'lesson-04' => [
                        'title' => 'Building a CLI Tool',
                        'slug' => 'building-cli',
                        'order' => 4,
                        'view' => 'student.programming.M5.lesson-04',
                        'type' => 'lesson'
                    ],
                    'lesson-05' => [
                        'title' => 'Time & Scheduling Concepts',
                        'slug' => 'time-scheduling',
                        'order' => 5,
                        'view' => 'student.programming.M5.lesson-05',
                        'type' => 'lesson'
                    ],
                ]
            ],
            'M6' => [
                'title' => 'M6 — Real-World Projects',
                'lessons' => [
                    'lesson-01' => [
                        'title' => '6A: Web Basics',
                        'slug' => 'web-basics',
                        'order' => 1,
                        'view' => 'student.programming.M6.lesson-01',
                        'type' => 'lesson'
                    ],
                    'lesson-02' => [
                        'title' => '6B: Spreadsheet Automation',
                        'slug' => 'spreadsheet-automation',
                        'order' => 2,
                        'view' => 'student.programming.M6.lesson-02',
                        'type' => 'lesson'
                    ],
                    'lesson-03' => [
                        'title' => '6C: Mini Database',
                        'slug' => 'mini-database',
                        'order' => 3,
                        'view' => 'student.programming.M6.lesson-03',
                        'type' => 'lesson'
                    ],
                    'lesson-04' => [
                        'title' => '6D: Document Automation',
                        'slug' => 'document-automation',
                        'order' => 4,
                        'view' => 'student.programming.M6.lesson-04',
                        'type' => 'lesson'
                    ],
                ]
            ],
            'M7' => [
                'title' => 'M7 — Final Automation Project',
                'lessons' => [
                    'lesson-01' => [
                        'title' => 'Proposal & Design',
                        'slug' => 'proposal-design',
                        'order' => 1,
                        'view' => 'student.programming.M7.lesson-01',
                        'type' => 'lesson'
                    ],
                ]
            ]
        ]
    ],

    'networking' => [
        'title' => 'Networking 1',
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

];
