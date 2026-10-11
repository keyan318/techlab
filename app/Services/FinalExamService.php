<?php

namespace App\Services;

use App\Mail\FinalExamViolation;
use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Where a student stands with their course's final exam (the "Final Boss"), in one place so the
 * course hub, the exam page and the certificate always agree.
 *
 *   locked   — lessons still left; the exam opens once every lesson is done
 *   not_set  — every lesson done, but the faculty member hasn't published a final exam yet
 *   ready    — they can take it now
 *   passed   — beaten: the certificate is theirs
 *   failed   — scored under the pass mark, or was caught leaving; the faculty member can allow a retake
 */
class FinalExamService
{
    /**
     * The crew's published final exam and this student's attempt at it.
     *
     * The exam is one page with nowhere else to go, so a student loading any other page while
     * their attempt is still in progress has left it (closed the tab, reloaded, crashed): that
     * attempt is failed here, the same as if the page had reported it on the way out.
     *
     * @return array{exam: ?CrewQuiz, attempt: ?CrewQuizAttempt}
     */
    public static function for(User $user, ?Crew $crew): array
    {
        $exam = $crew?->finalExam()->published()->withCount('questions')->first();
        $attempt = $exam ? $exam->attempts()->where('user_id', $user->id)->first() : null;

        if ($attempt?->inProgress()) {
            self::fail($attempt, 'left_page');
        }

        return ['exam' => $exam, 'attempt' => $attempt];
    }

    public static function state(?CrewQuiz $exam, ?CrewQuizAttempt $attempt, bool $lessonsDone): string
    {
        return match (true) {
            $attempt?->passed() => 'passed',
            $attempt !== null => 'failed',
            ! $lessonsDone => 'locked',
            $exam === null => 'not_set',
            default => 'ready',
        };
    }

    /** Has this student finished every lesson of the crew's course? The exam stays shut until then. */
    public static function lessonsDone(User $user, Crew $crew): bool
    {
        return CourseProgressService::finishedAt($user, CourseProgressService::keyFor($crew->planet, $crew->course_slug)) !== null;
    }

    /**
     * Ends an in-progress attempt as failed because the student broke an exam rule, and tells
     * the faculty member. The conditional update means two reports (the page's own, then the
     * reload check) only fail the exam and send the email once.
     */
    public static function fail(CrewQuizAttempt $attempt, string $violation): void
    {
        $ended = CrewQuizAttempt::whereKey($attempt->id)
            ->where('status', CrewQuizAttempt::IN_PROGRESS)
            ->update(['status' => CrewQuizAttempt::FAILED, 'score' => 0, 'violation' => $violation, 'finished_at' => now()]);

        $attempt->refresh();

        if (! $ended) {
            return;
        }

        $faculty = $attempt->quiz->crew->faculty;

        // A mail server hiccup must not turn the student's "exam ended" screen into an error.
        try {
            if ($faculty?->email) {
                Mail::to($faculty)->queue(new FinalExamViolation($attempt));
            }
        } catch (\Throwable $e) {
            Log::warning('Final exam alert email failed: '.$e->getMessage());
        }
    }
}
