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
                'scene' => 'scene-python-space',
                'level' => 'Beginner',
                'description' => 'Start from zero and build real Python skills one mission at a time, all the way to automating everyday tasks, with Astro as your guide.',
            ],
            'next-language' => [
                'banner' => 'linear-gradient(160deg,#1b1650 0%,#2d2379 55%,#43308f 100%)',
                'coming_soon' => true,
            ],
        ],
    ],
    'networking' => [
        'title' => 'Networking Nebula',
        'blurb' => 'Pick a course to learn how machines talk.',
        'courses' => [
            'networking-fundamentals' => [
                'title' => 'Networking 1',
                'blurb' => 'Call Earth: from cables to subnets',
                'banner' => 'linear-gradient(180deg,#0b1f4d 0%,#1d6fd8 45%,#5be1ff 100%)',
                'logo' => 'networking-logo',
                'level' => 'Beginner',
                'description' => 'Learn how machines talk to each other, from cables and packets to addresses, DHCP and DNS, with hands-on simulator labs.',
            ],
            'networking-2' => [
                'title' => 'Networking 2',
                'blurb' => 'Beyond the basics: routing, security and design',
                'banner' => 'linear-gradient(180deg,#0b1f4d 0%,#2a3fb8 45%,#8a6bff 100%)',
                'logo' => 'networking-logo',
                'level' => 'Intermediate',
                'description' => 'Content is being built by its faculty captain — enroll now with a code to be first in when it lands.',
            ],
        ],
    ],
    'cybersecurity' => [
        'title' => 'Cybersecurity Citadel',
        'blurb' => 'Pick a course to learn how to stay safe.',
        'courses' => [
            'information-security-1' => [
                'title' => 'Information Security 1',
                'blurb' => 'Siege of the Citadel: defend Codexia',
                'banner' => 'linear-gradient(180deg,#1a0f4d 0%,#5a2fd0 50%,#9b6bff 100%)',
                'logo' => 'cybersecurity-logo',
                'level' => 'Beginner',
                'description' => 'Planet Doom sends hackers against the crew\'s base. Learn information assurance and security, then defend the Citadel in hands-on attack-and-defend labs.',
            ],
        ],
    ],
];
