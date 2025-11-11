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

    public $reportsData;
    public $pic;
    public $areaName;
    public $ccEmails;

    /**
     * Create a new message instance.
     */
    public function __construct($reports, $pic, $ccEmails = [])
    {
        $this->pic = $pic;
        $this->ccEmails = $ccEmails;
        $this->areaName = $reports->first()->area->name ?? 'Area';
        
        // Prepare reports data with URLs (all logic here, not in blade)
        $this->reportsData = $reports->map(function($laporan) {
            $encryptedId = encrypt($laporan->id);
            
            return [
                'id' => $laporan->id,
                'category' => $laporan->problemCategory->name ?? '-',
                'description' => $laporan->deskripsi_masalah,
                'deadline' => $laporan->tenggat_waktu,
                'area' => $laporan->area ? $laporan->area->name : '-',
                'pic' => $laporan->penanggungJawab ? $laporan->penanggungJawab->name : 
                        ($laporan->area && $laporan->area->penanggungJawabs->isNotEmpty() ? 
                         $laporan->area->penanggungJawabs->pluck('name')->join(', ') : '-'),
                'status' => $laporan->status ?? '-',
                'url' => config('app.url') . '/laporan/' . $encryptedId, // Works in CLI context
            ];
        });
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
