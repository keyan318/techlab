<?php
/*
 * Programming M1 Lesson 4: Expressions & Operators
 */

$lessonData = [
    'title' => 'Expressions & Operators',
    'objective' => 'The student can write and evaluate expressions using arithmetic and comparison operators in Python.',
    'simple_explanation' => "An expression is a combination of values, variables, and operators that Python evaluates to produce a result. Operators include math symbols like +, -, *, / and comparison symbols like ==, >, <.",
    'astro_explanation' => "Astro uses expressions to calculate things like fuel consumption, trajectory angles, and system status. For example, Astro might calculate remaining fuel as: fuel_used / fuel_capacity * 100 to get a percentage.",
    'code_example' => "# Arithmetic expressions\ntotal_fuel = 1000\nused_fuel = 375\nremaining_fuel = total_fuel - used_fuel\nfuel_percentage = remaining_fuel / total_fuel * 100\n\nprint(f\"Remaining fuel: {remaining_fuel} liters\")\nprint(f\"Fuel percentage: {fuel_percentage}%\")\n\n# Comparison expressions\ntemperature = 22\nis_hot = temperature > 25\nprint(f\"Is it hot? {is_hot}\")",
    'interactive_exercise' => [
        'prompt' => 'Calculate the area of a rectangle with width 8 and height 5, then print whether the area is greater than 30.',
        'starter_code' => "width = 8\nheight = 5\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between = and == in Python. When would you use each?",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\nprint(10 + 3 * 2)',
            'options' => [
                'a' => '26',
                'b' => '16',
                'c' => '19',
                'd' => '36',
            ],
            'correct' => 'b',
            'explanation' => "Python follows order of operations: multiplication before addition. So 3 * 2 = 6, then 10 + 6 = 16.",
        ],
        [
            'question' => 'Which expression evaluates to True?',
            'options' => [
                'a' => '5 == \"5\"',
                'b' => '10 > 3',
                'c' => '7 <= 5',
                'd' => '4 != 4',
            ],
            'correct' => 'b',
            'explanation' => "10 > 3 evaluates to True because 10 is greater than 3. The others are False: 5 != '5' (different types), 7 <= 5 is False, and 4 != 4 is False.",
        ],
    ],
];

return $lessonData;
?>