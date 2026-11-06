<?php

namespace App\Http\Controllers;

use App\Models\CourseJoinRequest;
use App\Models\Crew;
use App\Services\CoursePrerequisiteService;
use App\Services\CourseStructure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Self-paced: a student starts a course on their own, right away, with the default TechLab
 * course, quizzes and final exam (the admin looks after it). Unlike joining a class, the
 * course before it has to be finished first. Also how a student leaves a class: switching
 * to self-paced shelves the class's progress and brings back their own.
 */
class CourseEnrollmentController extends Controller
{
    public function selfPaced(Request $request, string $planet, string $course): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $card = CourseStructure::card($planet, $course);
        abort_unless(($user->role ?? 'student') === 'student', 403);
        abort_unless($card && empty($card['coming_soon']) && empty($card['hidden']), 404);
        abort_unless($user->isEnrolledIn($planet), 403);

        // A track, once picked, is final: a class member can't move to Self-paced.
        $destination = route('student.course.overview', ['slug' => $planet, 'course' => $course]);
        if ($mine = Crew::forStudent($user, $planet, $course)) {
            if ($mine->isSelfPaced()) {
                return $request->expectsJson() ? response()->json(['redirect' => $destination]) : redirect($destination);
            }
            $message = "You're in {$mine->faculty?->name}'s class for this course. Your track can't be changed.";

            return $request->expectsJson()
                ? response()->json(['message' => $message], 422)
                : redirect($destination)->with('status', $message);
        }

        if ($required = CoursePrerequisiteService::missing($user, $planet, $course)) {
            $message = CoursePrerequisiteService::message($required);

            return $request->expectsJson()
                ? response()->json(['message' => $message, 'requires' => $required['id']], 422)
                : redirect()->route('student.planet', $planet)->with('status', $message);
        }

        // Self-paced is their pick: any class request still waiting is withdrawn, so it can't be accepted later.
        CourseJoinRequest::where('user_id', $user->id)->where('status', CourseJoinRequest::PENDING)
            ->whereHas('crew', fn ($q) => $q->forCourse($planet, $course))->delete();
        Crew::selfPaced($planet, $course)->admit($user);

        return $request->expectsJson()
            ? response()->json(['redirect' => $destination])
            : redirect($destination);
    }
}
