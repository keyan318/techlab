<?php
/*
 * Programming M4 Lesson 1: Collections
 */

$lessonData = [
    'title' => 'Collections',
    'objective' => 'The student can explain what collections are in Python and use lists to store and manipulate groups of related data.',
    'simple_explanation' => "Collections are data structures that hold multiple items. The most common collection in Python is a list, which is an ordered, changeable collection that allows duplicate members.",
    'astro_explanation' => "Astro uses lists to store collections of data: telemetry readings over time, lists of active sensors, or sequences of commands to execute.",
    'code_example' => "# Creating lists\nempty_list = []\nnumbers = [1, 2, 3, 4, 5]\ncrew_members = [\"Commander\", \"Pilot\", \"Specialist\"]\nmixed_list = [42, \"Hello\", 3.14, True]\n\n# Accessing list items\nprint(numbers[0])  # First item: 1\nprint(numbers[-1]) # Last item: 5\n\n# Modifying lists\nnumbers.append(6)  # Add to end\nnumbers.insert(0, 0)  # Insert at beginning\nnumbers[2] = 99  # Change existing item\n\n# List operations\nprint(len(numbers))  # Length: 7\nprint(99 in numbers)  # Membership test: True\n",
    'interactive_exercise' => [
        'prompt' => 'Create a list of your favorite programming languages and print the second item in the list.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between the append() and insert() methods for lists.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\ncolors = [\"red\", \"green\", \"blue\"]\ncolors.append(\"yellow\")\nprint(colors[2])',
            'options' => [
                'a' => 'red',
                'b' => 'green',
                'c' => 'blue',
                'd' => 'yellow',
            ],
            'correct' => 'c',
            'explanation' => "After appending 'yellow', the list is ['red', 'green', 'blue', 'yellow']. Index 2 refers to the third item, which is 'blue'.",
        ],
        [
            'question' => 'Which of these creates an empty list?',
            'options' => [
                'a' => 'list = ()',
                'b' => 'list = []',
                'c' => 'list = {}',
                'd' => 'list = \"\"',
            ],
            'correct' => 'b',
            'explanation' => "Square brackets [] create an empty list in Python. Parentheses () create a tuple, curly braces {} create a dictionary or set, and quotes create a string.",
        ],
    ],
];

return $lessonData;
?>