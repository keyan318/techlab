<?php
/*
 * Programming M5 Lesson 4: Building a CLI Tool
 */

$lessonData = [
    'title' => 'Building a CLI Tool',
    'objective' => 'The student can create command-line interface programs in Python that accept arguments and provide useful output.',
    'simple_explanation' => "A Command-Line Interface (CLI) program is run from a terminal or command prompt and interacts with users through text input and output. Python makes it easy to create CLI tools.",
    'astro_explanation' => "Astro uses CLI tools for system diagnostics, configuration management, and maintenance tasks that can be run directly from the spacecraft\\'s terminal interface.",
    'code_example' => "# Basic CLI program\nimport sys\n\nprint(\"Hello from Python CLI!\")\nprint(f\"You provided {len(sys.argv)} arguments:\")\nfor i, arg in enumerate(sys.argv):\n    print(f\"  {i}: {arg}\")\n\n# Using argparse for better argument handling\nimport argparse\n\nparser = argparse.ArgumentParser(description=\"Process some integers.\")\nparser.add_argument('integers', metavar='N', type=int, nargs='*',\n                   help='an integer for the accumulator')\nparser.add_argument('--sum', dest='accumulate', action='store_const',\n                   const=sum, default=max,\n                   help='sum the integers (default: find the max)')\n\nargs = parser.parse_args()\nprint(f\"Result: {args.accumulate(args.integers)}\")",
    'interactive_exercise' => [
        'prompt' => 'Write a CLI program that greets the user by name, where the name is provided as a command-line argument.',
        'starter_code' => "import sys\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between positional arguments and optional arguments (flags) in CLI programs.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\nimport sys\nprint(sys.argv[0])',
            'options' => [
                'a' => 'The first command-line argument',
                'b' => 'The name of the Python script',
                'c' => 'The number of command-line arguments',
                'd' => 'Error',
            ],
            'correct' => 'b',
            'explanation' => "sys.argv[0] contains the name of the Python script being executed.",
        ],
        [
            'question' => 'Which module is commonly used for parsing command-line arguments in Python?',
            'options' => [
                'a' => 'sys',
                'b' => 'os',
                'c' => 'argparse',
                'd' => 'math',
            ],
            'correct' => 'c',
            'explanation' => "The argparse module provides a standardized way to parse command-line arguments and generate help messages.",
        ],
    ],
];

return $lessonData;
?>