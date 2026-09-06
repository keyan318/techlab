<?php
/*
 * Programming M6 Lesson 2: 6B: Spreadsheet Automation
 */

$lessonData = [
    'title' => '6B: Spreadsheet Automation',
    'objective' => 'The student can read and write spreadsheet files in Python using the pandas library.',
    'simple_explanation' => "Spreadsheet files (like Excel or CSV) are commonly used for data storage and analysis. Python libraries like pandas make it easy to work with these files programmatically.",
    'astro_explanation' => "Astro uses spreadsheet automation to process scientific data, generate reports for mission control, and analyze sensor readings collected during missions.",
    'code_example' => "# Working with CSV files\nimport pandas as pd\n\n# Reading a CSV file\ntry:\n    df = pd.read_csv('data.csv')\n    print(\"First 5 rows:\")\n    print(df.head())\n    print(f\"\\nShape: {df.shape}\")\n    print(f\"\\nColumns: {list(df.columns)}\")\nexcept FileNotFoundError:\n    print(\"File not found! Creating sample data...\")\n    # Create sample data\n    data = {\n        'Timestamp': ['2023-01-15 10:00:00', '2023-01-15 10:01:00', '2023-01-15 10:02:00'],\n        'Temperature': [22.5, 23.0, 22.8],\n        'Pressure': [101.3, 101.2, 101.4]\n    }\n    df = pd.DataFrame(data)\n    df.to_csv('data.csv', index=False)\n    print(\"Sample data saved to data.csv\")\n\n# Basic data analysis\nprint(f\"\\nMean temperature: {df['Temperature'].mean():.2f}\")\nprint(f\"Max pressure: {df['Pressure'].max():.2f}\")\n\n# Filtering data\nhigh_temp = df[df['Temperature'] > 22.7]\nprint(f\"\\nRows with temperature > 22.7:\\n{high_temp}\")\n\n# Writing to Excel\ntry:\n    df.to_excel('output.xlsx', index=False)\n    print(\"\\nData saved to output.xlsx\")\nexcept Exception as e:\n    print(f\"\\nCould not save to Excel: {e}\")\n    print(\"Saving as CSV instead...\")\n    df.to_csv('output.csv', index=False)",
    'interactive_exercise' => [
        'prompt' => 'Write a program that reads a CSV file called \"sensor_data.csv\" and calculates the average of the \"value\" column.',
        'starter_code' => "import pandas as pd\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between reading a CSV file with pandas.read_csv() and Python\\'s built-in csv module.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What does pandas stand for?',
            'options' => [
                'a' => 'Python Data Analysis Library',
                'b' => 'Panel Data',
                'c' => 'Python Application for Data Submission and Retrieval',
                'd' => 'Personal Account Number Data System',
            ],
            'correct' => 'b',
            'explanation' => "pandas originally stood for 'Panel Data', referring to multidimensional structured data sets, though it\\'s now just the name of the library.",
        ],
        [
            'question' => 'Which method would you use to read an Excel file (.xlsx) with pandas?',
            'options' => [
                'a' => 'pd.read_csv()',
                'b' => 'pd.read_excel()',
                'c' => 'pd.read_json()',
                'd' => 'pd.read_sql()',
            ],
            'correct' => 'b',
            'explanation' => "pd.read_excel() is used to read Excel files (.xlsx, .xls) into a pandas DataFrame.",
        ],
    ],
];

return $lessonData;
?>