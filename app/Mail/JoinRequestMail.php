<?php

namespace App\Mail;

use App\Models\CourseJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Shared base for the emails a student gets when the course's faculty member answers
 * their join request. Queued, so the faculty member's Accept/Decline click is instant.
 */
abstract class JoinRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public CourseJoinRequest $joinRequest)
    {
        $this->afterCommit();
    }

    /**
     * What both emails show: who, which course, and where to go.
     *
     * @return array<string, string>
     */
    protected function details(): array
    {
        $crew = $this->joinRequest->crew;

        return [
            'studentName' => $this->joinRequest->student->name,
            'facultyName' => $crew->faculty?->name ?? 'Your faculty member',
            'courseTitle' => config("course-catalog.{$crew->planet}.courses.{$crew->course_slug}.title", $crew->name),
            'planetTitle' => config("course-catalog.{$crew->planet}.title", 'TechLab'),
            'url' => route('student.planet', $crew->planet),
        ];
    }
}
