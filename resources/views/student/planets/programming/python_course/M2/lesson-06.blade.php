<?php
/*
 * Programming M2 Lesson 6: Breaking the Loop
 */

$lessonData = [
    'title' => 'Breaking the Loop',
    'objective' => 'The student can use break and continue statements to control loop execution in Python.',
    'simple_explanation' => "The break statement exits a loop immediately, while the continue statement skips the rest of the current iteration and moves to the next one.",
    'astro_explanation' => "Astro uses break and continue in monitoring loops: break to exit when a critical issue is detected, continue to skip invalid sensor readings.",
    'code_example' => "# Using break to exit a loop\nfor i in range(10):\n    if i == 5:\n        break  # Exit the loop when i reaches 5\n    print(i)\n\n# Using continue to skip an iteration\nfor i in range(10):\n    if i % 2 == 0:\n        continue  # Skip even numbers\n    print(i)",  # Prints odd numbers: 1 3 5 7 9
    'interactive_exercise' => [
        'prompt' => 'Write a program that prints numbers from 1 to 10 but stops when it reaches 7.',
        'starter_code' => "for i in range(1, 11):\n    # your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain how you could use break and continue to process a list of user inputs, stopping when the user enters 'quit' and skipping empty inputs.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What will this code print?\nfor i in range(5):\n    if i == 2:\n        continue\n    print(i)',
            'options' => [
                'a' => '0 1 3 4',
                'b' => '0 1 2 3 4',
                'c' => '2',
                'd' => 'Error',
            ],
            'correct' => 'a',
            'explanation' => "When i equals 2, continue skips the print statement and moves to the next iteration. So we get 0, 1, 3, 4.",
        ],
        [
            'question' => 'What will this code print?\nfor i in range(5):\n    if i == 3:\n        break\n    print(i)\nprint(\"Loop ended\")',
            'options' => [
                'a' => '0 1 2 Loop ended',
                'b' => '0 1 2 3 4 Loop ended',
                'c' => '3 4 Loop ended',
                'd' => 'Error',
            ],
            'correct' => 'a',
            'explanation' => "When i equals 3, break exits the loop immediately, so we only get 0, 1, 2 before the loop ends.",
        ],
    ],
];

return $lessonData;
?>