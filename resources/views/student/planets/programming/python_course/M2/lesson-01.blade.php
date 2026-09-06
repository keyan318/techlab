<?php
/*
 * Programming M2 Lesson 1: Decision Points
 */

$lessonData = [
    'title' => 'Decision Points',
    'objective' => 'The student can write conditional statements in Python using if, elif, and else to make decisions in their code.',
    'simple_explanation' => "Conditional statements allow your program to make decisions and execute different code based on certain conditions. The most common is the if statement.",
    'astro_explanation' => "Astro uses conditional statements to make critical decisions: if temperature is too high, activate cooling systems; if oxygen levels are low, alert the crew.",
    'code_example' => "# Basic if statement\ntemperature = 25\nif temperature > 30:\n    print(\"It's hot! Activating cooling systems.\")\n\n# if-else statement\nif temperature > 30:\n    print(\"It's hot!\")\nelse:\n    print(\"Temperature is acceptable.\")\n\n# if-elif-else statement\nif temperature > 30:\n    print(\"It's hot!\")\nelif temperature < 10:\n    print(\"It's cold!\")\nelse:\n    print(\"Temperature is just right.\")",
    'interactive_exercise' => [
        'prompt' => 'Write a program that checks if a number is positive, negative, or zero.',
        'starter_code' => "number = int(input(\"Enter a number: \"))\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between = (assignment) and == (comparison) in Python. When would you use each?",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What will this code print?\nx = 10\nif x > 5:\n    print(\"x is greater than 5\")',
            'options' => [
                'a' => 'x is greater than 5',
                'b' => 'x is less than 5',
                'c' => 'Nothing',
                'd' => 'Error',
            ],
            'correct' => 'a',
            'explanation' => "Since x is 10, which is greater than 5, the condition is true and the print statement executes.",
        ],
        [
            'question' => 'Which of these is the correct syntax for an if-else statement in Python?',
            'options' => [
                'a' => 'if (x > 5) then print(\"big\") else print(\"small\")',
                'b' => 'if x > 5: print(\"big\") else: print(\"small\")',
                'c' => 'if x > 5:\n    print(\"big\")\nelse:\n    print(\"small\")',
                'd' => 'if x > 5 do { print(\"big\") } else { print(\"small\") }',
            ],
            'correct' => 'c',
            'explanation' => "Python uses indentation and colons for if-else statements, not parentheses or keywords like 'then' and 'do'.",
        ],
    ],
];

return $lessonData;
?>