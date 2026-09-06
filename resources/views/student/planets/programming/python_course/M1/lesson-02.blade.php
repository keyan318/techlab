<?php
/*
 * Programming M1 Lesson 2: Variables & Memory
 */

$lessonData = [
    'title' => 'Variables & Memory',
    'objective' => 'The student can create and use variables to store and manipulate data in Python.',
    'simple_explanation' => "Variables are like labeled boxes where you can store information. You give each variable a name, and then you can put values in it, change those values, and retrieve them later. In Python, you create a variable by assigning a value to it using the equals sign (=).",
    'astro_explanation' => "Astro needs to remember important information like fuel levels, temperature readings, and mission elapsed time. Variables allow Astro to store these values and use them later in calculations and decision-making.",
    'code_example' => "# Creating variables\nfuel_level = 75\ntemperature = -20\nmission_time = 0\n\n# Using variables\nprint(\"Fuel level:\", fuel_level, \"%\")\nprint(\"Temperature:\", temperature, \"°C\")",
    'interactive_exercise' => [
        'prompt' => 'Create a variable called "captain_name" and assign it the value "Nova Star". Then print the captains name.',
        'starter_code' => "# your code here",
        'expected_output' => "Nova Star",
    ],
    'challenge' => [
        'prompt' => "Create three variables to store Astro's current coordinates (x, y, z) and then print them in the format: (x, y, z)",
        'starter_code' => "# your code here",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the correct way to create a variable named "score" with the value 100 in Python?',
            'options' => [
                'a' => 'variable score = 100',
                'b' => 'score = 100',
                'c' => 'var score = 100',
                'd' => 'int score = 100',
            ],
            'correct' => 'b',
            'explanation' => "In Python, you create a variable by simply assigning a value to it using the equals sign. No keyword is needed.",
        ],
        [
            'question' => 'If you set x = 5 and then later set x = 10, what is the value of x?',
            'options' => [
                'a' => 5,
                'b' => 10,
                'c' => 15,
                'd' => 'It causes an error',
            ],
            'correct' => 'b',
            'explanation' => "When you assign a new value to an existing variable, the old value is replaced. So x would be 10.",
        ],
    ],
];

return $lessonData;
?>