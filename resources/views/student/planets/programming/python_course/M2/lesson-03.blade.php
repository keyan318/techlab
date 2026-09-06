<?php
/*
 * Programming M2 Lesson 3: Combining Conditions
 */

$lessonData = [
    'title' => 'Combining Conditions',
    'objective' => 'The student can use logical operators (and, or, not) to combine multiple conditions in a single if statement.',
    'simple_explanation' => "Instead of nesting if statements, you can use logical operators to check multiple conditions at once. 'and' requires both conditions to be true, 'or' requires at least one to be true.",
    'astro_explanation' => "Astro uses combined conditions for complex logic: if (system_overheated AND cooling_available) OR manual_override, then activate emergency cooling.",
    'code_example' => "# Using 'and' operator\nif temperature > 30 and humidity > 80:\n    print(\"Hot and humid - activate both cooling and dehumidifier\")\n\n# Using 'or' operator\nif is_weekend or is_holiday:\n    print(\"No work today!\")\n\n# Using 'not' operator\nif not is_maintenance_mode:\n    print(\"System operational\")",
    'interactive_exercise' => [
        'prompt' => 'Write a program that checks if a number is between 1 and 100 (inclusive).',
        'starter_code' => "number = int(input(\"Enter a number: \"))\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain De Morgan's laws and how they relate to combining conditions in programming.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What does this expression evaluate to?\nTrue and False or True',
            'options' => [
                'a' => 'True',
                'b' => 'False',
                'c' => 'Error',
                'd' => 'None',
            ],
            'correct' => 'a',
            'explanation' => "Python evaluates 'and' before 'or'. So: (True and False) or True = False or True = True.",
        ],
        [
            'question' => 'Which of these is equivalent to \"not (A and B)\"?',
            'options' => [
                'a' => 'not A and not B',
                'b' => 'not A or not B',
                'c' => 'A and not B',
                'd' => 'A or B',
            ],
            'correct' => 'b',
            'explanation' => "This is De Morgan's law: not (A and B) is equivalent to (not A) or (not B).",
        ],
    ],
];

return $lessonData;
?>