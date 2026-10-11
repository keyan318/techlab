<?php

namespace App\Mail;

use App\Models\CrewQuizAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Tells the faculty member a student was caught leaving their final exam (left full screen,
 * switched tab, …) and failed it. Queued, so the student's screen ends the exam instantly.
 */
class FinalExamViolation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public CrewQuizAttempt $attempt)
    {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Final exam alert: {$this->attempt->user->name} left the exam");
    }

    public function content(): Content
    {
        $crew = $this->attempt->quiz->crew;

        return new Content(markdown: 'emails.final-exam-violation', with: [
            'facultyName' => $crew->faculty?->name ?? 'Captain',
            'studentName' => $this->attempt->user->name,
            'examTitle' => $this->attempt->quiz->title,
            'courseTitle' => config("course-catalog.{$crew->planet}.courses.{$crew->course_slug}.title", $crew->name),
            'reason' => $this->attempt->violationText(),
            'when' => $this->attempt->finished_at?->format('j M Y, g:ia'),
            'url' => route('faculty.course', $crew).'#final-exam',
        ]);
    }
}
