<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
use App\Services\MiniBossService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * A module's mini boss, on the same full-screen exam page as the Final Boss and under the same rules.
 * The page only deters; the server is the judge of the attempt, the clock and the grade
 * (MiniBossService has the self-paced and class rules).
 */
class MiniBossController extends Controller
{
    /** GET /student/course/{slug}/{course}/mini-boss/{module}: the rules, then the exam itself. */
    public function show(string $slug, string $course, string $module): View|RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect(route('login'));
        }
        if (($user->role ?? 'student') === 'faculty') {
            return redirect(route('faculty.dashboard'));
        }

        $meta = config("course-catalog.{$slug}.courses.{$course}");
        abort_if(! $meta || ! empty($meta['coming_soon']), 404);
        $crew = request()->attributes->get('crew') ?? Crew::viewFor($user, $slug, $course);
        abort_if($crew === null, 404);

        $module = strtolower($module);
        $key = MiniBossService::keyFor($crew);
        $modules = MiniBossService::modules($key);
        abort_unless(isset($modules[$module]), 404);

        // First: an attempt left in progress fails here, before anything else is read.
        $attempt = MiniBossService::attempts($user, $crew)->get($module);
        $moduleDone = MiniBossService::moduleDone($user, $key, $module);
        // Made the first time a student reaches it, so modules nobody has finished cost nothing.
        $quiz = ($moduleDone || $attempt) && ($crew->miniBosses()->where('module', $module)->exists() || MiniBossService::hasDefaults($key, $module))
            ? MiniBossService::ensure($crew, $module)->loadCount('questions') : null;
        $state = MiniBossService::state($crew, $module, $quiz, $attempt, $moduleDone);
        $number = array_search($module, array_keys($modules), true) + 1;

        return view('student.course.final-exam', [
            'mini' => [
                'module' => $number,
                'moduleTitle' => $modules[$module],
                'selfPaced' => $crew->isSelfPaced(),
                'xpPerCorrect' => MiniBossService::XP_PER_CORRECT,
                'cooldown' => MiniBossService::COOLDOWN_MINUTES,
                'retryAt' => $attempt ? MiniBossService::retryAt($attempt) : null,
                'last' => $number === count($modules),
            ],
            'courseTitle' => $meta['title'],
            'captain' => $crew->faculty?->name ?? ($crew->isSelfPaced() ? "TechLab's admin" : null),
            'student' => $user,
            'exam' => $quiz,
            'state' => $state,
            'result' => $attempt && $quiz ? $this->result($quiz, $attempt) : null,
            'strikes' => 0,
            'closed' => $state === 'ready' && $quiz?->isPastDue(),
            'urls' => $quiz ? [
                'start' => route('student.mini-boss.start', $quiz),
                'violation' => route('student.mini-boss.violation', $quiz),
                'submit' => route('student.mini-boss.submit', $quiz),
            ] : null,
            'backUrl' => route('student.course.overview', ['slug' => $slug, 'course' => $course]),
            'certificateUrl' => null,
        ]);
    }

    /** POST: the student pressed Start (already full screen). Only now do the questions leave the server. */
    public function start(CrewQuiz $quiz): JsonResponse
    {
        $this->memberBoss($quiz);
        $user = Auth::user();
        $crew = $quiz->crew;

        abort_unless(MiniBossService::moduleDone($user, MiniBossService::keyFor($crew), $quiz->module), 403, 'Finish every lesson of this module first.');
        if ($quiz->isPastDue()) {
            abort(403, 'This mini boss closed on '.$quiz->due_at->format('j M Y, g:ia').'.');
        }

        // Self-paced, failed, and the wait is over: that old attempt makes way for a fresh one.
        $previous = $quiz->attempts()->where('user_id', $user->id)->first();
        $tryNumber = 1;
        if ($previous && $crew->isSelfPaced() && $previous->status === CrewQuizAttempt::FAILED && ! MiniBossService::retryAt($previous)?->isFuture()) {
            $tryNumber = $previous->try_number + 1;
            $previous->delete();
        }

        try {
            $attempt = CrewQuizAttempt::firstOrCreate(
                ['crew_quiz_id' => $quiz->id, 'user_id' => $user->id],
                ['score' => 0, 'total' => $quiz->questions()->count(), 'answers' => [], 'status' => CrewQuizAttempt::IN_PROGRESS, 'started_at' => now(), 'try_number' => $tryNumber],
            );
        } catch (QueryException) {
            // Two starts raced on the unique (quiz, user) index: the other one won.
            $attempt = $quiz->attempts()->where('user_id', $user->id)->firstOrFail();
        }

        // Starting again means the page was left mid-exam (reloaded, reopened): that ends it.
        if (! $attempt->wasRecentlyCreated) {
            if ($attempt->inProgress()) {
                MiniBossService::fail($attempt, 'left_page');
            }

            return response()->json(['message' => 'You already faced this mini boss.', 'result' => $this->result($quiz, $attempt)], 409);
        }

        return response()->json([
            'questions' => $quiz->studentQuestions($attempt->id),
            'seconds_left' => $quiz->minutes ? $quiz->minutes * 60 : null,
        ]);
    }

    /** POST: the page caught the student breaking a rule. Sent with sendBeacon as the page goes away too. */
    public function violation(Request $request, CrewQuiz $quiz): JsonResponse
    {
        $this->memberBoss($quiz);
        $data = $request->validate(['reason' => ['required', 'string', 'in:left_fullscreen,switched_away,left_page,screenshot']]);
        $attempt = $quiz->attempts()->where('user_id', Auth::id())->firstOrFail();

        if ($attempt->inProgress()) {
            MiniBossService::fail($attempt, $data['reason']);
        }

        return response()->json(['result' => $this->result($quiz, $attempt)]);
    }

    /** POST: the answers, graded here. Only the score comes back: a self-paced retry uses the same questions. */
    public function submit(Request $request, CrewQuiz $quiz): JsonResponse
    {
        $this->memberBoss($quiz);
        $data = $request->validate(['answers' => ['nullable', 'array']]);
        $attempt = $quiz->attempts()->where('user_id', Auth::id())->firstOrFail();

        if ($attempt->inProgress()) {
            $quiz->isOverTime($attempt)
                ? MiniBossService::fail($attempt, 'time_up')
                : MiniBossService::finish($quiz, $attempt, $data['answers'] ?? []);
        }

        return response()->json(['result' => $this->result($quiz, $attempt->refresh())]);
    }

    /** A published mini boss in one of this student's crews. */
    private function memberBoss(CrewQuiz $quiz): void
    {
        abort_unless(Auth::check() && $quiz->isMiniBoss() && $quiz->isPublished() && Auth::user()->belongsToCrew($quiz->crew_id), 404);
    }

    private function result(CrewQuiz $quiz, CrewQuizAttempt $attempt): array
    {
        $retryAt = MiniBossService::retryAt($attempt);

        return [
            'status' => $attempt->status,
            'score' => $attempt->score,
            'total' => $attempt->total,
            'percent' => $attempt->percent(),
            'pass_percent' => $quiz->passPercent(),
            'xp' => $attempt->xp,
            'violation' => $attempt->violationText(),
            'caught' => $attempt->cheated(),
            'self_paced' => $quiz->crew->isSelfPaced(),
            'retry_at' => $quiz->crew->isSelfPaced() && $retryAt ? $retryAt->toISOString() : null,
            'redo' => false,
            'banned' => false,
            'strikes' => 0,
            'certificate_url' => null,
        ];
    }
}
