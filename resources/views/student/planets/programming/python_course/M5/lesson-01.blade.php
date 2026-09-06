<?php
/*
 * Programming M5 Lesson 1: Pattern Matching
 */

$lessonData = [
    'title' => 'Pattern Matching',
    'objective' => 'The student can use regular expressions in Python to search for and manipulate text patterns.',
    'simple_explanation' => "Regular expressions (regex) are a powerful tool for matching patterns in text. They allow you to search for complex patterns, validate input, and extract specific parts of strings.",
    'astro_explanation' => "Astro uses pattern matching to validate incoming commands from mission control, extract data from telemetry strings, and identify specific patterns in sensor readings.",
    'code_example' => "# Import the regex module\nimport re\n\n# Simple pattern matching\ntext = \"Astro mission status: ALL SYSTEMS NORMAL\"\nif re.search(r\"NORMAL\", text):\n    print(\"System status is normal\")\n\n# Compiling patterns for reuse\npattern = re.compile(r\"Astro\")\nif pattern.search(text):\n    print(\"Found Astro in text\")\n\n# Extracting parts with groups\nlog_entry = \"2023-01-15 14:30:22 - TEMPERATURE: 22.5C\"\npattern = re.compile(r\"(\\d{4}-\\d{2}-\\d{2}) (\\d{2}:\\d{2}:\\d{2}) - (\\w+): (\\d+\\.\\d+)C\")\nmatch = pattern.search(log_entry)\nif match:\n    date, time, sensor, value = match.groups()\n    print(f\"{sensor} at {time} on {date}: {value}°C\")\n\n# Validating input\nemail = \"user@example.com\"\nemail_pattern = re.compile(r\"^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$\")\nif email_pattern.match(email):\n    print(\"Valid email format\")\n\n# Replacing text\ncorrected = re.sub(r\"ASTRO\", \"Astro\", \"Welcome to ASTRO Control\")\nprint(corrected)  # Welcome to Astro Control",
    'interactive_exercise' => [
        'prompt' => 'Write a program that checks if a string contains only letters and numbers (no special characters).',
        'starter_code' => "import re\ntext = input(\"Enter text: \")\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between re.match(), re.search(), and re.findall() in Python\\'s regex module.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What does this regex pattern match?\n\\d{3}-\\d{2}-\\d{4}',
            'options' => [
                'a' => 'Three digits, hyphen, two digits, hyphen, four digits (like a SSN)',
                'b' => 'Three digits followed by two digits followed by four digits',
                'c' => 'Any sequence of digits with hyphens',
                'd' => 'Exactly nine digits',
            ],
            'correct' => 'a',
            'explanation' => "This pattern matches exactly three digits, a hyphen, exactly two digits, a hyphen, and exactly four digits - the format of a US Social Security Number.",
        ],
        [
            'question' => 'Which regex pattern would match one or more digits?',
            'options' => [
                'a' => '\\d+',
                'b' => '\\d*',
                'c' => '\\d?',
                'd' => '\\d{1,}',
            ],
            'correct' => 'a',
            'explanation' => "The '+' quantifier means 'one or more' of the preceding element. Both '\\d+' and '\\d{1,}' would work, but '\\d+' is the standard notation.",
        ],
    ],
];

return $lessonData;
?>