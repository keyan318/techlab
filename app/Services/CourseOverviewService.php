<?php

namespace App\Services;

use App\Models\Crew;
use App\Models\User;

/**
 * Everything the course overview page shows: the course header, every chapter (module)
 * with each lesson's state for this student, and the progress / badge totals.
 *
 * A lesson is `done`, the one `current` lesson to take next, or `locked` (lessons unlock in order,
 * see CourseProgressService). A chapter's badge is earned once all of its lessons are done.
 * After the last lesson comes the faculty's final exam (the "Final Boss", FinalExamService):
 * passing it is what unlocks the certificate.
 */
class CourseOverviewService
{
    public static function build(User $user, string $slug, string $course): ?array
    {
        $meta = config("course-catalog.{$slug}.courses.{$course}");

        if (! $meta || ! empty($meta['coming_soon'])) {
            return null;
        }

        // Lessons/progress/URLs are keyed by course-structure key, which is the planet slug
        // for a planet's first course (networking, cybersecurity) but the course's own catalog
        // id for a planet's second+ course (e.g. computer-programming-1) — see keyFor().
        $key = CourseProgressService::keyFor($slug, $course);

        $done = array_flip(CourseProgressService::completedKeys($user, $key));
        $chapters = [];
        $current = null;
        $first = null;
        $lessonsDone = 0;
        $lessonsTotal = 0;

        foreach (config("course-structure.{$key}.modules", []) as $moduleKey => $module) {
            $lessons = [];
            $chapterDone = 0;

            foreach (array_values($module['lessons'] ?? []) as $i => $lesson) {
                $lessonKey = array_keys($module['lessons'])[$i];
                [$m, $l] = CourseProgressService::normalize($moduleKey, $lessonKey);
                $url = route('student.planet.module.lesson', ['slug' => $key, 'module' => $m, 'lesson' => $l]);

                $isDone = isset($done["{$m}/{$l}"]);
                $state = $isDone ? 'done' : ($current === null ? 'current' : 'locked');

                $first ??= $url;
                $current = $state === 'current' ? $url : $current;
                $chapterDone += $isDone ? 1 : 0;

                $lessons[] = [
                    'number' => $i + 1,
                    'title' => $lesson['title'],
                    'state' => $state,
                    'url' => $url,
                    'xp' => StudentDashboardService::XP_PER_LESSON,
                ];
            }

            $count = count($lessons);
            $lessonsDone += $chapterDone;
            $lessonsTotal += $count;

            $chapters[] = [
                'number' => count($chapters) + 1,
                'title' => trim(preg_replace('/^M\d+\s*[—–-]\s*/u', '', $module['title'])),
                'lessons' => $lessons,
                'done' => $chapterDone,
                'total' => $count,
                'percent' => $count ? (int) round($chapterDone / $count * 100) : 0,
                'complete' => $count > 0 && $chapterDone === $count,
            ];
        }

        // The course.member middleware already found the crew for a student; anyone else looks it up.
        $crew = request()->attributes->get('crew')
            ?? Crew::where('planet', $slug)->where('course_slug', $course)->first();
        $lessonsComplete = $lessonsTotal > 0 && $lessonsDone === $lessonsTotal;
        ['exam' => $exam, 'attempt' => $attempt] = FinalExamService::for($user, $crew);
        $finalState = FinalExamService::state($exam, $attempt, $lessonsComplete);

        $resume = match (true) {
            $lessonsTotal === 0 => null,
            $current !== null => ['label' => $lessonsDone > 0 ? 'Resume Learning' : 'Start Learning', 'url' => $current],
            default => ['label' => 'Review Course', 'url' => $first],
        };

        return [
            'user' => $user,
            'level' => StudentDashboardService::levelFor(StudentDashboardService::earnedXp($user)),
            'title' => $meta['title'],
            'description' => $meta['description'] ?? ($meta['blurb'] ?? ''),
            'courseLevel' => $meta['level'] ?? null,
            'banner' => $meta['banner'] ?? '#1b1650',
            'scene' => $meta['scene'] ?? null,
            'art' => $meta['art'] ?? null,
            'backUrl' => route('student.planet', $slug),
            'chapters' => $chapters,
            'lessonsDone' => $lessonsDone,
            'lessonsTotal' => $lessonsTotal,
            'percent' => $lessonsTotal ? (int) round($lessonsDone / $lessonsTotal * 100) : 0,
            'xpEarned' => $lessonsDone * StudentDashboardService::XP_PER_LESSON,
            'xpTotal' => $lessonsTotal * StudentDashboardService::XP_PER_LESSON,
            'badgesEarned' => count(array_filter($chapters, fn ($c) => $c['complete'])),
            'resume' => $resume,
            'finalExam' => [
                'state' => $finalState,
                'url' => route('student.course.final-exam', ['slug' => $slug, 'course' => $course]),
                'title' => $exam?->title,
                'questions' => $exam?->questions_count,
                'minutes' => $exam?->minutes,
                'passPercent' => $exam?->passPercent(),
                'percent' => $attempt?->percent(),
                'violation' => $attempt?->violationText(),
            ],
            // Passing the final exam earns the certificate (CourseCertificateController).
            'certificateUrl' => $finalState === 'passed'
                ? route('student.course.certificate', ['slug' => $slug, 'course' => $course])
                : null,
            // The course's syllabus, if its first resource is one — shown as an inline preview
            // on the hub's Home tab (see components/programming-sidebar.blade.php for the full list).
            'syllabus' => self::syllabus($key),
        ];
    }

    private static function syllabus(string $key): ?array
    {
        $first = config("course-structure.{$key}.resources.0");

        if (! $first || ($first['type'] ?? null) !== 'pdf' || stripos($first['title'] ?? '', 'syllabus') === false) {
            return null;
        }

        return $first;
    }
}
