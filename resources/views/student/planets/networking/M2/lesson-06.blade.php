<?php
/*
 * Networking M2 Lesson 6: Caching
 */

$lessonData = [
    'title' => 'Caching',
    'objective' => 'The student can explain how caching improves network performance and reduces latency.',
    'simple_explanation' => "Caching stores copies of frequently accessed data closer to the user, reducing the need to fetch it from the original source every time.",
    'astro_explanation' => "Due to the vast distances in space, caching is crucial for Astro's operations. Frequently used data like star maps or mission parameters can be cached onboard to reduce reliance on slow Earth communications.",
    'code_example' => "# Conceptual caching example\nprint(\"Checking cache for mission parameters...\")\nif (cache_hit) {\n    print(\"Using cached data - fast access!\")\n} else {\n    print(\"Fetching from server and storing in cache\")\n}",
    'interactive_exercise' => [
        'prompt' => 'Explain how a web browser cache works and why it improves browsing speed.',
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Describe the difference between client-side caching and server-side caching.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the primary benefit of caching in network applications?',
            'options' => [
                'a' => 'Increased security',
                'b' => 'Reduced latency and bandwidth usage',
                'c' => 'Improved data accuracy',
                'd' => 'Better encryption',
            ],
            'correct' => 'b',
            'explanation' => "Caching reduces latency by storing data closer to the user and decreases bandwidth usage by avoiding repeated downloads of the same data.",
        ],
        [
            'question' => 'Which of these describes a cache miss?',
            'options' => [
                'a' => 'The requested data is found in the cache',
                'b' => 'The requested data is not in the cache and must be fetched from the source',
                'c' => 'The cache is full and needs to be cleared',
                'd' => 'The cached data has expired and needs to be updated',
            ],
            'correct' => 'b',
            'explanation' => "A cache miss occurs when the requested data is not found in the cache, requiring the system to fetch it from the original source.",
        ],
    ],
];

return $lessonData;
?>