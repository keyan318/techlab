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

        // Live mode: the page asks for NDJSON and gets a status line per stage ("reading" → "organizing") and then the result.
        // Anything else (tests, old cached pages) gets one plain JSON response.
        if (str_contains((string) $request->header('Accept'), 'application/x-ndjson')) {
            $userId = Auth::id();

            return response()->stream(function () use ($file, $userId) {
                $emit = function (array $line) {
                    echo json_encode($line)."\n";
                    if (ob_get_level() > 0) {
                        @ob_flush();
                    }
                    flush();
                };
                [$status, $payload] = $this->handle($file, $userId, fn (string $stage) => $emit(['stage' => $stage]));
                $emit(['done' => $status < 400] + $payload + ['status' => $status]);
            }, 200, ['Content-Type' => 'application/x-ndjson', 'Cache-Control' => 'no-cache', 'X-Accel-Buffering' => 'no']);
        }

        [$status, $payload] = $this->handle($file, Auth::id());

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
    private function handle(UploadedFile $file, int $userId, ?callable $onStage = null): array
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
        DB::transaction(function () use ($rows, $userId) {
            TeacherClass::where('user_id', $userId)->delete();
            TeacherClass::insert(array_map(fn ($r) => $r + ['user_id' => $userId, 'created_at' => now(), 'updated_at' => now()], $rows));
        });

        return [200, ['ok' => true, 'classes' => $this->current($userId)]];
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
