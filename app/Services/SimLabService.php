<?php

namespace App\Services;

use App\Models\Course;
use App\Models\LessonSimLab;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * Faculty-written simulator labs ("Configure it yourself" / "Defend it yourself"). A networking or cybersecurity
 * lesson's lab is a JSON file the simulator loads: NetSim (public/netsim-app/labs/<id>.json, the lab under "lab"),
 * Relay Console (public/relay-console/labs/<id>.json) or Citadel Sim (public/citadel-sim/labs/<id>.json), both with
 * the lab at the top. Each has a title, steps with instructions, and objectives whose `check` is what the simulator
 * tests (a ping that must work, an address that must be in a subnet, the category an item belongs to, the flag to
 * submit...).
 *
 * A LessonSimLab row is a patch on that file: new words (title, summary, story messages, step titles and
 * instructions, objective labels), new values for the checks (another subnet, port, answer, category...) and, in
 * Citadel Sim, new contents for the files on the lab's computer. The network, the files that exist, the number of
 * steps and objectives and the kind of each check stay the file's, so the lab can always be loaded and graded.
 * apply() lays the patch on the file; the lab page hands the result to the simulator.
 *
 * Networking and cybersecurity courses (the programming planet's coding labs are LessonLabService).
 */
class SimLabService
{
    /** Where each simulator's lab files live, under public/. */
    public const DIRS = ['netsim' => 'netsim-app/labs', 'console' => 'relay-console/labs', 'citadel' => 'citadel-sim/labs'];

    public const PLANETS = ['networking', 'cybersecurity'];

    /** The story messages a faculty member may rewrite, when the lab file has them (Relay Console, Citadel Sim). */
    private const STORY = ['alert', 'vex', 'victory'];

    /** Citadel Sim's category shorthands (public/citadel-sim/js/engine.js CATEGORY): the check stores the full word. */
    private const CATEGORY = ['vuln' => 'vulnerability', 'c' => 'confidentiality', 'i' => 'integrity', 'a' => 'availability'];

    /** Check keys holding a pattern of a command or text (a regular expression). */
    private const PATTERNS = ['pattern', 'before', 'after', 'mustNotMatch'];

    /** Check keys a faculty member never edits: what kind of check it is, and the simulator's internal fact names. */
    private const FIXED = ['type', 'fact'];

    /** Check keys that name a device in the lab's network. */
    private const DEVICES = ['device', 'from', 'toDevice', 'neighbor', 'through', 'cut.device'];

    /** Values a check key may take ("type:key" for the ones that depend on the check). */
    private const OPTIONS = [
        'ping:expect' => ['success', 'fail'],
        'tcp:expect' => ['open', 'refused', 'filtered'],
        'dns:expect' => ['resolves', 'nxdomain', 'blocked'],
        'http:expect' => ['ok', 'fail'],
        'ospf-neighbor:expect' => ['full', 'none'],
        'route:expect' => ['present', 'absent'],
        'route:source' => ['ospf', 'static', 'connected'],
        'path:expect' => ['through', 'avoids'],
        'fw-policy:hook' => ['input', 'forward', 'output'],
        'fw-policy:policy' => ['accept', 'drop'],
        'stp-mode:mode' => ['pvst', 'rapid-pvst', 'mst'],
        'stp-port:state' => ['forwarding', 'blocking', 'err-disabled', 'root-inconsistent'],
        'fhrp-active:proto' => ['hsrp', 'vrrp'],
        'triaged:as' => ['keep', 'report'],
        'login:expect' => ['success', 'fail'],
        'iface-config:is.mode' => ['access', 'trunk'],
        'iface-config:is.psViolation' => ['shutdown', 'restrict', 'protect'],
    ];

    private const LABELS = [
        'device' => 'Device', 'from' => 'From device', 'toDevice' => 'To device', 'toAddr' => 'To address',
        'iface' => 'Interface', 'expect' => 'Result', 'inSubnet' => 'Address must be in subnet', 'cidr' => 'Exact address',
        'via' => 'Default gateway', 'value' => 'Correct answer', 'alt' => 'Also accept (one per line)',
        'url' => 'Web address', 'contains' => 'Page must contain', 'port' => 'Port', 'name' => 'Name',
        'addr' => 'Must resolve to', 'neighbor' => 'Neighbor', 'dest' => 'Destination network', 'source' => 'Learned from',
        'paths' => 'Equal-cost paths', 'vlan' => 'VLAN', 'state' => 'Port state', 'mode' => 'Mode', 'group' => 'Group',
        'proto' => 'Protocol', 'hook' => 'Chain', 'policy' => 'Policy', 'has' => 'Rule must contain', 'through' => 'Passes through',
        'pattern' => 'Command the student must run (pattern)', 'cut.device' => 'Cut link on device', 'cut.iface' => 'Cut link on interface',
        // Citadel Sim
        'file' => 'File', 'user' => 'User account', 'item' => 'Item', 'classified:as' => 'Correct category',
        'triaged:as' => 'Correct action', 'triaged:id' => 'Message', 'flag:value' => 'Correct answer (flag)',
        'process-killed:name' => 'Process', 'a' => 'File A', 'b' => 'Must match file B', 'password' => 'Password',
        'before' => 'Command run first (pattern)', 'after' => 'Command run after it (pattern)', 'mustNotMatch' => 'File must not contain (pattern)',
    ];

    public static function supports(string $course): bool
    {
        if (! CourseProgressService::exists($course) || ! in_array(CourseProgressService::planetFor($course), self::PLANETS, true)) {
            return false;
        }

        // A faculty-created course without a config blueprint declares its engine (or 'none')
        // itself; a config course has no such row, so it's always supported as before.
        $kind = Course::labKindFor($course);

        return $kind === null || in_array($kind, ['netsim', 'relay', 'citadel'], true);
    }

    /**
     * The lesson's lab file as it ships, or null when the lesson has no simulator lab.
     *
     * @return array{sim: string, id: string, file: array<string, mixed>}|null
     */
    public static function base(string $course, string $module, string $lesson): ?array
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        $id = CourseProgressService::labFor($module, $lesson, $course);
        $sim = CourseProgressService::simFor($module, $lesson, $course);

        return $id ? self::load($sim, $id) : null;
    }

    /** A lab file straight off disk, by engine and id — null if it isn't a real, usable lab file. */
    public static function load(string $sim, string $id): ?array
    {
        if (! isset(self::DIRS[$sim]) || ! preg_match('/^[a-z0-9-]{1,64}$/', $id)) {
            return null;
        }

        $path = public_path(self::DIRS[$sim].'/'.$id.'.json');
        $file = is_file($path) ? json_decode((string) file_get_contents($path), true) : null;

        return is_array($file) && is_array(self::def($file, $sim)['steps'] ?? null) ? ['sim' => $sim, 'id' => $id, 'file' => $file] : null;
    }

    /**
     * Every default sim lab a faculty member could start a custom lesson from, for one engine (or
     * all three): every config/course-structure.php lesson with a `lab`+`sim`, across every
     * networking/cybersecurity course, each a usable template to copy and then edit.
     *
     * @return list<array{course: string, module: string, lesson: string, sim: string, lab: string, title: string}>
     */
    public static function templates(?string $sim = null): array
    {
        $out = [];
        foreach (config('course-structure', []) as $course => $blueprint) {
            if (! in_array(CourseProgressService::planetFor((string) $course), self::PLANETS, true)) {
                continue;
            }
            foreach ($blueprint['modules'] ?? [] as $module => $mod) {
                foreach ($mod['lessons'] ?? [] as $lesson => $l) {
                    if (empty($l['lab']) || empty($l['sim']) || ($sim !== null && $l['sim'] !== $sim)) {
                        continue;
                    }
                    $out[] = [
                        'course' => (string) $course,
                        'module' => (string) $module,
                        'lesson' => (string) $lesson,
                        'sim' => $l['sim'],
                        'lab' => $l['lab'],
                        'title' => $l['title'] ?? $lesson,
                    ];
                }
            }
        }

        return $out;
    }

    /** The faculty's patch for a lesson's lab, or null. */
    public static function row(string $course, string $module, string $lesson): ?array
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        // A patch belongs to one class: self-paced students (null) always get the lab file as it is.
        if (($crew = CourseTrack::classId($course)) === null) {
            return null;
        }

        try {
            $all = Cache::memo()->rememberForever(self::cacheKey($course), fn () => LessonSimLab::where('course', $course)->whereNotNull('crew_id')->get()
                ->groupBy('crew_id')
                ->map(fn ($rows) => $rows->mapWithKeys(fn (LessonSimLab $l) => [$l->module.'/'.$l->lesson => $l->data])->all())
                ->all());
        } catch (QueryException) {
            return null;
        }

        return $all[$crew][$module.'/'.$lesson] ?? null;
    }

    public static function forget(string $course): void
    {
        Cache::memo()->forget(self::cacheKey($course));
    }

    public static function edited(string $course, string $module, string $lesson): bool
    {
        return self::supports($course) && self::row($course, $module, $lesson) !== null;
    }

    /** The lab file the simulator should load: the file with the faculty's patch laid on it. */
    public static function file(string $course, string $module, string $lesson): ?array
    {
        $base = self::base($course, $module, $lesson);
        if (! $base) {
            return null;
        }
        $patch = self::supports($course) ? self::row($course, $module, $lesson) : null;

        return $patch ? self::apply($base['file'], $base['sim'], $patch) : $base['file'];
    }

    /**
     * What the lab editor shows: every editable text and check value, as the students currently get them.
     *
     * @return array<string, mixed>|null
     */
    public static function document(string $course, string $module, string $lesson): ?array
    {
        $base = self::base($course, $module, $lesson);
        if (! $base) {
            return null;
        }
        $sim = $base['sim'];
        $current = self::def(self::file($course, $module, $lesson), $sim);
        $original = self::def($base['file'], $sim);
        $choices = self::choices($base['file'], $sim);

        $steps = [];
        foreach ($original['steps'] as $i => $step) {
            $objectives = [];
            foreach ($step['objectives'] ?? [] as $k => $objective) {
                $check = $current['steps'][$i]['objectives'][$k]['check'] ?? $objective['check'];
                $fields = [];
                foreach (self::fields($objective['check'], $choices) as $path => $field) {
                    $fields[] = $field + ['path' => $path, 'value' => Arr::get($check, $path)];
                }
                $objectives[] = [
                    'label' => $current['steps'][$i]['objectives'][$k]['label'] ?? $objective['label'],
                    'kind' => $objective['check']['type'],
                    'fields' => $fields,
                ];
            }
            $steps[] = [
                'title' => $current['steps'][$i]['title'] ?? $step['title'],
                'body' => $current['steps'][$i]['body'] ?? $step['body'],
                'objectives' => $objectives,
            ];
        }

        $story = [];
        foreach (self::STORY as $key) {
            if ($sim !== 'netsim' && is_string($original['story'][$key] ?? null)) {
                $story[$key] = (string) ($current['story'][$key] ?? '');
            }
        }
        $files = [];
        foreach (self::textFiles($original, $sim) as $path => $content) {
            $files[] = ['path' => $path, 'content' => (string) ($current['files'][$path] ?? $content)];
        }

        return [
            'sim' => $sim,
            'simulator' => ['console' => 'Relay Console', 'citadel' => 'Citadel Sim'][$sim] ?? 'NetSim',
            'edited' => self::row($course, $module, $lesson) !== null,
            'title' => (string) ($current['title'] ?? ''),
            'blurb' => $sim === 'netsim' ? (string) ($current['blurb'] ?? '') : null,
            'story' => (object) $story,
            'steps' => $steps,
            'files' => $files,
        ];
    }

    /**
     * Checks a faculty member's lab against the lesson's file and returns the patch to store.
     *
     * @param  array<string, mixed>  $input  {title, blurb?, story?: {alert, vex, victory}, files?: {path: content}, steps: [{title, body, objectives: [{label, values: {path: value}}]}]}
     * @return array{0: array<string, mixed>|null, 1: array<string, string>} the patch, or null and the errors
     */
    public static function patch(string $course, string $module, string $lesson, array $input): array
    {
        $base = self::base($course, $module, $lesson);
        $original = self::def($base['file'], $base['sim']);
        $choices = self::choices($base['file'], $base['sim']);
        $errors = [];
        $text = function (string $key, $value, int $max, bool $required = true) use (&$errors): string {
            $value = trim(str_replace("\r\n", "\n", is_scalar($value) ? (string) $value : ''));
            if ($required && $value === '') {
                $errors[$key] = 'This cannot be empty.';
            } elseif (mb_strlen($value) > $max) {
                $errors[$key] = "Keep it under {$max} characters.";
            }

            return $value;
        };

        $patch = ['title' => $text('title', $input['title'] ?? '', 160)];
        if ($base['sim'] === 'netsim') {
            $patch['blurb'] = $text('blurb', $input['blurb'] ?? '', 500);
        } else {
            foreach (self::STORY as $key) {
                if (is_string($original['story'][$key] ?? null)) {
                    $patch['story'][$key] = $text("story.$key", $input['story'][$key] ?? '', 1000);
                }
            }
        }
        foreach (self::textFiles($original, $base['sim']) as $path => $content) {
            $value = $input['files'][$path] ?? $content;
            $value = str_replace("\r\n", "\n", is_string($value) ? $value : '');
            if (mb_strlen($value) > 20000) {
                $errors["files.$path"] = "$path: keep the file under 20000 characters.";
            }
            $patch['files'][$path] = $value;
        }

        $patch['steps'] = [];
        foreach ($original['steps'] as $i => $step) {
            $in = $input['steps'][$i] ?? [];
            $n = $i + 1;
            $out = [
                'title' => $text("steps.$i.title", $in['title'] ?? '', 160),
                'body' => $text("steps.$i.body", $in['body'] ?? '', 5000),
                'objectives' => [],
            ];
            foreach ($step['objectives'] ?? [] as $k => $objective) {
                $o = $in['objectives'][$k] ?? [];
                $values = [];
                foreach (self::fields($objective['check'], $choices) as $path => $field) {
                    $key = "steps.$i.objectives.$k.$path";
                    $values[$path] = self::value($field, $o['values'][$path] ?? null, $key, $errors, "Step $n, objective ".($k + 1));
                }
                $out['objectives'][] = ['label' => $text("steps.$i.objectives.$k.label", $o['label'] ?? '', 200), 'values' => $values];
            }
            $patch['steps'][] = $out;
        }

        return $errors ? [null, $errors] : [$patch, []];
    }

    /**
     * A lab file with a faculty patch laid on it.
     *
     * @param  array<string, mixed>  $file
     * @param  array<string, mixed>  $patch
     * @return array<string, mixed>
     */
    public static function apply(array $file, string $sim, array $patch): array
    {
        $def = self::def($file, $sim);
        $def['title'] = $patch['title'] ?? $def['title'];
        if ($sim === 'netsim' && isset($patch['blurb'])) {
            $def['blurb'] = $patch['blurb'];
        }
        foreach ($patch['story'] ?? [] as $key => $value) {
            if (in_array($key, self::STORY, true) && is_string($def['story'][$key] ?? null)) {
                $def['story'][$key] = $value;
            }
        }
        // Older patches wrote the victory message on its own.
        if (isset($patch['victory']) && is_string($def['story']['victory'] ?? null)) {
            $def['story']['victory'] = $patch['victory'];
        }
        foreach ($patch['files'] ?? [] as $path => $content) {
            if ($sim === 'citadel' && is_string($def['files'][$path] ?? null)) {
                $def['files'][$path] = $content;
            }
        }
        foreach ($def['steps'] as $i => &$step) {
            $p = $patch['steps'][$i] ?? null;
            if (! $p) {
                continue;
            }
            $step['title'] = $p['title'] ?? $step['title'];
            $step['body'] = $p['body'] ?? $step['body'];
            foreach ($step['objectives'] as $k => &$objective) {
                $o = $p['objectives'][$k] ?? null;
                if (! $o) {
                    continue;
                }
                $objective['label'] = $o['label'] ?? $objective['label'];
                foreach ($o['values'] ?? [] as $path => $value) {
                    // Only keys the file's check already has: the kind of check never changes.
                    if (in_array($path, self::FIXED, true) || ! Arr::has($objective['check'], $path)) {
                        continue;
                    }
                    // Citadel Sim only accepts a submitted flag listed in the lab's flags: swap the old one out.
                    if ($sim === 'citadel' && $objective['check']['type'] === 'flag' && $path === 'value' && is_array($def['flags'] ?? null)) {
                        $old = $objective['check']['value'];
                        $def['flags'] = array_values(array_unique(array_map(fn ($f) => $f === $old ? $value : $f, $def['flags'])));
                        if (! in_array($value, $def['flags'], true)) {
                            $def['flags'][] = $value;
                        }
                    }
                    Arr::set($objective['check'], $path, $value);
                }
            }
            unset($objective);
        }
        unset($step);

        if ($sim === 'netsim') {
            $file['lab'] = $def;

            return $file;
        }

        return $def;
    }

    /**
     * What a lab file grades students on: every objective's check, the accepted flags and (Citadel Sim) the
     * files on the lab's computer. Two versions with the same grading are passed the same way, so only a change
     * to it has to be played through before it is saved.
     *
     * @param  array<string, mixed>  $file
     * @return array<string, mixed>
     */
    public static function grading(array $file, string $sim): array
    {
        $def = self::def($file, $sim);

        return [
            'checks' => array_map(fn ($s) => array_map(fn ($o) => $o['check'] ?? null, $s['objectives'] ?? []), $def['steps'] ?? []),
            'flags' => $def['flags'] ?? null,
            'files' => self::textFiles($def, $sim),
        ];
    }

    /** @return array<string, mixed> the lab inside a file: NetSim keeps it under "lab", the others at the top */
    private static function def(?array $file, string $sim): array
    {
        return $sim === 'netsim' ? ($file['lab'] ?? []) : ($file ?? []);
    }

    /** @return array<string, string> a Citadel Sim lab's files with text contents, by path */
    private static function textFiles(array $def, string $sim): array
    {
        return $sim === 'citadel' ? array_filter($def['files'] ?? [], 'is_string') : [];
    }

    /**
     * What a check value may be picked from, by check key ("key" or "type:key"): the devices of a NetSim network,
     * or a Citadel Sim lab's files, accounts, items to classify, messages and processes.
     *
     * @return array<string, list<string>>
     */
    private static function choices(array $file, string $sim): array
    {
        $list = fn ($rows, string $key) => array_values(array_filter(array_map(fn ($r) => is_array($r) ? ($r[$key] ?? null) : null, is_array($rows) ? $rows : [])));

        if ($sim === 'netsim') {
            return array_fill_keys(self::DEVICES, $list($file['devices'] ?? [], 'id'));
        }
        if ($sim !== 'citadel') {
            return [];
        }
        $files = array_keys($file['files'] ?? []);

        return [
            'file' => $files, 'a' => $files, 'b' => $files,
            'user' => $list($file['users'] ?? [], 'name'),
            'classified:item' => array_keys($file['classify'] ?? []),
            'triaged:id' => $list($file['inbox'] ?? [], 'id'),
            'process-killed:name' => $list($file['processes'] ?? [], 'name'),
        ];
    }

    /**
     * The editable values of a check, keyed by dotted path: {label, input: text|number|bool|select|lines, options?}.
     *
     * @return array<string, array<string, mixed>>
     */
    private static function fields(array $check, array $choices): array
    {
        $type = (string) ($check['type'] ?? '');
        $fields = [];
        $walk = function (array $node, string $prefix) use (&$walk, &$fields, $type, $choices) {
            foreach ($node as $key => $value) {
                $path = $prefix.$key;
                if (in_array($path, self::FIXED, true)) {
                    continue;
                }
                if (is_array($value) && ! array_is_list($value)) {
                    $walk($value, $path.'.');

                    continue;
                }
                $label = self::LABELS["$type:$path"] ?? self::LABELS[$path] ?? self::LABELS[$key] ?? ucfirst(strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', (string) $key)));
                $field = ['label' => $label];
                if (is_array($value)) {
                    $field['input'] = 'lines';
                    $field['numbers'] = $value !== [] && is_int($value[0]);
                } elseif (is_bool($value)) {
                    $field['input'] = 'bool';
                } elseif (is_int($value)) {
                    $field['input'] = 'number';
                } elseif ($options = self::OPTIONS["$type:$path"] ?? null) {
                    $field += ['input' => 'select', 'options' => $options];
                } elseif (in_array($value, $pick = $choices["$type:$path"] ?? $choices[$path] ?? [], true)) {
                    // Only offered as a list when the file's own value is in it, so a lab is never forced off its value.
                    $field += ['input' => 'select', 'options' => $pick];
                } else {
                    $field['input'] = 'text';
                    $field['regex'] = in_array($path, self::PATTERNS, true);
                    $field['category'] = $type === 'classified' && $path === 'as';
                }
                $fields[$path] = $field;
            }
        };
        $walk($check, '');

        return $fields;
    }

    /** A check value from the editor, checked against the kind of value the file has there. */
    private static function value(array $field, mixed $value, string $key, array &$errors, string $where): mixed
    {
        $fail = function (string $message) use ($key, &$errors, $where) {
            $errors[$key] = "$where: $message";

            return null;
        };

        switch ($field['input']) {
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $fail("{$field['label']} must be yes or no.");
            case 'number':
                return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 65535]]) === false
                    ? $fail("{$field['label']} must be a whole number.")
                    : (int) $value;
            case 'select':
                return in_array($value, $field['options'], true) ? $value : $fail("pick a {$field['label']} from the list.");
            case 'lines':
                $lines = is_array($value) ? $value : preg_split('/\r?\n/', (string) $value);
                $lines = array_values(array_filter(array_map(fn ($l) => trim((string) $l), $lines), fn ($l) => $l !== ''));
                if ($field['numbers']) {
                    foreach ($lines as $l) {
                        if (! ctype_digit($l)) {
                            return $fail("{$field['label']} takes whole numbers, one per line.");
                        }
                    }
                    if ($lines === []) {
                        return $fail("{$field['label']} needs at least one number.");
                    }

                    return array_map('intval', $lines);
                }

                return array_slice(array_map(fn ($l) => mb_substr($l, 0, 300), $lines), 0, 50);
            default:
                $value = trim(is_scalar($value) ? (string) $value : '');
                if ($value === '') {
                    return $fail("{$field['label']} cannot be empty.");
                }
                if (mb_strlen($value) > 300) {
                    return $fail("{$field['label']} is too long.");
                }
                if (! empty($field['category'])) {
                    // Students type it as one word after `classify <item>`; the simulator stores it in lower case.
                    $value = strtolower($value);
                    $value = self::CATEGORY[$value] ?? $value;
                    if (! preg_match('/^[a-z0-9-]{1,60}$/', $value)) {
                        return $fail("{$field['label']} must be one word (letters, numbers and dashes), like detection-gap.");
                    }
                }
                if (! empty($field['regex']) && @preg_match('/'.str_replace('/', '\\/', $value).'/i', '') === false) {
                    return $fail("{$field['label']} is not a valid pattern.");
                }

                return $value;
        }
    }

    private static function cacheKey(string $course): string
    {
        return "lesson-sim-labs:v2:{$course}";
    }
}
