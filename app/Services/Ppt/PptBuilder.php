<?php

namespace App\Services\Ppt;

use Illuminate\Support\Facades\Process;
use RuntimeException;

/** Draws a cleaned deck spec into a .pptx by running scripts/pptx/build.mjs (PptxGenJS) under Node. */
class PptBuilder
{
    /** @param array{title: string, theme: string, accent: ?string, slides: array<int, array<string, mixed>>} $deck */
    public function build(array $deck): string
    {
        $out = tempnam(sys_get_temp_dir(), 'techlab-ppt-').'.pptx';

        $result = Process::path(base_path())
            ->timeout((int) config('ppt.build_timeout', 60))
            ->input(json_encode($deck, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE))
            ->run([$this->node(), (string) config('ppt.script'), $out]);

        if (! $result->successful() || ! is_file($out) || filesize($out) === 0) {
            @unlink($out);

            throw new RuntimeException('PPT build failed: '.mb_substr(trim($result->errorOutput() ?: $result->output()), 0, 500));
        }

        return $out;
    }

    private function node(): string
    {
        $configured = config('ppt.node_binary');
        if ($configured) {
            return $configured;
        }

        foreach (['/opt/homebrew/bin/node', '/usr/local/bin/node', '/usr/bin/node'] as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }

        return 'node';   // fall back to PATH
    }
}
