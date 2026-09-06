<?php
/*
 * Networking M1 Lesson 3: OSI Model
 */

$lessonData = [
    'title' => 'OSI Model',
    'objective' => 'The student can explain the seven layers of the OSI model and the function of each layer.',
    'simple_explanation' => "The OSI (Open Systems Interconnection) model is a conceptual framework that divides network communication into seven layers, each with specific responsibilities.",
    'astro_explanation' => "Astro's communication systems use protocols that map to the OSI model layers, ensuring compatibility with Earth-based systems and standardized troubleshooting approaches.",
    'code_example' => "# The seven layers of the OSI model\nprint(\"7. Application: HTTP, FTP, DNS\")\nprint(\"6. Presentation: Encryption, compression\")\nprint(\"5. Session: Connection management\")\nprint(\"4. Transport: TCP, UDP\")\nprint(\"3. Network: IP, routing\")\nprint(\"2. Data Link: MAC, switching\")\nprint(\"1. Physical: Cables, signals\")",
    'interactive_exercise' => [
        'prompt' => 'Match each OSI layer to its primary function:\n1. Physical  2. Data Link  3. Network  4. Transport  5. Session  6. Presentation  7. Application\nA. End-user applications\nB. Data formatting and encryption\nC. Logical addressing and routing\nD. Reliable end-to-end communication',
        'starter_code' => "# your answer here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain how the TCP/IP model relates to the OSI model. Are they competing or complementary?",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'Which layer of the OSI model is responsible for logical addressing and routing?',
            'options' => [
                'a' => 'Layer 2 (Data Link)',
                'b' => 'Layer 3 (Network)',
                'c' => 'Layer 4 (Transport)',
                'd' => 'Layer 7 (Application)',
            ],
            'correct' => 'b',
            'explanation' => "Layer 3 (Network) handles logical addressing (IP addresses) and routing decisions for getting data from source to destination.",
        ],
        [
            'question' => 'Which OSI layer deals with MAC addresses and switching?',
            'options' => [
                'a' => 'Layer 1 (Physical)',
                'b' => 'Layer 2 (Data Link)',
                'c' => 'Layer 3 (Network)',
                'd' => 'Layer 4 (Transport)',
            ],
            'correct' => 'b',
            'explanation' => "Layer 2 (Data Link) handles physical addressing (MAC addresses) and switching within a local network segment.",
        ],
    ],
];

return $lessonData;
?>