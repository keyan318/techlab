<?php
/*
 * Programming M3 Lesson 1: Reusable Routines
 */

$lessonData = [
    'title' => 'Reusable Routines',
    'objective' => 'The student can define and use functions in Python to encapsulate reusable code.',
    'simple_explanation' => "Functions allow you to package a block of code that performs a specific task and reuse it throughout your program. This makes code more organized and easier to maintain.",
    'astro_explanation' => "Astro uses functions for common tasks: calculate_fuel_remaining(), check_system_status(), send_telemetry_to_earth(). This avoids duplicating code and makes updates easier.",
    'code_example' => "# Defining a function\ndef greet(name):\n    return f\"Hello, {name}!\"\n\n# Using the function\nmessage = greet(\"Astro\")\nprint(message)\n\n# Function with multiple parameters\ndef calculate_fuel_percent(current, max_capacity):\n    return (current / max_capacity) * 100\n\npercent = calculate_fuel_percent(450, 1000)\nprint(f\"Fuel level: {percent}%\")",
    'interactive_exercise' => [
        'prompt' => 'Write a function that calculates the area of a rectangle given its width and height.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between a function\\'s parameters and arguments.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\ndef square(x):\n    return x * x\n\nresult = square(5)\nprint(result)',
            'options' => [
                'a' => '10',
                'b' => '25',
                'c' => '5',
                'd' => 'Error',
            ],
            'correct' => 'b',
            'explanation' => "The square function returns x multiplied by itself. For x=5, it returns 5*5=25.",
        ],
        [
            'question' => 'Which keyword is used to define a function in Python?',
            'options' => [
                'a' => 'function',
                'b' => 'def',
                'c' => 'func',
                'd' => 'define',
            ],
            'correct' => 'b',
            'explanation' => "In Python, the 'def' keyword is used to define a function.",
        ],
    ],
];

return $lessonData;
?>