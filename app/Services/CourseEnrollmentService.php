<?php

namespace App\Services;

use App\Models\CourseJoinRequest;
use App\Models\Crew;
use App\Models\User;

/** Where a student stands with a course: on a track already, or the two ways in (Self-paced, Join Class). */
class CourseEnrollmentService
{
    /**
     * How a student stands with one catalog course, for its card: on a track already (Self-paced or a
     * class), or not yet — then the two ways in. Self-paced opens at once but needs the course before it
     * finished; Join Class lists every faculty member teaching it, and that faculty member decides.
     */
    public static function forCard(User $user, string $planet, string $id): array
    {
        $soon = ! empty(config("course-catalog.{$planet}.courses.{$id}.coming_soon"));
        $base = ['id' => $id, 'soon' => $soon, 'url' => null, 'percent' => null, 'track' => null, 'classes' => [],
            'self_paced_url' => null, 'requires_title' => null, 'requires_kind' => 'course', 'requires_done' => false,
            'self_paced_blocked' => false, 'locked' => false];
        if ($soon) {
            return $base;
        }

        $crews = Crew::forCourse($planet, $id)->with('faculty')
            ->withExists(['roster as is_member' => fn ($q) => $q->whereKey($user->id)])->get();
        $requests = CourseJoinRequest::where('user_id', $user->id)->whereIn('crew_id', $crews->modelKeys())->pluck('status', 'crew_id');

        // Accepted back when acceptance only emailed a code, and the code was never typed: let them in now.
        if (! $crews->contains('is_member', true) && ($accepted = $crews->first(fn (Crew $c) => ($requests[$c->id] ?? null) === CourseJoinRequest::ACCEPTED))) {
            $accepted->admit($user);
            $accepted->is_member = true;
        }

        $mine = $crews->firstWhere('is_member', true);
        $required = CoursePrerequisiteService::requirement($planet, $id);
        $requiredDone = $required && CoursePrerequisiteService::satisfied($user, $planet, $required);

        // Faculty and admins walk any course without a track.
        $open = $mine || ($user->role ?? 'student') !== 'student';

        return [
            'url' => $open ? route('student.course.overview', ['slug' => $planet, 'course' => $id]) : null,
            // This course's own progress: the planet's percent would mix in its other courses.
            'percent' => $open ? CourseProgressService::percent($user, CourseProgressService::keyFor($planet, $id)) : null,
            'track' => $mine ? ['type' => $mine->type, 'teacher' => $mine->faculty?->name] : null,
            'locked' => ! $open,
            // A track, once picked, is final: no Self-paced button for a class member (and vice versa below).
            'self_paced_url' => $mine ? null : route('student.course.self-paced', ['planet' => $planet, 'course' => $id]),
            // Self-paced waits for the course before it; a class doesn't (its faculty member decides).
            'requires_title' => $required['title'] ?? null,
            'requires_kind' => ($required['module_gate'] ?? false) ? 'module' : 'course',
            'requires_done' => $requiredDone,
            'self_paced_blocked' => $required && ! $requiredDone && ($user->role ?? 'student') === 'student',
            'classes' => $crews->filter(fn (Crew $c) => ! $c->isSelfPaced() && $c->faculty)->sortBy(fn (Crew $c) => $c->faculty->name)
                ->map(fn (Crew $c) => [
                    'id' => $c->id,
                    'teacher' => $c->faculty->name,
                    'status' => $c->is_member ? 'member' : ($requests[$c->id] ?? null),
                    'join_url' => route('student.join-request.store', $c),
                ])->values()->all(),
        ] + $base;
    }
}
