<?php

namespace App\Services;

use App\Models\Crew;
use App\Models\CrewQuiz;
use Illuminate\Support\Facades\DB;

/**
 * The self-paced run's final exam. Nobody writes it (a self-paced course has no faculty member): it is
 * built once from the course's own default Knowledge Checks, up to two questions from every lesson, and
 * published straight away with the usual Final Boss rules. The admin can still edit it afterwards.
 *
 * Built lazily (rendering every lesson file to read its quiz takes a moment): the first time a self-paced
 * student has finished every lesson, or by `php artisan techlab:default-finals`.
 */
class DefaultFinalExamService
{
    public const PER_LESSON = 2;

    public const MAX_QUESTIONS = 40;

    /** The crew's final exam, built first if this is a self-paced crew without one. Null for a class crew without one. */
    public static function ensure(Crew $crew): ?CrewQuiz
    {
        if ($existing = $crew->finalExam()->first()) {
            return $existing;
        }
        if (! $crew->isSelfPaced()) {
            return null;
        }

        $key = CourseProgressService::keyFor($crew->planet, $crew->course_slug);
        $questions = self::questions($key);
        if ($questions === []) {
            return null;
        }

        return DB::transaction(function () use ($crew, $questions) {
            // Someone else built it in the meantime: keep theirs.
            if ($existing = $crew->finalExam()->lockForUpdate()->first()) {
                return $existing;
            }

            $title = config("course-catalog.{$crew->planet}.courses.{$crew->course_slug}.title", $crew->name);
            $exam = $crew->quizzes()->create([
                'title' => "{$title} Final Exam",
                'description' => 'Questions from every lesson of the course. Pass it to earn your TechLab certificate.',
                'minutes' => max(15, (int) ceil(count($questions) * 1.5)),
                'is_final' => true,
                'pass_percent' => CrewQuiz::DEFAULT_PASS_PERCENT,
                'published_at' => now(),
            ]);

            foreach ($questions as $i => $q) {
                $exam->questions()->create([
                    'prompt' => $q['prompt'],
                    'source' => $q['source'],
                    'options' => $q['options'],
                    'type' => 'multiple_choice',
                    'correct_index' => ord($q['correct']) - 65,
                    'required' => true,
                    'position' => $i + 1,
                ]);
            }

            return $exam;
        });
    }

    /**
     * Up to PER_LESSON questions from each lesson's default quiz, in course order, then spread evenly down to
     * MAX_QUESTIONS so a long course still gets one from every part.
     *
     * @return list<array{prompt: string, options: list<string>, correct: string, source: string}>
     */
    public static function questions(string $key): array
    {
        $all = [];
        foreach (CourseProgressService::order($key) as $item) {
            try {
                $html = LessonQuizService::defaultSource($item['module'], $item['lesson'], $key);
            } catch (\Throwable $e) {
                report($e);   // one broken lesson file mustn't stop the whole exam being built

                continue;
            }
            $quiz = array_filter(LessonQuizService::parse((string) $html), fn (array $q) => $q['prompt'] !== '' && count($q['options']) >= 2);

            foreach (array_slice(array_values($quiz), 0, self::PER_LESSON) as $q) {
                $all[] = ['prompt' => $q['prompt'], 'options' => $q['options'], 'correct' => $q['correct'], 'source' => CourseStructure::lessonTitle($key, $item['module'], $item['lesson']) ?? strtoupper($item['module'])];
            }
        }

        if (count($all) <= self::MAX_QUESTIONS) {
            return $all;
        }

        $step = count($all) / self::MAX_QUESTIONS;

        return array_map(fn (int $i) => $all[(int) floor($i * $step)], range(0, self::MAX_QUESTIONS - 1));
    }
}
