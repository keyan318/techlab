<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
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

        return $quiz;
    }

    public function store(Request $request, Crew $crew): RedirectResponse
    {
        $crew = $this->ownCrew($crew);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'questions' => ['required', 'array', 'min:1', 'max:100'],
            'questions.*.prompt' => ['required', 'string', 'max:1000'],
            'questions.*.options' => ['required', 'array', 'min:2', 'max:6'],
            'questions.*.options.*' => ['required', 'string', 'max:500'],
            'questions.*.correct' => ['required', 'integer', 'min:0'],
        ], [
            'questions.required' => 'Add at least one question.',
            'questions.*.options.min' => 'Each question needs at least two answer options.',
            'questions.*.options.*.required' => 'Fill in every answer option (or remove the empty ones).',
        ]);

        foreach (array_values($data['questions']) as $q) {
            if ($q['correct'] >= count($q['options'])) {
                throw ValidationException::withMessages(['questions' => 'Pick the correct answer for every question.']);
            }
        }

        DB::transaction(function () use ($crew, $data) {
            $quiz = $crew->quizzes()->create(['title' => $data['title'], 'minutes' => $data['minutes'] ?? null]);
            foreach (array_values($data['questions']) as $i => $q) {
                $quiz->questions()->create([
                    'prompt' => $q['prompt'],
                    'options' => array_values($q['options']),
                    'correct_index' => (int) $q['correct'],
                    'position' => $i,
                ]);
            }
        });

        return redirect(route('faculty.course', $crew).'#quizzes');
    }

    public function destroy(CrewQuiz $quiz): RedirectResponse
    {
        $this->ownCrew($quiz->crew);
        $crewId = $quiz->crew_id;
        $quiz->delete();

        return redirect(route('faculty.course', $crewId).'#quizzes');
    }

    /** Student: the questions (no answers), plus their result if they already took it. */
    public function show(CrewQuiz $quiz): JsonResponse
    {
        $this->memberQuiz($quiz)->load('questions');
        $attempt = $quiz->attempts()->where('user_id', Auth::id())->first();

        return response()->json([
            'id' => $quiz->id,
            'title' => $quiz->title,
            'minutes' => $quiz->minutes,
            'questions' => $quiz->questions->map(fn ($q) => ['id' => $q->id, 'prompt' => $q->prompt, 'options' => $q->options])->values(),
            'result' => $attempt ? $this->result($quiz, $attempt) : null,
        ]);
    }

    public function submit(Request $request, CrewQuiz $quiz): JsonResponse
    {
        $this->memberQuiz($quiz)->load('questions');
        $data = $request->validate(['answers' => ['nullable', 'array']]);

        $answers = [];
        $score = 0;
        foreach ($quiz->questions as $q) {
            $chosen = $data['answers'][$q->id] ?? null;
            $chosen = is_numeric($chosen) ? (int) $chosen : null;
            $answers[$q->id] = $chosen;
            if ($chosen === $q->correct_index) {
                $score++;
            }
        }

        // The unique (quiz, user) index makes a double submit harmless: the first attempt stands.
        $attempt = CrewQuizAttempt::firstOrCreate(
            ['crew_quiz_id' => $quiz->id, 'user_id' => Auth::id()],
            ['score' => $score, 'total' => $quiz->questions->count(), 'answers' => $answers],
        );

        return response()->json(['result' => $this->result($quiz, $attempt)]);
    }

    private function result(CrewQuiz $quiz, CrewQuizAttempt $attempt): array
    {
        return [
            'score' => $attempt->score,
            'total' => $attempt->total,
            'percent' => $attempt->percent(),
            'review' => $quiz->questions->map(fn ($q) => [
                'id' => $q->id,
                'correct' => $q->correct_index,
                'chosen' => $attempt->answers[$q->id] ?? null,
            ])->values(),
        ];
    }
}
