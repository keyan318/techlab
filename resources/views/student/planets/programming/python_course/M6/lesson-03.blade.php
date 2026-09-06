<?php
/*
 * Programming M6 Lesson 3: 6C: Mini Database
 */

$lessonData = [
    'title' => '6C: Mini Database',
    'objective' => 'The student can create and query a simple SQLite database in Python.',
    'simple_explanation' => "SQLite is a lightweight, file-based database that\\'s built into Python. It\\'s perfect for small applications that need to store structured data.",
    'astro_explanation' => "Astro uses SQLite databases to store telemetry data, system logs, and mission parameters that need to be queried and analyzed.",
    'code_example' => "# Working with SQLite\nimport sqlite3\nfrom datetime import datetime\n\n# Create or connect to database\nconn = sqlite3.connect('astro_mission.db')\ncursor = conn.cursor()\n\n# Create table\ncursor.execute('''\n    CREATE TABLE IF NOT EXISTS telemetry (\n        id INTEGER PRIMARY KEY,\n        timestamp TEXT,\n        sensor_type TEXT,\n        value REAL,\n        unit TEXT\n    )\n''')\n\n# Insert sample data\nsample_data = [\n    ('2023-01-15 10:00:00', 'temperature', 22.5, 'Celsius'),\n    ('2023-01-15 10:01:00', 'temperature', 23.0, 'Celsius'),\n    ('2023-01-15 10:02:00', 'pressure', 101.3, 'kPa'),\n    ('2023-01-15 10:03:00', 'oxygen', 98.2, 'percent'),\n]\n\ncursor.executemany('''\n    INSERT INTO telemetry (timestamp, sensor_type, value, unit)\n    VALUES (?, ?, ?, ?)\n''', sample_data)\n\nconn.commit()\n\n# Query data\nprint(\"All telemetry data:\")\ncursor.execute('SELECT * FROM telemetry')\nrows = cursor.fetchall()\nfor row in rows:\n    print(f\"  {row}\")\n\n# Query with conditions\nprint(\"\\nTemperature readings:\")\ncursor.execute('''\n    SELECT * FROM telemetry\n    WHERE sensor_type = ?\n    ORDER BY timestamp\n''', ('temperature',))\ntemp_rows = cursor.fetchall()\nfor row in temp_rows:\n    print(f\"  {row}\")\n\n# Aggregate query\nprint(\"\\nAverage temperature:\")\ncursor.execute('''\n    SELECT AVG(value) FROM telemetry\n    WHERE sensor_type = ?\n''', ('temperature',))\navg_temp = cursor.fetchone()[0]\nprint(f\"  {avg_temp:.2f} Celsius\")\n\n# Close connection\nconn.close()",
    'interactive_exercise' => [
        'prompt' => 'Write a program that creates a database table for storing user information (id, name, email, age) and inserts one sample record.',
        'starter_code' => "import sqlite3\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between executing a single query with execute() and executing multiple queries with executemany().",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the file extension typically used for SQLite databases?',
            'options' => [
                'a' => '.sql',
                'b' => '.db',
                'c' => '.sqlite',
                'd' => 'Both .db and .sqlite are common',
            ],
            'correct' => 'd',
            'explanation' => "Both .db and .sqlite extensions are commonly used for SQLite database files, though SQLite doesn\\'t require any specific extension.",
        ],
        [
            'question' => 'Which SQL command would you use to remove a table from a database?',
            'options' => [
                'a' => 'DELETE',
                'b' => 'REMOVE',
                'c' => 'DROP',
                'd' => 'DELETE TABLE',
            ],
            'correct' => 'c',
            'explanation' => "The DROP TABLE command is used to completely remove a table and its data from a database.",
        ],
    ],
];

return $lessonData;
?>