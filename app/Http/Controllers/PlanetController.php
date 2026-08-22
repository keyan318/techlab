<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PlanetController extends Controller
{
    /**
     * The available mission planets (student learning tracks).
     */
    private const PLANETS = ['programming', 'networking', 'cybersecurity'];

    /**
     * Per-planet course shells.
     *
     * Each planet exposes a small placeholder curriculum so the course player
     * UI has something to render before the real content lands. The shape is
     * intentionally simple so it can be swapped for a database-driven model
     * later without touching the view layer.
     *
     * @return array<string, mixed>
     */
    private function courseShell(string $slug): array
    {
        return match ($slug) {
            'programming' => [
                'id' => 'programming',
                'title' => 'Programming City',
                'tag' => 'Programming track',
                'blurb' => 'A guided tour through the building blocks of software — from your first variable to shipping a small project.',
                'sections' => [
                    ['title' => 'Section 1 — Introduction', 'lessons' => [
                        ['title' => 'Welcome to Programming'],
                        ['title' => 'How a Computer Reads Code'],
                        ['title' => 'Setting Up Your Workspace'],
                    ]],
                    ['title' => 'Section 2 — Fundamentals', 'lessons' => [
                        ['title' => 'Variables and Values'],
                        ['title' => 'Conditions and Logic'],
                        ['title' => 'Loops and Repetition'],
                        ['title' => 'Functions'],
                    ]],
                    ['title' => 'Section 3 — Practice', 'lessons' => [
                        ['title' => 'Working with Lists'],
                        ['title' => 'Strings and Text'],
                        ['title' => 'Reading Errors'],
                    ]],
                    ['title' => 'Section 4 — Build', 'lessons' => [
                        ['title' => 'Planning a Small Project'],
                        ['title' => 'Putting It Together'],
                        ['title' => 'Module Checkpoint'],
                    ]],
                ],
            ],
            'networking' => [
                'id' => 'networking',
                'title' => 'Networking Nebula',
                'tag' => 'Networking track',
                'blurb' => 'A guided tour of how machines talk — packets, protocols, and the paths between them.',
                'sections' => [
                    ['title' => 'Section 1 — Introduction', 'lessons' => [
                        ['title' => 'Welcome to Networking'],
                        ['title' => 'What Is a Network?'],
                        ['title' => 'A Tour of the Internet'],
                    ]],
                    ['title' => 'Section 2 — Fundamentals', 'lessons' => [
                        ['title' => 'IP Addresses'],
                        ['title' => 'Packets and Routing'],
                        ['title' => 'DNS Basics'],
                    ]],
                    ['title' => 'Section 3 — Practice', 'lessons' => [
                        ['title' => 'Reading a Trace'],
                        ['title' => 'Common Tools'],
                        ['title' => 'Diagnosing Issues'],
                    ]],
                    ['title' => 'Section 4 — Build', 'lessons' => [
                        ['title' => 'Designing a Small LAN'],
                        ['title' => 'Putting It Together'],
                        ['title' => 'Module Checkpoint'],
                    ]],
                ],
            ],
            'cybersecurity' => [
                'id' => 'cybersecurity',
                'title' => 'Cybersecurity Citadel',
                'tag' => 'Cybersecurity track',
                'blurb' => 'A guided tour of how to defend systems — threats, defenses, and the mindset of a defender.',
                'sections' => [
                    ['title' => 'Section 1 — Introduction', 'lessons' => [
                        ['title' => 'Welcome to Cybersecurity'],
                        ['title' => 'Threats in the Wild'],
                        ['title' => 'The Defender Mindset'],
                    ]],
                    ['title' => 'Section 2 — Fundamentals', 'lessons' => [
                        ['title' => 'Authentication Basics'],
                        ['title' => 'Encryption Concepts'],
                        ['title' => 'Common Attack Surfaces'],
                    ]],
                    ['title' => 'Section 3 — Practice', 'lessons' => [
                        ['title' => 'Reading Logs'],
                        ['title' => 'Spotting Suspicious Activity'],
                        ['title' => 'Hardening a System'],
                    ]],
                    ['title' => 'Section 4 — Build', 'lessons' => [
                        ['title' => 'Designing a Defense Plan'],
                        ['title' => 'Putting It Together'],
                        ['title' => 'Module Checkpoint'],
                    ]],
                ],
            ],
            default => [],
        };
    }

    /**
     * Normalize a course shell so the view can render it predictably:
     * flat lesson list with stable ids, progress states, and counts.
     *
     * @param  array<string, mixed>  $course
     * @return array<string, mixed>
     */
    private function normalizeCourse(array $course): array
    {
        $sections = [];
        $lessons = [];

        foreach ($course['sections'] as $sectionIndex => $section) {
            $sectionLessons = [];

            foreach ($section['lessons'] as $lessonIndex => $lesson) {
                $isFirstLesson = $sectionIndex === 0 && $lessonIndex === 0;
                // Static placeholder progress: only the very first lesson is
                // unlocked-and-current. All later lessons are not-yet-started.
                // A future iteration can wire this to user progress.
                $state = $isFirstLesson ? 'current' : 'not_started';

                $lessonId = $course['id'].'-s'.$sectionIndex.'-l'.$lessonIndex;
                $sectionLessons[] = [
                    'id' => $lessonId,
                    'title' => $lesson['title'],
                    'state' => $state,
                    'section' => $sectionIndex,
                    'resources' => [],
                ];
                $lessons[] = $lessonId;
            }

            $sections[] = [
                'title' => $section['title'],
                'lessons' => $sectionLessons,
                'completed' => 0,
                'total' => count($sectionLessons),
            ];
        }

        $total = count($lessons);
        $currentId = $lessons[0] ?? null;

        return [
            'id' => $course['id'],
            'title' => $course['title'],
            'tag' => $course['tag'],
            'blurb' => $course['blurb'],
            'sections' => $sections,
            'lessons' => $lessons,
            'current' => $currentId,
            'completed' => 0,
            'total' => $total,
            'percent' => 0,
        ];
    }

    /**
     * Show a planet's overview page. Unknown slugs 404; guests are sent to login.
     */
    public function show(string $slug): View
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! in_array($slug, self::PLANETS, true)) {
            abort(404);
        }

        $course = $this->normalizeCourse($this->courseShell($slug));

        return view('student.planets.'.$slug, [
            'course' => $course,
        ]);
    }
}
