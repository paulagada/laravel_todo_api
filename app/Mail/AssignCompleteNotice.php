<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssignCompleteNotice extends Mailable
{
    use Queueable, SerializesModels;
    public $userMail;
    public $assignerMail;
    public $completed;
    public $todoTitle;

    /**
     * Create a new message instance.
     */
    public function __construct($userMail, $assignerMail, $todoTitle, $completed)
    {
        $this->userMail = $userMail;
        $this->assignerMail = $assignerMail;
        $this->completed = $completed;
        $this->todoTitle = $todoTitle;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Assign Complete Notice',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.assignCompleteNotice',
            with: [
                'userMail' => $this->userMail,
                'assignerMail' => $this->assignerMail,
                'completed' => $this->completed,
                'todoTitle' => $this->todoTitle
                ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
