<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $laporan;
    public $fullUrl;
    public $ccEmails;
    public $toRecipients;

    /**
     * Create a new message instance.
     */
    public function __construct($laporan, $ccEmails = [], $toRecipients = [])
    {
        $this->laporan = $laporan;
        $this->ccEmails = $ccEmails;
        $this->toRecipients = $toRecipients;
        

        $encryptedId = encrypt($laporan->id);
        $this->fullUrl = config('app.url') . '/laporan/' . $encryptedId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $categoryName = $this->laporan->problemCategory 
            ? $this->laporan->problemCategory->name 
            : 'Report';

        $envelope = new Envelope(
            subject: "New Safety Walk and Talk Report Assigned - {$categoryName}",
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
            view: 'mails.laporan-ditugaskan',
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
