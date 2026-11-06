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
 * Tells the faculty member a student was caught leaving their final exam or a module's mini boss
 * (left full screen, switched tab, …) and failed it. Queued, so the student's screen ends the exam instantly.
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
        $what = $this->attempt->banned() ? 'was caught again and is banned' : 'left the exam';
        $kind = $this->attempt->quiz->isMiniBoss() ? 'Mini boss' : 'Final exam';

        return new Envelope(subject: "{$kind} alert: {$this->attempt->user->name} {$what}");
    }

    public function content(): Content
    {
        $quiz = $this->attempt->quiz;
        $crew = $quiz->crew;

        return new Content(markdown: 'emails.final-exam-violation', with: [
            'facultyName' => $crew->faculty?->name ?? ($crew->isSelfPaced() ? 'Admin' : 'Captain'),
            'selfPaced' => $crew->isSelfPaced(),
            'studentName' => $this->attempt->user->name,
            'examTitle' => $this->attempt->quiz->title,
            'courseTitle' => config("course-catalog.{$crew->planet}.courses.{$crew->course_slug}.title", $crew->name),
            'reason' => $this->attempt->violationText(),
            'banned' => $this->attempt->banned(),
            'cheated' => $this->attempt->cheated(),
            'when' => $this->attempt->finished_at?->format('j M Y, g:ia'),
            'miniBoss' => $quiz->isMiniBoss() ? (int) substr($quiz->module, 1) : null,
            'url' => match (true) {
                $crew->isSelfPaced() => route('admin.self-paced'),
                $quiz->isMiniBoss() => route('faculty.exams.index', $crew).'#boss-'.$quiz->module,
                default => route('faculty.exams.index', $crew).'#final-exam',
            },
        ]);
    }
}
