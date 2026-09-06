<?php
/*
 * Networking M2 Lesson 5: Error Handling
 */

$lessonData = [
    'title' => 'Error Handling',
    'objective' => 'The student can explain common network error conditions and how to handle them.',
    'simple_explanation' => "Network communications can fail for many reasons: timeouts, connection refusals, DNS failures, etc. Good applications handle these errors gracefully.",
    'astro_explanation' => "When Astro communicates across vast distances in space, errors are inevitable due to signal degradation, cosmic interference, or hardware issues. Robust error handling ensures mission continuity.",
    'code_example' => "# Conceptual error handling\nprint(\"Attempting to connect to mission control...\")\nif (!connection_successful) {\n    print(\"Error: Connection timeout - retrying...\")\n    # retry logic here\n}",
    'interactive_exercise' => [
        'prompt' => 'List three common network errors and what they might indicate.',
        'starter_code' => "# your answer here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between a transient error and a permanent error in networking. Give examples of each.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What does a 504 Gateway Timeout error indicate?',
            'options' => [
                'a' => 'The server is not responding in time',
                'b' => 'The client sent an invalid request',
                'c' => 'The server is temporarily overloaded',
                'd' => 'The requested resource was not found',
            ],
            'correct' => 'a',
            'explanation' => "A 504 Gateway Timeout error indicates that a server acting as a gateway or proxy did not receive a timely response from an upstream server.",
        ],
        [
            'question' => 'Which of these is NOT a common cause of network errors?',
            'options' => [
                'a' => 'Physical cable damage',
                'b' => 'Incorrect DNS configuration',
                'c' => 'Using HTTPS instead of HTTP',
                'd' => 'Firewall blocking traffic',
            ],
            'correct' => 'c',
            'explanation' => "Using HTTPS instead of HTTP is not a cause of network errors; in fact, HTTPS is generally more secure and reliable for sensitive communications.",
        ],
    ],
];

return $lessonData;
?>