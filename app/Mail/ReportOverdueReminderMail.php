<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportOverdueReminderMail extends Mailable
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
            subject: '[Urgent] Safety Walk and Talk Report Overdue - Action Required',
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
            view: 'mails.laporan-reminder-overdue',
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
