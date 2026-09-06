<?php
/*
 * Networking M2 Lesson 4: Status Codes
 */

$lessonData = [
    'title' => 'Status Codes',
    'objective' => 'The student can explain common HTTP status codes and what they indicate about the outcome of a request.',
    'simple_explanation' => "HTTP status codes are three-digit numbers that indicate the result of an HTTP request. They are grouped by first digit: 1xx (informational), 2xx (success), 3xx (redirection), 4xx (client error), 5xx (server error).",
    'astro_explanation' => "When Astro communicates with mission control, status codes help it understand whether commands were successful, if data was received correctly, or if there are issues that need attention.",
    'code_example' => "# Common status codes\nprint(\"200: OK - Request successful\")\nprint(\"404: Not Found - Resource doesn\\'t exist\")\nprint(\"500: Internal Server Error - Server problem\")",
    'interactive_exercise' => [
        'prompt' => 'What status code would you expect to see if a webpage loads successfully?',
        'starter_code' => "# your answer here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between a 401 Unauthorized and a 403 Forbidden error.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What does a 200 status code indicate?',
            'options' => [
                'a' => 'Client error',
                'b' => 'Server error',
                'c' => 'Success',
                'd' => 'Redirection',
            ],
            'correct' => 'c',
            'explanation' => "A 200 status code indicates that the request was successful and the server returned the requested resource.",
        ],
        [
            'question' => 'Which status code indicates that a resource has been permanently moved?',
            'options' => [
                'a' => '301',
                'b' => '302',
                'c' => '404',
                'd' => '500',
            ],
            'correct' => 'a',
            'explanation' => "A 301 status code means 'Moved Permanently' and indicates that the requested resource has been assigned a new permanent URL.",
        ],
    ],
];

return $lessonData;
?>