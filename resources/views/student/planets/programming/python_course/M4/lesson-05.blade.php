<?php
/*
 * Programming M4 Lesson 5: Text Manipulation
 */

$lessonData = [
    'title' => 'Text Manipulation',
    'objective' => 'The student can manipulate strings in Python using various string methods and formatting techniques.',
    'simple_explanation' => "Strings in Python are sequences of characters that can be manipulated using built-in methods: changing case, splitting, joining, replacing, and more.",
    'astro_explanation' => "Astro uses string manipulation to process commands from mission control, format telemetry data for transmission, and generate human-readable reports.",
    'code_example' => "# String methods\nmessage = \"  Hello, Astro!  \"\nprint(message.strip())      # \"Hello, Astro!\" (remove whitespace)\nprint(message.upper())      # \"  HELLO, ASTRO!  \"\nprint(message.lower())      # \"  hello, astro!  \"\nprint(message.replace(\"Hello\", \"Hi\"))  # \"  Hi, Astro!  \"\n\n# Splitting and joining\ncsv_line = \"Alice,25,Engineer\"\nparts = csv_line.split(\",\")  # [\"Alice\", \"25\", \"Engineer\"]\nnew_csv = \";\".join(parts)   # \"Alice;25;Engineer\"\n\n# String formatting\nname = \"Astro\"\nstatus = \"online\"\nprint(f\"{name} is {status}\")  # f-string (Python 3.6+)\nprint(\"{} is {}\".format(name, status))  # .format() method\nprint(\"%s is %s\" % (name, status))     # %-formatting (older)\n\n# Checking string properties\nprint(\"hello\".startswith(\"he\"))  # True\nprint(\"world\".endswith(\"ld\"))   # True\nprint(\"hello\".isalpha())         # True (all letters)\nprint(\"123\".isdigit())           # True (all digits)\n",
    'interactive_exercise' => [
        'prompt' => 'Write a program that asks for the user\\'s name and greets them using their name in uppercase.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between the split() and join() string methods.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\ntext = \"  hello world  \"\nprint(text.strip().upper())',
            'options' => [
                'a' => '  HELLO WORLD  ',
                'b' => 'hello world',
                'c' => 'HELLO WORLD',
                'd' => 'Error',
            ],
            'correct' => 'c',
            'explanation' => "strip() removes whitespace: \"hello world\", then upper() converts to uppercase: \"HELLO WORLD\".",
        ],
        [
            'question' => 'Which method would you use to check if a string contains only alphabetic characters?',
            'options' => [
                'a' => 'isnumeric()',
                'b' => 'isalpha()',
                'c' => 'isdigit()',
                'd' => 'isalnum()',
            ],
            'correct' => 'b',
            'explanation' => "The isalpha() method returns True if all characters in the string are alphabetic and there is at least one character.",
        ],
    ],
];

return $lessonData;
?>