<?php

namespace App\Services;

use App\Models\LessonCheckpoint;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * In-lesson checkpoints: one question at the end of a lesson stage. The next stage stays locked until it is
 * answered right, so a student can't scroll past the reading straight to the Knowledge Check.
 *
 * Written in the lesson Blade as `<div class="checkpoint-block" data-cp="N">`, with the same inner markup as a
 * quiz question (`.quiz-prompt`, `.quiz-option` + `.quiz-correct`, `.quiz-explanation`), so LessonQuizService::strip
 * already removes the answer from what the browser gets. Unlike the quiz, a checkpoint can be retried (it is a
 * door, not a grade), but the right answer is never revealed on a miss, and each miss waits a little longer.
 *
 * A lesson with no checkpoint blocks is unaffected. Finished lessons, and anyone who isn't a student, see
 * every stage open.
 */
class LessonCheckpointService
{
    /**
     * @return array<int, array{correct: string, explanation: string}> checkpoint number => key
     */
    public static function key(string $html): array
    {
        $key = [];
        if (! preg_match_all('/<div class="checkpoint-block" data-cp="(\d+)">(.*?)<\/div>/s', $html, $blocks, PREG_SET_ORDER)) {
            return $key;
        }

        foreach ($blocks as $b) {
            if (preg_match('/<li class="quiz-option quiz-correct">\s*([A-D])\./', $b[2], $m)) {
                $key[(int) $b[1]] = [
                    'correct' => $m[1],
                    'explanation' => preg_match('/<p class="quiz-explanation">(.*?)<\/p>/s', $b[2], $e) ? trim($e[1]) : '',
                ];
            }
        }

        return $key;
    }

    /** Seconds to wait before retrying after this many misses: 5, 10, then 20. */
    public static function cooldown(int $misses): int
    {
        return $misses <= 0 ? 0 : [1 => 5, 2 => 10][$misses] ?? 20;
    }

    /** Seconds left before this row may be retried. */
    public static function waitFor(?LessonCheckpoint $row): int
    {
        if (! $row || $row->passed_at || ! $row->last_miss_at) {
            return 0;
        }
        $until = $row->last_miss_at->copy()->addSeconds(self::cooldown($row->misses));

        return max(0, (int) ceil(Carbon::now()->diffInSeconds($until, false)));
    }

    /** Everything is open: no checkpoints, a finished lesson, or someone who isn't a student (faculty previewing). */
    public static function exempt(User $user, bool $completed, array $key): bool
    {
        return ! $key || $completed || ($user->role ?? 'student') !== 'student';
    }

    /** @return Collection<int, LessonCheckpoint> keyed by checkpoint number */
    public static function rows(User $user, string $course, string $module, string $lesson)
    {
        return LessonCheckpoint::where(['user_id' => $user->id, 'course' => $course, 'module' => $module, 'lesson' => $lesson])
            ->get()->keyBy('checkpoint');
    }

    /**
     * Whether every checkpoint of the lesson is passed (or none applies), so its Knowledge Check may be answered.
     * $html is the lesson's rendered source when the caller already has it.
     */
    public static function cleared(User $user, string $course, string $module, string $lesson, bool $completed, ?string $html = null): bool
    {
        $html ??= LessonQuizService::source($module, $lesson, $course);
        $key = $html === null ? [] : self::key($html);
        if (self::exempt($user, $completed, $key)) {
            return true;
        }

        $passed = self::rows($user, $course, $module, $lesson)->filter(fn ($r) => $r->passed_at)->keys()->all();

        return ! array_diff(array_keys($key), $passed);
    }
}
