<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Laporan;
use Illuminate\Console\Command;
use App\Mail\ReportDeadlineReminderMail;
use App\Http\Library\SWTEmailNotifications;

class ReminderReportDeadline extends Command
{
    use SWTEmailNotifications;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:reminder-deadline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder H-2 before deadline for incomplete reports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Force URL for CLI context (workaround for missing HTTP context)
        // This ensures URL generation uses the correct base URL even in CLI
        \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
        
        // Hitung H-2 (2 hari kerja dari sekarang)
        $twoWorkdaysLater = Carbon::now()->addWeekdays(2)->toDateString();

        // Ambil laporan yang deadline-nya H-2 dan belum selesai
        $reports = Laporan::where('status', '!=', 'Completed')
            ->whereDate('tenggat_waktu', $twoWorkdaysLater)
            ->with(['area', 'penanggungJawab', 'problemCategory'])
            ->get();

        if ($reports->isEmpty()) {
            $this->info('Tidak ada laporan yang mendekati deadline H-2.');
            return;
        }

        // Use trait method to send reminders
        $this->sendReminderByPic($reports, ReportDeadlineReminderMail::class);

        $this->info("Total {$reports->count()} laporan dengan deadline H-2 telah diproses.");
    }
}
