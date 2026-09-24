<?php

namespace App\Services;

use App\Models\User;

/**
 * Everything the course overview page shows: the course header, every chapter (module)
 * with each lesson's state for this student, and the progress / badge totals.
 *
 * A lesson is `done`, the one `current` lesson to take next, or `locked` (lessons unlock in order,
 * see CourseProgressService). A chapter's badge is earned once all of its lessons are done.
 */
class CourseOverviewService
{
    public static function build(User $user, string $slug, string $course): ?array
    {
        $meta = config("course-catalog.{$slug}.courses.{$course}");

        if (! $meta || ! empty($meta['coming_soon'])) {
            return null;
        }

        $done = array_flip(CourseProgressService::completedKeys($user, $slug));
        $chapters = [];
        $current = null;
        $first = null;
        $lessonsDone = 0;
        $lessonsTotal = 0;

        foreach (config("course-structure.{$slug}.modules", []) as $moduleKey => $module) {
            $lessons = [];
            $chapterDone = 0;

            foreach (array_values($module['lessons'] ?? []) as $i => $lesson) {
                $lessonKey = array_keys($module['lessons'])[$i];
                [$m, $l] = CourseProgressService::normalize($moduleKey, $lessonKey);
                $url = route('student.planet.module.lesson', ['slug' => $slug, 'module' => $m, 'lesson' => $l]);

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
            'backUrl' => route('student.planet', $slug),
            'chapters' => $chapters,
            'lessonsDone' => $lessonsDone,
            'lessonsTotal' => $lessonsTotal,
            'percent' => $lessonsTotal ? (int) round($lessonsDone / $lessonsTotal * 100) : 0,
            'xpEarned' => $lessonsDone * StudentDashboardService::XP_PER_LESSON,
            'xpTotal' => $lessonsTotal * StudentDashboardService::XP_PER_LESSON,
            'badgesEarned' => count(array_filter($chapters, fn ($c) => $c['complete'])),
            'resume' => $resume,
        ];
    }
}
