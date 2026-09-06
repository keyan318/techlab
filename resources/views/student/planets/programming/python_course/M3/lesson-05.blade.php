<?php
/*
 * Programming M3 Lesson 5: Sanity Checks
 */

$lessonData = [
    'title' => 'Sanity Checks',
    'objective' => 'The student can write validation checks to ensure data is reasonable before processing it.',
    'simple_explanation' => "Sanity checks are validation tests that ensure input data makes sense before using it in calculations or operations. This prevents garbage-in, garbage-out scenarios.",
    'astro_explanation' => "Astro performs sanity checks on sensor data: if a temperature reading is -1000°C or 10000°C, it's likely invalid and should be investigated or ignored.",
    'code_example' => "# Input validation\ndef calculate_bmi(weight_kg, height_m):\n    if weight_kg <= 0:\n        raise ValueError(\"Weight must be positive\")\n    if height_m <= 0 or height_m > 3:  # Reasonable human height\n        raise ValueError(\"Height must be between 0 and 3 meters\")\n    return weight_kg / (height_m ** 2)\n\n# Range checking\ndef process_temperature(temp_celsius):\n    if temp_celsius < -273.15:  # Absolute zero\n        raise ValueError(\"Temperature below absolute zero is impossible\")\n    if temp_celsius > 1000:  # Unreasonable for most equipment\n        raise ValueError(\"Temperature exceeds maximum sensor range\")\n    return temp_celsius\n\n# Type checking\ndef process_list(items):\n    if not isinstance(items, list):\n        raise TypeError(\"Input must be a list\")\n    return len(items)",
    'interactive_exercise' => [
        'prompt' => 'Write a function that calculates the area of a circle, but only accepts positive radius values.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain why it's important to validate input data early in a function rather than waiting until later in the processing.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\ndef square_root(x):\n    if x < 0:\n        raise ValueError(\"Cannot calculate square root of negative number\")\n    return x ** 0.5\n\nresult = square_root(-4)',
            'options' => [
                'a' => '2.0',
                'b' => '-2.0',
                'c' => 'ValueError: Cannot calculate square root of negative number',
                'd' => 'Error',
            ],
            'correct' => 'c',
            'explanation' => "The function raises a ValueError when given a negative input, which is what gets printed as the output.",
        ],
        [
            'question' => 'Which of these is NOT a good reason to perform sanity checks on input data?',
            'options' => [
                'a' => 'To prevent crashes from invalid input',
                'b' => 'To make the program run faster',
                'c' => 'To ensure data quality and reliability',
                'd' => 'To provide clear error messages to users',
            ],
            'correct' => 'b',
            'explanation' => "While sanity checks add a small amount of overhead, their primary purpose is not to make the program run faster but to ensure correctness and reliability.",
        ],
    ],
];

return $lessonData;
?>