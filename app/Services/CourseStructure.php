<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseCustomization;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

/**
 * What a course looks like right now: the config defaults (config/course-structure.php for the module/lesson
 * outline, config/course-catalog.php for the course card) with the faculty's changes on top.
 *
 * The faculty's outline is a full ordered list of modules and lessons, keyed exactly like the config
 * ("M1", "lesson-01"). Keys never change once given out, so a student's progress never points at the wrong
 * lesson: renaming changes a title, deleting only hides it, and a new lesson takes the next free key.
 * A lesson the faculty added has no Blade file; its page lives in lesson_contents (see LessonContentService).
 *
 * Read on every lesson request, so the row is cached (memoized within the request too) and the cache is
 * cleared whenever the row is saved.
 */
class CourseStructure
{
    /**
     * The course's live modules, in order, shaped like config("course-structure.{course}.modules"):
     * ['M1' => ['title' => …, 'lessons' => ['lesson-01' => ['title' => …, 'expected' => …, 'custom' => bool], …]], …].
     * Hidden modules and lessons are left out.
     */
    public static function modules(string $course): array
    {
        $outline = self::customization($course)['outline'] ?? null;
        $config = config("course-structure.{$course}.modules", []);

        if (! is_array($outline) || $outline === []) {
            return $config;
        }

        $modules = [];
        foreach (self::mergedOutline($course) as $module) {
            if (! empty($module['hidden'])) {
                continue;
            }
            $lessons = [];
            foreach ($module['lessons'] as $lesson) {
                if (! empty($lesson['hidden'])) {
                    continue;
                }
                $base = $config[$module['key']]['lessons'][$lesson['key']] ?? [];
                $lessons[$lesson['key']] = array_merge($base, ['title' => $lesson['title'], 'custom' => ! empty($lesson['custom'])]
                    + (isset($lesson['lab']) ? ['lab' => $lesson['lab']] : [])
                    + (isset($lesson['sim']) ? ['sim' => $lesson['sim']] : []));
            }
            $base = $config[$module['key']] ?? [];
            unset($base['lessons']);
            $modules[$module['key']] = array_merge($base, ['title' => $module['title'], 'lessons' => $lessons]);
        }

        return $modules;
    }

    /**
     * The editable outline: every module and lesson, hidden ones included, in the faculty's order. Anything in
     * the config that the saved outline doesn't know yet (a lesson added to the course later) is appended.
     *
     * @return list<array{key: string, title: string, hidden: bool, custom: bool, lessons: list<array{key: string, title: string, hidden: bool, custom: bool}>}>
     */
    public static function mergedOutline(string $course): array
    {
        $config = config("course-structure.{$course}.modules", []);
        $saved = self::customization($course)['outline'] ?? [];
        $saved = is_array($saved) ? $saved : [];

        $out = [];
        $seen = [];
        foreach ($saved as $module) {
            $key = (string) ($module['key'] ?? '');
            if ($key === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = [];
            $lessons = [];
            foreach ($module['lessons'] ?? [] as $lesson) {
                $lk = (string) ($lesson['key'] ?? '');
                if ($lk === '' || isset($seen[$key][$lk])) {
                    continue;
                }
                $seen[$key][$lk] = true;
                $inConfig = isset($config[$key]['lessons'][$lk]);
                $lessons[] = [
                    'key' => $lk,
                    'title' => (string) ($lesson['title'] ?? $config[$key]['lessons'][$lk]['title'] ?? $lk),
                    'hidden' => (bool) ($lesson['hidden'] ?? false),
                    'custom' => ! $inConfig,
                    // A custom lesson's sim lab (if the faculty picked a template for it): which
                    // engine, and the template's file id — same fields a config lesson carries.
                    'lab' => $lesson['lab'] ?? ($config[$key]['lessons'][$lk]['lab'] ?? null),
                    'sim' => $lesson['sim'] ?? ($config[$key]['lessons'][$lk]['sim'] ?? null),
                ];
            }
            $out[] = [
                'key' => $key,
                'title' => (string) ($module['title'] ?? $config[$key]['title'] ?? $key),
                'hidden' => (bool) ($module['hidden'] ?? false),
                'custom' => ! isset($config[$key]),
                'lessons' => $lessons,
            ];
        }

        // Config entries the saved outline has never seen.
        foreach ($config as $key => $module) {
            $i = array_search($key, array_column($out, 'key'), true);
            if ($i === false) {
                $out[] = ['key' => (string) $key, 'title' => (string) ($module['title'] ?? $key), 'hidden' => false, 'custom' => false, 'lessons' => []];
                $i = array_key_last($out);
            }
            foreach ($module['lessons'] ?? [] as $lk => $lesson) {
                if (! in_array($lk, array_column($out[$i]['lessons'], 'key'), true)) {
                    $out[$i]['lessons'][] = ['key' => (string) $lk, 'title' => (string) ($lesson['title'] ?? $lk), 'hidden' => false, 'custom' => false];
                }
            }
        }

        return $out;
    }

    /** Whether a lesson is one the faculty added (it has no Blade file). */
    public static function isCustomLesson(string $course, string $module, string $lesson): bool
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        foreach (self::modules($course) as $mk => $m) {
            foreach ($m['lessons'] ?? [] as $lk => $l) {
                if (CourseProgressService::normalize((string) $mk, (string) $lk) === [$module, $lesson]) {
                    return ! empty($l['custom']);
                }
            }
        }

        return false;
    }

    /** A lesson's live title, or null when it isn't (or is no longer) part of the course. */
    public static function lessonTitle(string $course, string $module, string $lesson): ?string
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        foreach (self::modules($course) as $mk => $m) {
            foreach ($m['lessons'] ?? [] as $lk => $l) {
                if (CourseProgressService::normalize((string) $mk, (string) $lk) === [$module, $lesson]) {
                    return $l['title'] ?? $lk;
                }
            }
        }

        return null;
    }

    /** A module's live title, looked up by any spelling of its key ("M1" or "m1"). */
    public static function moduleTitle(string $course, string $module): ?string
    {
        foreach (self::modules($course) as $mk => $m) {
            if (strtolower((string) $mk) === strtolower($module)) {
                return $m['title'] ?? $mk;
            }
        }

        return null;
    }

    /**
     * A catalog course's card (config/course-catalog.php) with the faculty's title, blurb, level and cover art.
     * `art` becomes a path asset() can serve; an uploaded cover is on the public disk.
     */
    public static function card(string $planet, string $id): array
    {
        $card = config("course-catalog.{$planet}.courses.{$id}", []);

        if ($card === []) {
            $card = self::courseRowCard($planet, $id);
            if ($card === []) {
                return [];
            }
        }

        $edit = self::customization(CourseProgressService::keyFor($planet, $id))['card'] ?? null;

        if (! is_array($edit) || $edit === []) {
            return $card;
        }

        foreach (['title', 'blurb', 'level'] as $field) {
            if (filled($edit[$field] ?? null)) {
                $card[$field] = $edit[$field];
            }
        }
        if (filled($edit['art_path'] ?? null)) {
            $card['art'] = 'storage/'.$edit['art_path'];
        }

        return $card;
    }

    /** Every course of a planet as cards: config("course-catalog.{planet}.courses") plus published faculty-created courses. */
    public static function cards(string $planet): array
    {
        $out = [];
        foreach (array_keys(config("course-catalog.{$planet}.courses", [])) as $id) {
            $out[$id] = self::card($planet, (string) $id);
        }
        try {
            foreach (Course::where('planet', $planet)->where('status', Course::PUBLISHED)->pluck('slug') as $slug) {
                $out[$slug] = self::card($planet, (string) $slug);
            }
        } catch (QueryException) {
            // table not migrated yet: no faculty-created course can exist
        }

        return $out;
    }

    /** A faculty-created course's card, shaped like a config catalog entry. */
    private static function courseRowCard(string $planet, string $id): array
    {
        try {
            $course = Course::where('planet', $planet)->where('slug', $id)->first();
        } catch (QueryException) {
            return [];   // table not migrated yet
        }
        if (! $course) {
            return [];
        }

        return [
            'title' => $course->title,
            'code' => null,
            'blurb' => $course->blurb,
            'banner' => null,
            'art' => $course->art_path ? 'storage/'.$course->art_path : null,
            'logo' => null,
            'level' => $course->level,
            'description' => $course->blurb,
            'hidden' => ! $course->isPublished(),
        ];
    }

    /** @return array{card?: array|null, outline?: array|null} */
    public static function customization(string $course): array
    {
        // A faculty member's outline and card belong to their class: everyone else gets the config defaults.
        if (($crew = CourseTrack::classId($course)) === null) {
            return [];
        }

        try {
            $all = Cache::memo()->rememberForever(self::cacheKey($course), fn () => CourseCustomization::where('course', $course)->whereNotNull('crew_id')
                ->get(['crew_id', 'card', 'outline'])
                ->mapWithKeys(fn (CourseCustomization $c) => [$c->crew_id => ['card' => $c->card, 'outline' => $c->outline]])
                ->all());
        } catch (QueryException) {
            return [];   // table not migrated yet: the course is its config defaults
        }

        return $all[$crew] ?? [];
    }

    public static function forget(string $course): void
    {
        Cache::memo()->forget(self::cacheKey($course));
    }

    private static function cacheKey(string $course): string
    {
        return "course-custom:v2:{$course}";
    }
}
