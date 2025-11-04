<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Reminder H-2 sebelum deadline (setiap hari kerja jam 08:00)
Schedule::command('report:reminder-deadline')
    ->weekdays() // Senin-Jumat
    ->dailyAt('08:00')
    ->timezone('Asia/Jakarta')
    ->description('Send H-2 deadline reminder for incomplete reports');

// Reminder daily untuk report yang overdue (setiap hari kerja jam 09:00)
Schedule::command('report:reminder-overdue')
    ->weekdays() // Senin-Jumat
    ->dailyAt('09:00')
    ->timezone('Asia/Jakarta')
    ->description('Send daily reminder for overdue reports');
