<?php
/*
 * Networking M2 Lesson 2: DNS Resolution
 */

$lessonData = [
    'title' => 'DNS Resolution',
    'objective' => 'The student can explain how DNS translates domain names to IP addresses.',
    'simple_explanation' => "DNS (Domain Name System) is like the phonebook of the internet. It translates human-readable domain names (like google.com) into IP addresses that computers use to identify each other.",
    'astro_explanation' => "Astro uses DNS to find the IP addresses of mission control servers and other spacecraft, allowing it to establish connections even when the underlying IP addresses change.",
    'code_example' => "# Conceptual example\nprint(\"DNS lookup: astro-mission-control.nasa.gov → 203.0.113.45\")",
    'interactive_exercise' => [
        'prompt' => 'Explain what happens when you type a website address into your browser.',
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Describe the difference between an authoritative DNS server and a recursive DNS resolver.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the primary purpose of DNS?',
            'options' => [
                'a' => 'To encrypt web traffic',
                'b' => 'To translate domain names to IP addresses',
                'c' => 'To increase internet speed',
                'd' => 'To block malicious websites',
            ],
            'correct' => 'b',
            'explanation' => "DNS primarily functions as a directory service that translates human-readable domain names into machine-readable IP addresses.",
        ],
        [
            'question' => 'Which of these is a valid IP address?',
            'options' => [
                'a' => '999.999.999.999',
                'b' => '192.168.1.1',
                'c' => '256.0.0.1',
                'd' => '192.168.1.256',
            ],
            'correct' => 'b',
            'explanation' => "Valid IP addresses have four numbers separated by dots, each ranging from 0 to 255. 192.168.1.1 is valid, while the others contain numbers outside the 0-255 range.",
        ],
    ],
];

return $lessonData;
?>