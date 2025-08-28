<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily report to run every day at 8:00 AM (Ho Chi Minh time)
Schedule::command('report:daily')
    ->dailyAt('08:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->name('send-daily-admin-report')
    ->description('Gửi báo cáo doanh thu hàng ngày cho admin');
