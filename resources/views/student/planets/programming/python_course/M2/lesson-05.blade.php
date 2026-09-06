<?php
/*
 * Programming M2 Lesson 5: Counting Loops
 */

$lessonData = [
    'title' => 'Counting Loops',
    'objective' => 'The student can write for loops in Python to repeat actions a specific number of times.',
    'simple_explanation' => "A for loop is used to iterate over a sequence (like a list, string, or range) and execute a block of code for each item in the sequence.",
    'astro_explanation' => "Astro uses for loops to process collections of data: for each sensor reading, check if it's within normal range; for each command in a queue, execute it.",
    'code_example' => "# Basic for loop with range\nfor i in range(5):\n    print(\"Iteration:\", i)\n\n# For loop with a list\nfruits = [\"apple\", \"banana\", \"cherry\"]\nfor fruit in fruits:\n    print(\"I like\", fruit)\n\n# For loop with a string\nfor letter in \"Astro\":\n    print(\"Letter:\", letter)",
    'interactive_exercise' => [
        'prompt' => 'Write a program that prints the squares of numbers from 1 to 5 using a for loop.',
        'starter_code' => "for i in range(1, 6):\n    # your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain how you can use the enumerate() function to get both the index and value when iterating over a list.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What will this code print?\nfor i in range(3):\n    print(i * 2)',
            'options' => [
                'a' => '0 2 4',
                'b' => '2 4 6',
                'c' => '3 6 9',
                'd' => 'Error',
            ],
            'correct' => 'a',
            'explanation' => "range(3) produces the sequence 0, 1, 2. Multiplying each by 2 gives 0, 2, 4.",
        ],
        [
            'question' => 'Which of these will create a loop that runs exactly 5 times?',
            'options' => [
                'a' => 'for i in range(5):',
                'b' => 'for i in range(1, 5):',
                'c' => 'for i in range(0, 5, 2):',
                'd' => 'for i in range(5, 0, -1):',
            ],
            'correct' => 'a',
            'explanation' => "range(5) produces the sequence 0, 1, 2, 3, 4 - exactly 5 values.",
        ],
    ],
];

return $lessonData;
?>