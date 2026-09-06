<?php
/*
 * Programming M2 Lesson 4: Repeating Signals
 */

$lessonData = [
    'title' => 'Repeating Signals',
    'objective' => 'The student can write while loops in Python to repeat actions until a condition changes.',
    'simple_explanation' => "A while loop continues to execute its block of code as long as a specified condition remains true. It's useful for repeating actions until something changes.",
    'astro_explanation' => "Astro uses while loops for continuous monitoring: while mission is active, keep checking sensors and sending status updates to Earth.",
    'code_example' => "# Basic while loop\ncount = 0\nwhile count < 5:\n    print(\"Count is:\", count)\n    count = count + 1\n\n# While loop with user input\nresponse = \"\"\nwhile response.lower() != \"yes\":\n    response = input(\"Have you completed the task? (yes/no) \")\nprint(\"Great! Moving on...\")",
    'interactive_exercise' => [
        'prompt' => 'Write a program that counts from 1 to 10 using a while loop.',
        'starter_code' => "count = 1\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between a while loop and a for loop in Python. When would you use each?",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What will this code print?\nx = 1\nwhile x <= 3:\n    print(x)\n    x = x + 1',
            'options' => [
                'a' => '1 2 3',
                'b' => '1 2',
                'c' => '2 3 4',
                'd' => 'Error',
            ],
            'correct' => 'a',
            'explanation' => "The loop prints x (starting at 1), then increments x by 1. It continues while x is less than or equal to 3, so it prints 1, 2, and 3.",
        ],
        [
            'question' => 'What happens if the condition in a while loop is never false?',
            'options' => [
                'a' => 'The loop runs once and stops',
                'b' => 'The loop runs forever (infinite loop)',
                'c' => 'The program crashes',
                'd' => 'The loop is skipped',
            ],
            'correct' => 'b',
            'explanation' => "If the condition never becomes false, the while loop will continue executing indefinitely, creating an infinite loop.",
        ],
    ],
];

return $lessonData;
?>