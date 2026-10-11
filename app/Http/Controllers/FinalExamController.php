<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
use App\Services\FinalExamService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * The final exam ("Final Boss"): the faculty-written test a student must pass, once every lesson
 * is done, to earn the course certificate.
 *
 * It is taken full screen. The page reports the moment the student leaves full screen, switches
 * tab or window, or reaches for a screenshot, and this controller fails the attempt and emails
 * the faculty member. The browser can only deter, never fully prevent, so the rules that decide
 * the grade (one attempt, the clock, the answers) are all enforced here, not on the page.
 */
class FinalExamController extends Controller
{
    /** Seconds allowed after the clock hits zero for the page's own auto-submit to arrive. */
    private const LATE_GRACE_SECONDS = 90;

    /** GET /student/course/{slug}/{course}/final-exam — the arena: rules, then the exam itself. */
    public function show(string $slug, string $course): View|RedirectResponse
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

        $crew = request()->attributes->get('crew')
            ?? Crew::where('planet', $slug)->where('course_slug', $course)->first();
        abort_if($crew === null, 404);

        ['exam' => $exam, 'attempt' => $attempt] = FinalExamService::for($user, $crew);
        $lessonsDone = FinalExamService::lessonsDone($user, $crew);

        return view('student.course.final-exam', [
            'courseTitle' => $meta['title'],
            'captain' => $crew->faculty?->name,
            'student' => $user,
            'exam' => $exam,
            'state' => FinalExamService::state($exam, $attempt, $lessonsDone),
            'result' => $attempt ? $this->result($exam, $attempt, $slug, $course) : null,
            'closed' => $exam?->isPastDue() && $attempt === null,
            'backUrl' => route('student.course.overview', ['slug' => $slug, 'course' => $course]),
            'certificateUrl' => route('student.course.certificate', ['slug' => $slug, 'course' => $course]),
        ]);
    }

    /**
     * POST: the student pressed Start (already full screen). Opens their one attempt and only now
     * hands over the questions, so nobody can read them in advance.
     */
    public function start(CrewQuiz $quiz): JsonResponse
    {
        $this->openExam($quiz);

        if ($quiz->isPastDue()) {
            abort(403, 'This final exam closed on '.$quiz->due_at->format('j M Y, g:ia').'.');
        }

        try {
            $attempt = CrewQuizAttempt::firstOrCreate(
                ['crew_quiz_id' => $quiz->id, 'user_id' => Auth::id()],
                ['score' => 0, 'total' => $quiz->questions()->count(), 'answers' => [], 'status' => CrewQuizAttempt::IN_PROGRESS, 'started_at' => now()],
            );
        } catch (QueryException) {
            // Two starts raced on the unique (quiz, user) index: the other one won.
            $attempt = $quiz->attempts()->where('user_id', Auth::id())->firstOrFail();
        }

        // Starting again means the page was left mid-exam (reloaded, reopened): that ends it.
        if (! $attempt->wasRecentlyCreated) {
            if ($attempt->inProgress()) {
                FinalExamService::fail($attempt, 'left_page');
            }

            return response()->json(['message' => 'You have already used your attempt at this final exam.', 'result' => $this->resultFor($quiz, $attempt)], 409);
        }

        $quiz->load('questions');

        return response()->json([
            'questions' => $quiz->questions->map(fn ($q) => [
                'id' => $q->id,
                'prompt' => $q->prompt,
                'type' => $q->type,
                'options' => $q->options,
            ])->values(),
            // The page counts down from this; the server holds it to the same moment on submit.
            'seconds_left' => $quiz->minutes ? $quiz->minutes * 60 : null,
        ]);
    }

    /**
     * POST: the page caught the student breaking a rule. Sent with fetch(keepalive) or
     * sendBeacon as the page goes away, so it also accepts a plain form body.
     */
    public function violation(Request $request, CrewQuiz $quiz): JsonResponse
    {
        $this->memberExam($quiz);
        $data = $request->validate(['reason' => ['required', 'string', 'in:left_fullscreen,switched_away,left_page,screenshot']]);

        $attempt = $quiz->attempts()->where('user_id', Auth::id())->firstOrFail();

        if ($attempt->inProgress()) {
            FinalExamService::fail($attempt, $data['reason']);
        }

        return response()->json(['result' => $this->resultFor($quiz, $attempt)]);
    }

    /** POST: the student's answers. Graded here against the pass mark; the right answers are never sent back. */
    public function submit(Request $request, CrewQuiz $quiz): JsonResponse
    {
        $this->memberExam($quiz)->load('questions');
        $data = $request->validate(['answers' => ['nullable', 'array']]);

        $attempt = $quiz->attempts()->where('user_id', Auth::id())->firstOrFail();

        // Already over (caught leaving, or a double submit): the first outcome stands.
        if (! $attempt->inProgress()) {
            return response()->json(['result' => $this->resultFor($quiz, $attempt)]);
        }

        if ($quiz->minutes && now()->greaterThan($attempt->started_at->copy()->addMinutes($quiz->minutes)->addSeconds(self::LATE_GRACE_SECONDS))) {
            FinalExamService::fail($attempt, 'time_up');

            return response()->json(['result' => $this->resultFor($quiz, $attempt)]);
        }

        $graded = $quiz->grade($data['answers'] ?? []);
        $passed = $graded['total'] > 0 && $quiz->passPercent() <= $graded['score'] / $graded['total'] * 100;

        // Conditional, like fail(): a submit racing a "caught leaving" report cannot overwrite it.
        CrewQuizAttempt::whereKey($attempt->id)->where('status', CrewQuizAttempt::IN_PROGRESS)->update([
            'score' => $graded['score'],
            'total' => $graded['total'],
            'answers' => json_encode($graded['answers']),
            'status' => $passed ? CrewQuizAttempt::PASSED : CrewQuizAttempt::FAILED,
            'finished_at' => now(),
        ]);

        return response()->json(['result' => $this->resultFor($quiz, $attempt->refresh())]);
    }

    /** POST (faculty): let a student sit the final again, e.g. after an honest mistake. Their old result is removed. */
    public function retake(CrewQuizAttempt $attempt): RedirectResponse
    {
        $quiz = $attempt->quiz;
        abort_unless($quiz->is_final && Auth::check() && $quiz->crew->teacher_id === Auth::id(), 403);

        $name = $attempt->user?->name ?? 'The student';
        $attempt->delete();

        return redirect(route('faculty.course', $quiz->crew_id).'#final-exam')
            ->with('status', "{$name} can take the final exam again.");
    }

    /** A published final exam in one of this student's crews. */
    private function memberExam(CrewQuiz $quiz): CrewQuiz
    {
        abort_unless(Auth::check() && $quiz->is_final && $quiz->isPublished() && Auth::user()->belongsToCrew($quiz->crew_id), 404);

        return $quiz;
    }

    /** …that this student has also earned the right to sit: every lesson done. */
    private function openExam(CrewQuiz $quiz): CrewQuiz
    {
        $this->memberExam($quiz);
        abort_unless(FinalExamService::lessonsDone(Auth::user(), $quiz->crew), 403, 'Finish every lesson first. The Final Boss only opens after that.');

        return $quiz;
    }

    private function resultFor(CrewQuiz $quiz, CrewQuizAttempt $attempt): array
    {
        $crew = $quiz->crew;

        return $this->result($quiz, $attempt, $crew->planet, $crew->course_slug);
    }

    private function result(CrewQuiz $quiz, CrewQuizAttempt $attempt, string $slug, string $course): array
    {
        return [
            'status' => $attempt->status,
            'score' => $attempt->score,
            'total' => $attempt->total,
            'percent' => $attempt->percent(),
            'pass_percent' => $quiz->passPercent(),
            'violation' => $attempt->violationText(),
            'certificate_url' => $attempt->passed() ? route('student.course.certificate', ['slug' => $slug, 'course' => $course]) : null,
        ];
    }
}
