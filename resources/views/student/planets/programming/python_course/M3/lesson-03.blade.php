<?php
/*
 * Programming M3 Lesson 3: Scope
 */

$lessonData = [
    'title' => 'Scope',
    'objective' => 'The student can explain variable scope in Python and how it affects variable accessibility.',
    'simple_explanation' => "Variable scope determines where in your code a variable can be accessed. Variables defined inside a function have local scope and can only be accessed within that function.",
    'astro_explanation' => "Astro uses scope to protect critical variables: internal calculation variables are local to functions, while mission status variables might be global for broader access.",
    'code_example' => "# Global scope\nmission_time = 0  # Accessible throughout the script\n\ndef update_mission_time(hours):\n    global mission_time  # Declare we're using the global variable\n    mission_time += hours\n    return mission_time\n\n# Local scope\ndef calculate_orbit_speed(altitude):\n    gravitational_constant = 6.67430e-11  # Local to this function\n    earth_mass = 5.972e24\n    # ... calculation using local variables\n    return orbital_speed\n\nprint(mission_time)  # Works - global variable\nprint(gravitational_constant)  # Error - not accessible outside function",
    'interactive_exercise' => [
        'prompt' => 'Write a function that uses a local variable and try to access it outside the function.',
        'starter_code' => "# your code here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the difference between local scope, global scope, and enclosed (nonlocal) scope in Python.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the output of this code?\nx = 10  # Global variable\n\ndef my_function():\n    x = 5  # Local variable\n    print(x)  # Prints local x\n\nmy_function()\nprint(x)  # Prints global x',
            'options' => [
                'a' => '5 5',
                'b' => '5 10',
                'c' => '10 5',
                'd' => '10 10',
            ],
            'correct' => 'b',
            'explanation' => "Inside the function, x refers to the local variable (5). Outside the function, x refers to the global variable (10).",
        ],
        [
            'question' => 'What happens if you try to modify a global variable inside a function without declaring it as global?',
            'options' => [
                'a' => 'It modifies the global variable',
                'b' => 'It creates a new local variable with the same name',
                'c' => 'It causes an error',
                'd' => 'It depends on the Python version',
            ],
            'correct' => 'b',
            'explanation' => "Without the global declaration, Python treats the variable as local, creating a new local variable instead of modifying the global one.",
        ],
    ],
];

return $lessonData;
?>