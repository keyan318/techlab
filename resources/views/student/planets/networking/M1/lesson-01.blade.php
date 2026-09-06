<?php
/*
 * Networking M1 Lesson 1: Welcome to Networking
 */

$lessonData = [
    'title' => 'Welcome to Networking',
    'objective' => 'The student can explain what a network is and identify different types of networks.',
    'simple_explanation' => "A network is a collection of devices connected together to share resources and information. Think of it like a digital neighborhood where computers, phones, and other devices can talk to each other.",
    'astro_explanation' => "Astro needs to understand networks to communicate with mission control, send data back to Earth, and coordinate with other spacecraft. Networks are the nervous system of space exploration!",
    'code_example' => "# This is a conceptual example - networking concepts don't have direct code equivalents\n# But we can simulate network concepts!\nprint(\"Welcome to Networking Nebula!\")\nprint(\"Today we'll learn how devices communicate across networks.\")",
    'interactive_exercise' => [
        'prompt' => 'List three devices that could be part of a home network.',
        'starter_code' => "# your answer here",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between a LAN and a WAN in your own words.",
        'starter_code' => "# your explanation here",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the primary purpose of a computer network?',
            'options' => [
                'a' => 'To make computers look fancy',
                'b' => 'To share resources and information between devices',
                'c' => 'To increase the speed of individual computers',
                'd' => 'To store more data on each device',
            ],
            'correct' => 'b',
            'explanation' => "Networks exist to allow devices to communicate and share resources like files, printers, and internet connections.",
        ],
        [
            'question' => 'Which of these is an example of a network?',
            'options' => [
                'a' => 'A single laptop computer',
                'b' => 'A smartphone not connected to anything',
                'c' => 'Several computers connected to a router in an office',
                'd' => 'A USB flash drive',
            ],
            'correct' => 'c',
            'explanation' => "A network requires multiple devices that are connected together so they can communicate.",
        ],
    ],
];

return $lessonData;
?>