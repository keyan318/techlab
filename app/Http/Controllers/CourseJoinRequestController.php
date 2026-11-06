<?php

namespace App\Http\Controllers;

use App\Mail\JoinRequestDeclined;
use App\Models\CourseJoinRequest;
use App\Models\Crew;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Join Class: a student picks a faculty member teaching the course and asks to join their
 * class; that faculty member accepts or declines (prerequisites don't apply — they know who
 * belongs). Accepting enrolls the student on the spot and their class progress starts fresh;
 * declining emails them.
 */
class CourseJoinRequestController extends Controller
{
    /**
     * A student asks to join a course. Asking again after a decline re-opens the request;
     * asking while one is already pending or accepted changes nothing.
     */
    public function store(Request $request, Crew $crew): JsonResponse|RedirectResponse
    {
        // Only a faculty member's class takes requests (Self-paced has nobody to accept them).
        abort_unless($crew->planet && $crew->course_slug && ! $crew->isSelfPaced(), 404);
        $user = $request->user();
        abort_unless($user->isEnrolledIn($crew->planet), 403);

        // A track, once picked, is final: someone on this course already (Self-paced or another class) can't ask.
        $mine = Crew::forStudent($user, $crew->planet, $crew->course_slug);
        if ($mine && $mine->id !== $crew->id) {
            $message = $mine->isSelfPaced()
                ? "You're learning this course Self-paced. Your track can't be changed."
                : "You're in {$mine->faculty?->name}'s class for this course. Your track can't be changed.";

            return $request->expectsJson()
                ? response()->json(['message' => $message], 422)
                : redirect()->route('student.planet', $crew->planet)->with('status', $message);
        }

        $status = 'member';
        if (! $user->belongsToCrew($crew->id)) {
            // One open request per course: asking another faculty member withdraws the one before.
            CourseJoinRequest::where('user_id', $user->id)->where('status', CourseJoinRequest::PENDING)
                ->whereHas('crew', fn ($q) => $q->forCourse($crew->planet, $crew->course_slug)->whereKeyNot($crew->id))->delete();

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

    /** Let the student in: they're enrolled right away and see the course open when they refresh. */
    public function accept(CourseJoinRequest $joinRequest): RedirectResponse
    {
        $student = $joinRequest->student;
        $crew = $joinRequest->crew;

        // No prerequisite check here: whether a student belongs in this class is the faculty member's call.
        abort_unless($crew->teacher_id === Auth::id(), 403);

        // They picked another track for this course in the meantime: that choice is final, so there's nothing to accept.
        $mine = Crew::forStudent($student, $crew->planet, $crew->course_slug);
        if ($mine && $mine->id !== $crew->id) {
            $joinRequest->delete();

            return redirect()->route('faculty.dashboard')->with('status', "{$student->name} is already taking this course on another track.");
        }

        if (! $this->decide($joinRequest, CourseJoinRequest::ACCEPTED)) {
            return redirect()->route('faculty.dashboard');
        }

        $joinRequest->crew->admit($student);

        return redirect()->route('faculty.dashboard')->with('status', "Accepted {$student->name}. They're enrolled, and the course opens for them as soon as they refresh.");
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
    private function decide(CourseJoinRequest $joinRequest, string $status, ?JoinRequestDeclined $mail = null): bool
    {
        abort_unless($joinRequest->crew->teacher_id === Auth::id(), 403);

        $decided = CourseJoinRequest::whereKey($joinRequest->id)
            ->where('status', CourseJoinRequest::PENDING)
            ->update(['status' => $status, 'decided_at' => now()]);

        if ($decided && $mail) {
            Mail::to($joinRequest->student)->queue($mail);
        }

        return (bool) $decided;
    }
}
