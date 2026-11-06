<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\LessonQuiz;
use App\Services\CourseProgressService;
use App\Services\CourseStructure;
use App\Services\LessonContentService;
use App\Services\LessonQuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Faculty editing of the lesson Knowledge Checks. Every lesson starts with the default quiz written in its
 * Blade file; the course's faculty member can rewrite it (questions, choices, the right answer, the
 * explanation) for their course, or reset it back to the default.
 */
class LessonQuizEditorController extends Controller
{
    /** Every lesson of the course that has a quiz, and whether it is the default or the faculty's own. */
    public function index(Crew $crew): View
    {
        $course = $this->course($crew);
        $edited = LessonQuiz::where('course', $course)->where('crew_id', $crew->id)->get(['module', 'lesson', 'questions', 'updated_at'])
            ->keyBy(fn (LessonQuiz $q) => $q->module.'/'.$q->lesson);

        $modules = [];
        foreach (CourseStructure::modules($course) as $moduleKey => $module) {
            $lessons = [];
            foreach ($module['lessons'] ?? [] as $lessonKey => $lesson) {
                [$m, $l] = CourseProgressService::normalize($moduleKey, $lessonKey);
                $default = LessonQuizService::defaultCount($m, $l, $course);
                if ($default === 0) {
                    continue;   // no Knowledge Check in this lesson
                }
                $row = $edited->get($m.'/'.$l);
                $lessons[] = [
                    'module' => $m, 'lesson' => $l, 'title' => $lesson['title'] ?? $l,
                    'count' => $row ? count($row->questions) : $default,
                    'edited' => $row !== null, 'updated_at' => $row?->updated_at,
                ];
            }
            if ($lessons) {
                $modules[] = ['title' => $module['title'] ?? $moduleKey, 'lessons' => $lessons];
            }
        }

        return view('faculty.lesson-quizzes', compact('crew', 'modules'));
    }

    /** The editor: the course's questions for this lesson (edited or default), plus the default to reset to. */
    public function edit(Crew $crew, string $module, string $lesson): View
    {
        $course = $this->course($crew);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);

        $default = LessonQuizService::parse((string) LessonQuizService::defaultSource($module, $lesson, $course));
        abort_if($default === [], 404, 'This lesson has no quiz.');
        $row = LessonQuiz::where(compact('course', 'module', 'lesson') + ['crew_id' => $crew->id])->first();

        $title = $this->lessonTitle($course, $module, $lesson);

        return view('faculty.lesson-quiz-editor', [
            'crew' => $crew, 'module' => $module, 'lesson' => $lesson, 'title' => $title,
            'questions' => $row?->questions ?? $default, 'default' => $default, 'edited' => $row !== null,
        ]);
    }

    /** JSON for the in-lesson quiz editor: the course's questions (edited or default) and the default to reset to. */
    public function data(Crew $crew, string $module, string $lesson): JsonResponse
    {
        $course = $this->course($crew);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);
        abort_unless($this->hasQuiz($course, $module, $lesson), 404, 'This lesson has no quiz.');

        $default = LessonQuizService::parse((string) LessonQuizService::defaultSource($module, $lesson, $course));
        $row = LessonQuiz::where(compact('course', 'module', 'lesson') + ['crew_id' => $crew->id])->first();

        return response()->json(['questions' => $row?->questions ?? $default, 'default' => $default, 'edited' => $row !== null]);
    }

    /** PUT {questions: [...]}: save the faculty's version of the quiz (replaces the default for the course). */
    public function update(Request $request, Crew $crew, string $module, string $lesson): JsonResponse
    {
        $course = $this->course($crew);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);
        abort_unless($this->hasQuiz($course, $module, $lesson), 404, 'This lesson has no quiz.');

        $data = $request->validate([
            'questions' => ['required', 'array', 'min:1', 'max:50'],
            'questions.*.prompt' => ['required', 'string', 'max:1000'],
            'questions.*.options' => ['required', 'array', 'min:2', 'max:4'],
            'questions.*.options.*' => ['required', 'string', 'max:500'],
            'questions.*.correct' => ['required', 'string', 'in:A,B,C,D'],
            'questions.*.explanation' => ['nullable', 'string', 'max:1000'],
        ], [
            'questions.*.prompt.required' => 'Every question needs its question text.',
            'questions.*.options.*.required' => 'Fill in every choice (or remove the empty one).',
        ]);

        $questions = [];
        foreach ($data['questions'] as $i => $q) {
            $options = array_values(array_map('trim', $q['options']));
            if (ord($q['correct']) - 65 >= count($options)) {
                return response()->json(['message' => 'Question '.($i + 1).': pick which choice is the right answer.'], 422);
            }
            $questions[] = ['prompt' => trim($q['prompt']), 'options' => $options, 'correct' => $q['correct'], 'explanation' => trim($q['explanation'] ?? '')];
        }

        LessonQuiz::updateOrCreate(
            compact('course', 'module', 'lesson') + ['crew_id' => $crew->id],
            ['questions' => $questions, 'updated_by' => Auth::id()],
        );

        return response()->json(['ok' => true, 'count' => count($questions)]);
    }

    /** DELETE: throw away the faculty's version, so the lesson uses its default quiz again. */
    public function destroy(Request $request, Crew $crew, string $module, string $lesson): RedirectResponse|JsonResponse
    {
        $course = $this->course($crew);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);

        LessonQuiz::where(compact('course', 'module', 'lesson') + ['crew_id' => $crew->id])->get()->each->delete();
        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('faculty.lesson-quizzes.edit', ['crew' => $crew, 'module' => $module, 'lesson' => $lesson])
            ->with('status', 'Quiz reset to the default questions.');
    }

    /**
     * Whether a lesson can have a Knowledge Check: its Blade file has one, or its page has a "Quiz" stage (a lesson
     * the faculty added, or one whose edited page has a quiz section).
     */
    private function hasQuiz(string $course, string $module, string $lesson): bool
    {
        return LessonQuizService::defaultCount($module, $lesson, $course) > 0
            || (bool) preg_match('/<h2 class="section-heading"[^>]*>[^<]*quiz/i', (string) LessonContentService::raw($course, $module, $lesson));
    }

    /** Only this course's own faculty member; returns the course's lesson key. */
    private function course(Crew $crew): string
    {
        abort_unless(Auth::check() && (Auth::user()->role ?? 'student') === 'faculty' && $crew->teacher_id === Auth::id(), 403);
        $course = CourseProgressService::keyFor($crew->planet, $crew->course_slug);
        abort_unless(CourseProgressService::exists($course), 404);

        return $course;
    }

    /** @return array{0: string, 1: string} the normalized module and lesson, or a 404 */
    private function lesson(string $course, string $module, string $lesson): array
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        abort_if(CourseProgressService::indexOf($module, $lesson, $course) === null, 404);

        return [$module, $lesson];
    }

    private function lessonTitle(string $course, string $module, string $lesson): string
    {
        foreach (CourseStructure::modules($course) as $moduleKey => $m) {
            foreach ($m['lessons'] ?? [] as $lessonKey => $l) {
                if (CourseProgressService::normalize($moduleKey, $lessonKey) === [$module, $lesson]) {
                    return $l['title'] ?? $lesson;
                }
            }
        }

        return $lesson;
    }
}
