<?php
/*
 * Networking M1 Lesson 2: Network Topologies
 */

$lessonData = [
    'title' => 'Network Topologies',
    'objective' => 'The student can describe different network topologies and their advantages/disadvantages.',
    'simple_explanation' => "Network topology refers to the arrangement of different elements (links, nodes, etc.) in a computer network. It's like the map or blueprint of how devices are connected.",
    'astro_explanation' => "Different spacecraft might use different network topologies depending on their mission needs. A Mars rover might use a simple star topology, while a space station might use a more complex mesh topology for redundancy.",
    'code_example' => "# Conceptual representation of different topologies\nprint(\"Star Topology: All devices connect to a central hub\")\nprint(\"Bus Topology: All devices share a single communication line\")\nprint(\"Ring Topology: Devices form a closed loop\")",
    'interactive_exercise' => [
        'prompt' => 'Draw a simple star topology with 4 devices connected to a central router.',
        'starter_code' => "# your diagram here (described in text)",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Compare and contrast mesh and tree network topologies. When would you use each?",
        'starter_code' => "# your analysis here",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'In a star network topology, what happens if one device fails?',
            'options' => [
                'a' => 'The entire network goes down',
                'b' => 'Only that device is affected',
                'c' => 'The network automatically reconfigures itself',
                'd' => 'All devices lose internet access',
            ],
            'correct' => 'b',
            'explanation' => "In a star topology, each device connects directly to a central hub. If one device fails, it doesn't affect the others since they have independent connections to the hub.",
        ],
        [
            'question' => 'Which topology requires the most cabling to connect n devices?',
            'options' => [
                'a' => 'Star',
                'b' => 'Bus',
                'c' => 'Ring',
                'd' => 'Mesh',
            ],
            'correct' => 'd',
            'explanation' => "In a full mesh topology, every device connects to every other device, requiring n*(n-1)/2 connections, which grows much faster than other topologies.",
        ],
    ],
];

return $lessonData;
?>