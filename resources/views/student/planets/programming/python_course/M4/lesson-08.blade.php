<?php
/*
 * Programming M4 Lesson 8: Structured Data Formats
 */

$lessonData = [
    'title' => 'Structured Data Formats',
    'objective' => 'The student can work with structured data formats like JSON and XML in Python.',
    'simple_explanation' => "Structured data formats provide a standardized way to represent complex data. JSON (JavaScript Object Notation) is particularly popular for data exchange.",
    'astro_explanation' => "Astro uses JSON to exchange data with mission control systems, store configuration files, and transmit scientific data in a standardized format.",
    'code_example' => "# Working with JSON\nimport json\n\n# JSON string to Python object\njson_string = '{\"name\": \"Astro\", \"status\": \"online\", \"power\": 85}'\nastro_data = json.loads(json_string)\nprint(astro_data[\"name\"])  # Astro\n\n# Python object to JSON string\nastro_dict = {\n    \"mission_time\": 3600,\n    \"sensors\": {\"temperature\": 22.5, \"oxygen\": 98.2},\n    \"active\": true\n}\njson_output = json.dumps(astro_dict, indent=2)\nprint(json_output)\n\n# Reading JSON from file\nwith open(\"config.json\", \"r\") as file:\n    config = json.load(file)\n    print(f\"Loaded config: {config['version']}\")\n\n# Writing JSON to file\nwith open(\"output.json\", \"w\") as file:\n    json.dump(astro_dict, file, indent=2)\n\n# CSV (briefly mentioned as alternative)\nimport csv\nwith open(\"data.csv\", \"r\") as file:\n    reader = csv.DictReader(file)\n    for row in reader:\n        print(row)",
    'interactive_exercise' => [
        'prompt' => 'Create a Python dictionary representing a spacecraft\\'s status and convert it to a JSON string.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the advantages of using JSON for data exchange compared to custom formats.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\nimport json\ndata = {\"name\": \"Test\", \"value\": 42}\njson_string = json.dumps(data)\nprint(json_string)',
            'options' => [
                'a' => '{\"name\": \"Test\", \"value\": 42}',
                'b' => '[\"name\": \"Test\", \"value\": 42]',
                'c' => '{\"name\": \"Test\"}',
                'd' => 'Error',
            ],
            'correct' => 'a',
            'explanation' => "json.dumps() converts the Python dictionary to a JSON string with the same key-value pairs.",
        ],
        [
            'question' => 'Which of these is NOT a valid JSON value type?',
            'options' => [
                'a' => 'String',
                'b' => 'Number',
                'c' => 'Function',
                'd' => 'Boolean',
            ],
            'correct' => 'c',
            'explanation' => "JSON supports strings, numbers, booleans, arrays, objects, and null. Functions are not a valid JSON data type.",
        ],
    ],
];

return $lessonData;
?>