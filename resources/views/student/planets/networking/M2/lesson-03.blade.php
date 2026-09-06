<?php
/*
 * Networking M2 Lesson 3: HTTP Methods
 */

$lessonData = [
    'title' => 'HTTP Methods',
    'objective' => 'The student can explain the purpose of common HTTP methods (GET, POST, PUT, DELETE) and when to use each.',
    'simple_explanation' => "HTTP methods define the action to be performed on a resource. GET retrieves data, POST submits new data, PUT updates existing data, and DELETE removes data.",
    'astro_explanation' => "Astro uses different HTTP methods when communicating with mission control: GET to retrieve status updates, POST to send new telemetry data, PUT to modify configuration settings, and DELETE to clear old data files.",
    'code_example' => "# Conceptual examples\nprint(\"GET: Retrieve current mission status\")\nprint(\"POST: Send new sensor readings\")\nprint(\"PUT: Update spacecraft configuration\")\nprint(\"DELETE: Remove old log files\")",
    'interactive_exercise' => [
        'prompt' => 'Match each HTTP method to its purpose:\n1. GET  2. POST  3. PUT  4. DELETE\nA. Update existing data\nB. Retrieve data\nC. Remove data\nD. Submit new data',
        'starter_code' => "# your answer here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain why GET requests should be safe and idempotent, while POST requests are not necessarily either.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'Which HTTP method is typically used to submit a web form?',
            'options' => [
                'a' => 'GET',
                'b' => 'POST',
                'c' => 'PUT',
                'd' => 'DELETE',
            ],
            'correct' => 'b',
            'explanation' => "HTML forms typically use POST method to submit data, especially when the data should not be visible in the URL or when submitting sensitive information.",
        ],
        [
            'question' => 'Which HTTP method would you use to update a user\\'s profile information?',
            'options' => [
                'a' => 'GET',
                'b' => 'POST',
                'c' => 'PUT',
                'd' => 'DELETE',
            ],
            'correct' => 'c',
            'explanation' => "PUT is commonly used to update existing resources, such as updating a user's profile information with new data.",
        ],
    ],
];

return $lessonData;
?>