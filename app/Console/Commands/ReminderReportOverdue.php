<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Laporan;
use Illuminate\Console\Command;
use App\Mail\ReportOverdueReminderMail;
use App\Http\Library\SWTEmailNotifications;

class ReminderReportOverdue extends Command
{
    use SWTEmailNotifications;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:reminder-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily reminder for overdue incomplete reports (weekdays only)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Force URL for CLI context (workaround for missing HTTP context)
        // This ensures URL generation uses the correct base URL even in CLI
        \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
        
        // Check if today is Saturday (6) or Sunday (7)
        $dayOfWeek = date('N');
        if (in_array($dayOfWeek, [6, 7])) {
            $this->info('Code execution skipped as today is a weekend.');
            return;
        }

        $today = Carbon::now()->toDateString();

        // Ambil laporan yang sudah lewat deadline dan belum selesai
        $reports = Laporan::where('status', '!=', 'Completed')
            ->whereDate('tenggat_waktu', '<', $today)
            ->with(['area', 'penanggungJawab', 'problemCategory'])
            ->get();

        if ($reports->isEmpty()) {
            $this->info('Tidak ada laporan yang overdue.');
            return;
        }

        // Use trait method to send reminders
        $this->sendReminderByPic($reports, ReportOverdueReminderMail::class);

        $this->info("Total {$reports->count()} laporan overdue telah diproses.");
    }
}
