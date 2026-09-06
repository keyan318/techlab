<?php
/*
 * Programming M5 Lesson 3: File System Basics
 */

$lessonData = [
    'title' => 'File System Basics',
    'objective' => 'The student can work with the file system in Python using the os and pathlib modules.',
    'simple_explanation' => "Python provides modules to interact with the operating system\\'s file system: creating directories, listing files, checking file properties, and more.",
    'astro_explanation' => "Astro uses file system operations to manage data logs, create directories for different mission phases, check available storage space, and organize scientific data.",
    'code_example' => "# Using os module\nimport os\n\n# Current working directory\nprint(f\"Current directory: {os.getcwd()}\")\n\n# Listing directory contents\nfiles = os.listdir(\".\")\nprint(f\"Files in current directory: {files}\")\n\n# Checking if path exists\nif os.path.exists(\"data.txt\"):\n    print(\"data.txt exists\")\n    if os.path.isfile(\"data.txt\"):\n        print(\"data.txt is a file\")\n    if os.path.isdir(\"data\"):\n        print(\"data is a directory\")\n\n# Getting file size\nif os.path.exists(\"data.txt\"):\n    size = os.path.getsize(\"data.txt\")\n    print(f\"data.txt size: {size} bytes\")\n\n# Using pathlib (modern approach)\nfrom pathlib import Path\n\n# Current directory\ncurrent_dir = Path(\".\")\nprint(f\"Current directory: {current_dir.absolute()}\")\n\n# Listing files\nfor item in current_dir.iterdir():\n    print(f\"Item: {item.name}\")\n\n# Checking file types\nif (current_dir / \"data.txt\").is_file():\n    print(\"data.txt is a file\")\nif (current_dir / \"logs\").is_dir():\n    print(\"logs is a directory\")\n\n# Creating directories\nnew_dir = Path(\"new_directory\")\nnew_dir.mkdir(exist_ok=True)  # Create if doesn\\'t exist\n\n# Walking directory tree\nfor dir_path, dir_names, file_names in os.walk(\".\"):\n    print(f\"Directory: {dir_path}\")\n    for file_name in file_names:\n        print(f\"  File: {file_name}\")",
    'interactive_exercise' => [
        'prompt' => 'Write a program that lists all files in the current directory with their sizes.',
        'starter_code' => "import os\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between relative and absolute paths in a file system.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\nimport os\nprint(os.path.isfile(\"__file__\"))',
            'options' => [
                'a' => 'True',
                'b' => 'False',
                'c' => 'It depends on the file name',
                'd' => 'Error',
            ],
            'correct' => 'a',
            'explanation' => "__file__ is a special variable that contains the path to the current Python script, so os.path.isfile(__file__) returns True.",
        ],
        [
            'question' => 'Which method would you use to create a directory and all its parent directories if they don\\'t exist?',
            'options' => [
                'a' => 'os.mkdir()',
                'b' => 'os.makedirs()',
                'c' => 'Path.mkdir()',
                'd' => 'os.creat()',
            ],
            'correct' => 'b',
            'explanation' => "os.makedirs() creates a directory and any necessary parent directories, while os.mkdir() only creates the final directory and fails if parent directories don\\'t exist.",
        ],
    ],
];

return $lessonData;
?>