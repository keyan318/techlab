<?php

namespace App\Services;

use App\Models\MaterialChunk;
use App\Models\ModuleMaterial;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * The faculty's own uploads, as sources a student picks by hand — the "Add from Materials"
 * button next to "Add from Planets". Astro used to search these on every message, which meant
 * an irrelevant handout got cited just because a keyword happened to overlap. Now a material
 * only ever grounds an answer when the student attaches it themselves, exactly like a Planet
 * lesson, and only materials from courses they are on the roster of are ever reachable.
 */
class CourseMaterialSourceService
{
    /** Materials attached to one message, at most. */
    public const MAX_SOURCES = 4;

    /** Each attached material's text is trimmed to this before it goes in the prompt. */
    public const MAX_CHARS = 6000;

    /**
     * Materials the student can pick from, grouped by course then module, for the picker.
     * Only materials Astro can actually read make the list — a link, image or archive has
     * nothing to ground an answer in.
     *
     * @return array<int, array{crew_id: int, course: string, modules: array<int, array{title: string, materials: array<int, array{id: int, name: string, kind: string, url: string}>}>}>
     */
    public function catalog(User $user): array
    {
        $crews = $user->crewMemberships()->with('modules.materials')->get();

        $out = [];
        foreach ($crews as $crew) {
            $modules = [];
            foreach ($crew->modules as $module) {
                $materials = $module->materials
                    ->filter(fn (ModuleMaterial $m) => $m->isReadable())
                    ->map(fn (ModuleMaterial $m) => [
                        'id' => $m->id,
                        'name' => $m->name,
                        'kind' => $m->kind,
                        'url' => $m->openUrl(),
                    ])->values()->all();

                if ($materials) {
                    $modules[] = ['title' => $module->title, 'materials' => $materials];
                }
            }

            if ($modules) {
                $out[] = ['crew_id' => $crew->id, 'course' => $crew->name, 'modules' => $modules];
            }
        }

        return $out;
    }

    /**
     * Resolve materials the student picked by hand into grounding text. Anything not on the
     * roster for, or with nothing extracted yet, is silently dropped rather than erroring —
     * the same way a stale Planet-lesson reference is dropped by LessonSourceService::resolve().
     *
     * @param  int[]  $materialIds
     * @return array<int, array{label: string, title: string, course: string, url: string, text: string}>
     */
    public function resolve(array $materialIds, User $user): array
    {
        $ids = array_slice(array_values(array_unique(array_map('intval', $materialIds))), 0, self::MAX_SOURCES);
        if (! $ids) {
            return [];
        }

        $materials = ModuleMaterial::query()
            ->whereIn('id', $ids)
            ->whereIn('course_module_id', fn ($q) => $q->select('id')->from('course_modules')
                ->whereIn('crew_id', fn ($qq) => $qq->select('crew_id')->from('crew_members')->where('user_id', $user->id)))
            ->with('module.crew')
            ->get()
            ->keyBy('id');

        if ($materials->isEmpty()) {
            return [];
        }

        $textByMaterial = MaterialChunk::query()
            ->whereIn('module_material_id', $materials->keys())
            ->orderBy('module_material_id')->orderBy('position')
            ->get(['module_material_id', 'text'])
            ->groupBy('module_material_id')
            ->map(fn ($rows) => trim($rows->pluck('text')->implode("\n\n")));

        $out = [];
        foreach ($ids as $id) {
            $material = $materials->get($id);
            $text = $material ? ($textByMaterial->get($id) ?? '') : '';
            if (! $material || ! $material->module || $text === '') {
                continue;
            }

            $courseName = $material->module->crew?->name ?? 'Your course';
            $out[] = [
                'label' => $material->name.' — '.$courseName,
                'title' => $material->name,
                'course' => $courseName,
                'url' => $material->openUrl(),
                'text' => Str::limit($text, self::MAX_CHARS, '…'),
            ];
        }

        return $out;
    }

    /**
     * All the text of the given materials, for generating something from them rather than
     * answering a question. Scoped to one crew so a faculty member can only draw on their own.
     */
    public function textForMaterials(array $materialIds, int $crewId, int $maxChars = 8000): string
    {
        if (! $materialIds) {
            return '';
        }

        $text = MaterialChunk::query()
            ->where('crew_id', $crewId)
            ->whereIn('module_material_id', $materialIds)
            ->orderBy('module_material_id')->orderBy('position')
            ->limit(400)
            ->pluck('text')
            ->implode("\n\n");

        return trim(mb_substr($text, 0, $maxChars));
    }

    /**
     * System-prompt block grounding Astro in the faculty's own material.
     * Same rule as the web block: cite only what is listed here.
     */
    public function promptBlock(array $sources): string
    {
        if (! $sources) {
            return '';
        }

        $blocks = [];
        foreach ($sources as $i => $s) {
            $n = $i + 1;
            $blocks[] = "[Material {$n}: {$s['title']} — {$s['course']}]\n{$s['text']}";
        }

        return 'The student has attached these excerpts from their course materials as sources. '
            .'Answer from them first and cite by number like [Material 1]. Never cite a material that is '
            .'not listed here. If they do not cover part of the question, say which part and answer that '
            ."from general knowledge.\n\n".implode("\n\n---\n\n", $blocks);
    }
}
