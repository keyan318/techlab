<?php
/*
 * Programming M5 Lesson 5: Time & Scheduling Concepts
 */

$lessonData = [
    'title' => 'Time & Scheduling Concepts',
    'objective' => 'The student can work with time, dates, and scheduling in Python using the time and datetime modules.',
    'simple_explanation' => "Python provides modules for working with time and dates: time for Unix timestamps and basic time functions, and datetime for more complex date and time manipulations.",
    'astro_explanation' => "Astro uses time functions to timestamp sensor data, schedule regular tasks like system checks, and calculate mission elapsed time for logging and analysis.",
    'code_example' => "# Working with time\nimport time\n\n# Current time as Unix timestamp\nnow = time.time()\nprint(f\"Current timestamp: {now}\")\n\n# Current time as formatted string\nlocal_time = time.ctime(now)\nprint(f\"Local time: {local_time}\")\n\n# Measuring elapsed time\nstart = time.time()\n# ... some operation ...\nelapsed = time.time() - start\nprint(f\"Operation took {elapsed:.2f} seconds\")\n\n# Using datetime for more complex operations\nfrom datetime import datetime, timedelta\n\n# Current date and time\nnow = datetime.now()\nprint(f\"Current datetime: {now}\")\n\n# Specific date and time\nchristmas = datetime(2023, 12, 25, 0, 0, 0)\nprint(f\"Christmas: {christmas}\")\n\n# Date arithmetic\ntomorrow = now + timedelta(days=1)\nprint(f\"Tomorrow: {tomorrow}\")\n\n# Formatting dates\nformatted = now.strftime(\"%Y-%m-%d %H:%M:%S\")\nprint(f\"Formatted: {formatted}\")",
    'interactive_exercise' => [
        'prompt' => 'Write a program that prints the current date and time in the format \"YYYY-MM-DD HH:MM:SS\".',
        'starter_code' => "from datetime import datetime\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between the time.time() function and datetime.now() in Python.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What does time.time() return?',
            'options' => [
                'a' => 'The current time as a formatted string',
                'b' => 'The number of seconds since January 1, 1970 (Unix epoch)',
                'c' => 'The current date and time as a datetime object',
                'd' => 'The number of milliseconds since the program started',
            ],
            'correct' => 'b',
            'explanation' => "time.time() returns the current time as a floating-point number representing seconds since the Unix epoch (January 1, 1970).",
        ],
        [
            'question' => 'Which method would you use to convert a datetime object to a readable string?',
            'options' => [
                'a' => 'strftime()',
                'b' => 'strptime()',
                'c' => 'time()',
                'd' => 'timestamp()',
            ],
            'correct' => 'a',
            'explanation' => "strftime() (format time) converts a datetime object to a string according to a specified format.",
        ],
    ],
];

return $lessonData;
?>