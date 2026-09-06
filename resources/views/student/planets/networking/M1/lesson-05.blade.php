<?php
/*
 * Networking M1 Lesson 5: IP Addressing
 */

$lessonData = [
    'title' => 'IP Addressing',
    'objective' => 'The student can explain IPv4 addressing, subnetting, and how IP addresses are used in networks.',
    'simple_explanation' => "IP addresses are unique identifiers assigned to devices on a network. IPv4 uses 32-bit addresses written in dotted decimal notation (e.g., 192.168.1.1).",
    'astro_explanation' => "Each spacecraft, ground station, and device in Astro's network needs a unique IP address to ensure communications reach the correct destination.",
    'code_example' => "# IPv4 address examples\nprint(\"Device IP: 192.168.1.100\")\nprint(\"Network: 192.168.1.0/24\")\nprint(\"Broadcast: 192.168.1.255\")",
    'interactive_exercise' => [
        'prompt' => 'Convert the binary IP address 11000000.10101000.00000001.00000001 to dotted decimal notation.',
        'starter_code' => "# your answer here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between public and private IP addresses and give examples of each.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the decimal equivalent of the binary number 11000000?',
            'options' => [
                'a' => '192',
                'b' => '128',
                'c' => '64',
                'd' => '32',
            ],
            'correct' => 'a',
            'explanation' => "11000000 in binary equals 128 + 64 = 192 in decimal.",
        ],
        [
            'question' => 'Which of these is a private IP address range?',
            'options' => [
                'a' => '192.168.0.0 - 192.168.255.255',
                'b' => '100.64.0.0 - 100.127.255.255',
                'c' => '172.32.0.0 - 172.47.255.255',
                'd' => '169.254.0.0 - 169.254.255.255',
            ],
            'correct' => 'a',
            'explanation' => "The 192.168.0.0/16 range (192.168.0.0 to 192.168.255.255) is reserved for private networks as defined in RFC 1918.",
        ],
    ],
];

return $lessonData;
?>