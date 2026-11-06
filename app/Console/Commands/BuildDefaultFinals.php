<?php

namespace App\Console\Commands;

use App\Models\Crew;
use App\Services\DefaultFinalExamService;
use Illuminate\Console\Command;

/** Builds the default final exam of every course's self-paced run ahead of time (otherwise it's built on first need). */
class BuildDefaultFinals extends Command
{
    protected $signature = 'techlab:default-finals';

    protected $description = "Build each course's self-paced final exam from its lessons' Knowledge Checks";

    public function handle(): int
    {
        foreach (config('course-catalog') as $planet => $meta) {
            foreach ($meta['courses'] ?? [] as $course => $c) {
                if (! empty($c['coming_soon']) || ! empty($c['hidden'])) {
                    continue;
                }
                $exam = DefaultFinalExamService::ensure(Crew::selfPaced($planet, $course));
                $this->line(sprintf('%-40s %s', $c['title'] ?? $course, $exam ? $exam->questions()->count().' questions' : 'no lesson quizzes found'));
            }
        }

        return self::SUCCESS;
    }
}
