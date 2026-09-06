<?php
/*
 * Programming M4 Lesson 2: List Operations
 */

$lessonData = [
    'title' => 'List Operations',
    'objective' => 'The student can perform common list operations like sorting, reversing, and slicing in Python.',
    'simple_explanation' => "Python provides built-in methods and functions to manipulate lists: sorting items, reversing order, extracting slices, and more.",
    'astro_explanation' => "Astro uses list operations to organize data: sorting sensor readings by time, reversing command queues for LIFO processing, or slicing telemetry data for analysis windows.",
    'code_example' => "# Sorting lists\nnumbers = [3, 1, 4, 1, 5, 9, 2]\nnumbers.sort()  # Sorts in place: [1, 1, 2, 3, 4, 5, 9]\nprint(sorted(numbers, reverse=True))  # Returns new sorted list: [9, 5, 4, 3, 2, 1, 1]\n\n# Reversing lists\nletters = ['a', 'b', 'c', 'd']\nletters.reverse()  # In-place reverse: ['d', 'c', 'b', 'a']\n\n# Slicing lists\nnumbers = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]\nprint(numbers[2:5])   # Items 2,3,4: [2, 3, 4]\nprint(numbers[:3])    # First three: [0, 1, 2]\nprint(numbers[::2])   # Every second: [0, 2, 4, 6, 8]\nprint(numbers[::-1])  # Reversed: [9, 8, 7, 6, 5, 4, 3, 2, 1, 0]\n",
    'interactive_exercise' => [
        'prompt' => 'Create a list of numbers from 10 to 1, then sort it in ascending order.',
        'starter_code' => "numbers = [10, 9, 8, 7, 6, 5, 4, 3, 2, 1]\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between the sort() method and the sorted() function in Python.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\nnums = [5, 2, 8, 1, 9]\nnums.sort()\nprint(nums[3])',
            'options' => [
                'a' => '1',
                'b' => '2',
                'c' => '5',
                'd' => '8',
            ],
            'correct' => 'c',
            'explanation' => "After sorting, the list becomes [1, 2, 5, 8, 9]. Index 3 refers to the fourth item, which is 8.",
        ],
        [
            'question' => 'What does this code print?\nletters = [\"a\", \"b\", \"c\", \"d\", \"e\"]\nprint(letters[1:4])',
            'options' => [
                'a' => '[\"a\", \"b\", \"c\"]',
                'b' => '[\"b\", \"c\", \"d\"]',
                'c' => '[\"c\", \"d\", \"e\"]',
                'd' => '[\"a\", \"b\", \"c\", \"d\"]',
            ],
            'correct' => 'b',
            'explanation' => "Slice [1:4] includes indices 1, 2, and 3, which are 'b', 'c', and 'd'.",
        ],
    ],
];

return $lessonData;
?>