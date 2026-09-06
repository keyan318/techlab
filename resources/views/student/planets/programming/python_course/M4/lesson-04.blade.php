<?php
/*
 * Programming M4 Lesson 4: Nested Structures
 */

$lessonData = [
    'title' => 'Nested Structures',
    'objective' => 'The student can work with nested data structures in Python, such as lists of dictionaries or dictionaries containing lists.',
    'simple_explanation' => "Data structures can be nested inside each other to create complex representations. For example, a list of dictionaries where each dictionary represents a record with multiple fields.",
    'astro_explanation' => "Astro uses nested structures to represent complex telemetry: a list of sensor readings, where each reading is a dictionary with timestamp, sensor_id, and value fields.",
    'code_example' => "# List of dictionaries\ntelemetry_data = [\n    {\"timestamp\": 1000, \"sensor\": \"temperature\", \"value\": 22.5},\n    {\"timestamp\": 1001, \"sensor\": \"oxygen\", \"value\": 98.2},\n    {\"timestamp\": 1002, \"sensor\": \"power\", \"value\": 85.0}\n]\n\n# Accessing nested data\nprint(telemetry_data[0][\"value\"])  # 22.5\nprint(telemetry_data[2][\"sensor\"])  # power\n\n# Dictionary with lists\nmission_phases = {\n    \"launch\": [\"ignition\", \"liftoff\", \"max_q\"],\n    \"orbit\": [\"orbital_insertion\", \"stabilization\"],\n    \"landing\": [\"deorbit\", \"reentry\", \"touchdown\"]\n}\n\nprint(mission_phases[\"launch\"][1])  # liftoff\n\n# Modifying nested structures\ntelemetry_data.append({\n    \"timestamp\": 1003,\n    \"sensor\": \"temperature\",\n    \"value\": 23.0\n})\n\nmission_phases[\"orbit\"].append(\"systems_check\")\n\n# Iterating through nested data\nfor reading in telemetry_data:\n    print(f\"{reading['sensor']}: {reading['value']} at {reading['timestamp']}\")",
    'interactive_exercise' => [
        'prompt' => 'Create a list of dictionaries representing students, where each dictionary has name, age, and grade fields.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain how you would find all temperature readings in a list of telemetry dictionaries.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\ndata = [{\"x\": 1, \"y\": 2}, {\"x\": 3, \"y\": 4}]\nprint(data[1][\"x\"])',
            'options' => [
                'a' => '1',
                'b' => '2',
                'c' => '3',
                'd' => '4',
            ],
            'correct' => 'c',
            'explanation' => "data[1] accesses the second dictionary {\"x\": 3, \"y\": 4}, and [\"x\"] gets the value 3.",
        ],
        [
            'question' => 'Which of these creates a dictionary with a list as a value?',
            'options' => [
                'a' => '{\"key\": (1, 2, 3)}',
                'b' => '{\"key\": [1, 2, 3]}',
                'c' => '{\"key\": {1, 2, 3}}',
                'd' => '{\"key\": \"1, 2, 3\"}',
            ],
            'correct' => 'b',
            'explanation' => "Square brackets [] create a list, so {\"key\": [1, 2, 3]} creates a dictionary where the value for \"key\" is the list [1, 2, 3].",
        ],
    ],
];

return $lessonData;
?>