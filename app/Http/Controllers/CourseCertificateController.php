<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\User;
use App\Services\CertificatePdfService;
use App\Services\CourseProgressService;
use App\Services\CourseStructure;
use App\Services\FinalExamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * The certificate a student earns by finishing every lesson of a course and then passing the
 * faculty's final exam (the "Final Boss", FinalExamController).
 *
 * Nothing extra is stored for it: the issue date is when the final exam was passed and the
 * certificate number is derived from the student and the course, so the same certificate
 * comes back every time it is opened. Membership is already enforced by `course.member`.
 */
class CourseCertificateController extends Controller
{
    /**
     * GET /student/course/{slug}/{course}/certificate
     *
     * A white PDF certificate (CertificatePdfService), opened in the browser to view or save.
     * Self-paced: issued by TechLab. Class: signed by the faculty member.
     */
    public function show(string $slug, string $course, CertificatePdfService $pdfs): RedirectResponse|Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect(route('login'));
        }

        $meta = CourseStructure::card($slug, $course);

        abort_if($meta === [] || ! empty($meta['coming_soon']), 404);

        $key = CourseProgressService::keyFor($slug, $course);
        $crew = request()->attributes->get('crew')
            ?? Crew::viewFor(Auth::user(), $slug, $course);
        $attempt = FinalExamService::for($user, $crew)['attempt'];

        // Lessons left, or the Final Boss not beaten yet: the hub shows what is still to do.
        if (CourseProgressService::finishedAt($user, $key) === null || ! $attempt?->passed()) {
            return redirect()->route('student.course.overview', ['slug' => $slug, 'course' => $course]);
        }

        $issuedAt = $attempt->finished_at;
        $order = CourseProgressService::order($key);

        $data = [
            'studentName' => $user->name,
            'courseTitle' => $meta['title'],
            'courseCode' => $meta['code'] ?? null,
            'planetTitle' => config("course-catalog.{$slug}.title"),
            'lessons' => count($order),
            'labs' => count(array_filter($order, fn ($item) => $item['lab'] !== null)),
            'issuedAt' => $issuedAt,
            'certificateId' => self::certificateId($user, $key, $meta['code'] ?? $course),
            // A class's certificate is signed by its faculty member; a self-paced one is issued by TechLab.
            'selfPaced' => (bool) $crew?->isSelfPaced(),
            'captain' => $crew?->isSelfPaced() ? null : $crew?->faculty?->name,
            // A file path, not a URL: Dompdf reads the signature straight from disk.
            'signature' => ! $crew?->isSelfPaced() && $crew?->faculty?->signature_path ? Storage::disk('public')->path($crew->faculty->signature_path) : null,
        ];

        return response($pdfs->render($data), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.CertificatePdfService::fileName($data).'"',
        ]);
    }

    /** "TL-ITP1232-7F3A-09C2": the same for one student and course every time, and not guessable from either. */
    public static function certificateId(User $user, string $key, string $code): string
    {
        $hash = strtoupper(substr(hash_hmac('sha256', "certificate|{$user->id}|{$key}", (string) config('app.key')), 0, 8));

        return 'TL-'.strtoupper($code).'-'.substr($hash, 0, 4).'-'.substr($hash, 4, 4);
    }
}
