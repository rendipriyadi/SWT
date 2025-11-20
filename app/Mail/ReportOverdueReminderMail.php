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

    public $reportsData;
    public $pic;
    public $areaName;
    public $ccEmails;
    public $toNames;

    /**
     * Create a new message instance.
     */
    public function __construct($reports, $pic, $ccEmails = [], $toNames = [])
    {
        $this->pic = $pic;
        $this->ccEmails = $ccEmails;
        $this->toNames = $toNames;
        $this->areaName = $reports->first()->area->name ?? 'Area';
        
        // Prepare reports data with URLs (all logic here, not in blade)
        $this->reportsData = $reports->map(function($laporan) {
            // Calculate days overdue
            $deadline = $laporan->tenggat_waktu ? \Carbon\Carbon::parse($laporan->tenggat_waktu)->startOfDay() : null;
            $today = \Carbon\Carbon::now()->startOfDay();
            $daysOverdue = $deadline ? (int) $deadline->diffInDays($today) : 0;
            
            $encryptedId = encrypt($laporan->id);
            
            return [
                'id' => $laporan->id,
                'category' => $laporan->problemCategory->name ?? '-',
                'description' => $laporan->deskripsi_masalah,
                'deadline' => $laporan->tenggat_waktu,
                'deadline_formatted' => $deadline ? $deadline->locale('en')->isoFormat('dddd, D MMMM YYYY') : '-',
                'days_overdue' => $daysOverdue,
                'area' => $laporan->area ? $laporan->area->name : '-',
                'pic' => $laporan->penanggungJawab ? $laporan->penanggungJawab->name : 
                        ($laporan->area && $laporan->area->penanggungJawabs->isNotEmpty() ? 
                         $laporan->area->penanggungJawabs->pluck('name')->join(', ') : '-'),
                'status' => $laporan->status ?? '-',
                'url' => route('laporan.show', $encryptedId), // Works in CLI context
            ];
        });
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
