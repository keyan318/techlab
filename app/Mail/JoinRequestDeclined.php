<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * "Not this time": no code, but the student can ask again from the course card.
 */
class JoinRequestDeclined extends JoinRequestMail
{
    public function envelope(): Envelope
    {
        return new Envelope(subject: "Update on your {$this->details()['courseTitle']} request");
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.join-declined', with: $this->details());
    }
}
