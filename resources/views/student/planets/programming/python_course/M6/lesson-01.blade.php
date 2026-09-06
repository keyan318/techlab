<?php
/*
 * Programming M6 Lesson 1: 6A: Web Basics
 */

$lessonData = [
    'title' => '6A: Web Basics',
    'objective' => 'The student can explain how web applications work and make simple HTTP requests in Python.',
    'simple_explanation' => "Web applications communicate using HTTP requests. A client (like a browser or Python program) sends requests to a server, which responds with data.",
    'astro_explanation' => "Astro uses web technologies to communicate with mission control systems, upload scientific data, and download software updates or mission parameters.",
    'code_example' => "# Making HTTP requests\nimport requests\n\n# GET request\ntry:\n    response = requests.get('https://httpbin.org/get')\n    if response.status_code == 200:\n        print(\"GET request successful!\")\n        print(response.json())\n    else:\n        print(f\"GET request failed: {response.status_code}\")\nexcept requests.exceptions.RequestException as e:\n    print(f\"Error: {e}\")\n\n# POST request with data\ntry:\n    data = {\"message\": \"Hello from Astro!\", \"timestamp\": \"2023-01-15T10:30:00Z\"}\n    response = requests.post('https://httpbin.org/post', json=data)\n    if response.status_code == 200:\n        print(\"POST request successful!\")\n        print(response.json())\n    else:\n        print(f\"POST request failed: {response.status_code}\")\nexcept requests.exceptions.RequestException as e:\n    print(f\"Error: {e}\")",
    'interactive_exercise' => [
        'prompt' => 'Write a program that makes a GET request to https://httpbin.org/uuid and prints the returned UUID.',
        'starter_code' => "import requests\n# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between GET and POST HTTP requests and when you would use each.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What does HTTP stand for?',
            'options' => [
                'a' => 'HyperText Transfer Protocol',
                'b' => 'High-Speed Text Processing',
                'c' => 'Hyperlink Text Management Protocol',
                'd' => 'Host-to-Machine Transfer Protocol',
            ],
            'correct' => 'a',
            'explanation' => "HTTP stands for HyperText Transfer Protocol, which is the foundation of data communication for the World Wide Web.",
        ],
        [
            'question' => 'Which HTTP method is typically used to retrieve data from a server?',
            'options' => [
                'a' => 'POST',
                'b' => 'GET',
                'c' => 'PUT',
                'd' => 'DELETE',
            ],
            'correct' => 'b',
            'explanation' => "GET is the standard HTTP method for retrieving data from a server. It should be safe and idempotent.",
        ],
    ],
];

return $lessonData;
?>