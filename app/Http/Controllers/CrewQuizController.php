<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
use App\Models\CrewQuizQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Faculty-authored quizzes. Faculty create/delete them from their dashboard; students in the
 * crew take them from the crew page. Answers are graded here, never in the browser, and the
 * correct answers are only sent back after the student has submitted.
 */
class CrewQuizController extends Controller
{
    private function ownCrew(Crew $crew): Crew
    {
        abort_unless(Auth::check() && (Auth::user()->role ?? 'student') === 'faculty' && $crew->teacher_id === Auth::id(), 403);

        return $crew;
    }

    private function memberQuiz(CrewQuiz $quiz): CrewQuiz
    {
        abort_unless(Auth::check() && Auth::user()->belongsToCrew($quiz->crew_id), 403);
        // A draft does not exist yet as far as students are concerned.
        abort_unless($quiz->isPublished() || Auth::id() === $quiz->crew->teacher_id, 404);
        // The final exam is only ever taken through its own full-screen page (FinalExamController).
        abort_if($quiz->is_final, 404);

        return $quiz;
    }

    /**
     * The deadline is a lock, not a reminder: once it passes, a student who never started
     * can no longer take it. Someone who already submitted keeps their result.
     */
    private function openQuiz(CrewQuiz $quiz): CrewQuiz
    {
        $this->memberQuiz($quiz);

        if ($quiz->isPastDue() && ! $quiz->attempts()->where('user_id', Auth::id())->exists()) {
            abort(403, 'This quiz closed on '.$quiz->due_at->format('j M Y, g:ia').'.');
        }

        return $quiz;
    }

    public function store(Request $request, Crew $crew): JsonResponse|RedirectResponse
    {
        $crew = $this->ownCrew($crew);
        $data = $this->validateQuiz($request);
        $final = (bool) ($data['is_final'] ?? false);

        // One final exam per course: it is the single test that decides the certificate.
        if ($final && $crew->finalExam()->exists()) {
            throw ValidationException::withMessages(['is_final' => 'This course already has a final exam. Edit that one instead.']);
        }

        $quiz = DB::transaction(function () use ($crew, $data, $final) {
            $quiz = $crew->quizzes()->create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'minutes' => $data['minutes'] ?? null,
                'due_at' => $data['due_at'] ?? null,
                // Posting from the simple form publishes straight away; the builder saves drafts.
                'published_at' => ($data['draft'] ?? false) ? null : now(),
                'is_final' => $final,
                'pass_percent' => $final ? ($data['pass_percent'] ?? CrewQuiz::DEFAULT_PASS_PERCENT) : null,
            ]);
            $this->writeQuestions($quiz, $data['questions']);

            return $quiz;
        });

        return $request->expectsJson()
            ? response()->json(['id' => $quiz->id, 'published' => $quiz->isPublished(), 'saved_at' => now()->toISOString()])
            : redirect(route('faculty.course', $crew).($final ? '#final-exam' : '#quizzes'));
    }

    /** Builder autosave / publish: replace a quiz and its questions in one go. */
    public function update(Request $request, CrewQuiz $quiz): JsonResponse|RedirectResponse
    {
        $this->ownCrew($quiz->crew);
        $data = $this->validateQuiz($request);

        // Rewriting a final someone has already sat would change the exam under their result
        // (and under anyone taking it right now). Allowing them a retake is the way to redo it.
        if ($quiz->is_final && $quiz->attempts()->exists()) {
            throw ValidationException::withMessages(['questions' => 'A student has already taken this final exam, so it can no longer be changed.']);
        }

        DB::transaction(function () use ($quiz, $data) {
            $quiz->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'minutes' => $data['minutes'] ?? null,
                'due_at' => $data['due_at'] ?? null,
                // Publishing is one-way from the builder: a live quiz is not pulled back
                // to a draft under students who may already be taking it.
                'published_at' => ($data['draft'] ?? false) ? $quiz->published_at : ($quiz->published_at ?? now()),
                // A quiz stays what it was created as; only a final has a pass mark.
                'pass_percent' => $quiz->is_final ? ($data['pass_percent'] ?? $quiz->passPercent()) : null,
            ]);
            $this->writeQuestions($quiz, $data['questions']);
        });

        return $request->expectsJson()
            ? response()->json(['id' => $quiz->id, 'published' => $quiz->fresh()->isPublished(), 'saved_at' => now()->toISOString()])
            : redirect(route('faculty.course', $quiz->crew_id).($quiz->is_final ? '#final-exam' : '#quizzes'));
    }

    public function destroy(CrewQuiz $quiz): RedirectResponse
    {
        $this->ownCrew($quiz->crew);
        $crewId = $quiz->crew_id;
        $final = $quiz->is_final;
        $quiz->delete();

        return redirect(route('faculty.course', $crewId).($final ? '#final-exam' : '#quizzes'));
    }

    /** Student: the questions (no answers), plus their result if they already took it. */
    public function show(CrewQuiz $quiz): JsonResponse
    {
        $this->openQuiz($quiz)->load('questions');
        $attempt = $quiz->attempts()->where('user_id', Auth::id())->first();

        return response()->json([
            'id' => $quiz->id,
            'title' => $quiz->title,
            'description' => $quiz->description,
            'minutes' => $quiz->minutes,
            'due_at' => $quiz->due_at?->toISOString(),
            'questions' => $quiz->questions->map(fn ($q) => [
                'id' => $q->id,
                'prompt' => $q->prompt,
                'type' => $q->type,
                'options' => $q->options,
                'required' => $q->required,
            ])->values(),
            'result' => $attempt ? $this->result($quiz, $attempt) : null,
        ]);
    }

    public function submit(Request $request, CrewQuiz $quiz): JsonResponse
    {
        $this->openQuiz($quiz)->load('questions');
        $data = $request->validate(['answers' => ['nullable', 'array']]);

        // The unique (quiz, user) index makes a double submit harmless: the first attempt stands.
        $attempt = CrewQuizAttempt::firstOrCreate(
            ['crew_quiz_id' => $quiz->id, 'user_id' => Auth::id()],
            $quiz->grade($data['answers'] ?? []),
        );

        return response()->json(['result' => $this->result($quiz, $attempt)]);
    }

    /**
     * The rules a quiz has to follow, whichever screen it came from.
     *
     * @return array<string, mixed>
     */
    private function validateQuiz(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'due_at' => ['nullable', 'date'],
            'draft' => ['nullable', 'boolean'],
            'is_final' => ['nullable', 'boolean'],
            'pass_percent' => ['nullable', 'integer', 'min:1', 'max:100'],
            'questions' => ['required', 'array', 'min:1', 'max:100'],
            'questions.*.prompt' => ['required', 'string', 'max:1000'],
            'questions.*.type' => ['nullable', 'string', 'in:'.implode(',', CrewQuizQuestion::TYPES)],
            'questions.*.options' => ['required', 'array', 'min:2', 'max:10'],
            'questions.*.options.*' => ['required', 'string', 'max:500'],
            'questions.*.required' => ['nullable', 'boolean'],
            'questions.*.correct' => ['nullable', 'integer', 'min:0'],
            'questions.*.correct_many' => ['nullable', 'array'],
            'questions.*.correct_many.*' => ['integer', 'min:0'],
        ], [
            'questions.required' => 'Add at least one question.',
            'questions.*.options.min' => 'Each question needs at least two answer options.',
            'questions.*.options.*.required' => 'Fill in every answer option (or remove the empty ones).',
        ]);

        foreach (array_values($data['questions']) as $q) {
            $count = count($q['options']);
            $type = $q['type'] ?? 'multiple_choice';

            if ($type === 'checkboxes') {
                $picked = array_filter($q['correct_many'] ?? [], fn ($i) => $i < $count);
                if (! $picked) {
                    throw ValidationException::withMessages(['questions' => 'Tick at least one correct answer for every checkbox question.']);
                }

                continue;
            }

            if (! isset($q['correct']) || $q['correct'] >= $count) {
                throw ValidationException::withMessages(['questions' => 'Pick the correct answer for every question.']);
            }
        }

        return $data;
    }

    /** Replaces a quiz's questions with the given ones, in order. */
    private function writeQuestions(CrewQuiz $quiz, array $questions): void
    {
        $quiz->questions()->delete();

        foreach (array_values($questions) as $i => $q) {
            $type = $q['type'] ?? 'multiple_choice';
            $multi = $type === 'checkboxes';

            $quiz->questions()->create([
                'prompt' => $q['prompt'],
                'type' => $type,
                'options' => array_values($q['options']),
                'correct_index' => $multi ? 0 : (int) $q['correct'],
                'correct_indexes' => $multi ? array_values(array_unique(array_map('intval', $q['correct_many']))) : null,
                'required' => (bool) ($q['required'] ?? true),
                'position' => $i,
            ]);
        }
    }

    private function result(CrewQuiz $quiz, CrewQuizAttempt $attempt): array
    {
        return [
            'score' => $attempt->score,
            'total' => $attempt->total,
            'percent' => $attempt->percent(),
            'review' => $quiz->questions->map(fn ($q) => [
                'id' => $q->id,
                'type' => $q->type,
                'correct' => $q->isMultiAnswer() ? ($q->correct_indexes ?? []) : $q->correct_index,
                'chosen' => $attempt->answers[$q->id] ?? null,
            ])->values(),
        ];
    }
}
