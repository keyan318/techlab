<?php
/*
 * Programming M1 Lesson 3: Data Types
 */

$lessonData = [
    'title' => 'Data Types',
    'objective' => 'The student can distinguish between different data types in Python and use them appropriately.',
    'simple_explanation' => "In Python, every piece of data has a type. The most common types are numbers (integers and floats), text (strings), and booleans (True/False). Knowing the type helps you understand what operations you can perform.",
    'astro_explanation' => "Astro's sensors send back different kinds of data: temperature readings (numbers), status messages (text), and whether systems are online or offline (booleans). Astro needs to handle each type correctly to interpret the data properly.",
    'code_example' => "# Different data types in Python\nage = 42                    # integer\ntemperature = -10.5         # float\nname = \"Astro\"              # string\nis_online = True            # boolean\n\nprint(age)\nprint(temperature)\nprint(name)\nprint(is_online)",
    'interactive_exercise' => [
        'prompt' => 'Create variables for your name (string), age (integer), and whether you are a student (boolean). Then print them all.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain what happens when you try to add a string and an integer in Python. Give an example.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\nprint(type(42))',
            'options' => [
                'a' => '<class \\'int\\'>',
                'b' => '<class \\'str\\'>',
                'c' => '<class \\'float\\'>',
                'd' => '42',
            ],
            'correct' => 'a',
            'explanation' => "The type() function returns the type of the value. For integer 42, it returns <class 'int'>.",
        ],
        [
            'question' => 'Which of these is a boolean value in Python?',
            'options' => [
                'a' => '\"True\"',
                'b' => '0',
                'c' => 'True',
                'd' => '1',
            ],
            'correct' => 'c',
            'explanation' => "In Python, True and False (capitalized) are the boolean values. \"True\" in quotes is a string, and 0 and 1 are integers.",
        ],
    ],
];

return $lessonData;
?>