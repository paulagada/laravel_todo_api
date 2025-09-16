<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssignNotice extends Mailable
{
    use Queueable, SerializesModels;
    public $userMail;
    public $assignerMail;
    public $assigned;
    public $todoTitle;

    /**
     * Create a new message instance.
     */
    public function __construct($userMail, $assignerMail, $todoTitle, $assigned)
    {
        $this->userMail = $userMail;
        $this->assignerMail = $assignerMail;
        $this->assigned = $assigned;
        $this->todoTitle = $todoTitle;
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Assign Notice',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.assignNotice',
            with: [
                'userMail' => $this->userMail,
                'assignerMail' => $this->assignerMail,
                'assigned' => $this->assigned,
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
