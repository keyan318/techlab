<?php
/*
 * Programming M4 Lesson 3: Labeled Data
 */

$lessonData = [
    'title' => 'Labeled Data',
    'objective' => 'The student can explain what dictionaries are in Python and use them to store key-value pairs.',
    'simple_explanation' => "Dictionaries are Python's built-in mapping type. They store data as key-value pairs, allowing you to look up values by their associated keys.",
    'astro_explanation' => "Astro uses dictionaries to store structured data: telemetry packets with labeled fields, configuration settings, or mission parameters with descriptive names.",
    'code_example' => "# Creating dictionaries\nempty_dict = {}\nastro_status = {\n    \"mission_time\": 3600,\n    \"oxygen_level\": 98.5,\n    \"temperature\": 22.0,\n    \"power_level\": 85\n}\n\n# Accessing values\nprint(astro_status[\"mission_time\"])  # 3600\nprint(astro_status.get(\"pressure\", \"Not available\"))  # Safe access\n\n# Modifying dictionaries\nastro_status[\"temperature\"] = 23.5  # Update existing\nastro_status[\"battery_charge\"] = 92  # Add new key-value pair\n\n# Dictionary methods\nprint(list(astro_status.keys()))   # All keys\nprint(list(astro_status.values())) # All values\nprint(len(astro_status))           # Number of key-value pairs\n\n# Checking for keys\nprint(\"oxygen_level\" in astro_status)  # True\nprint(\"pressure\" in astro_status)      # False",
    'interactive_exercise' => [
        'prompt' => 'Create a dictionary to store information about a book: title, author, and year published.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between a list and a dictionary in Python. When would you use each?",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\nperson = {\"name\": \"Alice\", \"age\": 30}\nprint(person[\"name\"])',
            'options' => [
                'a' => 'Alice',
                'b' => '30',
                'c' => '{\"name\": \"Alice\", \"age\": 30}',
                'd' => 'Error',
            ],
            'correct' => 'a',
            'explanation' => "Accessing person[\"name\"] returns the value associated with the \"name\" key, which is \"Alice\".",
        ],
        [
            'question' => 'Which of these creates an empty dictionary?',
            'options' => [
                'a' => 'dict = ()',
                'b' => 'dict = []',
                'c' => 'dict = {}',
                'd' => 'dict = \"\"',
            ],
            'correct' => 'c',
            'explanation' => "Curly braces {} create an empty dictionary in Python. Parentheses () create a tuple, square brackets [] create a list, and quotes create a string.",
        ],
    ],
];

return $lessonData;
?>