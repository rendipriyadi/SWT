<?php

namespace App\Mail;

use App\Models\laporan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LaporanDieditSupervisor extends Mailable
{
    use Queueable, SerializesModels;

    public $laporan;
    public $perubahan;

    /**
     * Create a new message instance.
     */
    public function __construct(laporan $laporan, array $perubahan)
    {
        $this->laporan = $laporan;
        $this->perubahan = $perubahan;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Notifikasi Perubahan Laporan Safety Walk and Talk')
                    ->markdown('emails.laporan-diedit');
    }
}