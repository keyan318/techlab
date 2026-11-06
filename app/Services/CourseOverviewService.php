<?php

namespace App\Services;

use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\QuizAnswer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Everything the course overview page shows: the course header, every chapter (module)
 * with each lesson's state for this student, and the progress / badge totals.
 *
 * A lesson is `done`, the one `current` lesson to take next, or `locked` (lessons unlock in order,
 * see CourseProgressService). A chapter's badge is earned once all of its lessons are done.
 * Every chapter ends with its mini boss (MiniBossService): self-paced, the next chapter waits for
 * it to be beaten. After the last one comes the final exam (the "Final Boss", FinalExamService):
 * passing it is what unlocks the certificate.
 */
class CourseOverviewService
{
    public static function build(User $user, string $slug, string $course): ?array
    {
        $meta = CourseStructure::card($slug, $course) ?: null;

        if (! $meta || ! empty($meta['coming_soon'])) {
            return null;
        }

        // Lessons/progress/URLs are keyed by course-structure key, which is the planet slug
        // for a planet's first course (networking, cybersecurity) but the course's own catalog
        // id for a planet's second+ course (e.g. computer-programming-1) — see keyFor().
        $key = CourseProgressService::keyFor($slug, $course);

        $done = array_flip(CourseProgressService::completedKeys($user, $key));

        // The course.member middleware already found the crew for a student; anyone else looks it up.
        $crew = request()->attributes->get('crew')
            ?? Crew::viewFor(Auth::user(), $slug, $course);
        $selfPaced = (bool) $crew?->isSelfPaced();
        // Two queries for every mini boss of the course: the crew's quizzes, and this student's attempts.
        $bosses = $crew ? $crew->miniBosses()->withCount('questions')->get()->keyBy('module') : collect();
        $bossAttempts = $crew ? MiniBossService::attempts($user, $crew) : collect();
        $bossXp = 0;
        $bossXpTotal = 0;
        $bossesCleared = true;
        $heldBy = null;   // self-paced: the page of the mini boss in the way, if one is

        $chapters = [];
        $current = null;
        $first = null;
        $lessonsDone = 0;
        $lessonsTotal = 0;

        foreach (CourseStructure::modules($key) as $moduleKey => $module) {
            $lessons = [];
            $chapterDone = 0;

            foreach (array_values($module['lessons'] ?? []) as $i => $lesson) {
                $lessonKey = array_keys($module['lessons'])[$i];
                [$m, $l] = CourseProgressService::normalize($moduleKey, $lessonKey);
                $url = route('student.planet.module.lesson', ['slug' => $key, 'module' => $m, 'lesson' => $l]);

                $isDone = isset($done["{$m}/{$l}"]);
                // A chapter whose first lesson was done before mini bosses existed stays open.
                $heldBy = $i === 0 && $isDone ? null : $heldBy;
                $state = $isDone ? 'done' : ($current === null && $heldBy === null ? 'current' : 'locked');

                $first ??= $url;
                $current = $state === 'current' ? $url : $current;
                $chapterDone += $isDone ? 1 : 0;

                $lessons[] = [
                    'number' => $i + 1,
                    'title' => $lesson['title'],
                    'state' => $state,
                    'url' => $url,
                    'xp' => StudentDashboardService::MAX_XP_PER_LESSON,
                ];
            }

            $count = count($lessons);
            $lessonsDone += $chapterDone;
            $lessonsTotal += $count;
            $boss = $count && $crew ? self::boss($crew, strtolower($moduleKey), $bosses, $bossAttempts, $count > 0 && $chapterDone === $count) : null;
            if ($boss) {
                $bossXp += $boss['xp'] ?? 0;
                $bossXpTotal += $boss['questions'] * MiniBossService::XP_PER_CORRECT;
                $beaten = in_array($boss['state'], ['passed', 'not_set'], true);
                $bossesCleared = $bossesCleared && (! $selfPaced || $beaten);
                // Self-paced: nothing past this chapter opens until its mini boss is beaten (lessons
                // already done before mini bosses existed stay open, as CourseProgressService allows).
                if ($selfPaced && $current === null && $heldBy === null && $chapterDone === $count && ! $beaten) {
                    $heldBy = $boss['url'];
                }
            }

            $chapters[] = [
                'number' => count($chapters) + 1,
                'title' => trim(preg_replace('/^M\d+\s*[—–-]\s*/u', '', $module['title'])),
                'lessons' => $lessons,
                'done' => $chapterDone,
                'total' => $count,
                'percent' => $count ? (int) round($chapterDone / $count * 100) : 0,
                'complete' => $count > 0 && $chapterDone === $count,
                'boss' => $boss,
            ];
        }

        // Self-paced, the Final Boss also waits for every mini boss.
        $lessonsComplete = $lessonsTotal > 0 && $lessonsDone === $lessonsTotal && $bossesCleared;
        if ($lessonsComplete && $crew?->isSelfPaced()) {
            DefaultFinalExamService::ensure($crew);   // self-paced: the default final, built the first time it's needed
        }
        ['exam' => $exam, 'attempt' => $attempt] = FinalExamService::for($user, $crew);
        $finalState = FinalExamService::state($exam, $attempt, $lessonsComplete);

        $resume = match (true) {
            $lessonsTotal === 0 => null,
            $current !== null => ['label' => $lessonsDone > 0 ? 'Resume Learning' : 'Start Learning', 'url' => $current],
            $heldBy !== null => ['label' => 'Face the Mini Boss', 'url' => $heldBy],
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
            'xpEarned' => $lessonsDone * StudentDashboardService::XP_PER_LESSON
                + (int) QuizAnswer::where('user_id', $user->id)->where('course', $key)->sum('xp') + $bossXp,
            'xpTotal' => $lessonsTotal * StudentDashboardService::MAX_XP_PER_LESSON + $bossXpTotal,
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
                // Failed and sent back to redo the course (the exam reopens once every lesson is done again).
                'redo' => $attempt !== null && FinalExamService::sentBack($attempt),
                'banned' => (bool) $attempt?->banned(),
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

    /**
     * A chapter's mini boss row. A mini boss nobody has reached yet isn't made yet (MiniBossService::ensure
     * runs when it's opened), so its numbers come from the defaults until then.
     */
    private static function boss(Crew $crew, string $module, $bosses, $attempts, bool $chapterDone): array
    {
        $quiz = $bosses->get($module);
        $attempt = $attempts->get($module);
        $state = MiniBossService::state($crew, $module, $quiz, $attempt, $chapterDone);

        return [
            'state' => $state,
            'url' => MiniBossService::url($crew, $module),
            'questions' => $quiz ? $quiz->questions_count : count(MiniBossService::defaults(MiniBossService::keyFor($crew), $module)),
            'minutes' => $quiz?->minutes ?? MiniBossService::DEFAULT_MINUTES,
            'passPercent' => $quiz?->passPercent() ?? CrewQuiz::DEFAULT_PASS_PERCENT,
            'percent' => $attempt?->percent(),
            'xp' => $attempt?->xp,
            'violation' => $attempt?->violationText(),
            'retryAt' => $attempt ? MiniBossService::retryAt($attempt) : null,
            'selfPaced' => $crew->isSelfPaced(),
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
