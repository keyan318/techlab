<?php

namespace App\Http\Controllers;

use App\Exceptions\QuizGenerationException;
use App\Exceptions\QuizSourceException;
use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Services\CourseMaterialSourceService;
use App\Services\Quiz\QuizContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * The two-panel quiz builder: the faculty's own material on the left, a form editor on the
 * right. They can write every question by hand, or have Astro draft them from a material
 * they pick and then edit the result. Nothing reaches students until they press Publish.
 */
class CrewQuizBuilderController extends Controller
{
    private function ownCrew(Crew $crew): Crew
    {
        abort_unless(Auth::check() && (Auth::user()->role ?? 'student') === 'faculty' && $crew->teacher_id === Auth::id(), 403);

        return $crew;
    }

    /** A blank quiz, or with ?final=1 the course's final exam (straight to the existing one if it has one). */
    public function create(Request $request, Crew $crew): View|RedirectResponse
    {
        $this->ownCrew($crew);
        $final = $request->boolean('final');

        if ($final && ($existing = $crew->finalExam()->first())) {
            return redirect(route('faculty.quizzes.edit', $existing));
        }

        return view('faculty.quiz-builder', $this->shared($crew) + [
            'quiz' => null,
            'isFinal' => $final,
            'locked' => false,
            'saveUrl' => route('faculty.quizzes.store', $crew),
            'saveMethod' => 'POST',
        ]);
    }

    /** An existing one, draft or published. */
    public function edit(CrewQuiz $quiz): View
    {
        $crew = $this->ownCrew($quiz->crew);
        $quiz->load('questions');

        return view('faculty.quiz-builder', $this->shared($crew) + [
            'quiz' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'minutes' => $quiz->minutes,
                'due_at' => $quiz->due_at?->format('Y-m-d\TH:i'),
                'published' => $quiz->isPublished(),
                'pass_percent' => $quiz->is_final ? $quiz->passPercent() : null,
                'questions' => $quiz->questions->map(fn ($q) => [
                    'prompt' => $q->prompt,
                    'type' => $q->type,
                    'options' => $q->options,
                    'correct' => $q->correct_index,
                    'correct_many' => $q->correct_indexes ?? [],
                    'required' => $q->required,
                ])->all(),
            ],
            'isFinal' => $quiz->is_final,
            // A final someone has sat can't be rewritten (CrewQuizController::update refuses it).
            'locked' => $quiz->is_final && $quiz->attempts()->exists(),
            'saveUrl' => route('faculty.quizzes.update', $quiz),
            'saveMethod' => 'PUT',
        ]);
    }

    /**
     * "Ask Astro to draft it": turn the chosen materials into editable questions.
     *
     * The result is never published — it lands in the editor for the faculty member to
     * check and change, because a model can get a fact wrong and a student cannot.
     */
    public function draft(Request $request, Crew $crew): JsonResponse
    {
        $this->ownCrew($crew);

        $data = $request->validate([
            'material_ids' => ['required', 'array', 'min:1', 'max:10'],
            'material_ids.*' => ['integer'],
            'count' => ['nullable', 'integer', 'min:1', 'max:20'],
            'prompt' => ['nullable', 'string', 'max:500'],
        ], [
            'material_ids.required' => 'Pick at least one material for Astro to work from.',
        ]);

        $source = app(CourseMaterialSourceService::class)->textForMaterials($data['material_ids'], $crew->id);

        if ($source === '') {
            return response()->json([
                'message' => "Astro can't read the material you picked. It needs a PDF, Word, PowerPoint or text file with selectable text.",
            ], 422);
        }

        // A free-text steer from the faculty member ("focus on symmetric vs asymmetric").
        if (! empty($data['prompt'])) {
            $source = "The teacher asked you to focus on: {$data['prompt']}\n\n".$source;
        }

        try {
            $quiz = app(QuizContentService::class)->generate($source, (int) ($data['count'] ?? 5));
        } catch (QuizSourceException|QuizGenerationException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        } catch (\Throwable $e) {
            Log::error('Quiz draft failed: '.$e->getMessage());

            return response()->json(['message' => 'Astro could not draft this quiz right now. Try again in a moment.'], 503);
        }

        if (! empty($quiz['insufficient_content'])) {
            return response()->json(['message' => $quiz['reason']], 422);
        }

        return response()->json([
            'title' => $quiz['title'] ?? '',
            'questions' => self::toBuilderQuestions($quiz['questions'] ?? []),
        ]);
    }

    /**
     * The generator speaks in A/B/C/D choices; the builder speaks in options and an index.
     * Everything arrives as multiple choice — the faculty member switches the type if they
     * want true/false, checkboxes or a drop-down.
     */
    private static function toBuilderQuestions(array $questions): array
    {
        $out = [];

        foreach ($questions as $q) {
            $options = array_values(array_map(fn ($c) => (string) ($c['text'] ?? ''), $q['choices'] ?? []));
            $correct = array_search((string) ($q['correct_key'] ?? ''), array_column($q['choices'] ?? [], 'key'), true);

            if (count($options) < 2 || $correct === false) {
                continue;
            }

            $out[] = [
                'prompt' => (string) ($q['question'] ?? ''),
                'type' => 'multiple_choice',
                'options' => $options,
                'correct' => (int) $correct,
                'correct_many' => [],
                'required' => true,
            ];
        }

        return $out;
    }

    /** The left panel: everything this course has, and which files Astro can actually read. */
    private function shared(Crew $crew): array
    {
        return [
            'crew' => $crew,
            'modules' => $crew->modules()->with('materials')->get()->map(fn ($m) => [
                'title' => $m->title,
                'materials' => $m->materials->map(fn ($f) => [
                    'id' => $f->id,
                    'name' => $f->name,
                    'ext' => $f->kind,
                    'url' => $f->openUrl(),
                    'is_link' => $f->isLink(),
                    'readable' => $f->isReadable() && $f->index_error === null,
                ])->all(),
            ])->all(),
        ];
    }
}
