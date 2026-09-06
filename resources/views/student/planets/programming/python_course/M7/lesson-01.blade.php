<?php
/*
 * Programming M7 Lesson 1: Proposal & Design
 */

$lessonData = [
    'title' => 'Proposal & Design',
    'objective' => 'The student can propose and design a simple Python application to solve a real-world problem.',
    'simple_explanation' => "The final module brings together all the skills learned to design a complete application. This involves planning, breaking down problems, and creating a solution using Python.",
    'astro_explanation' => "Astro\\'s mission software was designed using similar principles: identifying requirements, breaking down complex systems into manageable components, and implementing solutions that work reliably in space.",
    'code_example' => "# Example: Simple mission timer application\nimport time\nfrom datetime import datetime, timedelta\n\nclass MissionTimer:\n    def __init__(self):\n        self.start_time = None\n        self.elapsed_paused = 0\n        self.is_running = False\n    \n    def start(self):\n        if not self.is_running:\n            self.start_time = time.time() - self.elapsed_paused\n            self.is_running = True\n            print(\"Mission timer started.\")\n    \n    def pause(self):\n        if self.is_running:\n            self.elapsed_paused = time.time() - self.start_time\n            self.is_running = False\n            print(f\"Mission timer paused at {self.format_time(self.elapsed_paused)}\")\n    \n    def reset(self):\n        self.start_time = None\n        self.elapsed_paused = 0\n        self.is_running = False\n        print(\"Mission timer reset.\")\n    \n    def format_time(self, seconds):\n        return str(timedelta(seconds=int(seconds)))\n    \n    def get_elapsed(self):\n        if self.is_running:\n            return time.time() - self.start_time\n        else:\n            return self.elapsed_paused\n\n# Example usage\nif __name__ == \"__main__\":\n    timer = MissionTimer()\n    \n    print(\"=== Astro Mission Timer ===\")\n    timer.start()\n    \n    # Simulate some mission activity\n    time.sleep(2)\n    \n    timer.pause()\n    print(f\"Elapsed time: {timer.format_time(timer.get_elapsed())}\")\n    \n    timer.start()\n    time.sleep(1)\n    print(f\"Elapsed time: {timer.format_time(timer.get_elapsed())}\")\n    \n    timer.reset()",
    'interactive_exercise' => [
        'prompt' => 'Think of a simple Python application that could help Astronauts on the ISS. Describe what it would do and what features it would have.',
        'starter_code' => "# your answer here\n",
        'expected_output' => null,
    ],
    'challenge' => [
        'prompt' => "Explain the importance of breaking down a large programming project into smaller, manageable components.",
        'starter_code' => "# your explanation here\n",
        'expected_output' => null,
    ],
    'quiz' => [
        [
            'question' => 'What is the first step in designing a Python application to solve a problem?',
            'options' => [
                'a' => 'Writing the code',
                'b' => 'Choosing variable names',
                'c' => 'Understanding and defining the problem',
                'd' => 'Selecting a Python framework',
            ],
            'correct' => 'c',
            'explanation' => "Before writing any code, you must clearly understand the problem you\\'re trying to solve and define what the application should do.",
        ],
        [
            'question' => 'Which of these is NOT a good practice when designing a software application?',
            'options' => [
                'a' => 'Breaking the problem into smaller parts',
                'b' => 'Planning before writing code',
                'c' => 'Making the application as complex as possible',
                'd' => 'Considering how users will interact with the application',
            ],
            'correct' => 'c',
            'explanation' => "Good software design aims for simplicity and clarity, not unnecessary complexity. Complexity should be managed, not maximized.",
        ],
    ],
];

return $lessonData;
?>