<?php
/*
 * Networking M1 Lesson 4: TCP/IP Suite
 */

$lessonData = [
    'title' => 'TCP/IP Suite',
    'objective' => 'The student can explain the TCP/IP protocol suite and how it enables internet communication.',
    'simple_explanation' => "TCP/IP (Transmission Control Protocol/Internet Protocol) is the foundational protocol suite of the internet, combining TCP for reliable delivery and IP for addressing and routing.",
    'astro_explanation' => "Astro uses TCP/IP to communicate with mission control and other spacecraft, benefiting from its robustness and widespread adoption in space and ground systems.",
    'code_example' => "# TCP/IP concepts\nprint(\"IP: Handles addressing and routing of packets\")\nprint(\"TCP: Ensures reliable, ordered delivery of data\")\nprint(\"UDP: Provides faster, connectionless delivery for time-sensitive data\")",
    'interactive_exercise' => [
        'prompt' => 'Explain the difference between TCP and UDP and when you would use each.',
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Describe what happens during a TCP three-way handshake.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What does IP stand for in TCP/IP?',
            'options' => [
                'a' => 'Internet Protocol',
                'b' => 'Internal Processing',
                'c' => 'Information Packet',
                'd' => 'Internet Path',
            ],
            'correct' => 'a',
            'explanation' => "IP stands for Internet Protocol, which is responsible for addressing and routing packets across networks.",
        ],
        [
            'question' => 'Which protocol is used for reliable, ordered delivery of data?',
            'options' => [
                'a' => 'HTTP',
                'b' => 'TCP',
                'c' => 'UDP',
                'd' => 'DNS',
            ],
            'correct' => 'b',
            'explanation' => "TCP (Transmission Control Protocol) provides reliable, ordered, and error-checked delivery of data between applications.",
        ],
    ],
];

return $lessonData;
?>