<?php
/*
 * Programming M1 Lesson 6: Reading Error Messages
 */

$lessonData = [
    'title' => 'Reading Error Messages',
    'objective' => 'The student can read and interpret common Python error messages to fix bugs in their code.',
    'simple_explanation' => "When Python encounters a problem, it displays an error message that tells you what went wrong and where. Learning to read these messages helps you fix bugs faster.",
    'astro_explanation' => "When Astro's software encounters an issue, it needs to understand what went wrong. Error messages are like diagnostic codes that help engineers troubleshoot problems in spacecraft software.",
    'code_example' => "# This code will cause an error when run:\nprint(\"Hello World\"\n# Missing closing parenthesis!",
    'interactive_exercise' => [
        'prompt' => 'Look at this error message and fix the code:\nNameError: name \\'galxy\\' is not defined\n\nprint(galxy)',
        'starter_code' => "print(galxy)",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between a syntax error and a runtime error. Give an example of each.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What type of error is this?\nIndentationError: unexpected indent',
            'options' => [
                'a' => 'Syntax error',
                'b' => 'Runtime error',
                'c' => 'Logic error',
                'd' => 'Type error',
            ],
            'correct' => 'a',
            'explanation' => "IndentationError occurs when the spacing or indentation of your code is incorrect, which is a syntax error because it violates Python's syntax rules.",
        ],
        [
            'question' => 'What does this error mean?\nTypeError: can only concatenate str (not \"int\") to str',
            'options' => [
                'a' => 'You tried to add a string and an integer together',
                'b' => 'You tried to divide by zero',
                'c' => 'You used a variable that doesn\\'t exist',
                'd' => 'You missed a colon after an if statement',
            ],
            'correct' => 'a',
            'explanation' => "This TypeError occurs when you try to use the + operator to concatenate a string and an integer. You need to convert the integer to a string first using str().",
        ],
    ],
];

return $lessonData;
?>