<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\FacultyDashboardService;
use App\Services\MiniBossService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * The faculty member's Exams tab: every module's mini boss, and the course's final boss, for their class.
 * Mini boss questions are edited in the quiz builder, or reset to TechLab's; the final exam's results and
 * retake controls live here too. Their changes reach only their own class; self-paced keeps the default.
 */
class ExamsController extends Controller
{
    private function ownCrew(Crew $crew): Crew
    {
        abort_unless(Auth::check() && (Auth::user()->role ?? 'student') === 'faculty' && $crew->teacher_id === Auth::id(), 403);

        return $crew;
    }

    public function index(Crew $crew): View
    {
        $this->ownCrew($crew);

        $key = MiniBossService::keyFor($crew);
        $modules = MiniBossService::modules($key);
        // Made now if not yet, so the faculty member can rewrite them before any student gets there.
        $quizzes = MiniBossService::ensureAll($crew)->load('questions');
        $students = $crew->roster()->wherePivot('role', 'student')->orderBy('name')->get();
        $attempts = CrewQuizAttempt::whereIn('crew_quiz_id', $quizzes->pluck('id'))->get()->groupBy('crew_quiz_id');

        $bosses = collect($modules)->map(function (string $title, string $module) use ($key, $quizzes, $students, $attempts) {
            $quiz = $quizzes->get($module);
            $mine = ($quiz ? $attempts->get($quiz->id) : null)?->keyBy('user_id') ?? collect();
            $taken = $mine->reject(fn (CrewQuizAttempt $a) => $a->inProgress());

            return [
                'module' => $module,
                'number' => (int) substr($module, 1),
                'title' => $title,
                'quiz' => $quiz,
                'hasDefault' => is_file(MiniBossService::defaultsPath($key, $module)),
                'taken' => $taken->count(),
                'passed' => $taken->where('status', CrewQuizAttempt::PASSED)->count(),
                'average' => $taken->isEmpty() ? null : (int) round($taken->avg(fn ($a) => $a->percent())),
                'rows' => $students->map(fn (User $s) => ['student' => $s, 'attempt' => $mine->get($s->id)])->all(),
            ];
        })->values();

        // Opening this page is how the faculty member sees who was caught, so those alerts clear.
        CrewQuizAttempt::whereIn('crew_quiz_id', $quizzes->pluck('id'))->whereNotNull('violation')->whereNull('faculty_seen_at')
            ->update(['faculty_seen_at' => now()]);

        $final = $crew->finalExam()->withCount('questions')->first();
        $finalResults = $final ? $this->finalResults($crew, $final) : [];

        if ($final) {
            $final->attempts()->whereNotNull('violation')->whereNull('faculty_seen_at')->update(['faculty_seen_at' => now()]);
        }

        return view('faculty.exams', compact('crew', 'bosses', 'final', 'finalResults'));
    }

    /** Put TechLab's default questions, time and pass mark back. Students' results stay. */
    public function reset(CrewQuiz $quiz): RedirectResponse
    {
        $this->ownCrew($quiz->crew);
        abort_unless($quiz->isMiniBoss(), 404);
        abort_if($quiz->attempts()->where('status', CrewQuizAttempt::IN_PROGRESS)->exists(), 422, 'A student is taking this mini boss right now. Try again once they hand it in.');

        MiniBossService::reset($quiz);

        return redirect(route('faculty.exams.index', $quiz->crew_id).'#boss-'.$quiz->module)
            ->with('status', 'Module '.substr($quiz->module, 1).' mini boss is back to TechLab\'s questions.');
    }

    /**
     * One row per student for the final exam: still on the lessons, ready to take it, taking it
     * right now, passed, failed, or caught leaving. Lesson progress for the whole crew comes in
     * one query (the database is remote, so a query per student would be slow).
     *
     * @return array<int, array<string, mixed>>
     */
    private function finalResults(Crew $crew, CrewQuiz $final): array
    {
        $students = $crew->roster()->wherePivot('role', 'student')->orderBy('name')->get();
        $attempts = $final->attempts()->get()->keyBy('user_id');

        $key = CourseProgressService::keyFor($crew->planet, $crew->course_slug);
        $lessons = collect(CourseProgressService::order($key))->map(fn ($i) => $i['module'].'/'.$i['lesson'])->flip();
        $done = LessonProgress::where('course', $key)->whereIn('user_id', $students->pluck('id'))
            ->get(['user_id', 'module', 'lesson'])
            ->filter(fn ($row) => $lessons->has($row->module.'/'.$row->lesson))
            ->countBy('user_id');

        return $students->map(function (User $student) use ($attempts, $done, $lessons) {
            $attempt = $attempts->get($student->id);
            $percent = $lessons->count() ? (int) round(($done[$student->id] ?? 0) / $lessons->count() * 100) : 0;

            return [
                'student' => $student,
                'attempt' => $attempt,
                'lessonsPercent' => $percent,
                'state' => FacultyDashboardService::finalState($attempt, $percent),
            ];
        })->all();
    }
}
