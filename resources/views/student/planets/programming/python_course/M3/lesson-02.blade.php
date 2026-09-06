<?php
/*
 * Programming M3 Lesson 2: Passing Information
 */

$lessonData = [
    'title' => 'Passing Information',
    'objective' => 'The student can pass information to functions using parameters and return values using return statements.',
    'simple_explanation' => "Parameters allow functions to accept input data, and return statements allow functions to output results. This makes functions flexible and reusable.",
    'astro_explanation' => "Astro's functions accept sensor data as parameters and return processed information: process_temperature(raw_sensor_data) returns temperature_in_celsius.",
    'code_example' => "# Function with parameters\ncalculate_fuel_remaining(consumed, capacity):\n    return capacity - consumed\n\n# Function that returns a value\nremaining = calculate_fuel_remaining(250, 1000)\nprint(f\"Fuel remaining: {remaining} liters\")\n\n# Function with multiple return values\ndef get_coordinates():\n    x = 100\n    y = 200\n    return x, y\n\nposition_x, position_y = get_coordinates()\nprint(f\"Position: ({position_x}, {position_y})\")",
    'interactive_exercise' => [
        'prompt' => 'Write a function that converts Celsius to Fahrenheit using the formula: F = C × 9/5 + 32.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain what happens if you forget to include a return statement in a function that is supposed to return a value.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\ndef add_numbers(a, b):\n    return a + b\n\nresult = add_numbers(3, 4)\nprint(result)',
            'options' => [
                'a' => '3',
                'b' => '4',
                'c' => '7',
                'd' => 'Error',
            ],
            'correct' => 'c',
            'explanation' => "The add_numbers function returns the sum of its parameters. For inputs 3 and 4, it returns 7.",
        ],
        [
            'question' => 'What does this function return?\ndef greet():\n    print(\"Hello, World!\")\n\nresult = greet()\nprint(result)',
            'options' => [
                'a' => '\"Hello, World!\"',
                'b' => 'None',
                'c' => '\"Hello, World!\"None',
                'd' => 'Error',
            ],
            'correct' => 'b',
            'explanation' => "The greet function prints a message but doesn't explicitly return a value, so it returns None by default.",
        ],
    ],
];

return $lessonData;
?>