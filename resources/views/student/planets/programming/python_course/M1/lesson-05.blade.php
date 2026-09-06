<?php
/*
 * Programming M1 Lesson 5: Talking to the Program
 */

$lessonData = [
    'title' => 'Talking to the Program',
    'objective' => 'The student can write Python programs that accept input from the user and respond accordingly.',
    'simple_explanation' => "Programs become more useful when they can interact with users. The input() function lets you ask questions and receive answers that your program can use.",
    'astro_explanation' => "Astro needs to receive commands from mission control and respond with status reports. Using input() and print(), Astro can have a two-way conversation with operators on Earth.",
    'code_example' => "# Asking for user input\nname = input(\"What is your name? \")\nstatus = input(\"How are you feeling today? \")\n\nprint(f\"Hello, {name}!\")\nprint(f\"I hear you're feeling {status}. That's great!\")",
    'interactive_exercise' => [
        'prompt' => 'Ask the user for their favorite planet, then print a message saying that planet is a great choice.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Write a program that asks for two numbers, then prints their sum.",
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What does the input() function do?',
            'options' => [
                'a' => 'Displays text on the screen',
                'b' => 'Gets input from the user and returns it as a string',
                'c' => 'Converts text to uppercase',
                'd' => 'Ends the program',
            ],
            'correct' => 'b',
            'explanation' => "The input() function displays a prompt, waits for the user to type something and press Enter, then returns what they typed as a string.",
        ],
        [
            'question' => 'If the user enters \"42\" when asked for input, what type of value does input() return?',
            'options' => [
                'a' => 'Integer',
                'b' => 'Float',
                'c' => 'String',
                'd' => 'Boolean',
            ],
            'correct' => 'c',
            'explanation' => "input() always returns a string, even if the user types numbers. To get a number, you need to convert the string using int() or float().",
        ],
    ],
];

return $lessonData;
?>