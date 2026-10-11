<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\FinalExamService;
use App\Services\StudentDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
    /** GET /student/course/{slug}/{course}/certificate */
    public function show(string $slug, string $course): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return redirect(route('login'));
        }

        $meta = config("course-catalog.{$slug}.courses.{$course}");

        abort_if(! $meta || ! empty($meta['coming_soon']), 404);

        $key = CourseProgressService::keyFor($slug, $course);
        $crew = request()->attributes->get('crew')
            ?? Crew::where('planet', $slug)->where('course_slug', $course)->first();
        $attempt = FinalExamService::for($user, $crew)['attempt'];

        // Lessons left, or the Final Boss not beaten yet: the hub shows what is still to do.
        if (CourseProgressService::finishedAt($user, $key) === null || ! $attempt?->passed()) {
            return redirect()->route('student.course.overview', ['slug' => $slug, 'course' => $course]);
        }

        $issuedAt = $attempt->finished_at;
        $order = CourseProgressService::order($key);

        return view('student.course.certificate', [
            'studentName' => $user->name,
            'courseTitle' => $meta['title'],
            'courseCode' => $meta['code'] ?? null,
            'tagline' => $meta['blurb'] ?? null,
            'planetTitle' => config("course-catalog.{$slug}.title"),
            'logo' => $meta['logo'] ?? null,
            'chapters' => count(config("course-structure.{$key}.modules", [])),
            'lessons' => count($order),
            'labs' => count(array_filter($order, fn ($item) => $item['lab'] !== null)),
            'xp' => count($order) * StudentDashboardService::XP_PER_LESSON,
            'issuedAt' => $issuedAt,
            'certificateId' => self::certificateId($user, $key, $meta['code'] ?? $course),
            'captain' => $crew?->faculty?->name,
            'backUrl' => route('student.course.overview', ['slug' => $slug, 'course' => $course]),
        ]);
    }

    /** "TL-ITP1232-7F3A-09C2": the same for one student and course every time, and not guessable from either. */
    public static function certificateId(User $user, string $key, string $code): string
    {
        $hash = strtoupper(substr(hash_hmac('sha256', "certificate|{$user->id}|{$key}", (string) config('app.key')), 0, 8));

        return 'TL-'.strtoupper($code).'-'.substr($hash, 0, 4).'-'.substr($hash, 4, 4);
    }
}
