<?php

/*
| Course catalog: which courses each planet (track) offers.
|
| A student picks a planet, then picks one of these courses before entering it.
| To add Java / C++ / C# later, add another entry under 'programming' — nothing
| else needs to change. `banner` is a CSS background for the card image.
|
| A course's lessons come from config/course-structure.php (keyed by planet slug);
| a course with no blueprint there simply shows 0% until its lessons exist.
*/

return [
    'programming' => [
        'title' => 'Programming City',
        'blurb' => 'Pick a language to learn. You can switch any time.',
        'courses' => [
            'python' => [
                'title' => 'Python',
                'blurb' => 'Zero to Automation',
                'banner' => 'linear-gradient(180deg,#38b6ff 0%,#8fe0ff 38%,#7bf06a 38%,#18b878 100%)',
            ],
        ],
    ],
    'networking' => [
        'title' => 'Networking Nebula',
        'blurb' => 'Pick a course to learn how machines talk.',
        'courses' => [
            'networking-fundamentals' => [
                'title' => 'Networking Fundamentals',
                'blurb' => 'Packets, protocols and paths',
                'banner' => 'linear-gradient(180deg,#0b1f4d 0%,#1d6fd8 45%,#5be1ff 100%)',
            ],
        ],
    ],
    'cybersecurity' => [
        'title' => 'Cybersecurity Citadel',
        'blurb' => 'Pick a course to learn how to stay safe.',
        'courses' => [
            'cybersecurity-basics' => [
                'title' => 'Cybersecurity Basics',
                'blurb' => 'Threats, defenses and good habits',
                'banner' => 'linear-gradient(180deg,#1a0f4d 0%,#5a2fd0 50%,#9b6bff 100%)',
            ],
        ],
    ],
];
