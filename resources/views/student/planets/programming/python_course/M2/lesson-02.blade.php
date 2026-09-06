<?php
/*
 * Programming M2 Lesson 2: Branching Paths
 */

$lessonData = [
    'title' => 'Branching Paths',
    'objective' => 'The student can write nested conditional statements to handle complex decision-making scenarios.',
    'simple_explanation' => "Sometimes decisions depend on multiple conditions. Nesting if statements allows you to check for combinations of conditions.",
    'astro_explanation' => "Astro might need to check multiple conditions before taking action: if it's daytime AND solar panels are deployed AND battery is low, then charge batteries.",
    'code_example' => "# Nested conditionals\nis_daytime = True\npanels_deployed = True\nbattery_low = True\n\nif is_daytime:\n    if panels_deployed:\n        if battery_low:\n            print(\"Charging batteries from solar power\")\n        else:\n            print(\"Solar power available, batteries full\")\n    else:\n        print(\"Panels not deployed - cannot charge\")\nelse:\n    print(\"Nighttime - using battery power\")",
    'interactive_exercise' => [
        'prompt' => 'Write a program that determines if a person can ride a roller coaster based on height (>=48 inches) and age (>=12 years).',
        'starter_code' => "height = int(input(\"Enter height in inches: \"))\nage = int(input(\"Enter age: \"))\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain how logical operators (and, or, not) can sometimes eliminate the need for nested conditionals.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What will this code print?\nx = 5\ny = 10\nif x > 0:\n    if y > 0:\n        print(\"Both positive\")',
            'options' => [
                'a' => 'Both positive',
                'b' => 'x is positive',
                'c' => 'Nothing',
                'd' => 'Error',
            ],
            'correct' => 'a',
            'explanation' => "Since both x (5) and y (10) are greater than 0, both conditions are true and the nested if statement executes.",
        ],
        [
            'question' => 'Which of these statements about indentation in Python is true?',
            'options' => [
                'a' => 'Indentation is optional and only for readability',
                'b' => 'Indentation must be exactly 2 spaces',
                'c' => 'Indentation defines blocks of code in Python',
                'd' => 'You can mix tabs and spaces for indentation',
            ],
            'correct' => 'c',
            'explanation' => "In Python, indentation is not just for readability - it defines the grouping of statements into blocks (like if-else bodies).",
        ],
    ],
];

return $lessonData;
?>