<?php

namespace App\Http\Controllers;

use App\Mail\JoinRequestAccepted;
use App\Mail\JoinRequestDeclined;
use App\Models\CourseJoinRequest;
use App\Models\Crew;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * The gate in front of a course code: a student asks to join, the course's faculty
 * member accepts or declines, and only an accepted student gets the code (by email)
 * and can redeem it (StudentController::joinCrew checks for the acceptance).
 */
class CourseJoinRequestController extends Controller
{
    /**
     * A student asks to join a course. Asking again after a decline re-opens the request;
     * asking while one is already pending or accepted changes nothing.
     */
    public function store(Request $request, Crew $crew): JsonResponse|RedirectResponse
    {
        abort_unless($crew->planet && $crew->course_slug, 404);
        $user = $request->user();
        abort_unless($user->isEnrolledIn($crew->planet), 403);

        $status = 'member';
        if (! $user->belongsToCrew($crew->id)) {
            $joinRequest = $crew->joinRequests()->firstOrCreate(
                ['user_id' => $user->id],
                ['status' => CourseJoinRequest::PENDING],
            );
            if ($joinRequest->status === CourseJoinRequest::DECLINED) {
                $joinRequest->update(['status' => CourseJoinRequest::PENDING, 'decided_at' => null]);
            }
            $status = $joinRequest->status;
        }

        return $request->expectsJson()
            ? response()->json(['status' => $status])
            : redirect()->route('student.planet', $crew->planet);
    }

    /** Let the student in: they get the course code by email. */
    public function accept(CourseJoinRequest $joinRequest): RedirectResponse
    {
        $student = $joinRequest->student;

        return $this->decide($joinRequest, CourseJoinRequest::ACCEPTED, new JoinRequestAccepted($joinRequest))
            ? redirect()->route('faculty.dashboard')->with('status', "Accepted {$student->name}. Their code is on its way to {$student->email}.")
            : redirect()->route('faculty.dashboard');
    }

    /** Turn the student away: they get a "not this time" email and can ask again. */
    public function decline(CourseJoinRequest $joinRequest): RedirectResponse
    {
        $student = $joinRequest->student;

        return $this->decide($joinRequest, CourseJoinRequest::DECLINED, new JoinRequestDeclined($joinRequest))
            ? redirect()->route('faculty.dashboard')->with('status', "Declined {$student->name}. We let them know by email.")
            : redirect()->route('faculty.dashboard');
    }

    /**
     * Only the course's own faculty member can answer, and only a pending request.
     * The conditional update means a double-clicked button sends one email, not two.
     */
    private function decide(CourseJoinRequest $joinRequest, string $status, JoinRequestAccepted|JoinRequestDeclined $mail): bool
    {
        abort_unless($joinRequest->crew->teacher_id === Auth::id(), 403);

        $decided = CourseJoinRequest::whereKey($joinRequest->id)
            ->where('status', CourseJoinRequest::PENDING)
            ->update(['status' => $status, 'decided_at' => now()]);

        if ($decided) {
            Mail::to($joinRequest->student)->queue($mail);
        }

        return (bool) $decided;
    }
}
