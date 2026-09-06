<?php
/*
 * Networking M1 Lesson 6: Subnetting
 */

$lessonData = [
    'title' => 'Subnetting',
    'objective' => 'The student can explain how subnetting divides networks into smaller, more manageable segments.',
    'simple_explanation' => "Subnetting divides a larger network into smaller sub-networks (subnets) by borrowing bits from the host portion of an IP address to create additional network bits.",
    'astro_explanation' => "Astro uses subnetting to organize its spacecraft network, separating critical systems like life support from experimental payloads for better security and performance.",
    'code_example' => "# Subnetting example\nprint(\"Network: 192.168.1.0/24 (256 addresses)\")\nprint(\"Subnet 1: 192.168.1.0/26 (64 addresses)\")\nprint(\"Subnet 2: 192.168.1.64/26 (64 addresses)\")",
    'interactive_exercise' => [
        'prompt' => 'How many usable host addresses are in a /26 subnet?',
        'starter_code' => "# your answer here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain why subnetting improves network performance and security.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What does the \"/24\" in 192.168.1.0/24 represent?',
            'options' => [
                'a' => 'The network portion uses 24 bits',
                'b' => 'There are 24 hosts in the network',
                'c' => 'The subnet mask is 255.255.240.0',
                'd' => 'The network can support 24 subnets',
            ],
            'correct' => 'a',
            'explanation' => "The \"/24\" is CIDR notation indicating that the first 24 bits of the IP address are used for the network portion, leaving 8 bits for hosts.",
        ],
        [
            'question' => 'How many subnets are created when you borrow 3 bits for subnetting?',
            'options' => [
                'a' => '6',
                'b' => '8',
                'c' => '16',
                'd' => '32',
            ],
            'correct' => 'b',
            'explanation' => "Borrowing n bits creates 2^n subnets. Borrowing 3 bits creates 2^3 = 8 subnets.",
        ],
    ],
];

return $lessonData;
?>