<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Services\CourseSummaryPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * The quiz builder: a Google Forms-style editor, with the course summary (a PDF) and the
 * faculty's own material open beside it to write from. Nothing reaches students until Publish.
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
                'pass_percent' => $quiz->isProctored() ? $quiz->passPercent() : null,
                'questions' => $quiz->questions->map(fn ($q) => [
                    'prompt' => $q->prompt,
                    'source' => $q->source,
                    'type' => $q->type,
                    'options' => $q->options,
                    'correct' => $q->correct_index,
                    'correct_many' => $q->correct_indexes ?? [],
                    'required' => $q->required,
                ])->all(),
            ],
            'isFinal' => $quiz->is_final,
            'miniBoss' => $quiz->isMiniBoss() ? (int) substr($quiz->module, 1) : null,
            // A final someone has sat can't be rewritten (CrewQuizController::update refuses it).
            'locked' => $quiz->is_final && $quiz->attempts()->exists(),
            'saveUrl' => route('faculty.quizzes.update', $quiz),
            'saveMethod' => 'PUT',
        ]);
    }

    /** The course summary PDF the builder shows beside the form (inline, so it opens in the browser's viewer). */
    public function summaryPdf(Crew $crew, CourseSummaryPdfService $pdfs): BinaryFileResponse
    {
        $this->ownCrew($crew);
        $path = $pdfs->path($crew);
        abort_unless($path, 404);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.str_replace('"', '', $pdfs->title($crew)).' - Course summary.pdf"',
        ]);
    }

    /** The reference panel: the course summary PDF, and the faculty's own uploads. */
    private function shared(Crew $crew): array
    {
        return [
            'crew' => $crew,
            'summaryUrl' => app(CourseSummaryPdfService::class)->has($crew) ? route('faculty.course.summary-pdf', $crew) : null,
            'summaryName' => app(CourseSummaryPdfService::class)->title($crew).' - Course summary.pdf',
            'modules' => $crew->modules()->with('materials')->get()->map(fn ($m) => [
                'title' => $m->title,
                'materials' => $m->materials->map(fn ($f) => [
                    'id' => $f->id,
                    'name' => $f->name,
                    'ext' => $f->kind,
                    'url' => $f->openUrl(),
                    // Inline preview for the panel's viewer (links just open in a new tab).
                    'preview' => $f->isLink() ? null : $f->previewUrl(),
                    'is_link' => $f->isLink(),
                ])->all(),
            ])->all(),
        ];
    }
}
