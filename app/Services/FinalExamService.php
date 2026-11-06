<?php

namespace App\Services;

use App\Mail\FinalExamViolation;
use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
 *   failed   — scored under the pass mark, ran out of time or was caught leaving. They are sent back
 *              to the start of the course; once every lesson is done again the exam is `ready`
 *              for another try. The faculty member can also allow a retake straight away.
 *              Caught a second time (on a later try) they are banned instead: still `failed`, but
 *              never sent back, so only the faculty member's "Allow retake" opens it again.
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
            // Sent back through the course and every lesson done again: the Final Boss is back.
            $attempt !== null && ! (self::sentBack($attempt) && $lessonsDone) => 'failed',
            ! $lessonsDone => 'locked',
            $exam === null => 'not_set',
            default => 'ready',
        };
    }

    /**
     * Has this student finished every lesson of the crew's course (and, self-paced, beaten every module's
     * mini boss)? The exam stays shut until then.
     */
    public static function lessonsDone(User $user, Crew $crew): bool
    {
        return CourseProgressService::finishedAt($user, CourseProgressService::keyFor($crew->planet, $crew->course_slug)) !== null
            && MiniBossService::allCleared($user, $crew);
    }

    /**
     * Ends an in-progress attempt as failed because the student broke an exam rule, and tells
     * the faculty member. The conditional update means two reports (the page's own, then the
     * reload check) only fail the exam, add the strike and send the email once.
     *
     * Cheating is a strike (running out of time is not). The first strike sends the student back
     * to redo the course like any fail; the second (on a later try) bans them from the exam, so
     * there is no course to redo and their lessons stay.
     */
    public static function fail(CrewQuizAttempt $attempt, string $violation): void
    {
        $strikes = $attempt->strikes + ($violation === 'time_up' ? 0 : 1);

        $ended = CrewQuizAttempt::whereKey($attempt->id)
            ->where('status', CrewQuizAttempt::IN_PROGRESS)
            ->update(['status' => CrewQuizAttempt::FAILED, 'score' => 0, 'violation' => $violation, 'strikes' => $strikes, 'finished_at' => now()]);

        $attempt->refresh();

        if (! $ended) {
            return;
        }

        if (! $attempt->banned()) {
            self::sendBackToStart($attempt);
        }

        // A class's teacher hears about it; a self-paced run has no teacher, so its admins do.
        $crew = $attempt->quiz->crew;
        $watchers = $crew->isSelfPaced()
            ? User::where('role', 'admin')->whereNotNull('email')->get()
            : collect([$crew->faculty])->filter(fn ($f) => $f?->email);

        // A mail server hiccup must not turn the student's "exam ended" screen into an error.
        try {
            foreach ($watchers as $watcher) {
                Mail::to($watcher)->queue(new FinalExamViolation($attempt));
            }
        } catch (\Throwable $e) {
            Log::warning('Final exam alert email failed: '.$e->getMessage());
        }
    }

    /** Was this failed attempt's student sent back to redo the course (rather than an old fail from before that rule)? */
    public static function sentBack(CrewQuizAttempt $attempt): bool
    {
        return $attempt->status === CrewQuizAttempt::FAILED && $attempt->wiped_progress !== null;
    }

    /**
     * Failing the Final Boss, however it happens, means doing the whole course again: this
     * student's lessons in it are wiped, so they relock in order and every challenge and lab has
     * to be passed again. What was wiped is kept on the attempt so "Allow retake" can give it back.
     */
    public static function sendBackToStart(CrewQuizAttempt $attempt): void
    {
        $crew = $attempt->quiz->crew;
        $key = CourseProgressService::keyFor($crew->planet, $crew->course_slug);

        DB::transaction(function () use ($attempt, $key) {
            $rows = LessonProgress::where('user_id', $attempt->user_id)->where('course', $key)->get(['module', 'lesson', 'completed_at']);

            $attempt->forceFill(['wiped_progress' => $rows->map(fn (LessonProgress $r) => [
                'module' => $r->module,
                'lesson' => $r->lesson,
                'completed_at' => $r->completed_at?->toISOString(),
            ])->values()->all()])->save();

            LessonProgress::where('user_id', $attempt->user_id)->where('course', $key)->delete();
        });
    }

    /** The faculty member's "Allow retake": the lessons wiped by the fail come back, so the student can sit it again now. */
    public static function giveBackProgress(CrewQuizAttempt $attempt): void
    {
        $crew = $attempt->quiz->crew;
        $key = CourseProgressService::keyFor($crew->planet, $crew->course_slug);

        foreach ($attempt->wiped_progress ?? [] as $row) {
            LessonProgress::firstOrCreate(
                ['user_id' => $attempt->user_id, 'course' => $key, 'module' => $row['module'], 'lesson' => $row['lesson']],
                ['completed_at' => $row['completed_at'] ?? now(), 'crew_id' => $crew->id],
            );
        }
    }
}
