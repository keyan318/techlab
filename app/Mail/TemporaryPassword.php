<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * "Forgot password?": carries a temporary password. Sent right away (not queued) so it
 * arrives even when no queue worker is running.
 */
class TemporaryPassword extends Mailable
{
    public function __construct(public User $user, public string $temporary, public int $minutes) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your TechLab temporary password');
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.temporary-password',
            with: [
                'name' => $this->user->name,
                'temporary' => $this->temporary,
                'minutes' => $this->minutes,
                'url' => $this->user->role === 'faculty' ? route('faculty.login') : route('login'),
            ],
        );
    }
}
