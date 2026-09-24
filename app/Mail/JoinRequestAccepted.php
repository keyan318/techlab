<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * "You're in": carries the course code, which the student then types on the course card.
 */
class JoinRequestAccepted extends JoinRequestMail
{
    public function envelope(): Envelope
    {
        return new Envelope(subject: "You're in! Your {$this->details()['courseTitle']} code");
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.join-accepted',
            with: $this->details() + ['code' => $this->joinRequest->crew->code],
        );
    }
}
