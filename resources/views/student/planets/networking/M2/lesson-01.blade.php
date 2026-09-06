<?php
/*
 * Networking M2 Lesson 1: HTTP & HTTPS
 */

$lessonData = [
    'title' => 'HTTP & HTTPS',
    'objective' => 'The student can explain the difference between HTTP and HTTPS and why HTTPS is important for secure communication.',
    'simple_explanation' => "HTTP (Hypertext Transfer Protocol) is the foundation of data communication on the web. HTTPS is HTTP with encryption, making it secure for transmitting sensitive information.",
    'astro_explanation' => "Astro uses HTTPS to communicate securely with mission control, ensuring that commands and data cannot be intercepted or altered by malicious actors in space.",
    'code_example' => "# Conceptual example showing the difference\nprint(\"HTTP: http://mission-control.nasa.gov (unencrypted)\")\nprint(\"HTTPS: https://mission-control.nasa.gov (encrypted)\")",
    'interactive_exercise' => [
        'prompt' => 'List two reasons why HTTPS is more secure than HTTP.',
        'starter_code' => "# your answer here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain what SSL/TLS certificates are and how they enable HTTPS.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What does the \"S\" in HTTPS stand for?',
            'options' => [
                'a' => 'Super',
                'b' => 'Secure',
                'c' => 'Speedy',
                'd' => 'Simple',
            ],
            'correct' => 'b',
            'explanation' => "The 'S' in HTTPS stands for 'Secure', indicating that the connection is encrypted using SSL/TLS.",
        ],
        [
            'question' => 'Which port does HTTPS typically use by default?',
            'options' => [
                'a' => '80',
                'b' => '443',
                'c' => '22',
                'd' => '8080',
            ],
            'correct' => 'b',
            'explanation' => "HTTPS typically uses port 443 by default, while HTTP uses port 80.",
        ],
    ],
];

return $lessonData;
?>