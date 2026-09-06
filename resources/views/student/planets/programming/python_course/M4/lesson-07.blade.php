<?php
/*
 * Programming M4 Lesson 7: Writing Files
 */

$lessonData = [
    'title' => 'Writing Files',
    'objective' => 'The student can write data to files in Python and create new files or modify existing ones.',
    'simple_explanation' => "Python provides built-in functions to write to files. You can write strings, format data, or write binary data to files.",
    'astro_explanation' => "Astro writes log files, saves sensor data, stores configuration changes, and creates reports to be transmitted back to Earth.",
    'code_example' => "# Writing text to a file\nwith open(\"output.txt\", \"w\") as file:\n    file.write(\"Hello, Astro!\\n\")\n    file.write(\"Mission status: nominal\\n\")\n\n# Writing multiple lines\nlines = [\n    \"First line\\n\",\n    \"Second line\\n\",\n    \"Third line\\n\"\n]\nwith open(\"output.txt\", \"w\") as file:\n    file.writelines(lines)\n\n# Appending to a file\nwith open(\"log.txt\", \"a\") as file:\n    file.write(\"[LOG] System check completed\\n\")\n\n# Writing formatted data\nwith open(\"data.csv\", \"w\") as file:\n    file.write(\"Timestamp,Temperature,Oxygen\\n\")\n    file.write(\"1000,22.5,98.2\\n\")\n    file.write(\"1001,23.0,98.0\\n\")\n\n# Working with CSV files\nimport csv\nwith open(\"missions.csv\", \"w\", newline='') as file:\n    writer = csv.DictWriter(file, fieldnames=[\"name\", \"status\", \"duration\"])\n    writer.writeheader()\n    writer.writerow({\"name\": \"Voyager\", \"status\": \"active\", \"duration\": \"45 years\"})",
    'interactive_exercise' => [
        'prompt' => 'Write a program that creates a file called \"greeting.txt\" and writes the message \"Hello from Earth!\" to it.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between the 'w' and 'a' file modes in Python.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\nwith open(\"test.txt\", \"w\") as file:\n    file.write(\"Line 1\\nLine 2\\n\")\n\nwith open(\"test.txt\", \"r\") as file:\n    content = file.read()\n    print(repr(content))',
            'options' => [
                'a' => ''Line 1\\nLine 2\\n''',
                'b' => ''Line 1Line 2\\n''',
                'c' => ''Line 1\\nLine 2''',
                'd' => 'Error',
            ],
            'correct' => 'a',
            'explanation' => "The file contains exactly what was written: 'Line 1\\nLine 2\\n'. The repr() function shows escape sequences explicitly.",
        ],
        [
            'question' => 'Which mode should you use to add content to the end of an existing file without overwriting it?',
            'options' => [
                'a' => '\"r\"',
                'b' => '\"w\"',
                'c' => '\"a\"',
                'd' => '\"rw\"',
            ],
            'correct' => 'c',
            'explanation' => "The 'a' mode opens a file for appending, which adds new content to the end of the file without removing existing content.",
        ],
    ],
];

return $lessonData;
?>