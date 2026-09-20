<?php

namespace App\Http\Controllers;

use App\Exceptions\AttachmentException;
use App\Exceptions\NvidiaNimException;
use App\Models\TeacherClass;
use App\Services\AttachmentService;
use App\Services\Schedule\ScheduleExtractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/** "My Classes" on the teacher dashboard: upload a timetable, Astro organizes it. */
class TeacherScheduleController extends Controller
{
    public function __construct(protected ScheduleExtractor $extractor) {}

    public function store(Request $request): Response
    {
        if (! $this->isTeacher()) {
            return response()->json(['error' => 'Teachers only.'], 403);
        }

        $request->validate(['file' => ['required', 'file', 'max:'.AttachmentService::maxFileKb()]], [
            'file.max' => 'That file is too large — the limit is '.AttachmentService::mb(AttachmentService::maxFileKb()).'.',
            'file.uploaded' => 'That file is too large — the limit is '.AttachmentService::mb(AttachmentService::maxFileKb()).'.',
        ]);

        set_time_limit(0);
        $file = $request->file('file');
        $startsOn = $this->startDate($request->input('today'));

        // Live mode: the page asks for NDJSON and gets a status line per stage ("reading" → "organizing") and then the result.
        // Anything else (tests, old cached pages) gets one plain JSON response.
        if (str_contains((string) $request->header('Accept'), 'application/x-ndjson')) {
            $userId = Auth::id();

            return response()->stream(function () use ($file, $userId, $startsOn) {
                $emit = function (array $line) {
                    echo json_encode($line)."\n";
                    if (ob_get_level() > 0) {
                        @ob_flush();
                    }
                    flush();
                };
                [$status, $payload] = $this->handle($file, $userId, $startsOn, fn (string $stage) => $emit(['stage' => $stage]));
                $emit(['done' => $status < 400] + $payload + ['status' => $status]);
            }, 200, ['Content-Type' => 'application/x-ndjson', 'Cache-Control' => 'no-cache', 'X-Accel-Buffering' => 'no']);
        }

        [$status, $payload] = $this->handle($file, Auth::id(), $startsOn);

        return response()->json($payload, $status);
    }

    /** The calendar drawer's data (the dashboard card already has it inline). */
    public function index(): JsonResponse
    {
        if (! $this->isTeacher()) {
            return response()->json(['error' => 'Teachers only.'], 403);
        }

        return response()->json(['classes' => $this->current()]);
    }

    /**
     * @return array{0: int, 1: array<string, mixed>} [HTTP status, JSON body]
     */
    private function handle(UploadedFile $file, int $userId, string $startsOn, ?callable $onStage = null): array
    {
        try {
            $rows = $this->extractor->extract($file, $onStage);
        } catch (AttachmentException $e) {
            return [422, ['error' => $e->getMessage()]];
        } catch (NvidiaNimException $e) {
            Log::warning('Schedule extraction failed', ['category' => $e->category]);

            $slow = in_array($e->category, ['NIM_TIMEOUT', 'NIM_CONNECTION_ERROR', 'NIM_MODEL_ERROR'], true) || $e->status === null;

            return [504, ['error' => $slow
                ? 'Astro is taking too long right now. Please try again — a smaller or clearer file usually helps.'
                : 'Astro could not read your schedule just now. Please try again in a moment.']];
        } catch (\Throwable $e) {
            Log::error('Schedule backend error', ['message' => $e->getMessage()]);

            return [500, ['error' => 'Something went wrong reading your schedule. Please try again.']];
        }

        if ($rows === []) {
            return [422, ['error' => "Astro couldn't find a weekly timetable in that file. Try a clearer photo, or a PDF/Word/CSV with days and times."]];
        }

        // Replace the old schedule in one go, so a half-saved upload never mixes with the previous one.
        DB::transaction(function () use ($rows, $userId, $startsOn) {
            TeacherClass::where('user_id', $userId)->delete();
            TeacherClass::insert(array_map(fn ($r) => $r + ['user_id' => $userId, 'starts_on' => $startsOn, 'created_at' => now(), 'updated_at' => now()], $rows));
        });

        return [200, ['ok' => true, 'classes' => $this->current($userId)]];
    }

    /** Add one class by hand. It repeats weekly from `starts_on` (default: today). */
    public function storeClass(Request $request): JsonResponse
    {
        if (! $this->isTeacher()) {
            return response()->json(['error' => 'Teachers only.'], 403);
        }

        $class = TeacherClass::create($this->classData($request) + ['user_id' => Auth::id()]);

        return response()->json(['ok' => true, 'class' => $class->toCard(), 'classes' => $this->current()], 201);
    }

    public function updateClass(Request $request, TeacherClass $class): JsonResponse
    {
        if (! $this->isTeacher() || $class->user_id !== Auth::id()) {
            return response()->json(['error' => 'Not found.'], 404);
        }

        $class->update($this->classData($request));

        return response()->json(['ok' => true, 'class' => $class->fresh()->toCard(), 'classes' => $this->current()]);
    }

    public function destroyClass(TeacherClass $class): JsonResponse
    {
        if (! $this->isTeacher() || $class->user_id !== Auth::id()) {
            return response()->json(['error' => 'Not found.'], 404);
        }

        $class->delete();

        return response()->json(['ok' => true, 'classes' => $this->current()]);
    }

    /** @return array<string, mixed> */
    private function classData(Request $request): array
    {
        $d = $request->validate([
            'subject' => ['required', 'string', 'max:120'],
            'class' => ['nullable', 'string', 'max:120'],
            'day' => ['required', 'integer', 'between:1,7'],
            'start' => ['required', 'date_format:H:i'],
            'end' => ['nullable', 'date_format:H:i', 'after:start'],
            'room' => ['nullable', 'string', 'max:60'],
            'from' => ['nullable', 'date_format:Y-m-d'],
        ]);

        return [
            'subject' => trim($d['subject']),
            'class_name' => filled($d['class'] ?? null) ? trim($d['class']) : null,
            'day' => (int) $d['day'],
            'starts_at' => $d['start'],
            'ends_at' => $d['end'] ?? null,
            'room' => filled($d['room'] ?? null) ? trim($d['room']) : null,
            'starts_on' => $d['from'] ?? $this->startDate(null),
        ];
    }

    /** The teacher's own "today" (the browser sends it), trusted only within a day or two of the server's clock. */
    private function startDate(?string $today): string
    {
        $server = now();
        if (is_string($today) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $today)) {
            try {
                $d = Carbon::createFromFormat('Y-m-d', $today)->startOfDay();
                if (abs($d->diffInDays($server->copy()->startOfDay(), false)) <= 2) {
                    return $d->toDateString();
                }
            } catch (\Throwable) {
            }
        }

        return $server->toDateString();
    }

    public function destroy(): JsonResponse
    {
        if (! $this->isTeacher()) {
            return response()->json(['error' => 'Teachers only.'], 403);
        }

        TeacherClass::where('user_id', Auth::id())->delete();

        return response()->json(['ok' => true, 'classes' => []]);
    }

    private function current(?int $userId = null): array
    {
        return TeacherClass::where('user_id', $userId ?? Auth::id())->orderBy('day')->orderBy('starts_at')->get()->map->toCard()->all();
    }

    private function isTeacher(): bool
    {
        return Auth::check() && (Auth::user()->role ?? 'student') === 'teacher';
    }
}
