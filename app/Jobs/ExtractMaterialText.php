<?php

namespace App\Jobs;

use App\Exceptions\DocumentTextException;
use App\Models\ModuleMaterial;
use App\Services\DocumentTextExtractor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExtractMaterialText implements ShouldQueue
{
    use Queueable;

    public const CHUNK_CHARS = 1200;

    public const MAX_CHARS = 400000;

    public function __construct(public int $materialId) {}

    public function handle(DocumentTextExtractor $extractor): void
    {
        $material = ModuleMaterial::with('module')->find($this->materialId);

        if (! $material || ! $material->module) {
            return;
        }

        if (! $material->isReadable()) {
            $material->update([
                'indexed_at' => now(),
                'index_error' => 'Astro can only read text out of PDF, Word, PowerPoint and text files.',
            ]);

            return;
        }

        $disk = Storage::disk('supabase_private');

        if (! $disk->exists($material->path)) {
            $material->update([
                'indexed_at' => now(),
                'index_error' => 'The file is missing from storage.',
            ]);

            return;
        }

        $temporaryPath = null;

        try {
            $stream = $disk->readStream($material->path);

            if (! is_resource($stream)) {
                throw new \RuntimeException('Unable to read the uploaded file from Supabase.');
            }

            $temporaryPath = tempnam(sys_get_temp_dir(), 'techlab-material-');

            if ($temporaryPath === false) {
                fclose($stream);

                throw new \RuntimeException('Unable to create a temporary file.');
            }

            $target = fopen($temporaryPath, 'wb');

            if ($target === false) {
                fclose($stream);

                throw new \RuntimeException('Unable to open the temporary file.');
            }

            try {
                if (stream_copy_to_stream($stream, $target) === false) {
                    throw new \RuntimeException('Unable to copy the uploaded file.');
                }
            } finally {
                fclose($stream);
                fclose($target);
            }

            $text = $extractor->extract(
                $temporaryPath,
                $material->ext,
                $material->name
            );

        } catch (DocumentTextException $e) {
            $material->update([
                'indexed_at' => now(),
                'index_error' => $e->getMessage(),
            ]);

            return;

        } catch (\Throwable $e) {
            Log::error("Material extraction failed for #{$material->id}: ".$e->getMessage());

            $material->update([
                'indexed_at' => now(),
                'index_error' => 'Astro could not read this file.',
            ]);

            return;

        } finally {
            if ($temporaryPath && file_exists($temporaryPath)) {
                @unlink($temporaryPath);
            }
        }

        $chunks = self::chunk($text);

        DB::transaction(function () use ($material, $chunks) {
            $material->chunks()->delete();

            $rows = [];

            foreach ($chunks as $i => $chunk) {
                $rows[] = [
                    'module_material_id' => $material->id,
                    'crew_id' => $material->module->crew_id,
                    'position' => $i,
                    'text' => $chunk,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            foreach (array_chunk($rows, 200) as $batch) {
                DB::table('material_chunks')->insert($batch);
            }

            $material->update([
                'indexed_at' => now(),
                'index_error' => $chunks ? null : 'This file had no readable text.',
            ]);
        });
    }

    public static function chunk(string $text): array
    {
        $text = trim(preg_replace("/\n{3,}/", "\n\n", $text) ?? $text);

        if ($text === '') {
            return [];
        }

        $text = mb_substr($text, 0, self::MAX_CHARS);

        $chunks = [];
        $current = '';

        foreach (preg_split("/\n\s*\n/", $text) ?: [] as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            if (mb_strlen($paragraph) > self::CHUNK_CHARS) {
                if ($current !== '') {
                    $chunks[] = $current;
                    $current = '';
                }

                foreach (mb_str_split($paragraph, self::CHUNK_CHARS) as $piece) {
                    $chunks[] = trim($piece);
                }

                continue;
            }

            if ($current !== '' && mb_strlen($current) + mb_strlen($paragraph) + 2 > self::CHUNK_CHARS) {
                $chunks[] = $current;
                $current = '';
            }

            $current = $current === ''
                ? $paragraph
                : $current."\n\n".$paragraph;
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return array_values(array_filter(
            $chunks,
            fn ($chunk) => trim($chunk) !== ''
        ));
    }
}
