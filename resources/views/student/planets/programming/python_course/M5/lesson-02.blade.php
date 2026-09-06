<?php
/*
 * Programming M5 Lesson 2: Practical Regex
 */

$lessonData = [
    'title' => 'Practical Regex',
    'objective' => 'The student can apply regular expressions to solve common text processing problems.',
    'simple_explanation' => "Regular expressions are used in many practical applications: validating user input, parsing log files, extracting data from structured text, and more.",
    'astro_explanation' => "Astro uses practical regex applications: validating command formats from mission control, parsing telemetry packets, filtering log entries by severity, and extracting coordinates from navigation data.",
    'code_example' => "# Validating a phone number\nimport re\nphone_pattern = re.compile(r\"^\\+?[1-9]\\d{1,14}$\")  # E.164 format\nif phone_pattern.match(\"+1234567890\"):\n    print(\"Valid phone number\")\n\n# Extracting URLs from text\ntext = \"Visit https://example.com or http://test.org for more info\"\nurl_pattern = re.compile(r\"https?://[^\\s]+\")\nurls = url_pattern.findall(text)\nprint(urls)  # [\"https://example.com\", \"http://test.org\"]\n\n# Parsing a log line\nlog_line = \"2023-01-15 10:30:45 ERROR: Failed to connect to server\"\nlog_pattern = re.compile(r\"(\\d{4}-\\d{2}-\\d{2}) (\\d{2}:\\d{2}:\\d{2}) (\\w+): (.+)\")\nmatch = log_pattern.search(log_line)\nif match:\n    date, time, level, message = match.groups()\n    print(f\"[{level}] {message} at {time} on {date}\")\n\n# Replacing multiple spaces with single space\ntext = \"This   has   multiple   spaces\"\nsingle_spaced = re.sub(r\"\\\\s+\", \" \", text)\nprint(single_spaced)  # \"This has multiple spaces\"\n\n# Splitting text while keeping delimiters\nsentence = \"Hello, world! How are you?\"\nparts = re.split(r\"([,.!?])\", sentence)\nprint(parts)  # [\"Hello\", \",\", \" world\", \"!\", \" How are you\", \"?\", \"\"]",
    'interactive_exercise' => [
        'prompt' => 'Write a program that extracts all email addresses from a block of text.',
        'starter_code' => "import re\ntext = \"\"\"\nPlease contact us at support@example.com or admin@test.org\nfor assistance. You can also reach billing@company.net.\n\"\"\"\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain how you would use regex to find all words that start with a capital letter in a sentence.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\nimport re\ntext = \"abc123def456\"\nnumbers = re.findall(r\"\\\\d+\", text)\nprint(numbers)',
            'options' => [
                'a' => '[\"123\", \"456\"]',
                'b' => '[\"abc\", \"def\"]',
                'c' => '[\"1\", \"2\", \"3\", \"4\", \"5\", \"6\"]',
                'd' => '[\"abc123def456\"]',
            ],
            'correct' => 'a',
            'explanation' => "The pattern \\\\d+ finds sequences of one or more digits. In the text, we have \"123\" and \"456\".",
        ],
        [
            'question' => 'Which regex flag makes the dot (.) match newline characters as well?',
            'options' => [
                'a' => 're.IGNORECASE',
                'b' => 're.MULTILINE',
                'c' => 're.DOTALL',
                'd' => 're.VERBOSE',
            ],
            'correct' => 'c',
            'explanation' => "The re.DOTALL flag makes the dot (.) special character match any character including newline, whereas normally it matches any character except newline.",
        ],
    ],
];

return $lessonData;
?>