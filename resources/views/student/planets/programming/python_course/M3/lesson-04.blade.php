<?php
/*
 * Programming M3 Lesson 4: Anticipating Failure
 */

$lessonData = [
    'title' => 'Anticipating Failure',
    'objective' => 'The student can write try-except blocks in Python to handle exceptions and prevent program crashes.',
    'simple_explanation' => "Exceptions are errors that occur during program execution. Try-except blocks allow you to catch and handle these exceptions gracefully instead of letting your program crash.",
    'astro_explanation' => "Astro uses exception handling to deal with unexpected situations: if a sensor fails, catch the error and use backup systems or safe defaults.",
    'code_example' => "# Basic exception handling\ntry:\n    result = 10 / 0\nexcept ZeroDivisionError:\n    print(\"Cannot divide by zero!\")\n\n# Handling multiple exception types\ntry:\n    age = int(input(\"Enter your age: \"))\nexcept ValueError:\n    print(\"Please enter a valid number!\")\nexcept KeyboardInterrupt:\n    print(\\nInput cancelled.\")\n\n# Using else and finally\ntry:\n    file = open(\"data.txt\", \"r\")\nexcept FileNotFoundError:\n    print(\"File not found!\")\nelse:\n    print(\"File opened successfully!\")\n    file.close()\nfinally:\n    print(\"This always executes.\")",
    'interactive_exercise' => [
        'prompt' => 'Write a program that asks for two numbers and divides them, handling the case where the user tries to divide by zero.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between catching specific exceptions and using a bare except clause.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\ntry:\n    print(\"Hello\")\nexcept:\n    print(\"Error\")\nelse:\n    print(\"No error\")',
            'options' => [
                'a' => 'Hello\nNo error',
                'b' => 'Hello\nError',
                'c' => 'Error\nNo error',
                'd' => 'Hello',
            ],
            'correct' => 'a',
            'explanation' => "Since no exception occurs in the try block, the else block executes after the try block completes successfully.",
        ],
        [
            'question' => 'Which exception would be raised by this code?\nint(\"hello\")',
            'options' => [
                'a' => 'ZeroDivisionError',
                'b' => 'ValueError',
                'c' => 'FileNotFoundError',
                'd' => 'TypeError',
            ],
            'correct' => 'b',
            'explanation' => "Trying to convert the string 'hello' to an integer raises a ValueError because the string doesn't represent a valid number.",
        ],
    ],
];

return $lessonData;
?>