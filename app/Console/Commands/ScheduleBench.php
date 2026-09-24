<?php

namespace App\Console\Commands;

use App\Services\Schedule\ScheduleExtractor;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;

class ScheduleBench extends Command
{
    protected $signature = 'schedule:bench {file : path to a timetable photo / PDF / CSV} {--n=5 : how many runs}';

    protected $description = 'Time the faculty-schedule extraction (vision → parse → AI) and print p50/p95 per stage';

    public function handle(ScheduleExtractor $extractor): int
    {
        $path = (string) $this->argument('file');
        if (! is_file($path)) {
            $this->error("No such file: {$path}");

            return self::FAILURE;
        }

        $runs = [];
        for ($i = 1, $n = max(1, (int) $this->option('n')); $i <= $n; $i++) {
            $file = new UploadedFile($path, basename($path), mime_content_type($path) ?: null, null, true);
            try {
                $rows = $extractor->extract($file, null, useCache: false);   // cache off: every run must do the real work
                $runs[] = $extractor->timings + ['rows' => count($rows)];
                $this->line(sprintf('run %d: %s', $i, json_encode($extractor->timings + ['rows' => count($rows)])));
            } catch (\Throwable $e) {
                $this->warn("run {$i} failed: ".$e->getMessage());
            }
        }

        if ($runs === []) {
            return self::FAILURE;
        }

        $rows = [];
        foreach (['read_s', 'vision_s', 'structure_s', 'total_s'] as $k) {
            $v = array_values(array_filter(array_column($runs, $k), 'is_numeric'));
            sort($v);
            if ($v !== []) {
                $rows[] = [$k, $this->pct($v, 0.5), $this->pct($v, 0.95), max($v)];
            }
        }
        $this->table(['stage', 'p50 (s)', 'p95 (s)', 'max (s)'], $rows);
        $this->info('via: '.implode(', ', array_unique(array_column($runs, 'via'))).'  ·  rows: '.implode(', ', array_column($runs, 'rows')));

        return self::SUCCESS;
    }

    private function pct(array $sorted, float $p): float
    {
        return $sorted[(int) min(count($sorted) - 1, floor($p * count($sorted)))];
    }
}
