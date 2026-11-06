<?php

namespace App\Services;

use App\Mail\FinalExamViolation;
use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
use App\Models\CrewQuizQuestion;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Mini bosses: a full-screen exam at the end of every module, with the Final Boss's rules (leaving full
 * screen, switching tab or window, reloading or reaching for a screenshot ends it as a fail and alerts the
 * class's faculty member, or the admins for self-paced). Every right answer is worth XP_PER_CORRECT.
 *
 *   Self-paced  the next module stays locked until the mini boss is passed. A fail (low score, time up or
 *               caught leaving) earns no XP, and the student can try again after COOLDOWN_MINUTES of study.
 *   Class       one try. Pass or fail, the student moves on; their score and XP are recorded for the
 *               faculty member, who sets the time and pass mark and can rewrite the questions.
 *
 * Every crew's mini boss starts as the default questions in resources/mini-bosses/{course key}/{module}.json
 * (at least 30 per module). It is made the first time it is needed, so nothing is written for modules nobody
 * has reached. A module the faculty member added has no default: theirs starts as an empty draft.
 *
 * Per student and module the state is:
 *   locked    the module's lessons aren't all done yet
 *   not_set   lessons done, but there is no published mini boss (a faculty-added module they haven't written)
 *   ready     they can take it now
 *   cooldown  self-paced, failed: they can try again at `retry_at`
 *   passed    beaten
 *   failed    class, below the pass mark: recorded, and they carry on
 */
class MiniBossService
{
    public const XP_PER_CORRECT = 10;

    public const COOLDOWN_MINUTES = 60;

    public const DEFAULT_MINUTES = 45;

    /** The minimum a default mini boss holds (checked by MiniBossTest). */
    public const MIN_DEFAULT_QUESTIONS = 30;

    /**
     * The default questions of one module, or [] when it has none.
     *
     * @return list<array{prompt: string, options: list<string>, correct: int}>
     */
    public static function defaults(string $key, string $module): array
    {
        $path = self::defaultsPath($key, $module);
        if (! is_file($path)) {
            return [];
        }

        $questions = json_decode((string) file_get_contents($path), true)['questions'] ?? [];

        return array_values(array_map(fn (array $q) => [
            'prompt' => (string) $q['prompt'],
            'options' => array_values(array_map('strval', $q['options'])),
            'correct' => ord(strtoupper((string) $q['correct'])) - 65,
        ], $questions));
    }

    public static function hasDefaults(string $key, string $module): bool
    {
        return is_file(self::defaultsPath($key, $module));
    }

    public static function defaultsPath(string $key, string $module): string
    {
        return resource_path('mini-bosses/'.$key.'/'.strtolower($module).'.json');
    }

    /**
     * The modules of a crew's course that have lessons, in order: [m1 => title, …].
     *
     * @return array<string, string>
     */
    public static function modules(string $key): array
    {
        $out = [];
        foreach (CourseStructure::modules($key) as $moduleKey => $module) {
            if (! empty($module['lessons'])) {
                $out[strtolower($moduleKey)] = trim(preg_replace('/^M\d+\s*[—–-]\s*/u', '', $module['title']));
            }
        }

        return $out;
    }

    public static function keyFor(Crew $crew): string
    {
        return CourseProgressService::keyFor($crew->planet, $crew->course_slug);
    }

    /**
     * The crew's mini boss for a module, made from the default questions the first time it is needed. Published
     * straight away; a module without defaults gets an empty draft for the faculty member to write.
     */
    public static function ensure(Crew $crew, string $module): CrewQuiz
    {
        $module = strtolower($module);
        if ($existing = $crew->miniBosses()->where('module', $module)->first()) {
            return $existing;
        }

        $key = self::keyFor($crew);
        $questions = self::defaults($key, $module);
        $title = self::modules($key)[$module] ?? strtoupper($module);

        return DB::transaction(function () use ($crew, $module, $questions, $title) {
            // Someone else made it in the meantime: keep theirs.
            if ($existing = $crew->miniBosses()->where('module', $module)->lockForUpdate()->first()) {
                return $existing;
            }

            $quiz = $crew->quizzes()->create([
                'title' => 'Mini Boss: '.$title,
                'description' => 'Everything from Module '.substr($module, 1).'. Beat it to prove you are ready for what comes next.',
                'minutes' => self::DEFAULT_MINUTES,
                'module' => $module,
                'pass_percent' => CrewQuiz::DEFAULT_PASS_PERCENT,
                'published_at' => $questions ? now() : null,
            ]);
            self::writeDefaults($quiz, $questions);

            return $quiz;
        });
    }

    /** Every module's mini boss for a crew (what the faculty member's Mini bosses page lists). */
    public static function ensureAll(Crew $crew): Collection
    {
        $have = $crew->miniBosses()->pluck('module')->all();
        foreach (array_keys(self::modules(self::keyFor($crew))) as $module) {
            if (! in_array($module, $have, true)) {
                self::ensure($crew, $module);
            }
        }

        return $crew->miniBosses()->withCount('questions')->get()->keyBy('module');
    }

    /** The faculty member's "Reset to default": their questions are replaced by TechLab's. Results stay. */
    public static function reset(CrewQuiz $quiz): void
    {
        $questions = self::defaults(self::keyFor($quiz->crew), $quiz->module);

        DB::transaction(function () use ($quiz, $questions) {
            $quiz->questions()->delete();
            $quiz->update([
                'minutes' => self::DEFAULT_MINUTES,
                'pass_percent' => CrewQuiz::DEFAULT_PASS_PERCENT,
                'published_at' => $questions ? ($quiz->published_at ?? now()) : null,
            ]);
            self::writeDefaults($quiz, $questions);
        });
    }

    /** One insert for every question: the database is remote, and 30 inserts would take seconds. */
    private static function writeDefaults(CrewQuiz $quiz, array $questions): void
    {
        $now = now();
        $rows = array_map(fn (array $q, int $i) => [
            'crew_quiz_id' => $quiz->id,
            'prompt' => $q['prompt'],
            'source' => null,
            'options' => json_encode($q['options']),
            'type' => 'multiple_choice',
            'correct_index' => $q['correct'],
            'correct_indexes' => null,
            'required' => true,
            'position' => $i,
            'created_at' => $now,
            'updated_at' => $now,
        ], $questions, array_keys($questions));

        if ($rows) {
            CrewQuizQuestion::insert($rows);
        }
    }

    /**
     * This student's attempts at the crew's mini bosses, keyed by module. The exam is one page with nowhere
     * else to go, so an attempt still in progress while they load any other page means they left it: it
     * fails here, the same as if the page had reported it on the way out.
     *
     * @return Collection<string, CrewQuizAttempt>
     */
    public static function attempts(User $user, Crew $crew): Collection
    {
        $attempts = CrewQuizAttempt::with('quiz:id,crew_id,module')
            ->where('user_id', $user->id)
            ->whereHas('quiz', fn ($q) => $q->where('crew_id', $crew->id)->whereNotNull('module'))
            ->get();

        foreach ($attempts as $attempt) {
            if ($attempt->inProgress()) {
                self::fail($attempt, 'left_page');
            }
        }

        return $attempts->keyBy(fn (CrewQuizAttempt $a) => $a->quiz->module);
    }

    public static function state(Crew $crew, string $module, ?CrewQuiz $quiz, ?CrewQuizAttempt $attempt, bool $moduleDone): string
    {
        if ($attempt?->passed()) {
            return 'passed';
        }
        if ($attempt && ! $crew->isSelfPaced()) {
            return 'failed';
        }
        if ($attempt && self::retryAt($attempt)?->isFuture()) {
            return 'cooldown';
        }

        return match (true) {
            ! $moduleDone => 'locked',
            $quiz ? ! $quiz->isPublished() : ! self::hasDefaults(self::keyFor($crew), $module) => 'not_set',
            default => 'ready',
        };
    }

    /** When a failed self-paced student may face the mini boss again. */
    public static function retryAt(CrewQuizAttempt $attempt): ?Carbon
    {
        return $attempt->status === CrewQuizAttempt::FAILED && $attempt->finished_at
            ? $attempt->finished_at->copy()->addMinutes(self::COOLDOWN_MINUTES)
            : null;
    }

    /** Has the student finished every lesson of this module? */
    public static function moduleDone(User $user, string $key, string $module): bool
    {
        $module = strtolower($module);
        $lessons = array_filter(CourseProgressService::order($key), fn (array $i) => $i['module'] === $module);
        if ($lessons === []) {
            return false;
        }
        $done = array_flip(CourseProgressService::completedKeys($user, $key));

        return collect($lessons)->every(fn (array $i) => isset($done[$i['module'].'/'.$i['lesson']]));
    }

    /**
     * May this student go past the end of a module? Only self-paced has to beat the mini boss first; a class
     * carries on whatever the score. Asked once per module boundary, so it costs a query only there.
     */
    public static function cleared(User $user, string $key, string $module): bool
    {
        $crew = CourseTrack::crew($user, $key);
        // Self-paced always runs the default course: a module with no default mini boss has nothing to beat.
        if (! $crew?->isSelfPaced() || ! self::hasDefaults($key, $module)) {
            return true;
        }

        return CrewQuizAttempt::where('user_id', $user->id)->where('status', CrewQuizAttempt::PASSED)
            ->whereHas('quiz', fn ($q) => $q->where('crew_id', $crew->id)->where('module', strtolower($module)))
            ->exists();
    }

    /** Self-paced: has the student beaten every module's mini boss (the Final Boss waits for that too)? */
    public static function allCleared(User $user, Crew $crew): bool
    {
        if (! $crew->isSelfPaced()) {
            return true;
        }
        $key = self::keyFor($crew);
        $modules = array_values(array_filter(array_keys(self::modules($key)), fn (string $m) => self::hasDefaults($key, $m)));
        $passed = CrewQuizAttempt::where('user_id', $user->id)->where('status', CrewQuizAttempt::PASSED)
            ->join('crew_quizzes', 'crew_quizzes.id', '=', 'crew_quiz_attempts.crew_quiz_id')
            ->where('crew_quizzes.crew_id', $crew->id)->whereIn('crew_quizzes.module', $modules)
            ->distinct()->count('crew_quizzes.module');

        return $passed >= count($modules);
    }

    /** The student's page for a module's mini boss. */
    public static function url(Crew $crew, string $module): string
    {
        return route('student.course.mini-boss', ['slug' => $crew->planet, 'course' => $crew->course_slug, 'module' => strtolower($module)]);
    }

    /** The same, from a lesson-URL key (for the lesson lock, which only knows the key). */
    public static function urlForKey(string $key, string $module): ?string
    {
        $course = CourseProgressService::catalogFor($key);

        return $course ? route('student.course.mini-boss', ['slug' => CourseProgressService::planetFor($key), 'course' => $course, 'module' => strtolower($module)]) : null;
    }

    /**
     * Grades a handed-in attempt and closes it. Conditional on it still being open, so a submit racing a
     * "caught leaving" report can't overwrite it: the first outcome stands.
     */
    public static function finish(CrewQuiz $quiz, CrewQuizAttempt $attempt, array $answers): CrewQuizAttempt
    {
        $quiz->loadMissing('questions');
        $graded = $quiz->grade($answers);
        $passed = $graded['total'] > 0 && $quiz->passPercent() <= $graded['score'] / $graded['total'] * 100;
        // Self-paced, a fail wipes the XP the right answers would have earned; a class keeps it.
        $xp = $passed || ! $quiz->crew->isSelfPaced() ? $graded['score'] * self::XP_PER_CORRECT : 0;

        CrewQuizAttempt::whereKey($attempt->id)->where('status', CrewQuizAttempt::IN_PROGRESS)->update([
            'score' => $graded['score'],
            'total' => $graded['total'],
            'xp' => $xp,
            'answers' => json_encode($graded['answers']),
            'status' => $passed ? CrewQuizAttempt::PASSED : CrewQuizAttempt::FAILED,
            'finished_at' => now(),
        ]);

        return $attempt->refresh();
    }

    /**
     * Ends an in-progress attempt as failed because the student broke an exam rule (or ran out of time),
     * with no score and no XP, and tells whoever watches the crew. Conditional, so two reports (the page's
     * own, then the reload check) only fail it and send the email once.
     */
    public static function fail(CrewQuizAttempt $attempt, string $violation): void
    {
        $ended = CrewQuizAttempt::whereKey($attempt->id)->where('status', CrewQuizAttempt::IN_PROGRESS)->update([
            'status' => CrewQuizAttempt::FAILED, 'score' => 0, 'xp' => 0, 'violation' => $violation, 'finished_at' => now(),
        ]);
        $attempt->refresh();

        if (! $ended || $violation === 'time_up') {
            return;
        }

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
            Log::warning('Mini boss alert email failed: '.$e->getMessage());
        }
    }

    /** The XP a student has from mini bosses (across every course, or one crew). */
    public static function xp(User $user, ?Crew $crew = null): int
    {
        return (int) CrewQuizAttempt::where('user_id', $user->id)
            ->whereHas('quiz', fn ($q) => $crew ? $q->where('crew_id', $crew->id) : $q)
            ->sum('xp');
    }
}
