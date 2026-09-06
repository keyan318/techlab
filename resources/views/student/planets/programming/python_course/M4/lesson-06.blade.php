<?php
/*
 * Programming M4 Lesson 6: Reading Files
 */

$lessonData = [
    'title' => 'Reading Files',
    'objective' => 'The student can read data from files in Python and process the contents.',
    'simple_explanation' => "Python provides built-in functions to read from files. You can read the entire file at once, line by line, or process it in chunks.",
    'astro_explanation' => "Astro reads configuration files, mission scripts, and data logs stored on board to load parameters, execute procedures, and analyze past performance.",
    'code_example' => "# Reading entire file\nwith open(\"data.txt\", \"r\") as file:\n    content = file.read()\n    print(content)\n\n# Reading line by line\nwith open(\"data.txt\", \"r\") as file:\n    for line in file:\n        print(f\"Line: {line.strip()}\")\n\n# Reading all lines into a list\nwith open(\"data.txt\", \"r\") as file:\n    lines = file.readlines()\n    print(f\"First line: {lines[0].strip()}\")\n\n# Working with CSV files\nimport csv\nwith open(\"missions.csv\", \"r\") as file:\n    reader = csv.DictReader(file)\n    for row in reader:\n        print(f\"Mission: {row['name']}, Status: {row['status']}\")\n\n# Handling file not found\ntry:\n    with open(\"missing.txt\", \"r\") as file:\n        content = file.read()\nexcept FileNotFoundError:\n    print(\"File not found!\")",
    'interactive_exercise' => [
        'prompt' => 'Write a program that reads a file called \"message.txt\" and prints its contents.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between using 'with open()' and manually opening and closing a file.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\nwith open(\"example.txt\", \"w\") as file:\n    file.write(\"Hello, Astro!\")\n\nwith open(\"example.txt\", \"r\") as file:\n    content = file.read()\n    print(content)',
            'options' => [
                'a' => 'Hello, Astro!',
                'b' => 'File not found!',
                'c' => 'Hello, Astro!\\n',
                'd' => 'Error',
            ],
            'correct' => 'a',
            'explanation' => "The code writes \"Hello, Astro!\" to the file, then reads it back and prints it. Note that write() doesn\\'t add a newline unless explicitly included.",
        ],
        [
            'question' => 'Which mode should you use to open a file for reading only?',
            'options' => [
                'a' => '\"w\"',
                'b' => '\"r\"',
                'c' => '\"a\"',
                'd' => '\"rw\"',
            ],
            'correct' => 'b',
            'explanation' => "The \"r\" mode opens a file for reading only. \"w\" is for writing (truncates existing file), \"a\" is for appending.",
        ],
    ],
];

return $lessonData;
?>