<?php

/*
| Course catalog: which courses each planet (track) offers.
|
| A student picks a planet, then picks one of these courses before entering it.
| To add Java / C++ / C# later, add another entry under 'programming'.
| `banner` is a CSS background for the card image; `art` (optional) is a pixel-art PNG
| drawn over it, and `scene` an animated Blade pixel scene (Python's). `code` is the school's course code,
| printed on the course's completion certificate.
|
| A course's lessons come from config/course-structure.php. The first course on a
| planet is keyed there by the planet slug; any later one is keyed by its catalog id
| and names its planet with 'planet'/'catalog' (see 'computer-programming-1'), which
| is what keeps two courses on one planet from sharing lessons or progress.
| A course with no blueprint there simply shows 0% until its lessons exist.
|
| A planet's `blurb` heads its course picker; its `description` is what the planet is about,
| shown on the planet cards and the dashboard's "Start here" card.
|
| `hidden` => true switches a course off without deleting it: its lessons, blueprint and
| progress stay on disk, but no student, admin or landing-page list offers it, and its
| lessons don't count toward the planet. Remove the flag to bring it back.
|
| `requires` names another course on the same planet that a student must finish first
| (every lesson done and its final exam passed, i.e. its certificate earned) before they
| can ask to join this one. See App\Services\CoursePrerequisiteService.
*/

return [
    'programming' => [
        'title' => 'Programming City',
        'blurb' => 'Pick a language to learn. You can switch any time.',
        'description' => 'Build with code — logic, apps, and the foundations of software.',
        'courses' => [
            // Not part of the Programming curriculum right now; kept for later.
            'python' => [
                'hidden' => true,
                'title' => 'Python',
                'blurb' => 'Zero to Automation',
                'banner' => 'linear-gradient(180deg,#38b6ff 0%,#8fe0ff 38%,#7bf06a 38%,#18b878 100%)',
                'scene' => 'scene-python-space',
                'level' => 'Beginner',
                'description' => 'Start from zero and build real Python skills one mission at a time, all the way to automating everyday tasks, with Astro as your guide.',
            ],
            'computer-programming-1' => [
                'title' => 'Computer Programming 1',
                'code' => 'CC102',
                'blurb' => 'The Dead Console: wake the Wanderer in C',
                'banner' => 'linear-gradient(180deg,#0d1b3e 0%,#1d4ed8 45%,#60a5fa 100%)',
                'art' => 'images/courses/computer-programming-1.png',
                'logo' => 'c-logo',
                'level' => 'Beginner',
                'description' => 'Astro\'s ship came down hard on the edge of Programming City, and its computer woke up blank. Learn C from zero — variables, decisions, functions and arrays — and bring the Wanderer back to life one program at a time.',
            ],
            // Season 2 of the Wanderer's story: picks up the morning after Programming 1 ends.
            'computer-programming-2' => [
                'requires' => 'computer-programming-1',
                'title' => 'Computer Programming 2',
                'code' => 'CC103',
                'blurb' => 'Switch the City On: Season 2 of the Wanderer, in C',
                'banner' => 'linear-gradient(180deg,#0d1b3e 0%,#4338ca 45%,#f5c04a 100%)',
                'art' => 'images/courses/computer-programming-2.png',
                'logo' => 'c-logo',
                'level' => 'Intermediate',
                'description' => 'The Wanderer is fixed, but its console forgets the crew every time the power drops, and the city below is still dark. Go down into Programming City with Astro\'s crew and switch it back on, street by street, with C functions, pointers, arrays, structures, linked lists and files.',
            ],
        ],
    ],
    'networking' => [
        'title' => 'Networking Nebula',
        'blurb' => 'Pick a course to learn how machines talk.',
        'description' => 'How the internet works — routing, protocols, and connections.',
        'courses' => [
            'networking-fundamentals' => [
                'title' => 'Networking 1',
                'code' => 'ITP422',
                'blurb' => 'Call Earth: from cables to subnets',
                'banner' => 'linear-gradient(180deg,#0b1f4d 0%,#1d6fd8 45%,#5be1ff 100%)',
                'art' => 'images/courses/networking-fundamentals.png',
                'logo' => 'networking-logo',
                'level' => 'Beginner',
                'description' => 'Learn how machines talk to each other, from cables and packets to addresses, DHCP and DNS, with hands-on simulator labs.',
            ],
            // Season 2 of Networking 1's story: picks up right after "Call Earth".
            'networking-2' => [
                'requires' => 'networking-fundamentals',
                'title' => 'Networking 2',
                'code' => 'ITP831',
                'blurb' => 'Light the Grid: Season 2 of Call Earth',
                'banner' => 'linear-gradient(180deg,#0b1f4d 0%,#2a3fb8 45%,#8a6bff 100%)',
                'art' => 'images/courses/networking-2.png',
                'logo' => 'networking-logo',
                'level' => 'Intermediate',
                'description' => 'Earth answered, and now Codexia needs a real network. Link the crew\'s scattered outposts into one Grid with OSPF and BGP, build it to survive failures, lock down its switches, keep the important traffic moving with QoS, and shield it from Planet Doom with firewalls and VPNs, on Cisco-style routers and switches.',
            ],
        ],
    ],
    'cybersecurity' => [
        'title' => 'Cybersecurity Citadel',
        'blurb' => 'Pick a course to learn how to stay safe.',
        'description' => 'Defend systems — threats, encryption, and secure design.',
        'courses' => [
            'information-security-1' => [
                'title' => 'Information Assurance and Security 1',
                'code' => 'ITP1232',
                'blurb' => 'Siege of the Citadel: defend Codexia',
                'banner' => 'linear-gradient(180deg,#1a0f4d 0%,#5a2fd0 50%,#9b6bff 100%)',
                'art' => 'images/courses/information-security-1.png',
                'logo' => 'cybersecurity-logo',
                'level' => 'Beginner',
                'description' => 'Planet Doom sends hackers against the crew\'s base. Learn information assurance and security, then defend the Citadel in hands-on attack-and-defend labs.',
            ],
            'information-assurance-security-2' => [
                'requires' => 'information-security-1',
                'title' => 'Information Assurance and Security 2',
                'code' => 'ITP1333',
                'blurb' => 'Rebuilding the Citadel: govern, design, defend',
                'banner' => 'linear-gradient(180deg,#0f1a3d 0%,#2f4fd0 50%,#6bc0ff 100%)',
                'art' => 'images/courses/information-assurance-security-2.png',
                'logo' => 'cybersecurity-logo',
                'level' => 'Intermediate',
                'description' => 'The siege is over, but the Citadel still has to hold. Learn the security models, policies, secure design and disaster-recovery plans that keep it standing, then defend it against Planet Doom\'s next move.',
            ],
        ],
    ],
];
