<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportDeadlineReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reports;
    public $pic;
    public $encryptedIds;
    public $fullUrls;
    public $ccEmails;

    /**
     * Create a new message instance.
     */
    public function __construct($reports, $pic, $encryptedIds, $fullUrls, $ccEmails = [])
    {
        $this->reports = $reports;
        $this->pic = $pic;
        $this->encryptedIds = $encryptedIds;
        $this->fullUrls = $fullUrls;
        $this->ccEmails = $ccEmails;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $envelope = new Envelope(
            subject: '[Reminder H-2] Safety Walk and Talk Report Deadline Approaching',
        );

        // Add CC if provided
        if (!empty($this->ccEmails)) {
            $envelope->cc($this->ccEmails);
        }

        return $envelope;
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.laporan-reminder-deadline',
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
